<?php

Route::group(['middleware' => ['web','tenant.context'], 'prefix' => 'leads', 'namespace' => 'Modules\Leads\Http\Controllers'], function()
{
    Route::post('/leads/toggle-valid/{id}', 'LeadsController@toggleStatus');
    Route::post('/leads/bulk-valid', 'LeadsController@massValid');
    Route::post('/leads/bulk-invalid', 'LeadsController@massInvalid');
    Route::get('/leads/add-client-response/{id}', 'LeadsController@addClientResponse');
    Route::post('/leads/add-client-response', 'LeadsController@clientResponse');
    Route::resource('/leads', 'LeadsController');
    Route::resource('/import', 'ImportLeadsController');
    Route::resource('/day-count', 'DayCountController');
    Route::resource('/district', 'DistrictController');
    Route::resource('/town', 'TownController');
    Route::resource('/settings', 'SettingController');
    Route::resource('/category', 'CategoryController');
    Route::resource('/labels', 'LabelController');

    Route::post('/ajax_mobile', 'AjaxController@ajax_mobile')->name('ajax_mobile');
    Route::post('/ajax_town', 'AjaxController@ajax_town')->name('ajax_town');
    Route::post('/ajax_district', 'AjaxController@ajax_district')->name('ajax_district');
});
