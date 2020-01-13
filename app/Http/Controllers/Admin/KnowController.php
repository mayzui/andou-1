<?php

namespace App\Http\Controllers\Admin;

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
       $data = $request->post();
       $findData = \DB::table("hotel_need")->first(['need_content']);
        if (empty($findData)) {
            $insertKnow  = \DB::table("hotel_need")->insert([
                'need_content'=>$data['content']
            ]);
            if ($insertKnow) {
                flash('添加成功') -> success();
                return redirect()->route('know.index');
            }
        }
        $updKnow  = \DB::table("hotel_need")->update([
            'need_content'=>$data['content']
        ]);
        if($updKnow){
            flash('修改成功') -> success();
            return redirect()->route('know.index');
        }
        flash('已经是最新内容') -> error();
        return redirect()->route('know.index');
    }


}