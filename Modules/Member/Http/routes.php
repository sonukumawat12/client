<?php


Route::group(['middleware' => ['web','tenant.context'], 'prefix' => 'member-module', 'namespace' => 'Modules\Member\Http\Controllers'], function()
{
    Route::post('/member/update-status', 'MemberController@updateStatus')->name('member.update_status');
    Route::resource('/members', 'MemberController');
    Route::resource('/member-settings', 'MemberSettingController');
  
    Route::resource('/gramaseva-vasama', 'GramasevaVasamaController');
    Route::resource('/balamandalaya', 'BalamandalayaController');
    Route::resource('/member-groups', 'MemberGroupController');
    Route::resource('/service-areas', 'ServiceAreasController');
    //add by sakhawat
    Route::resource('/province', 'ProvinceController');
    Route::resource('/district', 'DistrictController');
    Route::post('/district-get', 'DistrictController@get');
    Route::resource('/electrorate', 'ElectrorateController');
    Route::post('/electrorate-get', 'ElectrorateController@get');
   
    Route::get('/users-activity', 'MemberController@memberUserActivity');
    
    Route::get('/member-sms-settings', 'MemberController@smsSettings');
     Route::post('/sms-setting', 'MemberController@submitQuickSend');
    
    
    Route::resource('/member-staff', 'MemberStaffController');
   Route::get('suggestion-remark/{id}', 'SuggestionController@remark');
   Route::get('suggestion-document/{id}', 'SuggestionController@document');
    Route::PUT('suggestion-update/{id}', 'SuggestionController@update');
    Route::get('member-staff-status/{id}', 'MemberStaffController@updateStatus');
    Route::get('assign-suggestion/{id}', 'MemberStaffController@assignToSuggestion');
    Route::post('assigned-staff', 'MemberStaffController@storeAssignedStaff');
    Route::post('member-row', 'MemberController@addMememberRow');
    Route::get('member-profile/{id}', 'MemberController@view_profile_model');
     Route::PUT('members/{id}/edit', 'MemberController@member_update');
     Route::get('members/{id}/edits', 'MemberController@edits');
    Route::get('sub-members', 'MemberController@all_sub_member');
    
    Route::get('/get-provinces/{country}', 'ProvinceController@getProvinces');
    Route::get('/get-districts/{province}', 'DistrictController@getDistricts');
    Route::get('/get-electrorates/{district}', 'ElectrorateController@getElectrorates');
    Route::get('/get-gsvasamas/{electrorate}', 'GramasevaVasamaController@getGsvasamas');
});
