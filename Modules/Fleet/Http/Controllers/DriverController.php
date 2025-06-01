<?php

namespace Modules\Fleet\Http\Controllers;

use App\Transaction;
use App\TransactionPayment;
use App\Utils\ModuleUtil;
use App\Utils\ProductUtil;
use App\Utils\TransactionUtil;
use App\Utils\Util;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Essentials\Entities\HrmDepartment;
use Modules\Essentials\Entities\HrmDesignation;
use Modules\Fleet\Entities\Driver;
use Modules\Fleet\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;
use App\ExpenseCategory;
use Modules\HR\Entities\Employee;
use App\Category;
use Modules\Superadmin\Entities\Subscription;
class DriverController extends Controller
{
    protected $commonUtil;
    protected $moduleUtil;
    protected $productUtil;
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(Util $commonUtil, ModuleUtil $moduleUtil, ProductUtil $productUtil, TransactionUtil $transactionUtil)
    {
        $this->commonUtil = $commonUtil;
        $this->moduleUtil =  $moduleUtil;
        $this->productUtil =  $productUtil;
        $this->transactionUtil =  $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');

            $drivers = Driver::leftjoin('users', 'drivers.created_by', 'users.id')
                ->leftjoin('expense_categories as sal_cat','sal_cat.id','drivers.salary_expense_category')
                ->leftjoin('expense_categories as adv_cat','adv_cat.id','drivers.advance_expense_category')
                ->leftjoin('expense_categories as bata_cat', 'bata_cat.id', 'drivers.bata_expense_category')
                ->where('drivers.business_id', $business_id)
                ->select([
                    'drivers.*',
                    'sal_cat.name as sal_name',
                    'bata_cat.name as bata_name', // Correct selection for bata_expense_category
                    'adv_cat.name as adv_name',
                    'users.username as created_by',
                ])
                
               ->get();

            if (!empty(request()->employee_no)) {
                $drivers->where('employee_no', request()->employee_no);
            }
            if (!empty(request()->driver_name)) {
                $drivers->where('driver_name', request()->driver_name);
            }
            if (!empty(request()->nic_number)) {
                $drivers->where('nic_number', request()->nic_number);
            }
            if (!empty(request()->user_id)) {
                $drivers->where('created_by', request()->user_id);
            }
           
            return DataTables::of($drivers)
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
                        if (auth()->user()->can('fleet.drivers.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\DriverController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.drivers.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\DriverController@showDriver', [$row->id]) . '"class="btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.drivers.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\DriverController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        
                        $html .= '<li class="divider"></li>';
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\DriverController@show', [$row->id]) . '?tab=ledger" class=""><i class="fa fa-anchor"></i> ' . __("lang_v1.ledger") . '</a></li>';

                        return $html;
                    }
                )
                ->editColumn('joined_date', '{{@format_date($joined_date)}}')
                ->editColumn('expiry_date', '{{@format_date($expiry_date)}}')
                ->addColumn('bata_expense_category', function ($row) {
                        return $row->bata_name; // Access 'bata_name' field here
                    })
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        $prefix_type = 'employee_no';
        //Generate reference number
        $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);
        //Generate reference number
        $employee_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);
        
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');

        $departments =  HrmDepartment::where('business_id', $business_id)
                            ->pluck('name','id');

        $subscription = Subscription::where('business_id', $business_id)->select('id', 'package_details')->first();
        $package_details = $subscription->package_details;
        $is_hr_module = $package_details["hr_module"];
        return view('fleet::settings.drivers.create')->with(compact(
            'employee_no','departments','expense_categories', 'is_hr_module'
        ));
    }

    public function showDriver($id){
        $driver = Driver::find($id);
        $departments =  HrmDepartment::find($driver->department);
        $designation = HrmDesignation::find($driver->designation);
        $salary_expense_category = ExpenseCategory::find($driver->salary_expense_category);
        $advance_expense_category = ExpenseCategory::find($driver->advance_expense_category);
        $bata_expense_category = ExpenseCategory::find($driver->bata_expense_category);
        return view('fleet::settings.drivers.show_d')->with(compact(
            'driver','departments', 'designation', 
            'salary_expense_category', 'advance_expense_category', 'bata_expense_category'
        ));
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        Log::info('Form data received:', $request->all());
        $business_id = request()->session()->get('business.id');
        try {
            
            $hrmEnabled = $request->has('hrm_enabled') ? 1 : 0;
            $data = $request->only(['joined_date','hrm_enabled','department', 'designation',
            'employee_no', 'nic_number', 'dl_number', 'dl_type', 'expiry_date', 'pass_no', 'pass_expiry_date', 
            'salary_expense_category', 'advance_expense_category', 'bata_expense_category']);

            $data = $request->except('_token');
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);
            $data['expiry_date'] = $this->commonUtil->uf_date($data['expiry_date']);
            $data['pass_expiry_date'] = $this->commonUtil->uf_date($data['pass_expiry_date']);
            $data['business_id'] = $business_id;
            if($hrmEnabled == 1){
                $employeeSelect = json_decode($request->input(key: 'employee_select'), true);
                if (is_array($employeeSelect) && count($employeeSelect) === 3) {
                    $emp_id = $employeeSelect[0];
                    $emp_nic = $employeeSelect[1];
                    $emp_name = $employeeSelect[2];
                    $data['driver_name'] = $emp_name;
                    $data['employee_no'] = $emp_id;
                    $data['nic_number'] = $emp_nic;
                }
            }else{
                $data['driver_name'] = $request->input('driver_name');
            }
            $data['created_by'] = Auth::user()->id;

            //update emploeyee count
            $this->transactionUtil->setAndGetReferenceCount('employee_no', $business_id);
            Driver::create($data);
            $output = [
                'success' => true,
                'tab' => 'drivers',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'drivers',
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
        $driver_dropdown = Driver::where('business_id', $business_id)->pluck('driver_name', 'id');
        $view_type = request()->tab;
        $driver = Driver::find($id);
        $contact_id = $id;

        return view('fleet::settings.drivers.show')->with(compact(
            'driver_dropdown',
            'view_type',
            'driver',
            'contact_id'
        ));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $prefix_type = 'employee_no';
        $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);
        $new_employee_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

        $driver = Driver::find($id);
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');
        $departments =  HrmDepartment::where('business_id', $business_id)
            ->pluck('name','id');
        
        $subscription = Subscription::where('business_id', $business_id)->select('id', 'package_details')->first();
        $package_details = $subscription->package_details;
        $is_hr_module = $package_details["hr_module"];
        return view('fleet::settings.drivers.edit')->with(compact(
            'driver','expense_categories', 'departments', 'new_employee_no','is_hr_module'
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
        Log::info('Form data received:', $request->all());
        $business_id = request()->session()->get('business.id');
        try {
            
            $hrmEnabled = $request->has('hrm_enabled') ? 1 : 0;
            $data = $request->only(['joined_date','hrm_enabled','department', 'designation',
            'employee_no', 'nic_number', 'dl_number', 'dl_type', 'expiry_date', 'pass_no', 'pass_expiry_date', 
            'salary_expense_category', 'advance_expense_category', 'bata_expense_category']);

            $data = $request->except('_token', '_method', 'employee_select');
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);
            $data['expiry_date'] = $this->commonUtil->uf_date($data['expiry_date']);
            $data['pass_expiry_date'] = $this->commonUtil->uf_date($data['pass_expiry_date']);

            $data['business_id'] = $business_id;
            if($hrmEnabled == 1){
                $employeeSelect = json_decode($request->input(key: 'employee_select'), true);
                if (is_array($employeeSelect) && count($employeeSelect) === 3) {
                    $emp_id = $employeeSelect[0];
                    $emp_nic = $employeeSelect[1];
                    $emp_name = $employeeSelect[2];
                    $data['driver_name'] = $emp_name;
                    $data['employee_no'] = $emp_id;
                    $data['nic_number'] = $emp_nic;
                }
            }else{
                $data['driver_name'] = $request->input('driver_name');
            }
            $data['created_by'] = Auth::user()->id;

            //update emploeyee count
            $this->transactionUtil->setAndGetReferenceCount('employee_no', $business_id);
            Driver::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'drivers',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'drivers',
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
            Driver::where('id', $id)->delete();

            $route_operations = RouteOperation::where('driver_id', $id)->get();
            foreach ($route_operations as $route_operation) {
                Transaction::where('id', $route_operation->transaction_id)->delete();
                TransactionPayment::where('transaction_id', $route_operation->transaction_id)->delete();
            }
            RouteOperation::where('helper_id', $id)->delete();

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
