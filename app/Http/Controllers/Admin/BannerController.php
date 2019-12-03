<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class BannerController extends BaseController
{

    //广告列表
    public function index (Request $request)
    {
        $list = Banner::with('position')->paginate($request->input('size'));

        if ($list) {
            return $this->view('index',compact($list));
        }
    }

    // 添加广告
    public function add (Request $request)
    {


    }

    // 编辑广告
    public function update (Request $request)
    {


    }

    // 删除广告
    public function delete (Request $request)
    {


    }


    public function position (Request $request)
    {

    }

    public function positionAdd (Request $request)
    {

    }

    public function positionEdit (Request $request)
    {

    }

    public function positionDelete(Request $request)
    {

    }

}
