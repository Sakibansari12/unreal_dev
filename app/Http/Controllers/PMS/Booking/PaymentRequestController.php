<?php

namespace App\Http\Controllers\PMS\Booking;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use App\Models\TblHomeUnit;
use App\Models\PropertyBooking;
use App\Models\TblHomeMultiUnit;
use App\Models\PropertyBookingPaymentRequest;
use App\Mail\BookingPaymentRequestEmailNeftCash;
use App\Mail\BookingConfirmationEmail;
use App\Services\RazorpayService;
use App\Services\PriceLabsPayloadService;
use App\Services\PriceLabService;
use App\Services\PropertyService;
use App\helper\MasterHelper;
use App\Services\InteraktService;

class PaymentRequestController extends Controller
{


    protected $propertyService;
    protected $priceLabpayloadService;
    protected $priceLabService;
    protected $InteraktService;

    public function __construct(){
        $this->propertyService = new PropertyService();
        $this->priceLabpayloadService = new PriceLabsPayloadService();
        $this->priceLabService = new PriceLabService();
        $this->InteraktService = new InteraktService();
    }




    public function paymentRequest(Request $request)
    {
        $paymentRequestId = $request->id;
        $paymentRequestData = PropertyBookingPaymentRequest::where('id', $paymentRequestId)
            ->first();
        return response()->json([
            'status' => true,
            'data' => $paymentRequestData,
            'message' => 'Successfully fetch payment request data'
        ], 200);
    }

