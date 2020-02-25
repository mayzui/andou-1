<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class UsersaddressController extends Controller {
    public function __construct() {
        $all = request()->all();
        $token = request()->header('token') ?? '';
        if ($token != '') {
            $all['token'] = $token;
        }
        if (empty($all['uid']) || empty($all['token'])) {
            return $this->rejson(202, '登陆失效');
        }
        $check = $this->checktoten($all['uid'], $all['token']);
        if ($check['code'] == 202) {
            return $this->rejson($check['code'], $check['msg']);
        }
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
     * @apiParam {string} district_id 地址区id
     * @apiParam {string} address 详细地址
     * @apiParam {string} is_default 设为默认地址 1是 0不是
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"添加成功"
     *     }
     */
    public function addressAdd() {
        $all = request()->all();
        if (!isset($all['name']) || !isset($all['mobile']) || !isset($all['address']) || !isset($all['district_id']) || !isset($all['city_id']) || !isset($all['province_id']) || !isset($all['is_default'])) {
            return $this->rejson(201, '缺少参数');
        }
        $data['name'] = $all['name'];
        $data['mobile'] = $all['mobile'];
        $data['address'] = $all['address'];
        $data['province_id'] = $all['province_id'];
        $data['city_id'] = $all['city_id'];
        $data['district_id'] = $all['district_id'];
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());
        $data['is_default'] = $all['is_default'];
        $data['user_id'] = $all['uid'];

        DB::beginTransaction(); //开启事务
        if ($all['is_default'] == 1) {
            $datas['is_default'] = 0;
            $re = DB::table('user_address')->where('user_id', $all['uid'])->update($datas);
        }
        $res = DB::table('user_address')->insert($data);
        if ($res) {
            DB::commit();
            return $this->rejson(200, '添加成功');
        } else {
            DB::rollback();
            return $this->rejson(201, '添加失败');
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
     * {
     * "id": "地址id",
     * "name": "收货人",
     * "mobile": "收货电话",
     * "is_default":'1为默认地址'
     * "province_id": "省id",
     * "city_id": "市id",
     * "area_id": "区id",
     * "address": "详细地址",
     * "province": "省地址",
     * "city": "市地址",
     * "area": "区地址"
     * }
     * ],
     *       "msg":"添加成功"
     *     }
     */
    public function address() {
        $all = request()->all();
        $data = DB::table('user_address')
            ->select('id', 'name', 'mobile', 'is_default', 'province_id', 'city_id', 'area_id', 'address')
            ->where('user_id', $all['uid'])
            ->get();
        foreach ($data as $key => $value) {
            $data[$key]->province = DB::table('districts')->where('id', $value->province_id)->first()->name ?? '';
            $data[$key]->city = DB::table('districts')->where('id', $value->city_id)->first()->name ?? '';
            $data[$key]->area = DB::table('districts')->where('id', $value->area_id)->first()->name ?? '';
        }
        return $this->rejson(200, '查询成功', $data);
    }

    /**
     * @api {post} /api/Usersaddress/default 设置默认地址
     * @apiName default
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
    public function default() {
        $all = request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201, '缺少参数');
        }
        $data['is_default'] = 1;
        $datas['is_default'] = 0;
        DB::beginTransaction(); //开启事务
        $re = DB::table('user_address')->where('user_id', $all['uid'])->update($datas);
        $res = DB::table('user_address')->where(['user_id' => $all['uid'], 'id' => $all['id']])->update($data);
        if ($re && $res) {
            DB::commit();
            return $this->rejson(200, '设置成功');
        } else {
            DB::rollback();
            return $this->rejson(201, '设置失败');
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
     * "id": "地址id",
     * "name": "收货人",
     * "mobile": "收货电话",
     * "is_default":'1为默认地址'
     * "province_id": "省id",
     * "city_id": "市id",
     * "area_id": "区id",
     * "address": "详细地址",
     * "province": "省地址",
     * "city": "市地址",
     * "area": "区地址"
     * },
     *       "msg":"添加成功"
     *     }
     */
    public function details() {
        $all = request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201, '缺少参数');
        }
        $data = DB::table('user_address')
            ->select('id', 'name', 'mobile', 'is_default', 'province_id', 'city_id', 'area_id', 'address')
            ->where(['user_id' => $all['uid'], 'id' => $all['id']])
            ->first();
        $data->province = DB::table('districts')->where('id', $data->province_id)->first()->name ?? '';
        $data->city = DB::table('districts')->where('id', $data->city_id)->first()->name ?? '';
        $data->area = DB::table('districts')->where('id', $data->area_id)->first()->name ?? '';
        return $this->rejson(200, '查询成功', $data);
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
     * @apiParam {string} district_id 地址区id
     * @apiParam {string} address 详细地址
     * @apiParam {string} is_default 是否默认地址 1为默认
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"修改成功"
     *     }
     */
    public function addressEdit() {
        $all = request()->all();
        if (!isset($all['name']) || !isset($all['mobile']) || !isset($all['address']) || !isset($all['district_id']) || !isset($all['city_id']) || !isset($all['province_id']) || !isset($all['id']) || !isset($all['is_default'])) {
            return $this->rejson(201, '缺少参数');
        }
        $data['name'] = $all['name'];
        $data['mobile'] = $all['mobile'];
        $data['address'] = $all['address'];
        $data['district_id'] = $all['district_id'];
        $data['city_id'] = $all['city_id'];
        $data['province_id'] = $all['province_id'];
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $data['is_default'] = $all['is_default'];
        $data['user_id'] = $all['uid'];
        if ($all['is_default'] == 1) {
            $datas['is_default'] = 0;
            DB::table('user_address')->where('user_id', $all['uid'])->update($datas);
        }
        DB::table('user_address')->where(['user_id' => $all['uid'], 'id' => $all['id']])->update($data);
        return $this->rejson(200, '修改成功');
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
    public function addressDel() {
        $all = request()->all();
        if (!isset($all['id'])) {
            return $this->rejson(201, '缺少参数');
        }
        $re = DB::table('user_address')
            ->where(['user_id' => $all['uid'], 'id' => $all['id']])
            ->delete();
        if ($re) {
            return $this->rejson(200, '删除成功');
        } else {
            return $this->rejson(201, '删除失败');
        }
    }
}
