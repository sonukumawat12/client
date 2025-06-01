<?php

namespace Modules\HelpGuide\Http\Resources;

use Carbon\Carbon;
use Carbon\CarbonInterface;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerPurchaseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $supportPeroid = "expired";

        $supportedUntil = new Carbon($this->supported_until);
        $now = Carbon::now();

        if($now->diffInMinutes($supportedUntil, false) > 0){
            $supportPeroid = $supportedUntil->diffForHumans($now, ['syntax' => CarbonInterface::DIFF_RELATIVE_TO_NOW]);
        }

        return [
            'id' => $this->id,
            'amount' => $this->amount." USD",
            'buyer' => $this->buyer,
            'sold_at' => Carbon::parse($this->sold_at)->format(setting('date_format'))." - ".Carbon::parse($this->sold_at)->diffForHumans(),
            'item_id' => $this->item_id,
            'item_name' => $this->item_name,
            'item_icon' => $this->item_icon,
            'license' => $this->license,
            'purchase_code' => $this->purchase_code,
            'support_amount' => $this->support_amount." USD",
            'supported_until' => Carbon::parse($this->supported_until)->format(setting('date_format')),
            'supported_peroid' => $supportPeroid,
            'updated_at' => $this->updated_at,
        ];
    }
}
