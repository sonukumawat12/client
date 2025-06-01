<?php

namespace Modules\HelpGuide\Http\Controllers\MyAccount;

use Modules\HelpGuide\Entities\User;
use Modules\HelpGuide\Entities\Ticket;
use Modules\HelpGuide\Entities\TicketConversation;
use Modules\HelpGuide\Events\TicketClosed;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Http\Requests\Customer\UpdateTicketRequest;
use Modules\HelpGuide\Http\Resources\TicketConversation as TicketConversationResource;

class TicketConversationController extends Controller
{
    public function fetch($ticket_id)
    {
        $ticketConversation = TicketConversation::orderBy('id', 'desc');
        $ticketConversation = $ticketConversation->where('ticket_id', '=', $ticket_id);
        $ticketConversation = $ticketConversation->paginate(50);

        return TicketConversationResource::collection($ticketConversation);
    }

    public function store(UpdateTicketRequest $request, Ticket $ticket)
    {
        $user = Auth::user();
        $ticketConversation = new TicketConversation;
        $ticketConversation->user_id = $user->id;
        $ticketConversation->ticket_id = $ticket->id;

        if($request->status && $ticket->status != $request->status){
            $ticket->status = $request->status;
            if( $ticket->save() && $request->status == 'closed'){
              TicketClosed::dispatch( $ticket );
            }
        }
        
        if(!$request->content){
          return ['message' => __('Ticket has been updated.')];
        }

        $ticketConversation->content = $request->content;

        if ($ticketConversation->save()) {
          
            if($ticket->user_id == $user->id ){
                $notifiable = $ticket->assigned_to;
                $notificationLink = route('dashboard.ticket.single', ['id' => $ticket->id]);
            } else {
                $notifiable = $ticket->user_id;
                $notificationLink = route('my_account.tickets.single', ['id' => $ticket->id]);
            }
            
            try {
                User::find($notifiable)->notify(new \Modules\HelpGuide\Entities\Notifications\NewTicketReply($ticket, $user, $notificationLink));
            } catch(\Exception $e){
                report($e);
            }

            return [
              'message' => __('Ticket has been updated.'),
              'data' => new TicketConversationResource($ticketConversation)
            ];
        }
    }
}
