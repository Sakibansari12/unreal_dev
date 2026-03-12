<?php



namespace App\Models;



use Illuminate\Database\Eloquent\Factories\HasFactory;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\SoftDeletes;



class TblHomeTags extends Model

{

    use HasFactory, SoftDeletes;

    protected $guarded = [];

    public function tags_data(){
        return $this->hasOne(TblTag::class, 'tags_id', 'id');
    }

}

