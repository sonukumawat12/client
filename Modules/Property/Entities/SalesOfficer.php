<?php

namespace Modules\Property\Entities;

use Illuminate\Database\Eloquent\Model;

class SalesOfficer extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    protected $table = "sales_officers";
}
