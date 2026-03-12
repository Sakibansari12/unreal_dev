<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPropertyPublishLog extends Model{

    use HasFactory;
    protected $table = "tbl_property_publish_logs";

    protected $fillable = [
        'property_id',
        'status',
        'activity_date',
        'pType',
        'adate1',
        'adate2',
    ];

}