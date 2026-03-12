<?php

namespace App\Http\Controllers\Website;

use App\Http\Controllers\Controller;
use App\Models\TblHomeBanner;
use App\Models\TblLocation;
use App\Models\TblArea;
use App\Models\TblCollection;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\TblHomeReview;
use App\Models\TblBlog;
use App\Models\TblFaqCategory;
use App\Models\TblFaq;
use App\Services\PropertyService;
use App\Models\TblTermsandCondition;
use App\Models\TblPrivacyPolicy;
use App\Models\TblSpecialInvitation;
use Illuminate\Http\Request;
use DB;
use App\Http\Controllers\MinStayController;
use App\helper\MasterHelper;
use App\Models\PropertyBooking;
use App\Models\RuPropertyPrice;
use App\Models\FeaturedPropertySetting;
use App\Models\TblUnitMultiunit;
use App\Models\TblHomeCollection;
use App\Models\TblHomeType;
use App\Models\BookingEnquiry;
use Illuminate\Support\Facades\Validator;
use App\Services\PropertyFetureData;
use App\Models\CancellationPolicy;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\DiscountCoupon;
use App\Models\Service;
use App\Models\TblLanding;
use Carbon\Carbon;
use App\Models\TblTag;
use App\Models\TBGuest;
use App\Mail\landingpageenquiery;
use App\Mail\landingdynamicpage;
use Illuminate\Support\Facades\Mail;
use Barryvdh\DomPDF\Facade\Pdf;
use Dompdf\Dompdf;
use Dompdf\Options;

class HomeController extends Controller
{

    protected $propertyFeatureData;
    protected $propertyService;

    public function __construct(PropertyFetureData $propertyFeatureData)
    {
        $this->propertyFeatureData = $propertyFeatureData;
        $this->propertyService = new PropertyService();
    }

    public function index()
    {
        $banner_data = TblHomeBanner::orderBy('position', 'asc')->select('tbl_home_banners.*', 'image as image_path', 'mobile_image as mobile_image_path', DB::raw('CONCAT("/storage/home_banner/",image) as image'), DB::raw('CONCAT("/storage/home_banner/",mobile_image) as mobile_image'))->get();
        $locations = TblLocation::with(['properties'])->where('status', 1)->where('show_on_location_page', 1)
            ->whereHas('properties')
            ->get();

        $units = TblHomeUnit::where('show_on_home', 1)->with(['imagesWebsite', 'tags', 'homeReviews', 'locationData'])->where('status', 1)->whereNotNull('ru_property_id')
            ->get()
            ->map(function ($unit) {
                $unit->pType = 'unit';
                return $unit;
            });

        $multiUnits = TblHomeMultiUnit::where('show_on_home', 1)->with(['imagesWebsite', 'tags', 'homeReviews', 'locationData'])->where('status', 1)->whereNotNull('ru_property_id')
            ->get()
            ->map(function ($multiUnit) {
                $multiUnit->pType = 'multiunit';
                return $multiUnit;
            });
        $allList = $units->concat($multiUnits)->values();
        $onTopPropertyArray = $allList;
        $tag_data = TblTag::get();
        return view('website.index', compact('tag_data', 'banner_data', 'locations', 'onTopPropertyArray'));
    }



    public function landing(Request $request, $slug)
    {
        $detail = DB::table('tbl_landing')->where('status', 1)->whereNull('deleted_at')->where('slug', $slug)->first();

        if (!$detail) {
            abort(404);
        }

        $homeTypeIds = [];
        if (!empty($detail->property_type_id)) {
            if (is_string($detail->property_type_id)) {
                $decoded = json_decode($detail->property_type_id, true);
                $homeTypeIds = is_array($decoded) ? $decoded : explode(',', $detail->property_type_id);
            } else {
                $homeTypeIds = (array) $detail->property_type_id;
            }
        }

        $propertyIds = [];
        if (!empty($detail->property_id)) {
            if (is_string($detail->property_id)) {
                $decoded = json_decode($detail->property_id, true);
                $propertyIds = is_array($decoded) ? $decoded : explode(',', $detail->property_id);
            } else {
                $propertyIds = (array) $detail->property_id;
            }
        }


        // $unitQuery = TblHomeUnit::query()->with('homeReviews')
        //     ->whereNotNull('ru_property_id');
        // $multiUnitQuery = TblHomeMultiUnit::query()->with('homeReviews')
        //     ->whereNotNull('ru_property_id');
        // if (!empty($homeTypeIds)) {
        //     $unitQuery->whereIn('home_type_id', $homeTypeIds);
        //     $multiUnitQuery->whereIn('home_type_id', $homeTypeIds);
        // }
        // if (!empty($propertyIds)) {
        //     $unitQuery->whereIn('id', $propertyIds);
        //     $multiUnitQuery->whereIn('id', $propertyIds);
        // }
        // $units = $unitQuery->get();
        // $multiUnits = $multiUnitQuery->get();
        // $combinedPropertyData = $units->merge($multiUnits)->values();
        $units = collect();
        $multiUnits = collect();

        if (!empty($homeTypeIds) && !empty($propertyIds)) {
            $unitQuery = TblHomeUnit::query()->with('homeReviews')->where('ru_status', 1)
                ->whereNotNull('ru_property_id');
            $multiUnitQuery = TblHomeMultiUnit::query()->with('homeReviews')->where('ru_status', 1)
                ->whereNotNull('ru_property_id');

            if (!empty($homeTypeIds)) {
                $unitQuery->whereIn('home_type_id', $homeTypeIds);
                $multiUnitQuery->whereIn('home_type_id', $homeTypeIds);
            }

            if (!empty($propertyIds)) {
                $unitQuery->whereIn('id', $propertyIds);
                $multiUnitQuery->whereIn('id', $propertyIds);
            }

            $units = $unitQuery->get();
            $multiUnits = $multiUnitQuery->get();
        }

        $combinedPropertyData = $units->merge($multiUnits)->values();


        $services = Service::all();
        $collection_data = TblCollection::where('status', 1)->where('show_on_collection_page', 1)->get();

        $unitReviews = $units->pluck('homeReviews')->flatten();
        $multiUnitReviews = $multiUnits->pluck('homeReviews')->flatten();
        $reviews = $unitReviews->merge($multiUnitReviews);

        return view('website.landing', compact('detail', 'reviews', 'collection_data', 'combinedPropertyData', 'services'));
    }


