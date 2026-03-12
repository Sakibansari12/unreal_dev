<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class TblArea extends Model

{

    use HasFactory;

    protected $table = "tbl_area";



    protected $fillable = [
        'state_id',
        'location_id',
        'area_name',
        'status',
    ];



    





}

