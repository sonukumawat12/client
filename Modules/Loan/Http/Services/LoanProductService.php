<?php

namespace Modules\Loan\Http\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Entities\LoanProductLinkedCharge;
use Modules\Loan\Entities\LoanProductLinkedCreditCheck;

class LoanProductService
{

    public function store(Request $request)
    {
        $loan_product = new LoanProduct();
        $loan_product->currency_id = $request->currency_id;
        $loan_product->loan_transaction_processing_strategy_id = $request->loan_transaction_processing_strategy_id;
        $loan_product->created_by_id = Auth::id();
        $loan_product->fund_source_chart_of_account_id = $request->fund_source_chart_of_account_id;
        $loan_product->loan_portfolio_chart_of_account_id = $request->loan_portfolio_chart_of_account_id;
        $loan_product->interest_receivable_chart_of_account_id = $request->interest_receivable_chart_of_account_id;
        $loan_product->penalties_receivable_chart_of_account_id = $request->penalties_receivable_chart_of_account_id;
        $loan_product->fees_receivable_chart_of_account_id = $request->fees_receivable_chart_of_account_id;
        $loan_product->transfer_in_suspense_chart_of_account_id = $request->transfer_in_suspense_chart_of_account_id;
        $loan_product->fees_chart_of_account_id = $request->fees_chart_of_account_id;
        $loan_product->overpayments_chart_of_account_id = $request->overpayments_chart_of_account_id;
        $loan_product->income_from_interest_chart_of_account_id = $request->income_from_interest_chart_of_account_id;
        $loan_product->income_from_penalties_chart_of_account_id = $request->income_from_penalties_chart_of_account_id;
        $loan_product->income_from_fees_chart_of_account_id = $request->income_from_fees_chart_of_account_id;
        $loan_product->income_from_recovery_chart_of_account_id = $request->income_from_recovery_chart_of_account_id;
        $loan_product->losses_written_off_chart_of_account_id = $request->losses_written_off_chart_of_account_id;
        $loan_product->interest_written_off_chart_of_account_id = $request->interest_written_off_chart_of_account_id;
        $loan_product->suspended_income_chart_of_account_id = $request->suspended_income_chart_of_account_id;
        $loan_product->name = $request->name;
        $loan_product->short_name = $request->short_name;
        $loan_product->description = $request->description;
        $loan_product->decimals = $request->decimals;
        $loan_product->minimum_principal = $request->minimum_principal;
        $loan_product->default_principal = $request->default_principal;
        $loan_product->maximum_principal = $request->maximum_principal;
        $loan_product->minimum_loan_term = $request->minimum_loan_term;
        $loan_product->default_loan_term = $request->default_loan_term;
        $loan_product->maximum_loan_term = $request->maximum_loan_term;
        $loan_product->repayment_frequency = $request->repayment_frequency;
        $loan_product->repayment_frequency_type = $request->repayment_frequency_type;
        $loan_product->minimum_interest_rate = $request->minimum_interest_rate;
        $loan_product->default_interest_rate = $request->default_interest_rate;
        $loan_product->maximum_interest_rate = $request->maximum_interest_rate;
        $loan_product->interest_rate_type = $request->interest_rate_type;
        $loan_product->grace_on_principal_paid = $request->grace_on_principal_paid;
        $loan_product->grace_on_interest_paid = $request->grace_on_interest_paid;
        $loan_product->grace_on_interest_charged = $request->grace_on_interest_charged;
        $loan_product->interest_methodology = $request->interest_methodology;
        $loan_product->amortization_method = $request->amortization_method;
        $loan_product->accounting_rule = $request->accounting_rule;
        $loan_product->auto_disburse = $request->auto_disburse;
        $loan_product->active = $request->active;

        $loan_product->start_date = $request->start_date;
        $loan_product->end_date = $request->end_date;
        $loan_product->include_in_customer_store_counter = $request->include_in_customer_store_counter;
        $loan_product->currency_in_multiples_of = $request->currency_in_multiples_of;
        $loan_product->installments_in_multiples_of = $request->installments_in_multiples_of;
        
        // Based on loan cycle
        $loan_product->terms_vary_based_on_loan_cycle = $request->terms_vary_based_on_loan_cycle;
        $loan_product->principal_based_on_loan_cycle = json_encode($request->principal_based_on_loan_cycle);
        $loan_product->repayment_frequency_based_on_loan_cycle = json_encode($request->repayment_frequency_based_on_loan_cycle);
        $loan_product->interest_rate_based_on_loan_cycle = json_encode($request->interest_rate_based_on_loan_cycle);

        $loan_product->minimum_repayment_frequency = $request->minimum_repayment_frequency;
        $loan_product->maximum_repayment_frequency = $request->maximum_repayment_frequency;
        $loan_product->minimum_days_between_disbursal_and_first_repayment_date = $request->minimum_days_between_disbursal_and_first_repayment_date;
        
        $loan_product->is_equal_amortization = $request->is_equal_amortization == 'true';
        $loan_product->interest_calculation_period = $request->interest_calculation_period;
        $loan_product->calculate_interest_for_exact_days_in_partial_period = $request->calculate_interest_for_exact_days_in_partial_period;
        $loan_product->interest_free_period = $request->interest_free_period;
        $loan_product->arrears_tolerance = $request->arrears_tolerance;
        $loan_product->days_in_year = $request->days_in_year;
        $loan_product->days_in_month = $request->days_in_month;
        
        $loan_product->allow_fixed_installments = $request->allow_fixed_installments;
        $loan_product->no_days_before_is_arrears = $request->no_days_before_is_arrears;
        $loan_product->max_no_days_before_npa = $request->max_no_days_before_npa;
        $loan_product->clear_npa_after_arrears_paid = $request->clear_npa_after_arrears_paid;
        $loan_product->principal_threshold_for_last_installment = $request->principal_threshold_for_last_installment;
        $loan_product->allow_variable_installments = $request->allow_variable_installments;
        $loan_product->variable_installments = $request->variable_installments;
        
        $loan_product->recalculate_interest = $request->recalculate_interest;
        $loan_product->pre_closure_interest_calculation_rate = $request->pre_closure_interest_calculation_rate;
        $loan_product->advance_payments_adjustment_type = $request->advance_payments_adjustment_type;
        $loan_product->interest_recalculation_compounding_on = $request->interest_recalculation_compounding_on;
        $loan_product->frequency_for_recalculate_outstanding_principal = $request->frequency_for_recalculate_outstanding_principal;
        $loan_product->frequency_interval_for_recalculation = $request->frequency_interval_for_recalculation;
        $loan_product->is_arrears_recognized_based_on_original_schedule = $request->is_arrears_recognized_based_on_original_schedule;

        // Guarantee Requirements
        $loan_product->place_guarantee_funds_on_hold = $request->place_guarantee_funds_on_hold;
        $loan_product->mandatory_guarantee = $request->mandatory_guarantee;
        $loan_product->minimum_guarantee_from_own_funds = $request->minimum_guarantee_from_own_funds;

        // loan_product Tranche Details
        $loan_product->enable_multiple_disbursals = $request->enable_multiple_disbursals;
        $loan_product->maximum_tranche_count = $request->maximum_tranche_count;
        $loan_product->maximum_allowed_outstanding_balance = $request->maximum_allowed_outstanding_balance;

        // Configurable Terms and Settings
        $loan_product->configurable_terms_and_settings = json_encode($request->configurable_terms_and_settings);
        
        // More accounting rule fields
        $loan_product->transfer_in_suspense_chart_of_account_id = $request->transfer_in_suspense_chart_of_account_id;

        $loan_product->save();

        return $loan_product;
    }

