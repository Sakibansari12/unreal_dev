<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyPrice;
use Maatwebsite\Excel\Facades\Excel;
use App\helper\MasterHelper;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Models\PropertyBooking;
use Illuminate\Support\Facades\Storage;
use App\Models\PropertyBookingPaymentRequest;
use Illuminate\Support\Collection;
use App\Models\BookingGuestId;
use App\Models\BookingEnquiry;
use App\Exports\BookingExport;
use App\Models\TblState;
use App\Services\PriceLabsPayloadService;
use App\Services\PriceLabService;
use App\Services\PropertyService;

use DB;

class RuBookingController extends Controller{



    protected $propertyService;
    protected $priceLabpayloadService;
    protected $priceLabService;


    public function __construct(){
        $this->propertyService = new PropertyService();
        $this->priceLabpayloadService = new PriceLabsPayloadService();
        $this->priceLabService = new PriceLabService();
    }



    //---------------- This method use for registring the webhook url------//
    public function setBookingHandlerAPi(){
        $reqXml = "<LNM_PutHandlerUrl_RQ>
            <Authentication>
                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                <Password>".config('ru.RU_PASSWORD')."</Password> 
            </Authentication>
            <HandlerUrl>https://Unreal Estate.in/api/ru/webhook/get/bookings</HandlerUrl>
        </LNM_PutHandlerUrl_RQ>";

        $xmlResponse = MasterHelper::makeXmlRequest($reqXml);
        dd($reqXml, $xmlResponse);
    }

