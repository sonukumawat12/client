<?php



namespace Modules\Petro\Http\Controllers;



use App\Account;

use App\User;

use App\TaxRate;

use App\AccountTransaction; 

use App\AccountType;

use App\Business;

use App\BusinessLocation;

use App\Category;

use App\Contact;

use Milon\Barcode\DNS2D;

use App\ContactLedger;

use App\CustomerReference;

use App\ExpenseCategory;

use App\Product;

use App\Store;

use App\Transaction;

use App\TransactionPayment;

use App\TransactionSellLine;

use Illuminate\Http\Request;

use Illuminate\Http\Response;

use Illuminate\Routing\Controller;

use Modules\Petro\Entities\Pump;

use Modules\Petro\Entities\PumpOperator;

use App\Utils\Util;

use App\Utils\ProductUtil;

use App\Utils\ModuleUtil;

use App\Utils\TransactionUtil;

use App\Utils\BusinessUtil;

use App\Variation;

use Modules\HR\Entities\WorkShift;

;

use Illuminate\Support\Facades\DB;

use Illuminate\Support\Facades\Log;

use Modules\Petro\Entities\CustomerPayment;

use Modules\Petro\Entities\FuelTank;

use Modules\Petro\Entities\MeterSale;

use Modules\Petro\Entities\OtherIncome;

use Modules\Petro\Entities\OtherSale;

use Modules\Petro\Entities\Settlement;

use Modules\Petro\Entities\SettlementCardPayment;

use Modules\Petro\Entities\SettlementCashPayment;

use Modules\Petro\Entities\SettlementCashDeposit;

use Modules\Petro\Entities\SettlementChequePayment;

use Modules\Petro\Entities\SettlementCreditSalePayment;

use Modules\Petro\Entities\SettlementExcessPayment;

use Modules\Petro\Entities\SettlementExpensePayment;

use Modules\Petro\Entities\SettlementPayment;

use Modules\Petro\Entities\SettlementShortagePayment;

use Modules\Petro\Entities\TankPurchaseLine;

use Modules\Petro\Entities\TankSellLine;

use Modules\Petro\Entities\DailyCollection;

use Modules\Petro\Entities\PumpOperatorCommission;

use Modules\Superadmin\Entities\Subscription;

use Yajra\DataTables\DataTables;

use App\Utils\NotificationUtil;

use Modules\Petro\Entities\PumperDayEntry;



use Spatie\Activitylog\Models\Activity;



use Modules\Petro\Entities\SettlementLoanPayment;



use App\NotificationTemplate;

use App\Http\Controllers\ContactController;

use Modules\Petro\Entities\PumpOperatorAssignment;

use Modules\Petro\Entities\DayEnd;

use Modules\Petro\Entities\SettlementEditHistory;





class SettlementController extends Controller

{



    /**

     * All Utils instance.

     *

     */

    protected $productUtil;

    protected $moduleUtil;

    protected $transactionUtil;

    protected $commonUtil;

    protected $notificationUtil;



    private $barcode_types;



    /**

     * Constructor

     *

     * @param ProductUtils $product

     * @return void

     */

    public function __construct(Util $commonUtil, ProductUtil $productUtil, ModuleUtil $moduleUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil,NotificationUtil $notificationUtil)

    {

        $this->commonUtil = $commonUtil;

        $this->productUtil = $productUtil;

        $this->moduleUtil = $moduleUtil;

        $this->transactionUtil = $transactionUtil;

        $this->businessUtil = $businessUtil;

        $this->notificationUtil = $notificationUtil;

    }

    

    public function getUserActivityReport(Request $request)



    {



        

        $business_id = request()->session()->get('user.business_id');



        if (request()->ajax()) {



            $with = [];



            $shipping_statuses = $this->transactionUtil->shipping_statuses();



            $business_users = User::where('business_id', $business_id)->pluck('id')->toArray();



            $activity = Activity::whereIn('causer_id', $business_users)->whereIn('subject_type',$this->transactionUtil->petro_classes);

            

            if (!empty(request()->user) && request()->user !='All') {



                $user = request()->user;



                $activity->where('causer_id', $user);

            }

            

            if(!empty(request()->type) && request()->type !='All') {

                $type = request()->type;

        

                $activity->where('description', $type);

            }

            if(!empty(request()->subject) && request()->subject !='All') {

               

                $subject = request()->subject;



                $activity->where('log_name', $subject);

            }

            

           if(!empty(request()->startDate) && !empty(request()->endDate) ) {



 

                $activity->whereDate('created_at', '>=', request()->startDate);



                $activity->whereDate('created_at', '<=',request()->endDate);

            }



            $datatable = Datatables::of($activity)



                ->editColumn('created_at', '{{ @format_datetime($created_at) }}')



                ->removeColumn('id')



                ->editColumn('causer_id', function ($row) {



                    $causer_id = $row->causer_id;



                    $username = User::where('id', $causer_id)->select('username')->first()->username;



                    return $username;

                })

                ->addColumn('ref_no',function($row){

                    $attributes = json_decode($row->properties,true);

                    $new = $attributes['attributes'] ?? [];

                    

                    $html = "";

                    if($row->subject_type == 'App\TransactionPayment'){

                        if(!empty($new['payment_ref_no'])){

                            $html .= $new['payment_ref_no'];

                        }

                    }else{

                        if(!empty($new['invoice_no'])){

                            $html .= $new['invoice_no'];

                        }else{

                            if($row->subject_type == 'Modules\Petro\Entities\Settlement'){

                                $html .= $new['settlement_no'];

                            }

                        }

                    }

                    return $html;

                })

                ->addColumn('description_details',function($row){

                    $attributes = json_decode($row->properties,true);

                    

                    $new = $attributes['attributes'] ?? [];

                    $old = $attributes['old'] ?? [];

                    $html = "";

                    

                    if($row->description == 'updated'){

                       

                        foreach ($new as $key => $newValue) {

                            if($key != 'created_at' && $key != 'updated_at' && $key != 'id'){

                                $oldValue = $old[$key] ?? null;

                            

                                if ($newValue !== $oldValue) {

                                    $originalKey = str_replace('_', ' ', ucfirst($key));

                                    $html .= "Original $originalKey $oldValue changed to $newValue <br>";

                                }

                            }

                                

                        }

                    }elseif($row->description == 'deleted'){

                        if($row->subject_type == 'App\TransactionPayment'){

                            $contact = Contact::find($new['payment_for']);

                            if(!empty($contact)){

                                $html .= "Contact Name: ".$contact->name."<br>";

                            }

                            

                            if(!empty($new['amount'])){

                                $html .= 'Amount: '.$this->productUtil->num_f($new['amount'])."<br>";

                            }

                            

                            if(!empty($new['payment_ref_no'])){

                                $html .= 'Ref No: '.$new['payment_ref_no']."<br>";

                            }

                            

                        }else{

                            return "";

                        }

                    }

                     elseif($row->description == 'update' && $row->log_name == 'Settlement'){

                        $jsonProperties = $row->properties;

                        

                        $decodedProperties = json_decode($jsonProperties);

                        

                        $text = $decodedProperties[0];

                        

                        $html .= $text;

                                    // $html .= $row->properties;

                            

                    }

                     elseif(($row->description == 'update' || $row->description == 'delete') && ($row->log_name == 'Day End Settlement' || $row->log_name == 'Dip Chart' || $row->log_name == 'Dip Report')){

                        $jsonProperties = $row->properties;

                        

                        $decodedProperties = json_decode($jsonProperties);

                        

                        $text = $decodedProperties[0];

                        

                        $html .= nl2br($text);

                                    // $html .= $row->properties;

                            

                    }

                    else{

                        $html = "";

                    }

                    

                    return nl2br($html); 

                });



            $rawColumns = ['description_details'];



            return $datatable->rawColumns($rawColumns)



                ->make(true);

        }



        $users = User::where('business_id', $business_id)->pluck('username', 'id');

        

        $type =Activity::distinct()->pluck('description');

        $subject =Activity::distinct()->pluck('log_name');



        return view('petro::report.user_activity')



            ->with(compact('users','type','subject'));

    }

    

    public function adjustDiscounts(){

        ini_set('max_execution_time', 0);

        

        $meter_sales = MeterSale::all();

        $other_sales = OtherSale::all();

        

        foreach($meter_sales as $sale){

            

            if(empty($sale->discount) || empty($sale->discount_type)){

                MeterSale::where('id',$sale->id)->update(['discount_amount' => $sale->sub_total]);

            }else{

                $discount = 0;

                if($sale->discount_type == 'fixed'){

                    $discount = $sale->discount;

                }

                

                if($sale->discount_type == 'percentage'){

                    $discount = $sale->discount * $sale->sub_total / 100;

                }

                

                MeterSale::where('id',$sale->id)->update(['discount_amount' => $sale->sub_total - $discount]);

            }

            

        }

        

        foreach($other_sales as $sale){

            

            if(empty($sale->discount) || empty($sale->discount_type)){

                OtherSale::where('id',$sale->id)->update(['discount_amount' => 0]);

            }else{

                $discount = 0;

                if($sale->discount_type == 'fixed'){

                    $discount = $sale->discount;

                }

                

                if($sale->discount_type == 'percentage'){

                    $discount = $sale->discount * $sale->sub_total / 100;

                }

                

                OtherSale::where('id',$sale->id)->update(['discount_amount' => $discount]);

            }

            

        }

        

        

    }

    

    public function adjustMeterSalesDates(){

        ini_set('max_execution_time', 0);

        

        $query = TankSellLine::leftjoin('transactions','transactions.id','tank_sell_lines.transaction_id')

                    ->leftjoin('settlements', 'settlements.settlement_no', 'transactions.invoice_no')

                    ->leftjoin('fuel_tanks', 'fuel_tanks.id', 'tank_sell_lines.tank_id')

                    ->leftjoin('products', 'products.id', 'tank_sell_lines.product_id')

                    ->whereDate('settlements.created_at', date('Y-m-d'))

                    ->select([

                        'products.name as product_name',

                        'fuel_tanks.fuel_tank_number',

                        'tank_sell_lines.*',

                        'settlements.transaction_date',

                        'settlements.settlement_no',

                    ])->orderBy('settlements.transaction_date','DESC')->get();

        foreach($query as $one){

            $transaction_date = $one->transaction_date;

            $created_at = $one->created_at;

            

            if(date('Y-m-d',strtotime($transaction_date)) != date('Y-m-d',strtotime($created_at))){

                $new_date = date('Y-m-d',strtotime($transaction_date))." ".date('H:i:s',strtotime($created_at));

                TankSellLine::where('id',$one->id)->update(['created_at'=>$new_date]);

            }

        }

    }

    

    public function meter_sales(){

         $business_id = request()->session()->get('user.business_id');

            if (request()->ajax()) {

                $query = TankSellLine::leftjoin('transactions','transactions.id','tank_sell_lines.transaction_id')

                    ->leftjoin('settlements', 'settlements.settlement_no', 'transactions.invoice_no')

                    ->leftjoin('fuel_tanks', 'fuel_tanks.id', 'tank_sell_lines.tank_id')

                    ->leftjoin('products', 'products.id', 'tank_sell_lines.product_id')

                    ->where('settlements.business_id', $business_id)

                    ->select([

                        'products.name as product_name',

                        'fuel_tanks.fuel_tank_number',

                        'tank_sell_lines.*',

                        'settlements.transaction_date',

                        'settlements.settlement_no',

                    ])->orderBy('settlements.transaction_date','DESC');



                

                if (!empty(request()->settlement_no)) {

                    $query->where('settlements.settlement_no', request()->settlement_no);

                }

                

                if (!empty(request()->start_date) && !empty(request()->end_date)) {

                    $query->whereDate('settlements.transaction_date', '>=', request()->start_date);

                    $query->whereDate('settlements.transaction_date', '<=', request()->end_date);

                }

                

                

                $settlements = Datatables::of($query)

                    ->addColumn(

                        'action',

                        function ($row) {

                            $html = '<button data-href="'.action('\Modules\Petro\Http\Controllers\SettlementController@editMeterSale', [$row->id]).'" data-container=".fuel_tank_modal" class="btn btn-primary btn-xs btn-modal edit_reference_button"><i class="fa fa-pencil-square-o"></i>' .trans("messages.edit").'</button>';

                            

                            return $html;

                        }

                    )

                    ->editColumn('created_at', '{{@format_datetime($created_at)}}')

                    ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                    ->removeColumn('id');



                return $settlements->rawColumns(['action'])

                    ->make(true);

            }

    }

    

    public function editMeterSale($id){

        $meter_sale = TankSellLine::findOrFail($id);

        $transaction = Transaction::findOrFail($meter_sale->transaction_id);

        return view('petro::edit_settlement_date.edit')->with(compact(

            'meter_sale','transaction'

        ));

    }

    

    public function updateMeterSale($id, Request $request)

