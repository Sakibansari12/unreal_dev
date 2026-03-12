<?php

namespace App\Http\Controllers\PMS\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Models\TblHome;
use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use App\helper\MasterHelper;
use Illuminate\Support\Collection;
use App\Models\BookingGuestId;
use App\Models\BookingEnquiry;
use App\Models\TblGst;
use App\Exports\BookingExport;
use App\Mail\BookingConfirmationEmail;
use App\Mail\BookingEnquiryUnavailableEmail;
use App\Mail\BookingCancellationEmail;
use App\Mail\BookingPropertyHoldEmail;
use App\Mail\BookingPaymentRequestEmail;
use App\Mail\PaymentRequestConfirmationEmail;
use App\Models\RuPropertyPrice;
use App\Models\TblHomeUnit;
use App\Services\PropertyService;
use App\Models\TblLocation;
use App\Models\TblHomeMultiUnit;
use App\Services\RazorpayService;
use App\Services\PriceLabsPayloadService;
use App\Services\PriceLabService;
use DB;
use App\Mail\BookingPaymentRequestEmailNeftCash;
use Razorpay\Api\Api;
use Carbon\Carbon;
use App\Models\Admin;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;



class BookingByLocationController extends Controller
{

    protected $propertyService;
    protected $priceLabpayloadService;
    protected $priceLabService;


    public function __construct(){
        $this->propertyService = new PropertyService();
        $this->priceLabpayloadService = new PriceLabsPayloadService();
        $this->priceLabService = new PriceLabService();
    }





    // public function byLocation()
    // {
    //     return view('pms.booking.by-location');
    // }
    
     public function byLocation()
    {

        $states = DB::table('states')->get();

        return view('pms.booking.by-location', compact('states'));
    }

    public function locationfetch()
    {
        $locations = TblLocation::where('status', 1)->get();
        return response()->json([
            'status' => true,
            'data' => $locations,
        ]);
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

            $home = array();
            if($request->type == 'unit'){
                $home  = TblHomeUnit::where('id', $request->propertyId)->first();

            }
            else{
                $home  = TblHomeMultiUnit::where('id', $request->propertyId)->first();

            }

            $propertyBooking = new PropertyBooking();
            $propertyBooking->location_id = $request->locationId;
            $propertyBooking->property_id = $request->propertyId;
            $propertyBooking->property_name = $home->unit_name;
            $propertyBooking->total_amount = round($request->netPayableAmount - $request->taxAmount);
             $propertyBooking->website_markup_price = $request->website_markup_price;
            $propertyBooking->discount_amount = round($request->discount_amount);
            $propertyBooking->payable_amount = round($request->netPayableAmount);
            $propertyBooking->additional_charges = $request->additional_charges?json_encode($request->additional_charges):NULL;
            $propertyBooking->tax_amount = $request->taxAmount;
            $propertyBooking->customer_email = $request->email_address;
            $propertyBooking->customer_number = $request->mobile_number;
            $propertyBooking->booking_id = rand(10000000, 99999999);
            $propertyBooking->booking_status = 'pending';
            $propertyBooking->booking_created_by = 'admin';
            $propertyBooking->type = 'location';
            $propertyBooking->no_of_children = $request->no_children?$request->no_children:0;
            $propertyBooking->no_of_adult = $request->no_adult?$request->no_adult:1;
            $propertyBooking->customer_detail = json_encode(array('first_name'=>$request->first_name, 'last_name'=>$request->last_name, 'email'=>$request->email_address , 'country_code'=>$request->country_code,'mobile_number'=>$request->mobile_number));
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
            $propertyBooking->pType = $request->type;

            // if($home){
            //     $propertyBooking->ru_building_id = $home->ru_building_id;
            //     if($home->ru_building_id){
            //         $propertyBookingCount = PropertyBooking::where('ru_building_id', $home->ru_building_id)->where('checkin_date' , $request->checkInDate)->where('checkout_date' ,  $request->checkOutDate)->where('property_id', $request->propertyId)->count();
            //         $room_no = 1;
            //         if($propertyBookingCount >0){
            //             $room_no = $propertyBookingCount + 1;
            //         }
            //         $propertyBooking->room_no = $room_no;
            //     }
            // }




            $propertyBooking->user_id  = $home->user_id;
            $propertyBooking->parent_user_id  = $home->parent_user_id;
            $propertyBooking->owner_id  = $home->owner_id ?? null;
            $propertyBooking->save();


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
               $payload = $this->priceLabpayloadService->preparePriceLabsSyncUnconfirmedReservation($propertyBooking);
               $response = $this->priceLabService->syncReservations($payload);
               $res = $this->propertyService->updateAvaliabilityWithReservation($homeDetail->id, date('Y-m-d', strtotime($propertyBooking->checkin_date)), date('Y-m-d', strtotime($propertyBooking->checkout_date)), 0, $homeDetail->pType,  'Booking');
 
            }
          

            
            $propertyBookingRecent = PropertyBooking::where('id', $propertyBooking->id)->first();

