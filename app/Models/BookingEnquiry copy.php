<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BookingEnquiry extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'property_id',
        'no_of_guest',
        'no_of_night',
        'name',
        'phone_no',
        'email',
        'enquiry_message',
        'total_amount',
        'checkin_date',
        'checkout_date',
        'enquiry_status',
        'email_status',
        'payment_link',
        'payment_status',
    ];

    public function location(){
        return $this->belongsTo(TblLocation::class, 'location_id');
    }

    public function property(){
        return $this->belongsTo(TblHome::class, 'property_id')->with(['homeImage', 'owner']);
    }
}
