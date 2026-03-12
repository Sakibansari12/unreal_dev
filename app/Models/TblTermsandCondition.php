<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblTermsandCondition extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "tbl_terms_and_conditions";

    protected $fillable = [
        'title_text',
        'terms_and_condition',
    ];
    
}
