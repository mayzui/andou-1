<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class OpinionController extends Controller
{
    public function __construct()
    {
        $all=request()->all();
        if (empty($all['uid'])||empty($all['token'])) {
            return $this->rejson(201,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==201) {
            return $this->rejson($check['code'],$check['msg']);
        }
    }
    /**
     * @api {post} /api/opinion/index 意见反馈
     * @apiName index
     * @apiGroup opinion
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} content 意见内容
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {

     }
     */
    public function index(){
        $all = \request() -> all();
        // 根据获取的id
        if(empty($all['uid'])){
            return $this->rejson(201,'请输入用户id');
        }
        $data = [
            'user_id' => $all['uid'],
            'content' => $all['content'],
        ];
        $i = DB::table('feedback') -> insert($data);
        if($i){
            return $this->rejson(200,'添加成功',['success']);
        }else{
            return $this->rejson(201,'添加失败',['error']);
        }
    }
    /**
     * @api {post} /api/opinion/set 设置
     * @apiName set
     * @apiGroup opinion
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
        "avator":'头像',
        "name":'昵称',
        "mobile":'电话',
        "password":'密码',
     }
     */
    public function set(){
        $all = \request() -> all();
        // 根据获取的id
        if(empty($all['uid'])){
            return $this->rejson(201,'请输入用户id');
        }
        $data['information'] = DB::table('users')
            -> where('id',$all['uid'])
            -> select(['avator','name','mobile','password'])
            -> first();
        $data['edition'] = DB::table('config') -> where('id',9) -> select('value') -> first() -> value ?? '';

        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'查询失败',$data);
        }
    }
// W83tVnay3ZPCsMA
}