<?php

namespace Modules\Superadmin\Http\Controllers;


use App\Business;
use App\System;
use App\PackageVariable;
use App\Utils\BusinessUtil;
use App\Utils\ModuleUtil;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Modules\Superadmin\Entities\Package;
use Modules\Superadmin\Entities\Subscription;
use Illuminate\Support\Facades\DB;
use Modules\Superadmin\Entities\PayOnline;
use App\Currency;

class PackagesController extends BaseController
{
    /**
     * All Utils instance.
     *
     */
    protected $businessUtil;
    protected $moduleUtil;

    /**
     * Constructor
     *
     * @param ProductUtils $product
     * @return void
     */
    public function __construct(BusinessUtil $businessUtil, ModuleUtil $moduleUtil)
    {
        $this->businessUtil = $businessUtil;
        $this->moduleUtil = $moduleUtil;
    }

    /**
     * Display a listing of the resource.
     * @return Response
     */
    public function index()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        // $create_individual_company_package = System::getProperty('create_individual_company_package');
        $query = Package::orderby('sort_order', 'asc');
        // if ($create_individual_company_package != 'yes') {
        //     $query->whereNull('only_for_business');
        // }
        
        $packages = $query->paginate(20);
        //Get all module permissions and convert them into name => label
        $permissions = $this->moduleUtil->getModuleData('superadmin_package');
        $permission_formatted = [];
        foreach ($permissions as $permission) {
            foreach ($permission as $details) {
                $permission_formatted[$details['name']] = $details['label'];
            }
        }
        
        
        $PAY_ONLINE_CURRENCY_TYPE = json_decode(System::getProperty('PAY_ONLINE_CURRENCY_TYPE'), true);
        $status = PayOnline::payment_status();
        $pay_online_nos = PayOnline::pluck('pay_online_no', 'id');
        $names = PayOnline::select('id', DB::raw("concat(first_name, ' ',  last_name) as name"))->pluck('name', 'id');
        $currencies = Currency::whereIn('id', $PAY_ONLINE_CURRENCY_TYPE)->select('code', DB::raw("concat(country, ' - ',currency, '(', code, ') ') as info"))
            ->orderBy('country')
            ->pluck('info', 'code');
            
        $modules = collect($this->getModules())->mapWithKeys(function ($item) {
                        return [$item => $item];
                    })->toArray();

        $businesses = Business::pluck('name', 'id');
        
        
        
        return view('superadmin::packages.index')
            ->with(compact('packages', 'permission_formatted','status',
            'names',
            'pay_online_nos',
            'currencies','modules','businesses'));
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
                    "Installment Module",
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
                'installment_price',
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
                'installment_expiry_date',
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
                'installment_activated_on',
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
     * Show the form for creating a new resource.
     * @return Response
     */
    public function create()
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        $intervals = ['days' => __('lang_v1.days'), 'months' => __('lang_v1.months'), 'years' => __('lang_v1.years')];
        $currency = System::getCurrency();
        $currencies = $this->businessUtil->allCurrencies();
        $permissions = $this->moduleUtil->getModuleData('superadmin_package');
        $package_variables = PackageVariable::all();
        $all_variable_options = ['Number of Branches', 'Number of Users', 'Number of Products', 'Number of Periods', 'Monthly Total Sales', 'No of Family Members'];
        $all_increase_decrease = ['Increase', 'Decrease'];
        $all_variable_type = ['Fixed', 'Percentage'];
        $default_number_of_customers = System::getProperty('default_number_of_customers');
        return view('superadmin::packages.create')
            ->with(compact('default_number_of_customers', 'intervals', 'currency', 'permissions', 'currencies', 'package_variables', 'all_variable_options', 'all_increase_decrease', 'all_variable_type'));
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Response
     */
    public function store(Request $request)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            $input = $request->only([
                'visit_count', 'option_variables',
                'name', 'description', 'location_count', 'customer_count', 'employee_count', 'user_count', 'product_count', 'invoice_count', 'vehicle_count', 'store_count', 'interval', 'interval_count', 'trial_days', 'price', 'sort_order', 'is_active', 'custom_permissions', 'is_private', 'is_one_time', 'enable_custom_link', 'custom_link',
                'custom_link_text', 'currency_id'
            ]);
            $input['location_count'] = !empty($input['location_count']) ? $input['location_count'] : 0;
            $input['user_count'] = !empty($input['user_count']) ? $input['user_count'] : 0;
            $input['product_count'] = !empty($input['product_count']) ? $input['product_count'] : 0;
            $input['invoice_count'] = !empty($input['invoice_count']) ? $input['invoice_count'] : 0;
            $input['store_count'] = !empty($input['store_count']) ? $input['store_count'] : 0;
            $input['employee_count'] = !empty($input['employee_count']) ? $input['employee_count'] : 0;
            $input['vehicle_count'] = !empty($input['vehicle_count']) ? $input['vehicle_count'] : 0;
            $input['option_variables'] = !empty($input['option_variables']) ? json_encode($input['option_variables']) : '[]';
            $input['max_disk_size'] = $request->input('max_disk_size');
            if (empty($request->sales_commission_agent)) {
                $input['sales_commission_agent'] = 0;
            } else {
                $input['sales_commission_agent'] = 1;
            }
            if ($request->payday == '1') {
                $input['payday'] = 1;
            } else {
                $input['payday'] = 0;
            }
            if ($request->restaurant == '1') {
                $input['restaurant'] = 1;
            } else {
                $input['restaurant'] = 0;
            }
            if ($request->booking == '1') {
                $input['booking'] = 1;
            } else {
                $input['booking'] = 0;
            }
            if ($request->manufacturer == '1') {
                $input['manufacturer'] = 1;
            } else {
                $input['manufacturer'] = 0;
            }
            if ($request->sms_enable == '1') {
                $input['sms_enable'] = 1;
            } else {
                $input['sms_enable'] = 0;
            }
            if (empty($request->crm_enable)) {
                $input['crm_enable'] = 0;
            } else {
                $input['crm_enable'] = 1;
            }
            if (empty($request->hr_module)) {
                $input['hr_module'] = 0;
            } else {
                $input['hr_module'] = 1;
            }
            if (empty($request->hospital_system)) {
                $input['hospital_system'] = 0;
            } else {
                $input['hospital_system'] = 1;
            }
            if (empty($request->enable_duplicate_invoice)) {
                $input['enable_duplicate_invoice'] = 0;
            } else {
                $input['enable_duplicate_invoice'] = 1;
            }
            if (empty($request->petro_module)) {
                $input['petro_module'] = 0;
                $input['meter_resetting'] = 0;
                /**
                 * @author: Afes Oktavianus
                 * @since: 23-08-2021
                 * @req: 8413 - Package Permission for Petro Module
                 */
                $input['petro_dashboard'] = 0;
                $input['petro_task_management'] = 0;
                $input['pump_management'] = 0;
                $input['pump_management_testing'] = 0;
                $input['meter_reading'] = 0;
                $input['pump_dashboard_opening'] = 0;
                $input['pumper_management'] = 0;
                $input['daily_collection'] = 0;
                $input['settlement'] = 0;
                $input['list_settlement'] = 0;
                $input['delete_settlement'] = 0;

                $input['dip_management'] = 0;
                $input['fuel_tanks_edit'] = 0;
                $input['fuel_tanks_delete'] = 0;
                $input['pumps_edit'] = 0;
                $input['pumps_delete'] = 0;

            } else {
                $input['petro_module'] = 1;
                $input['meter_resetting'] = 1;
                /**
                 * @author: Afes Oktavianus
                 * @since: 23-08-2021
                 * @req: 8413 - Package Permission for Petro Module
                 */
                $input['petro_dashboard'] = $request->petro_dashboard == '1' ? 1 : 0;
                $input['petro_task_management'] = $request->petro_task_management == '1' ? 1 : 0;
                $input['pump_management'] = $request->pump_management == '1' ? 1 : 0;
                $input['pump_management_testing'] = $request->pump_management_testing == '1' ? 1 : 0;
                $input['meter_reading'] = $request->meter_reading == '1' ? 1 : 0;
                $input['pump_dashboard_opening'] = $request->pump_dashboard_opening == '1' ? 1 : 0;
                $input['pumper_management'] = $request->pumper_management == '1' ? 1 : 0;
                $input['daily_collection'] = $request->daily_collection == '1' ? 1 : 0;
                $input['settlement'] = $request->settlement == '1' ? 1 : 0;
                $input['list_settlement'] = $request->list_settlement == '1' ? 1 : 0;
                $packages_details['delete_settlement'] = $request->delete_settlement == '1' ? 1 : 0;

                $input['dip_management'] = $request->dip_management == '1' ? 1 : 0;
                $input['fuel_tanks_edit'] =$request->fuel_tanks_edit == '1' ? 1 : 0;
                $input['fuel_tanks_delete'] =$request->fuel_tanks_delete == '1' ? 1 : 0;
                $input['pumps_edit'] =$request->pumps_edit == '1' ? 1 : 0;
                $input['pumps_delete'] =$request->pumps_delete == '1' ? 1 : 0;
            }
            if (empty($request->pump_operator_dashboard)) {
                $input['pump_operator_dashboard'] = 0;
            } else {
                $input['pump_operator_dashboard'] = 1;
            }
            if (empty($request->account_access)) {
                $package_permissions['account_access'] = 0;
            } else {
                $package_permissions['account_access'] = 1;
            }
            if (empty($request->sms_settings_access)) {
                $package_permissions['sms_settings_access'] = 0;
            } else {
                $package_permissions['sms_settings_access'] = 1;
            }
            if (empty($request->module_access)) {
                $package_permissions['module_access'] = 0;
            } else {
                $package_permissions['module_access'] = 1;
            }
            if (empty($request->customer_order_own_customer)) {
                $input['customer_order_own_customer'] = 0;
            } else {
                $input['customer_order_own_customer'] = 1;
            }
            if (empty($request->customer_order_general_customer)) {
                $input['customer_order_general_customer'] = 0;
            } else {
                $input['customer_order_general_customer'] = 1;
            }
            if (empty($request->mpcs_module)) {
                $input['mpcs_module'] = 0;
            } else {
                $input['mpcs_module'] = 1;
            }
            
