<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AboutUs;
use App\Models\Team;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\TblHome;
use App\Models\TblTestimonial;
use App\Models\RuPropertyAvailability;
use Illuminate\Support\Facades\Validator;
use App\helper\MasterHelper;
use App\Models\TblHomeUnit;
use App\Models\Service;
use App\Models\TblHomeMultiUnit;
use App\Models\BookingQuotationProperty;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;
use App\Models\PropertyBooking;
use App\Services\PropertyService;
use App\Services\PropertyFetureData;
use App\Models\TblTermsandCondition;
use App\Models\BookingQuotation;
use App\Http\Controllers\MinStayController;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class BookingQuotationController extends Controller
{

    protected $propertyFeatureData;
    protected $propertyService;

    public function __construct(PropertyFetureData $propertyFeatureData)
    {
        $this->propertyFeatureData = $propertyFeatureData;
        $this->propertyService = new PropertyService();
    }





    public function bookingQuotationDetail(Request $request, $id)
    {
        try {
            $id = base64_decode($id);
            $quotationDetail = BookingQuotation::with(['bookingProperty', 'location'])->where('id', $id)->first();
            if (!$quotationDetail) {
                abort(404);
            }

            $bookingProperties = $quotationDetail->bookingProperty;
            $unitPropertyIds = $bookingProperties->where('pType', 'unit')->pluck('property_id');
            $multiUnitPropertyIds = $bookingProperties->where('pType', 'multiunit')->pluck('property_id');

            $units = TblHomeUnit::with(['imagesWebsite', 'locationData'])
                ->whereIn('id', $unitPropertyIds)
                ->whereNotNull('ru_property_id')
                ->get()
                ->keyBy('id');

            $multiUnits = TblHomeMultiUnit::with(['imagesWebsite', 'locationData'])
                ->whereIn('id', $multiUnitPropertyIds)
                ->whereNotNull('ru_property_id')
                ->get()
                ->keyBy('id');

            $quotationProperty = $bookingProperties->map(function ($property) use ($units, $multiUnits) {
                if ($property->pType === 'unit' && isset($units[$property->property_id])) {
                    $property->property_details = $units[$property->property_id];
                } elseif ($property->pType === 'multiunit' && isset($multiUnits[$property->property_id])) {
                    $property->property_details = $multiUnits[$property->property_id];
                } else {
                    $property->property_details = null;
                }
                return $property;
            });
            if ($quotationDetail->count() > 0) {
                if ($quotationDetail->is_link_expired  || $quotationDetail->booking_status != 'Not Booked') {
                    abort(404);
                }
                $guestOptions = guestOptions();
                //dd($quotationProperty, $quotationDetail);
                return view('website.quotation.quotation-list', compact('quotationDetail', 'quotationProperty', 'guestOptions'));
            } else {
                abort(404);
            }
        } catch (Exception $e) {
            abort(404);
        }
    }



    public function quotationPropertyDetail($ptype, $slug, Request $request)
    {

        $id = base64_decode($slug);
        $ptype = $ptype;

        $bookingQuotationProperty = BookingQuotationProperty::with(['bookingQuotationDetail'])->where('id', $id)->where('pType', $ptype)->first();

        // dd($bookingQuotationProperty);
        if ($bookingQuotationProperty) {
            $property = ($bookingQuotationProperty->pType === 'unit')
                ? TblHomeUnit::with(['imagesWebsite', 'tags', 'locationData', 'ImportantInformation.iconImage','homefaqSection', 'websiteamenities'])->where('id', $bookingQuotationProperty->property_id)->first()
                : TblHomeMultiUnit::with(['imagesWebsite', 'tags', 'locationData', 'ImportantInformation.iconImage','homefaqSection', 'websiteamenities'])->where('id', $bookingQuotationProperty->property_id)->first();


            $propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();
            $previouslyBookedCheckoutDates = PropertyBooking::where('property_id', $property->id)->where('checkout_date', '>=', date('Y-m-d'))->where('property_booking_status', 'Confirmed')->pluck('checkout_date')->toArray();
            $minStayController = new MinStayController();
            $minStayArray = $minStayController->syncMinStayFromToWeb(date('Y-m-d'), $property->id, $property->pType);


            $propertyFeatureData = $this->propertyFeatureData->mount($property->id, $property->ptype);

            $servicesData = Service::first();
             
            $importantInformation = [
                'House Rules' => $property->ImportantInformation->where('type_option', 'house_rules'),
                'Safety & Property' => $property->ImportantInformation->where('type_option', 'safety_property'),
                'Cancellation Policy' => $property->ImportantInformation->where('type_option', 'cancellation_policy'),
            ];
            $iconMap = [
                'House Rules' => 'icon-house-rules',
                'Safety & Property' => 'icon-home',
                'Cancellation Policy' => 'icon-cancellation-policy',
            ];


            return view('website.quotation.booking-quotation-property-detail', compact('bookingQuotationProperty','importantInformation','iconMap','servicesData',  'property', 'propertyFeatureData', 'propertyUnavailableDates', 'minStayArray', 'previouslyBookedCheckoutDates'));
        } else {
            abort(404);
        }
    }


    public function bookProperty($ptype, $slug, Request $request)
    {
        $id = base64_decode($slug);
        $ptype = $ptype;
        $bookingQuotationProperty = BookingQuotationProperty::with(['bookingQuotationDetail'])->where('id', $id)->where('pType', $ptype)->first();
        if ($bookingQuotationProperty) {
            $propertyDetail = ($bookingQuotationProperty->pType === 'unit')
                ? TblHomeUnit::with(['imagesWebsite', 'tags', 'ImportantInformation.iconImage','homefaqSection', 'locationData', 'websiteamenities'])->where('id', $bookingQuotationProperty->property_id)->first()
                : TblHomeMultiUnit::with(['imagesWebsite',  'tags','ImportantInformation.iconImage','homefaqSection', 'locationData', 'homeReviews', 'websiteamenities'])->where('id', $bookingQuotationProperty->property_id)->first();

            $totalReviews = $propertyDetail->homeReviews->count();
            //  dd($propertyDetail);
            $termsandCondition = TblTermsandCondition::first();

              

            $importantInformation = [
                'HouseRules' => $propertyDetail->ImportantInformation->where('type_option', 'house_rules'),
                'SafetyProperty' => $propertyDetail->ImportantInformation->where('type_option', 'safety_property'),
                'CancellationPolicy' => $propertyDetail->ImportantInformation->where('type_option', 'cancellation_policy'),
            ];
                
                $HouseRules = $propertyDetail->ImportantInformation->where('type_option', 'house_rules');
                $CancellationPolicy = $propertyDetail->ImportantInformation->where('type_option', 'cancellation_policy');

            $userData = User::where('id', Auth::guard('webusers')->id())->first();

            



            return view('website.quotation.quotation-property-booking', compact('bookingQuotationProperty', 'importantInformation', 'HouseRules', 'CancellationPolicy', 'termsandCondition', 'propertyDetail', 'totalReviews','userData'));
        } else {
            abort(404);
        }
    }





    public function customerPropertyBookFormPost(Request $request)
    {
        $rules = array();
        $rules['first_name'] = 'required';
        $rules['last_name'] = 'required';
        $rules['phone_number'] = 'required';
        $rules['email'] = 'required';
        $rules['state'] = 'required';
        $rules['city'] = 'required';
        if (isset($request->company_info)) {
            $rules['company_name'] = 'required';
            $rules['gst_no'] = 'required';
            $rules['company_state'] = 'required';
            $rules['company_city'] = 'required';
        }
        if (!$request->consent) {
            $rules['consent'] = 'required';
        }
        if (!$request->check_terms) {
            $rules['check_terms'] = 'required';
        }
        if (!$request->check_home_rules) {
            $rules['check_home_rules'] = 'required';
        }
        $validated = $request->validate($rules);
        $property = BookingQuotationProperty::with(['bookingQuotationDetail', 'propertyDetail'])->where('id', $request->id)->first();
        $bookingArray = array();
        $propertyBooking = (object)$bookingArray;
        $propertyBooking->location_id = $property->propertyDetail->location_id;
        $propertyBooking->property_id = $property->property_id;
        $propertyBooking->total_amount = $property->total_amount;
        $propertyBooking->payable_amount = round($property->payable_amount);
        $bookingInfo = json_decode($request->bookingInfo, true);
        if ($property->additional_charges_detail) {
            $propertyBooking->tot_additional_charge = $property->addon_total_amount;
            $propertyBooking->additional_charges_discount = $property->addon_discount_amount;
            $propertyBooking->additional_charges_detail = json_encode($property->additional_charges_detail);
        }
        $propertyBooking->tax_amount = $property->tax_amount;
        $propertyBooking->booking_id = rand(10000000, 99999999);
        $propertyBooking->booking_status = 'pending';
        $propertyBooking->booking_created_by = 'admin';
        $propertyBooking->type = 'location';
        $propertyBooking->no_of_children = $property->bookingQuotationDetail->no_children;
        $propertyBooking->no_of_adult = $property->bookingQuotationDetail->no_adults;
        $propertyBooking->customer_detail = json_encode(array('first_name' => $request->first_name, 'last_name' => $request->last_name, 'email' => $request->email, 'mobile_number' => $request->phone_number));
        $propertyBooking->customer_location_detail = json_encode(array('state' => $request->state, 'city' => $request->city, 'addresss' => $request->addresss));
        $propertyBooking->is_company_info = $request->company_info == 'checked' ? 1 : 0;
        $propertyBooking->customer_company_info = $request->company_info == 'checked' ? json_encode(array('company_name' => $request->company_name, 'gst_no' => $request->gst_no, 'company_state' => $request->company_state, 'company_city' => $request->company_city, 'company_address' => $request->company_address)) : NULL;
        $propertyBooking->checkin_date = date('Y-m-d', strtotime($property->bookingQuotationDetail->checkin_date));
        $propertyBooking->checkout_date = date('Y-m-d', strtotime($property->bookingQuotationDetail->checkout_date));
        $propertyBooking->is_blocking_hour = 0;
        $propertyBooking->channel = 'Website';
        $propertyBooking->per_night_price = $property->per_night_price;
        $propertyBooking->no_of_nights = $property->bookingQuotationDetail->no_of_nights;
        $propertyBooking->tax = $property->gst;
        $propertyBooking->base_price = $property->basePrice;
        $propertyBooking->taxable_amount = $property->taxable_amount;
        $propertyBooking->property_booking_status = 'Confirmed';
        $propertyBooking->payment_status = 'Paid';
        try {
            session(['cart' => $propertyBooking]);
            BookingQuotation::where('id', $property->booking_quotation_id)->update(['booking_status' => 'Booked']);
            return response()->json([
                'status' => true,

                'message' => 'Listed successfully.'
            ], 200);
        } catch (Exception $e) {
            dd($e->getMessage());
        }
    }
}