<?php

namespace App\Http\Controllers;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\AccountType;
use App\AccountGroup;
use App\ContactGroup;
use App\Transaction;
use App\AccountTransaction;
use App\TransactionPayment;
use App\User;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LDAP\Result;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Entities\Package;
use Yajra\DataTables\Facades\DataTables;
use App\ExpenseCategory;

class PostdatedChequeController extends Controller
{
    protected $transactionUtil;
    protected $moduleUtil;
    protected $productUtil;
    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
    public function postDatedFilters(){
        $business_id = request()->session()->get('user.business_id');
        $sells = AccountTransaction::leftJoin(
                    'accounts as A',
                    'account_transactions.account_id',
                    '=',
                    'A.id'
                )
                ->leftJoin('transaction_payments AS tp', 'account_transactions.transaction_payment_id', '=', 'tp.id')
                
                ->leftJoin(
                    'accounts as Arelated',
                    'tp.related_account_id',
                    '=',
                    'Arelated.id'
                )
                
                ->leftJoin(
                    'transactions',
                    'transactions.id',
                    '=',
                    'account_transactions.transaction_id'
                )
                ->leftJoin('contacts', 'tp.payment_for', '=', 'contacts.id')
                ->leftJoin('users', 'tp.created_by', '=', 'users.id')
                ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->leftjoin(
                    'account_types as ats',
                    'A.account_type_id',
                    '=',
                    'ats.id' 
                )
                ->where('A.business_id', $business_id)
                ->where(function ($query) {
                    $query->where('account_transactions.post_dated_cheque',1)
                        ->orWhere('tp.post_dated_cheque', 1);
                })
                ->whereDate('tp.cheque_date', '>=', DB::raw('CURDATE()'))
                ->whereNull('account_transactions.deleted_at')
                ->select(
                    'tp.cheque_number',
                     'tp.amount'
                );
                
            // dd($sells);
             if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                    
                if(request()->post_party_type == 'others'){
                    $sells->where('A.id',$customer_id)->where('account_transactions.type','credit');
                }elseif(request()->post_party_type == 'expense_payments'){
                    $sells->where('transactions.expense_category_id', $customer_id);
                }else{
                    $sells->where('contacts.id', $customer_id);
                }
                
            }
            
            if (!empty(request()->post_party_type)) {
                if(request()->post_party_type == 'others'){
                    $sells->whereNull('contacts.id');
                }else if(request()->post_party_type != 'expense_payments'){
                    $type = request()->post_party_type;
                    $sells->where('contacts.type', $type);
                }
                
            }
            
