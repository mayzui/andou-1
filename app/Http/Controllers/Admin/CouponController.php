<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class CouponController extends BaseController
{
    public function list ()
    {
        $data=Db::table('coupons')->paginate(20);
        return $this->view('',['data'=>$data]);
    }

    public function create ()
    {

    }

    public function update ()
    {
        $all = request()->all();
        if (request()->isMethod('post')) {
            $save['coupon_name']=$all['coupon_name'];
            $save['start_at']=$all['start_at'];
            $save['end_at']=$all['end_at'];
            $save['max_mun']=$all['max_mun'];
            $save['rest_num']=$all['max_mun'];
            if(!empty(request()->file('file'))){
                $file[0]=request()->file('file');
                $save['img']=$this->uploads($file);
                unset($all['file']);
            }
            if (empty($all['id'])) {
                $save['status']=0;
                $save['created_at']=date('Y-m-d H:i:s',time());
                $re=Db::table('coupons')->insert($save);
            }else{
                $re=Db::table('coupons')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('编辑成功')->success();
                return redirect()->route('coupon.list');
            }else{
                flash('编辑失败')->error();
                return redirect()->route('coupon.list');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                $data->type_name='';  
            }else{
                $data=Db::table('coupons')->where('id',$all['id'])->first();
            }
            return $this->view('',['data'=>$data]);
        }
    }

    public function delete ()
    {
        $all= request()->all();
        $save['status']=$all['status'];
        $id=$all['id'];
        $re=Db::table('coupons')->where('id',$id)->update($save);
        flash('删除成功')->success();
        return redirect()->route('coupon.list');
    }

    public function useLog ()
    {

    }

    public function getLog ()
    {

    }

}
