<?php

use Carbon\Carbon;
use App\BusinessLocation;

/**
 * boots pos.
 */
function pos_boot($ul, $pt, $lc, $em, $un, $type = 1, $pid = null){

    $ch = curl_init();
    $request_url = ($type == 1) ? base64_decode(config('author.lic1')) : base64_decode(config('author.lic2'));

    $pid = is_null($pid) ? config('author.pid') : $pid;

    $curlConfig = [CURLOPT_URL => $request_url, 
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_POSTFIELDS     => [
            'url' => $ul,
            'path' => $pt,
            'license_code' => $lc,
            'email' => $em,
            'username' => $un,
            'product_id' => $pid
        ]
    ];
    curl_setopt_array($ch, $curlConfig);
    $result = curl_exec($ch);

    if (curl_errno($ch)) {
        $error_msg = 'C'.'U'.'RL '.'E'.'rro'.'r: ';
        $error_msg .= curl_errno($ch);

        return redirect()->back()
            ->with('error', $error_msg);
    }
    curl_close($ch);

    if($result){
        $result = json_decode($result, true);

        if($result['flag'] == 'valid'){
            // if(!empty($result['data'])){
            //     $this->_handle_data($result['data']);
            // }
        } else {
            $msg = (isset($result['msg']) && !empty($result['msg'])) ? $result['msg'] : "I"."nvali"."d "."Lic"."ense Det"."ails";
            return redirect()->back()
                ->with('error', $msg);
        }
    }
}

if (! function_exists('humanFilesize')) {
    function humanFilesize($size, $precision = 2)
    {
        $units = ['B','kB','MB','GB','TB','PB','EB','ZB','YB'];
        $step = 1024;
        $i = 0;

        while (($size / $step) > 0.9) {
            $size = $size / $step;
            $i++;
        }
        
        return round($size, $precision).$units[$i];
    }
}

/**
 * Checks if the uploaded document is an image
 */
if (! function_exists('isFileImage')) {
    function isFileImage($filename)
    {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        $array = ['png', 'PNG', 'jpg', 'JPG', 'jpeg', 'JPEG', 'gif', 'GIF'];
        $output = in_array($ext, $array) ? true : false;

        return $output;
    }
}

/**
 * Checks number formate and round
 */
if (! function_exists('numberFormate')) {
    function numberFormate($number)
    {
        return number_format(round($number,2));
    }
}


if (! function_exists('hasSubscriptionAccess')) {
    function hasSubscriptionAccess($moduleKey)  {
        $business_id = request()->session()->get('user.business_id');
        $subscription = Modules\Superadmin\Entities\Subscription::current_subscription($business_id);
        
        if (!empty($subscription)) {
            $pacakge_details = $subscription->package_details;
            $disable_all_other_module_vr = 0;

            if (array_key_exists('disable_all_other_module_vr', $pacakge_details)) {
                $disable_all_other_module_vr = $pacakge_details['disable_all_other_module_vr'];
            }

            if ($disable_all_other_module_vr == 0) {
                return array_key_exists($moduleKey, $pacakge_details);
            }
    
        }
        return false;
    }
}

if (! function_exists('defaultCustomerForm')) {
    function defaultCustomerForm()  {
        return [
            'customer_name'=> 1,
            'need_to_send_sms'=> 1,
            'credit_notification_type'=> 1,
            'vat_no'=> 1,
            'customer_opening_balance'=> 1,
            'customer_customer_group'=> 1,
            'customer_credit_limit'=> 1,
            'customer_transaction_date'=> 1,
            'add_more_mobile'=>1,
            'customer_mobile'=>1,
            'customer_landline'=>1,
            'assigned_to'=>1,
            'customer_address'=>1,
            'address_line_2'=>1,
            'customer_city'=>1,
            'customer_state'=>1,
            'customer_country'=>1,
            'customer_landmark'=>1,
        ];
    }
}



if (!function_exists('displayPhoneFormatForApi')) {
    function displayPhoneFormatForApi($str)
    {
        
        if ($str != '') {
            $str = cleanPhoneNumber($str);
            $str =  substr($str, 0, 3) . ' ' . substr($str, 3, 3) . ' ' . substr($str, 6, 5);

        }
        return $str;
    }
}
if (!function_exists('cleanPhoneNumber')) {
    function cleanPhoneNumber($str,$isBird=false)
    {
        $phone = str_replace(array('(', ')','.','-', ' ','+','_'), array('', '', '', '','',''), $str);;
        if($isBird)
        return $phone;
        $str = substr($phone,-10);
        return $str;
    }
}
if (!function_exists('displayPhoneFormat')) {
    function displayPhoneFormat($str)
    {
        
        if ($str != '' && strlen($str) > 3) {
            $str = cleanPhoneNumber($str);
            $str = '(' . substr($str, 0, 3) . ') ' . substr($str, 3, 3) . '-' . substr($str, 6, 5);

        }
        else
        {
            $str = '(xxx)xxx-xxxx';
        }
        return $str;
    }


}

if (!function_exists('locationCurrency')) {
    function locationCurrency($number,$currency, $symbol_left_side = true)
    {
        
        return ($symbol_left_side)?$currency->symbol.number_format($number): number_format($number).$currency->symbol;
    }


}

if (!function_exists('createDate')) {
    function createDate($data,$formate='Y-m-d')
    {
       
        $years = implode($data['y']);
        $month = implode($data['m']);
        $day = implode($data['d']);
        if($years == ''  || $month =='' ||  $day =='' )
        {
            return null;
        }
        return Carbon::createFromFormat($formate, $years.'-'.$month.'-'.$day)->format($formate);
    }
}
if (!function_exists('createDateArray')) {
    function createDateArray($date)
    {
        return [
                'y' => str_split(Carbon::parse($date)->format('Y')),
                'm' => str_split(Carbon::parse($date)->format('m')),
                'd' => str_split(Carbon::parse($date)->format('d')),
        ];
    }
}

if (!function_exists('bussionLocation')) {
    function bussionLocation()
    {
        $business_id = request()->session()->get('user.business_id');
        return BusinessLocation::whereBusinessId($business_id)->pluck('name', 'id');
    }
}



if (!function_exists('currencyFormat')){
    function currencyFormat($value){
        $value = floatval($value);
    
        if (((is_numeric( $value ) && floor( $value ) != $value)== true)) {
        
            return floatval($value);
        }
        if ($value == 0) return 0;
        $business_id = request()->session()->get('business.id');
        $currency_precision = \App\Business::where('id', $business_id)->value('currency_precision');

        return number_format($value,$currency_precision,'.',',') ;
    }
}

if (!function_exists('quantityFormat')){
    function quantityFormat($value,$category = null){
        $value = floatval($value);
    
        if (((is_numeric( $value ) && floor( $value ) != $value)== true)) {
        
            return floatval($value);
        }
        if ($value == 0) return 0;
          if($category != 'fuel'){
            $quantity_precision = 3;
        }else{
            $business_id = request()->session()->get('business.id');
            $quantity_precision = \App\Business::where('id', $business_id)->value('quantity_precision');
        }

        return number_format($value,$quantity_precision,'.',',') ;
    }
}

        

//

   
