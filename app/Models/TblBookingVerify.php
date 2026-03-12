<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBookingVerify extends Model
{
    use HasFactory;

    protected $table = 'tbl_booking_verify';

    protected $fillable = [
        'otp',
        'booking_id',
        'start_date',
        'end_date',
        'otp_verified',
        'customer_number',
    ];
}
