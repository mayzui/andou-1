<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/12/30
 * Time: 14:59
 */

namespace App\Http\Controllers\Api;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
class DetailsController extends Controller
{
    /**
     * @api {post} /api/details/list 商家详情
     * @apiName list
     * @apiGroup details
     * @apiParam {string} id 商家id
     * @apiSuccessExample 参数返回：
     * {
     * "code":"200",
     * "data":{
     *         [
     *             "door_img":"商家门头图",
     *             "stars_all":"商家星级",
     *             "address":"详细地址",
     *             "praise_num":"点赞数量",
     *             "name":"商家名称",
     *             "tel":"商家电话",
     *             "id":"商户id",
     *             "desc":"商家简介",
     *             "facilities":"商家环境设施"
     *          ]
     * },
     *  "msg":"查询成功"
     * }
     *
     */
    public function list()
    {
        $all = request()->all();
        if(empty($all['id'])){
            return $this->rejson(201,"缺少参数");
        }
        $data = DB::table("merchants")
            ->select(['id','name','tel','door_img','stars_all','address','praise_num','desc','facilities'])
            ->where('id', $all['id'])
            ->first();
        if ($data) {
            return $this->rejson(200,'查询成功',$data);
        } else {
            return $this->rejson(201, '查询失败');
        }
    }

    /**
     * @api {post} /api/details/hotelSel 房间类型
     * @apiName hotelSel
     * @apiGroup details
     * @apiParam {string} id 房间类型的id
     * @apiSuccessExample 返回参数：
     *     {
     *       "code":"200",
     *       "data":[
     *
     * {
     * "img":"房间图片",
     * "price":"房间价格",
     * "house_name":"房间名称"
     * }
     * ],
     *    "msg":"查询成功"
     *     }
     */
    public function hotelSel()
    {
        $all = request()->all();
        if(empty($all['id'])){
            return $this->rejson(201,"缺少参数");
        }
        $data = DB::table("hotel_room")
            ->select(['img', 'price', 'house_name'])
            ->where('id', $all['id'])
            ->first();
        if ($data) {
            return $this->rejson(200, '查询成功', $data);
        } else {
            return $this->rejson(201, '查询失败');
        }
    }
    /**
     * @api {post} /api/details/commnets 酒店住宿评论
     * @apiName commnets
     * @apiGroup details
     * @apiParam {string}  id 商户id
     * @apiParam {string}  page 分页页码
     * @apiSuccessExample 返回参数：
     * {
     *     "code":"200",
     *     "data":[
     *          {
     *          "stars":"评星",
     *          "created_at":"评论时间",
     *          "content":"评论内容",
     *          "name":"用户名",
     *          "avator"："用户头像"
     *          }
     *      ],
     *      "msg":"查询成功"
     *   }
     */

    //评论
    public function commnets()
    {
        $all = \request()->all();
        $num=10;
        if ($all['page']){
            $pages=($all['page']-1)*$num;
        }else{
            $pages=0;
        }
        $data = DB::table("order_commnets as m")
            ->join("users as u","m.user_id","=","u.id")
            ->select(['m.merchants_id','m.stars','m.created_at','u.avator','u.name'])
            ->where(['m.merchants_id'=>$all['id'],'m.type'=>1])
            ->offset($pages)
            ->limit($num)
            ->get();
        if ($data) {
            return $this->rejson(200, "查询成功", $data);
        } else {
            return $this->rejson(201, "查询失败");
        }
    }

}