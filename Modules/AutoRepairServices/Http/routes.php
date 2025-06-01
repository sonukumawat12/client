<?php
Route::middleware('tenant.context')->group(function () {
    Route::get('/repair-status', 'Modules\AutoRepairServices\Http\Controllers\CustomerRepairStatusController@index')->name('repair-status');
    Route::post('/post-repair-status', 'Modules\AutoRepairServices\Http\Controllers\CustomerRepairStatusController@postRepairStatus')->name('post-repair-status');
});

Route::group([
    'middleware' => ['web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone','tenant.context'],
    'prefix' => 'autorepairservices', 'namespace' => 'Modules\AutoRepairServices\Http\Controllers'],
    function () {
    Route::get('edit-repair/{id}/status', 'RepairController@editRepairStatus');
    Route::post('update-repair-status', 'RepairController@updateRepairStatus');
    Route::get('delete-media/{id}', 'RepairController@deleteMedia');
    Route::get('print-label/{id}', 'RepairController@printLabel');
    Route::get('print-repair/{transaction_id}/customer-copy', 'RepairController@printCustomerCopy')->name('repair.customerCopy');
    Route::resource('/repair', 'RepairController')->except(['create', 'edit']);
    Route::resource('/status', 'RepairStatusController', ['except' => ['show']]);
    
    Route::resource('/repair-settings', 'RepairSettingsController', ['only' => ['index', 'store']]);
    
    Route::get('/install', 'InstallController@index');
    Route::post('/install', 'InstallController@install');
    Route::get('/install/uninstall', 'InstallController@uninstall');
    Route::get('/install/update', 'InstallController@update');

    Route::get('get-device-models', 'DeviceModelController@getDeviceModels');
    Route::post('device_category', 'DeviceModelController@store_device')->name('device_category');
    Route::get('device_category_modal', 'DeviceModelController@create_device')->name('device_category_modal');
    Route::get('sales-report-detail', 'ServiceReportController@selesDetailreport');
    Route::get('sales-report-detail-daily', 'ServiceReportController@selesDetailreportdaily');

    Route::get('models-repair-checklist', 'DeviceModelController@getRepairChecklists');
    Route::resource('device-models', 'DeviceModelController')->except(['show']);
    Route::resource('dashboard', 'DashboardController');

    Route::post('job-sheet-post-upload-docs', 'JobSheetController@postUploadDocs');
    Route::get('job-sheet/{id}/upload-docs', 'JobSheetController@getUploadDocs');
    Route::get('job-sheet/print/{id}', 'JobSheetController@print');
    Route::get('job-sheet/delete/{id}/image', 'JobSheetController@deleteJobSheetImage');
    Route::get('job-sheet/{id}/status', 'JobSheetController@editStatus');
    Route::put('job-sheet-update/{id}/status', 'JobSheetController@updateStatus');
    Route::get('job-sheet/add-parts/{id}', 'JobSheetController@addParts');
    Route::post('job-sheet/save-parts/{id}', 'JobSheetController@saveParts');
    Route::post('job-sheet/get-part-row', 'JobSheetController@jobsheetPartRow');
    Route::resource('job-sheet', 'JobSheetController');
    
    
    Route::resource('brands', 'AutorepairBrandController');
    Route::resource('service-report', 'ServiceReportController');
    Route::get('servicereport/service-report-print', 'ServiceReportController@print');
    // Route::get('brands/destroy/{id}', 'AutorepairBrandController@destroy');
    
    Route::resource('vehicle-brand', 'VehicleBrandController');

    Route::get('vehicleDetails/{id}','JobSheetController@getVehicleData');
    Route::get('customerData/{id}','JobSheetController@getCustomerData');
});

Route::group(['middleware' => ['web', 'authh', 'auth', 'SetSessionData', 'language', 'timezone','tenant.context']], function () {
    Route::get('/vehicle-brand/table', 'Modules\AutoRepairServices\Http\Controllers\VehicleBrandController@table');
    Route::get('/vehicle-brand/saveData', 'Modules\AutoRepairServices\Http\Controllers\VehicleBrandController@saveData');
    Route::get('/vehicle-brand/deleteData', 'Modules\AutoRepairServices\Http\Controllers\VehicleBrandController@deleteData');
    Route::get('/vehicle-brand/populateData', 'Modules\AutoRepairServices\Http\Controllers\VehicleBrandController@populateData');
});

