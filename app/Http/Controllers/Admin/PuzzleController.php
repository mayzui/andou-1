<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Handlers\Tree;
use App\Libraires\ApiResponse;
use Auth;

class PuzzleController extends BaseController
{
    public function index()
    {
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('merchant_type_id',2)
            -> where('is_reg',1)
            -> select('id')
            -> first();
        // $all = $request->all();
        if(!empty($i)){
            $data = DB::table('puzzle_goods')
                ->join('goods','puzzle_goods.goods_id','=','goods.id')
                ->where('puzzle_goods.merchant_id',$i->id)
                ->select('puzzle_goods.id','goods.name','goods.img','puzzle_goods.storage','puzzle_goods.price','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_goods.code')
                ->paginate(10);
        }else{
            $data = DB::table('puzzle_goods')
                ->join('goods','puzzle_goods.goods_id','=','goods.id')
                ->select('puzzle_goods.id','goods.name','goods.img','puzzle_goods.storage','puzzle_goods.price','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_goods.code')
                ->paginate(10);
        }
        $times = date('Y-m-d H:i:s',time());

        return $this->view('',['data'=>$data,'times'=>$times]);

    }

    public function status(Request $request){
        $id = $_GET['id'];
        $i = DB::table('puzzle_goods') -> where('id',$id)->first();
        if($i->code == 0){
            $arr = DB::table('puzzle_goods')->where('id',$id)->update(['code'=>1]);
            flash('下架成功')->success();
            return redirect()->route('shop.puzzle');
        }else{
            $arr = DB::table('puzzle_goods')->where('id',$id)->update(['code'=>0]);
            flash('上架成功')->success();
            return redirect()->route('shop.puzzle');
        }
    }

    public function puzzleUpd(Request $request){
        $input =$request->all();
        $id = $input['id'];
        $seckData = DB::table('puzzle_goods')->where('id',$id)->first();
        $sql = DB::table("goods")
            ->where('id',$seckData->goods_id)
            ->first(['name','id']);
        $arr = DB::table('goods_sku')->where('id',$seckData->sku_id)
            ->select('id','attr_value')->first();
        $arr->attr_value = json_decode($arr->attr_value, 1)[0]['value'];
        // var_dump($arr);die;
        return $this->view('',['arr'=>$seckData,'name'=>$sql,'res'=>$arr]);
    }

    public function puzzleEdit(Request $request)
    {
        $input = $request->all();
        $id = $input['id'];
        $goods_id  = $input['goods_id'];
        $begin_time = $input['begin_time'];
        $finish_time = $input['finish_time'];
        $top_member = $input['top_member'];
        $single_limit = $input['single_limit'];
        $price  = $input['price'];
        $storage  = $input['storage'];
        $config  = $input['config'];
        $sku_id = $input['sku_id'];
        $s = strtotime($begin_time);
        $e = strtotime($finish_time);
        $upd = DB::table('puzzle_goods')->where('id',$id)
            ->update([
                'goods_id'      =>$goods_id,
                'sku_id'         =>$sku_id,
                'begin_time'    =>$begin_time,
                'finish_time'   =>$finish_time,
                'top_member'    =>$top_member,
                'single_limit'  =>$single_limit,
                'price'         =>$price,
                'storage'       =>$storage,
                'config'        =>$config,
                'updated_at'=> date("Y-m-d:H:i:s",time())
            ]);
        if ($upd) {
            flash('编辑成功')->success();
            return redirect()->route('shop.puzzle');
        }
        flash('编辑失败')->error();
        return redirect()->route('shop.puzzle');
    }

