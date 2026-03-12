<?php

namespace App\Http\Controllers\PMS\Quotation;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Role;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\BookingQuotationProperty;
use App\Models\BookingQuotation;
use App\Models\TblLocation;
use App\Models\TblGst;
use App\Models\RuPropertyPrice;  
use App\Services\PropertyService;
use App\helper\MasterHelper;
use App\Mail\BookingQuotationEmail;
use DB;
use Mail;
use Carbon\Carbon;

class QuotationController extends Controller
{


    
    protected $propertyService;

    public function __construct(){
        $this->propertyService = new PropertyService();
    }

    public function index(Request $request)
    {
       // $units = TblHomeUnit::whereNotNull('ru_property_id')->get();
       // $multiUnits = TblHomeMultiUnit::whereNotNull('ru_property_id')->get();
      //  $properties = $units->merge($multiUnits);
      
        $units = $this->propertyService->applyUserRoleFilter(
            TblHomeUnit::whereNotNull('ru_property_id')
        )->get();
        
        $multiUnits = $this->propertyService->applyUserRoleFilter(
            TblHomeMultiUnit::whereNotNull('ru_property_id')
        )->get();
        
        $properties = $units->merge($multiUnits);
      
      
    
       // $query = BookingQuotation::query();
        $query = $this->propertyService->applyUserRoleFilter(BookingQuotation::query());
        
        $query->when(isset($request->guest_name), function ($q) use ($request) {
            $q->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ['%' . $request->guest_name . '%']);
        });
    
        $query->when(isset($request->property_name), function ($q) use ($request) {
            $quotationids = BookingQuotationProperty::where('property_id', $request->property_name)
                            ->pluck('booking_quotation_id')
                            ->toArray();
            $q->whereIn('id', $quotationids);
        });
    
        // $query->when(isset($request->checkin_date) && !isset($request->checkout_date), function ($q) use ($request) {
        //     $q->where('checkin_date', $request->checkin_date);
        // });
    
        // $query->when(!isset($request->checkin_date) && isset($request->checkout_date), function ($q) use ($request) {
        //     $q->where('checkout_date', $request->checkout_date);
        // });

        // $query->when(isset($request->checkin_date) && isset($request->checkout_date), function ($q) use ($request) {
        //     $q->where('checkin_date', '<=', $request->checkin_date)
        //       ->where('checkout_date', '>=', $request->checkout_date);
        // });
        
         $query->when($request->checkin_date && $request->checkout_date, function ($q) use ($request) {
             $q->where('checkin_date', '>=', $request->checkin_date)
                ->where('checkout_date', '<=', $request->checkout_date);
        });
        
