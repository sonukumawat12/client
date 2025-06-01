<?php

namespace Modules\Member\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\District;
use Modules\Member\Entities\Province;
use Modules\Member\Entities\Electrorate;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

/**
 * undocumented class
 */
class ElectrorateController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $items = Electrorate::where('electrorates.business_id', $business_id)
            
                ->leftjoin('districts', 'districts.id', 'electrorates.district_id')
                ->leftjoin('provinces', 'provinces.id', 'electrorates.province_id')
                ->leftjoin('users', 'users.id', 'electrorates.created_by')
                
                ->select([
                    'electrorates.*','provinces.name as province_name','districts.name as district_name','users.username as username'
                ]);
                if (!empty(request()->district_id)) {
                    $items->where('electrorates.district_id', request()->district_id);
                }

            return DataTables::of($items)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\ElectrorateController@edit\',[$id])}}" data-container=".electrorate_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\ElectrorateController@destroy\',[$id])}}" class="btn btn-xs btn-danger electrorate_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
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
        $districts = District::where('business_id', $business_id)->pluck('name','id');
        $provinces = Province::where('business_id', $business_id)->pluck('name','id');
        $electrorates = Electrorate::where('business_id', $business_id)->pluck('name','id');
        return view('member::settings.electrorate.create',compact('provinces','districts', 'electrorates'));
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
            $exists = Electrorate::where('province_id', $data['province'])
                  ->where('name', $data['name'])
                  ->exists();
        if (!$exists) {
            $save_data =
            [   
                'business_id' => $business_id,
                'name' => $data['name'],
                'district_id' => $data['district'],
                'province_id' => $data['province'],
                'created_by' => auth()->user()->id,
            ];
            Electrorate::create($save_data);

            //Create a new permission related to the created gramaseva_vasama
            // Permission::create(['name' => 'gramaseva_vasama.' . $gramaseva_vasama->id]);

            $output = [
                'success' => true,
                'tab' => 'electrorate',
                'msg' => __('member::lang.electrorate_create_success')
            ];
        } else {
               $output = [
                'success' => false,
                'tab' => 'electrorate',
                'msg' => __('member::lang.electrorate_already_exists', ['name' => $data['name']])
            ];
            }
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'electrorate',
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
        $business_id = $request->session()->get('business.id');
        $districts = District::where('business_id', $business_id)->pluck('name','id');
        $provinces = Province::where('business_id', $business_id)->pluck('name','id');
        $item = Electrorate::findOrFail($id);
        return view('member::settings.electrorate.edit')->with(compact('item','provinces','districts'));
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
        
        // Check if an electrorate with the same province_id, district_id, and name already exists (excluding the current electrorate)
        $exists = Electrorate::
              
              where('name', $data['name'])
              ->where('id', '!=', $id) // Exclude the current electrorate being updated
              ->exists();

        if (!$exists) {
            // Prepare the update data
            $update_data = [
                
                'name' => $data['name'],
            ];

            // Update the electrorate record
            Electrorate::where('id', $id)->update($update_data);

            $output = [
                'success' => true,
                'tab' => 'electrorate',
                'msg' => __('member::lang.electrorate_update_success')
            ];
        } else {
            // Return the specific message if the electrorate already exists
            $output = [
                'success' => false,
                'tab' => 'electrorate',
                'msg' => __('member::lang.electrorate_already_exists', ['name' => $data['name']]) // Add this to localization
            ];
        }
    } catch (\Exception $e) {
        Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
        $output = [
            'success' => false,
            'tab' => 'electrorate',
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
           
            Electrorate::where('id', $id)->delete();
            $output = [
                'success' => true,
                'tab' => 'electrorate',
                'msg' => __('member::lang.electrorate_delete_success')
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
     * Get Electrorate Details
     *
     * Undocumented function long description
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function get(Request $request)
    {
        $electrorate = Electrorate::with('district')->find($request->id);
        return response()->json([
            'name'=>$electrorate->name,'district_id'=>$electrorate->district_id,'district'=>$electrorate->district->name,'province_id'=>$electrorate->province_id
        ]);
    }
    
    public function getElectrorates($districtId)
    {
     $elctrorates = Electrorate::where('district_id', $districtId)->pluck('name', 'id');
     return response()->json($elctrorates);
   }
}
