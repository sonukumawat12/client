<?php

namespace App\Utils;

use \Notification;
use App\Business;
use App\Notifications\CustomerNotification;
use App\Notifications\RecurringInvoiceNotification;

use App\Notifications\SupplierNotification;
use Modules\Petro\Entities\PetroNotificationTemplate;

use App\NotificationTemplate;
use App\Restaurant\Booking;
use App\System;
use App\Transaction;
use App\User;
use Config;
use App\Utils\BusinessUtil;

use Modules\Superadmin\Entities\Subscription;


class NotificationUtil extends Util
{
    
    public function __construct(BusinessUtil $businessUtil)
    {
        $this->businessUtil = $businessUtil;
    }
    
    /**
     * Automatically send notification to customer/supplier if enabled in the template setting
     *
     * @param  int  $business_id
     * @param  string  $notification_type
     * @param  obj  $transaction
     * @param  obj  $contact
     *
     * @return void
     */
     
    
    public function autoSendNotification($business_id, $notification_type, $transaction, $contact,$is_single_pmt = null)
    {
        $notification_template = NotificationTemplate::where('business_id', $business_id)
                ->where('template_for', $notification_type)
                ->first();

        $business = Business::findOrFail($business_id);
        $data['email_settings'] = $business->email_settings;
        $data['sms_settings'] = $business->sms_settings;
        
        if (!empty($notification_template)) {
            
            if (!empty($notification_template->auto_send) || !empty($notification_template->auto_send_sms)) {
                $orig_data = [
                    'email_body' => $notification_template->email_body,
                    'sms_body' => $notification_template->sms_body,
                    'subject' => $notification_template->subject
                ];
                $tag_replaced_data = $this->replaceTags($business_id, $orig_data, $transaction,$is_single_pmt);

                $data['email_body'] = $tag_replaced_data['email_body'];
                $data['sms_body'] = $tag_replaced_data['sms_body'];
                
                
                //Auto send email
                if (!empty($notification_template->auto_send)) {
                    $data['subject'] = $tag_replaced_data['subject'];
                    $data['to_email'] = $contact->email;

                    $customer_notifications = NotificationTemplate::customerNotifications();
                    $supplier_notifications = NotificationTemplate::supplierNotifications();
                    $business_notifications = NotificationTemplate::businessNotifications();
                    if (array_key_exists($notification_type, $customer_notifications)) {
                        Notification::route('mail', $data['to_email'])
                                        ->notify(new CustomerNotification($data));
                    } elseif (array_key_exists($notification_type, $supplier_notifications)) {
                        Notification::route('mail', $data['to_email'])
                                        ->notify(new SupplierNotification($data));
                    } elseif (array_key_exists($notification_type, $business_notifications)) {
                        Notification::route('mail', $data['to_email'])
                                        ->notify(new SupplierNotification($data));
                    }
                }

                //Auto send sms
                if (!empty($notification_template->auto_send_sms)) {
                    if($contact->should_notify == 1){
                        
                        $sms_phone_nos = array();
                        // send to main number
                        if (!empty($contact->mobile)) {
                           $sms_phone_nos[] = $contact->mobile;
                        }
                        
                        // send to alternate number
                        if (!empty($contact->alternate_number)) {
                            $sms_phone_nos[] = $contact->alternate_number;
                        }
                        
                        // send to additional numbers
                        if (!empty($contact->notification_contacts) && !empty($notification_type) && !empty(json_decode($contact->notification_contacts,true))) {
                            
                            foreach(json_decode($contact->notification_contacts,true) as $n){
                                if(!empty($n['notifications']) && !empty($n['notifications'][$notification_type]) && $n['notifications'][$notification_type] == 1){
                                    $sms_phone_nos[] = $n['phone_number'];
                                }
                            }
                            
                        }
                        
                        if(!empty($sms_phone_nos)){
                            
                            $data['mobile_number'] = implode(',',$sms_phone_nos);
                
                            $this->sendSms($data,$notification_type);
                        }
                        
                    }
                        
                }else{
                    
                }
            }
        }
    }
    

