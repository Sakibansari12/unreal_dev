<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use App\Mail\BookingPaymentRequestEmail;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\helper\MasterHelper;
use App\Mail\BookingConfirmationEmail;
use Razorpay\Api\Api;
use Config;
use Mail;
use DB;
use URL;

class RazorpayService{
    public function __construct(){
        $this->api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    }

    public function createOrder($amount, $currency = 'INR', $receipt = 'order_rcptid_11'){
        try {
            $order = $this->api->order->create([
                'amount' => $amount * 100, // Amount in paisa
                'currency' => $currency,
                'receipt' => $receipt,
            ]);
            return $order['id'];
        }
        catch (Exception $e) {
            // Log the error or handle it
            \Log::error("Razorpay Order Creation Failed: " . $e->getMessage());
            return null;
        }
    }
  
  

    public static function createPaymentLink($payload){
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $paymentRequestInfo = PropertyBookingPaymentRequest::find(['id'=>$payload['booking_payment_request_id']])->first();
        $propertyBooking = PropertyBooking::where('id', $paymentRequestInfo->property_booking_id)->first();
        
        $home = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
        if($propertyBooking->pType == 'multiunit'){
            $home = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first();
        }
      
        $propertyBooking->home = $home;
        $amount = $payload['amount'];
        $customer_name = $payload['name'];
        $contact_no = $payload['contact_no'];
        $email = $payload['email'];
        $expire_by = time() + (24 * 60 * 60); 
        try {
            $payment = $api->invoice->create([
                'type' => 'link',
                'amount' => $amount * 100,
                'description' => 'Payment Request for '.$home->unit_name.' || reference no ['.$propertyBooking->booking_id.']',
                'callback_url' => 'https://unreal.tempsite.in/razorpay/webhook/callback',
                'notify' => [
                    'sms' => false,
                    'email' => false
                ],
                'customer' => [
                    'name' => $customer_name,
                    'email' => $email,
                    'contact' =>  $contact_no,
                ],
                'currency' => 'INR',
                'expire_by' => $expire_by
            ]);
            $paymentRequestInfo->payment_link_id = $payment['id'];
            $paymentRequestInfo->payment_link = $payment['short_url'];
            $paymentRequestInfo->link_status = 'Created';
            $paymentRequestInfo->save();
            
            
            $emailData =  array('bookingDetail'=>$propertyBooking, 'paymentRequestInfo'=>$paymentRequestInfo);
            $customerDetails = json_decode($emailData['bookingDetail']->customer_detail, true);
            $data = Mail::to($email)->send(new BookingPaymentRequestEmail($emailData, $customerDetails));
            return response()->json(['status'=>true, 'message' => 'Link created successfully'], 200);
        }
        catch (\Exception $e) {
            dd($e->getMessage());
            return response()->json(['status'=>false, 'message' => $e->getMessage()], 400);
        }
    }



    public static function handleWebhookCallBack($payload){
        // Construct the payload manually
        $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
        $payloadJson = json_encode([
            'event' => 'invoice.paid',
            'payload' => [
                'invoice' => [
                    'id' => $payload['razorpay_invoice_id'],
                    'receipt' => $payload['razorpay_invoice_receipt'],
                    'status' => $payload['razorpay_invoice_status'],
                    'payment_id' => $payload['razorpay_payment_id'],
                ]
            ]
        ]);
       // $signature = $request->header('X-Razorpay-Signature');
             $webhookSecret = config('services.razorpay.webhook_secret');
       
            //$api->utility->verifyWebhookSignature($payloadJson, $signature, $webhookSecret);
            $paymentRequestDetail = PropertyBookingPaymentRequest::where(['payment_link_id'=>$payload['razorpay_invoice_id']])->first();
            $paymentRequestDetail->link_status = $payload['razorpay_invoice_status'];
            if($payload['razorpay_invoice_status']=='paid'){
              $paymentRequestDetail->booking_request_status = 'Payment Received';
            }
            $paymentRequestDetail->save();
            if($payload['razorpay_invoice_status']=='paid'){
                $bookingDetail = PropertyBooking::find(['id'=>$paymentRequestDetail->property_booking_id])->first();
                $paid_amount = $bookingDetail->paid_amount + $paymentRequestDetail->amount;
                $bookingDetail->paid_amount = $paid_amount;
                $totPaid = PropertyBookingPaymentRequest::where(['property_booking_id'=>$bookingDetail->id])->sum('amount');
                $bookingDetail->booking_status = 'paid';
                $bookingDetail->property_booking_status = 'Confirmed';
                $bookingDetail->save();
                
             
                $homeDetail = TblHomeUnit::with('images')->where('id', $bookingDetail->property_id)->first();
                // if($bookingDetail->pType == 'multiunit') {
                //     $homeDetail = TblHomeMultiUnit::with('images')->where('id', $bookingDetail->property_id)->first();
                // }
                 
                // $ruResponse = MasterHelper::makeXmlRequest(reservationXmlRequest($bookingDetail, $homeDetail));
                // if(isset($ruResponse['data']['ReservationID'])  && $ruResponse['data']['ReservationID'] !="0"){
                //     PropertyBooking::where('id', $bookingDetail->id)->update(['ru_booking_id'=>$ruResponse['data']['ReservationID'], 'booking_id'=>$ruResponse['data']['ReservationID'], 'is_pushed_on_ru'=>1]);
                // }
                
                $bookingDetail->property = $homeDetail;
                $bookingDetail->home = $homeDetail;
               
                Mail::to($paymentRequestDetail->email)->send(new BookingConfirmationEmail(array('bookingDetail'=>$bookingDetail, 'id'=>$bookingDetail->id, 'type'=>'customer')));
                
                blockPropertyAvailabilityInRu($homeDetail->ru_property_id, date('y-m-d', strtotime($bookingDetail->checkin_date)) , date('y-m-d', strtotime($bookingDetail->checkout_date)));
            }
            return array('id'=>$paymentRequestDetail->id); 
        
       
    }
}