<?php

namespace App\Http\Controllers\Auth;

use App\Business;
use App\User;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Utils\ModuleUtil;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Modules\Petro\Entities\PumpOperatorAssignment;
use Illuminate\Support\Facades\Validator;
use Litespeed\LSCache\LSCache;
use App\PumperLoginAttempt;

class PumpOperatorLoginController extends Controller
{
    /**
     * All Utils instance.
     *
     */
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
     * @return Renderable
     */
    public function login()
    {
        $settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
        
        $cc = request()->cc;
        $business = null;
        if (!empty($cc)) {
            $business = Business::where('company_number', $cc)->select('name', 'id')->first();
        } else {
            abort(403, 'Unauthorized action.');
        }

        if (Auth::user() && request()->session()->get('user.is_pump_operator')) {
            return redirect()->to('/petro/pump-operators/dashboard');
        }
        
        return view('petro::pump_operators.login')->with(compact(
            'settings',
            'business',
            'cc'
        ));
    }

    public function postLogin(Request $request)
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip_address = $_SERVER['HTTP_CLIENT_IP'];
        }elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
        }else{
            $ip_address = $_SERVER['REMOTE_ADDR'];
        }
        $pumperLoginAttempt = PumperLoginAttempt::where('ip_address', $ip_address)->first();
        if((!is_null($pumperLoginAttempt)) && $pumperLoginAttempt->status == "Blocked"){
            $output = [
                'success' => 0,
                'msg' => "Login attempts exceeded. Contact admin to reset your passcode.",
            ];
            return redirect()->back()->with('status', $output);
        }

        $validator = Validator::make($request->all(), [
            'passcode' => 'required',
        ]);

        if ($validator->fails()) {
            $output = [
                'success' => 0,
                'msg' => $validator->errors()->all()[0]
            ];

            return redirect()->back()->with('status', $output);
        }

        $business_id = request()->session()->get('business.id');
        $passcode = $request->passcode;
        $user = User::where('pump_operator_passcode', $passcode)->first();

        if(is_null($user)){
            if(is_null($pumperLoginAttempt)){
                $pumperLoginAttempt = PumperLoginAttempt::create([
                    'business_id' => !empty($request->business_id) ? $request->business_id : $business_id,
                    'company_number' => $request->company_number,
                    'ip_address' => $ip_address,
                    'last_entered_passcode' => $passcode,
                    'attempt_count' => 1,
                    'status' => "Active",
                ]);
            } else {
                $pumperLoginAttempt->business_id = !empty($request->business_id) ? $request->business_id : $business_id;
                $pumperLoginAttempt->company_number = $request->company_number;
                $pumperLoginAttempt->attempt_count += 1;
                $pumperLoginAttempt->status = ($pumperLoginAttempt->attempt_count >= 5) ? "Blocked" : "Active";
                $pumperLoginAttempt->last_entered_passcode = $passcode;
                $pumperLoginAttempt->update();
            }
            $output = [
                'success' => 0,
                'msg' => "Your Passcode is incorrect. Please recheck and try again. {$pumperLoginAttempt->attempt_count} of 5 attempts",
            ];
            if((!is_null($pumperLoginAttempt)) && $pumperLoginAttempt->attempt_count >= 5){
                $output = [
                    'success' => 0,
                    'msg' => "Your passcode is incorrect. {$pumperLoginAttempt->attempt_count} of 5 attempts. Contact admin to reset your passcode.",
                ];
                return redirect()->back()->with('status', $output);
            }
            return redirect()->back()->with('status', $output);
        } else {
            if(!is_null($pumperLoginAttempt)) {
                $pumperLoginAttempt->business_id = !empty($request->business_id) ? $request->business_id : $business_id;
                $pumperLoginAttempt->company_number = $request->company_number;
                $pumperLoginAttempt->attempt_count = 0;
                $pumperLoginAttempt->status = "Active";
                $pumperLoginAttempt->update();
            }
        }

        $pump_operator_id = $user->pump_operator_id;
        $shift_number = PumpOperatorAssignment::where('pump_operator_id', $pump_operator_id)->max('shift_number');
        
        if (!empty($user)) {
            Auth::loginUsingId($user->id);

            // return redirect()->to('/petro/pump-operators/dashboard');
            return redirect()->to('/petro/pump-operators/dashboard?shift_number=' . urlencode($shift_number));
        } else {
            $output = [
                'success' => 0,
                'msg' => __('lang_v1.sorry_user_not_found')
            ];

            return redirect()->back()->with('status', $output);
        }


        return view('petro::pump_operators.login')->with(compact(
            'business',
            'settings'
        ));
    }

    /**
     * logout pump operator
     * @return Renderable
     */
    public function logout(Request $request)
    {
        $cc = $request->session()->get('business.company_number');
        $from_admin = $request->session()->get('from_admin');
        
        request()->session()->flush();
        
        LSCache::purge('*');
        Auth::logout();
        
        if(!empty($from_admin)){
            Auth::loginUsingId($from_admin);
            return redirect('/home');
        }
        
        if($request->main_system){
            return redirect('/login?cc=' . $cc);
        }
        

        return redirect('/pump-operator/login?cc=' . $cc);
    }
}
