<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

use Modules\Superadmin\Entities\SmsApiClient;
use App\Utils\TransactionUtil;
use App\Http\Controllers\Controller;
use Modules\Superadmin\Entities\RefillBusiness;
use App\SmsLog;


class SmsApiController extends Controller
{
    protected $transactionUtil;
    
    public function __construct(TransactionUtil $transactionUtil)
    {
        $this->transactionUtil = $transactionUtil;
    }
    
    public function checkBalance(Request $request)
    { 
        $rules = [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
        ];
    
        // Perform validation
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
    
        $username = $request->username;
        $password = $request->password;
        
        $auth_user = $this->__authenticate($username, $password);
        
        if(empty($auth_user)){
            return response()->json(['message' => 'Authorization faied'], 401);
        }
        
        $date = date('Y-m-d');
        
        $balance = $this->transactionUtil->__getSMSBalance($date, $auth_user->id, 'client');
        
        return response()->json(['message' => 'Success', 'balance' => $balance], 200);
        
    }
    
    public function sendSms(Request $request)
    { 
        $rules = [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'message' => 'required',
            'sender_name' => 'required',
            'phone_nos' => 'required',
        ];
    
        // Perform validation
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
    
        $username = $request->username;
        $password = $request->password;
        $message = $request->message;
        $sender_name = $request->sender_name;
        $phone_nos = str_replace(' ','',$request->phone_nos);
        
        $auth_user = $this->__authenticate($username, $password);
        
        if(empty($auth_user)){
            return response()->json(['message' => 'Authorization faied'], 401);
        }
        
        $sender_names = explode(',',str_replace(' ','',$auth_user->sender_names));
        
        if(!in_array($sender_name, $sender_names)){
            return response()->json(['message' => 'Sender name not recognized'], 403);
        }
        
        $configs = false;
        
        if($auth_user->default_gateway == 'ultimate_sms'){
            if(!empty($auth_user->ultimate_token)){
                $configs = true;
            }
        }
        
        if($auth_user->default_gateway == 'hutch_sms'){
            if(!empty($auth_user->hutch_username) && !empty($auth_user->hutch_password)){
                $configs = true;
            }
        }
        
        if(empty($configs)){
            return response()->json(['message' => 'API misconfigured, please contact support'], 406);
        }
        
        $date = date('Y-m-d');
        $total_cost = $this->__getTotalCost($message, $phone_nos, $auth_user);
        $balance = $this->transactionUtil->__getSMSBalance($date, $auth_user->id, 'client');
        
        if($total_cost > $balance){
            return response()->json(['message' => 'Insufficient Balance to send the messages. You wallet has '.$balance.' but you need '.$total_cost], 406);
        }
        
        $send_sms = $this->__sendSms($auth_user, $sender_name, $message, $phone_nos);
        
        if(empty($send_sms)){
            return response()->json(['message' => 'Messages could not be sent, please try again'], 500);
        }else{
            $this->transactionUtil->__notifyLowSMSBalance($auth_user->id, 'client',$sender_name);
            return response()->json(['message' => 'Success', 'data' => $send_sms], 200);
        }
        
    }
    
    public function updatePassword(Request $request)
    { 
        $rules = [
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:6',
            'new_password' => 'required|string|min:6',
            'confirm_password' => 'required|string|same:new_password'
        ];
    
        $validator = Validator::make($request->all(), $rules);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
        
        $username = $request->username;
        $password = $request->password;
        
        $new_password = $request->new_password;
        $confirm_password = $request->confirm_password;
        
        $auth_user = $this->__authenticate($username, $password);
        
        if(empty($auth_user)){
            return response()->json(['message' => 'Authorization faied'], 401);
        }
        
        $auth_user->password = $new_password;
        $auth_user->save();
        
        return response()->json(['message' => 'Success'], 200);
        
    }
   
   public function __authenticate($username, $password){
        $members = SmsApiClient::where('username',$username)->where('password',$password)->first();
        return $members;
   }
   
