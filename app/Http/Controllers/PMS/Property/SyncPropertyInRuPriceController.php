<?php

namespace App\Http\Controllers\PmsApi;

use App\Http\Controllers\Controller;
use App\Models\TblHomeUnit;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Models\TblLocation;
use App\Models\TblRuLocation;
use App\Models\TblHomeType;
use App\Models\RuPropertyPrice;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyMinstay;
use App\Models\PropertyBooking;
use App\helper\MasterHelper;
use App\Models\TblGst;
use Carbon\Carbon;
use App\Models\TblHomeMultiUnit;

class SyncPropertyInRuPriceController extends Controller{


    public function getBookingById($id){
        $detail = PropertyBooking::where('id', $id)->first();
        $detail->additional_charges = ($detail->additional_charges_detail)?json_decode($detail->additional_charges_detail):NULL;
        $detail->customer_detail = ($detail->customer_detail)?json_decode($detail->customer_detail):NULL;
        $gst_slab = TblGst::get();
        if($detail->pType =='unit'){
            $propertyDetail  = TblHomeUnit::where('id', $detail->property_id)->first(['tbl_home_units.*', 'tbl_home_units.unit_name as home_name']);
        }
        else{
            $propertyDetail  = TblHomeMultiUnit::where('id', $detail->property_id)->first(['tbl_home_multi_units.*', 'tbl_home_multi_units.unit_name as home_name']);
        }
        return response()->json([
            'status' => true,
            'data' =>$detail,
            'gst_slab'=>$gst_slab,
            'propertyDetail'=>$propertyDetail,
            'message' => 'Data Changed Successfully.'
        ], 200);
    }

   

    public function updateRuPrice(Request $request){
        
       
        if($request->type =='unit'){
            $property = TblHomeUnit::where('id', $request->home_id)->first();
            if($property){
                RuPropertyPrice::whereBetween('price_date', [date('Y-m-d', strtotime($request->date_from)), date('Y-m-d', strtotime($request->date_to))])->where('type', 'unit')->where('property_id', $property->id)->update(['price'=>$request->per_night_price]);
                $priceXml = "<Push_PutPrices_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <Prices PropertyID='".$property->ru_property_id."'>
                        <Season DateFrom='".date('Y-m-d', strtotime($request->date_from))."' DateTo='".date('Y-m-d', strtotime($request->date_to))."'>
                            <Price>".$request->per_night_price."</Price>
                            <Extra>0</Extra>
                        </Season>
                        </Prices>
                </Push_PutPrices_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml);   
                if($request->min_stay !=''){
                    $unit = 1;
                    $minStayXml = "<Push_PutAvbUnits_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <MuCalendar PropertyID='".$property->ru_property_id."'>
                        <Date From='".date('Y-m-d', strtotime($request->date_from))."' To='".date('Y-m-d', strtotime($request->date_to))."'>
                            <U>".$unit."</U>
                            <MS>".$request->min_stay."</MS>
                            <C>4</C>
                        </Date>
                        </MuCalendar>
                    </Push_PutAvbUnits_RQ>";
                    $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml); 
                    $startDate = Carbon::create($request->date_from);
                    $endDate = Carbon::create($request->date_to);
                    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                        $dateString = $date->toDateString();
                        $minStay = RuPropertyMinstay::firstOrNew(['home_id'=>$request->home_id, 'minstay_date'=>$dateString,  'type'=>'unit']);
                        $minStay->ru_property_id = $property->ru_property_id;
                        $minStay->home_id = $property->id;
                        $minStay->minstay_date = $dateString;
                        $minStay->is_minstay_count = $request->min_stay;
                        $minStay->save(); 
                    }
                }
                
                
                $type = 'yes';
                $u = 1;
                if($request->avaliability == 1){
                    $u = 0;
                    $type = 'no';
                }
                
              
           
                // RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($request->date_to)), $request->date_from])->where('type', 'unit')->update(['is_available'=>'yes']);
                
                
                // $xml = "<Push_PutAvbUnits_RQ>
                //             <Authentication>
                //                 <UserName>".config('ru.RU_USER_NAME')."</UserName>
                //                 <Password>".config('ru.RU_PASSWORD')."</Password>
                //             </Authentication>
                //             <MuCalendar PropertyID='".$property->ru_property_id."'>
                //                 <Date From='".date('Y-m-d', strtotime($request->date_from))."' To='".date('Y-m-d', strtotime($request->date_to))."'>
                //                     <U>".$u."</U>
                //                     <C>4</C>
                //                 </Date>
                //             </MuCalendar>
                //         </Push_PutAvbUnits_RQ>";
                // $xmlResponse = MasterHelper::makeXmlRequest($xml);
                
               
                return response()->json([
                    'status' => true,
                    'data' =>[],
                    'message' => 'Data Changed Successfully.'
                ], 200);
            }
            
            else{
                return response()->json([
                    'status' => false,
                    'data' =>[],
                    'message' => 'Property Not Found!.'
                ], 200);
            }
        }
        
        else if($request->type =='multiunit'){
            $property = TblHomeMultiUnit::where('id', $request->home_id)->first();
            if($property){
                RuPropertyPrice::whereBetween('price_date', [date('Y-m-d', strtotime($request->date_from)), date('Y-m-d', strtotime($request->date_to))])->where('type', 'multiunit')->update(['price'=>$request->per_night_price]);
                $priceXml = "<Push_PutPrices_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <Prices PropertyID='".$property->ru_property_id."'>
                        <Season DateFrom='".date('Y-m-d', strtotime($request->date_from))."' DateTo='".date('Y-m-d', strtotime($request->date_to))."'>
                            <Price>".$request->per_night_price."</Price>
                            <Extra>0</Extra>
                        </Season>
                        </Prices>
                </Push_PutPrices_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml);   
                if($request->min_stay !=''){
                    $unit = 1;
                    $minStayXml = "<Push_PutAvbUnits_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <MuCalendar PropertyID='".$property->ru_property_id."'>
                        <Date From='".date('Y-m-d', strtotime($request->date_from))."' To='".date('Y-m-d', strtotime($request->date_to))."'>
                            <U>".$unit."</U>
                            <MS>".$request->min_stay."</MS>
                            <C>4</C>
                        </Date>
                        </MuCalendar>
                    </Push_PutAvbUnits_RQ>";
                    $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml); 
                    $startDate = Carbon::create($request->date_from);
                    $endDate = Carbon::create($request->date_to);
                    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                        $dateString = $date->toDateString();
                        $minStay = RuPropertyMinstay::firstOrNew(['home_id'=>$request->home_id, 'minstay_date'=>$dateString, 'type'=>'multiunit']);
                        $minStay->ru_property_id = $property->ru_property_id;
                        $minStay->home_id = $property->id;
                        $minStay->minstay_date = $dateString;
                        $minStay->is_minstay_count = $request->min_stay;
                        $minStay->save(); 
                    }
                }
                
                $type = 'yes';
                if($request->avaliability == 1){
                    $u = 1;
                }
                else{
                    $u = 0;
                    $type = 'no';
                }
                // RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->whereBetween('availability_date', [date('Y-m-d', strtotime($request->date_to)), $request->date_from])->where('type', 'multiunit')->update(['is_available'=>'yes']);
                
                
                // $xml = "<Push_PutAvbUnits_RQ>
                //             <Authentication>
                //                 <UserName>".config('ru.RU_USER_NAME')."</UserName>
                //                 <Password>".config('ru.RU_PASSWORD')."</Password>
                //             </Authentication>
                //             <MuCalendar PropertyID='".$property->ru_property_id."'>
                //                 <Date From='".date('Y-m-d', strtotime($request->date_from))."' To='".date('Y-m-d', strtotime($request->date_to))."'>
                //                     <U>".$u."</U>
                //                     <C>4</C>
                //                 </Date>
                //             </MuCalendar>
                //         </Push_PutAvbUnits_RQ>";
                // $xmlResponse = MasterHelper::makeXmlRequest($xml);
                
                return response()->json([
                    'status' => true,
                    'data' =>[],
                    'message' => 'Data Changed Successfully.'
                ], 200);
            }
            
            else{
                return response()->json([
                    'status' => false,
                    'data' =>[],
                    'message' => 'Property Not Found!.'
                ], 200);
            }
        }
        
    }


    public function updatePrice($ruId, $price){
        if($ruId){
            if($ruId !=''){
                $minStayXml = "<Push_PutPrices_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <Prices PropertyID='".$ruId."'>
                        <Season DateFrom='2024-12-03' DateTo='2025-05-03'>
                            <Price>".$price."</Price>
                            <Extra>0</Extra>
                        </Season>
                        </Prices>
                    </Push_PutPrices_RQ>";
              
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml); 
            }

            return response()->json([
                'status' => true,
                'data' =>[],
                'message' => 'Data Changed Successfully.'
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
                'data' =>[],
                'message' => 'Property Not Found!.'
            ], 200);
        }
    }


    public function updateRuMinStay($ruId, $minStay){
       
        if($minStay){
            $minStayXml = "<Push_PutAvbUnits_RQ>
                <Authentication>
                <UserName>".config('ru.RU_USER_NAME')."</UserName>
                <Password>".config('ru.RU_PASSWORD')."</Password>
                </Authentication>
                <MuCalendar PropertyID='".$ruId."'>
                <Date From='".date('Y-m-d', strtotime('2024-12-03'))."' To='".date('Y-m-d', strtotime('2025-05-03'))."'>
                    <U>1</U>
                    <MS>".$minStay."</MS>
                    <C>4</C>
                </Date>
                </MuCalendar>
            </Push_PutAvbUnits_RQ>";
            $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml);
        }     
    }


    public function updateMinStay($id){
        $property = TblHome::where('id', $id)->first();
        if($property){
            if($property->min_stay !=''){
                $unit = 1;
                $minStayXml = "<Push_PutAvbUnits_RQ>
                    <Authentication>
                    <UserName>".config('ru.RU_USER_NAME')."</UserName>
                    <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <MuCalendar PropertyID='".$property->ru_property_id."'>
                    <Date From='".date('Y-m-d', strtotime('2024-12-03'))."' To='".date('Y-m-d', strtotime('2025-05-03'))."'>
                        <U>".$unit."</U>
                        <MS>".$property->min_stay."</MS>
                        <C>4</C>
                    </Date>
                    </MuCalendar>
                </Push_PutAvbUnits_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($minStayXml); 
             
                $startDate = Carbon::create('2024-12-02');
                $endDate = Carbon::create('2025-05-02');
                for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                    $dateString = $date->toDateString();
                    $minStay = RuPropertyMinstay::firstOrNew(['home_id'=>$property->id, 'minstay_date'=>$dateString]);
                    $minStay->ru_property_id = $property->ru_property_id;
                    $minStay->home_id = $property->id;
                    $minStay->minstay_date = $dateString;
                    $minStay->is_minstay_count = $property->min_stay;
                    $minStay->save(); 
                }
            }

            return response()->json([
                'status' => true,
                'data' =>[],
                'message' => 'Data Changed Successfully.'
            ], 200);
        }
        else{
            return response()->json([
                'status' => false,
                'data' =>[],
                'message' => 'Property Not Found!.'
            ], 200);
        }
    }
}
