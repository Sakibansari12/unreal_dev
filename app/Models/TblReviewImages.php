<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;

class TblReviewImages extends Model{
    use HasFactory;
    protected $guarded = [];

    protected $fillable = [
        'review_name',
        'review_image',
        'created_at',
        'updated_at',
    ];

   
    protected $appends =['icon_url'];

    public function getIconUrlAttribute(){
       // return URL('/').'/storage/review/images/'.$this->review_image;
       return $this->review_image 
        ? URL('/').'/storage/channel/'.$this->review_image 
        : URL('/').'/assets/images/no-image-icon.png';
    }
    
    public function reviews(){
        return $this->hasMany(TblHomeReview::class, 'review_type', 'id');
    }
     
}



