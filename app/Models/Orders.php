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
    protected $fillable = ['user_id', 'order_sn', 'order_money', 'type', 'status', 'updated_at'];
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
                if ($user->save() &&
                    Post::getInstance()->find($post_id)->update(['is_show' => 1, 'top_day' => $top_day])) {
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
}
