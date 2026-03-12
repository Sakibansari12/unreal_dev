<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\TblHome;



use App\Models\RuPropertyPrice;

use App\Models\BookingGuestId;

use Maatwebsite\Excel\Facades\Excel;

use App\Exports\BookingExport;

use App\helper\MasterHelper;

use App\Models\PropertyBooking;

use App\Models\PropertyBookingPaymentRequest;

use Barryvdh\DomPDF\Facade\Pdf;

use App\Models\TblCompany;



use App\Mail\PaymentRequestConfirmationEmail;

use App\Mail\BookingConfirmationEmail;

use App\Mail\BookingPaymentRequestEmail;

use App\Mail\BookingPropertyHoldEmail;

use App\Mail\BookingCancellationEmail;

use App\Models\BookingQuotation;

use App\Mail\BookingEnquiryEmail;

use App\Models\BookingEnquiry;



use App\Mail\BookingQuotationEmail;
 
use Mail;

use Carbon\Carbon;

use Storage;

use DB;

use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\RuPropertyMinstay;
use App\Models\RuPropertyAvailability;


class ScriptController extends Controller{



    public function ru(){

        // $xml = "<Push_PutAvbUnits_RQ>

        //                     <Authentication>

        //                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
        //                        <Password>".config('ru.RU_PASSWORD')."</Password>
        //                     </Authentication>

        //                     <MuCalendar PropertyID='3826813'>

        //                         <Date From='2024-06-25' To='2024-06-28'>

        //                         <U>0</U>

        //                         <C>3</C>

        //                         </Date>

        //                     </MuCalendar>

        //                 </Push_PutAvbUnits_RQ>";

        // $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xml);

    }





    public function ruAvaliability(){

        set_time_limit(0);

        try {

            $list = TblHome::whereNotNull('ru_property_id')->get();

            if(!empty($list)){

                $price_date=date('Y-m-d', strtotime(date('Y-m-d'). '-1 days'));

                foreach($list as $detail){

                    foreach($list as $detail){

                        $i = 210;

                        $date_from = date('Y-m-d');

                        $date_to = date('Y-m-d', strtotime($date_from . ' +'.$i.' day'));

                        $startDate = Carbon::create($date_from); // YYYY, MM, DD

                        $endDate = Carbon::create($date_to); // YYYY, MM, DD

                        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {

                            $cDate = $date->toDateString();

                            $xmlReqForPropertyPrice = "<Pull_ListPropertyAvailabilityCalendar_RQ>

                                <Authentication>

                                    <UserName>".config('ru.RU_USER_NAME')."</UserName>

                                    <Password>".config('ru.RU_PASSWORD')."</Password>

                                </Authentication>

                                <PropertyID>".$detail['ru_property_id']."</PropertyID>

                                <DateFrom>".$cDate."</DateFrom>

                                <DateTo>".$cDate."</DateTo>

                            </Pull_ListPropertyAvailabilityCalendar_RQ>";

                            $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);

                    

                            if($ruPropertyPriceResponse){

                                if(isset($ruPropertyPriceResponse['data']['PropertyCalendar']['CalDay']['IsBlocked'])){

                                    $isAvailable =  'yes';

                                    if($ruPropertyPriceResponse['data']['PropertyCalendar']['CalDay']['IsBlocked']=='true'){

                                        $isAvailable = 'no';

                                    }

                                    $detailA = array();

                                    $detailA['is_available'] = $isAvailable;

                                    $detailA['availability_date'] = $cDate;

                                    $detailA['ru_property_id'] = $detail['ru_property_id'];

                                    $count = RuPropertyAvailability::where(['ru_property_id'=>$detail['ru_property_id'], 'availability_date'=>$cDate])->count();

                                   

                                    if($count > 0){

                                        RuPropertyAvailability::where(['ru_property_id'=>$detail['ru_property_id'], 'availability_date'=>$cDate])->update($detailA);

                                    }

                                    else{

                                        RuPropertyAvailability::create($detailA);

                                    }

                                }

                            }

                        }

                    }

                }

            }

            echo 'Done';

        }

