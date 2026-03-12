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
use App\Models\TblHome;
use App\Models\TblUnitMultiunit;
use App\Models\TblGst;
use App\Models\TblLocation;
use Carbon\Carbon;
use ScssPhp\ScssPhp\Compiler;
use App\Services\PropertyService;
use URL;
use Carbon\CarbonPeriod;

class CalendarController extends Controller{
    protected $propertyService;

    public function __construct(){
        $this->propertyService = new PropertyService();
    }

    
    public function index(Request $request){
      
        $req = $request->all();


        DB::statement("SET SESSION sql_mode=(SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
        $inputMonth = $request->input('month');
        $month = $curr_month = ($inputMonth && date('m', strtotime($inputMonth)) != date('m'))
            ? $inputMonth
            : date('Y-m-d');

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
        
     
        if(date('m', strtotime($month)) == date('m')){
            $dj = date('j');
        }
        else{
           $dj = 0; 
        }
        
      
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
        if($request->has('location') && $request->location !=''){
            $query->where('location_id', $request->location);
        }
        if($request->has('property') && $request->property !=''){
            $query->where('id', $request->property);
        }
        $query = $this->propertyService->applyUserRoleFilter($query);
        $list  =  $query->where('status', 1)->where('ru_status', 1)->where('is_published', 1)->select(['id', 'unit_name as home_name', 'slug', 'home_type', 'location', 'checkin_time', 'checkout_time', 'min_stay', 'ru_property_id'])->get();


        foreach ($list as $detail) {
            $priceDates = RuPropertyPrice::where('ru_property_id', $detail->ru_property_id)
                ->whereBetween('price_date', [$calendarDateFrom, $calendarDateTo])
                ->where('type', 'unit')
                ->groupBy('price_date')
                ->get();

            $priceArray = [];
            foreach($priceDates as $priceKey=>$priceValue){
                $is_booked = false;
                $checkinDate = NULL;
                $checkoutDate = NULL;
                $totalPrice = NULL;
                $customer_name = NULL;
                $className = 'no';
                $booking_class= NULL;
                $no_of_guest= NULL;
                $reason= NULL;
                $minstay= NULL;
                $channel = NULL;
                $id  = $detail->id;

                if($detail->ru_building_id){
                    $propertyBooking = PropertyBooking::where('ru_building_id', $detail->ru_building_id)->where('pType', 'unit')->where('room_no', $detail->room_position)->where('checkin_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('property_id', $id)->where('property_booking_status', '!=', 'Canceled')->first();
                    $propertyCBooking = PropertyBooking::where('ru_building_id', $detail->ru_building_id)->where('pType', 'unit')->where('room_no', $detail->room_position)->where('checkout_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('property_id', $id)->where('pType', 'unit')->where('property_booking_status', '!=', 'Canceled')->first();
                }
                else{
                    $propertyBooking = PropertyBooking::where('checkin_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('property_id', $id)->where('property_booking_status', '!=', 'Canceled')->where('pType', 'unit')->first();
                    $propertyCBooking = PropertyBooking::where('checkout_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('pType', 'unit')->where('property_id', $id)->where('property_booking_status', '!=', 'Canceled')->first();
                }

                $propertyBookingConfirmed = PropertyBooking::where('checkin_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('property_id', $id)->where('property_booking_status', 'Confirmed')->where('pType', 'unit')->first();

                if($propertyBookingConfirmed){
                   $propertyBooking =  $propertyBookingConfirmed;
                }
                $ruPropertyAvailability = RuPropertyAvailability::where('ru_property_id', $detail->ru_property_id)->where('availability_date', $priceValue->price_date)->where('is_available', 'no')->where('type', 'unit')->get();
                $nextDate = date('Y-m-d', strtotime($priceValue->price_date . ' +1 day'));

                $avaliabilityDetail = RuPropertyAvailability::where('ru_property_id', $detail->ru_property_id)->where('availability_date', $priceValue->price_date)->where('type', 'unit')->first();
                $blockedDateRange = DB::table('ru_property_blocked')->where('date_from', $priceValue->price_date)->where('type', 'unit')->first();

                $ruPropertyAvailabilityNext = RuPropertyAvailability::where('ru_property_id', $detail->ru_property_id)->where('availability_date', $nextDate)->where('is_available', 'no')->where('type', 'unit')->get();


                $blockedStartDateRange =  DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_from',  $priceValue->price_date)->where('type', 'unit')->first();
                $blockedEndDateRange =  DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_to', $priceValue->price_date)->where('type', 'unit')->first();
                $blockedDates =  DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_from', '<', $priceValue->price_date)->where('date_to', '>', $priceValue->price_date)->where('type', 'unit')->first();

                $propertyBookingNext = PropertyBooking::where('checkin_date', $priceValue->price_date)->where('property_booking_status', '!=', 'Canceled')->where('pType', 'unit')->where('property_id', $detail->id)->first();

                $propertyBookingConfirmed = PropertyBooking::where('checkin_date', $priceValue->price_date)->where('property_id', $id)->where('pType', 'unit')->where('property_booking_status', 'Confirmed')->first();


                if($propertyBookingConfirmed){
                   $propertyBookingNext =  $propertyBookingConfirmed;
                }

                if($blockedStartDateRange && !$blockedEndDateRange){
                    $booking_class = 'bg-blocked';
                    $className = "start-date";
                    $customer_name = $blockedStartDateRange->reason;
                    $isBlockedStart = true;
                    $date_from = $blockedDateRange->date_from;
                    $date_to = $blockedDateRange->date_to;
                }
 
                if($blockedStartDateRange && $blockedEndDateRange){
                    $booking_class = 'bg-blocked';
                    $className = "start-date end-date";
                    $customer_name = $blockedStartDateRange->reason;
                    $isBlockedStart = true;
                }

                if(!$blockedStartDateRange && $blockedEndDateRange){
                   $booking_class = 'bg-blocked';
                    $className = "end-date";

                    $customer_name = $blockedEndDateRange->reason;
                    $isBlockedStart = true;
                }

                if($blockedDateRange){
                    $date_from = $blockedDateRange->date_from;
                    $date_to = $blockedDateRange->date_to;
                }
                else{
                    $date_from = null;
                    $date_to = null;
                }


                if((!empty($avaliabilityDetail))){
                    $reason = $avaliabilityDetail->reason;
                }

                if($ruPropertyAvailability->count() > 0 && $ruPropertyAvailabilityNext->count() == 0){
                    $booking_class = 'bg-blocked';
                    $className = "end-date";
                    $customer_name = 'By Admin';
                    $isBlockedStart = false;
                }

                $bookingId = NULL;
                if($propertyBooking){
                    $is_booked = true;
                    $channel = $propertyBooking->channel;
                    if(!$propertyBookingNext){
                        $bookingId = $propertyBooking->id;
                        $totalPrice = $propertyBooking->payable_amount;
                        $no_of_guest = $propertyBooking->no_of_adult;
                        $checkinDate = date('Y-m-d', strtotime($propertyBooking->checkin_date));
                        $checkoutDate = date('Y-m-d', strtotime($propertyBooking->checkout_date));
                        $customer_name = $propertyBooking->customer_name;
                    }
                    else{
                        $bookingId = $propertyBookingNext->id;
                        $totalPrice = $propertyBookingNext->payable_amount;
                        $no_of_guest = $propertyBooking->no_of_adult;
                        $checkinDate = date('Y-m-d', strtotime($propertyBookingNext->checkin_date));
                        $checkoutDate = date('Y-m-d', strtotime($propertyBookingNext->checkout_date));
                        $customer_name = $propertyBookingNext->customer_name;
                    }
                    $booking_class = 'bg-booked';
                    if($priceValue->price_date == $checkinDate && !$blockedEndDateRange){
                        $className = 'start-date';
                    }
                    if($priceValue->price_date == $checkinDate && $blockedEndDateRange){
                        $className = 'end-date start-date';
                    }
                    if($priceValue->price_date == $checkinDate && $propertyCBooking){
                        $className = 'start-date end-date';
                    }
                    if($priceValue->price_date > $checkinDate && $priceValue->price_date < $checkoutDate){
                        $className = 'disabled';
                    }

                    if($priceValue->price_date == $checkoutDate && !$blockedStartDateRange){
                         $className = 'end-date';
                    }
                    if($priceValue->price_date == $checkoutDate && $blockedStartDateRange){
                        $booking_class = 'bg-blocked';
                        $className = 'end-date start-date';
                        $checkinDate = '';
                        $checkoutDate = '';
                        $customer_name = $blockedStartDateRange->reason;
                        $bookingId = '';
                        $is_booked = false;
                    }
                    if($checkinDate < date('Y-m-t')){
                        if($priceValue->price_date == date('Y-m-t') && $checkoutDate < $calendarDateTo){
                           $className = 'start-date end-date-continuous';
                        }
                        if($priceValue->price_date == date('Y-m-t') && $checkoutDate < $calendarDateTo &&  date('Y-m-t') == $checkinDate){
                            $className = 'start-date';
                        }
                    }
                    if($checkoutDate > date('Y-m-t')){
                        if($checkoutDate == date('Y-m-d', strtotime(date('Y-m-t') . ' +1 day')) && $checkoutDate < $calendarDateTo){
                            $className = 'start-date-continuous-end';
                        }
                        // if($priceValue->price_date == date('Y-m-d', strtotime(date('Y-m-t') . ' +1 day'))){
                        //    $className = 'start-date start-date-continuous';
                        // }
                    }
                    if($checkinDate < date('Y-m-01')){
                        if($priceValue->price_date == date('Y-m-d') && $checkoutDate < $calendarDateTo){
                            $className = 'start-date start-date-continuous';
                        }
                        if($checkoutDate == date('Y-m-01')){
                            $className = 'start-date-continuous-end';
                        }

                    }
                    if($calendarDateTo < $checkoutDate){
                        if($calendarDateTo == $priceValue->price_date){
                            $className = 'end-date end-date-continuous';
                        }
                    }
                }
                $minSatyDetail = RuPropertyMinstay::where('minstay_date', $priceValue->price_date)->where('ru_property_id', $detail->ru_property_id)->first();
                $minSaty = 1;
                if($minSatyDetail){
                  $minSaty = $minSatyDetail->is_minstay_count;
                }
                $startDateLessThan =  DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_from', '<', $priceValue->price_date)->where('type', 'unit')->orderBy('id', 'desc')->first();
                if( $priceValue->price_date==date('Y-m-d') && $startDateLessThan){
                    if($startDateLessThan->date_from < $priceValue->price_date && $priceValue->price_date<  $startDateLessThan->date_to ){
                        $className = 'start-date start-date-continuous';
                        $date_from = $startDateLessThan->date_from;
                        $customer_name = $startDateLessThan->reason;
                        $date_to = $startDateLessThan->date_to;
                        $booking_class = 'bg-blocked';
                    }
                }
                if($priceValue->price_date <=$calendarDateTo){
                    $priceData = array('price_date'=>$priceValue->price_date, 'ru_property_id'=>$priceValue->ru_property_id, 'is_booked'=>$is_booked, 'checkinDate'=>$checkinDate , 'checkoutDate'=>$checkoutDate, 'customer_name'=>$customer_name, 'class_name'=>$className, 'price'=>$priceValue->price, 'booking_class'=>$booking_class, 'totalPrice'=>$totalPrice, 'no_of_guest'=>$no_of_guest, 'min_stay'=>$minSaty, 'is_booking_date_editable'=>true, 'bookingId'=>$bookingId, 'reason'=>$reason,'date_from'=>$date_from,'date_to'=>$date_to, 'channel'=>$channel, 'pType'=>'unit');
                    $priceData = (object)$priceData;
                    array_push($priceArray, $priceData);
                    $checkout_date = $checkoutDate;
                }
            }
            $detail->prices = $priceArray;
            $final_array[] = $detail;
        }


        //------------------multiunit----------------------------//

        $query = TblHomeMultiUnit::query();
        if($request->has('location') && $request->location !=''){
            $query->where('location_id', $request->location);
        }
        if($request->has('property') && $request->property !=''){
            $query->where('id', $request->property);
        }
        $query = $this->propertyService->applyUserRoleFilter($query);
        // $list  =  $query->where('status', 1)->where('is_published', 1)->select(['id', 'unit_name as home_name', 'slug', 'home_type', 'location', 'checkin_time', 'checkout_time', 'min_stay', 'ru_property_id'])->get();

        $list  =  $query->where('status', 1)->where('ru_status', 1)->where('is_published', 1)->select(['id', 'unit_name as home_name', 'slug', 'home_type', 'location', 'checkin_time', 'checkout_time', 'min_stay', 'ru_property_id'])->get();



        foreach ($list as $detail) {
            $priceDates = RuPropertyPrice::where('ru_property_id', $detail->ru_property_id)
                ->whereBetween('price_date', [$calendarDateFrom, $calendarDateTo])
                ->where('type', 'multiunit')
                ->groupBy('price_date')
                ->get();

            $priceArray = [];
            foreach($priceDates as $priceKey=>$priceValue){

                $is_booked = false;
                $checkinDate = NULL;
                $checkoutDate = NULL;
                $totalPrice = NULL;
                $customer_name = NULL;
                $className = 'no';
                $booking_class= NULL;
                $no_of_guest= NULL;
                $reason= NULL;
                $minstay= NULL;
                $channel = NULL;
                $id  = $detail->id;

                if($detail->ru_building_id){
                    $propertyBooking = PropertyBooking::where('ru_building_id', $detail->ru_building_id)->where('room_no', $detail->room_position)->where('checkin_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('property_id', $id)->where('property_booking_status', '!=', 'Canceled')->where('pType', 'multiunit')->first();
                    $propertyCBooking = PropertyBooking::where('ru_building_id', $detail->ru_building_id)->where('room_no', $detail->room_position)->where('checkout_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('property_id', $id)->where('property_booking_status', '!=', 'Canceled')->where('pType', 'multiunit')->first();
                }
                else{
                    $propertyBooking = PropertyBooking::where('checkin_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('property_id', $id)->where('pType', 'multiunit')->where('property_booking_status', '!=', 'Canceled')->first();
                    $propertyCBooking = PropertyBooking::where('checkout_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('pType', 'multiunit')->where('property_id', $id)->where('property_booking_status', '!=', 'Canceled')->first();
                }

                $propertyBookingConfirmed = PropertyBooking::where('checkin_date', '<=', $priceValue->price_date)->where('checkout_date', '>=', $priceValue->price_date)->where('property_id', $id)->where('pType', 'multiunit')->where('property_booking_status', 'Confirmed')->first();

                if($propertyBookingConfirmed){
                   $propertyBooking =  $propertyBookingConfirmed;
                }
                $ruPropertyAvailability = RuPropertyAvailability::where('ru_property_id', $detail->ru_property_id)->where('availability_date', $priceValue->price_date)->where('is_available', 'no')->where('type', 'multiunit')->get();
                $nextDate = date('Y-m-d', strtotime($priceValue->price_date . ' +1 day'));

                $avaliabilityDetail = RuPropertyAvailability::where('ru_property_id', $detail->ru_property_id)->where('availability_date', $priceValue->price_date)->where('type', 'multiunit')->first();
                $blockedDateRange = DB::table('ru_property_blocked')->where('date_from', $priceValue->price_date)->where('type', 'multiunit')->first();

                $ruPropertyAvailabilityNext = RuPropertyAvailability::where('ru_property_id', $detail->ru_property_id)->where('availability_date', $nextDate)->where('is_available', 'no')->where('type', 'multiunit')->get();


                $blockedStartDateRange =  DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_from',  $priceValue->price_date)->where('type', 'multiunit')->first();
                $blockedEndDateRange =  DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_to', $priceValue->price_date)->where('type', 'multiunit')->first();
                $blockedDates =  DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_from', '<', $priceValue->price_date)->where('date_to', '>', $priceValue->price_date)->where('type', 'multiunit')->first();

                $propertyBookingNext = PropertyBooking::where('checkin_date', $priceValue->price_date)->where('type', 'multiunit')->where('property_booking_status', '!=', 'Canceled')->where('type', 'multiunit')->where('property_id', $detail->id)->first();

                $propertyBookingConfirmed = PropertyBooking::where('checkin_date', $priceValue->price_date)->where('property_id', $id)->where('property_booking_status', 'Confirmed')->where('type', 'multiunit')->first();


                if($propertyBookingConfirmed){
                   $propertyBookingNext =  $propertyBookingConfirmed;
                }

                if($blockedStartDateRange && !$blockedEndDateRange){
                    $booking_class = 'bg-blocked';
                    $className = "start-date";
                    $customer_name = $blockedStartDateRange->reason;
                    $isBlockedStart = true;
                    $date_from = $blockedDateRange->date_from;
                    $date_to = $blockedDateRange->date_to;
                }

                if($blockedStartDateRange && $blockedEndDateRange){
                    $booking_class = 'bg-blocked';
                    $className = "start-date end-date";
                    $customer_name = $blockedStartDateRange->reason;
                    $isBlockedStart = true;
                }

                if(!$blockedStartDateRange && $blockedEndDateRange){
                   $booking_class = 'bg-blocked';
                    $className = "end-date";

                    $customer_name = $blockedEndDateRange->reason;
                    $isBlockedStart = true;
                }

                if($blockedDateRange){
                    $date_from = $blockedDateRange->date_from;
                    $date_to = $blockedDateRange->date_to;
                }
                else{
                    $date_from = null;
                    $date_to = null;
                }


                if((!empty($avaliabilityDetail))){
                    $reason = $avaliabilityDetail->reason;
                }

                if($ruPropertyAvailability->count() > 0 && $ruPropertyAvailabilityNext->count() == 0){
                    $booking_class = 'bg-blocked';
                    $className = "end-date";
                    $customer_name = 'By Admin';
                    $isBlockedStart = false;
                }

                $bookingId = NULL;
                if($propertyBooking){
                    $is_booked = true;
                    $channel = $propertyBooking->channel;
                    if(!$propertyBookingNext){
                        $bookingId = $propertyBooking->id;
                        $totalPrice = $propertyBooking->payable_amount;
                        $no_of_guest = $propertyBooking->no_of_adult;
                        $checkinDate = date('Y-m-d', strtotime($propertyBooking->checkin_date));
                        $checkoutDate = date('Y-m-d', strtotime($propertyBooking->checkout_date));
                        $customer_name = $propertyBooking->customer_name;
                    }
                    else{
                        $bookingId = $propertyBookingNext->id;
                        $totalPrice = $propertyBookingNext->payable_amount;
                        $no_of_guest = $propertyBooking->no_of_adult;
                        $checkinDate = date('Y-m-d', strtotime($propertyBookingNext->checkin_date));
                        $checkoutDate = date('Y-m-d', strtotime($propertyBookingNext->checkout_date));
                        $customer_name = $propertyBookingNext->customer_name;
                    }
                    $booking_class = 'bg-booked';
                    if($priceValue->price_date == $checkinDate && !$blockedEndDateRange){
                        $className = 'start-date';
                    }
                    if($priceValue->price_date == $checkinDate && $blockedEndDateRange){
                        $className = 'end-date start-date';
                    }
                    if($priceValue->price_date == $checkinDate && $propertyCBooking){
                        $className = 'start-date end-date';
                    }
                    if($priceValue->price_date > $checkinDate && $priceValue->price_date < $checkoutDate){
                        $className = 'disabled';
                    }

                    if($priceValue->price_date == $checkoutDate && !$blockedStartDateRange){
                         $className = 'end-date';
                    }
                    if($priceValue->price_date == $checkoutDate && $blockedStartDateRange){
                        $booking_class = 'bg-blocked';
                        $className = 'end-date start-date';
                        $checkinDate = '';
                        $checkoutDate = '';
                        $customer_name = $blockedStartDateRange->reason;
                        $bookingId = '';
                        $is_booked = false;
                    }
                    if($checkinDate < date('Y-m-t')){
                        if($priceValue->price_date == date('Y-m-t') && $checkoutDate < $calendarDateTo){
                           $className = 'start-date end-date-continuous';
                        }
                        if($priceValue->price_date == date('Y-m-t') && $checkoutDate < $calendarDateTo &&  date('Y-m-t') == $checkinDate){
                            $className = 'start-date';
                        }
                    }
                    if($checkoutDate > date('Y-m-t')){
                        if($checkoutDate == date('Y-m-d', strtotime(date('Y-m-t') . ' +1 day')) && $checkoutDate < $calendarDateTo){
                            $className = 'start-date-continuous-end';
                        }
                        // if($priceValue->price_date == date('Y-m-d', strtotime(date('Y-m-t') . ' +1 day'))){
                        //    $className = 'start-date start-date-continuous';
                        // }
                    }
                    if($checkinDate < date('Y-m-01')){
                        if($priceValue->price_date == date('Y-m-d') && $checkoutDate < $calendarDateTo){
                            $className = 'start-date start-date-continuous';
                        }
                        if($checkoutDate == date('Y-m-01')){
                            $className = 'start-date-continuous-end';
                        }

                    }
                    if($calendarDateTo < $checkoutDate){
                        if($calendarDateTo == $priceValue->price_date){
                            $className = 'end-date end-date-continuous';
                        }
                    }
                }
                $minSatyDetail = RuPropertyMinstay::where('minstay_date', $priceValue->price_date)->where('type', 'multiunit')->where('ru_property_id', $detail->ru_property_id)->first();
                $minSaty = 1;
                if($minSatyDetail){
                  $minSaty = $minSatyDetail->is_minstay_count;
                }
                $startDateLessThan =  DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_from', '<', $priceValue->price_date)->where('type', 'multiunit')->orderBy('id', 'desc')->first();
                if( $priceValue->price_date==date('Y-m-d') && $startDateLessThan){
                    if($startDateLessThan->date_from < $priceValue->price_date && $priceValue->price_date<  $startDateLessThan->date_to ){
                        $className = 'start-date start-date-continuous';
                        $date_from = $startDateLessThan->date_from;
                        $customer_name = $startDateLessThan->reason;
                        $date_to = $startDateLessThan->date_to;
                        $booking_class = 'bg-blocked';
                    }
                }
                if($priceValue->price_date <=$calendarDateTo){
                    $priceData = array('price_date'=>$priceValue->price_date, 'ru_property_id'=>$priceValue->ru_property_id, 'is_booked'=>$is_booked, 'checkinDate'=>$checkinDate , 'checkoutDate'=>$checkoutDate, 'customer_name'=>$customer_name, 'class_name'=>$className, 'price'=>$priceValue->price, 'booking_class'=>$booking_class, 'totalPrice'=>$totalPrice, 'no_of_guest'=>$no_of_guest, 'min_stay'=>$minSaty, 'is_booking_date_editable'=>true, 'bookingId'=>$bookingId, 'reason'=>$reason,'date_from'=>$date_from,'date_to'=>$date_to, 'channel'=>$channel, 'pType'=>'multiunit');
                    $priceData = (object)$priceData;
                    array_push($priceArray, $priceData);
                    $checkout_date = $checkoutDate;
                }
            }
            $detail->prices = $priceArray;
            $final_array[] = $detail;
        }

        $locations = TblLocation::where('status', 1)->get(['id', 'location_name']);

       // $list = TblHomeUnit::where('status', 1)->select(['id', 'unit_name as home_name', 'slug', 'home_type', 'location', 'checkin_time', 'checkout_time', 'min_stay', 'ru_property_id'])->get();

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


    public function calendarModal(Request $request){
        $req = $request->all();
        $html = view('pms.calendar-modal', compact('req'))->render();
        return response()->json([
            'modalHtml' => $html,
        ]);
    }

    public function calendarModalFormSubmit(Request $request){
        
        $message = ''; 
        if($request['type'] =='blockProperty'){
            $this->calendarBlockDates($request['formdata']);
            $message = 'Property dates blocked successfully';
        }
        elseif($request['type'] =='priceChange'){
            RuPropertyPrice::where('ru_property_id', $request['formdata']['propertyId'])
            ->whereBetween('price_date', [$request['formdata']['date_from'], $request['formdata']['date_to']])
            ->update(['price'=>$request['formdata']['pricePerNight']]);
            
            $priceXml = "<Push_PutPrices_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <Prices PropertyID='" . $request['formdata']['propertyId'] . "'>
                    <Season DateFrom='" . date('Y-m-d', strtotime($request['formdata']['date_from'])) . "' DateTo='" . date('Y-m-d', strtotime($request['formdata']['date_to'])) . "'>
                        <Price>" . $request['formdata']['pricePerNight']. "</Price>
                        <Extra>0</Extra>
                    </Season>
                </Prices>
            </Push_PutPrices_RQ>";
            $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml);

            if($request['formdata']['minNights'] !=''){
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
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <MuCalendar PropertyID='".$request['formdata']['propertyId']."'>
                    <Date From='".date('Y-m-d', strtotime($request['formdata']['date_from']))."' To='".date('Y-m-d', strtotime($request['formdata']['date_to']))."'>
                        <U>1</U>
                        <MS>".$request['formdata']['pricePerNight']."</MS>
                        <C>4</C>
                    </Date>
                    </MuCalendar>
                </Push_PutAvbUnits_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml);
            }
            $message = 'Price updated successfully';
        }

        return response()->json([
            'status' => true,
            'message' =>$message
        ]);
    }


    public function calendarBlockDates($formData){
        $property_id = $formData['propertyId'];
        $date_from = $formData['date_from'];
        $date_to = $formData['date_to'];
        $reason = $formData['reason'];
        $pType = $formData['pType'];


        if($formData['pType'] =='unit'){
            $property = ( $pType == 'unit')?TblHomeUnit::where('ru_property_id', $property_id)->first():TblHomeMultiUnit::where('ru_property_id', $property_id)->first();

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

            if($mUnit ){
                $property = TblHomeMultiUnit::where('id', $mUnit->multiunit_id)->first();
                if($property){
                    if($property->ru_property_id){
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


        if($formData['pType'] =='multiunit'){
            $property = ( $pType == 'unit')?TblHomeMultiUnit::where('ru_property_id', $property_id)->first():TblHomeMultiUnit::where('ru_property_id', $property_id)->first();

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
            if($mUnits ){
                foreach($mUnits as $mUnit){
                    $property = TblHomeUnit::where('id', $mUnit->unit_id)->first();
                    if($property){
                        if($property->ru_property_id){
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
                        ->where('availability_date', date('Y-m-d', strtotime($date_to.'+1 days')))->first();
                        
    
        $date_to_new =  date('Y-m-d', strtotime($date_to.'-1 days'));                   
                        
        if($checkAvaliability){
            if($checkAvaliability->is_available == 'no'){
                $date_to_new = date('Y-m-d', strtotime($date_to));
            }
        }                
        
        $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                </Authentication>
                <MuCalendar PropertyID='".$property->ru_property_id."'>
                    <Date From='".date('Y-m-d', strtotime($date_from))."' To='".$date_to_new."'>
                        <U>0</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";
            
            
        
       
            
      
        $xmlResponse = MasterHelper::makeXmlRequest($xml);
        
        return true;
    }

    public function calendarUnblockDates(Request $request){
        $property_id = $request->propertyId;
        $date_from = $request->blockedFrom;
        $date_to = $request->blockedTo;
        $pType = $request->pType;

        if($request->pType =='unit'){
            $property = ( $pType == 'unit')?TblHomeUnit::where('ru_property_id', $property_id)->first():TblHomeMultiUnit::where('ru_property_id', $property_id)->first();
            $type = 'yes';
            $u = 1;
            $checkinCheck = DB::table('ru_property_blocked')
            ->where('ru_property_id', $property->ru_property_id)
            ->where('date_to', $date_from)
            ->first();

            $checkOutCheck = DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->where('date_from', $date_to)->first();

            if($checkinCheck && !$checkOutCheck){
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                ->whereBetween('availability_date', [
                    date('Y-m-d', strtotime($date_from.'+1 days')),
                    date('Y-m-d', strtotime($date_to))
                ])
                ->where('type', 'unit')
                ->update([
                    'is_available' => $type,
                    'reason' =>  null,
                ]);
            }
            else if($checkOutCheck && !$checkinCheck){
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                ->whereBetween('availability_date', [
                    date('Y-m-d', strtotime($date_from)),
                    date('Y-m-d', strtotime($date_to.'-1 days'))
                ])
                ->where('type', 'unit')
                ->update([
                    'is_available' => $type,
                    'reason' =>  null,
                ]);
            }
            else if($checkOutCheck && $checkinCheck){
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                ->whereBetween('availability_date', [
                    date('Y-m-d', strtotime($date_from.'+1 days')),
                    date('Y-m-d', strtotime($date_to.'-1 days'))
                ])
                ->where('type', 'unit')
                ->update([
                    'is_available' => $type,
                    'reason' =>  null,
                ]);
            }
            else{
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
            $munitid= TblUnitMultiunit::where('unit_id', $property->id)->get()->pluck('multiunit_id')->toArray();
            $mruids =  TblHomeMultiUnit::whereIn('id', $munitid)->whereNotNull('ru_property_id')->get();


            foreach($mruids as $val){
                if($val->ru_property_id){
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

        if($request->pType =='multiunit'){
            $property = ( $pType == 'unit')?TblHomeUnit::where('ru_property_id', $property_id)->first():TblHomeMultiUnit::where('ru_property_id', $property_id)->first();
            $type = 'yes';
            $u = 1;
            $checkinCheck = DB::table('ru_property_blocked')
            ->where('ru_property_id', $property->ru_property_id)
            ->where('date_to', $date_from)
            ->first();

            $checkOutCheck = DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->where('date_from', $date_to)->first();

            if($checkinCheck && !$checkOutCheck){
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                ->whereBetween('availability_date', [
                    date('Y-m-d', strtotime($date_from.'+1 days')),
                    date('Y-m-d', strtotime($date_to))
                ])
                ->where('type',$pType)
                ->update([
                    'is_available' => $type,
                    'reason' =>  null,
                ]);
            }
            else if($checkOutCheck && !$checkinCheck){
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                ->whereBetween('availability_date', [
                    date('Y-m-d', strtotime($date_from)),
                    date('Y-m-d', strtotime($date_to.'-1 days'))
                ])
                ->where('type', $pType)
                ->update([
                    'is_available' => $type,
                    'reason' =>  null,
                ]);
            }
            else if($checkOutCheck && $checkinCheck){
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                ->whereBetween('availability_date', [
                    date('Y-m-d', strtotime($date_from.'+1 days')),
                    date('Y-m-d', strtotime($date_to.'-1 days'))
                ])
                ->where('type', $pType)
                ->update([
                    'is_available' => $type,
                    'reason' =>  null,
                ]);
            }
            else{
                RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)
                ->whereBetween('availability_date', [
                    date('Y-m-d', strtotime($date_from)),
                    date('Y-m-d', strtotime($date_to))
                ])
                ->where('type',$pType)
                ->update([
                    'is_available' => $type,
                    'reason' =>  null,
                ]);
            }

            DB::table('ru_property_blocked')->where('date_from', $date_from)->where('ru_property_id', $property->ru_property_id)->delete();
            $munitid= TblUnitMultiunit::where('multiunit_id', $property->id)->get()->pluck('unit_id')->toArray();
            $mruids =  TblHomeUnit::whereIn('id', $munitid)->whereNotNull('ru_property_id')->get();


            foreach($mruids as $val){
                if($val->ru_property_id){
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
                        ->where('availability_date', date('Y-m-d', strtotime($date_to.'+1 days')))->first();
        $date_to_new =  date('Y-m-d', strtotime($date_to));                   
                        
        if($checkAvaliability){
            if($checkAvaliability->is_available == 'no'){
                $date_to_new = date('Y-m-d', strtotime($date_to.'-1 days'));
            }
        }  
        $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                </Authentication>
                <MuCalendar PropertyID='".$property->ru_property_id."'>
                    <Date From='".date('Y-m-d', strtotime($date_from))."' To='".date('Y-m-d', strtotime($date_to_new))."'>
                        <U>1</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";
        $xmlResponse = MasterHelper::makeXmlRequest($xml);
        return response()->json([
            'status'=>true,
            'data'=>'',
            'message' => 'Property dates unblocked successfully.'
        ], 200);
    }


    public function calendarBookingForm(Request $request){
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
        } else {
            $date_difference_count = MasterHelper::getDateDifference($checkindate, $checkoutdate);
        }

        $propertyId = $request->propertyId;


        $query = ($request->pType=='unit')?TblHomeUnit::query():TblHomeMultiUnit::query();
        $query->when($id, function ($q) use ($id) {
            return $q->where('ru_property_id', $id);
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

        $bookingId = $request->has('bookingId')?$request->bookingId:'';
        if($request->has('bookingId')){
           $booking = PropertyBooking::where('id', $request->bookingId)->first();
           $customerDetail =  json_decode($booking->customer_detail, true);
        }


        $html = view('pms.calendar-booking-form', [
            'properties' => $detail,
            'no_of_nights' => $date_difference_count,
            'is_gst_allowed' => setting()->is_allow_gst,
            'gst_slab' => $gst_slab,
            'price' =>$price,
            'req' =>$req,
            'customerDetail'=>$customerDetail,
            'bookingId'=>$bookingId
        ])->render();
        return response()->json([
            'bookingFromHtml' => $html,
        ]);
    }


    public function cancelBooking($id=null){
        PropertyBooking::where('id', $id)->delete();
        $message = 'Booking canceled successfully';
        return response()->json([
            'status' => true,
            'message' =>$message
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



    public function calendarAjaxGetBookingPrice(Request $request){
        $req = $request->all();
        $no_adults = 1;
        $no_of_guests = $request->no_adults;
        $id = $request->propertyId;
        // $splitdate = explode(' - ',$request->check_in_date);
        $checkindate = $request->check_in_date;
        $checkoutdate = $request->check_out_date;
        $last_date =  date('Y-m-d', strtotime($checkoutdate . '-1 days'));
        $checkin_date =  $checkindate;
        if ($last_date == $checkindate) {
            $date_difference_count = 1;
        } else {
            $date_difference_count = MasterHelper::getDateDifference($checkindate, $checkoutdate);
        }
        $location_id = $request->location_id;
        $propertyId = $request->propertyId;
        $query = TblHomeUnit::query();
        $query->when($location_id != '', function ($q) use ($location_id) {
            return $q->where('location_id', $location_id);
        });
        $query->when($id, function ($q) use ($id) {
            return $q->where('id', $id);
        });
        $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
            return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        });
        $detail = $query->with('additionalCharge')->where('maximum_number_of_guests', '>=', $no_of_guests)->whereNotNull('ru_property_id')->first();

        if(!$detail){
            $query = TblHomeMultiUnit::query();
            $query->when($location_id != '', function ($q) use ($location_id) {
                return $q->where('location_id', $location_id);
            });
            $query->when($id, function ($q) use ($id) {
                return $q->where('id', $id);
            });
            $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
                return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
            });
            $detail = $query->with('additionalCharge')->where('maximum_number_of_guests', '>=', $no_of_guests)->whereNotNull('ru_property_id')->first();
        }

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
            'bookingId'=>$bookingId
        ])->render();
        return response()->json([
            'bookingFromHtml' => $html,
        ]);
    }
    
    
    public function setCurrency(){
        $xml = "<Push_ChangeCurrency_RQ>
            <Authentication>
                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                <Password>".config('ru.RU_PASSWORD')."</Password>
            </Authentication>
            <Location>5677</Location>
            <Currency>INR</Currency>
        </Push_ChangeCurrency_RQ>";
        $xmlResponse = MasterHelper::makeXmlRequest($xml);
    }
}