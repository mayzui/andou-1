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
     * @api {post} /api/order/express 快递查询 
     * @apiName express
     * @apiGroup order
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} express_id 快递公司id
     * @apiParam {string} courier_num 快递单号
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加成功"
     *     }
     */
    public function express($post) {

        $customer = "E9C982534CECCAF4A0CF245E82488F27";

        $key = 'qibOlrGo1156';

        $url = 'http://poll.kuaidi100.com/poll/query.do';

        $express_id=$all['express_id'];
        $courier_num=$all['courier_num'];


        if (!empty($express_id) && !empty($courier_num)) {

                $r01 = Db::table('express')->where()->first('id',$express_id);
                $type = $r01->com; //快递公司代码

                $kuaidi_name = $r01->name;

                $post_data["customer"] = $customer;

                $post_data["param"] = '{"com":"' . $type . '","num":"' . $courier_num . '"}';

                $post_data["sign"] = md5($post_data["param"] . $key . $post_data["customer"]);

                $post_data["sign"] = strtoupper($post_data["sign"]);

                $o = "";

                foreach ($post_data as $k => $v) {

                    $o.= "$k=" . urlencode($v) . "&";  //默认UTF-8编码格式
                }

                $post_data = substr($o, 0, -1);

                //发起CURL请求

                $ch = curl_init();

                curl_setopt($ch, CURLOPT_POST, 1);

                curl_setopt($ch, CURLOPT_HEADER, 0);

                curl_setopt($ch, CURLOPT_URL, $url);

                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

                $result = curl_exec($ch);

                $da = str_replace("\"", '"', $result);

                $res_1 = json_decode($da, true);

                $data['wuliu_msg'] = $res_1;

                $data['name'] = $kuaidi_name;

                $data['courier_num'] = $courier_num;

                return array('code' => 200, 'data' => $data, 'msg' => '获取信息成功！');
        } else {

            return array('code' => 201, 'data' => '', 'msg' => '未查询到物流信息！');
        }
        
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
        $dilivery=Db::table('goods')->select('dilivery','weight')->where('id',$all['goods_id'])->first();
        if ($dilivery->dilivery > 0) {
            $alldata['shipping_free']=$data['shipping_free']=$this->freight($dilivery->weight*$all['num'],$all['num'],$dilivery->dilivery);
        }else{
            $alldata['shipping_free']=$data['shipping_free']=0;
        }
        
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
            $alldata['shipping_free']=0;
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
            $dilivery=Db::table('goods')->select('dilivery','weight')->where('id',$car->goods_id)->first();
            if ($dilivery->dilivery > 0) {
                $data['shipping_free']=$this->freight($dilivery->weight*$all['num'],$all['num'],$dilivery->dilivery);
                $alldata['shipping_free']+=$data['shipping_free'];
            }else{
                $data['shipping_free']=0;
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
        $red=Db::table('cart')->where('user_id',$all['uid'])->whereIn('id',$all['id'])->delete();
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
                "integral":"使用积分",
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
        ->select('order_money','id','shipping_free','order_sn','address_id')
        ->first();
        if (empty($data)) {
            return $this->rejson(201,'无效的订单号');
        }
        $integral=DB::table('config')->where('key','integral')->first()->value;
        $data->integral=floor(($data->order_money-$data->shipping_free)*$integral);
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
     * @api {post} /api/order/details 订单详情 
     * @apiName details
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
                "integral":"使用积分",
                "pay_money":"支付金额",
                "pay_time":"付款时间",
                "status":"订单状态",
                "allnum":"购买商品总数",
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
    public function details(){
        $all=request()->all();
        if (empty($all['order_sn'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data=DB::table('orders')
        ->where(['order_sn'=>$all['order_sn'],'user_id'=>$all['uid'],'type'=>1,'is_del'=>0])
        ->select('order_money','pay_way','pay_money','pay_time','id','integral','shipping_free','order_sn','status','address_id')
        ->first();
        if (empty($data)) {
            return $this->rejson(201,'无效的订单号');
        }
        
        $integral=DB::table('config')->where('key','integral')->first()->value;
        $data->integral=floor(($data->order_money-$data->shipping_free)*$integral);
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
        $data->allnum=0;
        foreach ($data->details as $key => $value) {
            $data->details[$key]->attr_value=json_decode($value->attr_value,1)[0]['value'];
            $data->allnum += $value->num;
            $data->shipping_free += $value->shipping_free;
        }
        return $this->rejson(200,'查询成功',$data);
    }
     /**
     * @api {post} /api/order/pay 订单支付
     * @apiName pay
     * @apiGroup order
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} sNo 订单号
     * @apiParam {string} pay_id 支付方式id
     * @apiParam {string} is_integral 是否使用积分 1使用 0不使用
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",     
     *       "msg":"查询成功"
     *     }
     */
     public function pay(){
        $all=request()->all();
        if ($all['pay_id']==1) {//微信支付
            $this->wxpay();
        }else if($all['pay_id']==2){//支付宝支付
            return $this->rejson(201,'暂未开通');
        }else if($all['pay_id']==3){//银联支付
            return $this->rejson(201,'暂未开通');
        }else if($all['pay_id']==4){//余额支付
            $this->balancePay();
        }else if($all['pay_id']==5){//其他支付
            return $this->rejson(201,'暂未开通');
        }else{
            return $this->rejson(201,'暂未开通');
        }
     }
     public function balancePay(){
        $all=request()->all();
        if (empty($all['sNo'])) {
            return $this->rejson(201,'参数错误');
        }
        $sNo=$all['sNo'];
        $users=Db::table('users')
        ->select('money','integral')
        ->where('id',$all['uid'])
        ->first();
        $orders = Db::table('orders')
        ->where(['order_sn'=>$sNo,'status'=>10,'user_id'=>$all['uid']])
        ->first();
        if (empty($orders)) {
            return $this->rejson(201,'订单不存在');
        }
        
        if($all['is_integral']==1){
            $integrals=DB::table('config')->where('key','integral')->first()->value;
            $integral=floor(($orders->order_money-$orders->shipping_free)*$integrals);
            if ($users->integral<$integral) {
                return $this->rejson(201,'积分不足');
            }else{
                $dataintegral['integral']=$integral;
                Db('orders')->where('order_sn',$sNo)->update($dataintegral);
            }
        }else{
            $integral=0;
        }
        if($users->money < $orders->order_money-$integral){
            return $this->rejson(201,'余额不足');
        }
        $data['user_id']=$all['uid'];
        $data['describe']='订单：'.$sNo.'消费';
        $data['create_time']=date('Y-m-d H:i:s',time());
        $data['type_id']=2;
        $data['price']=$orders->order_money-$integral;
        $data['state']=2;
        $data['is_del']=0;
        $status['status']=20;
        $status['pay_money']=$orders->order_money-$integral;
        $status['pay_way']=$all['pay_id'];
        $status['pay_time']=date('Y-m-d H:i:s',time());
        DB::beginTransaction(); //开启事务
        $re=DB::table('user_logs')->insert($data);
        $ress=DB::table('orders')->where('order_sn',$sNo)->update($status);
        $res=DB::table('users')->where('id',$all['uid'])->decrement('money',$orders->order_money);
        if ($integral>0) {
            $addintegral=$data;
            $addintegral['price']=$integral;
            $addintegral['type_id']=1;
            $rei=DB::table('user_logs')->insert($addintegral);
            $resi=DB::table('users')->where('id',$all['uid'])->decrement('integral',$integral);
        }
        if ($res&&$re&&$ress) {
            DB::commit();
            return $this->rejson(200,'支付成功');
        }else{
            DB::rollback();
            return $this->rejson(201,'支付失败');
        }

     }   
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
        if($all['is_integral']==1){
            $allintegral=DB::table('users')->where('id',$all['uid'])->first()->integral;
            $integrals=DB::table('config')->where('key','integral')->first()->value;
            $integral=floor(($orders->order_money-$orders->shipping_free)*$integrals);
            if ($allintegral<$integral) {
                return $this->rejson(201,'积分不足');
            }else{
                $dataintegral['integral']=$integral;
                Db('orders')->where('order_sn',$sNo)->update($dataintegral);
            }
        }else{
            $integral=0;
        }
        $pay_money = 100*($orders->order_money-$integral);
        
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
            return  $this->rejson(200,'获取支付信息成功！',$order);
        }else{
            return  $this->rejson(201,'获取支付信息失败！');
        }
    }

    /**
     * @api {post} /api/order/addcomment 添加商品评论
     * @apiName addcomment
     * @apiGroup order
     * @apiParam {string} uid 用户id（必填）
     * @apiParam {string} token 用户验证（必填）
     * @apiParam {string} goods_id 商品id（必填）
     * @apiParam {string} order_id 订单号（必填）
     * @apiParam {string} merchants_id 商户id（必填）
     * @apiParam {string} content 评价内容（非必填）
     * @apiParam {string} stars 评价星级（必填）
     * @apiParam {string} image 商品图片（非必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": "",
     *     }
     */
    public function addcomment(){
        $all=request()->all();
        if (!isset($all['uid']) ||
            !isset($all['token']) ||
            !isset($all['stars']) ||
            !isset($all['goods_id']) ||
            !isset($all['order_id']) ||
            !isset($all['merchants_id']) ){
            return $this->rejson(201,'缺少参数');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==201) {
            return $this->rejson($check['code'],$check['msg']);
        }
        if(!empty($all['image'])){
            $image = json_encode($all['image']);
        }else{
            $image = '';
        }
        if(!empty($all['content'])){
            $content = $all['content'];
        }else{
            $content = '此用户没有评论任何内容';
        }
        $data = [
            'user_id' => $all['uid'],
            'order_id' => $all['order_id'],
            'goods_id' => $all['goods_id'],
            'merchants_id' => $all['merchants_id'],
            'content' => $content,
            'stars' => $all['stars'],
            'image' => $image,
            'created_at' => date('Y-m-d H:i:s'),
            'type' => 2,
        ];
        $i = DB::table('order_commnets') -> insert($data);
        if($i){
            return $this->rejson(200,'添加成功');
        }else{
            return $this->rejson(201,'添加失败');
        }
    }
    // W83tVnay3ZPCsMA
}