<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;
class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
    /**
     * 返回json数据
     */
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
            return array('code'=>201,'msg'=>'登陆失效');
        }
        if ($tokens!=$user->token) {
            return array('code'=>201,'msg'=>'登陆失效');
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
}
