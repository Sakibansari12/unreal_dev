<?php

namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;





class SubscribeWebsite extends Model{

    use HasFactory;

    protected $table = 'subscribe_websites';

    protected $fillable = [

        'email',

        'status',

    ];



}