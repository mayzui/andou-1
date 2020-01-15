<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class KnowController extends Controller
{
    /**
     * @descript 入住需知
     * @author  jsy
     */
    public function index()
    {
        $data = \DB::table("hotel_need")->first(['need_content']);
        return view('admin.know.index',['data'=>$data]);
    }

    /**
     * @descript 新增修改入住需知
     * @author  jsy
     */

    public function www(Request $request)
    {

        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();

        $data = $request->post();

        if ($i) {
            $knowInfo = Db::table("hotel_need")->where('merchantid',$id)->first(['merchantid','need_content']);
            if(empty($knowInfo)){
                $insertKnow  = \DB::table("hotel_need")->insert([
                    'need_content'=>$data['content'],
                    'merchantid'  =>$id
                ]);
                if ($insertKnow) {
                    flash('添加成功') -> success();
                    return redirect()->route('know.index');
                }
            }else{
                $updKnow  = \DB::table("hotel_need")->where('merchantid',$id)->update([
                    'need_content'=>$data['content'],
                    'merchantid'  =>$id
                ]);
                if($updKnow){
                    flash('修改成功') -> success();
                    return redirect()->route('know.index');
                }
                flash('已经是最新内容') -> error();
                return redirect()->route('know.index');
            }
        }else{
            $findData = \DB::table("hotel_need")->first(['need_content']);
            if (empty($findData)) {
                $insertKnow  = \DB::table("hotel_need")->insert([
                    'need_content'=>$data['content'],
                    'merchantid'  =>$id
                ]);
                if ($insertKnow) {
                    flash('添加成功') -> success();
                    return redirect()->route('know.index');
                }
            }
            $updKnow  = \DB::table("hotel_need")->where('merchantid',$id)->update([
                'need_content'=>$data['content'],
                'merchantid'  =>$id
            ]);
            if($updKnow){
                flash('修改成功') -> success();
                return redirect()->route('know.index');
            }
            flash('已经是最新内容') -> error();
            return redirect()->route('know.index');
        }

    }


}