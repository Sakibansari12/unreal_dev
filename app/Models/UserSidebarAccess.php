<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSidebarAccess extends Model{

    use HasFactory;
    protected $fillable = [
        'user_id',
        'role_id',
        'sidebar_id',
        'sub_sidebar_id',
        'type',
        'is_checked',
    ];
    
    public function sidebar()
    {
        return $this->belongsTo(Sidebar::class, 'sidebar_id');
    }
    
    public function SubSidebar()
    {
        return $this->belongsTo(SubSidebar::class, 'sub_sidebar_id');
    }
}
