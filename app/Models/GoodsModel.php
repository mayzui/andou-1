<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsModel extends Model
{
    protected $table = 'goods';

    protected $guarded = [];

    public $timestamps = false;

    public function secRule()
    {
        return $this->hasOne(SecRuleModel::class, 'goods_id');
    }

    public function secDetails()
    {
        return $this->hasMany(SecDetailModel::class, 'goods_id');
    }

    public function getImgAttribute($value){
        $protocol = request()->getScheme();
        $domain = request()->getHost();

        return $protocol . '://' . $domain . $value;
    }
}
