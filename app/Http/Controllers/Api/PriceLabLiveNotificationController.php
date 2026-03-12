<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Models\TblHomeUnit;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyPrice;
use App\Models\RuPropertyMinstay;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use App\Http\Traits\ValidatesDateRange;
use PhpOption\None;
use Illuminate\Support\Facades\Storage;
use App\Services\PropertyService;
use App\Services\PriceLabService;

use App\Services\PriceLabsPayloadService;
use App\Services\ApiAuditLogger;
use App\Services\PriceLabsSyncService;
use App\helper\MasterHelper;
use DB;
use Illuminate\Support\Facades\Log; 

class PriceLabLiveNotificationController extends Controller{
    use ValidatesDateRange;


    protected $propertyService;
    protected $priceLabService;
    protected $priceLabPayloadService;

    public function __construct(){
        $this->propertyService = new PriceLabService();
        $this->priceLabPayloadService = new PriceLabsPayloadService();

    }

    public function getCalendarTriggerUrlData(Request $request){
        if(!isPriceLabEnable()){
            abort('404');
        }
        $payload = $request->all();

        $directory = 'priclabnoti';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        $filename = $directory . '/calendar_trigger_' . now()->format('Y_m_d_His') . '_' . uniqid() . '.json';

        Storage::put($filename, json_encode($payload, JSON_PRETTY_PRINT));
        $fileContent = Storage::get($filename);
        $payload = json_decode($fileContent, true);
        
        $home = TblHomeUnit::where('ru_property_id', $payload['listing_id'])->first();
        $listingId = $home->pricelabs_unique_id;

        if(isset($listingId) && isset($payload['start_date']) && isset($payload['end_date'])){
            try{
                $pricelabPayload = $this->priceLabPayloadService->preparePriceLabsCalendarTriggerPayload($listingId, $payload['start_date'], $payload['end_date']);
                $this->propertyService->syncCalendars($pricelabPayload);
            }
            catch(Exception $e){
                return response()->json(['success' => true]);
            }

        }
        return response()->json(['success' => true]);


    }


    // public function getSyncUrlData(Request $request){
    //     if(!isPriceLabEnable()){
    //         abort('404');
    //     }
    //     try{
    //         $directory = 'priclabnoti';

    //         if (!Storage::exists($directory)) {
    //             Storage::makeDirectory($directory);
    //         }

    //         if(isset($request['listing_id'])){
    //             $filename = $directory . '/'.$request['listing_id'].'_sync_url_trigger_' . now()->format('Y_m_d_His') . '_' . uniqid() . '.json';
    //         }
    //         else{
    //              $filename = $directory . '/sync_url_trigger_' . now()->format('Y_m_d_His') . '_' . uniqid() . '.json';
    //         }
    //         Storage::put($filename, json_encode($request->all(), JSON_PRETTY_PRINT));

    //       // $filename = $directory . '/'."4530589_sync_url_trigger_2026_02_25_161407_699ed2773fc9f.json";
            
          
    //         $fileContent = Storage::get($filename);

    //         // Decode JSON
    //         $payload = json_decode($fileContent, true);
    //         $listingId = $payload['listing_id'] ?? null;
            
          
    //         if (!$listingId) {
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Invalid Lisiting id'
    //             ], 200);
    //         }

    //         $property = TblHomeUnit::where('ru_property_id', $listingId)->first();



    //         if(!$property){
    //             return response()->json([
    //                 'success' => true,
    //                 'message' => 'Invalid lisitng id'
    //             ], 200);
    //         }

    //         $data = $payload['data'] ?? [];
    //         if(!$data){
    //              return response()->json([
    //                 'success' => true,
    //                 'message' => 'Invalid data'
    //             ], 200);
    //         }


    //         $insertPriceData = [];
    //         $insertMinstayData = [];

    //         $priceDates = [];
    //         $minstayDates = [];

