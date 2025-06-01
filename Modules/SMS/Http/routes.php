<?php

Route::group(['middleware' => ['web','tenant.context'], 'prefix' => 'sms', 'namespace' => 'Modules\SMS\Http\Controllers'], function()
{
    Route::get('list/view-numbers/{id}', 'SMSController@showNumbers');
    Route::get('interest-ajax', 'SmsListInterestController@listInterests');
    
    Route::get('view-ledger', 'SmsLedger@viewLedger');
    Route::get('sms-delivery', 'SmsLedger@smsDelivery');
    
    Route::resource('list', 'SMSController');
    Route::resource('sms-logs', 'SmsLogController');
    
    Route::resource('interest', 'SmsListInterestController');
    Route::resource('ledger', 'SmsLedger');
});


Route::group(['middleware' => ['web','tenant.context'], 'prefix' => 'smsmodule', 'namespace' => 'Modules\SMS\Http\Controllers'], function()
{
    Route::get('quick-send', 'SmsSendController@quickSend');
    Route::post('quick-send', 'SmsSendController@submitQuickSend');
    
    Route::get('sms-campaign', 'SmsSendController@smsCampaign');
    Route::post('sms-campaign', 'SmsSendController@submitsmsCampaign');
    Route::get('sms-campaign/get-contacts-by-group', 'SmsSendController@getContactsByGroup');
    
    Route::get('sms-from-file', 'SmsSendController@smsFromFile');
    Route::post('sms-from-file', 'SmsSendController@submitSmsFile');
    Route::post('sms-from-file-final', 'SmsSendController@submitSmsFileFinal');
    
    Route::get('sms-groups', 'SMSController@smsGroups');
    Route::get('sms-groups/create', 'SMSController@createSmsGroup');
    Route::post('sms-groups', 'SMSController@storeSmsGroup');
    Route::get('sms-groups/edit/{id}', 'SMSController@editSmsGroup');
    Route::post('sms-groups/update/{id}', 'SMSController@updateSmsGroup');
    Route::delete('sms-groups/delete/{id}', 'SMSController@deleteSmsGroup');


    
});

Route::get('/execute-sms-campaign', '\Modules\SMS\Http\Controllers\SmsSendController@executeCampaign');
Route::get('/cron-sms-campaign', '\Modules\SMS\Http\Controllers\SmsSendController@sendMessages');
