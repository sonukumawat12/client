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

Route::group(['middleware' => ['web', 'auth', 'tenant.context'], 'prefix' => 'sales_discounts'], function () {
       Route::get('/', 'SalesDiscountsController@index')->name('salesdiscounts.index'); 
        Route::get('/get-Sales-Discount-List', 'SalesDiscountsController@getSalesDiscountList');
        Route::get('/{id}', 'SalesDiscountsController@edit');
        Route::get('/create-sales-discount', 'SalesDiscountsController@create');
        Route::post('/store-sales-discount', 'SalesDiscountsController@store');
        Route::get('/delete-sales-discount/{id}', 'SalesDiscountsController@destroy');
        Route::post('/updte-sales-discount/{id}', 'SalesDiscountsController@update');
        Route::get('/show/{id}', 'SalesDiscountsController@show');
});

 
