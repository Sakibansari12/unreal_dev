<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class TblJoinOurNetworkIntro extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_join_our_network_intro';
    protected $guarded = [];
} 
   