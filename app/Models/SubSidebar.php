<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubSidebar extends Model{

    use HasFactory;
    protected $fillable = [
        'sidebar_id',
        'name',
        'slug',
        'status'
    ];
    
    public function sidebar()
    {
        return $this->belongsTo(Sidebar::class, 'id');
    }
}
