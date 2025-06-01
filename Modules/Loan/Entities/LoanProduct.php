<?php

namespace Modules\Loan\Entities;

use Modules\Accounting\Entities\User;
use Illuminate\Database\Eloquent\Model;
use Modules\Loan\Http\Services\LoanProductService;

class LoanProduct extends Model
{
    protected $fillable = [];

    public $table = "loan_products";

    protected $appends = ['minimum_loan_term_in_days', 'maximum_loan_term_in_days'];

    public function charges()
    {
        return $this->hasMany(LoanProductLinkedCharge::class, 'loan_product_id', 'id');
    }

    public function selected_charges()
    {
        return $this->belongsToMany(LoanCharge::class, 'loan_product_linked_charges', 'loan_product_id', 'loan_charge_id')->wherePivot('is_overdue', false);
    }

    public function overdue_selected_charges()
    {
        return $this->belongsToMany(LoanCharge::class, 'loan_product_linked_charges', 'loan_product_id', 'loan_charge_id')->wherePivot('is_overdue', true);
    }

    public function selected_credit_checks()
    {
        return $this->belongsToMany(LoanCreditCheck::class, 'loan_product_linked_credit_checks', 'loan_product_id', 'loan_credit_check_id');
    }

    public function approval_officers()
    {
        return $this->belongsToMany(User::class, 'loan_product_approval_officers', 'product_id', 'user_id');
    }

    public static function forDropdown($select_all = false)
    {
        return LoanProduct::join('loans', 'loans.loan_product_id', 'loan_products.id')
            ->leftJoin('users', 'users.id', 'loan_products.created_by_id')
            ->where('users.business_id', session('business.id'))
            ->where('loan_products.active', 1)
            ->when(!$select_all, function ($query) {
                $query->select('loan_products.id', 'loan_products.name');
            })
            ->when($select_all, function ($query) {
                $query->select('loan_products.*');
            })
            ->groupBy('loans.loan_product_id')
            ->orderBy('loan_products.name')
            ->get();
    }

    public static function withCharges($select_all = false)
    {
        return LoanProduct::with('charges')
            ->with('charges.charge')
            ->leftJoin('users', 'users.id', 'loan_products.created_by_id')
            ->where('users.business_id', session('business.id'))
            ->where('active', 1)
            ->when(!$select_all, function ($query) {
                $query->select('loan_products.id', 'loan_products.name');
            })
            ->when($select_all, function ($query) {
                $query->select('loan_products.*');
            })
            ->orderBy('loan_products.name')
            ->get();
    }

    public function getMinimumLoanTermInDaysAttribute()
    {
        return (new LoanProductService())->calculateLoanTermInDays($this->minimum_loan_term, $this->repayment_frequency_type);
    }

    public function getMaximumLoanTermInDaysAttribute()
    {
        return (new LoanProductService())->calculateLoanTermInDays($this->maximum_loan_term, $this->repayment_frequency_type);
    }

    public function getConfigurableTermsAndSettingsAttribute($value)
    {
        $values = (array) json_decode($value);

        return array_map(function($value){
            return $value == "true";
        }, $values);
    }

    public function getPrincipalBasedOnLoanCycleAttribute($value)
    {
        return (array) json_decode($value);
    }

    public function getRepaymentFrequencyBasedOnLoanCycleAttribute($value)
    {
        return (array) json_decode($value);
    }

    public function getInterestRateBasedOnLoanCycleAttribute($value)
    {
        return (array) json_decode($value);
    }

    public static function getDefaultConfigurableTermsAndSettings()
    {
        return [
            'allow_overriding_select_terms_and_settings_in_loan_account' => true,
            'amortization' => true,
            'interest_method' => true,
            'repayment_strategy' => true,
            'interest_calculation_period' => true,
            'arrears_tolerance' => true,
            'repaid_every' => true,
            'moratorium' => true,
            'number_of_days_loan_may_be_due_before_moving_into_arrears' => true,
        ];
    }
}
