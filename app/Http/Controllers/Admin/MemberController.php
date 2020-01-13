<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class MemberController extends BaseController
{
    public function index ()
    {
        // 查询配置中 会员充值所需金额
        $config_data = \DB::table('config') -> where('key','vipRecharge') -> first();
        // 查询会员权益表中的内容
        $data = \DB::table('vip_equity') -> first();
        return $this -> view('',['config_data' => $config_data,'data'=> $data]);
    }

    public function indexChange(){
        $all = \request() -> all();
        \DB::beginTransaction();
        try{
            $content = [
                'content' => $all['desc']
            ];
            $m = \DB::table('vip_equity') -> where('id',$all['id']) -> update($content);
            $config = [
              'value' => $all['value']
            ];
            $n = \DB::table('config') -> where('id',$all['config_data_id']) -> update($config);
            \DB::commit();
            flash("修改成功") -> success();
            return redirect()->route('member.index');
        }catch (\Exception $exception){
            \DB::rollBack();
            flash("修改失败，请稍后重试") -> success();
            return redirect()->route('member.index');
        }

    }
}
