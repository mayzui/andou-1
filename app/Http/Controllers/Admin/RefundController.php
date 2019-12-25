<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefundController extends BaseController
{
    public function index ()
    {
    	// 查询意见反馈表中内容
        $data = \DB::table('refund_reason') -> paginate(10);
        return $this -> view('',['data' => $data]);
    }

    public function indexChange(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            if(empty($all['id'])){
                // 跳转新增界面
                return $this->view('');
            }else{
                // 跳转修改界面
                $data = \DB::table('refund_reason') -> where('id',$all['id']) -> first();
                return $this->view('',['data'=>$data]);
            }
        }else{
            if(empty($all['id'])){
                // 执行新增操作
                $data = [
                    'name' => $all['name']
                ];
                $i = \DB::table('refund_reason') -> insert($data);
                if($i){
                    flash("新增成功") -> success();
                    return redirect()->route('refund.index');
                }else{
                    flash("新增失败") -> error();
                    return redirect()->route('refund.index');
                }
            }else{
                // 执行修改操作
                $data = [
                    'name' => $all['name']
                ];
                $i = \DB::table('refund_reason') -> where('id',$all['id']) -> update($data);
                if($i){
                    flash("修改成功") -> success();
                    return redirect()->route('refund.index');
                }else{
                    flash("修改失败，未修改任何内容") -> error();
                    return redirect()->route('refund.index');
                }
            }
        }
    }

    // 删除退货原因
    public function indexDel(){
        $all = \request() -> all();
        // 判断删除状态
        if($all['is_del'] == 1){
            $data = [
                'is_del' => 0
            ];
        }else{
            $data = [
                'is_del' => 1
            ];
        }
        $i = \DB::table('refund_reason') -> where('id',$all['id']) -> update($data);
        if($i){
            flash("更新成功") -> success();
            return redirect()->route('refund.index');
        }else{
            flash("更新失败") -> error();
            return redirect()->route('refund.index');
        }
    }
}
