<?php



use App\Models\TblLocation;

use App\Models\TblSitesetting;

use App\Models\TblGst;

use App\helper\MasterHelper;

use App\Models\TblHome;
use App\Models\TBGuest;
use App\Models\HomeFooterBanner;
use App\Models\TblTag;
use App\Models\RuMinStay;
use App\Models\Icon;

use App\Models\RuPropertyAvailability;

use App\Models\PropertyBooking;

use App\Models\PropertyBookingPaymentRequest;


use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\TblUnitMultiunit;
use App\Models\RuPropertyPrice;
use App\Models\TblCollection;
use App\Http\Controllers\MinStayController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

// start 08-01-2026
function checkAuth()
{
    return Auth::guard('webusers')->check();
}

if (!function_exists('getGuestData')) {
    function getGuestData()
    {
        $guest_data = TBGuest::get();
        return $guest_data;
    }
}

if (!function_exists('getLocationData')) {
    function getLocationData()
    {
        $locationIds = DB::table('tbl_home_units')
            ->whereNotNull('ru_property_id')
            ->pluck('location_id')
            ->unique();

        return TblLocation::where('status', 1)
            ->whereIn('id', $locationIds)
            ->with('state:id,name')
            ->select('id', 'title', 'location_name', 'state_id','image')
            ->get();
    }
}

if (!function_exists('getHomeFooterBanners')) {
    function getHomeFooterBanners()
    {
        $home_footer_banners = HomeFooterBanner::get();
        $home_footer_banners = $home_footer_banners->map(function ($home_footer_banner) {
            $home_footer_banner->list_content = json_decode($home_footer_banner->list_content, true); // Pass true for associative array
            return $home_footer_banner;
        });
        //dd($home_footer_banners);
        return $home_footer_banners;
    }
}

if (!function_exists('getTags')) {
    function getTags()
    {
        $tags_data = TblTag::where('tag_show_on_page', 1)->first();
        return $tags_data;
    }
}
if (!function_exists('getIcons')) {
    function getIcons()
    {
        return Icon::where('status', 1)
            ->orderBy('icons_name')
            ->get()
            ->toArray();
    }
}

// end 08-01-2026



function getLocationList()
{

    return TblLocation::where('status', 1)->get()->toArray();
}

function countryCodeSide()
{
    return DB::table('countries')->get();
}

if (!function_exists('number_format_indian')) {
    function number_format_indian($num)
    {
        $x = explode('.', $num);
        $intPart = $x[0];
        $decimalPart = isset($x[1]) ? '.' . $x[1] : '';

        $lastThree = substr($intPart, -3);
        $rest = substr($intPart, 0, -3);
        if ($rest != '') {
            $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);
            $intPart = $rest . "," . $lastThree;
        }
        return $intPart . $decimalPart;
    }
}


if (!function_exists('formatIN')) {
    function formatIN($num)
    {

        // 🛑 Clean the number first (remove commas, spaces, ₹)
        $num = preg_replace("/[^0-9.]/", "", $num);

        $x = explode('.', $num);
        $intPart = $x[0];
        $decimalPart = isset($x[1]) ? '.' . $x[1] : '';

        $lastThree = substr($intPart, -3);
        $rest = substr($intPart, 0, -3);

        if ($rest != '') {
            $rest = preg_replace("/\B(?=(\d{2})+(?!\d))/", ",", $rest);
            $intPart = $rest . "," . $lastThree;
        }

        return $intPart . $decimalPart;
    }
}




function makeXmlRequest($xml)
{

    try {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        curl_setopt($curl, CURLOPT_HTTPHEADER, array(

            'Content-type: application/x-www-form-urlencoded; charset=utf-8'

        ));

        curl_setopt($curl, CURLOPT_URL, env('RU_URL'));

        curl_setopt($curl, CURLOPT_FRESH_CONNECT, true);

        if ($xml != "") {

            if (is_array($xml)) {

                $xml = implode("&", $xml);
            }

            curl_setopt($curl, CURLOPT_POST, 1);

            curl_setopt($curl, CURLOPT_POSTFIELDS, $xml);
        }

        $xml_result = curl_exec($curl);

        $result = simplexml_load_string($xml_result);

        curl_close($curl);

        $data = array('success' => true, 'message' => 'Listed successfully', 'code' => 200, 'data' => $result);
    } catch (Exception $e) {

        $data = array('success' => false, 'message' => $e->getmessge(), 'code' => 500, 'data' => null);
    }

    return $data;
}





function setting()
{

    return TblSitesetting::first();
}



function getAppliedGst($price)
{

    return TblGst::where('slabs_start', '<=', $price)->where('slabs_upto', '>=', $price)->first();
}



function blockPropertyAvailabilityInRu($ru_id, $checkin_date, $checkout_date)
{

    $checkoutDate = date('Y-m-d', strtotime($checkout_date));

    $ck = date('Y-m-d', strtotime($checkout_date));
    if ($checkout_date > $checkin_date) {
        $checkoutDate = date('Y-m-d', strtotime($checkout_date));
    } else {
        $checkoutDate = $ck;
    }

    $ruPropertyIdArray = array();
    $ruPropertyIdArray[] = $ru_id;


    $home = TblHomeUnit::where('ru_property_id', $ru_id)->first();

    if ($home) {
        
        DB::table('ru_property_blocked')->insert([
            'ru_property_id' => $home->ru_property_id,
            'property_id'    => $home->id,
            'date_from'      => date('Y-m-d', strtotime($checkin_date)),
            'is_available'   => 'no',
            'date_to'        => date('Y-m-d', strtotime($checkoutDate)),
            'type'           => 'unit',
            'reason'         => 'Booking'
        ]);

        $munitid = TblUnitMultiunit::where('unit_id', $home->id)->get()->pluck('multiunit_id')->toArray();
        $mruids =  TblHomeMultiUnit::whereIn('id', $munitid)->whereNotNull('ru_property_id')->get();
        foreach ($mruids as $val) {
            $ruPropertyIdArray[] = $val->ru_property_id;

            DB::table('ru_property_blocked')->insert([
                'ru_property_id' => $val->ru_property_id,
                'property_id' => $val->id,
                'date_from' => date('Y-m-d', strtotime($checkin_date)),
                'is_available' => 'no',
                'date_to' => date('Y-m-d', strtotime($checkoutDate)),
                'type' => 'multiunit',
                'reason' => 'By Admin'
            ]);
        }
    } else {
        $home = TblHomeMultiUnit::where('ru_property_id', $ru_id)->first();
        if ($home) {
            $munitid = TblUnitMultiunit::where('multiunit_id', $home->id)->get()->pluck('unit_id')->toArray();
            $mruids =  TblHomeUnit::whereIn('id', $munitid)->get();

            foreach ($mruids as $val) {
                $ruPropertyIdArray[] = $val->ru_property_id;

                DB::table('ru_property_blocked')->insert([
                    'ru_property_id' => $val->ru_property_id,
                    'property_id' => $val->id,
                    'date_from' => date('Y-m-d', strtotime($checkin_date)),
                    'is_available' => 'no',
                    'date_to' => date('Y-m-d', strtotime($checkoutDate)),
                    'type' => 'unit',
                    'reason' => 'By Admin'
                ]);
            }
        }
    }


    RuPropertyAvailability::whereIn('ru_property_id', $ruPropertyIdArray)
        ->whereBetween('availability_date', [$checkin_date, $checkoutDate])
        ->update(['is_available' => 'no']);


    $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <MuCalendar PropertyID='" . $ru_id . "'>
                    <Date From='" . date('Y-m-d', strtotime($checkin_date)) . "' To='" . date('Y-m-d', strtotime($checkoutDate . '-1 days')) . "'>
                        <U>0</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";

    // $xmlResponse = MasterHelper::makeXmlRequest($xml);
    // return $xmlResponse;
    return true;
}


