<?php

namespace App\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

//use App\Services\Admin\TableService;
use App\Repositories\Home\TableRepository;


class TableController extends Controller
{
    //
    private $service;
    private $repo;
    public function __construct()
    {
//        $this->service = new TableService;
        $this->repo = new TableRepository;
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



    /*
     * Home Data
     */
    public function data_index()
    {
        return $this->repo->view_data_index();
    }

    public function data_get_add()
    {
        return $this->repo->view_data_get_add(request()->all());
    }

    public function data_get_edit()
    {
        return $this->repo->view_data_get_edit(request()->all());
    }

    public function data_edit()
    {
        return $this->repo->data_save(request()->all());
    }

    public function data_delete()
    {
        return $this->repo->data_delete(request()->all());
    }


    /*
     * Home Chart
     */
    public function chart_index()
    {
        return $this->repo->view_chart_index();
    }

    public function chart_get_add()
    {
        return $this->repo->view_chart_get_add(request()->all());
    }

    public function chart_get_edit()
    {
        return $this->repo->view_chart_get_edit(request()->all());
    }

    public function chart_edit()
    {
        return $this->repo->chart_save(request()->all());
    }

    public function chart_delete()
    {
        return $this->repo->chart_delete(request()->all());
    }


    /*
     * chart
     */

    public function view_chart()
    {
        return $this->repo->view_chart(request()->all());
    }






}
