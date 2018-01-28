<?php

namespace App\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

//use App\Services\Admin\TableService;
use App\Repositories\Home\HomeRepository;

use App\Models\Table;

use Image;

class HomeController extends Controller
{
    //
    private $service;
    private $repo;
    public function __construct()
    {
//        $this->service = new TableService;
//        $this->repo = new HomeRepository;
    }


    public function index()
    {
        return view('home.index');
    }



}
