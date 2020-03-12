<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/11
 * Time: 20:13
 */

namespace App\Models;

class FoodsUserOrdering extends BaseModel {
    protected $table = 'foods_user_ordering';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }
}
