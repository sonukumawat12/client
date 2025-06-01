<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingStatus extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'created_by', 'added_date', 'shipping_status','status'];
    
    protected $table = 'shipping_status';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
