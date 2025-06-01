<?php 
Route::prefix('helpguide')->group(function() {
    Route::get('/', 'Install\InstallController@index')->name('install.index');
    Route::any('/requirements', 'Install\InstallController@requirements')->name('install.requirements');
    Route::any('/folder_permissions', 'Install\InstallController@folderPermissions')->name('install.folder_permissions');
    Route::any('/product_license', 'Install\InstallController@productLicense')->name('install.product_license');
    Route::any('/database', 'Install\InstallController@database')->name('install.database');
    Route::any('/admin_account', 'Install\InstallController@createUser')->name('install.admin_account');
    Route::any('/finish', 'Install\InstallController@finish')->name('install.finish');
});
