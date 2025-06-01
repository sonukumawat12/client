<?php

namespace Modules\Dsr\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Province extends Model
{
    use HasFactory;

    protected $fillable = ['country_id','name'];

    protected static function newFactory()
    {
        return \Modules\Dsr\Database\factories\ProvinceFactory::new();
    }
}