    /**
     * Replaces tags from notification body with original value
     *
     * @param  text  $body
     * @param  int  $booking_id
     *
     * @return array  
     */
    public function replaceBookingTags($business_id, $data, $booking_id)
    {
        $business = Business::findOrFail($business_id);
    
        $booking = Booking::where('business_id', $business_id)
                    ->with(['customer', 'table', 'correspondent', 'waiter', 'location', 'business'])
                    ->findOrFail($booking_id);
        foreach ($data as $key => $value) {
            //Replace contact name
            if (strpos($value, '{contact_name}') !== false) {
                $contact_name = $booking->customer->name;

                $data[$key] = str_replace('{contact_name}', $contact_name, $data[$key]);
            }

            //Replace table
            if (strpos($value, '{table}') !== false) {
                $table = !empty($booking->table->name) ?  $booking->table->name : '';

                $data[$key] = str_replace('{table}', $table, $data[$key]);
            }

            //Replace start_time
            if (strpos($value, '{start_time}') !== false) {
                $start_time = $this->format_date($booking->booking_start, true);

                $data[$key] = str_replace('{start_time}', $start_time, $data[$key]);
            }

            //Replace end_time
            if (strpos($value, '{end_time}') !== false) {
                $end_time = $this->format_date($booking->booking_end, true);

                $data[$key] = str_replace('{end_time}', $end_time, $data[$key]);
            }
            //Replace location
            if (strpos($value, '{location}') !== false) {
                $location = $booking->location->name;

                $data[$key] = str_replace('{location}', $location, $data[$key]);
            }

            //Replace service_staff
            if (strpos($value, '{service_staff}') !== false) {
                $service_staff = !empty($booking->waiter) ? $booking->waiter->user_full_name : '';

                $data[$key] = str_replace('{service_staff}', $service_staff, $data[$key]);
            }

            //Replace service_staff
            if (strpos($value, '{correspondent}') !== false) {
                $correspondent = !empty($booking->correspondent) ? $booking->correspondent->user_full_name : '';

                $data[$key] = str_replace('{correspondent}', $correspondent, $data[$key]);
            }

            //Replace business_name
            if (strpos($value, '{business_name}') !== false) {
                $business_name = $business->name;
                $data[$key] = str_replace('{business_name}', $business_name, $data[$key]);
            }

            //Replace business_logo
            if (strpos($value, '{business_logo}') !== false) {
                $logo_name = $business->logo;
                $business_logo = !empty($logo_name) ? '<img src="' . url('storage/business_logos/' . $logo_name) . '" alt="Business Logo" >' : '';

                $data[$key] = str_replace('{business_logo}', $business_logo, $data[$key]);
            }
        }
        return $data;
    }

    public function recurringInvoiceNotification($user, $invoice)
    {
        $user->notify(new RecurringInvoiceNotification($invoice));
    }

    public function configureEmail($notificationInfo, $check_superadmin = true)
    {
        $email_settings = $notificationInfo['email_settings'];

        $is_superadmin_settings_allowed = System::getProperty('allow_email_settings_to_businesses');
        #dd($is_superadmin_settings_allowed,$email_settings,$check_superadmin);
        //Check if prefered email setting is superadmin email settings
        if (!empty($is_superadmin_settings_allowed) && !empty($email_settings['use_superadmin_settings']) && $check_superadmin) {
            $email_settings['mail_driver'] = config('mail.driver');
            $email_settings['mail_host'] = config('mail.host');
            $email_settings['mail_port'] = config('mail.port');
            $email_settings['mail_username'] = config('mail.username');
            $email_settings['mail_password'] = config('mail.password');
            $email_settings['mail_encryption'] = config('mail.encryption');
            $email_settings['mail_from_address'] = config('mail.from.address');
        }

        $mail_driver = !empty($email_settings['mail_driver']) ? $email_settings['mail_driver'] : 'smtp';
        Config::set('mail.driver', $mail_driver);
        Config::set('mail.host', $email_settings['mail_host']);
        Config::set('mail.port', $email_settings['mail_port']);
        Config::set('mail.username', $email_settings['mail_username']);
        Config::set('mail.password', $email_settings['mail_password']);
        Config::set('mail.encryption', $email_settings['mail_encryption']);

        Config::set('mail.from.address', $email_settings['mail_from_address']);
        Config::set('mail.from.name', $email_settings['mail_from_name']);
    }
    
