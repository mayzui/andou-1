<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class MerchantController extends Controller
{ 
    /**
     * @api {post} /api/merchant/merchants 商家列表
     * @apiName merchants
     * @apiGroup merchant
     * @apiParam {string} merchant_type_id 商户分类id(不是必传)
     * @apiParam {string} province_id 省id(不是必传)
     * @apiParam {string} city_id 市id(不是必传)
     * @apiParam {string} area_id 区id(不是必传)
     * @apiParam {string} type 排序方式(不是必传 1按评分查询,2按点赞数查询)
     * @apiParam {string} page 查询页码(不是必传 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "merchants": [
                    {
                        "id": "商户id",
                        "created_at": "创建时间",
                        "stars_all": "星级",
                        "praise_num":"点赞数量"
                        "logo_img":"商家图片",
                        "name":"商家名字"
                    }
                ],
                "merchant_type": [
                    {
                        "id": "商户分类id",
                        "type_name": "分类名字"
                    }
                ],
                "districts": [
                    {
                        "name": "北京",
                        "id": 11,
                        "pid": 0,
                        "cities": [
                            {
                                "name": "北京",
                                "id": 1101,
                                "pid": 11,
                                "areas": [
                                    {
                                        "name": "东城",
                                        "id": 110101,
                                        "pid": 1101
                                    }
                                ]
                            }
                        ]
                    }
                ]
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function merchants(){
        $all=request()->all();
        $num=10;
        $start=0;
        if (!empty($all['page'])) {
            $page=$all['page'];
            $start=$num*($page-1);
        }
        $where[]=['is_reg',1];
        if (!empty($all['merchant_type_id'])) {
            $where[]=['merchant_type_id',$all['merchant_type_id']];
        }
        if (!empty($all['province_id'])) {
            $where[]=['province_id',$all['province_id']];
        }
        if (!empty($all['city_id'])) {
            $where[]=['city_id',$all['city_id']];
        }
        if (!empty($all['area_id'])) {
            $where[]=['area_id',$all['area_id']];
        }
        if (!empty($all['type'])) {
            if ($all['type']==1) {
                $orderBy="stars_all";
            }elseif ($all['type']==2) {
                $orderBy="praise_num";
            }else{
                $orderBy="created_at";
            }
        }else{
            $orderBy="created_at";
        }
        $data['merchants']=Db::table('merchants')
        ->where($where)
        ->select('id','created_at','address','tel','stars_all','praise_num','logo_img','name')
        ->orderBy($orderBy,"DESC")
        ->offset($start)
        ->limit(10)
        ->get();
        $data['merchant_type']=Db::table('merchant_type')
        ->select('id','type_name')
        ->where('status',1)
        ->orderBy('sort','ASC')
        ->get();
        $data['districts']=Redis::get('districts');
        if ($data['districts']) {
            $data['districts']=json_decode($data['districts'],1);
        }else{
            $data['districts']=$this->districts();
            Redis::set('districts',json_encode($data['districts'],1));
        }
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/merchant/merchant_goods 商户详情
     * @apiName merchant_goods
     * @apiGroup merchant
     * @apiParam {string} id 商户id
     * @apiParam {string} uid 用户id(登陆过后传)
     * @apiParam {string} keyword 关键字查询(非必传)
     * @apiParam {string} type_id 分类id查询(非必传)
     * @apiParam {string} price_sort 价格排序(非必传1为倒序,0为正序)
     * @apiParam {string} volume_sort 销量排序(非必传1为倒序,0为正序)
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *          "name": "商户名字",
                "banner_img": "背景海报图",
                "logo_img": "头像图片",
                "goods": [
                    {
                        "name": "商品名字",
                        "img": "商品图片",
                        "price": "价格",
                        "id": "商品id"
                    }
                ],
                "type": [
                    {
                        "name": "分类名字",
                        "id": "分类id"
                    }
                ]
     *       },
     *       "msg":"查询成功"
     *     }
     */
    public function merchantGoods(){
        $all=request()->all();
        $num=10;
        
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        if (empty($all['id'])) {
            return $this->rejson(201,'参数错误');
        }
        if (isset($all['uid'])) {//添加浏览记录
            $this->seemerchant($all['uid'],$all['id'],2);
        }
        $where[]=['merchant_id',$all['id']];
        if (isset($all['keyword'])) {
            $where[]=['name', 'like', '%'.$all['keyword'].'%'];
        }
        if (isset($all['type_id'])) {
            $where[]=['merchants_goods_type_id',$all['type_id']];
        }

        $orderBy='pv';
        $sort='DESC';
        if (isset($all['price_sort'])) {
            if ($all['price_sort']==1) {
               $orderBy='price'; 
            }else{
               $orderBy='price';
               $sort='ASC'; 
            }
        }
        if (isset($all['volume_sort'])) {
            if ($all['volume_sort']==1) {
               $orderBy='volume'; 
            }else{
               $orderBy='volume';
               $sort='ASC';  
            }
        }
        $id=$all['id'];
        $data=Db::table('merchants')->select('name','banner_img','logo_img')->where('id',$id)->first();
        $data->goods=Db::table('goods')
        ->select('name','img','price','id')
        ->where($where)
        ->orderBy($orderBy,$sort)
        ->offset($pages)
        ->limit($num)
        ->get();
        $data->type=Db::table('merchants_goods_type')->select('name','id')->where(['merchant_id'=>$id,'is_del'=>1])->get();
        return $this->rejson('200','查询成功',$data);

    }
}