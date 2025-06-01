<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingDelivery extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'created_by', 'added_date', 'shipping_delivery','status'];
    
    protected $table = 'shipping_delivery';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
