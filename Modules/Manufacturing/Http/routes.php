<?php

Route::group(['middleware' => ['web', 'IsInstalled', 'SetSessionData', 'auth', 'language', 'timezone','tenant.context'], 'prefix' => 'manufacturing', 'namespace' => 'Modules\Manufacturing\Http\Controllers'], function () {
    Route::get('/install', 'InstallController@index');
    Route::post('/install', 'InstallController@install');
    Route::get('/install/update', 'InstallController@update');

    Route::get('/is-recipe-exist/{variation_id}', 'RecipeController@isRecipeExist');
    Route::get('/ingredient-group-form', 'RecipeController@getIngredientGroupForm');
    Route::get('/get-recipe-details', 'RecipeController@getRecipeDetails');
    Route::get('/get-lot-numbers', 'RecipeController@getLotNumbersByProduct');
    Route::get('/get-ingredient-row/{variation_id}', 'RecipeController@getIngredientRow');
    Route::get('/get-by-product-row/{variation_id}', 'RecipeController@getByProductRow');
    Route::get('/production/creates', 'ProductionController@createNew')->name('manufacturing.production.createNew');
    Route::get('/add-ingredient', 'RecipeController@addIngredients');
    Route::resource('/recipe', 'RecipeController', ['except' => ['edit', 'update']]);
    Route::resource('/production', 'ProductionController');
    Route::resource('/settings', 'SettingsController', ['only' => ['index', 'store']]);
    Route::resource('/', 'ManufacturingController');

    Route::get('/report', 'ProductionController@getManufacturingReport');

    Route::post('/update-product-prices', 'RecipeController@updateRecipeProductPrices');
    Route::post('/saveWastage', 'SettingsController@saveWastage')->name('saveWastage');
    Route::get('/getWastage', 'SettingsController@getWastage')->name('getWastage');
    Route::post('/saveExtraCost', 'SettingsController@saveExtraCost')->name('saveExtraCost');
    Route::get('/getExtraCost', 'SettingsController@getExtraCost')->name('getExtraCost');
    Route::post('/saveByProducts', 'SettingsController@saveByProducts')->name('saveByProducts');
    Route::get('/getByProducts', 'SettingsController@getByProducts')->name('getByProducts');
    // Add New Line -- rmtemplate
    Route::get('/getByLotNumbers', 'SettingsController@getByLotNumbers')->name('getByLotNumbers');
    Route::post('/saveByLotNumbers', 'SettingsController@saveByLotNumbers')->name('saveByLotNumbers');
    Route::get('/getByProductsActive', 'SettingsController@getByProductsActive')->name('getByProductsActive');
    // End
    Route::post('/disableItem', 'SettingsController@disableItem')->name('disableItem');
    Route::post('/enableItem', 'SettingsController@enableItem')->name('enableItem');
});
