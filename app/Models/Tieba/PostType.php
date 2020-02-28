<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 21:21
 */

namespace App\Models\Tieba;

use App\Models\BaseModel;

class PostType extends BaseModel {

    protected $table = 'tieba_post_type';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }

    public function getType() {
        return $this->where('status', 1)->get(['id', 'type_name']);
    }
}
