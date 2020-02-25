<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
use Endroid\QrCode\QrCode;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * 返回json数据
     */
    
    private $wechat_config = [

        'appid'     => 'wxa2ea994d7f5b42e9',

        'appsecret'     => '9219c2f1d1a844e10ac492e922fd9966',

    ];
    public $star=[
        ['id'=>1,'name'=>'经济型'],
        ['id'=>3,'name'=>'舒适三星'],
        ['id'=>4,'name'=>'高档四星'],
        ['id'=>5,'name'=>'豪华五星']
    ];
    public $price_range=[
        ['start'=>0,'end'=>100,'id'=>1,'name'=>"0-100"],
        ['start'=>100,'end'=>200,'id'=>2,'name'=>"100-200"],
        ['start'=>200,'end'=>300,'id'=>3,'name'=>"200-300"],
        ['start'=>300,'end'=>400,'id'=>4,'name'=>"300-400"],
        ['start'=>500,'end'=>600,'id'=>5,'name'=>"400-500"],
        ['start'=>500,'end'=>'','id'=>6,'name'=>"500+"],
    ];
    public function rejson($code = 0, $msg = '', $data = '')
    {
        $response = [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
        $res=json_encode($response);
        exit(str_replace('null', '""', $res));
        // exit();
    }
    /**发送短信验证
     * [sendmessage description]
     * @param  [type] $code      [验证码]
     * @param  [type] $telephone [手机号]
     * @return [type]            [description]
     */
    public function sendmessage($code, $telephone)
    {
        $phone=$telephone;//要发送短信的手机号码    
        $content = "您的验证码是：{$code}，如非本人操作，请忽略此短信。";
        $sms_name='huhao';//短信平台帐号

        $sms_pwd='huhao18696536505';//短信平台密码
        $user=$sms_name;//短信平台帐号
        $pass=md5("$sms_pwd");//短信平台密码
        $smsapi="http://api.smsbao.com/";
        $sendurl=$smsapi."sms?u=".$user."&p=".$pass."&m=".$phone."&c=".urlencode($content);
        $result=file_get_contents($sendurl);
        if($result != 0){
            return false;
        }else{
            Redis::setex($telephone,'30000',$code);
            return true;
        }
    }
    /**地址查询
     * [districts description]
     * @return [type] [description]
     */
    public function districts(){
        $provincelist=Db::table('districts')->select('name','id','pid')->where('deep',0)->get();
        $citylist =Db::table('districts')->select('name','id','pid')->where('deep',1)->get();
        $arealist =Db::table('districts')->select('name','id','pid')->where('deep',2)->get();
        $provinceArray = [];
        $cityArray = [];
        $areaArray = [];
        foreach($arealist as $area)
        {
            $areaArray[$area->pid][] = $area;
        }
        foreach($citylist as $city)
        {
            $city->areas = isset($areaArray[$city->id]) ? $areaArray[$city->id] : null;
            $cityArray[$city->pid][] = $city;
        }
        foreach($provincelist as $province)
        {
            $province->cities = isset($cityArray[$province->id]) ? $cityArray[$province->id] : null;
            $provinceArray[]= $province;
        }
        return $provinceArray;
    }
    /**生成随机字符串
     * [suiji description]
     * @param  boolean $bool [description]
     * @return [type]        [description]
     */
    function suiji($bool=false){
        $suiji = "";
        if($bool){
            $c= "123456789";
            for($i = 0;$i<12;$i++){
                $c = str_shuffle($c);
                $suiji .= substr($c, 0,1);
            }
        }else{
            $c= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789";
            $suiji .= date("Ymd");
            for($i = 0;$i<6;$i++){
                $c = str_shuffle($c);
                $suiji .= substr($c, 0,1);
            }
            $suiji .= date("His");
        }
        return $suiji;
    }
    function code(){
        $c= "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz123456789";
        $suiji = '';
        for($i = 0;$i<6;$i++){
            $c = str_shuffle($c);
            $suiji .= substr($c, 0,1);
        }
        $re=DB::table('users')->where('invitation',$suiji)->first();
        if (!empty($re)) {
            $this->code();
        }
        return $suiji;
    }
    //生成邀请码
    function invitation($id){
            $data['invitation']=$this->code();
            Db::table('users')->where('id',$id)->update($data);
            return $data['invitation'];
    }
    //生成邀请二维码
    function qrcode($id){
        $re=Db::table('users')->where('id',$id)->select('invitation')->first();
        $qrCode = new QrCode();
        $qrCode->setText($re->invitation)
            ->setSize(300)
            ->setForegroundColor(array('r' => 0, 'g' => 0, 'b' => 0, 'a' => 0))
            ->setBackgroundColor(array('r'=>255,'g'=>255,'b'=>255,'a'=>0))
            ->setLabelFontSize(16);
        $filename = 'uploads/qrcode/'.$id.'.png';
        $qrCode->writeFile($filename);
        $data['qrcode']=$filename;
        Db::table('users')->where('id',$id)->update($data);
        return $filename;  
    }
    /**随机生成token
     * [token description]
     * @param  [type] $id [description]
     * @return [type]     [description]
     */
    function token($id){
        $charts = "ABCDEFGHJKLMNPQRSTUVWXYZabcdefghjkmnpqrstuvwxyz0123456789";
        $max = strlen($charts);
        $noncestr = "";
        for($i = 0; $i < 16; $i++){
            $noncestr .= substr($charts,mt_rand(0, $max),1);
        }
        $token['token'] = md5('andou'.$id.md5($noncestr));
        $token['noncestr'] = md5($noncestr);
        return $token;
    }
    /**验证toten
     * [checktoten description]
     * @return [type] [description]
     */
    public function checktoten($id,$token){
        $tokens=md5('andou'.$id.$token);

        $user=Db::table('users')->select('token')->where('id',$id)->first();
        
        if(empty($user->token)){
            return array('code'=>202,'msg'=>'登陆失效');
        }
        if ($tokens!==$user->token) {
            return array('code'=>202,'msg'=>'登陆失效');
        }
    }
    /**添加浏览记录
     * [seemerchant description]
     * @param  [type] $uid  [用户id]
     * @param  [type] $id   [产品id]
     * @param  [type] $type [浏览类型]
     * @return [type]       [description]
     */
    public function seemerchant($uid,$id,$type){
        $data['user_id']=$uid;
        $data['pid']=$id;
        $data['type']=$type;
        $data['created_at']=date('Y-m-d H:i:s',time());
        $datas=DB::table('see_log')
        ->where(['user_id'=>$uid,'pid'=>$id,'type'=>$type])
        ->orderBy('created_at','DESC')
        ->first();
        if (!empty($datas)) {
            $date=date('Y-m-d H:i:s',strtotime("-1 day"));
            if ($datas->created_at>$date) {
                return 0;
            }
        }
        DB::table('see_log')->insert($data);
        return 1;
    }
    /**
     * [freight description]
     * @param  [type] $freight  [商品重量]
     * @param  [type] $num  [购买的重量或者件数]
     * @param  [type] $express_id [计费模板id]
     * @return [type]       [description]
     */
    public function freight($freight,$num,$express_id){
        $res=Db::table('express_model')->where('id',$express_id)->first();
        if (empty($res)) {
            return 0;
        }
        if($res->caculate_method==2){
            if ($num <= $res->num) {
                return $res->basic_price;
            }else{
                return $res->basic_price+($num-$res->num)*$res->unit_price;
            }
        }else if($res->caculate_method==1){
            if ($freight <= $res->num) {
                return $res->basic_price;
            }else{
                return $res->basic_price+($freight-$res->num)*$res->unit_price;
            }
        }else{
            return $res->basic_price;
        }
    }

     /**

     * 获取openid

     * @return string|mixed

     */

    public function getUserAccessUserInfo($code = "")

    {

         

        if(empty($code)){

            $baseUrl = request()->url(true);

            $url = $this->getSingleAuthorizeUrl($baseUrl, "123");                

            Header("Location: $url");

            exit();

        }else{

            $access_token = $this->getSingleAccessToken($code);

            return $this->getUserInfo($access_token);

        }

    }

    /**

     * 微信授权链接

     * @param  string $redirect_uri 要跳转的地址

     * @return [type]               授权链接

     */

    public function getSingleAuthorizeUrl($redirect_url = "",$state = '1') {

        $redirect_url = urlencode($redirect_url);

        return "https://open.weixin.qq.com/connect/oauth2/authorize?appid=" . $this->wechat_config['appid'] . "&redirect_uri=".$redirect_url."&response_type=code&scope=snsapi_userinfo&state={$state}#wechat_redirect"; 

    }

    /**

     * 获取token

     * @return [type] 返回token 

     */

    public function getSingleAccessToken($code) {

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid='.$this->wechat_config['appid'].'&secret='.$this->wechat_config['appsecret'].'&code='.$code.'&grant_type=authorization_code';    

        $access_token = $this->https_request($url);

        return $access_token;     

    }

    

    /**

     * 发送curl请求

     * @param $url string

     * @param return array|mixed

     */

    public function https_request($url)

    {

        $curl = curl_init();

        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        curl_setopt($curl, CURLOPT_URL, $url);

        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

        $AjaxReturn = curl_exec($curl);

        //获取access_token和openid,转换为数组

        $data = json_decode($AjaxReturn,true);

        curl_close($curl);

        return $data;

    }

     /**

     * @explain

     * 通过code获取用户openid以及用户的微信号信息

     * @return array|mixed

     * @remark

     * 获取到用户的openid之后可以判断用户是否有数据，可以直接跳过获取access_token,也可以继续获取access_token

     * access_token每日获取次数是有限制的，access_token有时间限制，可以存储到数据库7200s. 7200s后access_token失效

     **/

    public function getUserInfo($access_token = []){
        // var_dump($access_token);exit;
        if(empty($access_token['access_token'])){

            return [

                'code' => 201,

                'msg' => '微信授权失败', 

                'data' => '', 

            ];

        }

        $userinfo_url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['openid'].'&lang=zh_CN';

        $userinfo_json = $this->https_request($userinfo_url);

     

        //获取用户的基本信息，并将用户的唯一标识保存在session中

        if(!$userinfo_json){

            return [

                'code' => 0,

                'msg' => '获取用户信息失败！', 

            ];

        }

        return $userinfo_json;

    }

    protected function responseJson($code, $message = '', $data = '')
    {
        return response()->json([
            'code' => $code,
            'msg'  => $message,
            'data' => $data
        ]);
    }
}
