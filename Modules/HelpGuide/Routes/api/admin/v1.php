<?php

use Modules\HelpGuide\Http\Controllers\UploadController;
use Modules\HelpGuide\Http\Controllers\Dashboard\SearchController;
use Modules\HelpGuide\Http\Controllers\Dashboard\TicketController;
use Modules\HelpGuide\Http\Controllers\Dashboard\SettingController;
use Modules\HelpGuide\Http\Controllers\Dashboard\EmployeeController;
use Modules\HelpGuide\Http\Controllers\Dashboard\StatisticsController;
use Modules\HelpGuide\Http\Controllers\Dashboard\ModulesManagerController;

/*
|--------------------------------------------------------------------------
| Ticket
|--------------------------------------------------------------------------
|
|
*/

Route::group(['prefix' => 'tickets'], function () {

  Route::get('/', [TicketController::class, 'list']);
  Route::get('/', [TicketController::class, 'list']);
  Route::post('re_assign/{ticket}', [TicketController::class, 'reAssign']);

  // Insights 
  Route::post('statistics/overview', [StatisticsController::class, 'ticketsOverview']);
});

/*
|--------------------------------------------------------------------------
| Upload
|--------------------------------------------------------------------------
|
|
*/
Route::post('/upload', [UploadController::class, 'index']);

/*
|--------------------------------------------------------------------------
| Upload
|--------------------------------------------------------------------------
|
|
*/
Route::get('/search', [SearchController::class, 'search']);

/*
|--------------------------------------------------------------------------
| Setting
|--------------------------------------------------------------------------
|
|
*/
Route::post('/settings', [SettingController::class, 'save']);

/*
|--------------------------------------------------------------------------
| employees
|--------------------------------------------------------------------------
|
|
*/
Route::group(['prefix' => 'employees'], function () {
  Route::get('/list', [EmployeeController::class, 'list']);
  Route::get('/{id}', [EmployeeController::class, 'show']);
});


/*
|--------------------------------------------------------------------------
| Modules
|--------------------------------------------------------------------------
|
|
*/
Route::group(['prefix' => 'modules'], function () {
  Route::get('/', [ModulesManagerController::class, 'list']);
  Route::post('/install', [ModulesManagerController::class, 'install']);
  Route::post('/{module}/toggle_module_status', [ModulesManagerController::class, 'toggleModuleStatus']);
  Route::get('/{module}/thumbnail', [ModulesManagerController::class, 'moduleThumbnail']);
});