            $property  = TblHomeUnit::where('id', $propertyBookingRecent->property_id)->first(['tbl_home_units.*', 'tbl_home_units.unit_name as home_name']);
            if(!$property){
                $property  = TblHomeMultiUnit::where('id', $propertyBookingRecent->property_id)->first(['tbl_home_multi_units.*', 'tbl_home_multi_units.unit_name as home_name']);
            }
            $propertyBookingRecent->property = $property;
            // $email =  Mail::to($request->email_address)->send(new BookingPropertyHoldEmail(array('mailData'=>$propertyBooking, 'type'=>'customer')));
            // $ruResponse = MasterHelper::makeXmlRequest(reservationXmlRequest($propertyBookingRecent->load('property')));

            // if(isset($ruResponse['data']['ReservationID'])  && $ruResponse['data']['ReservationID'] !="0"){
            //     PropertyBooking::where('id', $propertyBooking->id)->update(['booking_id'=>$ruResponse['data']['ReservationID']]);
            // }
            // else{
            //   //PropertyBooking::where('id', $propertyBooking->id)->update(['ru_response'=>json_encode($ruResponse, true)]);
            // }

            $guestDataBase = new BookingGuestId();
            $guestDataBase->name = $request->first_name.' '.$request->last_name;
            $guestDataBase->email = $request->email_address;
            $guestDataBase->country_code = $request->country_code;
            $guestDataBase->property_name  = $home->unit_name;
            $guestDataBase->mobile_no = $request->mobile_number;
            $guestDataBase->property_booking_id = $propertyBooking->id;
            $guestDataBase->user_id = $propertyBooking->user_id;
            $guestDataBase->save();

            $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
            if(!$property){
                $property  = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first();
            }

