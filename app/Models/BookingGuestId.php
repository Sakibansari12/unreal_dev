<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BookingGuestId extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'property_booking_id',
        'property_name',
        'user_id',
        'name',
        'email',
        'mobile_no',
        'id_proof_img',
        'checkin_date',
        'checkout_date',
        'status',
        'dob',
        'anniversary',
        'id_proof_front',
    ];
    
    public function propertyBooking()
    {
        return $this->belongsTo(PropertyBooking::class, 'property_booking_id', 'id');
    }
}