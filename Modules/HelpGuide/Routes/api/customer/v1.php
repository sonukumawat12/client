<?php

use Modules\HelpGuide\Http\Controllers\MyAccount\TicketsController;
use Modules\HelpGuide\Http\Controllers\MyAccount\TicketConversationController;

$middleware = ['role:customer|non-restricted_agent|agent|admin|super_admin'];

if((boolean)setting('verify_email', true) === true){
    $middleware[] = 'verified';
}

Route::group(['middleware' => 'frontend'], function () {

    // Ticket routes
    Route::group([ 'prefix' => 'tickets'], function () {

        Route::get('/', [TicketsController::class, 'list']);
        Route::get('/categories', [TicketsController::class, 'categories']);
        Route::get('/{id}', [TicketsController::class, 'view']);
        Route::get('/{id}/conversation', [TicketConversationController::class,'fetch']);

        Route::post('/save', [TicketsController::class, 'save']);
        Route::post('/{ticket}/conversation/add', [TicketConversationController::class, 'store']);

        // // View ticket
        // Route::group([ 'middleware' => 'permission:view_ticket'], function () {

        //     // Fetch ticket, json response
        //     Route::post('/fetch', "MyAccount\TicketsController@fetch");

        //     // Add new ticket reply
        //     Route::post('/conversation/add','MyAccount\TicketConversationController@store');

        //     // View single ticket
        //     Route::get('/{id}', "MyAccount\TicketsController@view")->name("my_account.tickets.view");

        //     // get ticket details
        //     Route::post('/{id}', "MyAccount\TicketsController@details");

        //     // Get ticket conversation
        //     Route::get('/{id}/conversation', 'MyAccount\TicketConversationController@fetch');

        // });
    });

});

?>
