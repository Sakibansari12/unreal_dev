<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AboutUs extends Model
{
    use HasFactory;

    protected $table = 'about_us';

    protected $fillable = [
        'title',
        'short_description',
        'image',
        'properties',
        'properties_icon',
        'properties_count',
         'happy_guests',
        'happy_guests',
        'happy_guests_icon',
        'hosting_experience',
        'hosting_experience_icon',
        'hosting_experience_count',
         'description',
    ];
}
