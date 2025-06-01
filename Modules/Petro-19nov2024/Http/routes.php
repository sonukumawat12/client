<?php

Route::group(['prefix' => 'petro', 'namespace' => 'Modules\Petro\Http\Controllers'], function () {
    Route::get('/adjust-dates', 'SettlementController@adjustMeterSalesDates');
});

Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData', 'DayEnd', 'tenant.context'], 'prefix' => 'ezyproduct', 'namespace' => 'Modules\Petro\Http\Controllers'], function () {
    Route::get('/products/view/{id}', 'ProductController@view');
    Route::get('/products/list', 'ProductController@getProducts');
    Route::resource('products', 'ProductController');
    Route::resource('categories', 'CategoryController');
    Route::resource('units', 'UnitController');
});
Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData', 'DayEnd', 'tenant.context'], 'prefix' => 'petro', 'namespace' => 'Modules\Petro\Http\Controllers'], function () {
    Route::get('/dashboard', 'PetroController@index');

    Route::post('/tank/save-import', 'FuelTankController@saveImport');
    Route::get('/tank/import', 'FuelTankController@import');

    Route::get('/tank-management/get-tank-product', 'FuelTankController@getTankProduct');
    Route::resource('/tank-management', 'FuelTankController');

    Route::resource('/prefixes', 'CustomerBillVatPrefixController');

    Route::resource('/notification-templates', 'PetroNotificationTemplateController');

    Route::get('/day-end-settlement-pumps', 'DayEndSettlementController@pendingPumps');
    Route::resource('/day-end-settlement', 'DayEndSettlementController');

    Route::resource('/tank-transfer', 'TankTransferController');

    Route::get('/tanks-transaction-summary', 'TanksTransactionDetailController@tankTransactionSummary');
    Route::resource('/tanks-transaction-details', 'TanksTransactionDetailController');
    Route::post('/pumps/save-import', 'PumpController@saveImport');
    Route::get('/pumps/import', 'PumpController@importPumps');
    Route::get('/pump-management/get-meter-readings', 'PumpController@getMeterReadings');
    Route::get('/pump-management/get-testing-details', 'PumpController@getTestingDetails');
    Route::resource('/pump-management', 'PumpController');

    Route::get('/pump-operators/get-settings', 'PumpOperatorController@dashboard_settings');
    Route::post('/pump-operators/get-settings', 'PumpOperatorController@store_settings');

    Route::get('/pump-operators/update-passcode', 'PumpOperatorController@update_passcode');
    Route::post('/pump-operators/update-passcode', 'PumpOperatorController@store_passcode');


    Route::get('/pump-operators/get-pumpter-excess-shortage-payments', 'PumpOperatorController@getPumperExcessShortagePayments');
    Route::post('/pump-operators/save-import', 'PumpOperatorController@saveImport');
    Route::get('/pump-operators/import', 'PumpOperatorController@importPumps');
    Route::get('/pump-operators/ledger', 'PumpOperatorController@getLedger');
    Route::get('/pump-operators/list-commission/{id}', 'PumpOperatorController@listCommission');
    Route::resource('/recover-shortage', 'RecoverShortageController');
    Route::resource('/excess-comission', 'ExcessComissionController');
    Route::get('/pump-operators/toggle-active/{id}', 'PumpOperatorController@toggleActivate');
    Route::get('/pump-operators/get-dashboard-data', 'PumpOperatorController@getDashboardData');

    Route::get('/pump-operators/setting_dash', 'PumpOperatorController@setting_dash');
    Route::get('/pump-operators/dashboard', 'PumpOperatorController@dashboard');
    Route::get('/pump-operators/check-passcode', 'PumpOperatorController@checPasscode');
    Route::get('/pump-operators/check-username', 'PumpOperatorController@checUsername');
    Route::resource('/pump-operators/shift-summary', 'ShiftSummaryController');
    Route::get('/pump-operators/pumper-day-entries/add-settlement-no/{id}', 'PumperDayEntryController@getAddSettlementNo');
    Route::post('/pump-operators/pumper-day-entries/add-settlement-no/{id}', 'PumperDayEntryController@postAddSettlementNo');
    Route::get('/pump-operators/pumper-day-entries/view-settlement-no/{id}', 'PumperDayEntryController@viewAddSettlementNo');
    Route::get('/pump-operators/pumper-day-entries/get-daily-collection', 'PumperDayEntryController@getDailyCollection');
    Route::resource('/pump-operators/pumper-day-entries', 'PumperDayEntryController');
    Route::get('/pump-operators/set-main-system-session', 'PumpOperatorController@setMainSystemSession');
    Route::resource('/pump-operators', 'PumpOperatorController');
    Route::resource('/opening-meter', 'OpeningMeterController');

    Route::post('/pump-operator-actions/get-pumper-assignment/{pump_id}/{pump_operator_id}', 'PumpOperatorActionsController@postPumperAssignment');
    Route::post('/pump-operator-actions/get-colsing-meter/{pump_id}', 'PumpOperatorActionsController@postClosingMeter');
    Route::get('/pump-operator-actions/get-colsing-meter/{pump_id}', 'PumpOperatorActionsController@getClosingMeter');
    Route::get('/pump-operator-actions/get-colsing-meter-modal', 'PumpOperatorActionsController@getClosingMeterModal');
    Route::get('/pump-operator-actions/get-receive-pump', 'PumpOperatorActionsController@getReceivePump');


    Route::post('/pump-operator-pmts/save-credit', 'PumpOperatorPaymentController@saveCredit');
    
    Route::post('/pump-operator-pmts/save-cheque', 'PumpOperatorPaymentController@saveChequePayment');
    
    Route::post('/pump-operator-pmts/save-other-sale', 'PumpOperatorPaymentController@saveOtherSale');

    Route::post('/pump-operator-pmts/save-other-sale-items', 'PumpOperatorPaymentController@saveOtherSaleItems');
    
    Route::post('/pump-operator-pmts/save-cash-denom', 'PumpOperatorPaymentController@saveCashDenom');
    
    Route::post('/pump-operator-pmts/save-card-pmt', 'PumpOperatorPaymentController@saveCardPayment');
    
    Route::post('/pump-operator-pmts/save-meter-sale', 'PumpOperatorPaymentController@saveMeterSale');
    
    Route::get('/pump-operator-pmts/get-other-sale', 'PumpOperatorPaymentController@getOtherSale');

    Route::get('/dailycollection/summary', 'DailyCollectionController@collectionSummary');
    
    Route::get('/dailycollection/shortage-excess', 'DailyCollectionController@indexShortageExcess');
    Route::delete('/dailycollection/shortage-excess/{id}', 'DailyCollectionController@destroyShortageExcess');
    
    Route::get('/dailycollection/cheques', 'DailyCollectionController@indexCheque');
    Route::delete('/dailycollection/cheques/{id}', 'DailyCollectionController@destroyCheque');
    
    Route::get('/dailycollection/others', 'DailyCollectionController@indexOther');
    Route::delete('/dailycollection/others/{id}', 'DailyCollectionController@destroyOther');

    Route::get('/pump-operator-payments/get-payment-modal', 'PumpOperatorPaymentController@getPaymentModal');
    
    Route::get('/pump-operator-payments/get-other-sales/{id}', 'PumpOperatorPaymentController@otherSales');
    Route::get('/pump-operator-payments/othersale', 'PumpOperatorPaymentController@othersalespage');
    Route::get('/pump-operator-payments/othersale/getproducts', 'PumpOperatorPaymentController@getProducts');
    Route::delete('/pump-operator-payments/delete-other-sale/{id}', 'PumpOperatorPaymentController@deleteOtherSale');
    Route::get('/pump-operator-payments/othersale-list', 'PumpOperatorPaymentController@otherSalesList');
    Route::get('/pump-operator-payments/metersale-list', 'PumpOperatorPaymentController@meterSalesList');
    
    Route::get('/pump-operator-payments/get-modal', 'PumpOperatorPaymentController@getPaymentSummaryModal');
    Route::get('/pump-operator-payments/balance-to-operator/{pump_operator}', 'PumpOperatorPaymentController@balanceToOperator');
    Route::resource('/pump-operator-payments', 'PumpOperatorPaymentController');
    Route::get('/closing-shift/close-shift/{pump_operator_id}', 'ClosingShiftController@closeShift');
    Route::resource('/closing-shift', 'ClosingShiftController');
    Route::get('/current-meter/get-modal', 'CurrentMeterController@getModal');
    Route::resource('/current-meter', 'CurrentMeterController');
    Route::get('/unload-stock/get-details', 'UnloadStockController@getDetails');
    Route::resource('/unload-stock', 'UnloadStockController');

    Route::get('/get-assigned-pumps/{id}', 'PumpOperatorController@getAssignedPumps');

    Route::get('/pump-operator-actions/get-pumper-assignment/{pump_id}/{pump_operator_id}', 'PumpOperatorAssignmentController@getPumperAssignment');
    
    Route::get('/pump-operator-actions/get-day-entry-summary', 'PumperDayEntryController@getPumperDayEntrySummary');
    
    Route::get('/pump-operator-actions/get-closing-shift-summary', 'PumperDayEntryController@getClosingShiftSummary');
    
    Route::get('/pump-operator-actions/confirm-pumps/{assignment_id}', 'PumpOperatorAssignmentController@confirmAssignment');
    Route::post('/pump-operator-actions/confirm-pumps/{assignment_id}', 'PumpOperatorAssignmentController@postConfirmAssignment');

    Route::post('/bulk-pump-operator-assignment', 'PumpOperatorAssignmentController@storeBulk');
    Route::resource('/pump-operator-assignment', 'PumpOperatorAssignmentController');

    //common controller for document & note
    Route::get('get-document-note-page', 'PumperDocumentAndNoteController@getDocAndNoteIndexPage');
    Route::post('post-document-upload', 'PumperDocumentAndNoteController@postMedia');
    Route::resource('pumper-note-documents', 'PumperDocumentAndNoteController');

    Route::get('/daily-collection/print/{pump_operator_id}', 'DailyCollectionController@print');

    Route::get('/daily-collection/edit-shortage/{id}', 'DailyCollectionController@editShortage');
    Route::put('/daily-collection/edit-shortage/{id}', 'DailyCollectionController@updateShortage');

    Route::get('/daily-collection/get-balance-collection/{pump_operator_id}', 'DailyCollectionController@getBalanceCollection');
    Route::resource('/daily-collection', 'DailyCollectionController');
    Route::resource('/daily-cards', 'DailyCardController');

    Route::get('/get-pump-sales', 'DailyStatusReportController@getPumpSales');
    Route::get('/get-fuel-sale', 'DailyStatusReportController@getFuelSale');
    Route::get('/get-lubricant-sale', 'DailyStatusReportController@getLubricantSale');
    Route::get('/get-other-sale', 'DailyStatusReportController@getOtherSale');
    Route::get('/get-gas-sale', 'DailyStatusReportController@getGasSale');
    Route::get('/get-credit-sale', 'DailyStatusReportController@getCreditSale');
    Route::get('/get-total-payments', 'DailyStatusReportController@getTotalPayments');
    Route::get('/print-report', 'DailyStatusReportController@printReport');
    Route::post('/download-pdf', 'DailyStatusReportController@downloadPdf');
    Route::resource('/daily-status-report', 'DailyStatusReportController');


    Route::get('/settlement/get_balance_stock/{id}', 'SettlementController@getBalanceStock');
    Route::get('/settlement/activity-report', 'SettlementController@getUserActivityReport');
    Route::get('/settlement/get_pumps/{id}', 'SettlementController@getPumps');

    Route::get('/settlement/get-meter-sales', 'SettlementController@meter_sales');

    Route::get('/settlement/update-meter-sale/{id}', 'SettlementController@editMeterSale');
    Route::post('/settlement/update-meter-sale/{id}', 'SettlementController@updateMeterSale');

    Route::get('/settlement/update-credit-sales', 'SettlementController@updateCreditSales');

    Route::get('/settlement/get_balance_stock_by_id/{id}', 'SettlementController@getBalanceStockById');
    Route::delete('/settlement/delete-customer-payment/{id}', 'SettlementController@deleteCustomerPayment');
    Route::delete('/settlement/delete-other-income/{id}', 'SettlementController@deleteOtherIncome');
    Route::delete('/settlement/delete-other-sale/{id}', 'SettlementController@deleteOtherSale');
    Route::delete('/settlement/delete-meter-sale/{id}', 'SettlementController@deleteMeterSale');
    Route::post('/settlement/save-customer-payment', 'SettlementController@saveCustomerPayment');
    Route::post('/settlement/save-other-income', 'SettlementController@saveOtherIncome');
    Route::post('/settlement/save-other-sale', 'SettlementController@saveOtherSale');
    Route::post('/settlement/save-meter-sale', 'SettlementController@saveMeterSale');
    Route::get('/settlement/get-pump-details/{pump_id}', 'SettlementController@getPumpDetails');
    Route::get('/settlement/print/{id}', 'SettlementController@print');
    Route::get('/settlement/get-meter-sale-form/{id}', 'SettlementController@getMeterSaleForm');
    Route::post('/settlement/update-settlement-meter-sale/{id}', 'SettlementController@updateSettlementMeterSale');
    Route::resource('/settlement', 'SettlementController');


    Route::delete('/settlement/payment/delete-excess-payment/{id}', 'AddPaymentController@deleteExcessPayment');
    Route::post('/settlement/payment/save-excess-payment', 'AddPaymentController@saveExcessPayment');
    Route::delete('/settlement/payment/delete-shortage-payment/{id}', 'AddPaymentController@deleteShortagePayment');
    Route::post('/settlement/payment/save-shortage-payment', 'AddPaymentController@saveShortagePayment');
    Route::delete('/settlement/payment/delete-expense-payment/{id}', 'AddPaymentController@deleteExpensePayment');
    Route::post('/settlement/payment/save-expense-payment', 'AddPaymentController@saveExpensePayment');
    Route::delete('/settlement/payment/delete-credit-sale-payment/{id}', 'AddPaymentController@deleteCreditSalePayment');
    Route::post('/settlement/payment/save-credit-sale-payment', 'AddPaymentController@saveCreditSalePayment');
    Route::delete('/settlement/payment/delete-cheque-payment/{id}', 'AddPaymentController@deleteChequePayment');
    Route::post('/settlement/payment/save-cheque-payment', 'AddPaymentController@saveChequePayment');
    Route::delete('/settlement/payment/delete-card-payment/{id}', 'AddPaymentController@deleteCardPayment');
    Route::post('/settlement/payment/save-card-payment', 'AddPaymentController@saveCardPayment');
    Route::delete('/settlement/payment/delete-cash-payment/{id}', 'AddPaymentController@deleteCashPayment');

    Route::delete('/settlement/payment/delete-customer-loans/{id}', 'AddPaymentController@deleteCustomerLoan');

    Route::delete('/settlement/payment/delete-loan-payment/{id}', 'AddPaymentController@deleteLoanPayment');
    Route::delete('/settlement/payment/delete-drawing-payment/{id}', 'AddPaymentController@deleteDrawingPayment');
    Route::delete('/settlement/payment/delete-cash-deposit/{id}', 'AddPaymentController@deleteCashDeposit');
    Route::post('/settlement/payment/save-cash-payment', 'AddPaymentController@saveCashPayment');

    Route::post('/settlement/payment/save-customer-loans', 'AddPaymentController@saveCustomerLoan');

    Route::post('/settlement/payment/save-loan-payment', 'AddPaymentController@saveLoanPayment');
    Route::post('/settlement/payment/save-drawing-payment', 'AddPaymentController@saveDrawingPayment');
    Route::post('/settlement/payment/save-cash-deposit', 'AddPaymentController@saveCashDeposit');
    Route::get('/settlement/payment/get-product-price', 'AddPaymentController@getProductPrice');
    Route::get('/settlement/payment/get-customer-details/{customer_id}', 'AddPaymentController@getCustomerDetails');
    Route::get('/settlement/payment/preview/{id}', 'AddPaymentController@preview');
    Route::get('/settlement/payment/preview/credit-sale-product/{id}', 'AddPaymentController@productPreview');
    Route::get('/settlement/payment', 'AddPaymentController@create');
    Route::resource('/settlement/payment', 'AddPaymentController');
    Route::get('/get-stores-by-id', 'SettlementController@getStoresById');
    Route::get('/get-products-by-store-id', 'SettlementController@getProductsByStoreId');


    Route::get('/get-dip-resetting', 'DipManagementController@getDipResetting');
    Route::get('/get-dip-report', 'DipManagementController@getDipReport');
    Route::get('/get-tank-balance-by-id/{tank_id}', 'DipManagementController@getTankBalanceById');
    Route::get('/get-tank-product/{tank_id}', 'DipManagementController@getTankProduct');
    Route::post('/save-resetting-dip', 'DipManagementController@saveResettingDip');
    Route::get('/add-resetting-dip', 'DipManagementController@addResettingDip');
    Route::post('/save-new-dip-reading', 'DipManagementController@saveNewDip');
    Route::get('/add-new-dip', 'DipManagementController@addNewDip');
    
    Route::post('/save-dip-chart', 'DipManagementController@saveDipChart');
    Route::get('/add-dip-chart', 'DipManagementController@addDipChart');
    Route::get('/get-dip-chart', 'DipManagementController@getDipChart');
    
    Route::post('/update-dip-chart/{id}', 'DipManagementController@updateDipChart');
    Route::get('/edit-dip-chart/{id}', 'DipManagementController@editDipChart');
    
    Route::get('/add-dip-chart-reading/{id}', 'DipManagementController@addDipChartReading');
    Route::post('/add-dip-chart-reading/{id}', 'DipManagementController@saveDipChartReading');
    
    Route::delete('/delete-dip-chart/{id}', 'DipManagementController@deleteDipChart');
    
    Route::resource('/dip-management', 'DipManagementController');


    Route::get('/meter-resetting/get-pump-details', 'MeterResettingController@getPumpDetails');
    Route::resource('/meter-resetting', 'MeterResettingController');


    Route::get('issue-customer-bill/get-customer-reference/{id}', 'IssueCustomerBillController@getCustomerReference');
    Route::get('issue-customer-bill/get-product-row', 'IssueCustomerBillController@getProductRow');
    Route::get('issue-customer-bill/get-product-price/{id}', 'IssueCustomerBillController@getProductPrice');
    Route::get('issue-customer-bill/print/{id}', 'IssueCustomerBillController@print');

    Route::get('issue-customer-bill-vat/print/{id}', 'IssueCustomerBillWithVATController@print');

    Route::resource('issue-customer-bill', 'IssueCustomerBillController');

    Route::get('get-prefixes/{id}', 'IssueCustomerBillWithVATController@getPrefixes');

    Route::resource('issue-customer-bill_VAT', 'IssueCustomerBillWithVATController');
    Route::get('issue-customer-bill/get-Customer-credit-limit/{id}', 'IssueCustomerBillWithVATController@getCustomerCreditLimit');

    Route::get('daily-voucher/print/{id}', 'DailyVoucherController@print');
    Route::get('daily-voucher/get-product-row', 'DailyVoucherController@getProductRow');
    Route::resource('daily-voucher', 'DailyVoucherController');
});
Route::group(['middleware' => ['web', 'auth', 'language', 'SetSessionData', 'DayEnd', 'tenant.context'], 'namespace' => 'Modules\Petro\Http\Controllers'], function () {
    Route::get('/vehicles', 'VehicleController@vehicles_list');
    Route::get('/vehicle/edit/{id}', 'VehicleController@edit')->name('vehicle.editVehicle');
    Route::post('/vehicle/update/{id}', 'VehicleController@update')->name('vehicle.updateVehicle');
    Route::get('/vehicle', 'VehicleController@index');
});
