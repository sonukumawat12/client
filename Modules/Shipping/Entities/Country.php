<?php

namespace Modules\Shipping\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Country extends Model
{
    use HasFactory;

    protected $fillable = ['country', 'country_code', 'currency_code'];

    protected static function newFactory()
    {
        return \Modules\Dsr\Database\factories\CountryFactory::new();
    }
}
