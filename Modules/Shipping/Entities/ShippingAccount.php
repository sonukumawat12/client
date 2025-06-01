<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;

class ShippingAccount extends Model
{
    protected $fillable = [];
    
    protected $table = "shipping_accounts";

    protected $guarded  = ['id'];
}
