<?php

namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;

class PumpOperatorOtherSale extends Model
{
     /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    protected $fillable = [
        'business_id',
        'store_id',
        'product_id',
        'price',
        'qty',
        'balance_stock',
        'discount',
        'sub_total',
        'shift_id',
        'created_at',
        'updated_at'
    ];
}
