<?php

namespace Modules\Dsr\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BusinessDsrOfficer extends Model
{
    use HasFactory;

    protected $fillable = [
        'business_ids',
        'dsr_officer_id'
    ];

}
