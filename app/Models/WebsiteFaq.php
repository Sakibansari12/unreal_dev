<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class WebsiteFaq extends Model{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'pType',
        'unit_id',
        'multi_unit_id',

    ];
}
