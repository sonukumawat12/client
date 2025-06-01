<?php



namespace Modules\Petro\Http\Controllers;



use App\Business;

use App\BusinessLocation;

use App\Product;

use App\Transaction;

use App\Utils\ModuleUtil;

use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use Illuminate\Support\Facades\DB;

use Yajra\DataTables\Facades\DataTables;

use Modules\Petro\Entities\TankTransfer;

;



class TanksTransactionDetailController extends Controller

{

    /**

     * All Utils instance.

     *

     */

    protected $productUtil;

    protected $transactionUtil;

    protected $moduleUtil;

    /**

     * Constructor

     *

     * @param ProductUtils $product

     * @return void

     */

    public function __construct(ProductUtil $productUtil, TransactionUtil $transactionUtil, ModuleUtil $moduleUtil)

    {

        $this->productUtil = $productUtil;

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
        DB::enableQueryLog();
        set_time_limit(0);

        $business_id = request()->session()->get('user.business_id');

        if (request()->ajax()) {
          
            $purchase_query = $this->_getPurchaseQuery($business_id);

            $sell_query = $this->_getSellQuery($business_id);
            
            $transfer_in = $this->_getTransferInQuery($business_id);
            $transfer_out = $this->_getTransferOutQuery($business_id);
            
            $sell_query->unionAll($purchase_query)->unionAll($transfer_in)->unionAll($transfer_out);
            
            $business_id = session()->get('user.business_id');

            $business_details = Business::find($business_id);

            $tanks_transaction_details = Datatables::of($sell_query)

                ->removeColumn('id')

                ->editColumn('created_at', function ($row) {

                    return $this->productUtil->format_date($row->created_at,true);

                })

                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                ->addColumn('balance_qty', function ($row) use ($business_details) {
                    
                    $ob = $this->transactionUtil->getTankBalanceByDateInclude($row->fuel_tank_id,$row->created_at);
                    return $this->productUtil->num_f($ob, false, $business_details, true);

                })

                ->addColumn('opening_balance_qty', function ($row) use ($business_details) {
                    if ($row->type == 'opening_stock') {

                        return $this->productUtil->num_f($row->purchase_qty, false, $business_details, true);

                    } else {
                        $ob = $this->transactionUtil->getTankBalanceByDate($row->fuel_tank_id,$row->created_at);
                        return $this->productUtil->num_f($ob, false, $business_details, true);
                    }
                        

                })

                ->editColumn('purchase_qty', function ($row) use ($business_details) {

                    if ($row->type == 'opening_stock' || $row->type == 'sell' || $row->type == 'transfer_out' || ($row->purchase_qty == 0 && $row->type == 'stock_adjustment')) {

                        return '';

                    } else {

                        return $this->productUtil->num_f($row->purchase_qty, false, $business_details, true);

                    }

                })

                ->editColumn('sold_qty', function ($row) use ($business_details) {

                    if ($row->type == 'opening_stock' || $row->type == 'purchase' ||$row->type == 'transfer_in' || ($row->sold_qty == 0 && $row->type == 'stock_adjustment')) {

                        return '';

                    } else {

                        return $this->productUtil->num_f(abs($row->sold_qty), false, $business_details, true);

                    }

                })

                ->editColumn('ref_no', function ($row) {

                    /**

                     * @ModifiedBy Afes Oktavianus

                     * @DateBy 07-06-2021

                     * @Task 3341

                     */

                    if ($row->type == 'sell') {

                        return __('petro::lang.settlment');

                    }elseif ($row->type == 'transfer_in' || $row->type == 'transfer_out') {

                        return __('petro::lang.tank_transfer');
                        
                    } else if ($row->type == 'opening_stock') {

                        return __('petro::lang.opening_stock');

                    } else if ($row->type == 'purchase') {

                        return __('petro::lang.purchase_reference_no') . ': ' . $row->ref_no;

                    }else if($row->type == 'stock_adjustment'){
                        return "<span class='text-danger'>".__('petro::lang.dip_reset')."</span>";
                    }

                })

                ->addColumn('purchase_order_no', function ($row) {

                    /**

                     * @ModifiedBy Afes Oktavianus

                     * @DateBy 07-06-2021

                     * @Task 3341

                     */

                    if ($row->type == 'sell' || $row->type == 'stock_adjustment' || $row->type == 'transfer_in' || $row->type == "transfer_out") {
                        
                        if($row->type == 'stock_adjustment'){
                            return "<span class='text-danger'>".$row->invoice_no."</span>";
                        }else{
                            return $row->invoice_no;
                        }

                        

                    } else {

                        return $row->ref_no;

                    }

                });

            return $tanks_transaction_details->rawColumns(['transaction_date', 'balance_qty', 'ref_no','purchase_order_no'])

                ->make(true);

        }

        return view('petro::tanks_transaction_details.index');

    }
    
