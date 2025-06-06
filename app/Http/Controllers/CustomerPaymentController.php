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
use App\TransactionPayment;
use App\User;
use App\AccountTransaction;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use LDAP\Result;
use Modules\Superadmin\Entities\Package;
use Yajra\DataTables\Facades\DataTables;
use App\Events\TransactionPaymentDeleted;
use App\Utils\ContactUtil;

class CustomerPaymentController extends Controller
{
    protected $transactionUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $contactUtil;
    /**
     * Constructor
     *
     * @param TransactionUtil $transactionUtil
     * @return void
     */
    public function __construct(TransactionUtil $transactionUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, ContactUtil $contactUtil)
    {
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->contactUtil = $contactUtil;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        
        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        $paid_in_types = ['customer_page' => 'Customer Page',
            'all_sale_page' => 'All Sale Page',
            'settlement' => 'Settlement',
            'customer_bulk' => 'Customer Bulk',
            'customer_simple' => 'Customer Simple'];
        $latest_ref_number = 0;
        $latest_ref_number_PP = 0;
        // $latest_ref_number_CPB = 0;
        $latest_ref_number_CPS = 0;
        try {
                $latest_ref_number = DB::table('transaction_payments')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_PP = DB::table('transaction_payments')->where('paid_in_type', 'customer_page')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_CPB = DB::table('transaction_payments')->where('paid_in_type', 'customer_bulk')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_CPS = DB::table('transaction_payments')->where('paid_in_type', 'customer_simple')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
            } catch (\Exception $exception) {
            }
            $latest_ref_number = (int)explode('/', $latest_ref_number);
            $latest_ref_number_PP = (int)explode('PP2021/', $latest_ref_number_PP);
            // $latest_ref_number_CPB = (int)explode('CPB-', $latest_ref_number_CPB);

            $latest_ref_number_CPB = DB::table('transaction_payments')->where('paid_in_type', 'customer_bulk')->orderBy('created_at', 'DESC')->first();
            $latest_ref_number_CPB = $latest_ref_number_CPB ? $latest_ref_number_CPB->payment_ref_no : "CPB-00";
            $latest_ref_number_CPB = (int) explode('CPB-', $latest_ref_number_CPB)[1];

            $latest_ref_number_CPS = (int)explode('CPS-', $latest_ref_number_CPS);
            $latest_ref_number += 1;
            $latest_ref_number_PP += 1;
            $latest_ref_number_CPB += 1;
            $latest_ref_number_CPS += 1;
        if (request()->ajax()) {
           $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->leftJoin('users', 'tp.created_by', '=', 'users.id')
                ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->leftJoin(
                    'account_transactions as act',
                    'transactions.id',
                    '=',
                    'act.transaction_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('contacts.type', 'customer')
                ->whereNull('tp.deleted_at')
                ->whereIn('transactions.payment_status', ['paid', 'partial'])
                ->where(function ($q) { $q->whereIn('transactions.type', $this->contactUtil->payable_customer_txns)->orWhere('transactions.is_credit_sale', 1);})
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'business_locations.name as location_name',
                    'tp.id as tp_id',
                    'tp.paid_on',
                    'tp.method',
                    'act.id as act_id',
                    'act.interest',
                    'tp.parent_id',
                    'tp.cheque_number',
                    'tp.card_number',
                    'tp.payment_ref_no',
                    'tp.paid_in_type',
                    'tp.created_by',
                     'users.username',
                     'tp.amount as total_paid',
                     'tp.created_at'
                    //DB::raw('SUM(tp.amount) as total_paid')
                );

                // dd($sells);
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->bill_no)) {
                $sells->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->payment_ref_no)) {
                $sells->where('tp.payment_ref_no', request()->payment_ref_no);
            }
            if (!empty(request()->cheque_number)) {
                $sells->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_method)) {
                $sells->where('tp.method', request()->payment_method);
            }
            if (!empty(request()->paid_in_type)) {
                $sells->where('tp.paid_in_type', request()->paid_in_type);
            }
            if (!empty(request()->payment_amount)) {
                $sells->where('tp.amount', request()->payment_amount);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('tp.paid_on', '>=', $start)
                    ->whereDate('tp.paid_on', '<=', $end);
            }
            $sells->groupBy(DB::raw('CASE WHEN tp.paid_in_type = "customer_page" THEN tp.parent_id ELSE tp.payment_ref_no END'))->orderBy('tp.id', 'desc');

            $datatable = DataTables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") || auth()->user()->can("view_own_sell_only")) {
                             $html .= '<li><a href="#" data-href="' .action("CustomerPaymentController@viewPayment", [$row->tp_id]).'" class="btn-modal-view" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                           
                        }
                        $html .= '</ul></div>';
                        
                        if (auth()->user()->can("sell.view")){
                            $html .= '&nbsp; <button type="button" class="btn btn-danger btn-xs delete_payment" 
                    
                                        data-href="'.action("CustomerPaymentController@destroy", [$row->tp_id]).'"
                    
                                        ><i class="fa fa-trash" aria-hidden="true"></i>'. __("messages.delete").'</button>';
                        }
                            
                        return $html;
                    }
                )
                ->addColumn('payment_amount', function ($row) use ($business_details) {
                    if (!empty($row->parent_id)) {
                        $parent_payment = TransactionPayment::where('id', $row->parent_id)->first();
                        // logger(json_encode($parent_payment));
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
                ->editColumn('payment_ref_no',function($row){
                    if (!empty($row->parent_id)) {
                        $parent_payment = TransactionPayment::where('id', $row->parent_id)->first();
                        if (!empty($parent_payment)) {
                            $ref = $parent_payment->payment_ref_no;
                        } else {
                            $ref = $row->payment_ref_no;
                        }
                    }else{
                        $ref = $row->payment_ref_no;
                    }
                    
                    return '<a href="#" data-href="' .action("CustomerPaymentController@viewPayment", [$row->tp_id]).'" class="btn-modal-view" data-container=".view_modal">' . $ref . '</a>';
                    
                })
                ->addColumn('interest', function ($row)  use ($business_details) {
                    $totalAmount = DB::table('transaction_payments')
                                ->join('account_transactions','transaction_payments.id','=','account_transactions.transaction_payment_id')
                                ->where('payment_ref_no', $row->payment_ref_no)
                                ->sum('account_transactions.interest');
                     
                               
                    return $this->productUtil->num_f($totalAmount, false, $business_details, false) ;
                })
                ->removeColumn('id')
                ->editColumn('final_total', function ($row) use ($business_details) {
                    return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->final_total . '">' . $this->productUtil->num_f($row->final_total, false, $business_details, false) . '</span>';
                })
                ->editColumn('total_paid', function ($row) use ($business_details) {
                    
                    
                    if (!empty($row->parent_id)) {
                        $parent_payment = TransactionPayment::where('id', $row->parent_id)->first();
                        if (!empty($parent_payment)) {
                            $totalAmount = $parent_payment->amount;
                        } else {
                            $totalAmount = DB::table('transaction_payments')
                                ->where('payment_ref_no', $row->payment_ref_no)
                                ->sum('amount');
                        }
                    }else{
                        $totalAmount = DB::table('transaction_payments')
                                ->where('payment_ref_no', $row->payment_ref_no)
                                ->sum('amount');
                    }
                                
                    $total_paid_html = '<span class="display_currency amount" data-currency_symbol="true" data-orig-value="' .( $totalAmount) . '">' . $this->productUtil->num_f(($totalAmount), false, $business_details, false) . '</span>';
                    
                
                    return $total_paid_html;
                    
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('created_at', '{{@format_datetime($created_at)}}')
                ->editColumn('paid_on', '{{@format_date($paid_on)}}')
                ->editColumn('method', function ($row) {
                    if ($row->method == 'bank_transfer') {
                        return 'Bank';
                    }
                    if ($row->method == 'card') {
                        if (!empty($row->card_number)) {
                            $htm = '<span class="" >Card <small>' . $row->card_number . '</small></span>';
                            return $htm;
                        }
                    }
                    if ($row->method == 'cheque') {
                        $html = '<span class="" >Cheque <small>' . $row->bank_name . '</small><small> ' . $row->cheque_number . '</small> <small>' . $row->cheque_date . '</small></span>';
                        return $html;
                    }
                    return ucfirst($row->method);
                })
                ->editColumn('cheque_number', function ($row) {
                    return $row->cheque_number.$row->card_number;
                })
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fa fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                    if (!empty($row->return_exists)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') . '"><i class="fa fa-undo"></i></small>';
                    }
                    if (!empty($row->is_recurring)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.subscribed_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    if (!empty($row->recur_parent_id)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="' . __('lang_v1.subscription_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    return $invoice_no;
                })
                ->addColumn('paid_in_type', function ($row) use ($paid_in_types) {
                    if (!empty($row->paid_in_type) && !empty($paid_in_types[$row->paid_in_type])) {
                        return $paid_in_types[$row->paid_in_type];
                    }
                    return '';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);
            $rawColumns = ['payment_ref_no','name', 'method', 'final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'payment_amount'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        $business_id = request()->session()->get('business.id');
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $payment_types = $this->transactionUtil->payment_types();
        $package_manage = Package::where('only_for_business', $business_id)->first();
        $customer_interest_deduct_option = $business_details->customer_interest_deduct_option;
        $customer_groups = ContactGroup::where('contact_groups.business_id', $business_id)
            ->where('contact_groups.type', 'customer')
            ->pluck('name', 'id');
        $income_accounts = Account::leftJoin('account_types', 'accounts.account_type_id', 'account_types.id')
            ->where('account_types.name', 'Income')
            ->select(DB::raw('accounts.name as name, accounts.id as id'))
            ->pluck('name', 'id');

            $account_type_query = AccountType::where('business_id', $business_id)
            ->whereNull('parent_account_type_id');
        $account_types_opts = $account_type_query->pluck('name', 'id');
        $account_type_query->with(['sub_types']);
        if (0 == 0) {
            $account_type_query->where(function ($q) {
                $q->where('name', 'Assets')->orWhere('name', 'Liabilities');
            });
        }
              $account_types = $account_type_query->get();
        // dd($account_types->toArray());
        $filterdata =[];
        $sub_acn_arr = [];
        $filterdata['subType_']['data'][] =array('id'=>"",'text'=>"All",true);
        foreach($account_types->toArray() as $acunts){
            $filterdata['subType_'.$acunts['id']]['data'][] =array('id'=>"",'text'=>"All",true);
            foreach($acunts['sub_types'] as $sub_Acn){
                $filterdata['subType_']['data'][] =array('id'=>$sub_Acn['id'],'text'=>$sub_Acn['name']);
                $filterdata['subType_'.$acunts['id']]['data'][] =array('id'=>$sub_Acn['id'],'text'=>$sub_Acn['name']);
                $sub_acn_arr[$sub_Acn['id']] = $sub_Acn['name'];
            }
        }
        $account_groups_raw = AccountGroup::where('business_id', $business_id)->whereIn('name', ['Cash Account', "Cheques in Hand (Customer's)", 'Card', 'Bank Account'])->get()->toArray();
        $account_groups = [];
        $filterdata['groupType_']['data'][] = array('id'=>"",'text'=>"All",true);
        foreach($account_groups_raw as $datarow){
            $filterdata['groupType_'.$datarow['account_type_id']]['data'][] = array('id'=>$datarow['id'],'text'=>$datarow['name']);
            $account_groups[$datarow['id']] = $datarow['name'];
        }
       
        return view('customer_payments.index')->with(compact(
            'customers',
            'filterdata',
            'business_locations',
            'payment_types',
            'account_types_opts',
            'account_groups',
            'customer_interest_deduct_option',
            'latest_ref_number',
            'latest_ref_number_PP',
            'latest_ref_number_CPB',
            'latest_ref_number_CPS',
            'customer_groups',
            'income_accounts'
        ));
    
    }
    
   public function printPayment($id){
       $business_id = request()->session()->get('business.id');
       
       try {
           
           $payment = TransactionPayment::where('id',$id)->with('contact')->first();
            if(!empty($payment->parent_id)){
                $parent_payment = TransactionPayment::where('id',$payment->parent_id)->with('contact')->first();
                $child_payments = Transaction::leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                                    ->select('tp.*', 'tp.id as tp_id', 'transactions.*')
                                    ->where('transactions.business_id', $business_id)
                                    ->where('tp.parent_id', $payment->parent_id)
                                    ->with('contact')->get();
            }else{
                $parent_payment = $payment;
                $parent_payment->amount = DB::table('transaction_payments')
                                    ->where('payment_ref_no', $payment->payment_ref_no)
                                    ->sum('amount');
                                    
                $child_payments = Transaction::leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                                    ->select('tp.*', 'tp.id as tp_id', 'transactions.*')
                                    ->where('transactions.business_id', $business_id)
                                    ->where('tp.payment_ref_no', $payment->payment_ref_no)
                                    ->with('contact')->get();
            }
            
            
            $company = Business::find($business_id);
            
            $receipt['html_content'] = view('customer_payments.partials.print_payment')
                ->with(compact(
                    'child_payments',
                    'parent_payment',
                    'company'
                ))->render();
                
            $output = ['success' => 1, 'receipt' => $receipt ];
           
       }catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg'     => trans("messages.something_went_wrong"),
                'error'   => $e->getMessage()
            ];
        }
        return $output;
       
        
   }
    
   public function viewPayment($id)
    {
        
        $business_id = request()->session()->get('business.id');

        // Fetch initial payment with contact relationship
        
        
        // $payment = TransactionPayment::with('contact')->findOrFail($id);
        $payment = TransactionPayment::with(['contact', 'transaction'])->findOrFail($id);


        // Initialize variables
        $parent_payment = $payment;
        $child_payments = collect();

        // Check for parent or use ref_no logic

        
        $child_payments = TransactionPayment::with('transaction')
                            ->where(function ($query) use ($payment, $business_id) {
                                if (!empty($payment->parent_id)) {
                                    $query->where('parent_id', $payment->parent_id);
                                } else {
                                    $query->where('payment_ref_no', $payment->payment_ref_no);
                                }
                            })
                            ->whereHas('transaction', function ($q) use ($business_id) {
                                $q->where('business_id', $business_id);
                            })
                            ->get();

        // Fetch company info
        $company = Business::find($business_id);

        // Determine location_id
        $location_id = $child_payments->first()->location_id ?? null;

        if (empty($location_id) && !empty($parent_payment->transaction_id)) {
            // $location_id = Transaction::where('id', $parent_payment->transaction_id)->value('location_id');
            $location_id = $child_payments->first()->transaction->location_id ?? null;
        }

        // Get location name
        // $location = $location_id ? BusinessLocation::where('id', $location_id)->value('name') : null;
        $location = optional(BusinessLocation::find($location_id))->name;


        // Render receipt view
        // $receipt_data = view('customer_payments.partials.view_payment')
        //     ->with(compact('child_payments', 'parent_payment', 'company', 'location'))
        //     ->render();
        $receipt_data = cache()->remember('view_payment_' . $id.$business_id, now()->addMinutes(2), function () use ($child_payments, $parent_payment, $company, $location) {
            return view('customer_payments.partials.view_payment')
                ->with(compact('child_payments', 'parent_payment', 'company', 'location'))
                ->render();
        });

        return view('customer_payments.view')
            ->with(compact('receipt_data', 'parent_payment', 'id'));
    }


    public function customerPaymentInformations($customer, $type){
        
        $start_date = request()->start ?? null;
        $end_date = request()->end ?? null;
        $bank = request()->bank;
        $post_party_type = request()->post_party_type;
        
        if($type === "amount"){
            // @eng START 19/2
            if($customer != 'all') {
                $amounts = TransactionPayment::get_customer_wise_unique_amounts($customer, $start_date, $end_date, $bank, $post_party_type);
                return response()->json([
                    'data' => $amounts->map(function($item){
                        return $item->amount;
                    }
                )]);

            }
            $ret = TransactionPayment::query()->whereHas('transaction',function($query) {
                $query->whereHas('contact',function($query){
                     $query->whereNotNull('id');
                });
            })->where('amount','>',0);
            
            
            
            if(!empty($start_date)){
                $ret->whereDate('transaction_payments.paid_on','>=', $start_date);
            }
            
            if(!empty($end_date)){
                $ret->whereDate('transaction_payments.paid_on','<=', $end_date);
            }
            
            if(!empty($bank)){
                $ret->where('transaction_payments.account_id', $bank);
            }
            
            
            return response()->json([
                'data' => $ret->distinct()->groupBy('amount')->get()->map(function($item) {
                    return $item->amount;
                })    
            ]);            
            // @eng END 19/2
        }

        if($type === "cheque_no"){
            // @eng START 19/2
            if($customer != 'all') {
                $amounts = TransactionPayment::get_customer_wise_unique_cheque_no($customer, $start_date, $end_date, $bank,$post_party_type); 
                return response()->json([
                    'data' => $amounts->map(function($item){
                        return $item->cheque_number;
                    }
                )]);
            }

            $ret = TransactionPayment::query()->whereHas('transaction',function($query) use($customer){
                // $query->whereHas('contact',function($query) use($customer){
                //     //  $query->where('id', $customer);
                //     $query->whereNotNull('id');
                // });
            })->where('cheque_number','!=',null); 
            
            
            if(!empty($start_date)){
                $ret->whereDate('transaction_payments.paid_on','>=', $start_date);
            }
            
            if(!empty($end_date)){
                $ret->whereDate('transaction_payments.paid_on','<=', $end_date);
            }
            
            if(!empty($bank)){
                $ret->where('transaction_payments.account_id', $bank);
            }
            
            
            return response()->json([
                'data' => $ret->distinct()->groupBy('cheque_number')->get()->map(function($item) {
                    return $item->cheque_number;
                })    
            ]);
            // @eng END 19/2
        }
    }

    // @eng START 19/2
    public function customerInfoFor($for, $data) {
        if($for == 'cheque_no') {
            if($data == 'all') {
                $customers = Contact::customersDropdown(request()->session()->get('user.business_id'), false);
                
                $allAmountsQuery = TransactionPayment::query()->whereHas('transaction',function($query) {
                    $query->whereHas('contact',function($query){
                         $query->whereNotNull('id');
                        });
                })->distinct()->groupBy('amount')->get();
                
                $allAmounts = $allAmountsQuery->map(function($item) {
                   return $item->amount;
                });
                
                return response()->json(['data' => $customers, 'amounts' => $allAmounts]);
            }
            
            $contact = DB::select("SELECT contacts.* FROM contacts WHERE contacts.id IN 
            (SELECT transactions.contact_id FROM transactions WHERE transactions.id IN 
            (SELECT transaction_id FROM transaction_payments WHERE cheque_number = ?))",
            [$data]);
            
            $amounts = DB::select("SELECT amount FROM transaction_payments WHERE cheque_number = ?", [$data]);
            $retAmounts = array_map(function($a) {
                return $a->amount;
            }, $amounts);
            return response()->json([
                'data' => [$contact[0]->id  => $contact[0]->name . " (" . $contact[0]->contact_id . ")"],
                'amounts' => $retAmounts
                ]);
        }
        elseif($for == 'amount') {
            if($data == 'all') {
                $customers = Contact::customersDropdown(request()->session()->get('user.business_id'), false);
                
                $allChequesQuery = TransactionPayment::query()->whereHas('transaction',function($query) {
                    $query->whereHas('contact',function($query) {
                        //  $query->where('id', $customer);
                        $query->whereNotNull('id');
                    });
                })->where('cheque_number','!=',null)->distinct()->groupBy('cheque_number')->get();            
                
                $allCheques = $allChequesQuery->map(function($item) {
                        return $item->cheque_number;
                });
                
                return response()->json(['data' => $customers, 'cheques' => $allCheques]);
            }
            
            $contacts = DB::select("SELECT contacts.* FROM contacts WHERE contacts.id IN 
            (SELECT transactions.contact_id FROM transactions WHERE transactions.id IN 
            (SELECT transaction_id FROM transaction_payments WHERE amount = ?))",
            [$data]);
            
            $retContacts = [];
            foreach($contacts as $c) $retContacts[$c->id] = $c->name . " (" . $c->contact_id . ")";
            
            $cheques = DB::select("SELECT cheque_number FROM transaction_payments WHERE amount = ?", [$data]);
            $retCheques = array_map(function($cheque) {
                return $cheque->cheque_number;
            }, $cheques);
            
            return response()->json(['data' => $retContacts, 'cheques' => $retCheques ]);            
        }
    }
    // @eng END 19/2
    
    public function CustomerInterest()
    {
        $business_id = request()->session()->get('user.business_id');
        $business_details = Business::find($business_id);
        $paid_in_types = ['customer_page' => 'Customer Page',
            'all_sale_page' => 'All Sale Page',
            'settlement' => 'Settlement',
            'customer_bulk' => 'Customer Bulk',
            'customer_simple' => 'Customer Simple'];
        $latest_ref_number = 0;
        $latest_ref_number_PP = 0;
        $latest_ref_number_CPB = 0;
        $latest_ref_number_CPS = 0;
        try {
                $latest_ref_number = DB::table('transaction_payments')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_PP = DB::table('transaction_payments')->where('paid_in_type', 'customer_page')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_CPB = DB::table('transaction_payments')->where('paid_in_type', 'customer_bulk')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
                $latest_ref_number_CPS = DB::table('transaction_payments')->where('paid_in_type', 'customer_simple')->orderBy('created_at', 'DESC')->first()->payment_ref_no;
            } catch (\Exception $exception) {
            }
            $latest_ref_number = (int)explode('/', $latest_ref_number);
            $latest_ref_number_PP = (int)explode('PP2021/', $latest_ref_number_PP);
            $latest_ref_number_CPB = (int)explode('CPB-', $latest_ref_number_CPB);
            $latest_ref_number_CPS = (int)explode('CPS-', $latest_ref_number_CPS);
            $latest_ref_number += 1;
            $latest_ref_number_PP += 1;
            $latest_ref_number_CPB += 1;
            $latest_ref_number_CPS += 1;
        if (request()->ajax()) {
            $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->leftJoin('users', 'tp.created_by', '=', 'users.id')
                ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
                ->leftJoin(
                    'account_transactions as act',
                    'transactions.id',
                    '=',
                    'act.transaction_id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('contacts.type', 'customer')
                ->where('act.interest','!=', null)
                ->whereIn('transactions.payment_status', ['paid', 'partial'])
                ->where(function ($q) { $q->where('transactions.type', 'opening_balance')->orWhere('transactions.is_credit_sale', 1);})
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.invoice_no',
                    'contacts.name',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'business_locations.name as location_name',
                    'tp.id as tp_id',
                    'tp.paid_on',
                    'tp.method',
                    'act.id as act_id',
                    'act.interest',
                    'tp.parent_id',
                    'tp.cheque_number',
                    'tp.card_number',
                    'tp.payment_ref_no',
                    'tp.paid_in_type',
                    'tp.created_by',
                     'users.username',
                     'tp.amount as total_paid'
                    //DB::raw('SUM(tp.amount) as total_paid')
                );
            if (!empty(request()->customer_id)) {
                $customer_id = request()->customer_id;
                $sells->where('contacts.id', $customer_id);
            }
            if (!empty(request()->bill_no)) {
                $sells->where('transactions.invoice_no', request()->bill_no);
            }
            if (!empty(request()->payment_ref_no)) {
                $sells->where('tp.payment_ref_no', request()->payment_ref_no);
            }
            if (!empty(request()->cheque_number)) {
                $sells->where('tp.cheque_number', request()->cheque_number);
            }
            if (!empty(request()->payment_method)) {
                $sells->where('tp.method', request()->payment_method);
            }
            if (!empty(request()->paid_in_type)) {
                $sells->where('tp.paid_in_type', request()->paid_in_type);
            }
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end = request()->end_date;
                $sells->whereDate('tp.paid_on', '>=', $start)
                    ->whereDate('tp.paid_on', '<=', $end);
            }
            $sells->orderBy('tp.paid_on', 'desc')->groupBy('tp.id');

            // dd($sells->get());


            $datatable = DataTables::of($sells)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                                    <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                        if (auth()->user()->can("sell.view") || auth()->user()->can("direct_sell.access") || auth()->user()->can("view_own_sell_only")) {
                            $html .= '<li><a href="#" data-href="' . action("SellController@show", [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-external-link" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                            $html .= '<li><a href="#" data-href="' . action("TransactionPaymentController@edit", [$row->tp_id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        $html .= '</ul></div>';
                        return $html;
                    }
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
                ->addColumn('interest', function ($row) {
                    return $row->interest == Null ? '0.00' : $row->interest;
                })
                ->removeColumn('id')
                ->editColumn('final_total', function ($row) use ($business_details) {
                    return '<span class="display_currency final-total" data-currency_symbol="true" data-orig-value="' . $row->final_total . '">' . $this->productUtil->num_f($row->final_total, false, $business_details, false) . '</span>';
                })
                ->editColumn('total_paid', function ($row) use ($business_details) {
                    $interest=$row->interest == Null ? '0.00' : $row->interest;
                    if ($row->total_paid == '') {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="0.00">' . $this->productUtil->num_f(0, false, $business_details, false) . '</span>';
                    } else {
                        $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="' .( $row->total_paid-$interest) . '">' . $this->productUtil->num_f(($row->total_paid-$interest), false, $business_details, false) . '</span>';
                    }
                    return $total_paid_html;

                    // if ($row->total_paid == '') {
                    //     $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="0.00">' . $this->productUtil->num_f(0, false, $business_details, false) . '</span>';
                    // } else {
                    //     $total_paid_html = '<span class="display_currency total-paid" data-currency_symbol="true" data-orig-value="' . $row->total_paid . '">' . $this->productUtil->num_f($row->total_paid, false, $business_details, false) . '</span>';
                    // }
                    // return $total_paid_html;
                })
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('paid_on', '{{@format_date($paid_on)}}')
                ->editColumn('method', function ($row) {
                    //return $row->method;
                    if ($row->method == 'bank_transfer') {
                        return 'Bank';
                    }
                    if ($row->method == 'card') {
                        if (!empty($row->card_number)) {
                            $htm = '<span class="" >Card <small>' . $row->card_number . '</small></span>';
                            return $htm;
                        }
                    }
                    if ($row->method == 'cheque') {
                        $html = '<span class="" >Cheque <small>' . $row->bank_name . '</small><small> ' . $row->cheque_number . '</small> <small>' . $row->cheque_date . '</small></span>';
                        return $html;
                    }
                    return ucfirst($row->method);
                })
                ->editColumn('cheque_number', function ($row) {
                    if ($row->method == 'bank_transfer' || $row->method == 'cheque') {
                        return $row->cheque_number;
                    }
                    if ($row->method == 'card') {
                        return $row->card_number;
                    }
                    return '';
                })
                ->editColumn('invoice_no', function ($row) {
                    $invoice_no = $row->invoice_no;
                    if (!empty($row->woocommerce_order_id)) {
                        $invoice_no .= ' <i class="fa fa-wordpress text-primary no-print" title="' . __('lang_v1.synced_from_woocommerce') . '"></i>';
                    }
                    if (!empty($row->return_exists)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.some_qty_returned_from_sell') . '"><i class="fa fa-undo"></i></small>';
                    }
                    if (!empty($row->is_recurring)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-red label-round no-print" title="' . __('lang_v1.subscribed_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    if (!empty($row->recur_parent_id)) {
                        $invoice_no .= ' &nbsp;<small class="label bg-info label-round no-print" title="' . __('lang_v1.subscription_invoice') . '"><i class="fa fa-recycle"></i></small>';
                    }
                    return $invoice_no;
                })
                ->addColumn('paid_in_type', function ($row) use ($paid_in_types) {
                    if (!empty($row->paid_in_type) && !empty($paid_in_types[$row->paid_in_type])) {
                        return $paid_in_types[$row->paid_in_type];
                    }
                    return '';
                })
                ->setRowAttr([
                    'data-href' => function ($row) {
                        if (auth()->user()->can("sell.view") || auth()->user()->can("view_own_sell_only")) {
                            return action('SellController@show', [$row->id]);
                        } else {
                            return '';
                        }
                    }
                ]);
            $rawColumns = ['name', 'method', 'final_total', 'action', 'total_paid', 'total_remaining', 'payment_status', 'invoice_no', 'discount_amount', 'tax_amount', 'total_before_tax', 'shipping_status', 'payment_amount'];
            return $datatable->rawColumns($rawColumns)
                ->make(true);
        }
        $business_id = request()->session()->get('business.id');
        $customers = Contact::customersDropdown($business_id, false);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $payment_types = $this->transactionUtil->payment_types();
        $package_manage = Package::where('only_for_business', $business_id)->first();
        $customer_interest_deduct_option = $business_details->customer_interest_deduct_option;



        return view('customer_payments.index')->with(compact(
            'customers',
            'business_locations',
            'payment_types',
            'customer_interest_deduct_option',
            'latest_ref_number',
            'latest_ref_number_PP',
            'latest_ref_number_CPB',
            'latest_ref_number_CPS'
        ));
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }
    /**
     * Display the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }
    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
      public function destroy($id)

    {

        if (!auth()->user()->can('purchase.delete.payments') && !auth()->user()->can('purchase.payments') &&
                !auth()->user()->can('purchase.edit.payments') &&
                !auth()->user()->can('add.payments') && !auth()->user()->can('sell.payments')) {
                abort(403, 'Unauthorized action.');

            }

        if (request()->ajax()) {

            try {

                $payment = TransactionPayment::findOrFail($id);

                if (!empty($payment->parent_id)) {
                    $parent_payment = TransactionPayment::find($payment->parent_id);
                    $child_payments = TransactionPayment::where('parent_id',$payment->parent_id)->get();
                    $parent_payment->delete();
                }else{
                    $child_payments = TransactionPayment::where('payment_ref_no',$payment->payment_ref_no)->get();
                }
                
                foreach($child_payments as $one){
                    // $transaction_id = $one->transaction_id;
            
                    $transaction = Transaction::find($one->transaction_id)->delete();
                    
                    // if($transaction){
                    //     $transaction->delete();
                    // }
                    
                    // $not_delete = Account::whereIn('name',['Accounts Receivable','Accounts Payable'])->pluck('id');
    
                    AccountTransaction::where('transaction_payment_id',$one->id)->delete();
                    
                    $one->delete();
    
                }
                if($payment):
                    $payment->delete();
                endif;

                $output = [

                    'success' => true,

                    'msg' => __('purchase.payment_deleted_success')

                ];

            } catch (\Exception $e) {

                logger($e);

                $output = [

                    'success' => false,

                    'msg' => __('messages.something_went_wrong')

                ];

            }

            return $output;

        }

    }
}
