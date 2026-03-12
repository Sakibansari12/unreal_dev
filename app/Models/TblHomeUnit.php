<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use URL;
use Illuminate\Support\Facades\Auth;

class TblHomeUnit extends Model{

    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $appends = ['admin_emails', 'primary_image','pType', 'smallimage'];
    
    public function getPTypeAttribute(){
        return 'unit';
    }

    public function features(){
        return $this->hasMany(TblFeatures::class, 'home_id', 'id');
    }

    public function homeReviews(){
        return $this->hasMany(TblHomeReview::class, 'unit_id')->orderBy('id', 'desc');
    }

    // public function imagesWebsite(){
    //     return $this->hasMany(TblHomeImageVideo::class, 'unit_id')->where('type', 'image')->where('pType', 'unit')->orderBy('position');
    // }
    
    public function imagesWebsite()
{
    return $this->hasMany(TblHomeImageVideo::class, 'unit_id')
                ->select(['id', 'home_id', 'unit_id', 'type', 'title', 'filename', 'default', 'position', 'status', 'add_ip', 'add_by', 'update_ip', 'update_by', 'tbl_ru_image_type_id', 'pType'])
                ->where('type', 'image')
                ->where('pType', 'unit')
                ->orderBy('position');
}
    
    
    public function imagesDisplaypms(){
        return $this->hasOne(TblHomeImageVideo::class, 'unit_id')->where('tbl_ru_image_type_id', 1)->where('type', 'image')->where('pType', 'unit');
    }

    public function home(){
        return $this->belongsTo(TblHome::class);
    }

    public function images(){
        return $this->hasMany(TblHomeImageVideo::class, 'unit_id')->where('type', 'image')->where('pType', 'unit');
    }
    // public function propertyVideo(){
    //     return $this->hasOne(TblHomeImageVideo::class, 'unit_id')->where('type', 'video')->where('pType', 'unit');
    // }
    
    public function propertyVideo()
{
    return $this->hasOne(TblHomeImageVideo::class, 'unit_id')
                ->select(['id', 'home_id', 'unit_id', 'type', 'title', 'filename', 'default', 'position', 'status', 'add_ip', 'add_by', 'update_ip', 'update_by', 'tbl_ru_image_type_id', 'pType'])
                ->where('type', 'video')
                ->where('pType', 'unit');
                
}

    
    
    public function homecollections(){
        return $this->hasMany(TblHomeCollection::class, 'home_id')->where('pType', 'unit')->orderBy('id', 'desc');
    }
    
    public function homefaqSection(){
        return $this->hasMany(WebsiteFaq::class, 'unit_id')->where('pType', 'unit');
    }
    
    public function layoutImageSection(){
        return $this->hasMany(TbllayoutImage::class, 'unit_id')->where('pType', 'unit')->orderBy('position');
    }
    
    public function locationData()
    {
        return $this->belongsTo(TblLocation::class, 'location_id', 'id');
    }

    public function homeImage(){
        return $this->hasMany(TblHomeImageVideo::class, 'unit_id')->where('type', 'image')->where('pType', 'unit');
    }

    public function videos(){
        return $this->hasMany(TblHomeImageVideo::class, 'unit_id')->where('type', 'video')->where('pType', 'unit');
    } 

    public function assets(){
        return $this->hasMany(TblHomeImageVideo::class, 'unit_id')->where('pType', 'unit');
    }

    public function additionalCharges(){
        return $this->hasMany(HomeAdditionalCharge::class, 'unit_id', 'id');
    }
    
    public function additionalCharge(){
        return $this->hasMany(HomeAdditionalCharge::class, 'unit_id', 'id');
    }

    public function reviews(){
        return $this->hasMany(TblHomeReview::class, 'home_id');
    }

    public function amenities(){
        return $this->hasMany(TblHomeAmenities::class, 'home_id')->join('tbl_amenities', 'tbl_amenities.id', '=', 'tbl_home_amenities.amenities_id');
    }
    
