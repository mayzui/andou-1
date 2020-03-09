<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Hash;
use Auth;

class LoginController extends Controller {
    /**
     * @api {post} /api/login/login_p 手机登陆
     * @apiName loginP
     * @apiGroup login
     * @apiParam {string} phone 手机号码
     * @apiParam {string} password 密码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *           'name':'刘明',
     *           'login_count':'登陆次数',
     *           'mobile':'18883562091',
     *           'token':'登陆验证'
     *       },
     *       "msg":"登陆成功"
     *     }
     */
    public function loginP() {
        $all = request()->all();
        if (empty($all['phone']) || empty($all['password'])) {
            return $this->rejson(201, '参数错误');
        } else {
            $phone = $all['phone'];
        }
        if (!Auth::guard('admin')->attempt([
            'mobile' => $phone,
            'password' => $all['password'],
        ])) {
            return $this->rejson(201, '账号密码错误');
        } else {
            $data = DB::table('users')
                ->select('id', 'name', 'login_count', 'mobile')
                ->where('mobile', $phone)
                ->first();
            $token = $this->token($data->id);
            $datas['token'] = $token['token'];
            $datas['login_count'] = $data->login_count + 1;
            DB::table('users')->where('mobile', $phone)->update($datas);
            $data->token = $token['noncestr'];
            return $this->rejson(200, '登陆成功', $data);
        }

    }

    /**
     * @api {post} /api/login/wxlogin 微信登陆
     * @apiName wxlogin
     * @apiGroup login
     * @apiParam {string} code code
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *           'name':'刘明',
     *           'login_count':'登陆次数',
     *           'mobile':'18883562091',
     *           'token':'登陆验证'
     *       },
     *       "msg":"登陆成功"
     *     }
     */
    public function wxlogin() {
        $all = request()->all();
        $code = $all['code'] ?? '';
        $user = $this->getUserAccessUserInfo($code);

        $re = DB::table('users')
            ->select('id', 'name', 'login_count', 'mobile')
            ->where('openid', $user['openid'])
            ->first();
        if ($re) {
            $token = $this->token($re->id);
            $datas['token'] = $token['token'];
            $datas['login_count'] = $re->login_count + 1;
            DB::table('users')->where('openid', $user['openid'])->update($datas);
            $re->token = $token['noncestr'];
            return $this->rejson(200, '登陆成功', $re);
        }
        // 绑定流程
        $data['openid'] = $user['openid'];
        $data['name'] = $user['nickname'];
        $data['avator'] = $user['headimgurl'];
        return $this->rejson(200, '绑定手机号密码', $data);
    }