        catch (\Exception $e) {

            dd($e->getMessage());

        }

    }



    public function bookingExport() {

        return Excel::download(new BookingExport('1'), 'bookings.xlsx');

    }





    // public function pdf() {
    //     $homes = TblHome::get();
    //     foreach($homes as $data){
    //         $priceArray = array('ru_property_id'=>$data->ru_property_id, 'is_available'=>'yes', 'property_id'=>$data->id);
    //         for($i=0; $i<=180; $i++){
    //             if($i == 0){
    //               $price_date = date('Y-m-d'); 
    //             }
    //             else{
    //                 $price_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
    //             }
    //             $priceArray['availability_date'] = $price_date;
    //             RuPropertyAvailability::create($priceArray);
    //         }
    //     }
    // }


    public function pdf(){
        $xml = "<Pull_ListAmenities_RQ>
            <Authentication>
            <UserName>".config('ru.RU_USER_NAME')."</UserName>
            <Password>".config('ru.RU_PASSWORD')."</Password>
            </Authentication>
        </Pull_ListAmenities_RQ>";

        dd($xml);
        $xmlResponse = MasterHelper::makeXmlRequest($xml);
        dd($xmlResponse);
        
    }
    
     public function ruMenualBooking($hash = NULL){
        try {
            $dateFrom = date("Y-m-d H:i:s", strtotime("-390 minutes"));
            $dateTo = date("Y-m-d H:i:s");
        
            // $dateFrom = '2024-06-23 00:00:00';
            // $dateTo = '2024-06-25 00:00:00';
            
            $xmlReq = "<Pull_ListReservations_RQ>
                        <Authentication>
                            <UserName>".config('ru.RU_USER_NAME')."</UserName>
                            <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <DateFrom>".$dateFrom."</DateFrom>
                        <DateTo>".$dateTo."</DateTo>
                        <LocationID>0</LocationID>
                    </Pull_ListReservations_RQ>";
            $response = MasterHelper::makeXmlRequest($xmlReq);
            
            dd($response);
            
         
            if(isset($response['data']['Reservations']['Reservation'])){
                Storage::disk('local')->put('booking_cron'.date('Y-m-d').'_'.time().'.txt', json_encode($response, true));
            }
            else{
                Storage::disk('local')->put('booking_cron_with_empty_'.date('Y-m-d').'_'.time().'.txt', json_encode($response, true));
            }
            if(isset($response['data']['Reservations']['Reservation'])){
                if(isset($response['data']['Reservations']['Reservation'][0])){
                    foreach($response['data']['Reservations']['Reservation'] as $key=>$reservation){
                        if(isset($reservation['ReservationID'])){
                            if($reservation['StatusID'] =='1' || $reservation['StatusID'] =='3'){
                             
                                $propertyBooking = array();
                                if($reservation['Creator']=='gpshospitalityservices@gmail.com'){
                                  
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
    
                                    $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                    $propertyBooking = array();
                                    $propertyBooking['location_id'] = 1;
                                    $propertyBooking['property_id'] = $home->id;
                                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                    $propertyBooking['booking_status'] = 'paid';
                                    $propertyBooking['booking_created_by'] = 'ru';
                                    $propertyBooking['booking_from'] = 'ru';
                                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                                    $propertyBooking['type'] = 'Location';
                                    $propertyBooking['channel'] = 'RU';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['MessagingContactId']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }

                                else if($reservation['Creator']=='agoda@rentalsunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
    
                                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                    $propertyBooking = array();
                                    $propertyBooking['location_id'] = $home->location_id;
                                    $propertyBooking['property_id'] = $home->id;
                                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                    $propertyBooking['booking_status'] = 'paid';
                                    $propertyBooking['booking_created_by'] = 'ru';
                                    $propertyBooking['booking_from'] = 'ru';
                                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                                    $propertyBooking['type'] = 'Location';
                                    $propertyBooking['channel'] = 'Agoda';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['MessagingContactId']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }
                                
                                else if($reservation['Creator']=='bookingcom@rentalsunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
    
                                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                    $propertyBooking = array();
                                    $propertyBooking['location_id'] = $home->location_id;
                                    $propertyBooking['property_id'] = $home->id;
                                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                    $propertyBooking['booking_status'] = 'paid';
                                    $propertyBooking['booking_created_by'] = 'ru';
                                    $propertyBooking['booking_from'] = 'ru';
                                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                                    $propertyBooking['type'] = 'Location';
                                    $propertyBooking['channel'] = 'Booking.com';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['Phone']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                    
                                
                                }
                                else if($reservation['Creator']=='airbnb@rentalsunited.com'){
                                    if(isset($reservation['StayInfos'])){
                                        $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                        $customerInfo =  $reservation['CustomerInfo'];
    
                                        $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                        $propertyBooking = array();
                                        $propertyBooking['location_id'] = $home->location_id;
                                        $propertyBooking['property_id'] = $home->id;
                                        $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                        $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                        $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                        $propertyBooking['booking_status'] = 'paid';
                                        $propertyBooking['booking_created_by'] = 'ru';
                                        $propertyBooking['booking_from'] = 'ru';
                                        $propertyBooking['ru_booking_status'] = 'Confirmed';
                                        $propertyBooking['type'] = 'Location';
                                        $propertyBooking['channel'] = 'Airbnb';
                                        $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                        $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['Phone']));
                                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                    }
                                    else{
                                        $customerInfo =  $reservation['CustomerInfo'];
                                        $home  = TblHome::where('ru_property_id', $reservation['PropertyID'])->first();
                                        $date_difference_count = MasterHelper::getDateDifference($reservation['DateFrom'], $reservation['DateTo']);
                                        $propertyBooking['location_id'] = $home->location_id;
                                        $propertyBooking['property_id'] = $home->id;
                                        $propertyBooking['total_amount'] = $reservation['Price'];
                                        $propertyBooking['payable_amount'] = $reservation['Price'];
                                        $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                        $propertyBooking['booking_status'] = 'paid';
                                        $propertyBooking['booking_created_by'] = 'ru';
                                        $propertyBooking['booking_from'] = 'ru';
                                        $propertyBooking['ru_booking_status'] = 'Confirmed';
                                        $propertyBooking['type'] = 'Location';
                                        $propertyBooking['channel'] = 'Airbnb';
                                        $propertyBooking['no_of_adult'] = $reservation['NumberOfGuests'];
                                        $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>($customerInfo['SurName']=='not provided'?'':$customerInfo['SurName']), 'email'=>$customerInfo['Email'] , 'mobile_number'=>(isset($customerInfo['MobilePhone'][0]))?$customerInfo['MobilePhone'][0]:'N/A'));
                                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($reservation['DateFrom']));
                                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($reservation['DateTo']));
                                    }
                                }
                                else if($reservation['Creator']=='makemytrip@rentalunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
                                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
    
                                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                    $propertyBooking = array();
                                    $propertyBooking['location_id'] = $home->location_id;
                                    $propertyBooking['property_id'] = $home->id;
                                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                    $propertyBooking['booking_status'] = 'paid';
                                    $propertyBooking['booking_created_by'] = 'ru';
                                    $propertyBooking['booking_from'] = 'ru';
                                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                                    $propertyBooking['type'] = 'Location';
                                    $propertyBooking['channel'] = 'MakeMyTrip';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>'N/A'));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }
                               
                                $count = PropertyBooking::where('booking_id', $reservation['ReservationID'])->count();
                                if($count == 0){
                                    PropertyBooking::create($propertyBooking);
                                    RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($stayInfo['DateFrom'])), date('Y-m-d', strtotime($stayInfo['DateTo']))])->update(['is_available'=>'no']);
                                }
                            }
                        }
                    }
                }
                else{
                    $reservation = $response['data']['Reservations']['Reservation'];
                    if(isset($reservation['ReservationID'])){
                            if($reservation['StatusID'] =='1' || $reservation['StatusID'] =='3'){
                                $propertyBooking = array();
                                if($reservation['Creator']=='gagan@tisyastays.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
    
                                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                    $propertyBooking = array();
                                    $propertyBooking['location_id'] = 1;
                                    $propertyBooking['property_id'] = $home->id;
                                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                    $propertyBooking['booking_status'] = 'paid';
                                    $propertyBooking['booking_created_by'] = 'ru';
                                    $propertyBooking['booking_from'] = 'ru';
                                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                                    $propertyBooking['type'] = 'Location';
                                    $propertyBooking['channel'] = 'RU';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['MessagingContactId']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }
                                else if($reservation['Creator']=='agoda@rentalsunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
    
                                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                    $propertyBooking = array();
                                    $propertyBooking['location_id'] = $home->location_id;
                                    $propertyBooking['property_id'] = $home->id;
                                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                    $propertyBooking['booking_status'] = 'paid';
                                    $propertyBooking['booking_created_by'] = 'ru';
                                    $propertyBooking['booking_from'] = 'ru';
                                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                                    $propertyBooking['type'] = 'Location';
                                    $propertyBooking['channel'] = 'Agoda';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['MessagingContactId']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }
                                else if($reservation['Creator']=='bookingcom@rentalsunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
    
                                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                    $propertyBooking = array();
                                    $propertyBooking['location_id'] = $home->location_id;
                                    $propertyBooking['property_id'] = $home->id;
                                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                    $propertyBooking['booking_status'] = 'paid';
                                    $propertyBooking['booking_created_by'] = 'ru';
                                    $propertyBooking['booking_from'] = 'ru';
                                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                                    $propertyBooking['type'] = 'Location';
                                    $propertyBooking['channel'] = 'Booking.com';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['Phone']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                    
                                }
                                else if($reservation['Creator']=='airbnb@rentalsunited.com'){
                                    if(isset($reservation['StayInfos'])){
                                        $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                        $customerInfo =  $reservation['CustomerInfo'];
    
                                        $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                        $propertyBooking = array();
                                        $propertyBooking['location_id'] = $home->location_id;
                                        $propertyBooking['property_id'] = $home->id;
                                        $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                        $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                        $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                        $propertyBooking['booking_status'] = 'paid';
                                        $propertyBooking['booking_created_by'] = 'ru';
                                        $propertyBooking['booking_from'] = 'ru';
                                        $propertyBooking['ru_booking_status'] = 'Confirmed';
                                        $propertyBooking['type'] = 'Location';
                                        $propertyBooking['channel'] = 'Airbnb';
                                        $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                        $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['Phone']));
                                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                    }
                                    else{
                                        $customerInfo =  $reservation['CustomerInfo'];
                                        $home  = TblHome::where('ru_property_id', $reservation['PropertyID'])->first();
                                        $date_difference_count = MasterHelper::getDateDifference($reservation['DateFrom'], $reservation['DateTo']);
                                        $propertyBooking['location_id'] = $home->location_id;
                                        $propertyBooking['property_id'] = $home->id;
                                        $propertyBooking['total_amount'] = $reservation['Price'];
                                        $propertyBooking['payable_amount'] = $reservation['Price'];
                                        $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                        $propertyBooking['booking_status'] = 'paid';
                                        $propertyBooking['booking_created_by'] = 'ru';
                                        $propertyBooking['booking_from'] = 'ru';
                                        $propertyBooking['ru_booking_status'] = 'Confirmed';
                                        $propertyBooking['type'] = 'Location';
                                        $propertyBooking['channel'] = 'Airbnb';
                                        $propertyBooking['no_of_adult'] = $reservation['NumberOfGuests'];
                                        $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>($customerInfo['SurName']=='not provided'?'':$customerInfo['SurName']), 'email'=>$customerInfo['Email'] , 'mobile_number'=>(isset($customerInfo['MobilePhone'][0]))?$customerInfo['MobilePhone'][0]:'N/A'));
                                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($reservation['DateFrom']));
                                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($reservation['DateTo']));
                                    }
                                }
                                else if($reservation['Creator']=='makemytrip@rentalunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
                                    $home  = TblHome::where('ru_property_id', $stayInfo['PropertyID'])->first();
    
                                    $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
    
                                    $propertyBooking = array();
                                    $propertyBooking['location_id'] = $home->location_id;
                                    $propertyBooking['property_id'] = $home->id;
                                    $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                                    $propertyBooking['booking_id'] = $reservation['ReservationID'];
                                    $propertyBooking['booking_status'] = 'paid';
                                    $propertyBooking['booking_created_by'] = 'ru';
                                    $propertyBooking['booking_from'] = 'ru';
                                    $propertyBooking['ru_booking_status'] = 'Confirmed';
                                    $propertyBooking['type'] = 'Location';
                                    $propertyBooking['channel'] = 'MakeMyTrip';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>'N/A'));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }


                                $price = $propertyBooking['payable_amount'];
                                $propertyBooking['paid_amount'] = $price;
                                $getAppliedGst  = getAppliedGst($price);
                                if($getAppliedGst){
                                    $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                                    $gst_amount = $precentageAmount;
                                    $gstPrecentage = (integer)$getAppliedGst->gst_percentage;
                                    $propertyBooking['tax'] = $gstPrecentage;
                                    $propertyBooking['taxable_amount'] = $gst_amount;
                                }
                                
                                
                             
                                $count = PropertyBooking::where('booking_id', $reservation['ReservationID'])->count();
                                if($count == 0){
                                    PropertyBooking::create($propertyBooking);
                                    RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($stayInfo['DateFrom'])), date('Y-m-d', strtotime($stayInfo['DateTo']))])->update(['is_available'=>'no']);
                                }
                            }
                        }
                }
                  
            }
        }
        catch (\Exception $e) {
            dd($e->getMessage());
        }
    
    }
    
    
    public function sendMail(){
       set_time_limit(0);
        $locationList  = DB::table('tbl_ru_location')->where('ru_location_type_id', '4')->where('ru_parent_location_id', '336')->where('status', 1)->get();
        foreach($locationList as $val){
            $xml = "<Push_ChangeCurrency_RQ>
                <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                </Authentication>
                <Location>".$val->ru_location_id."</Location>
                <Currency>INR</Currency>
            </Push_ChangeCurrency_RQ>";
            $xmlResponse = MasterHelper::makeXmlRequest($xml);
             
         
        }
    }
     
     
    public function changeAvaliability($ruproperid){
        set_time_limit(0);
        $i = 160;
        $date_from = date('Y-m-d');
        $date_to = date('Y-m-d', strtotime($date_from . ' +'.$i.' day'));
        $units = TblHomeUnit::where('ru_property_id', $ruproperid)->get();
      
        if($units){
            foreach($units as $unit){
                RuPropertyPrice::where('property_id', $unit->id)->where('type', 'unit')->delete();
                RuPropertyMinstay::where('home_id', $unit->id)->where('type', 'unit')->delete();
                RuPropertyAvailability::where('property_id', $unit->id)->where('type', 'unit')->delete();
                $avaliabilityArray = array('ru_property_id'=>$unit->ru_property_id, 'is_available'=>'yes', 'property_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $avaliabilityDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $avaliabilityArray['availability_date'] = $avaliabilityDate;
                    $avaliabilityArray['type'] = 'unit';
                    RuPropertyAvailability::create($avaliabilityArray);
                }
                $xmlAvaliability = "<Push_PutAvbUnits_RQ>
                            <Authentication>
                                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                                <Password>".config('ru.RU_PASSWORD')."</Password>
                            </Authentication>
                            <MuCalendar PropertyID='".$unit->ru_property_id."'>
                                <Date From='".$date_from."' To='".$date_to."'>
                                    <U>1</U>
                                    <C>4</C>
                                </Date>
                            </MuCalendar>
                        </Push_PutAvbUnits_RQ>";
                $xmlResponse = MasterHelper::makeXmlRequest($xmlAvaliability);
            
                if(isset($unit->per_night_price)){
                    $priceArray = array('price'=>$unit->per_night_price, 'property_id'=>$unit->id, 'ru_property_id'=>$unit->ru_property_id);
                    for($i=0; $i<=180; $i++){
                        $price_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                        $priceArray['price_date'] = $price_date;
                        $priceArray['type'] = 'unit';
                        RuPropertyPrice::create($priceArray);
                    }
                }
                
                $priceXml = "<Push_PutPrices_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <Prices PropertyID='".$unit->ru_property_id."'>
                        <Season DateFrom='".$date_from."' DateTo='".$date_to."'>
                            <Price>".$unit->per_night_price."</Price>
                            <Extra>0</Extra>
                        </Season>
                        </Prices>
                </Push_PutPrices_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml); 
                
                
                $minStayArray = array('ru_property_id'=>$unit->ru_property_id, 'is_minstay_count'=>$unit->min_stay, 'home_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $minStayArray['minstay_date'] = $date;
                    $minStayArray['is_minstay_count'] = $unit->min_stay;
                    $minStayArray['type'] = 'unit';
                    RuPropertyMinstay::create($minStayArray);
                }
                
                
                $minStayXml = "<Push_PutAvbUnits_RQ>
                    <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <MuCalendar PropertyID='".$unit->ru_property_id."'>
                    <Date From='".$date_from."' To='".$date_to."'>
                        <U>1</U>
                        <MS>".$unit->min_stay."</MS>
                        <C>4</C>
                    </Date>
                    </MuCalendar>
                </Push_PutAvbUnits_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml); 
                
            } 
        }    
    
        $munits = TblHomeMultiUnit::where('ru_property_id', $ruproperid)->get();
        if($munits){
            foreach($munits as $unit){
                RuPropertyPrice::where('property_id', $unit->id)->where('type', 'multiunit')->delete();
                RuPropertyMinstay::where('home_id', $unit->id)->where('type', 'multiunit')->delete();
                RuPropertyAvailability::where('property_id', $unit->id)->where('type', 'multiunit')->delete();
                $avaliabilityArray = array('ru_property_id'=>$unit->ru_property_id, 'is_available'=>'yes', 'property_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $avaliabilityDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $avaliabilityArray['availability_date'] = $avaliabilityDate;
                    $avaliabilityArray['type'] = 'multiunit';
                    RuPropertyAvailability::create($avaliabilityArray);
                }
                
                $xmlAvaliability = "<Push_PutAvbUnits_RQ>
                            <Authentication>
                                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                                <Password>".config('ru.RU_PASSWORD')."</Password>
                            </Authentication>
                            <MuCalendar PropertyID='".$unit->ru_property_id."'>
                                <Date From='".$date_from."' To='".$date_to."'>
                                    <U>1</U>
                                    <C>4</C>
                                </Date>
                            </MuCalendar>
                        </Push_PutAvbUnits_RQ>";
                $xmlResponse = MasterHelper::makeXmlRequest($xmlAvaliability);
                
                if(isset($unit->per_night_price)){
                    $priceArray = array('price'=>$unit->per_night_price, 'property_id'=>$unit->id, 'ru_property_id'=>$unit->ru_property_id);
                    for($i=0; $i<=180; $i++){
                        $price_date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                        $priceArray['price_date'] = $price_date;
                        $priceArray['type'] = 'multiunit';
                        RuPropertyPrice::create($priceArray);
                    }
                }
                
                $priceXml = "<Push_PutPrices_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <Prices PropertyID='".$unit->ru_property_id."'>
                        <Season DateFrom='".$date_from."' DateTo='".$date_to."'>
                            <Price>".$unit->per_night_price."</Price>
                            <Extra>0</Extra>
                        </Season>
                        </Prices>
                </Push_PutPrices_RQ>";
                
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml);
                
                
                $minStayArray = array('ru_property_id'=>$unit->ru_property_id, 'is_minstay_count'=>$unit->min_stay, 'home_id'=>$unit->id);
                for($i=0; $i<=180; $i++){
                    $date = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $minStayArray['minstay_date'] = $date;
                    $minStayArray['is_minstay_count'] = $unit->min_stay;
                    $minStayArray['type'] = 'multiunit';
                    RuPropertyMinstay::create($minStayArray);
                }
                
                
                $minStayXml = "<Push_PutAvbUnits_RQ>
                    <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <MuCalendar PropertyID='".$unit->ru_property_id."'>
                    <Date From='".$date_from."' To='".$date_to."'>
                        <U>1</U>
                        <MS>".$unit->min_stay."</MS>
                        <C>4</C>
                    </Date>
                    </MuCalendar>
                </Push_PutAvbUnits_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml); 
           }
        }
    }
     
     
    public function changePriceMinstayAvaliability(){
        set_time_limit(0);
        $daysRange = 180;
        $date_from = date('Y-m-d');
        $date_to = date('Y-m-d', strtotime($date_from . ' +' . $daysRange . ' days'));
    
        // Handle single units
        $units = TblHomeUnit::where('ru_property_id', '3965549')->where('is_published', 1)->get();
    
        if ($units) {
            foreach ($units as $unit) {
                for($i=0; $i<=180; $i++){
                    $avaliabilityDate = date('Y-m-d', strtotime(date('Y-m-d') . ' +'.$i.' day'));
                    $avaliabilityArray['minstay_date'] = $avaliabilityDate;
                    $avaliabilityArray['ru_property_id'] = $unit->ru_property_id;
                    $avaliabilityArray['home_id'] = $unit->id;
                    $avaliabilityArray['is_minstay_count'] = 2;
                    $avaliabilityArray['type'] = 'unit';
                    RuPropertyMinstay::create($avaliabilityArray);
                }
            }
        }
    
    }
    
    
    
    public function checkMail(){
        // $paymentRequestInfo = PropertyBookingPaymentRequest::find(['id'=>18])->first();
        // $propertyBooking = PropertyBooking::where('id', $paymentRequestInfo->property_booking_id)->first();
        
        // $home = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
        // if($propertyBooking->pType == 'multiunit'){
        //     $home = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first();
        // }
      
        // $propertyBooking->home = $home;
        // $emailData =  array('bookingDetail'=>$propertyBooking, 'paymentRequestInfo'=>$paymentRequestInfo);
        // $customerDetails = json_decode($emailData['bookingDetail']->customer_detail, true);
        // $data = Mail::to('puneet@iws.in')->send(new BookingPaymentRequestEmail($emailData, $customerDetails));
        
        $roomSpecificAmmenities = DB::table('tbl_ru_specific_room_ammenities')->get();
        
        foreach($roomSpecificAmmenities as $d){
            $a = DB::table('tbl_ru_amenities')->where('amenities_name', $d->ru_room_ammenity_name)->first();
            
            DB::table('tbl_ru_specific_room_ammenities')->where('id', $d->id)->update(['ru_room_ammenity_id'=>$a->ru_amenities_id]);
        }
    }
    
    public function checkMiniumQualityRu(){

         
    
        $xml = "<CM_LNM_OrderMinimumContentQualityCheck_RQ>
          <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                      </Authentication>
                    <ChannelID>426592</ChannelID>
                    <PropertyID>3952113</PropertyID>
                </CM_LNM_OrderMinimumContentQualityCheck_RQ>";
        $responseCheck = MasterHelper::makeXmlRequest($xml);
        dd($responseCheck);
    }
    
    
    public function pushPutPropertyCompositionRoomAmmenites(){
        $xml = "<Pull_ListAmenitiesAvailableForRooms_RQ>
                    <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                </Pull_ListAmenitiesAvailableForRooms_RQ>";
        $response = MasterHelper::makeXmlRequest($xml);
        
        $roomAmmenites = $response['data']['AmenitiesAvailableForRooms'];
      
        
        foreach($roomAmmenites as $rooms){
            foreach($rooms as $room){
                $CompositionRoom = $room['@attributes']['CompositionRoom'];
                $CompositionRoomID = $room['@attributes']['CompositionRoomID'];
                $amenities = $room['Amenity'];
                
                $roomDetail = array(
                   'ru_room_id'=>$CompositionRoomID,  
                   'ru_room_name'=>$CompositionRoom
                );
                DB::table('tbl_ru_specific_rooms')->insert($roomDetail);
                foreach($amenities as $key => $ammenity){
                    $roomAmmenityDetail = array(
                       'tbl_ru_specific_room_ammenity_id'=>$CompositionRoomID,  
                       'ru_room_ammenity_id'=>$key,
                       'ru_room_ammenity_name'=>$ammenity,
                    );
                    DB::table('tbl_ru_specific_room_ammenities')->insert($roomAmmenityDetail);
                }
            }     
        }
       
    }
}

