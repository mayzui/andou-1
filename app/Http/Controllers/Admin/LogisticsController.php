<?php
namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogisticsController extends BaseController
{
    // 修改物流信息
    public function updateLogistics(){
        $all = \request() ->  all();
        if(\request() -> isMethod("get")){
            // 根据提交的id，查询商品详情表中的内容
            $data = \DB::table('order_goods') -> where('id',$all['id']) -> first();
            // 查询物流表
            $type = \DB::table('express') -> get();
            return $this->view('',['data'=> $data,'type'=>$type]);
        }else{
            // 获取提交的数据
            $data = [
                'express_id' => $all['express_id'],
                'courier_num' => $all['courier_num']
            ];
            // 链接数据库修改内容
            $i = \DB::table('order_goods') -> where('id',$all['id']) -> update($data);
            if($i){
                flash("修改成功") -> success();
                return redirect()->route('logistics.indexs');
            }else{
                flash("修改失败，请稍后再试") -> error();
                return redirect()->route('logistics.indexs');
            }
        }
    }

    // 跳转物流信息界面
    public function indexs()
    {
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = \DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            //查询订单详情表中内容
            $data = \DB::table('order_goods')
                -> join('merchants','order_goods.merchant_id','=','merchants.id')
                -> join('users','order_goods.user_id','=','users.id')
                -> join('goods','order_goods.goods_id','=','goods.id')
                -> where('merchants.user_id',$id)
                -> select(['order_goods.id','order_goods.order_id','merchants.name as merchants_name','users.name as users_name','goods.name as goods_name',
                    'order_goods.num','order_goods.shipping_free','order_goods.total','order_goods.express_id','order_goods.courier_num'])
                -> paginate(5);
        }else{
            // 如果未开店，则是管理员
            //查询订单详情表中内容
            $data = \DB::table('order_goods')
                -> join('merchants','order_goods.merchant_id','=','merchants.id')
                -> join('users','order_goods.user_id','=','users.id')
                -> join('goods','order_goods.goods_id','=','goods.id')
                -> select(['order_goods.id','order_goods.order_id','merchants.name as merchants_name','users.name as users_name','goods.name as goods_name',
                    'order_goods.num','order_goods.shipping_free','order_goods.total','order_goods.express_id','order_goods.courier_num'])
                -> paginate(5);
        }
        return $this -> view('',['list' => $data]);
    }
    // 去发货
    public function goGoods(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            // 查询物流公司
            $type = \DB::table('express') -> get();
            return $this->view('',['id'=>$all['id'],'type'=>$type]);
        }else{
            // 获取提交的值
            $data = [
                'express_id' => $all['express_id'],
                'courier_num' => $all['courier_num'],
                'status' => 40
            ];
            $i = \DB::table('order_goods') -> where('id',$all['id']) -> update($data);
            if($i){
                flash("发货成功") -> success();
                return redirect()->route('logistics.indexs');
            }else{
                flash("发货失败，请稍后再试") -> error();
                return redirect()->route('logistics.indexs');
            }
        }
    }

    // 查看物流信息
    public function readLogistics(){
        $all = \request() -> all();
        $customer = "A85FFAADEF1E377FC67275CB15698F72";
        $key = 'HZdwfXDv3190';
        $url = 'http://poll.kuaidi100.com/poll/query.do';
        $express_id=$all['express_id'];     // 快递公司id
        $courier_num=$all['courier_num'];   // 订单号

        if (!empty($express_id) && !empty($courier_num)) {

            $r01 = \DB::table('express')->where('id',$express_id)->first();
            $type = $r01->com; //快递公司代码

            $kuaidi_name = $r01->name;

            $post_data["customer"] = $customer;

            $post_data["param"] = '{"com":"' . $type . '","num":"' . $courier_num . '"}';

            $post_data["sign"] = md5($post_data["param"] . $key . $post_data["customer"]);

            $post_data["sign"] = strtoupper($post_data["sign"]);

            $o = "";

            foreach ($post_data as $k => $v) {

                $o.= "$k=" . urlencode($v) . "&";  //默认UTF-8编码格式
            }

            $post_data = substr($o, 0, -1);

            //发起CURL请求

            $ch = curl_init();

            curl_setopt($ch, CURLOPT_POST, 1);

            curl_setopt($ch, CURLOPT_HEADER, 0);

            curl_setopt($ch, CURLOPT_URL, $url);

            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);

            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);

            $da = str_replace("\"", '"', $result);

            $res_1 = json_decode($da, true);

            $data['wuliu_msg'] = $res_1;

            $data['name'] = $kuaidi_name;

            $data['courier_num'] = $courier_num;
            // 判断订单状态 包括0在途，1揽收，2疑难，3签收，4退签，5派件，6退回等7个状态
            if(isset($res_1['state'])){
                if($res_1['state'] == 0){
                    $state = '在途中';
                }else if($res_1['state'] == 1){
                    $state = '已揽收';
                }else if($res_1['state'] == 2){
                    $state = '疑难';
                }else if($res_1['state'] == 3){
                    $state = '已签收';
                }else if($res_1['state'] == 4){
                    $state = '退签';
                }else if($res_1['state'] == 5){
                    $state = '已派件';
                }else if($res_1['state'] == 6){
                    $state = '退回';
                }
            }else{
                flash("对不起没有查询到该物流信息，请查看快递公司与快递单号是否一致") ->error();
                return redirect()->route('logistics.indexs');
            }

            return $this->view('',['data' => $data,'id'=>$all['id'],'state'=>$state]);
        } else {
            return "no";
        }

    }

}