function blockPropertyAvailabilityInRuWebsite($ru_id, $checkin_date, $checkout_date)
{

    $checkoutDate = date('Y-m-d', strtotime($checkout_date));

    $ck = date('Y-m-d', strtotime($checkout_date));
    if ($checkout_date > $checkin_date) {
        $checkoutDate = date('Y-m-d', strtotime($checkout_date));
    } else {
        $checkoutDate = $ck;
    }

    $ruPropertyIdArray = array();
    $ruPropertyIdArray[] = $ru_id;

    $home = TblHomeUnit::where('ru_property_id', $ru_id)->first();
    if ($home) {
        DB::table('ru_property_blocked')->insert([
            'ru_property_id' => $home->ru_property_id,
            'property_id' => $home->id,
            'date_from' => date('Y-m-d', strtotime($checkin_date)),
            'is_available' => 'no',
            'date_to' => date('Y-m-d', strtotime($checkoutDate)),
            'type' => 'unit',
            'reason' => 'By Admin'
        ]);
        $munitid = TblUnitMultiunit::where('unit_id', $home->id)->get()->pluck('multiunit_id')->toArray();
        $mruids =  TblHomeMultiUnit::whereIn('id', $munitid)->whereNotNull('ru_property_id')->get();
        foreach ($mruids as $val) {
            DB::table('ru_property_blocked')->insert([
                'ru_property_id' => $val->ru_property_id,
                'property_id' => $val->id,
                'date_from' => date('Y-m-d', strtotime($checkin_date)),
                'is_available' => 'no',
                'date_to' => date('Y-m-d', strtotime($checkoutDate)),
                'type' => 'multiunit',
                'reason' => 'By Admin'
            ]);
        }
    } else {
        $home = TblHomeMultiUnit::where('ru_property_id', $ru_id)->first();
        if ($home) {
            DB::table('ru_property_blocked')->insert([
                'ru_property_id' => $home->ru_property_id,
                'property_id' => $home->id,
                'date_from' => date('Y-m-d', strtotime($checkin_date)),
                'is_available' => 'no',
                'date_to' => date('Y-m-d', strtotime($checkoutDate)),
                'type' => 'multiunit',
                'reason' => 'By Admin'
            ]);
            $munitid = TblUnitMultiunit::where('multiunit_id', $home->id)->get()->pluck('unit_id')->toArray();
            $mruids =  TblHomeUnit::whereIn('id', $munitid)->get();
            foreach ($mruids as $val) {
                DB::table('ru_property_blocked')->insert([
                    'ru_property_id' => $val->ru_property_id,
                    'property_id' => $val->id,
                    'date_from' => date('Y-m-d', strtotime($checkin_date)),
                    'is_available' => 'no',
                    'date_to' => date('Y-m-d', strtotime($checkoutDate)),
                    'type' => 'unit',
                    'reason' => 'By Admin'
                ]);
            }
        }
    }
    RuPropertyAvailability::whereIn('ru_property_id', $ruPropertyIdArray)
        ->whereBetween('availability_date', [$checkin_date, $checkoutDate])
        ->update(['is_available' => 'no']);


    // $xml = "<Push_PutAvbUnits_RQ>
    //             <Authentication>
    //                 <UserName>".config('ru.RU_USER_NAME')."</UserName>
    //                 <Password>".config('ru.RU_PASSWORD')."</Password>
    //             </Authentication>
    //             <MuCalendar PropertyID='".$ru_id."'>
    //                 <Date From='".date('Y-m-d', strtotime($checkin_date))."' To='".date('Y-m-d', strtotime($checkoutDate.'-1 days'))."'>
    //                     <U>0</U>
    //                     <C>4</C>
    //                 </Date>
    //             </MuCalendar>
    //         </Push_PutAvbUnits_RQ>";

    // $xmlResponse = MasterHelper::makeXmlRequest($xml);
    // return $xmlResponse;
    return true;
}



function unBlockPropertyAvailabilityInRu($ru_id, $checkin_date, $checkout_date)
{
    $ck = date('Y-m-d', strtotime($checkout_date));
    if ($checkout_date > $checkin_date) {
        $checkoutDate = date('Y-m-d', strtotime($checkout_date));
    } else {
        $checkoutDate = $ck;
    }
    //$checkoutDate = date('Y-m-d', strtotime($checkout_date));
    RuPropertyAvailability::where('ru_property_id', $ru_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($checkin_date)), $checkoutDate])->where('type', 'unit')->update(['is_available' => 'yes']);
    $ck = date('Y-m-d', strtotime($checkout_date));
    if ($checkout_date > $checkin_date) {
        $checkoutDate = date('Y-m-d', strtotime($checkout_date));
    } else {
        $checkoutDate = $ck;
    }
    $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <MuCalendar PropertyID='" . $ru_id . "'>
                    <Date From='" . date('Y-m-d', strtotime($checkin_date)) . "' To='" . date('Y-m-d', strtotime($checkoutDate)) . "'>
                        <U>1</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";
    $xmlResponse = MasterHelper::makeXmlRequest($xml);
    return $xmlResponse;
}



