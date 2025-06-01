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
use Modules\Fleet\Entities\Helper;
use Modules\Fleet\Entities\RouteOperation;
use Yajra\DataTables\Facades\DataTables;
use App\ExpenseCategory;
use Modules\Superadmin\Entities\Subscription;
use App\Category;


class HelperController extends Controller
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

            $helpers = Helper::leftjoin('users', 'helpers.created_by', 'users.id')
                ->leftjoin('expense_categories as sal_cat','sal_cat.id','helpers.salary_expense_category')
                ->leftjoin('expense_categories as adv_cat','adv_cat.id','helpers.advance_expense_category')
                ->where('helpers.business_id', $business_id)
                ->select([
                    'helpers.*',
                    'sal_cat.name as sal_name',
                    'adv_cat.name as adv_name',
                    'users.username as created_by',
                ]);

            if (!empty(request()->employee_no)) {
                $helpers->where('employee_no', request()->employee_no);
            }
            if (!empty(request()->helper_name)) {
                $helpers->where('helper_name', request()->helper_name);
            }
            if (!empty(request()->nic_number)) {
                $helpers->where('nic_number', request()->nic_number);
            }
            if (!empty(request()->user_id)) {
                $helpers->where('created_by', request()->user_id);
            }
            
            return DataTables::of($helpers)
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
                        if (auth()->user()->can('fleet.helpers.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\HelperController@edit', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.helpers.edit')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\HelperController@showHelper', [$row->id]) . '" class="btn-modal" data-container=".view_modal"><i class="fa fa-eye"></i> ' . __("messages.view") . '</a></li>';
                        }
                        if (auth()->user()->can('fleet.helpers.delete')) {
                            $html .= '<li><a href="#" data-href="' . action('\Modules\Fleet\Http\Controllers\HelperController@destroy', [$row->id]) . '" class="delete_button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        }
                        $html .= '<li class="divider"></li>';
                        $html .= '<li><a href="' . action('\Modules\Fleet\Http\Controllers\HelperController@show', [$row->id]) . '?tab=ledger" class=""><i class="fa fa-anchor"></i> ' . __("lang_v1.ledger") . '</a></li>';
                        return $html;
                    }
                )
                ->editColumn('joined_date', '{{@format_date($joined_date)}}')
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
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');

        $prefix_type = 'employee_no';
        //Generate reference number
        $ref_count = $this->transactionUtil->onlyGetReferenceCount($prefix_type, $business_id, false);
        //Generate reference number
        $employee_no = $this->transactionUtil->generateReferenceNumber($prefix_type, $ref_count);

        $departments =  HrmDepartment::where('business_id', $business_id)
                            ->pluck('name','id');
        $subscription = Subscription::where('business_id', $business_id)->select('id', 'package_details')->first();
        $package_details = $subscription->package_details;
        $is_hr_module = $package_details["hr_module"];
        return view('fleet::settings.helpers.create')->with(compact(
            'employee_no','departments', 'expense_categories', 'is_hr_module'
        ));
    
    }

    public function showHelper($id){
        $helper = Helper::find($id);
        $departments =  HrmDepartment::find($helper->department);
        $designation = HrmDesignation::find($helper->designation);
        $salary_expense_category = ExpenseCategory::find($helper->salary_expense_category);
        $advance_expense_category = ExpenseCategory::find($helper->advance_expense_category);
        $bata_expense_category = ExpenseCategory::find($helper->bata_expense_category);
        return view('fleet::settings.helpers.show_h')->with(compact(
            'helper','departments', 'designation', 
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
            $data = $request->only(['hrm_enabled','department', 'designation',
            'employee_no', 'nic_number', 'pass_no', 'pass_expiry_date', 
            'salary_expense_category', 'advance_expense_category', 'bata_expense_category']);

            $data = $request->except('_token');
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);
            $data['business_id'] = $business_id;
            if($hrmEnabled == 1){
                $employeeSelect = json_decode($request->input(key: 'employee_select'), true);
                if (is_array($employeeSelect) && count($employeeSelect) === 3) {
                    $emp_id = $employeeSelect[0];
                    $emp_nic = $employeeSelect[1];
                    $emp_name = $employeeSelect[2];
                    $data['helper_name'] = $emp_name;
                    $data['employee_no'] = $emp_id;
                    $data['nic_number'] = $emp_nic;
                }
            }else{
                $data['helper_name'] = $request->input('helper_name');
            }
            $data['created_by'] = Auth::user()->id;

            //update emploeyee count
            $this->transactionUtil->setAndGetReferenceCount('employee_no', $business_id);
            Helper::create($data);
            $output = [
                'success' => true,
                'tab' => 'helpers',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'helpers',
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
        $helper_dropdown = Helper::where('business_id', $business_id)->pluck('helper_name', 'id');
        $view_type = request()->tab;
        $helper = Helper::find($id);
        $contact_id = $id;

        return view('fleet::settings.helpers.show')->with(compact(
            'helper_dropdown',
            'view_type',
            'helper','contact_id'
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
        $helper = Helper::find($id);
        $expense_categories = ExpenseCategory::where('business_id', $business_id)
            ->pluck('name', 'id');

        $departments =  HrmDepartment::where('business_id', $business_id)
                            ->pluck('name','id');
        $subscription = Subscription::where('business_id', $business_id)->select('id', 'package_details')->first();
        $package_details = $subscription->package_details;
        $is_hr_module = $package_details["hr_module"];                            
        return view('fleet::settings.helpers.edit')->with(compact(
            'helper','expense_categories', 'departments', 'is_hr_module'
        ));
    }

    function getDesignationByDepartmentId(Request $request){
        if (! empty($request->input('department_id'))) {
            $department_id = $request->input('department_id');
            $business_id = $request->session()->get('user.business_id');

            $designations = HrmDesignation::where('business_id', $business_id)
                        ->where('department_id', $department_id)
                        ->select(['name', 'id'])
                        ->get();
            
            if (! empty($designations)) {
                $html = '<option value="">Please Select</option>';
                foreach ($designations as $designation) {
                    $html .= '<option value="'.$designation->id.'">'.$designation->name.'</option>';
                }
            }else{
                $html = '<option value="">None</option>';
            }

            echo $html;
            exit;
        }
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
            $data = $request->only(['joined_date','department', 'designation',
            'employee_no', 'nic_number', 'pass_no', 'pass_expiry_date', 
            'salary_expense_category', 'advance_expense_category', 'bata_expense_category']);

            $data = $request->except('_token', '_method','employee_select');
            $data['hrm_enabled'] = $hrmEnabled;
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);
            if($hrmEnabled == 1){
                $employeeSelect = json_decode($request->input(key: 'employee_select'), true);
                if (is_array($employeeSelect) && count($employeeSelect) === 3) {
                    $emp_id = $employeeSelect[0];
                    $emp_nic = $employeeSelect[1];
                    $emp_name = $employeeSelect[2];
                    $data['helper_name'] = $emp_name;
                    $data['employee_no'] = $emp_id;
                    $data['nic_number'] = $emp_nic;
                }
            }else{
                $data['helper_name'] = $request->input('helper_name');
            }
            $data['created_by'] = Auth::user()->id;

            //update emploeyee count
            $this->transactionUtil->setAndGetReferenceCount('employee_no', $business_id);
            Helper::where('id', $id)->update($data);
            $output = [
                'success' => true,
                'tab' => 'helpers',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'helpers',
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()->back()->with('status', $output);
    }
    public function update1(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method');
            $data['joined_date'] = $this->commonUtil->uf_date($data['joined_date']);

            Helper::where('id', $id)->update($data);

            $output = [
                'success' => true,
                'tab' => 'helpers',
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'helpers',
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

            Helper::where('id', $id)->delete();

            $route_operations = RouteOperation::where('helper_id', $id)->get();
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
