<?php

namespace Modules\HelpGuide\Http\Resources\MyAccount;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\HelpGuide\Http\Resources\User as UserResource;

use Illuminate\Http\Resources\Json\JsonResource;

use Modules\HelpGuide\Http\Resources\Category as CategoryResource;
use Modules\HelpGuide\Http\Resources\TicketConversation as TicketConversationResource;

class Ticket extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $latestReply = $this->latestReply;
        
        $latestReplyContent = "";
        $hasReply = false;
        $last_reply_on = null;

        if( $latestReply ) {
            $latestReplyContent = e(strip_tags(html_entity_decode($latestReply->content)));
            $hasReply = $latestReply->user_id == Auth::user()->id ? false : true;
            $last_reply_on = Carbon::parse($this->latestReply->created_at)->diffForHumans();
        }

        return [
            'id' => $this->id,
            'status' => $this->status,
            'title' => $this->title,
            'user' => $this->user,
            'assigned_to' => $this->assignedTo,
            'category' => $this->category,
            'last_reply' => $latestReplyContent,
            'last_reply_on' => $last_reply_on,
            'has_reply' => $hasReply,
            'priority' => $this->priority,
            'attachments' => $this->attachments(),
            'submitted_on' => Carbon::parse($this->created_at)->format(setting('date_format'))." - ".Carbon::parse($this->created_at)->diffForHumans() ,
            'updated_at' => $this->updated_at,
        ];
    }
}
