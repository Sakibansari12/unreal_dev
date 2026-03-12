<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;
use URL;

class BookingQuotation extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'location_id',
        'user_id',
        'parent_user_id',
        'first_name',
        'last_name',
        'country_code',
        'mobile_number',
        'email',
        'checkin_date',
        'checkout_date',
        'no_of_nights',
        'no_adults',
        'no_children',
        'guest_included_count',
        'validity',
        'booking_status',
        'is_email_sent'
    ];

    protected $appends = ['url', 'is_link_expired', 'tot_guests', 'quotation_date'];

    

    // public function bookingProperties(){
    //     return $this->hasMany(BookingQuotationProperty::class, 'booking_quotation_id')->join('tbl_home_units', 'tbl_home_units.id', '=', 'booking_quotation_properties.property_id')->select(['booking_quotation_properties.*', 'tbl_home_units.*', 'booking_quotation_properties.id as booking_req_property_id', 'tbl_home_units.unit_name as home_name']);
    // }

    public function bookingProperties(){
        return $this->hasMany(BookingQuotationProperty::class, 'booking_quotation_id');
    }
  public function bookingProperty(){
        return $this->hasMany(BookingQuotationProperty::class, 'booking_quotation_id');
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

    public function getUrlAttribute(){
        return URL('/quotation/detail/'.base64_encode($this->id));
    }

    public function location(){
        return $this->hasOne(TblLocation::class, 'id', 'location_id');
    }

    public function getIsLinkExpiredAttribute(){
        $startTime = Carbon::parse($this->created_at);
        $currentTime = Carbon::now();
        $hoursDifference = $startTime->diffInHours($currentTime);

        $is_link_expired = false;
        if($hoursDifference > $this->validity){
            $is_link_expired = true;
        }
        return $is_link_expired;
    }


    public function getTotGuestsAttribute(){
        $totGuest = $this->no_adults;
        
        return $totGuest;
    }

    public function getQuotationDateAttribute(){
        return date('j F Y h:i:s', strtotime($this->created_at));
    }
}
