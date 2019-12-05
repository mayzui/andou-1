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
        }
        if (!empty($all['name'])) {
           $where[]=['name', 'like', '%'.$all['name'].'%']; 
        }
        $data=DB::table('merchants')->where($where)->paginate(10);
        foreach ($data as $key => $value) {
            $data[$key]->merchant_type_id=Db::table('merchant_type')->where('id',$value->merchant_type_id)->pluck('type_name')[0];
        }
        $type=DB::table('merchant_type')->get();
        return $this->view('',['data'=>$data],['type'=>$type]);
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
        $re=Db::table('merchants')->where('id',$id)->update($save);
        if ($re) {
            flash('修改成功')->success()->important();
            return redirect()->route('merchants.index');
        }else{
            flash('修改失败')->error()->important();
            return redirect()->route('merchants.index');
        }
    }
    /**商户分类列表
     * [merchantType description]
     * @return [type] [description]
     */
    public function merchantType()
    {   
        $data=DB::table('merchant_type')->paginate(20);
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
            if (empty($all['id'])) {
                $re=Db::table('merchant_type')->insert($save);
            }else{
                $re=Db::table('merchant_type')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('修改成功')->success()->important();
                return redirect()->route('merchants.merchant_type');
            }else{
                flash('修改失败')->error()->important();
                return redirect()->route('merchants.merchant_type');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                $data->type_name='';
                $data->has_children=0;
            }else{
                $data=Db::table('merchant_type')->where('id',$all['id'])->first();
            }
            return $this->view('',['data'=>$data]);
        }
    }
}