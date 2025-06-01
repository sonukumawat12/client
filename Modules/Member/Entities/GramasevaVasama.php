<?php

namespace Modules\Member\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class GramasevaVasama extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Gramaseva Vasama'; 

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

    /**
     * Get the electrorate associated with
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function electrorate()
    {
        return $this->hasOne(Electrorate::class, 'id', 'electrorate_id');
    }
}
