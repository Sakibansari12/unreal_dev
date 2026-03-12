<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TblIcon extends Model
{
    protected $table = 'tbl_icons';

    protected $primaryKey = 'id';

    public $timestamps = true;

    protected $fillable = [
        'icons_name',
        'icons_image',
        'status',
        'icons_code',
        'icon_slug',
        'add_ip',
        'add_by',
        'update_ip',
    ];

    protected $casts = [
        'status' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

}
