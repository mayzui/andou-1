<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SecRuleModel extends Model
{
    protected $table = 'seckill_rules';

    protected $guarded = [];

    public function goods()
    {
        return $this->belongsTo(GoodsModel::class, 'goods_id');    
    }
}