<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class LoginController extends Controller
{   
    /**
     * @api {post} /api/login/login 用户登陆
     * @apiName login
     * @apiGroup login
     * @apiParam {string} phone 手机号码
     * @apiParam {string} possword 密码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *           'name':'刘明',
     *           'phone':'18883562091'
     *       },
     *       "msg":"登陆成功"
     *     }
     */
    public function login(){
        $all=request()->all();
        if (empty($all['phone'])) {
            return $this->rejson(201,'参数错误');
        }else{
            $phone=$all['phone'];
        }

        $data=Db::table('users')->select('id','name')->where('mobile',$phone)->first();
        return $this->rejson(200,'查询成功',$data);
    }
}