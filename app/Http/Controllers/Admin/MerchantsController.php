<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Admin\RoleRequest;
use App\Models\Role;
use App\Repositories\RulesRepository;
use App\Handlers\Tree;
class MerchantsController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    /**商户列表
     * [index description]
     * @return [type] [description]
     */
    public function index()
    {   
        $all = request()->all();
        $where[]=['id','>','0'];
        if (!empty($all['merchant_type_id'])) {
           $where[]=['merchant_type_id',$all['merchant_type_id']];
           $screen['merchant_type_id'] = $all['merchant_type_id'];
        }else{
           $screen['merchant_type_id']='';
        }
        if (!empty($all['name'])) {
           $where[]=['name', 'like', '%'.$all['name'].'%'];
           $screen['name']=$all['name']; 
        }else{
           $screen['name']=''; 
        }
        $data=DB::table('merchants')->where($where)->paginate(10);

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
        $wheres['type']=DB::table('merchant_type')->get();
        $wheres['where']=$screen;
        return $this->view('',['data'=>$data],['wheres'=>$wheres]);
    }
    /**修改商户状态
     * [reg description]
     * @return [type] [description]
     */
    public function reg()
    {
        $all = request()->all();
        $save['is_reg']=$all['is_reg'];
        $id=$all['id'];
        if(empty($all['url'])){
            $url='merchants.index';
        }else{
            $url=$all['url']; 
        }
        $data=Db::table('merchants')->where('id',$id)->first();
        
        if($save['is_reg']==1 && !empty($data)){
            $res['allow_in']=1;
            $res['status']=1;
            $re=Db::table('users')->where('id',$data->user_id)->update($res);
            $role=Db::table('merchant_type')->where('id',$data->merchant_type_id)->first();
            
            $datas['role_id']=$role->role_id;
            $datas['user_id']=$data->user_id;
            $datas['created_at']=date('Y-m-d H:i:s',time());
            $datas['updated_at']=date('Y-m-d H:i:s',time());
            $ress=Db::table('user_role')->insert($datas);
        }else if($save['is_reg']==0 && !empty($data)){
            $res['allow_in']=0;
            $res['status']=0;
            $re=Db::table('users')->where('id',$data->user_id)->update($res);
            $role=Db::table('merchant_type')->where('id',$data->merchant_type_id)->first();
            $when['role_id']=$role->role_id;
            $when['user_id']=$data->user_id;
            $ress=Db::table('user_role')->where($when)->delete();
        }
        $re=Db::table('merchants')->where('id',$id)->update($save);
        
        if ($re) {
            flash('修改成功')->success();
            return redirect()->route($url);
        }else{
            flash('修改失败')->error();
            return redirect()->route($url);
        }
    }
    /**商户分类列表
     * [merchantType description]
     * @return [type] [description]
     */
    public function merchantType()
    {   
        $data=DB::table('merchant_type')->where('status',1)->paginate(20);
        return $this->view('',['data'=>$data]);
    }
    /**新增修改商户分类
     * [merchantTypeAdd description]
     * @return [type] [description]
     */
    public function merchantTypeAdd()
    {   
        $all = request()->all();
        if (request()->isMethod('post')) {
            $save['type_name']=$all['type_name'];
            $save['has_children']=$all['has_children'];
            $save['role_id']=$all['role_id'];
            if (empty($all['id'])) {
                $re=Db::table('merchant_type')->insert($save);
            }else{
                $re=Db::table('merchant_type')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('修改成功')->success();
                return redirect()->route('merchants.merchant_type');
            }else{
                flash('修改失败')->error();
                return redirect()->route('merchants.merchant_type');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                $data->type_name='';
                $data->has_children=0;
                $data->role_id=0;
            }else{
                $data=Db::table('merchant_type')->where('id',$all['id'])->first();
            }
            $role=Db::table('roles')->get();
            return $this->view('',['data'=>$data],['role'=>$role]);
        }
    }
    /**删除商户分类
     * [del description]
     * @param string $value [description]
     */
    public function del()
    {
        $all = request()->all();
        $id=$all['id'];
        $re=Db::table('merchant_type')->where('id',$id)->update(array('status'=>0));
        flash('删除成功')->success();
        return redirect()->route('merchants.merchant_type');
    }

    /**商户行业列表
     * [industry description]
     * @return [type] [description]
     */
    public function industry()
    {   
        $data=DB::table('merchant_industry')->where('status',1)->paginate(20);
        return $this->view('',['data'=>$data]);
    }
    /**新增修改商户行业
     * [industryAdd description]
     * @return [type] [description]
     */
    public function industryAdd()
    {   
        $all = request()->all();
        if (request()->isMethod('post')) {
            $save['name']=$all['name'];
            if (empty($all['id'])) {
                $re=Db::table('merchant_industry')->insert($save);
            }else{
                $re=Db::table('merchant_industry')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('修改成功')->success();
                return redirect()->route('merchants.industry');
            }else{
                flash('修改失败')->error();
                return redirect()->route('merchants.industry');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                $data->name='';
            }else{
                $data=Db::table('merchant_industry')->where('id',$all['id'])->first();
            }
            return $this->view('',['data'=>$data]);
        }
    }
    /**删除商户行业
     * [industryDel description]
     * @param string $value [description]
     */
    public function industryDel()
    {
        $all = request()->all();
        $id=$all['id'];
        $re=Db::table('merchant_industry')->where('id',$id)->update(array('status'=>0));
        flash('删除成功')->success();
        return redirect()->route('merchants.industry');
    }
}