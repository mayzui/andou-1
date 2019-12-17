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
    /*
     *      酒店评论
     * */
    public function commnets(){
        $id = Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 查询，酒店评论
            $data = DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('hotel_room','order_commnets.goods_id','=','hotel_room.id')     // 链接商品表
                -> where('type',1)
                -> where('merchants_id',$id)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','hotel_room.house_name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);
        }else{
            // 查询，酒店评论
            $data = DB::table('order_commnets')
                -> join('users','order_commnets.user_id','=','users.id')     // 链接用户表
                -> join('hotel_room','order_commnets.goods_id','=','hotel_room.id')     // 链接商品表
                -> where('type',1)
                -> where('order_commnets.is_del',0)
                -> select(['order_commnets.id','users.name as username','hotel_room.house_name as goodsname','stars','order_commnets.content','order_commnets.created_at'])
                -> paginate(10);
        }
        return $this->view('',['data' => $data]);
    }
    // 新增酒店评论
    public function commnetsAdd(){
        $id = Auth::id();
        if(\request() -> isMethod("get")){
            // 查询商品列表
            $goodsData = DB::table("hotel_room") -> get();
            // 跳转新增界面
            return $this->view('',['goodsData'=>$goodsData]);
        }else{
            // 执行新增操作
            // 获取提交的内容
            $all = \request() -> all();
            $data  = [
                'order_id' => $id,
                'user_id' => $id,
                'goods_id' => $all['goods_id'],
                'type' => 1,
                'merchants_id' => $id,
                'stars' => $all['stars'],
                'content' => $all['content'],
                'created_at' => date("Y-m-d H:i:s")
            ];
            // 链接数据库，新增内容
            $i = DB::table('order_commnets') -> insert($data);
            if($i){
                flash('新增成功') -> success();
                return redirect()->route('hotel.commnets');
            }else{
                flash('新增失败') -> error();
                return redirect()->route('hotel.commnets');
            }
        }
    }
    // 删除商品评论
    public function commnetsDel(){
        // 获取传入的id
        $all = \request() -> all();
        // 根据id删除表中数据
        $data = [
            'is_del' => 1
        ];
        $i = DB::table("order_commnets") -> where('id',$all['id']) -> update($data);
        if($i){
            flash('删除成功') -> success();
            return redirect()->route('hotel.commnets');
        }else{
            flash('删除失败') -> error();
            return redirect()->route('hotel.commnets');
        }
    }

    /**房间列表
     * [index description]
     * @return [type] [description]
     */
    public function index ()
    {   $all = request()->all();
        $admin = Auth::guard('admin')->user();
        $where = [];
        if (!empty($all['merchant_id'])) {
            $where[]=['merchant_id',$all['merchant_id']];
        }
        $user_id=$admin->id;
        $datas=Db::table('merchants')->where('user_id',$user_id)->first();
        if (!empty($datas)) {
            $role=$datas->id;
            $where[]=['merchant_id',$datas->id];
         }else{
            $role=0; 
        } 
        $data=Db::table('hotel_room')->where($where)->paginate(20);
        foreach ($data as $key => $value) {
            $data[$key]->merchant_id=Db::table('merchants')->where('id',$value->merchant_id)->pluck('name')[0];
            $data[$key]->user_id=Db::table('users')->where('id',$value->user_id)->pluck('name')[0];
        }
        return $this->view('',['data'=>$data],['role'=>$role]);
    }
    /**事务测试
     * [add description]
     * @param string $value [description]
     */
    public function text()
    {
        DB::beginTransaction(); //开启事务
        $data['status']=0;
        $re=DB::table('users')->where('id','1')->update($data);
        $re2 =DB::table('users')->where('id','2')->update($data);
        if ($re&&$re2) {
            DB::commit();
            echo 1;
        }else{
            DB::rollback();
            echo 0;
        }
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
                    $save['img'] = $this->uploads($all['imgs']);
                     // var_dump($save['img']);exit;
                    if ($save['img'] === 0) {
                        return json_encode(array('code'=>201,'msg'=>'文件格式错误'));
                    }
                    // var_dump($save['img']);exit();
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
                $data->img=array();  
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
                    $data->img=explode(',',$data->img);
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
    /**酒店预定列表
     * [books description]
     * @param  string $value [description]
     * @return [type]        [description]
     */
    public function books($value='')
    {
        $admin = Auth::guard('admin')->user();
        $user_id=$admin->id;
        $datas=Db::table('merchants')->where('user_id',$user_id)->first();
        $where=[];
        if (!empty($datas)) {
            $wheres['role']=$datas->id;
            $where[]=['merchant_id',$datas->id];
            // $data=Db::table('books')->where('merchant_id',$datas->id)->paginate(20);
         }else{
            $wheres['role']=0;    
        }
        $all = request()->all();
        if (!empty($all['merchant_id'])) {
            $where[]=['merchant_id',$all['merchant_id']];
            $wheres['merchant_id']=$all['merchant_id'];
        }
        if(!empty($all['book_sn'])){
            $where[]=['book_sn','like','%'.$all['book_sn'].'%'];
            $wheres['book_sn']=$all['book_sn'];
        }
        $data=Db::table('books')->where($where)->paginate(20);
        if (!empty($data)) {
             foreach ($data as $key => $value) {
                 $user=Db::table('users')->where('id',$value->user_id)->first();
                 if (!empty($user)) {
                     $data[$key]->user_id=$user->name;
                 }else{
                     $data[$key]->user_id='用户不存在';   
                 }
                 $merchants=Db::table('merchants')->where('id',$value->merchant_id)->first();
                 if (!empty($merchants)) {
                     $data[$key]->merchant_id=$merchants->name;
                 }else{
                     $data[$key]->merchant_id='商户不存在';   
                 }
                 $hotel=Db::table('hotel_room')->where('id',$value->hotel_room_id)->first();
                 if (!empty($hotel)) {
                     $data[$key]->hotel_room_id=$hotel->house_name;
                 }else{
                     $data[$key]->hotel_room_id='房间不存在';   
                 }
             }
         } 
        return $this->view('',['data'=>$data],['wheres'=>$wheres]);
    }
    public function merchant()
    {
        $all = request()->all();
        $where[]=['merchant_type_id',3];
        
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
}
