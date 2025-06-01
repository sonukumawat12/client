<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingPrefix extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'created_by', 'added_date', 'prefix','starting_no','shipping_mode','status'];
    
    protected $table = 'shipping_prefix';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
