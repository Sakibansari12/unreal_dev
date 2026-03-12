<?php

namespace App\Exports;

use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class SaleReportExport implements FromCollection, WithHeadings
{
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

   public function collection()
{
    $req = $this->request;

    $checkin_date = null;
    $checkout_date = null;
    $user = Auth::guard('admin')->user();
    if (!empty($req['searchDateRange']) && str_contains($req['searchDateRange'], ' to ')) {
        [$from, $to] = explode(' to ', $req['searchDateRange']);
        try {
            $checkin_date = Carbon::createFromFormat('d/m/Y', trim($from))->format('Y-m-d');
            $checkout_date = Carbon::createFromFormat('d/m/Y', trim($to))->format('Y-m-d');
        } catch (\Exception $e) {
            $checkin_date = null;
            $checkout_date = null;
        }
    }
//dd($checkin_date , $checkout_date);
    $query = PropertyBooking::query()
        ->when(!empty($req['searchChannel']) && $req['searchChannel'] != 'All', function ($query) use ($req) {
            return $query->where('channel', $req['searchChannel']);
        })
        ->when(!empty($req['searchPaymentStatus']) && $req['searchPaymentStatus'] != 'All', function ($query) use ($req) {
            return $query->where('property_booking_status', $req['searchPaymentStatus']);
        })
        ->when(!empty($req['searchPropertyId']) && $req['searchPropertyId'] != 'All' && !empty($req['pType']), function ($query) use ($req) {
            return $query->where('property_id', $req['searchPropertyId'])
                         ->where('pType', $req['pType']);
        })
        ->when(!empty($req['searchtype']) && $checkin_date && $checkout_date, function ($q) use ($req, $checkin_date, $checkout_date) {
            return $req['searchtype'] === 'checkin'
                ? $q->whereBetween('checkin_date', [$checkin_date, $checkout_date])
                : $q->whereBetween('created_at', [$checkin_date, $checkout_date]);
        })
        ->when(empty($req['searchtype']) && $checkin_date && $checkout_date, function ($q) use ($checkin_date, $checkout_date) {
            return $q->where('checkin_date', '>=', $checkin_date)
                     ->where('checkout_date', '<=', $checkout_date);
        })

        ->when($user->role_id != 1, function ($query) use ($user) {
            if (in_array($user->role_id, [3, 4, 5, 2])) {
                return $query->where('property_bookings.parent_user_id', $user->parent_user_id);
            } elseif ($user->role_id == 6) {
                return $query->where('property_bookings.owner_id', $user->id);
            } elseif ($user->role_id == 7) {
                return $query->where('property_bookings.parent_user_id', $user->id);
            } elseif ($user->role_id == 8) {
                return $query->where('property_bookings.travelagent_id', $user->id);
            }
        })
        ->when($user->role_id != 1 && $user->role_id != 8, function ($query) use ($user) {
            return $query->whereNull('property_bookings.travelagent_id');
        })


        ->where('payable_amount', '>', 0)
        ->whereNull('deleted_at')
        ->with('paymentRequests')
        ->orderBy('created_at', 'desc');

    // Fetch properties
    $unitProperties = TblHomeUnit::select('id', 'unit_name as property_name', 'location', 'home_type', 'state')
        ->get()
        ->mapWithKeys(fn($row) => ['unit_' . $row->id => $row]);

    $multiUnitProperties = TblHomeMultiUnit::select('id', 'unit_name as property_name', 'location', 'home_type', 'state')
        ->get()
        ->mapWithKeys(fn($row) => ['multiunit_' . $row->id => $row]);

    // Data processing
    $data = $query->get();
//dd($data);
 
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

    foreach ($data as $booking) {
        $paymentModes = $booking->paymentRequests->pluck('payment_mode')->unique()->implode(', ');

        $guest = json_decode($booking->customer_detail);
        $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $booking->id)
            ->where('booking_request_status', 'Payment Received')
            ->sum('amount');

        $key = $booking->pType . '_' . $booking->property_id;
        $property = $booking->pType == 'unit'
            ? ($unitProperties[$key] ?? null)
            : ($multiUnitProperties[$key] ?? null);
       // dd($booking->tax_amount);
        $finalData[] = [
            'booking_id' => $booking->booking_id,
            'home_name' => $property->property_name ?? '',
            'location' => $property->location ?? '',
            'booking_date' => $booking->created_at,
            'checkin_date' => $booking->checkin_date,
            'checkout_date' => $booking->checkout_date,
            'guest_email_id' => $guest->email,
            'guest_name' => ($guest->first_name ?? '') . ' ' . ($guest->last_name ?? ''),
            'mobile_number' => $guest->mobile_number,
            'channel' => $booking->channel,
            // 'base_price' => formatInr($booking->total_amount - $booking->website_markup_price),
            'base_price' => formatInr($booking->base_price),
            'website_markup_price' => formatInr($booking->website_markup_price),
            'tax' => $booking->tax ? $booking->tax . '%' : '',
           // 'tax_amount' => $booking->tax_amount,
           //'tax_amount' => ($booking->tax_amount === null || $booking->tax_amount === '') ? '0' : (string)$booking->tax_amount,
           'tax_amount' => 'INR ' . formatInr(
    ($booking->tax_amount === null || $booking->tax_amount === '' ? 0 : $booking->tax_amount)
),

            'payable_amount' => formatInr($booking->payable_amount),
            'payment_received' => ($booking->channel != 'PMS') ?  formatInr($booking->paid_amount) : ($paidAmount ?  $paidAmount : ''),
            'payment_mode' => $paymentModes,
            'invoice' => "",
            'property_booking_status' => $booking->property_booking_status,
        ];
    }

    return collect($finalData);
}

    public function headings(): array
    {
        return [
            'Booking ID',
            'Property Name',
            'Location',
            'Booking Date',
            'Checkin Date',
            'Checkout Date',
            'Guest Email ID',
            'Guest Name',
            'Guest Mobile No.',
            'Channel',
            'Base Price',
            'Markup Price',
            'Tax',
            'Tax Amount',
            'Payable',
            'Paid',
            'Payment Mode',
            'Invoice No.',
            'Booking Status',
        ];
    }
}