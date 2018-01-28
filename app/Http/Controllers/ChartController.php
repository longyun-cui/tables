<?php

namespace App\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Front\ChartRepository;

class ChartController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new ChartRepository;
    }


    public function index()
    {
        return $this->repo->index();
    }



}