    //         foreach ($data as $row) {
    //             if(isset($row['price'])){
    //                 $insertPriceData[] = [
    //                     'ru_property_id' => $listingId,
    //                     'property_id' => $property->id,
    //                     'price' => $row['price'],
    //                     'price_date' => $row['date'],
    //                     'status' => 1,
    //                     'type' => 'unit',
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ];
    //                 $priceDates[] = $row['date'];


    //                 $priceXml = "<Push_PutPrices_RQ>
    //                     <Authentication>
    //                         <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
    //                         <Password>" . config('ru.RU_PASSWORD') . "</Password>
    //                     </Authentication>
    //                     <Prices PropertyID='" . $listingId . "'>
    //                         <Season DateFrom='" . date('Y-m-d', strtotime($row['date'])) . "' DateTo='" . date('Y-m-d', strtotime($row['date'])) . "'>
    //                             <Price>" . $row['price'] . "</Price>
    //                             <Extra>0</Extra>
    //                         </Season>
    //                     </Prices>
    //                 </Push_PutPrices_RQ>";
    //                 $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml);
    //             }

    //             if(isset($row['min_stay'])){
    //                 $insertMinstayData[] = [
    //                     'ru_property_id' => $listingId,
    //                     'home_id' =>$property->id,
    //                     'is_minstay_count' => $row['min_stay'],
    //                     'minstay_date' => $row['date'],
    //                     'status' => 1,
    //                     'type' => 'unit',
    //                     'created_at' => now(),
    //                     'updated_at' => now(),
    //                 ];
    //                 $minstayDates[] = $row['date'];

    //                 $minStayXml = "<Push_PutAvbUnits_RQ>
    //                     <Authentication>
    //                     <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
    //                     <Password>" . config('ru.RU_PASSWORD') . "</Password>
    //                     </Authentication>
    //                     <MuCalendar PropertyID='" . $listingId . "'>
    //                     <Date From='" . date('Y-m-d', strtotime($row['date'])) . "' To='" . date('Y-m-d', strtotime($row['date'])) . "'>
    //                         <U>1</U>
    //                         <MS>" . $row['min_stay'] . "</MS>
    //                         <C>4</C>
    //                     </Date>
    //                     </MuCalendar>
    //                 </Push_PutAvbUnits_RQ>";
    //                 $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml);
    //             }
    //         }

    //         $priceDates = array_unique($priceDates);
    //         $minstayDates = array_unique($minstayDates);
    //         if (!empty($insertPriceData)) {
    //             DB::table('ru_property_prices')->where('ru_property_id', $listingId)->whereIn('price_date', $priceDates)->delete();
    //             DB::table('ru_property_prices')->insert($insertPriceData);
    //         }
    //         if (!empty($insertMinstayData)) {
    //             DB::table('ru_property_minstay')->where('ru_property_id', $listingId)->whereIn('minstay_date', $minstayDates)->delete();
    //             DB::table('ru_property_minstay')->insert($insertMinstayData);
    //         }
    //         $pricelabPayload = $this->priceLabPayloadService->preparePriceLabsSyncTriggerCalendarPayload($listingId, $data);
    //         $this->propertyService->syncCalendars($pricelabPayload);
    //         return response()->json(['success' => true]);
    //     }
    //     catch(Exception $e){
    //          return response()->json(['success' => true]);
    //     }

    // }
    
    public function getSyncUrlData(Request $request){
        if (!isPriceLabEnable()) {
            abort(404);
        }
    
        try {
            $directory = 'priclabnoti';
    
            if (!Storage::exists($directory)) {
                Storage::makeDirectory($directory);
            }
    
            $filename = isset($request['listing_id'])
                ? $directory . '/' . $request['listing_id'] . '_sync_url_trigger_' . now()->format('Y_m_d_His') . '_' . uniqid() . '.json'
                : $directory . '/sync_url_trigger_' . now()->format('Y_m_d_His') . '_' . uniqid() . '.json';
    
            Storage::put($filename, json_encode($request->all(), JSON_PRETTY_PRINT));
            
            //$filename = 'priclabnoti/45305899_sync_url_trigger_2026_02_27_163252_69a179dcd717f.json';
            
            $payload = json_decode(Storage::get($filename), true);
    
            $listingId = $payload['listing_id'] ?? null;
           
            
            if (!$listingId) {
                return response()->json(['success' => true, 'message' => 'Invalid listing id']);
            }
    
            $property = TblHomeUnit::where('pricelabs_unique_id', $listingId)->first();
            if (!$property) {
                return response()->json(['success' => true, 'message' => 'Invalid listing id']);
            }
    
            $data = $payload['data'] ?? [];
            if (empty($data)) {
                return response()->json(['success' => true, 'message' => 'Invalid data']);
            }
    
            $insertPriceData = [];
            $insertMinstayData = [];
            $priceDates = [];
            $minstayDates = [];
    
            $priceSeasonsXml = '';
            $minStayDatesXml = '';
    
            foreach ($data as $row) {
                $date = date('Y-m-d', strtotime($row['date']));
    
                // PRICE
                if (isset($row['price'])) {
                    $insertPriceData[] = [
                        'ru_property_id' => $property->ru_property_id,
                        'property_id' => $property->id,
                        'price' => $row['price'],
                        'price_date' => $row['date'],
                        'status' => 1,
                        'type' => 'unit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
    
                    $priceDates[] = $row['date'];
    
                    $priceSeasonsXml .= "
                        <Season DateFrom='{$date}' DateTo='{$date}'>
                            <Price>{$row['price']}</Price>
                            <Extra>0</Extra>
                        </Season>";
                }
    
                // MIN STAY
                if (isset($row['min_stay'])) {
                    $insertMinstayData[] = [
                        'ru_property_id' => $property->ru_property_id,
                        'home_id' => $property->id,
                        'is_minstay_count' => $row['min_stay'],
                        'minstay_date' => $row['date'],
                        'status' => 1,
                        'type' => 'unit',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
    
                    $minstayDates[] = $row['date'];
    
                    $minStayDatesXml .= "
                        <Date From='{$date}' To='{$date}'>
                            <U>1</U>
                            <MS>{$row['min_stay']}</MS>
                            <C>4</C>
                        </Date>";
                }
            }
    
            // 🔥 RU PRICE BATCH CALL
            if ($priceSeasonsXml) {
                $priceXml = "
                <Push_PutPrices_RQ>
                    <Authentication>
                        <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                        <Password>" . config('ru.RU_PASSWORD') . "</Password>
                    </Authentication>
                    <Prices PropertyID='{$property->ru_property_id}'>
                        {$priceSeasonsXml}
                    </Prices>
                </Push_PutPrices_RQ>";
    
                MasterHelper::makeXmlRequest($priceXml);
            }
    
            // 🔥 RU MIN STAY BATCH CALL
            if ($minStayDatesXml) {
                $minStayXml = "
                <Push_PutAvbUnits_RQ>
                    <Authentication>
                        <UserName>" . config('ru.RU_USER_NAME') . "</UserName>
                        <Password>" . config('ru.RU_PASSWORD') . "</Password>
                    </Authentication>
                    <MuCalendar PropertyID='{$property->ru_property_id}'>
                        {$minStayDatesXml}
                    </MuCalendar>
                </Push_PutAvbUnits_RQ>";
    
                MasterHelper::makeXmlRequest($minStayXml);
            }
    
            // DB SYNC
            
            $listingId = $property->ru_property_id;
            DB::transaction(function () use (
                $listingId,
                $insertPriceData,
                $insertMinstayData,
                $priceDates,
                $minstayDates
            ) {
                if (!empty($insertPriceData)) {
                    DB::table('ru_property_prices')
                        ->where('ru_property_id', $listingId)
                        ->whereIn('price_date', array_unique($priceDates))
                        ->delete();
    
                    DB::table('ru_property_prices')->insert($insertPriceData);
                }
    
                if (!empty($insertMinstayData)) {
                    DB::table('ru_property_minstay')
                        ->where('ru_property_id', $listingId)
                        ->whereIn('minstay_date', array_unique($minstayDates))
                        ->delete();
    
                    DB::table('ru_property_minstay')->insert($insertMinstayData);
                }
            });
    
            // PriceLabs Sync
            $pricelabPayload = $this->priceLabPayloadService
                ->preparePriceLabsSyncTriggerCalendarPayload($listingId, $data);
    
            $this->propertyService->syncCalendars($pricelabPayload);
    
            return response()->json(['success' => true]);
    
        } catch (\Exception $e) {
            Log::error('RU Sync Failed', [
                'listing_id' => $request['listing_id'] ?? null,
                'error' => $e->getMessage()
            ]);
    
            return response()->json(['success' => false]);
        }
    }

    public function getHookUrlData(Request $request){
        if(!isPriceLabEnable()){
            abort('404');
        }
        $payload = $request->all();



        $directory = 'priclabnoti';
        if (!Storage::exists($directory)) {
            Storage::makeDirectory($directory);
        }
        $filename = $directory . '/webhook_calendar_trigger_' . now()->format('Y_m_d_His') . '_' . uniqid() . '.json';

        Storage::put($filename, json_encode($payload, JSON_PRETTY_PRINT));
        return response()->json(['success' => true]);
    }

    public function callPriceLabIntegrationApi(Request $request){

        DB::table('tbl_sitesettings')->where('id', 1)->update(['is_pricelab_enabled'=>$request->status]);

        return response()->json([
            'status'  => true,
            'message' => 'Updated successfully',
            'status'    => $request->status
        ], 200);
    }
}
