<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class HomeAdditionalCharge extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'price',
        'home_id',
        'gst',
        'status',
        'display_on_website',
        'pType',
        'type_option',
        'unit_id',
        'multi_unit_id',

    ];
}