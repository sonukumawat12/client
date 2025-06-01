<?php

namespace Modules\Dsr\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DesignatedDsrOfficer extends Model
{
    use HasFactory;

    protected $table = 'designated_dsr_officers';

    protected $fillable = [
        'name',
        'fuel_provider_id',
        'country_id',
        'province_id',
        'district_id',
        'areas',
        'officer_name',
        'officer_mobile',
        'officer_username',
        'officer_password',
        'business_id'
    ];


    protected static function newFactory()
    {
        return \Modules\Dsr\Database\factories\DesignatedDsrOfficerFactory::new();
    }
}
