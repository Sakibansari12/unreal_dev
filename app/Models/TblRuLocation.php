<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblRuLocation extends Model{
    use HasFactory;
    protected $table = "tbl_ru_location";

    protected $fillable = [
        'ru_location_id',
        'ru_location_type_id',
        'ru_parent_location_id',
        'name',
        'status',
        'position'
    ];

}
