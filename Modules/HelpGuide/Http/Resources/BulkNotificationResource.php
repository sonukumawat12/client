<?php

namespace Modules\HelpGuide\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class BulkNotificationResource extends JsonResource
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
            'subject' => $this->subject,
            'body' => $this->body,
            'notify_by' => ucfirst(str_replace('_',' ', $this->notify_by)),
            'contacts' => $this->getContacts(),
            'created_at' => date('d/m/Y H:i',strtotime($this->created_at)),
        ];
    }
}
