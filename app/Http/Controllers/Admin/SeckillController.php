<?php

namespace App\Http\Controllers\Admin;
use App\Models\Goods;
use App\Models\Seckill;
use Illuminate\Support\Facades\DB;
use App\Services\ActionLogsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Auth;

class SeckillController extends BaseController
{
    /**
     * @author  jsy
     * @deprecated  秒杀列表
     */
    public function list(Request $request)
    {
        $input = $request->all();
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        $seckData = Seckill::where('status',1)->get()->toArray();
        if ($i){
            foreach ($seckData as $k){
                $sql = DB::table("goods")
                    ->where('id','=',$k['goods_id'])
                    ->where('merchant_id','=',$i['id'])
                    ->where('is_sec','=',1)
                    ->first(['name']);
                $arr [] =$sql;
            }
            for ($i=0;$i<count($seckData);$i++){
                $seckData[$i]['goods_name'] = $arr[$i];
            }
            return $this->view('seclist',['list'=>$seckData]);
        }
        foreach ($seckData as $k){
             $sql = DB::table("goods")
                 ->where('id','=',$k['goods_id'])
                 ->where('is_sec','=',1)
                 ->first(['name']);
             $arr [] =$sql;
             }
        for ($i=0;$i<count($seckData);$i++){
            $seckData[$i]['goods_name'] = $arr[$i];
        }
        return $this->view('seclist',['list'=>$seckData]);
    }

    /**
     * @author  jsy
     * @deprecated  秒杀下架
     */

    public function killDel(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $delData = Seckill::where('id',$id)->update(['status'=>0]);
        if ($delData){
            flash('下架成功')->success();
            return redirect()->route('seckill.list');
        }
        flash('下架失败')->success();
        return redirect()->route('seckill.list');
    }

    /**
     * @author  jsy
     * @deprecated  秒杀删除
     */

    public function killDels(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $delsData = Seckill::where('id',$id)->delete();
        if ($delsData){
            flash('删除成功')->success();
            return redirect()->route('seckill.list');
        }
        flash('删除失败')->success();
        return redirect()->route('seckill.list');
    }
}