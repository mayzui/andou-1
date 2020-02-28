<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 15:48
 */

namespace App\Models\Tieba;

use App\Models\BaseModel;

class PostImage extends BaseModel {

    protected $table = 'tieba_post_image';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }
}