    //---------------- Callback response  ---//
     public function getBookingFromRu($hash = NULL){
        header("Content-Type:application/json");
        $data = file_get_contents('php://input');
        $type ='unit';
        Storage::disk('local')->put('booking/booking_'.time().'.txt', $data);    
        $result = simplexml_load_string($data);
        $result_json = json_encode($result);
        $result_array = json_decode($result_json,TRUE);
        
       
        if(isset($result_array['Reservation'])){
            if(isset($result_array['Reservation']['ReservationStatusID'])){
                if($result_array['Reservation']['ReservationStatusID'] =='1' || $result_array['Reservation']['ReservationStatusID'] =='3'){
                    $propertyBooking = array();
                    if($result_array['Reservation']['Creator']==config('ru.RU_USER_NAME')."1234"){
                        $stayInfo =  $result_array['Reservation']['StayInfos']['StayInfo'];
                        $customerInfo =  $result_array['Reservation']['CustomerInfo'];
                    
                        $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
                        
                        if(!$home){
                            $home  = TblHomeMultiUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
                        }
                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
                        $noOfNights = (integer)(MasterHelper::getDateDifference(
                            $stayInfo['DateFrom'],
                            $stayInfo['DateTo']
                        ));
                        $basePrice = $stayInfo['Costs']['RUPrice'];
                        $propertyBooking = array();
                        $propertyBooking['location_id'] = $home->location_id;
                        $propertyBooking['property_id'] = $home->id;
                        $propertyBooking['pType'] = $home->pType;
                        $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['booking_id'] = $result_array['Reservation']['ReservationID'];
                        $propertyBooking['booking_status'] = 'paid';
                        $propertyBooking['booking_created_by'] = 'ru';
                        $propertyBooking['booking_from'] = 'ru';
                        $propertyBooking['ru_booking_status'] = 'Confirmed';
                        $propertyBooking['type'] = 'Location';
                        $propertyBooking['customer_name'] = $customerInfo['Name'].' '.$customerInfo['SurName'];
                        $propertyBooking['channel'] = 'RU';
                        
                        $propertyBooking['base_price'] = $basePrice;
                        $propertyBooking['no_of_nights'] = $noOfNights;
                        $propertyBooking['per_night_price'] = round($basePrice/$noOfNights);
                        
                        
                        $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                        $propertyBooking['customer_detail'] = json_encode([
                            'first_name'     => $customerInfo['Name'] ?? '',
                            'last_name'      => $customerInfo['SurName'] ?? '',
                            'email'          => (!empty($customerInfo['Email']) && $customerInfo['Email'] !== '[]') ? $customerInfo['Email'] : '',
                            'mobile_number'  => '',
                            'country_code'   => ''
                        ]);
                        $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                        $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                        $propertyBooking['pType'] = 'unit';
                    }
                    else if($result_array['Reservation']['Creator']=='airbnb@rentalsunited.com'){
                        if(isset($result_array['Reservation']['StayInfos'])){
                            $stayInfo =  $result_array['Reservation']['StayInfos']['StayInfo'];
                            $customerInfo =  $result_array['Reservation']['CustomerInfo'];
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
                            $propertyBooking['pType'] = $home->pType;
                            $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                            $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                            $propertyBooking['booking_id'] = $result_array['Reservation']['ReservationID'];
                            $propertyBooking['booking_status'] = 'paid';
                            $propertyBooking['booking_created_by'] = 'ru';
                            $propertyBooking['booking_from'] = 'ru';
                            $propertyBooking['ru_booking_status'] = 'Confirmed';
                            $propertyBooking['type'] = 'Location';
                            $propertyBooking['channel'] = 'Airbnb';
                            $propertyBooking['no_of_adult'] = $stayInfo['NumberOfGuests'];
                            $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>$customerInfo['SurName'], 'email'=> (!empty($customerInfo['Email']) && $customerInfo['Email'] !== '[]') ? $customerInfo['Email'] : '' , 'mobile_number'=>$customerInfo['Phone'], 'country_code'   => ''));
                            $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($stayInfo['DateFrom']));
                            $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($stayInfo['DateTo']));
                            
                            $propertyBooking['customer_name'] = $customerInfo['Name'].' '.$customerInfo['SurName'];
                            $propertyBooking['pType'] = 'unit';
                            
                            
                        }
                        else{
                            $customerInfo =  $result_array['Reservation']['CustomerInfo'];
                            $home  = TblHomeUnit::where('ru_property_id', $result_array['Reservation']['PropertyID'])->first();
                            $date_difference_count = MasterHelper::getDateDifference($result_array['Reservation']['DateFrom'], $result_array['Reservation']['DateTo']);
                            $propertyBooking['location_id'] = $home->location_id;
                            $propertyBooking['property_id'] = $home->id;
                            $propertyBooking['property_name'] = $home->unit_name;
                            $propertyBooking['pType'] = $home->pType;
                            $propertyBooking['total_amount'] = $result_array['Reservation']['Price'];
                            $propertyBooking['payable_amount'] = $result_array['Reservation']['Price'];
                            $propertyBooking['booking_id'] = $result_array['Reservation']['ReservationID'];
                            $propertyBooking['booking_status'] = 'paid';
                            $propertyBooking['booking_created_by'] = 'ru';
                            $propertyBooking['booking_from'] = 'ru';
                            $propertyBooking['ru_booking_status'] = 'Confirmed';
                            $propertyBooking['type'] = 'Location';
                            $propertyBooking['channel'] = 'Airbnb';
                            $propertyBooking['no_of_adult'] = $result_array['Reservation']['NumberOfGuests'];
                            $propertyBooking['customer_detail'] = json_encode(array('first_name'=>$customerInfo['Name'], 'last_name'=>($customerInfo['SurName']=='not provided'?'':$customerInfo['SurName']), 'email'=>$customerInfo['Email'] , 'mobile_number'=>(isset($customerInfo['MobilePhone'][0]))?$customerInfo['MobilePhone'][0]:'N/A'));
                            $propertyBooking['checkin_date'] = date('Y-m-d', strtotime($result_array['Reservation']['DateFrom']));
                            $propertyBooking['checkout_date'] = date('Y-m-d', strtotime($result_array['Reservation']['DateTo']));
                            $propertyBooking['pType'] = 'unit';
                        }
                    }
                    else if($result_array['Reservation']['Creator']=='makemytrip@rentalunited.com'){
                        $stayInfo =  $result_array['Reservation']['StayInfos']['StayInfo'];
                        $customerInfo =  $result_array['Reservation']['CustomerInfo'];
                        $home  = TblHomeUnit::where('ru_property_id', $stayInfo['PropertyID'])->first();
                        
                        $date_difference_count = MasterHelper::getDateDifference($stayInfo['DateFrom'], $stayInfo['DateTo']);
                    
                        $propertyBooking = array();
                        $propertyBooking['location_id'] = $home->location_id;
                        $propertyBooking['property_id'] = $home->id;
                        $propertyBooking['pType'] = $home->pType;
                        $propertyBooking['total_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['payable_amount'] = $stayInfo['Costs']['ClientPrice'];
                        $propertyBooking['booking_id'] = $result_array['Reservation']['ReservationID'];
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
                        $propertyBooking['customer_name'] = $customerInfo['Name'].' '.$customerInfo['SurName'];
                    }
                    $propertyBooking['property_booking_status'] = 'Confirmed';
                    $propertyBooking['payment_status'] = 'Paid';
                    $propertyBooking['paid_amount'] = $propertyBooking['payable_amount'];
                    $propertyBooking['ru_building_id'] = $home->ru_building_id;
                    
                    
                    
                    $count = PropertyBooking::where('booking_id', $result_array['Reservation']['ReservationID'])->count();
                    if($count == 0){
                        // if($home->ru_building_id){
                        //     $propertyBookingCount = PropertyBooking::where('checkin_date' , $propertyBooking['checkin_date'])->where('checkout_date' ,  $propertyBooking['checkout_date'])->where('property_id', $home->id)->count();
                        //     $room_no = 1;
                        //     if($propertyBookingCount >0){
                        //         $room_no = $propertyBookingCount + 1; 
                        //     }
                        //     $propertyBooking['room_no'] = $room_no;
                        // }
                        $booking = PropertyBooking::create($propertyBooking);
                    }
                    else{
                        $booking = PropertyBooking::where('booking_id', $result_array['Reservation']['ReservationID'])->first();
                        $booking->update($propertyBooking);
                    }

                    if($home->price_lab_sync_date_time){
                        $payload = $this->priceLabpayloadService->preparePriceLabsSyncUnconfirmedReservation($booking);
                        $response = $this->priceLabService->syncReservations($payload);
                        $res = $this->propertyService->updateAvaliabilityWithReservation($home->id, date('Y-m-d', strtotime($booking->checkin_date)), date('Y-m-d', strtotime($booking->checkout_date)), 0, $home->pType,  'Booking');
            
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
            else{
                dd('Lead');
            }
        }
        else{
            $bookingDetail = PropertyBooking::with('property')->where('booking_id', $result_array['ReservationID'])->first();
            $home  = TblHomeUnit::where('id', $bookingDetail->property_id)->first();
                        
            if(!$home){
                $home  = TblHomeMultiUnit::where('id', $bookingDetail->property_id)->first();
            }
            RuPropertyAvailability::where('ru_property_id', $home->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($bookingDetail->checkin_date)), date('Y-m-d', strtotime($bookingDetail->checkout_date))])->update(['is_available'=>'yes']);

            //PropertyBooking::where('booking_id', $result_array['ReservationID'])->delete();


            $reason = 'Booking Cancelled from OTA';
            $close = 1;
            $property = $bookingDetail->property;
            if(isPriceLabEnable() && $property->price_lab_sync_date_time){

                $res = $this->propertyService->updateAvaliabilityOnBookingCancelOrDeleteFromCalendar($property->id, $bookingDetail->checkin_date, $bookingDetail->checkout_date, $close, $home->pType,  $reason);
                
                //-----------cancel booking on reservation-------------//
                $payload = $this->priceLabpayloadService->preparePriceLabsReservationCancellationPayload($bookingDetail);
                $this->priceLabService->syncReservations($payload);
            }




        }
       
    }

}