<?php
namespace Modules\Petro\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class PetroDailyShift extends Model
{
    use LogsActivity;

    // Add this line to specify your actual table name
    protected $table = 'petro_daily_shifts';

    protected static $logAttributes = ['*'];
    protected static $logFillable = true;
    protected static $logName = 'Petro Daily Shift';
    
    protected $guarded = ['id'];
    
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }
}