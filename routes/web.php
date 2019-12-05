<?php

/**
 * 后台路由
 */

/**后台模块**/
Route::group(['namespace' => 'Admin','prefix' => 'admin'], function (){

    Route::get('login','AdminsController@showLoginForm')->name('login');  //后台登陆页面

    Route::post('login-handle','AdminsController@loginHandle')->name('login-handle'); //后台登陆逻辑

    Route::get('logout','AdminsController@logout')->name('admin.logout'); //退出登录

    /**需要登录认证模块**/
    Route::middleware(['auth:admin','rbac'])->group(function (){

        Route::resource('index', 'IndexsController', ['only' => ['index']]);  //首页

        Route::get('index/main', 'IndexsController@main')->name('index.main'); //首页数据分析

        Route::get('admins/status/{statis}/{admin}','AdminsController@status')->name('admins.status');
        Route::get('admins/allow/{allow_in}/{admin}','AdminsController@allow')->name('admins.allow');

        Route::get('admins/delete/{admin}','AdminsController@delete')->name('admins.delete');


        Route::resource('admins','AdminsController',['only' => ['index', 'create', 'store', 'update', 'edit']]); //管理员

        Route::get('roles/access/{role}','RolesController@access')->name('roles.access');

        Route::post('roles/group-access/{role}','RolesController@groupAccess')->name('roles.group-access');

        Route::resource('roles','RolesController',['only'=>['index','create','store','update','edit','destroy'] ]);  //角色

        Route::get('rules/status/{status}/{rules}','RulesController@status')->name('rules.status');

        Route::resource('rules','RulesController',['only'=> ['index','create','store','update','edit','destroy'] ]);  //权限

        Route::resource('actions','ActionLogsController',['only'=> ['index','destroy'] ]);  //日志

        // 配置管理

        Route::get('config/index','ConfigController@index')->name('config.index');
        Route::get('config/add','ConfigController@add')->name('config.add');
        Route::get('config/update','ConfigController@update')->name('config.update');
        Route::post('config/store','ConfigController@update')->name('config.store');
        Route::post('config/delete','ConfigController@delete')->name('config.delete');


        Route::resource('shop','ShopController',['only'=>['index','create','store','update','edit','destroy','goods','goodsCate','orders']]);
        Route::get('shop/goods','ShopController@goods')->name('shop.goods');
        Route::get('shop/goodsCate','ShopController@goodsCate')->name('shop.goodsCate');
        Route::get('shop/orders','ShopController@orders')->name('shop.orders');
        Route::get('shop/goodsBrand','ShopController@goodsBrand')->name('shop.goodsBrand');

        // 广告板块
        Route::get('banner/positionIndex','BannerController@position')->name('banner.position');
        Route::get('banner/positionAdd','BannerController@positionAdd')->name('banner.positionAdd');
        Route::post('banner/positionStore','BannerController@positionStore')->name('banner.positionStore');
        Route::get('banner/positionEdit/{id}','BannerController@positionEdit')->name('banner.positionEdit');
        Route::get('banner/positionDel','BannerController@positionDel')->name('banner.positionDel');
        Route::get('banner/status/{status}/{id}','BannerController@status')->name('banner.status');

        Route::get('banner/index','BannerController@index')->name('banner.index');
        Route::get('banner/add','BannerController@add')->name('banner.add');
        Route::any('banner/store','BannerController@store')->name('banner.store');
        Route::get('banner/update/{id}','BannerController@update')->name('banner.update');
        Route::get('banner/delete/{id}','BannerController@delete')->name('banner.delete');

        // 商户板块
        Route::get('merchants/index','MerchantController@index')->name('merchants.index');
        Route::get('merchants/merchant_type','MerchantController@index')->name('merchants.merchant_type');

    });

    // 图片上传
    Route::post('upload/uploadImage','UploadController@uploadImage');
});

