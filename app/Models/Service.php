<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
    use HasFactory;

    protected $table = 'services';

    protected $fillable = [
        'customer_service_title',
        'customer_service_icon',
        'customer_service_short_description',
        'privacy_flexibility_title',
        'privacy_flexibility_icon',
        'privacy_flexibility_short_description',
        'professionally_managed_title',
        'professionally_managed_icon',
        'professionally_managed_description',
        'best_feature_title',
        'best_feature_icon',
        'best_feature_description',
    ];
}