            if (empty($request->installment_module)) {
                $input['installment_module'] = 0;
            } else {
                $input['installment_module'] = 1;
            }
            
            if (empty($request->fleet_module)) {
                $input['fleet_module'] = 0;
            } else {
                $input['fleet_module'] = 1;
            }
            if (empty($request->ezyboat_module)) {
                $input['ezyboat_module'] = 0;
            } else {
                $input['ezyboat_module'] = 1;
            }
            if ($request->number_of_branches == '1') {
                $input['number_of_branches'] = 1;
            } else {
                $input['number_of_branches'] = 0;
            }
            if ($request->number_of_users == '1') {
                $input['number_of_users'] = 1;
            } else {
                $input['number_of_users'] = 0;
            }
            if ($request->number_of_products == '1') {
                $input['number_of_products'] = 1;
            } else {
                $input['number_of_products'] = 0;
            }
            if ($request->number_of_periods == '1') {
                $input['number_of_periods'] = 1;
            } else {
                $input['number_of_periods'] = 0;
            }
            if ($request->number_of_customers == '1') {
                $input['number_of_customers'] = 1;
            } else {
                $input['number_of_customers'] = 0;
            }
            if ($request->monthly_total_sales == '1') {
                $input['monthly_total_sales'] = 1;
            } else {
                $input['monthly_total_sales'] = 0;
            }
            if ($request->no_of_family_members == '1') {
                $input['no_of_family_members'] = 1;
            } else {
                $input['no_of_family_members'] = 0;
            }
            if ($request->no_of_vehicles == '1') {
                $input['no_of_vehicles'] = 1;
            } else {
                $input['no_of_vehicles'] = 0;
            }
            if (empty($request->customer_interest_deduct_option)) {
                $input['customer_interest_deduct_option'] = 0;
            } else {
                $input['customer_interest_deduct_option'] = 1;
            }
            $input['leads_module'] = $request->leads_module == '1' ? 1 : 0;
            /**
             * @author: Afes Oktavianus
             * @since: 23-08-2021
             * @req: 3413 - Package Permission for Petro Module
             */
            if (empty($request->contact_module)) {
                $input['contact_module'] = 0;
                $input['contact_supplier'] = 0;
                $input['contact_customer'] = 0;
                $input['contact_group_customer'] = 0;
                $input['contact_group_supplier'] = 0;
                $input['import_contact'] = 0;
                $input['customer_reference'] = 0;
                $input['customer_statement'] = 0;
                $input['customer_payment'] = 0;
                $input['outstanding_received'] = 0;
                $input['issue_payment_detail'] = 0;

                $input['edit_received_outstanding'] = 0;
                $input['stock_taking_page'] = 0;
            } else {
                $input['contact_module'] = 1;
                $input['contact_supplier'] = $request->contact_supplier == '1' ? 1 : 0;
                $input['contact_customer'] = $request->contact_customer == '1' ? 1 : 0;
                $input['contact_group_customer'] = $request->contact_group_customer == '1' ? 1 : 0;
                $input['contact_group_supplier'] = $request->contact_group_supplier == '1' ? 1 : 0;
                $input['import_contact'] = $request->import_contact == '1' ? 1 : 0;
                $input['customer_reference'] = $request->customer_reference == '1' ? 1 : 0;
                $input['customer_statement'] = $request->customer_statement == '1' ? 1 : 0;
                $input['customer_payment'] = $request->customer_payment == '1' ? 1 : 0;
                $input['outstanding_received'] = $request->outstanding_received == '1' ? 1 : 0;
                $input['issue_payment_detail'] = $request->issue_payment_detail == '1' ? 1 : 0;

                $input['edit_received_outstanding'] = $request->edit_received_outstanding == '1' ? 1 : 0;
                $input['stock_taking_page'] = $request->stock_taking_page == '1' ? 1 : 0;
            }
            
            $input['products'] = $request->products == '1' ? 1 : 0;
            $input['issue_customer_bill'] = $request->issue_customer_bill == '1' ? 1 : 0;
            $input['purchase'] = $request->purchase == '1' ? 1 : 0;
            $input['sale_module'] = $request->sale_module == '1' ? 1 : 0;
            $input['pos_sale'] = $request->pos_sale == '1' ? 1 : 0;
            $input['repair_module'] = $request->repair_module == '1' ? 1 : 0;
            $input['auto_services_and_repair_module'] = $request->auto_services_and_repair_module == '1' ? 1 : 0;
            $input['patient_module'] = $request->patient_module == '1' ? 1 : 0;
            $input['patient_test_module'] = $request->patient_test_module == '1' ? 1 : 0;
            $input['restore_module'] = $request->restore_module == '1' ? 1 : 0;
            $input['installment_module'] = $request->installment_module == '1' ? 1 : 0;
            $input['stock_taking_module'] = $request->stock_taking_module == '1' ? 1 : 0;
            $input['stock_transfer'] = $request->stock_transfer == '1' ? 1 : 0;
            $input['expenses'] = $request->expenses == '1' ? 1 : 0;
            $input['tasks_management'] = $request->tasks_management == '1' ? 1 : 0;
            /**
             * @author: Afes Oktavianus
             * @since: 23-08-2021
             * @req: 8413 - Package Permission for Petro Module
             */
            if (empty($request->report_module)) {
                $input['report_module'] = 0;
                $input['product_report'] = 0;
                $input['payment_status_report'] = 0;
                $input['report_daily'] = 0;
                $input['report_daily_summary'] = 0;
                $input['report_profit_loss'] = 0;
                $input['report_credit_status'] = 0;
                $input['activity_report'] = 0;
                $input['contact_report'] = 0;
                $input['trending_product'] = 0;
                $input['user_activity'] = 0;
                $input['report_verification'] = 0;
                $input['report_table'] = 0;
                $input['report_staff_service'] = 0;
                $input['report_verification'] = 0;
                $input['report_table'] = 0;
                $input['report_staff_service'] = 0;
                $input['report_register'] = 0;
            } else {
                $input['report_module'] = 1;
                $input['product_report'] = $request->product_report == '1' ? 1 : 0;
                $input['payment_status_report'] = $request->payment_status_report == '1' ? 1 : 0;
                $input['report_daily'] = $request->report_daily == '1' ? 1 : 0;
                $input['report_daily_summary'] = $request->report_daily_summary == '1' ? 1 : 0;
                $input['report_profit_loss'] = $request->report_profit_loss == '1' ? 1 : 0;
                $input['report_credit_status'] = $request->report_credit_status == '1' ? 1 : 0;
                $input['activity_report'] = $request->activity_report == '1' ? 1 : 0;
                $input['contact_report'] = $request->contact_report == '1' ? 1 : 0;
                $input['trending_product'] = $request->trending_product == '1' ? 1 : 0;
                $input['user_activity'] = $request->user_activity == '1' ? 1 : 0;
                $input['report_verification'] = $request->report_verification == '1' ? 1 : 0;
                $input['report_table'] = $request->report_table == '1' ? 1 : 0;
                $input['report_staff_service'] = $request->report_staff_service == '1' ? 1 : 0;
                $input['report_register'] = $request->report_register == '1' ? 1 : 0;
            }
            $input['catalogue_qr'] = $request->catalogue_qr == '1' ? 1 : 0;
            $input['backup_module'] = $request->backup_module == '1' ? 1 : 0;
            $input['notification_template_module'] = $request->notification_template_module == '1' ? 1 : 0;
            $input['member_registration'] = $request->member_registration == '1' ? 1 : 0;
            $input['user_management_module'] = $request->user_management_module == '1' ? 1 : 0;
            $input['banking_module'] = $request->banking_module == '1' ? 1 : 0;
            $input['list_easy_payment'] = $request->list_easy_payment == '1' ? 1 : 0;
            $input['settings_module'] = $request->settings_module == '1' ? 1 : 0;
            $input['business_settings'] = $request->business_settings == '1' ? 1 : 0;
            $input['business_location'] = $request->business_location == '1' ? 1 : 0;
            $input['invoice_settings'] = $request->invoice_settings == '1' ? 1 : 0;
            $input['settings_otp_verification'] = $request->settings_otp_verification == '1' ? 1 : 0;
            $input['settings_pay_online'] = $request->settings_pay_online == '1' ? 1 : 0;
            $input['settings_reports_configurations'] = $request->settings_reports_configurations == '1' ? 1 : 0;
            $input['settings_user_locations'] = $request->settings_user_locations == '1' ? 1 : 0;
            $input['tax_rates'] = $request->tax_rates == '1' ? 1 : 0;
            $input['home_dashboard'] = $request->home_dashboard == '1' ? 1 : 0;
            $input['day_end_enable'] = $request->day_end_enable == '1' ? 1 : 0;
            /**
             * @author: Afes Oktavianus
             * @since 24-08-2021
             * @req 8413 - Package Permission for Petro Module
             */
            if (empty($request->sale_module)) {
                $input['sale_module'] = 0;
                $input['all_sales'] = 0;
                $input['add_sale'] = 0;
                $input['list_pos'] = 0;
                $input['list_draft'] = 0;
                $input['list_quotation'] = 0;
                $input['list_sell_return'] = 0;
                $input['shipment'] = 0;
                $input['discount'] = 0;
                $input['reserved_stock'] = 0;
                $input['import_sale'] = 0;
                $input['upload_orders'] = 0;
                $input['list_orders'] = 0;
                $input['pos_button_on_top_belt'] = 0;
                $input['pos_sale'] = 0;
            } else {
                $input['sale_module'] = 1;
                $input['all_sales'] = $request->all_sales == '1' ? 1 : 0;
                $input['add_sale'] = $request->add_sale == '1' ? 1 : 0;
                $input['list_pos'] = $request->list_pos == '1' ? 1 : 0;
                $input['list_draft'] = $request->list_draft == '1' ? 1 : 0;
                $input['list_quotation'] = $request->list_quotation == '1' ? 1 : 0;
                $input['list_sell_return'] = $request->list_sell_return == '1' ? 1 : 0;
                $input['shipment'] = $request->shipment == '1' ? 1 : 0;
                $input['discount'] = $request->discount == '1' ? 1 : 0;
                $input['reserved_stock'] = $request->reserved_stock == '1' ? 1 : 0;
                $input['import_sale'] = $request->import_sale == '1' ? 1 : 0;
                $input['upload_orders'] = $request->upload_order == '1' ? 1 : 0;
                $input['list_orders'] = $request->list_order == '1' ? 1 : 0;
                $input['pos_button_on_top_belt'] = $request->pos_button_on_top_belt == '1' ? 1 : 0;
                $input['pos_sale'] = $request->pos_ == '1' ? 1 : 0;
            }


