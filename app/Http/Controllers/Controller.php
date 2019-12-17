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
        exit(json_encode($response));
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

}
