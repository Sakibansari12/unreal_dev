<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Support\Facades\Storage;

class TbllayoutImage extends Model{
    use HasFactory, SoftDeletes;
    protected $table = 'tbl_layout_images';
    protected $guarded = [];

    protected $fillable = [
        'home_id',
        'type',
        'title',
        'filename',
        'default',
        'position',
        'unit_id',
        'multi_unit_id',
        'status',
        'pType',
    ];
    
}