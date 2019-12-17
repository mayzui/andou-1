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
     Route::post('index/merchants', 'IndexController@merchants');
     Route::post('login/login', 'LoginController@login');
     Route::post('login/send', 'LoginController@send');
     Route::post('login/cache', 'LoginController@cache');
     Route::post('login/login_p', 'LoginController@loginP');
     Route::post('login/reg_p', 'LoginController@regP');
     Route::post('login/forget', 'LoginController@forget');
});
