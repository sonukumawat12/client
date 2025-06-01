<?php

namespace Modules\Vat\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class VatCustomerStatement extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;
    
    protected static $logName = 'VatCustomerStatementDetail'; 

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = ['id'];
    
     public function details()
    {
        return $this->hasMany(\Modules\Vat\Entities\VatCustomerStatementDetail::class, 'statement_id', 'id');
    }

    public function location()
    {
        return $this->hasOne(\App\BusinessLocation::class, 'id', 'location_id');
    }

    public function user()
    {
        return $this->hasOne(\App\User::class, 'id', 'added_by');
    }

    public function contact()
    {
        return $this->hasOne(\App\Contact::class, 'id', 'customer_id');
    }

    public function amount()
    {
        return $this->details->reduce(function ($carry, $item) {
            return $carry + $item->invoice_amount;
        });
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }
}
