<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class HtorderController extends Controller
{   
    public function __construct()
    {
        $all=request()->all();
        if (empty($all['uid'])||empty($all['token'])) {
           return $this->rejson(202,'登陆失效');
        }
        $check=$this->checktoten($all['uid'],$all['token']);
        if ($check['code']==202) {
           return $this->rejson($check['code'],$check['msg']);
        }
    }
    /**
     * @api {post} /api/htorder/settlement 酒店结算页 
     * @apiName settlement
     * @apiGroup htorder
     * @apiParam {string} uid 用户id
     * @apiParam {string} token 验证登陆
     * @apiParam {array}  start 入住时间
     * @apiParam {array}  end 离店时间
     * @apiParam {array}  id 房间id
     * @apiSuccessExample 参数返回:
     *     {
     *       "code": "200",
     *       "data": {
                "start": "入住时间",
                "end": "离店时间",
                "days": "入住天数",
                "room": {
                    "house_name": "房间名字",
                    "img": "房间图片",
                    "price": "单价",
                    "merchant_id": "商户id",
                    "id": "房间id",
                    "name": "酒店名字"
                },
                "integral": "使用积分",
                "allprice": "总价格"
            },
     *       "msg":"添加成功"
     *     }
     */
    public function settlement(){
            $all=request()->all();
            if (empty($all['start'])||empty($all['end'])||empty($all['id'])) {
               return $this->rejson(201,'缺少参数');
            }
            $data['start']=$all['start'];
            $data['end']=$all['end'];
            $id=$all['id'];
            $startdate=strtotime($data['start']);
            $enddate=strtotime($data['end']);
            $data['days']=round(($enddate-$startdate)/3600/24);
            $data['room']=Db::table('hotel_room as h')
            ->join('merchants as m','h.merchant_id','=','m.id')
            ->select('h.house_name','h.img','h.price','h.merchant_id','h.id','m.name')
            ->where(['h.status'=>1,'h.id'=>$id])
            ->first();
            if(empty($data['room'])){
                return $this->rejson(201,'房间不存在');
            }
            $integral=Db::table('config')->where('key','integral')->first()->value;
            $data['integral']=floor($data['room']->price*$data['days']*$integral);
            $data['allprice']=$data['room']->price*$data['days']-$data['integral'];
            return $this->rejson('200','查询成功',$data);
        }
}