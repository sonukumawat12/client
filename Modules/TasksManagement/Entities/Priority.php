<?php

namespace Modules\TasksManagement\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Priority extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Priority'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }
}
