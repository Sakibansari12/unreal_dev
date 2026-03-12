<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblOwnerExpenses extends Model
{
    use HasFactory;
    protected $guarded = [];

   
   public function unitRelation()
{
    return $this->belongsTo(TblHomeUnit::class, 'property_id');
}

public function multiunitRelation()
{
    return $this->belongsTo(TblHomeMultiUnit::class, 'property_id');
}

public function getUnitPropertyAttribute()
{
    if ($this->pType === 'unit') {
        return $this->unitRelation;
    } elseif ($this->pType === 'multiunit') {
        return $this->multiunitRelation;
    }

    return null;
}

 public function unitPropertyHome()
    {
        return $this->belongsTo(TblHome::class, 'home_id');
    }

}