<?php

namespace App\Exports;

use App\Models\BookingGuestId;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GuestDatabaseExport implements FromCollection, WithHeadings, WithMapping, WithColumnFormatting
{
    protected $search_name;
    protected $search_property_name;
    protected $search_email;
    protected $search_mobile;

    public function __construct(array $data)
    {
        $this->search_name          = $data['search_name'] ?? null;
        $this->search_property_name = $data['search_property_name'] ?? null;
        $this->search_email         = $data['search_email'] ?? null;
        $this->search_mobile        = $data['search_mobile'] ?? null;
    }

    public function collection()
    {
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");

        $user = Auth::guard('admin')->user();

        /*
        |--------------------------------------------------------------------------
        | Step 1: Decide ONE record per mobile_no (same as listing)
        |--------------------------------------------------------------------------
        */
        $baseIds = BookingGuestId::selectRaw('MIN(id) as id')
            ->groupBy('mobile_no');

        /*
        |--------------------------------------------------------------------------
        | Step 2: Main Query – sirf wahi records export honge
        |--------------------------------------------------------------------------
        */
        $query = BookingGuestId::with([
                'propertyBooking' => function ($q) {
                    $q->whereNull('deleted_at');
                },
                'propertyBooking.homeUnit.locationData',
                'propertyBooking.homeMultiUnit.locationData',
            ])
            ->whereIn('id', $baseIds)

            // 🔹 Name Filter
            ->when(!empty($this->search_name), function ($q) {
                $q->where('name', 'like', '%' . $this->search_name . '%');
            })

            // 🔹 Email Filter
            ->when(!empty($this->search_email), function ($q) {
                $q->where('email', 'like', '%' . $this->search_email . '%');
            })

            // 🔹 Mobile Filter
            ->when(!empty($this->search_mobile), function ($q) {
                $q->where('mobile_no', 'like', '%' . $this->search_mobile . '%');
            })

            // 🔹 Property Name Filter (unit & multiunit)
            ->when(!empty($this->search_property_name), function ($q) {
                $search = $this->search_property_name;

                $q->where(function ($qq) use ($search) {
                    $qq->whereHas('propertyBooking.homeUnit', function ($q2) use ($search) {
                        $q2->where('unit_name', 'like', "%{$search}%");
                    })
                    ->orWhereHas('propertyBooking.homeMultiUnit', function ($q2) use ($search) {
                        $q2->where('unit_name', 'like', "%{$search}%");
                    });
                });
            })

            // 🔹 Role-based filter
            ->when($user->role_id != 1, function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })

            ->whereHas('propertyBooking', function ($q) {
                $q->whereNull('deleted_at');
            })

            ->orderBy('id', 'ASC');

        return $query->get();
    }

    public function map($guest): array
    {
        $propertyName = '';
        $locationName = '';

        if ($guest->propertyBooking) {
            if ($guest->propertyBooking->pType === 'unit') {
                $propertyName = $guest->propertyBooking->homeUnit->unit_name ?? '';
                $locationName = $guest->propertyBooking->homeUnit->locationData->location_name ?? '';
            } elseif ($guest->propertyBooking->pType === 'multiunit') {
                $propertyName = $guest->propertyBooking->homeMultiUnit->unit_name ?? '';
                $locationName = $guest->propertyBooking->homeMultiUnit->locationData->location_name ?? '';
            }
        }

        return [
            $guest->name ?? '',
            $propertyName,
            $locationName,
            $guest->email ?? '',
            !empty($guest->mobile_no)
                ? (!empty($guest->country_code)
                    ? '+' . $guest->country_code . ' ' . $guest->mobile_no
                    : $guest->mobile_no)
                : '',
        ];
    }

    public function headings(): array
    {
        return [
            'NAME',
            'PROPERTY NAME',
            'LOCATION',
            'EMAIL',
            'PHONE NUMBER',
        ];
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_TEXT,
            'B' => NumberFormat::FORMAT_TEXT,
            'C' => NumberFormat::FORMAT_TEXT,
            'D' => NumberFormat::FORMAT_TEXT,
            'E' => NumberFormat::FORMAT_TEXT,
        ];
    }
}
