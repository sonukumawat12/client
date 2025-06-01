<?php

namespace App\Http\Controllers;

use App\User;
use App\System;
use App\Business;
use Carbon\Carbon;
use App\UserSetting;
use App\VerificationCode;
use App\Utils\BusinessUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Yajra\DataTables\Facades\DataTables;

class UsersOPTController extends Controller
{
   /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    /**
     * Constructor
     *
     * @param Util $commonUtil
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil)
    {
        $this->businessUtil = $businessUtil;
    }
   
   
   
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        if (request()->ajax()) {
                  $varifyActivity = VerificationCode::with('user');
            if (!empty(request()->start_date) && !empty(request()->end_date)) {
                $varifyActivity = $varifyActivity->whereDate('created_at', '>=', request()->start_date);
                $varifyActivity = $varifyActivity->whereDate('created_at', '<=', request()->end_date);
            }

            if(!empty(request()->get('user')))
            {
                $varifyActivity =  $varifyActivity->whereHas('user',function($query){
                    $query->where('id',request()->get('user'));
                });
            }

            return Datatables::of($varifyActivity)
                ->addColumn(
                    'date',
                    function ($row) {
                        return Carbon::parse($row->created_at)->format('d M,y');
                    }

                )
               
                
                
                ->addColumn('time', function($row){
                    return Carbon::parse($row->created_at)->format('H:i A');
                    }
                )
                ->addColumn('user',
                    function ($row) {
                        return $row->user->username;
                    }
                )
                ->addColumn('mobile',
                    function ($row) {
                        return $row->user->contact_number;
                    }
                )
               
                ->removeColumn('id')
                ->make(true);
        }

        $business = Business::with('owner')->select(DB::raw("CONCAT(COALESCE(name, ''),' ',COALESCE(company_number, '')) AS company_name"), 'id','owner_id')->get();
        $names = [];
        foreach ($business as $bus) {
            if($bus->owner)
            $names[$bus->owner->id] = $bus->company_name.'['.$bus->owner->username.']';
        }
        return view('opt_manage.index',compact('names'));
  
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }


    public function checkVerification(Request $request) {
        $code = $request->code;
        $user = User::find(auth()->user()->id);
        $setting = UserSetting::where('user_id',$user->id)->first();
        if($setting->verification_attempt_count >= 2)
        {
            return [
                'success'=> false,
                'resend_opt'=> ' <p>
                Verification tried 3 times. Please click here to <a class="link resend-opt" >resent the OTP</a>
            </p>',
                'msg'=> 'Verification tried 3 times. Please click here to resent the OTP',
            ];
        }
        $setting->verification_attempt_count ++;
        $setting->save();
        if($verify = VerificationCode::where(['user_id'=>$user->id,'code'=>$code])->whereNull(['expired_at','confirmed_at'])->first())
        {
            $verify->confirmed = true;
            $verify->confirmed_at = now();
            $verify->save();
           
            $setting->verification_done = true;
            $setting->save();
            $previousUrl = Session::get('previousUrl');
            if($previousUrl && strpos($previousUrl, route('shipping.index')) !== false) {
                return  ['success'=> true,'url'=>$previousUrl];
            }
            else
            {
                return  ['success'=> true,'url'=>'/home'];
            }

        }
        return [
            'success' => false,
            'msg'=> 'Wrong OTP. Please enter the correct OTP',
        ];
    }

    /**
     * Resend OPT Code
     *
     * @param Type $var Description
     * @return type
     * @throws conditon
     **/
    public function resendOpt()
    {
        if($this->sendOpt())
        {
            return [
                'status'=>true, 
                'msg'=>"Opt send to your's attached contact number"
            ];
        }
        return [
            'status' => false,
            'msg'=> 'something went wrong'
        ];
    }

    /**
     *  OTP Verification enabled 
     *
     * @dev Sakhawat Kamran
     **/
    public function sendOpt()
    {
        $user = User::with('setting')->find(auth()->user()->id);
        if($user->setting && $user->setting->opt_verification_enabled)
        {
            $user->setting->verification_attempt_count = 0;
            $user->setting->save();
            $business = Business::where('owner_id',$user->id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
            $msg = System::getProperty('sms_on_verification');
            VerificationCode::where(['user_id'=>$user->id,'type'=>'sms'])->whereNull('expired_at')->update(['expired_at'=>now()]);
            $verification_code = new VerificationCode();
            $verification_code->user_id = $user->id;
            $verification_code->type = 'sms';
            $verification_code->save();
            $code = $verification_code->code;
            $msg = str_replace('{CODE}',$code, $msg);
            
            $data = [

                'sms_settings' => $sms_settings,

                'mobile_number' => $user->contact_number,

                'sms_body' => $msg

            ];
            return $this->businessUtil->superadminTransactionalSms($data); 
        }
        return false;
    }
}
