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
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
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
            $data=DB::table('merchants')
                -> where('user_id',$id)
                -> where($where)
                -> paginate(10);
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
        }else{
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
            $data=DB::table('merchants')
                ->where($where)
                ->paginate(10);
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
        }
        return $this->view('',['data'=>$data,'i'=>$i],['wheres'=>$wheres]);
    }

    // 查看详情
    public function information(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            // 通过传入的id 查询商户信息
            $data = DB::table('merchants')
                -> join('merchant_type','merchants.merchant_type_id','=','merchant_type.id')
                -> where('merchants.id',$all['id'])
                -> select(['merchant_type.type_name','merchants.id',
                    'merchants.name','merchants.desc',
                    'merchants.province_id','merchants.city_id',
                    'merchants.area_id','merchants.address',
                    'merchants.tel','merchants.user_name',
                    'merchants.management_type','merchants.management_type',
                    'merchants.banner_img','merchants.logo_img',
                    'merchants.door_img','merchants.management_img',
                    'merchants.goods_img','merchants.merchant_type_id'])
                -> first();
//            return dd($data);
            return $this->view('',['data'=>$data]);
        }else{
            if(!empty($all['management_type'])){
                // 获得提交的内容
                $data = [
                    'name' => $all['name'],
                    'user_name' => $all['user_name'],
                    'tel' => $all['tel'],
                    'province_id' => $all['province_id'],
                    'city_id' => $all['city_id'],
                    'area_id' => $all['area_id'],
                    'address' => $all['address'],
                    'return_address' => $all['return_address'],
                    'desc' => $all['desc'],
                    'banner_img' => $all['banner_img'],
                    'logo_img' => $all['logo_img'],
                    'door_img' => $all['door_img'],
                    'management_img' => $all['management_img'],
                    'goods_img' => $all['goods_img'],
                    'management_type' => $all['management_type'],
                    'updated_at' => date("Y-m-d H:i:s")
                ];
            }else{
                // 获得提交的内容
                $data = [
                    'name' => $all['name'],
                    'user_name' => $all['user_name'],
                    'tel' => $all['tel'],
                    'province_id' => $all['province_id'],
                    'city_id' => $all['city_id'],
                    'area_id' => $all['area_id'],
                    'address' => $all['address'],
                    'return_address' => $all['return_address'],
                    'desc' => $all['desc'],
                    'banner_img' => $all['banner_img'],
                    'logo_img' => $all['logo_img'],
                    'door_img' => $all['door_img'],
                    'management_img' => $all['management_img'],
                    'goods_img' => $all['goods_img'],
                    'updated_at' => date("Y-m-d H:i:s")
                ];
            }
            // 根据传入的id 修改商户信息
            $i = DB::table('merchants') -> where('id',$all['id']) -> update($data);
            if($i){
                flash("商户信息修改成功") -> success();
                return redirect()->route('merchants.index');
            }else{
                flash("更新失败，请稍后重试") -> error();
                return redirect()->route('merchants.index');
            }
        }
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
            $save['remak']=$all['remak'];
            $save['role_id']=$all['role_id'];
            if(!empty(request()->file('img'))){
                $file[0]=request()->file('img');
                $save['img']=$this->uploads($file);
            }
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
                $data->img='';
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