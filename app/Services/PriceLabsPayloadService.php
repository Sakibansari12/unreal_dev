<?php

namespace App\Services;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use App\Models\TblHomeUnit;
use App\Models\TblHomeMultiUnit;
use App\Models\TblRoomMapping;
use App\Models\TblRoomWiseAmmenity;
use App\Models\TblHomeAmmenity;
use App\Models\RuPropertyBlocked;
use Carbon\Carbon;
use Config;
use DB;
use URL;



class PriceLabsPayloadService{

    protected string $type;
    protected int $days;
    public function __construct(){
        $this->type = 'RoomOnly';
        $this->days = 365;
    }

    public function preparePriceLabsListingPayload(TblHomeUnit $unit): array{
        $home = $unit->home;
        $pType = $unit->pType;
        $column = $pType == 'unit' ? 'unit_id' : 'multi_unit_id';
        
        $amenities = DB::table('tbl_home_amenities as ha')->select('ha.amenities_name')->join('tbl_amenities as a','a.id','=','ha.amenities_id')->where("ha.$column",$unit->id)->where('ha.pType',$pType)->where('ha.status',1)->get();

        $facilityArray = [];
        foreach ($amenities as $a) {                        
            $facilityArray[] = $a->amenities_name;
        }
        $listing = [
            'listing_id' => (string) $unit->pricelabs_unique_id,
            'name' => $unit->unit_name,
            'currency' => $home->currency_code ?? 'INR',
            'status' => 'available',
            'location' => [
                'latitude' => (float) $home->map_latitude,
                'longitude' => (float) $home->map_longitude,
                'city' => $home->location,
                'country' => 'IND',
            ],
            'number_of_bedrooms' => (int) $unit->no_of_rooms,
            
           
        ];
        if (count($facilityArray) > 0) {
            $listing['amenities'] = $facilityArray;
        }
        return [$listing];
    }

    public function preparePriceLabsRatePlanPayload(TblHomeUnit $unit): array{
        return [[
            'listing_id' => (string)  $unit->pricelabs_unique_id,
            'data' => [
                ['id' => 'IAPI-BAR_SGL-SGL', 'name' => 'Standard Rate', 'default' => true],
            ],
        ]];
    }

