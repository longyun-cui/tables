<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Format extends Model
{
    //
    protected $table = "formats";
    protected $fillable = [
        'sort', 'type', 'user_id', 'chart_id', 'table_id', 'column_id', 'name', 'title', 'description',
        'visit_num', 'share_num'
    ];
    protected $dateFormat = 'U';


    function chart()
    {
        return $this->belongsTo('App\Models\Chart','chart_id','id');
    }

    function column()
    {
        return $this->belongsTo('App\Models\Column','column_id','id');
    }





}
