<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/11
 * Time: 20:06
 */

namespace App\Models;

class Books extends BaseModel {

    protected $table = 'books';

    protected $fillable = [
        'user_id', 'order_sn', 'order_money', 'type', 'address_id', 'out_trade_no', 'pay_money', 'pay_way', 'pay_time',
        'status', 'updated_at'
    ];
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }
}
