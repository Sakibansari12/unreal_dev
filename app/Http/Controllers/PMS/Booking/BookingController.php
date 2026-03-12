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
use App\Exports\PropertyBookingExport;
use App\Mail\BookingConfirmationEmail;
use App\Mail\BookingEnquiryUnavailableEmail;
use App\Mail\BookingCancellationEmail;
use App\Mail\BookingPropertyHoldEmail;
use App\Mail\BookingPaymentRequestEmail;
use App\Mail\PaymentRequestConfirmationEmail;
use App\Models\RuPropertyPrice;
use App\Models\RuPropertyBlocked;
use App\Models\RuPropertyAvailability;
use App\Services\PropertyService;
use App\Models\TblHomeUnit;
use App\Models\TblLocation;
use App\Models\TblHomeMultiUnit;
use App\Services\RazorpayService;
use App\Services\PriceLabsPayloadService;
use App\Services\PriceLabService;
use DB;
use Mail;
use App\Mail\BookingPaymentRequestEmailNeftCash;
use Razorpay\Api\Api;
use Carbon\Carbon;
use App\Models\Admin;
use Barryvdh\DomPDF\Facade\Pdf;


class BookingController extends Controller
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


    public function index(Request $request)
    {


        $requestParams = $request->all();
        $user = Auth::guard('admin')->user();
        $bookings = PropertyBooking::query()


            ->when($request->searchPropertyId, function ($query) use ($request) {
                return $query->where('property_id', $request->searchPropertyId);
            })
            ->when($request->searchBookingId, function ($query) use ($request) {
                return $query->where('booking_id', $request->searchBookingId);
            })
            ->when($request->searchChannel, function ($query) use ($request) {
                return $query->where('channel', $request->searchChannel);
            })
            ->when($request->searchPaymentStatus, function ($query) use ($request) {
                return $query->where('payment_status', $request->searchPaymentStatus);
            })
            ->when($request->searchBookingStatus, function ($query) use ($request) {
                return $query->where('property_booking_status', $request->searchBookingStatus);
            })
            ->when($request->checkInDate && $request->checkOutDate, function ($query) use ($request) {
                return $query->where('checkin_date', '>=', $request->checkInDate)
                    ->where('checkout_date', '<=', $request->checkOutDate);
            })

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
            ->when($user->role_id != 1 && $user->role_id != 8, function ($query) use ($user) {
                return $query->whereNull('travelagent_id');
            })
            ->leftJoin('tbl_home_units as tbl_homes', 'tbl_homes.id', '=', 'property_bookings.property_id')
            ->with('paymentRequests')
            ->where('property_bookings.payable_amount', '>', 0)
            ->orderBy('property_bookings.id', 'desc')
            ->paginate(30, ['tbl_homes.unit_name as home_name', 'tbl_homes.ru_property_id', 'tbl_homes.home_type', 'tbl_homes.state', 'tbl_homes.location', 'property_bookings.*']);

        // $properties = TblHomeUnit::query();
        // $properties = $this->propertyService->applyUserRoleFilter($properties);
        // $properties->whereNotNull('ru_property_id')->get(['tbl_home_units.unit_name as home_name', 'tbl_home_units.*']);


        $units = $this->propertyService->applyUserRoleFilter(
            TblHomeUnit::whereNotNull('ru_property_id')
        )->get();

        $multiUnits = $this->propertyService->applyUserRoleFilter(
            TblHomeMultiUnit::whereNotNull('ru_property_id')
        )->get();

        $properties = $units->merge($multiUnits);


        $role = 'Admin';
        // dd($bookings);
        return view('pms.booking.list', compact('bookings', 'properties', 'role', 'requestParams'));
    }


    public function byproperty()
    {
        $locations = TblLocation::where('status', 1)->get();
        return view('pms.booking.by-property', compact('locations'));
    }

    public function propertyListByLocationId(Request $request)
    {

        try {
            $filtered_property_list = [];
            $location_id = $request->location_id;

            $models = [
                TblHomeUnit::class,
                TblHomeMultiUnit::class,
            ];

            foreach ($models as $model) {
                $query = $model::query();
                $query = $this->propertyService->applyUserRoleFilter($query);
                $query->when(!empty($location_id), function ($q) use ($location_id) {
                    return $q->where('location_id', $location_id);
                });

                $list = $query
                    ->with('additionalCharges')
                    ->whereNotNull('ru_property_id')
                    // ->where('is_published', 1)
                    //->where('ru_status', 1)
                    ->where('status', 1)
                    ->get();
                //  dd($list);
                foreach ($list as $detail) {
                    $detail->price = 0.00;
                    $detail->per_night_price = 0.00;
                    $detail->home_name = $detail->unit_name;
                    $filtered_property_list[] = $detail;
                }
            }
            return response()->json([
                'status' => true,
                'data' => array('property_list' => $filtered_property_list, 'no_of_nights' => 0),
                'message' => 'Listed successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function getPropertyBookingUnavailableDates(Request $request)
    {
        try {
            $id = $request->property_id;
            if ($request->pType == 'unit') {
                $detail  = TblHomeUnit::where('id', $id)->whereNotNull('ru_property_id')->first();
            }
            else {
                $detail  = TblHomeMultiUnit::where('id', $id)->whereNotNull('ru_property_id')->first();
            }
            // $detail = TblHomeUnit::where('id', $id)->whereNotNull('ru_property_id')->first();
            $propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();
            $previouslyBookedCheckoutDates = PropertyBooking::where('property_id', $detail->id)->where('checkout_date', '>', date('Y-m-d'))->where('pType', $request->pType)
                ->where('property_booking_status', 'Confirmed')
                ->pluck('checkout_date')
                ->toArray();
            $ac = array();
            foreach ($previouslyBookedCheckoutDates as $checkoutDate) {
                $checkin = PropertyBooking::where('property_id', $detail->id)
                    ->where('checkin_date', $checkoutDate)
                    ->where('property_booking_status', '!=', 'Canceled')
                    ->first();
                if ($checkin) {
                    array_push($ac, $checkoutDate);
                }
            }
            $previouslyBookedCheckoutDates = array_values(array_diff($previouslyBookedCheckoutDates, $ac));
            $propertyUnavailableDates = array_values(array_diff($propertyUnavailableDates, $previouslyBookedCheckoutDates));
           
            $blocks = DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_to', '>', date('Y-m-d'))->get(['date_to']);
            
            $finalFilterDateTo = [];
            $finalAddDateTo = [];
           $finalDateTo = [];
            foreach ($blocks as $i => $block) {
                $blocksExisitng = DB::table('ru_property_blocked')->where('ru_property_id', $detail->ru_property_id)->where('date_from', $block->date_to)->first();
                
                if(!$blocksExisitng){
                    $finalDateTo[] = $block->date_to;
                   
                }
                else{
                    $finalAddDateTo[] = $block->date_to;
                }
                
            }
            $propertyUnavailableDates = array_values(array_diff($propertyUnavailableDates, $finalDateTo));
            $propertyUnavailableDates = array_merge($propertyUnavailableDates, $finalAddDateTo);
            return response()->json([
                'status' => true,
                'propertyUnavailableDates' => $propertyUnavailableDates,
                'message' => 'Listed successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function searchProperties(Request $request)
    {
        $request->validate([
            'check_in_date' => 'required',
            'check_out_date' => 'required',
        ]);

        try {
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
            $query = ($request->pType == 'unit') ? TblHomeUnit::query() : TblHomeMultiUnit::query();
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


            $filtered_property_list = array();
            if (!empty($detail)) {
                $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkindate)->where('availability_date', '<=', $checkoutdate)->where('is_available', 'no')->count();
                $price = 0;
                $count = 0;
                if ($count == 0) {
                    $price = $initial_price = 0;
                    $price = RuPropertyPrice::where('property_id', $detail->id)->where('type', $request->pType)->whereBetween('price_date', [$checkindate, $last_date])->sum('price');
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
                        $website_markup_price = 0;
                        if (setting()->website_markup) {
                            $website_markup_price  = ($per_night_price * setting()->website_markup) / 100;
                            $per_night_price = $per_night_price +  ($per_night_price * setting()->website_markup) / 100;
                        }
                        $per_night_price = round($per_night_price);
                        $website_markup_price = $website_markup_price * $date_difference_count;
                        $price = $per_night_price * $date_difference_count;
                        $detail->per_night_price = $per_night_price;
                        $detail->price = $price;
                        $detail->website_markup_price = $website_markup_price;
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
            $states = DB::table('states')->get();
            $cities = $detail ? DB::table('cities')->where('state_id', $detail->state_id)->get() : collect([]);
            // return $detail;
            $html = view('pms.booking.search-property-details', [
                'properties' => $detail,
                'no_of_nights' => $date_difference_count,
                'is_gst_allowed' => setting()->is_allow_gst,
                'gst_slab' => $gst_slab,
                'no_adults' => $no_of_guests,
                'no_children' => $request->no_children,
                'check_in_date' => $checkindate,
                'check_out_date' => $checkoutdate,
                'states' => $states,
                'cities' => $cities,
            ])->render();

            return response()->json([
                'status' => true,
                'data' => array('propertyDetail' => $detail, 'no_of_nights' => $date_difference_count, 'is_gst_allowed' => setting()->is_allow_gst, 'gst_slab' => $gst_slab, 'states' => $states, 'cities' => $cities),
                'html' => $html,
                'message' => 'Listed successfully.'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }

        // $html = view('pms.booking.search-property-details', [
        //     'properties' => $filteredProperties
        // ])->render();


        // return response()->json([
        //     'status' => true,
        //     'message' => 'Properties found',
        //     'html' => $html
        // ]);
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

    public function paymentrequestupdate(Request $request)
    {
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

            if ($PropertyBooking_data->pType == 'multiunit') {
                $homeDetail = TblHomeMultiUnit::with('images')->where('id', $PropertyBooking_data->property_id)->first();
            }
            // dd($homeDetail);
            // $PropertyBooking_data->property = $homeDetail;
            // $ruResponse = MasterHelper::makeXmlRequest(reservationXmlRequest($PropertyBooking_data, $homeDetail));
            // if(isset($ruResponse['data']['ReservationID'])  && $ruResponse['data']['ReservationID'] !="0"){
            // PropertyBooking::where('id', $PropertyBooking_data->id)->update(['ru_booking_id'=>$ruResponse['data']['ReservationID'], 'booking_id'=>$ruResponse['data']['ReservationID'], 'is_pushed_on_ru'=>1]);
            // }

            // blockPropertyAvailabilityInRu($homeDetail->ru_property_id, date('y-m-d', strtotime($PropertyBooking_data->checkin_date)) , date('y-m-d', strtotime($PropertyBooking_data->checkout_date)));
            Mail::to($paymentStatus->email)->send(new BookingConfirmationEmail(array('bookingDetail' => $PropertyBooking_data, 'id' => $PropertyBooking_data->id, 'type' => 'customer')));
        }

        return response()->json(['message' => 'Payment status updated successfully']);
    }

    public function showPaymentRequestForm($id)
    {
        $bookingDetail = PropertyBooking::leftJoin('tbl_location', 'tbl_location.id', '=', 'property_bookings.location_id')
            ->leftJoin('tbl_home_units', 'tbl_home_units.id', '=', 'property_bookings.property_id')
            ->with('bookingGuests')
            ->where('property_bookings.id', $id)
            ->first(['tbl_home_units.unit_name as home_name', 'property_bookings.*']);

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
                // PropertyBooking::where('id', $property_booking_id)->update(['paid_amount'=>$bookingPaidAmount]);
            }

            $paymentRequestDetail = PropertyBookingPaymentRequest::where('id', $detail->id)->first();
            $payload = array('booking_id' => $paymentRequestDetail->property_booking_id, 'booking_payment_request_id' => $paymentRequestDetail->id, 'amount' => $paymentRequestDetail->amount, 'name' => $paymentRequestDetail->name, 'email' => $paymentRequestDetail->email, 'contact_no' => $paymentRequestDetail->mobile_no);

            // Razorpay logic commented out
            if ($request->payment_mode == 'Razorpay') {
                // RazorpayService::createPaymentLink($payload);
            }
            // Email sending logic commented out
            else {
                $propertyBooking = PropertyBooking::where('id', $detail->property_booking_id)->first();
                $home = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
                if ($propertyBooking->pType == 'multiunit') {
                    $home = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first();
                }
                $propertyBooking->home = $home;

                $emailData =  array('bookingDetail' => $propertyBooking, 'paymentRequestInfo' => $detail);
                $customerDetails = json_decode($emailData['bookingDetail']->customer_detail, true);
                $data = Mail::to($detail->email)->send(new BookingPaymentRequestEmailNeftCash($emailData, $customerDetails));
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



    public function bookingEnquiry(Request $request)
    {
        $checkIn = $request->input('checkInDate');
        $checkOut = $request->input('checkOutDate');


        // $query = $this->propertyService->applyUserRoleFilter(BookingEnquiry::query());
        $query = BookingEnquiry::with('propertyLocation');

        $query = $this->propertyService->applyUserRoleFilter($query);

        if ($checkIn && $checkOut) {
            $query->whereDate('checkin_date', '>=', $checkIn)
                ->whereDate('checkout_date', '<=', $checkOut);
        }

        $list = $query->orderBy('id', 'desc')->paginate(10);
        return view('pms.booking.enquiry-list', compact('list'));
    }



    public function delete($id)
    {
        try {
            BookingEnquiry::where('id', $id)->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Enquiry deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function multiDelete(Request $request)
    {
        $ids = $request->input('ids');
        if (empty($ids) || !is_array($ids)) {
            return response()->json([
                'status' => false,
                'message' => 'Enquiry selected for deletion.'
            ], 400);
        }
        try {
            BookingEnquiry::whereIn('id', $ids)->delete();
            return response()->json([
                'status' => true,
                'message' => ' Enquiries deleted successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Internal Error',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function bookingExport(Request $request)
    {
        return Excel::download(new PropertyBookingExport($request->all()), 'bookings.xlsx');
    }




    public function savePropertyBooking(Request $request)
    {
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
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->first()
            ], 500);
        }
        try {
            $home = array();
            if ($request->pType == 'unit') {
                $home  = TblHomeUnit::where('id', $request->propertyId)->first();
            } else {
                $home  = TblHomeMultiUnit::where('id', $request->propertyId)->first();
            }
            if ($request->id) {
                $propertyBooking = PropertyBooking::find($request->id);
            }
            $propertyBooking = new PropertyBooking();
            $propertyBooking->location_id = $request->locationId;
            $propertyBooking->property_id = $request->propertyId;
            $propertyBooking->property_name = $home->unit_name;
            $propertyBooking->total_amount = round($request->netPayableAmount - $request->taxAmount);
            $propertyBooking->discount_amount = round($request->discount_amount);
            $propertyBooking->payable_amount = round($request->netPayableAmount);
            $propertyBooking->additional_charges = $request->additional_charges ? json_encode($request->additional_charges) : NULL;
            $propertyBooking->tax_amount = $request->taxAmount;
            $propertyBooking->booking_id = rand(10000000, 99999999);
            $propertyBooking->booking_status = 'pending';
            $propertyBooking->booking_created_by = 'admin';
            $propertyBooking->type = ($request->has('type')) ? $request->type : 'Property';
            $propertyBooking->no_of_children = $request->no_children ? $request->no_children : 0;
            $propertyBooking->no_of_adult = $request->no_adult ? $request->no_adult : 1;
            $propertyBooking->customer_detail = json_encode(array('first_name' => $request->first_name, 'last_name' => $request->last_name, 'email' => $request->email_address, 'mobile_number' => $request->mobile_number, 'country_code' => $request->country_code));
            $propertyBooking->checkin_date = $request->checkInDate;
            $propertyBooking->checkout_date = $request->checkOutDate;
            if (isset($request->dont_block)) {
                $propertyBooking->is_blocking_hour = $request->dont_block;
            }

            $propertyBooking->per_night_price = $request->per_night_price;
            $propertyBooking->no_of_nights = $request->noOfNights;
            $propertyBooking->tax = $request->tax;
            $propertyBooking->additional_charges_detail = $request->additional_charges ? json_encode($request->additional_charges) : NULL;
            $propertyBooking->additional_charges_discount = $request->add_ons_discount_amount;
            $propertyBooking->tot_additional_charge = $request->tot_additional_charge_amount;
            $propertyBooking->base_price = $request->base_price;
            $propertyBooking->extra_guest_charge = $request->extra_guest_charge == 0 ? NULL : $request->extra_guest_charge;
            $propertyBooking->taxable_amount = $request->totalTaxableAmount;
            $propertyBooking->booking_notes = $request->booking_note;
            $propertyBooking->customer_name = $request->first_name . ' ' . $request->last_name;
            $propertyBooking->channel = 'Offline';
            $propertyBooking->pType = $request->pType;


            $propertyBooking->user_id  = $home->user_id;
            $propertyBooking->parent_user_id  = $home->parent_user_id;
            $propertyBooking->owner_id  = $home->owner_id ?? null;
            $traveluser = Auth::guard('admin')->user();
            if (!empty($traveluser) && $traveluser->role == 'Travel Agent') {
                $propertyBooking->travelagent_id  = $traveluser->id;
                $propertyBooking->channel = 'Travel Agent';
            }
            $propertyBooking->save();



            $propertyBookingRecent = PropertyBooking::where('id', $propertyBooking->id)->first();

            $property  = TblHomeUnit::where('id', $propertyBookingRecent->property_id)->first(['tbl_home_units.*', 'tbl_home_units.unit_name as home_name']);
            if (!$property) {
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
            $guestDataBase->name = $request->first_name . ' ' . $request->last_name;
            $guestDataBase->email = $request->email_address;
            $guestDataBase->property_booking_id = $propertyBooking->id;
            $guestDataBase->user_id = $propertyBooking->user_id;
            $guestDataBase->property_name  = $home->unit_name;
            $guestDataBase->country_code = $request->country_code;
            $guestDataBase->mobile_no = $request->mobile_number;
            $guestDataBase->save();

            DB::table('tbl_leads')->insert(['name' => $request->first_name . ' ' . $request->last_name, 'date' => date('Y-m-d'), 'email' => $request->email_address, 'mobile' => $request->mobile_number, 'booking_id' => $propertyBooking->booking_id, 'stage' => 'Booked']);

            $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
            if (!$property) {
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
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function bookingByProperty(Request $request)
    {
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
        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                'message' => $validator->messages()->first()
            ], 500);
        }

        try {

            $home = array();
            if ($request->pType == 'unit') {
                $home  = TblHomeUnit::where('id', $request->propertyId)->first();
                $property_name = $home->unit_name;
            } else {
                $home  = TblHomeMultiUnit::where('id', $request->propertyId)->first();
                $property_name = $home->unit_name;
            }


            $propertyBooking = new PropertyBooking();
            $propertyBooking->location_id = $request->locationId;
            $propertyBooking->property_id = $request->propertyId;
            $propertyBooking->property_name = $property_name;
            $propertyBooking->total_amount = round($request->netPayableAmount - $request->taxAmount);
            $propertyBooking->website_markup_price = $request->website_markup_price;
            $propertyBooking->discount_amount = round($request->discount_amount);
            $propertyBooking->payable_amount = round($request->netPayableAmount);
            $propertyBooking->additional_charges = $request->additional_charges ? json_encode($request->additional_charges) : NULL;
            $propertyBooking->tax_amount = $request->taxAmount;
            $propertyBooking->customer_email = $request->email_address;
            $propertyBooking->customer_number = $request->mobile_number;
            $propertyBooking->booking_id = rand(10000000, 99999999);
            $propertyBooking->booking_status = 'pending';
            $propertyBooking->booking_created_by = 'admin';
            $propertyBooking->type = ($request->has('type')) ? $request->type : 'Property';
            $propertyBooking->pType = $request->pType;
            $propertyBooking->no_of_children = $request->no_children ? $request->no_children : 0;
            $propertyBooking->no_of_adult = $request->no_adult ? $request->no_adult : 1;
            $propertyBooking->customer_detail = json_encode(array('first_name' => $request->first_name, 'last_name' => $request->last_name, 'email' => $request->email_address, 'country_code' => $request->country_code, 'mobile_number' => $request->mobile_number));
            $propertyBooking->checkin_date = $request->checkInDate;
            $propertyBooking->checkout_date = $request->checkOutDate;
            if (isset($request->dont_block)) {
                $propertyBooking->is_blocking_hour = $request->dont_block;
            }

            $propertyBooking->per_night_price = $request->per_night_price;
            $propertyBooking->no_of_nights = $request->noOfNights;
            $propertyBooking->tax = $request->tax;
            $propertyBooking->additional_charges_detail = $request->additional_charges ? json_encode($request->additional_charges) : NULL;
            $propertyBooking->additional_charges_discount = $request->add_ons_discount_amount;
            $propertyBooking->tot_additional_charge = $request->tot_additional_charge_amount;
            $propertyBooking->base_price = $request->base_price;
            $propertyBooking->extra_guest_charge = $request->extra_guest_charge == 0 ? NULL : $request->extra_guest_charge;
            $propertyBooking->taxable_amount = $request->totalTaxableAmount;
            $propertyBooking->booking_notes = $request->booking_note;
            $propertyBooking->customer_name = $request->first_name . ' ' . $request->last_name;
            $propertyBooking->channel = 'Offline';

            if ($home) {
                $propertyBooking->ru_building_id = $home->ru_building_id;
                if ($home->ru_building_id) {
                    $propertyBookingCount = PropertyBooking::where('ru_building_id', $home->ru_building_id)->where('checkin_date', $request->checkInDate)->where('checkout_date',  $request->checkOutDate)->where('property_id', $request->propertyId)->count();
                    $room_no = 1;
                    if ($propertyBookingCount > 0) {
                        $room_no = $propertyBookingCount + 1;
                    }
                    $propertyBooking->room_no = $room_no;
                }
            }

            if ($request->role == 'Owner') {
                $propertyBooking->user_id  = $request->userId;
            }
            if ($request->has('role') && $request->role != 'Owner' && $request->role != 'Admin') {
                $propertyBooking->user_id  = User::where('id', $request->userId)->value('owner_id');
                $propertyBooking->sub_user_id = $request->userId;
            }
            $propertyBooking->user_id  = $home->user_id;
            $propertyBooking->parent_user_id  = $home->parent_user_id;
            $propertyBooking->owner_id  = $home->owner_id;
            $traveluser = Auth::guard('admin')->user();
            if (!empty($traveluser) && $traveluser->role == 'Travel Agent') {
                $propertyBooking->travelagent_id  = $traveluser->id;
                $propertyBooking->channel = 'Travel Agent';
            }
            $propertyBooking->company_detail = $request->company_detail ? json_encode($request->company_detail) : null;
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
            if (!$property) {
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
            $guestDataBase->name = $request->first_name . ' ' . $request->last_name;
            $guestDataBase->email = $request->email_address;
            $guestDataBase->country_code = $request->country_code;
            $guestDataBase->property_name  = $home->unit_name;
            $guestDataBase->property_booking_id = $propertyBooking->id;
            $guestDataBase->user_id = $propertyBooking->user_id;
            $guestDataBase->mobile_no = $request->mobile_number;
            $guestDataBase->save();

           // DB::table('tbl_leads')->insert(['name' => $request->first_name . ' ' . $request->last_name, 'date' => date('Y-m-d'), 'email' => $request->email_address, 'mobile' => $request->mobile_number, 'booking_id' => $propertyBooking->booking_id, 'stage' => 'Booked']);


            $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
            if (!$property) {
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
        } catch (Exception $e) {
            return response()->json([
                'status' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // public function deleteBooking($id)
    // {
    //     try {
    //         $bookingDetail = PropertyBooking::where(['id' => $id])->first();


    //         $property  = TblHomeUnit::where('id', $bookingDetail->property_id)->first();
    //         if (!$property) {
    //             $property  = TblHomeMultiUnit::where('id', $bookingDetail->property_id)->first();
    //         }

    //         $checkinDate = date('Y-m-d', strtotime($bookingDetail->checkin_date));
    //         $checkoutDate = date('Y-m-d', strtotime($bookingDetail->checkout_date));

    //         $checkBookingCount = PropertyBooking::where('id', '!=', $bookingDetail->id)->where('checkin_date', $checkinDate)->where('checkout_date', $checkoutDate)->where('property_booking_status', 'Confirmed')->count();


    //         // if($checkBookingCount == 0){
    //         //     if($bookingDetail->booking_id){
    //         //         $xmlRequest = "<Push_CancelReservation_RQ>
    //         //                 <Authentication>
    //         //                     <UserName>".config('ru.RU_USER_NAME')."</UserName>
    //         //                     <Password>".config('ru.RU_PASSWORD')."</Password>
    //         //                 </Authentication>
    //         //                 <ReservationID>".$bookingDetail->booking_id."</ReservationID>
    //         //                 <CancelTypeID>1</CancelTypeID>
    //         //             </Push_CancelReservation_RQ>";
    //         //         $ruResponse = MasterHelper::makeXmlRequest($xmlRequest);
    //         //     }
    //         //     unblock($property->ru_property_id, $bookingDetail->checkin_date, $bookingDetail->checkout_date);
    //         // }

    //         $propertyBooking = PropertyBooking::where('id', $id)->first();
    //         $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_units.*', 'tbl_home_units.unit_name as home_name']);
    //         if (!$property) {
    //             $property  = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_multi_units.*', 'tbl_home_multi_units.unit_name as home_name']);
    //         }
    //         $propertyBooking->property = $property;
    //         $customerDetail = json_decode($propertyBooking->customer_detail);

    //         if ($customerDetail->email != []) {
    //             $email =  Mail::to($customerDetail->email)->send(new BookingCancellationEmail(array('mailData' => $propertyBooking, 'type' => 'customer')));
    //         }

    //         //$delete = PropertyBooking::where(['id' => $id])->update(['property_booking_status', 'property_booking_status']);
    //         $delete = PropertyBooking::where(['id' => $id])->delete();


    //         return response()->json([
    //             'status' => true,
    //             'data' => '',
    //             'message' => 'Role deleted successfully'
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Internal Error",
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }

    // public function cancelBooking($id)
    // {
    //     try {
    //         $bookingDetail = PropertyBooking::where(['id' => $id])->first();
    //         $property  = TblHomeUnit::where('id', $bookingDetail->property_id)->first();
    //         if (!$property) {
    //             $property  = TblHomeMultiUnit::where('id', $bookingDetail->property_id)->first();
    //         }
    //         $checkinDate = date('Y-m-d', strtotime($bookingDetail->checkin_date));
    //         $checkoutDate = date('Y-m-d', strtotime($bookingDetail->checkout_date));

    //         $checkBookingCount = PropertyBooking::where('id', '!=', $bookingDetail->id)->where('checkin_date', $checkinDate)->where('checkout_date', $checkoutDate)->where('property_booking_status', 'Confirmed')->count();

    //         unblockPropertyAvailabilityInRu($property->ru_property_id, date('Y-m-d', strtotime($bookingDetail->checkin_date)), date('Y-m-d', strtotime($bookingDetail->checkout_date)));

    //         $propertyBooking = PropertyBooking::where('id', $id)->first();
    //         $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_units.*', 'tbl_home_units.unit_name as home_name']);
    //         if (!$property) {
    //             $property  = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_multi_units.*', 'tbl_home_multi_units.unit_name as home_name']);
    //         }
    //         $propertyBooking->property = $property;
    //         $customerDetail = json_decode($propertyBooking->customer_detail);

    //         if ($customerDetail->email != []) {
    //             $email =  Mail::to($customerDetail->email)->send(new BookingCancellationEmail(array('mailData' => $propertyBooking, 'type' => 'customer')));
    //         }

    //         $delete = PropertyBooking::where(['id' => $id])->update(['property_booking_status' => 'Canceled']);
    //         //$delete = PropertyBooking::where(['id' => $id])->delete();

    //         // if($bookingDetail->transcation_id && $bookingDetail->channel=='Website' || $bookingDetail->channel=='pms'){
    //         //     $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
    //         //     $payment = $api->payment->fetch($bookingDetail->transcation_id);
    //         //     $refund = $payment->refund(['amount' => ($bookingDetail->payable_amount/2)*100]);
    //         // }


    //         return response()->json([
    //             'status' => true,
    //             'data' => '',
    //             'message' => "Successfully Deleted"
    //         ], 200);
    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'status' => false,
    //             'message' => "Internal Error",
    //             'error' => $e->getMessage()
    //         ], 500);
    //     }
    // }
    public function cancelBooking(Request $request, $id)
    {
        try {
            $bookingDetail = PropertyBooking::where(['id' => $id])->first();
            $property  = TblHomeUnit::where('id', $bookingDetail->property_id)->first();
            if (!$property) {
                $property  = TblHomeMultiUnit::where('id', $bookingDetail->property_id)->first();
            }
            $checkinDate = date('Y-m-d', strtotime($bookingDetail->checkin_date));
            $checkoutDate = date('Y-m-d', strtotime($bookingDetail->checkout_date));
            $checkBookingCount = PropertyBooking::where('id', '!=', $bookingDetail->id)->where('checkin_date', $checkinDate)->where('checkout_date', $checkoutDate)->where('property_booking_status', 'Confirmed')->count();
            unblockPropertyAvailabilityInRu($property->ru_property_id, date('Y-m-d', strtotime($bookingDetail->checkin_date)), date('Y-m-d', strtotime($bookingDetail->checkout_date)));
            $propertyBooking = PropertyBooking::where('id', $id)->first();

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


            $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_units.*', 'tbl_home_units.unit_name as home_name']);
            if (!$property) {
                $property  = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_multi_units.*', 'tbl_home_multi_units.unit_name as home_name']);
            }
            $propertyBooking->property = $property;

            $reason = NULL;
            $close = 1;
            $homeDetail = $bookingDetail->property;
            if(isPriceLabEnable() && $homeDetail->price_lab_sync_date_time){
                $request->merge(
                    [
                        'blockedFrom' => date('Y-m-d', strtotime($bookingDetail->checkin_date)),
                        'blockedTo' => date('Y-m-d', strtotime($bookingDetail->checkout_date)),
                        'propertyId' => $homeDetail->ru_property_id,
                    ]
                );
            
                $res = $this->calendarUnblockDatesUsingBooking($request);
               
                $propertyBooking = PropertyBooking::where('id', $id)->first();
                $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_units.*', 'tbl_home_units.unit_name as home_name']);
                if(!$property){
                    $property  = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_multi_units.*', 'tbl_home_multi_units.unit_name as home_name']);
                }
                $propertyBooking->property = $property;
                $customerDetail = json_decode($propertyBooking->customer_detail);
                
                //-----------cancel booking on reservation-------------//
                $payload = $this->priceLabpayloadService->preparePriceLabsReservationCancellationPayload($propertyBooking);
                $this->priceLabService->syncReservations($payload);
            }





            $customerDetail = json_decode($propertyBooking->customer_detail);
            if ($customerDetail->email != []) {
                try {
                    Mail::to($customerDetail->email)
                        ->send(new BookingCancellationEmail([
                            'mailData' => $propertyBooking,
                            'type' => 'customer'
                        ]));
                } catch (\Exception $e) {
                }
            }
            $delete = PropertyBooking::where(['id' => $id])->update(['property_booking_status' => 'Canceled']);
            //$delete = PropertyBooking::where(['id' => $id])->delete();
            // if($bookingDetail->transcation_id && $bookingDetail->channel=='Website' || $bookingDetail->channel=='pms'){
            //     $api = new Api(config('services.razorpay.key'), config('services.razorpay.secret'));
            //     $payment = $api->payment->fetch($bookingDetail->transcation_id);
            //     $refund = $payment->refund(['amount' => ($bookingDetail->payable_amount/2)*100]);
            // }
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => "Successfully Deleted"
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage()
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
    
    public function deleteBooking($id)
    {
        try {
            $bookingDetail = PropertyBooking::where(['id' => $id])->first();
            //$property  = TblHomeUnit::where('id', $bookingDetail->property_id)->first();
            $property = DB::table('tbl_home_units')->where('id', $bookingDetail->property_id)->first();

            if (!$property) {
                //$property  = TblHomeMultiUnit::where('id', $bookingDetail->property_id)->first();
                $property = DB::table('tbl_home_multi_units')->where('id', $bookingDetail->property_id)->first();
            }
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
            $propertyBooking = PropertyBooking::where('id', $id)->first();
            $property  = TblHomeUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_units.*', 'tbl_home_units.unit_name as home_name']);
            if (!$property) {
                $property  = TblHomeMultiUnit::where('id', $propertyBooking->property_id)->first(['tbl_home_multi_units.*', 'tbl_home_multi_units.unit_name as home_name']);
            }
            $propertyBooking->property = $property;
            $customerDetail = json_decode($propertyBooking->customer_detail);
            if ($customerDetail->email != []) {


                try {
                    Mail::to($customerDetail->email)->send(new BookingCancellationEmail(array('mailData' => $propertyBooking, 'type' => 'customer')));
                } catch (\Exception $e) {
                }
            }
            //$delete = PropertyBooking::where(['id' => $id])->update(['property_booking_status', 'property_booking_status']);
            $delete = PropertyBooking::where(['id' => $id])->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Role deleted successfully'
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }


    function checkBookingDate(Request $request){
        try {
            $blockedDates = [];
            $propertyUnavailableDates = DB::table('ru_property_availabilities')->where('property_id', $request->property_id)->where('availability_date', '>', $request->checkin_date )->where('is_available', 'no')->first();
            
            if($propertyUnavailableDates){
                $nextDate = date('Y-m-d', strtotime($propertyUnavailableDates->availability_date));
                $nextDate = Carbon::parse($propertyUnavailableDates->availability_date)->addDay()->format('Y-m-d');
                $endDate = Carbon::parse($nextDate)->addYears(15)->format('Y-m-d');
                $currentDate = Carbon::parse($nextDate);
                while ($currentDate->lte($endDate)) {
                    $blockedDates[] = $currentDate->format('Y-m-d');
                    $currentDate->addDay();
                }
            
            }
            
            
            return response()->json([
                'status' => true,
                'date' => $blockedDates,
                'message' => "Successfully Deleted"
            ], 200);
        } catch (\Exception $e) {
            \Log::error('Error in checkBookingDate: ' . $e->getMessage());
            return response()->json([
                'status' => false,
                'date' => '',
                'message' => 'An error occurred: ' . $e->getMessage(),
            ], 500);
        }
    }
}
