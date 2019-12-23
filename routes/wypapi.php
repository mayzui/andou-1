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
 *      接口路由
 */
Route::group(['namespace' => 'Api'], function () {
    //提示信息
    Route::post('index/information', 'IndexController@information');
    // 消息中心
    Route::post('index/notification_center', 'IndexController@notification_center');
    // 钱包
    Route::post('wallet/index', 'WalletController@index');      // 余额明细
    Route::post('wallet/cash', 'WalletController@cash');      // 提现明细
    Route::post('wallet/cash_withdrawal', 'WalletController@cash_withdrawal');      // 余额提现
    Route::post('wallet/integral', 'WalletController@integral');      // 积分明细



});
