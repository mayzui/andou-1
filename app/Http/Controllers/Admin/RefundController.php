<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefundController extends BaseController
{
    // 跳转售后服务界面
    public function aftermarket(){
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = \DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 查询数据库内容
            $data = \DB::table('order_goods')
                -> join('order_returns','order_goods.id','=','order_returns.order_goods_id')
                -> join('users','order_goods.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> where('order_goods.merchant_id',$id)
                -> select('order_goods.id','order_goods.order_id','users.name as user_name','refund_reason.name as retun_name',
                    'order_returns.content','order_returns.is_reg','order_returns.status')
                -> paginate(10);
        }else{
            // 查询数据库内容
            $data = \DB::table('order_goods')
                -> join('order_returns','order_goods.id','=','order_returns.order_goods_id')
                -> join('users','order_goods.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> select('order_goods.id','order_goods.order_id','users.name as user_name','refund_reason.name as retun_name',
                    'order_returns.content','order_returns.is_reg','order_returns.status')
                -> paginate(10);
        }

        return $this->view('',['data'=>$data]);
    }
    // 修改审核状态
    public function aftermarketChange(){
        $all = \request() -> all();
        if(empty($all['ids'])){
            // 根据当前提交的id 查询数据库中值
            $data = \DB::table('order_returns') -> where('id',$all['id']) -> select('is_reg','status')->first();
            if($data -> is_reg == 0){
                $data = [
                    'is_reg' => 1,
                    'handling_time' => date("Y-m-d H:i:s")
                ];
            }else if($data -> is_reg == 1){
                if($data -> status == 1){
                    $data =[
                        'is_reg' => 2,
                        'handling_time' => date("Y-m-d H:i:s")
                    ];
                }else{
                    $data =[
                        'is_reg' => 3,
                        'handling_time' => date("Y-m-d H:i:s")
                    ];
                }
            }else if($data -> is_reg == 2){
                $data = [
                    'is_reg' => 3,
                    'handling_time' => date("Y-m-d H:i:s")
                ];
            }
            $i = \DB::table('order_returns') -> where('id',$all['id']) -> update($data);
            if($i){
                flash("更新成功") -> success();
                return redirect()->route('refund.aftermarket');
            }else{
                flash("更新失败") -> success();
                return redirect()->route('refund.aftermarket');
            }
        }else{
            // 根据传入的ids 查询数据库中的内容
            $order_id = \DB::table('order_returns') -> where('order_goods_id',$all['ids']) -> select('order_goods_id') -> first();
//            return dd($order_id);
//            $goodsdata = \DB::table('order_goods') -> where('order_id',$order_id -> order_id) -> get();
            // 查询商品名称、商品图片、商品价格、商品数量
            $goodsdata = \DB::table('order_goods')
                -> join('goods_sku','order_goods.goods_sku_id','=','goods_sku.id')
                -> join('goods','order_goods.goods_id','=','goods.id')
                -> select(['goods.name as goods_name','goods.img as goods_img','goods_sku.price as goods_price','order_goods.num'])
                -> where('order_goods.id',$order_id -> order_goods_id)
                -> get();
            // 查询商品规格
            $attr_value = \DB::table('order_goods')
                -> join('goods_sku','order_goods.goods_sku_id','=','goods_sku.id')
                -> select(['goods_sku.attr_value'])
                -> where('order_goods.id',$order_id -> order_goods_id)
                -> get();
            foreach ($attr_value as $v){
                $datas[] = implode(json_decode($v -> attr_value)[0] -> value,',');
            }
            // 将商品规格添加到data中
            foreach ($datas as $k => $v){
                $goodsdata[$k] -> attr_value = $v;
            }
//            return dd($goodsdata);
            // 获取订单数据
            $orderdata = \DB::table('order_returns')
                -> join('express','order_returns.express_id','=','express.id')
                -> select('order_returns.consignee_realname','order_returns.consignee_telphone','order_returns.consignee_address','express.name as express_company')
                -> where('order_goods_id',$all['ids']) -> first();
            // 获取订单总金额
            $order_money = \DB::table('order_goods') -> where('id',$order_id -> order_goods_id) -> select('pay_money','order_id') -> first();
            $arr = [
                'goodsdata' => $goodsdata,
                'order_money' => $order_money,
                'orderdata' => $orderdata
            ];
//            return dd($arr);
            return $this->view('',$arr);
        }
    }

    // 跳转退款原因界面
    public function index ()
    {
        $id = \Auth::id();
        // 判断该用户，是否开店 并且已经认证通过
        $i = \DB::table('merchants') -> where("user_id",$id) -> where("is_reg",1) -> first();
        if(!empty($i)) {
            // 如果开店，则查询当前商户的信息
            // 查询退款原因表中内容
            $data = \DB::table('refund_reason')
                -> join('merchants','refund_reason.merchant_id','=','merchants.id')
                -> where('merchants.user_id',$id)
                -> select(['merchants.name as merchants_name','refund_reason.id','refund_reason.name as reason_name','refund_reason.is_del'])
                -> paginate(10);
        }else{
            // 查询退款原因表中内容
            $data = \DB::table('refund_reason')
                -> join('merchants','refund_reason.merchant_id','=','merchants.id')
                -> select(['merchants.name as merchants_name','refund_reason.id','refund_reason.name as reason_name','refund_reason.is_del'])
                -> paginate(10);
        }
        return $this -> view('',['data' => $data]);
    }

    // 退款原因新增 and 修改
    public function indexChange(){
        $all = \request() -> all();
        if(\request() -> isMethod("get")){
            if(empty($all['id'])){
                // 跳转新增界面
                return $this->view('');
            }else{
                // 跳转修改界面
                $data = \DB::table('refund_reason') -> where('id',$all['id']) -> first();
                return $this->view('',['data'=>$data]);
            }
        }else{
            if(empty($all['id'])){
                // 执行新增操作
                $data = [
                    'name' => $all['name']
                ];
                $i = \DB::table('refund_reason') -> insert($data);
                if($i){
                    flash("新增成功") -> success();
                    return redirect()->route('refund.index');
                }else{
                    flash("新增失败") -> error();
                    return redirect()->route('refund.index');
                }
            }else{
                // 执行修改操作
                $data = [
                    'name' => $all['name']
                ];
                $i = \DB::table('refund_reason') -> where('id',$all['id']) -> update($data);
                if($i){
                    flash("修改成功") -> success();
                    return redirect()->route('refund.index');
                }else{
                    flash("修改失败，未修改任何内容") -> error();
                    return redirect()->route('refund.index');
                }
            }
        }
    }

    // 删除退货原因
    public function indexDel(){
        $all = \request() -> all();
        // 判断删除状态
        if($all['is_del'] == 1){
            $data = [
                'is_del' => 0
            ];
        }else{
            $data = [
                'is_del' => 1
            ];
        }
        $i = \DB::table('refund_reason') -> where('id',$all['id']) -> update($data);
        if($i){
            flash("更新成功") -> success();
            return redirect()->route('refund.index');
        }else{
            flash("更新失败") -> error();
            return redirect()->route('refund.index');
        }
    }
}
