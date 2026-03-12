<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuPropertyMinstay extends Model{
    use HasFactory;
    protected $table = 'ru_property_minstay';
    protected $fillable = [
        'ru_property_id',
        'home_id',
        'minstay_date',
        'is_minstay_count',
        'status',
        'type'
    ];
}
