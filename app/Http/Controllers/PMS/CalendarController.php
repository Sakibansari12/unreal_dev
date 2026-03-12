<?php

namespace App\Http\Controllers\PMS;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;
use App\helper\MasterHelper;
use App\Models\TblHomeUnit;
use App\Models\RuPropertyPrice;
use App\Models\PropertyBooking;
use App\Models\RuPropertyAvailability;
use App\Http\Controllers\MinStayController;
use App\Models\RuPropertyMinstay;
use App\Models\TblHomeMultiUnit;
use App\Models\BookingGuestId;
use App\Models\TblHome;
use App\Models\TblUnitMultiunit;
use App\Models\TblGst;
use App\Models\TblLocation;
use App\Models\RuPropertyBlocked;
use Carbon\Carbon;
use ScssPhp\ScssPhp\Compiler;
use App\Services\PropertyService;
use App\Services\PriceLabsPayloadService;
use App\Services\PriceLabService;

use URL;
use Carbon\CarbonPeriod;

class CalendarController extends Controller
{
    protected $propertyService;
    protected $priceLabpayloadService;
    protected $priceLabService;

    public function __construct()
    {
        $this->propertyService = new PropertyService();
        $this->priceLabpayloadService = new PriceLabsPayloadService();
        $this->priceLabService = new PriceLabService();
    }


