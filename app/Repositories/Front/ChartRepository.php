<?php
namespace App\Repositories\Front;

use App\Models\Table;
use App\Models\Column;
use App\Models\Row;
use App\Models\Content;
use App\Models\Chart;
use App\Models\Format;
use App\Repositories\Common\CommonRepository;
use Response, Auth, Validator, DB, Exception;
use QrCode;

class ChartRepository
{

    private $model;
    private $repo;

    public function __construct()
    {
        $this->model = new Table;
    }


    /*
     * 数据
     */
    //
    public function view_data_index()
    {
        $table_encode = request("id", 0);
        $table_id = decode($table_encode);
        if (!$table_id && intval($table_id) !== 0) return response("参数有误", 404);

        $table = Table::with([
            'columns' => function ($query) { $query->orderBy('order', 'asc'); },
            'rows' => function ($query) { $query->with(['contents']); },
            'charts' => function ($query) { $query->with(['formats']); }
        ])->find($table_id);

        if ($table) {
            $user = Auth::user();
            if ($table->user_id == $user->id) {
                foreach ($table->rows as $key => $row) {
                    $datas = [];
                    $contents = $row->contents;
                    foreach ($table->columns as $ke => $column) {
                        $datas[$ke] = [];
                        for ($i = 0; $i < count($contents); $i++) {
                            if ($contents[$i]->column_id == $column->id) {
                                $datas[$ke] = $contents[$i];
//                                unset($contents[$i]);
                                continue;
                            }
                        }
                    }
                    $table->rows[$key]->datas = $datas;
                }

                return view('home.table.data.list')->with(['table_encode' => $table_encode, 'data' => $table]);
            }
            else return response("不是你的表格！", 404);
        }
        else return response("表格不存在！", 404);
    }


    /*
     * 图
     */
    // view chart add html



    public function index()
    {
        $charts = Chart::with([
            'table' => function ($query) { $query->with([
                'columns' => function ($query) { $query->orderBy('order', 'asc'); },
                'rows' => function ($query) { $query->with(['contents']); }
            ]); },
            'formats' => function ($query) { $query->with(['column']); }
        ])->get();

        foreach($charts as $num => $chart)
        {
            $grouped = $chart->formats->groupBy('type');
            $chart_title = $grouped[1][0];
            $chart_title_column_id = $chart_title->column_id;
            $chart_data_columns = $grouped[2];
//            dd($chart_title->toArray());

            $chart_datas = json_decode(json_encode(['rows'=>[],'columns'=>[]]));
            $chart_datas->columns = $chart_data_columns;


            foreach ($chart->table->rows as $key => $row)
            {
                $datas = [];
                $contents = $row->contents;

                foreach($contents as $k => $v)
                {
                    if ($v->column_id == $chart_title_column_id)
                    {
                        $chart_datas->rows[$key]["row_title"] = $v->content;
                        continue;
                    }
                }


                foreach ($chart_data_columns as $ke => $format)
                {
                    foreach($contents as $k => $v)
                    {
                        if ($v->column_id == $format->column_id)
                        {
                            $datas[$ke] = $v;
                            continue;
                        }
                    }
                    $chart_datas->rows[$key]["datas"] = $datas;
                }
            }

            $chart_datas->rows = collect($chart_datas->rows);
            foreach($chart_datas->rows as $key => $value)
            {
                $chart_datas->rows[$key] = json_decode(json_encode($chart_datas->rows[$key]));
            }

            $charts[$num]->chart_datas = $chart_datas;

        }

//        dd($charts->toArray());
        return view('frontend.index')->with(['charts'=>$charts]);
    }



}