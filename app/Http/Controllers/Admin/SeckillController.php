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
        flash('下架失败')->error();
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
        flash('删除失败')->error();
        return redirect()->route('seckill.list');
    }

    /**
     * @author  jsy
     * @deprecated  秒杀修改
     */

    public function killUpd(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $seckData = Seckill::where(['status'=>1,'id'=>$id])->first()->toArray();
        $sql = DB::table("goods")
            ->where('id','=',$seckData['goods_id'])
            ->where('is_sec','=',1)
            ->first(['name']);
        $seckData['start_time']=strtotime($seckData['start_time']);  //开始时间
        $seckData['end_time']=strtotime($seckData['end_time']);  //结束时间
        $end_time =  date('Y-m-d',$seckData['end_time'])."T".date('H:i:s',$seckData['end_time']);
        $start_time =  date('Y-m-d',$seckData['start_time'])."T".date('H:i:s',$seckData['start_time']);
        return $this->view('sechange',['data'=>$seckData,'gname'=>$sql,'end_time'=>$end_time,'start_time'=>$start_time]);
    }

    /**
     * @author  jsy
     * @deprecated  秒杀编辑
     */

    public function killEdit(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $start_time = $input['start_time'];   //开始时间
        $end_time   = $input['end_time'];     //结束时间
        $kill_price = $input['kill_price'];   //秒杀价格
        $kill_rule  = $input['kill_rule'];    //秒杀规则
        $num  = $input['num'];    //秒杀库存
        $upd = Seckill::where('id',$id)
            ->update([
               'start_time' => $start_time,
               'end_time'   => $end_time,
               'kill_price' => $kill_price,
               'kill_rule'  => $kill_rule,
                'num'        => $num,
                'updated_at'=> date("Y-m-d:H:i:s",time())
            ]);
        if ($upd) {
            flash('编辑成功')->success();
            return redirect()->route('seckill.list');
        }
        flash('编辑失败')->error();
        return redirect()->route('seckill.list');
    }

    /**
     * @author  jsy
     * @deprecated  显示新增秒杀商品页
     */

     public function addKill(Request $request)
     {
         $input = $request->all();
         if(empty($input['name'])){
             return $this->view('');
         }
         $name = $input['name'];         //商品名称
         $id = Auth::id();     // 当前登录用户的id
         // 判断当前用户是否是商家
         $i = DB::table('merchants')
             -> where('user_id',$id)
             -> where('is_reg',1)
             -> first();
         if($i) {
             $serData = DB::table("goods")
                 ->where('is_sec','=',1)
                 ->where('merchant_id','=',$i['id'])
                 ->where('name','like','%'.$name.'%')
                 ->get(['name','id']);
             return $this->view('',['data'=>$serData]);
         }
         $serData = DB::table("goods")
                   ->where('is_sec','=',1)
                   ->where('name','like','%'.$name.'%')
                   ->get(['name','id']);
         return $this->view('',['data'=>$serData]);
     }

    /**
     * @author  jsy
     * @deprecated  新增秒杀商品
     */

    public function addkillData(Request $request)
    {
        $input      = $request->all();
        $id = Auth::id();     // 当前登录用户的id
        $goods_id   = $input['goods_id'];        //秒杀商品id
        $start_time = $input['start_time'];      //开始时间
        $end_time   = $input['end_time'];        //结束时间
        $kill_price = $input['kill_price'];      //秒杀价格
        $num        = $input['num'];              //秒杀库存
        $kill_rule  = $input['kill_rule'];        //秒杀规则
        $s = strtotime($start_time);
        $e = strtotime($end_time);
        if($s>$e){
            echo '<script>alert("结束时间要比开始时间要大");window.location.href="/admin/seckill/addkill";</script>';exit;
        }
        if($s<time() && $e<time()){
            echo '<script>alert("选择的时间不能比当前时间小");window.location.href="/admin/seckill/addkill";</script>';exit;
        }
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        if($i){
            $addData = DB::table("seckill_rules")
                ->insert([
                    'goods_id'      =>$goods_id,
                    'start_time'    =>$start_time,
                    'end_time'      =>$end_time,
                    'kill_price'    =>$kill_price,
                    'num'            =>$num,
                    'kill_rule'     =>$kill_rule,
                    'status'        =>1,
                    'created_at'    =>date("Y-m-d:H:i:s",time()),
                    'merchantsid'   =>$i['id']
                ]);
            if($addData){
                flash('新增成功')->success();
                return redirect()->route('seckill.list');
            }
            flash('新增失败')->error();
            return redirect()->route('seckill.list');
        }
        $addData = DB::table("seckill_rules")
            ->insert([
                'goods_id'      =>$goods_id,
                'start_time'    =>$start_time,
                'end_time'      =>$end_time,
                'kill_price'    =>$kill_price,
                'num'            =>$num,
                'kill_rule'     =>$kill_rule,
                'status'        =>1,
                'created_at'    =>date("Y-m-d:H:i:s",time())
            ]);
        if($addData){
            flash('新增成功')->success();
            return redirect()->route('seckill.list');
        }
        flash('新增失败')->error();
        return redirect()->route('seckill.list');

    }
}