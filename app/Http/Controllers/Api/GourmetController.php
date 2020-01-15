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
                ->where('status',1)
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
     *               "id":"商铺id"，
     *               "name":"商家名称",
     *               "door_img":"商家门头图",
     *               "logo_img":"商家logo",
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
            ->select('m.name','m.id','m.logo_img','m.door_img','m.praise_num','m.address','m.desc','m.tel','m.stars_all','s.business_start','s.business_end')
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
                ->where('status',1)
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
            ->where('f.status',1)
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
     * @apiParam {string} token 用户token
     * @apiParam {string} merchant_id 商户id
     * @apiSuccessExample 参数返回：
     * {
    "code":"200",
     *           "data":[
     *                  {
     *                 "id":"菜品id",
     *                  "num":"菜品数量",
     *                  "name":"菜品名称",
     *                  "image":"菜品图片",
     *                  "price":"菜品价格"
     *                  }
     *                  ],
     *    "msg":"查询成功"
     * }
     */
    public function booking(){
        $all=\request()->all();
        $token=request()->header('token')??'';
        if ($token!='') {
            $all['token']=$token;
        }
        if (empty($all['user_id'])||empty($all['token'])) {
           return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['user_id'],$all['token']);
        if ($check['code']==202) {
           return $this->rejson($check['code'],$check['msg']);
        }
       $data=DB::table("foods_cart as c")
           ->join("merchants as m","c.merchant_id","=","m.id")
           ->join("foods_information as f","c.foods_id","=","f.id")
           ->select(['c.num','f.id','f.name','f.image','f.price'])
           ->where(['m.id'=>$all['merchant_id'],'c.user_id'=>$all['user_id']])
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
        if (empty($all['foods_id'])||empty($all['merchant_id'])){
            return $this->rejson(201,"缺少参数");
        }
        $data['foods_id']=$all['foods_id'];
        $data['merchant_id']=$all['merchant_id'];
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
    /**
     * @api {post} /api/gourmet/del_foods 删除购物车
     * @apiName del_foods
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 购物车id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"删除成功"
     *     }
     */
    public function del_foods(){
        $all=\request()->all();
        
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
        if(empty($all['id'])){
            return $this->rejson(201,"请选择要删除的id");
        }
        $res=DB::table("foods_cart")->where('user_id',$all['uid'])->where('id',$all['id'])->delete();
        if($res){
            return $this->rejson(200,'删除成功');
        }else{
            return $this->rejson(201,'删除失败');
        }
    }
    /**
     * @api {post} /api/gourmet/upd_foods 修改购物车
     * @apiName upd_foods
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 食品id
     * @apiParam {string} merchant_id 商户id
     * @apiParam {string} type 修改的方式(1自动加1 0自动减1)
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"删除成功"
     *     }
     */
    public function upd_foods(){
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
        if (empty($all['id']) || !isset($all['type'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data=DB::table("foods_cart")->select('num')->where('merchant_id',$all['merchant_id'])->where('foods_id',$all['id'])->where('user_id',$all['uid'])->first();
        if (empty($data)) {
            if ($all['type'] != 1) {
               return $this->rejson(201,'购物车不存在该商品');
            }
            $data['foods_id']=$all['id'];
            $data['merchant_id']=$all['merchant_id'];
            $data['num']=1;
            $data['user_id']=$all['uid'];
            $data=DB::table("foods_cart")->insert($data);
            return $this->rejson(200,'新增成功');
        }
        if($all['type'] == 1){
            $res=DB::table('foods_cart')->where('foods_id',$all['id'])->increment('num');
        }else if ($all['type'] == 0 ){
            if($data->num <= 1){
                $res=DB::table('foods_cart')->where(['foods_id'=>$all['id'],'user_id'=>$all['uid']])->delete();
            }else{
                $res=DB::table('foods_cart')->where(['foods_id'=>$all['id'],'user_id'=>$all['uid']])->decrement('num');
            }
        }
        return $this->rejson(200,'修改成功');
    }
    /**
     * @api {post} /api/gourmet/reserve 预定
     * @apiName reserve
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} merchant_id 商户id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "logo_img": "酒店图片",
                "name": "酒店名字",
                "foods": [
                    {
                        "name": "名字",
                        "price": "单价",
                        "num": "数量",
                        "image": "商品图片"
                    }
                ],
                "all": "总价格",
                "integral": "需要使用的积分"
            },
     *       "msg":"查询成功"
     *     }
     */

    public function reserve()
    {
        $all = request()->all();
        if(empty($all['merchant_id']))
        {
            return $this->rejson('201','缺少参数');
        }
        $data = DB::table('merchants')->where('id',$all['merchant_id'])->select('logo_img','name')->first();
        $data->foods = DB::table("foods_cart")
            ->join('foods_information','foods_cart.foods_id','=','foods_information.id')
            ->where('foods_cart.merchant_id',$all['merchant_id'])
            ->where('foods_cart.user_id',$all['uid'])
            ->select(['foods_information.name','foods_information.price','foods_cart.num','foods_information.image'])
            ->get();
        $all = 0;
        foreach ($data->foods as $value){
            $all += $value->price*$value->num;
        }
        $data->all = $all;
        $allintegral=DB::table('users')->where('id',$all['uid'])->first()->integral ?? 0;
        $integrals=DB::table('config')->where('key','integral')->first()->value;
        $data->integral=floor($all*$integrals);
        return $this->rejson('200','查询成功',$data);
    }
    /**
     * @api {post} /api/gourmet/timely 立即预定
     * @apiName timely
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} merchant_id 商家id
     * @apiParam {string} people 用餐人数
     * @apiParam {string} remark 备注
     * @apiParam {string} dinnertime 用餐时间
     * @apiParam {string} method 支付方式的id
     * @apiParam {string} is_integral 是否使用积分（1使用 0不使用）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"预定成功"
     *     }
     */

    public function timely()
    {
        $all = request()->all();

        if(empty($all['merchant_id']) ||
            empty($all['people']) ||
            empty($all['dinnertime'])){
            return $this->rejson('201','缺少参数');
        }
        $foods = DB::table("foods_cart")
            ->join('foods_information','foods_cart.foods_id','=','foods_information.id')
            ->where('foods_cart.merchant_id',$all['merchant_id'])
            ->where('foods_cart.user_id',$all['uid'])
            ->select(['foods_information.id','foods_information.price','foods_cart.num'])
            ->get();
        $prices = 0;
        $foodss=[];
        foreach ($foods as $key=>$value){
            $prices += $value->price*$value->num;
            $foodss[$key]['id']=$value->id;
            $foodss[$key]['num']=$value->num;
        }
        $integrals=DB::table('config')->where('key','integral')->first()->value;
        $integral=floor($prices*$integrals);
        if ($prices==0) {
            return $this->rejson('201','商品价格出错');
        }
        $users=Db::table('users')->where('id',$all['uid'])->select('name','mobile','integral')->first();
        if ($all['is_integral']==1) {
            if ($users->integral < $integral) {
                return $this->rejson('201','积分不足');
            }
        }else{
           $integral=0; 
        }
        $res = [
            'user_id'=>$all['uid'],
            'merchant_id'=>$all['merchant_id'],
            'foods_id'=>json_encode($foodss,1),
            'dinnertime'=>$all['dinnertime'],
            'orderingtime'=>date('Y-m-d H:i:s',time()),
            'remark'=>$all['remark'],
            'status'=>10,
            'phone'=>$users->mobile,
            'user_name'=>$users->name,
            'people'=>$all['people'],
            'prices'=>$prices,
            'order_sn'=>$this->suiji(),
            'method'=>$all['method'],
            'integral'=>$integral
        ];

        $alldata['status']=10;
        $alldata['order_money']=$prices;
        $alldata['type']=3;
        $alldata['remark']=$all['remark']??'';
        $alldata['order_sn']=$res['order_sn'];
        $alldata['user_id']=$all['uid'];
        $alldata['shipping_free']=0;
        $alldata['created_at'] = $alldata['updated_at']=$res['orderingtime'];
        $alldata['auto_receipt']=$all['auto_receipt']??0;
        $alldata['shipping_free']=0;
        $alldata['integral']=$integral;
        $sNo = $res['order_sn'];
        $datas = Db::table('orders')->insert($alldata);
        $data = DB::table('foods_user_ordering')->insert($res);
        $resss = DB::table("foods_cart")->where('user_id',$all['uid'])->where('merchant_id',$all['merchant_id'])->delete();
        if($data){
            if ($all['method']==1) {//微信支付
                $this->wxpay($sNo);
            }else if($all['method']==2){//支付宝支付
                return $this->rejson(201,'暂未开通');
            }else if($all['method']==3){//银联支付
                return $this->rejson(201,'暂未开通');
            }else if($all['method']==4){//余额支付
                $this->balancePay($sNo);
            }else if($all['method']==5){//其他支付
                return $this->rejson(201,'暂未开通');
            }else{
                return $this->rejson(201,'暂未开通');
            }
            return $this->rejson('200','下单成功');
        }else{
            return $this->rejson('201','添加失败');
        }
    }
    /**
     * @api {post} /api/gourmet/balancePay 饭店订单余额支付
     * @apiName balancePay
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} sNo 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"预定成功"
     *     }
     */
    public function balancePay($sNo=''){
        $all=request()->all();
        if (!empty($all['sNo'])) {
           $sNo=$all['sNo'];
        }
        $orders = Db::table('orders')
        ->where(['order_sn'=>$sNo,'status'=>10])
        ->first();
        $users = Db::table('users')
        ->where('id',$all['uid'])
        ->first();
        $data['user_id']=$all['uid'];
        $data['describe']='订单：'.$sNo.'消费';
        $data['create_time']=date('Y-m-d H:i:s',time());
        $data['type_id']=2;
        $data['price']=$orders->order_money - $orders->integral;
        if ($data['price']>$users->money) {
           return $this->rejson(201,'余额不足');
        }
        if ($orders->integral>$users->integral) {
           return $this->rejson(201,'积分不足');
        }
        $data['state']=2;
        $data['is_del']=0;
        $status['status']=20;
        $status['pay_money']=$orders->order_money-$orders->integral;
        $status['pay_time']=date('Y-m-d H:i:s',time());

        DB::beginTransaction(); //开启事务
        $re=DB::table('user_logs')->insert($data);
        $ress=DB::table('orders')->where('order_sn',$sNo)->update($status);
        $ress=DB::table('foods_user_ordering')->where('order_sn',$sNo)->update($status);
        $res=DB::table('users')->where('id',$all['uid'])->decrement('money',$data['price']);
        if ($orders->integral>0) {
            $addintegral=$data;
            $addintegral['price']=$orders->integral;
            $addintegral['type_id']=1;
            $rei=DB::table('user_logs')->insert($addintegral);
            $resi=DB::table('users')->where('id',$all['uid'])->decrement('integral',$orders->integral);
        }
        if ($res&&$re&&$ress) {
            DB::commit();
            return $this->rejson(200,'预定成功');
        }else{
            DB::rollback();
            return $this->rejson(201,'支付失败');
        }

     }
    /**
     * @api {post} /api/gourmet/wxPay 饭店订单微信支付
     * @apiName wxPay
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} sNo 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"预定成功"
     *     }
     */
    public function wxPay($sNo=''){
        $all=request()->all();

        require_once base_path()."/wxpay/lib/WxPay.Api.php";
        require_once base_path()."/wxpay/example/WxPay.NativePay.php";
        if (!empty($all['sNo'])) {
           $sNo=$all['sNo'];
        }
        if (empty($sNo)) {
            return $this->rejson(201,'参数错误');
        }
        $users = Db::table('users')
        ->where('id',$all['uid'])
        ->first();
        //查找表里是否有此订单
        $orders = Db::table('orders')
            ->where('order_sn',$sNo)
            ->first();
        if ($orders->integral>$users->integral) {
           return $this->rejson(201,'积分不足');
        }    
        if (empty($orders)) {
            return $this->rejson(201,'订单不存在');
        }

        $pay_money = 100*($orders->order_money-$orders->integral);

        $input = new \WxPayUnifiedOrder();

        $input->SetBody("安抖商城平台");
        $input->SetOut_trade_no($sNo);
        $input->SetTotal_fee($pay_money);
//        $input->SetTotal_fee(1);
        $input->SetNotify_url("http://andou.zhuosongkj.com/api/common/gourmet");
        $input->SetTrade_type("APP");
        $input->SetSpbill_create_ip($_SERVER['REMOTE_ADDR']);
//        $input->SetAttach($uid);
        $config = new \WxPayConfig();
        $order = \WxPayApi::unifiedOrder($config, $input);
        // var_dump($order);exit();
        if($order['return_code']=="SUCCESS"){
            $time = time();
            $string = "appid=".$order['appid']."&noncestr=".$order['nonce_str']."&package="."Sign=WXPay"."&partnerid=".$order['mch_id']."&prepayid=".$order['prepay_id']."&timestamp=".$time."&key=AndoubendishenghuoXIdoukeji66888";
            $string = md5($string);
            $order['sign'] = strtoupper($string);
            $order['timestamp'] = $time;
            return  $this->rejson(200,'获取支付信息成功！',$order);
        }else{
            return  $this->rejson(201,'获取支付信息失败！');
        }
    }

    /**
     * @api {post} /api/gourmet/order 饭店订单
     * @apiName order
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 分页页码
     * @apiParam {string} status 10未支付，20已支付,30已使用,待评价,40已评价
     * @apiSuccessExample 参数返回:
     * {
     *      "code":"200",
     *         "data":[
     *              {
     *                  "name":"饭店名称",
     *                  "logo_img":"商家logo图",
     *                  "prices":"订单总金额",
     *                  "merchant_id":"商户id",
     *                  "id":"订单id",
     *                  "order_sn":"订单编号",
     *                  "people":"用餐人数",
     *                  "dinnertime":"用餐时间",
     *                  "remark":"备注",
     *                  "status":"订单状态 (10未支付，20已支付,30已使用,待评价,40已评价)",
     *                  "foods":[
     *                                {
     *                                  "id":"菜品id",
     *                                  "name":"菜品名称",
     *                                  "price":"菜品价格",
     *                                  "num":"数量"
     *                                }
     *                                ]
     *              }
     *              ],
     * "msg":"查询成功"
     * }
     */
    public function order(){
        $all=\request()->all();
        $num=8;
        if (empty($all['page'])) {
            $pages=0;
        }else{
            $pages=($all['page']-1)*$num;
        }

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
        $where[]=['o.user_id',$all['uid']];
        if (!empty($all['status'])) {
            $where[]=['o.status',$all['status']];
        }
        $data=DB::table("foods_user_ordering as o")
            ->join("merchants as m","o.merchant_id","=","m.id")
            ->select(['m.name','o.foods_id','m.logo_img','o.prices','o.remark','o.dinnertime','o.people','o.id','o.merchant_id','o.order_sn','o.status'])
            ->where($where)
            ->offset($pages)
            ->limit($num)
            ->get();
        foreach ($data as $key => $value) {
            $foods=json_decode($value->foods_id,1);
//            var_dump($foods);exit();
            foreach ($foods as $k=>$v){
                $data[$key]->foods[$k]['id']=$v['id'];
                $data[$key]->foods[$k]['num']=$v['num'];
                $information=DB::table('foods_information')
                    ->select(['name','price'])
                    ->where('id',$v['id'])
                    ->first();
                $data[$key]->foods[$k]['name']=$information->name ?? '';
                $data[$key]->foods[$k]['price']=$information->price ?? '';
            }
        }
        if($data){
            return $this->rejson(200,"查询成功",$data);
        }else{
            return $this->rejson(201,"查询失败");
        }
    }
    /**
     * @api {post} /api/gourmet/order_details 饭店订单详情
     * @apiName order_details
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} id 订单id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     * {
     *        "code":"200"
     *            "data":{
     *                    "id":"订单id"
     *                    "order_sn":"订单编号",
     *                    "orderingtime":"下单时间",
     *                    "people":"用餐人数",
     *                    "prices":"总金额",
     *                    "dinnertime":"预约到店时间",
     *                    "method":"支付方式",
     *                    "remark":"备注",
     *                    "integral":"积分",
     *                    "pay_money":"支付总金额",
     *                    "name":"饭店名称",
     *                    "logo_img":"商家logo图",
     *                    "status":"订单状态",
     *                    "foods":[
     *                            {
     *                              "id":"菜品id",
     *                              "name":"菜品名称",
     *                              "price":"菜品价格",
     *                              "num":"数量"
     *                            }
     *                            ]
     *                   }
     * }
     */
    public function order_details(){
        $all=\request()->all();
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
        $data=DB::table("foods_user_ordering as o")
            ->join("merchants as m","o.merchant_id","=","m.id")
            ->select('o.order_sn','o.people','o.prices','o.dinnertime','o.method','o.remark','o.integral','o.pay_money','o.orderingtime','o.foods_id','o.id','o.status','m.name','m.logo_img')
            ->where('o.id',$all['id'])
            ->first();

            $foods=json_decode($data->foods_id,1);
            foreach ($foods as $k=>$v){
                $data->foods[$k]['id']=$v['id'];
                $data->foods[$k]['num']=$v['num'];
                $information=DB::table('foods_information')
                    ->select(['name','price'])
                    ->where('id',$v['id'])
                    ->first();
                $data->foods[$k]['name']=$information->name ?? '';
                $data->foods[$k]['price']=$information->price ?? '';
            }

        if($data){
            return $this->rejson(200,"查询成功",$data);
        }else{
            return $this->rejson(201,"查询失败");
        }
    }
     /**
     * @api {post} /api/gourmet/refund 饭店退款
     * @apiName refund
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  order_sn 订单编号
     * @apiParam {array}  refund_msg 退款备注
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"申请成功"
     *     }
     */
    public function refund(){
        $all=request()->all();
        if (empty($all['refund_msg'])||empty($all['order_sn'])) {
           return $this->rejson(201,'缺少参数');
        }
        $re=Db::table('foods_user_ordering')->where(['order_sn'=>$all['order_sn'],'status'=>20])->select('id')->first();
        if (empty($re)) {
            return $this->rejson(201,'订单编号错误');
        }
        $data['status']=60;
        $data['refund_msg']=$all['refund_msg'];
        DB::beginTransaction(); //开启事务
        $res=Db::table('foods_user_ordering')->where('order_sn',$all['order_sn'])->update($data);
        $ress=Db::table('orders')->where('order_sn',$all['order_sn'])->update(array('status'=>60));
        if ($res&&$ress) {
            DB::commit();
            return $this->rejson(200,'申请成功');
        }else{
            DB::rollback();
            return $this->rejson(201,'申请失败');
        }
    }

    /**
     * @api {post} /api/gourmet/addcomment 添加饭店评论
     * @apiName addcomment
     * @apiGroup gourmet
     * @apiParam {string} uid 用户id（必填）
     * @apiParam {string} token 用户验证（必填）
     * @apiParam {string} order_id 订单号（必填）
     * @apiParam {string} merchants_id 商户id（必填）
     * @apiParam {string} content 评价内容（非必填）
     * @apiParam {string} stars 评价星级（必填）
     * @apiParam {string} image 商品图片（非必填）
     * @apiParam {string} dianzhan 是否点赞(0未点赞 1点赞)
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": "",
     *     }
     */
    public function addcomment(){
        $all=request()->all();
        if (!isset($all['uid']) ||
            !isset($all['stars']) ||
            !isset($all['order_id']) ||
            !isset($all['merchants_id']) ){
            return $this->rejson(201,'缺少参数');
        }
        if(!empty($all['image'])){
            $image = json_encode($all['image']);
        }else{
            $image = '';
        }
        if(!empty($all['content'])){
            $content = $all['content'];
        }else{
            $content = '此用户没有评论任何内容';
        }
        $data = [
            'user_id' => $all['uid'],
            'order_id' => $all['order_id'],
            
            'merchants_id' => $all['merchants_id'],
            'content' => $content,
            'stars' => $all['stars'],
            'image' => $image,
            'created_at' => date('Y-m-d H:i:s'),
            'type' => 3,
        ];

        if(empty($all['dianzhan']==1)){
            $da['user_id']=$all['uid'];
            $da['pid']=$all['id'];
            $da['created_at']=date('Y-m-d H:i:s',time());
            $re=Db::table('fabulous')->insert($da);
            $res=DB::table('merchants')->where('id',$all['id'])->increment('praise_num');

        }
        $status['status']=40;
        $re=DB::table('orders')->where('order_sn',$all['order_id'])->update($status);
        $res=DB::table('foods_user_ordering')->where('order_sn',$all['order_id'])->update($status);
        $i = DB::table('order_commnets') -> insert($data);
        if($i){
            return $this->rejson(200,'添加成功');
        }else{
            return $this->rejson(201,'添加失败');
        }
    }

}