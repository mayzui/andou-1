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

     Route::post('goods/index', 'GoodsController@index');
     Route::post('goods/goods', 'GoodsController@goods');
     Route::post('goods/details', 'GoodsController@details');
     Route::post('goods/comment', 'GoodsController@comment');
     Route::post('goods/collection', 'GoodsController@collection');
     Route::post('goods/goods_cate', 'GoodsController@goodsCate');
     Route::post('goods/good_list', 'GoodsController@goodList');
     Route::post('goods/specslist', 'GoodsController@specslist');
     
     Route::post('Usersaddress/district', 'UsersaddressController@district');
     Route::post('Usersaddress/address_add', 'UsersaddressController@addressAdd');
     Route::post('Usersaddress/address','UsersaddressController@address');
     Route::post('Usersaddress/defualt','UsersaddressController@defualt');
     Route::post('Usersaddress/details','UsersaddressController@details');
     Route::post('Usersaddress/address_edit','UsersaddressController@addressEdit');
     Route::post('Usersaddress/address_del','UsersaddressController@addressDel');
     
     Route::post('users/merchant_record', 'UsersController@merchantRecord');
     Route::post('users/fabulous', 'UsersController@fabulous');

     Route::post('cart/index', 'CartController@index');
     Route::post('cart/delcar', 'CartController@delcar');
     Route::post('cart/update_num', 'CartController@update_num');
     Route::post('cart/addcar', 'CartController@addcar');

     Route::post('order/add_order', 'OrderController@addOrder');
     Route::post('order/add_order_car', 'OrderController@addOrderCar');
     Route::post('order/settlement', 'OrderController@settlement');
     Route::post('order/index', 'OrderController@index');
     Route::post('order/wx_pay', 'OrderController@wxPay');

     Route::post('login/login', 'LoginController@login');
     Route::post('login/send', 'LoginController@send');
     Route::post('login/cache', 'LoginController@cache');
     Route::post('login/login_p', 'LoginController@loginP');
     Route::post('login/reg_p', 'LoginController@regP');
     Route::post('login/forget', 'LoginController@forget');


     //商品管理
     Route::post('goods/manage','ManageController@index');
     Route::post('goods/manageDel','ManageController@manageDel');
     Route::post('goods/putaway','ManageController@putaway');
     Route::post('goods/soldOut','ManageController@soldOut');
     Route::post('goods/manageUpd','ManageController@manageUpd');
});
