<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManageController extends Controller
{
//    public function __construct()
//    {
//        $all = request()->all();
//        $token=request()->header('token')??'';
//        if ($token!='') {
//            $all['token']=$token;
//        }
//        if (empty($all['uid']) || empty($all['token'])) {
//            return $this->rejson(202, '登陆失效');
//        }
//        $check = $this->checktoten($all['uid'], $all['token']);
//        if ($check['code'] == 202) {
//            return $this->rejson($check['code'], $check['msg']);
//        }
//    }


    /**
     * @api {post} /api/goods/merchants 商家内容
     * @apiName merchants
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "id": "商家id",
                        "name": "商家名称",
                        "img": "商户分类图片",
                        "merchant_type_id": "商户分类id",
                        },
                    ],
     *       "msg":"查询成功"
     *     }
     */

    public function merchants()
    {
        $all = request()->all();
        $data = DB::table('users')
            ->join('merchants','users.id','=','merchants.user_id')
            ->join('merchant_type','merchants.merchant_type_id','=','merchant_type.id')
            ->where('users.id',$all['uid'])
            ->select(['merchants.id','merchant_type.type_name','merchant_type.img','merchants.merchant_type_id'])
            ->get();
        return $this->rejson('200','获取成功',$data);
    }

    /**
     * @api {post} /api/goods/manage 商品管理
     * @apiName manage
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiParam {string} page 页码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "id": "商品id",
                        "name": "商品名称",
                        "desc": "商品描述",
                        "img": "商品主图",
                        "price": "商品价格",
                        "is_sale": "是否上下架0是下架1是上架",
                        "num": "商品数量",
                        "weight": "商品重量"
                            },
                         ],
     *       "msg":"查询成功"
     *     }
     */

    public function index()
    {
        $all =  request()->all();
        if(empty($all['id'])){
            return $this->rejson('201','参数有误');
        }
        $num=10;
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        $data = DB::table('goods')
            ->join('goods_sku',"goods.id","=","goods_sku.goods_id")
            ->groupBy('goods_sku.goods_id')
            ->select(DB::raw("sum(goods_sku.store_num) as num"),'goods.id','goods.name','goods.desc','goods.img','goods.price','goods.is_sale','goods.weight')
            ->where('goods.merchant_id',$all['id'])
            -> limit($num)
            ->offset($pages)
            ->get();
        return $this->rejson(200,'查询成功',$data);
    }

    /**
     * @api {post} /api/goods/manageDel 删除商品
     * @apiName manageDel
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商品id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"删除成功"
     *     }
     */

    public function manageDel()
    {
        $all =  request()->all();
        if(empty($all['id'])){
            return $this->rejson('201','参数有误');
        }
        $data = ['is_del'=>1];
        $res = DB::table('goods')->where('id',$all['id'])->update($data);
        if($res){
            return $this->rejson('200','删除成功');
        }else{
            return $this->rejson('202','已删除');
        }
    }

    /**
     * @api {post} /api/goods/putaway 商品上架
     * @apiName putaway
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商品id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"上架成功"
     *     }
     */

    public function putaway()
    {
        $all = request()->all();
        $res = DB::table('goods')->where('id',$all['id'])->first();
        if($res->is_sale == 1){
            return $this->rejson('201','参数有误');
        }else{
            DB::table('goods')->where('id',$all['id'])->update(['is_sale'=>1]);
            return $this->rejson('200','商品上架完成');
        }
    }

    /**
     * @api {post} /api/goods/soldOut 商品下架
     * @apiName soldOut
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商品id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"下架成功"
     *     }
     */

    public function soldOut()
    {
        $all = request()->all();
        $res = DB::table('goods')->where('id',$all['id'])->first();
        if($res->is_sale == 0){
            return $this->rejson('201','参数有误');
        }else{
            DB::table('goods')->where('id',$all['id'])->update(['is_sale'=>0]);
            return $this->rejson('200','商品上架完成');
        }
    }

    /**
     * @api {post} /api/goods/centre  商家个人中心
     * @apiName centre
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiParam {string} merchant_type_id 商户分类id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                            "balance": [
                                    {
                                    "name": "商家名称",
                                    "logo_img": "商家logo图",
                                    "stars_all": "商家星级",
                                    }
                            ],
                        "payment": "待付款",
                        "deliver": "待发货",
                        "shipments": "已发货",
                        "affirm": "已完成",
                        "cancel": "退款订单",
                        "manage": "商品管理",
                        "balance": [
                                    {
                                        "money": "账户余额"
                                    }
                                ]
                        }
                    ],
     *       "msg":"获取成功"
     *     }
     */

    public function centre()
    {
        $all = request()->all();
        if(empty($all['id']) ||
            empty($all['merchant_type_id'])){
            return $this->rejson(201,'参数有误');
        }
        $data['info'] = DB::table('merchants')
            ->where('id',$all['id'])
            ->where('merchant_type_id',$all['merchant_type_id'])
            ->select(['name','logo_img','stars_all'])
            ->first();
        $data['today'] = DB::table('order_goods')->where('merchant_id',$all['id'])->where('created_at','>',date('Y-m-d 00:00:00', time()))->count();
        $data['all'] = DB::table('order_goods')->where('merchant_id',$all['id'])->count();
        $data['payment'] = DB::table('order_goods')
            ->where('merchant_id',$all['id'])
            ->where('status',10)->count();
        $data['deliver'] = DB::table('order_goods')
            ->where('merchant_id',$all['id'])
            ->where('status',20)->count();
        $data['shipments'] = DB::table('order_goods')
            ->where('merchant_id',$all['id'])
            ->where('status',40)->count();
        $data['affirm'] = DB::table('order_goods')
            ->where('merchant_id',$all['id'])
            ->where('status',50)->count();
        $data['cancel'] = DB::table('order_goods')
            ->where('merchant_id',$all['id'])
            ->where('status',0)->count();
        $data['manage'] = DB::table('goods')->where('merchant_id',$all['id'])->count();
        $data['balance'] = DB::table('merchants')
            ->join('users','merchants.user_id','=','users.id')
            ->where('merchants.id',$all['id'])
            ->select(['users.money'])
            ->get();
        return $this->rejson('200','获取成功',$data);
    }

    /**
     * @api {post} /api/goods/ordersCancel  订单退款
     * @apiName ordersCancel
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiParam {string} type 订单状态0未审核3已完成（非必传）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "id": "订单id",
                        "order_sn": "订单号",
                        "name": "商品名称",
                        "desc": "商品描述",
                        "img": "商品封面图",
                        "pay_money": "订单支付金额",
                        "num": "商品数量",
                        "status": "商品状态  0 已退款",
                        "is_reg": "是否审核 0审核中 1已同意退款 2用户发货中 3已完成",
                        }
                    ],
     *       "msg":"获取成功"
     *     }
     */

    public function ordersCancel()
    {
        $all = request()->all();
        if(empty($all['id'])){
            return $this->rejson('201','参数有误');
        }
        if(empty($all['type'])){

        }else{
            $where[] = ['order_returns.is_reg',$all['type']];
        }
        $where[] = ['orders.status',0];
        $data = DB::table('order_goods')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','order_goods.order_id','=','orders.order_sn')
            ->join('order_returns','order_returns.order_goods_id','=','orders.id')
            ->where('order_goods.merchant_id',$all['id'])
            ->where($where)
            ->select(['order_goods.id','orders.order_sn','goods.name','goods.desc','goods.img','order_goods.num','orders.pay_money','orders.status'])
            ->get();
            return $this->rejson('200','查询成功',$data);
    }

    /**
     * @api {post} /api/goods/ordersDetails 订单详情
     * @apiName ordersDetails
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  order_sn 订单编号
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                        "order_money": "订单总金额",
                        "id": "订单id",
                        "order_sn": "订单号",
                        "address_id": "收货地址id",
                        "integral":"使用积分",
                        "pay_money":"支付金额",
                        "pay_time":"付款时间",
                        "status":"订单状态",
                        "allnum":"购买商品总数",
                        "created_at":"创建时间",
                        "pay_way":"支付方式 1微信 2支付宝 3银联 4余额支付 5其他",
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
                                    "id": "订单编号id",
                                    "img": "商品图片",
                                    "name": "名字",
                                    "num": "购买数量",
                                    "shipping_free": "单商品邮费",
                                    "price": "单价",
                                    "courier_num": "快递单号",
                                    "express_id": "快递名称",
                                    "attr_value": [//规格
                                                    "4G+32G",
                                                    "精包装",
                                                    "白"
                                            ]
                                    }
                        ],
                        "shipping_free": "总运费"
                        "goodsPay": "减去运费后价格"
                        }
     *       "msg":"添加成功"
     *     }
     */

    public function ordersDetails()
    {
        $all = request()->all();
        if (empty($all['order_sn'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data=DB::table('orders')
            ->where(['order_sn'=>$all['order_sn'],'is_del'=>0])
            ->select('order_money','pay_way','pay_money','pay_time','id','integral','shipping_free','order_sn','status','address_id','created_at')
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
            ->select('o.id','g.img','g.name','o.num','shipping_free','s.price','s.attr_value','o.courier_num','o.express_id')
            ->get();
        foreach ($data->details as $v){
            $v->express_id = DB::table('express')->where('id', $v->express_id)->first();
        }
        $data->shipping_free=0;
        $data->allnum=0;
        foreach ($data->details as $key => $value) {
            $data->details[$key]->attr_value=json_decode($value->attr_value,1)[0]['value'];
            $data->allnum += $value->num;
            $data->shipping_free += $value->shipping_free;
        }
        $data->goodsPay = $data->order_money - $data->shipping_free;
        return $this->rejson(200,'查询成功',$data);
    }

    /**
     * @api {post} /api/goods/affirm  同意退货退款
     * @apiName affirm
     * @apiGroup menage
     * @apiParam {string} uid 商户id
     * @apiParam {string} id 订单id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"审核完成"
     *     }
     */

    public function affirm()
    {
        $all = request()->all();
        $arr = [ 'is_reg'=>1];
        $data = DB::table('order_returns')->where('order_id',$all['id'])->update($arr);
        if($data){
            return $this->rejson('200','审核完成');
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/store  店铺管理
     * @apiName store
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiParam {string} merchant_type_id 商家类型id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "name": "商铺名称",
                        "mobile": "联系方式",
                        "user_name": "联系人",
                        "desc": "商家公告",
                        "address": "地址",
                        "banner_img": "店铺形象图",
                        "nickname": "收货人",
                        "tel": "联系电话",
                        "return_address": "退货地址",
                        }
                    ],
     *       "msg":"查询成功"
     *     }
     */

    public function store()
    {
        $all = request()->all();
        if(empty($all['merchant_type_id'])){
            return $this->rejson('201','参数有误');
        }
        $data = DB::table('merchants')
            ->join('users','users.id','=','merchants.user_id')
            ->where('merchants.id',$all['id'])
            ->where('merchants.merchant_type_id',$all['merchant_type_id'])
            ->select(['merchants.name','users.name as nickname','merchants.tel','users.mobile','merchants.user_name','merchants.banner_img','merchants.desc','merchants.address','merchants.return_address'])
            ->first();
        if(!$data){
            return $this->rejson('202','未找到商家信息');
        }
            return $this->rejson('200','查询成功',$data);
    }

    /**
     * @api {post} /api/goods/saveStore  保存店铺管理
     * @apiName saveStore
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiParam {string} name 商铺名称
     * @apiParam {string} user_name 联系人
     * @apiParam {string} mobile 联系方式
     * @apiParam {string} desc 商家公告
     * @apiParam {string} address 地址
     * @apiParam {string} banner_img 店铺形象图
     * @apiParam {string} nickname 收货人
     * @apiParam {string} tel 联系电话
     * @apiParam {string} return_address 退货地址
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"修改成功"
     *     }
     */

    public function saveStore()
    {
        $all = request()->all();
        if(empty($all['nickname'])){
            return $this->rejson('202','参数不能为空');
        }
        if(empty($all['mobile'])){
            return $this->rejson('202','参数不能为空');
        }
        if(empty($all['desc'])){
            return $this->rejson('202','参数不能为空');
        }
        if(empty($all['address'])){
            return $this->rejson('202','参数不能为空');
        }
        if(empty($all['banner_img'])){
            return $this->rejson('202','参数不能为空');
        }
        if(empty($all['name'])){
            return $this->rejson('202','参数不能为空');
        }
        if(empty($all['user_name'])){
            return $this->rejson('202','参数不能为空');
        }
        if(empty($all['tel'])){
            return $this->rejson('202','参数不能为空');
        }
        if(empty($all['return_address'])){
            return $this->rejson('202','参数不能为空');
        }
        $arr = [
            'name'=>$all['nickname'],
            'mobile'=>$all['mobile'],
        ];
        $re = [
            'desc'=>$all['desc'],
            'address'=>$all['address'],
            'banner_img'=>$all['banner_img'],
            'name'=>$all['name'],
            'user_name'=>$all['user_name'],
            'tel'=>$all['tel'],
            'return_address'=>$all['return_address'],
        ];
        DB::beginTransaction();//开启事物
        $data = DB::table('users')->where('id',$all['uid'])->update($arr);
        $res = DB::table('merchants')->where('id',$all['id'])->update($re);
        if($data&&$res){
            DB::commit();//t提交
            return $this->rejson('200','修改成功');
        }else{
            DB::rollback();//回滚
            return $this->rejson('201','修改失败');
        }
    }

    /**
     * @api {post} /api/goods/classifys  分类列表
     * @apiName classifys
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加完成"
     *     }
     */

    public function classifys()
    {
        $all = request()->all();
        $res = DB::table('merchants_goods_type')->where('merchant_id',$all['id'])->get();
        if($res){
            return $this->rejson('200','添加完成');
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/classify  分类添加
     * @apiName classify
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiParam {string} name 分类名称
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加完成"
     *     }
     */

    public function classify()
    {
        $all = request()->all();
        $data = [
            'merchant_id'=>$all['id'],
            'name'=>$all['name'],
        ];
        $res = DB::table('merchants_goods_type')->insert($data);
        if($res){
            return $this->rejson('200','添加完成');
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/classify  上传新商品
     * @apiName upload
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiParam {string} name 商品名称
     * @apiParam {string} merchants_goods_type_id 商品分类id
     * @apiParam {string} price 商品价格
     * @apiParam {string} store_num 库存
     * @apiParam {string} dilivery 是否包邮
     * @apiParam {string} attr_value 商品属性
     * @apiParam {string} img 商品主图
     * @apiParam {string} album 商品详细图
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加完成"
     *     }
     */

    public function upload()
    {
        $all = request()->all();
        $data = [
            'merchants_goods_type_id'=>$all['merchants_goods_type_id'],
            'name'=>$all['name'],
            'price'=>$all['price'],
            'store_num'=>$all['store_num'],
            'dilivery'=>$all['dilivery'],
            'attr_value'=>$all['attr_value'],
            'img'=>$all['img'],
            'album'=>$all['album'],
            'created_at'=>date('Y-m-d H:i:s',time()),
        ];
    }

    /**
     * @api {post} /api/goods/lists 订单列表
     * @apiName lists
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiParam {string} type 状态(非必传 10-未支付 20-已支付 40-已发货  50-交易成功（确认收货） 60-交易关闭（已评论）)
     * @apiParam {string} page 查询页码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                            "id": "订单id",
                            "order_sn": "订单号",
                            "order_money": "总价",
                            "created_at": "时间",
                            "shipping_free": "总运费",
                            "status": "状态 10-未支付 20-已支付 40-已发货  50-交易成功（确认收货） 60-交易关闭（已评论）",
                            "details":  [
                                            {
                                            "img": "图片",
                                            "name": "名称",
                                            "num": "数量",
                                            "price": "单价",
                                            "attr_value": [
                                                        "4G+64G",
                                                        "纸包装",
                                                        "蓝",
                                                        "铁"
                                                        ]
                                            },
     *       "msg":"查询成功"
     *     }
     */
    public function lists(){
        $all=request()->all();
        $num=10;
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        if(!empty($all['type'])){
            $where[]=['order_goods.status',$all['type']];
        }
        $where[]=['order_goods.merchant_id',$all['id']];
        $data = DB::table('order_goods')
            ->join('orders','order_goods.order_id','=','orders.order_sn')
            ->where('order_goods.merchant_id',$all['id'])
            ->select('orders.order_sn','orders.order_money','orders.shipping_free','order_goods.status','orders.created_at')
            ->where($where)
            ->offset($pages)
            -> limit($num)
            ->get();
        foreach ($data as $v){
            $v->details = DB::table('order_goods as o')
                ->join('goods as g','g.id','=','o.goods_id')
                ->join('goods_sku as s','s.id','=','o.goods_sku_id')
                ->where('o.merchant_id',$all['id'])
                ->where('o.order_id',$v->order_sn)
                ->select('g.img','g.name','o.num','o.order_id','s.price','s.attr_value')
                ->get();
            foreach ($v->details as $key => $value){
                $v->details[$key]->attr_value=json_decode($value->attr_value,1)[0]['value'];
             }
        }
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/goods/awaitUpdate 修改价格
     * @apiName awaitUpdate
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 订单id
     * @apiParam {string} pay_money 修改后的价钱
     * @apiSuccessExample 参数返回
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"修改完成"
     *     }
     */

    public function awaitUpdate()
    {
        $all = request()->all();
        $res = [
            'total'=>$all['pay_money'],
        ];
        $data = DB::table('order_goods')
            ->where('id',$all['id'])
            ->update($res);
        if($data){
            return $this->rejson(200,'修改完成');
        }else{
            return $this->rejson(201,'参数有误');
        }
    }

    /**
     * @api {post} /api/goods/observer 评论
     * @apiName observer
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} order_id 订单id
     * @apiParam {string} type 类型 1商店 2商城 3饭店
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "id"   "评论id",
                        "name": "用户名",
                        "avator": "用户头像",
                        "created_at": "评论时间",
                        "goods_id": "评星",
                        "stars": "评论内容",
                        "image": "评论图片",
                        }
                    ],
     *       "msg":"修改完成"
     *     }
     */

    public function observer()
    {
        $all = request()->all();
        $data = DB::table('order_commnets as o')
            ->join('users','o.user_id','=','users.id')
            ->where('o.type',$all['type'])
            ->where('o.order_id',$all['order_id'])
            ->select(['users.name','users.avator','o.created_at','o.stars','o.content','o.image'])
            ->first();
        if($data)
        {
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'订单没有评论');
        }
    }

    /**
     * @api {post} /api/goods/delete 评论删除
     * @apiName delete
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 评论id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"删除成功"
     *     }
     */

    public function delete()
    {
        $all = request()->all();
        $res = ['is_del'=>1];
        $data = DB::table('order_commnets')->where('id',$all['id'])->update($res);
        if($data){
            return $this->rejson('200','删除成功');
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/water 商户流水
     * @apiName water
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商户id
     * @apiParam {string} status 1获得 2提现
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "id"   "流水id",
                        "price": "金额",
                        "msg": "备注",
                        "type": "1酒店 2商城 3饭店",
                        "status": "1获得 2提现",
                        "created": "时间",
                        }
                    ],
     *       "msg":"删除成功"
     *     }
     */

    public function water()
    {
        $all = requses()->all();
        $data = DB::table('merchant_log')
            ->where('merchant_id',$all['id'])
            ->where('status',$all['status'])
            ->select('price','msg','type','status','created')
            ->get();
        return $this->rejson('200','获取成功',$data);
    }

    /**
     * @api {post} /api/goods/quit 退出
     * @apiName quit
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"退出完成"
     *     }
     */

    public function quit()
    {
        $all = request()->all();
        $res = ['token'=>null];
        $data = DB::table('users')->where('id',$all['uid'])->update($res);
        if($data){
            return $this->rejson('200','退出完成');
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/deliver 发货
     * @apiName deliver
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 订单编号id
     * @apiParam {string} express_id 快递公司id
     * @apiParam {string} courier_num 快递单号
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"查询成功"
     *     }
     */

    public function deliver()
    {
        $all = request()->all();
        if(empty($all['express_id'])){
            return $this->rejson('201','请选择快递');
        }
        if(empty($all['courier_num'])){
            return $this->rejson('201','请填写快递单号');
        }
        $res = [
            'express_id'=>$all['express_id'],
            'courier_num'=>$all['courier_num']
        ];
        $data = DB::table('order_goods')->where('id',$all['id'])->update($res);
        if($data){
            return $this->rejson('200','发货完成');
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/uploads 图片上传
     * @apiName uploads
     * @apiGroup uploaded
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} img 图片
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "图片路径",
     *       "msg":"上传成功"
     *     }
     */

    public function uploads(Request $request)
    {
        if($request->method('post')){
            $files = $request->allFiles();
            if(is_array($files)){
                foreach($files as $key => $value){
                    $path = Storage::disk('uploads')->putFile('',$value);
                }
                if( $path ) {
                    return ['code' => 200, 'msg' => '上传成功','data' => '/uploads/'.date('Ymd').'/'.$path];
                }
                else {
                    return $this->rejson('202','传输失败');
                }
            }
        }else{
            return $this->rejson('201','非法请求');
        }
    }

}