     public function index(Request $request){
        $req = $request->all();
        $propertyBookings = PropertyBooking::with('property')->where('property_booking_status', '!=', 'Canceled')->where(DB::raw('DATE(created_at)'), '>=', date('Y-m-01'))->get();        
        foreach($propertyBookings as $propertyBookingId) {
            $blocked = RuPropertyBlocked::where('date_from', date('Y-m-d', strtotime($propertyBookingId->checkin_date)))->where('date_to', date('Y-m-d', strtotime($propertyBookingId->checkout_date)))->where('property_id',   $propertyBookingId->property_id)->first();
            if($blocked){
                if($blocked->booking_id == null){
                    $blocked->booking_id = $propertyBookingId->id;
                    $blocked->save();
                }
                
            }
            else{
                if($propertyBookingId->property){
                    $block = New RuPropertyBlocked();
                    $block->property_id = $propertyBookingId->property->id;
                    $block->ru_property_id = $propertyBookingId->property->ru_property_id;
                    $block->date_from = (new DateTime($propertyBookingId->checkin_date))->format('Y-m-d');
                    $block->date_to   = (new DateTime($propertyBookingId->checkout_date))->format('Y-m-d');
                    $block->type = 'unit';
                    $block->reason = "By Booking";
                    $block->save();  
                }
                            
            }
        }
    
        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
        $inputMonth = $request->input('month');
        $month = $curr_month = ($inputMonth && date('m', strtotime($inputMonth)) != date('m')) ? $inputMonth : date('Y-m-d');
        $calendarDateFrom = $month;
        
        $calendarDateTo = date('Y-m-d', strtotime($calendarDateFrom . ' +30 days'));
        $monthNumber = date('m', strtotime($calendarDateFrom));
        $daysInMonth = date('t', strtotime($calendarDateFrom));
        // Generate Month Headers
        $timestamp = mktime(0, 0, 0, $monthNumber, 1);
        $currentMonthName = date('M', $timestamp);
        $nextMonthNumber = $monthNumber + 1 > 12 ? 1 : $monthNumber + 1;
        $nextMonthName = date('M', mktime(0, 0, 0, $nextMonthNumber, 1));
        $months = [$currentMonthName, $nextMonthName];
        $lastDayOfMonth = date('t', strtotime($calendarDateFrom));
        $dj = date('m', strtotime($month)) == date('m') ? date('j') : 0;
        $remainingDays = $lastDayOfMonth - $dj  + 1;
        $colSpanArray = [$remainingDays, $lastDayOfMonth - $remainingDays, 1];
        // Dates
        $datesArray = [];
        $displayDatesArray = [];
        $bgClasses = [];
        for ($date = Carbon::create($calendarDateFrom); $date->lte(Carbon::create($calendarDateTo)); $date->addDay()) {
            $cDate = $date->toDateString();
            $datesArray[] = $cDate;
            $displayDatesArray[] = date('d D', strtotime($cDate));
            $bgClasses[] = '';
        }
        // Units + Prices + Bookings
        $final_array = [];
        $query = TblHomeUnit::query();
        if ($request->has('location') && $request->location != '') {
            $query->where('location_id', $request->location);
        }
        if ($request->has('property') && $request->property != '') {
            $query->where('id', $request->property);
        }
        $query = $this->propertyService->applyUserRoleFilter($query);
        $list  =  $query->where('status', 1)->with('home')->where('is_published', 1)->select(['no_of_rooms', 'id',  'unit_name', 'slug', 'home_type', 'location', 'checkin_time', 'checkout_time', 'min_stay', 'ru_property_id', 'unit_name as home_name'])->get();
        $unitIds = $list->pluck('id')->filter()->values();
        $ruPropertyIds = $list->pluck('ru_property_id')->filter()->unique()->values();
        $calendarDateToPlusOne = date('Y-m-d', strtotime($calendarDateTo . ' +1 day'));
        $pricesPrefetch = RuPropertyPrice::whereIn('ru_property_id', $ruPropertyIds) ->whereBetween('price_date', [$calendarDateFrom, $calendarDateTo])->where('type', 'unit')->groupBy('ru_property_id')->orderBy('price_date', 'asc')->groupBy('price_date')->get();
      
        $pricesByRuPropertyId = $pricesPrefetch->groupBy('ru_property_id');
        $bookingsPrefetch = PropertyBooking::whereIn('property_id', $unitIds)->where('pType', 'unit')->where('property_booking_status', '!=', 'Canceled')->where('checkin_date', '<=', $calendarDateTo)
        ->where('checkout_date', '>=', $calendarDateFrom)->get();
            
        $availabilityPrefetch = RuPropertyAvailability::whereIn('ru_property_id', $ruPropertyIds)->whereBetween('availability_date', [$calendarDateFrom, $calendarDateToPlusOne])->where('type', 'unit')->get();
        $availabilityAnyByKey = [];
        $availabilityNoByKey = [];
        foreach ($availabilityPrefetch as $row) {
            $k = $row->ru_property_id . '|' . $row->availability_date;
            if (!isset($availabilityAnyByKey[$k])) {
                $availabilityAnyByKey[$k] = $row;
            }
            if ($row->is_available === 'no') {
                $availabilityNoByKey[$k][] = $row;
            }
        }
        $blockedGlobalStartsPrefetch = DB::table('ru_property_blocked')->whereBetween('date_from', [$calendarDateFrom, $calendarDateTo]) ->where('type', 'unit')->orderBy('id', 'asc')->get();
        $blockedPrviousOneMonthDateRange = DB::table('ru_property_blocked')->whereBetween('date_from', [date('Y-m-d', strtotime($calendarDateFrom.'-30 days')), $calendarDateTo]) ->where('type', 'unit')->orderBy('id', 'asc')->get();
        $blockedGlobalByDateFrom = [];
        foreach ($blockedGlobalStartsPrefetch as $row) {
            if (!isset($blockedGlobalByDateFrom[$row->date_from])) {
                $blockedGlobalByDateFrom[$row->date_from] = $row;
            }
        }
        $blockedGlobalByDateTo = [];
        foreach ($blockedGlobalStartsPrefetch as $row) {
            if (!isset($blockedGlobalByDateTo[$row->date_to])) {
                $blockedGlobalByDateTo[$row->date_to] = $row;
            }
        }
        $blockedPrefetch = DB::table('ru_property_blocked')->whereIn('ru_property_id', $ruPropertyIds)->where('type', 'unit')
        ->where(function ($q) use ($calendarDateFrom, $calendarDateTo) {
            $q->whereBetween('date_from', [$calendarDateFrom, $calendarDateTo])->orWhereBetween('date_to', [$calendarDateFrom, $calendarDateTo])
            ->orWhere(function ($q) use ($calendarDateFrom, $calendarDateTo) {
                $q->where('date_from', '<', $calendarDateFrom)->where('date_to', '>', $calendarDateTo);
            });
        })->orderBy('id', 'asc')->get();
        $blockedStartsByKey = [];
        $blockedEndsByKey = [];
        $blockedRangesByRuPropertyId = [];
        foreach ($blockedPrefetch as $row) {
            $startKey = $row->ru_property_id . '|' . $row->date_from;
            $endKey = $row->ru_property_id . '|' . $row->date_to;
            if (!isset($blockedStartsByKey[$startKey])) {
                $blockedStartsByKey[$startKey] = $row;
            }
            if (!isset($blockedEndsByKey[$endKey])) {
                $blockedEndsByKey[$endKey] = $row;
            }
            $blockedRangesByRuPropertyId[$row->ru_property_id][] = $row;
        }
        $minStayPrefetch = RuPropertyMinstay::whereIn('ru_property_id', $ruPropertyIds)->whereBetween('minstay_date', [$calendarDateFrom, $calendarDateTo])->get();
        $minStayByKey = [];
        foreach ($minStayPrefetch as $row) {
            $minStayByKey[$row->ru_property_id . '|' . $row->minstay_date] = $row;
        }
        $firstWhere = function ($items, callable $predicate) {
            foreach ($items as $item) {
                if ($predicate($item)) {
                    return $item;
                }
            }
            return null;
        };
        
        foreach ($list as $detail) {
            $priceDates = $pricesByRuPropertyId->get($detail->ru_property_id, collect());
            $priceArray = [];
            foreach ($priceDates as $priceKey => $priceValue) {
                $is_booked = false;
                $checkinDate = NULL;
                $checkoutDate = NULL;
                $totalPrice = NULL;
                $customer_name = NULL;
                $className = 'no';
                $booking_class = NULL;
                $no_of_guest = NULL;
                $reason = NULL;
                $minstay = NULL;
                $channel = NULL;
                $id  = $detail->id;
                $bookingId = NULL;
                $isBlockedStart = false;
                $date_from = NULL;
                $date_to = NULL;
                $minSatyDetail = $minStayByKey[$detail->ru_property_id . '|' . $priceValue->price_date] ?? null;
                $minSaty = 1;
                if ($minSatyDetail) {
                    $minSaty = $minSatyDetail->is_minstay_count;
                }
                if($calendarDateFrom  == $priceValue->price_date){
                    $previousBlockDetail = collect($blockedPrviousOneMonthDateRange)->where('date_from', '<=', $calendarDateFrom)->where('ru_property_id', $detail->ru_property_id)->sortByDesc('date_from')->first();
                   
                    if($previousBlockDetail){
                        $customer_name = $previousBlockDetail->reason;
                        $isBlockedStart = true;
                        $date_from = $previousBlockDetail->date_from;
                        $date_to = $previousBlockDetail->date_to;
                        $booking_class = 'bg-blocked';
                        if($previousBlockDetail->date_to > $calendarDateFrom){
                            $className = "start-date start-date-continuous";
                        }
                        else if($previousBlockDetail->date_to == $calendarDateFrom){
                            $className = "end-date";
                        }
                        if($previousBlockDetail->booking_id){
                            $propertyBooking = $firstWhere($bookingsPrefetch, function ($b) use ($previousBlockDetail) {
                                return $b->id == $previousBlockDetail->booking_id;
                            });
                           
                            if($propertyBooking){
                                if(date('Y-m-d', strtotime($propertyBooking->checkout_date)) == $priceValue->price_date){
                                    $className = "end-date start-date-continuous";
                                }
                                else{
                                    $className = "start-date start-date-continuous";
                                }
                                $booking_class = 'bg-booked';
                                $customer_name = $propertyBooking->customer_name;
                                $isBlockedStart = true;
                                $date_from = $checkinDate =   $propertyBooking->checkin_date;
                                $date_to = $checkoutDate =  $propertyBooking->checkout_date;
                                $no_of_guest = $propertyBooking->no_of_adult;
                                $channel = $propertyBooking->channel;
                                $bookingId = $propertyBooking->id;
                                $is_booked = true;
                                    
                            }
                        }
                    } 
                }
                else{
                    $blockDateRangeKey = $detail->ru_property_id .'|'. $priceValue->price_date;
                    $blockedStartDateRange = $blockedStartsByKey[$blockDateRangeKey] ?? null;
                    $blockedEndDateRange = $blockedEndsByKey[$blockDateRangeKey] ?? null;
                    if($blockedStartDateRange && !$blockedEndDateRange){
                        $booking_class = 'bg-blocked';
                        $className = "start-date";
                        $customer_name = $blockedStartDateRange->reason;
                        $isBlockedStart = true;
                        $date_from = $blockedStartDateRange->date_from;
                        $date_to = $blockedStartDateRange->date_to;
                     
                        if($blockedStartDateRange->booking_id){
                            $propertyBooking = $firstWhere($bookingsPrefetch, function ($b) use ($blockedStartDateRange) {
                                return $b->id == $blockedStartDateRange->booking_id;
                            });
                           
                            if($propertyBooking){
                                $booking_class = 'bg-booked';
                                $className = "start-date";
                                $customer_name = $propertyBooking->customer_name;
                                $isBlockedStart = true;
                                $date_from = $checkinDate =   $propertyBooking->checkin_date;
                                $date_to = $checkoutDate =  $propertyBooking->checkout_date;
                                $no_of_guest = $propertyBooking->no_of_adult;
                                $channel = $propertyBooking->channel;
                                $bookingId = $propertyBooking->id;
                                $is_booked = true;
                            }
                        }
                    }
    
                
                    if($blockedEndDateRange && !$blockedStartDateRange){
                        $booking_class = 'bg-blocked';
                        $className = "end-date";
                        $customer_name = 'By Admin';
                        $isBlockedStart = false;
                        $bookingId = null;
                        $channel = null;
                        $reason = null;
                    }
    
                    if($blockedStartDateRange && $blockedEndDateRange){
                        $className = " end-date start-date";
                        $isBlockedStart = true;
                        $is_booked = false;
                        $booking_class = 'bg-blocked';
                        $customer_name = $blockedStartDateRange->reason;
                        $isBlockedStart = true;
                        $date_from = $blockedStartDateRange->date_from;
                        $date_to = $blockedStartDateRange->date_to;
                        $channel = null;
                        
                        if($blockedStartDateRange->booking_id){
                            $propertyBooking = $firstWhere($bookingsPrefetch, function ($b) use ($blockedStartDateRange) {
                                return $b->id == $blockedStartDateRange->booking_id;
                            });
                           
                            if($propertyBooking){
                                $booking_class = 'bg-booked';
                              
                                $customer_name = $propertyBooking->customer_name;
                                $isBlockedStart = true;
                                $date_from = $checkinDate =   $propertyBooking->checkin_date;
                                $date_to = $checkoutDate =  $propertyBooking->checkout_date;
                                $no_of_guest = $propertyBooking->no_of_adult;
                                $channel = $propertyBooking->channel;
                                $bookingId = $propertyBooking->id;
                                $is_booked = true;
                            }
                        }
                        
                    }
                }
                
                if($priceValue->price_date =='2026-03-03'){
                 //  dd($priceValue->price_date, $is_booked, $checkinDate, $calendarDateTo, $totalPrice, $customer_name, $className, $booking_class, $no_of_guest, $reason, $minSaty, $channel, $id, $bookingId, $isBlockedStart. $date_from, $date_to);
                }
                if ($priceValue->price_date <= $calendarDateTo) {
                    $priceData = array('price_date' => $priceValue->price_date, 'ru_property_id' => $priceValue->ru_property_id, 'is_booked' => $is_booked, 'checkinDate' => $checkinDate, 'checkoutDate' => $checkoutDate, 'customer_name' => $customer_name, 'class_name' => $className, 'price' => $priceValue->price, 'booking_class' => $booking_class, 'totalPrice' => $totalPrice, 'no_of_guest' => $no_of_guest, 'min_stay' => $minSaty, 'is_booking_date_editable' => true, 'bookingId' => $bookingId, 'reason' => $reason, 'date_from' => $date_from, 'date_to' => $date_to, 'channel' => $channel, 'pType' => 'unit');
                    $priceData = (object)$priceData;
                    $priceArray[] = $priceData;
                   
                }
            }
            $detail->prices = $priceArray;
            $final_array[] = $detail;
        }
        
        $locations = TblLocation::where('status', 1)->get(['id', 'location_name']);
        $query = TblHomeUnit::where('status', 1)->select(['id', 'unit_name as home_name', 'slug', 'home_type', 'location', 'checkin_time', 'checkout_time', 'min_stay', 'ru_property_id']);
        $list = $this->propertyService->applyUserRoleFilter($query)->get();
        $user = Auth::guard('admin')->user();
        return view('pms.calendar', compact(
            'month',
            'months',
            'colSpanArray',
            'displayDatesArray',
            'final_array',
            'list',
            'locations',
            'req',
            'user',
        ));
    }



