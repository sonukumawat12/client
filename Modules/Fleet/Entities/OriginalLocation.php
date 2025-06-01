<?php

namespace Modules\Fleet\Entities;

use Illuminate\Database\Eloquent\Model;

class OriginalLocation extends Model
{
    protected $fillable = [];
    
    protected $table = 'fleet_original_locations';

    protected $guarded  = ['id'];
}
