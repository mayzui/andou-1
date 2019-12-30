<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class HotelController extends Controller
{   
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
        if (empty($all['uid'])||empty($all['token'])) {
           return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
           return $this->rejson($check['code'],$check['msg']);
        }
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
    /**
     * @api {post} /api/hotel/cate 酒店分类
     * @apiName cate
     * @apiGroup hotel
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *           [
     *           'name':'分类名字',
     *           'img':'分类图标',
     *           'id':'分类id'
     *           ]
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function cate(){
          $data=Db::table('hotel_category')
          ->select('id','name','img')
          ->where('status',1)
          ->get();
          return $this->rejson('200','查询成功',$data);
    }
    /**
     * @api {post} /api/hotel/condition 酒店搜索配置
     * @apiName condition
     * @apiGroup hotel
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "star": [
                    {
                        "id": '星级id',
                        "name": "星级名字"
                    }
                ],
                "price_range": [
                    {
                        "start": "最小价格",
                        "end": "最大价格"
                    }
                ]
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function condition(){
     $data['star']=$this->star;
     $data['price_range']=$this->price_range;
     return $this->rejson('200','查询成功',$data);

    }
    /**
     * @api {post} /api/hotel/hotellist 酒店商家
     * @apiName hotellist
     * @apiGroup hotel
     * @apiParam {string} province_id 省id(不是必传)
     * @apiParam {string} city_id 市id(不是必传)
     * @apiParam {string} area_id 区id(不是必传)
     * @apiParam {string} type 排序方式(不是必传 1按距离,2按点价格)
     * @apiParam {string} page 查询页码(不是必传 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *       
               "merchants": [
                    {
                        "id": "商户id",
                        "address": "商家详细地址",
                        "tel": "电话号码",
                        "created_at": "创建时间",
                        "stars_all": "星级",
                        "merchant_type_id":"商户类型id",
                        "price":"最低价格",
                        "praise_num":"点赞数量"
                        "logo_img":"商家图片",
                        "name":"商家名字"
                    }
                ]
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function hotellist(){
        $all=request()->all();
        $num=10;
        $start=0;
        if (!empty($all['page'])) {
            $page=$all['page'];
            $start=$num*($page-1);
        }
        $where[]=['m.is_reg',1];
        $where[]=['m.merchant_type_id',3];
        if (!empty($all['province_id'])) {
            $where[]=['m.province_id',$all['province_id']];
        }
        if (!empty($all['city_id'])) {
            $where[]=['m.city_id',$all['city_id']];
        }
        if (!empty($all['area_id'])) {
            $where[]=['m.area_id',$all['area_id']];
        }
        $data['merchants']=Db::table('merchants as m')
        ->join('hotel_room as h','h.merchant_id','=','m.id')
        ->where($where)
        ->select('m.id','m.created_at','m.address','m.tel','m.stars_all','m.praise_num','m.logo_img','m.name',DB::raw('min(h.price) as price'))
        ->groupBy('m.id') 
        ->offset($start)
        ->limit(10)
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }

}

