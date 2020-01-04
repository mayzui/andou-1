<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class MerchantController extends Controller
{

    // public function __construct()
    // {
    //     $all = request()->all();
    //     $token=request()->header('token')??'';
    //     if ($token!='') {
    //         $all['token']=$token;
    //     }
    //     if (empty($all['uid']) || empty($all['token'])) {
    //         return $this->rejson(202, '登陆失效');
    //     }
    //     $check = $this->checktoten($all['uid'], $all['token']);
    //     if ($check['code'] == 202) {
    //         return $this->rejson($check['code'], $check['msg']);
    //     }
    // }

    /**
     * @api {post} /api/merchant/merchants 商家列表第一次请求
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
        ->whereIn('merchant_type_id',[2,3])
        ->select('id','created_at','merchant_type_id','address','tel','stars_all','praise_num','logo_img','name')
        ->orderBy($orderBy,"DESC")
        ->offset($start)
        ->limit(10)
        ->get();
        foreach ($data['merchants'] as $key => $value) {
            if ($value->merchant_type_id==2) {
                $data['merchants'][$key]->price=Db::table('goods')->where('merchant_id',$value->id)->orderBy('price')->first()->price ?? 0;
            }elseif ($value->merchant_type_id==3) {
                $data['merchants'][$key]->price=Db::table('hotel_room')->where('merchant_id',$value->id)->orderBy('price')->first()->price ?? 0;
            }
        }
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
     * @api {post} /api/merchant/merchants_two 商家列表条件查询
     * @apiName merchants_two
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
    public function merchantsTwo(){
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
        ->whereIn('merchant_type_id',[2,3])
        ->select('id','created_at','merchant_type_id','address','tel','stars_all','praise_num','logo_img','name')
        ->orderBy($orderBy,"DESC")
        ->offset($start)
        ->limit(10)
        ->get();
        foreach ($data['merchants'] as $key => $value) {
            if ($value->merchant_type_id==2) {
                $data['merchants'][$key]->price=Db::table('goods')->where('merchant_id',$value->id)->orderBy('price')->first()->price ?? 0;
            }elseif ($value->merchant_type_id==3) {
                $data['merchants'][$key]->price=Db::table('hotel_room')->where('merchant_id',$value->id)->orderBy('price')->first()->price ?? 0;
            }
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
            }elseif($all['price_sort']==2){
               $orderBy='price';
               $sort='ASC'; 
            }
        }
        if (isset($all['volume_sort'])) {
            if ($all['volume_sort']==1) {
               $orderBy='volume'; 
            }elseif($all['volume_sort']==2){
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
        return $this->rejson(200,'查询成功',$data);

    }
    /**
     * @api {post} /api/merchant/entry 商家入驻
     * @apiName entry
     * @apiGroup merchant
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 登录验证
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
    "id":"商户类型id",
    "type_name":"分类名称",
    "remak":"商户简介",
    "img":"图片"
     * }
     *     }
     */
    public function entry(){
        $all = \request() -> all();
        $token=request()->header('token')??'';
        if ($token!='') {
            $all['token']=$token;
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==201) {
            return $this->rejson($check['code'],$check['msg']);
        }
        $data = DB::table('merchant_type')
            -> select('id','type_name','remak','img')
            -> get();
        return $this->rejson('200','查询成功',$data);

    }
    /**
     * @api {post} /api/merchant/information 商家填写资料入驻
     * @apiName information
     * @apiGroup merchant
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 登录验证
     * @apiParam {string} type_id 入驻商家类型id（必填）
     * @apiParam {string} name 商家名称（必填）
     * @apiParam {string} user_name 联系人名称（必填）
     * @apiParam {string} tel 联系人电话（必填）
     * @apiParam {string} province_id 店铺地址：省（必填）
     * @apiParam {string} city_id 店铺地址：市（必填）
     * @apiParam {string} area_id 店铺地址：区（必填）
     * @apiParam {string} address 详细地址（必填）
     * @apiParam {string} desc 商家简介（必填）
     * @apiParam {string} banner_img 商家海报图（必填）
     * @apiParam {string} logo_img 商家Logo图（必填）
     * @apiParam {string} management_img 营业执照（必填）
     * @apiParam {string} door_img 商家门头图（商城商家：不填）
     * @apiParam {string} goods_img 食品经营许可证（饭店商家：必填）
     * @apiParam {string} management_type_id 经营品种id（饭店商家：必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"申请成功",
     *       "data": ""
     *     }
     */
    public function information(){
        $all = \request() -> all();
        $token=request()->header('token')??'';
        if ($token!='') {
            $all['token']=$token;
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==201) {
            return $this->rejson($check['code'],$check['msg']);
        }
        if(empty($all['type_id'])){
            return $this->rejson(201,'入驻商家类型id不能为空');
        }else if(empty($all['name'])){
            return $this->rejson(201,'商家名称不能为空');
        }else if(empty($all['user_name'])){
            return $this->rejson(201,'联系人名称不能为空');
        }else if(empty($all['tel'])){
            return $this->rejson(201,'联系人电话不能为空');
        }else if(empty($all['address'])){
            return $this->rejson(201,'详细地址不能为空');
        }else if(empty($all['desc'])){
            return $this->rejson(201,'商家简介不能为空');
        }else if(empty($all['banner_img'])){
            return $this->rejson(201,'商家海报图不能为空');
        }else if(empty($all['logo_img'])){
            return $this->rejson(201,'商家Logo图不能为空');
        }else if(empty($all['management_img'])){
            return $this->rejson(201,'营业执照不能为空');
        }else if(empty($all['province_id'])){
            return $this->rejson(201,'省不能为空');
        }else if(empty($all['city_id'])){
            return $this->rejson(201,'市不能为空');
        }else if(empty($all['area_id'])){
            return $this->rejson(201,'区不能为空');
        }
        // 判断用户入驻什么商家

        // 商城商家
        if($all['type_id'] == 2){
            $data = [
                'user_id' => $all['uid'],
                'merchant_type_id' => $all['type_id'],
                'name' => $all['name'],
                'banner_img' => $all['banner_img'],
                'logo_img' => $all['logo_img'],
                'management_img' => $all['management_img'],

                'desc' => $all['desc'],
                'province_id' => $all['province_id'],
                'city_id' => $all['city_id'],
                'area_id' => $all['area_id'],
                'address' => $all['address'],
                'tel' => $all['tel'],
                'user_name' => $all['user_name'],
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }else if($all['type_id'] ==  3){  // 入驻酒店商家
            if(empty($all['door_img'])){
                return $this->rejson(201,'商家门头图不能为空');
            }
            $data = [
                'user_id' => $all['uid'],
                'merchant_type_id' => $all['type_id'],
                'name' => $all['name'],
                'banner_img' => $all['banner_img'],
                'logo_img' => $all['logo_img'],
                'management_img' => $all['management_img'],
                'goods_img' => $all['goods_img'],

                'desc' => $all['desc'],
                'province_id' => $all['province_id'],
                'city_id' => $all['city_id'],
                'area_id' => $all['area_id'],
                'address' => $all['address'],
                'tel' => $all['tel'],
                'user_name' => $all['user_name'],
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }else if($all['type_id'] == 4){   // 入驻饭店商家
            if(empty($all['door_img'])){
                return $this->rejson(201,'商家门头图不能为空');
            }else if(empty($all['goods_img'])){
                return $this->rejson(201,'食品经营许可证不能为空');
            }else if(empty($all['management_type_id'])){
                return $this->rejson(201,'经营品种id不能为空');
            }
            $data = [
                'user_id' => $all['uid'],
                'merchant_type_id' => $all['type_id'],
                'name' => $all['name'],
                'banner_img' => $all['banner_img'],
                'logo_img' => $all['logo_img'],
                'management_img' => $all['management_img'],
                'door_img' => $all['door_img'],
                'goods_img' => $all['goods_img'],

                'desc' => $all['desc'],
                'province_id' => $all['province_id'],
                'city_id' => $all['city_id'],
                'area_id' => $all['area_id'],
                'address' => $all['address'],
                'tel' => $all['tel'],
                'user_name' => $all['user_name'],
                'management_type' => $all['management_type_id'],
                'created_at' => date("Y-m-d H:i:s"),
            ];
        }
        $i = DB::table('merchants') -> insert($data);
        if($i){
            return $this->rejson('200','已提交入驻申请');
        }else{
            return $this->rejson('200','当前入驻人数较多，请稍后再试');
        }

    }

    public function uploads($files)
    {
        // $files=$all['imgs'];
        $count=count($files);
        $msg=array();
        // var_dump($files);exit;
        foreach ($files as $k=>$v){
            $type = $v->getClientOriginalExtension();
            $path=$v->getPathname();
            if($type == "png" || $type == "jpg"){
                $newname = 'uploads/'.date ( "Ymdhis" ).rand(0,9999);
                $url = $newname.'.'.$type;
                $upload=move_uploaded_file($path,$url);
                $msg[]=$url;
            }else{
                return 0;
            }
        }
        return implode(',',$msg);
    }
    // W83tVnay3ZPCsMA
}