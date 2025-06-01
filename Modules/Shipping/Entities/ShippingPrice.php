<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingPrice extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'created_by', 'added_date', 'shipping_package', 'shipping_partner','constant_value','per_kg','shipping_mode','status','fixed_price'];
    
    protected $table = 'shipping_prices';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
