<?php

namespace Modules\Fleet\Entities;

use Illuminate\Database\Eloquent\Model;

class Driver extends Model
{
    protected $fillable = [];

    protected $guarded = ['id'];
    
    // public function employee()
    // {
    //     return $this->belongsTo('Modules\HR\Entities\Employee', 'employee_id', 'id');
    // }
    
}
