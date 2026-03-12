<?php



namespace App\Models;
use URL;


use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;



class TblTag extends Model{

    use HasFactory;

    protected $guarded = [];
    protected $appends =['tag_url'];

    public function getTagUrlAttribute(){
        return URL('/').$this->tags_image;
    }

}

