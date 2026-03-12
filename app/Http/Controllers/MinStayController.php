<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use App\Models\RuPropertyMinstay;
use App\Models\TblHome;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\RuMinStay;
use DB;


class MinStayController extends Controller{
    public function syncMinStay($date, $id, $ptype){
        set_time_limit(0);
       // $property = TblHome::where('id', $id)->whereNotNull('ru_property_id')->first();
       

        if ($ptype == 'unit') {
            $property = TblHomeUnit::where('id', $id)->whereNotNull('ru_property_id')->first();   
        }
        if($ptype == 'multiunit') {
            $property = TblHomeMultiUnit::where('id', $id)->whereNotNull('ru_property_id')->first();
        }
        //dd($property->ru_property_id);
    
        $date = date('Y-m-d', strtotime($date));
        $minStayNo = 1;
        
        /* $minStayDetail = RuMinStay::where(['ru_property_id'=>$property->ru_property_id, 'minstay_date'=>$date, 'type'=>$ptype])->first();
      
        if($minStayDetail){
            $minStayNo = $minStayDetail->minstay;
        } */
        return $minStayNo;
    }
    
    
    public function syncMinStayByRu($date, $id){
        set_time_limit(0);
       
        $minStayNo = 1;
        
      
        
        $minStayDetail = RuMinStay::where(['ru_property_id'=>$id, 'minstay_date'=>$date])->first();
        
      
      
        if($minStayDetail){
            $minStayNo = $minStayDetail->is_minstay_count;
        }
        return $minStayNo;
    }


    public function syncMinStayFromTo($date, $id, $ptype){
        set_time_limit(0);
        //$property = TblHome::where('id', $id)->whereNotNull('ru_property_id')->first();

        if ($ptype == 'unit') {
            $property = TblHomeUnit::where('id', $id)->whereNotNull('ru_property_id')->first();   
        }
        if($ptype == 'multiunit') {
            $property = TblHomeMultiUnit::where('id', $id)->whereNotNull('ru_property_id')->first();
        }



        $client = new Client();
        $headers = ['Content-Type' => 'application/xml'];

        $from = $date;
        $to = date('Y-m-d', strtotime($date . ' +180 day'));
        $minStay = 1;

        $minStayDetail = RuMinStay::where(['ru_property_id'=>$property->ru_property_id, 'type'=>$ptype])->whereBetween('minstay_date', [$from, $to])->get(['minstay_date', 'is_minstay_count'])->toArray();

        $datesWithMinStay = array();
        if($minStayDetail){
            $datesWithMinStay = array_column($minStayDetail, 'minstay', 'minstay_date');
        }
        return $datesWithMinStay;
    }
    
    
    public function syncMinStayFromToWeb($date, $id, $ptype){
        set_time_limit(0);
        $from = $date;
        $to = date('Y-m-d', strtotime($date . ' +180 day'));
        $minStay = 1;
        $minStayDetail = RuMinStay::where(['home_id'=>$id, 'type'=>$ptype])->whereBetween('minstay_date', [$from, $to])->get(['minstay_date', 'is_minstay_count'])->toArray();
      
        $datesWithMinStay = array();
        if($minStayDetail){
            $datesWithMinStay = array_column($minStayDetail, 'is_minstay_count', 'minstay_date');
        }
       
        return $datesWithMinStay;
    }
}
