<?php

namespace Modules\HelpGuide\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;

class User extends JsonResource
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
            'email' => isDemo() ? '** Hidden **' : $this->email,
            'name' => $this->name,
            'roles' => $this->roles ? $this->roles->pluck('name', 'id') : collect(),
            'notes' => $this->notes,
            'email_verified' => $this->email_verified_at ? true : false,
            'created_at' => Carbon::parse($this->created_at)->format(setting('date_format')) . ' - ' . Carbon::parse($this->created_at)->diffForHumans(),
            'last_login_at' =>  $this->last_login_at ? Carbon::parse($this->last_login_at)->diffForHumans() : "-",
            'avatar' => $this->avatar(),
            'total_tickets' => $this->total_tickets,
            'signature' => $this->signature,
            'envato_customer' => $this->envatoCustomer,
            'purchase_count' => $this->purchaseCount(),
            'custom_fields' => $this->metas->pluck('value', 'key'),
            'language' => getLocaleName($this->locale)
        ];
    }
}
