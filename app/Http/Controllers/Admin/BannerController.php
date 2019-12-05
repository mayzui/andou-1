<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\BannerPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;


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
        $model = Banner::find($id);
        if ($model->delete()){
            return redirect()->route('banner.index');;
        }
        return viewError('已删除或者删除失败');
    }


    public function position (Request $request)
    {
        $list = BannerPosition::paginate($request->input('size'));
        if ($list) {
            return $this->view('position',['list'=>$list]);
        }
    }

    public function positionAdd (Request $request)
    {
        return $this->view('positionAdd');
    }


    public function positionStore (Request $request)
    {
        $validate = Validator::make($request->all(),[
            'name' => 'required',
        ],[
            'name.required'=>'缺少广告位名',
        ]);

        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
           return redirect()->route('banner.positionAdd');
        }


        $model = new BannerPosition();
        if ($request->input('id')) {
            $model = BannerPosition::find($request->input('id'));
        }
        $model->name = $request->input('name');
        if ($model->save()) {
            return   redirect()->route('banner.position');
        }
        return  viewError('操作失败','banner.positionAdd');

    }
    public function positionEdit (Request $request,$id)
    {
        $position = BannerPosition::find($id);
        return $this->view('positionEdit',['position'=>$position]);
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
            'img.required'=>'请上传图片',
        ]);


        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('banner.add');
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

    public function  status (Request $request ,$status , $id)
    {

        $validate = Validator::make(['status'=>$status,'id'=>$id],[
            'status' => 'required',
            'id'     => 'required',
        ],[
            'status.required'=>'缺少状态值',
            'id.required'=>'缺少id',
        ]);

        if ($validate->errors()->first()) {
            return viewError($validate->errors()->first(),'banner.index');
        }

        $model = Banner::find($id);
        $model->status = $status;
        $model->save();
        return  redirect()->route('banner.index');
    }

}
