<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TblUnitMultiunit extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unit_id', 'multiunit_id', 'status', 'add_ip', 'add_time', 'add_by', 'update_ip', 'update_time'
    ];

    protected $appends = ['primary_image'];


    public function additionalCharges(){
        return $this->hasMany(HomeAdditionalCharge::class, 'multi_unit_id', 'id');
    }


    public function cancellationSlab(){
        return $this->hasMany(CancellationSlab::class, 'home_id', 'id');
    }

    public function images(){
        return $this->hasMany(TblHomeImageVideo::class, 'multi_unit_id')->with('ruImageType')->where('type', 'image');
    }

    public function ruamenities(){
        return $this->hasMany(TblRuAmenityMapping::class, 'multi_unit_id', 'id');
    }

    public function getPrimaryImageAttribute(){
        $filePath = URL('/').'/assets/images/noimage.jpg';
        $assets = TblHomeImageVideo::where('multi_unit_id', $this->id)->where('type', 'image')->get();
        foreach($assets as $asset){
            if(file_exists('storage/home/images/'.$asset->filename)){
                $filePath =  URL('/').'/storage/home/images/'.$asset->filename;
            }
        }
        return $filePath;
    }
}
