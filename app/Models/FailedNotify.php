<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2019-05-13
 * Time: 18:31
 */

namespace App\Models;

class FailedNotify extends BaseModel {

    protected $table = 'log_failed_notify';

    protected static $model;

    public static function call() {
        if (!self::$model) {
            self::$model = new self();
        }
        return self::$model;
    }

    /**
     * 写失败日志
     *
     * @param string $content
     * @param string $source
     * @param string $type
     *
     * @return int
     */
    public function insertLog($content, $source, $type = 'pay') {
        return $this->insertGetId([
            'request' => $content,
            'pay_source' => $source,
            'type' => $type
        ]);
    }

}
