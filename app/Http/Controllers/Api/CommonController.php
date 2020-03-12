<?php

namespace App\Http\Controllers\Api;

use App\Common\Ali\Alipay;
use App\Http\Controllers\Controller;
use App\Models\FailedNotify;
use App\Models\Orders;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Omnipay\Common\Exception\InvalidRequestException;

class CommonController extends Controller {

    /**
     * @api {post} /api/common/express 快递
     * @apiName express
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
     * {
     * "id"   "快递类型id",
     * "name": "公司名称",
     * }
     * ],
     *       "msg":"查询成功"
     *     }
     */

    public function express() {
        $data = DB::table('express')->select('id', 'name')->get();
        return $this->rejson('200', '查询成功', $data);
    }


    /**
     * @api {post} /api/common/pay_ways 支付方式
     * @apiName pay_ways
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
     * {
     * "id": "支付方式id",
     * "pay_way": "支付方式名字",
     * "logo": "图标"
     * }
     * ],
     *       "msg":"查询成功"
     *     }
     */
    public function payWays() {
        $data = DB::table('pay_ways')->select('id', 'pay_way', 'logo')->where('status', 1)->get();
        return $this->rejson(200, '查询成功', json_decode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @api {post} /api/common/merchant_type 商户类型配置
     * @apiName merchant_type
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
     * {
     * "id": "id",
     * "type_name": "商户类型名字"
     * }
     * ],
     *       "msg":"查询成功"
     *     }
     */
    public function merchantType() {
        $data['merchant_type'] = DB::table('merchant_type')
            ->select('id', 'type_name')
            ->where('status', 1)
            ->orderBy('sort', 'ASC')
            ->get();
        return $this->rejson(200, '查询成功', $data);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     *
     * @api {post} /api/common/district 获取所有地址列表
     * @apiName district
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":[
     * {
     * "name": "北京",
     * "id": 11,
     * "pid": 0,
     * "cities": [
     * {
     * "name": "北京",
     * "id": 1101,
     * "pid": 11,
     * "areas": [
     * {
     * "name": "东城",
     * "id": 110101,
     * "pid": 1101
     * }
     * ]
     * }
     * ]
     * }
     * ],
     *       "msg":"查询成功"
     *     }
     */
    public function district(Request $request) {
        $id = $request->get('id', 1);
        return $this->responseJson(200, 'OK', $this->districts($id));
    }

    /**
     * @api {post} /api/common/treaty 协议
     * @apiName treaty
     * @apiGroup common
     * @apiParam {string} type 协议分类 1隐私协议 其他后面再加
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": [
     *           {
     * "id": "协议id",
     * "name": "协议名称",
     * "content": "协议内容",
     * "create_time":"创建时间"
     * "update_time":"修改时间"
     * }
     *       ],
     *       "msg":"查询成功"
     *     }
     */
    public function treaty() {
        $all = request()->all();
        if (empty($all['type'])) {
            return $this->rejson(201, '参数错误');
        }
        $data = DB::table('protocol')
            ->where('type', $all['type'])->where('status', 1)
            ->orderBy('id', 'DESC')
            ->select('id', 'name', 'content', 'create_time', 'update_time')
            ->first();
        return $this->rejson(200, '查询成功', $data);
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

        $xml = file_get_contents('php://input');
        //$xml = PHP_VERSION <= 5.6 ? $GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $aa['val'] = $xml;
        $values['trade_status'] = $values['trade_status'] ?? '';
        $values['return_code'] = $values['return_code'] ?? '';
        DB::table('record')->insert($aa);

        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code'] == 'SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $total = $values['total_fee'] / 100;
            $time = Carbon::now()->toDateTimeString();
            $datas = ['status' => 20, 'pay_way' => 1, 'out_trade_no' => $trade_no, 'pay_time' => $time, 'pay_money' => $total];
            $out_trade_no = $values['out_trade_no'];
            // echo $out_trade_no;
            $ress = Orders::getInstance()->where(['order_sn' => $out_trade_no, 'status' => 10])->first();
            // var_dump($ress);exit();
            if (!empty($ress)) {
                // 贴吧订单
                if ($ress->type = 4) {
                    if (Orders::getInstance()->paidPostOrder($ress->id, 1, $total, $trade_no)) {
                        return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                    }
                    return 'fail';
                }

                $re = $ress->update($datas);
                $res = DB::table('order_goods')->where('order_id', $out_trade_no)->update($datas);
                if ($re && $res) {
                    return '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';

                } else {
                    return 'fail1';
                }
            } else {
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
    public function wxnotifyhotel() {
        $xml = file_get_contents('php://input');
        //$xml = PHP_VERSION <= 5.6 ? $GLOBALS['HTTP_RAW_POST_DATA']:file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $aa['val'] = $values['out_trade_no'];
        $values['trade_status'] = $values['trade_status'] ?? '';
        $values['return_code'] = $values['return_code'] ?? '';
        DB::table('record')->insert($aa);
        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code'] == 'SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $total = $values['total_fee'] / 100;
            $datas = ['status' => 20, 'pay_way' => 1, 'out_trade_no' => $trade_no, 'pay_time' => date('Y-m-d H:i:s', time()), 'pay_money' => $total];
            $out_trade_no = $values['out_trade_no'];

            $ress = DB::table('orders')->where(['order_sn' => $out_trade_no, 'status' => 10])->first();
            if (!empty($ress)) {
                $re = DB::table('orders')->where('order_sn', $out_trade_no)->update($datas);
                $res = DB::table('books')->where('book_sn', $out_trade_no)->update($datas);
                if ($re && $res) {
                    $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
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

        $xml = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $values['trade_status'] = $values['trade_status'] ?? '';
        $values['return_code'] = $values['return_code'] ?? '';
        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code'] == 'SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $total = $values['total_fee'] / 100;
            $datas = ['status' => 1, 'method' => 1, 'trade_no' => $trade_no, 'price' => $total];
            $out_trade_no = $values['out_trade_no'];

            $ress = DB::table('recharge')->where(['order_sn' => $out_trade_no])->where('status', 0)->first();
            if (!empty($ress)) {
                $re = DB::table('recharge')->where('order_sn', $out_trade_no)->update($datas);
                $arr = [
                    'user_id' => $ress->user_id,
                    'price' => $ress->price,
                    'describe' => '充值',
                    'create_time' => date('Y-m-d H:i:s'),
                    'type_id' => 2,
                    'state' => 1,
                    'phone' => $ress->phone,
                    'method' => 1,
                ];
                $res = DB::table('user_logs')->insert($arr);
                $pic = DB::table('users')->where('id', $ress->user_id)->select('money')->first();
                $money = [
                    'money' => $pic->money + $arr['price']
                ];
                $r = DB::table('users')->where('id', $ress->user_id)->update($money);
                if ($re && $res && $r) {
                    $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                    return $str;

                } else {
                    return 'fail1';
                }
            } else {
                return 'fail2';
            }

        } else {
            return 'fail3';
        }

    }

    /**
     * @api {post} /api/common/gourmet 饭店预定回调
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

        $xml = file_get_contents('php://input');
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $values['trade_status'] = $values['trade_status'] ?? '';
        $values['return_code'] = $values['return_code'] ?? '';
        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code'] == 'SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $total = $values['total_fee'] / 100;
            $datas = ['status' => 20, 'pay_way' => 1, 'out_trade_no' => $trade_no, 'pay_time' => date('Y-m-d H:i:s', time()), 'pay_money' => $total];
            $datass = ['status' => 20, 'method' => 1, 'out_trade_no' => $trade_no, 'pay_time' => date('Y-m-d H:i:s', time()), 'pay_money' => $total];
            $out_trade_no = $values['out_trade_no'];

            $ress = DB::table('orders')->where(['order_sn' => $out_trade_no, 'status' => 10])->first();
            if (!empty($ress)) {
                $re = DB::table('orders')->where('order_sn', $out_trade_no)->update($datas);
                $res = DB::table('foods_user_ordering')->where('order_sn', $out_trade_no)->update($datass);
                if ($re && $res) {
                    $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                    echo $str;

                } else {
                    exit('fail');
                }
            } else {
                exit('fail2');
            }
        } else {
            return 'fail3';
        }

    }

    /**
     * @api {post} /api/common/viprecharge vip充值回调
     * @apiName viprecharge
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":"",
     *       "msg":"查询成功"
     *     }
     */

    public function viprecharge() {
        $xml = file_get_contents('php://input');

        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        $values['trade_status'] = $values['trade_status'] ?? '';
        $values['return_code'] = $values['return_code'] ?? '';
        if ($values['trade_status'] == 'TRADE_SUCCESS' || $values['trade_status'] == 'TRADE_FINISHED' || $values['return_code'] == 'SUCCESS') {
            //这里根据项目需求来写你的操作 如更新订单状态等信息 更新成功返回'success'即可
            $trade_no = $values['transaction_id'];
            $total = $values['total_fee'] / 100;
            $out_trade_no = $values['out_trade_no'];
            $ress = DB::table('vip_recharge')->where(['order_sn' => $out_trade_no])->where('status', 0)->first();
            if (!empty($ress)) {
                $arr = [
                    'user_id' => $ress->user_id,
                    'grade' => 1,
                    'is_del' => 0
                ];
                $res = DB::table('vip')->insert($arr);
                $r = DB::table('vip_recharge')->where(['order_sn' => $out_trade_no])->update(['status' => 1, 'out_trade_no' => $out_trade_no]);
                if ($res && $r) {
                    $str = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
                    return $str;
                } else {
                    return 'fail1';
                }
            } else {
                return 'fail2';
            }

        } else {
            return 'fail3';
        }
    }

    /**
     * @param Request $request
     *
     * @return string
     */
    public function alipayNotify(Request $request) {
        $reqAppId = $request->post('app_id');
        $reqSellerId = $request->post('seller_id');
        $appId = config('ali.pay.app_id');
        $sellerId = config('ali.pay.seller_id');

        if ($reqAppId === $appId && $reqSellerId === $sellerId) {
            if ($request->post('receipt_amount')) {
                $type = 'pay';
            } else if ($request->post('refund_fee')) {
                $type = 'refund';
            } else {
                $ret = FailedNotify::call()
                    ->insertLog(json_encode($request->all(), JSON_UNESCAPED_UNICODE), 'alipay', 'other');
                // 写数据库失败日志则改写日志
                if (!$ret) {
                    Log::warning('Received Unknown Alipay Notify: ', $request->server());
                    return 'fail';
                }
                // 返回成功报文，交由相关人员手动处理
                return 'success';
            }

            $params = $request->post();

            try {
                $result = Alipay::getInstance()->notify($params, $type);
                if ($result === -1) {
                    return 'fail';
                }
            } catch (InvalidRequestException $e) {
                Log::error('Received Alipay Notify With Exception: ' . $e->getMessage(), $request->server());
                Log::error('Received Alipay Notify With Exception: ' . $e->getMessage(), $request->all());
                return 'fail';
            }

            if ($type === 'refund') {
                $refundData = $result->getData();
                $result = $result->isRefunded();
            } else {
                $refundData = null;
            }

            if ($result) {
                $trans_no = $request->get('trade_no');
                switch ($type) {
                    case 'pay':
                        $trade_no = $request->get('out_trade_no');
                        $pay_amount = $request->get('buyer_pay_amount');
                        $ret = $this->successPaid($trade_no, $trans_no, $pay_amount);
                        break;
                    case 'refund':
                        $refund_no = $refundData['out_biz_no'];
                        // TODO: do refunded process
                        $ret = $this->successRefund();
                        break;
                    default:
                        $ret = false;
                }

                if ($ret === true) {
                    return 'success';
                }
                // 写数据库失败日志
                $ret = FailedNotify::call()->insertLog(json_encode($request->all()), 'alipay', $type);
                if (!$ret) {
                    // 写日志
                    Log::warning('Alipay notify failed: ', $request->all());
                }
            }
        }
        return 'fail';
    }

    /**
     * @param string $trade_no 商户订单号
     * @param string $transaction_no 交易流水号
     * @param double $pay_amount 支付总额
     *
     * @return bool
     */
    private function successPaid($trade_no, $transaction_no, $pay_amount) {
        $order = Orders::getInstance()->where('order_sn', $trade_no)->first();
        if ($order->status != 10) {
            // paid
            return true;
        }

        // verify pay amount
        if ($order->order_money == $pay_amount) {
            switch ($order->type) {
                case 1:
                    // shop
                    return Orders::getInstance()->paidShopOrder($order->id, 2, $pay_amount, $transaction_no);
                case 2:
                    // hotel
                    return Orders::getInstance()->paidHotelOrder($order->id, 2, $pay_amount, $transaction_no);
                case 3:
                    // restaurant
                    return Orders::getInstance()->paidRestaurantOrder($order->id, 2, $pay_amount, $transaction_no);
                case 4:
                    // tieba
                    return Orders::getInstance()->paidPostOrder($order->id, 2, $pay_amount, $transaction_no);
            }
        }
        return false;
    }

    private function successRefund() {
        return false;
    }
}
