<?php

namespace App\Exports;

use App\Models\TblOwnerExpenses;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\PropertyBooking;
use Illuminate\Support\Collection;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class OwnerRevenueReportExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $months;

    public function __construct($request)
    {
        $this->request = $request;

        $filterYear = $request['year'] ?? now()->year;

        // Initialize months (Jan - Dec for selected year)
        $this->months = collect(range(1, 12))->map(
            fn($i) => Carbon::createFromDate($filterYear, $i, 1)
        );
    }


    public function collection()
{
    $request = (object) $this->request;

    $filterYear = $request->year ?? now()->year;
    $filterPType = $request->pType ?? null;
    $filterPropertyId = $request->property_id ?? null;

    $groupedData = [];

    if (!function_exists('formatInr')) {
    function formatInr($amount)
    {
        $amount = (float) $amount;
        if ($amount == 0) return '0';
        $amount = round($amount);

        // Indian numbering format logic
        $num = explode('.', $amount);
        $last3 = substr($num[0], -3);
        $restUnits = substr($num[0], 0, -3);
        if ($restUnits != '') {
            $last3 = ',' . $last3;
        }
        $restUnits = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $restUnits);
        $formatted = $restUnits . $last3;
        return $formatted;
    }
}


    if ($filterPType && $filterPropertyId) {
        foreach ($this->months as $month) {
            $start = $month->copy()->startOfMonth();
            $end   = $month->copy()->endOfMonth();
            $monthKey = $month->format('F');

            // Bookings
            $bookingQuery = PropertyBooking::whereBetween('checkin_date', [$start, $end])
                ->where('pType', $filterPType)
                ->where('property_id', $filterPropertyId);
            $bookings = $bookingQuery->get();

            $bookingSummary = $bookings->groupBy(fn($item) => $item->property_id . '||' . $item->pType)
                ->map(fn($items) => [
                    'bookings' => $items->count(),
                    'amount'   => $items->sum('base_price'),
                ]);

            // Expenses
            $expenseQuery = TblOwnerExpenses::whereBetween('date', [$start, $end])
                ->where('pType', $filterPType)
                ->where('property_id', $filterPropertyId);
            $expenses = $expenseQuery->get();

            $expenseSummary = $expenses->groupBy(fn($item) => $item->property_id . '||' . $item->pType)
                ->map(fn($items) => [
                    'expenses' => $items->sum('amount'),
                ]);

            $keys = $bookingSummary->keys()->merge($expenseSummary->keys())->unique();

            foreach ($keys as $key) {
                [$unitId, $unitPType] = explode('||', $key);

                $booking = $bookingSummary[$key] ?? ['bookings' => 0, 'amount' => 0];
                $expense = $expenseSummary[$key] ?? ['expenses' => 0];

                $uKey = $unitId . '||' . $unitPType;

                if (!isset($groupedData[$uKey])) {
                    $groupedData[$uKey] = [
                        'months' => []
                    ];
                }

                $groupedData[$uKey]['months'][$monthKey] = [
                    'bookings' => $booking['bookings'],
                    'amount'   => $booking['amount'],
                    'expenses' => $expense['expenses'],
                    'total'    => $booking['amount'] - $expense['expenses'],
                ];
            }
        }
    }

    // Format rows
    $rows = new Collection();

    foreach ($groupedData as $data) {
        // No. of Bookings Row
        $blankRow = [""];
        $rows->push($blankRow);
        $bookingRow = ["No. of Bookings"];
        foreach ($this->months as $month) {
            $monthKey = $month->format('F');
            $val = $data['months'][$monthKey]['bookings'] ?? '';
            $bookingRow[] = ($val != 0) ? $val : ''; // blank if 0
        }
        $rows->push($bookingRow);

        // Amount Row
        $amountRow = ["Amount"];
        foreach ($this->months as $month) {
            $monthKey = $month->format('F');
            $val = $data['months'][$monthKey]['amount'] ?? '';
            $amountRow[] = ($val != 0) ? formatInr($val) : ''; // blank if 0
        }
        $rows->push($amountRow);

        // Expenses Row
        $expenseRow = ["Expenses"];
        foreach ($this->months as $month) {
            $monthKey = $month->format('F');
            $val = $data['months'][$monthKey]['expenses'] ?? '';
            $expenseRow[] = ($val != 0) ? formatInr($val) : ''; // blank if 0
        }
        $rows->push($expenseRow);

        // Total Row
        $totalRow = ["Total"];
        foreach ($this->months as $month) {
            $monthKey = $month->format('F');
            $val = $data['months'][$monthKey]['total'] ?? '';
            $totalRow[] = ($val != 0) ? formatInr($val) : ''; // blank if 0
        }
        $rows->push($totalRow);
    }

    return $rows;
}

public function headings(): array
{
    $headings = [''];
    foreach ($this->months as $month) {
        $headings[] = $month->format('M Y');
    }
    return $headings;
}


}