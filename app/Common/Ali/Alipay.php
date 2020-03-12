<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/5
 * Time: 13:00
 */

namespace App\Common\Ali;

use Omnipay\Alipay\AopAppGateway;
use Omnipay\Alipay\Responses\{AopCompletePurchaseResponse,
    AopCompleteRefundResponse,
    AopTradeAppPayResponse,
    AopTradeCancelResponse,
    AopTradeCloseResponse,
    AopTradeQueryResponse,
    AopTradeRefundQueryResponse,
    AopTradeRefundResponse
};
use Omnipay\Common\Exception\InvalidRequestException;
use Omnipay\Common\Message\ResponseInterface;
use Omnipay\Omnipay;

/**
 * 默认创建 App 支付网关，如需创建其它网关，使用 resetGateway($gateway) 指定网关后再进行链式调用
 *
 * Class Alipay
 *
 * @package App\Common\Ali\Pay
 */
class Alipay {
    /** @var AopAppGateway $gateway */
    private $gateway;
    protected static $alipay;

    public function __construct() {
        $this->gateway = Omnipay::create('Alipay_AopApp');
        $this->gateway->setSignType('RSA2');
        $this->gateway->setAppId('2019030463414995');
        $this->gateway->setAlipayRootCert(storage_path('app/ali_cert/alipayRootCert.crt'));
        $this->gateway->setAlipayPublicCert(storage_path('app/ali_cert/alipayCertPublicKey_RSA2.crt'));
        $this->gateway->setPrivateKey(file_get_contents(storage_path('app/ali_cert/appPrivateKey')));
        $this->gateway->setAppCert(storage_path('app/ali_cert/appCertPublicKey_2019030463414995.crt'));
        $this->gateway->setEncryptKey('pKTAunEMjwfJoxedQrHnjA==');
        $this->gateway->setNotifyUrl('http://andou.zhuosongkj.com/index.php/api/common/alipay_notify'); // 通知回调地址
    }

    public static function getInstance() {
        if (!self::$alipay) {
            self::$alipay = new self();
        }
        return self::$alipay;
    }

    /**
     * 创建订单
     *
     * @param string           $trade_no 商户订单号，最长 64 位
     * @param string           $subject 订单标题
     * @param string           $body 交易或商品描述
     * @param int|float|string $amount 订单金额，单位为元
     * @param string           $time_expire 超时时间，格式 2016-12-31 10:05
     * @param int              $goods_type 商品类型，默认 1 为实物，0 为虚拟类
     *
     * @return bool|string
     */
    public function createOrder($trade_no, $subject, $body, $amount, $time_expire, $goods_type = 1) {
        $request = $this->gateway
            ->purchase()
            ->setBizContent([
                'out_trade_no' => $trade_no,
                'subject' => $subject,
                'body' => $body,
                'total_amount' => $amount,
                'goods_type' => $goods_type,
                'time_expire' => $time_expire,
                'product_code' => 'QUICK_MSECURITY_PAY',
            ]);

        $response = $request->send();
        /** @var AopTradeAppPayResponse $response */
        if ($response->isSuccessful()) {
            return $response->getOrderString();
        }
        return false;
    }

    /**
     * 订单关闭
     *
     * @param string $trade_no 商户订单号，最长 64 位
     * @param string $trans_no 支付宝流水号
     *
     * @return bool|AopTradeCloseResponse|ResponseInterface
     */
    public function closeOrder($trade_no = null, $trans_no = null) {
        $request = $this->gateway
            ->close()
            ->setBizContent([
                'out_trade_no' => $trade_no,
                'trade_no' => $trans_no
            ]);

        $response = $request->send();
        /** @var AopTradeCloseResponse $response */
        if ($response->isSuccessful()) {
            return $response;
        }
        return false;
    }