   function __smsType($text) {
        $isUnicode = preg_match('/[^\x00-\x7F]/', $text);
        $sms_type = $isUnicode ? 'Unicode' : 'English';
        return $sms_type;
    }
    
    function __getNumberOfSms($text) {
        $isUnicode = preg_match('/[^\x00-\x7F]/', $text);
        $charsPerSms = $isUnicode ? 70 : 160;
        $numOfSms = ceil(strlen($text) / $charsPerSms);
        return $numOfSms;
    }
    
    function __getTotalCost($message, $phone_nos, $auth_user){
        $no_of_sms = $this->__getNumberOfSms($message);
        $unit_cost = RefillBusiness::leftjoin('sms_refill_packages','sms_refill_packages.id','refill_business.package_id')
                        ->where('refill_business.business_id',$auth_user->id)->where('refill_business.type','client')
                        ->select(['sms_refill_packages.unit_cost'])
                        ->orderBy('refill_business.date','DESC')->first()->unit_cost ?? 2;
        $total_cost = $no_of_sms * $unit_cost * sizeof(explode(',',$phone_nos));
        return $total_cost;
    }
    
    
   
   public function __sendSms($auth_user, $sender_name, $message, $phone_nos){
       $sent = false;
       
        $no_of_sms = $this->__getNumberOfSms($message);
        $unit_cost = RefillBusiness::leftjoin('sms_refill_packages','sms_refill_packages.id','refill_business.package_id')
                        ->where('refill_business.business_id',$auth_user->id)->where('refill_business.type','client')
                        ->select(['sms_refill_packages.unit_cost'])
                        ->orderBy('refill_business.date','DESC')->first()->unit_cost ?? 2;
        $sms_log = array(
            'business_id' => $auth_user->id,
            'message' => $message,
            'no_of_characters' => strlen($message),
            'no_of_sms' => $no_of_sms,
            'sms_type' => 'api_message',
            'unit_cost' => $unit_cost,
            'sms_type_' => $this->__smsType($message),
            'total_cost' => $no_of_sms * $unit_cost * 1,
            'default_gateway' => $auth_user->default_gateway,
            'sender_name' => $sender_name,
            'sms_status' => 'Sent',
            'business_type' => 'client',
            'username' => $auth_user->username
        );
        
        
        $correct_phones = $this->transactionUtil->validateNos($phone_nos)['valid'];
        $incorrect_phones = $this->transactionUtil->validateNos($phone_nos)['invalid'];
        
        $phone_nos = implode(',',$correct_phones);
        
        foreach($incorrect_phones as $incorrect){
            $sms_log['recipient'] = $incorrect;
            $sms_log['uuid'] = rand(11111111111,99999999999);
            $sms_log['sender_name'] = $sender_name;
           
            $sms_log['sms_status'] = 'Delivered';
            
            SmsLog::create($sms_log);
        }
        
       if($auth_user->default_gateway == 'ultimate_sms'){
            $sent = $this->ultimateSMS($phone_nos,$message, $sender_name, $auth_user->ultimate_token);
            
            if(!empty($sent)){
                $sent_data = array();
                foreach($sent['data'] as $one){
                    $sms_log['recipient'] = $one['to'];
                    $sms_log['uuid'] = $one['uid'];
                    
                    if($one['customer_status'] == 'Delivered'){
                        $sms_log['sms_status'] = 'Delivered';
                    }
                    
                    $stored = SmsLog::create($sms_log);
                    
                    $sent_data[] = array('uid' => $stored->uuid, 'to' => $stored->recipient, 'sms_count' => $stored->no_of_sms, 'unit_cost' => $stored->unit_cost, 'total_cost' => $stored->total_cost);
                }
                
                $sent = $sent_data;
            }
        }
        
        if($auth_user->default_gateway == 'hutch_sms'){
            $sent = $this->hutchSendSMS($phone_nos,$message,$auth_user->hutch_username,$auth_user->hutch_password,$sender_name);
            
            if(!empty($sent)){
                $sent_data = array();
                foreach(explode(',',$phone_nos) as $one){
                    $sms_log['recipient'] = $one;
                    $sms_log['uuid'] = rand(111111111,999999999);
                    
                    $sms_log['sms_status'] = 'Delivered';
                    
                    $stored = SmsLog::create($sms_log);
                    
                    $sent_data[] = array('uid' => $stored->uuid, 'to' => $stored->recipient, 'sms_count' => $stored->no_of_sms, 'unit_cost' => $stored->unit_cost, 'total_cost' => $stored->total_cost);
                }
                
                $sent = $sent_data;
            }
        }
        
        return $sent;
   }
   
   
   public function ultimateSMS($phone,$sms, $sender_name, $token){
       
       $return = false;
       try{
           if(!empty(env('UTLIMATE_SMS_SERVER')) && !empty($phone) && !empty($sms) && !empty($sender_name) && !empty($token)){
            
                $url = env('UTLIMATE_SMS_SERVER');
                
                $type = $this->__smsType($sms) == 'English' ? 'plain' : 'unicode';
                
                
                $sdata = array(
                    'recipient' => $phone,
                    'sender_id' => $sender_name,
                    'type' => $type,
                    'message' => $sms
                );
                
                $token = $token;
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($sdata));
                curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                    'Content-Type: application/json',
                    "Authorization: Bearer $token"
                ));
                $result = curl_exec($ch);
                curl_close($ch);
                
