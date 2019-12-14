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
        Route::get('config/update/{id}','ConfigController@update')->name('config.update');
        Route::post('config/store','ConfigController@store')->name('config.store');
        Route::get('config/delete/{id}','ConfigController@delete')->name('config.delete');



        // 区域管理
        Route::get('districts/index','DistrictsController@index')->name('districts.index');
        Route::get('districts/create','DistrictsController@add')->name('districts.add');
        Route::get('districts/update/{id}','DistrictsController@update')->name('districts.update');
        Route::post('districts/store','DistrictsController@store')->name('districts.store');
        Route::get('districts/close/{id}','DistrictsController@delete')->name('districts.close');


        // 商品板块
        Route::resource('shop','ShopController',['only'=>['index','orders']]);
        Route::get('shop/setStatus/{field}/{status}/{id}','ShopController@setStatus')->name('shop.setStatus');
        Route::get('shop/goods','ShopController@goods')->name('shop.goods');
        Route::get('shop/create','ShopController@create')->name('shop.create');
        Route::post('shop/store','ShopController@store')->name('shop.store');
        Route::get('shop/update','ShopController@update')->name('shop.update');
        Route::get('shop/destroy','ShopController@destroy')->name('shop.destroy');

        // 商品属性
        Route::get('shop/addAttr','ShopController@addAttr')->name('shop.addAttr');
        Route::get('shop/attrUpdate/{id}','ShopController@attrUpdate')->name('shop.attrUpdate');
        Route::get('shop/attrDelete/{id}','ShopController@attrDelete')->name('shop.attrDelete');
        Route::get('shop/addAttrValue/{id}','ShopController@addAttrValue')->name('shop.addAttrValue');
        Route::post('shop/attrStore','ShopController@attrStore')->name('shop.attrStore');
        Route::post('shop/getAttr','ShopController@getAttr')->name('shop.getAttr');
        Route::get('shop/goodsAttr','ShopController@goodsAttr')->name('shop.goodsAttr');
        Route::post('shop/saveAttrValue','ShopController@saveAttrValue')->name('shop.saveAttrValue');
        Route::post('shop/storeAlbum','ShopController@storeAlbum')->name('shop.storeAlbum');
        Route::post('shop/storeComplateAttrs','ShopController@storeComplateAttrs')->name('shop.storeComplateAttrs');


        // 商品分类
        Route::get('shop/goodsCate','ShopController@goodsCate')->name('shop.goodsCate');
        Route::get('shop/cateAdd','ShopController@cateAdd')->name('shop.cateAdd');
        Route::get('shop/cateEdit/{id}','ShopController@cateEdit')->name('shop.cateEdit');
        Route::any('shop/cateStore','ShopController@cateStore')->name('shop.cateStore');
        Route::any('shop/cateDelete/{id}','ShopController@cateDelete')->name('shop.cateDelete');
        Route::post('shop/getCateChildren','ShopController@getCateChildren')->name('shop.getCateChildren');




        // 商品
        Route::get('shop/goodsAdd','ShopController@goods')->name('shop.goodsAdd');
        Route::get('foods/information','FoodsController@information')->name('foods.information');
        Route::get('foods/information1','FoodsController@information')->name('hotel.books');

        // 活动
        Route::get('shop/activity','ActivityController@activity')->name('shop.activity');


        // 快递
        Route::get('shop/express','ShopController@express')->name('shop.express');
        Route::get('shop/createExpress','ShopController@createExpress')->name('shop.createExpress');
        Route::get('shop/updateExpress/{id}','ShopController@updateExpress')->name('shop.updateExpress');
        Route::post('shop/storeExpress','ShopController@storeExpress')->name('shop.storeExpress');
        Route::get('shop/deleteExpress/{id}','ShopController@deleteExpress')->name('shop.deleteExpress');
        Route::get('shop/addExpressAttrs/{id}','ShopController@addExpressAttrs')->name('shop.addExpressAttrs');
        Route::get('shop/expressAttr','ShopController@expressAttr')->name('shop.expressAttr');
        Route::post('shop/storeExpressAttrs','ShopController@storeExpressAttrs')->name('shop.storeExpressAttrs');
        Route::get('shop/deleteExpressAttr/{id}','ShopController@deleteExpressAttr')->name('shop.deleteExpressAttr');

        // 统计
        Route::get('shop/statics','ShopController@statics')->name('shop.statics');


        Route::get('shop/orders','ShopController@orders')->name('shop.orders');
        Route::get('shop/goodsBrand','ShopController@goodsBrand')->name('shop.goodsBrand');
        Route::get('shop/brandAdd','ShopController@brandAdd')->name('shop.brandAdd');
        Route::get('shop/brandUpdate/{id}','ShopController@brandUpdate')->name('shop.brandUpdate');
        Route::get('shop/brandDelete/{id}','ShopController@brandDelete')->name('shop.brandDelete');
        Route::post('shop/brandStore','ShopController@brandStore')->name('shop.brandStore');

        // 优惠券

        Route::get('coupon/list','CouponController@list')->name('coupon.list');
        Route::get('coupon/create','CouponController@create')->name('coupon.create');
        Route::get('coupon/update','CouponController@update')->name('coupon.update');
        Route::get('coupon/delete','CouponController@delete')->name('coupon.delete');
        Route::get('coupon/useLog','CouponController@useLog')->name('coupon.useLog');
        Route::get('coupon/getLog','CouponController@getLog')->name('coupon.getLog');

        // 财务中心
        Route::get('finance/integral','FinanceController@integral')->name('finance.integral');
        Route::get('finance/cashOut','FinanceController@cashOut')->name('finance.cashOut');
        Route::get('finance/cashLogs','FinanceController@cashLogs')->name('finance.cashLogs');
        Route::get('finance/charge','FinanceController@charge')->name('finance.charge');

        // 用户中心
        Route::get('user/user_list','UserController@user_list')->name('user.user_list');
        Route::match(['get','post'],'user/user_listChange','UserController@user_listChange')->name('user.user_listChange'); // 新增 and 修改
        Route::get('user/user_listDel','UserController@user_listDel')->name('user.user_listDel');   // 删除

        Route::get('user/integralLog','UserController@integralLog')->name('user.integralLog');     // 积分记录
        Route::get('user/charge','UserController@charge')->name('user.charge');                      // 用户充值
        Route::get('user/cashOut','UserController@cashOut')->name('user.cashOut');                   // 用户提现

        Route::get('user/cashLogs','UserController@cashLogs')->name('user.cashLogs');               // 用户流水
        Route::match(['get','post'],'user/cashLogsChange','UserController@cashLogsChange')->name('user.cashLogsChange'); // 新增 and 修改
        Route::get('user/cashLogsDel','UserController@cashLogsDel')->name('user.cashLogsDel');   // 删除

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
        Route::get('banner/delete/{id}','BannerController@delete')->name('banner.delete');


        // 商户板块
        Route::get('merchants/index','MerchantController@index')->name('merchants.index');
        Route::get('merchants/merchant_type','MerchantController@index')->name('merchants.merchant_type');
        Route::get('hotel/merchant','MerchantController@merchant')->name('hotel.merchant');

        Route::get('foods/index','FoodsController@index')->name('foods.index');
        Route::get('foods/spec','FoodsController@spec')->name('foods.spec');
        Route::get('foods/cart','FoodsController@cart')->name('foods.cart');
        Route::get('foods/order','FoodsController@order')->name('foods.order');
        Route::get('user/merchant','userController@merchant')->name('user.merchant');
        Route::get('user/merchant_detailed','userController@merchant_detailed')->name('user.merchant_detailed');
        Route::get('user/users_detailed','userController@users_detailed')->name('user.users_detailed');
        Route::get('merchants.industry','userController@industry')->name('merchants.industry');



        Route::get('foods/orders','userController@orders')->name('foods.orders');
        Route::get('foods/examine','userController@examine')->name('foods.examine');
        Route::get('foods/administration','userController@administration')->name('foods.administration');
        Route::get('foods/comment','userController@administration')->name('foods.comment');
        Route::get('foods/merchant_classification','userController@merchant_classification')->name('foods.merchant_classification');
        Route::get('foods/set_meal','userController@set_meal')->name('foods.set_meal');
        Route::get('finance/integral_type','FinanceController@integral_type')->name('finance.integral_type');
        Route::get('finance/integral_record','FinanceController@integral_record')->name('finance.integral_record');
        Route::get('finance/integral_type','FinanceController@integral_type')->name('finance.integral_type');


    });

    // 图片上传
    Route::any('upload/uploadImage','UploadController@uploadImage');
});


