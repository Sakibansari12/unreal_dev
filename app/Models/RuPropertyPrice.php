<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuPropertyPrice extends Model{
    use HasFactory;
    protected $table = 'ru_property_prices';
    protected $fillable = [
        'ru_property_id',
        'price',
        'extra_price',
        'price_date',
        'property_id',
        'status',
        'type'
    ];
}