function unblock($ru_id, $checkin_date, $checkout_date)
{
    $ck = date('Y-m-d', strtotime($checkout_date));
    if ($checkout_date > $checkin_date) {
        $checkoutDate = date('Y-m-d', strtotime($checkout_date));
    } else {
        $checkoutDate = $checkout_date;
    }

    $ruPropertyIdArray = array();
    $ruPropertyIdArray[] = $ru_id;


    $home = TblHomeUnit::where('ru_property_id', $ru_id)->first();

    if ($home) {
        $munitid = TblUnitMultiunit::where('unit_id', $home->id)->get()->pluck('multiunit_id')->toArray();
        $mruids =  TblHomeMultiUnit::whereIn('id', $munitid)->whereNotNull('ru_property_id')->get();
        foreach ($mruids as $val) {
            $ruPropertyIdArray[] = $val->ru_property_id;
        }
    } else {
        $home = TblHomeMultiUnit::where('ru_property_id', $ru_id)->first();
        if ($home) {
            $munitid = TblUnitMultiunit::where('multiunit_id', $home->id)->get()->pluck('unit_id')->toArray();
            $mruids =  TblHomeUnit::whereIn('id', $munitid)->get();

            foreach ($mruids as $val) {
                $ruPropertyIdArray[] = $val->ru_property_id;
            }
        }
    }

    $checkin_date = date('Y-m-d', strtotime($checkin_date));
    $checkout_date = date('Y-m-d', strtotime($checkout_date));

    DB::table('ru_property_blocked')->whereIn('ru_property_id', $ruPropertyIdArray)->where('date_from', '<=', $checkin_date)->where('date_to', '>=', $checkout_date)->delete();

    $checkinCheck = DB::table('ru_property_blocked')
        ->whereIn('ru_property_id', $ruPropertyIdArray)
        ->where('date_to', $checkin_date)
        ->first();

    $checkOutCheck = DB::table('ru_property_blocked')
        ->whereIn('ru_property_id', $ruPropertyIdArray)
        ->where('date_from', $checkout_date)
        ->first();



    if ($checkinCheck && !$checkOutCheck) {
        RuPropertyAvailability::whereIn('ru_property_id', $ruPropertyIdArray)->whereBetween('availability_date', [date('Y-m-d', strtotime($checkin_date . '+1 days')), date('Y-m-d', strtotime($checkout_date))])->update(['is_available' => 'yes']);
    } else if ($checkOutCheck && !$checkinCheck) {
        RuPropertyAvailability::whereIn('ru_property_id', $ruPropertyIdArray)->whereBetween('availability_date', [date('Y-m-d', strtotime($checkin_date)), date('Y-m-d', strtotime($checkout_date . '-1 days'))])->update(['is_available' => 'yes']);
    } else if ($checkOutCheck && $checkinCheck) {
        RuPropertyAvailability::whereIn('ru_property_id', $ruPropertyIdArray)->whereBetween('availability_date', [date('Y-m-d', strtotime($checkin_date . '+1 days')), date('Y-m-d', strtotime($checkout_date . '-1 days'))])->update(['is_available' => 'yes']);
    } else {
        RuPropertyAvailability::whereIn('ru_property_id', $ruPropertyIdArray)->whereBetween('availability_date', [date('Y-m-d', strtotime($checkin_date)), date('Y-m-d', strtotime($checkout_date))])->update(['is_available' => 'yes']);
    }

    $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <MuCalendar PropertyID='" . $ru_id . "'>
                    <Date From='" . date('Y-m-d', strtotime($checkin_date)) . "' To='" . date('Y-m-d', strtotime($checkoutDate . '-1 days')) . "'>
                        <U>1</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";

    $xmlResponse = MasterHelper::makeXmlRequest($xml);
    return $xmlResponse;
    return true;
}


function guestOptions()
{

    $c1 = 1;
    $c2 = 0;
    $c3 = 0;

    if (session()->has('search_parameters')) {

        $c1 = session('search_parameters')['age_18_plus'];

        $c2 = session('search_parameters')['age_6_17'];
    }

    $guestOptions  = array(

        0 => array(

            'name' => 'age_18_plus',

            'title' => 'Adults',

            'sub_title' => 'Ages 18+',

            'count' => $c1

        ),

        1 => array(

            'name' => 'age_6_17',

            'title' => 'Children',

            'sub_title' => 'Up to 4 Yrs',

            'count' => $c2

        )

    );

    return $guestOptions;
}





function ratings($rating)
{

    return explode('.', $rating);
}





// function propertyList($search_parameters){

//     try {

//         $no_of_guests = (integer)$search_parameters['age_18_plus'] + (integer)$search_parameters['age_6_17'];

//         $last_date =  date('Y-m-d', strtotime($search_parameters['departureDate']. '-1 days'));

//         $checkin_date =  $search_parameters['arrivalDate'];

//         if($last_date == $search_parameters['arrivalDate']){

//             $date_difference_count = 1;

//         }

//         else{

//             $date_difference_count = MasterHelper::getDateDifference($search_parameters['arrivalDate'], $search_parameters['arrivalDate']);

//         }

//         if($date_difference_count > 1){

//             $date_difference_count = $date_difference_count;

//         }

//         else{

//             $date_difference_count = 1;

//         }

//         $location_id = $search_parameters['location_id'];

//         $query = TblHome::query();

//         $query->when($location_id != '', function ($q) use ($location_id) {

//             return $q->where('location_id', $location_id);

//         });

//         $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {

//             return $q->where('maximum_number_of_guests', '>=', $no_of_guests);

//         });

//         $list = $query->with(['additionalCharge', 'images'])->whereNotNull('ru_property_id')->get();







//         $filtered_property_list = array();

//         if(!empty($list)){

//             foreach($list as $detail){

//                 $checkIsAvailable = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', $checkin_date)->first('is_available');



//                 // $checkIsAvailable = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->whereDateBetween('availability_date', [$checkin_date, $search_parameters['departureDate']])->where('is_available', 'no')->count();

//                 $price = 0;

//                 if($checkIsAvailable && $checkIsAvailable->is_available =='yes'){

//                     $price = $initial_price = 0;

//                     do {

//                         $xmlReqForPropertyPrice = "<Pull_ListPropertyPrices_RQ>

//                             <Authentication>

//                                 <UserName>".config('ru.RU_USER_NAME')."</UserName>

//                                 <Password>".config('ru.RU_PASSWORD')."</Password>

