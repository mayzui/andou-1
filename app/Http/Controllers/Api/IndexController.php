<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class IndexController extends Controller
{   
    /**
     * @api {post} /api/index/index 首页
     * @apiName index
     * @apiGroup index
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *           "banner": [
     *               {
     *                   "id": "轮播图id",
     *                   "img": "图片地址",
     *                   "url": "跳转地址"
     *                }
     *          ],
     *           "merchant_type": [
                    {
                        "id": "商户分类id",
                        "img": "商户分类图片"
                    }
                ],
                 "merchants": [
                    {
                        "id": "商户id",
                        "logo_img": "商户logo",
                        "name": "商户名字"
                    }
                ],
                "notice": [
                    {
                        "id": "公告id",
                        "content": "公告内容",
                        "updated_at": "更新时间"
                    }
                ]
     *        }
     *       "msg":"登陆成功"
     *     }
     */
    public function index(){
        $data['banner']=Db::table('banner')
        ->select('id','img','url')
        ->where(['banner_position_id'=>6],['status'=>1])
        ->orderBy('sort','ASC')
        ->get();
        $data['merchant_type']=Db::table('merchant_type')
        ->select('id','img')
        ->where('status',1)
        ->orderBy('sort','ASC')
        ->get();
        $data['merchants']=Db::table('merchants')
        ->select('id','logo_img','name')
        ->where('recommend',1)
        ->orderBy('updated_at','DESC')
        ->get();
        $data['notice']=Db::table('notice')
        ->select('id','content','updated_at')
        ->where('status',1)
        ->orderBy('updated_at','DESC')
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/index/merchants 商家列表
     * @apiName merchants
     * @apiGroup index
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
                        "stars_all": "星级"
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
        ->select('id','created_at','address','tel','stars_all')
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
}