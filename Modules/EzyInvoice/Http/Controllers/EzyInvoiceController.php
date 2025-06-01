<?php

namespace Modules\EzyInvoice\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Contact;
use App\ContactLedger;
use App\CustomerReference;
use App\Product;
use App\Store;
use App\Variation;
use App\Category;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Routing\Controller;
use Modules\EzyInvoice\Entities\Ezyinvoice;
use Modules\EzyInvoice\Entities\EzyinvoiceCreditSalePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\ContactController;
use App\NotificationTemplate;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\DataTables;

class EzyInvoiceController extends Controller
{
     /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

    private $barcode_types;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->productUtil = $productUtil;
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
    }
    
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        $business = Business::where('id', $business_id)->first();
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
       

         if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            if (request()->ajax()) {
                $query = Ezyinvoice::leftjoin('business_locations', 'ezyinvoices.location_id', 'business_locations.id')
                    ->where('ezyinvoices.business_id', $business_id)
                    ->select([
                        'business_locations.name as location_name',
                        'ezyinvoices.*',
                    ]);

                if (!empty(request()->location_id)) {
                    $query->where('ezyinvoices.location_id', request()->location_id);
                }
                
                if (!empty(request()->invoice_no)) {
                    $query->where('ezyinvoices.id', request()->invoice_no);
                }
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('ezyinvoices.transaction_date', '>=', request()->start_date);
                    $query->whereDate('ezyinvoices.transaction_date', '<=', request()->end_date);
                }
                $query->orderBy('ezyinvoices.id', 'desc');
                $first = null;
                
                $first = Ezyinvoice::where('business_id', $business_id)->where('status', 0)->orderBy('id', 'desc')->first();

                $delete_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'delete_settlement');
                $edit_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'edit_settlement');

                $settlements = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) use ($first,$delete_settlement,$edit_settlement) {
                            $html = '';
                            if ($row->status == 1) {
                                $html .= '<a class="btn  btn-danger btn-sm" href="' . action("\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@create") . '">' . __("ezyinvoice::lang.finish_settlement") . '</a>';
                            } else {
                                $html .=  '<div class="btn-group">
                                <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                    data-toggle="dropdown" aria-expanded="false">' .
                                    __("messages.actions") .
                                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                    </span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';

                                $html .= '<li><a data-href="' . action("\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@show", [$row->id]) . '" class="btn-modal" data-container=".settlement_modal"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';
                                if (auth()->user()->can("settlement.edit") && $edit_settlement) {
                                    $html .= '<li><a href="' . action("\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@create") . '?edit_id='.$row->id.'" class="edit_settlement_button"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';
                                }
                                if (!empty($first) && $first->id == $row->id && $delete_settlement && auth()->user()->can("settlement.delete")) {
                                   // commented By M Usman for hiding Delete Action
                                    $html .= '<li><a href="' . action("\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@destroy", [$row->id]) . '" class="delete_settlement_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                                }
                                $html .= '<li><a data-href="' . action("\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@print", [$row->id]) . '" class="print_settlement_button"><i class="fa fa-print"></i> ' . __("ezyinvoice::lang.print") . '</a></li>';

                                $html .= '</ul></div>';
                            }
                            return $html;
                        }
                    )
                    ->editColumn('status', function ($row) {
                        if ($row->status == 0) {
                            return '<span class="label label-success">Completed</span>';
                        } else {
                            return '<span class="label label-danger">Pending</span>';
                        }
                    })
                    
                    ->addColumn('created_by', function ($row) {
                        $transaction = Transaction::where('invoice_no',$row->settlement_no)->leftJoin('users','users.id','transactions.created_by')->select('users.username')->first();
                        if(!empty($transaction)){
                            return $transaction->username;
                        }
                    })
                    ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                    ->editColumn('total_amount', '{{@num_format($total_amount)}}')
                    ->setRowAttr([
                        'data-href' => function ($row) {
                            return  action('\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@show', [$row->id]);
                        }
                    ])

                    ->removeColumn('id');

                return $settlements->rawColumns(['action', 'status', 'total_amount'])
                    ->make(true);
            }
        }
        
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        $products = Product::where('business_id', $business_id)->forModule('ezyinvoice_invoices')->pluck('name', 'id');
        
        return view('ezyinvoice::invoices.index')->with(compact(
            'business_locations',
            'customers',
            'products'
        ));
    }

    
   
    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $business = Business::where('id', $business_id)->first();
        $edit_id = request()->edit_id;
        
        if(!empty($edit_id)){
            $active_invoice = Ezyinvoice::where('id', $edit_id)
                ->where('business_id', $business_id)
                ->select('ezyinvoices.*')
                ->with(['credit_sale_payments'])->first();
        }else{
            $active_invoice = Ezyinvoice::where('status', 1)
                ->where('business_id', $business_id)
                ->select('ezyinvoices.*')
                ->with(['credit_sale_payments'])->first();
        }
        
        
            
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
            
        
        if(empty($active_invoice)){
            $count = Ezyinvoice::where('business_id', $business_id)->orderBy('id','DESC')->first();
        
            if(!empty($count)){
                $count = $this->extractLastInteger($count->invoice_no);
            }else{
                $count = 0;
            }
            
            $invoice_no = str_pad((1 + $count), 4, 0, STR_PAD_LEFT);
            $active_invoice = Ezyinvoice::create([
                "invoice_no" => $invoice_no,"business_id" => $business_id,"transaction_date" => date('Y-m-d'),"location_id" => $default_location,"status" => 1
            ]);
        }
        
        
        
        if (!empty($active_invoice)) {
            $invoice_no = $active_invoice->invoice_no;
        }

        

        $customers = Contact::customersDropdown($business_id, false, true, 'customer');
        $products = Product::where('business_id', $business_id)->forModule('ezyinvoice_invoices')->pluck('name', 'id');
        $ezyinvoice = Ezyinvoice::where('invoice_no', $invoice_no)->where('business_id', $business_id)->first();
        
        $credit_sale_payments = [];
        if (!empty($ezyinvoice)) {
            $ezyinvoice_credit_sale_payments = EzyinvoiceCreditSalePayment::leftjoin('contacts', 'ezyinvoice_credit_sale_payments.customer_id', 'contacts.id')
                ->leftjoin('products', 'ezyinvoice_credit_sale_payments.product_id', 'products.id')
                ->where('ezyinvoice_credit_sale_payments.invoice_no', $ezyinvoice->id)
                ->select('ezyinvoice_credit_sale_payments.*', 'contacts.name as customer_name', 'products.name as product_name')
                ->get();    
        }
        
        return view('ezyinvoice::invoices.create')->with(compact(
            'business_locations',
            'invoice_no',
            'ezyinvoice',
            'ezyinvoice_credit_sale_payments',
            'customers',
            'products'
        ));
    }

    function extractLastInteger($text) {
        // Use regular expression to find the last integer in the text
        preg_match('/\d+$/', $text, $matches);
    
        if (isset($matches[0])) {
            // $matches[0] contains the matched integer
            return intval($matches[0]);
        } else {
            // No integer found, handle accordingly (e.g., return 0, null, etc.)
            return 0;
        }
    }
    
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request, ContactController $contactController)
    {
        try {
            
            $settlement_no = $request->settlement_no;
            $business_id = $request->session()->get('business.id');
            $settlement = Ezyinvoice::where('invoice_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $edit = Ezyinvoice::where('ezyinvoices.id', $settlement->id)->where('ezyinvoices.business_id', $business_id)->where('status', 0)->first();
            
            DB::beginTransaction();
            if (!empty($edit)) {
                $this->deletePreviouseTransactions($settlement->id);
            }

            $business_locations = BusinessLocation::forDropdown($business_id);
            $default_location = current(array_keys($business_locations->toArray()));

            $settlement = Ezyinvoice::where('ezyinvoices.id', $settlement->id)->where('ezyinvoices.business_id', $business_id)
                ->with([
                    'credit_sale_payments'
                ])
                ->select('ezyinvoices.*')
                ->first();
            $business = Business::where('id', $settlement->business_id)->first();
            
           
            
            foreach ($settlement->credit_sale_payments as $credit_sale_payment) {
                $transactions = $this->createCreditSellTransactions($settlement, 'credit_sale', $credit_sale_payment, $business_id, $default_location, null, $credit_sale_payment->id);
                $transaction = $transactions[0];
                $product = $transactions[1];
                
                $credit_sale_payment->transaction_id = $transaction->id;
                $credit_sale_payment->save();
                
                
                if(!empty($product->vat_claimed) && $transaction->tax_amount > 0){
                    $account_id = $this->transactionUtil->account_exist_return_id('Taxes Payable');
                    $type = 'debit';
                    $this->createAccountTransaction($transaction, $type, $account_id, null, 'ledger_show', null, 0, true,$credit_sale_payment->note);
                }
                
                
                $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
                
                $contact = Contact::where('id',$credit_sale_payment->customer_id)->first();
                
                $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for','credit_sale')->first();
                $total_paid = $credit_sale_payment->amount;
                
                if(!empty($msg_template) && $contact){ //When a customer is walk in it's obvious they have no contact
                    
                    $msg = $msg_template->sms_body;
                    $msg = str_replace('{business_name}',$business->name,$msg);
                    $msg = str_replace('{total_amount}',$this->productUtil->num_f($credit_sale_payment->amount),$msg);
                    $msg = str_replace('{contact_name}',$contact->name,$msg);
                    $msg = str_replace('{invoice_number}',$settlement->invoice_no,$msg);
                    $msg = str_replace('{paid_amount}',$this->productUtil->num_f($total_paid),$msg);
                    $msg = str_replace('{due_amount}',$this->productUtil->num_f($credit_sale_payment->amount - $total_paid),$msg);
                    $msg = str_replace('{cumulative_due_amount}', $this->productUtil->num_f(strval($contactController->get_due_bal($credit_sale_payment->customer_id, false))),$msg);
                    
                    
                    if(!empty($business->sms_settings)){
                        $phones = explode(',',str_replace(' ','',$business->sms_settings['msg_phone_nos']));
                    }
                    
                    $phones[] = $contact->mobile;
                    $phones[] = $contact->alternate_number;
                    
                    if(!empty($phones)){
                        $data = [
                            'sms_settings' => $sms_settings,
                            'mobile_number' => implode(',',$phones),
                            'sms_body' => $msg
                        ];
                        
                        $response = $this->businessUtil->sendSms($data,'credit_sale',$contact); 
                    }
                    
                }
            }
            
            
            
            $settlement_total = $settlement->credit_sale_payments->sum('amount');
            $settlement->total_amount = $settlement_total;
            $settlement->status = 0; // set status to non active

            $settlement->finish_date = date('Y-m-d');
            $settlement->save();

            
            DB::commit();
            return view('ezyinvoice::invoices.print')->with(compact('settlement', 'business'));
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }

    
    public function createAccountTransaction($transaction, $type, $account_id, $transaction_payment_id = null, $sub_type = null, $contact_id = null, $amount = 0, $is_credit_sale = false,$note = null,$slip_no = null)
    {
        $account_transaction_data = [
            'amount' => abs($transaction->tax_amount),
            'account_id' => $account_id,
            'contact_id' => $transaction->contact_id,
            'type' => $type,
            'sub_type' => $sub_type,
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => $transaction_payment_id,
            'note' => $note,
            'slip_no' => $slip_no
        ];
        
        

        AccountTransaction::createAccountTransaction($account_transaction_data);
        // create ledger transactions
        if ($sub_type == 'ledger_show') {
            ContactLedger::createContactLedger($account_transaction_data);
            if (!$is_credit_sale) {
                if ($type == 'debit') {
                    $ledger_type = 'credit';
                }
                if ($type == 'credit') {
                    $ledger_type = 'debit';
                }
                $account_transaction_data['type'] = $ledger_type;
                ContactLedger::createContactLedger($account_transaction_data);
            }
        }
    }
    
    
    
    public function createCreditSellTransactions($settlement, $sub_type, $sale, $business_id, $default_location, $fuel_tank_id = null, $credit_sale_id = null)
    {
        $uf_quantity = $this->productUtil->num_uf($sale->qty);

        $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')
            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
            ->leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('products.id', $sale->product_id)
            ->select('variations.*', 'variation_location_details.location_id', 'categories.name as category_name','products.tax','products.vat_claimed')->first();
    
        $final_amount = $sale->qty * $sale->price;
        $total_before_tax = $sale->qty * $product->default_sell_price;
        $tax_amount = $final_amount - $total_before_tax;

       
        $ob_data = [
            'business_id' => $business_id,
            'location_id' => $settlement->location_id,
            'type' => 'sell',
            'status' => 'final',
            'payment_status' => 'due',
            'contact_id' => $sale->customer_id,
            'transaction_date' => \Carbon::parse($settlement->transaction_date)->format('Y-m-d'),
            'total_before_tax' => $total_before_tax,
            'final_total' => $final_amount,
            'tax_amount' => $tax_amount,
            'tax_id' => $product->tax,
            'credit_sale_id' => $credit_sale_id,
            'is_credit_sale' => 0,
            'created_by' => request()->session()->get('user.id')
        ];
        //Generate reference number
        $ob_data['invoice_no'] = $settlement->invoice_no;
        if (!empty($sale->customer_reference)) {
            $ob_data['ref_no'] = $sale->customer_reference;
            $ob_data['customer_ref'] = $sale->customer_reference;
        }
        if (!empty($sale->order_number)) {
            $ob_data['order_no'] = $sale->order_number;
        }
        if (!empty($sale->order_date)) {
            $ob_data['order_date'] = $sale->order_date;
        }
        if ($sub_type == 'credit_sale') {
            $ob_data['is_credit_sale'] = 1;
            $ob_data['sub_type'] = 'credit_sale';
            $ob_data['payment_status'] = 'due';
        }
        
        
        //Create transaction
        $transaction = Transaction::create($ob_data);
        
        return [$transaction,$product];
    }
    
    
    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));

        $settlement = Ezyinvoice::where('ezyinvoices.id', $id)->where('ezyinvoices.business_id', $business_id)
            ->with([
                'credit_sale_payments'
            ])
            ->select('ezyinvoices.*')
            ->first();
        
        $business = Business::where('id', !empty($settlement) ? $settlement->business_id : 0)->first();
        
        return view('ezyinvoice::invoices.show')->with(compact('settlement', 'business'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        // 
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }
    
    public function preview($id)
    {
        $business_id = request()->session()->get('business.id');

        $settlement = Ezyinvoice::where('ezyinvoices.id', $id)->where('ezyinvoices.business_id', $business_id)
            ->with([
                'credit_sale_payments',
            ])
            ->select('ezyinvoices.*')
            ->first();

        $business = Business::where('id', $settlement->business_id)->first();
        
        return view('ezyinvoice::invoices.preview')->with(compact('settlement', 'business'));
    }

    public function productPreview($id)
    {
        $business_id = request()->session()->get('business.id');

        $settlement = Ezyinvoice::where('ezyinvoices.id', $id)
            ->leftjoin('ezyinvoice_credit_sale_payments', 'ezyinvoices.id', 'ezyinvoice_credit_sale_payments.invoice_no')
            ->leftjoin('products', 'products.id', 'ezyinvoice_credit_sale_payments.product_id')
            ->select('ezyinvoices.*', 'products.*', 'ezyinvoice_credit_sale_payments.*')
            ->get();

        return view('ezyinvoice::invoices.product_preview')->with(compact('settlement'));
    }
    
        /**
     * get details of customer
     * @return Response
     */
    public function getCustomerDetails($customer_id, ContactController $contactController)
    {
        $business_id = request()->session()->get('business.id');
        $query = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('contact_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
            ->where('contacts.business_id', $business_id)
            ->where('contacts.id', $customer_id)
            ->onlyCustomers()
            ->select([
                'contacts.contact_id', 'contacts.name', 'contacts.created_at', 'total_rp', 'cg.name as customer_group', 'city', 'state', 'country', 'landmark', 'mobile', 'contacts.id', 'is_default',
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.type = 'sell' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_received"),
                DB::raw("SUM(IF(t.type = 'sell_return', final_total, 0)) as total_sell_return"),
                DB::raw("SUM(IF(t.type = 'sell_return', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as sell_return_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'advance_payment', -1*final_total, 0)) as advance_payment"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                'email', 'tax_number', 'contacts.pay_term_number', 'contacts.pay_term_type', 'contacts.credit_limit', 'contacts.custom_field1', 'contacts.custom_field2', 'contacts.custom_field3', 'contacts.custom_field4', 'contacts.type'
            ])
            ->groupBy('contacts.id')->first();
        $due = $query ? ($query->total_invoice - $query->invoice_received + $query->advance_payment) : 0;
        $return_due = $query ? ($query->total_sell_return - $query->sell_return_paid) : 0;
        $opening_balance = $query ? ($query->opening_balance - $query->opening_balance_paid) : 0;

        $total_outstanding =  $due -  $return_due + $opening_balance ;
        if (empty($total_outstanding) || empty($query)) {
            $total_outstanding = 0.00;
        }
        if (empty($query->credit_limit)) {
            $credit_limit = 'No Limit';
        } else {
            $credit_limit = $query->credit_limit;
        }
        $business_details = Business::find($business_id);
        $customer_references = CustomerReference::where('contact_id', $customer_id)->where('business_id', $business_id)->select('reference')->get();

        // return ['total_outstanding' =>  strval($this->productUtil->num_f($total_outstanding, false, $business_details, true)), 'credit_limit' => strval($credit_limit), 'customer_references' => $customer_references];
        return ['total_outstanding' =>  $this->productUtil->num_f(strval($contactController->get_cus_due_bal($customer_id, false))), 'credit_limit' => strval($credit_limit), 'customer_references' => $customer_references];
    }
        /**
     * add credit_sale payment data to db
     * @return Response
     */
    public function saveCreditSalePayment(Request $request)
    {
        try {

            $business_id = $request->session()->get('business.id');
            
            if(!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'same_order_no')){
                $validator = Validator::make($request->all(), [
                    'order_number' => [
                        'required',
                        Rule::unique('ezyinvoice_credit_sale_payments', 'order_number')
                            ->where(function ($query) use ($request) {
                                return $query->where('customer_id', $request->customer_id);
                            }),
                    ],
                ]);
            
                if ($validator->fails()) {
                    return [
                                'success' => false,
                                'msg' => $validator->errors()->first()
                            ];
                }
            }
            
            $settlement_exist = Ezyinvoice::where('invoice_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $settlement_data = array(
                'settlement_no' => $request->settlement_no,
                'business_id' => $business_id,
                'note' => $request->note,
                'status' => 1
            );
            if (empty($settlement_exist)) {
                $settlement_exist = Ezyinvoice::create($settlement_data);
            }
            
            $price = $this->productUtil->num_uf($request->price);
            $qty = $this->productUtil->num_uf($request->qty);
            $amount = $this->productUtil->num_uf($request->amount);
            $data = array(
                'business_id' => $business_id,
                'invoice_no' => $settlement_exist->id,
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'order_number' => $request->order_number,
                'order_date' => \Carbon::parse($request->order_date)->format('Y-m-d'),
                'price' => $price,
                'qty' => $qty,
                'amount' => $amount,
                'outstanding' => $this->productUtil->num_uf($request->outstanding),
                'credit_limit' => $request->credit_limit,
                'customer_reference' => $request->customer_reference,
                'note' => $request->note
            );
            $ezyinvoice_credit_sale_payment = EzyinvoiceCreditSalePayment::create($data);

            $output = [
                'success' => true,
                'settlement_credit_sale_payment_id' => $ezyinvoice_credit_sale_payment->id,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
    /**
     * delete credit_sale payment data to db
     * @return Response
     */
    public function deleteCreditSalePayment($id)
    {
        try {
            $payment = EzyinvoiceCreditSalePayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }
    
      /**
     * get price of product
     * @return Response
     */
    public function getProductPrice(Request $request)
    {
        $product_id =  $request->product_id;
        $product = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->where('products.id', $product_id)
            ->select('sell_price_inc_tax')
            ->first();
        if (!empty($product)) {
            $price = $product->sell_price_inc_tax;
        } else {
            $price = 0.00;
        }
        return ['price' => $price];
    }
    
        /**
     * print resources
     * @param settlement_id
     * @return Response
     */
    public function print($id)
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));

        $settlement = Ezyinvoice::where('ezyinvoices.id', $id)->where('ezyinvoices.business_id', $business_id)
            ->with([
                'credit_sale_payments',
            ])
            ->select('ezyinvoices.*')
            ->first();

        $business = Business::where('id', $settlement->business_id)->first();
        

        return view('ezyinvoice::invoices.print')->with(compact('settlement', 'business'));
    }
    
    
    public function deletePreviouseTransactions($settlement_id, $is_destory = false)
    {
        $business_id = request()->session()->get('business.id');
        $settlement = Ezyinvoice::find($settlement_id);
        $all_trasactions = Transaction::where('invoice_no', $settlement->invoice_no)->where('business_id', $business_id)->get();

        foreach ($all_trasactions as $transaction) {
            if (!empty($transaction)) {
                AccountTransaction::where('transaction_id', $transaction->id)->forceDelete();
                Transaction::where('id', $transaction->id)->forceDelete();
            }
        }

        $settlement->total_amount = 0;
        $settlement->save();


        if ($is_destory) {
            EzyinvoiceCreditSalePayment::where('invoice_no', $settlement->id)->delete();
        }
    }

    
    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $settlement = Settlement::findOrFail($id);
            $this->deletePreviouseTransactions($settlement->id, true);
            $settlement->delete();
            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('::lang.settlement_delete_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return $output;
    }
}
