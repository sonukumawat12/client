<?php

namespace Modules\Essentials\Http\Controllers;

use App\Category;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\HrmDepartment;
use Modules\Essentials\Entities\HrmDesignation;
use Modules\Essentials\Entities\Probation;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;

class EssentialsProbationController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        try {
            $probations = Probation::leftJoin('hrm_departments as dept', 'probations.department_id', '=', 'dept.id')
            ->leftJoin('hrm_designations as desig', 'probations.designation_id', '=', 'desig.id')
            ->select([
                'probations.id as id',
                'probations.date_time',
                DB::raw("CASE 
                            WHEN probations.department_id = 0 THEN 'All Departments' 
                            ELSE COALESCE(dept.name, 'Unknown') 
                         END as department"),
                DB::raw("CASE 
                            WHEN probations.designation_id = 0 THEN 'All Designations' 
                            ELSE COALESCE(desig.name, 'Unknown') 
                         END as designation"),
                'probations.period',
                'probations.status',
                'probations.user_added'
            ])
            ->get();

            return DataTables::of($probations)
                ->addColumn('action', function ($row) {
                    $edit_url = route('probation.edit', [$row->id]);
                    $delete_url = route('probation.destroy', [$row->id]);


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

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('essentials::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        try {

            $date_time = $request->input('date_time');
            $department = $request->input('department');
            $designation = $request->input('designation');
            $user = auth()->user()->username;
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


            \Log::emergency('File:'.$exception->getFile().'Line:'.$exception->getLine().'Message:'.$exception->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
            return redirect()->back()->with(['status' => $output]);
        }
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('essentials::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {

        try {
          if(request()->ajax()){
                $probation = Probation::findOrFail($id);
                $business_id = request()->session()->get('user.business_id');
                $departments = HrmDepartment::where('business_id', $business_id)
                    ->pluck('name','id');

                $designations = HrmDesignation::where('department_id', $probation->department_id)
                    ->pluck('name','id');



                return view('essentials::settings.partials.edit_probation')->with(compact('probation','departments','designations'));
           }
        }catch (\Exception $exception){
            \Log::error('Error deleting department: ' . $exception->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
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
        try {
            // Validate the request data
            $validatedData = $request->validate([
                'date_time' => 'required',
                'period' => 'required',
                'status' => 'required',
            ]);

            // Find the probation record by ID
            $probation = Probation::findOrFail($id);

            if (!$probation) {
                return redirect()->route('probation.index')->with('error', __('Probation record not found.'));
            }

            $validatedData['department_id'] = $request->department;
            $validatedData['designation_id'] = $request->designation;
            // Update the probation record
            $probation->update($validatedData);
            $output = ['success' => 1,
                'msg' => trans('lang_v1.updated_succesfully'),
            ];
            return redirect()->back()->with(['status' => $output]);


        }catch (\Exception $exception){

            \Log::emergency('File:'.$exception->getFile().'Line:'.$exception->getLine().'Message:'.$exception->getMessage());

            $output = ['success' => 0,
                'msg' => trans('messages.something_went_wrong'),
            ];
            return redirect()->back()->with(['status' => $output]);
        }

    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        // Find the probation record by ID
        $probation = Probation::find($id);

        if ($probation) {
            // Delete the record
            $probation->delete();

            // Return success response
            return response()->json([
                'success' => true,
                'msg' => __('Probation record deleted successfully.')
            ]);
        }

        // If record not found, return error response
        return response()->json([
            'success' => false,
            'msg' => __('Probation record not found.')
        ]);
    }

    public function get_probation_duration(Request $request)
    {
        try {
            $probation = Probation::where('probations.department_id', $request->department_id)
            ->where('probations.designation_id', $request->designation_id)
            ->where('status', 1)
            ->orderBy('id', 'desc')
            ->first();
            if(is_null($probation)){
                // Search setting for all designations for the selected department
                $probation = Probation::where('probations.department_id', $request->department_id)
                ->where('probations.designation_id', 0)
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->first();
                if(is_null($probation)){
                    // Search setting for all departments
                    $probation = Probation::where('probations.department_id', 0)
                    ->where('status', 1)
                    ->orderBy('id', 'desc')
                    ->first();
                }
            }
            if(!is_null($probation)){
                return response()->json([
                    'success' => true,
                    'duration' => $probation->period,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'msg' => "Probation setting not found for the selected department and designation."
                ]);
            }

        }catch (\Exception $exception){
            \Log::emergency('File:'.$exception->getFile().'Line:'.$exception->getLine().'Message:'.$exception->getMessage());
            return response()->json([
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ]);
        }
    }
}
