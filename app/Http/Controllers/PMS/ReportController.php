<?php

namespace App\Http\Controllers\PMS;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\TblHome;
use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use App\helper\MasterHelper;
use Illuminate\Support\Collection;
use App\Models\BookingGuestId;
use App\Models\BookingEnquiry;
use App\Models\TblGst;
use App\Models\User;
use App\Models\TblPropertyPublishLog;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Exports\BookingExport;
use App\Mail\BookingConfirmationEmail;
use App\Services\RazorpayService;
use App\Exports\SaleReportExport;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Services\PropertyService;
use App\Exports\PoliceVerificationReport;
use App\Mail\BookingCancellationEmail;
use DB;
use Mail;
use Carbon\Carbon;
use Razorpay\Api\Api;
use Barryvdh\DomPDF\Facade\Pdf;

class ReportController extends Controller{


protected $propertyService;

    public function __construct(){
        $this->propertyService = new PropertyService();
    }

public function saleReportList(Request $request)
{


    $checkin_date = null;
    $checkout_date = null;
    $user = Auth::guard('admin')->user();
    if ($request->filled('searchDateRange') && str_contains($request->searchDateRange, ' to ')) {
        [$from, $to] = explode(' to ', $request->searchDateRange);

        try {
            $checkin_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($from))->format('Y-m-d');
            $checkout_date = \Carbon\Carbon::createFromFormat('d/m/Y', trim($to))->format('Y-m-d');
        } catch (\Exception $e) {
            $checkin_date = null;
            $checkout_date = null;
        }
    }
    
    
    $query = PropertyBooking::query()
        ->when($request->filled('searchChannel') && $request->searchChannel != 'All', function ($query) use ($request) {
            return $query->where('channel', $request->searchChannel);
        })
        ->when($request->filled('searchPaymentStatus') && $request->searchPaymentStatus != 'All', function ($query) use ($request) {
            return $query->where('property_booking_status', $request->searchPaymentStatus);
        })
         ->when($user->role_id != 1 && $user->role_id != 8, function ($query) use ($user) {
            return $query->whereNull('travelagent_id');
        })
        ->when(
                $request->filled('searchPropertyId') && $request->searchPropertyId != 'All' && $request->filled('pType'),
                function ($query) use ($request) {
                    return $query->where('property_id', $request->searchPropertyId)
                                ->where('pType', $request->pType);
                }
            )
        ->when(
            $request->filled('searchtype') && $checkin_date && $checkout_date,
            function ($q) use ($request, $checkin_date, $checkout_date) {
                return $request->searchtype === 'checkin'
                    ? $q->whereBetween('checkin_date', [$checkin_date, $checkout_date])
                    : $q->whereBetween('created_at', [$checkin_date, $checkout_date]);
            }
        )
        ->when(
            !$request->filled('searchtype') && $checkin_date && $checkout_date,
            fn($q) => $q->where('checkin_date', '>=', $checkin_date)
                        ->where('checkout_date', '<=', $checkout_date)
        )

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



        ->where('payable_amount', '>', 0)
        ->whereNull('deleted_at')
        ->with('paymentRequests')
        ->orderBy('created_at', 'desc');

        $paginatedResults = $query->paginate(50);
   
    $unitProperties = TblHomeUnit::select('id', 'unit_name as property_name', 'location', 'home_type', 'state')
        ->get()
        ->mapWithKeys(fn($row) => ['unit_' . $row->id => $row]);

    $multiUnitProperties = TblHomeMultiUnit::select('id', 'unit_name as property_name', 'location', 'home_type', 'state')
        ->get()
        ->mapWithKeys(fn($row) => ['multiunit_' . $row->id => $row]);

    // Step 2: Process records
    $finalData = [];


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


    foreach ($paginatedResults->items() as $booking) {
        //dd($booking);
        $guest = json_decode($booking->customer_detail);
        $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $booking->id)
            ->where('booking_request_status', 'Payment Received')
            ->sum('amount');

