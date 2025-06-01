<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    protected $table = 'shipping_drivers';
}
