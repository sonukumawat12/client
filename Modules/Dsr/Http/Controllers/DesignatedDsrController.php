<?php

namespace Modules\Dsr\Http\Controllers;

use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Dsr\Entities\Areas;
use Modules\Dsr\Entities\BusinessDsrOfficer;
use Modules\Dsr\Entities\DesignatedDsrOfficer;
use Modules\Dsr\Entities\FuelProvider;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\DataTables;

class DesignatedDsrController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (request()->ajax()) {
            $business_id = request()->session()->get('business.id');
            $desinated_officers = DesignatedDsrOfficer::leftjoin('countries','countries.id','=','designated_dsr_officers.country_id')
                ->leftjoin('provinces','provinces.id','=','designated_dsr_officers.province_id')
                ->select('designated_dsr_officers.*',
                    'designated_dsr_officers.id as report_no',
                    'designated_dsr_officers.created_at as date','countries.country','provinces.name as province')
                ->where('designated_dsr_officers.business_id',$business_id)
                ->where('designated_dsr_officers.state','active');
            // check if request parameter as country_id then filter by country
            if(request()->has('country_id') && request()->country_id != ''){
                $desinated_officers->where('designated_dsr_officers.country_id',request()->country_id);
            }
            $desinated_officers = $desinated_officers->OrderBy('designated_dsr_officers.id','desc');
            return DataTables::of($desinated_officers)
                ->addColumn(
                    'action',
                    '
                    <button data-href="{{action(\'\Modules\Dsr\Http\Controllers\DesignatedDsrController@edit\',[$id])}}" data-container=".dsr_designated_officer_model" class="btn btn-xs btn-primary btn-modal edit_btn"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</button>
                    <button data-href="{{action(\'\Modules\Dsr\Http\Controllers\DesignatedDsrController@destroy\',[$id])}}" data-container=".dsr_officer_del" class="btn btn-xs btn-danger dsr_officer_del"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    <button data-href="{{action(\'\Modules\Dsr\Http\Controllers\DesignatedDsrController@bindBusiness\',[$id])}}" data-container=".dsr_designated_officer_model" class="btn btn-xs  btn-modal    btn-warning dsr_office_bind"><i class="glyphicon glyphicon-arrow-right"></i> @lang("messages.bind")</button>
                  
                    '
                )
                ->addColumn('business', function ($item) {
                    $businesses = BusinessDsrOfficer::where('dsr_officer_id',$item->id)->first();
                    if($businesses){
                        $businesses = json_decode($businesses->business_ids) ?? [];
                        $businesses = DB::table('business')->whereIn('id',$businesses)->pluck('name')->toArray();
                        $businesses = array_map(function($key, $value) {
                            return ($key + 1) . '. ' . $value;
                        }, array_keys($businesses), $businesses);

                        return implode('<br>', $businesses);
                    }
                    return '';
                })
                ->editColumn('date', function ($item) {
                    $date = $item->date;
                    $date = date_create($date);
                    return $date->format('F j, Y g:i A');
                })
                ->rawColumns(['action','date','business'])
                ->make(true);
        }
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        $fuelProviders = FuelProvider::pluck('name', 'id')->toArray();
        $countries = DB::table('countries')->pluck('country', 'id')->toArray();
        $provinces = DB::table('provinces')->pluck('name', 'id')->toArray();
        $districts = DB::table('districts')->pluck('name', 'id')->toArray();
        $allAreas = DB::Table('areas')->pluck('name','id')->toArray();

        return view('dsr::designateDsrOfficer.create',compact('provinces','allAreas','districts','fuelProviders','countries'));
    }
    public function bindBusiness($id) {
        if (request()->isMethod('post')) {
            $data['business_ids'] = json_encode(\request('businesses'));
            $data['dsr_officer_id'] = $id;
            BusinessDsrOfficer::updateOrCreate(['dsr_officer_id'=>$id],$data);
            $output = [
                'success' => true,
                'msg' => __("dsr::lang.dsr_officer_bind_successfully"),
                'reload' => true,
            ];
            return redirect()->back()->with('status', $output);
        }
        $dsr_officer = DesignatedDsrOfficer::findOrFail($id);
        $businesses = BusinessDsrOfficer::where('dsr_officer_id',$id);
        if($businesses->exists()){
            $businesses = $businesses->first();
            $businesses = json_decode($businesses->business_ids);
        }else{
            $businesses = [];
        }
        $allBusinesses = DB::table('business')->pluck('name','id')->toArray();
        $dsr_officer->businesses = $businesses;
        return view('dsr::designateDsrOfficer.bindBusiness',compact('dsr_officer','allBusinesses'));
    }
    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        $business_id = $request->session()->get('business.id');
        $data = $request->except(['_token','_method']);

        $data['business_id'] = $business_id;
        $data['areas'] = json_encode(@$data['areas']);
        
        $data['province_id'] = json_encode(@$data['province_id']);
        $data['district_id'] = json_encode(@$data['district_id']);
        
        
        
        $dsr_officer = DesignatedDsrOfficer::updateOrCreate(['officer_username'=>$data['officer_username']],$data);
        $user = User::updateOrCreate(['username'=>$data['officer_username']],[
            'surname' =>  $dsr_officer->officer_name,
            'username' => $dsr_officer->officer_username,
            'password' => Hash::make($dsr_officer->officer_password),
            'language' => !empty($details['language']) ? $details['language'] : 'en',
            'business_id' => $business_id,
            'created_by'=>auth()->user()->id,
        ]);
        $role = Role::where('name', 'dsr_officer')->first();
        if (empty($role)) {
            $role = Role::create(['name' => 'dsr_officer']);
        }
        $user->assignRole($role);
        $permission = Permission::where('name', 'manage_dsr')->first();
        if (empty($permission)) {
            $permission = Permission::create(['name' => 'manage_dsr']);
        }
        $user->givePermissionTo($permission);

        $output = [
            'success' => true,
            'msg' => __("dsr::lang.dsr_officer_created_successfully"),
            'reload' => true,
        ];
        return redirect()->back()->with('status', $output);

    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {

            $fuelProviders = FuelProvider::pluck('name', 'id')->toArray();
            $countries = DB::table('countries')->pluck('country', 'id')->toArray();
            $provinces = DB::table('provinces')->pluck('name', 'id')->toArray();
            $districts = DB::table('districts')->pluck('name', 'id')->toArray();
            $allAreas = DB::Table('areas')->pluck('name','id')->toArray();
            $dsr_officer = DesignatedDsrOfficer::findOrFail($id);
            $ares_ids = json_decode($dsr_officer->areas);
            if(!empty($ares_ids))
                $dsr_officer->areas = Areas::whereIn('id',$ares_ids)->pluck('name','id')->toArray();
            return view('dsr::designateDsrOfficer.edit',compact('provinces','allAreas','districts','fuelProviders','countries','dsr_officer'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        $data = $request->except(['_token','_method']);
        $data['areas'] = json_encode(@$data['areas']);
        $dsr_officer = DesignatedDsrOfficer::findOrFail($id);
        $dsr_officer->update($data);
        $output = [
            'success' => true,
            'msg' => __("dsr::lang.dsr_officer_update_successfully"),
            'reload' => true,
        ];
        return redirect()->back()->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        $dsr_officer = DesignatedDsrOfficer::findOrFail($id)->update(['state'=>'inactive']);
        $output = [
            'success' => true,
            'msg' => __("dsr::lang.dsr_officer_deleted_successfully"),
            'reload' => true,
        ];
        return redirect()->back()->with('status', $output);
    }
}
