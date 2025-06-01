<?php

Route::group(config('translation.route_group_config'), function ($router) {
    $router->get(config('translation.ui_url'), 'LanguageController@index')
        ->name('languages.index');

    $router->post(config('translation.ui_url'), 'LanguageController@list');

    $router->get(config('translation.ui_url').'/create', 'LanguageController@create')
        ->name('languages.create');

    $router->post(config('translation.ui_url').'/create', 'LanguageController@store')
        ->name('languages.store');

    $router->post(config('translation.ui_url').'/{language}/delete', 'LanguageController@delete');

    $router->get(config('translation.ui_url').'/{language}/translations', 'LanguageTranslationController@index')
        ->name('languages.translations.index');

    $router->post(config('translation.ui_url').'/{language}', 'LanguageTranslationController@update')
        ->name('languages.translations.update');

    $router->get(config('translation.ui_url').'/{language}/translations/create', 'LanguageTranslationController@create')
        ->name('languages.translations.create');

    $router->post(config('translation.ui_url').'/{language}/translations', 'LanguageTranslationController@store')
        ->name('languages.translations.store');
});
