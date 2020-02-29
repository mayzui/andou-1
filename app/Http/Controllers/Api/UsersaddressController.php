<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserAddress;
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
    public function addressAdd() {
        $all = request()->all();
        if (!isset($all['name']) || !isset($all['mobile']) || !isset($all['address']) ||
            !isset($all['area_id']) || !isset($all['is_defualt'])) {
            return $this->rejson(201, '缺少参数');
        }
        $data['name'] = $all['name'];
        $data['mobile'] = $all['mobile'];
        $data['address'] = $all['address'];
        $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());
        $data['is_defualt'] = $all['is_defualt'];
        $data['user_id'] = $all['uid'];

        $addrIds = UserAddress::getInstance()->getAddrIds($all['area_id']);
        $data['province_id'] = $addrIds[0];
        $data['city_id'] = $addrIds[1];
        $data['area_id'] = isset($addrIds[2]) ? $addrIds[2] : 0;

        DB::beginTransaction(); //开启事务
        if ($all['is_defualt'] == 1) {
            $ret = DB::table('user_address')->where('user_id', $all['uid'])->update([
                'is_defualt' => 0
            ]);
            if ($ret === false) {
                DB::rollBack();
                return $this->responseJson(201, ' 添加失败');
            }
        }
        $ret = DB::table('user_address')->insert($data);
        if ($ret) {
            DB::commit();
            return $this->responseJson(200, '添加成功');
        }
        DB::rollback();
        return $this->responseJson(201, '添加失败');
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
     * "is_defualt":'1为默认地址'
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
            ->leftJoin('util_area AS ap', 'ap.id', 'province_id')
            ->leftJoin('util_area AS ac', 'ac.id', 'city_id')
            ->leftJoin('util_area AS aa', 'aa.id', 'area_id')
            ->where('user_id', $all['uid'])
            ->where('status', 1)
            ->select(['user_address.id', 'user_address.name', 'mobile', 'is_defualt', 'address', 'ap.name AS province',
                'ac.name AS city'])
            ->selectRaw("IFNULL(aa.name, '') AS area")
            ->get();
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
        $data['is_defualt'] = 1;
        $datas['is_defualt'] = 0;
        DB::beginTransaction(); //开启事务
        $re = DB::table('user_address')->where('user_id', $all['uid'])->update($datas);
        $res = DB::table('user_address')->where(['user_id' => $all['uid'], 'id' => $all['id']])->update($data);
        if ($re !== false && $res !== false) {
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
     * "is_defualt":'1为默认地址'
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
            ->leftJoin('util_area AS ap', 'ap.id', 'province_id')
            ->leftJoin('util_area AS ac', 'ac.id', 'city_id')
            ->leftJoin('util_area AS aa', 'aa.id', 'area_id')
            ->where('user_id', $all['uid'])
            ->where('user_address.id', $all['id'])
            ->where('status', 1)
            ->select(['user_address.id', 'user_address.name', 'mobile', 'is_defualt', 'province_id', 'city_id', 'area_id',
                'address', 'ap.name AS province', 'ac.name AS city'])
            ->selectRaw("IFNULL(aa.name, '') AS area")
            ->first();
        return $this->responseJson(200, 'OK', $data);
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
    public function addressEdit() {
        $all = request()->all();
        if (!isset($all['name']) || !isset($all['mobile']) || !isset($all['address']) ||
            !isset($all['area_id']) || !isset($all['id']) || !isset($all['is_defualt'])) {
            return $this->responseJson(201, '缺少参数');
        }
        $data['name'] = $all['name'];
        $data['mobile'] = $all['mobile'];
        $data['address'] = $all['address'];
        $data['updated_at'] = date('Y-m-d H:i:s', time());
        $data['is_defualt'] = $all['is_defualt'];
        $data['user_id'] = $all['uid'];

        $addrIds = UserAddress::getInstance()->getAddrIds($all['area_id']);
        $data['province_id'] = $addrIds[0];
        $data['city_id'] = $addrIds[1];
        $data['area_id'] = isset($addrIds[2]) ? $addrIds[2] : 0;

        if ($all['is_defualt'] == 1) {
            DB::table('user_address')->where('user_id', $all['uid'])->update([
                'is_defualt' => 0
            ]);
        }
        DB::table('user_address')->where(['user_id' => $all['uid'], 'id' => $all['id']])->update($data);
        return $this->responseJson(200, '修改成功');
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
            ->update([
                'status' => -1
            ]);
        if ($re) {
            return $this->rejson(200, '删除成功');
        } else {
            return $this->rejson(201, '删除失败');
        }
    }
}
