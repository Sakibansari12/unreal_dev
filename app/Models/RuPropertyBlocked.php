<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuPropertyBlocked extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'ru_property_blocked';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'ru_property_id',
        'property_id',
        'date_from',
        'date_to',
        'is_available',
        'type',
        'reason',
        'created_at',
        'updated_at',
        'booking_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'date_from' => 'date',
        'date_to' => 'date',
    ];
}
