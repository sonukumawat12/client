<?php

namespace Modules\Superadmin\Http\Controllers;

use App\Account;
use App\Business;
use App\CompanyPackageVariable;
use App\PackageVariable;
use \Notification;
use App\System;
;
use Illuminate\Routing\Controller;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Subscription;
use Modules\Superadmin\Notifications\NewSubscriptionNotification;

class BaseController extends Controller
{

    /**
     * Returns the list of all configured payment gateway
     * @return Response
     */
    protected function _payment_gateways()
    {
        $gateways = [];

        //Check if stripe is configured or not
        if (env('STRIPE_PUB_KEY') && env('STRIPE_SECRET_KEY')) {
            $gateways['stripe'] = 'Stripe';
        }

        //Check if paypal is configured or not
        if ((env('PAYPAL_SANDBOX_API_USERNAME') && env('PAYPAL_SANDBOX_API_PASSWORD')  && env('PAYPAL_SANDBOX_API_SECRET')) || (env('PAYPAL_LIVE_API_USERNAME') && env('PAYPAL_LIVE_API_PASSWORD')  && env('PAYPAL_LIVE_API_SECRET'))) {
            $gateways['paypal'] = 'PayPal';
        }

        //Check if Razorpay is configured or not
        if ((env('RAZORPAY_KEY_ID') && env('RAZORPAY_KEY_SECRET'))) {
            $gateways['razorpay'] = 'Razor Pay';
        }

        //Check if Payhere is configured or not
        if (env('PAYHERE_MERCHANT_ID') &&  env('PAYHERE_LIVE')) {
            $gateways['payhere'] = 'Payhere';
        }

        //Check if Pesapal is configured or not
        if ((config('pesapal.consumer_key') && config('pesapal.consumer_secret')) && env('PESAPAL_LIVE')) {
            $gateways['pesapal'] = 'PesaPal';
        }

        //Check if PayOnline is configured or not
        if (env('PAY_ONLINE_LIVE')) {
            $gateways['online'] = 'Online';
        }

        $gateways['offline'] = 'Offline';

        return $gateways;
    }

