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
     * @apiParam {string} token 用户token
     * @apiParam {string} merchant_id 商户id
     * @apiSuccessExample 参数返回：
     * {
    "code":"200",
     *           "data":[
     *                  {
     *	                "id":"id",
     *                  "name":"菜品名称",
     *                  "price":"菜品价格",
     *                  "num":"菜品数量",
     *                  "image":"菜品图片"
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
            ->join("merchants as m",'c.merchant_id','=','m.id')
            ->join("foods_information as f","c.foods_id","=","f.id")
            ->join("foods_spec as s","c.spec_id","=","s.id")
            ->select(['f.name','s.price','c.num','c.id','f.image'])
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
     * @apiParam {string} id 购物车id
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
        $data=DB::table("foods_cart")->select('num')->where('id',$all['id'])->first();
        if($all['type'] == 1){
            $res=DB::table('foods_cart')->where('id',$all['id'])->increment('num');
        }else if ($all['type'] == 0 ){
            if($data->num <= 1){
                $res=DB::table('foods_cart')->where(['id'=>$all['id'],'user_id'=>$all['uid']])->delete();
            }else{
                $res=DB::table('foods_cart')->where(['id'=>$all['id'],'user_id'=>$all['uid']])->decrement('num');
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
     * @apiParam {string} foods_id 菜品id
     * @apiParam {string} spec_id 规格id
     * @apiParam {string} merchant_id 商户id
     * @apiParam {string} id 购物车列表id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加成功"
     *     }
     */

    public function reserve()
    {
        $all = request()->all();
        if(empty($all['id']) || empty($all['food_id']) || empty($all['merchant_id']))
        {
            return $this->rejson('201','缺少参数');
        }
        $data = DB::table('merchants')->where('id',$all['merchant_id'])->select('name')->first();
        $data->foods = DB::table("foods_cart")
            ->join('foods_information','foods_cart.foods_id','=','foods_information.id')
            ->where('foods_cart.id',$all['id'])
            ->select(['foods_cart.name','foods_cart.price','foods_cart.num','foods_information.image'])
            ->get();
        $all = 0;
        foreach ($data->foods as $value){
            $all += $value->price;
        }
        $data->all = $all;
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
     * @apiParam {string} orderingtime 下单时间
     * @apiParam {string} prices 订单总金额
     * @apiParam {string} method 支付方式（1微信，2支付宝，3银联）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加成功"
     *     }
     */

    public function timely()
    {
        $all = request()->all();
        if(empty($all['merchant_id']) ||
            empty($all['people']) ||
            empty($all['remark']) ||
            empty($all['prices']) ||
            empty($all['orderingtime']) ||
            empty($all['method'])){
            return $this->rejson('201','缺少参数');
        }
        $res = [
            'user_id'=>$all['uid'],
            'merchant_id'=>$all['merchant_id'],
            'orderingtime'=>$all['orderingtime'],
            'remark'=>$all['remark'],
            'phone'=>'165894685',
            'people'=>$all['people'],
            'prices'=>$all['prices'],
            'order_sn'=>$this->suiji(),
            'method'=>$all['method'],
        ];
        $sNo = $res['order_sn'];
        $data = DB::table('foods_user_ordering')->insert($res);
        if($data){
            if ($all['method']==1) {//微信支付
                $this->wxPay($sNo);
            }else if($all['method']==2){//支付宝支付
                return $this->rejson('201','暂未开通');
            }else if($all['method']==0){//银联支付
                return $this->rejson('201','暂未开通');
            }else{
                return $this->rejson('201','暂未开通');
            }
            return $this->rejson('200','下单成功');
        }else{
            return $this->rejson('201','添加失败');
        }
    }

    public function wxPay($sNo){
        require_once base_path()."/wxpay/lib/WxPay.Api.php";
        require_once base_path()."/wxpay/example/WxPay.NativePay.php";

        if (empty($sNo)) {
            return $this->rejson(201,'参数错误');
        }
        //查找表里是否有此订单
        $orders = Db::table('foods_user_ordering')
            ->where('order_sn',$sNo)
            ->first();
        if (empty($orders)) {
            return $this->rejson(201,'订单不存在');
        }

        $pay_money = 100*($orders->price);

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

}