    public function sendPetroNotification($template_for,$data,$passed_phones = null){
        $business_id = request()->session()->get('user.business_id');
        $msg_template = PetroNotificationTemplate::where('business_id',$business_id)->where('template_for',$template_for)->where('auto_send_sms',1)->first();
        
        if(!empty($msg_template)){
            $msg = self::__replacePetroTags($template_for, $data, $msg_template->sms_body);
            logger($msg);
            
            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
            
            if(!empty($passed_phones)){
                
                $data = [
                    'sms_settings' => $sms_settings,
                    'mobile_number' => $passed_phones,
                    'sms_body' => $msg
                ];
                $this->sendSms($data,$template_for);
                
            }else{
                
                $phones = [];
                if(!empty($msg_template->phone_nos)){
                    $phones = explode(',',str_replace(' ','',$msg_template->phone_nos));
                }else{
                    if(!empty($business->sms_settings)){
                        $phones = explode(',',str_replace(' ','',$business->sms_settings['msg_phone_nos']));
                    }
                }
                
                if(!empty($phones)){
                    $data = [
                        'sms_settings' => $sms_settings,
                        'mobile_number' => implode(',',$phones),
                        'sms_body' => $msg
                    ];
                    $this->sendSms($data,$template_for);
                }
            }
        }
            
    }
    
    public function sendGeneralNotification($template_for,$data){
        $business_id = request()->session()->get('user.business_id');
        $msg_template = NotificationTemplate::where('business_id',$business_id)->where('template_for',$template_for)->where('auto_send_sms',1)->first();
        
        if(!empty($msg_template)){
            $msg = self::__replaceGeneralTags($template_for, $data, $msg_template->sms_body);
            $business = Business::where('id', $business_id)->first();
            $sms_settings = empty($business->sms_settings) ? $this->businessUtil->defaultSmsSettings() : $business->sms_settings;
            
            $phones = [];
            if(!empty($msg_template->phone_nos)){
                $phones = explode(',',str_replace(' ','',$msg_template->phone_nos));
            }else{
                if(!empty($business->sms_settings)){
                    $phones = explode(',',str_replace(' ','',$business->sms_settings['msg_phone_nos']));
                }
            }
            
            
            if(!empty($phones)){
                
                $data = [
                    'sms_settings' => $sms_settings,
                    'mobile_number' => implode(',',$phones),
                    'sms_body' => $msg
                ];
                
                $this->sendSms($data,$template_for);
            }
            
        }
            
    }
    
