<?php

namespace App\Services;

use App\Exceptions\PriceLabsException;
use InvalidArgumentException;
use App\Models\PriceLabsApiLog;

class PriceLabService{
    
    protected string $baseUrl;
    protected int $timeout;

    public function __construct(){
        $config = config('pricelabs', []);
        $this->baseUrl = rtrim($config['base_url'] ?? '', '/');
        $this->timeout = (int)($config['timeout'] ?? 30);

        if (empty($this->baseUrl)) {
            throw new InvalidArgumentException('PriceLabs base url is not configured.');
        }
    }

    public function syncListings(array $listings): array{
        return $this->postJson('/listings', ['listings' => $listings]);
    }

    public function syncRatePlans(array $ratePlans): array{
        return $this->postJson('/rate_plans', ['rate_plans' => $ratePlans]);
    }

    public function registerIntegration(array $integration): array{
        return $this->postJson('/integration', ['integration' => $integration]);
    }

    public function registerSyncUrl(array $payload): array{
        return $this->postJson('/sync_url', $payload);
    }

    public function getPrices(array $payload): array{
        return $this->postJson('/get_prices', $payload);
    }

    public function syncReservations(array $reservations): array{
     
        return $this->postJson('/reservations', $reservations);
    }

    public function getStatuses(array $statuses): array{
        return $this->postJson('/status', $statuses);
    }
    
    public function syncCalendars(array $calendars): array{
        return $this->postJson('/calendar', ['calendars' => $calendars]);
    }
    
    
    public function setAuthToken($token): array{
        return $this->postJson('/set_auth_token', ['user_token' => $token]);
    }

    /**
     * Send POST request as JSON
     */
    protected function postJson(string $endpoint, array $payload = []): array{
        $body = json_encode([
            //'domain' => 'staah.tempsite.in',
            'domain' => config('services.pricelabs.domain'),
            'payload' => $payload,
        ]);

        $ch = curl_init($this->baseUrl . $endpoint);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS  => $body,
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_HTTPHEADER   => [
                'Content-Type: application/json', // JSON header
            ],
        ]);

        $response = curl_exec($ch);
    
        $statusCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
    
        // Save API log including domain + payload
        PriceLabsApiLog::create([
            'api_name'      => $endpoint,
            'http_method'   => 'POST',
            'request_body'  => $body,   
            'response_body' => $response,
            'status_code'   => $statusCode,
            'success'       => $statusCode >= 200 && $statusCode < 300,
        ]);
        \Log::info($response);
        return json_decode($response, true) ?? [];
    }
}