    public function _getSellQuery($business_id){
        $query = Transaction::leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')
                ->join('fuel_tanks', function ($join) {
                    $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');

                })

                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

                ->where('transactions.business_id', $business_id)

                ->select(

                    'transactions.ref_no',

                    'transactions.invoice_no',

                    'transactions.transaction_date',

                    'transactions.created_at',

                    'transactions.type',

                    DB::raw('0 as purchase_qty'),

                    DB::raw('SUM(tank_sell_lines.quantity) as sold_qty'),

                    'fuel_tanks.id as fuel_tank_id',

                    'business_locations.name as location_name',

                    'products.name as product_name',

                    'fuel_tanks.fuel_tank_number'

                )

                ->groupBy(['transactions.id', 'products.id', 'fuel_tanks.id'])

                ->orderby('transactions.id');
                
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('transactions.transaction_date', '>=', request()->start_date);
                    $query->whereDate('transactions.transaction_date', '<=', request()->end_date);
                }
    
                if (!empty(request()->location_id)) {
                    $query->where('transactions.location_id', request()->location_id);
                }
    
                if (!empty(request()->fuel_tank_number)) {
                    $query->where('fuel_tanks.fuel_tank_number', request()->fuel_tank_number);
                }
    
                if (!empty(request()->product_id)) {
                    $query->where('fuel_tanks.product_id', request()->product_id);
                }
    
                if (!empty(request()->settlement_id)) {
                    $query->where('transactions.invoice_no', request()->settlement_id);
                }
    