        $items = $query->with('bookingProperties')->orderBy('id', 'desc')->paginate(50)->withQueryString();
        return view('pms.quotation.list', compact('items', 'properties'));
    }
    
    public function delete($id){   
        try {
             BookingQuotation::where(['id' => $id])->delete();
             BookingQuotationProperty::where(['booking_quotation_id' => $id])->delete();
            return response()->json([
                'status' => true,
                'data' => '',
                'message' => 'Quotation deleted successfully'
            ], 200);
        }
        catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => "Internal Error",
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public function quotationCreate($id = null)
    {
        $detail = null;
        $preRenderedProperties = null;
    
        if ($id) {
            $detail = BookingQuotation::with('bookingProperties')->where('id', $id)->first();
            if ($detail) {
                $properties = [];
                foreach ($detail->bookingProperties as $property) {
                    $unitData = null;
                    if ($property->pType == 'unit') {
                        $unitData = \App\Models\TblHomeUnit::where('id', $property->property_id)->first();
                    } elseif ($property->pType == 'multiunit') {
                        $unitData = \App\Models\TblHomeMultiUnit::where('id', $property->property_id)->first();
                    }
                    $properties[] = [
                        'booking_quotation_property_id'=> $property->id,
                        'unit_name' => $property->property_name,
                        'maximum_number_of_guests' => $unitData ? $unitData->maximum_number_of_guests : 0,
                        'price_per_night' => $property->per_night_price,
                        'no_of_nights' => $detail->no_of_nights,
                        'price' => $property->payable_amount,
                        'property_id' => $property->property_id,
                        'pType' => $property->pType,
                        'extra_guest_charge' => $property->extra_guest_charge / $detail->no_of_nights,
                        'total_extra_guest_charge' => $property->extra_guest_charge,
                        'sub_total' => $property->total_amount,
                        'total_additional_charges' => $property->addon_total_amount,
                        'additionalCharge' => $property->additional_charges_detail,
                        'total_taxable_amount' => $property->taxable_amount,
                        'tax_amount' => $property->tax_amount,
                        'tax_percentage' => $property->gst,
                        'base_price' => $property->basePrice,
                        'final_price' => $property->payable_amount,
                        'max_guests' => $unitData ? $unitData->maximum_number_of_guests : 0,
                        'discount_amount' => $property->discountAmount ?? 0,
                        'discount_amount_additional' => $property->adOnsDiscountAmount ?? 0,
                        'selected' => true, 
                    ];
                }
    
                $preRenderedProperties = view('pms.quotation.property_list', [
                    'properties' => collect($properties),
                    'message' => 'Loaded saved properties.'
                ])->render();
            }
        }
    
        $locations = TblLocation::where('status', 1)->get();
    
        return view('pms.quotation.form', compact('locations', 'detail', 'preRenderedProperties'));
    }


public function ajaxSearch(Request $request)
 {
    
    $properties =  $this->propertyService->filterPropertyListByDates(['checkin_date'=>$request->checkin_date, 'checkout_date'=>$request->checkout_date]);
   // $properties = $this->propertyService->applyUserRoleFilter($properties);
    $noOfGuest = $request->no_adults + $request->no_children;
   
    if (!empty($request->input('location'))) {
        $properties = $properties->where('location_id', $request->location);
    }
    
    if (!empty($request->input('no_adults'))) {
       $properties = $properties->where('maximum_number_of_guests', '>=',$noOfGuest);
    }


$requiredFieldsOnly = [];

foreach ($properties as $property) {
    $price =  $base_price =   $base_price_with_other_charges = $property->price;
    $price_per_night = $property->per_night_price;
    $extra_guest_charge = 0;
    if($noOfGuest >$property->guests_included && $noOfGuest <= $property->maximum_number_of_guests){
        if($property->maximum_number_of_guests == $noOfGuest){
            $extra_no_of_guest = $property->maximum_number_of_guests - $property->guests_included;
        }
        else{
            $extra_no_of_guest = $property->maximum_number_of_guests - $noOfGuest;
        }
        $extra_guest_charge = $extra_no_of_guest*$property->extra_guest_charges*$property->no_of_nights;
        $price = $price + $extra_guest_charge;
        $base_price_with_other_charges = $base_price_with_other_charges + $extra_guest_charge;
    }
    $sub_total = $price;
//dd($property->additionalCharge);
    // Additional Charges
    $total_additional_charges = 0;
    if ($property->additionalCharge) {
        foreach ($property->additionalCharge as $charge) {
            $chargeAmount = ($charge->type_option == 'Per_Night')
                ? $charge->price * $property->no_of_nights
                : $charge->price;

            $total_additional_charges += $chargeAmount;
            $base_price_with_other_charges += $chargeAmount;
            $price += $chargeAmount;
        }
    }
   // dd($price);
    $total_taxable_amount = $price;
    // GST Calculation
    $tax = 0;
    $tax_amount = 0;
    $getAppliedGst = getAppliedGst($price_per_night);
   // dd($getAppliedGst);
    if ($getAppliedGst) {
        $tax = $getAppliedGst->gst_percentage;
        $tax_amount = ($price * $tax) / 100;
        $price += $tax_amount;
    }

    // Collect required fields only
    $requiredFieldsOnly[] = [
        'unit_name' => $property->unit_name,
        'website_markup_price' => $property->website_markup_price,
        'maximum_number_of_guests' => $property->maximum_number_of_guests,
        'price_per_night' => round($price_per_night),
        'no_of_nights' => $property->no_of_nights,
        'price' => round($price),
        'property_id' => $property->id,
        'pType' => $property->pType,
        'extra_guest_charge' => round($property->extra_guest_charges),
        'total_extra_guest_charge' => round($extra_guest_charge),
        'sub_total' => round($sub_total),
        'total_additional_charges' => round($total_additional_charges),
        'additionalCharge' => $property->additionalCharge,
        'total_taxable_amount' => round($total_taxable_amount),

        'tax_amount' => round($tax_amount),
        'tax_percentage' => $tax,
        'base_price' => round($base_price),
        'final_price' => round($price),
        'max_guests' => $property->maximum_number_of_guests,
        
    ];
}

  // dd($requiredFieldsOnly);
    
        $html = view('pms.quotation.property_list', [
            'properties' => collect($requiredFieldsOnly),
            'message' => 'Listed successfully.'
        ])->render();
        return response()->json(['html' => $html]);
 }


 public function saveBookingQuation(Request $request)
{
  //  dd($request);
    $validated = $request->validate([
        'email_address' => 'required',
        'mobile_number' => 'required',
        'first_name' => 'required',
        'last_name' => 'required',
        'validity' => 'required|in:1,24,48,72',
        'location' => 'required',
        'checkin_date' => 'required',
        'checkout_date' => 'required',
        'no_adults' => 'required',
        'no_children' => 'nullable',
    ]);

    try {
        $checkInDate = date('Y-m-d', strtotime($request->checkin_date));
        $checkOutDate = date('Y-m-d', strtotime($request->checkout_date));
        
       // $checkInDate = $request->checkin_date ? Carbon::createFromFormat('d/m/Y', $request->checkin_date)->format('Y-m-d') : null;
       // $checkOutDate = $request->checkout_date ? Carbon::createFromFormat('d/m/Y', $request->checkout_date)->format('Y-m-d') : null;

        
        $last_date = date('Y-m-d', strtotime($checkOutDate . ' -1 days'));
        $noOfNights = ($last_date == $checkInDate) ? 1 : MasterHelper::getDateDifference($checkInDate, $checkOutDate);
        $noOfNights = (int) $noOfNights;

        $user = Auth::guard('admin')->user();

        $quotationDetail = BookingQuotation::firstOrNew(['id' => $request->id]);
        $quotationDetail->location_id = $request->location;
        $quotationDetail->first_name = $request->first_name;
        $quotationDetail->user_id = $user->id;
        $quotationDetail->parent_user_id = ($user->role_id == 7) ? $user->id : $user->parent_user_id;
        $quotationDetail->last_name = $request->last_name;
        $quotationDetail->country_code = $request->country_code;
        $quotationDetail->mobile_number = $request->mobile_number;
        $quotationDetail->email = $request->email_address;
        $quotationDetail->checkin_date = $checkInDate;
        $quotationDetail->checkout_date = $checkOutDate;
        $quotationDetail->no_of_nights = $noOfNights;
        $quotationDetail->no_adults = $request->no_adults ?: 1;
        $quotationDetail->no_children = $request->no_children ?: 0;
        $quotationDetail->guest_included_count = $request->no_adults ?: 1;
        $quotationDetail->validity = $request->validity;
        $quotationDetail->booking_status = 'Not Booked';
        $quotationDetail->save();

        $properties = json_decode($request->property_list, true);
        if (!empty($properties)) {
            // Purane properties delete karo
            BookingQuotationProperty::where('booking_quotation_id', $quotationDetail->id)->delete();

            foreach ($properties as $quotation_property) {
                $quotationPropertyDetail = new BookingQuotationProperty();
                $quotationPropertyDetail->booking_quotation_id = $quotationDetail->id;
                $quotationPropertyDetail->property_id = $quotation_property['id'];
                $quotationPropertyDetail->pType = $quotation_property['ptype'];
                $quotationPropertyDetail->property_name = $quotation_property['propertyName'];
                $quotationPropertyDetail->price = $quotation_property['price'];
                $quotationPropertyDetail->total_amount = $quotation_property['totalAmount'];
                $quotationPropertyDetail->website_markup_price = $quotation_property['website_markup_price'];
                $quotationPropertyDetail->extra_guest_charge = $quotation_property['extraGuestCharge'];
                $quotationPropertyDetail->tax_amount = $quotation_property['taxAmount'];
                $quotationPropertyDetail->taxable_amount = $quotation_property['totalTaxableAmount'];
                $quotationPropertyDetail->addon_total_amount = $quotation_property['addOnsTotalAmount'];
                $quotationPropertyDetail->addon_discount_amount = $quotation_property['addOnsDiscountTotalAmount'];
                $quotationPropertyDetail->additional_charges_detail = $quotation_property['additionalCharges'];
                $quotationPropertyDetail->gst = $quotation_property['tax'];
                $quotationPropertyDetail->gst_amount = $quotation_property['taxAmount'];
                $quotationPropertyDetail->payable_amount = $quotation_property['totalPayableAmount'];
                $quotationPropertyDetail->per_night_price = $quotation_property['perNightPrice'];
                $quotationPropertyDetail->basePrice = $quotation_property['basePrice'];
                $quotationPropertyDetail->discountAmount = $quotation_property['discountAmount'] ?? 0;
                $quotationPropertyDetail->adOnsDiscountAmount = $quotation_property['adOnsDiscountAmount'] ?? 0;
                $quotationPropertyDetail->booking_status = 'Not Booked';
                $quotationPropertyDetail->save();
            }
        }
         $mailDetail = Mail::to($request->email_address)->send(new BookingQuotationEmail($quotationDetail));
         $quotationDetail->is_email_sent = 1;
        $quotationDetail->save();

        return response()->json([
            'status' => true,
            'data' => '',
            'message' => 'Booking Quotation ' . ($request->id ? 'Updated' : 'Generated') . ' Successfully.'
        ], 200);
    } catch (Exception $e) {
        return response()->json([
            'status' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
    
}