<?php

namespace Modules\Essentials\Http\Controllers;

use App\Category;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Essentials\Entities\HrmDepartment;
use Yajra\DataTables\Facades\DataTables;

class EssentialsDepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (!auth()->user()->can('category.view') && !auth()->user()->can('category.create')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            $business_id = request()->session()->get('user.business_id');


            $departments = HrmDepartment::where('hrm_departments.business_id', $business_id)
                ->leftjoin('users as u','u.id','created_by')
                ->select(['name','short_code','hrm_departments.id','u.username','description']);
            return Datatables::of($departments)
                ->addColumn('action', function ($row) {
                    $edit_url = route('department.edit', [$row->id]);
                    $delete_url = route('department.destroy', [$row->id]);

                    $edit_button = '<button data-href="' . $edit_url . '" class="btn btn-xs btn-primary btn-modal" data-container=".view_modal">
                            <i class="glyphicon glyphicon-edit"></i> ' . __('messages.edit') . '
                        </button>';

                    $delete_button = '<button data-href="' . $delete_url . '" class="btn btn-xs btn-danger delete-button">
                            <i class="glyphicon glyphicon-trash"></i> ' . __('messages.delete') . '
                        </button>';

                    return $edit_button . ' ' . $delete_button;
                })
                ->rawColumns(['action'])
                ->make(true);
        }




        return view('essentials::department.index');
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
            $input = $request->only(['name', 'short_code', 'description']);
            $input['business_id'] = $request->session()->get('user.business_id');



            $department = HrmDepartment::where('business_id',$input['business_id'] )
                            ->where('name',$input['name'])->first();

            if (!empty($department)) {
                $output = [
                    'success' => false,
                    'msg' => __("messages.department_name_exists")
                ];

                // Handle AJAX response
                if ($request->ajax()) {
                    return response()->json($output);
                }

                return redirect()->back()->with('status', $output);
            }

            $input['created_by'] = $request->session()->get('user.id');

            HrmDepartment::create($input);


            $output = [
                'success' => true,
                'msg' => __('lang_v1.added_success'),
            ];
        } catch (\Exception $e) {
            \Log::emergency('File:' . $e->getFile() . ' Line:' . $e->getLine() . ' Message:' . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong'),
            ];
        }

        // Handle AJAX response
        if ($request->ajax()) {
            return response()->json($output);
        }

        // Handle standard form submission
        if ($output['success']) {
            return redirect()->back()->with('status', $output);
        } else {
            return redirect()->back()->withErrors($output['msg']);
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
            if (request()->ajax()) {
                $business_id = request()->session()->get('user.business_id');

                $department = HrmDepartment::where('business_id', $business_id)
                                ->where('id',$id)
                                ->first();

                return view('essentials::department.edit')->with(compact('department'));

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
            $business_id = $request->session()->get('user.business_id');

            $input = $request->only(['name','short_code','description']);

            HrmDepartment::where(['business_id'=>$business_id,'id'=>$id])
                    ->update($input);

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
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {

        try {


            $department = HrmDepartment::findOrFail($id);

            $department->delete();

            return response()->json(['success' => true, 'msg' => __('lang_v1.deleted_success')]);
        } catch (\Exception $e) {

            \Log::error('Error deleting department: ' . $e->getMessage());
            return response()->json(['success' => false, 'msg' => __('messages.something_went_wrong')]);
        }
    }
}
