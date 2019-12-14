<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class IndexController extends Controller
{   
    /**
     * @api {post} /api/index/index 首页
     * @apiName index
     * @apiGroup index
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "banner": [
                    {
                        "id": "轮播图id",
                        "img": "图片地址",
                        "url": "跳转地址"
                    }
                ]
             }
     *       "msg":"登陆成功"
     *     }
     */
    public function index(){
        $data['banner']=Db::table('banner')
        ->select('id','img','url')
        ->where(['banner_position_id'=>6],['status'=>1])
        ->orderBy('sort','DESC')
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
}