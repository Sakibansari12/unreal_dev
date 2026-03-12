<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCollection extends Model
{
    use HasFactory;
    protected $table = "tbl_collection";

    protected $fillable = [
        'collection_name',
        'collection_description',
        'status',
        'image',
    ];
}