<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TblLanding extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = "tbl_landing";

    protected $fillable = [
        'title',
        'property_type_id',
        'property_id',
        'status',
        'slug',
        'image',
        'keyword',
        'description',
    ];
}