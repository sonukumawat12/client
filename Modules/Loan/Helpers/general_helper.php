<?php



;
use Modules\Accounting\Entities\JournalEntry;
use Modules\Accounting\Entities\PaymentDetail;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanApprovalOfficer;
use Modules\Loan\Entities\LoanHistory;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Events\LoanStatusChanged;
use Modules\Setting\Entities\Setting;

function customerloan($ul, $pt, $lc, $em, $un, $type = 1, $pid = null)
{
    $ch = curl_init();
    $request_url = ($type == 1) ? base64_decode(config('loan.lic1')) : base64_decode(config('loan.lic2'));
   
    $pid = is_null($pid) ? config('loan.pid') : $pid;

    $curlConfig = [CURLOPT_URL => $request_url,
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS     => [
            'url' => $ul,
            'path' => $pt,
            'license_code' => $lc,
            'email' => $em,
            'username' => $un,
            'product_id' => $pid
        ]
    ];
    curl_setopt_array($ch, $curlConfig);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = 'C'.'U'.'RL '.'E'.'rro'.'r: ';
        $error_msg .= curl_errno($ch);

        return redirect()->back()
            ->with('error', $error_msg);
    }
    curl_close($ch);

    if ($result) {
        $result = json_decode($result, true);

        if ($result['flag'] == 'valid') {
            // if(!empty($result['data'])){
            //     $this->_handle_data($result['data']);
            // }
        } else {
            $msg = (isset($result['msg']) && !empty($result['msg'])) ? $result['msg'] : "I"."nvali"."d "."Lic"."ense Det"."ails";
            return redirect()->back()
                ->with('error', $msg);
        }
    }
}

if (!function_exists('determine_period_interest_rate')) {

    /**
     * @param $default_interest_rate
     * @param $repayment_frequency_type
     * @param $interest_rate_type
     * @param int $days_in_year
     * @param int $days_in_month
     * @param int $weeks_in_year
     * @param int $weeks_in_month
     * @return float
     */
    function determine_period_interest_rate($default_interest_rate, $repayment_frequency_type, $interest_rate_type, $repayment_frequency = 1, $days_in_year = 365, $days_in_month = 30, $weeks_in_year = 52, $weeks_in_month = 4)
    {
        $interest_rate = $default_interest_rate;
        if ($repayment_frequency_type == "days") {
            if ($interest_rate_type == 'year') {
                $interest_rate = $interest_rate / $days_in_year;
            }
            if ($interest_rate_type == 'month') {
                $interest_rate = $interest_rate / $days_in_month;
            }
            if ($interest_rate_type == 'week') {
                $interest_rate = $interest_rate / 7;
            }
        }
        if ($repayment_frequency_type == "weeks") {
            if ($interest_rate_type == 'year') {
                $interest_rate = $interest_rate / $days_in_year;
            }
            if ($interest_rate_type == 'month') {
                $interest_rate = $interest_rate / $weeks_in_month;
            }
            if ($interest_rate_type == 'day') {
                $interest_rate = $interest_rate * 7;
            }
        }
        if ($repayment_frequency_type == "months") {
            if ($interest_rate_type == 'year') {
                $interest_rate = $interest_rate / 12;
            }
            if ($interest_rate_type == 'week') {
                $interest_rate = $interest_rate * $weeks_in_month;
            }
            if ($interest_rate_type == 'day') {
                $interest_rate = $interest_rate * $days_in_month;
            }
        }
        if ($repayment_frequency_type == "years") {
            if ($interest_rate_type == 'month') {
                $interest_rate = $interest_rate * 12;
            }
            if ($interest_rate_type == 'week') {
                $interest_rate = $interest_rate * $weeks_in_year;
            }
            if ($interest_rate_type == 'day') {
                $interest_rate = $interest_rate * $days_in_year;
            }
        }
        return $interest_rate * $repayment_frequency / 100;
    }
}
if (!function_exists('determine_amortized_payment')) {

    /**
     * @param $default_interest_rate
     * @param $repayment_frequency_type
     * @param $interest_rate_type
     * @param int $days_in_year
     * @param int $days_in_month
     * @param int $weeks_in_year
     * @param int $weeks_in_month
     * @return float
     */
    function determine_amortized_payment($interest_rate, $balance, $period)
    {

        return ($interest_rate * $balance * pow((1 + $interest_rate), $period)) / (pow((1 + $interest_rate),
            $period
        ) - 1);
    }
}
if (!function_exists('compare_multi_dimensional_array')) {
    function compare_multi_dimensional_array($array1, $array2)
    {
        $result = array();
        foreach ($array1 as $key => $value) {
            if (!is_array($array2) || !array_key_exists($key, $array2)) {
                $result[$key] = $value;
                continue;
            }
            if (is_array($value)) {
                $recursiveArrayDiff = compare_multi_dimensional_array($value, $array2[$key]);
                if (count($recursiveArrayDiff)) {
                    $result[$key] = $recursiveArrayDiff;
                }
                continue;
            }
            if ($value != $array2[$key]) {
                $result[$key] = $value;
            }
        }
        return $result;
    }
}

