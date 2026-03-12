<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TblAmenities extends Model
{
    use HasFactory, SoftDeletes;

    protected $guarded = []; //for All field are avilabel for add and modify
}
