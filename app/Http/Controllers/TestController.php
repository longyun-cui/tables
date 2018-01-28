<?php

namespace App\Http\Controllers;

use function foo\func;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;

class TestController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
    }


    public function index()
    {
        return view('admin.index');
    }

    public function eloquent()
    {
    }
}
