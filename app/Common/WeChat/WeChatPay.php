<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/1
 * Time: 02:40
 */

namespace App\Common\WeChat;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Omnipay;
use Omnipay\WechatPay\AppGateway;
use Omnipay\WechatPay\Message\{CloseOrderResponse,
    CompleteRefundResponse,
    PromotionTransferResponse,
    QueryOrderResponse,
    QueryRefundResponse,
    RefundOrderResponse
};

/**
 * 默认创建 App 支付网关，如需创建其它网关，使用 resetGateway($gateway) 指定网关后再进行链式调用
 *
 * Class WeChatPay
 *
 * @package App\Common\WeChat
 */
class WeChatPay {
    /** @var AppGateway $gateway */
    private $gateway;
    protected static $wechatPay;

    private $appId;
    private $mchId;
    private $apiKey;
    private $certPath;
    private $keyPath;
    private $notify_url;

    public function __construct() {
        $appId = 'wxa2ea994d7f5b42e9';
        $mchId = '1527302001';
        $apiKey = '941404112888b260f94824b01574da2d';

        $this->appId = $appId;
        $this->mchId = $mchId;
        $this->apiKey = $apiKey;
        $this->certPath = base_path('wxpay/lib/cert/apiclient_cert.pem');
        $this->keyPath = base_path('wxpay/lib/cert/apiclient_key.pem');
        $this->notify_url = 'http://andou.zhuosongkj.com/api/common/wxnotify';

        $this->gateway = Omnipay::create('WechatPay_App');
        $this->gateway->setAppId($appId);
        $this->gateway->setMchId($mchId);
        $this->gateway->setApiKey($apiKey);
        $this->gateway->setNotifyUrl($this->notify_url); // 通知回调地址
        $this->gateway->setCertPath($this->certPath);
        $this->gateway->setKeyPath($this->keyPath);
    }

    public static function getInstance() {
        if (!self::$wechatPay) {
            self::$wechatPay = new self();
        }
        return self::$wechatPay;
    }

    public function copy() {
        return clone $this;
    }

    public function clone() {
        return clone $this;
    }

    /**
     * 重置网关参数，默认重置为手机网站支付
     *
     * @param string $gateway 需创建的网关，可选值如下
     *                          WechatPay 通用
     *                          WechatPay_App App 支付
     *                          WechatPay_Native 原生扫码支付
     *                          WechatPay_Js 网页、公众号、小程序支付
     *                          WechatPay_Pos 刷卡支付
     *                          WechatPay_Mweb H5 支付
     * @param string $appId
     * @param string $mchId
     * @param string $apiKey
     *
     * @return WeChatPay
     */
    public function resetGateway($gateway, $appId = null, $mchId = null, $apiKey = null) {
        $this->gateway = Omnipay::create($gateway);
        $this->gateway->setAppId($appId ?: $this->appId);
        $this->gateway->setMchId($mchId ?: $this->mchId);
        $this->gateway->setApiKey($apiKey ?: $this->apiKey);
        $this->gateway->setCertPath($this->certPath);
        $this->gateway->setKeyPath($this->keyPath);
        $this->gateway->setNotifyUrl($this->notify_url); // 通知回调地址

        return $this;
    }

    /**
     * @param string     $trade_no 商户订单号，最长 32 位
     * @param string|int $amount 订单金额，单位为分，不能带小数点
     * @param string     $body 交易描述，最长 128 位，以下为描述规则
     *                         APP：APP 名称-概述，如“天天爱消除-游戏充值”
     *                         微信浏览器：商家名称-类目，如“腾讯-游戏”
     *                         PC 网站/H5 支付：网页主页 title-概述，如“腾讯充值中心-QQ会员充值”
     * @param string     $detail 详情描述
     * @param string     $spbill_create_ip 请求者 IP
     * @param string     $time_expire 超时时间，14 位字符串，格式 20091227091010
     * @param string     $trade_type 交易类型，默认为 APP
     *                               APP App支付
     *                               JSAPI JSAPI支付（或小程序支付）
     *                               NATIVE Native支付
     *                               MWEB H5支付
     * @param string     $open_id 用户 OpenID，仅 JSAPI
     * @param string     $attach 附加内容，最长 127 位
     *
     * @return array|bool|string
     */
    public function createOrder($trade_no, $amount, $body, $detail, $spbill_create_ip, $time_expire,
                                $trade_type = 'APP', $open_id = null, $attach = null) {
        $request = $this->gateway
            ->purchase([
                'out_trade_no' => $trade_no,
                'total_fee' => $amount,
                'body' => $body,
                'detail' => $detail,
                'spbill_create_ip' => $spbill_create_ip,
                'time_expire' => $time_expire,
                'trade_type' => $trade_type,
                'openid' => $open_id,
                'attach' => $attach,
                'fee_type' => 'CNY',
            ]);
        $response = $request->send();
        switch ($trade_type) {
            case 'APP':
                return $response->getAppOrderData() ?: false;
            case 'JSAPI':
                return $response->getJsOrderData() ?: false;
            case 'NATIVE':
                return $response->getCodeUrl() ?: false;
            case 'MWEB':
                return $response->getMwebUrl() ?: false;
        }
        return false;
    }

