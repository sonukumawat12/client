<?php
namespace Modules\Petro\Http\Controllers;

use App\BusinessLocation;
use App\Contact;
use App\CustomerReference;
use Illuminate\Routing\Controller;
use App\Product;
use Illuminate\Http\Request;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumpOperator;
use App\Utils\ProductUtil;
;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\DailyVoucher;
use Modules\Petro\Entities\DailyVoucherItem;
use Modules\Petro\Entities\IssueCustomerBill;
use Modules\Petro\Entities\IssueCustomerBillDetail;

use App\Transaction;
use App\AccountTransaction;
use App\ContactLedger;
use App\Variation;

use Modules\Petro\Entities\DailyCollection;

use Modules\Petro\Entities\IssueCustomerBillWithVat;
use Modules\Petro\Entities\IssueCustomerBillWithVatDetail;

use Modules\Petro\Entities\CustomerBillVatPrefix;

use Yajra\DataTables\Facades\DataTables;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
use App\Business;
use App\NotificationTemplate;
use App\System;
use App\Utils\ModuleUtil;

class IssueCustomerBillWithVATController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(ProductUtil $productUtil,BusinessUtil $businessUtil,TransactionUtil $transactionUtil,ModuleUtil $moduleUtil)
    {
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if (!auth()->user()->can('issue_customer_bill.access')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {
            $issue_customer_bills = IssueCustomerBillWithVat::leftjoin('pumps', 'issue_customer_bill_with_vat.pump_id', 'pumps.id')
                ->leftjoin('pump_operators', 'issue_customer_bill_with_vat.operator_id', 'pump_operators.id')
                ->leftjoin('contacts', 'issue_customer_bill_with_vat.customer_id', 'contacts.id')
                ->leftjoin('users', 'issue_customer_bill_with_vat.created_by', 'users.id')
                ->where('issue_customer_bill_with_vat.business_id', $business_id)
                ->select(
                    'issue_customer_bill_with_vat.*',
                    'pumps.pump_name',
                    'pump_operators.name as operator_name',
                    'contacts.name as customer_name',
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
                    if (auth()->user()->can('issue_customer_bill.view')) {
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Petro\Http\Controllers\IssueCustomerBillWithVATController@print', $row->id) . '" class="print_bill" ><i class="fa fa-print" aria-hidden="true"></i>' . __("messages.print") . '</a></li>';
                    }

                    $html .=  '</ul></div>';
                    return $html;
                })
                ->editColumn('outstanding_amount','{{@num_format($outstanding_amount)}}')
                ->editColumn('outstanding_amount','{{@num_format($new_outstanding_amount)}}')
                ->rawColumns(['action'])
                ->make(true);
        }
        return view('petro::issue_bill_customer.indexvat');
    }
    
  

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('issue_customer_bill.add')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('business.id');

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        
        $products = Product::where('business_id', $business_id)->forModule('billtocustomer_issuecustomerbillsvat')->pluck('name', 'id');
        
        $prefixes = CustomerBillVatPrefix::where('business_id',$business_id)->pluck('prefix','id');
        
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        
        $pumps = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        
        $pumper_dashboard = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_dashboard');

        return view('petro::issue_bill_customer.createVAT')->with(compact(
            'customers',
            'products',
            'prefixes',
            'business_locations',
            'pumps',
            'pump_operators',
            'pumper_dashboard'
        ));
    }
    
    public function getPrefixes($id){
        $prefixes = CustomerBillVatPrefix::findOrFail($id);
        $business_id = request()->session()->get('business.id');

        $existing = IssueCustomerBillWithVat::where('business_id',$business_id)->where('prefix',$id)->get()->last();
        if(!empty($existing)){
            $current_bill = $existing->customer_bill_no;
            $curr_arr = explode('-',$current_bill);
            $current_no = (int) $curr_arr[sizeof($curr_arr)-1];
        }else{
            $current_no = $prefixes->starting_no;
        }
        
        $new_prefix = $prefixes->prefix."-".($current_no + 1);
        
        return array('bill_no' => $new_prefix);
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
            $business_id = request()->session()->get('business.id');
            // dd($request->all());
            
            $data = array(
                'business_id' => $business_id,
                'date' => \Carbon::parse($request->voucher_order_date)->format('Y-m-d'),
                'customer_bill_no' => $request->customer_bill_no,
                'location_id' => $request->location_id,
                'pump_id' => $request->pump_id,
                'operator_id' => $request->pump_operator_id,
                'customer_id' => $request->customer_id,
                'reference_id' => $request->reference_id,
                'prefix' => $request->prefix_id,
                'created_by' => Auth::user()->id,
                'total_amount' => $this->productUtil->num_uf($request->voucher_order_amount),
                'tax_amount' => $request->vat_total,
                'discount_amount' => 0,
                'outstanding_amount' => $this->productUtil->num_uf($request->voucher_order_outstanding),
                'new_outstanding_amount' => $this->productUtil->num_uf($request->voucher_order_newoutstanding),
                'credit_limit' => $request->voucher_order_creditlimit,
                'limit_balance' => $request->limit_balance
            );
            DB::beginTransaction();
            $issue_customer_bill = IssueCustomerBillWithVat::create($data);

            $total_amount = 0;
            $tax_amount = 0;
            $discount_amount = 0;
            
            // $transaction = $this->createCreditSellTransactions($issue_customer_bill);
            
            foreach ($request->issue_customer_bill['product_id'] as $key => $product_id) {
                $total_amount += $this->productUtil->num_uf($request->issue_customer_bill['sub_total'][$key]);
                $tax_amount += $this->productUtil->num_uf($request->issue_customer_bill['tax'][$key]);
                $discount_amount += $this->productUtil->num_uf($request->issue_customer_bill['discount'][$key]);
                
                $details = array(
                    'business_id' => $business_id,
                    'issue_bill_id' => $issue_customer_bill->id,
                    'product_id' => $product_id,
                    'unit_price' => $this->productUtil->num_uf($request->issue_customer_bill['unit_price'][$key]),
                    'qty' => $this->productUtil->num_uf($request->issue_customer_bill['qty'][$key]),
                    'discount' => $this->productUtil->num_uf($request->issue_customer_bill['discount'][$key]),
                    'tax' => $this->productUtil->num_uf($request->issue_customer_bill['tax'][$key]),
                    'sub_total' => $this->productUtil->num_uf($request->issue_customer_bill['sub_total'][$key]),

                );
                // dd($details);
                $bill_detail = IssueCustomerBillWithVatDetail::create($details);
                
                // $business_locations = BusinessLocation::forDropdown($business_id);
                // $default_location = current(array_keys($business_locations->toArray()));
                
                // $this->createSellTransactions($transaction, $bill_detail, $business_id, $default_location);
                
            }
            

            $issue_customer_bill->total_amount = $total_amount;
            $issue_customer_bill->tax_amount = $tax_amount;
            $issue_customer_bill->discount_amount = $discount_amount;
            
            // $transaction->total_before_tax = $total_amount - $tax_amount;
            // $transaction->final_total = $total_amount;
            // $transaction->tax_amount = $tax_amount;
            // $transaction->discount_amount = $discount_amount;
            // $transaction->save();
            
            // $this->createStockAccountTransactions($transaction);

            $issue_customer_bill->save();
            
            $collection_form_no = (int) (DailyCollection::where('business_id', $business_id)->count()) + 1;
             $data = array(
                'business_id' => $business_id,
                'collection_form_no' => $collection_form_no,
                'pump_operator_id' => $request->pump_operator_id,
                'location_id' => $request->location_id,
                'balance_collection' => 0,
                'current_amount' => $total_amount,
                'created_by' =>  Auth::user()->id
            );

            DailyCollection::create($data);
            
            
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
                    $msg = str_replace('{due_amount}',$this->productUtil->num_f(0),$msg);
                    
                    $msg = str_replace('{transaction_date}',date('Y-m-d', strtotime($issue_customer_bill->date)),$msg);
                    
                    $msg = str_replace('{cumulative_due_amount}', $this->productUtil->num_f($issue_customer_bill->new_outstanding_amount),$msg);
                    
                    
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
                'msg' => __('lang_v1.success'),
                'print_url' => action('\Modules\Petro\Http\Controllers\IssueCustomerBillWithVATController@print', $issue_customer_bill->id),
            ];
        } catch (\Exception $e) {
            DB::rollback();
            
            dd($e);
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }


        return redirect()->back()->with('status', $output);
    }
    