            // if($home->ru_building_id){
            //     $propertyBookingCount = PropertyBooking::where('ru_building_id', $home->ru_building_id)->where('checkin_date' , $request->checkInDate)->where('checkout_date' , $request->checkOutDate)->where('property_id', $request->propertyId)->count();
            //     if($propertyBookingCount == $home->no_of_rooms){
            //         blockPropertyAvailabilityInRu($property->ru_property_id, $request->checkInDate, $request->checkOutDate);
            //     }
            // }
            // else{
            //     blockPropertyAvailabilityInRu($property->ru_property_id, $request->checkInDate, $request->checkOutDate);
            // }

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

   
    
    
    /*  public function propertyList(Request $request){

        $checkin_date = $request->checkin_date;
        $checkout_date = $request->checkout_date;

        $no_of_guests = $request->no_adults;

        $last_date =  date('Y-m-d', strtotime($checkout_date. '-1 days'));

        if($last_date == $checkin_date){
            $date_difference_count = 1;
        }
        else{
            $date_difference_count = MasterHelper::getDateDifference($checkin_date, $checkout_date);
        }
        $location_id = $request->location_id;
       // $query = TblHomeUnit::query();

        $filtered_property_list = array();

        //-----------------------unit--------------------//
        $query = $this->propertyService->applyUserRoleFilter(TblHomeUnit::query());
        $query->when($location_id != '', function ($q) use ($location_id) {
            return $q->where('location_id', $location_id);
        });
        $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
            return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        });
        if($request->role == 'Owner'){
            $home->user_id  = $request->userId;
        }
        if ($request->has('role') && $request->role != 'Owner' && $request->role != 'Admin') {
            $query->where('sub_user_id', $request->userId);
        }
        $list = $query->with('additionalCharge')->where('status', 1)->whereNotNull('ru_property_id')->get();
         
        if(!empty($list)){
            foreach($list as $detail){
                $checkOutIsAvailable = PropertyBooking::where('property_id', $detail->id)->where('checkout_date', $checkin_date)->where('pType', $detail->pType)->where('property_booking_status', 'Confirmed')->count();

                if($checkOutIsAvailable > 0){
                    $checkin_date = date('Y-m-d', strtotime($checkin_date.'+1 day'));
                }

                $checkInIsAvailable = PropertyBooking::where('property_id', $detail->id)->where('checkin_date', $checkout_date)->where('pType', $detail->pType)->where('property_booking_status', 'Confirmed')->count();

                if($checkInIsAvailable > 0){
                    $checkout_date = date('Y-m-d', strtotime($checkout_date.'-1 day'));
                }
                
                $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $checkout_date)->where('is_available', 'no')->count();
                $price = 0;
                if($count == 0){
                    $price = $initial_price = 0;
                    //dd($checkin_date, $last_date);
                    $price = RuPropertyPrice::where('property_id', $detail->id)->whereBetween('price_date', [$checkin_date, $last_date])->where('type', 'unit')->sum('price');
                    
                    if($price >0){
                        $gst_amount = 0;
                        $gstPrecentage = 0;
                        $getAppliedGst  = getAppliedGst($price);
                        if($getAppliedGst){
                            $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                            $gst_amount = $precentageAmount;
                            $gstPrecentage = $getAppliedGst->gst_percentage;
                        }
                        $per_night_price  = $price/$date_difference_count;
                        $website_markup_price = 0;
                        if(setting()->website_markup){
                            $website_markup_price  = ($per_night_price*setting()->website_markup)/100;
                            $per_night_price = $per_night_price +  ($per_night_price*setting()->website_markup)/100;
                        }
                        $price = $per_night_price*$date_difference_count;
                        $detail->per_night_price = $per_night_price;
                        $detail->website_markup_price = $website_markup_price*$date_difference_count;
                        $detail->price = $price;
                        $detail->initial_price = $price;
                        $detail->gst_amount = $gst_amount;
                        $detail->home_name = $detail->unit_name;
                        $detail->gst_percentage = $gstPrecentage;
                        $extra_no_of_guest = 0;
                        $extra_guest_charge = 0;

                        if($no_of_guests >$detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests){
                            if($detail->maximum_number_of_guests == $no_of_guests){
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                            }
                            else{
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                            }
                            $extra_guest_charge = $extra_no_of_guest*$detail->extra_guest_charges;
                            if($request->tax_inclusive == 1){
                                $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                                if($getAppliedGst){
                                    $precentageExtraGuestChargeAmount = ($extra_guest_charge*$getAppliedGst->gst_percentage)/100;
                                    $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                                }
                            }
                        }
                        $detail->extra_no_of_guest = $extra_no_of_guest;
                        $detail->final_extra_guest_charge = $extra_guest_charge;
                        array_push($filtered_property_list, $detail);
                    }
                }
            }
        }

        //-------------------multi unit------------------------//
        $query = $this->propertyService->applyUserRoleFilter(TblHomeMultiUnit::query());
        $query->when($location_id != '', function ($q) use ($location_id) {
            return $q->where('location_id', $location_id);
        });
        $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
            return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        });
        if($request->role == 'Owner'){
            $home->user_id  = $request->userId;
        }
        if ($request->has('role') && $request->role != 'Owner' && $request->role != 'Admin') {
            $query->where('sub_user_id', $request->userId);
        }
        $list = $query->with('additionalCharge')->where('status', 1)->whereNotNull('ru_property_id')->get();

        if(!empty($list)){
            foreach($list as $detail){
                $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $checkout_date)->where('is_available', 'no')->count();
                $price = 0;
                if($count == 0){
                    $price = $initial_price = 0;
                    $price = RuPropertyPrice::where('property_id', $detail->id)->whereBetween('price_date', [$checkin_date, $last_date])->where('type', 'multiunit')->sum('price');
                    if($price >0){
                        $gst_amount = 0;
                        $gstPrecentage = 0;
                        $getAppliedGst  = getAppliedGst($price);
                        if($getAppliedGst){
                            $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                            $gst_amount = $precentageAmount;
                            $gstPrecentage = $getAppliedGst->gst_percentage;
                        }
                        $per_night_price  = $price/$date_difference_count;
                        $website_markup_price = 0;
                        if(setting()->website_markup){
                            $website_markup_price  = ($per_night_price*setting()->website_markup)/100;
                            $per_night_price = $per_night_price +  ($per_night_price*setting()->website_markup)/100;
                        }
                        $price = $per_night_price*$date_difference_count;
                        $detail->per_night_price = $per_night_price;
                        $detail->website_markup_price = $website_markup_price*$date_difference_count;
                        $detail->price = $price;
                        $detail->initial_price = $price;
                        $detail->gst_amount = $gst_amount;
                        $detail->home_name = $detail->unit_name;
                        $detail->gst_percentage = $gstPrecentage;
                        $extra_no_of_guest = 0;
                        $extra_guest_charge = 0;

                        if($no_of_guests >$detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests){
                            if($detail->maximum_number_of_guests == $no_of_guests){
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                            }
                            else{
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                            }
                            $extra_guest_charge = $extra_no_of_guest*$detail->extra_guest_charges;
                            if($request->tax_inclusive == 1){
                                $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                                if($getAppliedGst){
                                    $precentageExtraGuestChargeAmount = ($extra_guest_charge*$getAppliedGst->gst_percentage)/100;
                                    $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                                }
                            }
                        }
                        $detail->extra_no_of_guest = $extra_no_of_guest;
                        $detail->final_extra_guest_charge = $extra_guest_charge;
                        array_push($filtered_property_list, $detail);
                    }
                }
            }
        }
        $gst_slab = TblGst::get();
        return response()->json([
            'status' => true,
            'data' => array('property_list'=>$filtered_property_list, 'no_of_nights'=>$date_difference_count, 'is_gst_allowed'=>setting()->is_allow_gst, 'states'=>MasterHelper::getStateList(), 'gst_slab'=>$gst_slab),
            'message' => 'Listed successfully.'
        ], 200);
    } */

