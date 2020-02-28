<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/28
 * Time: 12:16
 */

namespace App\Models;

class UtilArea extends BaseModel {

    protected $table = 'util_area';

    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }

    public function getChildren($parent_id = 1) {
        return $this->where('parent_id', $parent_id)->get(['id', 'name']);
    }
}
