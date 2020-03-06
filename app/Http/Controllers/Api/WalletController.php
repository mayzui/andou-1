<?php

namespace App\Http\Controllers\Api;

use App\Common\WeChat\WeChatPay;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use function request;

class WalletController extends Controller {
    public function __construct() {
        $all = request()->all();
        $token = request()->header('token') ?? '';
        if ($token != '') {
            $all['token'] = $token;
        }
        if (empty($all['uid']) || empty($all['token'])) {
            return $this->rejson(202, '登陆失效');
        }
        $check = $this->checktoten($all['uid'], $all['token']);
        if ($check['code'] == 202) {
            return $this->rejson($check['code'], $check['msg']);
        }
    }

    /**
     * @api {post} /api/wallet/index 余额明细
     * @apiName index
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 分页
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
     * "log":[
     * "superior_id": "上级id",
     * "price": "流动金额",
     * "describe": "流动描述",
     * "create_time": "流动时间",
     * "state": "1获得 2消费"
     * ],
     * "money": "总金额"
     *      }
     *     }
     */
    public function index() {
        $all = request()->all();
        $num = 10;
        // 根据获取的id
        if (empty($all['uid'])) {
            return $this->rejson(201, '请输入用户id');
        }
        if (!isset($all['page'])) {
            $all['page'] = 1;
        }
        // 根据用户id 查询资金流动表
        $data['log'] = DB::table('user_logs')
            ->where('user_id', $all['uid'])
            ->whereIn('type_id', [2, 4])
            ->where('user_logs.is_del', 0)
            ->select(['user_logs.superior_id', 'user_logs.price', 'user_logs.describe', 'user_logs.create_time', 'user_logs.state'])
            ->orderByDesc('create_time')
            ->forPage($all['page'], 10)
            ->get();
        $data['money'] = DB::table('users')
                ->where('id', $all['uid'])
                ->select('money')
                ->first()
                ->money ?? '';
        if (!empty($data)) {
            return $this->rejson(200, '查询成功', $data);
        } else {
            return $this->rejson(201, '未查询到该id');
        }
    }

    /**
     * @api {post} /api/wallet/cash 提现明细
     * @apiName cash
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 分页
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
     * "log":[
     * "superior_id": "上级id",
     * "price": "流动金额",
     * "describe": "流动描述",
     * "create_time": "流动时间",
     * "state": "1获得 2消费"
     * ],
     * "money": "总金额"
     *      }
     *     }
     */
    public function cash() {
        $all = request()->all();
        $num = 10;
        // 根据获取的id
        if (empty($all['uid'])) {
            return $this->rejson(201, '请输入用户id');
        }
        if (isset($all['page'])) {
            $pages = ($all['page'] - 1) * $num;
        } else {
            $pages = 0;
        }
        // 根据用户id 查询资金流动表
        $data['log'] = DB::table('user_logs')
            ->where('user_id', $all['uid'])
            ->where('type_id', 3)
            ->where('user_logs.is_del', 0)
            ->select(['user_logs.superior_id', 'user_logs.price', 'user_logs.describe', 'user_logs.create_time', 'user_logs.state'])
            ->orderByDesc('create_time')
            ->offset($pages)
            ->limit($num)
            ->get();
        $data['money'] = DB::table('users')
                ->where('id', $all['uid'])
                ->select('money')
                ->first()
                ->money ?? '';
        if (!empty($data)) {
            return $this->rejson(200, '查询成功', $data);
        } else {
            return $this->rejson(201, '未查询到该id');
        }
    }

