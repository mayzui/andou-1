<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class LogisticsController extends BaseController
{
    public function indexs()
    {
    return "aa";
//    	 查询订单详情表中内容
        $data = \DB::table('order_goods')
            -> join('merchants','order_goods.merchant_id','=','merchants.id')
            -> join('users','order_goods.user_id','=','users.id')
            -> join('goods','order_goods.goods_id','=','goods.id')
//            -> join('express','order_goods.express_id','=','express.id')
            -> select(['order_goods.id','merchants.name as merchants_name','users.name as users_name','goods.name as goods_name',
                'order_goods.num','order_goods.shipping_free','order_goods.total','order_goods.express_id','order_goods.courier_num'])
            -> paginate(5);
//        return dd($data);
//        return $this -> view('index',['list' => $data]);
        return $this -> view('index');
    }


    public function indexChange(){
        $all = \request() -> all();
        return dd($all);
//        if($i){
//            flash("修改成功") -> success();
//            return redirect()->route('about.index');
//        }else{
//            flash("修改失败") -> success();
//            return redirect()->route('about.index');
//        }
    }
}
