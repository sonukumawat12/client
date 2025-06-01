<?php

namespace Modules\HelpGuide\Http\Resources;

use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Modules\HelpGuide\Http\Resources\User as UserResource;
use Modules\HelpGuide\Http\Resources\Ticket as TicketResource;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketConversation extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'content' => $this->content,
            'user' => new UserResource($this->user),
            'attachments' => $this->attachments(),
            'is_owner' => $this->user_id == Auth::id(),
            'created_at' =>  Carbon::parse($this->created_at)->format(setting('date_format'))." - ".Carbon::parse($this->created_at)->diffForHumans() 
        ];
    }
}
