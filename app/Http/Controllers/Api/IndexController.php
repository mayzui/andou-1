<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
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
     *           "banner": [
     *               {
     *                   "id": "轮播图id",
     *                   "img": "图片地址",
     *                   "url": "跳转地址"
     *                }
     *          ],
     *           "merchant_type": [
                    {
                        "id": "商户分类id",
                        "img": "商户分类图片"
                    }
                ],
                 "merchants": [
                    {
                        "id": "商户id",
                        "logo_img": "商户logo",
                        "name": "商户名字",
                        "merchant_type_id":"商户类型id"
                    }
                ],
                "notice": [
                    {
                        "id": "公告id",
                        "content": "公告内容",
                        "updated_at": "更新时间"
                    }
                ]
     *        }
     *       "msg":"登陆成功"
     *     }
     */
    public function index(){
        $data['banner']=DB::table('banner')
        ->select('id','img','url')
        ->where(['banner_position_id'=>1,'status'=>1])
        ->orderBy('sort','ASC')
        ->get();
        $data['merchant_type']=DB::table('merchant_type')
        ->select('id','img')
        ->where('status',1)
        ->orderBy('sort','ASC')
        ->get();
        $data['merchants']=DB::table('merchants')
        ->select('id','logo_img','name','merchant_type_id')
        ->where('recommend',1)
        ->orderBy('updated_at','DESC')
        ->get();
        $data['notice']=DB::table('notice')
        ->select('id','content','updated_at')
        ->where('status',1)
        ->where('send','all')
        ->orderBy('updated_at','DESC')
        ->get();
        return $this->rejson(200,'查询成功',$data);
    }
    /**
     * @api {post} /api/index/information 通知信息
     * @apiName information
     * @apiGroup index
     * @apiParam {string} id 通知信息的id
     * @apiParam {string} uid 用户的id，非必传
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                 "id": "标题id",
                "title": "公告标题",
                "content": "公告内容",
                "created_at": "创建时间"
     *          },
     *       "msg":"查询成功"
     *     }
     */
    public function information(){
        $all = \request() -> all();
        if (empty($all['id'])) {
            return $this->rejson(201,'请输入id');
        }
        // 链接数据库根据id查询
        $data = DB::table('notice')
            -> where('id',$all['id'])
            -> where('status',1)
            -> select(['id','title','content','created_at','message'])
            ->first();
        $message = json_decode($data -> message) ?? [];
        if(!empty($all['uid'])){
            if(!in_array($all['uid'],$message)){
                $message[] =$all['uid'];
            }
            $messages = json_encode($message);
            $datas = [
                'message' => $messages
            ];
            DB::table('notice') -> where('id',$all['id']) -> update($datas);
        }

        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
    /**
     * @api {post} /api/index/notification_center 通知中心
     * @apiName notification_center
     * @apiGroup index
     * @apiParam {string} uid 用户的id，非必填
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                    [
                        "id": "标题id",
                        "title": "公告标题",
                        "message": "已读消息用户id",
                        "messageStatus": "公告状态 1已读 0未读",
                        "created_at": "发布时间"
                    ]
     *          },
     *       "msg":"查询成功"
     *     }
     */
    public function notification_center(){
        $all = \request() -> all();

        // 链接数据库根据id查询
        $where[]=['send', 'like', '%'.$all['uid'].'%'];
        $where[]=['status',1];
        $wheres[]=['send','all'];
        $wheres[]=['status',1];
        $data = DB::table('notice')
            -> where($where)
            -> orWhere($wheres)
            -> select(['id','title','created_at','message'])
            ->orderBy('id','DESC')
            -> get();

        if(!empty($data)){
            foreach ($data as $key => $value) {
                $message = json_decode($value -> message) ?? [];

                if(in_array($all['uid'],$message)){
                    $data[$key] -> messageStatus = 1;
                }else{
                    $data[$key] -> messageStatus = 0;
                }

            }
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
    /**
     * @api {post} /api/index/about 关于我们
     * @apiName about
     * @apiGroup index
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "image": "图片",
                "title": "标题",
                "content": "内容",
                "value": "版本",
                "copyright": "版权"
     *          },
     *       "msg":"查询成功"
     *     }
     */
    public function about(){
        $all = \request() -> all();
        // 链接数据库根据id查询
        $data = \DB::table('about')
            -> join('config','about.config_id','=','config.id')
            -> where('about.id',1)
            -> select(['about.image','about.title','about.content','config.value','about.copyright'])
            -> first();
        if(!empty($data)){
            return $this->rejson(200,'查询成功',$data);
        }else{
            return $this->rejson(201,'未查询到该id');
        }
    }
//W83tVnay3ZPCsMA
}
