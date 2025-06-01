<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingChangeStatus extends Model
{
    use HasFactory;

    protected $fillable = ['shipping_id', 'created_by', 'type_change','shipping_delivery','new_status','delivery_time','prev_status'];
    
    protected $table = 'shipping_change_status';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