        $key = $booking->pType . '_' . $booking->property_id;
      //  dd($key);
        $property = $booking->pType === 'unit'
            ? ($unitProperties[$key] ?? null)
            : ($multiUnitProperties[$key] ?? null);

        $finalData[] = [
            'booking_id' => $booking->booking_id,
            'property_booking_status' => $booking->property_booking_status,
            'checkin_date' => $booking->checkin_date,
            'checkout_date' => $booking->checkout_date,
            'no_of_nights' => $booking->no_of_nights,
            'no_of_adult' => $booking->no_of_adult,
            'no_of_children' => $booking->no_of_children,
            'booking_id' => $booking->booking_id,
            'home_name' => $property->property_name ?? '',
            'location' => $property->location ?? '',
            'guest_name' => ($guest->first_name ?? '') . ' ' . ($guest->last_name ?? ''),
            'channel' => $booking->channel,
           // 'base_price' => 'INR ' . number_format((float) ($booking->total_amount - $booking->website_markup_price)),
            'base_price' => 'INR ' . formatInr($booking->total_amount - $booking->website_markup_price),
            'website_markup_price' => 'INR ' . formatInr($booking->website_markup_price),
            'payable_amount' => 'INR ' . formatInr($booking->payable_amount),
            'paid_amount' => ($booking->channel != 'PMS') ? 'INR ' . formatInr($booking->paid_amount) : ($paidAmount ? 'INR ' . $paidAmount : ''),
            'tax' => $booking->tax ? $booking->tax . '%' : '',
            'tax_amount' => 'INR ' . formatInr($booking->tax_amount),
        ];
    }
