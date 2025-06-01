<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;

class ShippingAgentCommission extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    protected $table = 'shipping_agent_commission';
}
