<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ogoods extends Model
{
    protected $table = 'order_goods';
    public $timestamps = false;

    public function phone()
    {
        return $this->hasOne('App\Models\Orders','order_sn','order_id');
    }

    public function users(){
        return $this->hasOne('App\Models\Admin','id','user_id');
    }

}