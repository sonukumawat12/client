<?php



namespace Modules\Airline\Http\Controllers;

use Illuminate\Routing\Controller;


use PDO;
use App\Unit;
use App\User;
use App\Agent;
use App\Store;
use App\System;
use App\Account;
use App\Product;
use App\TaxRate;
use App\Business;
use App\Currency;
use DateTimeZone;
use \Notification;
use App\AccountType;
use App\AccountGroup;
use App\ContactGroup;
use App\PatientDetail;
use App\DefaultAccount;
use App\BusinessCategory;
use App\BusinessLocation;
use App\Contact;
use App\Utils\ModuleUtil;
use App\DefaultAccountType;

use App\Utils\BusinessUtil;
use App\DefaultAccountGroup;
use Illuminate\Http\Request;
use App\Utils\RestaurantUtil;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\Validator;
use App\Notifications\TestEmailNotification;
use Modules\Airline\Entities\AirlineClasses;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Entities\HelpExplanation;
use Symfony\Component\CssSelector\Node\FunctionNode;
use Modules\Superadmin\Entities\ModulePermissionLocation;
use Modules\Superadmin\Notifications\NewBusinessWelcomNotification;

use Modules\Airline\Entities\AirlineCustomers;
use Modules\Airline\Entities\AirlineFormSettingCustomer;
use Modules\Airline\Entities\AirlineFormSettingSupplier;
use Modules\Airline\Entities\AirlineFormSettingPassenger;
use Modules\Airline\Entities\AirlinePrefixStarting;




class FormSettingsController extends Controller

{

    protected $businessUtil;
    protected $restaurantUtil;
    protected $moduleUtil;
    protected $mailDrivers;


    public function __construct(BusinessUtil $businessUtil, RestaurantUtil $restaurantUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
        $this->theme_colors = [
            'blue' => 'Blue',
            'black' => 'Black',
            'purple' => 'Purple',
            'green' => 'Green',
            'red' => 'Red',
            'yellow' => 'Yellow',
            'blue-light' => 'Blue Light',
            'black-light' => 'Black Light',
            'purple-light' => 'Purple Light',
            'green-light' => 'Green Light',
            'red-light' => 'Red Light',
        ];
        $this->mailDrivers = [
            'smtp' => 'SMTP',
            'sendmail' => 'Sendmail',
            'mailgun' => 'Mailgun',
            'mandrill' => 'Mandrill',
            'ses' => 'SES',
            'sparkpost' => 'Sparkpost'
        ];
    }

    public function index()

    {

        if (!auth()->user()->can('airline.view_setting')) {

            abort(403, 'Unauthorized action.');

        }

        //  $business_id = $request->session()->get('user.business_id');
        // $created_by = $request->session()->get('user.id'); 

        // dd(request()->session()->get('user.id'));

        
        if (!$this->moduleUtil->isSubscribed(request()->session()->get('business.id'))) {
            return $this->moduleUtil->expiredResponse();
        }
        $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);
        $timezone_list = [];
        foreach ($timezones as $timezone) {
            $timezone_list[$timezone] = $timezone;
        }
        $business_id = request()->session()->get('user.business_id');
        $business = Business::where('id', $business_id)->first();

        // dd($business_id, $business);

        //add by sakhawat
        $businessLocations = BusinessLocation::whereBusinessId($business_id)->pluck('name', 'id');
      
        $type = request()->type;
        $invoice_prefixes = AirlinePrefixStarting::orderBy('id', 'desc')->select('id', 'mode_id', 'value')->with(['mode' => function($query){

            $query->select('id', 'name');

        }])->get();
        $airline_class = AirlineClasses::orderBy('id', 'desc')->get();

        $airline_supplier= Contact::where('type', 'supplier')->where('register_module','airline')->pluck('name', 'id');
        $customers = Contact::customersDropdown($business_id, false, true, 'customer');

