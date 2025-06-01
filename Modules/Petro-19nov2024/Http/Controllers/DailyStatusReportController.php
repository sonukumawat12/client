<?php

namespace Modules\Petro\Http\Controllers;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Response;
use DB;
use Session;
;
use Mpdf\Mpdf;
use Yajra\DataTables\Facades\DataTables;

use App\AccountTransaction;
use App\Business;
use App\BusinessLocation;
use App\Product;
use App\Contact;
use App\System;
use App\Transaction;
use App\TransactionSellLine;
use App\PurchaseLine;
use App\Store;
use App\Category;
use App\Utils\Util;
use App\Utils\ProductUtil;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use App\Utils\BusinessUtil;

use Modules\Petro\Entities\DipReading;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\DipResetting;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\Settlement;
use Modules\Petro\Entities\MeterSale;
use Modules\Petro\Entities\OtherSale;
use Modules\Superadmin\Entities\TankDipChart;
use Modules\Superadmin\Entities\TankDipChartDetail;
use Modules\Petro\Entities\TankPurchaseLine;
use Modules\Petro\Entities\TankSellLine;

class DailyStatusReportController extends Controller
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

    public function index(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $default_start = new \Carbon('today');
        $default_end = new \Carbon('today');
        $start_date = !empty($request->get('start_date')) ? date('y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
        $end_date = !empty($request->get('end_date')) ? date('y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');
        if ($request->ajax()) {
            $dip_details = $this->_getDipDetails($business_id, $start_date, $end_date);    
            $business_details = Business::find($business_id);
            $datatable = Datatables::of($dip_details)
                ->editColumn('dip_reading', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->dip_reading, false, $business_details, true); 
                })
                ->editColumn('qty_liters', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->fuel_balance_dip_reading, false, $business_details, true); 
                })
                ->editColumn('qty_system', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->current_qty, false, $business_details, true); 
                })
                ->addColumn('difference', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->fuel_balance_dip_reading - $row->current_qty, false, $business_details, true); 
                });
            return $datatable->make(true);
        }
        
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('petro::daily_status_report.index')->with(compact(
            'business_locations'
        ));
    }

    public function getTotalPayments(Request $request) {
        $business_id = request()->session()->get('user.business_id');
        $default_start = new \Carbon('today');
        $default_end = new \Carbon('today');
        $start_date = !empty($request->get('start_date')) ? date('y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
        $end_date = !empty($request->get('end_date')) ? date('y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');
        if ($request->ajax()) {
            return $this->_getTotalPayments($business_id, $start_date, $end_date);
        }
    }
    public function getPumpSales(Request $request)
    {
        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $default_start = new \Carbon('today');
            $default_end = new \Carbon('today');
            $start_date = !empty($request->get('start_date')) ? date('y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
            $end_date = !empty($request->get('end_date')) ? date('y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');
            $results = $this->_getPumpSales($business_id, $start_date, $end_date);
            $business_details = Business::find($business_id);
            $datatable = Datatables::of($results)
               ->editColumn('previous_meter', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->starting_meter, false, $business_details, true); 
                })
                ->editColumn('today_meter', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->closing_meter, false, $business_details, true); 
                })
                ->editColumn('sold_qty', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->sold_qty, false, $business_details, true); 
                })
                ->editColumn('amount', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->amount, false, $business_details, true); 
                })
                ->editColumn('banked', function($row) use ($business_details) {
                    if (empty($row->banked)) {
                        return 0;
                    } 
                    return $this->productUtil->num_f($row->banked, false, $business_details, true); 
                })
                ->editColumn('locker', function($row) use ($business_details) {
                    if (empty($row->locker)) {
                        return 0;
                    } 
                    return $this->productUtil->num_f($row->locker, false, $business_details, true); 
                })
                ->editColumn('card', function($row) use ($business_details) {
                    if (empty($row->card)) {
                        return 0;
                    } 
                    return $this->productUtil->num_f($row->card, false, $business_details, true); 
                });
            return $datatable->make(true);
        }
    }
    
    public function getFuelSale(Request $request) 
    {
        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $default_start = new \Carbon('today');
            $default_end = new \Carbon('today');
            $start_date = !empty($request->get('start_date')) ? date('y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
            $end_date = !empty($request->get('end_date')) ? date('y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');
            $category = Category::where('business_id', $business_id)
                ->where('parent_id', 0)
                ->where('name', 'Fuel')
                ->first();

            $sub_categories = Category::subCategoryOnlyFuel($business_id);
            
            $fuel_sales = $this->_getFuelSales($business_id, $category, $start_date, $end_date);
            
            $business_details = Business::find($business_id);

            $datatable = Datatables::of($fuel_sales)
                ->editColumn('qty', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->qty, false, $business_details, true); 
                })
                ->editColumn('value', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->value, false, $business_details, true); 
                })
                ->make(true);
                
            return $datatable;
        }
    }

    public function getLubricantSale(Request $request) 
    {
        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $default_start = new \Carbon('today');
            $default_end = new \Carbon('today');
            $start_date = !empty($request->get('start_date')) ? date('y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
            $end_date = !empty($request->get('end_date')) ? date('y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');

            $datatable = $this->makeDataTable($business_id, 'Lubricants', 'Lubricants', $start_date, $end_date);
            
            return $datatable->make(true);
        }
    }
    
    public function getOtherSale(Request $request) 
    {
        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $default_start = new \Carbon('today');
            $default_end = new \Carbon('today');
            $start_date = !empty($request->get('start_date')) ? date('y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
            $end_date = !empty($request->get('end_date')) ? date('y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');
            
            $other_sales = OtherSale::join('settlements','settlements.id','other_sales.settlement_no')
                                    ->leftjoin('business_locations','business_locations.id','settlements.location_id')
                                    ->join('products','products.id','other_sales.product_id')
                                    ->whereDate('settlements.transaction_date','>=',$start_date)
                                    ->whereDate('settlements.transaction_date','<=',$end_date)
                                    ->select([
                                        'products.name as product',
                                        'business_locations.name as location_name',
                                        'other_sales.qty as sold_qty',
                                        'other_sales.sub_total as amount',
                                        'other_sales.balance_stock as balance_qty'
                                    ])->get();
        

            $datatable = Datatables::of($other_sales)
            ->editColumn('sold_qty', function ($row) {
                        return $this->productUtil->num_f($row->sold_qty);
                })
            ->addColumn('balance_qty', function ($row) {
                    
                    return $this->productUtil->num_f($row->balance_qty);
                })
                ->editColumn('amount', function ($row) {
                    return $this->productUtil->num_f($row->amount);
                });
            
            return $datatable->make(true);
            
        }
    }
    
    public function getGasSale(Request $request) 
    {
        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $default_start = new \Carbon('today');
            $default_end = new \Carbon('today');
            $start_date = !empty($request->get('start_date')) ? date('y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
            $end_date = !empty($request->get('end_date')) ? date('y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');

            $datatable = $this->makeDataTable($business_id, 'Gas', '', $start_date, $end_date);
            
            return $datatable->make(true);
        }
    }
    
    public function getCreditSale(Request $request) {
        if ($request->ajax()) {
            $business_id = request()->session()->get('user.business_id');
            $default_start = new \Carbon('today');
            $default_end = new \Carbon('today');
            $start_date = !empty($request->get('start_date')) ? date('y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
            $end_date = !empty($request->get('end_date')) ? date('y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');
            $settlement = $this->_getCreditSales($business_id, $start_date, $end_date);

            $business_details = Business::find($business_id);

            $datatable = Datatables::of($settlement)
                ->editColumn('amount', function($row) use ($business_details) {
                    return $this->productUtil->num_f($row->amount, false, $business_details, true); 
                })
                ->make(true);
                
            return $datatable;
        }
    }
    
    public function makeDataTable($business_id, $category, $sub_category, $start_date, $end_date) {
        
            $products = $this->_getCategorySales($business_id, $category, $sub_category, $start_date, $end_date);

            $datatable = Datatables::of($products)
            
                ->removeColumn('enable_stock')

                ->removeColumn('unit')

                ->removeColumn('id')
                
                ->addColumn('purchase_qty', function ($row) use ($start_date, $end_date) {

                    $html = "";

                    $business_id = request()->session()->get('user.business_id');
                     
                    $business_details = Business::find($business_id);
                    
                    if ($row->tran_type == 'purchase_return') {

                        $res = $this->productUtil->num_f($row->returned_qty, false, $business_details, true);

                        if ($res > 0.00) {

                            $html = '-' . $res;
                        } else {

                            $html = $res;
                        }
                    } else if ($row->tran_type == 'stock_adjustment') {

                        if ($row->addjust_type == 'increase') {

                            $html = $this->productUtil->num_f($row->stock_qty, false, $business_details, true);
                            
                        } else if ($row->addjust_type == 'decrease') {

                            $res = $this->productUtil->num_f($row->stock_qty, false, $business_details, true);

                            if ($res > 0.00) {

                                $html = '-' . $res;
                            } else {

                                $html = $res;
                            }
                        }
                    } else {

                        $purchase_qty = 0.0;

                        if (!empty($row->purchase_qty)) {

                            $purchase_qty = $row->purchase_qty;
                        }

                        $html = $this->productUtil->num_f($purchase_qty);
                    }

                    return $html;
                })

                ->addColumn('sold_qty', function ($row) {
                    
                    $business_id = request()->session()->get('user.business_id');
                    
                    $business_details = Business::find($business_id);

                    if ($row->tran_type == 'sell_return') {

                        $res = $this->productUtil->num_f($row->sell_return, false, $business_details, true);

                        if ($res > 0.00) {

                            $html = '-' . $res;

                            return $html;
                        } else {

                            return $res;
                        }
                    } else {

                        $sold_qty = 0.0;

                        if (!empty($row->sold_qty)) {

                            $sold_qty = $row->sold_qty;
                        }

                        return $this->productUtil->num_f($sold_qty, false, $business_details, true);
                    }
                })

                ->addColumn('starting_qty', function ($row) {

                    $business_id = request()->session()->get('user.business_id');

                    $business_details = Business::find($business_id);

                    // first time new value for product next time previous

                    if (Session::get($row->product_id)) {

                        $balance = str_replace(',', '', Session::get($row->product_id));
                    } else {

                        $balance = 0;

                        $balance = ($row->purchase_qty + $row->sell_return) - ($row->sold_qty

                            + $row->purchase_return);

                        $balance = $balance;

                        Session::put($row->product_id, $balance);
                    }

                    return $this->productUtil->num_f($balance, false, $business_details, true);
                })

                ->addColumn('balance_qty', function ($row) {
                    
                    $business_id = request()->session()->get('user.business_id');
                   
                    $business_details = Business::find($business_id);

                    $starting_qty = ($row->purchase_qty + $row->sell_return) - ($row->sold_qty + $row->purchase_return);

                    if ($row->tran_type == 'stock_adjustment') {

                        if ($row->addjust_type == 'increase') {

                            $row->purchase_qty = $this->productUtil->num_uf($row->stock_qty);
                        } else if ($row->addjust_type == 'decrease') {

                            $res = $this->productUtil->num_uf($row->stock_qty);

                            $row->purchase_return = $res;
                        }
                    }

                    $balance = 0;

                    if (Session::get($row->product_id)) {

                        $oldbalance = str_replace(',', '', Session::get($row->product_id));

                        $balance = ($oldbalance + $row->purchase_qty + $row->sell_return)

                            - ($row->sold_qty + $row->purchase_return);

                        $balance = $balance;

                        Session::put($row->product_id, $balance);
                    } else {

                        $balance = ($starting_qty + $row->purchase_qty + $row->sell_return)

                            - ($row->sold_qty + $row->purchase_return);

                        $balance = $balance;

                        Session::put($row->product_id, $balance);
                    }

                    return $this->productUtil->num_f($balance, false, $business_details, true);
                })
                ->editColumn('amount', function ($row) {
                    $business_id = request()->session()->get('user.business_id');
                    $business_details = Business::find($business_id);
                    return $this->productUtil->num_f($row->amount, false, $business_details, true);
                });

                return $datatable;    
    }
    
    public function printReport(Request $request) {

        $business_id = request()->session()->get('user.business_id');
        $default_start = new \Carbon('today');
        $default_end = new \Carbon('today');
        $start_date = !empty($request->get('start_date')) ? date('y-m-d', strtotime($request->get('start_date'))) : $default_start->format('Y-m-d');
        $end_date = !empty($request->get('end_date')) ? date('y-m-d', strtotime($request->get('end_date'))) : $default_end->format('Y-m-d');

        $dip_sales = $this->_getDipDetails($business_id, $start_date, $end_date);
        
        $pump_sales = $this->_getPumpSales($business_id, $start_date, $end_date);
        
        $category = Category::where('business_id', $business_id)
                ->where('parent_id', 0)
                ->where('name', 'Fuel')
                ->first();
                
        $sub_categories = Category::subCategoryOnlyFuel($business_id);
        
        $fuel_sales = $this->_getFuelSales($business_id, $category , $start_date, $end_date);
        
        $lubricant_sales = $this->_getCategorySales($business_id, 'Lubricants', 'Lubricants', $start_date, $end_date);
        
        $other_sales = $this->_getCategorySales($business_id, 'Others', 'Others', $start_date, $end_date);
        
        $gas_sales = $this->_getCategorySales($business_id, 'Gas', 'Gas', $start_date, $end_date);
        
        $credit_sales = $this->_getCreditSales($business_id, $start_date, $end_date);
        
        $total_payments = $this->_getTotalPayments($business_id, $start_date, $end_date);

        return view('petro::daily_status_report.print')->with(compact(
                'dip_sales',
                'pump_sales',
                'fuel_sales',
                'lubricant_sales',
                'other_sales',
                'gas_sales',
                'credit_sales',
                'total_payments',
                'start_date',
                'end_date'
            ));
    }
    
    public function downloadPdf( Request $request){
        $html = $request->get('html');
        $mpdf = new Mpdf();
        $mpdf->SetFont('Calibri', '', 12);
        $mpdf->WriteHTML($html);
        
        $directoryPath = config('constants.reports_directory');
        if (!is_dir($directoryPath)) {
            mkdir($directoryPath, 0755, true);
        }
        
        $filename = Str::random(40).".pdf";
        $filePath = config('constants.reports_directory').$filename;
        $mpdf->Output($filePath, 'F');
        
        return response()->json(['path' => url("reports/".$filename)]);

    }
    
    public function _getDipDetails($business_id, $start_date, $end_date)
    {
        $fuel_tanks = FuelTank::leftjoin('business_locations','business_locations.id','fuel_tanks.location_id')->select([
                                    'fuel_tanks.fuel_tank_number as tank_no',
                                    'business_locations.name as location_name',
                                    DB::raw('(SELECT dip_reading FROM dip_readings as DR WHERE DR.tank_id = fuel_tanks.id AND STR_TO_DATE(DR.date_and_time, "%m/%d/%Y") <= "'.$end_date.'" ORDER BY DR.id DESC LIMIT 1) as dip_reading'),
                                    
                                    DB::raw('(SELECT fuel_balance_dip_reading FROM dip_readings as DR WHERE DR.tank_id = fuel_tanks.id AND STR_TO_DATE(DR.date_and_time, "%m/%d/%Y") <= "'.$end_date.'" ORDER BY DR.id DESC LIMIT 1) as fuel_balance_dip_reading'),
                                    
                                    DB::raw('(SELECT current_qty FROM dip_readings as DR WHERE DR.tank_id = fuel_tanks.id AND STR_TO_DATE(DR.date_and_time, "%m/%d/%Y") <= "'.$end_date.'" ORDER BY DR.id DESC LIMIT 1) as current_qty'),
                                ])
                                ->groupBy('fuel_tanks.id');
            if(!empty(request()->location_id)){
                $fuel_tanks->where('fuel_tanks.location_id',request()->location_id);
            }
                                

        return $fuel_tanks->get();;
    }
    
    public function _getPumpSales($business_id, $start_date, $end_date) {
    
        
        $results = Pump::leftjoin('business_locations','business_locations.id','pumps.location_id')->where('pumps.business_id',$business_id)->select('pump_no','pumps.id','business_locations.name as location_name');
        
        if(!empty(request()->location_id)){
            $results->where('pumps.location_id',request()->location_id);
        }
        
        $data = [];
        foreach($results->get() as $one){
            
            $starting_meter = MeterSale::join('settlements','settlements.id','meter_sales.settlement_no')->where('meter_sales.pump_id',$one->id)->whereDate('settlements.transaction_date','<',$start_date)->orderBy('meter_sales.id','DESC')->select('closing_meter')->first();
            $closing_meter = MeterSale::join('settlements','settlements.id','meter_sales.settlement_no')->where('meter_sales.pump_id',$one->id)->whereDate('settlements.transaction_date','<=',$end_date)->orderBy('meter_sales.id','DESC')->select('closing_meter')->first();
            $sales = MeterSale::join('settlements','settlements.id','meter_sales.settlement_no')->where('meter_sales.pump_id',$one->id)->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->orderBy('meter_sales.id','DESC')->select([DB::raw('SUM(meter_sales.sub_total) as amount'),DB::raw('SUM(meter_sales.qty) as sold_qty')])->first();
            
            $banked = MeterSale::join('settlements','settlements.id','meter_sales.settlement_no')->join('settlement_cash_deposits','settlements.id','settlement_cash_deposits.settlement_no')->where('meter_sales.pump_id',$one->id)->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->sum('settlement_cash_deposits.amount');
            $locker = MeterSale::join('settlements','settlements.id','meter_sales.settlement_no')->join('settlement_cash_payments','settlements.id','settlement_cash_payments.settlement_no')->where('meter_sales.pump_id',$one->id)->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->sum('settlement_cash_payments.amount');
            $card = MeterSale::join('settlements','settlements.id','meter_sales.settlement_no')->join('settlement_card_payments','settlements.id','settlement_card_payments.settlement_no')->where('meter_sales.pump_id',$one->id)->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->sum('settlement_card_payments.amount');
            
            
            $one->starting_meter = !empty($starting_meter) ? $starting_meter->closing_meter : 0;
            $one->closing_meter = !empty($closing_meter) ? $closing_meter->closing_meter : 0;
            $one->amount = !empty($sales) ? $sales->amount : 0;;
            $one->sold_qty = !empty($sales) ? $sales->sold_qty : 0;;
            $one->banked = $banked;
            $one->locker = $locker;
            $one->card = 0;
            $data[] = $one;
        }
        
        return $data;
    }
    
    
    public function _getFuelSales($business_id, $category, $start_date, $end_date) {
        
        $query = TransactionSellLine::leftjoin(

                'transactions as t',

                'transaction_sell_lines.transaction_id',

                '=',

                't.id'

            )

                ->leftjoin(

                    'variations as v',

                    'transaction_sell_lines.variation_id',

                    '=',

                    'v.id'

                )
                
                ->leftjoin('business_locations','business_locations.id','t.location_id')

                ->leftjoin('product_variations as pv', 'v.product_variation_id', '=', 'pv.id')

                ->leftjoin('contacts as c', 't.contact_id', '=', 'c.id')
                
                ->leftjoin('products as p', 'pv.product_id', '=', 'p.id')
                
                ->leftJoin('categories as c2', 'p.sub_category_id', '=', 'c2.id')

                ->leftjoin('tax_rates', 'transaction_sell_lines.tax_id', '=', 'tax_rates.id')

                ->leftjoin('units as u', 'p.unit_id', '=', 'u.id')

                ->where('t.business_id', $business_id)

                ->where('t.type', 'sell')

                ->where('t.status', 'final')

                ->where(function ($q) {

                    $q->where('t.sub_type', '!=', 'credit_sale')->orWhereNull('t.sub_type');
                })
                ->where('p.category_id', $category->id)

                ->select(

                    'c2.name as name',
                    
                    'business_locations.name as location_name',

                    DB::raw('SUM(transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) as qty'),

                    DB::raw('SUM((transaction_sell_lines.quantity - transaction_sell_lines.quantity_returned) * transaction_sell_lines.unit_price_inc_tax) as value')

                )

                ->groupBy(['p.sub_category_id']);
                
            if (!empty($start_date) && !empty($end_date)) {

                $query->whereBetween(DB::raw('date(transaction_date)'), [$start_date, $end_date]);
            }
            
            if(!empty(request()->location_id)){
                $query->where('t.location_id',request()->location_id);
            }
            
            
            return $query->get();
    }
    
    public function _getCategorySales($business_id, $category_name, $sub_category_name, $start_date, $end_date) {
        
        $category = Category::where('business_id', $business_id)
        ->where('parent_id', 0)
        ->where('name', $category_name)
        ->first();
        
        $query = Transaction::leftjoin('purchase_lines as pl', 'transactions.id', 'pl.transaction_id')
        
                ->leftjoin('business_locations','business_locations.id','transactions.location_id')

                ->leftjoin('purchase_lines as PRL', 'transactions.return_parent_id', 'PRL.transaction_id')

                ->leftjoin('transaction_sell_lines as tsl', function ($join) {

                    $join->on('transactions.id', 'tsl.transaction_id');
                })

                ->leftjoin('transaction_sell_lines as SRL', 'transactions.return_parent_id', 'SRL.transaction_id')

                ->leftjoin('stock_adjustment_lines', 'transactions.id', 'stock_adjustment_lines.transaction_id')

                ->leftjoin('products as p', function ($join) {

                    $join->on('pl.product_id', 'p.id')

                        ->orOn('tsl.product_id', 'p.id')

                        ->orOn('stock_adjustment_lines.product_id', 'p.id')

                        ->orOn('PRL.product_id', 'p.id')

                        ->orOn('SRL.product_id', 'p.id');
                })

                ->leftjoin('variations', 'p.id', 'variations.product_id')

                ->leftjoin('units', 'p.unit_id', '=', 'units.id')

                ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')

                ->leftjoin('variation_store_details as vsd', 'variations.id', '=', 'vsd.variation_id')

                ->leftjoin('product_variations as pv', 'variations.product_variation_id', '=', 'pv.id')

                ->where('transactions.sub_type', '!=', 'credit_sale')

                ->where('p.business_id', $business_id)->withTrashed();
                
                
            if (!empty($start_date) && !empty($end_date)) {

                $query->whereDate('transaction_date', '>=', $start_date)

                    ->whereDate('transaction_date', '<=', $end_date);
            }
            
            if (!empty($category)) {
                
                $query->where('p.category_id', $category->id);
                
                $sub_category = Category::where('business_id', $business_id)
                            ->where('parent_id', $category->id)
                            ->where('name', $sub_category_name)
                            ->select(['name', 'id'])
                            ->first();
            }
            
            if (!empty($sub_category)) {
                
                $query->where('p.sub_category_id', $sub_category->id);
                
            }
            if(!empty(request()->location_id)){
                $query->where('transactions.location_id',request()->location_id);
            }
                
            $products = $query->select(
                
                DB::raw('SUM(IF(transactions.deleted_at IS NULL, tsl.quantity, -1 * tsl.quantity) ) as sold_qty'),

                DB::raw('SUM(SRL.quantity_returned) as sell_return'),

                DB::raw('SUM(IF(transactions.deleted_at IS NULL, PRL.quantity, -1* PRL.quantity) ) as purchase_qty'),

                DB::raw('SUM(PRL.quantity_returned ) as returned_qty'),
                
                DB::raw('SUM(PRL.quantity_returned) as purchase_return'),

                DB::raw('SUM(stock_adjustment_lines.quantity ) as stock_qty'),

                DB::raw('stock_adjustment_lines.type as addjust_type'),
                

                'p.name as product',

                'p.id as product_id',

                'p.enable_stock as enable_stock',

                'pv.name as product_variation',

                'variations.name as variation_name',

                'pl.purchase_price_inc_tax as purchase_price',

                'pl.bonus_qty as bonus_qty',

                
                'tsl.unit_price_inc_tax as amount',

                'transactions.transaction_date as transaction_date',

                'transactions.id as transaction_id',
                'business_locations.name as location_name'
            )

                ->orderBy('transactions.id', 'asc')

                ->groupBy(['transactions.id', 'p.id']);
            return $products->get();
    }
    
    public function _getCreditSales($business_id, $start_date, $end_date) {
        $settlement = Settlement::where('settlements.business_id', $business_id)
            ->leftjoin('settlement_credit_sale_payments as cs', 'cs.settlement_no', 'settlements.id')
            ->leftjoin('contacts', 'contacts.id', 'cs.customer_id')
            ->leftjoin('business_locations','business_locations.id','settlements.location_id')
            ->select(
                'contacts.name as customer',
                'business_locations.name as location_name',
                DB::raw('SUM(cs.qty * cs.price) as amount')
            )
            ->whereNotNull('contacts.name')->where('contacts.name', '<>', '')
            ->groupBy('contacts.id');
            
   
       if (!empty($start_date) && !empty($end_date)) {
            $settlement->whereDate('settlements.transaction_date', '>=', $start_date);
            $settlement->whereDate('settlements.transaction_date', '<=', $end_date);
        }
        
        if(!empty(request()->location_id)){
            $settlement->where('settlements.location_id',request()->location_id);
        }
        
        return $settlement->get();
    }
    
     public function _getTotalPayments($business_id, $start_date, $end_date) {
        $banked = Settlement::join('settlement_cash_deposits','settlements.id','settlement_cash_deposits.settlement_no')->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->sum('settlement_cash_deposits.amount');
        $locker = Settlement::join('settlement_cash_payments','settlements.id','settlement_cash_payments.settlement_no')->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->sum('settlement_cash_payments.amount');
        $card = Settlement::join('settlement_card_payments','settlements.id','settlement_card_payments.settlement_no')->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->sum('settlement_card_payments.amount');
        $credit = Settlement::join('settlement_credit_sale_payments','settlements.id','settlement_credit_sale_payments.settlement_no')->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->sum('settlement_credit_sale_payments.amount');
        
        
        $meter_sales = MeterSale::join('settlements','settlements.id','meter_sales.settlement_no')->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->sum('meter_sales.sub_total');
        $other_sales = OtherSale::join('settlements','settlements.id','other_sales.settlement_no')->whereDate('settlements.transaction_date','>=',$start_date)->whereDate('settlements.transaction_date','<=',$end_date)->sum('other_sales.sub_total');
            
        
        return array('total_card_payments' => $card,'total_cash_payments' => $locker,'total_cash_deposits' => $banked,'total_credit_sale_payments' => $credit,'total_sales' => $meter_sales+$other_sales);    
    }
    
    public function store(Request $request)
    {
    }
    /**
     * Show the specified resource.
     * @return Response
     */
    
    public function show()
    {
        return view('petro::show');
    }
    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    
    public function edit($id)
    {
      

    }
    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    
    public function update(Request $request, $id)
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
     * Get tank product details
     * @return Response
     */
    
}
