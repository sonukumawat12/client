<?php

namespace App\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountTransaction;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
use App\Utils\Util;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

use Modules\Petro\Entities\Settlement;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\SettlementShortagePayment;

class ContactCreditSales extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $businessUtil;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil,BusinessUtil $businessUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {

        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil = $businessUtil;
    }
    
    public function index(){
        $business_id = request()->session()->get('user.business_id');
        
        if (request()->ajax()) {
            $customer_id = request()->input('customer_id');
            $invoice_no = request()->input('invoice_no');
            $location_id = request()->input('location_id');
            $start_date = request()->input('start_date');
            $end_date = request()->input('end_date');
            $id = $this->moduleUtil->account_exist_return_id('Accounts Receivable');
            
            $accounts = AccountTransaction::join('transactions','transactions.id','account_transactions.transaction_id')
                                        ->leftjoin('contacts','contacts.id','transactions.contact_id')
                                        ->where('account_transactions.business_id', $business_id)
                                        ->whereNull('account_transactions.deleted_at')
                                        ->where('account_transactions.account_id', $id);
                                        
            if (!empty($start_date) && !empty($end_date)) {
                $accounts->whereBetween(DB::raw('date(account_transactions.operation_date)'), [$start_date, $end_date]);
            }
            
            if(!empty($customer_id)){
                $accounts->where('transactions.contact_id',$customer_id);
            }
            
            
            if(!empty($location_id)){
                $accounts->where('transactions.location_id',$location_id);
            }
            
            if(!empty($invoice_no)){
                $accounts->where('transactions.invoice_no',$invoice_no);
            }
            
            $accounts->select(['account_transactions.operation_date as transaction_date','transactions.invoice_no','account_transactions.amount','contacts.name as customer_name','transactions.sub_type','transactions.payment_status','transactions.id'])->get();
            
            return DataTables::of($accounts)
            ->editColumn('customer_name',function($row){
                
                if($row->sub_type == 'shortage'){
                    $settlement = Settlement::where('settlement_no',$row->invoice_no)->first();
                    
                    if(!empty($settlement)){
                        $shortage = PumpOperator::find($settlement->pump_operator_id);
                        
                        return __('contact_credit_sales.pumper')." ".(!empty($shortage) ? $shortage->name : "")." ".__('contact_credit_sales.shortage');
                        
                    }
                        
                }else if($row->sub_type == 'excess'){
                    $settlement = Settlement::where('settlement_no',$row->invoice_no)->first();
                    
                    if(!empty($settlement)){
                        $shortage = PumpOperator::find($settlement->pump_operator_id);
                        
                        return __('contact_credit_sales.pumper')." ".(!empty($shortage) ? $shortage->name : "")." ".__('contact_credit_sales.excess');
                        
                    }
                }
                else{
                    return $row->customer_name;
                }
            })
            ->editColumn('payment_status',function($row){
                $payment_status = Transaction::getPaymentStatus($row);
                logger($payment_status);
                return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id]);
            })
            ->editColumn('amount','<span data-orig-value="{{$amount}}" class="final-total">{{@num_format($amount)}}</span>')
            ->editColumn('transaction_date','{{@format_date($transaction_date)}}')
            ->rawColumns(['amount','payment_status'])
            ->make(true);
                                    
            
        }
        
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        $invoices = Transaction::where('is_credit_sale',1)->where('business_id',$business_id)->pluck('invoice_no','invoice_no');
        
        return view('contact_credit_sales.index')
            ->with(compact('invoices','customers','business_locations'));
    }
}
