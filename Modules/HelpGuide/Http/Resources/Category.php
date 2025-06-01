<?php

namespace Modules\HelpGuide\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class Category extends JsonResource
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
            'name' => $this->name,
            'active' => $this->active,
            'is_featured' => $this->is_featured,
            'has_ticket' => $this->has_ticket,
            'parent' => $this->parent,
            'thumbnail' => $this->thumbnail,
            'tickets_count' => $this->countTickets(true),
            'articles_count' => $this->articles->count()
        ];
    }
}
