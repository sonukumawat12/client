<?php

namespace Modules\Member\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\Country;
use Modules\Member\Entities\Province;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

/**
 * undocumented class
 */
class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $provinces = Province::where('provinces.business_id', $business_id)
                ->leftjoin('countries', 'countries.id', 'provinces.country_id')
                ->leftjoin('users', 'users.id', 'provinces.created_by')
                ->select([
                    'provinces.*','countries.country as country','users.username as user_name'
                ]);

            return DataTables::of($provinces)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\ProvinceController@edit\',[$id])}}" data-container=".provinces_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\ProvinceController@destroy\',[$id])}}" class="btn btn-xs btn-danger province_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
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
        
        $countries = Country::pluck('country','id');
        $provinces = Province::where('business_id', $business_id)->pluck('name','id');
        return view('member::settings.province.create',compact('provinces','countries'));
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
            $exists = Province::where('country_id', $data['country'])
                  ->where('name', $data['name'])
                  ->exists();

            if (!$exists) {
                $save_data = [
                    'country_id' => $data['country'],
                    'name' => $data['name'],
                    'created_by' => auth()->user()->id,
                    'business_id' => $business_id,
                ];
            
                // Create the Province
                Province::create($save_data);
                 $output = [
                'success' => true,
                'msg' => __('member::lang.province_create_success')
            ];
            } else {
               $output = [
                'success' => false,
                'msg' => __('member::lang.province_already_exists', ['name' => $data['name']])
            ];
            }
            //Create a new permission related to the created gramaseva_vasama
            // Permission::create(['name' => 'gramaseva_vasama.' . $gramaseva_vasama->id]);

           
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
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
        $countries = Country::pluck('country','id');
        
        $province = Province::findOrFail($id);
        return view('member::settings.province.edit')->with(compact('countries','province'));
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
        
        // Check if the province with the same name and country already exists (excluding the current province)
        $exists = Province::where('country_id', $data['country'])
              ->where('name', $data['name'])
              ->where('id', '!=', $id) // Exclude the current province being updated
              ->exists();

        if (!$exists) {
            // Prepare the update data
            $update_data = [
                'country_id' => $data['country'],
                'name' => $data['name'],
            ];

            // Update the province record
            Province::where('id', $id)->update($update_data);

            $output = [
                'success' => true,
                'msg' => __('member::lang.province_update_success')
            ];
        } else {
            // Return the specific message if the province already exists
            $output = [
                'success' => false,
                'msg' => __('member::lang.province_already_exists', ['name' => $data['name']]) // Add this to localization
            ];
        }
    } catch (\Exception $e) {
        Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
        $output = [
            'success' => false,
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
            Province::where('id', $id)->delete();
            $output = [
                'success' => true,
                'msg' => __('member::lang.province_delete_success')
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
    
    public function getProvinces($countryId)
   {
    $provinces = Province::where('country_id', $countryId)->pluck('name', 'id');
    return response()->json($provinces);
  }
}
