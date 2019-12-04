<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\BannerPosition;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Ramsey\Uuid\Converter\Number\BigNumberConverter;

class BannerController extends BaseController
{

    //广告列表
    public function index (Request $request)
    {
        $list = Banner::with('position')->paginate($request->input('size'));
        if ($list) {
            return $this->view('index',['list'=>$list]);
        }
    }

    // 添加广告
    public function add (Request $request)
    {
        $position = BannerPosition::select('id','name')->get();
        return $this->view('add',['position'=>$position]);
    }

    // 编辑广告
    public function update (Request $request , $id)
    {
        $position = BannerPosition::select('id','name')->get();
        $banner = Banner::find($id);
        return $this->view('update',['position'=>$position,'banner'=>$banner]);

    }

    // 删除广告
    public function delete (Request $request,$id)
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

    public function store (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'banner_position_id' => 'required',
            'url'     => 'required',
            'desc' => 'required',
            'sort' => 'required|numeric',
            'status'    => 'required',
            'img'=>'required'
        ],[
            'banner_position_id.required'=>'缺少广告位',
            'url.required'=>'缺少链接',
            'sort.required'=>'缺少排序',
            'sort.numeric'=>'排序必须是数字',
            'desc.required'=>'缺少描述',
            'status.required'=>'缺少状态',
        ]);

        if ($validate->errors()->first()) {
            return viewError($validate->errors()->first(),'banner.add');
        }

        $model = new Banner();
        if ($request->input('id')) {
            $model = Banner::find($request->input('id'));
        }
        $model->banner_position_id = $request->input('banner_position_id');
        $model->url = $request->input('url');
        $model->desc = $request->input('desc');
        $model->sort = $request->input('sort');
        $model->status = $request->input('status');
        $model->img = $request->input('img');
        if ($model->save()) {
            return   redirect()->route('banner.index');
        }
        return  viewError('操作失败','banner.add');
    }

}
