<?php

namespace Modules\Loan\Entities;

use App\Variation;
use App\BusinessLocation;
use App\Contact;
use App\User;
;
use Illuminate\Database\Eloquent\Model;
use App\Currency;

class Loan extends Model
{
    protected $fillable = [];

    public $table = "loans";

    public function charges()
    {
        return $this->hasMany(LoanLinkedCharge::class, 'loan_id', 'id');
    }

    public function contact()
    {
        return $this->hasOne(Contact::class, 'id', 'contact_id')->withDefault();
    }

    public function business_location()
    {
        return $this->hasOne(BusinessLocation::class, 'id', 'location_id')->withDefault();
    }

    public function loan_product()
    {
        return $this->hasOne(Product::class, 'id', 'loan_product_id')->withDefault();
    }

    public function currency()
    {
        return $this->hasOne(Currency::class, 'id', 'currency_id')->withDefault();
    }

    public function loan_officer()
    {
        return $this->hasOne(User::class, 'id', 'loan_officer_id')->withDefault();
    }

    public function loan_purpose()
    {
        return $this->hasOne(LoanPurpose::class, 'id', 'loan_purpose_id')->withDefault();
    }

    public function loan_transaction_processing_strategy()
    {
        return $this->hasOne(LoanTransactionProcessingStrategy::class, 'id', 'loan_transaction_processing_strategy_id')->withDefault();
    }

    public function submitted_by()
    {
        return $this->hasOne(User::class, 'id', 'submitted_by_user_id')->withDefault();
    }

    public function approved_by()
    {
        return $this->hasOne(User::class, 'id', 'approved_by_user_id')->withDefault();
    }

    public function disbursed_by()
    {
        return $this->hasOne(User::class, 'id', 'disbursed_by_user_id')->withDefault();
    }

    public function rejected_by()
    {
        return $this->hasOne(User::class, 'id', 'rejected_by_user_id')->withDefault();
    }

    public function written_off_by()
    {
        return $this->hasOne(User::class, 'id', 'written_off_by_user_id')->withDefault();
    }

    public function closed_by()
    {
        return $this->hasOne(User::class, 'id', 'closed_by_user_id')->withDefault();
    }

    public function withdrawn_by()
    {
        return $this->hasOne(User::class, 'id', 'withdrawn_by_user_id')->withDefault();
    }

    public function rescheduled_by()
    {
        return $this->hasOne(User::class, 'id', 'rescheduled_by_user_id')->withDefault();
    }

    public function files()
    {
        return $this->hasMany(LoanFile::class, 'loan_id', 'id');
    }

    public function collateral()
    {
        return $this->hasMany(LoanCollateral::class, 'loan_id', 'id');
    }

    public function notes()
    {
        return $this->hasMany(LoanNote::class, 'loan_id', 'id')->orderBy('created_at', 'desc');
    }

    public function repayment_schedules()
    {
        return $this->hasMany(LoanRepaymentSchedule::class, 'loan_id', 'id')->orderBy('due_date', 'asc');
    }

    /**
     * Includes repayment schedule only upto current month.
     * The dates when the current month begins is specified by the due date not necessarily the beginning of the month.
     */
    public function current_repayment_schedules()
    {
        return $this->repayment_schedules()->currentDue();
    }

    public function paid_repayment_schedules()
    {
        return $this->repayment_schedules()->paid();
    }

    public function unpaid_repayment_schedules()
    {
        return $this->repayment_schedules()->unpaid();
    }

    /**
     * Returns all installments that have reached or passed the due date
     */
    public function due_repayment_schedules()
    {
        return $this->repayment_schedules()->due();
    }

    /**
     * Returns all unpaid installments past the due date 
     */
    public function overdue_repayment_schedules()
    {
        return $this->repayment_schedules()->overdue();
    }

    public function timely_repayment_schedules()
    {
        return $this->repayment_schedules()->timelyPayments();
    }

    public function late_repayment_schedules()
    {
        return $this->repayment_schedules()->latePayments();
    }

