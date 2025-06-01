<?php

namespace Modules\Dsr\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class FuelProvider extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'address', 'phone', 'email', 'added_by', 'state', 'business_id'];

    protected static function newFactory()
    {
        return \Modules\Dsr\Database\factories\FuelProviderFactory::new();
    }

}
