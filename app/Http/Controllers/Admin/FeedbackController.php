<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class FeedbackController extends BaseController
{
    public function index ()
    {
    	// 查询意见反馈表中内容
        $data = \DB::table('feedback')
            -> join('users','feedback.user_id','=','users.id')
            -> where('feedback.is_del',0)
            -> select(['feedback.id','feedback.content','users.name'])
            -> paginate(10);
        return $this -> view('',['data' => $data]);
    }

    public function indexDel(){
        // 获取提交的id
        $all = \request() -> all();
        // 链接数据库，删除数据
        $data = [
            'is_del' => 1
        ];
        $i = \DB::table('feedback') -> where('id',$all['id']) ->update($data);
        if($i){
            flash("删除成功") -> success();
            return redirect()->route('feedback.index');
        }else{
            flash("删除失败") -> success();
            return redirect()->route('feedback.index');
        }
    }
}