//                             </Authentication>

//                             <PropertyID>".$detail->ru_property_id."</PropertyID>

//                             <DateFrom>".$checkin_date."</DateFrom>

//                             <DateTo>".$checkin_date."</DateTo>

//                         </Pull_ListPropertyPrices_RQ>";

//                         $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);



//                         if($ruPropertyPriceResponse){

//                             if(isset($ruPropertyPriceResponse['data']['Prices'])){

//                                 if(isset($ruPropertyPriceResponse['data']['Prices']['Season'])){

//                                     if(isset($ruPropertyPriceResponse['data']['Prices']['Season']['Price'])){

//                                         $price = $price +  $ruPropertyPriceResponse['data']['Prices']['Season']['Price'];

//                                     }

//                                 }

//                             }

//                         }

//                         $checkin_date = date('Y-m-d', strtotime($checkin_date . '+1 day'));

//                     }while ($checkin_date <=$last_date);

//                 }

//                 if($price >0){

//                     $gst_amount = 0;

//                     $gstPrecentage = 0;

//                     $getAppliedGst  = getAppliedGst($price);

//                     if($getAppliedGst){

//                         $precentageAmount = ($price*$getAppliedGst->gst_percentage)/100;

//                         $gst_amount = $precentageAmount;

//                         $gstPrecentage = $getAppliedGst->gst_percentage;

//                     }

//                     $detail->price = $price;

//                     $detail->per_night_price = $price/$date_difference_count;

//                     $detail->initial_price = $price;

//                     $detail->gst_amount = $gst_amount;

//                     $detail->gst_percentage = $gstPrecentage;





//                     $extra_no_of_guest = 0;

//                     $extra_guest_charge = 0;



//                     if($no_of_guests >$detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests){

//                         if($detail->maximum_number_of_guests == $no_of_guests){

//                             $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;

//                         }

//                         else if($no_of_guests == $detail->maximum_number_of_guests){

//                             $extra_no_of_guest = 1;

//                         }

//                         else{

//                             $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;

//                         }

//                         $extra_guest_charge = $extra_no_of_guest*$detail->extra_guest_charges;

//                         $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);

//                         if($getAppliedGst){

//                             $precentageExtraGuestChargeAmount = ($extra_guest_charge*$getAppliedGst->gst_percentage)/100;

//                             $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;

//                         }

//                     }

//                     $detail->extra_no_of_guest = $extra_no_of_guest;

//                     $detail->final_extra_guest_charge = $extra_guest_charge;

//                     array_push($filtered_property_list, $detail);

//                 }

//             }

//         }



//         return $filtered_property_list;

//     }

//     catch (\Exception $e) {

//         return $e->getMessage();

//     }

// }



function propertyList($search_parameters)
{

    try {

        $no_of_guests = $search_parameters['age_18_plus'];

        $last_date =  date('Y-m-d', strtotime($search_parameters['departureDate'] . '-1 days'));

        $checkin_date =  $search_parameters['arrivalDate'];

        if ($last_date == $search_parameters['arrivalDate']) {

            $date_difference_count = 1;
        } else {

            $date_difference_count = MasterHelper::getDateDifference($search_parameters['arrivalDate'], $search_parameters['departureDate']);
        }

        if ($date_difference_count > 1) {

            $date_difference_count = $date_difference_count;
        } else {

            $date_difference_count = 1;
        }

        $location_id = $search_parameters['location_id'];

        $query = TblHome::query();

        $query->when($location_id != '', function ($q) use ($location_id) {

            return $q->where('location_id', $location_id);
        });

        $query->when($no_of_guests != 0, function ($q) use ($no_of_guests) {

            return $q->where('maximum_number_of_guests', '>=', $no_of_guests);
        });

        $list = $query->with(['additionalCharge', 'images'])->whereNotNull('ru_property_id')->get();



        $filtered_property_list = array();

        if (!empty($list)) {

            foreach ($list as $detail) {

                $count = DB::table('ru_property_availabilities')->where('ru_property_id', $detail->ru_property_id)->where('availability_date', '>=', $checkin_date)->where('availability_date', '<=', $search_parameters['departureDate'])->where('is_available', 'no')->count();



                if ($count == 0) {

                    $price = 0;

                    $price = $initial_price = 0;

                    $xmlReqForPropertyPrice = "<Pull_ListPropertyPrices_RQ>

                        <Authentication>

                            <UserName>" . config('ru.RU_USER_NAME') . "</UserName>

                            <Password>" . config('ru.RU_PASSWORD') . "</Password>

                        </Authentication>

                        <PropertyID>" . $detail->ru_property_id . "</PropertyID>

                        <DateFrom>" . $checkin_date . "</DateFrom>

                        <DateTo>" . $last_date . "</DateTo>

                    </Pull_ListPropertyPrices_RQ>";

                    $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);

                    $priceListDateWise = $ruPropertyPriceResponse['data']['Prices']['Season'];

                    if ($last_date == date('Y-m-d', strtotime($checkin_date))) {

                        $price  = $priceListDateWise['Price'];
                    } else {

                        foreach ($priceListDateWise as $key => $value) {

                            $price  = $price + $value['Price'];
                        }
                    }

                    if ($price > 0) {

                        $gst_amount = 0;

                        $gstPrecentage = 0;



                        $detail->price = $price;

                        $per_night_price = $price / $date_difference_count;









                        if (setting()->website_markup) {



                            $per_night_price = $per_night_price +  ($per_night_price * setting()->website_markup) / 100;
                        }







                        $detail->per_night_price = $per_night_price;

                        $detail->per_room_price = $per_night_price / $detail->no_of_bedrooms;



                        $detail->initial_price = $price;

                        $detail->gst_amount = $gst_amount;

                        $detail->gst_percentage = $gstPrecentage;



                        $getAppliedGst  = getAppliedGst($price);

                        if ($getAppliedGst) {

                            $precentageAmount = ($price * $getAppliedGst->gst_percentage) / 100;

                            $gst_amount = $precentageAmount;

                            $gstPrecentage = $getAppliedGst->gst_percentage;
                        }



                        $extra_no_of_guest = 0;

                        $extra_guest_charge = 0;



                        if ($no_of_guests > $detail->guests_included && $no_of_guests <= $detail->maximum_number_of_guests) {

                            if ($detail->maximum_number_of_guests == $no_of_guests) {

                                $extra_no_of_guest = $detail->maximum_number_of_guests - $detail->guests_included;
                            } else if ($no_of_guests == $detail->maximum_number_of_guests) {

                                $extra_no_of_guest = 1;
                            } else {

                                $extra_no_of_guest = $detail->maximum_number_of_guests - $no_of_guests;
                            }

                            $extra_guest_charge = $extra_no_of_guest * $detail->extra_guest_charges;

                            $getAppliedGeusetChargeGst  = getAppliedGst($extra_guest_charge);

                            if ($getAppliedGst) {

                                $precentageExtraGuestChargeAmount = ($extra_guest_charge * $getAppliedGst->gst_percentage) / 100;

                                $extra_guest_charge = $precentageExtraGuestChargeAmount + $extra_guest_charge;
                            }
                        }

                        $detail->extra_no_of_guest = $extra_no_of_guest;

                        $detail->final_extra_guest_charge = $extra_guest_charge;

                        array_push($filtered_property_list, $detail);
                    }
                }
            }
        }

        return $filtered_property_list;
    } catch (\Exception $e) {

        return $e->getMessage();
    }
}





