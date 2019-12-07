<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goods extends Model
{

    protected $table = 'goods';

    public function goodsCate ()
    {
        return $this->belongsTo('App\Models\GoodsCate','goods_cate_id','id')->select('id','name');
    }

    public function goodBrands ()
    {
        return $this->belongsTo('App\Models\GoodBrands','goods_brand_id','id')->select('id','name');
    }

}
