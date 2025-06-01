<?php


Route::get('update-statement-nos', 'Modules\Vat\Http\Controllers\CustomerStatementController@updatePrefixes');

Route::group(['middleware' => ['web','tenant.context','IsSubscribed:vat_module'], 'prefix' => 'vat-module', 'namespace' => 'Modules\Vat\Http\Controllers'], function () {
    
    Route::get('get-customer-statement-no', 'CustomerStatementController@getCustomerStatementNo');  
    Route::get('customer-statement/get-statement-list', 'CustomerStatementController@getCustomerStatementList');
    Route::get('customer-statement/reprint/{statement_id}', 'CustomerStatementController@rePrint');
    Route::get('customer-statement/export-excel/{statement_id}', 'CustomerStatementController@exportExcel');
    Route::post('/download-pdf', 'CustomerStatementController@downloadPdf');
    Route::get('customer-date', 'CustomerStatementController@getMinimumDate');
    
    Route::delete('customer-statement-payments/{id}', 'CustomerStatementController@destroyPayments');
    
    Route::get('convert-to-vat/{id}', 'CustomerStatementController@convertVAT');
    
    Route::resource('customer-statement', 'CustomerStatementController');
    
    Route::resource('vat-statement-logo', 'VatStatementLogoController');
    
    
    
    Route::get('/get-prefix/{id}', 'VatInvoiceController@getPrefixes');
    Route::get('/print/{id}', 'VatInvoiceController@print');
    Route::get('/vat-invoice/products-sold', 'VatInvoiceController@productsSold');
    
    Route::get('/get-route-ops/{id}', 'VatInvoice2Controller@getRouteOperations');
    Route::get('/get-ro-details/{id}', 'VatInvoice2Controller@routeOperationDetails');
    
    Route::get('/get-prefix2/{id}', 'VatInvoice2Controller@getPrefixes');
    Route::get('/print2/{id}', 'VatInvoice2Controller@print');
    
    Route::get('/fleet-print2/{id}', 'FleetVatInvoice2Controller@print');
    
    Route::get('/invoices-127', 'VatInvoice2Controller@index127');
    Route::get('/invoices-127/create', 'VatInvoice2Controller@create127');
    Route::post('/invoices-127', 'VatInvoice2Controller@store127');
    Route::get('/print127/{id}', 'VatInvoice2Controller@print127');
    
    
    Route::get('/quick-add-customer', 'VatInvoice2Controller@customerQuickAdd');
    Route::post('/quick-add-customer', 'VatInvoice2Controller@storeQuickCustomer');
    
    Route::get('/quick-add-reference', 'VatInvoice2Controller@referenceQuickAdd');
    Route::post('/quick-add-reference', 'VatInvoice2Controller@storeQuickReference');
    
    Route::get('/vat-invoice2/products-sold', 'VatInvoice2Controller@productsSold');
    Route::get('/invoices-127/edit127/{id}', 'VatInvoice2Controller@edit127');
    Route::put('/invoices-127/update127/{id}', 'VatInvoice2Controller@update127');
    
    Route::get('/vat-invoice2/invoices_setting', 'VatInvoice2Controller@invoicesSetting');
    Route::post('/vat-invoice2/invoices_setting/updateSetting', 'VatInvoice2Controller@updateSetting');
    
    Route::get('/vat-statement/setting', 'CustomerStatementController@invoicesSetting');
    Route::post('/vat-statement/setting/updateSetting', 'CustomerStatementController@updateSetting'); 
    
    Route::resource('/vat-invoice2', 'VatInvoice2Controller');
    
    Route::resource('/fleet-vat-invoice2', 'FleetVatInvoice2Controller');
    
    Route::resource('/vat-settings', 'SettingsController');
    Route::resource('/vat-invoice', 'VatInvoiceController');
    Route::resource('/vat-discount', 'VatPenaltyController');
    Route::resource('/vat-prefix', 'VatPrefixController');
    
    Route::resource('/vat-statement-prefix', 'VatStatementPrefixController');
    Route::resource('/vat-invoice2-prefix', 'VatInvoice2PrefixController');
    
    Route::resource('/vat-user-prefix', 'VatUserInvoicePrefixController');
    Route::resource('/vat-sms-type', 'VatInvoiceSmsTypeController');
    
    Route::resource('/vat-creditbill', 'VatCreditBillController');
    
    Route::resource('/vat-category', 'CategoryController');
    
    Route::resource('/vat-concerns', 'VatConcernController');
    Route::resource('/vat-bank-details', 'VatBankDetailController');
    Route::resource('/vat-supply-from', 'VatSupplyFromController');
    
    Route::get('/import-products', 'ImportProductsController@index');
    Route::post('/import-products/store', 'ImportProductsController@store');
    
    Route::get('/import-contacts', 'ImportContactsController@index');
    Route::post('/import-contacts/store', 'ImportContactsController@store');
    
    Route::resource('/vat-payable', 'VatPayableToAccountController');
    
    Route::post('/vat-products/mass-delete', 'VatProductController@massDestroy');
    Route::post('/vat-products/mass-deactivate', 'VatProductController@massDeactivate');
    Route::post('/vat-products/check_product_sku', 'VatProductController@checkProductSku');
    Route::get('/vat-products/view/{id}', 'VatProductController@view');
    Route::post('/vat-products/product_form_part', 'VatProductController@getProductVariationFormPart');
    Route::get('/vat-product-search', 'VatProductController@getProducts');
    Route::post('/vat-purchases/get_purchase_entry_row', 'VatPurchaseController@getPurchaseEntryRow');
    Route::get('/vat-purchases/print/{id}', 'VatPurchaseController@printInvoice');
    Route::post('/vat-purchases/update-status', 'VatPurchaseController@updateStatus');
     
    
    Route::resource('/vat-expense-categories', 'VatExpenseCategoryController');
    Route::resource('/vat-expense', 'VatExpenseController');
    Route::resource('/vat-payments', 'VatPaymentController');
    Route::resource('/vat-units', 'VatUnitController');
    Route::get('/vat-toggle-activate/{id}', 'VatContactController@toggleActivate');
    Route::post('/contact-massdestroy', 'VatContactController@massDestroy');
    Route::resource('/vat-contacts', 'VatContactController');
    Route::resource('/vat-products', 'VatProductController');
    Route::resource('/vat-purchases', 'VatPurchaseController');
    
    Route::get('/reports-get-ledger', 'VatReportController@getLedger');
    
    Route::resource('/reports-ledger', 'VatReportController');
    
   Route::get('/customer-vat-schedule', 'VatController@getCustomerVatSchedule');
   Route::get('/supplier-vat-schedule', 'VatController@getSupplierVatSchedule');
   
   Route::post('/update-range-vats', 'VatController@updateVats');
   Route::get('/update-single-vats', 'VatController@updateSingleVats');
   
   Route::get('/reports', 'VatController@getVatReport');
   Route::get('/print', 'VatController@printVatReport');
   
   
   
   
   
    Route::get('/settlement/get_balance_stock/{id}', 'VatSettlementController@getBalanceStock');
    Route::get('/settlement/update-credit-sales', 'VatSettlementController@updateCreditSales');
    
    Route::get('/settlement/get_balance_stock_by_id/{id}', 'VatSettlementController@getBalanceStockById');
    
    Route::delete('/settlement/delete-other-sale/{id}', 'VatSettlementController@deleteOtherSale');
    Route::delete('/settlement/delete-meter-sale/{id}', 'VatSettlementController@deleteMeterSale');
    
    Route::post('/settlement/save-customer-payment', 'VatSettlementController@saveCustomerPayment');
    Route::post('/settlement/save-other-sale', 'VatSettlementController@saveOtherSale');
    Route::post('/settlement/save-meter-sale', 'VatSettlementController@saveMeterSale');
    
    Route::get('/settlement/get-pump-details/{pump_id}', 'VatSettlementController@getPumpDetails');
    Route::get('/settlement/print/{id}', 'VatSettlementController@print');
    Route::resource('/settlement', 'VatSettlementController');
    
    Route::get('/settlement/payment/get-product-price', 'VatAddPaymentController@getProductPrice');


    Route::delete('/settlement/payment/delete-credit-sale-payment/{id}', 'VatAddPaymentController@deleteCreditSalePayment');
    Route::post('/settlement/payment/save-credit-sale-payment', 'VatAddPaymentController@saveCreditSalePayment');
    
    Route::delete('/settlement/payment/delete-card-payment/{id}', 'VatAddPaymentController@deleteCardPayment');
    Route::post('/settlement/payment/save-card-payment', 'VatAddPaymentController@saveCardPayment');
    
    Route::delete('/settlement/payment/delete-cash-payment/{id}', 'VatAddPaymentController@deleteCashPayment');
    Route::post('/settlement/payment/save-cash-payment', 'VatAddPaymentController@saveCashPayment');
    
    
    Route::get('/settlement/payment', 'VatAddPaymentController@create');
    Route::resource('/settlement/payment', 'VatAddPaymentController');
   
   
});