    public function landingenquireSave(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'phone' => 'required|digits_between:6,12',
            'email' => 'required|string|email|max:255',
            'check-in' => 'required',
            'check-out' => 'required',
            'no-of-people' => 'required|not_in:0',
            'budget' => 'required',
            'captcha' => 'required|string',
        ]);

        if (strtoupper($request->captcha) !== strtoupper($request->captcha_hidden)) {
            return response()->json([
                'success' => false,
                'errors' => ['captcha' => 'Captcha does not match.']
            ], 422);
        }

        $admin = "support@unrealestate.in";
        $bccRecipients = ['support@unrealestate.in'];
        Mail::to($admin)
            ->bcc($bccRecipients)
            ->send(new landingdynamicpage($request->all()));
        return response()->json([
            'success' => true,
            'message' => 'Thank you for submitting your enquiry! We’ve received your details and our team will review your submission shortly.'
        ]);
    }

    public function enquireSave(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'phone_number' => 'required|digits_between:6,12',
            'country_code' => 'required',
            'email' => 'required|string|email|max:255',
            'message' => 'required',
        ]);
        if ($validator->passes()) {



            if ($request->pType == 'unit') {
                $property = TblHomeUnit::where('id', $request->pId)->first();
            } else {
                $property = TblHomeMultiUnit::where('id', $request->pId)->first();
            }

            $enquiryDetail = array();
            $enquiryDetail['location_id'] = $property->location_id;
            $enquiryDetail['property_id'] = $property->id;
            $enquiryDetail['no_of_guest'] = $request->guest;
            $enquiryDetail['no_of_night'] = $request->tot_no_of_days;
            $enquiryDetail['name'] = $request->name;
            $enquiryDetail['email'] = $request->email;
            $enquiryDetail['pType'] = $request->pType;
            $enquiryDetail['phone_no'] = $request->phone_number;
            $enquiryDetail['enquiry_message'] = $request->message;
            $enquiryDetail['property_name'] = $property->unit_name;
            $enquiryDetail['location_name'] = $property->location;
            $enquiryDetail['state_name'] = $property->state;
            $enquiryDetail['country_code'] = $request->country_code;
            $enquiryDetail['total_amount'] = (int)(str_replace(",", "", $request->total_price_multiple));
            $enquiryDetail['checkin_date'] = $request->checkInDate;
            $enquiryDetail['checkout_date'] = $request->checkOutDate;
            BookingEnquiry::create($enquiryDetail);
            DB::table('tbl_leads')->insert(['name' => $request->name, 'date' => date('Y-m-d'), 'email' => $request->email, 'mobile' => $request->phone_number, 'stage' => 'Hot']);
            $admin = "support@unrealestate.in";
            // $admin="sakibansari904526@gmail.com";
            $bccRecipients = [];
            Mail::to($admin)->bcc($bccRecipients)->send(new landingpageenquiery($request->all()));
            // Mail::to($request->email)->send(new landingpageenquiery($request->all()));

            DB::table('booking_guest_ids')->insert(['name' => $request->name, 'email' => $request->email, 'mobile_no' => $request->phone]);
            return response()->json([
                'status' => true,
                'message' => "Enquiry Successfully",
            ]);
        } else {
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
            ]);
        }
    }

    // public function downloadBrochure($id, $pType)
    // {
    //     $property = ($pType === 'unit')
    //                 ? TblHomeUnit::with('imagesWebsite', 'locationData')->find($id)
    //                 : TblHomeMultiUnit::with('imagesWebsite', 'locationData')->find($id);

    //     if (!$property) {
    //         abort(404, 'Property not found.');
    //     }

    //     $pdf = Pdf::loadView('website.pdf.brochure', ['property' => $property]);

    //     return $pdf->stream('property-brochure.pdf');
    //     return $pdf->download('property-brochure.pdf');
    // }

    public function downloadBrochure($id, $pType)
    {
        // ini_set('max_execution_time', 300);
        $property = ($pType === 'unit')
            ? TblHomeUnit::with('imagesWebsite', 'websiteamenities', 'homeReviews', 'locationData')->find($id)
            : TblHomeMultiUnit::with('imagesWebsite', 'websiteamenities', 'homeReviews', 'locationData')->find($id);

        if (!$property) {
            abort(404, 'Property not found.');
        }

        $pdf = Pdf::loadView('website.pdf.brochure', ['property' => $property])
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'dpi' => 150,
                'defaultFont' => 'DM Sans',
                'isRemoteEnabled' => true, // ⭐ Important for external images/fonts
                'isHtml5ParserEnabled' => true,
                'isFontSubsettingEnabled' => true,
            ]);

        /// return $pdf->stream('property-brochure.pdf');
        return $pdf->download('property-brochure.pdf');
    }


     public function propertyList(Request $request, $slug = null)
    {

        if ($request->input('check_in') != '') {
            $properties = $allProperties = $this->propertyService->websitePropertyListByDates([
                'checkin_date' => $request->input('check_in'),
                'checkout_date' => $request->input('check_out'),
            ]);
        } else {
            $properties = $allProperties = $this->propertyService->list([
                'checkin_date' => $request->input('check_in'),
                'checkout_date' => $request->input('check_out'),
            ]);
        }
        $no_of_guests = $request->adults + $request->children;

        $areas = [];
        $loc_data = null;
        $type_show_heading = $request->input('type');
        if (!empty($request->input('location'))) {
            $locationInput = explode(',', $request->input('location'));
            if (is_array($locationInput)) {
                $locationIds = TblLocation::whereIn('location_name', $locationInput)->pluck('id');
                $properties = $properties->whereIn('location_id', $locationIds);


                $loc_data = TblLocation::where('location_name', $request->input('location'))->first();
                $areas = TblArea::where('location_id', $loc_data->id)->get();
                $type_show_heading = 'location';
                //$selectedAreas = explode(',', $request->input('areas'));
                // $areasId = TblArea::whereIn('area_name', $selectedAreas)->get()->pluck('id')->toArray();




            }
        }

        if ($request->has('areas')) {
            $selectedAreas = explode(',', $request->input('areas'));
            $areasId = TblArea::whereIn('area_name', $selectedAreas)->get()->pluck('id')->toArray();
            $properties = $properties->whereIn('area_id', $areasId);
        }

        if (!empty($no_of_guests)) {
            $properties = $properties->where('maximum_number_of_guests', '>=', $no_of_guests);
        }




        if (request()->has('tags') && !empty(request()->input('tags'))) {
            $selectedTags = request()->input('tags');
            //dd($selectedTags);
            $properties = $properties->filter(function ($property) use ($selectedTags) {
                $tags = $property->tags;
                $matchingAmenities = $tags->filter(function ($tag) use ($selectedTags) {
                    return in_array($tag->tags_id, $selectedTags);
                });
                return $matchingAmenities->isNotEmpty();
            });
        }



        $totalStays = count($properties);
        


        $perPage = $request->input('limit', 24);
        $page = $request->input('page', 1);
        $offset = ($page - 1) * $perPage;
        $pagedData = $properties->slice($offset, $perPage)->values();


        /* if (count($pagedData) == 0 && $request->input('available') != 'other_regions') {
            $properties = $this->propertyService->list([
                'checkin_date' => null,
                'checkout_date' => null,
                // ])->take(5);
            ]);
            $areas = [];
            $loc_data = null;
            $type_show_heading = '';
            if (!empty($request->input('location'))) {
                $locationInput = explode(',', $request->input('location'));
                if (is_array($locationInput)) {
                    $locationIds = TblLocation::whereIn('slug_name', $locationInput)->pluck('id');
                    $properties = $properties->whereIn('location_id', $locationIds);

                    $loc_data = TblLocation::where('slug_name', $request->input('location'))->first();
                    $areas = TblArea::where('location_id', $loc_data->id)->get();
                    $type_show_heading = 'location';
                }
            }
            if ($request->has('areas')) {
                $selectedAreas = explode(',', $request->input('areas'));
                $areasId = TblArea::whereIn('area_name', $selectedAreas)->get()->pluck('id')->toArray();
                $properties = $properties->whereIn('area_id', $areasId);
            }



            if (!empty($no_of_guests)) {
                $properties = $properties->where('maximum_number_of_guests', '>=', $no_of_guests);
            }

            if (request()->has('tags') && !empty(request()->input('tags'))) {
                $selectedTags = request()->input('tags');
                //dd($selectedTags);
                $properties = $properties->filter(function ($property) use ($selectedTags) {
                    $tags = $property->tags;
                    $matchingAmenities = $tags->filter(function ($tag) use ($selectedTags) {
                        return in_array($tag->tags_id, $selectedTags);
                    });
                    return $matchingAmenities->isNotEmpty();
                });
            }



            $totalStays = count($properties);
            $propertyCount = $totalStays;

            $properties = new LengthAwarePaginator(
                $properties,
                $totalStays,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $similar_text = 'These dates are fully booked. Below are similar vacation homes available in other dates';
            $next_available = "other_region_show";
        } else {
            $totalStays = count($properties);
            $properties = new LengthAwarePaginator(
                $pagedData,
                $totalStays,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $propertyCount = count($properties);
            $similar_text = '';
            $next_available = "";
        } */

        $totalStays = count($properties);
            $properties = new LengthAwarePaginator(
                $pagedData,
                $totalStays,
                $perPage,
                $page,
                ['path' => $request->url(), 'query' => $request->query()]
            );
            $propertyCount = count($properties);
            $similar_text = '';
            $next_available = "";


        $tag_data = TblTag::get();


        /*   if ($request->ajax()) {
            return response()->json([
                'html' => view('website.properties.ajax-filter-property', compact('properties','propertyCount', 'next_available','similar_text', 'totalStays', 'collection_data'))->render(),
                'totalStays' => $totalStays,
                'propertyCount' => $propertyCount,
                'similar_text' => $similar_text,
            ]);
        } */
        //dd($properties);
        //dd($areas);
        return view('website.properties.property-list', compact('properties', 'tag_data', 'areas', 'loc_data', 'type_show_heading', 'next_available', 'similar_text', 'totalStays', 'propertyCount'));
    }


    public function blogs()
    {
        $query = TblBlog::query();
        $blogs = $query->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
        return view('website.blog.blog', compact('blogs'));
    }
    public function blogDetail($slug = null)
    {
        $detail = TblBlog::where('slug', $slug)->firstOrFail();
        return view('website.blog.blog-details', compact('detail'));
    }
    public function faq()
    {
        $categories = TblFaqCategory::with(['faqs' => function ($query) {
            $query->where('status', 1); // Only active FAQs
        }])->get();

        return view('website.faq', compact('categories'));
    }




    public function coupen()
    {
        $specialOffers = TblSpecialInvitation::with('couponCode')
            ->where('status', 1)
            ->orderBy('position', 'asc')
            ->get();

        // Add image path for each offer
        foreach ($specialOffers as $offer) {
            $offer->image_path = $offer->image ? '/storage/specialoffer/' . $offer->image : '';
        }
        return view('website.coupens', compact('specialOffers'));
    }
    public function refundPolicy()
    {
        $terms = CancellationPolicy::first();
        return view('website.cancellation_and_refund', compact('terms'));
    }

    public function propertyDetail(Request $request, $ptype, $slug)
    {
        //return view('website.coming_soon');
        $checkinDate = "";
        $checkOutDate = "";
        // if($request->input('check_in') && $request->input('check_out')){
        //     $checkinDate = $request->input('check_in');
        //     $checkOutDate = $request->input('check_out');
        // }
        if ($request->input('next_available') == 'other_region_show') {
            $checkinDate = "";
            $checkOutDate = "";
        } else {
            if ($request->input('check_in') && $request->input('check_out')) {
                $checkinDate = $request->input('check_in');
                $checkOutDate = $request->input('check_out');
            }
        }
        $property =  $this->propertyService->getPropertyDetailBySlug(['checkin_date' => $checkinDate, 'checkout_date' => $checkOutDate, 'pType' => $ptype, 'slug' => $slug]);
        if (!$property) {
            abort(404);
        }
        //  dd($property);
        $propertyId = $property->id;
        $queryDiscountCoupon = DiscountCoupon::where('user_type', 'multiple')->where('generated_coupon_code_by', 'self')->where('status', 1)->get()
            ->filter(function ($coupon) use ($propertyId) {
                $propertyIds = $coupon->property_id;
                if (empty($propertyIds)) {
                    $isApplicable = true;
                } else {
                    $isApplicable = in_array($propertyId, $propertyIds);
                }
                if ($isApplicable) {
                    $today = Carbon::today();
                    $startDate = Carbon::parse($coupon->start_date);
                    $endDate = Carbon::parse($coupon->end_date);
                    $isDateValid = $today->between($startDate, $endDate);
                    if ($isDateValid) {
                        $usedCount = PropertyBooking::where('applied_discount_coupon', $coupon->code)
                            ->where('property_booking_status', 'Confirmed')
                            ->count();
                        return $usedCount < $coupon->use_limit;
                    }
                }
                return false;
            })->values();
        $propertyFeatureData = $this->propertyFeatureData->mount($property->id, $ptype);
        //$propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();
        $ruPropertyIdArray = array($property->id);
        if ($property->pType == 'unit') {
            $munitid = TblUnitMultiunit::where('unit_id', $property->id)->get()->pluck('multiunit_id')->toArray();
            $mruids =  TblHomeMultiUnit::whereIn('id', $munitid)->whereNotNull('ru_property_id')->get();
            foreach ($mruids as $val) {
                $ruPropertyIdArray[] = $val->id;
            }
        } else {
            $home = TblHomeMultiUnit::where('id', $property->id)->first();
            $munitid = TblUnitMultiunit::where('multiunit_id', $home->id)->get()->pluck('unit_id')->toArray();
            $mruids =  TblHomeUnit::whereIn('id', $munitid)->get();
            foreach ($mruids as $val) {
                $ruPropertyIdArray[] = $val->id;
            }
        }
        $propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();
        $adult = 1;
        $child = 0;
        if ($request->has('adults')) {
            $adult = $request->adults;
        }
        if ($request->has('children')) {
            $child = $request->children;
        }
        $guestCount = $adult;
        $totGuest = $adult;
        $noOfGuest = $totGuest;
        $reqParams = $request->all();
        $minStayController = new MinStayController();
        $minStayArray = $minStayController->syncMinStayFromToWeb(date('Y-m-d'), $property->id, $property->pType);
        //$totalReviews = $property->homeReviews->count();
        //  $servicesData = Service::first();
        // dd($property);
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
        //dd($importantInformation);
        //dd($property);
        $bookingDetail = NULL;
        $city_slug_name = $request->children;
        $checkin_date = $request->check_in;
        $checkout_date = $request->check_out;
        $city_id = $request->city_id;
        $tot_guest = $request->tot_guest;
        $petsCount = $request->petsCount;
        $pet = $request->petsCount;
        $all_total_guests = $request->all_total_guests;
        // $guest_data = TBGuest::get();
        $guest_data = TBGuest::get();
        $totGuest = 1;
        foreach ($guest_data as $gkey => $guest) {
            if ($gkey == 0) {
                $guest_data[$gkey]['count'] = $adult;
                $totGuest = $totGuest + $adult;
            } elseif ($gkey == 1) {
                $guest_data[$gkey]['count'] = $child;
                $totGuest = $totGuest + $child;
            } elseif ($gkey == 2) {
                $guest_data[$gkey]['count'] = $pet;
            }
        }
        $noOfGuest = $totGuest;
        $previouslyBookedCheckOutDates = PropertyBooking::where('property_id', $property->id)->where('property_booking_status', 'Confirmed')->where('checkin_date', '>=', date('Y-m-d'))->where(function ($query) {
            $query->where('channel', '!=', 'Website')->orWhere(function ($q) {
                $q->where('channel', 'Website')->where('property_booking_status', 'Confirmed');
            });
        })->pluck('checkout_date')->map(function ($date) {
            return date('Y-m-d', strtotime($date));
        })->toArray();
        $propertyUnavailableDates = array_values(array_diff($propertyUnavailableDates, $previouslyBookedCheckOutDates));

        $previouslyBookedChecInDates = PropertyBooking::where('property_id', $property->id)
            ->where('property_booking_status', '!=', 'Canceled')
            ->where('checkin_date', '>=', date('Y-m-d'))
            ->where(function ($query) {
                $query->where('channel', '!=', 'Website')
                    ->orWhere(function ($q) {
                        $q->where('channel', 'Website')->where('property_booking_status', 'Confirmed');
                    });
            })->pluck('checkout_date')
            ->map(function ($date) {
                return date('Y-m-d', strtotime($date));
            })->toArray();

        $filterDatesFrom = DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->pluck('date_from')->toArray();
        $filterDatesTo = DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->pluck('date_to')->toArray();
        $propertyUnavailableDates = array_values(array_diff($propertyUnavailableDates, $previouslyBookedChecInDates));
        $propertyUnavailableDates = array_values(array_diff($propertyUnavailableDates, $filterDatesTo));

        $previouslyBookedChecInDates = PropertyBooking::where('property_id', $property->id)
            ->where('property_booking_status', 'Confirmed')
            ->where('checkin_date', '>=', date('Y-m-d'))
            ->where(function ($query) {
                $query->where('channel', '!=', 'Website')
                    ->orWhere(function ($q) {
                        $q->where('channel', 'Website')->where('property_booking_status', 'Confirmed');
                    });
            })->pluck('checkin_date')
            ->map(function ($date) {
                return date('Y-m-d', strtotime($date));
            })->toArray();

        $datesNew = [];
        foreach ($previouslyBookedChecInDates as $date) {
            $previouslyBookedChecOutCount = PropertyBooking::where('property_id', $property->id)
                ->where('property_booking_status', 'Confirmed')
                ->where('checkout_date', $date)
                ->where(function ($query) {
                    $query->where('channel', '!=', 'Website')
                        ->orWhere(function ($q) {
                            $q->where('channel', 'Website')->where('property_booking_status', 'Confirmed');
                        });
                })->count();

            if ($previouslyBookedChecOutCount > 0) {
                array_push($datesNew, $date);
            }
        }

        $propertyUnavailableDates = array_merge($propertyUnavailableDates, $datesNew);
        $newDates = DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->where('date_from', '>', date('Y-m-d'))->pluck('date_from')->toArray();
        foreach ($newDates as $date) {
            $newDates = DB::table('ru_property_blocked')->where('ru_property_id', $property->ru_property_id)->where('date_to', $date)->where('date_from', '>', date('Y-m-d'))->first();
            if ($newDates) {
                array_push($propertyUnavailableDates, $date);
            }
        }


        $lastAvaliabilityRow = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->orderBy('id', 'desc')->first();
        $lastDate = Carbon::parse($lastAvaliabilityRow->availability_date);
        $startDate = $lastDate->copy()->addDay();
        $endDate = $lastDate->copy()->addYears(15);

        $futureDates = [];
        while ($startDate <= $endDate) {
            $futureDates[] = $startDate->toDateString();
            $startDate->addDay();
        }
        $propertyUnavailableDates = array_merge($propertyUnavailableDates, $futureDates);

        $previouslyBookedCheckoutDates = [];
        return view('website.properties.property-detail', compact(
            'adult',
            'child',
            'pet',
            'totGuest',
            'city_slug_name',
            'checkin_date',
            'checkout_date',
            'city_id',
            'tot_guest',
            'petsCount',
            'all_total_guests',
            'guest_data',
            'property',
            'bookingDetail',
            'importantInformation',
            'iconMap',
            'propertyUnavailableDates',
            'previouslyBookedCheckoutDates',
            'reqParams',
            'minStayArray',
            'queryDiscountCoupon'
        ));
    }


    public function propertyDetailPreview(Request $request, $ptype, $slug)
    {
        $checkinDate = "";
        $checkOutDate = "";

        // if($request->input('check_in') && $request->input('check_out')){
        //     $checkinDate = $request->input('check_in');
        //     $checkOutDate = $request->input('check_out');
        // }

        if ($request->input('next_available') == 'other_region_show') {
            $checkinDate = "";
            $checkOutDate = "";
        } else {

            if ($request->input('check_in') && $request->input('check_out')) {
                $checkinDate = $request->input('check_in');
                $checkOutDate = $request->input('check_out');
            }
        }



        $property =  $this->propertyService->getPropertyDetailBySlug(['checkin_date' => $checkinDate, 'checkout_date' => $checkOutDate, 'pType' => $ptype, 'slug' => $slug]);

        if (!$property) {
            abort(404);
        }

        $propertyId = $property->id;
        $queryDiscountCoupon = DiscountCoupon::where('user_type', 'multiple')->where('status', 1)->get()
            ->filter(function ($coupon) use ($propertyId) {
                $propertyIds = $coupon->property_id;
                if (empty($propertyIds)) {
                    $isApplicable = true;
                } else {
                    $isApplicable = in_array($propertyId, $propertyIds);
                }

                if ($isApplicable) {
                    $today = Carbon::today();

                    $startDate = Carbon::parse($coupon->start_date);
                    $endDate = Carbon::parse($coupon->end_date);

                    $isDateValid = $today->between($startDate, $endDate);

                    if ($isDateValid) {
                        $usedCount = PropertyBooking::where('applied_discount_coupon', $coupon->code)
                            ->where('property_booking_status', 'Confirmed')
                            ->count();

                        return $usedCount < $coupon->use_limit;
                    }
                }

                return false;
            })->values();

        $propertyFeatureData = $this->propertyFeatureData->mount($property->id, $ptype);

        //$propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();

        $ruPropertyIdArray = array($property->id);
        if ($property->pType == 'unit') {
            $munitid = TblUnitMultiunit::where('unit_id', $property->id)->get()->pluck('multiunit_id')->toArray();
            $mruids =  TblHomeMultiUnit::whereIn('id', $munitid)->whereNotNull('ru_property_id')->get();
            foreach ($mruids as $val) {
                $ruPropertyIdArray[] = $val->id;
            }
        } else {
            $home = TblHomeMultiUnit::where('id', $property->id)->first();
            $munitid = TblUnitMultiunit::where('multiunit_id', $home->id)->get()->pluck('unit_id')->toArray();
            $mruids =  TblHomeUnit::whereIn('id', $munitid)->get();

            foreach ($mruids as $val) {
                $ruPropertyIdArray[] = $val->id;
            }
        }


        $propertyUnavailableDates = DB::table('ru_property_availabilities')->where('ru_property_id', $property->ru_property_id)->where('is_available', 'no')->get()->pluck('availability_date')->toArray();



        $previouslyBookedCheckoutDates = PropertyBooking::where('property_id', $property->id)
            ->where('checkout_date', '>=', date('Y-m-d'))
            ->where('property_booking_status', 'Confirmed')
            ->where(function ($query) {
                $query->where('channel', '!=', 'Website')->orWhere(function ($q) {
                    $q->where('channel', 'Website')->where('property_booking_status', 'Confirmed');
                });
            })->pluck('checkout_date')
            ->map(function ($date) {
                return date('Y-m-d', strtotime($date));
            })->toArray();

        $ac = array();
        foreach ($previouslyBookedCheckoutDates as $checkoutDate) {
            $checkin = PropertyBooking::where('property_id', $property->id)->where('checkin_date', $checkoutDate)->where('property_booking_status', 'Confirmed')->first();
            if ($checkin) {
                array_push($ac, $checkoutDate);
            }
        }
        $previouslyBookedCheckoutDates = array_values(array_diff($previouslyBookedCheckoutDates, $ac));

        $adult = 1;
        $child = 0;

        if ($request->has('adults')) {

            $adult = $request->adults;
        }
        if ($request->has('children')) {
            $child = $request->children;
        }
        $guestCount = $adult;
        $totGuest = $adult;
        $noOfGuest = $totGuest;

        $reqParams = $request->all();


        $minStayController = new MinStayController();
        $minStayArray = $minStayController->syncMinStayFromToWeb(date('Y-m-d'), $property->id, $property->pType);


        $totalReviews = $property->homeReviews->count();
        $servicesData = Service::first();

        return view('website.properties.property-detail-preview', compact('property', 'servicesData', 'totalReviews', 'propertyFeatureData', 'totGuest', 'adult', 'child', 'propertyUnavailableDates',  'previouslyBookedCheckoutDates', 'reqParams', 'minStayArray', 'queryDiscountCoupon'));
    }

    public function PropertyPriceFilter(Request $request)
    {
        try {

            if ($request->ptype == 'unit') {
                $property = TblHomeUnit::where('ru_property_id', $request->propertyId)->first();
            }
            if ($request->ptype == 'multiunit') {
                $property = TblHomeMultiUnit::where('ru_property_id', $request->propertyId)->first();
            }

            $noOfGuest =  $request->tot_guest;
            $no_of_nights = $request->tot_no_of_days;
            $checkInDate = $request->checkin_date;
            $checkOutDate = $request->checkout_date;

            $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($checkInDate)), date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)) . '-1 days'))])->sum('price');


            /* if($price ==0){
                $xmlReqForPropertyPrice = "<Pull_ListPropertyPrices_RQ>
                    <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <PropertyID>".$property->ru_property_id."</PropertyID>
                    <DateFrom>".date('Y-m-d', strtotime($checkInDate))."</DateFrom>
                    <DateTo>".date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days'))."</DateTo>
                </Pull_ListPropertyPrices_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);
                $priceListDateWise = $ruPropertyPriceResponse['data']['Prices']['Season'];
                if(date('Y-m-d', strtotime(date('Y-m-d', strtotime($checkOutDate)). '-1 days')) == date('Y-m-d', strtotime($checkInDate))){
                    $price  = $base_price =  $priceListDateWise['Price'];
                }
                else{
                    foreach($priceListDateWise as $key=>$value){
                        $price = $base_price= $price + $value['Price'];
                    }
                }
            } */
            $base_price = $price;
            $price_per_night =  $price / $no_of_nights;
            $base_price =  $base_price / $no_of_nights;
            $website_markup_price = 0;
            if (setting()->website_markup) {
                $website_markup_price  = ($price_per_night * setting()->website_markup) / 100;
                $price_per_night = $price_per_night +  ($price_per_night * setting()->website_markup) / 100;
            }
            $no_of_nights = (int)MasterHelper::getDateDifference($checkInDate, $checkOutDate);
            $website_markup_price = $website_markup_price * $no_of_nights;


            $price = $base_price =   $base_price_with_other_charges = $price_per_night * $no_of_nights;

            $extraGuest  = 0;
            $extra_guest_charge = 0;
            $extra_no_of_guest  = 1;
            if ($noOfGuest > $property->guests_included && $noOfGuest <= $property->maximum_number_of_guests) {
                if ($property->maximum_number_of_guests == $noOfGuest) {
                    $extra_no_of_guest = $extraGuest = $property->maximum_number_of_guests - $property->guests_included;
                } else {
                    $extra_no_of_guest = $extraGuest = $property->maximum_number_of_guests - $noOfGuest;
                }
                $extra_guest_charge = $extra_no_of_guest * $property->extra_guest_charges * $no_of_nights;
                $price = $price + $extra_guest_charge;
                $base_price_with_other_charges = $base_price_with_other_charges + $extra_guest_charge;
            }
            $tax = 5;
            $total_additional_charges = 0;
            $additionalCharges = array();
            $additional_charges_name = "";
            if ($property->additionalCharge) {
                foreach ($property->additionalCharge as $akey => $avalue) {
                    if ($avalue->type_option == 'Per_Night') {

                        $price =  $price + $avalue->price * $no_of_nights;
                        $total_additional_charges =  $total_additional_charges + $avalue->price * $no_of_nights;
                        $property->additionalCharge[$akey]->final_additional_charge = $avalue->price * $no_of_nights;
                        $additional_charges_name = $avalue->name;
                    } else {
                        $price =  $price + $avalue->price;
                        $total_additional_charges =  $total_additional_charges + $avalue->price;
                        $property->additionalCharge[$akey]->final_additional_charge = $avalue->price * $no_of_nights;
                        $additional_charges_name = $avalue->name;
                    }
                }
                $additionalCharges = $property->additionalCharge;
            }

            $getAppliedGst  = getAppliedGst($price_per_night);
            if ($getAppliedGst) {
                $precentageAmount = ($price * $getAppliedGst->gst_percentage) / 100;
                $tax_amount = $precentageAmount;
                $tax = $getAppliedGst->gst_percentage;
                $price = $tax_amount + $price;
            }

            $total_price_multiple = $price_per_night * $request->tot_no_of_days;
            //$price = 1;
            $booking_amount_payable  = $balance_upon_checkin =  ($price / 2);

            $price = $booking_amount_payable * 2;

            $total_pet_charge = 0;
            if ($property->per_pet_charge && $request->pets > 0) {
                $total_pet_charge  = $request->pets * $property->per_pet_charge;
                $price =  $price + $total_pet_charge;
            }

            return response()->json([
                'status' => true,
                'data' => array(
                    'booking_amount_payable' => number_format(round($booking_amount_payable)),
                    'total_pet_charge' => round($total_pet_charge),
                    'per_pet_charge' => number_format(round($property->per_pet_charge)),
                    'total_pet_count' => $request->pets,

                    'website_markup_price' => $website_markup_price,
                    'balance_upon_checkin' => number_format(round($balance_upon_checkin)),
                    'extra_no_of_guest' => $extra_no_of_guest,
                    'base_price' => round($base_price),
                    'extra_guest_charge' => round($property->extra_guest_charges),
                    'total_extra_guest_charge' => round($extra_guest_charge),
                    'total_price' => round($price),
                    'tax_amount' => number_format(round($tax_amount)),
                    'tax' => $tax,
                    'num_formatted_tot_price' => number_format(round($price)),
                    'total_price_multiple' => number_format(round($total_price_multiple)),
                    'additional_charges_name' => $additional_charges_name,
                    'extraGuest' => $extraGuest,
                    'price_per_night' => $price_per_night,
                    'price_per_night_num_formatted' => number_format(round($price_per_night)),
                    'per_night_price' => round($price_per_night),
                    'formatted_base_price' => number_format(round($base_price)),
                    'formatted_total_taxable_amount' => number_format(round($tax_amount)),
                    'additionalCharges' => $additionalCharges,
                    'amountBeforeTax' => round($base_price_with_other_charges),
                    'total_additional_charges' => round($total_additional_charges),
                    'tax_amount' => $tax_amount,
                    'tot' => number_format(round($base_price_with_other_charges) + round($tax_amount) + $total_additional_charges)
                ),
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

    public function locationProperty(Request $request)
    {
        $sessionData = [];
        if ($request->has('city_slug_name') && !empty($request->input('city_slug_name'))) {
            $sessionData['city_slug_name'] = $request->input('city_slug_name');
        }
        if ($request->has('all_total_guests') && !empty($request->input('all_total_guests'))) {
            $sessionData['all_total_guests'] = $request->input('all_total_guests');
        }
        if ($request->has('checkin_date') || !empty($request->input('checkin_date'))) {
            $sessionData['checkin_date'] = $request->input('checkin_date');
        }
        if ($request->has('checkout_date') || !empty($request->input('checkout_date'))) {
            $sessionData['checkout_date'] = $request->input('checkout_date');
        }
        if ($request->has('city_id') && !empty($request->input('city_id'))) {
            $sessionData['city_id'] = $request->input('city_id');
        }
        if ($request->has('tot_guest') && !empty($request->input('tot_guest'))) {
            $sessionData['tot_guest'] = $request->input('tot_guest');
        }
        if ($request->has('adultsCount') && !empty($request->input('adultsCount'))) {
            $sessionData['adultsCount'] = $request->input('adultsCount');
        }
        // if ($request->has('childrenCount') && !empty($request->input('childrenCount'))) {
        //     $sessionData['childrenCount'] = $request->input('childrenCount');
        // }
        // if ($request->has('petsCount') && !empty($request->input('petsCount'))) {
        //     $sessionData['petsCount'] = $request->input('petsCount');
        // }
        if ($request->has('childrenCount') && !empty($request->input('childrenCount')) || $request->input('childrenCount') == 0) {
            $sessionData['childrenCount'] = $request->input('childrenCount');
        }
        if ($request->has('petsCount') && !empty($request->input('petsCount')) || $request->input('petsCount') == 0) {
            $sessionData['petsCount'] = $request->input('petsCount');
        }
        if (!empty($sessionData)) {
            session($sessionData);
        }
        return response()->json([
            'status' => true,
            'message' => 'Data saved successfully.'
        ], 200);
    }
}
