<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use URL;

class PropertyBookingPaymentRequest extends Model{

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'name',
        'booking_request_id',
        'property_booking_id',
        'email',
        'amount',
        'payment_mode',
        'booking_request_status',
        'status',
        'note',
        'mobile_no'
    ];

    // protected $appends = ['payment_link'];

    public function booking(){
        return $this->belongsTo(PropertyBooking::class, 'property_booking_id')->with(['property']);
    }

    // public function getPaymentLinkAttribute(){
        // return URL('/booking/part/payment/'.base64_encode($this->id));
    // }
}
