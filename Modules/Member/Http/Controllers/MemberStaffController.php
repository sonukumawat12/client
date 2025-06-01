<?php

namespace Modules\Member\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\District;
use Modules\Member\Entities\JobTitle;
use Modules\Member\Entities\Province;
use Modules\Member\Entities\Electrorate;
use Modules\Member\Entities\MemberStaff;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;
use Modules\Member\Entities\MemberDesignation;
use Modules\Member\Entities\Suggestion;

/**
 * undocumented class
 */
class MemberStaffController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $items = MemberStaff::with('user','designation')->where('business_id', $business_id)->latest();
         

            return DataTables::of($items)
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
                        $html .= '<li><a href="javascript:void(0)" data-href="' . action('\Modules\Member\Http\Controllers\MemberStaffController@edit', [$row->id]) . '" class="btn-modal" data-container=".common_model"><i class="glyphicon glyphicon-edit"></i> ' . __("messages.edit") . '</a></li>';

                        $html .= '<li><a href="javascript:void(0)" data-href="' . action('\Modules\Member\Http\Controllers\MemberStaffController@destroy', [$row->id]) . '" class="delete-button"><i class="fa fa-trash"></i> ' . __("messages.delete") . '</a></li>';
                        $html .= '<li class="divider"></li>';
                        $html .= '<li><div class="form-group m-5">
                                        <div class="checkbox checbox-switch switch-success">
                                            <label>';
                        $html .= '<input type="checkbox" name="change_status['.$row->id.']" value="enabled" class="change_status"  data-row="'.$row->id.'" data-href="' . action('\Modules\Member\Http\Controllers\MemberStaffController@updateStatus', [$row->id]) . '"';
                        if($row->status == 'Enabled')
                        $html .= ' checked />';
                        else
                        $html .= ' />';
                        
                        $html .= '<span></span>
                                                Enabled
                                            </label>
                                        </div>
                                    </div></li>';
                        
                        // $html .= '<li><a href="#" data-href="' . action('\Modules\Member\Http\Controllers\MemberStaffController@updateStatus', [$row->id,$row->status]) . '" ><i class="fa fa-adjust"></i> ' . __("member::lang.status") . '</a></li>';

                    $html .= '</ul></div>';

                        return $html;
                    }
                    
                )
                ->editColumn('join_date', '{{@format_date($join_date)}}')
                ->editColumn('job_id',function($row){
                    return $row->designation->job_title;
                })
                ->editColumn('created_by',function($row){
                    return $row->user->username;
                })
                ->editColumn('status',function($row){
                    return $row->status;
                })
               
                ->removeColumn('id')
                ->rawColumns(['action'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        return view('member::settings.staff.create');
    }

    /**
     * Store a newly created resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        try {
            $data = $request->except('_token');
               $exists = MemberStaff::where('name', $data['name'])
                 
                  ->exists();
                  
          if (!$exists) {
            MemberStaff::create([
                'business_id' => $business_id,
                'name' => $data['name'],
                'job_id'=> $this->storeOrUpdateDesignation($request),
                'join_date' => !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d'),
                'created_by' => auth()->user()->id,
            ]);

            //Create a new permission related to the created gramaseva_vasama
            // Permission::create(['name' => 'gramaseva_vasama.' . $gramaseva_vasama->id]);

            $output = [
                'success' => true,
                'tab' => 'staff_to_assign',
                'msg' => __('member::lang.staff_member_create_success')
            ];
          } else {
               $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
            }
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'staff_to_assign',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('member::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit(Request $request,$id)
    {
        $item = MemberStaff::with(
            'designation'
        )->findOrFail($id);
        return view('member::settings.staff.edit')->with(compact('item'));
    }

    /**
     * Update the specified resource in storage.
     * @param  Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        try {
            $data = $request->except('_token', '_method');
            
            
            MemberStaff::where('id', $id)->update([
                'name' => $data['name'],
                'job_id'=> $this->storeOrUpdateDesignation($request),
                'join_date' => !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d'),
            ]);
            $output = [
                'success' => true,
                'tab' => 'staff_to_assign',
                'msg' => __('member::lang.staff_member_update_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'staff_to_assign',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        try {
            MemberStaff::where('id', $id)->delete();
            $output = [
                'success' => true,
                'tab' => 'electrorate',
                'msg' => __('member::lang.staff_member_delete_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'electrorate',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    
    /**
     * Store Or Update Designation
     * 
     **/
    public function storeOrUpdateDesignation(Request $request)
    {
        if($request->designation)
        {
            $business_id = $request->session()->get('business.id');
            $title = $request->designation;
            $designation =  JobTitle::firstOrCreate( [   
                'business_id' => $business_id,
                'job_title' => $title,
            ]);
            return $designation->id;
                 
        }
        return 0;
    }

    /**
     * Update Staff Member Status
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function updateStatus($id)
    {
        try {
            $staff = MemberStaff::find($id);
            MemberStaff::where('id', $id)->update([
                'status' => ($staff->status=='Enabled')?'Disabled':'Enabled',
            ]);
            $output = [
                'success' => true,
                'msg' => __('member::lang.staff_member_status_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'electrorate',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     *  Assign staff to suggestion 
     *
     * Undocumented function long description
     **/
    public function assignToSuggestion($suggestion_id)
    {
        $staff = MemberStaff::where('status','Enabled')->pluck('name','id');
        $suggestion = Suggestion::find($suggestion_id);
        return view('member::settings.staff.suggestion_assign_box',compact('staff','suggestion'));
    }

    /**
     * Store Staff assign to suggestion
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function storeAssignedStaff(Request $request)
    {
       try {
            $suggestion = Suggestion::find($request->suggestion);
             
            $suggestion->update([
                'assigned_to_member_id' => $request->staff_member ?? null,
            ]);
            
            $output = [
                'success' => true,
                'msg' => __('member::lang.assigned_staff_create_success')
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
    
}
