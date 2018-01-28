<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Chart extends Model
{
    //
    protected $table = "charts";
    protected $fillable = [
        'sort', 'type', 'user_id', 'table_id', 'row_id', 'column_id', 'name', 'title', 'description',
        'visit_num', 'share_num'
    ];
    protected $dateFormat = 'U';


    function table()
    {
        return $this->belongsTo('App\Models\Table','table_id','id');
    }

    function formats()
    {
        return $this->hasMany('App\Models\Format','chart_id','id');
    }




}