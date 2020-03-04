<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/3
 * Time: 20:07
 */

namespace App\Models;

class OrderCancel extends BaseModel {

    protected $table = 'order_cancel';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }
}
