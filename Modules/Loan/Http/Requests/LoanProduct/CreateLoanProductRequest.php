<?php

namespace Modules\Loan\Http\Requests\LoanProduct;

use Illuminate\Foundation\Http\FormRequest;

class CreateLoanProductRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'currency_id' => ['required'],
            'loan_transaction_processing_strategy_id' => ['required'],
            'name' => ['required'],
            'short_name' => ['required'],
            'description' => ['required'],
            'minimum_principal' => ['required', 'numeric', 'lt:maximum_principal'],
            'default_principal' => ['required', 'numeric', 'lte:maximum_principal'], //previously lt
            'maximum_principal' => ['required', 'numeric', 'gt:minimum_principal'],
            'minimum_loan_term' => ['required', 'numeric', 'lt:maximum_loan_term'],
            'default_loan_term' => ['required', 'numeric', 'lte:maximum_loan_term'], //preiusly lt
            'maximum_loan_term' => ['required', 'numeric', 'gt:minimum_loan_term'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'minimum_interest_rate' => ['required', 'numeric', 'lt:maximum_interest_rate'],
            'default_interest_rate' => ['required', 'numeric', 'lte:maximum_interest_rate'], //previously lt
            'maximum_interest_rate' => ['required', 'numeric', 'gt:minimum_interest_rate'],
            'interest_rate_type' => ['required'],
            'grace_on_principal_paid' => ['required'],
            'grace_on_interest_paid' => ['required'],
            'grace_on_interest_charged' => ['required'],
            'interest_methodology' => ['required'],
            'amortization_method' => ['required'],
            'auto_disburse' => ['required'],
            'accounting_rule' => ['required'],
            'active' => ['required'],
            'fund_source_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'loan_portfolio_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual_periodic,accrual_upfront'],
            'penalties_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual_periodic,accrual_upfront'],
            'fees_receivable_chart_of_account_id' => ['required_if:accounting_rule,accrual_periodic,accrual_upfront'],
            'transfer_in_suspense_chart_of_account_id' => ['required_if:accounting_rule,accrual_periodic,accrual_upfront'],
            'overpayments_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_interest_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_penalties_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_fees_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'income_from_recovery_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'losses_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'interest_written_off_chart_of_account_id' => ['required_if:accounting_rule,cash'],
            'suspended_income_chart_of_account_id' => ['required_if:accounting_rule,cash'],
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'minimum_principal.lt' => 'Minimum Principal cannot be greater than maximum principal',
            'default_principal.lte' => 'Default Principal cannot be greater than maximum principal',
            'maximum_principal.gt' => 'Maximum Principal cannot be smaller than minimum principal',
            'minimum_loan_term.lt' => 'Minimum loan term cannot be greater than maximum loan term',
            'default_loan_term.lte' => 'Default loan term cannot be greater than maximum loan term',
            'maximum_loan_term.gt' => 'Maximum loan term cannot be smaller than maximum loan term',
            'minimum_interest_rate.lt' => 'Minimum interest cannot be greater than maximum interest rate',
            'default_interest_rate.lte' => 'Default interest cannot be greater than maximum interest rate',
            'maximum_interest_rate.gt' => 'Maximum interest cannot be smaller than minimum interest rate',
            'minimum_principal.numeric' => 'Minimum Principal must be a number',
            'default_principal.numeric' => 'Default Principal must be a number',
            'maximum_principal.numeric' => 'Maximum Principal must be a number',
            'minimum_loan_term.numeric' => 'Minimum loan term must be a number',
            'default_loan_term.numeric' => 'Default loan term must be a number',
            'maximum_loan_term.numeric' => 'Maximum loan term must be a number',
            'minimum_interest_rate.numeric' => 'Minimum interest must be a number',
            'default_interest_rate.numeric' => 'Default interest must be a number',
            'maximum_interest_rate.numeric' => 'Maximum interest must be a number',
            'repayment_frequency.numeric' => 'Repayment frequency must be a number',
        ];
    }

    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }
}