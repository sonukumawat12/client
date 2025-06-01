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
use Illuminate\Support\Facades\Route;

Route::group([
    'middleware' => ['web', 'tenant.context','IsSubscribed:dsr_module'],
     'prefix' => 'dsr',
    'namespace' => '\Modules\Dsr\Http\Controllers'
    ], function(){
        
    Route::get('get-provinces-multiple', 'LocationsController@getProvincesMultiple');
    Route::get('get-districts-multiple', 'LocationsController@getDistrictsMultiple');
    Route::get('get-areas-multiple', 'LocationsController@getAreasMultiple');        

    Route::get('/', 'DsrController@index');
    Route::resource('fuel-providers', 'FuelProvidersController');
    Route::get('/get-fuel-providers', 'FuelProvidersController@allFuelProviders');
    Route::resource('designated-officer', 'DesignatedDsrController');
    Route::get('settings', 'DsrController@settings')->name('dsr.settings');
    
    Route::get('settings/edit-ob/{id}', 'DsrSettingsController@editOB');
    
    Route::get('report', 'DsrController@report')->name('dsr.report');
    
    Route::get('/get-dealers', 'DsrController@getDealers');
    Route::get('/get-report', 'DsrController@fetchReport');
    Route::get('/get-products', 'DsrController@fetchProducts');
    
    Route::resource('dsr-settings', 'DsrSettingsController');
    Route::post('create-area', 'DsrSettingsController@createArea')->name('dsr-settings.create-area');
    Route::post('create-district', 'DsrSettingsController@createDistrict');
    Route::post('create-province', 'DsrSettingsController@createProvice');
    Route::get('locations', 'LocationsController@index');
    Route::get('add-country', 'LocationsController@addCountry');
    Route::post('add-country', 'LocationsController@addCountry');
    Route::get('countries', 'LocationsController@countries');
    Route::get('provinces', 'LocationsController@provinces');
    Route::get('add-province', 'LocationsController@addProvince');
    Route::post('add-province', 'LocationsController@addProvince');
    Route::get('districts', 'LocationsController@districts');
    Route::get('add-district', 'LocationsController@addDistrict');
    Route::post('add-district', 'LocationsController@addDistrict');
    Route::get('areas', 'LocationsController@areas');
    Route::get('get-provinces/{country_id}', 'LocationsController@getProvinces');
    Route::get('get-districts/{province_id}', 'LocationsController@getDistricts');
    Route::get('get-areas/{district_id}', 'LocationsController@getAreas');
    Route::get('areas', 'LocationsController@areas');
    Route::get('add-areas', 'LocationsController@addarea');
    Route::post('add-areas', 'LocationsController@addarea');
    Route::post('add-accumulative-sale-purchase', 'DsrSettingsController@addAccumulativeSalePurchase');
    Route::get('list-accumulative-sale-purchase', 'DsrSettingsController@listAccumulativeSalePurchase');
    Route::get('bind-business/{id}', 'DesignatedDsrController@bindBusiness');
    Route::post('bind-business/{id}', 'DesignatedDsrController@bindBusiness');
});