    public function addPuzzle(Request $request)
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
        $serData = DB::table("goods")
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
    public function addPuzzleData(Request $request)
    {
        $input  = $request->all();
        $id = Auth::id();
        $goods_id  = $input['goods_id'];
        $sku_id  = $input['sku_id'];

        $begin_time = $input['begin_time'];
        $finish_time = $input['finish_time'];
        $top_member = $input['top_member'];
        $single_limit = $input['single_limit'];
        $price  = $input['price'];
        $storage  = $input['storage'];
        $config  = $input['config'];
        $s = strtotime($begin_time);
        $e = strtotime($finish_time);
        if($s>$e){
            echo '<script>alert("结束时间要比开始时间要大");window.location.href="/admin/shop/addPuzzle";</script>';exit;
        }
        if($s<time() && $e<time()){
            echo '<script>alert("选择的时间不能比当前时间小");window.location.href="/admin/shop/addPuzzle";</script>';exit;
        }
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('is_reg',1)
            -> first();
        if($i){
            $mid = $i->id;
            $addData = DB::table("puzzle_goods")
                ->insert([
                    'goods_id'      =>$goods_id,
                    'sku_id'         =>$sku_id,
                    'begin_time'    =>$begin_time,
                    'finish_time'   =>$finish_time,
                    'top_member'    =>$top_member,
                    'single_limit'  =>$single_limit,
                    'price'         =>$price,
                    'storage'       =>$storage,
                    'config'        =>$config,
                    'created_at'    =>date("Y-m-d:H:i:s",time()),
                    'merchant_id'   =>$mid
                ]);
            if($addData){
                flash('新增成功')->success();
                return redirect()->route('shop.puzzle');
            }
            flash('新增失败')->error();
            return redirect()->route('shop.puzzle');
        }
        $addData = DB::table("puzzle_goods")
            ->insert([
                'goods_id'      =>$goods_id,
                'sku_id'         =>$sku_id,
                'begin_time'    =>$begin_time,
                'finish_time'   =>$finish_time,
                'top_member'    =>$top_member,
                'single_limit'  =>$single_limit,
                'price'         =>$price,
                'storage'       =>$storage,
                'config'        =>$config,
                'created_at'    =>date("Y-m-d:H:i:s",time()),
            ]);
        if($addData){
            flash('新增成功')->success();
            return redirect()->route('shop.puzzle');
        }
        flash('新增失败')->error();
        return redirect()->route('shop.puzzle');
    }

