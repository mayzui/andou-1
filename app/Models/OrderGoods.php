<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/6
 * Time: 16:31
 */

namespace App\Models;

class OrderGoods extends BaseModel {

    protected $table = 'order_goods';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }
}
