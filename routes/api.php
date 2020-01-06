<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

/**
 * 接口路由
 */
Route::group(['namespace' => 'Api'], function () {
	//首页
	 Route::post('index/index', 'IndexController@index');

     Route::post('merchant/merchants', 'MerchantController@merchants');
     Route::post('merchant/merchant_goods', 'MerchantController@merchantGoods');
     Route::post('merchant/merchants_two', 'MerchantController@merchantsTwo');
     

     Route::post('goods/index', 'GoodsController@index');
     Route::post('goods/goods', 'GoodsController@goods');
     Route::post('goods/details', 'GoodsController@details');
     Route::post('goods/comment', 'GoodsController@comment');
     Route::post('goods/collection', 'GoodsController@collection');
     Route::post('goods/goods_cate', 'GoodsController@goodsCate');
     Route::post('goods/good_list', 'GoodsController@goodList');
     Route::post('goods/specslist', 'GoodsController@specslist');
     Route::post('goods/hotsearch', 'GoodsController@hotsearch');
     
     Route::post('hotel/cate', 'HotelController@cate');
     Route::post('hotel/condition', 'HotelController@condition');
     Route::post('hotel/hotellist', 'HotelController@hotellist');
     Route::post('hotel/need', 'HotelController@need');
     
     Route::post('htorder/settlement', 'HtorderController@settlement');
     Route::post('htorder/add_order', 'HtorderController@addOrder');
     Route::post('htorder/orderdatails', 'HtorderController@orderdatails');
     Route::post('htorder/refund_reason', 'HtorderController@refundReason');
     Route::post('htorder/refund', 'HtorderController@refund');

     Route::post('Usersaddress/district', 'UsersaddressController@district');
     Route::post('Usersaddress/address_add', 'UsersaddressController@addressAdd');
     Route::post('Usersaddress/address','UsersaddressController@address');
     Route::post('Usersaddress/defualt','UsersaddressController@defualt');
     Route::post('Usersaddress/details','UsersaddressController@details');
     Route::post('Usersaddress/address_edit','UsersaddressController@addressEdit');
     Route::post('Usersaddress/address_del','UsersaddressController@addressDel');
     
     Route::post('users/merchant_record', 'UsersController@merchantRecord');
     Route::post('users/fabulous', 'UsersController@fabulous');
     Route::post('users/envelopes', 'UsersController@envelopes');
     Route::post('users/envelopes_add', 'UsersController@envelopesAdd');
     Route::post('users/upmodel', 'UsersController@upmodel');

     Route::post('common/uploads','CommonController@uploads');
     Route::post('common/pay_ways', 'CommonController@payWays');
     Route::post('common/merchant_type', 'CommonController@merchantType');
     Route::any('common/wxnotify', 'CommonController@wxnotify');
     Route::any('common/district', 'CommonController@district');
     Route::any('common/wxnotifyhotel', 'CommonController@wxnotifyhotel');

     Route::post('order/details', 'OrderController@details');

     Route::post('cart/index', 'CartController@index');
     Route::post('cart/delcar', 'CartController@delcar');
     Route::post('cart/update_num', 'CartController@update_num');
     Route::post('cart/addcar', 'CartController@addcar');

     Route::post('order/add_order', 'OrderController@addOrder');
     Route::post('order/add_order_car', 'OrderController@addOrderCar');
     Route::post('order/settlement', 'OrderController@settlement');
     Route::post('order/index', 'OrderController@index');
     Route::post('order/wx_pay', 'OrderController@wxPay');
     Route::post('order/pay', 'OrderController@pay');
     Route::post('order/express', 'OrderController@express');
     Route::post('order/confirm', 'OrderController@confirm');
     

     Route::post('login/login', 'LoginController@login');
     Route::post('login/send', 'LoginController@send');
     Route::post('login/caches', 'LoginController@caches');
     Route::post('login/login_p', 'LoginController@loginP');
     Route::post('login/reg_p', 'LoginController@regP');
     Route::post('login/forget', 'LoginController@forget');
     Route::any('login/wxlogin', 'LoginController@wxlogin');
     Route::post('login/bindmobile', 'LoginController@bindmobile');
     
     //商品管理
     Route::post('goods/manage','ManageController@index');
     Route::post('goods/manageDel','ManageController@manageDel');
     Route::post('goods/putaway','ManageController@putaway');
     Route::post('goods/soldOut','ManageController@soldOut');
     //订单管理
     Route::post('goods/ordersCancel','ManageController@ordersCancel');
     Route::post('goods/ordersDetails','ManageController@ordersDetails');
     Route::post('goods/audit','ManageController@audit');
     Route::post('goods/centre','ManageController@centre');
     Route::post('goods/affirm','ManageController@affirm');
     Route::post('goods/lists','ManageController@lists');
     Route::post('goods/awaitUpdate','ManageController@awaitUpdate');
     Route::post('goods/observer','ManageController@observer');
     Route::post('goods/delete','ManageController@delete');
     //店铺管理
     Route::post('goods/store','ManageController@store');
     Route::post('goods/saveStore','ManageController@saveStore');
     Route::post('goods/merchants','ManageController@merchants');
     //文件上传
     Route::post('goods/uploads','ManageController@uploads');
     //余额明细
     Route::post('goods/water','ManageController@water');
     //退出
     Route::post('goods/quit','ManageController@quit');
     //快递公司
     Route::post('goods/express','ManageController@express');
     //发货
     Route::post('goods/deliver','ManageController@deliver');

          //商家详情
     Route::post('details/list','DetailsController@list');
     //房间类型
     Route::post('details/hotelSel','DetailsController@hotelSel');
     //用户评论
     Route::post('details/commnets','DetailsController@commnets');
     //房间类型列表
     Route::post('details/room_list','DetailsController@room_list');
     //评论添加
     Route::post('details/addcomment','DetailsController@addcomment');

     Route::post('gourmet/delicious','GourmetController@delicious');
     Route::post('gourmet/list','GourmetController@list');
     Route::post('gourmet/details','GourmetController@details');
     Route::post('gourmet/reserve_list','GourmetController@reserve_list');
     Route::post('gourmet/dishtype','GourmetController@dishtype');
     Route::post('gourmet/dishes','GourmetController@dishes');
     Route::post('gourmet/comment','GourmetController@comment');
     Route::post('gourmet/booking','GourmetController@booking');
     Route::post('gourmet/shopping_num','GourmetController@shopping_num');
     Route::post('gourmet/add_foods','GourmetController@add_foods');
});
