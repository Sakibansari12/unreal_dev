<?php

namespace App\Http\Traits;

use Carbon\Carbon;
use Symfony\Component\HttpFoundation\Response;

trait ValidatesDateRange
{
    protected function validateDateRange($request): array
    {
        if (!$request->filled('date_from') || !$request->filled('date_to')) {
            abort(
                Response::HTTP_BAD_REQUEST,
                'date_from and date_to are required'
            );
        }

        try {
            $dateFrom = Carbon::createFromFormat('Y-m-d', $request->date_from)->startOfDay();
            $dateTo   = Carbon::createFromFormat('Y-m-d', $request->date_to)->startOfDay();
        } catch (\Exception $e) {
            abort(
                Response::HTTP_BAD_REQUEST,
                'Invalid date format. Required format is YYYY-MM-DD'
            );
        }

        if ($dateFrom->gt($dateTo)) {
            abort(
                Response::HTTP_BAD_REQUEST,
                'date_from must be less than or equal to date_to'
            );
        }

        return [$dateFrom, $dateTo];
    }
}
