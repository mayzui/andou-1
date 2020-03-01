<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/1
 * Time: 00:43
 */

namespace App\Models;

class OrderPost extends BaseModel {

    protected $table = 'order_post';
    protected $fillable = ['post_id', 'top_day'];

    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }
}
