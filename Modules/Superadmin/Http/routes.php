<?php

Route::group([ 'prefix' => 'superadmin', 'namespace' => 'Modules\Superadmin\Http\Controllers'], function () {
    Route::get('/notify-expired', 'BusinessController@notifyExpired');
    Route::get('/update-permissions', 'BusinessController@updatePerms');
});

Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData', 'DayEnd','tenant.context'], 'prefix' => 'superadmin', 'namespace' => 'Modules\Superadmin\Http\Controllers'], function () {
    
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
    
    Route::resource('default-notification-templates', 'DefaultNotificationTemplateController')->only(['index', 'store']);
    Route::resource('tax-rate', 'DefaultTaxRateController');
    Route::resource('group-tax', 'DefaultGroupTaxController');
    
    Route::resource('sms-reminder-settings', 'SmsReminderSettingController');
    
    
    Route::resource('sms-api-clients', 'SmsApiClientController');
    Route::resource('map-business-tanks', 'MapBusinessTankController');
    
    Route::resource('smsrefill-package', 'SmsRefillPackageController');
    
    Route::get('sms-summary', 'RefillBusinessController@businessSMSSummary');
    Route::get('get-history', 'RefillBusinessController@smsHistory');
    Route::resource('refill-business', 'RefillBusinessController');
    
    Route::get('/install', 'InstallController@index');
    Route::get('/install/update', 'InstallController@update');

    Route::get('/', 'SuperadminController@index');
    Route::get('/stats', 'SuperadminController@stats');

    Route::get('/{business_id}/toggle-active/{is_active}', 'BusinessController@toggleActive');
    Route::get('/business/back-to-superadmin', 'BusinessController@backToSuperadmin');
    Route::get('/business/login-as-business/{id}', 'BusinessController@loginAsBusiness');
    Route::get('/business/manage/{id}', 'BusinessController@manage');
    Route::post('/business/save-manage/{id}', 'BusinessController@saveManage');
    Route::resource('/business', 'BusinessController');
    Route::get('/business/{id}/destroy', 'BusinessController@destroy');
    Route::post('/account-entry/massdestroy', 'EditAccountEntriesController@massDestroy');
    Route::any('/business', ['as' => 'filter.business', 'uses' => 'BusinessController@index']);
    Route::any('/business/admin_register', ['as' => 'admin.business_register', 'uses' => 'BusinessController@store']);
    Route::any('/business/hospital_register', ['as' => 'hospital_register', 'uses' => 'BusinessController@hospital_register']);
    Route::any('/business/pharmacy_register', ['as' => 'pharmacy_register', 'uses' => 'BusinessController@pharmacy_register']);
    Route::any('/business/laboratory_register', ['as' => 'laboratory_register', 'uses' => 'BusinessController@laboratory_register']);

    Route::get('/packages/get_option_variables', 'PackagesController@getOptionVariables'); //for normal pacakge
    Route::resource('/packages', 'PackagesController');
    
    Route::resource('/account-numbers', 'AccountNumbersController');
    
    Route::get('/packages/{id}/destroy', 'PackagesController@destroy');

    Route::get('/settings', 'SuperadminSettingsController@edit')->name('settings');
    Route::get('/settings/product-categories', 'SuperadminSettingsController@getProductCategories');
    Route::get('/settings/add-product-category', 'SuperadminSettingsController@addProductCategory');
    Route::post('/settings/store-product-category', 'SuperadminSettingsController@storeProductCategory');
    Route::get('/settings/expense-categories', 'SuperadminSettingsController@getExpenseCategories');
    Route::get('/settings/add-expense-category', 'SuperadminSettingsController@addExpenseCategory');
    Route::post('/settings/store-expense-category', 'SuperadminSettingsController@storeExpenseCategory');
    // landing pages
    Route::get('/pages', 'SuperadminSettingsController@pages')->name('pages');
    
    Route::get('/landing-languages', 'SuperadminSettingsController@landing_languages')->name('landing-languages');
    
    Route::get('/landing-settings', 'SuperadminSettingsController@landAdminSettings')->name('landing-settings');
    Route::post('/landing-settings',  'SuperadminSettingsController@changeSettings')->name('landing-settings');
    
    Route::get('/edit-page/{id}', 'SuperadminSettingsController@editPage')->name('edit-page');
    Route::post('/save-page/{id}', 'SuperadminSettingsController@savePage')->name('save-page');
    
    Route::get('/landing-pages', 'SuperadminSettingsController@landingSettings')->name('landing-pages');
    Route::post('/landing-pages', 'SuperadminSettingsController@savelandingSettings')->name('landing-pages');
    
    
    Route::put('/settings', 'SuperadminSettingsController@update');
    
    Route::post('add-ad', 'SuperadminSettingsController@saveAd')->name('add.ad');    
    Route::get('edit-ad/{id}', 'SuperadminSettingsController@editAd')->name('edit.ad');
    Route::post('update-ad', 'SuperadminSettingsController@updateAd')->name('update.ad');
    Route::get('delete-ad', 'SuperadminSettingsController@deleteAd')->name('delete.ad');
    Route::post('/get-ad-slots-data', 'SuperadminSettingsController@getAdPageSlot')->name('ad.get-ad-slots-data');

    #ad slot
    Route::post('add-ad-slot', 'SuperadminSettingsController@saveAdSlot')->name('add.adslot');    


    Route::get('/edit-subscription/{id}', 'SuperadminSubscriptionsController@editSubscription');
    Route::post('/update-subscription', 'SuperadminSubscriptionsController@updateSubscription');
    Route::resource('/superadmin-subscription', 'SuperadminSubscriptionsController');
    Route::get('/get-option-variables/{id}/{business_id}', 'CompanyPackageVariableController@getOptionVariables'); //only for comapny pacakge eg manage
    Route::resource('/company-package-variables', 'CompanyPackageVariableController');
    Route::resource('/package-variables', 'PackageVariableController');

    Route::get('/communicator', 'CommunicatorController@index');
    Route::post('/communicator/send', 'CommunicatorController@send');
    Route::get('/communicator/get-history', 'CommunicatorController@getHistory');

    Route::resource('/frontend-pages', 'PageController');
    Route::resource('/tenant-management', 'TenantManagementController');
    Route::resource('/help-explanation', 'HelpExplanationController');
    Route::get('/default-manage-users/get-business-data', 'DefaultManageUserController@getBusinessData');
    Route::resource('/default-manage-users', 'DefaultManageUserController');
    Route::resource('/default-role', 'DefaultRoleController');
    Route::get('/tank-dip-chart/import', 'TankDipChartController@getImport');
    Route::post('/tank-dip-chart/import', 'TankDipChartController@postImport');
    Route::get('/tank-dip-chart-details/get-reading-value/{id}', 'TankDipChartController@getDipReadingValue');
    Route::get('/tank-dip-chart/get-by-id/{id}', 'TankDipChartController@getTankDipById');
    Route::resource('/tank-dip-chart', 'TankDipChartController');
    Route::resource('/referrals', 'ReferralController');
    Route::resource('/referral-starting-code', 'ReferralStartingCodeController');
    Route::resource('/give-away-gifts', 'GiveAwayGiftsController');

    Route::any('family-subscription/pay', 'FamilySubscriptionController@pay');
    Route::post('family-subscription/confirm', 'FamilySubscriptionController@confirm');
    Route::get('family-subscription/get-option-variable', 'FamilySubscriptionController@getOptionVariables');
    Route::get('/family-subscription/patient', 'FamilySubscriptionController@getPatientSubscriptions');
    Route::resource('/family-subscription', 'FamilySubscriptionController');
    
    Route::get('/get-family-subscription', 'FamilySubscriptionController@getFamilyPackages');
    
    Route::post('/import-file', 'ImportExportController@importFile');
    Route::get('/export-file', 'ImportExportController@exportFile');
    Route::resource('/imports-exports', 'ImportExportController');
    Route::get('/edit-account-transaction/{transaction_id}/{business_id}', 'EditAccountEntriesController@editAccountTransaction');
    Route::get('/get-account-drop-down-by-business/{buisness_id}', 'EditAccountEntriesController@getAccountDropdownByBusiness');
    Route::get('/list-edit-account-entries', 'EditAccountEntriesController@listEditAccountTransaction');
    Route::resource('/edit-account-entries', 'EditAccountEntriesController');
    Route::get('/edit-contact-transaction/get-ledger', 'EditContactEntriesController@getLedger');
    Route::get('/edit-contact-transaction/{transaction_id}/{business_id}', 'EditContactEntriesController@editContactTransaction');
    Route::get('/get-contact-drop-down-by-business/{buisness_id}/{type}', 'EditContactEntriesController@getContactDropdownByBusiness');
    Route::get('/list-edit-contact-entries', 'EditContactEntriesController@listEditContactTransaction');
    Route::resource('/edit-contact-entries', 'EditContactEntriesController');
    Route::resource('/agents', 'AgentController');
    Route::resource('/referral-group', 'ReferralGroupController');
    Route::resource('/income-method', 'IncomeMethodController');
    Route::get('/petro-quota-setting', '\Modules\Petro\Http\Controllers\VehicleController@petro_qouta_setting');
    Route::post('/petro-quota-setting', '\Modules\Petro\Http\Controllers\VehicleController@petro_qouta_setting');
    Route::delete('/petro-quota-setting/{type}/{id}', '\Modules\Petro\Http\Controllers\VehicleController@petro_qouta_setting_destory');

    Route::get('/petro-qouta-setting-ajax', '\Modules\Petro\Http\Controllers\VehicleController@petro_qouta_setting_ajax');
    Route::post('/petro-qouta-setting-ajax', '\Modules\Petro\Http\Controllers\VehicleController@petro_qouta_setting_ajax');

    
    Route::get('/user-locations', '\Modules\Superadmin\Http\Controllers\UserLocationsController@index')->name('userlocations.index');



});
Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData', 'DayEnd','tenant.context'], 'namespace' => 'Modules\Superadmin\Http\Controllers'], function () {
    Route::post('/family-subscription/notify-payhere', 'FamilySubscriptionController@notifyPayhere');
    Route::post('/pay-online/payhere-notify', 'PayOnlineController@notifyPayhere');
    Route::post('/subscription/payhere/confirm', 'SubscriptionController@payhereNotify')->name('subscription-payhere-confirm');
    Route::get('/subscription/{package_id}/get_package_variables', 'SubscriptionController@getPackageVariables');
});
Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData', 'DayEnd','tenant.context'],'namespace' => 'Modules\Superadmin\Http\Controllers'], function () {
    //Routes related to paypal checkout
    Route::get(
        '/subscription/{package_id}/paypal-express-checkout',
        'SubscriptionController@paypalExpressCheckout'
    );

    Route::post('/pay-online/initiate-payhere', 'PayOnlineController@initiatePayhere');
    Route::resource('/pay-online', 'PayOnlineController');

    //Routes related to pesapal checkout
    Route::get('/subscription/{package_id}/pesapal-callback', ['as' => 'pesapalCallback', 'uses' => 'SubscriptionController@pesapalCallback']);


    Route::any('/subscription/{package_id}/pay', 'SubscriptionController@pay');
    Route::any('/subscription/{package_id}/{gateway}/check-status', 'SubscriptionController@checkStatus');
    Route::any('/subscription/{package_id}/confirm', 'SubscriptionController@confirm')->name('subscription-confirm');
    Route::post('/subscription/payhere/payhereInitailData', 'SubscriptionController@payhereInitailData')->name('subscription-payhere-initaildata');
    Route::get('/all-subscriptions', 'SubscriptionController@allSubscriptions');

    Route::get('/subscription/{package_id}/register-pay', 'SubscriptionController@registerPay')->name('register-pay');

    Route::resource('/subscription', 'SubscriptionController');
});

Route::middleware('tenant.context')->group(function () {
    Route::get('/pricing', 'Modules\Superadmin\Http\Controllers\PricingController@index')->name('pricing')->middleware('web');
    Route::get('/page/{slug}', 'Modules\Superadmin\Http\Controllers\PageController@showPage')->name('frontend-pages');
});


