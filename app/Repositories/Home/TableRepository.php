<?php
namespace App\Repositories\Home;

use App\Models\Table;
use App\Models\Column;
use App\Models\Row;
use App\Models\Content;
use App\Models\Chart;
use App\Models\Format;
use App\Repositories\Common\CommonRepository;
use Response, Auth, Validator, DB, Exception;
use QrCode;

class TableRepository
{

    private $model;
    private $repo;

    public function __construct()
    {
        $this->model = new Table;
    }

    // 返回平台主页【表格】页
    public function index()
    {

        $table_encode = request("id", 0);
        $table_id = decode($table_encode);
        if (!$table_id && intval($table_id) !== 0) return response("参数有误", 404);

        $tables = Table::with([
            'user',
            'columns' => function ($query) { $query->orderBy('order', 'asc'); },
            'rows' => function ($query) { $query->with(['contents'])->where('is_shared',1); },
            'charts' => function ($query) { $query->with(['formats']); }
        ])->where(['is_shared'=>1])->orderBy('id', 'desc')->get();

        foreach($tables as $num => $table)
        {
            if ($table)
            {
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

            }
        }

        return view('frontend.root.tables')->with(['datas'=>$tables]);
    }



    // 返回列表数据
    public function get_list_datatable($post_data)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $query = Table::select("*")->where('user_id', $user_id);
        $total = $query->count();

        $draw = isset($post_data['draw']) ? $post_data['draw'] : 1;
        $skip = isset($post_data['start']) ? $post_data['start'] : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

        if (isset($post_data['order'])) {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        } else $query->orderBy("updated_at", "desc");

