<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Endroid\QrCode\QrCode;
class CommonController extends Controller
{

    /**
     * @api {post} /api/common/express 快递
     * @apiName express
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
                        {
                        "id"   "快递类型id",
                        "name": "公司名称",
                        }
                    ],
     *       "msg":"查询成功"
     *     }
     */

    public function express()
    {
        $data = DB::table('express')->select('id','name')->get();
        return $this->rejson('200','查询成功',$data);
    }
    /**
     * @api {post} /api/common/qrcode 获取邀请二维码
     * @apiName qrcode
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"查询成功"
     *     }
     */
    public  function qrcode(){
        $img = new QRcode();
        $value = '154545'; //二维码内容 
        $errorCorrectionLevel = 'L';//容错级别 
        $matrixPointSize = 6; // 生成图片大小 
        //生成二维码图片 
        $img->png($value, 'qrcode.png', $errorCorrectionLevel, $matrixPointSize, 2); 
        $QR = 'qrcode.png'; //已经生成的原始二维码图 
        echo '<img src="'.$QR.'">'; 
    }


    /**
     * @api {post} /api/common/pay_ways 支付方式
     * @apiName pay_ways
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                {
                    "id": "支付方式id",
                    "pay_way": "支付方式名字",
                    "logo": "图标"
                }
            ],     
     *       "msg":"查询成功"
     *     }
     */
    public function payWays(){
        $data=Db::table('pay_ways')->select('id','pay_way','logo')->where('status',1)->get();
        return $this->rejson(200,'查询成功',json_decode($data,JSON_UNESCAPED_UNICODE));
    }
    /**
     * @api {post} /api/common/merchant_type 商户类型配置
     * @apiName merchant_type
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                {
                    "id": "id",
                    "type_name": "商户类型名字"
                }
            ],     
     *       "msg":"查询成功"
     *     }
     */
    public function merchantType(){
        $data['merchant_type']=Db::table('merchant_type')
        ->select('id','type_name')
        ->where('status',1)
        ->orderBy('sort','ASC')
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/common/district 获取所有地址列表
     * @apiName district
     * @apiGroup common 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":[
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
                ],
     *       "msg":"查询成功"
     *     }
     */
    public function district(){
        $data=Redis::get('districts');
        if ($data) {
            $data=json_decode($data,1);
        }else{
            $data=$this->districts();
            Redis::set('districts',json_encode($data,1));
        }
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/common/wxnotify 微信商城支付回调
     * @apiName wxnotify
     * @apiGroup common 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":"",
     *       "msg":"查询成功"
     *     }
     */
    
    public function wxnotify() {
        
        $xml=file_get_contents('php://input');
        //$xml = PHP_VERSION <= 5.6 ? $GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $aa['val']=$xml;
        $values['trade_status']=$values['trade_status']??'';
        $values['return_code']=$values['return_code']??'';
        Db::table('record')->insert($aa);

        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code']=='SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $total=$values['total_fee']/100;
            $datas = array('status' => 20, 'pay_way' => 1, 'out_trade_no' => $trade_no,'pay_time'=>date('Y-m-d H:i:s',time()),'pay_money'=>$total);
            $out_trade_no = $values['out_trade_no'];
            // echo $out_trade_no;
            $ress=Db::table('orders')->where(['order_sn' =>$out_trade_no, 'status' =>10])->first();
            // var_dump($ress);exit();
            if (!empty($ress)) {
                $re = Db::table('orders')->where('order_sn', $out_trade_no)->update($datas);
                $res = Db::table('order_goods')->where('order_id', $out_trade_no)->update($datas);
                if ($re && $res) {
                    $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';  
                    return $str;
                    
                } else {
                    return 'fail1';
                }
            }else{
                 return 'fail2';
            }
            
        } else {
            return 'fail3';
        }

    }
     /**
     * @api {get} /api/common/wxnotifyhotel 微信支付酒店回调
     * @apiName wxnotifyhotel
     * @apiGroup common 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":"",
     *       "msg":"查询成功"
     *     }
     */
    public function wxnotifyhotel(){
        $xml=file_get_contents('php://input');
        //$xml = PHP_VERSION <= 5.6 ? $GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $aa['val']=$values['out_trade_no'];
        $values['trade_status']=$values['trade_status']??'';
        $values['return_code']=$values['return_code']??'';
        Db::table('record')->insert($aa);
        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code']=='SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $total=$values['total_fee'];
            $datas = array('status' => 20, 'pay_way' => 1, 'out_trade_no' => $trade_no,'pay_time'=>date('Y-m-d H:i:s',time()),'pay_money'=>$total);
            $out_trade_no = $values['out_trade_no'];

            $ress=Db::table('orders')->where(['order_sn' => $out_trade_no, 'status' =>10])->first();
            if (!empty($ress)) {
                $re = Db::table('orders')->where('order_sn', $out_trade_no)->update($datas);
                $res = Db::table('books')->where('book_sn', $out_trade_no)->update($datas);
                if ($re && $res) {
                    $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';  
                    echo $str;
                    
                } else {
                    exit('fail');
                }
            }
            
        } else {
            exit('fail');
        }
    }

    /**
     * @api {post} /api/common/wxRecharge 微信充值支付回调
     * @apiName wxRecharge
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":"",
     *       "msg":"查询成功"
     *     }
     */

    public function wxRecharge() {

        $xml=file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $values['trade_status']=$values['trade_status']??'';
        $values['return_code']=$values['return_code']??'';
        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code']=='SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $total=$values['total_fee']/100;
            $datas = array('status' => 1, 'method' => 1, 'trade_no' => $trade_no,'price'=>$total);
            $out_trade_no = $values['out_trade_no'];
            return 1;
            $ress=Db::table('recharge')->where(['order_sn' =>$out_trade_no])->where('status',0)->first();
            if (!empty($ress)) {
                $re = Db::table('recharge')->where('order_sn', $out_trade_no)->update($datas);
                $arr = [
                    'user_id'=>$ress->user_id,
                    'price'=>$ress->price,
                    'describe'=>'充值',
                    'create_time' => date('Y-m-d H:i:s'),
                    'type_id' => 2,
                    'state' => 1,
                    'phone' => $ress->phone,
                    'method' => 1,
                ];
                $res = DB::table('user_logs')->insert($arr);
                $pic = DB::table('users')->where('id',$ress->user_id)->select('money')->first();
                $money = [
                    'money'=>$pic->money+$arr['price']
                ];
                $r = DB::table('users')->where('id',$ress->user_id)->update($money);
                if ($re && $res && $r) {
                    $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                    return $str;

                } else {
                    return 'fail1';
                }
            }else{
                return 'fail2';
            }

        } else {
            return 'fail3';
        }

    }

    /**
     * @api {post} /api/common/gourmet 微信充值支付回调
     * @apiName gourmet
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":"",
     *       "msg":"查询成功"
     *     }
     */

    public function gourmet() {

        $xml=file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $values['trade_status']=$values['trade_status']??'';
        $values['return_code']=$values['return_code']??'';
        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code']=='SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $total=$values['total_fee']/100;
            $datas = array('status' => 1, 'method' => 1, 'trade_no' => $trade_no,'price'=>$total);
            $out_trade_no = $values['out_trade_no'];
            $ress=Db::table('recharge')->where(['order_sn' =>$out_trade_no])->where('status',0)->first();
            if (!empty($ress)) {
                $re = Db::table('recharge')->where('order_sn', $out_trade_no)->update($datas);
                $arr = [
                    'user_id'=>$ress->user_id,
                    'price'=>$ress->price,
                    'describe'=>'充值',
                    'create_time' => date('Y-m-d H:i:s'),
                    'type_id' => 2,
                    'state' => 1,
                    'phone' => $ress->phone,
                    'method' => 1,
                ];
                $res = DB::table('user_logs')->insert($arr);
                $pic = DB::table('users')->where('id',$ress->user_id)->select('money')->first();
//                return var_dump($pic);
                $money = [
                    'money'=>$pic->money+$arr['price']
                ];
                $r = DB::table('users')->where('id',$ress->user_id)->update($money);
//                var_dump($r);die;
                if ($re && $res && $r) {
                    $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                    return $str;

                } else {
                    return 'fail1';
                }
            }else{
                return 'fail2';
            }

        } else {
            return 'fail3';
        }

    }

}