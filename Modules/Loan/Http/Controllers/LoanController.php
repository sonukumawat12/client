<?php

namespace Modules\Loan\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Utils\Util;
use App\Variation;
use App\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Loan\Exports\LoanExport;
use App\Journal;
use App\PaymentDetail;
use App\PaymentMethod;
use App\Currency;
use Modules\Loan\Entities\Loan;
use Modules\Loan\Entities\LoanCharge;
use Modules\Loan\Entities\LoanHistory;
use Modules\Loan\Entities\LoanLinkedCharge;
use Modules\Loan\Entities\LoanOfficerHistory;
use Modules\Loan\Entities\LoanProduct;
use Modules\Loan\Entities\LoanPurpose;
use Modules\Loan\Entities\LoanRepaymentSchedule;
use Modules\Loan\Entities\LoanStatus;
use Modules\Loan\Entities\LoanTransaction;
use Modules\Loan\Entities\Product;
use Modules\Loan\Events\LoanStatusChanged;
use Modules\Loan\Events\TransactionUpdated;
use Modules\Accounting\Services\FlashService;
use Modules\Accounting\Services\AuthService;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use App\ChartOfAccount;
use App\User;
use Modules\Loan\Entities\LoanApprovalOfficer;
use Modules\Loan\Entities\LoanProductApprovalOfficer;
use Modules\Loan\Entities\LoanTransactionProcessingStrategy;
use Modules\Loan\Http\Services\LoanService;
use PDF;
use Yajra\DataTables\Facades\DataTables;

class LoanController extends Controller
{
    // use AuthService;
    private $loan_service;
    private $commonUtil;

    public function __construct(LoanService $loan_service, Util $commonUtil)
    {
        $this->loan_service = $loan_service;
        $this->commonUtil = $commonUtil;
    }

    private function getClientTypes()
    {
        return [
            'customer' => trans_choice('loan::general.customer', 1),
            'supplier' => trans_choice('loan::general.supplier', 1)
        ];
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index(Request $request)
    {
        $data = $this->loan_service->getLoanQuery()->get();
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));
        $loan_products = LoanProduct::forDropdown();
        $loan_officers = Loan::getLoanOfficerData();
        $has_loans_to_approve = LoanApprovalOfficer::hasLoansToApprove(Auth::id());
        $statuses = Loan::getStatusData();
        $contacts = Contact::has('loans')->get(['id', 'name']);

