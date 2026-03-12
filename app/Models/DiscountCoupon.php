<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class DiscountCoupon extends Model{

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'coupon_code',
        'title',
        'start_date',
        'end_date',
        'is_offer_valid_only_for_first_time',
        'user_type',
        'use_limit',
        'discount_type',
        'discount',
        'generated_coupon_code_by',
        'coupon_valid_on_min_no_of_nights',
        'coupon_valid_on_min_total_booking_amount',
        'stay_date_from',
        'stay_date_to',
        'property_type_id',
        'property_id',
        'term_and_conditions',
        'status',
        'code',
        'prefix'
        //'no_of_codes',
    ];

    protected $appends = ['discount_percentage', 'discount_flat', 'coupon_code_self', 'coupon_code_auto', 'codes'];
    protected $casts = [
        'property_type_id' => 'string',
        'property_id' => 'string',
        
    ];

    public function getPropertyTypeIdAttribute($value){
        // if(!$value){
        //     $value = [];
        // }
        // else{
        //     $value= json_decode(str_replace('"', '', $value), true);
        // }
        // return $value;
        return $value ? json_decode($value, true) : [];
    }

    public function getPropertyIdAttribute($value){
        // if(!$value){
        //     $value = [];
        // }
        // else{
        //     $value= json_decode(str_replace('"', '', $value), true);
        // }
        // return $value;
        return $value ? json_decode($value, true) : [];
    }

    public function getDiscountPercentageAttribute(){
        $amount = NULL;
        if($this->discount_type !='flat'){
           $amount = $this->discount;
        }
        return $amount;
    }

    public function getDiscountFlatAttribute(){
        $amount = NULL;
        if($this->discount_type =='flat'){
           $amount = $this->discount;
        }
        return $amount;
    }


    public function getCouponCodeSelfAttribute(){
        $code = NULL;
        if($this->generated_coupon_code_by !='auto'){
           $code = $this->code;
        }
        return $code;
    }

    public function getCouponCodeAutoAttribute(){
        $code = NULL;
        if($this->generated_coupon_code_by =='auto'){
           $code = $this->code;
        }
        return $code;
    }

    public function getCodesAttribute(){
        if($this->generated_coupon_code_by =='auto'){
            $list = DiscountCouponCodeMapping::where('discount_coupon_id', $this->id)->pluck('code');
            return $list->implode(', ');
        }
        else{
            return $this->code;
        }
    }
}
