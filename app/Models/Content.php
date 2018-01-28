<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Content extends Model
{
    //
    protected $table = "contents";
    protected $fillable = [
        'sort', 'type', 'user_id', 'table_id', 'row_id', 'column_id', 'content', 'title', 'description',
        'visit_num', 'share_num'
    ];
    protected $dateFormat = 'U';


    function table()
    {
        return $this->belongsTo('App\Models\Table','table_id','id');
    }

    function row()
    {
        return $this->belongsTo('App\Models\Row','row_id','id');
    }

    function column()
    {
        return $this->belongsTo('App\Models\Column','column_id','id');
    }




}
