<?php

namespace Modules\HelpGuide\Http\Controllers\Dashboard;

use Modules\HelpGuide\Entities\User;
use Modules\HelpGuide\Entities\Ticket;
use Modules\HelpGuide\Entities\TicketConversation;
use Illuminate\Http\Request;
use Modules\HelpGuide\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Modules\HelpGuide\Http\Resources\Ticket as TicketResource;
use Modules\HelpGuide\Http\Requests\Dashboard\ReAssignTicketRequest;

class TicketController extends Controller
{
    public function index()
    {
        return view('helpguide::dashboard.ticket.list');
    }
    
    public function list(Request $request)
    {
        $tickets = Ticket::distinct('tickets.id')
            ->select([
                'tickets.id',
                'tickets.user_id',
                'tickets.title',
                'tickets.priority',
                'tickets.status',
                'tickets.category_id',
                'tickets.assigned_to',
                'tickets.created_at',
                'tickets.updated_at',
                "ticket_conversation.created_at as 'ticket_conversation_created_at'"
            ])
            ->with('user:id,name,avatar','category:id,name','assignedTo:id,name')
            ->leftJoin('ticket_conversation','ticket_conversation.ticket_id', '=', 'tickets.id')
            ->orderBy('tickets.id', 'desc')
            ->groupBy('tickets.id');

            if( Auth::user()->can('view_any_ticket') ){
                $tickets->withoutGlobalScope('own_ticket');
            }

            if($request->input('category')){
                $tickets->where('category_id', (int)$request->input('category'));
            }

            if( $request->input('status') && $request->input('status') != 'all'){
              $tickets->where('status', $request->input('status'));
            }

            $tickets = $tickets->simplePaginate(10, ['tickets.id'])->withQueryString();
   
        return TicketResource::collection($tickets);
    }
    
    public function show($id, Request $request)
    {
        $ticket = Ticket::withoutGlobalScope('own_ticket')->findOrFail($id);
        // $this->authorize('view', $ticket);
        if($request->isMethod('get')){
            return view('helpguide::dashboard.ticket.index', ['ticket' => $ticket]);
        }
        return new TicketResource($ticket);
    }

    public function update($id, Request $request)
    {
        $ticket = Ticket::withoutGlobalScope('own_ticket')->findOrFail($id);
        // $this->authorize('update', $ticket);
        $ticket->category_id = $request->input('category_id');
        if ($ticket->save()) {return new TicketResource($ticket);}
    }

    public function reAssign(ReAssignTicketRequest $request, $ticket)
    {
        $ticket = Ticket::withoutGlobalScope('own_ticket')->findOrFail($ticket);

        $ticket->assigned_to = $request->assign_to;

        if ( $ticket->save() ) {
            
           $notificationLink = route('dashboard.ticket.single', ['id' => $ticket->id]);
           
            try {
                User::find($ticket->assigned_to)
                    ->notify(new \Modules\HelpGuide\Notifications\TicketAssigned($ticket, Auth::User(), $notificationLink));
            } catch(\Exception $e){
                report($e);
            }

            return [
                'message' => __('Ticket has been transferred'),
                'data' => new TicketResource($ticket)
            ];
        }

        return Response::json([
            'status' => 'fail',
            'message' => __('Oops! Something went wrong, please try again')
        ], 500);
    }

    public function destroy($id, Request $request)
    {
        $ticket = Ticket::withoutGlobalScope('own_ticket')->findOrFail($id);
        // $this->authorize('delete', $ticket);
        if ($ticket->delete()) {
            return ['status' => 'ok', 'message' => __(':record deleted successfully', ['record' => __('Ticket')])];
        }
        return ['status' => 'fail', 'message' => __('Deletion failed, please try again')];
    }
}