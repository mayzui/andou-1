<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class AboutController extends BaseController
{
    public function index ()
    {
    	// 查询意见反馈表中内容
        $data = \DB::table('about')
            -> join('config','about.config_id','=','config.id')
            -> where('about.id',1)
            -> select(['about.id','config.value','about.image','about.title','about.content','about.copyright'])
            -> first();
        return $this -> view('',['data' => $data]);
    }

    public function indexChange(){
        $all = \request() -> all();
        $data = [
            'image' => $all['img'],
            'title' => $all['title'],
            'content' => $all['content'],
            'copyright' => $all['copyright']
        ];
        $i = \DB::table('about') -> where('id',$all['id']) -> update($data);
        if($i){
            flash("修改成功") -> success();
            return redirect()->route('about.index');
        }else{
            flash("修改失败") -> success();
            return redirect()->route('about.index');
        }
    }
}
