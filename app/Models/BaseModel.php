<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 15:47
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BaseModel extends Model {

    public $timestamps = false;
    protected $domain;

    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
        $this->domain = env('APP_URL');
    }

    public function increment($column, $amount = 1, array $extra = []) {
        return parent::increment($column, $amount, $extra);
    }

    public function decrement($column, $amount = 1, array $extra = []) {
        return parent::decrement($column, $amount, $extra);
    }
}
