<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use URL;

class TblHome extends Model{

    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $appends = ['admin_emails', 'primary_image'];

    public function user()
    {
        return $this->belongsTo(Admin::class, 'user_id');
    }

    public function features(){
        return $this->hasMany(TblFeatures::class, 'home_id', 'id');
    }

    public function images(){
        return $this->hasMany(TblHomeImageVideo::class, 'home_id')->with('ruImageType')->where('type', 'image');
    }
    
    public function imagesWebsite(){
        return $this->hasMany(TblHomeImageVideo::class, 'unit_id')->where('type', 'image')->where('pType', 'unit')->orderBy('position');
    }

    public function videos(){
        return $this->hasMany(TblHomeImageVideo::class, 'home_id')->where('type', 'video');
    }

    public function assets(){
        return $this->hasMany(TblHomeImageVideo::class, 'home_id');
    }

    public function additionalCharges(){
        return $this->hasMany(HomeAdditionalCharge::class, 'home_id', 'id');
    }


    public function cancellationSlab(){
        return $this->hasMany(CancellationSlab::class, 'home_id', 'id');
    }


    public function additionalCharge(){
        return $this->hasMany(HomeAdditionalCharge::class, 'home_id', 'id');
    }

    public function reviews(){
        return $this->hasMany(TblHomeReview::class, 'home_id');
    }

    public function units(){
        return $this->hasMany(TblHomeUnit::class, 'home_id');
    }


    public function multiUnits(){
        return $this->hasMany(TblHomeMultiUnit::class, 'home_id');
    }

    public function amenities(){
        return $this->hasMany(TblHomeAmenities::class, 'home_id')->join('tbl_amenities', 'tbl_amenities.id', '=', 'tbl_home_amenities.amenities_id');
    }


    public function ruamenities(){
        return $this->hasMany(TblRuAmenityMapping::class, 'home_id', 'id');
    }

    public function availabilities(){
        return $this->hasMany(RuPropertyAvailability::class, 'ru_property_id', 'ru_property_id');
    }

    public function prices(){
        return $this->hasMany(RuPropertyPrice::class, 'ru_property_id', 'ru_property_id')->select(['id', 'price', 'price_date', 'ru_property_id']);
    }

    public function owner(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function state(){
        return $this->hasOne(TblState::class, 'id', 'state_id')->with('companyInfo');
    }

    public function getAdminEmailsAttribute(){
        if($this->comms){
           return json_decode($this->comms);
        }
        return NULL;
    }

    public function getPrimaryImageAttribute(){
        $filePath = URL('/').'/assets/pms/images/noimage.jpg';
        $asset = TblHomeImageVideo::where('home_id', $this->id)->where('type', 'image')->first();
        if($asset){
            if(file_exists('storage/home/images/'.$asset->filename)){
                $filePath =  URL('/').'/storage/home/images/'.$asset->filename;
            }
        }
        return $filePath;
    }

    public function getImageFullPathAttribute(){
        $detail = TblHomeImageVideo::where(['home_id'=>$this->id, 'type'=>'image'])->first();
        $fullPath = URL('/').'/storage/home/images/no-image.png';
        if($detail){
            $path = URL('/').'/'.$detail->filename;
            if(file_exists($detail->filename)){
                $fullPath = $path;
            }
        }
        return $fullPath;
    }
}