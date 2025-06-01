<?php

namespace Modules\AutoRepairServices\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use App\Contact;
use App\Brands;
use App\Product;
use App\BusinessLocation;
use App\Business;
use App\Category;
use App\CustomerReference;
use Modules\AutoRepairServices\Entities\DeviceModel;
use Modules\AutoRepairServices\Entities\RepairStatus;
use Modules\AutoRepairServices\Utils\RepairUtil;
use App\Utils\Util;
use Modules\AutoRepairServices\Entities\JobSheet;
use App\Utils\CashRegisterUtil;
use Yajra\DataTables\Facades\DataTables;
use DB;
use App\Utils\ModuleUtil;
use App\CustomerGroup;
use App\Utils\ContactUtil;
use App\Utils\ProductUtil;
use App\Media;
use Spatie\Activitylog\Models\Activity;
use App\new_vehicle;
use App\Utils\BusinessUtil;
use App\Utils\NotificationUtil;
use App\Transaction;

use Illuminate\Support\Facades\Log;

class JobSheetController extends Controller
{   
    /**
     * All Utils instance.
     *
     */
    protected $repairUtil;
    protected $commonUtil;
    protected $cashRegisterUtil;
    protected $moduleUtil;
    protected $contactUtil;
    protected $businessUtil;
    protected $notificationInfo;
    /**
     * Constructor
     *
     * @return void
     */
    public function __construct(RepairUtil $repairUtil, Util $commonUtil, CashRegisterUtil $cashRegisterUtil, ModuleUtil $moduleUtil,
        ContactUtil $contactUtil, ProductUtil $productUtil, BusinessUtil $businessUtil, NotificationUtil $notificationInfo)
    {
        $this->repairUtil = $repairUtil;
        $this->commonUtil = $commonUtil;
        $this->cashRegisterUtil = $cashRegisterUtil;
        $this->moduleUtil = $moduleUtil;
        $this->contactUtil = $contactUtil;
        $this->productUtil = $productUtil;
        $this->businessUtil = $businessUtil;
         $this->notificationInfo = $notificationInfo;
        
        
    }
  

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {  
        $business_id = request()->session()->get('user.business_id');
        
        
        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('job_sheet.create'))))) {
            abort(403, 'Unauthorized action.');
        }
       
        $is_user_admin = $this->commonUtil->is_admin(auth()->user(), $business_id);

      
        if (request()->ajax()) {
            $job_sheets = JobSheet::with('invoices')
                ->leftJoin('contacts', 'repair_job_sheets.contact_id', '=', 'contacts.id')
                ->leftJoin(
                    'repair_statuses AS rs',
                    'repair_job_sheets.status_id',
                    '=',
                    'rs.id'
                )
                ->leftJoin('users as technecian', 'repair_job_sheets.service_staff', '=', 'technecian.id')
                ->leftJoin(
                    'repair_device_models as rdm',
                    'rdm.id',
                    '=',
                    'repair_job_sheets.device_model_id'
                )
                ->leftJoin(
                    'brands AS b',
                    'repair_job_sheets.brand_id',
                    '=',
                    'b.id'
                )
                ->leftJoin(
                    'business_locations AS bl',
                    'repair_job_sheets.location_id',
                    '=',
                    'bl.id'
                )
                ->leftJoin(
                    'categories as device',
                    'device.id',
                    '=',
                    'repair_job_sheets.device_id'
                )
                ->leftJoin('users', 'repair_job_sheets.created_by', '=', 'users.id')
                ->where('repair_job_sheets.business_id', $business_id)
                ->where('repair_job_sheets.reportStatus', '=', '1')
                ->select('delivery_date', 'vehicle_id', 'job_sheet_no', DB::raw("CONCAT(COALESCE(technecian.surname, ''),' ',COALESCE(technecian.first_name, ''),' ',COALESCE(technecian.last_name,'')) as technecian"), DB::raw("CONCAT(COALESCE(users.surname, ''),' ',COALESCE(users.first_name, ''),' ',COALESCE(users.last_name,'')) as added_by"), 'contacts.name as customer', 'b.name as brand', 'rdm.name as device_model', 'serial_no', 'estimated_cost', 'rs.name as status', 'repair_job_sheets.id as id', 'repair_job_sheets.created_at as created_at', 'service_type', 'rs.color as status_color', 'bl.name as location', 'rs.is_completed_status', 'device.name as device', 'repair_job_sheets.custom_field_1', 'repair_job_sheets.custom_field_2', 'repair_job_sheets.custom_field_3', 'repair_job_sheets.custom_field_4', 'repair_job_sheets.custom_field_5')
                ->groupBy('repair_job_sheets.id');
        
            $job_sheet_status = JobSheet::where('repair_job_sheets.business_id', $business_id)
                ->leftJoin('repair_statuses', 'repair_job_sheets.status_id', '=', 'repair_statuses.id')
                ->where('repair_statuses.is_completed_status', 1)
                ->get();
            
            \Log::info("sqlContent: {$job_sheet_status}");
        
            // Store the job_sheet_status in a variable that will be available in the closure
            $has_completed_status = !$job_sheet_status->isEmpty();
         \Log::info("has_completed_status: {$has_completed_status}");
            //if user is not admin get only assgined/created_by job sheet
            if (!auth()->user()->can('job_sheet.view_all')) {
                if (!$is_user_admin) {
                    $user_id = auth()->user()->id;
                    $job_sheets->where(function ($query) use ($user_id) {
                        $query->where('repair_job_sheets.service_staff', $user_id)
                            ->orWhere('repair_job_sheets.created_by', $user_id);
                    });
                }
            }
        
            //if location is not all get only assgined location job sheet
            $permitted_locations = auth()->user()->permitted_locations();
            if ($permitted_locations != 'all') {
                $job_sheets->whereIn('repair_job_sheets.location_id', $permitted_locations);
            }
        
            //filter location
            if (!empty(request()->get('location_id'))) {
                $job_sheets->where('repair_job_sheets.location_id', request()->get('location_id'));
            }
        
            //filter by customer
            if (!empty(request()->contact_id)) {
                $job_sheets->where('repair_job_sheets.contact_id', request()->contact_id);
            }
        
            //filter by technecian
            if (!empty(request()->technician)) {
                $job_sheets->where('repair_job_sheets.service_staff', request()->technician);
            }
        
            //filter by status
            if (!empty(request()->status_id)) {
                $job_sheets->where('repair_job_sheets.status_id', request()->status_id);
            }
            if (!empty(request()->vehicle_no)) {
                $job_sheets->where('repair_job_sheets.vehicle_id', request()->vehicle_no);
            }
            //filter out mark as completed status
            if (request()->get('is_completed_status') === '1') {
                $job_sheets->where('rs.is_completed_status', 1);
            } else {
                $job_sheets->where(function ($q) {
                    $q->where('rs.is_completed_status', 0)
                        ->orWhereNull('rs.is_completed_status');
                });
            }
        
            return DataTables::of($job_sheets)
                ->addColumn('action', function ($row) use ($has_completed_status) { // Pass the variable to the closure
                    $html = '<div class="btn-group">
                                <button class="btn btn-info dropdown-toggle btn-xs" type="button"  data-toggle="dropdown" aria-expanded="false">
                                    ' . __("messages.action") . '
                                    <span class="caret"></span>
                                    <span class="sr-only">
                                    ' . __("messages.action") . '
                                    </span>
                                </button>';
        
                    $html .= '<ul class="dropdown-menu dropdown-menu-left" role="menu">';
        
                    if (auth()->user()->can("job_sheet.view_assigned") || auth()->user()->can("job_sheet.view_all") || auth()->user()->can("job_sheet.create")) {
                        $html .= '<li>
                            <a href="' . action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@show', ['job_sheet' => $row->id]) . '" class="cursor-pointer"><i class="fa fa-eye"></i> ' . __("messages.view") . '
                            </a>
                            </li>';
                    }
        
                    if (auth()->user()->can("repair.create") && $row->is_completed_status != 1) {
                        $html .= '<li>
                                    <a href="' . action('SellPosController@create') . '?sub_type=repair&job_sheet_id=' . $row->id . '" class="cursor-pointer"><i class="fas fa-plus-circle"></i> ' . __('autorepairservices::lang.add_invoice') . '
                                    </a>
                                </li>';
                    }
        
                    if (auth()->user()->can("job_sheet.edit") && $row->is_completed_status != 1) {
                        $html .= '<li>
                                    <a href="' . action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@edit', ['job_sheet' => $row->id]) . '" class="cursor-pointer edit_job_sheet"><i class="fa fa-edit"></i> ' . __("messages.edit") . '
                                    </a>
                                </li>';
        
                        $html .= '<li>
                                    <a href="' . action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@addParts', ['id' => $row->id]) . '" class="cursor-pointer">
                                        <i class="fas fa-toolbox"></i>
                                        ' . __("autorepairservices::lang.add_parts") . '
                                    </a>
                                </li>';
        
                        $html .= '<li>
                                    <a href="' . action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@getUploadDocs', ['id' => $row->id]) . '" class="cursor-pointer">
                                        <i class="fas fa-file-alt"></i>
                                        ' . __("autorepairservices::lang.upload_docs") . '
                                    </a>
                                </li>';
                    }
        
                    $html .= '<li>
                                    <a href="' . action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@print', ['id' => $row->id]) . '" target="_blank"><i class="fa fa-print"></i> ' . __("messages.print") . '
                                    </a>
                                </li>';
        
                    if ($has_completed_status) { // Use the passed variable here
                       // Example condition - disable if status is completed
                       $is_completed = $row->is_completed_status == 1;
                    $disabled = $is_completed ? 'disabled' : '';

                    $html .= '<li' . ($is_completed ? ' class="disabled"' : '') . '>
                                <a data-href="' . action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@editStatus', ['id' => $row->id]) . '" 
                                class="cursor-pointer edit_job_sheet_status" 
                                ' . $disabled . ' 
                                ' . ($is_completed ? 'style="pointer-events: none; opacity: 0.5;" onclick="return false;"' : '') . '>
                                    <i class="fa fa-edit"></i>
                                    ' . __("autorepairservices::lang.change_status") . '
                                </a>
                            </li>';
                    }
                    else
                    {
                        $html .= '<li>
                        <a data-href="' . action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@editStatus', ['id' => $row->id]) . '" class="cursor-pointer edit_job_sheet_status">
                            <i class="fa fa-edit"></i>
                            ' . __("autorepairservices::lang.change_status") . '
                        </a>
                    </li>';
                    }
        
                    if (auth()->user()->can("job_sheet.delete") && $row->is_completed_status != 1 ) {

                        $html .= '<li>
                                    <a data-href="' . action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@destroy', ['job_sheet' => $row->id]) . '"  id="delete_job_sheet" class="cursor-pointer">
                                        <i class="fas fa-trash"></i>
                                        ' . __("messages.delete") . '
                                    </a>
                                </li>';
                    }
        
                    $html .= '</ul>
                            </div>';
                    return $html;
                })
                ->editColumn(
                    'delivery_date',
                    '
                        @if($delivery_date)
                            {{@format_datetime($delivery_date)}}
                        @endif
                    '
                )
                ->editColumn(
                    'created_at',
                    '
                    {{@format_datetime($created_at)}}
                    '
                )
                ->editColumn('service_type', function ($row) {
                    return __('autorepairservices::lang.' . $row->service_type);
                })
                ->addColumn('vehicle_no', function ($row) {
                    return new_vehicle::where('id', $row->vehicle_id)->value('vehicle_no');
                })
                ->editColumn('estimated_cost', function ($row) {
                    $cost = '<span class="display_currency total-discount" data-currency_symbol="true" data-orig-value="' . $row->estimated_cost . '">' . $row->estimated_cost . '</span>';
        
                    return $cost;
                })
                ->editColumn('repair_no', function ($row) {
                    $invoice_no = [];
                    $add_invoice = '';
                    if ($row->invoices->count() > 0) {
                        foreach ($row->invoices as $key => $invoice) {
                            $invoice_no[] = $invoice->invoice_no;
                        }
                    }
        
                    if (empty($invoice_no) ) {
                        if (auth()->user()->can("repair.create") ) {
                            $add_invoice = '<br><a href="' . action('SellPosController@create') . '?sub_type=repair&job_sheet_id=' . $row->id . '" class="cursor-pointer" data-toggle="tooltip" title="' . __('autorepairservices::lang.add_invoice') . '">
                                    <i class="fas fa-plus-circle"></i>
                                </a>';
                        }
                    }
                    return implode(', ', $invoice_no) . $add_invoice;
                })
                ->editColumn('status', function ($row) {
                   if ($row->status == 'Completed') {
                    return '<span class="label" style="background-color:'.$row->status_color.';">
                                '.$row->status.'
                            </span>';
                } else {
                    $html = '<a data-href="' . action("\Modules\Repair\Http\Controllers\JobSheetController@editStatus", [$row->id]) . '" class="edit_job_sheet_status cursor-pointer" data-orig-value="'.$row->status.'" data-status-name="'.$row->status.'">
                                <span class="label" style="background-color:'.$row->status_color.';">
                                    '.$row->status.'
                                </span>
                            </a>';
                    return $html;
                }
                })
                ->rawColumns(['action', 'service_type', 'vehicle_no', 'delivery_date', 'repair_no', 'status', 'estimated_cost', 'created_at'])
                ->make(true);
        }

         
        $business_locations = BusinessLocation::forDropdown($business_id, false);
        
        $customers = Contact::customersDropdown($business_id, false);
        
        $status_dropdown = RepairStatus::forDropdown($business_id);
        $service_staffs = $this->commonUtil->serviceStaffDropdown($business_id);

        
        $user_role_as_service_staff = auth()->user()->roles()
                            ->where('is_service_staff', 1)
                            ->get()
                            ->toArray();
        
        $is_user_service_staff = false;
        if (!empty($user_role_as_service_staff) && !$is_user_admin) {
            $is_user_service_staff = true;
        }

        
        $repair_settings = $this->repairUtil->getRepairSettings($business_id);
        
        $vehicle=new_vehicle::vehicleDetails($business_id);
  
        return view('autorepairservices::job_sheet.index')
            ->with(compact('vehicle','business_locations', 'customers', 'status_dropdown', 'service_staffs', 'is_user_service_staff', 'repair_settings'));
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {   
        
        $status="1";
        $business_id = request()->session()->get('user.business_id');
        
        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('job_sheet.create')))) {
            abort(403, 'Unauthorized action.');
        }

        $repair_statuses = RepairStatus::getRepairSatuses($business_id);
        $device_models = DeviceModel::forDropdown($business_id);
 
        $brands = Brands::forDropdownAutoRepair($business_id, false, true);

        $devices = Product::where('business_id', $business_id )
                    ->where('product_custom_field1','auto-device')
                    ->pluck('name','id');
        $repair_settings = $this->repairUtil->getRepairSettings($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $types = Contact::getContactTypes();
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $walk_in_customer = [];//$this->contactUtil->getWalkInCustomer($business_id);
        $default_status = '';
        if (!empty($repair_settings['default_status'])) {
            $default_status = $repair_settings['default_status'];
        }

        //get service staff(technecians)
        $technecians = [];
        if ($this->commonUtil->isModuleEnabled('service_staff')) {
            $technecians = $this->commonUtil->serviceStaffDropdown($business_id);
        }
        
        $contact_id = $this->businessUtil->check_customer_code($business_id);
        $vehicals = new_vehicle::where('business_id', $business_id)
        ->pluck('vehicle_no', 'id');

        $last_inserted_vehicel_id = '';
        if(request()->session()->has('vehicle_id')){
            $last_inserted_vehicel_id = request()->session()->get('vehicle_id');
            request()->session()->forget('vehicle_id');
        }
        
        // New Hagop
         $customer_name = '';
         $customer_phone = '';
        if (session()->has('selected_customer_id')) {
            $selected_customer_id = session('selected_customer_id');
            $selected_customer = Contact::find($selected_customer_id);
            if ($selected_customer) {
                $contact_id = $selected_customer->id;
                $customer_name = $selected_customer->name;
                $customer_phone = $selected_customer->mobile;
            }
        }
        // New Hagop
        
        return view('autorepairservices::job_sheet.create')
            ->with(compact('status','repair_statuses', 'device_models', 'brands', 'devices', 'default_status', 'technecians', 'business_locations', 'types', 'customer_groups', 'walk_in_customer', 'repair_settings', 'contact_id', 'vehicals', 'last_inserted_vehicel_id','customer_name','customer_phone'));
    }

    public function create1()
    {   
        
        $business_id = request()->session()->get('user.business_id');
        
        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('job_sheet.create')))) {
            abort(403, 'Unauthorized action.');
        }

        $repair_statuses = RepairStatus::getRepairSatuses($business_id);
        $device_models = DeviceModel::forDropdown($business_id);
        $brands = Brands::forDropdownAutoRepair($business_id, false, true);
        $devices = Category::forDeviceDropdownAuto($business_id);
        $repair_settings = $this->repairUtil->getRepairSettings($business_id);
        $business_locations = BusinessLocation::forDropdown($business_id);
        $types = Contact::getContactTypes();
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $walk_in_customer = $this->contactUtil->getWalkInCustomer($business_id);
        $default_status = '';
        if (!empty($repair_settings['default_status'])) {
            $default_status = $repair_settings['default_status'];
        }

        //get service staff(technecians)
        $technecians = [];
        if ($this->commonUtil->isModuleEnabled('service_staff')) {
            $technecians = $this->commonUtil->serviceStaffDropdown($business_id);
        }

        return view('autorepairservices::job_sheet.create')
            ->with(compact('repair_statuses', 'device_models', 'brands', 'devices', 'default_status', 'technecians', 'business_locations', 'types', 'customer_groups', 'walk_in_customer', 'repair_settings'));
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('job_sheet.create')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            ////////////////////////////////////6029//////////////////////////////////
            $input = $request->only('vehicle_id','contact_id', 'service_type', 'brand_id', 'device_id', 'device_model_id', 'security_pwd', 'security_pattern', 'serial_no', 'status_id', 'delivery_date', 'estimated_cost', 'product_configuration', 'defects', 'product_condition', 'service_staff', 'location_id', 'pick_up_on_site_addr', 'comment_by_ss', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4', 'custom_field_5','warranty_number');
           //////////////////////////////////// end 6029//////////////////////////////////
            $reviewed = $this->commonUtil->get_review(date('Y-m-d'),date('Y-m-d'));
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't add a job sheet for an already reviewed date",
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
            
            if (!empty($input['delivery_date'])) {
                $input['delivery_date'] = $this->commonUtil->uf_date($input['delivery_date'], true);
            }

            if (!empty($input['estimated_cost'])) {
                $input['estimated_cost'] = $this->commonUtil->num_uf($input['estimated_cost']);
            }

            if (!empty($request->input('repair_checklist'))) {
                $input['checklist'] = $request->input('repair_checklist');
            }

            DB::beginTransaction();

            //Generate reference number
            $ref_count = $this->commonUtil->setAndGetReferenceCount('job_sheet', $business_id);
            $business = Business::find($business_id);
            $repair_settings = json_decode($business->repair_settings, true);

            $job_sheet_prefix = '';
            if (isset($repair_settings['job_sheet_prefix'])) {
                $job_sheet_prefix = $repair_settings['job_sheet_prefix'];
            }

            $input['job_sheet_no'] = $this->commonUtil->generateReferenceNumber('job_sheet', $ref_count, null, $job_sheet_prefix);

            $input['created_by'] = $request->user()->id;
            $input['business_id'] = $business_id;
            $input['reportStatus'] = 1;
            $job_sheet = JobSheet::create($input);
            
             // New Hagop
             $customer_id = $request->input('contact_id');
             session(['selected_customer_id' => $customer_id]);
             // New Hagop

            //upload media
            Media::uploadMedia($business_id, $job_sheet, $request, 'images');
             $activity = new Activity();
            $activity->log_name = "Job Sheet Repair";
            $activity->description = "Reapir";
            $activity->subject_id = $job_sheet->id;
            $activity->subject_type = JobSheet::class; // Use the actual JobSheet model
            $activity->causer_id = auth()->id(); // The user ID who performed the action
            $activity->causer_type = JobSheet::class; // Assign a valid Eloquent model
            $activity->properties = json_encode(['type' => 'repair']); // Store as JSON
            $activity->created_at = now();
            $activity->updated_at = now();
            
            // Save the activity
            $activity->save();
            
             if (!empty($request->input('send_notification')) && in_array('sms', $request->input('send_notification'))) {
                $status = RepairStatus::where('business_id', $business_id)
                            ->find($job_sheet->status_id);
                if (!empty($status->sms_template)) $this->repairUtil->sendJobSheetUpdateSmsNotification($status->sms_template, $job_sheet); 
             }

            if (!empty($request->input('send_notification')) && in_array('email', $request->input('send_notification'))) {
                $status = RepairStatus::where('business_id', $business_id)
                            ->find($job_sheet->status_id);
                $notification = [
                        'subject' => $status->email_subject,
                        'body' => $status->email_body
                    ];
                
                //Set email configuration
                //$notificationUtil = new \App\Utils\NotificationUtil();
               // $notificationUtil->configureEmail();
               try {
               $notificationUtil = new NotificationUtil($notificationInfo);
                $notificationUtil->configureEmail($notificationInfo, false);
                if (!empty($status->email_subject) && !empty($status->email_body))
                {
                    
                     $this->repairUtil->sendJobSheetUpdateEmailNotification($notification, $job_sheet); 
                }
        
               }
           catch (\Exception $e) {
                        // Log the error using Laravel's logging system
                        // Log::error('Error sending job sheet email notification:', [
                        //     'error_message' => $e->getMessage(),
                        //     'file' => $e->getFile(),
                        //     'line' => $e->getLine(),
                        //     'trace' => $e->getTraceAsString()
                        // ]);
                        
                        // Optionally display a message to the user
                       // return redirect()->back()->with('error', 'An error occurred while sending the email notification.');
                    }
                
             
            }
            
            DB::commit();

            if (!empty($request->input('submit_type')) && $request->input('submit_type') == 'save_and_add_parts') {
                return redirect()
                ->action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@addParts', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
            } elseif (!empty($request->input('submit_type')) && $request->input('submit_type') == 'save_and_upload_docs') {
                return redirect()
                    ->action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@getUploadDocs', [$job_sheet->id])
                    ->with('status', ['success' => true, 'msg' => __("lang_v1.success")]);
            }

            return redirect()
                ->action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@show', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);

        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('status', ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function show($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('job_sheet.create'))))) {
            abort(403, 'Unauthorized action.');
        }
  $user_name=auth()->user()->username;
        $query = JobSheet::with('customer',
                        'customer.business', 'technician',
                        'status', 'Brand', 'Device', 'deviceModel', 'businessLocation', 'invoices', 'media')
                        ->where('business_id', $business_id);
                        
        //if user is not admin or didn't have permission `job_sheet.view_all` get only assgined/created_by job sheet
        if (!($this->commonUtil->is_admin(auth()->user(), $business_id) || auth()->user()->can('job_sheet.view_all'))) {
            $user_id = auth()->user()->id;
          
            $query->where(function ($q) use ($user_id){
                $q->where('repair_job_sheets.service_staff', $user_id)
                    ->orWhere('repair_job_sheets.created_by', $user_id);
            });
        }

        $job_sheet = $query->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->repair_settings, true);

        $activities = Activity::forSubject($job_sheet)
           ->with(['causer', 'subject'])
           ->latest()
           ->get();
        
        return view('autorepairservices::job_sheet.show')
            ->with(compact('job_sheet', 'repair_settings', 'parts', 'activities','user_name'));
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Response
     */
    public function edit($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('use') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('job_sheet.edit')))) {
            abort(403, 'Unauthorized action.');
        }

        $job_sheet = JobSheet::where('business_id', $business_id)
                    ->findOrFail($id);

        $repair_statuses = RepairStatus::getRepairSatuses($business_id);
        $device_models = DeviceModel::forDropdown($business_id);
        $brands = Brands::forDropdownAutoRepair($business_id, false, true);
        $devices = Category::forDeviceDropdownAuto($business_id);
        $repair_settings = $this->repairUtil->getRepairSettings($business_id);
        $types = Contact::getContactTypes();
        $customer_groups = CustomerGroup::forDropdown($business_id);
        $default_status = '';
        if (!empty($repair_settings['default_status'])) {
            $default_status = $repair_settings['default_status'];
        }

        //get service staff(technecians)
        $technecians = [];
        if ($this->commonUtil->isModuleEnabled('service_staff')) {
            $technecians = $this->commonUtil->serviceStaffDropdown($business_id);
        }
        
        $contact_id = $this->businessUtil->check_customer_code($business_id);

        return view('autorepairservices::job_sheet.edit')
            ->with(compact('job_sheet', 'repair_statuses', 'device_models', 'brands', 'devices', 'default_status', 'technecians', 'types', 'customer_groups', 'repair_settings', 'contact_id'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Response
     */
    public function update(Request $request, $id)
    {

        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('use') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('job_sheet.edit')))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            ////////////////////////////////////6029//////////////////////////////////
            $input = $request->only('contact_id', 'service_type', 'brand_id', 'device_id', 'device_model_id', 'security_pwd', 'security_pattern', 'serial_no', 'status_id', 'delivery_date', 'estimated_cost', 'product_configuration', 'defects', 'product_condition', 'service_staff', 'pick_up_on_site_addr', 'comment_by_ss', 'custom_field_1', 'custom_field_2', 'custom_field_3', 'custom_field_4', 'custom_field_5','warranty_number');
            //////////////////////////////////// end 6029/////////////////////////////////
            
            $has_reviewed = $this->commonUtil->hasReviewed($input['delivery_date']);
        
            if(!empty($has_reviewed)){
                $output              = [
                    'success' => 0,
                    'msg'     =>__('lang_v1.review_first'),
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
            
            $reviewed = $this->commonUtil->get_review($input['delivery_date'],$input['delivery_date']);
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't edit a job for an already reviewed date",
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
            
            if (!empty($input['delivery_date'])) {
                $input['delivery_date'] = $this->commonUtil->uf_date($input['delivery_date'], true);
            }

            if (!empty($input['estimated_cost'])) {
                $input['estimated_cost'] = $this->commonUtil->num_uf($input['estimated_cost']);
            }

            if (!empty($request->input('repair_checklist'))) {
                $input['checklist'] = $request->input('repair_checklist');
            } else {
                $input['checklist'] = [];
            }
            
            DB::beginTransaction();

            $job_sheet = JobSheet::where('business_id', $business_id)
                            ->findOrFail($id);
                            
            $job_sheet->update($input);

            //upload media
            Media::uploadMedia($business_id, $job_sheet, $request, 'images');
            
            // if (!empty($request->input('send_notification')) && in_array('sms', $request->input('send_notification'))) {
                $status = RepairStatus::where('business_id', $business_id)
                            ->find($job_sheet->status_id);
                if (!empty($status->sms_template)) $this->repairUtil->sendJobSheetUpdateSmsNotification($status->sms_template, $job_sheet); 
            // }
            
            if (!empty($request->input('send_notification')) && in_array('email', $request->input('send_notification'))) {
                $status = RepairStatus::where('business_id', $business_id)
                            ->find($job_sheet->status_id);
                $notification = [
                        'subject' => $status->email_subject,
                        'body' => $status->email_body
                    ];
                
                //Set email configuration
                $notificationUtil = new \App\Utils\NotificationUtil();
                $notificationUtil->configureEmail();

                if (!empty($status->email_subject) && !empty($status->email_body)) $this->repairUtil->sendJobSheetUpdateEmailNotification($notification, $job_sheet); 
            }

            DB::commit();

            if (!empty($request->input('submit_type')) && $request->input('submit_type') == 'save_and_add_parts') {
                return redirect()
                ->action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@addParts', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
            } elseif (!empty($request->input('submit_type')) && $request->input('submit_type') == 'save_and_upload_docs') {
                return redirect()
                    ->action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@getUploadDocs', [$job_sheet->id])
                    ->with('status', ['success' => true, 'msg' => __("lang_v1.success")]);
            }

            return redirect()
                ->action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@show', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
                
        } catch (\Exception $e) {
            DB::rollBack();
            
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());

            return redirect()->back()
                ->with('status', ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Response
     */
    public function destroy($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('use') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && auth()->user()->can('job_sheet.delete')))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $job_sheet = JobSheet::where('business_id', $business_id)
                    ->findOrFail($id);
                    
                    
                    
                $reviewed = $this->commonUtil->get_review($job_sheet->delivery_date,$job_sheet->delivery_date);
            
                if(!empty($reviewed)){
                    $output = [
                        'success' => 0,
                        'msg'     =>"You can't delete a job sheet for an already reviewed date",
                    ];
                    
                    return $output;
                }

                $job_sheet->delete();
                $job_sheet->media()->delete();
                
                $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
            } catch (\Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    /**
     * Show the form for editing the status
     * @param int $id
     * @return Response
     */
    public function editStatus($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('use') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.edit'))))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {

            $job_sheet = JobSheet::where('business_id', $business_id)->with(['status'])->findOrFail($id);
            $job_sheet_status = JobSheet::where('repair_job_sheets.business_id', $business_id)
                ->leftJoin('repair_statuses', 'repair_job_sheets.status_id', '=', 'repair_statuses.id')
                ->where('repair_statuses.is_completed_status', 1)
                ->get();
            
            \Log::info("sqlContent: {$job_sheet_status}");
        
            // Store the job_sheet_status in a variable that will be available in the closure
            $has_completed_status = !$job_sheet_status->isEmpty();
             \Log::info("has_completed_status: {$job_sheet_status->isEmpty()}");
            $status_dropdown = RepairStatus::forDropdown($business_id, true);
            $status_template_tags = $this->repairUtil->getRepairStatusTemplateTags();
            return view('autorepairservices::job_sheet.partials.edit_status')
                ->with(compact('job_sheet', 'status_dropdown', 'status_template_tags','has_completed_status'));
        }
    }

    private function updateJobsheetStatus($input, $jobsheet_id)
    {
        $job_sheet = JobSheet::where('business_id', $input['business_id'])->findOrFail($jobsheet_id);
        
        
        
        $reviewed = $this->commonUtil->get_review($job_sheet->delivery_date,$job_sheet->delivery_date);
            
        if(!empty($reviewed)){
            $output = [
                'success' => 0,
                'msg'     =>"You can't modify a job sheet for an already reviewed date",
            ];
            
            return $output;
        }
        
        $job_sheet->status_id = $input['status_id'];
        $job_sheet->save();

        $status = RepairStatus::where('business_id', $input['business_id'])->findOrFail($input['status_id']);

        //send job sheet updates
        if (!empty($input['send_sms'])) {
            $sms_body = $input['sms_body'];
            $response = $this->repairUtil->sendJobSheetUpdateSmsNotification($sms_body, $job_sheet);
        }

        if (!empty($input['send_email'])) {
                $subject = $input['email_subject'];
                $body = $input['email_body'];
                $notification = [
                    'subject' => $subject,
                    'body' => $body
                ];
            
            //Set email configuration
            $notificationUtil = new \App\Utils\NotificationUtil();
            $notificationUtil->configureEmail();

            if (!empty($subject) && !empty($body)) $this->repairUtil->sendJobSheetUpdateEmailNotification($notification, $job_sheet); 
        }

        activity()
            ->performedOn($job_sheet)
            ->withProperties(['update_note' => $input['update_note'], 'updated_status' => $status->name  ])
            ->log('status_changed');
    }

    public function updateStatus(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('use') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.edit'))))) {
            abort(403, 'Unauthorized action.');
        }

        if ($request->ajax()) {
            try {
                $input = $request->only([
                    'status_id',
                    'update_note'
                ]);

                $input['business_id'] = $business_id;

                if (!empty($request->input('send_sms'))) {
                    $input['send_sms'] = true;
                    $input['sms_body'] = $request->input('sms_body');
                }
                if (!empty($request->input('send_email'))) {
                    $input['send_email'] = true;
                    $input['email_body'] = $request->input('email_body');
                    $input['email_subject'] = $request->input('email_subject');
                }
                $status_id = $request->input('status_id');

                $status = RepairStatus::find($status_id);

                if ($status->is_completed_status == 1) {
                    $input['job_sheet_id'] = $id;
                    $request->session()->put('repair_status_update_data', $input);
                    return $output = ['success' => true];
                }

                $this->updateJobsheetStatus($input, $id);

                $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
            } catch (Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    public function deleteJobSheetImage(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('use') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('job_sheet.create'))))) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {

                Media::deleteMedia($business_id, $id);
                
                $output = ['success' => true,
                    'msg' => __("lang_v1.success")
                ];
            } catch (\Exception $e) {
                $output = ['success' => false,
                    'msg' => __('messages.something_went_wrong')
                ];
            }

            return $output;
        }
    }

    public function addParts($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('use') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.edit'))))) {
            abort(403, 'Unauthorized action.');
        }

        $status_update_data = request()->session()->get('repair_status_update_data');

        $job_sheet = JobSheet::where('business_id', $business_id)->findOrFail($id);

       $parts = $job_sheet->getPartsUsed();

