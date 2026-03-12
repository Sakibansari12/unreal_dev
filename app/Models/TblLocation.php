<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblLocation extends Model{

    use HasFactory;
    protected $table = "tbl_location";

    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope('orderByPosition', function ($query) {
            $query->orderBy('position', 'asc');
        });
    }

    protected $fillable = [
        'state_id',
        'ru_location_id',
        'location_name',
        'image',
        'show_on_location_page',
        'slug_name',
        'tax',
        'status',
        'position',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'title',
        'sub_title',

    ];
    
     public function state()
    {
        return $this->belongsTo(TblState::class, 'state_id', 'id');
    }
    public function properties(){
        return $this->hasMany(TblHomeUnit::class, 'location_id', 'id')->whereNotNull('ru_property_id');
    }


    public function locationProperties()
    {
        return $this->hasMany(TblHomeUnit::class, 'location_id', 'id')
            ->whereNotNull('ru_property_id')
            ->select('id', 'location_id', 'ru_property_id', 'unit_name_website'); 
    }


}
