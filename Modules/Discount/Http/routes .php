<?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\Chequer\ChequeWriteController;
    use Modules\Discount\Http\Controllers\NewdiscountController;
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

Route::group(['middleware' => 'IsSubscribed:discount_module'], function () {
        Route::delete('/store', [NewdiscountController::class, 'store']);
        Route::delete('/get_discounts', [NewdiscountController::class, 'getdiscounts']);
           
   });
        
    