<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CancellationPolicy extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "cancellation_policies";

    protected $fillable = [
        'title',
        'cancellation_policy',
    ];
    
}
