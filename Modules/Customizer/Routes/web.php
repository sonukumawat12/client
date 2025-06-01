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

Route::prefix('customizer')->group(function() {
    Route::get('/', 'CustomizerController@index')->name('modules.customizer');
    Route::post('/save', 'CustomizerController@store');
    Route::post('/settings', 'CustomizerController@fetch');
});
