<?php

namespace Modules\Loan\Entities;

use App\BusinessLocation;
use Illuminate\Database\Eloquent\Model;
use App\PaymentDetail;
use App\User;

class LoanTransaction extends Model
{
    protected $fillable = [];
    public $table = "loan_transactions";

    public function payment_detail()
    {
        return $this->hasOne(PaymentDetail::class, 'id', 'payment_detail_id')->withDefault();
    }

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id')->withDefault();
    }

    public function business_location()
    {
        return $this->hasOne(BusinessLocation::class, 'id', 'location_id')->withDefault();
    }

    public function created_by()
    {
        return $this->hasOne(User::class, 'id', 'created_by_id')->withDefault();
    }

    public function scopeRecent($query)
    {
        return $query->with('loan.contact')
            ->forBusiness()
            ->orderBy('loan_transactions.created_at', 'DESC')
            ->limit(5);
    }

    public function scopeForBusiness($query)
    {
        return $query->whereHas('business_location', function ($q) {
            $q->where('business_locations.business_id', session('business.id'));
        });
    }

    public function scopeForContact($query, $contact_id)
    {
        return $query->whereHas('loan', function ($q) use ($contact_id) {
            return $q->where('loans.contact_id', $contact_id);
        });
    }

    public function getLoanTransactionTypeAttribute()
    {
        $loan_transaction_types = [
            1 => trans_choice('loan::general.disbursement', 1),
            2 => trans_choice('loan::general.repayment', 1),
            3 => trans_choice('loan::general.contra', 1),
            4 => trans_choice('loan::general.waive', 1) . ' ' . trans_choice('loan::general.interest', 1),
            5 => trans_choice('loan::general.repayment', 1) . ' ' . trans_choice('accounting::core.at', 1) . ' ' . trans_choice('loan::general.disbursement', 1),
            6 => trans_choice('loan::general.write_off', 1),
            7 => trans_choice('loan::general.marked_for_rescheduling', 1),
            8 => trans_choice('loan::general.recovery', 1) . ' ' . trans_choice('loan::general.repayment', 1),
            9 => trans_choice('loan::general.waive', 1) . ' ' . trans_choice('loan::general.fee', 2),
            10 => trans_choice('loan::general.apply', 1) . ' ' . trans_choice('loan::general.fee', 2),
            11 => trans_choice('loan::general.apply', 1) . ' ' . trans_choice('loan::general.interest', 1),
            12 => trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.top_up', 1),
            13 => trans_choice('loan::general.closed', 1)
        ];

        return $loan_transaction_types[$this->loan_transaction_type_id];
    }

    public function scopeRepayment($query)
    {
        return $query->where('name', 'Repayment');
    }
}
