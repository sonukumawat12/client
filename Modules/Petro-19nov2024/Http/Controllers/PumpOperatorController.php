<?php

namespace Modules\Petro\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountTransaction;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
use App\Notifications\CustomerNotification;
use App\Product;
use App\System;
use App\Transaction;
use App\TransactionPayment;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Yajra\DataTables\Facades\DataTables;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Petro\Entities\PumpOperator;
use App\Utils\BusinessUtil;
use App\Utils\Util;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Modules\Petro\Entities\FuelTank;
use Modules\Petro\Entities\Pump;
use Modules\Petro\Entities\PumperDayEntry;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Modules\Petro\Entities\PumpOperatorPayment;
use Modules\Petro\Entities\PumpOperatorCommission;
use Modules\Petro\Entities\PetroShift;
use Spatie\Permission\Models\Role;
use Litespeed\LSCache\LSCache;

class PumpOperatorController extends Controller
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

        //barcode types
        $this->barcode_types = $this->productUtil->barcode_types();
    }


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id =  Auth::user()->business_id;
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {

            $business_id =  Auth::user()->business_id;
            if (request()->ajax()) {
                $query = PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->leftjoin('settlements', 'pump_operators.id', 'settlements.pump_operator_id')
                    ->where('pump_operators.business_id', $business_id)
                    ->select([
                        'pump_operators.*',
                        'settlements.settlement_no as st_no',
                        'pump_operators.id as pump_operator_id',
                        'business_locations.name as location_name',
                    ])->groupBy('pump_operators.id');

                if (!empty(request()->location_id)) {
                    $query->where('pump_operators.location_id', request()->location_id);
                }
                if (!empty(request()->pump_operator)) {
                    $query->where('pump_operators.id', request()->pump_operator);
                }
                if (!empty(request()->settlement_no)) {
                    $query->where('settlements.settlement_no', request()->settlement_no);
                }
                if (!empty(request()->status)) {
                    if (request()->status == 'active') {
                        $query->where('pump_operators.active', 1);
                    } else {
                        $query->where('pump_operators.active', 0);
                    }
                }
                if (!empty(request()->type)) {
                }

                $start_date = request()->start_date;
                $end_date = request()->end_date;
                $business_details = Business::find($business_id);


                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $business_id = session()->get('user.business_id');
                            $pay_excess_commission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pay_excess_commission');
                            $recover_shortage = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'recover_shortage');
                            $pump_operator_ledger = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_ledger');

                            $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">

                            <li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@show', [$row->id]) . '"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>
                            <li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@edit', [$row->id]) . '" class="edit_contact_button"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';
                            if (auth()->user()->can('pum_operator.active_inactive')) {
                                $html .= '<li class="divider"></li>';
                                if (!$row->active) {
                                    $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@toggleActivate', [$row->id]) . '" class="toggle_active_button"><i class="fa fa-check"></i> ' . __("lang_v1.activate") . '</a></li>';
                                } else {
                                    $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@toggleActivate', [$row->id]) . '" class="toggle_active_button"><i class="fa fa-times"></i> ' . __("lang_v1.deactivate") . '</a></li>';
                                }
                            }

                            $html .= '<li class="divider"></li>';
                            if ($pay_excess_commission) {
                                $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\ExcessComissionController@create', ['pump_operator_id' => $row->id]) . '" class="edit_contact_button"> ' . __("petro::lang.pay_excess_and_commission") . '</a></li>';
                            }
                            if ($recover_shortage) {
                                $html .= '<li><a href="' . action('\Modules\Petro\Http\Controllers\RecoverShortageController@create', ['pump_operator_id' => $row->id]) . '" class="edit_contact_button"> ' . __("petro::lang.recover_shortages") . '</a></li>';
                            }
                            $html .= '<li class="divider"></li>
                            <li>
                                <a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@show', [$row->id]) . "?view=contact_info" . '">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    ' . __("contact.contact_info", ["contact" => __("contact.contact")]) . '
                                </a>
                            </li>
                            ';

                            if ($pump_operator_ledger) {
                                $html .= '<li>
                                    <a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@show', [$row->id]) . "?view=ledger" . '">
                                        <i class="fa fa-anchor" aria-hidden="true"></i>
                                        ' . __("lang_v1.ledger") . '
                                    </a>
                                </li>';
                            }

                            $html .= '<li>
                                    <a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@listCommission', [$row->id]) . '">
                                        <i class="fa fa-anchor" aria-hidden="true"></i>
                                        ' . __("petro::lang.list_commission") . '
                                    </a>
                                </li>';

                            $html .= '<li>
                                <a href="' . action('\Modules\Petro\Http\Controllers\PumpOperatorController@show', [$row->id]) . "?view=documents_and_notes" . '">
                                    <i class="fa fa-paperclip" aria-hidden="true"></i>
                                     ' . __("lang_v1.documents_and_notes") . '
                                </a>
                            </li>

                        </ul></div>';

                            return $html;
                        }
                    )
                    ->editColumn('name', function ($row) {
                        $html = $row->name;
                        if ($row->is_default == 1) {
                            $html .= "&nbsp;<span class='badge bg-danger'>Default</span>";
                        }

                        return $html;
                    })
                    ->addColumn(
                        'pump_no',
                        ''
                    )
                    ->addColumn(
                        'settlement_no',
                        ''
                    )
                    ->addColumn(
                        'sold_fuel_qty',
                        function ($row) use ($business_details, $start_date, $end_date) {
                            $qty =    PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                                ->leftjoin('transactions', 'pump_operators.id', 'transactions.pump_operator_id')
                                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                                ->leftjoin('categories', 'products.category_id', 'categories.id')
                                ->where('transactions.type', 'sell')
                                ->where('categories.name', 'Fuel')
                                ->where('transactions.transaction_date', '>=', $start_date)
                                ->where('transactions.transaction_date', '<=', $end_date)
                                ->where('pump_operators.id', $row->pump_operator_id)
                                ->select([
                                    DB::raw('SUM(transaction_sell_lines.quantity) as sold_fuel_qty')
                                ])
                                ->groupBy('pump_operators.id')->first();

                            if (empty($qty->sold_fuel_qty)) {
                                return $this->productUtil->num_f(0, false, $business_details, true);
                            }
                            return  '<span class="display_currency sold_fuel_qty" data-orig-value="' . $qty->sold_fuel_qty . '" data-currency_symbol = true>' . $this->productUtil->num_f($qty->sold_fuel_qty, false, $business_details, true) . '</span>';
                        }
                    )
                    ->addColumn(
                        'sale_amount_fuel',
                        function ($row) use ($business_details, $start_date, $end_date) {
                            $amount =    PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                                ->leftjoin('transactions', 'pump_operators.id', 'transactions.pump_operator_id')
                                ->leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
                                ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
                                ->leftjoin('categories', 'products.category_id', 'categories.id')
                                ->where('transactions.type', 'sell')
                                ->where('categories.name', 'Fuel')
                                ->where('transactions.transaction_date', '>=', $start_date)
                                ->where('transactions.transaction_date', '<=', $end_date)
                                ->where('pump_operators.id', $row->pump_operator_id)
                                ->select([
                                    'pump_operators.*',
                                    'business_locations.name as location_name',
                                    DB::raw('SUM(transaction_sell_lines.quantity * unit_price) as sale_amount_fuel')
                                ])->first();
                            return  '<span class="display_currency sale_amount_fuel" data-orig-value="' . $amount->sale_amount_fuel . '" data-currency_symbol = true>' . $this->productUtil->num_f($amount->sale_amount_fuel, false, $business_details, false) . '</span>';
                        }
                    )
                    ->addColumn(
                        'current_balance',
                        function ($row) {
                            //$balance_due = $this->getLedgerDetailsForDateRange($row->pump_operator_id, $start_date,$end_date)['balance_due'];
                            $balance_due = $this->transactionUtil->getPumpOperatorBalance($row->pump_operator_id);
                            return  '<span class="display_currency current_balance" data-orig-value="' . $balance_due . '" data-currency_symbol = true>' . $this->productUtil->num_f($balance_due, false) . '</span>';
                        }
                    )
                    ->editColumn(
                        'excess_amount',
                        function ($row) use ($start_date, $end_date) {
                            $total_excess = $this->getLedgerDetailsForDateRange($row->pump_operator_id, $start_date, $end_date)['total_recovered_excess'];
                            return  '<span class="display_currency excess_amount" data-orig-value="' .  $total_excess . '" data-currency_symbol = true>' . $this->productUtil->num_f($total_excess, false) . '</span>';
                        }
                    )
                    ->editColumn(
                        'short_amount',
                        function ($row) use ($start_date, $end_date) {
                            $total_shortage = $this->getLedgerDetailsForDateRange($row->pump_operator_id, $start_date, $end_date)['total_short'];
                            return  '<span class="display_currency short_amount" data-orig-value="' . $total_shortage . '" data-currency_symbol = true>' . $this->productUtil->num_f($total_shortage, false) . '</span>';
                        }
                    )
                    ->editColumn(
                        'commission_type',
                        function ($row) {
                            return  ucfirst($row->commission_type);
                        }
                    )
                    ->editColumn(
                        'commission_rate',
                        function ($row) use ($business_details) {
                            return  '<span class="display_currency commission_ap" data-orig-value="' . $row->commission_ap . '" data-currency_symbol = false>' . $this->productUtil->num_f($row->commission_ap, false, $business_details, false) . '</span>';
                        }
                    )
                    ->addColumn(
                        'commission_amount',
                        function ($row) use ($business_details, $start_date, $end_date) {
                            $amount =   $this->transactionUtil->getPumpOperatorCommission($row->pump_operator_id, $start_date, $end_date);
                            return  '<span class="display_currency commission_amount" data-orig-value="' . $amount . '" data-currency_symbol = true>' . $this->productUtil->num_f($amount, false, $business_details, true) . '</span>';
                        }
                    )

                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['name', 'action', 'sold_fuel_qty', 'sale_amount_fuel', 'excess_amount', 'short_amount', 'commission_rate', 'commission_amount', 'current_balance'])
                    ->make(true);
            }
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');

        // dd($pump_operators);

        $pumps = Pump::where('pumps.business_id', $business_id)
            ->select('pumps.*')
            ->orderBy('pumps.id')
            ->get();
        
        foreach($pumps as $pump){
           
            $po_assign = PumpOperatorAssignment::leftjoin('pump_operators','pump_operators.id','pump_operator_assignments.pump_operator_id')
                            ->where('pump_operator_assignments.business_id', $business_id)->where('pump_operator_assignments.pump_id', $pump->id)
                            ->where('pump_operator_assignments.status', 'open')
                            ->select('pump_operator_assignments.pump_operator_id', 'pump_operators.name as pumper_name')
                            ->first();
            if(!empty($po_assign)){
                $pump->pumper_name = $po_assign->pumper_name;
                $pump->pump_operator_id = $po_assign->pump_operator_id;
            }
        }
            
        $business_locations = BusinessLocation::forDropdown($business_id);
        $default_location = current(array_keys($business_locations->toArray()));
        $payment_types = $this->productUtil->payment_types($default_location);
        $tanks = FuelTank::where('business_id', $business_id)->pluck('fuel_tank_number', 'id');
        $products = Product::leftjoin('categories', 'products.category_id', 'categories.id')->where('products.business_id', $business_id)->where('categories.name', 'Fuel')->pluck('products.name', 'products.id');
        $settlement_nos = [];
        
         $shifts = PetroShift::join('pump_operators','pump_operators.id','petro_shifts.pump_operator_id')->where('petro_shifts.business_id',$business_id)->select('pump_operators.name','petro_shifts.*')->orderBy('id','DESC');
        
        
        $shifts = $shifts->get();

        $message = $this->transactionUtil->getGeneralMessage('general_message_pump_management_checkbox');

        return view('petro::pump_operators.index')->with(compact(
            'business_locations',
            'pump_operators',
            'settlement_nos',
            'message',
            'payment_types',
            'pumps',
            'tanks',
            'products',
            'shifts'
        ));
    }

    public function listCommission($id)
    {
        $business_id =  Auth::user()->business_id;
        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {

            $start_date = request()->start_date;
            $end_date = request()->end_date;
            $business_details = Business::find($business_id);

            $business_id =  Auth::user()->business_id;
            if (request()->ajax()) {
                $query = PumpOperatorCommission::leftjoin('pump_operators', 'pump_operators.id', 'pump_operator_commission.pump_operator_id')
                    ->leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->leftjoin('meter_sales', 'pump_operator_commission.meter_sale_id', 'meter_sales.id')
                    ->leftjoin('pumps', 'meter_sales.pump_id', 'pumps.id')
                    ->leftjoin('settlements', 'meter_sales.settlement_no', 'settlements.id')
                    ->where('pump_operator_commission.transaction_date', '>=', $start_date)
                    ->where('pump_operator_commission.transaction_date', '<=', $end_date)
                    ->where('pump_operator_commission.pump_operator_id', $id)
                    ->select([
                        'pump_operator_commission.transaction_date',
                        'settlements.settlement_no',
                        'pumps.pump_no',
                        'meter_sales.discount_amount as sale_amount',
                        'pump_operator_commission.type',
                        'pump_operator_commission.value',
                        'pump_operator_commission.amount as commission_amount',
                    ]);


                if (!empty(request()->type)) {
                    $query->where('pump_operator_commission.type', request()->type);
                }


                $fuel_tanks = Datatables::of($query)

                    ->editColumn(
                        'sale_amount',
                        function ($row) {
                            return $this->productUtil->num_f($row->sale_amount, false);
                        }
                    )
                    ->editColumn(
                        'commission_amount',
                        function ($row) {
                            return $this->productUtil->num_f($row->commission_amount, false);
                        }
                    )
                    ->editColumn(
                        'value',
                        function ($row) {
                            return $this->productUtil->num_f($row->value, false);
                        }
                    )
                    ->editColumn(
                        'transaction_date',
                        function ($row) {
                            return $this->productUtil->format_date($row->transaction_date);
                        }
                    )
                    ->removeColumn('id');


                return $fuel_tanks->rawColumns([])
                    ->make(true);
            }
        }


        return view('petro::pump_operators.commission')->with(compact(
            'id'
        ));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('business.id');
        $locations = BusinessLocation::forDropdown($business_id);

        $commission_type_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'commission_type');
        $pump_operator_dashboard = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_dashboard');

        $generate_passcode = sprintf("%04d", rand(0, 9999));

        return view('petro::pump_operators.create')->with(compact('locations', 'commission_type_permission', 'pump_operator_dashboard', 'generate_passcode'));
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'address' => 'required',
            'location_id' => 'required',
            'email' => 'required|unique:users',
            'cnic' => 'required',
            'dob' => 'required',
            'commission_type' => 'required',
            'mobile' => 'required',
            'username' => 'required|unique:users',
            'transaction_date' => 'required|date',
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }

        $business_id = request()->session()->get('business.id');
        try {
            //Check if subscribed or not, then check for users quota
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            } else if (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action('ManageUserController@index'));
            }

            $has_reviewed = $this->transactionUtil->hasReviewed($request->input('transaction_date'));

            if (!empty($has_reviewed)) {
                $output              = [
                    'success' => 0,
                    'msg'     => __('lang_v1.review_first'),
                ];

                return redirect()->back()->with(['status' => $output]);
            }


            $reviewed = $this->transactionUtil->get_review($request->input('transaction_date'), $request->input('transaction_date'));

            if (!empty($reviewed)) {
                $output = [
                    'success' => 0,
                    'msg'     => "You can't add a pump operator for an already reviewed date",
                ];

                return redirect()->back()->with(['status' => $output]);
            }

            if ($request->is_default == 1) {
                PumpOperator::where('business_id', $business_id)->update(['is_default' => 0]);
            }


            $data = array(
                'business_id' => $business_id,
                'name' => $request->name,
                'address' => $request->address,
                'location_id' => $request->location_id,
                'cnic' => $request->cnic,
                'dob' => \Carbon::parse($request->dob)->format('Y-m-d'),
                'commission_type' => $request->commission_type,
                'commission_ap' => !empty($request->commission_ap) ? $request->commission_ap : 0.00,
                'mobile' => $request->mobile,
                'landline' => $request->landline,
                'status' => 1,
                'is_default' => $request->is_default,
                'can_fullscreen' => $request->can_fullscreen,
                'transaction_date' => $request->transaction_date
            );

            if (!empty($request->input('opening_balance'))) {
                if ($request->input('opening_balance_type') == 'shortage') {
                    $data['short_amount'] = $request->input('opening_balance');
                }

                if ($request->input('opening_balance_type') == 'excess') {
                    $data['excess_amount'] = $request->input('opening_balance');
                }
            }

            DB::beginTransaction();
            $pump_operator = PumpOperator::create($data);
            $this->createUser($request, $pump_operator);

            //Add opening balance
            if (!empty($request->input('opening_balance'))) {
                $this->transactionUtil->createOpeningBalanceTransactionForPumpOperator($business_id, $pump_operator->id, $request->input('opening_balance'), $request->input('opening_balance_type'), $request->location_id, $request->input('transaction_date'));
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_operator_add_success')
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

    public function createUser($request, $pump_operator)
    {
        $business_id = request()->session()->get('business.id');
        $pump_operator_data = array(
            'business_id' => $business_id,
            'surname' => '',
            'first_name' => $request->name,
            'last_name' => '',
            'email' => $request->email,
            'username' => $request->username,
            'password' => Hash::make($request->password),
            'contact_number' => $request->mobile,
            'address' => $request->address,
            'is_pump_operator' => 1,
            'pump_operator_id' => $pump_operator->id,
            'pump_operator_passcode' => $request->password
        );

        $user = User::create($pump_operator_data);
        $role = Role::where('name', 'Pump Operator#' . $business_id)->first();

        if (empty($role)) {
            $role = Role::create([
                'name' => 'Pump Operator#' . $business_id,
                'business_id' => $business_id,
                'is_service_staff' => 0
            ]);
            $role->givePermissionTo('pump_operator.dashboard');
        }
        $user->assignRole($role->name);

        return true;
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        $business_id =  Auth::user()->business_id;
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $pump_operator = PumpOperator::findOrFail($id);

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        //get contact view type : ledger, notes etc.
        $view_type = request()->get('view');
        if (is_null($view_type)) {
            $view_type = 'contact_info';
        }

        $pump_operator_ledger_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_ledger');

        return view('petro::pump_operators.show')
            ->with(compact('pump_operator', 'pump_operators', 'business_locations', 'view_type', 'pump_operator_ledger_permission'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $locations = BusinessLocation::forDropdown($business_id);

        $pump_operator = PumpOperator::findOrFail($id);
        $transaction = Transaction::where([
            'type' => 'opening_balance',
            'pump_operator_id' => $pump_operator->id,
            'business_id' => $business_id
        ])->first();
        // dd($transaction->transaction_date);
        
        // dump($transaction);exit;
        // logger(json_encode($transaction));

        $user = User::where('business_id', $business_id)->where('pump_operator_id', $id)->first();
        $pump_operator_dashboard = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_dashboard');

        return view('petro::pump_operators.edit')->with(compact('locations', 'pump_operator', 'user', 'pump_operator_dashboard', 'transaction'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update($id, Request $request)
    {
        $business_id = request()->session()->get('business.id');
        try {

            $has_reviewed = $this->transactionUtil->hasReviewed($request->transaction_date);

            if (!empty($has_reviewed)) {
                $output              = [
                    'success' => 0,
                    'msg'     => __('lang_v1.review_first'),
                ];

                return redirect()->back()->with(['status' => $output]);
            }


            $reviewed = $this->transactionUtil->get_review($request->transaction_date, $request->transaction_date);

            if (!empty($reviewed)) {
                $output = [
                    'success' => 0,
                    'msg'     => "You can't edit a pump operator for an already reviewed date",
                ];

                return redirect()->back()->with(['status' => $output]);
            }

            if ($request->is_default == 1) {
                PumpOperator::where('business_id', $business_id)->update(['is_default' => 0]);
            }

            $data = array(
                'business_id' => $business_id,
                'name' => $request->name,
                'address' => $request->address,
                'location_id' => $request->location_id,
                'commission_type' => $request->commission_type,
                'commission_ap' => $request->commission_ap,
                'mobile' => $request->mobile,
                'landline' => $request->landline,
                'status' => 1,
                'is_default' => $request->is_default,
                'can_fullscreen' => $request->can_fullscreen
            );

            PumpOperator::where('id', $id)->update($data);
            $pump_operator = PumpOperator::findOrFail($id);
            
            // print_r($request->input('opening_balance_type'));echo '<br>';exit;
            
            //Add opening balance
            $this->transactionUtil->updateOpeningBalanceTransactionForPumpOperator($business_id, $pump_operator->id, $request->input('opening_balance'), $request->input('opening_balance_type'), $request->location_id, $request->input('transaction_date'));

            $user = User::where('pump_operator_id', $id)->where('business_id', $business_id)->first();
            if (empty($user)) {
                $this->createUser($request, $pump_operator);
            } else {
                $user->email = $request->email;
                if (!empty($request->password)) {
                    $user->password = Hash::make($request->password);
                    $user->pump_operator_passcode = $request->password;
                }
                $user->save();
            }

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_operator_update_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
    
        // print_r($output);
        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
    /**
     * Import Operators
     * @return Response
     */
    public function importPumps()
    {
        $business_id = request()->session()->get('business.id');
        $business_locations = BusinessLocation::forDropdown($business_id);

        return view('petro::pump_operators.import_operators')->with(compact('business_locations'));
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

                    $pump_operator = [];
                    $pump_operator['business_id'] = $business_id;
                    $pump_operator['location_id'] = $location_id;
                    $pump_operator['commission_type'] = $type;

                    //Check if any column is missing
                    if (count($value) < 5) {
                        $is_valid =  false;
                        $error_msg = "Some of the columns are missing. Please, use latest CSV file template.";
                    }

                    $name = (trim($value[0]));
                    if ($name) {
                        $pump_operator['name'] = $name;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for pump operator name in row no. $row_no";
                        
                    }

                    $address = (trim($value[1]));
                    if ($address) {
                        $pump_operator['address'] = $address;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for address in row no. $row_no";
                        
                    }

                    $mobile = (trim($value[2]));
                    if ($mobile) {
                        $pump_operator['mobile'] = $mobile;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for mobile in row no. $row_no";
                        
                    }

                    $landline = (trim($value[3]));
                    if ($landline) {
                        $pump_operator['landline'] = $landline;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for landline in row no. $row_no";
                        
                    }

                    $dob =(trim($value[4]));
                    if ($dob) {
                        $pump_operator['dob'] = \Carbon::parse(strtotime($dob))->format('Y-m-d');
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for date of birth in row no. $row_no";
                        
                    }

                    $cnic = (trim($value[5]));
                    if ($cnic) {
                        $pump_operator['cnic'] = $cnic;
                    } else {
                        $is_valid = false;
                        $error_msg = "Invalid value for national identity number in row no. $row_no";
                        
                    }
                    
                    $ob = (trim($value[6]));
                    $transaction_date = (trim(strtotime($value[7]))) ?? date('Y-m-d');
                    $ob_type = strtolower(trim($value[8])) ?? 'excess';
                    
                    $email = (trim($value[9]));


                    if (!$is_valid) {
                        throw new \Exception($error_msg);
                        break;
                    }

                    $pump_operator['status'] = 1;

                    $pump_op = PumpOperator::create($pump_operator);
                    
                    if(!empty($email)){
                        $pass = rand(1111,9999);
                        
                        $pump_operator_data = array(
                            'business_id' => $business_id,
                            'surname' => '',
                            'first_name' => $pump_operator['name'],
                            'last_name' => '',
                            'email' => $email,
                            'username' => $email,
                            'password' => Hash::make($pass),
                            'contact_number' => $mobile,
                            'address' => $address,
                            'is_pump_operator' => 1,
                            'pump_operator_id' => $pump_op->id,
                            'pump_operator_passcode' => $pass
                        );
                
                        $user = User::create($pump_operator_data);
                        $role = Role::where('name', 'Pump Operator#' . $business_id)->first();
                
                        if (empty($role)) {
                            $role = Role::create([
                                'name' => 'Pump Operator#' . $business_id,
                                'business_id' => $business_id,
                                'is_service_staff' => 0
                            ]);
                            $role->givePermissionTo('pump_operator.dashboard');
                        }
                        $user->assignRole($role->name);
                    }
                    
                    
                    
                    if (!empty($ob)) {
                        $this->transactionUtil->createOpeningBalanceTransactionForPumpOperator(
                            $business_id, 
                            $pump_op->id, 
                            $ob, 
                            $ob_type, 
                            $request->location_id, 
                            $transaction_date);
                    }

                    $row_no++;
                }
                DB::commit();
            }

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_operator_import_success')
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => $e->getMessage()
            ];

            return redirect()->back()->with('notification', $output);
        }

        return redirect('/petro/pump-operators')->with('status', $output);
    }

    /**
     * Shows ledger for contacts
     *
     * @param  \Illuminate\Http\Request
     * @return \Illuminate\Http\Response
     */
    public function getLedger()
    {
        $business_id =  Auth::user()->business_id;
        $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')
            ->where('account_types.name', 'like', '%Assets%')
            ->where('accounts.business_id', $business_id)
            ->pluck('accounts.id')->toArray();
        $pump_operator_id = request()->input('pump_operator_id') ? request()->input('pump_operator_id') : $pump_operator_id;

        $start_date = request()->start_date;
        $end_date =  request()->end_date;

        $pump_operator = PumpOperator::find($pump_operator_id);
        $business_details = $this->businessUtil->getDetails($pump_operator->business_id);
        $location_details = BusinessLocation::where('business_id', $pump_operator->business_id)->first();
        $opening_balance = Transaction::where('pump_operator_id', $pump_operator_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');

        $query = Transaction::leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->whereIn('transactions.sub_type', ['excess', 'shortage'])
            ->where('transactions.status', 'final')
            ->where('transactions.pump_operator_id', $pump_operator_id)
            ->select(
                'business_locations.name as location_name',
                'transactions.invoice_no as ref_no',
                DB::raw('CASE 
                                WHEN transactions.type = "opening_balance" THEN "opening_balance"
                                ELSE transactions.sub_type
                            END as type'),
                'transactions.transaction_date as date',
                DB::raw('ABS(transactions.final_total) as amount'),
                DB::raw('"" as method'),
                'transactions.sub_type as sub_type'
            );

        $commission = PumpOperatorCommission::leftjoin('pump_operators', 'pump_operators.id', 'pump_operator_commission.pump_operator_id')
            ->leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
            ->leftjoin('meter_sales', 'pump_operator_commission.meter_sale_id', 'meter_sales.id')
            ->leftjoin('settlements', 'meter_sales.settlement_no', 'settlements.id')
            ->where('pump_operator_commission.transaction_date', '>=', $start_date)
            ->where('pump_operator_commission.transaction_date', '<=', $end_date)
            ->where('pump_operator_commission.pump_operator_id', $pump_operator_id)
            ->select([
                'business_locations.name as location_name',
                'settlements.settlement_no as ref_no',
                DB::raw('"commission" as type'),
                'pump_operator_commission.transaction_date as date',
                'pump_operator_commission.amount as amount',
                DB::raw('"" as method'),
                DB::raw('"" as sub_type')
            ]);


        $payments = Transaction::leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('transaction_payments', 'transaction_payments.transaction_id', 'transactions.id')


            ->where(function ($q) {
                $q->whereIn('transactions.sub_type', ['excess', 'shortage'])
                    ->orwhereIn('transactions.type', ['excess_bulk_payment', 'shortage_bulk_payment']);
            })

            ->where('transactions.status', 'final')
            ->where('transactions.pump_operator_id', $pump_operator_id)
            ->whereNull('transaction_payments.parent_id')
            ->selectRaw('
                                business_locations.name as location_name,
                                transaction_payments.payment_ref_no as ref_no,
                                CASE 
                                    WHEN transactions.sub_type = "excess" THEN "excess_paid"
                                    WHEN transactions.sub_type = "shortage" THEN "shortage_recovered"
                                    WHEN transactions.type = "excess_bulk_payment" THEN "excess_paid"
                                    WHEN transactions.type = "shortage_bulk_payment" THEN "shortage_recovered"
                                END as type,
                                transaction_payments.paid_on as date,
                                transaction_payments.amount as amount,
                                transaction_payments.method as method,
                                transactions.sub_type as sub_type
                    ');

        if (!empty($start_date)  && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date);
            $payments->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date);
        }

        $commissionResult = $commission->union($query)->orderBy('date', 'asc');
        $ledger_transactions = $commissionResult->union($payments)->orderBy('date', 'asc')->where('amount', '>',0)->get();


        $ledger_details = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_short' => 0,
            'total_recovered_excess' => 0,
            'beginning_balance' => $this->transactionUtil->getPumpOperatorBFBalance($pump_operator_id, $start_date),
            'balance_due' => 0
        ];



        $ledger_details['balance_due'] = $this->transactionUtil->getPumpOperatorBalance($pump_operator_id);;
        $payment_types = $this->transactionUtil->payment_types();

        if (request()->input('action') == 'pdf') {
            $for_pdf = true;
            $html = view('petro::pump_operators.ledger')
                ->with(compact('ledger_details', 'pump_operator', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'payment_types'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);
            $mpdf->Output();
        }
        if (request()->input('action') == 'print') {
            $for_pdf = true;
            return view('petro::pump_operators.ledger')
                ->with(compact('ledger_details', 'pump_operator', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details', 'payment_types'))->render();
        }

        return view('petro::pump_operators.ledger')
            ->with(compact('ledger_details', 'pump_operator', 'opening_balance', 'ledger_transactions', 'business_details', 'location_details', 'payment_types'));
    }

    private function getLedgerDetailsForDateRange($pump_operator_id, $start_date, $end_date)
    {
        $query = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
            ->leftjoin('transaction_payments', 'account_transactions.transaction_payment_id', 'transaction_payments.id')
            ->where('pump_operator_id', $pump_operator_id)
            ->where('account_transactions.sub_type', 'ledger_show')
            ->whereIn('transactions.sub_type', ['excess', 'shortage'])
            ->select(
                'account_transactions.*',
                'account_transactions.type as acc_transaction_type',
                'business_locations.name as location_name',
                'transactions.ref_no',
                'transactions.invoice_no',
                'transactions.sub_type',
                'transactions.transaction_date',
                'transactions.payment_status',
                'transaction_payments.method as payment_method',
                'transaction_payments.payment_ref_no',
                'transaction_payments.id as tp_id',
                'transaction_payments.paid_on',
                'transactions.type as transaction_type',
                DB::raw('(SELECT SUM(IF(AT.type="credit", -1 * AT.amount, AT.amount)) from account_transactions as AT WHERE AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id <= account_transactions.id) as balance')
            );

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date);
        }

        $ledger_transactions = $query->groupBy('account_transactions.id')->orderBy('account_transactions.id', 'asc')->withTrashed()->get();

        $total_short = $total_recovered_excess = 0;
        foreach ($ledger_transactions as $transaction) {
            if ($transaction->acc_transaction_type === 'debit') {
                $total_short += $transaction->amount;
            }

            if ($transaction->acc_transaction_type === 'credit') {
                $total_recovered_excess += $transaction->amount;
            }
        }

        $beginning_balance = $this->getOneDayBeforeDueBalance($pump_operator_id, $start_date);
        $balance_due = ($total_short + $beginning_balance) - $total_recovered_excess;

        return [
            'total_short' => $total_short,
            'total_recovered_excess' => $total_recovered_excess,
            'balance_due' => $balance_due
        ];
    }

    private function getOneDayBeforeDueBalance($pump_operator_id, $start_date)
    {
        $start_date = $end_date = (new \DateTime($start_date))->modify('-1 days');
        $query = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
            ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
            ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
            ->leftjoin('transaction_payments', 'account_transactions.transaction_payment_id', 'transaction_payments.id')
            ->where('pump_operator_id', $pump_operator_id)
            ->where('account_transactions.sub_type', 'ledger_show')
            ->whereIn('transactions.sub_type', ['excess', 'shortage'])
            ->select(
                'account_transactions.*',
                'account_transactions.type as acc_transaction_type',
                'business_locations.name as location_name',
                'transactions.ref_no',
                'transactions.invoice_no',
                'transactions.sub_type',
                'transactions.transaction_date',
                'transactions.payment_status',
                'transaction_payments.method as payment_method',
                'transaction_payments.payment_ref_no',
                'transaction_payments.id as tp_id',
                'transaction_payments.paid_on',
                'transactions.type as transaction_type',
                DB::raw('(SELECT SUM(IF(AT.type="credit", -1 * AT.amount, AT.amount)) from account_transactions as AT WHERE AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id <= account_transactions.id) as balance')
            );

        if (!empty($start_date) && !empty($end_date)) {
            $query->whereDate('transactions.transaction_date', '>=', $start_date)->whereDate('transactions.transaction_date', '<=', $end_date);
        }

        $ledger_transactions = $query->groupBy('account_transactions.id')->orderBy('account_transactions.id', 'asc')->withTrashed()->get();

        $total_short = $total_recovered_excess = 0;
        foreach ($ledger_transactions as $transaction) {
            if ($transaction->acc_transaction_type === 'debit') {
                $total_short += $transaction->amount;
            }

            if ($transaction->acc_transaction_type === 'credit') {
                $total_recovered_excess += $transaction->amount;
            }
        }

        return $total_short - $total_recovered_excess;
    }

    /**
     * Function to get ledger details
     *
     */
    private function __getLedgerDetails($pump_operator_id, $start_date, $end_date)
    {
        $business_id =  Auth::user()->business_id;
        $contact = PumpOperator::where('id', $pump_operator_id)->first();
        //Get transaction totals between dates

        $pump_op_query = Transaction::where('business_id', $business_id)
            ->where('type', 'settlement')
            ->whereIn('sub_type', ['excess', 'shortage'])
            ->where('pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select(
                DB::raw("SUM(IF(transactions.sub_type = 'excess', ABS(final_total), 0)) as excess"),
                DB::raw("SUM(IF(transactions.sub_type = 'shortage', final_total, 0)) as shortage")
            )->first();

        $total_paid_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->where('type', 'settlement')
            ->whereIn('sub_type', ['excess', 'shortage'])
            ->where('pump_operator_id', $pump_operator_id)
            ->whereDate('transaction_payments.paid_on', '>=', $start_date)
            ->whereDate('transaction_payments.paid_on', '<=', $end_date)
            ->select(
                DB::raw("SUM(IF(transactions.sub_type = 'excess', ABS(transaction_payments.amount), 0)) as excess_paid"),
                DB::raw("SUM(IF(transactions.sub_type = 'shortage', transaction_payments.amount, 0)) as shortage_recovered")
            )->first();

        $total_short  = $pump_op_query->shortage + $total_paid_query->excess_paid;
        $total_recovered_excess  = $pump_op_query->excess + $total_paid_query->shortage_recovered;

        $beginning_balance = $this->getBeginningBalance($pump_operator_id, $start_date, $end_date);
        $balance_due = $beginning_balance + $total_short - $total_recovered_excess;
        $output = [
            'start_date' => $start_date,
            'end_date' => $end_date,
            'total_short' => $total_short,
            'total_recovered_excess' => $total_recovered_excess,
            'beginning_balance' => $beginning_balance,
            'balance_due' => $balance_due
        ];

        return $output;
    }

    private function getBeginningBalance($pump_operator_id, $start_date)
    {
        $business_id =  Auth::user()->business_id;
        $pump_op_query = Transaction::where('business_id', $business_id)
            ->where('transactions.type', 'settlement')
            ->whereIn('transactions.sub_type', ['excess', 'shortage'])
            ->where('pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '<', $start_date)
            ->select(
                DB::raw("SUM(IF(transactions.sub_type = 'excess', final_total, 0)) as excess"),
                DB::raw("SUM(IF(transactions.sub_type = 'shortage', final_total, 0)) as shortage")
            )->first();

        $total_paid_query = Transaction::leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'settlement')
            ->whereIn('transactions.sub_type', ['excess', 'shortage'])
            ->where('pump_operator_id', $pump_operator_id)
            ->whereDate('transaction_payments.paid_on', '<', $start_date)
            ->select(
                DB::raw("SUM(IF(transactions.sub_type = 'excess', transaction_payments.amount, 0)) as excess_paid"),
                DB::raw("SUM(IF(transactions.sub_type = 'shortage', transaction_payments.amount, 0)) as shortage_recovered")
            )->first();

        $total_short  = $pump_op_query->shortage + $total_paid_query->excess_paid;
        $total_recovered_excess  = $pump_op_query->excess + $total_paid_query->shortage_recovered;

        $balance_due = $total_short - $total_recovered_excess;

        return $balance_due;
    }

    /**
     * Query to get transaction totals for a customer
     *
     */
    private function __transactionQuery($pump_operator_id, $start, $end = null)
    {
        $business_id =  Auth::user()->business_id;
        $transaction_type_keys = array_keys(Transaction::transactionTypes());

        $query = Transaction::where('transactions.pump_operator_id', $pump_operator_id)
            ->where('transactions.business_id', $business_id)
            ->where('status', '!=', 'draft')
            ->whereIn('type', $transaction_type_keys);

        if (!empty($start)  && !empty($end)) {
            $query->whereDate(
                'transactions.transaction_date',
                '>=',
                $start
            )
                ->whereDate('transactions.transaction_date', '<=', $end)->get();
        }

        if (!empty($start)  && empty($end)) {
            $query->whereDate('transactions.transaction_date', '<', $start);
        }

        return $query;
    }

    /**
     * Query to get payment details for a customer
     *
     */
    private function __paymentQuery($pump_operator_id, $start, $end = null)
    {
        $business_id =  Auth::user()->business_id;

        $query = TransactionPayment::join(
            'transactions as t',
            'transaction_payments.transaction_id',
            '=',
            't.id'
        )
            ->leftJoin('business_locations as bl', 't.location_id', '=', 'bl.id')
            ->where('t.pump_operator_id', $pump_operator_id)
            ->where('t.business_id', $business_id)
            ->where('t.status', '!=', 'draft');

        if (!empty($start)  && !empty($end)) {
            $query->whereDate('paid_on', '>=', $start)
                ->whereDate('paid_on', '<=', $end);
        }

        if (!empty($start)  && empty($end)) {
            $query->whereDate('paid_on', '<', $start);
        }

        return $query;
    }

    /**
     * Function to send ledger notification
     *
     */
    public function sendLedger(Request $request)
    {
        $notAllowed = $this->notificationUtil->notAllowedInDemo();
        if (!empty($notAllowed)) {
            return $notAllowed;
        }

        try {
            $data = $request->only(['to_email', 'subject', 'email_body', 'cc', 'bcc']);
            $emails_array = array_map('trim', explode(',', $data['to_email']));

            $pump_operator_id = $request->input('pump_operator_id');
            $business_id = request()->session()->get('business.id');

            $start_date = request()->input('start_date');
            $end_date =  request()->input('end_date');

            $pump_operator = PumpOperator::find($pump_operator_id);

            $asset_account_id = Account::leftjoin('account_types', 'accounts.account_type_id', 'accounts.id')
                ->where('account_types.name', 'like', '%Assets%')
                ->where('accounts.business_id', $business_id)
                ->pluck('accounts.id')->toArray();

            $ledger_details = $this->__getLedgerDetails($pump_operator_id, $start_date, $end_date);

            $business_details = $this->businessUtil->getDetails($pump_operator->business_id);
            $location_details = BusinessLocation::where('business_id', $pump_operator->business_id)->first();
            $opening_balance = Transaction::where('pump_operator_id', $pump_operator_id)->where('type', 'opening_balance')->where('payment_status', 'due')->sum('final_total');


            $query = AccountTransaction::leftjoin('transactions', 'account_transactions.transaction_id', 'transactions.id')
                ->leftjoin('transaction_payments', 'transactions.id', 'transaction_payments.transaction_id')
                ->leftjoin('business_locations', 'transactions.location_id', 'business_locations.id')
                ->leftjoin('accounts', 'account_transactions.account_id', 'accounts.id')
                ->where('transactions.type', 'sell')
                ->orWhere('transactions.type', 'opening_balance')
                ->where('pump_operator_id', $pump_operator_id)
                ->select(
                    'account_transactions.*',
                    'account_transactions.type as acc_transaction_type',
                    'business_locations.name as location_name',
                    'transactions.ref_no',
                    'transactions.transaction_date',
                    'transactions.payment_status',
                    'transaction_payments.method as payment_method',
                    'transactions.type as transaction_type',
                    DB::raw('(SELECT SUM(IF(AT.type="credit", -1 * AT.amount, AT.amount)) from account_transactions as AT WHERE AT.operation_date <= account_transactions.operation_date AND AT.account_id  =account_transactions.account_id AND AT.deleted_at IS NULL AND AT.id <= account_transactions.id) as balance')
                );

            if (!empty($start_date)  && !empty($end_date)) {
                $query->whereDate(
                    'transactions.transaction_date',
                    '>=',
                    $start_date
                )->whereDate('transactions.transaction_date', '<=', $end_date)->get();
            }
            $ledger_transactions = $query->get();

            $orig_data = [
                'email_body' => $data['email_body'],
                'subject' => $data['subject']
            ];

            $tag_replaced_data = $this->notificationUtil->replaceTags($business_id, $orig_data, null, $contact);
            $data['email_body'] = $tag_replaced_data['email_body'];
            $data['subject'] = $tag_replaced_data['subject'];

            //replace balance_due
            $data['email_body'] = str_replace('{balance_due}', $this->notificationUtil->num_f($ledger_details['balance_due']), $data['email_body']);

            $data['email_settings'] = request()->session()->get('business.email_settings');


            $for_pdf = true;
            $html = view('petro::pump_operators.ledger')
                ->with(compact('ledger_details', 'pump_operator', 'for_pdf', 'ledger_transactions', 'business_details', 'location_details'))->render();
            $mpdf = $this->getMpdf();
            $mpdf->WriteHTML($html);

            $file = config('constants.mpdf_temp_path') . '/' . time() . '_ledger.pdf';
            $mpdf->Output($file, 'F');

            $data['attachment'] =  $file;
            $data['attachment_name'] =  'ledger.pdf';
            \Notification::route('mail', $emails_array)
                ->notify(new CustomerNotification($data));

            if (file_exists($file)) {
                unlink($file);
            }

            $output = ['success' => 1, 'msg' => __('lang_v1.notification_sent_successfully')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage()
            ];
        }

        return $output;
    }


    public function getReport()
    {
        $business_id =  Auth::user()->business_id;

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            if (request()->ajax()) {
                $query = PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->where('pump_operators.business_id', $business_id)
                    ->select([
                        'pump_operators.*',
                        'business_locations.name as location_name',
                    ]);

                if (!empty(request()->location_id)) {
                }
                if (!empty(request()->pump_operator)) {
                }
                if (!empty(request()->settlement_no)) {
                }
                if (!empty(request()->type)) {
                }
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                }

                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'pump_no',
                        ''
                    )
                    ->addColumn(
                        'settlement_no',
                        ''
                    )
                    ->addColumn(
                        'pumped_fuel_ltrs',
                        ''
                    )
                    ->addColumn(
                        'amount',
                        ''
                    )
                    ->addColumn(
                        'commission_rate',
                        '{{$commission_type}}'
                    )
                    ->addColumn(
                        'commission_amount',
                        '{{$commission_ap}}'
                    )

                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action'])
                    ->make(true);
            }
        }
        $business_locations = BusinessLocation::forDropdown($business_id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'id');
        $settlement_nos = [];

        return view('petro::pump_operators.pump_operator_report')->with(compact('business_locations', 'pump_operators', 'settlement_nos'));
    }


    /**
     * Display a excess and shortage of the resource.
     * @return Response
     */
    public function getPumperExcessShortagePayments()
    {
        $business_id =  Auth::user()->business_id;

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module')) {
            abort(403, 'Unauthorized Access');
        }

        if (request()->ajax()) {
            $business_id =  Auth::user()->business_id;
            $payment_types = $this->productUtil->payment_types();
            if (request()->ajax()) {
                $query = PumpOperator::leftjoin('business_locations', 'pump_operators.location_id', 'business_locations.id')
                    ->leftjoin('transactions', 'pump_operators.id', 'transactions.pump_operator_id')
                    ->leftjoin('transaction_payments', function ($join) {
                        $join->on('transactions.id', 'transaction_payments.transaction_id')->whereNull('transaction_payments.deleted_at');
                    })
                    ->where('pump_operators.business_id', $business_id)
                    ->where('transactions.type', 'settlement')
                    ->whereIn('transactions.sub_type', ['shortage', 'excess'])
                    ->select([
                        'pump_operators.*',
                        'transactions.id as t_id',
                        'transactions.type',
                        'transactions.sub_type',
                        'transactions.final_total',
                        'transactions.transaction_date',
                        'pump_operators.id as pump_operator_id',
                        'business_locations.name as location_name',
                        'transaction_payments.amount',
                        'transaction_payments.method',
                        'transaction_payments.id as tp_id',
                        'transaction_payments.paid_on',
                        'transaction_payments.payment_ref_no'
                    ])->groupBy('transaction_payments.id');

                if (!empty(request()->location_id)) {
                    $query->where('transactions.location_id', request()->location_id);
                }
                if (!empty(request()->pump_operator)) {
                    $query->where('transactions.pump_operator_id', request()->pump_operator);
                }

                if (!empty(request()->type)) {
                    $query->where('transactions.sub_type', request()->type);
                }
                if (!empty(request()->payment_type)) {
                    $query->where('transaction_payments.method', request()->payment_type);
                }
                if (!empty(request()->start_date) && !empty(request()->end_date)) {
                    $query->whereDate('transaction_payments.paid_on', '>=', request()->start_date);
                    $query->whereDate('transaction_payments.paid_on', '<=', request()->end_date);
                }
                $business_id = session()->get('user.business_id');
                $business_details = Business::find($business_id);


                $fuel_tanks = Datatables::of($query)
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

                            if (!empty($row->tp_id)) {
                                $html .= '<li><a href="' . action('TransactionPaymentController@show', [$row->t_id]) . '" class="view_payment_modal"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';

                                if ($row->sub_type == 'shortage') {
                                    $html .= '<li><a href="#" data-href="' . action("\Modules\Petro\Http\Controllers\RecoverShortageController@edit", [$row->tp_id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>
                                    <li><a href="#" data-href="' . action("\Modules\Petro\Http\Controllers\RecoverShortageController@destroy", [$row->tp_id]) . '" class="delete_payment" ><i class="fa fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</a></li>';
                                }
                                if ($row->sub_type == 'excess') {
                                    $html .= '<li><a href="#" data-href="' . action("\Modules\Petro\Http\Controllers\ExcessComissionController@edit", [$row->tp_id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit" aria-hidden="true"></i> ' . __("messages.edit") . '</a></li>
                                    <li><a href="#" data-href="' . action("\Modules\Petro\Http\Controllers\ExcessComissionController@destroy", [$row->tp_id]) . '" class="delete_payment" ><i class="fa fa-trash" aria-hidden="true"></i> ' . __("messages.delete") . '</a></li>';
                                }
                            }

                            $html .= '</ul></div>';

                            return $html;
                        }
                    )
                    ->editColumn(
                        'paid_on',
                        '{{@format_date($paid_on)}}'
                    )
                    ->editColumn(
                        'excess_amount',
                        function ($row) use ($business_details) {
                            if ($row->sub_type == 'excess') {
                                return  '<span class="display_currency excess_amount" data-orig-value="' . $row->final_total . '" data-currency_symbol = true>' . $this->productUtil->num_f($row->final_total, false, $business_details, false) . '</span>';
                            }
                            return $this->productUtil->num_f(0, false, $business_details, false);
                        }
                    )
                    ->editColumn(
                        'short_amount',
                        function ($row) use ($business_details) {
                            if ($row->sub_type == 'shortage') {
                                return  '<span class="display_currency short_amount" data-orig-value="' . $row->final_total . '" data-currency_symbol = true>' . $this->productUtil->num_f($row->final_total, false, $business_details, false) . '</span>';
                            }
                            return $this->productUtil->num_f(0, false, $business_details, false);
                        }
                    )
                    ->addColumn('shortage_recover', function ($row) use ($business_details) {
                        if ($row->sub_type == 'shortage') {
                            return $this->productUtil->num_f($row->amount, false, $business_details, false);
                        }
                        return $this->productUtil->num_f(0, false, $business_details, false);
                    })
                    ->addColumn('excess_paid', function ($row) use ($business_details, $payment_types) {
                        $method = '';
                        if (!empty($row->method)) {
                            $method = $payment_types[$row->method];
                        }
                        if ($row->sub_type == 'excess') {
                            return $this->productUtil->num_f($row->amount, false, $business_details, false) . ' ' . $method;
                        }
                        return $this->productUtil->num_f(0, false, $business_details, false);
                    })

                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['action', 'sold_fuel_qty', 'sale_amount_fuel', 'excess_amount', 'short_amount', 'commission_rate', 'commission_amount'])
                    ->make(true);
            }
        }
    }

    public function toggleActivate($id)
    {
        try {
            $pump_operator = PumpOperator::findOrFail($id);
            $pump_operator->active = !$pump_operator->active;
            $pump_operator->save();

            $output = [
                'success' => true,
                'msg' => __('lang_V1.success')
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

    public function dashboard_settings()
    {
        $business_id = Auth::user()->business_id;
        $pump_operator = PumpOperator::findOrFail(Auth::user()->pump_operator_id);
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'name');

        $card_types = [];
        $card_group = AccountGroup::where('business_id', $business_id)->where('name', 'Card')->first();
        if (!empty($card_group)) {
            $card_types = Account::where('business_id', $business_id)->where('asset_type', $card_group->id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        }

        return view('petro::pump_operators.dashboard_settings')->with(compact(
            'business_id',
            'pump_operator',
            'card_types',
            'pump_operators'
        ));
    }
    
    public function store_settings(Request $request){
        try {
            DB::beginTransaction();
                $data = $request->only('created_at','user_added','show_bulk_pumps','card_type','credit_sales_direct_to_customer','logoff_time', 'logoff', 'meter_sales_compulsory','enter_cash_denominations','card_amount_to_enter');
                
                if($request->is_admin == 1){
                    PumpOperator::where('business_id', Auth::user()->business_id)->update(['dashboard_settings' => json_encode($data)]);
                }else{
                    if(Auth::user()->pump_operator_id != 0)
                        $pump_operator = PumpOperator::findOrFail(Auth::user()->pump_operator_id);
                    else  $pump_operator = PumpOperator::where('business_id', Auth::user()->business_id)->where('is_default', '1')->first() ?? PumpOperator::findOrFail(1);
                    $pump_operator->dashboard_settings = json_encode($data);
                    $pump_operator->save();
                }
                
            DB::commit();

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
    
    public function update_passcode()
    {
        $user = User::find(auth()->user()->id);

        return view('petro::pump_operators.update_passcode')->with(compact(
            'user'
        ));
    }

    public function store_passcode(Request $request)
    {
        try {
            $user = User::find(auth()->user()->id);

            if (strlen($request->current_pass) < 4) {
                $output = [
                    'success' => 0,
                    'msg' => __('petro::lang.need_longer_pass')
                ];
                return redirect()->back()->with('status', $output);
            }

            if ($request->current_pass == $user->pump_operator_passcode) {
                $output = [
                    'success' => 0,
                    'msg' => __('petro::lang.need_new_password')
                ];
                return redirect()->back()->with('status', $output);
            }


            DB::beginTransaction();
            $user->pump_operator_passcode = $request->current_pass;
            $user->pump_operator_pass_changed = 1;
            $user->save();
            DB::commit();

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


    public function dashboard()
    {
        if (empty(Auth::user()->pump_operator_id)) {

            // if is admin
            $pump_op = PumpOperator::where('business_id', Auth::user()->business_id)->where('is_default', '1')->first();

            if (!empty($pump_op)) {
                $user = User::where('pump_operator_id', $pump_op->id)->first();
                if (!empty($user)) {
                    request()->session()->put('from_admin', auth()->user()->id);
                    // request()->session()->flush();
                    LSCache::purge('*');
                    Auth::logout();

                    Auth::loginUsingId($user->id);
                }
            }
        }

        $business_id = Auth::user()->business_id;
        $pump_operator_id = Auth::user()->pump_operator_id;

        $fy = $this->businessUtil->getCurrentFinancialYear($business_id);
        $date_filters['this_fy'] = $fy;
        $date_filters['this_month']['start'] = date('Y-m-01');
        $date_filters['this_month']['end'] = date('Y-m-t');
        $date_filters['this_week']['start'] = date('Y-m-d', strtotime('monday this week'));
        $date_filters['this_week']['end'] = date('Y-m-d', strtotime('sunday this week'));

        $fuel_tanks = FuelTank::where('business_id', $business_id)->get();

        $general_message = '';

        if (!$this->moduleUtil->hasThePermissionInSubscription($business_id, 'pump_operator_dashboard')) {
            if (System::getProperty('general_message_pump_operator_dashbaord_checkbox') == 1) {
                $font_size = System::getProperty('customer_supplier_security_deposit_current_liability_font_size');
                $color = System::getProperty('customer_supplier_security_deposit_current_liability_color');
                $msg = System::getProperty('customer_supplier_security_deposit_current_liability_message');
                $general_message = '<p style="font-size: ' . $font_size . ';color: ' . $color . ' ">' . $msg . '</p>';
            }
        }

        $today_close_pump_count = PumpOperatorAssignment::where('business_id', $business_id)->where('pump_operator_id', $pump_operator_id)->where('status', 'open')->count();

        $can_close_shift = true;
        if ($today_close_pump_count == 0) {
            $can_close_shift = true;
        }

        if (empty(session()->get('pump_operator_main_system'))) {
            $layout = 'pumper';
        } else {
            $layout = 'app';
        }
        
        $_settings = PumpOperator::findOrFail($pump_operator_id)->dashboard_settings;
        
        $dashboard_settings = !empty($_settings) ? json_decode($_settings,true) : [];
        
        
        $pending_pumps = PumpOperatorAssignment::whereNotNull('shift_id')
                            ->where('pump_operator_id',$pump_operator_id)
                            ->whereNull('pump_operator_assignments.pump_operator_other_sale_id')->where('is_confirmed',1)->count();
        
        // if(!empty($dashboard_settings) && !empty($dashboard_settings['meter_sales_compulsory']) && $dashboard_settings['meter_sales_compulsory'] == 'yes' && $pending_pumps > 0){
        //     $output = [
        //         'success' => false,
        //         'msg' => __('petro::lang.you_must_first_enter_meter_reading')
        //     ];
            
        //     return redirect('/petro/pump-operator-payments/create')->with('status', $output);
        // }
        
        
        $unconfirmed_meters = PumpOperatorAssignment::where('pump_operator_id',$pump_operator_id)->whereNotNull('shift_id')->where('is_confirmed',0)->count();
        
        return view('petro::pump_operators.dashboard')->with(compact(
            'general_message',
            'fuel_tanks',
            'layout',
            'can_close_shift',
            'date_filters',
            'pump_operator_id','unconfirmed_meters'
        ));
    }
    
    public function setting_dash()
    {
        $card_types = [];
        $business_id =  Auth::user()->business_id;
        $card_group = AccountGroup::where('business_id', $business_id)->where('name', 'Card')->first();
        if (!empty($card_group)) {
            $card_types = Account::where('business_id', $business_id)->where('asset_type', $card_group->id)->where(DB::raw("REPLACE(`name`, '  ', ' ')"), '!=', 'Cards (Credit Debit) Account')->pluck('name', 'id');
        }
        
        $pump_operators = PumpOperator::where('business_id', $business_id)->pluck('name', 'name');
        
        $settings = $pump_operators = PumpOperator::where('business_id', $business_id)->whereNotNull('dashboard_settings')->first();
        

        return view('petro::pump_operators.setting_dash')->with(compact(
            'card_types',
            'pump_operators',
            'business_id',
            'settings'
        ));
    }

    public function getAssignedPumps($id)
    {
        $business_id =  Auth::user()->business_id;

        $pumps = Pump::join('pump_operator_assignments', function ($join) {
            $join->on('pumps.id', 'pump_operator_assignments.pump_id')->where('status', '!=', 'close');
        })
            ->leftjoin('pump_operators', 'pump_operator_assignments.pump_operator_id', 'pump_operators.id')
            ->where('pumps.business_id', $business_id)
            ->where('pump_operator_assignments.pump_operator_id', $id)
            ->select('pumps.pump_name', 'pumps.id')
            ->groupBy('pumps.id')
            ->pluck('pump_name', 'id') ?? [];
        return $pumps;
    }

    public function getDashboardData(Request $request)
    {
        $pump_operator_id =  $request->pump_operator_id;
        $pump_operator = PumpOperator::findOrFail($pump_operator_id);
        $start_date =  $request->start ?? date('Y-m-01');
        $end_date =  $request->end ?? date('Y-m-t');
        $data = [
            'total_liter_sold' => 0,
            'total_income_earned' => 0,
            'total_short' => 0,
            'total_excess' => 0,
        ];
        $sold_fuel_query =    Transaction::leftjoin('transaction_sell_lines', 'transactions.id', 'transaction_sell_lines.transaction_id')
            ->leftjoin('products', 'transaction_sell_lines.product_id', 'products.id')
            ->leftjoin('categories', 'products.category_id', 'categories.id')
            ->where('transactions.type', 'sell')
            ->where('categories.name', 'Fuel')
            ->where('transactions.pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select([
                DB::raw('SUM(transaction_sell_lines.quantity) as sold_fuel_qty'),
                DB::raw('SUM(transaction_sell_lines.quantity * unit_price) as sale_amount_fuel')
            ])->first();

        if (!empty($sold_fuel_query->sold_fuel_qty)) {
            $data['total_liter_sold'] = $sold_fuel_query->sold_fuel_qty;
        }

        if ($pump_operator->commission_type == 'fixed') {
            $data['total_income_earned'] =  $sold_fuel_query->sold_fuel_qty *  $pump_operator->commission_ap;
        }
        if ($pump_operator->commission_type == 'percentage') {
            $data['total_income_earned'] =  ($sold_fuel_query->sale_amount_fuel * $pump_operator->commission_ap) / 100;
        }

        $short_amount_query =    Transaction::where('transactions.type', 'settlement')
            ->where('transactions.sub_type', 'shortage')
            ->where('transactions.pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select([
                DB::raw('SUM(transactions.final_total) as short_amount')
            ])->first();
        $excess_amount_query =    Transaction::where('transactions.type', 'settlement')
            ->where('transactions.sub_type', 'excess')
            ->where('transactions.pump_operator_id', $pump_operator_id)
            ->whereDate('transactions.transaction_date', '>=', $start_date)
            ->whereDate('transactions.transaction_date', '<=', $end_date)
            ->select([
                DB::raw('SUM(transactions.final_total) as excess_amount')
            ])->first();

        $data['total_short'] = $short_amount_query->short_amount;
        $data['total_excess'] = $excess_amount_query->excess_amount;



        return $data;
    }

    /**
     * check user name exist or not
     * @return Renderable
     */
    public function checUsername(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if (empty($user)) {
            return ['success' => 1, 'msg' => 'ok'];
        } else {
            return ['success' => 0, 'msg' => __('lang_v1.username_already_exist')];
        }
    }

    /**
     * check user name exist or not
     * @return Renderable
     */
    public function checPasscode(Request $request)
    {
        $user = User::where('pump_operator_passcode', $request->passcode)->first();

        if (empty($user)) {
            return ['success' => 1, 'msg' => 'ok'];
        } else {
            return ['success' => 0, 'msg' => __('lang_v1.passcode_already_exist')];
        }
    }

    /**
     * check user name exist or not
     * @return Renderable
     */
    public function setMainSystemSession(Request $request)
    {
        $request->session()->put('pump_operator_main_system', true);

        return redirect()->to('petro/pump-operators/dashboard');
    }
}