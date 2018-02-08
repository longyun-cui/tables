<?php
namespace App\Repositories\Home;

use App\Models\Table;
use App\Models\Column;
use App\Models\Row;
use App\Repositories\Common\CommonRepository;
use Response, Auth, Validator, DB, Exception;
use QrCode;

class ColumnRepository {

    private $model;
    private $repo;
    public function __construct()
    {
        $this->model = new Column;
    }

    // 返回列表数据
    public function get_list_datatable($post_data)
    {
        $user = Auth::user();
        $user_id = $user->id;
        $query = Table::select("*")->where('user_id',$user_id);
        $total = $query->count();

        $draw  = isset($post_data['draw'])  ? $post_data['draw']  : 1;
        $skip  = isset($post_data['start'])  ? $post_data['start']  : 0;
        $limit = isset($post_data['length']) ? $post_data['length'] : 20;

        if(isset($post_data['order']))
        {
            $columns = $post_data['columns'];
            $order = $post_data['order'][0];
            $order_column = $order['column'];
            $order_dir = $order['dir'];

            $field = $columns[$order_column]["data"];
            $query->orderBy($field, $order_dir);
        }
        else $query->orderBy("updated_at", "desc");

        if($limit == -1) $list = $query->get();
        else $list = $query->skip($skip)->take($limit)->get();

        foreach ($list as $k => $v)
        {
            $list[$k]->encode_id = encode($v->id);
        }
        return datatable_response($list, $draw, $total);
    }

    // 返回添加视图
    public function view_create()
    {
        return view('home.table.edit');
    }
    // 返回编辑视图
    public function view_edit()
    {
        $id = request("id",0);
        $decode_id = decode($id);
        if(!$decode_id) return response("参数有误", 404);

        if($decode_id == 0)
        {
            $org = Table::find($decode_id);
            return view('home.table.edit')->with(['operate'=>'create', 'encode_id'=>$id, 'org'=>$org]);
        }
        else
        {
            $table = Table::with(['columns'=>function($query) { $query->orderBy('order','asc'); }])->find($decode_id);
            if($table)
            {
                unset($table->id);
                return view('home.table.edit')->with(['operate'=>'edit', 'encode_id'=>$id, 'data'=>$table]);
            }
            else return response("表格不存在！", 404);
        }
    }

    // 保存数据
    public function save($post_data)
    {
        $messages = [
            'table_id.required' => '参数有误',
            'id.required' => '参数有误',
//            'name.required' => '请输入后台名称',
            'title.required' => '请输入标题',
//            'default.required' => '请输入默认值',
        ];
        $v = Validator::make($post_data, [
            'table_id' => 'required',
            'id' => 'required',
//            'name' => 'required',
            'title' => 'required'//,
//            'default' => 'required'
        ], $messages);
        if ($v->fails())
        {
            $messages = $v->errors();
            return response_error([],$messages->first());
        }

        $user = Auth::user();

        $table_id = decode($post_data["table_id"]);
        $table = Table::find($table_id);
        if($table)
        {
            if($table->user_id == $user->id)
            {
                $operate = decode($post_data["operate"]);
                $id = decode($post_data["id"]);
                if(intval($id) !== 0 && !$id) return response_error();

                $post_data["table_id"] = $table->id;

                DB::beginTransaction();
                try
                {
                    if($id == 0) // $id==0，添加一个新的列
                    {
                        $column = new Column;
                        $post_data["user_id"] = $user->id;
                    }
                    else // 编辑lie
                    {
                        $column = Column::find($id);
                        if(!$column) return response_error([],"该列不存在，刷新页面重试");
                        if($table->user_id != $user->id) return response_error([],"你没有操作权限");
                    }

                    $bool = $column->fill($post_data)->save();
                    if($bool)
                    {
                    }
                    else throw new Exception("insert-column-fail");

                    DB::commit();
                    return response_success();
                }
                catch (Exception $e)
                {
                    DB::rollback();
//                    $msg = $e->getMessage();
//                    exit($e->getMessage());
                    $fail_text = '操作失败，请重试！';
                    $fail_text = $e->getMessage();
                    return response_fail([],$fail_text);
                }
            }
            else return response_fail([],'您没有权限，刷新重试！');
        }
        else return response_fail([],'表格不存在！');
    }

    // 删除
    public function delete($post_data)
    {
        $user = Auth::user();
        $id = decode($post_data["column_id"]);
        if(intval($id) !== 0 && !$id) return response_error([],"该文章不存在，刷新页面试试");

        $column = Column::find($id);
        if($column->user_id != $user->id) return response_error([],"你没有操作权限");

        DB::beginTransaction();
        try
        {
            $bool = $column->delete();
            if($bool)
            {
//                $contents = Item::where($column->table_id)->get();
//                if(count($contents) > 0)
//                {
//                    $bool1 = $contents->delete();
//                    if(!$bool1) throw new Exception("delete-contents--fail");
//                }
            }
            else throw new Exception("delete-column--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            return response_fail([],'删除失败，请重试');
        }

    }

    // 问题排序
    public function sort($post_data)
    {
        $table_id = decode($post_data["table_id"]);
        if(!$table_id) return response_error();

        $columns = collect($post_data['column'])->values()->toArray();

        foreach($columns as $k => $v)
        {
            $id = $v['id'];
            $column = Column::find($id);
            if(!$column) return response_error();
            $order['order'] = $k;
            $bool = $column->fill($order)->save();
        }
        return response_success();
    }


    // 启用
    public function enable($post_data)
    {
        $admin = Auth::guard('admin')->user();
        $id = decode($post_data["id"]);
        if(intval($id) !== 0 && !$id) return response_error([],"该文章不存在，刷新页面试试");

        $article = Article::find($id);
        if($article->admin_id != $admin->id) return response_error([],"你没有操作权限");
        $update["active"] = 1;
        DB::beginTransaction();
        try
        {
            $bool = $article->fill($update)->save();
            if($bool)
            {
                $item = Item::find($article->item_id);
                if($item)
                {
                    $bool1 = $item->fill($update)->save();
                    if(!$bool1) throw new Exception("update-item--fail");
                }
            }
            else throw new Exception("update-article--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            return response_fail([],'启用失败，请重试');
        }
    }

    // 禁用
    public function disable($post_data)
    {
        $admin = Auth::guard('admin')->user();
        $id = decode($post_data["id"]);
        if(intval($id) !== 0 && !$id) return response_error([],"该文章不存在，刷新页面试试");

        $article = Article::find($id);
        if($article->admin_id != $admin->id) return response_error([],"你没有操作权限");
        $update["active"] = 9;
        DB::beginTransaction();
        try
        {
            $bool = $article->fill($update)->save();
            if($bool)
            {
                $item = Item::find($article->item_id);
                if($item)
                {
                    $bool1 = $item->fill($update)->save();
                    if(!$bool1) throw new Exception("update-item--fail");
                }
            }
            else throw new Exception("update-article--fail");

            DB::commit();
            return response_success([]);
        }
        catch (Exception $e)
        {
            DB::rollback();
            return response_fail([],'禁用失败，请重试');
        }
    }


}