<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Models\BannerPosition;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Auth;

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
        $list = \DB::table('banner_position') -> where('is_del',0) -> paginate(10);
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
    // 修改广告
    public function positionEdit (Request $request)
    {
        $all = $request -> all();
        if($request -> isMethod("get")){
            // 跳转修改界面
            // 根据获取的id，查询数据库内容
            $data = \DB::table('banner_position') -> where('id',$all['id']) -> first();
            return $this->view('',['data'=>$data]);
        }else{
            // 执行修改操作
            // 获取提交的值
            $data = [
                'name' => $all['name']
            ];
            // 根据id 修改当前的值
            $i = \DB::table('banner_position') -> where('id',$all['id']) -> update($data);
            if($i){
                flash("修改成功！") -> success();
                return redirect()->route('banner.position');
            }else{
                flash("修改失败，未修改任何内容") -> error();
                return redirect()->route('banner.position');
            }
        }
    }
    // 删除广告
    public function positionDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 通过提交的id 查询数据库的值
        $status = \DB::table('banner_position') -> where('id',$all['id']) -> select(['status']) -> first();

        // 判断该数据是否启用
        if($status -> status == 1){
            $data = [
                'status' => 0
            ];
        }else{

            $data = [
                'status' => 1
            ];
        }
        // 执行修改状态操作
        $i = \DB::table('banner_position') -> where('id',$all['id']) -> update($data);
        if($i){
            flash("状态更新成功！") -> success();
            return redirect()->route('banner.position');
        }else{
            flash("状态更改失败！请重试") -> error();
            return redirect()->route('banner.position');
        }
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

    public function notice(){
        $data=Db::table('notice')->paginate(10);
        return $this->view('',['data'=>$data]);
    }
    public function noticeedit(){
        $all = request()->all();
        if (request()->isMethod('post')) {
            $save['title']=$all['title'];
            $save['content']=$all['content'];
            if (empty($all['id'])) {
                $save['status']=0;
                $save['auther']=Auth::id();
                $save['created_at']=$save['updated_at']=date('Y-m-d H:i:s',time());
                $re=Db::table('notice')->insert($save);
            }else{
                $save['updated_at']=date('Y-m-d H:i:s',time());
                $save['auther']=Auth::id();
                $re=Db::table('notice')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('修改成功')->success();
                return redirect()->route('banner.notice');
            }else{
                flash('修改失败')->error();
                return redirect()->route('banner.notice');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                
            }else{
                $data=Db::table('notice')->where('id',$all['id'])->first();
            }
            return $this->view('',['data'=>$data]);
        }
    }
    public function noticedel(){
         // 获取传入的id
        $all = request() -> all();
        // 根据id删除表中数据
        $data['status'] = $all['status'];
        $i = DB::table("notice") ->where('id',$all['id']) ->update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('banner.notice');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('banner.notice');
        }
    }

}
