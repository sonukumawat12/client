<?php

namespace Modules\Vat\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
use Modules\Vat\Entities\VatContact;

use Modules\Vat\Entities\VatProduct;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Petro\Entities\PumpOperator;
use Modules\Vat\Entities\VatSettlement;

use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;
;

use Modules\Vat\Entities\VatMeterSale;
use Modules\Vat\Entities\VatOtherSale;

use Modules\Vat\Entities\VatSettlementCardPayment;
use Modules\Vat\Entities\VatSettlementCashPayment;
use Modules\Vat\Entities\VatSettlementCreditSalePayment;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\ContactController;

use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Validator;

use Modules\Superadmin\Entities\Subscription;

class VatAddPaymentController extends Controller
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
     * @param ProductUtils $Vatproduct
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
     * @return Response
     */
    public function index()
    {
        return view('vat::index');
    }

    
    public function create(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        $business = Business::where('id', $business_id)->first();
        $pos_settings = json_decode($business->pos_settings,true);
        $cash_denoms = !empty($pos_settings['cash_denominations']) ? explode(',',$pos_settings['cash_denominations']) : array();

        $settlement_no = $request->settlement_no;

        $pump_operator_id = $request->operator_id;
    
        $settlement = VatSettlement::where('settlement_no', $settlement_no)->where('business_id', $business_id)->first();
        
        $pump_operator = PumpOperator::where('id', $pump_operator_id)->first();

        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        $payment_types = $this->productUtil->payment_types($default_location,false, false, false, false,"is_sale_enabled");

        
        $customers = VatContact::customersDropdown($business_id, false, true, 'customer');
        $products = VatProduct::where('business_id', $business_id)->pluck('name', 'id');

        $card_types = [];
        $card_group = AccountGroup::where('business_id', $business_id)->where('name', 'Card')->first();
        if (!empty($card_group)) {
            $card_types = Account::where('business_id', $business_id)->where('asset_type', $card_group->id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        }

        $settlement_cash_payments = VatSettlementCashPayment::leftjoin('vat_contacts', 'vat_settlement_cash_payments.customer_id', 'vat_contacts.id')
            ->where('vat_settlement_cash_payments.settlement_no', $settlement->id)
            ->select('vat_settlement_cash_payments.*', 'vat_contacts.name as customer_name')
            ->get();
            
        $settlement_card_payments = VatSettlementCardPayment::leftjoin('vat_contacts', 'vat_settlement_card_payments.customer_id', 'vat_contacts.id')
            ->leftjoin('accounts', 'vat_settlement_card_payments.card_type', 'accounts.id')
            ->where('vat_settlement_card_payments.settlement_no', $settlement->id)
            ->select('vat_settlement_card_payments.*', 'vat_contacts.name as customer_name', 'accounts.name as card_type')
            ->get();
            
        
        $settlement_credit_sale_payments = VatSettlementCreditSalePayment::leftjoin('vat_contacts', 'vat_settlement_credit_sale_payments.customer_id', 'vat_contacts.id')
            ->leftjoin('products', 'vat_settlement_credit_sale_payments.product_id', 'products.id')
            ->where('vat_settlement_credit_sale_payments.settlement_no', $settlement->id)
            ->select('vat_settlement_credit_sale_payments.*', 'vat_contacts.name as customer_name', 'products.name as product_name')
            ->get();
        
        
        
        $total_commission = $this->calculateCommission($pump_operator_id, $settlement->id);
        
        $business_details = Business::find($business_id);
        $currency_precision = $business_details->currency_precision;

        $total_meter_sale = VatMeterSale::where('settlement_no', $settlement->id)->sum('discount_amount');
        $total_other_sale = VatOtherSale::where('settlement_no', $settlement->id)->sum('sub_total');
        $total_other_sale_discount = VatOtherSale::where('settlement_no', $settlement->id)->sum('discount_amount');
        
        $total_other_sale -= $total_other_sale_discount;
        
        $total_amount = number_format(($total_meter_sale + $total_other_sale), $currency_precision, '.', '');

        $total_settlement_cash_payment = VatSettlementCashPayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_settlement_card_payment = VatSettlementCardPayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_settlement_credit_sale_payment = VatSettlementCreditSalePayment::where('settlement_no', $settlement->id)->sum('amount');
        $total_settlement_credit_sale_discount = VatSettlementCreditSalePayment::where('settlement_no', $settlement->id)->sum('total_discount');
        
        $total_paid = number_format(($total_settlement_cash_payment + $total_settlement_card_payment + $total_settlement_credit_sale_payment-$total_settlement_credit_sale_discount), $currency_precision, '.', '');
        
        $total_balance = number_format($total_amount - $total_paid, $currency_precision, '.', '');
        
        $bank_account_group_id = AccountGroup::getGroupByName('Bank Account');


        $bank_accounts = Account::where('business_id', $business_id)->where('asset_type', $bank_account_group_id->id)->pluck('name','id');
        
        $subscription = Subscription::current_subscription($business_id);
        $package_details = $subscription->package_details;
        
        $message = $package_details['notsubscribed_message_content'];
        $font_family = $package_details['ns_font_family'];;
        $font_color = $package_details['ns_font_color'];;
        $font_size = $package_details['ns_font_size'];;
        $background_color = $package_details['ns_background_color'];;
        
        $message = !empty($message) ? $message : "You have not subscribed to this module!";
        

        return view('vat::settlement.partials.add_payment')->with(compact(
            'message','font_family','font_color','font_size','background_color','package_details',
            'bank_accounts',
            'settlement',
            'cash_denoms',
            'pump_operator',
            'settlement_cash_payments',
            'settlement_card_payments',
            'settlement_credit_sale_payments',
            'payment_types',
            'customers',
            'products',
            'card_types',
            'total_amount',
            'total_paid',
            'total_balance'
            
        ));
    }
    
    public function getProductPrice(Request $request)
    {
        $product_id =  $request->product_id;
        $product = VatProduct::leftjoin('vat_variations', 'vat_products.id', 'vat_variations.product_id')
            ->where('vat_products.id', $product_id)
            ->select('default_sell_price')
            ->first();
        if (!empty($product)) {
            $price = $product->default_sell_price;
        } else {
            $price = 0.00;
        }
        return ['price' => $price];
    }

    
    public function calculateCommission($pump_operator_id, $settlement_id)
    {
        $all_sales = VatOtherSale::where('settlement_no', $settlement_id)->get();

        $pump_operator = PumpOperator::where('id', $pump_operator_id)->first();
        $pump_operator_commission_type = isset($pump_operator->commission_type) ? $pump_operator->commission_type : '';
        $pump_operator_commission_value = isset($pump_operator->commission_ap) ? $pump_operator->commission_ap : 0;

        if ($pump_operator_commission_type == 'fixed') {
            $total_sales_counts = $all_sales->count();

            return $total_sales_counts * $pump_operator_commission_value;
        } elseif ($pump_operator_commission_type == 'percentage') {
            $total_sales_commission = 0;


            return $total_sales_commission;
        } else {
            return 0.00;
        }
    }
    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        // return view('vat::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit()
    {
        // return view('vat::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request)
    {
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }


    /**
     * add cash payment data to db
     * @return Response
     */
    public function saveCashPayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            $settlement = VatSettlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'amount' => $request->amount,
                'customer_id' => $request->customer_id,
                'note' => $request->note
            );
   
            $settlement_cash_payment = VatSettlementCashPayment::create($data);

            $output = [
                'success' => true,
                'settlement_cash_payment_id' => $settlement_cash_payment->id,
                'msg' => __('petro::lang.success')
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
    
    
    public function deleteCashPayment($id)
    {
        try {
            $payment = VatSettlementCashPayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
                'msg' => __('petro::lang.success')
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
    
   
    public function saveCardPayment(Request $request)
    {
        try {
            $slip_no = trim(str_replace(' ', '', $request->slip_no));
            $today = \Carbon::now()->format('Y-m-d');
            $existingRecord = VatSettlementCardPayment::where('slip_no', $slip_no)
                                ->whereDate('created_at', $today)
                                ->exists();
            if (!empty($slip_no) && $existingRecord) {
               $output = [
                    'success' => false,
                    'msg' => __('messages.duplicate_slip')
                ];
                
                return $output;
            }
            
            $business_id = $request->session()->get('business.id');
            $settlement = VatSettlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'amount' => $request->amount,
                'card_type' => $request->card_type,
                'card_number' => $request->card_number,
                'customer_id' => $request->customer_id,
                'note' => $request->note,
                'slip_no' => $slip_no
            );

            $settlement_card_payment = VatSettlementCardPayment::create($data);

            $output = [
                'success' => true,
                'settlement_card_payment_id' => $settlement_card_payment->id,
                'msg' => __('petro::lang.success')
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
     * delete card payment data to db
     * @return Response
     */
    public function deleteCardPayment($id)
    {
        try {
            $payment = VatSettlementCardPayment::where('id', $id)->first();
            $amount = $payment->amount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount,
                'msg' => __('petro::lang.success')
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
    
    public function saveCreditSalePayment(Request $request)
    {
        try {
            $business_id = $request->session()->get('business.id');
            
            if(!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'same_order_no')){
                $validator = Validator::make($request->all(), [
                    'order_number' => [
                        'required',
                        Rule::unique('vat_settlement_credit_sale_payments', 'order_number')
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
            
            $settlement = VatSettlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();
            
            
            $price = $this->productUtil->num_uf($request->price);
            $unit_discount = $this->productUtil->num_uf($request->unit_discount);
            $qty = $this->productUtil->num_uf($request->qty);
            $amount = $this->productUtil->num_uf($request->amount);
            $sub_total = $this->productUtil->num_uf($request->sub_total);
            $total_discount = $this->productUtil->num_uf($request->total_discount);
            
            
            $data = array(
                'business_id' => $business_id,
                'settlement_no' => $settlement->id,
                'customer_id' => $request->customer_id,
                'product_id' => $request->product_id,
                'order_number' => $request->order_number,
                'order_date' => \Carbon::parse($request->order_date)->format('Y-m-d'),
                'price' => $price,
                'discount' => $unit_discount,
                'qty' => $qty,
                'amount' => $amount,
                'sub_total' => $sub_total,
                'total_discount' => $total_discount,
                'note' => $request->note
            );
            $settlement_credit_sale_payment = VatSettlementCreditSalePayment::create($data);

            $output = [
                'success' => true,
                'settlement_credit_sale_payment_id' => $settlement_credit_sale_payment->id,
                'msg' => __('petro::lang.success')
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
            $payment = VatSettlementCreditSalePayment::where('id', $id)->first();
            $amount = $payment->amount;
            $discount = $payment->total_discount;
            $payment->delete();
            $output = [
                'success' => true,
                'amount' => $amount-$discount,
                'msg' => __('petro::lang.success')
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
