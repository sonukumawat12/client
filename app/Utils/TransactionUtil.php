<?php
namespace App\Utils; 
use App\AccountTransaction;
use App\Account;
use App\AccountType;
use Modules\Fleet\Entities\Fleet;
use App\Business;
use App\BusinessLocation;
use App\Utils\Util;
use App\Category;
use App\Contact;
use App\ContactLedger;
use App\Currency;
use App\Events\TransactionPaymentAdded;
use App\Events\TransactionPaymentDeleted;
use App\Events\TransactionPaymentUpdated;
use App\Exceptions\PurchaseSellMismatch;
use App\Http\Controllers\Ecom\ContactController;
use App\InvoiceScheme;
use App\Product;
use App\PurchaseLine;
use App\Restaurant\ResTable;
use App\TaxRate;
use App\Transaction;
use App\TransactionPayment;
use App\TransactionSellLine;
use App\TransactionSellLinesPurchaseLines;
use App\Variation;
use App\VariationLocationDetails;
use App\VariationStoreDetail;
use Illuminate\Support\Facades\DB;
use App\PaymentMethod;
use App\System;
;
use Illuminate\Support\Facades\Auth;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\PumpOperator;
use Modules\Petro\Entities\TankSellLine;
use Modules\Petro\Entities\TankPurchaseLine;
use Modules\Property\Entities\Property;
use Modules\Property\Entities\PropertyBlock;
use Modules\Property\Entities\PropertySellLine;
use Modules\Property\Entities\PropertyAccountSetting;
use Modules\Petro\Entities\DipReading;
use Modules\Petro\Entities\PumpOperatorCommission;
use App\Variation_store_detail;
use App\ExpenseCategory;
use App\Utils\ModuleUtil;
use App\Utils\ContactUtil;
use Modules\Petro\Entities\Settlement;
use Modules\Petro\Entities\TankTransfer;

use Modules\Vat\Entities\VatCustomerStatement;
use Modules\Vat\Entities\VatCustomerStatementDetail;

use Modules\Shipping\Entities\ShippingAgentCommission;
use Modules\Shipping\Entities\ShippingPartnerCommission;
use Modules\SMS\Entities\SmsListInterest;
use Modules\Superadmin\Entities\RefillBusiness;
use App\SmsLog;

use Modules\Superadmin\Entities\Subscription;
use Modules\Vat\Entities\VatSetting;

use Modules\Superadmin\Entities\SmsApiClient;
use Modules\Superadmin\Entities\SmsReminderSetting;
use Modules\Petro\Entities\PumpOperatorOtherSale;
use App\ProductVariation;
use App\Unit;
use App\Brands;
use Modules\Petro\Entities\DailyVoucherItem;
use Modules\Petro\Entities\DailyVoucher;
use App\Http\Controllers\SellController;
use Illuminate\Http\Request;

class TransactionUtil extends Util
{
    protected $moduleUtil;
    protected $contactUtil;
    public $petro_classes;
    public $bakery_classes;
    
    public $payment_transaction_types;
    public $outstanding_payment_types;
    
    public function __construct(ModuleUtil $moduleUtil, ContactUtil $contactUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->contactUtil = $contactUtil;
        $this->petro_classes = array(
            'Modules\Petro\Entities\CurrentMeter',
            'Modules\Petro\Entities\DailyVoucher',
            'Modules\Petro\Entities\DailyCollection',
            'Modules\Petro\Entities\DailyCard',
            'Modules\Petro\Entities\CustomerPayment',
            'Modules\Petro\Entities\CustomerBillVatPrefix',
            'Modules\Petro\Entities\DailyVoucherItem',
            'Modules\Petro\Entities\IssueCustomerBillDetail',
            'Modules\Petro\Entities\IssueCustomerBill',
            'Modules\Petro\Entities\FuelTank',
            'Modules\Petro\Entities\DipResetting',
            'Modules\Petro\Entities\DipReading',
            'Modules\Superadmin\Entities\TankDipChart',
            'Modules\Superadmin\Entities\TankDipChartDetail',
            'Modules\Petro\Entities\PumperDayEntry',
            'Modules\Petro\Entities\Pump',
            'Modules\Petro\Entities\OtherSale',
            'Modules\Petro\Entities\OtherIncome',
            'Modules\Petro\Entities\OpeningMeter',
            'Modules\Petro\Entities\MeterSale',
            'Modules\Petro\Entities\MeterResetting',
            'Modules\Petro\Entities\IssueCustomerBillWithVatDetail',
            'Modules\Petro\Entities\IssueCustomerBillWithVat',
            'Modules\Petro\Entities\Settlement',
            'Modules\Petro\Entities\PumpOperatorPreAssignment',
            'Modules\Petro\Entities\PumpOperatorPayment',
            'Modules\Petro\Entities\PumpOperatorCommission',
            'Modules\Petro\Entities\PumpOperatorAssignment',
            'Modules\Petro\Entities\PumpOperator',
            'Modules\Petro\Entities\SettlementDrawingPayment',
            'Modules\Petro\Entities\SettlementCustomerLoan',
            'Modules\Petro\Entities\SettlementCreditSalePayment',
            'Modules\Petro\Entities\SettlementChequePayment',
            'Modules\Petro\Entities\SettlementCashPayment',
            'Modules\Petro\Entities\SettlementCashDeposit',
            'Modules\Petro\Entities\SettlementCardPayment',
            'Modules\Petro\Entities\UnloadStock',
            'Modules\Petro\Entities\TankTransfer',
            'Modules\Petro\Entities\TanksTransactionDetail',
            'Modules\Petro\Entities\TankSellLine',
            'Modules\Petro\Entities\TankPurchaseLine',
            'Modules\Petro\Entities\SettlementShortagePayment',
            'Modules\Petro\Entities\SettlementLoanPayment',
            'Modules\Petro\Entities\SettlementExpensePayment',
            'Modules\Petro\Entities\SettlementExcessPayment',
            'Modules\Petro\Entities\DayEnd',
            'App\Settlement',
            
        );
        
        $this->bakery_classes = array(
                'Modules/Bakery/Entities/BakeryUser',
                'Modules/Bakery/Entities/BakeryRoute',
                'Modules/Bakery/Entities/BakeryProduct',
                'Modules/Bakery/Entities/BakeryOpeningBalance',
                'Modules/Bakery/Entities/BakeryLoadingReturnProduct',
                'Modules/Bakery/Entities/BakeryLoadingReturn',
                'Modules/Bakery/Entities/BakeryLoadingProduct',
                'Modules/Bakery/Entities/BakeryLoading',
                'Modules/Bakery/Entities/BakeryInvoiceNumber',
                'Modules/Bakery/Entities/BakeryFleet',
                'Modules/Bakery/Entities/BakeryDriver'
            );
        
        
        
        $this->payment_transaction_types = array(
            'advance_payment' => 'Advance Payment',
            'airline_ticket' => 'Airline Ticket',
            'cheque_opening_balance' => 'Cheque Opening Balance',
            'direct_customer_loan' => 'Customer Loan',
            // 'expense' => 'Expense',
            'fleet_opening_balance' => 'Fleet Opening Balance',
            'opening_balance' => 'Opening Balance',
            // 'property_purchase' => 'Property Purchase',
            // 'purchase' => 'Purchase',
            'purchase_return' => 'Purchase Return',
            'route_operation' => 'ROute Operation',
            'security_deposit' => 'Security Deposit',
            // 'security_deposit_refund' => 'Security Deposit Refund',
            'sell' => 'Sell',
            'sell_return' => 'Sale Return',
            'settlement' => 'Settlement',
            'shipment' => 'Shipment'
        );
        
        $this->outstanding_payment_types = array(
            'advance_payment',
            'airline_ticket',
            'cheque_opening_balance',
            'direct_customer_loan',
            'fleet_opening_balance',
            'opening_balance',
            'purchase_return',
            'route_operation',
            'security_deposit',
            'sell',
            'settlement',
            'shipment'
        );
        
    }
    
    public function getOutstandingInvoices($start_date,$end_date){
        $outstanding_types = ($this->outstanding_payment_types);
        
        $business_id = request()->session()->get('user.business_id');
        
         $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')

                ->whereNull('tp.deleted_at')

                ->where('transactions.business_id', $business_id)

                ->where('contacts.type', 'customer')

                ->whereIn('transactions.payment_status', ['paid', 'partial'])
                
                ->whereIn('transactions.type',$outstanding_types)
                
                ->whereDate('tp.paid_on', '>=', $start_date)

                ->whereDate('tp.paid_on', '<=', $end_date)
                
                ->whereNotNull('transactions.invoice_no')
                
                ->select('transactions.invoice_no')
                
                -> pluck('transactions.invoice_no','transactions.invoice_no');
        
            
        return $sells;
    }
    
    public function getOutstandingPaymentRefs($start_date,$end_date){
        $outstanding_types = ($this->outstanding_payment_types);
        
        $business_id = request()->session()->get('user.business_id');
            
        $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')

                ->whereNull('tp.deleted_at')

                ->where('transactions.business_id', $business_id)

                ->where('contacts.type', 'customer')

                ->whereIn('transactions.payment_status', ['paid', 'partial'])
                
                ->whereIn('transactions.type',$outstanding_types)
                
                ->whereDate('tp.paid_on', '>=', $start_date)

                ->whereDate('tp.paid_on', '<=', $end_date)
                
                ->whereNotNull('tp.payment_ref_no')
            
                ->select('tp.payment_ref_no')
                
                ->distinct('tp.payment_ref_no')
                -> pluck('tp.payment_ref_no','tp.payment_ref_no');
            
        return $sells;
    }
    
    public function getOutstandingCheques($start_date,$end_date){
        $outstanding_types = ($this->outstanding_payment_types);
        
        $business_id = request()->session()->get('user.business_id');
            
        $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')

                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')

                ->whereNull('tp.deleted_at')

                ->where('transactions.business_id', $business_id)

                ->where('contacts.type', 'customer')

                ->whereIn('transactions.payment_status', ['paid', 'partial'])
                
                ->whereIn('transactions.type',$outstanding_types)
                
                ->whereDate('tp.paid_on', '>=', $start_date)

                ->whereDate('tp.paid_on', '<=', $end_date)
                
                ->whereNotNull('tp.cheque_number')
            
                ->select('tp.cheque_number')
                
                ->distinct('tp.cheque_number')
                
                -> pluck('tp.cheque_number','tp.cheque_number');
            
        return $sells;
    }
    
    
    /**
     * Add Sell transaction
     *
     * @param int $business_id
     * @param array $input
     * @param float $invoice_total
     * @param int $user_id
     *
     * @return object
     * 
     */
    

     
    public function validateNos($nos) {
        $business_id = request()->session()->get('user.business_id');
        $numbers = explode(',', str_replace(' ','',$nos));
        
        $validNumbers = [];
        $invalidNumbers = [];

        $business = Business::findOrFail($business_id);
        $settings = json_decode($business->hms_settings, true);
    
        foreach ($numbers as $number) {
            $number = trim($number);
        
            $isAllCountries = isset($settings['all_countries']) && $settings['all_countries'];
        
            if ($isAllCountries) {
                // Allow all numbers
                $validNumbers[] = $number;
            } else {
                // Check against specific allowed country codes
                $allowedCodes = explode(',', $settings['applicable_country_codes'] ?? '');
        
                // Extract the country code from the beginning of the number (assume max 4-digit codes)
                foreach ($allowedCodes as $code) {
                    if (strpos($number, $code) === 0) {
                        $validNumbers[] = $number;
                        continue 2; // Break out of both inner and outer loop on match
                    }
                }
        
                // If we didn’t match any allowed code
                $invalidNumbers[] = $number;
            }
        }
    
        return [
            'valid' => $validNumbers,
            'invalid' => $invalidNumbers,
        ];
    }

    
    public function __getVatEffectiveDate($business_id){
        // vat effective date
            $subscription = Subscription::active_subscription($business_id);
        	$pacakge_details = $subscription->package_details ?? [];
        	
        	$vat_settings = VatSetting::where('business_id', $business_id)->where('status',1)->first();
           
            
            if(!empty($vat_settings)){
                if(!empty($pacakge_details) && !empty($pacakge_details['vat_effective_date'])){
                    
                    if(strtotime($vat_settings->effective_date) > strtotime($pacakge_details['vat_effective_date'])){
                        $pacakge_details['vat_effective_date'] = $vat_settings->effective_date;
                    }
                    
                }else{
                    $pacakge_details['vat_effective_date'] = $vat_settings->effective_date;
                }
                
            }
            
            $effective_date = (!empty($pacakge_details) && !empty($pacakge_details['vat_effective_date'])) ? $pacakge_details['vat_effective_date'] : date('Y-m-d');
            
            return $effective_date;
    }
    public function __getSMSBalance($date, $business_id = null, $business_type = 'business'){  
        
        if(empty($business_id)){
            $business_id = request()->session()->get('business.id');
        }
        
        $interest = SmsListInterest::where('sms_list_interests.business_id', $business_id)->where('type',$business_type)
            ->where('date','<=',$date)->sum('amount');
            
        $refill = RefillBusiness::leftjoin('sms_refill_packages','sms_refill_packages.id','refill_business.package_id')
            ->where('refill_business.business_id', $business_id)->where('refill_business.type',$business_type)
            ->where('refill_business.date','<=',$date)->sum('amount');
            
        $sms_cost = SmsLog::where('business_id',$business_id)->where('business_type',$business_type)->whereDate('created_at','<=',$date)->sum('total_cost');
            
        return ($refill - $interest-$sms_cost);
    }
    
    public function __notifyLowSMSBalance($business_id, $business_type = 'business',$sender_name = null){
        $date = date('Y-m-d');
        $sms_bal = $this->__getSMSBalance($date, $business_id, $business_type);
        
        if($business_type == 'business'){
            $business = Business::findOrFail($business_id);
            $bname = $business->name;
            $sms_settings = $business->sms_settings;
            $phones = [];
            if(!empty($business->sms_settings)){
                $phones = str_replace(' ','',$business->sms_settings['msg_phone_nos']);
            }
        }else{
            $business = SmsApiClient::findOrFail($business_id);
            $bname = $business->name;
            $phones = $business->contact_mobile;
            
            $sms_settings = array(
                                    'default_gateway' => $business->default_gateway,
                                    'ultimate_sender_id' => $sender_name,
                                    'ultimate_token' => $business->ultimate_token,
                                    'hutch_username' => $business->hutch_username,
                                    'hutch_password' => $business->hutch_password,
                                    'hutch_mask' => $sender_name,
                            );
        }
        
        $sms_reminder = SmsReminderSetting::first();
        
        if(!empty($sms_reminder) && !empty($phones) && !empty($bname)){
            $_amts = array();
            if(isset($sms_reminder->days_1) && !empty($sms_reminder->days_1_status)){
                $_amts[] = $sms_reminder->days_1;
            }
            
            if(isset($sms_reminder->days_2) && !empty($sms_reminder->days_2_status)){
                $_amts[] = $sms_reminder->days_2;
            }
            
            if(isset($sms_reminder->days_3) && !empty($sms_reminder->days_3_status)){
                $_amts[] = $sms_reminder->days_3;
            }
            
            if(isset($sms_reminder->days_4) && !empty($sms_reminder->days_4_status)){
                $_amts[] = $sms_reminder->days_4;
            }
            
            $should_notify = false;
            foreach ($_amts as $amt) {
                if ($sms_bal <= $amt && ($amt <= $business->last_sms_notification || empty($business->last_sms_notification))) {
                    $should_notify = true;
                    break;
                }
            }
            
            if ($should_notify) {
                $msg = $sms_reminder->sms_body;
                $msg = str_replace('{business_client_name}',$bname,$msg);
                $msg = str_replace('{sms_balance}',$sms_bal,$msg);
                
                $data = [
                    'sms_settings' => $sms_settings,
                    'mobile_number' => $phones,
                    'sms_body' => $msg
                ];
                
                
                $this->superadminTransactionalSms($data);
                $business->last_sms_notification = ceil($sms_bal);
                $business->save();
            }
        }
        
        
        
    }
    
    public function __getSMSBFBalance($date, $business_type = 'business'){
        $business_id = request()->session()->get('business.id');
        
        $interest = SmsListInterest::where('sms_list_interests.business_id', $business_id)->where('type',$business_type)
            ->whereDate('date','<',$date)->sum('amount');
            
        $refill = RefillBusiness::leftjoin('sms_refill_packages','sms_refill_packages.id','refill_business.package_id')
            ->where('refill_business.business_id', $business_id)->where('refill_business.type',$business_type)
            ->where('refill_business.date','<',$date)->sum('amount');
        
        $sms_cost = SmsLog::where('business_id',$business_id)->where('business_type',$business_type)->whereDate('created_at','<',$date)->sum('total_cost');
            
        return ($refill - $interest - $sms_cost); 
    } 
    
    public function __getSMSLedger($start_date, $end_date, $business_type = 'business'){
        $business_id = request()->session()->get('business.id');
        
        $sms_lists = SmsListInterest::where('sms_list_interests.business_id', $business_id)->where('sms_list_interests.type',$business_type)
            ->whereDate('date','>=',$start_date)->whereDate('date','<=',$end_date)
            ->select([
                'sms_list_interests.id as id',
                'sms_list_interests.date as date',
                'sms_list_interests.amount as amount',
                DB::raw('"interest" as type'),
            ]);
            
        $sms_refill = RefillBusiness::leftjoin('sms_refill_packages','sms_refill_packages.id','refill_business.package_id')
                ->where('refill_business.business_id',$business_id)->where('refill_business.type',$business_type)
                ->whereDate('refill_business.date','>=',$start_date)->whereDate('refill_business.date','<=',$end_date)
                ->select([
                    'refill_business.id as id',
                    'refill_business.date as date',
                    'sms_refill_packages.amount as amount',
                    DB::raw('"refill" as type'), 
                ]);
        $sms_cost = SmsLog::where('business_id',$business_id)->where('business_type',$business_type)
                    ->whereDate('created_at','>=',$start_date)->whereDate('created_at','<=',$end_date)
                    ->select([
                        'id as id',
                        'created_at as date',
                        'total_cost as amount',
                        DB::raw('"sms_sent" as type'),
                    ]);
            
        
                
        $query = $sms_lists->unionAll($sms_refill)->unionAll($sms_cost)->orderBy('date','ASC')->get();
        
        return $query;
        
    }
    
     
    public function __getPartnerBfBalance($id,$start_date){
        $commissions = ShippingPartnerCommission::where('partner_id', $id)
                        ->whereDate('transaction_date', '<', $start_date)
                        ->sum('amount');
        
        $payments = Transaction::where('parent_transaction_id', $id)
            ->whereIn('transactions.type',['partner_payment','shipping_partner_ob'])
            ->whereDate('transaction_date', '<', $start_date)
            ->sum('final_total');

        return $commissions - $payments;
    }
    
    public function __getPartnerBalance($id){
        $commissions = ShippingPartnerCommission::where('partner_id', $id)
            ->sum('amount');
        
        $payments = Transaction::where('parent_transaction_id', $id)
            ->whereIn('transactions.type',['partner_payment','shipping_partner_ob'])
            ->sum('final_total');

        return $commissions - $payments;
    }
    
    public function __getPartnerLedger($id,$start_date,$end_date){
        
        $commissions = ShippingPartnerCommission::leftjoin('shipments','shipping_partner_commission.shipment_id','shipments.id')
                                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                                ->leftjoin('shipping_agents','shipping_agents.id','shipping_partner_commission.partner_id')
                                ->leftjoin('users','users.id','shipping_partner_commission.created_by')
                                ->leftjoin('shipping_mode','shipping_mode.id','shipments.shipping_mode')
                                ->leftjoin('shipping_packages','shipping_packages.id','shipments.package_type_id')
                                ->leftjoin('shipping_partners','shipping_partners.id','shipments.shipping_partner')
                                ->where('shipping_partner_commission.partner_id', $id)
                                ->select([
                                    'shipping_partner_commission.transaction_date as date',
                                    
                                    'shipments.tracking_no',
                                    'shipping_packages.package_name',
                                    'shipping_partners.name as partner_name',
                                    'contacts.name as customer_name',
                                    
                                     DB::raw('"commission" as type'),
                                    'shipping_partner_commission.amount as amount',
                                    
                                    DB::raw('"" as method'),
                                    DB::raw('"" as cheque_number'),
                                    DB::raw('"" as cheque_date'),
                                    DB::raw('"" as payment_ref_no'),
                                    'shipments.id as parent_id',
                                    
                                ]);
        
        $payments = Transaction::leftjoin('shipping_partners','shipping_partners.id','transactions.parent_transaction_id')
                                ->leftjoin('transaction_payments','transaction_payments.transaction_id','transactions.id')
                                ->where('shipping_partners.id', $id)
                                ->whereIn('transactions.type',['partner_payment','shipping_partner_ob'])
                                ->select([
                                    'transactions.transaction_date as date',
                                    
                                    DB::raw('"" as tracking_no'),
                                    DB::raw('"" as package_name'),
                                    DB::raw('"" as partner_name'),
                                    DB::raw('"" as customer_name'),
                                    
                                    DB::raw('transactions.type as type'),
                                    'transactions.final_total as amount', 
                                    
                                    'transaction_payments.method',
                                   'transaction_payments.cheque_number',
                                    'transaction_payments.cheque_date',
                                    'transaction_payments.payment_ref_no',
                                    'transactions.parent_transaction_id as parent_id',
                                ]);
                                
                                
        if (!empty($start_date)  && !empty($end_date)) {
            $payments->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date);
            
            $payments->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        
        $ledger_transactions = $commissions->union($payments)->orderBy('date', 'asc')->get();
        
        return $ledger_transactions;
    }
    
    public function __getAgentBalance($id,$module = null){
        
        $commissions = ShippingAgentCommission::where('agent_id', $id)
            ->sum('amount');
        if($module == 'airline')
        {
           // $commissions = 0;
           return  $payments =  Transaction::where('parent_transaction_id', $id)
            ->whereIn('type', ['airline_ticket','airline_agent_ob']) 
            ->sum('final_total');
            

        }else{
            $payments = Transaction::where('parent_transaction_id', $id)
            ->whereIn('type', ['agent_payment','shipping_agent_ob']) 
            ->sum('final_total');
        }    
        return $commissions - $payments;
    
    }
    
    public function __getAgentBFBalance($id, $start_date , $module = null){
        if($module == 'airline')
        {
            $payments = Transaction::where('parent_transaction_id', $id)
            ->whereIn('type', ['airline_ticket']) 
            ->whereDate('transaction_date', '<', $start_date)
            ->sum('final_total');
            return $payments;
        }
        $commissions = ShippingAgentCommission::where('agent_id', $id)
            ->whereDate('transaction_date', '<', $start_date)
            ->sum('amount');
        
        $payments = Transaction::where('parent_transaction_id', $id)
            ->whereIn('type', ['agent_payment','shipping_agent_ob']) 
            ->whereDate('transaction_date', '<', $start_date)
            ->sum('final_total');

        return $commissions - $payments;
    }
    
    public function __getAgentLedger($id, $start_date, $end_date,$module = null){
        
        //module airline commission not working

        if($module == 'airline')
        {
            $payments = Transaction::leftjoin('transaction_payments','transaction_payments.transaction_id','transactions.id')
            ->where('transactions.parent_transaction_id', $id)
            ->whereIn('transactions.type',['airline_ticket'])
            ->select([
                'transactions.transaction_date as date',
                
                DB::raw('"" as tracking_no'),
                DB::raw('"" as package_name'),
                DB::raw('"" as partner_name'),
                DB::raw('"" as customer_name'),
                
                DB::raw('transactions.type as type'),
                'transactions.final_total as amount',
                
                'transaction_payments.method',
               'transaction_payments.cheque_number',
                'transaction_payments.cheque_date',
                'transaction_payments.payment_ref_no',
                'transactions.parent_transaction_id as parent_id',
            ]);
            
            
            if (!empty($start_date)  && !empty($end_date)) {
            $payments->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date);
            }

            $ledger_transactions = $payments->orderBy('date', 'asc')->get();
            return $ledger_transactions;
        }
        $commissions = ShippingAgentCommission::leftjoin('shipments','shipping_agent_commission.shipment_id','shipments.id')
                                ->leftjoin('contacts', 'shipments.customer_id', 'contacts.id')
                                ->leftjoin('shipping_agents','shipping_agents.id','shipping_agent_commission.agent_id')
                                ->leftjoin('users','users.id','shipping_agent_commission.created_by')
                                ->leftjoin('shipping_mode','shipping_mode.id','shipments.shipping_mode')
                                ->leftjoin('shipping_packages','shipping_packages.id','shipments.package_type_id')
                                ->leftjoin('shipping_partners','shipping_partners.id','shipments.shipping_partner')
                                ->where('shipping_agent_commission.agent_id', $id)
                                ->select([
                                    'shipping_agent_commission.transaction_date as date',
                                    
                                    'shipments.tracking_no',
                                    'shipping_packages.package_name',
                                    'shipping_partners.name as partner_name',
                                    'contacts.name as customer_name',
                                    
                                     DB::raw('"commission" as type'),
                                    'shipping_agent_commission.amount as amount',
                                    
                                    DB::raw('"" as method'),
                                    DB::raw('"" as cheque_number'),
                                    DB::raw('"" as cheque_date'),
                                    DB::raw('"" as payment_ref_no'),
                                    'shipments.id as parent_id',
                                    
                                ]);
                                 
        $payments = Transaction::leftjoin('shipping_agents','shipping_agents.id','transactions.parent_transaction_id')
                                ->leftjoin('transaction_payments','transaction_payments.transaction_id','transactions.id')
                                ->where('shipping_agents.id', $id)
                                ->whereIn('transactions.type',['agent_payment','shipping_agent_ob'])
                                ->select([
                                    'transactions.transaction_date as date',
                                    
                                    DB::raw('"" as tracking_no'),
                                    DB::raw('"" as package_name'),
                                    DB::raw('"" as partner_name'),
                                    DB::raw('"" as customer_name'),
                                    
                                    DB::raw('transactions.type as type'),
                                    'transactions.final_total as amount',
                                    
                                    'transaction_payments.method',
                                   'transaction_payments.cheque_number',
                                    'transaction_payments.cheque_date',
                                    'transaction_payments.payment_ref_no',
                                    'transactions.parent_transaction_id as parent_id',
                                ]);
                                
                                
        if (!empty($start_date)  && !empty($end_date)) {
            $commissions ->whereDate('shipping_agent_commission.transaction_date', '>=', $start_date)->whereDate('shipping_agent_commission.transaction_date', '<=', $end_date);
            $payments->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        
        $ledger_transactions = $commissions->union($payments)->orderBy('date', 'asc')->get();
        
        return $ledger_transactions;
        
    }
    
    
    public function __totalPurchaseAndTransferIn($business_id,$start,$end,$tank_id){
        $transfer_in = TankTransfer::join('fuel_tanks', function ($join) {
                    $join->on('tank_transfers.to_tank', 'fuel_tanks.id');
                })
                ->where('tank_transfers.business_id', $business_id)
                ->where('fuel_tanks.id',$tank_id)
                ->whereDate('tank_transfers.date', '>=', $start)
                ->whereDate('tank_transfers.date', '<=', $end)
                ->sum('tank_transfers.quantity');
        
        $purchase = $query = Transaction::leftjoin('tank_purchase_lines', function ($join) {
                    $join->on('transactions.id', 'tank_purchase_lines.transaction_id')->where('tank_purchase_lines.quantity', '!=', 0);
                })
                ->join('fuel_tanks', function ($join) {
                    $join->on('tank_purchase_lines.tank_id', 'fuel_tanks.id');
                })
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', '!=', 'opening_stock')
                ->whereDate('transactions.transaction_date', '>=', $start)
                ->whereDate('transactions.transaction_date', '<=', $end)
                ->where('fuel_tanks.id',$tank_id)->sum('tank_purchase_lines.quantity');
                
               
                
        $total = $transfer_in + $purchase;   
                
        return $total;
    }
    
    public function __totalSellAndTransferOut($business_id,$start,$end,$tank_id){
        $sell =  Transaction::leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')
                ->join('fuel_tanks', function ($join) {
                    $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');
                })
                ->where('transactions.business_id', $business_id)
                ->whereDate('transactions.transaction_date', '>=', $start)
                ->whereDate('transactions.transaction_date', '<=', $end)
                ->where('fuel_tanks.id',$tank_id)->sum('tank_sell_lines.quantity');
        
        $transfer_out = TankTransfer::join('fuel_tanks', function ($join) {
                    $join->on('tank_transfers.from_tank', 'fuel_tanks.id');
                })
                ->where('tank_transfers.business_id', $business_id)
                ->where('fuel_tanks.id',$tank_id)
                ->whereDate('tank_transfers.date', '>=', $start)
                ->whereDate('tank_transfers.date', '<=', $end)
                ->sum('tank_transfers.quantity');
        
        $total = $sell + $transfer_out;
        
        return $total;
                
    }

    public function __totalTesting($business_id,$start,$end,$tank_id){
        $testing_qty =  Transaction::leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')
        ->join('settlements', 'transactions.invoice_no', 'settlements.settlement_no')
        ->join('meter_sales', 'settlements.id', 'meter_sales.settlement_no')
        ->join('fuel_tanks', function ($join) {
            $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');
        })
        ->where('transactions.business_id', $business_id)
        ->whereDate('transactions.transaction_date', '>=', $start)
        ->whereDate('transactions.transaction_date', '<=', $end)
        ->where('fuel_tanks.id',$tank_id)->sum('meter_sales.testing_qty');

        return $testing_qty;
    }
    
    public function createOrUpdatePriceAdjustment($sale,$id = null){
        $final_total = $sale->price_adjustment;
        $total_before_tax = $sale->price_adjustment;
        
        
        if($final_total != 0){
            $ob_data = [
                'business_id' => $sale->business_id,
                'type' => 'vat_price_adjustment',
                'status' => 'final',
                'payment_status' => 'paid',
                'contact_id' => $sale->customer_id,
                'transaction_date' => !empty($sale->print_date) ? \Carbon::parse($sale->print_date)->format('Y-m-d') : \Carbon::parse($sale->date)->format('Y-m-d'),
                'total_before_tax' => $total_before_tax,
                'final_total' => $final_total,
                'tax_amount' => 0,
                'created_by' => request()->session()->get('user.id'),
                'invoice_no' => !empty($sale->statement_no) ? $sale->statement_no : $sale->customer_bill_no,
                
            ];
            
            $transaction = Transaction::where('invoice_no', $id)->where('type','vat_price_adjustment')->first();
                
            if(empty($transaction)){
                //Create transaction
                $transaction = Transaction::create($ob_data);
            }else{
                Transaction::where('invoice_no', $id)->where('type','vat_price_adjustment')->update($ob_data);
            }
            
            if(!empty($transaction)){
                // first delete previous account transactions
                AccountTransaction::where('transaction_id',$transaction->id)->forceDelete();
                $account_id = $this->account_exist_return_id('Accounts Receivable');
                    
                if($sale->price_adjustment > 0){
                    $type = 'debit';
                }elseif($sale->price_adjustment < 0){
                    $type = 'credit';
                }
                
                $account_transaction_data = [
                    'amount' => abs($transaction->final_total),
                    'account_id' => $account_id,
                    'contact_id' => $transaction->contact_id,
                    'type' => $type,
                    'operation_date' => $transaction->transaction_date,
                    'created_by' => $transaction->created_by,
                    'transaction_id' => $transaction->id
                ];
                
            
                AccountTransaction::createAccountTransaction($account_transaction_data);
            }
            
           
            
            
            return $transaction;
        }
        
        return false;
            
    }
    
    
    public function createSellTransaction($business_id, $input, $invoice_total, $user_id, $uf_data = true)
{
    try {
        $invoice_scheme_id = !empty($input['invoice_scheme_id']) ? $input['invoice_scheme_id'] : null;
        $invoice_no = !empty($input['invoice_no']) ? $input['invoice_no'] : $this->getInvoiceNumber($business_id, $input['status'], $input['location_id'], $invoice_scheme_id);

        $duplicate_invoice_count = Transaction::where('is_duplicate', 1)
            ->where('business_id', $business_id)
            ->count();

        $business = Business::where('id', $business_id)->first();
        $pos_settings = json_decode($business->pos_settings);

        if ($input['is_duplicate']) {
            $d_prefix = '';
            if (!empty($pos_settings->enable_prefix_duplicate_invoice)) {
                $d_prefix = $pos_settings->duplicate_invoice_prefix;
            }
            if ($duplicate_invoice_count <= 0) {
                $invoice_no = $d_prefix . '1';
            } else {
                $duplicate_invoice_count++;
                $invoice_no = $d_prefix . $duplicate_invoice_count;
            }
        }

        $final_total = $uf_data ? $this->num_uf($input['final_total']) : $input['final_total'];
        $ref_no = $input['is_quotation'] ? $this->getLastQuotationRefNo() : '';

        $transaction = Transaction::create([
            'business_id' => $business_id,
            'location_id' => $input['location_id'],
            'is_duplicate' => $input['is_duplicate'],
            'type' => $input['type'],
            'status' => $input['status'],
            'contact_id' => $input['contact_id'],
            'customer_group_id' => $input['customer_group_id'],
            'invoice_no' => $invoice_no,
            'ref_no' => $ref_no,
            'total_before_tax' => $invoice_total['total_before_tax'],
            'transaction_date' => $input['transaction_date'],
            'tax_id' => !empty($input['tax_rate_id']) ? $input['tax_rate_id'] : null,
            'order_tax_id' => !empty($input['order_tax_modal']) ? $input['order_tax_modal'] : null,
            'discount_type' => !empty($input['discount_type']) ? $input['discount_type'] : null,
            'discount_amount' => $uf_data ? $this->num_uf($input['discount_amount']) : $input['discount_amount'],
            'tax_amount' => $invoice_total['tax'],
            'final_total' => $final_total,
            'additional_notes' => !empty($input['sale_note']) ? $input['sale_note'] : null,
            'staff_note' => !empty($input['staff_note']) ? $input['staff_note'] : null,
            'created_by' => $user_id,
            'is_direct_sale' => !empty($input['is_direct_sale']) ? $input['is_direct_sale'] : 0,
            'commission_agent' => $input['commission_agent'],
            'is_quotation' => isset($input['is_quotation']) ? $input['is_quotation'] : 0,
            'is_customer_order' => isset($input['is_customer_order']) ? $input['is_customer_order'] : 0,
            'shipping_details' => isset($input['shipping_details']) ? $input['shipping_details'] : null,
            'shipping_address' => isset($input['shipping_address']) ? $input['shipping_address'] : null,
            'shipping_status' => isset($input['shipping_status']) ? $input['shipping_status'] : null,
            'delivered_to' => isset($input['delivered_to']) ? $input['delivered_to'] : null,
            'shipping_charges' => isset($input['shipping_charges']) ? ($uf_data ? $this->num_uf($input['shipping_charges']) : $input['shipping_charges']) : 0,
            'exchange_rate' => !empty($input['exchange_rate']) ? ($uf_data ? $this->num_uf($input['exchange_rate']) : $input['exchange_rate']) : 1,
            'selling_price_group_id' => isset($input['selling_price_group_id']) ? $input['selling_price_group_id'] : null,
            'pay_term_number' => isset($input['pay_term_number']) ? $input['pay_term_number'] : null,
            'pay_term_type' => isset($input['pay_term_type']) ? $input['pay_term_type'] : null,
            'is_suspend' => !empty($input['is_suspend']) ? 1 : 0,
            'is_recurring' => !empty($input['is_recurring']) ? $input['is_recurring'] : 0,
            'recur_interval' => !empty($input['recur_interval']) ? $input['recur_interval'] : null,
            'recur_interval_type' => !empty($input['recur_interval_type']) ? $input['recur_interval_type'] : null,
            'subscription_no' => !empty($input['subscription_no']) ? $input['subscription_no'] : null,
            'recur_repetitions' => !empty($input['recur_repetitions']) ? $input['recur_repetitions'] : 0,
            'order_addresses' => !empty($input['order_addresses']) ? $input['order_addresses'] : null,
            'sub_type' => !empty($input['sub_type']) ? $input['sub_type'] : null,
            'rp_earned' => $input['status'] == 'final' ? $this->calculateRewardPoints($business_id, $final_total) : 0,
            'rp_redeemed' => !empty($input['rp_redeemed']) ? $input['rp_redeemed'] : 0,
            'rp_redeemed_amount' => !empty($input['rp_redeemed_amount']) ? $input['rp_redeemed_amount'] : 0,
            'is_created_from_api' => !empty($input['is_created_from_api']) ? 1 : 0,
            'types_of_service_id' => !empty($input['types_of_service_id']) ? $input['types_of_service_id'] : null,
            'packing_charge' => !empty($input['packing_charge']) ? $input['packing_charge'] : 0,
            'packing_charge_type' => !empty($input['packing_charge_type']) ? $input['packing_charge_type'] : null,
            'service_custom_field_1' => !empty($input['service_custom_field_1']) ? $input['service_custom_field_1'] : null,
            'service_custom_field_2' => !empty($input['service_custom_field_2']) ? $input['service_custom_field_2'] : null,
            'service_custom_field_3' => !empty($input['service_custom_field_3']) ? $input['service_custom_field_3'] : null,
            'service_custom_field_4' => !empty($input['service_custom_field_4']) ? $input['service_custom_field_4'] : null,
            'order_status' => !empty($input['order_status']) ? $input['order_status'] : null,
            'order_no' => !empty($input['order_no']) ? $input['order_no'] : null,
            'order_date' => !empty($input['order_date']) ? $input['order_date'] : null,
            'customer_ref' => !empty($input['customer_ref']) ? $input['customer_ref'] : null,
            'repair_job_sheet_id' => !empty($input['job_sheet_id']) ? $input['job_sheet_id'] : null,
            'is_credit_sale' => !empty($input['is_credit_sale']) ? $input['is_credit_sale'] : 0,
            'need_to_reserve' => !empty($input['need_to_reserve']) ? $input['need_to_reserve'] : null,
            'is_over_limit_credit_sale' => !empty($input['is_over_limit_credit_sale']) ? $input['is_over_limit_credit_sale'] : 0,
            'approved_user' => !empty($input['approved_user']) ? $input['approved_user'] : null,
            'requested_by' => !empty($input['requested_by']) ? $input['requested_by'] : null,
            'over_limit_amount' => !empty($input['over_limit_amount']) ? $input['over_limit_amount'] : 0.00,
            'customer_limit' => !empty($input['customer_limit']) ? $input['customer_limit'] : 0.00,
            'price_later' => !empty($input['price_later']) ? $input['price_later'] : 0,
            'store_id' => $input['store_id']
        ]);

        \Log::info("Sell transaction created successfully.", ['transaction_id' => $transaction->id]);

        return $transaction;
    } catch (\Exception $e) {
        \Log::error("Error creating sell transaction: " . $e->getMessage(), [
            'input' => $input,
            'invoice_total' => $invoice_total,
            'user_id' => $user_id,
            'trace' => $e->getTraceAsString()
        ]);
        throw $e; // optionally rethrow to handle it elsewhere
    }
}

    /**
     * Add Sell transaction
     *
     * @param mixed $transaction_id
     * @param int $business_id
     * @param array $input
     * @param float $invoice_total
     * @param int $user_id
     *
     * @return Transaction
     */
     
     
    public function calculateAndUpdateVAT($transaction){
        switch($transaction->type){
            case 'expense':
                $this->__calculateExpenseVAT($transaction);
                break;
            case 'purchase':
                $this->__calculatePurchaseVAT($transaction);
                break;
            case 'sell':
                $this->__calculateSaleVAT($transaction);
                break;
            default:
        }
    }
    
    function __formatTime($start_time, $end_time) {
        $time_taken = $end_time - $start_time;
        
        if ($time_taken < 60) {
            return $time_taken . ' seconds';
        } elseif ($time_taken < 3600) {
            return ceil($time_taken / 60) . ' minutes';
        } else {
            return ceil($time_taken / 3600) . ' hours';
        }
    }
    
    public function __correctSecurityDeposits(){
        $transactions = Transaction::where('type','security_deposit')->with(['payment_lines','contact'])->get();
        $start_time = time();
        
        foreach($transactions as $one){
            
            $transaction = $one;
            $contact = $transaction->contact;
            $payment = $transaction->payment_lines[0];
            
            $account_transaction_data = [
                'amount' => abs($transaction->final_total),
                'business_id' => $transaction->business_id,
                'account_id' => $payment->account_id,
                'contact_id' => $contact->id,
                'operation_date' => $transaction->transaction_date,
                'created_by' => $transaction->created_by,
                'transaction_id' => $transaction->id,
                'transaction_payment_id' => $payment->id
            ];
            if ($contact->type == 'customer') {
                $account_transaction_data['type'] = 'debit';
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['type'] = 'credit';
            }
            
            $criteria = ['account_id' => $account_transaction_data['account_id'],'type' => $account_transaction_data['type'],'transaction_id' => $account_transaction_data['transaction_id'], 'transaction_payment_id' => $account_transaction_data['transaction_payment_id']];
            
            
            // dd($account_transaction_data);
            
            // add transaction
            if(!empty($account_transaction_data['account_id'])){
                if(!empty($account_transaction_data['account_id'])){
                    AccountTransaction::updateOrCreate($criteria,$account_transaction_data);
                }
            }
            
            
            if ($contact->type == 'customer') {
                $account_transaction_data['account_id'] = Account::where('business_id', $transaction->business_id)->where('name', 'Accounts Payable')->first()->id ?? 0;
                $account_transaction_data['type'] = 'credit';
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['account_id'] = Account::where('business_id', $transaction->business_id)->where('name', 'Company Deposits')->first()->id ?? 0;
                $account_transaction_data['type'] = 'debit';
            }
            
            $criteria = ['account_id' => $account_transaction_data['account_id'],'type' => $account_transaction_data['type'],'transaction_id' => $account_transaction_data['transaction_id'], 'transaction_payment_id' => $account_transaction_data['transaction_payment_id']];
            
            
            if(!empty($account_transaction_data['account_id'])){
                AccountTransaction::updateOrCreate($criteria,$account_transaction_data);
            }
        }
        $end_time = time();
        echo "Completed<br><b>Time taken: </b>".$this->__formatTime($start_time, $end_time);
    }
    public function __correctExpenses(){
        $transactions = Transaction::leftjoin('account_transactions','account_transactions.transaction_id','transactions.id')
                            ->where('transactions.type','expense')->whereNull('account_transactions.id')->with(['payment_lines'])->select('transactions.*')->get();
        $start_time = time();
        
        foreach($transactions as $one){
            
            $transaction = $one;
            $contact = $transaction->contact;
            $payment = empty($transaction->payment_lines->toArray()) ? [] : $one->payment_lines[0];
            
            if (!empty($transaction->expense_account)) {
                $account_transaction_data = [
    
                    'amount' => $transaction->final_total,
    
                    'account_id' => $transaction->expense_account,
    
                    'type' => 'debit',
    
                    'sub_type' => 'expense',
    
                    'operation_date' => $transaction->transaction_date,
    
                    'created_by' => $transaction->created_by,
    
                    'business_id' => $transaction->business_id,
                    
                    'transaction_id' => $transaction->id,
    
                    'transaction_payment_id' => !empty($payment) ? $payment->id : null,
                ];
                
                
                $criteria = ['account_id' => $account_transaction_data['account_id'],'type' => $account_transaction_data['type'],'transaction_id' => $account_transaction_data['transaction_id']];
                if(!empty($account_transaction_data['account_id'])){
                    AccountTransaction::updateOrCreate($criteria,$account_transaction_data);
                }
            }
            
            $account_payable_id = !empty($transaction->controller_account) ? $transaction->controller_account : Account::where('business_id', $transaction->business_id)->where('name', 'Accounts Payable')->first()->id;
            $ap_transaction_data = [

                'operation_date' => $transaction->transaction_date,

                'created_by' => $transaction->created_by,

                'transaction_id' => $transaction->id,

                'business_id' => $transaction->business_id,
                
                'transaction_payment_id' => !empty($payment) ? $payment->id : null,
                
                'operation_date' =>  $transaction->transaction_date

            ];
            
            
            if (empty($payment)) {

                if(!empty($account_payable_id)){
                    $ap_transaction_data['amount'] = $transaction->final_total;
    
                    $ap_transaction_data['account_id'] = $account_payable_id;
    
                    $ap_transaction_data['type'] =  'credit';
                    
                    AccountTransaction::createAccountTransaction($ap_transaction_data);
                }
                    

            }else if ($payment->amount < $transaction->final_total) {

                $ap_transaction_data['amount'] = $payment->amount;  //paid amount
                
                $ap_transaction_data['account_id'] = $payment->account_id;

                $ap_transaction_data['type'] =  'credit';
                
                AccountTransaction::createAccountTransaction($ap_transaction_data);
                
               if(!empty($account_payable_id)){
                    $ap_transaction_data['amount'] = $transaction->final_total - $payment->amount; //unpaid amount
    
                    $ap_transaction_data['account_id'] = $account_payable_id;
    
                    $ap_transaction_data['type'] = 'credit';
    
                    AccountTransaction::createAccountTransaction($ap_transaction_data);
               }

            }
            
            if ($payment->amount == $transaction->final_total) {

                $ap_transaction_data['amount'] = $payment->amount;  
                
                $ap_transaction_data['account_id'] = $payment->account_id;

                $ap_transaction_data['type'] = 'credit';

               
                AccountTransaction::createAccountTransaction($ap_transaction_data);

            }
        }
        $end_time = time();
        echo "Completed<br><b>Time taken: </b>".$this->__formatTime($start_time, $end_time);
    }
    
    public function __correctPurchasePayments(){
        $transactions = TransactionPayment::leftjoin('transactions','transactions.id','transaction_payments.transaction_id')
                                ->leftjoin('account_transactions','account_transactions.transaction_payment_id','transaction_payments.id')
                                ->where('transactions.type' , 'purchase')->whereNull('account_transactions.id')->where('transaction_payments.account_id','>',0)->with(['contact'])->select('transaction_payments.*')->get();
        // dd($transactions->toArray());
        $start_time = time();
        
        foreach($transactions as $one){
            
            $transaction = $one;
            $contact = $transaction->contact;
            
            $account_transaction_data = [
                'amount' => abs($transaction->amount),
                'account_id' => $transaction->account_id,
                'contact_id' => !empty($contact) ? $contact->id : null,
                'operation_date' => $transaction->paid_on,
                'created_by' => $transaction->created_by,
                'business_id' => $transaction->business_id,
                'transaction_id' => $transaction->transaction_id,
                'transaction_payment_id' => $transaction->id
            ];
            $account_transaction_data['type'] = 'credit';
            $criteria = ['account_id' => $account_transaction_data['account_id'],'type' => $account_transaction_data['type'], 'transaction_payment_id' => $account_transaction_data['transaction_payment_id']];
            
            
            // dd($account_transaction_data);
            
            // add transaction
            if(!empty($account_transaction_data['account_id'])){
                AccountTransaction::updateOrCreate($criteria,$account_transaction_data);
            }
            
            $one->is_deposited = 0;
            $one->save();
        }
        $end_time = time();
        echo "Completed<br><b>Time taken: </b>".$this->__formatTime($start_time, $end_time);
    }
    
    public function __correctCustomerPayments(){
        $transactions = TransactionPayment::where('paid_in_type' , 'customer_page')->whereNull('transaction_id')->where('account_id','>',0)->with(['contact'])->get();
        $start_time = time();
        
        foreach($transactions as $one){
            
            $transaction = $one;
            $contact = $transaction->contact;
            
            $account_transaction_data = [
                'amount' => abs($transaction->amount),
                'account_id' => $transaction->account_id,
                'contact_id' => !empty($contact) ? $contact->id : null,
                'operation_date' => $transaction->paid_on,
                'business_id' => $transaction->business_id,
                'created_by' => $transaction->created_by,
                'transaction_payment_id' => $transaction->id
            ];
            if ($contact->type == 'customer') {
                $account_transaction_data['type'] = 'debit';
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['type'] = 'credit';
            }
            
            $criteria = ['account_id' => $account_transaction_data['account_id'],'type' => $account_transaction_data['type'], 'transaction_payment_id' => $account_transaction_data['transaction_payment_id']];
            
            
            // dd($account_transaction_data);
            
            // add transaction
            if(!empty($account_transaction_data['account_id'])){
                AccountTransaction::updateOrCreate($criteria,$account_transaction_data);
            }
            
            if ($contact->type == 'customer') {
                $account_transaction_data['account_id'] = Account::where('business_id', $transaction->business_id)->where('name', 'Accounts Receivable')->first()->id ?? 0;
                $account_transaction_data['type'] = 'credit';
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['account_id'] = Account::where('business_id', $transaction->business_id)->where('name', 'Accounts Payable')->first()->id ?? 0;
                $account_transaction_data['type'] = 'debit';
            }
            
            $account_transaction_data['sub_type'] = 'ledger_show';
            
            $criteria = ['account_id' => $account_transaction_data['account_id'],'type' => $account_transaction_data['type'], 'transaction_payment_id' => $account_transaction_data['transaction_payment_id']];
            
            
            if(!empty($account_transaction_data['account_id'])){
                AccountTransaction::updateOrCreate($criteria,$account_transaction_data);
            }
            
            $one->is_deposited = 0;
            $one->save();
        }
        $end_time = time();
        echo "Completed<br><b>Time taken: </b>".$this->__formatTime($start_time, $end_time);
    }
    
    public function __correctSellPayments(){
        $transactions = TransactionPayment::leftjoin('transactions','transactions.id','transaction_payments.transaction_id')
                            ->leftjoin('account_transactions','account_transactions.transaction_payment_id','transaction_payments.id') 
                            ->where('transactions.type' , 'sell')->whereNull('account_transactions.id')->whereNull('transaction_payments.parent_id')->select('transaction_payments.*')->get();
        
        $start_time = time();
        
        foreach($transactions as $one){
            
            $transaction = $one;
            
            if($one->method == 'cash'){
                $account_id = Account::where('business_id', $transaction->business_id)->where('name', 'Cash')->first()->id ?? 0;
            }else{
                $account_id = $one->card_type;
            }
            
            $account_transaction_data = [
                'amount' => abs($transaction->amount),
                'account_id' => $account_id,
                'operation_date' => $transaction->paid_on,
                'business_id' => $transaction->business_id,
                'created_by' => $transaction->created_by,
                'transaction_payment_id' => $transaction->id
            ];
            $account_transaction_data['type'] = 'debit';
            
            $criteria = ['account_id' => $account_transaction_data['account_id'],'type' => $account_transaction_data['type'], 'transaction_payment_id' => $account_transaction_data['transaction_payment_id']];
            
            if(!empty($account_id)){
                if(!empty($account_transaction_data['account_id'])){
                    AccountTransaction::updateOrCreate($criteria,$account_transaction_data);
                }
            }
            
            
            $one->is_deposited = 0;
            $one->save();
        }
        $end_time = time();
        echo "Completed<br><b>Time taken: </b>".$this->__formatTime($start_time, $end_time);
    }
    
    public function __correctSettlement(){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', -1);
        
        $start_time = time();
        $settlements = Settlement::with([
                    'meter_sales',
                    'other_sales',
                    'other_incomes',
                    'customer_payments',
                    'cash_payments',
                    'cash_deposits',
                    'card_payments',
                    'cheque_payments',
                    'credit_sale_payments',
                    'expense_payments',
                    'excess_payments',
                    'shortage_payments',
                    'cash_deposits',
                    'loan_payments',
                    'drawings_payments',
                    'customer_loans'
                ])
                ->select('settlements.*')
                ->get();
                
        foreach($settlements as $settlement){
            $settlement_no = $settlement->settlement_no;
            
            foreach ($settlement->customer_loans as $customer_loan) {
                
                $customer_loan_transaction = Transaction::where('type','settlement')
                                                ->where('sub_type','customer_loan')
                                                ->where('invoice_no',$settlement->settlement_no)
                                                ->where('final_total',$customer_loan->amount)
                                                ->where('contact_id',$customer_loan->customer_id)->first();
                                                
                if(!empty($customer_loan_transaction)){
                    $type = 'debit';
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Accounts Receivable')->first()->id ?? 0;
                    
                    $this->createAccountTransaction($customer_loan_transaction, $type, $account_id, $customer_loan_transaction->id, 'null', null,  $customer_loan->amount,false,$customer_loan->note);
                    
                    $type = 'debit';
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Cash')->first()->id ?? 0;
                    $this->createAccountTransaction($customer_loan_transaction, $type, $account_id, $customer_loan_transaction->id, 'null', null,  $customer_loan->amount,false,$customer_loan->note);
                    
                    $type = 'credit';
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Cash')->first()->id ?? 0;
                    $this->createAccountTransaction($customer_loan_transaction, $type, $account_id, $customer_loan_transaction->id, 'null', null,  $customer_loan->amount,false,$customer_loan->note);
                }
                                                
                                                
                
                
            }
            
            $loan_note = "";
            foreach ($settlement->loan_payments as $loan_payment) {
                $i = 0;
                //this transaction will use in report to show amounts
                $loan_transaction_payment = Transaction::where('type','settlement')
                                                ->where('sub_type','loan_payment')
                                                ->where('invoice_no',$settlement->settlement_no)
                                                ->where('final_total',$loan_payment->amount)->first();
                                                
                if(!empty($loan_transaction_payment)){
                    $loan_note .=!empty($loan_payment->note) ? "Note ".$i++.": ".$loan_payment->note."\n" : "";
                    
                    $type = 'debit';
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Cash')->first()->id ?? 0;
                    $this->createAccountTransaction($loan_transaction_payment, $type, $account_id, $loan_transaction_payment->id, 'null', null,  $loan_payment->amount,false,$loan_payment->note);
                    
                    
                    $type = 'credit';
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Cash')->first()->id ?? 0;
                    $this->createAccountTransaction($loan_transaction_payment, $type, $account_id, $loan_transaction_payment->id, 'null', null,  $loan_payment->amount,false,$loan_payment->note);
                    
                    $type = 'debit';
                    $account_id = $loan_payment->loan_account;
                    $this->createAccountTransaction($loan_transaction_payment, $type, $account_id, $loan_transaction_payment->id, 'null', null,  $loan_payment->amount,false,$loan_payment->note);
                }
                
                    
            
            }
            
            $drawing_note = "";
            foreach ($settlement->drawings_payments as $drawing_payment) {
                $i = 0;
                //this transaction will use in report to show amounts
                $drawing_transaction_payment = Transaction::where('type','settlement')
                                                ->where('sub_type','drawing_payment')
                                                ->where('invoice_no',$settlement->settlement_no)
                                                ->where('final_total',$drawing_payment->amount)->first();
                
                
                $drawing_note .=!empty($drawing_payment->note) ? "Note ".$i++.": ".$drawing_payment->note."\n" : "";
                
                if(!empty($drawing_transaction_payment)){
                    $type = 'debit';
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Cash')->first()->id ?? 0;
                    $this->createAccountTransaction($drawing_transaction_payment, $type, $account_id, $drawing_transaction_payment->id, 'null', null,  $drawing_payment->amount,false,$drawing_payment->note);
                    
                    $type = 'credit';
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Cash')->first()->id ?? 0;
                    $this->createAccountTransaction($drawing_transaction_payment, $type, $account_id, $drawing_transaction_payment->id, 'null', null,  $drawing_payment->amount,false,$drawing_payment->note);
                    
                    
                    $type = 'debit';
                    $account_id = $drawing_payment->loan_account;
                    $this->createAccountTransaction($drawing_transaction_payment, $type, $account_id, $drawing_transaction_payment->id, 'null', null,  $drawing_payment->amount,false,$drawing_payment->note);
                }
                
                    
            
            }
            
            foreach ($settlement->cash_deposits as $cash_payment) {
                $i = 0;
                //this transaction will use in report to show amounts
                $cash_deposit = Transaction::where('type','settlement')
                                                ->where('sub_type','cash_deposit')
                                                ->where('invoice_no',$settlement->settlement_no)
                                                ->where('ref_no',$cash_payment->id)
                                                ->where('final_total',$cash_payment->amount)->first();
                                                
                if(!empty($cash_deposit)){
                    
                    $type = 'debit';
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Cash')->first()->id ?? 0;
                    $this->createAccountTransaction($cash_deposit, $type, $account_id, $cash_deposit->id, 'null', null,  $cash_payment->amount,false,null);
                    
                    $this->createAccountTransaction($cash_deposit, 'credit', $account_id, $cash_deposit->id, 'null', null,  $cash_payment->amount,false,null);
                    
                    
                    $type = 'debit';
                    $account_id = $cash_payment->bank_id;
                    $this->createAccountTransaction($cash_deposit, $type, $account_id, $cash_deposit->id, 'null', null,  $cash_payment->amount,false,null);
                    
                }
                
            }
            
            
            $cash_transaction_payment = null;
            
            foreach ($settlement->shortage_payments as $shortage_payment) {
                $transaction = Transaction::find($shortage_payment->transaction_id);
                
                if(!empty($transaction)){
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Accounts Receivable')->first()->id ?? 0;
                    $type = 'debit';
                    $this->createAccountTransaction($transaction, $type, $account_id, null, 'ledger_show',null,0,false,$shortage_payment->note);
                }
                
                
            }
            
            foreach ($settlement->excess_payments as $excess_payment) {
                $transaction = Transaction::find($excess_payment->transaction_id);
                
                if(!empty($transaction)){
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Accounts Receivable')->first()->id ?? 0;
                    $type = 'credit';
                    $this->createAccountTransaction($transaction, $type, $account_id, null, 'ledger_show',null,0,false,$excess_payment->note);
                }
                
                
            }
            

            foreach ($settlement->expense_payments as $expense_payment) {
                $transaction = Transaction::find($expense_payment->transaction_id);
                $transaction_payment = TransactionPayment::where('transaction_id',$transaction->id)->first();
                
                if(!empty($transaction) && !empty($transaction_payment)){
                    $account_id = $expense_payment->account_id;
                    $type = 'debit';
                    $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id);
                    
                    $account_id = Account::where('business_id', $settlement->business_id)->where('name', 'Cash')->first()->id ?? 0;
                    $type = 'credit';
                    $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id);
                }
                
                
            }

            
            
            
        }
                
        $end_time = time();
        echo "Completed<br><b>Time taken: </b>".$this->__formatTime($start_time, $end_time);
        
    }
    
    public function createAccountTransaction($transaction, $type, $account_id, $transaction_payment_id = null, $sub_type = null, $contact_id = null, $amount = 0, $is_credit_sale = false,$note = null,$slip_no = null)
    {
        $account_transaction_data = [
            'amount' => abs($transaction->final_total),
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
        
        
        
        if (!empty($contact_id)) {
            $account_transaction_data['contact_id'] = $contact_id;
        }
        if (!empty($amount)) {
            $account_transaction_data['amount'] = $amount;
        }

        $criteria = ['account_id' => $account_transaction_data['account_id'],'type' => $account_transaction_data['type'], 'transaction_id' => $account_transaction_data['transaction_id']];
        if(!empty($account_transaction_data['account_id'])){
                AccountTransaction::updateOrCreate($criteria,$account_transaction_data);
            }
        
    }
    
    
    public function corectStockAccounts(){
        
        // first select all sell transactions
        // then link the transaction sell lines
        // search by amount (previously exclusive of VAT)
        // if transaction found, update the amount with the VAT inclusive amount
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', -1);
        $sells = Transaction::where('type','sell')->where('transaction_updated',0)->orderBy('id','DESC')->limit(1000)->get();
        foreach($sells as $sale){
            
            $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
                ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
                ->where('transaction_id', $sale->id)
                ->select('transaction_sell_lines.*',  'products.stock_type','variations.dpp_inc_tax','variations.default_purchase_price','products.category_id', 'products.sub_category_id')
                ->get();
            foreach($sell_lines as $sl){
                
                if (!empty($sl->sub_category_id)) {
                    $account = Category::where('business_id', $sale->business_id)->where('id', $sl->sub_category_id)->select('cogs_account_id')->first()->cogs_account_id ?? null;
                    if (empty($account_id)) {
                        $account = Category::where('business_id', $sale->business_id)->where('id', $sl->category_id)->select('cogs_account_id')->first()->cogs_account_id ?? null;
                    }
                    
                } else {
                    $account = Category::where('business_id', $sale->business_id)->where('id', $sl->category_id)->select('cogs_account_id')->first()->cogs_account_id ?? null;
                }
                
                $account = Account::where(DB::raw("REPLACE(`name`, '  ', ' ')"), 'Cost of Goods Sold')->where('business_id', $sale->business_id)->first()->id ?? 0;
                
                
                $initial_amount = abs($sl->quantity * $sl->default_purchase_price);
                $new_amount = abs($sl->quantity * $sl->dpp_inc_tax);
                
                $cogs = AccountTransaction::where('account_id',$account)->where('type','debit')->where('transaction_id', $sale->id)->where('amount',$initial_amount)->first();
                $fga = AccountTransaction::where('account_id',$sl->stock_type)->where('type','credit')->where('transaction_id', $sale->id)->where('amount',$initial_amount)->first();
                
                
                if(!empty($cogs) && $new_amount > 0){
                    $cogs->amount = $new_amount;
                    $cogs->save;
                }
                
                if(!empty($fga) && $new_amount > 0){
                    $fga->amount = $new_amount;
                    $fga->save;
                }
                
                
            }
            
            $sale->transaction_updated = 1;
            $sale->save();
            
        }
        
        echo "success ".$sells->count();
        
        
        
    }
    
    public function transferPostDatedCheques(){
        ini_set('max_execution_time', 0);
        ini_set('memory_limit', -1);
        
        $postdated = AccountTransaction::join('accounts','accounts.id','account_transactions.account_id')
                                        ->join('transaction_payments','transaction_payments.id','account_transactions.transaction_payment_id')
                                        ->whereIn('accounts.name',['Post Dated Cheques','Issued Post Dated Cheques'])
                                        ->whereDate('transaction_payments.cheque_date',date('Y-m-d'))
                                        ->where('account_transactions.postdated_transafer_status',0)
                                        ->where('account_transactions.update_post_dated_cheque',1)
                                        ->select('account_transactions.*','transaction_payments.cheque_date','transaction_payments.cheque_number','transaction_payments.bank_name','transaction_payments.payment_for','transaction_payments.payment_ref_no','transaction_payments.related_account_id as rel_acc_id','transaction_payments.id as tp_id')
                                        ->get();
        foreach($postdated as $one){
            DB::beginTransaction();
        
            $transaction = Transaction::create([
                'business_id' => $one->business_id,
                'type' => 'postdated_transfer',
                'status' => 'final',
                'contact_id' => $one->payment_for,
                'ref_no' => $one->payment_ref_no,
                'total_before_tax' => $one->amount,
                'transaction_date' => $one->cheque_date,
                'final_total' => $one->amount,
                'created_by' => $one->created_by
            ]);
            
            if($one->type == 'credit'){
                $postdated_type = 'debit';
            }else{
                $postdated_type = 'credit';
            }
            
            $account_transaction_data = [
                'amount' =>  $one->amount,
                'account_id' => $one->account_id,
                'type' => $postdated_type,
                'sub_type' => null,
                'operation_date' => $one->cheque_date,
                'created_by' => $one->created_by,
                'transaction_id' =>  $transaction->id,
                'note' => null,
                'bank_name' => $one->bank_name,
                'cheque_date' => $one->cheque_date,
                'cheque_number' => $one->cheque_number,
                'transaction_payment_id' => $one->tp_id
            ];
            
            
            AccountTransaction::createAccountTransaction($account_transaction_data);
            
            if(!empty($one->credit_related_account)){
                $account_transaction_data['account_id'] = $one->credit_related_account;
                $account_transaction_data['transfer_account_id'] = $one->rel_acc_id;
                $account_transaction_data['sub_type'] = 'fund_transfer';
                
                $credit = AccountTransaction::createAccountTransaction($account_transaction_data);
                
                $account_transaction_data['transfer_account_id'] = $one->credit_related_account;
                $account_transaction_data['transfer_transaction_id'] = $credit->id;
            }
            
            $account_transaction_data['account_id'] = $one->rel_acc_id;
            $account_transaction_data['type'] = $one->type;
            
            $debit = AccountTransaction::createAccountTransaction($account_transaction_data);
            
            if(!empty($one->credit_related_account)){
                $credit->transfer_transaction_id = $debit->id;
                $credit->save();
            }
            
            $one->postdated_transafer_status = 1;
            $one->save();
            
            DB::commit();
        }
        
    }
    
    private function __calculateSaleVAT($transaction){
        if(!empty($transaction)){
            
            $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
                ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
                ->where('transaction_id', $transaction->id)
                ->select('transaction_sell_lines.*', 'products.category_id', 'products.sub_category_id','products.vat_claimed','variations.dpp_inc_tax','variations.default_sell_price')
                ->get();
                
            $tax_amt = 0;
            $vat_claimed = 0;
            $tax_id = 0;
            
            
            // delete any previous tax entries
            $tax_account_id = $this->account_exist_return_id('Taxes Payable');
            AccountTransaction::where('account_id',$tax_account_id)->where('transaction_id',$transaction->id)->forceDelete();
            
            foreach($sell_lines as $sell){
                $cat = Category::findOrFail($sell->category_id);
                
                $tax_rate = null;
                if(!empty($sell->tax_id)){
                    $tax_rate = TaxRate::find($sell->tax_id);
                }
                
                // if the tax id does not retun any results; then get the first tax in the list
                if(empty($tax_rate)){
                    $tax_rate = TaxRate::where('business_id',$transaction->business_id)->first();
                }
                
               
                
                if(!empty($tax_rate)){
                    $sub_cat_vat = null;
                    if(!empty($sell->sub_category_id)){
                        $sub_cat = Category::findOrFail($sell->sub_category_id);
                        $sub_cat_vat = $sub_cat->vat_based_on;
                    }
                    
                    if(!empty($sub_cat) && $sub_cat->vat_exempted == 'Yes'){
                        $multiplier=0;
                    }else{
                        
                        if(!empty($sub_cat_vat)){
                            if($sub_cat_vat == 'profit'){
                                $multiplier= $tax_rate->amount /100 * ($sell->unit_price_inc_tax - $sell->dpp_inc_tax);
                            }
                            
                            elseif($sub_cat_vat == 'sale_price'){
                                $multiplier= $sell->unit_price_inc_tax - $sell->default_sell_price;
                            }
                            
                            elseif($sub_cat_vat == 'profit_percentage'){
                                $multiplier=($sub_cat->profit_percentage /100 * $sell->unit_price_inc_tax) - ($sub_cat->profit_percentage /100 * $sell->default_sell_price);
                            }
                            
                            else{
                                $multiplier=0;
                            }
                            
                        }else{
                            if($sub_cat_vat == 'profit'){
                                $multiplier= $tax_rate->amount /100 * ($sell->unit_price_inc_tax - $sell->dpp_inc_tax);
                            }
                            
                            elseif($cat->vat_based_on == 'sale_price'){
                                $multiplier= $sell->unit_price_inc_tax - $sell->default_sell_price;
                            }
                            
                            elseif($cat->vat_based_on == 'profit_percentage'){
                                $multiplier=($cat->profit_percentage /100 * $sell->unit_price_inc_tax) - ($cat->profit_percentage /100 * $sell->default_sell_price);
                            }
                            
                            else{
                                $multiplier=0;
                            }
                        }
                        
                        
                    }
                    
                    $tax_amt += $sell->quantity * $multiplier;
                    if(!empty($sell->vat_claimed)){
                        $vat_claimed += $sell->quantity * $multiplier;
                    }
                    
                    $tax_id = $tax_rate->id;
                    
                }
                
                
            }
            
            
            if($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'vat_module')){
                // if product is set as vat claimed; and the VAT is existend; store in Account Transaction
                if(!empty($tax_account_id) && $vat_claimed > 0){
                    $account_transaction_data = [
                        'amount' =>  $vat_claimed,
                        'account_id' => $tax_account_id,
                        'type' => "debit",
                        'sub_type' => null,
                        'operation_date' => $transaction->transaction_date,
                        'created_by' => $transaction->created_by,
                        'transaction_id' =>  $transaction->id,
                        'sell_line_id' =>  $sell->id,
                        'note' => null
                    ];
                    // AccountTransaction::createAccountTransaction($account_transaction_data);
                }
                
                $rounded_tax_amount = $this->num_uf($this->num_f($tax_amt));
                $transaction->tax_id = $tax_id;
                $transaction->total_before_tax = $transaction->final_total - $rounded_tax_amount;
                $transaction->tax_amount = $rounded_tax_amount;
                $transaction->save(); 
            }else{
                $transaction->tax_id = null;
                $transaction->total_before_tax = $transaction->final_total;
                $transaction->tax_amount = 0;
                $transaction->save(); 
            }
                
            
        }
            
    }
    
    private function __calculatePurchaseVAT($transaction){
        if(!empty($transaction)){
            
            $sell_lines = PurchaseLine::leftjoin('products', 'purchase_lines.product_id', 'products.id')
                ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
                ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
                ->where('transaction_id', $transaction->id)
                ->select('purchase_lines.*', 'products.category_id', 'products.sub_category_id','products.vat_claimed')
                ->get();
                
            $tax_amt = 0;
            $vat_claimed = 0;
            $tax_id = 0;
            
            // delete any previous tax entries
            $tax_account_id = $this->account_exist_return_id('Taxes Receivable');
            AccountTransaction::where('account_id',$tax_account_id)->where('transaction_id',$transaction->id)->forceDelete();
            
            foreach($sell_lines as $sell){
                $cat = Category::findOrFail($sell->category_id);
                
                $tax_rate = null;
                if(!empty($sell->tax_id)){
                    $tax_rate = TaxRate::find($sell->tax_id);
                    // if the tax id does not retun any results; then get the first tax in the list
                    if(empty($tax_rate)){
                        $tax_rate = TaxRate::where('business_id',$transaction->business_id)->first();
                    }
                }
                
                if(!empty($tax_rate)){
                    $sub_cat_vat = null;
                    if(!empty($sell->sub_category_id)){
                        $sub_cat = Category::findOrFail($sell->sub_category_id);
                        $sub_cat_vat = $sub_cat->vat_based_on;
                    }
                    
                    if(!empty($sub_cat) && $sub_cat->vat_exempted == 'Yes'){
                        $multiplier=0;
                    }else{
                        if(!empty($sub_cat_vat)){
                            if($sub_cat_vat == 'sale_price'){
                                $multiplier= $sell->purchase_price_inc_tax - $sell->purchase_price;
                            }else{
                                $multiplier=0;
                            }
                            
                        }else{
                            if($cat->vat_based_on == 'sale_price'){
                                $multiplier= $sell->purchase_price_inc_tax - $sell->purchase_price;
                            }else{
                                $multiplier=0;
                            }
                        }
                    }
                    $tax_amt += $sell->quantity * $multiplier;
                    if(!empty($sell->vat_claimed)){
                        $vat_claimed += $sell->quantity * $multiplier;
                    }
                    
                    $tax_id = $tax_rate->id;
                    
                }
                
                
            }
            
            if($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'vat_module')){
                // if product is set as vat claimed; and the VAT is existend; store in Account Transaction
                if(!empty($tax_account_id) && $vat_claimed > 0){
                    $account_transaction_data = [
                        'amount' =>  $vat_claimed,
                        'account_id' => $tax_account_id,
                        'type' => "debit",
                        'sub_type' => null,
                        'operation_date' => $transaction->transaction_date,
                        'created_by' => $transaction->created_by,
                        'transaction_id' =>  $transaction->id,
                        'purchase_line_id' =>  $sell->id,
                        'note' => null
                    ];
                    // AccountTransaction::createAccountTransaction($account_transaction_data);
                }
                
                $rounded_tax_amount = $this->num_uf($this->num_f($tax_amt));
                $transaction->tax_id = $tax_id;
                $transaction->total_before_tax = $transaction->final_total - $rounded_tax_amount;
                $transaction->tax_amount = $rounded_tax_amount;
                $transaction->save(); 
            }else{
                $transaction->tax_id = null;
                $transaction->total_before_tax = $transaction->final_total;
                $transaction->tax_amount = 0;
                $transaction->save(); 
            }
                
            
        }
            
    }
    
    private function __calculateExpenseVAT($transaction){
        // conditions for expense VAT:
        // ii) VAT rate must be defined
        
        if(!empty($transaction)){
            // delete any previous tax entries
            $tax_account_id = $this->account_exist_return_id('Taxes Receivable');
            AccountTransaction::where('account_id',$tax_account_id)->where('transaction_id',$transaction->id)->forceDelete();
            
            $tax_rate = null;
            if(!empty($transaction->tax_id)){
                $tax_rate = TaxRate::find($transaction->tax_id);
                
                // if the tax id does not retun any results; then get the first tax in the list
                if(empty($tax_rate)){
                    $tax_rate = TaxRate::where('business_id',$transaction->business_id)->first();
                }
            }else{
                if(!empty($transaction->is_vat)){
                    $tax_rate = TaxRate::where('business_id',$transaction->business_id)->first();
                }
                
            }
            
            if(!empty($tax_rate)){
                $tax_amount = $transaction->final_total - ($transaction->final_total / (1 + ($tax_rate->amount / 100)));
                
                $rounded_tax_amount = $this->num_uf($this->num_f($tax_amount));
                $transaction->tax_id = $tax_rate->id;
                $transaction->total_before_tax = $transaction->final_total - $rounded_tax_amount;
                $transaction->tax_amount = $rounded_tax_amount;
                $transaction->save(); 
                
                $tax_account_id = $this->account_exist_return_id('Taxes Receivable');
                $expense_cat = ExpenseCategory::find($transaction->expense_category_id);
                
            
                if ($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'vat_module') && $transaction->tax_amount > 0) {
                    
                    // first delete previous tax account transactions
                    AccountTransaction::where('account_id',$tax_account_id)->where('transaction_id',$transaction->id)->forceDelete();
                            
                    if(!empty($tax_account_id) && !empty($expense_cat) && !empty($expense_cat->vat_claimed)){
                        
                        $account_transaction_data = [
                            'amount' =>  abs($transaction->tax_amount),
                            'account_id' => $tax_account_id,
                            'type' => "debit",
                            'sub_type' => null,
                            'operation_date' => $transaction->transaction_date,
                            'created_by' => $transaction->created_by,
                            'transaction_id' =>  $transaction->id,
                            'note' => null
                        ];
                        // AccountTransaction::createAccountTransaction($account_transaction_data);
                    }
                }else{
                    // vat module is disabled remove any VAT components saved
                    $transaction->tax_id = null;
                    $transaction->total_before_tax = $transaction->final_total;
                    $transaction->tax_amount = 0;
                    $transaction->save(); 
                    
                    // delete any tax account transactions
                    AccountTransaction::where('account_id',$tax_account_id)->where('transaction_id',$transaction->id)->forceDelete();
                }
                
            }
        }
            
    }
    
     public function getUserTotalSales($business_id, $user_id, $start_date, $end_date)
    {
        $totals = Transaction::where('business_id', $business_id)
                                ->where('commission_agent', $user_id)
                                ->where('type', 'sell')
                                ->where('status', 'final')
                                ->whereBetween(DB::raw('transaction_date'), [$start_date, $end_date])
                                ->select(
                                    DB::raw('SUM(final_total) as total_sales'),
                                    DB::raw('SUM(total_before_tax - shipping_charges - (SELECT SUM(item_tax*quantity) FROM transaction_sell_lines as tsl WHERE tsl.transaction_id=transactions.id) ) as total_sales_without_tax')
                                )
                                ->first();

        return [
            'total_sales' => $totals->total_sales ?? 0,
            'total_sales_without_tax' => $totals->total_sales_without_tax ?? 0,
        ];
    }
    
     private function __transactionQuery($contact_id, $start, $end = null, $location_id = null)
    {
        $business_id = request()->session()->get('user.business_id');
        $transaction_type_keys = array_keys(Transaction::transactionTypes());

        $query = Transaction::where('transactions.contact_id', $contact_id)
                        ->where('transactions.business_id', $business_id)
                        ->where('transactions.status', '!=', 'draft')
                        ->whereIn('transactions.type', $transaction_type_keys);

        if (!empty($start)  && !empty($end)) {
            $query->whereDate(
                'transactions.transaction_date',
                '>=',
                $start
            )
                ->whereDate('transactions.transaction_date', '<=', $end)->get();
        }

        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }

        if (!empty($start)  && empty($end)) {
            $query->whereDate('transactions.transaction_date', '<', $start);
        }

        return $query;
    }
    
    private function __paymentQuery($contact_id, $start, $end = null, $location_id = null)
    {
        $business_id = request()->session()->get('user.business_id');

        $query = TransactionPayment::leftJoin(
            'transactions as t',
            'transaction_payments.transaction_id',
            '=',
            't.id'
        )
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
            ->where('transaction_payments.payment_for', $contact_id);
            //->whereNotNull('transaction_payments.transaction_id');
            //->whereNull('transaction_payments.parent_id');

        if (!empty($start)  && !empty($end)) {
            $query->whereDate('paid_on', '>=', $start)
                        ->whereDate('paid_on', '<=', $end);
        }

        if (!empty($start)  && empty($end)) {
            $query->whereDate('paid_on', '<', $start);
        }

        if (!empty($location_id)) {
            //if location id present get all transaction with the location id and opening balance
             $query->where(function($q) use ($location_id) {
                 $q->where('transaction_payments.is_advance', 1)
                     ->orWhere('t.location_id', $location_id);
             });
        }


        return $query;
    }
    
    public function getLedgerDetails($contact_id, $start, $end, $format = 'format_1', $location_id = null, $line_details = false)
    {
        $business_id = request()->session()->get('user.business_id');
        //Get sum of totals before start date
        $previous_transaction_sums = $this->__transactionQuery($contact_id, $start, null, $location_id)
                ->select(
                    DB::raw("SUM(IF(type = 'purchase', final_total, 0)) as total_purchase"),
                    DB::raw("SUM(IF(type = 'sell' AND status = 'final', final_total, 0)) as total_invoice"),
                    DB::raw("SUM(IF(type = 'sell_return', final_total, 0)) as total_sell_return"),
                    DB::raw("SUM(IF(type = 'purchase_return', final_total, 0)) as total_purchase_return"),
                    DB::raw("SUM(IF(type = 'opening_balance', final_total, 0)) as total_opening_balance"),
                    DB::raw("SUM(IF(type = 'ledger_discount', final_total, 0)) as total_ledger_discount")
                )->first();

        //Get payment totals before start date
        $prev_payments = $this->__paymentQuery($contact_id, $start, null, $location_id)
                            ->select('transaction_payments.*', 'bl.name as location_name', 't.type as transaction_type', 'is_advance')
                                    ->get();

        $prev_total_invoice_paid = $prev_payments->where('transaction_type', 'sell')->where('is_return', 0)->sum('amount');
        $prev_total_ob_paid = $prev_payments->where('transaction_type', 'opening_balance')->where('is_return', 0)->sum('amount');
        $prev_total_sell_change_return = $prev_payments->where('transaction_type', 'sell')->where('is_return', 1)->sum('amount');
        $prev_total_sell_change_return = !empty($prev_total_sell_change_return) ? $prev_total_sell_change_return : 0;
        $prev_total_invoice_paid -= $prev_total_sell_change_return;
        $prev_total_purchase_paid = $prev_payments->where('transaction_type', 'purchase')->where('is_return', 0)->sum('amount');
        $prev_total_sell_return_paid = $prev_payments->where('transaction_type', 'sell_return')->sum('amount');
        $prev_total_purchase_return_paid = $prev_payments->where('transaction_type', 'purchase_return')->sum('amount');
        //$prev_total_advance_payment = $prev_payments->where('is_advance', 1)->sum('amount');
        $prev_total_advance_payment = $this->__paymentQuery($contact_id, $start, null, $location_id)
                                        ->select('bl.name as location_name', 
                                                't.type as transaction_type', 
                                                'is_advance',
                                                'transaction_payments.id',
                                                DB::raw('(transaction_payments.amount - COALESCE((SELECT SUM(amount) from transaction_payments as TP where TP.parent_id = transaction_payments.id), 0)) as amount')
                                        )
                                        ->where('is_advance', 1)
                                        ->get()
                                        ->sum('amount');

        $total_prev_paid = $prev_total_invoice_paid + $prev_total_purchase_paid - $prev_total_sell_return_paid - $prev_total_purchase_return_paid + $prev_total_ob_paid + $prev_total_advance_payment;

        $total_prev_invoice = $previous_transaction_sums->total_purchase + $previous_transaction_sums->total_invoice -  $previous_transaction_sums->total_sell_return -  $previous_transaction_sums->total_purchase_return + $previous_transaction_sums->total_opening_balance - $previous_transaction_sums->total_ledger_discount;
        //$total_prev_paid = $prev_payments_sum->total_paid;
        $beginning_balance = $total_prev_invoice - $total_prev_paid;

        $contact = Contact::find($contact_id);

        $with = ['location'];
        if ($line_details) {
            $with = ['location', 'sell_lines', 'sell_lines.sub_unit', 'sell_lines.product', 
                    'sell_lines.variations', 'sell_lines.product.unit', 'sell_lines.variations.product_variation',
                    'sell_lines.line_tax', 'purchase_lines', 'purchase_lines.product', 'purchase_lines.variations', 'purchase_lines.variations.product_variation', 'purchase_lines.line_tax'];
        }
        //Get transaction totals between dates
        $transaction_query = $this->__transactionQuery($contact_id, $start, $end, $location_id)
                            ->with(['location'])
                            ->select('transactions.*');

        if ($format == 'format_2') {
            $transaction_query->leftjoin('transaction_payments as tp', 'tp.transaction_id', '=', 'transactions.id')
                            ->addSelect(DB::raw('COALESCE(SUM(tp.amount), 0) as total_paid'))
                            ->groupBy('transactions.id');
        }

        $transactions = $transaction_query->get();
        $transaction_types = Transaction::transactionTypes();
        $ledger = [];

        $opening_balance = 0;
        $opening_balance_paid = 0;
        $ledger_discount = 0;
        
        foreach ($transactions as $transaction) {

            if($transaction->type == 'opening_balance'){
                //Skip opening balance, it will be added in the end
                $opening_balance += $transaction->final_total;

                continue;
            }

            if($transaction->type == 'ledger_discount'){
                $ledger_discount += $transaction->final_total;
            }

            $temp_array = [
                'date' => $transaction->transaction_date,
                'ref_no' => in_array($transaction->type, ['sell', 'sell_return']) ? $transaction->invoice_no : $transaction->ref_no,
                'type' => $transaction_types[$transaction->type],
                'location' => $transaction->location->name ?? '',
                'payment_status' => !in_array($transaction->type, ['ledger_discount']) ?  __('lang_v1.' . $transaction->payment_status) : '',
                'total' => '',
                'payment_method' => '',
                'debit' => in_array($transaction->type, ['sell', 'purchase_return']) || ($transaction->sub_type == 'purchase_discount') ? $transaction->final_total : '',
                'credit' => in_array($transaction->type, ['purchase', 'sell_return']) || ($transaction->sub_type == 'sell_discount') ? $transaction->final_total : '',
                'others' => $transaction->additional_notes,
                'transaction_id' => $transaction->id,
                'transaction_type' => $transaction->type
            ];

            if ($format == 'format_2') {
                $temp_array['final_total'] = $transaction->final_total;
                $temp_array['total_due'] = $transaction->final_total - $transaction->total_paid;
                $temp_array['due_date'] = $transaction->due_date;
                $temp_array['payment_status'] = $transaction->payment_status;
            }

            if ($format == 'format_3') {
                $temp_array['sell_lines'] = $transaction->sell_lines;
                $temp_array['purchase_lines'] = $transaction->purchase_lines;
            }

            $ledger[] = $temp_array;
        }

        $invoice_sum = $transactions->where('type', 'sell')->sum('final_total');
        $purchase_sum = $transactions->where('type', 'purchase')->sum('final_total');
        $sell_return_sum = $transactions->where('type', 'sell_return')->sum('final_total');
        $purchase_return_sum = $transactions->where('type', 'purchase_return')->sum('final_total');

        //Get payment totals between dates
        if ($format == 'format_1' || $format == 'format_3') {
            $payments = $this->__paymentQuery($contact_id, $start, $end, $location_id)
                            ->select('transaction_payments.*', 'bl.name as location_name', 't.type as transaction_type', 't.ref_no', 't.invoice_no')
                            ->get();
        } else {
            $payments = [];
        }

        $paymentTypes = $this->payment_types(null, true, $business_id);

        $total_reverse_payment = 0;

        foreach ($payments as $payment) {
            if($payment->transaction_type == 'opening_balance'){
                $opening_balance_paid += $payment->amount;
            }

            if ($contact->type == 'customer' && $payment->is_advance == 0 && empty($payment->transaction_id) && $payment->payment_type == 'debit') {
                $total_reverse_payment += $payment->amount; 
            }
            if ($contact->type == 'supplier' && $payment->is_advance == 0 && empty($payment->transaction_id) && $payment->payment_type == 'credit') {
                $total_reverse_payment += $payment->amount; 
            }

            //Hide all the adjusted payments because it has already been summed as advance payment
            if (!empty($payment->parent_id)) {
                continue;
            }

            $ref_no = in_array($payment->transaction_type, ['sell', 'sell_return']) ?  $payment->invoice_no :  $payment->ref_no;
            $note = $payment->note;
            if (!empty($ref_no)) {
                $note .='<small>' . __('account.payment_for') . ': ' . $ref_no . '</small>';
            }

            if ($payment->is_advance == 1) {
                $note .='<small>' . __('lang_v1.advance_payment') . '</small>';
            }

            if ($payment->is_return == 1) {
                $note .='<small>(' . __('lang_v1.change_return') . ')</small>';
            }

            $ledger[] = [
                'date' => $payment->paid_on,
                'ref_no' => $payment->payment_ref_no,
                'type' => $transaction_types['payment'],
                'location' => $payment->location_name,
                'payment_status' => '',
                'total' => '',
                'payment_method' => !empty($paymentTypes[$payment->method]) ? $paymentTypes[$payment->method] : '',
                'payment_method_key' => $payment->method,
                'debit' => in_array($payment->transaction_type, ['purchase', 'sell_return']) || ($payment->is_advance == 1 && $contact->type == 'supplier') || (in_array($payment->transaction_type, ['sell', 'purchase_return', 'opening_balance']) &&  $payment->is_return == 1) || $payment->payment_type == 'debit' ? $payment->amount : '',
                'credit' => (in_array($payment->transaction_type, ['sell', 'purchase_return', 'opening_balance']) || ($payment->is_advance == 1 && in_array($contact->type, ['customer', 'both']))) && $payment->is_return == 0 || $payment->payment_type == 'credit' ? $payment->amount : '',
                'others' =>  $note 
            ];
        }

        $total_excess_advance_payment = $this->__paymentQuery($contact_id, $start, $end, $location_id)
                                    ->select(
                                            DB::raw('(transaction_payments.amount - COALESCE((SELECT SUM(amount) from transaction_payments as TP where TP.parent_id = transaction_payments.id), 0)) as amount')
                                    )
                                    ->where('is_advance', 1)
                                    ->get()
                                    ->sum('amount');

        $total_invoice_paid = !empty($payments) ? $payments->where('transaction_type', 'sell')->where('is_return', 0)->sum('amount') : 0;
        $total_sell_change_return = !empty($payments) ? $payments->where('transaction_type', 'sell')->where('is_return', 1)->sum('amount') : 0;
        $total_sell_change_return = !empty($total_sell_change_return) ? $total_sell_change_return : 0;
        $total_invoice_paid -= $total_sell_change_return;
        $total_purchase_paid = !empty($payments) ? $payments->where('transaction_type', 'purchase')->where('is_return', 0)->sum('amount') : 0;
        $total_sell_return_paid = !empty($payments) ? $payments->where('transaction_type', 'sell_return')->sum('amount') : 0;
        $total_purchase_return_paid = !empty($payments) ? $payments->where('transaction_type', 'purchase_return')->sum('amount') : 0;

        $total_invoice_paid += $opening_balance_paid;

        $start_date = $this->format_date($start);
        $end_date = $this->format_date($end);

        $total_invoice = $invoice_sum - $sell_return_sum;
        $total_purchase = $purchase_sum - $purchase_return_sum;

        $opening_balance_due = $opening_balance;

        $total_paid = $total_invoice_paid + $total_purchase_paid - $total_sell_return_paid - $total_purchase_return_paid + $total_excess_advance_payment;
        
        $curr_due = $total_invoice + $total_purchase - $total_paid + $beginning_balance + $opening_balance_due;

        //Sort by date
        if (!empty($ledger)) {
            usort($ledger, function ($a, $b) {
                $t1 = strtotime($a['date']);
                $t2 = strtotime($b['date']);
                return $t1 - $t2;
            });
        }

        $total_opening_bal = $beginning_balance + $opening_balance_due;
        if ($format != 'format_2') {
            //Add Beginning balance & openining balance to ledger
            $ledger = array_merge([[
                'date' => $start,
                'ref_no' => '',
                'type' => __('lang_v1.opening_balance') ,
                'location' => '',
                'payment_status' => '',
                'total' => '',
                'payment_method' => '',
                'debit' => $contact->type == 'customer' ? abs($total_opening_bal) : '',
                'credit' => $contact->type == 'supplier' ? abs($total_opening_bal) : '',
                'others' => '',
                'final_total' => abs($total_opening_bal),
                'total_due' => 0,
                'due_date' => null
            ]], $ledger) ;
        }
        

        $bal = 0;
        foreach($ledger as $key => $val) {
            $credit = !empty($val['credit']) ? $val['credit'] : 0;
            $debit = !empty($val['debit']) ? $val['debit'] : 0;

            //NOTE:: Commented because of mismatch between final ledger table balance due and top balance due
            // if (!empty($val['payment_method_key']) && $val['payment_method_key'] == 'advance') {
            //     $credit = 0;
            //     $debit = 0;
            // }
            $bal += ($credit - $debit);
            $balance = $this->num_f(abs($bal));

            if ($bal < 0) {
                $balance .= ' ' . __('lang_v1.dr');
            } else if ($bal > 0) {
                $balance .= ' ' . __('lang_v1.cr');
            }

            $ledger[$key]['balance'] = $balance;
        }

        $output = [
            'ledger' => $ledger,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_invoice' => $total_invoice,
            'total_purchase' => $total_purchase,
            'beginning_balance' => $beginning_balance + $opening_balance_due,
            'balance_due' => $curr_due,
            'total_paid' => $total_paid,
            'total_reverse_payment' => $total_reverse_payment,
            'ledger_discount' => $ledger_discount
        ];

        return $output;
    }
     
   public function getListSells($business_id, $sale_type = 'sell')
    {
        $sells = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                // ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->leftJoin('transaction_sell_lines as tsl', function($join) {
                    $join->on('transactions.id', '=', 'tsl.transaction_id')
                        ->whereNull('tsl.parent_sell_line_id');
                })
                ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                ->leftJoin('users as ss', 'transactions.res_waiter_id', '=', 'ss.id')
                ->leftJoin('res_tables as tables', 'transactions.res_table_id', '=', 'tables.id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin(
                    'transactions AS SR',
                    'transactions.id',
                    '=',
                    'SR.return_parent_id'
                )
                ->leftJoin(
                    'types_of_services AS tos',
                    'transactions.types_of_service_id',
                    '=',
                    'tos.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', $sale_type)
                ->select(
                    'transactions.id',
                    'transactions.transaction_date',
                    'transactions.type',
                    'transactions.is_direct_sale',
                    'transactions.invoice_no',
                    'transactions.invoice_no as invoice_no_text',
                    'contacts.name',
                    'contacts.mobile',
                    'contacts.contact_id',
                    'contacts.supplier_business_name',
                    'transactions.status',
                    'transactions.payment_status',
                    'transactions.final_total',
                    'transactions.tax_amount',
                    'transactions.discount_amount',
                    'transactions.discount_type',
                    'transactions.total_before_tax',
                    'transactions.rp_redeemed',
                    'transactions.rp_redeemed_amount',
                    'transactions.rp_earned',
                    'transactions.types_of_service_id',
                    'transactions.shipping_status',
                    'transactions.pay_term_number',
                    'transactions.pay_term_type',
                    'transactions.additional_notes',
                    'transactions.staff_note',
                    'transactions.shipping_details',
                    'transactions.document',
                    
                    /*'transactions.shipping_custom_field_1',
                    'transactions.shipping_custom_field_2',
                    'transactions.shipping_custom_field_3',
                    'transactions.shipping_custom_field_4',
                    'transactions.shipping_custom_field_5',
                    'transactions.custom_field_1',
                    'transactions.custom_field_2',
                    'transactions.custom_field_3',
                    'transactions.custom_field_4',*/
                    
                    DB::raw('DATE_FORMAT(transactions.transaction_date, "%Y/%m/%d") as sale_date'),
                    DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by"),
                    DB::raw('(SELECT SUM(IF(TP.is_return = 1,-1*TP.amount,TP.amount)) FROM transaction_payments AS TP WHERE
                        TP.transaction_id=transactions.id) as total_paid'),
                    'bl.name as business_location',
                    DB::raw('COUNT(SR.id) as return_exists'),
                    DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=SR.id ) as return_paid'),
                    DB::raw('COALESCE(SR.final_total, 0) as amount_return'),
                    'SR.id as return_transaction_id',
                    'tos.name as types_of_service_name',
                    'transactions.service_custom_field_1',
                    DB::raw('COUNT( DISTINCT tsl.id) as total_items'),
                    DB::raw("CONCAT(COALESCE(ss.surname, ''),' ',COALESCE(ss.first_name, ''),' ',COALESCE(ss.last_name,'')) as waiter"),
                    'tables.name as table_name',
                    DB::raw('SUM(tsl.quantity - tsl.so_quantity_invoiced) as so_qty_remaining'),
                    'transactions.is_export'
                );

        if ($sale_type == 'sell') {
            $sells->where('transactions.status', 'final');
        }

        return $sells;
    }
    
    public function getListPurchases($business_id)
    {
        $purchases = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                    ->join(
                        'business_locations AS BS',
                        'transactions.location_id',
                        '=',
                        'BS.id'
                    )
                    ->leftJoin(
                        'transaction_payments AS TP',
                        'transactions.id',
                        '=',
                        'TP.transaction_id'
                    )
                    ->leftJoin(
                        'transactions AS PR',
                        'transactions.id',
                        '=',
                        'PR.return_parent_id'
                    )
                    ->leftJoin('users as u', 'transactions.created_by', '=', 'u.id')
                    ->where('transactions.business_id', $business_id)
                    ->where('transactions.type', 'purchase')
                    ->select(
                        'transactions.id',
                        'transactions.document',
                        'transactions.transaction_date',
                        'transactions.ref_no',
                        'contacts.name',
                        'contacts.supplier_business_name',
                        'transactions.status',
                        'transactions.payment_status',
                        'transactions.final_total',
                        'BS.name as location_name',
                        'transactions.pay_term_number',
                        'transactions.pay_term_type',
                        'PR.id as return_transaction_id',
                        DB::raw('SUM(TP.amount) as amount_paid'),
                        DB::raw('(SELECT SUM(TP2.amount) FROM transaction_payments AS TP2 WHERE
                        TP2.transaction_id=PR.id ) as return_paid'),
                        DB::raw('COUNT(PR.id) as return_exists'),
                        DB::raw('COALESCE(PR.final_total, 0) as amount_return'),
                        DB::raw("CONCAT(COALESCE(u.surname, ''),' ',COALESCE(u.first_name, ''),' ',COALESCE(u.last_name,'')) as added_by")
                    )
                    ->groupBy('transactions.id');

        return $purchases;
    }
    
    public function updateSellTransaction($transaction_id, $business_id, $input, $invoice_total, $user_id, $uf_data = true, $change_invoice_number = true)
    {
        $transaction = $transaction_id;
        if (!is_object($transaction)) {
            $transaction = Transaction::where('id', $transaction_id)
                ->where('business_id', $business_id)
                ->firstOrFail();
        }
        //Update invoice number if changed from draft to finalize or vice-versa
        $invoice_no = $transaction->invoice_no;
        if ($transaction->status != $input['status'] && $change_invoice_number) {
            // $invoice_scheme_id = !empty($input['invoice_scheme_id']) ? $input['invoice_scheme_id'] : null;
            // $invoice_no = $this->getInvoiceNumber($business_id, $input['status'], $transaction->location_id, $invoice_scheme_id);
            $sellController = app()->make(SellController::class);
            $request = new Request([
                'location_id' => 0,
                'type' => 'final'
            ]);
            $getInvoiveNo = $sellController->getInvoiveNo($request);
            $invoice_no = $getInvoiveNo['orignal_invoice_no'];
        }
        $final_total = $uf_data ? $this->num_uf($input['final_total']) : $input['final_total'];
        $update_date = [
            'status' => $input['status'],
            'invoice_no' => $invoice_no,
            'sale_ref' => $transaction->invoice_no,
            'contact_id' => $input['contact_id'],
            'customer_group_id' => $input['customer_group_id'],
            'total_before_tax' => $invoice_total['total_before_tax'],
            'tax_id' => $input['tax_rate_id'],
            'discount_type' => $input['discount_type'],
            'discount_amount' => $uf_data ? $this->num_uf($input['discount_amount']) : $input['discount_amount'],
            'tax_amount' => $invoice_total['tax'],
            'final_total' => $final_total,
            'additional_notes' => !empty($input['sale_note']) ? $input['sale_note'] : null,
            'staff_note' => !empty($input['staff_note']) ? $input['staff_note'] : null,
            'commission_agent' => $input['commission_agent'],
            'is_quotation' => isset($input['is_quotation']) ? $input['is_quotation'] : 0,
            'shipping_details' => isset($input['shipping_details']) ? $input['shipping_details'] : null,
            'shipping_charges' => isset($input['shipping_charges']) ? $uf_data ? $this->num_uf($input['shipping_charges']) : $input['shipping_charges'] : 0,
            'shipping_address' => isset($input['shipping_address']) ? $input['shipping_address'] : null,
            'shipping_status' => isset($input['shipping_status']) ? $input['shipping_status'] : null,
            'delivered_to' => isset($input['delivered_to']) ? $input['delivered_to'] : null,
            'exchange_rate' => !empty($input['exchange_rate']) ? $uf_data ? $this->num_uf($input['exchange_rate']) : $input['exchange_rate'] : 1,
            'selling_price_group_id' => isset($input['selling_price_group_id']) ? $input['selling_price_group_id'] : null,
            'pay_term_number' => isset($input['pay_term_number']) ? $input['pay_term_number'] : null,
            'pay_term_type' => isset($input['pay_term_type']) ? $input['pay_term_type'] : null,
            'is_suspend' => !empty($input['is_suspend']) ? 1 : 0,
            'is_recurring' => !empty($input['is_recurring']) ? $input['is_recurring'] : 0,
            'recur_interval' => !empty($input['recur_interval']) ? $input['recur_interval'] : null,
            'recur_interval_type' => !empty($input['recur_interval_type']) ? $input['recur_interval_type'] : null,
            'recur_repetitions' => !empty($input['recur_repetitions']) ? $input['recur_repetitions'] : 0,
            'order_addresses' => !empty($input['order_addresses']) ? $input['order_addresses'] : null,
            'rp_earned' => $input['status'] == 'final' ? $this->calculateRewardPoints($business_id, $final_total) : 0,
            'rp_redeemed' => !empty($input['rp_redeemed']) ? $input['rp_redeemed'] : 0,
            'repair_job_sheet_id' => !empty($input['job_sheet_id']) ? $input['job_sheet_id'] : ($transaction->repair_job_sheet_id ?? null),
            'rp_redeemed_amount' => !empty($input['rp_redeemed_amount']) ? $input['rp_redeemed_amount'] : 0,
            'types_of_service_id' => !empty($input['types_of_service_id']) ? $input['types_of_service_id'] : null,
            'packing_charge' => !empty($input['packing_charge']) ? $input['packing_charge'] : 0,
            'packing_charge_type' => !empty($input['packing_charge_type']) ? $input['packing_charge_type'] : null,
            'service_custom_field_1' => !empty($input['service_custom_field_1']) ? $input['service_custom_field_1'] : null,
            'service_custom_field_2' => !empty($input['service_custom_field_2']) ? $input['service_custom_field_2'] : null,
            'service_custom_field_3' => !empty($input['service_custom_field_3']) ? $input['service_custom_field_3'] : null,
            'service_custom_field_4' => !empty($input['service_custom_field_4']) ? $input['service_custom_field_4'] : null
        ];
        if (!empty($input['transaction_date'])) {
            $update_date['transaction_date'] = $input['transaction_date'];
        }
        $transaction->fill($update_date);
        $transaction->update();
        return $transaction;
    }
    /**
     * Add/Edit transaction sell lines
     *
     * @param object/int $transaction
     * @param array $products
     * @param array $location_id
     * @param boolean $return_deleted = false
     * @param array $extra_line_parameters = []
     *   Example: ['database_trasnaction_linekey' => 'products_line_key'];
     *
     * @return boolean/object
     */
    public function createOrUpdateSellLines($transaction, $products, $location_id, $return_deleted = false, $status_before = null, $extra_line_parameters = [], $uf_data = true)
    {
        $lines_formatted = [];
        $modifiers_array = [];
        $edit_ids = [0];
        $modifiers_formatted = [];
        $combo_lines = [];
        $products_modified_combo = [];
        foreach ($products as $product) {
            $multiplier = 1;
            if (isset($product['sub_unit_id']) && $product['sub_unit_id'] == $product['product_unit_id']) {
                unset($product['sub_unit_id']);
            }
            if (!empty($product['sub_unit_id']) && !empty($product['base_unit_multiplier'])) {
                $multiplier = $product['base_unit_multiplier'];
            }
            //Check if transaction_sell_lines_id is set, used when editing.
            if (!empty($product['transaction_sell_lines_id'])) {
                $edit_ids[] = $product['transaction_sell_lines_id'];
                $this->editSellLine($product, $location_id, $status_before, $multiplier);
                //update or create modifiers for existing sell lines
                if ($this->isModuleEnabled('modifiers')) {
                    if (!empty($product['modifier'])) {
                        foreach ($product['modifier'] as $key => $value) {
                            if (!empty($product['modifier_sell_line_id'][$key])) {
                                //Dont delete modifier sell line if exists
                                $edit_ids[] = $product['modifier_sell_line_id'][$key];
                            } else {
                                if (!empty($product['modifier_price'][$key])) {
                                    $this_price = $uf_data ? $this->num_uf($product['modifier_price'][$key]) : $product['modifier_price'][$key];
                                    $modifiers_formatted[] = new TransactionSellLine([
                                        'product_id' => $product['modifier_set_id'][$key],
                                        'variation_id' => $value,
                                        'quantity' => 1,
                                        'unit_price_before_discount' => $this_price,
                                        'unit_price' => $this_price,
                                        'unit_price_inc_tax' => $this_price,
                                        'parent_sell_line_id' => $product['transaction_sell_lines_id'],
                                        'children_type' => 'modifier'
                                    ]);
                                }
                            }
                        }
                    }
                }
            } else {
                $products_modified_combo[] = $product;
                //calculate unit price and unit price before discount
                $uf_unit_price = $uf_data ? $this->num_uf($product['unit_price']) : $product['unit_price'];
                $unit_price_before_discount = $uf_unit_price / $multiplier;
                $unit_price = $unit_price_before_discount;
                if (!empty($product['line_discount_type']) && $product['line_discount_amount']) {
                    $discount_amount = $uf_data ? $this->num_uf($product['line_discount_amount']) : $product['line_discount_amount'];
                    if ($product['line_discount_type'] == 'fixed') {
                        //Note: Consider multiplier for fixed discount amount
                        $unit_price = $unit_price_before_discount - $discount_amount;
                    } elseif ($product['line_discount_type'] == 'percentage') {
                        $unit_price = ((100 - $discount_amount) * $unit_price_before_discount) / 100;
                    }
                }
                $uf_quantity = $uf_data ? $this->num_uf($product['quantity']) : $product['quantity'];
                $uf_item_tax = $uf_data ? $this->num_uf($product['item_tax']) : $product['item_tax'];
                $uf_unit_price_inc_tax = $uf_data ? $this->num_uf($product['unit_price_inc_tax']) : $product['unit_price_inc_tax'];
                $line = [
                    'product_id' => $product['product_id'],
                    'variation_id' => $product['variation_id'],
                    'quantity' =>  $uf_quantity * $multiplier,
                    'unit_price_before_discount' => $unit_price_before_discount,
                    'unit_price' => $unit_price,
                    'line_discount_type' => !empty($product['line_discount_type']) ? $product['line_discount_type'] : null,
                    'line_discount_amount' => !empty($product['line_discount_amount']) ? $uf_data ? $this->num_uf($product['line_discount_amount']) : $product['line_discount_amount'] : 0,
                    'item_tax' =>  $uf_item_tax / $multiplier,
                    'tax_id' => $product['tax_id'],
                    'unit_price_inc_tax' =>  $uf_unit_price_inc_tax / $multiplier,
                    'sell_line_note' => !empty($product['sell_line_note']) ? $product['sell_line_note'] : '',
                    'sub_unit_id' => !empty($product['sub_unit_id']) ? $product['sub_unit_id'] : null,
                    'discount_id' => !empty($product['discount_id']) ? $product['discount_id'] : null,
                    'res_service_staff_id' => !empty($product['res_service_staff_id']) ? $product['res_service_staff_id'] : null,
                    'res_line_order_status' => !empty($product['res_service_staff_id']) ? 'received' : null,
                    'weight_loss' => !empty($product['weight_loss']) ? $this->num_uf($product['weight_loss']) : null,
                    'weight_excess' => !empty($product['weight_excess']) ? $this->num_uf($product['weight_excess']) : null,
                    'last_purchased_price' => !empty($product['last_purchased_price']) ? $this->num_uf($product['last_purchased_price']) : null
                ];
                foreach ($extra_line_parameters as $key => $value) {
                    $line[$key] = isset($product[$value]) ? $product[$value] : '';
                }
                if (!empty($product['lot_no_line_id'])) {
                    $line['lot_no_line_id'] = $product['lot_no_line_id'];
                }
                //Check if restaurant module is enabled then add more data related to that.
                if ($this->isModuleEnabled('modifiers')) {
                    $sell_line_modifiers = [];
                    if (!empty($product['modifier'])) {
                        foreach ($product['modifier'] as $key => $value) {
                            if (!empty($product['modifier_price'][$key])) {
                                $this_price = $uf_data ? $this->num_uf($product['modifier_price'][$key]) : $product['modifier_price'][$key];
                                $sell_line_modifiers[] = [
                                    'product_id' => $product['modifier_set_id'][$key],
                                    'variation_id' => $value,
                                    'quantity' => 1,
                                    'unit_price_before_discount' => $this_price,
                                    'unit_price' => $this_price,
                                    'unit_price_inc_tax' => $this_price,
                                    'children_type' => 'modifier'
                                ];
                            }
                        }
                    }
                    $modifiers_array[] = $sell_line_modifiers;
                }
                $lines_formatted[] = new TransactionSellLine($line);
                $sell_line_warranties[] = !empty($product['warranty_id']) ? $product['warranty_id'] : 0;
            }
        }
        if (!is_object($transaction)) {
            $transaction = Transaction::findOrFail($transaction);
        }
        //Delete the products removed and increment product stock.
        $deleted_lines = [];
        if (!empty($edit_ids)) {
            $deleted_lines = TransactionSellLine::where('transaction_id', $transaction->id)
                ->whereNotIn('id', $edit_ids)
                ->whereNull('parent_sell_line_id')
                ->select('id')->get()->toArray();
            $combo_delete_lines = TransactionSellLine::whereIn('parent_sell_line_id', $deleted_lines)->where('children_type', 'combo')->select('id')->get()->toArray();
            $deleted_lines = array_merge($deleted_lines, $combo_delete_lines);
            $adjust_qty = $status_before == 'draft' ? false : true;
            $this->deleteSellLines($deleted_lines, $location_id, $adjust_qty);
        }
        $combo_lines = [];
        if (!empty($lines_formatted)) {
            $transaction->sell_lines()->saveMany($lines_formatted);
            //Add corresponding modifier sell lines if exists
            if ($this->isModuleEnabled('modifiers')) {
                foreach ($lines_formatted as $key => $value) {
                    if (!empty($modifiers_array[$key])) {
                        foreach ($modifiers_array[$key] as $modifier) {
                            $modifier['parent_sell_line_id'] = $value->id;
                            $modifiers_formatted[] = new TransactionSellLine($modifier);
                        }
                    }
                }
            }
            //Combo product lines.
            //$products_value = array_values($products);
            foreach ($lines_formatted as $key => $value) {
                if (!empty($products_modified_combo[$key]['product_type']) && $products_modified_combo[$key]['product_type'] == 'combo') {
                    $combo_lines = array_merge($combo_lines, $this->__makeLinesForComboProduct($products_modified_combo[$key]['combo'], $value));
                }
                //Save sell line warranty if set
                if (!empty($sell_line_warranties[$key])) {
                    $value->warranties()->sync([$sell_line_warranties[$key]]);
                }
            }
        }
        if (!empty($combo_lines)) {
            $transaction->sell_lines()->saveMany($combo_lines);
        }
        if (!empty($modifiers_formatted)) {
            $transaction->sell_lines()->saveMany($modifiers_formatted);
        }
        if ($return_deleted) {
            return $deleted_lines;
        }
        return true;
    }
    /**
     * Returns the line for combo product
     *
     * @param array $combo_items
     * @param object $parent_sell_line
     *
     * @return array
     */
    private function __makeLinesForComboProduct($combo_items, $parent_sell_line)
    {
        $combo_lines = [];
        //Calculate the percentage change in price.
        $combo_total_price = 0;
        foreach ($combo_items as $key => $value) {
            $sell_price_inc_tax = Variation::findOrFail($value['variation_id'])->sell_price_inc_tax;
            $combo_items[$key]['unit_price_inc_tax'] = $sell_price_inc_tax;
            $combo_total_price += $value['quantity'] * $sell_price_inc_tax;
        }
        $change_percent = $this->get_percent($combo_total_price, $parent_sell_line->unit_price_inc_tax * $parent_sell_line->quantity);
        foreach ($combo_items as $value) {
            $price = $this->calc_percentage($value['unit_price_inc_tax'], $change_percent, $value['unit_price_inc_tax']);
            $combo_lines[] = new TransactionSellLine([
                'product_id' => $value['product_id'],
                'variation_id' => $value['variation_id'],
                'quantity' => $value['quantity'],
                'unit_price_before_discount' => $price,
                'unit_price' => $price,
                'line_discount_type' => null,
                'line_discount_amount' => 0,
                'item_tax' => 0,
                'tax_id' => null,
                'unit_price_inc_tax' => $price,
                'sub_unit_id' => null,
                'discount_id' => null,
                'parent_sell_line_id' => $parent_sell_line->id,
                'children_type' => 'combo'
            ]);
        }
        return $combo_lines;
    }
    /**
     * Edit transaction sell line
     *
     * @param array $product
     * @param int $location_id
     *
     * @return boolean
     */
    public function editSellLine($product, $location_id, $status_before, $multiplier = 1)
    {
        //Get the old order quantity
        $sell_line = TransactionSellLine::with(['product', 'warranties'])
            ->find($product['transaction_sell_lines_id']);
        //Adjust quanity
        if ($status_before != 'draft') {
            $new_qty = $this->num_uf($product['quantity']) * $multiplier;
            $difference = $sell_line->quantity - $new_qty;
            $this->adjustQuantity($location_id, $product['product_id'], $product['variation_id'], $difference);
        }
        $unit_price_before_discount = $this->num_uf($product['unit_price']) / $multiplier;
        $unit_price = $unit_price_before_discount;
        if (!empty($product['line_discount_type']) && $product['line_discount_amount']) {
            $discount_amount = $this->num_uf($product['line_discount_amount']);
            if ($product['line_discount_type'] == 'fixed') {
                $unit_price = $unit_price_before_discount - $discount_amount;
            } elseif ($product['line_discount_type'] == 'percentage') {
                $unit_price = ((100 - $discount_amount) * $unit_price_before_discount) / 100;
            }
        }
        //Update sell lines.
        $sell_line->fill([
            'product_id' => $product['product_id'],
            'variation_id' => $product['variation_id'],
            'quantity' => $this->num_uf($product['quantity']) * $multiplier,
            'unit_price_before_discount' => $unit_price_before_discount,
            'unit_price' => $unit_price,
            'line_discount_type' => !empty($product['line_discount_type']) ? $product['line_discount_type'] : null,
            'line_discount_amount' => !empty($product['line_discount_amount']) ? $this->num_uf($product['line_discount_amount']) : 0,
            'item_tax' => $this->num_uf($product['item_tax']) / $multiplier,
            'tax_id' => $product['tax_id'],
            'unit_price_inc_tax' => $this->num_uf($product['unit_price_inc_tax']) / $multiplier,
            'sell_line_note' => !empty($product['sell_line_note']) ? $product['sell_line_note'] : '',
            'sub_unit_id' => !empty($product['sub_unit_id']) ? $product['sub_unit_id'] : null,
            'res_service_staff_id' => !empty($product['res_service_staff_id']) ? $product['res_service_staff_id'] : null,
            'weight_loss' => !empty($product['weight_loss']) ? $this->num_uf($product['weight_loss']) : null,
            'weight_excess' => !empty($product['weight_excess']) ? $this->num_uf($product['weight_excess']) : null
        ]);
        $sell_line->save();
        //Set warranty
        if (!empty($product['warranty_id'])) {
            $warranty_ids = $sell_line->warranties->pluck('warranty_id')->toArray();
            if (!in_array($product['warranty_id'], $warranty_ids)) {
                $warranty_ids[] = $product['warranty_id'];
                $sell_line->warranties()->sync($warranty_ids);
            }
        } else {
            $sell_line->warranties()->sync([]);
        }
        //Adjust the sell line for combo items.
        if (isset($product['product_type']) && $product['product_type'] == 'combo') {
            //$this->editSellLineCombo($sell_line, $location_id, $sell_line->quantity, $new_qty);
            $adjust_stock = ($status_before != 'draft');
            $this->updateEditedSellLineCombo($product['combo'], $location_id, $adjust_stock);
        }
    }
    /**
     * Delete the products removed and increment product stock.
     *
     * @param array $transaction_line_ids
     * @param int $location_id
     *
     * @return boolean
     */
    public function deleteSellLines($transaction_line_ids, $location_id, $adjust_qty = true)
    {
        if (!empty($transaction_line_ids)) {
            $sell_lines = TransactionSellLine::whereIn('id', $transaction_line_ids)
                ->get();
            //Adjust quanity
            if ($adjust_qty) {
                foreach ($sell_lines as $line) {
                    $this->adjustQuantity($location_id, $line->product_id, $line->variation_id, $line->quantity);
                }
            }
            TransactionSellLine::whereIn('id', $transaction_line_ids)
                ->delete();
        }
    }
    /**
     * Delete the products removed and increment product stock.
     *
     * @param array $transaction_line_ids
     * @param int $location_id
     *
     * @return boolean
     */
    public function deleteSellLinesSettlement($transaction_line_ids, $location_id, $adjust_qty = true)
    {
        if (!empty($transaction_line_ids)) {
            $sell_lines = TransactionSellLine::whereIn('id', $transaction_line_ids)
                ->get();
            //Adjust quanity
            if ($adjust_qty) {
                foreach ($sell_lines as $line) {
                    $this->adjustQuantity($location_id, $line->product_id, $line->variation_id, $line->quantity);
                }
            }
            TransactionSellLine::whereIn('id', $transaction_line_ids)
                ->forceDelete();
        }
    }
    /**
     * Adjust the quantity of product and its variation
     *
     * @param int $location_id
     * @param int $product_id
     * @param int $variation_id
     * @param float $increment_qty
     *
     * @return boolean
     */
    public function adjustQuantity($location_id, $product_id, $variation_id, $increment_qty)
    {
        if ($increment_qty != 0) {
            $enable_stock = Product::find($product_id)->enable_stock;
            if ($enable_stock == 1) {
                
                $store_details = VariationStoreDetail::where('variation_id', $variation_id)
                    ->where('product_id', $product_id)
                    ->first();
                if(!empty($store_details)){
                    $store_details->increment('qty_available', $increment_qty);
                }
                
                
                //Adjust Quantity in variations location table
                VariationLocationDetails::where('variation_id', $variation_id)
                    ->where('product_id', $product_id)
                    ->where('location_id', $location_id)
                    ->increment('qty_available', $increment_qty);
            }
        }
    }
    /**
     * Add line for payment
     *
     * @param object/int $transaction
     * @param array $payments
     *
     * @return boolean
     */
    public function createOrUpdatePaymentLines($transaction, $payments, $business_id = null, $user_id = null, $uf_data = true,$status = null, $cheque_nos = null)
    {
        $payments_formatted = [];
        $account_transactions = [];
        $edit_ids = [];
        if (!is_object($transaction)) {
            $transaction = Transaction::findOrFail($transaction);
        }
        //If status is draft don't add payment
        if ($transaction->status == 'draft') {
            return true;
        }
        $c = 0;
        foreach ($payments as $payment) {
            
            //Check if transaction_sell_lines_id is set.
            $payment_mehod = $payment['method'];
            if (!empty($payment['payment_id'])) {
                
                $edit_ids[] = $payment['payment_id'];
                $this->editPaymentLine($payment, $transaction, $uf_data);
            } else {
                $payment_amount = $uf_data ? $this->num_uf($payment['amount']) : $payment['amount'];
                if ($payment_mehod == 'credit_sale') {
                    // $payment_amount = 0; // Fix customer ledger is not showing for credit sale [Chirag 16 Oct 2024]
                }

                //If amount is 0 then skip.
                if ($payment_amount > 0 && $payment_mehod != 'credit_purchase') { //will be true if amount is zero to create account credit transaction  changed !=0 to >=0
                    $prefix_type = 'sell_payment';
                    if ($transaction->type == 'purchase') {
                        $prefix_type = 'purchase_payment';
                    }
                    $ref_count = $this->setAndGetReferenceCount($prefix_type, $business_id);
                    //Generate reference number
                    $payment_ref_no = $this->generateReferenceNumber($prefix_type, $ref_count, $business_id);
                    //If change return then set account id same as the first payment line account id
                    if (isset($payment['is_return']) && $payment['is_return'] == 1) {
                        $payment['account_id'] = !empty($payments[0]['account_id']) ? $payments[0]['account_id'] : null;
                    }
                    
                    
                    $payment_data = [
                        'amount' => $payment_amount,
                        'method' => $payment['method'],
                        'business_id' => $transaction->business_id,
                        'is_return' => isset($payment['is_return']) ? $payment['is_return'] : 0,
                        'card_transaction_number' => $payment['card_transaction_number'],
                        'bank_name' => !empty($payment['bank_name']) ? $payment['bank_name'] : null,
                        'cheque_number' => $payment['cheque_number'].$cheque_nos,
                        'cheque_date' => !empty($payment['cheque_date']) ? $payment['cheque_date'] : date('Y-m-d'),
                        'note' => !empty($payment['note']) ? $payment['note'] : null,
                        'paid_on' => !empty($payment['paid_on']) ? $this->uf_date($payment['paid_on']) : $transaction->transaction_date,
                        'created_by' => empty($user_id) ? auth()->user()->id : $user_id,
                        'payment_for' => $transaction->contact_id,
                        'payment_ref_no' => $payment_ref_no,
                        'account_id' => !empty($payment['account_id']) ? $payment['account_id'] : null,
                        'payment_option_id' => !empty($payment['payment_option_id']) ? $payment['payment_option_id'] : null,
                        'post_dated_cheque' => $payment['post_dated_cheque'] ?? 0,
                        'update_post_dated_cheque' => $payment['update_post_dated_cheque'] ?? 0
                    ];
                    
                    if ($payment['method'] == 'custom_pay_1') {
                        $payment_data['transaction_no'] = $payment['transaction_no_1'];
                    } elseif ($payment['method'] == 'custom_pay_2') {
                        $payment_data['transaction_no'] = $payment['transaction_no_2'];
                    } elseif ($payment['method'] == 'custom_pay_3') {
                        $payment_data['transaction_no'] = $payment['transaction_no_3'];
                    }
                    // if method value is integer, then method holds cash group accounts id
                    if (!empty($payment['method'])) {
                        
                        if(empty($payment['account_id'])){
                            
                            $payment_method_Cash = $this->account_exist_return_id('Cash'); //Account::where('business_id', $business_id)->where('name', 'Cash')->where('is_closed', 0)->select('id')->first();
                            $payment_method_Cards = $this->account_exist_return_id('Cards'); //Account::where('business_id', $business_id)->where('name', 'Cards')->where('is_closed', 0)->select('id')->first();
                            $payment_method_Credit_sales = $this->account_exist_return_id('Accounts Receivable'); //Account::where('business_id', $business_id)->where('name', 'Accounts Receivable')->where('is_closed', 0)->select('id')->first();
                            
                            if($payment['method'] == 'cash'){
                                $payment_data['account_id'] = !empty($payment_method_Cash) ? $payment_method_Cash : null;
                            }elseif($payment['method'] == 'card'){
                                $payment_data['account_id'] = !empty($payment_method_Cards) ? $payment_method_Cards : null;
                            }elseif($payment['method'] == 'credit_sale'){
                                $payment_data['account_id'] = !empty($payment_method_Credit_sales) ? $payment_method_Credit_sales : null;
                            }
                            
                        }else{
                            if(!empty($payment_data['update_post_dated_cheque'])){
                                
                                $payment_data['related_account_id'] = $payment['account_id'];
                                
                                if($transaction->type == 'purchase' || $transaction->type == 'expense'){
                                    $payment_data['account_id'] = $this->account_exist_return_id('Issued Post Dated Cheques'); 
                                }else{
                                    $payment_data['account_id'] = $this->account_exist_return_id('Post Dated Cheques'); 
                                }
                               
                            }else{
                                $payment_data['account_id'] = $payment['account_id'];
                            }
                            
                        }
                        
                        
                      
                        if ($payment['method'] == 'direct_bank_deposit' || $payment['method'] == 'bank_transfer') {
                            $payment_data['account_id'] = $payment['account_id']; // set account id to selected bank account id
                            if ($transaction->type == 'purchase') {
                                //if cheque date and order date does not matched then create account payable transactions
                                if (date("Y-m-d", strtotime($transaction->transaction_date)) != $payment_data['cheque_date']) {
                                    $this->creaetAccountPayableDiffChequeDate($transaction, $payment_data);
                                }
                            }
                        }
                        
                    }
                    
                    
                    $payments_formatted[] = new TransactionPayment($payment_data);
                    
                    //if method is cash, cheque , card get default account for method
                    //if method is bank transfer or direct bank deposit set account as selected
                    $payment_data['amount'] =   $payment_amount;
                    $payment_data['transaction_type'] = $transaction->type;
                    $payment_data['location_id'] =   $transaction->location_id;
                    
                    
                    $account_transactions[$c] = [];
                    $account_transactions[$c] = $payment_data;
                    $c++; 

                    
                } else {
                    $account_transaction_data = [
                        'contact_id' => !empty($transaction) ? $transaction->contact_id : null,
                        'amount' => $uf_data ? $this->num_uf($payment['amount']) : $payment['amount'],
                        'type' => 'credit',
                        'operation_date' =>  !empty($transaction->transaction_date) ? $transaction->transaction_date : date('Y-m-d H:i:s'),
                        'created_by' => Auth::user()->id,
                        'transaction_id' => !empty($transaction) ? $transaction->id : null,
                        'transaction_payment_id' => null
                    ];
                    if ($payment_mehod == 'credit_purchase' && $status == "received") {
                        $this->createCreditPurchaseTransactions($transaction, $account_transaction_data);
                    }
                    
                    if ($payment_mehod == 'credit_sale') {
                        $account_transaction_data['account_id'] = $payment['account_id'];
                        $this->createCreditSaleTransactions($transaction, $account_transaction_data);
                    }
                }
            }
        }

        if(empty($edit_ids) && $status != "received" && $payment_mehod != 'credit_purchase'){
            
             $transit_transaction_data = [
                'contact_id' => !empty($transaction) ? $transaction->contact_id : null,
                'amount' => $transaction->final_total,
                'type' => 'debit',
                'operation_date' =>  !empty($transaction->transaction_date) ? $transaction->transaction_date : date('Y-m-d H:i:s'),
                'created_by' => Auth::user()->id,
                'transaction_id' => !empty($transaction) ? $transaction->id : null,
                'transaction_payment_id' => null,
                'post_dated_cheque' => $payments[0]['post_dated_cheque'] ?? 0,
                'update_post_dated_cheque' => $payments[0]['update_post_dated_cheque'] ?? 0
            ];
            
            $this->createTransitTransactions($transaction, $transit_transaction_data);
        }
        
        //Delete the payment lines removed.
        if (!empty($edit_ids)) {
            $deleted_transaction_payments = $transaction->payment_lines()->whereNotIn('id', $edit_ids)->get();
            $transaction->payment_lines()->whereNotIn('id', $edit_ids)->forcedelete();

            AccountTransaction::whereNotIn('transaction_payment_id', $edit_ids)->where('transaction_id',$transaction->id)->forcedelete();;

            //Fire delete transaction payment event
            foreach ($deleted_transaction_payments as $deleted_transaction_payment) {
                event(new TransactionPaymentDeleted($deleted_transaction_payment->id, $deleted_transaction_payment->account_id));
            }
        }
        if (!empty($payments_formatted)) {
            $transaction->payment_lines()->saveMany($payments_formatted);
            foreach ($transaction->payment_lines as $key => $value) {
                if (!empty($account_transactions[$key])) {
                    if($account_transactions[$key]['transaction_type'] == 'route_operation'){
                        $this->addRouteOperationTransactions($value, $account_transactions[$key]);
                    }else{
                        event(new TransactionPaymentAdded($value, $account_transactions[$key]));
                    }
                    
                }
            }
        }
        return true;
    }
    
    public function getAccountTypeIdOfAccount($account_id, $business_id)
    {
        $account_type = Account::join('account_types', 'accounts.account_type_id', 'account_types.id')
            ->where('accounts.id', $account_id)
            ->where('accounts.business_id', $business_id)
            ->select('account_types.id as account_type_id')
            ->first();
        return $account_type->account_type_id;
    }
    
    public function addRouteOperationTransactions($transactionPayment,$formInput)
    {
        $business_id = request()->session()->get('business.id');
        $asset_type_ids = AccountType::getAccountTypeIdOfType('Assets', $business_id);
        $account_type_id = $this->getAccountTypeIdOfAccount($formInput['account_id'], $business_id);
        $transaction_payment_details = TransactionPayment::where('id', $transactionPayment->id)->first();
        $transaction = Transaction::where('id',  $transaction_payment_details->transaction_id)->first();
        $account_payable = Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->where('is_closed', 0)->first();
        $account_payable_id = !empty($account_payable) ? $account_payable->id : 0;
        $account_receivable = Account::where('business_id', $business_id)->where('name', 'Accounts Receivable')->where('is_closed', 0)->first();
        $account_receivable_id = !empty($account_receivable) ? $account_receivable->id : 0;

        
        $account_transaction_data = [
                'contact_id' => !empty($transaction) ? $transaction->contact_id : null,
                'amount' => $formInput['amount'],
                'account_id' => $formInput['account_id'],
                'type' => AccountTransaction::getAccountTransactionType($formInput['transaction_type']),
                'operation_date' =>  !empty($transaction->transaction_date) ? $transaction->transaction_date : date('Y-m-d H:i:s'),
                'created_by' => $transactionPayment->created_by,
                'transaction_id' => !empty($transaction) ? $transaction->id : null,
                'transaction_payment_id' =>  !empty($transactionPayment->id) ? $transactionPayment->id : null
            ];
            
        $fleet = Fleet::find($transaction->fleet_id);
        $account_transaction_data['type'] = 'debit';
        AccountTransaction::createAccountTransaction($account_transaction_data);
        
        $account_transaction_data['type'] = 'credit';
        $account_transaction_data['account_id'] = $fleet->income_account_id;
        AccountTransaction::createAccountTransaction($account_transaction_data);
    }
    
    public function createCreditPurchaseTransactions($transaction, $account_transaction_data)
    {
        $account_payable_id = $this->account_exist_return_id('Accounts Payable');
        $payable_transaction_exist = AccountTransaction::where('transaction_id', $transaction->id)->where('type', 'credit')->where('account_id', $account_payable_id)->first();
        $contact_ledger_exist = ContactLedger::where('transaction_id', $transaction->id)->where('type', 'credit')->first();
        if (!empty($payable_transaction_exist)) {
            AccountTransaction::where('transaction_id', $transaction->id)->where('type', 'credit')->where('account_id', $account_payable_id)->where('id', '!=', $payable_transaction_exist->id)->forcedelete(); // quick fix for more then one payable account entries
            ContactLedger::where('transaction_id', $transaction->id)->where('type', 'credit')->where('id', '!=', $contact_ledger_exist->id)->forcedelete();
        } else {
            if ($transaction->type == 'purchase') {
                $account_transaction_data['type'] = 'credit';
                $account_transaction_data['account_id'] = $account_payable_id;
                AccountTransaction::createAccountTransaction($account_transaction_data);
                ContactLedger::createContactLedger($account_transaction_data);
            }
        }
        return true;
    }
    
    public function createCreditSaleTransactions($transaction, $account_transaction_data)
    {
        $account_payable_id = $account_transaction_data['account_id'];
        $payable_transaction_exist = AccountTransaction::where('transaction_id', $transaction->id)->where('type', 'credit')->where('account_id', $account_payable_id)->first();
        $contact_ledger_exist = ContactLedger::where('transaction_id', $transaction->id)->where('type', 'credit')->first();
        if (!empty($payable_transaction_exist)) {
            AccountTransaction::where('transaction_id', $transaction->id)->where('type', 'credit')->where('account_id', $account_payable_id)->where('id', '!=', $payable_transaction_exist->id)->forcedelete(); // quick fix for more then one payable account entries
            ContactLedger::where('transaction_id', $transaction->id)->where('type', 'credit')->where('id', '!=', $contact_ledger_exist->id)->forcedelete();
        } else {
            $account_transaction_data['type'] = 'credit';
            $account_transaction_data['account_id'] = $account_payable_id;
            AccountTransaction::createAccountTransaction($account_transaction_data);
            ContactLedger::createContactLedger($account_transaction_data);
        }
        return true;
    }
    
    
    public function createTransitTransactions($transaction, $account_transaction_data)
    {
        $account_payable_id = $this->account_exist_return_id('Goods in Transit');
        $payable_transaction_exist = AccountTransaction::where('transaction_id', $transaction->id)->where('type', 'debit')->where('account_id', $account_payable_id)->first();
        if (!empty($payable_transaction_exist)) {
            AccountTransaction::where('transaction_id', $transaction->id)->where('type', 'debit')->where('account_id', $account_payable_id)->where('id', '!=', $payable_transaction_exist->id)->forcedelete(); // quick fix for more then one payable account entries
        } else {
            if ($transaction->type == 'purchase') {
                $account_transaction_data['amount'] =  $transaction->final_total;
                $account_transaction_data['type'] = 'debit';
                $account_transaction_data['account_id'] = $account_payable_id;
                AccountTransaction::createAccountTransaction($account_transaction_data);
            }
        }
        return true;
    }
    
    
    public function updateCreditTransactions($transaction, $payment_array)
    {
        $transaction_id = $transaction->id;
        $payable_account_id = $this->account_exist_return_id('Accounts Payable');
        if ($transaction->type == 'purchase') {
            //delete the account transaction if exist, payments, and ledger transactions
            AccountTransaction::where('transaction_id', $transaction_id)->forcedelete();
            TransactionPayment::where('transaction_id', $transaction_id)->forcedelete();
            ContactLedger::where('transaction_id', $transaction_id)->forcedelete();
            //create account payable entry
            $contact_id = $transaction->contact_id;
            $account_transaction_data = [
                'amount' => $this->num_uf($payment_array['amount']),
                'contact_id' => $contact_id,
                'account_id' => $payable_account_id,
                'type' => 'credit',
                'operation_date' => $transaction->transaction_date,
                'created_by' => Auth::user()->id,
                'transaction_id' => $transaction->id,
                'transaction_payment_id' => null,
                'note' => null
            ];
            AccountTransaction::createAccountTransaction($account_transaction_data);
            //create ledger entry
            ContactLedger::createContactLedger($account_transaction_data);
        }
    }
    //create account payable transaction if purchase order date and cheque date not matches
    public function creaetAccountPayableDiffChequeDate($transaction, $payment_data)
    {
        $account_payable_id = $this->account_exist_return_id('Accounts Payable');
        $account_transaction_credit = AccountTransaction::where('transaction_id', $transaction->id)->where('type', 'credit')->where('account_id', $account_payable_id)->first();
        $account_transaction_data = [
            'amount' => $this->num_uf($payment_data['amount']),
            'type' => 'credit',
            'account_id' => $account_payable_id,
            'operation_date' =>  $transaction->transaction_date,
            'created_by' => Auth::user()->id,
            'transaction_id' => !empty($transaction) ? $transaction->id : null,
            'transaction_payment_id' => null
        ];
        if (!empty($account_transaction_credit)) {
            $account_transaction_credit->update($account_transaction_data);
        } else {
            AccountTransaction::createAccountTransaction($account_transaction_data);
        }
        $account_transaction_data['type'] = 'debit';
        $account_transaction_data['operation_date'] = $payment_data['cheque_date'];
        $account_transaction_debit = AccountTransaction::where('transaction_id', $transaction->id)->where('type', 'debit')->where('account_id', $account_payable_id)->first();
        if (!empty($account_transaction_debit)) {
            $account_transaction_debit->update($account_transaction_data);
        } else {
            AccountTransaction::createAccountTransaction($account_transaction_data);
        }
    }
    /**
     * Edit transaction payment line
     *
     * @param array $product
     *
     * @return boolean
     */
    public function editPaymentLine($payment, $transaction = null, $uf_data = true)
    {
        $payment_id = $payment['payment_id'];
        unset($payment['payment_id']);
        unset($payment['paid_on']);
        //if payment method chagned to credit purchase then delete the account and ledger transactions and delete the older payment
        if ($payment['method'] == 'credit_purchase') {
            $this->updateCreditTransactions($transaction, $payment);
            return true;
        }
        if ($payment['method'] == 'direct_bank_deposit' || $payment['method'] == 'bank_transfer' || $payment['method'] == 'cheque') {
            if (!empty($payment['cheque_date'])) {
                // $payment['paid_on'] = $payment['cheque_date'];
            }
        }

       if ($payment['method'] == 'direct_bank_deposit' || $payment['method'] == 'bank_transfer' || $payment['method'] == 'Bank') {
            $payment['account_id'] = $payment['account_id'] ?? null;
            if ($transaction->type == 'purchase') {
                //if cheque date and order date does not matched then create account payable transactions
                if (date("Y-m-d", strtotime($transaction->transaction_date)) != date("Y-m-d", strtotime($payment['cheque_date']))) {
                    $this->creaetAccountPayableDiffChequeDate($transaction, $payment);
                }
            }
        } 
        
        if(!empty($payment['update_post_dated_cheque'])){
            $payment_data['related_account_id'] = $payment['account_id'] ?? null;
            
            if($transaction->type == 'purchase' || $transaction->type == 'expense'){
                $payment['account_id'] = $this->account_exist_return_id('Issued Post Dated Cheques'); 
            }else{
                $payment['account_id'] = $this->account_exist_return_id('Post Dated Cheques'); 
            }
           
        }else{
            $payment['account_id'] = $payment['account_id'] ?? null;
        }
        
        
        unset($payment['transaction_no_1'], $payment['transaction_no_2'], $payment['transaction_no_3']);
        $payment['cheque_date'] = !empty($payment['cheque_date']) ?  $payment['cheque_date'] : null;
        $payment['amount'] = $uf_data ? $this->num_uf($payment['amount']) : $payment['amount'];
        $payment['post_dated_cheque'] =  $payment['post_dated_cheque'] ?? 0;
        $payment['update_post_dated_cheque'] =  $payment['update_post_dated_cheque'] ?? 0;
        $tp = TransactionPayment::where('id', $payment_id)
            ->first();
        $transaction_type = !empty($transaction->type) ? $transaction->type : null;
        $tp->update($payment);
        

        if($tp) {
            AccountTransaction::where('transaction_payment_id', $tp->id)->update(['amount' => $payment['amount'], 'account_id' => $payment['account_id'], 'cheque_date' => $payment['cheque_date'], 'type' => 'credit', 'post_dated_cheque' => $payment['post_dated_cheque'],'update_post_dated_cheque' => $payment['update_post_dated_cheque']]);

            $cts = ContactLedger::where('transaction_payment_id', $tp->id)->get();
            foreach ($cts as $ct) {
                $ct->amount = $payment['amount'];
                $ct->operation_date = $payment['cheque_date'];
                $ct->save();
            }
        }

        //event
        event(new TransactionPaymentUpdated($tp, $transaction->type));
        return true;
    }
    /**
     * Get payment line for a transaction
     *
     * @param int $transaction_id
     *
     * @return boolean
     */
    public function getPaymentDetails($transaction_id)
    {
        $payment_lines = TransactionPayment::where('transaction_id', $transaction_id)
            ->get()->toArray();
        return $payment_lines;
    }
    /**
     * Gives the receipt details in proper format.
     *
     * @param int $transaction_id
     * @param int $location_id
     * @param object $invoice_layout
     * @param array $business_details
     * @param array $receipt_details
     * @param string $receipt_printer_type
     *
     * @return array
     */

     function floattostr( $val )
{
    preg_match( "#^([\+\-]|)([0-9]*)(\.([0-9]*?)|)(0*)$#", trim($val), $o );
    return $o[1].sprintf('%d',$o[2]).($o[3]!='.'?$o[3]:'');
}
    public function getReceiptDetails($transaction_id, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type)
    {
        $il = $invoice_layout;
        $transaction = Transaction::find($transaction_id);
        $rep = $transaction->reprint_no + 1;
        $transaction->reprint_no = $rep;
        $transaction->save();
        
        $transaction_type = $transaction->type;
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
            'reprint' => $rep,
            'tax_amount' => $transaction->tax_amount
        ];
        //Display name
        $output['display_name'] = $output['business_name'];
        if (!empty($output['location_name'])) {
            if (!empty($output['display_name'])) {
                $output['display_name'] .= ', ';
            }
            $output['display_name'] .= $output['location_name'];
        }
        $contact_details = $this->getCustomerDetails($transaction->contact_id);
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
        $customer = Contact::find($transaction->contact_id);
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
        if ($il->show_reward_point == 1) {
            $output['customer_rp_label'] = $business_details->rp_name;
            $output['customer_total_rp'] = $customer->total_rp;
        }
        $output['client_id'] = '';
        $output['client_id_label'] = '';
        if ($il->show_client_id == 1) {
            $output['client_id_label'] = !empty($il->client_id_label) ? $il->client_id_label : '';
            $output['client_id'] = !empty($customer->contact_id) ? $customer->contact_id : '';
        }
        //Sales person info
        $output['sales_person'] = '';
        $output['sales_person_label'] = '';
        if ($il->show_sales_person == 1) {
            $output['sales_person_label'] = !empty($il->sales_person_label) ? $il->sales_person_label : '';
            $output['sales_person'] = !empty($transaction->sales_person->user_full_name) ? $transaction->sales_person->user_full_name : '';
        }
        //Invoice info
        $output['invoice_no'] = $transaction->is_quotation ? $transaction->ref_no : $transaction->invoice_no;
        $output['quotation_no'] = $transaction->is_quotation ? $transaction->invoice_no : '';
        $output['shipping_address'] = !empty($transaction->shipping_address()) ? $transaction->shipping_address() : $transaction->shipping_address;
        //Heading & invoice label, when quotation use the quotation heading.
        if ($transaction_type == 'sell_return') {
            $output['invoice_heading'] = $il->cn_heading;
            $output['invoice_no_prefix'] = $il->cn_no_label;
        } elseif ($transaction->status == 'draft' && $transaction->is_quotation == 1) {
            $output['invoice_heading'] = $il->quotation_heading;
            $output['invoice_no_prefix'] = $il->quotation_no_prefix;
        } else {
            $output['invoice_no_prefix'] = $il->invoice_no_prefix;
            $output['invoice_heading'] = $il->invoice_heading;
            if ($transaction->payment_status == 'paid' && !empty($il->invoice_heading_paid)) {
                $output['invoice_heading'] .= ' ' . $il->invoice_heading_paid;
            } elseif (in_array($transaction->payment_status, ['due', 'partial']) && !empty($il->invoice_heading_not_paid)) {
                $output['invoice_heading'] .= ' ' . $il->invoice_heading_not_paid;
            }
        }
        $output['date_label'] = $il->date_label;
        if (blank($il->date_time_format)) {
            $output['invoice_date'] = $this->format_date($transaction->transaction_date, true, $business_details);
        } else {
            $output['invoice_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', $transaction->transaction_date)->format($il->date_time_format);
        }
        if (!empty($il->common_settings['show_due_date'])) {
            $output['due_date_label'] = !empty($il->common_settings['due_date_label']) ? $il->common_settings['due_date_label'] : '';
            $due_date = $transaction->due_date;
            if (!empty($due_date)) {
                if (blank($il->date_time_format)) {
                    $output['due_date'] = $this->format_date($due_date->toDateTimeString(), true, $business_details);
                } else {
                    $output['due_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', $due_date->toDateTimeString())->format($il->date_time_format);
                }
            }
        }
        $show_currency = true;
        if ($receipt_printer_type == 'printer' && trim($business_details->currency_symbol) != '$') {
            $show_currency = false;
        }
        //Invoice product lines
        $is_lot_number_enabled = $business_details->enable_lot_number;
        $is_product_expiry_enabled = $business_details->enable_product_expiry;
        $output['lines'] = [];
        if ($transaction_type == 'sell') {
            $sell_line_relations = ['modifiers', 'sub_unit', 'warranties'];
            if ($is_lot_number_enabled == 1) {
                $sell_line_relations[] = 'lot_details';
            }
            $lines = $transaction->sell_lines()->whereNull('parent_sell_line_id')->with($sell_line_relations)->get();
            foreach ($lines as $key => $value) {
                if (!empty($value->sub_unit_id)) {
                    $formated_sell_line = $this->recalculateSellLineTotals($business_details->id, $value);
                    $lines[$key] = $formated_sell_line;
                }
            }

            $details = $this->_receiptDetailsSellLines($lines, $il, $business_details);

            $output['lines'] = $details['lines'];
            $output['taxes'] = [];
            foreach ($details['lines'] as $line) {
                if (!empty($line['group_tax_details'])) {
                    foreach ($line['group_tax_details'] as $tax_group_detail) {
                        if (!isset($output['taxes'][$tax_group_detail['name']])) {
                            $output['taxes'][$tax_group_detail['name']] = 0;
                        }
                        $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                    }
                } elseif (!empty($line['tax_unformatted']) && $line['tax_unformatted'] != 0) {
                    if (!isset($output['taxes'][$line['tax_name']])) {
                        $output['taxes'][$line['tax_name']] = 0;
                    }
                    $output['taxes'][$line['tax_name']] += $line['tax_unformatted'];
                }
            }
        } 
        elseif ($transaction_type == 'autoservice') {
            $sell_line_relations = ['modifiers', 'sub_unit', 'warranties'];
            if ($is_lot_number_enabled == 1) {
                $sell_line_relations[] = 'lot_details';
            }
            $lines = $transaction->sell_lines()->whereNull('parent_sell_line_id')->with($sell_line_relations)->get();
            foreach ($lines as $key => $value) {
                if (!empty($value->sub_unit_id)) {
                    $formated_sell_line = $this->recalculateSellLineTotals($business_details->id, $value);
                    $lines[$key] = $formated_sell_line;
                }
            }

            $details = $this->_receiptDetailsSellLines($lines, $il, $business_details);

            $output['lines'] = $details['lines'];
            $output['taxes'] = [];
            foreach ($details['lines'] as $line) {
                if (!empty($line['group_tax_details'])) {
                    foreach ($line['group_tax_details'] as $tax_group_detail) {
                        if (!isset($output['taxes'][$tax_group_detail['name']])) {
                            $output['taxes'][$tax_group_detail['name']] = 0;
                        }
                        $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                    }
                } elseif (!empty($line['tax_unformatted']) && $line['tax_unformatted'] != 0) {
                    if (!isset($output['taxes'][$line['tax_name']])) {
                        $output['taxes'][$line['tax_name']] = 0;
                    }
                    $output['taxes'][$line['tax_name']] += $line['tax_unformatted'];
                }
            }
        }

        elseif ($transaction_type == 'sell_return') {
            $parent_sell = Transaction::find($transaction->return_parent_id);
            $lines = $parent_sell->sell_lines;
            foreach ($lines as $key => $value) {
                if (!empty($value->sub_unit_id)) {
                    $formated_sell_line = $this->recalculateSellLineTotals($business_details->id, $value);
                    $lines[$key] = $formated_sell_line;
                }
            }
            $details = $this->_receiptDetailsSellReturnLines($lines, $il, $business_details);
            $output['lines'] = $details['lines'];
            $output['taxes'] = [];
            foreach ($details['lines'] as $line) {
                if (!empty($line['group_tax_details'])) {
                    foreach ($line['group_tax_details'] as $tax_group_detail) {
                        if (!isset($output['taxes'][$tax_group_detail['name']])) {
                            $output['taxes'][$tax_group_detail['name']] = 0;
                        }
                        $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                    }
                }
            }
        }



        //show cat code
        $output['show_cat_code'] = $il->show_cat_code;
        $output['cat_code_label'] = $il->cat_code_label;
        //Subtotal
        $output['subtotal_label'] = $il->sub_total_label . ':';
        $output['subtotal'] = ($transaction->total_before_tax != 0) ? $this->num_f($transaction->total_before_tax, $show_currency, $business_details) : 0;

        $subtotal_final = array_sum(
            array_map(function ($value) {
                return isset($value) ? (float)str_replace(',', '', $value) : 0;
            }, array_column($details['lines'], 'sub_total_final'))
        );

        $output['subtotal_final'] = $this->num_f($subtotal_final, false, $business_details);

        $output['subtotal_unformatted'] = ($transaction->total_before_tax != 0) ? $transaction->total_before_tax : 0;
        //Discount
        $output['line_discount_label'] = $invoice_layout->discount_label;
        $output['discount_label'] = $invoice_layout->discount_label;
        $output['discount_label'] .= ($transaction->discount_type == 'percentage') ? ' (' . $this->floattostr($transaction->discount_amount) . '%) :' : '';
        if ($transaction->discount_type == 'percentage') {
            $discount = ($transaction->discount_amount / 100) * $transaction->total_before_tax;
        } else {
            $discount = $transaction->discount_amount;
        }
        $output['discount'] = ($discount != 0) ? $this->num_f($discount, $show_currency, $business_details) : 0;
        //reward points
        if ($business_details->enable_rp == 1 && !empty($transaction->rp_redeemed)) {
            $output['reward_point_label'] = $business_details->rp_name;
            $output['reward_point_amount'] = $this->num_f($transaction->rp_redeemed_amount, $show_currency, $business_details);
        }
        //Format tax
        if (!empty($output['taxes'])) {
            foreach ($output['taxes'] as $key => $value) {
                $output['taxes'][$key] = $this->num_f($value, $show_currency, $business_details);
            }
        }
        //Order Tax
        $tax = $transaction->tax;
        $output['tax_label'] = $invoice_layout->tax_label;
        $output['line_tax_label'] = $invoice_layout->tax_label;
        if (!empty($tax) && !empty($tax->name)) {
            $output['tax_label'] .= ' (' . $tax->name . ')';
        }
        $output['tax_label'] .= ':';

        //$output['tax'] = ($transaction->tax_amount != 0) ? $this->num_f($transaction->tax_amount, $show_currency, $business_details) : 0;

        $final_tax = array_sum(array_column($details['lines'], 'tax'));

        $output['tax'] = $this->num_f($final_tax, false, $business_details);

        if ($transaction->tax_amount != 0 && !empty($tax) && $tax->is_tax_group) {
            $transaction_group_tax_details = $this->groupTaxDetails($tax, $transaction->tax_amount);
            $output['group_tax_details'] = [];
            foreach ($transaction_group_tax_details as $value) {
                $output['group_tax_details'][$value['name']] = $this->num_f($value['calculated_tax'], $show_currency, $business_details);
            }
        }
        //Shipping charges
        $output['shipping_charges'] = ($transaction->shipping_charges != 0) ? $this->num_f($transaction->shipping_charges, $show_currency, $business_details) : 0;
        $output['shipping_charges_label'] = trans("sale.shipping_charges");
        //Shipping details
        $output['shipping_details'] = $transaction->shipping_details;
        $output['shipping_details_label'] = trans("sale.shipping_details");
        //Total
        if ($transaction_type == 'sell_return') {
            $output['total_label'] = $invoice_layout->cn_amount_label . ':';
            $output['total'] = $this->num_f($transaction->final_total, $show_currency, $business_details);
        } else {
            $output['total_label'] = $invoice_layout->total_label . ':';
            $output['total'] = $this->num_f($transaction->final_total, $show_currency, $business_details);
        }
        //Paid & Amount due, only if final
        $output['total_paid_label'] = $il->paid_label;
        $output['total_due_label'] = $il->total_due_label;
        
        if ($transaction_type == 'sell' && $transaction->status == 'final') {
            $paid_amount = $this->getTotalPaid($transaction->id);
            $due = $transaction->final_total - $paid_amount;
            $output['total_paid'] = ($paid_amount == 0) ? 0 : $this->num_f($paid_amount, $show_currency, $business_details);
            $output['total_due'] = ($due == 0) ? 0 : $this->num_f($due, $show_currency, $business_details);
           
            if ($il->show_previous_bal == 1) {
                $all_due = $this->getContactDue($transaction->contact_id);
                if (!empty($all_due)) {
                    $output['all_bal_label'] = $il->prev_bal_label;
                    $output['all_due'] = $this->num_f($all_due, $show_currency, $business_details);
                }
            }
            if($paid_amount > $transaction->final_total){
                $output['paid_greater'] = true;
                $output['due_amount'] = $this->num_f($due, $show_currency, $business_details);
            }else{
                $output['paid_greater'] = false;
                $output['due_amount'] = $this->num_f($due, $show_currency, $business_details);
            }
            //Get payment details
            $output['payments'] = [];
            if ($il->show_payments == 1) {
                $payments = $transaction->payment_lines->toArray();
                $payment_types = $this->payment_types();
                if (!empty($payments)) {
                    foreach ($payments as $value) {
                        $method = !empty($payment_types[$value['method']]) ? $payment_types[$value['method']] : '';
                        if ($value['method'] == 'cash') {
                            $output['payments'][] =
                                [
                                    'method' => $method . ($value['is_return'] == 1 ? ' (' . $il->change_return_label . ')(-)' : ''),
                                    'amount' => $this->num_f($value['amount'], $show_currency, $business_details),
                                    'date' => $this->format_date($value['paid_on'], false, $business_details)
                                ];
                            if ($value['is_return'] == 1) {
                            }
                        } elseif ($value['method'] == 'card') {
                            $output['payments'][] =
                                [
                                    'method' => $method . (!empty($value['card_transaction_number']) ? (', Transaction Number:' . $value['card_transaction_number']) : ''),
                                    'amount' => $this->num_f($value['amount'], $show_currency, $business_details),
                                    'date' => $this->format_date($value['paid_on'], false, $business_details)
                                ];
                        } elseif ($value['method'] == 'cheque') {
                            $output['payments'][] =
                                [
                                    'method' => $method . (!empty($value['cheque_number']) ? (', Cheque Number:' . $value['cheque_number']) : ''),
                                    'amount' => $this->num_f($value['amount'], $show_currency, $business_details),
                                    'date' => $this->format_date($value['paid_on'], false, $business_details)
                                ];
                        } elseif ($value['method'] == 'bank_transfer') {
                            $output['payments'][] =
                                [
                                    'method' => $method . (!empty($value['bank_account_number']) ? (', Account Number:' . $value['bank_account_number']) : ''),
                                    'amount' => $this->num_f($value['amount'], $show_currency, $business_details),
                                    'date' => $this->format_date($value['paid_on'], false, $business_details)
                                ];
                        } elseif ($value['method'] == 'other') {
                            $output['payments'][] =
                                [
                                    'method' => $method,
                                    'amount' => $this->num_f($value['amount'], $show_currency, $business_details),
                                    'date' => $this->format_date($value['paid_on'], false, $business_details)
                                ];
                        } elseif ($value['method'] == 'custom_pay_1') {
                            $output['payments'][] =
                                [
                                    'method' => $method . (!empty($value['transaction_no']) ? (', ' . trans("lang_v1.transaction_no") . ':' . $value['transaction_no']) : ''),
                                    'amount' => $this->num_f($value['amount'], $show_currency, $business_details),
                                    'date' => $this->format_date($value['paid_on'], false, $business_details)
                                ];
                        } elseif ($value['method'] == 'custom_pay_2') {
                            $output['payments'][] =
                                [
                                    'method' => $method . (!empty($value['transaction_no']) ? (', ' . trans("lang_v1.transaction_no") . ':' . $value['transaction_no']) : ''),
                                    'amount' => $this->num_f($value['amount'], $show_currency, $business_details),
                                    'date' => $this->format_date($value['paid_on'], false, $business_details)
                                ]; 
                        } elseif ($value['method'] == 'custom_pay_3') {
                            $output['payments'][] =
                                [
                                    'method' => $method . (!empty($value['transaction_no']) ? (', ' . trans("lang_v1.transaction_no") . ':' . $value['transaction_no']) : ''),
                                    'amount' => $this->num_f($value['amount'], $show_currency, $business_details),
                                    'date' => $this->format_date($value['paid_on'], false, $business_details)
                                ];
                        }
                    }
                }
            }
        }
        //Check for barcode
        $output['barcode'] = ($il->show_barcode == 1) ? $transaction->invoice_no : false;
        //Additional notes
        $output['additional_notes'] = $transaction->additional_notes;
        $output['footer_text'] = $invoice_layout->footer_text;
        //Barcode related information.
        $output['show_barcode'] = !empty($il->show_barcode) ? true : false;
        //Module related information.
        $il->module_info = !empty($il->module_info) ? json_decode($il->module_info, true) : [];
        if (!empty($il->module_info['tables']) && $this->isModuleEnabled('tables')) {
            //Table label & info
            $output['table_label'] = null;
            $output['table'] = null;
            if (isset($il->module_info['tables']['show_table'])) {
                $output['table_label'] = !empty($il->module_info['tables']['table_label']) ? $il->module_info['tables']['table_label'] : '';
                if (!empty($transaction->res_table_id)) {
                    $table = ResTable::find($transaction->res_table_id);
                }
                //res_table_id
                $output['table'] = !empty($table->name) ? $table->name : '';
            }
        }
        if (!empty($il->module_info['types_of_service']) && $this->isModuleEnabled('types_of_service') && !empty($transaction->types_of_service_id)) {
            //Table label & info
            $output['types_of_service_label'] = null;
            $output['types_of_service'] = null;
            if (isset($il->module_info['types_of_service']['show_types_of_service'])) {
                $output['types_of_service_label'] = !empty($il->module_info['types_of_service']['types_of_service_label']) ? $il->module_info['types_of_service']['types_of_service_label'] : '';
                $output['types_of_service'] = $transaction->types_of_service->name;
            }
            if (isset($il->module_info['types_of_service']['show_tos_custom_fields'])) {
                $output['types_of_service_custom_fields'] = [];
                if (!empty($transaction->service_custom_field_1)) {
                    $output['types_of_service_custom_fields'][__('lang_v1.service_custom_field_1')] = $transaction->service_custom_field_1;
                }
                if (!empty($transaction->service_custom_field_2)) {
                    $output['types_of_service_custom_fields'][__('lang_v1.service_custom_field_2')] = $transaction->service_custom_field_2;
                }
                if (!empty($transaction->service_custom_field_3)) {
                    $output['types_of_service_custom_fields'][__('lang_v1.service_custom_field_3')] = $transaction->service_custom_field_3;
                }
                if (!empty($transaction->service_custom_field_4)) {
                    $output['types_of_service_custom_fields'][__('lang_v1.service_custom_field_4')] = $transaction->service_custom_field_4;
                }
            }
        }
        if (!empty($il->module_info['service_staff']) && $this->isModuleEnabled('service_staff')) {
            //Waiter label & info
            $output['service_staff_label'] = null;
            $output['service_staff'] = null;
            if (isset($il->module_info['service_staff']['show_service_staff'])) {
                $output['service_staff_label'] = !empty($il->module_info['service_staff']['service_staff_label']) ? $il->module_info['service_staff']['service_staff_label'] : '';
                if (!empty($transaction->res_waiter_id)) {
                    $waiter = \App\User::find($transaction->res_waiter_id);
                }
                //res_table_id
                $output['service_staff'] = !empty($waiter->id) ? implode(' ', [$waiter->first_name, $waiter->last_name]) : '';
            }
        }
        //Repair module details
        if (!empty($il->module_info['repair']) && $transaction->sub_type == 'repair') {
            if (!empty($il->module_info['repair']['show_repair_status'])) {
                $output['repair_status_label'] = $il->module_info['repair']['repair_status_label'];
                $output['repair_status'] = '';
                if (!empty($transaction->repair_status_id)) {
                    $repair_status = \Modules\Repair\Entities\RepairStatus::find($transaction->repair_status_id);
                    $output['repair_status'] = $repair_status->name;
                }
            }
            if (!empty($il->module_info['repair']['show_repair_warranty'])) {
                $output['repair_warranty_label'] = $il->module_info['repair']['repair_warranty_label'];
                $output['repair_warranty'] = '';
                if (!empty($transaction->repair_warranty_id)) {
                    $repair_warranty = \Modules\Repair\Entities\Warranty::find($transaction->repair_warranty_id);
                    $output['repair_warranty'] = $repair_warranty->name;
                }
            }
            if (!empty($il->module_info['repair']['show_serial_no'])) {
                $output['serial_no_label'] = $il->module_info['repair']['serial_no_label'];
                $output['repair_serial_no'] = $transaction->repair_serial_no;
            }
            if (!empty($il->module_info['repair']['show_defects'])) {
                $output['defects_label'] = $il->module_info['repair']['defects_label'];
                $output['repair_defects'] = $transaction->repair_defects;
            }
        }
        $output['design'] = $il->design;
        $output['table_tax_headings'] = !empty($il->table_tax_headings) ? array_filter(json_decode($il->table_tax_headings), 'strlen') : null;
        return (object) $output;
    }

    public function getOtherSaleReceiptDetails($print_other_sale_ids, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type)
    {
        $il = $invoice_layout;
        $rep = $print_other_sale_ids[0];
        
        $transaction_type = "sell";
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
            'reprint' => $rep,
            'tax_amount' => "..."
        ];
        //Display name
        $output['display_name'] = $output['business_name'];
        if (!empty($output['location_name'])) {
            if (!empty($output['display_name'])) {
                $output['display_name'] .= ', ';
            }
            $output['display_name'] .= $output['location_name'];
        }
        $contact_details = ['due_amount' => 0, 'customer_name' => 'Customer', 'sol_with_approval' => false];
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
        $business_id =  Auth::user()->business_id;
        $customer = Contact::where('business_id', $business_id)->where('active', 1)->where('name', "Walk-In Customer")->orderBy('id', 'asc')->first();
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
        if ($il->show_reward_point == 1) {
            $output['customer_rp_label'] = $business_details->rp_name;
            $output['customer_total_rp'] = $customer->total_rp;
        }
        $output['client_id'] = '';
        $output['client_id_label'] = '';
        if ($il->show_client_id == 1) {
            $output['client_id_label'] = !empty($il->client_id_label) ? $il->client_id_label : '';
            $output['client_id'] = !empty($customer->contact_id) ? $customer->contact_id : '';
        }
        //Sales person info
        $output['sales_person'] = '';
        $output['sales_person_label'] = '';
        $pump_operator_id = Auth::user()->pump_operator_id;
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        if ($il->show_sales_person == 1) {
            $output['sales_person_label'] = !empty($il->sales_person_label) ? $il->sales_person_label : '';
            $output['sales_person'] = !empty($pump_operator->name) ? $pump_operator->name : '';
        }
        //Invoice info
        $output['invoice_no'] = "00" . $rep;
        $output['quotation_no'] = "";
        $output['shipping_address'] = "...";
        //Heading & invoice label, when quotation use the quotation heading.
        $output['invoice_no_prefix'] = $il->invoice_no_prefix;
        $output['invoice_heading'] = $il->invoice_heading;
        $output['invoice_heading'] .= ' ' . $il->invoice_heading_paid;
        
        $output['date_label'] = $il->date_label;
        if (blank($il->date_time_format)) {
            $output['invoice_date'] = $this->format_date(date('Y-m-d H:i:s', time()), true, $business_details);
        } else {
            $output['invoice_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', time()))->format($il->date_time_format);
        }
        \Log::debug("other sale receipt details", [
            "date" => date('Y-m-d H:i:s', time()),
            "invoice_date" => $output['invoice_date'],
        ]);
        if (!empty($il->common_settings['show_due_date'])) {
            $output['due_date_label'] = !empty($il->common_settings['due_date_label']) ? $il->common_settings['due_date_label'] : '';
            $due_date = date('Y-m-d H:i:s', time());
            if (!empty($due_date)) {
                if (blank($il->date_time_format)) {
                    $output['due_date'] = $this->format_date($due_date->toDateTimeString(), true, $business_details);
                } else {
                    $output['due_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', $due_date->toDateTimeString())->format($il->date_time_format);
                }
            }
        }
        $show_currency = true;
        if ($receipt_printer_type == 'printer' && trim($business_details->currency_symbol) != '$') {
            $show_currency = false;
        }
        //Invoice product lines
        $is_lot_number_enabled = $business_details->enable_lot_number;
        $is_product_expiry_enabled = $business_details->enable_product_expiry;
        $output['lines'] = [];
        $sub_total = 0;
        if ($transaction_type == 'sell') {
            $sell_line_relations = ['modifiers', 'sub_unit', 'warranties'];
            if ($is_lot_number_enabled == 1) {
                $sell_line_relations[] = 'lot_details';
            }
            $lines = PumpOperatorOtherSale::whereIn('id',$print_other_sale_ids)->get();
            foreach ($lines as $line) {
                $line->product = Product::where('id', $line->product_id)->first();
                $line->variations = Variation::where('id', $line->product_id)->first();
                $line->variations->product_variation = ProductVariation::where('product_id', $line->product_id)->first();
                $line->product->unit = Unit::where('id', $line->product->unit_id)->first();
                $line->product->brand = Brands::where('id', $line->product->brand_id)->first();
                $line->product->category = Category::where('id', $line->product->category_id)->first();
                $sub_total = $sub_total + $line->sub_total;
            }
            $details = $this->_receiptOtherSaleDetailsSellLines($lines, $il, $business_details);

            $output['lines'] = $details['lines'];
            $output['taxes'] = [];
            foreach ($details['lines'] as $line) {
                if (!empty($line['group_tax_details'])) {
                    foreach ($line['group_tax_details'] as $tax_group_detail) {
                        if (!isset($output['taxes'][$tax_group_detail['name']])) {
                            $output['taxes'][$tax_group_detail['name']] = 0;
                        }
                        $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                    }
                } elseif (!empty($line['tax_unformatted']) && $line['tax_unformatted'] != 0) {
                    if (!isset($output['taxes'][$line['tax_name']])) {
                        $output['taxes'][$line['tax_name']] = 0;
                    }
                    $output['taxes'][$line['tax_name']] += $line['tax_unformatted'];
                }
            }
        }



        //show cat code
        $output['show_cat_code'] = $il->show_cat_code;
        $output['cat_code_label'] = $il->cat_code_label;
        //Subtotal
        $output['subtotal_label'] = $il->sub_total_label . ':';
        $output['subtotal'] = $this->num_f($sub_total, $show_currency, $business_details);

        $subtotal_final = array_sum(
            array_map(function ($value) {
                return isset($value) ? (float)str_replace(',', '', $value) : 0;
            }, array_column($details['lines'], 'sub_total_final'))
        );

        $output['subtotal_final'] = $this->num_f($subtotal_final, false, $business_details);

        $output['subtotal_unformatted'] = $sub_total;
        //Discount
        $output['line_discount_label'] = $invoice_layout->discount_label;
        $output['discount_label'] = $invoice_layout->discount_label;
        $output['discount_label'] .= '';
        $discount = 0;
        $output['discount'] = ($discount != 0) ? $this->num_f($discount, $show_currency, $business_details) : 0;
        //Format tax
        if (!empty($output['taxes'])) {
            foreach ($output['taxes'] as $key => $value) {
                $output['taxes'][$key] = $this->num_f($value, $show_currency, $business_details);
            }
        }
        //Order Tax
        $tax = null;
        $output['tax_label'] = $invoice_layout->tax_label;
        $output['line_tax_label'] = $invoice_layout->tax_label;
        if (!empty($tax) && !empty($tax->name)) {
            $output['tax_label'] .= ' (' . $tax->name . ')';
        }
        $output['tax_label'] .= ':';
        $output['tax'] = 0;
        //Shipping charges
        $output['shipping_charges'] = 0;
        $output['shipping_charges_label'] = trans("sale.shipping_charges");
        //Shipping details
        $output['shipping_details'] = "...";
        $output['shipping_details_label'] = trans("sale.shipping_details");
        //Total
        $output['total_label'] = $invoice_layout->total_label . ':';
        $output['total'] = $this->num_f($sub_total, $show_currency, $business_details);
        //Paid & Amount due, only if final
        $output['total_paid_label'] = $il->paid_label;
        $output['total_due_label'] = $il->total_due_label;
        
        if ($transaction_type == 'sell') {
            $paid_amount = $sub_total;
            $due = 0;
            $output['total_paid'] = ($paid_amount == 0) ? 0 : $this->num_f($paid_amount, $show_currency, $business_details);
            $output['total_due'] = ($due == 0) ? 0 : $this->num_f($due, $show_currency, $business_details);
           
            if ($il->show_previous_bal == 1) {
                $all_due = $this->getContactDue(0);
                if (!empty($all_due)) {
                    $output['all_bal_label'] = $il->prev_bal_label;
                    $output['all_due'] = $this->num_f($all_due, $show_currency, $business_details);
                }
            }
            if($paid_amount > $sub_total){
                $output['paid_greater'] = true;
                $output['due_amount'] = $this->num_f($due, $show_currency, $business_details);
            }else{
                $output['paid_greater'] = false;
                $output['due_amount'] = $this->num_f($due, $show_currency, $business_details);
            }
            //Get payment details
            $output['payments'] = [];
        }
        //Check for barcode
        $output['barcode'] = ($il->show_barcode == 1) ? "00" . $rep : false;
        //Additional notes
        $output['additional_notes'] = "...";
        $output['footer_text'] = $invoice_layout->footer_text;
        //Barcode related information.
        $output['show_barcode'] = !empty($il->show_barcode) ? true : false;
        $output['design'] = $il->design;
        $output['table_tax_headings'] = !empty($il->table_tax_headings) ? array_filter(json_decode($il->table_tax_headings), 'strlen') : null;
        return (object) $output;
    }

    public function getCreditSaleReceiptDetails($daily_voucher_item_ids, $location_id, $invoice_layout, $business_details, $location_details, $receipt_printer_type)
    {
        $il = $invoice_layout;
        $rep = $daily_voucher_item_ids[0];
        
        $transaction_type = "sell";
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
            'reprint' => $rep,
            'tax_amount' => "..."
        ];
        //Display name
        $output['display_name'] = $output['business_name'];
        if (!empty($output['location_name'])) {
            if (!empty($output['display_name'])) {
                $output['display_name'] .= ', ';
            }
            $output['display_name'] .= $output['location_name'];
        }
        $contact_details = ['due_amount' => 0, 'customer_name' => 'Customer', 'sol_with_approval' => false];
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
        $daily_voucher_item = DailyVoucherItem::where('id',$rep)->select('daily_voucher_id')->first();
        $daily_voucher = DailyVoucher::where('id',$daily_voucher_item->daily_voucher_id)->select('customer_id')->first();
        $customer = Contact::find($daily_voucher->customer_id);
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
        if ($il->show_reward_point == 1) {
            $output['customer_rp_label'] = $business_details->rp_name;
            $output['customer_total_rp'] = $customer->total_rp;
        }
        $output['client_id'] = '';
        $output['client_id_label'] = '';
        if ($il->show_client_id == 1) {
            $output['client_id_label'] = !empty($il->client_id_label) ? $il->client_id_label : '';
            $output['client_id'] = !empty($customer->contact_id) ? $customer->contact_id : '';
        }
        //Sales person info
        $output['sales_person'] = '';
        $output['sales_person_label'] = '';
        $pump_operator_id = Auth::user()->pump_operator_id;
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        if ($il->show_sales_person == 1) {
            $output['sales_person_label'] = !empty($il->sales_person_label) ? $il->sales_person_label : '';
            $output['sales_person'] = !empty($pump_operator->name) ? $pump_operator->name : '';
        }
        //Invoice info
        $output['invoice_no'] = "00" . $rep;
        $output['quotation_no'] = "";
        $output['shipping_address'] = "...";
        //Heading & invoice label, when quotation use the quotation heading.
        $output['invoice_no_prefix'] = $il->invoice_no_prefix;
        $output['invoice_heading'] = $il->invoice_heading;
        $output['invoice_heading'] .= ' ' . $il->invoice_heading_paid;
        
        $output['date_label'] = $il->date_label;
        if (blank($il->date_time_format)) {
            $output['invoice_date'] = $this->format_date(date('Y-m-d H:i:s', time()), true, $business_details);
        } else {
            $output['invoice_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', date('Y-m-d H:i:s', time()))->format($il->date_time_format);
        }
        \Log::debug("other sale receipt details", [
            "date" => date('Y-m-d H:i:s', time()),
            "invoice_date" => $output['invoice_date'],
        ]);
        if (!empty($il->common_settings['show_due_date'])) {
            $output['due_date_label'] = !empty($il->common_settings['due_date_label']) ? $il->common_settings['due_date_label'] : '';
            $due_date = date('Y-m-d H:i:s', time());
            if (!empty($due_date)) {
                if (blank($il->date_time_format)) {
                    $output['due_date'] = $this->format_date($due_date->toDateTimeString(), true, $business_details);
                } else {
                    $output['due_date'] = \Carbon::createFromFormat('Y-m-d H:i:s', $due_date->toDateTimeString())->format($il->date_time_format);
                }
            }
        }
        $show_currency = true;
        if ($receipt_printer_type == 'printer' && trim($business_details->currency_symbol) != '$') {
            $show_currency = false;
        }
        //Invoice product lines
        $is_lot_number_enabled = $business_details->enable_lot_number;
        $is_product_expiry_enabled = $business_details->enable_product_expiry;
        $output['lines'] = [];
        $sub_total = 0;
        if ($transaction_type == 'sell') {
            $sell_line_relations = ['modifiers', 'sub_unit', 'warranties'];
            if ($is_lot_number_enabled == 1) {
                $sell_line_relations[] = 'lot_details';
            }
            $lines = DailyVoucherItem::whereIn('id',$daily_voucher_item_ids)->get();
            foreach ($lines as $line) {
                $line->product = Product::where('id', $line->product_id)->first();
                $line->variations = Variation::where('id', $line->product_id)->first();
                $line->variations->product_variation = ProductVariation::where('product_id', $line->product_id)->first();
                $line->product->unit = Unit::where('id', $line->product->unit_id)->first();
                $line->product->brand = Brands::where('id', $line->product->brand_id)->first();
                $line->product->category = Category::where('id', $line->product->category_id)->first();
                $sub_total = $sub_total + $line->sub_total;
            }
            $details = $this->_receiptOtherSaleDetailsSellLines($lines, $il, $business_details);
            $output['lines'] = $details['lines'];
            $output['taxes'] = [];
            foreach ($details['lines'] as $line) {
                if (!empty($line['group_tax_details'])) {
                    foreach ($line['group_tax_details'] as $tax_group_detail) {
                        if (!isset($output['taxes'][$tax_group_detail['name']])) {
                            $output['taxes'][$tax_group_detail['name']] = 0;
                        }
                        $output['taxes'][$tax_group_detail['name']] += $tax_group_detail['calculated_tax'];
                    }
                } elseif (!empty($line['tax_unformatted']) && $line['tax_unformatted'] != 0) {
                    if (!isset($output['taxes'][$line['tax_name']])) {
                        $output['taxes'][$line['tax_name']] = 0;
                    }
                    $output['taxes'][$line['tax_name']] += $line['tax_unformatted'];
                }
            }
        }

        //show cat code
        $output['show_cat_code'] = $il->show_cat_code;
        $output['cat_code_label'] = $il->cat_code_label;
        //Subtotal
        $output['subtotal_label'] = $il->sub_total_label . ':';
        $output['subtotal'] = $this->num_f($sub_total, $show_currency, $business_details);

        $subtotal_final = array_sum(
            array_map(function ($value) {
                return isset($value) ? (float)str_replace(',', '', $value) : 0;
            }, array_column($details['lines'], 'sub_total_final'))
        );

        $output['subtotal_final'] = $this->num_f($subtotal_final, false, $business_details);

        $output['subtotal_unformatted'] = $sub_total;
        //Discount
        $output['line_discount_label'] = $invoice_layout->discount_label;
        $output['discount_label'] = $invoice_layout->discount_label;
        $output['discount_label'] .= '';
        $discount = 0;
        $output['discount'] = ($discount != 0) ? $this->num_f($discount, $show_currency, $business_details) : 0;
        //Format tax
        if (!empty($output['taxes'])) {
            foreach ($output['taxes'] as $key => $value) {
                $output['taxes'][$key] = $this->num_f($value, $show_currency, $business_details);
            }
        }
        //Order Tax
        $tax = null;
        $output['tax_label'] = $invoice_layout->tax_label;
        $output['line_tax_label'] = $invoice_layout->tax_label;
        if (!empty($tax) && !empty($tax->name)) {
            $output['tax_label'] .= ' (' . $tax->name . ')';
        }
        $output['tax_label'] .= ':';
        $output['tax'] = 0;
        //Shipping charges
        $output['shipping_charges'] = 0;
        $output['shipping_charges_label'] = trans("sale.shipping_charges");
        //Shipping details
        $output['shipping_details'] = "...";
        $output['shipping_details_label'] = trans("sale.shipping_details");
        //Total
        $output['total_label'] = $invoice_layout->total_label . ':';
        $output['total'] = $this->num_f($sub_total, $show_currency, $business_details);
        //Paid & Amount due, only if final
        $output['total_paid_label'] = $il->paid_label;
        $output['total_due_label'] = $il->total_due_label;
        
        if ($transaction_type == 'sell') {
            $paid_amount = $sub_total;
            $due = 0;
            $output['total_paid'] = ($paid_amount == 0) ? 0 : $this->num_f($paid_amount, $show_currency, $business_details);
            $output['total_due'] = ($due == 0) ? 0 : $this->num_f($due, $show_currency, $business_details);
           
            if ($il->show_previous_bal == 1) {
                $all_due = $this->getContactDue(0);
                if (!empty($all_due)) {
                    $output['all_bal_label'] = $il->prev_bal_label;
                    $output['all_due'] = $this->num_f($all_due, $show_currency, $business_details);
                }
            }
            if($paid_amount > $sub_total){
                $output['paid_greater'] = true;
                $output['due_amount'] = $this->num_f($due, $show_currency, $business_details);
            }else{
                $output['paid_greater'] = false;
                $output['due_amount'] = $this->num_f($due, $show_currency, $business_details);
            }
            //Get payment details
            $output['payments'] = [];
        }
        //Check for barcode
        $output['barcode'] = ($il->show_barcode == 1) ? "00" . $rep : false;
        //Additional notes
        $output['additional_notes'] = "...";
        $output['footer_text'] = $invoice_layout->footer_text;
        //Barcode related information.
        $output['show_barcode'] = !empty($il->show_barcode) ? true : false;
        $output['design'] = $il->design;
        $output['table_tax_headings'] = !empty($il->table_tax_headings) ? array_filter(json_decode($il->table_tax_headings), 'strlen') : null;
        return (object) $output;
    }

    public function getCustomerDetails($contact_id)
    {
        if (!empty($contact_id)) {
            $customer_id = $contact_id;
        }
        $business_id = request()->session()->get('business.id');
        $query = Contact::leftjoin('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->leftjoin('contact_groups AS cg', 'contacts.customer_group_id', '=', 'cg.id')
            ->where('contacts.business_id', $business_id)
            ->where('contacts.id', $customer_id)
            ->onlyCustomers()
            ->select([
                'contacts.contact_id', 'contacts.name', 'contacts.created_at', 'total_rp', 'cg.name as customer_group', 'sol_with_approval', 'state', 'country', 'landmark', 'mobile', 'contacts.id', 'is_default',
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
        $due = $query->total_invoice - $query->invoice_received + $query->advance_payment;
        $return_due = $query->total_sell_return - $query->sell_return_paid;
        $opening_balance = $query->opening_balance;
        $total_outstanding =  $due -  $return_due + $opening_balance;
        if (empty($total_outstanding)) {
            $total_outstanding = 0.00;
        }
        $total_outstanding = $this->num_f($total_outstanding, false);
        return ['due_amount' => $total_outstanding, 'customer_name' => $query->name, 'sol_with_approval' => $query->sol_with_approval];
    }
    /**
     * Returns each line details for sell invoice display
     *
     * @return array
     */
    protected function _receiptDetailsSellLines($lines, $il, $business_details)
    {
        $is_lot_number_enabled = $business_details->enable_lot_number;
        $is_product_expiry_enabled = $business_details->enable_product_expiry;
        $output_lines = [];
        //$output_taxes = ['taxes' => []];
        $product_custom_fields_settings = !empty($il->product_custom_fields) ? $il->product_custom_fields : [];
        $is_warranty_enabled = !empty($business_details->common_settings['enable_product_warranty']) ? true : false;
        foreach ($lines as $line) {
            $product = $line->product;
            $variation = $line->variations;
            $product_variation = $line->variations->product_variation;
            $unit = $line->product->unit;
            $brand = $line->product->brand;
            $cat = $line->product->category;
            $tax_details = TaxRate::find($line->tax_id);
            $unit_name = !empty($unit->short_name) ? $unit->short_name : '';

            if (!empty($line->sub_unit->short_name)) {
                $unit_name = $line->sub_unit->short_name;
            }
            $line_array = [
                //Field for 1st column
                'name' => $product->name,
                'variation' => (empty($variation->name) || $variation->name == 'DUMMY') ? '' : $variation->name,
                'product_variation' => (empty($product_variation->name) || $product_variation->name == 'DUMMY') ? '' : $product_variation->name,
                //Field for 2nd column
                'quantity' => $this->num_f($line->quantity, false, $business_details, true),
                'units' => $unit_name,
                'unit_price' => $this->num_f($line->unit_price, false, $business_details),
                'tax' => $this->num_f($line->item_tax, false, $business_details),
                'tax_unformatted' => $line->item_tax,
                'tax_name' => !empty($tax_details) ? $tax_details->name : null,
                'tax_percent' => !empty($tax_details) ? $tax_details->amount : null,
                //Field for 3rd column
                'unit_price_inc_tax' => $this->num_f($line->unit_price_inc_tax, false, $business_details),
                'unit_price_exc_tax' => $this->num_f($line->unit_price, false, $business_details),
                'price_exc_tax' => $line->quantity * $line->unit_price,
                'unit_price_before_discount' => $this->num_f($line->unit_price_before_discount, false, $business_details),
                //Fields for 4th column
                'line_total' => $this->num_f($line->unit_price_inc_tax * $line->quantity, false, $business_details),
                'inital_unit_price' => $this->num_f($line->unit_price_inc_tax + $line->line_discount_amount, false, $business_details), 
                'default_sale_price' => $this->num_f($variation->default_sell_price, false, $business_details),
                'sub_total_final' => $this->num_f($line->unit_price, false, $business_details)
            ];
            $temp = [];
            if (!empty($product->product_custom_field1) && in_array('product_custom_field1', $product_custom_fields_settings)) {
                $temp[] = $product->product_custom_field1;
            }
            if (!empty($product->product_custom_field2) && in_array('product_custom_field2', $product_custom_fields_settings)) {
                $temp[] = $product->product_custom_field2;
            }
            if (!empty($product->product_custom_field3) && in_array('product_custom_field3', $product_custom_fields_settings)) {
                $temp[] = $product->product_custom_field3;
            }
            if (!empty($product->product_custom_field4) && in_array('product_custom_field4', $product_custom_fields_settings)) {
                $temp[] = $product->product_custom_field4;
            }
            if (!empty($temp)) {
                $line_array['product_custom_fields'] = implode(',', $temp);
            }
            //Group product taxes by name.
            if (!empty($tax_details)) {
                if ($tax_details->is_tax_group) {
                    $group_tax_details = $this->groupTaxDetails($tax_details, $line->quantity * $line->item_tax);
                    $line_array['group_tax_details'] = $group_tax_details;
                    // foreach ($group_tax_details as $key => $value) {
                    //     if (!isset($output_taxes['taxes'][$key])) {
                    //         $output_taxes['taxes'][$key] = 0;
                    //     }
                    //     $output_taxes['taxes'][$key] += $value;
                    // }
                }
                // else {
                //     $tax_name = $tax_details->name;
                //     if (!isset($output_taxes['taxes'][$tax_name])) {
                //         $output_taxes['taxes'][$tax_name] = 0;
                //     }
                //     $output_taxes['taxes'][$tax_name] += ($line->quantity * $line->item_tax);
                // }
            }
            $line_array['line_discount'] = method_exists($line, 'get_discount_amount') ? $this->num_uf($line->get_discount_amount()) : 0;
            $line_array['line_discount_percentage'] = '';
            if ($line->line_discount_type == 'percentage') {
                $line_array['line_discount_percentage'] .= ' (' . $this->num_uf($line->line_discount_amount) . '%)';
            }
            if ($il->show_brand == 1) {
                $line_array['brand'] = !empty($brand->name) ? $brand->name : '';
            }
            if ($il->show_sku == 1) {
                $line_array['sub_sku'] = !empty($variation->sub_sku) ? $variation->sub_sku : '';
            }
            if ($il->show_image == 1) {
                $media = $variation->media;
                if (count($media)) {
                    $first_img = $media->first();
                    $line_array['image'] = !empty($first_img->display_url) ? $first_img->display_url : asset('/img/default.png');
                } else {
                    $line_array['image'] = $product->image_url;
                }
            }
            if ($il->show_cat_code == 1) {
                $line_array['cat_code'] = !empty($cat->short_code) ? $cat->short_code : '';
            }
            if ($il->show_sale_description == 1) {
                $line_array['sell_line_note'] = !empty($line->sell_line_note) ? $line->sell_line_note : '';
            }
            if ($is_lot_number_enabled == 1 && $il->show_lot == 1) {
                $line_array['lot_number'] = !empty($line->lot_details->lot_number) ? $line->lot_details->lot_number : null;
                $line_array['lot_number_label'] = __('lang_v1.lot');
            }
            if ($is_product_expiry_enabled == 1 && $il->show_expiry == 1) {
                $line_array['product_expiry'] = !empty($line->lot_details->exp_date) ? $this->format_date($line->lot_details->exp_date, false, $business_details) : null;
                $line_array['product_expiry_label'] = __('lang_v1.expiry');
            }
            //Set warranty data if enabled
            if ($is_warranty_enabled && !empty($line->warranties->first())) {
                $warranty = $line->warranties->first();
                if (!empty($il->common_settings['show_warranty_name'])) {
                    $line_array['warranty_name'] = $warranty->name;
                }
                if (!empty($il->common_settings['show_warranty_description'])) {
                    $line_array['warranty_description'] = $warranty->description;
                }
                if (!empty($il->common_settings['show_warranty_exp_date'])) {
                    $line_array['warranty_exp_date'] = $warranty->getEndDate($line->transaction->transaction_date);
                }
            }
            //If modifier is set set modifiers line to parent sell line
            if (!empty($line->modifiers)) {
                foreach ($line->modifiers as $modifier_line) {
                    $product = $modifier_line->product;
                    $variation = $modifier_line->variations;
                    $unit = $modifier_line->product->unit;
                    $brand = $modifier_line->product->brand;
                    $cat = $modifier_line->product->category;
                    $modifier_line_array = [
                        //Field for 1st column
                        'name' => $product->name,
                        'variation' => (empty($variation->name) || $variation->name == 'DUMMY') ? '' : $variation->name,
                        //Field for 2nd column
                        'quantity' => $this->num_f($modifier_line->quantity, false, $business_details),
                        'units' => !empty($unit->short_name) ? $unit->short_name : '',
                        //Field for 3rd column
                        'unit_price_inc_tax' => $this->num_f($modifier_line->unit_price_inc_tax, false, $business_details),
                        'unit_price_exc_tax' => $this->num_f($modifier_line->unit_price, false, $business_details),
                        'price_exc_tax' => $modifier_line->quantity * $modifier_line->unit_price,
                        //Fields for 4th column
                        'line_total' => $this->num_f($modifier_line->unit_price_inc_tax * $line->quantity, false, $business_details),
                    ];
                    if ($il->show_sku == 1) {
                        $modifier_line_array['sub_sku'] = !empty($variation->sub_sku) ? $variation->sub_sku : '';
                    }
                    if ($il->show_cat_code == 1) {
                        $modifier_line_array['cat_code'] = !empty($cat->short_code) ? $cat->short_code : '';
                    }
                    if ($il->show_sale_description == 1) {
                        $modifier_line_array['sell_line_note'] = !empty($line->sell_line_note) ? $line->sell_line_note : '';
                    }
                    $line_array['modifiers'][] = $modifier_line_array;
                }
            }
            $output_lines[] = $line_array;
        }

        return ['lines' => $output_lines];
    }

    protected function _receiptOtherSaleDetailsSellLines($lines, $il, $business_details)
    {
        $is_lot_number_enabled = $business_details->enable_lot_number;
        $is_product_expiry_enabled = $business_details->enable_product_expiry;
        $output_lines = [];
        //$output_taxes = ['taxes' => []];
        $product_custom_fields_settings = !empty($il->product_custom_fields) ? $il->product_custom_fields : [];
        $is_warranty_enabled = !empty($business_details->common_settings['enable_product_warranty']) ? true : false;
        foreach ($lines as $line) {
            $product = $line->product;
            $variation = $line->variations;
            $product_variation = $line->variations->product_variation;
            $unit = $line->product->unit;
            $brand = $line->product->brand;
            $cat = $line->product->category;
            $tax_details = TaxRate::find(0);
            $unit_name = !empty($unit->short_name) ? $unit->short_name : '';
            
            $line_array = [
                //Field for 1st column
                'name' => $product->name,
                'variation' => (empty($variation->name) || $variation->name == 'DUMMY') ? '' : $variation->name,
                'product_variation' => (empty($product_variation->name) || $product_variation->name == 'DUMMY') ? '' : $product_variation->name,
                //Field for 2nd column
                'quantity' => $this->num_f($line->qty, false, $business_details, true),
                'units' => $unit_name,
                'unit_price' => $this->num_f($line->price ?? $line->unit_price, false, $business_details),
                'tax' => 0,
                'tax_unformatted' => 0,
                'tax_name' => !empty($tax_details) ? $tax_details->name : null,
                'tax_percent' => !empty($tax_details) ? $tax_details->amount : null,
                //Field for 3rd column
                'unit_price_inc_tax' => $this->num_f($line->price ?? $line->unit_price, false, $business_details),
                'unit_price_exc_tax' => $this->num_f($line->price ?? $line->unit_price, false, $business_details),
                'price_exc_tax' => $line->qty * $line->price ?? $line->unit_price,
                'unit_price_before_discount' => $this->num_f($line->price ?? $line->unit_price, false, $business_details),
                //Fields for 4th column
                'line_total' => $this->num_f(($line->price ?? $line->unit_price) * $line->qty, false, $business_details),
                'inital_unit_price' => $this->num_f($line->unit_price_inc_tax + $line->line_discount_amount, false, $business_details), 
                'default_sale_price' => $this->num_f($variation->default_sell_price, false, $business_details),
                'sub_total_final' => $this->num_f($line->unit_price, false, $business_details)

            ];

            $temp = [];
            if (!empty($product->product_custom_field1) && in_array('product_custom_field1', $product_custom_fields_settings)) {
                $temp[] = $product->product_custom_field1;
            }
            if (!empty($product->product_custom_field2) && in_array('product_custom_field2', $product_custom_fields_settings)) {
                $temp[] = $product->product_custom_field2;
            }
            if (!empty($product->product_custom_field3) && in_array('product_custom_field3', $product_custom_fields_settings)) {
                $temp[] = $product->product_custom_field3;
            }
            if (!empty($product->product_custom_field4) && in_array('product_custom_field4', $product_custom_fields_settings)) {
                $temp[] = $product->product_custom_field4;
            }
            if (!empty($temp)) {
                $line_array['product_custom_fields'] = implode(',', $temp);
            }
            //Group product taxes by name.
            if (!empty($tax_details)) {
                if ($tax_details->is_tax_group) {
                    $group_tax_details = $this->groupTaxDetails($tax_details, $line->qty * 0);
                    $line_array['group_tax_details'] = $group_tax_details;
                    // foreach ($group_tax_details as $key => $value) {
                    //     if (!isset($output_taxes['taxes'][$key])) {
                    //         $output_taxes['taxes'][$key] = 0;
                    //     }
                    //     $output_taxes['taxes'][$key] += $value;
                    // }
                }
                // else {
                //     $tax_name = $tax_details->name;
                //     if (!isset($output_taxes['taxes'][$tax_name])) {
                //         $output_taxes['taxes'][$tax_name] = 0;
                //     }
                //     $output_taxes['taxes'][$tax_name] += ($line->quantity * $line->item_tax);
                // }
            }
            $line_array['line_discount'] = 0;
            $line_array['line_discount_percentage'] = '';
            if ($il->show_brand == 1) {
                $line_array['brand'] = !empty($brand->name) ? $brand->name : '';
            }
            if ($il->show_sku == 1) {
                $line_array['sub_sku'] = !empty($variation->sub_sku) ? $variation->sub_sku : '';
            }
            if ($il->show_image == 1) {
                $media = $variation->media;
                if (count($media)) {
                    $first_img = $media->first();
                    $line_array['image'] = !empty($first_img->display_url) ? $first_img->display_url : asset('/img/default.png');
                } else {
                    $line_array['image'] = $product->image_url;
                }
            }
            if ($il->show_cat_code == 1) {
                $line_array['cat_code'] = !empty($cat->short_code) ? $cat->short_code : '';
            }
            if ($il->show_sale_description == 1) {
                $line_array['sell_line_note'] = '';
            }
            if ($is_product_expiry_enabled == 1 && $il->show_expiry == 1) {
                $line_array['product_expiry'] = null;
                $line_array['product_expiry_label'] = __('lang_v1.expiry');
            }
            $output_lines[] = $line_array;
        }
        return ['lines' => $output_lines];
    }
    /**
     * Returns each line details for sell return invoice display
     *
     * @return array
     */
    protected function _receiptDetailsSellReturnLines($lines, $il, $business_details)
    {
        $is_lot_number_enabled = $business_details->enable_lot_number;
        $is_product_expiry_enabled = $business_details->enable_product_expiry;
        $output_lines = [];
        $output_taxes = ['taxes' => []];
        foreach ($lines as $line) {
            //Group product taxes by name.
            $tax_details = TaxRate::find($line->tax_id);
            $product = $line->product;
            $variation = $line->variations;
            $unit = $line->product->unit;
            $brand = $line->product->brand;
            $cat = $line->product->category;
            $unit_name = !empty($unit->short_name) ? $unit->short_name : '';
            if (!empty($line->sub_unit->short_name)) {
                $unit_name = $line->sub_unit->short_name;
            }
            $line_array = [
                //Field for 1st column
                'name' => $product->name,
                'variation' => (empty($variation->name) || $variation->name == 'DUMMY') ? '' : $variation->name,
                //Field for 2nd column
                'quantity' => $this->num_f($line->quantity_returned, false, $business_details, true),
                'units' => $unit_name,
                'unit_price' => $this->num_f($line->unit_price, false, $business_details),
                'tax' => $this->num_f($line->item_tax, false, $business_details),
                'tax_name' => !empty($tax_details) ? $tax_details->name : null,
                //Field for 3rd column
                'unit_price_inc_tax' => $this->num_f($line->unit_price_inc_tax, false, $business_details),
                'unit_price_exc_tax' => $this->num_f($line->unit_price, false, $business_details),
                //Fields for 4th column
                'line_total' => $this->num_f($line->unit_price_inc_tax * $line->quantity_returned, false, $business_details),
            ];
            $line_array['line_discount'] = 0;
            //Group product taxes by name.
            if (!empty($tax_details)) {
                if ($tax_details->is_tax_group) {
                    $group_tax_details = $this->groupTaxDetails($tax_details, $line->quantity * $line->item_tax);
                    $line_array['group_tax_details'] = $group_tax_details;
                }
            }
            if ($il->show_brand == 1) {
                $line_array['brand'] = !empty($brand->name) ? $brand->name : '';
            }
            if ($il->show_sku == 1) {
                $line_array['sub_sku'] = !empty($variation->sub_sku) ? $variation->sub_sku : '';
            }
            if ($il->show_cat_code == 1) {
                $line_array['cat_code'] = !empty($cat->short_code) ? $cat->short_code : '';
            }
            if ($il->show_sale_description == 1) {
                $line_array['sell_line_note'] = !empty($line->sell_line_note) ? $line->sell_line_note : '';
            }
            $output_lines[] = $line_array;
        }
        return ['lines' => $output_lines, 'taxes' => $output_taxes];
    }
    /**
     * Gives the invoice number for a Final/Draft invoice
     *
     * @param int $business_id
     * @param string $status
     * @param string $location_id
     *
     * @return string
     */
    public function getInvoiceNumber($business_id, $status, $location_id, $invoice_scheme_id = null)
    {
        if ($status == 'final') {
            if (empty($invoice_scheme_id)) {
                $scheme = $this->getInvoiceScheme($business_id, $location_id);
            } else {
                $scheme = InvoiceScheme::where('business_id', $business_id)
                    ->find($invoice_scheme_id);
            }
            if ($scheme->scheme_type == 'blank') {
                $prefix = $scheme->prefix;
            } else {
                $prefix = date('Y') . '-';
            }
            //Count
            $count = $scheme->start_number + $scheme->invoice_count;
            $count = str_pad($count, $scheme->total_digits, '0', STR_PAD_LEFT);
            //Prefix + count
            $invoice_no = $prefix . $count;
            //Increment the invoice count
            $scheme->invoice_count = $scheme->invoice_count + 1;
            $scheme->save();
            return $invoice_no;
        } else {
            // return str_random(5);
            return \Illuminate\Support\Str::random(5);
        }
    }
    private function getInvoiceScheme($business_id, $location_id)
    {
        $scheme_id = BusinessLocation::where('business_id', $business_id)
            ->where('id', $location_id)
            ->first()
            ->invoice_scheme_id;
        if (!empty($scheme_id) && $scheme_id != 0) {
            $scheme = InvoiceScheme::find($scheme_id);
        }
        //Check if scheme is not found then return default scheme
        if (empty($scheme)) {
            $scheme = InvoiceScheme::where('business_id', $business_id)
                ->where('is_default', 1)
                ->first();
        }
        return $scheme;
    }
    /**
     * Gives the list of products for a purchase transaction
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return array
     */
    public function getPurchaseProducts($business_id, $transaction_id)
    {
        $products = Transaction::join('purchase_lines as pl', 'transactions.id', '=', 'pl.transaction_id')
            ->leftjoin('products as p', 'pl.product_id', '=', 'p.id')
            ->leftjoin('variations as v', 'pl.variation_id', '=', 'v.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.id', $transaction_id)
            ->where('transactions.type', 'purchase')
            ->select('p.id as product_id', 'p.name as product_name', 'v.id as variation_id', 'v.name as variation_name', 'pl.quantity as quantity')
            ->get();
        return $products;
    }
    /**
     * Gives the total cost value for sales
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return object
     */
    public function getSaleCost($business_id, $start_date, $end_date)
    {
        $query = TransactionSellLine::join('transactions as sale', 'transaction_sell_lines.transaction_id', 'sale.id')
            ->join('variations as v', 'transaction_sell_lines.variation_id', 'v.id')
            ->where('sale.business_id', $business_id)
            ->where('sale.type', 'sell')
            ->where(function ($q) {
                $q->whereNull('sale.sub_type')->orWhere('sale.sub_type', 'settlement');
            })
            ->whereDate('sale.transaction_date', '>=', $start_date)
            ->whereDate('sale.transaction_date', '<=', $end_date)
            ->select(DB::raw('SUM(transaction_sell_lines.quantity * v.dpp_inc_tax) AS total_sale_cost'))->first();
        return $query;
    }
    /**
     * Gives the total purchase amount for a business within the date range passed
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return array
     */
    public function getPurchaseTotals($business_id, $start_date = null, $end_date = null, $location_id = null)
    {
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->select(
                'final_total',
                DB::raw("(final_total - tax_amount) as total_exc_tax"),
                DB::raw("SUM((SELECT SUM(tp.amount) FROM transaction_payments as tp WHERE tp.transaction_id=transactions.id)) as total_paid"),
                DB::raw('SUM(total_before_tax) as total_before_tax'),
                'shipping_charges'
            )
            ->groupBy('transactions.id');
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($start_date)) {
            $query->whereDate('transaction_date','>=', $start_date);
        }
        
        if (!empty($end_date)) {
            $query->whereDate('transaction_date','<=', $end_date);
        }
        
        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $purchase_details = $query->get();
        $output['total_purchase_inc_tax'] = $purchase_details->sum('final_total');
        $output['total_purchase_exc_tax'] = $purchase_details->sum('total_before_tax');
        $output['purchase_due'] = $purchase_details->sum('final_total') - $purchase_details->sum('total_paid');
        $output['total_shipping_charges'] = $purchase_details->sum('shipping_charges');
        
        
        return $output;
    }
    public function getPurchaseTotalsAll($business_id, $location_id = null)
    {
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'purchase')
            ->select(
                'final_total',
                DB::raw("(final_total - tax_amount) as total_exc_tax"),
                DB::raw("SUM((SELECT SUM(tp.amount) FROM transaction_payments as tp WHERE tp.transaction_id=transactions.id)) as total_paid"),
                DB::raw('SUM(total_before_tax) as total_before_tax'),
                'shipping_charges'
            )
            ->groupBy('transactions.id');
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $purchase_details = $query->get();
        $output['total_purchase_due'] = $purchase_details->sum('total_paid');
        return $output;
    }
    /**
     * Gives the total sell amount for a business within the date range passed
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return array
     */
    public function getSellTotals($business_id, $start_date = null, $end_date = null, $location_id = null, $created_by = null)
    {
        $query = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where(function ($q) {
                $q->whereNull('transactions.sub_type')->orWhere('transactions.sub_type', 'settlement');
            })
            ->select(
                'transactions.id',
                'final_total',
                DB::raw("(final_total - tax_amount) as total_exc_tax"),
                DB::raw('(SELECT SUM(IF(tp.is_return = 1, -1*tp.amount, tp.amount)) FROM transaction_payments as tp WHERE tp.transaction_id = transactions.id) as total_paid'),
                DB::raw('SUM(total_before_tax) as total_before_tax'),
                'shipping_charges'
            )
            ->groupBy('transactions.id');
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
        }
        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        if (!empty($created_by)) {
            $query->where('transactions.created_by', $created_by);
        }
        $sell_details = $query->get();
        $output['total_sell_inc_tax'] = $sell_details->sum('final_total');
        //$output['total_sell_exc_tax'] = $sell_details->sum('total_exc_tax');
        $output['total_sell_exc_tax'] = $sell_details->sum('total_before_tax');
        $output['invoice_due'] = $sell_details->sum('final_total') - $sell_details->sum('total_paid');
        $output['total_shipping_charges'] = $sell_details->sum('shipping_charges');
        return $output;
    }
    public function getSellTotalsAll($business_id, $location_id = null, $created_by = null)
    {
        $query = Transaction::where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->where(function ($q) {
                $q->whereNull('transactions.sub_type')->orWhere('transactions.sub_type', 'settlement');
            })
            ->select(
                'transactions.id',
                'final_total',
                DB::raw("(final_total - tax_amount) as total_exc_tax"),
                DB::raw('(SELECT SUM(IF(tp.is_return = 1, -1*tp.amount, tp.amount)) FROM transaction_payments as tp WHERE tp.transaction_id = transactions.id) as total_paid'),
                DB::raw('SUM(total_before_tax) as total_before_tax'),
                'shipping_charges'
            )
            ->groupBy('transactions.id');
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        if (!empty($created_by)) {
            $query->where('transactions.created_by', $created_by);
        }
        $sell_details = $query->get();
        $output['total_sell_due'] = $sell_details->sum('total_paid');
        return $output;
    }
    /**
     * Gives the total sell amount for a business within the date range passed for customer
     *
     * @param int $business_id
     * @param int $transaction_id
     *
     * @return array
     */
    public function getPurchaseTotalsForCustomer($contact_id, $start_date = null, $end_date = null, $location_id = null, $created_by = null)
    {
        $query = Transaction::leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->where('contacts.contact_id', $contact_id)
            ->where('transactions.type', 'sell') //sell by company is purchase by customer
            ->where('transactions.status', 'final')
            ->select(
                'transactions.id',
                'final_total',
                DB::raw("(final_total - tax_amount) as total_exc_tax"),
                DB::raw('(SELECT SUM(IF(tp.is_return = 1, -1*tp.amount, tp.amount)) FROM transaction_payments as tp WHERE tp.transaction_id = transactions.id) as total_paid'),
                DB::raw('SUM(total_before_tax) as total_before_tax'),
                'shipping_charges'
            )
            ->groupBy('transactions.id');
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
        }
        $purchase_details = $query->get();
        $output['total_purchase_inc_tax'] = $purchase_details->sum('final_total');
        //$output['total_sell_exc_tax'] = $purchase_details->sum('total_exc_tax');
        // $output['total_sell_exc_tax'] = $purchase_details->sum('total_before_tax');
        $output['purchase_dues'] = $purchase_details->sum('final_total') - $purchase_details->sum('total_paid');
        $output['total_paids'] =  $purchase_details->sum('total_paid');
        $output['total_shipping_charges'] = $purchase_details->sum('shipping_charges');
        return $output;
    }
    public function getSellTotalsByPaymentType($business_id, $start_date = null, $end_date = null, $location_id = null, $type)
    {
        $payment_method_Cash = PaymentMethod::where('business_id', $business_id)->where('name', 'Cash')->select('id')->first();
        $payment_method_Cheques = PaymentMethod::where('business_id', $business_id)->where('name', 'Cheques')->select('id')->first();
        $payment_method_Cards = PaymentMethod::where('business_id', $business_id)->where('name', 'Cards')->select('id')->first();
        $payment_method_Bank_transfer = PaymentMethod::where('business_id', $business_id)->where('name', 'Bank transfer')->select('id')->first();
        $payment_method_Other = PaymentMethod::where('business_id', $business_id)->where('name', 'Other')->select('id')->first();
        $payment_method_Credit_sales = PaymentMethod::where('business_id', $business_id)->where('name', 'Credit-Sales')->select('id')->first();
        $payment_method_Credit_purchases = PaymentMethod::where('business_id', $business_id)->where('name', 'Credit Purchases')->select('id')->first();
        if ($type == 1) {
            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                // ->whereBetween('transactions.transaction_date', [$start_date, $end_date])
                ->select(
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cash->id . ' ,transaction_payments.amount, 0))  as total_cash'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cheques->id . ' , transaction_payments.amount, 0))  as total_cheques'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cards->id . ', transaction_payments.amount, 0))   as total_cards'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Bank_transfer->id . ', transaction_payments.amount, 0))   as total_bank_transfer'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Other->id . ', transaction_payments.amount, 0))   as total_other'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_sales->id . ', transaction_payments.amount, 0))   as total_c_sale'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_purchases->id . ', transaction_payments.amount, 0))   as total_c_purchase'),
                    DB::raw('SUM(IF(payment_status= "paid", transaction_payments.amount, 0))  as total_payment'),
                    DB::raw('(SELECT SUM(IF(tp.is_return = 1, -1*tp.amount, tp.amount)) FROM transaction_payments as tp WHERE tp.transaction_id = transactions.id) as total_refund'),
                    DB::raw('(SELECT SUM(tp.final_total) FROM transactions as tp WHERE tp.type = "sell") as total_sales')
                    // DB::raw('(SELECT SUM(IF(transaction_payments.method = '.$payment_method_Cash->id .', transaction_payments.amount, transaction_payments.amount))) as total_cash'),
                    // DB::raw('SUM(total_before_tax) as total_before_tax'),
                );
            // ->groupBy('transactions.id');
            // //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            if (!empty($start_date)) {
                $query->whereDate('transactions.transaction_date', '>=', $start_date);
            }
            if (!empty($end_date)) {
                $query->whereDate('transactions.transaction_date', '<=', $end_date);
            }
            $data = $query->get();
        }
        if ($type == 3) {
            $query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.status', 'final')
                ->where('is_return', '1')
                // ->whereBetween('transactions.transaction_date', [$start_date, $end_date])
                ->select(
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cash->id . ', transaction_payments.amount ,0 )) as total_cash'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cheques->id . ',transaction_payments.amount ,0)) as total_cheques'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cards->id . ', transaction_payments.amount ,0  )) as total_cards'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Bank_transfer->id . ', transaction_payments.amount, 0)) as total_bank_transfer'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Other->id . ' , transaction_payments.amount,0)) as total_other'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_sales->id . ', transaction_payments.amount  ,0)) as total_c_sale'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_purchases->id . ', transaction_payments.amount, 0 )) as total_c_purchase'),
                    DB::raw('SUM(IF(payment_status= "paid", transaction_payments.amount, 0)) as total_payment'),
                    DB::raw('(SELECT SUM(IF(tp.is_return = 1, -1*tp.amount, tp.amount)) FROM transaction_payments as tp WHERE tp.transaction_id = transactions.id) as total_refund'),
                    DB::raw('(SELECT SUM(tp.final_total) FROM transactions as tp WHERE tp.type = "sell") as total_sales')
                    // DB::raw('(SELECT SUM(IF(transaction_payments.method = '.$payment_method_Cash->id .', transaction_payments.amount, transaction_payments.amount))) as total_cash'),
                    // DB::raw('SUM(total_before_tax) as total_before_tax'),
                )
                ->groupBy('transactions.id');
            // //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            if (!empty($start_date)) {
                $query->whereDate('transactions.transaction_date', '>=', $start_date);
            }
            if (!empty($end_date)) {
                $query->whereDate('transactions.transaction_date', '<=', $end_date);
            }
            $data = $query->get();
        }
        if ($type == 4) {
            $query = Transaction::where('transactions.business_id', $business_id)
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->where('type', 'purchase')
                ->select(
                    'final_total',
                    DB::raw("(final_total - tax_amount) as total_exc_tax"),
                    DB::raw("SUM((SELECT SUM(tp.amount) FROM transaction_payments as tp WHERE tp.transaction_id=transactions.id)) as total_paid"),
                    DB::raw('SUM(total_before_tax) as total_before_tax'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cash->id . ', transaction_payments.amount ,0 )) as total_cash'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cheques->id . ',transaction_payments.amount ,0)) as total_cheques'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cards->id . ', transaction_payments.amount ,0  )) as total_cards'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Bank_transfer->id . ', transaction_payments.amount, 0)) as total_bank_transfer'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Other->id . ' , transaction_payments.amount,0)) as total_other'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_sales->id . ', transaction_payments.amount  ,0)) as total_c_sale'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_purchases->id . ', transaction_payments.amount, 0 )) as total_c_purchase'),
                    DB::raw('SUM(IF(payment_status= "paid", transaction_payments.amount, 0)) as total_payment'),
                    DB::raw('(SELECT SUM(IF(transaction_payments.is_return = "1", -1*transaction_payments.amount, 0)) FROM transaction_payments WHERE transaction_payments.transaction_id = transactions.id) as total_refund'),
                    DB::raw('(SELECT SUM(tp.final_total) FROM transactions as tp WHERE tp.type = "purchase") as total_purchase')
                );
            // ->groupBy('transactions.id');
            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            if (empty($start_date) && !empty($end_date)) {
                $query->whereDate('transaction_date', '<=', $end_date);
            }
            //Filter by the location
            if (!empty($location_id)) {
                $query->where('transactions.location_id', $location_id);
            }
            $data = $query->get();
        }
        if ($type == 5) {
            $query = Transaction::where('transactions.business_id', $business_id)
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->where('type', 'purchase')
                ->where('is_return', '1')
                ->select(
                    'final_total',
                    DB::raw("(final_total - tax_amount) as total_exc_tax"),
                    DB::raw("SUM((SELECT SUM(tp.amount) FROM transaction_payments as tp WHERE tp.transaction_id=transactions.id)) as total_paid"),
                    DB::raw('SUM(total_before_tax) as total_before_tax'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cash->id . ', transaction_payments.amount ,0 )) as total_cash'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cheques->id . ',transaction_payments.amount ,0)) as total_cheques'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cards->id . ', transaction_payments.amount ,0  )) as total_cards'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Bank_transfer->id . ', transaction_payments.amount, 0)) as total_bank_transfer'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Other->id . ' , transaction_payments.amount,0)) as total_other'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_sales->id . ', transaction_payments.amount  ,0)) as total_c_sale'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_purchases->id . ', transaction_payments.amount, 0 )) as total_c_purchase'),
                    DB::raw('SUM(IF(payment_status= "paid", transaction_payments.amount, 0)) as total_payment'),
                    DB::raw('(SELECT SUM(IF(transaction_payments.is_return = "1", -1*transaction_payments.amount, 0)) FROM transaction_payments WHERE transaction_payments.transaction_id = transactions.id) as total_refund'),
                    DB::raw('(SELECT SUM(tp.final_total) FROM transactions as tp WHERE tp.type = "purchase") as total_purchase')
                );
            // ->groupBy('transactions.id');
            //Check for permitted locations of a user
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            if (empty($start_date) && !empty($end_date)) {
                $query->whereDate('transaction_date', '<=', $end_date);
            }
            //Filter by the location
            if (!empty($location_id)) {
                $query->where('transactions.location_id', $location_id);
            }
            $data = $query->get();
        }
        if ($type == 6) {
            $query = Transaction::leftjoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
                ->leftJoin('transaction_payments', 'transactions.id', '=', 'transaction_payments.transaction_id')
                ->where('transactions.business_id', $business_id)
                ->where('type', 'expense')
                ->select(
                    'final_total',
                    DB::raw("(final_total - tax_amount) as total_exc_tax"),
                    DB::raw("SUM((SELECT SUM(tp.amount) FROM transaction_payments as tp WHERE tp.transaction_id=transactions.id)) as total_paid"),
                    DB::raw('SUM(total_before_tax) as total_before_tax'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cash->id . ', transaction_payments.amount ,0 )) as total_cash'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cheques->id . ',transaction_payments.amount ,0)) as total_cheques'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Cards->id . ', transaction_payments.amount ,0  )) as total_cards'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Bank_transfer->id . ', transaction_payments.amount, 0)) as total_bank_transfer'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Other->id . ' , transaction_payments.amount,0)) as total_other'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_sales->id . ', transaction_payments.amount  ,0)) as total_c_sale'),
                    DB::raw('SUM(IF(transaction_payments.method = ' . $payment_method_Credit_purchases->id . ', transaction_payments.amount, 0 )) as total_c_purchase'),
                    DB::raw('SUM(IF(payment_status= "paid", transaction_payments.amount, 0)) as total_payment'),
                    // DB::raw('(SELECT SUM(IF(transaction_payments.is_return = "1", -1*transaction_payments.amount, 0)) FROM transaction_payments WHERE transaction_payments.transaction_id = transactions.id) as total_refund'),
                    DB::raw('(SELECT SUM(tp.final_total) FROM transactions as tp WHERE tp.type = "expense") as total_expense')
                );
            // ->where('payment_status', 'paid');
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $query->whereIn('transactions.location_id', $permitted_locations);
            }
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereBetween(DB::raw('date(transaction_date)'), [
                    $start_date,
                    $end_date
                ]);
            }
            $data = $query->get();
        }
        if ($type == 2) {
            $data = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
                ->leftJoin('transaction_payments as tp', 'transactions.id', '=', 'tp.transaction_id')
                ->join(
                    'business_locations AS bl',
                    'transactions.location_id',
                    '=',
                    'bl.id'
                )
                ->where('transactions.business_id', $business_id)
                ->where('transactions.type', 'sell')
                ->where('transactions.payment_status', 'final')
                ->whereBetween('transactions.transaction_date', [date($start_date), date($end_date)])
                // ->whereDate('transactions.transaction_date', '<=',$end_date)
                ->select(
                    DB::raw('SUM(tp.amount) as today_settled'),
                    DB::raw('SUM(tp.amount) as today_due'),
                    DB::raw('SUM(IF(transactions.transaction_date < ' . date($start_date) . ', tp.amount, 0)) as opening_due')
                )->first();
        }
        return $data;
    }
    public function getProfitLossDetails($business_id, $location_id, $start_date, $end_date, $user_id = null)
    {
        //For Opening stock date should be 1 day before
        $day_before_start_date = \Carbon::createFromFormat('Y-m-d', $start_date)->subDay()->format('Y-m-d');
        $filters = ['user_id' => $user_id];
        //Get Opening stock
        $opening_stock = $this->getOpeningClosingStock($business_id, $day_before_start_date, $location_id, true, false, $filters);
        $opening_stock_by_sp = $this->getOpeningClosingStock($business_id, $day_before_start_date, $location_id, true, true, $filters);
        //Get Closing stock
        $closing_stock = $this->getOpeningClosingStock(
            $business_id,
            $end_date,
            $location_id,
            false,
            false,
            $filters
        );
        $closing_stock_by_sp = $this->getOpeningClosingStock(
                $business_id,
                $end_date,
                $location_id,
                false,
                true,
                $filters
            );
        //Get Purchase details
        $purchase_details = $this->getPurchaseTotals(
            $business_id,
            $start_date,
            $end_date,
            $location_id,
            $user_id
        );
        //Get Sell details
        $sell_details = $this->getSellTotals(
            $business_id,
            $start_date,
            $end_date,
            $location_id,
            $user_id
        );
        $transaction_types = [
            'purchase_return', 'sell_return', 'expense', 'stock_adjustment', 'sell_transfer', 'purchase', 'sell'
        ];
        $transaction_totals = $this->getTransactionTotals(
            $business_id,
            $transaction_types,
            $start_date,
            $end_date,
            $location_id,
            $user_id
        );
        $gross_profit = $this->getGrossProfit(
            $business_id,
            $start_date,
            $end_date,
            $location_id,
            $user_id
        );
        // dd($transaction_totals);
        $data['total_purchase_shipping_charge'] = !empty($purchase_details['total_shipping_charges']) ? $purchase_details['total_shipping_charges'] : 0;
        $data['total_sell_shipping_charge'] = !empty($sell_details['total_shipping_charges']) ? $sell_details['total_shipping_charges'] : 0;
        //Shipping
        $data['total_transfer_shipping_charges'] = !empty($transaction_totals['total_transfer_shipping_charges']) ? $transaction_totals['total_transfer_shipping_charges'] : 0;
        //Discounts
        $total_purchase_discount = $transaction_totals['total_purchase_discount'];
        $total_sell_discount = $transaction_totals['total_sell_discount'];
        $total_reward_amount = $transaction_totals['total_reward_amount'];
        $total_sell_round_off = $transaction_totals['settlement_expense'];
        //Stocks
        $data['opening_stock'] = !empty($opening_stock) ? $opening_stock : 0;
        $data['closing_stock'] = !empty($closing_stock) ? $closing_stock : 0;
        $data['opening_stock_by_sp'] = !empty($opening_stock_by_sp) ? $opening_stock_by_sp : 0;
        $data['closing_stock_by_sp'] = !empty($closing_stock_by_sp) ? $closing_stock_by_sp : 0;
        //Purchase
        $data['total_purchase'] = !empty($purchase_details['total_purchase_exc_tax']) ? $purchase_details['total_purchase_exc_tax'] : 0;
        $data['total_purchase_discount'] = !empty($total_purchase_discount) ? $total_purchase_discount : 0;
        $data['total_purchase_return'] = $transaction_totals['total_purchase_return_exc_tax'];
        //Sales
        $data['total_sell'] = !empty($sell_details['total_sell_exc_tax']) ? $sell_details['total_sell_exc_tax'] : 0;
        $data['total_sell_discount'] = !empty($total_sell_discount) ? $total_sell_discount : 0;
        $data['total_sell_return'] = $transaction_totals['total_sell_return_exc_tax'];
        $data['total_sell_round_off'] = !empty($total_sell_round_off) ? $total_sell_round_off : 0;
        //Expense
        $data['total_expense'] =  $transaction_totals['total_expense'];
        //Stock adjustments
        $data['total_adjustment'] = $transaction_totals['total_adjustment'];
        $data['total_recovered'] = $transaction_totals['total_recovered'];
        // $data['closing_stock'] = $data['closing_stock'] - $data['total_adjustment'];
        $data['total_reward_amount'] = !empty($total_reward_amount) ? $total_reward_amount : 0;
        $moduleUtil = new ModuleUtil();
        $module_parameters = [
            'business_id' => $business_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'location_id' => $location_id,
            'user_id' => $user_id
        ];
        $modules_data = $moduleUtil->getModuleData('profitLossReportData', $module_parameters);
        $data['left_side_module_data'] = [];
        $data['right_side_module_data'] = [];
        $module_total = 0;
        if (!empty($modules_data)) {
            foreach ($modules_data as $module_data) {
                if (!empty($module_data[0])) {
                    foreach ($module_data[0] as $array) {
                        $data['left_side_module_data'][] = $array;
                        if (!empty($array['add_to_net_profit'])) {
                            $module_total -= $array['value'];
                        }
                    }
                }
                if (!empty($module_data[1])) {
                    foreach ($module_data[1] as $array) {
                        $data['right_side_module_data'][] = $array;
                        if (!empty($array['add_to_net_profit'])) {
                            $module_total += $array['value'];
                        }
                    }
                }
            }
        }
        // $data['net_profit'] = $module_total + $data['total_sell']
        //                         + $data['closing_stock']
        //                         - $data['total_purchase']
        //                         - $data['total_sell_discount']
        //                         + $data['total_sell_round_off']
        //                         - $data['total_reward_amount']
        //                         - $data['opening_stock']
        //                         - $data['total_expense']
        //                         + $data['total_recovered']
        //                         - $data['total_transfer_shipping_charges']
        //                         - $data['total_purchase_shipping_charge']
        //                         + $data['total_sell_shipping_charge']
        //                         + $data['total_purchase_discount']
        //                         + $data['total_purchase_return']
        //                         - $data['total_sell_return'];
        $data['net_profit'] = $module_total + $gross_profit
                                + ($data['total_sell_round_off'] + $data['total_recovered'] + $data['total_sell_shipping_charge'] + $data['total_purchase_discount']
                                ) - ( $data['total_reward_amount'] + $data['total_expense'] + $data['total_adjustment'] + $data['total_transfer_shipping_charges'] + $data['total_purchase_shipping_charge']
                                );
        //get gross profit from Project Module
        $module_parameters = [
            'business_id' => $business_id,
            'start_date' => $start_date,
            'end_date' => $end_date,
            'location_id' => $location_id
        ];
        $project_module_data = $moduleUtil->getModuleData('grossProfit', $module_parameters);
        if (!empty($project_module_data['Project']['gross_profit'])) {
            $gross_profit = $gross_profit + $project_module_data['Project']['gross_profit'];
            $data['gross_profit_label'] = __('project::lang.project_invoice');
        }
        $data['gross_profit'] = $gross_profit;
        //get sub type for total sales
        $sales_by_subtype = Transaction::where('business_id', $business_id)
            ->where('type', 'sell')
            ->where('status', 'final');
        if (!empty($start_date) && !empty($end_date)) {
            if ($start_date == $end_date) {
                $sales_by_subtype->whereDate('transaction_date', $end_date);
            } else {
                $sales_by_subtype->whereBetween(DB::raw('transaction_date'), [$start_date, $end_date]);
            }
        }
        $sales_by_subtype = $sales_by_subtype->select(DB::raw('SUM(total_before_tax) as total_before_tax'), 'sub_type')
            ->whereNotNull('sub_type')
            ->groupBy('transactions.sub_type')
            ->get();
        $data['total_sell_by_subtype'] = $sales_by_subtype;
        return $data;
    }
    /**
     * Gives the total input tax for a business within the date range passed
     *
     * @param int $business_id
     * @param string $start_date default null
     * @param string $end_date default null
     *
     * @return float
     */
    public function getInputTax($business_id, $start_date = null, $end_date = null, $location_id = null)
    {
        //Calculate purchase taxes
        $query1 = Transaction::where('transactions.business_id', $business_id)

                ->where('transactions.tax_amount' ,'>',0)
                
                // ->where('transactions.is_vat' ,1)
                
                ->whereIn('transactions.type',['purchase'])
                
                 ->whereIn('transactions.status',array('received','final'))
                
                ->select(
                    'transactions.*'
                );
       
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query1->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query1->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (!empty($location_id)) {
            $query1->where('transactions.location_id', $location_id);
        }
        $output['total_tax'] = $query1->sum('tax_amount');
        return $output;
    }
    /**
     * Gives the total output tax for a business within the date range passed
     *
     * @param int $business_id
     * @param string $start_date default null
     * @param string $end_date default null
     *
     * @return float
     */
    public function getOutputTax($business_id, $start_date = null, $end_date = null, $location_id = null)
    {
        //Calculate sell taxes
        $query1 = Transaction::where('transactions.business_id', $business_id)

                ->where('transactions.tax_amount' ,'>',0)
                
                ->whereIn('transactions.type',['sell'])
                
                 ->whereIn('transactions.status',array('received','final'))
                
                ->select(
                    'transactions.*'
                );
       
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query1->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query1->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (!empty($location_id)) {
            $query1->where('transactions.location_id', $location_id);
        }
        $output['total_tax'] = $query1->sum('tax_amount');
        return $output;
    }
    /**
     * Gives the total expense tax for a business within the date range passed
     *
     * @param int $business_id
     * @param string $start_date default null
     * @param string $end_date default null
     *
     * @return float
     */
    public function getExpenseTax($business_id, $start_date = null, $end_date = null, $location_id = null)
    {
        $query1 = Transaction::where('transactions.business_id', $business_id)

                ->where('transactions.tax_amount' ,'>',0)
                
                // ->where('transactions.is_vat' ,1)
                
                 ->whereIn('transactions.status',array('received','final'))
                
                ->whereIn('transactions.type',['expense'])
                
                ->select(
                    'transactions.*'
                );
       
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query1->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query1->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (!empty($location_id)) {
            $query1->where('transactions.location_id', $location_id);
        }
        $output['total_tax'] = $query1->sum('tax_amount');
        return $output;
    }
    /**
     * Gives total sells of last 30 days day-wise
     *
     * @param int $business_id
     * @param array $filters
     *
     * @return Obj
     */
    public function getSellsLast30Days($business_id, $group_by_location = false)
    {
        $query = Transaction::leftjoin('transactions as SR', function ($join) {
            $join->on('SR.return_parent_id', '=', 'transactions.id')
                ->where('SR.type', 'sell_return');
        })
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->whereBetween(DB::raw('date(transactions.transaction_date)'), [\Carbon::now()->subDays(30), \Carbon::now()]);
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        $query->select(
            DB::raw("DATE_FORMAT(transactions.transaction_date, '%Y-%m-%d') as date"),
            DB::raw("SUM( transactions.final_total - COALESCE(SR.final_total, 0) ) as total_sells")
        )
            ->groupBy(DB::raw('Date(transactions.transaction_date)'));
        if ($group_by_location) {
            $query->addSelect('transactions.location_id');
            $query->groupBy('transactions.location_id');
        }
        $sells = $query->get();
        if (!$group_by_location) {
            $sells = $sells->pluck('total_sells', 'date');
        }
        return $sells;
    }
    /**
     * Gives total sells of last 30 days day-wise
     *
     * @param int $business_id
     * @param array $filters
     *
     * @return Obj
     */
    public function getCreditSells($business_id, $group_by_location = false, $start_date, $end_date, $location_id = null)
    {
        $query = Transaction::leftjoin('transactions as SR', function ($join) {
            $join->on('SR.return_parent_id', '=', 'transactions.id')
                ->where('SR.type', 'sell_return');
        })
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.is_credit_sale', '1')
            ->where('transactions.status', 'final');
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date);
            $query->whereDate('transactions.transaction_date', '<', $end_date);
        }
        $query->select(
            DB::raw("DATE_FORMAT(transactions.transaction_date, '%Y-%m-%d') as date"),
            DB::raw("SUM( transactions.final_total - COALESCE(SR.final_total, 0) ) as total_sells"),
            DB::raw('SUM(IF( transactions.payment_status = "paid", transactions.final_total, 0))as total_paid'),
            DB::raw('SUM(IF( transactions.payment_status = "due", transactions.final_total, 0))as total_due')
        )
            ->groupBy(DB::raw('Date(transactions.transaction_date)'));
        if ($group_by_location) {
            $query->addSelect('transactions.location_id');
            $query->groupBy('transactions.location_id');
        }
        $sells = $query->get();
        if (!$group_by_location) {
            $sells = $sells->pluck('total_sells', 'date');
        }
        return $sells;
    }
    /**
     * Gives total purchase of last 30 days day-wise for customer
     *
     * @param int $contact_id
     * @param array $filters
     *
     * @return Obj
     */
    public function getPurchaseLast30DaysForCustomer($contact_id, $group_by_location = false)
    {
        $query = Transaction::leftjoin('transactions as SR', function ($join) {
            $join->on('SR.return_parent_id', '=', 'transactions.id')
                ->where('SR.type', 'sell_return');
        })->leftjoin('contacts', 'transactions.contact_id', 'contacts.id')
            ->where('contacts.contact_id', $contact_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->whereBetween(DB::raw('date(transactions.transaction_date)'), [\Carbon::now()->subDays(30), \Carbon::now()]);
        //Check for permitted locations of a user
        $query->select(
            DB::raw("DATE_FORMAT(transactions.transaction_date, '%Y-%m-%d') as date"),
            DB::raw("SUM( transactions.final_total - COALESCE(SR.final_total, 0) ) as total_purchase")
        )
            ->groupBy(DB::raw('Date(transactions.transaction_date)'));
        if ($group_by_location) {
            $query->addSelect('transactions.location_id');
            $query->groupBy('transactions.location_id');
        }
        $purchase = $query->get();
        if (!$group_by_location) {
            $purchase = $purchase->pluck('total_purchase', 'date');
        }
        return $purchase;
    }
    /**
     * Gives total sells of current FY month-wise
     *
     * @param int $business_id
     * @param string $start
     * @param string $end
     *
     * @return Obj
     */
    public function getSellsCurrentFy($business_id, $start, $end, $group_by_location = false)
    {
        $query = Transaction::leftjoin('transactions as SR', function ($join) {
            $join->on('SR.return_parent_id', '=', 'transactions.id')
                ->where('SR.type', 'sell_return');
        })
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start, $end]);
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        $query->groupBy(DB::raw("DATE_FORMAT(transactions.transaction_date, '%Y-%m')"))
            ->select(
                DB::raw("DATE_FORMAT(transactions.transaction_date, '%m-%Y') as yearmonth"),
                DB::raw("SUM( transactions.final_total - COALESCE(SR.final_total, 0)) as total_sells")
            );
        if ($group_by_location) {
            $query->addSelect('transactions.location_id');
            $query->groupBy('transactions.location_id');
        }
        $sells = $query->get();
        if (!$group_by_location) {
            $sells = $sells->pluck('total_sells', 'yearmonth');
        }
        return $sells;
    }
    /**
     * Retrives expense report
     *
     * @param int $business_id
     * @param array $filters
     * @param string $type = by_category (by_category or total)
     *
     * @return Obj
     */
    public function getExpenseReport(
        $business_id,
        $filters = [],
        $type = 'by_category'
    ) {
        $query = Transaction::leftjoin('expense_categories AS ec', 'transactions.expense_category_id', '=', 'ec.id')
            ->where('transactions.business_id', $business_id)
            ->where('type', 'expense');
        // ->where('payment_status', 'paid');
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($filters['location_id'])) {
            $query->where('transactions.location_id', $filters['location_id']);
        }
        if (!empty($filters['expense_for'])) {
            $query->where('transactions.expense_for', $filters['expense_for']);
        }
        if (!empty($filters['category'])) {
            $query->where('ec.id', $filters['category']);
        }
        if (!empty($filters['start_date']) && !empty($filters['end_date'])) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [
                $filters['start_date'],
                $filters['end_date']
            ]);
        }
        //Check tht type of report and return data accordingly
        if ($type == 'by_category') {
            $expenses = $query->select(
                DB::raw("SUM( final_total ) as total_expense"),
                'ec.name as category'
            )
                ->groupBy('expense_category_id')
                ->get();
        } elseif ($type == 'total') {
            $expenses = $query->select(
                DB::raw("SUM( final_total ) as total_expense")
            )
                ->first();
        }
        return $expenses;
    }
    /**
     * Get total paid amount for a transaction
     *
     * @param int $transaction_id
     *
     * @return int
     */
    public function getTotalPaid($transaction_id)
    {
        $total_paid = TransactionPayment::where('transaction_id', $transaction_id)
            // ->where('method', '!=', 'credit_sale')
            ->whereNull('deleted_at')
            ->select(DB::raw('SUM(IF(deleted_at IS NULL, IF(is_return = 0, amount, -amount), 0)) as total_paid'))
            ->first()
            ->total_paid;
        return $total_paid ?? 0;
    }
    /**
     * Calculates the payment status and returns back.
     *
     * @param int $transaction_id
     * @param float $final_amount = null
     *
     * @return string
     */
    public function calculatePaymentStatus($transaction_id, $final_amount = null,$amount =null)
    {
        if($amount!=null){
            $total_paid=$amount;
        }else{
            $total_paid = $this->getTotalPaid($transaction_id);
        }
        // dd( $total_paid);
        if (is_null($final_amount)) {
            $final_amount = Transaction::find($transaction_id)->final_total;
        }
        $status = 'due';
  
        if ($final_amount <= $total_paid) {
            $status = 'paid';
        } elseif ($total_paid > 0 && $final_amount > $total_paid) {
            $status = 'partial';
        }
            //   dd($status);
        return $status;
    }
    /**
     * Update the payment status for purchase or sell transactions. Returns
     * the status
     *
     * @param int $transaction_id
     *
     * @return string
     */
      public function updatePaymentStatus00($transaction_id, $final_amount = null, $paymentMethod= null,$amount=null)
    {
        $status = $this->calculatePaymentStatus($transaction_id, $final_amount,$amount);
        // dd($status);
        $transaction = Transaction::find($transaction_id);
        if ($transaction->type == 'sell' || ($transaction->type == 'settlement' && $transaction->sub_type == 'credit_sale')) {
            if ($status == 'due' || $status == 'partial') {
                Transaction::where('id', $transaction_id)
                    ->update(['is_credit_sale' =>  1]);
            }
        }
        if ($transaction->is_pos_return == 1) {
            $status = 'paid';
        }
        if($paymentMethod="credit_expense"){
            $status ='due';
        }
        
        Transaction::where('id', $transaction_id)
            ->update(['payment_status' => $status]);
            
        $updatedTransaction = Transaction::find($transaction_id);
        $databaseName = DB::connection()->getDatabaseName();
    
        return $status;
    }
    
    public function updatePaymentStatus($transaction_id, $final_amount = null, $paymentMethod= null,$amount = null)
    {
        // dd($amount);
        $status = $this->calculatePaymentStatus($transaction_id, $final_amount,$amount);
        $transaction = Transaction::find($transaction_id);
        if ($transaction->type == 'sell' || ($transaction->type == 'settlement' && $transaction->sub_type == 'credit_sale')) {
            if ($status == 'due' || $status == 'partial') {
                $transaction->is_credit_sale = 1; // Correctly set the model property
                $transaction->save(); // Persist the changes to the database
            }
        }
        if ($transaction->is_pos_return == 1) {
            $status = 'paid';
        }
        
        if($paymentMethod === "credit_expense"){
           
            $status ='due';
        }

        // if($status == 'paid'){
        //     //send sms
        //     Util::print_sms($transaction->id, $transaction->mobile_no, 'payment_done');
        //     Util::print_sms($transaction->id, $transaction->whatsapp_no, 'payment_done');
        // }
        
        $transaction->payment_status = $status; 
        $transaction->save(); 
            
        
        $updatedTransaction = Transaction::find($transaction_id);
        $databaseName = DB::connection()->getDatabaseName();
    
        return $status;
    }
    /**
     * Update the payment status for purchase or sell transactions. Returns
     * the status
     *
     * @param int $transaction_id
     *
     * @return string
     */
    public function deleteAccountAndLedgerTransactionReverse($transaction, $payment_id)
    {
        $transaction_id = $transaction->id;
        $transaction_payment = TransactionPayment::find($payment_id);
        if ($transaction->type == 'purchase') {
            $account_id = $transaction_payment->account_id;
            
            $contact_id = $transaction->contact_id;
            
            if (empty($account_id)) {
                $parent_transaction = TransactionPayment::where('id', $transaction_payment->parent_id)->withTrashed()->first();
                $account_id = !empty($parent_transaction) ? $parent_transaction->id : null;
                
                $contact_id = $transaction->contact_id;
                if(!empty($account_id)){
                    $account_id->delete();
                }
            }
        }
        if ($transaction->type == 'sell') {
            $account_id = $transaction_payment->account_id;
            $contact_id = $transaction->contact_id;
            
            if (empty($account_id)) {
                $parent_transaction = TransactionPayment::where('id', $transaction_payment->parent_id)->withTrashed()->first();
                $account_id = !empty($parent_transaction) ? $parent_transaction->id : null;
                
                if(!empty($account_id)){
                    $contact_id = $transaction->contact_id;
                    $account_id->delete();
                }
            }
        }
        if ($transaction->type == 'settlement') {
            $account_id = $transaction_payment->account_id;
            $contact_id = $transaction->contact_id;
            if (empty($account_id)) {
                $parent_transaction = TransactionPayment::where('id', $transaction_payment->parent_id)->withTrashed()->first();
                $account_id = !empty($parent_transaction) ? $parent_transaction->id : null;
                
                if(!empty($account_id)){
                    $account_id->delete();
                }
            }
            
        }
        return true;
    }
    public function deleteAccountPaybleTransactionReverse($transaction)
    {
        if ($transaction->type == 'purchase') {
            $payable_account_id = $this->account_exist_return_id('Accounts Payable');
            $contact_id = $transaction->contact_id;
            $account_transaction_data = [
                'amount' => $transaction->final_total,
                'contact_id' => $contact_id,
                'account_id' => $payable_account_id,
                'type' => 'debit',
                'operation_date' => \Carbon::now(),
                'created_by' => Auth::user()->id,
                'transaction_id' => $transaction->id,
                'transaction_payment_id' => null,
                'note' => null
            ];
            $new_account_transaction = AccountTransaction::createAccountTransaction($account_transaction_data);
            $new_account_transaction->deleted_by = Auth::user()->id;
            $new_account_transaction->save();
        }
        if ($transaction->type == 'sell') {
            $payable_account_id = $this->account_exist_return_id('Accounts Payable');
            $contact_id = $transaction->contact_id;
            $account_transaction_data = [
                'amount' => $transaction->final_total,
                'contact_id' => $contact_id,
                'account_id' => $payable_account_id,
                'type' => 'debit',
                'operation_date' => \Carbon::now(),
                'created_by' => Auth::user()->id,
                'transaction_id' => $transaction->id,
                'transaction_payment_id' => null,
                'note' => null
            ];
            $new_account_transaction = AccountTransaction::createAccountTransaction($account_transaction_data);
            $new_account_transaction->deleted_by = Auth::user()->id;
            $new_account_transaction->save();
        }
    }

    public function deleteProductStockTransactionReverse($transaction, $stock_id)
    {
        $contact_id = $transaction->contact_id;
        $account_transaction_data = [
            'amount' => $transaction->final_total,
            'contact_id' => $contact_id,
            'account_id' => $stock_id,
            'type' => 'credit',
            'operation_date' => \Carbon::now(),
            'created_by' => Auth::user()->id,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => null,
            'note' => null
        ];
        $new_account_transaction = AccountTransaction::createAccountTransaction($account_transaction_data);
        $new_account_transaction->deleted_by = Auth::user()->id;
        $new_account_transaction->save();
    }
    /**
     * Purchase currency details
     *
     * @param int $business_id
     *
     * @return object
     */
    public function purchaseCurrencyDetails($business_id)
    {
        $business = Business::find($business_id);
        $output = [
            'purchase_in_diff_currency' => false,
            'p_exchange_rate' => 1,
            'decimal_seperator' => '.',
            'thousand_seperator' => ',',
            'symbol' => '',
        ];
        //Check if diff currency is used or not.
        if ($business->purchase_in_diff_currency == 1) {
            $output['purchase_in_diff_currency'] = true;
            $output['p_exchange_rate'] = $business->p_exchange_rate;
            $currency_id = $business->purchase_currency_id;
        } else {
            $output['purchase_in_diff_currency'] = false;
            $output['p_exchange_rate'] = 1;
            $currency_id = $business->currency_id;
        }
        $currency = Currency::find($currency_id);
        $output['thousand_separator'] = $currency->thousand_separator;
        $output['decimal_separator'] = $currency->decimal_separator;
        $output['symbol'] = $currency->symbol;
        $output['code'] = $currency->code;
        $output['name'] = $currency->currency;
        return (object) $output;
    }
    /**
     * Pay contact due at once
     *
     * @param obj $parent_payment, string $type
     *
     * @return void
     */
    public function payAtOnce($parent_payment, $type)
    {
        //Get all unpaid transaction for the contact
        // $types = ['opening_balance', $type];
        if ($type == 'purchase_return') {
            $types = [$type];
        }elseif($type == 'sell_return'){
            $types = [$type];
        }else{
            $types = array_merge($this->contactUtil->payable_supplier_txns, $this->contactUtil->payable_customer_txns);
        }
        
        $due_transactions = Transaction::where('contact_id', $parent_payment->payment_for)
            ->whereIn('type', $types)
            ->where('payment_status', '!=', 'paid')
            ->orderBy('transaction_date', 'asc')
            ->get();
        $total_amount = $parent_payment->amount;
        $tranaction_payments = [];
        if ($due_transactions->count()) {
            foreach ($due_transactions as $key => $transaction) {
                if ($total_amount > 0) {
                    $total_paid = $this->getTotalPaid($transaction->id);
                    $due = $transaction->final_total - $total_paid;
                    $now = \Carbon::now()->toDateTimeString();
                    $array = [
                        'transaction_id' => $transaction->id,
                        'business_id' => $parent_payment->business_id,
                        'method' => $parent_payment->method,
                        'transaction_no' => $parent_payment->method,
                        'card_transaction_number' => $parent_payment->card_transaction_number,
                        'cheque_number' => $parent_payment->cheque_number,
                        'cheque_date' => $parent_payment->cheque_date,
                        'bank_account_number' => $parent_payment->bank_account_number,
                        'bank_name' => $parent_payment->bank_name,
                        'paid_on' => $parent_payment->paid_on,
                        'created_by' => $parent_payment->created_by,
                        'payment_for' => $parent_payment->payment_for,
                        'parent_id' => $parent_payment->id,
                        'account_id' => $parent_payment->account_id,
                        'paid_in_type' => $parent_payment->paid_in_type,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                    
                    //Generate reference number
                    $payment_ref_no = $parent_payment->payment_ref_no."-".($key+1);
                    $array['payment_ref_no'] = $payment_ref_no;
                    
                    if ($due <= $total_amount) {
                        $array['amount'] = $due;
                        $tranaction_payments[] = $array;
                        //Update transaction status to paid
                        $transaction->payment_status = 'paid';
                        $transaction->save();
                        $total_amount = $total_amount - $due;
                    } else {
                        $array['amount'] = $total_amount;
                        $tranaction_payments[] = $array;
                        //Update transaction status to partial
                        $transaction->payment_status = 'partial';
                        $transaction->save();
                        break;
                    }
                }
            }
            //Insert new transaction payments
            if (!empty($tranaction_payments)) {
                TransactionPayment::insert($tranaction_payments);
            }
            
            $this->updatePaymentStatus($transaction->id);
        }
    }
    
    
    public function payCustomerStatementAtOnce($parent_payment, $transaction_ids)
    {
        
        
        $due_transactions = Transaction::where('contact_id', $parent_payment->payment_for)
            ->whereIn('id', $transaction_ids)
            ->where('payment_status', '!=', 'paid')
            ->orderBy('transaction_date', 'asc')
            ->get();
            
        $total_amount = $parent_payment->amount;
        $tranaction_payments = [];
        if ($due_transactions->count()) {
            foreach ($due_transactions as $key => $transaction) {
                if ($total_amount > 0) {
                    $total_paid = $this->getTotalPaid($transaction->id);
                    $due = $transaction->final_total - $total_paid;
                    $now = \Carbon::now()->toDateTimeString();
                    $array = [
                        'transaction_id' => $transaction->id,
                        'business_id' => $parent_payment->business_id,
                        'method' => $parent_payment->method,
                        'transaction_no' => $parent_payment->method,
                        'card_transaction_number' => $parent_payment->card_transaction_number,
                        'cheque_number' => $parent_payment->cheque_number,
                        'cheque_date' => $parent_payment->cheque_date,
                        'bank_account_number' => $parent_payment->bank_account_number,
                        'bank_name' => $parent_payment->bank_name,
                        'paid_on' => $parent_payment->paid_on,
                        'created_by' => $parent_payment->created_by,
                        'payment_for' => $parent_payment->payment_for,
                        'parent_id' => $parent_payment->id,
                        'account_id' => $parent_payment->account_id,
                        'paid_in_type' => $parent_payment->paid_in_type,
                        'created_at' => $now,
                        'updated_at' => $now,
                        'linked_customer_statement' => $parent_payment->linked_customer_statement
                    ];
                    
                    //Generate reference number
                    $payment_ref_no = $parent_payment->payment_ref_no."-".($key+1);
                    $array['payment_ref_no'] = $payment_ref_no;
                    
                    if ($due <= $total_amount) {
                        $array['amount'] = $due;
                        $tranaction_payments[] = $array;
                        //Update transaction status to paid
                        $transaction->payment_status = 'paid';
                        $transaction->save();
                        $total_amount = $total_amount - $due;
                    } else {
                        $array['amount'] = $total_amount;
                        $tranaction_payments[] = $array;
                        //Update transaction status to partial
                        $transaction->payment_status = 'partial';
                        $transaction->save();
                        break;
                    }
                }
            }
            //Insert new transaction payments
            if (!empty($tranaction_payments)) {
                TransactionPayment::insert($tranaction_payments);
            }
            
            $this->updatePaymentStatus($transaction->id);
        }
    }
    
    
    public function payVATAtOnce($parent_payment, $statement_id)
    {
        $individual_ids = VatCustomerStatementDetail::where('statement_id',$statement_id)->pluck('transaction_id') ?? [];
                    
        $due_transactions = Transaction::whereIn('id',$individual_ids)
            ->where('payment_status', '!=', 'paid')
            ->orderBy('transaction_date', 'asc')
            ->get();
            
        $total_amount = $parent_payment->amount;
        $tranaction_payments = [];
        if ($due_transactions->count()) {
            foreach ($due_transactions as $transaction) {
                if ($total_amount > 0) {
                    $total_paid = $this->getTotalPaid($transaction->id);
                    $due = $transaction->final_total - $total_paid;
                    $now = \Carbon::now()->toDateTimeString();
                    $array = [
                        'transaction_id' => $transaction->id,
                        'business_id' => $parent_payment->business_id,
                        'method' => $parent_payment->method,
                        'transaction_no' => $parent_payment->method,
                        'card_transaction_number' => $parent_payment->card_transaction_number,
                        'cheque_number' => $parent_payment->cheque_number,
                        'cheque_date' => $parent_payment->cheque_date,
                        'bank_account_number' => $parent_payment->bank_account_number,
                        'bank_name' => $parent_payment->bank_name,
                        'paid_on' => $parent_payment->paid_on,
                        'created_by' => $parent_payment->created_by,
                        'payment_for' => $parent_payment->payment_for,
                        'parent_id' => $parent_payment->id,
                        'account_id' => $parent_payment->account_id,
                        'paid_in_type' => $parent_payment->paid_in_type,
                        'created_at' => $now,
                        'updated_at' => $now
                    ];
                    $prefix_type = 'purchase_payment';
                    if (in_array($transaction->type, ['sell', 'sell_return'])) {
                        $prefix_type = 'sell_payment';
                    }
                    $default_prefix = null;
                    if (in_array($transaction->type, ['cheque_return'])) {
                        $prefix_type = 'cheque_return_payment';
                        $default_prefix = 'CRP';
                    }
                    $ref_count = $this->setAndGetReferenceCount($prefix_type);
                    //Generate reference number
                    $payment_ref_no = $this->generateReferenceNumber($prefix_type, $ref_count, null, $default_prefix);
                    $array['payment_ref_no'] = $payment_ref_no;
                    if ($due <= $total_amount) {
                        $array['amount'] = $due;
                        $tranaction_payments[] = $array;
                        //Update transaction status to paid
                        $transaction->payment_status = 'paid';
                        $transaction->save();
                        $total_amount = $total_amount - $due;
                    } else {
                        $array['amount'] = $total_amount;
                        $tranaction_payments[] = $array;
                        //Update transaction status to partial
                        $transaction->payment_status = 'partial';
                        $transaction->save();
                        break;
                    }
                }
            }
            //Insert new transaction payments
            if (!empty($tranaction_payments)) {
                TransactionPayment::insert($tranaction_payments);
            }
        }
    }
    /**
     * Update payment contact due at once
     *
     * @param obj $parent_payment, string $type
     *
     * @return void
     */
    public function updatePaymentAtOnce($parent_payment, $type)
    {
        $payments = TransactionPayment::where('parent_id', $parent_payment->id)->get();
        $total_amount = $parent_payment->amount;
        if ($payments->count()) {
            foreach ($payments as $payment) {
                if ($total_amount > 0) {
                    $array = [
                        'method' => $parent_payment->method,
                        'transaction_no' => $parent_payment->method,
                        'card_transaction_number' => $parent_payment->card_transaction_number,
                        'cheque_number' => $parent_payment->cheque_number,
                        'cheque_date' => $parent_payment->cheque_date,
                        'bank_account_number' => $parent_payment->bank_account_number,
                        'bank_name' => $parent_payment->bank_name,
                        'paid_on' => $parent_payment->paid_on,
                        'created_by' => $parent_payment->created_by,
                        'payment_for' => $parent_payment->payment_for,
                        'parent_id' => $parent_payment->id,
                        'account_id' => $parent_payment->account_id
                    ];
                    $transaction = Transaction::find($payment->transaction_id);
                    if ($payment->amount <= $total_amount) {
                        $array['amount'] = $payment->amount;
                        //update payment
                        TransactionPayment::where('id', $payment->id)->update($array);
                        //Update transaction status to paid
                        $transaction->payment_status = 'paid';
                        $transaction->save();
                        $total_amount = $total_amount - $payment->amount;
                    } else {
                        if ($total_amount > 0) {
                            $array['amount'] = $total_amount;
                            $transaction->payment_status = 'partial';
                            $transaction->save();
                            $total_amount = $total_amount - $array['amount'];
                            TransactionPayment::where('id', $payment->id)->update($array);
                        } else {
                            $this_transaction_id = $payment->transaction_id;
                            TransactionPayment::where('id', $payment->id)->forcedelete();
                            $this->updatePaymentStatus($this_transaction_id);
                        }
                    }
                }
            }
        }
    }
    /**
     * Pay contact due at once
     *
     * @param obj $parent_payment, string $type
     *
     * @return void
     */
    public function getTotalAmountConsumable($parent_payment, $type)
    {
        //Get all unpaid transaction for the contact
        $types = ['opening_balance', $type];
        if ($type == 'purchase_return') {
            $types = [$type];
        }
        $due_transactions = Transaction::where('contact_id', $parent_payment->payment_for)
            ->whereIn('type', $types)
            ->where('payment_status', '!=', 'paid')
            ->orderBy('transaction_date', 'asc')
            ->get();
        $total_amount = $parent_payment->amount;
        $amount_consumed = 0;
        if ($due_transactions->count()) {
            foreach ($due_transactions as $transaction) {
                if ($total_amount > 0) {
                    $total_paid = $this->getTotalPaid($transaction->id);
                    $due = $transaction->final_total - $total_paid;
                    if ($due <= $total_amount) {
                        $amount_consumed += $due;
                        $total_amount = $total_amount - $due;
                    } else {
                        $amount_consumed += $total_amount;
                    }
                }
            }
        }
        return $amount_consumed;
    }
    /**
     * Add a mapping between purchase & sell lines.
     * NOTE: Don't use request variable here, request variable don't exist while adding
     * dummybusiness via command line
     *
     * @param array $business
     * @param array $transaction_lines
     * @param string $mapping_type = purchase (purchase or stock_adjustment)
     * @param boolean $check_expiry = true
     * @param int $purchase_line_id (default: null)
     *
     * @return object
     */
    public function mapPurchaseSell($business, $transaction_lines, $mapping_type = 'purchase', $check_expiry = true, $purchase_line_id = null)
    {
        if (empty($transaction_lines)) {
            return false;
        }
        $allow_overselling = !empty($business['pos_settings']['allow_overselling']) ?
            true : false;
        //Set flag to check for expired items during SELLING only.
        $stop_selling_expired = false;
        if ($check_expiry) {
            if (session()->has('business') && request()->session()->get('business')['enable_product_expiry'] == 1 && request()->session()->get('business')['on_product_expiry'] == 'stop_selling') {
                if ($mapping_type == 'purchase') {
                    $stop_selling_expired = true;
                }
            }
        }
        $qty_selling = null;
        foreach ($transaction_lines as $line) {
            //Check if stock is not enabled then no need to assign purchase & sell
            $product = Product::find($line->product_id);
            if ($product->enable_stock != 1) {
                continue;
            }
            $qty_sum_query = $this->get_pl_quantity_sum_string('PL');
            //Get purchase lines, only for products with enable stock.
            $query = Transaction::join('purchase_lines AS PL', 'transactions.id', '=', 'PL.transaction_id')
                ->where('transactions.business_id', $business['id'])
                ->where('transactions.location_id', $business['location_id'])
                ->whereIn('transactions.type', [
                    'purchase', 'purchase_transfer',
                    'opening_stock', 'production_purchase'
                ])
                ->where('transactions.status', 'received')
                ->whereRaw("( $qty_sum_query ) < PL.quantity")
                ->where('PL.product_id', $line->product_id)
                ->where('PL.variation_id', $line->variation_id);
            //If product expiry is enabled then check for on expiry conditions
            if ($stop_selling_expired && empty($purchase_line_id)) {
                $stop_before = request()->session()->get('business')['stop_selling_before'];
                $expiry_date = \Carbon::today()->addDays($stop_before)->toDateString();
                $query->whereRaw('PL.exp_date IS NULL OR PL.exp_date > ?', [$expiry_date]);
            }
            //If lot number present consider only lot number purchase line
            if (!empty($line->lot_no_line_id)) {
                $query->where('PL.id', $line->lot_no_line_id);
            }
            //If purchase_line_id is given consider only that purchase line
            if (!empty($purchase_line_id)) {
                $query->where('PL.id', $purchase_line_id);
            }
            //Sort according to LIFO or FIFO
            if ($business['accounting_method'] == 'lifo') {
                $query = $query->orderBy('transaction_date', 'desc');
            } else {
                $query = $query->orderBy('transaction_date', 'asc');
            }
            $rows = $query->select(
                'PL.id as purchase_lines_id',
                DB::raw("(PL.quantity - ( $qty_sum_query )) AS quantity_available"),
                'PL.quantity_sold as quantity_sold',
                'PL.quantity_adjusted as quantity_adjusted',
                'PL.quantity_returned as quantity_returned',
                'PL.mfg_quantity_used as mfg_quantity_used',
                'transactions.invoice_no'
            )->get();
            $purchase_sell_map = [];
            //Iterate over the rows, assign the purchase line to sell lines.
            $qty_selling = $line->quantity;
            foreach ($rows as $k => $row) {
                $qty_allocated = 0;
                //removed stock adjustement quantity from $qty_sum_query  // Util.php method get_pl_quantity_sum_string()
                //adding stock adjustment qunatity for purchase line based on adjustemnt type
                $total_adjusted_quantity = $this->getStockAdjustedQuantityForPurchaseLine($row->purchase_lines_id);
                if (!empty($total_adjusted_quantity)) {
                    // add / subtract adjusted quanity from quantity available based on adjustment type
                    $row->quantity_available = $row->quantity_available + $total_adjusted_quantity;
                }
                //Check if qty_available is more or equal
                if ($qty_selling <= $row->quantity_available) {
                    $qty_allocated = $qty_selling;
                    $qty_selling = 0;
                } else {
                    $qty_selling = $qty_selling - $row->quantity_available;
                    $qty_allocated = $row->quantity_available;
                }
                //Check for sell mapping or stock adjsutment mapping
                if ($mapping_type == 'stock_adjustment') {
                    //Mapping of stock adjustment
                    $purchase_adjustment_map[] =
                        [
                            'stock_adjustment_line_id' => $line->id,
                            'purchase_line_id' => $row->purchase_lines_id,
                            'quantity' => $qty_allocated,
                            'created_at' => \Carbon::now(),
                            'updated_at' => \Carbon::now()
                        ];
                    //Update purchase line
                    PurchaseLine::where('id', $row->purchase_lines_id)
                        ->update(['quantity_adjusted' => $row->quantity_adjusted + $qty_allocated]);
                } elseif ($mapping_type == 'purchase') {
                    //Mapping of purchase
                    $purchase_sell_map[] = [
                        'sell_line_id' => $line->id,
                        'purchase_line_id' => $row->purchase_lines_id,
                        'quantity' => $qty_allocated,
                        'created_at' => \Carbon::now(),
                        'updated_at' => \Carbon::now()
                    ];
                    //Update purchase line
                    PurchaseLine::where('id', $row->purchase_lines_id)
                        ->update(['quantity_sold' => $row->quantity_sold + $qty_allocated]);
                } elseif ($mapping_type == 'production_purchase') {
                    //Mapping of purchase
                    $purchase_sell_map[] = [
                        'sell_line_id' => $line->id,
                        'purchase_line_id' => $row->purchase_lines_id,
                        'quantity' => $qty_allocated,
                        'created_at' => \Carbon::now(),
                        'updated_at' => \Carbon::now()
                    ];
                    //Update purchase line
                    PurchaseLine::where('id', $row->purchase_lines_id)
                        ->update(['mfg_quantity_used' => $row->mfg_quantity_used + $qty_allocated]);
                }
                if ($qty_selling == 0) {
                    break;
                }
            }
            if ($qty_selling < 0 || is_null($qty_selling)) {
                //If overselling not allowed through exception else create mapping with blank purchase_line_id
                if (!$allow_overselling) {
                    $variation = Variation::find($line->variation_id);
                    $mismatch_name = $product->name;
                    if (!empty($variation->sub_sku)) {
                        $mismatch_name .= ' ' . 'SKU: ' . $variation->sub_sku;
                    }
                    if (!empty($qty_selling)) {
                        $mismatch_name .= ' ' . 'Quantity: ' . abs($qty_selling);
                    }
                    if ($mapping_type == 'purchase') {
                        $mismatch_error = trans(
                            "messages.purchase_sell_mismatch_exception",
                            ['product' => $mismatch_name]
                        );
                        if ($stop_selling_expired) {
                            $mismatch_error .= __('lang_v1.available_stock_expired');
                        }
                    } elseif ($mapping_type == 'stock_adjustment') {
                        $mismatch_error = trans(
                            "messages.purchase_stock_adjustment_mismatch_exception",
                            ['product' => $mismatch_name]
                        );
                    } else {
                        $mismatch_error = trans(
                            "lang_v1.quantity_mismatch_exception",
                            ['product' => $mismatch_name]
                        );
                    }
                    $business_name = optional(Business::find($business['id']))->name;
                    $location_name = optional(BusinessLocation::find($business['location_id']))->name;
                    \Log::emergency($mismatch_error . ' Business: ' . $business_name . ' Location: ' . $location_name);
                    throw new PurchaseSellMismatch($mismatch_error);
                } else {
                    //Mapping with no purchase line
                    $purchase_sell_map[] = [
                        'sell_line_id' => $line->id,
                        'purchase_line_id' => 0,
                        'quantity' => $qty_selling,
                        'created_at' => \Carbon::now(),
                        'updated_at' => \Carbon::now()
                    ];
                }
            }
            //Insert the mapping
            if (!empty($purchase_adjustment_map)) {
                TransactionSellLinesPurchaseLines::insert($purchase_adjustment_map);
            }
            if (!empty($purchase_sell_map)) {
                TransactionSellLinesPurchaseLines::insert($purchase_sell_map);
            }
        }
    }
    public function getStockAdjustedQuantityForPurchaseLine($purchase_line_id)
    {
        $quantity = 0;
        $stock_adjusted_query =  TransactionSellLinesPurchaseLines::leftjoin('stock_adjustment_lines', 'transaction_sell_lines_purchase_lines.stock_adjustment_line_id', 'stock_adjustment_lines.id')
            ->where('purchase_line_id', $purchase_line_id)
            ->whereNotNull('stock_adjustment_line_id')
            ->select('stock_adjustment_lines.type', 'stock_adjustment_lines.quantity')
            ->groupBy('stock_adjustment_lines.id')
            ->get();
        foreach ($stock_adjusted_query as $stock_adjusted) {
            if ($stock_adjusted->type == 'increase') {
                $quantity += $stock_adjusted->quantity;
            }
            if ($stock_adjusted->type == 'decrease') {
                $quantity -= $stock_adjusted->quantity;
            }
        }
        return $quantity;
    }
    /**
     * F => D (Delete all mapping lines, decrease the qty sold.)
     * D => F (Call the mapPurchaseSell function)
     * F => F (Check for quantity of existing product, call mapPurchase for new products.)
     *
     * @param  string $status_before
     * @param  object $transaction
     * @param  array $business
     * @param  array $deleted_line_ids = [] //deleted sell lines ids.
     *
     * @return void
     */
    public function adjustMappingPurchaseSell(
        $status_before,
        $transaction,
        $business,
        $deleted_line_ids = []
    ) {
        if ($status_before == 'final' && $transaction->status == 'draft') {
            //Get sell lines used for the transaction.
            $sell_purchases = Transaction::join('transaction_sell_lines AS SL', 'transactions.id', '=', 'SL.transaction_id')
                ->join('transaction_sell_lines_purchase_lines as TSP', 'SL.id', '=', 'TSP.sell_line_id')
                ->where('transactions.id', $transaction->id)
                ->select('TSP.purchase_line_id', 'TSP.quantity', 'TSP.id')
                ->get()
                ->toArray();
            //Included the deleted sell lines
            if (!empty($deleted_line_ids)) {
                $deleted_sell_purchases = TransactionSellLinesPurchaseLines::whereIn('sell_line_id', $deleted_line_ids)
                    ->select('purchase_line_id', 'quantity', 'id')
                    ->get()
                    ->toArray();
                $sell_purchases = $sell_purchases + $deleted_sell_purchases;
            }
            //TODO: Optimize the query to take our of loop.
            $sell_purchase_ids = [];
            if (!empty($sell_purchases)) {
                //Decrease the quantity sold of products
                foreach ($sell_purchases as $row) {
                    PurchaseLine::where('id', $row['purchase_line_id'])
                        ->decrement('quantity_sold', $row['quantity']);
                    $sell_purchase_ids[] = $row['id'];
                }
                //Delete the lines.
                TransactionSellLinesPurchaseLines::whereIn('id', $sell_purchase_ids)
                    ->delete();
            }
        } elseif ($status_before == 'draft' && $transaction->status == 'final') {
            $this->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');
        } elseif ($status_before == 'final' && $transaction->status == 'final') {
            //Handle deleted line
            if (!empty($deleted_line_ids)) {
                $deleted_sell_purchases = TransactionSellLinesPurchaseLines::whereIn('sell_line_id', $deleted_line_ids)
                    ->select('sell_line_id', 'quantity')
                    ->get();
                if (!empty($deleted_sell_purchases)) {
                    foreach ($deleted_sell_purchases as $value) {
                        $this->mapDecrementPurchaseQuantity($value->sell_line_id, $value->quantity);
                    }
                }
            }
            //Check for update quantity, new added rows, deleted rows.
            $sell_purchases = Transaction::join('transaction_sell_lines AS SL', 'transactions.id', '=', 'SL.transaction_id')
                ->leftjoin('transaction_sell_lines_purchase_lines as TSP', 'SL.id', '=', 'TSP.sell_line_id')
                ->where('transactions.id', $transaction->id)
                ->select(
                    'TSP.purchase_line_id',
                    'TSP.quantity AS tsp_quantity',
                    'TSP.id as tsp_id',
                    'SL.*'
                )
                ->get();
            $deleted_sell_lines = [];
            $new_sell_lines = [];
            $processed_sell_lines = [];
            foreach ($sell_purchases as $line) {
                if (empty($line->purchase_line_id)) {
                    $new_sell_lines[] = $line;
                } else {
                    //Skip if already processed.
                    if (in_array($line->purchase_line_id, $processed_sell_lines)) {
                        continue;
                    }
                    $processed_sell_lines[] = $line->purchase_line_id;
                    $total_sold_entry = TransactionSellLinesPurchaseLines::where('sell_line_id', $line->id)
                        ->select(DB::raw('SUM(quantity) AS quantity'))
                        ->first();
                    if ($total_sold_entry->quantity != $line->quantity) {
                        if ($line->quantity > $total_sold_entry->quantity) {
                            //If quantity is increased add it to new sell lines by decreasing tsp_quantity
                            $line_temp = $line;
                            $line_temp->quantity = $line_temp->quantity - $total_sold_entry->quantity;
                            $new_sell_lines[] = $line_temp;
                        } elseif ($line->quantity < $total_sold_entry->quantity) {
                            $decrement_qty = $total_sold_entry->quantity - $line->quantity;
                            $this->mapDecrementPurchaseQuantity($line->id, $decrement_qty);
                        }
                    }
                }
            }
            //Add mapping for new sell lines and for incremented quantity
            if (!empty($new_sell_lines)) {
                $this->mapPurchaseSell($business, $new_sell_lines);
            }
        }
    }
    /**
     * Decrease the purchase quantity from
     * transaction_sell_lines_purchase_lines and purchase_lines.quantity_sold
     *
     * @param  int $sell_line_id
     * @param  int $decrement_qty
     *
     * @return void
     */
    private function mapDecrementPurchaseQuantity($sell_line_id, $decrement_qty)
    {
        $sell_purchase_line = TransactionSellLinesPurchaseLines::where('sell_line_id', $sell_line_id)
            ->orderBy('id', 'desc')
            ->get();
        foreach ($sell_purchase_line as $row) {
            if ($row->quantity > $decrement_qty) {
                PurchaseLine::where('id', $row->purchase_line_id)
                    ->decrement('quantity_sold', $decrement_qty);
                $row->quantity = $row->quantity - $decrement_qty;
                $row->save();
                $decrement_qty = 0;
            } else {
                PurchaseLine::where('id', $row->purchase_line_id)
                    ->decrement('quantity_sold', $decrement_qty);
                $row->delete();
            }
            $decrement_qty = $decrement_qty - $row->quantity;
            if ($decrement_qty <= 0) {
                break;
            }
        }
    }
    /**
     * Decrement quantity adjusted in product line according to
     * transaction_sell_lines_purchase_lines
     * Used in delete of stock adjustment
     *
     * @param  array $line_ids
     *
     * @return boolean
     */
    public function mapPurchaseQuantityForDeleteStockAdjustment($line_ids)
    {
        if (empty($line_ids)) {
            return true;
        }
        $map_line = TransactionSellLinesPurchaseLines::whereIn('stock_adjustment_line_id', $line_ids)
            ->orderBy('id', 'desc')
            ->get();
        foreach ($map_line as $row) {
            PurchaseLine::where('id', $row->purchase_line_id)
                ->decrement('quantity_adjusted', $row->quantity);
        }
        //Delete the tslp line.
        TransactionSellLinesPurchaseLines::whereIn('stock_adjustment_line_id', $line_ids)
            ->delete();
        return true;
    }
    /**
     * Adjust the existing mapping between purchase & sell on edit of
     * purchase
     *
     * @param  string $before_status
     * @param  object $transaction
     * @param  object $delete_purchase_lines
     *
     * @return void
     */
    public function adjustMappingPurchaseSellAfterEditingPurchase($before_status, $transaction, $delete_purchase_lines)
    {
        if ($before_status == 'received' && $transaction->status == 'received') {
            //Check if there is some irregularities between purchase & sell and make appropiate adjustment.
            //Get all purchase line having irregularities.
            $purchase_lines = Transaction::join(
                'purchase_lines AS PL',
                'transactions.id',
                '=',
                'PL.transaction_id'
            )
                ->join(
                    'transaction_sell_lines_purchase_lines AS TSPL',
                    'PL.id',
                    '=',
                    'TSPL.purchase_line_id'
                )
                ->groupBy('TSPL.purchase_line_id')
                ->where('transactions.id', $transaction->id)
                ->havingRaw('SUM(TSPL.quantity) > MAX(PL.quantity)')
                ->select([
                    'TSPL.purchase_line_id AS id',
                    DB::raw('SUM(TSPL.quantity) AS tspl_quantity'),
                    DB::raw('MAX(PL.quantity) AS pl_quantity')
                ])
                ->get()
                ->toArray();
        } elseif ($before_status == 'received' && $transaction->status != 'received') {
            //Delete sell for those & add new sell or throw error.
            $purchase_lines = Transaction::join(
                'purchase_lines AS PL',
                'transactions.id',
                '=',
                'PL.transaction_id'
            )
                ->join(
                    'transaction_sell_lines_purchase_lines AS TSPL',
                    'PL.id',
                    '=',
                    'TSPL.purchase_line_id'
                )
                ->groupBy('TSPL.purchase_line_id')
                ->where('transactions.id', $transaction->id)
                ->select([
                    'TSPL.purchase_line_id AS id',
                    DB::raw('MAX(PL.quantity) AS pl_quantity')
                ])
                ->get()
                ->toArray();
        } else {
            return true;
        }
        //Get detail of purchase lines deleted
        if (!empty($delete_purchase_lines)) {
            $purchase_lines = $delete_purchase_lines->toArray() + $purchase_lines;
        }
        //All sell lines & Stock adjustment lines.
        $sell_lines = [];
        $stock_adjustment_lines = [];
        foreach ($purchase_lines as $purchase_line) {
            $tspl_quantity = isset($purchase_line['tspl_quantity']) ? $purchase_line['tspl_quantity'] : 0;
            $pl_quantity = isset($purchase_line['pl_quantity']) ? $purchase_line['pl_quantity'] : $purchase_line['quantity'];
            $extra_sold = abs($tspl_quantity - $pl_quantity);
            //Decrease the quantity from transaction_sell_lines_purchase_lines or delete it if zero
            $tspl = TransactionSellLinesPurchaseLines::where('purchase_line_id', $purchase_line['id'])
                ->leftjoin(
                    'transaction_sell_lines AS SL',
                    'transaction_sell_lines_purchase_lines.sell_line_id',
                    '=',
                    'SL.id'
                )
                ->leftjoin(
                    'stock_adjustment_lines AS SAL',
                    'transaction_sell_lines_purchase_lines.stock_adjustment_line_id',
                    '=',
                    'SAL.id'
                )
                ->orderBy('transaction_sell_lines_purchase_lines.id', 'desc')
                ->select([
                    'SL.product_id AS sell_product_id',
                    'SL.variation_id AS sell_variation_id',
                    'SL.id AS sell_line_id',
                    'SAL.product_id AS adjust_product_id',
                    'SAL.variation_id AS adjust_variation_id',
                    'SAL.id AS adjust_line_id',
                    'transaction_sell_lines_purchase_lines.quantity',
                    'transaction_sell_lines_purchase_lines.purchase_line_id', 'transaction_sell_lines_purchase_lines.id as tslpl_id'
                ])
                ->get();
            foreach ($tspl as $row) {
                if ($row->quantity <= $extra_sold) {
                    if (!empty($row->sell_line_id)) {
                        $sell_lines[] = (object) [
                            'id' => $row->sell_line_id,
                            'quantity' => $row->quantity,
                            'product_id' => $row->sell_product_id,
                            'variation_id' => $row->sell_variation_id,
                        ];
                        PurchaseLine::where('id', $row->purchase_line_id)
                            ->decrement('quantity_sold', $row->quantity);
                    } else {
                        $stock_adjustment_lines[] =
                            (object) [
                                'id' => $row->adjust_line_id,
                                'quantity' => $row->quantity,
                                'product_id' => $row->adjust_product_id,
                                'variation_id' => $row->adjust_variation_id,
                            ];
                        PurchaseLine::where('id', $row->purchase_line_id)
                            ->decrement('quantity_adjusted', $row->quantity);
                    }
                    $extra_sold = $extra_sold - $row->quantity;
                    TransactionSellLinesPurchaseLines::where('id', $row->tslpl_id)->delete();
                } else {
                    if (!empty($row->sell_line_id)) {
                        $sell_lines[] = (object) [
                            'id' => $row->sell_line_id,
                            'quantity' => $extra_sold,
                            'product_id' => $row->sell_product_id,
                            'variation_id' => $row->sell_variation_id,
                        ];
                        PurchaseLine::where('id', $row->purchase_line_id)
                            ->decrement('quantity_sold', $extra_sold);
                    } else {
                        $stock_adjustment_lines[] =
                            (object) [
                                'id' => $row->adjust_line_id,
                                'quantity' => $extra_sold,
                                'product_id' => $row->adjust_product_id,
                                'variation_id' => $row->adjust_variation_id,
                            ];
                        PurchaseLine::where('id', $row->purchase_line_id)
                            ->decrement('quantity_adjusted', $extra_sold);
                    }
                    TransactionSellLinesPurchaseLines::where('id', $row->tslpl_id)->update(['quantity' => $row->quantity - $extra_sold]);
                    $extra_sold = 0;
                }
                if ($extra_sold == 0) {
                    break;
                }
            }
        }
        $business = Business::find($transaction->business_id)->toArray();
        $business['location_id'] = $transaction->location_id;
        //Allocate the sold lines to purchases.
        if (!empty($sell_lines)) {
            $sell_lines = (object) $sell_lines;
            $this->mapPurchaseSell($business, $sell_lines, 'purchase');
        }
        //Allocate the stock adjustment lines to purchases.
        if (!empty($stock_adjustment_lines)) {
            $stock_adjustment_lines = (object) $stock_adjustment_lines;
            $this->mapPurchaseSell($business, $stock_adjustment_lines, 'stock_adjustment');
        }
    }
    /**
     * Check if transaction can be edited based on business     transaction_edit_days
     *
     * @param  int/object $transaction
     * @param  int $edit_duration
     *
     * @return boolean
     */
    public function canBeEdited($transaction, $edit_duration)
    {
        if (!is_object($transaction)) {
            $transaction = Transaction::find($transaction);
        }
        if (empty($transaction)) {
            return false;
        }
        $date = \Carbon::parse($transaction->transaction_date)
            ->addDays($edit_duration);
        $today = today();
        if ($date->gte($today)) {
            return true;
        } else {
            return false;
        }
    }
    /**
     * Calculates total stock on the given date
     *
     * @param int $business_id
     * @param string $date
     * @param int $location_id
     * @param boolean $is_opening = false
     *
     * @return float
     */
    
    public function getNonFuelProductBalanceOnDate($one,$date,$business_id){
        
        $product_id = $one->product_id;
        $variation_id = $one->id;
        $sum = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['purchase','purchase_return'])
                    ->where('purchase_lines.product_id', $product_id)
                    ->where('transactions.transaction_date','<=',$date)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.status','received')
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity, SUM(purchase_lines.quantity_returned) AS sum_quantity_returned')
                    ->withoutTrashed()
                    ->first();
                
                $sumPurchaseDeleted = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                    ->where('transactions.business_id', $business_id)
                    
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['_deleted_purchase'])
                    ->where('purchase_lines.product_id', $product_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.status','received')
                    ->where('transactions.transaction_date','<=',$date)
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity, SUM(purchase_lines.quantity_returned) AS sum_quantity_returned')
                    ->withoutTrashed()
                    ->first();
                    
                $sumTransfer = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                    
                    ->where('transactions.business_id', $business_id)
                    
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['purchase_transfer'])
                    ->where('purchase_lines.product_id', $product_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.status','received')
                    ->where('transactions.transaction_date','<=',$date)
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                
                $sumProd = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['production_purchase'])
                    ->where('purchase_lines.product_id', $product_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.transaction_date','<=',$date)
                    ->where('transactions.status','received')
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                
                    
                $sumOpening = Transaction::leftJoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['opening_stock'])
                    ->where('purchase_lines.product_id', $product_id)
                    ->where('purchase_lines.variation_id', $variation_id)
                    ->where('transactions.transaction_date','<=',$date)
                    ->where('transactions.status','received')
                    ->whereNull('purchase_lines.deleted_at')
                    ->groupBy('purchase_lines.variation_id')
                    ->selectRaw('SUM(purchase_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                    
                $sumAdjust = Transaction::leftJoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
                
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['stock_adjustment'])
                    ->where('stock_adjustment_lines.product_id', $product_id)
                    ->where('stock_adjustment_lines.variation_id', $variation_id)
                    ->where('transactions.transaction_date','<=',$date)
                    ->where('transactions.status','received')
                    ->select(
                        DB::raw("SUM(IF(stock_adjustment_lines.stock_adjustment_type='increase', stock_adjustment_lines.quantity, 0)) as increased"),
                        DB::raw("SUM(IF(stock_adjustment_lines.stock_adjustment_type='decrease', stock_adjustment_lines.quantity, 0)) as decreased")
                        )
                    ->withoutTrashed()
                    ->get()
                    ->first();
                
                $incr = !empty($sumAdjust) ? $sumAdjust->increased : 0;
                $decr = !empty($sumAdjust) ? $sumAdjust->decreased : 0;
                
                $sumOp = !empty($sumOpening) ? $sumOpening->sum_quantity : 0;
                $sumPt = !empty($sumTransfer) ? $sumTransfer->sum_quantity : 0;
                $sumPr = !empty($sumProd) ? $sumProd->sum_quantity : 0;
                
                $sumQuantity = !empty($sum) ? $sum->sum_quantity : 0;
                $sumQuantityDeleted = !empty($sumPurchaseDeleted) ? $sumPurchaseDeleted->sum_quantity : 0;
                $sumQuantityReturnedDeleted = !empty($sumPurchaseDeleted) ? $sumPurchaseDeleted->sum_quantity_returned : 0;
                
                $sumQuantityReturned = !empty($sum) ? $sum->sum_quantity_returned : 0;
                
                
                $sumSell = Transaction::leftJoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['sell','sell_return'])
                    ->where('transaction_sell_lines.product_id', $product_id)
                    ->where('transaction_sell_lines.variation_id', $variation_id)
                    ->where('transactions.status','final')
                    ->where('transactions.transaction_date','<=',$date)
                    ->whereNull('transaction_sell_lines.deleted_at')
                    ->groupBy('transaction_sell_lines.variation_id')
                    ->selectRaw('SUM(transaction_sell_lines.quantity) AS sum_quantity, SUM(transaction_sell_lines.quantity_returned) AS sum_quantity_returned')
                    ->withoutTrashed()
                    ->first();
                
                $sumSellTr = Transaction::leftJoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['sell_transfer'])
                    ->where('transaction_sell_lines.product_id', $product_id)
                    ->where('transaction_sell_lines.variation_id', $variation_id)
                    ->where('transactions.status','final')
                    ->where('transactions.transaction_date','<=',$date)
                    ->whereNull('transaction_sell_lines.deleted_at')
                    ->groupBy('transaction_sell_lines.variation_id')
                    ->selectRaw('SUM(transaction_sell_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                    
                $sumSellPr = Transaction::leftJoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->where(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', 1)
                                       ->where('transactions.is_credit_sale', 0);
                        })->orWhere(function ($innerQuery) {
                            $innerQuery->where('transactions.is_settlement', '<>', 1);
                        });
                    })
                    
                    ->whereIn('transactions.type',['production_sell'])
                    ->where('transaction_sell_lines.product_id', $product_id)
                    ->where('transaction_sell_lines.variation_id', $variation_id)
                    ->where('transactions.status','final')
                    ->where('transactions.transaction_date','<=',$date)
                    ->whereNull('transaction_sell_lines.deleted_at')
                    ->groupBy('transaction_sell_lines.variation_id')
                    ->selectRaw('SUM(transaction_sell_lines.quantity) AS sum_quantity')
                    ->withoutTrashed()
                    ->first();
                
                $sumQuantitySell = !empty($sumSell) ? $sumSell->sum_quantity : 0;
                $sumQuantityTr = !empty($sumSellTr) ? $sumSellTr->sum_quantity : 0;
                $sumQuantityPr = !empty($sumSellPr) ? $sumSellPr->sum_quantity : 0;
                
                $sumQuantitySell_2 = !empty($sumSell) ? $sumSell->sum_quantity_sell : 0;
                $sumQuantityReturnedSell = !empty($sumSell) ? $sumSell->sum_quantity_returned : 0;
                
                $bal = $sumOp + $sumQuantity - $sumQuantityDeleted - $sumQuantityReturned + $sumQuantityReturnedDeleted - $sumQuantitySell + $sumQuantityReturnedSell + $incr - $decr + $sumPt - $sumQuantityTr - $sumQuantityPr + $sumPr;
              return $bal;  
    }
    
    public function getOpeningClosingStock($business_id, $date, $location_id, $is_opening = false, $by_sale_price = false,$filters = [])
    {
        $query = PurchaseLine::join(
            'transactions as purchase',
            'purchase_lines.transaction_id',
            '=',
            'purchase.id'
        )
            ->where('purchase.business_id', $business_id);
        $price_query_part = 'v.dpp_inc_tax'; /*"(purchase_lines.purchase_price +
            COALESCE(purchase_lines.item_tax, 0))";*/
        if ($by_sale_price) {
            $price_query_part = 'v.sell_price_inc_tax';
        }
        $query->leftjoin('variations as v', 'v.id', '=', 'purchase_lines.variation_id')
            ->leftjoin('products as p', 'p.id', '=', 'purchase_lines.product_id')
            ->leftjoin('variation_store_details as vsd', 'v.id', '=', 'vsd.variation_id');

        if (!empty($filters['category_id'])) {
            $query->where('p.category_id', $filters['category_id']);
        }
        if (!empty($filters['sub_category_id'])) {
            $query->where('p.sub_category_id', $filters['sub_category_id']);
        }
        if (!empty($filters['brand_id'])) {
            $query->where('p.brand_id', $filters['brand_id']);
        }
        if (!empty($filters['unit_id'])) {
            $query->where('p.unit_id', $filters['unit_id']);
        }
        if (!empty($filters['tax_id'])) {
            $query->where('p.tax', $filters['tax_id']);
        }
        if (!empty($filters['type'])) {
            $query->where('p.type', $filters['type']);
        }
        if (isset($filters['active_state']) && $filters['active_state'] == 'active') {
            $query->where('p.is_inactive', 0);
        }
        if (isset($filters['active_state']) && $filters['active_state'] == 'inactive') {
            $query->where('p.is_inactive', 1);
        }
        if (isset($filters['not_for_selling']) && $filters['not_for_selling'] == 1) {
            $query->where('p.not_for_selling', 1);
        }
        if (!empty($filters['repair_model_id'])) {
            $query->where('p.repair_model_id', request()->get('repair_model_id'));
        }
        if(!empty($filters['store_id'])){
            $query->where('vsd.store_id', $filters['store_id']);
        }
        if (isset($filters['only_mfg_products']) && $filters['only_mfg_products'] == 1) {
            $query->join('mfg_recipes as mr', 'mr.variation_id', '=', 'v.id');
        }

        //If opening
        if ($is_opening) {
            $next_day = \Carbon::createFromFormat('Y-m-d', $date)->addDay()->format('Y-m-d');
            $query->where(function ($query) use ($date, $next_day) {
                $query->whereRaw("date(transaction_date) <= '$date'")
                    ->orWhereRaw("date(transaction_date) = '$next_day' AND purchase.type='opening_stock' ");
            });
        } else {
            $query->whereRaw("date(transaction_date) <= '$date'");
        }
        
        
        $query->select(
            DB::raw("SUM((purchase_lines.quantity - purchase_lines.quantity_returned - purchase_lines.quantity_adjusted -
                            (SELECT COALESCE(SUM(tspl.quantity - tspl.qty_returned), 0) FROM
                            transaction_sell_lines_purchase_lines AS tspl
                            JOIN transaction_sell_lines as tsl ON
                            tspl.sell_line_id=tsl.id
                            JOIN transactions as sale ON
                            tsl.transaction_id=sale.id
                            WHERE tspl.purchase_line_id = purchase_lines.id AND
                            date(sale.transaction_date) <= '$date') ) * $price_query_part
                        ) as stock")
        );
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('purchase.location_id', $permitted_locations);
        }
        if (!empty($location_id)) {
            $query->where('purchase.location_id', $location_id);
        }

   
        $details = $query->first();

        return $details->stock;
    }
    
    public function getrangeOpeningClosingStock($business_id, $start_date,$end_date, $location_id)
    {
        $query = PurchaseLine::join(
            'transactions as purchase',
            'purchase_lines.transaction_id',
            '=',
            'purchase.id'
        )
            ->where('purchase.business_id', $business_id);
        $price_query_part = 'v.dpp_inc_tax'/*"(purchase_lines.purchase_price +
            COALESCE(purchase_lines.item_tax, 0))"*/;
        
        $query->leftjoin('variations as v', 'v.id', '=', 'purchase_lines.variation_id')
            ->leftjoin('products as p', 'p.id', '=', 'purchase_lines.product_id')
            ->leftjoin('variation_store_details as vsd', 'v.id', '=', 'vsd.variation_id');


        //If opening
       
        $query->where(function ($query) use ($start_date, $end_date) {
            $query->whereRaw("date(transaction_date) <= '$end_date' AND date(transaction_date) >= '$start_date' AND purchase.type='opening_stock' ");
        });
       
        $query->select(
            DB::raw("SUM((purchase_lines.quantity - purchase_lines.quantity_returned - purchase_lines.quantity_adjusted -
                            (SELECT COALESCE(SUM(tspl.quantity - tspl.qty_returned), 0) FROM
                            transaction_sell_lines_purchase_lines AS tspl
                            JOIN transaction_sell_lines as tsl ON
                            tspl.sell_line_id=tsl.id
                            JOIN transactions as sale ON
                            tsl.transaction_id=sale.id
                            WHERE tspl.purchase_line_id = purchase_lines.id AND date(sale.transaction_date) >= '$start_date' AND
                            date(sale.transaction_date) <= '$end_date' AND sale.type='opening_stock') ) * $price_query_part
                        ) as stock")
        );
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('purchase.location_id', $permitted_locations);
        }
        if (!empty($location_id)) {
            $query->where('purchase.location_id', $location_id);
        }

   
        $details = $query->first();

        return $details->stock;
    }
    /**
     * Calculates opening stock on the given date
     *
     * @param int $business_id
     * @param string $date
     * @param string $end_date
     * @param int $location_id
     * @param boolean $is_opening = false
     *
     * @return float
     */
    public function getOpeningClosingStockFinal($business_id, $date, $end_date,$location_id)
    {
        $query = PurchaseLine::join(
            'transactions as purchase',
            'purchase_lines.transaction_id',
            '=',
            'purchase.id'
        )
            ->where('purchase.business_id', $business_id);

        $query->leftjoin('variations as v', 'v.id', '=', 'purchase_lines.variation_id')
            ->leftjoin('products as p', 'p.id', '=', 'purchase_lines.product_id');
        if (!empty($filters['category_id'])) {
            $query->where('p.category_id', $filters['category_id']);
        }
        if (!empty($filters['sub_category_id'])) {
            $query->where('p.sub_category_id', $filters['sub_category_id']);
        }
        if (!empty($filters['brand_id'])) {
            $query->where('p.brand_id', $filters['brand_id']);
        }
        if (!empty($filters['unit_id'])) {
            $query->where('p.unit_id', $filters['unit_id']);
        }

        //calculate first day of stock
        $next_day = \Carbon::createFromFormat('Y-m-d', $date)->addDay()->format('Y-m-d');
        $opening_date_query = PurchaseLine::join('transactions','purchase_lines.transaction_id','=','transactions.id')
            ->select(DB::raw("min(date(transactions.transaction_date)) as stock_date"))
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type','opening_stock')
            ->whereBetween(DB::raw('date(transactions.transaction_date)'), [$next_day, $end_date]);

        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('purchase.location_id', $permitted_locations);
            $opening_date_query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($location_id)) {
            $query->where('purchase.location_id', $location_id);
            $opening_date_query->where('transactions.location_id', $location_id);
        }
        $stock_date = $opening_date_query->first()->stock_date;
        $stock_date_found = false;
        if(  $stock_date ){
            $stock_date_format = \Carbon::createFromFormat('Y-m-d', $stock_date);
            $start_date_format= \Carbon::createFromFormat('Y-m-d', $next_day);
            $end_date_format = \Carbon::createFromFormat('Y-m-d', $end_date);
            if( $stock_date_format->gte($start_date_format) && $stock_date_format->lte($end_date_format)) {
                $stock_date_found = true;
            }

        }
        if ( $stock_date_found ) {
            $query->select(
                DB::raw("SUM((purchase_lines.quantity - purchase_lines.quantity_returned + purchase_lines.quantity_adjusted) * purchase_lines.purchase_price_inc_tax 
                            ) as stock")
            )
            ->where('purchase.type','opening_stock')
            ->whereRaw("date(transaction_date) <= '$stock_date'");
            
            $details = $query->first();
            
                
// and then you can get query log

            return $details->stock;

        } else {
            $query->where(function ($query) use ($date, $next_day) {
                $query->whereRaw("date(transaction_date) <= '$date'")
                    ->orWhereRaw("date(transaction_date) = '$next_day' AND purchase.type='opening_stock' ");
            });
        }
        $query->select(
            DB::raw("SUM((purchase_lines.quantity - purchase_lines.quantity_returned + purchase_lines.quantity_adjusted -
                            (SELECT COALESCE(SUM(tspl.quantity - tspl.qty_returned), 0) FROM
                            transaction_sell_lines_purchase_lines AS tspl
                            JOIN transaction_sell_lines as tsl ON
                            tspl.sell_line_id=tsl.id
                            JOIN transactions as sale ON
                            tsl.transaction_id=sale.id
                            WHERE tspl.purchase_line_id = purchase_lines.id AND
                            date(sale.transaction_date) <= '$date'))  * purchase_lines.purchase_price_inc_tax
                        ) as stock")
        );


        // Your Eloquent query executed by using get()



        $details = $query->first();
        return $details->stock;
    }
    /**
     * Calculates total stock on the given date
     *
     * @param int $business_id
     * @param string $date
     * @param int $location_id
     * @param boolean $is_opening = false
     *
     * @return float
     */
    public function getOpeningClosingStockNew($business_id, $date, $end_date, $location_id, $is_opening = false)
    {
        $query = PurchaseLine::join(
            'transactions as purchase',
            'purchase_lines.transaction_id',
            '=',
            'purchase.id'
        )->where('purchase.business_id', $business_id);
        //If opening
        if ($is_opening) {
            $query->where(function ($query) use ($date, $end_date) {
                $query->whereRaw("date(transaction_date) <= '$end_date' AND purchase.type='opening_stock'");
            });
        } else {
            $query->whereRaw("date(transaction_date) <= '$date'");
        }
        $query->select(
            DB::raw("SUM((purchase_lines.quantity - purchase_lines.quantity_returned - purchase_lines.quantity_adjusted -
                            (SELECT COALESCE(SUM(tspl.quantity - tspl.qty_returned), 0) FROM
                            transaction_sell_lines_purchase_lines AS tspl
                            JOIN transaction_sell_lines as tsl ON
                            tspl.sell_line_id=tsl.id
                            JOIN transactions as sale ON
                            tsl.transaction_id=sale.id
                            WHERE tspl.purchase_line_id = purchase_lines.id AND
                            date(sale.transaction_date) <= '$date') ) * (purchase_lines.purchase_price +
                            COALESCE(purchase_lines.item_tax, 0))
                        ) as stock")
        );
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('purchase.location_id', $permitted_locations);
        }
        if (!empty($location_id)) {
            $query->where('purchase.location_id', $location_id);
        }
        $details = $query->first();
        return $details->stock;
    }
    /**
     * Gives the total sell commission for a commission agent within the date range passed
     *
     * @param int $business_id
     * @param string $start_date
     * @param string $end_date
     * @param int $location_id
     * @param int $commission_agent
     *
     * @return array
     */
    public function getTotalSellCommission($business_id, $start_date = null, $end_date = null, $location_id = null, $commission_agent = null)
    {
        $query = Transaction::leftjoin('transactions as SR', function ($join) {
            $join->on('SR.return_parent_id', '=', 'transactions.id')
                ->where('SR.type', 'sell_return');
        })
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->where('transactions.status', 'final')
            ->select(DB::raw("SUM( transactions.final_total - COALESCE(SR.final_total, 0) ) as final_total"));
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transactions.transaction_date)'), [$start_date, $end_date]);
        }
        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        if (!empty($commission_agent)) {
            $query->where('transactions.commission_agent', $commission_agent);
        }
        $sell_details = $query->get();
        $output['total_sales_with_commission'] = $sell_details->sum('final_total');
        return $output;
    }
    /**
     * Add Sell transaction
     *
     * @param int $business_id
     * @param array $input
     * @param float $invoice_total
     * @param int $user_id
     *
     * @return boolean
     */
    public function createSellReturnTransaction($business_id, $input, $invoice_total, $user_id)
    {
        $transaction = Transaction::create([
            'business_id' => $business_id,
            'location_id' => $input['location_id'],
            'type' => 'sell_return',
            'status' => 'final',
            'contact_id' => $input['contact_id'],
            'customer_group_id' => $input['customer_group_id'],
            'ref_no' => $input['ref_no'],
            'total_before_tax' => $invoice_total['total_before_tax'],
            'transaction_date' => $input['transaction_date'],
            'tax_id' => null,
            'discount_type' => $input['discount_type'],
            'discount_amount' => $this->num_uf($input['discount_amount']),
            'tax_amount' => $invoice_total['tax'],
            'final_total' => $this->num_uf($input['final_total']),
            'additional_notes' => !empty($input['additional_notes']) ? $input['additional_notes'] : null,
            'created_by' => $user_id,
            'is_quotation' => isset($input['is_quotation']) ? $input['is_quotation'] : 0
        ]);
        return $transaction;
    }
    public function groupTaxDetails($tax, $amount)
    {
        if (!is_object($tax)) {
            $tax = TaxRate::find($tax);
        }
        if (!empty($tax)) {
            $sub_taxes = $tax->sub_taxes;
            $sum = $tax->sub_taxes->sum('amount');
            $details = [];
            foreach ($sub_taxes as $sub_tax) {
                $details[] = [
                    'id' => $sub_tax->id,
                    'name' => $sub_tax->name,
                    'amount' => $sub_tax->amount,
                    'calculated_tax' => ($amount / $sum) * $sub_tax->amount,
                ];
            }
            return $details;
        } else {
            return [];
        }
    }
    public function sumGroupTaxDetails($group_tax_details)
    {
        $output = [];
        foreach ($group_tax_details as $group_tax_detail) {
            if (!isset($output[$group_tax_detail['name']])) {
                $output[$group_tax_detail['name']] = 0;
            }
            $output[$group_tax_detail['name']] += $group_tax_detail['calculated_tax'];
        }
        return $output;
    }
    /**
     * Retrieves all available lot numbers of a product from variation id
     *
     * @param  int $variation_id
     * @param  int $business_id
     * @param  int $location_id
     *
     * @return boolean
     */
    public function getLotNumbersFromVariation($variation_id, $business_id, $location_id, $exclude_empty_lot = false)
    {
        $query = PurchaseLine::join(
            'transactions as T',
            'purchase_lines.transaction_id',
            '=',
            'T.id'
        )
            ->where('T.business_id', $business_id)
            ->where('T.location_id', $location_id)
            ->where('purchase_lines.variation_id', $variation_id);
        //If expiry is disabled
        if (request()->session()->get('business.enable_product_expiry') == 0) {
            $query->whereNotNull('purchase_lines.lot_number');
        }
        if ($exclude_empty_lot) {
            $query->whereRaw('(purchase_lines.quantity_sold + purchase_lines.quantity_adjusted + purchase_lines.quantity_returned) < purchase_lines.quantity');
        } else {
            $query->whereRaw('(purchase_lines.quantity_sold + purchase_lines.quantity_adjusted + purchase_lines.quantity_returned) <= purchase_lines.quantity');
        }
        $purchase_lines = $query->select('purchase_lines.id as purchase_line_id', 'lot_number', 'purchase_lines.exp_date as exp_date', DB::raw('(purchase_lines.quantity - (purchase_lines.quantity_sold + purchase_lines.quantity_adjusted + purchase_lines.quantity_returned)) AS qty_available'))->get();
        return $purchase_lines;
    }
    /**
     * Checks if credit limit of a customer is exceeded
     *
     * @param  array $input
     * @param  int $exclude_transaction_id (For update sell)
     *
     * @return mixed
     * if exceeded returns credit_limit else false
     */
    public function isCustomerCreditLimitExeeded(
        $input,
        $exclude_transaction_id = null
    ) {
        $credit_limit = Contact::find($input['contact_id'])->credit_limit;
        $customer = Contact::find($input['contact_id']);
        if ($credit_limit == null) {
            return false;
        }
        $over_limit_percentage = 0;
        if ($customer->sell_over_limit  == 1) {
            $over_limit_percentage =  ($credit_limit * $customer->over_limit_percentage) / 100;
            $credit_limit = $credit_limit +  $over_limit_percentage;
        }
        $query = Contact::where('contacts.id', $input['contact_id'])
            ->join('transactions AS t', 'contacts.id', '=', 't.contact_id');
        //Exclude transaction id if update transaction
        if (!empty($exclude_transaction_id)) {
            $query->where('t.id', '!=', $exclude_transaction_id);
        }
        $credit_details =  $query->select(
            DB::raw("SUM(IF(t.type = 'sell', final_total, 0)) as total_invoice"),
            DB::raw("SUM(IF(t.type = 'sell', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_paid")
        )->first();
        $total_invoice = !empty($credit_details->total_invoice) ? $credit_details->total_invoice : 0;
        $invoice_paid = !empty($credit_details->invoice_paid) ? $credit_details->invoice_paid : 0;
        $final_total = $this->num_uf($input['final_total']);
        $curr_total_payment = 0;
        if (!empty($input['payment'])) {
            foreach ($input['payment'] as $payment) {
                $curr_total_payment += $this->num_uf($payment['amount']);
            }
        }
        $curr_due = $final_total - $curr_total_payment;
        $total_due = $total_invoice - $invoice_paid + $curr_due;
        if ($total_due <= $credit_limit) {
            return false;
        }
        return $credit_limit;
    }
    // if sale is overlimit sale return true
    public function isOverLimitCreditSale(
        $input,
        $exclude_transaction_id = null
    ) {
        $credit_limit = Contact::find($input['contact_id'])->credit_limit;
        $customer = Contact::find($input['contact_id']);
        $query = Contact::where('contacts.id', $input['contact_id'])
            ->join('transactions AS t', 'contacts.id', '=', 't.contact_id');
        //Exclude transaction id if update transaction
        if (!empty($exclude_transaction_id)) {
            $query->where('t.id', '!=', $exclude_transaction_id);
        }
        $credit_details =  $query->select(
            DB::raw("SUM(IF(t.type = 'sell', final_total, 0)) as total_invoice"),
            DB::raw("SUM(IF(t.type = 'sell', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_paid")
        )->first();
        $total_invoice = !empty($credit_details->total_invoice) ? $credit_details->total_invoice : 0;
        $invoice_paid = !empty($credit_details->invoice_paid) ? $credit_details->invoice_paid : 0;
        $final_total = $this->num_uf($input['final_total']);
        $curr_total_payment = 0;
        if (!empty($input['payment'])) {
            foreach ($input['payment'] as $payment) {
                $curr_total_payment += $this->num_uf($payment['amount']);
            }
        }
        $curr_due = $final_total - $curr_total_payment;
        $total_due = $total_invoice - $invoice_paid + $curr_due;
        if ($total_due > $credit_limit) {
            return true;
        }
        return false;
    }
    // if sale is overlimit sale return true
    public function getOverLimitAmount(
        $input,
        $exclude_transaction_id = null
    ) {
        $credit_limit = Contact::find($input['contact_id'])->credit_limit;
        $customer = Contact::find($input['contact_id']);
        $query = Contact::where('contacts.id', $input['contact_id'])
            ->join('transactions AS t', 'contacts.id', '=', 't.contact_id');
        //Exclude transaction id if update transaction
        if (!empty($exclude_transaction_id)) {
            $query->where('t.id', '!=', $exclude_transaction_id);
        }
        $credit_details =  $query->select(
            DB::raw("SUM(IF(t.type = 'sell', final_total, 0)) as total_invoice"),
            DB::raw("SUM(IF(t.type = 'sell', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as invoice_paid")
        )->first();
        $total_invoice = !empty($credit_details->total_invoice) ? $credit_details->total_invoice : 0;
        $invoice_paid = !empty($credit_details->invoice_paid) ? $credit_details->invoice_paid : 0;
        $final_total = $this->num_uf($input['final_total']);
        $curr_total_payment = 0;
        if (!empty($input['payment'])) {
            foreach ($input['payment'] as $payment) {
                $curr_total_payment += $this->num_uf($payment['amount']);
            }
        }
        $curr_due = $final_total - $curr_total_payment;
        $total_due = $total_invoice - $invoice_paid + $curr_due;
        return $total_due - $credit_limit;
    }
    /**
     * Creates a new opening balance transaction for a contact
     *
     * @param  int $business_id
     * @param  int $contact_id
     * @param  int $amount
     *
     * @return void
     */
    public function createOpeningBalanceTransactionForPumpOperator($business_id, $pump_operator_id, $amount, $type, $location_id, $transaction_date = null)
    {
        $final_amount = $this->num_uf($amount);
        $ob_data = [
            'business_id' => $business_id,
            'location_id' => $location_id,
            'type' => 'opening_balance',
            'sub_type' => $type,
            'status' => 'final',
            'payment_status' => 'due',
            'pump_operator_id' => $pump_operator_id,
            'transaction_date' => \Carbon::parse($transaction_date) ?: \Carbon::now(),
            'total_before_tax' => $final_amount,
            'final_total' => $final_amount,
            'created_by' => request()->session()->get('user.id')
        ];
        //Update reference count
        $ob_ref_count = $this->setAndGetReferenceCount('opening_balance');
        //Generate reference number
        $ob_data['ref_no'] = $this->generateReferenceNumber('opening_balance', $ob_ref_count);
        //Create opening balance transaction
        $transaction = Transaction::create($ob_data);
        if ($type == 'shortage') {
            $transaction_type = 'debit';
            $account_id = $this->account_exist_return_id('Accounts Receivable');
        }
        if ($type == 'excess') {
            $transaction_type = 'credit';
            $account_id = $this->account_exist_return_id('Accounts Payable');
        }
        $account_transaction_data = [
            'amount' => abs($transaction->final_total),
            'account_id' => $account_id,
            'type' => $transaction_type,
            'sub_type' => 'ledger_show',
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => null,
            'note' => null
        ];
        AccountTransaction::createAccountTransaction($account_transaction_data);

        if ($type == 'shortage') {
            $opening_balance_transaction_type = 'credit';
        }
        if ($type == 'excess') {
            $opening_balance_transaction_type = 'debit';
        }
        $opening_balance_equity_id = $this->account_exist_return_id('Opening Balance Equity Account');
        $this->createAccountTransaction($transaction, $opening_balance_transaction_type, $opening_balance_equity_id, abs($transaction->final_total));
    }
    
    public function createOpeningBalanceTransactionForBakeryUser($business_id, $pump_operator_id, $amount, $type, $location_id, $transaction_date = null)
    {
        $final_amount = $this->num_uf($amount);
        $ob_data = [
            'business_id' => $business_id,
            'location_id' => $location_id,
            'type' => 'bakery_user_opening_balance',
            'sub_type' => $type,
            'status' => 'final',
            'payment_status' => 'due',
            'pump_operator_id' => $pump_operator_id,
            'transaction_date' => \Carbon::parse($transaction_date) ?: \Carbon::now(),
            'total_before_tax' => $final_amount,
            'final_total' => $final_amount,
            'created_by' => request()->session()->get('user.id')
        ];
        //Update reference count
        $ob_ref_count = $this->setAndGetReferenceCount('opening_balance');
        //Generate reference number
        $ob_data['ref_no'] = $this->generateReferenceNumber('opening_balance', $ob_ref_count);
        //Create opening balance transaction
        $transaction = Transaction::create($ob_data);
        if ($type == 'shortage') {
            $transaction_type = 'debit';
            $account_id = $this->account_exist_return_id('Accounts Receivable');
        }
        if ($type == 'excess') {
            $transaction_type = 'credit';
            $account_id = $this->account_exist_return_id('Accounts Payable');
        }
        $account_transaction_data = [
            'amount' => abs($transaction->final_total),
            'account_id' => $account_id,
            'type' => $transaction_type,
            'sub_type' => 'ledger_show',
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => null,
            'note' => null
        ];
        AccountTransaction::createAccountTransaction($account_transaction_data);

        if ($type == 'shortage') {
            $opening_balance_transaction_type = 'credit';
        }
        if ($type == 'excess') {
            $opening_balance_transaction_type = 'debit';
        }
        $opening_balance_equity_id = $this->account_exist_return_id('Opening Balance Equity Account');
        $this->createAccountTransaction($transaction, $opening_balance_transaction_type, $opening_balance_equity_id, abs($transaction->final_total));
    }
    
    public function updateOpeningBalanceTransactionForPumpOperator($business_id, $pump_operator_id, $amount, $type, $location_id, $transaction_date = null)
    {
        $final_amount = $this->num_uf($amount);
        
        $opening_bal = Transaction::where([
                    'type' => 'opening_balance',
                    'pump_operator_id' => $pump_operator_id,
                    'business_id' => $business_id
                ])->first();
                
        if(!empty($opening_bal)){
            AccountTransaction::where('transaction_id',$opening_bal->id)->forcedelete();
        }
        
            // print_r($amount);
        // if(!empty($amount)){
            
            $ob_data = [
                'business_id' => $business_id,
                'location_id' => $location_id,
                'type' => 'opening_balance',
                'sub_type' => $type,
                'status' => 'final',
                'payment_status' => 'due',
                'pump_operator_id' => $pump_operator_id,
                'transaction_date' => \Carbon::parse($transaction_date) ?: \Carbon::now(),
                'total_before_tax' => $final_amount,
                'final_total' => $final_amount,
                'created_by' => request()->session()->get('user.id')
            ];
            if(!empty($opening_bal)){
                Transaction::where([
                    'type' => 'opening_balance',
                    'pump_operator_id' => $pump_operator_id,
                    'business_id' => $business_id
                ])->update($ob_data);
                
                $transaction = $opening_bal;
            }else{
                
                //Update reference count
                $ob_ref_count = $this->setAndGetReferenceCount('opening_balance');
                //Generate reference number
                $ob_data['ref_no'] = $this->generateReferenceNumber('opening_balance', $ob_ref_count);
                //Create opening balance transaction
                $transaction = Transaction::create($ob_data);
            }
        
            
            if ($type == 'shortage') {
                $transaction_type = 'debit';
                $account_id = $this->account_exist_return_id('Accounts Receivable');
            }
            if ($type == 'excess') {
                $transaction_type = 'credit';
                $account_id = $this->account_exist_return_id('Accounts Payable');
            }
            $account_transaction_data = [
                'amount' => abs($transaction->final_total),
                'account_id' => $account_id,
                'type' => $transaction_type,
                'sub_type' => 'ledger_show',
                'operation_date' => $transaction->transaction_date,
                'created_by' => $transaction->created_by,
                'transaction_id' => $transaction->id,
                'transaction_payment_id' => null,
                'note' => null
            ];
            AccountTransaction::createAccountTransaction($account_transaction_data);
    
            if ($type == 'shortage') {
                $opening_balance_transaction_type = 'credit';
            }
            if ($type == 'excess') {
                $opening_balance_transaction_type = 'debit';
            }
            $opening_balance_equity_id = $this->account_exist_return_id('Opening Balance Equity Account');
            $this->createAccountTransaction($transaction, $opening_balance_transaction_type, $opening_balance_equity_id, abs($transaction->final_total));
            
        // }else{
        //     if(!empty($opening_bal)){
        //         $opening_bal->forcedelete();
        //     }
        // }
        

            
    }
    
    public function updateOpeningBalanceTransactionForBakeryUser($business_id, $pump_operator_id, $amount, $type, $location_id, $transaction_date = null)
    {
        $final_amount = $this->num_uf($amount);
        
        $opening_bal = Transaction::where([
                    'type' => 'bakery_user_opening_balance',
                    'pump_operator_id' => $pump_operator_id,
                    'business_id' => $business_id
                ])->first();
                
        if(!empty($opening_bal)){
            AccountTransaction::where('transaction_id',$opening_bal->id)->forcedelete();
        }
        
        if(!empty($amount)){
            
            $ob_data = [
                'business_id' => $business_id,
                'location_id' => $location_id,
                'type' => 'bakery_user_opening_balance',
                'sub_type' => $type,
                'status' => 'final',
                'payment_status' => 'due',
                'pump_operator_id' => $pump_operator_id,
                'transaction_date' => \Carbon::parse($transaction_date) ?: \Carbon::now(),
                'total_before_tax' => $final_amount,
                'final_total' => $final_amount,
                'created_by' => request()->session()->get('user.id')
            ];
            
            if(!empty($opening_bal)){
                Transaction::where([
                    'type' => 'bakery_user_opening_balance',
                    'pump_operator_id' => $pump_operator_id,
                    'business_id' => $business_id
                ])->update($ob_data);
                
                $transaction = $opening_bal;
            }else{
                
                //Update reference count
                $ob_ref_count = $this->setAndGetReferenceCount('opening_balance');
                //Generate reference number
                $ob_data['ref_no'] = $this->generateReferenceNumber('opening_balance', $ob_ref_count);
                //Create opening balance transaction
                $transaction = Transaction::create($ob_data);
            }
        
            
            if ($type == 'shortage') {
                $transaction_type = 'debit';
                $account_id = $this->account_exist_return_id('Accounts Receivable');
            }
            if ($type == 'excess') {
                $transaction_type = 'credit';
                $account_id = $this->account_exist_return_id('Accounts Payable');
            }
            $account_transaction_data = [
                'amount' => abs($transaction->final_total),
                'account_id' => $account_id,
                'type' => $transaction_type,
                'sub_type' => 'ledger_show',
                'operation_date' => $transaction->transaction_date,
                'created_by' => $transaction->created_by,
                'transaction_id' => $transaction->id,
                'transaction_payment_id' => null,
                'note' => null
            ];
            AccountTransaction::createAccountTransaction($account_transaction_data);
    
            if ($type == 'shortage') {
                $opening_balance_transaction_type = 'credit';
            }
            if ($type == 'excess') {
                $opening_balance_transaction_type = 'debit';
            }
            $opening_balance_equity_id = $this->account_exist_return_id('Opening Balance Equity Account');
            $this->createAccountTransaction($transaction, $opening_balance_transaction_type, $opening_balance_equity_id, abs($transaction->final_total));
            
        }else{
            if(!empty($opening_bal)){
                $opening_bal->forcedelete();
            }
        }
        

            
    }
    
    /**
     * Creates a new opening balance transaction for a contact
     *
     * @param  int $business_id
     * @param  int $contact_id
     * @param  int $amount
     *
     * @return void
     */
    public function createOpeningBalanceTransaction($business_id, $contact_id, $amount, $transaction_date = null,$invoice_no = null)
    {
        $business_location = BusinessLocation::where('business_id', $business_id)
            ->first();
        $contact = Contact::where('id', $contact_id)->first();
        $final_amount = $this->num_uf($amount);
        $ob_data = [
            'business_id' => $business_id,
            'location_id' => $business_location->id,
            'type' => 'opening_balance',
            'status' => 'final',
            'payment_status' => 'due',
            'invoice_no' => !empty($invoice_no) ? $invoice_no : null,
            'contact_id' => $contact_id,
            'transaction_date' => !empty($transaction_date) ? \Carbon::parse($transaction_date)->format('Y-m-d') : \Carbon::now(),
            'total_before_tax' => $final_amount,
            'final_total' => $final_amount,
            'created_by' => request()->session()->get('user.id')
        ];
        //Update reference count
        $ob_ref_count = $this->setAndGetReferenceCount('opening_balance');
        //Generate reference number
        $ob_data['ref_no'] = $this->generateReferenceNumber('opening_balance', $ob_ref_count);
        //Create opening balance transaction
        $transaction = Transaction::create($ob_data);
        if ($contact->type == 'supplier') {
            if ($final_amount > 0) {
                $type = 'credit';
            }
            if ($final_amount < 0) {
                $type = 'debit';
            }
            $account_id = $this->account_exist_return_id('Accounts Payable');
        }
        if ($contact->type == 'customer') {
            if ($final_amount > 0) {
                $type = 'debit';
            }
            if ($final_amount < 0) {
                $type = 'credit';
            }
            $account_id = $this->account_exist_return_id('Accounts Receivable');
        }
        $account_transaction_data = [
            'amount' => abs($transaction->final_total),
            'contact_id' => $contact_id,
            'account_id' => $account_id,
            'type' => $type,
            'sub_type' => 'ledger_show',
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => null,
            'note' => null
        ];
        AccountTransaction::createAccountTransaction($account_transaction_data);
        ContactLedger::createContactLedger($account_transaction_data);
        
        if ($contact->type == 'customer') {
            if ($final_amount > 0) {
                $type = 'credit';
            }
            if ($final_amount < 0) {
                $type = 'debit';
            }
        } else {
            if ($final_amount > 0) {
                $type = 'debit';
            }
            if ($final_amount < 0) {
                $type = 'credit';
            }
        }
        
        
        $opening_balance_equity_id = $this->account_exist_return_id('Opening Balance Equity Account');
        $this->createAccountTransaction($transaction, $type, $opening_balance_equity_id, abs($transaction->final_total));
       
        
        if ($final_amount < 0) {
            if ($contact->type == 'customer') {
                $type = 'credit';
                $customer_over_payment_id = $this->account_exist_return_id('Customer Over Payments');
                $this->createAccountTransaction($transaction, $type, $customer_over_payment_id, abs($transaction->final_total));
            } else {
                $type = 'debit';
                $supplier_over_payment_id = $this->account_exist_return_id('Supplier Over Payments');
                $this->createAccountTransaction($transaction, $type, $supplier_over_payment_id, abs($transaction->final_total));
            }
        }
    }
    public function adjustAdvancePayments($transaction, $paying_amount, $business_id)
    {
        // get advance amounts to supplier
        $due_amount = $transaction->final_total - $paying_amount;  // amount to pay
        $advance_remainings = Transaction::where('business_id', $business_id)->where('contact_id', $transaction->contact_id)->where('type', 'advance_payment')->where('advance_remaining', '>', 0)->select('advance_remaining', 'id')->get();
        foreach ($advance_remainings as $advance_remaining) {
            if ($due_amount > 0) {
                $this_advance = $advance_remaining->advance_remaining;
                $this_advance_remaining = 0;
                if ($this_advance >= $due_amount) {
                    $this_advance_remaining = $this_advance - $due_amount;
                    $due_amount = 0;
                    Transaction::where('id', $advance_remaining->id)->update(['advance_remaining' => $this_advance_remaining]);
                    break;
                }
                if ($this_advance < $due_amount) {
                    $this_advance_remaining = 0;
                    $due_amount = $due_amount - $this_advance;
                    Transaction::where('id', $advance_remaining->id)->update(['advance_remaining' => $this_advance_remaining]);
                }
            }
        }
        return  $due_amount;
    }
    /**
     * Creates a new advance payment transaction for a contact
     *
     * @param  int $business_id
     * @param  int $contact_id
     * @param  int $amount
     *
     * @return object
     */
    public function createAdvancePaymentTransaction($business_id, $contact_id, $amount, $account_id, $payment_type, $transaction_date, $current_liability_account = null, $inputs = null)
    {
        $contact = Contact::findOrFail($contact_id);
        $business_location = BusinessLocation::where('business_id', $business_id)
            ->first();
        $final_amount = $this->num_uf($amount);
        $ob_data = [
            'business_id' => $business_id,
            'location_id' => $business_location->id,
            'type' => $payment_type,
            'status' => 'final',
            'payment_status' => 'paid',
            'contact_id' => $contact_id,
            'transaction_date' => $transaction_date,
            'total_before_tax' => $final_amount,
            'final_total' => $final_amount,
            'created_by' => request()->session()->get('user.id')
        ];
        if ($payment_type == 'advance_payment') {
            $ob_data['advance_remaining'] = $final_amount;
        }
        //Update reference count
        $ob_ref_count = $this->setAndGetReferenceCount($payment_type);
        //Generate reference number
        $ob_data['ref_no'] = $this->generateReferenceNumber($payment_type, $ob_ref_count);
        //Create opening balance transaction
        $transaction = Transaction::create($ob_data);
        
        $account_transaction_data = [
            'amount' => abs($transaction->final_total),
            'account_id' => $account_id,
            'contact_id' => $contact_id,
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => null,
            'note' => null,
            'post_dated_cheque' => $inputs['post_dated_cheque'] ?? 0,
            'update_post_dated_cheque' => $inputs['update_post_dated_cheque'] ?? 0,
        ];
        
        $issued_post_dated = $this->account_exist_return_id('Issued Post Dated Cheques'); 
        $post_dated = $this->account_exist_return_id('Post Dated Cheques'); 
        
        if ($payment_type == 'advance_payment') {
            if ($contact->type == 'customer') {
                $account_transaction_data['type'] = 'debit';
                
                if(!empty($inputs) && !empty($inputs['update_post_dated_cheque'])){
                    $account_transaction_data['related_account_id'] = $account_id;
                    $account_transaction_data['account_id'] = $post_dated;
                }
                
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['type'] = 'credit';
                
                if(!empty($inputs) && !empty($inputs['update_post_dated_cheque'])){
                    $account_transaction_data['related_account_id'] = $account_id;
                    $account_transaction_data['account_id'] = $issued_post_dated;
                }
                
            }
        }
        if ($payment_type == 'security_deposit') {
            if ($contact->type == 'customer') {
                $account_transaction_data['type'] = 'debit';
                
                if(!empty($inputs) && !empty($inputs['update_post_dated_cheque'])){
                    $account_transaction_data['related_account_id'] = $account_id;
                    $account_transaction_data['account_id'] = $post_dated;
                }
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['type'] = 'credit';
                
                if(!empty($inputs) && !empty($inputs['update_post_dated_cheque'])){
                    $account_transaction_data['related_account_id'] = $account_id;
                    $account_transaction_data['account_id'] = $issued_post_dated;
                }
            }
        }
        
        if ($payment_type == 'security_deposit_refund') {
            if ($contact->type == 'customer') {
                $account_transaction_data['type'] = 'credit';
                
                if(!empty($inputs) && !empty($inputs['update_post_dated_cheque'])){
                    $account_transaction_data['related_account_id'] = $account_id;
                    $account_transaction_data['account_id'] = $issued_post_dated;
                }
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['type'] = 'debit';
                
                if(!empty($inputs) && !empty($inputs['update_post_dated_cheque'])){
                    $account_transaction_data['related_account_id'] = $account_id;
                    $account_transaction_data['account_id'] = $post_dated;
                }
            }
        }
        
        if ($payment_type == 'deposit') {
            $account_transaction_data['type'] = 'debit';
        }
        
        // dd($account_transaction_data);
        
        AccountTransaction::createAccountTransaction($account_transaction_data);
        
        
        if ($payment_type == 'advance_payment') {
            if ($contact->type == 'customer') {
                $account_transaction_data['account_id'] = $this->account_exist_return_id('Accounts Receivable');
                $account_transaction_data['type'] = 'credit';
                ContactLedger::createContactLedger($account_transaction_data);
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['account_id'] = $this->account_exist_return_id('Advances to Suppliers');
                $account_transaction_data['type'] = 'debit';
                ContactLedger::createContactLedger($account_transaction_data);
            }
        }
        if ($payment_type == 'security_deposit') {
            if ($contact->type == 'customer') {
                $account_transaction_data['account_id'] = $current_liability_account;
                $account_transaction_data['type'] = 'credit';
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['account_id'] = $this->account_exist_return_id('Company Deposits');
                $account_transaction_data['type'] = 'debit';
            }
        }
        
        if ($payment_type == 'security_deposit_refund') {
            if ($contact->type == 'customer') {
                $account_transaction_data['account_id'] = $current_liability_account;
                $account_transaction_data['type'] = 'debit';
            }
            if ($contact->type == 'supplier') {
                $account_transaction_data['account_id'] = $current_liability_account;
                $account_transaction_data['type'] = 'credit';
            }
        }
        
        if ($payment_type == 'deposit') {
            $account_transaction_data['account_id'] = $this->account_exist_return_id('Customer Deposits');
            $account_transaction_data['type'] = 'credit';
        }
        AccountTransaction::createAccountTransaction($account_transaction_data);
        return $transaction;
    }
    /**
     * Creates a new refund payment transaction for a contact
     *
     * @param  int $business_id
     * @param  int $contact_id
     * @param  int $amount
     *
     * @return object
     */
    public function createRefundPaymentTransaction($business_id, $contact_id, $amount, $account_id, $payment_type, $transaction_date,$cheque_number = null, $bank_name, $cheque_date)
    {
        $business_location = BusinessLocation::where('business_id', $business_id)
            ->first();
        $final_amount = $this->num_uf($amount);
        if ($payment_type == 'cheque_return') {
            $payment_status = 'due';
        } else {
            $payment_status = 'paid';
        }
        $cheque_return_charges = !empty(request()->cheque_return_charges) ? $this->num_uf(request()->cheque_return_charges) : 0;
        $ob_data = [
            'business_id' => $business_id,
            'location_id' => $business_location->id,
            'type' => $payment_type,
            'status' => 'final',
            'payment_status' => $payment_status,
            'contact_id' => $contact_id,
            'transaction_date' => $transaction_date,
            'total_before_tax' => $final_amount,
            // 'final_total' => $final_amount + $cheque_return_charges, //adding $cheque_return_charges to final total
            'final_total' => $final_amount, //adding $cheque_return_charges to final total gave wrong amount value in account transactions and contact ledger tables
            'cheque_return_charges' => $cheque_return_charges,  // cheque return charges amount
            'invoice_no' => request()->sale_invoice_bill_number,  // invoice no for refund
            'created_by' => request()->session()->get('user.id')
        ];
        //Update reference count
        $ob_ref_count = $this->setAndGetReferenceCount($payment_type);
        //Generate reference number
        $ob_data['ref_no'] = $this->generateReferenceNumber($payment_type, $ob_ref_count);
        //Create opening balance transaction
        $transaction = Transaction::create($ob_data);
        $account_transaction_data = [
            'amount' => abs($transaction->final_total),
            'account_id' => $account_id,
            'contact_id' => $contact_id,
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => null,
            'cheque_number' => $cheque_number,
            'note' => null,
            'bank_name' => $bank_name,
            'cheque_date' => $cheque_date,
        ];
        //Update reference count
        $cr_ref_count = $this->setAndGetReferenceCount($payment_type);
         //cheque_ref_no
        $account_transaction_data['cheque_ref_no'] = $this->generateReferenceNumber($payment_type, $cr_ref_count);
        
        if ($payment_type == 'refund' || $payment_type == 'cheque_return') {
            $account_transaction_data['type'] = 'credit';
            AccountTransaction::createAccountTransaction($account_transaction_data);
            
            $account_transaction_data['account_id'] = $this->account_exist_return_id('Accounts Receivable');
            $account_transaction_data['type'] = 'debit';
            
            AccountTransaction::createAccountTransaction($account_transaction_data);
            ContactLedger::createContactLedger($account_transaction_data);
        }
        //ledger and account transaction for cheque return charges amount
        if ($payment_type == 'cheque_return') {
            if(!empty(request()->cheque_return_charges)){
                $account_transaction_data['amount'] = !empty(request()->cheque_return_charges) ? request()->cheque_return_charges : 0;
                $account_transaction_data['account_id'] = $this->account_exist_return_id('Accounts Receivable');
                $account_transaction_data['type'] = 'debit';
                AccountTransaction::createAccountTransaction($account_transaction_data);
                $account_transaction_data['sub_type'] = 'cheque_return_charges';
                ContactLedger::createContactLedger($account_transaction_data);
                $account_transaction_data['account_id'] = $this->account_exist_return_id('Cheque Return Income');
                $account_transaction_data['type'] = 'credit';
                AccountTransaction::createAccountTransaction($account_transaction_data);
            }
                
        }
        return $transaction;
    }
    /**
     * Updates quantity sold in purchase line for sell return
     *
     * @param  obj $sell_line
     * @param  decimal $new_quantity
     * @param  decimal $old_quantity
     *
     * @return void
     */
    public function updateQuantitySoldFromSellLine($sell_line, $new_quantity, $old_quantity)
    {
        $qty_difference = $this->num_uf($new_quantity) - $this->num_uf($old_quantity);
        if ($qty_difference != 0) {
            $qty_left_to_update = $qty_difference;
            $sell_line_purchase_lines = TransactionSellLinesPurchaseLines::where('sell_line_id', $sell_line->id)->get();
            //Return from each purchase line
            foreach ($sell_line_purchase_lines as $tslpl) {
                //If differnce is +ve decrease quantity sold
                if ($qty_difference > 0) {
                    if ($tslpl->qty_returned < $tslpl->quantity) {
                        //Quantity that can be returned from sell line purchase line
                        $tspl_qty_left_to_return = $tslpl->quantity - $tslpl->qty_returned;
                        $purchase_line = PurchaseLine::find($tslpl->purchase_line_id);
                        if ($qty_left_to_update <= $tspl_qty_left_to_return) {
                            $purchase_line->quantity_sold -= $qty_left_to_update;
                            $purchase_line->save();
                            $tslpl->qty_returned += $qty_left_to_update;
                            $tslpl->save();
                            break;
                        } else {
                            $purchase_line->quantity_sold -= $tspl_qty_left_to_return;
                            $purchase_line->save();
                            $tslpl->qty_returned += $tspl_qty_left_to_return;
                            $tslpl->save();
                            $qty_left_to_update -= $tspl_qty_left_to_return;
                        }
                    }
                } //If differnce is -ve increase quantity sold
                elseif ($qty_difference < 0) {
                    $purchase_line = PurchaseLine::find($tslpl->purchase_line_id);
                    $tspl_qty_to_return = $tslpl->qty_returned + $qty_left_to_update;
                    if ($tspl_qty_to_return >= 0) {
                        $purchase_line->quantity_sold -= $qty_left_to_update;
                        $purchase_line->save();
                        $tslpl->qty_returned += $qty_left_to_update;
                        $tslpl->save();
                        break;
                    } else {
                        $purchase_line->quantity_sold += $tslpl->quantity;
                        $purchase_line->save();
                        $tslpl->qty_returned = 0;
                        $tslpl->save();
                        $qty_left_to_update += $tslpl->quantity;
                    }
                }
            }
        }
    }
    /**
     * Check if return exist for a particular purchase or sell
     * @param id $transacion_id
     *
     * @return boolean
     */
    public function isReturnExist($transacion_id)
    {
        return Transaction::where('return_parent_id', $transacion_id)->exists();
    }
    /**
     * Recalculates sell line data according to subunit data
     *
     * @param integer $unit_id
     *
     * @return array
     */
    public function recalculateSellLineTotals($business_id, $sell_line)
    {
        $unit_details = $this->getSubUnits($business_id, $sell_line->product->unit->id);
        $sub_unit = null;
        $sub_unit_id = $sell_line->sub_unit_id;
        foreach ($unit_details as $key => $value) {
            if ($key == $sub_unit_id) {
                $sub_unit = $value;
            }
        }
        if (!empty($sub_unit)) {
            $multiplier = !empty($sub_unit['multiplier']) ? $sub_unit['multiplier'] : 1;
            $sell_line->quantity = $sell_line->quantity / $multiplier;
            $sell_line->unit_price_before_discount = $sell_line->unit_price_before_discount * $multiplier;
            $sell_line->unit_price = $sell_line->unit_price * $multiplier;
            $sell_line->unit_price_inc_tax = $sell_line->unit_price_inc_tax * $multiplier;
            $sell_line->item_tax = $sell_line->item_tax * $multiplier;
            $sell_line->quantity_returned = $sell_line->quantity_returned / $multiplier;
            $sell_line->unit_details = $unit_details;
        }
        return $sell_line;
    }
    /**
     * Retrieves sum of due amount of a contact
     * @param int $contact_id
     *
     * @return mixed
     */
    public function getContactDue($contact_id)
    {
        $contact_payments = Contact::where('contacts.id', $contact_id)
            ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->whereIn('t.type', ['sell', 'opening_balance'])
            ->where('is_customer_order', 0)
            ->select(
                DB::raw("SUM(IF(t.status = 'final' AND t.type = 'sell', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.status = 'final' AND t.type = 'sell', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
            )->first();
        $due = $contact_payments->total_invoice - $contact_payments->total_paid + $contact_payments->opening_balance - $contact_payments->opening_balance_paid;
        return $due;
    }
    /**
     * Retrieves sum of due amount of a contact
     * @param int $customer_id
     *
     *
     * @return mixed
     */
    public function getGeneralCustomerDue($contact_id)
    {
        $contact_payments = Contact::where('contacts.id', $contact_id)
            ->join('transactions AS t', 'contacts.id', '=', 't.contact_id')
            ->whereIn('t.type', ['sell', 'opening_balance'])
            ->where('is_customer_order', 1)
            ->select(
                DB::raw("SUM(IF(t.status = 'final' AND t.type = 'sell', final_total, 0)) as total_invoice"),
                DB::raw("SUM(IF(t.status = 'final' AND t.type = 'sell', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as total_paid"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                DB::raw("SUM(IF(t.type = 'opening_balance', (SELECT SUM(amount) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid")
            )->first();
        $due = $contact_payments->total_invoice - $contact_payments->total_paid + $contact_payments->opening_balance - $contact_payments->opening_balance_paid;
        return $due;
    }
    /**
     * Check if lot number is used in any sell
     * @param obj $transaction
     *
     * @return boolean
     */
    public function isLotUsed($transaction)
    {
        foreach ($transaction->purchase_lines as $purchase_line) {
            $exists = TransactionSellLine::where('lot_no_line_id', $purchase_line->id)->exists();
            if ($exists) {
                return true;
            }
        }
        return false;
    }
    /**
     * Creates recurring invoice from existing sale
     * @param obj $transaction, bool $is_draft
     *
     * @return obj $recurring_invoice
     */
    public function createRecurringInvoice($transaction, $is_draft = false)
    {
        $data = $transaction->toArray();
        unset($data['id']);
        unset($data['created_at']);
        unset($data['updated_at']);
        if ($is_draft) {
            $data['status'] = 'draft';
        }
        $data['payment_status'] = 'due';
        $data['recur_parent_id'] = $transaction->id;
        $data['is_recurring'] = 0;
        $data['recur_interval'] = null;
        $data['recur_interval_type'] = null;
        $data['recur_repetitions'] = 0;
        $data['recur_stopped_on'] = null;
        $data['transaction_date'] = \Carbon::now();
        if (isset($data['invoice_token'])) {
            $data['invoice_token'] = null;
        }
        if (isset($data['woocommerce_order_id'])) {
            $data['woocommerce_order_id'] = null;
        }
        if (isset($data['recurring_invoices'])) {
            unset($data['recurring_invoices']);
        }
        if (isset($data['sell_lines'])) {
            unset($data['sell_lines']);
        }
        if (isset($data['business'])) {
            unset($data['business']);
        }
        $data['invoice_no'] = $this->getInvoiceNumber($transaction->business_id, $data['status'], $data['location_id']);
        $recurring_invoice = Transaction::create($data);
        $recurring_sell_lines = [];
        foreach ($transaction->sell_lines as $sell_line) {
            $sell_line_data = $sell_line->toArray();
            unset($sell_line_data['id']);
            unset($sell_line_data['created_at']);
            unset($sell_line_data['updated_at']);
            unset($sell_line_data['product']);
            if (isset($sell_line_data['quantity_returned'])) {
                unset($sell_line_data['quantity_returned']);
            }
            if (isset($sell_line_data['lot_no_line_id'])) {
                unset($sell_line_data['lot_no_line_id']);
            }
            if (isset($sell_line_data['woocommerce_line_items_id'])) {
                unset($sell_line_data['woocommerce_line_items_id']);
            }
            $recurring_sell_lines[] = $sell_line_data;
        }
        $recurring_invoice->sell_lines()->createMany($recurring_sell_lines);
        return $recurring_invoice;
    }
    /**
     * Retrieves and sum total amount paid for a transaction
     * @param int $transaction_id
     *
     */
    public function getTotalAmountPaid($transaction_id)
    {
        $paid = TransactionPayment::where(
            'transaction_id',
            $transaction_id
        )->sum('amount');
        return $paid;
    }
    /**
     * Calculates transaction totals for the given transaction types
     *
     * @param  int $business_id
     * @param  array $transaction_types
     * available types = ['purchase_return', 'sell_return', 'expense',
     * 'stock_adjustment', 'sell_transfer', 'purchase', 'sell']
     * @param  string $start_date = null
     * @param  string $end_date = null
     * @param  int $location_id = null
     * @param  int $created_by = null
     *
     * @return array
     */
    public function getTransactionTotals(
        $business_id,
        $transaction_types,
        $start_date = null,
        $end_date = null,
        $location_id = null,
        $created_by = null
    ) {
        $query = Transaction::where('business_id', $business_id);
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
        }
        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        //Filter by created_by
        if (!empty($created_by)) {
            $query->where('transactions.created_by', $created_by);
        }
        if (in_array('purchase_return', $transaction_types)) {
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='purchase_return', final_total, 0)) as total_purchase_return_inc_tax"),
                DB::raw("SUM(IF(transactions.type='purchase_return', total_before_tax, 0)) as total_purchase_return_exc_tax")
            );
        }
        if (in_array('sell_return', $transaction_types)) {
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='sell_return', final_total, 0)) as total_sell_return_inc_tax"),
                DB::raw("SUM(IF(transactions.type='sell_return', total_before_tax, 0)) as total_sell_return_exc_tax")
            );
        }
        if (in_array('sell_transfer', $transaction_types)) {
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='sell_transfer', shipping_charges, 0)) as total_transfer_shipping_charges")
            );
        }
        if (in_array('expense', $transaction_types)) {
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='expense', final_total, 0)) as total_expense")
            );
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='settlement' AND transactions.sub_type='expense', final_total, 0)) as settlement_expense")
            );
        }
        if (in_array('payroll', $transaction_types)) {
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='payroll', final_total, 0)) as total_payroll")
            );
        }
        if (in_array('stock_adjustment', $transaction_types)) {
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='stock_adjustment', final_total, 0)) as total_adjustment"),
                DB::raw("SUM(IF(transactions.type='stock_adjustment' AND transactions.stock_adjustment_type='decrease', final_total, 0)) as decrease_stock_adjustment"),
                DB::raw("SUM(IF(transactions.type='stock_adjustment' AND transactions.stock_adjustment_type='increase', final_total, 0)) as increase_stock_adjustment"),
                DB::raw("SUM(IF(transactions.type='stock_adjustment', total_amount_recovered, 0)) as total_recovered")
            );
        }
        if (in_array('purchase', $transaction_types)) {
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='purchase', IF(discount_type = 'percentage', COALESCE(discount_amount, 0)*total_before_tax/100, COALESCE(discount_amount, 0)), 0)) as total_purchase_discount")
            );
        }
        if (in_array('sell', $transaction_types)) {
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='sell' AND transactions.status='final', IF(discount_type = 'percentage', COALESCE(discount_amount, 0)*total_before_tax/100, COALESCE(discount_amount, 0)), 0)) as total_sell_discount"),
                DB::raw("SUM(IF(transactions.type='sell' AND transactions.status='final', rp_redeemed_amount, 0)) as total_reward_amount")
            );
        }
        if (in_array('credit_sales_details', $transaction_types)) {
            $query->addSelect(
                DB::raw("SUM(IF(transactions.type='sell' AND transactions.is_credit_sale='1', transactions.final_total, 0)) as total_credit_sales"),
                DB::raw("SUM(IF(transactions.type='sell' AND transactions.is_credit_sale='1' AND transactions.payment_status='paid', transactions.final_total, 0)) as total_credit_sales_paid"),
                DB::raw("SUM(IF(transactions.type='sell' AND transactions.is_credit_sale='1' AND transactions.payment_status='due', transactions.final_total, 0)) as total_credit_sales_due")
            );
        }
        $transaction_totals = $query->first();
        $output = [];
        if (in_array('purchase_return', $transaction_types)) {
            $output['total_purchase_return_inc_tax'] = !empty($transaction_totals->total_purchase_return_inc_tax) ?
                $transaction_totals->total_purchase_return_inc_tax : 0;
            $output['total_purchase_return_exc_tax'] =
                !empty($transaction_totals->total_purchase_return_exc_tax) ?
                $transaction_totals->total_purchase_return_exc_tax : 0;
        }
        if (in_array('sell_return', $transaction_types)) {
            $output['total_sell_return_inc_tax'] =
                !empty($transaction_totals->total_sell_return_inc_tax) ?
                $transaction_totals->total_sell_return_inc_tax : 0;
            $output['total_sell_return_exc_tax'] =
                !empty($transaction_totals->total_sell_return_exc_tax) ?
                $transaction_totals->total_sell_return_exc_tax : 0;
        }
        if (in_array('sell_transfer', $transaction_types)) {
            $output['total_transfer_shipping_charges'] =
                !empty($transaction_totals->total_transfer_shipping_charges) ?
                $transaction_totals->total_transfer_shipping_charges : 0;
        }
        if (in_array('expense', $transaction_types)) {
            $output['total_expense'] =
                !empty($transaction_totals->total_expense) ?
                $transaction_totals->total_expense : 0;
            $output['settlement_expense'] =
                !empty($transaction_totals->settlement_expense) ?
                $transaction_totals->settlement_expense : 0;
        }
        if (in_array('payroll', $transaction_types)) {
            $output['total_payroll'] =
                !empty($transaction_totals->total_payroll) ?
                $transaction_totals->total_payroll : 0;
        }
        if (in_array('stock_adjustment', $transaction_types)) {
            $output['total_adjustment'] =
                !empty($transaction_totals->total_adjustment) ?
                $transaction_totals->total_adjustment : 0;
            $output['decrease_stock_adjustment'] =
                !empty($transaction_totals->decrease_stock_adjustment) ?
                $transaction_totals->decrease_stock_adjustment : 0;
            $output['increase_stock_adjustment'] =
                !empty($transaction_totals->increase_stock_adjustment) ?
                $transaction_totals->increase_stock_adjustment : 0;
            $output['total_recovered'] =
                !empty($transaction_totals->total_recovered) ?
                $transaction_totals->total_recovered : 0;
        }
        if (in_array('purchase', $transaction_types)) {
            $output['total_purchase_discount'] =
                !empty($transaction_totals->total_purchase_discount) ?
                $transaction_totals->total_purchase_discount : 0;
        }
        if (in_array('sell', $transaction_types)) {
            $output['total_sell_discount'] =
                !empty($transaction_totals->total_sell_discount) ?
                $transaction_totals->total_sell_discount : 0;
            $output['total_reward_amount'] =
                !empty($transaction_totals->total_reward_amount) ?
                $transaction_totals->total_reward_amount : 0;
        }
        if (in_array('credit_sales_details', $transaction_types)) {
            $output['total_credit_sales'] =
                !empty($transaction_totals->total_credit_sales) ?
                $transaction_totals->total_credit_sales : 0;
            $output['total_credit_sales_paid'] =
                !empty($transaction_totals->total_credit_sales_paid) ?
                $transaction_totals->total_credit_sales_paid : 0;
            $output['total_credit_sales_due'] =
                !empty($transaction_totals->total_credit_sales_due) ?
                $transaction_totals->total_credit_sales_due : 0;
        }
        return $output;
    }
    public function getGrossProfit($business_id, $start_date = null, $end_date = null, $location_id = null)
    {
        $query = TransactionSellLinesPurchaseLines::join('transaction_sell_lines
                        as SL', 'SL.id', '=', 'transaction_sell_lines_purchase_lines.sell_line_id')
            ->join('transactions as sale', 'SL.transaction_id', '=', 'sale.id')
            ->join('purchase_lines as PL', 'PL.id', '=', 'transaction_sell_lines_purchase_lines.purchase_line_id')
            ->where('sale.business_id', $business_id);
        if (!empty($start_date) && !empty($end_date) && $start_date != $end_date) {
            $query->whereBetween(DB::raw('sale.transaction_date'), [$start_date, $end_date]);
        }
        if (!empty($start_date) && !empty($end_date) && $start_date == $end_date) {
            $query->whereDate('sale.transaction_date', $end_date);
        }
        //Filter by the location
        if (!empty($location_id)) {
            $query->where('sale.location_id', $location_id);
        }
        $gross_profit_obj = $query->select(DB::raw('SUM(
                        (transaction_sell_lines_purchase_lines.quantity - transaction_sell_lines_purchase_lines.qty_returned) * (SL.unit_price_inc_tax - PL.purchase_price_inc_tax) ) as gross_profit'))
            ->first();
        $gross_profit = !empty($gross_profit_obj->gross_profit) ? $gross_profit_obj->gross_profit : 0;
        //Deduct the sell transaction discounts.
        $transaction_totals = $this->getTransactionTotals($business_id, ['sell'], $start_date, $end_date, $location_id);
        $sell_discount = !empty($transaction_totals['total_sell_discount']) ? $transaction_totals['total_sell_discount'] : 0;
        //KNOWS ISSUE: If products are returned then also the discount gets applied for it.
        return $gross_profit - $sell_discount;
    }
    /**
     * Calculates reward points to be earned from an order
     *
     * @return integer
     */
    public function calculateRewardPoints($business_id, $total)
    {
        if (session()->has('business')) {
            $business = session()->get('business');
        } else {
            $business = Business::find($business_id);
        }
        $total_points = 0;
        if ($business->enable_rp == 1) {
            //check if order total elegible for reward
            if ($business->min_order_total_for_rp > $total) {
                return $total_points;
            }
            $amount_per_unit_point = $business->amount_for_unit_rp;
            $total_points = floor($total / $amount_per_unit_point);
            if (!empty($business->max_rp_per_order) && $business->max_rp_per_order < $total_points) {
                $total_points = $business->max_rp_per_order;
            }
        }
        return $total_points;
    }
    /**
     * Updates reward point of a customer
     *
     * @return void
     */
    public function updateCustomerRewardPoints(
        $customer_id,
        $earned,
        $earned_before = 0,
        $redeemed = 0,
        $redeemed_before = 0
    ) {
        $customer = Contact::find($customer_id);
        //Return if walk in customer
        if ($customer->is_default == 1) {
            return false;
        }
        $total_earned = $earned - $earned_before;
        $total_redeemed = $redeemed - $redeemed_before;
        $diff = $total_earned - $total_redeemed;
        $customer_points = empty($customer->total_rp) ? 0 : $customer->total_rp;
        $total_points = $customer_points + $diff;
        $customer->total_rp = $total_points;
        $customer->total_rp_used += $total_redeemed;
        $customer->save();
    }
    /**
     * Calculates reward points to be redeemed from an order
     *
     * @return array
     */
    public function getRewardRedeemDetails($business_id, $customer_id)
    {
        if (session()->has('business')) {
            $business = session()->get('business');
        } else {
            $business = Business::find($business_id);
        }
        $details = ['points' => 0, 'amount' => 0];
        $customer = Contact::where('business_id', $business_id)
            ->find($customer_id);
        $customer_reward_points = $customer->total_rp;
        //If zero reward point or walk in customer return blank values
        if (empty($customer_reward_points) || $customer->is_default == 1) {
            return $details;
        }
        $min_reward_point_required = $business->min_redeem_point;
        if (!empty($min_reward_point_required) && $customer_reward_points < $min_reward_point_required) {
            return $details;
        }
        $max_redeem_point = $business->max_redeem_point;
        if (!empty($max_redeem_point) && $max_redeem_point <= $customer_reward_points) {
            $customer_reward_points = $max_redeem_point;
        }
        $amount_per_unit_point = $business->redeem_amount_per_unit_rp;
        $equivalent_amount = $customer_reward_points * $amount_per_unit_point;
        $details = ['points' => $customer_reward_points, 'amount' => $equivalent_amount];
        return $details;
    }
    /**
     * Checks whether a reward point date is expired
     *
     * @return boolean
     */
    public function isRewardExpired($date, $business_id)
    {
        if (session()->has('business')) {
            $business = session()->get('business');
        } else {
            $business = Business::find($business_id);
        }
        $is_expired = false;
        if (!empty($business->rp_expiry_period)) {
            $expiry_date = \Carbon::parse($date);
            if ($business->rp_expiry_type == 'month') {
                $expiry_date = $expiry_date->addMonths($business->rp_expiry_period);
            } elseif ($business->rp_expiry_type == 'year') {
                $expiry_date = $expiry_date->addYears($business->rp_expiry_period);
            }
            if ($expiry_date->format('Y-m-d') >= \Carbon::now()->format('Y-m-d')) {
                $is_expired = true;
            }
        }
        return $is_expired;
    }
    /**
     * Calculates total production cost
     *
     * @param  int $business_id
     * @param  string $start_date = null
     * @param  string $end_date = null
     * @param  int $location_id = null
     *
     * @return array
     */
    public function getTotalProductionCost(
        $business_id,
        $start_date = null,
        $end_date = null,
        $location_id = null
    ) {
        $query = Transaction::where('business_id', $business_id)
            ->where('type', 'production_purchase')
            ->where('mfg_is_final', 1);
        //Check for permitted locations of a user
        $permitted_locations = auth()->user()->permitted_locations();
        if ($permitted_locations != 'all') {
            $query->whereIn('transactions.location_id', $permitted_locations);
        }
        if (!empty($start_date) && !empty($end_date)) {
            $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
        }
        if (empty($start_date) && !empty($end_date)) {
            $query->whereDate('transaction_date', '<=', $end_date);
        }
        //Filter by the location
        if (!empty($location_id)) {
            $query->where('transactions.location_id', $location_id);
        }
        $total = $query->select(
            DB::raw('SUM(final_total - ((final_total * 100) / (mfg_production_cost + 100) ) ) as total_production_cost')
        )->first();
        $total_production_cost = !empty($total->total_production_cost) ? $total->total_production_cost : 0;
        return $total_production_cost;
    }
    public function getDefaultAccountId($method_name, $location_id)
    {
        if ($method_name == 'credit_expense' || $method_name == 'credit_purchase') {
            return 0;
        }
        $business_id = request()->session()->get('business.id');
        $account_id = null;
        $defualt_accounts = BusinessLocation::where('business_id', $business_id)->where('id', $location_id)->first();
        if (!empty($defualt_accounts)) {
            $default_payment_accounts = (array) json_decode($defualt_accounts->default_payment_accounts);
            $account_id = $default_payment_accounts[$method_name]->account;
        }
        return $account_id;
    }
    public function createOrUpdateSellLinesSettlement($transaction, $product_id, $variation_id, $location_id, $meter_sale)
    {
        $sell_line_data = array(
            'transaction_id' => $transaction->id,
            'product_id' => $product_id,
            'variation_id' => $variation_id,
            'quantity' => $meter_sale->qty,
            'unit_price_before_discount' => $meter_sale->price,
            'unit_price_inc_tax' => $meter_sale->price,
            'unit_price' => $meter_sale->price,
            'line_discount_type' => 'fixed',
            'line_discount_amount' => !empty($meter_sale->discount) ? $meter_sale->discount : 0,
            'item_tax' => 0.00
        );
        TransactionSellLine::create($sell_line_data);
    }
    
    public function createOrUpdateSellLinesVatBill($transaction, $product_id, $variation_id, $location_id, $sale)
    {
        $sell_line_data = array(
            'transaction_id' => $transaction->id,
            'product_id' => $product_id,
            'variation_id' => $variation_id,
            'quantity' => $sale->qty,
            'unit_price_before_discount' => $sale->unit_price,
            'unit_price_inc_tax' => $sale->unit_price,
            'unit_price' => $sale->unit_price,
            'line_discount_type' => 'fixed',
            'line_discount_amount' => !empty($sale->discount) ? $sale->discount : 0,
            'item_tax' =>!empty($sale->tax) ? $sale->tax : 0,
        );
        TransactionSellLine::create($sell_line_data);
    }
    
    public function getTransactionProductDetail($transaction_id, $transaction_type)
    {
        if ($transaction_type == 'purchase' || $transaction_type == 'purchase_return' || $transaction_type == 'opening_stock') {
            $product = Transaction::leftjoin('purchase_lines', 'transactions.id', 'purchase_lines.transaction_id')
                ->leftjoin('products', 'purchase_lines.product_id', 'products.id')
                ->where('transactions.id', $transaction_id)
                ->select('products.id', 'purchase_lines.id as purchase_line_id', 'enable_stock', 'stock_type', DB::raw('SUM(purchase_lines.quantity * purchase_lines.purchase_price_inc_tax) as amount'))->groupBy('stock_type')->get();
        }
        if ($transaction_type == 'sell' || $transaction_type == 'sell_return') {
            $product = Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
                ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
                ->where('transactions.id', $transaction_id)
                ->select('products.id', 'enable_stock', 'stock_type', DB::raw('SUM(transaction_sell_lines.quantity*variations.dpp_inc_tax) as amount'))->first();
        }
        return $product;
    }
    
    
    public function createCostofGoodsSoldTransaction($transaction, $sub_type = null, $type)
    {
        $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
            ->where('transaction_id', $transaction->id)
            ->select('transaction_sell_lines.*', 'products.category_id', 'products.sub_category_id', 'variations.default_purchase_price','variations.dpp_inc_tax','products.vat_claimed as tax')
            ->get();
        foreach ($sell_lines as $sale) {
            $account_id = $this->account_exist_return_id('Cost of Goods Sold');
            if ($sale->quantity >= 0) { //not include pos page return
                if (!empty($sale->sub_category_id)) {
                    $account_id = $this->getCategoryAccountId($sale->sub_category_id, 'cogs');
                    if (empty($account_id)) {
                        $account_id = $this->getCategoryAccountId($sale->category_id, 'cogs');
                    }
                    if (empty($account_id)) {
                        $account_id = $this->account_exist_return_id('Cost of Goods Sold');
                    }
                } else {
                    $account_id = $this->getCategoryAccountId($sale->category_id, 'cogs');
                }
                if (!empty($account_id)) {
                    $business_id = request()->session()->get('business.id');
                    
                    $account_transaction_data = [
                        'amount' =>  abs($sale->quantity * $sale->dpp_inc_tax), // @eng 11/2
                        'account_id' => $account_id,
                        'type' => $type,
                        'sub_type' => $sub_type,
                        'operation_date' => $transaction->transaction_date,
                        'created_by' => $transaction->created_by,
                        'transaction_id' =>  $transaction->id,
                        'sell_line_id' =>  $sale->id,
                        'note' => null
                    ];
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
            }
        }
    }
    public function updateCostofGoodsSoldTransaction($transaction)
    {
        $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
            ->where('transaction_id', $transaction->id)
            ->select('transaction_sell_lines.*', 'products.category_id', 'products.sub_category_id', 'variations.default_purchase_price','variations.dpp_inc_tax')
            ->get();
        foreach ($sell_lines as $sale) {
            $account_id = $this->account_exist_return_id('Cost of Goods Sold');
            if ($sale->quantity >= 0) { //not include pos page return
                if (!empty($sale->sub_category_id)) {
                    $account_id = $this->getCategoryAccountId($sale->sub_category_id, 'cogs');
                    if (empty($account_id)) {
                        $account_id = $this->getCategoryAccountId($sale->category_id, 'cogs');
                    }
                    if (empty($account_id)) {
                        $account_id = $this->account_exist_return_id('Cost of Goods Sold');
                    }
                } else {
                    $account_id = $this->getCategoryAccountId($sale->category_id, 'cogs');
                }
                if (!empty($account_id)) {
                    $account_transaction = AccountTransaction::where('transaction_id', $transaction->id)->where('account_id', $account_id)->where('sell_line_id', $sale->id)->first();
                    if (!empty($account_transaction)) {
                        $account_transaction->amount = abs($sale->quantity * $sale->dpp_inc_tax);
                        $account_transaction->save();
                    } else {
                        $account_transaction_data = [
                            'amount' =>  abs($sale->quantity * $sale->dpp_inc_tax),
                            'account_id' => $account_id,
                            'type' => 'debit',
                            'sub_type' => null,
                            'operation_date' => $transaction->transaction_date,
                            'created_by' => $transaction->created_by,
                            'transaction_id' =>  $transaction->id,
                            'sell_line_id' =>  $sale->id,
                            'note' => null
                        ];
                        AccountTransaction::createAccountTransaction($account_transaction_data);
                    }
                }
            }
        }
    }
    public function createSaleIncomeTransaction($transaction, $sub_type = null, $type)
    {
        $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->where('transaction_id', $transaction->id)
            ->select('transaction_sell_lines.*', 'products.category_id', 'products.sub_category_id')
            ->get();
        foreach ($sell_lines as $sale) {
            if ($sale->quantity >= 0) { //not include pos page return
                if (!empty($sale->sub_category_id)) {
                    $account_id = $this->getCategoryAccountId($sale->sub_category_id, 'sale_income');
                    if (empty($account_id)) {
                        $account_id = $this->getCategoryAccountId($sale->category_id, 'sale_income');
                    }
                    if (empty($account_id)) {
                        $account_id = $this->account_exist_return_id('Sales Income');
                    }
                } else {
                    $account_id = $this->getCategoryAccountId($sale->category_id, 'sale_income');
                }
                if (!empty($account_id)) {
                    $account_transaction_data = [
                        // 'amount' => abs($sale->quantity * $sale->unit_price_inc_tax - $sale->line_discount_amount), // Credit Sales Income to use price including tax
                        'amount' => abs($sale->quantity * $sale->unit_price_inc_tax), // line_discount_amount is already included in unit_price_inc_tax
                        'account_id' => $account_id,
                        'type' => $type,
                        'sub_type' => $sub_type,
                        'operation_date' => $transaction->transaction_date,
                        'created_by' => $transaction->created_by,
                        'transaction_id' => $transaction->id,
                        'sell_line_id' =>  $sale->id,
                        'note' => null
                    ];
                    AccountTransaction::createAccountTransaction($account_transaction_data);
                }
            }
        }
    }
    public function updateSaleIncomeTransaction($transaction)
    {
        $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->where('transaction_id', $transaction->id)
            ->select('transaction_sell_lines.*', 'products.category_id', 'products.sub_category_id')
            ->get();
        foreach ($sell_lines as $sale) {
            if ($sale->quantity >= 0) { //not include pos page return
                if (!empty($sale->sub_category_id)) {
                    $account_id = $this->getCategoryAccountId($sale->sub_category_id, 'sale_income');
                    if (empty($account_id)) {
                        $account_id = $this->getCategoryAccountId($sale->category_id, 'sale_income');
                    }
                    if (empty($account_id)) {
                        $account_id = $this->account_exist_return_id('Sales Income');
                    }
                } else {
                    $account_id = $this->getCategoryAccountId($sale->category_id, 'sale_income');
                }
                if (!empty($account_id)) {
                    $account_transaction = AccountTransaction::where('transaction_id', $transaction->id)->where('account_id', $account_id)->where('sell_line_id', $sale->id)->first();
                    if (!empty($account_transaction)) {
                        $account_transaction->amount = abs($sale->quantity * $sale->unit_price);
                        $account_transaction->save();
                    } else {
                        $account_transaction_data = [
                            'amount' => abs($sale->quantity * $sale->unit_price - $sale->line_discount_amount),
                            'account_id' => $account_id,
                            'type' => 'credit',
                            'sub_type' => null,
                            'operation_date' => $transaction->transaction_date,
                            'created_by' => $transaction->created_by,
                            'transaction_id' => $transaction->id,
                            'sell_line_id' =>  $sale->id,
                            'note' => null
                        ];
                        AccountTransaction::createAccountTransaction($account_transaction_data);
                    }
                }
            }
        }
    }
    public function getCategoryAccountId($category_id, $group)
    {
        $business_id = request()->session()->get('business.id');
        if ($group == 'cogs') {
            return Category::where('business_id', $business_id)->where('id', $category_id)->select('cogs_account_id')->first()->cogs_account_id;
        }
        if ($group == 'sale_income') {
            return Category::where('business_id', $business_id)->where('id', $category_id)->select('sales_income_account_id')->first()->sales_income_account_id;
        }
    }
    public function updateManageStockAccount($transaction)
    {
        $product_details = $this->getTransactionProductDetail($transaction->id, $transaction->type);
        if ($transaction->type == 'sell') {
            $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
                ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
                ->where('transaction_id', $transaction->id)
                ->select('transaction_sell_lines.*', 'products.category_id', 'products.enable_stock', 'products.stock_type', 'products.sub_category_id', 'variations.default_purchase_price','variations.dpp_inc_tax')
                ->get();
            foreach ($sell_lines as $sale) {
                if ($sale->quantity >= 0) { //exclude pos page return
                    if ($sale->enable_stock) {
                        $amount = abs($sale->quantity * $sale->dpp_inc_tax);
                        if (!empty($sale->stock_type)) {
                            $account_id = $sale->stock_type;
                            $account_transaction = AccountTransaction::where('transaction_id', $transaction->id)->where('account_id', $account_id)->where('sell_line_id', $sale->id)->first();
                            if (!empty($account_transaction)) {
                                $account_transaction->amount = $amount;
                                $account_transaction->save();
                            } else {
                                $account_transaction_data['type'] = 'credit';
                                $account_transaction_data['sub_type'] = null;
                                $account_transaction_data['amount'] = $amount;
                                $account_transaction_data['account_id'] = $account_id;
                                $account_transaction_data['transaction_id'] = $transaction->id;
                                $account_transaction_data['sell_line_id'] = $sale->id;
                                $account_transaction_data['operation_date'] = $transaction->transaction_date;
                                $account_transaction = AccountTransaction::createAccountTransaction($account_transaction_data);
                            }
                        }
                    }
                }
            }
        } else {
            foreach ($product_details as $product_detail) {
                if ($product_detail->enable_stock) {
                    $amount = $product_detail->amount;
                    if (!empty($product_detail->stock_type)) {
                        $account_id = $product_detail->stock_type;
                        $account_transaction = AccountTransaction::where('transaction_id', $transaction->id)->where('account_id', $account_id)->first();
                        if (!empty($account_transaction)) {
                            $account_transaction->amount = $amount;
                            $account_transaction->operation_date = $transaction->transaction_date;
                            $account_transaction->save();
                        } else {
                            $account_transaction_data['type'] = 'debit';
                            $account_transaction_data['sub_type'] = null;
                            $account_transaction_data['amount'] = $amount;
                            $account_transaction_data['account_id'] = $account_id;
                            $account_transaction_data['transaction_id'] = $transaction->id;
                            $account_transaction_data['operation_date'] = $transaction->transaction_date;
                            $account_transaction = AccountTransaction::createAccountTransaction($account_transaction_data);
                        }
                    }
                }
            }
        }
        return true;
    }
    public function manageStockAccount($transaction, $account_transaction_data, $trans_type, $amount, $sub_type = null,$status = null)
    {
        $product_details = $this->getTransactionProductDetail($transaction->id, $transaction->type);
        if ($transaction->type == 'sell') {
            $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
                ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
                ->where('transaction_id', $transaction->id)
                ->select('transaction_sell_lines.*', 'products.category_id', 'products.enable_stock', 'products.stock_type', 'products.sub_category_id', 'variations.default_purchase_price','variations.dpp_inc_tax')
                ->get();
            foreach ($sell_lines as $sale) {
                if ($sale->quantity >= 0) { //exclude pos page return
                    $account_transaction_data['type'] = $trans_type;
                    if ($sale->enable_stock) {
                        $account_transaction_data['type'] = $trans_type;
                        $account_transaction_data['sub_type'] = $sub_type;
                        $account_transaction_data['amount'] = abs($sale->quantity * $sale->dpp_inc_tax);
                        $account_transaction_data['operation_date'] = $transaction->transaction_date;
                        if (!empty($sale->stock_type)) {
                            $account_transaction_data['account_id'] = $sale->stock_type;
                            AccountTransaction::createAccountTransaction($account_transaction_data);
                        }
                    }
                }
            }
        } else {
            $account_transaction_data['type'] = $trans_type;
            $account_transaction_data['amount'] = $amount;
            foreach ($product_details as $product_detail) {
                if ($product_detail->enable_stock && $status == "received") {
                    $account_transaction_data['type'] = $trans_type;
                    $account_transaction_data['sub_type'] = $sub_type;
                    $account_transaction_data['amount'] = $product_detail->amount;
                    $account_transaction_data['operation_date'] = $transaction->transaction_date;
                    if (!empty($product_detail->stock_type)) {
                        $account_transaction_data['account_id'] = $product_detail->stock_type;
                        $account_transaction_data['transaction_id'] = $transaction->id;
                        $account_transaction = AccountTransaction::createAccountTransaction($account_transaction_data);
                    }
                }
            }
        }
        return true;
    }
    public function getAmountofTransactionWithoutPosReturn($transaction)
    {
        $sell_lines = TransactionSellLine::leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('product_variations', 'products.id', 'product_variations.product_id')
            ->leftjoin('variations', 'product_variations.id', 'variations.product_variation_id')
            ->where('transaction_id', $transaction->id)
            ->select('transaction_sell_lines.*')
            ->get();
        $amount = 0;
        foreach ($sell_lines as $sale) {
            if ($sale->quantity >= 0) { //not include pos page return
                $amount += ($sale->quantity * $sale->unit_price)  - $sale->line_discount_amount;
            }
        }
        return $amount;
    }
    public function getTankBalanceById($tank_id)
    {
        $business_id = request()->session()->get('business.id');
        DB::enableQueryLog();
        $purchase_query = FuelTank::leftjoin('tank_purchase_lines', 'fuel_tanks.id', 'tank_purchase_lines.tank_id')
            ->where('fuel_tanks.id', $tank_id)
            ->where('fuel_tanks.business_id', $business_id)
            ->select([
                DB::raw('SUM(tank_purchase_lines.quantity) as pruchase_qty')
            ])->first();
            
        
        $transfers_in = FuelTank::leftjoin('tank_transfers', 'fuel_tanks.id', 'tank_transfers.to_tank')
            ->where('fuel_tanks.id', $tank_id)
            ->where('fuel_tanks.business_id', $business_id)
            ->select([
                DB::raw('SUM(tank_transfers.quantity) as qty')
            ])->first();
            
        $transfers_out = FuelTank::leftjoin('tank_transfers', 'fuel_tanks.id', 'tank_transfers.from_tank')
            ->where('fuel_tanks.id', $tank_id)
            ->where('fuel_tanks.business_id', $business_id)
            ->select([
                DB::raw('SUM(tank_transfers.quantity) as qty')
            ])->first();
            
        
        $sell_query = TankSellLine::where('tank_id', $tank_id)
            ->select([
                DB::raw('SUM(quantity) as sell_qty')
            ])->first();
            
        $purchase_qty = !empty($purchase_query->pruchase_qty) ? $purchase_query->pruchase_qty : 0;
        $transfer_in_qty = $transfers_in->qty ??  0;
        $transfer_out_qty = $transfers_out->qty ??  0;
        
        
        $sell_qty = !empty($sell_query->sell_qty) ? $sell_query->sell_qty : 0;
        $stock_adjustment = Transaction::leftjoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
            ->where('stock_adjustment_lines.tank_id', $tank_id)
            ->select(
                DB::raw("SUM(IF(stock_adjustment_lines.type='increase', stock_adjustment_lines.quantity, -1 * stock_adjustment_lines.quantity)) as stock_adjusted")
            )->first();
        $stock_adjusted = !empty($stock_adjustment->stock_adjusted) ? $stock_adjustment->stock_adjusted : 0;
        return ($purchase_qty  - abs($sell_qty) + $transfer_in_qty - $transfer_out_qty); //+  $stock_adjusted;
    }
    
    public function getTankBalanceByDate($tank_id,$date)
    {
        $business_id = request()->session()->get('business.id');
        
        $purchase_query = FuelTank::leftjoin('tank_purchase_lines', 'fuel_tanks.id', 'tank_purchase_lines.tank_id')
            ->leftjoin('transactions', 'transactions.id', 'tank_purchase_lines.transaction_id')
            ->where('fuel_tanks.id', $tank_id)
            ->where('fuel_tanks.business_id', $business_id)
            ->where('transactions.created_at', '<', $date)
            ->select([
                DB::raw('SUM(tank_purchase_lines.quantity) as pruchase_qty')
            ])->first();
            
        
        $transfers_in = FuelTank::leftjoin('tank_transfers', 'fuel_tanks.id', 'tank_transfers.to_tank')
            ->where('fuel_tanks.id', $tank_id)
            ->where('tank_transfers.created_at', '<', $date)
            ->where('fuel_tanks.business_id', $business_id)
            ->select([
                DB::raw('SUM(tank_transfers.quantity) as qty')
            ])->first();
            
        $transfers_out = FuelTank::leftjoin('tank_transfers', 'fuel_tanks.id', 'tank_transfers.from_tank')
            ->where('fuel_tanks.id', $tank_id)
            ->where('tank_transfers.created_at', '<', $date)
            ->where('fuel_tanks.business_id', $business_id)
            ->select([
                DB::raw('SUM(tank_transfers.quantity) as qty')
            ])->first();
            
        
        $sell_query = TankSellLine::where('tank_id', $tank_id)
            ->leftjoin('transactions', 'transactions.id', 'tank_sell_lines.transaction_id')
            ->where('transactions.created_at', '<', $date)
            ->select([
                DB::raw('SUM(quantity) as sell_qty')
            ])->first();
            
        $purchase_qty = !empty($purchase_query->pruchase_qty) ? $purchase_query->pruchase_qty : 0;
        $transfer_in_qty = $transfers_in->qty ??  0;
        $transfer_out_qty = $transfers_out->qty ??  0;
        
        
        $sell_qty = !empty($sell_query->sell_qty) ? $sell_query->sell_qty : 0;
        $sell_qty = !empty($sell_query->sell_qty) ? $sell_query->sell_qty : 0;
        $stock_adjustment = Transaction::leftjoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
            ->where('stock_adjustment_lines.tank_id', $tank_id)
            ->select(
                DB::raw("SUM(IF(stock_adjustment_lines.type='increase', stock_adjustment_lines.quantity, -1 * stock_adjustment_lines.quantity)) as stock_adjusted")
            )->first();
        $stock_adjusted = !empty($stock_adjustment->stock_adjusted) ? $stock_adjustment->stock_adjusted : 0;
        return ($purchase_qty  - abs($sell_qty) + $transfer_in_qty - $transfer_out_qty); //+  $stock_adjusted;
    }
    public function getTankBalanceByDateInclude($tank_id,$date)
    {
        $business_id = request()->session()->get('business.id');
        
        $purchase_query = FuelTank::leftjoin('tank_purchase_lines', 'fuel_tanks.id', 'tank_purchase_lines.tank_id')
            ->leftjoin('transactions', 'transactions.id', 'tank_purchase_lines.transaction_id')
            ->where('fuel_tanks.id', $tank_id)
            ->where('fuel_tanks.business_id', $business_id)
            ->where('transactions.created_at', '<=', $date)
            ->select([
                DB::raw('SUM(tank_purchase_lines.quantity) as pruchase_qty')
            ])->first();
            
        
        $transfers_in = FuelTank::leftjoin('tank_transfers', 'fuel_tanks.id', 'tank_transfers.to_tank')
            ->where('fuel_tanks.id', $tank_id)
            ->where('tank_transfers.created_at', '<=', $date)
            ->where('fuel_tanks.business_id', $business_id)
            ->select([
                DB::raw('SUM(tank_transfers.quantity) as qty')
            ])->first();
            
        $transfers_out = FuelTank::leftjoin('tank_transfers', 'fuel_tanks.id', 'tank_transfers.from_tank')
            ->where('fuel_tanks.id', $tank_id)
            ->where('tank_transfers.created_at', '<=', $date)
            ->where('fuel_tanks.business_id', $business_id)
            ->select([
                DB::raw('SUM(tank_transfers.quantity) as qty')
            ])->first();
            
        
        $sell_query = TankSellLine::where('tank_id', $tank_id)
            ->leftjoin('transactions', 'transactions.id', 'tank_sell_lines.transaction_id')
            ->where('transactions.created_at', '<=', $date)
            ->select([
                DB::raw('SUM(quantity) as sell_qty')
            ])->first();
            
        $purchase_qty = !empty($purchase_query->pruchase_qty) ? $purchase_query->pruchase_qty : 0;
        $transfer_in_qty = $transfers_in->qty ??  0;
        $transfer_out_qty = $transfers_out->qty ??  0;
        
        
        $sell_qty = !empty($sell_query->sell_qty) ? $sell_query->sell_qty : 0;
        $stock_adjustment = Transaction::leftjoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')
            ->where('stock_adjustment_lines.tank_id', $tank_id)
            ->select(
                DB::raw("SUM(IF(stock_adjustment_lines.type='increase', stock_adjustment_lines.quantity, -1 * stock_adjustment_lines.quantity)) as stock_adjusted")
            )->first();
        $stock_adjusted = !empty($stock_adjustment->stock_adjusted) ? $stock_adjustment->stock_adjusted : 0;
        return ($purchase_qty  - abs($sell_qty) + $transfer_in_qty - $transfer_out_qty); //+  $stock_adjusted;
    }
    
    /**
     * @ModifiedBy Afes Oktavianus
     * @DateBy 05-06-2021
     * @Task 3343
     */
    public function getTankCurrentDifference($tank_id)
    {
        $business_id = request()->session()->get('business.id');
        $fuel_balance_dip_reading = DipReading::query()->where('dip_readings.business_id', $business_id)
                ->where('dip_readings.tank_id', $tank_id)
                ->sum('fuel_balance_dip_reading');
        $current_qty = DipReading::query()->where('dip_readings.business_id', $business_id)
                ->where('dip_readings.tank_id', $tank_id)
                ->sum('current_qty');
        $current_diff = ($fuel_balance_dip_reading - $current_qty);
        return abs($current_diff) ;
    }
    public function getTankProductBalanceByProductId($product_id)
    {
        DB::enableQueryLog();
        $business_id = request()->session()->get('business.id');
        
        $fuel_tanks = FuelTank::where('business_id',$business_id)->where('product_id',$product_id)->get();
        $bal = 0;
        foreach($fuel_tanks as $tank){
            $bal += $this->getTankBalanceById($tank->id);
        }
        
        return $bal;
    }
    
    public function getTankProductBalanceByProductIdDate($product_id,$date)
    {
        DB::enableQueryLog();
        $business_id = request()->session()->get('business.id');
        
        $fuel_tanks = FuelTank::where('business_id',$business_id)->where('product_id',$product_id)->get();
        $bal = 0;
        foreach($fuel_tanks as $tank){
            $bal += $this->getTankBalanceByDateInclude($tank->id,$date);
        }
        
        return $bal;
    }
    
    // Modified by Muneeb Ahmad for Store Dropdown - Task#3454
    public function getProductDropDownArray($business_id, ?int $fuel_category_id, $module = null)
    {
        $business_details = Business::find($business_id);
        $default_store = request()->session()->get('business.default_store');
        $business_locations = BusinessLocation::forDropdown($business_id);
        $location_id = current(array_keys($business_locations->toArray()));
        $products = Product::join('units', 'products.unit_id', 'units.id')
            ->leftJoin('variation_location_details', 'products.id', 'variation_location_details.product_id');
        if($location_id){
            // $products = $products->join('stores', 'stores.location_id', 'variation_location_details.location_id');
            // $products = $products->join('variation_store_details', 'stores.id', 'variation_store_details.store_id');
        }
        $products = $products->join('variations', 'products.id', 'variations.product_id');
        // $products = $products->join('variations', function ($join) {
        //     $join->on('products.id', '=', 'variations.product_id');
        //     // $join->on('variation_location_details.variation_id', '=', 'variations.id');
        //     // $join->on('variations.id', '=', 'variation_store_details.variation_id');
        // })
        // ->active()
        // ->where('products.business_id', $business_id)
        // ->whereNull('variations.deleted_at');
        
        // if(!empty($module)){
        //     $products->forModule($module);
        // }
        
        // if($fuel_category_id){
        //     $products = $products->where('category_id', '!=', $fuel_category_id);
        // }
        // if($location_id){
        //     $products = $products->where('variation_location_details.location_id', $location_id);
        // }
        if($default_store){
            // $products = $products->where('stores.id', $default_store);
        }
        $products = $products->select('products.id', 'products.name', 'products.sku', 'units.short_name as unit', 'variation_location_details.qty_available as qty', 'variations.dpp_inc_tax as price')->get();
        $product_arr = [];
        foreach ($products as $product) {
            if($default_store){
                $variant = Variation_store_detail::where('store_id',$default_store)->where('product_id',$product->id)->get();
            }else{
                $variant = Variation_store_detail::where('product_id',$product->id)->get();
            }
            if($variant){
                $store_available_qty = 0;
                foreach($variant as $var){
                    $store_available_qty += $var->qty_available;
                }
                $product->store_available_qty = $store_available_qty;
            }else{
                $product->store_available_qty = 0;
            }
            $product_arr[$product->id] = $product->sku . ', ' . $product->name . ', ' . $this->num_f($product->qty, false, $business_details, true) . ' ' . $product->unit . ', ' . $this->num_f($product->price, true, $business_details, false).', (Available Qty : '. $this->num_f($product->qty, false, $business_details, true).' '.$product->unit.')';
        }
        if($default_store){
            return $product_arr;
        }else{
            return array();
        }
    }
    
    public function getPumpOperatorBalance( $pump_operator_id )
    {
        $total_shortage = 0;
            $shortage =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select([
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'shortage' AND t.status = 'final', ABS(final_total), 0)) as total_shortage"),
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'shortage' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as shortage_recover"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($shortage)) {
                $total_shortage = $shortage->total_shortage - $shortage->shortage_recover;
            }
            
            $total_excess = 0;
            $excess =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select([
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'excess' AND t.status = 'final', ABS(final_total), 0)) as total_excess"),
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'excess' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as excess_paid"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($excess)) {
                $total_excess = $excess->total_excess - $excess->excess_paid;
            }
            
            $total_shortage_ob = 0;
            $shortage_ob =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select([
                    DB::raw("SUM(IF(t.type = 'opening_balance' AND sub_type = 'shortage' AND t.status = 'final', ABS(final_total), 0)) as total_shortage"),
                    DB::raw("SUM(IF(t.type = 'opening_balance' AND sub_type = 'shortage' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as shortage_recover"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($shortage_ob)) {
                $total_shortage_ob = $shortage_ob->total_shortage - $shortage_ob->shortage_recover;
            }
            
            $total_excess_ob = 0;
            $excess_ob =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select([
                    DB::raw("SUM(IF(t.type = 'opening_balance' AND sub_type = 'excess' AND t.status = 'final', ABS(final_total), 0)) as total_excess"),
                    DB::raw("SUM(IF(t.type = 'opening_balance' AND sub_type = 'excess' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as excess_paid"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($excess_ob)) {
                $total_excess_ob = $excess_ob->total_excess - $excess_ob->excess_paid;
            }
            
            
            
            $commission =    PumpOperatorCommission::where('pump_operator_id', $pump_operator_id)
                                ->sum('amount');
                                
            $total_commission = !empty($commission) ? $commission : 0;
            
            return abs($total_shortage)-abs($total_excess) + abs($total_shortage_ob) - abs($total_excess_ob) + $total_commission;
     
    }
    
    public function getPumpOperatorBFBalance( $pump_operator_id, $date )
    {
            $total_shortage = 0;
            $shortage =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->whereDate('t.transaction_date','<',$date)
                ->select([
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'shortage' AND t.status = 'final' AND DATE(t.transaction_date) < '".$date."', ABS(final_total), 0)) as total_shortage"),
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'shortage' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND DATE(transaction_payments.paid_on) < '".$date."'), 0)) as shortage_recover"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($shortage)) {
                $total_shortage = $shortage->total_shortage - $shortage->shortage_recover;
            }
            
            $total_excess = 0;
            $excess =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                // ->whereDate('t.transaction_date','<',$date)
                ->select([
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'excess' AND t.status = 'final' AND DATE(t.transaction_date) < '".$date."', ABS(final_total), 0)) as total_excess"),
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'excess' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND DATE(transaction_payments.paid_on) < '".$date."'), 0)) as excess_paid"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($excess)) {
                $total_excess = $excess->total_excess - $excess->excess_paid;
                logger($excess->total_excess."----".$excess->excess_paid);
            }
            
            $total_shortage_ob = 0;
            $shortage_ob =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->whereDate('t.transaction_date','<',$date)
                ->select([
                    DB::raw("SUM(IF(t.type = 'opening_balance' AND sub_type = 'shortage' AND t.status = 'final' AND DATE(t.transaction_date) < '".$date."', ABS(final_total), 0)) as total_shortage"),
                    DB::raw("SUM(IF(t.type = 'opening_balance' AND sub_type = 'shortage' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND DATE(transaction_payments.paid_on) < '".$date."'), 0)) as shortage_recover"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($shortage_ob)) {
                $total_shortage_ob = $shortage_ob->total_shortage - $shortage_ob->shortage_recover;
            }
            
            $total_excess_ob = 0;
            $excess_ob =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->whereDate('t.transaction_date','<',$date)
                ->select([
                    DB::raw("SUM(IF(t.type = 'opening_balance' AND sub_type = 'excess' AND t.status = 'final' AND DATE(t.transaction_date) < '".$date."', ABS(final_total), 0)) as total_excess"),
                    DB::raw("SUM(IF(t.type = 'opening_balance' AND sub_type = 'excess' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND DATE(transaction_payments.paid_on) < '".$date."'), 0)) as excess_paid"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($excess_ob)) {
                $total_excess_ob = $excess_ob->total_excess - $excess_ob->excess_paid;
            }
            
            
            
            $commission =    PumpOperatorCommission::whereDate('transaction_date','<',$date)
                                ->where('pump_operator_id', $pump_operator_id)
                                ->sum('amount');
                                
            $total_commission = !empty($commission) ? $commission : 0;
            
            return abs($total_shortage)-abs($total_excess) + abs($total_shortage_ob) - abs($total_excess_ob) + $total_commission;
     
    }
    
    public function getPumpOperatorCommission($pump_operator_id,$start_date,$end_date){
        $commission =    PumpOperatorCommission::whereDate('transaction_date','>=',$start_date)
                                ->whereDate('transaction_date','<=',$end_date)
                                ->where('pump_operator_id', $pump_operator_id)
                                ->sum('amount');
                                
        $total_commission = !empty($commission) ? $commission : 0;
        
        return $total_commission;
    }
    
    public function getPumpOperatorExcessOrShortage($pump_operator_id, $type)
    {
        if ($type == 'shortage') {
            $total_shortage = 0;
            $shortage =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select([
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'shortage' AND t.status = 'final', ABS(final_total), 0)) as total_shortage"),
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'shortage' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as shortage_recover"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($shortage)) {
                $total_shortage = $shortage->total_shortage - $shortage->shortage_recover;
            }
            return abs($total_shortage);
        }
        if ($type == 'excess') {
            $total_excess = 0;
            $excess =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select([
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'excess' AND t.status = 'final', ABS(final_total), 0)) as total_excess"),
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'excess' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as excess_paid"),
                ])->groupBy('pump_operators.id')->first();
            if (!empty($excess)) {
                $total_excess = $excess->total_excess - $excess->excess_paid;
            }
            return  abs($total_excess);
        }
    }
    public function getPumpOperatorExcessOrShortageByDate($pump_operator_id, $type, $start_date = null, $end_date = null)
    {
        $total_paid_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('type', 'settlement')
            ->whereIn('sub_type', ['excess', 'shortage'])
            ->where('pump_operator_id', $pump_operator_id)
            ->whereDate('transaction_payments.paid_on', '>=', $start_date)
            ->whereDate('transaction_payments.paid_on', '<=', $end_date)
            ->select(
                DB::raw("SUM(IF(transactions.sub_type = 'excess', ABS(transaction_payments.amount), 0)) as excess_paid"),
                DB::raw("SUM(IF(transactions.sub_type = 'shortage', ABS(transaction_payments.amount), 0)) as shortage_recovered")
            )->first();
        $balance = 0;
        if ($type == 'shortage') {
            $total_shortage = 0;
            $query =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select([
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'shortage' AND t.status = 'final', ABS(final_total), 0)) as total_shortage"),
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'shortage' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND DATE(transaction_payments.paid_on) >= $start_date AND DATE(transaction_payments.paid_on) <= $end_date), 0)) as shortage_recover"),
                ])->groupBy('pump_operators.id');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('t.transaction_date', '>=', $start_date);
                $query->whereDate('t.transaction_date', '<=', $end_date);
            }
            $shortage = $query->first();
            if (!empty($shortage)) {
                $total_shortage = !empty($shortage->total_shortage) ? $shortage->total_shortage : 0;
                $shortage_recover = !empty($total_paid_query->shortage_recovered) ? $total_paid_query->shortage_recovered : 0;
                $balance =  $total_shortage - $shortage_recover;
            }
            return abs($balance);
        }
        if ($type == 'excess') {
            $total_excess = 0;
            $query =   PumpOperator::leftjoin('transactions as t', 'pump_operators.id', 't.pump_operator_id')
                ->where('pump_operators.id', $pump_operator_id)
                ->select([
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'excess' AND t.status = 'final', ABS(final_total), 0)) as total_excess"),
                    DB::raw("SUM(IF(t.type = 'settlement' AND sub_type = 'excess' AND t.status = 'final', (SELECT SUM(IF(is_return = 1,-1*amount,ABS(amount))) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id AND DATE(transaction_payments.paid_on) >= $start_date AND DATE(transaction_payments.paid_on) <= $end_date), 0)) as excess_paid"),
                ])->groupBy('pump_operators.id');
            if (!empty($start_date) && !empty($end_date)) {
                $query->whereDate('t.transaction_date', '>=', $start_date);
                $query->whereDate('t.transaction_date', '<=', $end_date);
            }
            $excess = $query->first();
            if (!empty($excess)) {
                $total_excess = !empty($excess->total_excess) ? $excess->total_excess : 0;
                $excess_paid = !empty($total_paid_query->excess_paid) ? $total_paid_query->excess_paid : 0;
                $balance =  $total_excess - $excess_paid;
            }
            return  abs($balance);
        }
    }
    public function updatePropertyStatus($property_id)
    {
        $blocks = PropertyBlock::where('property_id', $property_id)->where('is_sold', 0)->first();
        if (empty($blocks)) {
            Property::where('id', $property_id)->update(['status' => 'close']);
        }
        return true;
    }
    public function getPropertyAccountSettingsByTransaction($transaction_id)
    {
        $transaction_sell_line = PropertySellLine::where('transaction_id', $transaction_id)->first();
        $account_settings = PropertyAccountSetting::where('property_id', $transaction_sell_line->property_id)->first();
        return $account_settings;
    }
    /**
     * Pay contact due at once
     *
     * @param obj $parent_payment, string $type
     *
     * @return void
     */
    public function payAtOnceExcessShortage($inputs, $sub_type, $pump_operator_id)
    {
        $business_id =  Auth::user()->business_id;
        $pump_operator = PumpOperator::where('id', $pump_operator_id)->first();
        $due_transactions = Transaction::where('pump_operator_id', $pump_operator_id)
            ->whereIn('type', ['opening_balance', 'settlement'])
            ->where('sub_type', $sub_type)
            ->where('payment_status', '!=', 'paid')
            ->orderBy('transaction_date', 'asc')
            ->get();
        $total_amount = $inputs['amount'];
        
        // store transaction for parent pament amount
        $ob_data = [
            'business_id' => $business_id,
            'location_id' => $pump_operator->location_id,
            'type' => $sub_type.'_bulk_payment',
            'status' => 'final',
            'payment_status' => 'paid',
            'pump_operator_id' => $pump_operator_id,
            'transaction_date' => \Carbon::parse($inputs['paid_on'])->format('Y-m-d'),
            'total_before_tax' => $total_amount,
            'final_total' => $total_amount,
            'tax_amount' => 0,
            'discount_type' => 'fixed',
            'discount_amount' => 0,
            'is_settlement' => 1,
            'created_by' => auth()->user()->id,
            'invoice_no' => $inputs['payment_ref_no']
        ];
        
        $parent_transaction = Transaction::create($ob_data);
        
        $parent_array = [
                        'transaction_id' => $parent_transaction->id,
                        'business_id' => $business_id,
                        'method' => $inputs['method'],
                        'transaction_no' => null,
                        'card_transaction_number' => $inputs['card_transaction_number'],
                        'bank_name' => !empty($inputs['bank_name']) ? $inputs['bank_name'] : null,
                        'cheque_number' => $inputs['cheque_number'],
                        'bank_account_number' => !empty($inputs['bank_account_number']) ? $inputs['bank_account_number'] : null,
                        'bank_name' => !empty($inputs['bank_name']) ? $inputs['bank_name'] : null,
                        'paid_on' => $inputs['paid_on'],
                        'created_by' => Auth::user()->id,
                        'payment_ref_no' => $inputs['payment_ref_no'],
                        'amount' => $total_amount
                    ];
        
        $parent_payment = TransactionPayment::create($parent_array);
        $this->createExcessShortageAccountTransaction($parent_payment, $inputs, $sub_type, $parent_transaction, $pump_operator);
        
        
        
        $tranaction_payments = [];
        if ($due_transactions->count()) {
            foreach ($due_transactions as $transaction) {
                $break = false;
                if ($total_amount > 0) {
                    $total_paid = $this->getTotalPaid($transaction->id);
                    $due = abs($transaction->final_total) - $total_paid;
                    $array = [
                        'transaction_id' => $transaction->id,
                        'business_id' => $business_id,
                        'method' => $inputs['method'],
                        'transaction_no' => null,
                        'card_transaction_number' => $inputs['card_transaction_number'],
                        'bank_name' => !empty($inputs['bank_name']) ? $inputs['bank_name'] : null,
                        'cheque_number' => $inputs['cheque_number'],
                        'bank_account_number' => !empty($inputs['bank_account_number']) ? $inputs['bank_account_number'] : null,
                        'bank_name' => !empty($inputs['bank_name']) ? $inputs['bank_name'] : null,
                        'paid_on' => $inputs['paid_on'],
                        'created_by' => Auth::user()->id,
                        // 'payment_for' => $inputs['payment_for'],
                        'parent_id' => $parent_payment->id
                    ];
                    $array['payment_ref_no'] = $inputs['payment_ref_no'];
                    
                    
                    if ($due <= $total_amount) {
                        $array['amount'] = $due;
                        $tranaction_payments[] = $array;
                        //Update transaction status to paid
                        $transaction->payment_status = 'paid';
                        $transaction->save();
                        $total_amount = $total_amount - $due;
                    } else {
                        $array['amount'] = $total_amount;
                        $tranaction_payments[] = $array;
                        //Update transaction status to partial
                        $transaction->payment_status = 'partial';
                        $transaction->save();
                        $break = true;
                    }
                    $transaction_payment = TransactionPayment::create($array);
                    if ($break) {
                        break;
                    }
                }
            }
            // $this->createExcessShortageAccountTransaction($transaction_payment, $inputs, $sub_type, $transaction, $pump_operator);
            if ($sub_type == 'excess') {
                // $account_transaction_data['type'] = 'credit';
                $pump_operator->excess_amount = abs($pump_operator->excess_amount) - $inputs['amount'];
            }
            if ($sub_type == 'shortage') {
                // $account_transaction_data['type'] = 'debit';
                $pump_operator->short_amount = abs($pump_operator->short_amount) - $inputs['amount'];
            }
            $pump_operator->save();
        }
    }
    public function createExcessShortageAccountTransaction($transaction_payment, $inputs, $sub_type, $transaction, $pump_operator)
    {
        //create account transaction for expense account selected 
        $account_transaction_data = [
            'amount' => $inputs['amount'],
            'sub_type' => 'ledger_show',
            'operation_date' => $inputs['paid_on'],
            'created_by' => Auth::user()->id,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => $transaction_payment->id,
            'note' => null
        ];
        
        $location_id = $pump_operator->location_id;
        
        $account_transaction_data['account_id'] = $inputs['account_id'];
        
        if ($sub_type == 'excess') {
            $account_transaction_data['type'] = 'credit';
        }
        if ($sub_type == 'shortage') {
            $account_transaction_data['type'] = 'debit';
        }
        $account_transaction_data['sub_type'] = null;
        
        AccountTransaction::createAccountTransaction($account_transaction_data);
        $transaction_payment->account_id = $account_transaction_data['account_id'];
        $transaction_payment->save();
        
        // Accounts receivable entry
        if ($sub_type == 'excess') {
            $account_transaction_data['type'] = 'debit';
            $account_transaction_data['account_id'] = $this->account_exist_return_id('Accounts Receivable');
            AccountTransaction::createAccountTransaction($account_transaction_data);
        }
        
        if ($sub_type == 'shortage') {
            $account_transaction_data['type'] = 'credit';
            $account_transaction_data['account_id'] = $this->account_exist_return_id('Accounts Receivable');
            AccountTransaction::createAccountTransaction($account_transaction_data);
        }
        
    }
    public function getStockForSubCateogryByTransactionType($type, $sub_cat_id, $start_date, $end_date, $get_previous = false, $get_qty = true, $module = 'dailysummary_stocksummary_qty')
    {
        $business_id = session()->get('user.business_id');
        $query = Transaction::where('transactions.business_id', $business_id)->where('transactions.type', $type);
        if ($type == 'sell') {
            $query->leftjoin(
                'transaction_sell_lines',
                'transactions.id',
                'transaction_sell_lines.transaction_id'
            )
                ->leftjoin(
                    'products',
                    'transaction_sell_lines.product_id',
                    'products.id'
                );
        }
        if ($type == 'purchase' || $type == 'opening_stock') {
            $query->leftjoin(
                'purchase_lines',
                'transactions.id',
                'purchase_lines.transaction_id'
            )
                ->leftjoin(
                    'products',
                    'purchase_lines.product_id',
                    'products.id'
                );
        }
        if ($type == 'stock_adjustment') {
            $query->leftjoin(
                'stock_adjustment_lines',
                'transactions.id',
                'stock_adjustment_lines.transaction_id'
            )
                ->leftjoin(
                    'products',
                    'stock_adjustment_lines.product_id',
                    'products.id'
                );
        }
        $query->leftjoin(
            'variations',
            'products.id',
            'variations.product_id'
        );
        if (!empty($start_date) && !empty($end_date) && !$get_previous && $type != 'opening_stock') {
            $query->whereDate('transactions.transaction_date', '>=', $start_date);
            $query->whereDate('transactions.transaction_date', '<=', $end_date);
        }
        if ($get_previous && $type != 'opening_stock') {
            $query->whereDate('transactions.transaction_date', '<', $start_date);
        }
        if ($type == 'opening_stock') {
            $query->whereDate('transactions.transaction_date', '<', $end_date);
        }
        $query->where('products.sub_category_id', $sub_cat_id)->groupBy('products.sub_category_id');
        
        $query->where(function($q) use($module){
            $q->whereNull('products.disabled_in')->orwhereRaw("NOT FIND_IN_SET(?, products.disabled_in)", [$module]);
        });
        
        
        if (!$get_qty) {
            if ($type == 'sell') {
                $query->where(function ($q) {
                    $q->where('transactions.sub_type', '!=', 'credit_sale')->orWhereNull('transactions.sub_type');
                });
                $amount = $query->select(
                    DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned)*variations.dpp_inc_tax) as amount')
                )->first();
            }
            return $amount ? $amount->amount : 0;
        }
        if ($get_qty) {
            if ($type == 'sell') {
                $query->where(function ($q) {
                    $q->where('transactions.sub_type', '!=', 'credit_sale')->orWhereNull('transactions.sub_type');
                });
                $qty = $query->select(
                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as qty')
                )->first();
            }
            if ($type == 'purchase' || $type == 'opening_stock') {
                $qty = $query->select(
                    DB::raw("SUM(purchase_lines.quantity) as qty")
                )->first();
            }
            if ($type == 'stock_adjustment') {
                $qty = $query->select(
                    DB::raw("SUM(stock_adjustment_lines.quantity) as qty")
                )->first();
            }
            return $qty ? $qty->qty : 0;
        }
    }
    // Added by Muneeb Ahmad for Store Dropdown
    public function getProductsByStoreId($business_id= false, $location_id= false, $store_id= false, $tab= false,$except = null,$module = null){
       
        $business_details = Business::find($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $products = Product::join('units', 'products.unit_id', 'units.id')
            ->join('variation_location_details', 'products.id', 'variation_location_details.product_id');
            
            // $products1 = Product::join('units', 'products.unit_id', 'units.id')
            // ->join('variation_location_details', 'products.id', 'variation_location_details.product_id')->get();
            
            
        if($location_id){
            $products = $products->join('stores', 'stores.location_id', 'variation_location_details.location_id');
            // $products = $products->join('variation_store_details', 'stores.id', 'variation_store_details.store_id');
        }
        $products = $products->join('variations', 'products.id', 'variations.product_id');
        // $products = $products->join('variations', function ($join) {
        //     $join->on('products.id', '=', 'variations.product_id');
        //     $join->on('variation_location_details.variation_id', '=', 'variations.id');
        //     $join->on('variations.id', '=', 'variation_store_details.variation_id');
        // })
        // ->active()
        // ->where('products.business_id', $business_id)
        // ->whereNull('variations.deleted_at');
        

        // if(!empty($module)){
        //     $products->forModule($module);
        // }
        
        if($location_id){
           $products->where('variation_location_details.location_id', $location_id);
        }
        
        // if(!empty($except)){
        //     $products->where('products.category_id','!=',$except);
        // }
        
        if($store_id){
           $products->where('stores.id', $store_id);
        }

        $cat_ids = [];
        // if($tab && $tab == 'other_sal'){
        //     $cat_ids = Category::where('business_id', $business_id)->where('name', 'Fuel')->pluck('id')->toArray();
        // }
        $products = $products->select('products.id','products.category_id', 'products.name', 'products.sku', 'units.short_name as unit', 'variation_location_details.qty_available as qty', 'variations.dpp_inc_tax as price')->get();
        $product_arr = [];
        foreach ($products as $product) {
            if($cat_ids && (in_array($product->category_id,$cat_ids))){
                continue;
             }
            $variant = Variation_store_detail::where('store_id',$store_id)->where('product_id',$product->id)->get();
            if($variant){
                $store_available_qty = 0;
                foreach($variant as $var){
                    $store_available_qty += $var->qty_available;
                }
                $product->store_available_qty = $store_available_qty;
            }else{
                $product->store_available_qty = 0;
            }
            $product_arr[$product->id] = $product->sku . ', ' . $product->name . ', ' . $this->num_f($product->qty, false, $business_details, true) . ' ' . $product->unit . ', ' . $this->num_f($product->price, true, $business_details, false).', (Available Qty : '. $this->num_f($product->store_available_qty, false, $business_details, true).' '.$product->unit.')';
        }
         
        if(!empty($product_arr)){
           
            return $this->createDropdownHtml($product_arr, 'Please Select');
        }else{
            return $this->createDropdownHtml($product_arr, 'No Item Found');
        }
    }

    // update post dated cheque field in account transaction after creating payment
    public function updatePostdatedCheque($transaction)
    {
        $payments = TransactionPayment::where('transaction_id', $transaction->id)->get();
        if ($payments->count()) {
            foreach ($payments as $tp) {
                //update account transaction
                AccountTransaction::where('transaction_payment_id', $tp->id)->update(['type' => $tp->post_dated_cheque == 1 ? 'debit' : 'credit', 'post_dated_cheque' => $tp->post_dated_cheque,'update_post_dated_cheque' => $tp->update_post_dated_cheque]);
            }
        }
    }
    
    public function getLastQuotationRefNo()
    {
        $maxRefNo = Transaction::where("is_quotation", true)->where("type", "sell")->max("ref_no");
        $maxRefNo = $maxRefNo == "" ? 0 : $maxRefNo;
        return $maxRefNo + 1;
    }
}