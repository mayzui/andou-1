<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class WalletController extends Controller
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
     * @api {post} /api/wallet/index 余额明细
     * @apiName index
     * @apiGroup wallet
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {string} page 分页
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "msg":"查询成功",
     *       "data": {
         "log":[
            "superior_id": "上级id",
            "price": "流动金额",
            "describe": "流动描述",
            "create_time": "流动时间",
            "state": "1获得 0消费"
        ],
        "money": "总金额"
     *      }
     *     }
     */
    public function index(){
        $all = \request() -> all();
        $num = 10;
        // 根据获取的id
        if(empty($all['uid'])){
            return $this->rejson(201,'请输入用户id');
        }
        if (isset($all['page'])) {
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        // 根据用户id 查询资金流动表
        $data['log'] = DB::table('user_logs')
            -> where('user_id',$all['uid'])
            -> where('type_id',2)
            -> select(['user_logs.superior_id','user_logs.price','user_logs.describe','user_logs.create_time','user_logs.state'])
            -> offset($pages)
            -> limit($num)
            -> get();
        $data['money'] = DB::table('users')
            -> where('id',$all['uid'])
            -> select('money')
            -> first()
            ->money ?? '';
        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
// W83tVnay3ZPCsMA
}