<?php

Route::prefix('helpguide')->group(function() {
    $middleware = ['role:customer|non-restricted_agent|agent|admin|super_admin'];
    
    if((boolean)setting('verify_email', true) === true){
        $middleware[] = 'verified';
    }
    Route::group(['middleware' => 'frontend'], function () {
    
        // Customer home page
        Route::get('/', 'MyAccount\IndexController@index')->name('my_account');
    
        // Customer page
        Route::group([ 'prefix' => 'profile',], function () {
            Route::get('/', "MyAccount\ProfileController@profile")->name("my_account.profile");
            Route::post('/', "MyAccount\ProfileController@profile");
        });
    
        // Upload file
        Route::post('/upload', 'UploadController@index');
    
        // Notifications
        Route::group([ 'prefix' => 'notifications'], function(){
            // Get all notifications
            Route::get('/', 'MyAccount\NotificationsController@all');
    
            // Get unread notifications
            Route::post('unread', 'MyAccount\NotificationsController@unread');
    
            // Make notifications as read
            Route::post('mark_as_read', 'MyAccount\NotificationsController@markAsRead');
        });
    
         // js lang
         Route::get('lang.js', ['uses' => 'LanguageController@langJs', 'file' => 'js'])->name('my_account.lang');
    
    });
});