    /**
     * 撤销订单（支付交易返回失败或支付系统超时时使用）
     *
     * @param string $trade_no 商户订单号，最长 64 位
     * @param string $trans_no 支付宝流水号
     *
     * @return bool|AopTradeCancelResponse|ResponseInterface
     */
    public function cancelOrder($trade_no = null, $trans_no = null) {
        $request = $this->gateway
            ->cancel()
            ->setBizContent([
                'out_trade_no' => $trade_no,
                'trade_no' => $trans_no
            ]);

        $response = $request->send();
        /** @var AopTradeCancelResponse $response */
        if ($response->isSuccessful()) {
            return $response;
        }
        return false;
    }

    /**
     * 订单退款
     *
     * @param string|double $amount 退款金额
     * @param string        $trade_no 商户订单号，最长 64 位
     * @param string        $trans_no 支付宝流水号
     * @param string        $refund_reason 退款原因
     * @param array         $goods_detail 商品详情
     *                      [
     *                          'goods_id' => 商品编号,
     *                          'goods_name' => 商品名称,
     *                          'quantity' => 商品数量,
     *                          'price' => 商品单价，元,
     *                          'goods_category' => 商品类目,
     *                          'body' => 商品描述
     *                      ]
     * @param string        $out_request_no 退款请求标识，部分退款时此参数必传
     *
     * @return bool|AopTradeRefundResponse|ResponseInterface
     */
    public function refundOrder($amount, $trade_no = null, $trans_no = null,
                                $refund_reason = '', $goods_detail = [], $out_request_no = null) {
        $request = $this->gateway
            ->refund()
            ->setBizContent([
                    'refund_amount' => $amount,
                    'out_trade_no' => $trade_no,
                    'trade_no' => $trans_no,
                    'refund_reason' => $refund_reason,
                    'goods_detail' => $goods_detail,
                    'out_request_no' => $out_request_no
                ]
            );

        $response = $request->send();
        /** @var AopTradeRefundResponse $response */
        if ($response->isSuccessful()) {
            return $response;
        }
        return false;
    }

    /**
     * 订单查询
     *
     * @param string $trade_no 商户订单号，最长 64 位
     * @param string $trans_no 支付宝流水号
     *
     * @return bool|AopTradeQueryResponse|ResponseInterface
     */
    public function queryOrder($trade_no = null, $trans_no = null) {
        $request = $this->gateway->query()->setBizContent(['out_trade_no' => $trade_no, 'trade_no' => $trans_no,]);

        $response = $request->send();
        /** @var AopTradeQueryResponse $response */
        if ($response->isSuccessful()) {
            return $response;
        }
        return false;
    }

    /**
     * 退款查询
     *
     * @param string $trade_no 商户订单号，最长 64 位
     * @param string $trans_no 支付宝流水号
     * @param string $out_request_no 退款请求标识，如果创建退款时未传入可为空
     *
     * @return bool|AopTradeRefundQueryResponse|ResponseInterface
     */
    public function refundQuery($trade_no = null, $trans_no = null, $out_request_no = null) {
        $out_request_no = $out_request_no ?: $trade_no; // 退款请求标识不存在则等同于商户订单号
        $request = $this->gateway
            ->refundQuery()
            ->setBizContent([
                'out_trade_no' => $trade_no,
                'trade_no' => $trans_no,
                'out_request_no' => $out_request_no
            ]);

        $response = $request->send();
        /** @var AopTradeRefundQueryResponse $response */
        if ($response->isSuccessful()) {
            return $response;
        }
        return false;
    }

    /**
     * @param array  $params
     * @param string $type
     *
     * @return bool|int|AopCompleteRefundResponse
     * @throws InvalidRequestException
     */
    public function notify(array $params, $type) {
        switch ($type) {
            case 'pay':
                /** @var AopCompletePurchaseResponse $response */
                $response = $this->gateway->completePurchase()->setParams($params)->send();
                if ($response->isSuccessful()) {
                    return $response->isPaid();
                }
                break;
            case 'refund':
                /** @var AopCompleteRefundResponse $response */
                $response = $this->gateway->completeRefund()->setParams($params)->send();
                if ($response->isSuccessful()) {
                    return $response;
                }
                break;
        }
        return -1;
    }
}
