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

Route::group(['middleware' => ['web', 'auth', 'tenant.context'], 'prefix' => 'DocManagement'], function () {
        Route::get('documet', 'DocManagementController@index');
        Route::get('doc_settings', 'DocManagementSettingsController@index');
        Route::get('document_category_gets', 'DocManagementSettingsController@doc_category_gets');
          Route::get('document_department_gets', 'DocManagementSettingsController@doc_department_gets');
        Route::get('document_type_gets', 'DocManagementSettingsController@doc_type_gets');
        Route::get('document_purpose_gets', 'DocManagementSettingsController@doc_purpose_gets');
        Route::get('document_forwardwith_gets', 'DocManagementSettingsController@doc_forwardwith_get');
        Route::get('document_mandatorysignature_gets', 'DocManagementSettingsController@doc_mandatorysignature_gets');
        Route::get('document_upload_gets', 'DocManagementSettingsController@doc_upload_gets');
        Route::get('document_uploadlogo_gets', 'DocManagementSettingsController@doc_uploadlogo_gets');
         Route::get('document_purpose_gets', 'DocManagementSettingsController@document_purpose_gets');
        Route::get('store_type', 'DocManagementSettingsController@store_type');
        Route::get('store_purpose', 'DocManagementSettingsController@store_purpose');
        Route::post('store_signature_upload', 'DocManagementSettingsController@store_signatures');
        Route::post('store_logo', 'DocManagementSettingsController@store_logo');
        Route::get('store_mandatorySignature', 'DocManagementSettingsController@store_mandatorySignature');
        Route::get('store_forwardwith', 'DocManagementSettingsController@store_forwardwith');
        Route::get('store_category_type', 'DocManagementSettingsController@store_category');
        Route::get('documet', 'DocManagementController@index');
        Route::get('show', 'DocManagementController@show_status');
        Route::get('create', 'DocManagementController@show');  
       Route::get('view/{doc_no}', [\Module\Http\Controllers\DocManagementController::class, 'view'])->name('doc.view'); 
        Route::get('edit/{doc_no}', [\Module\Http\Controllers\DocManagementController::class, 'edit'])->name('doc.edit'); 
      Route::get('print/{doc_no}', [\Module\Http\Controllers\DocManagementController::class, 'view'])->name('doc.print'); 
        Route::POST('store_upload', 'DocManagementController@store');  
          Route::POST('update_referred', 'DocManagementController@update_referred'); 
        Route::get('get_upload_table', 'DocManagementController@get_upload_table'); 
});
