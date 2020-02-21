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
        $id =Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        if(!empty($input['name'])){
            if($i){
                $names  = $input['name'];     //要搜索的商品名字
                $seckData = DB::table("seckill_rules")
                    ->join("goods",'seckill_rules.goods_id','=','goods.id')
                    ->where('seckill_rules.status','=',1)
                    ->where('goods.is_sec','=',1)
                    ->where('goods.name','like','%'.$names.'%')
                    ->where('seckill_rules.merchantsid','=',$i->id)
                    ->select(['goods.name','seckill_rules.num','seckill_rules.start_time','seckill_rules.end_time','seckill_rules.kill_num','seckill_rules.id as seid','seckill_rules.sku_id'])
                    ->paginate(10);
                return $this->view('seclist',['list'=>$seckData,'names'=>$names]);
            }
            $names  = $input['name'];     //要搜索的商品名字
            $seckData = DB::table("seckill_rules")
                ->join("goods",'seckill_rules.goods_id','=','goods.id')
                ->where('seckill_rules.status','=',1)
                ->where('goods.is_sec','=',1)
                ->where('goods.name','like','%'.$names.'%')
                ->select(['goods.name','seckill_rules.num','seckill_rules.start_time','seckill_rules.end_time','seckill_rules.kill_num','seckill_rules.id as seid','seckill_rules.sku_id'])
                ->paginate(10);
            return $this->view('seclist',['list'=>$seckData,'names'=>$names]);
        }
        if (!empty($input['status'])){
            $status =$input['status'];   //接受状态值
            if($status ==2) {         //进行中
                $seckData = Seckill::where(['status'=>1])
                    ->where(function($query){
                        $query->where('start_time','<',now())
                            ->where(function($query){
                                $query->where('end_time','>',now())
                                    ->where(function($query){
                                        $query->where('num','!=',0);
                                    });
                            });
                    })
                    ->paginate(10);
            }elseif ($status==3){     //已结束
                $seckData = Seckill::where(['status'=>1])
                    ->where(function($query){
                        $query->where('end_time','<',now());
                    })
                    ->paginate(10);
            }elseif ($status==4){     //进行中(售罄)
                $seckData = Seckill::where(['num'=>0,'status'=>1])
                    ->where(function($query){
                        $query->where('start_time','<',now())
                            ->where(function($query){
                                $query->where('end_time','>',now());
                            });
                    })
                    ->paginate(10);
            }elseif($status==1){
                $seckData = Seckill::where('status',1)->paginate(10);
            }elseif($status==6){     //搜索
                if ($i){
                    $names  = $input['named'];     //要搜索的商品名字
                    $seckData = DB::table("seckill_rules")
                        ->join("goods",'seckill_rules.goods_id','=','goods.id')
                        ->where('seckill_rules.status','=',1)
                        ->where('goods.is_sec','=',1)
                        ->where('goods.name','like','%'.$names.'%')
                        ->where('seckill_rules.merchantsid','=',$i->id)
                        ->select(['goods.name','seckill_rules.num','seckill_rules.start_time','seckill_rules.end_time','seckill_rules.kill_num','seckill_rules.id as seid'])
                        ->paginate(10);
                    return $this->view('seclist',['list'=>$seckData,'names'=>$names]);
                }
                $names  = $input['named'];     //要搜索的商品名字
                $seckData = DB::table("seckill_rules")
                    ->join("goods",'seckill_rules.goods_id','=','goods.id')
                    ->where('seckill_rules.status','=',1)
                    ->where('goods.is_sec','=',1)
                    ->where('goods.name','like','%'.$names.'%')
                    ->select(['goods.name','seckill_rules.num','seckill_rules.start_time','seckill_rules.end_time','seckill_rules.kill_num','seckill_rules.id as seid'])
                    ->paginate(10);
                return $this->view('seclist',['list'=>$seckData,'names'=>$names]);
            }else{}
        }else{
            $status = 0;
            $seckData = Seckill::where('status',1)->paginate(10);
        }

        if ($i){
            $mid = $i->id;        //商户id
            $seckData = Seckill::where(['status'=>1,'merchantsid'=>$mid])->paginate(10);
            for($i=0;$i<count($seckData);$i++){
                $sql = DB::table("goods")
                    ->where('id','=',$seckData[$i]['goods_id'])
                    ->where('merchant_id','=',$mid)
                    ->where('is_sec','=',1)
                    ->first(['name']);
                $arr [] =$sql;
            }
            for ($i=0;$i<count($seckData);$i++){
                $seckData[$i]['goods_name'] = $arr[$i];
            }
            return $this->view('seclist',['list'=>$seckData]);
        }
        for($i=0;$i<count($seckData);$i++){
                $sql = DB::table("goods")
                    ->where('id','=',$seckData[$i]['goods_id'])
                    ->where('is_sec','=',1)
                    ->first(['name']);
                $arr [] =$sql;
        }
        for ($i=0;$i<count($seckData);$i++){
            $seckData[$i]['goods_name'] = $arr[$i];
        }
        if($status==0){
            return $this->view('seclist',['list'=>$seckData]);
        }
        return $this->view('seclist',['list'=>$seckData,'status'=>$status]);
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
        if(empty($input['sku_id']))
        {
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
        }else{
            $sku_id = $input['sku_id'];   //商品规格id
            $id = $input['id'];
            $seckData = Seckill::where(['status'=>1,'id'=>$id])->first()->toArray();
            //商品规格
            $skuData = DB::table("goods_sku")
                ->where('id','=',$sku_id)
                ->where('is_valid','=',1)
                ->first(['attr_value','id']);
            $sql = DB::table("goods")
                ->where('id','=',$seckData['goods_id'])
                ->where('is_sec','=',1)
                ->first(['name']);
            $gdata =  DB::table("goods_sku")
                ->where('goods_id','=',$seckData['goods_id'])
                ->where('is_valid','=',1)
                ->get(['id','attr_value']);
            $seckData['start_time']=strtotime($seckData['start_time']);  //开始时间
            $seckData['end_time']=strtotime($seckData['end_time']);  //结束时间
            $end_time =  date('Y-m-d',$seckData['end_time'])."T".date('H:i:s',$seckData['end_time']);
            $start_time =  date('Y-m-d',$seckData['start_time'])."T".date('H:i:s',$seckData['start_time']);
            return $this->view('sechange',['data'=>$seckData,'gname'=>$sql,'end_time'=>$end_time,'start_time'=>$start_time,'skudata'=>$skuData,'gdata'=>$gdata,'kid'=>$sku_id]);
        }

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
        $diffTime =(strtotime($end_time)-strtotime($start_time))/3600;
        if(!preg_match("/^[1-9][0-9]*$/" ,$diffTime)){
            echo '<script>alert("活动时间必须是整点");window.location.href="/admin/seckill/list";</script>';exit;
        }
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
            $mid = $i->id;
            $serData = DB::table("goods")
                ->where('is_sec','=',1)
                ->where('merchant_id','=',$mid)
                ->where('name','like','%'.$name.'%')
                ->get(['name','id']);
            for($i=0;$i<count($serData);$i++){
                $goods_id = $serData[$i]->id;       //商品id
                $sql = DB::table("goods_sku")
                    ->where('goods_id','=',$goods_id)
                    ->where('is_valid','=',1)
                    ->get(['id','attr_value']);
                $arr [] =$sql;
            }
            if(empty($arr)){
                return $this->view('',['data'=>$serData]);
            }
            for ($i=0;$i<count($arr[0]);$i++)
            {
                $arr[0][$i]->attr_value = json_decode($arr[0][$i]->attr_value);
            }
            return $this->view('',['data'=>$serData,'sku'=>$arr]);
        }
        //非商户
        $serData = DB::table("goods")
            ->where('is_sec','=',1)
            ->where('name','like','%'.$name.'%')
            ->get(['name','id']);
        for($i=0;$i<count($serData);$i++){
            $goods_id = $serData[$i]->id;       //商品id
            $sql = DB::table("goods_sku")
                ->where('goods_id','=',$goods_id)
                ->where('is_valid','=',1)
                ->get(['id','attr_value']);
            $arr [] =$sql;
        }
        if(empty($arr)){
            return $this->view('',['data'=>$serData]);
        }
        for ($i=0;$i<count($arr[0]);$i++)
        {
            $arr[0][$i]->attr_value = json_decode($arr[0][$i]->attr_value);
        }
        return $this->view('',['data'=>$serData,'sku'=>$arr]);
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
        $sku_id     = $input['sku_id'];           //商品规格id
        $s = strtotime($start_time);
        $e = strtotime($end_time);

        if(empty($start_time)){
            echo '<script>alert("未选择时间");window.location.href="/admin/seckill/addkill";</script>';exit;
        }
        if(empty($end_time)){
            echo '<script>alert("未选择时间");window.location.href="/admin/seckill/addkill";</script>';exit;
        }
        if(!empty($sku_id)){
            $skuData = DB::table("goods_sku")
                ->where('id','=',$sku_id)
                ->where('is_valid','=',1)
                ->first(['id','store_num']);
           if($num<=$skuData->store_num){
           }else{
               echo '<script>alert("该商品的库存已超出");window.location.href="/admin/seckill/addkill";</script>';exit;
           }
        }
        if($s>$e){
            echo '<script>alert("结束时间要比开始时间要大");window.location.href="/admin/seckill/addkill";</script>';exit;
        }
        if($s<time() && $e<time()){
            echo '<script>alert("选择的时间不能比当前时间小");window.location.href="/admin/seckill/addkill";</script>';exit;
        }
      $diffTime = ($e-$s)/3600;
        if(!preg_match("/^[1-9][0-9]*$/" ,$diffTime)){
            echo '<script>alert("活动时间必须是整点");window.location.href="/admin/seckill/addkill";</script>';exit;
        }
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        if($i){
            $mid = $i->id;
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
                    'merchantsid'   =>$mid,
                    'sku_id'        =>$sku_id
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
                'created_at'    =>date("Y-m-d:H:i:s",time()),
                'sku_id'        =>$sku_id
            ]);
        if($addData){
            flash('新增成功')->success();
            return redirect()->route('seckill.list');
        }
        flash('新增失败')->error();
        return redirect()->route('seckill.list');
    }

    /**
     * @author  jsy
     * @deprecated  秒杀统计
     */
    public function killCount(Request $request)
    {
        $input = $request->all();
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        if ($i){
            $mid = $i->id;
            $selData = DB::table("seckill_details")
                ->where('merchantsid','=',$mid)
                ->paginate(10);
            for($i=0;$i<count($selData);$i++){
                $sql = DB::table("goods")
                    ->where('id','=',$selData[$i]->goods_id)
                    ->where('is_sec','=',1)
                    ->first(['name']);
                $arr [] =$sql;    //商品名称
            }
            for($i=0;$i<count($selData);$i++){
                $sql = DB::table("users")
                    ->where('id','=',$selData[$i]->user_id)
                    ->first(['name']);
                $user [] =$sql;    //用户名
            }
            for ($i=0;$i<count($selData);$i++){
                $selData[$i]->goods_name = $arr[$i];   //赋值商品名称
                $selData[$i]->user_name = $user[$i];    //赋值用户名称
            }
            return $this->view('killcount',['data'=>$selData]);
        }

        $selData = DB::table("seckill_details")->paginate(10);
        for($i=0;$i<count($selData);$i++){
            $sql = DB::table("goods")
                ->where('id','=',$selData[$i]->goods_id)
                ->where('is_sec','=',1)
                ->first(['name']);
            $arr [] =$sql;    //商品名称
        }
        for($i=0;$i<count($selData);$i++){
            $sql = DB::table("users")
                ->where('id','=',$selData[$i]->user_id)
                ->first(['name']);
            $user [] =$sql;    //用户名
        }
        for ($i=0;$i<count($selData);$i++){
            $selData[$i]->goods_name = $arr[$i];   //赋值商品名称
            $selData[$i]->user_name = $user[$i];    //赋值用户名称
        }
        return $this->view('killcount',['data'=>$selData]);
    }

    /**
     * @author  jsy
     * @deprecated  秒杀统计删除
     */
    public function countDel(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $delData = DB::table("seckill_details")
                   ->where('id',$id)
                   ->delete();
        if ($delData){
            flash('删除成功')->success();
            return redirect()->route('seckill.count');
        }else{
            flash('删除失败')->error();
            return redirect()->route('seckill.count');
        }
    }

    /**
     * @author  jsy
     * @deprecated  秒杀商品规格
     */
    public function sku(Request $request)
    {
        $input = $request->all();
        $gid = $input['gid'];
        $skuData = DB::table("goods_sku")
            ->where('goods_id','=',$gid)
            ->where('is_valid','=',1)
            ->get(['id','attr_value']);
        for($i=0;$i<count($skuData);$i++){
            $skuData[$i]->attr_value = json_decode($skuData[$i]->attr_value);
        }

        echo json_encode(['code'=>0,'data'=>$skuData]);
    }
}