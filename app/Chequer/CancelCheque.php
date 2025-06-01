<?php

namespace App\Chequer;
use App\User;
use App\Account;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class CancelCheque extends Model
{
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;

   
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['fillable', 'some_other_attribute']);
    }
    protected static $logName = 'cancel_cheque'; 

    protected $guarded = ['id'];
  
    protected $table = 'cancel_cheque';

    public $timestamps = false;

    /**
     * Get the user that owns the CancelCheque
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
    
    /**
     * Get the chequeBookNo associated with the CancelCheque
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function chequeBookNo()
    {
        return $this->hasOne(ChequeNumber::class,'id','cheque_bk_id');
    }


}
