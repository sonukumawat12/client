<?php

namespace Modules\Dsr\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DsrSettings extends Model
{
    use HasFactory;

    protected $fillable = [
        'date_time',
        'country_id',
        'province_id',
        'district_id',
        'areas',
        'fuel_provider_id',
        'dealer_number',
        'dealer_name',
        'dsr_starting_number',
        'user_id',
        'business_id',
        'product_id',
        'accumulative_sale'
        ,'accumulative_purchase'
    ];

    protected static function newFactory()
    {
        return \Modules\Dsr\Database\factories\DsrSettingsFactory::new();
    }
}
