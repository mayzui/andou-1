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
    public function list()
    {
        $data=Db::table('coupons')->paginate(20);
        return $this->view('',['data'=>$data]);
    }

    public function create ()
    {

    }

    public function list_change(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            if(empty($all['id'])){
                // 跳转新增界面
                return $this->view('');
            }else{
                // 跳转修改界面
                // 根据提交的id 查询数据库的值
                $data = DB::table('coupons') -> where('id',$all['id']) -> first();
//                str_replace(' ', 'T', $date->format('Y-m-d H:i:00'));
                $data -> start_at = str_replace(' ', 'T', $data -> start_at);
                $data -> end_at = str_replace(' ', 'T', $data -> end_at);
                return $this->view('',['data' => $data]);
            }
        }else{
            if(empty($all['id'])){
                // 执行新增方法
                $data = [
                    'coupon_name' => $all['coupon_name'],
                    'coupon_type_id' => $all['coupon_type_id'],
                    'start_at' => $all['start_at'],
                    'end_at' => $all['end_at'],
                    'max_mun' => $all['max_mun'],
                    'rest_num' => $all['max_mun'],
                    'money' => $all['money']
                ];
                $i = DB::table('coupons') -> insert($data);
                if($i){
                    flash('新增成功')->success();
                    return redirect()->route('coupon.list');
                }else{
                    flash('新增失败')->error();
                    return redirect()->route('coupon.list');
                }
            }else{
                // 执行修改方法
                $data = [
                    'coupon_name' => $all['coupon_name'],
                    'coupon_type_id' => $all['coupon_type_id'],
                    'start_at' => $all['start_at'],
                    'end_at' => $all['end_at'],
                    'max_mun' => $all['max_mun'],
                    'money' => $all['money']
                ];
                $i = DB::table('coupons') -> where('id',$all['id']) -> update($data);
                if($i){
                    flash('修改成功')->success();
                    return redirect()->route('coupon.list');
                }else{
                    flash('修改失败，请稍后重试')->error();
                    return redirect()->route('coupon.list');
                }
            }
        }
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