    {

        try {

            $business_id = $request->session()->get('business.id');



            $data = array(

                'created_at' => $request->created_at,

            );



            TankSellLine::where('id', $id)->update($data);

            

            $meter_sale = TankSellLine::findOrFail($id);

            $transaction = Transaction::findOrFail($meter_sale->transaction_id);

            $transaction->created_at = $request->created_at;

            $transaction->save();

            

            $output = [

                'success' => 1,

                'msg' => __('lang_v1.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);

    }





    /**

     * Display a listing of the resource.

     * @return Response

     */

    public function index()

    {

        $business_id = request()->session()->get('user.business_id');
        
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {

            abort(403, 'Unauthorized Access');

        }



        if (request()->ajax()) {

            $business_id = request()->session()->get('user.business_id');

            if (request()->ajax()) {

                $query = Settlement::leftjoin('business_locations', 'settlements.location_id', 'business_locations.id')

                    ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')
                    
                    ->leftjoin('pump_operator_assignments', 'settlements.pump_operator_id', 'pump_operator_assignments.pump_operator_id')

                    ->where('settlements.business_id', $business_id)

                    ->select([

                        'pump_operators.name as pump_operator_name',

                        'business_locations.name as location_name',

                        'settlements.*',
                        
                        'pump_operator_assignments.shift_number'

                    ])

                    ->with(['meter_sales']);



                if (!empty(request()->location_id)) {

                    $query->where('settlements.location_id', request()->location_id);

                }

                if (!empty(request()->pump_operator)) {

                    $query->where('settlements.pump_operator_id', request()->pump_operator);

                }

                if (!empty(request()->settlement_no)) {

                    $query->where('settlements.id', request()->settlement_no);

                }

                if (!empty(request()->start_date) && !empty(request()->end_date)) {

                    $query->whereDate('settlements.transaction_date', '>=', request()->start_date);

                    $query->whereDate('settlements.transaction_date', '<=', request()->end_date);

                }
                
                $query->groupBy('settlements.id');

                $query->orderBy('settlements.id', 'desc');

                $first = null;

                $first = Settlement::where('business_id', $business_id)->where('status', 0)->orderBy('id', 'desc')->first();



                $delete_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'delete_settlement');

                $edit_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'edit_settlement');

                $edit_settlement_no_change = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'edit_settlement_no_change');



                $settlements = Datatables::of($query)

                    ->addColumn(

                        'action',

                        function ($row) use ($first,$delete_settlement,$edit_settlement,$edit_settlement_no_change) {

                            $html = '';

                            if ($row->status == 1) {

                                $html .= '<a class="btn  btn-danger btn-sm" href="' . action("\Modules\Petro\Http\Controllers\SettlementController@create") . '">' . __("petro::lang.finish_settlement") . '</a>';

                            }else if($row->is_edit == 1){

                               $html .= '<a class="btn  btn-warning btn-sm" href="' . action("\Modules\Petro\Http\Controllers\SettlementController@edit", [$row->id]) . '">' . __("petro::lang.finish_editting") . '</a>'; 

                            } else {

                                $html .=  '<div class="btn-group">

                                <button type="button" class="btn btn-info dropdown-toggle btn-xs"

                                    data-toggle="dropdown" aria-expanded="false">' .

                                    __("messages.actions") .

                                    '<span class="caret"></span><span class="sr-only">Toggle Dropdown

                                    </span>

                                </button>

                                <ul class="dropdown-menu dropdown-menu-left" role="menu">';



                                $html .= '<li><a data-href="' . action("\Modules\Petro\Http\Controllers\SettlementController@show", [$row->id]) . '" class="btn-modal" data-container=".settlement_modal"><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</a></li>';

                                if (auth()->user()->can("settlement.edit") && $edit_settlement) {

                                    $html .= '<li><a href="' . action("\Modules\Petro\Http\Controllers\SettlementController@edit", [$row->id]) . '" class="edit_settlement_button"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';

                                }

                                

                                if (auth()->user()->can("settlement.edit") && $edit_settlement_no_change) {

                                    $html .= '<li><a href="' . action("\Modules\Petro\Http\Controllers\SettlementController@edit", [$row->id]) . '?no_change=1" class="edit_settlement_button"><i class="fa fa-pencil-square-o"></i> ' . __("petro::lang.edit_no_change") . '</a></li>';

                                }

                                

                                if($this->moduleUtil->hasThePermissionInSubscription(request()->session()->get('user.business_id'), 'individual_sale')){

                                    $settlement = DB::table('transactions')->where('invoice_no',$row->settlement_no)->where('type','sell')->first();

                                    

                                    if(!empty($settlement)){

                                        if(strtotime($this->transactionUtil->__getVatEffectiveDate(request()->session()->get('user.business_id'))) <= strtotime($row->transaction_date)){

                                            $html .= '<li><a href="#" data-href="' . action('\Modules\Vat\Http\Controllers\VatController@updateSingleVats', ['transaction_id' => $settlement->id]) . '" class="regenerate-vat"><i class="fa fa-pencil"></i> ' . __("superadmin::lang.regenerate_vat") . '</a></li>';

                                        }

                                    }

                                    

                                }

                                

                                

                                if (!empty($first) && $first->id == $row->id && $delete_settlement && auth()->user()->can("settlement.delete")) {

                                   // commented By M Usman for hiding Delete Action

                                    $html .= '<li><a href="' . action("\Modules\Petro\Http\Controllers\SettlementController@destroy", [$row->id]) . '" class="delete_settlement_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';

                                }

                                $html .= '<li><a data-href="' . action("\Modules\Petro\Http\Controllers\SettlementController@print", [$row->id]) . '" class="print_settlement_button"><i class="fa fa-print"></i> ' . __("petro::lang.print") . '</a></li>';



                                $html .= '</ul></div>';

                            }

                            return $html;

                        }

                    )

                    ->editColumn('status', function ($row) {

                        if ($row->status == 0) {

                            return '<span class="label label-success">Completed</span>';

                        } else {

                            return '<span class="label label-danger">Pending</span>';

                        }

                    })

                    // ->addColumn('pump_nos', function($row){

                    //     $pump_nos = '';

                    //     if(!empty($row->meter_sales())){

                    //         $_pump_nos = $row->meter_sales->pluck('pump_id')->toArray() ?? [];

                    //         $_pumps = Pump::whereIn('id',$_pump_nos)->pluck('pump_no')->toArray();

                    //         $pump_nos = implode(', ',$_pumps);

                    //     }

                    //     return $pump_nos;

                    // })

                    ->addColumn('pump_nos', function($row) {
                        $pump_nos = '';
                        if (!empty($row->meter_sales) && $row->meter_sales->count() > 0) {
                            $_pump_nos = $row->meter_sales->pluck('pump_id')->toArray();
                            $_pumps = Pump::whereIn('id', $_pump_nos)->pluck('pump_no')->toArray();
                            $pump_nos = implode(', ', $_pumps);
                        }
                        return $pump_nos;
                    })

                  

                    // ->editColumn('shift', function ($row) {

                    //     if (!empty($row->work_shift)) {

                    //         $shifts = WorkShift::whereIn('id', $row->work_shift)->pluck('shift_name')->toArray();

                    //         return implode(',', $shifts);

                    //     } else {

                    //         return '';

                    //     }

                    // })

                    ->editColumn('shift', function ($row) {
                        if (!empty($row->work_shift) && is_array($row->work_shift)) {
                            $shifts = WorkShift::whereIn('id', $row->work_shift)->pluck('shift_name')->toArray();
                            return implode(',', $shifts);
                        }
                        return '';
                    })

                    ->addColumn('created_by', function ($row) {

                        $transaction = Transaction::where('invoice_no',$row->settlement_no)->leftJoin('users','users.id','transactions.created_by')->select('users.username')->first();

                        if(!empty($transaction)){

                            return $transaction->username;

                        }

                    })

                    ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')

                    ->editColumn('total_amount', '{{@num_format($total_amount)}}')

                    ->setRowAttr([

                        'data-href' => function ($row) {

                            return  action('\Modules\Petro\Http\Controllers\SettlementController@show', [$row->id]);

                        }

                    ])



                    ->removeColumn('id');



                return $settlements->rawColumns(['action', 'status', 'total_amount'])

                    ->make(true);

            }

        }

        $business_locations = BusinessLocation::forDropdown($business_id);

        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        $settlement_nos = Settlement::where('business_id', $business_id)->pluck('settlement_no', 'id');



        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');



        return view('petro::settlement.index')->with(compact(

            'business_locations',

            'pump_operators',

            'settlement_nos',

            'message'

        ));

    }

    

    function extractLastInteger($text) {

        if (preg_match('/\d+$/', $text, $matches)) {

            return intval($matches[0]);

        } else {

            return 0;

        }



    }

    

    public function updateCreditSales(){

        try {

            DB::beginTransaction();

                $trans = SettlementCreditSalePayment::join('transactions','transactions.id','settlement_credit_sale_payments.transaction_id')->where('transactions.final_total',0)->get();

                

                foreach($trans as $one){

                    $final_total = $one->amount- $one->total_discount;

                    Transaction::where('id',$one->transaction_id)->update(['final_total' => $final_total,'total_before_tax' => $final_total]);

                }

                

                $output = "Successfull";

            DB::commit();

        } catch (\Exception $e) {

            DB::rollback();

            logger($e);

            $output = "Failed";

        }

        

        dd($output);

    }



    /**

     * Show the form for creating a new resource.

     * @return Response

     */

    public function create()

    {

        $reviewed = $this->transactionUtil->get_review(date('Y-m-d'),date('Y-m-d'));

        

            if(!empty($reviewed)){

                $output              = [

                    'success' => 0,

                    'msg'     =>"You can't add a settlement for an already reviewed date",

                ];

                

                return redirect()->back()->with(['status' => $output]);

            }

            

        $business_id = request()->session()->get('business.id');

        $business = Business::where('id', $business_id)->first();

        $pos_settings = json_decode($business->pos_settings,true);

        $check_qty = !empty($pos_settings['allow_overselling']) ? false : true;

        $cash_denoms = !empty($pos_settings['cash_denominations']) ? explode(',',$pos_settings['cash_denominations']) : array();

        

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_location = current(array_keys($business_locations->toArray()));



        $payment_types = $this->productUtil->payment_types($default_location,false, false, false, false,"is_sale_enabled");

        $customers = Contact::customersDropdown($business_id, false);

        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');



        $items = [];



        $ref_no_prefixes = request()->session()->get('business.ref_no_prefixes');

        $ref_no_starting_number = request()->session()->get('business.ref_no_starting_number');

        $prefix =   !empty($ref_no_prefixes['settlement']) ? $ref_no_prefixes['settlement'] : '';

        $starting_no =  !empty($ref_no_starting_number['settlement']) ? (int) $ref_no_starting_number['settlement'] : 1;

        $count = Settlement::where('business_id', $business_id)->orderBy('id','DESC')->first();

        

        if(!empty($count)){

            $count = $this->extractLastInteger($count->settlement_no);

        }else{

            $count = 0;

        }

        

        $settlement_no = $prefix . (1 + $count);

        

        

    

        $active_settlement = Settlement::where('status', 1)

            ->where('business_id', $business_id)

            ->select('settlements.*')

            ->with(['meter_sales', 'other_sales', 'other_incomes', 'customer_payments'])->first();



            
        //  dd($active_settlement);
            

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_location = current(array_keys($business_locations->toArray()));

        if (!empty($active_settlement)) {

            $already_pumps = MeterSale::where('settlement_no', $active_settlement->id)->pluck('pump_id')->toArray();

            $pump_nos = Pump::where('business_id', $business_id)->whereNotIn('id', $already_pumps)->pluck('pump_name', 'id');

        } else {

            $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');

        }



        //other_sale tab

        $stores =  Store::forDropdown($business_id, 0,1, 'sell');

        

        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();

        $fuel_category_id = !empty($fuel_category_id) ? $fuel_category_id->id : null;

        $items = $this->transactionUtil->getProductDropDownArray($business_id, $fuel_category_id,'petro_settlements');

        // other income tab

        $services = Product::where('business_id', $business_id)->forModule('petro_settlements')->where('enable_stock', 0)->pluck('name', 'id');

        $subscription = Subscription::active_subscription($business_id);
        
        if($subscription->customer_credit_notification_type == [])
        {
            $show_shift_no = false;
        } else {
            $firstDecode = json_decode($subscription->customer_credit_notification_type, true);
            if (is_string($firstDecode)) {
                $decodedData = json_decode($firstDecode, true);
                $show_shift_no = in_array("pumper_dashboard", $decodedData) ? true : false;
            } else {
                $show_shift_no = false;
            }
        }



        $payment_meter_sale_total = !empty($active_settlement->meter_sales) ? $active_settlement->meter_sales->sum('discount_amount') :  0.00;

        $payment_other_sale_total = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('sub_total') :  0.00;

        

        $payment_other_sale_discount = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('discount_amount') :  0.00;

        

        $payment_other_sale_total -= $payment_other_sale_discount;

        

        $payment_other_income_total = !empty($active_settlement->other_incomes) ? $active_settlement->other_incomes->sum('sub_total') :  0.00;

        $payment_customer_payment_total = !empty($active_settlement->customer_payments) ? $active_settlement->customer_payments->sum('sub_total') :  0.00;



        $wrok_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');

        $bulk_tanks = FuelTank::where('business_id', $business_id)->where('bulk_tank', 1)->pluck('fuel_tank_number', 'id');





        $select_pump_operator_in_settlement = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'select_pump_operator_in_settlement');



        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');

        $discount_types = ['fixed' => 'Fixed', 'percentage' => 'Percentage'];


