<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    //
    protected $table = "tables";
    protected $fillable = [
        'sort', 'type', 'user_id', 'name', 'title', 'description',
        'visit_num', 'share_num'
    ];
    protected $dateFormat = 'U';


    function columns()
    {
        return $this->hasMany('App\Models\Column','table_id','id');
    }

    function rows()
    {
        return $this->hasMany('App\Models\Row','table_id','id');
    }

    function charts()
    {
        return $this->hasMany('App\Models\Chart','table_id','id');
    }




}
