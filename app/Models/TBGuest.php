<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class TBGuest extends Model{

    use HasFactory;

    protected $table = 'tb_guests';

    protected $fillable = [
        'name',
        'title',
        'sub_title',
        'count',
        'allow_guest_count'
    ];

}

