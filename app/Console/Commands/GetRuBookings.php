<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TblHomeUnit;
use App\Models\RuPropertyPrice;
use App\Models\PropertyBooking;
use App\Models\RuPropertyAvailability;
use App\helper\MasterHelper;

use Illuminate\Support\Facades\Storage;
use carbon;
use DB;

class GetRuBookings extends Command{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'job:getRuBookings';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Ru Bookings';
    /**
     * Execute the console command.
     */
    public function handle(){
        set_time_limit(0);
        try {
            $dateFrom = date("Y-m-d H:i:s", strtotime("-3900 minutes"));
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
            
           
            $channelArray = $response['data'];
            if(isset($response['data']['Reservations']['Reservation'])){
                Storage::disk('local')->put('booking/booking_cron'.date('Y-m-d').'_'.time().'.txt', json_encode($response, true));
            }
            else{
                Storage::disk('local')->put('booking/booking_cron_with_empty_'.date('Y-m-d').'_'.time().'.txt', json_encode($response, true));
            }
            if(isset($response['data']['Reservations']['Reservation'])){
                
                if(isset($response['data']['Reservations']['Reservation'][0])){
                    foreach($response['data']['Reservations']['Reservation'] as $key=>$reservation){
                        if(isset($reservation['ReservationID'])){
                            if($reservation['StatusID'] =='1' || $reservation['StatusID'] =='3'){
                             
                                $propertyBooking = array();
                                if($reservation['Creator']=='agoda@rentalsunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
                                    
    
                                    $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
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
    
                                    $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
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
    
                                        $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
                                        
                                        $basePrice = $stayInfo['Costs']['ClientPrice'];
    
                                        $propertyBooking = array();
                                        $propertyBooking['location_id'] = $home->location_id;
                                        $propertyBooking['property_id'] = $home->id;
                                        $propertyBooking['property_name'] =$home->unit_name;
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
                                        $propertyBooking['pType'] = 'unit';
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
                                        $propertyBooking['pType'] = 'unit';
                                        
                                        
                                        $propertyBooking['no_of_adult'] = $reservation['NumberOfGuests'];
                                        $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>($customerInfo['SurName']=='not provided'?'':$customerInfo['SurName']), 'email'=>$customerInfo['Email'] , 'mobile_number'=>(isset($customerInfo['MobilePhone'][0]))?$customerInfo['MobilePhone'][0]:'N/A'));
                                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($reservation['DateFrom']));
                                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($reservation['DateTo']));
                                    }
                                    
                                }
                                else if($reservation['Creator']=='makemytrip@rentalunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
                                    $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
    
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
                                $propertyBooking['pType'] = 'unit';
                                
                              
                               
                                $count = PropertyBooking::where('booking_id', $reservation['ReservationID'])->count();
                                if($count == 0){
                                    PropertyBooking::create($propertyBooking);
                                    RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($stayInfo['DateFrom'])), date('Y-m-d', strtotime($stayInfo['DateTo']))])->update(['is_available'=>'no']);
                                    DB::table('ru_property_blocked')->insert([
                                        'ru_property_id' => $home->ru_property_id,
                                        'property_id' => $home->id,
                                        'date_from' => date('Y-m-d', strtotime($stayInfo['DateFrom'])),
                                        'date_to' => date('Y-m-d', strtotime($stayInfo['DateTo'])),
                                        'is_available' => 'no',
                                        'reason' => 'By Admin',
                                        'type' => 'unit',
                                    ]);
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
    
                                    $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
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
                                    $propertyBooking['channel'] = 'RU';
                                    $propertyBooking['property_booking_status'] = 'Confirmed';
                                    $propertyBooking['payment_status'] = 'Paid';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['MessagingContactId']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }
                                else if($reservation['Creator']=='agoda@rentalsunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
    
                                    $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
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
                                    $propertyBooking['property_booking_status'] = 'Confirmed';
                                    $propertyBooking['payment_status'] = 'Paid';
                                    $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['MessagingContactId']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }
                                else if($reservation['Creator']=='bookingcom@rentalsunited.com'){
                                    $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                    $customerInfo =  $reservation['CustomerInfo'];
    
                                    $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
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
                                    $propertyBooking['property_booking_status'] = 'Confirmed';
                                    $propertyBooking['payment_status'] = 'Paid';
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>$customerInfo['Phone']));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                    
                                }
                                else if($reservation['Creator']=='airbnb@rentalsunited.com'){
                                    if(isset($reservation['StayInfos'])){
                                        $stayInfo =  $reservation['StayInfos']['StayInfo'];
                                        $customerInfo =  $reservation['CustomerInfo'];
    
                                        $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
                                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
                                        
                                        $basePrice = $stayInfo['Costs']['ClientPrice'];
                                        
                                       
                                        $price_per_night = round($basePrice / (int)$date_difference_count,2);
                                        
                                        
                                        $tax =  1.05;
                                        if ($price_per_night > 7500) {
                                            $tax = 1.18;
                                        }
                                        $basePrice = round($basePrice / $tax, 2);
                                        $base_price_after_discount = $basePrice;
                                       
                                        $price_per_night = round($base_price_after_discount / (int)$date_difference_count);
                                    
                                        $tax = 5;
                                        if ($price_per_night > 7500) {
                                            $tax = 18;
                                        }
                                        $tax_amount = ($base_price_after_discount * $tax) / 100;
                                        

                                        $propertyBooking = array();
                                        $propertyBooking['tax_amount'] = $tax_amount;
                                        $propertyBooking['no_of_nights'] = (int)$date_difference_count;
                                        $propertyBooking['tax'] = $tax;
                                        $propertyBooking['base_price'] = $base_price_after_discount;
                                        $propertyBooking['taxable_amount'] = $base_price_after_discount;
                                        $propertyBooking['per_night_price'] = $price_per_night;
                                        $propertyBooking['pType'] = 'unit';
                                        
                                        
                                        $propertyBooking['location_id'] = $home->location_id;
                                        $propertyBooking['property_id'] = $home->id;
                                        $propertyBooking['property_name'] = $home->unit_name;
                                     
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
                                        $propertyBooking['property_booking_status'] = 'Confirmed';
                                        $propertyBooking['payment_status'] = 'Paid';
                                        $propertyBooking['customer_detail'] = json_encode([
                                            'first_name'     => $customerInfo['Name'],
                                            'last_name'      => ($customerInfo['SurName'] === 'not provided' ? '' : $customerInfo['SurName']),
                                            'email'          => $customerInfo['Email'],
                                            'mobile_number'  => isset($customerInfo['MobilePhone'][0]) 
                                                                ? $customerInfo['MobilePhone'][0] 
                                                                : 'N/A',
                                            'country_code'   => isset($customerInfo['MobilePhone'][0]) 
                                                                ? '91' 
                                                                : null,
                                        ]);
                                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                        $propertyBooking['customer_name'] = $customerInfo['Name'].' '.$customerInfo['SurName'];
                                        $propertyBooking['customer_email'] = $customerInfo['Email'];
                                    }
                                    else{
                                     
                                        $customerInfo =  $reservation['CustomerInfo'];
                                        $home  = TblHomeUnit::where('ru_property_id', $reservation['PropertyID'])->first();
                                        $date_difference_count = MasterHelper::getDateDifference($reservation['DateFrom'], $reservation['DateTo']);
                                        $propertyBooking['location_id'] = $home->location_id;
                                        $propertyBooking['property_name'] = $home->unit_name;
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
                                        $propertyBooking['property_booking_status'] = 'Confirmed';
                                        $propertyBooking['payment_status'] = 'Paid';
                                        $propertyBooking['pType'] = 'unit';
                                        $propertyBooking['customer_detail'] = json_encode([
                                            'first_name'     => $customerInfo['Name'],
                                            'last_name'      => ($customerInfo['SurName'] === 'not provided' ? '' : $customerInfo['SurName']),
                                            'email'          => $customerInfo['Email'],
                                            'mobile_number'  => isset($customerInfo['MobilePhone'][0]) 
                                                                ? $customerInfo['MobilePhone'][0] 
                                                                : 'N/A',
                                            'country_code'   => isset($customerInfo['MobilePhone'][0]) 
                                                                ? '91' 
                                                                : null,
                                        ]);

                                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($reservation['DateFrom']));
                                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($reservation['DateTo']));
                                        $propertyBooking['customer_name'] = $customerInfo['Name'].' '.$customerInfo['SurName'];
                                        $propertyBooking['customer_email'] = $customerInfo['Email'];
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
                                    $propertyBooking['property_booking_status'] = 'Confirmed';
                                    $propertyBooking['payment_status'] = 'Paid';
                                    $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=>$customerInfo['Email'] , 'mobile_number'=>'N/A', 'country_code'=>NULL));
                                    $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                                    $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                                }


                                $price = $propertyBooking['payable_amount'];
                                $propertyBooking['property_booking_status'] = 'Confirmed';
                                $propertyBooking['payment_status'] = 'Paid';
                                $propertyBooking['paid_amount'] = $price;
                                $propertyBooking['pType'] = 'unit';
                                $propertyBooking['customer_name'] = $customerInfo['Name'].' '.$customerInfo['SurName'];
                                $propertyBooking['customer_email'] = $customerInfo['Email'];
                            
                                $getAppliedGst  = getAppliedGst($price);
                                if($getAppliedGst){
                                    $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;
                                    $gst_amount = $precentageAmount;
                                    $gstPrecentage = (integer)$getAppliedGst->gst_percentage;
                                    // $propertyBooking['tax'] = $gstPrecentage;
                                    // $propertyBooking['taxable_amount'] = $gst_amount;
                                }
                                
                                $propertyBooking['property_name'] = $home->unit_name;
                                
                                 
                                $count = PropertyBooking::where('booking_id', $reservation['ReservationID'])->count();
                                if($count == 0){
                                    
                                   try {
                                        PropertyBooking::create($propertyBooking);
                                    } catch (\Exception $e) {
                                       dd($e->getMessage());
                                    }
                                    RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($stayInfo['DateFrom'])), date('Y-m-d', strtotime($stayInfo['DateTo']))])->update(['is_available'=>'no']);
                                    DB::table('ru_property_blocked')->insert([
                                        'ru_property_id' => $home->ru_property_id,
                                        'property_id' => $home->id,
                                        'date_from' => date('Y-m-d', strtotime($stayInfo['DateFrom'])),
                                        'date_to' => date('Y-m-d', strtotime($stayInfo['DateTo'])),
                                        'is_available' => 'no',
                                        'reason' => 'By Admin',
                                        'type' => 'unit',
                                    ]);
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
}