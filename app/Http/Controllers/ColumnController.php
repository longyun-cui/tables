<?php

namespace App\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

//use App\Services\Admin\ColumnService;
use App\Repositories\Home\ColumnRepository;


class ColumnController extends Controller
{
    //
    private $service;
    private $repo;
    public function __construct()
    {
//        $this->service = new TableService;
        $this->repo = new ColumnRepository;
    }

    // 列表
    public function viewList()
    {
        if(request()->isMethod('get')) return view('home.table.list');
        else if(request()->isMethod('post')) return $this->repo->get_list_datatable(request()->all());
    }

    // 创建
    public function createAction()
    {
        return $this->repo->view_create();
    }

    // 编辑
    public function editAction()
    {
        if(request()->isMethod('get')) return $this->repo->view_edit();
        else if (request()->isMethod('post')) return $this->repo->save(request()->all());
    }

    // 删除
    public function deleteAction()
    {
        return $this->repo->delete(request()->all());
    }

    // 【排序】
    public function sortAction()
    {
        return $this->repo->sort(request()->all());
    }



}
