<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class WalletController extends Controller
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
     * @api {post} /api/wallet/index 余额明细
     * @apiName index
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 分页
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
         "log":[
            "superior_id": "上级id",
            "price": "流动金额",
            "describe": "流动描述",
            "create_time": "流动时间",
            "state": "1获得 2消费"
        ],
        "money": "总金额"
     *      }
     *     }
     */
    public function index(){
        $all = \request() -> all();
        $num = 10;
        // 根据获取的id
        if(empty($all['uid'])){
            return $this->rejson(201,'请输入用户id');
        }
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        // 根据用户id 查询资金流动表
        $data['log'] = DB::table('user_logs')
            -> where('user_id',$all['uid'])
            -> where('type_id',2)
            -> where('user_logs.is_del',0)
            -> select(['user_logs.superior_id','user_logs.price','user_logs.describe','user_logs.create_time','user_logs.state'])
            -> offset($pages)
            -> limit($num)
            -> get();
        $data['money'] = DB::table('users')
            -> where('id',$all['uid'])
            -> select('money')
            -> first()
            ->money ?? '';
        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
    /**
     * @api {post} /api/wallet/cash 提现明细
     * @apiName cash
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 分页
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
         "log":[
            "superior_id": "上级id",
            "price": "流动金额",
            "describe": "流动描述",
            "create_time": "流动时间",
            "state": "1获得 2消费"
        ],
        "money": "总金额"
     *      }
     *     }
     */
    public function cash(){
        $all = \request() -> all();
        $num = 10;
        // 根据获取的id
        if(empty($all['uid'])){
            return $this->rejson(201,'请输入用户id');
        }
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        // 根据用户id 查询资金流动表
        $data['log'] = DB::table('user_logs')
            -> where('user_id',$all['uid'])
            -> where('type_id',3)
            -> where('user_logs.is_del',0)
            -> select(['user_logs.superior_id','user_logs.price','user_logs.describe','user_logs.create_time','user_logs.state'])
            -> offset($pages)
            -> limit($num)
            -> get();
        $data['money'] = DB::table('users')
            -> where('id',$all['uid'])
            -> select('money')
            -> first()
            -> money ?? '';
        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
    /**
     * @api {post} /api/wallet/integral 积分明细
     * @apiName integral
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 分页
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
         "log":[
            "superior_id": "上级id",
            "price": "流动金额",
            "describe": "流动描述",
            "create_time": "流动时间",
            "state": "1获得 2消费"
        ],
        "integral": "总积分"
     *      }
     *     }
     */
    public function integral(){
        $all = \request() -> all();
        $num = 10;
        // 根据获取的id
        if(empty($all['uid'])){
            return $this->rejson(201,'请输入用户id');
        }
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        // 根据用户id 查询资金流动表
        $data['log'] = DB::table('user_logs')
            -> where('user_id',$all['uid'])
            -> where('type_id',1)
            -> where('user_logs.is_del',0)
            -> select(['user_logs.superior_id','user_logs.price','user_logs.describe','user_logs.create_time','user_logs.state'])
            -> offset($pages)
            -> limit($num)
            -> get();
        $data['integral'] = DB::table('users')
            -> where('id',$all['uid'])
            -> select('integral')
            -> first()
            -> integral ?? '';
        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
    /**
     * @api {post} /api/wallet/cash_withdrawal 余额提现
     * @apiName cash_withdrawal
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
                "mobile":'用户联系方式',
                "money":'用户余额',
                "name":'用户名称',
              }
     */
    public function cash_withdrawal(){
        $all = \request() -> all();
        // 根据获取的id
        if(empty($all['uid'])){
            return $this->rejson(201,'请输入用户id');
        }
        // 根据提交的id，查询用户表的内容
        $data = DB::table('users')
            -> where('id',$all['uid'])
            -> select(['mobile','money','name'])
            -> first();
        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
    /**
     * @api {post} /api/wallet/personal 个人中心
     * @apiName personal
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
        "id":'用户id',
        "name":'用户名称',
        "avator":'用户头像',
        "grade":'用户vip等级',
     }
     */
    public function personal(){
        $all = \request() -> all();
        // 根据获取的id
        if(empty($all['uid'])){
            return $this->rejson(201,'请输入用户id');
        }
        $data = DB::table('users')
            -> join('vip','users.id','=','vip.user_id')
            -> where('users.id',$all['uid'])
            -> select(['users.id','users.name','users.avator','vip.grade'])
            -> first();
        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
// W83tVnay3ZPCsMA
}