function convertNumberToIndianCurrencyWords($number)
{

    $hyphen      = '-';

    $conjunction = ' and ';

    $separator   = ', ';

    $negative    = 'Negative ';

    $dictionary  = [

        0                   => 'Zero',

        1                   => 'One',

        2                   => 'Two',

        3                   => 'Three',

        4                   => 'Four',

        5                   => 'Five',

        6                   => 'Six',

        7                   => 'Seven',

        8                   => 'Eight',

        9                   => 'Nine',

        10                  => 'Ten',

        11                  => 'Eleven',

        12                  => 'Twelve',

        13                  => 'Thirteen',

        14                  => 'Fourteen',

        15                  => 'Fifteen',

        16                  => 'Sixteen',

        17                  => 'Seventeen',

        18                  => 'Eighteen',

        19                  => 'Nineteen',

        20                  => 'Twenty',

        30                  => 'Thirty',

        40                  => 'Forty',

        50                  => 'Fifty',

        60                  => 'Sixty',

        70                  => 'Seventy',

        80                  => 'Eighty',

        90                  => 'Ninety',

        100                 => 'Hundred',

        1000                => 'Thousand',

        100000              => 'Lakh',

        10000000            => 'Crore'

    ];



    if (!is_numeric($number)) {

        return false;
    }



    if ($number < 0) {

        return $negative . convertNumberToIndianCurrencyWords(abs($number));
    }



    $string = $fraction = null;



    if (strpos($number, '.') !== false) {

        list($number, $fraction) = explode('.', $number);
    }



    switch (true) {

        case $number < 21:

            $string = $dictionary[$number];

            break;

        case $number < 100:

            $tens   = ((int) ($number / 10)) * 10;

            $units  = $number % 10;

            $string = $dictionary[$tens];

            if ($units) {

                $string .= $hyphen . $dictionary[$units];
            }

            break;

        case $number < 1000:

            $hundreds  = (int) ($number / 100);

            $remainder = $number % 100;

            $string = $dictionary[$hundreds] . ' ' . $dictionary[100];

            if ($remainder) {

                $string .= $conjunction . convertNumberToIndianCurrencyWords($remainder);
            }

            break;

        case $number < 100000:

            $thousands = (int) ($number / 1000);

            $remainder = $number % 1000;

            $string = convertNumberToIndianCurrencyWords($thousands) . ' ' . $dictionary[1000];

            if ($remainder) {

                $string .= $remainder < 100 ? $conjunction : $separator;

                $string .= convertNumberToIndianCurrencyWords($remainder);
            }

            break;

        case $number < 10000000:

            $lakhs = (int) ($number / 100000);

            $remainder = $number % 100000;

            $string = convertNumberToIndianCurrencyWords($lakhs) . ' ' . $dictionary[100000];

            if ($remainder) {

                $string .= $remainder < 100 ? $conjunction : $separator;

                $string .= convertNumberToIndianCurrencyWords($remainder);
            }

            break;

        default:

            $crores = (int) ($number / 10000000);

            $remainder = $number % 10000000;

            $string = convertNumberToIndianCurrencyWords($crores) . ' ' . $dictionary[10000000];

            if ($remainder) {

                $string .= $remainder < 100 ? $conjunction : $separator;

                $string .= convertNumberToIndianCurrencyWords($remainder);
            }

            break;
    }



    if (null !== $fraction && is_numeric($fraction)) {

        $string .= $decimal;

        $words = [];

        foreach (str_split((string) $fraction) as $number) {

            $words[] = $dictionary[$number];
        }

        $string .= implode(' ', $words);
    }



    return $string;
}





function getInvoiceSerialNo()
{

    $bookingDetail = PropertyBooking::whereNotNull('invoice_serial_no')->orderBy('id', 'desc')->first('invoice_serial_no');

    if ($bookingDetail) {

        return $bookingDetail->invoice_serial_no + 1;
    }

    return 1;
}





function getPartPayments($booikngId)
{

    return PropertyBookingPaymentRequest::where('property_booking_id', $booikngId)->where('booking_request_status', 'Payment Received')->pluck('amount')->implode(', ');
}





function covertTime12HourFormat($time)
{

    return date("g:i A", strtotime($time));
}



function countPaymentReq($booikngId)
{

    return PropertyBookingPaymentRequest::where('property_booking_id', $booikngId)->where('booking_request_status', 'Payment Received')->count();
}





