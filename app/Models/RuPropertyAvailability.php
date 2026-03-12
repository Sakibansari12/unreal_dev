<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuPropertyAvailability extends Model{
    use HasFactory;
    protected $table = 'ru_property_availabilities';
    protected $fillable = [
        'ru_property_id',
        'availability_date',
        'is_available',
        'property_id',
        'status',
        'type',
        'reason'
    ];
}
