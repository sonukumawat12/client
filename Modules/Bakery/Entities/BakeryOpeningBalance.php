<?php

namespace Modules\Bakery\Entities;

use Illuminate\Database\Eloquent\Model;
use App\Contact;

use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class BakeryOpeningBalance extends Model
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
    
    protected $table = 'bakery_opening_balance';
    
    public function contact()
    {
        return $this->belongsTo(Contact::class, 'contact_id', 'id')->withDefault();
    }
}