            /* Purchase Module */

            if (empty($request->purchase_module)) {
                $input['purchase_module'] = 0;
                $input['all_purchase'] = 0;
                $input['add_purchase'] = 0;
                $input['import_purchase'] = 0;
                $input['add_bulk_purchase'] = 0;
                $input['pop_button_on_top_belt'] = 0;
                $input['purchase_return'] = 0;
            } else {
                $input['purchase_module'] = 1;
                $input['all_purchase'] = $request->all_purchase == '1' ? 1 : 0;
                $input['add_purchase'] = $request->add_purchase == '1' ? 1 : 0;
                $input['import_purchase'] = $request->import_purchase == '1' ? 1 : 0;
                $input['add_bulk_purchase'] = $request->add_bulk_purchase == '1' ? 1 : 0;
                $input['pop_button_on_top_belt'] = $request->pop_button_on_top_belt == '1' ? 1 : 0;
                $input['purchase_return'] = $request->purchase_return == '1' ? 1 : 0;
            }
            /* Cheque Writing Module */

            if (empty($request->cheque_write_module)) {
                $input['cheque_write_module'] = 0;
                $input['cheque_templates'] = 0;
                $input['write_cheque'] = 0;
                $input['manage_stamps'] = 0;
                $input['manage_payee'] = 0;
                $input['cheque_number_list'] = 0;
                $input['deleted_cheque_details'] = 0;
                $input['printed_cheque_details'] = 0;
                $input['default_setting'] = 0;
            } else {
                $input['cheque_write_module'] = 1;
                $input['cheque_templates'] = $request->cheque_templates == '1' ? 1 : 0;
                $input['write_cheque'] = $request->write_cheque == '1' ? 1 : 0;
                $input['manage_stamps'] = $request->manage_stamps == '1' ? 1 : 0;
                $input['manage_payee'] = $request->manage_payee == '1' ? 1 : 0;
                $input['cheque_number_list'] = $request->cheque_number_list == '1' ? 1 : 0;
                $input['deleted_cheque_details'] = $request->deleted_cheque_details == '1' ? 1 : 0;
                $input['printed_cheque_details'] = $request->printed_cheque_details == '1' ? 1 : 0;
                $input['default_setting'] = $request->cheque_number_list == '1' ? 1 : 0;
            }
            if (empty($request->property_module)) {
                $input['property_module'] = 0;
            } else {
                $input['property_module'] = 1;
            }
            if (empty($request->visitors_registration_module)) {
                $input['visitors_registration_module'] = 0;
                $input['visitors'] = 0;
                $input['visitors_registration'] = 0;
                $input['visitors_registration_setting'] = 0;
                $input['visitors_district'] = 0;
                $input['visitors_town'] = 0;
                $input['disable_all_other_module_vr'] = 0;
            } else {
                $input['visitors_registration_module'] = 1;
                $input['visitors'] = 1;
                $input['visitors_registration'] = 1;
                $input['visitors_registration_setting'] = 1;
                $input['visitors_district'] = 1;
                $input['visitors_town'] = 1;
                $input['disable_all_other_module_vr'] = 1;
            }
            $currency = System::getCurrency();
            $input['price'] = $request->price;
            $input['is_active'] = empty($input['is_active']) ? 0 : 1;
            $input['created_by'] = $request->session()->get('user.id');
            $input['package_permissions'] = !empty($package_permissions) ? json_encode(($package_permissions)) : '';
            $input['is_private'] = empty($input['is_private']) ? 0 : 1;
            $input['is_one_time'] = empty($input['is_one_time']) ? 0 : 1;
            $input['visible'] = 1;
            $input['enable_custom_link'] = empty($input['enable_custom_link']) ? 0 : 1;
            $input['custom_link'] = empty($input['enable_custom_link']) ? '' : $input['custom_link'];
            $input['custom_link_text'] = empty($input['enable_custom_link']) ? '' : $input['custom_link_text'];
            $input['hospital_business_type'] = empty($request->hospital_business_type) ? '[]' : json_encode($request->hospital_business_type);
            $input['monthly_max_sale_limit'] = $request->monthly_max_sale_limit;
            $input['no_of_backup'] = $request->no_of_backup;
            $input['no_of_day'] = $request->no_of_day;
            if ($request->petro_quota_module == '1') {
                $input['petro_quota_module'] = 1;
            } else {
                $input['petro_quota_module'] = 0;
            }

            $business_id = request()->session()->get('user.business_id');
            $business = Business::where('id', $business_id)->first();
            if (empty($request->customer_interest_deduct_option)) {
                $business_details['customer_interest_deduct_option'] = 0;
            } else {
                $business_details['customer_interest_deduct_option'] = 1;
            }
            if (!empty($request->day_end_enable)) {
                $business_details['day_end_enable'] = $request->day_end_enable;
            } else {
                $business_details['day_end_enable'] = 0;
            }
            
            $business->fill($business_details);
            $business->save();
            $package = new Package;
            $package->fill($input);
            $package->save();

