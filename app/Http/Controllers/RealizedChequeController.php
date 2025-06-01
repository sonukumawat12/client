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
use Modules\Superadmin\Entities\Package;
use Yajra\DataTables\Facades\DataTables;
;

class RealizedChequeController extends Controller
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
    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        if (request()->ajax()) {
           $business_id = session()->get('user.business_id');
            
            
            $cheque_account = Account::getAccountByAccountName('Cheques in Hand');
            $query = AccountTransaction::join('transaction_payments', 'account_transactions.transaction_payment_id', 'transaction_payments.id')
                ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
                ->leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')
                ->leftjoin('contacts', 'transaction_payments.payment_for', 'contacts.id')
                ->leftjoin('users','users.id','account_transactions.created_by')
                ->where('account_transactions.account_id','!=', $cheque_account->id)
                ->where('transaction_payments.method', 'cheque')
                ->where('account_transactions.type', 'debit')
                ->where('transaction_payments.is_deposited', 1)
                ->whereNull('transaction_payments.deleted_at')
                ->where('transaction_payments.is_realized', 1);
                
        
            $cheque_lists = $query->select(
                'contacts.name as customer_name',
                'transaction_payments.cheque_number',
                'transaction_payments.cheque_date',
                'accounts.name as bank_name',
                'account_transactions.amount',
                'transaction_payments.id',
                'transactions.id as t_id',
                'users.username',
                'transaction_payments.updated_at',
                'accounts.account_number'
            )->orderBy('transaction_payments.cheque_date','desc')->get();


            $datatable = DataTables::of($cheque_lists)
                ->editColumn('cheque_date', '{{@format_date($cheque_date)}}')
                ->editColumn('updated_at', '{{@format_datetime($updated_at)}}')
                ->editColumn('amount', '{{@num_format($amount)}}');
                
            $rawColumns = ['name', 'bank_name', 'action', 'payment_amount'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        $business_id = session()->get('user.business_id');
            
        
        return view('realized_cheques.index');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    
}
