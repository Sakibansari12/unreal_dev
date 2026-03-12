<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class BookingEnquiry extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'user_id',
        'parent_user_id',
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
        'pType',
        'property_name',
    ];

    protected $appends = ['property_detail'];
   
    // BookingEnquiry.php
    public function getPropertyDetailAttribute()
    {
       // dd($this->pType);
       if ($this->pType === 'unit') {
        return TblHomeUnit::select('id', 'unit_name')->find($this->property_id);
    } elseif ($this->pType === 'multiunit') {
        return TblHomeMultiUnit::select('id', 'unit_name')->find($this->property_id);
    }
    
    return null;
    }

    // public function location(){
    //     return $this->belongsTo(TblLocation::class, 'location_id');
    // }

    // public function property(){
    //     return $this->belongsTo(TblHome::class, 'property_id')->with(['homeImage', 'owner']);
    // }


    protected function checkinDate(): Attribute{
       
        return Attribute::make(
            get: fn (string $value) => date('j F Y', strtotime($value)),
        );
    }

    protected function checkoutDate(): Attribute{
        return Attribute::make(
            get: fn (string $value) => date('j F Y', strtotime($value)),
        );
    }
    
    public function propertyLocation()
    {
        return $this->belongsTo(TblLocation::class, 'location_id');
    }
}