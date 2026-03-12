<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class DiscountCouponCodeMapping extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'code',
        'discount_coupon_id',
        'is_used',
    ];

    public function discountCoupon(){
        return $this->hasOne(DiscountCoupon::class, 'id', 'discount_coupon_id');
    }
}