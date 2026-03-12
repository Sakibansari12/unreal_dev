<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RuLog extends Model{

    use HasFactory;

    protected $fillable = [
        'api_request',
        'response_id',
        'api_status',
        'message',
        'log',
        'add_ip'
    ];
}
