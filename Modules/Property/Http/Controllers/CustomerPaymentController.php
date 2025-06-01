<?php

namespace Modules\Property\Http\Controllers;

use App\Contact;
use App\Account;
use App\BusinessLocation;
use App\Business;
use App\Transaction;


use App\ContactLedger;
use App\AccountTransaction;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use App\Utils\ProductUtil;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertyBlock;
use Modules\Property\Entities\PaymentOption;
use Modules\Property\Entities\PropertyFinalize;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
// use Modules\Superadmin\Entities\Subscription;

class CustomerPaymentController extends Controller
{
    protected $moduleUtil;
    protected $commonUtil;
    protected $businessUtil;
    protected $transactionUtil;
    protected $productUtil;

    /**
     * Constructor
     *
     *
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, Util $commonUtil, BusinessUtil $businessUtil, TransactionUtil $transactionUtil,  ProductUtil $productUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->commonUtil = $commonUtil;
        $this->businessUtil = $businessUtil;
        $this->transactionUtil = $transactionUtil;
        $this->productUtil = $productUtil;
        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'bank_name' => ''
        ];
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        return view('property::customer');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
       
        
        $business_id = request()->session()->get('user.business_id');
        $payment_options = PaymentOption::where('business_id', $business_id)->pluck('payment_option', 'id');
        $layout = 'property';
        $customers = Contact::propertyCustomerDropdown($business_id, false, true);
   
        //$customers = Contact::propertyCustomerDropdownForCustomerPayment($business_id, false, true, 1); // this 1 should be replaced with property id
       //dd($customers);
        $payment_types = $this->commonUtil->payment_types();
        $land_and_blocks = Property::getLandAndBlockDropdown($business_id, true, true);
        
        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');
        $payment = $this->dummyPaymentLine;
        
        return view('property::customer_payment.create')->with(compact(
            'layout',
            'payment_options',
            'customers',
            'payment',
            'payment_types',
            'bank_group_accounts',
            'land_and_blocks'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
   /* public function store(Request $request)
    {
        //
        echo "<h2>PHP is Fun!</h2>";
        //dd("hello");
    }*/
    