        return view('airline::form_settings.index',compact('business','businessLocations', 'type', 'invoice_prefixes','airline_supplier','customers','airline_class'));

    }



    public function updateCustomers(Request $request)
    {
     
    
        $business_id = request()->session()->get('user.business_id');
        $created_by = request()->session()->get('user.id'); 
    
        $airlineCustomer = AirlineFormSettingCustomer::where('business_id', $business_id)
            ->where('created_by', $created_by)
            ->first();
       
        // If record exists, update it, otherwise create a new record
        if ($airlineCustomer) {
            $airlineCustomer->update([
                'name' => $request->customer_name,
                'vat_no' => $request->vat_no,
                'credit_limit' => $request->customer_credit_limit,
                'mobile' => $request->customer_mobile,
                'address' => $request->customer_address,
                'state' => $request->customer_state,
                'tax_number' => $request->customer_tax_number,
                'confirm_password' => $request->customer_confirm_password,
                'sub_customer' => $request->sub_customer,
                'passport_nic_no' => $request->passport_nic_no,
                'need_to_send_sms' => $request->need_to_send_sms,
                'opening_balance' => $request->customer_opening_balance,
                'transaction_date' => $request->customer_transaction_date,
                'landline' => $request->customer_landline,
                'address_line_2' => $request->address_line_2,
                'country' => $request->customer_country,
                'pay_term' => $request->customer_pay_term,
                'email' => $request->customer_email,
                'vehicle_no' => $request->vehicle_no,
                'passport_nic_image' => $request->passport_nic_image,
                'credit_notification_type' => $request->credit_notification_type,
                'customer_group' => $request->customer_customer_group,
                'add_more_mobile_numbers' => $request->add_more_mobile,
                'assigned_to' => $request->assigned_to,
                'city' => $request->customer_city,
                'landmark' => $request->customer_landmark,
                'password' => $request->customer_password,
                'alternate_contact_number' => $request->customer_alternate_contact_number,
                'address_line_3' => $request->address_line_3,
                'signature' => $request->signature,
                // 'whatsapp_number' => $request->whatsapp_number,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $message = 'Customer form settings updated successfully!';
        } else {
            // Create a new record if no match is found
            AirlineFormSettingCustomer::create([
                'business_id' => $business_id,
                'created_by' => $created_by,
                'name' => $request->customer_name,
                'vat_no' => $request->vat_no,
                'credit_limit' => $request->customer_credit_limit,
                'mobile' => $request->customer_mobile,
                'address' => $request->customer_address,
                'state' => $request->customer_state,
                'tax_number' => $request->customer_tax_number,
                'confirm_password' => $request->customer_confirm_password,
                'sub_customer' => $request->sub_customer,
                'passport_nic_no' => $request->passport_nic_no,
                'need_to_send_sms' => $request->need_to_send_sms,
                'opening_balance' => $request->customer_opening_balance,
                'transaction_date' => $request->customer_transaction_date,
                'landline' => $request->customer_landline,
                'address_line_2' => $request->address_line_2,
                'country' => $request->customer_country,
                'pay_term' => $request->customer_pay_term,
                'email' => $request->customer_email,
                'vehicle_no' => $request->vehicle_no,
                'passport_nic_image' => $request->passport_nic_image,
                'credit_notification_type' => $request->credit_notification_type,
                'customer_group' => $request->customer_customer_group,
                'add_more_mobile_numbers' => $request->add_more_mobile,
                'assigned_to' => $request->assigned_to,
                'city' => $request->customer_city,
                'landmark' => $request->customer_landmark,
                'password' => $request->customer_password,
                'alternate_contact_number' => $request->customer_alternate_contact_number,
                'address_line_3' => $request->address_line_3,
                'signature' => $request->signature,
                // 'whatsapp_number' => $request->whatsapp_number,
                'created_at' => date('Y-m-d H:i:s'),

            ]);
            $message = 'Customer form settings inserted successfully!';
        }
 
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }
    




    function checkFormSettingCustomers() {

        $business_id = request()->session()->get('user.business_id');
        $created_by = request()->session()->get('user.id'); 
        Log::info($created_by);
        

        $updatedSettings = AirlineFormSettingCustomer::where('business_id', $business_id)
        ->where('created_by', $created_by)
        ->first();
Log::info("fjkdfjkdfjk".$updatedSettings);
        return response()->json([
            'status' => 'success',
            'message' => 'Customer form settings retrieved successfully!',
            'data' => $updatedSettings,
        ]);
    }
  
    
    public function updateSuppliers(Request $request)
    {
      
        $business_id = request()->session()->get('user.business_id');
        $created_by = request()->session()->get('user.id'); 
    
        $airlineCustomer = AirlineFormSettingSupplier::where('business_id', $business_id)
            ->where('created_by', $created_by)
            ->first();
    
        // If record exists, update it, otherwise create a new record
        if ($airlineCustomer) {
            $airlineCustomer->update([
                'type' => $request->type,
                'tax_number' => $request->tax_number,
                'transaction_date' => $request->transaction_date,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'country' => $request->country,
                'custom_field_1' => $request->custom_field_1,
                'custom_field_2' => $request->custom_field_2,
                'custom_field_3' => $request->custom_field_3,
                'custom_field_4' => $request->custom_field_4,
                'name' => $request->name,
                'opening_balance' => $request->opening_balance,
                'supplier_group' => $request->supplier_group,
                'alternate_contact_number' => $request->alternate_contact_number,
                'city' => $request->city,
                'landmark' => $request->landmark,
                'contact_id' => $request->contact_id,
                'pay_term' => $request->pay_term,
                'email' => $request->email,
                'landline' => $request->landline,
                'state' => $request->state,
                'created_at' => date('Y-m-d H:i:s'),
                
            ]);
            $message = 'Customer form settings updated successfully!';
        } else {
            AirlineFormSettingSupplier::create([
                'created_by' => $created_by,
                'business_id' => $business_id,
                'type' => $request->type,
                'tax_number' => $request->tax_number,
                'transaction_date' => $request->transaction_date,
                'mobile' => $request->mobile,
                'address' => $request->address,
                'country' => $request->country,
                'custom_field_1' => $request->custom_field_1,
                'custom_field_2' => $request->custom_field_2,
                'custom_field_3' => $request->custom_field_3,
                'custom_field_4' => $request->custom_field_4,
                'name' => $request->name,
                'opening_balance' => $request->opening_balance,
                'supplier_group' => $request->supplier_group,
                'alternate_contact_number' => $request->alternate_contact_number,
                'city' => $request->city,
                'landmark' => $request->landmark,
                'contact_id' => $request->contact_id,
                'pay_term' => $request->pay_term,
                'email' => $request->email,
                'landline' => $request->landline,
                'state' => $request->state,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $message = 'Customer form settings inserted successfully!';
        }
       
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }

    function checkFormSettingSuppliers() {
        $business_id = request()->session()->get('user.business_id');
        $created_by = request()->session()->get('user.id'); 

        $updatedSettings = AirlineFormSettingSupplier::where('business_id', $business_id)
        ->where('created_by', $created_by)
        ->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Customer form settings retrieved successfully!',
            'data' => $updatedSettings,
        ]);
    }
   

    public function updatePassengers(Request $request)
    {
        $business_id = request()->session()->get('user.business_id');
        $created_by = request()->session()->get('user.id'); 
    
        $airlineCustomer = AirlineFormSettingPassenger::where('business_id', $business_id)
            ->where('created_by', $created_by)
            ->first();
    
        if ($airlineCustomer) {
            $airlineCustomer->update([
                'name' => $request->passenger_name,
                'passenger_mobile_no' => $request->passenger_mobile_no,
                'frequent_flyer_no' => $request->frequent_flyer_no,
                'additional_service' => $request->additional_service,
                'passport_number' => $request->passport_number,
                'select_passport_image' => $request->select_passport_image,
                'child' => $request->child,
                'additional_service_amount' => $request->additional_service_amount,
                'vat_number' => $request->vat_number,
                'need_to_send_sms' => $request->need_to_send_sms,
                'price' => $request->price,
                'passenger_type' => $request->passenger_type,
                'updated_at' => date('Y-m-d H:i:s'),


            ]);
            $message = 'Customer form settings updated successfully!';
        } else {
            AirlineFormSettingPassenger::create([
                'created_by' => $created_by,
                'business_id' => $business_id,
                'name' => $request->passenger_name,
                'passenger_mobile_no' => $request->passenger_mobile_no,
                'frequent_flyer_no' => $request->frequent_flyer_no,
                'additional_service' => $request->additional_service,
                'passport_number' => $request->passport_number,
                'select_passport_image' => $request->select_passport_image,
                'child' => $request->child,
                'additional_service_amount' => $request->additional_service_amount,
                'vat_number' => $request->vat_number,
                'need_to_send_sms' => $request->need_to_send_sms,
                'price' => $request->price,
                'passenger_type' => $request->passenger_type,
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $message = 'Customer form settings inserted successfully!';
        }
       
        return response()->json([
            'status' => 'success',
            'message' => $message,
        ]);
    }
    function checkFormSettingPassengers() {
        $business_id = request()->session()->get('user.business_id');
        $created_by = request()->session()->get('user.id'); 

        $updatedSettings = AirlineFormSettingPassenger::where('business_id', $business_id)
        ->where('created_by', $created_by)
        ->first();

        return response()->json([
            'status' => 'success',
            'message' => 'Customer form settings retrieved successfully!',
            'data' => $updatedSettings,
        ]);
    }
 

   

}

