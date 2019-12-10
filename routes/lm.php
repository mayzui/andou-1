<?php  
/**后台模块**/
Route::group(['namespace' => 'Admin','prefix' => 'admin'], function (){
/**需要登录认证模块**/
    Route::middleware(['auth:admin','rbac'])->group(function (){//LM
        //商户管理
        Route::match(['get','post'],'merchants/index','MerchantsController@index')->name('merchants.index');
        Route::get('merchants/reg','MerchantsController@reg')->name('merchants.reg');
        Route::get('merchants/del','MerchantsController@del')->name('merchants.del');
        Route::get('merchants/merchant_type','MerchantsController@merchantType')->name('merchants.merchant_type');
        Route::match(['get','post'],'merchants/merchant_type_add','MerchantsController@merchantTypeAdd')->name('merchants.merchant_type_add');
        Route::get('merchants/industry','MerchantsController@industry')->name('merchants.industry');
        Route::get('merchants/industry_add','MerchantsController@industryAdd')->name('merchants.industry_add');
        Route::get('merchants/industry_del','MerchantsController@industryDel')->name('merchants.industry_del');
        //酒店管理
        Route::match(['get','post'],'hotel/index','HotelController@index')->name('hotel.index');
        Route::get('hotel/faci','HotelController@faci')->name('hotel.faci');
        Route::match(['get','post'],'hotel/faci_add','HotelController@faciAdd')->name('hotel.faci_add');
        Route::match(['get','post'],'hotel/add','HotelController@add')->name('hotel.add');
        Route::get('hotel/faci_del','HotelController@faciDel')->name('hotel.faci_del');
        Route::get('hotel/del','HotelController@del')->name('hotel.del');
        Route::get('hotel/status','HotelController@status')->name('hotel.status');
        Route::match(['get','post'],'hotel/books','HotelController@books')->name('hotel.books');
        Route::get('hotel/text','HotelController@text')->name('hotel.text');
        Route::match(['get','post'],'hotel/merchant','HotelController@merchant')->name('hotel.merchant');
        //个人中心
        Route::get('user/merchant','UserController@merchant')->name('user.merchant');
        Route::match(['get','post'],'user/merchant_update','UserController@merchantUpdate')->name('user.merchant_update');
        Route::get('user/address','UserController@address')->name('user.address');
        Route::get('user/list','UserController@list')->name('user.list');
        //点餐模块
        // 菜品分类
        Route::get('foods/index','FoodsController@index')->name('foods.index');
        Route::match(['get','post'],'foods/add','FoodsController@add')->name('foods.add');  // 新增 and 修改
        Route::get('foods/del','FoodsController@del')->name('foods.del'); // 删除
        // 菜品规格
        Route::get('foods/spec','FoodsController@spec')->name('foods.spec');
        Route::match(['get','post'],'foods/specadd','FoodsController@specadd')->name('foods.specadd'); // 新增 and 修改
        Route::get('foods/specdel','FoodsController@specdel')->name('foods.specdel'); // 删除
        // 菜品详情
        Route::get('foods/information','FoodsController@information')->name('foods.information');
        Route::match(['get','post'],'foods/informationadd','FoodsController@informationadd')->name('foods.informationadd'); // 新增 and 修改
        Route::get('foods/informationdel','FoodsController@informationdel')->name('foods.informationdel'); // 删除
        Route::get('foods/cart','FoodsController@cart')->name('foods.cart');
        Route::get('foods/order','FoodsController@order')->name('foods.order');
        Route::get('foods/orders','FoodsController@orders')->name('foods.orders');
        Route::get('foods/verify','FoodsController@verify')->name('foods.verify');
        Route::get('foods/verify','FoodsController@examine')->name('foods.examine');
        Route::get('foods/administration','FoodsController@administration')->name('foods.administration');
        Route::get('shop/goodsAttr','ShopController@goodsAttr')->name('shop.goodsAttr');
        Route::get('finance/integral','FinanceController@integral')->name('finance.integral');
        Route::get('finance/charge','FinanceController@charge')->name('finance.charge');
        Route::get('finance/cashOut','FinanceController@cashOut')->name('finance.cashOut');
        Route::get('finance/cashLogs','FinanceController@cashLogs')->name('finance.cashLogs');
        
    });
});
?>