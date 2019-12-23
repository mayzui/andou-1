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
    // 钱包
    Route::post('wallet/index', 'WalletController@index');

});