                if (!empty(request()->purchase_no)) {
                    $query->where('transactions.ref_no', request()->purchase_no);
                }

                
                return $query;
    }
    
    public function _getPurchaseQuery($business_id){
        $query = Transaction::leftjoin('tank_purchase_lines', function ($join) {
                    $join->on('transactions.id', 'tank_purchase_lines.transaction_id')->where('tank_purchase_lines.quantity', '!=', 0);
    
                })

                ->join('fuel_tanks', function ($join) {

                    $join->on('tank_purchase_lines.tank_id', 'fuel_tanks.id');

                })

                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')

                ->where('transactions.business_id', $business_id)

                ->select(

                    'transactions.ref_no',

                    'transactions.invoice_no',

                    'transactions.transaction_date',

                    'transactions.created_at',

                    'transactions.type',

                    DB::raw('SUM(tank_purchase_lines.quantity) as purchase_qty'),

                    DB::raw('0 as sold_qty'),

                    'fuel_tanks.id as fuel_tank_id',

                    'business_locations.name as location_name',

                    'products.name as product_name',

                    'fuel_tanks.fuel_tank_number'

                )

                ->groupBy(['transactions.id', 'products.id', 'fuel_tanks.id']);
                
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('transactions.transaction_date', '>=', request()->start_date);
                    $query->whereDate('transactions.transaction_date', '<=', request()->end_date);
                }
    
                if (!empty(request()->location_id)) {
                    $query->where('transactions.location_id', request()->location_id);
                }
    
                if (!empty(request()->fuel_tank_number)) {
                    $query->where('fuel_tanks.fuel_tank_number', request()->fuel_tank_number);
                }
    
                if (!empty(request()->product_id)) {
                    $query->where('fuel_tanks.product_id', request()->product_id);
                }
    
                if (!empty(request()->settlement_id)) {
                    $query->where('transactions.invoice_no', request()->settlement_id);
                }
    
                if (!empty(request()->purchase_no)) {
                    $query->where('transactions.ref_no', request()->purchase_no);
                }

                
                return $query;
    }
    
    public function _getTransferInQuery($business_id){
        $query = TankTransfer::join('fuel_tanks', function ($join) {
                    $join->on('tank_transfers.to_tank', 'fuel_tanks.id');
                })
                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                ->leftjoin('business_locations', 'fuel_tanks.location_id', 'business_locations.id')

                ->where('tank_transfers.business_id', $business_id)

                ->select(

                    DB::raw('"" as ref_no'),

                    'tank_transfers.transfer_no as invoice_no',

                    'tank_transfers.date as transaction_date',

                    'tank_transfers.created_at',

                    DB::raw('"transfer_in" as type'),

                    'tank_transfers.quantity as purchase_qty',

                    DB::raw('0 as sold_qty'),

                    'fuel_tanks.id as fuel_tank_id',

                    'business_locations.name as location_name',

                    'products.name as product_name',

                    'fuel_tanks.fuel_tank_number'

                )

                ->groupBy(['tank_transfers.id', 'products.id', 'fuel_tanks.id']);
                
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('tank_transfers.date', '>=', request()->start_date);
                    $query->whereDate('tank_transfers.date', '<=', request()->end_date);
                }
    
                if (!empty(request()->location_id)) {
                    $query->where('fuel_tanks.location_id', request()->location_id);
                }
    
                if (!empty(request()->fuel_tank_number)) {
                    $query->where('fuel_tanks.fuel_tank_number', request()->fuel_tank_number);
                }
    
                if (!empty(request()->product_id)) {
                    $query->where('fuel_tanks.product_id', request()->product_id);
                }
    
                
                
                return $query;
    }
    
    public function _getTransferOutQuery($business_id){
        $query = TankTransfer::join('fuel_tanks', function ($join) {
                    $join->on('tank_transfers.from_tank', 'fuel_tanks.id');
                })
                ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                ->leftjoin('business_locations', 'fuel_tanks.location_id', 'business_locations.id')

                ->where('tank_transfers.business_id', $business_id)

                ->select(

                    DB::raw('"" as ref_no'),

                    'tank_transfers.transfer_no as invoice_no',

                    'tank_transfers.date as transaction_date',

                    'tank_transfers.created_at',

                    DB::raw('"transfer_out" as type'),

                    DB::raw('0 as purchase_qty'),
                    
                    'tank_transfers.quantity as sold_qty',

                    'fuel_tanks.id as fuel_tank_id',

                    'business_locations.name as location_name',

                    'products.name as product_name',

                    'fuel_tanks.fuel_tank_number'

                )

                ->groupBy(['tank_transfers.id', 'products.id', 'fuel_tanks.id']);
                
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('tank_transfers.date', '>=', request()->start_date);
                    $query->whereDate('tank_transfers.date', '<=', request()->end_date);
                }
    
                if (!empty(request()->location_id)) {
                    $query->where('fuel_tanks.location_id', request()->location_id);
                }
    
                if (!empty(request()->fuel_tank_number)) {
                    $query->where('fuel_tanks.fuel_tank_number', request()->fuel_tank_number);
                }
    
                if (!empty(request()->product_id)) {
                    $query->where('fuel_tanks.product_id', request()->product_id);
                }
    
                
                
                return $query;
    }
    

    /**

     * Show tank transaction summary

     *

     * @return \Illuminate\Http\Response

     */
    
    public function generateDateArray($startDate, $endDate)
    {
        $dates = [];
    
        $start = \Carbon::parse($startDate);
        $end = \Carbon::parse($endDate);
        
        // Loop through each day and add it to the array
        for ($date = $start; $date->lte($end); $date->addDay()) {
            $dates[] = $date->toDateString();
        }
    
        return $dates;
    }

    public function tankTransactionSummary()

    {

        set_time_limit(0);

        if (request()->ajax()) {

            $this->settleTransactionSummary();

            $business_id = request()->session()->get('user.business_id');

            $business_details = Business::find($business_id);

            if (request()->ajax()) {

                $start_date = request()->start_date;

                $end_date = request()->end_date;
                
                $dates = $this->generateDateArray(request()->start_date, request()->end_date);
                

                $query = DB::table('fuel_tanks')
                    ->select('fuel_tanks.*',
                        'products.name as product_name','products.id as product_id')
                    ->leftJoin('products', 'fuel_tanks.product_id', '=', 'products.id')
                    ->leftjoin('business_locations','business_locations.id','fuel_tanks.location_id')
                    ->where('fuel_tanks.business_id', $business_id)->select('fuel_tanks.*','business_locations.name as location_name','products.name as product_name');
                
                

                if (!empty(request()->location_id)) {

                    $query->where('fuel_tanks.location_id', request()->location_id);

                }

                if (!empty(request()->fuel_tank_number)) {
                    $query->where('fuel_tanks.fuel_tank_number', request()->fuel_tank_number);
                }

                if (!empty(request()->product_id)) {
                    $query->where('fuel_tanks.product_id', request()->product_id);

                }
                
                $tanks = [];
                
                foreach($dates as $date){
                    foreach($query->get() as $tank){
                        $date_obj = \Carbon::parse($date);
                        
                        if ($date_obj->endOfDay()->isAfter(now())) {
                            break;
                        }
                        
                        $tankTransactionDate = \Carbon::parse($tank->transaction_date);
                        
                        if ($tankTransactionDate->greaterThan($date_obj)) {
                            continue;
                        }
                        
                        $tank->start_date = $date_obj;
                        $tank->end_date = $date_obj->endOfDay();
                        
                        $tank->starting_qty = $this->transactionUtil->getTankBalanceByDate($tank->id,$tank->start_date);
                        $tank->sold_qty = $this->transactionUtil->__totalSellAndTransferOut($business_id,$tank->start_date,$tank->end_date,$tank->id);
                        $tank->purchase_qty = $this->transactionUtil->__totalPurchaseAndTransferIn($business_id,$tank->start_date,$tank->end_date,$tank->id);
                        $tank->balance_qty = $this->transactionUtil->getTankBalanceByDateInclude($tank->id,$tank->end_date);
                        
                        $tanks[] = $tank;
                        
                    }
                }
                
               
                
                $tanks_transaction_details = Datatables::of($tanks)

                    ->addColumn('transaction_date', function ($row) use ($business_id) {
                        
                        // if($row->transaction_date){
                        //     $this->productUtil->format_date($row->transaction_date);
                        // }
                        
                        // // if no transaction, then return date for opening stock
                        // $opening_Stock = Transaction::where('type','opening_stock')->where('business_id',$business_id)->where('opening_stock_product_id',$row->product_id)->first();

                        // if(!empty($opening_Stock) && $opening_Stock->transaction_date){
                        //     return $this->productUtil->format_date($opening_Stock->transaction_date);
                        // }
                        
                        // return "";
                        return $this->productUtil->format_date($row->end_date);

                    })

                    ->editColumn('created_at', function ($row) {
                        
                        // return $this->productUtil->format_date($row->created_at,true);
                        return $this->productUtil->format_date($row->end_date);
                        

                    })

                    ->editColumn('starting_qty', function ($row) use($business_details){
                        return $this->productUtil->num_f($row->starting_qty, false, $business_details, true);

                    })

                    ->editColumn('sold_qty', function($row) use($business_details){
                        return $this->productUtil->num_f($row->sold_qty, false, $business_details, true);  
                    })

                    ->editColumn('purchase_qty', function($row) use($business_details){
                        return $this->productUtil->num_f($row->purchase_qty, false, $business_details, true);
                    })

                    ->editColumn('balance_qty', function ($row) use($business_details) {

                        return $this->productUtil->num_f($row->balance_qty, false, $business_details, true);

                    });
                    
                   
                return $tanks_transaction_details->rawColumns(['balance_qty', 'opening_stock', 'total_stock', 'sold_qty', 'purchase_qty'])

                    ->make(true);

            }

        }

        return view('petro::tanks_transaction_details.tank_transactions_summary');

    }

    public function settleTransactionSummary()

    {

        $business_id = request()->session()->get('user.business_id');

        $business_details = Business::find($business_id);

        $business_location_id = BusinessLocation::where('business_id', $business_id)->get()->first()->id;

        $fuel_tanks = DB::select("SELECT * FROM `fuel_tanks` WHERE  business_id = $business_id");

        $start_date = date('Y-m-d', strtotime($business_details->start_date));

        $end_date = date('2020-12-31');

        while (strtotime($start_date) <= strtotime($end_date)) {

            foreach ($fuel_tanks as $fuel_tank_id) {

                $query = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                    ->leftjoin('tank_sell_lines', 'transactions.id', 'tank_sell_lines.transaction_id')

                    ->join('fuel_tanks', function ($join) {

                        $join->on('tank_sell_lines.tank_id', 'fuel_tanks.id');

                    })

                    ->leftjoin('products', 'fuel_tanks.product_id', 'products.id')

                    ->where('fuel_tanks.business_id', $business_id)

                    ->Where('fuel_tanks.id', $fuel_tank_id->id)

                    ->whereDate('transactions.transaction_date', '>=', $start_date)

                    ->whereDate('transactions.transaction_date', '<=', $start_date)

                    ->where('transactions.type', '!=', 'opening_stock')

                    ->select(

                        'fuel_tanks.fuel_tank_number',

                        'fuel_tanks.id as fuel_tank_id',

                        'transactions.transaction_date',

                        'transactions.created_at',

                        DB::raw('SUM(tank_sell_lines.quantity) as sell_qty'),

                        DB::raw("fuel_tanks.current_balance as total_stock"),

                        'products.name as product_name'

                    )->orderBy('transactions.transaction_date')

                    ->groupBy('fuel_tanks.id', 'transactions.transaction_date');

                if (!($query->count() > 0)) {

                    $transaction = new Transaction();

                    $transaction->business_id = $business_id;

                    $transaction->location_id = $business_location_id;

                    $transaction->type = 'sell';

                    $transaction->status = 'final';

                    $transaction->transaction_date = $start_date;

                    $transaction->created_by = 2;

                    $transaction->save();

                    $transaction_id = $transaction->id;

                    DB::insert('insert into tank_sell_lines (business_id, transaction_id,tank_id,product_id,quantity) values (?, ?,?, ?,?)', [$business_id, $transaction_id, $fuel_tank_id->id, $fuel_tank_id->product_id, '0.00000']);

                    echo $start_date;

                }

            }

            $start_date = strtotime("1 day", strtotime($start_date));

            $start_date = date('Y-m-d', $start_date);

        }

    }

}

