<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;

class ShippingPartner extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    protected $table = 'shipping_partners';
}
