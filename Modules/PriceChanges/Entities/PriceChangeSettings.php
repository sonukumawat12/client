<?php

namespace Modules\PriceChanges\Entities;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Account;

class PriceChangeSettings extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

    
    protected static $logName = 'Price Change Settings'; 
    
    protected $table = 'price_change_settings';
    
    protected $fillable = [
            'gain_account_id',
            'loss_account_id',
            'date',
            'business_id',
            'user'
        ];

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
    
    public function gainAccount() {
        return $this->belongsTo(Account::class,'gain_account_id');
    }
    
    public function lossAccount() {
        return $this->belongsTo(Account::class,'loss_account_id');
    }
}
