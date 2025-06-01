<?php

namespace Modules\Bakery\Entities;

use Illuminate\Database\Eloquent\Model;
use Modules\Bakery\Entities\BakeryOpeningBalance;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BakeryFleet extends Model
{
    protected $fillable = [];

    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Bakery';

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
    
     public function balanceDetail()
    {
        return $this->hasMany(BakeryOpeningBalance::class, 'fleets_id', 'id');
    }
}
