<?php

namespace App\Http\Controllers\Admin;

use App\Models\Config;
use Illuminate\Http\Request;

class ConfigController extends BaseController
{
    public function index (Request $request)
    {
        $list = Config::paginate($request->input('size'));
        if ($list) {
            return $this->view('index',['list'=>$list]);
        }
    }

    public function  add (Request $request)
    {


    }

    public function update ()
    {

    }

    public function store (Request $request)
    {

    }

    public function delete ()
    {

    }

}