    /**
     * @api {post} /api/wallet/integral 积分明细
     * @apiName integral
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 分页
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
     * "log":[
     * "superior_id": "上级id",
     * "price": "流动金额",
     * "describe": "流动描述",
     * "create_time": "流动时间",
     * "state": "1获得 2消费"
     * ],
     * "integral": "总积分"
     *      }
     *     }
     */
    public function integral() {
        $all = request()->all();
        $num = 10;
        // 根据获取的id
        if (empty($all['uid'])) {
            return $this->rejson(201, '请输入用户id');
        }
        if (isset($all['page'])) {
            $pages = ($all['page'] - 1) * $num;
        } else {
            $pages = 0;
        }
        // 根据用户id 查询资金流动表
        $data['log'] = DB::table('user_logs')
            ->where('user_id', $all['uid'])
            ->where('type_id', 1)
            ->where('user_logs.is_del', 0)
            ->select(['user_logs.superior_id', 'user_logs.price', 'user_logs.describe', 'user_logs.create_time', 'user_logs.state'])
            ->offset($pages)
            ->limit($num)
            ->get();
        $data['integral'] = DB::table('users')
                ->where('id', $all['uid'])
                ->select('integral')
                ->first()
                ->integral ?? '';
        if (!empty($data)) {
            return $this->rejson(200, '查询成功', $data);
        } else {
            return $this->rejson(201, '未查询到该id');
        }
    }

    /**
     * @api {post} /api/wallet/cash_withdrawal 余额提现
     * @apiName cash_withdrawal
     * @apiGroup wallet
     * @apiParam {string} uid 用户id （必填）
     * @apiParam {string} token 验证登陆 （必填）
     * @apiParam {string} money 提现金额 （必填）
     * @apiParam {string} phone 联系方式 （必填）
     * @apiParam {string} num 提现账号 （必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": ""
     */
    public function cash_withdrawal() {
        $all = request()->all();
        // 根据获取的id
        if (empty($all['money']) || empty($all['phone']) || empty($all['num'])) {
            return $this->rejson(201, '缺少必填项');
        }
        $data = DB::table('users')
            ->where('id', $all['uid'])
            ->select('money')
            ->first();
        $yue = $data->money - $all['money'];
        if ($yue < 0) {
            return $this->rejson(201, '当前余额不足');
        }
        DB::beginTransaction();
        try {
            // 当前用户减少金额
            DB::table('users')->where('id', $all['uid'])->update(['money' => $yue]);
            // 提现成功，添加提现明细
            $add = [
                'user_id' => $all['uid'],
                'price' => $all['money'],
                'describe' => "用户提现",
                'create_time' => date('Y-m-d H:i:s'),
                'type_id' => 3,
                'state' => 2,
                'phone' => $all['phone'],
                'card' => $all['num']
            ];
            $i = DB::table('user_logs')->insert($add);

            if ($i) {
                DB::commit();
                return $this->rejson(200, '提现成功');
            } else {
                DB::rollBack();
                return $this->rejson(201, '未查询到该id');
            }
        } catch (Exception $e) {
            DB::rollBack();
            return $this->rejson(201, '提现失败');
        }

    }

    /**
     * @api {post} /api/wallet/rechar 余额充值明细
     * @apiName rechar
     * @apiGroup wallet
     * @apiParam {string} uid 用户id（必填）
     * @apiParam {string} token 验证登陆 （必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
     * 'money' "总金额",
     * 'mobile' "联系方式"
     *          }
     */

    public function rechar() {
        $all = request()->all();
        $data = DB::table('users')
            ->where('id', $all['uid'])
            ->select('money', 'mobile')
            ->first();
        if ($data) {
            return $this->rejson('200', '查询成功', $data);
        } else {
            return $this->rejson('201', '未找到用户');
        }
    }

