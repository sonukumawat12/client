<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingDimension extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'created_by', 'added_date', 'dimension_no','dimension_type','weight','length','width','height','status'];
    
    protected $table = 'shipping_dimensions';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
