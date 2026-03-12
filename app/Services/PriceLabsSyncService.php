<?php

namespace App\Services;

use Throwable;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use Illuminate\Support\Facades\Log;
use App\Services\PropertyService;


class PriceLabsSyncService
{
    protected int $customerId;

    /* =====================================================
     | SET CONTEXT
     ===================================================== */

    public function withCustomerId(int $customerId): self
    {
        $this->customerId = $customerId;
        return $this;
    }

    /* =====================================================
     | PUBLIC ENTRY POINTS
     ===================================================== */

    public function syncUnit(TblHomeUnit $unit): bool
    {
        Log::info('PriceLabs syncUnit started', [
            'unit_id'    => $unit->id,
            'listing_id' => $unit->ru_property_id,
            'customer_id' => $this->customerId,
        ]);

        if (! $this->syncUnitCore($unit)) {
            return false;
        }

        // ✅ LOCAL DB SYNC (MANDATORY)
        app(PropertyService::class)->seedLocalPricingData(
            $unit,
            'unit',
            now()->format('Y-m-d'),
            now()->addDays(365)->format('Y-m-d')
        );

        // Optional / non-blocking
        //$this->syncUnitPost($unit);

        return true;
    }

    public function syncMultiUnit(TblHomeMultiUnit $property): bool
    {
        Log::info('PriceLabs syncMultiUnit started', [
            'property_id' => $property->id,
            'listing_id' => $property->ru_property_id,
            'customer_id' => $this->customerId,
        ]);

        if (! $this->syncMultiUnitCore($property)) {
            return false;
        }

        app(PropertyService::class)->seedLocalPricingData(
            $property,
            'multi_unit',
            now()->format('Y-m-d'),
            now()->addDays(365)->format('Y-m-d')
        );

        //$this->syncMultiUnitPost($property);

        return true;
    }

    /* =====================================================
     | CORE SYNC
     ===================================================== */

