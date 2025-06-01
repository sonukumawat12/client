<?php 
/**
 * @var string $moduleName
 */
$moduleName = 'HelpGuide';
Route::prefix('helpguide')->group(function() {
    // a group with locale prefix
    Route::pattern('locale', '[a-z]{2}');
    Route::group(['middleware' => 'frontend', 'prefix' => '{locale}', 'as' => '_'], function() {
        require module_path('HelpGuide', 'Routes/web.php');
    });
    
    // a group with empty prefix
    Route::group(['middleware' => 'frontend'], function() {
        require module_path('HelpGuide', 'Routes/web.php');
    });
    
    // js lang
    Route::get('lang.js', ['uses' => 'LanguageController@langJs', 'file' => 'frontend_js'])->name('frontend.lang');
});