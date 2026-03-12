<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\helper\MasterHelper;

class BookingQuotationProperty extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'booking_quotation_id',
        'property_id',
        'checkout_date',
        'no_of_night',
        'guest_included',
        'property_name',
        'pType',
        'website_markup_price',
        'validity',
        'booking_status',
    ];

    protected $appends = ['images', 'features', 'reviews', 'videos', 'initial_per_night_price', 'discounted_per_night_price', 'current_per_night_price'];


    public function getImagesAttribute(){
        return TblHomeImageVideo::where('home_id', $this->property_id)->where('type', 'image')->get();
    }

    public function getVideosAttribute(){
        return TblHomeImageVideo::where('home_id', $this->property_id)->where('type', 'video')->get();
    }

    public function getFeaturesAttribute(){
        return TblFeatures::where('home_id', $this->property_id)->get();
    }

    public function getReviewsAttribute(){
        return TblHomeReview::where('home_id', $this->property_id)->get();
    }

    protected $casts = [
        'additional_charges_detail' => 'array',
    ];

    public function getAdditionalChargesDetailAttribute($value){
        if(!$value){
            $value = [];
        }
        else{
            $value =json_decode($value, true);
        }
        return $value;
    }

    public function bookingQuotationDetail(){
        return $this->hasOne(BookingQuotation::class, 'id', 'booking_quotation_id');
    }

    public function propertyDetail(){
        return $this->hasOne(TblHomeUnit::class, 'id', 'property_id');
    }

    public function getInitialPerNightPriceAttribute($value){
         return '';
    }

    public function getDiscountedPerNightPriceAttribute(){
        $quotationDetail = BookingQuotation::where('id', $this->booking_quotation_id)->first();
        $amount = $this->taxable_amount;
        if($this->addon_total_amount){
           $amount = $amount - $this->addon_total_amount;
        }
        return $amount/$quotationDetail->no_of_nights;
    }

    public function getCurrentPerNightPriceAttribute(){
        $quotationDetail = BookingQuotation::where('id', $this->booking_quotation_id)->first();
        $amount = $this->basePrice;
        return $amount/$quotationDetail->no_of_nights;
    }
}