<?php

namespace Modules\MPCS\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class FormF15Header extends Model
{
    use LogsActivity;
    
    protected $table = 'mpcs_form_f15_headers'; 
    
    protected $fillable = [
        'business_id', 
        'dated_at', 
        'created_by', 
        'created_at', 
        'updated_at'
    ];

    public function fsetting()
    {
        return $this->hasMany(Mpcs15FormSettings::class, 'id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }
}
