<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'v1', 'namespace' => 'Api'], function () {
    Route::post('/check-balance', 'SmsApiController@checkBalance');
    Route::post('/update-password', 'SmsApiController@updatePassword');
    Route::post('/send-sms', 'SmsApiController@sendSms');
    Route::get('/api/get-liability-accounts', 'AdvancesController@getLiabilityAccounts');
});
