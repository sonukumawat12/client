<?php

namespace Modules\Fleet\Http\Controllers;

use App\Business;
use App\ContactLedger;
use App\Account;
use App\AccountType;
use App\BusinessLocation;
use App\ExpenseCategory;
use App\FuelType;
use App\NotificationTemplate;
use App\TaxRate;
use App\Transaction;
use App\OpeningBalance;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\ContactGroup;
use App\Utils\Util;
use Modules\Property\Entities\PaymentOption;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Modules\Fleet\Entities\Fleet;
use Modules\Fleet\Entities\Route;

use Modules\Fleet\Entities\FleetFuelType;
use Modules\Fleet\Entities\FleetFuelDetail;
use Modules\Fleet\Entities\Driver;


use App\Contact;
use Yajra\DataTables\Facades\DataTables;
use Modules\Fleet\Entities\RouteOperation;
use App\AccountTransaction;
use function PHPUnit\Framework\isNan;



class FleetController extends Controller
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
        $this->businessUtil = $businessUtil;
    }

        public function addFuelDetails($id){
        $business_id = request()->session()->get('business.id');

        $drivers = Driver::where('business_id',$business_id)->select('driver_name','id')->pluck('driver_name','id');
        $fuel_types = FleetFuelType::where('status', 1)
            ->where('business_id', $business_id)
            ->orderBy('id', 'desc')
            ->pluck('type','id'); 

        $vehicle = Fleet::where('id', $id)->first();
        $vehicle_number = $vehicle->vehicle_number;
        $fuel_type = $vehicle->fuel_type_id;

        $fuel_type_saved = is_numeric($fuel_type)  ? FleetFuelType::find($fuel_type) : null;
        $currentPrice = 0;
        if($fuel_type_saved) {
            $fuels = FleetFuelType::where('type', $fuel_type_saved->type)->get();
            $idCheck = 0;
            foreach ($fuels as $data) {
                if($data->status != 0) {
                    if($currentPrice == 0) {
                        $currentPrice = $data->price_per_litre;
                        $idCheck = $data->id;
                    } else {
                        if($data->id > $idCheck) {
                            $idCheck = $data->id;
                            $currentPrice = $data->price_per_litre;
                        }
                    }
                }


            }
        } else {
            $fuels = FleetFuelType::where('type', $fuel_type)->get();
            $idCheck = 0;
            foreach ($fuels as $data) {
                if($data->status != 0) {
                    if($currentPrice == 0) {
                        $currentPrice = $data->price_per_litre;
                        $idCheck = $data->id;
                    } else {
                        if($data->id > $idCheck) {
                            $idCheck = $data->id;
                            $currentPrice = $data->price_per_litre;
                        }
                    }
                }


            }
        }

        $previous_odometer = FleetFuelDetail::where('fleet_id', $id)->orderBy('id', 'desc')->latest()->first();
        if(!empty($previous_odometer)){
            $previous_odometer = $previous_odometer->current_odometer;
        }else{
            $previous_odometer = 0;
        }

        $first_location = BusinessLocation::where('business_id', $business_id)->first();
        $payment_types = $this->productUtil->payment_types($first_location, true, true, false, false, true, "is_purchase_enabled");

        // dd($payment_types);
//        unset($payment_types['card']);
//        unset($payment_types['credit_sale']);

        //Accounts
        $accounts = $this->moduleUtil->accountsDropdown($business_id, true);

        //Expense Categories
        $expenses = ExpenseCategory::leftjoin('accounts','accounts.id','expense_categories.expense_account')->where('expense_categories.business_id', $business_id)->select('accounts.name as expense_account_name','expense_categories.*')->get();

        $expense_account_type_id = AccountType::where('business_id', $business_id)->where('name', 'Expenses')->first();


        //locations
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = $first_location->id;

        $account_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');

        $payee_name = Contact::select('name')->where('business_id', $business_id)->first()->name;
        
        if(!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_cheque_writing')){
            $payee_name = 'Cheque Module is not enabled';
        }
        
        $expense_account_id = null;
        $expense_accounts = [];

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

        return view('fleet::fleet.add_fuel_details')->with(compact(
            'previous_odometer',
            'id',
            'fuel_types',
            'drivers',
            'vehicle_number',
            'fuel_type',
            'currentPrice',
            'payment_types',
            'accounts',
            'expenses',
            'business_locations',
            'default_location',
            'payee_name',
            'expense_accounts',
            'expense_account_id'
        ));


    }

    public function oneFuelType(Request $request){
        $id = $request->id;
        $fuel = FleetFuelType::findOrFail($id);

        return response()->json(['fuel' => $fuel]);
    }

    public function editFuelDetail($id){
        $business_id = request()->session()->get('business.id');

        $fuelDetail = FleetFuelDetail::findOrFail($id);

        $drivers = Driver::where('business_id',$business_id)->select('driver_name','id')->pluck('driver_name','id');
        $fuel_types = FleetFuelType::where('business_id',$business_id)->where('status',1)->select('type','id')->orderBy('id','desc')->pluck('type','id');

        $previous_odometer = FleetFuelDetail::where('fleet_id', $id)->orderBy('id', 'desc')->first();
        if(!empty($previous_odometer)){
            $previous_odometer = $previous_odometer->current_odometer;
        }else{
            $previous_odometer = 0;
        }

        return view('fleet::fleet.edit_fuel_details')->with(compact(
            'previous_odometer',
            'id',
            'fuel_types',
            'drivers',
            'fuelDetail'
        ));
    }

    public function destroyFuelDetail($id){
        try {
            FleetFuelDetail::where('id', $id)->delete();
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

        return $output;
    }


    public function fuelManagement(){
        $business_id = request()->session()->get('business.id');
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'fleet_module')) {
            abort(403, 'Unauthorized action.');
        }
        if (request()->ajax()) {
            $fleets = FleetFuelDetail::leftjoin('users', 'fleet_fuel_details.created_by', 'users.id')
                ->leftJoin('fleets','fleets.id','fleet_fuel_details.fleet_id')
                ->leftJoin('fleet_fuel_types','fleet_fuel_types.id','fleet_fuel_details.fuel_type')
                ->leftJoin('drivers','drivers.id','fleet_fuel_details.driver_id')
                ->where('fleet_fuel_details.business_id', $business_id)
                ->select([
                    'fleet_fuel_details.*',
                    'fleet_fuel_details.date_of_operation as date',
                    'users.username as added_by',
                    'fleet_fuel_types.type as fuel_typen',
                    'drivers.driver_name as driver',
                    'fleets.vehicle_number'
                ]);

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $fleets->whereDate('fleet_fuel_details.date_of_operation', '>=', request()->start_date);
                $fleets->whereDate('fleet_fuel_details.date_of_operation', '<=', request()->end_date);
            }

            if (!empty(request()->vehicle_number)) {
                $fleets->where('fleet_fuel_details.fleet_id', request()->vehicle_number);
            }

            if (!empty(request()->driver_id)) {
                $fleets->where('fleet_fuel_details.driver_id', request()->driver_id);
            }
            $fleets->orderBy('fleet_fuel_details.id','DESC');


            if (!empty(request()->vehicle_number)) {
                $one = request()->vehicle_number;

                $first_fuel_avg = FleetFuelDetail::where('fleet_id',$one);
                $last_fuel_avg = FleetFuelDetail::where('fleet_id',$one);
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $first_fuel_avg->whereDate('fleet_fuel_details.date_of_operation', '>=', request()->start_date);
                    $first_fuel_avg->whereDate('fleet_fuel_details.date_of_operation', '<=', request()->end_date);

                    $last_fuel_avg->whereDate('fleet_fuel_details.date_of_operation', '>=', request()->start_date);
                    $last_fuel_avg->whereDate('fleet_fuel_details.date_of_operation', '<=', request()->end_date);
                }

                if (!empty(request()->driver_id)) {
                    $first_fuel_avg->where('fleet_fuel_details.driver_id', request()->driver_id);

                    $last_fuel_avg->where('fleet_fuel_details.driver_id', request()->driver_id);
                }

                $liters = $first_fuel_avg->sum('liters');

                $first = $first_fuel_avg->orderBy('id','ASC')->first()->previous_odometer ?? 0;
                $last = $last_fuel_avg->latest()->first()->current_odometer ?? 0;
                $total = $first_fuel_avg->sum('total_amount');

                $avg = ($last - $first) == 0 ? 0 : ($last - $first) / $liters;
                $avg_km = ($last - $first) == 0 ? 0 : $total / ($last - $first);

            }else{
                $fleet_ids = FleetFuelDetail::where('fleet_fuel_details.business_id', $business_id)->select('fleet_id')->distinct()->pluck('fleet_id');
                $avg = 0;
                $tot = 0;
                $tot_cost = 0;
                $avg_km = 0;

                if(!empty($fleet_ids)){
                    foreach($fleet_ids as $one){
                        $first_fuel_avg = FleetFuelDetail::where('fleet_id',$one);
                        $last_fuel_avg = FleetFuelDetail::where('fleet_id',$one);
                        if (!empty(request()->start_date) && !empty(request()->end_date)) {
                            $first_fuel_avg->whereDate('fleet_fuel_details.date_of_operation', '>=', request()->start_date);
                            $first_fuel_avg->whereDate('fleet_fuel_details.date_of_operation', '<=', request()->end_date);

                            $last_fuel_avg->whereDate('fleet_fuel_details.date_of_operation', '>=', request()->start_date);
                            $last_fuel_avg->whereDate('fleet_fuel_details.date_of_operation', '<=', request()->end_date);
                        }

                        if (!empty(request()->driver_id)) {
                            $first_fuel_avg->where('fleet_fuel_details.driver_id', request()->driver_id);

                            $last_fuel_avg->where('fleet_fuel_details.driver_id', request()->driver_id);
                        }

                        $liters = $first_fuel_avg->sum('liters');
                        $total = $first_fuel_avg->sum('total_amount');

                        $first = $first_fuel_avg->orderBy('id','ASC')->first()->previous_odometer;
                        $last = $last_fuel_avg->latest()->first()->current_odometer;

                        $tot += ($last - $first) / $liters;
                        $tot_cost += $total / ($last - $first);
                    }


                    $avg = !empty(sizeof($fleet_ids)) > 0 ? $tot / sizeof($fleet_ids) : 0;
                    $avg_km = !empty(sizeof($fleet_ids)) > 0 ? $tot_cost / sizeof($fleet_ids) : 0;
                }

            }


            return DataTables::of($fleets)
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
                        if (auth()->user()->can('fleet.fuel_management')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FleetController@editFuelDetail', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FleetController@destroyFuelDetail', [$row->id]) . '" class="delete-fleet"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '<li class="divider"></li>';

                        return $html;
                    }
                )
                ->editColumn('date', '{{@format_date($date)}}')

                ->editColumn('previous_odometer', function ($row) {
                    $html = '<span class="display_currency previous_odometer" data-currency_symbol="false" data-orig-value="' . ($row->current_odometer - $row->previous_odometer) . '">' . $row->previous_odometer . '</span>';

                    return $html;
                })

                ->editColumn('current_odometer', function ($row) {
                    $avg = ($row->current_odometer - $row->previous_odometer) / $row->liters;
                    $html = '<span class="display_currency average_consumption" data-currency_symbol="false" data-orig-value="' . $avg . '">' . $row->current_odometer . '</span>';

                    return $html;
                })

                ->editColumn('liters', function ($row) {
                    $html = '<span class="display_currency liters_pumped" data-currency_symbol="false" data-orig-value="' . $row->liters . '">' . $row->liters . '</span>';

                    return $html;
                })

                ->editColumn('price_per_liter', function ($row) {
                    $html = '<span class="display_currency price" data-currency_symbol="false" data-orig-value="' . $row->price_per_liter . '">' . $row->price_per_liter . '</span>';

                    return $html;
                })

                ->editColumn('total_amount', function ($row) {
                    $html = '<span class="display_currency total_amount" data-currency_symbol="false" data-orig-value="' . $row->total_amount . '">' . $row->total_amount . '</span>';

                    return $html;
                })

                ->editColumn('fuel_cost', function ($row) {
                    $html = '<span class="display_currency" data-currency_symbol="false" data-orig-value="' . $row->fuel_cost . '">' . $row->fuel_cost . '</span>';

                    return $html;
                })

                ->editColumn('fuel_consumption', function ($row) {
                    $fuel_consumption = ($row->current_odometer - $row->previous_odometer) / $row->liters;
                    $html = '<span class="display_currency average_consumption" data-currency_symbol="false" data-orig-value="' .$fuel_consumption . '">' . $fuel_consumption . '</span>';

                    return $html;
                })

                ->with(['avg' => $avg,'avg_cost' => $avg_km])
                ->removeColumn('id')
                ->rawColumns(['action', 'previous_odometer','current_odometer','liters','price_per_liter','total_amount','fuel_cost', 'fuel_consumption'])
                ->make(true);
        }


        $fleets = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets->pluck('vehicle_number', 'id');
        $drivers = Driver::where('business_id',$business_id)->select('driver_name','id')->pluck('driver_name','id');

        return view('fleet::fleet.fuel_management')->with(compact(
            'vehicle_numbers',
            'drivers'
        ));
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
            $fleets = Fleet::leftjoin('transactions AS t', 'fleets.id', '=', 't.fleet_id')
                ->leftjoin('business_locations', 'fleets.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 't.id', 'transaction_payments.transaction_id')
                ->leftjoin('users', 'fleets.created_by', 'users.id')
                ->where('fleets.business_id', $business_id)
                ->select([
                    'fleets.*',
                    'business_locations.name as location_name',
                    DB::raw("SUM(IF(t.type = 'route_operation', final_total, 0)) as income"),
                    DB::raw("SUM(IF(t.type = 'route_operation', transaction_payments.amount, 0)) as total_received"),
                    DB::raw("SUM(IF(t.type = 'fleet_opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'fleet_opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                ]);

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $fleets->whereDate('fleets.date', '>=', request()->start_date);
                $fleets->whereDate('fleets.date', '<=', request()->end_date);
            }
            if (!empty(request()->location_id)) {
                $fleets->where('fleets.location_id', request()->location_id);
            }
            if (!empty(request()->vehicle_number)) {
                $fleets->where('fleets.vehicle_number', request()->vehicle_number);
            }
            if (!empty(request()->vehicle_type)) {
                $fleets->where('fleets.vehicle_type', request()->vehicle_type);
            }
            if (!empty(request()->vehicle_brand)) {
                $fleets->where('fleets.vehicle_brand', request()->vehicle_brand);
            }
            if (!empty(request()->vehicle_model)) {
                $fleets->where('fleets.vehicle_model', request()->vehicle_model);
            }
            $fleets->groupBy('fleets.id')->orderBy('fleets.id','DESC');

            return DataTables::of($fleets)
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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FleetController@edit', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FleetController@addFuelDetails', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"> ' . __("fleet::lang.add_fuel_details") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FleetController@destroy', [$row->id]) . '" class="delete-fleet"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '<li class="divider"></li>';

                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\FleetController@show', [$row->id]) . '?tab=info" class="" ><i class="fa fa-info-circle"></i> ' . __("fleet::lang.info") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\FleetController@show', [$row->id]) . '?tab=ledger" class="" ><i class="fa fa-anchor"></i> ' . __("fleet::lang.ledger") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\FleetController@show', [$row->id]) . '?tab=income" class="" ><i class="fa fa-money"></i> ' . __("fleet::lang.income") . '</a></li>';
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\FleetController@show', [$row->id]) . '?tab=expenses" class="" ><i class="fa fa-minus"></i> ' . __("fleet::lang.expenses") . '</a></li>';


                        return $html;
                    }
                )
                ->addColumn('fuel_type', function ($row) {
                    if(!$row->fuel_type_id)
                        return "Not available";
                    else {
                        $type = FleetFuelType::find($row->fuel_type_id);
                        return $type ? $type->type : "Not Found";
                    }
                })
                ->editColumn('date', '{{@format_date($date)}}')
                ->addColumn('income', function ($row) {
                    $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . $row->income . '">' . $row->income . '</span>';

                    return $html;
                })
                ->addColumn('ending_meter', function ($row) {
                    if(!empty(RouteOperation::where('fleet_id', $row->id)->latest()->first())){
                        return  $this->commonUtil->num_f(RouteOperation::where('fleet_id', $row->id)->latest()->first()->ending_meter);
                    }else{
                        return "";
                    }
                })
                ->addColumn('payment_received', function ($row) {
                    $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . $row->total_received . '">' . $row->total_received . '</span>';

                    return $html;
                })
                ->addColumn('payment_due', function ($row) {
                    $payment_due = $row->income - $row->total_received;
                    $html = '<span class="display_currency" data-currency_symbol="true" data-orig-value="' . $payment_due . '">' . $payment_due . '</span>';

                    return $html;
                })
                ->editColumn('opening_balance', function ($row) {
                    $paid_opening_balance = !empty($row->opening_balance_paid) ? $row->opening_balance_paid : 0;
                    $opening_balance = !empty($row->opening_balance) ? $row->opening_balance : 0;
                    $balance_value = $opening_balance - ($paid_opening_balance);
                    $html = '<span class="display_currency ob" data-currency_symbol="true" data-orig-value="' . $balance_value . '">' . $balance_value . '</span>&nbsp;&nbsp;&nbsp;<a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FleetController@viewopeningbalance', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="fa fa-eye" style="font-size: 125%;"></i></a>';


                    return $html;
                })
                // ->editColumn('starting_meter', function ($row) {
                //     if(!empty(RouteOperation::where('fleet_id', $row->id)->latest()->first())){
                //          return  $this->commonUtil->num_f(RouteOperation::where('fleet_id', $row->id)->latest()->first()->actual_meter);
                //     }else{
                //         return $this->commonUtil->num_f($row->starting_meter);
                //     }
                // })

                ->removeColumn('id')
                ->rawColumns(['action', 'payment_due', 'opening_balance', 'income', 'payment_received'])
                ->make(true);
        }


        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $fleets = Fleet::where('business_id', $business_id)->get();
        $vehicle_numbers = $fleets->pluck('vehicle_number', 'vehicle_number');
        $vehicle_types = $fleets->pluck('vehicle_type', 'vehicle_type');
        $vehicle_brands = $fleets->pluck('vehicle_brand', 'vehicle_brand');
        $vehicle_models = $fleets->pluck('vehicle_model', 'vehicle_model');

        $access_account = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id);
        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');
        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');
        // Maximum vehicles allowed
        $subscriptions = Subscription::active_subscription($business_id);

        $max_vehicles = $subscriptions ? $subscriptions->package_details['vehicle_count'] :0;

        // Vehicles added
        $vehicles_added = DB::table('fleets')->where('business_id', $business_id)->count();



        return view('fleet::fleet.index')->with(compact(
            'business_locations',
            'vehicle_numbers',
            'vehicle_types',
            'vehicle_brands',
            'access_account',
            'income_accounts',
            'vehicle_models',
            'customers',
            'max_vehicles',
            'vehicles_added'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function viewopeningbalance($id)
    {
        $fleets = OpeningBalance::with('contact')->where('fleets_id', $id)->get();
        $fleet = Fleet::findOrFail($id);
        $vehicle_number = $fleet->vehicle_number;
        $total_received = $fleets->sum('opening_amount');
        $opening_balance = $total_received;
        $to_be_added = $opening_balance - $total_received;


        return view('fleet::fleet.opening_balance')->with(compact(
            'fleets',
            'total_received',
            'opening_balance',
            'to_be_added',
            'vehicle_number'
        ));
    }

    public function editOpeningBalance($id){
        $fleets = OpeningBalance::findOrFail($id);
        $business_id = request()->session()->get('business.id');

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');

        $transactions = Transaction::where('invoice_no',$fleets->invoice_no)->first();

        return view('fleet::fleet.edit_opening_balance')->with(compact(
            'fleets','transactions','customers'
        ));
    }

    public function updateOpeningBalance(Request $request){

        try {
            $business_id = request()->session()->get('business.id');
            $fleets = OpeningBalance::findOrFail($request->id);
            $fleets->opening_amount = $request->opening_amount;
            $fleets->invoice_no = $request->invoice_no;
            $fleets->contact_id = $request->contact_id;
            $fleets->invoice_date = $request->invoice_date;
            $fleets->notes = $request->notes;
            $fleets->save();

            $transactions = Transaction::findOrFail($request->transaction_id);
            $transactions->final_total = $request->opening_amount;
            $transactions->invoice_no = $request->invoice_no;
            $transactions->contact_id = $request->contact_id;
            $transactions->transaction_date = $request->invoice_date;
            $transactions->save();


            $receivealbe_account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
            $ob_account_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');


            $ob_transaction_data = [
                'amount' => $request->opening_amount,
                'operation_date' => $request->invoice_date
            ];

            AccountTransaction::where('transaction_id',$request->transaction_id)->where('account_id',$ob_account_id)->update($ob_transaction_data);
            ContactLedger::where('transaction_id',$request->transaction_id)->update($ob_transaction_data);
            AccountTransaction::where('transaction_id',$request->transaction_id)->where('account_id',$receivealbe_account_id)->update($ob_transaction_data);

            DB::beginTransaction();


            DB::commit();
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

    public function create()
    {
        $business_id = request()->session()->get('business.id');

        $contact_id = $this->businessUtil->check_customer_code($business_id, 1);

        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');
        $code_for_vehicle =  Fleet::where('business_id', $business_id)->count() + 1;

        $access_account = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $expense_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);

        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');

        $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_type_id)->pluck('name', 'id');

        // $income_accounts1 = ExpenseCategory::leftjoin('accounts', 'expense_account', 'accounts.id')
        //     ->where('expense_categories.business_id', $business_id)
        //     ->select(['expense_categories.name'])->get();
        // $expense_accounts = $income_accounts1->map(function ($val) {
        //     return $val->name;
        // });

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');

        // Maximum vehicles allowed
        $subscriptions = Subscription::active_subscription($business_id);

        $max_vehicles = $subscriptions ? $subscriptions->package_details['vehicle_count'] :0;

        // Vehicles added
        $vehicles_added = DB::table('fleets')->where('business_id', $business_id)->count();

        //Fuel types
        $business_id_user= request()->session()->get('user.business_id');

        $fuelTypes = FleetFuelType::where('status', '!=', 0)
            ->where('business_id', $business_id_user)
            ->orderBy('id', 'desc')
            ->pluck('type','id'); // Get the collection without plucking specific columns

  

        $types = [];
        if (auth()->user()->can('supplier.create')) {
            $typest['supplier'] = __('report.supplier');
        }
        if (auth()->user()->can('customer.create')) {
            $types['customer'] = __('report.customer');
        }
        if (auth()->user()->can('supplier.create') && auth()->user()->can('customer.create')) {
            $types['both'] = __('lang_v1.both_supplier_customer');
        }
        $type = 'customer'; //contact type /used in quick add contact
        $customer_groups = ContactGroup::forDropdown($business_id);

        return view('fleet::fleet.create')->with(compact(
            'business_locations',
            'access_account',
            'income_accounts',
            'expense_accounts',
            'code_for_vehicle',
            'customers',
            'max_vehicles',
            'vehicles_added','type','types','customer_groups','contact_id',
            'fuelTypes'
        ));
    }

    public function opening_balance()
    {
        $business_id = request()->session()->get('business.id');

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');

        return view('fleet::fleet.opening_balance')->with(compact('customers'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');

            // Maximum vehicles allowed
            $subscriptions = Subscription::active_subscription($business_id);

            $max_vehicles = $subscriptions ? $subscriptions->package_details['vehicle_count'] :0;

            // Vehicles added
            $vehicles_added = DB::table('fleets')->where('business_id', $business_id)->count();

            if ($max_vehicles > 0 && $vehicles_added >= $max_vehicles) {
                $output = [
                    'success' => false,
                    'msg' => __('messages.max_vehicles_reached')
                ];

                // dd($output);
                return redirect()->back()->with('status', $output);
            }

            $inputs = $request->except('_token', 'opening_balance');
            $inputs['date'] = $this->transactionUtil->uf_date($inputs['date']);
            $inputs['business_id'] = $business_id;
            $inputs['created_by'] = Auth::user()->id;
            DB::beginTransaction();
            $fleet = Fleet::create($inputs);

            if (!empty($request->opening_balance)) {
                // $transaction = Transaction::create(
                //     [
                //         'type' => 'opening_balance',
                //         'fleet_id' => $fleet->id,
                //         'status' => 'received',
                //         'business_id' => $business_id,
                //         'transaction_date' => $inputs['date'],
                //         'total_before_tax' => $request->opening_balance,
                //         'location_id' => $request->location_id,
                //         'final_total' => $request->opening_balance,
                //         'payment_status' => 'due',
                //         'created_by' => Auth::user()->id
                //     ]
                // );



                $customer = explode(',', $request->new_contact_id);
                $amount = explode(',', $request->new_opening_amount);
                $notes = explode(',', $request->new_notes);
                $invoices = explode(',', $request->new_invoices);
                $dates = explode(',', $request->new_dates);

                $receivealbe_account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                $ob_account_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');


                foreach ($customer as $key => $data) {
                    $opening_balance = OpeningBalance::create(
                        [
                            'fleets_id' => $fleet->id,
                            'contact_id' => $data,
                            'opening_amount' => $amount[$key],
                            'notes' => $notes[$key],
                            'invoice_no' => $invoices[$key],
                            'invoice_date' => $dates[$key]
                        ]
                    );

                    $transaction1 = Transaction::create(
                        [
                            'type' => 'fleet_opening_balance',
                            'status' => 'received',
                            'fleet_id' => $fleet->id,
                            'business_id' => $business_id,
                            'transaction_date' => !empty($dates[$key]) ? $dates[$key] : \Carbon::now(),
                            'invoice_no' => $invoices[$key],
                            'total_before_tax' => $amount[$key],
                            'location_id' => $request->location_id,
                            'final_total' => $amount[$key],
                            'payment_status' => 'due',
                            'contact_id' => $data,
                            'created_by' => Auth::user()->id
                        ]);



                    $ob_transaction_data = [
                        'amount' => $amount[$key],
                        'account_id' => $receivealbe_account_id,
                        'contact_id' => $data,
                        'type' => "debit",
                        'sub_type' => 'fleet_opening_balance',
                        'operation_date' => !empty($dates[$key]) ? $dates[$key] : \Carbon::now(),
                        'created_by' => Auth::user()->id,
                        'transaction_id' => $transaction1->id
                    ];

                    AccountTransaction::createAccountTransaction($ob_transaction_data);
                    ContactLedger::createContactLedger($ob_transaction_data);

                    $ob_transaction_data['type'] = 'credit';
                    $ob_transaction_data['account_id'] = $ob_account_id;

                    AccountTransaction::createAccountTransaction($ob_transaction_data);

                }
            }

            DB::commit();
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

       public function storeFuelDetails(Request $request)
    {
        try {
            $business_id = request()->session()->get('business.id');

            if (!$this->moduleUtil->isSubscribed($business_id)) {

                return $this->moduleUtil->expiredResponse(action('ExpenseController@index'));

            }


            $transaction_data = $request->only(['location_id', 'fleet_id', 'expense_category_id']);
            $transaction_data['final_total'] = $request->total_amount;
            $transaction_data['transaction_date'] = $this->transactionUtil->uf_date($request->date);//$request->date;

            $has_reviewed = $this->transactionUtil->hasReviewed($transaction_data['transaction_date']);

            if(!empty($has_reviewed)){
                $output              = [
                    'success' => 0,
                    'msg'     =>__('lang_v1.review_first'),
                ];

                return redirect()->back()->with(['status' => $output]);
            }

            $reviewed = $this->transactionUtil->get_review($transaction_data['transaction_date'],$transaction_data['transaction_date']);

            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add an expense for an already reviewed date",
                ];

                return redirect('expenses')->with('status', $output);
            }

            $transaction_data['business_id'] = $business_id;

            $transaction_data['created_by'] = Auth::user()->id;

            $transaction_data['type'] = 'expense';

            $transaction_data['status'] = 'final';

            $transaction_data['payment_status'] = 'due';

            $transaction_data['expense_account'] = $request->expense_account;

            $transaction_data['controller_account'] = $request->controller_account;

            //$transaction_data['transaction_date'] = $this->transactionUtil->uf_date($transaction_data['transaction_date']);

            $transaction_data['final_total'] = $this->transactionUtil->num_uf(

                $transaction_data['final_total']

            );

            $transaction_data['total_before_tax'] = $transaction_data['final_total'];

            if (!empty($transaction_data['tax_id'])) {

                $tax_details = TaxRate::find($transaction_data['tax_id']);

                $transaction_data['total_before_tax'] = $this->transactionUtil->calc_percentage_base($transaction_data['final_total'], $tax_details->amount);

                $transaction_data['tax_amount'] = $transaction_data['final_total'] - $transaction_data['total_before_tax'];

            }

            //Update reference count

            $ref_count = $this->transactionUtil->setAndGetReferenceCount('expense');

            //Generate reference number

            if (empty($transaction_data['ref_no'])) {

                $transaction_data['ref_no'] = $this->transactionUtil->generateReferenceNumber('expense', $ref_count);

            }
            Log::info("Start transaction");
            Log::info($transaction_data);

            //DB::beginTransaction();
            
            
            $transaction = Transaction::create($transaction_data);
            
            //Log::info("end of transaction");

            // add VAT components
            $this->transactionUtil->calculateAndUpdateVAT($transaction);


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


                    // post dated cheque input
                    $inputs['post_dated_cheque'] = $inputs['post_dated_cheque'] ?? 0;

                    $inputs['is_return'] =  0; //added by ahmed

                    unset($inputs['transaction_no_1']);

                    unset($inputs['transaction_no_2']);

                    unset($inputs['transaction_no_3']);

                    unset($inputs['controller_account']);



                    $tp = TransactionPayment::create($inputs);



                    //update payment status

                    $this->transactionUtil->updatePaymentStatus($transaction_id, $transaction->final_total);

                }

            }
            //$this->addAccountTransaction($transaction, $request, $business_id, $tp);

            $newReview = ["created_by" => request()->session()->get('user.id'),  "description" => "Created a new expense: ".$transaction_data['ref_no'],"module" => "expense"];
            $reviewed = $this->transactionUtil->reviewChange($transaction_data['transaction_date'],$newReview);

            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
            $accountName = Account::find($request->expense_account);
            $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for','expense_created')->first();

            if(!empty($msg_template)){
                $msg = $msg_template->sms_body;

                $msg = str_replace('{account}',!empty($accountName) ? $accountName->name : "",$msg);
                $msg = str_replace('{amount}',$this->transactionUtil->num_f($transaction->final_total),$msg);
                $msg = str_replace('{ref}',$transaction_data['ref_no'],$msg);
                $msg = str_replace('{staff}',auth()->user()->username,$msg);

                $phones = [];
                if(!empty($business->sms_settings)){
                    $phones = explode(',',str_replace(' ','',$business->sms_settings['msg_phone_nos']));
                }
                
                if(!empty($phones)){
                    $data = [
                        'sms_settings' => $sms_settings,
                        'mobile_number' => implode(',',$phones),
                        'sms_body' => $msg
                    ];
                    
                    $response = $this->businessUtil->sendSms($data,'credit_sale'); 
                }
            }



            FleetFuelDetail::create([
                'fleet_id' => $request->fleet_id,
                'driver_id' => $request->driver_id,
                'previous_odometer' => $request->previous_odometer,
                'current_odometer' => $request->current_odometer,
                'fuel_type' => $request->fuel_type,
                'liters' => $request->liters,
                'total_amount' => $request->total_amount,
                'fuel_cost' => $request->total_amount,
                'price_per_liter' => $request->price_per_liter,
                'date_of_operation' => $this->transactionUtil->uf_date($request->date),
                'business_id' => $business_id,
                'created_by' => Auth::user()->id
            ]);

            if(is_numeric($request ->fuel_type)) {
                $fleet = Fleet::find($request ->fuel_type);
                $fleet->fuel_type_id = $request ->fuel_type;
                $fleet->save();
            }

             DB::commit();

            $output = [
                'success' => true,
                'msg' => __('fleet::lang.success')
            ];
        } catch (\Exception $e) {
            Log::info($request);
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    public function updateFuelDetails(Request $request,$id)
    {
        try {
            $business_id = request()->session()->get('business.id');

            $inputs = $request->except('_token');
            $inputs['date_of_operation'] = $this->transactionUtil->uf_date($inputs['date']);
            $inputs['business_id'] = $business_id;
            unset($inputs['date']);
            $inputs['created_by'] = Auth::user()->id;
            DB::beginTransaction();
            $fleet = FleetFuelDetail::where('id',$id)->update($inputs);

            DB::commit();
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
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        $business_id = request()->session()->get('business.id');

        $fleet = Fleet::with('balanceDetail', 'balanceDetail.contact')->leftjoin('business_locations', 'fleets.location_id', 'business_locations.id')
            ->leftjoin('transactions AS t', 'fleets.id', '=', 't.fleet_id')
            ->leftjoin('transaction_payments', 't.id', 'transaction_payments.transaction_id')
            ->where('fleets.id', $id)
            ->select('fleets.*',
                DB::raw("SUM(IF(t.type = 'route_operation', final_total, 0)) as income"),
                DB::raw("SUM(IF(t.type = 'opening_balance', final_total, 0)) as opening_balance"),
                'business_locations.name as location_name')
            ->first();
        $income_acc = !empty(Account::where('id', $fleet->income_account_id)->first()) ? Account::where('id', $fleet->income_account_id)->first()->name : null;
        $expense_acc = !empty(Account::where('id', $fleet->expense_account_id)->first()) ? Account::where('id', $fleet->expense_account_id)->first()->name : null;

        $current_meter = !empty(RouteOperation::where('fleet_id', $id)->latest()->first()) ? $this->commonUtil->num_uf(RouteOperation::where('fleet_id', $id)->latest()->first()->actual_meter) : $fleet->starting_meter;
        $routes = Route::where('business_id', $business_id)->pluck('route_name', 'id');
        $view_type = request()->tab;
        $fleet_dropdown = Fleet::where('business_id', $business_id)->pluck('vehicle_number', 'id');
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        $payment_types = $this->commonUtil->payment_types();
        return view('fleet::fleet.show')->with(compact(
            'fleet',
            'view_type',
            'routes',
            'fleet_dropdown',
            'payment_types',
            'expense_categories',
            'current_meter',
            'income_acc',
            'expense_acc'
        ));
    }

    /**
     * Fetch account summary for fleet ledger
     * Task 6322 (NafMKD)
     *
     */
    public function fetchLedgerSummarised(Request $request){
        $id = $request->id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $summary = [];
        $account_summarys = Fleet::with(['balanceDetail', 'balanceDetail.contact'])->leftjoin('transactions AS t', 'fleets.id', '=', 't.fleet_id')
            ->leftjoin('transaction_payments', 't.id', 'transaction_payments.transaction_id')
            ->where('fleets.id', $id)
            ->select([
                't.final_total',
                DB::raw("SUM(IF(t.type = 'route_operation', transaction_payments.amount, 0)) as payment_received")
            ])->groupBy('t.id');

        if (!empty($start_date) && !empty($end_date)) {
            $account_summarys->whereDate('t.transaction_date', '>=', $start_date);
            $account_summarys->whereDate('t.transaction_date', '<=', $end_date);
        }

        $final_total=[];
        $payment_received=[];
        foreach($account_summarys->get() as $account_summary) {
            $final_total[]=$account_summary->final_total;
            $payment_received[]=$account_summary->payment_received;
        }

        $summary['final_total'] = array_sum($final_total);
        $summary['payment_received'] = array_sum($payment_received);
        $summary['balance'] = $this->commonUtil->num_f($summary['final_total'] - $summary['payment_received']);
        $summary['final_total'] = $this->commonUtil->num_f($summary['final_total']);
        $summary['payment_received'] = $this->commonUtil->num_f($summary['payment_received']);

        return response()->json($summary);
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $fleet = Fleet::find($id);
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $access_account = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $expense_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);
        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');

        $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_type_id)->pluck('name', 'id');
        // $income_accounts1 = ExpenseCategory::leftjoin('accounts', 'expense_account', 'accounts.id')
        //     ->where('expense_categories.business_id', $business_id)
        //     ->select(['expense_categories.name'])->get();
        //     logger($income_accounts1);
        // $expense_accounts = $income_accounts1->map(function ($val) {
        //     return $val->name;
        //     logger($expense_accounts);
        // });
$fuelTypes = FleetFuelType::where('status', '!=', 0)
    ->where('business_id', $business_id)
    ->orderBy('id', 'desc')
    ->get(['id', 'status', 'type']); // Get the collection without plucking specific columns

$fuelTypes = $fuelTypes->transform(function ($item, $key) {
    if ($item->status == 3) {
        $item->type = $item->type . "(new price)";
        unset($item->status); // Unset the 'status' property if it equals 3
    }
    return ['id' => $item->id, 'type' => $item->type];
})->keyBy('id')->map->type;
        return view('fleet::fleet.edit')->with(compact(
            'business_locations',
            'access_account',
            'income_accounts',
            'expense_accounts',
            'fleet',
            'fuelTypes'
        ));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        try {
            $inputs = $request->except('_token', '_method');
            $inputs['date'] = $this->transactionUtil->uf_date($inputs['date']);

            Fleet::where('id', $id)->update($inputs);

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
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        try {
            Fleet::where('id', $id)->delete();
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

        return $output;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function vehicle_check(Request $request)
    {
        if ($request->has('fleet_id')) {
            $data = Fleet::where('vehicle_number', $request->vehicle_number)->where('id', '!=', $request->fleet_id)->count();
        } else {
            $data = Fleet::where('vehicle_number', $request->vehicle_number)->count();
        }

        if ($data > 0) {
            $output = [
                'success' => false,
                'msg' => __('fleet::lang.vehicle_check')
            ];
        }

        return $output;
    }
    public function getLedger($id)
    {
        $business_id = request()->session()->get('business.id');

        if (request()->ajax()) {
            $fleets = Fleet::with(['balanceDetail', 'balanceDetail.contact'])->leftjoin('transactions AS t', 'fleets.id', '=', 't.fleet_id')
                ->leftjoin('business_locations', 'fleets.location_id', 'business_locations.id')
                ->leftjoin('route_operations', 't.id', 'route_operations.transaction_id')
                ->leftjoin('contacts', 'route_operations.contact_id', 'contacts.id')
                ->leftjoin('routes', 'route_operations.route_id', 'routes.id')
                ->leftjoin('transaction_payments', 't.id', 'transaction_payments.transaction_id')
                ->leftjoin('users', 'fleets.created_by', 'users.id')
                ->whereIn('t.type', ['route_operation','opening_balance','fleet_opening_balance'])
                ->where('t.fleet_id', $id)
                ->where('fleets.business_id', $business_id)
                ->select([
                    'fleets.*',
                    'route_operations.order_number',
                    'contacts.name as customer_name',
                    't.transaction_date',
                    't.final_total',
                    't.fleet_id',
                    't.invoice_no',
                    'routes.orignal_location',
                    'routes.destination',
                    'routes.distance',
                    'routes.route_name',
                    't.type',
                    'transaction_payments.method',
                    'business_locations.name as location_name',
                    DB::raw("SUM(IF(t.type = 'route_operation', transaction_payments.amount, 0)) as payment_received")
                ])->groupBy('t.id');

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $fleets->whereDate('t.transaction_date', '>=', request()->start_date);
                $fleets->whereDate('t.transaction_date', '<=', request()->end_date);
            }

            $fleets->groupBy('fleets.id');
            Session::put('balance', 0);
            // Session::put('first', 0);

            return DataTables::of($fleets)
                ->editColumn('transaction_date', function ($row) {
                    $html = '';

                    $html .= $this->commonUtil->format_date($row->transaction_date);

                    return $html;
                })
                ->addColumn('final_total', function ($row) {
                    $html = '';

                    $html .= '<span class="display_currency" data-currency_symbol="false" data-orig-value="' . $row->final_total . '">' . $row->final_total . '</span>';

                    return $html;
                })
                ->addColumn('payment_received', function ($row) {
                    $html = '';

                    $html .= '<span class="display_currency" data-currency_symbol="false" data-orig-value="' . $row->payment_received . '">' . $row->payment_received . '</span>';

                    return $html;
                })
                ->addColumn('balance', function ($row) {
                    $balance = Session::get('balance');
                    $html = '';

                    $balance = $balance +  $row->final_total - $row->payment_received;
                    Session::put('balance', $balance);

                    $html .= '<span class="display_currency" data-currency_symbol="false" data-orig-value="' . $balance . '">' . $balance . '</span>';

                    return $html;
                })
                ->addColumn('description', function ($row) {
                    $details = '';
                    if ($row->type == 'fleet_opening_balance') {
                        if ($row->final_total) {
                            $details .= '<p class="text-danger">Opening Balance</p>';
                        } else {
                            $details .= '<p>Opening Balance</p>';
                        }

                    }else{
                        $details .= '<b>'. __('fleet::lang.trip_no').': </b>'.$row->invoice_no. '<br>'.'<b>'. __('fleet::lang.trip').': </b>'.$row->route_name. '<br>'.'<b>'. __('fleet::lang.order_no').': </b>'.$row->order_number .'<br>'. '<b>' . __('fleet::lang.customer') . ':</b> ' . $row->customer_name . '<br>';
                    }

                    return $details;
                })
                ->editColumn('method', function ($row) {
                    $html = '';

                    if ($row->payment_status == 'due') {
                        return '';
                    }
                    if ($row->method == 'bank_transfer') {
                        $bank_acccount = Account::find($row->account_id);
                        if (!empty($bank_acccount)) {
                            $html .= '<b>Bank Name:</b> ' . $bank_acccount->name . '</hr>';
                            $html .= '<b>Account Number:</b> ' . $bank_acccount->account_number . '</br>';
                        }
                    } else {
                        $html .= ucfirst($row->method);
                    }

                    return $html;
                })

                ->addColumn('invoice_no', function ($row) {
                    $html = '';

                    $html .=  $row->invoice_no;

                    return $html;
                })
                ->addColumn('destination', function ($row) {
                    $html = '';

                    $html .=  $row->destination;

                    return $html;
                })
                ->addColumn('distance', function ($row) {
                    $html = '';

                    $html .=  $row->distance;

                    return $html;
                })
                ->removeColumn('id')
                ->rawColumns(['destination', 'distance', 'invoice_no', 'final_total', 'transaction_date', 'action', 'final_total', 'addColumn', 'payment_received', 'method', 'balance', 'description'])
                ->make(true);
        }
    }
}
