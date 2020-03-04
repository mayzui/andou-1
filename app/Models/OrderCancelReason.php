<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/4
 * Time: 14:05
 */

namespace App\Models;

class OrderCancelReason extends BaseModel {

    protected $table = 'order_cancel_reason';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }
}
