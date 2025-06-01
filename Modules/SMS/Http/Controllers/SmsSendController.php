<?php

namespace Modules\SMS\Http\Controllers;

use App\Member;
use App\AllContact;
use App\Utils\ModuleUtil;
use App\Utils\BusinessUtil;
use App\Utils\TransactionUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Modules\Member\Entities\Balamandalaya;
use Modules\Member\Entities\GramasevaVasama;
use Modules\Superadmin\Entities\Subscription;
use Modules\SMS\Entities\SmsCampaign;
use Modules\SMS\Entities\SmsGroup;
use Yajra\DataTables\Facades\DataTables;
use App\Business;
use App\ContactGroup;
use App\Contact;
use Illuminate\Support\Facades\DB;
use App\SmsLog;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;



use Maatwebsite\Excel\Facades\Excel;

class SmsSendController extends Controller
{
    protected $businessUtil;
    protected $transactionUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param Util $businessUtil
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, TransactionUtil $transactionUtil,ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->transactionUtil =  $transactionUtil;
        $this->moduleUtil = $moduleUtil;

    }

    public function smsCampaign() {
        $business_id = request()->session()->get('business.id');

        // Fetch sms settings, non-delivery setting, and subscription details in one query
        $business = Business::where('id', $business_id)->select('sms_settings', 'sms_non_delivery')->first();
        $smsSettings = $business->sms_settings;
        $smsNonDelivery = $business->sms_non_delivery;

        // Fetch SMS groups and subscription package details
        $sms_group = SmsGroup::where('business_id', $business_id)
        ->select('id', 'group_name', \DB::raw("JSON_LENGTH(members) as member_count"))
        ->get();

        $subscription = Subscription::where('business_id', $business_id)->select('package_details','module_activation_details')->first();
        $package_details = $subscription->package_details;
        $module_activation_details = $subscription->module_activation_details;
        $module_activation_details = json_decode($module_activation_details, true);
        // Determine if Customer Group is enabled based on package details
        $isCustomerGroupEnabled = (
            $package_details['contact_module'] == 1 &&
            $package_details['shipping_module'] == 1 &&
            $package_details['airline_module'] == 1
        );
        // dd(ModuleUtil::hasThePermissionInSubscription($business_id, 'contact_group_customer'));
        // ($business_id, $prepend_none = true, $prepend_all = false, $type = null)
        // Fetch contact groups for dropdown
        // $contact_grps = ContactGroup::forDropdown($business_id, false);
        // $contact_grps_supplier = ContactGroup::forDropdown($business_id, false,false, 'supplier');
        $modelMap = [];

        if (
            ModuleUtil::hasThePermissionInSubscription($business_id, 'contact_group_customer') ||
            ModuleUtil::hasThePermissionInSubscription($business_id, 'contact_group_supplier')
        ) {
            $modelMap['1'] = 'Contact Group';
        }

        if ($this->moduleUtil->isModuleEnabled('account')) {
            $modelMap['2'] = 'Account Groups';
        }

        if (empty($module_activation_details['airline_module_expiry_date']) ||
            !Carbon::parse($module_activation_details['airline_module_expiry_date'])->isPast()) {
            $modelMap['3'] = 'Airline';
            $modelMap['4'] = 'Airline Agents';
        }

        if (empty($module_activation_details['contact_expiry_date']) ||
            !Carbon::parse($module_activation_details['contact_expiry_date'])->isPast()) {
            $modelMap['5'] = 'Contacts';
        }

        if (empty($module_activation_details['bakery_expiry_date']) ||
            !Carbon::parse($module_activation_details['bakery_expiry_date'])->isPast()) {
            $modelMap['6'] = 'Bakery Drivers';
        }

        if (empty($module_activation_details['shipping_module_expiry_date']) ||
            !Carbon::parse($module_activation_details['shipping_module_expiry_date'])->isPast()) {
            $modelMap['7'] = 'Shipping Agents';
            $modelMap['8'] = 'Shipping Partners';
            $modelMap['9'] = 'Shipping Recipients';
        }

        if (empty($module_activation_details['hr_expiry_date']) ||
            !Carbon::parse($module_activation_details['hr_expiry_date'])->isPast()) {
            $modelMap['10'] = 'Employees';
        }


        // Convert to array for the dropdown
        $allContacts = $modelMap;



        // Return view with compacted variables
        return view('sms::send_sms.sms_campaign')->with(compact(
            'allContacts', 'isCustomerGroupEnabled', 'sms_group', 'smsNonDelivery', 'smsSettings'
        ));
    }

    public function getContactsByGroup(Request $request)
    {
        $globalIds = $request->get('global_ids', []);

        $modelMap = [
            '1' => ContactGroup::class,
            '2' => \App\AccountGroup::class,
            '3' => \Modules\Airline\Entities\Airline::class,
            '4' => \Modules\Airline\Entities\AirlineAgent::class,
            '5' => Contact::class,
            '6' => \Modules\Bakery\Entities\BakeryDriver::class,
            '7' => \Modules\Shipping\Entities\ShippingAgent::class,
            '8' => \Modules\Shipping\Entities\ShippingPartner::class,
            '9' => \Modules\Shipping\Entities\ShippingRecipient::class,
            '10' => \Modules\HR\Entities\Employee::class,
        ];

        $nameFieldMap = [
            '1' => 'name',
            '2' => 'name',
            '3' => 'airline',
            '4' => 'agent',
            '5' => 'name',
            '6' => 'driver_name',
            '7' => 'name',
            '8' => 'name',
            '9' => 'name',
            '10' => 'username',
        ];

        $sourceMap = [
            '1' => 'contact_groups', 
            '2' => 'account_groups',
            '3' => 'airlines',
            '4' =>'airline_agents',
            '5'=>'contacts',
            '6' => 'bakery_drivers',
            '7' =>'shipping_agents',
            '8' => 'shipping_partners',
            '9' =>'shipping_recipients',
            '10' =>'employees',
        ];

        $allContacts = [];

        foreach ($globalIds as $globalId) {
            $source = $globalId;
            $model = $modelMap[$source] ?? null;
            $nameField = $nameFieldMap[$source] ?? 'name';

            if ($model && $nameField) {
                $results = $model::select('id', $nameField . ' as name')->get();
                foreach ($results as $item) {
                    $allContacts[] = [
                        'id' => $sourceMap[$source] . '_' . $item->id,  // formatted ID
                        'name' => $item->name,
                    ];
                }
            }
        }

        return response()->json([
            'contacts' => $allContacts
        ]);
    }


    public function smsFromFile(){
        $business_id = request()->session()->get('business.id');
        $smsSettings = Business::where('id', $business_id)->value('sms_settings');

        return view('sms::send_sms.sms_from_file')->with(compact('smsSettings'));
    }

    public function submitSmsFile(Request $request){
        $business_id = request()->session()->get('business.id');
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            $data = array();
            if ($request->hasFile('file')) {
                $file = $request->file('file');

                $parsed_array = Excel::toArray([], $file);

                $original_parsed_array = $parsed_array;


                //Remove header row
                $data['imported_data'] = array_splice($parsed_array[0], 1);

                $tags = array();
                foreach($parsed_array[0][0] as $tag){
                    $tags["{".str_replace(' ','',strtolower($tag))."}"] = $tag;
                }

                $data['tags'] = $tags;

                $data['schedule_campaign'] = $request->schedule_campaign;
                $data['name'] = $request->name;
                $data['send_time'] = $request->send_time;

            }

            if(empty($data['imported_data'])){
                $output = [
                    'success' => false,
                    'msg' => __('sms::lang.file_is_empty')
                ];

                return redirect()->back()->with('status', $output);
            }


            return view('sms::send_sms.sms_from_file_final')->with(compact('data'));

        } catch (\Exception $e) {
            DB::rollback();

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];

            return redirect()->back()->with('status', $output);
        }


    }
     private function str_replace_array($search, array $replace, $subject) {
        foreach ($replace as $item) {
            $subject = preg_replace('/' . preg_quote($search, '/') . '/', $item, $subject, 1);
        }
        return $subject;
    }

    public function submitsmsCampaign(Request $request) {
        // dd($request->all());
        $business_id = request()->session()->get('business.id');
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            DB::beginTransaction();

            $input = $request->except('_token');

            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

            $customerGroupId = $input['customer_group'] ?? null;
            $contact_ids = $input['contact_id'] ?? null;
            $smsGroupIds = $input['sms_group'] ?? null;

           $sms_group_ids = array_filter(array_map('trim', explode(',', $smsGroupIds)), 'is_numeric');
           $members = [];
           $phone_nos = [];

            if (empty($input['send_time']) && $input['frequency'] == 'One Time' && !array_key_exists('dSpeed', $input)) {
                // $contactsQuery = Contact::whereRaw('LENGTH(mobile) = 11');

                
                $contactsQuery = AllContact::query();

                if (!empty($customerGroupId)) {
                    $sourceMap = [
                        '1' => 'contact_groups',
                        '2' => 'account_groups',
                        '3' => 'airlines',
                        '4' => 'airline_agents',
                        '5' => 'contacts',
                        '6' => 'bakery_drivers',
                        '7' => 'shipping_agents',
                        '8' => 'shipping_partners',
                        '9' => 'shipping_recipients',
                        '10' => 'employees',
                    ];
                    if (!empty($customerGroupId) && is_array($customerGroupId)) {
                        // Map valid numeric IDs to source names
                        $sources = collect($customerGroupId)
                            ->filter(fn($id) => isset($sourceMap[$id]))
                            ->map(fn($id) => $sourceMap[$id])
                            ->values()
                            ->all();
                    
                        if (!empty($sources)) {
                            $contactsQuery->whereIn('source', $sources);
                        }
                    }

                    if (is_array($$contact_ids) && count($contact_ids) != 0) {
                        $contactsQuery->whereIn('global_id', $contact_ids);
                    }
                    // $contactsQuery->where('global_id', $customerGroupId);
                    $contacts = $contactsQuery->select('id','source')->get();
                    // $phone_nos = $contacts->pluck('mobile')->toArray();
                    $contactsWithMobile = $contacts->map(function ($contact) {
                        $modelMap = [
                            'contact_groups' => ContactGroup::class,
                            'employees' => \Modules\HR\Entities\Employee::class,
                            'airlines' => \Modules\Airline\Entities\Airline::class,
                            'airline_agents' => \Modules\Airline\Entities\AirlineAgent::class,
                            'bakery_drivers' => \Modules\Bakery\Entities\BakeryDriver::class,
                            'contacts' => Contact::class,
                            'account_groups' => \App\AccountGroup::class,
                            'shipping_agents' => \Modules\Shipping\Entities\ShippingAgent::class,
                            'shipping_partners' => \Modules\Shipping\Entities\ShippingPartner::class,
                            'shipping_recipients' => \Modules\Shipping\Entities\ShippingRecipient::class,
                        ];
                    
                        $modelClass = $modelMap[$contact->source] ?? null;
                    
                        $originalRecord = $modelClass ? $modelClass::find($contact->id) : null;

                        $mobile = null;

                        if ($originalRecord) {
                            $mobile = $originalRecord->mobile 
                                ?? $originalRecord->mobile_1 
                                ?? $originalRecord->mobile_2
                                ?? $originalRecord->phone 
                                ?? null;
                        }
                    
                        return [
                            'id' => $contact->id,
                            'source' => $contact->source,
                            'mobile' => $mobile,
                        ];
                    });
                }

                if (!empty($sms_group_ids)) {
                    $query = SmsGroup::whereIn('id', $sms_group_ids);
                    $sql = $this->str_replace_array('?', $sms_group_ids, $query->toSql());
                    $smsgroups = $query->get();

                    foreach ($smsgroups as $smsGroup) {
                        $groupMembers = json_decode($smsGroup->members, true);

                        if (is_array($groupMembers)) {
                            $members = array_merge($members, $groupMembers);
                        }
                    }
                }

                // foreach ($contactsWithMobile as $contact) {
                //     echo $contact['mobile'];
                // }

                // Merge contacts with members
                $contactMobiles = collect($contactsWithMobile)->pluck('mobile')->filter()->toArray();
                $memberMobiles = collect($members)->pluck('mobile')->filter()->toArray();

                $allPhoneNumbers = array_merge($contactMobiles, $memberMobiles);


                // Optionally remove duplicates
                $allPhoneNumbers = array_unique($allPhoneNumbers);

                $no_of_sms = $this->transactionUtil->__getNumberOfSms($input['message']);
                $unit_cost = $this->transactionUtil->__businessSMSUnitCost($business_id);
                $date = date('Y-m-d');
                $balance = $this->transactionUtil->__getSMSBalance($date, $business_id, 'business');
                $total_cost = $no_of_sms * $unit_cost * count($allPhoneNumbers);

                if ($total_cost > $balance) {
                    $output = [
                        'success' => false,
                        'msg' => __('sms::lang.insuffucient_balance')
                    ];
                    Log::warning('Insufficient balance for SMS campaign');
                    return redirect()->back()->with('status', $output);
                }

                foreach ($allPhoneNumbers as $contact) {
                    $msg = $input['message'];
                    $validatedNumbers = $this->transactionUtil->validateNos($contact,$business_id);  // Pass the number directly

                    $correct_phones = $validatedNumbers['valid'];
                    $incorrect_phones = $validatedNumbers['invalid'];

                    if (!empty($sms_settings)) {
                        $no_of_sms = $this->transactionUtil->__getNumberOfSms($msg);
                        $unit_cost = $sms_settings['cost_per_sms'] ?? 0; // Assuming you have a way to get unit cost
                        $sms_log_template = [
                            'business_id' => $business_id,
                            'message' => $msg,
                            'no_of_characters' => strlen($msg),
                            'no_of_sms' => $no_of_sms,
                            'sms_type' => $input['name'],
                            'unit_cost' => $unit_cost,
                            'total_cost' => $no_of_sms * $unit_cost,
                            'sms_status' => 'Scheduled',
                            'schedule_time' => $input['send_time'],
                            'business_type' => 'business',
                            'username' => auth()->user()->username,
                            'default_gateway' => $sms_settings['default_gateway'],
                            'uuid' => rand(11111111111, 99999999999),
                            'sender_name' => $sms_settings['hutch_mask']
                        ];

                        // Handle incorrect phone numbers
                        if (!empty($incorrect_phones)) {
                            foreach ($incorrect_phones as $ph) {
                                $sms_log = array_merge($sms_log_template, [
                                    'sms_status' => 'Failed',
                                    'recipient' => $ph,
                                ]);
                                SmsLog::create($sms_log);
                                Log::info('SMS log created for incorrect phone', ['phone' => $ph]);
                            }
                        }

                        // Handle correct phone numbers
                        if (!empty($correct_phones)) {
                            foreach ($correct_phones as $ph) {
                                $sms_log = array_merge($sms_log_template, [
                                    'sms_status' => 'Delivered',
                                    'recipient' => $ph,
                                    'sms_settings' => $sms_settings,
                                    'mobile_number' => $ph,
                                    'sms_body' => $msg,
                                ]);

                                Log::info('Attempting to create SMS log', ['sms_log' => $sms_log]);
                                $this->transactionUtil->sendSms($sms_log, $input['name']);
                            }

                            // Send the SMS in bulk for correct phones
                            $data = [
                                'sms_settings' => $sms_settings,
                                'mobile_number' => implode(',', $correct_phones),
                                'sms_body' => $msg
                            ];
                            // $this->transactionUtil->sendSms($data, 'Campaign');
                        }
                    }
                }


            } else {
                $data = [
                    'business_id' => $business_id,
                    'frequency' => $input['frequency'] ?? 1,
                    'name' => $input['name'],
                    'message' => $input['message'],
                    'customer_group' => $input['customer_group'] ?? null,
                    'sms_group' => $smsGroupIds,
                    'next_date' => $input['send_time'],
                    'end_date' => $input['end_time']
                ];

                SmsCampaign::create($data);
                Log::info('SMS campaign created for non-one-time frequency');
            }

            DB::commit();
            $output = [
                'success' => true,
                'msg' =>  __('lang_v1.msg_sent_successfully')
            ];
        } catch (\Exception $e) {
            // Rollback any database changes
            DB::rollback();

            // Log the error with detailed context
            Log::emergency('An error occurred during the process.', [
                'error_message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(), // Optional: adds full stack trace for deeper debugging
            ]);

            // Prepare output message for the response
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }


        return redirect()->back()->with('status', $output);
    }


    public function executeCampaign(){
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            DB::beginTransaction();

            $campaigns = SmsCampaign::where('next_date','<=',date('Y-m-d H:i'))->get();

            foreach($campaigns as $ca){
                $business_id = $ca->business_id;

                $business = Business::where('id', $business_id)->first();
                $sms_settings = $business->sms_settings;

                $contacts = Contact::where('customer_group_id',$ca->customer_group)->whereRaw('LENGTH(mobile) = 11')->select('name','mobile','address','email')->get();
                $phone_nos = Contact::where('customer_group_id',$ca->customer_group)->whereRaw('LENGTH(mobile) = 11')->pluck('mobile')->toArray();

                $no_of_sms = $this->transactionUtil->__getNumberOfSms($ca->message);
                $unit_cost = $this->transactionUtil->__businessSMSUnitCost($business_id);

                $date = date('Y-m-d');
                $balance = $this->transactionUtil->__getSMSBalance($date, $business_id, 'business');
                $total_cost = $no_of_sms * $unit_cost * sizeof($phone_nos);

                if($total_cost < $balance){
                    foreach($contacts as $contact){
                        $msg = $ca->message;
                        $msg = str_replace('{name}',$contact->name,$msg);
                        $msg = str_replace('{phone}',$contact->mobile,$msg);
                        $msg = str_replace('{email}',$contact->email,$msg);
                        $msg = str_replace('{address}',$contact->address,$msg);

                        $correct_phones = $this->transactionUtil->validateNos($contact->mobile)['valid'];
                        $incorrect_phones = $this->transactionUtil->validateNos($contact->mobile)['invalid'];

                        if(!empty($sms_settings)){
                            $no_of_sms = $this->transactionUtil->__getNumberOfSms($msg);
                            $sms_log = array(
                                'business_id' => $business_id,
                                'message' => $msg,
                                'no_of_characters' => strlen($msg),
                                'no_of_sms' => $no_of_sms,
                                'sms_type' => $ca->name,
                                'unit_cost' => $unit_cost,
                                'sms_type_' => $this->transactionUtil->__smsType($msg),
                                'total_cost' => $no_of_sms * $unit_cost * 1,
                                'sms_status' => 'Scheduled',
                                'schedule_time' => $ca->next_date,
                                'business_type' => 'business',
                                'username' => null,
                                'default_gateway' => $sms_settings['default_gateway'],
                                'uuid' => rand(11111111111,99999999999),
                                'sender_name' => $sms_settings['ultimate_sender_id']
                            );

                            if(!empty($incorrect_phones)){
                                foreach($incorrect_phones as $ph){
                                    $sms_log['sms_status'] = 'Delivered';
                                    $sms_log['recipient'] = $ph;

                                }
                            }

                            if(!empty($correct_phones)){
                                foreach($correct_phones as $ph){
                                    $sms_log['recipient'] = $ph;

                                }
                            }


                            SmsLog::create($sms_log);


                        }
                    }

                    if(!empty($sms_settings)){
                        if($ca->frequency == 'One Time'){
                            $ca->delete();
                        }else{
                            $nextTime = Carbon::parse($ca->next_date);

                            if ($ca->frequency == 'daily') {
                                $ca->next_date = $nextTime->addDay();
                            } elseif ($ca->frequency == 'monthly') {
                                $ca->next_date = $nextTime->addMonth();
                            } elseif ($ca->frequency == 'yearly') {
                                $ca->next_date = $nextTime->addYear();
                            }

                            $ca->save();
                        }
                    }
                }


            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            DB::rollback();

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }

    public function sendMessages(){
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);
            DB::beginTransaction();

            $campaigns = SmsLog::where('sms_status','Scheduled')->where(function ($query){
                return $query->where('schedule_time','<=',date('Y-m-d H:i'))->orWhere('schedule_time');
            })->get();


            foreach($campaigns as $ca){
                $business_id = $ca->business_id;

                $business = Business::where('id', $business_id)->first();
                $sms_settings = $business->sms_settings;

                if(!empty($sms_settings)){
                    $data = [
                        'sms_settings' => $sms_settings,
                        'mobile_number' => $ca->recipient,
                        'sms_body' => $ca->message
                    ];


                    $this->transactionUtil->superadminTransactionalSms($data);

                    $ca->sms_status = 'Delivered';
                    $ca->save();
                }


            }

            DB::commit();

            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            DB::rollback();

            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return $output;
    }



    public function quickSend(){
        $business_id = request()->session()->get('business.id');
        $smsSettings = Business::where('id', $business_id)->value('sms_settings');
        return view('sms::send_sms.quick_send')->with(compact('smsSettings'));
    }

    public function submitQuickSend(Request $request){
        $business_id = request()->session()->get('business.id');
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            DB::beginTransaction();
            $input = $request->except('_token');

            $no_of_sms = $this->transactionUtil->__getNumberOfSms($input['message']);
            $unit_cost = $this->transactionUtil->__businessSMSUnitCost($business_id);

            $date = date('Y-m-d');
            $balance = $this->transactionUtil->__getSMSBalance($date, $business_id, 'business');
            $total_cost = $no_of_sms * $unit_cost * sizeof(explode(',',$input['phone_nos']));

            if($total_cost > $balance){

                $output = [
                    'success' => false,
                    'msg' => __('sms::lang.insuffucient_balance')
                ];

                 return redirect()->back()->with('status', $output);
            }

            $correct_phones = $this->transactionUtil->validateNos($input['phone_nos'])['valid'];
            $incorrect_phones = $this->transactionUtil->validateNos($input['phone_nos'])['invalid'];

            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

            if(!empty($sms_settings)){

                if(!empty($incorrect_phones)){
                    foreach($incorrect_phones as $ph){
                        $sms_log = array(
                            'business_id' => $business_id,
                            'message' => $input['message'],
                            'no_of_characters' => strlen($input['message']),
                            'no_of_sms' => $no_of_sms,
                            'sms_type' => 'Quick Send',
                            'unit_cost' => $unit_cost,
                            'sms_type_' => $this->transactionUtil->__smsType($input['message']),
                            'total_cost' => $no_of_sms * $unit_cost * 1,
                            'sms_status' => 'Delivered',
                            'business_type' => 'business',
                            'username' => auth()->user()->username,
                            'default_gateway' => $sms_settings['default_gateway']
                        );

                        $sms_log['recipient'] = $ph;
                        $sms_log['uuid'] = rand(11111111111,99999999999);
                        $sms_log['sender_name'] = $sms_settings['ultimate_sender_id'];

                        SmsLog::create($sms_log);
                    }
                }

                if(!empty(!empty($correct_phones))){
                    $data = [
                    'sms_settings' => $sms_settings,
                        'mobile_number' => implode(',',$correct_phones),
                        'sms_body' => $input['message']
                    ];

                    $this->transactionUtil->sendSms($data,'Quick Send');
                }

            }


            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()->back()->with('status', $output);
    }

    public function submitSmsFileFinal(Request $request){
        $business_id = request()->session()->get('business.id');
        try {
            ini_set('max_execution_time', 0);
            ini_set('memory_limit', -1);

            DB::beginTransaction();
            $input = $request->except('_token');
            $input['data'] = json_decode($input['data'],true);

            $no_of_sms = $this->transactionUtil->__getNumberOfSms($input['message']);
            $unit_cost = $this->transactionUtil->__businessSMSUnitCost($business_id);

            $date = date('Y-m-d');
            $balance = $this->transactionUtil->__getSMSBalance($date, $business_id, 'business');
            $total_cost = $no_of_sms * $unit_cost * sizeof($input['data']['imported_data']);

            if($total_cost > $balance){

                $output = [
                    'success' => false,
                    'msg' => __('sms::lang.insuffucient_balance')
                ];

                 return redirect('/smsmodule/sms-from-file')->with('status', $output);
            }


            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;

            if(!empty($sms_settings)){

                foreach($input['data']['imported_data'] as $ca){
                    $msg = $input['message'];

                    $correct_phones = $this->transactionUtil->validateNos($ca[0])['valid'];
                    $incorrect_phones = $this->transactionUtil->validateNos($ca[0])['invalid'];

                    $i=0;
                    foreach($input['data']['tags'] as $key => $tag){
                        $msg = str_replace($key,$ca[$i],$msg);
                        $i++;
                    }

                    $no_of_sms = $this->transactionUtil->__getNumberOfSms($msg);
                    $sms_log = array(
                        'business_id' => $business_id,
                        'message' => $msg,
                        'no_of_characters' => strlen($msg),
                        'no_of_sms' => $no_of_sms,
                        'sms_type' => $input['data']['name'],
                        'unit_cost' => $unit_cost,
                        'sms_type_' => $this->transactionUtil->__smsType($msg),
                        'total_cost' => $no_of_sms * $unit_cost * 1,
                        'sms_status' => 'Scheduled',
                        'schedule_time' => $input['data']['send_time'],
                        'business_type' => 'business',
                        'username' => auth()->user()->username,
                        'default_gateway' => $sms_settings['default_gateway'],
                        'uuid' => rand(11111111111,99999999999),
                        'sender_name' => $sms_settings['ultimate_sender_id']
                    );

                    if(!empty($incorrect_phones)){
                        foreach($incorrect_phones as $ph){
                            $sms_log['sms_status'] = 'Delivered';
                            $sms_log['recipient'] = $ph;

                        }
                    }

                    if(!empty($correct_phones)){
                        foreach($correct_phones as $ph){
                            $sms_log['recipient'] = $ph;

                        }
                    }

                    SmsLog::create($sms_log);

                }
            }


            DB::commit();
            $output = [
                'success' => true,
                'msg' => __('lang_v1.success')
            ];
        } catch (\Exception $e) {
            DB::rollback();
            Log::emergency('File: ' . $e->getFile() . 'Line: ' . $e->getLine() . 'Message: ' . $e->getMessage());
            $output = [
                'success' => false,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect('/smsmodule/sms-from-file')->with('status', $output);
    }


}
