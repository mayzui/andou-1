<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class WalletController extends Controller
{
//    public function __construct()
//    {
//        $all=request()->all();
//        $token=request()->header('token')??'';
//        if ($token!='') {
//            $all['token']=$token;
//        }
//        if (empty($all['uid'])||empty($all['token'])) {
//            return $this->rejson(202,'登陆失效');
//        }
//        $check=$this->checktoten($all['uid'],$all['token']);
//        if ($check['code']==202) {
//            return $this->rejson($check['code'],$check['msg']);
//        }
//    }
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
     * @api {post} /api/wallet/rechar 余额充值明细
     * @apiName rechar
     * @apiGroup wallet
     * @apiParam {string} uid 用户id（必填）
     * @apiParam {string} token 验证登陆 （必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
                    'money' "总金额",
                    'mobile' "联系方式"
     *          }
     */

    public function rechar()
    {
        $all =\request()->all();
        $data = DB::table('users')
            ->where('id',$all['uid'])
            ->select('money','mobile')
            ->first();
        if($data){
            return $this->rejson('200','查询成功',$data);
        }else{
            return $this->rejson('201','未找到用户');
        }
    }
    /**
     * @api {post} /api/common/pay_ways 支付方式
     * @apiName pay_ways
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                        {
                        "id": "支付方式id",
                        "pay_way": "支付方式名字",
                        "logo": "图标"
                        }
                        ],
     *       "msg":"查询成功"
     *     }
     */
    public function payWays(){
        $data=Db::table('pay_ways')->select('id','pay_way','logo')
            ->where('id',1)
            ->where('id',2)
            ->where('id',3)
            ->where('status',1)->get();
        return $this->rejson(200,'查询成功',json_decode($data,JSON_UNESCAPED_UNICODE));
    }

    /**
     * @api {post} /api/wallet/recharge 余额充值
     * @apiName recharge
     * @apiGroup wallet
     * @apiParam {string} uid 用户id（必填）
     * @apiParam {string} token 验证登陆 （必填）
     * @apiParam {string} money 充值金额 （必填）
     * @apiParam {string} mobile 联系方式 （必填）
     * @apiParam {string} method 充值的方式 0银联 1微信 2支付宝 （必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": ""
     */
    public function recharge(){
        $all = \request() -> all();
        // 根据获取的id
        if (empty($all['money']) || empty($all['phone']) || empty($all['method'])) {
            return $this->rejson(201,'缺少必填项');
        }
        DB::beginTransaction();
        try{
            $add = [
                'order_sn'=>$this->suiji(),
                'user_id' => $all['uid'],
                'price' => $all['money'],
                'create_time' => date('Y-m-d H:i:s'),
                'phone' => $all['mobile'],
                'method' => $all['method'],
            ];
            $sNo = $add['order_sn'];
            $i = DB::table('recharge') -> insert($add);
            if($i){
                if ($all['money']==1) {//微信支付
                    $this->wxPay($sNo);
                }else if($all['money']==2){//支付宝支付
                    return $this->rejson(201,'暂未开通');
                }else if($all['money']==0){//银联支付
                    return $this->rejson(201,'暂未开通');
                }else{
                    return $this->rejson(201,'暂未开通');
                }
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

    public function wxPay($sNo){
        require_once base_path()."/wxpay/lib/WxPay.Api.php";
        require_once base_path()."/wxpay/example/WxPay.NativePay.php";

        if (empty($sNo)) {
            return $this->rejson(201,'参数错误');
        }

        $orders = Db::table('recharge')
            ->where('order_sn',$sNo)
            ->first();
        if (empty($orders)) {
            return $this->rejson(201,'订单不存在');
        }

        $pay_money = 100*($orders->price);

        $input = new \WxPayUnifiedOrder();

        $input->SetBody("安抖商城平台");
        $input->SetOut_trade_no($sNo);
         $input->SetTotal_fee($pay_money);
//        $input->SetTotal_fee(1);
        $input->SetNotify_url("http://andou.zhuosongkj.com/api/common/wxRecharge");
        $input->SetTrade_type("APP");
        $input->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);
//        $input->SetAttach($uid);
        $config = new \WxPayConfig();
        $order = \WxPayApi::unifiedOrder($config, $input);
        // var_dump($order);exit();
        if($order['return_code']=="SUCCESS"){
            $time = time();
            $string = "appid=".$order['appid']."&noncestr=".$order['nonce_str']."&package="."Sign=WXPay"."&partnerid=".$order['mch_id']."&prepayid=".$order['prepay_id']."&timestamp=".$time."&key=AndoubendishenghuoXIdoukeji66888";
            $string = md5($string);
            $order['sign'] = strtoupper($string);
            $order['timestamp'] = $time;
            return  $this->rejson(200,'获取支付信息成功！',$order);
        }else{
            return  $this->rejson(201,'获取支付信息失败！');
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
        "status":'是否是会员 0普通用户 1超级会员',
        "money":'用户总金额',
        "integral":'用户积分',
        "collect":'商品收藏数',
        "focus":'关注店铺数',
        "record":'浏览记录数',
     }
     */
    public function personal(){
        $all = \request() -> all();
        // 根据获取的id
        if(empty($all['uid'])){
            return $this->rejson(201,'请输入用户id');
        }
        $data = DB::table('users')
            -> where('id',$all['uid'])
            -> select(['id','name','avator','money','integral'])
            -> first();
        $grade = DB::table('vip')
            -> where('user_id',$all['uid'])
            -> where('is_del',0)
            -> select('grade')
            -> first();
        $data->collect = DB::table('collection')->where('user_id',$all['uid'])->where('type',1)->count();
        $data->focus = DB::table('collection')->where('user_id',$all['uid'])->where('type',3)->count();
        $data->record = DB::table('see_log')->where('user_id',$all['uid'])->where('type',2)->count();
        if(empty($grade)){
            $data->status = 0;
            $data -> grade = 0;
        }else{
            $data->status = 1;
            $data -> grade = $grade -> grade;
        }
        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }

    // 8fcc685decce987fbfdb713d7514928f
}