                if(!empty($result)){
                    
                   
                    $response = json_decode($result,true);
                    if(!empty($response) && !empty($response['status']) && $response['status'] == 'success'){
                        $return = $response;
                    }
                }
                
            }
           
       }catch(\Exception $e){
           
       }
        
        return $return;

    }
    
    public function hutchSendSMS($phone,$sms,$username,$password,$sender_name){
        $return = false;
        try{
            if(!empty(env('HUTCH_SEND_SMS_LINK')) &&  !empty(env('HUTCH_AUTH_LINK')) && !empty(env('HUTCH_VERSION')) && !empty($username) && !empty($password) && !empty($sender_name)){
                
                $url = env('HUTCH_SEND_SMS_LINK');
                
                $sdata = array(
                    "campaignName" => "Demo",
                    "mask" => $sender_name,
                    "numbers" => $phone,
                    "content" => $sms,
                    "deliveryReportRequest" => true
                );
                
                $token = $this->hutchAuth($username, $password);
                
                if(!empty($token)){
                    
                    $curl = curl_init();
    
                    curl_setopt_array($curl, array(
                      CURLOPT_URL => $url,
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => '',
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 0,
                      CURLOPT_FOLLOWLOCATION => true,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => 'POST',
                      CURLOPT_POSTFIELDS => json_encode($sdata),
                      CURLOPT_HTTPHEADER => array(
                        'X-API-VERSION: v1',
                        'Content-Type: application/json',
                        'Authorization: Bearer '.$token,
                      ),
                    ));
                    
                    $result = curl_exec($curl);
                    curl_close($curl);
                    
                    if(!empty($result)){
                        
                        $response = json_decode($result,true);
                        
                        if(!empty($response) && !empty($response['serverRef'])){
                            $return = $response['serverRef'];
                        }
                    }
                    
                    
                }
                
            }
        }catch(\Exception $e){
           
       }
        
        return $return;
    }
    
    public function hutchAuth($username,$password){
        $accessToken = null;
        
        $curl = curl_init();
            
        $sdata = array(
            'username' => $username,
            'password' => $password,
        );

        curl_setopt_array($curl, array(
            CURLOPT_URL => env('HUTCH_AUTH_LINK'),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>  json_encode($sdata),
            CURLOPT_HTTPHEADER => array(
                'X-API-VERSION: '.env('HUTCH_VERSION'),
                'Content-Type: application/json',
            ),
        ));
        
        $response = curl_exec($curl);
        $status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE); // Get the HTTP status code
        
        curl_close($curl);
        
        if ($status_code === 200) {
            $data = json_decode($response, true);
            
            if (isset($data['accessToken'])) {
                $accessToken = $data['accessToken'];
                
            } 
        } 
        
        return $accessToken;
    }
   
}
