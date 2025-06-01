<?php

namespace Modules\EzyInvoice\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Ezyinvoice extends Model
{
    protected $fillable = [];

    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Pumps';

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
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'work_shift' => 'array'
    ];

    
    public function credit_sale_payments()
    {
        return $this->hasMany('\Modules\EzyInvoice\Entities\EzyinvoiceCreditSalePayment', 'invoice_no', 'id');
    }
    
}