//dd($finalData);
    // Step 3: Property filter dropdown
    $unitsQuery = TblHomeUnit::select('id', 'ru_property_id', 'unit_name')
        ->whereNotNull('ru_property_id');
    $units = $this->propertyService->applyUserRoleFilter($unitsQuery)
        ->get()
        ->map(function ($unit) {
            $unit->pType = 'unit';
            return $unit;
        });

    $multiUnitsQuery = TblHomeMultiUnit::select('id', 'ru_property_id', 'unit_name')
        ->whereNotNull('ru_property_id');
    $multiUnits = $this->propertyService->applyUserRoleFilter($multiUnitsQuery)
        ->get()
        ->map(function ($unit) {
            $unit->pType = 'multiunit';
            return $unit;
        });

    $propertyList = $units->concat($multiUnits);
    return view('pms.report.sale-report', [
        'list' => $finalData,
        'propertyList' => $propertyList,
        'isLoading' => false,
        'pagination' => $paginatedResults
    ]);
}


    public function saleReportExportFile(Request $request){
         //dd($request->all());
        return  Excel::download(new SaleReportExport($request->all()), 'salereport.xlsx');
    }


     public function salePoliceVerification(Request $request){
       return view('pms.report.police-verification');
    }


    public function policeVerificationReport(Request $request){
        try{
            $list = PropertyBooking::query()
                ->when(isset($request->property_id), function ($query) use ($request) {
                    return $query->where('property_id', $request->property_id);
                })
                ->leftJoin('tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')->with('paymentRequests')
                ->orderBy('property_bookings.created_at', 'desc')
                ->get(['tbl_homes.home_name', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);
            $finalData = array();
            foreach($list as $key=>$value){
                $guest_detail = $value->customer_detail;
                $detail = array();

                $name = $guest_detail['first_name'].' '.$guest_detail['last_name'];
                $email = $guest_detail['email'];
                $mobile_no = $guest_detail['mobile_number'];
                $detail['guest_name'] = $guest_detail['first_name'].' '.$guest_detail['last_name'];
                $detail['guest_email_id'] = $guest_detail['email'];
                $detail['guest_mobile_no'] = $guest_detail['mobile_number'];
                $detail['checkin_date'] = $value->checkin_date;
                $detail['checkout_date'] = $value->checkout_date;

                if(isset($request->name) || isset($request->email) || isset($request->mobile_no)){
                    if(isset($request->name)  && !isset($request->email) && !isset($request->email)){
                        if(str_contains(strtolower($name), strtolower($request->name))){
                            array_push($finalData, $detail);
                        }
                    }
                    if(isset($request->email) && !isset($request->name) && !isset($request->mobile_no)){
                        if($email !=[]){
                            if(str_contains($email, $request->email)){
                                array_push($finalData, $detail);
                            }
                        }
                    }
                    if(isset($request->mobile_no) && !isset($request->name) && !isset($request->email)){
                        if(str_contains($mobile_no, $request->mobile_no)){
                            array_push($finalData, $detail);
                        }
                    }
                    if(isset($request->name) && isset($request->email) && !isset($request->mobile_no)){
                        if($email !=[]){
                            if(str_contains(strtolower($name), strtolower($request->name)) && str_contains($email, $request->email)){
                                array_push($finalData, $detail);
                            }
                        }
                    }
                    if(!isset($request->name) && isset($request->email) && isset($request->mobile_no)){
                        if($email !=[]){
                            if(str_contains(strtolower($email), strtolower($request->email)) && str_contains(strtolower($mobile_no), strtolower($request->mobile_no))){
                                array_push($finalData, $detail);
                            }
                        }
                    }

                    if(isset($request->name) && !isset($request->email) && isset($request->mobile_no)){
                        if(str_contains(strtolower($name), strtolower($request->name)) && str_contains(strtolower($mobile_no), strtolower($request->mobile_no))){
                            array_push($finalData, $detail);
                        }

                    }

                    if(isset($request->name) && isset($request->email) && isset($request->mobile_no)){
                        if($email !=[]){
                            if(str_contains(strtolower($name), strtolower($request->name)) && str_contains(strtolower($email), strtolower($request->email)) && str_contains(strtolower($mobile_no), strtolower($request->mobile_no))){
                                array_push($finalData, $detail);
                            }
                        }
                    }
                }
                else{
                    array_push($finalData, $detail);
                }
            }
            return response()->json([
                'status'=>true,
                'data'=>$finalData,
                'message' => 'Booking Enquiry Listed Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function policeVerificationExport(Request $request){
        return Excel::download(new PoliceVerificationReport($request->all()), 'policereport.xlsx');
    }
    
    
    
    


//  public function PropertyBillingReportList(Request $request){
//         $req = $request->all();

//         // Selected month
//         $selectedMonth = isset($req['month_drop_down']) && $req['month_drop_down'] != ''
//             ? date('Y-m-01', strtotime($req['month_drop_down']))
//             : date('Y-m-01');

//         $monthStart = Carbon::parse($selectedMonth)->startOfMonth();
//         $monthEnd   = Carbon::parse($selectedMonth)->endOfMonth();
//         $currentDate = Carbon::now();

//         $units = TblHomeUnit::select('id', 'ru_property_id','is_published_date', 'unit_name', 'ru_status')->whereNotNull('ru_property_id')->get();
//         $units = $units->map(function ($unit) use ($monthStart, $monthEnd, $currentDate, $selectedMonth) {
//             if($unit->is_published_date <=  $selectedMonth){
//                     if($selectedMonth < $currentDate->format('Y-m-01')){
//                         $count = Carbon::parse($selectedMonth)->diffInDays($monthEnd) + 1;
//                     }
//                     else{
//                         $count = $currentDate->diffInDays($monthStart) + 1;
//                     }
                    
//                     $logs = TblPropertyPublishLog::where('property_id', $unit->id)->where('pType', $unit->pType)->get();
//                     $countDaysPublished = 0;
//                     foreach($logs as $log){
//                         $adate1 = Carbon::parse($log->adate1);
//                         $adate2 = Carbon::parse($log->adate2);
//                         if($adate1 != $adate2){
//                             if($adate1->greaterThan($monthEnd) || $adate2->lessThan($monthStart)){
//                                 continue;
//                             }
//                             if(empty($log->adate2)){
//                                 $countDaysPublished += Carbon::now()->diffInDays($adate1);
//                             }
//                             else{
//                                 $start = $adate1->lessThan($monthStart) ? $monthStart : $adate1;
//                                 $end = $adate2->greaterThan($monthEnd) ? $monthEnd : $adate2;
//                                 $countDaysPublished += $end->diffInDays($start)-1;
//                             }
//                         }
//                         else{
//                             $countDaysPublished += 0;
//                         }    
//                     }
//                     $unit->days_published_in_month = $count - $countDaysPublished;
//                 }
//                 else{
//                     $unit->days_published_in_month = 0;
//                 }        
//             return $unit;
//         });
 
//         $multiUnits = collect([]);
//         $propertyList = $units->concat($multiUnits)->sortByDesc('id')->values();

//         // Pagination
//         $perPage = 100;
//         $currentPage = request()->get('page', 1);
//         $pagedData = $propertyList->forPage($currentPage, $perPage);

//         $items = new LengthAwarePaginator(
//             $pagedData,
//             $propertyList->count(),
//             $perPage,
//             $currentPage,
//             ['path' => request()->url(), 'query' => request()->query()]
//         );

//         return view('pms.report.property-billing', compact('items', 'selectedMonth'));
//     }



public function PropertyBillingReportList(Request $request)
{
    
    $selectedMonth = $request->month_drop_down
        ? Carbon::parse($request->month_drop_down)->startOfMonth()
        : Carbon::now()->startOfMonth();

    $monthStart = $selectedMonth->copy()->startOfMonth();
    $monthEnd   = $selectedMonth->copy()->endOfMonth();
    $today      = Carbon::now()->startOfDay(); // Today at 00:00

    $properties = TblHomeUnit::select('id', 'ru_property_id', 'is_published_date', 'unit_name', 'ru_status')
        ->whereNotNull('ru_property_id')->orderBy('id', 'DESC')
        ->get();

    $items = [];

    foreach ($properties as $property) {
        $publishedDate = $property->is_published_date 
            ? Carbon::parse($property->is_published_date)->startOfDay() 
            : null;

        
        if (!$publishedDate || $publishedDate->greaterThan($monthEnd)) {
            $property->days_published_in_month = 0;
            $items[] = $property;
            continue;
        }

      
        $effectiveStart = $publishedDate->greaterThan($monthStart) ? $publishedDate : $monthStart;

        
        $previousLog = TblPropertyPublishLog::where('property_id', $property->id)
            ->where('activity_date', '<', $effectiveStart)
            ->orderBy('activity_date', 'desc')
            ->first();

        $lastStatus = $previousLog ? $previousLog->status : 1; 
        $lastChangeDate = $effectiveStart->copy();
        $totalDays = 0;

       
        $logs = TblPropertyPublishLog::where('property_id', $property->id)
            ->where('activity_date', '>=', $effectiveStart)
            ->where('activity_date', '<=', $monthEnd)
            ->orderBy('activity_date')
            ->get();

        
        foreach ($logs as $log) {
            $from = Carbon::parse($lastChangeDate)->startOfDay();
            $to   = Carbon::parse($log->activity_date)->startOfDay();

            if ($lastStatus == 1) {
                $days = $from->diffInDays($to) + 1;
                if ($from->isSameDay($to)) $days = 1;
                $totalDays += $days;
            }

            $lastStatus = $log->status;
            $lastChangeDate = $log->activity_date;
        }

        $finalEnd = ($selectedMonth->isCurrentMonth()) ? $today : $monthEnd;

        if ($lastStatus == 1) {
            $from = Carbon::parse($lastChangeDate)->startOfDay();
            $to   = $finalEnd;

            $days = $from->diffInDays($to) + 1;
            if ($from->isSameDay($to)) $days = 1;
            $totalDays += $days;
        }

        // Cap at possible days
        $possibleDays = $effectiveStart->diffInDays($finalEnd) + 1;
        $totalDays = min($totalDays, $possibleDays);

        $property->days_published_in_month = $totalDays;
        $items[] = $property;
        
    }

    return view('pms.report.property-billing', compact('items', 'selectedMonth'));
}



}