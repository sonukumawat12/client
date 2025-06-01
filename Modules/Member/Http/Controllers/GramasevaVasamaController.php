<?php

namespace Modules\Member\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\District;
use Modules\Member\Entities\Electrorate;
use Modules\Member\Entities\GramasevaVasama;
use Modules\Member\Entities\Province;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class GramasevaVasamaController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $gramaseva_vasama = GramasevaVasama::where('gramaseva_vasamas.business_id', $business_id)
               
                ->leftjoin('electrorates', 'electrorates.id', 'gramaseva_vasamas.electrorate_id')
                ->leftjoin('districts', 'districts.id', 'electrorates.district_id')
                ->leftjoin('provinces', 'provinces.id', 'gramaseva_vasamas.province_id')
                ->leftjoin('users','gramaseva_vasamas.created_by', 'users.id')
                
                ->select([
                    'gramaseva_vasamas.*','electrorates.name as electrorate_name','provinces.name as province_name','districts.name as district_name','users.username as username'
                ]);
                if (!empty(request()->district)) {
                    $gramaseva_vasama->where('electrorates.district_id', request()->district);
                }
                if (!empty(request()->province)) {
                    $gramaseva_vasama->where('gramaseva_vasamas.province_id', request()->province);
                }
                if (!empty(request()->electrorate)) {
                    $gramaseva_vasama->where('gramaseva_vasamas.electrorate_id', request()->electrorate);
                }
            return DataTables::of($gramaseva_vasama)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\GramasevaVasamaController@edit\',[$id])}}" data-container=".gramaseva_vasama_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\GramasevaVasamaController@destroy\',[$id])}}" class="btn btn-xs btn-danger gramaseva_vasama_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>

                    '
                )
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
        $business_id = request()->session()->get('business.id');
       
        $electrorates = Electrorate::where('business_id',$business_id)->pluck('name','id');
        $provinces = Province::where('business_id',$business_id)->pluck('name','id');
        $districts = District::where('business_id',$business_id)->pluck('name','id');
        $gramasevavasamas = GramasevaVasama::where('business_id',$business_id)->pluck('gramaseva_vasama','id');  
        return view('member::settings.gramaseva_vasama.create',compact('electrorates','provinces','districts', 'gramasevavasamas'));
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
         
              $exists = GramasevaVasama::where('electrorate_id', $data['electrorate_id'])
                  ->where('gramaseva_vasama', $data['gramaseva_vasama'])
                  ->exists();
              
          if (!$exists) {
            $data['business_id'] = $business_id;
            $data['district_id'] = $data['district_gram'];
            $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d');
            $data['created_by'] = auth()->user()->id;
            $gramaseva_vasama = GramasevaVasama::create($data);

            //Create a new permission related to the created gramaseva_vasama
            Permission::create(['name' => 'gramaseva_vasama.' . $gramaseva_vasama->id]);

            $output = [
                'success' => true,
                'tab' => 'gramaseva_vasama',
                'msg' => __('member::lang.gramaseva_vasama_create_success')
            ];
          } else {
               $output = [
                'success' => false,
                'tab' => 'gramaseva_vasama',
               'msg' => __('member::lang.gramaseva_vasama_already_exists', ['gramaseva_vasama' => $data['gramaseva_vasama']]) 
            ];
            }
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'gramaseva_vasama',
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
    public function edit($id)
    {
        
        $electrorates = Electrorate::pluck('name','id');
       
        $provinces = Province::has('electrorate')->pluck('name','id');
        $gramaseva_vasama = GramasevaVasama::with('electrorate.district')->findOrFail($id);
        
        return view('member::settings.gramaseva_vasama.edit')->with(compact('gramaseva_vasama','electrorates','provinces'));
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
       
        // Format the date or use the current date
        $data['date'] = !empty($data['date']) ? Carbon::parse($data['date'])->format('Y-m-d') : date('Y-m-d');
        
        // Check if the Gramaseva Vasama with the same electrorate_id and gramaseva_vasama already exists (excluding the current record)
        $exists = GramasevaVasama::where('electrorate_id', $data['electrorate_id'])
              ->where('gramaseva_vasama', $data['gramaseva_vasama'])
              ->where('id', '!=', $id) // Exclude the current gramaseva vasama being updated
              ->exists();

        if (!$exists) {
            // Prepare the update data
            $update_data = [
                'gramaseva_vasama' => $data['gramaseva_vasama'],
                'electrorate_id' => $data['electrorate_id'],
                'date' => $data['date'],
            ];

            // Update the Gramaseva Vasama record
            GramasevaVasama::where('id', $id)->update($update_data);

            $output = [
                'success' => true,
                'tab' => 'gramaseva_vasama',
                'msg' => __('member::lang.gramaseva_vasama_update_success')
            ];
        } else {
            // Return the specific message if the Gramaseva Vasama already exists
            $output = [
                'success' => false,
                'tab' => 'gramaseva_vasama',
                'msg' => __('member::lang.gramaseva_vasama_already_exists', ['name' => $data['gramaseva_vasama']]) // Add this to localization
            ];
        }
    } catch (\Exception $e) {
        Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() .' Message: ' . $e->getMessage());
        $output = [
            'success' => false,
            'tab' => 'gramaseva_vasama',
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
           
            GramasevaVasama::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('member::lang.gramaseva_vasama_delete_success')
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
    
    public function getGsvasamas($electrorateId)
   {
    $gsvasamas = GramasevaVasama::where('electrorate_id', $electrorateId)->pluck('gramaseva_vasama', 'id');
    return response()->json($gsvasamas);
  }
    
    
}
