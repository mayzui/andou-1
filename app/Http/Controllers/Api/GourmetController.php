<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/3
 * Time: 10:03
 */

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class GourmetController extends Controller
{
    /**
     * @api {post} /api/gourmet/delicious 美食分类
     * @apiName delicious
     * @apiGroup gourmet
     * @apiSuccessExample 参数返回：
     * {
    "code":"200",
     *      "data":[
     *             {
     *              "id":"id",
    "name":"名称",
    "img":"分类图标"
     *             }
     *             ],
     * "msg":"查询成功"
     * }
     */
    public function delicious(){
        $all=\request()->all();
        $data=DB::table("hotel_category")
            ->select(['name','id','img'])
            ->where(['type_id'=>2])
            ->get();

        if($data){
            return $this->rejson(200,"查询成功",$data);
        }else{
            return $this->rejson(201,"查询失败");
        }
    }
    /**
     * @api {post} /api/gourmet/list 商铺列表
     * @apiName list
     * @apiGroup gourmet
     * @apiParam {string} name 关键字name
     * @apiSuccessExample 参数返回：
     *{
    "code":"200",
     *    "data":[
     *         {
    "id":"商户id",
    "name":"商家名称",
    "praise_num":"点赞数量",
    "stars_all":"商家星级",
    "door_img":"商家门头图",
    "cai":[
    {
    "id":"菜品id",
    "image":"菜品图片"
     *                }
     *                ]
     *         }
     *    ],
     *     "msg":"查询成功",
     * }
     */
    public function list(){
        $all=request()->all();
        $num=10;
        if(isset($all['page'])){
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        if (!empty($all['name'])) {
            $where[]=['name', 'like', '%'.$all['name'].'%'];
        }
        if(!empty($all['cate_id'])){
            $where[]=['cate_id',$all['cate_id']];
        }
        $where[]=['status',1];
        $where[]=['is_reg',1];
        $where[]=['merchant_type_id',4];
        $data=DB::table("merchants")
            ->select('name','praise_num','stars_all','door_img','id')
            ->where($where)
            ->offset($pages)
            ->limit($num)
            ->get();
        foreach ($data as $key => $value) {
            $data[$key]->cai=DB::table('foods_information')
                ->select(['id','image'])
                ->where('merchant_id',$value->id)
                ->offset(0)
                ->limit(3)
                ->get();
        }
        if($data){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'查询失败');
        }
    }
    /**
     * @api {post} /api/gourmet/details 商铺详情
     * @apiName details
     * @apiGroup gourmet
     * @apiParam {string} id 商家id
     * @apiSuccessExample 参数返回：
     * {
    "code":"200",
     *      "data":{
     *               "name":"商家名称",
     *               "door_img":"商家门头图",
     *               "praise_num":"点赞数量",
     *               "address":"商家地址",
     *               "desc":"商家简介",
     *               "stars_all":"商家星级",
     *               "business_start":"营业时间",
     *               "business_end":"结束时间",
     *               "tel":"联系电话"
     *             },
     *          "msg":"查询成功"
     * }
     */
    public function details(){
        $all=\request()->all();
        $data=DB::table("merchants as m")
            ->leftJoin("merchant_stores as s","m.id","=","s.merchant_id")
            ->select('m.name','m.door_img','m.praise_num','m.address','m.desc','m.tel','m.stars_all','s.business_start','s.business_end')
            ->where('m.id',$all['id'])
            ->first();
        if($data){
            return $this->rejson(200,"查询成功",$data);
        }else{
            return $this->rejson(201,"查询失败");
        }
    }
    /**
     * @api {post} /api/gourmet/dishtype 菜品类型
     * @apiName dishtype
     * @apiGroup gourmet
     * @apiParam {string} merchants_id 商铺id
     * @apiSuccessExample 参数返回：
     * {
    "code":"200"
     *        "data":[
     *               {
     *                "name":"菜品类型名称",
     *                "information":[
     *                              {
     *                              "id":"菜品id",
     *                              "image":"菜品图片",
     *                              "name":"菜品名称",
     *                              "remark":"菜品介绍",
     *                              "price":"菜品价格"
     *                              }
     *                              ]
     *               }
     *               ],
     *        "msg":"查询成功"
     * }
     */
    public function dishtype(){
        $all=\request()->all();
        $data=DB::table("foods_classification")
            ->select('name','id')
            ->where('merchants_id',$all['merchants_id'])
            ->get();
        foreach ($data as $key => $value) {
            $data[$key]->information=DB::table('foods_information')
                ->select(['id','image','name','remark','price'])
                ->where('classification_id',$value->id)
                ->get();
        }
        if($data){
            return $this->rejson(200,"查询成功",$data);
        }else{
            return $this->rejson(201,"查询失败");
        }
    }

    /**
     * @api {post} /api/gourmet/dishes 菜品详情
     * @apiName dishes
     * @apiGroup gourmet
     * @apiParam {string} id 菜品id
     * @apiSuccessExample 参数返回：
     * {
    "code":"200"
     *      "data":{
     *               "id":"菜品id",
     *               "image":"菜品图片",
     *               "name":"菜品名称",
     *               "remark":"菜品介绍",
     *               "price":"价格",
     *               "name":"规格名称"
     *              },
     *           "msg":"查询成功"
     * }
     */
    public function dishes(){
        $all=\request()->all();
        $data=DB::table("foods_information as f")
            ->join("foods_spec as s","f.classification_id","=","s.id")
            ->select('f.id','f.image','f.name','f.remark','f.price','s.name','s.id')
            ->where('f.id',$all['id'])
            ->first();
        if($data){
            return $this->rejson(200,"查询成功",$data);
        }else{
            return $this->rejson(201,"查询失败");
        }
    }

    /**
     * @api {post} /api/gourmet/comment 用户评论
     * @apiName comment
     * @apiGroup gourmet
     * @apiParam {string}  id 商户id
     * @apiParam {string}  page 分页页码
     * @apiSuccessExample 返回参数：
     * {
     *     "code":"200",
     *     "data":[
     *          {
     *          "stars":"评星",
     *          "created_at":"评论时间",
     *          "content":"评论内容",
     *          "name":"用户名",
     *          "avator"："用户头像"
     *          }
     *      ],
     *      "msg":"查询成功"
     *   }
     */
    //评论
    public function comment()
    {
        $all = \request()->all();
        $num=10;
        if ($all['page']){
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        $data = DB::table("order_commnets as m")
            ->join("users as u","m.user_id","=","u.id")
            ->select(['m.merchants_id','m.content','m.stars','m.created_at','u.avator','u.name'])
            ->where(['m.merchants_id'=>$all['id'],'m.type'=>3])
            ->offset($pages)
            ->limit($num)
            ->get();
        if ($data) {
            return $this->rejson(200, "查询成功", $data);
        } else {
            return $this->rejson(201, "查询失败");
        }
    }

    /**
     * @api {post} /api/gourmet/booking 购物车列表
     * @apiName booking
     * @apiGroup gourmet
     * @apiParam {string} user_id 用户id
     * @apiParam {string} merchant_id 商户id
     * @apiSuccessExample 参数返回：
     * {
    "code":"200",
     *           "data":[
     *                  {
     *	          "id":"id",
     *                  "name":"菜品名称",
     *                  "price":"菜品价格",
     *                  "num":"菜品数量"
     *                  }
     *                  ],
     *    "msg":"查询成功"
     * }
     */
    public function booking(){
        $all=\request()->all();
        $data=DB::table("foods_cart as c")
            ->join("merchants as m",'c.merchant_id','=','m.id')
            ->select(['c.name','c.price','c.num','c.id'])
            ->where(['c.user_id'=>['user_id'],'m.id'=>$all['merchant_id']])
            ->get();
        if($data){
            return $this->rejson(200,"查询成功",$data);
        }else{
            return $this->rejson(201,"查询失败");
        }
    }

    /**
     * @api {post} /api/gourmet/shopping_num 购物车数量
     * @apiName shopping_num
     * @apiGroup gourmet
     * @apiParam {string} user_id 用户id
     * @apiParam {string} merchant_id 商户id
     * @apiSuccessExample 参数返回：
     * {
    "code":"200",
     *           "data":[
     *                  {
     *                   "num":"购物车菜品数量",
     *                    "price":"价格"
     *                  }
     *                  ],
     *          "msg":"查询成功"
     * }
     */
    public function shopping_num(){
        $all=\request()->all();
        $data=DB::table("foods_cart as c")
            ->join("merchants as m",'c.merchant_id','=','m.id')
            ->select(['c.num','c.price'])
            ->where(['c.user_id'=>$all['user_id'],'c.merchant_id'=>$all['merchant_id']])
            ->get();
        if($data){
            return $this->rejson(200,"查询成功",count($data));
        }else{
            return $this->rejson(201,"查询失败");
        }
    }

    /**
     * @api {post} /api/gourmet/add_foods 添加购物车
     * @apiName add_foods
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} foods_id 菜品id
     * @apiParam {string} spec_id 规格id
     * @apiParam {string} merchant_id 商户id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加成功"
     *     }
     */
    public function add_foods(){
        $all=request()->all();
        if (empty($all['foods_id'])||empty($all['merchant_id'])){
            return $this->rejson(201,"缺少参数");
        }
        $data['foods_id']=$all['foods_id'];
        $data['merchant_id']=$all['merchant_id'];
        $data['spec_id']=$all['spec_id'];
        $data['num']=1;
        $data['user_id']=$all['uid'];
        $where[]=['foods_id',$data['foods_id']];
        $where[]=['merchant_id',$data['merchant_id']];
        $where[]=['user_id',$data['user_id']];
        $res=DB::table('foods_cart')->where($where)->first();
        if(!empty($res)){
            $re=DB::table('foods_cart')->where($where)->increment('num');
            return $this->rejson(200,'添加成功');
        }
        $re=DB::table('foods_cart')->insert($data);
        if ($re) {
            return $this->rejson(200,'添加成功');
        }else{
            return $this->rejson(201,'添加失败');
        }
    }


}