    public function show(Request $request)
    {
        $input  = $request->all();
        $id = Auth::id();     // 当前登录用户的id
        // 判断当前用户是否是商家
        $i = DB::table('merchants')
            -> where('user_id',$id)
            -> where('merchant_type_id',2)
            -> where('is_reg',1)
            -> select('id')
            -> first();

        if (!empty($input['name'])) {
            $where[]=['puzzle_groups.group_code', 'like', '%'.$input['name'].'%'];
            if(!empty($i)){
                $data = DB::table('puzzle_goods')
                    ->join('goods','puzzle_goods.goods_id','=','goods.id')
                    ->join('puzzle_groups','puzzle_goods.id','=','puzzle_groups.puzzle_id')
                    ->join('users','puzzle_groups.captain_id','=','users.id')
                    ->where($where)
                    ->where('puzzle_goods.merchant_id',$i->id)
                    ->select('puzzle_goods.id','goods.name','users.name as nickname','puzzle_groups.group_code','puzzle_groups.member_num','puzzle_groups.total_member','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_groups.status')
                    ->paginate(10);
            }else{
                $data = DB::table('puzzle_goods')
                    ->join('goods','puzzle_goods.goods_id','=','goods.id')
                    ->join('puzzle_groups','puzzle_goods.id','=','puzzle_groups.puzzle_id')
                    ->join('users','puzzle_groups.captain_id','=','users.id')
                    ->where($where)
                    ->select('puzzle_goods.id','goods.name','users.name as nickname','puzzle_groups.group_code','puzzle_groups.member_num','puzzle_groups.total_member','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_groups.status')
                    ->paginate(10);
            }
            $times = date('Y-m-d H:i:s',time());
            return $this->view('',['data'=>$data,'times'=>$times]);

        }else{

        }
        if(!empty($input['status'])){
            if($input['status'] == 1){
                $where[] = ['puzzle_groups.status',1];
            }elseif($input['status'] == 2){
                $where[] = ['puzzle_groups.status',2];
            }elseif($input['status'] == 3){
                $where[] = ['puzzle_groups.status',3];
            }elseif($input['status'] == 4){
                $where[] = ['puzzle_groups.status',4];
            }else{
                if(!empty($i)){
                    $data = DB::table('puzzle_goods')
                        ->join('goods','puzzle_goods.goods_id','=','goods.id')
                        ->join('puzzle_groups','puzzle_goods.id','=','puzzle_groups.puzzle_id')
                        ->join('users','puzzle_groups.captain_id','=','users.id')
                        ->where('puzzle_goods.merchant_id',$i->id)
                        ->select('puzzle_goods.id','goods.name','users.name as nickname','puzzle_groups.group_code','puzzle_groups.member_num','puzzle_groups.total_member','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_groups.status')
                        ->paginate(10);
                }else{
                    $data = DB::table('puzzle_goods')
                        ->join('goods','puzzle_goods.goods_id','=','goods.id')
                        ->join('puzzle_groups','puzzle_goods.id','=','puzzle_groups.puzzle_id')
                        ->join('users','puzzle_groups.captain_id','=','users.id')
                        ->select('puzzle_goods.id','goods.name','users.name as nickname','puzzle_groups.group_code','puzzle_groups.member_num','puzzle_groups.total_member','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_groups.status')
                        ->paginate(10);
                }
                $times = date('Y-m-d H:i:s',time());
                return $this->view('',['data'=>$data,'times'=>$times]);

            }

            if(!empty($i)){
                $data = DB::table('puzzle_goods')
                    ->join('goods','puzzle_goods.goods_id','=','goods.id')
                    ->join('puzzle_groups','puzzle_goods.id','=','puzzle_groups.puzzle_id')
                    ->join('users','puzzle_groups.captain_id','=','users.id')
                    ->where($where)
                    ->where('puzzle_goods.merchant_id',$i->id)
                    ->select('puzzle_goods.id','goods.name','users.name as nickname','puzzle_groups.group_code','puzzle_groups.member_num','puzzle_groups.total_member','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_groups.status')
                    ->paginate(10);
            }else{
                $data = DB::table('puzzle_goods')
                    ->join('goods','puzzle_goods.goods_id','=','goods.id')
                    ->join('puzzle_groups','puzzle_goods.id','=','puzzle_groups.puzzle_id')
                    ->join('users','puzzle_groups.captain_id','=','users.id')
                    ->where($where)
                    ->select('puzzle_goods.id','goods.name','users.name as nickname','puzzle_groups.group_code','puzzle_groups.member_num','puzzle_groups.total_member','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_groups.status')
                    ->paginate(10);
            }
            $times = date('Y-m-d H:i:s',time());
            return $this->view('',['data'=>$data,'times'=>$times]);

        }
        $p_id = $input['id'];
        $where[] = ['puzzle_goods.id',$p_id];
        if(!empty($i)){
            $data = DB::table('puzzle_goods')
                ->join('goods','puzzle_goods.goods_id','=','goods.id')
                ->join('puzzle_groups','puzzle_goods.id','=','puzzle_groups.puzzle_id')
                ->join('users','puzzle_groups.captain_id','=','users.id')
                ->where($where)
                ->where('puzzle_goods.merchant_id',$i->id)
                ->select('puzzle_goods.id','goods.name','users.name as nickname','puzzle_groups.group_code','puzzle_groups.member_num','puzzle_groups.total_member','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_groups.status')
                ->paginate(10);
        }else{
            $data = DB::table('puzzle_goods')
                ->join('goods','puzzle_goods.goods_id','=','goods.id')
                ->join('puzzle_groups','puzzle_goods.id','=','puzzle_groups.puzzle_id')
                ->join('users','puzzle_groups.captain_id','=','users.id')
                ->where($where)
                ->select('puzzle_goods.id','goods.name','users.name as nickname','puzzle_groups.group_code','puzzle_groups.member_num','puzzle_groups.total_member','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.created_at','puzzle_groups.status')
                ->paginate(10);
        }
        $times = date('Y-m-d H:i:s',time());
        return $this->view('',['data'=>$data,'times'=>$times]);

    }
    public function display(Request $request){
        $input = $request->all();
        $id = $input['id'];
        // echo $id;die;
        $res = DB::table('puzzle_groups')
            ->join('users','puzzle_groups.captain_id','=','users.id')
            ->join('puzzle_goods','puzzle_groups.puzzle_id','=','puzzle_goods.id')
            ->where('puzzle_groups.id',$id)
            ->select('puzzle_groups.id','users.name','puzzle_groups.group_code','puzzle_groups.status','puzzle_goods.begin_time','puzzle_goods.finish_time','puzzle_goods.goods_id')
            ->first();
        $arr = DB::table('goods')
            ->where('id',$res->goods_id)
            ->select('id','name','desc')
            ->first();
        $data = DB::table('puzzle_users')
            ->join('users','puzzle_users.user_id','=','users.id')
            ->join('orders','puzzle_users.order_id','=','orders.id')
            ->where('puzzle_users.group_id',$id)
            ->select('users.avator','users.name','puzzle_users.part_time','orders.order_sn')
            ->get();
        // var_dump($data);die;
        return $this->view('',['res'=>$res,'arr'=>$arr,'data'=>$data]);
    }
}