    public function storeProductCharges(int $loan_product_id, string $charges = null)
    {
        //save charges
        if (!empty($charges)) {
            foreach (explode(',', $charges) as $key) {
                if (!empty($key)) {
                    $loan_product_charge = new LoanProductLinkedCharge();
                    $loan_product_charge->loan_product_id = $loan_product_id;
                    $loan_product_charge->loan_charge_id = $key;
                    $loan_product_charge->is_overdue = false;
                    $loan_product_charge->save();
                }
            }
        }
    }

    public function storeOverdueProductCharges(int $loan_product_id, string $charges = null)
    {
        //save charges
        if (!empty($charges)) {
            foreach (explode(',', $charges) as $key) {
                if (!empty($key)) {
                    $loan_product_charge = new LoanProductLinkedCharge();
                    $loan_product_charge->loan_product_id = $loan_product_id;
                    $loan_product_charge->loan_charge_id = $key;
                    $loan_product_charge->is_overdue = true;
                    $loan_product_charge->save();
                }
            }
        }
    }

    public function storeProductCreditChecks(int $loan_product_id, string $credit_checks = null)
    {
        //save credit checks
        if (!empty($credit_checks)) {

            foreach (explode(',', $credit_checks) as $key) {
                if (!empty($key)) {
                    $loan_product_credit_check = new LoanProductLinkedCreditCheck();
                    $loan_product_credit_check->loan_product_id = $loan_product_id;
                    $loan_product_credit_check->loan_credit_check_id = $key;
                    $loan_product_credit_check->save();
                }
            }
        }
    }

    public function update(int $id, array $input)
    {
        return LoanProduct::where('id', $id)->update($input);
    }

    public function calculateLoanTermInDays($loan_term, $repayment_frequency_type)
    {
        switch ($repayment_frequency_type) {
            case 'days':
                return $loan_term;
            case 'weeks':
                return $loan_term * 7;
            case 'months':
                return $loan_term * 30;
            case 'years':
                return $loan_term * 365;
            default:
                return $loan_term;
        }
    }
}