function sendWhatsAppMessage($requestParameters, $phone_no)
{

    try {

        $curl = curl_init();

        curl_setopt_array($curl, array(

            CURLOPT_URL => env('WHATSAPP_API_END_POINT') . '/api/v1/sendTemplateMessage?whatsappNumber=' . $phone_no,

            CURLOPT_RETURNTRANSFER => true,

            CURLOPT_ENCODING => '',

            CURLOPT_MAXREDIRS => 10,

            CURLOPT_TIMEOUT => 0,

            CURLOPT_FOLLOWLOCATION => true,

            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,

            CURLOPT_CUSTOMREQUEST => 'POST',

            CURLOPT_POSTFIELDS => $requestParameters,

            CURLOPT_HTTPHEADER => array(

                'Content-Type: application/json',

                'Authorization: Bearer ' . env('WHATSAPP_API_TOKEN')

            ),

        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return array('status' => true, 'result' => json_decode($response, true));
    } catch (Exception $e) {

        return array('status' => false, 'result' => $e->getMessage());
    }
}


function reservationXmlRequest($booking)
{
    $property  = TblHomeUnit::where('id', $booking->property_id)->first();
    if (!$property) {
        $property  = TblHomeMultiUnit::where('id', $booking->property_id)->first();
    }


    

    $total_price = $price =  RuPropertyPrice::where('ru_property_id', $property->ru_property_id)
    ->whereBetween(
        'price_date',
        [
            date('Y-m-d', strtotime($booking->checkin_date)),
            date('Y-m-d', strtotime($booking->checkout_date.'-1 days')),
        ]
    )
    ->sum('price');



    $customerDetail = json_decode($booking->customer_detail);

    $req = '<Push_PutConfirmedReservationMulti_RQ>
            <Authentication>
                <UserName>' . config('ru.RU_USER_NAME') . '</UserName>
                <Password>' . config('ru.RU_PASSWORD') . '</Password>
            </Authentication>
            <Reservation>
                <StayInfos>
                    <StayInfo>
                        <PropertyID>' . $property->ru_property_id . '</PropertyID>
                        <DateFrom>' . date('Y-m-d', strtotime($booking->checkin_date)) . '</DateFrom>
                        <DateTo>' . date('Y-m-d', strtotime($booking->checkout_date)) . '</DateTo>
                        <NumberOfGuests>' . $booking->no_of_adult . '</NumberOfGuests>
                        <Costs>
                            <RUPrice>' . round($total_price) . '</RUPrice>
                            <ClientPrice>' . $booking->payable_amount . '</ClientPrice>
                            <AlreadyPaid>' . $booking->payable_amount . '</AlreadyPaid>
                            <ChannelCommission>0.00</ChannelCommission>
                        </Costs>
                    </StayInfo>
                </StayInfos>
                <CancellationPolicyInfo>
                    <PolicyText>Cancellation Policy</PolicyText>
                    <CancellationPolicies>
                        <CancellationPolicy ValidFrom="0" ValidTo="3">100</CancellationPolicy>
                        <CancellationPolicy ValidFrom="4" ValidTo="10">50</CancellationPolicy>
                    </CancellationPolicies>
                </CancellationPolicyInfo>
                <CustomerInfo>
                    <Name>' . $customerDetail->first_name . '</Name>
                    <SurName>' . $customerDetail->last_name . '</SurName>
                    <Email>' . $customerDetail->email . '</Email>
                    <Phone>' . $customerDetail->mobile_number . '</Phone>
                    <CountryID>42</CountryID>
                </CustomerInfo>
                <GuestDetailsInfo>
                    <NumberOfAdults>' . $booking->no_of_adult . '</NumberOfAdults>
                    <NumberOfChildren>0</NumberOfChildren>
                    <NumberOfInfants>0</NumberOfInfants>
                    <NumberOfPets>0</NumberOfPets>
                </GuestDetailsInfo>
                <Comments>Booking from nook and co website</Comments>
            </Reservation>
        </Push_PutConfirmedReservationMulti_RQ>';
    return $req;
}

// function getPriceAndAvalibility($ru_property_id, $pType)
// {
//     if ($pType == 'unit') {
//         $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();
//     }
//     if ($pType == 'multiunit') {
//         $property = TblHomeMultiUnit::where('ru_property_id', $ru_property_id)->first();
//     }
   
//     $markup = setting()->website_markup ?? 0;
//     $availabilityYesDates = RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->where('availability_date', '>=', date('Y-m-d'))->where('is_available', 'yes')->orderBy('availability_date')
//         ->pluck('availability_date')->toArray();
//     $previouslyBookedCheckoutDates = PropertyBooking::where('property_id', $property->id)->where('property_booking_status', '!=', 'Canceled')->where('checkin_date', '>=', date('Y-m-d'))
//         ->where(function ($query) {
//             $query->where('channel', '!=', 'Website')
//                 ->orWhere(function ($q) {
//                     $q->where('channel', 'Website')
//                         ->where('property_booking_status', 'Confirmed');
//                 });
//         })
//         ->pluck('checkout_date')->map(function ($date) {
//             return date('Y-m-d', strtotime($date));
//         })->toArray();
//     $availabilities = array_values(
//         array_unique(
//             array_merge($availabilityYesDates, $previouslyBookedCheckoutDates)
//         )
//     );
//     sort($availabilities);
//     if (!empty($availabilities)) {
//         $checkInDate = null;
//         $minStay = 1;
//         foreach ($availabilities as $availableDate) {
//             // Sync minstay for this date
//             $minStayController = new MinStayController();
//             $minStayController->syncMinStay(
//                 date('Y-m-d', strtotime($availableDate)),
//                 $property->id,
//                 $property->pType
//             );
//             $minStayDetail = RuMinStay::where([
//                 'ru_property_id' => $property->ru_property_id,
//                 'minstay_date'   => date('Y-m-d', strtotime($availableDate)),
//                 'type'           => $property->pType
//             ])->orderBy('id', 'desc')->first();
//             if ($minStayDetail) {
//                 $minStay = (int) $minStayDetail->is_minstay_count;
//             }
//             // ðŸ”¥ Check continuous availability for minstay
//             $isValid = true;
//             for ($i = 0; $i < $minStay; $i++) {
//                 $checkDate = date('Y-m-d', strtotime($availableDate . " +$i days"));
//                 if (!in_array($checkDate, $availabilities)) {
//                     $isValid = false;
//                     break;
//                 }
//             }
//             // âœ… Valid check-in date found
//             if ($isValid) {
//                 $checkInDate = $availableDate;
//                 break;
//             }
//         }
//         // Agar valid check-in mila
//         if ($checkInDate) {
//             $checkOutDate = date('Y-m-d', strtotime($checkInDate . " +$minStay days"));
//             $date_difference_count = $minStay;
//             $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)
//             ->whereBetween('price_date', [
//                 $checkInDate,
//                 date('Y-m-d', strtotime($checkOutDate . '-1 days'))
//             ])->get()->unique('price_date')->sum('price');
            
            
//             $property->per_night_price = round($price / $date_difference_count);
//             $property->per_night_price += ($property->per_night_price * $markup) / 100;
//             $property->no_of_nights = $date_difference_count;
//             $property->price       = $property->per_night_price * $date_difference_count;
//             $property->date_from   = $checkInDate;
//             $property->date_to     = $checkOutDate;
//         }
//     }
//     return array('no_of_nights'=>$property->no_of_nights, 'per_night_price' => $property->per_night_price, 'next_available_date_from' => $property->date_from, 'next_available_date_to' => $property->date_to);
// }

function getPriceAndAvalibility($ru_property_id, $pType)
{
    if ($pType == 'unit') {
        $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();
    }
    if ($pType == 'multiunit') {
        $property = TblHomeMultiUnit::where('ru_property_id', $ru_property_id)->first();
    }
   
    $markup = setting()->website_markup ?? 0;
    $availabilityYesDates = RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->where('availability_date', '>=', date('Y-m-d'))->where('is_available', 'yes')->orderBy('availability_date')
        ->pluck('availability_date')->toArray();
    $previouslyBookedCheckoutDates = PropertyBooking::where('property_id', $property->id)->where('property_booking_status',  'Confirmed')->where('checkin_date', '>=', date('Y-m-d'))
        ->pluck('checkout_date')->map(function ($date) {
            return date('Y-m-d', strtotime($date));
        })->toArray();
    //  if($property->ru_property_id == "4437668"){ 
    //       dd($previouslyBookedCheckoutDates);
    //   }
    $availabilities = array_values(
        array_unique(
            array_merge($availabilityYesDates, $previouslyBookedCheckoutDates)
        )
    );
    sort($availabilities);
    if (!empty($availabilities)) {
        $checkInDate = null;
        $minStay = 1;
        foreach ($availabilities as $availableDate) {
            // Sync minstay for this date
            $minStayController = new MinStayController();
            $minStayController->syncMinStay(
                date('Y-m-d', strtotime($availableDate)),
                $property->id,
                $property->pType
            );
            $minStayDetail = RuMinStay::where([
                'ru_property_id' => $property->ru_property_id,
                'minstay_date'   => date('Y-m-d', strtotime($availableDate)),
                'type'           => $property->pType
            ])->orderBy('id', 'desc')->first();
            if ($minStayDetail) {
                $minStay = (int) $minStayDetail->is_minstay_count;
            }
            // ðŸ”¥ Check continuous availability for minstay
            
            
            $isValid = true;
            for ($i = 0; $i < $minStay; $i++) {
                $checkDate = date('Y-m-d', strtotime($availableDate . " +$i days"));
                if (!in_array($checkDate, $availabilities)) {
                    $isValid = false;
                    break;
                }
            }
            
            $isValid = true;
            for ($i = 0; $i < $minStay; $i++) {
                $checkDate = date('Y-m-d', strtotime($availableDate . " +$i days"));
                $blocked = DB::table('ru_property_blocked')
                    ->where('ru_property_id', $property->ru_property_id)
                    ->where('date_from', '<=', $checkDate)
                    ->where('date_to', '>', $checkDate) // ✅ checkout excluded
                    ->exists();
            
                if ($blocked) {
                    $isValid = false;
                    break;
                }
            }
            
            
            
            // âœ… Valid check-in date found
            if ($isValid) {
                $checkInDate = $availableDate;
                break;
            }
        }
        // Agar valid check-in mila
        if ($checkInDate) {
            $checkOutDate = date('Y-m-d', strtotime($checkInDate . " +$minStay days"));
            $date_difference_count = $minStay;
            $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)
            ->whereBetween('price_date', [
                $checkInDate,
                date('Y-m-d', strtotime($checkOutDate . '-1 days'))
            ])->get()->unique('price_date')->sum('price');
            
            
            $property->per_night_price = round($price / $date_difference_count);
            $property->per_night_price += ($property->per_night_price * $markup) / 100;
            $property->no_of_nights = $date_difference_count;
            $property->price       = $property->per_night_price * $date_difference_count;
            $property->date_from   = $checkInDate;
            $property->date_to     = $checkOutDate;
        }
    }
    return array('no_of_nights'=>$property->no_of_nights, 'per_night_price' => $property->per_night_price, 'next_available_date_from' => $property->date_from, 'next_available_date_to' => $property->date_to);
}


if (!function_exists('getLocations')) {
    function getLocations()
    {
        $location_list = TblLocation::with(['state', 'properties'])
            ->where('status', 1)
            // ->whereHas('properties') 
            ->whereHas('properties', function ($query) {
                $query->whereNotNull('ru_property_id');
            })
            ->get();
        return $location_list;
    }
}

if (!function_exists('getCollections')) {
    function getCollections()
    {
        $collection_list = TblCollection::where('status', 1)->get();
        return $collection_list;
    }
}

if (!function_exists('getStatesWithLocations')) {
    function getStatesWithLocations()
    {
        $state_data = TblState::where('status', 1)
            ->with(['locations' => function ($query) {
                $query->where('status', 1)->whereNull('deleted_at');
            }])
            ->orderBy('name', 'asc')
            ->get();
        return $state_data->filter(function ($state) {
            return $state->locations->isNotEmpty();
        });
    }
}


function reservationXmlRequestNew($booking, $property)
{
    $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($booking->checkin_date)), date('Y-m-d', strtotime(date('Y-m-d', strtotime($booking->checkout_date)) . '-1 days'))])->sum('price');
    $req = '<Push_PutConfirmedReservationMulti_RQ>
            <Authentication>
                <UserName>' . config('ru.RU_USER_NAME') . '</UserName>
                <Password>' . config('ru.RU_PASSWORD') . '</Password>
            </Authentication>
            <Reservation>
                <StayInfos>
                    <StayInfo>
                        <PropertyID>' . $property->ru_property_id . '</PropertyID>
                        <DateFrom>' . date('Y-m-d', strtotime($booking->checkin_date)) . '</DateFrom>
                        <DateTo>' . date('Y-m-d', strtotime($booking->checkout_date)) . '</DateTo>
                        <NumberOfGuests>' . $booking->no_of_adult . '</NumberOfGuests>
                        <Costs>
                            <RUPrice>' . round($price) . '</RUPrice>
                            <ClientPrice>' . $booking->payable_amount . '</ClientPrice>
                            <AlreadyPaid>0.00</AlreadyPaid>
                            <ChannelCommission>0.00</ChannelCommission>
                        </Costs>
                    </StayInfo>
                </StayInfos>
                <CancellationPolicyInfo>
                    <PolicyText>Full refund until 11 days before arrival. 50% charge from 4 to 10 days before arrival. 100% charge from 0 to 3 days before arrival.</PolicyText>
                    <CancellationPolicies>
                        <CancellationPolicy ValidFrom="0" ValidTo="3">100</CancellationPolicy>
                        <CancellationPolicy ValidFrom="4" ValidTo="10">50</CancellationPolicy>
                    </CancellationPolicies>
                </CancellationPolicyInfo>
                <CustomerInfo>
                    <Name>' . json_decode($booking->customer_detail)->first_name . '</Name>
                    <SurName>' . json_decode($booking->customer_detail)->last_name . '</SurName>
                    <Email>' . json_decode($booking->customer_detail)->email . '</Email>
                    <Phone>' . json_decode($booking->customer_detail)->mobile_number . '</Phone>
                    <CountryID>42</CountryID>
                </CustomerInfo>
                <GuestDetailsInfo>
                    <NumberOfAdults>' . $booking->no_of_adult . '</NumberOfAdults>
                    <NumberOfChildren>0</NumberOfChildren>
                    <NumberOfInfants>0</NumberOfInfants>
                    <NumberOfPets>0</NumberOfPets>
                </GuestDetailsInfo>
                <Comments>This is the testing booking</Comments>
            </Reservation>
        </Push_PutConfirmedReservationMulti_RQ>';
    return $req;
}