    public function calendarModal(Request $request)
    {
        $req = $request->all();
        $html = view('pms.calendar-modal', compact('req'))->render();
        return response()->json([
            'modalHtml' => $html,
        ]);
    }

    public function calendarModalFormSubmit(Request $request)
    {

        $message = '';
        if ($request['type'] == 'blockProperty') {
            $this->calendarBlockDates($request['formdata']);
            $message = 'Property dates blocked successfully';
        } elseif ($request['type'] == 'priceChange') {
            RuPropertyPrice::where('ru_property_id', $request['formdata']['propertyId'])
                ->whereBetween('price_date', [$request['formdata']['date_from'], $request['formdata']['date_to']])
                ->update(['price' => $request['formdata']['pricePerNight']]);

            $priceXml = "<Push_PutPrices_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <Prices PropertyID='" . $request['formdata']['propertyId'] . "'>
                    <Season DateFrom='" . date('Y-m-d', strtotime($request['formdata']['date_from'])) . "' DateTo='" . date('Y-m-d', strtotime($request['formdata']['date_to'])) . "'>
                        <Price>" . $request['formdata']['pricePerNight'] . "</Price>
                        <Extra>0</Extra>
                    </Season>
                </Prices>
            </Push_PutPrices_RQ>";
            $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml);

            if ($request['formdata']['minNights'] != '') {
                $dates = CarbonPeriod::create($request['formdata']['date_from'], $request['formdata']['date_to']);
                foreach ($dates as $date) {
                    RuPropertyMinstay::updateOrCreate(
                        [
                            'ru_property_id' => $request['formdata']['propertyId'],
                            'minstay_date' => $date->toDateString(),
                        ],
                        [
                            'is_minstay_count' => $request['formdata']['minNights'],
                        ]
                    );
                }

                $minStayXml = "<Push_PutAvbUnits_RQ>
                    <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                    </Authentication>
                    <MuCalendar PropertyID='" . $request['formdata']['propertyId'] . "'>
                    <Date From='" . date('Y-m-d', strtotime($request['formdata']['date_from'])) . "' To='" . date('Y-m-d', strtotime($request['formdata']['date_to'])) . "'>
                        <U>1</U>
                        <MS>" . $request['formdata']['minNights'] . "</MS>
                        <C>4</C>
                    </Date>
                    </MuCalendar>
                </Push_PutAvbUnits_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml);
            }


            $property = TblHomeUnit::where('ru_property_id', $request['formdata']['propertyId'])->first();
            
            
            $payload = $this->priceLabpayloadService->preparePriceLabsCalendarPayloadOnUpdate($property->ru_property_id, $request['formdata']['date_from'], $request['formdata']['date_to'], $request['formdata']['pricePerNight'], $request['formdata']['minNights']);
            $response = $this->priceLabService->syncCalendars($payload);

