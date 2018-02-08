<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    //
    protected $table = "charts";
    protected $fillable = [
        'sort', 'type', 'user_id', 'table_id', 'row_id', 'column_id', 'name', 'title', 'description', 'is_shared',
        'visit_num', 'share_num'
    ];
    protected $dateFormat = 'U';


    function user()
    {
        return $this->belongsTo('App\User','user_id','id');
    }

    function table()
    {
        return $this->belongsTo('App\Models\Table','table_id','id');
    }

    function formats()
    {
        return $this->hasMany('App\Models\Format','chart_id','id');
    }




}
