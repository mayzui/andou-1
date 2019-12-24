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
     * @apiGroup goods
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
                        "img": "商品封面图",
                        "price": "商品价格",
                        "attr_id": "商品属性",
                        "store_num": "商品库存",
                        "price": "商品状态",
     *                  'is_sale'  "商品上下架 0下架 1上架",
                        }
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
        static $arr = [];
        foreach($data as $k => $value){
            $arr[$k] =  $value->attr_value;

        }
        return $arr;
//        return $this->rejson(200,'查询成功',$data);
    }

    /**
     * @api {post} /api/goods/manageDel 删除商品
     * @apiName manageDel
     * @apiGroup goods
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
     * @apiGroup goods
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
            return $this->rejson('201','商品已上架');
        }else{
            DB::table('goods')->where('id',$all['id'])->update(['is_sale'=>1]);
            return $this->rejson('200','商品上架完成');
        }
    }

    /**
     * @api {post} /api/goods/soldOut 商品下架
     * @apiName soldOut
     * @apiGroup goods
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
            return $this->rejson('201','商品已下架');
        }else{
            DB::table('goods')->where('id',$all['id'])->update(['is_sale'=>0]);
            return $this->rejson('200','商品上架完成');
        }
    }

    /**
     * @api {post} /api/goods/soldOut 商品修改
     * @apiName soldOut
     * @apiGroup goods
     * @apiParam {string} id 商品id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"获取成功"
     *     }
     */

    public function manageUpd()
    {
        $all = request()->all();
        $res = DB::table('goods')->where('id',$all['id'])->get();
        if($res){
            return $this->rejson('200','查询成功',$res);
        }else{
            return $this->rejson('201','参数有误');
        }
    }

    /**
     * @api {post} /api/goods/orders  订单已取消
     * @apiName orders
     * @apiGroup goods
     * @apiParam {string} id 商户id
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
                        "status": "商品状态",
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
            ->where('order_goods.merchant_id',$all['id'])
            ->where('orders.status',0)
            ->select(['order_goods.id','orders.order_sn','goods.name','goods.img','order_goods.num','orders.pay_money','orders.status'])
            ->get();
        if($data){
            return $this->rejson('200','查询成功',$data);
        }else{
            return $this->rejson('201','参数有误');
        }
    }

}