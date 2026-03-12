<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class CancellationSlab extends Model{
    use HasFactory, SoftDeletes;


    protected $fillable = [
        'home_id',
        'slab_from',
        'slab_to',
        'slab',
        'unit_id',
        'multi_unit_id',
        'type',
    ];
}
