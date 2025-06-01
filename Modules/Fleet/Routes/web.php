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



Route::group(['middleware' => ['web', 'auth', 'language','tenant.context'], 'prefix' => 'fleet-management'], function () {
    Route::get('/fleet/view_opening_balance/{id}', 'FleetController@viewopeningbalance');
    Route::get('/fleet/add-fuel-details/{id}', 'FleetController@addFuelDetails');
    
    Route::get('/fleet/edit-fuel-details/{id}', 'FleetController@editFuelDetail');
    Route::post('/fleet/one-fuel-type', 'FleetController@oneFuelType');
    Route::delete('/fleet/destroy-fuel-details/{id}', 'FleetController@destroyFuelDetail');
    
    
    Route::post('/fleet/add-fuel-details', 'FleetController@storeFuelDetails');
    Route::post('/fleet/update-fuel-details/{id}', 'FleetController@updateFuelDetails');
    Route::get('/fleet/view-fuel-details', 'FleetController@fuelManagement');
    
    Route::get('/fleet/edit_opening_balance/{id}', 'FleetController@editOpeningBalance');
    Route::post('/fleet/update_opening_balance', 'FleetController@updateOpeningBalance');
    
    Route::get('/fleet/get-ledger/{id}', 'FleetController@getLedger');
    Route::resource('/fleet', 'FleetController');
    Route::get('/get-ledger-summary', 'FleetController@fetchLedgerSummarised');
    Route::post('/vehicle_check', 'FleetController@vehicle_check')->name('vehicle_check');
    Route::get('/opening_balance', 'FleetController@opening_balance');
    Route::get('/routes/get-details/{id}', 'RouteController@getDetails');
    Route::get('/routes/get-dropdown', 'RouteController@getRouteDropdown');
    Route::resource('/routes', 'RouteController');
    
    Route::resource('/trip-categories', 'TripCategoryController');
    
    Route::resource('/drivers', 'DriverController');
    Route::get('show-driver/{id}', [\Modules\Fleet\Http\Controllers\DriverController::class, 'showDriver']);
    Route::get('show-helper/{id}', [\Modules\Fleet\Http\Controllers\HelperController::class, 'showHelper']);
    Route::resource('/vehicle-category', 'VehicleCategoryController');
    Route::resource('/account-nos', 'FleetAccountNumberController');
    Route::resource('/fleet-logos', 'FleetLogoController');
    Route::resource('/helpers', 'HelperController');
    Route::resource('/settings', 'SettingController');
    Route::resource('/fuels', 'FuelController');
    Route::get('/newPrice', 'FuelController@newPrice')->name('newPrice');
    Route::put('/addNewPrice', 'FuelController@addNewPrice')->name('addNewPrice');
    Route::get('/createIncentives', 'SettingController@createIncentives');
    Route::get('/viewIncentive/{id}', 'RouteController@viewIncentive');
    Route::get('/route-operation/get-by-fleet/{id}', 'RouteOperationController@getByFleetId'); 
    Route::get('/actualmeter/{id}', 'RouteOperationController@actualmeter');
    Route::post('/updateactualmeter/{id}', 'RouteOperationController@updateactualmeter')->name('updateactualmeter');
    Route::get('/get-contact-ledger','RouteOperationController@fetchLedger');
    
    Route::get('/get-contact-ledger-summary','RouteOperationController@fetchLedgerSummarised');
    
    Route::get('/milage-changes','RouteOperationController@milage_changes');
    
    Route::get('/get-ro-advance/{id}','RouteOperationController@getRO_Advance');
    
    Route::get('/get-ro-payment/{id}','RouteOperationController@getPayments');
    
    Route::get('/create-fleet-invoices','RouteOperationController@index_create');
    
    Route::get('/fleet-profit-loss','RouteOperationController@fleet_profit_loss');
    Route::get('/fetch-profit-loss-summary','RouteOperationController@fetch_profit_loss_summary');
    
    Route::get('/get-advance/{id}','RouteOperationController@RO_Advance');
    Route::post('/post-advance','RouteOperationController@postRO_Advance');
    Route::post('/insert-invoice','RouteOperationController@insert_fleetInvoice');
    Route::get('/list-invoice','RouteOperationController@list_invoices');
    Route::get('/list-invoices-nos/{id}','RouteOperationController@list_invoices_numbers');
    Route::get('/print-invoice/{id}','RouteOperationController@printInvoice');
    
    Route::get('/get-ro-salary/{id}','RouteOperationController@getSal_Advance');
    Route::get('/get-salary/{id}','RouteOperationController@RO_Salary');
    Route::get('/add-expense/{id}','RouteOperationController@addExpense');
    
    Route::get('/view-expense/{id}','RouteOperationController@viewExpense');
    
    Route::post('/post-salary','RouteOperationController@postSal_Advance');
    
     Route::post('/post-expense','RouteOperationController@storeExpense');
    
    Route::resource('/route-operation', 'RouteOperationController');
    Route::resource('/income', 'IncomeController');
    Route::resource('/route-invoice-number', 'RouteInvoiceNumberController');
    Route::resource('/route-products', 'RouteProductController');
    Route::resource('/original-locations', 'OriginalLocationsController');
     Route::get('/settings/vehicle_category/create', function () {
        return view('fleet::settings.vehicle_category.create');
    })->name('vehicle_category.create');
    Route::post('/get-designation-by-department-id', 'HelperController@getDesignationByDepartmentId');
});
