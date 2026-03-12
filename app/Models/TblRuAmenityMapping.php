<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class TblRuAmenityMapping extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'home_id',
        'unit_id',
        'multi_unit_id',
        'floor_id',
        'bedroom_no',
        'bathroom_no',
        'amenity_type',
        'ru_amenity_id',
    ];
}
