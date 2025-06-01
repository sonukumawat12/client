<?php

Route::group(['middleware' => ['web','auth','tenant.context'], 'prefix' => 'pricechanges', 'namespace' => 'Modules\PriceChanges\Http\Controllers'], function()
{
    Route::get('/get-last-verified-form-f22', 'F22FormController@getLastVerifiedF22Form');
    Route::get('/get-form-f22-list', 'F22FormController@getF22FormList');
    Route::get('/get-form-f22', 'F22FormController@getF22Form');
    Route::post('/save-form-f22', 'F22FormController@saveF22Form');
    Route::get('/print-form-f22-by-id/{header_id}', 'F22FormController@printF22FormById');
    Route::put('/update-form-f22/{id}', 'F22FormController@update');
    Route::get('/edit-form-f22/{id}', 'F22FormController@edit');
    Route::post('/print-form-f22', 'F22FormController@printF22Form');
    Route::get('/F22_stock_taking', 'F22FormController@F22StockTaking');
    Route::get('/form-set-1', 'MPCSController@FromSet1')->middleware('auth');
    Route::get('/get-form-16a', 'MPCSController@get16AForm');
    Route::get('/get_previous_value_16a', 'MPCSController@getPreviousValue16AForm');
    Route::get('/F14', 'MPCSController@F14');
    Route::get('/get_previous_value_9c', 'MPCSController@getPreviousValue9CForm');
    Route::get('/get-9c-form', 'MPCSController@get9CForm');
    Route::get('/F15-9ABC', 'MPCSController@F159ABC');
    Route::get('/F21', 'MPCSController@F21');
    Route::get('/get_21_c_form_all_query', 'MPCSController@get_21_c_form_all_query');
    Route::get('/frm-21-query', 'MPCSController@Form21CQuery');
    //Route::get('/get_opening_stock_21_form', 'MPCSController@getOpeningStock21Form');

    //New addition 
    Route::post('/save-form-f17', 'F17FormController@store');



    Route::get('/get-form-14b', 'F20F14bFormController@getFrom14B');
    Route::get('/get-form-20', 'F20F14bFormController@getFrom20');
    Route::resource('/F14B_F20_Forms', 'F20F14bFormController');
    
    Route::get('/list-F17', 'F17FormController@list');
    Route::get('/get-unit', 'F17FormController@getUnit');
    Route::get('/get-product', 'F17FormController@getProduct');
    Route::resource('/', 'F17FormController');
    Route::post('/save-prices_change_settings', 'F17FormController@savePriceChangeSettings');
    Route::post('/edit-prices_change_settings/{id}', 'F17FormController@editPriceChangeSettings');
    Route::get('/edit-prices_change_settings_form/{id}', 'F17FormController@editForm');
    Route::get('/list-prices_change_settings', 'F17FormController@listPriceChangeSettings');
    Route::get('/details', 'F17FormController@details');

    Route::get('/form-opening-value/print/{id}', 'FormOpeningValueController@print');
    Route::resource('/form-opening-value', 'FormOpeningValueController');

    Route::post('/forms-setting/formf33', 'FormsSettingController@postFormF22Setting');
    Route::get('/forms-setting/formf33', 'FormsSettingController@getFormF22Setting');
    Route::post('/forms-setting/form21C', 'FormsSettingController@postForm21CSetting');
    Route::get('/forms-setting/form21C', 'FormsSettingController@getForm21CSetting');
    Route::post('/forms-setting/form159abc', 'FormsSettingController@saveForm159ABCSetting');
    Route::get('/forms-setting/form159abc', 'FormsSettingController@getForm159ABCSetting');
    Route::post('/forms-setting/form16a', 'FormsSettingController@postForm16ASetting');
    Route::get('/forms-setting/form16a', 'FormsSettingController@getForm16ASetting');
    Route::post('/forms-setting/form9c', 'FormsSettingController@postForm9CSetting');
    Route::get('/forms-setting/form9c', 'FormsSettingController@getForm9CSetting');
    Route::resource('/forms-setting', 'FormsSettingController')->middleware('auth');
    
    Route::post('/products/get_sub_categories', 'ProductController@getSubCategories');
    Route::get('/{id}', 'F17FormController@show');
    Route::get('/{id}/edit', 'F17FormController@edit');




});
