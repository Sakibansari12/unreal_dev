<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TblHome;
use App\Services\RazorpayService;
use App\Models\PropertyBooking;
use App\Models\PropertyBookingPaymentRequest;
use App\Models\TblHomeImageVideo;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use Mail;
use DB;


class RazorpayController extends Controller{
    
    public function handleWebhookCallBack(Request $request) {
        if ($request->missing('razorpay_invoice_id')) {
            return response()->json([
                'success' => true,
                'message' => 'missing keys.',
            ], 200); 
        }
        try {
           
            DB::table('razorpaywebhooks')->insert([
                'data'=>json_encode($request->all(), true)    
            ]);
            $payload = array(
                'razorpay_invoice_id'=>$request->query('razorpay_invoice_id'),
                'razorpay_invoice_receipt'=>$request->query('razorpay_invoice_receipt'),
                'razorpay_invoice_status'=>$request->query('razorpay_invoice_status'),
                'razorpay_payment_id'=>$request->query('razorpay_payment_id'),
                'razorpay_signature'=>$request->query('razorpay_signature')
            );
            
            $paymentRequestInfo = PropertyBookingPaymentRequest::where(['payment_link_id'=>$payload['razorpay_invoice_id']])->first();
            
            if(!$paymentRequestInfo){
                abort('404');
            }
    
            if($paymentRequestInfo->link_status !='paid'){
                $handleCallBackRes = RazorpayService::handleWebhookCallBack($payload);
            }

            $data = PropertyBooking::withTrashed()->where('id', $paymentRequestInfo->property_booking_id)->first();
            
            if(!$data){
                abort('404');
            }
            $home = TblHomeUnit::where('id', $data->property_id)->first();

            $data->home = $home;

            return view('webhookthankyou', compact('data', 'payload','paymentRequestInfo'));
            return response()->json([
                'success' => true,
                'message' => 'success',
            ], 200); 
        }
        catch (\Exception $e) {
            dd('message',$e->getMessage());
        }
    }
    
    
    public function paymentThankyou($id) {
        $paymentRequestInfo = PropertyBookingPaymentRequest::find(['id'=>$id])->first();
        $data = PropertyBooking::withTrashed()->where('id', $paymentRequestInfo->property_booking_id)->first();
        
        if($propertyBooking->pType == 'multiunit'){
            $home = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first();
        }else{
            $home = TblHomeUnit::where('id', $data->property_id)->first();
        }

        return view('emails.thankyou', compact('paymentRequestInfo', 'data'));
    }
}