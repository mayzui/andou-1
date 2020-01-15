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
    /*
     *      语音提醒
     * */
    public function voice_play(){
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $merchant_data = \DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> get();
        if(count($merchant_data) == 0){
            return 0;
        }else{
            if(!empty($merchant_data)){
                foreach ($merchant_data as $v){
                    // 查询数据库中，是否存在未播放的语音
                    $voice_paly_status[] = \DB::table('order_goods') -> where('merchant_id',$v -> id) -> where('voice_paly',0) -> get();
                    // 将此商户的所有语音修改为以播放状态
                    $data = [
                        'voice_paly' => 1
                    ];
                    \DB::table('order_goods') -> where('merchant_id',$v -> id) -> update($data);
                }
                return json_encode($voice_paly_status);
            }else{
                return "no";
            }
        }

    }

    /*
     *      修改密码
     * */
    public function updataPwd(){
        $all = request() -> all();
        $admin = \Auth::guard('admin')->user();
        // 判断输入的旧密码是否正确
        $isCheck = \Hash::check($all['old_pwd'],$admin -> password);
        if($isCheck){
            // 判断新密码与确认密码是否相同
            if($all['new_pwd'] == $all['con_pwd']){
                $data = [
                    'password' => \Hash::make($all['new_pwd'])
                ];
                $i = \DB::table('users') -> where('id',$all['id']) -> update($data);
                if($i){
                    return 1;
                }else{
                    return "密码修改失败，请稍后再试";
                }
            }else{
                return "新密码与确认密码不同，请重新输入。";
            }
        }else{
            return "旧密码错误，请重新输入。";
        }
    }

    public function index()
    {
        return view('admin.indexs.index');
    }

    public function main()
    {
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = \DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) ->select('id') -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 查询总评论
            $merchants_comments = \DB::table('order_commnets')
//                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
//                -> join('goods','order_commnets.goods_id','=','goods.id')     // 链接商品表
                -> where('order_commnets.type',2)
//                -> where('order_commnets.is_del',0)
//                -> select(['order_commnets.id','users.name as username','goods.name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> get();
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
                -> get();
            // 查询售后服务
            $merchants_returns =\DB::table('order_goods')
                -> join('order_returns','order_goods.id','=','order_returns.order_goods_id')
                -> join('users','order_goods.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> where('order_goods.merchant_id',$id)
                -> select('order_goods.id','order_goods.order_id','users.name as user_name','refund_reason.name as retun_name',
                    'order_returns.content','order_returns.is_reg','order_returns.status')
                -> get();

        }else{
            // 反之则为。管理员
            // 查询，商城评论
            $merchants_comments = \DB::table('order_commnets')
                -> where('type',2)
                -> get();
            // 查询商品个数
            $merchants_goods = \DB::table('goods')
                -> join('merchants','goods.merchant_id','=','merchants.id')
                -> where('is_del',0)
                -> select(['merchants.name as merchant_name','goods.id','goods.pv','goods.created_at','goods.updated_at','goods.goods_cate_id','goods.img','goods.desc','goods.is_hot','goods.is_recommend','goods.is_sale','goods.is_bargain','goods.dilivery'])
                -> orderBy('goods.id','desc')
                -> get();
            // 查询订单个数
            $merchants_order = \DB::table('orders')
                -> where('orders.is_del',0)
                -> get();
            // 查询售后服务
            $merchants_returns =\DB::table('order_goods')
                -> join('order_returns','order_goods.id','=','order_returns.order_goods_id')
                -> join('users','order_goods.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> select('order_goods.id','order_goods.order_id','users.name as user_name','refund_reason.name as retun_name',
                    'order_returns.content','order_returns.is_reg','order_returns.status')
                -> get();
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
            -> get();
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
            -> get();
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
