<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Row extends Model
{
    //
    protected $table = "rows";
    protected $fillable = [
        'sort', 'type', 'user_id', 'table_id', 'name', 'title', 'description', 'is_shared',
        'visit_num', 'share_num'
    ];
    protected $dateFormat = 'U';


    function table()
    {
        return $this->belongsTo('App\Models\Table','table_id','id');
    }

    function contents()
    {
        return $this->hasMany('App\Models\Content','row_id','id');
    }



}
