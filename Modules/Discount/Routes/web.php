<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
        
   Route::group(['middleware' => ['web','tenant.context','IsSubscribed:discount_module'], 'prefix' => 'discount-templates', 'namespace' => 'Modules\Discount\Http\Controllers'], function () {
    //Route::delete('/', [NewdiscountController::class, 'getdiscounts']);
    Route::delete('/discount-templates', '\Modules\Discount\Http\Controllers\NewdiscountController@getdiscounts');
    Route::post('/store', '\Modules\Discount\Http\Controllers\NewdiscountController@store');

});
