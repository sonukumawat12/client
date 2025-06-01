<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ShippingInvoiceSend extends Model
{
    use HasFactory;

    protected $fillable = ['business_id', 'created_by', 'shipment_id','whatsapp_number','email_id'];
    
    protected $table = 'shipping_invoice_send';

    protected static function newFactory()
    {
        return \Modules\Shipping\Database\factories\TypeFactory::new();
    }
}
