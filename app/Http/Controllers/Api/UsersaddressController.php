<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class UsersaddressController extends Controller
{   
    public function __construct()
    {
        $all=request()->all();
        if (empty($all['uid'])||empty($all['token'])) {
           return $this->rejson(201,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==201) {
           return $this->rejson($check['code'],$check['msg']);
        }
    }

    /**
     * @api {post} /api/Usersaddress/districts 获取所有地址列表
     * @apiName districts
     * @apiGroup Usersaddress 
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":[
                    {
                        "name": "北京",
                        "id": 11,
                        "pid": 0,
                        "cities": [
                            {
                                "name": "北京",
                                "id": 1101,
                                "pid": 11,
                                "areas": [
                                    {
                                        "name": "东城",
                                        "id": 110101,
                                        "pid": 1101
                                    }
                                ]
                            }
                        ]
                    }
                ],
     *       "msg":"查询成功"
     *     }
     */
    public function districts(){
        $data=Redis::get('districts');
        if ($data) {
            $data=json_decode($data,1);
        }else{
            $data=$this->districts();
            Redis::set('districts',json_encode($data['districts'],1));
        }
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/Usersaddress/address_add 添加收货地址 
     * @apiName address_add
     * @apiGroup Usersaddress
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} name 收货人名字
     * @apiParam {string} mobile 收货人电话
     * @apiParam {string} province_id 地址省id
     * @apiParam {string} city_id 地址市id
     * @apiParam {string} area_id 地址区id
     * @apiParam {string} address 详细地址
     * @apiParam {string} is_defualt 设为默认地址 1是 0不是
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加成功"
     *     }
     */
    public function addressAdd(){
        $all=request()->all();
        if (!isset($all['name']) || !isset($all['mobile']) || !isset($all['address']) || !isset($all['area_id']) || !isset($all['city_id']) || !isset($all['province_id'] || !isset($all['is_defualt'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data['name']=$all['name'];
        $data['mobile']=$all['mobile'];
        $data['address']=$all['address'];
        $data['area_id']=$all['area_id'];
        $data['city_id']=$all['city_id'];
        $data['province_id']=$all['province_id'];
        $data['created_at']=$data['updated_at']=date('Y-m-d H:i:s',time());
        $data['is_defualt']=$all['is_defualt'];
        $data['user_id']=$all['uid'];
        
        DB::beginTransaction(); //开启事务
        if ($all['is_defualt']==1) {
            $datas['is_defualt']=0;
            $re=Db::table('user_address')->where('user_id',$all['uid'])->update($datas);   
        }
        $res=Db::table('user_address')->insert($data);
        if ($res) {
            DB::commit();
            return $this->rejson(200,'添加成功');
        }else{
            DB::rollback();
            return $this->rejson(201,'添加失败');
        }
    }
    /**
     * @api {post} /api/Usersaddress/address 收货地址列表 
     * @apiName address
     * @apiGroup Usersaddress
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                    {
                        "id": "地址id",
                        "name": "收货人",
                        "mobile": "收货电话",
                        "is_defualt":'1为默认地址'
                        "province_id": "省id",
                        "city_id": "市id",
                        "area_id": "区id",
                        "address": "详细地址",
                        "province": "省地址",
                        "city": "市地址",
                        "area": "区地址"
                    }
        ],
     *       "msg":"添加成功"
     *     }
     */
    public function address(){
        $all=request()->all();
        $data=Db::table('user_address')
        ->select('id','name','mobile','is_defualt','province_id','city_id','area_id','address')
        ->where('user_id',$all['uid'])
        ->get();
        foreach ($data as $key => $value) {
            $data[$key]->province=DB::table('districts')->where('id',$value->province_id)->first()->name ?? '';
            $data[$key]->city=DB::table('districts')->where('id',$value->city_id)->first()->name ?? '';
            $data[$key]->area=DB::table('districts')->where('id',$value->area_id)->first()->name ?? '';
        }
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/Usersaddress/defualt 设置默认地址 
     * @apiName defualt
     * @apiGroup Usersaddress
     * @apiParam {string} id 地址id
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"设置成功"
     *     }
     */
    public function defualt(){
        $all=request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data['is_defualt']=1;
        $datas['is_defualt']=0;
        DB::beginTransaction(); //开启事务
        $re=Db::table('user_address')->where('user_id',$all['uid'])->update($datas);
        $res=Db::table('user_address')->where(['user_id'=>$all['uid'],'id'=>$all['id']])->update($data);
        if ($re&&$res) {
            DB::commit();
            return $this->rejson(200,'设置成功');
        }else{
            DB::rollback();
            return $this->rejson(201,'设置失败');
        }
    }
    /**
     * @api {post} /api/Usersaddress/details 地址详细 
     * @apiName details
     * @apiGroup Usersaddress
     * @apiParam {string} id 地址id
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "id": "地址id",
                "name": "收货人",
                "mobile": "收货电话",
                "is_defualt":'1为默认地址'
                "province_id": "省id",
                "city_id": "市id",
                "area_id": "区id",
                "address": "详细地址",
                "province": "省地址",
                "city": "市地址",
                "area": "区地址"
            },
     *       "msg":"添加成功"
     *     }
     */
    public function details(){
        $all=request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data=Db::table('user_address')
        ->select('id','name','mobile','is_defualt','province_id','city_id','area_id','address')
        ->where(['user_id'=>$all['uid'],'id'=>$all['id']])
        ->first();
        $data->province=DB::table('districts')->where('id',$data->province_id)->first()->name ?? '';
        $data->city=DB::table('districts')->where('id',$data->city_id)->first()->name ?? '';
        $data->area=DB::table('districts')->where('id',$data->area_id)->first()->name ?? '';
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/Usersaddress/address_edit 修改收货地址 
     * @apiName address_edit
     * @apiGroup Usersaddress
     * @apiParam {string} id 地址id
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} name 收货人名字
     * @apiParam {string} mobile 收货人电话
     * @apiParam {string} province_id 地址省id
     * @apiParam {string} city_id 地址市id
     * @apiParam {string} area_id 地址区id
     * @apiParam {string} address 详细地址
     * @apiParam {string} is_defualt 是否默认地址 1为默认 
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"修改成功"
     *     }
     */
    public function addressEdit(){
        $all=request()->all();
        if (!isset($all['name']) || !isset($all['mobile']) || !isset($all['address']) || !isset($all['area_id']) || !isset($all['city_id']) || !isset($all['province_id']) || !isset($all['id'])||!isset($all['is_defualt'])) {
            return $this->rejson(201,'缺少参数');
        }
        $data['name']=$all['name'];
        $data['mobile']=$all['mobile'];
        $data['address']=$all['address'];
        $data['area_id']=$all['area_id'];
        $data['city_id']=$all['city_id'];
        $data['province_id']=$all['province_id'];
        $data['updated_at']=date('Y-m-d H:i:s',time());
        $data['is_defualt']=$all['is_defualt'];
        $data['user_id']=$all['uid'];
        if ($all['is_defualt']==1) {
            $datas['is_defualt']=0;
            $re=Db::table('user_address')->where('user_id',$all['uid'])->update($datas);
        }
        $res=Db::table('user_address')->where(['user_id'=>$all['uid'],'id'=>$all['id']])->update($data);
        return $this->rejson(200,'修改成功');    
    }
    /**
     * @api {post} /api/Usersaddress/address_del 删除地址 
     * @apiName address_del
     * @apiGroup Usersaddress
     * @apiParam {string} id 地址id
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"删除成功"
     *     }
     */
    public function addressDel(){
        $all=request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201,'缺少参数');
        }
        $re=Db::table('user_address')
        ->where(['user_id'=>$all['uid'],'id'=>$all['id']])
        ->delete();
        if ($re) {
            return $this->rejson(200,'删除成功');
        }else{
            return $this->rejson(201,'删除失败');
        }
    }
}