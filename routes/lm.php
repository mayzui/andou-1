<?php  
/**后台模块**/
Route::group(['namespace' => 'Admin','prefix' => 'admin'], function (){
/**需要登录认证模块**/
    Route::middleware(['auth:admin','rbac'])->group(function (){//LM
        //商户管理
        Route::match(['get','post'],'merchants/index','MerchantsController@index')->name('merchants.index');
        Route::get('merchants/reg','MerchantsController@reg')->name('merchants.reg');
        Route::get('merchants/merchant_type','MerchantsController@merchantType')->name('merchants.merchant_type');
        Route::match(['get','post'],'merchants/merchant_type_add','MerchantsController@merchantTypeAdd')->name('merchants.merchant_type_add');
        //酒店管理
        Route::get('hotel/index','hotelController@index')->name('hotel.index');
    });
});
?>