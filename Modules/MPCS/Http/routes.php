<?php

Route::group(['middleware' => ['web', 'tenant.context'], 'prefix' => 'mpcs', 'namespace' => 'Modules\MPCS\Http\Controllers'], function () {
  
    Route::get('/get-last-verified-form-f22', 'F22FormController@getLastVerifiedF22Form');
    Route::get('/get-form-f22-list', 'F22FormController@getF22FormList');
    Route::get('/get-form-f22-list_gain_loss', 'F22FormController@getF22FormListGainLoss');
    Route::get('/check-user-existence', 'F22FormController@checkUserExistence');
    Route::get('/fetch-pumps', 'F22FormController@fetchPumps');
    Route::post('/get_link_account_state', 'F22FormController@getLinkaccountstate');
  Route::get('/get-form-f16-list', 'F16AFormController@getF16FormList');
  Route::get('/get-form-f22', 'F22FormController@getF22Form');
  Route::post('/save-form-f22', 'F22FormController@saveF22Form');
  Route::post('/edit-form-16', 'F16AFormController@updateF16Form');
  Route::post('/edit-form-16/{id}', 'F16AFormController@save');
  Route::get('/print-form-f22-by-id/{header_id}', 'F22FormController@printF22FormById');
  Route::put('/update-form-f22/{id}', 'F22FormController@update');
  Route::get('/edit-form-f22/{id}', 'F22FormController@edit');
  Route::get('/view-form-16/{id}', 'F16AFormController@view');
  Route::get('/edit-form-16/{id}', 'F16AFormController@edit');
  Route::post('/print-form-f22', 'F22FormController@printF22Form');
Route::post('/save_f22_stock_taking', 'F22FormController@store_stock_taking');
  Route::get('/print-form-f16', 'F16AFormController@print');
  Route::get('/F22_stock_taking', 'F22FormController@F22StockTaking');
  Route::get('/form-set-1', 'MPCSController@FromSet1')->middleware('auth');
  Route::get('/form-9a', 'MPCSController@From9A')->middleware('auth');
  
  Route::get('/form-9c', 'MPCSController@From9C');//->middleware('auth');
  Route::get('/form-9ccr', 'MPCSController@From9CCR');//->middleware('auth');
  Route::get('/mpcs/F14B', 'F14FormController@index');
  
  Route::get('/form9c-settings/create', 'Form9CSettingsController@create')->name('form9c-settings.create');
  Route::post('/form9c-settings/store', 'Form9CSettingsController@store');
  Route::post('/form9c-settings_update/{id}', 'Form9CSettingsController@update');
  Route::get('/edit-form-9c-settings/{id}', 'Form9CSettingsController@edit');
  Route::get('/get-form-9c-settings', 'Form9CSettingsController@index');
  
  Route::get('/get-form-9ccr-settings', 'Form9CCRSettingsController@index');
  Route::get('/form9ccr-settings/create', 'Form9CCRSettingsController@create');
  Route::post('/form9ccr-settings/store', 'Form9CCRSettingsController@store');
  Route::post('/form9ccr-settings_update/{id}', 'Form9CCRSettingsController@update');
  Route::get('/edit-form-9ccr-settings/{id}', 'Form9CCRSettingsController@edit');
  
  Route::get('/get-form-9a-settings', 'Form9ASettingsController@index');
  Route::get('/get-9a-form', 'Form9ASettingsController@get9AForm');

  Route::get('/edit-form-settings/{id}', 'Form9ASettingsController@edit');
  Route::get('/get-form-16a', 'MPCSController@get16AForm');
  Route::get('/get_previous_value_16a', 'MPCSController@getPreviousValue16AForm');
  Route::get('/F14', 'MPCSController@F14');
  Route::get('/get_previous_value_9c', 'MPCSController@getPreviousValue9CForm');
  Route::get('/get-9c-form', 'MPCSController@get9CForm');
  Route::get('/get-9a-form_value', 'MPCSController@get9AForm');
  Route::get('/get-9ccash-form', 'MPCSController@get9CCashForm');
  Route::get('/get-9ccredit-form', 'MPCSController@get9CCreditForm');
  Route::get('/F15-9ABC', 'MPCSController@F159ABC');
  
  Route::get('/F21', 'F21CFormController@index');
  Route::get('/form9ccash', 'Form9CCashController@index');
  Route::post('/popup-form', 'Form9CCashController@store');

    //By Zamaluddin : Time 04:20 PM : 28 January 2025
  Route::get('/F15', 'F15FormController@index');
  Route::get('/f15/header/get-form-f15-data', 'F15FormController@getFormF15Data'); 
   
  Route::get('/get-15-form-setting', 'F15FormController@get15FormSetting');
  Route::get('/15formsettings', 'F15FormController@mpcs15FormSettings');
  Route::post('/store-15-form-setting', 'F15FormController@store15FormSetting');
  Route::get('/edit-15-form-settings/{id}', 'F15FormController@edit15FormSetting');
//   30 January 2025
  Route::get('/delete-15-form-settings/{id}', 'F15FormController@delete15FormSetting');
//   End
  Route::post('/update-15-form-settings/{id}', 'F15FormController@mpcs15Update');
    //   End
  
 
  Route::get('/get_21_c_form_all_query', 'F21CFormController@get_21_c_form_all_query');
  Route::get('/get-21c-form', 'MPCSController@get21CForm');
  Route::get('/get-9c-forms', 'MPCSController@get21CForms');
  //Route::get('/get_opening_stock_21_form', 'MPCSController@getOpeningStock21Form');
  Route::post('/save-form-f16', 'F16AFormController@saveF16Form');
  Route::get('/get-form-14b', 'F20F14bFormController@getFrom14B');
  Route::get('/get-form-20', 'F20F14bFormController@getFrom20');
  Route::resource('/F14B_F20_Forms', 'F20F14bFormController');
  
  Route::get('/mpcs/F14B', 'NewF14FormController@index');
  Route::get('/mpcs/get-form-14', 'NewF14FormController@getForm14');

  Route::get('/list-F17', 'F17FormController@list');
  Route::resource('/F17', 'F17FormController');

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
  Route::get('/16A', 'F16AFormController@index');


  Route::get('/get-16a-form-setting', 'FormsSettingController@get16AFormSetting');
  Route::get('/16aformsettings', 'FormsSettingController@mpcs16aFormSettings');
  Route::post('/store-16a-form-setting', 'FormsSettingController@store16aFormSetting');
  Route::get('/edit-16-a-form-settings/{id}', 'FormsSettingController@edit16aFormSetting');
  Route::post('/update-16a-form-settings/{id}', 'FormsSettingController@mpcs16Update');

  Route::get('/21CForm', 'F21FormController@index');
  Route::get('/get-21c-form-setting', 'F21FormController@get21CFormSettings');
  Route::get('/get-subcategory-pump/{id}', 'F21FormController@getSubcategoryPumps');

  Route::post('/add-newpump-row', 'F21FormController@addNewPumpRow');
  Route::post('/store-21c-form-setting', 'F21FormController@store21cFormSettings');
  Route::get('/21cformsettings', 'F21FormController@mpcs21cFormSettings');
  Route::get('/edit-21-c-form-settings/{id}', 'F21FormController@edit21cFormSetting');
  Route::post('/update-21c-form-settings/{id}', 'F21FormController@mpcs21Update');


  Route::get('/21Form', 'F21FormController@get21Form');
  Route::get('/getpos', 'F21FormController@getPos');
  Route::get('/get-purchase-order', 'F21FormController@getPurchaseOrder');
  Route::get('/get-sales-return', 'F21FormController@getSellReturn');
  Route::get('/get-purchase-return', 'F21FormController@getPurchaseReturn');
  Route::get('/get-settlement', 'F21FormController@getSettlement');

  Route::get('/20Form', 'F20FormController@index');
  Route::get('/get-20-form-settings', 'F20FormController@get20FormSettings');
  Route::post('/store-20-form-setting', 'F20FormController@store20FormSettings');
  Route::get('/20formsettings', 'F20FormController@mpcs20FormSettings');
  Route::get('/edit-20-form-settings/{id}', 'F20FormController@edit20FormSetting');
  Route::post('/update-20-form-settings/{id}', 'F20FormController@mpcs20Update');
  Route::get('/get-form-20-data', 'F20FormController@getFrom20Data');
  Route::get('/test-connection', 'F20FormController@testConnection');
  Route::get('/get-form-20-datas', 'F20FormController@getForm20Data');
//   Route::get('/mpcs/fetch-form-number', 'F20FormController@fetchFormNumber');
//   Route::get('/mpcs/fetch-form-number', [F20FormController::class, 'fetchFormNumber'])->name('mpcs.fetch-form-number');
     Route::get('/fetch-form-number', 'F20FormController@fetchFormNumber');

});