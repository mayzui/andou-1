<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2020/1/16 0016
 * Time: 14:30
 */

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\DB;

class ProtocolController
{
    public function index(){
        $data = DB::table('protocol')->get();
        return view("/admin/protocol/index",['data'=>$data]);
    }
    public function protocolAdd(){
        $all = request()->all();
        if (request()->isMethod('post')) {
            $save = [
                'name'=>$all['name'],
                'content'=>$all['content'],
            ];
            if (empty($all['id'])) {
                $re=Db::table('protocol')->insert($save);
            }else{
                $re=Db::table('protocol')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('修改成功')->success();
                return redirect()->route('about.protocol');
            }else{
                flash('修改失败')->error();
                return redirect()->route('about.protocol');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                $data->name='';
                $data->content='';
            }else{
                $data=Db::table('protocol')->where('id',$all['id'])->first();
            }
            return view('/admin/protocol/protocolAdd',['data'=>$data]);
        }
    }
    public function upd(){
        $all = request()->all();
        if($all['status'] == 0){
            $re=Db::table('protocol')->where('id',$all['id'])->update(['status'=>1]);
            return redirect()->route('about.protocol');
        }else{
            $re=Db::table('protocol')->where('id',$all['id'])->update(['status'=>0]);
            return redirect()->route('about.protocol');
        }
    }
}