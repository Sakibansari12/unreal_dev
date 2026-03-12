<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblState extends Model
{
    use HasFactory;

    protected $fillable = [
        'country_id',
        'name',
        'state_code',
        'status',
    ];


    public function companyInfo(){
        return $this->hasOne(TblCompany::class, 'state_id', 'id');
    }
    public function locations()
    {
        return $this->hasMany(TblLocation::class, 'state_id');
    }
}