    /**
     * @api {post} /api/wallet/payWays 支付方式
     * @apiName payWays
     * @apiGroup wallet
     * @apiParam {string} uid 用户id（必填）
     * @apiParam {string} token 验证登陆 （必填）
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
        $data = DB::table('pay_ways')->select('id', 'pay_way', 'logo')
            ->having('id', '!=', 4)
            ->where('status', 1)->get();
        return $this->rejson(200, '查询成功', json_decode($data, JSON_UNESCAPED_UNICODE));
    }

    /**
     * @api {post} /api/wallet/recharge 余额充值
     * @apiName recharge
     * @apiGroup wallet
     * @apiParam {string} uid 用户id（必填）
     * @apiParam {string} token 验证登陆 （必填）
     * @apiParam {string} money 充值金额 （必填）
     * @apiParam {string} mobile 联系方式 （必填）
     * @apiParam {string} method 充值的方式 0银联 1微信 2支付宝 （必填）
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": ""
     */
    public function recharge() {
        $all = request()->all();
        // 根据获取的id
        if (empty($all['money']) || empty($all['mobile']) || empty($all['method'])) {
            return $this->rejson(201, '缺少必填项');
        }
        try {
            DB::beginTransaction();
            $add = [
                'order_sn' => app('Snowflake\Snowflake')->next(),
                'user_id' => $all['uid'],
                'price' => $all['money'],
                'create_time' => Carbon::now()->toDateTimeString(),
                'phone' => $all['mobile'],
                'method' => $all['method'],
            ];
            $i = DB::table('recharge')->insert($add);
            if ($i) {
                DB::commit();
                if ($all['method'] == 1) {//微信支付
                    return $this->responseJson(200, 'OK', $this->wxPay($add['order_sn'], $add['price']));
                } else if ($all['method'] == 2) {//支付宝支付
                    return $this->rejson(201, '暂未开通');
                } else if ($all['method'] == 0) {//银联支付
                    return $this->rejson(201, '暂未开通');
                } else {
                    return $this->rejson(201, '暂未开通');
                }
            }
            DB::rollBack();
            return $this->rejson(201, '未查询到该id');
        } catch (Exception $e) {
            DB::rollBack();
            return $this->rejson(201, '充值失败');
        }
    }

    public function wxPay($sNo, $money) {
        return WeChatPay::getInstance()->copy()
            ->resetGateway('WechatPay_App', 'http://andou.zhuosongkj.com/api/common/wxRecharge')
            ->createOrder(
                $sNo,
                $money * 100,
                '安抖本地生活-消费',
                '余额充值',
                request()->ip(),
                Carbon::now()->addHour()->format('YmdHis')
            );
    }

    /**
     * @api {post} /api/wallet/personal 个人中心
     * @apiName personal
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
     * "id":'用户id',
     * "name":'用户名称',
     * "avator":'用户头像',
     * "grade":'用户vip等级',
     * "status":'是否是会员 0普通用户 1超级会员',
     * "money":'用户总金额',
     * "integral":'用户积分',
     * "collect":'商品收藏数',
     * "focus":'关注店铺数',
     * "record":'浏览记录数',
     * "goodordernum":"商城订单数",
     * "foodsordernum":"饭店订单数",
     * "booksordernum":"酒店订单数",
     * }
     */
    public function personal() {
        $all = request()->all();
        // 根据获取的id
        if (empty($all['uid'])) {
            return $this->rejson(201, '请输入用户id');
        }
        $data = DB::table('users')
            ->where('id', $all['uid'])
            ->select(['id', 'name', 'avator', 'money', 'integral'])
            ->first();
        $grade = DB::table('vip')
            ->where('user_id', $all['uid'])
            ->where('is_del', 0)
            ->select('grade')
            ->first();
        $data->collect = DB::table('collection')->where('user_id', $all['uid'])->where('type', 1)->count();
        $data->focus = DB::table('collection')->where('user_id', $all['uid'])->where('type', 3)->count();
        $data->record = DB::table('see_log')->where('user_id', $all['uid'])->where('type', 2)->count();
        $data->goodordernum = DB::table('order_goods')->where('user_id', $all['uid'])->whereIn('status', ['10', '50'])->count();
        $data->booksordernum = DB::table('books')->where('user_id', $all['uid'])->whereIn('status', ['20'])->count();
        $data->foodsordernum = DB::table('foods_user_ordering')->where('user_id', $all['uid'])->whereIn('status', ['20'])->count();
        if (empty($grade)) {
            $data->status = 0;
            $data->grade = 0;
        } else {
            $data->status = 1;
            $data->grade = $grade->grade;
        }
        if (!empty($data)) {
            return $this->rejson(200, '查询成功', $data);
        } else {
            return $this->rejson(201, '未查询到该id');
        }
    }

    // 8fcc685decce987fbfdb713d7514928f
}
