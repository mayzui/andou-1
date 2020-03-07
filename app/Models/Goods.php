<?php

namespace App\Models;

class Goods extends BaseModel {

    protected $table = 'goods';
    protected $fillable = ['volume'];
    private static $model;

    public static function getInstance() {
        return self::$model ?: self::$model = new self();
    }

    public function goodsCate() {
        return $this->belongsTo('App\Models\GoodsCate', 'goods_cate_id', 'id')->select('id', 'name');
    }

    public function goodBrands() {
        return $this->belongsTo('App\Models\GoodBrands', 'goods_brand_id', 'id')->select('id', 'name');
    }

}
