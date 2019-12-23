<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class CommonController extends Controller
{   
    /**
     * @api {post} /api/common/pay_ways 支付方式
     * @apiName pay_ways
     * @apiGroup common
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data":  [
                {
                    "id": "支付方式id",
                    "pay_way": "支付方式名字",
                    "logo": "图标"
                }
            ],     
     *       "msg":"查询成功"
     *     }
     */
    public function payWays(){
        $data=Db::table('pay_ways')->select('id','pay_way','logo')->where('status',1)->get();
        return $this->rejson(200,'查询成功',$data);
    }
}