//     public function createAccountTransaction($transaction, $type, $account_id, $sub_type = null, $is_credit_sale)
//     {
//         $account_transaction_data = [
//             'amount' => abs($transaction->final_total),
//             'account_id' => $account_id,
//             'contact_id' => $transaction->contact_id,
//             'type' => $type,
//             'sub_type' => $sub_type,
//             'operation_date' => $transaction->transaction_date,
//             'created_by' => $transaction->created_by,
//             'transaction_id' => $transaction->id
//         ];
        
    
//         AccountTransaction::createAccountTransaction($account_transaction_data);
//         // create ledger transactions
//         if ($sub_type == 'ledger_show') {
//             ContactLedger::createContactLedger($account_transaction_data);
//             if (!$is_credit_sale) {
//                 if ($type == 'debit') {
//                     $ledger_type = 'credit';
//                 }
//                 if ($type == 'credit') {
//                     $ledger_type = 'debit';
//                 }
//                 $account_transaction_data['type'] = $ledger_type;
//                 ContactLedger::createContactLedger($account_transaction_data);
//             }
//         }
//     }
    
//     public function createCreditSellTransactions($sale)
//     {
        
//         $final_total = $sale->total_amount - $sale->discount_amount;
//         $total_before_tax = $sale->total_amount - $sale->tax_amount;
//         $ob_data = [
//             'business_id' => $sale->business_id,
//             'location_id' => $sale->location_id,
//             'type' => 'sell',
//             'status' => 'final',
//             'payment_status' => 'due',
//             'contact_id' => $sale->customer_id,
//             'pump_operator_id' => $sale->operator_id,
//             'transaction_date' => \Carbon::parse($sale->date)->format('Y-m-d'),
//             'total_before_tax' => $total_before_tax,
//             'final_total' => $final_total,
//             'tax_amount' => $sale->tax_amount,
//             'discount_type' => 'fixed',
//             'discount_amount' => $sale->discount_amount,
//             'credit_sale_id' => $sale->id,
//             'is_credit_sale' => 1,
//             'is_settlement' => 0,
//             'created_by' => request()->session()->get('user.id'),
//             'invoice_no' => $sale->customer_bill_no,
//             'sub_type' => 'credit_sale',
            
