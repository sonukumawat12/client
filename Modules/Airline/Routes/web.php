<?php



use Modules\Airline\Http\Controllers\AirlineAgentController;



/*

|--------------------------------------------------------------------------

| Web Routes

|--------------------------------------------------------------------------

|

| Here is where you can register web routes for your application. These

| routes are loaded by the RouteServiceProvider within a group whichcreate_invoice

| contains the "web" middleware group. Now create something great!

|

*/



Route::group(['middleware' => ['web', 'auth', 'tenant.context'], 'prefix' => 'airline'], function () {

    

    Route::get('get-countries', 'AirlineTicketingController@fetchCountries');

    

    Route::resource('ticketing', 'AirlineTicketingController');

    //add by sakhawat

    Route::resource('agents', 'AirlineAgentController');

    

    Route::get('/agents/get-ledger/{id}', 'AirlineAgentController@viewLedger');

    



    Route::get('/agents/shipping-details/{id}', 'AgentController@shippingDetails');

    Route::get('/agents/shipment-details/{id}', 'AgentController@shipmentDetails');

    Route::get('/agents/add-commission/{id}', 'AgentController@addCommission');

    Route::post('/agents/add-commission/{id}', 'AgentController@storeCommission');

    Route::get('/agents/one-shipment-details/{id}', 'AgentController@oneShipmentDetails');

    

    Route::get('/agents/view-commisions/{id}', 'AgentController@viewCommissions');

    Route::get('/agents/view-commisions-table/{id}', 'AgentController@viewCommissionsTable');

    

    //End Agent Module 

    Route::resource('airline_settings', 'AirlineSettingController');

    Route::get('ticketing/get-transit/{transaction_id}', 'AirlineTicketingController@get_transit');

//     Route::post('add_prefix_starting_no', 'AirlineSettingController@store_prefix');

    // Route::post('add_airline', 'AirlineSettingController@store_airline');

    

    Route::post('add_multiple_prefix_starting_no', 'AirlineSettingController@store_multiple_prefix');

    Route::patch('edit_prefix_starting_no', 'AirlineSettingController@edit_prefix');

    Route::delete('delete_prefix_starting_no', 'AirlineSettingController@delete_prefix');

    Route::patch('update_status_prefix_starting_no', 'AirlineSettingController@update_status_prefix');

    Route::get('get_airport_table', 'AirlineSettingController@get_airport_table');

    



    Route::post('add_airport', 'AirlineSettingController@store_airport');

    Route::post('edit_airport', 'AirlineSettingController@edit_airport');

    

    Route::delete('delete_airport', 'AirlineSettingController@delete_airport');

    Route::patch('update_status_airport', 'AirlineSettingController@update_status_airport');

    

    Route::post('add_airline', 'AirlineSettingController@store_airline')->name('add_airline');

    Route::patch('edit_airline', 'AirlineSettingController@edit_airline')->name('edit_airline');

    Route::delete('delete_airline', 'AirlineSettingController@delete_airline')->name('delete_airline');



    Route::post('add_agent', 'AirlineSettingController@store_agent')->name('add_airline_agent');

    Route::patch('edit_agent', 'AirlineSettingController@edit_agent')->name('edit_airline_agent');

    Route::delete('delete_agent', 'AirlineSettingController@delete_agent')->name('delete_airline_agent');


    Route::get('location', 'AirlineSettingController@getLocationsContacts')->name('location.contacts');
    Route::post('location', 'AirlineSettingController@storeContactsSettings')->name('store.contacts.settings');
    Route::get('check_location_exist', 'AirlineSettingController@checkLocationExist')->name('check.contacts.settings.location');

    

    Route::get('create_invoice', 'AirlineTicketingController@create_invoice');
  
    Route::post('create_invoice/invoice/print', 'AirlineTicketingController@print_invoice');
    // Route::post('create_invoice/invoice/printWithoutSupplierPayment', 'AirlineTicketingController@print_invoice_without_supplier');
    Route::post('/email/invoice', 'AirlineTicketingController@sendEmailInvoicePDF');
    Route::post('/whatsapp/invoice', 'AirlineTicketingController@sendWhatsappInvoicePDF');

    Route::get('create', 'AirlineTicketingController@add_commission');

    Route::get('list_commision', 'AirlineTicketingController@list_commission');

    Route::post('Add_commission_filter', 'AirlineTicketingController@Add_commission_filter');

    Route::get('get_incremental_prefix_value_by_prefix_starting_id/{id}','AirlineTicketingController@get_incremental_prefix_value_by_prefix_starting_id');

    Route::get('get_wallet_amount', 'AirlineTicketingController@get_wallet');

    Route::get('create_passenger', 'AirlineTicketingController@create_passenger');

    Route::get('create_commission', 'AirlineTicketingController@create_commission');

    Route::post('store_passenger', 'AirlineTicketingController@store_passenger');

    Route::get('add_commision_store', 'AirlineTicketingController@add_commision_store');

    Route::post('store_invoice', 'AirlineTicketingController@store_invoice');

    Route::get('create_payment', 'AirlineTicketingController@create_payment');

    Route::get('create_payment_supplier', 'AirlineTicketingController@create_payment_supplier');

    Route::get('airport_suppliers', 'AirlineTicketingController@airline_suppliers');

    Route::get('airport_commision_type', 'AirlineTicketingController@commision_store');

    

    Route::get('save_account', 'AirlineTicketingController@save_account');

    Route::get('airport_commision_type_get', 'AirlineTicketingController@commision_type_get');

     Route::get('getInvoiceno', 'AirlineTicketingController@getInvoiceno');

      Route::get('getInvoiceNumbers', 'AirlineTicketingController@getInvoiceNumbers');

        Route::get('get_airline_commission_print', 'AirlineTicketingController@get_airline_commission_print');

    Route::get('airport_linked_account_get', 'AirlineTicketingController@airport_linked_get');

    Route::get('get_commission_types', 'AirlineTicketingController@get_commission_types');


    // passenger type 

    Route::get('passenger_type_get', 'AirlineTicketingController@passenger_type_get');
    Route::post('passenger_type_store', 'AirlineTicketingController@passenger_type_store');

    Route::get('additional_service_get', 'AirlineTicketingController@additional_service_get');

    Route::get('locations', 'AirlineTicketingController@additional_service_get');

 
    // airline classes 

    Route::get('airline_classes_get', 'AirlineTicketingController@airline_classes_get');
    Route::post('airline_classes_store', 'AirlineTicketingController@airline_classes_store');

    

    Route::get('customers_by_group_id', 'AirlineTicketingController@customers_by_group_id');

    Route::get('customers-all', 'AirlineTicketingController@getCustomer')->name('get-customer');

    Route::get('get_customer_fin_data', 'AirlineTicketingController@get_customer_fin_information_by_contact_id');



    Route::get('airlines', 'AirlineTicketingController@airlines');

    Route::get('airline_agents', 'AirlineTicketingController@airline_agents');

    Route::get('airline_airports', 'AirlineTicketingController@airline_airports')->name('airports');

    

    

    Route::get('airport/create/{id?}', 'AirlineSettingController@create_edit_airport');

   

    Route::post('add_service', 'AirlineServiceController@store_service')->name('add_service');

    Route::patch('edit_service', 'AirlineServiceController@edit_service')->name('edit_service');

    Route::delete('delete_service', 'AirlineServiceController@delete_service')->name('delete_service');

    // form settings 
    Route::get('/form_settings', 'FormSettingsController@index');
    Route::post('/form_settings/update_customers', 'FormSettingsController@updateCustomers')->name('update_customers');
    Route::post('/form_settings/update_suppliers', 'FormSettingsController@updateSuppliers')->name('update_suppliers');
    Route::post('/form_settings/update_passengers', 'FormSettingsController@updatePassengers')->name('update_passengers');

    Route::get('/form_settings/check_form_settings_customers', 'FormSettingsController@checkFormSettingCustomers')->name('check_form_settings_customers');
    Route::get('/form_settings/check_form_settings_suppliers', 'FormSettingsController@checkFormSettingSuppliers')->name('check_form_settings_suppliers');
    Route::get('/form_settings/check_form_settings_passengers', 'FormSettingsController@checkFormSettingPassengers')->name('check_form_settings_passengers');
    
    
    //Linked supplier account
    Route::get('/airline/linked_supplier_account', 'AirlineTicketingController@linked_supplier_account')->name('airline.linked_supplier_account');;
    Route::get('/airline/create_linked_supplier_account/{supplier_id}', 'AirlineTicketingController@create_linked_supplier_account')->name('airline.create_linked_supplier_account');
    Route::get('/airline/get_accounts_by_group/{accountGroupId}', 'AirlineTicketingController@getAccountsByGroup')->name('get.accounts.by.group');
    Route::get('/airline/get_sub_types/{businessId}/{accountTypeId?}', 'AirlineTicketingController@getSubAccountTypes')->name('get_account_sub_types');
    Route::get('/airline/get_account_by_sub_type/{businessId}/{accountTypeId}', 'AirlineTicketingController@getAccountByAccountSubType')->name('get_account_by_sub_type');
    Route::post('/submit_linked_supplier_account', 'AirlineTicketingController@submitLinkedSupplierAccount')->name('airline.submit_linked_supplier_account');
    Route::get('/submit_linked_supplier_account', 'AirlineTicketingController@submitLinkedSupplierAccount')->name('airline.submit_linked_supplier_account');
    Route::get('/get_linked_supplier_accounts', 'AirlineTicketingController@getLinkedSupplierAccounts')->name('airline.get_linked_supplier_accounts');
    Route::get('/get_linked_supplier_account/{accountId}', 'AirlineTicketingController@getLinkedSupplierAccount')->name('airline.get_linked_supplier_account');
    Route::get('/delete_linked_supplier_account/{id}', 'AirlineTicketingController@deleteLinkedSupplierAccount')->name('airline.delete_linked_supplier_account');
    Route::delete('/delete_linked_supplier_account/{id}', 'AirlineTicketingController@deleteLinkedSupplierAccount')->name('airline.delete_linked_supplier_account');



     


});