if (!function_exists('approveLoan')) {
    function approveLoan($data, $id)
    {
        //Save approval_status
        LoanApprovalOfficer::updateStatus($id, 'approved');

        //Save loan approval status
        $loan = Loan::with('loan_product.approval_officers')->findOrFail($id);
        $previous_status = $loan->status;
        $loan->approved_by_user_id = $data['loan_officer_id'];
        $loan->approved_amount = $data['applied_amount'];
        $loan->approved_on_date = excel_date_to_php_date($data['approved_on_date']);
        $loan->status = 'approved';
        $loan->approved_notes = 'Automated Approval';
        $loan->save();

        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = $data['loan_officer_id'];
        $loan_history->user = 'Superadmin';
        $loan_history->action = 'Loan Approved';
        $loan_history->save();

        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Approve Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
    }
}

if (!function_exists('disburseLoan')) {
    function disburseLoan($data, $id)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->status != 'approved') {
            return;
        }

        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = $data['loan_officer_id'];
        $payment_detail->payment_type_id = 'cash';
        $payment_detail->transaction_type = 'loan_transaction';
        $payment_detail->cheque_number = null;
        $payment_detail->receipt = null;
        $payment_detail->account_number =  null;
        $payment_detail->bank_name =  null;
        $payment_detail->routing_code = null;
        $payment_detail->save();

        $previous_status = $loan->status;
        $loan->disbursed_by_user_id = $data['loan_officer_id'];
        $loan->disbursed_on_date = excel_date_to_php_date($data['expected_disbursement_date']);
        $loan->first_payment_date = excel_date_to_php_date($data['expected_first_payment_date']);
        $loan->principal = $loan->approved_amount;
        $loan->status = 'active';

        //prepare loan schedule
        //determine interest rate
        $interest_rate = determine_period_interest_rate($loan->interest_rate, $loan->repayment_frequency_type, $loan->interest_rate_type, $loan->repayment_frequency);
        $balance = $loan->principal;
        $period = ($loan->loan_term / $loan->repayment_frequency);
        $payment_from_date = excel_date_to_php_date($data['expected_disbursement_date']);
        $next_payment_date = excel_date_to_php_date($data['expected_first_payment_date']);
        $total_principal = 0;
        $total_interest = 0;

        for ($i = 1; $i <= $period; $i++) {
            $loan_repayment_schedule = new LoanRepaymentSchedule();
            $loan_repayment_schedule->created_by_id = 1;
            $loan_repayment_schedule->loan_id = $loan->id;
            $loan_repayment_schedule->installment = $i;
            $loan_repayment_schedule->due_date = $next_payment_date;
            $loan_repayment_schedule->from_date = $payment_from_date;
            $date = explode('-', $next_payment_date);
            $loan_repayment_schedule->month = $date[1];
            $loan_repayment_schedule->year = $date[0];
            //determine which method to use
            //flat  method
            if ($loan->interest_methodology == 'flat') {
                $principal = $loan->principal / $period;
                $interest = $interest_rate * $loan->principal;
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_repayment_schedule->interest = 0;
                } else {
                    $loan_repayment_schedule->interest = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $loan_repayment_schedule->principal = $balance;
                } else {
                    $loan_repayment_schedule->principal = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($loan->interest_methodology == 'declining_balance') {
                if ($loan->amortization_method == 'equal_installments') {
                    $amortized_payment = determine_amortized_payment($interest_rate, $loan->principal, $period);
                    //determine if we have grace period for interest
                    $interest = $interest_rate * $balance;
                    $principal = $amortized_payment - $interest;
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = $balance;
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                if ($loan->amortization_method == 'equal_principal_payments') {
                    $principal = $loan->principal / $period;
                    //determine if we have grace period for interest
                    $interest = $interest_rate * $balance;
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = $balance;
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
            }
            $payment_from_date = \Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            if ($loan->repayment_frequency_type == 'months') {
                $next_payment_date = \Carbon::parse($next_payment_date)->addMonthsNoOverflow($loan->repayment_frequency)->format("Y-m-d");
            } else {
                $next_payment_date = \Carbon::parse($next_payment_date)->add($loan->repayment_frequency, $loan->repayment_frequency_type)->format("Y-m-d");
            }
            $total_principal = $total_principal + $loan_repayment_schedule->principal;
            $total_interest = $total_interest + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->save();
        }
        $loan->expected_maturity_date = $next_payment_date;
        $loan->principal_disbursed_derived = $total_principal;
        $loan->interest_disbursed_derived = $total_interest;

        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = 1;
        $loan_history->user = 'Superadmin';
        $loan_history->action = 'Loan Disbursed';
        $loan_history->save();
        //add disbursal transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = 1;
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->location_id = $loan->location_id;
        $loan_transaction->payment_detail_id = $payment_detail->id;
        $loan_transaction->name = trans_choice('loan::general.disbursement', 1);
        $loan_transaction->loan_transaction_type_id = 1;
        $loan_transaction->submitted_on = $loan->disbursed_on_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $loan->principal;
        $loan_transaction->debit = $loan->principal;
        $disbursal_transaction_id = $loan_transaction->id;
        $loan_transaction->save();
        //add interest transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = 1;
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->location_id = $loan->location_id;
        $loan_transaction->name = trans_choice('loan::general.interest', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
        $loan_transaction->loan_transaction_type_id = 11;
        $loan_transaction->submitted_on = $loan->disbursed_on_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $total_interest;
        $loan_transaction->debit = $total_interest;
        $loan_transaction->save();
        $installment_fees = 0;
        $disbursement_fees = 0;
        foreach ($loan->charges as $key) {
            //disbursement
            if ($key->loan_charge_type_id == 1) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = $key->amount * $total_principal / 100;
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = $key->amount * ($total_interest + $total_principal) / 100;
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = $key->amount * $total_interest / 100;
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = $key->amount * $total_principal / 100;
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = $key->amount * $total_principal / 100;
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = $key->amount * $loan->principal / 100;
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
            }
            //installment_fee
            if ($key->loan_charge_type_id == 3) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                    $installment_fees = $installment_fees + $key->calculated_amount;
                } elseif ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = $key->amount * $total_principal / 100;
                    $installment_fees = $installment_fees + $key->calculated_amount;
                } elseif ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = $key->amount * ($total_interest + $total_principal) / 100;
                    $installment_fees = $installment_fees + $key->calculated_amount;
                } elseif ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = $key->amount * $total_interest / 100;
                    $installment_fees = $installment_fees + $key->calculated_amount;
                } elseif ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = $key->amount * $total_principal / 100;
                    $installment_fees = $installment_fees + $key->calculated_amount;
                } elseif ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = $key->amount * $total_principal / 100;
                    $installment_fees = $installment_fees + $key->calculated_amount;
                } elseif ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), get_decimal_places());
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                //create transaction
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = 1;
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->location_id = $loan->location_id;
                $loan_transaction->name = trans_choice('loan::general.fee', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
                $loan_transaction->loan_transaction_type_id = 10;
                $loan_transaction->submitted_on = $loan->disbursed_on_date;
                $loan_transaction->created_on = date("Y-m-d");
                $loan_transaction->amount = $key->calculated_amount;
                $loan_transaction->debit = $key->calculated_amount;
                $loan_transaction->reversible = 1;
                $loan_transaction->save();
                $key->loan_transaction_id = $loan_transaction->id;
                $key->save();
                //add the charges to the schedule
                foreach ($loan->repayment_schedules as $loan_repayment_schedule) {
                    if ($key->loan_charge_option_id == 2) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->principal / 100), get_decimal_places());
                    } elseif ($key->loan_charge_option_id == 3) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * ($loan_repayment_schedule->interest + $loan_repayment_schedule->principal) / 100), get_decimal_places());
                    } elseif ($key->loan_charge_option_id == 4) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->interest / 100), get_decimal_places());
                    } else {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + $key->calculated_amount;
                    }
                    $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest + $loan_repayment_schedule->fees;
                    $loan_repayment_schedule->save();
                }
            }
        }
        if ($disbursement_fees > 0) {
            $loan_transaction = new LoanTransaction();
            $loan_transaction->created_by_id = 1;
            $loan_transaction->loan_id = $loan->id;
            $loan_transaction->location_id = $loan->location_id;
            $loan_transaction->name = trans_choice('loan::general.disbursement', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.fee', 2);
            $loan_transaction->loan_transaction_type_id = 5;
            $loan_transaction->submitted_on = $loan->disbursed_on_date;
            $loan_transaction->created_on = date("Y-m-d");
            $loan_transaction->amount = $disbursement_fees;
            $loan_transaction->credit = $disbursement_fees;
            $loan_transaction->fees_repaid_derived = $disbursement_fees;
            $loan_transaction->save();
            $disbursement_fees_transaction_id = $loan_transaction->id;
        }
        $loan->disbursement_charges = $disbursement_fees;
        $loan->save();
        //check if accounting is enabled
        if ($loan->accounting_rule == "cash" || $loan->accounting_rule == "accrual_periodic" || $loan->accounting_rule == "accrual_upfront") {
            //loan disbursal
            //credit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = 1;
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->transaction_number = 'L' . $disbursal_transaction_id;
            $journal_entry->location_id = $loan->location_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->fund_source_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_disbursement';
            $journal_entry->date = $loan->disbursed_on_date;
            $date = explode('-', $loan->disbursed_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $loan->principal;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
            //debit account
            $journal_entry = new JournalEntry();
            $journal_entry->created_by_id = 1;
            $journal_entry->transaction_number = 'L' . $disbursal_transaction_id;
            $journal_entry->payment_detail_id = $payment_detail->id;
            $journal_entry->location_id = $loan->location_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_portfolio_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_disbursement';
            $journal_entry->date = $loan->disbursed_on_date;
            $date = explode('-', $loan->disbursed_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $loan->principal;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
            //
            if ($disbursement_fees > 0) {
                //credit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = 1;
                $journal_entry->payment_detail_id = $payment_detail->id;
                $journal_entry->transaction_number = 'L' . $disbursement_fees_transaction_id;
                $journal_entry->location_id = $loan->location_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->income_from_fees_chart_of_account_id;
                $journal_entry->transaction_type = 'repayment_at_disbursement';
                $journal_entry->date = $loan->disbursed_on_date;
                $date = explode('-', $loan->disbursed_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->credit = $loan->principal;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
                //debit account
                $journal_entry = new JournalEntry();
                $journal_entry->created_by_id = 1;
                $journal_entry->transaction_number = 'L' . $disbursement_fees_transaction_id;
                $journal_entry->payment_detail_id = $payment_detail->id;
                $journal_entry->location_id = $loan->location_id;
                $journal_entry->currency_id = $loan->currency_id;
                $journal_entry->chart_of_account_id = $loan->fund_source_chart_of_account_id;
                $journal_entry->transaction_type = 'repayment_at_disbursement';
                $journal_entry->date = $loan->disbursed_on_date;
                $date = explode('-', $loan->disbursed_on_date);
                $journal_entry->month = $date[1];
                $journal_entry->year = $date[0];
                $journal_entry->debit = $loan->principal;
                $journal_entry->reference = $loan->id;
                $journal_entry->save();
            }
        }
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Disburse Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
    }

    if (!function_exists('get_pre_closure_interest_rate_calculation_rate_rules')) {
        function get_pre_closure_interest_rate_calculation_rate_rules()
        {
            return [
                'calculate_until_pre_closure_date' => trans('loan::general.calculate_until_pre_closure_date'),
                'calculate_until_rest_frequency_date' => trans('loan::general.calculate_until_rest_frequency_date'),
            ];
        }
    }

    if (!function_exists('get_advance_payments_adjustment_type')) {
        function get_advance_payments_adjustment_type()
        {
            return [
                'reduce_emi_amount' => trans('loan::general.reduce_emi_amount'),
                'reduce_number_of_installments' => trans('loan::general.reduce_number_of_installments'),
                'reschedule_next_repayments' => trans('loan::general.reschedule_next_repayments'),
            ];
        }
    }

    if (!function_exists('interest_calculation_compounding_on')) {
        function get_interest_calculation_compounding_on()
        {
            return [
                'none' => trans('accounting::core.none'),
                'fee' => trans_choice('accounting::core.fee', 1),
                'interest' => trans_choice('accounting::core.interest', 1),
                'fee_and_interest' => trans_choice('loan::general.fee_and_interest', 1),
            ];
        }
    }

    if (!function_exists('get_frequency_for_recalculate_outstanding_principal')) {
        function get_frequency_for_recalculate_outstanding_principal()
        {
            return [
                'same_as_repayment_period' => trans('loan::general.same_as_repayment_period'),
                'daily' => trans('accounting::core.daily'),
                'weekly' => trans('accounting::core.weekly'),
                'monthly' => trans('accounting::core.monthly'),
            ];
        }
    }
}