        // dd($active_settlement,123);
        return view('petro::settlement.create')->with(compact(

            'select_pump_operator_in_settlement',

            'message',

            'business_locations',

            'payment_types',

            'customers',

            'pump_operators',

            'wrok_shifts',

            'pump_nos',

            'items',

            'settlement_no',

            'default_location',

            'active_settlement',

            'stores',

            'payment_meter_sale_total',

            'payment_other_sale_total',

            'payment_other_income_total',

            'payment_customer_payment_total',

            'bulk_tanks',

            'services',

            'discount_types',

            'cash_denoms',

            'check_qty',

            'payment_other_sale_discount',

            'show_shift_no'

        ));

    }



    /**

     * Store a newly created resource in storage.

     * @param  Request $request

     * @return Response

     */

    public function store(Request $request, ContactController $contactController)

    {

        try {

            $denom_qty = $request->denom_qty;

            $denom_value = $request->denom_value;

            $denom_enabled = $request->denom_enabled;

            $denom_data = array();

            

            if($denom_enabled > 0){

                $i = 0;

                foreach($denom_qty as $one){

                   $denom_data[] = array("value" => $denom_value[$i], "qty" => $denom_qty[$i]);

                    $i++; 

                }

            }

            $settlement_no = $request->settlement_no;

            $no_change = $request->no_change;

            

            

            $business_id = $request->session()->get('business.id');

            $settlement = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();

            $edit = Settlement::where('settlements.id', $settlement->id)->where('settlements.business_id', $business_id)->where('status', 0)->first();


            //adding daily collection to cash payments
            $settlement_total = $settlement->meter_sales->sum('sub_total') + $settlement->other_sales->sum('sub_total') + $settlement->other_incomes->sum('sub_total') + $settlement->customer_payments->sum('sub_total');
            // //Get Daily Collection from businiss_id and pomp_operator id and settlement_id is null

            $daily_collections = DailyCollection::leftjoin('business_locations', 'daily_collections.location_id', 'business_locations.id')

            ->leftjoin('pump_operators', 'daily_collections.pump_operator_id', 'pump_operators.id')

            ->leftjoin('users', 'daily_collections.created_by', 'users.id')

            ->leftjoin('settlements', 'daily_collections.settlement_id', 'settlements.id')

            ->where('daily_collections.business_id', $business_id)

            ->where('daily_collections.pump_operator_id', $settlement->pump_operator_id)

            ->whereNull('settlement_id')

            ->select([

                'daily_collections.*',

                'business_locations.name as location_name',

                'pump_operators.name as pump_operator_name',

                'settlements.id as settlements_id',

                'users.username as user',

            ])->orderBy('daily_collections.id')->get();

            $outstanding_payment = $settlement_total;

            foreach ($daily_collections as $daily_collections) {
                if ($outstanding_payment >= 0) {
                    $customers = Contact::customersDropdown($business_id, false, true, 'customer');
                    $data = array(
                        'business_id' => $business_id,
                        'settlement_no' => $settlement->id,
                        'amount' => floatval($daily_collections->current_amount),
                        'customer_id' => array_key_first($customers->toArray())
                    );
                    SettlementCashPayment::create($data);
                }
            }

            

            if($settlement->is_edit == 0 && $settlement->status == 0 && empty($no_change)){

                return [

                    'success' => 0,

                    'msg' => __('petro::lang.no_change_performed')

                ];

            }

            

            DB::beginTransaction();

            if (!empty($edit)) {

                $this->deletePreviouseTransactions($settlement->id,false, $no_change);

            }



            $business_locations = BusinessLocation::forDropdown($business_id);

            $default_location = current(array_keys($business_locations->toArray()));



            $settlement = Settlement::where('settlements.id', $settlement->id)->where('settlements.business_id', $business_id)

                ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')

                ->with([

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

                ->select('settlements.*', 'pump_operators.name as pump_operator_name')

                ->first();

            $business = Business::where('id', $settlement->business_id)->first();

            $pump_operator = PumpOperator::where('id', $settlement->pump_operator_id)->first();

            

            

            $total_sales_amount = $settlement->meter_sales->sum('sub_total') + $settlement->other_sales->sum('sub_total');

            $total_sales_discount_amount = $settlement->meter_sales->sum('discount_amount') + $settlement->other_sales->sum('discount_amount');

            

            $pump_ids = $settlement->meter_sales->pluck('pump_id')->unique()->toArray();

            $pumps = Pump::whereIn('id',$pump_ids)->select('pump_name')->pluck('pump_name')->toArray() ?? [];

            

            $subscription = Subscription::active_subscription($business_id);

            $monthly_max_sale_limit = $subscription->package->monthly_max_sale_limit;



            $startOfMonth = \Carbon::now()->startOfMonth()->toDateString();

            $endOfMonth = \Carbon::now()->endOfMonth()->toDateString();



            $current_monthly_sale = DB::table('transactions')

                ->select(DB::raw('sum(final_total) as total'))

                ->where('business_id', $business_id)

                ->whereIn('type', ['sell', 'property_sell'])

                ->whereBetween('transaction_date', [$startOfMonth, $endOfMonth])

                ->groupBy('business_id')

                ->first();



            $current_monthly_sale = is_null($current_monthly_sale) ? 0 : (double) $current_monthly_sale->total;

            $current_monthly_sale += $total_sales_amount;



            if($current_monthly_sale > $monthly_max_sale_limit) {

                return [

                    'success' => 0,

                    'msg' => __('lang_v1.monthly_max_sale_limit_exceeded', ['monthly_max_sale_limit' => $monthly_max_sale_limit])

                ];

            }

            $transaction = $this->createTransaction($settlement, $total_sales_amount, null, $settlement->pump_operator_id, 'sell', 'settlement', $settlement_no, null, 0, $total_sales_discount_amount);

            $sell_transaction = $transaction;

            $tax_amt = 0;

            foreach ($settlement->meter_sales as $meter_sale) {

                $fuel_tank_id = Pump::where('id', $meter_sale->pump_id)->first()->fuel_tank_id;

                $sell_line = $this->createSellTransactions($transaction, $meter_sale, $business_id, $default_location, $fuel_tank_id,null);

                MeterSale::where('id', $meter_sale->id)->update(['transaction_id' => $transaction->id]);

            }

            

            foreach ($settlement->other_sales as $other_sale) {

                $getOtherSale = OtherSale::where('id', $other_sale->id)->first();

                if($getOtherSale->transaction_id == null || $getOtherSale->transaction_id !=$transaction->id){

                    $sell_line = $this->createSellTransactions($transaction, $other_sale, $business_id, $default_location,null,true);

                    OtherSale::where('id', $other_sale->id)->update(['transaction_id' => $transaction->id]);

                }

            }

            

            foreach ($settlement->other_incomes as $other_income) {

                $sell_line = $this->createSellTransactions($transaction, $other_income, $business_id, $default_location,null,null);

                OtherIncome::where('id', $other_income->id)->update(['transaction_id' => $transaction->id]);

            }

            

            /* map purchase sell lines */

            $this->createStockAccountTransactions($transaction); // @eng 11/2 1700

            $this->mapSellPurchaseLines($business_id, $transaction, $settlement);

            $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');

            

            $cash_note = "";

            foreach ($settlement->cash_payments as $cash_payment) {

                $i = 0;

                $cash_transaction_payment = $this->createTransaction($settlement, $cash_payment->amount, $cash_payment->customer_id, null, 'settlement', 'cash_payment', $settlement_no);

                $cash_note .=!empty($cash_payment->note) ? "Note ".$i++.": ".$cash_payment->note."\n" : "";

            }

            

            foreach ($settlement->customer_loans as $customer_loan) {

                

                $customer_loan_transaction = $this->createTransaction($settlement, $customer_loan->amount, $customer_loan->customer_id, null, 'settlement', 'customer_loan', $settlement_no,null, 0, 0.00,$customer_loan->note);

                

                $type = 'debit';

                $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');

                

                $this->createAccountTransaction($customer_loan_transaction, $type, $account_id, $customer_loan_transaction->id, 'null', null,  $customer_loan->amount,false,$customer_loan->note);

                

                $type = 'debit';

                $account_id = $this->transactionUtil->account_exist_return_id('Cash');

                $this->createAccountTransaction($customer_loan_transaction, $type, $account_id, $customer_loan_transaction->id, 'null', null,  $customer_loan->amount,false,$customer_loan->note);

                

                $type = 'credit';

                $account_id = $this->transactionUtil->account_exist_return_id('Cash');

                $this->createAccountTransaction($customer_loan_transaction, $type, $account_id, $customer_loan_transaction->id, 'null', null,  $customer_loan->amount,false,$customer_loan->note);

                

            }

            

            $loan_note = "";

            foreach ($settlement->loan_payments as $loan_payment) {

                $i = 0;

                //this transaction will use in report to show amounts

                $loan_transaction_payment = $this->createTransaction($settlement, $loan_payment->amount, null, null, 'settlement', 'loan_payment', $settlement_no);

                

                $loan_note .=!empty($loan_payment->note) ? "Note ".$i++.": ".$loan_payment->note."\n" : "";

                

                $type = 'debit';

                $account_id = $this->transactionUtil->account_exist_return_id('Cash');

                $this->createAccountTransaction($loan_transaction_payment, $type, $account_id, $loan_transaction_payment->id, 'null', null,  $loan_payment->amount,false,$loan_payment->note);

                

                

                $type = 'credit';

                $account_id = $this->transactionUtil->account_exist_return_id('Cash');

                $this->createAccountTransaction($loan_transaction_payment, $type, $account_id, $loan_transaction_payment->id, 'null', null,  $loan_payment->amount,false,$loan_payment->note);

                

                $type = 'debit';

                $account_id = $loan_payment->loan_account;

                $this->createAccountTransaction($loan_transaction_payment, $type, $account_id, $loan_transaction_payment->id, 'null', null,  $loan_payment->amount,false,$loan_payment->note);

            

            }

            

            $drawing_note = "";

            foreach ($settlement->drawings_payments as $drawing_payment) {

                $i = 0;

                //this transaction will use in report to show amounts

                $drawing_transaction_payment = $this->createTransaction($settlement, $drawing_payment->amount, null, null, 'settlement', 'drawing_payment', $settlement_no);

                $drawing_note .=!empty($drawing_payment->note) ? "Note ".$i++.": ".$drawing_payment->note."\n" : "";

                

                $type = 'debit';

                $account_id = $this->transactionUtil->account_exist_return_id('Cash');

                $this->createAccountTransaction($drawing_transaction_payment, $type, $account_id, $drawing_transaction_payment->id, 'null', null,  $drawing_payment->amount,false,$drawing_payment->note);

                

                $type = 'credit';

                $account_id = $this->transactionUtil->account_exist_return_id('Cash');

                $this->createAccountTransaction($drawing_transaction_payment, $type, $account_id, $drawing_transaction_payment->id, 'null', null,  $drawing_payment->amount,false,$drawing_payment->note);

                

                

                $type = 'debit';

                $account_id = $drawing_payment->loan_account;

                $this->createAccountTransaction($drawing_transaction_payment, $type, $account_id, $drawing_transaction_payment->id, 'null', null,  $drawing_payment->amount,false,$drawing_payment->note);

            

            }

            

            foreach ($settlement->cash_deposits as $cash_payment) {

                $i = 0;

                //this transaction will use in report to show amounts

                $cash_deposit = $this->createTransaction($settlement, $cash_payment->amount,null, null, 'settlement', 'cash_deposit', $settlement_no,$cash_payment->id);

                

                $type = 'debit';

                $account_id = $this->transactionUtil->account_exist_return_id('Cash');

                $this->createAccountTransaction($cash_deposit, $type, $account_id, $cash_deposit->id, 'null', null,  $cash_payment->amount,false,null);

                

                $this->createAccountTransaction($cash_deposit, 'credit', $account_id, $cash_deposit->id, 'null', null,  $cash_payment->amount,false,null);

                

                

                $type = 'debit';

                $account_id = $cash_payment->bank_id;

                $this->createAccountTransaction($cash_deposit, $type, $account_id, $cash_deposit->id, 'null', null,  $cash_payment->amount,false,null);

                

                

                $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

                $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for','cash_deposit')->first();

                if(!empty($msg_template)){

                    $msg = $msg_template->sms_body;

                    

                    $account = Account::find($cash_payment->bank_id);

                    

                    $bank_name = !empty($account) ? $account->name : "";

                

                    $msg = str_replace('{account}',$cash_payment->account_no,$msg);

                    $msg = str_replace('{amount}',$this->transactionUtil->num_f($cash_payment->amount),$msg);

                    $msg = str_replace('{time}',$this->transactionUtil->format_date($cash_payment->time_deposited,true),$msg);

                    $msg = str_replace('{bank}',$bank_name,$msg);

                    

                    

                    $phones = [];

                

                    if(!empty($business->sms_settings)){

                        $phones = explode(',',str_replace(' ','',$business->sms_settings['msg_phone_nos']));

                    }

                    foreach($phones as $phone){

                        $data = [

                            'sms_settings' => $sms_settings,

                            'mobile_number' => $phone,

                            'sms_body' => $msg

                        ];

                        $response = $this->transactionUtil->sendSms($data);

                    }

                }

            

                

            }

            

            

            $cash_transaction_payment = null;

            

            if ($settlement->cash_payments->sum('amount') > 0) {

                $cash_transaction_payment = $this->createTansactionPayment($transaction, 'cash', $settlement->cash_payments->sum('amount'));

              

            }

            



            foreach ($settlement->card_payments as $card_payment) {

                //this transaction will use in report to show amounts

                $this->createTransaction($settlement, $card_payment->amount, $card_payment->customer_id, null, 'settlement', 'card_payment', $settlement_no);

                $transaction_payment = $this->createTansactionPayment($transaction, 'card', $card_payment->amount, $card_payment->card_number, $card_payment->card_type);

                SettlementCardPayment::where('id', $card_payment->id)->update(['customer_payment_id' => $transaction_payment->id]);

                if (!empty($card_payment->card_type)) {

                    $account_id = $card_payment->card_type;

                } else {

                    $account_id = $this->transactionUtil->account_exist_return_id('Cards (Credit Debit) Account');

                }

                $type = 'debit';

                

                $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id, 'ledger_show', $card_payment->customer_id, $card_payment->amount,false,$card_payment->note,$card_payment->slip_no);

            }

            

            foreach ($settlement->cheque_payments as $cheque_payment) {

                //this transaction will use in report to show amounts

                $cheque_transaction = $this->createTransaction($settlement, $cheque_payment->amount, $cheque_payment->customer_id, null, 'settlement', 'cheque_payment', $settlement_no);

                $transaction_payment = $this->createTansactionPayment($transaction, 'cheque', $cheque_payment->amount, null, null, $cheque_payment->cheque_number, $cheque_payment->bank_name, $cheque_payment->cheque_date,$cheque_payment->post_dated_cheque);

                

                $contact = Contact::where('id',$cheque_payment->customer_id)->first();

                

                $cheque_transaction->contact = $contact;

                $cheque_transaction->single_payment_amount = $this->transactionUtil->num_uf($cheque_payment->amount);

                $cheque_transaction->payment_ref_number ="";

                $this->notificationUtil->autoSendNotification($business_id, 'payment_received', $cheque_transaction, $cheque_transaction->contact,true);

                

                SettlementChequePayment::where('id', $cheque_payment->id)->update(['customer_payment_id' => $transaction_payment->id]);

                $account_id = $this->transactionUtil->account_exist_return_id('Cheques in Hand');

                $type = 'debit';

                

                

               $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id, 'ledger_show', $cheque_payment->customer_id, $cheque_payment->amount,false,$cheque_payment->note);

            }

            

            

            if(empty($no_change)){

                foreach ($settlement->credit_sale_payments as $credit_sale_payment) {

                

                    $transaction = $this->createCreditSellTransactions($settlement, $credit_sale_payment, $default_location);

                    SettlementCreditSalePayment::where('id', $credit_sale_payment->id)->update(['transaction_id' => $transaction->id]);

                    $credit_sale_payment->transaction_id = $transaction->id;

                    $credit_sale_payment->save();

                    

                    $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');

                    $type = 'debit';

                    $this->createAccountTransaction($transaction, $type, $account_id, null, 'ledger_show', null, 0, true,$credit_sale_payment->note);

                    

                    if($credit_sale_payment->is_from_pumper == 0){

                        

                        // store the customer reference

                        if(!empty($credit_sale_payment->customer_reference)){

                            $customer = Contact::findOrFail($credit_sale_payment->customer_id);

                            $name = $customer->name;

                            $barcode_string = $name . '.' . $credit_sale_payment->customer_reference;

                            $qr = new DNS2D();

                            $qr = $qr->getBarcodePNG($barcode_string, 'QRCODE');

                            $src = 'data:image/png;base64,' . $qr;

                

                            

                            $ref_data = array(

                                'business_id' => $credit_sale_payment->business_id,

                                'date' => date('Y-m-d', strtotime($credit_sale_payment->order_date)),

                                'contact_id' => $credit_sale_payment->customer_id,

                                'reference' =>$credit_sale_payment->customer_reference,

                                'barcode_src' => $src

                            );

                            CustomerReference::updateOrCreate(['business_id' => $credit_sale_payment->business_id,'contact_id' => $credit_sale_payment->customer_id,'reference' =>$credit_sale_payment->customer_reference],$ref_data);

                            

                        }

                        

                        $cheque_pmt = SettlementChequePayment::where('customer_id',$credit_sale_payment->customer_id)

                                                            ->where('settlement_no',$credit_sale_payment->settlement_no)->sum('amount');

                        

                        $cash_pmt = SettlementCardPayment::where('customer_id',$credit_sale_payment->customer_id)

                                                            ->where('settlement_no',$credit_sale_payment->settlement_no)->sum('amount');

                                                            

                        $card_pmt = SettlementCashPayment::where('customer_id',$credit_sale_payment->customer_id)

                                                            ->where('settlement_no',$credit_sale_payment->settlement_no)->sum('amount');

                                                            

                        $total_paid = $cheque_pmt + $cash_pmt + $card_pmt;

                    

                    

                        $business_id = request()->session()->get('user.business_id');

                        $business = Business::where('id', $business_id)->first();

                        $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

                        

                        $contact = Contact::where('id',$credit_sale_payment->customer_id)->first();

                        

                        $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for','credit_sale')->first();

    

    

                        $final_total = $credit_sale_payment->amount - $credit_sale_payment->total_discount;

                        $product = Product::findOrFail($credit_sale_payment->product_id);

                        

                        $product_msg = PHP_EOL."Product Sold: ".ucfirst($product->name).PHP_EOL."Quantity: ".$this->productUtil->num_f($credit_sale_payment->qty);

                        

                    

                        if(!empty($msg_template) && $contact->credit_notification == 'settlement'){

                            

                            $msg = $msg_template->sms_body;

                            $msg = str_replace('{business_name}',$business->name,$msg);

                            $msg = str_replace('{total_amount}',$this->productUtil->num_f($final_total),$msg);

                            $msg = str_replace('{contact_name}',$contact->name,$msg);

                            $msg = str_replace('{invoice_number}',$settlement->settlement_no,$msg);

                            

                            $msg = str_replace('{transaction_date}',$settlement->transaction_date,$msg);

                            

                            $msg = str_replace('{paid_amount}',$this->productUtil->num_f($total_paid),$msg);

                            $msg = str_replace('{due_amount}',$this->productUtil->num_f($final_total - $total_paid),$msg);

                            $msg = str_replace('{cumulative_due_amount}', $this->productUtil->num_f(strval($contactController->get_due_bal($credit_sale_payment->customer_id, false))),$msg);

                            $msg .= $product_msg;

                           

                            if(!empty($business->sms_settings)){

                                $phones = explode(',',str_replace(' ','',$business->sms_settings['msg_phone_nos']));

                            }

                            

                            $data = [

                                    'sms_settings' => $sms_settings,

                                    'mobile_number' => $contact->mobile,

                                    'sms_body' => $msg

                            ];

                            

                            $response = $this->businessUtil->sendSms($data);

        

                            $data['mobile_number'] = $contact->alternate_number;

                            

                            $response = $this->businessUtil->sendSms($data,$contact,'credit_sale');

                        }

                    }else{

                        $credit_sale_payment->is_from_pumper = 0;

                        $credit_sale_payment->is_committed = 1;

                        $credit_sale_payment->save();

                    } 

                }

            }



            

            

            $total_shortage = $pump_operator->short_amount; //get previous amount

            foreach ($settlement->shortage_payments as $shortage_payment) {

                $transaction = $this->createTransaction($settlement, $shortage_payment->amount, null, $settlement->pump_operator_id, 'settlement', 'shortage', $settlement_no);

                SettlementShortagePayment::where('id', $shortage_payment->id)->update(['transaction_id' => $transaction->id]);

                $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');

                $type = 'debit';

                $this->createAccountTransaction($transaction, $type, $account_id, null, 'ledger_show',null,0,false,$shortage_payment->note);

                $total_shortage += $shortage_payment->amount;

            }

            

            $total_excess = $pump_operator->excess_amount; //get previous amount

            foreach ($settlement->excess_payments as $excess_payment) {

                $transaction = $this->createTransaction($settlement, $excess_payment->amount, null, $settlement->pump_operator_id, 'settlement', 'excess', $settlement_no);

                SettlementExcessPayment::where('id', $excess_payment->id)->update(['transaction_id' => $transaction->id]);

                $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');

                $type = 'credit';

                $this->createAccountTransaction($transaction, $type, $account_id, null, 'ledger_show',null,0,false,$excess_payment->note);

                $total_excess += $excess_payment->amount;

            }

            $pump_operator->short_amount =  $total_shortage;

            $pump_operator->excess_amount =  $total_excess;

            $pump_operator->settlement_no =  $settlement->settlement_no;



            $pump_operator->save();



            foreach ($settlement->expense_payments as $expense_payment) {

                $transaction = $this->createTransaction($settlement, $expense_payment->amount, null, $settlement->pump_operator_id, 'settlement', 'expense', $settlement_no);

                $transaction->expense_category_id =  $expense_payment->category_id;

                $transaction->ref_no =  "Settlement No: " . $settlement->settlement_no;

                $transaction->expense_account = $expense_payment->account_id;

                $transaction->save();

                SettlementExpensePayment::where('id', $expense_payment->id)->update(['transaction_id' => $transaction->id]);

                $transaction_payment = $this->createTansactionPayment($transaction, 'cash');

                

                $account_id = $expense_payment->account_id;

                $type = 'debit';

                $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id);

                

                $account_id = $this->transactionUtil->account_exist_return_id('Cash');

                $type = 'credit';

                $this->createAccountTransaction($transaction, $type, $account_id, $transaction_payment->id);

            }



            if(($settlement->expense_payments->sum('amount') + $settlement->cash_payments->sum('amount')) > 0){

                //Cash payment + expense payment  //doc 3075 - POS Settlement Expense amount in cash account – 5 Nov 2020

                $account_id = $this->transactionUtil->account_exist_return_id('Cash');

                $expense_transaction_data = [

                    'amount' => $settlement->expense_payments->sum('amount') + $settlement->cash_payments->sum('amount'),

                    'account_id' => $account_id,

                    'contact_id' => $sell_transaction->contact_id,

                    'type' => 'debit',

                    'sub_type' => null,

                    'operation_date' => $sell_transaction->transaction_date,

                    'created_by' => $sell_transaction->created_by,

                    'transaction_id' => $sell_transaction->id,

                    'transaction_payment_id' => !empty($cash_transaction_payment) ? $cash_transaction_payment->id : null,

                    'note' => $cash_note

                ];

                AccountTransaction::createAccountTransaction($expense_transaction_data);

            }

            

            foreach($settlement->customer_payments as $customer_payments){

                $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');

                

                $ob_transaction_data = [

                    'amount' => $customer_payments->amount,

                    'post_dated_cheque' => $customer_payments->post_dated_cheque,

                    'account_id' => $account_id,

                    'type' => 'debit',

                    'sub_type' => 'deposit',

                    'operation_date' => $sell_transaction->transaction_date,

                    'created_by' => auth()->user()->id,

                    'transaction_id' =>$sell_transaction->id,

                    'transaction_payment_id' =>  null

                ];

    

                AccountTransaction::createAccountTransaction($ob_transaction_data);

            }

                





            //this for only to show in print page customer payments which entered in customer payments tab

            $customer_payments_tab = CustomerPayment::leftjoin('contacts', 'customer_payments.customer_id', 'contacts.id')

                ->where('customer_payments.settlement_no', $settlement->id)

                ->where('customer_payments.business_id', $business_id)

                ->select('customer_payments.*', 'contacts.name as customer_name')

                ->get();

            $settlement_total = $settlement->meter_sales->sum('sub_total') + $settlement->other_sales->sum('sub_total') + $settlement->other_incomes->sum('sub_total') + $settlement->customer_payments->sum('sub_total');

            $settlement->total_amount = $settlement_total;

            

            $settlement->status = 0; // set status to non active

            $settlement->is_edit = 0;

            if($denom_enabled > 0){

                $settlement->cash_denomination = json_encode($denom_data);

            }else{

                $settlement->cash_denomination = NULL;

            }

            $settlement->finish_date = date('Y-m-d');

            $settlement->save();



            // //Get Daily Collection from businiss_id and pomp_operator id and settlement_id is null

            $daily_collections = DailyCollection::leftjoin('business_locations', 'daily_collections.location_id', 'business_locations.id')

            ->leftjoin('pump_operators', 'daily_collections.pump_operator_id', 'pump_operators.id')

            ->leftjoin('users', 'daily_collections.created_by', 'users.id')

            ->leftjoin('settlements', 'daily_collections.settlement_id', 'settlements.id')

            ->where('daily_collections.business_id', $business_id)

            ->where('daily_collections.pump_operator_id', $settlement->pump_operator_id)

            ->whereNull('settlement_id')

            ->select([

                'daily_collections.*',

                'business_locations.name as location_name',

                'pump_operators.name as pump_operator_name',

                'settlements.id as settlements_id',

                'users.username as user',

            ])->orderBy('daily_collections.id')->get();



            $outstanding_payment = $settlement_total;

            foreach ($daily_collections as $daily_collections) {

                # code...

                if ($outstanding_payment >= 0) {

                    $outstanding_payment = floatval($outstanding_payment) - floatval($daily_collections->current_amount);

                    DB::update('update daily_collections set settlement_id = ?, settlement_date = ?, balance_collection = ? where business_id = ? and pump_operator_id = ? and id = ? and settlement_id is null',

                     [$settlement->id, $settlement->finish_date, floatval($daily_collections->current_amount), $business_id, $settlement->pump_operator_id, $daily_collections->id]);

                    // echo var_dump($outstanding_payment ."/nr");

                }

            }

            

            

            // create VAT entries

            $this->transactionUtil->calculateAndUpdateVAT($sell_transaction);
            
            
            PumpOperatorAssignment::where('settlement_id', null)
                                ->whereIn('pump_id', $pump_ids)
                                ->update(['settlement_id' => $settlement->id]);

            



            DB::commit();

            

            

            

            $sms_data = array(

                'settlement_id' => $settlement->id,

                'settlement_no' => $settlement->settlement_no,

                'settlement_date' => $this->transactionUtil->format_date($settlement->transaction_date),

                'pump_operator_name' => $pump_operator->name,

                'settlement_pumps' => implode(',',$pumps),

                'total_sale_amount' => $this->transactionUtil->num_f($total_sales_amount),

                'total_cash' => $this->transactionUtil->num_f($settlement->cash_payments->sum('amount')),

                'total_cards' => $this->transactionUtil->num_f($settlement->card_payments->sum('amount')),

                'total_credit_sales' => $this->transactionUtil->num_f($settlement->credit_sale_payments->sum('amount')),

                'total_short' => $this->transactionUtil->num_f($settlement->shortage_payments->sum('amount')),

                'total_loans' => $this->transactionUtil->num_f($settlement->customer_loans->sum('amount')),

                'total_cheques' => $this->transactionUtil->num_f($settlement->cheque_payments->sum('amount')),

                

                'cash_deposit' => $this->transactionUtil->num_f($settlement->cash_deposits->sum('amount')),

                'total_expenses' => $this->transactionUtil->num_f($settlement->expense_payments->sum('amount')),

                'total_excess' => $this->transactionUtil->num_f($settlement->excess_payments->sum('amount')),

                'loan_payments' => $this->transactionUtil->num_f($settlement->loan_payments->sum('amount')),

                'owners_drawings' => $this->transactionUtil->num_f($settlement->drawings_payments->sum('amount')),

                

                'editted_by' => auth()->user()->username

            );

           

            if (!empty($edit)) {

                

                $original_details = SettlementEditHistory::where('settlement_id',$settlement->id)->first();

                $o_details = "";

                $n_details = "";

                

                $is_changed = false;

                $changed_msg = "";

                if(!empty($original_details)){

                    

                    if($original_details->settlement_date!= $sms_data['settlement_date']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.settlement_date').__('petro::lang.changed_from')

                                        .$original_details->settlement_date

                                        .__('petro::lang.to').$sms_data['settlement_date'].PHP_EOL;

                                        

                        $o_details .= __('petro::lang.settlement_date').": ".$original_details->settlement_date.PHP_EOL;

                        $n_details .= __('petro::lang.settlement_date').": ".$sms_data['settlement_date'].PHP_EOL;

                    }

                    

                    if($original_details->pump_operator_name!= $sms_data['pump_operator_name']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.pump_operator_name').__('petro::lang.changed_from')

                                        .$original_details->pump_operator_name

                                        .__('petro::lang.to').$sms_data['pump_operator_name'].PHP_EOL;

                        $o_details .= __('petro::lang.pump_operator_name').": ".$original_details->pump_operator_name.PHP_EOL;

                        $n_details .= __('petro::lang.pump_operator_name').": ".$sms_data['pump_operator_name'].PHP_EOL;

                        

                    }

                    

                    if($original_details->settlement_pumps != $sms_data['settlement_pumps']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.settlement_pumps').__('petro::lang.changed_from')

                                        .$original_details->settlement_pumps

                                        .__('petro::lang.to').$sms_data['settlement_pumps'].PHP_EOL;

                        $o_details .= __('petro::lang.settlement_pumps').": ".$original_details->settlement_pumps.PHP_EOL;

                        $n_details .= __('petro::lang.settlement_pumps').": ".$sms_data['settlement_pumps'].PHP_EOL;

                    }

                    

                    if($original_details->total_sale_amount!= $sms_data['total_sale_amount']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.total_sale_amount').__('petro::lang.changed_from')

                                        .$original_details->total_sale_amount

                                        .__('petro::lang.to').$sms_data['total_sale_amount'].PHP_EOL;

                        $o_details .= __('petro::lang.total_sale_amount').": ".$original_details->total_sale_amount.PHP_EOL;

                        $n_details .= __('petro::lang.total_sale_amount').": ".$sms_data['total_sale_amount'].PHP_EOL;

                    }

                    

                    if($original_details->total_cash!= $sms_data['total_cash']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.total_cash').__('petro::lang.changed_from')

                                        .$original_details->total_cash

                                        .__('petro::lang.to').$sms_data['total_cash'].PHP_EOL;

                        

                        $o_details .= __('petro::lang.total_cash').": ".$original_details->total_cash.PHP_EOL;

                        $n_details .= __('petro::lang.total_cash').": ".$sms_data['total_cash'].PHP_EOL;

                    }

                    

                    if($original_details->total_cards!= $sms_data['total_cards']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.total_cards').__('petro::lang.changed_from')

                                        .$original_details->total_cards

                                        .__('petro::lang.to').$sms_data['total_cards'].PHP_EOL;

                        $o_details .= __('petro::lang.total_cards').": ".$original_details->total_cards.PHP_EOL;

                        $n_details .= __('petro::lang.total_cards').": ".$sms_data['total_cards'].PHP_EOL;

                    }

                    

                    if($original_details->total_credit_sales!= $sms_data['total_credit_sales']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.total_credit_sales').__('petro::lang.changed_from')

                                        .$original_details->total_credit_sales

                                        .__('petro::lang.to').$sms_data['total_credit_sales'].PHP_EOL;

                        $o_details .= __('petro::lang.total_credit_sales').": ".$original_details->total_credit_sales.PHP_EOL;

                        $n_details .= __('petro::lang.total_credit_sales').": ".$sms_data['total_credit_sales'].PHP_EOL;

                    }

                    

                    if($original_details->total_short!= $sms_data['total_short']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.total_short').__('petro::lang.changed_from')

                                        .$original_details->total_short

                                        .__('petro::lang.to').$sms_data['total_short'].PHP_EOL;

                                        

                        $o_details .= __('petro::lang.total_short').": ".$original_details->total_short.PHP_EOL;

                        $n_details .= __('petro::lang.total_short').": ".$sms_data['total_short'].PHP_EOL;

                    }

                     if($original_details->total_loans!= $sms_data['total_loans']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.total_loans').__('petro::lang.changed_from')

                                        .$original_details->total_loans

                                        .__('petro::lang.to').$sms_data['total_loans'].PHP_EOL;

                                        

                        $o_details .= __('petro::lang.total_loans').": ".$original_details->total_loans.PHP_EOL;

                        $n_details .= __('petro::lang.total_loans').": ".$sms_data['total_loans'].PHP_EOL;

                    }

                    

                     if($original_details->total_cheques!= $sms_data['total_cheques']){

                        $is_changed = true;

                        $changed_msg .=  __('petro::lang.total_cheques').__('petro::lang.changed_from')

                                        .$original_details->total_cheques

                                        .__('petro::lang.to').$sms_data['total_cheques'].PHP_EOL;

                                        

                        $o_details .= __('petro::lang.total_cheques').": ".$original_details->total_cheques.PHP_EOL;

                        $n_details .= __('petro::lang.total_cheques').": ".$sms_data['total_cheques'].PHP_EOL;

                    }

                    

                    if(!empty($is_changed) && !empty($changed_msg)){

                        

                        $activity = new Activity();

                        $activity->log_name = "Settlement";

                        $activity->description = "update";

                        $activity->subject_id = $settlement->id;

                        $activity->subject_type = "App\Settlement";

                        $activity->causer_id = auth()->user()->id;

                        $activity->causer_type = 'App\User';

                        $activity->properties = $changed_msg ;

                        $activity->created_at = date('Y-m-d H:i');

                        $activity->updated_at = date('Y-m-d H:i');

                        

                        // Save the activity

                        $activity->save();

                        

                    }

                    

                    

                    

                }

                

                

                $data = array(

                    'settlement_no' => $settlement->settlement_no,

                    'editted_date' => $this->transactionUtil->format_date(date('Y-m-d')),

                    'user_editted' => auth()->user()->username,

                    'original_details' => $o_details,

                    'editted_details' => $n_details,

                );

                

                $this->notificationUtil->sendPetroNotification('edit_settlements',$data);

            }else{

                $this->notificationUtil->sendPetroNotification('settlements',$sms_data);

            }

            

            

            SettlementEditHistory::updateOrCreate(array('settlement_id' => $settlement->id),$sms_data);
            
            $total_daily_collection = floatval(DailyCollection::where('pump_operator_id', $settlement->pump_operator_id)->where('business_id', $business_id)->where('settlement_id', $settlement->id)->sum('current_amount'));


            return view('petro::settlement.print')->with(compact('settlement', 'business', 'pump_operator', 'customer_payments_tab', 'total_daily_collection'));

        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => 0,

                'msg' => $e->getMessage()

            ];

        }



        return $output;

    }







    public function createTransaction($settlement, $amount, $customer_id = null, $pump_operator_id = null, $type = 'sell', $sub_type, $settlement_no, $ref_no = null, $is_credit_sale = 0, $total_sales_discount_amount = 0.00,$transaction_note = null)

    {



        $business_id = request()->session()->get('business.id');

        $business_location = BusinessLocation::where('business_id', $business_id)

            ->first();

        $total_sales_discount_amount = !empty($total_sales_discount_amount) ?? 0;

        $final_amount = $amount;

        $ob_data = [

            'business_id' => $business_id,

            'location_id' => $business_location->id,

            'type' => $type,

            'sub_type' => $sub_type,

            'status' => 'final',

            'payment_status' => 'paid',

            'contact_id' => $customer_id,

            'pump_operator_id' => $pump_operator_id,

            'transaction_date' => \Carbon::parse($settlement->transaction_date)->format('Y-m-d'),

            'total_before_tax' => $final_amount,

            'final_total' => $final_amount,

            'discount_amount' => $total_sales_discount_amount,

            'created_by' => request()->session()->get('user.id'),

            'is_settlement' => 1,

            'transaction_note' => $transaction_note

        ];

        if ($sub_type == 'excess' || $sub_type == 'shortage' || $sub_type == 'customer_loan') {

            $ob_data['payment_status'] = 'due';

        }



        $ob_data['invoice_no'] = $settlement_no;

        $ob_data['ref_no'] = !empty($ref_no) ? $ref_no : null;

        if ($is_credit_sale == 1) {

            $ob_data['type'] = 'sell';

            $ob_data['sub_type'] = 'credit_sale';

        }

        $transaction = Transaction::create($ob_data);

        return $transaction;

    }



    public function createSellTransactions($transaction, $sale, $business_id, $default_location, $fuel_tank_id = null,$is_other_sale = null)

    {

        $uf_quantity = $this->productUtil->num_uf($sale->qty);

        $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')

            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')

            ->leftjoin('categories', 'products.category_id', 'categories.id')

            ->where('products.id', $sale->product_id)

            ->select('variations.id as variation_id', 'variation_location_details.location_id', 'products.id as product_id', 'categories.name as category_name', 'products.enable_stock')->first();



        $this->transactionUtil->createOrUpdateSellLinesSettlement($transaction, $product->product_id, $product->variation_id, $product->location_id, $sale);

        $location_product = !empty($product->location_id) ? $product->location_id : $default_location;

        

        // if enable stock

        if ($product->enable_stock && !empty($is_other_sale)) {

            

            $otherSale = OtherSale::where('id', $sale->id)->first();



            $this->productUtil->decreaseProductQuantity(

                $sale->product_id,

                $product->variation_id,

                $location_product,

                $uf_quantity,

                0,

                'decrease',

                isset($otherSale->store_id) ? $otherSale->store_id : 0

            );

            

            $store_id = Store::where('business_id', $business_id)->first()->id;

			$this->productUtil->decreaseProductQuantityStore(

                $sale->product_id,

                $product->variation_id,

                $location_product,

                $uf_quantity,

                isset($otherSale->store_id) ? $otherSale->store_id : $store_id,

                "decrease",

                0

            );



        }



        //update qty to fuel tank current stock

        if (!empty($fuel_tank_id)) {

            FuelTank::where('id', $fuel_tank_id)->decrement('current_balance', $sale->qty);

            TankSellLine::create([

                'business_id' => $business_id,

                'transaction_id' => $transaction->id,

                'tank_id' => $fuel_tank_id,

                'product_id' => $sale->product_id,

                'quantity' => $sale->qty

            ]);

        }



        return true;

    }



    

    public function createCreditSellTransactions($settlement, $sale,$default_location)

    {

        $final_total = $sale->amount - $sale->total_discount;

        $ob_data = [

            'business_id' => $sale->business_id,

            'location_id' => $settlement->location_id,

            'type' => 'sell',

            'status' => 'final',

            'payment_status' => 'due',

            'contact_id' => $sale->customer_id,

            'pump_operator_id' => $settlement->pump_operator_id,

            'transaction_date' => \Carbon::parse($settlement->transaction_date)->format('Y-m-d'),

            'total_before_tax' => $final_total,

            'final_total' => $final_total,

            'discount_type' => 'fixed',

            'discount_amount' => $sale->total_discount,

            'credit_sale_id' => $sale->id,

            'is_credit_sale' => 1,

            'is_settlement' => 1,

            'created_by' => request()->session()->get('user.id'),

            'invoice_no' => $settlement->settlement_no,

            'ref_no' => $sale->customer_reference,

            'customer_ref' => $sale->customer_reference,

            'order_date' => $sale->order_date,

            'order_no' => $sale->order_number,

            'sub_type' => 'credit_sale',

            

        ];

        

       

        //Create transaction

        $transaction = Transaction::create($ob_data);

        

        return $transaction;

    }



    public function mapSellPurchaseLines($business_id, $transaction, $settlement)

    {

        //Allocate the quantity from purchase and add mapping of

        //purchase & sell lines in transaction_sell_lines_purchase_lines table

        $business_details = $this->businessUtil->getDetails($business_id);

        $pos_settings = empty($business_details->pos_settings) ? $this->businessUtil->defaultPosSettings() : json_decode($business_details->pos_settings, true);



        $business = [

            'id' => $business_id,

            'accounting_method' => request()->session()->get('business.accounting_method'),

            'location_id' => $settlement->location_id,

            'pos_settings' => $pos_settings

        ];

        $this->transactionUtil->mapPurchaseSell($business, $transaction->sell_lines, 'purchase');

    }



    public function createTansactionPayment($transaction, $method, $amount = 0, $card_number = null, $card_type = null, $cheque_number = null, $bank_name = null, $cheque_date = null,$post_dated_cheque = 0)

    {

        $business_id = request()->session()->get('business.id');

        $transaction_payment_data = [

            'transaction_id' => $transaction->id,

            'business_id' => $business_id,

            'amount' => abs($transaction->final_total),

            'method' => $method,

            'paid_on' => $transaction->transaction_date,

            'created_by' => $transaction->created_by,

            'card_number' => $card_number,

            'card_type' => $card_type,

            'cheque_number' => $cheque_number,

            'bank_name' => $bank_name,

            'cheque_date' => !empty($cheque_date) ? \Carbon::parse($cheque_date)->format('Y-m-d') : null,

            'post_dated_cheque' => $post_dated_cheque

        ];



        if (!empty($amount)) {

            $transaction_payment_data['amount'] = $amount;

        }



        $transaction_payment_data['paid_in_type'] = 'settlement';



        $transaction_payment = TransactionPayment::create($transaction_payment_data);



        return $transaction_payment;

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



        AccountTransaction::createAccountTransaction($account_transaction_data);

        // create ledger transactions

        if ($sub_type == 'ledger_show') {

            ContactLedger::createContactLedger($account_transaction_data);

            if (!$is_credit_sale) {

                if ($type == 'debit') {

                    $ledger_type = 'credit';

                }

                if ($type == 'credit') {

                    $ledger_type = 'debit';

                }

                $account_transaction_data['type'] = $ledger_type;

                ContactLedger::createContactLedger($account_transaction_data);

            }

        }

    }



    public function createStockAccountTransactions($transaction)

    {

        $account_transaction_data = [

            'amount' => abs($transaction->final_total),

            'operation_date' => $transaction->transaction_date,

            'created_by' => $transaction->created_by,

            'transaction_id' => $transaction->id,

            'note' => null

        ];



        $this->transactionUtil->manageStockAccount($transaction, $account_transaction_data, 'credit', $transaction->final_total);

        $this->transactionUtil->createCostofGoodsSoldTransaction($transaction, 'ledger_show', 'debit');

        $this->transactionUtil->createSaleIncomeTransaction($transaction, 'ledger_show', 'credit');

    }



    public function deletePreviouseTransactions($settlement_id, $is_destory = false, $no_change = false)

    {

        $business_id = request()->session()->get('business.id');

        $settlement = Settlement::find($settlement_id);

        $all_trasactions = Transaction::where('invoice_no', $settlement->settlement_no)->where('is_settlement', 1)->where('business_id', $business_id)->with(['sell_lines'])->withTrashed()->get();



        foreach ($all_trasactions as $transaction) {

            

            if(!empty($no_change) && $transaction->sub_type == 'credit_sale'){

                // for credit sales and no change edit type; skip deleting the existing credit sales

                continue;

            }

            

            

            if (!empty($transaction)) {

                $deleted_sell_lines = $transaction->sell_lines;

                $deleted_sell_lines_ids = $deleted_sell_lines->pluck('id')->toArray();

                if ($transaction->sub_type == 'credit_sale') {

                    $this->transactionUtil->deleteSellLinesSettlement(

                        $deleted_sell_lines_ids,

                        $transaction->location_id,

                        false

                    );

                } else {

                    $this->transactionUtil->deleteSellLinesSettlement(

                        $deleted_sell_lines_ids,

                        $transaction->location_id

                    );

                }



                $transaction->status = 'draft';

                $business = [

                    'id' => $business_id,

                    'accounting_method' => request()->session()->get('business.accounting_method'),

                    'location_id' => $transaction->location_id

                ];

                if ($transaction->sub_type != 'credit_sale') {

                    $this->transactionUtil->adjustMappingPurchaseSell('final', $transaction, $business, $deleted_sell_lines_ids);

                }



                //Delete Cash register transactions

                $transaction->cash_register_payments()->delete();

            }



            $tank_sell_lines =  TankSellLine::where('transaction_id', $transaction->id)->get();

            foreach ($tank_sell_lines as $tank_sell_line) {

                FuelTank::where('id', $tank_sell_line->tank_id)->increment('current_balance', $tank_sell_line->quantity);

            }

            TankSellLine::where('transaction_id', $transaction->id)->forceDelete();

            AccountTransaction::where('transaction_id', $transaction->id)->forceDelete();

            ContactLedger::where('transaction_id', $transaction->id)->forceDelete();

            TransactionPayment::where('transaction_id', $transaction->id)->forceDelete();

            Transaction::where('id', $transaction->id)->forceDelete();

        }



        $settlement->total_amount = 0;

        $settlement->save();





        if ($is_destory) {

            $meter_sales = MeterSale::where('settlement_no', $settlement->id)->get();

            foreach ($meter_sales as $meter_sale) {

                Pump::where('id', $meter_sale->pump_id)->update(['last_meter_reading' => $meter_sale->starting_meter]);

                $meter_sale->delete();

            }

            OtherSale::where('settlement_no', $settlement->id)->delete();

            OtherIncome::where('settlement_no', $settlement->id)->delete();

            CustomerPayment::where('settlement_no', $settlement->id)->delete();

            SettlementCardPayment::where('settlement_no', $settlement->id)->delete();

            SettlementCashPayment::where('settlement_no', $settlement->id)->delete();

            SettlementCashDeposit::where('settlement_no', $settlement->id)->delete();

            SettlementChequePayment::where('settlement_no', $settlement->id)->delete();

            SettlementExpensePayment::where('settlement_no', $settlement->id)->delete();

            SettlementExcessPayment::where('settlement_no', $settlement->id)->delete();

            SettlementShortagePayment::where('settlement_no', $settlement->id)->delete();

            SettlementCreditSalePayment::where('settlement_no', $settlement->id)->delete();

        }

    }





    public function getDiscount($discount)

    {

        $pos = strpos($discount, '%');

        $discount_amount = str_replace('%', '', $discount);

        if ($pos === false) {

            $discount_type = 'fixed';

        } else {

            $discount_type = 'percentage';

        }



        return ['discount_amount' => $discount_amount, 'discount_type' => $discount_type];

    }

    /**

     * Show the specified resource.

     * @return Response

     */

    public function show($id)

    {

        $business_id = request()->session()->get('business.id');

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_location = current(array_keys($business_locations->toArray()));



        $settlement = Settlement::where('settlements.id', $id)->where('settlements.business_id', $business_id)

            ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')

            ->with([

                'meter_sales',

                'other_sales',

                'other_incomes',

                'customer_payments',

                'cash_payments',

                'card_payments',

                'cheque_payments',

                'credit_sale_payments',

                'expense_payments',

                'excess_payments',

                'shortage_payments',

                'loan_payments',

                'drawings_payments',

                'customer_loans'

            ])

            ->select('settlements.*', 'pump_operators.name as pump_operator_name')

            ->first();

        

        $business = Business::where('id', !empty($settlement) ? $settlement->business_id : 0)->first();

        $pump_operator = PumpOperator::where('id', !empty($settlement) ? $settlement->pump_operator_id : 0)->first();



        //this for only to show in print page customer payments which entered in customer payments tab

        $customer_payments_tab = CustomerPayment::leftjoin('contacts', 'customer_payments.customer_id', 'contacts.id')

            ->where('customer_payments.settlement_no', $id)

            ->where('customer_payments.business_id', $business_id)

            ->select('customer_payments.*', 'contacts.name as customer_name')

            ->get();

        $total_daily_collection = floatval(DailyCollection::where('pump_operator_id', $settlement->pump_operator_id)->where('business_id', $business->id)->where('settlement_id', $settlement->id)->sum('current_amount'));
    
    



        return view('petro::settlement.show')->with(compact('settlement', 'business', 'pump_operator', 'customer_payments_tab', 'total_daily_collection'));

    }



    /**

     * Show the form for editing the specified resource.

     * @return Response

     */

    public function edit($id)

    {

        $business_id = request()->session()->get('business.id');

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_location = current(array_keys($business_locations->toArray()));

        $payment_types = $this->productUtil->payment_types($default_location,false, false, false, false,"is_sale_enabled");

        $customers = Contact::customersDropdown($business_id, false, true, 'customer');

        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');



        $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');



        $items = [];





        $active_settlement = Settlement::where('id', $id)

            ->select('settlements.*')

            ->with(['meter_sales', 'other_sales', 'other_incomes', 'customer_payments'])->first();

        $settlement_no = $active_settlement->settlement_no;

        

        

        

        $has_reviewed = $this->transactionUtil->hasReviewed($active_settlement->transaction_date);

        

        if(!empty($has_reviewed)){

            $output              = [

                'success' => 0,

                'msg'     =>__('lang_v1.review_first'),

            ];

            

            return redirect()->back()->with(['status' => $output]);

        }

        

        $reviewed = $this->transactionUtil->get_review($active_settlement->transaction_date,$active_settlement->transaction_date);

        

            if(!empty($reviewed)){

                $output              = [

                    'success' => 0,

                    'msg'     =>"You can't edit a settlement for an already reviewed date",

                ];

                

                return redirect()->back()->with(['status' => $output]);

            }



        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_location = current(array_keys($business_locations->toArray()));

        if (!empty($active_settlement)) {

            $already_pumps = MeterSale::where('settlement_no', $active_settlement->id)->pluck('pump_id')->toArray();
            if($active_settlement->meter_sales->count()){
                $already_pumps = array_diff($already_pumps, [$active_settlement->meter_sales->toArray()[0]['pump_id']]);
                $already_pumps = array_values($already_pumps);
            }
            $pump_nos = Pump::where('business_id', $business_id)->whereNotIn('id', $already_pumps)->pluck('pump_name', 'id');

        } else {

            $pump_nos = Pump::where('business_id', $business_id)->pluck('pump_name', 'id');

        }



        //other_sale tab

        $stores = Store::forDropdown($business_id, 0,1, 'sell');

        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();

        $fuel_category_id = !empty($fuel_category_id) ? $fuel_category_id->id : null;

        // $items = Product::where('category_id', '!=', $fuel_category_id)->where('business_id', $business_id)->pluck('name', 'id');



        $items = $this->transactionUtil->getProductDropDownArray($business_id, $fuel_category_id,'petro_settlements');





        $payment_meter_sale_total = !empty($active_settlement->meter_sales) ? $active_settlement->meter_sales->sum('discount_amount') :  0.00;

        $payment_other_sale_total = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('sub_total') :  0.00;

        $payment_other_income_total = !empty($active_settlement->other_incomes) ? $active_settlement->other_incomes->sum('sub_total') :  0.00;

        $payment_customer_payment_total = !empty($active_settlement->customer_payments) ? $active_settlement->customer_payments->sum('sub_total') :  0.00;

        $payment_other_sale_discount = !empty($active_settlement->other_sales) ? $active_settlement->other_sales->sum('discount_amount') :  0.00;

        

        $payment_other_sale_total -= $payment_other_sale_discount;



        $wrok_shifts = WorkShift::where('business_id', $business_id)->pluck('shift_name', 'id');

        $bulk_tanks = FuelTank::where('business_id', $business_id)->where('bulk_tank', 1)->pluck('fuel_tank_number', 'id');



        $services = Product::where('business_id', $business_id)->forModule('petro_settlements')->where('enable_stock', 0)->pluck('name', 'id');

        $discount_types = ['fixed' => 'Fixed', 'percentage' => 'Percentage'];

        

        

        $can_edit_details = $this->canEditSettlement($id);

        



        return view('petro::settlement.edit')->with(compact(

            'business_locations',

            'payment_types',

            'services',

            'customers',

            'pump_operators',

            'wrok_shifts',

            'pump_nos',

            'items',

            'settlement_no',

            'default_location',

            'active_settlement',

            'stores',

            'payment_meter_sale_total',

            'payment_other_sale_total',

            'payment_other_income_total',

            'payment_customer_payment_total',

            'bulk_tanks',

            'discount_types',

            'can_edit_details'

        ));

    }

    

    public function canEditSettlement($id){

        $settlement = Settlement::findOrFail($id); 

        $transaction = Transaction::where('invoice_no',$settlement->settlement_no)->where('type','sell')->first();

        

        // see the customer payments marked as paid

        

        // see the loan to customer already paid for by the customer

        $paid_customer_loan = Transaction::where('invoice_no',$settlement->settlement_no)->where('sub_type','customer_loan')->whereIn('payment_status',['partial','paid'])->count();

        

        

        // see the cheques already deposited

        if(!empty($transaction)){

            $deposited_cheques = TransactionPayment::where('transaction_id',$transaction->id)->where('method','cheque')->where('is_deposited',1)->count();

        }

        

        $can_edit = 1;

        $reasons = "";

        

        if($paid_customer_loan > 0 || $deposited_cheques > 0){

            $can_edit = 0;

            

            $reasons .= "<ol>";

            

            if($paid_customer_loan > 0){

                $reasons .= "<li>".__('petro::lang.paid_customer_loan')."</li>";

            }

            

            if($deposited_cheques > 0){

                $reasons .= "<li>".__('petro::lang.deposited_cheque')."</li>";

            }

            

            $reasons .= "</ol>";

            

        }

        

        return [$can_edit,$reasons];

    }



    /**

     * Update the specified resource in storage.

     * @param  Request $request

     * @return Response

     */

    public function update(Request $request, $id)

    {

        try {

            $input = $request->except('_token', '_method');

          

            $settlement = Settlement::find($id);   

            $currentWorkShift = $settlement->work_shift;

            $currentTransactionDate =  $settlement->transaction_date; 

            $note =  $settlement->note;

            

            $operator =  $settlement->pump_operator_id;

            $location =  $settlement->location_id;

            $input['work_shift'] = !empty($request->work_shift)? json_encode($request->work_shift) : json_encode(array());

            $input['transaction_date'] = \Carbon::parse($request->transaction_date)->format('Y-m-d');

            

            

            $business_id = request()->session()->get('business.id');

            $latest_date = DayEnd::where('business_id',$business_id)->get()->last()->day_end_date ?? null;

            if(!empty($latest_date) && strtotime($latest_date) >= strtotime($input['transaction_date'])){

                return ['success' => false,

                                'msg' => __('petro::lang.date_greater_than_day_end')

                            ];

            }

            

            $input['note']=$request->note;

            $input['pump_operator_id']=$request->pump_operator_id;

            Settlement::where('id', $id)->update($input);

             $changedFields ='';

              

           if ($input['work_shift'] !== $currentWorkShift) {

                $changedFields = 'Update Work shift';

            }

            if ($input['transaction_date'] !== $currentTransactionDate) {

                $changedFields = 'Update Transaction Date';

            }

           if ($input['note'] !== $note) {

                $changedFields = 'Update Note';

            } 

          if ($request->pump_operator_id <> $operator) {

                $changedFields = 'Update Pump Operator';

            }

            if ($request->location_id <> $location) {

                $changedFields = 'Update Location';

            }
            
            $assigned_pumps = PumpOperatorAssignment::where('pump_operator_assignments.pump_operator_id', $request->pump_operator_id)
            ->leftJoin('settlements', 'pump_operator_assignments.settlement_id', '=', 'settlements.id')
            ->where(function($query) {
                $query->where('settlements.status', 1)
                    ->orWhereNull('pump_operator_assignments.settlement_id');
            })
            ->select('pump_operator_assignments.*')
            ->get();


            $optionHtml = '';
            foreach ($assigned_pumps as $item) {
                $optionHtml .= '<option value="' . $item['shift_id'] . '">' . $item['shift_number'] . '</option>';
            }

            $output = [
                'success' => true,
            
                // Assuming $changedFields is an array, join it into a string if necessary
                'msg' => __($changedFields),            
                'optionHtml' => $optionHtml
            ];
            


        } catch (\Exception $e) {

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return response()->json($output);
    }



    /**

     * Remove the specified resource from storage.

     * @return Response

     */

    public function destroy($id)

    {

        try {

            DB::beginTransaction();

            $settlement = Settlement::findOrFail($id);

            $this->deletePreviouseTransactions($settlement->id, true);

            $settlement->delete();

            DB::commit();



            $output = [

                'success' => true,

                'msg' => __('petro::lang.settlement_delete_success')

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

     * get details for pump id

     * @return Response

     */

    public function getPumpDetails($pump_id)

    {

        $pump = Pump::where('id', $pump_id)->first();

        $last_meter_reading = $pump->last_meter_reading;

        $last_meter_sale = MeterSale::where('pump_id', $pump_id)->orderBy('id', 'desc')->first();

        if (!empty($last_meter_sale)) {

            $last_meter_reading = !empty($last_meter_sale->meter_reset_value) ? $last_meter_sale->meter_reset_value : $last_meter_sale->closing_meter;

        }

        

        

        $is_open = PumpOperatorAssignment::where('pump_id', $pump_id)->where('status','open')->count();

        

        $ass = PumpOperatorAssignment::where('pump_id', $pump_id)->where('closed_in_settlement',0)->first();

        

        

        

        $po_closing = 0;

        $day_entry = null;

        

        if(!empty($ass)){

            $po_closing = $ass->closing_meter;

            $day_entry = PumperDayEntry::where('pump_id', $pump_id)->where('pumper_assignment_id', $ass->id)->where('closed_in_settlement',0)->first();

        }
        
        print_r($po_closing);
        exit;

        

        

        $fuel_tank = FuelTank::where('id', $pump->fuel_tank_id)->first();



        $product = Variation::leftjoin('products', 'variations.product_id', 'products.id')

            ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')

            ->where('products.id', $fuel_tank->product_id)

            ->select('sku', 'variations.sell_price_inc_tax as default_sell_price', 'products.name', 'products.id', 'variation_location_details.qty_available')->first();



        $current_balance = $this->transactionUtil->getTankBalanceById($pump->fuel_tank_id);



        return [

            'colsing_value' => number_format($last_meter_reading, 3, '.', ''),

            'tank_remaing_qty' => $current_balance,

            'product' => $product,

            'pump_name' => $pump->pump_name,

            'product_id' => $product->id,

            'pump_id' => $pump->id,

            'bulk_sale_meter' => $pump->bulk_sale_meter,

            'po_closing' => $last_meter_reading >= $po_closing ? 0 : $po_closing,

            'po_testing' => $last_meter_reading >= $po_closing ? 0 : (!empty($day_entry) ? $day_entry->testing_ltr : 0),

            

            'assignment_id' => !empty($ass) ? $ass->id : 0,

            'pumper_entry_id' => !empty($day_entry) ? $day_entry->id : 0,

            

            'is_open' => $is_open

        ];

    }



    /**

     * get balance stock of product

     * @param product_id

     * @return Response

     */

    

    public function getPumps($id){

        try {

                $business_id = request()->session()->get('business.id');

                $assigned_pumps = PumpOperatorAssignment::where('pump_operator_id',$id)->where('settlement_id', null)->whereDate('date_and_time', date('Y-m-d'))->pluck('pump_id');

                if(!empty($assigned_pumps) && sizeof($assigned_pumps) > 0){

                    $pumps = Pump::where('business_id', $business_id)->whereIn('id', $assigned_pumps)->pluck('pump_no', 'id');

                }else{

                    $pumps = Pump::where('business_id', $business_id)->pluck('pump_no', 'id');

                }



            $output = [

                'success' => true,

                'pumps' => $pumps,

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

        

        

            

    }

    

    public function getBalanceStock($id)

    {

        try {

            $product = Product::leftjoin('variations', 'products.id', 'variations.product_id')

                ->leftjoin('variation_location_details', 'variations.id', 'variation_location_details.variation_id')

                ->where('products.id', $id)->select('qty_available', 'sell_price_inc_tax', 'products.name', 'sku')->first();



            $output = [

                'success' => true,

                'balance_stock' => $product->qty_available,

                'price' => $product->sell_price_inc_tax,

                'product_name' => $product->name,

                'code' => $product->sku,

                'msg' => __('petro::lang.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }

    /**

     * save meter sale to db

     * @return Response

     */

    public function saveMeterSale(Request $request)

    {

        try {

            $business_id = $request->session()->get('business.id');

            $business_locations = BusinessLocation::forDropdown($business_id);

            $default_location = current(array_keys($business_locations->toArray()));



            DB::beginTransaction();



            $settlement_exist = $this->createSettlementIfNotExist($request);

            

            if(is_int($settlement_exist) && $settlement_exist == 406){

                return ['success' => false,

                            'msg' => __('petro::lang.date_greater_than_day_end')

                        ];

            }



            $pump = Pump::where('id', $request->pump_id)->first();

            $tank_id = $pump->fuel_tank_id;

            $data = array(

                'business_id' => $business_id,

                'settlement_no' => $settlement_exist->id,

                'product_id' => $request->product_id,

                'pump_id' => $request->pump_id,

                'starting_meter' => $request->starting_meter,

                'closing_meter' => $pump->bulk_sale_meter == 0 ? $request->closing_meter : '',

                'price' => $request->price,

                'qty' => $request->qty,

                'discount' => $request->discount,

                'discount_type' => $request->discount_type,

                'discount_amount' => $request->discount_amount,

                'testing_qty' => $request->testing_qty,

                'sub_total' => $request->sub_total

            );



            $meter_sale = MeterSale::create($data);

            

            if(!empty($request->is_from_pumper)){

                logger($request->pumper_entry_id);

                logger($request->assignment_id);

                

                PumperDayEntry::where('id', $request->pumper_entry_id)

                        ->update(['settlement_no' => $request->settlement_no,'settlement_added_by' => auth()->user()->id,'closed_in_settlement' => 1]);

                        

                PumpOperatorAssignment::where('id',$request->assignment_id)->update(array('closed_in_settlement' => 1));

            }

            

            Settlement::where('id',$settlement_exist->id)->update(['is_edit' => request()->is_edit]);

            

            // add pump operator commission

            $pump_operator = PumpOperator::find($settlement_exist->pump_operator_id);

            if(!empty($pump_operator)){

                if(!empty($pump_operator->commission_type) && !empty($pump_operator->commission_ap)){

                    $commission_amount = 0;

                    $discounted_amount = $request->discount_amount;

                    if($pump_operator->commission_type == 'percentage'){

                        $commission_amount = $discounted_amount * $pump_operator->commission_ap / 100;

                    }

                    

                    if($pump_operator->commission_type == 'fixed'){

                        $commission_amount = $request->qty * $pump_operator->commission_ap;

                    }

                    

                    $commission_data = array(

                                            'pump_operator_id' => $settlement_exist->pump_operator_id,

                                            'meter_sale_id' => $meter_sale->id,

                                            'transaction_date' => $settlement_exist->transaction_date,

                                            'amount' => $commission_amount,

                                            'type' => $pump_operator->commission_type,

                                            'value' => $pump_operator->commission_ap

                                        );

                    PumpOperatorCommission::create($commission_data);

                }

            }



            Pump::where('id', $request->pump_id)->update(['starting_meter' => $request->starting_meter, 'last_meter_reading' => $request->closing_meter]);

            

            DB::commit();

            

            $output = [

                'success' => true,

                'msg' => 'success',

                'meter_sale_id' => $meter_sale->id,

                'settlement_id' => $settlement_exist->id

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }



    public function deleteMeterSale($id)

    {

        try {

            $meter_sale = MeterSale::where('id', $id)->first();

            Settlement::where('id',$meter_sale->settlement_no)->update(['is_edit' => request()->is_edit]);

            

            $amount = $meter_sale->discount_amount;

            $starting_meter = $meter_sale->starting_meter;

            $closing_meter = $meter_sale->closing_meter;

            $pump = Pump::where('id', $meter_sale->pump_id)->first();

            $tank_id = $pump->fuel_tank_id;

            FuelTank::where('id', $tank_id)->increment('current_balance', $meter_sale->qty);

            $meter_sale->delete();

            $pump->last_meter_reading = $starting_meter; //reset back to previous starting meter



            $previous_meter_sale = MeterSale::where('pump_id', $pump->id)->orderBy('id', 'desc')->first();

            if (!empty($previous_meter_sale)) {

                $pump->starting_meter = $previous_meter_sale->starting_meter;

            }

            $pump->save();



            $pump_name = $pump->pump_name;

            $pump_id = $pump->id;

            

            // delete pump operator commission

            PumpOperatorCommission::where('meter_sale_id',$id)->delete();



            $output = [

                'success' => true,

                'amount' => $amount,

                'pump_name' => $pump_name,

                'pump_id' => $pump_id,

                'msg' => __('petro::lang.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }



    /**

     * save other sale data in db

     * @param product_id

     * @return Response

     */

    public function saveOtherSale(Request $request)

    {

        try {

            $business_id = $request->session()->get('business.id');



            $settlement_exist = $this->createSettlementIfNotExist($request);

            

            if(is_int($settlement_exist) && $settlement_exist == 406){

                return ['success' => false,

                            'msg' => __('petro::lang.date_greater_than_day_end')

                        ];

            }

            $data = array(

                'business_id' => $business_id,

                'settlement_no' => $settlement_exist->id,

                'store_id' => $request->store_id,

                'product_id' => $request->product_id,

                'price' => $request->price,

                'qty' => $request->qty,

                'balance_stock' => $request->balance_stock,

                'discount' => $request->discount,

                'discount_type' => $request->discount_type,

                'discount_amount' => $request->discount_amount,

                'sub_total' => $request->sub_total

            );

            $other_sale = OtherSale::create($data);

            Settlement::where('id',$settlement_exist->id)->update(['is_edit' => request()->is_edit]);



            $output = [

                'success' => true,

                'other_sale_id' => $other_sale->id,

                'msg' => __('petro::lang.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }



    public function deleteOtherSale($id)

    {

        try {

            $other_sale = OtherSale::where('id', $id)->first();

            Settlement::where('id',$other_sale->settlement_no)->update(['is_edit' => request()->is_edit]);

            $amount = $other_sale->sub_total - $other_sale->discount_amount;

            $other_sale->delete();



            $output = [

                'success' => true,

                'amount' => $amount,

                'msg' => __('petro::lang.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }



    /**

     * save other income data in db

     * @param product_id

     * @return Response

     */

    public function saveOtherIncome(Request $request)

    {

        try {

            $business_id = $request->session()->get('business.id');



            $settlement_exist = $this->createSettlementIfNotExist($request);

            

            if(is_int($settlement_exist) && $settlement_exist == 406){

                return ['success' => false,

                            'msg' => __('petro::lang.date_greater_than_day_end')

                        ];

            }

            $data = array(

                'business_id' => $business_id,

                'settlement_no' => $settlement_exist->id,

                'product_id' => $request->product_id,

                'qty' => $request->qty,

                'price' => $request->price,

                'reason' => $request->other_income_reason,

                'sub_total' => $request->sub_total

            );

            $other_income = OtherIncome::create($data);

            Settlement::where('id',$settlement_exist->id)->update(['is_edit' => request()->is_edit]);



            $output = [

                'success' => true,

                'other_income_id' => $other_income->id,

                'msg' => __('petro::lang.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }



    public function deleteOtherIncome($id)

    {

        try {

            $other_income = OtherIncome::where('id', $id)->first();

            Settlement::where('id',$other_income->settlement_no)->update(['is_edit' => request()->is_edit]);

            $sub_total = $other_income->sub_total;

            $other_income->delete();



            $output = [

                'success' => true,

                'sub_total' => $sub_total,

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

     * save customer payment data in db

     * @param product_id

     * @return Response

     */

    public function saveCustomerPayment(Request $request)

    {

        try {

            $business_id = $request->session()->get('business.id');



            $settlement_exist = $this->createSettlementIfNotExist($request);

            

            if(is_int($settlement_exist) && $settlement_exist == 406){

                return ['success' => false,

                            'msg' => __('petro::lang.date_greater_than_day_end')

                        ];

            }

            $data = array(

                'business_id' => $business_id,

                'settlement_no' => $settlement_exist->id,

                'customer_id' => $request->customer_id,

                'payment_method' => $request->payment_method,

                'cheque_date' => !empty($request->cheque_date) ? \Carbon::parse($request->cheque_date)->format('Y-m-d') : null,

                'cheque_number' => $request->cheque_number,

                'bank_name' => $request->bank_name,

                'amount' => $request->amount,

                'sub_total' => $request->sub_total,

                'post_dated_cheque' => $request->post_dated_cheque

            );

            DB::beginTransaction();

            $customer_payment = CustomerPayment::create($data);

            Settlement::where('id',$settlement_exist->id)->update(['is_edit' => request()->is_edit]);



            



            if ($request->payment_method == 'cash') {

                $cash_data = array(

                    'business_id' => $business_id,

                    'settlement_no' => $settlement_exist->id,

                    'amount' => $request->amount,

                    'customer_id' => $request->customer_id,

                    'customer_payment_id' => $customer_payment->id

                );



                $settlement_cash_payment = SettlementCashPayment::create($cash_data);

            }

            if ($request->payment_method == 'card') {

                $card_data = array(

                    'business_id' => $business_id,

                    'settlement_no' => $settlement_exist->id,

                    'amount' => $request->amount,

                    'card_type' => $request->card_type,

                    'card_number' => $request->card_number,

                    'customer_id' => $request->customer_id,

                    'customer_payment_id' => $customer_payment->id

                );



                $settlement_card_payment = SettlementCardPayment::create($card_data);

            }

            if ($request->payment_method == 'cheque') {

                $cheque_data = array(

                    'business_id' => $business_id,

                    'settlement_no' => $settlement_exist->id,

                    'amount' => $request->amount,

                    'bank_name' => $request->bank_name,

                    'cheque_number' => $request->cheque_number,

                    'cheque_date' => !empty($request->cheque_date) ? \Carbon::parse($request->cheque_date)->format('Y-m-d') : null,

                    'customer_id' => $request->customer_id,

                    'customer_payment_id' => $customer_payment->id

                );



                $settlement_cheque_payment = SettlementChequePayment::create($cheque_data);

            }

            DB::commit();

            $output = [

                'success' => true,

                'customer_payment_id' => $customer_payment->id,

                'msg' => __('petro::lang.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }



    public function deleteCustomerPayment($id)

    {

        try {

            $customer_payment = CustomerPayment::where('id', $id)->first();

            Settlement::where('id',$customer_payment->settlement_no)->update(['is_edit' => request()->is_edit]);

            $amount = $customer_payment->amount;

            $customer_payment->delete();

            SettlementCashPayment::where('customer_payment_id', $id)->delete();

            SettlementCardPayment::where('customer_payment_id', $id)->delete();

            SettlementChequePayment::where('customer_payment_id', $id)->delete();



            $output = [

                'success' => true,

                'amount' =>  $amount,

                'msg' => __('petro::lang.success')

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }



    public function createSettlementIfNotExist(Request $request)

    {

        $business_id = $request->session()->get('business.id');

        $settlement_data = array(

            'settlement_no' => $request->settlement_no,

            'business_id' => $business_id,

            'transaction_date' => \Carbon::parse($request->transaction_date)->format('Y-m-d'),

            'location_id' => $request->location_id,

            'pump_operator_id' => $request->pump_operator_id,

            'work_shift' => !empty($request->work_shift) ? $request->work_shift : [],

            'note' => $request->note,

            'status' => 1

        );

        

        $latest_date = DayEnd::where('business_id',$business_id)->get()->last()->day_end_date ?? null;

        if(!empty($latest_date) && strtotime($latest_date) >= strtotime($settlement_data['transaction_date'])){

            return 406;

        }

        

        

        $settlement_exist = Settlement::where('settlement_no', $request->settlement_no)->where('business_id', $business_id)->first();

        if (empty($settlement_exist)) {

            $settlement_exist = Settlement::create($settlement_data);

        }



        return $settlement_exist;

    }



    /**

     * print resources

     * @param settlement_id

     * @return Response

     */

    public function print($id)

    {

        $business_id = request()->session()->get('business.id');

        $business_locations = BusinessLocation::forDropdown($business_id);

        $default_location = current(array_keys($business_locations->toArray()));



        $settlement = Settlement::where('settlements.id', $id)->where('settlements.business_id', $business_id)

            ->leftjoin('pump_operators', 'settlements.pump_operator_id', 'pump_operators.id')

            ->with([

                'meter_sales',

                'other_sales',

                'other_incomes',

                'customer_payments',

                'cash_payments',

                'card_payments',

                'cheque_payments',

                'credit_sale_payments',

                'expense_payments',

                'excess_payments',

                'shortage_payments',

                'loan_payments',

                'drawings_payments',

                'customer_loans'

            ])

            ->select('settlements.*', 'pump_operators.name as pump_operator_name')

            ->first();



        $business = Business::where('id', $settlement->business_id)->first();

        $pump_operator = PumpOperator::where('id', $settlement->pump_operator_id)->first();



        //this for only to show in print page customer payments which entered in customer payments tab

        $customer_payments_tab = CustomerPayment::leftjoin('contacts', 'customer_payments.customer_id', 'contacts.id')

            ->where('customer_payments.settlement_no', $settlement->settlement_no)

            ->where('customer_payments.business_id', $business_id)

            ->select('customer_payments.*', 'contacts.name as customer_name')

            ->get();

        $total_daily_collection = floatval(DailyCollection::where('pump_operator_id', $settlement->pump_operator_id)->where('business_id', $business->id)->where('settlement_id', $settlement->id)->sum('current_amount'));



        return view('petro::settlement.print')->with(compact('settlement', 'business', 'pump_operator', 'customer_payments_tab', 'total_daily_collection'));

    }



    // Added by Muneeb Ahmad for Store Dropdown

    public function getStoresById(Request $request)

    {

        $business_id = $request->session()->get('user.business_id');

        $account_type = null;

        $stores = '<option value="">Please Select</option>';



        $stores = Store::where('business_id', $business_id);

        if($request->location_id){

            $stores = $stores->where('location_id', $request->location_id);

        }

        $stores = $stores->pluck('name', 'id');

        return $this->transactionUtil->createDropdownHtml($stores, 'Please Select');

    }



    public function getProductsByStoreId(Request $request)

    {

        $business_id = $request->session()->get('user.business_id');

        $location_id = $request->location_id;

        $store_id = $request->store_id;

        $tab = $request->tab?? 0;
        
        // dump($tab);exit;
        

        

        $fuel_category_id = Category::where('business_id', $business_id)->where('name', 'Fuel')->first();

        $fuel_category_id = !empty($fuel_category_id) ? $fuel_category_id->id : null;

            

        if($store_id){

            return $this->transactionUtil->getProductsByStoreId($business_id, $location_id, $store_id,$tab,$fuel_category_id,'petro_settlements');

        }else{

            $products = [];

            return $this->transactionUtil->createDropdownHtml($products, 'No Item Found');

        }

    }



public function getBalanceStockById(Request $request, $id)
{
    try {
        $product = Product::join('variations', 'products.id', '=', 'variations.product_id')
            ->leftJoin('variation_location_details', function($join) use ($id, $request) {
                $join->on('variations.id', '=', 'variation_location_details.variation_id')
                    ->where('variation_location_details.product_id', '=', $id)
                    ->where('variation_location_details.location_id', '=', $request->location_id);
            })
            ->leftJoin('variation_store_details', function($join) use ($request) {
                $join->on('variations.id', '=', 'variation_store_details.variation_id')
                    ->where('variation_store_details.store_id', '=', $request->store_id);
            })
            ->where('products.id', $id)
            ->select(
                DB::raw('COALESCE(variation_store_details.qty_available, 0) as qty_available'),
                DB::raw('COALESCE(sell_price_inc_tax, 0) as sell_price_inc_tax'),
                'products.name',
                'products.sku'
            )
            ->first();

        $output = [
            'success' => true,
            'balance_stock' => $product->qty_available,
            'price' => $product->sell_price_inc_tax,
            'product_name' => $product->name,
            'code' => $product->sku,
            'msg' => __('petro::lang.success')
        ];
    } catch (\Exception $e) {
        \Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
        $output = [
            'success' => false,
            'msg' => __('messages.something_went_wrong')
        ];
    }

    return $output;
}


    public function getMeterSaleForm($id)
    {
        $output = [

            'success' => false,

            'msg' => __('messages.something_went_wrong')

        ];
        try {
          $meter_sale = MeterSale::where('id', $id)->first();
          if($meter_sale){
            $active_settlement = Settlement::where('id',$meter_sale->settlement_no)->first();
            $discount_types = ['fixed' => 'Fixed', 'percentage' => 'Percentage'];
            $already_pumps = MeterSale::where('settlement_no', $active_settlement->id)->pluck('pump_id')->toArray();
            $business_id = $meter_sale->business_id;
            if(request()->action_type=='cancel'){
                $meter_sale = [];
            }else{
                $already_pumps = array_diff($already_pumps, [$meter_sale->pump_id]);
                $already_pumps = array_values($already_pumps);
                $meter_sale = $meter_sale->toArray();
            }
            $pump_nos = Pump::where('business_id', $business_id)->whereNotIn('id', $already_pumps)->pluck('pump_name', 'id');
            $html = view('petro::settlement.partials.meter_sale_form', compact('meter_sale', 'pump_nos', 'discount_types'))->render();

            $output = [

                'success' => true,

                'html' => $html,

                'msg' => __('petro::lang.success')

            ];
          }
        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,


            ];

        }



        return $output;

    }

    public function updateSettlementMeterSale($id, Request $request)

    {

        try {
            $meter_sale = MeterSale::where('id', $id)->first();

            //Settlement::where('id',$meter_sale->settlement_no)->update(['is_edit' => $request->is_edit]);

            $pumpOld = Pump::where('id', $meter_sale->pump_id)->first();
            
            $tank_id_old = $pumpOld->fuel_tank_id;
            FuelTank::where('id', $tank_id_old)->increment('current_balance', $meter_sale->qty);

            $amount = $meter_sale->discount_amount;

            $starting_meter_old = $meter_sale->starting_meter;

            $closing_meter_old = $meter_sale->closing_meter;

            $pumpOld->last_meter_reading = $starting_meter_old; //reset back to previous starting meter

            $previous_meter_sale = MeterSale::where('pump_id', $pumpOld->id)->orderBy('id', 'desc')->first();

            if (!empty($previous_meter_sale)) {

                $pumpOld->starting_meter = $previous_meter_sale->starting_meter;

            }

            $pumpOld->save();


            $pumpNew = Pump::where('id', $request->pump_id)->first();
            $tank_id_new = $pumpNew->fuel_tank_id;
            FuelTank::where('id', $tank_id_new)->decrement('current_balance', $request->qty);

            $pumpNew->starting_meter = $request->starting_meter;
            $pumpNew->last_meter_reading = $request->closing_meter;
            $pumpNew->save();

            $data = array(

                'pump_id' => $pumpNew->id,

                'starting_meter' => $request->starting_meter,

                'closing_meter' => $pumpNew->bulk_sale_meter == 0 ? $request->closing_meter : '',

                'price' => $request->price,

                'qty' => $request->qty,

                'discount' => $request->discount,

                'discount_type' => $request->discount_type,

                'discount_amount' => $request->discount_amount,

                'testing_qty' => $request->testing_qty,

                'sub_total' => $request->sub_total

            );

            MeterSale::where('id', $id)->update($data);

            $settlement_exist = Settlement::where('id',$meter_sale->settlement_no)->first();
            $pump_operator = PumpOperator::find($settlement_exist->pump_operator_id);

            if(!empty($pump_operator)){

                if(!empty($pump_operator->commission_type) && !empty($pump_operator->commission_ap)){

                    $commission_amount = 0;

                    $discounted_amount = $request->discount_amount;

                    if($pump_operator->commission_type == 'percentage'){

                        $commission_amount = $discounted_amount * $pump_operator->commission_ap / 100;

                    }

                    

                    if($pump_operator->commission_type == 'fixed'){

                        $commission_amount = $request->qty * $pump_operator->commission_ap;

                    }

                    

                    $commission_data = array(
                                            'transaction_date' => $settlement_exist->transaction_date,

                                            'amount' => $commission_amount,

                                            'type' => $pump_operator->commission_type,

                                            'value' => $pump_operator->commission_ap

                                        );

                    PumpOperatorCommission::where(['pump_operator_id' => $settlement_exist->pump_operator_id,'meter_sale_id' => $meter_sale->id])
                                            ->update($commission_data);

                }

            }



            $output = [

                'success' => true,

                'msg' => 'success',

                'meter_sale_id' => $meter_sale->id,

                'settlement_id' => $settlement_exist->id,

                'amount' => $request->discount_amount,

            ];

        } catch (\Exception $e) {

            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());

            $output = [

                'success' => false,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return $output;

    }

}

