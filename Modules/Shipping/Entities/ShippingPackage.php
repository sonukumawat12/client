<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingPackage extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'created_by', 'added_date', 'package_name', 'package_details','status'];
    
    protected $table = 'shipping_packages';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