            $manage_stock_enable = $request->manage_stock_enable;
            if ($manage_stock_enable == 1) {
                \App\Business::query()
                    ->update(['is_manged_stock_enable' => 1]);
            }
            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }

        return redirect()
            ->action('\Modules\Superadmin\Http\Controllers\PackagesController@index')
            ->with('status', $output);
    }

    /**
     * Show the specified resource.
     * @return Response
     */
    public function show()
    {
        return view('superadmin::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @return Response
     */
    public function edit($id)
    {
        $packages = Package::where('id', $id)->first();
        $intervals = ['days' => __('lang_v1.days'), 'months' => __('lang_v1.months'), 'years' => __('lang_v1.years')];
        $currencies = $this->businessUtil->allCurrencies();
        $permissions = $this->moduleUtil->getModuleData('superadmin_package', true);
        $is_manage_stock_enable = \App\Business::where('id', auth()->user()->business_id)->value('is_manged_stock_enable');
        
        return view('superadmin::packages.edit')->with(compact('is_manage_stock_enable', 'packages', 'intervals', 'permissions', 'currencies'));
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @return Response
     */
    public function update(Request $request, $id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $packages_details = $request->only(['option_variables', 'visit_count', 'currency_id', 'name', 'id', 'description', 'location_count', 'customer_count', 'employee_count', 'user_count', 'product_count', 'invoice_count', 'vehicle_count', 'store_count', 'interval', 'interval_count', 'trial_days', 'sort_order', 'is_active', 'custom_permissions', 'is_private', 'is_one_time', 'enable_custom_link', 'custom_link', 'custom_link_text']);
            $packages_details['price'] = $request->price;
            $packages_details['is_active'] = empty($packages_details['is_active']) ? 0 : 1;
            $packages_details['custom_permissions'] = empty($packages_details['custom_permissions']) ? [] : $packages_details['custom_permissions'];
            $packages_details['package_permissions'] = !empty($request->package_permissions) ? json_encode(($request->package_permissions)) : '';
            $packages_details['is_private'] = empty($packages_details['is_private']) ? 0 : 1;
            $packages_details['is_one_time'] = empty($packages_details['is_one_time']) ? 0 : 1;
            $packages_details['visible'] = 1;
            $packages_details['enable_custom_link'] = empty($packages_details['enable_custom_link']) ? 0 : 1;
            $packages_details['custom_link'] = empty($packages_details['enable_custom_link']) ? '' : $packages_details['custom_link'];
            $packages_details['custom_link_text'] = empty($packages_details['enable_custom_link']) ? '' : $packages_details['custom_link_text'];
            $packages_details['hospital_business_type'] = empty($request->hospital_business_type) ? '[]' : json_encode($request->hospital_business_type);
            $packages_details['option_variables'] = !empty($packages_details['option_variables']) ? json_encode($packages_details['option_variables']) : '[]';
            $packages_details['max_disk_size'] = $request->input('max_disk_size');
            if ($request->payday == '1') {
                $packages_details['payday'] = 1;
            } else {
                $packages_details['payday'] = 0;
            }
            if ($request->sales_commission_agent == '1') {
                $packages_details['sales_commission_agent'] = 1;
            } else {
                $packages_details['sales_commission_agent'] = 0;
            }
            if ($request->restaurant == '1') {
                $packages_details['restaurant'] = 1;
            } else {
                $packages_details['restaurant'] = 0;
            }
            if ($request->booking == '1') {
                $packages_details['booking'] = 1;
            } else {
                $packages_details['booking'] = 0;
            }
            if (array_key_exists('manufacturing_module', $packages_details['custom_permissions'])) {
                $packages_details['manufacturer'] = 1;
            } else {
                $packages_details['manufacturer'] = 0;
            }
            if ($request->sms_enable == '1') {
                $packages_details['sms_enable'] = 1;
            } else {
                $packages_details['sms_enable'] = 0;
            }
            if ($request->crm_enable == '1') {
                $packages_details['crm_enable'] = 1;
            } else {
                $packages_details['crm_enable'] = 0;
            }
            if ($request->hr_module == '1') {
                $packages_details['hr_module'] = 1;
            } else {
                $packages_details['hr_module'] = 0;
            }
            if ($request->hospital_system == '1') {
                $packages_details['hospital_system'] = 1;
            } else {
                $packages_details['hospital_system'] = 0;
            }
            if ($request->enable_duplicate_invoice == '1') {
                $packages_details['enable_duplicate_invoice'] = 1;
            } else {
                $packages_details['enable_duplicate_invoice'] = 0;
            }
            if ($request->petro_quota_module == '1') {
                $packages_details['petro_quota_module'] = 1;
            } else {
                $packages_details['petro_quota_module'] = 0;
            }
            if (empty($request->petro_module)) {
                $packages_details['petro_module'] = 0;
                $packages_details['meter_resetting'] = 0;
                /**
                 * @author: Afes Oktavianus
                 * @since: 23-08-2021
                 * @req: 8413 - Package Permission for Petro Module
                 */
                $packages_details['petro_dashboard'] = 0;
                $packages_details['petro_task_management'] = 0;
                $packages_details['pump_management'] = 0;
                $packages_details['pump_management_testing'] = 0;
                $packages_details['meter_reading'] = 0;
                $packages_details['pump_dashboard_opening'] = 0;
                $packages_details['pumper_management'] = 0;
                $packages_details['daily_collection'] = 0;
                $packages_details['settlement'] = 0;
                $packages_details['list_settlement'] = 0;
                $packages_details['delete_settlement'] = 0;

                $packages_details['dip_management'] = 0;
                $packages_details['fuel_tanks_edit'] = 0;
                $packages_details['fuel_tanks_delete'] = 0;
                $packages_details['pumps_edit'] = 0;
                $packages_details['pumps_delete'] = 0;
            } else {
                $packages_details['petro_module'] = 1;
                $packages_details['meter_resetting'] = $request->meter_resetting == '1' ? 1 : 0;
                /**
                 * @author: Afes Oktavianus
                 * @since: 23-08-2021
                 * @req: 8413 - Package Permission for Petro Module
                 */
                $packages_details['petro_dashboard'] = $request->petro_dashboard == '1' ? 1 : 0;
                $packages_details['petro_task_management'] = $request->petro_task_management == '1' ? 1 : 0;
                $packages_details['pump_management'] = $request->pump_management == '1' ? 1 : 0;
                $packages_details['pump_management_testing'] = $request->pump_management_testing == '1' ? 1 : 0;
                $packages_details['meter_reading'] = $request->meter_reading == '1' ? 1 : 0;
                $packages_details['pump_dashboard_opening'] = $request->pump_dashboard_opening == '1' ? 1 : 0;
                $packages_details['pumper_management'] = $request->pumper_management == '1' ? 1 : 0;
                $packages_details['daily_collection'] = $request->daily_collection == '1' ? 1 : 0;
                $packages_details['settlement'] = $request->settlement == '1' ? 1 : 0;
                $packages_details['list_settlement'] = $request->list_settlement == '1' ? 1 : 0;
                $packages_details['delete_settlement'] = $request->delete_settlement == '1' ? 1 : 0;

                $packages_details['dip_management'] = $request->dip_management == '1' ? 1 : 0;
                $packages_details['fuel_tanks_edit'] =$request->fuel_tanks_edit == '1' ? 1 : 0;
                $packages_details['fuel_tanks_delete'] =$request->fuel_tanks_delete == '1' ? 1 : 0;
                $packages_details['pumps_edit'] =$request->pumps_edit == '1' ? 1 : 0;
                $packages_details['pumps_delete'] =$request->pumps_delete == '1' ? 1 : 0;
            }
            if ($request->pump_operator_dashboard == '1') {
                $packages_details['pump_operator_dashboard'] = 1;
            } else {
                $packages_details['pump_operator_dashboard'] = 0;
            }
            if (empty($request->account_access)) {
                $package_permissions['account_access'] = 0;
            } else {
                $package_permissions['account_access'] = 1;
            }
            if (empty($request->sms_settings_access)) {
                $package_permissions['sms_settings_access'] = 0;
            } else {
                $package_permissions['sms_settings_access'] = 1;
            }
            if (empty($request->module_access)) {
                $package_permissions['module_access'] = 0;
            } else {
                $package_permissions['module_access'] = 1;
            }
            if (empty($request->customer_order_general_customer)) {
                $packages_details['customer_order_general_customer'] = 0;
            } else {
                $packages_details['customer_order_general_customer'] = 1;
            }
            if (empty($request->customer_order_own_customer)) {
                $packages_details['customer_order_own_customer'] = 0;
            } else {
                $packages_details['customer_order_own_customer'] = 1;
            }
            if (empty($request->mpcs_module)) {
                $packages_details['mpcs_module'] = 0;
            } else {
                $packages_details['mpcs_module'] = 1;
            }
            if (empty($request->fleet_module)) {
                $packages_details['fleet_module'] = 0;
            } else {
                $packages_details['fleet_module'] = 1;
            }
            if (empty($request->ezyboat_module)) {
                $packages_details['ezyboat_module'] = 0;
            } else {
                $packages_details['ezyboat_module'] = 1;
            }
            if ($request->number_of_branches == '1') {
                $packages_details['number_of_branches'] = 1;
            } else {
                $packages_details['number_of_branches'] = 0;
            }
            if ($request->number_of_users == '1') {
                $packages_details['number_of_users'] = 1;
            } else {
                $packages_details['number_of_users'] = 0;
            }
            if ($request->number_of_products == '1') {
                $packages_details['number_of_products'] = 1;
            } else {
                $packages_details['number_of_products'] = 0;
            }
            if ($request->number_of_periods == '1') {
                $packages_details['number_of_periods'] = 1;
            } else {
                $packages_details['number_of_periods'] = 0;
            }
            if ($request->number_of_customers == '1') {
                $packages_details['number_of_customers'] = 1;
            } else {
                $packages_details['number_of_customers'] = 0;
            }
            if ($request->monthly_total_sales == '1') {
                $packages_details['monthly_total_sales'] = 1;
            } else {
                $packages_details['monthly_total_sales'] = 0;
            }
            if ($request->no_of_family_members == '1') {
                $packages_details['no_of_family_members'] = 1;
            } else {
                $packages_details['no_of_family_members'] = 0;
            }
            if ($request->no_of_vehicles == '1') {
                $packages_details['no_of_vehicles'] = 1;
            } else {
                $packages_details['no_of_vehicles'] = 0;
            }
            if (empty($request->customer_interest_deduct_option)) {
                $packages_details['customer_interest_deduct_option'] = 0;
            } else {
                $packages_details['customer_interest_deduct_option'] = 1;
            }
            $packages_details['leads_module'] = $request->leads_module == '1' ? 1 : 0;
            
            
            $packages_details['essentials_module'] = $request->essentials_module == '1' ? 1 : 0;
            $packages_details['leave_request'] = $request->leave_request == '1' ? 1 : 0;
            $packages_details['attendance'] = $request->attendance == '1' ? 1 : 0;
            $packages_details['payroll'] = $request->payroll == '1' ? 1 : 0;
            $packages_details['hr_settings'] = $request->hr_settings == '1' ? 1 : 0;
            $packages_details['holidays'] = $request->holidays == '1' ? 1 : 0;
            $packages_details['leave_type'] = $request->leave_type == '1' ? 1 : 0;
            $packages_details['essentials_todo'] = $request->essentials_todo == '1' ? 1 : 0;
            $packages_details['essentials_document'] = $request->essentials_document == '1' ? 1 : 0;
            $packages_details['essentials_memos'] = $request->essentials_memos == '1' ? 1 : 0;
            $packages_details['essentials_reminders'] = $request->essentials_reminders == '1' ? 1 : 0;
            $packages_details['essentials_messages'] = $request->essentials_messages == '1' ? 1 : 0;
            $packages_details['essentials_settings'] = $request->essentials_settings == '1' ? 1 : 0;
            $packages_details['allowance_deduction'] = $request->allowance_deduction == '1' ? 1 : 0;
            
            
            
            /**
             * @author: Afes Oktavianus
             * @since: 23-08-2021
             * @req: 3413 - Package Permission for Petro Module
             */
            if (empty($request->contact_module)) {
                $packages_details['contact_module'] = 0;
                $packages_details['contact_supplier'] = 0;
                $packages_details['contact_customer'] = 0;
                $packages_details['contact_group_customer'] = 0;
                $packages_details['contact_group_supplier'] = 0;
                $packages_details['import_contact'] = 0;
                $packages_details['customer_reference'] = 0;
                $packages_details['customer_statement'] = 0;
                $packages_details['customer_payment'] = 0;
                $packages_details['outstanding_received'] = 0;
                $packages_details['issue_payment_detail'] = 0;

                $packages_details['edit_received_outstanding'] = 0;
                $packages_details['stock_taking_page'] = 0;
            } else {

                $packages_details['contact_module'] = 1;
                $packages_details['contact_supplier'] = $request->contact_supplier == '1' ? 1 : 0;
                $packages_details['contact_customer'] = $request->contact_customer == '1' ? 1 : 0;
                $packages_details['contact_group_customer'] = $request->contact_group_customer == '1' ? 1 : 0;
                $packages_details['contact_group_supplier'] = $request->contact_group_supplier == '1' ? 1 : 0;
                $packages_details['import_contact'] = $request->import_contact == '1' ? 1 : 0;
                $packages_details['customer_reference'] = $request->customer_reference == '1' ? 1 : 0;
                $packages_details['customer_statement'] = $request->customer_statement == '1' ? 1 : 0;
                $packages_details['customer_payment'] = $request->customer_payment == '1' ? 1 : 0;
                $packages_details['outstanding_received'] = $request->outstanding_received == '1' ? 1 : 0;
                $packages_details['issue_payment_detail'] = $request->issue_payment_detail == '1' ? 1 : 0;

                $packages_details['edit_received_outstanding'] = $request->edit_received_outstanding == '1' ? 1 : 0;
                $packages_details['stock_taking_page'] =  $request->stock_taking_page == '1' ? 1 : 0;;
            }

            /**
             * @author: Afes Oktavianus
             * @since: 23-08-2021
             * @req: 8413 - Package Permission for Petro Module
             */
            if (empty($request->report_module)) {
                $packages_details['report_module'] = 0;
                $packages_details['product_report'] = 0;
                $packages_details['payment_status_report'] = 0;
                $packages_details['report_daily'] = 0;
                $packages_details['report_daily_summary'] = 0;
                $packages_details['report_profit_loss'] = 0;
                $packages_details['report_credit_status'] = 0;
                $packages_details['activity_report'] = 0;
                $packages_details['contact_report'] = 0;
                $packages_details['trending_product'] = 0;
                $packages_details['user_activity'] = 0;
                $packages_details['report_verification'] = 0;
                $packages_details['report_table'] = 0;
                $packages_details['report_staff_service'] = 0;
                $packages_details['report_register'] = 0;
            } else {
                $packages_details['report_module'] = 1;
                $packages_details['product_report'] = $request->product_report == '1' ? 1 : 0;
                $packages_details['payment_status_report'] = $request->payment_status_report == '1' ? 1 : 0;
                $packages_details['report_daily'] = $request->report_daily == '1' ? 1 : 0;
                $packages_details['report_daily_summary'] = $request->report_daily_summary == '1' ? 1 : 0;
                $packages_details['report_profit_loss'] = $request->report_profit_loss == '1' ? 1 : 0;
                $packages_details['report_credit_status'] = $request->report_credit_status == '1' ? 1 : 0;
                $packages_details['activity_report'] = $request->activity_report == '1' ? 1 : 0;
                $packages_details['contact_report'] = $request->contact_report == '1' ? 1 : 0;
                $packages_details['trending_product'] = $request->trending_product == '1' ? 1 : 0;
                $packages_details['user_activity'] = $request->user_activity == '1' ? 1 : 0;
                $packages_details['report_verification'] = $request->report_verification == '1' ? 1 : 0;
                $packages_details['report_table'] = $request->report_table == '1' ? 1 : 0;
                $packages_details['report_staff_service'] = $request->report_staff_service == '1' ? 1 : 0;
                $packages_details['report_register'] = $request->report_register == '1' ? 1 : 0;
            }
            
            // dd($request->installment_module);
            
            $packages_details['products'] = $request->products == '1' ? 1 : 0;
            $packages_details['issue_customer_bill'] = $request->issue_customer_bill == '1' ? 1 : 0;
            $packages_details['purchase'] = $request->purchase == '1' ? 1 : 0;
            $packages_details['sale_module'] = $request->sale_module == '1' ? 1 : 0;
            $packages_details['pos_sale'] = $request->pos_sale == '1' ? 1 : 0;
            $packages_details['repair_module'] = $request->repair_module == '1' ? 1 : 0;
            $packages_details['auto_services_and_repair_module'] = $request->auto_services_and_repair_module == '1' ? 1 : 0;
            $packages_details['patient_module'] = $request->patient_module == '1' ? 1 : 0;
            $packages_details['patient_test_module'] = $request->patient_test_module == '1' ? 1 : 0;
            
            $packages_details['restore_module'] = $request->restore_module == '1' ? 1 : 0;
            $packages_details['installment_module'] = $request->installment_module == '1' ? 1 : 0;
            
            $packages_details['stock_taking_module'] = $request->stock_taking_module == '1' ? 1 : 0;
            
            $packages_details['stock_transfer'] = $request->stock_transfer == '1' ? 1 : 0;
            $packages_details['expenses'] = $request->expenses == '1' ? 1 : 0;
            $packages_details['tasks_management'] = $request->tasks_management == '1' ? 1 : 0;
            $packages_details['catalogue_qr'] = $request->catalogue_qr == '1' ? 1 : 0;
            $packages_details['backup_module'] = $request->backup_module == '1' ? 1 : 0;
            $packages_details['notification_template_module'] = $request->notification_template_module == '1' ? 1 : 0;
            $packages_details['member_registration'] = $request->member_registration == '1' ? 1 : 0;
            $packages_details['user_management_module'] = $request->user_management_module == '1' ? 1 : 0;
            $packages_details['banking_module'] = $request->banking_module == '1' ? 1 : 0;
            $packages_details['list_easy_payment'] = $request->list_easy_payment == '1' ? 1 : 0;
            $packages_details['settings_module'] = $request->settings_module == '1' ? 1 : 0;
            $packages_details['business_settings'] = $request->business_settings == '1' ? 1 : 0;
            $packages_details['business_location'] = $request->business_location == '1' ? 1 : 0;
            $packages_details['invoice_settings'] = $request->invoice_settings == '1' ? 1 : 0;
            $packages_details['settings_otp_verification'] = $request->settings_otp_verification == '1' ? 1 : 0;
            $packages_details['settings_pay_online'] = $request->settings_pay_online == '1' ? 1 : 0;
            $packages_details['settings_reports_configurations'] = $request->settings_reports_configurations == '1' ? 1 : 0;
            $packages_details['settings_user_locations'] = $request->settings_user_locations == '1' ? 1 : 0;
            $packages_details['tax_rates'] = $request->tax_rates == '1' ? 1 : 0;
            $packages_details['home_dashboard'] = $request->home_dashboard == '1' ? 1 : 0;
            /**
             * @author: Afes Oktavianus
             * @since 24-08-2021
             * @req 8413 - Package Permission for Petro Module
             */
            if (empty($request->sale_module)) {
                $packages_details['sale_module'] = 0;
                $packages_details['all_sales'] = 0;
                $packages_details['add_sale'] = 0;
                $packages_details['list_pos'] = 0;
                $packages_details['list_draft'] = 0;
                $packages_details['list_quotation'] = 0;
                $packages_details['list_sell_return'] = 0;
                $packages_details['shipment'] = 0;
                $packages_details['discount'] = 0;
                $packages_details['reserved_stock'] = 0;
                $packages_details['import_sale'] = 0;
                $packages_details['upload_orders'] = 0;
                $packages_details['list_orders'] = 0;
                $packages_details['pos_button_on_top_belt'] = 0;
                $packages_details['pos_sale'] = 0;
            } else {
                $packages_details['sale_module'] = 1;
                $packages_details['all_sales'] = $request->all_sales == '1' ? 1 : 0;
                $packages_details['add_sale'] = $request->add_sale == '1' ? 1 : 0;
                $packages_details['list_pos'] = $request->list_pos == '1' ? 1 : 0;
                $packages_details['list_draft'] = $request->list_draft == '1' ? 1 : 0;
                $packages_details['list_quotation'] = $request->list_quotation == '1' ? 1 : 0;
                $packages_details['list_sell_return'] = $request->list_sell_return == '1' ? 1 : 0;
                $packages_details['shipment'] = $request->shipment == '1' ? 1 : 0;
                $packages_details['discount'] = $request->discount == '1' ? 1 : 0;
                $packages_details['reserved_stock'] = $request->reserved_stock == '1' ? 1 : 0;
                $packages_details['import_sale'] = $request->import_sale == '1' ? 1 : 0;
                $packages_details['upload_orders'] = $request->upload_order == '1' ? 1 : 0;
                $packages_details['list_orders'] = $request->list_order == '1' ? 1 : 0;
                $packages_details['pos_button_on_top_belt'] = $request->pos_button_on_top_belt == '1' ? 1 : 0;
                $packages_details['pos_sale'] = $request->pos_ == '1' ? 1 : 0;
            }

            /* Purchase Module */

            if (empty($request->purchase_module)) {
                $packages_details['purchase_module'] = 0;
                $packages_details['all_purchase'] = 0;
                $packages_details['add_purchase'] = 0;
                $packages_details['import_purchase'] = 0;
                $packages_details['add_bulk_purchase'] = 0;
                $packages_details['pop_button_on_top_belt'] = 0;
                $packages_details['purchase_return'] = 0;
            } else {
                $packages_details['purchase_module'] = 1;
                $packages_details['all_purchase'] = $request->all_purchase == '1' ? 1 : 0;
                $packages_details['add_purchase'] = $request->add_purchase == '1' ? 1 : 0;
                $packages_details['import_purchase'] = $request->import_purchase == '1' ? 1 : 0;
                $packages_details['add_bulk_purchase'] = $request->add_bulk_purchase == '1' ? 1 : 0;
                $packages_details['pop_button_on_top_belt'] = $request->pop_button_on_top_belt == '1' ? 1 : 0;
                $packages_details['purchase_return'] = $request->purchase_return == '1' ? 1 : 0;
            }
            if ($request->property_module == '1') {
                $packages_details['property_module'] = 1;
            } else {
                $packages_details['property_module'] = 0;
            }
            if (empty($request->visitors_registration_module)) {
                $packages_details['visitors_registration_module'] = 0;
                $packages_details['visitors'] = 0;
                $packages_details['visitors_registration'] = 0;
                $packages_details['visitors_registration_setting'] = 0;
                $packages_details['visitors_district'] = 0;
                $packages_details['visitors_town'] = 0;
                $packages_details['disable_all_other_module_vr'] = 0;
            } else {
                $packages_details['visitors_registration_module'] = 1;
                $packages_details['visitors'] = 1;
                $packages_details['visitors_registration'] = 1;
                $packages_details['visitors_registration_setting'] = 1;
                $packages_details['visitors_district'] = 1;
                $packages_details['visitors_town'] = 1;
                $packages_details['disable_all_other_module_vr'] = 1;
            }
            /* Cheque Writing Module */

            if (empty($request->cheque_write_module)) {
                $packages_details['cheque_write_module'] = 0;
                $packages_details['cheque_templates'] = 0;
                $packages_details['write_cheque'] = 0;
                $packages_details['manage_stamps'] = 0;
                $packages_details['manage_payee'] = 0;
                $packages_details['cheque_number_list'] = 0;
                $packages_details['deleted_cheque_details'] = 0;
                $packages_details['printed_cheque_details'] = 0;
                $packages_details['default_setting'] = 0;
            } else {
                $packages_details['cheque_write_module'] = 1;
                $packages_details['cheque_templates'] = $request->cheque_templates == '1' ? 1 : 0;
                $packages_details['write_cheque'] = $request->write_cheque == '1' ? 1 : 0;
                $packages_details['manage_stamps'] = $request->manage_stamps == '1' ? 1 : 0;
                $packages_details['manage_payee'] = $request->manage_payee == '1' ? 1 : 0;
                $packages_details['cheque_number_list'] = $request->cheque_number_list == '1' ? 1 : 0;
                $packages_details['deleted_cheque_details'] = $request->deleted_cheque_details == '1' ? 1 : 0;
                $packages_details['printed_cheque_details'] = $request->printed_cheque_details == '1' ? 1 : 0;
                $packages_details['default_setting'] = $request->cheque_number_list == '1' ? 1 : 0;
            }
            $packages_details['day_end_enable'] = $request->day_end_enable == '1' ? 1 : 0;
            
            $business_id = request()->session()->get('user.business_id');
            $business = Business::whereHas('subscriptions', function ($q) use ($id) {
                $q->where('package_id', $id)
                    ->whereDate('end_date', '>=', \Carbon::now());
            })->where('id', $business_id)->first();
            
            if (!empty($request->day_end_enable)) {
                $business_details['day_end_enable'] = $request->day_end_enable;
            } else {
                $business_details['day_end_enable'] = 0;
            }
            if (empty($request->customer_interest_deduct_option)) {
                $business_details['customer_interest_deduct_option'] = 0;
            } else {
                $business_details['customer_interest_deduct_option'] = 1;
            }
            if (!is_null($business)) {
                $business->fill($business_details);
                $business->save();
            }
            $packages_details['package_permissions'] = !empty($package_permissions) ? json_encode(($package_permissions)) : '';
            $packages_details['monthly_max_sale_limit'] = $request->monthly_max_sale_limit;
            $packages_details['no_of_backup'] = $request->no_of_backup;
            $packages_details['no_of_day'] = $request->no_of_day;

            
            
            
            $package = Package::where('id', $id)
                ->first();
            $package->fill($packages_details);
            $package->save();
            // return   $packages_details;
            if (!empty($request->input('update_subscriptions'))) {
                $package_details = [
                    'location_count' => $package->location_count,
                    'user_count' => $package->user_count,
                    'customer_count' => $package->customer_count,
                    'employee_count' => $package->employee_count,
                    'product_count' => $package->product_count,
                    'invoice_count' => $package->invoice_count,
                    'vehicle_count' => $package->vehicle_count,
                    'name' => $package->name,
                    'enable_sale_cmsn_agent' => $packages_details['sales_commission_agent'],
                    'enable_restaurant' => $packages_details['restaurant'],
                    'enable_booking' => $packages_details['booking'],
                    'enable_crm' => $packages_details['crm_enable'],
                    'manufacturing_module' => $packages_details['manufacturer'],
                    'mf_module' => $packages_details['manufacturer'],
                    'enable_sms' => $packages_details['sms_enable'],
                    'products' => $packages_details['products'],
                    'issue_customer_bill' => $packages_details['issue_customer_bill'],
                    'hr_module' => $packages_details['hr_module'],
                    'leads_module' => $packages_details['leads_module'],
                    'hospital_system' => $packages_details['hospital_system'],
                    'contact_module' => $packages_details['contact_module'],
                    'stock_taking_page' => $packages_details['stock_taking_page'],
                    'contact_supplier' => $packages_details['contact_supplier'],
                    'contact_customer' => $packages_details['contact_customer'],
                    'contact_group_customer' => $packages_details['contact_group_customer'],
                    'contact_group_supplier' => $packages_details['contact_group_supplier'],
                    'import_contact' => $packages_details['import_contact'],
                    'customer_reference' => $packages_details['customer_reference'],
                    'customer_statement' => $packages_details['customer_statement'],
                    'customer_payment' => $packages_details['customer_payment'],
                    'outstanding_received' => $packages_details['outstanding_received'],
                    'issue_payment_detail' => $packages_details['issue_payment_detail'],
                    'edit_received_outstanding' => $packages_details['edit_received_outstanding'],
                    'enable_duplicate_invoice' => $packages_details['enable_duplicate_invoice'],
                    'mpcs_module' => $packages_details['mpcs_module'],
                    'home_dashboard' => $packages_details['home_dashboard'],
                    'enable_petro_module' => $packages_details['petro_module'],
                    'enable_petro_dashboard' => $packages_details['petro_dashboard'],
                    'enable_petro_task_management' => $packages_details['petro_task_management'],
                    'enable_petro_pump_management' => $packages_details['pump_management'],
                    'enable_petro_management_testing' => $packages_details['pump_management_testing'],
                    'enable_petro_meter_reading' => $packages_details['meter_reading'],
                    'enable_petro_pump_dashboard' => $packages_details['pump_dashboard_opening'],
                    'enable_petro_pumper_management' => $packages_details['pumper_management'],
                    'enable_petro_daily_collection' => $packages_details['daily_collection'],
                    'enable_petro_settlement' => $packages_details['settlement'],
                    'enable_petro_list_settlement' => $packages_details['list_settlement'],
                    'enable_petro_dip_management' => $packages_details['dip_management'],
                    'enable_petro_fuel_tanks_edit' => $packages_details['fuel_tanks_edit'],
                    'enable_petro_fuel_tanks_delete' => $packages_details['fuel_tanks_delete'],
                    'enable_petro_pumps_edit' => $packages_details['pumps_edit'],
                    'enable_petro_pumps_delete' => $packages_details['pumps_delete'],

                    'meter_resetting' => $packages_details['meter_resetting'],
                    'pump_operator_dashboard' => $packages_details['pump_operator_dashboard'],
                    'property_module' => $packages_details['property_module'],
                    'customer_order_own_customer' => $packages_details['customer_order_own_customer'],
                    'customer_order_general_customer' => $packages_details['customer_order_general_customer'],
                    'access_account' => $package_permissions['account_access'],
                    'access_sms_settings' => $package_permissions['sms_settings_access'],
                    'access_module' => $package_permissions['module_access'],
                    'purchase' => $package->purchase,
                    'stock_transfer' => $package->stock_transfer,
                    'service_staff' => $package->service_staff,
                    'enable_subscription' => $package->enable_subscription,
                    'add_sale' => $package->add_sale,
                    'stock_adjustment' => $package->stock_adjustment,
                    'tables' => $package->tables,
                    'type_of_service' => $package->type_of_service,
                    'pos_sale' => $package->pos_sale,
                    'expenses' => $package->expenses,
                    'modifiers' => $package->modifiers,
                    'kitchen' => $package->kitchen,
                    'banking_module' => $package->banking_module,
                    'sale_module' => $package->sale_module,
                    'all_sales' => $package->all_sales,
                    'list_pos' => $package->list_pos,
                    'list_draft' => $package->list_draft,
                    'list_quotation' => $package->list_quotation,
                    'list_sell_return' => $package->list_sell_return,
                    'shipment' => $package->shipment,
                    'discount' => $package->discount,
                    'repair_module' => $package->repair_module,
                    'auto_services_and_repair_module' => $package->auto_services_and_repair_module,
                    'restore_module' => $package->restore_module,
                    'patient_module' => $package->patient_module,
                    'patient_test_module' => $package->patient_test_module,
                    'installment_module' => $package->installment_module,
                    'stock_taking_module' => $package->stock_taking_module,
                    'tasks_management' => $package->tasks_management,
                    'report_module' => $package->report_module,
                    'product_report' => $packages_details['product_report'],
                    'payment_status_report' => $packages_details['payment_status_report'],
                    'report_daily' => $packages_details['report_daily'],
                    'report_daily_summary' => $packages_details['report_daily_summary'],
                    'report_profit_loss' => $packages_details['report_profit_loss'],
                    'report_credit_status' => $packages_details['report_credit_status'],
                    'activity_report' => $packages_details['activity_report'],
                    'contact_report' => $packages_details['contact_report'],
                    'trending_product' => $packages_details['trending_product'],
                    'user_activity' => $packages_details['user_activity'],
                    'report_verification' => $packages_details['report_verification'],
                    'report_table' => $packages_details['report_table'],
                    'report_staff_service' => $packages_details['report_staff_service'],
                    'report_register' => $packages_details['report_register'],
                    'catalogue_qr' => $package->catalogue_qr,
                    'backup_module' => $package->backup_module,
                    'notification_template_module' => $package->notification_template_module,
                    'member_registration' => $package->member_registration,
                    'user_management_module' => $package->user_management_module,
                    'settings_module' => $package->settings_module,
                    'business_settings' => $package->business_settings,
                    'business_location' => $package->business_location,
                    'invoice_settings' => $package->invoice_settings,
                    'settings_otp_verification' => $packages_details['settings_otp_verification'],
                    'settings_pay_online' => $packages_details['settings_pay_online'],
                    'settings_reports_configurations' => $packages_details['settings_reports_configurations'],
                    'settings_user_locations' => $packages_details['settings_user_locations'],
                    'tax_rates' => $package->tax_rates,
                    'list_easy_payment' => $package->list_easy_payment,
                    'fleet_module' => $package->fleet_module,
                    'ezyboat_module' => $package->ezyboat_module,
                    'enable_custom_link' => $package->enable_custom_link,
                    'visitors_registration_module' => $package->visitors_registration_module,
                    'payday' => $package->payday,
                    'purchase_module' => $packages_details['purchase_module'],
                    'all_purchase' => $packages_details['all_purchase'],
                    'add_purchase' => $packages_details['add_purchase'],
                    'import_purchase' => $packages_details['import_purchase'],
                    'add_bulk_purchase' => $packages_details['add_bulk_purchase'],
                    'pop_button_on_top_belt' => $packages_details['pop_button_on_top_belt'],
                    'purchase_return' => $packages_details['purchase_return'],

                    'enable_cheque_writing' => $package->cheque_write_module,
                    'cheque_templates' => $packages_details['cheque_templates'],
                    'write_cheque' => $packages_details['write_cheque'],
                    'manage_stamps' => $packages_details['manage_stamps'],
                    'manage_payee' => $packages_details['manage_payee'],
                    'cheque_number_list' => $packages_details['cheque_number_list'],
                    'deleted_cheque_details' => $packages_details['deleted_cheque_details'],
                    'printed_cheque_details' => $packages_details['printed_cheque_details'],
                    'default_setting' => $packages_details['default_setting'],
                    'delete_settlement'=> $packages_details['delete_settlement']? $packages_details['delete_settlement'] : 0,
                    'petro_quota_module' => $packages_details['petro_quota_module'] ?$packages_details['petro_quota_module']: 0

                ];
                
                $addto = Package::getPackagePeriodInDays($package);
                $interval = ucfirst($package->interval);
                $length = $package->interval_count;
                $module_activation_data = [];
                if($package_details['mf_module'] == 1){
                    $module_activation_data['mf_length'] = $length;
                    $module_activation_data['mf_interval'] = $interval;
                    $module_activation_data['mf_activated_on'] = date('Y-m-d');
                    $module_activation_data['mf_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                if($package_details["access_account"] == 1){
                    $module_activation_data['ac_length'] = $length;
                    $module_activation_data['ac_interval'] = $interval;
                    $module_activation_data['ac_activated_on'] = date('Y-m-d');
                    $module_activation_data['ac_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                if($package_details["access_module"] == 0){
                    $module_activation_data['access_module_activated_on'] = date('Y-m-d');
                    $module_activation_data['access_module_interval'] = $interval;
                    $module_activation_data['access_module_length'] = $length;
                    $module_activation_data['access_module_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                
                }
                
                if($package_details["hr_module"] == 1){
                    $module_activation_data['hr_length'] = $length;
                    $module_activation_data['hr_interval'] = $interval;
                    $module_activation_data['hr_activated_on'] = date('Y-m-d');
                    $module_activation_data['hr_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                if($package_details["visitors_registration_module"] == 1){
                
                    $module_activation_data['vreg_length'] = $length;
                    $module_activation_data['vreg_interval'] = $interval;
                    $module_activation_data['vreg_activated_on'] = date('Y-m-d');
                    $module_activation_data['vreg_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                
                }
                
                
                if( $package_details["enable_petro_module"] == 1){
                    $module_activation_data['petro_length'] = $length;
                    $module_activation_data['petro_interval'] = $interval;
                    $module_activation_data['petro_activated_on'] = date('Y-m-d');
                    $module_activation_data['petro_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if( $package_details["repair_module"] == 1){
                    $module_activation_data['repair_length'] = $length;
                    $module_activation_data['repair_interval'] = $interval;
                    $module_activation_data['repair_activated_on'] = date('Y-m-d');
                    $module_activation_data['repair_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if( $package_details["fleet_module"] == 1){
                    $module_activation_data['fleet_length'] = $length;
                    $module_activation_data['fleet_interval'] = $interval;
                    $module_activation_data['fleet_activated_on'] = date('Y-m-d');
                    $module_activation_data['fleet_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["mpcs_module"] == 1){
                    $module_activation_data['mpcs_length'] = $length;
                    $module_activation_data['mpcs_interval'] = $interval;
                    $module_activation_data['mpcs_activated_on'] = date('Y-m-d');
                    $module_activation_data['mpcs_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["backup_module"] == 1){
                    $module_activation_data['backup_length'] = $length;
                    $module_activation_data['backup_interval'] = $interval;
                    $module_activation_data['backup_activated_on'] = date('Y-m-d');
                    $module_activation_data['backup_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["property_module"] == 1){
                    $module_activation_data['property_length'] = $length;
                    $module_activation_data['property_interval'] = $interval;
                    $module_activation_data['property_activated_on'] = date('Y-m-d');
                    $module_activation_data['property_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["auto_services_and_repair_module"] == 1){
                    $module_activation_data['auto_length'] = $length;
                    $module_activation_data['auto_interval'] = $interval;
                    $module_activation_data['auto_activated_on'] = date('Y-m-d');
                    $module_activation_data['auto_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["contact_module"] == 1){
                    $module_activation_data['contact_length'] = $length;
                    $module_activation_data['contact_interval'] = $interval;
                    $module_activation_data['contact_activated_on'] = date('Y-m-d');
                    $module_activation_data['contact_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                // if($package_details["ran_module"] == 1){
                //     $module_activation_data['ran_length'] = $length;
                //     $module_activation_data['ran_interval'] = $interval;
                //     $module_activation_data['ran_activated_on'] = date('Y-m-d');
                //     $module_activation_data['ran_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                // }
                
                
                if($package_details["report_module"] == 1){
                    $module_activation_data['report_length'] = $length;
                    $module_activation_data['report_interval'] = $interval;
                    $module_activation_data['report_activated_on'] = date('Y-m-d');
                    $module_activation_data['report_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["settings_module"] == 1){
                    $module_activation_data['settings_length'] = $length;
                    $module_activation_data['settings_interval'] = $interval;
                    $module_activation_data['settings_activated_on'] = date('Y-m-d');
                    $module_activation_data['settings_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["user_management_module"] == 1){
                    $module_activation_data['um_length'] = $length;
                    $module_activation_data['um_interval'] = $interval;
                    $module_activation_data['um_activated_on'] = date('Y-m-d');
                    $module_activation_data['um_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if( $package_details["banking_module"] == 1){
                
                    $module_activation_data['banking_length'] = $length;
                    $module_activation_data['banking_interval'] = $interval;
                    $module_activation_data['banking_activated_on'] = date('Y-m-d');
                    $module_activation_data['banking_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["sale_module"] == 1){
                    $module_activation_data['sale_length'] = $length;
                    $module_activation_data['sale_interval'] = $interval;
                    $module_activation_data['sale_activated_on'] = date('Y-m-d');
                    $module_activation_data['sale_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if( $package_details["leads_module"]== 1){
                    $module_activation_data['leads_length'] = $length;
                    $module_activation_data['leads_interval'] = $interval;
                    $module_activation_data['leads_activated_on'] = date('Y-m-d');
                    $module_activation_data['leads_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if( $package_details["hospital_system"] == 1){
                    $module_activation_data['hospital_length'] = $length;
                    $module_activation_data['hospital_interval'] = $interval;
                    $module_activation_data['hospital_activated_on'] = date('Y-m-d');
                    $module_activation_data['hospital_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["enable_restaurant"] == 1){
                
                    $module_activation_data['restaurant_length'] = $length;
                    $module_activation_data['restaurant_interval'] = $interval;
                    $module_activation_data['restaurant_activated_on'] = date('Y-m-d');
                    $module_activation_data['restaurant_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["enable_duplicate_invoice"] == 1){
                    $module_activation_data['duplicate_invoice_length'] = $length;
                    $module_activation_data['duplicate_invoice_interval'] = $interval;
                    $module_activation_data['duplicate_invoice_activated_on'] = date('Y-m-d');
                    $module_activation_data['duplicate_invoice_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if( $package_details["tasks_management"] == 1){
                    $module_activation_data['tasks_length'] = $length;
                    $module_activation_data['tasks_interval'] = $interval;
                    $module_activation_data['tasks_activated_on'] = date('Y-m-d');
                    $module_activation_data['tasks_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["enable_cheque_writing"] == 1){
                    $module_activation_data['cheque_length'] = $length;
                    $module_activation_data['cheque_interval'] = $interval;
                    $module_activation_data['cheque_activated_on'] = date('Y-m-d');
                    $module_activation_data['cheque_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if( $package_details["list_easy_payment"] == 1){
                    $module_activation_data['list_easy_length'] = $length;
                    $module_activation_data['list_easy_interval'] = $interval;
                    $module_activation_data['list_easy_activated_on'] = date('Y-m-d');
                    $module_activation_data['list_easy_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                
                if($package_details["pump_operator_dashboard"] == 1){
                    $module_activation_data['pump_length'] = $length;
                    $module_activation_data['pump_interval'] = $interval;
                    $module_activation_data['pump_activated_on'] = date('Y-m-d');
                    $module_activation_data['pump_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                if($package_details["stock_taking_module"] == 1){
                    $module_activation_data['stock_taking_length'] = $length;
                    $module_activation_data['stock_taking_interval'] = $interval;
                    $module_activation_data['stock_taking_activated_on'] = date('Y-m-d');
                    $module_activation_data['stock_taking_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }
                
                if($package_details["installment_module"] == 1){
                    $module_activation_data['installment_module_length'] = $length;
                    $module_activation_data['installment_module_interval'] = $interval;
                    $module_activation_data['installment_module_activated_on'] = date('Y-m-d');
                    $module_activation_data['installment_module_expiry_date'] =    date('Y-m-d',strtotime("+{$addto} day"));
                
                }

                
                //Update subscription package details
                

                $subscriptions = Subscription::where('package_id', $package->id)
                    ->whereDate('end_date', '>=', \Carbon::now())
                    ->update(['package_details' => json_encode($package_details)]);
                    
                $subscriptions = Subscription::where('package_id', $package->id)
                    ->update(['module_activation_details' => json_encode($module_activation_data)]);
            }
            $manage_stock_enable = $request->manage_stock_enable == 1 ?: 0;
            \App\Business::query()
                ->update(['is_manged_stock_enable' => $manage_stock_enable]);
            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()
            ->action('\Modules\Superadmin\Http\Controllers\PackagesController@index')
            ->with('status', $output);
    }

    /**
     * Remove the specified resource from storage.
     * @return Response
     */
    public function destroy($id)
    {
        if (!auth()->user()->can('superadmin')) {
            abort(403, 'Unauthorized action.');
        }
        try {
            Package::where('id', $id)
                ->delete();
            $output = ['success' => 1, 'msg' => __('lang_v1.success')];
        } catch (\Exception $e) {
            \Log::emergency("File:" . $e->getFile() . "Line:" . $e->getLine() . "Message:" . $e->getMessage());
            $output = [
                'success' => 0,
                'msg' => __('messages.something_went_wrong')
            ];
        }
        return redirect()
            ->action('\Modules\Superadmin\Http\Controllers\PackagesController@index')
            ->with('status', $output);
    }

    /**
     * get option variable of resource
     *
     * @return \Illuminate\Http\Response
     */
    public function getOptionVariables(Request $request)
    {
        $option_id = $request->option_id;
        $option_variables = PackageVariable::where('variable_options', $option_id)->get();
        $selected_variables = [];
        if (!empty($request->package_id)) {
            $selected_variables = json_decode(Package::where('id', $request->package_id)->first()->option_variables);
        }
        return view('superadmin::packages.partials.option_variables')->with(compact(
            'option_variables',
            'selected_variables'
        ));
    }
}
