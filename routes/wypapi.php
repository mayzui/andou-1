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
    // 关于我们
    Route::post('index/about', 'IndexController@about');
    // 钱包
    Route::post('wallet/index', 'WalletController@index');      // 余额明细
    Route::post('wallet/cash', 'WalletController@cash');      // 提现明细
    Route::post('wallet/cash_withdrawal', 'WalletController@cash_withdrawal');      // 余额提现
    Route::post('wallet/recharge', 'WalletController@recharge');      // 余额充值
    Route::post('wallet/integral', 'WalletController@integral');      // 积分明细
    // 个人中心
    Route::post('wallet/personal', 'WalletController@personal');      // 积分明细
    // 意见反馈
    Route::post('opinion/index', 'OpinionController@index');      // 反馈意见
    Route::post('opinion/set', 'OpinionController@set');      // 设置
    // 检测新版本
    Route::post('edition/new_edition', 'EditionController@new_edition');      // 检测新版本
    // 发表评论
    Route::post('order/addcomment', 'OrderController@addcomment');
    // 退款
    Route::post('refund/reason', 'RefundController@reason');    // 退款原因
    Route::post('refund/apply', 'RefundController@apply');    // 申请退款




});
