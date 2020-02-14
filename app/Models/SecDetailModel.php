<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecDetailModel extends Model
{
    protected $table = 'seckill_details';

    protected $guarded = [];

    public $timestamps = false;

    public function toGoods()
    {
        return $this->belongsTo(GoodsModel::class, 'goods_id');
    }
}
