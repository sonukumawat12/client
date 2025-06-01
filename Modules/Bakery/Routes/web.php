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

Route::middleware(['web',  'SetSessionData', 'auth', 'language', 'timezone','tenant.context'])->prefix('bakery')->group(function() {
    Route::get('/', 'BakeryController@index');
    
    Route::get('/fleet/view_opening_balance/{id}', 'FleetController@viewopeningbalance');
    Route::get('/fleet/edit_opening_balance/{id}', 'FleetController@editOpeningBalance');
    Route::post('/fleet/update_opening_balance', 'FleetController@updateOpeningBalance');
    Route::post('/vehicle_check', 'FleetController@vehicle_check')->name('bakery_vehicle_check');
    
    Route::resource('/drivers', 'DriverController');
    Route::resource('/routes', 'RouteController');
    Route::resource('/fleets', 'FleetController');
    
    Route::resource('/invoice-no', 'BakeryInvoiceNumberController');
    Route::resource('/products', 'BakeryProductController');
    
    Route::get('/get-products-returns', 'BakeryLoadingReturnController@getProducts');
    
    Route::get('/get-products', 'BakeryLoadingController@getProducts');
    Route::get('/get-products-show/{id}', 'BakeryLoadingController@getProductsShow');
    Route::resource('/bakery-loading', 'BakeryLoadingController');
    Route::resource('/bakery-loading-return', 'BakeryLoadingReturnController');
    
    Route::get('/settings', 'BakeryController@settings');
    Route::get('/activity-log', 'BakeryController@getUserActivityReport');

    Route::get('/fleet-test', 'BakeryController@fleet');
    
    //common controller for document & note
    Route::get('get-document-note-page', 'BakeryUserDocumentAndNoteController@getDocAndNoteIndexPage');
    Route::post('post-document-upload', 'BakeryUserDocumentAndNoteController@postMedia');
    Route::resource('pumper-note-documents', 'BakeryUserDocumentAndNoteController');

    
    
    Route::get('/bakery-users/update-passcode', 'BakeryUserController@update_passcode');
    Route::post('/bakery-users/update-passcode', 'BakeryUserController@store_passcode');
    // Route::post('/bakery-users/save-import', 'BakeryUserController@saveImport');
    // Route::get('/bakery-users/ledger', 'BakeryUserController@getLedger');
    // Route::get('/bakery-users/list-commission/{id}', 'BakeryUserController@listCommission');
    Route::get('/bakery-users/toggle-active/{id}', 'BakeryUserController@toggleActivate');
    // Route::get('/bakery-users/get-dashboard-data', 'BakeryUserController@getDashboardData');
    Route::get('/bakery-users/dashboard', 'BakeryUserController@dashboard');
    Route::get('/bakery-users/check-passcode', 'BakeryUserController@checPasscode');
    Route::get('/bakery-users/check-username', 'BakeryUserController@checUsername');
    Route::get('/bakery-users/set-main-system-session', 'BakeryUserController@setMainSystemSession');
    Route::resource('/bakery-users', 'BakeryUserController');

});


Route::middleware(['web',  'SetSessionData', 'auth', 'language', 'timezone','tenant.context'])->prefix('loading')->group(function() {
    Route::get('/settings', 'LoadingController@settings');

    Route::resource('/loading', 'LoadingController');

});
