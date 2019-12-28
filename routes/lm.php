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
        Route::match(['get','post'],'merchants/industry_add','MerchantsController@industryAdd')->name('merchants.industry_add');
        Route::get('merchants/industry_del','MerchantsController@industryDel')->name('merchants.industry_del');
        Route::match(['get','post'],'merchants/information','MerchantsController@information')->name('merchants.information');
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
        Route::get('hotel/commnets','HotelController@commnets')->name('hotel.commnets');
        Route::match(['get','post'],'hotel/commnetsAdd','HotelController@commnetsAdd')->name('hotel.commnetsAdd');
        Route::get('hotel/commnetsDel','HotelController@commnetsDel')->name('hotel.commnetsDel');

        //个人中心
        Route::get('user/merchant','UserController@merchant')->name('user.merchant');
        Route::match(['get','post'],'user/merchant_update','UserController@merchantUpdate')->name('user.merchant_update');
        Route::get('user/address','UserController@address')->name('user.address');
        Route::get('user/list','UserController@list')->name('user.list');

        //饭店模块
        // 饭店商家管理
        Route::match(['get','post'],'foods/administration','FoodsController@administration')->name('foods.administration');
        Route::get('foods/administrationStatus','FoodsController@administrationStatus')->name('foods.administrationStatus'); // 删除
        // 饭店商家审核
        Route::match(['get','post'],'foods/examine','FoodsController@examine')->name('foods.examine');
        Route::get('foods/examinepass','FoodsController@examinepass')->name('foods.examinepass'); // 删除
        // 订单总管理
        Route::match(['get','post'],'foods/orders','FoodsController@orders')->name('foods.orders');
        Route::match(['get','post'],'foods/orderschange','FoodsController@orderschange')->name('foods.orderschange'); // 新增 and 修改
        Route::get('foods/ordersdel','FoodsController@ordersdel')->name('foods.ordersdel'); // 删除
        // 菜品详情
        Route::match(['get','post'],'foods/information','FoodsController@information')->name('foods.information');
        Route::match(['get','post'],'foods/informationadd','FoodsController@informationadd')->name('foods.informationadd'); // 新增 and 修改
        Route::get('foods/informationdel','FoodsController@informationdel')->name('foods.informationdel'); // 删除
        // 菜品分类
        Route::match(['get','post'],'foods/index','FoodsController@index')->name('foods.index');
        Route::match(['get','post'],'foods/add','FoodsController@add')->name('foods.add');  // 新增 and 修改
        Route::get('foods/del','FoodsController@del')->name('foods.del'); // 删除
        // 菜品套餐
        Route::match(['get','post'],'foods/set_meal','FoodsController@set_meal')->name('foods.set_meal');
        Route::match(['get','post'],'foods/set_mealchange','FoodsController@set_mealchange')->name('foods.set_mealchange');  // 新增 and 修改
        Route::get('foods/set_mealdel','FoodsController@set_mealdel')->name('foods.set_mealdel'); // 删除
        Route::get('foods/set_mealstatus','FoodsController@set_mealstatus')->name('foods.set_mealstatus'); // 修改上下架状态
        Route::match(['get','post'],'foods/set_meal_information','FoodsController@set_meal_information')->name('foods.set_meal_information');  // 新增 and 修改
        Route::match(['get','post'],'foods/set_meal_informationChange','FoodsController@set_meal_informationChange')->name('foods.set_meal_informationChange');  // 修改套餐中的商品信息
        // 菜品规格
        Route::match(['get','post'],'foods/spec','FoodsController@spec')->name('foods.spec');
        Route::match(['get','post'],'foods/specadd','FoodsController@specadd')->name('foods.specadd'); // 新增 and 修改
        Route::get('foods/specdel','FoodsController@specdel')->name('foods.specdel'); // 删除
        // 饭店评论
        Route::match(['get','post'],'foods/commnets','FoodsController@commnets')->name('foods.commnets');
        Route::match(['get','post'],'foods/commnetsAdd','FoodsController@commnetsAdd')->name('foods.commnetsAdd');
        Route::get('foods/commnetsDel','FoodsController@commnetsDel')->name('foods.commnetsDel');


        /*
         *      财务管理模块
         * */
        // 感恩币中心
        Route::match(['get','post'],'finance/integralChange','FinanceController@integralChange')->name('finance.integralChange'); // 新增 and 修改
        Route::get('finance/integralDel','FinanceController@integralDel')->name('finance.integralDel'); // 删除
        // 感恩币类型
        Route::get('finance/integral_type','FinanceController@integral_type')->name('finance.integral_type');
        Route::match(['get','post'],'finance/integral_typeChange','FinanceController@integral_typeChange')->name('finance.integral_typeChange'); // 新增 and 修改
        Route::get('finance/integral_typeDel','FinanceController@integral_typeDel')->name('finance.integral_typeDel'); // 删除
        // 感恩币明细
        Route::get('finance/integral_record','FinanceController@integral_record')->name('finance.integral_record');
        Route::match(['get','post'],'finance/integral_recordChange','FinanceController@integral_recordChange')->name('finance.integral_recordChange'); // 新增 and 修改
        Route::get('finance/integral_recordDel','FinanceController@integral_recordDel')->name('finance.integral_recordDel'); // 删除

        // 充值中心
        Route::match(['get','post'],'finance/chargeChange','FinanceController@chargeChange')->name('finance.chargeChange'); // 新增 and 修改
        Route::get('finance/chargeDel','FinanceController@chargeDel')->name('finance.chargeDel'); // 删除
        // 提现管理
        Route::match(['get','post'],'finance/cashOutChange','FinanceController@cashOutChange')->name('finance.cashOutChange'); // 新增 and 修改
        Route::get('finance/cashOutDel','FinanceController@cashOutDel')->name('finance.cashOutDel'); // 删除
        Route::get('finance/cashOutExamine','FinanceController@cashOutExamine')->name('finance.cashOutExamine');

        // 平台流水
        Route::get('finance/cashLogsDel','FinanceController@cashLogsDel')->name('finance.cashLogsDel'); // 删除
        Route::get('banner/notice','BannerController@notice')->name('banner.notice'); 
        Route::get('banner/noticedel','BannerController@noticedel')->name('banner.noticedel');
        Route::match(['get','post'],'banner/noticeedit','BannerController@noticeedit')->name('banner.noticeedit');
        Route::get('shop/hotkeywords','ShopController@hotkeywords')->name('shop.hotkeywords');
        Route::get('shop/hotkeywordsdel','ShopController@hotkeywordsdel')->name('shop.hotkeywordsdel');
        Route::match(['get','post'],'shop/hotkeywordsedit','ShopController@hotkeywordsedit')->name('shop.hotkeywordsedit');  
    });
});
?>