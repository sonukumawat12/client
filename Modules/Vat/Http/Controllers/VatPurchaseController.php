<?php

namespace Modules\Vat\Http\Controllers;

use App\Business;
use App\Account;
use App\Transaction;
use Modules\Vat\Entities\VatUnit;
use Modules\Vat\Entities\VatVariation;

use Modules\Vat\Entities\VatProduct;
use Modules\Vat\Entities\VatProductVariation;

use Modules\Vat\Entities\VatPurchase;
use Modules\Vat\Entities\VatPurchasePayment;
use Modules\Vat\Entities\VatPurchaseProduct;
use Modules\Vat\Entities\VatContact;

use App\TaxRate;
use App\User;
use App\Utils\BusinessUtil;
use App\Utils\ContactUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\NotificationUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Mpdf\Tag\Option;
use Yajra\DataTables\Facades\DataTables;

use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;
;

use App\NotificationTemplate;
use Illuminate\Routing\Controller;

class VatPurchaseController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    protected $contactUtil;
    
    protected $notificationUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil, ModuleUtil $moduleUtil, ContactUtil $contactUtil,NotificationUtil $notificationUtil)
    {
        $this->productUtil = $productUtil;
        $this->transactionUtil = $transactionUtil;
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->contactUtil = $contactUtil;
        $this->notificationUtil = $notificationUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'account_id' => ''
        ];
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $purchases = VatPurchase::leftJoin('vat_contacts', 'vat_purchases.supplier_id', '=', 'vat_contacts.id')
                ->leftJoin('vat_purchase_payments AS TP', function ($join) {
                    $join->on('vat_purchases.id', '=', 'TP.purchase_id');
                })
                ->leftJoin('users as u', 'vat_purchases.created_by', '=', 'u.id')
                ->leftJoin('vat_purchase_products as pl', 'pl.purchase_id', '=', 'vat_purchases.id')
                ->where('vat_purchases.business_id', $business_id)
                ->select(
                    'vat_purchases.*',
                    'vat_contacts.name',
                    'TP.method',
                    'TP.id as tp_id',
                    'TP.account_id',
                    'TP.cheque_number',
                    DB::raw('(SELECT SUM(vat_purchase_payments.amount) FROM vat_purchase_payments WHERE
                    vat_purchase_payments.purchase_id = vat_purchases.id) as amount_paid'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                )
                ->groupBy('vat_purchases.id');

            
            if (!empty(request()->supplier_id)) {
                $purchases->where('vat_contacts.id', request()->supplier_id);
            }
            
            if (!empty(request()->purchase_list_order_no)) {
                $purchases->where('vat_purchases.invoice_no', request()->purchase_list_order_no);
            }
            if (!empty(request()->input('payment_status')) && request()->input('payment_status') != 'overdue') {
                $purchases->where('vat_purchases.payment_status', request()->input('payment_status'));
            } elseif (request()->input('payment_status') == 'overdue') {
                $purchases->whereIn('vat_purchases.payment_status', ['due', 'partial']);
            }


            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $start = request()->start_date;
                $end =  request()->end_date;
                $purchases->whereDate('vat_purchases.invoice_date', '>=', $start)
                    ->whereDate('vat_purchases.invoice_date', '<=', $end);
            }
            
            return Datatables::of($purchases)
                ->addColumn('action', function ($row) {
                    $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                        __("messages.actions") .
                        '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                    
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Vat\Http\Controllers\VatPurchaseController@show', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                    
                        $html .= '<li><a href="#" class="print-invoice" data-href="' . action('\Modules\Vat\Http\Controllers\VatPurchaseController@printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                        
                        $html .= '<li><a href="' . action('\Modules\Vat\Http\Controllers\VatPurchaseController@edit', [$row->id]) . '"><i class="glyphicon glyphicon-edit"></i>' . __("messages.edit") . '</a></li>';
                        
                        $html .= '<li><a href="' . action('\Modules\Vat\Http\Controllers\VatPurchaseController@destroy', [$row->id]) . '" class="delete-purchase"><i class="fa fa-trash"></i>' . __("messages.delete") . '</a></li>';
                        
                        $html .= '<li><a href="#" data-purchase_id="' . $row->id .
                                '" data-status="' . $row->status . '" class="update_status"><i class="fa fa-edit" aria-hidden="true" ></i>' . __("lang_v1.update_status") . '</a></li>';
                    

                    $html .=  '</ul></div>';
                    return $html;
                })
                
                ->removeColumn('id')
                
                ->addColumn(
                    'final_total',
                    '<span class="display_currency final_total" data-currency_symbol="true" data-orig-value="{{empty($deletedBy) ? $total_amount : 0}}">{{$total_amount}}</span>'
                )
                ->editColumn('invoice_date', '{{@format_date($invoice_date)}}')
                ->editColumn(
                    'purchase_status',
                    '<a href="#" class="update_status no-print" data-purchase_id="{{$id}}" data-status="{{$purchase_status}}"><span class="label @transaction_status($purchase_status) status-label" data-status-name="{{__(\'lang_v1.\' . $purchase_status)}}" data-orig-value="{{$purchase_status}}">{{__(\'lang_v1.\' . $purchase_status)}}
                        </span></a>'
                )
                ->editColumn(
                    'payment_status',
                    function ($row) {
                        $payment_status = Transaction::getPaymentStatus($row);
                        return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                    }
                )
                ->addColumn('payment_due', function ($row) {
                    $due = ($row->total_amount - $row->amount_paid) ;
                    $due_html = '<strong>' . __('lang_v1.purchase') . ':</strong> <span class="display_currency payment_due" data-currency_symbol="true" data-orig-value="' . $due . '">' . $due . '</span>';
                    return $due_html;
                })
                ->addColumn('payment_method', function ($row) {
                    
                    $html = '';
                    if ($row->payment_status == 'due') {
                        return 'Credit Purchase';
                    }
                    
                    
                    if (strtolower($row->method) == 'bank_transfer' || strtolower($row->method) == 'direct_bank_deposit' || strtolower($row->method) == 'bank') {
                        $html .= "Bank";
                        
                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<br><b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                        }
                        if(!empty($row->cheque_number)){
                            $html .= '<b>Cheque Number:</b> ' . $row->cheque_number . '</br>';
                        }
                        if(!empty($row->cheque_date)){
                            $html .= '<b>Cheque Date:</b> ' . $this->productUtil->format_date($row->cheque_date) . '</br>';
                        }
                        
                    } else {
                        $html .= ucfirst(str_replace("_"," ",$row->method));
                    }

                    return $html;
                })
                ->rawColumns(['final_total', 'action', 'payment_due', 'payment_status', 'purchase_status', 'ref_no', 'payment_method','invoice_no'])
                ->make(true);
        }

        $suppliers = VatContact::suppliersDropdown($business_id, false);
        $ordernos = [];
        $ordernos = DB::table('vat_purchases')
                    ->select('invoice_no')
                    ->where('business_id', $business_id)
                    ->where('invoice_no','!=',null)
                    ->distinct()
                    ->orderBy('id', 'DESC')
                    // ->limit(5)
                    ->pluck('invoice_no')
                    ->toArray();
        // dd($ordernos);  
        
         $orderStatuses = $this->productUtil->orderStatuses();
        
        return view('vat::purchase.index')
            ->with(compact('suppliers', 'ordernos','orderStatuses'));
    }

   
    public function create()
    {
        
        $business_id = request()->session()->get('user.business_id');

        $taxes = TaxRate::where('business_id', $business_id)
            ->get();
            
        $orderStatuses = $this->productUtil->orderStatuses();
        
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        
        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types(null, false, false, false, false, true);
        
        unset($payment_types['card']);
        unset($payment_types['credit_sale']);
        
        $recent = VatPurchase::where('business_id',$business_id)->get()->last();
        if(!empty($recent)){
            $po = explode('-',$recent->purchase_no) ?? [];
            if(!empty($po) && sizeof($po) > 0){
                $no = "PO-".((int) $po[1] + 1);
            }else{
                $no = "PO-1";
            }
        }else{
            $no = "PO-1";
        }
        $purchase_no = $no;
        
        $suppliers = VatContact::where('business_id', $business_id)->where('type', 'supplier')->pluck('name', 'id');
        $cash_account_id = Account::getAccountByAccountName('Cash')->id;

        return view('vat::purchase.create')
            ->with(compact(
                'purchase_no',
                'taxes',
                'orderStatuses',
                'currency_details',
                'default_purchase_status',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'suppliers',
                'cash_account_id'
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
            
            
            $business_id = request()->session()->get('user.business_id');
            
            $transaction_data = $request->only(['sub_total','vat_invoice', 'invoice_no','invoice_date', 'purchase_no', 'purchase_status', 'supplier_id', 'discount_amount', 'vat_amount', 'total_amount', 'exchange_rate']);

            $exchange_rate = 1;

            $user_id = $request->session()->get('user.id');
            
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            
            
            $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details) * $exchange_rate;

            $transaction_data['vat_amount'] = $this->productUtil->num_uf($transaction_data['vat_amount'], $currency_details) * $exchange_rate;
            $transaction_data['total_amount'] = $this->productUtil->num_uf($transaction_data['total_amount'], $currency_details) * $exchange_rate;
            $transaction_data['sub_total'] = $this->productUtil->num_uf($transaction_data['sub_total'], $currency_details) * $exchange_rate;
            
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['payment_status'] = 'due';
            $transaction_data['invoice_date'] =$transaction_data['invoice_date'];
            
        
            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('purchase');
            
            if (empty($transaction_data['purchase_no'])) {
                $transaction_data['purchase_no'] = $this->productUtil->generateReferenceNumber('purchase', $ref_count);
            }

            $transaction = VatPurchase::create($transaction_data);
            $purchases = $request->input('purchases');
            
            // insert purchase products
            foreach($purchases as $purchase){
                $data = array(
                    "purchase_id" =>$transaction->id,
                    "product_id" =>$purchase['product_id'],
                    "purchase_qty" =>$this->productUtil->num_uf($purchase['quantity']),
                    "free_qty" =>$this->productUtil->num_uf($purchase['free_qty']),
                    "unit_before_discount" =>$this->productUtil->num_uf($purchase['pp_without_discount']),
                    "discount" =>$this->productUtil->num_uf($purchase['discount_percent']),
                    "unit_cost" =>$this->productUtil->num_uf($purchase['purchase_price']),
                    "subtotal_before_tax" =>$this->productUtil->num_uf($purchase['purchase_price']) * $this->productUtil->num_uf($purchase['quantity']),
                    "tax_id" =>$purchase['purchase_line_tax_id'],
                    "tax_amount" =>$this->productUtil->num_uf($purchase['item_tax']),
                    "net_cost" =>$this->productUtil->num_uf($purchase['purchase_price_inc_tax']),
                    "line_total" => $this->productUtil->num_uf($purchase['quantity']) * $this->productUtil->num_uf($purchase['purchase_price_inc_tax']),
                    "profit_margin" =>$this->productUtil->num_uf($purchase['profit_percent']),
                    "unit_selling_price" =>$this->productUtil->num_uf($purchase['default_sell_price'])
                );
                
                VatPurchaseProduct::create($data);
            }
            
            // insert payments
            
            $amt_paid = 0;
            foreach($request->payment as $payment){
                
                if($payment['method'] != 'credit_purchase'){
                    $amt_paid += $this->productUtil->num_uf($payment['amount']);
                }

                $payment_data = [
                    'purchase_id' => $transaction->id,
                    'account_id' => $payment['account_id'],
                    'business_id' => $business_id,
                    'amount' => $this->productUtil->num_uf($payment['amount']),
                    'method' => $payment['method'],
                    'card_transaction_number' => $payment['card_transaction_number'],
                    'cheque_number' => $payment['cheque_number'],
                    'cheque_date' => $payment['cheque_date'],
                    'bank_name' => $payment['bank_name'],
                    'paid_on' => $request->invoice_date,
                    'created_by' => auth()->user()->id,
                    'payment_for' => $request->customer_id,
                    'note' => $payment['note']
                ];
                
                VatPurchasePayment::create($payment_data);
            }
            
            if($amt_paid >= $transaction->total_amount){
                 $transaction->payment_status = 'paid';
            }elseif($amt_paid > 0 && $amt_paid < $transaction->total_amount){
                 $transaction->payment_status = 'partial';
            }else{
                $transaction->payment_status = 'due';
            }
            
            $transaction->save();
            
            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_add_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('vat-module/vat-purchases')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
     
    public function show($id)
    {
        if (!auth()->user()->can('purchase.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $taxes = TaxRate::where('business_id', $business_id)
            ->pluck('name', 'id');
        $purchase = VatPurchase::leftjoin('vat_contacts','vat_contacts.id','vat_purchases.supplier_id')->where('vat_purchases.business_id', $business_id)
            ->where('vat_purchases.id', $id)
            ->select('vat_purchases.*','vat_contacts.name','vat_contacts.mobile')
            ->firstOrFail();

        $payment_methods = $this->productUtil->payment_types(null, false, false, false, false, true);
        
        $products = VatPurchaseProduct::leftjoin('vat_products','vat_products.id','vat_purchase_products.product_id')->where('purchase_id',$id)->select('vat_purchase_products.*','vat_products.name')->get();
        $payments = VatPurchasePayment::leftjoin('accounts','accounts.id','vat_purchase_payments.account_id')->select('vat_purchase_payments.*','accounts.name as account_name')->where('purchase_id',$id)->get();
        
        
        return view('vat::purchase.show')
            ->with(compact('taxes', 'purchase', 'payment_methods','products','payments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
       
         $business_id = request()->session()->get('user.business_id');

        $taxes = TaxRate::where('business_id', $business_id)
            ->get();
            
        $orderStatuses = $this->productUtil->orderStatuses();
        
        $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

        $default_purchase_status = null;
        if (request()->session()->get('business.enable_purchase_status') != 1) {
            $default_purchase_status = 'received';
        }

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $types['supplier'] = __('report.supplier');
        }
        
        $business_details = $this->businessUtil->getDetails($business_id);
        $shortcuts = json_decode($business_details->keyboard_shortcuts, true);

        $payment_line = $this->dummyPaymentLine;
        $payment_types = $this->productUtil->payment_types(null, false, false, false, false, true);
        
        unset($payment_types['card']);
        unset($payment_types['credit_sale']);
        
        $recent = VatPurchase::where('business_id',$business_id)->get()->last();
        if(!empty($recent)){
            $po = explode('-',$recent->purchase_no) ?? [];
            if(!empty($po) && sizeof($po) > 0){
                $no = "PO-".((int) $po[1] + 1);
            }else{
                $no = "PO-1";
            }
        }else{
            $no = "PO-1";
        }
        $purchase_no = $no;
        
        $suppliers = VatContact::where('business_id', $business_id)->where('type', 'supplier')->pluck('name', 'id');
        $cash_account_id = Account::getAccountByAccountName('Cash')->id;
        
        $purchase = VatPurchase::leftjoin('vat_contacts','vat_contacts.id','vat_purchases.supplier_id')->where('vat_purchases.business_id', $business_id)
            ->where('vat_purchases.id', $id)
            ->select('vat_purchases.*','vat_contacts.name','vat_contacts.mobile')
            ->firstOrFail();
            
        $products = VatPurchaseProduct::leftjoin('vat_products','vat_products.id','vat_purchase_products.product_id')->where('purchase_id',$id)->select('vat_purchase_products.*','vat_products.name')->get();
        $payments = VatPurchasePayment::leftjoin('accounts','accounts.id','vat_purchase_payments.account_id')->select('vat_purchase_payments.*','accounts.name as account_name')->where('purchase_id',$id)->get();
        $is_admin = $this->transactionUtil->is_admin(auth()->user(), $business_id);    

        return view('vat::purchase.edit')
            ->with(compact(
                'purchase_no',
                'is_admin',
                'taxes',
                'orderStatuses',
                'currency_details',
                'default_purchase_status',
                'types',
                'shortcuts',
                'payment_line',
                'payment_types',
                'suppliers',
                'cash_account_id',
                'purchase',
                'products',
                'payments'
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
        
        try {
            $business_id = request()->session()->get('user.business_id');
            
            $transaction_data = $request->only(['sub_total','vat_invoice', 'invoice_no','invoice_date', 'purchase_no', 'purchase_status', 'supplier_id', 'discount_amount', 'vat_amount', 'total_amount', 'exchange_rate']);

            $exchange_rate = 1;

            $user_id = $request->session()->get('user.id');
            
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            
            
            $transaction_data['discount_amount'] = $this->productUtil->num_uf($transaction_data['discount_amount'], $currency_details) * $exchange_rate;

            $transaction_data['vat_amount'] = $this->productUtil->num_uf($transaction_data['vat_amount'], $currency_details) * $exchange_rate;
            $transaction_data['total_amount'] = $this->productUtil->num_uf($transaction_data['total_amount'], $currency_details) * $exchange_rate;
            $transaction_data['sub_total'] = $this->productUtil->num_uf($transaction_data['sub_total'], $currency_details) * $exchange_rate;
            
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['payment_status'] = 'due';
            $transaction_data['invoice_date'] = $transaction_data['invoice_date'];
            
        
            DB::beginTransaction();

            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('purchase');
            
            if (empty($transaction_data['purchase_no'])) {
                $transaction_data['purchase_no'] = $this->productUtil->generateReferenceNumber('purchase', $ref_count);
            }

            VatPurchase::where('id',$id)->update($transaction_data);
            VatPurchaseProduct::where('purchase_id',$id)->delete();
            VatPurchasePayment::where('purchase_id',$id)->delete();
            
            $transaction = VatPurchase::findOrFail($id);
            
            $purchases = $request->input('purchases');
            
            // insert purchase products
            foreach($purchases as $purchase){
                $data = array(
                    "purchase_id" =>$transaction->id,
                    "product_id" =>$purchase['product_id'],
                    "purchase_qty" =>$this->productUtil->num_uf($purchase['quantity']),
                    "free_qty" =>$this->productUtil->num_uf($purchase['free_qty']),
                    "unit_before_discount" =>$this->productUtil->num_uf($purchase['pp_without_discount']),
                    "discount" =>$this->productUtil->num_uf($purchase['discount_percent']),
                    "unit_cost" =>$this->productUtil->num_uf($purchase['purchase_price']),
                    "subtotal_before_tax" =>$this->productUtil->num_uf($purchase['purchase_price']) * $this->productUtil->num_uf($purchase['quantity']),
                    "tax_id" =>$purchase['purchase_line_tax_id'],
                    "tax_amount" =>$this->productUtil->num_uf($purchase['item_tax']),
                    "net_cost" =>$this->productUtil->num_uf($purchase['purchase_price_inc_tax']),
                    "line_total" => $this->productUtil->num_uf($purchase['quantity']) * $this->productUtil->num_uf($purchase['purchase_price_inc_tax']),
                    "profit_margin" =>$this->productUtil->num_uf($purchase['profit_percent']),
                    "unit_selling_price" =>$this->productUtil->num_uf($purchase['default_sell_price'])
                );
                
                VatPurchaseProduct::create($data);
            }
            
            // insert payments
            
            $amt_paid = 0;
            foreach($request->payment as $payment){
                
                if($payment['method'] != 'credit_purchase'){
                    $amt_paid += $this->productUtil->num_uf($payment['amount']);
                }

                $payment_data = [
                    'purchase_id' => $transaction->id,
                    'account_id' => $payment['account_id'],
                    'business_id' => $business_id,
                    'amount' => $this->productUtil->num_uf($payment['amount']),
                    'method' => $payment['method'],
                    'card_transaction_number' => $payment['card_transaction_number'],
                    'cheque_number' => $payment['cheque_number'],
                    'cheque_date' => $payment['cheque_date'],
                    'bank_name' => $payment['bank_name'],
                    'paid_on' => $request->invoice_date,
                    'created_by' => auth()->user()->id,
                    'payment_for' => $request->customer_id,
                    'note' => $payment['note']
                ];
                
                VatPurchasePayment::create($payment_data);
            }
            
            if($amt_paid >= $transaction->total_amount){
                 $transaction->payment_status = 'paid';
            }elseif($amt_paid > 0 && $amt_paid < $transaction->total_amount){
                 $transaction->payment_status = 'partial';
            }else{
                $transaction->payment_status = 'due';
            }
            
            $transaction->save();
            
            
            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_update_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
            return back()->with('status', $output);
        }

        return redirect('vat-module/vat-purchases')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('purchase.delete')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            if (request()->ajax()) {
                
                DB::beginTransaction();
                    VatPurchase::where('id',$id)->delete();
                    VatPurchaseProduct::where('purchase_id',$id)->delete();
                    VatPurchasePayment::where('purchase_id',$id)->delete();

                DB::commit();

                $output = [
                    'success' => true,
                    'msg' => __('lang_v1.purchase_delete_success')
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }
    
    
  
    public function getPurchaseEntryRow(Request $request)
    {
        if (request()->ajax()) {
            $product_id = $request->input('product_id');
            $variation_id = $request->input('variation_id');
            $location_id = $request->input('location_id');
            
            $business_id = request()->session()->get('user.business_id');
            
            $product = VatProduct::where('vat_products.id',$product_id)
                                ->first();
                                
            $current_stock = 0;
            
            $hide_tax = 'hide';
            if ($request->session()->get('business.enable_inline_tax') == 1) {
                $hide_tax = '';
            }

            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);

            if (!empty($product_id)) {
                $row_count = $request->input('row_count');
                $product = VatProduct::where('id', $product_id)
                    ->with(['unit'])
                    ->first();
                
                $sub_units = VatUnit::where('id',$product->unit->id)->get();

                $query = VatVariation::where('product_id', $product_id)
                    ->with(['product_variation']);
                if ($variation_id !== '0') {
                    $query->where('id', $variation_id);
                }

                $variations =  $query->get();
                
                $taxes = TaxRate::where('business_id', $business_id)
                    ->get();
                $temp_qty = null;
                $purchase_pos =  0;
                $enable_petro_module =  false;
                //If brands, category are enabled then send else false.
                $categories =  false;
                $brands =  false;
                $is_fuel_category = false;
                $purchase_zero = false;

    
                $active = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'price_changes_module');
                $purchase_zero = auth()->user()->can('purchase_zero');
                return view('vat::purchase.partials.purchase_entry_row')
                    ->with(compact(
                        'active',
                        'categories',
                        'brands',
                        'purchase_pos',
                        'product',
                        'variations',
                        'row_count',
                        'variation_id',
                        'taxes',
                        'currency_details',
                        'hide_tax',
                        'sub_units',
                        'current_stock',
                        'temp_qty',
                        'is_fuel_category',
                        'purchase_zero'
                    ));
            }
        }
    }


    public function checkRefNumber(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        $contact_id = $request->input('contact_id');
        $ref_no = $request->input('ref_no');
        $purchase_id = $request->input('purchase_id');

        $count = 0;
        if (!empty($contact_id) && !empty($ref_no)) {
            //check in transactions table
            $query = Transaction::where('business_id', $business_id)
                ->where('ref_no', $ref_no)
                ->where('contact_id', $contact_id);
            if (!empty($purchase_id)) {
                $query->where('id', '!=', $purchase_id);
            }
            $count = $query->count();
        }
        if ($count == 0) {
            echo "true";
            exit;
        } else {
            echo "false";
            exit;
        }
    }

    
    public function printInvoice($id)
    {
        try {
            $business_id = request()->session()->get('user.business_id');
            $taxes = TaxRate::where('business_id', $business_id)
                ->pluck('name', 'id');
            $purchase = VatPurchase::leftjoin('vat_contacts','vat_contacts.id','vat_purchases.supplier_id')->where('vat_purchases.business_id', $business_id)
                ->where('vat_purchases.id', $id)
                ->select('vat_purchases.*','vat_contacts.name','vat_contacts.mobile')
                ->firstOrFail();
    
            $payment_methods = $this->productUtil->payment_types(null, false, false, false, false, true);
            
            $products = VatPurchaseProduct::leftjoin('vat_products','vat_products.id','vat_purchase_products.product_id')->where('purchase_id',$id)->select('vat_purchase_products.*','vat_products.name')->get();
            $payments = VatPurchasePayment::leftjoin('accounts','accounts.id','vat_purchase_payments.account_id')->select('vat_purchase_payments.*','accounts.name as account_name')->where('purchase_id',$id)->get();
         
            $output = ['success' => 1, 'receipt' => []];
            $output['receipt']['html_content'] = view('vat::purchase.partials.show_details', compact('taxes', 'purchase', 'payment_methods','products','payments'))->render();
            
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Update purchase status.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateStatus(Request $request)
    {
        
        

        try {
            $business_id = request()->session()->get('user.business_id');

            $transaction = VatPurchase::findOrFail($request->input('purchase_id'));

            $update_data['purchase_status'] = $request->input('status');

            DB::beginTransaction();

            $transaction->update($update_data);

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('purchase.purchase_update_success')
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];
        }

        return $output;
    }

 
    public function getInvoiceNo()
    {
        $business_id = request()->session()->get('business.id');
        if(!empty($recent)){
            $po = explode('-',$recent->purchase_no) ?? [];
            if(!empty($po) && sizeof($po) > 0){
                $no = "PO-".((int) $po[1] + 1);
            }else{
                $no = "PO-1";
            }
        }else{
            $no = "PO-1";
        }
        $purchase_no = $no;

        $purchase_count = Transaction::where('business_id', $business_id)->where('type', 'purchase')->count();

        if (!empty($purchase_count)) {
            $number = $purchase_count + 1;
            $purchase_entry_no = 'PE' . $number;
        } else {
            $purchase_entry_no = 'PE' . 1;
        }

        return ['invoice_no' => $purchase_no, 'purchase_entry_no' => $purchase_entry_no];
    }
}