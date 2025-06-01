<?php

namespace Modules\Fleet\Http\Controllers;

use App\ContactLedger;
use App\Account;
use App\AccountType;
use App\AccountTransaction;
use App\BusinessLocation;
use App\Contact;
use App\Product;
use App\Transaction;
use App\TransactionPayment;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Modules\Fleet\Entities\Driver;

use Modules\Fleet\Entities\TripCategory;

use Modules\Fleet\Entities\Fleet;
use Modules\Fleet\Entities\Helper;
use Modules\Fleet\Entities\Route;
use Modules\Fleet\Entities\RouteOperation;
use Modules\Fleet\Entities\RouteProduct;
use Modules\Fleet\Entities\FleetContactLedger;
use Modules\Fleet\Entities\FleetAccountNumber;
use Modules\Fleet\Entities\FleetLogo;
use Modules\Fleet\Entities\OriginalLocation;
use Modules\Petro\Entities\SettlementExpensePayment;
use Modules\Fleet\Entities\FleetInvoice;
use Modules\Fleet\Entities\FleetInvoiceDetail;

use Yajra\DataTables\Facades\DataTables;
use App\ExpenseCategory;
use App\User;
use App\TaxRate;

class RouteOperationController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;
    protected $businessUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil, BusinessUtil $businessUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->businessUtil =  $businessUtil;

        $this->dummyPaymentLine = [
            'method' => 'cash', 'amount' => 0, 'note' => '', 'card_transaction_number' => '', 'card_number' => '', 'card_type' => '', 'card_holder_name' => '', 'card_month' => '', 'card_year' => '', 'card_security' => '', 'cheque_number' => '', 'cheque_date' => '', 'bank_account_number' => '',
            'is_return' => 0, 'transaction_no' => '', 'account_id' => ''
        ];
    }


    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.id', 'route_operations.transaction_id')
                ->leftjoin('business_locations', 'route_operations.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('drivers', 'route_operations.driver_id', 'drivers.id')
                ->leftjoin('trip_categories', 'route_operations.amt_method', 'trip_categories.id')
                ->leftjoin('helpers', 'route_operations.helper_id', 'helpers.id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('fleet_account_numbers','fleet_account_numbers.id', 'routes.delivered_to_acc')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('transactions.type', 'route_operation')
                ->where('route_operations.business_id', $business_id)
                ->select([
                    'route_operations.*',
                    'fleet_account_numbers.delivered_to_acc_no',
                    'trip_categories.amount_method',
                    'drivers.driver_name',
                    'helpers.helper_name',
                    'routes.route_name',
                    'fleets.vehicle_number',
                    'contacts.name as contact_name',
                    'transactions.id as t_id',
                    'transactions.payment_status',
                    'transaction_payments.method',
                    'transaction_payments.account_id',
                    'business_locations.name as location_name',
                ])->groupBy('route_operations.id');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->location_id)) {
                $route_operations->where('route_operations.location_id', request()->location_id);
            }
            if (!empty(request()->contact_id)) {
                $route_operations->where('route_operations.contact_id', request()->contact_id);
            }
            if (!empty(request()->route_id)) {
                $route_operations->where('route_operations.route_id', request()->route_id);
            }
            if (!empty(request()->vehicle_no)) {
                $route_operations->where('fleets.vehicle_number', request()->vehicle_no);
            }
            if (!empty(request()->driver_id)) {
                $route_operations->where('route_operations.driver_id', request()->driver_id);
            }
            if (!empty(request()->helper_id)) {
                $route_operations->where('route_operations.helper_id', request()->helper_id);
            }
            if (!empty(request()->payment_status)) {
                $route_operations->where('transactions.payment_status', request()->payment_status);
            }
            if (!empty(request()->payment_method)) {
                $route_operations->where('transaction_payments.method', request()->payment_method);
            }

            return DataTables::of($route_operations)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@edit', [$row->t_id]) . '" class=""><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@destroy', [$row->t_id]) . '" class="delete-fleet"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        if (auth()->user()->can('fleet.add_actual_meter')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@actualmeter', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="fa fa-tachometer"></i> ' . __("fleet::lang.actual_meter") . '</a></li>';
                        }
                            
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@RO_Advance', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="fa fa-money"></i> ' . __("fleet::lang.add_advance") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@RO_Salary', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="fa fa-money"></i> ' . __("fleet::lang.add_salary") . '</a></li>';
                        
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@addExpense', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"> ' . __("fleet::lang.add_expense") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@viewExpense', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"> ' . __("fleet::lang.view_expense") . '</a></li>';
                        
                        
                        if ($row->payment_status != 'paid') {
                            $html .= '<li class="divider"></li>';
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->t_id]) . '" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i>' . __("purchase.add_payment") . '</a></li>';
                        }
                        
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@getPayments', [$row->t_id]) . '" ><i class="fa fa-money" aria-hidden="true"></i> ' . __("fleet::lang.trip_payments") . '</a></li>';
                        
                        return $html;
                    }
                )
                
                ->editColumn('amount_method',function($row){
                    if($row->amount_method == 'km_distance_qty'){
                        return "(Per km Rate) * (Distance) * (Quantity)";
                    }elseif($row->amount_method == 'km_distance'){
                        return "(Per km Rate) * (Distance)";
                    }
                })
                
                ->editColumn('date_of_operation', '{{@format_date($date_of_operation)}}')
                ->editColumn('amount', '{{@num_format($amount)}}')
                ->editColumn('starting_meter', '{{@num_format($starting_meter)}}')
                ->editColumn('ending_meter', '{{@num_format($ending_meter)}}')
                ->editColumn('driver_incentive', '{{@num_format($driver_incentive)}}')
                ->editColumn('helper_incentive', '{{@num_format($helper_incentive)}}')
                ->editColumn('distance', '{{@num_format($distance)}}')
                ->editColumn('payment_status', function ($row) {
                    $payment_status = Transaction::getPaymentStatus($row);
                    return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->t_id, 'for_purchase' => true]);
                })
                ->editColumn('contact_name',function($row){
                    $html = $row->contact_name;
                    
                    if(!empty($row->delivered_to_acc_no)){
                        $html .= "<span class='badge bg-success delivered-to-acc-no' data-string='".$row->delivered_to_acc_no."'>".__('fleet::lang.delivered_to_acc_no')."</span>";
                    }
                    
                    return $html;
                })
                ->editColumn('balance_due', function ($row) {
                    $transaction = Transaction::where('id', $row->t_id)->where('business_id', request()->session()->get('user.business_id'))->first();
                    $paid_amount = $this->transactionUtil->getTotalPaid($row->t_id);
                    $amount = $transaction->final_total - $paid_amount;
                    
                    if ($amount < 0) {
                        $amount = 0;
                    }
                    
                    return $amount;
                })
                ->editColumn('method', function ($row) {
                    $html = '';
                    
                    if ($row->payment_status == 'due') {
                        return 'Credit Sale';
                    }
                    
                    
                    $mthds = TransactionPayment::where('transaction_id',$row->t_id)->get();
                    
                    foreach($mthds as $mthd){
                        if ($mthd->method == 'bank_transfer' || $mthd->method == 'bank') {
                            $html .= ucfirst($mthd->method)."<br>";
                            $bank_acccount = Account::find($mthd->account_id);
                            if (!empty($bank_acccount)) {
                                $html .= '<b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                                $html .= '<b>Account Number:</b> ' . $bank_acccount->account_number . '</br>';
                            }
                        } else {
                            $html .= ucfirst($mthd->method)."<br>";
                        }
                    }
                    
                    

                    return $html;
                })
                ->editColumn('invoice_no',function($row){
                    $html = $row->invoice_no;
                    if(!empty($row->is_vat)){
                        $html .= "&nbsp; <span class='badge bg-info'>".__('fleet::lang.vat').'</span>';
                    }
                    
                    return $html;
                })
                ->editColumn('meter_difference', function ($row) {
                    if($row->actual_meter){
                        return $this->commonUtil->num_f($row->actual_meter-$row->ending_meter);
                    }else{
                        return 0;
                    }
                })
                ->editColumn('qty', function ($row) {
                    $amounts = "";
                    if(!empty($row->qty)){
                        $qty_array = json_decode($row->qty) ;
                        if (is_array($qty_array)) {
                            foreach ($qty_array as $key => $one) {
                                $amounts .= !empty($one) ? number_format( (float) $one,0,'.',','): "";
                                if($key != sizeof($qty_array) - 1){
                                    $amounts .= " + "; 
                                }
                            }
                        } else {
                            $amounts .=  !empty($qty_array) ? number_format($qty_array,0,'.',','): "";
                        }
                    }
                        
                    return $amounts;
                })
                
                ->addColumn('product_name', function ($row) {
                    $amounts = "";
                    if(!empty($row->product_id)){
                        $prod_array = json_decode($row->product_id);
                        
                        if (is_array($prod_array)) {
                            foreach ($prod_array as $key => $one) {
                                $product = RouteProduct::where('id',$one)->first();
                                if(!empty($product)){
                                   $amounts .= $product->name;
                                   if($key != sizeof($prod_array) - 1 ){
                                       $amounts .= " + ";
                                   }
                                }
                            }
                        } else {
                            $product = RouteProduct::where('id',$prod_array)->first();
                            if(!empty($product)){
                                   $amounts .= $product->name;
                            }
                        }
                        
                    }
                        
                    return $amounts;
                })

                // ->removeColumn('id')
                ->rawColumns(['action', 'payment_status', 'method','invoice_no','contact_name'])
                ->make(true);
        }


        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $fleets_query = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets_query->pluck('vehicle_number', 'vehicle_number');
        $vehicle_types = $fleets_query->pluck('vehicle_type', 'vehicle_type');
        $vehicle_brands = $fleets_query->pluck('vehicle_brand', 'vehicle_brand');
        $vehicle_models = $fleets_query->pluck('vehicle_model', 'vehicle_model');
        $contacts = Contact::where('business_id', $business_id)->pluck('name', 'id');
        $products = RouteProduct::where('business_id', $business_id)->pluck('name', 'id');
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id');
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id');
        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        $fleets = Fleet::where('business_id', $business_id)->pluck('code_for_vehicle', 'id');
        $payment_status = ['partial' => 'Partial', 'due' => 'Due', 'paid' => 'Paid'];
         $payment_methods = $this->productUtil->payment_types(null,false, false, false, false,"is_sale_enabled");

        return view('fleet::route_operations.index')->with(compact(
            'business_locations',
            'vehicle_numbers',
            'contacts',
            'products',
            'drivers',
            'helpers',
            'routes',
            'fleets',
            'payment_status',
            'payment_methods'
        ));
    }
    
    public function index_create()
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.id', 'route_operations.transaction_id')
                ->leftjoin('business_locations', 'route_operations.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('drivers', 'route_operations.driver_id', 'drivers.id')
                ->leftjoin('trip_categories', 'route_operations.amt_method', 'trip_categories.id')
                ->leftjoin('helpers', 'route_operations.helper_id', 'helpers.id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('fleet_account_numbers','fleet_account_numbers.id', 'routes.delivered_to_acc')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('transactions.type', 'route_operation')
                ->where('route_operations.business_id', $business_id)
                ->select([
                    'route_operations.*',
                    'fleet_account_numbers.delivered_to_acc_no',
                    'trip_categories.name as category_name',
                    'drivers.driver_name',
                    'helpers.helper_name',
                    'routes.route_name',
                    'fleets.vehicle_number',
                    'contacts.name as contact_name',
                    'transactions.id as t_id',
                    'transactions.payment_status',
                    'transaction_payments.method',
                    'transaction_payments.account_id',
                    'business_locations.name as location_name',
                ])->groupBy('route_operations.id');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            if (!empty(request()->location_id)) {
                $route_operations->where('route_operations.location_id', request()->location_id);
            }
            
            if (!empty(request()->trip_category)) {
                $route_operations->where('route_operations.amt_method', request()->trip_category);
            }
            
            if (!empty(request()->contact_id)) {
                $route_operations->where('route_operations.contact_id', request()->contact_id);
            }
            if (!empty(request()->route_id)) {
                $route_operations->where('route_operations.route_id', request()->route_id);
            }
            if (!empty(request()->vehicle_no)) {
                $route_operations->where('fleets.vehicle_number', request()->vehicle_no);
            }
            if (!empty(request()->driver_id)) {
                $route_operations->where('route_operations.driver_id', request()->driver_id);
            }
            if (!empty(request()->helper_id)) {
                $route_operations->where('route_operations.helper_id', request()->helper_id);
            }
            if (!empty(request()->payment_status)) {
                $route_operations->where('transactions.payment_status', request()->payment_status);
            }
            if (!empty(request()->payment_method)) {
                $route_operations->where('transaction_payments.method', request()->payment_method);
            }

            return DataTables::of($route_operations)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@edit', [$row->t_id]) . '" class=""><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@destroy', [$row->t_id]) . '" class="delete-fleet"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        // if ($row->is_updated_actual_meter == 0) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@actualmeter', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="fa fa-tachometer"></i> ' . __("fleet::lang.actual_meter") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@RO_Advance', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="fa fa-money"></i> ' . __("fleet::lang.add_advance") . '</a></li>';
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@RO_Salary', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="fa fa-money"></i> ' . __("fleet::lang.add_salary") . '</a></li>';
                        
                        
                        // }
                        if ($row->payment_status != 'paid') {
                            $html .= '<li class="divider"></li>';
                            $html .= '<li><a href="' . action('TransactionPaymentController@addPayment', [$row->t_id]) . '" class="add_payment_modal"><i class="fa fa-money" aria-hidden="true"></i>' . __("purchase.add_payment") . '</a></li>';
                        }
                        
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@getRO_Advance', [$row->t_id]) . '" ><i class="fa fa-money" aria-hidden="true"></i> ' . __("fleet::lang.view_advance_payments") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@getSal_Advance', [$row->t_id]) . '" ><i class="fa fa-money" aria-hidden="true"></i> ' . __("fleet::lang.view_salary_payments") . '</a></li>';
                        
                        
                        return $html;
                    }
                )
                ->editColumn('date_of_operation', '{{@format_date($date_of_operation)}}')
                ->editColumn('amount', '{{@num_format($amount)}}')
                ->editColumn('starting_meter', '{{@num_format($starting_meter)}}')
                ->editColumn('ending_meter', '{{@num_format($ending_meter)}}')
                ->editColumn('driver_incentive', '{{@num_format($driver_incentive)}}')
                ->editColumn('helper_incentive', '{{@num_format($helper_incentive)}}')
                ->editColumn('distance', '{{@num_format($distance)}}')
                ->editColumn('payment_status', function ($row) {
                    $payment_status = Transaction::getPaymentStatus($row);
                    return (string) view('sell.partials.payment_status', ['payment_status' => $payment_status, 'id' => $row->id, 'for_purchase' => true]);
                })
                ->editColumn('balance_due', function ($row) {
                    $transaction = Transaction::where('id', $row->t_id)->where('business_id', request()->session()->get('user.business_id'))->first();
                    $paid_amount = $this->transactionUtil->getTotalPaid($row->t_id);
                    $amount = $transaction->final_total - $paid_amount;
                    
                    if ($amount < 0) {
                        $amount = 0;
                    }
                    
                    return $amount;
                })
                ->editColumn('method', function ($row) {
                    $html = '';
                    if ($row->payment_status == 'due') {
                        return 'Credit Sale';
                    }
                    if ($row->method == 'bank_transfer') {
                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                            $html .= '<b>Account Number:</b> ' . $bank_acccount->account_number . '</br>';
                        }
                    } else {
                        $html .= ucfirst($row->method);
                    }

                    return $html;
                })
                ->editColumn('meter_difference', function ($row) {
                    if($row->actual_meter){
                        return $this->commonUtil->num_f($row->actual_meter-$row->ending_meter);
                    }else{
                        return 0;
                    }
                })
                ->editColumn('qty', function ($row) {
                    $amounts = "";
                    if(!empty($row->qty)){
                        $qty_array = json_decode($row->qty) ;
                        if (is_array($qty_array)) {
                            
                            foreach ($qty_array as $key => $one) {
                                $amounts .=  number_format((int)$one,0,'.',',');
                                if($key != sizeof($qty_array) - 1 ){
                                   $amounts .= " + ";
                                }
                            }
                        } else {
                            $amounts .=  number_format((int)$qty_array,0,'.',',');
                        }
                    }
                        
                    return $amounts;
                })
                
                ->addColumn('product_name', function ($row) {
                    $amounts = "";
                    if(!empty($row->product_id)){
                        $prod_array = json_decode($row->product_id);
                        
                        if (is_array($prod_array)) {
                            foreach ($prod_array as $key => $one) {
                                $product = RouteProduct::where('id',$one)->first();
                                if(!empty($product)){
                                   $amounts .= $product->name;
                                   if($key != sizeof($prod_array) -1){
                                       $amounts .= " + ";
                                   }
                                }
                            }
                        } else {
                            $product = RouteProduct::where('id',$prod_array)->first();
                            if(!empty($product)){
                                   $amounts .= $product->name;
                            }
                        }
                        
                    }
                        
                    return $amounts;
                })

                // ->removeColumn('id')
                ->rawColumns(['action', 'payment_status', 'method'])
                ->make(true);
        }


        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $fleets_query = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets_query->pluck('vehicle_number', 'vehicle_number');
        $vehicle_types = $fleets_query->pluck('vehicle_type', 'vehicle_type');
        $vehicle_brands = $fleets_query->pluck('vehicle_brand', 'vehicle_brand');
        $vehicle_models = $fleets_query->pluck('vehicle_model', 'vehicle_model');
        $contacts = Contact::where('business_id', $business_id)->pluck('name', 'id');
        $products = RouteProduct::where('business_id', $business_id)->pluck('name', 'id');
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id');
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id');
        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        $original_locations = Route::where('business_id', $business_id)->pluck('orignal_location', 'id');
        
        $s_original_locations = OriginalLocation::where('business_id', $business_id)->pluck('name', 'id');
        
        $fleets = Fleet::where('business_id', $business_id)->pluck('code_for_vehicle', 'id');
        $payment_status = ['partial' => 'Partial', 'due' => 'Due', 'paid' => 'Paid'];
        $payment_methods = $this->productUtil->payment_types(null,false, false, false, false,"is_sale_enabled");
        $invoice_name = FleetAccountNumber::where('business_id',$business_id)->pluck('invoice_name','id');
        $logos = FleetLogo::where('business_id',$business_id)->pluck('image_name','id');
        
        $trip_categories = TripCategory::where('business_id',$business_id)->pluck('name','id');

        return view('fleet::fleet_invoices.index')->with(compact(
            'business_locations',
            'vehicle_numbers',
            'contacts',
            'products',
            'drivers',
            'helpers',
            'routes',
            'fleets',
            'payment_status',
            'payment_methods',
            'invoice_name','logos','original_locations','s_original_locations','trip_categories'
        ));
    }
    
    public function list_invoices()
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $route_operations = FleetInvoice::leftjoin('contacts', 'contacts.id', 'fleet_invoices.customer_id')
                ->leftjoin('fleet_account_numbers', 'fleet_account_numbers.id', 'fleet_invoices.invoice_name')
                ->where('fleet_invoices.business_id', $business_id)
                ->select([
                    'fleet_invoices.*',
                    'contacts.name as customer',
                    'fleet_account_numbers.invoice_name as invoice_name'
                ]);

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('fleet_invoices.print_date', '>=', request()->start_date);
                $route_operations->whereDate('fleet_invoices.print_date', '<=', request()->end_date);
            }
            if (!empty(request()->invoice_name)) {
                $route_operations->where('fleet_invoices.invoice_name', request()->invoice_name);
            }
            
            

            return DataTables::of($route_operations)
                ->addColumn(
                    'action',
                    function ($row) {
                        $html = '<div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">' .
                            __("messages.actions") .
                            '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                        $html .= '<li><a href="#" class="print-invoice" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@printInvoice', [$row->id]) . '"><i class="fa fa-print" aria-hidden="true"></i>' .  __("messages.print") . '</a></li>';
    
                        
                        return $html;
                    }
                )
                ->addColumn(
                    'date_range', function($row){
                        return $this->commonUtil->format_date($row->date_from) ." - ".$this->commonUtil->format_date($row->date_to);
                    }
                )
                
                ->addColumn(
                    'invoice_nos',
                    function($row){
                        return '<a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\RouteOperationController@list_invoices_numbers', [$row->id]) . '" class="btn-modal" data-container=".view_modal">'.__('fleet::lang.click_view').'</a>';
                    }
                )
                
                ->editColumn('print_date', '{{@format_date($print_date)}}')
                
                ->rawColumns(['action', 'payment_status', 'method','invoice_nos'])
                ->make(true);
        }
    }
    
    public function list_invoices_numbers($id)
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        
        $invoices  = FleetInvoiceDetail::where('invoice_id',$id)->get();
        
        return view('fleet::fleet_invoices.invoice_nos')->with(compact(
            'business_id','id','invoices'
        ));
    }
    
    public function fetch_profit_loss_summary(Request $request){
        $business_id = request()->session()->get('business.id');
        
        $route_operations = Transaction::leftjoin('route_operations', 'transactions.id', 'route_operations.transaction_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->where('route_operations.business_id', $business_id)
                ->where('transactions.type','route_operation')
                ->select([
                    'route_operations.amount'
                ])->groupBy('route_operations.id');
                
            $advances = Transaction::leftjoin('transactions as trans', 'transactions.invoice_no', 'trans.id')
                ->leftjoin('route_operations', 'trans.id', 'route_operations.transaction_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->where('route_operations.business_id', $business_id)
                ->whereIn('transactions.type',['ro_advance','ro_salary'])
                ->select([
                    'transactions.final_total as expense'
                ])->groupBy('route_operations.id');
                
                
            $expenses = Transaction::join('route_operations', 'transactions.parent_transaction_id', 'route_operations.id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->where('route_operations.business_id', $business_id)
                ->where('transactions.type','expense')
                ->whereNotNull('transactions.parent_transaction_id')
                ->select([
                    'transactions.final_total as expense'
                ])->groupBy('route_operations.id');
                
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
                
                $expenses->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $expenses->whereDate('transactions.transaction_date', '<=', request()->end_date);
                
                $advances->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $advances->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            
            
            if (!empty(request()->vehicle_no)) {
                
                $route_operations->where('fleets.vehicle_number', request()->vehicle_no);
                
                $expenses->where('fleets.vehicle_number', request()->vehicle_no);
                
                $advances->where('fleets.vehicle_number', request()->vehicle_no);
            }
            
            if (!empty(request()->invoice_no)) {
                
                $route_operations->where('route_operations.invoice_no', request()->invoice_no);
                
                $expenses->where('route_operations.invoice_no', request()->invoice_no);
                
                $advances->where('route_operations.invoice_no', request()->invoice_no);
            }
            
            
            $income = $route_operations->get()->sum('amount');
            $expenses_tot = $expenses->get()->sum('expense');
            $advances_tot = $advances->get()->sum('expense');
            $tot_expe = ($expenses_tot + $advances_tot);
            
            $bf = $this->fetchPLBF($request);
            
            $profit = $bf + $income - $tot_expe;
            
            if($profit < 0){
                $prof = "(".$this->commonUtil->num_f(abs($profit)).")";
            }else{
                $prof = $this->commonUtil->num_f($profit);
            }
            
            $output = array('income' => $this->commonUtil->num_f($income),'expenses' => $this->commonUtil->num_f($tot_expe),'bf' => $this->commonUtil->num_f($bf),'profit' => $prof);
            
            return response()->json($output);
            
    }
    
    function fetchPLBF($request){
        $business_id = request()->session()->get('business.id');
        
        $route_operations = Transaction::leftjoin('route_operations', 'transactions.id', 'route_operations.transaction_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->where('route_operations.business_id', $business_id)
                ->where('transactions.type','route_operation')
                ->select([
                    'route_operations.amount'
                ])->groupBy('route_operations.id');
                
            $advances = Transaction::leftjoin('transactions as trans', 'transactions.invoice_no', 'trans.id')
                ->leftjoin('route_operations', 'trans.id', 'route_operations.transaction_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->where('route_operations.business_id', $business_id)
                ->whereIn('transactions.type',['ro_advance','ro_salary'])
                ->select([
                    'transactions.final_total as expense'
                ])->groupBy('route_operations.id');
                
                
            $expenses = Transaction::join('route_operations', 'transactions.parent_transaction_id', 'route_operations.id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->where('route_operations.business_id', $business_id)
                ->where('transactions.type','expense')
                ->whereNotNull('transactions.parent_transaction_id')
                ->select([
                    'transactions.final_total as expense'
                ])->groupBy('route_operations.id');
                
            
            
            $route_operations->whereDate('transactions.transaction_date', '<', $request->start_date);
            
            $expenses->whereDate('transactions.transaction_date', '<', $request->start_date);
            
            $advances->whereDate('transactions.transaction_date', '<', $request->start_date);
           
            
            
            if (!empty(request()->vehicle_no)) {
                
                $route_operations->where('fleets.vehicle_number', $request->vehicle_no);
                
                $expenses->where('fleets.vehicle_number', $request->vehicle_no);
                
                $advances->where('fleets.vehicle_number', $request->vehicle_no);
            }
            
            if (!empty(request()->invoice_no)) {
                
                $route_operations->where('route_operations.invoice_no', $request->invoice_no);
                
                $expenses->where('route_operations.invoice_no', $request->invoice_no);
                
                $advances->where('route_operations.invoice_no', $request->invoice_no);
            }
            
            
            $income = $route_operations->get()->sum('amount');
            $expenses_tot = $expenses->get()->sum('expense');
            $advances_tot = $advances->get()->sum('expense');
            $tot_expe = ($expenses_tot + $advances_tot);
            
            return $income - $tot_expe;
    }
    
    public function fleet_profit_loss(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            
            $vehicle_no = $request->vehicle_no;
            
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.id', 'route_operations.transaction_id')
                ->leftJoin('drivers','drivers.id','route_operations.driver_id')
                ->leftJoin('helpers','helpers.id','route_operations.helper_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftjoin('business_locations', 'route_operations.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('route_operations.business_id', $business_id)
                ->where('transactions.type','route_operation')
                ->select([
                    'transactions.type',
                    'transactions.sub_type',
                    'transactions.ref_no',
                    'transactions.expense_category_id',
                    'helpers.helper_name',
                    'drivers.driver_name',
                    'route_operations.date_of_operation',
                    'route_operations.amount',
                    'route_operations.invoice_no',
                    'route_operations.order_number',
                    'fleets.vehicle_number',
                    'contacts.name as customer_name',
                    DB::raw('0 as expense'),
                ])->groupBy('route_operations.id');
                
            $advances = Transaction::leftjoin('transactions as trans', 'transactions.invoice_no', 'trans.id')
                ->leftjoin('route_operations', 'trans.id', 'route_operations.transaction_id')
                ->leftJoin('drivers','drivers.id','route_operations.driver_id')
                ->leftJoin('helpers','helpers.id','route_operations.helper_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftjoin('business_locations', 'route_operations.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('route_operations.business_id', $business_id)
                ->whereIn('transactions.type',['ro_advance','ro_salary'])
                ->select([
                    'transactions.type',
                    'transactions.sub_type',
                    'transactions.ref_no',
                    'transactions.expense_category_id',
                    'helpers.helper_name',
                    'drivers.driver_name',
                    'transactions.transaction_date as date_of_operation',
                    DB::raw('0 as amount'),
                    'route_operations.invoice_no',
                    'route_operations.order_number',
                    'fleets.vehicle_number',
                    'contacts.name as customer_name',
                    'transactions.final_total as expense'
                ])->groupBy('route_operations.id');
                
                
            $expenses = Transaction::join('route_operations', 'transactions.parent_transaction_id', 'route_operations.id')
                ->leftJoin('drivers','drivers.id','route_operations.driver_id')
                ->leftJoin('helpers','helpers.id','route_operations.helper_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftjoin('business_locations', 'route_operations.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('route_operations.business_id', $business_id)
                ->where('transactions.type','expense')
                ->whereNotNull('transactions.parent_transaction_id')
                ->select([
                    'transactions.type',
                    'transactions.sub_type',
                    'transactions.ref_no',
                    'transactions.expense_category_id',
                    'helpers.helper_name',
                    'drivers.driver_name',
                    'transactions.transaction_date as date_of_operation',
                    DB::raw('0 as amount'),
                    'route_operations.invoice_no',
                    'route_operations.order_number',
                    'fleets.vehicle_number',
                    'contacts.name as customer_name',
                    'transactions.final_total as expense'
                ])->groupBy('route_operations.id');
                
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
                
                $expenses->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $expenses->whereDate('transactions.transaction_date', '<=', request()->end_date);
                
                $advances->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $advances->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            
            
            if (!empty(request()->vehicle_no)) {
                
                $route_operations->where('fleets.vehicle_number', request()->vehicle_no);
                
                $expenses->where('fleets.vehicle_number', request()->vehicle_no);
                
                $advances->where('fleets.vehicle_number', request()->vehicle_no);
            }
            
            if (!empty(request()->invoice_no)) {
                
                $route_operations->where('route_operations.invoice_no', request()->invoice_no);
                
                $expenses->where('route_operations.invoice_no', request()->invoice_no);
                
                $advances->where('route_operations.invoice_no', request()->invoice_no);
            }
            
            
            if (!empty(request()->type)) {
                if(request()->type == 'expense'){
                    $combined = $advances->union($expenses);
                }else{
                    $combined = $route_operations;
                }
            }else{
                $combined = $route_operations->union($advances)->union($expenses);
            }
            
                
            
            

            $combined->orderBy('date_of_operation', 'asc');
            
            
            
            
            $results = $combined->get();
            
            $bal = $this->fetchPLBF($request);
                
                

            return DataTables::of($results)
                ->editColumn('invoice_no', function ($row) {
                    if ($row->type == "expense") {
                        return $row->ref_no;
                    }
                    return $row->invoice_no;
                })
                ->addColumn('description', function ($row) {
                    if($row->amount > 0){
                        $customer_name = $row->customer_name;
                        $order_no = $row->order_number;
                        return "<b>Customer:</b> $customer_name <br> <b>Order No:</b> $order_no";
                    }
                    
                    if($row->expense > 0){
                        if($row->type == 'ro_advance'){
                            if($row->sub_type == 'driver'){
                                return "<b>".$row->driver_name." advance</b>";
                            }elseif($row->sub_type == 'helper'){
                                return "<b>".$row->helper_name." advance</b>";
                            }
                        }elseif($row->type == 'ro_salary'){
                            if($row->sub_type == 'driver'){
                                return "<b>".$row->driver_name." salary</b>";
                            }elseif($row->sub_type == 'helper'){
                                return "<b>".$row->helper_name." salary</b>";
                            }
                        }elseif($row->type == 'expense'){
                            $expense = ExpenseCategory::find($row->expense_category_id);
                            $cat = "";
                            if(!empty($expense)){
                                $cat = $expense->name;
                            }
                            $trip_no = $row->invoice_no;
                            $order_no = $row->order_number;
                            return "<b>Expense</b><br>.$cat <br> <b>Trip Operation No:</b> $trip_no <br> <b>Order No:</b> $order_no";
                        }
                    }
                    
                })
                ->editColumn('date_of_operation', '{{@format_date($date_of_operation)}}')
                ->editColumn('amount', function($row){
                    if($row->amount > 0){
                        return $this->commonUtil->num_f($row->amount);
                    }
                })
                ->addColumn('expense', function ($row) {
                    if($row->expense > 0){
                        return $this->commonUtil->num_f($row->expense);
                    }
                    
                })
                ->addColumn('balance', function ($row) use (&$bal) {
                    $bal += $row->amount - $row->expense;
                    return $this->commonUtil->num_f($bal);
                })
                ->rawColumns(['action', 'description', 'method', 'expense', 'balance'])
                ->make(true);
        }

        $fleets_query = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets_query->pluck('vehicle_number', 'vehicle_number');
        $refs = RouteOperation::where('business_id',$business_id)->distinct('invoice_no')->select('invoice_no')->pluck('invoice_no','invoice_no');

        return view('fleet::fleet.profit_loss_account')->with(compact(
            'vehicle_numbers',
            'refs'
        ));
    }
    
    
    public function printInvoice($id){
        if (request()->ajax()) {
            try {
                $output = [
                    'success' => 0,
                    'msg'     => trans("messages.something_went_wrong"),
                ];
                $business_id = request()->session()->get('business.id');
                
                
                $fleet_invoice = FleetInvoice::findOrFail($id);
                $fleet_acc = FleetAccountNumber::findOrFail($fleet_invoice->invoice_name);
                
                if($fleet_invoice->type == "dynamic"){
                    $route = Route::find($fleet_invoice->original_location);
                }else{
                    $route = OriginalLocation::find($fleet_invoice->original_location);
                    $route->orignal_location = $route->name;
                }
                
                
               
                $location_details = BusinessLocation::find($fleet_invoice->location_id);
                $il = $this->businessUtil->invoiceLayout($business_id, $fleet_invoice->location_id, $location_details->invoice_layout_id);
                
                $business_details = $this->businessUtil->getDetails($business_id);
                
                $fleetlogo = FleetLogo::find($fleet_invoice->logo);
                
                
                $temp = [];
                $rec = [];
                // if ($il->show_landmark == 1) {
                //     $rec['address'] = $location_details->landmark . "\n";
                // }
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
                    $rec['address'] = implode(',', $temp);
                }
                
                $rec['city'] = $location_details->city;
                
                $logo = !empty($fleetlogo) ? url($fleetlogo->logo) : false;
                $fleet_items = FleetInvoiceDetail::where('invoice_id',$id)->get();
                
                $output_receipt = [];
                $receipt_details = [];
                
                $output_receipt['html_content'] = view('fleet::fleet_invoices.receipt', compact('route','il','fleet_items','logo','rec','fleet_invoice','fleet_acc','business_details','location_details'))->render();
                
                if (!empty($output_receipt)) {
                    $output = ['success' => 1, 'receipt' => $output_receipt];
                }
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
                $output = [
                    'success' => 0,
                    'msg'     => trans("messages.something_went_wrong"),
                ];
            }
            return $output;
        }
    }
    
    
    public function insert_fleetInvoice(Request $request){
        try {
            $business_id = request()->session()->get('business.id');
            $user_id = $request->session()->get('user.id');
            
            $data = $request->all();
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $location_id = $request->location_id;
            $customer_id = $request->customer_id;
            $invoice_name = $request->invoice_name;
            $logo = $request->logo;
            $original_location = $request->original_location;
            $type = $request->type;
            $print_date = date('Y-m-d');
            
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.id', 'route_operations.transaction_id')
                ->leftjoin('business_locations', 'route_operations.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('drivers', 'route_operations.driver_id', 'drivers.id')
                ->leftjoin('helpers', 'route_operations.helper_id', 'helpers.id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('transactions.type', 'route_operation')
                ->where('route_operations.business_id', $business_id)
                ->select([
                    'route_operations.*',
                    'drivers.driver_name',
                    'helpers.helper_name',
                    'routes.route_name',
                    'fleets.vehicle_number',
                    'contacts.name as contact_name',
                    'transactions.id as t_id',
                    'transactions.payment_status',
                    'transaction_payments.method',
                    'transaction_payments.account_id',
                    'business_locations.name as location_name',
                ])->groupBy('route_operations.id');

            $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
            $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
            $route_operations->where('route_operations.location_id', $location_id);
            $route_operations->where('route_operations.contact_id', $customer_id);
            $route_operations->where('fleets.vehicle_number', request()->vehicle_no);
            
            
            $invoice_data = ["type" => $type,"original_location" => $original_location, "business_id" => $business_id,"customer_id" => $customer_id,"location_id" => $location_id,"invoice_name" => $invoice_name,"print_date" => $print_date,"date_from" => $start_date,"date_to" => $end_date,"added_by" => $user_id,'logo' => $logo];
            
            
            if ($route_operations->count() === 0) {
                DB::rollBack();
                $output = [
                    'success' => false,
                    'msg' => __('fleet::lang.no_invoices')
                ];
                return response()->json($output);
            }
            
            
            
            $fleet_invoice = FleetInvoice::create($invoice_data);
            foreach($route_operations->get() as $one){
                    $amounts = "";
                    if(!empty($one->qty)){
                        $qty_array = json_decode($one->qty) ;
                        if (is_array($qty_array)) {
                            foreach ($qty_array as $qt) {
                                $amounts .=  $this->commonUtil->num_f((int)$qt).",";
                            }
                        } else {
                            $amounts .=  $this->commonUtil->num_f((int)$qty_array);
                        }
                    }
                    
                    $names = "";
                    if(!empty($one->product_id)){
                        $prod_array = json_decode($one->product_id);
                        if (is_array($prod_array)) {
                            foreach ($prod_array as $nm) {
                                $product = RouteProduct::where('id',$nm)->first();
                                if(!empty($product)){
                                   $names .= $product->name .",";
                                }
                            }
                        } else {
                            $product = RouteProduct::where('id',$prod_array)->first();
                            if(!empty($product)){
                                   $names .= $product->name .",";
                            }
                        }
                        
                    }
                    
                    $html = '';
                    if ($one->payment_status == 'due') {
                        $html .= 'Credit Sale';
                    }
                    if ($one->method == 'bank_transfer') {
                        $bank_acccount = Account::find($one->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<b>Bank Name:</b> ' . $bank_acccount->name . '</br>';
                            $html .= '<b>Account Number:</b> ' . $bank_acccount->account_number . '</br>';
                        }
                    } else {
                        $html .= ucfirst($one->method);
                    }
                
                $fl_detail = ["business_id" => $business_id, "invoice_id" => $fleet_invoice->id, "date" => $one->date_of_operation,
                "location" => $location_id, "invoice_no" => $one->order_number, "product" => $names,"qty" => $amounts,
                "vehicle_number" => $one->vehicle_number, "mileage" => $one->distance, "invoice_amount" => $one->amount, "payment_details" => $html];
                
                FleetInvoiceDetail::create($fl_detail);
            }
            
            $output = [
                'success' => true,
                'msg' => __('fleet::lang.success')
            ];
            
        } catch (\Exception $e) {
            logger($e);
            DB::rollBack();
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
         return response()->json($output);
    }
    
    public function milage_changes(Request $request)
    {
        {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            
            $vehicle_no = $request->vehicle_no;
            
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.id', 'route_operations.transaction_id')
                ->leftJoin('drivers','drivers.id','route_operations.driver_id')
                ->leftJoin('helpers','helpers.id','route_operations.helper_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftjoin('business_locations', 'route_operations.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.actual_meter_added_by', 'users.id')
                ->where('route_operations.actual_meter_added_by','>', 0)
                ->where('route_operations.business_id', $business_id)
                ->where('transactions.type','route_operation')
                ->select([
                    'transactions.type',
                    'transactions.sub_type',
                    'transactions.expense_category_id',
                    'helpers.helper_name',
                    'drivers.driver_name',
                    'route_operations.date_of_operation',
                    'route_operations.ending_meter',
                    'route_operations.actual_meter_added_on',
                    'route_operations.amount',
                    'route_operations.invoice_no',
                    'route_operations.order_number',
                    'route_operations.actual_meter',
                    'users.first_name as names',
                    'fleets.vehicle_number',
                    'contacts.name as customer_name',
                ])->orderBy('date_of_operation', 'asc');
                
            
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            
            
            if (!empty(request()->route_operations)) {
                $route_operations->where('route_operations.invoice_no', request()->route_operations);
            }
            
            if (!empty(request()->route)) {
                $route_operations->where('route_operations.route_id', request()->route);
            }
            
            if (!empty(request()->milage_status)) {
                if(request()->milage_status == 'lower'){
                    $route_operations->where('route_operations.actual_meter', '<','route_operations.ending_meter');
                }else{
                    $route_operations->where('route_operations.actual_meter', '>','route_operations.ending_meter');
                }
                
            }
            
            
            return DataTables::of($route_operations)
                ->addColumn('description', function ($row) {
                    if($row->amount > 0){
                        $customer_name = $row->customer_name;
                        $order_no = $row->order_number;
                        return "<b>Customer:</b> $customer_name <br> <b>Order No:</b> $order_no";
                    }
                    
                    
                })
                
                ->addColumn('milage_status',function($row){
                    if($row->actual_meter > $row->ending_meter){
                        return "Higher";
                    }elseif($row->actual_meter < $row->ending_meter){
                        return "Lower";
                    }else{
                        return "Equal";
                    }
                })
                
                ->addColumn('changed_distance',function($row){
                    return $row->actual_meter - $row->ending_meter;
                })
                
                ->editColumn('date_of_operation', '{{@format_date($date_of_operation)}}')
                ->editColumn('actual_meter_added_on', '{{@format_date($actual_meter_added_on)}}')
                ->rawColumns(['action', 'description', 'method'])
                ->make(true);
        }

        $fleets_query = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets_query->pluck('vehicle_number', 'vehicle_number');
        $vehicle_numbers = $fleets_query->pluck('vehicle_number', 'vehicle_number');
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id');
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id');
        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        $route_operations = RouteOperation::where('business_id', $business_id)->select('invoice_no')->distinct()->pluck('invoice_no', 'invoice_no');
        $fleets = Fleet::where('business_id', $business_id)->pluck('code_for_vehicle', 'id');
        return view('fleet::fleet.milage_changes')->with(compact(
            'vehicle_numbers',
            'drivers',
            'routes',
            'helpers',
            'route_operations'
        ));
    }
    }
    
    /**
     * get all payment for the trip opration
     *
     * Task - 2 DOC 6323 (Nafmkd)
     */
    public function getPayments($id) 
    {
       $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        $payment_for = ['ro_salary' => __('fleet::lang.salary'), 'ro_advance' => __('fleet::lang.advance')];
        if (request()->ajax()) {
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.invoice_no', 'route_operations.transaction_id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftJoin('helpers', function ($join) {
                    $join->on('helpers.id', '=', 'transactions.contact_id')
                        ->where('transactions.sub_type', '=', 'helper');
                })
                ->leftJoin('drivers', function ($join) {
                    $join->on('drivers.id', '=', 'transactions.contact_id')
                        ->where('transactions.sub_type', '=', 'driver');
                })
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                // ->where('transactions.type', 'ro_salary')
                ->where('route_operations.business_id', $business_id)
                ->where('route_operations.transaction_id',$id)
                ->select([
                    'transactions.*',
                    'users.username',
                    DB::raw('CASE WHEN drivers.driver_name IS NOT NULL THEN drivers.driver_name ELSE helpers.helper_name END AS staff_name'),
                    'routes.route_name',
                    'fleets.vehicle_number',
                    'transaction_payments.method',
                    'transaction_payments.cheque_number',
                    'transaction_payments.cheque_date',
                ])->groupBy('transactions.id');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            
            if (!empty(request()->payment_for)) {
                $route_operations->where('transactions.type', request()->payment_for);
            }
            
            

            return DataTables::of($route_operations)
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('final_total', '{{@num_format($final_total)}}')
                
                ->editColumn('method', function ($row) {
                    
                   $html = ucfirst($row->method);
                    if($row->method == 'cheque' || $row->method == 'bank_transfer'){
                        $html .= "<br><b>Bank</b>:".$row->bank_name."<br><b>Cheque</b>:".$row->cheque_number."<br><b>Cheque Date</b>:".$row->cheque_date;
                    }
                    return $html;
                })
                ->editColumn('type', function($row) use ($payment_for){
                    return $payment_for[$row->type];
                })
                
                // ->removeColumn('id')
                ->rawColumns(['action', 'payment_status', 'method'])
                ->make(true);
        }


        $fleets_query = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets_query->pluck('vehicle_number', 'vehicle_number');
        
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id')->toArray();
        $formatted = [];
        foreach($drivers as $key=>$one){
            $formatted["driver_".$key] = $one;
        }
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id')->toArray();
        foreach($helpers as $key=>$one){
            $formatted["helper_".$key] = $one;
        }
        $staff = $formatted;
        
        return view('fleet::route_operations.route_payments')->with(compact(
            'vehicle_numbers',
            'staff',
            'id',
            'payment_for'
        )); 
    }
    
    public function getRO_Advance($id)
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.invoice_no', 'route_operations.transaction_id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftJoin('helpers', function ($join) {
                    $join->on('helpers.id', '=', 'transactions.contact_id')
                        ->where('transactions.sub_type', '=', 'helper');
                })
                ->leftJoin('drivers', function ($join) {
                    $join->on('drivers.id', '=', 'transactions.contact_id')
                        ->where('transactions.sub_type', '=', 'driver');
                })
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('transactions.type', 'ro_advance')
                ->where('route_operations.business_id', $business_id)
                ->where('route_operations.transaction_id',$id)
                ->select([
                    'transactions.*',
                    DB::raw('CASE WHEN drivers.driver_name IS NOT NULL THEN drivers.driver_name ELSE helpers.helper_name END AS staff_name'),
                    'routes.route_name',
                    'fleets.vehicle_number',
                    'transaction_payments.method',
                    'transaction_payments.cheque_number',
                    'transaction_payments.cheque_date',
                ])->groupBy('transactions.id');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            
            if (!empty(request()->staff_id)) {
                $route_operations->where('transactions.contact_id', explode('_',request()->staff_id)[1]);
                $route_operations->where('transactions.sub_type', explode('_',request()->staff_id)[0]);
            }
            if (!empty(request()->route_id)) {
                $route_operations->where('route_operations.route_id', request()->route_id);
            }
            if (!empty(request()->vehicle_no)) {
                $route_operations->where('fleets.vehicle_number', request()->vehicle_no);
            }
            
            

            return DataTables::of($route_operations)
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('final_total', '{{@num_format($final_total)}}')
                
                ->editColumn('method', function ($row) {
                    
                   $html = ucfirst($row->method);
                    if($row->method == 'cheque' || $row->method == 'bank_transfer'){
                        $html .= "<br><b>Bank</b>:".$row->bank_name."<br><b>Cheque</b>:".$row->cheque_number."<br><b>Cheque Date</b>:".$row->cheque_date;
                    }
                    return $html;
                })
                
                
                // ->removeColumn('id')
                ->rawColumns(['action', 'payment_status', 'method'])
                ->make(true);
        }


        $fleets_query = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets_query->pluck('vehicle_number', 'vehicle_number');
        
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id')->toArray();
        $formatted = [];
        foreach($drivers as $key=>$one){
            $formatted["driver_".$key] = $one;
        }
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id')->toArray();
        foreach($helpers as $key=>$one){
            $formatted["helper_".$key] = $one;
        }
        $staff = $formatted;
        
        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        
        return view('fleet::route_operations.route_advance_payments')->with(compact(
            'vehicle_numbers',
            'staff',
            'routes',
            'id'
        ));
    }
    
     public function getSal_Advance($id)
    {
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $route_operations = Transaction::leftjoin('route_operations', 'transactions.invoice_no', 'route_operations.transaction_id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('fleets', 'route_operations.fleet_id', 'fleets.id')
                ->leftJoin('helpers', function ($join) {
                    $join->on('helpers.id', '=', 'transactions.contact_id')
                        ->where('transactions.sub_type', '=', 'helper');
                })
                ->leftJoin('drivers', function ($join) {
                    $join->on('drivers.id', '=', 'transactions.contact_id')
                        ->where('transactions.sub_type', '=', 'driver');
                })
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('users', 'route_operations.created_by', 'users.id')
                ->where('transactions.type', 'ro_salary')
                ->where('route_operations.business_id', $business_id)
                ->where('route_operations.transaction_id',$id)
                ->select([
                    'transactions.*',
                    DB::raw('CASE WHEN drivers.driver_name IS NOT NULL THEN drivers.driver_name ELSE helpers.helper_name END AS staff_name'),
                    'routes.route_name',
                    'fleets.vehicle_number',
                    'transaction_payments.method',
                    'transaction_payments.cheque_number',
                    'transaction_payments.cheque_date',
                ])->groupBy('transactions.id');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $route_operations->whereDate('transactions.transaction_date', '>=', request()->start_date);
                $route_operations->whereDate('transactions.transaction_date', '<=', request()->end_date);
            }
            
            if (!empty(request()->staff_id)) {
                $route_operations->where('transactions.contact_id', explode('_',request()->staff_id)[1]);
                $route_operations->where('transactions.sub_type', explode('_',request()->staff_id)[0]);
            }
            if (!empty(request()->route_id)) {
                $route_operations->where('route_operations.route_id', request()->route_id);
            }
            if (!empty(request()->vehicle_no)) {
                $route_operations->where('fleets.vehicle_number', request()->vehicle_no);
            }
            
            

            return DataTables::of($route_operations)
                ->editColumn('transaction_date', '{{@format_date($transaction_date)}}')
                ->editColumn('final_total', '{{@num_format($final_total)}}')
                
                ->editColumn('method', function ($row) {
                    
                   $html = ucfirst($row->method);
                    if($row->method == 'cheque' || $row->method == 'bank_transfer'){
                        $html .= "<br><b>Bank</b>:".$row->bank_name."<br><b>Cheque</b>:".$row->cheque_number."<br><b>Cheque Date</b>:".$row->cheque_date;
                    }
                    return $html;
                })
                
                
                // ->removeColumn('id')
                ->rawColumns(['action', 'payment_status', 'method'])
                ->make(true);
        }


        $fleets_query = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets_query->pluck('vehicle_number', 'vehicle_number');
        
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id')->toArray();
        $formatted = [];
        foreach($drivers as $key=>$one){
            $formatted["driver_".$key] = $one;
        }
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id')->toArray();
        foreach($helpers as $key=>$one){
            $formatted["helper_".$key] = $one;
        }
        $staff = $formatted;
        
        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        
        return view('fleet::route_operations.route_salary_payments')->with(compact(
            'vehicle_numbers',
            'staff',
            'routes',
            'id'
        ));
    }
    
    public function RO_Advance($id){
        $business_id = request()->session()->get('business.id');
        $route_operation = RouteOperation::findOrFail($id);
        $driver = Driver::findOrFail($route_operation->driver_id);
        $helper = Helper::findOrFail($route_operation->helper_id);
        $expense_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);
        $fleet = Fleet::findOrFail($route_operation->fleet_id);
        $payment_line = $this->dummyPaymentLine;
        $first_location = BusinessLocation::where('business_id', $business_id)->first();
        $payment_types = $this->transactionUtil->payment_types($first_location->id, true, false, true, false, true,"is_expense_enabled");
        
        $driver_exp_category = ExpenseCategory::find($driver->advance_expense_category);
        $helper_exp_category = ExpenseCategory::find($helper->advance_expense_category);
        
        $driver_acc_name = !empty($driver_exp_category) ? Account::findOrFail($driver_exp_category->expense_account)->name : null;
        $helper_acc_name = !empty($helper_exp_category) ? Account::findOrFail($helper_exp_category->expense_account)->name : null;
        
        
        $driver_acc_id = !empty($driver_exp_category) ? Account::findOrFail($driver_exp_category->expense_account)->id : null;
        $helper_acc_id = !empty($helper_exp_category) ? Account::findOrFail($helper_exp_category->expense_account)->id : null;
        
        
        $ref_count = $this->transactionUtil->onlyGetReferenceCount('expense', null, false);
        $ref_no = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);
        
        $staff = [
            'driver_'.$driver->id => $driver->driver_name,
            'helper_'.$helper->id => $helper->helper_name
        ];
        
        $driver_bal = Transaction::where('contact_id',$driver->id)->where('sub_type','driver')->where('invoice_no',$route_operation->transaction_id)->whereIn('type',['ro_advance','ro_salary'])->sum('final_total');
        $helper_bal = Transaction::where('contact_id',$helper->id)->where('sub_type','helper')->where('invoice_no',$route_operation->transaction_id)->whereIn('type',['ro_advance','ro_salary'])->sum('final_total');
        
        $amounts = json_encode([
            'driver_'.$driver->id => $route_operation->driver_incentive-$driver_bal,
            'helper_'.$helper->id => $route_operation->helper_incentive-$helper_bal,
            'paid_driver_'.$driver->id => $driver_bal,
            'paid_helper_'.$helper->id => $helper_bal,
            'salary_driver_'.$driver->id => $route_operation->driver_incentive,
            'salary_helper_'.$helper->id => $route_operation->helper_incentive,
            
            'cat_id_driver_'.$driver->id => $driver->advance_expense_category,
            'cat_id_helper_'.$helper->id => $helper->advance_expense_category,
            
            'cat_name_driver_'.$driver->id => !empty($driver_exp_category) ? $driver_exp_category->name : "",
            'cat_name_helper_'.$helper->id => !empty($helper_exp_category) ? $helper_exp_category->name : "",
            
            'acc_id_driver_'.$driver->id => $driver_acc_id,
            'acc_id_helper_'.$helper->id => $helper_acc_id,
            
            'acc_name_driver_'.$driver->id => $driver_acc_name,
            'acc_name_helper_'.$helper->id => $helper_acc_name
            
        ]);
        
        unset($payment_types['credit_sale']);
        
        $expense_categories = ExpenseCategory::leftjoin('accounts','accounts.id','expense_categories.expense_account')->where('expense_categories.business_id', $business_id)->select('accounts.name as expense_account_name','expense_categories.*')->get();
            
        $fleets = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');
        $ro = RouteOperation::findOrFail($id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_type_id)->pluck('name', 'id');
        return view('fleet::route_operations.advance')->with(compact('payment_types','first_location',
                'payment_line','business_locations','ro','fleets','expense_categories','ref_no','route_operation','staff','expense_accounts','fleet','business_id','amounts'));

    }
    
    public function viewExpense($id){
        return view('fleet::route_operations.view_expense')->with(compact('id'));
    }
    
    public function RO_Salary($id){
        $business_id = request()->session()->get('business.id');
        $route_operation = RouteOperation::findOrFail($id);
        $driver = Driver::findOrFail($route_operation->driver_id);
        $helper = Helper::findOrFail($route_operation->helper_id);
        $expense_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);
        $fleet = Fleet::findOrFail($route_operation->fleet_id);
        $payment_line = $this->dummyPaymentLine;
        $first_location = BusinessLocation::where('business_id', $business_id)->first();
        $payment_types = $this->transactionUtil->payment_types($first_location->id, true, false, true, false, true,"is_expense_enabled");
        
        $driver_exp_category = ExpenseCategory::find($driver->advance_expense_category);
        $helper_exp_category = ExpenseCategory::find($helper->advance_expense_category);
        
        $driver_acc_name = !empty($driver_exp_category) ? Account::findOrFail($driver_exp_category->expense_account)->name : null;
        $helper_acc_name = !empty($helper_exp_category) ? Account::findOrFail($helper_exp_category->expense_account)->name : null;
        
        
        $driver_acc_id = !empty($driver_exp_category) ? Account::findOrFail($driver_exp_category->expense_account)->id : null;
        $helper_acc_id = !empty($helper_exp_category) ? Account::findOrFail($helper_exp_category->expense_account)->id : null;
        
        
        $ref_count = $this->transactionUtil->onlyGetReferenceCount('expense', null, false);
        $ref_no = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);
        
        $staff = [
            'driver_'.$driver->id => $driver->driver_name,
            'helper_'.$helper->id => $helper->helper_name
        ];
        
        $driver_bal = Transaction::where('contact_id',$driver->id)->where('sub_type','driver')->where('invoice_no',$route_operation->transaction_id)->whereIn('type',['ro_advance','ro_salary'])->sum('final_total');
        $helper_bal = Transaction::where('contact_id',$helper->id)->where('sub_type','helper')->where('invoice_no',$route_operation->transaction_id)->whereIn('type',['ro_advance','ro_salary'])->sum('final_total');
        
        $amounts = json_encode([
            'driver_'.$driver->id => $route_operation->driver_incentive-$driver_bal,
            'helper_'.$helper->id => $route_operation->helper_incentive-$helper_bal,
            'paid_driver_'.$driver->id => $driver_bal,
            'paid_helper_'.$helper->id => $helper_bal,
            'salary_driver_'.$driver->id => $route_operation->driver_incentive,
            'salary_helper_'.$helper->id => $route_operation->helper_incentive,
            
            'cat_id_driver_'.$driver->id => $driver->advance_expense_category,
            'cat_id_helper_'.$helper->id => $helper->advance_expense_category,
            
            'cat_name_driver_'.$driver->id => !empty($driver_exp_category) ? $driver_exp_category->name : "",
            'cat_name_helper_'.$helper->id => !empty($helper_exp_category) ? $helper_exp_category->name : "",
            
            'acc_id_driver_'.$driver->id => $driver_acc_id,
            'acc_id_helper_'.$helper->id => $helper_acc_id,
            
            'acc_name_driver_'.$driver->id => $driver_acc_name,
            'acc_name_helper_'.$helper->id => $helper_acc_name
        ]);
        
        unset($payment_types['credit_sale']);
        
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
            
        $fleets = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');
        $ro = RouteOperation::findOrFail($id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        
        $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_type_id)->pluck('name', 'id');
        return view('fleet::route_operations.salary')->with(compact('payment_types','first_location',
                'payment_line','business_locations','ro','fleets','expense_categories','ref_no','route_operation','staff','expense_accounts','fleet','business_id','amounts'));

    }
    
    
    public function postRO_Advance(Request $request){
        try {
            $business_id = request()->session()->get('business.id');
            
            $transaction_data = $request->only(['ref_no', 'transaction_date', 'location_id', 'final_total', 'expense_for', 'fleet_id', 'additional_notes', 'expense_category_id', 'tax_id', 'contact_id']);
            
            $transaction_data['business_id'] = $business_id;
            $transaction_data['contact_id'] = explode("_",$request->advance_for)[1];
            $transaction_data['total_before_tax'] = $request->advance_amount;
            $transaction_data['total_after_tax'] = $request->advance_amount;
            $transaction_data['final_total'] = $request->advance_amount;
            $user_id = $request->session()->get('user.id');
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'ro_advance';
            $transaction_data['sub_type'] = explode("_",$request->advance_for)[0];
            $transaction_data['payment_status'] = 'paid';
            $date_of_operation = $this->commonUtil->uf_date($request->transaction_date);
            $transaction_data['transaction_date'] = $date_of_operation;
            
            $transaction_data['expense_account'] = $request->expense_account;
            $transaction_data['parent_transaction_id'] = $request->ro_id;
            
            $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');

            //Generate reference number

            if (empty($transaction_data['ref_no'])) {

                $transaction_data['ref_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);

            }
            
            
            $transaction_data['invoice_no'] = $request->route_operation_id; 
            
            $transaction = Transaction::create($transaction_data);
            
            $tp_data = [
                'transaction_id' => $transaction->id,
                'business_id' => $business_id,
                'amount' => $request->advance_amount,
                'method' => $request->method,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'created_by' => $user_id,
            ];
            
            $transaction_payment = TransactionPayment::create($tp_data);
            
            $credit_data = [
                'amount' => $request->advance_amount,
                'account_id' => $request->account_id,
                'transaction_id' => $transaction->id,
                'type' => 'credit',
                'sub_type' => null,
                'operation_date' => $transaction_data['transaction_date'],
                'created_by' => session()->get('user.id'),
                'transaction_payment_id' => $transaction_payment->id,
                'note' => null,
                'attachment' => null
            ];
            
            $driver_ledger_data = [
                'contact_id' => explode("_",$request->advance_for)[1],
                'amount' => $this->commonUtil->num_uf($request->advance_amount),
                'type' => "credit",
                'sub_type' =>  explode("_",$request->advance_for)[0],
                'operation_date' => $date_of_operation ? $date_of_operation : \Carbon::now(),
                'created_by' => $user_id,
                'transaction_id' => $transaction_data['invoice_no'] ? $transaction_data['invoice_no'] : null
            ];
            FleetContactLedger::createContactLedger($driver_ledger_data);
                            
            $credit = AccountTransaction::createAccountTransaction($credit_data);
            
            $credit_data['type'] = 'debit';
            $credit_data['account_id'] = $request->expense_account;
            $credit_data['transaction_payment_id'] = null;
            
            $credit = AccountTransaction::createAccountTransaction($credit_data);
            
            $output = [
                'success' => true,
                'msg' => __('fleet::lang.success')
            ];
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect()->back()->with('status', $output);
    }
    
    public function postSal_Advance(Request $request){
        try {
            $business_id = request()->session()->get('business.id');
            
            $transaction_data = $request->only(['ref_no', 'transaction_date', 'location_id', 'final_total', 'expense_for', 'fleet_id', 'additional_notes', 'expense_category_id', 'tax_id', 'contact_id']);
            
            $transaction_data['business_id'] = $business_id;
            $transaction_data['contact_id'] = explode("_",$request->advance_for)[1];
            $transaction_data['total_before_tax'] = $request->advance_amount;
            $transaction_data['total_after_tax'] = $request->advance_amount;
            $transaction_data['final_total'] = $request->advance_amount;
            $user_id = $request->session()->get('user.id');
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'ro_salary';
            $transaction_data['sub_type'] = explode("_",$request->advance_for)[0];
            $transaction_data['payment_status'] = 'paid';
            $date_of_operation = $this->commonUtil->uf_date($request->transaction_date);
            $transaction_data['transaction_date'] = $date_of_operation;
            
            $transaction_data['expense_account'] = $request->expense_account;
            $transaction_data['parent_transaction_id'] = $request->ro_id;
            
            $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');

            //Generate reference number

            if (empty($transaction_data['ref_no'])) {

                $transaction_data['ref_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);

            }
            
            
            $transaction_data['invoice_no'] = $request->route_operation_id;
            
            $transaction = Transaction::create($transaction_data);
            
            $tp_data = [
                'transaction_id' => $transaction->id,
                'business_id' => $business_id,
                'amount' => $request->advance_amount,
                'method' => $request->method,
                'cheque_number' => $request->cheque_number,
                'cheque_date' => $request->cheque_date,
                'created_by' => $user_id,
            ];
            
            $transaction_payment = TransactionPayment::create($tp_data);
            
            $credit_data = [
                                'amount' => $request->advance_amount,
                                'account_id' => $request->account_id,
                                'transaction_id' => $transaction->id,
                                'type' => 'credit',
                                'sub_type' => null,
                                'operation_date' => $transaction_data['transaction_date'],
                                'created_by' => session()->get('user.id'),
                                'transaction_payment_id' => $transaction_payment->id,
                                'note' => null,
                                'attachment' => null
                            ];
                            
                            
            $driver_ledger_data = [
                'contact_id' => explode("_",$request->advance_for)[1],
                'amount' => $this->commonUtil->num_uf($request->advance_amount),
                'type' => "credit",
                'sub_type' =>  explode("_",$request->advance_for)[0],
                'operation_date' => $date_of_operation ? $date_of_operation : \Carbon::now(),
                'created_by' => $user_id,
                'transaction_id' => $transaction_data['invoice_no'] ? $transaction_data['invoice_no'] : null
            ];

            FleetContactLedger::createContactLedger($driver_ledger_data);
            
            
                            
            $credit = AccountTransaction::createAccountTransaction($credit_data);
            
            $credit_data['type'] = 'debit';
            $credit_data['account_id'] = $request->expense_account;
            $credit_data['transaction_payment_id'] = null;
            
            $credit = AccountTransaction::createAccountTransaction($credit_data);
            // location_id created_by
            $output = [
                'success' => true,
                'msg' => __('fleet::lang.success')
            ];
        } catch (\Exception $e) {
            // dd($e);
            DB::rollBack();
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        
        return redirect()->back()->with('status', $output);
    }
    
     public function getByFleetId($fleet_id)
    {
        if(!empty(RouteOperation::where('fleet_id', $fleet_id)->latest()->first())){
             return  RouteOperation::where('fleet_id', $fleet_id)->latest()->first();
        }else{
            $fleet =  Fleet::where('id', $fleet_id)->first();
            echo json_encode(array('actual_meter' => $fleet->starting_meter));
        }
     
    }
    public function actualmeter($id)
    {
        $data = RouteOperation::where('route_operations.id',$id)->leftjoin('users','users.id','=','route_operations.actual_meter_added_by')->select(['route_operations.*','users.username as actual_meter_user'])->first();
        return view('fleet::route_operations.actual_meter')->with(compact(
            'data'
        ));
    }
    public function updateactualmeter($id,Request $request)
    {
        try {
            $meter = $request->only(['actual_meter','trip_completed_on','notes']);
            $meter['actual_meter_added_by'] = $request->session()->get('user.id');
            $meter['actual_meter_added_on'] = date('Y-m-d H:i');
            
            RouteOperation::where('id', $id)->update($meter);

            $output = [
                'success' => true,
                'msg' => __('fleet::lang.success')
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
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $routes = Route::where('business_id', $business_id)->get();
        $products = RouteProduct::where('business_id', $business_id)->pluck('name', 'id');
        $fleets = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id');
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id');
        $payment_line = $this->dummyPaymentLine;
        $payment_types =  $this->productUtil->payment_types(null,false, false, false, false,"is_sale_enabled");
        unset($payment_types['credit_purchase']);

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');
            
        $prefix_type = 'route_no';
        
        $trip_cats = TripCategory::where('business_id',$business_id)->get();
        
        $invoice_number = $this->commonUtil->getRouteOperationInvoiceNumber($business_id);
        // $delivery_date = $this->commonUtil->getRouteOperationInvoiceNumber($business_id);
        
        //  $delivery_date = Fleet::where('business_id', $business_id)->pluck('delivery_date');
        // $invoice_number = $this->transactionUtil->generateReferenceNumber($prefix_type, $invoice_number);

        return view('fleet::route_operations.create')->with(compact(
            'payment_line',
            'payment_types',
            'bank_group_accounts',
            'business_locations',
            'customers',
            'invoice_number',
            'routes',
            'products',
            'fleets',
            'drivers',
            'helpers',
            'trip_cats'
            // ,
            // 'delivery_date'
        ));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {
            
            
            $has_reviewed = $this->transactionUtil->hasReviewed($request->date_of_operation);
        
            if(!empty($has_reviewed)){
                $output              = [
                    'success' => 0,
                    'msg'     =>__('lang_v1.review_first'),
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
        
            
            $reviewed = $this->transactionUtil->get_review($request->date_of_operation,$request->date_of_operation);
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add a Route Operation for an already reviewed date",
                ];
                
                return redirect()->back()->with('status', $output);
            }
            
            
            $user_id = $request->session()->get('user.id');

            $date_of_operation = $this->commonUtil->uf_date($request->date_of_operation);
            $data = [
                'date_of_operation' => $date_of_operation,
                'business_id' => $business_id,
                'location_id' => $request->location_id,
                'contact_id' => $request->contact_id,
                'route_id' => $request->route_id,
                'fleet_id' => $request->fleet_id,
                'starting_meter' => $this->commonUtil->num_uf($request->starting_meter),
                'ending_meter' => $this->commonUtil->num_uf($request->ending_meter),
                'actual_meter' => $this->commonUtil->num_uf($request->ending_meter),
                'invoice_no' => $request->invoice_no,
                'product_id' => json_encode($request->product_id),
                'qty' => json_encode(explode(',',$request->qty)),
                'driver_id' => $request->driver_id,
                'helper_id' => $request->helper_id,
                'order_number' => $request->order_number,
                'order_date' => !empty($request->order_date) ?  $this->commonUtil->uf_date($request->order_date) : null,
                'distance' => $this->commonUtil->num_uf($request->distance),
                'amount' => $this->commonUtil->num_uf($request->amount),
                'driver_incentive' => $this->commonUtil->num_uf($request->driver_incentive),
                'helper_incentive' => $this->commonUtil->num_uf($request->helper_incentive),
                'is_vat' => $request->is_vat,
                'amt_method' => $request->amt_method,
                'created_by' => $user_id
                // ,
                // 'delivery_date'  => $delivery_date
            ];
            
            DB::beginTransaction();
            $route_operation = RouteOperation::create($data);


            $transaction_data = $request->only(['invoice_no', 'ref_no', 'status', 'fleet_id', 'contact_id', 'total_before_tax', 'location_id', 'discount_type', 'discount_amount', 'tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'final_total', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type']);
            $transaction_data['business_id'] = $business_id;
            $transaction_data['created_by'] = $user_id;
            $transaction_data['type'] = 'route_operation';
            $transaction_data['payment_status'] = 'due';
            $transaction_data['store_id'] = $request->input('store_id');
            $transaction_data['transaction_date'] = $date_of_operation;
            $transaction = Transaction::create($transaction_data);

            $route_operation->transaction_id = $transaction->id;
            $route_operation->save();
            
            $driver_ledger_data = [
                'contact_id' => $request->driver_id,
                'amount' => $this->commonUtil->num_uf($request->driver_incentive),
                'type' => "debit",
                'sub_type' =>  'driver',
                'operation_date' => $date_of_operation ? $date_of_operation : \Carbon::now(),
                'created_by' => $user_id,
                'transaction_id' => $transaction->id ? $transaction->id : null
            ];
            
            $helper_ledger_data = [
                'contact_id' => $request->helper_id,
                'amount' => $this->commonUtil->num_uf($request->helper_incentive),
                'type' => "debit",
                'sub_type' =>  'helper',
                'operation_date' => $date_of_operation ? $date_of_operation : \Carbon::now(),
                'created_by' => $user_id,
                'transaction_id' => $transaction->id ? $transaction->id : null
            ];
            
            // driver ledger
            FleetContactLedger::createContactLedger($driver_ledger_data);
            
            // Helper data
            FleetContactLedger::createContactLedger($helper_ledger_data);
            

            $payments = $request->payment;
            
            //add payment for transaction
            $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments);
            $this->transactionUtil->updatePaymentStatus($transaction->id, $transaction->final_total);
            
            
            if(!empty($request->contact_id)){
                $ob_transaction_data = [
                        'amount' => $this->commonUtil->num_uf($request->amount),
                        'contact_id' => $request->contact_id,
                        'type' => "debit",
                        'operation_date' => $date_of_operation ? $date_of_operation : \Carbon::now(),
                        'created_by' => Auth::user()->id,
                        'transaction_id' => $transaction->id ? $transaction->id : null
                    ];
                ContactLedger::createContactLedger($ob_transaction_data);
                
            }
            
            
            $cheque_nos = "";
            if(!empty($request->select_cheques)){
                foreach ($request->select_cheques as $select_cheque) {
                    if (!empty($select_cheque)) {
                        $account_transaction = AccountTransaction::find($select_cheque);
                        
                        $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);
                        
                        if (!empty($transaction_payment)) {
                            $amount = $this->transactionUtil->num_uf($account_transaction->amount);
                            if (!empty($amount)) {
                                $credit_data = [
                                    'amount' => $amount,
                                    'account_id' => $account_transaction->account_id,
                                    'transaction_id' => $transaction->id,
                                    'type' => 'credit',
                                    'sub_type' => null,
                                    'operation_date' => $transaction_data['transaction_date'],
                                    'created_by' => session()->get('user.id'),
                                    'transaction_payment_id' => $transaction_payment->id,
                                    'note' => null,
                                    'attachment' => null
                                ];
                                $credit = AccountTransaction::createAccountTransaction($credit_data);
                                
                                $cheque_nos .= !empty($transaction_payment->cheque_number) ? $transaction_payment->cheque_number."," : "";
                                
                                $transaction_payment->is_deposited = 1;
                                $transaction_payment->save();
                            }
                        }
                    }
                }
            }
            
            
            
            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->to('/fleet-management/route-operation')->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('fleet::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($transaction_id)
    {
        $business_id = request()->session()->get('business.id');

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        $routes = Route::where('business_id', $business_id)->get();
        $products = RouteProduct::where('business_id', $business_id)->pluck('name', 'id');
        $fleets = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');
        $drivers = Driver::where('business_id', $business_id)->pluck('driver_name', 'id');
        $helpers = Helper::where('business_id', $business_id)->pluck('helper_name', 'id');
        $payment_line = $this->dummyPaymentLine;
        $payment_types =  $this->productUtil->payment_types();
        unset($payment_types['credit_purchase']);

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');

        $transaction = Transaction::where('transactions.id', $transaction_id)
            ->with('payment_lines', 'route_operation')
            ->first();
            
        $trip_cats = TripCategory::where('business_id',$business_id)->get();

        return view('fleet::route_operations.edit')->with(compact(
            'payment_line',
            'payment_types',
            'transaction',
            'bank_group_accounts',
            'business_locations',
            'customers',
            'routes',
            'products',
            'fleets',
            'drivers',
            'helpers',
            'trip_cats'
        ));
    }
    
    
    public function fetchFleetBF($contact_id,$start_date,$contact_type, $type = null){
       $query = FleetContactLedger::leftjoin('transactions','fleet_contact_ledgers.transaction_id','transactions.id')
                            ->where('fleet_contact_ledgers.contact_id',$contact_id)
                            ->where('fleet_contact_ledgers.sub_type',$contact_type)
                            ->where('fleet_contact_ledgers.type','debit')
                            ->whereDate('fleet_contact_ledgers.operation_date','<',$start_date);
                            
                            
        $query1 = FleetContactLedger::leftjoin('transactions','fleet_contact_ledgers.transaction_id','transactions.id')
                            ->where('fleet_contact_ledgers.contact_id',$contact_id)
                            ->where('fleet_contact_ledgers.sub_type',$contact_type)
                            ->where('fleet_contact_ledgers.type','credit')
                            ->whereDate('fleet_contact_ledgers.operation_date','<',$start_date);
        
        if(!empty($type)){
            $query->where('fleet_contact_ledgers.type',$type);
            $query1->where('fleet_contact_ledgers.type',$type);
        }
        
        $income = 0;
        $paid = 0;
        
        $income = $query->select('fleet_contact_ledgers.amount')->get()->sum('amount');
        $paid = $query1->select('fleet_contact_ledgers.amount')->get()->sum('amount');
        
        return $income - $paid;
    }
    
    public function fetchFleetSummary($contact_id,$start_date,$end_date,$contact_type, $type = null){
       $query = FleetContactLedger::leftjoin('transactions','fleet_contact_ledgers.transaction_id','transactions.id')
                            ->where('fleet_contact_ledgers.contact_id',$contact_id)
                            ->where('fleet_contact_ledgers.sub_type',$contact_type)
                            ->where('fleet_contact_ledgers.type','debit')
                            ->whereDate('fleet_contact_ledgers.operation_date','>=',$start_date)
                            ->whereDate('fleet_contact_ledgers.operation_date','<=',$end_date);
                            
        $query1 = FleetContactLedger::leftjoin('transactions','fleet_contact_ledgers.transaction_id','transactions.id')
                            ->where('fleet_contact_ledgers.contact_id',$contact_id)
                            ->where('fleet_contact_ledgers.sub_type',$contact_type)
                            ->where('fleet_contact_ledgers.type','credit')
                            ->whereDate('fleet_contact_ledgers.operation_date','>=',$start_date)
                            ->whereDate('fleet_contact_ledgers.operation_date','<=',$end_date);
                            
        if(!empty($type)){
            $query->where('fleet_contact_ledgers.type',$type);
            $query1->where('fleet_contact_ledgers.type',$type);
        }
        
        $income = 0;
        $paid = 0;
        
        $income = $query->select('fleet_contact_ledgers.amount')->get()->sum('amount');
        $paid = $query1->select('fleet_contact_ledgers.amount')->get()->sum('amount');
        
        
        return array('income' => $this->commonUtil->num_f($income), 'paid' => $this->commonUtil->num_f($paid));
    }
    
    public function addExpense($id){
        $business_id = request()->session()->get('user.business_id');
        
        $payment_line = $this->dummyPaymentLine;

        $first_location = BusinessLocation::where('business_id', $business_id)->first();

        $payment_types = $this->transactionUtil->payment_types($first_location->id, true, false, true, false, true,"is_expense_enabled");
        $account_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');


        unset($payment_types['credit_sale']);

        $accounts = [];

        $expense_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();

        $current_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Current Assets')->first();

        $current_liability_account_type = AccountType::where('business_id', $business_id)->where('name', 'Current Liabilities')->first();

        $current_liability_account_type_id = !empty($current_liability_account_type) ? $current_liability_account_type->id : 0;

        $expense_accounts = [];

        $expense_account_id = null;

        $payee_name = Contact::select('name')->where('business_id', $business_id)->first();



        if ($account_module) {

            if (!empty($expense_account_type_id)) {
                
                $expense_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
                ->where('accounts.business_id', $business_id)
                ->where('account_groups.name', 'CPC')
                ->orWhere('accounts.account_type_id', $expense_account_type_id->id)
                ->select('accounts.id', 'accounts.name')->get()->pluck('name','id');

                 
            }

        } else {

            $expense_account_id = Account::where('name', 'Expenses')->where('business_id', $business_id)->first()->id;

            $expense_accounts = Account::where('business_id', $business_id)->where('name', 'Expenses')->pluck('name', 'id');

        }
        
        $current_liabilities_accounts =  Account::where('business_id', $business_id)->where('account_type_id', $current_liability_account_type_id)->pluck('name', 'id');



        $business_locations = BusinessLocation::forDropdown($business_id);

        $contacts = Contact::suppliersDropdown($business_id);
        
        

        $expense_categories = ExpenseCategory::where('business_id', $business_id)

            ->pluck('name', 'id');

        $users = User::forDropdown($business_id, true, true);

        $fleets = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');



        $taxes = TaxRate::forBusinessDropdown($business_id, true, true);



        $ref_count = $this->transactionUtil->onlyGetReferenceCount('expense', null, false);

        //Generate reference number

        $ref_no = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);





        $temp_data = DB::table('temp_data')->where('business_id', $business_id)->select('add_expense_data')->first();

        if (!empty($temp_data)) {

            $temp_data = json_decode($temp_data->add_expense_data);

        }

        if (!request()->session()->get('business.popup_load_save_data')) {

            $temp_data = [];

        }

        $cash_account_id = Account::getAccountByAccountName('Cash')->id;



        $fleet_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module');

        $bank_group_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'Bank Account')
            ->pluck('accounts.name', 'accounts.id');
        $cpc_accounts = Account::leftjoin('account_groups', 'accounts.asset_type', 'account_groups.id')
            ->where('accounts.business_id', $business_id)
            ->where('account_groups.name', 'CPC')
            ->pluck('accounts.name', 'accounts.id');
        
        $ro = RouteOperation::findOrFail($id);
        
        $driver = Driver::where('id', $ro->driver_id)->pluck('driver_name')->first();
        
        //Account transactions
        $initial_payments = TransactionPayment::where("transaction_id", $ro->transaction_id)->get();

        return view('fleet::route_operations.create-expense')

            ->with(compact(
                'ro',
                'cpc_accounts',
                
                'bank_group_accounts',
                
                'cash_account_id',

                'cash_account_id',

                'ref_no',

                'account_module',

                'accounts',

                'expense_accounts',

                'payment_types',

                'payment_line',

                'expense_categories',

                'business_locations',

                'users',

                'fleets',

                'fleet_module',

                'taxes',

                'temp_data',

                'contacts',

                'current_liabilities_accounts',

                'expense_account_id',
                'payee_name',
                'driver',
                'initial_payments'

            ));
    }
    
    public function storeExpense(Request $request){
        try {
            $business_id = $request->session()->get('user.business_id');


            $transaction_data = $request->only(['ref_no', 'transaction_date', 'location_id', 'final_total', 'expense_for', 'fleet_id', 'additional_notes', 'expense_category_id', 'tax_id', 'contact_id']);

            

            $user_id = $request->session()->get('user.id');

            $transaction_data['business_id'] = $business_id;

            $transaction_data['created_by'] = $user_id;

            $transaction_data['type'] = 'expense';

            $transaction_data['status'] = 'final';

            $transaction_data['payment_status'] = 'due';

            $transaction_data['expense_account'] = $request->expense_account;

            $transaction_data['controller_account'] = $request->controller_account;

            $transaction_data['transaction_date'] = $this->transactionUtil->uf_date($transaction_data['transaction_date']);

            $transaction_data['final_total'] = $this->transactionUtil->num_uf(

                $transaction_data['final_total']

            );

            $transaction_data['total_before_tax'] = $transaction_data['final_total'];
            
            $transaction_data['parent_transaction_id'] = $request->ro_id;

            
            //Update reference count

            $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');

            //Generate reference number

            if (empty($transaction_data['ref_no'])) {

                $transaction_data['ref_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);

            }



            if ($request->has('is_recurring')) {

                $transaction_data['is_recurring'] = 1;

                $transaction_data['recur_interval'] = !empty($request->input('recur_interval')) ? $request->input('recur_interval') : 1;

                $transaction_data['recur_interval_type'] = $request->input('recur_interval_type');

                $transaction_data['recur_repetitions'] = $request->input('recur_repetitions');

                $transaction_data['subscription_repeat_on'] = $request->input('recur_interval_type') == 'months' && !empty($request->input('subscription_repeat_on')) ? $request->input('subscription_repeat_on') : null;

            }



            DB::beginTransaction();

            $transaction = Transaction::create($transaction_data);



            $transaction_id =  $transaction->id;

            $tp = null;

            if ($transaction->payment_status != 'paid') {

                $inputs = $request->payment[0];

                $inputs['paid_on'] = $transaction->transaction_date;

                $inputs['transaction_id'] = $transaction->id;

                $inputs['cheque_date'] = !empty($inputs['cheque_date']) ? $inputs['cheque_date'] : $transaction->transaction_date;

                if ($inputs['method'] == 'direct_bank_deposit' || $inputs['method'] == 'bank_transfer' || $inputs['method'] == 'cheque') {

                    if (!empty($inputs['cheque_date'])) {

                        $inputs['paid_on'] =  $inputs['cheque_date'];

                    }

                }

                $inputs['amount'] = $this->transactionUtil->num_uf($inputs['amount']);

                $amount = $inputs['amount'];

                if ($amount > 0 && $inputs['method'] != 'credit_expense') {

                    $inputs['created_by'] = auth()->user()->id;

                    $inputs['payment_for'] = $transaction->contact_id;



                    if (is_numeric($inputs['method'])) {

                        // $inputs['account_id'] = $inputs['method'];

                        $inputs['method'] = 'cash';

                    }



                    if (!empty($inputs['account_id'])) {

                        $inputs['account_id'] = $inputs['account_id'];

                    }


                    $prefix_type = 'expense_payment';
                    if ($transaction->type == 'expense') {

                        $prefix_type = 'expense_payment';

                    }

                    $transaction->controller_account = !empty($inputs['controller_account']) ? $inputs['controller_account'] : null;

                    $transaction->save();



                    $ref_count = $this->transactionUtil->setAndGetReferenceCount($prefix_type);

                    //Generate reference number

                    $inputs['payment_ref_no'] = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);



                    $inputs['business_id'] = $request->session()->get('business.id');

                    $inputs['document'] = $this->transactionUtil->uploadFile($request, 'document', 'documents');



                    $inputs['is_return'] =  0; //added by ahmed

                    unset($inputs['transaction_no_1']);

                    unset($inputs['transaction_no_2']);

                    unset($inputs['transaction_no_3']);

                    unset($inputs['controller_account']);
                    
                    
                    $cheque_nos = "";
                    if(!empty($request->select_cheques)){
                        foreach ($request->select_cheques as $select_cheque) {
                            if (!empty($select_cheque)) {
                                $account_transaction = AccountTransaction::find($select_cheque);
                                
                                $transaction_payment = TransactionPayment::find($account_transaction->transaction_payment_id);
                                
                                if (!empty($transaction_payment)) {
                                    $amount = $this->transactionUtil->num_uf($account_transaction->amount);
                                    if (!empty($amount)) {
                                        $credit_data = [
                                            'amount' => $amount,
                                            'account_id' => $account_transaction->account_id,
                                            'transaction_id' => $transaction->id,
                                            'type' => 'credit',
                                            'sub_type' => null,
                                            'operation_date' => $transaction_data['transaction_date'],
                                            'created_by' => session()->get('user.id'),
                                            'transaction_payment_id' => $transaction_payment->id,
                                            'note' => null,
                                            'attachment' => null
                                        ];
                                        $credit = AccountTransaction::createAccountTransaction($credit_data);
                                        
                                        $cheque_nos .= !empty($transaction_payment->cheque_number) ? $transaction_payment->cheque_number."," : "";
                                        
                                        $transaction_payment->is_deposited = 1;
                                        $transaction_payment->save();
                                    }
                                }
                            }
                        }
                    }
                    $inputs['cheque_number'] = $cheque_nos;
                    $tp = TransactionPayment::create($inputs);



                    //update payment status

                    $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);

                }

            }

            $this->addAccountTransaction($transaction, $request, $business_id, $tp);
            

            DB::commit();

            $output = [

                'success' => 1,

                'msg' => __('expense.expense_add_success')

            ];

        } catch (\Exception $e) {

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());



            $output = [

                'success' => 0,

                'msg' => __('messages.something_went_wrong')

            ];

        }



        return redirect()->back()->with('status', $output);
    }
    
    
     public function addAccountTransaction($transaction, $request, $business_id,  $tp)

    {
        // dd($request->all());

        if (!empty($request->expense_account)) {

            $ob_transaction_data = [

                'amount' => $request->final_total,

                'account_id' => $request->expense_account,

                'type' => 'debit',

                'sub_type' => 'expense',

                'operation_date' => $transaction->transaction_date,

                'created_by' => Auth::user()->id,

                'transaction_id' => $transaction->id,

                'transaction_payment_id' => !empty($tp) ? $tp->id : null

            ];



            AccountTransaction::createAccountTransaction($ob_transaction_data);



            $payment = $request->payment[0];
            $payment['amount'] = $this->transactionUtil->num_uf($payment['amount']);



            $account_payable_id = !empty($payment['controller_account']) ? $payment['controller_account'] : Account::where('business_id', $business_id)->where('name', 'Accounts Payable')->first()->id;

            $ap_transaction_data = [

                'operation_date' => $transaction->transaction_date,

                'created_by' => Auth::user()->id,

                'transaction_id' => $transaction->id,

                'transaction_payment_id' => !empty($tp) ? $tp->id : null

            ];

            if ($payment['method'] == 'credit_expense') {

                $payment['amount'] = 0;

            }



            //if no amount paid

            if ($payment['amount'] == 0) {

                $ap_transaction_data['amount'] = $request->final_total;

                $ap_transaction_data['account_id'] = $account_payable_id;

                $ap_transaction_data['type'] = 'credit';


                if(!empty($tp) &&  $tp->method != 'cheque'){
                    AccountTransaction::createAccountTransaction($ap_transaction_data);
                }
                

            }



            //if partial amount paid

            else if ($payment['amount'] < $request->final_total) {

                $ap_transaction_data['amount'] = $payment['amount'];  //paid amount
                
                $ap_transaction_data['account_id'] = $payment['account_id'];

                // if ($payment['method'] == 'bank_transfer' || $payment['method'] == 'direct_bank_deposit') {

                //     $ap_transaction_data['account_id'] = $payment['account_id'];

                // } else {

                //     //if method is numberic, then it hold the cash groups account id

                //     if (is_numeric($payment['method'])) {

                //         // $ap_transaction_data['account_id'] = $payment['method'];

                //         $payment['method'] = 'cash';

                //     } else {

                //         $ap_transaction_data['account_id'] =  $this->transactionUtil->getDefaultAccountId($payment['method'], $request->location_id);

                //     }

                // }

                $ap_transaction_data['type'] = 'credit';

                if ($payment['method'] == 'direct_bank_deposit' || $payment['method'] == 'bank_transfer' || $payment['method'] == 'cheque') {

                    if (!empty($payment['cheque_date'])) {

                        $ap_transaction_data['operation_date'] =  $payment['cheque_date'];

                    }

                }

                if($tp->method != 'cheque'){
                    AccountTransaction::createAccountTransaction($ap_transaction_data);
                }



                $ap_transaction_data['amount'] = $request->final_total - $payment['amount']; //unpaid amount

                $ap_transaction_data['account_id'] = $account_payable_id;

                $ap_transaction_data['type'] = 'credit';

                $ap_transaction_data['operation_date'] =  $transaction->transaction_date;



                AccountTransaction::createAccountTransaction($ap_transaction_data);

            }

            // if full amount paid

            if ($payment['amount'] == $request->final_total) {

                $ap_transaction_data['amount'] = $payment['amount'];  // full paid amount

                // if ($payment['method'] == 'bank_transfer' || $payment['method'] == 'direct_bank_deposit') {

                //     $ap_transaction_data['account_id'] = $payment['account_id'];

                // } else {

                //     if (is_numeric($payment['method'])) {

                //         // $ap_transaction_data['account_id'] = $payment['method'];

                //         $payment['method'] = 'cash';

                //     } else {

                //         $ap_transaction_data['account_id'] =  $this->transactionUtil->getDefaultAccountId($payment['method'], $request->location_id);

                //     }

                // }
                
                $ap_transaction_data['account_id'] = $payment['account_id'];

                $ap_transaction_data['type'] = 'credit';



                if ($payment['method'] == 'direct_bank_deposit' || $payment['method'] == 'bank_transfer' || $payment['method'] == 'cheque') {

                    if (!empty($payment['cheque_date'])) {

                        $ap_transaction_data['operation_date'] =  $payment['cheque_date'];

                    }

                }



                if(!empty($tp) && $tp->method != 'cheque'){
                    AccountTransaction::createAccountTransaction($ap_transaction_data);
                }

            }

        }

    }

    
    
    public function fetchLedgerSummarised(Request $request){
        $type = $request->type;
        $contact_id = $request->contact_id;
        $contact_type = $request->contact_type;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        
        $summary = array();
        
        $balance = $this->fetchFleetBF($contact_id,$start_date,$contact_type,$type);
        $summary = $this->fetchFleetSummary($contact_id,$start_date,$end_date,$contact_type,$type);
        $summary['balance'] = $this->commonUtil->num_f($balance);
        $summary['balance_due'] = $this->commonUtil->num_f($balance + $this->commonUtil->num_uf($summary['income']) - $this->commonUtil->num_uf($summary['paid']));
        
        return response()->json($summary);
    }
    
    
    public function fetchLedger(Request $request){
        $type = $request->type;
        $contact_id = $request->contact_id;
        $contact_type = $request->contact_type;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        
        $query1 = FleetContactLedger::leftjoin('transactions','fleet_contact_ledgers.transaction_id','transactions.id')
                            ->leftjoin('route_operations','transactions.id','route_operations.transaction_id')
                            ->leftjoin('fleets','route_operations.fleet_id','fleets.id')
                            ->leftjoin('routes','route_operations.route_id','routes.id')
                            ->where('fleet_contact_ledgers.contact_id',$contact_id);
                            
                            
        if ($contact_type == 'driver') {
            $query1->leftJoin('drivers', 'drivers.id', '=', 'fleet_contact_ledgers.contact_id');
            $query1->where('fleet_contact_ledgers.sub_type','driver');
            
        } else {
            $query1->leftJoin('helpers', 'helpers.id', '=', 'fleet_contact_ledgers.contact_id');
            $query1->where('fleet_contact_ledgers.sub_type','helper');
        }
        
        if(!empty($start_date) && !empty($end_date)){
            $query1->whereDate('fleet_contact_ledgers.operation_date','>=',$start_date);
            $query1->whereDate('fleet_contact_ledgers.operation_date','<=',$end_date);
        }
        
        if(!empty($type)){
            $query1->where('fleet_contact_ledgers.type',$type);
        }
        
        $query1->select(
                'fleets.vehicle_number',
                'fleet_contact_ledgers.*',
                'route_operations.invoice_no',
                'route_operations.order_number',
                'route_operations.contact_id',
                'route_operations.id as ro_no',
                'routes.route_name',
                'fleet_contact_ledgers.operation_date',
                'fleet_contact_ledgers.amount',
                'transactions.amount_paid_from_advance')
            ->groupBy('fleet_contact_ledgers.id');
            
        
        
        $results = $query1->orderBy('operation_date', 'asc')->get();
        
        $balance = $this->fetchFleetBF($contact_id,$start_date,$contact_type,$type);
        
        
        return DataTables::of($results)
                ->addColumn('payment_method', function ($row) use($contact_type) {
                    
                    $paid = Transaction::leftJoin('transaction_payments','transaction_payments.transaction_id','transactions.id')->where('invoice_no',$row->transaction_id)->whereIn('type',['ro_salary','ro_advance'])->where('sub_type',$contact_type)->first();
                    
                    $html = '';
                    if(!empty($paid) && $row->type == 'credit'){
                        $html .= ucfirst($paid->method);
                        if($paid->method == 'cheque' || $paid->method == 'bank'){
                            $html .= "<br><b>Bank</b>:".$paid->bank_name."<br><b>Cheque</b>:".$paid->cheque_number."<br><b>Cheque Date</b>:".$paid->cheque_date;
                        }
                    }
                    
                    return $html;
                })
                ->addColumn('description', function ($row) {
                    
                    $html = '';
                    $contact = Contact::find($row->contact_id);
                    $fleet = "";
                    $html .= "<b>Trip operation</b>: ".$row->invoice_no."<br><b>Order Number </b>: ".$row->order_number."<br><b>Customer: </b>".(!empty($contact) ? $contact->name : "")."<br><b>Vehicle No.</b>: ".$row->vehicle_number;
                    return $html;
                })
                ->editColumn('operation_date', '{{@format_date($operation_date)}}')
                ->editColumn('amount', function($row){
                    if($row->type == 'debit'){
                        return $this->commonUtil->num_f($row->amount);
                    }
                })
                ->editColumn('amount_paid',  function($balance){
                    // if($row->type == 'credit'){
                    //     return $this->commonUtil->num_f($row->amount);
                    // }
                    return $balance;
                    // return $this->commonUtil->num_f($row->amount_paid_from_advance);
                })
                ->editColumn('balance',  function($row) use(&$balance){
                        if($row->type == 'credit'){
                            $balance -= $row->amount;
                        }
                        
                        if($row->type == 'debit'){
                            $balance += $row->amount - $row->amount_paid;
                        }
                    
                        
                        return $this->commonUtil->num_f($balance);
                })
                
                ->rawColumns(['action', 'payment_method', 'description'])
                ->make(true);
                            
    }
    
    

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $transaction_id)
    {
        try {
            $has_reviewed = $this->transactionUtil->hasReviewed($request->date_of_operation);
        
            if(!empty($has_reviewed)){
                $output              = [
                    'success' => 0,
                    'msg'     =>__('lang_v1.review_first'),
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
        
            $reviewed = $this->transactionUtil->get_review($request->date_of_operation,$request->date_of_operation);
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't edit a Route Operation for an already reviewed date",
                ];
                
                return redirect()->back()->with('status', $output);
            }
            
            // dd($request->product_id);
            
            
            $date_of_operation = $this->commonUtil->uf_date($request->date_of_operation);
            $data = [
                'date_of_operation' => $date_of_operation,
                'location_id' => $request->location_id,
                'contact_id' => $request->contact_id,
                'route_id' => $request->route_id,
                'fleet_id' => $request->fleet_id,
                 'starting_meter' => $request->starting_meter,
                'ending_meter' => $request->ending_meter,
                'actual_meter' => $request->ending_meter,
                'invoice_no' => $request->invoice_no,
                'order_number' => $request->order_number,
                'order_date' => !empty($request->order_date) ?  $this->commonUtil->uf_date($request->order_date) : null,
                'product_id' => json_encode($request->product_id),
                'qty' => json_encode(explode(',',$request->qty)),
                'driver_id' => $request->driver_id,
                'helper_id' => $request->helper_id,
                'distance' => $this->commonUtil->num_uf($request->distance),
                'amount' => $this->commonUtil->num_uf($request->amount),
                'driver_incentive' => $this->commonUtil->num_uf($request->driver_incentive),
                'helper_incentive' => $this->commonUtil->num_uf($request->helper_incentive),
                'is_vat' => $request->is_vat,
                'amt_method' => $request->amt_method,
            ];

            DB::beginTransaction();
            $route_operation = RouteOperation::where('transaction_id', $transaction_id)->update($data);


            $transaction_data = $request->only(['invoice_no', 'ref_no', 'status', 'contact_id', 'total_before_tax', 'location_id', 'discount_type', 'discount_amount', 'tax_id', 'tax_amount', 'shipping_details', 'shipping_charges', 'additional_notes', 'exchange_rate', 'pay_term_number', 'pay_term_type']);
            $transaction_data['final_total'] = $this->commonUtil->num_uf($request->amount);
            
            $transaction_data['transaction_date'] = $date_of_operation;
            Transaction::where('id', $transaction_id)->update($transaction_data);

            $payments = $request->payment;

            $transaction = Transaction::find($transaction_id);
            //add payment for transaction
            // $this->transactionUtil->createOrUpdatePaymentLines($transaction, $payments);
            
            $user_id = $request->session()->get('user.id');
            
            $driver_ledger_data = [
                'contact_id' => $request->driver_id,
                'amount' => $this->commonUtil->num_uf($request->driver_incentive),
                'type' => "debit",
                'sub_type' =>  'driver',
                'operation_date' => $date_of_operation ? $date_of_operation : \Carbon::now(),
                'created_by' => $user_id,
                'transaction_id' => $transaction->id ? $transaction->id : null
            ];
            
            $helper_ledger_data = [
                'contact_id' => $request->helper_id,
                'amount' => $this->commonUtil->num_uf($request->helper_incentive),
                'type' => "debit",
                'sub_type' =>  'driver',
                'operation_date' => $date_of_operation ? $date_of_operation : \Carbon::now(),
                'created_by' => $user_id,
                'transaction_id' => $transaction->id ? $transaction->id : null
            ];
            
            $driverfleet = FleetContactLedger::where('type','debit')->where('transaction_id',$transaction->id)->first();
            $helperfleet = FleetContactLedger::where('type','debit')->where('transaction_id',$transaction->id)->first();
            
            if(!empty($driverfleet)){
                // driver ledger
                $driverfleet->fill($driver_ledger_data);
                $driverfleet->save();
            }
            
            
            if(!empty($driverfleet)){
                // driver ledger
                $helperfleet->fill($helper_ledger_data);
                $helperfleet->save();
            }
            

            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->to('/fleet-management/route-operation')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            
            $transaction = Transaction::where('id', $id)->first();
            
            $has_reviewed = $this->transactionUtil->hasReviewed($transaction->transaction_date);
        
            if(!empty($has_reviewed)){
                $output              = [
                    'success' => 0,
                    'msg'     =>__('lang_v1.review_first'),
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
            
            $reviewed = $this->transactionUtil->get_review($transaction->transaction_date,$transaction->transaction);
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't delete a Route Operation for an already reviewed date",
                ];
                
                return $output;
            }
            
            Transaction::where('id', $id)->delete();
            RouteOperation::where('transaction_id', $id)->delete();
            TransactionPayment::where('transaction_id', $id)->delete();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
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
