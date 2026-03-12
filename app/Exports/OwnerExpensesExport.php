<?php

namespace App\Exports;

use App\Models\TblOwnerExpenses;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Collection;
use App\Services\PropertyService;
class OwnerExpensesExport implements FromCollection, WithHeadings
{
    protected $request;
    protected $propertyService;
    public function __construct($request)
    {
        $this->request = $request;
        $this->propertyService = new PropertyService();
    }
    

    public function collection()
    {
        $request = (object) $this->request;

        // Date filter
        $start_date = null;
        $end_date = null;
        if (!empty($request->searchDateRange) && str_contains($request->searchDateRange, ' to ')) {
            [$from, $to] = explode(' to ', $request->searchDateRange);
            try {
                $start_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($from))->format('Y-m-d');
                $end_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($to))->format('Y-m-d');
            } catch (\Exception $e) {
                $start_date = null;
                $end_date = null;
            }
        }

        // Base query
       // $query = TblOwnerExpenses::query();
        $query = $this->propertyService->applyUserRoleFilter(TblOwnerExpenses::query());
        // Expense name filter
        if (!empty($request->search)) {
            $query->where('expenses_name', 'like', '%' . $request->search . '%');
        }

        // Date filter
        if ($start_date && $end_date) {
            $query->whereBetween('date', [$start_date, $end_date]);
        }

        // Unit filter
        if (!empty($request->searchUnitId)) {
            $query->where('pType', $request->pType)->where('property_id', $request->searchUnitId);
        }

        $items = $query->orderBy('id', 'desc')->get();

        // Attach related unit + owner
        $items->transform(function ($item) {
            if ($item->pType === 'unit') {
                $item->unit_property = TblHomeUnit::with('ownerData')->find($item->property_id);
            } elseif ($item->pType === 'multiunit') {
                $item->unit_property = TblHomeMultiUnit::with('ownerData')->find($item->property_id);
            } else {
                $item->unit_property = null;
            }
            return $item;
        });

        // Owner name filter
        if (!empty($request->owner_name)) {
            $items = $items->filter(function ($item) use ($request) {
                if (!$item->unit_property || !$item->unit_property->ownerData) {
                    return false;
                }

                return stripos($item->unit_property->ownerData->name, $request->owner_name) !== false;
            })->values();
        }

        $currentPage = isset($request->page) ? (int)$request->page : 1;
        $perPage = 50;
        $items = $items->slice(($currentPage - 1) * $perPage, $perPage)->values();

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


        // Format data for Excel
        $exportData = $items->map(function ($item) {
            return [
                'date' => \Carbon\Carbon::parse($item->date)->format('d M, Y'),
                'expenses_name' => $item->expenses_name ?? '',
                'amount' => formatInr($item->amount) ?? '',
                'unit_name' => $item->unit_property->unit_name ?? '',
                'owner_name' => $item->unit_property->ownerData->name ?? '',
            ];
        });

        return new Collection($exportData);
    }

    public function headings(): array
    {
        return [
            "Date",
            "Expenses Name",
            "Amount",
            "Unit",
            "Owner Name",
        ];
    }
}