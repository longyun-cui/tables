<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Repositories\Common\CommonRepository;


class CommonController extends Controller
{
    //
    private $repo;
    public function __construct()
    {
        $this->repo = new CommonRepository;
    }



    public function change_captcha()
    {
        return response_success(['src'=>captcha_src()],'');
//        return response_success(['img'=>captcha_img()],'');
    }

}