    public function websiteamenities(){
        return $this->hasMany(TblWebsiteAmenities::class, 'unit_id')->join('tbl_amenities', 'tbl_amenities.id', '=', 'tbl_website_amenities.amenities_id');
    }
    // public function websiteAmenities() {
    //     return $this->hasMany(TblWebsiteAmenities::class, 'unit_id','id')->where('pType', 'unit');
    // }
    

    public function availabilities(){
        return $this->hasMany(RuPropertyAvailability::class, 'ru_property_id', 'ru_property_id');
    }

    public function prices(){
        return $this->hasMany(RuPropertyPrice::class, 'ru_property_id', 'ru_property_id')->select(['id', 'price', 'price_date', 'ru_property_id']);
    }

    public function owner(){
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function cancellationSlab(){
        return $this->hasMany(CancellationSlab::class, 'unit_id', 'id');
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
    
    public function getSmallimageAttribute(){
        $filePath = URL('/').'/assets/pms/images/noimage.jpg';
       
        $asset = TblHomeImageVideo::where('unit_id', $this->id)->where('pType', 'unit')->where('tbl_ru_image_type_id', 1)->where('type', 'image')->orderBy('position')->first();
        
        if (!$asset) {
            $asset = TblHomeImageVideo::where('unit_id', $this->id)->where('pType', 'unit')->where('type', 'image')->orderBy('position')->first();
        }
        
        if($asset){
            if(file_exists($asset->filename)){
                $filePath =  URL('/').'/'.$asset->smallimage;
            }
        }
        return $filePath;
    }

    public function getPrimaryImageAttribute(){
        $filePath = URL('/').'/assets/pms/images/noimage.jpg';
        // $asset = TblHomeImageVideo::where('unit_id', $this->id)->where('type', 'image')->first();
        // if($asset){
        //     if(file_exists('storage/home/images/'.$asset->filename)){
        //         $filePath =  URL('/').'/storage/home/images/'.$asset->filename;
        //     }
        // }
        
        $asset = TblHomeImageVideo::where('unit_id', $this->id)->where('pType', 'unit')->where('tbl_ru_image_type_id', 1)->where('type', 'image')->orderBy('position')->first();
        
        if (!$asset) {
            $asset = TblHomeImageVideo::where('unit_id', $this->id)->where('pType', 'unit')->where('type', 'image')->orderBy('position')->first();
        }
        
        
        
        
        if($asset){
            if(file_exists($asset->filename)){
                $filePath =  URL('/').'/'.$asset->filename;
            }
        }
        return $filePath;
    }

    public function primaryImageRelation()
{
    return $this->hasOne(TblHomeImageVideo::class, 'unit_id')
        ->where('pType', 'unit')
        ->where('type', 'image')
        ->orderBy('tbl_ru_image_type_id', 'asc') // first preference
        ->orderBy('position', 'asc');
}

    public function getPerNightPriceAttribute()
    {
        $per_night_price = $this->attributes['per_night_price'] ?? 0;
        $traveluser = Auth::guard('admin')->user();
        if ($traveluser && $traveluser->role == 'Travel Agent') {
            $discount = ($per_night_price * $traveluser->discount) / 100;
            $per_night_price -= $discount;
        }
        return $per_night_price;
    }

    public function getPriceAttribute()
    {
        $price = $this->attributes['price'] ?? 0;
        $traveluser = Auth::guard('admin')->user();
        if ($traveluser && $traveluser->role == 'Travel Agent') {
            $discount = ($price * $traveluser->discount) / 100;
            $price -= $discount;
        }
        return $price;
    }


    public function ruamenities(){
        return $this->hasMany(TblRuAmenityMapping::class, 'unit_id', 'id');
    }
    
    
    public function stateDetail(){
        return $this->hasOne(TblState::class, 'id', 'state_id')->with('companyInfo');
    }
    
    public function ownerData()
    {
        return $this->belongsTo(Admin::class, 'owner_id');
    }


    public function tags(){
        return $this->hasMany(TblHomeTags::class, 'unit_id')->join('tbl_tags', 'tbl_tags.id', '=', 'tbl_home_tags.tags_id');
    }

    public function ImportantInformation(){
        return $this->hasMany(HomeImportantInformation::class, 'unit_id', 'id');
    }
    



    
}