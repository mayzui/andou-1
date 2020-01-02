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
        Route::get('index/round', 'IndexsController@round')->name('index.round'); //右下角数据分析
        Route::get('index/census', 'IndexsController@census')->name('index.census'); //右侧左边数据分析

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
        Route::get('shop/goodsDel','ShopController@goodsDel')->name('shop.goodsDel');
        Route::get('shop/create','ShopController@create')->name('shop.create');
        Route::post('shop/store','ShopController@store')->name('shop.store');
        Route::get('shop/update','ShopController@update')->name('shop.update');
        Route::get('shop/destroy','ShopController@destroy')->name('shop.destroy');
        Route::get('shop/commnets','ShopController@commnets')->name('shop.commnets');
        Route::match(['get','post'],'shop/commnetsAdd','ShopController@commnetsAdd')->name('shop.commnetsAdd');
        Route::get('shop/commnetsDel','ShopController@commnetsDel')->name('shop.commnetsDel');
        Route::post('shop/deleteAll','ShopController@deleteAll')->name('shop.deleteAll');       // 批量删除数据


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
        Route::match(['get','post'],'shop/storeComplateAttrs','ShopController@storeComplateAttrs')->name('shop.storeComplateAttrs');

        // 分类管理
        Route::get('shop/goodsCate','ShopController@goodsCate')->name('shop.goodsCate');
        Route::get('shop/cateAdd','ShopController@cateAdd')->name('shop.cateAdd');
        Route::get('shop/cateEdit/{id}','ShopController@cateEdit')->name('shop.cateEdit');
        Route::any('shop/cateStore','ShopController@cateStore')->name('shop.cateStore');
        Route::any('shop/cateDelete/{id}','ShopController@cateDelete')->name('shop.cateDelete');
        Route::post('shop/getCateChildren','ShopController@getCateChildren')->name('shop.getCateChildren');

        // 商家管理
        Route::get('shop/shopMerchant','ShopController@shopMerchant')->name('shop.shopMerchant');
        Route::get('shop/shopMerchantOrder','ShopController@shopMerchantOrder')->name('shop.shopMerchantOrder');    // 查询订单
        Route::get('shop/shopMerchantMoney','ShopController@shopMerchantMoney')->name('shop.shopMerchantMoney');    // 查询资金流水
        Route::get('shop/shopDiscount','ShopController@shopDiscount')->name('shop.shopDiscount');   // 平台优惠
        Route::match(['get','post'],'shop/information','ShopController@information')->name('shop.information');   // 商家详情

        // 物流信息
        Route::get('logistics/indexs','LogisticsController@indexs') -> name('logistics.indexs');
        Route::match(['get','post'],'logistics/goGoods','LogisticsController@goGoods') -> name('logistics.goGoods');     // 去发货
        Route::match(['get','post'],'logistics/readLogistics','LogisticsController@readLogistics') -> name('logistics.readLogistics');     // 查看物流详情


        // 商品分类
        Route::get('shop/merchants_goods_type','ShopController@merchants_goods_type')->name('shop.merchants_goods_type');
        Route::match(['get','post'],'shop/merchants_goods_typeChange','ShopController@merchants_goods_typeChange')->name('shop.merchants_goods_typeChange');
        Route::get('shop/merchants_goods_typeDel','ShopController@merchants_goods_typeDel')->name('shop.merchants_goods_typeDel');


        // 商品
        Route::get('shop/goodsAdd','ShopController@goods')->name('shop.goodsAdd');
        Route::get('foods/information','FoodsController@information')->name('foods.information');
        Route::get('foods/information1','FoodsController@information')->name('hotel.books');

        // 活动
        Route::get('shop/activity','ShopController@activity')->name('shop.activity');
        Route::match(['get','post'],'shop/activityChange','ShopController@activityChange')->name('shop.activityChange'); // 新增 and 修改
        Route::get('shop/activityDel','ShopController@activityDel')->name('shop.activityDel'); // 删除


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

        Route::get('shop/ordersAdd','ShopController@ordersAdd')->name('shop.ordersAdd');//添加订单测试数据
        Route::any('shop/ordersAdds','ShopController@ordersAdds')->name('shop.ordersAdds');//订单测试
        Route::get('shop/ordersDel','ShopController@ordersDel')->name('shop.ordersDel');//订单删除
        Route::get('shop/ordersUpd','ShopController@ordersUpd')->name('shop.ordersUpd');//订单修改
        Route::any('shop/ordersUpds','ShopController@ordersUpds')->name('shop.ordersUpds');//订单修改提交

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
        Route::get('coupon/uselogAdd','CouponController@uselogAdd')->name('coupon.uselogAdd');
        Route::any('coupon/uselogAdds','CouponController@uselogAdds')->name('coupon.uselogAdds');
        Route::get('coupon/useLogDel','CouponController@useLogDel')->name('coupon.useLogDel');
        Route::get('coupon/getLogDel','CouponController@getLogDel')->name('coupon.getLogDel');

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
        Route::match(['get','post'],'banner/positionEdit','BannerController@positionEdit')->name('banner.positionEdit');
        Route::get('banner/positionDel','BannerController@positionDel')->name('banner.positionDel');
        Route::get('banner/status/{status}/{id}','BannerController@status')->name('banner.status');

        Route::get('banner/index','BannerController@index')->name('banner.index');
        Route::get('banner/add','BannerController@add')->name('banner.add');
        Route::any('banner/store','BannerController@store')->name('banner.store');
        Route::get('banner/update/{id}','BannerController@update')->name('banner.update');
        Route::get('banner/delete/{id}','BannerController@delete')->name('banner.delete');
        Route::get('banner/delete/{id}','BannerController@delete')->name('banner.delete');
        Route::get('foods/cart','FoodsController@cart')->name('foods.cart');
        Route::get('user/merchant_detailed','UserController@merchant_detailed')->name('user.merchant_detailed');
        Route::get('user/users_detailed','UserController@users_detailed')->name('user.users_detailed');
        Route::get('foods/comment','UserController@administration')->name('foods.comment');
        Route::get('foods/merchant_classification','UserController@merchant_classification')->name('foods.merchant_classification');

        // 意见反馈
        Route::get('feedback/index','FeedbackController@index')->name('feedback.index');
        Route::get('feedback/indexDel','FeedbackController@indexDel')->name('feedback.indexDel');

        //关于我们
        Route::get('about/index','AboutController@index')->name('about.index');
        Route::match(['get','post'],'about/indexChange','AboutController@indexChange')->name('about.indexChange');

        // 退款原因 Refund reason
        Route::get('refund/index','RefundController@index')->name('refund.index');
        Route::match(['get','post'],'refund/indexChange','RefundController@indexChange')->name('refund.indexChange');
        Route::get('refund/indexDel','RefundController@indexDel')->name('refund.indexDel');

        // 售后服务
        Route::get('refund/aftermarket','RefundController@aftermarket')->name('refund.aftermarket');
        Route::match(['get','post'],'refund/aftermarketChange','RefundController@aftermarketChange')->name('refund.aftermarketChange');




        Route::match(['get','post'],'know/index','RefundController@aftermarketChange')->name('know.index');
    });

    // 图片上传
    Route::any('upload/uploadImage','UploadController@uploadImage');
});


