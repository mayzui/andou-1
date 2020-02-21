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
        Route::get('merchants/updateStatus','MerchantsController@updateStatus')->name('merchants.updateStatus');
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
        Route::get('hotel/classification','HotelController@classification')->name('hotel.classification');        // 酒店分类
        Route::get('hotel/hotelStatus','HotelController@hotelStatus')->name('hotel.hotelStatus');        // 酒店状态
        // 核销用户
        Route::match(['get','post'],'hotel/write_off','HotelController@write_off')->name('hotel.write_off');
        // 确认退款
        Route::match(['get','post'],'hotel/return_money','HotelController@return_money')->name('hotel.return_money');

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
        Route::get('foods/status','FoodsController@status')->name('foods.status'); // 禁用商家
        // 订单总管理
        Route::match(['get','post'],'foods/orders','FoodsController@orders')->name('foods.orders');
        Route::match(['get','post'],'foods/orderschange','FoodsController@orderschange')->name('foods.orderschange'); // 新增 and 修改
        Route::get('foods/ordersdel','FoodsController@ordersdel')->name('foods.ordersdel'); // 删除
        Route::get('foods/return_money','FoodsController@return_money')->name('foods.return_money'); // 同意退款
        Route::get('foods/return_refuse','FoodsController@return_refuse')->name('foods.return_refuse'); // 拒绝退款
        // 菜品详情
        Route::match(['get','post'],'foods/information','FoodsController@information')->name('foods.information');
        Route::match(['get','post'],'foods/informationadd','FoodsController@informationadd')->name('foods.informationadd'); // 新增 and 修改
        Route::get('foods/informationdel','FoodsController@informationdel')->name('foods.informationdel'); // 删除
        Route::get('foods/informationStatus','FoodsController@informationStatus')->name('foods.informationStatus'); // 修改状态
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

        Route::match(['get','post'],'foods/write_off','FoodsController@write_off')->name('foods.write_off');    // 饭店核销
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
        //入住需知
        Route::any('know/index','KnowController@index')->name('know.index');
        Route::any('know/www','KnowController@www')->name('know.add');
        // 排序
        Route::get('shop/sort','ShopController@sort')->name('shop.sort');
        // 商城商户
        Route::match(['get','post'],'shop/mall_merchants','ShopController@mall_merchants')->name('shop.mall_merchants');
        Route::get('shop/shopStatus','ShopController@shopStatus')->name('shop.shopStatus'); // 修改状态

        // 语音提醒
        Route::post('indexs/voice_play','IndexsController@voice_play')->name('indexs.voice_play'); // 语音提醒


        Route::get('hotel/decoration','HotelController@decoration')->name('hotel.decoration'); // 环境设施
        Route::post('hotel/addDecoration','HotelController@addDecoration')->name('hotel.addDecoration'); // 新增环境图片
        //秒杀管理
        Route::get('seckill/list','SeckillController@list')->name('seckill.list'); //秒杀列表
        Route::get('seckill/del','SeckillController@killDel')->name('seckill.killdel'); //秒杀下架
        Route::get('seckill/dels','SeckillController@killDels')->name('seckill.killdels'); //秒杀删除
        Route::match(['get','post'],'seckill/change','SeckillController@killUpd')->name('seckill.killupd');  //秒杀详情
        Route::match(['get','post'],'seckill/edit','SeckillController@killEdit')->name('seckill.edit');  //秒杀编辑
        Route::match(['get','post'],'seckill/addkill','SeckillController@addKill')->name('seckill.addkill');  //新增秒杀商品页
        Route::match(['get','post'],'seckill/addkilldata','SeckillController@addkillData')->name('seckill.addkilldata');  //新增秒杀商品
        Route::match(['get','post'],'seckill/sku','SeckillController@sku')->name('seckill.sku');  //秒杀商品规格
        Route::match(['get','post'],'seckill/count','SeckillController@killCount')->name('seckill.count');  //秒杀统计
        Route::match(['get','post'],'seckill/countdel','SeckillController@countDel')->name('seckill.countdel');  //秒杀统计删除
    });

});
?>