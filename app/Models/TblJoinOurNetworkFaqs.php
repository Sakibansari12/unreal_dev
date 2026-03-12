<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblJoinOurNetworkFaqs extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tbl_join_our_network_faqs';
    protected $guarded = [];
}