    public function __replacePetroTags($template_for, $data, $msg){
        switch($template_for){
            case 'settlements':
                $msg = str_replace('{settlement_date}',array_key_exists('settlement_date',$data) ? $data['settlement_date'] : "",$msg);
                $msg = str_replace('{pump_operator_name}',array_key_exists('pump_operator_name',$data) ? $data['pump_operator_name'] : "",$msg);
                $msg = str_replace('{settlement_pumps}',array_key_exists('settlement_pumps',$data) ? $data['settlement_pumps'] : "",$msg);
                $msg = str_replace('{total_sale_amount}',array_key_exists('total_sale_amount',$data) ? $data['total_sale_amount'] : "",$msg);
                $msg = str_replace('{total_cash}',array_key_exists('total_cash',$data) ? $data['total_cash'] : "",$msg);
                $msg = str_replace('{total_cards}',array_key_exists('total_cards',$data) ? $data['total_cards'] : "",$msg);
                $msg = str_replace('{total_credit_sales}',array_key_exists('total_credit_sales',$data) ? $data['total_credit_sales'] : "",$msg);
                $msg = str_replace('{total_short}',array_key_exists('total_short',$data) ? $data['total_short'] : "",$msg);
                $msg = str_replace('{total_loans}',array_key_exists('total_loans',$data) ? $data['total_loans'] : "",$msg);
                $msg = str_replace('{total_cheques}',array_key_exists('total_cheques',$data) ? $data['total_cheques'] : "",$msg);
                
                $msg = str_replace('{cash_deposit}',array_key_exists('cash_deposit',$data) ? $data['cash_deposit'] : "",$msg);
                $msg = str_replace('{total_expenses}',array_key_exists('total_expenses',$data) ? $data['total_expenses'] : "",$msg);
                $msg = str_replace('{total_excess}',array_key_exists('total_excess',$data) ? $data['total_excess'] : "",$msg);
                $msg = str_replace('{loan_payments}',array_key_exists('loan_payments',$data) ? $data['loan_payments'] : "",$msg);
                $msg = str_replace('{owners_drawings}',array_key_exists('owners_drawings',$data) ? $data['owners_drawings'] : "",$msg);
                $msg = str_replace('{settlement_no}',array_key_exists('settlement_no',$data) ? $data['settlement_no'] : "",$msg);
                
                break;
            case 'edit_settlements':
                $msg = str_replace('{settlement_no}',array_key_exists('settlement_no',$data) ? $data['settlement_no'] : "",$msg);
                $msg = str_replace('{editted_date}',array_key_exists('editted_date',$data) ? $data['editted_date'] : "",$msg);
                $msg = str_replace('{user_editted}',array_key_exists('user_editted',$data) ? $data['user_editted'] : "",$msg);
                $msg = str_replace('{original_details}',array_key_exists('original_details',$data) ? $data['original_details'] : "",$msg);
                $msg = str_replace('{editted_details}',array_key_exists('editted_details',$data) ? $data['editted_details'] : "",$msg);
                break;
            
            case 'day_end_settlement':
                $msg = str_replace('{date}',array_key_exists('date',$data) ? $data['date'] : "",$msg);
                $msg = str_replace('{total_sale}',array_key_exists('total_sale',$data) ? $data['total_sale'] : "",$msg);
                $msg = str_replace('{pumpers_worked}',array_key_exists('pumpers_worked',$data) ? $data['pumpers_worked'] : "",$msg);
                $msg = str_replace('{pumps}',array_key_exists('pumps',$data) ? $data['pumps'] : "",$msg);
                $msg = str_replace('{total_cash}',array_key_exists('total_cash',$data) ? $data['total_cash'] : "",$msg);
                $msg = str_replace('{total_cards}',array_key_exists('total_cards',$data) ? $data['total_cards'] : "",$msg);
                $msg = str_replace('{total_credit_sales}',array_key_exists('total_credit_sales',$data) ? $data['total_credit_sales'] : "",$msg);
                $msg = str_replace('{total_short}',array_key_exists('total_short',$data) ? $data['total_short'] : "",$msg);
                $msg = str_replace('{total_loans}',array_key_exists('total_loans',$data) ? $data['total_loans'] : "",$msg);
                $msg = str_replace('{total_cheques}',array_key_exists('total_cheques',$data) ? $data['total_cheques'] : "",$msg);
                $msg = str_replace('{tank_product_qty_difference}',array_key_exists('tank_product_qty_difference',$data) ? $data['tank_product_qty_difference'] : "",$msg);
                $msg = str_replace('{fuel_category_products}',array_key_exists('fuel_category_products',$data) ? $data['fuel_category_products'] : "",$msg);
                
                $msg = str_replace('{cash_deposit}',array_key_exists('cash_deposit',$data) ? $data['cash_deposit'] : "",$msg);
                $msg = str_replace('{total_expenses}',array_key_exists('total_expenses',$data) ? $data['total_expenses'] : "",$msg);
                $msg = str_replace('{total_excess}',array_key_exists('total_excess',$data) ? $data['total_excess'] : "",$msg);
                $msg = str_replace('{loan_payments}',array_key_exists('loan_payments',$data) ? $data['loan_payments'] : "",$msg);
                $msg = str_replace('{owners_drawings}',array_key_exists('owners_drawings',$data) ? $data['owners_drawings'] : "",$msg);
                
                $msg = str_replace('{product_sold_qty}',array_key_exists('product_sold_qty',$data) ? $data['product_sold_qty'] : "",$msg);
                $msg = str_replace('{bulk_sale_qty}',array_key_exists('bulk_sale_qty',$data) ? $data['bulk_sale_qty'] : "",$msg);
                
                
                break;
                
            case 'stock_and_dip_details':
                $msg = str_replace('{date_entered}',array_key_exists('date_entered',$data) ? $data['date_entered'] : "",$msg);
                $msg = str_replace('{time_entered}',array_key_exists('time_entered',$data) ? $data['time_entered'] : "",$msg);
                $msg = str_replace('{dip_details}',array_key_exists('dip_details',$data) ? $data['dip_details'] : "",$msg);
                $msg = str_replace('{opening_stock}',array_key_exists('opening_stock',$data) ? $data['opening_stock'] : "",$msg);
                $msg = str_replace('{received_stock}',array_key_exists('received_stock',$data) ? $data['received_stock'] : "",$msg);
                $msg = str_replace('{sold_qty}',array_key_exists('sold_qty',$data) ? $data['sold_qty'] : "",$msg);
                $msg = str_replace('{testing_qty}',array_key_exists('testing_qty',$data) ? $data['testing_qty'] : "",$msg);
                $msg = str_replace('{balance_stock}',array_key_exists('balance_stock',$data) ? $data['balance_stock'] : "",$msg);
                $msg = str_replace('{dip_stock}',array_key_exists('dip_stock',$data) ? $data['dip_stock'] : "",$msg);
                $msg = str_replace('{difference_Stock}',array_key_exists('difference_Stock',$data) ? $data['difference_Stock'] : "",$msg);
                
                break;
                
            case 'load_received':
                $msg = str_replace('{date}',array_key_exists('date',$data) ? $data['date'] : "",$msg);
                $msg = str_replace('{load_details}',array_key_exists('load_details',$data) ? $data['load_details'] : "",$msg);
                
                break;
                
            case 'daily_collection':
                $msg = str_replace('{date}',array_key_exists('date',$data) ? $data['date'] : "",$msg);
                $msg = str_replace('{time}',array_key_exists('time',$data) ? $data['time'] : "",$msg);
                $msg = str_replace('{pump_operator}',array_key_exists('pump_operator',$data) ? $data['pump_operator'] : "",$msg);
                $msg = str_replace('{amount}',array_key_exists('amount',$data) ? $data['amount'] : "",$msg);
                $msg = str_replace('{pumper_cummulative_amount}',array_key_exists('pumper_cummulative_amount',$data) ? $data['pumper_cummulative_amount'] : "",$msg);
                $msg = str_replace('{total_amount}',array_key_exists('total_amount',$data) ? $data['total_amount'] : "",$msg);
                
                break;
                
            case 'pumper_dashboard_cash_deposit':
                $msg = str_replace('{date}',array_key_exists('date',$data) ? $data['date'] : "",$msg);
                $msg = str_replace('{time}',array_key_exists('time',$data) ? $data['time'] : "",$msg);
                $msg = str_replace('{pump_operator}',array_key_exists('pump_operator',$data) ? $data['pump_operator'] : "",$msg);
                $msg = str_replace('{amount}',array_key_exists('amount',$data) ? $data['amount'] : "",$msg);
                
                break;
                
            case 'pumper_dashboard_credit_sales_customer':
                $msg = str_replace('{date}',array_key_exists('date',$data) ? $data['date'] : "",$msg);
                $msg = str_replace('{time}',array_key_exists('time',$data) ? $data['time'] : "",$msg);
                $msg = str_replace('{pump_operator}',array_key_exists('pump_operator',$data) ? $data['pump_operator'] : "",$msg);
                $msg = str_replace('{customer}',array_key_exists('customer',$data) ? $data['customer'] : "",$msg);
                $msg = str_replace('{amount}',array_key_exists('amount',$data) ? $data['amount'] : "",$msg);
                $msg = str_replace('{order_no}',array_key_exists('order_no',$data) ? $data['order_no'] : "",$msg);
                $msg = str_replace('{cumulative_amount}',array_key_exists('cumulative_amount',$data) ? $data['cumulative_amount'] : "",$msg);
                
                break;
                
            case 'pumper_dashboard_credit_sales':
                $msg = str_replace('{date}',array_key_exists('date',$data) ? $data['date'] : "",$msg);
                $msg = str_replace('{time}',array_key_exists('time',$data) ? $data['time'] : "",$msg);
                $msg = str_replace('{pump_operator}',array_key_exists('pump_operator',$data) ? $data['pump_operator'] : "",$msg);
                $msg = str_replace('{customer}',array_key_exists('customer',$data) ? $data['customer'] : "",$msg);
                $msg = str_replace('{amount}',array_key_exists('amount',$data) ? $data['amount'] : "",$msg);
                $msg = str_replace('{order_no}',array_key_exists('order_no',$data) ? $data['order_no'] : "",$msg);
                $msg = str_replace('{cumulative_amount}',array_key_exists('cumulative_amount',$data) ? $data['cumulative_amount'] : "",$msg);
                $msg = str_replace('{customer_reference}',array_key_exists('customer_reference',$data) ? $data['customer_reference'] : "",$msg);
                $msg = str_replace('{vehicle_no}',array_key_exists('customer_reference',$data) ? $data['customer_reference'] : "",$msg);
                
                break;
        }
        
        return $msg;
    }
    
