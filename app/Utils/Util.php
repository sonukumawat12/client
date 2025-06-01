<?php

namespace App\Utils;

use App\Account;
use App\Business;
use App\BusinessLocation;
use App\Product;
use App\ReferenceCount;
use App\System;
use App\Transaction;
use App\Utils\TransactionUtil;
use Modules\Hms\Entities\HmsBookingLine;
use Modules\Hms\Entities\HmsBookingExtra;
use Modules\Hms\Entities\HmsExtra;
use Carbon\Carbon;
use App\Utils\ModuleUtil;
use App\Utils\ContactUtil;
use App\TransactionSellLine;
use App\Unit;
use App\User;
use App\AccountGroup;
use App\AccountTransaction;
use App\VariationLocationDetails;
use DB;
use App\Utils\BusinessUtil;
use Intervention\Image\Facades\Image;
use GuzzleHttp\Client;
use Modules\Fleet\Entities\RouteInvoiceNumber;
use Modules\Fleet\Entities\RouteOperation;
use Modules\Petro\Entities\Pump;
use Modules\Superadmin\Entities\ModulePermissionLocation;
use Modules\Superadmin\Entities\Subscription;
use PhpParser\Node\Expr\FuncCall;
use Spatie\Permission\Models\Role;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

use Modules\Superadmin\Entities\RefillBusiness;
use App\SmsLog;

class Util
{
    /**
     * This function unformats a number and returns them in plain eng format
     *
     * @param int $input_number
     *
     * @return float
     */
    public function num_uf($input_number, $currency_details = null)
    {
        $thousand_separator  = '';
        $decimal_separator  = '';

        if (!empty($currency_details)) {
            $thousand_separator = $currency_details->thousand_separator;
            $decimal_separator = $currency_details->decimal_separator;
        } else {
            $thousand_separator = session()->has('currency') ? session('currency')['thousand_separator'] : '';
            $decimal_separator = session()->has('currency') ? session('currency')['decimal_separator'] : '';
        }

        $num = str_replace($thousand_separator, '', $input_number);
        $num = str_replace($decimal_separator, '.', $num);

        return (float)$num;
    }
    
    public function getDays()
    {
        return [
            'sunday' => __('lang_v1.sunday'),
            'monday' => __('lang_v1.monday'),
            'tuesday' => __('lang_v1.tuesday'),
            'wednesday' => __('lang_v1.wednesday'),
            'thursday' => __('lang_v1.thursday'),
            'friday' => __('lang_v1.friday'),
            'saturday' => __('lang_v1.saturday'),
        ];
    }
    
    public function checkCheques($cheque_no, $bank_name){
        $business_id = request()->session()->get('user.business_id');
        $cheque = DB::table('transaction_payments')
            ->where('business_id', '=', $business_id)
            ->where('cheque_number',$cheque_no)
            ->where('bank_name',$bank_name)
            ->whereNull('deleted_at')
            ->whereNull('deleted_by')
            ->count();
        return $cheque; 
    }
    
    public function get_reviewStatus($start_date,$end_date = "",$business_id = null,$is_petro = true){
        
        
        if (empty($business_id)) {
            $business_id = request()->session()->get('user.business_id');
        }
        
        if($is_petro){
            
            $reviewed = DB::table('daily_report_review_status')
            ->join('users', 'daily_report_review_status.reviewed_by', '=', 'users.id')
            ->select('daily_report_review_status.*', 'users.first_name')
            ->where('daily_report_review_status.status','=',1)
            ->whereDate('daily_report_review_status.reiew_date', '>=', date('Y-m-d',strtotime($start_date)))
            ->where('daily_report_review_status.business_id', '=', $business_id)
            ->first();
            
        }
        return $reviewed;
    }
    
    public function get_review($start_date,$end_date = "",$business_id = null,$is_petro = true){
        
        if (auth()->user()->can('bypass.review')) { return []; }
        
        if (empty($business_id)) {
            $business_id = request()->session()->get('user.business_id');
        }
        
        if($is_petro){
            
            $reviewed = DB::table('daily_report_review_status')
            ->join('users', 'daily_report_review_status.reviewed_by', '=', 'users.id')
            ->select('daily_report_review_status.*', 'users.first_name')
            ->where('daily_report_review_status.status','=',1)
            ->whereDate('daily_report_review_status.reiew_date', '>=', date('Y-m-d',strtotime($start_date)))
            ->where('daily_report_review_status.business_id', '=', $business_id)
            ->first();
            
        }
        return $reviewed;
    }
    
    public function getDropdownForRoles($business_id)
    {
        $app_roles = Role::where('business_id', $business_id)
            ->pluck('name', 'id');

        $roles = [];
        foreach ($app_roles as $key => $value) {
            $roles[$key] = str_replace("#" . $business_id, '', $value);
        }

        return $roles;
    }
    
    public function hasReviewed($date){
        
        // if (auth()->user()->can('bypass.review')) { return []; }
        
        
        $business_id = request()->session()->get('user.business_id');
        $subscription = Subscription::current_subscription($business_id);
    	$pacakge_details = $subscription->package_details;
    	
    	
    	if(!empty($pacakge_details['daily_review']) && $pacakge_details['daily_review'] == 1){
    	    if(date('Y-m-d',strtotime($date)) > date('Y-m-d')){
                $business_id = request()->session()->get('user.business_id');
            
                $reviewed = DB::table('daily_report_review_status')
                    ->join('users', 'daily_report_review_status.reviewed_by', '=', 'users.id')
                    ->select('daily_report_review_status.*', 'users.first_name')
                    ->where('daily_report_review_status.status','=',1)
                    ->whereDate('daily_report_review_status.reiew_date', '=', date('Y-m-d'))
                    ->where('daily_report_review_status.business_id', '=', $business_id)
                    ->first();
                if(empty($reviewed)){
                   return ['can_proceed' => false] ;
                }else{
                    return [];
                }
                
            }else{
                return [];
            }
    	}else{
    	    return [];
    	}
    }
    
    public function reviewChange($date,$data){
        
        $business_id = request()->session()->get('user.business_id');
        $subscription = Subscription::current_subscription($business_id);
    	$pacakge_details = $subscription->package_details;
    	
    	
    	if(!empty($pacakge_details['daily_review']) && $pacakge_details['daily_review'] == 1){
    	    
            $reviewed = DB::table('daily_report_review_status')
                ->join('users', 'daily_report_review_status.reviewed_by', '=', 'users.id')
                ->select('daily_report_review_status.*', 'users.first_name')
                ->where('daily_report_review_status.status','=',1)
                ->whereDate('daily_report_review_status.reiew_date', '=', date('Y-m-d',strtotime($date)))
                ->where('daily_report_review_status.business_id', '=', $business_id)
                ->first();
            if(!empty($reviewed)){
                
                $changes = DB::table('reviewed_changes')
                        ->where('business_id',$business_id)
                        ->whereDate('date',date('Y-m-d',strtotime($date)))
                        ->select('id')
                        ->first();
                if(!empty($changes)){
                    $reviewID = $changes->id;
                    
                    $data['review_id'] = $reviewID;
                    
                    DB::table('reviewed_changes_description')->insert($data);
    
                    
                }else{
                    
                    $reviewID = DB::table('reviewed_changes')->insertGetId([
                        'business_id' => $business_id,
                        'date' => $date
                    ]);
                    
                    $data['review_id'] = $reviewID;
                    
                   DB::table('reviewed_changes_description')->insert($data);
    
                    
                }
                
            }
    	}
    	
    	return true;
    }

