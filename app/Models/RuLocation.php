<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class RuLocation extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ru_location_id',
        'ru_location_name',
        'active',
        'status'
    ];
}
