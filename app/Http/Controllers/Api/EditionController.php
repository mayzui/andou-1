<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
class EditionController extends Controller
{
    /**
     * @api {post} /api/edition/new_edition 检测新版本
     * @apiName new_edition
     * @apiGroup edition
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "edition": "当前版本",
     *          },
     *       "msg":"查询成功"
     *     }
     */
    public function new_edition(){
        $all = \request() -> all();
        // 链接数据库根据id查询
        $data = DB::table('config') -> where('id',9) -> select('value as edition') -> first();
        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
//W83tVnay3ZPCsMA
}