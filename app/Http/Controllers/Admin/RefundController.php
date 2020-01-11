<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class RefundController extends BaseController
{
    // 跳转售后服务界面
    public function aftermarket(){
        $id = \Auth::id();
        $all = \request() -> all();
        $where[]=['order_goods.id','>','0'];
        // 判断条件搜索
        if (!empty($all['order_num'])) {
            $order_num = $all['order_num'];
            $where[]=['order_goods.order_id', 'like', '%'.$all['order_num'].'%'];
        }else{
            $order_num = '';
        }
        // 判断条件查询
        if(!empty($all['status'])){
            $status = $all['status'];
            if($all['status'] == 2){            // 待处理
                $where[] = ['order_returns.is_reg',0];
            }elseif ($all['status'] == 1){      // 已完成
                $where[] = ['order_returns.is_reg',3];
            }elseif ($all['status'] == 3){      // 退货中
                $where[] = ['order_returns.is_reg',2];
            }elseif ($all['status'] == 4){      // 已拒绝
                $where[] = ['order_returns.is_reg',4];
            }else{

            }
        }else{
            $status = 0;
        }
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
                -> where($where)
                -> select('order_goods.id','order_goods.express_id','order_goods.courier_num','order_goods.order_id','users.name as user_name','refund_reason.name as retun_name',
                    'order_returns.content','order_returns.is_reg','order_returns.status')
                -> paginate(10);
        }else{
            // 查询数据库内容
            $data = \DB::table('order_goods')
                -> join('order_returns','order_goods.id','=','order_returns.order_goods_id')
                -> join('users','order_goods.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> where($where)
                -> select('order_goods.id','order_goods.express_id','order_goods.courier_num','order_goods.order_id','users.name as user_name','refund_reason.name as retun_name',
                    'order_returns.content','order_returns.is_reg','order_returns.status')
                -> paginate(10);
        }
        return $this->view('',['data'=>$data,'status' => $status,"order_num" => $order_num]);
    }
    // 修改审核状态
    public function aftermarketChange(){
        $all = \request() -> all();
        if(empty($all['ids'])){
            // 根据当前提交的id 查询数据库中值
            $data = \DB::table('order_returns') -> where('order_goods_id',$all['id']) -> select('is_reg','status')->first();
            // 判断售后类型
            if($data -> status == 1){
                // 退货退款
                if($data -> is_reg == 0){
                    $data = [
                        'is_reg' => 2,
                        'handling_time' => date("Y-m-d H:i:s")
                    ];
                }else if($data -> is_reg == 2){
                    $data = [
                        'is_reg' => 3,
                        'handling_time' => date("Y-m-d H:i:s")
                    ];
                }
            }else{
                // 仅退款
                if($data -> is_reg == 0){
                    $data = [
                        'is_reg' => 3,
                        'handling_time' => date("Y-m-d H:i:s")
                    ];
                }
            }

            $i = \DB::table('order_returns') -> where('order_goods_id',$all['id']) -> update($data);
            if($i){
                flash("更新成功") -> success();
                return redirect()->route('refund.aftermarket');
            }else{
                flash("更新失败") -> success();
                return redirect()->route('refund.aftermarket');
            }
        }else{
            $data = [
                'is_reg' => 4,
                'handling_time' => date("Y-m-d H:i:s")
            ];
            $i = \DB::table('order_returns') -> where('order_goods_id',$all['ids']) -> update($data);
            if($i){
                flash("更新成功") -> success();
                return redirect()->route('refund.aftermarket');
            }else{
                flash("更新失败") -> success();
                return redirect()->route('refund.aftermarket');
            }
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
