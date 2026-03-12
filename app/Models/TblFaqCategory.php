<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblFaqCategory extends Model
{
    use HasFactory, SoftDeletes;
    
    protected $fillable = ['title'];
    
    public function faqs()
    {
        return $this->hasMany(TblFaq::class, 'faq_category_id');
    }
    
}
