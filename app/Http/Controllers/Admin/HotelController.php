<?php

namespace App\Http\Controllers\Admin;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Admin\HotelRequest;
use App\Services\ActionLogsService;
use Illuminate\Http\Request;
use App\Services\HotelService;
use App\Repositories\HotelRepository;


class HotelController extends BaseController
{
    /**房间列表
     * [index description]
     * @return [type] [description]
     */
    public function index ()
    {    
        $data=Db::table('hotel_room')->paginate(20);
        foreach ($data as $key => $value) {
            $data[$key]->merchant_id=Db::table('merchants')->where('id',$value->merchant_id)->pluck('name')[0];
            $data[$key]->user_id=Db::table('users')->where('id',$value->user_id)->pluck('name')[0];
        }
        return $this->view('',['data'=>$data]);
    }
    /**添加修改房间
     * [add description]
     * @param string $value [description]
     */
    public function add($value='')
    {
        $all = request()->all();
        if (request()->isMethod('post')) {
            $save['house_name']=$all['house_name'];
            if (empty($all['id'])) {
                $re=Db::table('hotel_room')->insert($save);
            }else{
                $re=Db::table('hotel_room')->where('id',$all['id'])->update($save);
            }
            if ($re) {
                flash('修改成功')->success();
                return redirect()->route('hotel.index');
            }else{
                flash('修改失败')->error();
                return redirect()->route('hotel.index');
            }
        }else{
            if (empty($all['id'])) {
                $data = (object)[];  
            }else{
                $data=Db::table('hotel_room')->join('hotel_attr_value','hotel_room.id','hotel_attr_value.hotel_room_id')->where('id',$all['id'])->first();
            }
            $desc=Db::table('hotel_faci')->get();
            return $this->view('',['data'=>$data],['desc'=>$desc]);
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
                flash('修改成功')->success();
                return redirect()->route('hotel.faci');
            }else{
                flash('修改失败')->error();
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
