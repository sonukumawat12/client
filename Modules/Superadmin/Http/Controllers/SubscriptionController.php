<?php

namespace Modules\Superadmin\Http\Controllers;

use \Notification;
use App\Business;
use App\CompanyPackageVariable;
use App\Currency;
use App\PackageVariable;
use App\System;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Modules\Superadmin\Entities\Package;

use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Notifications\SubscriptionOfflinePaymentActivationConfirmation;

use Pesapal;
use Razorpay\Api\Api;
use Srmklive\PayPal\Services\ExpressCheckout;
use Stripe\Charge;

use Stripe\Customer;
use Stripe\Stripe;

use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Str;
use Modules\Superadmin\Entities\GiveAwayGift;
use Modules\Superadmin\Notifications\NewSubscriptionNotification;

class SubscriptionController extends BaseController
{
    protected $provider;
    protected $businessUtil;

    public function __construct(ModuleUtil $moduleUtil = null, BusinessUtil $businessUtil)
    {
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }

        if (!defined('CURLOPT_SSLVERSION')) {
            define('CURLOPT_SSLVERSION', 6);
        }

        $this->moduleUtil = $moduleUtil;
        $this->businessUtil = $businessUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('subscribe')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        //Get active subscription and upcoming subscriptions.
        $active = Subscription::active_subscription($business_id);

        $nexts = Subscription::upcoming_subscriptions($business_id);
        $waiting = Subscription::waiting_approval($business_id);
        $is_business_pacakge = 1;
        $packages = Package::active()->get();
        if ($packages->count() == 0) {
            $query = Package::active()->orderby('sort_order');
            if (request()->session()->get('business.is_patient')) {
                $query->whereJsonContains('hospital_business_type', 'patient');
            } else {
                $query->whereJsonDoesntContain('hospital_business_type', 'patient');
            }
            $packages = $query->get();
            $is_business_pacakge = 1;
        }

        //Get all module permissions and convert them into name => label
        $permissions = $this->moduleUtil->getModuleData('superadmin_package');
        $permission_formatted = [];
        foreach ($permissions as $permission) {
            foreach ($permission as $details) {
                $permission_formatted[$details['name']] = $details['label'];
            }
        }

        $intervals = ['days' => __('lang_v1.days'), 'months' => __('lang_v1.months'), 'years' => __('lang_v1.years')];

