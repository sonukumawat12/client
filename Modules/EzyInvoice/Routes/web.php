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

Route::group(['middleware' => ['web', 'auth', 'language','tenant.context'], 'prefix' => 'ezy-invoice'/*, 'namespace' => 'Modules\EzyInvoice\Http\Controllers'*/], function () {
    Route::get('/invoices/preview/{id}', 'EzyInvoiceController@preview');
    Route::get('/invoices/preview/credit-sale-product/{id}', 'EzyInvoiceController@productPreview');
    Route::post('/invoices/payment/save-credit-sale-payment', 'EzyInvoiceController@saveCreditSalePayment');
    Route::delete('/invoices/payment/delete-credit-sale-payment/{id}', 'EzyInvoiceController@deleteCreditSalePayment');
    Route::get('/invoices/payment/get-customer-details/{customer_id}', 'EzyInvoiceController@getCustomerDetails');
    Route::get('/invoices/payment/get-product-price', 'EzyInvoiceController@getProductPrice');
    Route::get('/invoices/print/{id}', 'EzyInvoiceController@print');
    Route::resource('/invoices', 'EzyInvoiceController');
});