    protected function syncUnitCore(TblHomeUnit $unit): bool
    {
        $payload = app(PriceLabsPayloadService::class);
        $api     = new PriceLabService();
        $listingId = (string) $unit->ru_property_id;

        try {
            /* ---------- LISTING ---------- */
            $listingPayload  = $payload->preparePriceLabsListingPayload($unit);
            $listingResponse = $api->syncListings($listingPayload);

            ApiAuditLogger::log([
                'customer_id'      => $this->customerId,
                'http_method'      => 'POST',
                'endpoint'         => '/listings',
                'request_payload'  => $listingPayload,
                'response_payload' => $listingResponse,
                'status_code'      => 200,
                'user_agent'       => 'PriceLabsSyncService',
            ]);

            if (! $this->isListingSuccessful($listingResponse, $listingId)) {
                return false;
            }

            /* ---------- RATE PLANS ---------- */
            $ratePayload  = $payload->preparePriceLabsRatePlanPayload($unit);
            // $rateResponse = $api->syncRatePlans($ratePayload);

            // ApiAuditLogger::log([
            //     'customer_id'      => $this->customerId,
            //     'http_method'      => 'POST',
            //     'endpoint'         => '/rate_plans',
            //     'request_payload'  => $ratePayload,
            //     'response_payload' => $rateResponse,
            //     'status_code'      => 200,
            //     'user_agent'       => 'PriceLabsSyncService',
            // ]);

            /* ---------- CALENDAR ---------- */
            $calendarPayload  = $payload->preparePriceLabsCalendarPayload($unit);
            $calendarResponse = $api->syncCalendars($calendarPayload);

            ApiAuditLogger::log([
                'customer_id'      => $this->customerId,
                'http_method'      => 'POST',
                'endpoint'         => '/calendar',
                'request_payload'  => $calendarPayload,
                'response_payload' => $calendarResponse,
                'status_code'      => 200,
                'user_agent'       => 'PriceLabsSyncService',
            ]);

            $unit->update([
                'is_basic_detail_sync' => 1,
                'is_rate_plan_sync'    => 1,
                'is_price_and_avaliability_sync' => 1,
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error('PriceLabs UNIT core sync exception', [
                'unit_id' => $unit->id,
                'error'   => $e->getMessage(),
            ]);
            return false;
        }
    }

    protected function syncMultiUnitCore(TblHomeMultiUnit $property): bool
    {
        $payload = app(PriceLabsPayloadService::class);
        $api = new PriceLabService();
        $listingId = (string) $property->ru_property_id;

        try {
            /* ---------------- LISTING ---------------- */
            $listingResponse = $api->syncListings(
                $payload->preparePriceLabsMultiUnitListingPayload($property)
            );

            if (!$this->isListingSuccessful($listingResponse, $listingId)) {
                Log::error('PriceLabs MULTI-UNIT listing failed', [
                    'listing_id' => $listingId,
                    'response' => $listingResponse,
                ]);
                return false;
            }

            /* ---------------- RATE PLANS ---------------- */
            $api->syncRatePlans(
                $payload->preparePriceLabsMultiUnitRatePlanPayload($property)
            );

            /* ---------------- CALENDAR ---------------- */
            $api->syncCalendars(
                $payload->preparePriceLabsMultiUnitCalendarPayload($property)
            );

            $property->update([
                'is_basic_detail_sync' => 1,
                'is_rate_plan_sync' => 1,
                'is_price_and_avaliability_sync' => 1,
            ]);

            return true;
        } catch (Throwable $e) {
            Log::error('PriceLabs MULTI-UNIT core sync exception', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }

    /* =====================================================
     | POST SYNC (SAFE / NON-BLOCKING)
     ===================================================== */

    protected function syncUnitPost(TblHomeUnit $unit): void
    {
        $payload = app(PriceLabsPayloadService::class);
        $api = new PriceLabService();

        try {
            // $api->registerIntegration(
            //     $payload->preparePriceLabsIntegrationPayload()
            // );
            $integrationPayload = $payload->preparePriceLabsIntegrationPayload();
            $integrationResp   = $api->registerIntegration($integrationPayload);

            ApiAuditLogger::log([
                'customer_id'      => $this->customerId,
                'http_method'      => 'POST',
                'endpoint'         => '/integration',
                'request_payload'  => $integrationPayload,
                'response_payload' => $integrationResp,
                'status_code'      => 200,
                'user_agent'       => 'PriceLabsSyncService',
            ]);

            // $api->getPrices(
            //     $unit->ru_property_id,
            //     'IAPI-FLEX-SGL',
            //     true
            // );

            $pricesResp = $api->getPrices(
                $unit->ru_property_id,
                'IAPI-FLEX-SGL',
                true
            );

            ApiAuditLogger::log([
                'customer_id'      => $this->customerId,
                'http_method'      => 'POST',
                'endpoint'         => '/get_prices',
                'request_payload'  => [
                    'listing_id' => $unit->ru_property_id,
                    'rate_plan'  => 'IAPI-FLEX-SGL',
                    'delta_only' => true,
                ],
                'response_payload' => $pricesResp,
                'status_code'      => 200,
                'user_agent'       => 'PriceLabsSyncService',
            ]);

            $api->getStatuses(
                $payload->prepareStatusPayload($unit)
            );

            $api->registerSyncUrl(
                $payload->prepareSyncUrlPayload()
            );
        } catch (Throwable $e) {
            Log::warning('PriceLabs UNIT post-sync failed', [
                'unit_id' => $unit->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    protected function syncMultiUnitPost(TblHomeMultiUnit $property): void
    {
        $payload = app(PriceLabsPayloadService::class);
        $api = new PriceLabService();

        try {
            // $api->registerIntegration(
            //     $payload->preparePriceLabsIntegrationPayload()
            // );

            $api->getPrices(
                $property->ru_property_id,
                'IAPI-FLEX-SGL',
                true
            );

            $api->getStatuses(
                $payload->prepareMultiUnitStatusPayload($property)
            );

            $api->registerSyncUrl(
                $payload->prepareSyncUrlPayload()
            );
        } catch (Throwable $e) {
            Log::warning('PriceLabs MULTI-UNIT post-sync failed', [
                'property_id' => $property->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /* =====================================================
     | HELPERS
     ===================================================== */

    protected function isListingSuccessful(array $response, string $listingId): bool
    {
        return in_array($listingId, $response['successes'] ?? [])
            || in_array($listingId, $response['success'] ?? []);
    }
}