function reservationXmlRequestModify($booking, $property, $oldBooking)
{
    $price = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->whereBetween('price_date', [date('Y-m-d', strtotime($booking->checkin_date)), date('Y-m-d', strtotime(date('Y-m-d', strtotime($booking->checkout_date)) . '-1 days'))])->sum('price');
    $req = '<Push_ModifyStay_RQ>
            <Authentication>
                 <UserName>' . config('ru.RU_USER_NAME') . '</UserName>
                <Password>' . config('ru.RU_PASSWORD') . '</Password>
            </Authentication>
            <ReservationID>' . $booking->booking_id . '</ReservationID>
            <Current>
                <PropertyID>' . $property->ru_property_id . '</PropertyID>
                <DateFrom>' . date('Y-m-d', strtotime($oldBooking->checkin_date)) . '</DateFrom>
                <DateTo>' . date('Y-m-d', strtotime($oldBooking->checkout_date)) . '</DateTo>
            </Current>
            <Modify>
              
                <DateFrom>' . date('Y-m-d', strtotime($booking->checkin_date)) . '</DateFrom>
                <DateTo>' . date('Y-m-d', strtotime($booking->checkout_date)) . '</DateTo>
                <NumberOfGuests>' . $booking->no_of_adult . '</NumberOfGuests>
                <ClientPrice>' . round($price) . '</ClientPrice>
                <AlreadyPaid>' . round($price) . '</AlreadyPaid>
                <ChannelCommission>0.00</ChannelCommission>
                <PMSReservationId>' . $booking->booking_id . '</PMSReservationId>
                <CancellationPolicyInfo>
                    <PolicyText>Full refund until 11 days before arrival. 50% charge from 4 to 10 days before arrival. 100% charge from 0 to 3 days before arrival.</PolicyText>
                    <CancellationPolicies>
                        <CancellationPolicy ValidFrom="0" ValidTo="3">100</CancellationPolicy>
                        <CancellationPolicy ValidFrom="4" ValidTo="10">50</CancellationPolicy>
                    </CancellationPolicies>
                </CancellationPolicyInfo>
                <GuestDetailsInfo>
                    <NumberOfAdults>' . $booking->no_of_adult . '</NumberOfAdults>
                    <NumberOfChildren>0</NumberOfChildren>
                    <NumberOfInfants>0</NumberOfInfants>
                    <NumberOfPets>0</NumberOfPets>
                </GuestDetailsInfo>
            </Modify>
            <AllowOverbooking>true</AllowOverbooking>
            <UseCurrentPrice>false</UseCurrentPrice>
        </Push_ModifyStay_RQ>';
    return $req;
}



