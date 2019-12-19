<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class GoodsController extends Controller
{   
    /**
     * @api {post} /api/goods/index 在线商城
     * @apiName index
     * @apiGroup goods
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "banner": [
                    {
                        "id": '轮播id',
                        "img": "图片地址",
                        "url": "跳转地址"
                    }
                ],
                "category": [
                    {
                        "id": '分类id',
                        "img": "图片地址",
                        "name": "分类名字"
                    }
                    
                ],
                "recommend_goods": [
                    {
                        "id": '推荐商品id',
                        "img": "图片地址",
                        "name": "商品名字",
                        "price": "价格"
                    }
                ],
                "bargain_goods": [
                    {
                        "id": '特价商品id',
                        "img": "图片地址",
                        "name": "名字",
                        "price": "价格"
                    }
                ]
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function index(){
        $data['banner']=Db::table('banner')
        ->select('id','img','url')
        ->where(['banner_position_id'=>6],['status'=>1])
        ->orderBy('sort','ASC')
        ->get();
        $data['category']=Db::table('goods_cate')
        ->select('id','img','name')
        ->where(['pid'=>0],['status'=>1])
        ->orderBy('sort','ASC')
        ->limit(8)
        ->get();
        $data['recommend_goods']=Db::table('goods')
        ->select('id','img','name','price')
        ->where(['is_recommend'=>1],['is_sale'=>1])
        ->orderBy('created_at','DESC')
        ->limit(4)
        ->get();
        $data['bargain_goods']=Db::table('goods')
        ->select('id','img','name','price')
        ->where(['is_bargain'=>1],['is_sale'=>1])
        ->orderBy('created_at','DESC')
        ->limit(4)
        ->get();
        return $this->rejson(200,'查询成功',$data); 
    }
    /**
     * @api {post} /api/goods/goods 商品详情数据
     * @apiName goods
     * @apiGroup goods
     * @apiParam {string} id 商品id
     * @apiParam {string} uid 用户id(非必传)
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "name": "商品名字",
                "img": "商品封面图",
                "album": "商品轮播图",
                "price": "价格",
                "dilivery": "运费",
                "volume": "销量",
                "store_num": "库存"
                "is_collection": "1为以收藏 0未收藏"
     *       },
     *       "msg":"登陆成功"
     *     }
     */
    public function goods() {
        $all=request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201,'缺少参数'); 
        }
        if(isset($all['uid'])){//添加浏览记录
            $this->seemerchant($all['uid'],$all['id'],1);
        }
        $data=DB::table('goods')
        ->select('name','img','album','price','dilivery','volume')
        ->where('id',$all['id'])
        ->first();
        $store_num=DB::table('goods_sku')
        ->where('goods_id',$all['id'])
        ->sum('store_num');
        if($store_num){
            $data->store_num=$store_num;
        }
        return $this->rejson(200,'查询成功',$data); 
    }
}