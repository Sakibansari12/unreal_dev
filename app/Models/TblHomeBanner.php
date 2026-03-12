<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;




class TblHomeBanner extends Model
{
    use HasFactory;
    protected $guarded = [];


    protected $casts = [
        'image' => 'string'
    ];


    public function getImageAttribute($value){
        // if($value){
        //     return str_replace(' ', '%20', $value);
        // }
        return $value;
    }

}
     