    public function paymentRequestSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required',
            'amount' => 'required',
            'booking_id' => 'required',
            'payment_mode' => 'required'
        ]);

        $property_booking_id = $request->booking_id;

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->first()
            ], 500);
        }

        try {
            $propertyBookingDetail = PropertyBooking::where('id', $property_booking_id)->first();
            $lastEntery = PropertyBookingPaymentRequest::orderBy('id', 'desc')->first();
            if ($lastEntery) {
                $count = $lastEntery->id;
            } else {
                $count = 1;
            }

            if ($request->booking_request_id != '') {
                PropertyBookingPaymentRequest::where('booking_request_id', $request->booking_request_id)->delete();
            }

            if ($request->id) {
                // UPDATE
                $detail = PropertyBookingPaymentRequest::find($request->id);
                if (!$detail) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid payment request ID'
                    ], 404);
                }
            } else if ($request->payment_request_id) {
                $detail = PropertyBookingPaymentRequest::find($request->payment_request_id);
                if (!$detail) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid payment request ID'
                    ], 404);
                }
            } else {
                // INSERT
                $detail = new PropertyBookingPaymentRequest();
                $detail->booking_request_id = rand(10000000, 99999999);
                $detail->booking_request_status = 'Pending';
            }

            $detail->property_booking_id = $property_booking_id;
            $detail->name = $request->name;
            $detail->email = $request->email;
            $detail->amount = $request->amount;
            $detail->country_code = $request->country_code;
            $detail->mobile_no = $request->mobile_no;
            $detail->payment_mode = $request->payment_mode;
            $detail->save();

            $insertedId = $detail->id;

            $amount = $detail->amount;
            if ($request->payment_mode == 'Cash' || $request->payment_mode == 'NEFT') {
                $bookingPaidAmount = $propertyBookingDetail->paid_amount + $amount;
            }

            $paymentRequestDetail = PropertyBookingPaymentRequest::where('id', $detail->id)->first();
            $payload = array('booking_id' => $paymentRequestDetail->property_booking_id, 'booking_payment_request_id' => $paymentRequestDetail->id, 'amount' => $paymentRequestDetail->amount, 'name' => $paymentRequestDetail->name, 'email' => $paymentRequestDetail->email, 'contact_no' => $paymentRequestDetail->mobile_no);

            // Razorpay logic commented out
            if($request->payment_mode == 'Razorpay'){
                RazorpayService::createPaymentLink($payload);
            }
            else{
                $propertyBooking = PropertyBooking::where('id', $detail->property_booking_id)->first();
                $home = TblHomeUnit::with('images')->where('id', $propertyBooking->property_id)->first();
                if($propertyBooking->pType == 'multiunit'){
                    $home = TblHomeMultiUnit::with('images')->where('id', $propertyBooking->property_id)->first();
                }
                $propertyBooking->home = $home;

                $emailData =  array('bookingDetail'=>$propertyBooking, 'paymentRequestInfo'=>$detail);
                $customerDetails = json_decode($emailData['bookingDetail']->customer_detail, true);
                try {
                    $data = Mail::to($detail->email)->send(new BookingPaymentRequestEmailNeftCash($emailData, $customerDetails));
                } catch (\Throwable $th) {
                    //throw $th;
                }
            }


            $propertyBookingDetail = PropertyBooking::where('id', $property_booking_id)->first();
            $totalRequestedAmount = PropertyBookingPaymentRequest::where('property_booking_id', $property_booking_id)->sum('amount');

            return response()->json([
                'status' => true,
                'data' => $detail,
                'inserted_id' => $insertedId,
                'propertyBookingDetail' => $propertyBookingDetail,
                'totalRequestedAmount' => $totalRequestedAmount,
                'message' => 'Payment requested successfully.'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function paymentrequestupdate(Request $request){
        $request->validate([
            'booking_request_status' => 'required',
            'note' => 'required|string',
        ]);
        $paymentStatus = PropertyBookingPaymentRequest::where('id', $request->payment_request_id)->first();

        if (!$paymentStatus) {
            return response()->json(['message' => 'Payment request not found'], 404);
        }

        $detail =  PropertyBookingPaymentRequest::findOrFail($request->payment_request_id);
        $detail->note = $request->note;
        $detail->booking_request_status = $request->booking_request_status;
        $detail->save();

        if ($paymentStatus->booking_request_status != 'Payment Received') {
            $booking = PropertyBooking::findOrFail($detail->property_booking_id);
            // dd($booking);
            $paidAmount = $booking->paid_amount + $detail->amount;
            $booking->paid_amount = $paidAmount;
            $booking->property_booking_status = 'Confirmed';

            if ($paidAmount >= $booking->payable_amount) {
                $booking->booking_status = 'paid';
                $booking->payment_status = 'Paid';
            }
            $booking->save();

            $PropertyBooking_data  = PropertyBooking::where('id', $paymentStatus->property_booking_id)->first();
            $homeDetail = TblHomeUnit::with('images')->where('id', $PropertyBooking_data->property_id)->first();

            if($PropertyBooking_data->pType == 'multiunit') {
                $homeDetail = TblHomeMultiUnit::with('images')->where('id', $PropertyBooking_data->property_id)->first();
            }
            $ruResponse = MasterHelper::makeXmlRequest(reservationXmlRequest($booking, $homeDetail));
            if(isset($ruResponse['data']['ReservationID'])  && $ruResponse['data']['ReservationID'] !="0"){
                PropertyBooking::where('id', $PropertyBooking_data->id)->update(['ru_booking_id'=>$ruResponse['data']['ReservationID'], 'booking_id'=>$ruResponse['data']['ReservationID']]);
    
            }
            blockPropertyAvailabilityInRu($homeDetail->ru_property_id, date('Y-m-d', strtotime($booking->checkin_date)), date('Y-m-d', strtotime($booking->checkout_date)));
            try {



                if (is_string($PropertyBooking_data->customer_detail)) {
                    $customerDetails = json_decode($PropertyBooking_data->customer_detail, true);
                } else {
                    $customerDetails = $PropertyBooking_data->customer_detail; // Assume it's already an array
                }

                $this->InteraktService->sendWhatsappConfirmation($customerDetails['first_name'] . " " . $customerDetails['last_name'],$PropertyBooking_data->booking_id,$customerDetails['mobile_number']);

                Mail::to($paymentStatus->email)->send(new BookingConfirmationEmail(array('bookingDetail'=>$PropertyBooking_data, 'id'=>$PropertyBooking_data->id, 'type'=>'customer')));
            } catch (\Throwable $th) {
                //throw $th;
            }

            
            try {
                $bookingDetail = $PropertyBooking_data->load('property');
                if($bookingDetail->property->admin_emails){
                    if(isset($bookingDetail->property->admin_emails->email) && $bookingDetail->property->admin_emails->email){
                        $mail = Mail::to($bookingDetail->property->admin_emails->email);
                        if(isset($bookingDetail->property->admin_emails->cc_emails)){
                                $mail->cc($bookingDetail->property->admin_emails->cc_emails);
                        }
                        $mail->send(new BookingConfirmationEmail(array('bookingDetail'=>$PropertyBooking_data, 'id'=>$PropertyBooking_data->id, 'type'=>'booking_manager')));
                    }                   
                }
           
                } catch (\Throwable $th) {
                
            }




            //----------------push reservation to price lab-----------//
            $close = 0;
            $reason = "Booking";
            
            if($homeDetail->price_lab_sync_date_time){
                $payload = $this->priceLabpayloadService->preparePriceLabsSyncConfirmedReservation($booking);
                $response = $this->priceLabService->syncReservations($payload);
                $res = $this->propertyService->updateAvaliabilityWithReservation($homeDetail->id, date('Y-m-d', strtotime($booking->checkin_date)), date('Y-m-d', strtotime($booking->checkout_date)), $close, $homeDetail->pType,  $reason);
            }

        }
        return response()->json(['message' => 'Payment status updated successfully']);
    }

    public function showPaymentRequestForm($id){
        $user = Auth::guard('admin')->user();
        $bookings = PropertyBooking::query()
        ->when($user->role_id != 1, function ($query) use ($user) {
            if($user->role_id == 8){
                return $query->where('property_bookings.travelagent_id', $user->id);
            }
            else{
                return $query->where('property_bookings.user_id', $user->id);
            } 
        });
        $bookingDetail = $bookings->leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')->leftJoin('tbl_home_units', 'tbl_home_units.id', '=', 'property_bookings.property_id')->with('bookingGuests')->where('property_bookings.id', $id)->first(['tbl_home_units.unit_name as home_name', 'property_bookings.*']);

        if (!$bookingDetail) {
            abort(404, 'Booking not found.');
        }

        $homeName = $bookingDetail->home_name ?? '';

        $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $id)->sum('amount');
        $netPayableAmount = $bookingDetail->payable_amount;
        $remainingAmount = max(0, $netPayableAmount - $paidAmount);

        $customer = json_decode($bookingDetail->customer_detail ?? '{}');

        // First row for blade
        $rows = [[
            'name' => trim(($customer->first_name ?? '') . ' ' . ($customer->last_name ?? '')),
            'email' => $customer->email ?? '',
            'country_code' => $customer->country_code ?? '',
            'mobile_no' => $customer->mobile_number ?? '',
            'country_code' => $customer->country_code ?? '',
            'amount' => $remainingAmount,
            'payment_mode' => '',
        ]];

        $paymentModeList = ['Cash', 'Razorpay', 'NEFT', 'Other'];

        return view('pms.booking.create-payment-request', compact(
            'bookingDetail',
            'homeName',
            'netPayableAmount',
            'remainingAmount',
            'rows',
            'paymentModeList'
        ));
    }

    public function showPaymentRequestEditForm($property_booking_id, $payment_request_id)
    {
        // dd('property_booking_id:', $property_booking_id, 'payment_request_id:', $payment_request_id);
        $bookingDetail = PropertyBooking::leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')
            ->leftJoin('tbl_home_units', 'tbl_home_units.id', '=', 'property_bookings.property_id')
            ->with('bookingGuests')
            ->where('property_bookings.id', $property_booking_id)
            ->first(['tbl_home_units.unit_name as home_name', 'property_bookings.*']);

        if (!$bookingDetail) {
            abort(404, 'Booking not found.');
        }

        $homeName = $bookingDetail->home_name ?? '';

        $paidAmount = PropertyBookingPaymentRequest::where('property_booking_id', $property_booking_id)->sum('amount');
        $netPayableAmount = $bookingDetail->payable_amount;
        $remainingAmount = max(0, $netPayableAmount - $paidAmount);

        $customer = json_decode($bookingDetail->customer_detail ?? '{}');

        $paymentRequestData = PropertyBookingPaymentRequest::where('id', $payment_request_id)->first();
        // dd($paymentRequestData);
        // First row for blade
        $rows = [[
            'name' => trim($paymentRequestData->name ?? ''),
            'email' => $paymentRequestData->email ?? '',
            'mobile_no' => $paymentRequestData->mobile_no ?? '',
            'amount' => $paymentRequestData->amount,
            'payment_mode' => $paymentRequestData->payment_mode,
        ]];

        $paymentModeList = ['Cash', 'Razorpay', 'NEFT', 'Other'];

        return view('pms.booking.edit-payment-request', compact(
            'bookingDetail',
            'homeName',
            'netPayableAmount',
            'remainingAmount',
            'rows',
            'paymentModeList',
            'paymentRequestData',
            'payment_request_id'
        ));
    }
}