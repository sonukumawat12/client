<?php

Route::group(['middleware' => ['web','tenant.context'], 'prefix' => 'tasks-management', 'namespace' => 'Modules\TasksManagement\Http\Controllers'], function () {
    Route::get('/', 'TasksManagementController@index');
    Route::get('settings', 'SettingsController@index');
    Route::resource('note-groups', 'NoteGroupController');
    Route::resource('task-groups', 'TaskGroupController');
    Route::resource('priority', 'PriorityController');

    Route::get('notes/get_note_id', 'NoteController@getNoteId');
    Route::resource('notes', 'NoteController');
    
    Route::get('tasks/get_note_id', 'TaskController@getTaskId');
    Route::resource('tasks', 'TaskController');
    Route::get('reminders/cancel_reminder/{id}', 'ReminderController@cancelReminder');
    Route::post('reminders/snooze_reminder/{id}', 'ReminderController@snoozeReminder');
    
    Route::get('reminders/enable/{id}', 'ReminderController@enable');
    Route::get('reminders/disable/{id}', 'ReminderController@disable');
    
    Route::resource('reminders', 'ReminderController');


});
