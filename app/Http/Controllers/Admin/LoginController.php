<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class LoginController extends Controller
{

    /** 获取验证码
     * @return mixed
     */
    public function captcha()
    {
        return response()->json(['status_code'=>'1','message' =>'created succeed','url'=> app('captcha')->create('default', true)]);
    }

    /** 用户登陆
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        //验证图片操作
        if (!captcha_api_check($request->captcha, $request->catKey)){
            return response()->json(['status_code' => 400, 'message' => '验证码不匹配' ]);
        }

        $input = $request->except(['captcha','catKey']);

        if(!$token = Auth::guard('admin')->attempt($input)){
            return code_response('10004','账号或密码错误');
        };

        $data['token'] = 'Bearer '. $token;

        return code_response('10','登陆成功','200',$data);
    }
}