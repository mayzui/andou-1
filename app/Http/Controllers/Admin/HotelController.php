<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\HotelRequest;
use App\Services\ActionLogsService;
use Illuminate\Http\Request;
use App\Services\HotelService;
use App\Repositories\HotelRepository;
use Auth;

class HotelController extends BaseController
{
    /**房间列表
     * [index description]
     * @return [type] [description]
     */
    public function index ()
    {   
        $admin = Auth::guard('admin')->user();
        // foreach ($admin->roles as $key => $value) {
        //     $role[]=$value->id;
        // }
        $user_id=$admin->id;
        $datas=Db::table('merchants')->where('user_id',$user_id)->first();
        if (!empty($datas)) {
            $role=$datas->id;
            $data=Db::table('hotel_room')->where('user_id',$user_id)->paginate(20);
         }else{
            $role=0;
            $data=Db::table('hotel_room')->paginate(20);
        } 
        foreach ($data as $key => $value) {
            $data[$key]->merchant_id=Db::table('merchants')->where('id',$value->merchant_id)->pluck('name')[0];
            $data[$key]->user_id=Db::table('users')->where('id',$value->user_id)->pluck('name')[0];
        }
        return $this->view('',['data'=>$data],['role'=>$role]);
    }
    /**添加修改房间
     * [add description]
     * @param string $value [description]
     */
    public function add($value='')
    {
        $all = request()->all();
        $admin = Auth::guard('admin')->user();
        if (request()->isMethod('post')) {

            $save['house_name']=$all['house_name'];
            if (!empty($all['desc'])) {
                $save['desc']=implode(',',$all['desc']);
            }
            $save['price']=$all['price'];
            $save['updated_at']=date('Y-m-d H:i:s',time());
            $data['areas']=$all['areas'];
            $data['has_window']=$all['has_window'];
            $data['wifi']=$all['wifi'];
            $data['num']=$all['num'];
            $data['has_breakfast']=$all['has_breakfast'];
            $data['bed_type']=$all['bed_type'];
            $data['other_sets']=$all['other_sets'];
            if (empty($all['imgs']) && empty($all['id'])){
               return json_encode(array('code'=>201,'msg'=>'缺少图片信息')); 
            }else{
                if (!empty($all['imgs'])) {
                    $files=$all['imgs'];
                    $count=count($files);
                    $msg=array();
                    // var_dump($files);exit;
                    foreach ($files as $k=>$v){
                        $type = $v->getClientOriginalExtension();
                        $path=$v->getPathname();
                        if($type == "png" || $type == "jpg"){
                            $newname = 'uploads/'.date ( "Ymdhis" ).rand(0,9999);
                            $url = $newname.'.'.$type;
                            $upload=move_uploaded_file($path,$url);
                            $msg[]=$url;
                        }else{
                           return json_encode(array('code'=>201,'msg'=>'文件格式错误'));  
                        }
                    }
                    $save['img']=implode(',',$msg);
                }
            }
            if (empty($all['id'])) {
                $save['user_id']=$admin->id;
                $save['merchant_id']=Db::table('merchants')->where('user_id',$admin->id)->pluck('id')[0];
                $save['status']=0;
                $save['created_at']=date('Y-m-d H:i:s',time());
                $ids=Db::table('hotel_room')->insertGetId($save);
                $data['hotel_room_id']=$ids;
                $re=Db::table('hotel_attr_value')->insertGetId($data);
            }else{
                $re=Db::table('hotel_room')->where('id',$all['id'])->update($save);
                $res=Db::table('hotel_attr_value')->where('hotel_room_id',$all['id'])->update($data);
            }
            if ($re) {
                return json_encode(array('code'=>200,'msg'=>'编辑成功'));  
            }else{
                return json_encode(array('code'=>201,'msg'=>'编辑失败'));
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                $data->desc=array();
                $data->has_breakfast=1;
                $data->wifi=1;
                $data->has_window=1;  
            }else{
                $data=Db::table('hotel_room as hr')
                ->select('hr.*','hv.id as tid','hv.*')
                ->join('hotel_attr_value as hv','hr.id','=','hv.hotel_room_id')
                ->where('hr.id',$all['id'])
                ->first();
                if (empty($data->desc)) {
                    $data->desc=array();
                }else{
                    $data->desc=explode(',',$data->desc);
                }
            }
            $desc=Db::table('hotel_faci')->get();
            return $this->view('',['data'=>$data],['desc'=>$desc]);
        }
    }
    /**删除房间
     * [del description]
     * @param string $value [description]
     */
    public function del()
    {
        $all = request()->all();
        $id=$all['id'];
        $re=Db::table('hotel_room')->where('id',$id)->delete();
        $res=Db::table('hotel_attr_value')->where('hotel_room_id',$id)->delete();
        flash('删除成功')->success();
        return redirect()->route('hotel.index');
    }
    /**删除配置
     * [faciDel description]
     * @param string $value [description]
     */
    public function faciDel()
    {
        $all = request()->all();
        $id=$all['id'];
        $where[]=['desc', 'like', '%'.$id.'%']; 
        $res=Db::table('hotel_room')->where($where)->first();
        if (!empty($res)) {
            flash('该配置有房间使用不能删除')->error();
            return redirect()->route('hotel.faci'); 
        }
        $re=Db::table('hotel_faci')->where('id',$id)->delete();
        // $res=Db::table('hotel_attr_value')->where('hotel_room_id',$id)->delete();
        flash('删除成功')->success();
        return redirect()->route('hotel.faci');
    }
    /**修改房间状态
     * [status description]
     * @return [type] [description]
     */
    public function status()
    {
        $all = request()->all();
        $save['status']=$all['status'];
        $id=$all['id'];
        $re=Db::table('hotel_room')->where('id',$id)->update($save);
        if ($re) {
            flash('修改成功')->success();
            return redirect()->route('hotel.index');
        }else{
            flash('修改失败')->error();
            return redirect()->route('hotel.index');
        }
    }
    public function faci()
    {
        $data=Db::table('hotel_faci')->paginate(20);
        return $this->view('',['data'=>$data]);
    }
    /**新增修改酒店配置
     * [faciAdd description]
     * @return [type] [description]
     */
    public function faciAdd()
    {   
        $all = request()->all();
        if (request()->isMethod('post')) {
            $save['name']=$all['name'];
            if (empty($all['id'])) {
                $re=Db::table('hotel_faci')->insert($save);
            }else{
                $re=Db::table('hotel_faci')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('编辑成功')->success();
                return redirect()->route('hotel.faci');
            }else{
                flash('编辑失败')->error();
                return redirect()->route('hotel.faci');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];
                $data->type_name='';  
            }else{
                $data=Db::table('hotel_faci')->where('id',$all['id'])->first();
            }
            return $this->view('',['data'=>$data]);
        }
    }
    public function goods()
    {
        dd(123);

        return $this->view(null);
    }

    public function goodsCate ()
    {
        dd(123);

        return $this->view(null);
    }

    public function orders ()
    {
        dd(123);

        return $this->view(null);
    }

    public function goodsBrand ()
    {
        dd(123);

        return $this->view(null);
    }
}