    /**
     * @api {post} /api/login/bindmobile 绑定手机号
     * @apiName bindmobile
     * @apiGroup login
     * @apiParam {string} phone 手机号码
     * @apiParam {string} verify 验证码
     * @apiParam {string} name 用户名
     * @apiParam {string} openid 微信openid
     * @apiParam {string} avator 微信头像
     * @apiParam {string} password 登陆密码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "",
     *       "msg":"绑定成功"
     *     }
     */
    public function bindmobile() {
        $all = request()->all();
        if (empty($all['phone']) || empty($all['password'] || empty($all['verify']) || empty($all['openid']))) {
            return $this->rejson(201, '参数错误');
        } else {
            if ($all['verify'] != Redis::get($all['phone'])) {
                return $this->rejson(201, '验证码错误');
            }
            $re = DB::table('users')->where('mobile', $all['phone'])->first();
            if ($re) {
                $data['openid'] = $all['openid'];
                $data['password'] = Hash::make($all['password']);
                $data['avator'] = '/images/7520e6faa309a1eed8a4fd95fb49770.jpg';
                // $re->avator ?? $all['avator'];
                $data['name'] = $re->name ?? $all['name'];
                $re = DB::table('users')->where('mobile', $all['phone'])->update($data);
            } else {
                $data['create_ip'] = request()->ip();
                $data['last_login_ip'] = request()->ip();
                $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());
                $data['source'] = 0;
                $data['is_del'] = 0;
                $data['openid'] = $all['openid'];
                $data['password'] = Hash::make($all['password']);
                $data['avator'] = '/images/7520e6faa309a1eed8a4fd95fb49770.jpg';
                // $all['avator'];
                $data['name'] = $all['name'];
                $data['mobile'] = $all['phone'];
                $re = DB::table('users')->insertGetId($data);
            }
            if ($re) {
                $datare = DB::table('users')
                    ->select('               ', 'name', 'login_count', 'mobile')
                    ->where('mobile', $all['phone'])
                    ->first();
                $token = $this->token($datare->id);
                $datas['token'] = $token['token'];
                $datas['login_count'] = $datare->login_count + 1;
                DB::table('users')->where('mobile', $all['phone'])->update($datas);
                $datare->token = $token['noncestr'];
                return $this->rejson(200, '绑定成功', $datare);
            } else {
                return $this->rejson(201, '绑定失败');
            }
        }
    }

    /**
     * @api {post} /api/login/reg_p 手机注册
     * @apiName regP
     * @apiGroup login
     * @apiParam {string} phone 手机号码
     * @apiParam {string} password 密码
     * @apiParam {string} verify 验证码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *           'name':'刘明',
     *           'login_count':'登陆次数',
     *           'mobile':'18883562091'
     *       },
     *       "msg":"登陆成功"
     *     }
     */
    public function regP() {
        $all = request()->all();
        if (empty($all['phone']) || empty($all['password']) || empty($all['verify'])) {
            return $this->rejson(201, '参数错误');
        } else {
            // if ($all['password'] != $all['password_two']) {
            //     return $this->rejson(201,'两次密码不一样');
            // }
            if ($all['verify'] != Redis::get($all['phone'])) {
                return $this->rejson(201, '验证码错误');
            }
            $re = DB::table('users')->where('mobile', $all['phone'])->first();
            if ($re) {
                return $this->rejson(201, '账户已存在');
            }
            $data['mobile'] = $all['phone'];
            $data['password'] = Hash::make($all['password']);
            $data['create_ip'] = request()->ip();
            $data['last_login_ip'] = request()->ip();
            $data['created_at'] = $data['updated_at'] = date('Y-m-d H:i:s', time());
            $data['source'] = 0;
            $data['is_del'] = 0;
            $data['name'] = '用户:' . $all['phone'];
            $data['avator'] = '/images/7520e6faa309a1eed8a4fd95fb49770.jpg';
            $re = DB::table('users')->insertGetId($data);
            if ($re) {
                $data = DB::table('users')
                    ->select('id', 'name', 'mobile')
                    ->where('mobile', $all['phone'])
                    ->first();
                return $this->rejson(200, '注册成功', $data);
            } else {
                return $this->rejson(201, '注册失败');
            }
        }
    }

    /**
     * @api {post} /api/login/send 发送短信验证
     * @apiName send
     * @apiGroup login
     * @apiParam {string} phone 手机号码
     * @apiParam {string} type 用户注册为1其它为零
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *
     *       },
     *       "msg":"登陆成功"
     *     }
     */
    public function send() {//发送短信验证
        $code = rand(100000, 999999);
        $all = request()->all();
        $mobile = $all['phone'];
        $type = $all['type'];
        $pattern = "/^1[34578]\d{9}$/";
        $res_1 = preg_match($pattern, $mobile);
        if (empty($mobile) || !$res_1) {
            return $this->rejson(201, '参数错误');
        }
        if (!empty($type) && $type == 1) {
            $re = DB::table('users')->where('mobile', $mobile)->first();
            if ($re) {
                return $this->rejson(201, '账户已存在');
            }
        }
        $send = $this->sendmessage($code, $mobile);

        if ($send) {
            return $this->rejson(200, '发送手机验证成功');
        } else {
            return $this->rejson(201, '发送短信验证失败');
        }

    }

    /**
     * @api {post} /api/login/forget 忘记密码
     * @apiName forget
     * @apiGroup login
     * @apiParam {string} phone 手机号码
     * @apiParam {string} verify 验证码
     * @apiParam {string} new_password 新密码
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
     *            "password":"新密码"
     *       },
     *       "msg":"修改成功"
     *     }
     */
    public function forget() {
        $all = request()->all();
        if (empty($all['phone']) || empty($all['verify']) || empty($all['new_password'])) {
            return $this->rejson(201, '参数错误');
        }
        $mobile = $all['phone'];
        if ($all['verify'] != Redis::get($all['phone'])) {
            return $this->rejson(201, '验证码错误');
        }
        $re = DB::table('users')->where('mobile', $mobile)->first();
        if (empty($re)) {

            return $this->rejson(201, '用户不存在');
        }

        $new_password = $all['new_password'];
        $datas['password'] = Hash::make($new_password);
        $re = DB::table('users')->where('mobile', $mobile)->update($datas);
        $datas = [
            'title' => '账户安全通知',
            'content' => '密码被修改为了您的账户安全请及时查验',
            'created_at' => date('Y-m-d H:i:s', time()),
            'send' => $re['id'],
        ];
        DB::table('notice')->insert($datas);
        return $this->rejson('200', "修改密码成功", ['password' => $new_password]);
    }

    /**
     * @api {post} /api/login/cache 获取短信测试
     * @apiName get_cache
     * @apiGroup login
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": "验证码",
     *       "msg":"修改成功"
     *     }
     */
    public function caches() {
        return $a = Redis::get('18883562091');
    }
}
