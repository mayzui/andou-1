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
                        "store_num": "商品数量",
                        "attr_value": [
                                    "4G+32G",
                                    "纸包装",
                                    "白"
                                ]
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
        $data = DB::table('goods')
            ->join('goods_sku',"goods.id","=","goods_sku.goods_id")
            ->select(['goods.id','goods.name','goods.desc','goods.img','goods_sku.price','goods.is_sale','goods_sku.store_num','goods_sku.attr_value'])
            ->where('goods.merchant_id',$all['id'])
            ->orderBy('goods.created_at','DESC')
            ->get();
        foreach($data as $k => $value){
            $data->attr_value =  $value->attr_value=json_decode($value->attr_value,1)[0]['value'];;
        }
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
        $res = DB::table('goods')->where('id',$all['id'])->delete();
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
        $res = DB::table('goods')->where('id',$all['id']);
        if($res['is_sale'] == 1){
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
        $res = DB::table('goods')->where('id',$all['id'])->get();
        if($res['is_sale'] != 0){
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
     * @api {post} /api/goods/ordersCancel  已退款
     * @apiName ordersCancel
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
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
        $data = DB::table('order_goods')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','order_goods.order_id','=','orders.order_sn')
            ->join('order_returns','order_returns.order_id','=','orders.id')
            ->where('order_goods.merchant_id',$all['id'])
            ->where('type',1)
            ->where('orders.status',0)
            ->where('order_returns.is_reg',1)
            ->select(['order_goods.id','orders.order_sn','goods.name','goods.desc','goods.img','order_goods.num','orders.pay_money','orders.status'])
            ->get();
            return $this->rejson('200','查询成功',$data);
    }

    /**
     * @api {post} /api/goods/ordersDetails  订单详情
     * @apiName ordersDetails
     * @apiGroup menage
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiParam {string} order_id 订单id
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
                        "order_money": "订单金额",
                        "num": "商品数量",
                        "content": "退款原因",
                        "pay_money": "支付金额",
                        }
                    ],
     *       "msg":"获取成功"
     *     }
     */

    public function ordersDetails()
    {
        $all = request()->all();
        $data = DB::table('order_goods')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','order_goods.order_id','=','orders.order_sn')
            ->join('order_returns','order_goods.order_id','=','order_returns.order_id')
            ->where('order_goods.merchant_id',$all['id'])
            ->where('order.id',$all['order_id'])
            ->where('order_returns.is_reg',1)
            ->where('orders.status',0)
            ->select(['orders.id','orders.order_sn','goods.name','goods.desc','goods.img','order_goods.num','orders.order_money','order_returns.content','orders.pay_money'])
            ->get();
            return $this->rejson('200','查询成功',$data);
    }

    /**
     * @api {post} /api/goods/audit  待审核订单
     * @apiName audit
     * @apiGroup menage
     * @apiParam {string} uid 商户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id  商家id
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
                        }
                    ],
     *       "msg":"获取成功"
     *     }
     */

    public function audit()
    {
        $all = request()->all();
        $data = DB::table('order_goods')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','order_goods.order_id','=','orders.id')
            ->join('order_returns','order_goods.order_id','=','order_returns.order_id')
            ->where('order_goods.merchant_id',$all['id'])
            ->where('order_returns.is_reg',0)
            ->where('orders.status',0)
            ->select(['orders.id','orders.order_sn','goods.name','goods.desc','goods.img','order_goods.num','orders.order_money','orders.pay_money'])
            ->get();
            return $this->rejson('200','查询成功',$data);
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
     * @apiParam {string} uid 商户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "name": "商铺名称",
                        "mobile": "联系方式",
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
        $data = DB::table('merchants')
            ->join('users','users.id','=','merchants.user_id')
            ->where('user_id',$all['uid'])
            ->select(['merchants.name','users.name as nickname','merchants.tel','users.mobile','merchants.banner_img','merchants.desc','merchants.address','merchants.return_address'])
            ->get();
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
        $arr = [
            'name'=>$all['nickname'],
            'mobile'=>$all['mobile'],
        ];
        $re = [
            'desc'=>$all['desc'],
            'address'=>$all['address'],
            'banner_img'=>$all['banner_img'],
            'name'=>$all['name'],
            'tel'=>$all['tel'],
            'return_address'=>$all['return_address'],
        ];
        DB::beginTransaction();//开启事物
        $data = DB::table('users')->where('id',$all['id'])->update($arr);
        $res = DB::table('merchants')->where('user_id',$all['id'])->update($re);
        if($data&&$res){
            DB::commit();//t提交
            return $this->rejson('200','修改成功');
        }else{
            DB::rollback();//回滚
            return $this->rejson('201','修改失败');
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
     * @apiParam {string} type 状态(非比传 10-未支付 20-已支付 40-已发货  50-交易成功（确认收货） 60-交易关闭（已评论）)
     * @apiParam {string} page 查询页码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                            "img": "商品图",
                            "name": "商品名字",
                            "goods_id": "商品id",
                            "merchant_id": "商户id",
                            "order_id": "订单号",
                            "status": "状态 10-未支付 20-已支付 40-已发货  50-交易成功（确认收货） 60-交易关闭（已评论）",
                            "mname": "商家名字",
                            "logo_img": "商家图",
                            "num": "数量",
                            "id": "订单id",
                            "express_id":"快递公司id",
                            "courier_num":"快递单号",
                            "shipping_free": "运费",
                            "price": "单价",
                            "pay_money": "总价",
                            "attr_value": [
                            "4G+32G",
                            "纸包装",
                            "白"
                            ]
                        }
                    ],
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
        if(empty($all['type'])){

        }else{
            $where[]=['o.status',$all['type']];
        }
        $where[]=['o.merchant_id',$all['id']];
        $data=DB::table('order_goods as o')
            ->join('goods as g','g.id','=','o.goods_id')
            ->join('merchants as m','m.id','=','o.merchant_id')
            ->join('goods_sku as s','s.id','=','o.goods_sku_id')
            ->where($where)
            ->select('g.img','g.name','o.goods_id','o.merchant_id','o.order_id','o.status','m.name as mname','m.logo_img','o.num','o.id','shipping_free','o.express_id','o.courier_num','s.price','pay_money','s.attr_value')
            ->get();
        foreach ($data as $k => $v) {
            $data[$k]->attr_value=json_decode($v->attr_value,1)[0]['value'];
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
     * @apiSuccessExample 参数返回:
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
            'pay_money'=>$all['pay_money'],
        ];
        $data = DB::table('orders')
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
            ->where('o.order_id',$all['roder_id'])
            ->select(['users.name','o.created_at','o.stars','o.content','o.image'])
            ->first();
        if($data)
        {
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'参数有误');
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
                    return ['code' => 200, 'msg' => '上传成功','data' => '/uploads/'.$path];
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