    /**
     * Enter details for subscriptions
     * @return object
     */
    protected function _add_subscription($business_id, $package, $gateway, $payment_transaction_id, $user_id, $price,  $is_superadmin = false, $option_variables_selected = null, $module_selected = null)
    {
        $trial_used = Business::where('id', $business_id)->first()->trial_used;
        $module_selected = json_decode($module_selected);
        $option_variables_selected = json_decode($option_variables_selected);
        $subcription = Subscription::active_subscription($business_id);
        $already_pacakge = null;
        if (!empty($subcription)) {
            $already_pacakge = Package::where('id', $subcription->package_id)->first();
        }
        $is_company_pacakge = 0;
        if (!empty($package->only_for_business)) {
            $is_company_pacakge = 1;
        }
        $subscription = [
            'business_id' => $business_id,
            'package_id' => $package->id,
            'paid_via' => $gateway,
            'payment_transaction_id' => $payment_transaction_id
        ];

        if (in_array($gateway, ['online', 'offline', 'pesapal', 'payhere']) && !$is_superadmin) {
            //If offline then dates will be decided when approved by superadmin
            $subscription['start_date'] = null;
            $subscription['end_date'] = null;
            $subscription['trial_end_date'] = null;
            $subscription['status'] = 'waiting';
        } else {
            $dates = $this->_get_package_dates($business_id, $package);

            $subscription['start_date'] = $dates['start'];
            $subscription['end_date'] = $dates['end'];
            $subscription['trial_end_date'] = $dates['trial'];
            $subscription['status'] = 'approved';
        }
        if ($package->trial_days > 0 && $trial_used == 0) {
            $dates = $this->_get_package_dates($business_id, $package);
            $subscription['start_date'] = $dates['start'];
            $subscription['end_date'] = $dates['trial'];
            $subscription['trial_end_date'] = $dates['trial'];
            $subscription['status'] = 'approved';
            Business::where('id', $business_id)->update(['trial_used' => 1]);
        }
        $subscription['package_price'] = $price;
        if (!empty($option_variables_selected)) {
            $subscription['package_details'] = [
                'location_count' => $this->getoption_value(0, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'user_count' => $this->getoption_value(1, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'product_count' => $this->getoption_value(2, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'invoice_count' => $package->invoice_count,
                'customer_count' => $package->customer_count,
                'period_count' => $this->getoption_value(3, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'customer_count' => $this->getoption_value(4, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'vehicle_count' => $this->getoption_value(7, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'monthly_total_sales_limit' => $this->getoption_value(5, $option_variables_selected, $is_company_pacakge, $already_pacakge),
                'name' => $package->name,
                'mf_module' =>  $this->check_module_selected('mf_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_sale_cmsn_agent' => $this->check_module_selected('enable_sale_cmsn_agent', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_restaurant' =>  $this->check_module_selected('enable_restaurant', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_booking' => $this->check_module_selected('enable_booking', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_crm' => $this->check_module_selected('enable_crm', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_sms' => $this->check_module_selected('enable_sms', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_module' => $this->check_module_selected('hr_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'employee' => $this->check_module_selected('employee', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'teminated' => $this->check_module_selected('teminated', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'award' => $this->check_module_selected('award', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leave_request' => $this->check_module_selected('leave_request', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'attendance' => $this->check_module_selected('attendance', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'import_attendance' => $this->check_module_selected('import_attendance', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'late_and_over_time' => $this->check_module_selected('late_and_over_time', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'payroll' => $this->check_module_selected('payroll', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'salary_details' => $this->check_module_selected('salary_details', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'basic_salary' => $this->check_module_selected('basic_salary', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'payroll_payments' => $this->check_module_selected('payroll_payments', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_reports' => $this->check_module_selected('hr_reports', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'attendance_report' => $this->check_module_selected('attendance_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'employee_report' => $this->check_module_selected('employee_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'payroll_report' => $this->check_module_selected('payroll_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'notice_board' => $this->check_module_selected('notice_board', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_settings' => $this->check_module_selected('hr_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'department' => $this->check_module_selected('department', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'jobtitle' => $this->check_module_selected('jobtitle', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'jobcategory' => $this->check_module_selected('jobcategory', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'workingdays' => $this->check_module_selected('workingdays', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'workshift' => $this->check_module_selected('workshift', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'holidays' => $this->check_module_selected('holidays', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leave_type' => $this->check_module_selected('leave_type', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'salary_grade' => $this->check_module_selected('salary_grade', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'employment_status' => $this->check_module_selected('employment_status', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'salary_component' => $this->check_module_selected('salary_component', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_prefix' => $this->check_module_selected('hr_prefix', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_tax' => $this->check_module_selected('hr_tax', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'religion' => $this->check_module_selected('religion', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'hr_setting_page' => $this->check_module_selected('hr_setting_page', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'mpcs_module' => $this->check_module_selected('mpcs_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'fleet_module' => $this->check_module_selected('fleet_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'ezyboat_module' => $this->check_module_selected('ezyboat_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'mpcs_form_settings' => $this->check_module_selected('mpcs_form_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_opening_values' => $this->check_module_selected('list_opening_values', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'merge_sub_category' => $this->check_module_selected('merge_sub_category', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'backup_module' => $this->check_module_selected('backup_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_separate_customer_statement_no' => $this->check_module_selected('enable_separate_customer_statement_no', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'edit_customer_statement' => $this->check_module_selected('edit_customer_statement', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_cheque_writing' => $this->check_module_selected('enable_cheque_writing', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'issue_customer_bill' => $this->check_module_selected('issue_customer_bill', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_petro_module' => $package->petro_module,
                'hospital_system' => $package->hospital_system,
                'customer_order_own_customer' => $this->check_module_selected('customer_order_own_customer', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'customer_settings' => $this->check_module_selected('customer_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'customer_order_general_customer' => $this->check_module_selected('customer_order_general_customer', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'customer_to_directly_in_panel' => $this->check_module_selected('customer_to_directly_in_panel', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'meter_resetting' => $this->check_module_selected('meter_resetting', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'tasks_management' => $this->check_module_selected('tasks_management', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'notes_page' => $this->check_module_selected('notes_page', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'tasks_page' => $this->check_module_selected('tasks_page', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'reminder_page' => $this->check_module_selected('reminder_page', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'member_registration' => $this->check_module_selected('member_registration', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_registration_module' => $this->check_module_selected('visitors_registration_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors' => $this->check_module_selected('visitors', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_registration' => $this->check_module_selected('visitors_registration', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_registration_setting' => $this->check_module_selected('visitors_registration_setting', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_district' => $this->check_module_selected('visitors_district', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'visitors_town' => $this->check_module_selected('visitors_town', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'disable_all_other_module_vr' => $this->check_module_selected('disable_all_other_module_vr', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'catalogue_qr' => $this->check_module_selected('catalogue_qr', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'pay_excess_commission' => $this->check_module_selected('pay_excess_commission', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'recover_shortage' => $this->check_module_selected('recover_shortage', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'pump_operator_ledger' => $this->check_module_selected('pump_operator_ledger', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'commission_type' => $this->check_module_selected('commission_type', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'access_account' => json_decode($package->package_permissions)->account_access,
                'access_sms_settings' => json_decode($package->package_permissions)->sms_settings_access,
                'access_module' => json_decode($package->package_permissions)->module_access,
                'home_dashboard' => $this->check_module_selected('home_dashboard', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'contact_module' => $this->check_module_selected('contact_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'contact_customer' => $this->check_module_selected('contact_customer', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'contact_supplier' => $this->check_module_selected('contact_supplier', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'property_module' => $package->property_module,
                'tank_dip_chart' => $this->check_module_selected('tank_dip_chart', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'ran_module' => $this->check_module_selected('ran_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'report_module' => $this->check_module_selected('report_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'verification_report' => $this->check_module_selected('verification_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'monthly_report' => $this->check_module_selected('monthly_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'comparison_report' => $this->check_module_selected('comparison_report', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'notification_template_module' => $this->check_module_selected('notification_template_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_easy_payment' => $this->check_module_selected('list_easy_payment', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'settings_module' => $this->check_module_selected('settings_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'business_settings' => $this->check_module_selected('business_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'business_location' => $this->check_module_selected('business_location', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'invoice_settings' => $this->check_module_selected('invoice_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'settings_otp_verification' => $this->check_module_selected('settings_otp_verification', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'settings_pay_online' => $this->check_module_selected('settings_pay_online', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'settings_reports_configurations' => $this->check_module_selected('settings_reports_configurations', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'settings_user_locations' => $this->check_module_selected('settings_user_locations', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'tax_rates' => $this->check_module_selected('tax_rates', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'user_management_module' => $this->check_module_selected('user_management_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'banking_module' => $this->check_module_selected('banking_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'orders' => $this->check_module_selected('orders', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'products' => $this->check_module_selected('products', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'purchase' => $this->check_module_selected('purchase', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'stock_transfer' => $this->check_module_selected('stock_transfer', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'service_staff' => $this->check_module_selected('service_staff', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'enable_subscription' => $this->check_module_selected('enable_subscription', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'sale_module' => $this->check_module_selected('sale_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'all_sales' => $this->check_module_selected('all_sales', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'add_sale' => $this->check_module_selected('add_sale', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_pos' => $this->check_module_selected('list_pos', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'pos_sale' => $this->check_module_selected('pos_sale', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_draft' => $this->check_module_selected('list_draft', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_quotation' => $this->check_module_selected('list_quotation', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_sell_return' => $this->check_module_selected('list_sell_return', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'shipment' => $this->check_module_selected('shipment', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'discount' => $this->check_module_selected('discount', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'import_sale' => $this->check_module_selected('import_sale', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'reserved_stock' => $this->check_module_selected('reserved_stock', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'stock_adjustment' => $this->check_module_selected('stock_adjustment', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'tables' => $this->check_module_selected('tables', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'type_of_service' => $this->check_module_selected('type_of_service', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'expenses' => $this->check_module_selected('expenses', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'modifiers' => $this->check_module_selected('modifiers', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'kitchen' => $this->check_module_selected('kitchen', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'upload_images' => $this->check_module_selected('upload_images', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leads_module' => $this->check_module_selected('leads_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leads' => $this->check_module_selected('leads', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'day_count' => $this->check_module_selected('day_count', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leads_import' => $this->check_module_selected('leads_import', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'leads_settings' => $this->check_module_selected('leads_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'sms_module' => $this->check_module_selected('sms_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'cache_clear' => $this->check_module_selected('cache_clear', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'pump_operator_dashboard' => $this->check_module_selected('pump_operator_dashboard', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_sms' => $this->check_module_selected('list_sms', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'status_order' => $this->check_module_selected('status_order', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_orders' => $this->check_module_selected('list_orders', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'upload_orders' => $this->check_module_selected('upload_orders', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'subcriptions' => $this->check_module_selected('subcriptions', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'over_limit_sales' => $this->check_module_selected('over_limit_sales', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'repair_module' => $this->check_module_selected('repair_module', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'job_sheets' => $this->check_module_selected('job_sheets', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'add_job_sheet' => $this->check_module_selected('add_job_sheet', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'list_invoice' => $this->check_module_selected('list_invoice', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'add_invoice' => $this->check_module_selected('add_invoice', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'brands' => $this->check_module_selected('brands', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'repair_settings' => $this->check_module_selected('repair_settings', $module_selected, $package, $is_company_pacakge, $already_pacakge),
                'payday' => $this->check_module_selected('payday', $module_selected, $package, $is_company_pacakge, $already_pacakge)

            ];
        } else {
            $subscription['package_details'] = [
                'auto_services_and_repair_module' => (int)$package->location_count,
                'installment_module' => (int)$package->installment_module,
                'daily_shift_page' => (int)$package->daily_shift_page,
                'daily_cash_tab' => (int)$package->daily_cash_tab,
                'daily_credit_sales' => (int)$package->daily_credit_sales,
                'daily_cards' => (int)$package->daily_cards,
                'daily_shortage_excess' => (int)$package->daily_shortage_excess,
                'daily_cheques' => (int)$package->daily_cheques,
                'collection_summary' => (int)$package->collection_summary,
                'daily_collections_settings' => (int)$package->daily_collections_settings,
                'daily_cash_status' => (int)$package->daily_cash_status,
                'daily_shift' => (int)$package->daily_shift,

                'settlement_sw_other_income' => (int)$package->settlement_sw_other_income,
                'settlement_sw_customer_payments' => (int)$package->settlement_sw_customer_payments,
                'settlement_sw_expenses' => (int)$package->settlement_sw_expenses,
                'settlement_sw_payment' => (int)$package->settlement_sw_payment,

                'settlement_sw_cash' => (int)$package->settlement_sw_cash,
                'settlement_sw_cash_deposit' => (int)$package->settlement_sw_cash_deposit,
                'settlement_sw_cards' => (int)$package->settlement_sw_cards,
                'settlement_sw_cheque' => (int)$package->settlement_sw_cheque,
                'settlement_sw_payment_expenses' => (int)$package->settlement_sw_payment_expenses,
                'settlement_sw_shortage' => (int)$package->settlement_sw_shortage,
                'settlement_sw_excess' => (int)$package->settlement_sw_excess,
                'settlement_sw_credit_sales' => (int)$package->settlement_sw_credit_sales,
                'settlement_sw_loan_payments' => (int)$package->settlement_sw_loan_payments,
                'settlement_sw_owners_drawings' => (int)$package->settlement_sw_owners_drawings,
                'settlement_sw_loan_to_customer' => (int)$package->settlement_sw_loan_to_customer,


                'stock_taking_module' => (int)$package->stock_taking_module,
                'location_count' => (int)$package->location_count,
                'user_count' => (int)$package->user_count,
                'customer_count' => (int)$package->customer_count,
                'monthly_total_sales_limit' => 0,
                'product_count' => (int)$package->product_count,
                'invoice_count' => (int)$package->invoice_count,
                'vehicle_count' => (int)$package->vehicle_count,
                'name' => $package->name,
                'enable_sale_cmsn_agent' => (int)$package->sales_commission_agent,
                'manufacturing_module' => (int)$package->manufacturer,
                'mf_module' => (int)$package->manufacturer,
                'enable_restaurant' => (int)$package->restaurant,
                'enable_booking' => (int)$package->booking,
                'enable_crm' => (int)$package->crm_enable,
                'enable_sms' => (int)$package->sms_enable,

                'hr_module' => (int)$package->hr_module,
                'employee' => (int)$package->employee,
                'teminated' => (int)$package->teminated,
                'award' => (int)$package->award,
                'leave_request' => (int)$package->leave_request,
                'attendance' => (int)$package->attendance,
                'import_attendance' => (int)$package->import_attendance,
                'late_and_over_time' => (int)$package->late_and_over_time,
                'payroll' => (int)$package->payroll,
                'salary_details' => (int)$package->salary_details,
                'basic_salary' => (int)$package->basic_salary,
                'payroll_payments' => (int)$package->payroll_payments,
                'hr_reports' => (int)$package->hr_reports,
                'attendance_report' => (int)$package->attendance_report,
                'employee_report' => (int)$package->employee_report,
                'payroll_report' => (int)$package->payroll_report,
                'notice_board' => (int)$package->notice_board,
                'hr_settings' => (int)$package->hr_settings,
                'department' => (int)$package->department,
                'jobtitle' => (int)$package->jobtitle,
                'jobcategory' => (int)$package->jobcategory,
                'workingdays' => (int)$package->workingdays,
                'workshift' => (int)$package->workshift,
                'holidays' => (int)$package->holidays,
                'leave_type' => (int)$package->leave_type,
                'salary_grade' => (int)$package->salary_grade,
                'employment_status' => (int)$package->employment_status,
                'salary_component' => (int)$package->salary_component,
                'hr_prefix' => (int)$package->hr_prefix,
                'hr_tax' => (int)$package->hr_tax,
                'religion' => (int)$package->religion,
                'hr_setting_page' => (int)$package->hr_setting_page,

                'enable_petro_module' => (int)$package->petro_module,
                'mpcs_module' => (int)$package->mpcs_module,
                'fleet_module' => (int)$package->fleet_module,
                'ezyboat_module' => (int)$package->ezyboat_module,
                'mpcs_form_settings' => (int)$package->mpcs_form_settings,
                'list_opening_values' => (int)$package->list_opening_values,
                'merge_sub_category' => (int)$package->merge_sub_category,
                'backup_module' => (int)$package->backup_module,
                'enable_separate_customer_statement_no' => (int)$package->enable_separate_customer_statement_no,
                'edit_customer_statement' => (int)$package->edit_customer_statement,
                'enable_cheque_writing' => (int)$package->enable_cheque_writing,
                'issue_customer_bill' => (int)$package->issue_customer_bill,
                'hospital_system' => (int)$package->hospital_system,
                'customer_order_own_customer' => (int)$package->customer_order_own_customer,
                'customer_settings' => (int)$package->customer_settings,
                'customer_order_general_customer' => (int)$package->customer_order_general_customer,
                'customer_to_directly_in_panel' => (int)$package->customer_to_directly_in_panel,
                'meter_resetting' => (int)$package->meter_resetting,
                'tasks_management' => (int)$package->tasks_management,
                'notes_page' => (int)$package->notes_page,
                'tasks_page' => (int)$package->tasks_page,
                'reminder_page' => (int)$package->reminder_page,
                'member_registration' => (int)$package->member_registration,
                'visitors_registration_module' => (int)$package->visitors_registration_module,
                'visitors' => (int)$package->visitors,
                'visitors_registration' => (int)$package->visitors_registration,
                'visitors_registration_setting' => (int)$package->visitors_registration_setting,
                'visitors_district' => (int)$package->visitors_district,
                'visitors_town' => (int)$package->visitors_town,
                'disable_all_other_module_vr' => (int)$package->disable_all_other_module_vr,
                'catalogue_qr' => (int)$package->catalogue_qr,
                'pay_excess_commission' => (int)$package->pay_excess_commission,
                'recover_shortage' => (int)$package->recover_shortage,
                'pump_operator_ledger' => (int)$package->pump_operator_ledger,
                'commission_type' => (int)$package->commission_type,
                'enable_duplicate_invoice' => (int)$package->enable_duplicate_invoice,
                'access_account' => (int)json_decode($package->package_permissions)->account_access,
                'access_sms_settings' => (int)json_decode($package->package_permissions)->sms_settings_access,
                'access_module' => (int)json_decode($package->package_permissions)->module_access,
                'home_dashboard' => (int)$package->home_dashboard,
                'contact_module' => (int)$package->contact_module,
                'contact_customer' => (int)$package->contact_customer,
                'contact_supplier' => (int)$package->contact_supplier,
                'property_module' => (int)$package->property_module,
                'tank_dip_chart' => (int)$package->tank_dip_chart,
                'ran_module' => (int)$package->ran_module,
                'report_module' => (int)$package->report_module,
                'verification_report' => (int)$package->verification_report,
                'monthly_report' => (int)$package->monthly_report,
                'comparison_report' => (int)$package->comparison_report,
                'notification_template_module' => (int)$package->notification_template_module,
                'list_easy_payment' => (int)$package->list_easy_payment,
                'settings_module' => (int)$package->settings_module,
                'business_settings' => (int)$package->business_settings,
                'business_location' => (int)$package->business_location,
                'invoice_settings' => (int)$package->invoice_settings,
                'tax_rates' => (int)$package->tax_rates,
                'user_management_module' => (int)$package->user_management_module,
                'banking_module' => (int)$package->banking_module,
                'orders' => (int)$package->orders,
                'products' => (int)$package->products,
                'purchase' => (int)$package->purchase,
                'stock_transfer' => (int)$package->stock_transfer,
                'service_staff' => (int)$package->service_staff,
                'enable_subscription' => (int)$package->enable_subscription,
                'sale_module' => (int)$package->sale_module,
                'all_sales' => (int)$package->add_sale,
                'add_sale' => (int)$package->add_sale,
                'list_pos' => (int)$package->list_pos,
                'pos_sale' => (int)$package->pos_sale,
                'list_draft' => (int)$package->list_draft,
                'list_quotation' => (int)$package->list_quotation,
                'list_sell_return' => (int)$package->list_sell_return,
                'shipment' => (int)$package->shipment,
                'discount' => (int)$package->discount,
                'import_sale' => (int)$package->import_sale,
                'reserved_stock' => (int)$package->reserved_stock,
                'stock_adjustment' => (int)$package->stock_adjustment,
                'tables' => (int)$package->tables,
                'type_of_service' => (int)$package->type_of_service,
                'expenses' => (int)$package->expenses,
                'modifiers' => (int)$package->modifiers,
                'kitchen' => (int)$package->kitchen,
                'upload_images' => (int)$package->upload_images,
                'leads_module' => (int)$package->leads_module,
                'leads' => (int)$package->leads,
                'day_count' => (int)$package->day_count,
                'leads_import' => (int)$package->leads_import,
                'leads_settings' => (int)$package->leads_settings,
                'sms_module' => (int)$package->sms_module,
                'cache_clear' => (int)$package->cache_clear,
                'pump_operator_dashboard' => (int)$package->pump_operator_dashboard,
                'list_sms' => (int)$package->list_sms,
                'status_order' => (int)$package->status_order,
                'list_orders' => (int)$package->list_orders,
                'upload_orders' => (int)$package->upload_orders,
                'subcriptions' => (int)$package->subcriptions,
                'over_limit_sales' => (int)$package->over_limit_sales,
                'repair_module' => (int)$package->repair_module,
                'job_sheets' => (int)$package->job_sheets,
                'add_job_sheet' => (int)$package->add_job_sheet,
                'list_invoice' => (int)$package->list_invoice,
                'add_invoice' => (int)$package->add_invoice,
                'brands' => (int)$package->brands,
                'repair_settings' => (int)$package->repair_settings,
                'payday' => (int)$package->payday
            ];
            
        }
        
        
        $addto = Package::getPackagePeriodInDays($package);
        $interval = ucfirst($package->interval);
        $length = $package->interval_count;
        $module_activation_data = [];
        
        
        if($subscription['package_details']['mf_module'] == 1){
            $module_activation_data['mf_length'] = $length;
            $module_activation_data['mf_interval'] = $interval;
            $module_activation_data['mf_activated_on'] = date('Y-m-d');
            $module_activation_data['mf_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        if($subscription['package_details']["access_account"] == 1){
            $module_activation_data['ac_length'] = $length;
            $module_activation_data['ac_interval'] = $interval;
            $module_activation_data['ac_activated_on'] = date('Y-m-d');
            $module_activation_data['ac_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        if($subscription['package_details']["access_module"] == 0){
            $module_activation_data['access_module_activated_on'] = date('Y-m-d');
            $module_activation_data['access_module_interval'] = $interval;
            $module_activation_data['access_module_length'] = $length;
            $module_activation_data['access_module_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        
        }
        
        if($subscription['package_details']["hr_module"] == 1){
            $module_activation_data['hr_length'] = $length;
            $module_activation_data['hr_interval'] = $interval;
            $module_activation_data['hr_activated_on'] = date('Y-m-d');
            $module_activation_data['hr_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        if($subscription['package_details']["visitors_registration_module"] == 1){
        
            $module_activation_data['vreg_length'] = $length;
            $module_activation_data['vreg_interval'] = $interval;
            $module_activation_data['vreg_activated_on'] = date('Y-m-d');
            $module_activation_data['vreg_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        
        }
        
        
        if( $subscription['package_details']["enable_petro_module"] == 1){
            $module_activation_data['petro_length'] = $length;
            $module_activation_data['petro_interval'] = $interval;
            $module_activation_data['petro_activated_on'] = date('Y-m-d');
            $module_activation_data['petro_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if( $subscription['package_details']["repair_module"] == 1){
            $module_activation_data['repair_length'] = $length;
            $module_activation_data['repair_interval'] = $interval;
            $module_activation_data['repair_activated_on'] = date('Y-m-d');
            $module_activation_data['repair_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if( $subscription['package_details']["fleet_module"] == 1){
            $module_activation_data['fleet_length'] = $length;
            $module_activation_data['fleet_interval'] = $interval;
            $module_activation_data['fleet_activated_on'] = date('Y-m-d');
            $module_activation_data['fleet_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["mpcs_module"] == 1){
            $module_activation_data['mpcs_length'] = $length;
            $module_activation_data['mpcs_interval'] = $interval;
            $module_activation_data['mpcs_activated_on'] = date('Y-m-d');
            $module_activation_data['mpcs_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["backup_module"] == 1){
            $module_activation_data['backup_length'] = $length;
            $module_activation_data['backup_interval'] = $interval;
            $module_activation_data['backup_activated_on'] = date('Y-m-d');
            $module_activation_data['backup_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["property_module"] == 1){
            $module_activation_data['property_length'] = $length;
            $module_activation_data['property_interval'] = $interval;
            $module_activation_data['property_activated_on'] = date('Y-m-d');
            $module_activation_data['property_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["auto_services_and_repair_module"] == 1){
            $module_activation_data['auto_length'] = $length;
            $module_activation_data['auto_interval'] = $interval;
            $module_activation_data['auto_activated_on'] = date('Y-m-d');
            $module_activation_data['auto_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["contact_module"] == 1){
            $module_activation_data['contact_length'] = $length;
            $module_activation_data['contact_interval'] = $interval;
            $module_activation_data['contact_activated_on'] = date('Y-m-d');
            $module_activation_data['contact_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        // if($subscription['package_details']["ran_module"] == 1){
        //     $module_activation_data['ran_length'] = $length;
        //     $module_activation_data['ran_interval'] = $interval;
        //     $module_activation_data['ran_activated_on'] = date('Y-m-d');
        //     $module_activation_data['ran_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        // }
        
        
        if($subscription['package_details']["report_module"] == 1){
            $module_activation_data['report_length'] = $length;
            $module_activation_data['report_interval'] = $interval;
            $module_activation_data['report_activated_on'] = date('Y-m-d');
            $module_activation_data['report_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["settings_module"] == 1){
            $module_activation_data['settings_length'] = $length;
            $module_activation_data['settings_interval'] = $interval;
            $module_activation_data['settings_activated_on'] = date('Y-m-d');
            $module_activation_data['settings_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["user_management_module"] == 1){
            $module_activation_data['um_length'] = $length;
            $module_activation_data['um_interval'] = $interval;
            $module_activation_data['um_activated_on'] = date('Y-m-d');
            $module_activation_data['um_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if( $subscription['package_details']["banking_module"] == 1){
        
            $module_activation_data['banking_length'] = $length;
            $module_activation_data['banking_interval'] = $interval;
            $module_activation_data['banking_activated_on'] = date('Y-m-d');
            $module_activation_data['banking_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["sale_module"] == 1){
            $module_activation_data['sale_length'] = $length;
            $module_activation_data['sale_interval'] = $interval;
            $module_activation_data['sale_activated_on'] = date('Y-m-d');
            $module_activation_data['sale_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if( $subscription['package_details']["leads_module"]== 1){
            $module_activation_data['leads_length'] = $length;
            $module_activation_data['leads_interval'] = $interval;
            $module_activation_data['leads_activated_on'] = date('Y-m-d');
            $module_activation_data['leads_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if( $subscription['package_details']["hospital_system"] == 1){
            $module_activation_data['hospital_length'] = $length;
            $module_activation_data['hospital_interval'] = $interval;
            $module_activation_data['hospital_activated_on'] = date('Y-m-d');
            $module_activation_data['hospital_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["enable_restaurant"] == 1){
        
            $module_activation_data['restaurant_length'] = $length;
            $module_activation_data['restaurant_interval'] = $interval;
            $module_activation_data['restaurant_activated_on'] = date('Y-m-d');
            $module_activation_data['restaurant_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["enable_duplicate_invoice"] == 1){
            $module_activation_data['duplicate_invoice_length'] = $length;
            $module_activation_data['duplicate_invoice_interval'] = $interval;
            $module_activation_data['duplicate_invoice_activated_on'] = date('Y-m-d');
            $module_activation_data['duplicate_invoice_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if( $subscription['package_details']["tasks_management"] == 1){
            $module_activation_data['tasks_length'] = $length;
            $module_activation_data['tasks_interval'] = $interval;
            $module_activation_data['tasks_activated_on'] = date('Y-m-d');
            $module_activation_data['tasks_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["enable_cheque_writing"] == 1){
            $module_activation_data['cheque_length'] = $length;
            $module_activation_data['cheque_interval'] = $interval;
            $module_activation_data['cheque_activated_on'] = date('Y-m-d');
            $module_activation_data['cheque_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if( $subscription['package_details']["list_easy_payment"] == 1){
            $module_activation_data['list_easy_length'] = $length;
            $module_activation_data['list_easy_interval'] = $interval;
            $module_activation_data['list_easy_activated_on'] = date('Y-m-d');
            $module_activation_data['list_easy_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        
        if($subscription['package_details']["pump_operator_dashboard"] == 1){
            $module_activation_data['pump_length'] = $length;
            $module_activation_data['pump_interval'] = $interval;
            $module_activation_data['pump_activated_on'] = date('Y-m-d');
            $module_activation_data['pump_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        if($subscription['package_details']["stock_taking_module"] == 1){
            $module_activation_data['stock_taking_length'] = $length;
            $module_activation_data['stock_taking_interval'] = $interval;
            $module_activation_data['stock_taking_activated_on'] = date('Y-m-d');
            $module_activation_data['stock_taking_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }
        
        if($subscription['package_details']["installment_module"] == 1){
            $module_activation_data['installment_module_length'] = $length;
            $module_activation_data['installment_module_interval'] = $interval;
            $module_activation_data['installment_module_activated_on'] = date('Y-m-d');
            $module_activation_data['installment_module_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
        
        }

        

        //Custom permissions.
        if (!empty($package->custom_permissions)) {
            foreach ($package->custom_permissions as $name => $value) {
                $subscription['package_details'][$name] = $value;
            }
        }
        
        $subscription['module_activation_details'] = json_encode($module_activation_data);

        $subscription['created_id'] = $user_id;

        $subscription = Subscription::create($subscription);

        //visible all account if account module is enable
        if (!empty($subscription['package_details']['access_account'])) {
            Account::where('business_id', $business_id)->update(['visible' => 1]);
        }

        if (!$is_superadmin && $gateway != 'payhere' && $trial_used != 0) {
            $email = System::getProperty('email');
            $is_notif_enabled = System::getProperty('enable_new_subscription_notification');

            if (!empty($email) && $is_notif_enabled == 1 && $subscription->status == 'approved') {
                Notification::route('mail', $email)
                    ->notify(new NewSubscriptionNotification($subscription));
            }
        }

        return $subscription;
    }

    protected function check_module_selected($key, $module_selected, $package, $is_company_pacakge, $already_pacakge)
    {
        if ($is_company_pacakge == 1) {
            if (in_array($key, $module_selected)) {
                return 1;
            }
            if (!empty($already_pacakge)) {
                if ($key == 'mf_module') {
                    return $already_pacakge->manufacturer;
                }
                if ($key == 'enable_sale_cmsn_agent') {
                    return $already_pacakge->sales_commission_agent;
                }
                if ($key == 'enable_restaurant') {
                    return $already_pacakge->restaurant;
                }
                if ($key == 'enable_booking') {
                    return $already_pacakge->booking;
                }
                if ($key == 'enable_crm') {
                    return $already_pacakge->crm_enable;
                }
                if ($key == 'enable_sms') {
                    return $already_pacakge->sms_enable;
                }
                if ($key == 'hr_module') {
                    return $already_pacakge->hr_module;
                }
            }
        }
        if ($key == 'mf_module') {
            return $package->manufacturer;
        }
        if ($key == 'enable_sale_cmsn_agent') {
            return $package->sales_commission_agent;
        }
        if ($key == 'enable_restaurant') {
            return $package->restaurant;
        }
        if ($key == 'enable_booking') {
            return $package->booking;
        }
        if ($key == 'enable_crm') {
            return $package->crm_enable;
        }
        if ($key == 'enable_sms') {
            return $package->sms_enable;
        }
        if ($key == 'hr_module') {
            return $package->hr_module;
        }

        return $package->$key;
    }

    protected function getoption_value($option, $option_variables_selected, $is_company_pacakge, $already_pacakge)
    {
        if ($is_company_pacakge == 1) {
            foreach ($option_variables_selected as $opt_var) {
                $value = PackageVariable::where('id', $opt_var)->where('is_company_variable', 1)->first();
                if ($value->variable_options == $option) {
                    return $value->option_value;
                }
            }
        } else {
            foreach ($option_variables_selected as $opt_var) {
                $value = PackageVariable::where('id', $opt_var)->where('is_company_variable', 0)->first();
                if ($value->variable_options == $option) {
                    return $value->option_value;
                }
            }
        }

        if (!empty($already_pacakge)) {
            if ($option == 0) {
                return $already_pacakge->location_count;
            }
            if ($option == 1) {
                return $already_pacakge->user_count;
            }
            if ($option == 2) {
                return $already_pacakge->product_count;
            }
            if ($option == 3) {
                return $already_pacakge->period_count;
            }
            if ($option == 4) {
                return $already_pacakge->customer_count;
            }
        }

        return 1;
    }
    /**
     * The function returns the start/end/trial end date for a package.
     *
     * @param int $business_id
     * @param object $package
     *
     * @return array
     */
    protected function _get_package_dates($business_id, $package)
    {
        $output = ['start' => '', 'end' => '', 'trial' => ''];

        //calculate start date
        $start_date = Subscription::end_date($business_id);
        $start_date = $start_date->subDays(1);
        $output['start'] = $start_date->toDateString();

        //Calculate end date
        if ($package->interval == 'days') {
            $output['end'] = $start_date->addDays($package->interval_count)->toDateString();
        } elseif ($package->interval == 'months') {
            $output['end'] = $start_date->addMonths($package->interval_count)->toDateString();
        } elseif ($package->interval == 'years') {
            $output['end'] = $start_date->addYears($package->interval_count)->toDateString();
        }

        //if company already used the trial period then no need to add trial again
        $trial_used = Business::where('id', $business_id)->first()->trial_used;
        if ($trial_used == 0) {
            $st_date = \Carbon::parse($output['start']);
            $output['trial'] =  $st_date->addDays($package->trial_days)->toDateString();
        } else {
            $output['trial'] = NULL;
        }

        return $output;
    }
}
