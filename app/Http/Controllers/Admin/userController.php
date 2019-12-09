<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Repositories\RulesRepository;
use App\Handlers\Tree;
use Auth;
class UserController extends BaseController
{
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