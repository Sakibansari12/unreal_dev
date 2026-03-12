<?php



namespace App\Http\Controllers;



use Illuminate\Http\Request;

use App\Models\TblHome;

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

use Carbon\Carbon;

use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;

use DB;


class RuLiveNotificationWebhookController extends Controller{



    //---------------- This method use for registring the webhook url------//

    //---- URL for webhook registration -> https://pmsdemo.tempsite.in/ru/set/booking/webhook  ----//

    public function setLiveNotificationWebhook(){
        $reqXml = "<Push_PutLiveNotificationMechanismSubscriptions_RQ>
            <Authentication>
                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                <Password>".config('ru.RU_PASSWORD')."</Password>
            </Authentication>
            <ChangeTypes>
                <Type>PropertyAvailability</Type>
                <Type>PropertyPrice</Type>
                <Type>PropertyMCQEligibilityCheck</Type>
            </ChangeTypes>
            <ObservedOwners>
                <Owner>".config('ru.RU_OWNER_ID')."</Owner>
            </ObservedOwners>
            <UrlBase>".config('ru.RU_LIVE_NOTIFICATION_URL')."</UrlBase>
        </Push_PutLiveNotificationMechanismSubscriptions_RQ>";
        $xmlResponse = MasterHelper::makeXmlRequest($reqXml);
        dd($xmlResponse);
    }


    //---------------- This method is call after htting the url 'https://pmsdemo.tempsite.in/ru/webhook/get/bookings' ---//

    public function getLiveNotificationWebhook(Request $request){
        // Storage::disk('local')->put('ru_live_notification'.$request->query('Type').'_'.time().'.txt', $request);
        // if($request->query('Type')=='PropertyAvailability'){
        //     $dateFromStringWithoutZ = rtrim($request->query('DateFrom'), 'Z');
        //     $dateFromC = Carbon::parse($dateFromStringWithoutZ);
        //     $dateToStringWithoutZ = rtrim($request->query('DateTo'), 'Z');
        //     $dateToC = Carbon::parse($dateToStringWithoutZ);
        //     $dateFrom =  $dateFromc =  date('Y-m-d', strtotime($dateFromC->toDateTimeString()));
        //     $dateTo =  date('Y-m-d', strtotime($dateToC->toDateTimeString()));
        //     $ruId =  $request->query('PropertyId');
            
        //     $count = TblHomeMultiUnit::where('ru_property_id', $ruId)->count();
            
        //     $home = TblHomeUnit::where('ru_property_id', $ruId)->first();
        //     $type = 'unit';
        //     if($count> 0){
        //         $type = 'multiunit';
        //         $home = TblHomeMultiUnit::where('ru_property_id', $ruId)->first();
        //     }
        
        //     do {
        //         $xmlReqForPropertyPrice = "<Pull_ListPropertyAvailabilityCalendar_RQ>
        //             <Authentication>
        //                 <UserName>".config('ru.RU_USER_NAME')."</UserName>
        //                 <Password>".config('ru.RU_PASSWORD')."</Password>
        //             </Authentication>
        //             <PropertyID>".$ruId."</PropertyID>
        //             <DateFrom>".$dateFrom."</DateFrom>
        //             <DateTo>".$dateFrom."</DateTo>
        //         </Pull_ListPropertyAvailabilityCalendar_RQ>";
        //         $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($xmlReqForPropertyPrice);

        //         if($ruPropertyPriceResponse){
        //             if(isset($ruPropertyPriceResponse['data']['PropertyCalendar']['CalDay']['IsBlocked'])){
        //                 $isAvailable =  'yes';
        //                 if($ruPropertyPriceResponse['data']['PropertyCalendar']['CalDay']['IsBlocked']=='true'){
        //                     $isAvailable = 'no';
        //                 }
        //                 $detail = array();
        //                 $detail['is_available'] = $isAvailable;
        //                 $detail['availability_date'] = $dateFrom;
        //                 $detail['ru_property_id'] = $ruId;
        //                 $detail['type'] = $type;
                        
        //                 $count = RuPropertyAvailability::where(['ru_property_id'=>$ruId, 'availability_date'=>$dateFrom, 'type'=>$type])->count();
                       
        //                 if($count > 0){
        //                     RuPropertyAvailability::where(['ru_property_id'=>$ruId, 'availability_date'=>$dateFrom, 'type'=>$type])->update($detail);
        //                 }
        //                 else{
        //                     RuPropertyAvailability::create($detail);
        //                 }
        //             }
        //         }
        //         $dateFrom = date('Y-m-d', strtotime($dateFrom . '+1 day'));
                
                
                

        //     }while ($dateFrom <=$dateTo);
            
            
        //     $uss = RuPropertyAvailability::whereBetween('availability_date', [$dateFromc, $dateTo])->where('ru_property_id', $ruId)->first();
         
        //     // if($uss->is_available == 'yes'){
        //     //     DB::table('ru_property_blocked')->where('date_from', $dateFromc)->where('date_to', $dateTo)->where('ru_property_id', $ruId)->delete();
        //     // }
        //     // else{
        //     //   DB::table('ru_property_blocked')->insert([
        //     //         'ru_property_id' => $home->ru_property_id,
        //     //         'property_id' => $home->id,
        //     //         'date_from' => $dateFromc,
        //     //         'date_to' => $dateTo,
        //     //         'is_available' => 'no',
        //     //         'reason' => 'ru',
        //     //         'type' => $type,
        //     //     ]); 
        //     // }
        // }
    }

}