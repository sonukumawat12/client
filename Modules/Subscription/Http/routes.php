<?php

Route::group(['middleware' => ['web','auth','tenant.context'], 'prefix' => 'subscription', 'namespace' => 'Modules\Subscription\Http\Controllers'], function()
{
    
    Route::post('/setings/add-subscription/{id}', 'SubscriptionSettingController@save_subscription');
    Route::get('/setings/add-subscription/{id}', 'SubscriptionSettingController@add_subscription');
    Route::get('/setings/fetch-subscription/{id}', 'SubscriptionSettingController@fetch_subscription');
    
    Route::resource('/settings', 'SubscriptionSettingController');
    Route::resource('/templates', 'SubscriptionSmsTemplateController');
    Route::resource('/user-activity', 'SubscriptionUserActivityController');
    
    Route::get('/subscription/list-invoices', 'SubscriptionListController@listInvoices');
    Route::get('/subscription/print/{id}', 'SubscriptionListController@print');
    
    
    Route::post('/list/edit-status/{id}', 'SubscriptionListController@update_status');
    Route::get('/list/edit-status/{id}', 'SubscriptionListController@edit_status');
    
    Route::post('/list/add-payment/{id}', 'SubscriptionListController@save_payment');
    Route::get('/list/add-payment/{id}', 'SubscriptionListController@add_payment');
    Route::get('/list/view-history/{id}', 'SubscriptionListController@view_history');
    Route::get('/list/view-expiring', 'SubscriptionListController@view_expiring');
    
    Route::get('/get-cycles/{product}', 'SubscriptionListController@getproductCycles');
    Route::resource('/list', 'SubscriptionListController');

});

// unauthenticated routes
Route::group(['prefix' => 'subscription', 'namespace' => 'Modules\Subscription\Http\Controllers'], function()
{
    Route::get('/notify-expiring', 'SubscriptionListController@notify_expiring');
});


