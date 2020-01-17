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
                ->join('goods','order_goods.goods_id','=','goods.id')
                -> join('users','order_goods.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> where('order_goods.merchant_id',$id)
                -> where($where)
                -> select('order_goods.id','order_goods.pay_way','order_goods.express_id','order_goods.courier_num','order_goods.order_id','users.name as user_name','refund_reason.name as retun_name',
                    'order_returns.content','order_returns.is_reg','order_returns.status','goods.id as gid')
                -> paginate(10);
        }else{
            // 查询数据库内容
            $data = \DB::table('order_goods')
                -> join('order_returns','order_goods.id','=','order_returns.order_goods_id')
                ->join('goods','order_goods.goods_id','=','goods.id')
                -> join('users','order_goods.user_id','=','users.id')
                -> join('refund_reason','order_returns.reason_id','=','refund_reason.id')
                -> where($where)
                -> select('order_goods.id','order_goods.pay_way','order_goods.express_id','order_goods.courier_num','order_goods.order_id','users.name as user_name','refund_reason.name as retun_name',
                    'order_returns.content','order_returns.is_reg','order_returns.status','goods.id as gid')
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
            // 根据当前传入id 查询商品详情表中,用户支付的金额
            $order_goods_data = \DB::table('order_goods') -> where('id',$all['id']) -> first();
            // 判断售后类型
            if($data -> status == 1){
                // 退货退款
                if($data -> is_reg == 0){
                    $datas = [
                        'is_reg' => 2,
                        'handling_time' => date("Y-m-d H:i:s")
                    ];
                }else if($data -> is_reg == 2){
                    // 执行退款操作
                    // 判断退款方式
                    $datas = [
                        'is_reg' => 3,
                        'handling_time' => date("Y-m-d H:i:s")
                    ];
                }
            }else{
                // 仅退款
                if($data -> is_reg == 0){
                    $datas = [
                        'is_reg' => 3,
                        'handling_time' => date("Y-m-d H:i:s")
                    ];
                }
            }
            // 查询主表和副表，判断用户的支付方式
            if($order_goods_data -> pay_way == 4){     // 余额支付
                \DB::beginTransaction();
                try{
                    // 更新退货表
                    $i = \DB::table('order_returns') -> where('order_goods_id',$all['id']) -> update($datas);
                    $data = \DB::table('order_returns') -> where('order_goods_id',$all['id']) -> select('is_reg','status')->first();
                    if($data -> is_reg == 3){
                        // 将用户支付的金额退还给用户
                        $user_data = \DB::table('users') -> where('id',$order_goods_data -> user_id) -> first();
                        $money = $user_data ->money + $order_goods_data -> pay_money;
                        // 更新用户表
                        $m = \DB::table('users') -> where('id',$order_goods_data -> user_id) -> update(['money' => $money]);
                        if($m){
                            \DB::commit();
                            flash("更新成功") -> success();
                            return redirect()->route('refund.aftermarket');
                        }else{
                            \DB::rollBack();
                            flash("更新失败") -> success();
                            return redirect()->route('refund.aftermarket');
                        }
                    }else{
                        if($i){
                            \DB::commit();
                            flash("更新成功") -> success();
                            return redirect()->route('refund.aftermarket');
                        }else{
                            \DB::rollBack();
                            flash("更新失败") -> success();
                            return redirect()->route('refund.aftermarket');
                        }
                    }
                }catch (\Exception $exception){
                    \DB::rollBack();
                    flash("更新失败,请稍后重试") -> success();
                    return redirect()->route('refund.aftermarket');
                }

            }else if($order_goods_data -> pay_way == 1){       // 微信支付

                // 更新退货表
                $i = \DB::table('order_returns') -> where('order_goods_id',$all['id']) -> update($datas);
                $data = \DB::table('order_returns') -> where('order_goods_id',$all['id']) -> select('is_reg','status')->first();
                if($data -> is_reg == 3){
                    // 查询订单,根据订单里边的数据进行退款
                    $order = \DB::table('order_returns as or')
                        -> join('order_goods as og','or.order_goods_id','=','og.id')
                        -> where('or.order_goods_id',$all['id'])
                        -> select('og.order_id as order_sn','or.*')
                        -> first();
                    $order = json_decode(json_encode($order),true);
                    // 微信退款
                    require_once base_path()."/wxpay/lib/WxPay.Api.php";
                    require_once base_path()."/wxpay/example/WxPay.NativePay.php";
                    $merch = new \WxPayConfig();
                    $merchid = $merch->GetMerchantId();
                    if(!$order){
                        return false;
                    }
                    $suiji = $this -> suiji();
                    $input = new \WxPayRefund();
                    $input->SetOut_trade_no($order['order_sn']);   //自己的订单号
                    $input->SetTransaction_id($order['returns_no']);  //微信官方生成的订单流水号，在支付成功中有返回
                    $input->SetOut_refund_no($suiji);   //退款单号
                    $input->SetTotal_fee($order['returns_amount']);   // 订单标价金额，单位为分
                    $input->SetRefund_fee($order['returns_amount']);   // 退款总金额，订单总金额，单位为分，只能为整数
                    $input->SetOp_user_id($merchid);        // 商户号
                    $result = \WxPayApi::refund($merch,$input); //退款操作
                    // 这句file_put_contents是用来查看服务器返回的退款结果 测试完可以删除了
                    if(($result['return_code']=='SUCCESS') && ($result['result_code']=='SUCCESS')){
                        $n = DB::table('order_goods') -> where('id',$all['id']) -> update(['status'=>60]);
                        //退款成功
                        flash('退款成功') -> success();
                        return redirect()->route('refund.aftermarket');
                    }else if(($result['return_code']=='FAIL') || ($result['result_code']=='FAIL')){
                        //退款失败
                        //原因
                        $reason = (empty($result['err_code_des'])?$result['return_msg']:$result['err_code_des']);
                        flash($reason) -> error();
                        return redirect()->route('refund.aftermarket');
                    }else{
                        //失败
                        flash("退款失败请稍后重试") -> error();
                        return redirect()->route('refund.aftermarket');
                    }
                }else{
                    if($i){
                        \DB::commit();
                        flash("更新成功") -> success();
                        return redirect()->route('refund.aftermarket');
                    }else{
                        \DB::rollBack();
                        flash("更新失败") -> success();
                        return redirect()->route('refund.aftermarket');
                    }
                }
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
