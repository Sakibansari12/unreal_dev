<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TblMultiunit extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'unit_id', 'multiunit_id', 'status', 'add_ip', 'add_time', 'add_by', 'update_ip', 'update_time'
    ];


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
}
