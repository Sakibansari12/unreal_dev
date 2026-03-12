<?php

namespace App\Exports;

use App\Models\PropertyBooking;
use App\Models\TblHome;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Auth;

class PropertyBookingExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $reqParameter = $this->request;
        $user = Auth::guard('admin')->user();
        $propertyIds = [];
        if (isset($reqParameter['role']) && $reqParameter['role'] == 'Owner') {
            $propertyIds = TblHome::where('user_id', $reqParameter['roleId'])
                ->get()
                ->pluck('id')
                ->toArray();
        }

        $list = PropertyBooking::query()
            ->when(isset($reqParameter['role']) && $reqParameter['role'] == 'Owner', function ($query) use ($propertyIds) {
                return $query->whereIn('property_id', $propertyIds);
            })
            ->when(isset($reqParameter['property_id']) && $reqParameter['property_id'] != '', function ($query) use ($reqParameter) {
                return $query->where('property_id', $reqParameter['property_id']);
            })
            ->when(isset($reqParameter['booking_id']) && $reqParameter['booking_id'] != '', function ($query) use ($reqParameter) {
                return $query->where('booking_id', $reqParameter['booking_id']);
            })
            ->when(isset($reqParameter['channel']) && $reqParameter['channel'] != '', function ($query) use ($reqParameter) {
                return $query->where('channel', $reqParameter['channel']);
            })
            ->when(isset($reqParameter['payment_status']) && $reqParameter['payment_status'] != '', function ($query) use ($reqParameter) {
                return $query->where('booking_status', $reqParameter['payment_status']);
            })
            ->when(isset($reqParameter['booking_status']) && $reqParameter['booking_status'] != '', function ($query) use ($reqParameter) {
                return $query->where('property_booking_status', $reqParameter['booking_status']);
            })
            ->when($user->role_id != 1 && $user->role_id != 8, function ($query) use ($user) {
                return $query->whereNull('travelagent_id');
            })
            ->when(in_array($user->role_id, [2, 3, 4, 5]), function ($query) use ($user) {
                return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
            })
            ->when($user->role_id == 6, function ($query) use ($user) {
                return $query->where('property_bookings.owner_id', $user->id);
            })
            ->when($user->role_id == 7, function ($query) use ($user) {
                return $query->where('property_bookings.parent_user_id', $user->id);
            })
            ->when($user->role_id == 8, function ($query) use ($user) {
                return $query->where('property_bookings.travelagent_id', $user->id);
            })
            ->when(isset($reqParameter['checkin_date']) && !empty($reqParameter['checkin_date']) &&
                isset($reqParameter['checkout_date']) && !empty($reqParameter['checkout_date']), function ($query) use ($reqParameter) {
                return $query->where('checkin_date', '>=', $reqParameter['checkin_date'])
                    ->where('checkin_date', '<=', $reqParameter['checkout_date']);
            })
            ->orderBy('checkin_date', 'desc')
            ->get();
      
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
      
      
      
        $finalData = [];

        foreach ($list as $key => $value) {
            $property = $this->getProperty($value);

            if (!$property) {
                continue;
            }

            $guest_detail = json_decode($value->customer_detail, true) ?? [];
           
            $detail = [
                'booking_id' => $value->booking_id ?? 'N/A',
                'home_name' => $property->unit_name ?? 'N/A',
                'guest_name' => ($guest_detail['first_name'] ?? '') . ' ' . ($guest_detail['last_name'] ?? ''),
                'guest_mobile_no' => 
                !empty($guest_detail['mobile_number'])
                    ? ( (!empty($guest_detail['country_code']) ? '+' . $guest_detail['country_code'] . ' ' : '') . $guest_detail['mobile_number'] )
                    : 'N/A',

                'guest_email_id' => $guest_detail['email'] ?? 'N/A',
                'channel' => $value->channel ?: 'N/A',
                'Price' => 'INR ' . formatInr($value->payable_amount ?? '0'),
                'payment_received' => ($value->paid_amount != 0) ? 'INR ' . formatInr($value->paid_amount) : '-',
                'payment_status' => ucfirst($value->booking_status ?? 'unknown'),
                'booking_status' => $value->property_booking_status ?? 'N/A',
                'checkin_date' => $value->checkin_date ?? 'N/A',
                'checkout_date' => $value->checkout_date ?? 'N/A'
            ];

            array_push($finalData, $detail);
        }

        return collect($finalData);
    }

    protected function getProperty($booking)
    {
        if (isset($booking->pType)) {
            if ($booking->pType == 'unit') {
                return TblHomeUnit::where('id', $booking->property_id)
                    ->whereNotNull('ru_property_id')
                    ->first();
            }

            if ($booking->pType == 'multiunit') {
                return TblHomeMultiUnit::where('id', $booking->property_id)
                    ->whereNotNull('ru_property_id')
                    ->first();
            }
        }

        $property = TblHomeUnit::where('id', $booking->property_id)
            ->whereNotNull('ru_property_id')
            ->first();

        if (!$property) {
            $property = TblHomeMultiUnit::where('id', $booking->property_id)
                ->whereNotNull('ru_property_id')
                ->first();
        }

        return $property;
    }

    public function headings(): array
    {
        return [
            "BOOKING ID",
            "PROPERTY NAME",
            "GUEST NAME",
            "GUEST MOBILE NO",
            "GUEST EMAIL ID",
            "CHANNEL",
            "PRICE",
            "PAYMENT RECEIVED",
            "PAYMENT STATUS",
            "BOOKING STATUS",
            "ARRIVAL",
            "DEPARTURE"
        ];
    }
}