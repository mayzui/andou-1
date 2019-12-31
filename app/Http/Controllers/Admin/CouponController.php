<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Uselog;
use App\Models\Getlog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
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

    //优惠券领取记录展示
    public function useLog ()
    {
        $data = DB::table('uselog')
            -> join('users','uselog.user_id','=','users.id')
            -> select(['uselog.id','uselog.coupon_name','uselog.coupon_type_id','uselog.start_at','uselog.end_at','users.name'])
            -> paginate(10);
        return $this->view('useLog',['data'=>$data]);
    }
    /*
    *添加测试数据
    */
    public function uselogAdd ()
    {
        return $this->view('uselogAdd');
    }

    public function uselogAdds ()
    {
        $input = request()->all();
        $data = [
            'user_id'=>$input['user_id'],
            'coupon_name'=>$input['coupon_name'],
            'coupon_type_id'=>$input['coupon_type_id'],
            'start_at'=>date('Y-m-d H:i:s',time()),
            'end_at'=>date('Y-m-d H:i:s',time()+24*3600),
        ];
        $res = DB::table('uselog')->insert($data);
        if($res){
            flash('编辑成功')->success();
            return redirect()->route('coupon.useLog');
        }else{
            flash('编辑失败')->error();
            return redirect()->route('coupon.useLog');
        }
    }

    //删除优惠券领取记录
    public function useLogDel ()
    {
        $id = input::get('id');
        $res = Uselog::where('id',$id)->delete();
        if ($res){
            return redirect()->route('coupon.useLog');
        }
        return viewError('已删除或者删除失败');
    }

    public function getLog ()
    {
        $data = DB::table('getlog')->paginate(10);
        return $this->view('getLog',['data'=>$data]);
    }

    public function getLogDel ()
    {
        $id = input::get('id');
        $res = Getlog::where('id',$id)->delete();
        if ($res){
            return redirect()->route('coupon.getLog');
        }
        return viewError('已删除或者删除失败');
    }

}
