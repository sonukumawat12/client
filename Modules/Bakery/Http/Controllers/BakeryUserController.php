<?php

namespace Modules\Bakery\Http\Controllers;

use App\Account;
use App\AccountGroup;
use App\AccountTransaction;
use App\AccountType;
use App\Business;
use App\BusinessLocation;
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
use Modules\Bakery\Entities\BakeryUser;
use App\Utils\BusinessUtil;
use App\Utils\Util;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Litespeed\LSCache\LSCache;

class BakeryUserController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $productUtil;
    protected $moduleUtil;
    protected $transactionUtil;
    protected $commonUtil;

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


    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id =  Auth::user()->business_id;
        

        if (request()->ajax()) {

            $business_id =  Auth::user()->business_id;
            if (request()->ajax()) {
                $query = BakeryUser::leftjoin('business_locations', 'bakery_users.location_id', 'business_locations.id')
                    ->where('bakery_users.business_id', $business_id)
                    ->select([
                        'bakery_users.*',
                        'bakery_users.id as pump_operator_id',
                        'business_locations.name as location_name',
                    ])->groupBy('bakery_users.id');

                
                if (!empty(request()->pump_operator)) {
                    $query->where('bakery_users.id', request()->pump_operator);
                }
                
                if (!empty(request()->location_id)) {
                    $query->where('bakery_users.location_id', request()->location_id);
                }
               
                if (!empty(request()->status)) {
                    if (request()->status == 'active') {
                        $query->where('bakery_users.active', 1);
                    } else {
                        $query->where('bakery_users.active', 0);
                    }
                }
                
                $start_date = request()->start_date;
                $end_date = request()->end_date;
                $business_details = Business::find($business_id);


                $fuel_tanks = Datatables::of($query)
                    ->addColumn(
                        'action',
                        function ($row) {
                            $business_id = session()->get('user.business_id');
                           
                            $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs"
                                data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                </span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-left" role="menu">

                            <li><a href="' . action('\Modules\Bakery\Http\Controllers\BakeryUserController@show', [$row->id]) . '"><i class="fa fa-eye" aria-hidden="true"></i>' . __("messages.view") . '</a></li>';
                            
                            if (auth()->user()->can('bakery_edit_user')) {
                                $html .= '<li><a hre="#"  data-href="' . action('\Modules\Bakery\Http\Controllers\BakeryUserController@edit', [$row->id]) . '" class="btn-modal"  data-container=".view_modal"><i class="fa fa-pencil-square-o"></i> ' . __("messages.edit") . '</a></li>';
                            }
                            
                            $html .= '<li class="divider"></li>';
                            if (!$row->active) {
                                $html .= '<li><a href="' . action('\Modules\Bakery\Http\Controllers\BakeryUserController@toggleActivate', [$row->id]) . '" class="toggle_active_button"><i class="fa fa-check"></i> ' . __("lang_v1.activate") . '</a></li>';
                            } else {
                                $html .= '<li><a href="' . action('\Modules\Bakery\Http\Controllers\BakeryUserController@toggleActivate', [$row->id]) . '" class="toggle_active_button"><i class="fa fa-times"></i> ' . __("lang_v1.deactivate") . '</a></li>';
                            }
                           

                            
                            $html .= '<li class="divider"></li>
                            <li>
                                <a href="' . action('\Modules\Bakery\Http\Controllers\BakeryUserController@show', [$row->id]) . "?view=contact_info" . '">
                                    <i class="fa fa-user" aria-hidden="true"></i>
                                    ' . __("contact.contact_info", ["contact" => __("contact.contact")]) . '
                                </a>
                            </li>
                            ';

                        
                            $html .= '<li>
                                <a href="' . action('\Modules\Bakery\Http\Controllers\BakeryUserController@show', [$row->id]) . "?view=ledger" . '">
                                    <i class="fa fa-anchor" aria-hidden="true"></i>
                                    ' . __("lang_v1.ledger") . '
                                </a>
                            </li>';

                            $html .= '<li>
                                <a href="' . action('\Modules\Bakery\Http\Controllers\BakeryUserController@show', [$row->id]) . "?view=documents_and_notes" . '">
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
                    
                    ->editColumn('created_at','{{@format_datetime($created_at)}}')
                   
                    ->addColumn(
                        'sold_amount',
                        function ($row) {
                            
                            // return  '<span class="display_currency sale_amount_fuel" data-orig-value="' . $amount->sale_amount_fuel . '" data-currency_symbol = true>' . $this->productUtil->num_f($amount->sale_amount_fuel, false, $business_details, false) . '</span>';
                        }
                    )
                    ->addColumn(
                        'current_balance',
                        function ($row) {
                            // return  '<span class="display_currency current_balance" data-orig-value="' . $balance_due . '" data-currency_symbol = true>' . $this->productUtil->num_f($balance_due, false) . '</span>';
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
                            // return  '<span class="display_currency commission_amount" data-orig-value="' . $amount . '" data-currency_symbol = true>' . $this->productUtil->num_f($amount, false, $business_details, true) . '</span>';
                        }
                    )

                    ->removeColumn('id');


                return $fuel_tanks->rawColumns(['name', 'action', 'sold_fuel_qty', 'sale_amount_fuel', 'excess_amount', 'short_amount', 'commission_rate', 'commission_amount', 'current_balance'])
                    ->make(true);
            }
        }

        $business_locations = BusinessLocation::forDropdown($business_id);
        $pump_operators = BakeryUser::where('business_id', $business_id)->pluck('name', 'id');

            
        $default_location = current(array_keys($business_locations->toArray()));

        return view('bakery::bakery_users.index')->with(compact(
            'business_locations',
            'pump_operators'
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
        
        $generate_passcode = sprintf("%04d", rand(0, 9999));

        return view('bakery::bakery_users.create')->with(compact('locations', 'commission_type_permission', 'generate_passcode'));
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
                'status' => 1
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
            $pump_operator = BakeryUser::create($data);
            $this->createUser($request, $pump_operator);

            //Add opening balance
            if (!empty($request->input('opening_balance'))) {
                $this->transactionUtil->createOpeningBalanceTransactionForBakeryUser($business_id, $pump_operator->id, $request->input('opening_balance'), $request->input('opening_balance_type'), $request->location_id, $request->input('transaction_date'));
            }

            DB::commit();

            $output = [
                'success' => 1,
                'msg' => __('petro::lang.pump_operator_add_success'),
                'tab' => 'users'
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
                'tab' => 'users'
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
        $role = Role::where('name', 'Bakery User#' . $business_id)->first();

        if (empty($role)) {
            $role = Role::create([
                'name' => 'Bakery User#' . $business_id,
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
        $pump_operators = BakeryUser::where('business_id', $business_id)->pluck('name', 'id');
        $pump_operator = BakeryUser::findOrFail($id);

        $business_locations = BusinessLocation::forDropdown($business_id, true);

        //get contact view type : ledger, notes etc.
        $view_type = request()->get('view');
        if (is_null($view_type)) {
            $view_type = 'contact_info';
        }

       
        return view('bakery::bakery_users.show')
            ->with(compact('pump_operator', 'pump_operators', 'business_locations', 'view_type'));
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('business.id');
        $locations = BusinessLocation::forDropdown($business_id);

        $pump_operator = BakeryUser::findOrFail($id);
        $transaction = Transaction::where([
            'type' => 'bakery_user_opening_balance',
            'pump_operator_id' => $pump_operator->id,
            'business_id' => $business_id
        ])->first();
        
        // logger(json_encode($transaction));

        $user = User::where('business_id', $business_id)->where('pump_operator_id', $id)->first();

        return view('bakery::bakery_users.edit')->with(compact('locations', 'pump_operator', 'user', 'transaction'));
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
            );

            BakeryUser::where('id', $id)->update($data);
            $pump_operator = BakeryUser::findOrFail($id);
            
            //Add opening balance
            $this->transactionUtil->updateOpeningBalanceTransactionForBakeryUser($business_id, $pump_operator->id, $request->input('opening_balance'), $request->input('opening_balance_type'), $request->location_id, $request->input('transaction_date'));

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
                'msg' => __('petro::lang.pump_operator_update_success'),
                'tab' => 'users'
            ];
        } catch (\Exception $e) {
            \Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),
                'tab' => 'users'
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy()
    {
    }
   

    public function toggleActivate($id)
    {
        try {
            $pump_operator = BakeryUser::findOrFail($id);
            $pump_operator->active = !$pump_operator->active;
            $pump_operator->save();

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

    
    
    public function update_passcode()
    {
        $user = User::find(auth()->user()->id);

        return view('bakery::bakery_users.update_passcode')->with(compact(
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