//         ];
        
       
//         //Create transaction
//         $transaction = Transaction::create($ob_data);
        
//         $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
//         $type = 'debit';
//         $this->createAccountTransaction($transaction, $type, $account_id, 'ledger_show', true);
        
//         return $transaction;
//     }
    
//     public function createStockAccountTransactions($transaction)
//     {
//         $account_transaction_data = [
//             'amount' => abs($transaction->final_total),
//             'operation_date' => $transaction->transaction_date,
//             'created_by' => $transaction->created_by,
//             'transaction_id' => $transaction->id,
//             'note' => null
//         ];

//         $this->transactionUtil->manageStockAccount($transaction, $account_transaction_data, 'credit', $transaction->final_total);
//         $this->transactionUtil->createCostofGoodsSoldTransaction($transaction, 'ledger_show', 'debit');
//         $this->transactionUtil->createSaleIncomeTransaction($transaction, 'ledger_show', 'credit');
//     }
    
//     public function createSellTransactions($transaction, $sale, $business_id, $default_location)
//     {
//         $uf_quantity = $this->productUtil->num_uf($sale->qty);
        
//         $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')
//             ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')
//             ->leftjoin('categories', 'products.category_id', 'categories.id')
//             ->where('products.id', $sale->product_id)
//             ->select('variations.id as variation_id', 'variation_location_details.location_id', 'products.id as product_id', 'categories.name as category_name', 'products.enable_stock')->first();

//         $this->transactionUtil->createOrUpdateSellLinesVatBill($transaction, $product->product_id, $product->variation_id, $product->location_id, $sale);
//         $location_product = !empty($product->location_id) ? $product->location_id : $default_location;
        
//         // if enable stock
//         if ($product->enable_stock && !empty($is_other_sale)) {
            
//             $this->productUtil->decreaseProductQuantity(
//                 $sale->product_id,
//                 $product->variation_id,
//                 $location_product,
//                 $uf_quantity,
//                 0,
//                 'decrease',
//                 0
//             );
            
//             $store_id = Store::where('business_id', $business_id)->first()->id;
// 			$this->productUtil->decreaseProductQuantityStore(
//                 $sale->product_id,
//                 $product->variation_id,
//                 $location_product,
//                 $uf_quantity,
//                 $store_id,
//                 "decrease",
//                 0
//             );

