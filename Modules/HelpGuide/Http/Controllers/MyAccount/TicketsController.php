<?php

namespace Modules\HelpGuide\Http\Controllers\MyAccount;

use Modules\HelpGuide\Entities\User;
use Modules\HelpGuide\Entities\Ticket;

use Modules\HelpGuide\Entities\Category;
use Carbon\Carbon;
use Modules\HelpGuide\Entities\TicketConversation;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Http\Requests\Customer\CreateTicketRequest;
use Modules\HelpGuide\Http\Resources\MyAccount\Ticket as TicketResource;

class TicketsController extends Controller
{
    public function view(Request $request, $id)
    {
        $ticket = Ticket::where('user_id', Auth::id())
        ->select(['id','user_id', 'priority','title','status','category_id','assigned_to','created_at','updated_at'])
        ->with('user:id,first_name','category:id,name','assignedTo:id,first_name')
        ->findOrFail($id);

        return new TicketResource($ticket);
    }

    public function categories()
    {
        return collect(Ticket::categories());
    }

    public function details(Request $request, $id)
    {
        $ticket = Ticket::where('user_id', Auth::id())->select(['id','user_id', 'priority','title','status','category_id','assigned_to','created_at','updated_at'])
        ->with('user:id,first_name','category:id,name','assignedTo:id,first_name')
        ->findOrFail($id);

        return new TicketResource($ticket);
    }

    public function save(CreateTicketRequest $request)
    {
        $ticket = new Ticket;
        $assignTo = Ticket::assignTo();
        $user = Auth::User();

        if(!$assignTo){
            return response()->json([
                'status' => 'fail',
                'message' => __('Can not create a ticket, no agent available')
            ]);
        }

        $ticket->user_id = $user->id;
        $ticket->title = $request->input('title');
        $ticket->category_id = $request->input('category');
        $ticket->status = "open";
        $ticket->assigned_to = $assignTo;
        $ticket->priority = $request->input('priority');

        if($ticket->save()) {
            $ticketConversation = new TicketConversation;
            $ticketConversation->user_id = $user->id;
            $ticketConversation->ticket_id = $ticket->id;
            $ticketConversation->content = $request->input('content');
            if(!$ticketConversation->save()){
                return response()->json(['status' => 'fail', 'message' => __('Failed to submit your ticket, please try again')]);
            }

            // Save custom fields
            $customFields = customFields('ticket', 'create_ticket');
            $inputCustomFields = (array)$request->input('custom_fields');
            foreach($customFields as $field){
                if( $inputCustomFields[$field['key']] ){
                    $ticket->updateMeta($field['key'], $inputCustomFields[$field['key']] );
                }
            }

            // Attached the media if exists
            $attachments = (array)json_decode($request->input('attachments'), true);
            $ticketAttachs = [];

            foreach ($attachments as $file) {
                $exists = Storage::disk('ticket')->exists( $file );
                if ( $exists ){

                    if(defaultSetting('disk_ticket_driver') == 's3'){
                        $ticket->addMediaFromUrl(  Storage::disk('ticket')->url( $file ) )->toMediaCollection('attachments', 'ticket');
                    } else {
                        $ticket->addMedia(  Storage::disk('ticket')->path( $file ) )->toMediaCollection('attachments', 'ticket');
                    }

                    // Delete tmp files after adding it to the collection, avoid duplicate files
                    Storage::disk('ticket')->delete( $file );
                }
            }

            if($ticket->user_id == $user->id ){
                $notifiable = $ticket->assigned_to;
                $notificationLink = route('dashboard.ticket.single', ['id' => $ticket->id]);
            } else {
                $notifiable = $ticket->user_id;
                $notificationLink = route('my_account.tickets.single', ['id' => $ticket->id]);
            }

            try{
                User::find($ticket->assigned_to)
                ->notify(new \Modules\HelpGuide\Entities\Notifications\NewTicket($ticket, Auth::user(), $notificationLink));
            }
            catch(\Exception $e){
                report($e);
            }

            return response()->json([
                'status' => 'ok',
                'message' => __('Ticket has been sent'),
                'id' => $ticket->id,
            ]);
        }

        return response()->json(['status' => 'fail','message' => __('Failed to save data')]);
    }

    public function list(Request $request)
    {
        $ticket = Ticket::where('tickets.user_id', Auth::id());

        if( in_array($request->input('status'), ['open', 'resolved', 'closed']) ){
            $ticket->where('status', $request->input('status'));
        }

        $ticket->distinct('tickets.id')
            ->select([
                'tickets.id',
                'tickets.user_id',
                'tickets.title',
                'tickets.priority',
                'tickets.status',
                'tickets.category_id',
                'tickets.assigned_to',
                'tickets.created_at',
                'tickets.updated_at'
            ])
            ->with('user:id,first_name','category:id,name','assignedTo:id,first_name')
            ->leftJoin('ticket_conversation','ticket_conversation.ticket_id', '=', 'tickets.id')
            ->orderBy('ticket_conversation.created_at', 'desc');

        $ticket = $ticket->paginate(50, ['tickets.id']);

        return TicketResource::collection($ticket);
    }
}
