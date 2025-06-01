<?php

namespace Modules\Bakery\Http\Controllers;

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
use Modules\Bakery\Entities\BakeryOpeningBalance;
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
use Modules\Bakery\Entities\BakeryFleet;
use Modules\Fleet\Entities\Route;

use Modules\Fleet\Entities\FleetFuelType;
use Modules\Fleet\Entities\FleetFuelDetail;
use Modules\Bakery\Entities\BakeryDriver;


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
            $fleets = BakeryFleet::leftjoin('transactions AS t', 'bakery_fleets.id', '=', 't.fleet_id')
                ->leftjoin('business_locations', 'bakery_fleets.location_id', 'business_locations.id')
                ->leftjoin('transaction_payments', 't.id', 'transaction_payments.transaction_id')
                ->leftjoin('users', 'bakery_fleets.created_by', 'users.id')
                ->where('bakery_fleets.business_id', $business_id)
                ->select([
                    'bakery_fleets.*',
                    'business_locations.name as location_name',
                    DB::raw("SUM(IF(t.type = 'route_operation', final_total, 0)) as income"),
                    DB::raw("SUM(IF(t.type = 'route_operation', transaction_payments.amount, 0)) as total_received"),
                    DB::raw("SUM(IF(t.type = 'bakery_fleet_opening_balance', final_total, 0)) as opening_balance"),
                    DB::raw("SUM(IF(t.type = 'bakery_fleet_opening_balance', (SELECT SUM(IF(is_return = 1,-1*amount,amount)) FROM transaction_payments WHERE transaction_payments.transaction_id=t.id), 0)) as opening_balance_paid"),
                ]);

            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $fleets->whereDate('bakery_fleets.date', '>=', request()->start_date);
                $fleets->whereDate('bakery_fleets.date', '<=', request()->end_date);
            }
            if (!empty(request()->location_id)) {
                $fleets->where('bakery_fleets.location_id', request()->location_id);
            }
            if (!empty(request()->vehicle_number)) {
                $fleets->where('bakery_fleets.vehicle_number', request()->vehicle_number);
            }
            if (!empty(request()->vehicle_type)) {
                $fleets->where('bakery_fleets.vehicle_type', request()->vehicle_type);
            }
            if (!empty(request()->vehicle_brand)) {
                $fleets->where('bakery_fleets.vehicle_brand', request()->vehicle_brand);
            }
            if (!empty(request()->vehicle_model)) {
                $fleets->where('bakery_fleets.vehicle_model', request()->vehicle_model);
            }
            $fleets->groupBy('bakery_fleets.id')->orderBy('bakery_fleets.id','DESC');

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
                        $html .= '<li><a href="#" data-href="' . action('\Modules\Bakery\Http\Controllers\FleetController@edit', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                        // $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\FleetController@addFuelDetails', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"> ' . __("Bakeryfleet::lang.add_fuel_details") . '</a></li>';

                        $html .= '<li><a href="#" data-href="' . action('\Modules\Bakery\Http\Controllers\FleetController@destroy', [$row->id]) . '" class="delete-fleet"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '<li class="divider"></li>';

                        // $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\FleetController@show', [$row->id]) . '?tab=info" class="" ><i class="fa fa-info-circle"></i> ' . __("Bakeryfleet::lang.info") . '</a></li>';
                        // $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\FleetController@show', [$row->id]) . '?tab=ledger" class="" ><i class="fa fa-anchor"></i> ' . __("Bakeryfleet::lang.ledger") . '</a></li>';
                        // $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\FleetController@show', [$row->id]) . '?tab=income" class="" ><i class="fa fa-money"></i> ' . __("Bakeryfleet::lang.income") . '</a></li>';
                        // $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\FleetController@show', [$row->id]) . '?tab=expenses" class="" ><i class="fa fa-minus"></i> ' . __("Bakeryfleet::lang.expenses") . '</a></li>';


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
                    $html = '<span class="display_currency ob" data-currency_symbol="true" data-orig-value="' . $balance_value . '">' . $balance_value . '</span>&nbsp;&nbsp;&nbsp;<a href="#" data-href="' . action('\Modules\Bakery\Http\Controllers\FleetController@viewopeningbalance', [$row->id]) . '" class="btn-modal" data-container=".fleet_model"><i class="fa fa-eye" style="font-size: 125%;"></i></a>';


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

        $fleets = BakeryFleet::where('business_id', $business_id)->get();
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



        return view('bakery::fleet.index')->with(compact(
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
        $fleets = BakeryOpeningBalance::with('contact')->where('fleets_id', $id)->get();
        $fleet = BakeryFleet::findOrFail($id);
        $vehicle_number = $fleet->vehicle_number;
        $total_received = $fleets->sum('opening_amount');
        $opening_balance = $total_received;
        $to_be_added = $opening_balance - $total_received;


        return view('bakery::fleet.opening_balance')->with(compact(
            'fleets',
            'total_received',
            'opening_balance',
            'to_be_added',
            'vehicle_number'
        ));
    }

    public function editOpeningBalance($id){
        $fleets = BakeryOpeningBalance::findOrFail($id);
        $business_id = request()->session()->get('business.id');

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');

        $transactions = Transaction::where('invoice_no',$fleets->invoice_no)->first();

        return view('bakery::fleet.edit_opening_balance')->with(compact(
            'fleets','transactions','customers'
        ));
    }

    public function updateOpeningBalance(Request $request){

        try {
            $business_id = request()->session()->get('business.id');
            $fleets = BakeryOpeningBalance::findOrFail($request->id);
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
                'msg' => __('Bakeryfleet::lang.success')
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
        $code_for_vehicle =  BakeryFleet::where('business_id', $business_id)->count() + 1;

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
        
        $drivers = BakeryDriver::where('business_id', $business_id)->pluck('driver_name','id');

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

        return view('bakery::fleet.create')->with(compact(
            'business_locations',
            'access_account',
            'income_accounts',
            'expense_accounts',
            'code_for_vehicle',
            'customers',
            'max_vehicles',
            'vehicles_added','type','types','customer_groups','contact_id',
            'fuelTypes',
            'drivers'
        ));
    }

    public function opening_balance()
    {
        $business_id = request()->session()->get('business.id');

        $customers = Contact::where('business_id', $business_id)->where('type', 'customer')->pluck('name', 'id');

        return view('bakery::fleet.opening_balance')->with(compact('customers'));
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
            $fleet = BakeryFleet::create($inputs);

            if (!empty($request->opening_balance)) {
                
                $customer = explode(',', $request->new_contact_id);
                $amount = explode(',', $request->new_opening_amount);
                $notes = explode(',', $request->new_notes);
                $invoices = explode(',', $request->new_invoices);
                $dates = explode(',', $request->new_dates);

                $receivealbe_account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                $ob_account_id = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');


                foreach ($customer as $key => $data) {
                    $opening_balance = BakeryOpeningBalance::create(
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
                            'type' => 'bakery_fleet_opening_balance',
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
                        'sub_type' => 'bakery_fleet_opening_balance',
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
                'msg' => __('fleet::lang.success'),
                'data' => $fleet
            ];
            
            if (request()->ajax()) {
                return $output;
            }
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
            
            if (request()->ajax()) {
                return $output;
            }
        }

        return redirect()->back()->with('status', $output);
    }


    public function edit($id)
    {
        $fleet = BakeryFleet::find($id);
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::where('business_id', $business_id)->pluck('name', 'id');

        $access_account = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'access_account');
        $income_type_id = AccountType::getAccountTypeIdByName('Income', $business_id, true);
        $expense_type_id = AccountType::getAccountTypeIdByName('Expenses', $business_id, true);
        $income_accounts = Account::where('business_id', $business_id)->where('account_type_id', $income_type_id)->pluck('name', 'id');

        $expense_accounts = Account::where('business_id', $business_id)->where('account_type_id', $expense_type_id)->pluck('name', 'id');
       
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
        return view('bakery::fleet.edit')->with(compact(
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

            BakeryFleet::where('id', $id)->update($inputs);

            $output = [
                'success' => true,
                'msg' => __('Bakeryfleet::lang.success')
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
            BakeryFleet::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('Bakeryfleet::lang.success')
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
            $data = BakeryFleet::where('vehicle_number', $request->vehicle_number)->where('id', '!=', $request->fleet_id)->count();
        } else {
            $data = BakeryFleet::where('vehicle_number', $request->vehicle_number)->count();
        }

        if ($data > 0) {
            $output = [
                'success' => false,
                'msg' => __('Bakeryfleet::lang.vehicle_check')
            ];
        }

        return $output;
    }
}
