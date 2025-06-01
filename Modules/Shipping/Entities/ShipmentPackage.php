<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;

class ShipmentPackage extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    protected $table = 'shipment_packages';
}
