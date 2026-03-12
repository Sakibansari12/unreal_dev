<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblFaq extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = ['faq_category_id','question','answer','add_ip','add_by','status'];
    
    public function category()
    {
        return $this->belongsTo(FaqCategory::class, 'faq_category_id');
    }

}
