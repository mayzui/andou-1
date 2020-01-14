<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class CartController extends Controller
{   
    public function __construct()
    {
        $all=request()->all();
        $token=request()->header('token')??'';
        if ($token!='') {
            $all['token']=$token;
        }
        if (empty($all['uid'])||empty($all['token'])) {
           return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
           return $this->rejson($check['code'],$check['msg']);
        }
    }
    /**
     * @api {post} /api/cart/index 购物车列表
     * @apiName index
     * @apiGroup cart
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 分页参数
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "id": "购物车id",
                        "goods_id": "商品id",
                        "goods_sku_id": "商品规格id",
                        "merchant_id": "商户id",
                        "num": "购买数量",
                        "goods_name": "商品名字",
                        "img": "商品图片",
                        "price": "价格",
                        "merchant_name": "商家名字",
                        "logo_img": "商家图片"
                        }
             ],
     *       "msg":"查询成功"
     *     }
     */
    public function index(){
        $all=request()->all();
        $num=10;
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        $data=Db::table('cart as c')
        ->join("goods as g","c.goods_id","=","g.id")
        ->join("goods_sku as s","c.goods_sku_id","=","s.id")
        ->join("merchants as m","c.merchant_id","=","m.id")
        ->select('c.id','c.goods_id','c.goods_sku_id','c.merchant_id','c.num','g.name as goods_name','g.img','s.price','m.name as merchant_name','m.logo_img')
        ->where(['c.user_id'=>$all['uid'],'c.type'=>1])
        ->orderBy('c.created_at','DESC')
        ->offset($pages)
        ->limit($num)
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/cart/delcar 删除购物车 
     * @apiName delcar
     * @apiGroup cart
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array} id 需要删除的购物车id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"删除成功"
     *     }
     */
    public function delcar(){
        $all=request()->all();
        $all['id']=explode(',',$all['id']);
        if (empty($all['id'])) {
            return $this->rejson(201,'请选择需要删除的数据');
        }
        $re=Db::table('cart')->where('user_id',$all['uid'])->whereIn('id',$all['id'])->delete();
        if($re){
            return $this->rejson(200,'删除成功');
        }else{
            return $this->rejson(201,'删除失败');
        }
    }
    /**
     * @api {post} /api/cart/addcar 添加购物车 
     * @apiName addcar
     * @apiGroup cart
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} goods_id 商品id
     * @apiParam {string} merchant_id 商户id
     * @apiParam {string} goods_sku_id 规格id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加成功"
     *     }
     */
    public function addcar(){
        $all=request()->all();
        if (empty($all['goods_id'])||empty($all['merchant_id'])||empty($all['goods_sku_id'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data['goods_id']=$all['goods_id'];
        $data['merchant_id']=$all['merchant_id'];
        $data['goods_sku_id']=$all['goods_sku_id'];
        $data['num']=1;
        $data['user_id']=$all['uid'];
        $data['type']=1;
        $data['created_at']=date('Y-m-d H:i:s',time());
        $where[]=['goods_id',$data['goods_id']];
        $where[]=['merchant_id',$data['merchant_id']];
        $where[]=['goods_sku_id',$data['goods_sku_id']];
        $where[]=['user_id',$data['user_id']];
        $where[]=['type',$data['type']];
        $res=DB::table('cart')->where($where)->first();
        if (!empty($res)) {
            $re=DB::table('cart')->where($where)->increment('num');
            return $this->rejson(200,'添加成功');
        }
        $re=DB::table('cart')->insert($data);
        if ($re) {
            return $this->rejson(200,'添加成功');
        }else{
            return $this->rejson(201,'添加失败');
        }
    }
    /**
     * @api {post} /api/cart/update_num 修改购物车购买数量 
     * @apiName update_num
     * @apiGroup cart
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 购物车id
     * @apiParam {string} type 修改的方式(1自动加1 0自动减1)
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"删除成功"
     *     }
     */
    public function update_num(){
        $all=request()->all();
        if (empty($all['id']) || !isset($all['type'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data=DB::table('cart')->select('num')->where('id',$all['id'])->first();
        if ($all['type'] == 1) {   
            $re=DB::table('cart')->where('id',$all['id'])->increment('num'); 
        }else if($all['type'] == 0){
            if ($data->num <= 1) {
                 return $this->rejson(200,'购买数量不能小于1');
            }else{
                $re=DB::table('cart')->where(['id'=>$all['id'],'user_id'=>$all['uid']])->decrement('num');
            }
        }
        return $this->rejson(200,'修改成功');
    }
}