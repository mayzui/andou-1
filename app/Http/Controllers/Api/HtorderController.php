<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class HtorderController extends Controller
{   
    public function __construct()
    {
        $all=request()->all();
        $token=request()->header('token')??'';
        if ($token!='') {
            $all['token']=$token;
        }
        if (empty($all['uid'])||empty($all['token'])) {
           return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
           return $this->rejson($check['code'],$check['msg']);
        }
    }
    /**
     * @api {post} /api/htorder/settlement 酒店结算页 
     * @apiName settlement
     * @apiGroup htorder
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  start 入住时间
     * @apiParam {array}  end 离店时间
     * @apiParam {array}  id 房间id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "start": "入住时间",
                "end": "离店时间",
                "days": "入住天数",
                "room": {
                    "house_name": "房间名字",
                    "img": "房间图片",
                    "price": "单价",
                    "merchant_id": "商户id",
                    "id": "房间id",
                    "name": "酒店名字"
                },
                "integral": "使用积分",
                "allprice": "总价格"
            },
     *       "msg":"添加成功"
     *     }
     */
    public function settlement(){
            $all=request()->all();
            if (empty($all['start'])||empty($all['end'])||empty($all['id'])) {
               return $this->rejson(201,'缺少参数');
            }
            $data['start']=$all['start'];
            $data['end']=$all['end'];
            $id=$all['id'];
            $startdate=strtotime($data['start']);
            $enddate=strtotime($data['end']);
            $data['days']=round(($enddate-$startdate)/3600/24);
            $data['room']=Db::table('hotel_room as h')
            ->join('merchants as m','h.merchant_id','=','m.id')
            ->select('h.house_name','h.img','h.price','h.merchant_id','h.id','m.name')
            ->where(['h.status'=>1,'h.id'=>$id])
            ->first();
            if(empty($data['room'])){
                return $this->rejson(201,'房间不存在');
            }
            $integral=Db::table('config')->where('key','integral')->first()->value;
            $data['integral']=floor($data['room']->price*$data['days']*$integral);
            $data['allprice']=$data['room']->price*$data['days']-$data['integral'];
            return $this->rejson('200','查询成功',$data);
    }
    /**
     * @api {post} /api/htorder/add_order 酒店预定
     * @apiName add_order
     * @apiGroup htorder
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  id 房间id
     * @apiParam {array}  merchant_id 商户id
     * @apiParam {array}  start_time 入住时间
     * @apiParam {array}  end_time 离开时间
     * @apiParam {array}  real_name 真实姓名
     * @apiParam {array}  mobile 手机号
     * @apiParam {array}  num 入住人数
     * @apiParam {array}  day_num 入住天数
     * @apiParam {array}  pay_way 支付方式
     * @apiParam {array}  is_integral 是否使用积分 使用传1
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"预定成功"
     *     }
     */
    public function addOrder(){
        $all=request()->all();
        if (empty($all['merchant_id'])
            ||empty($all['uid'])
            ||empty($all['id'])
            ||empty($all['start_time'])
            ||empty($all['end_time'])
            ||empty($all['real_name'])
            ||empty($all['mobile'])
            ||empty($all['num'])
            ||empty($all['day_num'])
            ||empty($all['pay_way'])
            ) {
            return $this->rejson('201','缺少参数');
        }
        $data['hotel_room_id']=$all['id'];
        $data['num']=$all['num'];
        $data['merchant_id']=$all['merchant_id'];
        $data['start_time']=$all['start_time'];
        $data['end_time']=$all['end_time'];
        $data['real_name']=$all['real_name'];
        $data['day_num']=$all['day_num'];
        $startdate=strtotime($data['start_time']);
        $enddate=strtotime($data['end_time']);
        $data['day_num']=round(($enddate-$startdate)/3600/24);
        $data['pay_way']=$all['pay_way'];
        $data['mobile']=$all['mobile'];
        $users=Db::table('users')
            ->select('money','integral')
            ->where('id',$all['uid'])
            ->first();
        $price=Db::table('hotel_room')
            ->select('price')
            ->where('id',$all['id'])
            ->first()
            ->price ?? 0;
        if ($price==0) {
            return $this->rejson('201','房间错误');
        }
        $data['money']=$price*$all['day_num'];
        $all['is_integral']=1;
        $data['status']=10;
        if($all['is_integral']==1){
            $integrals=DB::table('config')->where('key','integral')->first()->value;
            $integral=floor($price*$all['day_num']*$integrals);
            if ($users->integral<$integral) {
                return $this->rejson(201,'积分不足');
            }else{
                $alldata['integral']=$data['integral']=$integral;
            }
        }else{
            $alldata['integral']=$data['integral']=0;
        }
        if ($all['pay_way']==4) {
            if ($users->money < $data['money']-$data['integral']) {
               return $this->rejson(201,'余额不足');
            }
        }
        $alldata['status']=10;
        $alldata['order_money']=$data['money'];
        $alldata['type']=2;
        $alldata['remark']=$all['remark']??'';
        $alldata['order_sn']=$data['book_sn']=$this->suiji();
        $alldata['user_id']=$data['user_id']=$all['uid'];
        $alldata['shipping_free']=0;
        $alldata['created_at'] = $alldata['updated_at']=$data['created_at'] = $data['updated_at'] =date('Y-m-d H:i:s',time());
        $alldata['auto_receipt']=$all['auto_receipt']??0;
        $alldata['shipping_free']=0;
        DB::beginTransaction(); //开启事务
        $re=Db::table('books')->insert($data);
        $res=Db::table('orders')->insert($alldata);
            if ($re&&$res) {
                DB::commit();
                $all=request()->all();
            if ($all['pay_way']==1) {//微信支付
                $this->wxpay($data['book_sn']);
            }else if($all['pay_way']==2){//支付宝支付
                return $this->rejson(201,'暂未开通');
            }else if($all['pay_way']==3){//银联支付
                return $this->rejson(201,'暂未开通');
            }else if($all['pay_way']==4){//余额支付
                $this->balancePay($data['book_sn']);
            }else if($all['pay_way']==5){//其他支付
                return $this->rejson(201,'暂未开通');
            }else{
                return $this->rejson(201,'暂未开通');
            }
            //return $this->rejson(200,'下单成功',array('order_sn'=>$data['order_id']));
        }else{
            DB::rollback();
            return $this->rejson(201,'下单失败');
        }
    }

    public function balancePay($sNo){
        $all=request()->all();
        $orders = Db::table('orders')
        ->where(['order_sn'=>$sNo,'status'=>10])
        ->first();
        $data['user_id']=$all['uid'];
        $data['describe']='订单：'.$sNo.'消费';
        $data['create_time']=date('Y-m-d H:i:s',time());
        $data['type_id']=2;
        $data['price']=$orders->order_money - $orders->integral;
        $data['state']=2;
        $data['is_del']=0;
        $status['status']=20;
        $status['pay_money']=$orders->order_money-$orders->integral;
        $status['pay_time']=date('Y-m-d H:i:s',time());

        DB::beginTransaction(); //开启事务
        $re=DB::table('user_logs')->insert($data);
        $ress=DB::table('orders')->where('order_sn',$sNo)->update($status);
        $ress=DB::table('books')->where('book_sn',$sNo)->update($status);
        $res=DB::table('users')->where('id',$all['uid'])->decrement('money',$data['price']);
        if ($orders->integral>0) {
            $addintegral=$data;
            $addintegral['price']=$orders->integral;
            $addintegral['type_id']=1;
            $rei=DB::table('user_logs')->insert($addintegral);
            $resi=DB::table('users')->where('id',$all['uid'])->decrement('integral',$orders->integral);
        }
        if ($res&&$re&&$ress) {
            DB::commit();
            return $this->rejson(200,'预定成功');
        }else{
            DB::rollback();
            return $this->rejson(201,'支付失败');
        }

     }

     public function wxPay($sNo){
        require_once base_path()."/wxpay/lib/WxPay.Api.php";
        require_once base_path()."/wxpay/example/WxPay.NativePay.php";
        $all=request()->all();
        //$sNo=$all['sNo'];
        
        $orders = Db::table('orders')
        ->where('order_sn',$sNo)
        ->first();
        
        $pay_money = 100*($orders->order_money-$orders->integral);
        
        $input = new \WxPayUnifiedOrder();
        
        $input->SetBody("安抖商城平台");
        $input->SetOut_trade_no($sNo);
        // $input->SetTotal_fee($pay_money);
        $input->SetTotal_fee(1);
        $input->SetNotify_url("http://andou.zhuosongkj.com/api/common/wxnotifyhotel");
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
     * @api {post} /api/htorder/orderdatails 酒店预定详情
     * @apiName orderdatails
     * @apiGroup htorder
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  book_sn 订单编号
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *              "book_sn": "订单编号",
                    "created_at": "下单时间",
                    "pay_way": "支付方式",
                    "id": "房间id",
                    "merchant_id": "商户id",
                    "merchants_name": "商户名字",
                    "status": "订单状态",（0-取消订单 10-未支付订单 20-已支付(待入住) 30 已入住 40-已完成(离店) 50-已评价）
                    "img": "房间图片",
                    "house_name": "房间名字",
                    "price": "单价",
                    "integral": "使用积分",
                    "money": "订单总金额",
                    "start_time": "入住时间",
                    "end_time": "离开时间",
                    "day_num": "入住天数",
                    "real_name": "入住人",
                    "mobile": "联系电话",
                    "pay_money":"支付金额"
     *       },
     *       "msg":"预定成功"
     *     }
     */
    public function orderdatails(){
        $all=request()->all();
        if (empty($all['book_sn'])) {
            return $this->rejson(201,'缺少参数');
        }
        // 查询酒店订单
        $data = DB::table('books')
            -> join('merchants','books.merchant_id','=','merchants.id')
            -> join('hotel_room','books.hotel_room_id','=','hotel_room.id')
            -> where('books.book_sn',$all['book_sn'])
            -> select(['books.book_sn','books.created_at','books.pay_way','hotel_room.id','hotel_room.merchant_id','merchants.name as merchants_name','books.status','hotel_room.img','hotel_room.house_name','hotel_room.price','books.integral','books.money','books.start_time','books.end_time','books.day_num','books.real_name','books.mobile'])
            ->first();
        if (!empty($data)) {
            $data->pay_money=$data->money-$data->integral;
            $data->pay_way=Db::table('pay_ways')->where('id',$data->pay_way)->first()->pay_way??'';
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'订单编号错误');
        }
    }
    /**
     * @api {post} /api/htorder/refund_reason 酒店退款原因
     * @apiName refund_reason
     * @apiGroup htorder
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  merchants_id 商户id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [{
     *           "id":"退款原因id",
     *           "name":"退款原因"
     *       }]       
     *       ,
     *       "msg":"预定成功"
     *     }
     */
    public function refundReason(){
        $data=Db::table('refund_reason')
        ->select('id','name')
        ->where(['type'=>1,'is_del'=>0,'merchants_id'=>$all['merchants_id']])
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/htorder/refund 酒店退款
     * @apiName refund
     * @apiGroup htorder
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  book_sn 订单编号
     * @apiParam {array}  refund_id 退款原因id
     * @apiParam {array}  refund_msg 退款备注
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"申请成功"
     *     }
     */
    public function refund(){
        $all=request()->all();
        if (empty($all['refund_msg'])||empty($all['refund_id'])||empty($all['book_sn'])) {
           return $this->rejson(201,'缺少参数');
        }
        $re=Db::table('books')->where(['book_sn'=>$all['book_sn'],'status'=>20])->select('id')->first();
        if (empty($re)) {
            return $this->rejson(201,'订单编号错误');
        }
        $data['status']=50;
        $data['refund_msg']=$all['refund_msg'];
        $data['book_sn']=$all['book_sn'];
        DB::beginTransaction(); //开启事务
        $res=Db::table('books')->where('book_sn',$all['book_sn'])->update($data);
        $ress=Db::table('orders')->where('order_sn',$all['order_sn'])->update(array('status'=>50));
        if ($res&&$ress) {
            DB::commit();
            return $this->rejson(200,'申请成功');
        }else{
            DB::rollback();
            return $this->rejson(201,'申请失败');
        }
    }    
}