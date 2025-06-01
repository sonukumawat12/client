<?php

namespace Modules\Bakery\Entities;

use Illuminate\Database\Eloquent\Model;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BakeryProduct extends Model
{
    protected $fillable = [];

    use LogsActivity;

    protected static $logAttributes = ['*']; // This tells the package to log all attributes
    protected static $logFillable = true; // This tells the package to include all attributes in the log

    protected static $logName = 'Bakery';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id']; // Ensure 'id' is protected

    // Optionally, if you want to include the specific attributes to log
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable(); // Logs all fillable attributes
    }
}
