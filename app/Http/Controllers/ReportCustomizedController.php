<?php

namespace App\Http\Controllers;

 

use App\Account;

use App\AccountGroup;

use App\AccountTransaction;

use App\AccountType;

use App\Brands;

use App\Business;

use App\BusinessLocation;

use App\CashRegister;

use App\Category;

use Modules\Petro\Entities\Pump;

use Modules\Petro\Entities\MeterSale;

use App\Contact;

use App\Currency;

use App\ContactGroup;

use App\ExpenseCategory;

use App\Journal;

use App\Store;
use App\VariationStoreDetail;
use App\Variation;

//Mahmoud Sabry
use App\MeterSales;
use App\SettlementChequePayments;
//

use App\PurchaseLine;

use App\Restaurant\ResTable;

use App\SellingPriceGroup;

use App\StockAdjustmentLine;

use App\Transaction;

use App\TransactionPayment;

use App\TransactionSellLine;

use App\TransactionSellLinesPurchaseLines;

use App\Unit;

use App\User;

use App\TaxRate;

use App\Utils\CashRegisterUtil;

use App\Utils\ModuleUtil;

use App\Utils\BusinessUtil;

use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

use App\Utils\Util;

use App\VariationLocationDetails;

use Modules\HR\Entities\WorkShift;

use Carbon\CarbonPeriod;

use Charts;

use Modules\Petro\Controllers\DailyStatusReportController;

use Illuminate\Support\Facades\DB;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

use Modules\Petro\Entities\CustomerPayment;

use Modules\Petro\Entities\PumpOperator;

use Modules\Fleet\Entities\Route;

use Modules\Petro\Entities\Settlement;

use Spatie\Activitylog\Models\Activity;

use Yajra\DataTables\Facades\DataTables;

use App\Charts\CommonChart;

use Illuminate\Support\Facades\Session;

use Modules\Petro\Entities\DipReading;

use Modules\Petro\Entities\FuelTank;

use Modules\Petro\Entities\TankSellLine;

use Modules\Superadmin\Entities\Subscription;

use Maatwebsite\Excel\Facades\Excel;

use App\Exports\VelidationMonthlyReportExport;

use App\Notifications\ReportsNotifications;
use Modules\Vat\Entities\VatSetting;
use App\Product;

class ReportCustomizedController extends Controller

{

    /**

     * All Utils instance.

     *

     */

    protected $transactionUtil;

    protected $cashRegisterUtil;

    protected $productUtil;

    protected $moduleUtil;

    protected $businessUtil;

    protected $util;

    /**

     * Create a new controller instance.

     *

     * @return void

     */

    public function __construct(CashRegisterUtil $cashRegisterUtil, TransactionUtil $transactionUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, Util $util, BusinessUtil $businessUtil)

    {

        $this->cashRegisterUtil = $cashRegisterUtil;

        $this->transactionUtil = $transactionUtil;

        $this->productUtil = $productUtil;

        $this->moduleUtil = $moduleUtil;

        $this->businessUtil = $businessUtil;

        $this->util = $util;
    }
    
   

    /**

     * Shows Product Related Reports

     *

     * @return \Illuminate\Http\Response

     */

   
    
     public function getProductReportCustomized(Request $request)

