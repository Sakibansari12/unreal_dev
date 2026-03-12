<?php



namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;



class HomeFooterBanner extends Model

{

    use HasFactory, SoftDeletes;
    protected  $table = 'home_footer_banners';
    protected $guarded = [];

}

     