<?php

namespace Modules\Essentials\Http\Controllers;
use Illuminate\Support\Facades\DB;

use App\Business;
use App\Category;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\HrmDepartment;
use Modules\Essentials\Entities\HrmDesignation;
use Modules\Essentials\Entities\Probation;
use Yajra\DataTables\DataTables;

class EssentialsSettingsController extends Controller
{
    /**
     * All Utils instance.
     */
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param  ProductUtils  $product
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil)
    {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return Response
     */
    public function edit()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if (! ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $settings = request()->session()->get('business');
        // dd($settings->essentials_settings);
        $settings = ! empty($settings) ? json_decode($settings->essentials_settings, true) : [];

         $departments = HrmDepartment::where('business_id', $business_id)
            ->pluck('name','id');

        $designations = HrmDesignation::where('business_id', $business_id)
            ->pluck('name','id');
            
            $probation_period = Probation::join('categories as departments', 'probations.department_id', '=', 'departments.id')
    ->join('categories as designations', 'probations.designation_id', '=', 'designations.id')
    ->where('departments.category_type', 'hrm_department')
    ->where('designations.category_type', 'hrm_designation')
  
    ->select(
        'probations.*',
        'departments.name as department_name',
        'designations.name as designation_name'
    )
    ->get();
   
        if ($is_admin) {
            return view('essentials::settings.add')->with(compact('settings','departments','designations','probation_period'));
        }
    }
    
    public function editEssential()
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if (! ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        $settings = request()->session()->get('business');
        // dd($settings->essentials_settings);
        $settings = ! empty($settings) ? json_decode($settings->essentials_settings, true) : [];

        if ($is_admin) {
            return view('essentials::essentials_settings.add')->with(compact('settings'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @return Response
     */
    public function update(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        // if (! ($this->moduleUtil->hasThePermissionInSubscription($business_id, 'essentials_module')) && ! $is_admin) {
        //     abort(403, 'Unauthorized action.');
        // }

        try {
            
            $business = Business::find($business_id);
            $setting = json_decode($business->essentials_settings);
            $input['employees_starting_number']= $request->employees_starting_number??$setting->employees_starting_number ?? null;
            $input['leave_ref_no_prefix']= $request->leave_ref_no_prefix??$setting->leave_ref_no_prefix ?? null;
            $input['leave_instructions']= $request->leave_instructions??$setting->leave_instructions ?? null;
            $input['payroll_ref_no_prefix']= $request->payroll_ref_no_prefix??$setting->payroll_ref_no_prefix ?? null;
            $input['essentials_todos_prefix']= $request->essentials_todos_prefix??$setting->essentials_todos_prefix ?? null;
            $input['grace_before_checkin']= $request->grace_before_checkin??$setting->grace_before_checkin ?? null;
            $input['grace_after_checkin']= $request->grace_after_checkin??$setting->grace_after_checkin ?? null;
            $input['grace_after_checkin']= $request->grace_after_checkin??$setting->grace_after_checkin ?? null;
            $input['grace_before_checkout']= $request->grace_before_checkout??$setting->grace_before_checkout ?? null;
            $input['grace_after_checkout']= $request->grace_after_checkout??$setting->grace_after_checkout ?? null;
            
            if($request->input('calculate_sales_target_commission_without_tax')){
                $input['calculate_sales_target_commission_without_tax']=1;
            }elseif($request->input('calculate_sales_target_commission_without_tax_one')){
            
                $input['calculate_sales_target_commission_without_tax']=0;
            }else{
                $input['calculate_sales_target_commission_without_tax']=$setting->calculate_sales_target_commission_without_tax ?? null;
            }
            
            if($request->input('is_location_required')){
                $input['is_location_required']=1;
            }elseif($request->input('is_location_required_one')){
            
                $input['is_location_required']=0;
            }else{
                $input['is_location_required']=$setting->is_location_required ?? null;
            }
            
            
            
            $business->essentials_settings = json_encode($input);
            $business->save();

            $request->session()->put('business', $business);

            $output = ['success' => 1,
                'msg' => trans('lang_v1.updated_succesfully'),
            ];
        } catch (\Exception $e) {
            
           dd($e->getMessage());
            \Log::emergency('File:'.$e->getFile().'Line:'.$e->getLine().'Message:'.$e->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
        }

        return redirect()->back()->with(['status' => $output]);
    }
    
    
    
    //fetch probation period 
    
     public function get_period_probation()
    {
        try {
            $probations = Probation::join('categories as departments', 'probations.department_id', '=', 'departments.id')
                ->join('categories as designations', 'probations.designation_id', '=', 'designations.id')
                ->where('departments.category_type', 'hrm_department')
                ->where('designations.category_type', 'hrm_designation')
                ->select([
                    'probations.id as id',
                    'probations.date_time',
                    'departments.name as department',
                    'designations.name as designation',
                    'probations.period',
                    'probations.status',
                    'probations.user_added'
                ]);

            return DataTables::of($probations)
                ->addColumn('action', function ($row) {
//                    $edit_url = route('probation.edit-data', [$row->id]);
//                    $delete_url = route('probation.delete-data', [$row->id]);
                    $edit_url = '#';
                    $delete_url = '#';

                    $edit_button = '<button data-href="' . $edit_url . '" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal">
                    <i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '
                </button>';

                    $delete_button = '<button data-href="' . $delete_url . '" class="btn btn-xs btn-danger delete-button">
                    <i class="glyphicon glyphicon-trash"></i> ' . __('messages.delete') . '
                </button>';

                    return $edit_button . ' ' . $delete_button;
                })
                ->editColumn('status', function ($row) {
                    return $row->status == 1
                        ? '<span class="label label-success h4">' . __('essentials::lang.active') . '</span>'
                        : '<span class="label label-danger h4">' . __('essentials::lang.inactive') . '</span>';
                })
                ->rawColumns(['action', 'status']) // Define columns that contain raw HTML
                ->make(true);
        }catch (\Exception $exception){
            //dd($exception->getMessage());
            \Log::emergency('File:'.$exception->getFile().'Line:'.$exception->getLine().'Message:'.$exception->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
            return redirect()->back()->with(['status' => $output]);
        }
    }


//update probation

public function update_probation_status(Request $request)
{
    try {
        $probation = Probation::findOrFail($request->id);
        $probation->status = $request->status;
        $probation->save();

        return response()->json(['success' => true, 'message' => 'Status updated successfully']);
    } catch (\Exception $exception) {
        \Log::emergency('File:'.$exception->getFile().' Line:'.$exception->getLine().' Message:'.$exception->getMessage());
        return response()->json(['success' => false, 'message' => 'Something went wrong']);
    }
}

    
    public function store_probation_period(Request $request)
    {
        try {
           
            $date_time = $request->input('date_time');
            $department = $request->input('department');
            $designation = $request->input('designation');
            $user = auth()->user()->name;
            $period = $request->input('period');

            $create = Probation::create([
               'date_time' => $date_time,
                'department_id' => $department,
                'designation_id' => $designation,
                'period' => $period,
                'user_added' => $user,
            ]);
            $output = ['success' => 1,
                'msg' => trans('lang_v1.updated_succesfully'),
            ];
            if($create){
                return redirect()->back()->with(['status' => $output]);
            }
        }catch (\Exception $exception){

            dd($exception->getMessage());
            \Log::emergency('File:'.$exception->getFile().'Line:'.$exception->getLine().'Message:'.$exception->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
            return redirect()->back()->with(['status' => $output]);
        }
    }

    public function get_desig_by_department(Request $request)
    {
        $designations = HrmDesignation::where('department_id', $request->department_id)->pluck('name', 'id');

        return response()->json($designations);
    }

    public function edit_probation($id)
    {

    }

    public function delete_probation(Request $request, $id)
    {

    }

}