     public function store(Request $request)
    {
       // dd($request);
            $business_id = request()->session()->get('user.business_id');
            DB::table('temp_data')->where('business_id', $business_id)->update(['pos_create_data' => '']);
            $property_id = $request->project_property_id;
            $property_id2;
            if(is_null($property_id))
            {
                $property_id = $request->property_id;
                //dd($property_id);
                $property_id2 = DB::table('property_blocks')->where('block_number', $property_id)->value('property_id');
                // dd($property_id2);
                $property_id = $property_id2;
            }
            
            $transaction_data = $request->only(['date', 'contact_id', 'final_total', 'discount', 'finance_option_id']);
            $installment_details = $request->only(['installment_start_on', 'installment_ends_on', 'installment_amount']);
            $busines_location = BusinessLocation::where('business_id', $business_id)->first();
            $transaction_data['location_id'] = !empty($busines_location) ? $busines_location->id : null;
            $transaction_data['discount'] = "2325000";
            $exchange_rate = 1; //field in "transactions" table
            $request->validate([
                'customer_id' => 'required',
                'final_total' => 'required',
                // 'final_total' => 'required',
            ]);
            $user_id = Auth::user()->id;
            $currency_details = $this->transactionUtil->purchaseCurrencyDetails($business_id);
            
            //final total will be store in debit column
            
            //unformat input values
            $transaction_data['total_before_tax'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;
            $transaction_data['tax_amount'] = 0;
            $transaction_data['shipping_charges'] = 0;
            $transaction_data['final_total'] = $this->productUtil->num_uf($transaction_data['final_total'], $currency_details) * $exchange_rate;
            $transaction_data['discount'] = $this->productUtil->num_uf($transaction_data['discount'], $currency_details);
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'property_sell';
            $transaction_data['payment_status'] = 'paid';
            $transaction_data['status'] = 'final';
            $transaction_data['store_id'] = null;
            $transaction_data['transaction_date'] = $this->productUtil->uf_date($transaction_data['date'], false);
            unset($transaction_data['date']);
            unset($transaction_data['discount']);
            // $subscription = Subscription::active_subscription($business_id);
            // $monthly_max_sale_limit = $subscription->package->monthly_max_sale_limit;
            // $startOfMonth = \Carbon::now()->startOfMonth()->toDateString();
            // $endOfMonth = \Carbon::now()->endOfMonth()->toDateString();
            // $current_monthly_sale = DB::table('transactions')
            // ->select(DB::raw('sum(final_total) as total'))
            // ->where('business_id', $business_id)
            // ->whereIn('type', ['sell', 'property_sell'])
            // ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])
            // ->groupBy('business_id')
            // ->first();
        // $current_monthly_sale = is_null($current_monthly_sale) ? 0 : (double) $current_monthly_sale->total;
        // $current_monthly_sale += $transaction_data['final_total'];
        // if($current_monthly_sale > $monthly_max_sale_limit) {
        //     $output = [
        //         'success' => 0,
        //         'msg' => __('lang_v1.monthly_max_sale_limit_exceeded', ['monthly_max_sale_limit' => $monthly_max_sale_limit])
        //     ];
        //     return redirect()
        //         ->action('\Modules\Property\Http\Controllers\SaleAndCustomerPaymentController@dashboard')
        //         ->with('status', $output);
        // }
            DB::beginTransaction();
            //Update reference count
            $ref_count = $this->productUtil->setAndGetReferenceCount('installment');
            //Generate reference number
            if (empty($transaction_data['ref_no'])) {
                $transaction_data['ref_no'] = $this->productUtil->generateReferenceNumber('installment', $ref_count);
            }
            $transaction_data['invoice_no'] = $this->transactionUtil->getInvoiceNumber($business_id, 'final', $transaction_data['location_id']);
            //dd($transaction_data);
            $transaction = Transaction::create($transaction_data);
            
            
            
            // $sell_lines = $request->sell_line;
            // $sold_block_ids = [];
            // $sold_block_nos = [];
            // $sold_total_block_value = [];
            // if(!empty($sell_lines) && $sell_lines != null && $sell_lines !=''){
            //     foreach ($sell_lines as $sell_line) {
            //         $sell_line_array = [
            //             'transaction_id' => $transaction->id,
            //             'property_id' => $sell_line['property_id'],
            //             'block_id' => $sell_line['block_id'],
            //             'block_number' => $sell_line['block_number'],
            //             'unit' => $sell_line['unit'],
            //             'size' => $sell_line['size'],
            //             'block_value' => $sell_line['block_value']
            //         ];
            //         $sold_block_nos[] = $sell_line['block_number'];
            //         $sold_block_ids[] = $sell_line['block_id'];
            //         $sold_total_block_value[] = $sell_line['block_value'];
            //         PropertySellLine::create($sell_line_array);
            //         //PropertyBlock::where('id', $sell_line['block_id'])->update(['block_sold_price' =>  $transaction_data['discount']]);
            //         PropertyBlock::where('id', $sell_line['block_id'])->update(['block_sold_price' =>  $sell_line['block_value']]);
            //     }
            // }
            // if (!empty($sold_block_ids)) {
            //     PropertyBlock::whereIn('id', $sold_block_ids)->update(['customer_id' => $transaction->contact_id, 'is_sold' => 1, 'sold_by' => $user_id]);
            // }
            
            
            $payments = $request->input('account_of');
            $payment_record = $request->input('payment');
            
            //dd($payment_record);
            $on_account_of['total_amount'] = $request->total_amount;
            // $on_account_of_record = [];
            // foreach($payments as $valPayment){
            //     if($request->payment_method =='bank_transfer'){
            //         $temp['on_account_of'] = $request->payment_option;
            //     }
            // }
             $this->createSellAccountTransactions($transaction, $payments, $payment_record, $business_id);
    
            DB::commit();
            $business = Business::find($business_id);
            $location_details = BusinessLocation::where('business_id', $business_id)->first();
            $contact = Contact::find($transaction->contact_id);
            //dd($contact);
            //dd($payments);
            //dd($property_id);
            
            
            $property = Property::find($property_id);
            //dd($property);
                if(is_null($property))
                {
                    // dd($property_id2);
                    $property = DB::table('property_blocks')->where('block_number', $property_id2)->value('property_id');
                    $property = Property::find($property_id2);
                    //dd($property);
                }
            //dd($property);
            
            
            // $block_value = PropertySellLine::where('transaction_id', $transaction->id)->first();
            // $installment_details = $request->only(['installment_start_on', 'installment_ends_on', 'installment_amount']);
            $block_value = $request->property_id;
            $paid_amount = $request->final_total;
  
            return view('property::customer_payment.print')->with(compact(
                'business',
                // 'installment_details',
                'location_details',
                'transaction',
                'contact',
                'property',
                'payments',
                'payment_record',
                'block_value',
                'paid_amount'
                // 'sold_total_block_value'
            ));
        
      
    }
    public function createSellAccountTransactions($transaction, $payments, $payment_record, $business_id)
    {
        // $transaction_sell_line = PropertySellLine::where('transaction_id', $transaction->id)->first();
        // $property_accounts = PropertyAccountSetting::where('property_id', $transaction_sell_line->property_id)->first();
        $account_transaction_data['contact_id'] = $transaction->contact_id; //if not work, change it with customer_id
        // $account_transaction_data['account_id'] = 0;
        //$account_transaction_data['account_id'] = $property_accounts->income_account_id;
        // $account_transaction_data['amount'] = $transaction->final_total;
        $account_transaction_data['amount'] = $transaction->final_total;
        $account_transaction_data['type'] = 'credit'; // changings are made(debit to credit) because it was required to show "1. On Account of => Debit column of the customer ledger "
        $account_transaction_data['created_by'] = Auth::user()->id;
        $account_transaction_data['transaction_id'] = $transaction->id;
        //$account_transaction_data['transaction_sell_line_id'] = $transaction_sell_line->id;
        ContactLedger::createContactLedger($account_transaction_data);
        if(!is_null($payments)) {
            foreach($payments as $payment) {
                $account_transaction_data['type'] = 'debit';
                $account_transaction_data['payment_option_id'] = $payment['payment_option_id'];
                $account_transaction_data['amount'] = $payment['amount'];
                ContactLedger::createContactLedger($account_transaction_data);
                // if (!empty($property_accounts->income_account_id)) {
                //     $account_transaction_data['type'] = 'credit';
                //     $account_transaction_data['account_id'] = $property_accounts->income_account_id;
                    
                //     AccountTransaction::createAccountTransaction($account_transaction_data);
                // }
                // if (!empty($property_accounts->account_receivable_account_id)) {
                //     $account_transaction_data['type'] = 'debit';
                //     $account_transaction_data['account_id'] = $property_accounts->account_receivable_account_id;
                //     AccountTransaction::createAccountTransaction($account_transaction_data);
                // }
            }
        }
        if(!is_null($payment_record)) {
            foreach($payment_record as $payment) {
                if(isset($payment['payment_method_amount'])) {
                    $account_transaction_data['amount'] = $payment['payment_method_amount'];
                    if ($payment['method'] === 'card') {
                        $account = Account::where('business_id', $business_id)->where('name', 'Cards (Credit Debit)  Account')->first();
                    } else if($payment['method'] === 'cheque') {
                        $account = Account::where('business_id', $business_id)->where('name', 'Cheques in Hand')->first();
                    } else if($payment['method'] === 'cash') {
                        $account = Account::where('business_id', $business_id)->where('name', 'Cash')->first();
                    }
                    // dd($account);
                    if (!is_null($account)) {
                        $account_transaction_data['type'] = 'debit';
                        $account_transaction_data['account_id'] = $account->id;
                        $account_transaction_data['payment_method'] = $payment['method'];
                        if($payment['method'] === 'cheque'){
                            $account_transaction_data['cheque_number'] = $payment['cheque_number'];
                            $account_transaction_data['bank_name'] = $payment['bank_name'];
                            $account_transaction_data['cheque_date'] = $payment['cheque_date'];
                        }
                        AccountTransaction::createAccountTransaction($account_transaction_data);
                        if (!empty($property_accounts->account_receivable_account_id)) {
                            $account_transaction_data['type'] = 'credit';
                            $account_transaction_data['account_id'] = $property_accounts->account_receivable_account_id;
                            $account_transaction_data['payment_method'] = $payment['method'];
                            if($payment['method'] === 'cheque'){
                                $account_transaction_data['cheque_number'] = $payment['cheque_number'];
                                $account_transaction_data['bank_name'] = $payment['bank_name'];
                                $account_transaction_data['cheque_date'] = $payment['cheque_date'];

                            }
                            AccountTransaction::createAccountTransaction($account_transaction_data);
                        }
                    }
                }
            }
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('property::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('property::edit');
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

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }

    /**
     * get property dropddown by customer id
     * @param int $customer_id
     * @return Renderable
     */
    public function getPropertyDropdownByCustomer($customer_id){
        
        $properties = Property::getLandAndBlockByCustomerDropdown($customer_id, true, true);
        

        return $this->transactionUtil->createDropdownHtml($properties, 'Please Select');
    }
    
    public function getPropertyInstallmentDetails($property_id){
        
        
        
        /*---------------New Query Start--------------*/
        
        $propertyInstallmentDetails = DB::table('property_finalizes')
            ->leftJoin('installment_cycles', 'property_finalizes.installment_cycle_id', '=', 'installment_cycles.id')
            ->where('property_finalizes.block_id', '=', $property_id)
            ->get();
        
        //dd($propertyInstallmentDetails);
        //dd($property_id);
        
        //dd($propertyInstallmentDetails);
        return ['success' => 1, 'data' => $propertyInstallmentDetails];
        
        /*---------------New Query End--------------*/
        
        
        
        
        /*
        
         $propertyInstallmentDetails = PropertyFinalize::leftjoin('property_finalize', 'property_blocks.id', 'property_finalize.block_id')
            ->leftjoin('property_finalize',  'installment_cycles.id', 'property_finalize.installment_cycle_id')
            ->where('property_blocks.id', $property_id)
            ->select('property_finalize.*');
            ->where('installment_cycles.id', '=' , 'property_finalize.installment_cycle_id')
            ->select('property_finalize.*', DB::raw('CONCAT(actual_name, " (", short_name, ")") as name'))->first();
        
        $propertyInstallmentDetails = PropertyFinalize::getInstallmentDetailsByPropertyBlock($property_id, true);
        dd($propertyInstallmentDetails);
   
        return ['success' => 1, 'data' => $propertyInstallmentDetails];
        */
        
    }
    
    
}
