<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ogoods extends Model
{
    protected $table = 'order_goods';
    public $timestamps = false;

    public function phone()
    {
        return $this->hasOne('App\Models\Orders','order_id','order_sn');
    }

}