        return view('superadmin::subscription.index')
            ->with(compact('packages', 'active', 'nexts', 'waiting', 'permission_formatted', 'intervals', 'is_business_pacakge'));
    }
    
    
    public function getModules()
    {
        return array("Manufacturing Module",
                    "Accounting Module",
                    "Access Module",
                    "HR Module",
                    "Visitors Registration Module",
                    "Petro Module",
                    "Repair Module",
                    "Fleet Module",
                    "Mpcs Module",
                    "Backup Module",
                    "Property Module",
                    "Auto Repair Module",
                    "Contact Module",
                    "Ran Module",
                    "Report Module",
                    "Settings Module",
                    "User Management Module",
                    "Banking Module",
                    "Sale Module",
                    "Leads Module",
                    
                    "Hospital System",
                    "Enable Restaurant",
                    "Enable Duplicate Invoice",
                    "Tasks Management",
                    "Enable Cheque Writing",
                    "List Easy Payment",
                    "Pump Operator Dashboard",
                    "Stock Taking Module"
                    );
    }
    
    public function getModulePriceKeys(){
        return array(
                'mf_price',
                'ac_price',
                'access_module_price',
                'hr_price',
                'vreg_price',
                'petro_price',
                'repair_price',
                'fleet_price',
                'mpcs_price',
                'backup_price',
                'property_price',
                'auto_price',
                'contact_price',
                'ran_price',
                'report_price',
                'settings_price',
                'um_price',
                'banking_price',
                'sale_price',
                'leads_price',
                
                'hospital_price',
                'restaurant_price',
                'duplicate_invoice_price',
                'tasks_price',
                'cheque_price',
                'list_easy_price',
                'pump_price',
                'stock_taking_price'
                
                
            );
    }
    
    public function getModuleExpiryKeys(){
        return array(
                'mf_expiry_date',
                'ac_expiry_date',
                'access_module_expiry_date',
                'hr_expiry_date',
                'vreg_expiry_date',
                'petro_expiry_date',
                'repair_expiry_date',
                'fleet_expiry_date',
                'mpcs_expiry_date',
                'backup_expiry_date',
                'property_expiry_date',
                'auto_expiry_date',
                'contact_expiry_date',
                'ran_expiry_date',
                'report_expiry_date',
                'settings_expiry_date',
                'um_expiry_date',
                'banking_expiry_date',
                'sale_expiry_date',
                'leads_expiry_date',
                
                
                'hospital_expiry_date',
                'restaurant_expiry_date',
                'duplicate_invoice_expiry_date',
                'tasks_expiry_date',
                'cheque_expiry_date',
                'list_easy_expiry_date',
                'pump_expiry_date',
                'stock_taking_pexpiry_date'
            );
    }
    
    public function getModuleActivatedKeys(){
        return array(
                'mf_activated_on',
                'ac_activated_on',
                'access_module_activated_on',
                'hr_activated_on',
                'vreg_activated_on',
                'petro_activated_on',
                'repair_activated_on',
                'fleet_activated_on',
                'mpcs_activated_on',
                'backup_activated_on',
                'property_activated_on',
                'auto_activated_on',
                'contact_activated_on',
                'ran_activated_on',
                'report_activated_on',
                'settings_activated_on',
                'um_activated_on',
                'banking_activated_on',
                'sale_activated_on',
                'leads_activated_on',
                
                'hospital_activated_on',
                'restaurant_activated_on',
                'duplicate_invoice_activated_on',
                'tasks_activated_on',
                'cheque_activated_on',
                'list_easy_activated_on',
                'pump_activated_on',
                'stock_taking_activated_on'
            );
    }
    
    public function getModuleSubscription()
    {
        
        if (request()->ajax()) {
            $business_id = request()->business_id;
            $business = Business::where('id', $business_id)->first();
            $subscription = Subscription::active_subscription( $business_id);
            $start_date = request()->start_date;
            $end_date = request()->end_date;
            $statusi = request()->status;
            $expires_on = request()->expired_on;
            $module_name = request()->module_name;
            
            $modules = $this->getModules();
            $pricekeys = $this->getModulePriceKeys();
            $expirykeys = $this->getModuleExpiryKeys();
            $activatedkeys = $this->getModuleActivatedKeys();
            
            $module_subscriptions = array();
            $filtered_module_subs = array();
            
            if(!empty($subscription)){
                $activation_details = json_decode($subscription->module_activation_details,true);
                $i=0;
                foreach($modules as $one){
                    $status = "";
                    $activated_on = !empty($activation_details[$activatedkeys[$i]]) ? $activation_details[$activatedkeys[$i]] : null;
                    $expired_on = !empty($activation_details[$expirykeys[$i]]) ? $activation_details[$expirykeys[$i]] : null;
                    $price = !empty($activation_details[$pricekeys[$i]]) ? $activation_details[$pricekeys[$i]] : null;
                    logger($activation_details[$pricekeys[$i]]);
                    if(!empty($activated_on)){
                        if(strtotime($expired_on) > time()){
                            $status = "active";
                        }else{
                            $status = "expired";
                        }
                    }
                    $module_subscriptions[] = array(
                        "name" => $one,
                        "business" => $business->name,
                        "status" => $status,
                        "activated_on" => $activated_on,
                        "expired_on" => $expired_on,
                        "price" => $price
                    );
                    $i++;
                }
                
                
            
            }
            
            
            
            if(!empty($module_subscriptions)){
               
                $filtered_module_subs = array_filter($module_subscriptions, function($item) use ($start_date,$end_date,$statusi,$expires_on,$module_name) {
                        
                        if (!empty($statusi)) {
                            if (!isset($item['status']) || $item['status'] != $statusi) {
                                return false;
                            }
                        }
                    
                        if (!empty($module_name)) {
                            if (!isset($item['name']) || $item['name'] != $module_name) {
                                return false;
                            }
                        }
                        
                        if (!empty($expires_on)) {
                            if (!isset($item['expired_on']) || strtotime($item['expired_on']) > strtotime($expires_on)) {
                                return false;
                            }
                        }
                        
                        
                        if (!empty($start_date) && !empty($end_date)) {
                            
                            if (!isset($item['expired_on']) || !isset($item['activated_on']) || strtotime($item['expired_on']) > strtotime($end_date) || strtotime($item['activated_on']) < strtotime($start_date)) {
                                return false;
                            }
                        }
                    
                        return true;
                    });
                
            }else{
                $filtered_module_subs = $module_subscriptions;
            }
            
            return DataTables::of($filtered_module_subs)
                ->editColumn('status', function($row) {
                    
                    if($row["status"] == "expired"){
                        return '<span class="label bg-red">expired
                                </span>';
                    }elseif($row['status'] == "active"){
                        return '<span class="label bg-light-green">active
                                </span>';
                    }
                    
                })
                ->rawColumns(['status'])
                ->make(true);
        }
    }

    /**
     * Show pay form for a new package.
     * @return Response
     */
    public function pay($package_id, $form_register = null)
    {
        if (!auth()->user()->can('subscribe')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $custom_price = null;
            if (!empty(request()->custom_price)) {
                $custom_price = request()->custom_price;
            }
            $option_variables_selected = null;
            $option_variables = null;
            $module_selected = null;
            if (!empty(request()->option_variables_selected)) {
                $option_variables_selected = request()->option_variables_selected;  //if option variables selected by user
                $option_variables = json_decode(request()->option_variables_selected);  //if option variables selected by user
            }
            if (!empty(request()->module_selected)) {
                $module_selected = request()->module_selected;  //if moudle selected by user
            }

            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');
            $register_success = session('register_success');

            $package = Package::active()->find($package_id);
            $is_company_pacakge = 0;
            if (!empty($package->only_for_business)) {
                $is_company_pacakge = 1;
            }

            $options['locations'] = 0;
            $options['users'] = 0;
            $options['products'] = 0;
            if (!empty($option_variables)) {
                $options['locations'] =  $this->getoption_counts(0, $option_variables, $is_company_pacakge);
                $options['users'] =  $this->getoption_counts(1, $option_variables, $is_company_pacakge);
                $options['products'] =  $this->getoption_counts(2, $option_variables, $is_company_pacakge);
            }

            //Check if superadmin only package
            if ($package->is_private == 1 && !auth()->user()->can('superadmin')) {
                $output = ['success' => 0, 'msg' => __('superadmin::lang.not_allowed_for_package')];
                return redirect()
                    ->back()
                    ->with('status', $output);
            }

            //Check if one time only package
            if (empty($form_register) && $package->is_one_time) {
                $count_subcriptions = Subscription::where('business_id', $business_id)
                    ->where('package_id', $package_id)
                    ->count();

                if ($count_subcriptions > 0) {
                    $output = ['success' => 0, 'msg' => __('superadmin::lang.maximum_subscription_limit_exceed')];
                    return redirect()
                        ->back()
                        ->with('status', $output);
                }
            }
            $trial_used = Business::where('id', $business_id)->first()->trial_used;
            //Check for free package & subscribe it.
            if ($package->price == 0  || ($package->trial_days > 0 && $trial_used == 0)) {
                $gateway = null;
                $payment_transaction_id = 'FREE';
                if ($package->trial_days > 0) {
                    $payment_transaction_id = 'TRIAL';
                }
                $user_id = request()->session()->get('user.id');
                $this->_add_subscription($business_id, $package, $gateway, $payment_transaction_id, $user_id, 0, false, request()->option_variables_selected, request()->module_selected);

                DB::commit();

                if ($package->trial_days > 0 && $trial_used == 0) {
                    $output = ['success' => 1, 'msg' => __('lang_v1.success')];
                    return redirect()->action('HomeController@index')->with('register_success', $register_success);
                }
                if (empty($form_register)) {
                    $output = ['success' => 1, 'msg' => __('lang_v1.success')];
                    return redirect()
                        ->action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index')
                        ->with('status', $output);
                } else {
                    $output = ['success' => 1, 'msg' => __('superadmin::lang.registered_and_subscribed')];
                    return redirect()
                        ->action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index')
                        ->with('status', $output);
                }
            }

            $gateways = $this->_payment_gateways();

            $system_currency = System::getCurrency();

            $currency_code = !empty($package->currency_id) ? Currency::where('id', $package->currency_id)->first()->code : 'LKR';

            $order_id = Str::random(5);

            DB::commit();

            if (empty($form_register)) {
                $layout = 'layouts.app';
            } else {
                $layout = 'layouts.auth';
            }

            $user = request()->session()->get('user');
            $currencies = $this->businessUtil->allCurrencies();
            $timezone_list = $this->businessUtil->allTimeZones();

            $months = [];
            for ($i = 1; $i <= 12; $i++) {
                $months[$i] = __('business.months.' . $i);
            }

            $accounting_methods = $this->businessUtil->allAccountingMethods();
            $package_id = request()->package;
            $packages = Package::orderby('sort_order', 'asc')
                ->paginate();
            $system_settings = System::getProperties(['superadmin_enable_register_tc', 'superadmin_register_tc'], true);
            $show_give_away_gift_in_register_page = !empty(System::getProperty('show_give_away_gift_in_register_page')) ? System::getProperty('show_give_away_gift_in_register_page') : [];
            $show_referrals_in_register_page = !empty(System::getProperty('show_referrals_in_register_page')) ? System::getProperty('show_referrals_in_register_page') : [];
            $show_give_away_gift_in_register_page = json_decode($show_give_away_gift_in_register_page, true);
            $show_referrals_in_register_page = json_decode($show_referrals_in_register_page, true);
            $give_away_gifts_array = GiveAwayGift::pluck('name', 'id')->toArray();

            $give_away_gifts['all'] = 'All';
            foreach ($give_away_gifts_array as $key => $value) {
                $give_away_gifts[$key] = $value;
            }

            return view('superadmin::subscription.pay')
                ->with(compact(
                    'package',
                    'gateways',
                    'system_currency',
                    'layout',
                    'user',
                    'currency_code',
                    'order_id',
                    'custom_price',
                    'option_variables_selected',
                    'options',
                    'module_selected',
                    'currencies',
                    'timezone_list',
                    'months',
                    'accounting_methods',
                    'package_id',
                    'packages',
                    'system_settings',
                    'show_give_away_gift_in_register_page',
                    'show_referrals_in_register_page',
                    'give_away_gifts',
                    'register_success',
                    'form_register'
                ));
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());

            $output = ['success' => 0, 'msg' => "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage()];

            return redirect()
                ->action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index')
                ->with('status', $output);
        }
    }

    function getoption_counts($option, $ov, $is_company_pacakge)
    {
        if ($is_company_pacakge == 1) {
            foreach ($ov as $opt_var) {
                $value = CompanyPackageVariable::where('id', $opt_var)->first();
                if ($value->variable_options == $option) {
                    return $value->option_value;
                }
            }
        } else {
            foreach ($ov as $opt_var) {
                $value = PackageVariable::where('id', $opt_var)->first();
                if ($value->variable_options == $option) {
                    return $value->option_value;
                }
            }
        }

        return null;
    }

    /**
     * Show pay form for a new package.
     * @return Response
     */
    public function registerPay($package_id)
    {
        return $this->pay($package_id, 1);
    }

    /**
     * Save the payment details and add subscription details
     * @return Response
     */
    public function confirm($package_id, Request $request)
    {
        if (!auth()->user()->can('subscribe')) {
            abort(403, 'Unauthorized action.');
        }

        try {

            //Disable in demo
            if (config('app.env') == 'demo') {
                $output = [
                    'success' => 0,
                    'msg' => 'Feature disabled in demo!!'
                ];
                return back()->with('status', $output);
            }

            //Confirm for pesapal payment gateway
            if (isset($this->_payment_gateways()['pesapal']) && (strpos($request->merchant_reference, 'PESAPAL') !== false)) {
                return $this->confirm_pesapal($package_id, $request);
            }

            DB::beginTransaction();

            $business_id = request()->session()->get('user.business_id');
            $business_name = request()->session()->get('business.name');
            $user_id = request()->session()->get('user.id');
            $package = Package::active()->find($package_id);
            $price = request()->custom_price ?? $package->price;
            //Call the payment method
            $pay_function = 'pay_' . request()->gateway;
            $payment_transaction_id = null;
            if (method_exists($this, $pay_function)) {
                if ($pay_function == 'pay_offline' || $pay_function == 'pay_online') {
                    $payment_transaction_id = $this->$pay_function($business_id, $business_name, $package, $request, $price);
                } else {
                    $payment_transaction_id = $this->$pay_function($business_id, $business_name, $package, $request);
                }
            }

            //Add subscription details after payment is succesful
            $this->_add_subscription($business_id, $package, request()->gateway, $payment_transaction_id, $user_id, $price, false, request()->option_variables_selected, request()->module_selected);
            DB::commit();

            $business = Business::find($business_id);
            $user = Auth::user();
            $system_url = '<a href=' . env('APP_URL') . '>' . env('APP_URL') . '</a>';
            if(request()->gateway == 'offline' || request()->gateway == 'online') {
                $title = System::getProperty('subscription_message_offline_success_title');
                $msg = System::getProperty('subscription_message_offline_success_msg');
            }else{
                $title = System::getProperty('subscription_message_online_success_title');
                $msg = System::getProperty('subscription_message_online_success_msg');
            }

            $msg = str_replace('{business_name}', $business->name ?: 'SYZYGY', $msg);
            $msg = str_replace('{username}', $user->username, $msg);
            $msg = str_replace('{first_name}', $user->first_name, $msg);
            $msg = str_replace('{last_name}', $user->last_name, $msg);
            $msg = str_replace('{system_url}', $system_url, $msg);
            $msg = str_replace('{package_name}', $package->name, $msg);
            $msg = str_replace('{system_email}', System::getProperty('email'), $msg);

            if (request()->gateway == 'offline' || request()->gateway == 'online') {
                $output = [
                    'success' => 1,
                    'title' => $title,
                    'msg' => $msg
                ];
            }
        } catch (\Exception $e) {
            DB::rollBack();

            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            echo "File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage();
            exit;
            $output = ['success' => 0, 'msg' => $e->getMessage()];
        }

        if (request()->ajax()) {
            $output = [
                'success' => 1,
                'title' => $title,
                'msg' => $msg
            ];
            return $output;
        }

        Session::flash('subscription', $output);
        return redirect()->action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index');
    }

    /**
     * Confirm for pesapal gateway
     * when payment gateway is PesaPal payment gateway request package_id
     * is transaction_id & merchant_reference in session contains
     * the package_id.
     *
     * @return Response
     */
    protected function confirm_pesapal($transaction_id, $request)
    {
        $merchant_reference = $request->merchant_reference;
        $pesapal_session = $request->session()->pull('pesapal');

        if ($pesapal_session['ref'] == $merchant_reference) {
            $package_id = $pesapal_session['package_id'];

            $business_id = request()->session()->get('user.business_id');
            $business_name = request()->session()->get('business.name');
            $user_id = request()->session()->get('user.id');
            $package = Package::active()->find($package_id);

            $this->_add_subscription($business_id, $package, 'pesapal', $transaction_id, $user_id, $package->price);
            $output = ['success' => 1, 'msg' => __('superadmin::lang.waiting_for_confirmation')];

            return redirect()
                ->action('\Modules\Superadmin\Http\Controllers\SubscriptionController@index')
                ->with('status', $output);
        }
    }

    /**
     * Stripe payment method
     * @return Response
     */
    protected function pay_stripe($business_id, $business_name, $package, $request)
    {
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        $metadata = ['business_id' => $business_id, 'business_name' => $business_name, 'stripe_email' => $request->stripeEmail, 'package_name' => $package->name];
        // $customer = Customer::create(array(
        //     'email' => $request->stripeEmail,
        //     'source'  => $request->stripeToken,
        //     'metadata' => $metadata
        // ));

        $system_currency = System::getCurrency();

        $charge = Charge::create([
            'amount'   => $package->price * 100,
            'currency' => strtolower($system_currency->code),
            "source" => $request->stripeToken,
            //'customer' => $customer
            'metadata' => $metadata
        ]);

        return $charge->id;
    }

    /**
     * Offline payment method
     * @return Response
     */
    protected function pay_offline($business_id, $business_name, $package, $request, $price)
    {
        //Disable in demo
        if (config('app.env') == 'demo') {
            $output = [
                'success' => 0,
                'msg' => 'Feature disabled in demo!!'
            ];
            return back()->with('status', $output);
        }

        //Send notification
        $email = System::getProperty('email');
        $business = Business::find($business_id);

        if (!$this->moduleUtil->IsMailConfigured()) {
            return null;
        }
        $system_currency = System::getCurrency();
        $package->price = $system_currency->symbol . number_format($price, 2, $system_currency->decimal_separator, $system_currency->thousand_separator);

        Notification::route('mail', $email)
            ->notify(new SubscriptionOfflinePaymentActivationConfirmation($business, $package));

        return null;
    }

    /**
     * Offline payment method
     * @return Response
     */
    protected function pay_online($business_id, $business_name, $package, $request, $price)
    {
        //Disable in demo
        if (config('app.env') == 'demo') {
            $output = [
                'success' => 0,
                'msg' => 'Feature disabled in demo!!'
            ];
            return back()->with('status', $output);
        }

        //Send notification
        $email = System::getProperty('email');
        $business = Business::find($business_id);

        if (!$this->moduleUtil->IsMailConfigured()) {
            return null;
        }
        $system_currency = System::getCurrency();
        $package->price = $system_currency->symbol . number_format($price, 2, $system_currency->decimal_separator, $system_currency->thousand_separator);

        Notification::route('mail', $email)
            ->notify(new SubscriptionOfflinePaymentActivationConfirmation($business, $package));

        return null;
    }

    /**
     * Paypal payment method
     * @return Response
     */
    protected function pay_paypal($business_id, $business_name, $package, $request)
    {
        //Set config to use the currency
        $system_currency = System::getCurrency();
        $provider = new ExpressCheckout();
        config(['paypal.currency' => $system_currency->code]);

        $provider = new ExpressCheckout();
        $response = $provider->getExpressCheckoutDetails($request->token);

        $token = $request->get('token');
        $PayerID = $request->get('PayerID');
        $invoice_id = $response['INVNUM'];

        // if response ACK value is not SUCCESS or SUCCESSWITHWARNING we return back with error
        if (!in_array(strtoupper($response['ACK']), ['SUCCESS', 'SUCCESSWITHWARNING'])) {
            return back()
                ->with('status', ['success' => 0, 'msg' => 'Something went wrong with paypal transaction']);
        }

        $data = [];
        $data['items'] = [
            [
                'name' => $package->name,
                'price' => (float) $package->price,
                'qty' => 1
            ]
        ];
        $data['invoice_id'] = $invoice_id;
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $data['return_url'] = action('\Modules\Superadmin\Http\Controllers\SubscriptionController@confirm', [$package->id]);
        $data['cancel_url'] = action('\Modules\Superadmin\Http\Controllers\SubscriptionController@pay', [$package->id]);
        $data['total'] = (float) $package->price;

        // if payment is not recurring just perform transaction on PayPal and get the payment status
        $payment_status = $provider->doExpressCheckoutPayment($data, $token, $PayerID);
        $status = isset($payment_status['PAYMENTINFO_0_PAYMENTSTATUS']) ? $payment_status['PAYMENTINFO_0_PAYMENTSTATUS'] : null;

        if (!empty($status) && $status != 'Invalid') {
            return $invoice_id;
        } else {
            $error = 'Something went wrong with paypal transaction';
            throw new \Exception($error);
        }
    }

    /**
     * Paypal payment method - redirect to paypal url for payments
     *
     * @return Response
     */
    public function paypalExpressCheckout(Request $request, $package_id)
    {

        //Disable in demo
        if (config('app.env') == 'demo') {
            $output = [
                'success' => 0,
                'msg' => 'Feature disabled in demo!!'
            ];
            return back()->with('status', $output);
        }

        // Get the cart data or package details.
        $package = Package::active()->find($package_id);

        $data = [];
        $data['items'] = [
            [
                'name' => $package->name,
                'price' => (float) $package->price,
                'qty' => 1
            ]
        ];
        $data['invoice_id'] = str_random(5);
        $data['invoice_description'] = "Order #{$data['invoice_id']} Invoice";
        $data['return_url'] = action('\Modules\Superadmin\Http\Controllers\SubscriptionController@confirm', [$package_id]) . '?gateway=paypal';
        $data['cancel_url'] = action('\Modules\Superadmin\Http\Controllers\SubscriptionController@pay', [$package_id]);
        $data['total'] = (float) $package->price;

        // send a request to paypal
        // paypal should respond with an array of data
        // the array should contain a link to paypal's payment system
        $system_currency = System::getCurrency();
        $provider = new ExpressCheckout();
        $response = $provider->setCurrency(strtoupper($system_currency->code))->setExpressCheckout($data);

        // if there is no link redirect back with error message
        if (!$response['paypal_link']) {
            return back()
                ->with('status', ['success' => 0, 'msg' => 'Something went wrong with paypal transaction']);
            //For the actual error message dump out $response and see what's in there
        }

        // redirect to paypal
        // after payment is done paypal
        // will redirect us back to $this->expressCheckoutSuccess
        return redirect($response['paypal_link']);
    }

    /**
     * Razor pay payment method
     * @return Response
     */
    protected function pay_razorpay($business_id, $business_name, $package, $request)
    {
        $razorpay_payment_id = $request->razorpay_payment_id;
        $razorpay_api = new Api(env('RAZORPAY_KEY_ID'), env('RAZORPAY_KEY_SECRET'));

        $payment = $razorpay_api->payment->fetch($razorpay_payment_id)->capture(['amount' => $package->price * 100]); // Captures a payment

        if (empty($payment->error_code)) {
            return $payment->id;
        } else {
            $error_description = $payment->error_description;
            throw new \Exception($error_description);
        }
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show($id)
    {
        if (!auth()->user()->can('subscribe')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscription = Subscription::where('business_id', $business_id)
            ->with(['package', 'created_user', 'business'])
            ->find($id);

        $system_settings = System::getProperties([
            'invoice_business_name',
            'email',
            'invoice_business_landmark',
            'invoice_business_city',
            'invoice_business_zip',
            'invoice_business_state',
            'invoice_business_country'
        ]);
        $system = [];
        foreach ($system_settings as $setting) {
            $system[$setting['key']] = $setting['value'];
        }

        return view('superadmin::subscription.show_subscription_modal')
            ->with(compact('subscription', 'system'));
    }

    /**
     * Retrieves list of all subscriptions for the current business
     *
     * @return \Illuminate\Http\Response
     */
    public function allSubscriptions()
    {
        if (!auth()->user()->can('subscribe')) {
            abort(403, 'Unauthorized action.');
        }

        $business_id = request()->session()->get('user.business_id');

        $subscriptions = Subscription::where('subscriptions.business_id', $business_id)
            ->leftjoin(
                'packages as P',
                'subscriptions.package_id',
                '=',
                'P.id'
            )
            ->leftjoin(
                'users as U',
                'subscriptions.created_id',
                '=',
                'U.id'
            )
            ->addSelect(
                'P.name as package_name',
                'P.currency_id',
                DB::raw("CONCAT(COALESCE(U.surname, ''), ' ', COALESCE(U.first_name, ''), ' ', COALESCE(U.last_name, '')) as created_by"),
                'subscriptions.*'
            );
        return Datatables::of($subscriptions)
            ->editColumn(
                'start_date',
                '@if(!empty($start_date)){{@format_date($start_date)}}@endif'
            )
            ->editColumn(
                'end_date',
                '@if(!empty($end_date)){{@format_date($end_date)}}@endif'
            )
            ->editColumn(
                'trial_end_date',
                '@if(!empty($trial_end_date)){{@format_date($trial_end_date)}}@endif'
            )
            ->editColumn(
                'package_price',
                function ($row) {
                    $currency_symbol = Currency::where('id', $row->currency_id)->first()->symbol;
                    $html =  '<span>' . $currency_symbol . ' ' . $row->package_price . '</span>';
                    return $html;
                }
            )
            ->editColumn(
                'created_at',
                '@if(!empty($created_at)){{@format_date($created_at)}}@endif'
            )
            ->filterColumn('created_by', function ($query, $keyword) {
                $query->whereRaw("CONCAT(COALESCE(U.surname, ''), ' ', COALESCE(U.first_name, ''), ' ', COALESCE(U.last_name, '')) like ?", ["%{$keyword}%"]);
            })
            ->addColumn('action', function ($row) {
                return '<button type="button" class="btn btn-primary btn-xs btn-modal" data-container=".view_modal" data-href="' . action("\Modules\Superadmin\Http\Controllers\SubscriptionController@show", $row->id) . '" ><i class="fa fa-eye" aria-hidden="true"></i> ' . __("messages.view") . '</button>';
            })
            ->rawColumns(['package_price', 'action'])
            ->make(true);
    }

    public function checkStatus($package_id, $gateway)
    {
        $business_id = request()->session()->get('user.business_id');
        $package = Package::find($package_id);

        $subscription = Subscription::where('business_id', $business_id)->where('paid_via', $gateway)->where('package_id', $package_id)->whereDate('created_at', date('Y-m-d'))->select('*')->orderBy('id', 'desc')->first();

        $business = Business::find($business_id);
        $system_url = '<a href=' . env('APP_URL') . '>' . env('APP_URL') . '</a>';
        $user = Auth::user();
        $title = System::getProperty('subscription_message_online_success_title');
        $msg = System::getProperty('subscription_message_online_success_msg');

        $msg = str_replace('{business_name}', $business->name, $msg);
        $msg = str_replace('{username}', $user->username, $msg);
        $msg = str_replace('{first_name}', $user->first_name, $msg);
        $msg = str_replace('{last_name}', $user->last_name, $msg);
        $msg = str_replace('{system_url}', $system_url, $msg);
        $msg = str_replace('{package_name}', $package->name, $msg);

        if ($subscription->status == 'waiting') {

            $email = System::getProperty('email');
            $is_notif_enabled = System::getProperty('enable_new_subscription_notification');

            if (!empty($email) && $is_notif_enabled == 1) {
                Notification::route('mail', $email)
                    ->notify(new NewSubscriptionNotification($subscription));
            }

            $output = [
                'success' => 1,
                'title' => $title,
                'msg' => $msg
            ];
            return $output;
        }

        return null;
    }
    public function payhereInitailData(Request $request)
    {
        $initial_data = array(
            'order_id' => $request->order_id,
            'business_id' => $request->business_id,
            'package_id' => $request->package_id,
            'transaction_id' => $request->package_id,
            'user_id' => $request->user_id,
            'price' => $request->price,
            'status' => 'pending',
        );

        DB::table('payhere')->insert($initial_data);

        return json_encode(['status' => 1]);
    }

    public function payhereNotify(Request $request)
    {
        $merchant_id         = $request->merchant_id;
        $order_id             = $request->order_id;
        $payhere_amount     = $request->payhere_amount;
        $payhere_currency    = $request->payhere_currency;
        $status_code         = $request->status_code;
        $md5sig                = $request->md5sig;

        $merchant_secret = env('PAYHERE_MERCHANT_SECRET'); // Replace with your Merchant Secret (Can be found on your PayHere account's Settings page)

        $local_md5sig = strtoupper(md5($merchant_id . $order_id . $payhere_amount . $payhere_currency . $status_code . strtoupper(md5($merchant_secret))));

        if (($local_md5sig === $md5sig) and ($status_code == 2)) {
            //TODO: Update your database as payment success
            $payment_data = DB::table('payhere')->where('order_id', $order_id)->first();
            $package_id = $payment_data->package_id;
            $payment_transaction_id = $payment_data->id;
            $user_id = $payment_data->user_id;
            $price = $payment_data->price;
            $business_id = $payment_data->business_id;
            $package = Package::active()->find($package_id);

            $payment_data_update['status_code'] = $status_code;
            $payment_data_update['status'] = 'completed';
            DB::table('payhere')->where('id',  $payment_data->id)->update($payment_data_update);

            $subscription = Subscription::where('business_id', $business_id)->orderBy('id', 'desc')->first();
            if (!empty($subscription)) {
                $dates = $this->_get_package_dates($business_id, $package);

                $subscription->start_date = $dates['start'];
                $subscription->end_date = $dates['end'];
                $subscription->trial_end_date = $dates['trial'];
                $subscription->status = 'approved';
                $subscription->payment_transaction_id = $payment_transaction_id;
                $subscription->save();
            } else {
                $subscription = $this->_add_subscription($business_id, $package, 'payhere', $payment_transaction_id, $user_id, $price);
            }

            $email = System::getProperty('email');
            $is_notif_enabled = System::getProperty('enable_new_subscription_notification');

            //send package added email notification to user
            if (!empty($email) && $is_notif_enabled == 1) {
                Notification::route('mail', $email)
                    ->notify(new NewSubscriptionNotification($subscription));
            }


            $subscription->save();
        } else {
            $payment_data = DB::table('payhere')->where('order_id', $order_id)->first();
            $package_id = $payment_data->package_id;
            $payment_transaction_id = $payment_data->id;
            $business_id = $payment_data->business_id;

            $payment_data_update['status_code'] = $status_code;
            $payment_data_update['status'] = 'declined';
            DB::table('payhere')->where('id',  $payment_data->id)->update($payment_data_update);


            $subscription = Subscription::where('business_id', $business_id)->orderBy('id', 'desc')->first();
            if (!empty($subscription)) {
                $subscription->status = 'declined';
                $subscription->payment_transaction_id = $payment_transaction_id;
                $subscription->save();
            }
        }
    }


    /**
     * get package variable of resource
     *
     * @return \Illuminate\Http\Response
     */

    public function getPackageVariables($package_id)
    {

        $package = Package::active()->find($package_id);
        $coantian_variables = !empty($package->option_variables) ? json_decode($package->option_variables) : [];

        $is_business_pacakge = null;
        $module_enable_price = [];
        $manage_module_enable = [];
        $module_price_total = 0;
        if ($package->only_for_business) {
            $is_business_pacakge = 1;
        }
        $number_of_branches = null;
        $number_of_users = null;
        $number_of_products = null;
        $number_of_periods = null;
        $number_of_customer = null;
        $number_of_stores = null;
        $monthly_total_sales = null;
        $no_of_family_members = null;
        $no_of_vehicles = null;
        if ($package->number_of_branches) {
            $number_of_branches = $this->getVariableByType(0, $is_business_pacakge, $coantian_variables);
        }
        if ($package->number_of_users) {
            $number_of_users =  $this->getVariableByType(1, $is_business_pacakge, $coantian_variables);
        }
        if ($package->number_of_products) {
            $number_of_products =  $this->getVariableByType(2, $is_business_pacakge, $coantian_variables);
        }
        if ($package->number_of_periods) {
            $number_of_periods =  $this->getVariableByType(3, $is_business_pacakge, $coantian_variables);
        }
        if ($package->number_of_customers) {
            $number_of_customer = $this->getVariableByType(4, $is_business_pacakge, $coantian_variables);
        }
        if ($package->monthly_total_sales) {
            $monthly_total_sales = $this->getVariableByType(5, $is_business_pacakge, $coantian_variables);
        }
        if ($package->no_of_family_members) {
            $no_of_family_members = $this->getVariableByType(6, $is_business_pacakge, $coantian_variables);
        }
        if ($package->no_of_vehicles) {
            $no_of_vehicles = $this->getVariableByType(7, $is_business_pacakge, $coantian_variables);
        }

        if ($is_business_pacakge) {
            $number_of_stores = $this->getVariableByType(5, $is_business_pacakge, $coantian_variables);
            $module_enable_price = json_decode($package->module_enable_price); //input values
            $manage_module_enable = json_decode($package->manage_module_enable); //checkbox
        }

        $currency_symbol = Currency::where('id', $package->currency_id)->first();


        return view('superadmin::subscription.partials.package_variables')->with(compact(
            'package',
            'number_of_branches',
            'number_of_users',
            'number_of_products',
            'number_of_periods',
            'number_of_customer',
            'number_of_stores',
            'monthly_total_sales',
            'no_of_family_members',
            'no_of_vehicles',
            'module_enable_price',
            'currency_symbol',
            'is_business_pacakge',
            'manage_module_enable'
        ));
    }

    public function getVariableByType($type, $is_business_pacakge, $coantian_variables)
    {
        $query = PackageVariable::where('variable_options', $type)->whereIn('id', $coantian_variables)->orderBy('option_value', 'asc');
        if ($is_business_pacakge) {
            $query->where('is_company_variable', 1);
        }
        return $query->get();
    }
}
