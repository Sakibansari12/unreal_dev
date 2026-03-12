<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sidebar extends Model{

    use HasFactory;
    protected $fillable = [
        'name',
        'slug',
        'status'
    ];
    
    public function subSidebars()
    {
        return $this->hasMany(SubSidebar::class, 'sidebar_id');
    }
}
