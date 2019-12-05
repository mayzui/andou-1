<?php

namespace App\Models;

use function Couchbase\defaultDecoder;
use Illuminate\Database\Eloquent\Model;

class Config extends Model
{
    public $timestamps = false;
    protected $table = 'config';

    public function getTypeAttribute ($value)
    {
        switch ($value)
        {
            case 1 :
                return  'JSON';
                break;
            case 2 :
                return 'number';
                break;
            case 3 :
                return  'string';
                break;
            default:
                return false;
        }
    }

}