    {
         
 
        $business_id = $request->session()->get('user.business_id');
        $report_type = $request->report_type ?? 'daily';
        // 
        
        if (request()->ajax()) {
            
                    $start_date = $request->start_date;
                    $end_date = $request->end_date;
                    $business = Business::findOrFail($business_id);
                    
                    
                    if($report_type == 'daily'){    
                        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();
                        
                        $sub_categories = Category::where('business_id', $business_id)->where('parent_id', $fuel_category_id->id)->get();
                        
                        $fuel_products = Product::where('business_id', $business_id)->where('category_id',$fuel_category_id->id)->get();
                        
                        $prod_arr = [];
                        $sales_arr = [];
                        
                        $products_ob = [];
                        foreach($fuel_products as $prod){
                            
                           
                            // select fuel tanks
                            $tanks = FuelTank::where('business_id', $business_id)->where('product_id',$prod->id)->select('fuel_tank_number','id')->get();
                            $prod_total = 0;
                            foreach($tanks as $tank){
                                $balance = $this->transactionUtil->getTankBalanceByDate($tank->id,$start_date);
                                $tank->balance = $balance;
                                $prod_total += $balance;
                            }
                            $prod_arr[] = array('rowspan' => $tanks->count(),'tanks' => $tanks,'product_total' => $prod_total);
                            
                            $sales_ob = TransactionSellLine::leftjoin('transactions','transactions.id','transaction_sell_lines.transaction_id')
                                                            ->where('transaction_sell_lines.product_id',$prod->id)
                                                            ->where('transactions.transaction_date','<',$start_date)
                                                            ->sum('quantity') ?? 0;
                            $sales_today = TransactionSellLine::leftjoin('transactions','transactions.id','transaction_sell_lines.transaction_id')
                                                            ->where('transaction_sell_lines.product_id',$prod->id)
                                                            ->where('transactions.transaction_date','>=',$start_date)
                                                            ->where('transactions.transaction_date','<=',$end_date)
                                                            ->sum('quantity') ?? 0;
                                                            
                            $cumulative = $sales_ob + $sales_today;
                            
                            $sales_arr[] = array('product' => $prod->name,'ob' => $sales_ob,'today' => $sales_today,'cummulative' => $cumulative);
                            
                        }
                        
                        $pump_sales = [];
                        $ob_totals = [];
                        $highest = 0;
                        foreach($sub_categories as $cat){
                            $pumps = Pump::leftjoin('products','products.id','pumps.product_id')->where('products.sub_category_id',$cat->id)->where('products.business_id',$business_id)->select('pumps.id','pump_name','product_id','fuel_tank_id')->get();
                            
                            
                            if($pumps->count() > $highest){
                                $highest = $pumps->count();
                            }
                            
                            
                            $cat_total = 0;
                            foreach($pumps as $pump){
                                
                                $dpp = Variation::where('product_id',$pump->product_id)->first()->dpp_inc_tax ?? 0;
                                $tankbalance = $this->transactionUtil->getTankBalanceByDate($pump->fuel_tank_id,$start_date);
                                $cat_total += $dpp * $tankbalance;
                                
                                $meter_sale_income = MeterSale::leftjoin('settlements','settlements.id','meter_sales.settlement_no')
                                                        ->where('meter_sales.pump_id',$pump->id)
                                                        ->where('settlements.transaction_date','>=',$start_date)
                                                        ->where('settlements.transaction_date','<=',$end_date)
                                                        ->sum('sub_total');
                                                        
                                $meter_sale = MeterSale::leftjoin('settlements','settlements.id','meter_sales.settlement_no')
                                                        ->where('meter_sales.pump_id',$pump->id)
                                                        ->where('settlements.transaction_date','>=',$start_date)
                                                        ->where('settlements.transaction_date','<=',$end_date)
                                                        ->sum('qty');
                                $pump->total_sold = $meter_sale;
                                $pump->total_income = $meter_sale_income;
                            }
                            
                            $ob_totals[] = $cat_total;
                            $pump_sales[] = $pumps;
                        }
                        
                        
                        
                        return view('customized_reports.daily_sales_print')->with(compact('prod_arr','start_date','end_date','sales_arr','pump_sales','highest','ob_totals','business'));
                    }
                    
                    if($report_type == 'pumper'){
                        $pump_operator_sales_query = PumpOperator::leftjoin('settlements', 'pump_operators.id', 'settlements.pump_operator_id')
        
                            ->leftjoin('transactions', 'settlements.settlement_no', 'transactions.invoice_no')
                
                            ->where('transactions.status', 'final')
                
                            ->where('transactions.is_settlement', '1')
                
                            ->where('transactions.business_id', $business_id)
                
                            ->where('pump_operators.business_id', $business_id)
                
                            ->whereDate('transactions.transaction_date', '>=', $start_date)
                
                            ->whereDate('transactions.transaction_date', '<=', $end_date);
                
                        
                        $pump_operator_sales = $pump_operator_sales_query->select(
                
                            'pump_operators.name as pump_operator_name',
                            
                            DB::raw("SUM(IF(transactions.type = 'sell' AND transactions.is_settlement = '1' AND transactions.is_credit_sale = '0' , transactions.final_total, 0)) as grand_total"),
                
                            DB::raw('GROUP_CONCAT(DISTINCT settlements.settlement_no SEPARATOR ", ") as settlement_nos'),
                
                            DB::raw("SUM(IF(transactions.type = 'sell' AND transactions.is_credit_sale = '1' , transactions.final_total, 0)) as credit_sale_total"),
                
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'cash_payment', transactions.final_total, 0)) as cash_total"),
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'cash_deposit', transactions.final_total, 0)) as deposit_total"),
                            
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'loan_payment', transactions.final_total, 0)) as loan_total"),
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'customer_loan', transactions.final_total, 0)) as customer_loan_total"),
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'drawing_payment', transactions.final_total, 0)) as drawing_total"),
                            
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'cheque_payment', transactions.final_total, 0)) as cheque_total"),
                
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'card_payment', transactions.final_total, 0)) as card_total"),
                
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'shortage', transactions.final_total, 0)) as shortage_amount"),
                
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'excess', transactions.final_total, 0)) as excess_amount"),
                
                            DB::raw("SUM(IF(transactions.type = 'settlement' AND transactions.sub_type = 'expense', transactions.final_total, 0)) as expense_amount")
                
                        )->groupBy(['pump_operators.id'])->get();
                        
                        $sub_categories = Category::where('business_id', $business_id)->whereNotNull('parent_id')->whereNull('category_type')->get();
                        
                        $sub_sales = [];
                        foreach($sub_categories as $sub){
                            $sales_today_total = TransactionSellLine::leftJoin('transactions', 'transactions.id', '=', 'transaction_sell_lines.transaction_id')
                                                        ->leftJoin('products', 'products.id', '=', 'transaction_sell_lines.product_id')
                                                        ->leftJoin('variations', 'variations.product_id', '=', 'products.id')
                                                        ->where('products.sub_category_id', $sub->id)
                                                        ->where('transactions.transaction_date', '>=', $start_date)
                                                        ->where('transactions.transaction_date', '<=', $end_date)
                                                        ->selectRaw('SUM(transaction_sell_lines.quantity * variations.sell_price_inc_tax) as total_sales')
                                                        ->first();
                                                    
                            $total_sales_amount = $sales_today_total->total_sales ?? 0;
                            
                            $sub_sales[] = ['name' => $sub->name,'sales' => $total_sales_amount];

                        }
                        
                        
                        
                        
                        return view('customized_reports.pumper_print')->with(compact('pump_operator_sales','start_date','end_date','business','sub_sales'));
                        
                    }
                    
                }
                
        
                return view('customized_reports.reports');
    }
    
}
