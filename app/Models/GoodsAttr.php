<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsAttr extends Model
{
    public $timestamps = false;
    protected $table = 'goods_attr';

    public function attrValue ()
    {
        return  $this->hasMany('App\Models\GoodsAttrValue','goods_attr_id','id');
    }
}
