<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/29
 * Time: 11:25
 */

namespace App\Models;

use Illuminate\Support\Facades\DB;

class UserAddress extends BaseModel {

    protected $table = 'user_address';
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }

    public function getAddrIds($last_area_id) {
        $ids = DB::selectOne('SELECT fn_get_addr_ids(?) AS ids', [$last_area_id])->ids;
        $ids = explode(',', $ids);
        if ($ids && is_array($ids)) {
            return array_reverse($ids);
        }
        return false;
    }
}
