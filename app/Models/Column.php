<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Column extends Model
{
    //
    protected $table = "columns";
    protected $fillable = [
        'sort', 'type', 'user_id', 'table_id', 'order', 'name', 'title', 'description', 'default', 'is_shared',
        'visit_num', 'share_num'
    ];
    protected $dateFormat = 'U';


    function table()
    {
        return $this->belongsTo('App\Models\Table','table_id','id');
    }

    function contents()
    {
        return $this->hasMany('App\Models\Content','column_id','id');
    }




}
