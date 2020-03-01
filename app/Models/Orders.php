<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/17 0017
 * Time: 9:14
 */

namespace App\Models;


use Exception;
use Illuminate\Support\Facades\DB;

class Orders extends BaseModel {
    protected $table = 'orders';
    protected $fillable = ['user_id', 'order_sn', 'order_money', 'type', 'created_at'];
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
     * @return bool
     * @throws Exception
     */
    public function createPostOrder($order_data, $post_id, $top_day) {
        DB::beginTransaction();

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