            $message = 'Price updated successfully';
        }

        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }


    public function calendarBlockDates($formData)
    {
        $property_id = $formData['propertyId'];
        $date_from = $formData['date_from'];
        $date_to = $formData['date_to'];
        $reason = $formData['reason'];
        $pType = $formData['pType'];


        if ($formData['pType'] == 'unit') {
            $property = ($pType == 'unit') ? TblHomeUnit::where('ru_property_id', $property_id)->first() : TblHomeMultiUnit::where('ru_property_id', $property_id)->first();

            $type = 'no';
            $u = 0;

            RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                ->whereBetween('availability_date', [
                    date('Y-m-d', strtotime($date_from)),
                    date('Y-m-d', strtotime($date_to))
                ])
                ->where('type', $pType)
                ->update([
                    'is_available' => $type,
                    'reason' =>  $reason,
                ]);

            DB::table('ru_property_blocked')->insert([
                'ru_property_id' => $property->ru_property_id,
                'property_id' => $property_id,
                'date_from' => $date_from,
                'date_to' => date('Y-m-d', strtotime($date_to)),
                'is_available' => 'no',
                'reason' => $reason,
                'type' => $pType,
            ]);


            $mUnit = DB::table('tbl_unit_multiunits')->where('unit_id', $property->id)->first();

            if ($mUnit) {
                $property = TblHomeMultiUnit::where('id', $mUnit->multiunit_id)->first();
                if ($property) {
                    if ($property->ru_property_id) {
                        $u = 0;
                        $type = 'no';

                        RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                            ->whereBetween('availability_date', [
                                date('Y-m-d', strtotime($date_from)),
                                date('Y-m-d', strtotime($date_to))
                            ])
                            ->where('type', 'multiunit')
                            ->update([
                                'is_available' => $type,
                                'reason' =>  $reason,
                            ]);

                        DB::table('ru_property_blocked')->insert([
                            'ru_property_id' => $property->ru_property_id,
                            'property_id' => $property_id,
                            'date_from' => $date_from,
                            'date_to' => date('Y-m-d', strtotime($date_to)),
                            'is_available' => $type,
                            'reason' => $reason,
                            'type' => 'multiunit',
                        ]);
                    }
                }
            }
        }


        if ($formData['pType'] == 'multiunit') {
            $property = ($pType == 'unit') ? TblHomeMultiUnit::where('ru_property_id', $property_id)->first() : TblHomeMultiUnit::where('ru_property_id', $property_id)->first();

            $type = 'no';
            $u = 0;

            RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                ->whereBetween('availability_date', [
                    date('Y-m-d', strtotime($date_from)),
                    date('Y-m-d', strtotime($date_to))
                ])
                ->where('type', $pType)
                ->update([
                    'is_available' => $type,
                    'reason' =>  $reason,
                ]);

            DB::table('ru_property_blocked')->insert([
                'ru_property_id' => $property->ru_property_id,
                'property_id' => $property_id,
                'date_from' => $date_from,
                'date_to' => date('Y-m-d', strtotime($date_to)),
                'is_available' => 'no',
                'reason' => $reason,
                'type' => $pType,
            ]);
            $mUnits = DB::table('tbl_unit_multiunits')->where('multiunit_id', $property->id)->get();
            if ($mUnits) {
                foreach ($mUnits as $mUnit) {
                    $property = TblHomeUnit::where('id', $mUnit->unit_id)->first();
                    if ($property) {
                        if ($property->ru_property_id) {
                            $u = 0;
                            $type = 'no';
                            RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                                ->whereBetween('availability_date', [
                                    date('Y-m-d', strtotime($date_from)),
                                    date('Y-m-d', strtotime($date_to))
                                ])
                                ->where('type', 'unit')
                                ->update([
                                    'is_available' => $type,
                                    'reason' =>  $reason,
                                ]);

                            DB::table('ru_property_blocked')->insert([
                                'ru_property_id' => $property->ru_property_id,
                                'property_id' => $property_id,
                                'date_from' => $date_from,
                                'date_to' => date('Y-m-d', strtotime($date_to)),
                                'is_available' => $type,
                                'reason' => $reason,
                                'type' => 'unit',
                            ]);
                        }
                    }
                }
            }
        }

        $checkAvaliability = RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
            ->where('availability_date', date('Y-m-d', strtotime($date_to . '+1 days')))->first();


        $date_to_new =  date('Y-m-d', strtotime($date_to . '-1 days'));

        if ($checkAvaliability) {
            if ($checkAvaliability->is_available == 'no') {
                $date_to_new = date('Y-m-d', strtotime($date_to));
            }
        }

        $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <MuCalendar PropertyID='" . $property->ru_property_id . "'>
                    <Date From='" . date('Y-m-d', strtotime($date_from)) . "' To='" . $date_to_new . "'>
                        <U>0</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";

        $xmlResponse = MasterHelper::makeXmlRequest($xml);
        
        if(isPriceLabEnable() && $property->price_lab_sync_date_time){
            $close = 0;
            $res = $this->propertyService->updateAvaliability($property->id, $date_from, $date_to, $close, $pType, $reason);
        }
        return true;
    }

    public function calendarUnblockDates(Request $request)
    {
        $property_id = $request->propertyId;
        $date_from = $request->blockedFrom;
        $date_to = $request->blockedTo;
        $pType = $request->pType;

        if ($request->pType == 'unit') {
            $property = ($pType == 'unit') ? TblHomeUnit::where('ru_property_id', $property_id)->first() : TblHomeMultiUnit::where('ru_property_id', $property_id)->first();
            $type = 'yes';
            $u = 1;
            $checkinCheck = DB::table('ru_property_blocked')
                ->where('ru_property_id', $property->ru_property_id)
                ->where('date_to', $date_from)
                ->first();

            $checkOutCheck = DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->where('date_from', $date_to)->first();

            if ($checkinCheck && !$checkOutCheck) {
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                    ->whereBetween('availability_date', [
                        date('Y-m-d', strtotime($date_from . '+1 days')),
                        date('Y-m-d', strtotime($date_to))
                    ])
                    ->where('type', 'unit')
                    ->update([
                        'is_available' => $type,
                        'reason' =>  null,
                    ]);
            } else if ($checkOutCheck && !$checkinCheck) {
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                    ->whereBetween('availability_date', [
                        date('Y-m-d', strtotime($date_from)),
                        date('Y-m-d', strtotime($date_to . '-1 days'))
                    ])
                    ->where('type', 'unit')
                    ->update([
                        'is_available' => $type,
                        'reason' =>  null,
                    ]);
            } else if ($checkOutCheck && $checkinCheck) {
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                    ->whereBetween('availability_date', [
                        date('Y-m-d', strtotime($date_from . '+1 days')),
                        date('Y-m-d', strtotime($date_to . '-1 days'))
                    ])
                    ->where('type', 'unit')
                    ->update([
                        'is_available' => $type,
                        'reason' =>  null,
                    ]);
            } else {
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                    ->whereBetween('availability_date', [
                        date('Y-m-d', strtotime($date_from)),
                        date('Y-m-d', strtotime($date_to))
                    ])
                    ->where('type', 'unit')
                    ->update([
                        'is_available' => $type,
                        'reason' =>  null,
                    ]);
            }

            DB::table('ru_property_blocked')->where('date_from', $date_from)->where('ru_property_id', $property->ru_property_id)->delete();
            $munitid = TblUnitMultiunit::where('unit_id', $property->id)->get()->pluck('multiunit_id')->toArray();
            $mruids =  TblHomeMultiUnit::whereIn('id', $munitid)->whereNotNull('ru_property_id')->get();


            foreach ($mruids as $val) {
                if ($val->ru_property_id) {
                    RuPropertyAvailability::where('ru_property_id', $val->ru_property_id)
                        ->whereBetween('availability_date', [
                            date('Y-m-d', strtotime($date_from)),
                            date('Y-m-d', strtotime($date_to))
                        ])
                        ->where('type', 'multiunit')
                        ->update([
                            'is_available' => 'yes',
                            'reason' =>  null,
                        ]);
                    DB::table('ru_property_blocked')->where('date_from', $date_from)->where('ru_property_id', $val->ru_property_id)->where('type', 'multiunit')->delete();
                }
            }
        }

        if ($request->pType == 'multiunit') {
            $property = ($pType == 'unit') ? TblHomeUnit::where('ru_property_id', $property_id)->first() : TblHomeMultiUnit::where('ru_property_id', $property_id)->first();
            $type = 'yes';
            $u = 1;
            $checkinCheck = DB::table('ru_property_blocked')
                ->where('ru_property_id', $property->ru_property_id)
                ->where('date_to', $date_from)
                ->first();

            $checkOutCheck = DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->where('date_from', $date_to)->first();

            if ($checkinCheck && !$checkOutCheck) {
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                    ->whereBetween('availability_date', [
                        date('Y-m-d', strtotime($date_from . '+1 days')),
                        date('Y-m-d', strtotime($date_to))
                    ])
                    ->where('type', $pType)
                    ->update([
                        'is_available' => $type,
                        'reason' =>  null,
                    ]);
            } else if ($checkOutCheck && !$checkinCheck) {
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                    ->whereBetween('availability_date', [
                        date('Y-m-d', strtotime($date_from)),
                        date('Y-m-d', strtotime($date_to . '-1 days'))
                    ])
                    ->where('type', $pType)
                    ->update([
                        'is_available' => $type,
                        'reason' =>  null,
                    ]);
            } else if ($checkOutCheck && $checkinCheck) {
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                    ->whereBetween('availability_date', [
                        date('Y-m-d', strtotime($date_from . '+1 days')),
                        date('Y-m-d', strtotime($date_to . '-1 days'))
                    ])
                    ->where('type', $pType)
                    ->update([
                        'is_available' => $type,
                        'reason' =>  null,
                    ]);
            } else {
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                    ->whereBetween('availability_date', [
                        date('Y-m-d', strtotime($date_from)),
                        date('Y-m-d', strtotime($date_to))
                    ])
                    ->where('type', $pType)
                    ->update([
                        'is_available' => $type,
                        'reason' =>  null,
                    ]);
            }

            DB::table('ru_property_blocked')->where('date_from', $date_from)->where('ru_property_id', $property->ru_property_id)->delete();
            $munitid = TblUnitMultiunit::where('multiunit_id', $property->id)->get()->pluck('unit_id')->toArray();
            $mruids =  TblHomeUnit::whereIn('id', $munitid)->whereNotNull('ru_property_id')->get();


            foreach ($mruids as $val) {
                if ($val->ru_property_id) {
                    RuPropertyAvailability::where('ru_property_id', $val->ru_property_id)
                        ->whereBetween('availability_date', [
                            date('Y-m-d', strtotime($date_from)),
                            date('Y-m-d', strtotime($date_to))
                        ])
                        ->where('type', 'unit')
                        ->update([
                            'is_available' => 'yes',
                            'reason' =>  null,
                        ]);
                    DB::table('ru_property_blocked')->where('date_from', $date_from)->where('ru_property_id', $val->ru_property_id)->where('type', 'unit')->delete();
                }
            }
        }

        $checkAvaliability = RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
            ->where('availability_date', date('Y-m-d', strtotime($date_to . '+1 days')))->first();
        $date_to_new =  date('Y-m-d', strtotime($date_to));

        if ($checkAvaliability) {
            if ($checkAvaliability->is_available == 'no') {
                $date_to_new = date('Y-m-d', strtotime($date_to . '-1 days'));
            }
        }
        $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <MuCalendar PropertyID='" . $property->ru_property_id . "'>
                    <Date From='" . date('Y-m-d', strtotime($date_from)) . "' To='" . date('Y-m-d', strtotime($date_to_new)) . "'>
                        <U>1</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";
        $xmlResponse = MasterHelper::makeXmlRequest($xml);


        if(isPriceLabEnable() && $property->price_lab_sync_date_time){
            $close = 1;
            $reason =null;
            $res = $this->propertyService->updateAvaliability($property->id, $date_from, $date_to, $close, $pType, $reason);
       }

        return response()->json([
            'status' => true,
            'data' => '',
            'message' => 'Property dates unblocked successfully.'
        ], 200);
    }


    public function calendarBookingForm(Request $request)
    {
        $req = $request->all();
        $no_adults = 1;
        $no_of_guests = $request->no_adults;
        $id = $request->propertyId;
        // $splitdate = explode(' - ',$request->check_in_date);
        $checkindate = $request->date_from;
        $checkoutdate = $request->date_to;
        $last_date =  date('Y-m-d', strtotime($checkoutdate . '-1 days'));
        $checkin_date =  $checkindate;
        if ($last_date == $checkindate) {
            $date_difference_count = 1;
        }
        else {
            $date_difference_count = MasterHelper::getDateDifference($checkindate, $checkoutdate);
        }

        $propertyId = $request->propertyId;
        $query = ($request->pType == 'unit') ? TblHomeUnit::query() : TblHomeMultiUnit::query();
        $query->when($id, function ($q) use ($id) {
            return $q->where('ru_property_id', $id);
        });
        if($request->has('bookingId') && $request->bookingId){
            $booking = PropertyBooking::where('id', $request->bookingId)->first();
            $propertyId = $booking->property_id;
            $query =  $query->where('id', $booking->property_id);
        }
        // $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
        //     return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        // });
        $detail = $query->with('additionalCharge')->where('maximum_number_of_guests', '>=', $no_of_guests)->whereNotNull('ru_property_id')->first();
    
        $filtered_property_list = array();
        if (!empty($detail)) {
            $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkindate)->where('availability_date', '<=', $checkoutdate)->where('is_available', 'no')->count();
            $price = 0;
            $count = 0;
            if ($count == 0) {
                $price = $initial_price = 0;
                $price = RuPropertyPrice::where('property_id', $detail->id)->whereBetween('price_date', [$checkindate, $last_date])->sum('price');
                if ($price > 0) {
                    $gst_amount = 0;
                    $gstPrecentage = 0;
                    $getAppliedGst  = getAppliedGst($price);
                    if ($getAppliedGst) {
                        $precentageAmount = ($price * $getAppliedGst->gst_percentage) / 100;
                        $gst_amount = $precentageAmount;
                        $gstPrecentage = $getAppliedGst->gst_percentage;
                    }
                    $per_night_price = $price / $date_difference_count;
                    
                  

                    if (setting()->website_markup) {
                        $per_night_price = $per_night_price +  ($per_night_price * setting()->website_markup) / 100;
                    }
                    $price = $per_night_price * $date_difference_count;
                    $detail->per_night_price = $per_night_price;
                    $detail->price = $price;
                    $detail->initial_price = $price;
                    $detail->gst_amount = $gst_amount;
                    $detail->gst_percentage = $gstPrecentage;
                    $detail->home_name = $detail->unit_name;
                    $extra_no_of_guest = 0;
                    $extra_guest_charge = 0;
                    if ($no_of_guests > $detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests) {

                        if ($detail->maximum_number_of_guests == $no_of_guests) {
                            $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                        } else {
                            $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                        }
                        $extra_guest_charge = $extra_no_of_guest * $detail->extra_guest_charges;
                        if ($request->tax_inclusive == 1) {
                            $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                            if ($getAppliedGst) {
                                $precentageExtraGuestChargeAmount = ($extra_guest_charge * $getAppliedGst->gst_percentage) / 100;
                                $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                            }
                        }
                    }
                    $detail->extra_no_of_guest = $extra_no_of_guest;
                    $detail->final_extra_guest_charge = $extra_guest_charge;
                }
            }
        }
        $gst_slab = TblGst::get()->toArray();

        $customerDetail = array();

        $bookingId = $request->has('bookingId') ? $request->bookingId : '';
        if ($request->has('bookingId')) {
            $booking = PropertyBooking::where('id', $request->bookingId)->first();
            $customerDetail =  json_decode($booking->customer_detail, true);
        }
 

        $html = view('pms.calendar-booking-form', [
            'properties' => $detail,
            'no_of_nights' => $date_difference_count,
            'is_gst_allowed' => setting()->is_allow_gst,
            'gst_slab' => $gst_slab,
            'price' => $price,
            'req' => $req,
            'customerDetail' => $customerDetail,
            'bookingId' => $bookingId
        ])->render();
        return response()->json([
            'bookingFromHtml' => $html,
        ]);
    }


    public function cancelBooking(Request $request, $id=null){
        PropertyBooking::where('id', $id)->update(['property_booking_status'=>'Canceled']);
       
        $bookingDetail = PropertyBooking::where('id', $id)->first();
        $property  = TblHomeUnit::where('id', $bookingDetail->property_id)->first();
        $checkinDate = date('Y-m-d', strtotime($bookingDetail->checkin_date));
        $checkoutDate = date('Y-m-d', strtotime($bookingDetail->checkout_date));
        $checkBookingCount = PropertyBooking::where('id', '!=', $bookingDetail->id)->where('checkin_date', $checkinDate)->where('checkout_date', $checkoutDate)->where('property_booking_status', 'Confirmed')->count();
        unblockPropertyAvailabilityInRu($property->ru_property_id, date('Y-m-d', strtotime($bookingDetail->checkin_date)), date('Y-m-d', strtotime($bookingDetail->checkout_date)));
        
        if ($checkBookingCount == 0) {
            if ($bookingDetail->booking_id) {
                $xmlRequest = "<Push_CancelReservation_RQ>
                        <Authentication>
                            <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                            <Password>" . config('ru.RU_PASSWORD') . "</Password>
                        </Authentication>
                        <ReservationID>" . $bookingDetail->booking_id . "</ReservationID>
                        <CancelTypeID>1</CancelTypeID>
                    </Push_CancelReservation_RQ>";
                $ruResponse = MasterHelper::makeXmlRequest($xmlRequest);
            }
            unblock($property->ru_property_id, $bookingDetail->checkin_date, $bookingDetail->checkout_date);
        }

        $propertyBooking  = PropertyBooking::with('property')->where('id', $id)->first();
        if($propertyBooking){
            $request->merge(
                [
                    'blockedFrom' => date('Y-m-d', strtotime($propertyBooking->checkin_date)),
                    'blockedTo' => date('Y-m-d', strtotime($propertyBooking->checkout_date)),
                    'propertyId' => $propertyBooking->property->ru_property_id,
                    
                ]
            );
            
            $res = $this->calendarUnblockDatesUsingBooking($request);
            $payload = $this->priceLabpayloadService->preparePriceLabsReservationCancellationPayload($propertyBooking);
            $this->priceLabService->syncReservations($payload);
        }    
        $message = 'Booking canceled successfully';
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }


    public function calendarModalBookingEdit(Request $request){
        $req = $request->all();
        
        $bookingDetail = PropertyBooking::with('property')->where('id', $request->bookingId)->first();
        $html = view('pms.calendar-modal-booking-edit', compact('req', 'bookingDetail'))->render();
        return response()->json([
            'modalHtml' => $html,
        ]);
    }



    // public function calendarAjaxGetBookingPrice(Request $request){
    //     $req = $request->all();
    //     $no_adults = 1;
    //     $no_of_guests = $request->no_adults;
    //     $id = $request->propertyId;
    //     // $splitdate = explode(' - ',$request->check_in_date);
    //     $checkindate = $request->check_in_date;
    //     $checkoutdate = $request->check_out_date;
    //     $last_date =  date('Y-m-d', strtotime($checkoutdate . '-1 days'));
    //     $checkin_date =  $checkindate;
    //     if ($last_date == $checkindate) {
    //         $date_difference_count = 1;
    //     } else {
    //         $date_difference_count = MasterHelper::getDateDifference($checkindate, $checkoutdate);
    //     }
    //     $location_id = $request->location_id;
    //     $propertyId = $request->propertyId;
    //     $query = TblHomeUnit::query();
    //     $query->when($location_id != '', function ($q) use ($location_id) {
    //         return $q->where('location_id', $location_id);
    //     });
    //     $query->when($id, function ($q) use ($id) {
    //         return $q->where('id', $id);
    //     });
    //     $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
    //         return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
    //     });
    //     $detail = $query->with('additionalCharge')->where('maximum_number_of_guests', '>=', $no_of_guests)->whereNotNull('ru_property_id')->first();

    //     if (!$detail) {
    //         $query = TblHomeMultiUnit::query();
    //         $query->when($location_id != '', function ($q) use ($location_id) {
    //             return $q->where('location_id', $location_id);
    //         });
    //         $query->when($id, function ($q) use ($id) {
    //             return $q->where('id', $id);
    //         });
    //         $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
    //             return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
    //         });
    //         $detail = $query->with('additionalCharge')->where('maximum_number_of_guests', '>=', $no_of_guests)->whereNotNull('ru_property_id')->first();
    //     }

    //     $filtered_property_list = array();
    //     if (!empty($detail)) {
    //         $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkindate)->where('availability_date', '<=', $checkoutdate)->where('is_available', 'no')->count();
    //         $price = 0;
    //         $count = 0;
    //         if ($count == 0) {
    //             $price = $initial_price = 0;
    //             $price = RuPropertyPrice::where('property_id', $detail->id)->whereBetween('price_date', [$checkindate, $last_date])->sum('price');
    //             if ($price > 0) {
    //                 $gst_amount = 0;
    //                 $gstPrecentage = 0;
    //                 $getAppliedGst  = getAppliedGst($price);
    //                 if ($getAppliedGst) {
    //                     $precentageAmount = ($price * $getAppliedGst->gst_percentage) / 100;
    //                     $gst_amount = $precentageAmount;
    //                     $gstPrecentage = $getAppliedGst->gst_percentage;
    //                 }
    //                 $per_night_price = $price / $date_difference_count;

    //                 if (setting()->website_markup) {
    //                     $per_night_price = $per_night_price +  ($per_night_price * setting()->website_markup) / 100;
    //                 }
    //                 $price = $per_night_price * $date_difference_count;
    //                 $detail->per_night_price = $per_night_price;
    //                 $detail->price = $price;
    //                 $detail->initial_price = $price;
    //                 $detail->gst_amount = $gst_amount;
    //                 $detail->gst_percentage = $gstPrecentage;
    //                 $detail->home_name = $detail->unit_name;
    //                 $extra_no_of_guest = 0;
    //                 $extra_guest_charge = 0;
    //                 if ($no_of_guests > $detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests) {

    //                     if ($detail->maximum_number_of_guests == $no_of_guests) {
    //                         $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
    //                     } else {
    //                         $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
    //                     }
    //                     $extra_guest_charge = $extra_no_of_guest * $detail->extra_guest_charges;
    //                     if ($request->tax_inclusive == 1) {
    //                         $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
    //                         if ($getAppliedGst) {
    //                             $precentageExtraGuestChargeAmount = ($extra_guest_charge * $getAppliedGst->gst_percentage) / 100;
    //                             $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
    //                         }
    //                     }
    //                 }
    //                 $detail->extra_no_of_guest = $extra_no_of_guest;
    //                 $detail->final_extra_guest_charge = $extra_guest_charge;
    //             }
    //         }
    //     }
    //     $gst_slab = TblGst::get()->toArray();

    //     $customerDetail = array();

    //     $bookingId = $request->has('bookingId') ? $request->bookingId : '';
    //     if ($request->has('bookingId')) {
    //         $booking = PropertyBooking::where('id', $request->bookingId)->first();
    //         $customerDetail =  json_decode($booking->customer_detail, true);
    //     }


    //     $html = view('pms.ajax-get-booking-price', [
    //         'properties' => $detail,
    //         'no_of_nights' => $date_difference_count,
    //         'is_gst_allowed' => setting()->is_allow_gst,
    //         'gst_slab' => $gst_slab,
    //         'price' => $price,
    //         'req' => $req,
    //         'customerDetail' => $customerDetail,
    //         'bookingId' => $bookingId
    //     ])->render();
    //     return response()->json([
    //         'bookingFromHtml' => $html,
    //     ]);
    // }
    
    
    public function calendarAjaxGetBookingPrice(Request $request){
        
        $request->merge([
            'date_from' => $request->input('check_in_date'),
            'date_to'   => $request->input('check_out_date'),
        ]);

        $req = $request->all();
        $no_adults = 1;
        $no_of_guests = $request->no_adults;
        $id = $request->propertyId;
        // $splitdate = explode(' - ',$request->check_in_date);
        $checkindate = $request->date_from;
        $checkoutdate = $request->date_to;
        $last_date =  date('Y-m-d', strtotime($checkoutdate . '-1 days'));
        $checkin_date =  $checkindate;
        if ($last_date == $checkindate) {
            $date_difference_count = 1;
        }
        else {
            $date_difference_count = MasterHelper::getDateDifference($checkindate, $checkoutdate);
        }

        $propertyId = $request->propertyId;
        $query = ($request->pType=='unit')?TblHomeUnit::query():TblHomeMultiUnit::query();
        $query->when($id, function ($q) use ($id) {
            return $q->where('id', $id);
        });
        // $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
        //     return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        // });
        $detail = $query->with('additionalCharge')->where('maximum_number_of_guests', '>=', $no_of_guests)->whereNotNull('ru_property_id')->first();
       
        $filtered_property_list = array();
        if (!empty($detail)) {
            $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkindate)->where('availability_date', '<=', $checkoutdate)->where('is_available', 'no')->count();
            $price = 0;
            $count = 0;
            if ($count == 0) {
                $price = $initial_price = 0;
                $price = RuPropertyPrice::where('property_id', $detail->id)->whereBetween('price_date', [$checkindate, $last_date])->sum('price');
                if ($price > 0) {
                    $gst_amount = 0;
                    $gstPrecentage = 0;
                    $getAppliedGst  = getAppliedGst($price);
                    if ($getAppliedGst) {
                        $precentageAmount = ($price * $getAppliedGst->gst_percentage) / 100;
                        $gst_amount = $precentageAmount;
                        $gstPrecentage = $getAppliedGst->gst_percentage;
                    }
                    $per_night_price = $price / $date_difference_count;

                    if (setting()->website_markup) {
                        $per_night_price = $per_night_price +  ($per_night_price * setting()->website_markup) / 100;
                    }
                    $price = $per_night_price * $date_difference_count;
                    $detail->per_night_price = $per_night_price;
                    $detail->price = $price;
                    $detail->initial_price = $price;
                    $detail->gst_amount = $gst_amount;
                    $detail->gst_percentage = $gstPrecentage;
                    $detail->home_name = $detail->unit_name;
                    $extra_no_of_guest = 0;
                    $extra_guest_charge = 0;
                    if ($no_of_guests > $detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests) {

                        if ($detail->maximum_number_of_guests == $no_of_guests) {
                            $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                        } else {
                            $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                        }
                        $extra_guest_charge = $extra_no_of_guest * $detail->extra_guest_charges;
                        if ($request->tax_inclusive == 1) {
                            $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                            if ($getAppliedGst) {
                                $precentageExtraGuestChargeAmount = ($extra_guest_charge * $getAppliedGst->gst_percentage) / 100;
                                $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                            }
                        }
                    }
                    $detail->extra_no_of_guest = $extra_no_of_guest;
                    $detail->final_extra_guest_charge = $extra_guest_charge;
                }
            }
        }
        $gst_slab = TblGst::get()->toArray();

        $customerDetail = array();
        $booking = '';
        $bookingId = $request->has('bookingId')?$request->bookingId:'';
        if($request->has('bookingId')){
           $booking = PropertyBooking::where('id', $request->bookingId)->first();
           $customerDetail =  json_decode($booking->customer_detail, true);
        }
        $html = view('pms.ajax-get-booking-price', [
            'properties' => $detail,
            'no_of_nights' => $date_difference_count,
            'is_gst_allowed' => setting()->is_allow_gst,
            'gst_slab' => $gst_slab,
            'price' =>$price,
            'req' =>$req,
            'customerDetail'=>$customerDetail,
            'bookingId'=>$bookingId,
            'bookingDetail'=>$booking,
        ])->render();
        return response()->json([
            'bookingFromHtml' => $html,
        ]);
    }



    public function setCurrency()
    {
        $xml = "<Push_ChangeCurrency_RQ>
            <Authentication>
                <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                <Password>" . config('ru.RU_PASSWORD') . "</Password>
            </Authentication>
            <Location>5677</Location>
            <Currency>INR</Currency>
        </Push_ChangeCurrency_RQ>";
        $xmlResponse = MasterHelper::makeXmlRequest($xml);
        $locationList  = DB::table('tbl_ru_location')->where('status', 1)->get();
        foreach ($locationList as $val) {
            $xml = "<Push_ChangeCurrency_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <Location>" . $val->ru_location_id . "</Location>
                <Currency>INR</Currency>
            </Push_ChangeCurrency_RQ>";
            $xmlResponse = MasterHelper::makeXmlRequest($xml);
        }
    }
    
    
    public function savePropertyBooking(Request $request){
      
        $validator = Validator::make($request->all(), [
            'checkInDate' => 'required',
            'checkOutDate' => 'required',
            'email_address' => 'required',
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'propertyId' => 'required',
            'locationId' => 'required',
        ]);
        if($validator->fails()){
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->first()
            ], 500);
        }
        try{
            
            if($request->id){
                $propertyBooking = PropertyBooking::find($request->id);
            }
            
            /* if($request->has('bookingId') && $request->bookingId !=''){
                $propertyBooking  = PropertyBooking::with('property')->where('id', $request->bookingId)->first();
                $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
                unblockPropertyAvailabilityInRu($property->ru_property_id, date('Y-m-d', strtotime($propertyBooking->checkin_date)), date('Y-m-d', strtotime($propertyBooking->checkout_date)));
                unblock($property->ru_property_id, $propertyBooking->checkin_date, $propertyBooking->checkout_date);
                $propertyId = $propertyBooking->property_id;
                $home = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
            }
            else{
                $propertyBooking = new PropertyBooking();
                $propertyId = $request->propertyId;
                $home = array();
                if($request->pType == 'unit'){
                    $home  = TblHomeUnit::where('id', $request->propertyId)->first();
                }
                else{
                    $home  = TblHomeMultiUnit::where('id', $request->propertyId)->first();
                }
            }
 */
            $home = array();
            if($request->pType == 'unit'){
                $home  = TblHomeUnit::where('id', $request->propertyId)->first();
            }
            else{
                $home  = TblHomeMultiUnit::where('id', $request->propertyId)->first();
            }
           
            if($request->has('bookingId') && $request->bookingId !=''){
                $propertyId = $request->propertyId;
                $propertyBooking  = PropertyBooking::with('property')->where('id', $request->bookingId)->first();
                
               
                $request->merge(
                    [
                        'blockedFrom' => date('Y-m-d', strtotime($propertyBooking->checkin_date)),
                        'blockedTo' => date('Y-m-d', strtotime($propertyBooking->checkout_date)),
                        'propertyId' => $propertyBooking->property->ru_property_id,
                    ]
                );
                $res = $this->calendarUnblockDatesUsingBooking($request);
                $payload = $this->priceLabpayloadService->preparePriceLabsReservationCancellationPayload($propertyBooking);
                $response = $this->priceLabService->syncReservations($payload);
                $request->propertyId = $propertyId;
                
                
            }
            else{
                 $propertyBooking = new PropertyBooking();
            }
             //dd($propertyBooking);



            $propertyBooking->location_id = $home->location_id;
            $propertyBooking->property_id = $request->propertyId;
            $propertyBooking->property_name = $home->unit_name;
            $propertyBooking->total_amount = round($request->netPayableAmount - $request->taxAmount);
            $propertyBooking->discount_amount = round($request->discount_amount);
            $propertyBooking->payable_amount = round($request->netPayableAmount);
            $propertyBooking->additional_charges = $request->additional_charges?json_encode($request->additional_charges):NULL;
            $propertyBooking->tax_amount = $request->taxAmount;
            $propertyBooking->booking_id = rand(10000000, 99999999);
            $propertyBooking->booking_status = 'pending';
            $propertyBooking->booking_created_by = 'admin';
            $propertyBooking->type = ($request->has('type'))?$request->type:'Property';
            $propertyBooking->no_of_children = $request->no_children?$request->no_children:0;
            $propertyBooking->no_of_adult = $request->no_adult?$request->no_adult:1;
            $propertyBooking->customer_detail = json_encode(array('first_name'=>$request->first_name, 'last_name'=>$request->last_name, 'email'=>$request->email_address , 'mobile_number'=>$request->mobile_number, 'country_code'=>$request->country_code));
            $propertyBooking->checkin_date = $request->checkInDate;
            $propertyBooking->checkout_date = $request->checkOutDate;
            if(isset($request->dont_block)){
                $propertyBooking->is_blocking_hour = $request->dont_block;
            }

            $propertyBooking->per_night_price = $request->per_night_price;
            $propertyBooking->no_of_nights = $request->noOfNights;
            $propertyBooking->tax = $request->tax;
            $propertyBooking->additional_charges_detail = $request->additional_charges?json_encode($request->additional_charges):NULL;
            $propertyBooking->additional_charges_discount = $request->add_ons_discount_amount;
            $propertyBooking->tot_additional_charge = $request->tot_additional_charge_amount;
            $propertyBooking->base_price = $request->base_price;
            $propertyBooking->extra_guest_charge = $request->extra_guest_charge==0?NULL:$request->extra_guest_charge;
            $propertyBooking->taxable_amount = $request->totalTaxableAmount;
            $propertyBooking->booking_notes = $request->booking_note;
            $propertyBooking->customer_name = $request->first_name.' '.$request->last_name;
            $propertyBooking->channel = 'Offline';
            $propertyBooking->pType =$request->pType;


            $propertyBooking->user_id  = $home->user_id;
            $propertyBooking->parent_user_id  = $home->parent_user_id;
            $propertyBooking->owner_id  = $home->owner_id ?? null;
            $traveluser = Auth::guard('admin')->user();
            if(!empty($traveluser) && $traveluser->role == 'Travel Agent'){
                $propertyBooking->travelagent_id  = $traveluser->id;
                $propertyBooking->channel = 'Travel Agent';
            }
            $propertyBooking->save();




            $booking = $propertyBooking;
            $propertyBooking = $propertyBooking->load('property');
            $homeDetail = $propertyBooking->property;

            $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <MuCalendar PropertyID='" . $homeDetail->ru_property_id . "'>
                    <Date From='" . date('Y-m-d', strtotime($request->checkInDate)) . "' To='" . $request->checkOutDate . "'>
                        <U>0</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";

            $xmlResponse = MasterHelper::makeXmlRequest($xml);
            
            if($homeDetail->price_lab_sync_date_time){
                if($propertyBooking->property_booking_status =='Confirmed'){
                    $payload = $this->priceLabpayloadService->preparePriceLabsSyncConfirmedReservation($booking);
                    
                }
                else{
                    $payload = $this->priceLabpayloadService->preparePriceLabsSyncUnconfirmedReservation($booking);
                }
                $response = $this->priceLabService->syncReservations($payload);
                
                $res = $this->propertyService->updateAvaliabilityWithReservation($homeDetail->id, date('Y-m-d', strtotime($booking->checkin_date)), date('Y-m-d', strtotime($booking->checkout_date)), 0, $homeDetail->pType,  'Update booking');
                
            }






            $propertyBookingRecent = PropertyBooking::where('id', $propertyBooking->id)->first();

            $property  = TblHomeUnit::where('id', $propertyBookingRecent->property_id)->first(['tbl_home_units.*', 'tbl_home_units.unit_name as home_name']);
            if(!$property){
                $property  = TblHomeMultiUnit::where('id', $propertyBookingRecent->property_id)->first(['tbl_home_multi_units.*', 'tbl_home_multi_units.unit_name as home_name']);
            }
            $propertyBookingRecent->property = $property;
            // $email =  Mail::to($request->email_address)->send(new BookingPropertyHoldEmail(array('mailData'=>$propertyBooking, 'type'=>'customer')));
         
            $guestDataBase = new BookingGuestId();
            $guestDataBase->name = $request->first_name.' '.$request->last_name;
            $guestDataBase->email = $request->email_address;
            $guestDataBase->property_booking_id = $propertyBooking->id;
            $guestDataBase->property_name  = $home->unit_name;
            $guestDataBase->user_id = $propertyBooking->user_id;
            $guestDataBase->country_code = $request->country_code;
            $guestDataBase->mobile_no = $request->mobile_number;
            $guestDataBase->save();

            DB::table('tbl_leads')->insert(['name'=>$request->first_name.' '.$request->last_name,'date'=> date('Y-m-d'), 'email'=>$request->email_address, 'mobile'=>$request->mobile_number,'booking_id'=>$propertyBooking->booking_id,'stage'=>'Booked']);

            $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
            if(!$property){
                $property  = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first();
            }

            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Booking Created Successfully.'
            ], 200);
        }
        catch(Exception $e){
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function calendarUnblockDatesUsingBooking($request){
        $property_id = $request->propertyId;
        $date_from = $request->blockedFrom;
        $date_to = $request->blockedTo;
        
        $actual_date_from = $request->blockedFrom;
        $actual_date_to = $request->blockedTo;
        
        $type = 'unit';
        
        $pType = 'unit';
        
        $reason = '';
        if($pType =='unit'){
            $property = ( $pType == 'unit')?TblHomeUnit::where('ru_property_id', $property_id)->first():TblHomeMultiUnit::where('ru_property_id', $property_id)->first();
            $checkinCheck = RuPropertyBlocked::where('ru_property_id', $property->ru_property_id)->where('date_to', $date_from)->exists();
            $checkOutCheck = RuPropertyBlocked::where('ru_property_id', $property->ru_property_id)->where('date_from', $date_to)->exists();
           
            if ($checkinCheck && !$checkOutCheck) {
                $date_from = date('Y-m-d', strtotime($date_from . ' +1 day'));
            }
            elseif ($checkOutCheck && !$checkinCheck) {
                $date_to = date('Y-m-d', strtotime($date_to . ' -1 day'));
            }
            elseif ($checkOutCheck && $checkinCheck) {
                $date_from = date('Y-m-d', strtotime($date_from . ' +1 day'));
                $date_to   = date('Y-m-d', strtotime($date_to . ' -1 day'));
            }
            else {
                $date_from = $date_from;
                $date_to = $date_to;
            }
            
        
            RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
            ->whereBetween('availability_date', [
                date('Y-m-d', strtotime($date_from)),
                date('Y-m-d', strtotime($date_to))
            ])->where('type', 'unit')->update([
                'is_available' => 'yes',
                'reason' =>  null,
            ]);

            $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <MuCalendar PropertyID='" . $property->ru_property_id . "'>
                    <Date From='" . date('Y-m-d', strtotime($date_from)) . "' To='" . $date_to . "'>
                        <U>1</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";

            $xmlResponse = MasterHelper::makeXmlRequest($xml);
            DB::table('ru_property_blocked')->where('property_id', $property->id)->where('type', $pType)->where('date_from', $actual_date_from)->where('date_to', $actual_date_to)->delete();
            $close = 1;
            $res = $this->propertyService->updateAvaliabilityOnBookingCancelOrDeleteFromCalendar($property->id, $actual_date_from, $actual_date_to, $close, $pType,  $reason);
        }
        
       
        return true;
        return response()->json([
            'status'=>true,
            'data'=>'',
            'message' => 'Property dates unblocked successfully.'
        ], 200);
    }
}