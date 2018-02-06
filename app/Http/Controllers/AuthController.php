<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
//use App\Services\Home\AuthService;
use App\Repositories\Home\AuthRepository;
use App\Models\Softorg;
use App\User;
use Response, Auth, Validator, DB, Exception;


class AuthController extends Controller
{
    //
    private $service;
    private $repo;
    public function __construct()
    {
//        $this->service = new AuthService;
        $this->repo = new AuthRepository;
    }

    // 登陆
    public function user_login()
    {
        if(request()->isMethod('get')) return view('home.auth.login');
        else if(request()->isMethod('post'))
        {
            $where['email'] = request()->get('email');
            $where['password'] = request()->get('password');
            $email = request()->get('email');
            $password = request()->get('password');
            $user = User::whereEmail($email)->first();
            if($user)
            {
                if($user->active == 1)
                {
                    if(password_check($password,$user->password))
                    {
                        Auth::login($user,true);
                        return response_success();
                    }
                    else return response_error([],'账户or密码不正确 ');
                }
                else return response_error([],'账户尚未激活，请先去邮箱激活。');
            }
            else return response_error([],'账户不存在');
        }
    }

    // 退出
    public function user_logout()
    {
        Auth::logout();
        return redirect('/login');
    }

    // 退出
    public function admin_logout()
    {
        Auth::guard('admin')->logout();
        return redirect('/admin/login');
    }

    // 注册
    public function user_register()
    {
        if(request()->isMethod('get')) return view('home.auth.register');
        else if(request()->isMethod('post'))
        {
            return $this->repo->register(request()->all());
        }
    }

    // 激活用户
    public function activation()
    {
        $this->repo->activation(request()->all());
    }





}
