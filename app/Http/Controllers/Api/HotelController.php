<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class HotelController extends Controller
{   
    public function __construct()
    {
        $all=request()->all();
        if (empty($all['uid'])||empty($all['token'])) {
           return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
           return $this->rejson($check['code'],$check['msg']);
        }
    }
    /**
     * @api {post} /api/hotel/order 酒店订单
     * @apiName order
     * @apiGroup hotel
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
        "book_sn":"订单编号",
        "logo_img":"商家logo图",
        "merchants_name":"商家名称",
        "status":"订单状态（0-取消订单 10-未支付订单 20-已支付(待入住) 30 已入住 40-已完成(离店) 50-已评价）",
        "img":"房间图片",
        "house_name":"房间名称",
        "price":"房间价格"
     * },
     *       "msg":"查询成功"
     *     }
     */
    public function order(){
        $all=request()->all();
        // 查询酒店订单
        $data = DB::table('books')
            -> join('orders','books.book_sn','=','orders.order_sn')
            -> join('merchants','books.merchant_id','=','merchants.id')
            -> join('hotel_room','books.hotel_room_id','=','hotel_room.id')
            -> where('hotel_room.status',1)
            -> where('books.user_id',$all['uid'])
            -> select(['books.book_sn','merchants.logo_img','merchants.name as merchants_name','orders.status','hotel_room.img','hotel_room.house_name','hotel_room.price'])
            -> get();
        return $this->rejson(200,'查询成功',$data);
    }
    // 095ea37ba7e076a2dfc1592a2022bd6a
}