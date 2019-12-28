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
            $data = \DB::table('orders')
                -> join('order_returns','orders.order_sn','=','order_returns.order_id')
                -> join('users','orders.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> where('merchant_id',$id)
                -> select(['order_returns.id as id','order_returns.order_id as order_id','users.name as user_name','order_returns.is_reg','order_returns.status','order_returns.content','refund_reason.name as retun_name'])
                -> paginate(10);
        }else{
            // 查询数据库内容
            $data = \DB::table('orders')
                -> join('order_returns','orders.order_sn','=','order_returns.order_id')
                -> join('users','orders.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> select(['order_returns.id as id','order_returns.order_id as order_id','users.name as user_name','order_returns.is_reg','order_returns.status','order_returns.content','refund_reason.name as retun_name'])
                -> paginate(10);
        }

        return $this->view('',['data'=>$data]);
    }
    // 修改审核状态
    public function aftermarketChange(){
        $all = \request() -> all();
        if(empty($all['ids'])){
            // 根据当前提交的id 查询数据库中值
            $is_reg = \DB::table('order_returns') -> where('id',$all['id']) -> select('is_reg')->first();
            $status = \DB::table('order_returns') -> where('id',$all['id']) -> select('status')->first();
            if($is_reg -> is_reg == 0){
                $data = [
                    'is_reg' => 1,
                    'handling_time' => date("Y-m-d H:i:s")
                ];
            }else if($is_reg -> is_reg == 1){
                if($status -> status == 1){
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
            }else if($is_reg -> is_reg == 2){
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
            $order_id = \DB::table('order_returns') -> where('id',$all['ids']) -> select('order_id') -> first();
//            $goodsdata = \DB::table('order_goods') -> where('order_id',$order_id -> order_id) -> get();
            // 查询商品名称、商品图片、商品价格、商品数量
            $goodsdata = \DB::table('order_goods')
                -> join('goods_sku','order_goods.goods_sku_id','=','goods_sku.id')
                -> join('goods','order_goods.goods_id','=','goods.id')
                -> select(['goods.name as goods_name','goods.img as goods_img','goods_sku.price as goods_price','order_goods.num'])
                -> where('order_goods.order_id',$order_id -> order_id)
                -> get();
            // 查询商品规格
            $attr_value = \DB::table('order_goods')
                -> join('goods_sku','order_goods.goods_sku_id','=','goods_sku.id')
                -> select(['goods_sku.attr_value'])
                -> where('order_goods.order_id',$order_id -> order_id)
                -> get();
            foreach ($attr_value as $v){
                $datas[] = implode(json_decode($v -> attr_value)[0] -> value,',');
            }
            // 将商品规格添加到data中
            foreach ($datas as $k => $v){
                $goodsdata[$k] -> attr_value = $v;
            }
            // 获取订单数据
            $orderdata = \DB::table('order_returns') -> where('id',$all['ids']) -> first();
            // 获取订单总金额
            $order_money = \DB::table('orders') -> where('order_sn',$order_id -> order_id) -> select('order_money') -> first();
            $arr = [
                'goodsdata' => $goodsdata,
                'order_money' => $order_money,
                'orderdata' => $orderdata
            ];
            return $this->view('',$arr);
        }
    }

    // 跳转退款原因界面
    public function index ()
    {
    	// 查询意见反馈表中内容
        $data = \DB::table('refund_reason') -> paginate(10);
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
