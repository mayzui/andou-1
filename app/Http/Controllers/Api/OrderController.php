<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class OrderController extends Controller
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
     * @api {post} /api/order/index 订单列表 
     * @apiName index
     * @apiGroup order
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} type 状态(非比传 10-未支付 20-已支付 40-已发货  50-交易成功（确认收货） 60-交易关闭（已评论）)
     * @apiParam {string} page 查询页码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                {
                    "order_money": "总金额",
                    "id": "总订单id",
                    "order_sn": "订单编号",
                    "status":"10-未支付 20-已支付 40-已发货  50-交易成功（确认收货） 60-交易关闭（已评论）"
                    "details": [
                        {
                            "img": "商品图",
                            "name": "商品名字",
                            "num": "购买数量",
                            "shipping_free": "商品运费",
                            "price": "价格",
                            "attr_value": [
                                "4G+32G",
                                "纸包装",
                                "白"
                            ]
                        }
                    ]
                }
            ],
     *       "msg":"添加成功"
     *     }
     */
    public function index(){
        $all=request()->all();
        $num=10;
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        if (empty($all['type'])) {
            
        }else{
            $where[]=['status',$all['type']];
        }
        
        $where[]=['user_id',$all['uid']];
        $where[]=['type',1];
        $where[]=['is_del',0];
        $data=DB::table('orders')
        ->where($where)
        ->select('order_money','status','id','order_sn')
        ->get();
        if (empty($data[0])) {
            return $this->rejson(201,'暂无订单');
        }
        foreach ($data as $key => $value) {
            $data[$key]->details=DB::table('order_goods as o')
            ->join('goods as g','g.id','=','o.goods_id')
            ->join('goods_sku as s','s.id','=','o.goods_sku_id')
            ->where('o.order_id',$value->order_sn)
            ->select('g.img','g.name','o.num','shipping_free','s.price','s.attr_value')
            ->get();
            foreach ($data[$key]->details as $k => $v) {
            $data[$key]->details[$k]->attr_value=json_decode($v->attr_value,1)[0]['value'];
            }
        }
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/order/add_order 立即购买 
     * @apiName add_order
     * @apiGroup order
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} goods_id 商品id
     * @apiParam {string} merchant_id 商户id
     * @apiParam {stringstring} goods_sku_id 规格id
     * @apiParam {string} num 购买数量
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "order_sn": "订单号"
                }
     *       "msg":"添加成功"
     *     }
     */
    public function addOrder(){
        $all=request()->all();
        if (empty($all['goods_id'])||empty($all['merchant_id'])||empty($all['goods_sku_id'])||empty($all['num'])) {
            return $this->rejson(201,'缺少参数');
        }
        $address=Db::table('user_address')->where(['user_id'=>$all['uid'],'is_defualt'=>1])->first();
        if(empty($address)){
            return $this->rejson(201,'请填写收货地址');
        }else{
            $alldata['address_id']=$address->id;
        }
        $data['goods_id']=$all['goods_id'];
        $data['merchant_id']=$all['merchant_id'];
        $data['goods_sku_id']=$all['goods_sku_id'];
        $data['num']=$all['num'];
        $data['pay_discount']=1;
        $alldata['user_id']=$data['user_id']=$all['uid'];
        $alldata['order_sn']=$data['order_id']=$this->suiji();
        $alldata['created_at'] = $alldata['updated_at']=$data['created_at'] = $data['updated_at'] =date('Y-m-d H:i:s',time());
        $alldata['shipping_free']=$data['shipping_free']=0;
        $datas=Db::table('goods_sku')->where('id',$all['goods_sku_id'])->where('store_num','>',0)->first();
        if (empty($data)) {
            return $this->rejson(201,'商品库存不足');
        }
        $alldata['order_money']=$data['pay_money']=$datas->price*$all['num']*$data['pay_discount']+$data['shipping_free'];
        $data['total']=$datas->price*$all['num']+$data['shipping_free'];
        $alldata['type']=1;
        $alldata['remark']=$all['remark']??'';
        $alldata['auto_receipt']=$all['auto_receipt']??0;
        DB::beginTransaction(); //开启事务
        $re=DB::table('order_goods')->insert($data);
        $res=DB::table('orders')->insert($alldata);
        if ($res&&$re) {
            DB::commit();
            return $this->rejson(200,'下单成功',array('order_sn'=>$data['order_id']));
        }else{
            DB::rollback();
            return $this->rejson(201,'下单失败');
        }
    }
    /**
     * @api {post} /api/order/add_order_car 购物车购买 
     * @apiName add_order_car
     * @apiGroup order
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  id 购物车id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "order_sn": "订单号"
                }
     *       "msg":"添加成功"
     *     }
     */
    public function addOrderCar(){
        $all=request()->all();
        // $all['id']=array(1,2);
        if (empty($all['id'])) {
            return $this->rejson(201,'缺少参数');
        }
        $address=Db::table('user_address')->where(['user_id'=>$all['uid'],'is_defualt'=>1])->first();
        if(empty($address)){
            return $this->rejson(201,'请填写收货地址');
        }else{
            $alldata['address_id']=$address->id;
            $alldata['order_money']=0;
            $alldata['type']=1;
            $alldata['remark']=$all['remark']??'';
            $alldata['order_sn']=$data['order_id']=$this->suiji();
            $alldata['user_id']=$data['user_id']=$all['uid'];
            $alldata['shipping_free']=$data['shipping_free']=0;
            $alldata['created_at'] = $alldata['updated_at']=$data['created_at'] = $data['updated_at'] =date('Y-m-d H:i:s',time());
            $alldata['auto_receipt']=$all['auto_receipt']??0;
        }
        DB::beginTransaction(); //开启事务
        foreach ($all['id'] as $v) {
            $car=DB::table('cart')//查询购物车
            ->where(['id'=>$v,'user_id'=>$all['uid']])
            ->first();
            if (empty($car)) {
                DB::rollback();
                return $this->rejson(201,'购物车id不存在');
            }
            $datas=Db::table('goods_sku')->where('id',$car->goods_sku_id)->where('store_num','>',0)->first();
            if (empty($datas)) {
                DB::rollback();
                return $this->rejson(201,'商品库存不足');
            }
            $data['goods_id']=$car->goods_id;
            $data['merchant_id']=$car->merchant_id;
            $data['goods_sku_id']=$car->goods_sku_id;
            $data['num']=$car->num;
            $data['pay_discount']=1;
            $alldata['order_money']+=$data['pay_money']=$datas->price*$data['num']*$data['pay_discount']+$data['shipping_free'];
            $data['total']=$datas->price*$data['num']+$data['shipping_free'];
            $re=DB::table('order_goods')->insert($data);
            if (!$re) {
                DB::rollback();
                return $this->rejson(201,'下单失败');
            }
        }
        $res=DB::table('orders')->insert($alldata);
        $red=Db::table('cart')->whereIn(['id'=>$all['id']],['user_id'=>$all['uid']])->delete();
        if ($res&&$red) {
            DB::commit();
            return $this->rejson(200,'下单成功',array('order_sn'=>$data['order_id']));
        }else{
            DB::rollback();
            return $this->rejson(201,'下单失败');
        }
    }
    /**
     * @api {post} /api/order/settlement 购买结算页 
     * @apiName settlement
     * @apiGroup order
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  order_sn 订单号
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "order_money": "订单总金额",
                "id": 订单id,
                "order_sn": "订单号",
                "address_id": "收货地址id",
                "userinfo": {
                    "name": "收货人",
                    "address": "收货详细地址",
                    "mobile": "收货人电话",
                    "province": "省",
                    "city": "市",
                    "area": "区"
                },
                "details": [
                    {
                        "img": "商品图片",
                        "name": "名字",
                        "num": "购买数量",
                        "shipping_free": "单商品邮费",
                        "price": "单价",
                        "attr_value": [//规格
                            "4G+32G",
                            "精包装",
                            "白"
                        ]
                    }
                ],
                "shipping_free": "总运费"
            }
     *       "msg":"添加成功"
     *     }
     */
    public function settlement(){
        $all=request()->all();
        if (empty($all['order_sn'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data=DB::table('orders')
        ->where(['order_sn'=>$all['order_sn'],'user_id'=>$all['uid'],'type'=>1,'is_del'=>0])
        ->select('order_money','id','order_sn','address_id')
        ->first();
        if (empty($data)) {
            return $this->rejson(201,'无效的订单号');
        }
        $address=Db::table('user_address')
        ->where('id',$data->address_id)
        ->first();
        $province=DB::table('districts')->where('id',$address->province_id)->first()->name ?? '';
        $city=DB::table('districts')->where('id',$address->city_id)->first()->name ?? '';
        $area=DB::table('districts')->where('id',$address->area_id)->first()->name ?? '';
        $data->userinfo=array('name'=>$address->name,'address'=>$address->address,'mobile'=>$address->mobile,'province'=>$province,'city'=>$city,'area'=>$area);
        $data->details=DB::table('order_goods as o')
        ->join('goods as g','g.id','=','o.goods_id')
        ->join('goods_sku as s','s.id','=','o.goods_sku_id')
        ->where('o.order_id',$all['order_sn'])
        ->select('g.img','g.name','o.num','shipping_free','s.price','s.attr_value')
        ->get();
        $data->shipping_free=0;
        foreach ($data->details as $key => $value) {
            $data->details[$key]->attr_value=json_decode($value->attr_value,1)[0]['value'];
            $data->shipping_free += $value->shipping_free;
        }
        return $this->rejson(200,'查询成功',$data);
    }
     /**
     * @api {post} /api/order/wx_pay 微信支付
     * @apiName wx_pay
     * @apiGroup order
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} sNo 订单号
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",     
     *       "msg":"查询成功"
     *     }
     */   
     public function wxPay(){
        require_once base_path()."/wxpay/lib/WxPay.Api.php";
        require_once base_path()."/wxpay/example/WxPay.NativePay.php";
        $all=request()->all();
        if (empty($all['sNo'])) {
            return $this->rejson(201,'参数错误');
        }
        $sNo=$all['sNo'];
        
        $orders = Db::table('orders')
        ->where('order_sn',$sNo)
        ->first();
        
        if (empty($orders)) {
            return $this->rejson(201,'订单不存在');
        }
        $pay_money = 100*$orders->order_money;
        
        $input = new \WxPayUnifiedOrder();
        
        $input->SetBody("安抖商城平台");
        $input->SetOut_trade_no($sNo);
        $input->SetTotal_fee($pay_money);
//        $input->SetTotal_fee(1);
        $input->SetNotify_url("https://api.dajuhui68.com/public/index.php/index/Alipays/wx_notify");
        $input->SetTrade_type("APP");
        $input->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);
//        $input->SetAttach($uid);
        $config = new \WxPayConfig();
        $order = \WxPayApi::unifiedOrder($config, $input);
        if($order['return_code']=="SUCCESS"){
            $time = time();
            $string = "appid=".$order['appid']."&noncestr=".$order['nonce_str']."&package="."Sign=WXPay"."&partnerid=".$order['mch_id']."&prepayid=".$order['prepay_id']."&timestamp=".$time."&key=qTYpBNvOTNyKEkGEI3wj80Wla6ZLIP7u";
            $string = md5($string);
            $order['sign'] = strtoupper($string);
            $order['timestamp'] = $time;
            return  json_encode(array('code' =>200,'data'=>$order,'msg'=>"获取支付信息成功！"),true);
        }else{
            return  json_encode(array('code' =>201,'data'=>'','msg'=>"获取支付信息失败！"),true);
        }
    }
}