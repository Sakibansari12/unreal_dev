<?php


namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ApiAuditLogger
{
    public static function log(array $data): void
    {
        try {
            Http::timeout(3)->withHeaders([
                'X-INTERNAL-SECRET' => config('services.easyrentit.internal_secret'),
            ])->post(
                config('services.easyrentit.base_url') . '/api/internal/staah/api-log',
                $data
            );
        } catch (\Throwable $e) {
            Log::debug('API audit log skipped', [
                'reason' => $e->getMessage(),
            ]);
        }
    }
}
