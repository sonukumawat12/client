<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingDeliveryDays extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'created_by', 'added_date', 'shipping_partner','days','shipping_mode','status'];
    
    protected $table = 'shipping_delivery_days';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
