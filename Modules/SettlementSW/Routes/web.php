<?php
use Illuminate\Support\Facades\Route;
use Modules\SettlementSW\Http\Controllers\SettlementSWController;

Route::group([
  'prefix'     => 'settlement-sw',
  'middleware' => ['web', 'auth', 'language', 'SetSessionData', 'DayEnd', 'tenant.context'],
], function() {
    Route::get('/', [SettlementSWController::class, 'index'])
         ->name('settlement-sw.index');
    Route::get('/create', [SettlementSWController::class, 'create'])
         ->name('settlement-sw.create');
    Route::post('/save-meter-sale', [SettlementSWController::class, 'saveMeterSale']);
    Route::get('/get-pump-details/{pump_id}', [SettlementSWController::class, 'getPumpDetails']);
    Route::get('/get_pumps/{id}', [SettlementSWController::class, 'getPumps']);
    Route::post('/save-other-sale', [SettlementSWController::class,'saveOtherSale']);
    Route::get('/get_balance_stock_by_id/{id}', [SettlementSWController::class,'getBalanceStockById']);
    Route::post('/save-other-income', [SettlementSWController::class,'saveOtherIncome']);
    Route::post('/save-customer-payment', [SettlementSWController::class,'saveCustomerPayment']);
    Route::post('/save-expense-payment', [SettlementSWController::class,'saveExpansePayment']);
    Route::post('/save-credit-sale-payment', [SettlementSWController::class,'saveCreditSalePayment']);
    Route::delete('/delete-meter-sale/{id}', [SettlementSWController::class,'deleteMeterSale']);
    Route::delete('/delete-other-sale/{id}', [SettlementSWController::class,'deleteOtherSale']);
});
