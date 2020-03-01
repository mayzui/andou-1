<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/3/1
 * Time: 14:29
 */

namespace App\Models\Information;

use App\Models\BaseModel;

class InformationType extends BaseModel {

    protected $table = 'information_type';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }
}