   public function propertyList(Request $request){
        $checkin_date = $request->checkin_date;
        $checkout_date = $request->checkout_date;
        $no_of_guests = $request->no_adults;
        $last_date =  date('Y-m-d', strtotime($checkout_date. '-1 days'));
        if($last_date == $checkin_date){
            $date_difference_count = 1;
        }
        else{
            $date_difference_count = MasterHelper::getDateDifference($checkin_date, $checkout_date);
        }
        $location_id = $request->location_id;
       // $query = TblHomeUnit::query();
        $filtered_property_list = array();
        //-----------------------unit--------------------//
        $query = $this->propertyService->applyUserRoleFilter(TblHomeUnit::query());
        $query->when($location_id != '', function ($q) use ($location_id) {
            return $q->where('location_id', $location_id);
        });
        $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
            return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        });
        if($request->role == 'Owner'){
            $home->user_id  = $request->userId;
        }
        if ($request->has('role') && $request->role != 'Owner' && $request->role != 'Admin') {
            $query->where('sub_user_id', $request->userId);
        }
        $list = $query->with('additionalCharge')->where('status', 1)->whereNotNull('ru_property_id')->get();
         
        if(!empty($list)){
            foreach($list as $detail){
                $checkOutIsAvailable = DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_to', $checkin_date)->first();
                if($checkOutIsAvailable){
                    $checkin_date = date('Y-m-d', strtotime($checkin_date.'+1 day'));
                }
                $checkInIsAvailable = DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_from', $checkout_date)->first();
               
                
                if($checkInIsAvailable){
                    $checkout_date = date('Y-m-d', strtotime($checkout_date.'-1 day'));
                }
                
                $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $checkout_date)->where('is_available', 'no')->count();
                $price = 0;
                
                 
                if($count == 0){
                    $price = $initial_price = 0;
                    //dd($checkin_date, $last_date);
                    $price = RuPropertyPrice::where('property_id', $detail->id)->whereBetween('price_date', [$request->checkin_date, $last_date])->where('type', 'unit')->sum('price');
                   
                    if($price >0){
                        $gst_amount = 0;
                        $gstPrecentage = 0;
                        $getAppliedGst  = getAppliedGst($price);
                        if($getAppliedGst){
                            $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                            $gst_amount = $precentageAmount;
                            $gstPrecentage = $getAppliedGst->gst_percentage;
                        }
                        $per_night_price  = $price/$date_difference_count;
                        $website_markup_price = 0;
                        if(setting()->website_markup){
                            $website_markup_price  = ($per_night_price*setting()->website_markup)/100;
                            $per_night_price = $per_night_price +  ($per_night_price*setting()->website_markup)/100;
                        }
                        $price = $per_night_price*$date_difference_count;
                        $detail->per_night_price = $per_night_price;
                        $detail->website_markup_price = $website_markup_price*$date_difference_count;
                        $detail->price = $price;
                        $detail->initial_price = $price;
                        $detail->gst_amount = $gst_amount;
                        $detail->home_name = $detail->unit_name;
                        $detail->gst_percentage = $gstPrecentage;
                        $extra_no_of_guest = 0;
                        $extra_guest_charge = 0;
                        if($no_of_guests >$detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests){
                            if($detail->maximum_number_of_guests == $no_of_guests){
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                            }
                            else{
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                            }
                            $extra_guest_charge = $extra_no_of_guest*$detail->extra_guest_charges;
                            if($request->tax_inclusive == 1){
                                $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                                if($getAppliedGst){
                                    $precentageExtraGuestChargeAmount = ($extra_guest_charge*$getAppliedGst->gst_percentage)/100;
                                    $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                                }
                            }
                        }
                        $detail->extra_no_of_guest = $extra_no_of_guest;
                        $detail->final_extra_guest_charge = $extra_guest_charge;
                        array_push($filtered_property_list, $detail);
                    }
                }
            }
        }
        //-------------------multi unit------------------------//
        $query = $this->propertyService->applyUserRoleFilter(TblHomeMultiUnit::query());
        $query->when($location_id != '', function ($q) use ($location_id) {
            return $q->where('location_id', $location_id);
        });
        $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {
            return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        });
        if($request->role == 'Owner'){
            $home->user_id  = $request->userId;
        }
        if ($request->has('role') && $request->role != 'Owner' && $request->role != 'Admin') {
            $query->where('sub_user_id', $request->userId);
        }
        $list = $query->with('additionalCharge')->where('status', 1)->whereNotNull('ru_property_id')->get();
        if(!empty($list)){
            foreach($list as $detail){
                $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $checkout_date)->where('is_available', 'no')->count();
                $price = 0;
                if($count == 0){
                    $price = $initial_price = 0;
                    $price = RuPropertyPrice::where('property_id', $detail->id)->whereBetween('price_date', [$checkin_date, $last_date])->where('type', 'multiunit')->sum('price');
                    if($price >0){
                        $gst_amount = 0;
                        $gstPrecentage = 0;
                        $getAppliedGst  = getAppliedGst($price);
                        if($getAppliedGst){
                            $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                            $gst_amount = $precentageAmount;
                            $gstPrecentage = $getAppliedGst->gst_percentage;
                        }
                        $per_night_price  = $price/$date_difference_count;
                        $website_markup_price = 0;
                        if(setting()->website_markup){
                            $website_markup_price  = ($per_night_price*setting()->website_markup)/100;
                            $per_night_price = $per_night_price +  ($per_night_price*setting()->website_markup)/100;
                        }
                        $price = $per_night_price*$date_difference_count;
                        $detail->per_night_price = $per_night_price;
                        $detail->website_markup_price = $website_markup_price*$date_difference_count;
                        $detail->price = $price;
                        $detail->initial_price = $price;
                        $detail->gst_amount = $gst_amount;
                        $detail->home_name = $detail->unit_name;
                        $detail->gst_percentage = $gstPrecentage;
                        $extra_no_of_guest = 0;
                        $extra_guest_charge = 0;
                        if($no_of_guests >$detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests){
                            if($detail->maximum_number_of_guests == $no_of_guests){
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                            }
                            else{
                                $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                            }
                            $extra_guest_charge = $extra_no_of_guest*$detail->extra_guest_charges;
                            if($request->tax_inclusive == 1){
                                $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);
                                if($getAppliedGst){
                                    $precentageExtraGuestChargeAmount = ($extra_guest_charge*$getAppliedGst->gst_percentage)/100;
                                    $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                                }
                            }
                        }
                        $detail->extra_no_of_guest = $extra_no_of_guest;
                        $detail->final_extra_guest_charge = $extra_guest_charge;
                        array_push($filtered_property_list, $detail);
                    }
                }
            }
        }
        $gst_slab = TblGst::get();
        return response()->json([
            'status' => true,
            'data' => array('property_list'=>$filtered_property_list, 'no_of_nights'=>$date_difference_count, 'is_gst_allowed'=>setting()->is_allow_gst, 'states'=>MasterHelper::getStateList(), 'gst_slab'=>$gst_slab),
            'message' => 'Listed successfully.'
        ], 200);
    }
  
}