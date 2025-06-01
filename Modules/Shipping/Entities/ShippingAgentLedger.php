<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;

class ShippingAgentLedger extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    protected $table = 'shipping_agent_ledger';
}
