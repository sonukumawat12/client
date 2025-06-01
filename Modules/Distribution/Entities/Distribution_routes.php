<?php

namespace Modules\Distribution\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Distribution\Entities\Distribution_provinces;
use Modules\Distribution\Entities\Distribution_districts;
use Modules\Distribution\Entities\Distribution_areas;

class Distribution_routes extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    public function area()
    {
        return $this->belongsTo(Distribution_areas::class);
    }
    
    public function district()
    {
        return $this->belongsTo(Distribution_districts::class);
    }
    
    public function province()
    {
        return $this->belongsTo(Distribution_provinces::class);
    }
    
}
