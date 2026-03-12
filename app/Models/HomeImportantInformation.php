<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use App\Models\TblIcon;

class HomeImportantInformation extends Model
{
    use HasFactory, SoftDeletes;
    // protected $fillable = [
    //     'title',
    //     'sub_title',
    //     'home_id',
    //     'ptype_id',
    //     'unit_id',
    //     'multiunit_id',
    //     'icon_id',
    //     'type_option',
    //     'display_on_website',
    //     'status',
    //     'icon_image',
    // ];
    protected $fillable = [
        'title',
        'sub_title',
        'home_id',
        'pType',
        'unit_id',
        'multi_unit_id',
        'icon_id',
        'type_option',
        'display_on_website',
        'status',
        'icon_image',
    ];

    public function iconImage()
    {
        return $this->belongsTo(TblIcon::class, 'icon_id');
    }
}
