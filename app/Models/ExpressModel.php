<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpressModel extends Model
{
    public $timestamps =false;
    protected $table='express_model';

    public function merchant ()
    {
        return  $this->belongsTo("App\Models\Admin",'merchant_user_id','id')->select('id','name');
    }

    public function getIsFreeAttribute ($value)
    {
        return  ($value == 1) ? '免运费' : '买家付运费';
    }
    public function expressModel ()
    {
        return  $this->hasMany('App\Models\ExpressAttr','express_model_id','id');
    }

}
