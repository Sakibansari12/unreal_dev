<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use URL;

class TblHomeReview extends Model{
    use HasFactory, SoftDeletes;
    protected $guarded = [];

    protected $fillable = [
        'home_id',
        'unit_id',
        'multi_unit_id',
        'pType',
        'guest_name',
        'file',
        'file_type',
        'banner_image',
        'review_date',
        'rating',
        'comment',
        'position',
        'img',
        'status',
        'add_ip',
        'add_by',
        'review_type',
        'link',
        'update_ip',
        'update_by'
    ];


    protected $casts = [
        'img' => 'string',
    ];

    public function getImgAttribute($value){
        $path= 'storage/review/'.$value;
        if(file_exists($path)){
            $path= URL::to('/').'/storage/review/'.$value;
        }
        else{
            $path= 'assets/images/noimage-property.jpg';
        }
        return $path;
    }
    
    public function getMediaUrlAttribute()
    {
        // For image type
        if ($this->file_type === 'images') {
            $value = $this->getRawOriginal('file');
            $path = 'storage/review/images/' . $value;

            if ($value && file_exists(public_path($path))) {
                return asset($path);
            } else {
                return asset('assets/images/noimage-property.jpg');
            }
        }
        
        // For video type
        if ($this->file_type === 'video') {
            $value = $this->getRawOriginal('file');
            $path = 'storage/review/videos/' . $value;

            if ($value && file_exists(public_path($path))) {
                return asset($path);
            } else {
                // fallback video placeholder or null
                return null;
            }
        }

        // Default fallback
        return null;
    }

    public function getThumbnailUrlAttribute()
    {
        $thumb = $this->getRawOriginal('banner_image'); 
        $path = 'storage/review/banner_images/' . $thumb;

        if ($thumb && file_exists(public_path($path))) {
            return asset($path);
        }

        return asset('assets/website/images/video-thumbnail.webp');
    }
    
    public function PropertyUnit(){
        return $this->belongsTo(TblHomeUnit::class, 'unit_id', 'id');
    }
    public function PropertyMUnit(){
        return $this->belongsTo(TblHomeMultiUnit::class, 'multi_unit_id', 'id');
    }
    
    public function reviewImages(){
    return $this->belongsTo(TblReviewImages::class, 'review_type','id');
}



}