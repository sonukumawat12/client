<?php

namespace App\Utils;

use App\Contact;
use App\ContactGroup;
use App\Transaction;
use App\TransactionPayment;
use Illuminate\Support\Facades\DB;
use Modules\Petro\Entities\CustomerPayment;
use Modules\Vat\Entities\VatPayment;
use Modules\Vat\Entities\VatPayableToAccount;

class ContactUtil
{

    public function __construct(){
        
    }
    
    public $payable_customer_txns = ['cheque_return', 'direct_customer_loan', 'customer_loan', 'property_sell', 'route_operation', 'expense', 'sell', 'fpos_sale', 'vat_price_adjustment','opening_balance','fleet_opening_balance'];
    public $payable_supplier_txns = ['cheque_return','property_purchase','expense','opening_balance','purchase'];
    
    public $transaction_types = ['vat_price_adjustment','direct_customer_loan','fleet_opening_balance','cheque_return','property_sell','route_operation','expense','sell','opening_balance','sell_return','fpos_sale','ledger_discount'];
    public $supplier_types = ['cheque_return','property_purchase','expense','opening_balance','purchase','purchase_return', '_deleted_purchase'];
    public $tax_txn_types = ['purchase','sell','expense','vat_penalty'];
     
    public function getCustomerBalance($contact_id,$business_id,$get_balance = false){
        
        $balance_details = array('opening_balance' => 0, 'total_sale' => 0, 'total_paid' => 0);
        
        $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->whereIn('transactions.type', $this->transaction_types)
                          ->orWhere(function ($query) {
                              $query->where('transactions.type', 'settlement')->where('transactions.sub_type','customer_loan');
                        });
                    })
                    ->whereNull('transactions.deleted_at')
                    ->where('transactions.status','final')
                    ->where('transactions.contact_id',$contact_id)
                    ->select([
                        DB::raw("SUM(IF(transactions.type = 'fleet_opening_balance', final_total, 0)) as fleet_opening_balance"),
                        DB::raw("SUM(IF(transactions.type = 'vat_price_adjustment', final_total, 0)) as vat_price_adjustment"),
                        DB::raw("SUM(IF(transactions.type = 'cheque_return', final_total, 0)) as cheque_return"),
                        DB::raw("SUM(IF(transactions.type = 'property_sell', final_total, 0)) as property_sell"),
                        DB::raw("SUM(IF(transactions.type = 'route_operation', final_total, 0)) as route_operation"),
                        DB::raw("SUM(IF(transactions.type = 'expense', final_total, 0)) as expense"),
                        DB::raw("SUM(IF(transactions.type = 'sell' OR transactions.type = 'fpos_sale', final_total, 0)) as sell"),
                        DB::raw("SUM(IF(transactions.type = 'opening_balance', final_total, 0)) as opening_balance"),
                        DB::raw("SUM(IF(transactions.type = 'sell_return', final_total, 0)) as sell_return"),
                        DB::raw("SUM(IF(transactions.type = 'direct_customer_loan', final_total, 0)) as direct_customer_loan"),
                        DB::raw("SUM(IF(transactions.sub_type = 'customer_loan', final_total, 0)) as customer_loan"),
                        DB::raw("SUM(IF(transactions.type = 'ledger_discount', final_total, 0)) as ledger_discount"),
                    ])
                    ->groupBy('contact_id')
                    ->first();
        $pmts = TransactionPayment::leftjoin('transactions','transaction_payments.transaction_id','transactions.id')
                    ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transaction_payments.business_id', $business_id)
                    ->whereNull('transaction_payments.deleted_at')
                    ->whereNull('transaction_payments.parent_id')
                    ->where(function ($query) {
                        $query->whereNull('transaction_payments.transaction_id')
                            ->orWhere(function ($query) {
                                $query->whereNotIn('transactions.type',['security_deposit', 'refund_security_deposit','security_deposit_refund','cheque_opening_balance']);
                        });
                    })
                    ->where('transaction_payments.payment_for',$contact_id)->sum('transaction_payments.amount');
        
       
        if(!empty($txns)){
            $balance_details['opening_balance'] = ($txns->fleet_opening_balance + $txns->opening_balance);
            $balance_details['total_sale'] = ($txns->cheque_return + $txns->direct_customer_loan + $txns->customer_loan + $txns->property_sell + $txns->route_operation + $txns->expense + $txns->sell + $txns->vat_price_adjustment) - ($txns->sell_return + $txns->ledger_discount);
        }
        
        $balance_details['total_paid'] = $pmts;
        
        $customer_payments = CustomerPayment::where('customer_id', $contact_id)->sum('sub_total');
        if(!empty($customer_payments)){
            $balance_details['total_paid'] += $customer_payments;
        }
        
        
        $balance = $balance_details['total_sale'] + $balance_details['opening_balance'] - $balance_details['total_paid'] ;
        if(!empty($get_balance)){
            return $balance;
        }
        
        $balance_details['total_balance'] = $balance;

        return $balance_details;
    }
    
    public function getSupplierBalance($contact_id,$business_id,$get_balance = false){
        
        $balance_details = array('opening_balance' => 0, 'total_purchase' => 0, 'total_paid' => 0);
        
        $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transactions.business_id', $business_id)
                    ->whereIn('transactions.type', $this->supplier_types)
                    ->whereNull('transactions.deleted_at')
                    ->whereIn('transactions.status',['final','received'])
                    ->where('transactions.contact_id',$contact_id)
                    ->select([
                        DB::raw("SUM(IF(transactions.type = 'cheque_return', final_total, 0)) as cheque_return"),
                        DB::raw("SUM(IF(transactions.type = 'property_purchase', final_total, 0)) as property_purchase"),
                        DB::raw("SUM(IF(transactions.type = 'expense', final_total, 0)) as expense"),
                        DB::raw("SUM(IF(transactions.type = 'purchase', final_total, 0)) as purchase"),
                        DB::raw("SUM(IF(transactions.type = '_deleted_purchase', final_total, 0)) as purchase_deleted"),
                        DB::raw("SUM(IF(transactions.type = 'opening_balance', final_total, 0)) as opening_balance"),
                        DB::raw("SUM(IF(transactions.type = 'purchase_return', final_total, 0)) as purchase_return"),
                        DB::raw("SUM(IF(transactions.type = 'ledger_discount', final_total, 0)) as ledger_discount"),
                    ])
                    ->groupBy('contact_id')
                    ->first();
                    
        $pmts = TransactionPayment::leftjoin('transactions','transaction_payments.transaction_id','transactions.id')
                    ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transaction_payments.business_id', $business_id)
                    ->whereNull('transaction_payments.deleted_at')
                    ->whereNull('transaction_payments.parent_id')
                    ->where(function ($query) {
                        $query->whereNull('transaction_payments.transaction_id')
                            ->orWhere(function ($query) {
                                $query->whereNotIn('transactions.type',['security_deposit', 'refund_security_deposit','security_deposit_refund','cheque_opening_balance']);
                        });
                    })
                    ->where('transaction_payments.payment_for',$contact_id)->sum('transaction_payments.amount');
                    
        if(!empty($txns)){
            $balance_details['opening_balance'] = ($txns->opening_balance);
            $balance_details['total_purchase'] = ($txns->cheque_return + $txns->property_purchase +  $txns->expense + $txns->purchase) - ($txns->purchase_deleted + $txns->purchase_return + $txns->ledger_discount);
            
        }
        
        $balance_details['total_paid'] = $pmts;
        
        
        
        $balance = $balance_details['total_purchase'] + $balance_details['opening_balance'] - $balance_details['total_paid'] ;
        if(!empty($get_balance)){
            return $balance;
        }
        
        $balance_details['total_balance'] = $balance;

        return $balance_details;
        
    }
    
    public function getCustomerBf($contact_id,$business_id,$start_date){
        $balance_details = array('opening_balance' => 0, 'total_sale' => 0, 'total_paid' => 0);
        
            $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->whereIn('transactions.type', $this->transaction_types)
                          ->orWhere(function ($query) {
                              $query->where('transactions.type', 'settlement')->where('transactions.sub_type','customer_loan');
                        });
                    })
                    ->whereNull('transactions.deleted_at')
                    ->where('transactions.status','final')
                    ->whereDate('transactions.transaction_date','<',$start_date)
                    ->where('transactions.contact_id',$contact_id)
                    ->select([
                        DB::raw("SUM(IF(transactions.type = 'fleet_opening_balance', final_total, 0)) as fleet_opening_balance"),
                        DB::raw("SUM(IF(transactions.type = 'vat_price_adjustment', final_total, 0)) as vat_price_adjustment"),
                        DB::raw("SUM(IF(transactions.type = 'cheque_return', final_total, 0)) as cheque_return"),
                        DB::raw("SUM(IF(transactions.type = 'property_sell', final_total, 0)) as property_sell"),
                        DB::raw("SUM(IF(transactions.type = 'route_operation', final_total, 0)) as route_operation"),
                        DB::raw("SUM(IF(transactions.type = 'expense', final_total, 0)) as expense"),
                       DB::raw("SUM(IF(transactions.type = 'sell' OR transactions.type = 'fpos_sale', final_total, 0)) as sell"),
                        DB::raw("SUM(IF(transactions.type = 'opening_balance', final_total, 0)) as opening_balance"),
                        DB::raw("SUM(IF(transactions.type = 'sell_return', final_total, 0)) as sell_return"),
                        DB::raw("SUM(IF(transactions.type = 'direct_customer_loan', final_total, 0)) as direct_customer_loan"),
                        DB::raw("SUM(IF(transactions.sub_type = 'customer_loan', final_total, 0)) as customer_loan"),
                        DB::raw("SUM(IF(transactions.type = 'ledger_discount', final_total, 0)) as ledger_discount"),
                    ])
                    ->groupBy('contact_id')
                    ->first();
        $pmts = TransactionPayment::leftjoin('transactions','transaction_payments.transaction_id','transactions.id')
                    ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transaction_payments.business_id', $business_id)
                    ->whereNull('transaction_payments.deleted_at')
                    ->whereNull('transaction_payments.parent_id')
                    ->where(function ($query) {
                        $query->whereNull('transaction_payments.transaction_id')
                            ->orWhere(function ($query) {
                                $query->whereNotIn('transactions.type',['security_deposit', 'refund_security_deposit','security_deposit_refund','cheque_opening_balance']);
                        });
                    })
                    ->whereDate('transaction_payments.paid_on','<',$start_date)
                    ->where('transaction_payments.payment_for',$contact_id)->sum('transaction_payments.amount');
                    
       
                    
       if(!empty($txns)){
            $balance_details['opening_balance'] = ($txns->fleet_opening_balance + $txns->opening_balance);
            $balance_details['total_sale'] = ($txns->cheque_return + $txns->direct_customer_loan + $txns->customer_loan + $txns->property_sell + $txns->route_operation + $txns->expense + $txns->sell + $txns->vat_price_adjustment) - ($txns->sell_return + $txns->ledger_discount);
            
        }
        
        $balance_details['total_paid'] = $pmts;
        
        $customer_payments = CustomerPayment::leftjoin('settlements','customer_payments.settlement_no','settlements.id')
                                            ->whereDate('settlements.transaction_date','<',$start_date)
                                            ->where('customer_payments.customer_id', $contact_id)
                                            ->sum('customer_payments.sub_total');
        if(!empty($customer_payments)){
            $balance_details['total_paid'] += $customer_payments;
        }
        
        
        $balance = $balance_details['total_sale'] + $balance_details['opening_balance'] - $balance_details['total_paid'] ;
        return $balance;
    }
    
    public function getCustomerTaxBf($business_id,$start_date,$minimum_date){
        $balance = 0;
        
            $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->whereIn('transactions.type', $this->tax_txn_types);
                    })
                    ->where('transactions.tax_amount' ,'>',0)
                    ->where(function ($query) {
                        $query->whereIn('transactions.type', ['sell','vat_penalty'])
                            ->orWhere('transactions.is_vat' ,1);
                    })
                    ->whereNull('transactions.deleted_at')
                    ->whereDate('transactions.transaction_date','<',$start_date)
                    ->whereDate('transactions.transaction_date','>',$minimum_date)
                    ->select([
                        DB::raw("SUM(IF(transactions.type = 'purchase', tax_amount, 0)) as purchase_tax"),
                        DB::raw("SUM(IF(transactions.type = 'expense', tax_amount, 0)) as expense_tax"),
                        DB::raw("SUM(IF(transactions.type = 'sell', tax_amount, 0)) as sell_tax"),
                        DB::raw("SUM(IF(transactions.type = 'vat_penalty', tax_amount, 0)) as penalty_tax")
                    ])
                    // ->groupBy('contact_id')
                    ->first();
                    
            if(!empty($txns)){
                $balance = $txns->sell_tax + $txns->penalty_tax - ($txns->purchase_tax + $txns->expense_tax) ;
            }
            
            $pmts = VatPayment::whereDate('date','<',$start_date)
                    ->where('business_id', $business_id)
                    ->whereDate('date','>',$minimum_date)
                    ->sum('amount');
            $obs = VatPayableToAccount::whereDate('created_at','<',$start_date)
                    ->whereDate('created_at','>',$minimum_date)
                    ->where('business_id', $business_id)
                    ->select([
                        DB::raw("SUM(IF(type = 'vat_receivable_account', amount, 0)) as input_ob"),
                        DB::raw("SUM(IF(type = 'vat_payable_account', amount, 0)) as output_ob")
                    ])->first();
                    
            if(!empty($obs)){
                $balance += $obs->ouput_ob - $obs->input_ob;
            }
            
            $balance -= $pmts;
            
        
        
        return $balance;
    }
    
    public function getCustomerTaxBalance($business_id,$minimum_date,$get_balance = false){
        $balance_details = array('input_tax' => 0, 'output_tax' => 0, 'total_paid' => 0);
        
            $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->whereIn('transactions.type', $this->tax_txn_types);
                    })
                    ->where('transactions.tax_amount' ,'>',0)
                    ->where(function ($query) {
                        $query->whereIn('transactions.type', ['sell','vat_penalty'])
                            ->orWhere('transactions.is_vat' ,1);
                    })
                    ->whereNull('transactions.deleted_at')
                    ->whereDate('transactions.transaction_date','>',$minimum_date)
                    ->select([
                        DB::raw("SUM(IF(transactions.type = 'purchase', tax_amount, 0)) as purchase_tax"),
                        DB::raw("SUM(IF(transactions.type = 'expense', tax_amount, 0)) as expense_tax"),
                        DB::raw("SUM(IF(transactions.type = 'sell', tax_amount, 0)) as sell_tax"),
                        DB::raw("SUM(IF(transactions.type = 'vat_penalty', tax_amount, 0)) as penalty_tax")
                    ])
                    ->groupBy('contact_id')
                    ->first();
                    
            if(!empty($txns)){
                $balance['input_tax'] = $txns->purchase_tax + $txns->expense_tax ;
                $balance['output_tax'] = $txns->sell_tax + $txns->penalty_tax;
            }
        
            $pmts = VatPayment::whereDate('date','>',$minimum_date)
                    ->where('business_id', $business_id)
                    ->sum('amount');
            $balance['total_paid'] = $pmts;
            
            $obs = VatPayableToAccount::whereDate('created_at','>',$minimum_date)
                    ->where('business_id', $business_id)
                    ->select([
                        DB::raw("SUM(IF(type = 'vat_receivable_account', amount, 0)) as input_ob"),
                        DB::raw("SUM(IF(type = 'vat_payable_account', amount, 0)) as output_ob")
                    ])->first();
                    
            if(!empty($obs)){
                $balance += $obs->ouput_ob - $obs->input_ob;
            }
                    
        
        
        $balance = $balance_details['output_tax'] - $balance_details['input_tax'] - $balance_details['total_paid'] ;
        if(!empty($get_balance)){
            return $balance;
        }
        
        $balance_details['total_balance'] = $balance;

        return $balance_details;
    }
    
    public function getCustomerTaxLedger($business_id,$start_date,$end_date,$minimum_date){
        $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transactions.business_id', $business_id)
                    ->where(function ($query) {
                        $query->whereIn('transactions.type', $this->tax_txn_types);
                    })
                    ->where('transactions.tax_amount' ,'>',0)
                    ->where(function ($query) {
                        $query->whereIn('transactions.type', ['sell','vat_penalty'])
                            ->orWhere('transactions.is_vat' ,1);
                    })
                    ->whereNull('transactions.deleted_at')
                    ->whereDate('transactions.transaction_date','>',$minimum_date)
                    ->whereDate('transactions.transaction_date','>=',$start_date)
                    ->whereDate('transactions.transaction_date','<=',$end_date)
                    ->select([
                        'transactions.id',
                        'transactions.transaction_date as date',
                        'transactions.type as type',
                        'transactions.tax_amount as amount',
                        'transactions.transaction_note'
                        
                    ]);
                    
        $pmts = VatPayment::where('vat_payments.business_id', $business_id)
                    ->whereDate('date','>',$minimum_date)
                    ->whereDate('date','>=',$start_date)
                    ->whereDate('date','<=',$end_date)
                    ->select([
                        'id',
                        'date as date',
                        DB::raw('"vat_payment" as type'),
                        'amount as amount',
                        'note as transaction_note'

                    ]);
                    
        $obs = VatPayableToAccount::where('business_id', $business_id)
                    ->whereDate('created_at','>',$minimum_date)
                    ->whereDate('created_at','>=',$start_date)
                    ->whereDate('created_at','<=',$end_date)
                    ->select([
                        'id',
                        'created_at as date',
                        DB::raw('
                            CASE 
                                WHEN type = "vat_receivable_account" THEN "input_ob"
                                WHEN type = "vat_payable_account" THEN "output_ob" 
                            END as type'
                        ),
                        'amount as amount',
                        'note as transaction_note'

                    ]);
                    
                    
        
        $txnResult = $txns->unionAll($pmts)->unionAll($obs)->orderBy('date', 'asc');
        
        
        return $txnResult->get();
    }
    
    public function getSupplierBf($contact_id,$business_id,$start_date){
        $balance_details = array('opening_balance' => 0, 'total_purchase' => 0, 'total_paid' => 0);
        $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transactions.business_id', $business_id)
                    ->whereIn('transactions.type', $this->supplier_types)
                    ->whereNull('transactions.deleted_at')
                    ->whereIn('transactions.status',['final','received'])
                    ->whereDate('transactions.transaction_date','<',$start_date)
                    ->where('transactions.contact_id',$contact_id)
                    ->select([
                        DB::raw("SUM(IF(transactions.type = 'cheque_return', final_total, 0)) as cheque_return"),
                        DB::raw("SUM(IF(transactions.type = 'property_purchase', final_total, 0)) as property_purchase"),
                        DB::raw("SUM(IF(transactions.type = 'expense', final_total, 0)) as expense"),
                        DB::raw("SUM(IF(transactions.type = 'purchase', final_total, 0)) as purchase"),
                        DB::raw("SUM(IF(transactions.type = '_deleted_purchase', final_total, 0)) as purchase_deleted"),
                        DB::raw("SUM(IF(transactions.type = 'opening_balance', final_total, 0)) as opening_balance"),
                        DB::raw("SUM(IF(transactions.type = 'purchase_return', final_total, 0)) as purchase_return"),
                        DB::raw("SUM(IF(transactions.type = 'ledger_discount', final_total, 0)) as ledger_discount"),
                    ])
                    ->groupBy('contact_id')
                    ->first();
                    
        $pmts = TransactionPayment::leftjoin('transactions','transaction_payments.transaction_id','transactions.id')
                    ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transaction_payments.business_id', $business_id)
                    ->whereNull('transaction_payments.deleted_at')
                    ->whereNull('transaction_payments.parent_id')
                    ->where(function ($query) {
                        $query->whereNull('transaction_payments.transaction_id')
                            ->orWhere(function ($query) {
                                $query->whereNotIn('transactions.type',['security_deposit', 'refund_security_deposit','security_deposit_refund','cheque_opening_balance']);
                        });
                    })
                    ->whereDate('transaction_payments.paid_on','<',$start_date)
                    ->where('transaction_payments.payment_for',$contact_id)->sum('transaction_payments.amount');
                    
       
                    
       if(!empty($txns)){
            $balance_details['opening_balance'] = ($txns->opening_balance);
            $balance_details['total_purchase'] = ($txns->cheque_return + $txns->property_purchase +  $txns->expense + $txns->purchase) - ($txns->purchase_deleted + $txns->purchase_return + $txns->ledger_discount);
            
        }
        
        $balance_details['total_paid'] = $pmts;
        
        
        $balance = $balance_details['total_purchase'] + $balance_details['opening_balance'] - $balance_details['total_paid'] ;
        return $balance;
    }
    
    public function getContactSummaryBf($contact_id,$business_id,$start_date){
        $balance_details = 0;
         $total_sale = 0; $total_paid = 0; $opening_balance = 0; $total_purchase = 0;
        $contact = Contact::findOrFail($contact_id);
        if($contact->type == 'supplier' || $contact->type == 'both'){
            $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                        ->where('transactions.business_id', $business_id)
                        ->whereIn('transactions.type', $this->supplier_types)
                        ->whereNull('transactions.deleted_at')
                        ->whereIn('transactions.status',['final','received'])
                        ->whereDate('transactions.transaction_date','<',$start_date)
                        ->where('transactions.contact_id',$contact_id)
                        ->select([
                            DB::raw("SUM(IF(transactions.type = 'cheque_return', final_total, 0)) as cheque_return"),
                            DB::raw("SUM(IF(transactions.type = 'property_purchase', final_total, 0)) as property_purchase"),
                            DB::raw("SUM(IF(transactions.type = 'expense', final_total, 0)) as expense"),
                            DB::raw("SUM(IF(transactions.type = 'purchase', final_total, 0)) as purchase"),
                            DB::raw("SUM(IF(transactions.type = '_deleted_purchase', final_total, 0)) as purchase_deleted"),
                            DB::raw("SUM(IF(transactions.type = 'opening_balance', final_total, 0)) as opening_balance"),
                            DB::raw("SUM(IF(transactions.type = 'purchase_return', final_total, 0)) as purchase_return"),
                            DB::raw("SUM(IF(transactions.type = 'ledger_discount', final_total, 0)) as ledger_discount"),
                        ])
                        ->groupBy('contact_id')
                        ->first();
                        
            $pmts = TransactionPayment::leftjoin('transactions','transaction_payments.transaction_id','transactions.id')
                        ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                        ->where('transaction_payments.business_id', $business_id)
                        ->whereNull('transaction_payments.deleted_at')
                        ->whereNull('transaction_payments.parent_id')
                        ->where(function ($query) {
                            $query->whereNull('transaction_payments.transaction_id')
                                ->orWhere(function ($query) {
                                    $query->whereNotIn('transactions.type',['security_deposit', 'refund_security_deposit','security_deposit_refund','cheque_opening_balance']);
                            });
                        })
                        ->whereDate('transaction_payments.paid_on','<',$start_date)
                        ->where('transaction_payments.payment_for',$contact_id)->sum('transaction_payments.amount');
                        
           
                        
           if(!empty($txns)){
                $opening_balance = ($txns->opening_balance);
                $total_purchase = ($txns->cheque_return + $txns->property_purchase +  $txns->expense + $txns->purchase) - ($txns->purchase_deleted + $txns->purchase_return + $txns->ledger_discount);
                
            }
            
            $total_paid = $pmts;
            
            $balance = $total_purchase + $opening_balance - $total_paid ;
            
            return $balance;
        }else{
            
           
            $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                        ->where('transactions.business_id', $business_id)
                        ->where(function ($query) {
                            $query->whereIn('transactions.type', $this->transaction_types)
                              ->orWhere(function ($query) {
                                  $query->where('transactions.type', 'settlement')->where('transactions.sub_type','customer_loan');
                            });
                        })
                        ->whereNull('transactions.deleted_at')
                        ->where('transactions.status','final')
                        ->whereDate('transactions.transaction_date','<',$start_date)
                        ->where('transactions.contact_id',$contact_id)
                        ->select([
                            DB::raw("SUM(IF(transactions.type = 'fleet_opening_balance', final_total, 0)) as fleet_opening_balance"),
                            DB::raw("SUM(IF(transactions.type = 'vat_price_adjustment', final_total, 0)) as vat_price_adjustment"),
                            DB::raw("SUM(IF(transactions.type = 'cheque_return', final_total, 0)) as cheque_return"),
                            DB::raw("SUM(IF(transactions.type = 'property_sell', final_total, 0)) as property_sell"),
                            DB::raw("SUM(IF(transactions.type = 'route_operation', final_total, 0)) as route_operation"),
                            DB::raw("SUM(IF(transactions.type = 'expense', final_total, 0)) as expense"),
                           DB::raw("SUM(IF(transactions.type = 'sell' OR transactions.type = 'fpos_sale', final_total, 0)) as sell"),
                            DB::raw("SUM(IF(transactions.type = 'opening_balance', final_total, 0)) as opening_balance"),
                            DB::raw("SUM(IF(transactions.type = 'sell_return', final_total, 0)) as sell_return"),
                            DB::raw("SUM(IF(transactions.type = 'direct_customer_loan', final_total, 0)) as direct_customer_loan"),
                            DB::raw("SUM(IF(transactions.sub_type = 'customer_loan', final_total, 0)) as customer_loan"),
                            DB::raw("SUM(IF(transactions.type = 'ledger_discount', final_total, 0)) as ledger_discount"),
                        ])
                        ->groupBy('contact_id')
                        ->first();
            $pmts = TransactionPayment::leftjoin('transactions','transaction_payments.transaction_id','transactions.id')
                        ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                        ->where('transaction_payments.business_id', $business_id)
                        ->whereNull('transaction_payments.deleted_at')
                        ->whereNull('transaction_payments.parent_id')
                        ->where(function ($query) {
                            $query->whereNull('transaction_payments.transaction_id')
                                ->orWhere(function ($query) {
                                    $query->whereNotIn('transactions.type',['security_deposit', 'refund_security_deposit','security_deposit_refund','cheque_opening_balance']);
                            });
                        })
                        ->whereDate('transaction_payments.paid_on','<',$start_date)
                        ->where('transaction_payments.payment_for',$contact_id)->sum('transaction_payments.amount');
                        
           
                        
           if(!empty($txns)){
                $opening_balance = ($txns->fleet_opening_balance + $txns->opening_balance);
                $total_sale = ($txns->cheque_return + $txns->direct_customer_loan + $txns->customer_loan + $txns->property_sell + $txns->route_operation + $txns->expense + $txns->sell + $txns->vat_price_adjustment) - ($txns->sell_return + $txns->ledger_discount);
                
            }
            
            $total_paid = $pmts;
            
            $customer_payments = CustomerPayment::leftjoin('settlements','customer_payments.settlement_no','settlements.id')
                                                ->whereDate('settlements.transaction_date','<',$start_date)
                                                ->where('customer_payments.customer_id', $contact_id)
                                                ->sum('customer_payments.sub_total');
            if(!empty($customer_payments)){
                $total_paid += $customer_payments;
            }
            
            
            $balance = $total_sale + $opening_balance - $total_paid ;
            return $balance;
            
        }
            
    }
    
    public function getContactSummaryLedger($contact_id,$business_id,$start_date){
        $balance_details = array('total_in' => 0, 'total_out' => 0);
        
        $contact = Contact::findOrFail($contact_id);
        if($contact->type == 'supplier' || $contact->type == 'both'){
            $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                        ->where('transactions.business_id', $business_id)
                        ->whereIn('transactions.type', $this->supplier_types)
                        ->whereNull('transactions.deleted_at')
                        ->whereIn('transactions.status',['final','received'])
                        ->whereDate('transactions.transaction_date','=',$start_date)
                        ->where('transactions.contact_id',$contact_id)
                        ->select([
                            DB::raw("SUM(IF(transactions.type = 'cheque_return', final_total, 0)) as cheque_return"),
                            DB::raw("SUM(IF(transactions.type = 'property_purchase', final_total, 0)) as property_purchase"),
                            DB::raw("SUM(IF(transactions.type = 'expense', final_total, 0)) as expense"),
                            DB::raw("SUM(IF(transactions.type = 'purchase', final_total, 0)) as purchase"),
                            DB::raw("SUM(IF(transactions.type = '_deleted_purchase', final_total, 0)) as purchase_deleted"),
                            DB::raw("SUM(IF(transactions.type = 'opening_balance', final_total, 0)) as opening_balance"),
                            DB::raw("SUM(IF(transactions.type = 'purchase_return', final_total, 0)) as purchase_return"),
                            DB::raw("SUM(IF(transactions.type = 'ledger_discount', final_total, 0)) as ledger_discount"),
                        ])
                        ->groupBy('contact_id')
                        ->first();
                        
            $pmts = TransactionPayment::leftjoin('transactions','transaction_payments.transaction_id','transactions.id')
                        ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                        ->where('transaction_payments.business_id', $business_id)
                        ->whereNull('transaction_payments.deleted_at')
                        ->whereNull('transaction_payments.parent_id')
                        ->where(function ($query) {
                            $query->whereNull('transaction_payments.transaction_id')
                                ->orWhere(function ($query) {
                                    $query->whereNotIn('transactions.type',['security_deposit', 'refund_security_deposit','security_deposit_refund','cheque_opening_balance']);
                            });
                        })
                        ->whereDate('transaction_payments.paid_on','=',$start_date)
                        ->where('transaction_payments.payment_for',$contact_id)->sum('transaction_payments.amount');
                        
           
                        
           if(!empty($txns)){
                $balance_details['total_in'] = ($txns->cheque_return + $txns->property_purchase +  $txns->expense + $txns->purchase) - ($txns->purchase_deleted + $txns->purchase_return + $txns->ledger_discount) + ($txns->opening_balance);
                
            }
            $balance_details['total_out'] = $pmts;
            return $balance_details;
        }else{
            
            $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                        ->where('transactions.business_id', $business_id)
                        ->where(function ($query) {
                            $query->whereIn('transactions.type', $this->transaction_types)
                              ->orWhere(function ($query) {
                                  $query->where('transactions.type', 'settlement')->where('transactions.sub_type','customer_loan');
                            });
                        })
                        ->whereNull('transactions.deleted_at')
                        ->where('transactions.status','final')
                        ->whereDate('transactions.transaction_date','=',$start_date)
                        ->where('transactions.contact_id',$contact_id)
                        ->select([
                            DB::raw("SUM(IF(transactions.type = 'fleet_opening_balance', final_total, 0)) as fleet_opening_balance"),
                            DB::raw("SUM(IF(transactions.type = 'vat_price_adjustment', final_total, 0)) as vat_price_adjustment"),
                            DB::raw("SUM(IF(transactions.type = 'cheque_return', final_total, 0)) as cheque_return"),
                            DB::raw("SUM(IF(transactions.type = 'property_sell', final_total, 0)) as property_sell"),
                            DB::raw("SUM(IF(transactions.type = 'route_operation', final_total, 0)) as route_operation"),
                            DB::raw("SUM(IF(transactions.type = 'expense', final_total, 0)) as expense"),
                           DB::raw("SUM(IF(transactions.type = 'sell' OR transactions.type = 'fpos_sale', final_total, 0)) as sell"),
                            DB::raw("SUM(IF(transactions.type = 'opening_balance', final_total, 0)) as opening_balance"),
                            DB::raw("SUM(IF(transactions.type = 'sell_return', final_total, 0)) as sell_return"),
                            DB::raw("SUM(IF(transactions.type = 'direct_customer_loan', final_total, 0)) as direct_customer_loan"),
                            DB::raw("SUM(IF(transactions.sub_type = 'customer_loan', final_total, 0)) as customer_loan"),
                            DB::raw("SUM(IF(transactions.type = 'ledger_discount', final_total, 0)) as ledger_discount"),
                        ])
                        ->groupBy('contact_id')
                        ->first();
            $pmts = TransactionPayment::leftjoin('transactions','transaction_payments.transaction_id','transactions.id')
                        ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                        ->where('transaction_payments.business_id', $business_id)
                        ->whereNull('transaction_payments.deleted_at')
                        ->whereNull('transaction_payments.parent_id')
                        ->where(function ($query) {
                            $query->whereNull('transaction_payments.transaction_id')
                                ->orWhere(function ($query) {
                                    $query->whereNotIn('transactions.type',['security_deposit', 'refund_security_deposit','security_deposit_refund','cheque_opening_balance']);
                            });
                        })
                        ->whereDate('transaction_payments.paid_on','=',$start_date)
                        ->where('transaction_payments.payment_for',$contact_id)->sum('transaction_payments.amount');
                        
           
                        
           if(!empty($txns)){
                $balance_details['total_in'] = ($txns->cheque_return + $txns->direct_customer_loan + $txns->customer_loan + $txns->property_sell + $txns->route_operation + $txns->expense + $txns->sell + $txns->vat_price_adjustment) - ($txns->sell_return + $txns->ledger_discount) + ($txns->fleet_opening_balance + $txns->opening_balance);
                
            }
            
            $balance_details['total_out'] = $pmts;
            
            $customer_payments = CustomerPayment::leftjoin('settlements','customer_payments.settlement_no','settlements.id')
                                                ->whereDate('settlements.transaction_date','=',$start_date)
                                                ->where('customer_payments.customer_id', $contact_id)
                                                ->sum('customer_payments.sub_total');
            if(!empty($customer_payments)){
                $balance_details['total_out'] += $customer_payments;
            }
            
            
            $balance = $balance_details;
            return $balance;
            
        }
            
    }
    
    public function getCustomerLedger($contact_id,$business_id,$start_date,$end_date){

        $txns = Transaction::leftjoin('business_locations', 'business_locations.id', 'transactions.location_id')
        ->leftjoin('air_ticket_invoices', 'transactions.id', 'air_ticket_invoices.transaction_id')
        ->leftjoin('account_transactions', 'account_transactions.transaction_id', 'transactions.id') 
        ->where('transactions.business_id', $business_id)
        ->where(function ($query) {
            $query->whereIn('transactions.type', $this->transaction_types)
                ->orWhere(function ($query) {
                    $query->where('transactions.type', 'settlement')->where('transactions.sub_type', 'customer_loan');
                })
                ->orWhere(function ($query) {
                    $query->where('transactions.type', 'refund');
                });
        })
        ->where('transactions.deleted_at',0)
        ->whereIn('transactions.status', ['final', 'received'])
        ->whereDate('transactions.transaction_date', '>=', $start_date)
        ->whereDate('transactions.transaction_date', '<=', $end_date)
        ->where('transactions.contact_id', $contact_id)
        ->select([
            'transactions.id',
            'transactions.transaction_date as date',
            'transactions.type as type',
            'transactions.invoice_no as invoice_no',
            'business_locations.name as location_name',
            'transactions.payment_status as payment_status',
            'transactions.final_total as amount',
            'transactions.cheque_return_charges as ch_charges',
            DB::raw('NULL as payment_row'),
            DB::raw('NULL as account_id'),
            'transactions.deleted_at',
            'transactions.deleted_by',
            'transactions.created_at as created_at',
            'transactions.ref_no as bill_no',
            'air_ticket_invoices.airticket_no as airticket_no',
            'account_transactions.cheque_number as cheque_number',
            'account_transactions.operation_date as operation_date',    
            'account_transactions.bank_name as bank_name',         
            DB::raw('NULL as paid_in_type'),
            DB::raw('NULL as is_settlement_customer_payment'),
            DB::raw('NULL as pump_operator'),
        ]) ->groupBy(['transactions.id'])
        ; // Ensure unique records

        $pmts = TransactionPayment::leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')
        ->leftjoin('business_locations', 'business_locations.id', 'transactions.location_id')
        ->leftjoin('air_ticket_invoices', 'transactions.id', 'air_ticket_invoices.transaction_id')
        ->leftjoin('account_transactions', 'account_transactions.transaction_id', 'transactions.id') // Direct join with account_transactions table
        ->where('transaction_payments.business_id', $business_id)
        ->whereNull('transaction_payments.parent_id')
        ->where(function ($query) {
            $query->whereNull('transaction_payments.transaction_id')
                ->orWhere(function ($query) {
                    $query->whereNotIn('transactions.type', ['security_deposit', 'refund_security_deposit', 'security_deposit_refund', 'cheque_opening_balance']);
                });
        })
        ->where('transaction_payments.payment_for', $contact_id)
        ->whereDate('transaction_payments.paid_on', '>=', $start_date)
        ->whereDate('transaction_payments.paid_on', '<=', $end_date)
        ->withTrashed()
        ->select([
            'transactions.id',
            'transaction_payments.paid_on as date',
            DB::raw('"payment" as type'),
            'transaction_payments.payment_ref_no as invoice_no',
            'business_locations.name as location_name',
            'transactions.payment_status as payment_status',
            'transaction_payments.amount as amount',
            DB::raw('NULL as ch_charges'),
            DB::raw('transaction_payments.id as payment_row'),
            DB::raw('transaction_payments.account_id as account_id'),
            'transaction_payments.deleted_at',
            'transaction_payments.deleted_by',
            'transaction_payments.created_at as created_at',
            'transaction_payments.payment_ref_no as bill_no',
            'air_ticket_invoices.airticket_no as airticket_no',
            'account_transactions.cheque_number as cheque_number', 
            'account_transactions.operation_date as operation_date',    
            'account_transactions.bank_name as bank_name',         
            'transaction_payments.paid_in_type',
            DB::raw('NULL as is_settlement_customer_payment'),
            DB::raw('NULL as pump_operator'),
        ]) ->groupBy(['transactions.id'])
        ; // Ensure unique records;

        $customer_payments = CustomerPayment::leftjoin('settlements', 'customer_payments.settlement_no', 'settlements.id')
        ->leftjoin('transactions', 'transactions.invoice_no', 'settlements.settlement_no')
        ->leftjoin('air_ticket_invoices', 'transactions.id', 'air_ticket_invoices.transaction_id')
        ->leftjoin('business_locations', 'business_locations.id', 'transactions.location_id')
        ->leftJoin('pump_operators', 'settlements.pump_operator_id', '=', 'pump_operators.id')
        ->leftjoin('account_transactions', 'account_transactions.transaction_id', 'transactions.id') // Direct join with account_transactions table
        ->whereDate('settlements.transaction_date', '>=', $start_date)
        ->whereDate('settlements.transaction_date', '<=', $end_date)
        ->where('customer_payments.customer_id', $contact_id)
        ->select([
            'transactions.id',
            'settlements.transaction_date as date',
            DB::raw('"customer_payment" as type'),
            'settlements.settlement_no as invoice_no',
            'business_locations.name as location_name',
            'transactions.payment_status as payment_status',
            'customer_payments.sub_total as amount',
            DB::raw('NULL as ch_charges'),
            DB::raw('customer_payments.id as payment_row'),
            DB::raw('customer_payments.bank_name as account_id'),
            DB::raw('NULL as deleted_at'),
            DB::raw('NULL as deleted_by'),
            'customer_payments.created_at as created_at',
            'settlements.settlement_no as bill_no',
            'air_ticket_invoices.airticket_no as airticket_no',
            DB::raw('NULL as paid_in_type'),
            DB::raw('"1" as is_settlement_customer_payment'),
            DB::raw('pump_operators.name as pump_operator'),
            'account_transactions.cheque_number as cheque_number', 
            'account_transactions.operation_date as operation_date',     
            'account_transactions.bank_name as bank_name',         
        ])->groupBy('customer_payments.id','transactions.id'); // Ensure unique records; ->groupBy(['transactions.id'])

        $txnResult = $txns->unionAll($pmts)->unionAll($customer_payments)->orderBy('id', 'asc');

        $transactions = $txnResult->get();

        return $transactions;



    }
    
    public function getCustomerLedgerList($contact_id,$business_id,$start_date,$end_date){
                    
        $txns = Transaction::leftjoin('business_locations', 'business_locations.id', 'transactions.location_id')
        ->leftjoin('air_ticket_invoices', 'transactions.id', 'air_ticket_invoices.transaction_id')
        ->leftjoin('account_transactions', 'account_transactions.transaction_id', 'transactions.id') 
        ->where(function ($query) {
            $query->whereIn('transactions.type', $this->transaction_types)
                ->orWhere(function ($query) {
                    $query->where('transactions.type', 'settlement')->where('transactions.sub_type', 'customer_loan');
                })
                ->orWhere(function ($query) {
                    $query->where('transactions.type', 'refund');
                });
        })
        ->whereNull('transactions.deleted_at')
        ->whereIn('transactions.status', ['final', 'received'])
        ->whereDate('transactions.transaction_date', '>=', $start_date)
        ->whereDate('transactions.transaction_date', '<=', $end_date)
        ->select([
            'transactions.id',
            'transactions.transaction_date as date',
            'transactions.type as type',
            'transactions.invoice_no as invoice_no',
            'business_locations.name as location_name',
            'transactions.payment_status as payment_status',
            'transactions.final_total as amount',
            'transactions.cheque_return_charges as ch_charges',
            DB::raw('NULL as payment_row'),
            DB::raw('NULL as account_id'),
            'transactions.deleted_at',
            'transactions.deleted_by',
            'transactions.created_at as created_at',
            'transactions.ref_no as bill_no',
            'air_ticket_invoices.airticket_no as airticket_no',
            'account_transactions.cheque_number as cheque_number',
            'account_transactions.operation_date as operation_date',    
            'account_transactions.bank_name as bank_name',         
            DB::raw('NULL as paid_in_type'),
            DB::raw('NULL as is_settlement_customer_payment'),
            DB::raw('NULL as pump_operator'),
        ]) ->groupBy(['transactions.id'])
        ; // Ensure unique records

        $pmts = TransactionPayment::leftjoin('transactions', 'transaction_payments.transaction_id', 'transactions.id')
        ->leftjoin('business_locations', 'business_locations.id', 'transactions.location_id')
        ->leftjoin('air_ticket_invoices', 'transactions.id', 'air_ticket_invoices.transaction_id')
        ->leftjoin('account_transactions', 'account_transactions.transaction_id', 'transactions.id') // Direct join with account_transactions table
        ->where('transaction_payments.business_id', $business_id)
        ->whereNull('transaction_payments.parent_id')
        ->where(function ($query) {
            $query->whereNull('transaction_payments.transaction_id')
                ->orWhere(function ($query) {
                    $query->whereNotIn('transactions.type', ['security_deposit', 'refund_security_deposit', 'security_deposit_refund', 'cheque_opening_balance']);
                });
        })
        ->where('transaction_payments.payment_for', $contact_id)
        ->whereDate('transaction_payments.paid_on', '>=', $start_date)
        ->whereDate('transaction_payments.paid_on', '<=', $end_date)
        ->withTrashed()
        ->select([
            'transactions.id',
            'transaction_payments.paid_on as date',
            DB::raw('"payment" as type'),
            'transaction_payments.payment_ref_no as invoice_no',
            'business_locations.name as location_name',
            'transactions.payment_status as payment_status',
            'transaction_payments.amount as amount',
            DB::raw('NULL as ch_charges'),
            DB::raw('transaction_payments.id as payment_row'),
            DB::raw('transaction_payments.account_id as account_id'),
            'transaction_payments.deleted_at',
            'transaction_payments.deleted_by',
            'transaction_payments.created_at as created_at',
            'transaction_payments.payment_ref_no as bill_no',
            'air_ticket_invoices.airticket_no as airticket_no',
            'account_transactions.cheque_number as cheque_number', 
            'account_transactions.operation_date as operation_date',    
            'account_transactions.bank_name as bank_name',         
            'transaction_payments.paid_in_type',
            DB::raw('NULL as is_settlement_customer_payment'),
            DB::raw('NULL as pump_operator'),
        ]) ->groupBy(['transactions.id'])
        ; // Ensure unique records;

        $customer_payments = CustomerPayment::leftjoin('settlements', 'customer_payments.settlement_no', 'settlements.id')
        ->leftjoin('transactions', 'transactions.invoice_no', 'settlements.settlement_no')
        ->leftjoin('air_ticket_invoices', 'transactions.id', 'air_ticket_invoices.transaction_id')
        ->leftjoin('business_locations', 'business_locations.id', 'transactions.location_id')
        ->leftJoin('pump_operators', 'settlements.pump_operator_id', '=', 'pump_operators.id')
        ->leftjoin('account_transactions', 'account_transactions.transaction_id', 'transactions.id') // Direct join with account_transactions table
        ->whereDate('settlements.transaction_date', '>=', $start_date)
        ->whereDate('settlements.transaction_date', '<=', $end_date)
        ->where('customer_payments.customer_id', $contact_id)
        ->select([
            'transactions.id',
            'settlements.transaction_date as date',
            DB::raw('"customer_payment" as type'),
            'settlements.settlement_no as invoice_no',
            'business_locations.name as location_name',
            'transactions.payment_status as payment_status',
            'customer_payments.sub_total as amount',
            DB::raw('NULL as ch_charges'),
            DB::raw('customer_payments.id as payment_row'),
            DB::raw('customer_payments.bank_name as account_id'),
            DB::raw('NULL as deleted_at'),
            DB::raw('NULL as deleted_by'),
            'customer_payments.created_at as created_at',
            'settlements.settlement_no as bill_no',
            'air_ticket_invoices.airticket_no as airticket_no',
            DB::raw('NULL as paid_in_type'),
            DB::raw('"1" as is_settlement_customer_payment'),
            DB::raw('pump_operators.name as pump_operator'),
            'account_transactions.cheque_number as cheque_number', 
            'account_transactions.operation_date as operation_date',     
            'account_transactions.bank_name as bank_name',         
        ])->groupBy('customer_payments.id','transactions.id')
        ; // Ensure unique records; ->groupBy(['transactions.id'])

        $txnResult = $txns->unionAll($pmts)->unionAll($customer_payments)->orderBy('id', 'asc');

        $transactions = $txnResult->get();

        return $transactions;
    }
    
    public function getSupplierLedger($contact_id,$business_id,$start_date,$end_date){
        $txns = Transaction::leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transactions.business_id', $business_id)
                    ->whereIn('transactions.type', $this->supplier_types)
                    ->whereNull('transactions.deleted_at')
                    ->whereIn('transactions.status',['final','received'])
                    ->whereDate('transactions.transaction_date','>=',$start_date)
                    ->whereDate('transactions.transaction_date','<=',$end_date)
                    ->where('transactions.contact_id',$contact_id)
                    ->select([
                        'transactions.id',
                        'transactions.transaction_date as date',
                        'transactions.type as type',
                        'transactions.invoice_no as invoice_no',
                        'business_locations.name as location_name',
                        'transactions.payment_status as payment_status',
                        'transactions.final_total as amount',
                        DB::raw('NULL as payment_row'),
                        DB::raw('NULL as account_id'),
                        'transactions.created_at as created_at',
                        'transactions.ref_no as bill_no'
                        
                    ]);
                    
        $pmts = TransactionPayment::leftjoin('transactions','transaction_payments.transaction_id','transactions.id')
                    ->leftjoin('business_locations','business_locations.id','transactions.location_id')
                    ->where('transaction_payments.business_id', $business_id)
                    ->whereNull('transaction_payments.deleted_at')
                    ->whereNull('transaction_payments.parent_id')
                    ->where(function ($query) {
                        $query->whereNull('transaction_payments.transaction_id')
                            ->orWhere(function ($query) {
                                $query->whereNotIn('transactions.type',['security_deposit', 'refund_security_deposit','security_deposit_refund','cheque_opening_balance']);
                        });
                    })
                    ->where('transaction_payments.payment_for',$contact_id)
                    ->whereDate('transaction_payments.paid_on','>=',$start_date)
                    ->whereDate('transaction_payments.paid_on','<=',$end_date)
                    ->select([
                        'transactions.id',
                        'transaction_payments.paid_on as date',
                        DB::raw('"payment" as type'),
                        'transaction_payments.payment_ref_no as invoice_no',
                        'business_locations.name as location_name',
                        DB::raw('NULL as payment_status'),
                        'transaction_payments.amount as amount',
                        DB::raw('transaction_payments.id as payment_row'),
                        DB::raw('transaction_payments.account_id as account_id'),
                        'transaction_payments.created_at',
                        'transaction_payments.payment_ref_no as bill_no'
                        
                        
                    ]);
                    
        
        $txnResult = $txns->unionAll($pmts)->orderBy('id', 'asc');
        
        
        return $txnResult->get();
    }
    
    public function getWalkInCustomer($business_id)
    {
        $contact = Contact::where('type', 'customer')
                    ->where('business_id', $business_id)
                    ->where('is_default', 1)
                    ->first()
                    ->toArray();

        if (!empty($contact)) {
            return $contact;
        } else {
            return false;
        }
    }
    /**
     * Returns Walk In Supplier for a Business
     *
     * @param int $business_id
     *
     * @return array/false
     */
    public function getDefaultSupplier($business_id)
    {
        $contact = Contact::where('type', 'supplier')
                    ->where('business_id', $business_id)
                    ->where('is_default', 1)
                    ->first();

        if (!empty($contact)) {
            return $contact->toArray();
        } else {
            return false;
        }
    }

    /**
     * Returns the customer group
     *
     * @param int $business_id
     * @param int $customer_id
     *
     * @return array
     */
    public function getCustomerGroup($business_id, $customer_id)
    {
        $cg = [];

        if (empty($customer_id)) {
            return $cg;
        }

        $contact = Contact::leftjoin('contact_groups as CG', 'contacts.customer_group_id', 'CG.id')
            ->where('contacts.id', $customer_id)
            ->where('CG.type', 'customer')
            ->where('contacts.business_id', $business_id)
            ->select('CG.*')
            ->first();

        return $contact;
    }
}
