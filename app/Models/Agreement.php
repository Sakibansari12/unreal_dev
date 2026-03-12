<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Agreement extends Model
{
    use HasFactory;

 
    protected $table = 'agreements';

 
    protected $fillable = [
        'file_pdf',
        'title',
        'add_ip',
        'add_by',
        'status', 
    ];
}
