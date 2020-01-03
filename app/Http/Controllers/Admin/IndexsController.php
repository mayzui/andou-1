<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

class IndexsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('admin.indexs.index');
    }

    public function main()
    {
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = \DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 查询总评论
            $merchants_comments = \DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('goods','order_commnets.goods_id','=','goods.id')     // 链接商品表
                -> where('type',2)
                -> where('merchants_id',$id)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','goods.name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);
            // 查询商品个数
            $merchants_goods = \DB::table('goods')
                -> join('merchants','goods.merchant_id','=','merchants.id')
                -> where('merchants.user_id',$id)
                -> where('is_del',0)
                -> select(['merchants.name as merchant_name','goods.id','goods.pv','goods.created_at','goods.updated_at','goods.goods_cate_id','goods.img','goods.desc','goods.is_hot','goods.is_recommend','goods.is_sale','goods.is_bargain','goods.dilivery'])
                -> orderBy('goods.id','desc')
                -> get();
            // 查询订单个数
            $merchants_order = \DB::table('orders')
                -> join('users','orders.user_id','=','users.id')
                -> where('orders.is_del',0)
                -> where('orders.user_id',$id)
                -> select(['orders.id','orders.order_sn','orders.pay_way','orders.pay_money','orders.order_money','orders.status','orders.shipping_free','orders.remark','orders.auto_receipt','orders.pay_time','users.name'])
                -> paginate(10);
            // 查询售后服务
            $merchants_returns =\DB::table('orders')
                -> join('order_returns','orders.order_sn','=','order_returns.order_id')
                -> join('users','orders.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> where('refund_reason.merchant_id',$id)
                -> select(['order_returns.id as id','order_returns.order_id as order_id','users.name as user_name',
                    'order_returns.is_reg','order_returns.status','order_returns.content','refund_reason.name as retun_name'])
                -> paginate(10);
        }else{
            // 反之则为。管理员
            // 查询，商城评论
            $merchants_comments = \DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('goods','order_commnets.goods_id','=','goods.id')     // 链接商品表
                -> where('type',2)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','goods.name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);
            // 查询商品个数
            $merchants_goods = \DB::table('goods')
                -> join('merchants','goods.merchant_id','=','merchants.id')
                -> where('is_del',0)
                -> select(['merchants.name as merchant_name','goods.id','goods.pv','goods.created_at','goods.updated_at','goods.goods_cate_id','goods.img','goods.desc','goods.is_hot','goods.is_recommend','goods.is_sale','goods.is_bargain','goods.dilivery'])
                -> orderBy('goods.id','desc')
                -> get();
            // 查询订单个数
            $merchants_order = \DB::table('orders')
                -> join('users','orders.user_id','=','users.id')
                -> where('orders.is_del',0)
                -> select(['orders.id','orders.order_sn','orders.pay_way','orders.pay_money','orders.order_money','orders.status','orders.shipping_free','orders.remark','orders.auto_receipt','orders.pay_time','users.name'])
                -> paginate(10);
            // 查询售后服务
            $merchants_returns =\DB::table('orders')
                -> join('order_returns','orders.order_sn','=','order_returns.order_id')
                -> join('users','orders.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> select(['order_returns.id as id','order_returns.order_id as order_id','users.name as user_name','order_returns.is_reg','order_returns.status','order_returns.content','refund_reason.name as retun_name'])
                -> paginate(10);
        }
        // 查询商城商家个数
        $merchants_num = \DB::table('merchants')
            -> where('merchant_type_id',2)
            -> get();

        // 查询酒店商家个数
        $hotle_num = \DB::table('merchants')
            -> where('merchant_type_id',3)
            -> get();
        // 查询订单个数
        $hotle_order = \DB::table('books')
            -> get();
        // 查询总评论
        $hotle_comments = \DB::table('order_commnets')
            -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
            -> join('hotel_room','order_commnets.goods_id','=','hotel_room.id')     // 链接商品表
            -> where('type',1)
            -> where('order_commnets.is_del',0)
            -> select(['order_commnets.id','users.name as username','hotel_room.house_name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
            -> paginate(10);
        // 查询饭店商家
        $goods_num = \DB::table('merchants')
            -> where('merchant_type_id',4)
            -> get();
        // 查询订单总id
        $goods_order = \DB::table('foods_user_ordering') -> get();
        // 查询总点评
        $goods_commnets = \DB::table('order_commnets')
            -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
            -> join('foods_information','order_commnets.goods_id','=','foods_information.id')     // 链接商品表
            -> where('type',3)
            -> where('order_commnets.is_del',0)
            -> select(['order_commnets.id','users.name as username','foods_information.name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
            -> paginate(10);
        $arr = [
            'merchants_num' => count($merchants_num),
            'merchants_goods' => count($merchants_goods),
            'merchants_order' => count($merchants_order),
            'merchants_returns' => count($merchants_returns),
            'merchants_comments' => count($merchants_comments),
            'hotle_num' => count($hotle_num),
            'hotle_order' => count($hotle_order),
            'hotle_comments' => count($hotle_comments),
            'goods_num' => count($goods_num),
            'goods_order' => count($goods_order),
            'goods_commnets' => count($goods_commnets),
        ];
        return view('admin.indexs.main',$arr);
    }
    public function round()
    {
        return view('admin.indexs.round');
    }
    public function census()
    {
        return view('admin.indexs.census');
    }
}
