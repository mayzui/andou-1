<?php

namespace App\Http\Controllers\Admin;

use App\Models\Config;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

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
        return $this->view('add');
    }

    public function update (Request $request,$id)
    {
        $config = Config::find($id);
        return $this->view('update',['config'=>$config]);
    }

    public function store (Request $request)
    {

        $ruls = [
            'key' => 'required',
            'value'=> 'required',
            'desc' => 'required',
            'type' => 'required|numeric',
        ];
        if (!$request->filled('id')) {
            $ruls['key'] = 'required|unique:config,key';
        }

        $validate = Validator::make($request->all(),$ruls,[
            'key.required'=>'缺少配置键名称',
            'key.unique'=>'键已存在',
            'value.required'=>'缺少配置值',
            'desc.required'=>'缺少配置描述',
            'type.required'=>'缺少配置数据类型',
        ]);

        if ($validate->fails()) {
            flash($validate->errors()->first())->error()->important();
            return redirect()->route('config.add');
        }

        $model = new Config();
        if ($request->input('id')) {
            $model = Config::find($request->input('id'));
        }
        $model->key = $request->input('key');
        $model->value = $request->input('value');
        $model->desc = $request->input('desc');
        $model->type = $request->input('type');
        if ($model->save()) {
            return  redirect()->route('config.index');
        }
        flash('操作失败')->error()->important();
        return redirect()->route('config.add');
    }

    public function delete (Request $request ,$id)
    {
        $model = Config::find($id);
        if ($model->delete()){
            return redirect()->route('config.index');;
        }
        return viewError('已删除或者删除失败');
    }

}
