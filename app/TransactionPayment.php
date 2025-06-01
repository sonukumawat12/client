<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionPayment extends Model
{
    use SoftDeletes;
    use LogsActivity;

    protected static $logAttributes = ['*'];

    protected static $logFillable = true;


    protected static $logName = 'Transaction Payment';

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
     * Get the phone record associated with the user.
     */
    public function payment_account()
    {
        return $this->belongsTo(\App\Account::class, 'account_id');
    }
    
    public function contact()
    {
        return $this->belongsTo(\App\Contact::class, 'payment_for');
    }

    /**
     * Get the transaction related to this payment.
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Transaction::class, 'transaction_id');
    }

    /**
     * Get the user.
     */
    public function created_user()
    {
        return $this->belongsTo(\App\User::class, 'created_by');
    }

    /**
     * Retrieves documents path if exists
     */
    public function getDocumentPathAttribute()
    {
        $path = !empty($this->document) ? asset('/uploads/documents/' . $this->document) : null;

        return $path;
    }

    /**
     * Removes timestamp from document name
     */
    public function getDocumentNameAttribute()
    {
        $document_name = !empty(explode("_", $this->document, 2)[1]) ? explode("_", $this->document, 2)[1] : $this->document;
        return $document_name;
    }


    /**
     * Get unique amounts customer_wise
     */
    public static  function get_customer_wise_unique_amounts($customer,$start_date = null, $end_date = null, $bank = null,$post_party_type = null){
        $result =  self::query()->whereHas('transaction',function($query) use($customer){
            $query->whereHas('contact',function($query) use($customer){
                 $query->where('id', $customer);
            })->where('amount','>',0);
        });
        
        if(!empty($start_date)){
            $result->whereDate('transaction_payments.paid_on','>=', $start_date);
        }
        
        if(!empty($end_date)){
            $result->whereDate('transaction_payments.paid_on','<=', $end_date);
        }
        
        if(!empty($bank)){
            $result->where('transaction_payments.account_id', $bank);
        }
        
        if(!empty($post_party_type)){
            
        }
        
        return $result->distinct()->groupBy('amount')->get();
    }

     /**
     * Get unique cheque numbers customer_wise
     */
    public static  function get_customer_wise_unique_cheque_no($customer, $start_date = null, $end_date = null, $bank = null,$post_party_type = null){
        $result =  self::query()->whereHas('transaction',function($query) use($customer){
            $query->whereHas('contact',function($query) use($customer){
                 $query->where('id', $customer);
            });
        })->where('cheque_number','!=',null);
        
        if(!empty($start_date)){
            $result->whereDate('transaction_payments.paid_on','>=', $start_date);
        }
        
        if(!empty($end_date)){
            $result->whereDate('transaction_payments.paid_on','<=', $end_date);
        }
        
        if(!empty($bank)){
            $result->where('transaction_payments.account_id', $bank);
        }
        
        if(!empty($post_party_type)){
            
        }
        
        return $result->distinct()->groupBy('cheque_number')->get();
    }
}
