<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpressAttr extends Model
{
    public $timestamps = false;
    protected $table = 'express_detail';

    public function city ()
    {
        return $this->belongsTo('App\Models\District','city_id','id')->select('id','name');
    }


}