$parts_data = [];

if (!empty($parts)) {
    foreach ($parts as $part) {
        $parts_data[] = [
            'variation_name' => $part['variation_name'],
            'variation_id' => $part['variation_id'],
            'quantity' => $part['quantity'],
            'unit' => $part['unit'],
            'unit_price' => $part['unit_price'],
            'purchase_price' => $part['purchase_price'],
        ];
    }
}
 
           if (!empty($parts_data)) {
    $first_part = $parts_data[0];

    $variation_name  = $first_part['variation_name'];
    $variation_id    = $first_part['variation_id'];
    $quantity        = $first_part['quantity'];
    $unit            = $first_part['unit'];
    $unit_price      = $first_part['unit_price'];
    $purchase_price  = $first_part['purchase_price'];
}
else
{
 $variation_name  = '';
    $variation_id    = '';
    $quantity        = 1;
    $unit            = '';
    $unit_price      = 1;
    $purchase_price  = 1;
}

        $status_dropdown = RepairStatus::forDropdown($business_id, true);
        $status_template_tags = $this->repairUtil->getRepairStatusTemplateTags();

        return view('autorepairservices::job_sheet.add_parts')
            ->with(compact('job_sheet', 'parts', 'status_update_data', 'status_dropdown', 'status_template_tags','variation_name','variation_id','quantity','unit','unit_price','purchase_price'));
    }

    public function saveParts(Request $request, $id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.edit'))))) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $parts = $request->input('parts');
            
            $job_sheet = JobSheet::where('business_id', $business_id)->findOrFail($id);
            
            $reviewed = $this->commonUtil->get_review($job_sheet->delivery_date,$job_sheet->delivery_date);
            
            if(!empty($reviewed)){
                $output = [
                    'success' => 0,
                    'msg'     =>"You can't modify a job sheet for an already reviewed date",
                ];
                
                return redirect()->back()->with(['status' => $output]);
            }
            
        
            $job_sheet->parts = !empty($parts) ? $parts : null;
            $job_sheet->save();

            if (!empty($request->session()->get('repair_status_update_data')) && !empty($request->input('status_id'))) {
                $input = $request->only([
                    'status_id',
                    'update_note'
                ]);

                $input['business_id'] = $business_id;

                if (!empty($request->input('send_sms'))) {
                    $input['send_sms'] = true;
                    $input['sms_body'] = $request->input('sms_body');
                }
                if (!empty($request->input('send_email'))) {
                    $input['send_email'] = true;
                    $input['email_body'] = $request->input('email_body');
                    $input['email_subject'] = $request->input('email_subject');
                }

                $this->updateJobsheetStatus($input, $job_sheet->id);

                $request->session()->forget('repair_status_update_data');
            }

            $output = ['success' => true,
                'msg' => __("lang_v1.success")
            ];
        } catch (\Exception $e) {
            
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
        }

        return redirect()
                ->action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@show', [$job_sheet->id])
                ->with('status', ['success' => true,
                    'msg' => __("lang_v1.success")]);
    }

    public function jobsheetPartRow(Request $request)
    {
        if (request()->ajax()) {
            $variation_id = $request->input('variation_id');

            $business_id = $request->session()->get('user.business_id');
            $product = $this->productUtil->getDetailsFromVariation($variation_id, $business_id);
    \Log::info("sqlContent: {$product}");
     $variation_name = '';
            $variation_id = '';
            $quantity = 1;
            $unit = '';
            $unit_price=0;
            $purchase_price=0;
    if(!empty($product))
    {

            $variation_name = $product->product_name . ' - ' . $product->sub_sku;
            $variation_id = $product->variation_id;
            $quantity = 1;
            $unit = $product->unit;
            $unit_price=$product->default_sell_price;
            $purchase_price=$product->purchase_price;
    }
   
            
            return view('autorepairservices::job_sheet.partials.job_sheet_part_row')
            ->with(compact('variation_name', 'variation_id', 'quantity', 'unit','unit_price','purchase_price'));
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Response
     */
    public function print($id)
    {   
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.view_assigned') || auth()->user()->can('job_sheet.view_all') || auth()->user()->can('job_sheet.create'))))) {
            abort(403, 'Unauthorized action.');
        }

        $query = JobSheet::with('customer',
                        'customer.business', 'technician',
                        'status', 'Brand', 'Device', 'deviceModel', 'businessLocation', 'invoices', 'media')
                        ->where('business_id', $business_id);
                        
        //if user is not admin or didn't have permission `job_sheet.view_all` get only assgined/created_by job sheet
        if (!($this->commonUtil->is_admin(auth()->user(), $business_id) || auth()->user()->can('job_sheet.view_all'))) {
            $user_id = auth()->user()->id;
            $query->where(function ($q) use ($user_id){
                $q->where('repair_job_sheets.service_staff', $user_id)
                    ->orWhere('repair_job_sheets.created_by', $user_id);
            });
        }

        $job_sheet = $query->findOrFail($id);

        $parts = $job_sheet->getPartsUsed();

        $business = Business::find($business_id);
        $repair_settings = json_decode($business->repair_settings, true);
        
        $html = view('autorepairservices::job_sheet.print_pdf')
            ->with(compact('job_sheet', 'repair_settings', 'parts'))->render();
        $mpdf = new \Mpdf\Mpdf(['tempDir' => public_path('uploads/temp'), 
                    'mode' => 'utf-8', 
                    'autoScriptToLang' => true,
                    'autoLangToFont' => true,
                    'autoVietnamese' => true,
                    'autoArabic' => true,
                    'margin_top' => 8,
                    'margin_bottom' => 8
                ]);
        $mpdf->useSubstitutions=true;
        $mpdf->SetTitle(__('autorepairservices::lang.job_sheet') . ' | ' . $job_sheet->job_sheet_no);
        $mpdf->WriteHTML($html);
        $mpdf->Output('job_sheet.pdf', 'I');

        return view('autorepairservices::job_sheet.print_pdf')
            ->with(compact('job_sheet', 'repair_settings', 'parts'));
    }

    public function getUploadDocs($id)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.edit'))))) {
            abort(403, 'Unauthorized action.');
        }

        $job_sheet = JobSheet::with(['media'])
                        ->where('business_id', $business_id)
                        ->findOrFail($id);

        return view('autorepairservices::job_sheet.upload_doc', compact('job_sheet'));
    }

    public function postUploadDocs(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');

        if (!(auth()->user()->can('user') || ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'repair_module') && (auth()->user()->can('job_sheet.create') || auth()->user()->can('job_sheet.edit'))))) {
            abort(403, 'Unauthorized action.');
        }

        try {

            $images = json_decode($request->input('images'), true);

            $job_sheet = JobSheet::where('business_id', $business_id)
                        ->findOrFail($request->input('job_sheet_id'));

            if (!empty($images) && !empty($job_sheet)) {

                Media::attachMediaToModel($job_sheet, $business_id, $images);
            }

            $output = ['success' => true,
                'msg' => __("lang_v1.success")
            ];

        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile(). "Line:" . $e->getLine(). "Message:" . $e->getMessage());
            $output = ['success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()
            ->action('\Modules\AutoRepairServices\Http\Controllers\JobSheetController@show', [$job_sheet->id])
            ->with('status', ['success' => true,
                'msg' => __("lang_v1.success")]);
    }

    function getVehicleData($id)
    {
        $data=new_vehicle::where('customer_id',$id)->get();
        $customerReference = CustomerReference::where('contact_id', $id)
             ->select('id as customer_id', 'reference as vehicle_no')->get();

          
        $data = $data->concat($customerReference);
        return $data;
    }

    function getCustomerData($id)
    {
        $data=Contact::leftJoin('new_vehicle', 'new_vehicle.customer_id', '=', 'contacts.id')
            ->where('new_vehicle.id',$id)
            ->select('contacts.id', 'contacts.name')->get();

        return $data;
    }
    
    // New Hagop
        public function redirectToPos($job_sheet_id)
        {
            $business_id = request()->session()->get('user.business_id');
        
            $job_sheet = JobSheet::with('customer', 'vehicle')
                ->where('business_id', $business_id)
                ->findOrFail($job_sheet_id);
        
            $customer_id = optional($job_sheet->customer)->id;
            $customer_name = optional($job_sheet->customer)->name;
            $customer_phone = optional($job_sheet->customer)->mobile;
            $vehicle_number = optional($job_sheet->vehicle)->vehicle_no;
        
            // Store session data to use later in POS
            session([
                'job_sheet_id' => $job_sheet_id,
                'pos_customer_id' => $customer_id,
                'pos_customer_name' => $customer_name,
                'pos_customer_phone' => $customer_phone,
                'pos_vehicle_number' => $vehicle_number
            ]);
        
            return redirect()->route('sell.create'); 
        }

    // New Hagop
    public function listInvoices()
    {
        $business_id = request()->session()->get('user.business_id');
    
        if (!auth()->user()->can('sell.view') && !auth()->user()->can('direct_sell.access')) {
            abort(403, 'Unauthorized action.');
        }
    
        $invoices = Transaction::leftJoin('contacts', 'transactions.contact_id', '=', 'contacts.id')
            ->leftJoin('repair_job_sheets', 'transactions.job_sheet_id', '=', 'repair_job_sheets.id')
            ->leftJoin('business_locations', 'transactions.location_id', '=', 'business_locations.id')
            ->where('transactions.business_id', $business_id)
            ->where('transactions.type', 'sell')
            ->whereNotNull('transactions.job_sheet_id')
            ->select(
                'transactions.invoice_no',
                'transactions.final_total',
                'transactions.transaction_date',
                'contacts.name as customer_name',
                'repair_job_sheets.vehicle_id',
                'repair_job_sheets.job_sheet_no',
                'business_locations.name as location_name'
            )
            ->latest('transactions.transaction_date')
            ->paginate(20);
    
        return view('autorepairservices::invoices.index')->with(compact('invoices'));
    }
    // New Hagop
    
}
