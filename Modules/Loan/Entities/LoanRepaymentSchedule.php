<?php

namespace Modules\Loan\Entities;

use Illuminate\Database\Eloquent\Model;

class LoanRepaymentSchedule extends Model
{
    protected $fillable = ['principal_repaid_derived', 'fees_repaid_derived', 'interest_repaid_derived', 'penalties_repaid_derived'];

    public $table = "loan_repayment_schedules";

    public function loan()
    {
        return $this->hasOne(Loan::class, 'id', 'loan_id');
    }

    public function getTotalPrincipalAttribute()
    {
        return $this->principal - $this->principal_waived_derived - $this->principal_written_off_derived;
    }

    public function getTotalFeesAttribute()
    {
        return $this->fees - $this->fees_written_off_derived - $this->fees_waived_derived;
    }

    public function getTotalPenaltiesAttribute()
    {
        return $this->penalties - $this->penalties_written_off_derived - $this->penalties_waived_derived;
    }

    public function getTotalInterestAttribute()
    {
        return $this->interest - $this->interest_waived_derived - $this->interest_written_off_derived;
    }

    public function getPrincipalDueAttribute()
    {
        return $this->total_principal - $this->principal_repaid_derived;
    }

    public function getFeesDueAttribute()
    {
        return $this->total_fees - $this->fees_repaid_derived;
    }

    public function getPenaltiesDueAttribute()
    {
        return $this->total_penalties - $this->penalties_repaid_derived;
    }

    public function getInterestDueAttribute()
    {
        return $this->total_interest - $this->interest_repaid_derived;
    }

    public function getAmountDueAttribute()
    {
        return $this->total_principal + $this->total_interest + $this->total_fees + $this->total_penalties;
    }

    public function getTotalPaidAttribute()
    {
        return $this->principal_repaid_derived + $this->fees_repaid_derived + $this->penalties_repaid_derived + $this->interest_repaid_derived;
    }

    public function getBalanceDueAttribute()
    {
        return $this->amount_due - $this->total_paid;
    }

    public function getIsOverdueAttribute()
    {
        return $this->due_date < date('Y-m-d') && $this->total_due > 0;
    }

    public function getTotalOverdueAttribute()
    {
        return $this->is_overdue ? $this->total_due : 0;
    }

    /**
     * This captures the installments that are due i.e intsallments that have a due date 
     * before or on the current day 
     * This is used to calculate the amount expected to be repaid by a certain date   
     * as in the pro rata pending dues (loan show page > Pending Dues Tab)
     */
    public function scopeCurrentDue($query)
    {
        return $query->where('due_date', '<=', date('Y-m-d'));
    }

    public function scopeUnpaid($query)
    {
        return $query->where('total_due', '>', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('total_due', 0);
    }

    /**
     * Returns all installments that have reached or passed the due date
     */
    public function scopeDue($query)
    {
        return $query->where('due_date', '<=', date('Y-m-d'));
    }

    /**
     * Returns all unpaid installments past the due date 
     */
    public function scopeOverdue($query)
    {
        return $query->unpaid()->where('due_date', '<', date('Y-m-d'));
    }

    public function scopeTimelyPayments($query)
    {
        return $query->paid()->whereRaw('paid_by_date <= due_date');
    }

    public function scopeLatePayments($query)
    {
        return $query->paid()->whereRaw('paid_by_date > due_date');
    }
}
