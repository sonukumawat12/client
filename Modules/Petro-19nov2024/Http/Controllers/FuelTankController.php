<?php



namespace Modules\Petro\Http\Controllers;



use App\AccountTransaction;

use App\Business;

use App\BusinessLocation;

use Modules\Superadmin\Entities\Subscription;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use Modules\Petro\Entities\FuelTank;

use App\Product;

use App\ProductVariation;

use App\PurchaseLine;

use App\System;

use App\Store;

use App\Transaction;

use App\Utils\ModuleUtil;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Auth;

use Yajra\DataTables\Facades\DataTables;

use App\Utils\ProductUtil;

use App\Utils\TransactionUtil;

use App\Variation;

;

use Illuminate\Support\Facades\Log;

use Modules\Petro\Entities\Settlement;

use Modules\Petro\Entities\TankPurchaseLine;

use Modules\Petro\Entities\TankSellLine;

use Modules\Superadmin\Entities\HelpExplanation;

use Modules\Superadmin\Entities\TankDipChart;
use Modules\Petro\Entities\MeterSale;

use Maatwebsite\Excel\Facades\Excel;


class FuelTankController extends Controller

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
     
    public function getTankProduct(){
        
    }

    public function index()

    {

        $business_id = request()->session()->get('user.business_id');


        
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            
            abort(403, 'Unauthorized Access');
            
        }



        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            $canEdit = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_fuel_tanks_edit') || auth()->user()->can('superadmin')?true:false;
            $canDelete = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_fuel_tanks_delete')||auth()->user()->can('superadmin') ?true:false;
          
            if (request()->ajax()) {

                $query = FuelTank::leftjoin('products', 'fuel_tanks.product_id', 'products.id')
                    
                    ->leftjoin('variations', 'products.id', '=', 'variations.product_id')

                    ->leftjoin('variation_location_details as vld', 'variations.id', '=', 'vld.variation_id')

                    ->leftjoin('business_locations', 'fuel_tanks.location_id', 'business_locations.id')

                    ->where('fuel_tanks.business_id', $business_id)

                    ->select([

                        'fuel_tanks.*',

                        "vld.qty_available as stock",

                        'products.name as product_name',

                        'business_locations.name as location_name'

                    ])->groupBy('fuel_tanks.id');

                if(request()->fuel_tank_number){
                    $query->where('fuel_tanks.fuel_tank_number',request()->fuel_tank_number);
                }
                
                if(request()->location_id){
                    $query->where('fuel_tanks.location_id',request()->location_id);
                }
                  
                    

                $fuel_tanks = Datatables::of($query)

                    ->addColumn( 'action', function ($row) use($canEdit, $canDelete){
                        $reshtml = '';
                        if($canEdit || $canDelete || 1){
                            if($canEdit || 1){
                                $reshtml = '<button data-href="'.action('\Modules\Petro\Http\Controllers\FuelTankController@edit', [$row->id]).'" data-container=".fuel_tank_modal" class="btn btn-primary btn-xs btn-modal edit_reference_button"><i class="fa fa-pencil-square-o"></i>'. trans("messages.edit").'</button>';
                            }
                            if($canDelete){
                                $reshtml .= '<a href="'.action('\Modules\Petro\Http\Controllers\FuelTankController@destroy', [$row->id]).'" class="delete_tank_button btn btn-danger btn-xs"><i class="fa fa-trash"></i>'.trans("messages.delete").'</a>';
                            }
                        }
                        return $reshtml;      

                    })

                    ->editColumn('bulk_tank', '@if($bulk_tank == 1) Yes @else No @endif')

                    ->addColumn('new_balance', function ($row) use ($business_id) {
                        
                        $current_balance = $this->transactionUtil->getTankBalanceById($row->id);


                        $business_details = Business::find($business_id);

                        return $this->productUtil->num_f($current_balance, false, $business_details, true);
                        // return $row;

                    })

                    ->editColumn('transaction_date', function($row){
                        // $latest_sale = MeterSale::leftjoin('pumps','pumps.id','pump_id')->leftjoin('fuel_tanks','fuel_tanks.id','pumps.fuel_tank_id')->where('fuel_tanks.id',$row->id)->select('meter_sales.created_at')->get()->last();
                        $transaction_date = /*!empty($latest_sale) ? $latest_sale->created_at :*/ $row->transaction_date;
                        
                        return $this->transactionUtil->format_date($transaction_date);
                        
                    })

                    ->removeColumn('id');





                return $fuel_tanks->rawColumns(['action', 'new_balance'])

                    ->make(true);

            }

        }



        $business_locations = BusinessLocation::forDropdown($business_id);

        $tank_numbers = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'fuel_tank_number');

        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')->where('products.business_id', $business_id)->where('categories.name', 'Fuel')->pluck('products.name', 'products.id');

        $settlements = Settlement::where('business_id', $business_id)->pluck('settlement_no', 'settlement_no');

        $purhcase_nos = Transaction::where('business_id', $business_id)->where('type', 'purchase')->pluck('ref_no', 'ref_no');



        $message = $this->transactionUtil->getGeneralMessage('general_message_tank_management_checkbox');



        return view('petro::fuel_tanks.index')->with(compact(

            'business_locations',

            'message',

            'tank_numbers',

            'products',

            'settlements',

            'purhcase_nos'

        ));

    }



    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()

    {

        $business_id = request()->session()->get('business.id');

        $locations = BusinessLocation::forDropdown($business_id);

        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')

            ->where('products.business_id', $business_id)

            ->where('categories.name', 'Fuel')

            ->pluck('products.name', 'products.id');

        $help_explanations = HelpExplanation::pluck('value', 'help_key');

        $sheet_names = TankDipChart::pluck('sheet_name', 'id');

        $tank_dip_chart_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'tank_dip_chart');
        
        $subscriptions = Subscription::active_subscription($business_id);
        $max_tanks = $subscriptions ? $subscriptions->package_details['allowed_tanks'] :0;
        $tanks_added = DB::table('fuel_tanks')->where('business_id', $business_id)->count();



        return view('petro::fuel_tanks.create')->with(compact('max_tanks','tanks_added','locations', 'products', 'help_explanations', 'tank_dip_chart_permission', 'sheet_names'));

    }



    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */
     
    public function import()
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('petro::fuel_tanks.import')->with(compact('business_locations'));
    }
    /**
     * Import Operators saves
     * @return Response
     */
    public function saveImport(Request $request)
    {
        $notAllowed = $this->productUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }
        $business_id = request()->session()->get('business.id');
        $location_id =   $request->location_id;
        $type =   $request->commission_type;

        try {
            //Set maximum php execution time
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            if ($request->hasFile('pumps_csv')) {
                $file = $request->file('pumps_csv');

                $parsed_array = Excel::toArray([], $file);

                //Remove header row
                $imported_data = array_splice($parsed_array[0], 1);

                $formated_data = [];

                $is_valid = true;
                $error_msg = '';

                $total_rows = count($imported_data);

                $row_no = 0;
                DB::beginTransaction();
                foreach ($imported_data as $key => $value) {
                    $row_no++;

                    //Check if any column is missing
                    if (count($value) < 6) {
                        $is_valid =  false;
                        $error_msg = "Some of the columns are missing. Please, use latest CSV file template.";
                        
                    }

                    $tank_no = (trim($value[0]));
                    if (empty($tank_no)) {
                        $is_valid = false;
                        $error_msg = "Invalid value for Tank No in row no. $row_no";
                        
                    }
                    
                    // tank no exists
                    $fuel_tanks = FuelTank::where('business_id', $business_id)->where('fuel_tank_number',$tank_no)->count();
                    if($fuel_tanks > 0){
                        $is_valid = false;
                        $error_msg = "Similar Fuel Tank No already exists in row no. $row_no";
                    }
                    
                    $product_name = (trim($value[1]));
                    if (empty($product_name)) {
                        $is_valid = false;
                        $error_msg = "Invalid value for Product in row no. $row_no";
                        
                    }
                    
                    // product not found
                    $product = Product::where('business_id', $business_id)
        
                        ->where('name', $product_name)
        
                        ->with(['variations', 'product_tax'])
        
                        ->first();
                        
                    if(empty($product)){
                        $is_valid = false;
                        $error_msg = "Product Name does not exist in DB in row no. $row_no";
                    }
                    
                    
                    $storage_volume = (trim($value[2])) ?? 0;
                    
                    $current_balance = (trim($value[3])) ?? 0;
                    
                    $transaction_date = ($value[4]);
                    if (empty($transaction_date)) {
                        $is_valid = false;
                        $error_msg = "Invalid value for Transaction Date row no. $row_no";
                       
                    }
                    
                   
                    $bulk_tank = strtolower(trim($value[5]));
                    if (empty($bulk_tank)) {
                        $is_valid = false;
                        $error_msg = "Invalid value for Bulk Tank in row no. $row_no";
                        
                    }
                    


                    if (!$is_valid) {
                        throw new \Exception($error_msg);
                        break;
                    }
                    
                    $variation = Variation::where('product_id', $product->id)->first();
                    $k = $variation->id;
                    
                    $qty_remaining = $this->productUtil->num_uf($current_balance);

                    $purchase_price_inc_tax = $variation->dpp_inc_tax;
                    $default_purchase_price = $variation->default_purchase_price;
                    $item_tax = ($purchase_price_inc_tax-$default_purchase_price) * $qty_remaining;

                    //Calculate transaction total
        
                    $purchase_total = ($purchase_price_inc_tax * $qty_remaining);
                    
                    
                    $exp_date = null;

                    $lot_number = null;
                    
                    $old_qty = 0;

                    $data = array(
        
                        'business_id' =>  $business_id,
        
                        'product_id' =>   $product->id,
        
                        'fuel_tank_number' =>   $tank_no,
        
                        'location_id' =>   $location_id,
        
                        'storage_volume' =>   $storage_volume,
        
                        'bulk_tank' =>   $bulk_tank == 'yes' ? 1 : 0,
        
                        'current_balance' =>   0, //this will update current qty in product stock updated below
        
                        'user_id' =>   Auth::user()->id,
        
                        'transaction_date' =>   date('Y-m-d', strtotime($transaction_date))
        
                    );
                    
                    $fuel_tank = FuelTank::create($data);

                //$k is variation id
    
                $this->productUtil->updateProductQuantity($location_id, $product->id, $k, $current_balance, $old_qty, null, false, $fuel_tank->id);
                $this->productUtil->updateProductQuantityStore($location_id, $product->id, $k, $current_balance, null, $old_qty, null, false);
            
    
                if ($qty_remaining != 0) {
                    $transaction = Transaction::create(
                        [
        
                            'type' => 'opening_stock',
        
                            'opening_stock_product_id' => $product->id,
        
                            'status' => 'received',
        
                            'business_id' => $business_id,
        
                            'transaction_date' => date('Y-m-d', strtotime($transaction_date)),
        
                            'total_before_tax' => $purchase_total,
        
                            'location_id' => $location_id,
        
                            'final_total' => $purchase_total,
        
                            'payment_status' => 'paid',
        
                            'created_by' => Auth::user()->id
        
                        ]
                    );
                    
                    $purchase_line = PurchaseLine::create(['product_id' => $product->id,
                        'variation_id' => $k,
                        'item_tax' => $item_tax,
                        'tax_id' => $product->tax,
                        'quantity' => $current_balance,
                        'pp_without_discount' => $default_purchase_price,
                        'purchase_price_inc_tax' => $purchase_price_inc_tax,
                        'exp_date' => $exp_date,
                        'purchase_price' => $default_purchase_price,
                        'lot_number' => $lot_number,
                        'transaction_id' => $transaction->id
                    ]);
        
        
    
    
    
                    //create pruchase line for tank 
        
                    TankPurchaseLine::create([
        
                        'business_id' => $business_id,
        
                        'transaction_id' => $transaction->id,
        
                        'tank_id' => $fuel_tank->id,
        
                        'product_id' => $product->id,
        
                        'quantity' => $current_balance
        
                    ]);





                    if ($qty_remaining  > 0) {
        
                        $acc_tran_type = 'debit';
        
                    }
        
                    if ($qty_remaining  < 0) {
        
                        $acc_tran_type = 'credit';
        
                    }
        
                        if (!empty($product->enable_stock)) {
        
                            if (!empty($product->stock_type)) {
        
                                $account_id = $product->stock_type;
        
                                $account_transaction_data = [
        
                                    'amount' => $transaction->final_total,
        
                                    'account_id' => $account_id,
        
                                    'type' => $acc_tran_type,
        
                                    'operation_date' => $transaction->transaction_date,
        
                                    'created_by' => $transaction->created_by,
        
                                    'transaction_id' => $transaction->id,
        
                                    'transaction_payment_id' => null,
        
                                    'note' => null
        
                                ];
        
        
        
                                AccountTransaction::createAccountTransaction($account_transaction_data);
        
                            }
        
                        }
        
        
        
                        $opening_balance_equity_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
        
                        $this->transactionUtil->createAccountTransaction($transaction, 'credit', $opening_balance_equity_id, $transaction->final_total);
        
                    }
                    
                }
                DB::commit();
            }

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.import_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];

            return redirect()->back()->with('notification', $output);
        }

        return redirect('/petro/tank-management')->with('status', $output);
    }

    public function store(Request $request)

    {



        try {

            $business_id = request()->session()->get('business.id');

            $transaction_date = $request->transaction_date;

            $product_id = $request->product_id;

            $variation = Variation::where('product_id', $request->product_id)->first();

            $k = $variation->id;

            $product = Product::where('business_id', $business_id)

                ->where('id', $product_id)

                ->with(['variations', 'product_tax'])

                ->first();

            $qty_remaining = $this->productUtil->num_uf(trim($request->current_balance));

            $purchase_price_inc_tax = $variation->dpp_inc_tax;
            $default_purchase_price = $variation->default_purchase_price;
            $item_tax = ($purchase_price_inc_tax-$default_purchase_price) * $qty_remaining;

            //Calculate transaction total

            $purchase_total = ($purchase_price_inc_tax * $qty_remaining);

            $exp_date = null;

            $lot_number = null;

            $old_qty = 0;

            $data = array(

                'business_id' =>  $business_id,

                'product_id' =>   $request->product_id,

                'fuel_tank_number' =>   $request->fuel_tank_number,

                'location_id' =>   $request->location_id,

                'storage_volume' =>   $request->storage_volume,

                'bulk_tank' =>   $request->bulk_tank,

                'current_balance' =>   0, //this will update current qty in product stock updated below

                'user_id' =>   Auth::user()->id,

                'transaction_date' =>   date('Y-m-d', strtotime($transaction_date)),

                'tank_dip_chart_id' =>   $request->tank_dip_chart_id,

                'tank_manufacturer' =>   $request->tank_manufacturer,

                'tank_capacity' =>   $request->tank_capacity,

                'unit_name' =>   $request->unit_name

            );



            DB::beginTransaction();



            $fuel_tank = FuelTank::create($data);

            //$k is variation id

            $this->productUtil->updateProductQuantity($request->location_id, $request->product_id, $k, $request->current_balance, $old_qty, null, false, $fuel_tank->id);
            $this->productUtil->updateProductQuantityStore($request->location_id, $request->product_id, $k, $request->current_balance, null, $old_qty, null, false);
        

            if ($qty_remaining != 0) {
                $transaction = Transaction::create(
                    [
    
                        'type' => 'opening_stock',
    
                        'opening_stock_product_id' => $request->product_id,
    
                        'status' => 'received',
    
                        'business_id' => $business_id,
    
                        'transaction_date' => date('Y-m-d', strtotime($transaction_date)),
    
                        'total_before_tax' => $purchase_total,
    
                        'location_id' => $request->location_id,
    
                        'final_total' => $purchase_total,
    
                        'payment_status' => 'paid',
    
                        'created_by' => Auth::user()->id
    
                    ]
                );
                
                $purchase_line = PurchaseLine::create(['product_id' => $product->id,
                    'variation_id' => $k,
                    'item_tax' => $item_tax,
                    'tax_id' => $product->tax,
                    'quantity' => $request->current_balance,
                    'pp_without_discount' => $default_purchase_price,
                    'purchase_price_inc_tax' => $purchase_price_inc_tax,
                    'exp_date' => $exp_date,
                    'purchase_price' => $default_purchase_price,
                    'lot_number' => $lot_number,
                    'transaction_id' => $transaction->id
                ]);
    
    



                //create pruchase line for tank 
    
                TankPurchaseLine::create([
    
                    'business_id' => $business_id,
    
                    'transaction_id' => $transaction->id,
    
                    'tank_id' => $fuel_tank->id,
    
                    'product_id' => $request->product_id,
    
                    'quantity' => $request->current_balance
    
                ]);





            if ($qty_remaining  > 0) {

                $acc_tran_type = 'debit';

            }

            if ($qty_remaining  < 0) {

                $acc_tran_type = 'credit';

            }

                if (!empty($product->enable_stock)) {

                    if (!empty($product->stock_type)) {

                        $account_id = $product->stock_type;

                        $account_transaction_data = [

                            'amount' => $transaction->final_total,

                            'account_id' => $account_id,

                            'type' => $acc_tran_type,

                            'operation_date' => $transaction->transaction_date,

                            'created_by' => $transaction->created_by,

                            'transaction_id' => $transaction->id,

                            'transaction_payment_id' => null,

                            'note' => null

                        ];



                        AccountTransaction::createAccountTransaction($account_transaction_data);

                    }

                }



                $opening_balance_equity_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');

                $this->transactionUtil->createAccountTransaction($transaction, 'credit', $opening_balance_equity_id, $transaction->final_total);

            }



            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('petro::lang.fuel_tank_add_success')

            ];

        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);

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

        $locations = BusinessLocation::forDropdown($business_id);

        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')

            ->where('products.business_id', $business_id)

            ->where('categories.name', 'Fuel')

            ->pluck('products.name', 'products.id');

        $fuel_tank = FuelTank::findOrFail($id);
        
        $opening_stock = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                ->where('transactions.business_id', $business_id)

                ->where('transactions.type', 'opening_stock')

                ->where('tank_purchase_lines.product_id', $fuel_tank->product_id)
                
                ->where('tank_purchase_lines.tank_id', $fuel_tank->id)

                ->select('tank_purchase_lines.quantity')

                ->first()->quantity ?? 0;

        $sheet_names = TankDipChart::pluck('sheet_name', 'id');

        $tank_dip_chart_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'tank_dip_chart');



        return view('petro::fuel_tanks.edit')->with(compact('locations', 'products', 'fuel_tank', 'tank_dip_chart_permission', 'sheet_names','opening_stock'));

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
            
            $fuel_tank = FuelTank::findOrFail($id);

            $business_id = request()->session()->get('business.id');

            $transaction_date = $request->transaction_date;

            $product_id = $request->product_id;

            $variation = Variation::where('product_id', $request->product_id)->first();

            $k = $variation->id;

            $product = Product::where('business_id', $business_id)

                ->where('id', $product_id)

                ->with(['variations', 'product_tax'])

                ->first();

            $qty_remaining = $this->productUtil->num_uf(trim($request->current_balance));

            $purchase_price_inc_tax = $variation->dpp_inc_tax;
            $default_purchase_price = $variation->default_purchase_price;
            $item_tax = ($purchase_price_inc_tax-$default_purchase_price) /** $qty_remaining*/;

            //Calculate transaction total

            $purchase_total = ($purchase_price_inc_tax * $qty_remaining);

            $exp_date = null;

            $lot_number = null;

            $old_qty = 0;

            $data = array(

                'business_id' =>  $business_id,

                'product_id' =>   $request->product_id,

                'fuel_tank_number' =>   $request->fuel_tank_number,

                'location_id' =>   $request->location_id,

                'storage_volume' =>   $request->storage_volume,

                'tank_dip_chart_id' =>   $request->tank_dip_chart_id,  //sheet name

                'tank_manufacturer' =>   $request->tank_manufacturer,

                'tank_capacity' =>   $request->tank_capacity,

                'unit_name' =>   $request->unit_name,

                'bulk_tank' =>   $request->bulk_tank,

                'user_id' =>   Auth::user()->id,

                'transaction_date' =>   date('Y-m-d', strtotime($request->transaction_date))

            );



            DB::beginTransaction();

            FuelTank::where('id', $id)->update($data);
            
            if ($qty_remaining != 0) {
                $opening_stock = Transaction::leftjoin('tank_purchase_lines', 'transactions.id', 'tank_purchase_lines.transaction_id')

                    ->where('transactions.business_id', $business_id)
    
                    ->where('transactions.type', 'opening_stock')
    
                    ->where('tank_purchase_lines.product_id', $fuel_tank->product_id)
                    
                    ->where('tank_purchase_lines.tank_id', $fuel_tank->id)
    
                    ->select('transactions.*')
    
                    ->first();
                    
                if(!empty($opening_stock)){
                    $transaction = Transaction::findOrFail($opening_stock->id);
                    
                    Transaction::where('id',$opening_stock->id)->update(
                    
                    [
    
                        'type' => 'opening_stock',
    
                        'opening_stock_product_id' => $request->product_id,
    
                        'status' => 'received',
    
                        'business_id' => $business_id,
    
                        'transaction_date' => date('Y-m-d', strtotime($transaction_date)),
    
                        'total_before_tax' => $purchase_total,
    
                        'location_id' => $request->location_id,
    
                        'final_total' => $purchase_total,
    
                        'payment_status' => 'paid',
    
                        'created_by' => Auth::user()->id
    
                    ]
                );
                    
                }else{
                    $transaction = Transaction::create(
                        
                        [
        
                            'type' => 'opening_stock',
        
                            'opening_stock_product_id' => $request->product_id,
        
                            'status' => 'received',
        
                            'business_id' => $business_id,
        
                            'transaction_date' => date('Y-m-d', strtotime($transaction_date)),
        
                            'total_before_tax' => $purchase_total,
        
                            'location_id' => $request->location_id,
        
                            'final_total' => $purchase_total,
        
                            'payment_status' => 'paid',
        
                            'created_by' => Auth::user()->id
        
                        ]
                    );
                }
                
                
                
                $purchase_line = PurchaseLine::updateOrCreate(
                    [
                        'transaction_id' => $transaction->id,
                    ],
                    
                    ['product_id' => $product->id,
                    'variation_id' => $k,
                    'item_tax' => $item_tax,
                    'tax_id' => $product->tax,
                    'quantity' => $request->current_balance,
                    'pp_without_discount' => $default_purchase_price,
                    'purchase_price_inc_tax' => $purchase_price_inc_tax,
                    'exp_date' => $exp_date,
                    'purchase_price' => $default_purchase_price,
                    'lot_number' => $lot_number,
                    'transaction_id' => $transaction->id
                ]);


                //create pruchase line for tank 
    
                TankPurchaseLine::updateOrCreate(
                    [
                        'business_id' => $business_id,
    
                        'transaction_id' => $transaction->id,
        
                        'tank_id' => $fuel_tank->id,
        
                    ],
                    
                    [
    
                    'business_id' => $business_id,
    
                    'transaction_id' => $transaction->id,
    
                    'tank_id' => $fuel_tank->id,
    
                    'product_id' => $request->product_id,
    
                    'quantity' => $request->current_balance
    
                ]);





            if ($qty_remaining  > 0) {

                $acc_tran_type = 'debit';
                $eqt_type = 'credit';

            }

            if ($qty_remaining  < 0) {

                $acc_tran_type = 'credit';
                $eqt_type = 'debit';

            }
            
            AccountTransaction::where('transaction_id',$transaction->id)->forcedelete();

                if (!empty($product->enable_stock)) {

                    if (!empty($product->stock_type)) {

                        $account_id = $product->stock_type;

                        $account_transaction_data = [

                            'amount' => abs($transaction->final_total),

                            'account_id' => $account_id,

                            'type' => $acc_tran_type,

                            'operation_date' => $transaction->transaction_date,

                            'created_by' => $transaction->created_by,

                            'transaction_id' => $transaction->id,

                            'transaction_payment_id' => null,

                            'note' => null

                        ];



                        AccountTransaction::createAccountTransaction($account_transaction_data);

                    }

                }


                $obe_account = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
                $account_transaction_data['account_id'] = $obe_account;
                $account_transaction_data['type'] = $eqt_type;
                
                
                AccountTransaction::createAccountTransaction($account_transaction_data);
                

            }


            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('petro::lang.fuel_tank_update_success')

            ];

        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);

    }



    /**

     * Remove the specified resource from storage.

     *

     * @param  int  $id

     * @return \Illuminate\Http\Response

     */

    public function destroy($id)

    {

        $business_id = request()->session()->get('business.id');



        try {

            $tank_purchases = TankPurchaseLine::leftjoin('transactions', 'tank_purchase_lines.transaction_id', 'transactions.id')->where('transactions.type', '!=', 'opening_stock')->where('tank_purchase_lines.business_id', $business_id)->where('tank_id', $id)->count();

            $tank_lines = TankSellLine::where('business_id', $business_id)->where('tank_id', $id)->count();



            if ($tank_purchases == 0 && $tank_lines == 0) {

                $fuel_tank = FuelTank::where('id', $id)->first();

                $product_id = $fuel_tank->product_id;

                $variation = Variation::where('product_id', $product_id)->first();

                $product = Product::findOrFail($product_id);

                $location_id = $fuel_tank->location_id;

                $old_quantity = 0;

                $new_quantity = $fuel_tank->current_balance;



                $this->productUtil->decreaseProductQuantity($product_id, $variation->id, $location_id, $new_quantity, $old_quantity);
                
                $store_id = Store::where('business_id', $business_id)->first()->id;
                $type = ($new_quantity > $old_quantity) ? 'increase' : 'decrease';
				$this->productUtil->decreaseProductQuantityStore(
                    $product_id, $variation->id, $location_id, $new_quantity,
                    $store_id,
                    $type,
                    $old_quantity
                );



                $account_id = $product->stock_type;



                $account_transaction_data = [

                    'amount' => $variation->default_purchase_price * $new_quantity,

                    'account_id' => $product->stock_type,

                    'type' => 'credit',

                    'operation_date' => date('Y-m-d H:i:s'),

                    'created_by' => Auth::user()->id,



                ];



                AccountTransaction::createAccountTransaction($account_transaction_data);



                $fuel_tank->delete();

                $output = [

                    'success' => true,

                    'msg' => __('petro::lang.tank_delete_success')

                ];

            } else {

                $output = [

                    'success' => false,

                    'msg' => __('petro::lang.transactions_exist_for_tank')

                ];

            }

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

