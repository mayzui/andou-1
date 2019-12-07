<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsCate extends Model
{
    public $timestamps = false;
    protected $table = 'goods_cate';

    public function children ()
    {
        return  $this->hasMany('App\Models\GoodsCate','pid','id')->select('id','name','pid');
    }

}
