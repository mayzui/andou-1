<?php  
/**后台模块**/
Route::group(['namespace' => 'Admin','prefix' => 'admin'], function (){
/**需要登录认证模块**/
    Route::middleware(['auth:admin','rbac'])->group(function (){//LM
        Route::get('merchants/index','MerchantsController@index')->name('merchants.index');
    });
});
?>