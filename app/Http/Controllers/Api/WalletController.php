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
            return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
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
     * @apiParam {string} uid 用户id （必填）
     * @apiParam {string} token 验证登陆 （必填）
     * @apiParam {string} money 提现金额 （必填）
     * @apiParam {string} phone 联系方式 （必填）
     * @apiParam {string} num 提现账号 （必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": ""
     */
    public function cash_withdrawal(){
        $all = \request() -> all();
        // 根据获取的id
        if (empty($all['money']) || empty($all['phone']) ||empty($all['num'])) {
            return $this->rejson(201,'缺少必填项');
        }
        $data = DB::table('users')
            -> where('id',$all['uid'])
            -> select('money')
            -> first();
        $yue = $data -> money - $all['money'];
        if($yue < 0){
            return $this->rejson(201,'当前余额不足');
        }
        DB::beginTransaction();
        try{
            // 当前用户减少金额
            DB::table('users') -> where('id',$all['uid']) -> update(['money'=>$yue]);
            // 提现成功，添加提现明细
            $add = [
                'user_id' => $all['uid'],
                'price' => $all['money'],
                'describe' => "用户提现",
                'create_time' => date('Y-m-d H:i:s'),
                'type_id' => 3,
                'state' => 2,
                'phone' => $all['phone'],
                'card' => $all['num']
            ];
            $i = DB::table('user_logs') -> insert($add);

            if($i){
                DB::commit();
                return $this->rejson(200,'提现成功');
            }else{
                DB::rollBack();
                return $this->rejson(201,'未查询到该id');
            }
        }catch (\Exception $e){
            DB::rollBack();
            return $this->rejson(201,'提现失败');
        }

    }
    /**
     * @api {post} /api/wallet/recharge 余额充值
     * @apiName recharge
     * @apiGroup wallet
     * @apiParam {string} uid 用户id（必填）
     * @apiParam {string} token 验证登陆 （必填）
     * @apiParam {string} money 充值金额 （必填）
     * @apiParam {string} phone 联系方式 （必填）
     * @apiParam {string} method 充值的方式 0银联 1微信 2支付宝 （必填）
     * @apiParam {string} num 充值账号账号（必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": ""
     */
    public function recharge(){
        $all = \request() -> all();
        // 根据获取的id
        if (empty($all['money']) || empty($all['phone']) || empty($all['method']) ||empty($all['num'])) {
            return $this->rejson(201,'缺少必填项');
        }
        $data = DB::table('users')
            -> where('id',$all['uid'])
            -> select('money')
            -> first();
        DB::beginTransaction();
        try{
            $yue = $data -> money + $all['money'];
            // 当前用户减少金额
            DB::table('users') -> where('id',$all['uid']) -> update(['money'=>$yue]);
            // 提现成功，添加提现明细
            $add = [
                'user_id' => $all['uid'],
                'price' => $all['money'],
                'describe' => "用户充值",
                'create_time' => date('Y-m-d H:i:s'),
                'type_id' => 2,
                'state' => 1,
                'phone' => $all['phone'],
                'card' => $all['num'],
                'method' => $all['method'],
            ];
            $i = DB::table('user_logs') -> insert($add);

            if($i){
                DB::commit();
                return $this->rejson(200,'充值成功');
            }else{
                DB::rollBack();
                return $this->rejson(201,'未查询到该id');
            }
        }catch (\Exception $e){
            DB::rollBack();
            return $this->rejson(201,'充值失败');
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
    // 8fcc685decce987fbfdb713d7514928f
}