            if (!empty(request()->bill_no)) {
                $sells->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->bank)) {
                $sells->where('account_transactions.account_id', request()->bank);
            }
            if (!empty(request()->cheque_number)) {
                $sells->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_method)) {
                $sells->where('tp.method', request()->payment_method);
            }

            if (!empty(request()->payment_amount)) {
                $sells->where('tp.amount', request()->payment_amount);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('tp.cheque_date', '>=', $start)
                    ->whereDate('tp.cheque_date', '<=', $end);
            }
            $sells->orderBy('tp.cheque_date', 'desc');
            
            if(request()->type == 'cheque_number'){
                $sells->groupBy('tp.cheque_number');
                return response()->json([
                    'data' => $sells->pluck('cheque_number')
                ]);
            }
            
            if(request()->type == 'amount'){
                $sells->groupBy('tp.amount');
                return response()->json([
                    'data' => $sells->pluck('amount')]);
            }
            
            
    }
    
    public function oldpostDatedFilters(){
        $business_id = request()->session()->get('user.business_id');
        $sells = AccountTransaction::leftJoin(
                    'accounts as A',
                    'account_transactions.account_id',
                    '=',
                    'A.id'
                )
                ->leftJoin('transaction_payments AS tp', 'account_transactions.transaction_payment_id', '=', 'tp.id')
                
                ->leftJoin(
                    'accounts as Arelated',
                    'tp.related_account_id',
                    '=',
                    'Arelated.id'
                )
                
                ->leftJoin(
                    'transactions',
                    'transactions.id',
                    '=',
                    'account_transactions.transaction_id'
                )
                ->leftJoin('contacts', 'tp.payment_for', '=', 'contacts.id')
                ->leftJoin('users', 'tp.created_by', '=', 'users.id')
                ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->leftjoin(
                    'account_types as ats',
                    'A.account_type_id',
                    '=',
                    'ats.id' 
                )
                ->where('A.business_id', $business_id)
                ->where(function ($query) {
                    $query->where('account_transactions.post_dated_cheque',1)
                        ->orWhere('tp.post_dated_cheque', 1);
                })
                ->whereDate('tp.cheque_date', '<=', DB::raw('CURDATE()'))
                ->whereNull('account_transactions.deleted_at')
                ->select(
                    'tp.cheque_number',
                     'tp.amount'
                );
                
            // dd($sells);
             if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                    
                if(request()->post_party_type == 'others'){
                    $sells->where('A.id',$customer_id)->where('account_transactions.type','credit');
                }elseif(request()->post_party_type == 'expense_payments'){
                    $sells->where('transactions.expense_category_id', $customer_id);
                }else{
                    $sells->where('contacts.id', $customer_id);
                }
                
            }
            
            if (!empty(request()->post_party_type)) {
                if(request()->post_party_type == 'others'){
                    $sells->whereNull('contacts.id');
                }else if(request()->post_party_type != 'expense_payments'){
                    $type = request()->post_party_type;
                    $sells->where('contacts.type', $type);
                }
                
            }
            
            if (!empty(request()->bill_no)) {
                $sells->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->bank)) {
                $sells->where('account_transactions.account_id', request()->bank);
            }
            if (!empty(request()->cheque_number)) {
                $sells->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_method)) {
                $sells->where('tp.method', request()->payment_method);
            }

            if (!empty(request()->payment_amount)) {
                $sells->where('tp.amount', request()->payment_amount);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('tp.cheque_date', '>=', $start)
                    ->whereDate('tp.cheque_date', '<=', $end);
            }
            $sells->orderBy('tp.cheque_date', 'desc');
            
            if(request()->type == 'cheque_number'){
                $sells->groupBy('tp.cheque_number');
                return response()->json([
                    'data' => $sells->pluck('cheque_number')
                ]);
            }
            
            if(request()->type == 'amount'){
                $sells->groupBy('tp.amount');
                return response()->json([
                    'data' => $sells->pluck('amount')]);
            }
            
            
    }
    
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        if (request()->ajax()) {
           $sells = AccountTransaction::leftJoin(
                    'accounts as A',
                    'account_transactions.account_id',
                    '=',
                    'A.id'
                )
                ->leftJoin('transaction_payments AS tp', 'account_transactions.transaction_payment_id', '=', 'tp.id')
                
                ->leftJoin(
                    'accounts as Arelated',
                    'tp.related_account_id',
                    '=',
                    'Arelated.id'
                )
                
                ->leftJoin(
                    'transactions',
                    'transactions.id',
                    '=',
                    'account_transactions.transaction_id'
                )
                ->leftJoin('contacts', 'tp.payment_for', '=', 'contacts.id')
                ->leftJoin('users', 'tp.created_by', '=', 'users.id')
                ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->leftjoin(
                    'account_types as ats',
                    'A.account_type_id',
                    '=',
                    'ats.id' 
                )
                ->where('A.business_id', $business_id)
                ->where(function ($query) {
                    $query->where('account_transactions.post_dated_cheque',1)
                        ->orWhere('tp.post_dated_cheque', 1);
                })
                ->whereDate('tp.cheque_date', '>=', DB::raw('CURDATE()'))
                ->whereNull('account_transactions.deleted_at')
                ->select(
                    'tp.note',
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'A.name as bank_name',
                    'Arelated.name as related_bank_name',
                    'contacts.name',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'business_locations.name as location_name',
                    'tp.id as tp_id',
                    'tp.cheque_date',
                    'tp.method',
                    'account_transactions.id as act_id',
                    'account_transactions.interest',
                    'tp.parent_id',
                    'tp.cheque_number',
                    'tp.card_number',
                    'tp.payment_ref_no',
                    'tp.paid_in_type',
                    'tp.created_by',
                     'users.username',
                     'tp.amount as total_paid',
                     'contacts.type as contact_type'
                );
                
            // dd($sells);
             if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                    
                if(request()->post_party_type == 'others'){
                    $sells->where('A.id',$customer_id)->where('account_transactions.type','credit');
                }elseif(request()->post_party_type == 'expense_payments'){
                    $sells->where('transactions.expense_category_id', $customer_id);
                }else{
                    $sells->where('contacts.id', $customer_id);
                }
                
            }
            
            if (!empty(request()->post_party_type)) {
                if(request()->post_party_type == 'others'){
                    $sells->whereNull('contacts.id');
                }else if(request()->post_party_type != 'expense_payments'){
                    $type = request()->post_party_type;
                    $sells->where('contacts.type', $type);
                }
                
            }
            
            if (!empty(request()->bill_no)) {
                $sells->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->bank)) {
                $sells->where('account_transactions.account_id', request()->bank);
            }
            if (!empty(request()->cheque_number)) {
                $sells->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_method)) {
                $sells->where('tp.method', request()->payment_method);
            }

            if (!empty(request()->payment_amount)) {
                $sells->where('tp.amount', request()->payment_amount);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('tp.cheque_date', '>=', $start)
                    ->whereDate('tp.cheque_date', '<=', $end);
            }
            
            $subscription = Subscription::active_subscription($business_id);
            $pacakge_details = $subscription->package_details;
            
            if(!empty($package_details) && !empty($package_details->post_dated_cheques_effective_date)){
                $sells->whereDate('tp.cheque_date', '>=', $package_details->post_dated_cheques_effective_date);
            }
            
            
            $sells->orderBy('tp.cheque_date', 'desc')->groupBy('tp.payment_ref_no');

            $datatable = DataTables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false"> @lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                </span>
                        </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                    <li><a href="{{action("CustomerPaymentController@viewPayment", [$tp_id])}}" class="view_payment_modal"><i class="fa fa-money" aria-hidden="true" ></i> @lang("purchase.view_payments")</a></li>
                    </ul></div>'
                )
                ->addColumn('payment_amount', function ($row) use ($business_details) {
                    if (!empty($row->parent_id)) {
                        $parent_payment = TransactionPayment::where('id', $row->parent_id)->first();
                        if (!empty($parent_payment)) {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $parent_payment->amount . '">' . $this->productUtil->num_f($parent_payment->amount, false, $business_details, false) . '</span>';
                        } else {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                        }
                    } else {
                        return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
                })->addColumn('name', function ($row) {
                    return $row->name;
                })
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('cheque_date', '{{@format_date($cheque_date)}}')
                ->editColumn('bank_name', function ($row) {
                    if(!empty($row->related_bank_name)){
                        return ucfirst($row->related_bank_name);
                    }else{
                        return ucfirst($row->bank_name);
                    }
                    
                })
                ->editColumn('note', function ($row)  {
                    $html = $row->note;
                    
                    if(!empty($html)){
                        return '<button type="button" class="btn btn-xs note_btn" style="background: #8F3A84; color:#fff;" data-string="' . $html . '">' . __('lang_v1.note') . '</button>';
                    
                    }else{
                        return '';
                    }
                    
                    
                   
                })
                ->editColumn('contact_type', function ($row) {
                    if(empty($row->contact_type)){
                        return __('account.others');
                    }
                    return ucfirst($row->contact_type);
                })
                
                ->editColumn('name', function ($row) {
                    if(empty($row->contact_type)){
                        if(!empty($row->related_bank_name)){
                            return ucfirst($row->related_bank_name);
                        }else{
                            return ucfirst($row->bank_name);
                        }
                    }
                    return ucfirst($row->name);
                })
                
                ->editColumn('cheque_number', function ($row) {
                    return $row->cheque_number;
                })
                ->setRowAttr([

                ]);
            $rawColumns = ['name', 'bank_name', 'action', 'payment_amount','note'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        
        $business_id = request()->session()->get('business.id');
        $contacts = Contact::contactDropdown($business_id, false, false);
        $suppliers = Contact::suppliersDropdown($business_id, false, true);
        $customers = Contact::customersDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $payment_types = $this->transactionUtil->payment_types();
        $banks = $this->getBankAccountByGroupName('Bank Account', $business_id, null);
        $package_manage = Package::where('only_for_business', $business_id)->first();
        $accounts = Account::forDropdown($business_id,false);
        
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');


        return view('postdated_cheques.index')->with(compact(
            'contacts',
            'business_locations',
            'banks',
            'suppliers',
            'customers',
            'accounts',
            'expense_categories'
        ));
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function oldPostDatedCheques()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        if (request()->ajax()) {
           $sells = AccountTransaction::leftJoin(
                    'accounts as A',
                    'account_transactions.account_id',
                    '=',
                    'A.id'
                )
                ->leftJoin('transaction_payments AS tp', 'account_transactions.transaction_payment_id', '=', 'tp.id')
                ->leftJoin(
                    'accounts as Arelated',
                    'tp.related_account_id',
                    '=',
                    'Arelated.id'
                )
            
                ->leftJoin(
                    'transactions',
                    'transactions.id',
                    '=',
                    'account_transactions.transaction_id'
                )
                ->leftJoin('contacts', 'tp.payment_for', '=', 'contacts.id')
                ->leftJoin('users', 'tp.created_by', '=', 'users.id')
                ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->leftjoin(
                    'account_types as ats',
                    'A.account_type_id',
                    '=',
                    'ats.id'
                )
                ->where('A.business_id', $business_id)
                ->where(function ($query) {
                    $query->where('account_transactions.post_dated_cheque',1)
                        ->orWhere('tp.post_dated_cheque', 1);
                })
                ->whereDate('tp.cheque_date', '<=', DB::raw('CURDATE()'))
                ->whereNull('account_transactions.deleted_at')
                ->select(
                    'tp.note',
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'A.name as bank_name',
                    'Arelated.name as related_bank_name',
                    'contacts.name',
                    'contacts.type as contact_type',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'business_locations.name as location_name',
                    'tp.id as tp_id',
                    'tp.cheque_date',
                    'tp.method',
                    'account_transactions.id as act_id',
                    'account_transactions.interest',
                    'tp.parent_id',
                    'tp.cheque_number',
                    'tp.card_number',
                    'tp.payment_ref_no',
                    'tp.paid_in_type',
                    'tp.created_by',
                    'users.username',
                    'tp.amount as total_paid'
                );
                
            // dd($sells);
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                    
                if(request()->post_party_type == 'others'){
                    $sells->where('A.id',$customer_id)->where('account_transactions.type','credit');
                }elseif(request()->post_party_type == 'expense_payments'){
                    $sells->where('transactions.expense_category_id', $customer_id);
                }else{
                    $sells->where('contacts.id', $customer_id);
                }
                
            }
            
            if (!empty(request()->post_party_type)) {
                if(request()->post_party_type == 'others'){
                    $sells->whereNull('contacts.id');
                }else if(request()->post_party_type != 'expense_payments'){
                    $type = request()->post_party_type;
                    $sells->where('contacts.type', $type);
                }
                
            }
            
            if (!empty(request()->bill_no)) {
                $sells->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->bank)) {
                $sells->where('account_transactions.account_id', request()->bank);
            }
            if (!empty(request()->cheque_number)) {
                $sells->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_method)) {
                $sells->where('tp.method', request()->payment_method);
            }

            if (!empty(request()->payment_amount)) {
                $sells->where('tp.amount', request()->payment_amount);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('tp.cheque_date', '>=', $start)
                    ->whereDate('tp.cheque_date', '<=', $end);
            }
            
            $subscription = Subscription::active_subscription($business_id);
            $pacakge_details = $subscription->package_details;
            
            if(!empty($package_details) && !empty($package_details->post_dated_cheques_effective_date)){
                $sells->whereDate('tp.cheque_date', '>=', $package_details->post_dated_cheques_effective_date);
            }
            
            $sells->orderBy('tp.cheque_date', 'desc')->groupBy('tp.payment_ref_no');

            $datatable = DataTables::of($sells)
                ->addColumn(
                    'action',
                    '<div class="btn-group">
                        <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                            data-toggle="dropdown" aria-expanded="false"> @lang("messages.actions")<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                </span>
                        </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                    <li><a href="{{action("CustomerPaymentController@viewPayment", [$tp_id])}}" class="view_payment_modal"><i class="fa fa-money" aria-hidden="true" ></i> @lang("purchase.view_payments")</a></li>
                    </ul></div>'
                )
                ->addColumn('payment_amount', function ($row) use ($business_details) {
                    if (!empty($row->parent_id)) {
                        $parent_payment = TransactionPayment::where('id', $row->parent_id)->first();
                        if (!empty($parent_payment)) {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $parent_payment->amount . '">' . $this->productUtil->num_f($parent_payment->amount, false, $business_details, false) . '</span>';
                        } else {
                            return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                        }
                    } else {
                        return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    }
                })
                
                ->editColumn('contact_type', function ($row) {
                    if(empty($row->contact_type)){
                        return __('account.others');
                    }
                    return ucfirst($row->contact_type);
                })
                
                ->editColumn('name', function ($row) {
                    if(empty($row->contact_type)){
                        if(!empty($row->related_bank_name)){
                            return ucfirst($row->related_bank_name);
                        }else{
                            return ucfirst($row->bank_name);
                        }
                    }
                    return ucfirst($row->name);
                })
                ->removeColumn('id')
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('cheque_date', '{{@format_date($cheque_date)}}')
                ->editColumn('bank_name', function ($row) {
                    if(!empty($row->related_bank_name)){
                        return ucfirst($row->related_bank_name);
                    }else{
                        return ucfirst($row->bank_name);
                    }
                    
                })
                ->editColumn('note', function ($row)  {
                    $html = $row->note;
                    
                    if(!empty($html)){
                        return '<button type="button" class="btn btn-xs note_btn" style="background: #8F3A84; color:#fff;" data-string="' . $html . '">' . __('lang_v1.note') . '</button>';
                    
                    }else{
                        return '';
                    }
                    
                    
                   
                })
                
                ->editColumn('cheque_number', function ($row) {
                    return $row->cheque_number;
                })
                ->setRowAttr([

                ]);
            $rawColumns = ['name', 'bank_name', 'action', 'payment_amount','note'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        
        $business_id = request()->session()->get('business.id');
        $contacts = Contact::contactDropdown($business_id, false, false);
        $suppliers = Contact::suppliersDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $payment_types = $this->transactionUtil->payment_types();
        $banks = $this->getBankAccountByGroupName('Bank Account', $business_id, null);
        $package_manage = Package::where('only_for_business', $business_id)->first();
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');


        return view('postdated_cheques.index')->with(compact(
            'contacts',
            'business_locations',
            'banks',
            'suppliers',
            'expense_categories'
        ));
    }


    public function getBankAccountByGroupName($group_name, $business_id, $location_id)
    { 
        if(!empty($group_name)){
                $group_id = AccountGroup::where('business_id', $business_id)->where('name', $group_name)->first();
                if (!empty($group_id)) {
                    $accounts = Account::where('business_id', $business_id)->where('asset_type', $group_id->id)->where('is_main_account', 0)->pluck('name', 'id');
                } else {
                    $accounts = [];
                }
            
        }else{
            $accounts = [];
        }
        return $accounts;
    } 
    
    public function partyType(){
        $cheque_type = request()->cheque_type;
        $business_id = request()->session()->get('business.id');
        
        if($cheque_type == 'customer'){
            $data = Contact::customersDropdown($business_id, false, true);
        }else if($cheque_type == 'supplier'){
            $data = Contact::suppliersDropdown($business_id, false, true);
        }else if($cheque_type == 'others'){
            $data = Account::forDropdown($business_id,false);
        }else if($cheque_type == 'expense_payments'){
            $data = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        }else{
            $data = [];
        }
        
        return response()->json([
            'data' => $data
        ]);

    }
    
    public function create(){
        $business_id = request()->session()->get('business.id');
        $contacts = Contact::contactDropdown($business_id, false, false);
        $suppliers = Contact::suppliersDropdown($business_id, false, true);
        $customers = Contact::customersDropdown($business_id, false, true);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $payment_types = $this->transactionUtil->payment_types();
        $banks = $this->getBankAccountByGroupName('Bank Account', $business_id, null);
        $package_manage = Package::where('only_for_business', $business_id)->first();
        $accounts = Account::forDropdown($business_id,false);
        
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');


        return view('postdated_cheques.create')->with(compact(
            'contacts',
            'business_locations',
            'banks',
            'suppliers',
            'customers',
            'accounts',
            'expense_categories'
        ));
    }
    
    public function store(Request $request){
        try {
                DB::beginTransaction();
                
                $business_id = $request->session()->get('user.business_id');
                
                $data = $request->all();
                
                // dd($data);
                
                $prefix_type = 'postdated_deposit';
                $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);
                $payment_ref_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);
                
                $contact_id = null;
                $expense_cat_id = null;
                
                if($data['post_party_type'] == 'customer' || $data['post_party_type'] == 'supplier'){
                    $contact_id = $data['post_dated_cheque_customer_id'];
                }
                
                if($data['post_party_type'] == 'expense_payments'){
                    $expense_cat_id = $data['post_dated_cheque_customer_id'];
                }
                
                $account_id = $data['account_id'];
                if($data['post_party_type'] == 'others'){
                    $account_id = $data['post_dated_cheque_customer_id'];
                }
                
                $loans_given = AccountGroup::getGroupByName('Loans Given', true);
                $loans_taken = AccountGroup::getGroupByName('Loans Taken', true);
                $loans_given_accs = Account::where('business_id', $business_id)->where('asset_type', $loans_given)->pluck('id');
                $loans_taken_accs = Account::where('business_id', $business_id)->where('asset_type', $loans_taken)->pluck('id');
                
                
                if($data['post_party_type'] == 'customer' || in_array($account_id,$loans_given_accs)){
                    $post_dated_acc = $this->transactionUtil->account_exist_return_id('Post Dated Cheques');
                    $equity_type = 'credit';
                    $postdated_type = 'debit';  
                }elseif($data['post_party_type'] == 'supplier' || $data['post_party_type'] == 'expense_payments' || in_array($account_id,$loans_taken_accs)){
                    $post_dated_acc = $this->transactionUtil->account_exist_return_id('Issued Post Dated Cheques');
                    $equity_type = 'debit';
                    $postdated_type = 'credit';
                }
                
                
                $transaction_data = array(
                    'business_id' => $business_id,
                    'type' => 'postdated_deposit',
                    'status' => 'final',
                    'contact_id' => $contact_id,
                    'expense_categories_id' => $expense_cat_id,
                    'ref_no' => $payment_ref_no,
                    'invoice_no' => $payment_ref_no,
                    'total_before_tax' => $data['amount'],
                    'transaction_date' => date('Y-m-d'),
                    'final_total' => $data['amount'],
                    'created_by' => auth()->user()->id
                );
                
                $transaction = Transaction::create($transaction_data);
                
                $payment_data = [
                    'amount' => $transaction->final_total,
                    'transaction_id' => $transaction->id,
                    'method' => 'cheque',
                    'business_id' => $business_id,
                    'cheque_date' => !empty($data['cheque_date']) ? $data['cheque_date'] : date('Y-m-d'),
                    'cheque_number' => $data['cheque_number'],
                    'bank_name' => $data['bank_name'],
                    'paid_on' => $transaction->transaction_date,
                    'created_by' =>  auth()->user()->id,
                    'payment_for' => $contact_id,
                    'payment_ref_no' => $payment_ref_no,
                    'account_id' => $account_id,
                    'post_dated_cheque' => 1,
                    'update_post_dated_cheque' => $data['update_post_dated_cheque'] ?? 0
                ];
                
                if(!empty($data['update_post_dated_cheque'])){
                    $payment_data['related_account_id'] = $account_id;
                    $payment_data['account_id'] = $post_dated_acc;
                }
                
                // create transaction payment
                $tp = TransactionPayment::create($payment_data);
                
                $account_transaction_data = [
                    'amount' =>  $data['amount'],
                    'account_id' => $account_id,
                    'type' => $postdated_type,
                    'transaction_payment_id' => $tp->id,
                    'contact_id' => $contact_id,
                    'cheque_date' => !empty($data['cheque_date']) ? $data['cheque_date'] : date('Y-m-d'),
                    'cheque_number' => $data['cheque_number'],
                    'bank_name' => $data['bank_name'],
                    'sub_type' => null,
                    'operation_date' => $transaction->transaction_date,
                    'created_by' => $transaction->created_by,
                    'transaction_id' =>  $transaction->id,
                    'note' => null,
                    'post_dated_cheque' => 1,
                    'update_post_dated_cheque' => $inputs['update_post_dated_cheque'] ?? 0,
                ];
                
                
                
                if(!empty($data['update_post_dated_cheque'])){
                    $account_transaction_data['related_account_id'] = $account_id;
                    $account_transaction_data['account_id'] = $post_dated_acc;
                }
                
                AccountTransaction::createAccountTransaction($account_transaction_data);
                
                
                // create opening balance entry
                if(!empty($data['opening_balance'])){
                    $account_transaction_data['account_id'] = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
                    $account_transaction_data['type'] = $equity_type;
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
                
                DB::commit();
                               
                $output = [
                    'success' => true,
                    'msg' => __("account.account_created_success")
                ];
                
                return redirect('/accounting-module/post-dated-cheques')->with('status', $output);
                
            } catch (\Exception $e) {
                logger($e);
                
                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
                
                return redirect()->back()->with('status', $output);
            }
    }
    
    public function show($id){
        
    }
    
    public function edit($id){
        
    }
    
    public function update(Request $request, $id){
        
    }
    public function destroy($id){
        
    }
}
