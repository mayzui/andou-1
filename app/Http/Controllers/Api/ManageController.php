<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class ManageController extends Controller
{
//    public function __construct()
//    {
//        $all = request()->all();
//        if (empty($all['uid']) || empty($all['token'])) {
//            return $this->rejson(201, '登陆失效');
//        }
//        $check = $this->checktoten($all['uid'], $all['token']);
//        if ($check['code'] == 201) {
//            return $this->rejson($check['code'], $check['msg']);
//        }
//    }

    /**
     * @api {post} /api/goods/manage 商品管理
     * @apiName manage
     * @apiGroup menage
     * @apiParam {string} uid 商户id
     * @apiParam {string} token 验证登陆
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
        $data = DB::table('goods')
            ->join('goods_sku',"goods.id","=","goods_sku.goods_id")
            ->select(['goods.id','goods.name','goods.desc','goods.img','goods_sku.price','goods.is_sale','goods_sku.store_num','goods_sku.attr_value'])
            ->where('goods.merchant_id',$all['uid'])
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
     * @apiParam {string} uid 商户id
     * @apiParam {string} id 商品id
     * @apiParam {string} token 验证登陆
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
        $res = DB::table('goods')->where('id',$all['id'])->delete();
        if($res){
            return $this->rejson('200','删除成功');
        }else{
            return $this->rejson('201','删除失败');
        }
    }

    /**
     * @api {post} /api/goods/putaway 商品上架
     * @apiName putaway
     * @apiGroup menage
     * @apiParam {string} uid 商户id
     * @apiParam {string} id 商品id
     * @apiParam {string} token 验证登陆
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
     * @apiParam {string} uid 商户id
     * @apiParam {string} id 商品id
     * @apiParam {string} token 验证登陆
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
        if($res['is_sale'] == 0){
            return $this->rejson('201','参数有误');
        }else{
            DB::table('goods')->where('id',$all['id'])->update(['is_sale'=>0]);
            return $this->rejson('200','商品上架完成');
        }
    }

    /**
     * @api {post} /api/goods/ordersCancel  已取消订单
     * @apiName ordersCancel
     * @apiGroup menage
     * @apiParam {string} uid 商户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "id": "订单id",
                        "order_sn": "订单号",
                        "name": "商品名称",
                        "img": "商品封面图",
                        "pay_money": "订单支付金额",
                        "num": "商品数量",
                        "status": "商品状态 0已取消",
                        }
                    ],
     *       "msg":"获取成功"
     *     }
     */

    public function ordersCancel()
    {
        $all = request()->all();
        $data = DB::table('order_goods')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','order_goods.order_id','=','orders.id')
            ->join('order_returns','order_returns.order_id','=','orders.id')
            ->where('order_goods.merchant_id',$all['uid'])
            ->where('orders.status',0)
            ->where('order_returns.is_reg',1)
            ->select(['order_goods.id','orders.order_sn','goods.name','goods.img','order_goods.num','orders.pay_money','orders.status'])
            ->get();
        if($data){
            return $this->rejson('200','查询成功',$data);
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/ordersDetails  订单详情
     * @apiName ordersDetails
     * @apiGroup menage
     * @apiParam {string} id 订单id
     * @apiParam {string} uid 商户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "goods": [
                                     {
                                        "id": "订单id",
                                        "order_sn": "订单号",
                                        "created_at"  "下单时间"
                                        "name": "商品名称",
                                        "img": "商品封面图",
                                        "order_money": "订单金额",
                                        "num": "商品数量",
                                        "pay_discount": "折扣金额",
                                        "pay_money": "支付金额",
                                     }
                          ]

                        "returns": [
                                        {
                                        "created_at": "订单创建时间",
                                        "consignee_realname": "收货人",
                                        "consignee_telphone": "联系电话",
                                        "returns_no": "退款编号",
                                        "created_time": "退款时间",
                                        "express_company": "退款原因",
                                        "returns_amount": "退款金额",
                                    }
                                ]
                            }
                        ],
     *       "msg":"获取成功"
     *     }
     */

    public function ordersDetails()
    {
        $all = request()->all();
        $data['goods'] = DB::table('order_goods')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','order_goods.order_id','=','orders.id')
            ->where('order_goods.merchant_id',$all['id'])
            ->where('orders.status',0)
            ->select(['orders.order_sn','orders.created_at','goods.name','goods.img','order_goods.num','order_goods.pay_discount','orders.total','orders.pay_money','orders.pay_way'.'orders.status'])
            ->get();
        $data['returns'] = DB::table('order_returns as o')
            ->join('orders','o.order_id','=','orders.id')
            ->where('o.merchant_id',$all['id'])
            ->where('orders.status',0)
            ->select(['orders.created_at','o.consignee_realname','o.consignee_telphone','o.returns_no','o.created_time','o.express_company','o.returns_amount'])
            ->get();
        if($data){
            return $this->rejson('200','查询成功',$data);
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/audit  未审核订单
     * @apiName audit
     * @apiGroup menage
     * @apiParam {string} uid 商户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "id": "订单id",
                        "order_sn": "订单号",
                        "name": "商品名称",
                        "img": "商品封面图",
                        "pay_money": "订单支付金额",
                        "num": "商品数量",
                        "is_reg": "是否审核 0 待审核 1已审核",
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
            ->join('order_returns','order_returns.order_id','=','orders.id')
            ->where('order_goods.merchant_id',$all['uid'])
            ->where('orders.status',0)
            ->where('order_returns.is_reg',0)
            ->select(['order_goods.id','orders.order_sn','goods.name','goods.img','order_goods.num','orders.pay_money','order_returns.is_reg'])
            ->get();
        if($data){
            return $this->rejson('200','查询成功',$data);
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/affirm  确认取消
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
     * @api {post} /api/goods/centre  商家个人中心
     * @apiName centre
     * @apiGroup menage
     * @apiParam {string} uid 商户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "payment": "待付款",
                        "deliver": "代发货",
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
        $data['payment'] = DB::table('order_goods')
            ->join('merchants','merchants.id','=','order_goods.merchant_id')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','orders.id','=','order_goods.order_id')
            ->where('order_goods.merchant_id',$all['uid'])
            ->where('orders.status',10)->count();
        $data['deliver'] = DB::table('order_goods')
            ->join('merchants','merchants.id','=','order_goods.merchant_id')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','orders.id','=','order_goods.order_id')
            ->where('order_goods.merchant_id',$all['uid'])
            ->where('orders.status',20)->count();
        $data['shipments'] = DB::table('order_goods')
            ->join('merchants','merchants.id','=','order_goods.merchant_id')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','orders.id','=','order_goods.order_id')
            ->where('order_goods.merchant_id',$all['uid'])
            ->where('orders.status',40)->count();
        $data['affirm'] = DB::table('order_goods')
            ->join('merchants','merchants.id','=','order_goods.merchant_id')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','orders.id','=','order_goods.order_id')
            ->where('order_goods.merchant_id',$all['uid'])
            ->where('orders.status',50)->count();
        $data['cancel'] = DB::table('order_goods')
            ->join('merchants','merchants.id','=','order_goods.merchant_id')
            ->join('goods','goods.id','=','order_goods.goods_id')
            ->join('orders','orders.id','=','order_goods.order_id')
            ->where('order_goods.merchant_id',$all['uid'])
            ->where('orders.status',0)->count();
        $data['manage'] = DB::table('goods')->where('merchant_id',$all['uid'])->count();
        $data['balance'] = DB::table('merchants')
            ->join('users','merchants.user_id','=','users.id')
            ->where('merchants.id',$all['uid'])
            ->select(['users.money'])
            ->get();
//        $data = array_map('get_object_vars', $data);
        return $this->rejson('200','获取成功',$data);
    }

}