    public function __replaceGeneralTags($template_for, $data, $msg){
        switch($template_for){
            case 'expense_created':
                $msg = str_replace('{transaction_date}',array_key_exists('transaction_date',$data) ? $data['transaction_date'] : "",$msg);
                $msg = str_replace('{ref}',array_key_exists('ref',$data) ? $data['ref'] : "",$msg);
                $msg = str_replace('{amount}',array_key_exists('amount',$data) ? $data['amount'] : "",$msg);
                $msg = str_replace('{account}',array_key_exists('account',$data) ? $data['account'] : "",$msg);
                $msg = str_replace('{staff}',array_key_exists('staff',$data) ? $data['staff'] : "",$msg);
                $msg = str_replace('{expense_category}',array_key_exists('expense_category',$data) ? $data['expense_category'] : "",$msg);
                break;
            
            case 'general_purchase_created':
                $msg = str_replace('{transaction_date}',array_key_exists('transaction_date',$data) ? $data['transaction_date'] : "",$msg);
                $msg = str_replace('{contact_name}',array_key_exists('contact_name',$data) ? $data['contact_name'] : "",$msg);
                $msg = str_replace('{purchase_ref_number}',array_key_exists('purchase_ref_number',$data) ? $data['purchase_ref_number'] : "",$msg);
                $msg = str_replace('{amount}',array_key_exists('amount',$data) ? $data['amount'] : "",$msg);
            
                break;
        }
        
        return $msg;
    }
}