        return view('loan::loan.index', compact('data', 'has_loans_to_approve', 'business_locations', 'loan_products', 'loan_officers', 'statuses', 'contacts'));
    }

    public function get_loans()
    {
        $query = $this->loan_service->getLoanQuery()->get();

        return DataTables::of($query)
            ->editColumn('principal', function ($data) {
                return number_format($data->principal, $data->decimals);
            })
            ->editColumn('balance', function ($data) {
                return number_format(
                    $data->current_balance,
                    $data->decimals
                );
            })
            ->editColumn('status', function ($data) {
                return $data->status_label;
            })
            ->editColumn('id', function ($data) {
                return '<a href="' . url('contact_loan/' . $data->id . '/show') . '" class="">' . $data->id . '</a>';
            })
            ->editColumn('business_location', function ($data) {
                return '<a href="' . url('business_location/' . $data->business_location->id . '/show') . '" class="">' . $data->business_location->name . '</a>';
            })
            ->editColumn('contact', function ($data) {
                return '<a href="' . url('contact/' . $data->contact->id . '/show') . '" class="">' . $data->contact->name . '</a>';
            })
            ->editColumn('action', function ($data) {
                return '
                    <a href="' . url('contact_loan/' . $data->id . '/show') . '" class="btn btn-info">'
                    . trans_choice('accounting::core.detail', 2) .
                    '</a>
                ';
            })
            ->editColumn('loan_product', function ($data) {
                return $data->loan_product->name;
            })
            ->editColumn('loan_officer', function ($data) {
                return '<a href="' . url('contact_loan/' . $data->loan_officer->id . '/show') . '" class="">' . $data->loan_officer->full_name . '</a>';
            })
            ->rawColumns(['id', 'loan_officer', 'business_location', 'contact', 'status', 'action'])
            ->make(true);
    }

    /**    
     * Close an active loan  
     */
    public function close_loan(Request $request, $id)
    {
        $request->validate([
            'closed_notes' => ['required'],
        ]);

        $loan = Loan::findOrFail($id);
        $previous_status = $loan->status;
        $loan->closed_by_user_id = Auth::id();
        $loan->closed_on_date = date("Y-m-d");
        $loan->status = 'closed';
        $loan->closed_notes = $request->closed_notes;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Closed';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Closed Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    /**
     * Undo Close an active loan  
     
     */
    public function undo_loan_close($id)
    {
        $loan = Loan::findOrFail($id);
        $previous_status = $loan->status;
        $loan->closed_by_user_id = null;
        $loan->closed_on_date = null;
        $loan->status = 'active';
        $loan->closed_notes = null;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unclosed';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Close');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = session()->get('user.business_id');
        
        $contacts = Contact::where('business_id',$business_id)->pluck('name','id');
        $loan_products = Product::forBusiness()->productForSales()->with('variations')->with('product_locations')->pluck('name','id');
        $loan_purposes = LoanPurpose::forBusiness()->pluck('name','id');
        $users = User::where('business_id',$business_id)->pluck('first_name','id');

        //Get all business_locations that are associated with any given contact
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'))->pluck('name','id');

        $repayment_frequency_types = Loan::getRepaymentFrequencyTypes();
        $currentAssets = Account::getAccountByAccountTypeName('Current Assets');
        $fixedAssets = Account::getAccountByAccountTypeName('Fixed Assets');
        
        $currentLiabilities = Account::getAccountByAccountTypeName('Current Liabilities');
        $fixedLiabilities = Account::getAccountByAccountTypeName('Long Term Liabilities');
        
        $javascript_data = [ 
            'business_locations' => $business_locations,
            'repayment_frequency_types' => $repayment_frequency_types,
            'client_types' => $this->getClientTypes(),
            'amortization_methods' => Loan::getAmortizationMethods(),
            'loan_transaction_processing_strategies' => LoanTransactionProcessingStrategy::orderBy('id')->pluck('name','id'),
            'currency' => Currency::find(session('business.currency_id')),
            'assets' => $currentAssets->merge($fixedAssets),
            'expenses' => Account::getAccountByAccountTypeName('Expenses'),
            'income' => Account::getAccountByAccountTypeName('Income'),
            'liabilities' => $currentLiabilities->merge($fixedLiabilities),
            'variations' => Variation::whereHas('product', function ($query) {
                return $query->where('products.business_id', session('business.id'));
            })->pluck('name','id')
        ];

        $compact_data = compact('contacts', 'loan_products', 'loan_purposes', 'users');

        return view('loan::loan.create', array_merge($javascript_data, $compact_data));
    }

    public function get_approval_officers_for_product(Request $request)
    {
        $officer_ids = [];

        try {
            LoanProductApprovalOfficer::getOfficerIds($request->loan_product_id);
        } catch (\Exception $e) {
            return response()->json(trans('accounting::core.an_error_occurred'), 500);
        }

        return response()->json($officer_ids, 200);
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $request->validate([
            // Details
            'contact_type' => ['required'],
            'contact_id' => ['required'],
            'external_id' => ['nullable', Rule::unique('loans')->ignore('')],
            'location_id' => ['required'],
            'loan_product_id' => ['required'],
            // Terms
            'variation_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
            'interest_rate' => ['required', 'numeric'],
            'interest_rate_type' => ['required'],
            'loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_officer_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
            // Settings
            'loan_purpose_id' => ['required'],
            'loan_approval_officers' => ['required'],
            'grace_on_principal_paid' => ['nullable'],
            'grace_on_interest_paid' => ['nullable'],
            'grace_on_interest_charged' => ['nullable'],
            'interest_methodology' => ['required'],
            'amortization_method' => ['required'],
            'loan_transaction_processing_strategy_id' => ['required'],
            // Accounting
            'accounting_rule' => ['required'],
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
            'auto_disburse' => ['required'],
        ]);


        try {
            DB::beginTransaction();

            $loan = new Loan();

            $data = $request->except('_token');
            $data['external_id'] = (new LoanService)->setExternalId($request->external_id);
            (new LoanService)->storeOrUpdate($loan, $data);

            //Save the Loan Approval Officers if they exists for that particular loan product
            $officer_ids = explode(',', $request->loan_approval_officers);
            if (count($officer_ids) > 0) {
                LoanApprovalOfficer::addNew($officer_ids, $request->loan_product_id, $loan->id);
            }

            //Save loan history
            $loan_history = new LoanHistory();
            $loan_history->loan_id = $loan->id;
            $loan_history->created_by_id = Auth::id();
            $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
            $loan_history->action = 'Loan Created';
            $loan_history->save();

            //Save officer history
            $loan_officer_history = new LoanOfficerHistory();
            $loan_officer_history->loan_id = $loan->id;
            $loan_officer_history->created_by_id = Auth::id();
            $loan_officer_history->loan_officer_id = $request->loan_officer_id;
            $loan_officer_history->start_date = date("Y-m-d");
            $loan_officer_history->save();

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Create Loan');
        // (new FlashService())->onSave();
        //fire loan status changed event
        event(new LoanStatusChanged($loan));

        return redirect('contact_loan/' . $loan->id . '/show');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function reschedule_loan(Request $request, $id)
    {
        $request->validate([
            'rescheduled_from_date' => ['required', 'date'],
            'rescheduled_on_date' => ['required', 'date'],
            'rescheduled_first_payment_date' => ['required_if:reschedule_first_payment_date,on', 'date'],
            'reschedule_grace_on_principal_paid' => ['nullable', 'numeric'],
            'reschedule_grace_on_interest_paid' => ['nullable', 'numeric'],
            'reschedule_extra_installments' => ['required_if:reschedule_add_extra_installments,on', 'numeric'],
            'reschedule_interest_rate' => ['required_if:reschedule_adjust_loan_interest_rate,on', 'numeric'],
        ]);
        $loan = Loan::findOrFail($id);

        if (empty($loan->repayment_schedules->where('due_date', $request->rescheduled_from_date)->first())) {
            // (new FlashService())->onWarning(trans_choice("loan::general.no_installment_schedule_found", 1));
            return redirect()->back();
        }
        $reschedule_principal = $loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->where('due_date', '<', $request->rescheduled_from_date)->sum('principal');
        LoanRepaymentSchedule::where('due_date', '>=', $request->rescheduled_from_date)->where('loan_id', $loan->id)->delete();
        $interest_rate = determine_period_interest_rate($request->reschedule_interest_rate ?: $loan->interest_rate, $loan->repayment_frequency_type, $loan->interest_rate_type);
        $balance = round($reschedule_principal, 2);
        $period = $request->installment_amount > 0 ? round($balance / $request->installment_amount)
            : $loan->repayment_schedules->where('due_date', '>=', $request->rescheduled_from_date)->count() + $request->reschedule_extra_installments;
        $payment_from_date = $request->rescheduled_on_date;
        $next_payment_date = $request->rescheduled_first_payment_date ?: $loan->repayment_schedules->where('due_date', '>=', $request->rescheduled_from_date)->first()->due_date;
        $raw_period = $balance / max($request->installment_amount, 1);

        for ($i = 1; $i <= $period; $i++) {
            $loan_repayment_schedule = new LoanRepaymentSchedule();
            $loan_repayment_schedule->created_by_id = Auth::id();
            $loan_repayment_schedule->loan_id = $loan->id;
            $loan_repayment_schedule->installment = $i;
            $loan_repayment_schedule->due_date = $next_payment_date;
            $loan_repayment_schedule->from_date = $payment_from_date;
            $date = explode('-', $next_payment_date);
            $loan_repayment_schedule->month = $date[1];
            $loan_repayment_schedule->year = $date[0];
            //determine which method to usesummarytable
            if (!$request->installment_amount) {

                //flat method
                if ($loan->interest_methodology == 'flat') {
                    $principal = round($reschedule_principal / max($period, 1), 2);
                    $interest = round($interest_rate * $principal, 2);
                    if ($request->reschedule_grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, 2);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                //reducing balance
                if ($loan->interest_methodology == 'declining_balance') {
                    if ($loan->amortization_method == 'equal_installments') {
                        $amortized_payment = round(determine_amortized_payment($interest_rate, $reschedule_principal, $period), 2);
                        //determine if we have grace period for interest
                        $interest = round($interest_rate * $balance, 2);
                        $principal = round(($amortized_payment - $interest), 2);
                        if ($request->reschedule_grace_on_interest_charged >= $i) {
                            $loan_repayment_schedule->interest = 0;
                        } else {
                            $loan_repayment_schedule->interest = $interest;
                        }
                        if ($i == $period) {
                            //account for values lost during rounding
                            $loan_repayment_schedule->principal = round($balance, 2);
                        } else {
                            $loan_repayment_schedule->principal = $principal;
                        }
                        //determine next balance
                        $balance = ($balance - $principal);
                    }
                    if ($loan->amortization_method == 'equal_principal_payments') {
                        $principal = round($reschedule_principal / max($period, 1), 2);
                        //determine if we have grace period for interest
                        $interest = round($interest_rate * $balance, 2);
                        if ($request->reschedule_grace_on_interest_charged >= $i) {
                            $loan_repayment_schedule->interest = 0;
                        } else {
                            $loan_repayment_schedule->interest = $interest;
                        }
                        if ($i == $period) {
                            //account for values lost during rounding
                            $loan_repayment_schedule->principal = round($balance, 2);
                        } else {
                            $loan_repayment_schedule->principal = $principal;
                        }
                        //determine next balance
                        $balance = ($balance - $principal);
                    }
                }
            } else {
                //rounded off
                // $total_due = round($reschedule_principal / max($raw_period, 1), 2);
                // $principal = $i != $period ? round($total_due / (1 + $interest_rate)) : round($balance, 2);
                // $interest = $principal * $interest_rate;

                //not rounded off
                $total_due = round($reschedule_principal / max($raw_period, 1), 2);
                $principal = $i != $period ? round($total_due / (1 + $interest_rate)) : $balance;
                $interest = $i != $period ? $total_due - $principal : $principal * $interest_rate;

                if ($request->reschedule_grace_on_interest_charged >= $i) {
                    $loan_repayment_schedule->interest = 0;
                } else {
                    $loan_repayment_schedule->interest = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $loan_repayment_schedule->principal = round($balance, 2);
                } else {
                    $loan_repayment_schedule->principal = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }


            $payment_from_date = \Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            $next_payment_date = \Carbon::parse($next_payment_date)->add($loan->repayment_frequency, $loan->repayment_frequency_type)->format("Y-m-d");
            $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest;
            $loan_repayment_schedule->save();
        }

        $loan->load('repayment_schedules');
        $total_principal = $loan->repayment_schedules->sum('principal');
        $total_interest = $loan->repayment_schedules->sum('interest');
        foreach ($loan->charges->whereIn('loan_charge_type_id', [3, 2]) as $key) {
            //installment_fee
            $total_calculated_amount = 0;
            if ($key->loan_charge_type_id == 3) {
                if ($key->loan_charge_option_id == 1) {
                    $key->calculated_amount = $key->amount;
                } elseif ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                } elseif ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), 2);
                } elseif ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = round(($key->amount * $total_interest / 100), 2);
                } elseif ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                } elseif ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                } elseif ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), 2);
                }

                //reverse and create new transaction
                if (!empty($key->transaction)) {
                    $key->transaction->credit = $key->transaction->amount;
                    $key->transaction->debit = $key->transaction->amount;
                    $key->transaction->reversed = 1;
                    $key->transaction->save();
                }

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
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
                foreach ($loan->repayment_schedules->where('due_date', '>=', $request->rescheduled_from_date) as $loan_repayment_schedule) {
                    if ($key->loan_charge_option_id == 2) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->principal / 100), 2);
                    } elseif ($key->loan_charge_option_id == 3) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * ($loan_repayment_schedule->interest + $loan_repayment_schedule->principal) / 100), 2);
                    } elseif ($key->loan_charge_option_id == 4) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->interest / 100), 2);
                    } else {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + $key->calculated_amount;
                    }
                    $loan_repayment_schedule->total_due = $loan_repayment_schedule->principal + $loan_repayment_schedule->interest + $loan_repayment_schedule->fees;
                    $loan_repayment_schedule->save();
                }
            }
        }

        $loan->save();

        $loan->expected_maturity_date = $next_payment_date;
        $loan->rescheduled_on_date = $request->rescheduled_on_date;
        $loan->rescheduled_notes = $request->rescheduled_notes;
        $loan->rescheduled_by_user_id = Auth::id();
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Rescheduled';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Reschedule Loan');
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function show($id)
    {
        $business_id = session('business.id');

        $users = User::where('business_id',$business_id)->get();

        $payment_types = PaymentMethod::get();

        //Add calculated values to the loan object
        $loan = $this->get_loan_values($id);

        $transaction_balance = $loan->transactions->sum('debit') - $loan->transactions->sum('credit');

        $excess = $transaction_balance < 0 ? abs($transaction_balance) : null;

        $can_approve_loan = LoanApprovalOfficer::canApproveLoan($id, Auth::id());

        // $module_names = get_module_names();

        $currency = Currency::where('id', session('business.currency_id'))->firstOrFail(['code']);

        return view('loan::loan.show', compact('loan', 'payment_types', 'can_approve_loan', 'users', 'excess',  'currency'));
    }

    private function get_loan_values($id)
    {
        $loan = Loan::with('repayment_schedules')->with('transactions')->with('charges')->with('contact')
            ->with('loan_product.approval_officers')->with('approval_officers')->with('notes')->with('files')
            ->with('collateral')->with('collateral.collateral_type')->with('notes.created_by')
            ->with('business_location')
            ->with('variation')
            ->findOrFail($id);

        $balance = 0;
        $principal = $loan->repayment_schedules->sum('principal');
        $principal_waived = $loan->repayment_schedules->sum('principal_waived_derived');
        $principal_paid = $loan->repayment_schedules->sum('principal_repaid_derived');

        $principal_written_off = 0;
        $principal_outstanding = 0;
        $principal_overdue = 0;
        $interest = $loan->repayment_schedules->sum('interest');
        $interest_waived = $loan->repayment_schedules->sum('interest_waived_derived');
        $interest_paid = $loan->repayment_schedules->sum('interest_repaid_derived');
        $interest_written_off = $loan->repayment_schedules->sum('interest_written_off_derived');

        $interest_outstanding = 0;
        $interest_overdue = 0;
        $fees = $loan->repayment_schedules->sum('fees') + $loan->disbursement_charges;
        $fees_waived = $loan->repayment_schedules->sum('fees_waived_derived');
        $fees_paid = $loan->repayment_schedules->sum('fees_repaid_derived') + $loan->disbursement_charges;
        $fees_written_off = $loan->repayment_schedules->sum('fees_written_off_derived');

        $fees_outstanding = 0;
        $fees_overdue = 0;
        $penalties = $loan->repayment_schedules->sum('penalties');
        $penalties_waived = $loan->repayment_schedules->sum('penalties_waived_derived');
        $penalties_paid = $loan->repayment_schedules->sum('penalties_repaid_derived');
        $penalties_written_off = $loan->repayment_schedules->sum('penalties_written_off_derived');

        $penalties_outstanding = 0;
        $penalties_overdue = 0;

        $principal_outstanding = $principal - $principal_waived - $principal_paid - $principal_written_off;
        $interest_outstanding = $interest - $interest_waived - $interest_paid - $interest_written_off;
        $fees_outstanding = $fees - $fees_waived - $fees_paid - $fees_written_off;
        $penalties_outstanding = $penalties - $penalties_waived - $penalties_paid - $penalties_written_off;
        $balance = $principal_outstanding + $interest_outstanding + $fees_outstanding + $penalties_outstanding;

        $loan->balance = $balance;

        $loan->penalties = $penalties;
        $loan->penalties_waived = $penalties_waived;
        $loan->penalties_written_off = $penalties_written_off;
        $loan->penalties_paid = $penalties_paid;
        $loan->penalties_outstanding = $penalties_outstanding;
        $loan->penalties_overdue = $penalties_overdue;

        $loan->principal = $principal;
        $loan->principal_waived = $principal_waived;
        $loan->principal_paid = $principal_paid;
        $loan->principal_written_off = $principal_written_off;
        $loan->principal_outstanding = $principal_outstanding;
        $loan->principal_overdue = $principal_overdue;

        $loan->interest = $interest;
        $loan->interest_paid = $interest_paid;
        $loan->interest_waived = $interest_waived;
        $loan->interest_written_off = $interest_written_off;
        $loan->interest_overdue = $interest_overdue;
        $loan->interest_outstanding = $interest_outstanding;
        $loan->interest_overdue = $interest_overdue;

        $loan->fees = $fees;
        $loan->fees_paid = $fees_paid;
        $loan->fees_waived = $fees_waived;
        $loan->fees_written_off = $fees_written_off;
        $loan->fees_outstanding = $fees_outstanding;
        $loan->fees_overdue = $fees_overdue;

        return $loan;
    }

    public function edit($id)
    {
        $loan = Loan::with('contact')->with('loan_product')->findOrFail($id);
        $contacts = Contact::forBusiness()->active()->get();
        $loan_products = Product::forBusiness()->productForSales()->with('variations')->with('product_locations')->get();
        $loan_purposes = LoanPurpose::forBusiness()->get();
        $users = User::forBusiness()->loanOfficer()->get();

        //Get all business_locations that are associated with any given contact
        $business_locations = BusinessLocation::getDropdownCollection(session('business.id'));

        $repayment_frequency_types = Loan::getRepaymentFrequencyTypes();

        $javascript_data = [
            'business_locations' => $business_locations,
            'repayment_frequency_types' => $repayment_frequency_types,
            'client_types' => $this->getClientTypes(),
            'amortization_methods' => Loan::getAmortizationMethods(),
            'loan_transaction_processing_strategies' => LoanTransactionProcessingStrategy::orderBy('id')->get(),
            'currency' => Currency::find(session('business.currency_id')),
            'assets' => ChartOfAccount::forBusiness()->where('account_type', 'asset')->get(),
            'expenses' => ChartOfAccount::forBusiness()->where('account_type', 'expense')->get(),
            'income' => ChartOfAccount::forBusiness()->where('account_type', 'income')->get(),
            'liabilities' => ChartOfAccount::forBusiness()->where('account_type', 'liability')->get(),
            'variations' => Variation::whereHas('product', function ($query) {
                return $query->where('products.business_id', session('business.id'));
            })->get()
        ];

        $compact_data = compact('contacts', 'loan_products', 'loan_purposes', 'users', 'loan');

        return view('loan::loan.edit', array_merge($javascript_data, $compact_data));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            // Details
            'contact_type' => ['required'],
            'contact_id' => ['required'],
            'external_id' => ['required', Rule::unique('loans')->ignore($id)],
            'location_id' => ['required'],
            'loan_product_id' => ['required'],
            // Terms
            'variation_id' => ['required'],
            'applied_amount' => ['required', 'numeric'],
            'interest_rate' => ['required', 'numeric'],
            'interest_rate_type' => ['required'],
            'loan_term' => ['required', 'numeric'],
            'repayment_frequency' => ['required', 'numeric'],
            'repayment_frequency_type' => ['required'],
            'expected_disbursement_date' => ['required', 'date'],
            'loan_officer_id' => ['required'],
            'expected_first_payment_date' => ['required', 'date'],
            // Settings
            'loan_purpose_id' => ['required'],
            'loan_approval_officers' => ['required'],
            'grace_on_principal_paid' => ['nullable'],
            'grace_on_interest_paid' => ['nullable'],
            'grace_on_interest_charged' => ['nullable'],
            'interest_methodology' => ['required'],
            'amortization_method' => ['required'],
            'loan_transaction_processing_strategy_id' => ['required'],
            // Accounting
            'accounting_rule' => ['required'],
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
            'auto_disburse' => ['required'],
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $loan = Loan::findOrFail($id);
                (new LoanService)->storeOrUpdate($loan, $request->except('_token'));

                //Save the Loan Approval Officers if they exists for that particular loan product
                $officer_ids = explode(',', $request->loan_approval_officers);
                if (count($officer_ids) > 0) {
                    LoanApprovalOfficer::addNew($officer_ids, $request->loan_product_id, $loan->id);
                }

                activity()->on($loan)
                    ->withProperties(['id' => $loan->id])
                    ->log('Update Loan');
            });
        } catch (\Exception $e) {
            // return (new FlashService())->onException($e)->redirectBackWithInput($e);
        }
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $id . '/show');
    }

    public function export(Request $request)
    {
        $fileName = trans_choice('loan::general.loan_sheet', 1) . " (" . date('d-m-Y') . ")";
        $data = $this->loan_service->getLoanQuery()->get();
        $view = view('loan::loan.loan_sheet', compact('data'));

        switch ($request->type) {
            case 'csv':
                return Excel::download(new LoanExport($view), $fileName . '.csv');
                break;

            case 'excel':
                return Excel::download(new LoanExport($view), $fileName . '.xlsx');
                break;

            case 'excel_2007':
                return Excel::download(new LoanExport($view), $fileName . '.xls');
                break;

            case 'pdf':
                $pdf = PDF::loadView(theme_view_file('loan::loan.loan_sheet'), compact('data'));
                return $pdf->download($fileName . '.pdf');
                break;

            default:
                return back()->with('error', 'An unsupported format was chosen');
                break;
        }
    }

    public function approve_loan(Request $request, $id)
    {
        $request->validate([
            'approved_on_date' => ['required', 'date'],
        ]);

        //Save approval_status
        LoanApprovalOfficer::updateStatus($id, 'approved');

        //Save loan approval status
        $loan = Loan::with('loan_product.approval_officers')->findOrFail($id);
        $previous_status = $loan->status;
        $loan->approved_by_user_id = Auth::id();
        $loan->approved_amount = $request->approved_amount;
        $loan->approved_on_date = $request->approved_on_date;
        $loan->status = LoanApprovalOfficer::getApprovalStatus($id);
        $loan->approved_notes = $request->approved_notes;
        $loan->save();

        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Approved';
        $loan_history->save();

        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Approve Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function undo_approval(Request $request, $id)
    {
        //Save approval_status
        LoanApprovalOfficer::updateStatus($id, 'pending');

        $loan = Loan::findOrFail($id);
        $previous_status = $loan->status;
        $loan->approved_by_user_id = null;
        $loan->approved_amount = null;
        $loan->approved_on_date = null;
        $loan->status = 'submitted';
        $loan->approved_notes = null;
        $loan->save();

        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unapproved';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Approval');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function reject_loan(Request $request, $id)
    {
        $request->validate([
            'rejected_notes' => ['required'],
        ]);

        //Save approval_status
        LoanApprovalOfficer::updateStatus($id, 'rejected');

        $loan = Loan::findOrFail($id);
        $previous_status = $loan->status;
        $loan->rejected_by_user_id = Auth::id();
        $loan->rejected_on_date = date("Y-m-d");
        $loan->status = 'rejected';
        $loan->rejected_notes = $request->rejected_notes;
        $loan->save();

        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Rejected';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Reject Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function undo_rejection(Request $request, $id)
    {
        //Save approval_status
        LoanApprovalOfficer::updateStatus($id, 'pending');

        $loan = Loan::findOrFail($id);
        $previous_status = $loan->status;
        $loan->rejected_by_user_id = null;
        $loan->rejected_on_date = null;
        $loan->status = 'submitted';
        $loan->rejected_notes = null;
        $loan->save();

        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unrejected';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Rejection');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function withdraw_loan(Request $request, $id)
    {

        $request->validate([
            'withdrawn_notes' => ['required'],
        ]);

        //Save approval_status
        LoanApprovalOfficer::updateStatus($id, 'withdrawn');

        $loan = Loan::findOrFail($id);
        $previous_status = $loan->status;
        $loan->withdrawn_by_user_id = Auth::id();
        $loan->withdrawn_on_date = date("Y-m-d");
        $loan->status = 'withdrawn';
        $loan->withdrawn_notes = $request->withdrawn_notes;
        $loan->save();
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Withdrawn';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Withdraw Loan');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function undo_withdrawn(Request $request, $id)
    {
        //Save approval_status
        LoanApprovalOfficer::updateStatus($id, 'pending');

        $loan = Loan::findOrFail($id);
        $previous_status = $loan->status;
        $loan->withdrawn_by_user_id = null;
        $loan->withdrawn_on_date = null;
        $loan->status = 'submitted';
        $loan->withdrawn_notes = null;
        $loan->save();

        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unwithdrawn';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Withdrawal');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function write_off_loan(Request $request, $id)
    {
        $request->validate([
            'written_off_on_date' => ['required'],
            'written_off_notes' => ['required'],
        ]);
        $loan = Loan::with('repayment_schedules')->findOrFail($id);
        $principal = $loan->repayment_schedules->sum('principal') - $loan->repayment_schedules->sum('principal_written_off_derived') - $loan->repayment_schedules->sum('principal_repaid_derived');
        $interest = $loan->repayment_schedules->sum('interest') - $loan->repayment_schedules->sum('interest_written_off_derived') - $loan->repayment_schedules->sum('interest_repaid_derived') - $loan->repayment_schedules->sum('interest_waived_derived');
        $fees = $loan->repayment_schedules->sum('fees') - $loan->repayment_schedules->sum('fees_written_off_derived') - $loan->repayment_schedules->sum('fees_repaid_derived') - $loan->repayment_schedules->sum('fees_waived_derived');
        $penalties = $loan->repayment_schedules->sum('penalties') - $loan->repayment_schedules->sum('penalties_written_off_derived') - $loan->repayment_schedules->sum('penalties_repaid_derived') - $loan->repayment_schedules->sum('penalties_waived_derived');
        $balance = $principal + $interest + $fees + $penalties;
        $previous_status = $loan->status;
        $loan->written_off_by_user_id = Auth::id();
        $loan->written_off_on_date = date("Y-m-d");
        $loan->status = 'written_off';
        $loan->written_off_notes = $request->written_off_notes;
        $loan->save();

        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->name = trans_choice('loan::general.write_off', 1);
        $loan_transaction->loan_transaction_type_id = 6;
        $loan_transaction->submitted_on = $loan->written_off_on_date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $balance;
        $loan_transaction->credit = $balance;
        $loan_transaction->save();
        //check if accounting is enabled
        if ($loan->accounting_rule == "cash" || $loan->accounting_rule == "accrual_periodic" || $loan->accounting_rule == "accrual_upfront") {
            //credit account
            $journal_entry = new Journal();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'L' . $loan_transaction->id;
            $journal_entry->location_id = $loan->location_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->loan_portfolio_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_write_off';
            $journal_entry->date = $loan->written_off_on_date;
            $date = explode('-', $loan->written_off_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->credit = $balance;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
            //debit account
            $journal_entry = new Journal();
            $journal_entry->created_by_id = Auth::id();
            $journal_entry->transaction_number = 'L' . $loan_transaction->id;
            $journal_entry->location_id = $loan->location_id;
            $journal_entry->currency_id = $loan->currency_id;
            $journal_entry->chart_of_account_id = $loan->losses_written_off_chart_of_account_id;
            $journal_entry->transaction_type = 'loan_write_off';
            $journal_entry->date = $loan->written_off_on_date;
            $date = explode('-', $loan->written_off_on_date);
            $journal_entry->month = $date[1];
            $journal_entry->year = $date[0];
            $journal_entry->debit = $balance;
            $journal_entry->reference = $loan->id;
            $journal_entry->save();
        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Written off';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Writeoff Loan');
        event(new TransactionUpdated($loan));
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }



    public function undo_write_off($id)
    {
        $loan = Loan::findOrFail($id);
        $previous_status = $loan->status;
        $loan->written_off_by_user_id = null;
        $loan->written_off_on_date = null;
        $loan->status = 'active';
        $loan->written_off_notes = null;
        $loan->save();
        foreach (LoanTransaction::where('loan_id', $loan->id)->where('loan_transaction_type_id', 6)->where('reversed', 0)->get() as $key) {
            $key->amount = 0;
            $key->debit = $key->credit;
            $key->reversed = 1;
            $key->save();
        }
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Unwritten off';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan writeoff');
        event(new TransactionUpdated($loan));
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function change_loan_officer(Request $request, $id)
    {
        $request->validate([
            'loan_officer_id' => ['required'],
        ]);
        $loan = Loan::findOrFail($id);
        $previous_loan_officer_id = $loan->loan_officer_id;
        $loan->loan_officer_id = $request->loan_officer_id;
        $loan->save();
        if ($previous_loan_officer_id != $request->loan_officer_id) {
            $previous_loan_officer = LoanOfficerHistory::where('loan_id', $loan->id)->where('loan_officer_id', $request->loan_officer_id)->where('end_date', '')->first();
            if (!empty($previous_loan_officer)) {
                $previous_loan_officer->end_date = date("Y-m-d");
                $previous_loan_officer->save();
            }
            $loan_officer_history = new LoanOfficerHistory();
            $loan_officer_history->loan_id = $loan->id;
            $loan_officer_history->created_by_id = Auth::id();
            $loan_officer_history->loan_officer_id = $request->loan_officer_id;
            $loan_officer_history->start_date = date("Y-m-d");
            $loan_officer_history->save();
        }
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Change Loan Officer');
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function disburse_loan(Request $request, $id)
    {
        
        $request->validate([
            'disbursed_on_date' => ['required', 'date'],
            'first_payment_date' => ['required', 'date', 'after:disbursed_on_date'],
            'payment_type_id' => ['required'],
        ]);

        $loan = Loan::findOrFail($id);
        if ($loan->status != 'approved') {
            // (new FlashService())->onWarning(trans_choice('loan::general.loan', 1) . ' ' . trans_choice('accounting::core.not', 1) . ' ' . trans_choice('loan::general.approved', 1));
            return redirect()->back();
        }
        
        
        //payment details
        // $payment_detail = new PaymentDetail();
        // $payment_detail->created_by_id = Auth::id();
        // $payment_detail->payment_type_id = $request->payment_type_id;
        // $payment_detail->transaction_type = 'loan_transaction';
        // $payment_detail->cheque_number = $request->cheque_number;
        // $payment_detail->receipt = $request->receipt;
        // $payment_detail->account_number = $request->account_number;
        // $payment_detail->bank_name = $request->bank_name;
        // $payment_detail->routing_code = $request->routing_code;
        // $payment_detail->save();
        
        $previous_status = $loan->status;
        $loan->disbursed_by_user_id = Auth::id();
        $loan->disbursed_on_date = $request->disbursed_on_date;
        $loan->first_payment_date = $request->first_payment_date;
        $loan->principal = $loan->approved_amount;
        $loan->status = 'active';

        //prepare loan schedule
        //determine interest rate
        $interest_rate = determine_period_interest_rate($loan->interest_rate, $loan->repayment_frequency_type, $loan->interest_rate_type, $loan->repayment_frequency);
        $balance = round($loan->principal, 2);
        $period = ($loan->loan_term / $loan->repayment_frequency);
        $payment_from_date = $request->disbursed_on_date;
        $next_payment_date = $request->first_payment_date;
        $total_principal = 0;
        $total_interest = 0;

        for ($i = 1; $i <= $period; $i++) {
            $loan_repayment_schedule = new LoanRepaymentSchedule();
            $loan_repayment_schedule->created_by_id = Auth::id();
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
                $principal = round($loan->principal / $period, 2);
                $interest = round($interest_rate * $loan->principal, 2);
                if ($loan->grace_on_interest_charged >= $i) {
                    $loan_repayment_schedule->interest = 0;
                } else {
                    $loan_repayment_schedule->interest = $interest;
                }
                if ($i == $period) {
                    //account for values lost during rounding
                    $loan_repayment_schedule->principal = round($balance, 2);
                } else {
                    $loan_repayment_schedule->principal = $principal;
                }
                //determine next balance
                $balance = ($balance - $principal);
            }
            //reducing balance
            if ($loan->interest_methodology == 'declining_balance') {
                if ($loan->amortization_method == 'equal_installments') {
                    $amortized_payment = round(determine_amortized_payment($interest_rate, $loan->principal, $period), 2);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, 2);
                    $principal = round(($amortized_payment - $interest), 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, 2);
                    } else {
                        $loan_repayment_schedule->principal = $principal;
                    }
                    //determine next balance
                    $balance = ($balance - $principal);
                }
                if ($loan->amortization_method == 'equal_principal_payments') {
                    $principal = round($loan->principal / $period, 2);
                    //determine if we have grace period for interest
                    $interest = round($interest_rate * $balance, 2);
                    if ($loan->grace_on_interest_charged >= $i) {
                        $loan_repayment_schedule->interest = 0;
                    } else {
                        $loan_repayment_schedule->interest = $interest;
                    }
                    if ($i == $period) {
                        //account for values lost during rounding
                        $loan_repayment_schedule->principal = round($balance, 2);
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
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Disbursed';
        $loan_history->save();
        //add disbursal transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->location_id = $loan->location_id;
        // $loan_transaction->payment_detail_id = $payment_detail->id;
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
        $loan_transaction->created_by_id = Auth::id();
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
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = round(($key->amount * $total_interest / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $key->amount_paid_derived = $key->calculated_amount;
                    $key->is_paid = 1;
                    $disbursement_fees = $disbursement_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), 2);
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
                }
                if ($key->loan_charge_option_id == 2) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 3) {
                    $key->calculated_amount = round(($key->amount * ($total_interest + $total_principal) / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 4) {
                    $key->calculated_amount = round(($key->amount * $total_interest / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 5) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 6) {
                    $key->calculated_amount = round(($key->amount * $total_principal / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                if ($key->loan_charge_option_id == 7) {
                    $key->calculated_amount = round(($key->amount * $loan->principal / 100), 2);
                    $installment_fees = $installment_fees + $key->calculated_amount;
                }
                //create transaction
                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
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
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->principal / 100), 2);
                    } elseif ($key->loan_charge_option_id == 3) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * ($loan_repayment_schedule->interest + $loan_repayment_schedule->principal) / 100), 2);
                    } elseif ($key->loan_charge_option_id == 4) {
                        $loan_repayment_schedule->fees = $loan_repayment_schedule->fees + round(($key->amount * $loan_repayment_schedule->interest / 100), 2);
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
            $loan_transaction->created_by_id = Auth::id();
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
            $journal_entry = new Journal();
            $journal_entry->created_by_id = Auth::id();
            // $journal_entry->payment_detail_id = $payment_detail->id;
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
            $journal_entry = new Journal();
            $journal_entry->created_by_id = Auth::id();
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
                $journal_entry = new Journal();
                $journal_entry->created_by_id = Auth::id();
                // $journal_entry->payment_detail_id = $payment_detail->id;
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
                $journal_entry = new Journal();
                $journal_entry->created_by_id = Auth::id();
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
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }



    public function undo_disbursement($id)
    {
        $loan = Loan::findOrFail($id);
        $previous_status = $loan->status;
        $loan->disbursed_by_user_id = null;
        $loan->disbursed_on_date = null;
        $loan->status = 'approved';
        $loan->disbursed_notes = null;
        $loan->save();
        //destroy loan repayment schedules
        LoanLinkedCharge::where('loan_id', $loan->id)->update(["loan_transaction_id" => null]);
        LoanRepaymentSchedule::where('loan_id', $loan->id)->delete();
        LoanTransaction::where('loan_id', $loan->id)->delete();
        //reverse journal entries
        Journal::whereIn('transaction_type', ['repayment_at_disbursement', 'loan_disbursement', 'loan_repayment'])->where('reference', $loan->id)->update(["reversed" => 1]);
        $loan_history = new LoanHistory();
        $loan_history->loan_id = $loan->id;
        $loan_history->created_by_id = Auth::id();
        $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
        $loan_history->action = 'Loan Undisbursed';
        $loan_history->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Undo Loan Disbursement');
        //fire loan status changed event
        event(new LoanStatusChanged($loan, $previous_status));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    //transactions
    public function show_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('business_location')->with('loan.contact')->findOrFail($id);
        $contact = $loan_transaction->loan->contact;
        return view('loan::loan_transaction.show', compact('loan_transaction', 'contact'));
    }

    public function pdf_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('business_location')->with('loan.contact')->findOrFail($id);
        $contact = $loan_transaction->loan->contact;
        $is_pdf = true;
        $pdf = PDF::loadView(theme_view_file('loan::loan_transaction.pdf'), compact('loan_transaction', 'contact', 'is_pdf'));
        return $pdf->download(trans_choice('loan::general.transaction', 1) . ' ' . trans_choice('accounting::core.detail', 2) . ".pdf");
    }

    public function print_transaction($id)
    {
        $loan_transaction = LoanTransaction::with('payment_detail')->with('business_location')->with('loan.contact')->findOrFail($id);
        $contact = $loan_transaction->loan->contact;
        return view('loan::loan_transaction.print', compact('loan_transaction', 'contact'));
    }

    //schedules
    public function email_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->find($id);
        return view('loan::loan_schedule.email', compact('loan'));
    }

    public function pdf_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->findOrFail($id);
        $pdf = PDF::loadView(theme_view_file('loan::loan_schedule.pdf'), compact('loan'))->setPaper('a4', 'landscape');
        return $pdf->download(trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.emi', 1) . ".pdf");
    }

    public function print_schedule($id)
    {
        $loan = Loan::with('repayment_schedules')->findOrFail($id);
        return view('loan::loan_schedule.print', compact('loan'));
    }

    //Bulk Repayment
    public function bulk()
    {
        $payment_types = PaymentMethod::get();
        // dd($payment_types);
        $loans = Loan::leftJoin('contacts', 'contacts.id', 'loans.contact_id')
            ->forBusiness()
            ->active()
            ->selectRaw('CONCAT(contacts.name) as contact, loans.id')
            ->get();
        return view('loan::loan_repayment.bulk', compact('payment_types', 'loans'));
    }

    public function store_bulk_repayment(Request $request)
    {
        //All the input fields in the table
        $inputs = $request->except('_method', '_token');

        //Group the input fields by row
        $no_fields = 6; //Make sure this is the number of fields in each row
        $chunked_inputs = array_chunk($inputs, $no_fields, true);

        try {
            DB::beginTransaction();
            for ($i = 0; $i < count($chunked_inputs); $i++) {
                $row = $chunked_inputs[$i];
                $loan_id = $row["loan_id_$i"];
                $payment_type_id = $row["payment_type_id_$i"];
                $repayment_amount = $row["repayment_amount_$i"];
                $repayment_date = $row["repayment_date_$i"];
                $description = $row["description_$i"];

                //Payment details fields
                $receipt_number = $row["receipt_number_$i"];

                //Validation of the fields
                if (empty($loan_id) || empty($payment_type_id) || empty($repayment_amount)) {
                    continue;
                }

                $loan = Loan::findOrFail($loan_id);

                //payment details
                $payment_detail = new PaymentDetail();
                $payment_detail->created_by_id = Auth::id();
                $payment_detail->payment_type_id = $payment_type_id;
                $payment_detail->transaction_type = 'loan_transaction';
                $payment_detail->receipt = $receipt_number;
                $payment_detail->description = $description;
                $payment_detail->save();

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
                $loan_transaction->loan_id = $loan_id;
                $loan_transaction->payment_detail_id = $payment_detail->id;
                $loan_transaction->name = trans_choice('loan::general.repayment', 1);
                $loan_transaction->loan_transaction_type_id = 2;
                $loan_transaction->submitted_on = $repayment_date;
                $loan_transaction->created_on = date("Y-m-d");
                $loan_transaction->amount = $repayment_amount;
                $loan_transaction->credit = $repayment_amount;
                $loan_transaction->save();

                activity()->on($loan_transaction)
                    ->withProperties(['id' => $loan_transaction->id])
                    ->log('Create Loan Repayment');
                //fire transaction updated event
                event(new TransactionUpdated($loan));
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            // return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        // (new FlashService())->onSave();
        return back();
    }

    //repayments
    public function create_repayment($id)
    {
        $payment_types = PaymentMethod::get();
        $charges = LoanCharge::where('active', 1)->get();
        return view('loan::loan_repayment.create', compact('id', 'payment_types', 'charges'));
    }

    public function store_repayment(Request $request, $id)
    {
        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],

            'loan_charge_id' => ['required_if:apply_charge,1'],
            'charge_amount' => ['required_if:apply_charge,1'],
            'charge_date' => ['required_if:apply_charge,1'],
        ]);

        try {
            DB::transaction(function () use ($request, $id) {
                $loan = Loan::with('loan_product')->findOrFail($id);
                //payment details
                $payment_detail = new PaymentDetail();
                $payment_detail->created_by_id = Auth::id();
                $payment_detail->payment_type_id = $request->payment_type_id;
                $payment_detail->transaction_type = 'loan_transaction';
                $payment_detail->cheque_number = $request->cheque_number;
                $payment_detail->receipt = $request->receipt;
                $payment_detail->account_number = $request->account_number;
                $payment_detail->bank_name = $request->bank_name;
                $payment_detail->routing_code = $request->routing_code;
                $payment_detail->description = $request->description;
                $payment_detail->save();

                $loan_transaction = new LoanTransaction();
                $loan_transaction->created_by_id = Auth::id();
                $loan_transaction->loan_id = $loan->id;
                $loan_transaction->payment_detail_id = $payment_detail->id;
                $loan_transaction->name = trans_choice('loan::general.repayment', 1);
                $loan_transaction->location_id = $loan->location_id;
                $loan_transaction->loan_transaction_type_id = 2;
                $loan_transaction->submitted_on = $request->date;
                $loan_transaction->created_on = date("Y-m-d");
                $loan_transaction->amount = $request->amount;
                $loan_transaction->credit = $request->amount;
                $loan_transaction->save();

                (new LoanService())->updateLoanStatus($id);

                //Apply charge if any
                if ($request->has('apply_charge')) {
                    (new LoanService())->storeLoanLinkedCharge($request, $id);
                }

                activity()->on($loan_transaction)
                    ->withProperties(['id' => $loan_transaction->id])
                    ->log('Create Loan Repayment');
                //fire transaction updated event
                event(new TransactionUpdated($loan));
            });
        } catch (\Exception $e) {
            // return (new FlashService())->onException($e)->redirectBackWIthInput();
        }

        // (new FlashService())->onSave();

        return redirect('contact_loan/' . $id . '/show');
    }

    public function edit_repayment($id)
    {
        $loan_transaction = LoanTransaction::findOrFail($id);
        $payment_types = PaymentMethod::get();
        return view('loan::loan_repayment.edit', compact('loan_transaction', 'payment_types'));
    }

    public function update_repayment(Request $request, $id)
    {
        $request->validate([
            'amount' => ['required', 'numeric'],
            'date' => ['required', 'date'],
            'payment_type_id' => ['required'],
        ]);
        $loan_transaction = LoanTransaction::findOrFail($id);
        $loan = $loan_transaction->loan;
        //payment details
        $payment_detail = PaymentDetail::find($loan_transaction->payment_detail_id);
        $payment_detail->payment_type_id = $request->payment_type_id;
        $payment_detail->cheque_number = $request->cheque_number;
        $payment_detail->receipt = $request->receipt;
        $payment_detail->account_number = $request->account_number;
        $payment_detail->bank_name = $request->bank_name;
        $payment_detail->routing_code = $request->routing_code;
        $payment_detail->description = $request->description;
        $payment_detail->save();
        $loan_transaction->submitted_on = $request->date;
        $loan_transaction->amount = $request->amount;
        $loan_transaction->credit = $request->amount;
        $loan_transaction->save();
        activity()->on($loan_transaction)
            ->withProperties(['id' => $loan_transaction->id])
            ->log('Update Loan Repayment');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function waive_charge($id)
    {
        $loan_linked_charge = LoanLinkedCharge::with('loan')->with('transaction')->findOrFail($id);
        $loan_linked_charge->waived = 1;
        $loan_linked_charge->save();
        $loan = $loan_linked_charge->loan;
        $loan_transaction = $loan_linked_charge->transaction;
        $loan_transaction->credit = $loan_transaction->amount;
        $loan_transaction->debit = $loan_transaction->amount;
        $loan_transaction->reversed = 1;
        $loan_transaction->save();
        if ($loan_linked_charge->loan_charge_type_id == 2 || $loan_linked_charge->loan_charge_type_id == 4 || $loan_linked_charge->loan_charge_type_id == 6 || $loan_linked_charge->loan_charge_type_id == 2 || $loan_linked_charge->loan_charge_type_id == 7 || $loan_linked_charge->loan_charge_type_id == 8) {
            $repayment_schedule = LoanRepaymentSchedule::where('loan_id', $loan->id)->where('due_date', $loan_transaction->due_date)->first();
            if ($loan_linked_charge->is_penalty == 1) {
                $repayment_schedule->penalties_waived_derived = $repayment_schedule->penalties_waived_derived + $loan_linked_charge->calculated_amount;
            } else {
                $repayment_schedule->fees_waived_derived = $repayment_schedule->fees_waived_derived + $loan_linked_charge->calculated_amount;
            }
            $repayment_schedule->save();
        }
        if ($loan_linked_charge->loan_charge_type_id == 3) {
            $amount = 0;
            foreach ($loan->repayment_schedules as $repayment_schedule) {
                if ($loan_linked_charge->loan_charge_option_id == 1) {
                    $amount = $loan_linked_charge->calculated_amount;
                }
                if ($loan_linked_charge->loan_charge_option_id == 2) {
                    $amount = round(($loan_linked_charge->amount * $repayment_schedule->principal / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 3) {
                    $amount = round(($loan_linked_charge->amount * ($repayment_schedule->interest + $repayment_schedule->principal) / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 4) {
                    $amount = round(($loan_linked_charge->amount * $repayment_schedule->interest / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 5) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 6) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), 2);
                }
                if ($loan_linked_charge->loan_charge_option_id == 7) {
                    $amount = round(($loan_linked_charge->amount * $loan->principal / 100), 2);
                }
                $repayment_schedule->fees_waived_derived = $repayment_schedule->fees_waived_derived + $amount;
                $repayment_schedule->save();
            }
        }
        activity()->on($loan_linked_charge)
            ->withProperties(['id' => $loan_linked_charge->id])
            ->log('Waive Loan Charge');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function create_loan_linked_charge($id)
    {
        $loan = Loan::with('loan_product')->with('loan_product.charges')->with('loan_product.charges.charge')->findOrFail($id);
        $charges = LoanCharge::where('active', 1)->get(); // previously limited to charges where loan_charge_type_id == 1
        return view('loan::loan_linked_charge.create', compact('loan', 'charges'));
    }

    public function store_loan_linked_charge(Request $request, $id)
    {
        $request->validate([
            'charge_amount' => ['required'],
            'loan_charge_id' => ['required'],
            'charge_date' => ['required', 'date'],
        ]);

        try {
            (new LoanService())->storeLoanLinkedCharge($request, $id);
        } catch (\Exception $e) {
            // return (new FlashService())->onException($e)->redirectBackWithInput();
        }

        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $id . '/show');
    }

    public function waive_interest(Request $request, $id)
    {
        $loan = Loan::with('repayment_schedules')->findOrFail($id);
        $request->validate([
            'interest_waived_amount' => ['required'],
            'date' => ['required', 'date'],
        ]);

        //find schedule to apply this charge
        $repayment_schedule = $loan->repayment_schedules->where('due_date', '>=', $request->date)->where('from_date', '<=', $request->date)->first();
        if (empty($repayment_schedule)) {
            if (\Carbon::parse($request->date)->lessThan($loan->first_payment_date)) {
                $repayment_schedule = $loan->repayment_schedules->first();
            } else {
                $repayment_schedule = $loan->repayment_schedules->last();
            }
        }
        $amount = $request->interest_waived_amount;
        foreach ($loan->repayment_schedules->where('due_date', '>=', $repayment_schedule->due_date) as $repayment_schedule) {
            $interest = $repayment_schedule->interest
                - $repayment_schedule->interest_written_off_derived
                - $repayment_schedule->interest_repaid_derived
                - $repayment_schedule->interest_waived_derived;

            if ($interest <= 0) {
                continue;
            }
            if ($amount >= $interest) {
                $repayment_schedule->interest_waived_derived = $repayment_schedule->interest_waived_derived + $interest;
                $amount = $amount - $interest;
            } else {
                $repayment_schedule->interest_waived_derived = $repayment_schedule->interest_waived_derived + $amount;
                $amount = 0;
            }
            $repayment_schedule->save();
            if ($amount <= 0) {
                break;
            }
        }
        $repayment_schedule->fees = $repayment_schedule->fees + $amount;
        $repayment_schedule->save();
        //create transaction
        $loan_transaction = new LoanTransaction();
        $loan_transaction->created_by_id = Auth::id();
        $loan_transaction->loan_id = $loan->id;
        $loan_transaction->name = trans_choice('loan::general.waive', 1) . ' ' . $loan_transaction->name = trans_choice('loan::general.interest', 1);
        $loan_transaction->loan_transaction_type_id = 4;
        $loan_transaction->submitted_on = $request->date;
        $loan_transaction->created_on = date("Y-m-d");
        $loan_transaction->amount = $request->interest_waived_amount;
        $loan_transaction->credit = $request->interest_waived_amount;
        $loan_transaction->reversible = 0;
        $loan_transaction->save();
        activity()->on($loan)
            ->withProperties(['id' => $loan->id])
            ->log('Waive Loan Interest');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $id . '/show');
    }

    public function reverse_repayment($id)
    {
        $loan_transaction = LoanTransaction::findOrFail($id);
        $loan = $loan_transaction->loan;

        $loan_transaction->amount = 0;
        $loan_transaction->debit = $loan_transaction->credit;
        $loan_transaction->reversed = 1;
        $loan_transaction->save();

        (new LoanService())->updateLoanStatus($loan->id);

        activity()->on($loan_transaction)
            ->withProperties(['id' => $loan_transaction->id])
            ->log('Reverse Loan Repayment');
        //fire transaction updated event
        event(new TransactionUpdated($loan));
        // (new FlashService())->onSave();
        return redirect('contact_loan/' . $loan->id . '/show');
    }

    public function create_loan_calculator()
    {
        $loan_products = Product::forBusiness()->productForSales()->with('variations')->with('product_locations')->get();
        $currency = Currency::find(session('business.currency_id'));
        return view('loan::loan_calculator.create', compact('loan_products', 'currency'));
    }
    
    // public function 2{
    //     return 2;
    // }


    public function process_loan_calculator(Request $request)
    {
        $loan_product = Product::with('charges')->with('charges.charge')->findOrFail($request->loan_product_id);
        $loan_details = [];
        $loan_details['principal'] = $request->applied_amount;
        $loan_details['disbursement_date'] = $request->expected_disbursement_date;

        $schedules = [];
        $loan_principal = $request->applied_amount;
        $interest_rate = determine_period_interest_rate($request->interest_rate, $request->repayment_frequency_type, $request->interest_rate_type);
        $balance = round($loan_principal, 2);
        $period = ($request->loan_term / $request->repayment_frequency);
        $payment_from_date = $request->expected_disbursement_date;
        $next_payment_date = $request->expected_first_payment_date;
        $total_principal = 0;
        $total_interest = 0;
        for ($i = 1; $i <= $period; $i++) {
            $schedule = [];

            $schedule['installment'] = $i;
            $schedule['due_date'] = $next_payment_date;
            $schedule['from_date'] = $payment_from_date;
            $schedule['fees'] = 0;

            //flat method
            $principal = round($loan_principal / $period, 2);
            $interest = round($interest_rate * $loan_principal, 2);
            $schedule['interest'] = $interest;
            if ($i == $period) {
                //account for values lost during rounding
                $schedule['principal'] = round($balance, 2);
            } else {
                $schedule['principal'] = $principal;
            }

            //determine next balance
            $balance = ($balance - $principal);
            $payment_from_date = \Carbon::parse($next_payment_date)->add(1, 'day')->format("Y-m-d");
            $next_payment_date = \Carbon::parse($next_payment_date)->add($request->repayment_frequency, $request->repayment_frequency_type)->format("Y-m-d");
            $total_principal = $total_principal + $schedule['principal'];
            $total_interest = $total_interest + $schedule['interest'];
            $schedules[] = $schedule;
        }

        $installment_fees = 0;
        $disbursement_fees = 0;
        foreach ($loan_product->charges as $key) {
            //disbursement

            if ($key->charge->loan_charge_type_id == 1) {
                $amount = 0;
                if ($key->charge->loan_charge_option_id == 1) {
                    $amount = $key->charge->amount;
                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $amount = round(($key->charge->amount * $total_principal / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $amount = round(($key->charge->amount * $total_interest / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $amount = round(($key->charge->amount * $total_principal / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $amount = round(($key->charge->amount * $total_principal / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $amount = round(($key->charge->amount * $loan_principal / 100), 2);
                }
                $disbursement_fees = $disbursement_fees + $amount;
            }
            //installment_fee
            if ($key->charge->loan_charge_type_id == 3) {
                $amount = 0;
                if ($key->charge->loan_charge_option_id == 1) {
                    $amount = $key->charge->amount;
                }
                if ($key->charge->loan_charge_option_id == 2) {
                    $amount = round(($key->charge->amount * $total_principal / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 3) {
                    $amount = round(($key->charge->amount * ($total_interest + $total_principal) / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 4) {
                    $amount = round(($key->charge->amount * $total_interest / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 5) {
                    $amount = round(($key->charge->amount * $total_principal / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 6) {
                    $amount = round(($key->charge->amount * $total_principal / 100), 2);
                }
                if ($key->charge->loan_charge_option_id == 7) {
                    $amount = round(($key->charge->amount * $loan_principal / 100), 2);
                }
                $installment_fees = $installment_fees + $amount;
                //add the charges to the schedule
                foreach ($schedules as &$temp) {
                    if ($key->charge->loan_charge_option_id == 2) {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * $temp['principal'] / 100), 2);
                    } elseif ($key->charge->loan_charge_option_id == 3) {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * ($temp['interest'] + $temp['principal']) / 100), 2);
                    } elseif ($key->charge->loan_charge_option_id == 4) {
                        $temp['fees'] = $temp['fees'] + round(($key->charge->amount * $temp['interest'] / 100), 2);
                    } else {
                        $temp['fees'] = $temp['fees'] + $key->charge->amount;
                    }
                }
            }
        }
        $loan_details['total_interest'] = $total_interest;
        $loan_details['decimals'] = 2;
        $loan_details['disbursement_fees'] = $disbursement_fees;
        $loan_details['total_fees'] = $disbursement_fees + $installment_fees;
        $loan_details['total_due'] = $disbursement_fees + $installment_fees + $total_interest + $total_principal;
        $loan_details['maturity_date'] = $next_payment_date;
        activity()->log('Use Loan Calculator');
        return view('loan::loan_calculator.show', compact('loan_details', 'schedules'));
    }

    public function getImportLoan()
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $zip_loaded = extension_loaded('zip') ? true : false;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import'
            ];
            return view('loan::loan.import')->with('notification', $output);
        }

        $repayment_frequency_types = implode(', ', array_keys(Loan::getRepaymentFrequencyTypes()));

        $status_options = array_keys(Loan::getStatuses());

        $amortization_methods = array_keys(Loan::getAmortizationMethods());

        $transaction_processing_strategy_options = LoanTransactionProcessingStrategy::orderBy('id')->get();

        $instructions = [
            [
                'column_name' => trans_choice('accounting::core.contact', 1) . ' ' . trans_choice('accounting::core.type', 1),
                'required' => trans('lang_v1.required'),
                'instruction' => trans('loan::lang.contact_type_ins')
            ],
            [
                'column_name' => trans_choice('loan::general.location', 1) . ' ' . trans('accounting::core.id'),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('accounting::core.contact', 1) . ' ' . trans('accounting::core.id'),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('loan::general.loan_product', 1) . ' ' . trans('accounting::core.id'),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('loan::general.applied_amount', 1),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.term', 1),
                'required' => trans('lang_v1.required'),
            ],
            [
                'column_name' => trans_choice('loan::general.repayment', 1) . ' ' . trans_choice('loan::general.frequency', 1),
                'required' => trans('lang_v1.required'),
                'instruction' => __('loan::lang.tooltip_loancreateloanrepaymentfrequency')
            ],
            [
                'column_name' => trans_choice('loan::general.repayment', 1) . ' ' . trans_choice('loan::general.frequency', 1) . ' ' . trans_choice('loan::general.type', 1),
                'required' => trans('lang_v1.required'),
                'instruction' => trans_choice('loan::general.option', 2) . ': ' . $repayment_frequency_types
            ],
            [
                'column_name' => trans_choice('loan::general.interest', 1) . ' ' . trans_choice('loan::general.rate', 1),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('loan::general.interest', 1) . ' ' . trans_choice('loan::general.rate', 1) . ' ' . trans_choice('accounting::core.type', 1),
                'required' => trans('lang_v1.required'),
                'instruction' => __('loan::lang.tooltip_loan_productperterestratetype'),
            ],
            [
                'column_name' => trans_choice('loan::general.interest_methodology', 1),
                'required' => trans('lang_v1.required'),
                'instruction' => __('loan::lang.tooltip_loan_productinterestmethodology'),
            ],
            [
                'column_name' => trans_choice('loan::general.amortization_method', 1),
                'required' => trans('lang_v1.required'),
                'instruction' => __('loan::lang.tooltip_loan_productamortizationmethod') . ' ' . trans_choice('loan::general.option', 2) . ': ' . implode(', ', $amortization_methods),
            ],
            [
                'column_name' => trans_choice('loan::general.loan_transaction_processing_strategy', 2) . ' ' . trans('accounting::core.id'),
                'required' => trans('lang_v1.required'),
                'instruction' => __('loan::lang.tooltip_loan_productloantransactionprocessingstrategy') . '<br/>' .
                    $transaction_processing_strategy_options->map(function ($strategy) {
                        return $strategy->id . ' = ' . $strategy->name;
                    })->implode('<br/>')
            ],
            [
                'column_name' => trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.disbursement', 1) . ' ' . trans_choice('accounting::core.date', 1),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('loan::general.loan_officer', 1) . ' ' . trans('accounting::core.id'),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('loan::general.loan', 1) . ' ' . trans_choice('loan::general.purpose', 1) . ' ' . trans('accounting::core.id'),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('loan::general.expected', 1) . ' ' . trans_choice('loan::general.first_payment_date', 1),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('loan::general.status', 1),
                'required' => trans('lang_v1.required'),
                'instruction' => trans_choice('accounting::core.option', 2) . ': ' . implode(', ', $status_options)
            ],
            [
                'column_name' => trans_choice('loan::general.grace_on_principal_paid', 1),
                'required' => trans('lang_v1.optional'),
                'instruction' => __('loan::lang.tooltip_loan_productgraceonprincipalpayment')
            ],
            [
                'column_name' => trans_choice('loan::general.grace_on_interest_paid', 1),
                'required' => trans('lang_v1.optional'),
                'instruction' => __('loan::lang.tooltip_loan_productgraceoninterestpayment')
            ],
            [
                'column_name' => trans_choice('loan::general.grace_on_interest_charged', 1),
                'required' => trans('lang_v1.optional'),
                'instruction' => __('loan::lang.tooltip_loan_productgraceoninterestcharged')
            ],
        ];

        return view('loan::loan.import', compact('instructions'));
    }

    /**
     * Imports contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function postImportLoan(Request $request)
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $notAllowed = $this->commonUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('loan_csv')) {
                $file = $request->file('loan_csv');
                $parsed_array = Excel::toArray([], $file);
                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);
                $header_row = $parsed_array[0][0];
                $keys = get_keys_from_header_row($header_row);

                DB::beginTransaction();

                foreach ($imported_data as $index => $values) {
                    $data = array_combine($keys, $values);

                    $line_number = $index + 2; // + 2 because indexes are zero based, and first row is header
                    $loan_product = Product::find($data['loan_product_id']);
                    $contact = Contact::find($data['contact_id']);

                    if (!$loan_product) {
                        throw new \Exception(trans('accounting::core.resource_does_not_exist', [
                            'resource' => trans('loan::general.loan_product_id'),
                            'line_number' => $line_number
                        ]));
                    }

                    if (!$contact) {
                        throw new \Exception(trans('accounting::core.resource_does_not_exist', [
                            'resource' => trans('loan::general.contact_id'),
                            'line_number' => $line_number
                        ]));
                    }

                    $loan = new Loan();
                    $loan->currency_id = session('business.currency_id');
                    $loan->loan_product_id = $loan_product->id;
                    $loan->contact_id = $data['contact_id'];
                    $loan->location_id = $data['location_id'];
                    $loan->loan_transaction_processing_strategy_id = $data['loan_transaction_processing_strategy_id'];
                    $loan->loan_purpose_id = $data['loan_purpose_id'];
                    $loan->loan_officer_id = $data['loan_officer_id'];
                    $loan->expected_disbursement_date = excel_date_to_php_date($data['expected_disbursement_date']);
                    $loan->expected_first_payment_date = excel_date_to_php_date($data['expected_first_payment_date']);
                    $loan->created_by_id = Auth::id();
                    $loan->applied_amount = $data['applied_amount'];
                    $loan->loan_term = $data['loan_term'];
                    $loan->repayment_frequency = $data['repayment_frequency'];
                    $loan->repayment_frequency_type = $data['repayment_frequency_type'];
                    $loan->interest_rate = $data['interest_rate'];
                    $loan->interest_rate_type = $data['interest_rate_type'];
                    $loan->interest_methodology = $data['interest_methodology'];
                    $loan->amortization_method = $data['amortization_method'];
                    $loan->grace_on_principal_paid = $data['grace_on_principal_paid'] ?? 0;
                    $loan->grace_on_interest_paid = $data['grace_on_interest_paid'] ?? 0;
                    $loan->grace_on_interest_charged = $data['grace_on_interest_charged'] ?? 0;
                    $loan->auto_disburse = false;
                    $loan->submitted_on_date = date("Y-m-d");
                    $loan->submitted_by_user_id = Auth::id();
                    $loan->save();

                    //Save loan history
                    $loan_history = new LoanHistory();
                    $loan_history->loan_id = $loan->id;
                    $loan_history->created_by_id = Auth::id();
                    $loan_history->user = Auth::user()->first_name . ' ' . Auth::user()->last_name;
                    $loan_history->action = 'Loan Created';
                    $loan_history->save();

                    //Save officer history
                    $loan_officer_history = new LoanOfficerHistory();
                    $loan_officer_history->loan_id = $loan->id;
                    $loan_officer_history->created_by_id = Auth::id();
                    $loan_officer_history->loan_officer_id = $data['loan_officer_id'];
                    $loan_officer_history->start_date = date("Y-m-d");
                    $loan_officer_history->save();

                    //Save the Loan Approval Officers if they exists for that particular loan product
                    $officer_ids = explode(',', $data['loan_officer_id']);
                    if (count($officer_ids) > 0) {
                        LoanApprovalOfficer::addNew($officer_ids, $data['loan_product_id'], $loan->id);
                    }
                }

                // approveLoan($data, $loan->id);

                // disburseLoan($data, $loan->id);

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // (new FlashService())->onException($e, $e->getMessage());
            return redirect('contact_loan/import');
        }

        // (new FlashService())->onSave();
        return redirect('contact_loan');
    }

    public function bulk_import_repayments()
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }

        $payment_types_instructions = $this->commonUtil->payment_types(null, false, session('business.id'));

        $zip_loaded = extension_loaded('zip') ? true : false;

        //Check if zip extension it loaded or not.
        if ($zip_loaded === false) {
            $output = [
                'success' => 0,
                'msg' => 'Please install/enable PHP Zip archive for import'
            ];

            return view('loan::deposit.bulk_import')->with('notification', $output);
        }

        //keys: column_name, required, instruction(optional)
        $instructions = [
            [
                'column_name' => trans_choice('loan::general.loan', 1) . ' ' . trans('accounting::core.id'),
                'required' => trans('lang_v1.required'),
            ],
            [
                'column_name' => trans_choice('accounting::core.method', 1),
                'required' => trans('lang_v1.required'),
                'instruction' => collect($payment_types_instructions)->map(function ($value, $key) {
                    return $key . ' = ' . $value;
                })->implode('<br>')
            ],
            [
                'column_name' => trans_choice('accounting::core.amount', 1),
                'required' => trans('lang_v1.required')
            ],
            [
                'column_name' => trans_choice('accounting::core.repayment', 1) . ' ' . trans('accounting::core.date'),
                'required' => trans('lang_v1.required'),
            ],
            [
                'column_name' => trans_choice('accounting::core.description', 1),
                'required' => trans('lang_v1.optional')
            ],
        ];

        return view('loan::loan_repayment.import', compact('instructions'));
    }

    /**
     * Imports contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function store_import_repayments(Request $request)
    {
        if (!auth()->user()->can('supplier.create') && !auth()->user()->can('customer.create')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $notAllowed = $this->commonUtil->notAllowedInDemo();
            if (!empty($notAllowed)) {
                return $notAllowed;
            }

            //Set maximum php execution time
            ini_set('max_execution_time', 0);

            if ($request->hasFile('bulk_loan_csv')) {
                $file = $request->file('bulk_loan_csv');

                $parsed_array = Excel::toArray([], $file);
                $header_row = $parsed_array[0][0];
                $keys = get_keys_from_header_row($header_row);

                //Remove header row 
                $imported_data = array_splice($parsed_array[0], 1);

                DB::beginTransaction();

                foreach ($imported_data as $index => $values) {
                    $data = array_combine($keys, $values);
                    $line_number = $index + 2; // + 2 because indexes are zero based, and first row is header

                    $loan_id = trim($data["loan_id"]);
                    $deposit_amount = trim($data["amount"]);
                    $payment_type_id = trim($data["payment_type_id"]);
                    $repayment_date = excel_date_to_php_date($data["repayment_date"]);
                    $description = trim($data["description"]) ?: trans('loan::general.no_description');  //Set default value for description if none is provided
                    $receipt_number = trim($data["transaction_reference"]);
                    $loan = Loan::with('loan_product')->find($loan_id);

                    if (!$loan) {
                        throw new \Exception(trans('accounting::core.resource_does_not_exist', [
                            'resource' => trans('loan::general.loan_product_id'),
                            'line_number' => $line_number,
                        ]));
                    }

                    if (empty($loan)) {
                        throw new \Exception(trans('loan::general.loan_product_not_found'));
                    }

                    //payment details
                    $payment_detail = new PaymentDetail();
                    $payment_detail->created_by_id = Auth::id();
                    $payment_detail->payment_type_id = $payment_type_id;
                    $payment_detail->transaction_type = 'loan_transaction';
                    $payment_detail->receipt = $receipt_number;
                    $payment_detail->routing_code = $receipt_number;
                    $payment_detail->description = $description;
                    $payment_detail->save();

                    $loan_transaction = new LoanTransaction();
                    $loan_transaction->created_by_id = Auth::id();
                    $loan_transaction->loan_id = $loan_id;
                    $loan_transaction->payment_detail_id = $payment_detail->id;
                    $loan_transaction->name = trans_choice('loan::general.repayment', 1);
                    $loan_transaction->loan_transaction_type_id = 2;
                    $loan_transaction->submitted_on = $repayment_date;
                    $loan_transaction->created_on = date("Y-m-d");
                    $loan_transaction->amount = $deposit_amount;
                    $loan_transaction->credit = $deposit_amount;
                    $loan_transaction->save();

                    activity()->on($loan_transaction)
                        ->withProperties(['id' => $loan_transaction->id])
                        ->log('Create Loan Repayment');
                    //fire transaction updated event
                    event(new TransactionUpdated($loan));
                }

                DB::commit();
            }
        } catch (\Exception $e) {
            DB::rollBack();
            // (new FlashService())->onException($e, $e->getMessage());
            return redirect('contact_loan/bulk_import_repayments');
        }

        // (new FlashService())->onSave();
        return redirect('contact_loan');
    }

    public function activity_log($loan_id)
    {
        return (new LoanService())->getActivityLog($loan_id);
    }

    public function get_pending_dues(Request $request, $id)
    {
        $request->validate([
            'due_date' => ['required']
        ]);

        return (new LoanService())->getProRataPendingDues($request->all(), $id);
    }

    public function status()
    {
        $loan_statuses = LoanStatus::forBusiness()->get();
        return view('loan::loan_status.index', compact('loan_statuses'));
    }

    public function create_status()
    {
        $parent_statuses = Loan::getParentStatuses();
        return view('loan::loan_status.create', compact('parent_statuses'));
    }

    public function store_status(Request $request)
    {
        $input = $request->validate([
            'name' => ['required'],
            'parent_status' => ['required'],
        ]);

        $input['business_id'] = session('business.id');

        $input['active'] = $request->has('active');

        try {
            LoanStatus::create($input);
            // (new FlashService())->onSave();
        } catch (\Exception $e) {
            // return (new FlashService())->onException($e)->redirectBackWIthInput();
        }

        return redirect('contact_loan/status');
    }

    public function edit_status(Request $request, $id)
    {
        $parent_statuses = Loan::getParentStatuses();
        $loan_status = LoanStatus::findOrFail($id);
        return view('loan::loan_status.edit', compact('loan_status', 'parent_statuses'));
    }

    public function update_status(Request $request, $id)
    {
        $input = $request->validate([
            'name' => ['required'],
            'parent_status' => ['required'],
        ]);

        $input['active'] = $request->has('active');

        try {
            LoanStatus::findOrFail($id)->update($input);
            // (new FlashService())->onSave();
        } catch (\Exception $e) {
            // return (new FlashService())->onException($e)->redirectBackWIthInput();
        }

        return redirect('contact_loan/status');
    }

    public function destroy_status($id)
    {
        try {
            LoanStatus::destroy($id);
            // (new FlashService())->onDelete();
        } catch (\Exception $e) {
            // return (new FlashService())->onException($e)->redirectBackWIthInput();
        }

        return redirect('contact_loan/status');
    }
}
