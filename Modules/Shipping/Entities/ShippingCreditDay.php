<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingCreditDay extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'created_by', 'added_date', 'credit_days','status'];
    
    protected $table = 'shipping_credit_days';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
