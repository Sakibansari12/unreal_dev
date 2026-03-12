<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblAboutInformation extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_about_information';
    
    protected $guarded = [];

    public function about()
    {
        return $this->belongsTo(TblAboutUs::class, 'about_id');
    }
}
