<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class RefundController extends Controller
{
//    public function __construct()
//    {
//        $all=request()->all();
//        if (empty($all['uid'])||empty($all['token'])) {
//            return $this->rejson(201,'登陆失效');
//        }
//        $check=$this->checktoten($all['uid'],$all['token']);
//        if ($check['code']==201) {
//            return $this->rejson($check['code'],$check['msg']);
//        }
//    }
    /**
     * @api {post} /api/refund/reason 退款原因
     * @apiName reason
     * @apiGroup refund
     * @apiParam {string} page 分页（非必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
        "name":"退货理由"
     *      }
     */
    public function reason(){
        $all = \request() -> all();
        $num = 5;
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }

        $data = DB::table('refund_reason')
            -> where('is_del',0)
            -> select('name')
            -> offset($pages)
            -> limit($num)
            -> get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/refund/apply 申请退款
     * @apiName apply
     * @apiGroup refund
     * @apiParam {string} uid 用户id （必填）
     * @apiParam {string} token 验证 （必填）
     * @apiParam {string} order_id 订单id （必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
        "name":"退货理由"
     *      }
     */
    public function apply(){
        $all = \request() -> all();
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==201) {
            return $this->rejson($check['code'],$check['msg']);
        }
        // 查询商品名称、商品图片、商品价格、商品数量
        $data = DB::table('order_goods')
            -> join('goods_sku','order_goods.goods_sku_id','=','goods_sku.id')
            -> join('goods','order_goods.goods_id','=','goods.id')
            -> select(['goods.name as goods_name','goods.img as goods_img','goods_sku.price as goods_price','order_goods.num'])
            -> where('order_goods.order_id',$all['order_id'])
            -> get();
        // 查询商品规格
        $attr_value = DB::table('order_goods')
            -> join('goods_sku','order_goods.goods_sku_id','=','goods_sku.id')
            -> select(['goods_sku.attr_value'])
            -> where('order_goods.order_id',$all['order_id'])
            -> get();
        foreach ($attr_value as $v){
            $datas[] = implode(json_decode($v -> attr_value)[0] -> value,',');
        }
        // 将商品规格添加到data中
        foreach ($datas as $k => $v){
            $data[$k] -> attr_value = $v;
        }
        return $this->rejson(200,'查询成功',$data);
    }
// W83tVnay3ZPCsMA
}