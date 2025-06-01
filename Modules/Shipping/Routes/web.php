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

Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData', 'tenant.context'], 'prefix' => 'shipping'], function () {
    Route::get('/', 'ShippingController@index');
    
    Route::post('/add-agent', 'ShippingController@addAgent');
    Route::post('/add-partner', 'ShippingController@addPartner');
    
    
    Route::post('/customer-details', 'AddShipmentController@customer_details');  
    
    Route::get('/add-dimensions/{id}', 'AddShipmentController@updatePackages');
    Route::post('/add-dimensions/{id}', 'AddShipmentController@postupdatePackages');
    
    Route::post('/recipient-details', 'AddShipmentController@recipient_details');
    Route::post('/get-cost-perkg', 'AddShipmentController@getRatePerKg');
    Route::post('/send-whatsapp-invoice', 'ShippingController@sendWhatsappInvoice');
    Route::post('/save-shipping-details', 'ShippingController@saveShippingDetails');
    
    Route::get('/add-agent-commission/{id}', 'ShippingController@addAgentCommission');
    Route::post('/add-agent-commission/{id}', 'ShippingController@storeAgentCommission');
    
    Route::get('/add-partner-commission/{id}', 'ShippingController@addPartnerCommission');
    Route::post('/add-partner-commission/{id}', 'ShippingController@storePartnerCommission');
    
    Route::post('/get-shipping-change-details', 'ShippingController@getShippingChangeDetails');
    Route::get('/scan-code', 'BarQrCodeController@scanCode');
    Route::post('/create-sipment-code', 'BarQrCodeController@createShipmentCode');
    Route::post('/send-emailids-invoice', 'ShippingController@sendEmailIdsInvoice');
    Route::get('/get-bar-code', 'AddShipmentController@createShipmentBarCode');
    //Route::get('/shipping/get-bar-code/{trackingNo}', 'AddShipmentController@createShipmentBarCode')->name('shipment.createBarcode');


    Route::resource('/drivers', 'DriverController');
    Route::resource('/shipping', 'ShippingController');
    
    Route::resource('/collection-officers', 'CollectionOfficerController');
    Route::resource('/types', 'TypeController');
    Route::resource('/status', 'StatusController');
    Route::resource('/mode', 'ModeController');
    Route::resource('/delivery', 'DeliveryController');
    Route::resource('/delivery-days', 'DeliveryDaysController');
    Route::resource('/prefix', 'PrefixController');
    Route::resource('/price', 'PriceController');
    Route::resource('/package', 'PackageController');
    Route::resource('/barqrcode', 'BarQrCodeController');
    Route::resource('/credit-days', 'CreditDaysController');
    Route::resource('/shipping-accounts', 'ShippingAccountController');
    Route::resource('/dimensions', 'DimensionController');
    Route::resource('/settings', 'SettingController');
    
    Route::resource('/add-shipment', 'AddShipmentController');
    Route::resource('add-shipment-sw', 'AddShipmentSWController');
    //add by sakhawat
    Route::get('shipment-terms-condition', 'AddShipmentSWController@printTermCondition')->name('print_terms_condition');
   
    Route::get('/recipients/shipping-details/{id}', 'RecipientController@shippingDetails');
    Route::get('/recipients/shipment-details/{id}', 'RecipientController@shipmentDetails');
    
    Route::get('/agents/shipping-details/{id}', 'AgentController@shippingDetails');
    Route::get('/agents/shipment-details/{id}', 'AgentController@shipmentDetails');
    Route::get('/agents/add-commission/{id}', 'AgentController@addCommission');
    Route::post('/agents/add-commission/{id}', 'AgentController@storeCommission');
    Route::get('/agents/one-shipment-details/{id}', 'AgentController@oneShipmentDetails');
    
    Route::get('/agents/view-commisions/{id}', 'AgentController@viewCommissions');
    Route::get('/agents/view-commisions-table/{id}', 'AgentController@viewCommissionsTable');
    
    Route::get('/partners/view-commisions/{id}', 'PartnerController@viewCommissions');
    Route::get('/partners/view-commisions-table/{id}', 'PartnerController@viewCommissionsTable');
    
    Route::get('/agents/add-payment/{id}', 'AgentController@addPayment');
    Route::post('/agents/add-payment/{id}', 'AgentController@postPayment');
    
    Route::get('/agents/get-ledger/{id}', 'AgentController@viewLedger');
    Route::get('/partners/get-ledger/{id}', 'PartnerController@viewLedger');
    
    Route::get('/partners/add-payment/{id}', 'PartnerController@addPayment');
    Route::post('/partners/add-payment/{id}', 'PartnerController@postPayment');
    
    Route::resource('/agents', 'AgentController');
    Route::resource('/recipients', 'RecipientController');
    Route::resource('/partners', 'PartnerController');
    
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

    Route::get('/printreceipt', 'AddShipmentController@printreceipt');
});
