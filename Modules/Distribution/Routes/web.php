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

Route::group(['middleware' => ['web', 'auth', 'language','tenant.context'], 'prefix' => 'distribution'], function () {
    Route::resource('/areas', 'DistributionAreasController');
    Route::resource('/provinces', 'DistributionProvincesController');
    Route::resource('/districts', 'DistributionDistrictsController');
    Route::resource('/routes', 'DistributionRoutesController');
    
    Route::get('/districtdropdown/{id}', 'DistributionRoutesController@getProvincesDistricts');
    Route::get('/areadropdown/{id}', 'DistributionRoutesController@getDistrictAreas');
    
    Route::resource('/settings', 'SettingController');
   
});
