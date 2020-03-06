<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/5
 * Time: 20:38
 */

namespace App\Models;

class UserLog extends BaseModel {

    protected $table = 'user_logs';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }
}
