<?php

/**
 * Created by IntelliJ IDEA.
 * User: maxsky
 * Date: 2020/2/27
 * Time: 16:35
 */

namespace App\Models;

class Users extends BaseModel {

    protected $table = 'users';
    protected $fillable = ['order_money'];
}
