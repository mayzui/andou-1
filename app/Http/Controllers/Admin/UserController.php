<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Repositories\RulesRepository;
use App\Handlers\Tree;
use Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends BaseController
{
    // 用户流水
    public function cashLogs(){
        // 链接数据库，查询数据库中，用户流水表
        $data = DB::table('user_logs')
            -> join("users","user_logs.user_id","=","users.id")
            -> where('source',0)
            -> where('user_logs.is_del',0)
            -> orderBy('type_id')
            -> select(['user_logs.id','users.name','user_logs.price','user_logs.describe','user_logs.state','user_logs.type_id','user_logs.create_time'])
            -> paginate(10);
        return $this->view('',['data'=>$data]);
    }
    // 新增用户流水
    public function cashLogsChange(){
        if(\request() -> isMethod("get")){
            // 跳转新增界面
            return $this->view('');
        }else{
            // 执行新增操作
            $id = Auth::id();
            // 获取提交的数据
            $all = \request() -> all();
            $data = [
              'user_id' => $id,
              'price' => $all['price'],
              'describe' => $all['describe'],
              'type_id' => $all['type_id'],
              'state' => $all['state'],
              'create_time' => date("Y-m-d H:i:s")
            ];
            // 链接数据库，执行新增操作
            $i = DB::table('user_logs') -> insert($data);
            if ($i) {
                flash('新增成功')->success();
                return redirect()->route('user.cashLogs');
            }else{
                flash('新增失败')->error();
                return redirect()->route('user.cashLogs');
            }
        }
    }
    // 删除用户流水
    public function cashLogsDel(){
        // 获取提交的id
        $all = \request() -> all();
        // 根据获取的id，删除数据库中的内容
        $data = [
            'is_del' => 1
        ];
        // 执行删除操作
        $i = DB::table('user_logs') -> where('id',$all['id']) -> update($data);
        if ($i) {
            flash('删除成功')->success();
            return redirect()->route('user.cashLogs');
        }else{
            flash('删除失败')->error();
            return redirect()->route('user.cashLogs');
        }
    }
    // 用户提现流水
    public function cashOut(){
        // 查询数据库内容
        $data = DB::table('user_logs')
            -> join("users","user_logs.user_id","=","users.id")
            -> where('source',0)
            -> where('user_logs.is_del',0)
            -> where('user_logs.type_id',3)
            -> orderBy('type_id')
            -> select(['user_logs.id','users.name','user_logs.price','user_logs.describe','user_logs.state','user_logs.type_id','user_logs.create_time'])
            -> paginate(10);
        return $this->view('',['data'=>$data]);
    }
    // 用户充值流水
    public function charge(){
        // 查询数据库内容
        $data = DB::table('user_logs')
            -> join("users","user_logs.user_id","=","users.id")
            -> where('source',0)
            -> where('user_logs.is_del',0)
            -> where('user_logs.type_id',2)
            -> orderBy('type_id')
            -> select(['user_logs.id','users.name','user_logs.price','user_logs.describe','user_logs.state','user_logs.type_id','user_logs.create_time'])
            -> paginate(10);
        return $this->view('',['data'=>$data]);
    }
    // 用户积分流水
    public function integralLog(){
        // 查询数据库内容
        $data = DB::table('user_logs')
            -> join("users","user_logs.user_id","=","users.id")
            -> where('source',0)
            -> where('user_logs.is_del',0)
            -> where('user_logs.type_id',1)
            -> orderBy('type_id')
            -> select(['user_logs.id','users.name','user_logs.price','user_logs.describe','user_logs.state','user_logs.type_id','user_logs.create_time'])
            -> paginate(10);
        return $this->view('',['data'=>$data]);
    }

    // 用户列表
    public function user_list(){
        // 链接数据库，查询用户表
        $data = DB::table("users")
            ->leftJoin('vip','users.id','=','vip.user_id')
            ->select('users.id','users.name','users.mobile','users.created_at','users.updated_at','vip.grade')
            -> where("users.source",0)
            -> where('users.is_del',0)
            -> paginate(10);
//        dd($data);die;
        return $this->view('',['data'=>$data]);
    }
    // 新增 用户
    public function user_listChange(Request $request){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            // 跳转新增界面
            return $this->view('');
        }else{
            // 执行新增操作
            $avator = "";
            $avator = $all['img'];
            // 获取提交的数据
            $data = [
                'name' => $all['name'],
                'password' => Hash::make($all['password']),
                'mobile' => $all['mobile'],
                'avator' => $avator,
                'allow_in' => 0,
                'status' => 0,
                'created_at' => date("Y-m-d H:i:s"),
                'source' => 0
            ];
            // 链接数据库，执行新增操作
            $i = DB::table('users') -> insert($data);
            if ($i) {
                flash('新增成功')->success();
                return redirect()->route('user.user_list');
            }else{
                flash('新增失败')->error();
                return redirect()->route('user.user_list');
            }
        }
    }
    // 删除用户列表
    public function user_listDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 根据id ，链接数据库，删除数据
        $data = [
            'is_del' => 1
        ];
        // 执行删除操作
        $i = DB::table('users') -> where('id',$all['id']) -> update($data);
        if ($i) {
            flash('删除成功')->success();
            return redirect()->route('user.user_list');
        }else{
            flash('删除失败')->error();
            return redirect()->route('user.user_list');
        }
    }


     public function merchant()
     {
        $id = Auth::guard('admin')->user()->id;
        $data=DB::table('merchants')->where('user_id',$id)->paginate(10);
        foreach ($data as $key => $value) {
            $merchant_type=Db::table('merchant_type')->where('id',$value->merchant_type_id)->pluck('type_name');
            if (!empty($merchant_type[0])) {
                $data[$key]->merchant_type_id=$merchant_type[0];
            }else{
                $data[$key]->merchant_type_id='';
            }
            $username=Db::table('users')->where('id',$value->user_id)->pluck('name');
            if (!empty($username[0])) {
                $data[$key]->username=$username[0];
            }else{
                $data[$key]->username='';
            }
        }
       
        return $this->view('',['data'=>$data]);
     }

     public function merchantUpdate()
     {
        $all = request()->all();
        if (request()->isMethod('post')) {
            $id=$all['id'];
            $all['updated_at']=date('Y-m-d H:i:s',time());
            // echo date('Y-m-d H:i:s',time());exit();
            if(!empty(request()->file('file'))){
                $file[0]=request()->file('file');
                $all['logo_img']=$this->uploads($file);
                unset($all['file']);
            }
            unset($all['id']);
            unset($all['_token']);
            $re=Db::table('merchants')->where('id',$id)->update($all);
            if ($re) {
                flash('修改成功')->success();
                return redirect()->route('user.merchant');
            }else{
                flash('修改失败')->error();
                return redirect()->route('user.merchant');
            }
        }else{
            $configure['type']=Db::table('merchant_type')->get();
            $data=Db::table('merchants')->where('id',$all['id'])->first();
            $configure['province']=Db::table('districts')->where('pid',0)->get();
            if($data->province_id > 0){
                $configure['city']=Db::table('districts')->where('pid',$data->province_id)->get();
            }else{
                $configure['city']=array();
            }
            if ($data->city_id > 0) {
                $configure['area']=Db::table('districts')->where('pid',$data->city_id)->get();
            }else{
                $configure['area']=array();
            }
            return $this->view('',['data'=>$data],['configure'=>$configure]);
        }
     }
     public function address(){
        $all = request()->all();
        $pid=$all['id'];
        $data=Db::table('districts')->where('pid',$pid)->get();
        return json_encode(array('code'=>200,'data'=>$data),1);
     }
}