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

class FinanceController extends Controller
{
    /*
     *      感恩币中心
     * */
    public function integral ()
    {
        // 查询数据库，感恩币总表的数据
        $data = DB::table("integral")
            -> join("integral_type","integral.type_id","=","integral_type.id")
            -> join("users","integral.user_id","=","users.id")
            -> where('source',0)
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
            $data = DB::table("integral_record")
                -> join("users","integral_record.user_id","=","users.id")
                -> where('source',0)
                -> where('is_del',0)
                -> where('integral_record.user_id',$all['id'])
                -> select(['integral_record.id','users.name','integral_record.describe','integral_record.num','integral_record.create_time'])
                -> paginate(10);
        }else{
            $data = DB::table("integral_record")
                -> join("users","integral_record.user_id","=","users.id")
                -> where('source',0)
                -> where('is_del',0)
                -> select(['integral_record.id','users.name','integral_record.describe','integral_record.num','integral_record.create_time'])
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
                // 执行新增方法
                // 获取提交的值
                $data = [
                    'user_id' => Auth::id(),
                    'describe' => $all['describe'],
                    'num' => $all['num'],
                    'create_time' => date("Y-m-d H:i:s")
                ];
                // 链接数据库执行新增操作
                $i = DB::table("integral_record") -> insert($data);
                if($i){
                    flash('新增成功') -> success();
                    return redirect()->route('finance.integral_record');
                }else{
                    flash('新增失败') -> error();
                    return redirect()->route('finance.integral_record');
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

    public function cashOut ()
    {
        return "提现管理，模块开发中... ...";
    }

    public function cashLogs ()
    {
        return "平台流水，模块开发中... ...";
    }

    public function charge ()
    {
        return "充值中心，模块开发中... ...";
    }




}
