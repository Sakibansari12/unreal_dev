<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class TblHomeImageVideo extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'tbl_home_image_video';
    protected $guarded = [];

    protected $fillable = [
        'home_id',
        'tbl_ru_image_type_id',
        'type',
        'title',
        'filename',
        'default',
        'position',
        'status',
        'add_ip',
        'add_by',
        'update_ip',
        'update_by',
        'base64_image'
    ];

   // protected $hidden = ['base64_image'];
    protected $casts = [
       
        'filename' => 'string',
    ];
    
    protected $appends = ['filepath', 'smallimage'];
    
   
    
    public function getSmallimageAttribute(){
        
        $value = null;
        if($this->filename){
            $explode = explode('/', $this->filename);
            $value = end($explode);
        }
     
        $videoExtensionArray = array('flv', 'm3u8', 'mp4', 'ts', '3gp', 'mov', 'avi', 'wmv');
        $expolode = explode('.', $value);
        $folder = 'images';
        if(in_array($expolode[1], $videoExtensionArray)){
            $folder = 'video';
        }
        $path= 'storage/home/small/'.$value;
        if(file_exists($path)){
            $path= 'storage/home/small/'.$value;
        }
        else{
            $path= 'assets/images/noimage-property.jpg';
        }
       
        return $path;
    }

    public function getFilenameAttribute($value){
        $videoExtensionArray = array('flv', 'm3u8', 'mp4', 'ts', '3gp', 'mov', 'avi', 'wmv');
        $expolode = explode('.', $value);
        $folder = 'images';
        if(in_array($expolode[1], $videoExtensionArray)){
            $folder = 'video';
        }
        $path= 'storage/home/'.$folder.'/'.$value;
        if(file_exists($path)){
            $path= 'storage/home/'.$folder.'/'.$value;
        }
        else{
            // $path= 'assets/images/noimage-property.jpg';
             $path = '';
        }
        return $path;
    }


    

    public function getFilepathAttribute(){
        return $this->type == 'image' ? '/storage/home/images/'.$this->filename : '/storage/home/videos/'.$this->filename;
    }

    public function getWebsiteImageAttribute(){
        $value = $this->attributes['filename'] ?? null; 
        if (!$value) {
            return asset('assets/images/noimage-property.jpg'); 
        }

        $videoExtensionArray = ['flv', 'm3u8', 'mp4', 'ts', '3gp', 'mov', 'avi', 'wmv'];
        $exploded = explode('.', $value);
        $extension = strtolower($exploded[1] ?? ''); 
        $folder = in_array($extension, $videoExtensionArray) ? 'video' : 'images';

        $path = 'storage/home/small/' . $value;

        return file_exists(public_path($path)) ? url($path) : asset('assets/images/noimage-property.jpg');
    }
    
    public function getMediumImageAttribute(){
        $value = $this->attributes['filename'] ?? null; 
        if (!$value) {
            return asset('assets/website/images/no-image.png'); 
        }

        $videoExtensionArray = ['flv', 'm3u8', 'mp4', 'ts', '3gp', 'mov', 'avi', 'wmv'];
        $exploded = explode('.', $value);
        $extension = strtolower($exploded[1] ?? ''); 
        $folder = in_array($extension, $videoExtensionArray) ? 'video' : 'images';

        $path = 'storage/home/medium/' . $value;

        return file_exists(public_path($path)) ? url($path) : asset('assets/website/images/no-image.png');
    }
    
    public function getDisplayImageAttribute(){
        $value = $this->attributes['filename'] ?? null; 
        if (!$value) {
            return asset('assets/website/images/no-image.png'); 
        }

        $videoExtensionArray = ['flv', 'm3u8', 'mp4', 'ts', '3gp', 'mov', 'avi', 'wmv'];
        $exploded = explode('.', $value);
        $extension = strtolower($exploded[1] ?? ''); 
        $folder = in_array($extension, $videoExtensionArray) ? 'video' : 'images';

        $path = 'storage/home/images/' . $value;

        return file_exists(public_path($path)) ? url($path) : asset('assets/website/images/no-image.png');
    }
    
    public function ruImageType(){
        return $this->hasOne(TblRuImageType::class, 'image_category_id', 'tbl_ru_image_type_id');
    }
}