//         }

        
//         return true;
//     }

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
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function getCustomerReference($id)
    {
        $refs = CustomerReference::where('contact_id', $id)->select('reference', 'id')->get();

        $html = '<option>Please Select</option>';

        foreach ($refs as $ref) {
            $html .= '<option value="' . $ref->id . '">' . $ref->reference . '</option>';
        }

        return $html;
    }

    public function getProductPrice($id)
    {
        $price = Product::leftjoin('variations', 'products.id', 'variations.product_id')
            ->where('variations.product_id', $id)
            ->select('sell_price_inc_tax')
            ->first();

        if (!empty($price)) {
            return $this->productUtil->num_f($price->sell_price_inc_tax);
        } else {
            return '0';
        }
    }

    public function getProductRow()
    {
        $index = request()->index;
        $business_id = request()->session()->get('business.id');
        $products = Product::where('business_id', $business_id)->forModule('billtocustomer_issuecustomerbillsvat')->pluck('name', 'id');
        return view('petro::issue_bill_customer.partials.product_row')->with(compact('products', 'index'));
    }

    public function print($id)
    {
        $issue_customer_bill = IssueCustomerBillWithVat::leftjoin('pumps', 'issue_customer_bill_with_vat.pump_id', 'pumps.id')
            ->leftjoin('pump_operators', 'issue_customer_bill_with_vat.operator_id', 'pump_operators.id')
            ->leftjoin('contacts', 'issue_customer_bill_with_vat.customer_id', 'contacts.id')
            ->leftjoin('customer_references', 'issue_customer_bill_with_vat.reference_id', 'customer_references.id')
            ->leftjoin('users', 'issue_customer_bill_with_vat.created_by', 'users.id')
            ->where('issue_customer_bill_with_vat.id', $id)
            ->select(
                'issue_customer_bill_with_vat.*',
                'pumps.pump_name',
                'pump_operators.name as operator_name',
                'customer_references.reference',
                'contacts.name as customer_name',
                'users.username as username'
            )->first();

        $bill_details = IssueCustomerBillWithVatDetail::leftjoin('products', 'issue_customer_bill_with_vat_details.product_id', 'products.id')
            ->where('issue_customer_bill_with_vat_details.issue_bill_id', $id)
            ->select('issue_customer_bill_with_vat_details.*', 'products.name as product_name')
            ->get();
            
        $receipt_details = $this->__getReceiptDetails($issue_customer_bill);

        return view('petro::issue_bill_customer.printvat')->with(compact('issue_customer_bill', 'bill_details','receipt_details'));
    }
    
    
    
    public function __getReceiptDetails($transaction, $receipt_printer_type = 'browser'){
        $business_details = $this->businessUtil->getDetails($transaction->business_id);
        $location_details = BusinessLocation::find($transaction->location_id);
        $invoice_layout = $this->businessUtil->invoiceLayout($transaction->business_id, $transaction->location_id, $location_details->invoice_layout_id);
        $il = $invoice_layout;
        
        $footer_top_margin = System::getProperty('footer_top_margin');
        $admin_invoice_footer = System::getProperty('admin_invoice_footer');
        
        $output = [
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
            'pump_name' => $transaction->pump_name,
            'operator_name' => $transaction->operator_name
        ];
        
        
        $output['display_name'] = $output['business_name'];
        
        if (!empty($output['location_name'])) {
            if (!empty($output['display_name'])) {
                $output['display_name'] .= ', ';
            }
            $output['display_name'] .= $output['location_name'];
        }
        
        $contact_details = $this->transactionUtil->getCustomerDetails($transaction->customer_id);
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
        if ($il->show_tax_1 == 1 && !empty($business_details->tax_number_1)) {
            $output['tax_label1'] = !empty($business_details->tax_label_1) ? $business_details->tax_label_1 . ': ' : '';
            $output['tax_info1'] = $business_details->tax_number_1;
        }
        if ($il->show_tax_2 == 1 && !empty($business_details->tax_number_2)) {
            if (!empty($output['tax_info1'])) {
                $output['tax_info1'] .= ', ';
            }
            $output['tax_label2'] = !empty($business_details->tax_label_2) ? $business_details->tax_label_2 . ': ' : '';
            $output['tax_info2'] = $business_details->tax_number_2;
        }
        
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
        $customer = Contact::find($transaction->customer_id);
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
        $output['total'] = $this->transactionUtil->num_f($transaction->total_amount, $show_currency, $business_details);
        
        $output['footer_text'] = $invoice_layout->footer_text;
        $output['design'] = $il->design;
        return (object) $output;
    }
    
    
}
