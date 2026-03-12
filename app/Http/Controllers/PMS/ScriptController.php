<?php

namespace App\Http\Controllers\PMS;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Pagination\Paginator;


use Carbon\Carbon;
use ScssPhp\ScssPhp\Compiler;
use App\Services\PropertyService;
use URL;
use Carbon\CarbonPeriod;

use App\helper\MasterHelper;
use App\Models\TblHomeUnit;
use App\Models\RuPropertyPrice;
use App\Models\RuPropertyAvailability;
use App\Models\RuPropertyMinstay;

class ScriptController extends Controller{
    protected $propertyService;

    public function __construct(){
        $this->propertyService = new PropertyService();
    }

    
    public function index(Request $request){
        set_time_limit(0);
        $properties = TblHomeUnit::whereNotNull('ru_property_id')->where('id', '67')->get(['id', 'ru_property_id']);
       
        foreach($properties as $property){
            $priceDetail = RuPropertyPrice::where('ru_property_id', $property->ru_property_id)->orderBy('id', 'desc')->first();
            $avaliabilityDetail = RuPropertyAvailability::where('ru_property_id', $property->ru_property_id)->orderBy('id', 'desc')->first();
            $minstayDetail = RuPropertyMinstay::where('ru_property_id', $property->ru_property_id)->orderBy('id', 'desc')->first();
            
          
            $dateTo = '2026-06-31';
            
            if($minstayDetail){
                
                
                
                $dateFrom = $minstayDetail->minstay_date;
                $start = strtotime($dateFrom);
                $end = strtotime($dateTo);
                for ($i = $start; $i <= $end; $i = strtotime('+1 day', $i)) {
                    $price_date = date('Y-m-d', $i);
                    
                    $minstayDetail = [
                            'is_minstay_count' => 2,
                            'home_id' => $property->id,
                            'type' => 'unit',
                            'minstay_date' => $price_date,
                            'ru_property_id' => $property->ru_property_id,
                        ];
                        
                        
                  
                    RuPropertyMinstay::updateOrCreate(
                        [
                            'ru_property_id' => $property->ru_property_id,
                            'minstay_date' => $price_date,
                        ],
                        $minstayDetail  
                    );
                }
                
                $minStayXml = "<Push_PutAvbUnits_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <MuCalendar PropertyID='".$property->ru_property_id."'>
                        <Date From='".$dateFrom."' To='".$dateTo."'>
                            <U>1</U>
                            <MS>2</MS>
                            <C>4</C>
                        </Date>
                        </MuCalendar>
                    </Push_PutAvbUnits_RQ>";
                $ruResponse = MasterHelper::makeXmlRequest($minStayXml);
                
            }
            if($priceDetail){
                $dateFrom = $priceDetail->price_date;
                
                $start = strtotime($dateFrom);
                $end = strtotime($dateTo);
    
                for ($i = $start; $i <= $end; $i = strtotime('+1 day', $i)) {
                    $price_date = date('Y-m-d', $i);
    
                    RuPropertyPrice::updateOrCreate(
                        [
                            'ru_property_id' => $property->ru_property_id,
                            'price_date' => $price_date,
                        ],
                        [
                            'price' => $priceDetail->price,
                            'property_id' => $property->id,
                            'type' => 'unit',
                            'price_date' => $price_date,
                             'ru_property_id' => $property->ru_property_id,
                        ]
                    );
                }
                
                $priceXml = "<Push_PutPrices_RQ>
                        <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                        </Authentication>
                        <Prices PropertyID='".$property->ru_property_id."'>
                        <Season DateFrom='".$dateFrom."' DateTo='".$dateTo."'>
                            <Price>".$priceDetail->price."</Price>
                            <Extra>0</Extra>
                        </Season>
                        </Prices>
                </Push_PutPrices_RQ>";
                $ruPropertyPriceResponse = MasterHelper::makeXmlRequest($priceXml);
                
            }
            
            if($avaliabilityDetail){
                $dateFrom = $avaliabilityDetail->avaliability_date;
                $start = strtotime($dateFrom);
                $end = strtotime($dateTo);
                for ($i = $start; $i <= $end; $i = strtotime('+1 day', $i)) {
                    $price_date = date('Y-m-d', $i);
    
                    RuPropertyAvailability::updateOrCreate(
                        [
                            'ru_property_id' => $property->ru_property_id,
                            'availability_date' => $price_date,
                        ],
                        [
                            'is_available' => 'yes',
                            'property_id' => $property->id,
                            'type' => 'unit',
                            'availability_date' => $price_date,
                             'ru_property_id' => $property->ru_property_id,
                        ]
                    );
                }
                
                $xmlAvaliability = "<Push_PutAvbUnits_RQ>
                    <Authentication>
                        <UserName>".config('ru.RU_USER_NAME')."</UserName>
                        <Password>".config('ru.RU_PASSWORD')."</Password>
                    </Authentication>
                    <MuCalendar PropertyID='".$property->ru_property_id."'>
                        <Date From='".$dateFrom."' To='".$dateTo."'>
                            <U>1</U>
                            <C>4</C>
                        </Date>
                    </MuCalendar>
                </Push_PutAvbUnits_RQ>";
                $ruResponse = MasterHelper::makeXmlRequest($xmlAvaliability);
                
            }
            
            
            
        }
        
        dd('done');
        
    }


  
}