    /**
     * This function formats a number and returns them in specified format
     *
     * @param int $input_number
     * @param boolean $add_symbol = false
     * @param object $business_details = null
     * @param boolean $is_quantity = false; If number represents quantity
     *
     * @return string
     */
    public function num_f($input_number, $add_symbol = false, $business_details = null, $is_quantity = false)
    {
        $thousand_separator = !empty($business_details) ? $business_details->thousand_separator : session('currency')['thousand_separator'];
        $decimal_separator = !empty($business_details) ? $business_details->decimal_separator : session('currency')['decimal_separator'];

        $currency_precision =  !empty($business_details) && !empty($business_details->currency_precision) ? $business_details->currency_precision : config('constants.currency_precision', 2);
        
        
        if ($is_quantity) {
            $currency_precision = !empty($business_details) && !empty($business_details->quantity_precision) ? $business_details->quantity_precision : config('constants.quantity_precision', 2);
        }

        $formatted = number_format($input_number, $currency_precision, $decimal_separator, $thousand_separator);

        if ($add_symbol) {
            $currency_symbol_placement = !empty($business_details) ? $business_details->currency_symbol_placement : session('business.currency_symbol_placement');
            $symbol = !empty($business_details->currency_symbol) ? $business_details->currency_symbol : session('currency')['symbol'];

            if ($currency_symbol_placement == 'after') {
                $formatted = $formatted . ' ' . $symbol;
            } else {
                $formatted = $symbol . ' ' . $formatted;
            }
        }
        
        return $formatted;
    }

    /**
     * Calculates percentage for a given number
     *
     * @param int $number
     * @param int $percent
     * @param int $addition default = 0
     *
     * @return float
     */
    public function calc_percentage($number, $percent, $addition = 0)
    {
        return ($addition + ($number * ($percent / 100)));
    }

    /**
     * Calculates base value on which percentage is calculated
     *
     * @param int $number
     * @param int $percent
     *
     * @return float
     */
    public function calc_percentage_base($number, $percent)
    {
        return (intval($number) * 100) / (100 + intval($percent));
    }

    /**
     * Calculates percentage
     *
     * @param int $base
     * @param int $number
     *
     * @return float
     */
    public function get_percent($base, $number)
    {
        if ($base == 0) {
            return 0;
        }

        $diff = $number - $base;
        return ($diff / $base) * 100;
    }

    //Returns all avilable purchase statuses
    public function orderStatuses()
    {
        return ['received' => __('lang_v1.received'), 'pending' => __('lang_v1.pending'), 'ordered' => __('lang_v1.ordered')];
    }

    /**
     * Defines available Payment Types
     *
     * @return array
     */
    public function payment_types($location = null, $merge_cash_group_accounts = false, $add_credit_purchase = false, $add_credit_expense = false, $add_credit_sale = false, $is_company = false,$action = null)
    {
        $business_id = request()->session()->get('user.business_id');
        
        
        if(!empty($location) && $location != 'null'){
            $location = is_object($location) ? $location : BusinessLocation::find($location);
        }else{
            $location = BusinessLocation::where('business_id', $business_id)->first();
        }

        $payment_types = [];
        $payments = !empty($location) ? json_decode($location->default_payment_accounts,true) ?? [] : [];
        // dd($location);
        foreach($payments as $key => $value){
            if(!empty($value['is_enabled']) && $value['is_enabled'] == 1){
                
                if(!empty($action) ){
                    if(!empty($value[$action]) && $value[$action] == 1){
                        $payment_types[$key] = ucfirst(str_replace("_"," ",$key));
                        // $payment_types[$key.'_account'] = $value['account'];
                    }
                    
                }else{
                    $payment_types[$key] = ucfirst(str_replace("_"," ",$key));
                    // $payment_types[$key.'_account'] = $value['account'];
                }
                
                
            }
        }
        $payment_types['location_id'] = $location->id;
        
        // dd($payment_types);
        
        if ($add_credit_sale) {
            $payment_types['credit_sale'] = __('lang_v1.credit_sale');
        }
        if ($add_credit_purchase) {
            $payment_types['credit_purchase'] = __('lang_v1.credit_purchase');
        }
        if ($add_credit_expense) {
            $payment_types['credit_expense'] = __('lang_v1.credit_expense');
        }
        
        // $payment_types['cpc'] = "CPC";

        return $payment_types;
    }
    
    public function one_payment_type($type,$location = null)
    {
        $business_id = request()->session()->get('user.business_id');
        
        if(!empty($location)){
            $location = is_object($location) ? $location : BusinessLocation::find($location);
        }else{
            $location = BusinessLocation::where('business_id', $business_id)->first();
        }
        
        $payments = json_decode($location->default_payment_accounts,true);
        
        return $payments[$type]['account'];
    }
    
