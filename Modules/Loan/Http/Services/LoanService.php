<?php

namespace Modules\Loan\Http\Services;

use App\Utils\Util;
use App\Utils\TransactionUtil;
;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use App\PaymentDetail;
use Modules\Loan\Entities\LoanCharge;
use Modules\Loan\Entities\LoanLinkedCharge;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Events\TransactionUpdated;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class LoanService
{
    public function storeOrUpdate($loan, array $data_array)
    {
        $data = (object) $data_array;
        $loan->currency_id = session('business.currency_id'); // $loan_product->currency_id
        $loan->loan_product_id = $data->loan_product_id; //$loan_product->id
        $loan->contact_type = $data->contact_type;
        $loan->contact_id = $data->contact_id; //$contact->id
        $loan->location_id = $data->location_id; //$contact->location_id
        $loan->loan_transaction_processing_strategy_id = $data->loan_transaction_processing_strategy_id; // $loan_product->loan_transaction_processing_strategy_id
        $loan->loan_purpose_id = $data->loan_purpose_id;
        $loan->loan_officer_id = $data->loan_officer_id;
        $loan->expected_disbursement_date = $data->expected_disbursement_date;
        $loan->expected_first_payment_date = $data->expected_first_payment_date;
        $loan->external_id = $data->external_id;
        $loan->created_by_id = Auth::id();
        $loan->variation_id = $data->variation_id;
        $loan->applied_amount = $data->applied_amount;
        $loan->loan_term = $data->loan_term;
        $loan->repayment_frequency = $data->repayment_frequency;
        $loan->repayment_frequency_type = $data->repayment_frequency_type;
        $loan->interest_rate = $data->interest_rate;
        $loan->interest_rate_type = $data->interest_rate_type; // $loan_product->interest_rate_type
        $loan->grace_on_principal_paid = $data->grace_on_principal_paid; // $loan_product->grace_on_principal_paid
        $loan->grace_on_interest_paid = $data->grace_on_interest_paid; // $loan_product->grace_on_interest_paid
        $loan->grace_on_interest_charged = $data->grace_on_interest_charged; // $loan_product->grace_on_interest_charged
        $loan->interest_methodology = $data->interest_methodology; // $loan_product->interest_methodology
        $loan->amortization_method = $data->amortization_method; // $loan_product->amortization_method
        $loan->submitted_on_date = date("Y-m-d");
        $loan->submitted_by_user_id = Auth::id();

        /**Accounting start */
        $loan->accounting_rule = $data->accounting_rule;

        if (in_array($data->accounting_rule, ['accrual_periodic', 'accrual_upfront'])) {
            $loan->interest_receivable_chart_of_account_id = $data->interest_receivable_chart_of_account_id;
            $loan->fees_receivable_chart_of_account_id = $data->fees_receivable_chart_of_account_id;
            $loan->penalties_receivable_chart_of_account_id = $data->penalties_receivable_chart_of_account_id;
        }

        if ($data->accounting_rule != 'none') {
            $loan->fund_source_chart_of_account_id = $data->fund_source_chart_of_account_id;
            $loan->loan_portfolio_chart_of_account_id = $data->loan_portfolio_chart_of_account_id;
            $loan->suspended_income_chart_of_account_id = $data->suspended_income_chart_of_account_id;
            $loan->transfer_in_suspense_chart_of_account_id = $data->transfer_in_suspense_chart_of_account_id;

            $loan->income_from_interest_chart_of_account_id = $data->income_from_interest_chart_of_account_id;
            $loan->income_from_penalties_chart_of_account_id = $data->income_from_penalties_chart_of_account_id;
            $loan->income_from_fees_chart_of_account_id = $data->income_from_fees_chart_of_account_id;
            $loan->income_from_recovery_chart_of_account_id = $data->income_from_recovery_chart_of_account_id;

            $loan->losses_written_off_chart_of_account_id = $data->losses_written_off_chart_of_account_id;
            $loan->interest_written_off_chart_of_account_id = $data->interest_written_off_chart_of_account_id;
            $loan->overpayments_chart_of_account_id = $data->overpayments_chart_of_account_id;
        }

        $loan->auto_disburse = $data->auto_disburse;
        /**Accounting end */

        $loan->save();
    }

    public function getLoanQuery($contact_id = null)
    {
        $orderBy = request()->order_by;
        $orderByDir = request()->order_by_dir;
        $status = request()->status;
        $contact_id = $contact_id ?: request()->contact_id;
        $loan_officer_id = request()->loan_officer_id;
        $loan_product_id = request()->loan_product_id;
        $location_id = request()->location_id;

        return Loan::with("contact")
            ->with("current_repayment_schedules")
            ->with("repayment_schedules")
            ->with("loan_product")
            ->with("business_location")
            ->with("loan_officer")
            ->forBusiness()
            ->when($contact_id, function ($query) use ($contact_id) {
                $query->where("loans.contact_id", $contact_id);
            })
            ->when($loan_officer_id, function ($query) use ($loan_officer_id) {
                $query->where("loans.loan_officer_id", $loan_officer_id);
            })
            ->when($loan_product_id, function ($query) use ($loan_product_id) {
                $query->where("loans.loan_product_id", $loan_product_id);
            })
            ->when($location_id, function ($query) use ($location_id) {
                $query->where("loans.location_id", $location_id);
            })
            ->when($status, function ($query) use ($status) {
                $query->forStatus($status);
            })
            ->when($orderBy, function (Builder $query) use ($orderBy, $orderByDir) {
                $query->orderBy($orderBy, $orderByDir);
            });
    }

    public static function getAverageLoanTenure()
    {
        $repayment_schedules = LoanRepaymentSchedule::join('loans', 'loans.id', 'loan_repayment_schedules.loan_id')
            ->join('loan_products', 'loan_products.id', 'loans.loan_product_id')
            ->join('users', 'loan_repayment_schedules.created_by_id', 'users.id')
            ->where('users.business_id', session('business.id'))
            ->select('loan_repayment_schedules.loan_id', 'loan_products.repayment_frequency_type')
            ->get();

        $loan_ids = $repayment_schedules->unique('loan_id')->pluck('loan_id');

        if (!count($loan_ids) > 0) {
            return 0;
        }

        $loan_terms = $loan_ids->map(function ($loan_id) use ($repayment_schedules) {
            $schedule = $repayment_schedules->where('loan_id', $loan_id)->first();
            $no_instalments = $repayment_schedules->where('loan_id', $loan_id)->count('instalment');
            $no_days = get_no_days()[$schedule->repayment_frequency_type];
            return $no_instalments * $no_days;
        })->toArray();

        return round(array_sum($loan_terms) / (max(count($loan_terms), 1)));
    }

    public static function getRepaymentDataPerMonth($months, $attribute)
    {
        $totals = collect(array_map(function ($month) use ($attribute) {
            $loans = Loan::whereHas('repayment_schedules', function ($q) use ($month) {
                $q->whereBetween('due_date', [$month->start, $month->end]);
            })->whereHas('business_location', function ($q) {
                $q->where('business_id', session('business.id'));
            })->get();

            $totals = $loans->map(function ($loan) use ($attribute) {
                return $loan->repayment_schedules->sum($attribute);
            });

            return $totals->sum();
        }, $months));

        return $totals;
    }

    public static function getLoansNotTakenUp()
    {
        return Loan::join('business_locations', 'business_locations.id', 'loans.location_id')
            ->where('business_locations.business_id', session('business.id'))
            ->where('loans.status', 'withdrawn')
            ->where('loans.approved_on_date', null)
            ->where('business_locations.business_id', session('business.id'))
            ->select('loans.id')
            ->get();
    }

    public function transferTransaction(Request $request, $id)
    {
        $loan = Loan::with('loan_product')->findOrFail($id);
        //payment details
        $payment_detail = new PaymentDetail();
        $payment_detail->created_by_id = Auth::id();
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->transaction_type = 'loan_transaction';
        $payment_detail->receipt = $request->receipt;
        $payment_detail->description = $request->description;
        $payment_detail->save();

        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->payment_detail_id = $payment_detail->id;
        $loan_transaction->name = 'Transfer to ' . $request->transfer_to;
        $loan_transaction->location_id = $loan->location_id;
        $loan_transaction->loan_transaction_type_id = 2;
        $loan_transaction->submitted_on = $request->date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $request->amount;
        $loan_transaction->debit = $request->amount;
        $loan_transaction->save();
        activity()->on($loan_transaction)
            ->withProperties(['id' => $loan_transaction->id])
            ->log('Create Loan Transfer');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
    }

    public function updateLoanStatus($id)
    {
        $loan_transactions = LoanTransaction::where('loan_id', $id)
            ->where('reversed', 0)
            ->get();

        $transaction_balance = $loan_transactions->sum('debit') - $loan_transactions->sum('credit');

        $excess = $transaction_balance < 0 ? abs($transaction_balance) : null;

        $loan = Loan::find($id);

        if ($excess && $loan->status != 'overpaid') {
            $loan->status = 'overpaid';
            $loan->save();
        } else if (!$excess && $loan->current_balance == 0) {
            $loan->status = 'fully_paid';
            $loan->save();
            //If the status is not active while the conditions above are not satisfied set loan as active
            //This can happen when a transaction is reversed making the loan no longer fully paid or overpaid 
        } else if ($loan->status != 'active') {
            $loan->status = 'active';
            $loan->save();
        }
    }

    public function getSubjectType($type)
    {
        $exploded = explode('\\', $type);
        return end($exploded);
    }

    public function getLoanActivities($loan_id)
    {
        $business_id = request()->session()->get('user.business_id');

        $loan_activities = Activity::with(['subject'])
            ->leftjoin('users as u', 'u.id', '=', 'activity_log.causer_id')
            ->leftjoin('loans', 'loans.id', '=', 'activity_log.subject_id')
            // ->where('activity_log.business_id', $business_id)
            ->where('subject_type', 'Modules\Loan\Entities\Loan')
            ->where('loans.id', $loan_id)
            ->select(
                'activity_log.*',
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as created_by")
            )->get();

        $loan_transaction_activities = Activity::with(['subject'])
            ->leftjoin('users as u', 'u.id', '=', 'activity_log.causer_id')
            ->leftjoin('loan_transactions', 'loan_transactions.loan_id', '=', 'activity_log.subject_id')
            // ->where('activity_log.business_id', $business_id)
            ->where('subject_type', 'Modules\Loan\Entities\LoanTransaction')
            // ->where('loan_transactions.loan_id', $loan_id)
            ->select(
                'activity_log.*',
                DB::raw("CONCAT(COALESCE(u.surname, ''), ' ', COALESCE(u.first_name, ''), ' ', COALESCE(u.last_name, '')) as created_by")
            )->get();

        $activities = $loan_activities->concat($loan_transaction_activities);

        return $activities;
    }

    public function storeLoanLinkedCharge(Request $request, $id)
    {
        try {
            DB::transaction(function () use ($request, $id) {
                $loan_charge = LoanCharge::findOrFail($request->loan_charge_id);
                $loan = Loan::with('repayment_schedules')->findOrFail($id);

                $loan_linked_charge = new LoanLinkedCharge();
                $loan_linked_charge->loan_id = $loan->id;
                $loan_linked_charge->name = $loan_charge->name;
                $loan_linked_charge->loan_charge_id = $loan_charge->id;
                if ($loan_charge->allow_override == 1) {
                    $loan_linked_charge->amount = $request->charge_amount;
                } else {
                    $loan_linked_charge->amount = $loan_charge->amount;
                }
                $loan_linked_charge->loan_charge_type_id = $loan_charge->loan_charge_type_id;
                $loan_linked_charge->loan_charge_option_id = $loan_charge->loan_charge_option_id;
                $loan_linked_charge->is_penalty = $loan_charge->is_penalty;
                $loan_linked_charge->save();
                //find schedule to apply this charge
                $repayment_schedule = $loan->repayment_schedules->where('due_date', '>=', $request->charge_date)->where('from_date', '<=', $request->charge_date)->first();
                if (empty($repayment_schedule)) {
                    if (\Carbon::parse($request->charge_date)->lessThan($loan->first_payment_date)) {
                        $repayment_schedule = $loan->repayment_schedules->first();
                    } else {
                        $repayment_schedule = $loan->repayment_schedules->last();
                    }
                }
                //calculate the amount
                if ($loan_linked_charge->loan_charge_option_id == 1) {
                    $amount = $loan_linked_charge->amount;
                }
                if ($loan_linked_charge->loan_charge_option_id == 2) {
                    $amount = round(($loan_linked_charge->amount * ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived) / 100), get_decimal_places());
                }
                if ($loan_linked_charge->loan_charge_option_id == 3) {
                    $amount = round(($loan_linked_charge->amount * (($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) + ($repayment_schedule->principal - $repayment_schedule->principal_repaid_derived - $repayment_schedule->principal_written_off_derived)) / 100), get_decimal_places());
                }
                if ($loan_linked_charge->loan_charge_option_id == 4) {
                    $amount = round(($loan_linked_charge->amount * ($repayment_schedule->interest - $repayment_schedule->interest_repaid_derived - $repayment_schedule->interest_waived_derived - $repayment_schedule->interest_written_off_derived) / 100), get_decimal_places());
                }
                if ($loan_linked_charge->loan_charge_option_id == 5) {
                    $amount = round(($loan_linked_charge->amount * ($loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->sum('principal_repaid_derived') - $loan->repayment_schedules->sum('principal_written_off_derived')) / 100), get_decimal_places());
                }
                if ($loan_linked_charge->loan_charge_option_id == 6) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), get_decimal_places());
                }
                if ($loan_linked_charge->loan_charge_option_id == 7) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), get_decimal_places());
                }

                $repayment_schedule->fees = $repayment_schedule->fees + $amount;
                $repayment_schedule->save();
                $loan_linked_charge->calculated_amount = $amount;
                //create transaction
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->name = trans_choice('loan::general.fee', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.applied', 1);
                $loan_transaction->loan_transaction_type_id = 10;
                $loan_transaction->submitted_on = $repayment_schedule->due_date;
                $loan_transaction->created_on = date("Y-m-d");
                $loan_transaction->amount = $loan_linked_charge->calculated_amount;
                $loan_transaction->due_date = $repayment_schedule->due_date;
                $loan_transaction->debit = $loan_linked_charge->calculated_amount;
                $loan_transaction->reversible = 1;
                $loan_transaction->save();
                $loan_linked_charge->loan_transaction_id = $loan_transaction->id;
                $loan_linked_charge->save();

                activity()->on($loan_transaction)
                    ->withProperties(['id' => $loan_transaction->id])
                    ->log('Create Loan Charge');
                //fire transaction updated event
                event(new TransactionUpdated($loan));
            });
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getActivityLog($loan_id)
    {
        if (request()->ajax()) {
            $activities = (new LoanService())->getLoanActivities($loan_id);

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $activities = $activities->where('created_at', '>=', $start)->where('created_at', '<=', $end);
            }

            if (!empty(request()->user_id)) {
                $activities = $activities->where('causer_id', request()->user_id);
            }

            return DataTables::of($activities)
                ->editColumn('created_at', function ($row) {
                    return readable_datetime($row->created_at);
                })
                ->addColumn('subject_type', function ($row) {
                    return (new LoanService())->getSubjectType($row->subject_type);
                })
                ->addColumn('note', function ($row) {
                    $html = '';

                    if (!empty($row->getExtraProperty('name'))) {
                        $html .= __('user.name') . ': ' . $row->getExtraProperty('name') . '<br>';
                    }

                    if (!empty($row->getExtraProperty('id'))) {
                        $html .= 'id: ' . $row->getExtraProperty('id') . '<br>';
                    }

                    return $html;
                })
                ->editColumn('description', function ($row) {
                    $description = __('lang_v1.' . $row->description);
                    // Remove "lang_v1." from string  
                    return str_replace('lang_v1.', '', $description);
                })
                ->rawColumns(['note'])
                ->toJson();
        }
    }

    public function getProRataPendingDues(array $data, $id)
    {
        $loan = Loan::with(['repayment_schedules' => function ($q) use ($data) {
            $q->where('due_date', '<=', $data['due_date']);
        }])->findOrFail($id);

        return json_encode([
            'total_principal' => number_format($loan->repayment_schedules->sum('total_principal'), 2),
            'total_interest' => number_format($loan->repayment_schedules->sum('total_interest'), 2),
            'total_fees' => number_format($loan->repayment_schedules->sum('total_fees'), 2),
            'total_penalties' => number_format($loan->repayment_schedules->sum('total_penalties'), 2),
            'amount_due' => number_format($loan->repayment_schedules->sum('amount_due'), 2),

            'principal_repaid_derived' => number_format($loan->repayment_schedules->sum('principal_repaid_derived'), 2),
            'interest_repaid_derived' => number_format($loan->repayment_schedules->sum('interest_repaid_derived'), 2),
            'fees_repaid_derived' => number_format($loan->repayment_schedules->sum('fees_repaid_derived'), 2),
            'penalties_repaid_derived' => number_format($loan->repayment_schedules->sum('penalties_repaid_derived'), 2),
            'total_paid' => number_format($loan->repayment_schedules->sum('total_paid'), 2),

            'principal_due' => number_format($loan->repayment_schedules->sum('principal_due'), 2),
            'interest_due' => number_format($loan->repayment_schedules->sum('interest_due'), 2),
            'fees_due' => number_format($loan->repayment_schedules->sum('fees_due'), 2),
            'penalties_due' => number_format($loan->repayment_schedules->sum('penalties_due'), 2),
            'current_balance' => number_format($loan->repayment_schedules->sum('balance_due'), 2),
        ]);
    }

    public function setExternalId($external_id)
    {
        if (!$external_id) {
            $ref_count = (new TransactionUtil)->setAndGetReferenceCount('loans');
            $external_id = (new Util)->generateReferenceNumber('loans', $ref_count);
        }
        return $external_id;
    }
}
