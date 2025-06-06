<?php

namespace App\Http\Controllers;

use App\User;
use App\Store;
use App\System;
use App\Contact;

use App\Business;
use App\UserSetting;
use GuzzleHttp\Client;
use App\BusinessLocation;
use App\Utils\ModuleUtil;

use App\VerificationCode;
use App\Utils\BusinessUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Modules\Member\Entities\MemberGroup;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use Modules\Member\Entities\Balamandalaya;
use Modules\Member\Entities\GramasevaVasama;
use Modules\Essentials\Entities\EssentialsEmployee;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ManageUserController extends Controller
{

    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $moduleUtil;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(ModuleUtil $moduleUtil,BusinessUtil $businessUtil)
    {
        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
     
     
    public function face(){

 // Path to the Python script located in the public directory
    $pythonScript = public_path('face.py');

    // Image file located in the public directory
    $imageFile = public_path('muazzamazaz.jpg');

    // Creating a new process to run the Python script
    $process = new Process(['python3', $pythonScript, $imageFile]);    $process->run();
    
    if (!$process->isSuccessful()) {
        throw new ProcessFailedException($process);
    }
    
    echo $process->getOutput();
    
    }
    
    public function index()
    {
        if (!auth()->user()->can('user.view') && !auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $user_id = request()->session()->get('user.id');

            $users = User::with('setting')->where('business_id', $business_id)
                ->where('id', '!=', $user_id)
                ->where('is_cmmsn_agnt', 0)
                ->where('is_customer', 0)
                ->select([
                    'id', 'username', 'designation' ,'language','pump_operator_pass_changed',
                    DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"), 'email', 'pump_operator_passcode'
                ]);
            return Datatables::of($users)
                ->addColumn(
                    'role',
                    function ($row) {
                        $role_name = $this->moduleUtil->getUserRoleName($row->id);
                        return $role_name;
                    }
                )
                ->addColumn(
                    'action',
                    '@can("user.update")
                        <a href="{{action(\'ManageUserController@edit\', [$id])}}" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> @lang("messages.edit")</a>
                        &nbsp;
                    @endcan
                    @can("user.view")
                    <a href="{{action(\'ManageUserController@show\', [$id])}}" class="btn btn-xs btn-info"><i class="fa fa-eye"></i> @lang("messages.view")</a>
                    &nbsp;
                    @endcan
                    @can("user.delete")
                        <button data-href="{{action(\'ManageUserController@destroy\', [$id])}}" class="btn btn-xs btn-danger delete_user_button"><i class="glyphicon glyphicon-trash"></i> @lang("messages.delete")</button>
                    @endcan'
                )
                ->editColumn('pump_operator_passcode', function($row){
                    if($row->pump_operator_pass_changed == 1){
                        return __('petro::lang.changed_by_user');
                    }else{
                        return $row->pump_operator_passcode;
                    }
                })
                
                ->editColumn('language', function($row){
                    if(!empty($row->language)){
                        $languages = (array) config('constants.langs');
                        return $languages[$row->language]['full_name'];
                    }
                })
                ->addColumn(
                    'reCAPTCHA',
                    function ($row){
                    return ($row->setting && !$row->setting->re_captcha_enabled)?
                    '<button data-href="'.action('ManageUserController@changeReCAPTCHAStatus', [$row->id]).'" class="btn btn-xs btn-danger change_recaptcha_user_button"><i class="glyphicon glyphicon-refresh"></i> '.__("business.disable").' </button>'
                    :'<button data-href="'.action('ManageUserController@changeReCAPTCHAStatus', [$row->id]).'" class="btn btn-xs btn-primary change_recaptcha_user_button"><i class="glyphicon glyphicon-refresh"></i> '.__("business.enable").' </button>';
                    }
                 )
                
                ->filterColumn('full_name', function ($query, $keyword) {
                    $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
                })
                ->removeColumn('id')
                ->rawColumns(['action','reCAPTCHA'])
                ->make(true);
        }

        $petro_module = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'enable_petro_module');

        return view('manage_user.index')->with(compact('petro_module'));
    }


    public function list()
    { 
        if (!auth()->user()->can('user.create') && !auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = request()->session()->get('user.business_id');
        if (request()->ajax()) {
            $user_id = request()->session()->get('user.id');
/*
            $users = User::with(['business.business_locations'])->where('business_locations.business_id', $business_id)
                ->where('id', '!=', $user_id)
                ->where('is_cmmsn_agnt', 0)
                ->where('is_customer', 0)
                ->select([
                    'id', 'username', 'designation','country', 'language','pump_operator_pass_changed',
                    DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"), 'email', 'pump_operator_passcode'
                ]);
  */              
            $users = User::join('business', 'users.business_id', '=', 'business.id')
                    ->join('business_locations', 'business.id', '=', 'business_locations.business_id')
                    ->select('users.id','users.username', 'business.name as business_name', 'business_locations.name as address', 'business_locations.country', 'business_locations.state', 'business_locations.district', 'business_locations.city')
                    ->where('users.id', '!=', $user_id);
                   
                   
                    return Datatables::of($users)
                    ->filterColumn('business_name', function ($query, $keyword) {
                          $query->where('business.name', 'like', "%{$keyword}%");
                     })
                   ->filterColumn('username', function ($query, $keyword) {
            // Filtering based on the username
            $query->where('users.username', 'like', "%{$keyword}%");
        })
        ->filterColumn('address', function ($query, $keyword) {
            // Filtering based on address
            $query->where('business_locations.name', 'like', "%{$keyword}%");
        })
        ->filterColumn('country', function ($query, $keyword) {
            // Filtering based on country
            $query->where('business_locations.country', 'like', "%{$keyword}%");
        })
        ->filterColumn('state', function ($query, $keyword) {
            // Filtering based on state
            $query->where('business_locations.state', 'like', "%{$keyword}%");
        })
        ->filterColumn('district', function ($query, $keyword) {
            // Filtering based on district
            $query->where('business_locations.district', 'like', "%{$keyword}%");
        })
        ->filterColumn('city', function ($query, $keyword) {
            // Filtering based on city
            $query->where('business_locations.city', 'like', "%{$keyword}%");
        })
          
                ->removeColumn('id')
              //  ->rawColumns(['action','username'])
                ->make(true);
     /*           
     return DataTables::of($users)
           ->editColumn('username', function($row){
                    if(!empty($row->username)){
                        return $row->username;
                    }
                })
            ->rawColumns(['username', 'action'])
            ->make(true);
            */
        }
       
        return view('manage_user.list');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if (!auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Check if subscribed or not, then check for users quota
        if (!$this->moduleUtil->isSubscribed($business_id)) {
            return $this->moduleUtil->expiredResponse();
        } elseif (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
            return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action('ManageUserController@index'));
        }

        $roles  = $this->getRolesArray($business_id);
        $username_ext = $this->getUsernameExtension();
        $contacts = Contact::contactDropdown($business_id, true, false);
        $locations = BusinessLocation::where('business_id', $business_id)
            ->Active()
            ->get();

        $store = Store::where('business_id', $business_id)->pluck('name', 'id');
        $gramasevaka_areas = GramasevaVasama::all();
        $bala_mandalaya_areas = Balamandalaya::all();
        $member_groups = MemberGroup::all();
        $member_module_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'member_registration');
        $property_module_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'property_module');
        $employees = EssentialsEmployee::where('business_id', $business_id)
            ->select('id', 'name', 'dob', 'address')
            ->get();
        return view('manage_user.create')
            ->with(compact('roles', 'username_ext', 'contacts', 'locations', 'store', 'member_module_permission', 'gramasevaka_areas', 'bala_mandalaya_areas', 'member_groups', 'employees','property_module_permission'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('user.create')) {
            abort(403, 'Unauthorized action.');
        }
        $business_id = $request->session()->get('user.business_id');
        $member_module_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'member_registration');
        $property_module_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'property_module');
        try {
            $user_details = $request->only([
                'surname', 'first_name', 'last_name', 'designation', 'username', 'email', 'password', 'selected_contacts', 'marital_status',
                'blood_group', 'contact_number', 'fb_link', 'twitter_link', 'social_media_1',
                'social_media_2', 'permanent_address', 'current_address',
                'guardian_name', 'custom_field_1', 'custom_field_2', 'language',
                'custom_field_3', 'custom_field_4', 'id_proof_name', 'id_proof_number', 'cmmsn_percent', 'max_sales_discount_percent', 'employee_id','is_property_user'
            ]);
            
            $user_details['status'] = !empty($request->input('is_active')) ? 'active' : 'inactive';

            if (!isset($user_details['selected_contacts'])) {
                $user_details['selected_contacts'] = false;
            }

            if (!empty($request->input('dob'))) {
                $user_details['dob'] = $this->moduleUtil->uf_date($request->input('dob'));
            }

            if($request->mobile['number']){
                $user_details['contact_number'] = cleanPhoneNumber($request->mobile['number']);
                $user_details['contact_number_code'] = $request->mobile['code'];
            }

            if (!empty($request->input('bank_details'))) {
                $user_details['bank_details'] = json_encode($request->input('bank_details'));
            }

            $user_details['business_id'] = $business_id;
            $user_details['password'] = Hash::make($user_details['password']);


            $ref_count = $this->moduleUtil->setAndGetReferenceCount('username');
            if (blank($user_details['username'])) {
                $user_details['username'] = $this->moduleUtil->generateReferenceNumber('username', $ref_count);
            }

            $username_ext = $this->getUsernameExtension();
            if (!empty($username_ext)) {
                $user_details['username'] .= $username_ext;
            }

            //Check if subscribed or not, then check for users quota
            if (!$this->moduleUtil->isSubscribed($business_id)) {
                return $this->moduleUtil->expiredResponse();
            } elseif (!$this->moduleUtil->isQuotaAvailable('users', $business_id)) {
                return $this->moduleUtil->quotaExpiredResponse('users', $business_id, action('ManageUserController@index'));
            }

            //Sales commission percentage
            $user_details['cmmsn_percent'] = !empty($user_details['cmmsn_percent']) ? $this->moduleUtil->num_uf($user_details['cmmsn_percent']) : 0;
            $user_details['user_store'] = !empty($request->input('user_store')) ? json_encode($request->input('user_store')) : null;
            $user_details['max_sales_discount_percent'] = !is_null($user_details['max_sales_discount_percent']) ? $this->moduleUtil->num_uf($user_details['max_sales_discount_percent']) : null;

            if($request->has('profile_photo')) {
                $user_details['profile_photo'] = $user_details['username'] . '.' . $request->profile_photo->getClientOriginalExtension();
                $request->profile_photo->storeAs('user-profile-photos/'.$user_details['profile_photo']);
            }

            DB::beginTransaction();
            //Create the user
            $user = User::create($user_details);

            $role_id = $request->input('role');
            $role = Role::findOrFail($role_id);
            $user->assignRole($role->name);

            //Assign selected contacts
            if ($user_details['selected_contacts'] == 1) {
                $contact_ids = $request->get('selected_contact_ids');
                $user->contactAccess()->sync($contact_ids);
            }

            if (!empty($request->is_property_user)) {
                $validator = Validator::make($request->all(), [
                    'password' => 'required|unique:users,property_user_passcode',
                ]);
                if ($validator->fails()) {
                    $output = [
                        'success' => 0,
                        'msg' => $validator->errors()->all()[0]
                    ];

                    return redirect()->back()->with('status', $output);
                }
                $user->is_property_user = 1;
                $user->property_user_passcode = $request->password;
                $user->save();
            }else{
                $user->is_property_user = 0;
                $user->save();
            }
            
            if ($member_module_permission) {
                //Grant permissions
                $this->givePermissions($user, $request, 'bala_mandalaya_permissions');
                $this->givePermissions($user, $request, 'gramaseva_vasama_permissions');
                $this->givePermissions($user, $request, 'member_group_permissions');
            } else {
                //Grant Location permissions
                $this->giveLocationPermissions($user, $request);
            }
            DB::commit();
            $output = [
                'success' => 1,
                'msg' => __("user.user_added")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect('users')->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    { 
        if (!auth()->user()->can('user.view')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $user = User::where('business_id', $business_id)
            ->with(['contactAccess'])
            ->find($id);

        return view('manage_user.show')->with(compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $user = User::where('business_id', $business_id)
            ->with(['contactAccess'])
            ->findOrFail($id);
       $roles = $this->getRolesArray($business_id);

        $contact_access = $user->contactAccess->pluck('id')->toArray();
        $contacts = Contact::contactDropdown($business_id, true, false);

        if ($user->status == 'active') {
            $is_checked_checkbox = true;
        } else {
            $is_checked_checkbox = false;
        }

        $locations = BusinessLocation::where('business_id', $business_id)
            ->get();

        $permitted_locations = $user->permitted_locations();

        $store = Store::where('business_id', $business_id)->pluck('name', 'id');
        $gramasevaka_areas = GramasevaVasama::all();
        $bala_mandalaya_areas = Balamandalaya::all();
        $member_groups = MemberGroup::all();
        $employees = EssentialsEmployee::where('business_id', $business_id)
            ->select('id', 'name', 'dob', 'address')
            ->get();
        $member_module_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'member_registration');
        $property_module_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'property_module');

        return view('manage_user.edit')
            ->with(compact('store', 'roles', 'user', 'contact_access', 'contacts', 'is_checked_checkbox', 'locations', 'permitted_locations', 'member_module_permission', 'gramasevaka_areas', 'bala_mandalaya_areas', 'member_groups', 'employees','property_module_permission'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('user.update')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');
        $member_module_permission = $this->moduleUtil->hasThePermissionInSubscription($business_id, 'member_registration');

        try {
            $user_data = $request->only([
                'surname', 'first_name', 'last_name', 'designation' , 'email', 'selected_contacts', 'marital_status',
                'blood_group', 'contact_number', 'fb_link', 'twitter_link', 'social_media_1',
                'social_media_2', 'permanent_address', 'current_address',
                'guardian_name', 'custom_field_1', 'custom_field_2', 'language',
                'custom_field_3', 'custom_field_4', 'id_proof_name', 'id_proof_number', 'cmmsn_percent', 'max_sales_discount_percent', 'employee_id','is_property_user'
            ]);
            $user_data['status'] = !empty($request->input('is_active')) ? 'active' : 'inactive';
            $business_id = request()->session()->get('user.business_id');

            if (!isset($user_data['selected_contacts'])) {
                $user_data['selected_contacts'] = 0;
            }

            if (!empty($request->input('password'))) {
                $user_data['password'] = Hash::make($request->input('password'));
            }

            //Sales commission percentage
            $user_data['cmmsn_percent'] = !empty($user_data['cmmsn_percent']) ? $this->moduleUtil->num_uf($user_data['cmmsn_percent']) : 0;

            if (!empty($request->input('dob'))) {
                $user_data['dob'] = $this->moduleUtil->uf_date($request->input('dob'));
            }

            if (!empty($request->input('bank_details'))) {
                $user_data['bank_details'] = json_encode($request->input('bank_details'));
            }
            if($request->mobile['number']){
                $user_data['contact_number'] = cleanPhoneNumber($request->mobile['number']);
                $user_data['contact_number_code'] = $request->mobile['code'];
            }

            $user = User::where('business_id', $business_id)
                ->findOrFail($id);
            $user_data['max_sales_discount_percent'] = !is_null($user_data['max_sales_discount_percent']) ? $this->moduleUtil->num_uf($user_data['max_sales_discount_percent']) : null;
            $user_data['user_store'] = !empty($request->input('user_store')) ? json_encode($request->input('user_store')) : null;
            
            if($request->has('profile_photo')) {
                $user_data['profile_photo'] = $user->username . '.' . $request->profile_photo->getClientOriginalExtension();
                $request->profile_photo->storeAs('user-profile-photos/'.$user_data['profile_photo']);
            }

            $user->update($user_data);

            $role_id = $request->input('role');
            $user_role = $user->roles->first();

            if ($user_role->id != $role_id) {
                $user->removeRole($user_role->name);

                $role = Role::findOrFail($role_id);
                $user->assignRole($role->name);
            }

            //Assign selected contacts
            if ($user_data['selected_contacts'] == 1) {
                $contact_ids = $request->get('selected_contact_ids');
            } else {
                $contact_ids = [];
            }
            $user->contactAccess()->sync($contact_ids);
            
            if (!empty($request->is_property_user)) {
                
                $user->is_property_user = 1;
                
                if(!empty($request->password)){
                    $user->property_user_passcode = $request->password;   
                }
                
                $user->save();
            }else{
                $user->is_property_user = 0;
                $user->save();
            }


            if ($member_module_permission) {
                //Grant permissions
                $this->givePermissions($user, $request, 'bala_mandalaya_permissions');
                $this->givePermissions($user, $request, 'gramaseva_vasama_permissions');
                $this->givePermissions($user, $request, 'member_group_permissions');
            } else {
                //Grant Location permissions
                $this->giveLocationPermissions($user, $request);
            }

            $output = [
                'success' => 1,
                'msg' => __("user.user_update_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => 0,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect('users')->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('user.delete')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                User::where('business_id', $business_id)
                    ->where('id', $id)->delete();

                $output = [
                    'success' => true,
                    'msg' => __("user.user_delete_success")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    private function getUsernameExtension()
    {
        $extension = !empty(System::getProperty('enable_business_based_username')) ? '-' . str_pad(session()->get('business.id'), 2, 0, STR_PAD_LEFT) : null;
        return $extension;
    }

    /**
     * Retrives roles array (Hides admin role from non admin users)
     *
     * @param  int  $business_id
     * @return array $roles
     */
    private function getRolesArray($business_id)
    {
        $roles_array = Role::where('business_id', $business_id)->get()->pluck('name', 'id');
        $roles = [];

        $is_admin = $this->moduleUtil->is_admin(auth()->user(), $business_id);

        foreach ($roles_array as $key => $value) {
            if (!$is_admin && $value == 'Admin#' . $business_id) {
                continue;
            }
            $roles[$key] = str_replace('#' . $business_id, '', $value);
        }
        return $roles;
    }

    /**
     * Adds or updates permissions of a user
     */
    private function givePermissions($user, $request, $type)
    {
        $permissions = !empty($request->input($type)) ? $request->input($type) : [];

        $assigned_permissions = [];
        $all_permissions = [];
        if ($type == 'bala_mandalaya_permissions') {
            $balas = Balamandalaya::all();
            foreach ($balas as $bala) {
                $user->revokePermissionTo('balamandalaya.' . $bala->id);
                $all_permissions[] = 'balamandalaya.' . $bala->id;
            }
            foreach ($permissions as $permission) {
                if (in_array($permission,  $all_permissions)) {
                    $assigned_permissions[] = $permission;
                }
            }
        }
        if ($type == 'gramaseva_vasama_permissions') {
            $gras = GramasevaVasama::all();
            foreach ($gras as $gra) {
                $user->revokePermissionTo('gramaseva_vasama.' . $gra->id);
                $all_permissions[] = 'gramaseva_vasama.' . $gra->id;
            }
            foreach ($permissions as $permission) {
                if (in_array($permission,  $all_permissions)) {
                    $assigned_permissions[] = $permission;
                }
            }
        }
        if ($type == 'member_group_permissions') {
            $members = MemberGroup::all();
            foreach ($members as $mem) {
                $user->revokePermissionTo('member_group.' . $mem->id);
                $all_permissions[] = 'member_group.' . $mem->id;
            }
            foreach ($permissions as $permission) {
                if (in_array($permission,  $all_permissions)) {
                    $assigned_permissions[] = $permission;
                }
            }
        }

        if (!empty($assigned_permissions)) {
            $user->givePermissionTo($assigned_permissions);
        }
    }

    /**
     * Adds or updates location permissions of a user
     */
    private function giveLocationPermissions($user, $request)
    {
        $permitted_locations = $user->permitted_locations();
        $permissions = $request->input('access_all_locations');
        $revoked_permissions = [];
        //If not access all location then revoke permission
        if ($permitted_locations == 'all' && $permissions != 'access_all_locations') {
            $user->revokePermissionTo('access_all_locations');
        }

        //Include location permissions
        $location_permissions = $request->input('location_permissions');
        if (
            empty($permissions) &&
            !empty($location_permissions)
        ) {
            $permissions = [];
            foreach ($location_permissions as $location_permission) {
                $permissions[] = $location_permission;
            }

            if (is_array($permitted_locations)) {
                foreach ($permitted_locations as $key => $value) {
                    if (!in_array('location.' . $value, $permissions)) {
                        $revoked_permissions[] = 'location.' . $value;
                    }
                }
            }
        }

        if (!empty($revoked_permissions)) {
            $user->revokePermissionTo($revoked_permissions);
        }
        if (!empty($permissions)) {
            $user->givePermissionTo($permissions);
        }
    }

    public function lockScreen()
    {
        User::where('id', Auth::user()->id)->update(['lock_screen' => 1]);
        $output = [
            'success' => 1,
            'msg' => __('lang_v1.success')
        ];
        return $output;
    }

    public function checkUserPassword(Request $request)
    {
        $password = $request->password;
        $username = auth()->user()->username;
        $credentials = ['username' => $username, 'password' => $password];
        if (empty($username)) {
            $output = [
                'success' => 2,
                'msg' => __('lang_v1.user_not_found')
            ];
            return $output;
        }
        if (Auth::attempt($credentials)) {
            User::where('id', Auth::user()->id)->update(['lock_screen' => 0]);
            $output = [
                'success' => 1,
                'msg' => __('lang_v1.success')
            ];
        } else {
            $output = [
                'success' => 0,
                'msg' => __('lang_v1.wrong_password')
            ];
        }


        return $output;
    }

    public function businessUsers(Request $request)
    {
        $business_id = $request->business_id;
        $users = User::leftjoin('business', 'users.business_id', 'business.id')
            ->where('users.business_id', $business_id)
            ->select([
                'users.id', 'username', 'business.name as business_name', 'contact_number',
                DB::raw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) as full_name"), 'email'
            ]);

        return Datatables::of($users)
            ->addColumn(
                'role',
                function ($row) {
                    $role_name = $this->moduleUtil->getUserRoleName($row->id);
                    return $role_name;
                }
            )
            ->addColumn(
                'business_name',
                function ($row) {
                    return $row->business_name;
                }
            )
            
            ->addColumn(
                'action',
                '
                <button data-href="{{action(\'ManageUserController@changePassword\',[$id])}}" data-container=".change_password" class="btn btn-xs btn-primary btn-modal"><i class="fa fa-key"></i> @lang("superadmin::lang.update_password")</button>
                '
            )
            ->filterColumn('full_name', function ($query, $keyword) {
                $query->whereRaw("CONCAT(COALESCE(surname, ''), ' ', COALESCE(first_name, ''), ' ', COALESCE(last_name, '')) like ?", ["%{$keyword}%"]);
            })
            ->removeColumn('id')
            ->rawColumns(['action'])
            ->make(true);
    }

    public function changePassword($id)
    {
        $username = User::where('id', $id)->first()->username;
        return view('manage_user.change_password')->with(compact('id', 'username'));
    }

    public function updatePassword($id, Request $request)
    {
        try {
            $password = Hash::make($request->password);
            User::where('id', $id)->update(['password' => $password]);
            if (!empty($request->update_sms)) {
                $user = User::where('id', $id)->first();
                $sms_settings = request()->session()->get('business.sms_settings');

                $request_data = [
                    $sms_settings['send_to_param_name'] => $user->contact_number,
                    $sms_settings['msg_param_name'] => System::getProperty('sms_on_password_change'),
                ];
                $client = new Client();
                if ($sms_settings['request_method'] == 'get') {
                    $response = $client->get($sms_settings['url'] . '?' . http_build_query($request_data));
                } else {
                    $response = $client->post($sms_settings['url'], [
                        'form_params' => $request_data
                    ]);
                }
            }
            $output = [
                'success' => true,
                'msg' => __("superadmin::lang.password_update_success")
            ];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    //User Setting Work

    /**
     *  Enable Or Disable reCAPTCHA For Users
     * 
     * @developer Sakhawatn
     **/
    public function changeReCAPTCHAStatus($user_id)
    {
        if (!auth()->user()->can('user.edit')) {
            abort(403, 'Unauthorized action.');
        }

        if (request()->ajax()) {
            try {
                $business_id = request()->session()->get('user.business_id');

                $user = User::with('setting')->where('business_id', $business_id)
                    ->where('id', $user_id)->first();
                $userSetting = UserSetting::where('user_id',$user_id)->first();
                if(!$userSetting)
                {
                    $userSetting = new UserSetting();
                    $userSetting->user_id = $user_id;
                }
                $userSetting->re_captcha_enabled =($user->setting) ? !$user->setting->re_captcha_enabled :false;
                $userSetting->re_captcha_enabled_date = now();
                $userSetting->save();  

                $output = [
                    'success' => true,
                    'msg' =>  $userSetting->re_captcha_enabled ?__("business.ReCAPTCHA_enable_success_message"):__("business.ReCAPTCHA_disable_success_message")
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'success' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        }
    }

    /**
     * Check ReCAPCHA Setting Allow
     *
     **/
    public function reCapcha_setting(Request $request)
    {
        if (request()->ajax()) {
            try {
        
                $user = User::with('setting')->where('username',$request->user_name)->first();
                $output = [
                    'status' => (isset($user->setting) && $user->setting->re_captcha_enabled == 1)?true :false,
                ];
            } catch (\Exception $e) {
                \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

                $output = [
                    'status' => false,
                    'msg' => __("messages.something_went_wrong")
                ];
            }

            return $output;
        } 
        
    }

  
}