    function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;
    
        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }
    
        return $merged;
    }

    /**
     * return default mapped account for location and method
     *
     * @return array
     */
    public function payment_types_default_account($location = null, $method)
    {
        $account_id = null;
        //Unset payment types if not enabled in business location
        if (!empty($location)) {
            $location = is_object($location) ? $location : BusinessLocation::find($location);
            $location_account_settings = json_decode($location->default_payment_accounts);
            foreach ($location_account_settings as $key => $value) {
                if ($key == $method) {
                    if (!empty($value->account)) {
                        $account_id = $value->account;
                    }
                }
            }
        }

        return $account_id;
    }

    /**
     * Returns the list of modules enabled
     *
     * @return array
     */
    public function allModulesEnabled()
    {
        $enabled_modules = session()->has('business') ? session('business')['enabled_modules'] : null;
        $enabled_modules = (!empty($enabled_modules) && $enabled_modules != 'null') ? $enabled_modules : [];

        return $enabled_modules;
        //Module::has('Restaurant');
    }

    /**
     * Returns the list of modules enabled
     *
     * @return array
     */
    public function isModuleEnabled($module)
    {
        $enabled_modules = $this->allModulesEnabled();

        if (in_array($module, $enabled_modules)) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Converts date in business format to mysql format
     *
     * @param string $date
     * @param bool $time (default = false)
     * @return strin
     */
    // public function uf_date($date, $time = false)
    // {
    //     if (strtotime($date) !== false) {
    //         return $date;
    //     }
    
    //     $date_format = session('business.date_format');
    //     $mysql_format = 'Y-m-d';
    //     if ($time) {
    //         if (session('business.time_format') == 12) {
    //             $date_format = $date_format . ' h:i A';
    //         } else {
    //             $date_format = $date_format . ' H:i';
    //         }
    //         $mysql_format = 'Y-m-d H:i:s';
    //     }


    //     return !empty($date_format) ? \Carbon::createFromFormat($date_format, $date)->format($mysql_format) : null;
    // }
    
    public function uf_date($date, $time = false)
    {
        if (preg_match('/^\d{4}-\d{2}-\d{2}( \d{2}:\d{2}(:\d{2})?)?$/', $date)) {
            return $date;
        }
    
        $date_format = session('business.date_format');
        $mysql_format = 'Y-m-d';
        
        if ($time) {
            if (session('business.time_format') == 12) {
                $date_format .= ' h:i A';
            } else {
                $date_format .= ' H:i';
            }
            $mysql_format = 'Y-m-d H:i:s';
    
            if (strpos($date, ':') === false) {
                $date .= ' 00:00';
            }
        }
    
        try {
            return !empty($date_format) ? \Carbon::createFromFormat($date_format, $date)->format($mysql_format) : null;
        } catch (\Exception $e) {
            return null;
        }
    }



    /**
     * Converts time in business format to mysql format
     *
     * @param string $time
     * @return strin
     */
    public function uf_time($time)
    {
        $time_format = 'H:i';
        if (session('business.time_format') == 12) {
            $time_format = 'h:i A';
        }
        return !empty($time_format) ? \Carbon::createFromFormat($time_format, $time)->format('H:i') : null;
    }

    /**
     * Converts time in business format to mysql format
     *
     * @param string $time
     * @return strin
     */
    public function format_time($time)
    {
        $time_format = 'H:i';
        if (session('business.time_format') == 12) {
            $time_format = 'h:i A';
        }
        return !empty($time) ? \Carbon::createFromFormat('H:i:s', $time)->format($time_format) : null;
    }

    /**
     * Converts date in mysql format to business format
     *
     * @param string $date
     * @param bool $time (default = false)
     * @return strin
     */
    public function format_date($date, $show_time = false, $business_details = null)
    {
        $format = !empty($business_details) ? $business_details->date_format : session('business.date_format');
        if (!empty($show_time)) {
            $time_format = !empty($business_details) ? $business_details->time_format : session('business.time_format');
            if ($time_format == 12) {
                $format .= ' h:i A';
            } else {
                $format .= ' H:i';
            }
        }

        return !empty($date) ? \Carbon::createFromTimestamp(strtotime($date))->format($format) : null;
    }

    /**
     * Increments reference count for a given type and given business
     * and gives the updated reference count
     *
     * @param string $type
     * @param int $business_id
     *
     * @return int
     */
    public function setAndGetReferenceCount($type, $business_id = null)
    {
        if (empty($business_id)) {
            $business_id = request()->session()->get('user.business_id');
        }

        $ref = ReferenceCount::where('ref_type', $type)
            ->where('business_id', $business_id)
            ->first();
        if (!empty($ref)) {
            $ref->ref_count += 1;
            $ref->save();
            return $ref->ref_count;
        } else {
            $business = Business::find($business_id);
            $starting_numbers = $business->ref_no_starting_number;

            $starting_number = !empty($starting_numbers[$type]) ? $starting_numbers[$type] : 1;

            $new_ref = ReferenceCount::create([
                'ref_type' => $type,
                'business_id' => $business_id,
                'ref_count' => $starting_number
            ]);
            return $new_ref->ref_count;
        }
    }
    /**
     * Increments reference count for a given type and given business
     * and gives the updated reference count
     *
     * @param string $type
     * @param int $business_id
     * @param boolean
     * @return int
     */
    public function onlyGetReferenceCount($type, $business_id = null, $increment_only)
    {
        if (empty($business_id)) {
            $business_id = request()->session()->get('user.business_id');
        }

        $ref = ReferenceCount::where('ref_type', $type)
            ->where('business_id', $business_id)
            ->first();
        if (!empty($ref)) {
            if ($increment_only) {
                $ref->ref_count += 1;
                $ref->save();
            }
            return $ref->ref_count;
        } else {
            $business = Business::find($business_id);
            $starting_numbers = $business->ref_no_starting_number;

            $starting_number = !empty($starting_numbers[$type]) ? $starting_numbers[$type] : 1;

            $new_ref = ReferenceCount::create([
                'ref_type' => $type,
                'business_id' => $business_id,
                'ref_count' => $starting_number
            ]);
            return $new_ref->ref_count;
        }
    }

    /**
     * Generates reference number
     *
     * @param string $type
     * @param int $business_id
     *
     * @return int
     */
    public function generateCustomPrefix($l = 2){
        $randomLetters = '';
        for ($i = 0; $i < $l; $i++) {
            $randomLetters .= chr(rand(65, 90)); // ASCII values for A-Z
        }
        
        return $randomLetters;
    }
    public function generateReferenceNumber($type, $ref_count, $business_id = null, $default_prefix = null)
    {
        $prefix = '';

        if (session()->has('business') && !empty(request()->session()->get('business.ref_no_prefixes')[$type])) {
            $prefix = request()->session()->get('business.ref_no_prefixes')[$type];
        }
        if (!empty($business_id)) {
            $business = Business::find($business_id);
            $prefixes = $business->ref_no_prefixes;
            $prefix = isset($prefixes[$type]) ? $prefixes[$type] : '';
        }

        if (!empty($default_prefix)) {
            $prefix = $default_prefix;
        }
        
        if(empty($prefix)){
            $prefix = $this->generateCustomPrefix();
        }

        $ref_digits =  str_pad($ref_count, 4, 0, STR_PAD_LEFT);

        if (!in_array($type, ['contacts', 'business_location', 'username', 'employee_no','route_no'])) {
            $ref_year = \Carbon::now()->year;
            if ($type == 'contacts') {
            }
            $ref_number = $prefix . $ref_year . '/' . $ref_digits;
        } else {
            if ($type == 'contacts') {
                $ref_number = $prefix . '-' . $ref_digits;
            } else {
                $ref_number = $prefix . $ref_digits;
            }
        }

        return $ref_number;
    }

    /**
     * Checks if the given user is admin
     *
     * @param obj $user
     * @param int $business_id
     *
     * @return bool
     */
    public function is_admin($user, $business_id)
    {
        return $user->hasRole('Admin#' . $business_id) ? true : false;
    }

    /**
     * Checks if the feature is allowed in demo
     *
     * @return mixed
     */
    public function notAllowedInDemo()
    {
        //Disable in demo
        if (config('app.env') == 'demo') {
            $output = [
                'success' => 0,
                'msg' => __('lang_v1.disabled_in_demo')
            ];
            if (request()->ajax()) {
                return $output;
            } else {
                return back()->with('status', $output);
            }
        }
    }

    /**
     * Sends SMS notification.
     *
     * @param  array $data
     * @return void
     */
    public function superadminSendSms($data){
        $this->ultimateSMS($data['mobile_number'],$data['sms_body'],$sms_settings['ultimate_sender_id'],$sms_settings['ultimate_token']);
        return true;
    }
    
   
    
    public function __businessSMSUnitCost($business_id){
        $unit_cost = RefillBusiness::leftjoin('sms_refill_packages','sms_refill_packages.id','refill_business.package_id')
                                ->where('refill_business.business_id',$business_id)->where('refill_business.type','business')
                                ->select(['sms_refill_packages.unit_cost'])
                                ->orderBy('refill_business.date','DESC')->first()->unit_cost ?? 2;
                                
        return $unit_cost;
    }

    static function renderSmsTemplate($business, $template, $transaction, $extras, $booking_rooms, $extras_id)
    {
        // Check if template is empty
        if (empty($template)) {
            return null;
        }

        // Check if transaction is empty
        if (empty($transaction)) {
            return null;
        }

        // Check if booking_rooms is empty
        if (empty($booking_rooms)) {
            return null;
        }

        // Check if extras is empty
        if (empty($extras)) {
            return null;
        }
        // Build full address
        $addressParts = array_filter([
            $transaction->contact->landmark ?? null,
            $transaction->contact->city ?? null,
            $transaction->contact->state ?? null,
            $transaction->contact->country ?? null,
        ]);
        $fullAddress = implode(', ', $addressParts);

        if (empty($transaction->hms_coupon_id) && $transaction->discount_amount > 0) {

          $discount=  number_format($transaction->discount_amount, 2) ."% ₨". number_format($transaction->discount_amount * ($transaction->room_price + $transaction->extra_price) / 100, 2);
        }
        else{
            $discount = "₨ ".number_format($transaction->discount_amount, 2);
        }
        $couponCode = $transaction->coupon_code;

        // Static replacements
        $replacements = [
            '{{business_name}}'  => $business->name ?? '',
            '{{booking_no}}'     => $transaction->ref_no ?? '',
            '{{room_no}}'        => $booking_rooms->pluck('room.name')->join(', '),
            '{{coupon_code}}'    => $couponCode,
            '{{client_name}}'    => $transaction->contact->name ?? '',
            '{{client_phone}}'   => $transaction->contact->mobile ?? '',
            '{{address}}'        => $fullAddress ?? '',
            '{{status}}'         => $transaction->status ?? '',
            '{{arrival_time}}'   => $transaction->formatted_arrival ?? '',
            '{{departure_date}}' => $transaction->formatted_departure ?? '',
            '{{duration}}'       => $transaction->hms_total_nights ?? '',
            '{{amount}}'         => number_format($transaction->final_total, 2),
            '{{amount_paid}}'    => number_format($transaction->total_paid, 2),
            '{{amount_due}}'     => number_format($transaction->final_total - $transaction->total_paid, 2),
            '{{extended_till}}'  => $transaction->extended_till ?? '',
            '{{room_price}}'   => number_format($transaction->room_price, 2),
            '{{extra_price}}'   => number_format($transaction->extra_price, 2),
            '{{coupon_code}}'    => $transaction->coupon_code ?? '',
            '{{discount}}'    => $discount ?? '',
        ];

        // Replace basic tags first
        $template = strtr($template, $replacements);

        // Handle booking_rooms loop if placeholder exists
        if (strpos($template, '{{booking_rooms}}') !== false) {
            $roomsText = '';
            foreach ($booking_rooms as $room) {
                $roomsText .= "Type: " . ($room->type ?? '') . "\n";
                $roomsText .= "Room No: " . ($room->room_number ?? '') . "\n";
                $roomsText .= "Adults: " . $room->adults . "\n";
                $roomsText .= "Children: " . $room->childrens . "\n";
                $roomsText .= "Price: ₨" . number_format($room->total_price, 2) . "\n\n";
            }

            // Replace the tag with rendered room list
            $template = str_replace('{{booking_rooms}}', trim($roomsText), $template);
        }

         // Handle extras loop
        if (strpos($template, '{{extras}}') !== false) {
            $extrasText = '';
            foreach ($extras as $extra) {
                if (in_array($extra->id, $extras_id)) {
                    $extrasText .= "- " . $extra->name;
                    $extrasText .= " (₨ " . number_format($extra->price, 2);
                    $extrasText .= "/" . str_replace('_', ' ', $extra->price_per) . ")\n";
                }
            }
            $template = str_replace('{{extras}}', trim($extrasText), $template);
        }
        
        $subscription = Subscription::active_subscription($business->id);
        $module_activation_data = !empty($subscription->module_activation_details) ? json_decode($subscription->module_activation_details,true) : array();
        // Optional dynamic footer from business or fallback
        $footer = $module_activation_data['invoice_footer'] ?? '';

        // Replace any variables in the footer as well
        $footer = strtr($footer, $replacements);

        return $template;
    }

    public static function print_sms($id,$phones,$format){
        // dd("the id is: ".$id);
    try{
        $business_id = request()->session()->get('user.business_id');

        $business = Business::find($business_id);

        $transaction =  Transaction::where('transactions.business_id', $business_id)
        ->with(['contact'])
        ->leftJoin('hms_booking_lines as hbl', 'transactions.id', '=', 'hbl.transaction_id')
        ->leftJoin('hms_booking_extras as hbe', 'transactions.id', '=', 'hbe.transaction_id')
        ->leftJoin('hms_coupons as coupons', 'transactions.hms_coupon_id', '=', 'coupons.id')
        ->where('transactions.type', 'hms_booking')
        ->select(
            'transactions.*',
            DB::raw('(SELECT SUM(total_price) FROM hms_booking_lines WHERE transaction_id = transactions.id) as room_price'),
            DB::raw('(SELECT SUM(price) FROM hms_booking_extras WHERE transaction_id = transactions.id) as extra_price'),
            'coupons.coupon_code',DB::raw('(SELECT SUM(IF(TP.is_return = 1,-1*TP.amount,TP.amount)) FROM transaction_payments AS TP WHERE
            TP.transaction_id=transactions.id) as total_paid')
        )
        ->groupBy('transactions.id') // Group by transaction ID
        ->findOrFail($id);

        $booking_rooms = HmsBookingLine::where('transaction_id',$id)
                            ->leftjoin('hms_rooms as room', 'room.id', '=', 'hms_booking_lines.hms_room_id')
                            ->leftjoin('hms_room_types as type', 'type.id', '=', 'hms_booking_lines.hms_room_type_id')
                            ->get();
        // dd($booking_rooms);
                                             
        $extras_id =  HmsBookingExtra::where('transaction_id', $id)->pluck('hms_extra_id')->toArray();

        $extras = HmsExtra::where('business_id', $business_id)->get();

        $transaction->formatted_arrival = Carbon::parse($transaction->hms_booking_arrival_date_time)
            ->format(session('business.date_format') . ' H:i');
        
        $transaction->formatted_departure = Carbon::parse($transaction->hms_booking_departure_date_time)
            ->format(session('business.date_format') . ' H:i');
            
            foreach ($extras as $extra) {
                $extra->formatted_price = number_format($extra->price, 2); // e.g. "1000.00"
                $extra->formatted_price_per = str_replace('_', ' ', $extra->price_per); // e.g. "per_day"
            }

        $settings = json_decode($business->hms_settings);
        $template = $settings->sms_templates->$format ?? '';
        //  dd($settings->sms_templates->$format);
        $msg = Util::renderSmsTemplate($business,$template, $transaction, $extras, $booking_rooms, $extras_id);
        // dd($msg);
        // $msg = view('hms::bookings.print_pdf_sms')
        //     ->with(compact('business', 'transaction', 'booking_rooms', 'extras_id', 'extras'))
        //     ->render();
        $businessUtil = new BusinessUtil();
        $sms_settings = empty($business->sms_settings)
            ? $businessUtil->defaultSmsSettings()
            : $business->sms_settings;
        // $phonesArray = explode(',', $phones);
        $data = [
            'sms_settings' => $sms_settings,
            'mobile_number' => $phones,
            'sms_body' => strip_tags($msg) // Optional: remove HTML tags
        ];
        // dd($msg);
        // $data = [
        //     'sms_settings' => $sms_settings,
        //     'mobile_number' => "94768366178",
        //     'sms_body' => "this is test sms" // Optional: remove HTML tags
        // ];
        
        $businessUtil->sendSms($data, 'transaction_changed');
        }catch (\Exception $e) {
            DB::rollBack();
            \Log::emergency('File:' . $e->getFile() . 'Line:' . $e->getLine() . 'Message:' . $e->getMessage());
            // dd($e);
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong'),           ];

            return back()->with('status', $output);
        }
    }

    
    public function sendSms($data, $notification_type = 'General Message', $contact = null)
    {
        $business_id = request()->session()->get('business.id');
        $subscription = Subscription::current_subscription($business_id);
        
        if(!empty($subscription)){
           
            $permissions = $subscription->package_details;
            if(!empty($permissions['enable_sms']) && $permissions['enable_sms'] == 1){  
                
                
                
                if(!empty($contact)){
                    // send to additional numbers
                        if (!empty($contact->notification_contacts) && !empty($notification_type) && !empty(json_decode($contact->notification_contacts,true))) {
                            
                            $sms_phone_nos = explode(',',$data['mobile_number']);
                            
                            foreach(json_decode($contact->notification_contacts,true) as $n){
                                if(!empty($n['notifications']) && !empty($n['notifications'][$notification_type]) && $n['notifications'][$notification_type] == 1){
                                    $sms_phone_nos[] = $n['phone_number'];
                                }
                            }
                            
                            $data['mobile_number'] = implode(',',$sms_phone_nos);
                            
                        }
                }
                
               
                $sms_settings = $data['sms_settings'];
                
                $sent = false;
       
                $no_of_sms = $this->__getNumberOfSms($data['sms_body']);
                $unit_cost = $this->__businessSMSUnitCost($business_id);
                $transactionUtil = new TransactionUtil(new ModuleUtil(), new ContactUtil());
                $date = date('Y-m-d');
                $balance = $transactionUtil->__getSMSBalance($date, $business_id, 'business');
                $total_cost = $no_of_sms * $unit_cost * sizeof(explode(',',$data['mobile_number'])); 
                
                if($total_cost > $balance){
                    logger('Insufficient Balance to send the messages. You wallet has '.$balance.' but you need '.$total_cost);
                    return false;
                }
                
                
                $correct_phones = $transactionUtil->validateNos($data['mobile_number'],$business_id)['valid'];
                $incorrect_phones = $transactionUtil->validateNos($data['mobile_number'],$business_id)['invalid'];
                
                // dd($correct_phones);
                
                $data['mobile_number'] = implode(',',$correct_phones);
            
                $sms_log = array(
                    'business_id' => $business_id,
                    'message' => $data['sms_body'],
                    'no_of_characters' => strlen($data['sms_body']),
                    'no_of_sms' => $no_of_sms,
                    'sms_type' => $notification_type,
                    'unit_cost' => $unit_cost,
                    'sms_type_' => $this->__smsType($data['sms_body']),
                    'total_cost' => $no_of_sms * $unit_cost * 1,
                    'sms_status' => 'Sent',
                    'business_type' => 'business',
                    'username' => auth()->user()->username,
                    'default_gateway' => $sms_settings['default_gateway']
                );
                
                
                if(!empty($sms_settings['default_gateway'])){
                    if($sms_settings['default_gateway'] == 'utlimate_sms'){
                        
                        $sent = $this->ultimateSMS($data['mobile_number'],$data['sms_body'],$sms_settings['ultimate_sender_id'],$sms_settings['ultimate_token']);
                        $transactionUtil->__notifyLowSMSBalance($business_id, 'business',$sms_settings['ultimate_sender_id']);
                        $sender_name = $sms_settings['ultimate_sender_id'];
                        if(!empty($sent)){
                            foreach($sent['data'] as $one){
                                $sms_log['recipient'] = $one['to'];
                                $sms_log['uuid'] = $one['uid'];
                                $sms_log['sender_name'] = $sms_settings['ultimate_sender_id'];
                               
                                if($one['customer_status'] == 'Delivered'){
                                    $sms_log['sms_status'] = 'Delivered';
                                }
                                
                                SmsLog::create($sms_log);
                            }
                        }
                        
                        return true;
                    }elseif($sms_settings['default_gateway'] == 'hutch_sms'){
                        $sent = $this->hutchSendSMS($data['mobile_number'],$data['sms_body'],$sms_settings['hutch_username'],$sms_settings['hutch_password'],$sms_settings['hutch_mask']);
                        $transactionUtil->__notifyLowSMSBalance($business_id, 'business',$sms_settings['hutch_mask']);
                        if(!empty($sent)){
                            
                            foreach(explode(',',$data['mobile_number']) as $one){
                                $sms_log['recipient'] = $one;
                                $sms_log['uuid'] = rand(111111111,999999999);
                                $sms_log['sms_status'] = 'Delivered';
                                $sms_log['sender_name'] = $sms_settings['hutch_mask'];
                               
                                SmsLog::create($sms_log);
                            }
                            
                        }
                        
                        $sender_name = $sms_settings['hutch_mask'];
                        
                        return true;
                    }else{
                        
                        // $request_data = [
                        //     $sms_settings['send_to_param_name'] => ltrim($data['mobile_number'], '+'),
                        //     $sms_settings['msg_param_name'] => $data['sms_body'],
                        // ];
                
                        // if (!empty($sms_settings['param_1'])) {
                        //     $request_data[$sms_settings['param_1']] = $sms_settings['param_val_1'];
                        // }
                        // if (!empty($sms_settings['param_2'])) {
                        //     $request_data[$sms_settings['param_2']] = $sms_settings['param_val_2'];
                        // }
                        // if (!empty($sms_settings['param_3'])) {
                        //     $request_data[$sms_settings['param_3']] = $sms_settings['param_val_3'];
                        // }
                        // if (!empty($sms_settings['param_4'])) {
                        //     $request_data[$sms_settings['param_4']] = $sms_settings['param_val_4'];
                        // }
                        // if (!empty($sms_settings['param_5'])) {
                        //     $request_data[$sms_settings['param_5']] = $sms_settings['param_val_5'];
                        // }
                        // if (!empty($sms_settings['param_6'])) {
                        //     $request_data[$sms_settings['param_6']] = $sms_settings['param_val_6'];
                        // }
                        // if (!empty($sms_settings['param_7'])) {
                        //     $request_data[$sms_settings['param_7']] = $sms_settings['param_val_7'];
                        // }
                        // if (!empty($sms_settings['param_8'])) {
                        //     $request_data[$sms_settings['param_8']] = $sms_settings['param_val_8'];
                        // }
                        // if (!empty($sms_settings['param_9'])) {
                        //     $request_data[$sms_settings['param_9']] = $sms_settings['param_val_9'];
                        // }
                        // if (!empty($sms_settings['param_10'])) {
                        //     $request_data[$sms_settings['param_10']] = $sms_settings['param_val_10'];
                        // }
                
                        // $client = new Client();
                
                        // $headers = [];
                        // if (!empty($sms_settings['header_1'])) {
                        //     $headers[$sms_settings['header_1']] = $sms_settings['header_val_1'];
                        // }
                        // if (!empty($sms_settings['header_2'])) {
                        //     $headers[$sms_settings['header_2']] = $sms_settings['header_val_2'];
                        // }
                        // if (!empty($sms_settings['header_3'])) {
                        //     $headers[$sms_settings['header_3']] = $sms_settings['header_val_3'];
                        // }
                
                        // $options = [];
                        // if (!empty($headers)) {
                        //     $options['headers'] = $headers;
                        // }
                
                        // if (empty($sms_settings['url'])) {
                        //     return false;
                        // }
                
                        // if ($sms_settings['request_method'] == 'get') {
                        //     $response = $client->get($sms_settings['url'] . '?' . http_build_query($request_data), $options);
                        // } else {
                        //     $options['form_params'] = $request_data;
                
                        //     $response = $client->post($sms_settings['url'], $options);
                        // }
                
                        // return $response;
                    }
                    
                    if(!empty($sender_name)){
                        foreach($incorrect_phones as $incorrect){
                            $sms_log['recipient'] = $incorrect;
                            $sms_log['uuid'] = rand(11111111111,99999999999);
                            $sms_log['sender_name'] = $sender_name;
                           
                            $sms_log['sms_status'] = 'Delivered';
                            
                            SmsLog::create($sms_log);
                        }
                    }
                    
                
                }
                
            }else{
                logger("not permitted");
            }
        }

    }
    
     public function getWhatsappNotificationLink($data)
    {
        //Supports only integers without leading zeros
        $whatsapp_number = abs((int) filter_var($data['mobile_number'], FILTER_SANITIZE_NUMBER_INT));
        $text = $data['whatsapp_text'];

        $base_url = config('constants.whatsapp_base_url').'/'.$whatsapp_number;

        return $base_url.'?text='.urlencode($text);
    }
    
    public function superadminTransactionalSms($data)
    {
        
            $sms_settings = $data['sms_settings'];
            
            
            if(!empty($sms_settings['default_gateway'])){
                if($sms_settings['default_gateway'] == 'utlimate_sms'){
                    
                    $sent = $this->ultimateSMS($data['mobile_number'],$data['sms_body'],$sms_settings['ultimate_sender_id'],$sms_settings['ultimate_token']);
                    return true;
                }elseif($sms_settings['default_gateway'] == 'hutch_sms'){
                    $sent = $this->hutchSendSMS($data['mobile_number'],$data['sms_body'],$sms_settings['hutch_username'],$sms_settings['hutch_password'],$sms_settings['hutch_mask']);
                    return true;
                }
            }
                
            
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
    
    /**
     * Retrieves sub units of a base unit
     *
     * @param integer $business_id
     * @param integer $unit_id
     * @param boolean $return_main_unit_if_empty = false
     * @param integer $product_id = null
     *
     * @return array
     */
    public function getSubUnits($business_id, $unit_id, $return_main_unit_if_empty = false, $product_id = null)
    {
        $unit = Unit::where('business_id', $business_id)
            ->with(['sub_units'])
            ->findOrFail($unit_id);

        //Find related subunits for the product.
        $related_sub_units = [];
        if (!empty($product_id)) {
            $product = Product::where('business_id', $business_id)->findOrFail($product_id);
            $related_sub_units = $product->sub_unit_ids;
        }

        $sub_units = [];

        //Add main unit as per given parameter or conditions.
        if (($return_main_unit_if_empty && count($unit->sub_units) == 0)) {
            $sub_units[$unit->id] = [
                'name' => $unit->actual_name,
                'multiplier' => 1,
                'allow_decimal' => $unit->allow_decimal
            ];
        } elseif (empty($related_sub_units) || in_array($unit->id, $related_sub_units)) {
            $sub_units[$unit->id] = [
                'name' => $unit->actual_name,
                'multiplier' => 1,
                'allow_decimal' => $unit->allow_decimal
            ];
        }

        if (count($unit->sub_units) > 0) {
            foreach ($unit->sub_units as $sub_unit) {
                //Check if subunit is related to the product or not.
                if (empty($related_sub_units) || in_array($sub_unit->id, $related_sub_units)) {
                    $sub_units[$sub_unit->id] = [
                        'name' => $sub_unit->actual_name,
                        'multiplier' => $sub_unit->base_unit_multiplier,
                        'allow_decimal' => $sub_unit->allow_decimal
                    ];
                }
            }
        }

        return $sub_units;
    }

    public function getMultiplierOf2Units($base_unit_id, $unit_id)
    {
        if ($base_unit_id == $unit_id || is_null($base_unit_id) || is_null($unit_id)) {
            return 1;
        }

        $unit = Unit::where('base_unit_id', $base_unit_id)
            ->where('id', $unit_id)
            ->first();
        if (empty($unit)) {
            return 1;
        } else {
            return $unit->base_unit_multiplier;
        }
    }

    /**
     * Generates unique token
     *
     * @param void
     *
     * @return string
     */
    public function generateToken()
    {
        return md5(rand(1, 10) . microtime());
    }

    /**
     * Generates invoice url for the transaction
     *
     * @param int $transaction_id, int $business_id
     *
     * @return string
     */
    public function getInvoiceUrl($transaction_id, $business_id)
    {
        $transaction = Transaction::where('business_id', $business_id)
            ->findOrFail($transaction_id);

        if (empty($transaction->invoice_token)) {
            $transaction->invoice_token = $this->generateToken();
            $transaction->save();
        }

        return route('show_invoice', ['token' => $transaction->invoice_token]);
    }

    /**
     * Uploads document to the server if present in the request
     * @param obj $request, string $file_name, string dir_name
     *
     * @return string
     */
    // public function uploadFile($request, $file_name, $dir_name, $file_type = 'document')
    // {
    //     //If app environment is demo return null
    //     if (config('app.env') == 'demo') {
    //         return null;
    //     }

    //     $uploaded_file_name = null;
    //     if ($request->hasFile($file_name) && $request->file($file_name)->isValid()) {

    //         if (!file_exists('./public/uploads/' . $dir_name)) {
    //             mkdir('./public/uploads/' . $dir_name, 0777, true);
    //         }

    //         //Check if mime type is image
    //         if ($file_type == 'image') {
    //             if (strpos($request->$file_name->getClientMimeType(), 'image/') === false) {
    //                 throw new \Exception("Invalid image file");
    //             }
    //         }
            
    //         if ($request->$file_name->getSize() <= config('constants.document_size_limit')) {
    //             $new_file_name = time() . '_' . $request->$file_name->getClientOriginalName();

    //             $upload_image_quality = (int) System::getProperty('upload_image_quality');
    //             $img = Image::make($request->$file_name->getRealPath())->save('public/uploads/' . $dir_name . '/' . $new_file_name, $upload_image_quality);

    //             $uploaded_file_name = $new_file_name;
    //         }
    //     }

    //     return $uploaded_file_name;
    // }
    
    
    
    public function uploadFile($request, $file_name, $dir_name, $file_type = 'document')
    {
        // If app environment is demo, return null
        if (config('app.env') == 'demo') {
            return null;
        }
    
        $uploaded_file_name = null;
        if ($request->hasFile($file_name) && $request->file($file_name)->isValid()) {
    
            if (!file_exists('./public/uploads/' . $dir_name)) {
                mkdir('./public/uploads/' . $dir_name, 0777, true);
            }
    
            // Check if mime type is image
            if ($file_type == 'image' && strpos($request->$file_name->getClientMimeType(), 'image/') === false) {
                throw new \Exception("Invalid image file");
            }
    
            if ($request->$file_name->getSize() <= config('constants.document_size_limit')) {
                $new_file_name = time() . '_' . $request->$file_name->getClientOriginalName();
    
                $uploaded_file_path = 'public/uploads/' . $dir_name . '/' . $new_file_name;
    
                if (strpos($request->$file_name->getClientMimeType(), 'image/') === true) {
                    // Convert the image to AVIF format
                    $img = Image::make($request->$file_name->getRealPath())->save($uploaded_file_path, null, function ($constraint) {
                        $constraint->format('avif');
                    });
                } else {
                    // Save the uploaded file without modification
                    $request->$file_name->move(public_path('uploads/' . $dir_name), $new_file_name);
                }
    
                $uploaded_file_name = $new_file_name;
            }
        }
    
        return $uploaded_file_name;
    }

    public function serviceStaffDropdown($business_id, $location_id = null)
    {
        $waiters = [];
        //Get all service staff roles
        $service_staff_roles_obj = Role::where('business_id', $business_id)
            ->where('is_service_staff', 1)
            ->get();

        $service_staff_roles = $service_staff_roles_obj->pluck('name')->toArray();

        //Get all users of service staff roles
        if (!empty($service_staff_roles)) {
            $waiters = User::where('business_id', $business_id)
                ->role($service_staff_roles);

            if (!empty($location_id)) {
                $waiters->permission(['location.' . $location_id, 'access_all_locations']);
            }

            $waiters = $waiters->select('id', DB::raw('CONCAT(COALESCE(first_name, ""), " ", COALESCE(last_name, "")) as full_name'))->get()->pluck('full_name', 'id');
        }

        return $waiters;
    }

    /**
     * Replaces tags from notification body with original value
     *
     * @param  text  $body
     * @param  int  $transaction_id
     *
     * @return array
     */
    public function replaceTags($business_id, $data, $transaction,$is_single_pmt = null)
    {
        // logger($data);
        if (!is_object($transaction)) {
            $transaction = Transaction::where('business_id', $business_id)
                ->with(['contact', 'payment_lines'])
                ->findOrFail($transaction);
        }

        $business = Business::findOrFail($business_id);

        foreach ($data as $key => $value) {
            //Replace contact name
            if (strpos($value, '{contact_name}') !== false) {
                $contact_name = $transaction->contact->name;

                $data[$key] = str_replace('{contact_name}', $contact_name, $data[$key]);
            }
            
            // replace transaction date:
            if (strpos($value, '{transaction_date}') !== false) {
                $transaction_date = $this->format_date($transaction->transaction_date);
                $data[$key] = str_replace('{transaction_date}', $transaction_date,$data[$key]);
            }
            
            if (strpos($value, '{loan_date}') !== false) {
                $transaction_date = $this->format_date($transaction->transaction_date);
                $data[$key] = str_replace('{loan_date}', $transaction_date,$data[$key]);
            }
            
            //Replace customer name
            if (strpos($value, '{customer_name}') !== false) {
                $customer_name = $transaction->contact->name;

                $data[$key] = str_replace('{customer_name}', $customer_name, $data[$key]);
            }
            
            //Replace bank name
            if (strpos($value, '{bank_name}') !== false) {
                $bank_name = $transaction->bank_name;

                $data[$key] = str_replace('{bank_name}', $bank_name, $data[$key]);
            }

            //Replace invoice number
            if (strpos($value, '{invoice_number}') !== false) {
                $invoice_number = $transaction->type == 'sell' ? $transaction->invoice_no : $transaction->ref_no;

                $data[$key] = str_replace('{invoice_number}', $invoice_number, $data[$key]);
            }
            
            //Replace expense number
            if (strpos($value, '{expense_number}') !== false) {
                $invoice_number = $transaction->ref_no;

                $data[$key] = str_replace('{expense_number}', $invoice_number, $data[$key]);
            }
            
            //Replace purchase ref number
            if (strpos($value, '{purchase_ref_number}') !== false) {
                $invoice_number = $transaction->ref_no;

                $data[$key] = str_replace('{purchase_ref_number}', $invoice_number, $data[$key]);
            }
            
            //Replace total_amount
            if (strpos($value, '{total_amount}') !== false) {
                $total_amount = $this->num_f($transaction->final_total, false);
                $data[$key] = str_replace('{total_amount}', $total_amount, $data[$key]);
            }
            
            
            //Replace total_amount
            if (strpos($value, '{loan_amount}') !== false) {
                $total_amount = $this->num_f($transaction->final_total, false);
                $data[$key] = str_replace('{loan_amount}', $total_amount, $data[$key]);
            }
            

            $total_paid = 0;
            foreach ($transaction->payment_lines as $payment) {
                if(empty($is_single_pmt)){
                    if ($payment->is_return != 1 ) {
                        $total_paid += $payment->amount;
                    }
                }
                
            }
            
            if(!empty($is_single_pmt)){
                $total_paid = $transaction->single_payment_amount;
            }
            
            //Replace total_amount
            if (strpos($value, '{paid_amount}') !== false) {
                $paid_amount = $this->num_f($total_paid, false);

                $data[$key] = str_replace('{paid_amount}', $paid_amount, $data[$key]);
            }
            
            // replace payment ref
            if (strpos($value, '{payment_ref_number}') !== false) {
                $payment_ref_number = $transaction->payment_ref_number;

                $data[$key] = str_replace('{payment_ref_number}', $payment_ref_number, $data[$key]);
            }
            
            //Replace total_amount
            if (strpos($value, '{received_amount}') !== false) {
                $paid_amount = $this->num_f($total_paid, false);

                $data[$key] = str_replace('{received_amount}', $paid_amount, $data[$key]);
            }

            //Replace due_amount
            if (strpos($value, '{due_amount}') !== false) {
                $due = $transaction->final_total - $total_paid;
                $due_amount = $this->num_f($due, false);

                $data[$key] = str_replace('{due_amount}', $due_amount, $data[$key]);
            }
            
            //Replace amount
            if (strpos($value, '{amount}') !== false) {
                $due = $transaction->final_total;
                $due_amount = $this->num_f($due, false);

                $data[$key] = str_replace('{amount}', $due_amount, $data[$key]);
            }
            
            //Replace due_amount
            if (strpos($value, '{cumulative_due_amount}') !== false) {
                $contactUtil = new \App\Utils\ContactUtil();
                
                if($transaction->contact->type == 'customer'){
                    $due = $contactUtil->getCustomerBalance($transaction->contact->id,$business_id,true);
                }else{
                    $due = $contactUtil->getSupplierBalance($transaction->contact->id,$business_id,true);
                }
                
                $due_amount = $this->num_f($due, false);

                $data[$key] = str_replace('{cumulative_due_amount}', $due_amount, $data[$key]);
            }
            
            if (strpos($value, '{outstanding_amount}') !== false) {
                $contactUtil = new \App\Utils\ContactUtil();
                
                if($transaction->contact->type == 'customer'){
                    $due = $contactUtil->getCustomerBalance($transaction->contact->id,$business_id,true);
                }else{
                    $due = $contactUtil->getSupplierBalance($transaction->contact->id,$business_id,true);
                }
                
                $due_amount = $this->num_f($due-$transaction->final_total, false);

                $data[$key] = str_replace('{outstanding_amount}', $due_amount, $data[$key]);
            }

            //Replace business_name
            if (strpos($value, '{business_name}') !== false) {
                $business_name = $business->name;
                $data[$key] = str_replace('{business_name}', $business_name, $data[$key]);
            }

            //Replace business_logo
            if (strpos($value, '{business_logo}') !== false) {
                $logo_name = $business->logo;
                $business_logo = !empty($logo_name) ? '<img src="' . url('public/uploads/business_logos/' . $logo_name) . '" alt="Business Logo" >' : '';

                $data[$key] = str_replace('{business_logo}', $business_logo, $data[$key]);
            }

            //Replace invoice_url
            if (strpos($value, '{invoice_url}') !== false && $transaction->type == 'sell') {
                $invoice_url = $this->getInvoiceUrl($transaction->id, $transaction->business_id);
                $data[$key] = str_replace('{invoice_url}', $invoice_url, $data[$key]);
            }

            //Replace Vehicle No.
            if (strpos($value, '{vehicle_no}') !== false) {
                $vehicle= \App\new_vehicle::where("customer_id", $transaction->contact->id ?? '')->first();
                if(!is_null($vehicle)){
                    $vehicle_no = $vehicle->vehicle_no ?? '';
                } else {
                    $vehicle_no = '';
                }
                $data[$key] = str_replace('{vehicle_no}', $vehicle_no, $data[$key]);
            }

            //Replace customer_reference
            if (strpos($value, '{customer_reference}') !== false) {
                $vehicle= \Modules\Petro\Entities\SettlementCreditSalePayment::where("customer_id", $transaction->contact->id ?? '')->first();
                if(!is_null($vehicle)){
                    $customer_reference = $vehicle->customer_reference ?? '';
                } else {
                    $customer_reference = '';
                }
                $data[$key] = str_replace('{customer_reference}', $customer_reference, $data[$key]);
            }
        }

        return $data;
    }

    public function getCronJobCommand()
    {
        $php_binary_path = empty(PHP_BINARY) ? "php" : PHP_BINARY;

        $command = "* * * * * " . $php_binary_path . " " . base_path('artisan') . " schedule:run >> /dev/null 2>&1";

        if (config('app.env') == 'demo') {
            $command = '';
        }

        return $command;
    }

    /**
     * Checks whether mail is configured or not
     *
     * @return boolean
     */
    public function IsMailConfigured()
    {
        $is_mail_configured = false;

        if (
            !empty(env('MAIL_DRIVER')) &&
            !empty(env('MAIL_HOST')) &&
            !empty(env('MAIL_PORT')) &&
            !empty(env('MAIL_USERNAME')) &&
            !empty(env('MAIL_PASSWORD')) &&
            !empty(env('MAIL_FROM_ADDRESS'))
        ) {
            $is_mail_configured = true;
        }

        return $is_mail_configured;
    }

    /**
     * Returns the list of barcode types
     *
     * @return array
     */
    public function barcode_types()
    {
        $types = ['C128' => 'Code 128 (C128)', 'C39' => 'Code 39 (C39)', 'EAN13' => 'EAN-13', 'EAN8' => 'EAN-8', 'UPCA' => 'UPC-A', 'UPCE' => 'UPC-E'];

        return $types;
    }

    /**
     * Returns the default barcode.
     *
     * @return string
     */
    public function barcode_default()
    {
        return 'C128';
    }

    /**
     * Retrieves user role name.
     *
     * @return string
     */
    public function getUserRoleName($user_id)
    {
        $user = User::findOrFail($user_id);

        $roles = $user->getRoleNames();

        $role_name = '';

        if (!empty($roles[0])) {
            $array = explode('#', $roles[0], 2);
            $role_name = !empty($array[0]) ? $array[0] : '';
        }
        return $role_name;
    }

    /**
     * Retrieves all admins of a business
     *
     * @param int $business_id
     *
     * @return obj
     */
    public function get_admins($business_id)
    {
        $admins = User::role('Admin#' . $business_id)->get();

        return $admins;
    }

    /**
     * Retrieves IP address of the user
     *
     * @return string
     */
    public function getUserIpAddr()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            //ip from share internet
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            //ip pass from proxy
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }

    /**
     * This function updates the stock of products present in combo product and also updates transaction sell line.
     *
     * @param array $lines
     * @param int $location_id
     * @param boolean $adjust_stock = true
     *
     * @return void
     */
    public function updateEditedSellLineCombo($lines, $location_id, $adjust_stock = true)
    {
        if (empty($lines)) {
            return true;
        }

        $change_percent = null;

        foreach ($lines as $key => $line) {
            $prev_line = TransactionSellLine::find($line['transaction_sell_lines_id']);

            $difference = $prev_line->quantity - $line['quantity'];
            if ($difference != 0) {
                //Update stock in variation location details table.
                //Adjust Quantity in variations location table
                if ($adjust_stock) {
                    VariationLocationDetails::where('variation_id', $line['variation_id'])
                        ->where('product_id', $line['product_id'])
                        ->where('location_id', $location_id)
                        ->increment('qty_available', $difference);
                }

                //Update the child line quantity
                $prev_line->quantity = $line['quantity'];
            }

            //Recalculate the price.
            if (is_null($change_percent)) {
                $parent = TransactionSellLine::findOrFail($prev_line->parent_sell_line_id);
                $child_sum = TransactionSellLine::where('parent_sell_line_id', $prev_line->parent_sell_line_id)
                    ->select(DB::raw('SUM(unit_price_inc_tax * quantity) as total_price'))
                    ->first()
                    ->total_price;

                $change_percent = $this->get_percent($child_sum, $parent->unit_price_inc_tax * $parent->quantity);
            }

            $price = $this->calc_percentage($prev_line->unit_price_inc_tax, $change_percent, $prev_line->unit_price_inc_tax);
            $prev_line->unit_price_before_discount = $price;
            $prev_line->unit_price = $price;
            $prev_line->unit_price_inc_tax = $price;

            $prev_line->save();
        }
    }

    /**
     *
     * Generates string to calculate sum of purchase line quantity used
     */
    public function get_pl_quantity_sum_string($table_name = '')
    {
        $table_name = !empty($table_name) ? $table_name . '.' : '';
        // + " . $table_name . "quantity_adjusted removed from query
        $string = $table_name . "quantity_sold + " . $table_name . "quantity_returned + " . $table_name . "mfg_quantity_used";

        return $string;
    }

    public function shipping_statuses()
    {
        $statuses = [
            'ordered' => __('lang_v1.ordered'),
            'packed' => __('lang_v1.packed'),
            'shipped' => __('lang_v1.shipped'),
            'delivered' => __('lang_v1.delivered'),
            'cancelled' => __('restaurant.cancelled')
        ];

        return $statuses;
    }

    public function createDropdownHtml($array, $append_text = null)
    {
        if (!empty($append_text)) {
            $html = '<option value="">' . $append_text . '</option>';
        }
        foreach ($array as $key => $value) {
            $html .= '<option value="' . $key . '">' . $value . '</option>';
        }

        return $html;
    }

    public function getPumpsQuotaByLocation($business_id, $location_id)
    {
        if (auth()->user()->can('superadmin')) {
            return true;
        }
        $pump_count = Pump::where('business_id', $business_id)->where('location_id', $location_id)->count();
        $pumps_permission = ModulePermissionLocation::getModulePermissionLocations($business_id, 'number_of_pumps');
        $allocated_pumps = 0;
        if (!empty($pumps_permission)) {
            $allocated_pumps = array_key_exists($location_id, $pumps_permission->locations) ? $pumps_permission->locations[$location_id] : 0;
        }
        if ($pump_count >= $allocated_pumps) {
            return false;
        }

        return true;
    }

    public function account_exist_return_id($account_name)
    {
        $business_id = request()->session()->get('business.id');
        $account = Account::where(DB::raw("REPLACE(`name`, '  ', ' ')"), $account_name)->where('business_id', $business_id)->first();
        if (!empty($account)) {
            return $account->id;
        } else {
            return 0;
        }
    }

    public function createAccountTransaction($transaction, $type, $account_id, $amount, $transaction_payment_id = null, $note = null)
    {
        $account_transaction_data = [
            'amount' => $amount,
            'account_id' => $account_id,
            'type' => $type,
            'operation_date' => $transaction->transaction_date,
            'created_by' => $transaction->created_by,
            'transaction_id' => $transaction->id,
            'transaction_payment_id' => $transaction_payment_id,
            'note' => $note
        ];

        AccountTransaction::createAccountTransaction($account_transaction_data);
    }
    public function getRouteOperationInvoiceNumber($business_id)
    {
        $route_count = RouteOperation::where('business_id', $business_id)->count();

        $route_invoice_number = RouteInvoiceNumber::where('business_id', $business_id)->orderBy('date', 'desc')->first();

        $invoice_number = '';
        $number = 1;
        $prefix = '';
        if (!empty($route_invoice_number->prefix)) {
            $prefix = $route_invoice_number->prefix;
        }
        if (!empty($route_invoice_number->starting_number)) {
            $number = $route_invoice_number->starting_number;
        }
        $number = $number +  $route_count;

        $invoice_number = $prefix . $number;
        return $invoice_number;
    }

    public function getGeneralMessage($key)
    {
        $message = '';

        $font_size = System::getProperty('customer_supplier_security_deposit_current_liability_font_size');
        $color = System::getProperty('customer_supplier_security_deposit_current_liability_color');
        $msg = System::getProperty('customer_supplier_security_deposit_current_liability_message');

        if (System::getProperty($key) == 1) {
            $message = '<p class="text-center" style="font-size: ' . $font_size . 'px !important; padding-top: 10px; color: ' . $color . ' ">' . $msg . '</p>';
        }

        return $message;
    }
    public function roundQuantity($quantity)
    {
        $quantity_precision = session('business.quantity_precision', 2);

        return round($quantity, $quantity_precision);
    }

    /**
     * Convert an amount to words (custom implementation)
     *
     * @param float|int $amount
     * @return string
     */
    public function convertAmountToWords($amount)
    {
        $amount = floor($amount); // Handle only the integer part
        $words = $this->numberToWords($amount);

        return ucfirst($words);
    }

    /**
     * Recursive function to convert a number into words
     *
     * @param int $number
     * @return string
     */
    private function numberToWords($number)
    {
        $units = [
            '', 'one', 'two', 'three', 'four', 'five', 'six', 'seven', 'eight', 'nine',
            'ten', 'eleven', 'twelve', 'thirteen', 'fourteen', 'fifteen', 'sixteen',
            'seventeen', 'eighteen', 'nineteen'
        ];
        $tens = [
            '', '', 'twenty', 'thirty', 'forty', 'fifty', 'sixty', 'seventy', 'eighty', 'ninety'
        ];
        $thousands = [
            '', 'thousand', 'million', 'billion', 'trillion'
        ];

        if ($number < 20) {
            return $units[$number];
        } elseif ($number < 100) {
            return $tens[intval($number / 10)] . ($number % 10 > 0 ? ' ' . $units[$number % 10] : '');
        } elseif ($number < 1000) {
            return $units[intval($number / 100)] . ' hundred' .
                ($number % 100 > 0 ? ' and ' . $this->numberToWords($number % 100) : '');
        } else {
            foreach ($thousands as $index => $thousand) {
                $divider = pow(1000, $index);
                if ($number < $divider * 1000) {
                    return $this->numberToWords(intval($number / $divider)) . ' ' . $thousand .
                        ($number % $divider > 0 ? ' ' . $this->numberToWords($number % $divider) : '');
                }
            }
        }

        return '';
    }
}
