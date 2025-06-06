<?php
namespace Modules\Vat\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\Customer;
use App\TaxRate;


use Illuminate\Routing\Controller;
use App\Product;
use Illuminate\Http\Request;
;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use Modules\Vat\Entities\FleetVatInvoice2;
use Modules\Vat\Entities\FleetVatInvoiceDetail2;

use Modules\Vat\Entities\FleetVatInvoicePayment2;

use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\TankSellLine;

use Yajra\DataTables\Facades\DataTables;
use App\Business;
use App\NotificationTemplate;
use App\System;

use App\Transaction;
use App\TransactionPayment;
use App\AccountTransaction;
use App\ContactLedger;
use App\Variation;
use Modules\Vat\Entities\VatCreditBill;

use Modules\Vat\Entities\VatSupplyFrom;
use Modules\Vat\Entities\VatBankDetail;
use Modules\Vat\Entities\VatConcern;
use App\CustomerReference;
use Milon\Barcode\DNS2D;

use Modules\Vat\Entities\VatInvoice2Setting;

use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Utils\ContactUtil;
use Modules\Fleet\Entities\RouteOperation;

class FleetVatInvoice2Controller extends Controller
{
    protected $commonUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $businessUtil;
    protected $productUtil;
    protected $contactUtil;
    //protected $balance_duen;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(
        Util $commonUtil,
        ModuleUtil $moduleUtil,
        TransactionUtil $transactionUtil,
        BusinessUtil $businessUtil,
        ProductUtil $productUtil,
        ContactUtil $contactUtil
        //balance_duen $GLOBALS
    ) {

        $this->commonUtil = $commonUtil;
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->contactUtil = $contactUtil;
        //$this->balance_duen =& $GLOBALS;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        
        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {
            $issue_customer_bills = FleetVatInvoice2::leftjoin('contacts', 'fleet_vat_invoices_2.customer_id', 'contacts.id')
                ->leftjoin('contacts as subc', 'fleet_vat_invoices_2.sub_customer', 'subc.id')
                ->leftjoin('users', 'fleet_vat_invoices_2.created_by', 'users.id')
                ->where('fleet_vat_invoices_2.business_id', $business_id)
                ->select(
                    'fleet_vat_invoices_2.*',
                    'contacts.name as customer_name',
                    'subc.name as sub_customer',
                    'users.username as username'
                )->get();

            return DataTables::of($issue_customer_bills)

                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right" role="menu">';
                    
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@print', $row->id) . '" class="print_bill" ><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@edit', $row->id) . '" class="" ><i class="fa fa-edit" aria-hidden="true"></i>' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action("\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@destroy", [$row->id]) . '" class="delete-issue_bill_customer"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                   

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->editColumn('outstanding_amount','{{@num_format($outstanding_amount)}}')
                ->editColumn('date','{{@format_date($date)}}')
                ->rawColumns(['action'])
                ->make(true);
        }
        
        return view('vat::fleet_vat_invoice2.index');
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        
        $business_id = request()->session()->get('business.id');

        
        $products = Product::where('business_id', $business_id)->forModule('vat_vatinvoice2')->pluck('name', 'id');
        
        
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        
        $bill_no = ((int)FleetVatInvoice2::where('business_id',$business_id)->get()->last()->customer_bill_no ?? 0) + 1;
        
        $payment_types = $this->productUtil->payment_types(null, false, false, false, false, true);
        
        $fleet_active = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module');
        $ro_customerIDs = RouteOperation::where('business_id',$business_id)->where('is_vat',1)->whereNull('vat_applied')->pluck('contact_id')->toArray();
        
        $customers = Contact::whereIn('id',$ro_customerIDs)->pluck('name','id');
        
        return view('vat::fleet_vat_invoice2.create')->with(compact(
            'customers',
            'products',
            'business_locations',
            'payment_types',
            'fleet_active',
            'bill_no'
        ));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            
            // dd($request->all());
            
            $business_id = request()->session()->get('business.id');
            $is_print  = request()->is_print;
            // dd($request->all());
            
            $vat_bill = VatCreditBill::where('customer_id',$request->customer_id)->first();
            $save_txns = false;
            if(!empty($vat_bill)){
                if($vat_bill->linked_accounts == "yes"){
                    $save_txns = true;
                }
            }
            
            $data = array(
                'business_id' => $business_id,
                'date' => \Carbon::parse($request->voucher_order_date)->format('Y-m-d'),
                'customer_bill_no' => $request->customer_bill_no,
                'location_id' => $request->location_id,
                'customer_id' => $request->customer_id,
                'reference_id' => $request->reference_id,
                'prefix' => $request->prefix_id,
                'created_by' => Auth::user()->id,
                'total_amount' => $this->productUtil->num_uf($request->voucher_order_amount),
                'outstanding_amount' => $this->productUtil->num_uf($request->voucher_order_outstanding),
                'tax_amount' => $request->vat_total,
                'discount_amount' => 0,
                'credit_limit' => $request->voucher_order_creditlimit,
                'sub_customer' => $request->sub_customer,
                'invoice_to' => $request->invoice_to,
                'supplied_on' => $request->supplied_on,
                'price_adjustment' => $request->price_adjustment,
                'sale_type' => $request->sale_type,
                'route_operation_id' => $request->route_operation_id
            );
            DB::beginTransaction();
            
            $issue_customer_bill = FleetVatInvoice2::create($data);
            
            if(!empty($request->route_operation_id)){
                RouteOperation::where('id',$request->route_operation_id)->update(array('vat_applied' => 1));
            }
            
            if(!empty($save_txns)){
                $transaction = $this->createCreditSellTransactions($issue_customer_bill);
                $pa_transaction = $this->transactionUtil->createOrUpdatePriceAdjustment($issue_customer_bill,$issue_customer_bill->customer_bill_no);
            }
            

            $total_amount = 0;
            $tax_amount = 0;
            $discount_amount = 0;
            $unit_vat_rate = 0;
            
            foreach ($request->issue_customer_bill['product_id'] as $key => $product_id) {
                $total_amount += $this->productUtil->num_uf($request->issue_customer_bill['sub_total'][$key]);
                $tax_amount += $this->productUtil->num_uf($request->issue_customer_bill['tax'][$key]);
                $discount_amount += $this->productUtil->num_uf($request->issue_customer_bill['discount'][$key]);
                $unit_vat_rate += $this->productUtil->num_uf($request->issue_customer_bill['unit_vat_rate'][$key]);
                
                $details = array(
                    'business_id' => $business_id,
                    'issue_bill_id' => $issue_customer_bill->id,
                    'product_id' => $product_id,
                    'unit_price' => $this->productUtil->num_uf($request->issue_customer_bill['unit_price'][$key]),
                    'unit_price_before_tax' => $this->productUtil->num_uf($request->issue_customer_bill['unit_price_excl'][$key]),
                    'qty' => $this->productUtil->num_uf($request->issue_customer_bill['qty'][$key]),
                    'discount' => $this->productUtil->num_uf($request->issue_customer_bill['discount'][$key]),
                    'tax' => $this->productUtil->num_uf($request->issue_customer_bill['tax'][$key]),
                    'sub_total' => $this->productUtil->num_uf($request->issue_customer_bill['sub_total'][$key]),
                    'unit_vat_rate' =>  $this->productUtil->num_uf($request->issue_customer_bill['unit_vat_rate'][$key]),

                );
                // dd($details);
                $bill_detail = FleetVatInvoiceDetail2::create($details);
                
                $business_locations = BusinessLocation::forDropdown($business_id);
                $default_location = current(array_keys($business_locations->toArray()));
                
                if(!empty($save_txns)){
                    $this->createSellTransactions($transaction, $bill_detail, $business_id, $default_location);
                }
                
                
                
            }
            
            foreach($request->payment as $payment){

                $payment_data = [
                    'invoice_id' => $issue_customer_bill->id,
                    'account_id' => $payment['account_id'],
                    'business_id' => $business_id,
                    'amount' => $this->productUtil->num_uf($payment['amount']),
                    'method' => $payment['method'],
                    'card_transaction_number' => $payment['card_transaction_number'],
                    'cheque_number' => $payment['cheque_number'],
                    'cheque_date' => $payment['cheque_date'],
                    'bank_name' => $payment['bank_name'],
                    'paid_on' => \Carbon::parse($request->voucher_order_date)->format('Y-m-d'),
                    'created_by' => auth()->user()->id,
                    'payment_for' => $request->customer_id,
                    'note' => $payment['note']
                ];
                
                FleetVatInvoicePayment2::create($payment_data);
            }
            
            
            $issue_customer_bill->total_amount = $total_amount;
            $issue_customer_bill->tax_amount = $tax_amount;
            $issue_customer_bill->discount_amount = $discount_amount;
            $issue_customer_bill->unit_vat_rate_total = $unit_vat_rate;
            $issue_customer_bill->save();
            
            
            if(!empty($save_txns)){
                $transaction->total_before_tax = $total_amount - $tax_amount;
                $transaction->final_total = $total_amount;
                $transaction->tax_amount = $tax_amount;
                $transaction->discount_amount = $discount_amount;
                $transaction->save();
                
                $payments = $request->payment ?? [];
                
                if(!empty($payments)){
                    $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments, null, null,  true,'due');
                }
                
                
                $status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
            }
                
            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
            
            $contact = Contact::where('id',$issue_customer_bill->customer_id)->first();
            
            $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for','credit_sale')->first();

            if(!empty($msg_template) && $contact->credit_notification == 'customer_bill'){
                
                $msg = $msg_template->sms_body;
                $msg = str_replace('{business_name}',$business->name,$msg);
                $msg = str_replace('{total_amount}',$this->productUtil->num_f($issue_customer_bill->total_amount),$msg);
                $msg = str_replace('{contact_name}',$contact->name,$msg);
                $msg = str_replace('{invoice_number}',$issue_customer_bill->customer_bill_no,$msg);
                $msg = str_replace('{paid_amount}',$this->productUtil->num_f($issue_customer_bill->total_amount),$msg);
                
                $msg = str_replace('{transaction_date}',date('Y-m-d', strtotime($issue_customer_bill->date)),$msg);
                
                $msg = str_replace('{due_amount}',$this->productUtil->num_f(0),$msg);
                $msg = str_replace('{cumulative_due_amount}', $this->productUtil->num_f(($issue_customer_bill->outstanding_amount+$issue_customer_bill->total_amount)),$msg);
                
                
                $phones = [];
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
                

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
            if($is_print){
                $output['print_url'] = action('\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@print', $issue_customer_bill->id);
            }
        } catch (\Exception $e) {
            DB::rollback();
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }


         return redirect('vat-module/fleet-vat-invoice2')->with('status', $output);
    }
    
    
    public function createAccountTransaction($transaction, $type, $account_id, $sub_type = null, $is_credit_sale)
    {
        $account_transaction_data = [
            'amount' => abs($transaction->final_total),
            'account_id' => $account_id,
            'contact_id' => $transaction->contact_id,
            'type' => $type,
            'sub_type' => $sub_type,
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id
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
    
    public function createCreditSellTransactions($sale,$id = null)
    {
        
        $final_total = $sale->total_amount - $sale->discount_amount;
        $total_before_tax = $sale->total_amount - $sale->tax_amount;
        $ob_data = [
            'business_id' => $sale->business_id,
            'location_id' => $sale->location_id,
            'type' => 'sell',
            'status' => 'final',
            'payment_status' => 'due',
            'contact_id' => $sale->customer_id,
            'pump_operator_id' => $sale->operator_id,
            'transaction_date' => \Carbon::parse($sale->date)->format('Y-m-d'),
            'total_before_tax' => $total_before_tax,
            'final_total' => $final_total,
            'tax_amount' => $sale->tax_amount,
            'discount_type' => 'fixed',
            'discount_amount' => $sale->discount_amount,
            'credit_sale_id' => $sale->id,
            'is_credit_sale' => 1,
            'is_settlement' => 0,
            'created_by' => request()->session()->get('user.id'),
            'invoice_no' => $sale->customer_bill_no,
            'sub_type' => 'credit_sale',
            
        ];
        
        if(empty($id)){
            //Create transaction
            $transaction = Transaction::create($ob_data);
        }else{
            $transaction = Transaction::where('invoice_no', $id)->where('type','sell')->first();
            Transaction::where('invoice_no', $id)->where('type','sell')->update($ob_data);
        }
        
        return $transaction;
    }
    
    public function createSellTransactions($transaction, $sale, $business_id, $default_location)
    {
        $uf_quantity = $this->productUtil->num_uf($sale->qty);
        
        $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')
            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
            ->leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('products.id', $sale->product_id)
            ->select('variations.id as variation_id', 'variation_location_details.location_id', 'products.id as product_id', 'categories.name as category_name', 'products.enable_stock')->first();

        $this->transactionUtil->createOrUpdateSellLinesVatBill($transaction, $product->product_id, $product->variation_id, $product->location_id, $sale);
        $location_product = !empty($product->location_id) ? $product->location_id : $default_location;
        
        // if enable stock
        if ($product->enable_stock && !empty($is_other_sale)) {
            
            $this->productUtil->decreaseProductQuantity(
                $sale->product_id,
                $product->variation_id,
                $location_product,
                $uf_quantity,
                0,
                'decrease',
                0
            );
            
            $store_id = Store::where('business_id', $business_id)->first()->id;
			$this->productUtil->decreaseProductQuantityStore(
                $sale->product_id,
                $product->variation_id,
                $location_product,
                $uf_quantity,
                $store_id,
                "decrease",
                0
            );

        }
        
        $fuel_tank = FuelTank::where('product_id',$sale->product_id)->first();
        
        if (!empty($fuel_tank)) {
            $fuel_tank_id = $fuel_tank->id;
            FuelTank::where('id', $fuel_tank_id)->decrement('current_balance', $sale->qty);
            TankSellLine::create([
                'business_id' => $sale->business_id,
                'transaction_id' => $transaction->id,
                'tank_id' => $fuel_tank_id,
                'product_id' => $sale->product_id,
                'quantity' => $sale->qty
            ]);
        }

        
        return true;
    }
    

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $business_id = request()->session()->get('business.id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        
        $payment_types = $this->productUtil->payment_types(null, false, false, false, false, true);
        
        $payment = FleetVatInvoicePayment2::where('invoice_id',$id)->get();
        $invoice = FleetVatInvoice2::findOrFail($id);
        $invoice_details = FleetVatInvoiceDetail2::where('issue_bill_id',$id)->get();
        $customer_ref = CustomerReference::where('business_id',$business_id)->where('contact_id',$invoice->customer_id)->pluck('reference','id');
        
        $ro = RouteOperation::findOrFail($invoice_details[0]->product_id);
        $customer = Contact::findOrFail($invoice->customer_id);
        
        return view('vat::fleet_vat_invoice2.edit')->with(compact(
            'business_locations',
            'payment_types',
            'payment',
            'invoice',
            'customer_ref',
            'invoice_details',
            'ro',
            'customer'
        ));
    }
    
    

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try{
            
            $business_id = request()->session()->get('business.id');
            $is_print  = request()->is_print;
            
            $vat_bill = VatCreditBill::where('customer_id',$request->customer_id)->first();
            $save_txns = false;
            if(!empty($vat_bill)){
                if($vat_bill->linked_accounts == "yes"){
                    $save_txns = true;
                }
            }
            
            $data = array(
                'business_id' => $business_id,
                'date' => \Carbon::parse($request->voucher_order_date)->format('Y-m-d'),
                'customer_bill_no' => $request->customer_bill_no,
                'location_id' => $request->location_id,
                'reference_id' => $request->reference_id,
                'created_by' => Auth::user()->id,
                'total_amount' => $this->productUtil->num_uf($request->voucher_order_amount),
                'tax_amount' => $request->vat_total,
                'discount_amount' => 0,
                'outstanding_amount' => $this->productUtil->num_uf($request->voucher_order_outstanding),
                'credit_limit' => $request->voucher_order_creditlimit,
                'sub_customer' => $request->sub_customer,
                'invoice_to' => $request->invoice_to,
                'supplied_on' => $request->supplied_on,
                'price_adjustment' => $request->price_adjustment,
                'sale_type' => $request->sale_type
            );
            DB::beginTransaction();
            
            FleetVatInvoice2::where('id',$id)->update($data);
            $issue_customer_bill = FleetVatInvoice2::findOrFail($id);
            $this->deletePreviouseTransactions($issue_customer_bill->id);
            
            if(!empty($save_txns)){
                $transaction = $this->createCreditSellTransactions($issue_customer_bill,$issue_customer_bill->customer_bill_no);
                $pa_transaction = $this->transactionUtil->createOrUpdatePriceAdjustment($issue_customer_bill,$issue_customer_bill->customer_bill_no);
            }

            $total_amount = 0;
            $tax_amount = 0;
            $discount_amount = 0;
            $unit_vat_rate = 0;
            
            foreach ($request->issue_customer_bill['product_id'] as $key => $product_id) {
                $total_amount += $this->productUtil->num_uf($request->issue_customer_bill['sub_total'][$key]);
                $tax_amount += $this->productUtil->num_uf($request->issue_customer_bill['tax'][$key]);
                $discount_amount += $this->productUtil->num_uf($request->issue_customer_bill['discount'][$key]);
                $unit_vat_rate += $this->productUtil->num_uf($request->issue_customer_bill['unit_vat_rate'][$key]);
                
                $details = array(
                    'business_id' => $business_id,
                    'issue_bill_id' => $issue_customer_bill->id,
                    'product_id' => $product_id,
                    'unit_price' => $this->productUtil->num_uf($request->issue_customer_bill['unit_price'][$key]),
                    'unit_price_before_tax' => $this->productUtil->num_uf($request->issue_customer_bill['unit_price_excl'][$key]),
                    'qty' => $this->productUtil->num_uf($request->issue_customer_bill['qty'][$key]),
                    'discount' => $this->productUtil->num_uf($request->issue_customer_bill['discount'][$key]),
                    'tax' => $this->productUtil->num_uf($request->issue_customer_bill['tax'][$key]),
                    'sub_total' => $this->productUtil->num_uf($request->issue_customer_bill['sub_total'][$key]),
                    'unit_vat_rate' =>  $this->productUtil->num_uf($request->issue_customer_bill['unit_vat_rate'][$key]),

                );
                // dd($details);
                $bill_detail = FleetVatInvoiceDetail2::create($details);
                $business_locations = BusinessLocation::forDropdown($business_id);
                $default_location = current(array_keys($business_locations->toArray()));
                
                if(!empty($save_txns)){
                    $this->createSellTransactions($transaction, $bill_detail, $business_id, $default_location);
                }
                
            }
            
            $payments =  [];
            
            foreach($request->payment as $payment){

                $payment_data = [
                    'invoice_id' => $issue_customer_bill->id,
                    'account_id' => $payment['account_id'],
                    'business_id' => $business_id,
                    'amount' => $this->productUtil->num_uf($payment['amount']),
                    'method' => $payment['method'],
                    'card_transaction_number' => $payment['card_transaction_number'],
                    'cheque_number' => $payment['cheque_number'],
                    'cheque_date' => $payment['cheque_date'],
                    'bank_name' => $payment['bank_name'],
                    'paid_on' => \Carbon::parse($request->voucher_order_date)->format('Y-m-d'),
                    'created_by' => auth()->user()->id,
                    'payment_for' => $request->customer_id,
                    'note' => $payment['note']
                ];
                
                unset($payment['payment_id']);
                
                $payments[] = $payment;
                
                FleetVatInvoicePayment2::create($payment_data);
            }
            

            $issue_customer_bill->total_amount = $total_amount;
            $issue_customer_bill->tax_amount = $tax_amount;
            $issue_customer_bill->discount_amount = $discount_amount;
            $issue_customer_bill->unit_vat_rate_total = $unit_vat_rate;
            $issue_customer_bill->save();
            
            
            if(!empty($save_txns)){
                $transaction->total_before_tax = $total_amount - $tax_amount;
                $transaction->final_total = $total_amount;
                $transaction->tax_amount = $tax_amount;
                $transaction->discount_amount = $discount_amount;
                $transaction->save();
                
                
                if(!empty($payments)){
                    $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments, null, null,  true,'due');
                }
                
                
                $status = $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
            }
            
            
            
            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
            
            $contact = Contact::where('id',$issue_customer_bill->customer_id)->first();
            
            $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for','credit_sale')->first();

            if(!empty($msg_template) && $contact->credit_notification == 'customer_bill'){
                
                $msg = $msg_template->sms_body;
                $msg = str_replace('{business_name}',$business->name,$msg);
                $msg = str_replace('{total_amount}',$this->productUtil->num_f($issue_customer_bill->total_amount),$msg);
                $msg = str_replace('{contact_name}',$contact->name,$msg);
                $msg = str_replace('{invoice_number}',$issue_customer_bill->customer_bill_no,$msg);
                $msg = str_replace('{paid_amount}',$this->productUtil->num_f($issue_customer_bill->total_amount),$msg);
                
                $msg = str_replace('{transaction_date}',date('Y-m-d', strtotime($issue_customer_bill->date)),$msg);
                
                $msg = str_replace('{due_amount}',$this->productUtil->num_f(0),$msg);
                $msg = str_replace('{cumulative_due_amount}', $this->productUtil->num_f(($issue_customer_bill->outstanding_amount+$issue_customer_bill->total_amount)),$msg);
                
                
                $phones = [];
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
                

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
            
            if($is_print){
                $output['print_url'] = action('\Modules\Vat\Http\Controllers\FleetVatInvoice2Controller@print', $issue_customer_bill->id);
            }
            
        } catch (\Exception $e) {
            DB::rollback();
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }


        return redirect('vat-module/fleet-vat-invoice2')->with('status', $output);
    }
    
    

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            $settlement = FleetVatInvoice2::findOrFail($id);
            $this->deletePreviouseTransactions($settlement->id, true);
            $settlement->delete();
            DB::commit();

            $output = [
                'success' => true,
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
    
    public function deletePreviouseTransactions($id, $is_destory = false)
    {
        $business_id = request()->session()->get('business.id');
        $settlement = FleetVatInvoice2::find($id);
        
        FleetVatInvoiceDetail2::where('issue_bill_id',$id)->delete();
        FleetVatInvoicePayment2::where('invoice_id',$id)->delete();
        
        $all_trasactions = Transaction::where('invoice_no', $settlement->customer_bill_no)->where('business_id', $business_id)->with(['sell_lines'])->withTrashed()->get();

        foreach ($all_trasactions as $transaction) {
            if (!empty($transaction)) {
                $deleted_sell_lines = $transaction->sell_lines;
                $deleted_sell_lines_ids = $deleted_sell_lines->pluck('id')->toArray();
                if ($transaction->sub_type == 'credit_sale') {
                    $this->transactionUtil->deleteSellLinesSettlement(
                        $deleted_sell_lines_ids,
                        $transaction->location_id,
                        false
                    );
                } else {
                    $this->transactionUtil->deleteSellLinesSettlement(
                        $deleted_sell_lines_ids,
                        $transaction->location_id
                    );
                }

                $transaction->status = 'draft';
                $business = [
                    'id' => $business_id,
                    'accounting_method' => request()->session()->get('business.accounting_method'),
                    'location_id' => $transaction->location_id
                ];
                if ($transaction->sub_type != 'credit_sale') {
                    $this->transactionUtil->adjustMappingPurchaseSell('final', $transaction, $business, $deleted_sell_lines_ids);
                }

                //Delete Cash register transactions
                $transaction->cash_register_payments()->delete();
            }

            $tank_sell_lines =  TankSellLine::where('transaction_id', $transaction->id)->get();
            foreach ($tank_sell_lines as $tank_sell_line) {
                FuelTank::where('id', $tank_sell_line->tank_id)->increment('current_balance', $tank_sell_line->quantity);
            }
            TankSellLine::where('transaction_id', $transaction->id)->forceDelete();
            AccountTransaction::where('transaction_id', $transaction->id)->forceDelete();
            ContactLedger::where('transaction_id', $transaction->id)->forceDelete();
            TransactionPayment::where('transaction_id', $transaction->id)->forceDelete();
        }

       
        if ($is_destory) {
            FleetVatInvoiceDetail2::where('issue_bill_id',$id)->delete();
            FleetVatInvoicePayment2::where('invoice_id',$id)->delete();
            Transaction::where('invoice_no', $settlement->customer_bill_no)->forceDelete();
            $settlement->delete();
        }
    }



public function print($id)
{
    $issue_customer_bill = FleetVatInvoice2::leftjoin('contacts', 'fleet_vat_invoices_2.customer_id', 'contacts.id')
        ->leftjoin('customer_references', 'fleet_vat_invoices_2.reference_id', 'customer_references.id')
        ->leftjoin('users', 'fleet_vat_invoices_2.created_by', 'users.id')
        ->where('fleet_vat_invoices_2.id', $id)
        ->select(
            'fleet_vat_invoices_2.*',
            'customer_references.reference',
            'contacts.name as customer_name',
            'users.username as username'
        )->first();

    $bill_details = FleetVatInvoiceDetail2::leftjoin('route_operations', 'fleet_vat_invoice_details_2.product_id', 'route_operations.id')
        ->where('fleet_vat_invoice_details_2.issue_bill_id', $id)
        ->select('fleet_vat_invoice_details_2.*', 'route_operations.invoice_no as product_name')
        ->get();

    // // Calculate the total tax amount (total_vat)
    // $taxSum = FleetVatInvoiceDetail2::where('issue_bill_id', $id)->sum('tax') ?? 0;

    // // Calculate the total of sub_total as total_with_vat
    // $totalWithVat = FleetVatInvoiceDetail2::where('issue_bill_id', $id)->sum('sub_total') ?? 0;
    
    $business_details = $this->businessUtil->getDetails($issue_customer_bill->business_id);

    $receipt_details = $this->__getReceiptDetails($issue_customer_bill);
    // $receipt_details->total_vat = $this->productUtil->num_f($taxSum,true,$business_details); // Overwrite total_vat with taxSum
    // $receipt_details->total_with_vat =  $this->productUtil->num_f($totalWithVat + $taxSum,true,$business_details); // Overwrite total_with_vat with totalWithVat
    $receipt_details->price_adjustment =  $this->productUtil->num_f($issue_customer_bill->price_adjustment,true,$business_details);
    $receipt_details->final_total =  $this->productUtil->num_f($issue_customer_bill->total_amount + $issue_customer_bill->price_adjustment,true,$business_details);
    
    $payment_details = FleetVatInvoicePayment2::where('invoice_id',$id)->get();

    return view('vat::fleet_vat_invoice2.print')->with(compact('issue_customer_bill', 'bill_details', 'receipt_details','payment_details'));
}


    
    
    public function __getReceiptDetails($transaction, $receipt_printer_type = 'browser'){
        $business_details = $this->businessUtil->getDetails($transaction->business_id);
        $tax_rate = TaxRate::where('business_id',$transaction->business_id)->first();
        
        
        $supply_from = VatSupplyFrom::where('business_id',$transaction->business_id)->where('status',1)->first();
        $bank_detail = VatBankDetail::where('business_id',$transaction->business_id)->where('status',1)->first();
        $concern = VatConcern::where('business_id',$transaction->business_id)->where('status',1)->first();
        
        $location_details = BusinessLocation::find($transaction->location_id);
        $invoice_layout = $this->businessUtil->invoiceLayout($transaction->business_id, $transaction->location_id, $location_details->invoice_layout_id);
        $il = $invoice_layout;
        
        $footer_top_margin = System::getProperty('footer_top_margin');
        $admin_invoice_footer = System::getProperty('admin_invoice_footer');
        
        $ref_no = CustomerReference::find($transaction->reference_id)->reference ?? '';
        
        $output = [
            'reference_no' => $ref_no,
            'header_text' => isset($il->header_text) ? $il->header_text : '',
            'business_name' => ($il->show_business_name == 1) ? $business_details->name : '',
            'location_name' => ($il->show_location_name == 1) ? $location_details->name : '',
            'sub_heading_line1' => trim($il->sub_heading_line1),
            'sub_heading_line2' => trim($il->sub_heading_line2),
            'sub_heading_line3' => trim($il->sub_heading_line3),
            'sub_heading_line4' => trim($il->sub_heading_line4),
            'sub_heading_line5' => trim($il->sub_heading_line5),
            'table_product_label' => $il->table_product_label,
            'table_qty_label' => $il->table_qty_label,
            'table_unit_price_label' => $il->table_unit_price_label,
            'table_subtotal_label' => $il->table_subtotal_label,
            'font_size' => $il->font_size,
            'header_font_size' => $il->header_font_size,
            'footer_font_size' => $il->footer_font_size,
            'business_name_font_size' => $il->business_name_font_size,
            'invoice_heading_font_size' => $il->invoice_heading_font_size,
            'footer_top_margin' => $footer_top_margin,
            'admin_invoice_footer' => $admin_invoice_footer,
            'logo_height' => $il->logo_height,
            'logo_width' => $il->logo_width,
            'logo_margin_top' => $il->logo_margin_top,
            'logo_margin_bottom' => $il->logo_margin_bottom,
            'header_align' => $il->header_align,
            'tax_amount' => $transaction->tax_amount,
            'tax_rate' => $tax_rate,
            'business_location' => $location_details,
            'supply_from' => $supply_from,
            'bank_detail' => $bank_detail,
            'concern'  => $concern ,
        ];
        
        
        $output['display_name'] = $output['business_name'];
        
        if (!empty($output['location_name'])) {
            if (!empty($output['display_name'])) {
                $output['display_name'] .= ', ';
            }
            $output['display_name'] .= $output['location_name'];
        }
        
        $contact_details = $this->transactionUtil->getCustomerDetails($transaction->invoice_to == 'customer' ? $transaction->customer_id : $transaction->sub_customer);
        $output['contact_details'] = $contact_details;
        
        //Logo
        $output['logo'] = $il->show_logo != 0 && !empty($il->logo) && file_exists(public_path('uploads/invoice_logos/' . $il->logo)) ? asset('uploads/invoice_logos/' . $il->logo) : false;
        
        //Address
        $output['address'] = '';
        $temp = [];
        if ($il->show_landmark == 1) {
            $output['address'] .= $location_details->landmark . "\n";
        }
        if ($il->show_city == 1 &&  !empty($location_details->city)) {
            $temp[] = $location_details->city;
        }
        if ($il->show_state == 1 &&  !empty($location_details->state)) {
            $temp[] = $location_details->state;
        }
        if ($il->show_zip_code == 1 &&  !empty($location_details->zip_code)) {
            $temp[] = $location_details->zip_code;
        }
        if ($il->show_country == 1 &&  !empty($location_details->country)) {
            $temp[] = $location_details->country;
        }
        if (!empty($temp)) {
            $output['address'] .= implode(',', $temp);
        }
        
        $output['website'] = $location_details->website;
        $output['location_custom_fields'] = '';
        $temp = [];
        
        $location_custom_field_settings = !empty($il->location_custom_fields) ? $il->location_custom_fields : [];
        if (!empty($location_details->custom_field1) && in_array('custom_field1', $location_custom_field_settings)) {
            $temp[] = $location_details->custom_field1;
        }
        if (!empty($location_details->custom_field2) && in_array('custom_field2', $location_custom_field_settings)) {
            $temp[] = $location_details->custom_field2;
        }
        if (!empty($location_details->custom_field3) && in_array('custom_field3', $location_custom_field_settings)) {
            $temp[] = $location_details->custom_field3;
        }
        if (!empty($location_details->custom_field4) && in_array('custom_field4', $location_custom_field_settings)) {
            $temp[] = $location_details->custom_field4;
        }
        if (!empty($temp)) {
            $output['location_custom_fields'] .= implode(', ', $temp);
        }
        
        
        //Tax Info
        // if (!empty($business_details->tax_number_1)) {
        //     $output['tax_label1'] = !empty($business_details->tax_label_1) ? $business_details->tax_label_1 . ': ' : '';
        //  
        
        $output['tax_label1'] = !empty($business_details->tax_label_1) ? $business_details->tax_label_1 . ': ' : '';
        $output['tax_info1'] = !empty($business_details->tax_number_1) ? $business_details->tax_number_1 : '';
        
        
        
        //Shop Contact Info
        $output['contact'] = '';
        if ($il->show_mobile_number == 1 && !empty($location_details->mobile)) {
            $output['contact'] .= __('contact.mobile') . ': ' . $location_details->mobile;
        }
        if ($il->show_alternate_number == 1 && !empty($location_details->alternate_number)) {
            if (empty($output['contact'])) {
                $output['contact'] .= __('contact.mobile') . ': ' . $location_details->alternate_number;
            } else {
                $output['contact'] .= ', ' . $location_details->alternate_number;
            }
        }
        if ($il->show_email == 1 && !empty($location_details->email)) {
            if (!empty($output['contact'])) {
                // $output['contact'] .= "\n";
            }
            $output['contact'] .= __('business.email') . ': ' . $location_details->email;
        }
        
        //Customer show_customer
        $customer = Contact::find($transaction->invoice_to == 'customer' ? $transaction->customer_id : $transaction->sub_customer);
        $output['customer'] = $customer;
        $output['customer_info'] = '';
        $output['customer_tax_number'] = '';
        $output['customer_tax_label'] = '';
        $output['customer_custom_fields'] = '';
        if ($il->show_customer == 1) {
            $output['customer_label'] = !empty($il->customer_label) ? $il->customer_label : '';
            $output['customer_name'] = !empty($customer->name) ? $customer->name : '';
            if (!empty($output['customer_name']) && $receipt_printer_type != 'printer') {
                $output['customer_info'] .= $customer->landmark;
                // $output['customer_info'] .= '<br>' . implode(',', array_filter([$customer->city, $customer->state, $customer->country]));
                $output['customer_info'] .= '<br>' . $customer->mobile;
            }
            $output['customer_tax_number'] = !empty($customer->tax_number) ? $customer->tax_number : null;
            $output['customer_tax_label'] = !empty($il->client_tax_label) ? $il->client_tax_label : '';
            $temp = [];
            $customer_custom_fields_settings = !empty($il->contact_custom_fields) ? $il->contact_custom_fields : [];
            if (!empty($customer->custom_field1) && in_array('custom_field1', $customer_custom_fields_settings)) {
                $temp[] = $customer->custom_field1;
            }
            if (!empty($customer->custom_field2) && in_array('custom_field2', $customer_custom_fields_settings)) {
                $temp[] = $customer->custom_field2;
            }
            if (!empty($customer->custom_field3) && in_array('custom_field3', $customer_custom_fields_settings)) {
                $temp[] = $customer->custom_field3;
            }
            if (!empty($customer->custom_field4) && in_array('custom_field4', $customer_custom_fields_settings)) {
                $temp[] = $customer->custom_field4;
            }
            if (!empty($temp)) {
                $output['customer_custom_fields'] .= implode(',', $temp);
            }
        }
        
        $output['client_id'] = '';
        $output['client_id_label'] = '';
        if ($il->show_client_id == 1) {
            $output['client_id_label'] = !empty($il->client_id_label) ? $il->client_id_label : '';
            $output['client_id'] = !empty($customer->contact_id) ? $customer->contact_id : '';
        }
        
        
        //Invoice info
        $output['invoice_no'] = $transaction->customer_bill_no;
        
        //Heading & invoice label, when quotation use the quotation heading.
        $output['invoice_no_prefix'] = $il->invoice_no_prefix;
        $output['invoice_heading'] = $il->invoice_heading;
            
        $output['date_label'] = $il->date_label;
        if (blank($il->date_time_format)) {
            $output['invoice_date'] = $this->transactionUtil->format_date($transaction->date, true, $business_details);
        } else {
            $output['invoice_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', $transaction->date)->format($il->date_time_format);
        }
        
        
        $show_currency = true;
        $output['show_cat_code'] = $il->show_cat_code;
        $output['cat_code_label'] = $il->cat_code_label;
        //Subtotal
        $output['subtotal_label'] = $il->sub_total_label . ':';
        $output['subtotal'] = ($transaction->total_amount != 0) ? $this->transactionUtil->num_f($transaction->total_amount, $show_currency, $business_details) : 0;
        $output['subtotal_unformatted'] = ($transaction->total_amount != 0) ? $transaction->total_amount: 0;
        //Discount
        $output['line_discount_label'] = $invoice_layout->discount_label;
        $output['discount_label'] = $invoice_layout->discount_label;
        $discount = $transaction->discount_amount;
        
        $output['discount'] = ($discount != 0) ? $this->transactionUtil->num_f($discount, $show_currency, $business_details) : 0;
        
        
        //Order Tax
        $tax = $transaction->tax_amount;
        $output['tax_label'] = $invoice_layout->tax_label;
        $output['tax_label'] .= ':';
        $output['tax'] = ($transaction->tax_amount != 0) ? $this->transactionUtil->num_f($transaction->tax_amount, $show_currency, $business_details) : 0;
        
        
        $output['total_label'] = $invoice_layout->total_label . ':';
        $output['total'] = $this->transactionUtil->num_f($transaction->total_amount-$transaction->tax_amount, $show_currency, $business_details);
        $output['total_vat'] = $this->transactionUtil->num_f($transaction->tax_amount, $show_currency, $business_details);  
        $output['total_with_vat'] = $this->transactionUtil->num_f($transaction->total_amount, $show_currency, $business_details);  
        
        $output['footer_text'] = $invoice_layout->footer_text;
        $output['design'] = $il->design;
        return (object) $output;
    }
    
    
}