    public function preparePriceLabsCalendarPayload(TblHomeUnit $unit): array{
        // MUST include TODAY
        $start = now()->startOfDay();
        $end   = now()->addYear()->startOfDay();

        $data = [];

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'price' => (float) $unit->per_night_price,
                'available_units' => 1,
                'booked_units' => 0,
                'blocked_units' => 0,
                'settings' => [
                    'min_stay' => $unit->min_stay,
                    'check_in' => true,
                    'check_out' => true,
                    // 'weekly_discount' => 0,
                    // 'monthly_discount' => 0,
                    // 'extra_person_fee' => 0,
                    // 'extra_person_fee_trigger' => $unit->extra_guest_charges??0,
                ],
            ];
        }

        return [[
            'listing_id' => (string)  $unit->pricelabs_unique_id,
            'currency' => $unit->home->currency_code ?? 'INR',
            'data' => $data,
        ]];
    }


    public function preparePriceLabsIntegrationPayload(): array
    {
        return [
            'sync_url' => route('pricelabs.sync'),
            'calendar_trigger_url' => route('pricelabs.calendar.trigger'),
            'hook_url' => route('pricelabs.hook'),
            'features' => [
                'min_stay' => true,
                'check_in' => false,
                'check_out' => false,
                'monthly_weekly_discounts' => false,
                'extra_person_fee' => false,
            ],
        ];
    }

    public function prepareGetPricesPayload($id)
    {
        return [[
            'listing_id' => $id,
          
            'date_range' => [
                'start' => now()->format('Y-m-d'),
                'end'   => now()->addMonths(6)->format('Y-m-d'),
            ],
        ]];
    }


    public function prepareStatusPayload(TblHomeUnit $unit): array
    {
        return [[
            'id' => (string)  $unit->pricelabs_unique_id,
            'type' => 'calendar',
        ]];
    }

    public function prepareSyncUrlPayload(): array
    {
        return [
            'sync_url' => route('pricelabs.calendar.trigger'),
        ];
    }

    public function preparePriceLabsGetPricesPayload(TblHomeUnit $unit): array{
        return [
            'listings' => [
                [
                    'listing_id' => (string)  $unit->pricelabs_unique_id,
                    'rate_plan_ids' => ['IAPI-FLEX-SGL'],
                    'date_range' => [
                        'start' => now()->format('Y-m-d'),
                        'end'   => now()->addMonths(6)->format('Y-m-d'),
                    ],
                    'settings' => [
                        'include_derived' => true,
                        'include_restrictions' => true,
                    ],
                ],
            ],
        ];
    }
    
    
    public function updateAvaliabilityWithReservation($ru_property_id, $date_from, $date_to): array{
        // MUST include TODAY
        $start = Carbon::parse($date_from)->startOfDay();
        $end   = Carbon::parse($date_to)->startOfDay();
        
        $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();

        $data = [];
        
        $isRecordFirstDateExists = RuPropertyBlocked::where('ru_property_id', $ru_property_id)->where('date_to', $start->format('Y-m-d'))->exists();
        $isRecordLastDateExists = RuPropertyBlocked::where('ru_property_id', $ru_property_id)->where('date_from', $end->format('Y-m-d'))->exists();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $isLastDate = $date->equalTo($end);
            
            $availableUnit = 0;
            $bookedUnit = 1;
            $blockUnit = 0;
            
            if($isLastDate == true){
                $availableUnit = 1;
                $bookedUnit = 0;
                $blockUnit = 0;
            }
            
            if($date->format('Y-m-d') == $end->format('Y-m-d')){
                if($isRecordLastDateExists){
                    $availableUnit = 0;
                    $blockUnit = 1;
                }
            }
            
            
            $data[] = [
                'date'  => $date->format('Y-m-d'),
                'available_units' => $availableUnit,
                'booked_units'    => $bookedUnit,
                'blocked_units'   => $blockUnit,
            ];
        }

        return [[
            'listing_id'   => (string) $property->pricelabs_unique_id,
            'currency'     => 'INR',
            'data'         => $data,
        ]];
    }



    public function preparePriceLabsBlockUnblockPayload($ru_property_id, $date_from, $date_to, $blocked_units): array{
        // MUST include TODAY
        $start = Carbon::parse($date_from)->startOfDay();
        $end   = Carbon::parse($date_to)->startOfDay();
        
        $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();
     
        $data = [];
        
        $availableUnit = 1;
        $bookedUnit = 0;
        $blockUnit = 0;
       
        if($blocked_units == 0){
            $availableUnit = 0;
            $blockUnit = 1;
        } 
        
        $isRecordFirstDateExists = RuPropertyBlocked::where('ru_property_id', $ru_property_id)->where('date_to', $start->format('Y-m-d'))->exists();
        $isRecordLastDateExists = RuPropertyBlocked::where('ru_property_id', $ru_property_id)->where('date_from', $end->format('Y-m-d'))->exists();
       
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            if($date->format('Y-m-d') == $end->format('Y-m-d')){
                $availableUnit = 1;
                $blockUnit = 0;
                
                if($isRecordLastDateExists){
                    $availableUnit = 0;
                    $blockUnit = 1;
                }
            }
        
            if($date->format('Y-m-d') == $start->format('Y-m-d')){
                if($isRecordFirstDateExists){
                    $availableUnit = 0;
                    $blockUnit = 1;
                }
            }
            
            if($blocked_units == 1){
                $availableUnit = 1;
                $blockUnit = 0;
                
                // if($date->format('Y-m-d') == $start->format('Y-m-d')){
                //     if($isRecordFirstDateExists){
                //         $availableUnit = 0;
                //         $blockUnit = 1;
                //     }
                // }
                
                if($date->format('Y-m-d') == $end->format('Y-m-d')){
                    
                    if($isRecordLastDateExists){
                        $availableUnit = 0;
                        $blockUnit = 1;
                    }
                }
                
                
            }
            $data[] = [
                'date'  => $date->format('Y-m-d'),
                'available_units' => $availableUnit,
                'booked_units' => $bookedUnit,
                'blocked_units' => $blockUnit,
            ];
        }
        
     
        return [[
            'listing_id'=> (string) $property->pricelabs_unique_id,
            'currency' => 'INR',
            'data' => $data,
        ]];
    }
    
    
    public function preparePriceLabsBlockUnblockOnBookingCancelOrDelete($ru_property_id, $date_from, $date_to): array{
        // MUST include TODAY
        $start = Carbon::parse($date_from)->startOfDay();
        $end   = Carbon::parse($date_to)->startOfDay();
        
        $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();
     
        $data = [];
        
        $availableUnit = 1;
        $bookedUnit = 0;
        $blockUnit = 0;
        
        $isRecordLastDateExists = RuPropertyBlocked::where('ru_property_id', $ru_property_id)->where('date_from', $end->format('Y-m-d'))->exists();
        
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            
            if($date->format('Y-m-d') == $end->format('Y-m-d')){
                if($isRecordLastDateExists){
                    $availableUnit = 0;
                    $blockUnit = 1;
                }
            }
            
            $data[] = [
                'date'  => $date->format('Y-m-d'),
                'available_units' => $availableUnit,
                'booked_units' => $bookedUnit,
                'blocked_units' => $blockUnit,
            ];
        }
      
      
        return [[
            'listing_id'=> (string) $property->pricelabs_unique_id,
            'currency' => 'INR',
            'data' => $data,
        ]];
    }
    
    
    public function preparePriceLabsSyncUnconfirmedReservation($propertyBooking): array{
        $home = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
        return [
            "reservations" => [
                [
                    "listing_id" => $home->pricelabs_unique_id,
                    "data" => [
                        [
                            "reservation_id" => 'REV'.$propertyBooking->booking_id ?? rand(100000,999999),
                            "cancel_time" => null,
                            "start_date" => date('Y-m-d', strtotime($propertyBooking->checkin_date)),
                            "end_date" => date('Y-m-d', strtotime($propertyBooking->checkout_date)),
                            "booked_time" => date('Y-m-d'),
                            "total_days" => (int)$propertyBooking->no_of_nights,
                            "total_cost" => round($propertyBooking->payable_amount),
                            "total_fees" => $propertyBooking->tot_additional_charge?$propertyBooking->tot_additional_charge:0,
                            "total_taxes" => round($propertyBooking->tax_amount),
                            "host_payout" => round($propertyBooking->payable_amount),
                            "ota_commission" => 0,
                            "rental_revenue" => $propertyBooking->base_price ?? 0,
                            "currency" => "INR",
                            "status" => "booked"
                        ]
                    ]
                ]
            ]
        ];
    }
    
    public function preparePriceLabsSyncConfirmedReservation($propertyBooking): array{
        $home = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
        return [
            "reservations" => [
                [
                    "listing_id" => $home->pricelabs_unique_id,
                    "data" => [
                        [
                            "reservation_id" => 'REV'.$propertyBooking->booking_id ?? rand(100000,999999),
                            "cancel_time" => null,
                            "start_date" => date('Y-m-d', strtotime($propertyBooking->checkin_date)),
                            "end_date" => date('Y-m-d', strtotime($propertyBooking->checkout_date)),
                            "booked_time" => date('Y-m-d'),
                            "total_days" => (int)$propertyBooking->no_of_nights,
                            "total_cost" => round($propertyBooking->payable_amount),
                            "total_fees" => $propertyBooking->tot_additional_charge?$propertyBooking->tot_additional_charge:0,
                            "total_taxes" => round($propertyBooking->tax_amount),
                            "host_payout" => round($propertyBooking->payable_amount),
                            "ota_commission" => 0,
                            "rental_revenue" => $propertyBooking->base_price ?? 0,
                            "currency" => "INR",
                            "status" => "booked"
                        ]
                    ]
                ]
            ]
        ];
    }
    
    
    public function preparePriceLabsReservationCancellationPayload($propertyBooking): array{
        $home = TblHomeUnit::where('id', $propertyBooking->property_id)->first();
        return [
            "reservations" => [
                [
                    "listing_id" => $home->pricelabs_unique_id,
                    "data" => [
                        [
                            "reservation_id" => 'REV'.$propertyBooking->booking_id ?? rand(100000,999999),
                            "cancel_time" => date('Y-m-d'),
                            "start_date" => date('Y-m-d', strtotime($propertyBooking->checkin_date)),
                            "end_date" => date('Y-m-d', strtotime($propertyBooking->checkout_date)),
                            "booked_time" => date('Y-m-d'),
                            "total_days" => (int)$propertyBooking->no_of_nights,
                            "total_cost" => round($propertyBooking->payable_amount),
                            "total_fees" => $propertyBooking->tot_additional_charge?(int)$propertyBooking->tot_additional_charge:0,
                            "total_taxes" => round($propertyBooking->tax_amount),
                            "host_payout" => (int)round($propertyBooking->payable_amount),
                            "ota_commission" => 0,
                            "rental_revenue" => $propertyBooking->base_price ? (int)$propertyBooking->base_price: 0,
                            "currency" => "INR",
                            "status" => "canceled"
                        ]
                    ]
                ]
            ]
        ];
    }
    
    
    public function preparePriceLabsSyncReservationOnDateBlock($ru_property_id, $date_from, $date_to): array{
        $start = Carbon::parse($date_from)->startOfDay();
        $end   = Carbon::parse($date_to)->startOfDay();
        $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();
        return [
            "reservations" => [
                [
                    "listing_id" => $property->pricelabs_unique_id,
                    "data" => [
                        [
                            "reservation_id" => 'BLOCK'. str_replace('-', '', $date_from). str_replace('-', '', $date_to),
                            "cancel_time" => null,
                            "start_date" => date('Y-m-d', strtotime($date_from)),
                            "end_date" => date('Y-m-d', strtotime($date_to)),
                            "booked_time" => date('Y-m-d'),
                            "total_days" => (int)$start->diffInDays($end),
                            "total_cost" => 0,
                            "total_fees" => 0,
                            "total_taxes" => 0,
                            "host_payout" => 0,
                            "ota_commission" => 0,
                            "rental_revenue" => 0,
                            "currency" => "INR",
                            "status" => "blocked"
                        ]
                    ]
                ]
            ]
        ];
    }
    
    
    public function preparePriceLabsCancelReservationOnDateBlock($ru_property_id, $date_from, $date_to): array{
        $start = Carbon::parse($date_from)->startOfDay();
        $end   = Carbon::parse($date_to)->startOfDay();
        $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();
        return [
            "reservations" => [
                [
                    "listing_id" => $property->pricelabs_unique_id,
                    "data" => [
                        [
                            "reservation_id" => 'BLOCK'. str_replace('-', '', $date_from). str_replace('-', '', $date_to),
                            "cancel_time" => date('Y-m-d'),
                            "start_date" => date('Y-m-d', strtotime($date_from)),
                            "end_date" => date('Y-m-d', strtotime($date_to)),
                            "booked_time" => date('Y-m-d'),
                            "total_days" => (int)$start->diffInDays($end),
                            "total_cost" => 0,
                            "total_fees" => 0,
                            "total_taxes" => 0,
                            "host_payout" => 0,
                            "ota_commission" => 0,
                            "rental_revenue" => 0,
                            "currency" => "INR",
                            "status" => "canceled"
                        ]
                    ]
                ]
            ]
        ];
    }
    
    public function preparePriceLabsCalendarPayloadOnUpdate($ru_property_id, $date_from, $date_to, $price, $minstay){
        // MUST include TODAY
        $start = Carbon::parse($date_from)->startOfDay();
        $end   = Carbon::parse($date_to)->startOfDay();
        $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();

        $data = [];
        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $settings = [];
            if($minstay !=''){
                $settings['min_stay'] = (int)$minstay;
            }
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'price' => (float) $price,
                'settings' => $settings,
            ];
        }

        return [[
            'listing_id' => (string) $property->pricelabs_unique_id,
            'currency' => 'INR',
            'data' => $data,
        ]];
    }
    
    
    public function preparePriceLabsCalendarTriggerPayload($ru_property_id, $date_from, $date_to){
        
        $start = Carbon::parse($date_from)->startOfDay();
        $end   = Carbon::parse($date_to)->startOfDay();
        
        $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();

        $data = [];
        
        $isRecordLastDateExists = RuPropertyBlocked::where('ru_property_id', $ru_property_id)->where('date_from', $end->format('Y-m-d'))->exists();

        for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
            $availableUnit = 1;
            $bookedUnit = 0;
            $blockUnit = 0;
            
            $listingPrice = DB::table('ru_property_prices')->where('ru_property_id', $ru_property_id)->where('price_date', $date->format('Y-m-d'))->first();
            if(!$listingPrice){
                $listingPrice = DB::table('ru_property_prices')->where('ru_property_id', $ru_property_id)->first();
            }
            $listingMinstay = DB::table('ru_property_minstay')->where('ru_property_id', $ru_property_id)->where('minstay_date', $date->format('Y-m-d'))->first();
            if(!$listingMinstay){
                $listingMinstay = DB::table('ru_property_minstay')->where('ru_property_id', $ru_property_id)->first();
            }
            $listningavaliability = DB::table('ru_property_availabilities')->where('ru_property_id', $ru_property_id)->where('availability_date', $date->format('Y-m-d'))->first();
            
            if($listningavaliability){
                if($listningavaliability->is_available == 'yes'){
                    $availableUnit = 1;
                    $bookedUnit = 0;
                }
                else{
                    $availableUnit = 0;
                    $bookedUnit = 1;
                }
            }
            
            $isRecordStartDateExists = RuPropertyBlocked::where('ru_property_id', $ru_property_id)->where('date_from', $date->format('Y-m-d'))->exists();
            $isRecordLastDateExists = RuPropertyBlocked::where('ru_property_id', $ru_property_id)->where('date_to', $date->format('Y-m-d'))->exists();
            
            if(!$isRecordStartDateExists && $isRecordLastDateExists){
                $availableUnit = 1;
                $bookedUnit = 0;
            }
            
            $settings = [];
            
            
            $settings['min_stay'] = $listingMinstay->is_minstay_count;
            
            if($date->format('Y-m-d') == $end->format('Y-m-d')){
                if($isRecordLastDateExists){
                    $availableUnit = 0;
                    $blockUnit = 1;
                }
            }
            $data[] = [
                'date' => $date->format('Y-m-d'),
                'available_units' => $availableUnit,
                'booked_units' => $bookedUnit,
                'blocked_units' => $blockUnit,
                'price' => (float) $listingPrice->price,
                'settings' => $settings,
            ];
        }

        return [[
            'listing_id' => (string) $property->pricelabs_unique_id,
            'currency' => 'INR',
            'data' => $data,
        ]];
    }
    
    
    
    public function preparePriceLabsSyncTriggerCalendarPayload($ru_property_id, $rows){
        $property = TblHomeUnit::where('ru_property_id', $ru_property_id)->first();
        $data = [];
        foreach ($rows as $row) {
            $settings = [];
            if(isset($row['min_stay'])){
                $settings['min_stay'] = $row['min_stay'];
            }
            
            
            $data[] = [
                'date' => $row['date'],
                'price' =>  $row['price'],
                'settings' => $settings,
            ];
        }

        return [[
            'listing_id' => (string) $property->pricelabs_unique_id,
            'currency' => 'INR',
            'data' => $data,
        ]];
    }
}
