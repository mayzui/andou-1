<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class UsersController extends Controller
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
     * @api {post} /api/users/merchant_record 商家浏览记录
     * @apiName merchant_record
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 查询页码(不是必传 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                {
                    "id": "商户id",
                    "created_at": "创建时间",
                    "stars_all": "星级",
                    "praise_num":"点赞数量",
                    "logo_img":"商家图片",
                    "name":"商家名字",
                    "address":"商家地址",
                    "tel":"商家电话",
                    "merchant_type_id":"商户类型id"
                }
            ],     
     *       "msg":"查询成功"
     *     }
     */
    public function merchantRecord(){
        $all=request()->all();
        $num=10;
        $start=0;
        if (!empty($all['page'])) {
            $page=$all['page'];
            $start=$num*($page-1);
        }
        $data=Db::table('see_log as c')
        ->join('merchants as m','m.id','=','c.pid')
        ->where(['c.user_id'=>$all['uid'],'c.type'=>2])
        ->select('m.id','m.address','m.merchant_type_id','m.tel','m.stars_all','m.praise_num','m.name','m.logo_img')
        ->orderBy('c.id',"DESC")
        ->offset($start)
        ->limit($num)
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/users/invitations 邀请码 邀请二维码
     * @apiName invitations
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                {
                    "id": "用户id",
                    "name": "用户名字",
                    "avator": "用户头像",
                    "invitation": "邀请码",
                    "qrcode": "邀请二维码" 
                }
            ],     
     *       "msg":"查询成功"
     *     }
     */
    public function invitations(){
        $all=request()->all();
        $id=$all['uid'];
        $data=Db::table('users')->where('id',$id)->select('id','name','avator','invitation','qrcode')->first();
        if ($data->invitation=='0') {
            $data->invitation=$this->invitation($data->id);
        }
        if (empty($data->qrcode)) {
            $data->qrcode=$this->qrcode($data->id);
        }
        return $this->rejson(200,'查询成功',$data);
    }
     /**
     * @api {post} /api/users/binding 绑定上级
     * @apiName binding
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} code 上级邀请码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",     
     *       "msg":"查询成功"
     *     }
     */
    public function binding(){
        $all=request()->all();
        if (empty($all['code'])) {
            return $this->rejson(201,'缺少参数');
        }
        $ress=Db::table('users')->where('id',$all['uid'])->select('guide_puser_id')->first();
        if ($ress->guide_puser_id>0) {
           return $this->rejson(201,'你已经存在上级用户');
        }
        $re=Db::table('users')->where('invitation',$all['code'])->select('id')->first();
        if (empty($re)) {
            return $this->rejson(201,'邀请码不存在');
        }
        $data['guide_puser_id']=$re->id;
        $res=Db::table('users')->where('id',$all['uid'])->update($data);
        if ($res) {
           return $this->rejson(200,'绑定成功');
        }else{
           return $this->rejson(201,'绑定失败'); 
        }
    }
    /**
     * @api {post} /api/users/collection 商品收藏记录
     * @apiName collection
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 查询页码(不是必传 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                {
                    "id": "商品id",
                    "created_at": "创建时间",
                    "price": "商品价格",
                    "img":"商品图片",
                    "name":"商品名字"
                }
            ],     
     *       "msg":"查询成功"
     *     }
     */
    public function collection(){
        $all=request()->all();
        $num=10;
        $start=0;
        if (!empty($all['page'])) {
            $page=$all['page'];
            $start=$num*($page-1);
        }
        $data=Db::table('collection as c')
        ->join('goods as m','m.id','=','c.pid')
        ->where(['c.user_id'=>$all['uid'],'c.type'=>1])
        ->select('m.id','m.price','m.img','m.name')
        ->orderBy('c.id',"DESC")
        ->offset($start)
        ->limit($num)
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/users/follow 商家关注记录
     * @apiName follow
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 查询页码(不是必传 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                {
                    "id": "商户id",
                    "created_at": "创建时间",
                    "stars_all": "星级",
                    "praise_num":"点赞数量",
                    "logo_img":"商家图片",
                    "name":"商家名字",
                    "address":"商家地址",
                    "merchant_type_id":"商家类型id",
                    "tel":"商家电话"
                }
            ],     
     *       "msg":"查询成功"
     *     }
     */
    public function follow(){
        $all=request()->all();
        $num=10;
        $start=0;
        if (!empty($all['page'])) {
            $page=$all['page'];
            $start=$num*($page-1);
        }
        $data=Db::table('collection as c')
        ->join('merchants as m','m.id','=','c.pid')
        ->where(['c.user_id'=>$all['uid'],'c.type'=>3])
        ->select('m.id','m.address','m.merchant_type_id','m.tel','m.stars_all','m.praise_num','m.name','m.logo_img')
        ->orderBy('c.id',"DESC")
        ->offset($start)
        ->limit($num)
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/users/fabulous 给商家点赞
     * @apiName fabulous
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} id 商家id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",     
     *       "msg":"查询成功"
     *     }
     */
    public function fabulous(){
        $all=request()->all();
        if (empty($all['id'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data['user_id']=$all['uid'];
        $data['pid']=$all['id'];
        $data['created_at']=date('Y-m-d H:i:s',time());
        $datas=Db::table('fabulous')->where(['user_id'=>$all['uid'],'pid'=>$all['id']])->first();
    
        if (empty($datas)) {
            $re=Db::table('fabulous')->insert($data);
            $res=DB::table('merchants')->where('id',$all['id'])->increment('praise_num');
            return $this->rejson(200,'点赞成功');
        }else{
            return $this->rejson(201,'不能重复点赞');
        }   
    }
    /**
     * @api {post} /api/users/envelopes 红包金额查询
     * @apiName envelopes
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *               "value":"领取金额"
     *        },     
     *       "msg":"查询成功"
     *     }
     */
    public function envelopes(){
        $all=request()->all();
        $re=Db::table('user_logs')->where(['user_id'=>$all['uid'],'type_id'=>'4'])->first();
        if (!empty($re)) {
            return $this->rejson(201,'该用户已经领取过新用户红包');
        }
        $data=Db::table('config')->select('value')->where('key','envelopes')->first();
        return $this->rejson(200,'获取成功',$data);
    }
    /**
     * @api {post} /api/users/envelopes_add 新用户领取红包
     * @apiName envelopes_add
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",     
     *       "msg":"领取成功"
     *     }
     */
    public function envelopesAdd(){
        $all=request()->all();
        $re=Db::table('user_logs')->where(['user_id'=>$all['uid'],'type_id'=>'4'])->first();
        if (!empty($re)) {
            return $this->rejson(201,'该用户已经领取过新用户红包');
        }
        $data['price']=Db::table('config')->where('key','envelopes')
        ->select('value')
        ->first()
        ->value ?? '';
        if ($data['price'] == '') {
            return $this->rejson(201,'系统错误');
        }
        $data['user_id']=$all['uid'];
        $data['describe']='新用户红包领取';
        $data['create_time']=date('Y-m-d H:i:s',time());
        $data['type_id']=4;
        $data['state']=1;
        $data['is_del']=0;
        DB::beginTransaction(); //开启事务
        $re=DB::table('user_logs')->insert($data);
        $res=DB::table('users')->where('id',$all['uid'])->increment('money',$data['price']);
        if ($res&&$re) {
            DB::commit();
            return $this->rejson(200,'领取成功');
        }else{
            DB::rollback();
            return $this->rejson(201,'领取失败');
        }
    }

    /**
     * @api {post} /api/users/upmodel 修改手机号
     * @apiName upmodel
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} phone 手机号码
     * @apiParam {string} verify 验证码
     * 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"修改成功"
     *     }
     */
    public function upmodel(){
       $all=request()->all();
       if (empty($all['phone']) || empty($all['verify']) ||empty($all['uid'])) {
            return $this->rejson(201,'参数错误');
       }
       $data['mobile']=$all['phone'];
       if ($all['verify'] != Redis::get($all['phone'])) {
                return $this->rejson(201,'验证码错误');
        }
       $re=Db::table('users')->where('id',$all['uid'])->update($data);
       return $this->rejson('200',"修改手机号成功");
    }
    /**
     * @api {post} /api/users/vip_recharge vip购买
     * @apiName vip_recharge
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} pay_id 支付方式id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"修改成功"
     *     }
     */
    public function vipRecharge(){
        $all=request()->all();
        $price=Db::table('config')->where('key','vipRecharge')->first()->value ?? 0;
        if (!$price) {
            return $this->rejson(201,'后台未设置会员开通价格');
        }
        $data['user_id']=$all['uid'];
        $data['price']=$price;
        $data['created_at']=$data['updated_at']=date('Y-m-d H:i:s',time());
        $data['order_sn']=$this->suiji();
        $re=Db::table('vip_recharge')->insert($data);
        if ($all['pay_id']==1) {//微信支付
            $this->wxpay($data['order_sn']);
        }else if($all['pay_id']==2){//支付宝支付
            return $this->rejson(201,'暂未开通');
        }else if($all['pay_id']==3){//银联支付
            return $this->rejson(201,'暂未开通');
        }else if($all['pay_id']==5){//其他支付
            return $this->rejson(201,'暂未开通');
        }else{
            return $this->rejson(201,'暂未开通');
        }
    }
    /**
     * @api {post} /api/users/vip_rote 会员规者和金额
     * @apiName vip_rote
     * @apiGroup users
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"修改成功"
     *     }
     */
    public function vipRote(){
        $all=request()->all();
        $data['price']=Db::table('config')->where('key','vipRecharge')->first()->value ?? 0;
        if (!$data['price']) {
            return $this->rejson(201,'后台未设置会员开通价格');
        }
        $data['vip_rote']=Db::table('vip_equity')->first()->content ?? 0;
        return $this->rejson(200,'获取成功',$data);
    }
    public function wxPay($sNo){
        require_once base_path()."/wxpay/lib/WxPay.Api.php";
        require_once base_path()."/wxpay/example/WxPay.NativePay.php";
        $orders = Db::table('vip_recharge')
        ->where('order_sn',$sNo)
        ->first();
        if (empty($orders)) {
            return $this->rejson(201,'订单不存在');
        }
        $pay_money = 100*$orders->price;
        $input = new \WxPayUnifiedOrder();
        $input->SetBody("安抖商城平台");
        $input->SetOut_trade_no($sNo);
        // $input->SetTotal_fee($pay_money);
        $input->SetTotal_fee(1);
        $input->SetNotify_url("http://andou.zhuosongkj.com/api/common/viprecharge");
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