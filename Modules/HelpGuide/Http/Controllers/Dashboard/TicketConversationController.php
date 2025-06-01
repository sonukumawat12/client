<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\User;
use Modules\HelpGuide\Entities\Ticket;
use Modules\HelpGuide\Entities\TicketConversation;
use Modules\HelpGuide\Events\TicketClosed;
use Illuminate\Http\Request;
use Modules\HelpGuide\Events\AgentRepliedTicket;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Event;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Http\Resources\TicketConversation as TicketConversationResource;

class TicketConversationController extends Controller
{
    public function fetch($ticket_id)
    {
        $ticket = Ticket::withoutGlobalScope('own_ticket')->findOrFail($ticket_id);
        // $this->authorize('view', $ticket);
        $ticketConversation = TicketConversation::orderBy('id', 'desc');
        $ticketConversation = $ticketConversation->where('ticket_id', '=', $ticket_id);
        $ticketConversation = $ticketConversation->paginate(100);

        return TicketConversationResource::collection($ticketConversation);
    }

    public function store(Request $request)
    {
        $rules = [];
        $rulesMsg = [];

        $rules['ticket_id'] =  ['required','exists:tickets,id'];
        $rulesMsg['ticket_id.required'] = __("Ticket not specified");
        $rulesMsg['ticket_id.exists'] = __("Ticket not exists or has been deleted");

        $validatedData = Validator::make($request->all(), $rules, $rulesMsg);

        if ($validatedData->fails()) {
            return ['status' => 'fail', "message" => $validatedData->errors()];
        }

        $ticket = Ticket::withoutGlobalScope('own_ticket')->findOrFail($request->ticket_id);
       
        // $this->authorize('createReply', $ticket);

        $ticketConversation = new TicketConversation;
        $ticketConversation->user_id = Auth::id();
        $ticketConversation->ticket_id = $request->input('ticket_id');

        if($request->input('status') && $ticket->status != $request->input('status')){
            $ticket->status = $request->input('status');
            if( $ticket->save() && $request->input('status') == 'closed'){
                Event::dispatch(new TicketClosed($ticket));
            }
        }
        
        if(!$request->content){
            return response()->json([
                'status' => 'ok',
                'message' => __('Ticket has been updated.')
            ]);
        }

        $ticketConversation->content = $request->content;

        if ($ticketConversation->save()) {

            if($ticket->user_id == Auth::id() ){
                $notifiable = $ticket->assigned_to;
                $notificationLink = route('dashboard');
            } else {
                $notifiable = $ticket->user_id;
                $notificationLink = route('my_account');
            }

            // Attach the media if exists
            $attachments = (array)json_decode($request->input('attachments'), true);
            $ticketAttachs = [];

            foreach ($attachments as $file) {
                $exists = Storage::disk('ticket_conversation')->exists( $file );
                if ( $exists ){

                    if(defaultSetting('disk_ticket_driver') == 's3'){
                        $ticketConversation->addMediaFromUrl(  Storage::disk('ticket_conversation')->url( $file ) )->toMediaCollection('ticket_conversation_attachments', 'ticket_conversation');
                    } else {
                        $ticketConversation->addMedia(  Storage::disk('ticket_conversation')->path( $file ) )->toMediaCollection('ticket_conversation_attachments', 'ticket_conversation');
                    }
                    
                    // Delete tmp files after adding it to the collection, avoid duplicate files
                    Storage::disk('ticket_conversation')->delete( $file );
                }
            }
            
            Event::dispatch(new AgentRepliedTicket($ticket));

            try {
                User::find($notifiable)->notify(new \Modules\HelpGuide\Notifications\NewTicketReply($ticket, Auth::User(), $notificationLink));
            } catch(\Exception $e){
                report($e);
            }
        
            return ['status' => 'ok', 'message' => __('Ticket has been updated.'), 'data' => new TicketConversationResource($ticketConversation)];
        }
    }

    public function updateReply(Request $request)
    {
        $ticketConversation = TicketConversation::findOrFail($request->reply_id);
        // $this->authorize('update', $ticketConversation);
        
        $validatedData = Validator::make($request->all(), [
            'content' => 'required'
        ]);

        if ($validatedData->fails()) {
            return Response::json([
                'errors' => $validatedData->errors()
            ], 422);
        }

        $ticketConversation->content = $request->content;

        if ( $ticketConversation->save() ) {
            return Response::json([
                'message' => __('Reply has been updated')
            ], 201);
        }

        return Response::json([
            'message' => __('Save reply failed. please try again')
        ], 406);
    }

    public function destroy(Request $request)
    {
        $ticketConversation = TicketConversation::findOrFail($request->reply_id);
        // $this->authorize('delete', $ticketConversation);
        if ($ticketConversation->delete()) {
            return ['status' => 'ok', 'message' => __(':record deleted successfully', ['record' => __('Reply')])];
        }
        return ['status' => 'fail', 'message' => __('Deletion failed, please try again')];
    }

}
