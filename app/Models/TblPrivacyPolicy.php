<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblPrivacyPolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "tbl_privacy_policys";

    protected $fillable = [
        'title',
        'cancellation_policy',
    ];
    
}
