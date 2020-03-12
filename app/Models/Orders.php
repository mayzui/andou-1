<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/17 0017
 * Time: 9:14
 */

namespace App\Models;


use App\Models\Tieba\Post;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class Orders extends BaseModel {
    protected $table = 'orders';
    protected $fillable = [
        'user_id', 'order_sn', 'order_money', 'type', 'address_id', 'out_trade_no', 'pay_money', 'pay_way', 'pay_time',
        'status', 'updated_at'
    ];
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }

    public function order() {
        return $this->hasOne(OrderPost::class, 'order_id', 'id');
    }

    /**
     * @param array $order_data
     * @param int   $post_id
     * @param int   $top_day
     *
     * @return bool|int
     * @throws Exception
     */
    public function createPostOrder($order_data, $post_id, $top_day) {
        DB::beginTransaction();

        if ($order_data['pay_way'] == 4) {
            $user = Users::find($order_data['user_id']);
            if ($user->money >= $order_data['order_money']) {
                $user->money -= $order_data['order_money'];
                $now = Carbon::now()->toDateTimeString();
                if ($user->save() && Post::getInstance()->find($post_id)
                        ->update(['is_show' => 1, 'top_day' => $top_day, 'paid_at' => $now, 'updated_at' => $now])) {
                    $order_data['status'] = 20;
                    $order_data['pay_time'] = $order_data['created_at'];
                    $order_data['pay_money'] = $order_data['order_money'];
                    // 写明细
                    if (!UserLog::getInstance()->insert([
                        'user_id' => $order_data['user_id'],
                        'merchant_id' => 0,
                        'describe' => '贴吧置顶服务消费',
                        'create_time' => Carbon::now()->toDateTimeString(),
                        'type_id' => 2,
                        'state' => 2,
                    ])) {
                        DB::rollBack();
                        return false;
                    }
                } else {
                    DB::rollBack();
                    return false;
                }
            } else {
                DB::rollBack();
                return -1;
            }
        }

        $ret = self::create($order_data);
        if ($ret) {
            $ret->order()->create(['post_id' => $post_id, 'top_day' => $top_day]);
            if ($ret) {
                DB::commit();
                return true;
            }
        }

        DB::rollBack();
        return false;
    }

    private function paidUpdateOrder($order_id, $pay_way, $pay_money, $transaction_no) {
        $now = Carbon::now()->toDateTimeString();
        try {
            DB::beginTransaction();
        } catch (Exception $e) {
            return false;
        }

        $ret = $this->where('status', 10)->find($order_id)->update([
            'pay_way' => $pay_way,
            'pay_money' => $pay_money,
            'out_trade_no' => $transaction_no,
            'status' => 20,
            'pay_time' => $now,
            'updated_at' => $now
        ]);

        if ($ret) {
            DB::commit();
            return true;
        }

        DB::rollBack();
        return false;
    }

    /**
     * @param int    $order_id
     * @param int    $pay_way
     * @param double $pay_amount
     * @param string $transaction_no
     *
     * @return bool
     */
    public function paidShopOrder($order_id, $pay_way, $pay_amount, $transaction_no) {
        try {
            DB::beginTransaction();
        } catch (Exception $e) {
            return false;
        }

        if ($this->paidUpdateOrder($order_id, $pay_way, $pay_amount, $transaction_no)) {
            $orderSN = $this->find($order_id, ['order_sn'])->value('order_sn');
            $now = Carbon::now()->toDateTimeString();
            $ret = OrderGoods::getInstance()
                ->where('order_id', $orderSN)
                ->where('status', 10)
                ->update([
                    'pay_way' => $pay_way,
                    'pay_money' => $pay_amount,
                    'out_trade_no' => $transaction_no,
                    'status' => 20,
                    'pay_time' => $now,
                    'updated_at' => $now
                ]);

            if ($ret !== false) {
                DB::commit();
                return true;
            }
        }

        DB::rollBack();
        return false;
    }

    public function paidHotelOrder($order_id, $pay_way, $pay_amount, $transaction_no) {
        try {
            DB::beginTransaction();
        } catch (Exception $e) {
            return false;
        }

        if ($this->paidUpdateOrder($order_id, $pay_way, $pay_amount, $transaction_no)) {
            $orderSN = $this->find($order_id, ['order_sn'])->value('order_sn');
            $now = Carbon::now()->toDateTimeString();
            $ret = FoodsUserOrdering::getInstance()
                ->where('order_sn', $orderSN)
                ->where('status', 10)
                ->update([
                    'pay_way' => $pay_way,
                    'pay_money' => $pay_amount,
                    'out_trade_no' => $transaction_no,
                    'status' => 20,
                    'pay_time' => $now,
                    'updated_at' => $now
                ]);

            if($ret !== false){
                DB::commit();
                return true;
            }
        }

        DB::rollBack();
        return false;
    }

    public function paidRestaurantOrder($order_id, $pay_way, $pay_amount, $transaction_no) {
        try {
            DB::beginTransaction();
        } catch (Exception $e) {
            return false;
        }

        if ($this->paidUpdateOrder($order_id, $pay_way, $pay_amount, $transaction_no)) {
            $orderSN = $this->find($order_id, ['order_sn'])->value('order_sn');
            $now = Carbon::now()->toDateTimeString();
            $ret = Books::getInstance()
                ->where('book_sn', $orderSN)
                ->where('status', 10)
                ->update([
                    'pay_way' => $pay_way,
                    'pay_money' => $pay_amount,
                    'out_trade_no' => $transaction_no,
                    'status' => 20,
                    'pay_time' => $now,
                    'updated_at' => $now
                ]);

            if($ret !== false){
                DB::commit();
                return true;
            }
        }

        DB::rollBack();
        return false;
    }

    /**
     * @param int    $order_id
     * @param int    $pay_way
     * @param double $pay_amount
     * @param string $out_trade_no
     *
     * @return bool
     */
    public function paidPostOrder($order_id, $pay_way, $pay_amount, $out_trade_no) {
        try {
            DB::beginTransaction();
        } catch (Exception $e) {
            return false;
        }

        if ($this->paidUpdateOrder($order_id, $pay_way, $pay_amount, $out_trade_no)) {
            $now = Carbon::now()->toDateTimeString();
            $orderPost = OrderPost::getInstance()->find($order_id);
            if ($orderPost) {
                $post = Post::getInstance()->find($orderPost->post_id);
                if ($post) {
                    $ret = $post->update([
                        'is_show' => 1,
                        'top_day' => $orderPost->top_day,
                        'paid_at' => $now,
                        'updated_at' => $now
                    ]);
                    if ($ret) {
                        DB::commit();
                        return true;
                    }
                }
            }
        }
        DB::rollBack();
        return false;
    }
}
