<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use URL;

class TblHomeMultiUnit extends Model{

    use HasFactory, SoftDeletes;
    protected $guarded = [];
    protected $appends = ['admin_emails', 'primary_image', 'unit_collection','pType'];
    
    public function getPtypeAttribute()
    {
        return 'multiunit';
    }
    
    public function features(){
        return $this->hasMany(TblFeatures::class, 'home_id', 'id');
    }

    public function homeReviews(){
        return $this->hasMany(TblHomeReview::class, 'multi_unit_id')->orderBy('id', 'desc');
    }

    // public function imagesWebsite(){
    //     return $this->hasMany(TblHomeImageVideo::class, 'multi_unit_id')->where('type', 'image')->orderBy('position');
    // }
    
      public function imagesWebsite()
{
    return $this->hasMany(TblHomeImageVideo::class, 'multi_unit_id')
                ->select(['id', 'home_id', 'multi_unit_id', 'type', 'title', 'filename', 'default', 'position', 'status', 'add_ip', 'add_by', 'update_ip', 'update_by', 'tbl_ru_image_type_id', 'pType'])
                ->where('type', 'image')
                ->where('pType', 'multiunit')
                ->orderBy('position');
}

public function primaryImageRelation()
{
    return $this->hasOne(TblHomeImageVideo::class, 'multi_unit_id')
        ->where('pType', 'multiunit')
        ->where('type', 'image')
        ->orderBy('tbl_ru_image_type_id', 'asc') // first preference
        ->orderBy('position', 'asc');
}
    

    public function locationData()
    {
        return $this->belongsTo(TblLocation::class, 'location_id', 'id');
    }
    
    public function homecollections(){
        return $this->hasMany(TblHomeCollection::class, 'home_id')->where('pType', 'multiunit')->orderBy('id', 'desc');
    }
    
    public function homefaqSection(){
        return $this->hasMany(WebsiteFaq::class, 'multi_unit_id')->where('pType', 'multiunit');
    }
    
    public function layoutImageSection(){
        return $this->hasMany(TbllayoutImage::class, 'multi_unit_id')->where('pType', 'multiunit')->orderBy('position');
    }

    public function home(){
        return $this->belongsTo(TblHome::class);
    }

    public function images(){
        return $this->hasMany(TblHomeImageVideo::class, 'multi_unit_id')->where('type', 'image');
    }
    
    // public function propertyVideo(){
    //     return $this->hasOne(TblHomeImageVideo::class, 'multi_unit_id')->where('type', 'video')->where('pType', 'multiunit');
    // }
    
     public function propertyVideo()
{
    return $this->hasOne(TblHomeImageVideo::class, 'multi_unit_id')
                ->select(['id', 'home_id', 'multi_unit_id', 'type', 'title', 'filename', 'default', 'position', 'status', 'add_ip', 'add_by', 'update_ip', 'update_by', 'tbl_ru_image_type_id', 'pType'])
                ->where('type', 'image')
                ->where('pType', 'multiunit');
                
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
    
    
    public function additionalCharge(){
        return $this->hasMany(HomeAdditionalCharge::class, 'multi_unit_id', 'id');
    }

    public function reviews(){
        return $this->hasMany(TblHomeReview::class, 'home_id');
    }

    public function amenities(){
        return $this->hasMany(TblHomeAmenities::class, 'home_id')->join('tbl_amenities', 'tbl_amenities.id', '=', 'tbl_home_amenities.amenities_id');
    }
    
    public function websiteamenities(){
        return $this->hasMany(TblWebsiteAmenities::class, 'multi_unit_id')->join('tbl_amenities', 'tbl_amenities.id', '=', 'tbl_website_amenities.amenities_id');
    }
    
    // public function websiteAmenities() {
    //     return $this->hasMany(TblWebsiteAmenities::class, 'multi_unit_id','id')->where('pType', 'multiunit');
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
        // $asset = TblHomeImageVideo::where('multi_unit_id', $this->id)->where('type', 'image')->first();
        // if($asset){
        //     if(file_exists('storage/home/images/'.$asset->filename)){
        //         $filePath =  URL('/').'/storage/home/images/'.$asset->filename;
        //     }
        // }
        
        $asset = TblHomeImageVideo::where('multi_unit_id', $this->id)->where('pType', 'multiunit')->where('tbl_ru_image_type_id', 1)->where('type', 'image')->orderBy('position')->first();
        
        if (!$asset) {
            $asset = TblHomeImageVideo::where('multi_unit_id', $this->id)->where('pType', 'multiunit')->where('type', 'image')->orderBy('position')->first();
        }        
        if($asset){
            if(file_exists($asset->filename)){
                $filePath =  URL('/').'/'.$asset->filename;
            }
        }
        return $filePath;
    }


    public function getUnitCollectionAttribute(){
        $getAssignedUnits = TblUnitMultiunit::where('multiunit_id', $this->id)->get()->pluck('unit_id')->toArray();
        $units = TblHomeUnit::whereIn('id', $getAssignedUnits)->get()->pluck('unit_name')->toArray();
        if($units){
            return implode(', ', $units);
        }
        else{
            return '';
        }
    }

    public function ruamenities(){
        return $this->hasMany(TblRuAmenityMapping::class, 'multi_unit_id', 'id');
    }
    
    public function stateDetail(){
        return $this->hasOne(TblState::class, 'id', 'state_id')->with('companyInfo');
    }
    
    public function ownerData()
    {
        return $this->belongsTo(Admin::class, 'owner_id');
    }

     public function tags(){
        return $this->hasMany(TblHomeTags::class, 'multi_unit_id')->join('tbl_tags', 'tbl_tags.id', '=', 'tbl_home_tags.tags_id');
    }

     public function ImportantInformation(){
        return $this->hasMany(HomeImportantInformation::class, 'multi_unit_id', 'id');
    }
    
}