    public function transactions()
    {
        return $this->hasMany(LoanTransaction::class, 'loan_id', 'id')->orderBy('submitted_on', 'asc')->orderBy('id', 'asc');
    }

    public function repayment_transactions()
    {
        return $this->transactions()->repayment();
    }

    public function repayments_due_today()
    {
        return $this->unpaid_repayment_schedules()->where('due_date', date('Y-m-d'));
    }

    public function approval_officers()
    {
        return $this->belongsToMany(User::class, 'loan_approval_officers', 'loan_id', 'user_id')->withPivot(['status']);
    }

    public function pending_approval()
    {
        return $this->approval_officers()->wherePivot('status', 'pending');
    }

    public function media()
    {
        return $this->morphMany(\App\Media::class, 'model');
    }

    public function variation()
    {
        return $this->hasOne(Variation::class, 'id', 'variation_id')->withDefault();
    }

    public function scopeForBusiness($query)
    {
        return $query->whereHas('business_location', function ($q) {
            $q->where('business_locations.business_id', session('business.id'));
        });
    }

    public function scopeForLocation($query, $location_id)
    {
        return $query->where('loans.location_id', $location_id);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeForContact($query, $contact_id)
    {
        return $query->where('loans.contact_id', $contact_id);
    }

    public function scopeForStatus($query, $status)
    {
        return ($status == 'pending_and_submitted') ?
            $query->whereIn("loans.status", ['pending', 'submitted']) :
            $query->where("loans.status", $status);
    }

    public function scopeForSavingsWithdrawal($query, $contact_id)
    {
        $query->with('repayment_schedules')
            ->with('current_repayment_schedules')
            ->where('contact_id', $contact_id)
            ->where('status', 'active')
            ->select('id')
            ->latest() //Get the latest loans (order by DESC)
            ->limit(10); //Limit to avoid longer loading times
    }

    public function scopeForFixedDepositWithdrawal($query, $contact_id)
    {
        $query->with('repayment_schedules')
            ->with('current_repayment_schedules')
            ->where('contact_id', $contact_id)
            ->where('status', 'active')
            ->select('id')
            ->latest() //Get the latest loans (order by DESC)
            ->limit(10); //Limit to avoid longer loading times
    }

    public function scopeAwaitingDisbursement($query)
    {
        $query->where('loans.status', 'approved');
    }

    public function scopeRejected($query)
    {
        $query->where('loans.status', 'rejected');
    }

    public static function getStatusData()
    {
        return Loan::select('status')->groupBy('status')->get();
    }

    public static function getLoanOfficerData()
    {
        return Loan::join('users', 'users.id', 'loans.loan_officer_id')
            ->selectRaw('users.id, CONCAT(users.first_name, " ", users.last_name) AS name')
            ->where('users.business_id', session('business.id'))
            ->groupBy('loans.loan_officer_id')
            ->orderBy('users.first_name')
            ->get();
    }

    public function getStatusLabelAttribute()
    {
        $statuses = Loan::getStatuses();

        $status_label = array_key_exists($this->status, $statuses) ? $statuses[$this->status]['label'] : '';

        $loan_standing = $this->loan_standing;
        if (!empty($loan_standing->status) && !empty($loan_standing->label_class) && $this->status == 'active') {
            $status_label .= ' ' . '
                <a href="#" class="label label-' . $loan_standing->label_class . '">' . $loan_standing->status . '</a>';
        }

        return $status_label;
    }

    public function getStatusTooltipAttribute()
    {
        $statuses = Loan::getStatuses();
        $status = array_key_exists($this->status, $statuses) ? $statuses[$this->status] : [];
        return array_key_exists('tooltip', $status) ? $status['tooltip'] : null;
    }

    public function getInterestRateLabelAttribute()
    {
        return number_format($this->interest_rate, 2) . '% ' . strtolower(trans('accounting::core.per')) . ' ' . $this->interest_rate_type;
    }

    public static function getStatuses()
    {
        return [
            'pending' => [
                'name' => trans('loan::general.pending'),
                'label' => '<span class="label label-warning">' . trans_choice('loan::general.pending_approval', 1) . '</span>',
                'tooltip' => __('loan::lang.tooltip_loanshowstatuspendingapproval')
            ],
            'submitted' => [
                'name' => trans('loan::general.submitted'),
                'label' => '<span class="label label-warning">' . trans_choice('loan::general.pending_approval', 1) . '</span>',
                'tooltip' => __('loan::lang.tooltip_loanshowstatuspendingapproval')
            ],
            'overpaid' => [
                'name' => trans('loan::general.overpaid'),
                'label' => '<span class="label label-warning"> ' . trans_choice('loan::general.overpaid', 1) . ' </span>'
            ],
            'approved' => [
                'name' => trans('loan::general.approved'),
                'label' => '<span class="label label-primary">' . trans_choice('loan::general.awaiting_disbursement', 1) . '</span>',
                'tooltip' => __('loan::lang.tooltip_loanshowstatusawaitingdisbursement')
            ],
            'active' => [
                'name' => trans('loan::general.active'),
                'label' => '<span class="label label-success">' . trans_choice('loan::general.active', 1) . '</span>'
            ],
            'fully_paid' => [
                'name' => trans('loan::general.fully_paid'),
                'label' => '<span class="label label-success">' . trans_choice('loan::general.fully_paid', 1) . '</span>'
            ],
            'rejected' => [
                'name' => trans('loan::general.rejected'),
                'label' => '<span class="label label-danger">' . trans_choice('loan::general.rejected', 1) . '</span',
                'tooltip' => __('loan::lang.tooltip_loanshowstatusreject')
            ],
            'withdrawn' => [
                'name' => trans('loan::general.withdrawn'),
                'label' => '<span class="label label-danger">' . trans_choice('loan::general.withdrawn', 1) . '</span>',
                'tooltip' => __('loan::lang.tooltip_loanshowstatuswithdrawn')
            ],
            'written_off' => [
                'name' => trans('loan::general.written_off'),
                'label' => '<span class="label label-danger">' . trans_choice('loan::general.written_off', 1) . '</span>'
            ],
            'closed' => [
                'name' => trans('loan::general.closed'),
                'label' => '<span class="label label-danger"> ' . trans_choice('loan::general.closed', 1) . '</span'
            ],
            'pending_reschedule' => [
                'name' => trans('loan::general.pending_reschedule'),
                'label' => '<span class="label label-warning">' . trans_choice('loan::general.pending_reschedule', 1) . '</span>'
            ],
            'rescheduled' => [
                'name' => trans('loan::general.rescheduled'),
                'label' => '<span class="label label-primary">' . trans_choice('loan::general.rescheduled', 1) . '</span>'
            ],
        ];
    }

    public static function getParentStatuses()
    {
        $statuses = Loan::getStatuses();

        $parent_keys = ['closed'];

        return array_filter($statuses, function ($k) use ($parent_keys) {
            return in_array($k, $parent_keys);
        }, ARRAY_FILTER_USE_KEY);
    }

    /**Arrears calculation start */
    public function getArrearsLastScheduleAttribute()
    {
        return $this->overdue_repayment_schedules()->orderBy('due_date', 'desc')->first();
    }

    public function getArrearsDaysAttribute()
    {
        return !empty($this->arrears_last_schedule) ?
            \Carbon::today()->diffInDays(\Carbon::parse($this->arrears_last_schedule->due_date)) : 0;
    }

    public function getArrearsWeeksAttribute()
    {
        return round($this->arrears_days / 7, 1);
    }

    public function getArrearsWeeksBandAttribute()
    {
        if ($this->arrears_days == 0) {
            return 'on_schedule';
        }

        return ceil($this->arrears_weeks) <= 12 ? ceil($this->arrears_weeks) : '13+';
    }

    public static function getWeeklyBands()
    {
        $weekly_bands = range(1, 12);

        array_unshift($weekly_bands, 'on_schedule');

        array_push($weekly_bands, '13+');

        return $weekly_bands;
    }

    public function getArrearsWeeksBandLabelAttribute()
    {
        return trans('accounting::core.' . $this->arrears_weeks_band);
    }

    public function getArrearsDaysBandAttribute()
    {
        if ($this->arrears_days == 0) {
            return 'on_schedule';
        } else if ($this->arrears_days <= 30) {
            return '1 - 30';
        } else if ($this->arrears_days <= 60) {
            return '31 - 60';
        } else if ($this->arrears_days <= 90) {
            return '61 - 90';
        } else if ($this->arrears_days <= 180) {
            return '91 - 180';
        } else if ($this->arrears_days <= 360) {
            return '181 - 360';
        } else {
            return '361+';
        }
    }

    public function getArrearsDaysBandLabelAttribute()
    {
        return trans('accounting::core.' . $this->arrears_days_band);
    }

    public static function getMonthlyBands()
    {
        return [
            'on_schedule',
            '1 - 30',
            '31 - 60',
            '61 - 90',
            '91 - 180',
            '181 - 360',
            '361+'
        ];
    }

    public function getArrearsAmountAttribute()
    {
        return $this->overdue_repayment_schedules->sum('amount_due');
    }
    /**Arrears calculation end */

    public function getProjectedBalanceAttribute()
    {
        return  $this->repayment_schedules->sum('total_principal') +                            //Total principal +
            $this->repayment_schedules->sum('total_penalties') +                                //Total penalties +
            $this->repayment_schedules->sum('total_fees') +                                     //Total fees + 
            $this->repayment_schedules->sum('total_interest') -                                 //Total interest upto last installment -
            ($this->repayment_schedules->sum('total_paid') + $this->disbursement_charges);      //Total paid
    }

    public function getCurrentBalanceAttribute()
    {
        $current_balance = $this->repayment_schedules->sum('total_principal') +                 //Total principal +
            $this->repayment_schedules->sum('total_penalties') +                                //Total penalties +
            $this->repayment_schedules->sum('total_fees') +                                     //Total fees + 
            $this->current_repayment_schedules->sum('total_interest') -                         //Interest upto current installment -
            ($this->repayment_schedules->sum('total_paid') + $this->disbursement_charges);      //Total paid

        //Max function ensures that the current balance is always zero or greater - this avoids negative balance values
        return max($current_balance, 0);
    }

    public function getBalanceTodayAttribute()
    {
        return  $this->current_repayment_schedules->sum('total_principal') +                            //Total principal upto current installment +
            $this->current_repayment_schedules->sum('total_penalties') +                                //Total penalties upto current installment +
            $this->current_repayment_schedules->sum('total_fees') +                                     //Total fees upto current installment + 
            $this->current_repayment_schedules->sum('total_interest') -                                 //Interest upto current installment -
            ($this->current_repayment_schedules->sum('total_paid') + $this->disbursement_charges);      //Total paid upto current installment
    }

    public function getOutstandingAmountAttribute()
    {
        return $this->current_balance;
    }

    /**
     * Returns aggregate of loan transactions
     * @param int $contact_id Only needed to filter by contact_id 
     */
    public static function getAggregates($contact_id = null)
    {
        $loan = Loan::join('loan_transactions', 'loan_transactions.loan_id', '=', 'loans.id')
            ->join('business_locations', 'business_locations.id', '=', 'loan_transactions.location_id')
            ->where('business_locations.business_id', session('business.id'))
            ->when($contact_id, function ($query) use ($contact_id) {
                $query->where('loans.contact_id', $contact_id);
            })
            ->select('loan_transactions.name', 'loan_transactions.amount')
            ->get();

        return (object)[
            'total_disbursed' => $loan->where('name', 'Disbursement')->sum('amount'),
            'total_repayment' => $loan->where('name', 'Repayment')->sum('amount'),
        ];
    }

    public static function getRepaymentFrequencyTypes()
    {
        return [
            'days' => trans_choice('loan::general.day', 2),
            'weeks' => trans_choice('loan::general.week', 2),
            'months' => trans_choice('loan::general.month', 2),
        ];
    }

    public function getAdditionalNotesAttribute()
    {
        return [
            'approved_notes' => (object)[
                'label' => trans('loan::general.approved') . ' ' . trans_choice('accounting::core.note', 2),
                'note' => $this->approved_notes,
                'created_by' => $this->approved_by,
                'created_at' => $this->approved_on_date,
                'created_when' => trans('loan::general.on_approval'),
            ],
            'dibursed_notes' => (object)[
                'label' => trans('loan::general.disbursed') . ' ' . trans_choice('accounting::core.note', 2),
                'note' => $this->dibursed_notes,
                'created_by' => $this->disbursed_by,
                'created_at' => $this->disbursed_on_date,
                'created_when' => trans('loan::general.on_disbursal'),
            ],
            'rejected_notes' => (object)[
                'label' => trans('loan::general.rejected') . ' ' . trans_choice('accounting::core.note', 2),
                'note' => $this->rejected_notes,
                'created_by' => $this->rejected_by,
                'created_at' => $this->rejected_on_date,
                'created_when' => trans('loan::general.on_rejection'),
            ],
            'written_off_notes' => (object)[
                'label' => trans('loan::general.written_off') . ' ' . trans_choice('accounting::core.note', 2),
                'note' => $this->written_off_notes,
                'created_by' => $this->written_off_by,
                'created_at' => $this->written_off_on_date,
                'created_when' => trans('loan::general.on_write_off'),
            ],
            'closed_notes' => (object)[
                'label' => trans('loan::general.closed') . ' ' . trans_choice('accounting::core.note', 2),
                'note' => $this->closed_notes,
                'created_by' => $this->closed_by,
                'created_at' => $this->closed_on_date,
                'created_when' => trans('loan::general.on_close'),
            ],
            'rescheduled_notes' => (object)[
                'label' => trans('loan::general.rescheduled') . ' ' . trans_choice('accounting::core.note', 2),
                'note' => $this->rescheduled_notes,
                'created_by' => $this->rescheduled_by,
                'created_at' => $this->rescheduled_on_date,
                'created_when' => trans('loan::general.on_reschedule'),
            ],
            'withdrawn_notes' => (object)[
                'label' => trans('loan::general.withdrawn') . ' ' . trans_choice('accounting::core.note', 2),
                'note' => $this->withdrawn_notes,
                'created_by' => $this->withdrawn_by,
                'created_at' => $this->withdrawn_on_date,
                'created_when' => trans('loan::general.on_withdrawal'),
            ],
        ];
    }

    public function getRepaymentFrequencyTypeLabelAttribute()
    {
        switch ($this->repayment_frequency_type) {
            case 'days':
                return trans_choice('loan::general.day', 2);
            case 'weeks':
                return trans_choice('loan::general.week', 2);
            case 'months':
                return trans_choice('loan::general.month', 2);
            case 'years':
                return trans_choice('loan::general.year', 2);
            default:
                return trans('accounting::core.none');
        }
    }

    public function getRepaymentFrequencyLabelAttribute()
    {
        $type = $this->repayment_frequency > 1 ? $this->repayment_frequency_type : substr($this->repayment_frequency_type, 0, -1);
        return $this->repayment_frequency . ' ' . $type;
    }

    public function getLoanTermLabelAttribute()
    {
        $type = $this->loan_term > 1 ? $this->repayment_frequency_type : substr($this->repayment_frequency_type, 0, -1);
        return $this->loan_term . ' ' . $type;
    }

    public function getTotalRepaymentAttribute()
    {
        return $this->repayment_transactions()->sum('amount');
    }

    public function getRestrictionsAttribute()
    {
        $contact_restrictions = $this->contact->restrictions;
        if (!empty($this->contact->restrictions)) {
            return $contact_restrictions;
        }
        return $this->business_location->restrictions;
    }

    public function getCurrentAmountDueAttribute()
    {
        return $this->repayment_schedules->where('due_date', '<=', date('Y-m-d'))->where('paid_by_date', null)->sum('total_due');
    }

    public function getMaturityDateAttribute()
    {
        return $this->repayment_schedules->pluck('due_date')->last();
    }

    public function getLastRepaymentDateAttribute()
    {
        $last_repayment = $this->repayment_schedules->where('paid_by_date', '!=', null)->last();
        return $last_repayment ? $last_repayment->paid_by_date : trans('accounting::core.none');
    }

    public function getDaysToExpectedDisbursementDateAttribute()
    {
        $now = time();
        $expected_disbursement_date = strtotime($this->expected_disbursement_date ?: date('Y-m-d'));
        $datediff = $expected_disbursement_date - $now;
        return round($datediff / (60 * 60 * 24));
    }

    public function getDaysPendingApprovalAttribute()
    {
        $now = time();
        $created_at = strtotime($this->created_at ?: date('Y-m-d'));
        $datediff = $now - $created_at;
        return round($datediff / (60 * 60 * 24));
    }

    public function getPortfolioAtRiskAttribute()
    {
        //Portfolio at risk = Principal outstanding / Principal disbursed
        return $this->repayment_schedules->sum('principal_due') /
            max($this->repayment_schedules->sum('principal'), 1);
    }

    public function getPortfolioAtRiskPercentageAttribute()
    {
        return $this->portfolio_at_risk * 100;
    }

    public static function getAmortizationMethods()
    {
        return [
            'equal_installments' => trans_choice('loan::general.equal_installments', 1),
            'equal_principal_payments' => trans_choice('loan::general.equal_principal_payments', 1)
        ];
    }

    public function getInterestFreePeriodAttribute()
    {
        return $this->repayment_schedules->filter(function ($instalment) {
            return $instalment->interest == 0 || $instalment->interest_waived_derived >= $instalment->interest;
        })->count();
    }

    public function getInterestCalculationPeriodAttribute()
    {
        return $this->repayment_schedules->filter(function ($instalment) {
            return $instalment->interest > 0 && $instalment->interest_waived_derived == 0;
        })->count();
    }

    public function getNumberOfRepaymentsAttribute()
    {
        return $this->repayment_schedules->where('paid_by_date', '!=', null)->count('paid_by_date');
    }

    public function getNumberOfInstallmentsAttribute()
    {
        return $this->repayment_schedules->count('due_date');
    }

    public function getPercentageOfTimelyRepaymentsAttribute()
    {
        // Will either be the number of installments due or number of installments paid - whichever is bigger 
        $no_instalments = max($this->due_repayment_schedules->count(), $this->paid_repayment_schedules->count());

        //Max function in this case is a protection from zero division since 1 is the least number that can be the divisor
        $percentage = $this->timely_repayment_schedules->count() / max($no_instalments, 1) * 100;
        // Min function ensures that the highest percentage is 100 
        return number_format(min($percentage, 100), 2);
    }

    public function getLoanStandingAttribute()
    {
        if ($this->repayments_due_today->count() > 0) {
            return (object)[
                'status' => trans('accounting::core.due') . ' ' . trans('accounting::core.today'),
                'label_class' => 'info'
            ];
        }

        $arrears_amount = $this->arrears_amount;

        if ($arrears_amount > 0) {
            return (object)[
                'status' => trans_choice('loan::lang.missed_repayment', 1),
                'label_class' => 'warning'
            ];
        }

        $in_bad_standing = (object)[
            'status' => trans('loan::lang.in_bad_standing'),
            'label_class' => 'danger'
        ];

        $in_good_standing = (object)[
            'status' => trans('loan::lang.in_good_standing'),
            'label_class' => 'success'
        ];

        return ($this->late_repayment_schedules->count() > 0) ?
            $in_bad_standing :
            $in_good_standing;
    }
}
