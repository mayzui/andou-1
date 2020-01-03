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
                $this->rejson(0,'添加成功','');die;
            }
        }
        $updKnow  = \DB::table("hotel_need")->update([
            'need_content'=>$data['content']
        ]);
        if($updKnow){
            $this->rejson(0,'修改成功','');die;
        }
        $this->rejson(2,'已经是最新内容','');
    }


}