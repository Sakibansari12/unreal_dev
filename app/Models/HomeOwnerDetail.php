<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class HomeOwnerDetail extends Model{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'mobile_no',
        'user_name',
        'password',
        'status',
        'home_id'
    ];
}
