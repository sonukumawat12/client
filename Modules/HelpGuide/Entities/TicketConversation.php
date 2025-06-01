<?php

namespace Modules\HelpGuide\Entities;

use Illuminate\Database\Eloquent\Model;

class TicketConversation extends Model
{
    protected $table = "ticket_conversation";

    public function ticket()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\Ticket');
    }

    public function user()
    {
        return $this->belongsTo('Modules\HelpGuide\Entities\user');
    }

    public function attachments()
    {
        // $mediaItems = $this->getMedia('ticket_conversation_attachments');
        $mediaItems = [];
        $attachments = [];
        foreach($mediaItems  as $file){
            $attachments[] = array('url' => asset($file->getUrl()), 'file_type'=> $file->mime_type);
        }
        return $attachments;
    }
}
