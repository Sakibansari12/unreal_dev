<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceLabsApiLog extends Model
{
    use HasFactory;
    protected $table = 'pricelabs_api_logs';

    protected $fillable = [
        'api_name',
        'http_method',
        'request_body',
        'response_body',
        'status_code',
        'success',
    ];
}
