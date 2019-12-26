<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class CommonController extends Controller
{   
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
        return $this->rejson(200,'查询成功',$data);
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
     * @api {get} /api/common/wxnotify 微信支付回调
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
        // $aa['event']=$values['out_trade_no'];
        // Db('record')->insert($aa);
        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code']=='SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $datas = array('status' => 20, 'pay_way' => 0, 'out_trade_no' => $trade_no,'pay_time'=>date('Y-m-d H:i:s'),time());
            $out_trade_no = $values['out_trade_no'];

            $ress=Db::table('orders')->where(['order_sn' => $out_trade_no, 'status' =>10])->first();
            if ($ress) {
                $re = Db::table('order')->where('order_sn', $out_trade_no)->update($datas);
                
                if ($re && $res) {
                    exit('SUCCESS'); //成功处理后必须输出这个字符串给支付宝
                } else {
                    exit('fail');
                }
            }
            
        } else {
            exit('fail');
        }

    }
}