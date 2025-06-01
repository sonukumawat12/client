<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingBarQrCode extends Model
{
    use HasFactory;

    protected $fillable = ['business_id','created_by', 'details', 'bar_code', 'qr_code'];
    
    protected $table = 'shipping_bar_qr_code';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
