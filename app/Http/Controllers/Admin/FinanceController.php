<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
//use Illuminate\Http\Request;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Repositories\RulesRepository;
use App\Handlers\Tree;
use Mockery\Exception;

class FinanceController extends Controller
{
    /*
     *      平台流水
     * */
    public function cashLogs ()
    {
        // 链接数据库，查询流水数据
        $data = DB::table("cashlogs")
            -> join("users","cashlogs.user_id","=","users.id")
            -> where('cashlogs.is_del',0)
            -> select(['cashlogs.id','users.name','cashlogs.price','cashlogs.state','cashlogs.describe','cashlogs.create_time','cashlogs.type_id'])
            -> orderBy("type_id")
            -> paginate(10);
        return view("/admin/finance/cashLogs",['data'=>$data]);
    }
    // 删除 流水
    public function cashLogsDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 链接数据库，根据id 删除数据库中的内容
        // 链接数据库，根据id，删除数据
        $data = [
            'is_del' => 1
        ];
        // 执行删除操作
        $i = DB::table("cashlogs") -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('finance.cashLogs');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('finance.cashLogs');
        }
    }

    /*
     *      提现管理
     * */
    public function cashOut ()
    {
        // 查询数据库中，资金流动表
        $data = DB::table("money_flow")
            -> join("users","money_flow.user_id","=","users.id")
            -> where('money_flow.is_del',0)
            -> select(['money_flow.id','users.name','money_flow.price','money_flow.create_time','money_flow.status'])
            -> paginate(10);
        return view("/admin/finance/cashOut",['data'=>$data]);
    }
    // 新增提现明细
    public function cashOutChange(){
        // 判断跳转页面还是，执行操作
        if(\request() -> isMethod("get")){
            // 跳转新增界面
            return view('/admin/finance/cashOutChange');
        }else{
            // 执行新增操作
            $all = \request() -> all();
            // 获取提交的内容
            $data = [
                'user_id' => Auth::id(),
                'price' => $all['price'],
                'create_time' => date("Y-m-d H:i:s")
            ];
            // 链接数据库，执行新增操作
            $i = DB::table("money_flow") -> insert($data);
            if($i){
                flash('新增成功') -> success();
                return redirect()->route('finance.cashOut');
            }else{
                flash('新增失败') -> error();
                return redirect()->route('finance.cashOut');
            }
        }
    }
    // 审核提现
    public function cashOutExamine(){
        // 获取传入的id
        $all = \request() -> all();
        // 链接数据库，根据id 删除数据库中的内容
        // 链接数据库，根据id，删除数据
        $data = [
            'status' => 1
        ];
//        return dd(json_decode(json_encode(DB::table("money_flow")-> where('id',$all['id']) -> first()),true));
        // 执行删除操作
        $i = DB::table("money_flow") -> where('id',$all['id']) -> update($data);
        if($i){
            flash('审核成功') -> success();
            return redirect()->route('finance.cashOut');
        }else{
            flash('审核失败') -> error();
            return redirect()->route('finance.cashOut');
        }
    }
    // 删除提现明细
    public function cashOutDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 链接数据库，根据id 删除数据库中的内容
        // 链接数据库，根据id，删除数据
        $data = [
            'is_del' => 1
        ];
        // 执行删除操作
        $i = DB::table("money_flow") -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('finance.cashOut');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('finance.cashOut');
        }
    }


    /*
     *      充值中心
     * */
    public function charge()
    {
        // 链接数据库，查询充值明细表内容
        $data = DB::table("cashlogs")
            -> join("users","cashlogs.user_id","=","users.id")
            -> where('cashlogs.type_id',2)
            -> where('cashlogs.is_del',0)
            -> select(['cashlogs.id','users.name','cashlogs.price','cashlogs.describe','cashlogs.create_time'])
            -> paginate(10);
        return view("/admin/finance/charge",['data'=>$data]);
    }
    // 新增充值明细
    public function chargeChange(){
        if(\request() -> isMethod("get")){
            // 跳转新增界面
            return view('/admin/finance/chargeChange');
        }else{
            // 执行新增操作
            // 获取提交的值
            $all = \request() -> all();
            $data = [
                'user_id' => Auth::id(),
                'merchant_id' => Auth::id(),
                'price' => $all['price'],
                'describe' => $all['describe'],
                'create_time' => date("Y-m-d H:i:s"),
                'type_id' => 2
            ];
            // 链接数据库，执行新增操作
            $i = DB::table("cashlogs") -> insert($data);
            if($i){
                flash('新增成功') -> success();
                return redirect()->route('finance.charge');
            }else{
                flash('新增失败') -> error();
                return redirect()->route('finance.charge');
            }
        }
    }
    // 删除充值明细
    public function chargeDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 链接数据库，根据id，删除数据
        $data = [
            'is_del' => 1
        ];
        // 执行删除操作
        $i = DB::table("cashlogs") -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('finance.charge');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('finance.charge');
        }
    }

    /*
     *      感恩币中心
     * */
    public function integral ()
    {
        // 查询数据库，感恩币总表的数据
        $data = DB::table("integral")
            -> join("integral_type","integral.type_id","=","integral_type.id")
            -> join("users","integral.user_id","=","users.id")
            -> where('integral.is_del',0)
            -> select(['users.name as username','integral.id','integral_type.name as typename','user_id','count','integral.describe','integral.create_time','integral.update_time'])
            -> paginate(10);
        return view("/admin/finance/integral",['data'=>$data]);
    }
    // 新增 and 修改 感恩币
    public function integralChange(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            // 判断跳转新增界面，还是跳转修改界面
            if(empty($all['id'])){
                // 链接数据库，获取感恩币类型
                $type =DB::table("integral_type") -> get();
                $data = (object)[
                    'type_id' => ""
                ];
                // 跳转新增界面
                $arr = [
                    'type' => $type,
                    'data' => $data
                ];
                return view("/admin/finance/integralChange",$arr);
            }else{
                // 跳转修改界面
                // 链接数据库，获取感恩币类型
                $type =DB::table("integral_type") -> get();
                // 根据传入的id，查询数据库中的值
                $data = DB::table("integral") -> where("id",$all['id']) -> first();
                // 定义一个数组，用于上传数据
                $arr = [
                    'type' => $type,
                    'data' => $data
                ];
                return view("/admin/finance/integralChange",$arr);
            }
        }else{
            // 判断执行新增操作，还是执行修改操作
            if(empty($all['id'])){
                // 执行新增操作
                // 获取提交的数据
                $data = [
                    'type_id' => $all['type_id'],
                    'user_id' => Auth::id(),
                    'count' => $all['count'],
                    'describe' => $all['describe'],
                    'create_time' => date("Y-m-d H:i:s")
                ];
                // 链接数据库，执行新增操作
                $i = DB::table("integral") -> insert($data);
                if($i){
                    flash('新增成功') -> success();
                    return redirect()->route('finance.integral');
                }else{
                    flash('新增失败') -> error();
                    return redirect()->route('finance.integral');
                }
            }else{
                // 执行修改操作
                // 获取提交的内容
                $data = [
                    'type_id' => $all['type_id'],
                    'count' => $all['count'],
                    'describe' => $all['describe'],
                    'update_time' => date("Y-m-d H:i:s")
                ];
                // 链接数据库，执行修改操作
                $i = DB::table("integral") -> where('id',$all['id']) -> update($data);
                if($i){
                    flash('修改成功') -> success();
                    return redirect()->route('finance.integral');
                }else{
                    flash('修改失败') -> error();
                    return redirect()->route('finance.integral');
                }
            }
        }
    }
    // 删除感恩币信息
    public function integralDel(){
        // 获取提交的id
        $all = \request() -> all();
        $data = [
            'is_del' => 1
        ];
        // 链接数据库，删除数据
        $i = DB::table("integral") -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('finance.integral');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('finance.integral');
        }
    }

    // 感恩币类型
    public function integral_type(){
        // 链接数据库，查询感恩币类型表中的数据
        $data = DB::table("integral_type") -> where('is_del',0) -> paginate(10);
        return view('/admin/finance/integral_type',['data'=>$data]);
    }
    // 新增 and 修改 感恩币类型
    public function integral_typeChange(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            // 判断跳转新增界面，还是跳转修改界面
            if(empty($all['id'])){
                // 跳转新增界面
                return view("/admin/finance/integral_typeChange");
            }else{
                // 跳转修改界面
                // 根据传入的id 查询数据库中的值
                $data = DB::table("integral_type") -> where('id',$all['id']) ->first();
                return view('/admin/finance/integral_typeChange',['data'=>$data]);
            }
        }else{
            // 判断执行新增操作，还是执行修改操作
            if(empty($all['id'])){
                // 执行新增操作
                // 获取提交的数据
                $data = [
                    'name' => $all['name'],
                    'describe' => $all['describe'],
                    'num' => $all['num'],
                    'create_time' => date("Y-m-d H:i:s")
                ];
                // 链接数据库，执行新增操作
                $i = DB::table("integral_type") -> insert($data);
                if($i){
                    flash('新增成功') -> success();
                    return redirect()->route('finance.integral_type');
                }else{
                    flash('新增失败') -> error();
                    return redirect()->route('finance.integral_type');
                }
            }else{
                // 执行修改操作
                // 获取提交的数据
                $data = [
                    'name' => $all['name'],
                    'describe' => $all['describe'],
                    'num' => $all['num'],
                    'update_time' => date("Y-m-d H:i:s")
                ];
                // 链接数据库，执行修改操作
                $i = DB::table("integral_type") -> where('id',$all['id']) -> update($data);
                if($i){
                    flash('修改成功') -> success();
                    return redirect()->route('finance.integral_type');
                }else{
                    flash('修改失败') -> error();
                    return redirect()->route('finance.integral_type');
                }
            }
        }
    }
    // 删除感恩币类型
    public function integral_typeDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 链接数据库，根据传入的id，删除数据
        $data = [
            'is_del' => 1
        ];
        $i = DB::table('integral_type') -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('finance.integral_type');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('finance.integral_type');
        }
    }

    // 感恩币明细
    public function integral_record(){
        // 判断是否有id传过来
        $all = \request() -> all();
        if(!empty($all['id'])){
            // 如果有id传入，则根据这个id查询，明细表中的数据
            $data = DB::table("cashlogs")
                -> join("users","cashlogs.user_id","=","users.id")
                -> where('cashlogs.is_del',0)
                -> where('cashlogs.type_id',1)
                -> where('cashlogs.user_id',$all['id'])
                -> select(['cashlogs.id','users.name','cashlogs.describe','cashlogs.state','cashlogs.price','cashlogs.create_time'])
                -> paginate(10);
        }else{
            $data = DB::table("cashlogs")
                -> join("users","cashlogs.user_id","=","users.id")
                -> where('source',0)
                -> where('cashlogs.is_del',0)
                -> where('cashlogs.type_id',1)
                -> select(['cashlogs.id','users.name','cashlogs.describe','cashlogs.state','cashlogs.price','cashlogs.create_time'])
                -> paginate(10);
        }
        // 链接数据库，查询感恩币明细
        return view('/admin/finance/integral_record',['data'=>$data]);
    }
    // 新增 and 修改 感恩币明细
    public function integral_recordChange(){
        $all = \request() -> all();
        // 判断跳转，还是执行
        if(\request() -> isMethod("get")){
            // 判断跳转新增界面，还是跳转修改界面
            if(empty($all['id'])){
                // 跳转新增界面
                return view('/admin/finance/integral_recordChange');
            }else{
                // 跳转修改界面
                return "update";
            }
        }else{
            // 判断执行新增方法，还是执行修改方法
            if(empty($all['id'])){
//                $id = 6;
                $id = Auth::id();
                // 执行新增方法
                // 判断财务状况
                if($all['state'] == 1){
                    // 获得感恩币
                    // 计算感恩币总值
                    $a = $all['price'];     // 当前获取的值
                    // 获取感恩币总表中的 感恩币值
                    $integral = DB::table("integral") -> where('user_id',$id) ->first();
                    // 将获取的数据转换成数组
                    $arr = json_decode(json_encode($integral),true);
                    $num = $arr['count']+$a;
                    // 修改感恩币总表中的值
                    $inte = [
                        'count' => $num
                    ];
                }else{
                    // 消耗感恩币
                    // 计算感恩币总值
                    $a = $all['price'];     // 当前获取的值
                    // 获取感恩币总表中的 感恩币值
                    $integral = DB::table("integral") -> where('user_id',$id) ->first();
                    // 将获取的数据转换成数组
                    $arr = json_decode(json_encode($integral),true);
                    $num = $arr['count']-$a;
                    // 修改感恩币总表中的值
                    $inte = [
                        'count' => $num
                    ];
                }
                DB::beginTransaction();
                try{
                    // 链接感恩币总表数据库
                    DB::table("integral") -> where('user_id',$id) -> update($inte);
                    // 获取提交的值
                    $data = [
                        'user_id' => $id,
                        'merchant_id' => $id,
                        'price' => $all['price'],
                        'describe' => $all['describe'],
                        'create_time' => date("Y-m-d H:i:s"),
                        'state' => $all['state'],
                        'type_id' => 1
                    ];
                    // 链接数据库执行新增操作
                    $i = DB::table("cashlogs") -> insert($data);
                    if($i){
                        DB::commit();
                        flash('新增成功') -> success();
                        return redirect()->route('finance.integral_record');
                    }else{
                        DB::rollBack();
                        flash('新增失败') -> error();
                        return redirect()->route('finance.integral_record');
                    }
                }catch (Exception $e){
                    DB::rollBack();
                }

            }else{
                // 执行修改方法
                return "doUpdate";
            }
        }
    }
    // 删除明细
    public function integral_recordDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 链接数据库，根据传入的id，删除数据
        $data = [
            'is_del' => 1
        ];
        $i = DB::table('integral_record') -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('finance.integral_record');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('finance.integral_record');
        }
    }







}
