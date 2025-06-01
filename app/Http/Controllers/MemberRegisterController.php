<?php

namespace App\Http\Controllers;

use App\Member;
use App\System;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Modules\Member\Http\Controllers\MemberController;
use App\User;
use App\Utils\LocationUtil;

class MemberRegisterController extends Controller
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(
        ModuleUtil $moduleUtil
    ) {
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // print_r($request->member_username); die();

        $validator = Validator::make($request->all(), [
            'username' => 'required|unique:members',
            //'member_password' => 'required',
            //'member_confirm_password' => 'required|same:member_password',
            'member_name' => 'required',
            'member_address' => 'required',
            // 'member_town' => 'required',
            // 'member_district' => 'required',
            'member_mobile_number_1' => 'numeric',
            //'member_gender' => 'required',
            //'member_group' => 'required',
        ]);
        $member_password=123456;
        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];
            return redirect()->back()->with('status', $output);
        }
        
        try {
            $member_data = [
                'username' => $request->username,
                'password' => ($request->member_password)? Hash::make($request->member_password) :'',
                'name' => $request->member_name,
                'address' => $request->member_address,
                'district' => $request->member_district ?? null,
                'town' => $request->member_town ?? null,
                'mobile_number_1' => $request->member_mobile_number_1,
                'mobile_number_2' => $request->member_mobile_number_2,
                'mobile_number_3' => $request->member_mobile_number_3,
                'land_number' => $request->member_land_number ?? null,
                'gender' => $request->member_gender ?? null,
                'date_of_birth' =>($request->member_date_of_birth)?createDate($request->member_date_of_birth) : null,
                'gramasevaka_area' => $request->gramasevaka_area,
                'bala_mandalaya_area' => $request->bala_mandalaya_area ?? null,
                'member_group' => $request->member_group ?? null,
                'electrorate_id' => $request->electrorate ?? null,

            ];
            
            $user_data = [
                'username' => $request->username,
                'password' => ($member_password)? Hash::make($member_password) :'',
                'first_name' => $request->member_name,
                'member' => 1,

            ];
            
            $location_resp = LocationUtil::getRequestLocation( $request );
            
            
            DB::beginTransaction();
            $member = Member::create($member_data);
            $user= User::create($user_data);
                $user_settings = [
                'user_id' => $user->id,
                

            ];
            DB::table('user_settings')->insert($user_settings);
            
            if($request->has('family'))
            {
                $memberController = new MemberController();
                $memberController->addOrUpdateMember($request->family,$member->id,$member_data);
            }
            //Module function to be called after after business is created
            if (config('app.env') != 'demo') {
                $this->moduleUtil->getModuleData('after_member_created', ['member' => $member]);
            }
            
            if( $location_resp['success'] ){
                LocationUtil::storeUserLocation( request()->session()->get('user.id'), 'member_register', $location_resp['data'] );
            }
            
            DB::commit();


            $system_url = '<a href='.env('APP_URL').'>'.env('APP_URL').'</a>';
            $title = System::getProperty('member_register_success_title');
            $msg = System::getProperty('member_register_success_msg');
            $msg = str_replace('{username}', $member->username, $msg);
            $msg = str_replace('{name}', $member->name, $msg);
            $msg = str_replace('{system_url}', $system_url, $msg);

            $output = [
                'success' => 1,
                'title' => $title,
                'msg' => __('member::lang.member_group_add_success')
            ];
         
        } catch (\Exception $e) {
            Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = [
                'success' => false,
                'msg' => __("messages.something_went_wrong")
            ];
            return redirect()->back()->with('status', $output);
        }
           return redirect()->back()->with('status', $output);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
