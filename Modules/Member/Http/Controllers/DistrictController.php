<?php

namespace Modules\Member\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\District;
use Modules\Member\Entities\Province;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

/**
 * undocumented class
 */
class DistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        $business_id = request()->session()->get('business.id');
        if (request()->ajax()) {
            $provinces = District::where('provinces.business_id', $business_id)
                ->leftjoin('provinces', 'provinces.id', 'districts.province_id')
                ->leftjoin('users', 'users.id', 'districts.created_by')
                ->select([
                    'districts.*','provinces.name as province_name','users.username as username'
                ]);

            return DataTables::of($provinces)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\DistrictController@edit\',[$id])}}" data-container=".districts_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Member\Http\Controllers\DistrictController@destroy\',[$id])}}" class="btn btn-xs btn-danger district_delete"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                   
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
       
        $provinces = Province::where('business_id', $business_id)->pluck('name','id');
        $districts = District::where('business_id', $business_id)->pluck('name','id');
        return view('member::settings.district.create',compact('provinces', 'districts'));
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
            $province = Province::find($data['province']);
               $exists = District::where('province_id', $data['province'])
                  ->where('name', $data['name'])
                  ->exists();
          if (!$exists) {
            $save_data = [
                'province_id' => $data['province'],
                'name' => $data['name'],
                'created_by'=> auth()->user()->id,
                'business_id' => $business_id,
                'country_id' => $province->country_id,
                'date' => date('Y-m-d'),
           
            ];
            District::create($save_data);
     $output = [
                'success' => true,
                'tab' => 'district',
                'msg' => __('member::lang.district_create_success')
            ];
            } else {
               $output = [
                'success' => false,
                'tab' => 'district',
               'msg' => __('member::lang.district_already_exists', ['name' => $data['name']]) 
            ];
            }
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'district',
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
        $provinces = Province::where('business_id', $business_id)->pluck('name','id');
        $district = District::findOrFail($id);
        // dd($id);
        return view('member::settings.district.edit')->with(compact('district','provinces'));
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
        
        // Check for duplicate district name within the same province
        $exists = District::where('name', $data['name'])
            ->where('province_id', $data['province'])
            ->where('id', '!=', $id) // Exclude the current district
            ->exists();
        
        if ($exists) {
            // District already exists
            $output = [
                'success' => false,
                'tab' => 'district',
                'msg' => __('member::lang.district_already_exists')
            ];
        } else {
            // Prepare the update data
            $update_data = [
                'province_id' => $data['province'],
                'name' => $data['name'],
            ];

            // Perform the update
            District::where('id', $id)->update($update_data);

            $output = [
                'success' => true,
                'tab' => 'district',
                'msg' => __('member::lang.district_update_success')
            ];
        }
    } catch (\Exception $e) {
        Log::emergency('File: ' . $e->getFile() . ' Line: ' . $e->getLine() . ' Message: ' . $e->getMessage());
        $output = [
            'success' => false,
            'tab' => 'district',
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
            District::where('id', $id)->delete();
            $output = [
                'success' => true,
                'tab' => 'balamandalaya',
                'msg' => __('member::lang.district_delete_success')
            ];
        } catch (\Exception $e) {
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'tab' => 'balamandalaya',
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    /**
     * Get District Province
     *
     * Undocumented function long description
     *
     **/
    public function get(Request $request)
    {
        $district = District::find($request->id);
        return response()->json(
            [
                'province'=> $district->province_id,
                'country' => $district->country_id,
                'name' => $district->name,
            ]
            );
    }
    
    /**  GET DISTRICT BASED ON PROVINCE */
    public function getDistricts($provinceId)
    {
     $districts = District::where('province_id', $provinceId)->pluck('name', 'id');
     return response()->json($districts);
   }
    
}
