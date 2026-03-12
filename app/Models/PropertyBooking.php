<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class PropertyBooking extends Model{

    use HasFactory, SoftDeletes;
    protected $fillable = [
        'booking_id',
        'website_markup_price',
        'user_id',
        'parent_user_id',
        'location_id',
        'property_id',
        'total_amount',
        'payable_amount',
        'paid_amount',
        'tax_amount',
        'discount_amount',
        'transcation_id',
        'customer_detail',
        'provider',
        'booking_status',
        'booking_created_by',
        'no_of_adult',
        'no_of_children',
        'checkin_date',
        'checkout_date',
        'type',
        'status',
        'additional_charges',
        'booking_from',
        'ru_booking_status',
        'is_blocking_hour',
        'channel',
        'per_night_price',
        'no_of_nights',
        'tax',
        'additional_charges_detail',
        'additional_charges_discount',
        'tot_additional_charge',
        'base_price',
        'extra_guest_charge',
        'taxable_amount',
        'customer_location_detail',
        'is_company_info',
        'customer_company_info',
        'applied_discount_coupon',
        'invoice_serial_no',
        'invoice_year',
        'invoice_no',
        'invoice',
        'invoice_file',
        'property_booking_status',
        'payment_status',
        'booking_notes',
        'customer_name',
        'ru_building_id',
        'room_no',
        'property_name',
        'pType'
    ];

    protected $appends = ['tot_guests', 'total_base_addon_charge'];

    public function home(){
        return $this->HasOne(TblHome::class, 'id', 'property_id');
    }

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

    protected function createdAt(): Attribute{
        return Attribute::make(
            get: fn (string $value) => date('j F Y h:i:A', strtotime($value)),
        );
    }

    public function paymentRequests(){
        return $this->hasMany(PropertyBookingPaymentRequest::class);
    }

    public function bookingGuests(){
        return $this->hasMany(BookingGuestId::class);
    }

    public function getTotGuestsAttribute(){
        $totGuest = $this->no_of_adult;
        if($this->no_of_children){
            $totGuest = $totGuest + $this->no_of_children;
        }
        return $totGuest;
    }

    public function getTotalBaseAddonChargeAttribute(){
        $total_base_addon_charge = '';
        if($this->additional_charges_detail){
            $total_base_addon_charge = 0;
            foreach(json_decode($this->additional_charges_detail) as $detail){

                $total_base_addon_charge = $total_base_addon_charge + (integer)$detail->price;
            }
        }
        return $total_base_addon_charge;
    }

    

    
    public function property(){
        return $this->hasOne(TblHomeUnit::class, 'id', 'property_id')->with('homeImage');
    }
    
    public function homeUnit()
    {
        //return $this->belongsTo(TblHomeUnit::class, 'property_id');
        return $this->belongsTo(TblHomeUnit::class, 'property_id', 'id');
    }

    public function homeMultiUnit()
    {
        //return $this->belongsTo(TblHomeMultiUnit::class, 'property_id');
        return $this->belongsTo(TblHomeMultiUnit::class, 'property_id', 'id');
    }

    
}