    /**
     * 关闭订单
     *
     * @param string $trade_no 商户订单号，最长 32 位
     *
     * @return bool
     */
    public function closeOrder($trade_no) {
        $request = $this->gateway->close(['out_trade_no' => $trade_no]);
        $response = $request->send();

        /** @var CloseOrderResponse $response */
        if ($response->isSuccessful()) {
            $data = $response->getData();
            // 响应成功或已关闭时皆判定为该订单关闭成功
            return isset($data['result_code']) &&
                ($data['result_code'] === 'SUCCESS' || $data['result_code'] === 'ORDERCLOSED');
        }
        return false;
    }

    /**
     * 查询订单
     *
     * @param string $trade_no 商户订单号，最长 32 位
     * @param string $transaction_id 微信订单号
     *
     * @return bool|array
     */
    public function queryOrder($trade_no = null, $transaction_id = null) {
        $request = $this->gateway
            ->query([
                'out_trade_no' => $trade_no,
                'transaction_id' => $transaction_id
            ]);

        $response = $request->send();
        /** @var QueryOrderResponse $response */
        if ($response->isSuccessful()) {
            return $response->getData();
        }
        return false;
    }

    /**
     * 订单退款，单号条件 2 选 1
     *
     * @param string $refund_no 商户退款单号，最长 32 位
     * @param int    $amount 订单金额，单位为分，不能带小数点
     * @param int    $refund 退款金额，单位为分，不能带小数点
     * @param string $trade_no 商户订单号，最长 32 位
     * @param string $transaction_id 微信订单号
     * @param string $refund_desc 退款原因，最长 80 位
     *
     * @return bool|ResponseInterface|RefundOrderResponse
     */
    public function refundOrder($refund_no, $amount, $refund,
                                $trade_no = null, $transaction_id = null, $refund_desc = '') {
        $request = $this->gateway->refund([
            'out_refund_no' => $refund_no,
            'total_fee' => $amount,
            'refund_fee' => $refund,
            'out_trade_no' => $trade_no,
            'transaction_id' => $transaction_id,
            'refund_desc' => $refund_desc,
            'refund_fee_type' => 'CNY'
        ]);

        $response = $request->send();
        if ($response->isSuccessful()) {
            return $response;
        }
        return false;
    }

    /**
     * 退款查询，条件 4 选一
     *
     * @param string $refund_no 商户退款单号，最长 32 位
     * @param string $trade_no 商户订单号，最长 32 位
     * @param string $refund_id 微信退款单号
     * @param string $transaction_id 微信订单号
     * @param int    $offset 偏移量，当部分退款次数超过 10 次时可使用，表示返回的查询结果从这个偏移量开始取记录
     *
     * @return bool|QueryOrderResponse|QueryRefundResponse
     */
    public function refundQuery($refund_no = null, $trade_no = null, $refund_id = null, $transaction_id = null,
                                $offset = null) {
        $request = $this->gateway
            ->queryRefund([
                'out_refund_no' => $refund_no,
                'out_trade_no' => $trade_no,
                'refund_id' => $refund_id,
                'transaction_id' => $transaction_id,
                'offset' => $offset
            ]);

        $response = $request->send();
        /** @var QueryRefundResponse $response */
        if ($response->isSuccessful()) {
            return $response;
        }
        return false;
    }

    /**
     * @param $params
     *
     * @return PromotionTransferResponse
     */
    public function transfer($params) {
        $request = $this->gateway->transfer($params);

        return $request->send();
    }

    /**
     * 微信回调通知
     *
     * @param string $content 通知内容。微信发送的 XML 文本
     * @param string $type 类型，默认 pay 支付，可选值 pay|refund（退款）
     *
     * @return bool|int|CompleteRefundResponse 请求成功且签名匹配返回 bool 型，请求失败或签名不匹配返回 -1
     */
    public function notify($content, $type = 'pay') {
        switch ($type) {
            case 'pay':
                $response = $this->gateway->completePurchase(['request_params' => $content])->send();
                if ($response->isSuccessful() && $response->isSignMatch()) {
                    return $response->isPaid();
                }
                break;
            case 'refund':
                /** @var CompleteRefundResponse $response */
                $response = $this->gateway->completeRefund(['request_params' => $content])->send();
                if ($response->isSuccessful() && $response->isSignMatch()) {
                    return $response;
                }
                break;
        }
        return -1;
    }
}
