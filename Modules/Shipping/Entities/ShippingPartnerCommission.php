<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;

class ShippingPartnerCommission extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    protected $table = 'shipping_partner_commission';
}