function block($ru_id, $checkin_date, $checkout_date)
{
    $ck = date('Y-m-d', strtotime($checkout_date));
    if ($checkout_date > $checkin_date) {
        $checkoutDate = date('Y-m-d', strtotime($ck . '-1 day'));
    } else {
        $checkoutDate = $ck;
    }

    $checkoutDate = date('Y-m-d', strtotime($checkout_date));


    RuPropertyAvailability::where('ru_property_id', $ru_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($checkin_date)), $checkoutDate])->where('type', 'unit')->update(['is_available' => 'no']);
    $ck = date('Y-m-d', strtotime($checkout_date));
    if ($checkout_date > $checkin_date) {
        $checkoutDate = date('Y-m-d', strtotime($checkout_date));
    } else {
        $checkoutDate = $ck;
    }
    $xml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                    <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                    <Password>" . config('ru.RU_PASSWORD') . "</Password>
                </Authentication>
                <MuCalendar PropertyID='" . $ru_id . "'>
                    <Date From='" . date('Y-m-d', strtotime($checkin_date)) . "' To='" . date('Y-m-d', strtotime($checkoutDate)) . "'>
                        <U>0</U>
                        <C>4</C>
                    </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";
    $xmlResponse = MasterHelper::makeXmlRequest($xml);
    return $xmlResponse;
}

function formatIndianNumber($number)
{
    $number = (string) $number;
    if (strlen($number) <= 3) {
        return $number;
    }
    $lastThreeDigits = substr($number, -3);
    $remainingDigits = substr($number, 0, -3);
    $formattedRemaining = preg_replace('/\B(?=(\d{2})+(?!\d))/', ',', $remainingDigits);
    return $formattedRemaining . ',' . $lastThreeDigits;
}




// captcha function
if (!function_exists('generateCaptcha')) {
    function generateCaptcha()
    {
        $digits = rand(100, 999);
        $characters = '';
        $alphanumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 3; $i++) {
            $characters .= $alphanumeric[rand(0, strlen($alphanumeric) - 1)];
        }
        $captcha = $digits . $characters;
        return strtoupper($captcha);
    }
}
if (!function_exists('generateCaptchaNew')) {
    function generateCaptchaNew()
    {
        $digits = rand(100, 999);
        $characters = '';
        $alphanumeric = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        for ($i = 0; $i < 3; $i++) {
            $characters .= $alphanumeric[rand(0, strlen($alphanumeric) - 1)];
        }
        $captcha = $digits . $characters;
        return strtoupper($captcha);
    }
}

if (!function_exists('isPriceLabEnable')) {
        function isPriceLabEnable(){
            $detail = DB::table('tbl_sitesettings')->first();
            $status = false;
            if($detail){
               if(isset($detail->is_pricelab_enabled) && $detail->is_pricelab_enabled == 1){
                   $status = true;
               }
            }
            
            return $status;
        }
    }
