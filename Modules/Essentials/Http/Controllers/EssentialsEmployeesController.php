<?php

namespace Modules\Essentials\Http\Controllers;

use App\BusinessLocation;
use App\Utils\ModuleUtil;
use App\Utils\TransactionUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\EssentialsEmployee;
use Modules\Essentials\Entities\EssentialsEmployeeAdvance;
use Modules\Essentials\Entities\HrmDepartment;
use Modules\Essentials\Entities\HrmDesignation;
use Modules\Essentials\Entities\HrmEmployeeLedger;
use Yajra\DataTables\Facades\DataTables;
use App\Category;
use Modules\Essentials\Entities\EssentialsEmployeesSalaryDetail;
use App\Account;
use App\Transaction;
use App\AccountTransaction;
use Illuminate\Support\Facades\DB;


class EssentialsEmployeesController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;
    
    protected $transactionUtil;

    /**
     * Constructor
     *
     * @param  ModuleUtil  $moduleUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil, TransactionUtil $transactionUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->transactionUtil = $transactionUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('user.business_id');

        
        
        if (request()->ajax()) {
            $holidays = EssentialsEmployee::where('essentials_employees.business_id', $business_id)
                        ->leftJoin('hrm_departments as dpt', 'dpt.id', '=', 'essentials_employees.department')
                        ->leftJoin('hrm_designations as des', 'des.id', '=', 'essentials_employees.designation')
                        ->leftJoin('users','users.id','essentials_employees.created_by')
                        ->select([
                            'essentials_employees.*',
                            'dpt.name as department',
                            'des.name as designation',
                            'users.username'
                        ]);
                        
            if (! empty(request()->input('department'))) {
                $holidays->where('essentials_employees.department', request()->input('department'));
            }
            
            if (! empty(request()->input('employees_employee_no'))) {
                $holidays->where('essentials_employees.employee_no', request()->input('employees_employee_no'));
            }
            
            if (! empty(request()->input('designation'))) {
                $holidays->where('essentials_employees.designation', request()->input('designation'));
            }

            if (! empty(request()->added_start_date) && ! empty(request()->added_end_date)) {
                $start = request()->added_start_date;
                $end = request()->added_end_date;
                $holidays->whereDate('essentials_employees.created_at', '>=', $start)
                            ->whereDate('essentials_employees.created_at', '<=', $end);
            }
            
            if (! empty(request()->probation_start_date) && ! empty(request()->probation_end_date)) {
                $start = request()->probation_start_date;
                $end = request()->probation_end_date;
                $holidays->whereDate('essentials_employees.probation_ends', '>=', $start)
                            ->whereDate('essentials_employees.probation_ends', '<=', $end);
            }
            
            if (! empty(request()->joined_start_date) && ! empty(request()->joined_end_date)) {
                $start = request()->joined_start_date;
                $end = request()->joined_end_date;
                $holidays->whereDate('essentials_employees.date_joined', '>=', $start)
                            ->whereDate('essentials_employees.date_joined', '<=', $end);
            }
            

            return Datatables::of($holidays)
                ->addColumn(
                    'action',
                    function ($row) use($business_id)  {
                        
                        $html = '<div class="btn-group">
                            <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                                data-toggle="dropdown" aria-expanded="false">' .
                                __("messages.actions") .
                                '<span class="caret"></span><span class="sr-only">Toggle Dropdown
                                        </span>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-left" role="menu">';
                                    
                                    $html .= '<li>
                                    <a class="btn-modal" data-container="#add_holiday_modal" data-href="'.action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'edit'], [$row->id]).'"><i class="fa fa-edit"></i> '.__('messages.edit').'</a>
                                    <a class="btn-modal" data-container="#add_holiday_modal" data-href="'.action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'show'], [$row->id]).'"><i class="fa fa-eye"></i> '.__('messages.view').'</a>';
                                    
                                    if($this->moduleUtil->hasThePermissionInSubscription($business_id, 'hrm_ledger')){
                                        $html .= '<a class="" href="'.action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'showEmployeeLedger'], [$row->id]).'?tab=ledger"><i class="fa fa-anchor"></i> '.__('lang_v1.hrm_ledger').'</a>';
                                    }
                                    
                                    if($this->moduleUtil->hasThePermissionInSubscription($business_id, 'hrm_salary_details')){
                                        $html .= '<a class="btn-modal" data-container="#add_holiday_modal" data-href="'.action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'salary_details'], [$row->id]).'"><i class="fa fa-dollar"></i> '.__('essentials::lang.salary_details').'</a>';
                                    }
                                    $html .= '<a class="btn-modal" data-container="#add_holiday_modal" data-href="'.action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'addEarning'], [$row->id]).'"><i class="fa fa-plus"></i>'.__('lang_v1.add_earning').'</a>';
                                    
                                    $html .= '<a href="#" class="note_btn" data-string="' . $row->note . '">' . __('lang_v1.note') . '</a>';
                            
                        $html .=  '</ul></div>';
                        return $html;
                    }
                )
                ->editColumn('salary', '{{@num_format($salary)}}')
                ->editColumn('date_joined', '{{@format_date($date_joined)}}')
                ->editColumn('dob', '{{@format_date($dob)}}')
                ->editColumn('probation_ends', '{{@format_date($probation_ends)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }

        $locations = BusinessLocation::forDropdown($business_id);
        
        $departments = HrmDepartment::where('business_id', $business_id)
                            ->pluck('name','id');
        
        $designations = HrmDesignation::where('business_id', $business_id)
                            ->pluck('name','id');
        
        $employee_nos = EssentialsEmployee::where('essentials_employees.business_id', $business_id)->distinct()->pluck('employee_no','employee_no');

        return view('essentials::employees.index')->with(compact('locations','departments','designations','employee_nos'));
    }
    
     public function getemployeesbyDesignation(Request $request){
        $business_id = request()->session()->get('user.business_id');
        $designation_id = $request->input('designation');

        $employees = EssentialsEmployee::where('business_id', $business_id)
                        ->select('name','employee_no', 'nic')
                        ->get();

        $html = "<option value=''> None </option>";
        foreach($employees as $one){
            $html .= "<option value='".json_encode([$one->employee_no,$one->nic, $one->name])."'> $one->name </option>";
        }

        return $html;
    }
    public function viewNote($id){
        $note = HrmEmployeeLedger::where('id', $id)
                        ->value('note');
        return view('essentials::employees.ledger.view_note')->with(compact( 'note'));
    }
    public function addEarning($id){
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);
        
        $departments = HrmDepartment::where('business_id', $business_id)
                            ->pluck('name','id');
        
        $designations = HrmDesignation::where('business_id', $business_id)
                            ->pluck('name','id');

        $locations = BusinessLocation::forDropdown($business_id);

        $lastFormNumber = HrmEmployeeLedger::where('employee_id', $id)
                        ->orderBy('form_number', 'desc')
                        ->value('form_number');
        $formNumber = $lastFormNumber ? $lastFormNumber + 1 : 1;
        $username = auth()->user()->username;
        $employeeId = $id;
        return view('essentials::employees.ledger.create')->with(compact('departments','designations', 'formNumber', 'employeeId', 'username'));
    }
    public function storeEarning(Request $request){
        $business_id = $request->session()->get('user.business_id');
        try {
            $input = $request->only(['form_number','date', 'amount','note','employee_id', 'add_by']);
            $input['business_id'] = $business_id;
            $input["created_by"] = $request->session()->get('user.id');
            
            $employee = HrmEmployeeLedger::create($input);
            $output = ['success' => true,
                'msg' => __('lang_v1.updated_success'),
            ];
            // return redirect('hrm/employees')->with('success', __('lang_v1.added_success'));
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
            // return redirect()->back()->with('error', __('messages.something_went_wrong'));
        }
        return $output;
    }
    public function list_salary_history($id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if (request()->ajax()) {
            $holidays = EssentialsEmployeesSalaryDetail::leftjoin('users','users.id','essentials_employees_salary_details.created_by')
                        ->where('essentials_employees_salary_details.employee_id', $id)
                        ->select([
                            'essentials_employees_salary_details.*',
                            'users.username'
                        ]);

            return Datatables::of($holidays)
                ->addColumn(
                    'action',
                    function ($row)  {
                        $html = '';
                        
                            $html .= '<button class="btn btn-xs btn-danger delete-holiday" data-href="'.action([\Modules\Essentials\Http\Controllers\EssentialsEmployeesController::class, 'destroy_details'], [$row->id]).'"><i class="fa fa-trash"></i> '.__('messages.delete').'</button>';
                        
                        return $html;
                    }
                )
                ->addColumn(
                    'salary_increased',
                    function ($row)  {
                        return $this->moduleUtil->num_f($row->new_salary - $row->current_salary);
                    }
                )
                ->editColumn('current_salary', '{{@num_format($current_salary)}}')
                ->editColumn('new_salary', '{{@num_format($new_salary)}}')
                ->editColumn('created_at', '{{@format_date($created_at)}}')
                ->editColumn('applicable_date', '{{@format_date($applicable_date)}}')
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create()
    {
        $business_id = request()->session()->get('user.business_id');
        
        $settings = request()->session()->get('business');
        // dd($settings->essentials_settings);
        $settings = ! empty($settings) ? json_decode($settings->essentials_settings, true) : [];
        
        $current_employee = EssentialsEmployee::where('business_id',$business_id)->orderBy('id','DESC')->first();
        $employee_no = !empty($current_employee) ? $current_employee->employee_no + 1 : (!empty($settings['employees_starting_number']) ? $settings['employees_starting_number'] : 1);
        
        
        $departments = HrmDepartment::where('business_id', $business_id)
                            ->pluck('name','id');
        
        $designations = HrmDesignation::where('business_id', $business_id)
                            ->pluck('name','id');
        
        

        return view('essentials::employees.create')->with(compact('departments','designations','employee_no'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        
        try {
            $input = $request->only(['note','name', 'nic','address','dob','employee_no', 'salary', 'date_joined', 'probation_ends','department','designation', "sales_target_applicable"]);

            $input['business_id'] = $business_id;
            $input["created_by"] = $request->session()->get('user.id');
            
            $employee = EssentialsEmployee::create($input);
            
            if(!empty($request->employee_ob)){
                
                if($request->employee_ob > 0){
                    $type = "debit";
                    $eqt_type= "credit";
                    $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                }else if($request->employee_ob < 0){
                    $type = "credit";
                    $eqt_type= "debit";
                    $account_id = $this->transactionUtil->account_exist_return_id('Salaries & Wages Payable');
                }
            
                
                $business_locations = BusinessLocation::forDropdown($business_id);
                $default_location = current(array_keys($business_locations->toArray()));
                
                $ob_data = [
                                'business_id' => $business_id,
                                'location_id' => $default_location,
                                'type' => 'essentials_employee_ob',
                                'status' => 'final',
                                'payment_status' => 'due',
                                'transaction_date' => date('Y-m-d'),
                                'total_before_tax' => $request->employee_ob,
                                'final_total' => $request->employee_ob,
                                'created_by' => request()->session()->get('user.id')
                            ];
                $transaction = Transaction::create($ob_data);
                
                $employee->transaction_id = $transaction->id;
                $employee->save();
                
            
                $account_transaction_data = [
                            'amount' =>  abs($request->employee_ob),
                            'account_id' => $account_id,
                            'type' => $type,
                            'sub_type' => null,
                            'operation_date' => $transaction->transaction_date,
                            'created_by' => $transaction->created_by,
                            'transaction_id' =>  $transaction->id,
                            'note' => $request->note
                        ];
                        
                AccountTransaction::createAccountTransaction($account_transaction_data);
                $account_transaction_data['account_id'] = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
                $account_transaction_data['type'] = $eqt_type;
                
                AccountTransaction::createAccountTransaction($account_transaction_data);
            }
                
            
            
            $output = ['success' => true,
                'msg' => __('lang_v1.added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
    
    public function post_salary_details($id,Request $request)
    {
        $business_id = $request->session()->get('user.business_id');
        
        

        try {
            
            $input = ["employee_id" => $id,'applicable_date' => $request->applicable_date,	"current_salary" => $request->current_salary,	"new_salary" => $request->salary,	"created_by" => $request->session()->get('user.id')];

            
            EssentialsEmployeesSalaryDetail::create($input);
            $output = ['success' => true,
                'msg' => __('lang_v1.added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Show the specified resource.
     *
     * @return Response
     */
    public function show($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        

        $employee = EssentialsEmployee::where('business_id', $business_id)
                                    ->findOrFail($id);
        
        $departments = HrmDepartment::where('business_id', $business_id)
                            ->pluck('name','id');
        
        $designations = HrmDesignation::where('business_id', $business_id)
                            ->pluck('name','id');

        $locations = BusinessLocation::forDropdown($business_id);

        return view('essentials::employees.show')->with(compact('departments','designations', 'employee'));
    }
    
    public function showLedger($id)
    {
        $business_id = request()->session()->get('business.id');
        $driver_dropdown = EssentialsEmployee::where('business_id', $business_id)->pluck('name', 'id');
        $view_type = request()->tab;
        $driver = EssentialsEmployee::where('business_id', $business_id)
                                    ->findOrFail($id);
        $contact_id = $id;
        $ledgerData = HrmEmployeeLedger::All();

        return view('essentials::employees.ledger')->with(compact(
            'driver_dropdown',
            'view_type',
            'driver',
            'contact_id',
            'ledgerData'
        ));
    }
    public function showEmployeeLedger($id)
    {
        $business_id = request()->session()->get('business.id');
        $driver_dropdown = EssentialsEmployee::where('business_id', $business_id)->pluck('name', 'id');
        $view_type = request()->tab;
        $driver = EssentialsEmployee::where('business_id', $business_id)->findOrFail($id);
        $emp_id = $id;

        return view('essentials::employees.ledger.employee_ledger')->with(compact(
            'driver_dropdown',
            'view_type',
            'driver',
            'emp_id'
        ));
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

       

        $employee = EssentialsEmployee::where('business_id', $business_id)
                                    ->findOrFail($id);

        $departments = HrmDepartment::where('business_id', $business_id)
                            ->pluck('name','id');
        
        $designations = HrmDesignation::where('business_id', $business_id)
                            ->pluck('name','id');

        $locations = BusinessLocation::forDropdown($business_id);

        return view('essentials::employees.edit')->with(compact('departments','designations', 'employee'));
    }
    
    public function salary_details($id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        
        $employee = EssentialsEmployee::where('business_id', $business_id)
                                    ->findOrFail($id);
        
        $current = EssentialsEmployeesSalaryDetail::where('employee_id',$id)->orderBy('id','DESC')->first();
        
        $current_salary = !empty($current) ? $current->new_salary : $employee->salary;

        return view('essentials::employees.salary_details')->with(compact('employee','current_salary'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        $business_id = $request->session()->get('user.business_id');
        
        

        try {
            $input = $request->only(['note','employee_ob','name', 'nic','address','dob','employee_no', 'salary', 'date_joined', 'probation_ends','department','designation', 'sales_target_applicable']);

            
            EssentialsEmployee::where('business_id', $business_id)
                        ->where('id', $id)
                        ->update($input);
                        
            $employee = EssentialsEmployee::where('business_id', $business_id)
                                    ->findOrFail($id);
            
            $transaction = Transaction::find($employee->transaction_id);
            AccountTransaction::where('transaction_id',$employee->transaction_id)->forceDelete();
            
            if(!empty($request->employee_ob)){
                
                if($request->employee_ob > 0){
                    $type = "debit";
                    $eqt_type= "credit";
                    $account_id = $this->transactionUtil->account_exist_return_id('Accounts Receivable');
                }else if($request->employee_ob < 0){
                    $type = "credit";
                    $eqt_type= "debit";
                    $account_id = $this->transactionUtil->account_exist_return_id('Salaries & Wages Payable');
                }
                            
                if(empty($transaction)){
                    $business_locations = BusinessLocation::forDropdown($business_id);
                    $default_location = current(array_keys($business_locations->toArray()));
                    
                    $ob_data = [
                            'business_id' => $business_id,
                            'location_id' => $default_location,
                            'type' => 'essentials_employee_ob',
                            'status' => 'final',
                            'payment_status' => 'due',
                            'transaction_date' => date('Y-m-d'),
                            'total_before_tax' => $request->employee_ob,
                            'final_total' => $request->employee_ob,
                            'created_by' => request()->session()->get('user.id')
                        ];
                    
                    $transaction = Transaction::create($ob_data);
                        
                    $employee->transaction_id = $transaction->id;
                    $employee->save();
                
                }else{
                    $transaction->total_before_tax = $request->employee_ob;
                    $transaction->final_total = $request->employee_ob;
                    $transaction->save();
                } 
                
                
                
            
                $account_transaction_data = [
                            'amount' =>  abs($request->employee_ob),
                            'account_id' => $account_id,
                            'type' => $type,
                            'sub_type' => null,
                            'operation_date' => $transaction->transaction_date,
                            'created_by' => $transaction->created_by,
                            'transaction_id' =>  $transaction->id,
                            'note' => $request->note
                        ];
                        
                AccountTransaction::createAccountTransaction($account_transaction_data);
                $account_transaction_data['account_id'] = $this->transactionUtil->account_exist_return_id('Opening Balance Equity Account');
                $account_transaction_data['type'] = $eqt_type;
                
                AccountTransaction::createAccountTransaction($account_transaction_data);
            }
            

            $output = ['success' => true,
                'msg' => __('lang_v1.updated_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        

        try {
            EssentialsEmployee::where('business_id', $business_id)
                        ->where('id', $id)
                        ->delete();

            $output = ['success' => true,
                'msg' => __('lang_v1.deleted_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
    
    public function destroy_details($id)
    {
        $business_id = request()->session()->get('user.business_id');
        
        

        try {
            EssentialsEmployeesSalaryDetail::where('id', $id)
                        ->delete();

            $output = ['success' => true,
                'msg' => __('lang_v1.deleted_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        return $output;
    }
    
    public function fetchLedgerSummarised(Request $request){
        $contact_id = $request->contact_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        
        $summary = array();
        
        $balance = $this->fetchBF($contact_id,$start_date,$end_date);
        $summary = $this->fetchSummary($contact_id,$start_date,$end_date);
        $summary['balance'] = $balance;
        $summary['balance_due'] = $summary['income'] - $summary['paid'];
        
        return response()->json($summary);
    }
    
    public function fetchEmployeeLedger(Request $request){
		$business_id = request()->session()->get('user.business_id');
        $emp_id = $request->emp_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;

        $ob = EssentialsEmployeeAdvance::join('essentials_employees', 'essentials_employees.id', '=', 'essentials_employee_advances.employee_id' )
        ->join('essentials_employee_payment_settings','essentials_employee_payment_settings.id','=','essentials_employee_advances.payment_type_id')
        ->join('accounts','accounts.id','=', 'essentials_employee_payment_settings.liability_account_id')
        ->where('essentials_employee_advances.employee_id','=',$emp_id)
        ->whereDate('essentials_employee_advances.datetime_entered','>=',$start_date)
        ->where('essentials_employee_advances.datetime_entered','<',$end_date)
        ->select(
            'essentials_employee_advances.id',
            'essentials_employees.name',
            'essentials_employees.employee_no',
            'essentials_employee_advances.amount',
            'essentials_employee_advances.datetime_entered',
            'essentials_employee_advances.payment_type_id',
            'essentials_employee_advances.salary_period_start',
            'essentials_employee_advances.salary_period_end',
            'essentials_employee_advances.amount_paid',
            'essentials_employee_advances.payment_status',
            'essentials_employee_advances.account_id',
            'essentials_employee_advances.check_no',
            'essentials_employee_payment_settings.name as payment_type_name',
            'essentials_employee_payment_settings.liability_account_id',
            'essentials_employee_payment_settings.id as employee_payment_id',
            'accounts.id as payment_method_id',
            'accounts.name as payment_method_name',
            'accounts.account_number as payment_account_number'
        )
        ->get();
        $PM = Account::join('account_types','accounts.account_type_id','=','account_types.id');
        $paymentMethod = $PM->where(function ($query) use ($business_id) {
                $query->where('account_types.name','Like','%Expense%')
				->where(['account_types.business_id'=>$business_id])
				->where(['accounts.business_id'=>$business_id])
                ->orWhere(function ($query2) use ($business_id) {
                        $query2->where('account_types.name','Like','%Liabilities%')
						->where(['account_types.business_id'=>$business_id])
						->where(['accounts.business_id'=>$business_id]);
                    });
            })->select(
				'accounts.id',
				'accounts.name as name',
			)->first();
        
        $balance = $this->fetchBF($emp_id,$start_date,$end_date);
        $row['balance_t'] = $balance;
        return DataTables::of($ob)
                ->addColumn('date', function ($row) {
                    return $row->datetime_entered;
                })   
                ->addColumn('description', function ($row) use ($paymentMethod)  {
                    $html = '<span> <b>Payment Type:</b> '.$row->payment_type_name.'</span><br/> <span> <b>Payment Method:</b> '.$row->payment_method_name.'</span><br/> <span> <b>Salary Period:</b> '.$row->salary_period_start.' - '.$row->salary_period_end.'</span><br/><span> <b>Liability or Expense Account :</b> '.$paymentMethod->name.'</span>';
                    return $html;
                })
                ->addColumn('amount', function ($row) {
                    return $row->amount;
                })

                ->addColumn('paid_amount', function ($row) {
                    return $row->amount_paid; 
                })
                ->addColumn('balance', function ($row) {
                    return abs($row->amount-$row->amount_paid) ;
                })
                ->addColumn('payment_method', function ($row) {
                    return $row->payment_method_name;
                })
                ->rawColumns(['action', 'form_number', 'description'])
                ->make(true);
                            
    }
    public function fetchLedger(Request $request){
        $contact_id = $request->contact_id;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        
        $ob = EssentialsEmployee::join('transactions','transactions.id','essentials_employees.transaction_id')
                                ->where('essentials_employees.id',$contact_id)
                                ->whereDate('transaction_date','>=',$start_date)
                                ->where('transaction_date','<',$end_date)
                                ->where('employee_ob','!=',0)
                                ->select([
                                    DB::raw('"opening_balance" AS trans_type'),
                                    'transactions.transaction_date as operation_date',
                                    'employee_ob as amount',
                                    DB::raw('CASE WHEN employee_ob >= 0 THEN "credit" ELSE "debit" END AS type'),
                                ])
                                ->get();
            
        
        
        
        $balance = $this->fetchBF($contact_id,$start_date);
          
        return DataTables::of($ob)
                ->addColumn('payment_method', function ($row) {
                    
                    $html = '';
                    return $html;
                })
                ->addColumn('description', function ($row) {
                    
                    $html = ucfirst(str_replace('_',' ',$row->trans_type));
                    return $html;
                })
                ->editColumn('operation_date', '{{@format_date($operation_date)}}')
                ->editColumn('amount', function($row){
                    if($row->type == 'debit'){
                        return $this->transactionUtil->num_f(abs($row->amount));
                    }
                })
                ->addColumn('amount_paid',  function($row){
                    if($row->type == 'credit'){
                        return $this->transactionUtil->num_f(abs($row->amount));
                    }
                })
                ->addColumn('balance',  function($row) use(&$balance){
                        if($row->type == 'credit'){
                            $balance -= abs($row->amount);
                        }
                        
                        if($row->type == 'debit'){
                            $balance += abs($row->amount);
                        }
                    
                        
                        return $this->transactionUtil->num_f($balance);
                })
                
                ->rawColumns(['action', 'payment_method', 'description'])
                ->make(true);
                            
    }
    
    public function fetchBF($contact_id,$start_date,$end_date){
        // add other ledger records
        $bf = 0;
        
        // capture opening balances
        $ob = EssentialsEmployee::join('essentials_employee_advances','essentials_employees.id','essentials_employee_advances.employee_id')
        ->where('essentials_employees.id',$contact_id)
        ->whereDate('essentials_employee_advances.datetime_entered','>=',$start_date)
        ->where('essentials_employee_advances.datetime_entered','<',$end_date)
        ->select(
            'essentials_employee_advances.amount',
            'essentials_employee_advances.amount_paid'
        )->get();
        if(!empty($ob)){
            $totalAmount = round($ob->sum('amount'),2);
            $totalPaid = round($ob->sum('amount_paid'),2);
            $bf = round($totalAmount-$totalPaid,2);
            // $bf = $bf - abs($ob->employee_ob);
        }
        
        return $bf;
    }
    
    public function fetchSummary($contact_id,$start_date,$end_date){
        // add other ledger records
        
        $income = 0;
        $paid = 0;
        $bf = 0;
        
        // capture opening balances
        $ob = EssentialsEmployee::join('essentials_employee_advances','essentials_employees.id','essentials_employee_advances.employee_id')
        ->where('essentials_employees.id',$contact_id)
        ->whereDate('essentials_employee_advances.datetime_entered','>=',$start_date)
        ->where('essentials_employee_advances.datetime_entered','<',$end_date)
        ->select(
            'essentials_employee_advances.amount',
            'essentials_employee_advances.amount_paid'
        )->get();
        if(!empty($ob)){
            $income = $ob->sum('amount');
            $paid = $ob->sum('amount_paid');
            $bf = $income-$paid;
            // $bf = $bf - abs($ob->employee_ob);
        }
        
        return array('income' => $income, 'paid' => $paid);
    }


    public function getDesignationDepartment(Request $request)
    {
        if (! empty($request->input('cat_id'))) {
            $business_id = $request->input('cat_id');
            $business_id = $request->session()->get('user.business_id');
            $sub_categories = HrmDesignation::where('business_id', $business_id)
                ->where('id', $business_id)
                ->select(['name', 'id'])
                ->get();
            $html = '<option value="">None</option>';
            if (! empty($sub_categories)) {
                foreach ($sub_categories as $sub_category) {
                    $html .= '<option value="'.$sub_category->id.'">'.$sub_category->name.'</option>';
                }
            }
            echo $html;
            exit;
        }
    }
}
