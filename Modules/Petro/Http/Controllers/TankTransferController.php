<?php



namespace Modules\Petro\Http\Controllers;



use App\AccountTransaction;

use App\Business;

use App\BusinessLocation;

use Modules\Superadmin\Entities\Subscription;

use Illuminate\Http\Request;

use Illuminate\Routing\Controller;

use Modules\Petro\Entities\FuelTank;

use Modules\Petro\Entities\TankTransfer;

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



class TankTransferController extends Controller

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

                $query = TankTransfer::leftjoin('fuel_tanks as t_from','t_from.id','tank_transfers.from_tank')
                
                    ->leftjoin('fuel_tanks as t_to','t_to.id','tank_transfers.to_tank')
                    
                    ->leftjoin('business_locations','business_locations.id','t_from.location_id')
                
                    ->leftjoin('products', 't_from.product_id', 'products.id')
                    
                    ->leftjoin('users', 'tank_transfers.created_by', 'users.id')

                    ->where('tank_transfers.business_id', $business_id)

                    ->select([

                        'tank_transfers.*',
                        
                        'business_locations.name as location_name',

                        'users.username as user_created',

                        'products.name as product_name',

                        't_from.fuel_tank_number as t_from_name',
                        
                        't_to.fuel_tank_number as t_to_name',

                    ]);

                if (!empty(request()->product_id)) {
                    $query->where('products.id', request()->product_id);
                }
                
                if (!empty(request()->location_id)) {
                    $query->where('t_from.location_id', request()->location_id);
                }
                
                if (!empty(request()->from_tank)) {
                    $query->where('tank_transfers.from_tank', request()->from_tank);
                }
                if (!empty(request()->to_tank)) {
                    $query->where('tank_transfers.to_tank', request()->to_tank);
                }
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('tank_transfers.date', '>=', request()->start_date);
                    $query->whereDate('tank_transfers.date', '<=', request()->end_date);
                }
                
                $business_details = Business::find($business_id);
                    

                $fuel_tanks = Datatables::of($query)

                    ->addColumn('quantity', function ($row)  {
                        
                        return $this->productUtil->num_f($row->quantity);
                        // return $row;

                    })
                    
                    ->addColumn('from_qty', function ($row)use($business_details)  {
                       $ob = $this->transactionUtil->getTankBalanceByDate($row->from_tank,$row->created_at);
                        return $this->productUtil->num_f($ob, false, $business_details, true);
                    })
                    
                    ->addColumn('to_qty', function ($row)use($business_details)  {
                       $ob = $this->transactionUtil->getTankBalanceByDate($row->to_tank,$row->created_at);
                       return $this->productUtil->num_f($ob, false, $business_details, true);
                    })

                    ->editColumn('date', function($row){
                        return $this->transactionUtil->format_date($row->date);
                        
                    })

                    ->removeColumn('id');



                return $fuel_tanks->rawColumns(['action'])

                    ->make(true);

            }


        $tank_numbers = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        
        $business_locations = BusinessLocation::forDropdown($business_id);
       

        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')->where('products.business_id', $business_id)->where('categories.name', 'Fuel')->pluck('products.name', 'products.id');

        
        return view('petro::tank_transfers.index')->with(compact(

            'tank_numbers',

            'products',
            'business_locations'

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

        $tank_numbers = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        
        $tank_bals = [];
        
        foreach($tank_numbers as $key => $tank){
            $tank_bals[$key] = $this->transactionUtil->getTankBalanceById($key);
        }
        
         $latest_transfer = TankTransfer::where('business_id', $business_id)->get()->last();
        
        if(!empty($latest_transfer)){
            $transfer_no = (int) $latest_transfer->transfer_no ?? 0;
            $transfer_no = str_pad(($transfer_no + 1),4,"0",STR_PAD_LEFT);
        }else{
            $transfer_no = 0001;
        }

        return view('petro::tank_transfers.create')->with(compact('tank_numbers','transfer_no','tank_bals'));

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

            
            DB::beginTransaction();
            
            $data = $request->except('_token');
            $data['date'] = date('Y-m-d', strtotime($data['date']));
            $data['created_by'] = auth()->user()->id;
            $data['business_id'] = $business_id;
            
            // dd($data);
            
            $from_tank = FuelTank::findOrFail($data['from_tank']);
            $to_tank = FuelTank::findOrFail($data['to_tank']);
            
            $from_tank->current_balance -= $data['quantity'];
            $to_tank->current_balance += $data['quantity'];
            
            $from_tank->save();
            $to_tank->save();
            
            TankTransfer::create($data);
            
            
            DB::commit();

            $output = [

                'success' => true,

                'msg' => __('messages.success')

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

}