        if ($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v) {
            $list[$k]->encode_id = encode($v->id);
        }
        return datatable_response($list, $draw, $total);
    }



    // 返回添加视图
    public function view_create()
    {
        return view('home.table.create');
    }

    // 返回编辑视图
    public function view_edit()
    {
        $id = request("id", 0);
        $decode_id = decode($id);
        if (!$decode_id) return response("参数有误", 404);

        if ($decode_id == 0) {
            $org = Table::find($decode_id);
            return view('home.table.edit')->with(['operate' => 'create', 'encode_id' => $id, 'org' => $org]);
        } else {
            $table = Table::with(['columns' => function ($query) {
                $query->orderBy('order', 'asc');
            }])->find($decode_id);
            if ($table) {
                unset($table->id);
                return view('home.table.edit')->with(['operate' => 'edit', 'encode_id' => $id, 'data' => $table]);
            } else return response("表格不存在！", 404);
        }
    }

    // 保存数据
    public function save($post_data)
    {
        $messages = [
            'id.required' => '参数有误',
            'name.required' => '请输入名称',
            'title.required' => '请输入标题',
        ];
        $v = Validator::make($post_data, [
            'id' => 'required',
            'name' => 'required',
            'title' => 'required'
        ], $messages);
        if ($v->fails()) {
            $messages = $v->errors();
            return response_error([], $messages->first());
        }

        $user = Auth::user();

        $id = decode($post_data["id"]);
        $operate = decode($post_data["operate"]);
        if (intval($id) !== 0 && !$id) return response_error();

        DB::beginTransaction();
        try {
            if ($id == 0) // $id==0，添加一个新的表格
            {
                $table = new Table;
                $post_data["user_id"] = $user->id;

            } else // 编辑表格
            {
                $table = Table::find($id);
                if (!$table) return response_error([], "该文章不存在，刷新页面重试");
                if ($table->user_id != $user->id) return response_error([], "你没有操作权限");
            }

            $bool = $table->fill($post_data)->save();
            if ($bool) {
                $encode_id = encode($user->id);
                // 目标URL
                $url = 'http://www.softorg.cn/article?id=' . $encode_id;
                // 保存位置
                $qrcode_path = 'resource/user/' . $user->id . '/unique/tables';
                if (!file_exists(storage_path($qrcode_path)))
                    mkdir(storage_path($qrcode_path), 0777, true);
                // qrcode图片文件
                $qrcode = $qrcode_path . '/qrcode_table_' . $encode_id . '.png';
                QrCode::errorCorrection('H')->format('png')->size(160)->margin(0)->encoding('UTF-8')->generate($url, storage_path($qrcode));


                if (!empty($post_data["cover"])) {
                    $upload = new CommonRepository();
                    $result = $upload->upload($post_data["cover"], 'org-' . $user->id . '-unique-articles', 'cover_article_' . $encode_id);
                    if ($result["status"]) {
                        $user->cover_pic = $result["data"];
                        $user->save();
                    } else throw new Exception("upload-cover-fail");
                }

            } else throw new Exception("insert-table-fail");


            DB::commit();
            return response_success(['id' => $encode_id]);
        } catch (Exception $e) {
            DB::rollback();
            $msg = $e->getMessage();
//            exit($e->getMessage());
            return response_fail([], $e->getMessage());
        }
    }



    // 【删除表格】
    public function delete($post_data)
    {
        $id = decode($post_data["id"]);
        if (intval($id) !== 0 && !$id) return response_error([], "该表格不存在，刷新页面试试");

        $user = Auth::user();
        $table = Table::find($id);
        if ($table->user_id != $user->id) return response_error([], "你没有操作权限");

        DB::beginTransaction();
        try {
            $bool = $table->delete();
            if (!$bool) throw new Exception("delete--table--fail");

            DB::commit();
            return response_success([]);
        } catch (Exception $e) {
            DB::rollback();
            return response_fail([], '删除失败，请重试');
        }

    }

    // 【分享】
    public function enshared($post_data)
    {
        $id = decode($post_data["id"]);
        if (intval($id) !== 0 && !$id) return response_error([], "该表格不存在，刷新页面试试");

        $user = Auth::user();
        $table = Table::find($id);
        if ($table->user_id != $user->id) return response_error([], "你没有操作权限");
        $update["is_shared"] = 1;
        DB::beginTransaction();
        try {
            $bool = $table->fill($update)->save();
            if(!$bool) throw new Exception("update--table--fail");

            DB::commit();
            return response_success([]);
        } catch (Exception $e) {
            DB::rollback();
            return response_fail([], '分享失败，请重试');
        }
    }

    // 【取消分享】
    public function disshared($post_data)
    {
        $id = decode($post_data["id"]);
        if (intval($id) !== 0 && !$id) return response_error([], "该表格不存在，刷新页面试试");

        $user = Auth::user();
        $table = Table::find($id);
        if ($table->user_id != $user->id) return response_error([], "你没有操作权限");
        $update["is_shared"] = 9;
        DB::beginTransaction();
        try {
            $bool = $table->fill($update)->save();
            if(!$bool) throw new Exception("update--table--fail");

            DB::commit();
            return response_success([]);
        } catch (Exception $e) {
            DB::rollback();
            return response_fail([], '禁用失败，请重试');
        }
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

    // view data add html
    public function view_data_get_add($post_data)
    {
        $table_encode = $post_data["table_id"];
        $table_id = decode($post_data["table_id"]);
        if (!$table_id && intval($table_id) !== 0) return response_error([], '参数有误！');

        $table = Table::with([
            'columns' => function ($query) {
                $query->orderBy('order', 'asc');
            }
        ])->find($table_id);
        if ($table) {
            $user = Auth::user();
            if ($table->user_id == $user->id) {
                $return["html"] = view('home.table.data.add')->with(['table_encode' => $table_encode, 'data' => $table])->__toString();
                return response_success($return);
            } else return response_error([], '不是你的表格！');
        } else return response_error([], '表格不存在！');
    }

    // view data edit html
    public function view_data_get_edit($post_data)
    {
        $table_encode = $post_data["table_id"];
        $table_id = decode($post_data["table_id"]);
        if (!$table_id && intval($table_id) !== 0) return response_error([], '参数有误！');

        $row_encode = $post_data["row_id"];
        $row_id = decode($post_data["row_id"]);
        if (!$row_id && intval($row_id) !== 0) return response_error([], '参数有误！');

        $table = Table::with([
            'columns' => function ($query) {
                $query->orderBy('order', 'asc');
            }
        ])->find($table_id);
        if ($table) {
            $user = Auth::user();
            if ($table->user_id == $user->id)
            {
                $row = Row::with(['contents'])->find($row_id);
                if ($row)
                {
                    $datas = [];
                    $contents = $row->contents;
                    foreach ($table->columns as $key => $column)
                    {
                        $datas[$key] = [];
                        foreach ($contents as $k => $v)
                        {
                            if ($v->column_id == $column->id)
                            {
                                $datas[$key] = $v;
//                                unset($contents[$i]);
                                continue;
                            }
                        }
                        $table->columns[$key]->content = $datas[$key];
                    }
                }
                $return["html"] = view('home.table.data.edit')
                    ->with(['table_encode' => $table_encode, 'row_encode' => $row_encode, 'data' => $table])->__toString();
                return response_success($return);
            }
            else return response_error([], '不是你的表格！');
        }
        else return response_error([], '表格不存在！');
    }

    // save
    public function data_save($post_data)
    {
        $table_id = decode($post_data["table_id"]);
        if (!$table_id && intval($table_id) !== 0) return response_error([], '参数有误！');

        $table = Table::find($table_id);
        if ($table) {
            $user = Auth::user();
            if ($table->user_id == $user->id) {
                DB::beginTransaction();
                try {
                    $operate = $post_data["operate"];
                    if ($operate == 'create') {
                        $row = new Row;
                        $row_create['user_id'] = $user->id;
                        $row_create['table_id'] = $table->id;
                        $bool = $row->fill($row_create)->save();
                        if ($bool) {
                        } else throw new Exception("insert-row-fail");
                    } else if ($operate == 'edit') {
                        $row_id = decode($post_data["row_id"]);
                        if (intval($row_id) !== 0 && !$row_id) throw new Exception("row-fail");
                        $row = Row::find($row_id);
                        if ($row) {
                        } else throw new Exception("row-no-exist");
                    }

                    $columns_data = $post_data["column"];
                    foreach ($columns_data as $k => $v) {
                        $content_insert = [];
                        if ($v["content_id"] != 0) {
                            $content = Content::find($v["content_id"]);
                        } else {
                            $content = new Content;

//                            $content["user_id"] = $user->id;
//                            $content["table_id"] = $table->id;
//                            $content["row_id"] = $row->id;
//                            $content["column_id"] = $v["column_id"];
//                            $content["content"] = $v["value"];
//                            $content_insert[] = $content;
                        }
                        $content->user_id = $user->id;
                        $content->table_id = $table->id;
                        $content->row_id = $row->id;
                        $content->column_id = $v["column_id"];
                        $content->content = $v["value"];
                        $content->save();
                        unset($columns_data[$k]);
                    }
//                    $contents = $row->contents()->createMany($content_insert);

                    DB::commit();
                    return response_success();
                } catch (Exception $e) {
                    DB::rollback();
                    $msg = $e->getMessage();
//                    exit($e->getMessage());
//                    $msg = '';
                    return response_fail([], $msg);
                }

            } else return response_error([], '不是你的表格！');
        } else return response_error([], '表格不存在！');
    }

    // Row 数据行 【删除】
    public function data_delete($post_data)
    {
        $table_encode = $post_data["table_id"];
        $table_id = decode($table_encode);
        if (intval($table_id) !== 0 && !$table_id) return response_error([], '参数有误！');

        $row_encode = $post_data["row_id"];
        $row_id = decode($row_encode);
        if (intval($row_id) !== 0 && !$row_id) return response_error([], '参数有误！');


        $table = Table::find($table_id);
        if($table)
        {
            $user = Auth::user();
            if ($table->user_id == $user->id)
            {
                $row = Row::find($row_id);
                if($row)
                {
                    if ($row->user_id == $user->id)
                    {
                        DB::beginTransaction();
                        try {
                            $bool = $row->delete();
                            if($bool)
                            {
                                $num = Content::where('row_id', $row_id)->count();
                                if($num)
                                {
                                    $bool1 = Content::where('row_id', $row_id)->delete();
                                    if($bool1 != $num) throw new Exception('delete-contents-fail');
                                }
                            }
                            else throw new Exception('delete-row-fail');

                            DB::commit();
                            return response_success();
                        } catch (Exception $e) {
                            DB::rollback();
                            $msg = $e->getMessage();
//                            exit($e->getMessage());
//                            $msg = '删除失败';
                            return response_fail([], $msg);
                        }
                    }
                    else return response_error([], '该记录不是你的！');
                }
                else  return response_error([], '记录不存在！');
            }
            else return response_error([], '不是你的表格！');
        }
        else return response_error([], '表格不存在！');
    }

    // Row 数据行 【分享】
    public function data_enshared($post_data)
    {
        $table_encode = $post_data["table_id"];
        $table_id = decode($table_encode);
        if (intval($table_id) !== 0 && !$table_id) return response_error([], '参数有误！');

        $row_encode = $post_data["row_id"];
        $row_id = decode($row_encode);
        if (intval($row_id) !== 0 && !$row_id) return response_error([], '参数有误！');


        $table = Table::find($table_id);
        if($table)
        {
            $user = Auth::user();
            if ($table->user_id == $user->id)
            {
                $row = Row::find($row_id);
                if($row)
                {
                    if ($row->user_id == $user->id)
                    {
                        DB::beginTransaction();
                        try {
                            $update["is_shared"] = 1;
                            $bool = $row->fill($update)->save();
                            if(!$bool) throw new Exception('update-row-fail');

                            DB::commit();
                            return response_success();
                        } catch (Exception $e) {
                            DB::rollback();
//                            exit($e->getMessage());
//                            $msg = $e->getMessage();
                            $msg = '分享失败';
                            return response_fail([], $msg);
                        }
                    }
                    else return response_error([], '该记录不是你的！');
                }
                else  return response_error([], '记录不存在！');
            }
            else return response_error([], '不是你的表格！');
        }
        else return response_error([], '表格不存在！');
    }

    // Row 数据行 【取消分享】
    public function data_disshared($post_data)
    {
        $table_encode = $post_data["table_id"];
        $table_id = decode($table_encode);
        if (intval($table_id) !== 0 && !$table_id) return response_error([], '参数有误！');

        $row_encode = $post_data["row_id"];
        $row_id = decode($row_encode);
        if (intval($row_id) !== 0 && !$row_id) return response_error([], '参数有误！');


        $table = Table::find($table_id);
        if($table)
        {
            $user = Auth::user();
            if ($table->user_id == $user->id)
            {
                $row = Row::find($row_id);
                if($row)
                {
                    if ($row->user_id == $user->id)
                    {
                        DB::beginTransaction();
                        try {
                            $update["is_shared"] = 9;
                            $bool = $row->fill($update)->save();
                            if(!$bool) throw new Exception('update-row-fail');

                            DB::commit();
                            return response_success();
                        } catch (Exception $e) {
                            DB::rollback();
//                            exit($e->getMessage());
//                            $msg = $e->getMessage();
                            $msg = '取消分享失败';
                            return response_fail([], $msg);
                        }
                    }
                    else return response_error([], '该记录不是你的！');
                }
                else  return response_error([], '记录不存在！');
            }
            else return response_error([], '不是你的表格！');
        }
        else return response_error([], '表格不存在！');
    }



    /*
     * 图
     */
    // view chart add html
    public function view_chart_get_add($post_data)
    {
        $table_encode = $post_data["table_id"];
        $table_id = decode($table_encode);
        if (!$table_id && intval($table_id) !== 0) return response_error([], '参数有误！');

        $table = Table::with([
            'columns' => function ($query) {
                $query->orderBy('order', 'asc');
            }
        ])->find($table_id);
        if ($table) {
            $user = Auth::user();
            if ($table->user_id == $user->id) {
                $return["html"] = view('home.table.chart.add')->with(['table_encode' => $table_encode, 'data' => $table])->__toString();
                return response_success($return);
            } else return response_error([], '不是你的表格！');
        } else return response_error([], '表格不存在！');
    }

    // view chart edit html
    public function view_chart_get_edit($post_data)
    {
        $chart_encode = $post_data["chart_id"];
        $chart_id = decode($chart_encode);
        if (!$chart_id && intval($chart_id) !== 0) return response_error([], '参数有误！');

        $chart = Chart::with([
            'table'=> function ($query) { $query->with([
                'columns' => function ($query) { $query->orderBy('order', 'asc'); }
            ]); },
            'formats'
        ])->find($chart_id);

        if ($chart) {
            $user = Auth::user();
            if ($chart->user_id == $user->id) {
                $return["html"] = view('home.table.chart.edit')
                    ->with(['chart_encode' => $chart_encode, 'table_encode' => encode($chart->table->id), 'data' => $chart])->__toString();
                return response_success($return);
            } else return response_error([], '不是你的表格！');
        } else return response_error([], '表格不存在！');
    }

    // save
    public function chart_save($post_data)
    {
        $messages = [
            'table_id.required' => '参数有误',
            'name.required' => '请输入名称',
            'title.required' => '请输入标题',
            'type.required' => '请选择图类型',
            'format_title' => '参数有误',
        ];
        $v = Validator::make($post_data, [
            'table_id' => 'required',
            'name' => 'required',
            'title' => 'required',
            'type' => 'required|numeric',
            'format_title.id' => 'required|numeric',
            'format_title.value' => 'required|numeric'
        ], $messages);
        if ($v->fails()) {
            $messages = $v->errors();
            return response_error([], $messages->first());
        }

        $table_id = decode($post_data["table_id"]);
        if (!$table_id && intval($table_id) !== 0) return response_error([], '参数有误！');

        $table = Table::find($table_id);
        if ($table) {
            $user = Auth::user();
            if ($table->user_id == $user->id) {
                DB::beginTransaction();
                try {
                    $operate = $post_data["operate"];
                    if ($operate == 'create') {
                        $chart = new Chart;
                        $chart->user_id = $user->id;
                        $chart->table_id = $table->id;
                        $chart->type = $post_data["type"];
                        $chart->name = $post_data["name"];
                        $chart->title = $post_data["title"];
                        $bool = $chart->save();
                        if ($bool) {
                        } else throw new Exception("insert-chart-fail");
                    } else if ($operate == 'edit') {
                        $chart_id = decode($post_data["chart_id"]);
                        if (intval($chart_id) !== 0 && !$chart_id) throw new Exception("chart-id-error");
                        $chart = Chart::find($chart_id);
                        if ($chart) {
                        } else throw new Exception("row-no-exist");
                    }

                    $formats_count = Format::where('chart_id', $chart->id)->count();
                    if($formats_count)
                    {
                        $num = Format::where('chart_id', $chart->id)->delete();
                        if($num != $formats_count) throw new Exception("format-delete-fail");
                    }

                    if($post_data["format_title"]["id"] == 0)
                    {
                        $format_title = new Format;
                        $format_title->user_id = $user->id;
                        $format_title->table_id = $table->id;
                        $format_title->chart_id = $chart->id;
                    }
                    else
                    {
                        $format_title = Format::find($post_data["format_title"]["id"]);
                        if($format_title->user_id != $user->id) throw new Exception("format-title-no-mine");
                    }
                    $format_title->type = 1;
                    $format_title->column_id = $post_data["format_title"]["value"];
                    $format_title->save();

                    $datas = $post_data["datas"];
                    $formats = $post_data["formats"];
                    $maxs = $post_data["maxs"];
                    foreach ($datas as $k => $v) {
                        $format_id = $formats[$k]["id"];
                        if ($format_id != 0) {
                            $format = Format::find($format_id);
                        } else {
                            $format = new Format;
                        }
                        $format->type = 2;
                        $format->user_id = $user->id;
                        $format->table_id = $table->id;
                        $format->chart_id = $chart->id;
                        $format->column_id = $v["value"];
                        if($post_data["type"] == 4) $format->max = $maxs[$k]["value"];
                        $format->save();
                        unset($datas[$k]);
                    }
//                    $contents = $row->contents()->createMany($content_insert);

                    DB::commit();
                    return response_success();
                } catch (Exception $e) {
                    DB::rollback();
                    $msg = $e->getMessage();
//                    exit($e->getMessage());
//                    $msg = '';
                    return response_fail([], $msg);
                }

            } else return response_error([], '不是你的表格！');
        } else return response_error([], '表格不存在！');
    }

    // Chart 图 【删除】
    public function chart_delete($post_data)
    {
        $table_encode = $post_data["table_id"];
        $table_id = decode($table_encode);
        if (intval($table_id) !== 0 && !$table_id) return response_error([], '参数有误！');

        $chart_encode = $post_data["chart_id"];
        $chart_id = decode($chart_encode);
        if (intval($chart_id) !== 0 && !$chart_id) return response_error([], '参数有误！');


        $table = Table::find($table_id);
        if($table)
        {
            $user = Auth::user();
            if ($table->user_id == $user->id)
            {
                $chart = Chart::find($chart_id);
                if($chart)
                {
                    if ($chart->user_id == $user->id)
                    {
                        DB::beginTransaction();
                        try {
                            $bool = $chart->delete();
                            if($bool)
                            {
                                $num = Format::where('chart_id', $chart_id)->count();
                                if($num)
                                {
                                    $bool1 = Format::where('chart_id', $chart_id)->delete();
                                    if($bool1 != $num) throw new Exception('delete-format-fail');
                                }
                            }
                            else throw new Exception('delete-chart-fail');

                            DB::commit();
                            return response_success();
                        } catch (Exception $e) {
                            DB::rollback();
//                            exit($e->getMessage());
//                            $msg = $e->getMessage();
                            $msg = '删除失败';
                            return response_f、ail([], $msg);
                        }
                    }
                    else return response_error([], '该"图"不是你的！');
                }
                else  return response_error([], '"图"不存在！');
            }
            else return response_error([], '不是你的"图"！');
        }
        else return response_error([], '表格不存在！');
    }

    // Chart 图 【分享】
    public function chart_enshared($post_data)
    {
        $table_encode = $post_data["table_id"];
        $table_id = decode($table_encode);
        if (intval($table_id) !== 0 && !$table_id) return response_error([], '参数有误！');

        $chart_encode = $post_data["chart_id"];
        $chart_id = decode($chart_encode);
        if (intval($chart_id) !== 0 && !$chart_id) return response_error([], '参数有误！');


        $table = Table::find($table_id);
        if($table)
        {
            $user = Auth::user();
            if ($table->user_id == $user->id)
            {
                $chart = Chart::find($chart_id);
                if($chart)
                {
                    if ($chart->user_id == $user->id)
                    {
                        DB::beginTransaction();
                        try {
                            $update["is_shared"] = 1;
                            $bool = $chart->fill($update)->save();
                            if(!$bool) throw new Exception('update-chart-fail');

                            DB::commit();
                            return response_success();
                        } catch (Exception $e) {
                            DB::rollback();
//                            exit($e->getMessage());
//                            $msg = $e->getMessage();
                            $msg = '分享失败';
                            return response_f、ail([], $msg);
                        }
                    }
                    else return response_error([], '该"图"不是你的！');
                }
                else  return response_error([], '"图"不存在！');
            }
            else return response_error([], '不是你的"图"！');
        }
        else return response_error([], '表格不存在！');
    }

    // Chart 图 【取消分享】
    public function chart_disshared($post_data)
    {
        $table_encode = $post_data["table_id"];
        $table_id = decode($table_encode);
        if (intval($table_id) !== 0 && !$table_id) return response_error([], '参数有误！');

        $chart_encode = $post_data["chart_id"];
        $chart_id = decode($chart_encode);
        if (intval($chart_id) !== 0 && !$chart_id) return response_error([], '参数有误！');


        $table = Table::find($table_id);
        if($table)
        {
            $user = Auth::user();
            if ($table->user_id == $user->id)
            {
                $chart = Chart::find($chart_id);
                if($chart)
                {
                    if ($chart->user_id == $user->id)
                    {
                        DB::beginTransaction();
                        try {
                            $update["is_shared"] = 9;
                            $bool = $chart->fill($update)->save();
                            if(!$bool) throw new Exception('update-chart-fail');

                            DB::commit();
                            return response_success();
                        } catch (Exception $e) {
                            DB::rollback();
//                            exit($e->getMessage());
//                            $msg = $e->getMessage();
                            $msg = '分享失败';
                            return response_f、ail([], $msg);
                        }
                    }
                    else return response_error([], '该"图"不是你的！');
                }
                else  return response_error([], '"图"不存在！');
            }
            else return response_error([], '不是你的"图"！');
        }
        else return response_error([], '表格不存在！');
    }



    public function view_chart($post_data)
    {
        $chart_encode = request("id", 0);
        $chart_id = decode($chart_encode);
        if (!$chart_id && intval($chart_id) !== 0) return response("参数有误", 404);

        $chart = Chart::with([
            'table' => function ($query) { $query->with([
                'columns' => function ($query) { $query->orderBy('order', 'asc'); },
                'rows' => function ($query) { $query->with(['contents']); }
            ]); },
            'formats' => function ($query) { $query->with(['column']); }
        ])->find($chart_id);

        if($chart)
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
        }
        $chart_datas->rows = collect($chart_datas->rows);
        foreach($chart_datas->rows as $key => $value)
        {
            $chart_datas->rows[$key] = json_decode(json_encode($chart_datas->rows[$key]));
        }
//        dd($chart_datas);
        return view('frontend.chart.index')->with(['chart'=>$chart,'chart_datas'=>$chart_datas]);
    }



}