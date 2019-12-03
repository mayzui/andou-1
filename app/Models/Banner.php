<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $table = 'banner';

    public function position ()
    {
        return $this->belongsTo('App\Models\BannerPosition','banner_position_id','id');
    }
}
