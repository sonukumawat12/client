@inject('request', 'Illuminate\Http\Request')
@php
$sidebar_setting = App\SiteSettings::where('id', 1)
->select('ls_side_menu_bg_color', 'ls_side_menu_font_color', 'sub_module_color', 'sub_module_bg_color')
->first();

$module_array['disable_all_other_module_vr'] = 0;
$module_array['enable_petro_module'] = 0;
$module_array['enable_petro_dashboard'] = 0;
$module_array['petro_daily_status'] = 0;
$module_array['tank_transfer'] = 0;
$module_array['enable_petro_task_management'] = 0;
$module_array['enable_petro_pump_dashboard'] = 0;
$module_array['enable_petro_pumper_management'] = 0;
$module_array['enable_petro_daily_collection'] = 0;
$module_array['enable_petro_settlement'] = 0;
$module_array['enable_petro_list_settlement'] = 0;
$module_array['enable_petro_dip_management'] = 0;
$module_array['enable_sale_cmsn_agent'] = 0;
$module_array['pump_operator_dashboard'] = 0;
$module_array['enable_crm'] = 0;
$module_array['mf_module'] = 0;
$module_array['helpguide'] = 0;
$module_array['customized_report'] = 0;
$module_array['petro_sms_notifications'] = 0;

$module_array['contact_list_customer_loans'] = 0;
$module_array['contact_settings'] = 0;
$module_array['contact_list_supplier_map_products'] = 0;
$module_array['contact_add_supplier_products'] = 0;
$module_array['contact_import_opening_balalnces'] = 0;
$module_array['contact_returned_cheque_details'] = 0;
$module_array['product_print_labels'] = 0;


$module_array['cheque_dashboard'] = 0;
$module_array['cheque_add_template'] = 0;
$module_array['cheque_cancelled_cheques'] = 0;
$module_array['cheque_printed_cheques'] = 0;
$module_array['ezy_list_products'] = 0;
$module_array['ezy_units'] = 0;
$module_array['ezy_categories'] = 0;
$module_array['ezy_show_current_stock'] = 0;
$module_array['ezy_show_stock_report'] = 0;

$module_array['cheque_dashboard'] = 0;
$module_array['cheque_add_template'] = 0;
$module_array['cheque_cancelled_cheques'] = 0;
$module_array['cheque_printed_cheques'] = 0;


$module_array['ezy_products'] = 0;

$module_array['bakery_module'] = 0;

$module_array['post_dated_cheque'] = 0;

$module_array['crm_module'] = 0;
$module_array['list_credit_sales_page'] = 0;
$module_array['stock_conversion_module'] = 0;
$module_array['docmanagement_module'] = 0;


$module_array['ezyinvoice_module'] = 0;
$module_array['shipping_module'] = 0;
$module_array['airline_module'] = 0;

$module_array['vat_module'] = 0;
$module_array['asset_module'] = 0;
$module_array['deposits_module'] = 0;
$module_array['dsr_module'] = 0;

$module_array['ns_asset_management'] = 0;
$module_array['ns_deposits_module'] = 0;
$module_array['ns_discount_module'] = 0;
$module_array['ns_dsr_module'] = 0; 

$module_array['ns_vat_module'] = 0;

$module_array['realize_cheque'] = 0;
$module_array['discount_module'] = 0;
$module_array['tpos_module'] = 0;

$module_array['hms_module'] = 0;

$module_array['hr_module'] = 0;
$module_array['loan_module'] = 0;
$module_array['employee'] = 0;
$module_array['teminated'] = 0;
$module_array['award'] = 0;
$module_array['leave_request'] = 0;
$module_array['attendance'] = 0;
$module_array['import_attendance'] = 0;
$module_array['late_and_over_time'] = 0;
$module_array['payroll'] = 0;
$module_array['salary_details'] = 0;
$module_array['basic_salary'] = 0;
$module_array['payroll_payments'] = 0;
$module_array['hr_reports'] = 0;
$module_array['notice_board'] = 0;
$module_array['hr_settings'] = 0;
$module_array['department'] = 0;
$module_array['jobtitle'] = 0;
$module_array['jobcategory'] = 0;
$module_array['workingdays'] = 0;
$module_array['workshift'] = 0;
$module_array['holidays'] = 0;
$module_array['leave_type'] = 0;
$module_array['salary_grade'] = 0;
$module_array['employment_status'] = 0;
$module_array['salary_component'] = 0;
$module_array['hr_prefix'] = 0;
$module_array['hr_tax'] = 0;
$module_array['religion'] = 0;
$module_array['hr_setting_page'] = 0;
$module_array['enable_sms'] = 0;
$module_array['access_account'] = 0;
$module_array['enable_booking'] = 0;
$module_array['customer_order_own_customer'] = 0;
$module_array['customer_settings'] = 0;
$module_array['customer_order_general_customer'] = 0;
$module_array['mpcs_module'] = 0;

$module_array['price_changes_module'] = 0;

$module_array['shipping_module'] = 0;

$module_array['fleet_module'] = 0;
$module_array['ezyboat_module'] = 0;
$module_array['merge_sub_category'] = 0;
$module_array['backup_module'] = 0;
$module_array['banking_module'] = 0;
$module_array['products'] = 0;
$module_array['purchase'] = 0;
$module_array['stock_transfer'] = 0;
$module_array['service_staff'] = 0;
$module_array['enable_subscription'] = 0;
$module_array['add_sale'] = 0;
$module_array['stock_adjustment'] = 0;
$module_array['tables'] = 0;
$module_array['type_of_service'] = 0;
$module_array['pos_sale'] = 0;
$module_array['expenses'] = 0;
$module_array['modifiers'] = 0;
$module_array['kitchen'] = 0;
$module_array['orders'] = 0;
$module_array['enable_cheque_writing'] = 0;
$module_array['issue_customer_bill'] = 0;
$module_array['issue_customer_bill_vat'] = 0;
$module_array['tasks_management'] = 0;
$module_array['notes_page'] = 0;
$module_array['tasks_page'] = 0;
$module_array['reminder_page'] = 0;
$module_array['member_registration'] = 0;
$module_array['visitors_registration_module'] = 0;
$module_array['visitors'] = 0;
$module_array['visitors_registration'] = 0;
$module_array['visitors_registration_setting'] = 0;
$module_array['visitors_district'] = 0;
$module_array['visitors_town'] = 0;
$module_array['home_dashboard'] = 0;
$module_array['contact_module'] = 0;
$module_array['stock_taking_page'] = 0;
$module_array['contact_supplier'] = 0;
$module_array['contact_customer'] = 0;
$module_array['contact_group_customer'] = 0;
$module_array['import_contact'] = 0;
$module_array['customer_reference'] = 0;
$module_array['customer_statement'] = 0;
$module_array['customer_statement_pmt'] = 0;
$module_array['customer_payment'] = 0;
$module_array['outstanding_received'] = 0;
$module_array['issue_payment_detail'] = 0;
$module_array['property_module'] = 0;
$module_array['ran_module'] = 0;
$module_array['report_module'] = 0;
$module_array['product_report'] = 0;
$module_array['payment_status_report'] = 0;
$module_array['verification_report'] = 0;
$module_array['activity_report'] = 0;
$module_array['contact_report'] = 0;
$module_array['trending_product'] = 0;
$module_array['user_activity'] = 0;
$module_array['report_verification'] = 0;
$module_array['report_table'] = 0;
$module_array['report_staff_service'] = 0;
$module_array['verification_report'] = 0;
$module_array['notification_template_module'] = 0;
$module_array['settings_module'] = 0;
$module_array['user_management_module'] = 0;
$module_array['leads_module'] = 0;
$module_array['leads'] = 0;
$module_array['day_count'] = 0;
$module_array['leads_import'] = 0;
$module_array['leads_settings'] = 0;
$module_array['sms_module'] = 0;
$module_array['list_sms'] = 0;
$module_array['smsmodule_module'] = 0;
$module_array['status_order'] = 0;
$module_array['list_orders'] = 0;
$module_array['upload_orders'] = 0;
$module_array['subcriptions'] = 0;
$module_array['over_limit_sales'] = 0;
$module_array['sale_module'] = 0;
$module_array['all_sales'] = 0;
$module_array['list_pos'] = 0;
$module_array['list_draft'] = 0;
$module_array['list_quotation'] = 0;
$module_array['list_sell_return'] = 0;
$module_array['shipment'] = 0;
$module_array['discount'] = 0;
$module_array['import_sale'] = 0;
$module_array['reserved_stock'] = 0;
$module_array['auto_services_and_repair_module'] = 0;
$module_array['repair_module'] = 0;
$module_array['catalogue_qr'] = 0;
$module_array['business_settings'] = 0;
$module_array['business_location'] = 0;
$module_array['invoice_settings'] = 0;
$module_array['tax_rates'] = 0;
$module_array['list_easy_payment'] = 0;
$module_array['payday'] = 0;

$module_array['patient_module'] = 0;

$module_array['purchase_module'] = 0;
$module_array['all_purchase'] = 0;
$module_array['add_purchase'] = 0;
$module_array['import_purchase'] = 0;
$module_array['add_bulk_purchase'] = 0;
$module_array['purchase_return'] = 0;

$module_array['cheque_write_module'] = 0;
$module_array['cheque_templates'] = 0;
$module_array['chequer_dashboard'] = 0;
$module_array['write_cheque'] = 0;
$module_array['manage_stamps'] = 0;
$module_array['manage_payee'] = 0;
$module_array['cheque_number_list'] = 0;
$module_array['deleted_cheque_details'] = 0;
$module_array['printed_cheque_details'] = 0;
$module_array['default_setting'] = 0;
$module_array['petro_quota_module'] = 0;
$module_array['stock_taking_module'] = 0;
$module_array['installment_module'] = 0;

$module_array['distribution_module'] = 0;
$module_array['spreadsheet'] = 0;

$module_array['allowance_deduction'] = 0;
$module_array['essentials_module'] = 0;
$module_array['essentials_todo'] = 0;
$module_array['essentials_document'] = 0;
$module_array['essentials_memos'] = 0;
$module_array['essentials_reminders'] = 0;
$module_array['essentials_messages'] = 0;
$module_array['essentials_settings'] = 0;

foreach ($module_array as $key => $module_value) {
    ${$key} = 0;
}

$business_id = request()->session()->get('user.business_id');
$subscription = Modules\Superadmin\Entities\Subscription::current_subscription($business_id);
$stock_adjustment = 0;
$pacakge_details = array();

if (!empty($subscription)) {
    $pacakge_details = $subscription->package_details;
    $stock_adjustment = $pacakge_details['stock_adjustment'];
    $disable_all_other_module_vr = 0;

    if (array_key_exists('disable_all_other_module_vr', $pacakge_details)) {
        $disable_all_other_module_vr = $pacakge_details['disable_all_other_module_vr'];
    }

    foreach ($module_array as $key => $module_value) {
        if ($disable_all_other_module_vr == 0) {
            if (array_key_exists($key, $pacakge_details)) {
                ${$key} = $pacakge_details[$key];
                //logger($key." ".$pacakge_details[$key]);
            } else {
                ${$key} = 0;
            }
        } else {
            ${$key} = 0;
            $disable_all_other_module_vr = 1;
            $visitors_registration_module = 1;
            $visitors = 1;
            $visitors_registration = 1;
            $visitors_registration_setting = 1;
            $visitors_district = 1;
            $visitors_town = 1;
        }
    }
}

if (auth()->user()->can('superadmin')) {
    foreach ($module_array as $key => $module_value) {
        ${$key} = 1;
    }
    $disable_all_other_module_vr = 0;
}



@endphp
<style>
    .skin-blue .main-sidebar {
        background-color: @if ( !empty($sidebar_setting->ls_side_menu_bg_color)) {
                {
                $sidebar_setting->ls_side_menu_bg_color
            }
        }

        @endif ;
    }

    .skin-blue .sidebar a {
        color: @if ( !empty($sidebar_setting->ls_side_menu_font_color)) {
                {
                $sidebar_setting->ls_side_menu_font_color
            }
        }

        @endif ;
    }

    .skin-blue .treeview-menu>li>a {
        color: @if ( !empty($sidebar_setting->sub_module_color)) {
                {
                $sidebar_setting->sub_module_color
            }
        }

        @endif ;
    }

    .skin-blue .sidebar-menu>li>.treeview-menu {
        background: @if ( !empty($sidebar_setting->sub_module_bg_color)) {
                {
                $sidebar_setting->sub_module_bg_color
            }
        }

        @endif ;
    }
   #sidebarFilter {
    margin: 0 10px;
    background-color: white;
    border: 1px solid #ddd;
    border-radius: 4px;
    display: block;
    width: calc(100% - 20px); /* Adjust width to account for margins */
}

</style>

@php
$user = App\User::where('id', auth()->user()->id)->first();
$is_admin = $user->hasRole(
'Admin#' .
request()
->session()
->get('business.id'),
)
? true
: false;
@endphp

<!-- Left side column. contains the logo and sidebar -->

@if (session()->get('business.is_patient') && $patient_module)
<ul class="custom-overflow sidebar-menu navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="width: 220px !important;">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-text mx-3">SYZYGY</div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider">

    @if (session()->get('business.is_patient'))
    <li class="nav-item {{ $request->segment(1) == 'patient' ? 'active' : '' }}">
        <a href="{{ action('PatientController@index') }}"> <i class="fa fa-dashboard"></i> <span>
                @lang('home.home')</span> </a>
    </li>
    @endif @if (session()->get('business.is_hospital'))
    <li class="nav-item {{ $request->segment(1) == 'patient' ? 'active' : '' }}">
        <a href="{{ action('HospitalController@index') }}"> <i class="fa fa-dashboard"></i> <span>
                @lang('home.home')</span> </a>
    </li>
    @endif

    <li class="nav-item {{ $request->segment(1) == 'reports' ? 'active' : '' }}">
        <a href="{{ action('ReportController@getUserActivityReport') }}">
            <i class="fa fa-eercast"></i>
            <span class="title">@lang('report.user_activity')</span>
        </a>
    </li>

    @if ($is_admin)
    @if (Module::has('Superadmin')) @includeIf('superadmin::layouts_v2.partials.subscription')
    @endif
    @if (request()->session()->get('business.is_patient'))
    <li class="nav-item @if (in_array($request->segment(1), ['family-members', 'superadmin', 'pay-online'])) {{ 'active active-sub' }} @endif">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#patientbs-menu" aria-expanded="true" aria-controls="patientbs-menu">
            <i class="fa fa-cog"></i>
            <span>@lang('business.settings')</span>
        </a>
        <div id="patientbs-menu" class="collapse @if (in_array($request->segment(1), ['family-members', 'superadmin', 'pay-online'])) {{ 'show' }} @endif" aria-labelledby="patientbs-menu" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('business.settings'):</h6>
                <a class="collapse-item {{ $request->segment(1) == 'family-member' ? 'active' : '' }}" href="{{ action('FamilyController@index') }}">@lang('patient.family_member')</a>

                <a class="collapse-item {{ $request->segment(2) == 'family-subscription' ? 'active' : '' }}" href="{{ action('\Modules\Superadmin\Http\Controllers\FamilySubscriptionController@index') }}">@lang('patient.family_subscription')</a>

                <a class="collapse-item {{ $request->segment(1) == 'pay-online' && $request->segment(2) == 'create' ? 'active active-sub' : '' }}" href="{{ action('\Modules\Superadmin\Http\Controllers\PayOnlineController@create') }}">@lang('superadmin::lang.pay_online')</a>
            </div>
        </div>
    </li>
    @endif
    @endif


</ul> 
@elseif(auth()->user()->hasRole('dsr_officer'))
<ul class="custom-overflow sidebar-menu navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="width: 220px !important;max-height: 100vh;">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-text mx-3">SYZYGY</div>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider">
    @if($dsr_module || $ns_dsr_module)
        @includeIf('dsr::layouts_v2.partials.sidebar')
    @endif
</ul>
@else
<ul class="custom-overflow sidebar-menu navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar" style="width: 220px !important;max-height: 100vh;">
    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="#">
        <div class="sidebar-brand-text mx-3">SYZYGY</div>
    </a>
    </a>
    <!-- Divider -->
    <hr class="sidebar-divider">
    
 <li class="">
        <input type="text" id="sidebarFilter" placeholder="Filter menu..." class="form-control mx-5 p-4">
    </li>
    <!-- Call superadmin module if defined -->
    @if (Module::has('Superadmin'))
        @includeIf('superadmin::layouts_v2.partials.sidebar')
    @endif
    <li class="nav-item {{ in_array($request->segment(1), ['helpguide', 'my_account']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#helpguide-menu"
            aria-expanded="true" aria-controls="helpguide-menu">
            <i class="ti-settings"></i>
            <span>Help Guide</span>
        </a>
        <div id="helpguide-menu" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Help Guide:</h6>
                @cannot('superadmin')
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == '' ? 'active active-sub' : '' }}" href="{{ action('\Modules\HelpGuide\Http\Controllers\Frontend\IndexController@index') }}">Home Page</a>
                    <a class="collapse-item {{ $request->segment(1) == 'my_account' && $request->segment(2) == 'helpguide' ? 'active active-sub' : '' }}" href="{{ route('my_account') }}#/tickets">Tickets</a>
                @endcannot
                @can('superadmin')
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' ? 'active active-sub' : '' }}" href="{{ action('\Modules\HelpGuide\Http\Controllers\Dashboard\IndexController@index') }}">Dashboard</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == '' ? 'active active-sub' : '' }}" href="{{ action('\Modules\HelpGuide\Http\Controllers\Frontend\IndexController@index') }}" target="_blank">Home Page</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'tickets' ? 'active active-sub' : '' }}" href="/helpguide/dashboard/tickets">Tickets</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'articles' ? 'active active-sub' : '' }}" href="/helpguide/dashboard#/articles">Articles</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'categories' ? 'active active-sub' : '' }}" href="/helpguide/dashboard#/categories">Categories</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'saved_replies' ? 'active active-sub' : '' }}" href="/helpguide/dashboard#/saved_replies">Saved Replies</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'customers' ? 'active active-sub' : '' }}" href="/helpguide/dashboard#/customers">Customers</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'employees' ? 'active active-sub' : '' }}" href="/helpguide/dashboard#/employees">Employees</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'modules' ? 'active active-sub' : '' }}" href="{{ route('languages.index') }}">Translations</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'modules' ? 'active active-sub' : '' }}" href="/helpguide/dashboard#/modules">Modules</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'settings' ? 'active active-sub' : '' }}" href="/helpguide/dashboard/settings">Settings</a>
                    <a class="collapse-item {{ $request->segment(1) == 'helpguide' && $request->segment(2) == 'dashboard' && $request->segment(3) == 'customizer' ? 'active active-sub' : '' }}" href="/helpguide/dashboard/customizer">Customizer</a>
                @endcan
            </div>
        </div>
    </li>

    @if ($home_dashboard)
    @if (auth()->user()->can('dashboard.data') &&
    !auth()->user()->is_pump_operator &&
    !auth()->user()->is_property_user)
    <li class="nav-item {{ $request->segment(1) == 'home' ? 'active' : '' }}">
        <a class="nav-link" href="{{ action('HomeController@index') }}">
            <i class="fa fa-clone"></i>
            <span>@lang('home.home')</span></a>
    </li>
    <li class="nav-item {{ $request->segment(1) == 'home' ? 'active' : '' }}">
        <a class="nav-link" href="{{ action('DashboardLogisticsController@index') }}">
            <i class="fa fa-clone"></i>
            <span>@lang('home.dashboard_logistics')</span></a>
    </li>
    
    @includeIf('subscription::layouts_v2.partials.sidebar')
    
    
    @if ($contact_module)
    @if (auth()->user()->can('supplier.view') ||
    auth()->user()->can('customer.view'))
    <li class="nav-item {{ in_array($request->segment(1), ['contacts', 'customer-group', 'contact-group','List_product_bind','product_bind', 'customer-reference', 'customer-statement', 'outstanding-received-report']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#contacts-menu" aria-expanded="true" aria-controls="contacts-menu">
            <i class="ti-id-badge"></i>
            <span>@lang('contact.contacts')</span>
        </a>
        <div id="contacts-menu" class="collapse {{ in_array($request->segment(1), ['contacts', 'customer-group', 'contact-group','List_product_bind','product_bind', 'customer-reference', 'customer-statement', 'outstanding-received-report']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('contact.contacts'):</h6>
                @if ($contact_supplier)
                @can('supplier.view')
                <a class="collapse-item {{ $request->input('type') == 'supplier' ? 'active' : '' }}" href="{{ action('ContactController@index', ['type' => 'supplier']) }}">@lang('report.supplier')</a>
                @endcan @endif @can('customer.view') @if ($contact_customer) {{-- @if (!$property_module) --}}
                <a class="collapse-item {{ $request->input('type') == 'customer' ? 'active' : '' }}" href="{{ action('ContactController@index', ['type' => 'customer']) }}">@lang('report.customer')</a>
                {{-- @endif --}} @endif @if ($contact_group_customer)
                
                @if($contact_list_customer_loans)
                    <a class="collapse-item  {{ $request->segment(1) == 'contacts' ? 'active' : '' }}" href="{{ action('ShowCustomerLoansController@index') }}">@lang('contact.list_customer_loans')</a>
                @endif
                
                @if($contact_settings)
                    <a class="collapse-item {{ $request->segment(1) == 'contacts' ? 'active' : '' }}" href="{{ action('ContactController@settings') }}">@lang('contact.settings')</a>
                @endif
                
                @if($contact_list_supplier_map_products)
                    <a class="collapse-item {{ $request->segment(1) == 'List_product_bind' && $request->segment(2) == 'product_bind' ? 'active' : '' }}" href="{{ action('SupplierMappingController@index') }}">@lang('lang_v1.list_supplier_map_products')</a>
                @endif
                
                @if($contact_add_supplier_products)
                   <a class="collapse-item {{ $request->segment(1) == 'product_bind' && $request->segment(2) == 'product_bind' ? 'active' : '' }}" href="{{ action('SupplierMappingController@addMapping') }}">@lang('lang_v1.add_supplier_map_products')</a>
                @endif
                
                <a class="collapse-item {{ $request->segment(1) == 'contact-group' ? 'active' : '' }}" href="{{ action('ContactGroupController@index') }}">@lang('lang_v1.contact_groups')</a>
                @endif @endcan @if ($import_contact)
                @if (!$property_module && $contact_customer)
                @if (auth()->user()->can('supplier.create') ||
                auth()->user()->can('customer.create'))
                <a class="collapse-item {{ $request->segment(1) == 'contacts' && $request->segment(2) == 'import' ? 'active' : '' }}" href="{{ action('ContactController@getImportContacts') }}">@lang('lang_v1.import_contacts')</a>
                @endcan @endif @endif @if ($customer_reference)
                <a class="collapse-item {{ $request->segment(1) == 'customer-reference' ? 'active' : '' }}" href="{{ action('CustomerReferenceController@index') }}">@lang('lang_v1.customer_reference')</a>
                @endif @if ($contact_customer)
                @if ($customer_statement)
                <a class="collapse-item {{ $request->segment(1) == 'customer-statement' ? 'active' : '' }}" href="{{ action('CustomerStatementController@index') }}">@lang('contact.customer_statements')</a>
                @endif 
                @if ($customer_statement_pmt)
                 <a class="collapse-item {{ $request->segment(1) == 'customer-statement' ? 'active' : '' }}" href="{{url('customer-statement/get-statement-list-pmts')}}">@lang('contact.customer_statements_with_payment')</a>
                @endif
                @if ($customer_payment)
                <a class="collapse-item {{ $request->segment(1) == 'customer-payment-simple' ? 'active' : '' }}" href="{{ action('CustomerPaymentController@index') }}">@lang('lang_v1.customer_payments')</a>
                @endif @if ($outstanding_received)
                <a class="collapse-item {{ $request->segment(1) == 'outstanding-received-report' ? 'active' : '' }}" href="{{ action('ContactController@getOutstandingReceivedReport') }}">@lang('lang_v1.outstanding_received')</a>
                
                @if($contact_import_opening_balalnces)
                    <a class="collapse-item {{ $request->segment(1) == 'import-balance' ? 'active' : '' }}" href="{{ action('ContactController@getImportBalance') }}">@lang('lang_v1.import_contacts_balance')</a>
                @endif
                
                @endif @endif @if ($contact_supplier)
                @if ($issue_payment_detail)
                <a class="collapse-item {{ $request->segment(1) == 'issued-payment-details' ? 'active' : '' }}" href="{{ action('ContactController@getIssuedPaymentDetails') }}">@lang('lang_v1.issued_payment_details')</a>
                @endif
                @endif
                
                @if($contact_returned_cheque_details)
                    <a class="collapse-item {{ $request->segment(1) == 'returned-cheque-details' ? 'active' : '' }}" href="{{ action('ContactController@getReturnedCheques') }}">@lang('sale.returned_cheques_details')</a>
                @endif
                
                <a class="collapse-item {{ $request->segment(1) == 'contact-user-activity' ? 'active' : '' }}" href="{{ action('CustomerStatementController@getUserActivityReport') }}">@lang('lang_v1.contact_module_user_activity')</a>
            </div>
        </div>
    </li>
    @endif
    @endif
    
    @includeIf('bakery::layouts_v2.partials.sidebar')

    @endif @endif @if (auth()->user()->is_pump_operator)
    @if (auth()->user()->can('pump_operator.dashboard'))
    <li class=" nav-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'pump-operators' && $request->segment(3) == 'dashboard' ? 'active' : '' }}">
        <a href="{{ action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard') }}"><i class="fa fa-tachometer"></i> <span>@lang('petro::lang.dashboard')</span></a>
    </li>
    @endif
    <li class="nav-item {{ $request->segment(1) == 'petro' && $request->segment(2) == 'pump-operators' && $request->segment(3) == 'pumper-day-entries' ? 'active' : '' }}">
        <a href="{{ action('\Modules\Petro\Http\Controllers\PumperDayEntryController@index') }}"><i class="fa fa-calculator"></i> <span>@lang('petro::lang.pumper_day_entries')</span></a>
    </li>
    @endif

    @if ($is_admin && $patient_module)
        @includeIf('myhealth::layouts_v2.partials.sidebar')
    @endif


    @if (auth()->user()->is_customer == 0)
    @if (auth()->user()->can('crm.view'))
    @if ($enable_crm == 1)
    <li class="nav-item {{ in_array($request->segment(1), ['crm']) ? 'active active-sub' : '' }}">

        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#crm-menu" aria-expanded="true" aria-controls="crm-menu">
            <i class="fa fa-users"></i>
            <span>@lang('lang_v1.crm')</span>
        </a>
        <div id="crm-menu" class="collapse {{ in_array($request->segment(1), ['crm']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('lang_v1.crm'):</h6>
                @can('crm.view')
                <a class="collapse-item {{ $request->segment(1) == 'crm' && $request->input('type') == 'customer' ? 'active' : '' }}" href="{{ action('CRMController@index') }}">@lang('lang_v1.crm')</a>
                <a class="collapse-item {{ $request->segment(1) == 'crmgroups' ? 'active' : '' }}" href="{{ action('CrmGroupController@index') }}">@lang('lang_v1.crm_group')</a>
                @endcan
                <a class="collapse-item {{ $request->segment(1) == 'crm-activity' ? 'active' : '' }}" href="{{ action('CRMActivityController@index') }}">@lang('lang_v1.crm_activity')</a>
            </div>
        </div>
    </li>

    @endif
    @endif
    @endif

    @if ($leads_module)
    @includeIf('leads::layouts_v2.partials.sidebar')
    @endif


    <!-- Start Task Management Module -->
    @if ($tasks_management)
    @can('tasks_management.access')
    @includeIf('tasksmanagement::layouts_v2.partials.sidebar')
    @endcan
    @endif




    @if ($installment_module)
    @includeIf('installment::layouts.partials.sidebar')
    @endif

    @if (Auth::guard('agent')->check())
    @includeIf('agent::layouts_v2.partials.sidebar')
    @endif

   
    
    @if($list_credit_sales_page)
        <li class="nav-item {{ in_array($request->segment(1), ['credit-sales']) ? 'active active-sub' : '' }}">
            <a class="nav-link" href="{{ action('ContactCreditSales@index') }}">
                <i class="fa fa-qrcode"></i>
                <span>@lang('contact_credit_sales.credit_sales')</span></a>
        </li>
    @endif

    @if ($crm_module)
    @can('crm.access')
    @includeIf('crm::layouts.sidebar')
    @endcan
    @endif
    
    @if($ezy_products)
    
        <li class="nav-item ">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#ezy-products-menu" aria-expanded="true" aria-controls="ezy-products-menu">
                <i class="ti-id-badge"></i>
                <span>@lang('petro::lang.ezy_products')</span>
            </a>
            <div id="ezy-products-menu" class="collapse " aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                   <h6 class="collapse-header">@lang('petro::lang.ezy_products'):</h6>
                    
                    @if($ezy_list_products)
                        <a class="collapse-item {{ $request->segment(1) == 'products' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ action('\Modules\Petro\Http\Controllers\ProductController@index') }}">@lang('lang_v1.list_products')</a>
                    @endif
                    
                    @if($ezy_units)
                        <a class="collapse-item {{ $request->segment(1) == 'units' ? 'active' : '' }}" href="{{ action('\Modules\Petro\Http\Controllers\UnitController@index') }}">@lang('unit.units')</a>
                    @endif
                    
                    @if($ezy_categories)
                        <a class="collapse-item {{ $request->segment(1) == 'categories' ? 'active' : '' }}" href="{{ action('\Modules\Petro\Http\Controllers\CategoryController@index') }}">@lang('category.categories')</a>
                    @endif
                    
                </div>
            </div>
        </li>
    @endif

    @if($ezyinvoice_module == 1)
    @can('ezyinvoice.access')
    @includeIf('ezyinvoice::layouts.nav')
    @endcan
    @endif

    @if($airline_module == 1)
    @can('airline.access')
    @includeIf('airline::layouts.partials.sidebar')
    @endcan
    @endif


    @if ($shipping_module)
    @can('shipping.access')
    @includeIf('shipping::layouts_v2.partials.sidebar')
    @endcan
    @endif

    @if ($property_module)
    @includeIf('property::layouts_v2.partials.sidebar')
    @endif
    
    @if($products)
    @if (auth()->user()->can('product.view') ||
    auth()->user()->can('product.create') ||
    auth()->user()->can('brand.view') ||
    auth()->user()->can('unit.view') ||
    auth()->user()->can('category.view') ||
    auth()->user()->can('product.product_bind') ||
    auth()->user()->can('brand.create') ||
    auth()->user()->can('unit.create') || 
    auth()->user()->can('category.create'))

    <li class="nav-item {{ in_array($request->segment(1), ['variation-templates', 'products', 'labels','product_bind', 'stock_conversion', 'import-products', 'import-opening-stock', 'selling-price-group', 'brands', 'units', 'categories', 'warranties']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#products-menu" aria-expanded="true" aria-controls="products-menu">
            <i class="ti-layout-media-right-alt"></i>
            <span>@lang('sale.products')</span>
        </a>
        <div id="products-menu" class="collapse {{ in_array($request->segment(1), ['variation-templates', 'products', 'labels','product_bind', 'stock_conversion', 'import-products', 'import-opening-stock', 'selling-price-group', 'brands', 'units', 'categories', 'warranties']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sale.products'):</h6>
                
                @if((array_key_exists('products_list_product',$pacakge_details) && !empty($pacakge_details['products_list_product'])) || !array_key_exists('products_list_product',$pacakge_details) )
                    @can('product.view')
                        <a class="collapse-item {{ $request->segment(1) == 'products' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ action('ProductController@index') }}">@lang('lang_v1.list_products')</a>
                    @endcan 
                @endif
                
                @if((array_key_exists('products_add_edit',$pacakge_details) && !empty($pacakge_details['products_add_edit'])) || !array_key_exists('products_add_edit',$pacakge_details) )
                    @can('product.create')
                        <a class="collapse-item {{ $request->segment(1) == 'products' && $request->segment(2) == 'create' ? 'active' : '' }}" href="{{ action('ProductController@create') }}">@lang('product.add_product')</a>
                    @endcan 
                @endif
                
                @can('product.view')
                    @if($product_print_labels)
                        <a class="collapse-item {{ $request->segment(1) == 'labels' && $request->segment(2) == 'show' ? 'active' : '' }}" href="{{ action('LabelsController@show') }}">@lang('barcode.print_labels')</a>
                    @endif
                @endcan @can('product.product_bind')
                
                @endcan 
                
                @if((array_key_exists('products_variations',$pacakge_details) && !empty($pacakge_details['products_variations'])) || !array_key_exists('products_variations',$pacakge_details) )
                    @can('product.create')
                        <a class="collapse-item {{ $request->segment(1) == 'variation-templates' ? 'active' : '' }}" href="{{ action('VariationTemplateController@index') }}">@lang('product.variations')</a>
                    @endcan
                @endif
                
                @if((array_key_exists('products_import',$pacakge_details) && !empty($pacakge_details['products_import'])) || !array_key_exists('products_import',$pacakge_details) )
                    @can('product.create')
                        <a class="collapse-item {{ $request->segment(1) == 'import-products' ? 'active' : '' }}" href="{{ action('ImportProductsController@index') }}">@lang('product.import_products')</a>
                    @endcan
                @endif
                
                
                @if (session()->get('business.is_pharmacy'))
                <a class="collapse-item {{ $request->segment(1) == 'sample-medical-product-list' ? 'active' : '' }}" href="{{ action('SampleMedicalProductController@index') }}">@lang('lang_v1.sample_medical_product_list')</a>
                @endif 
                
                @if((array_key_exists('products_import_opening_stock',$pacakge_details) && !empty($pacakge_details['products_import_opening_stock'])) || !array_key_exists('products_import_opening_stock',$pacakge_details) )
                    @can('product.opening_stock')
                        <a class="collapse-item {{ $request->segment(1) == 'import-opening-stock' ? 'active' : '' }}" href="{{ action('ImportOpeningStockController@index') }}">@lang('lang_v1.import_opening_stock')</a>
                    @endcan 
                @endif
                
                @if((array_key_exists('products_selling_price_group',$pacakge_details) && !empty($pacakge_details['products_selling_price_group'])) || !array_key_exists('products_selling_price_group',$pacakge_details) )
                    @can('product.create')
                        <a class="collapse-item {{ $request->segment(1) == 'selling-price-group' ? 'active' : '' }}" href="{{ action('SellingPriceGroupController@index') }}">@lang('lang_v1.selling_price_group')</a>
                    @endcan 
                @endif
                
                @if (auth()->user()->can('unit.view') ||
                auth()->user()->can('unit.create'))
                
                    @if((array_key_exists('products_units',$pacakge_details) && !empty($pacakge_details['products_units'])) || !array_key_exists('products_units',$pacakge_details) )
                        <a class="collapse-item {{ $request->segment(1) == 'units' ? 'active' : '' }}" href="{{ action('UnitController@index') }}">@lang('unit.units')</a>
                    @endif
                    
                    @if((array_key_exists('products_stock_conversion',$pacakge_details) && !empty($pacakge_details['products_stock_conversion'])) || !array_key_exists('products_stock_conversion',$pacakge_details) )
                        <a class="collapse-item {{ $request->segment(1) == 'stock_conversion' && $request->segment(2) == 'stock_conversion' ? 'active' : '' }}" href="{{ action('StockConversionController@index') }}">@lang('Stock Conversion')</a>
                    @endif
                
                @endif 
                
                @if((array_key_exists('products_categories',$pacakge_details) && !empty($pacakge_details['products_categories'])) || !array_key_exists('products_categories',$pacakge_details) )
                    @if (auth()->user()->can('category.view') ||
                    auth()->user()->can('category.create'))
                        <a class="collapse-item {{ $request->segment(1) == 'categories' ? 'active' : '' }}" href="{{ action('CategoryController@index') }}">@lang('category.categories')</a>
                    @endif
                @endif
                
                @if((array_key_exists('products_brand_warranties',$pacakge_details) && !empty($pacakge_details['products_brand_warranties']) ) || !array_key_exists('products_brand_warranties',$pacakge_details) )
                    @if (auth()->user()->can('brand.view') ||
                    auth()->user()->can('brand.create'))
                        <a class="collapse-item {{ $request->segment(1) == 'brands' ? 'active' : '' }}" href="{{ action('BrandController@index') }}">@lang('brand.brands')</a>
                    @endif
                
                    <a class="collapse-item {{ $request->segment(1) == 'warranties' ? 'active active-sub' : '' }}" href="{{ action('WarrantyController@index') }}">@lang('lang_v1.warranties')</a>
                @endif
                
                @if ($stock_taking_page)
                <a class="collapse-item {{ $request->segment(1) == 'stock-taking' ? 'active' : '' }}" href="{{ action('StockTakingController@index') }}">@lang('mpcs::lang.StockTaking_form')</a>
                @endif
                @if ($enable_petro_module)
                @if ($merge_sub_category)
                <a class="collapse-item {{ $request->segment(1) == 'merged-sub-categories' ? 'active active-sub' : '' }}" href="{{ action('MergedSubCategoryController@index') }}">@lang('lang_v1.merged_sub_categories')</a>
                @endif
                @endif
            </div>
        </div>
    </li>
    @endif
    @endif
    
    
    @if ($hms_module)
    @can('hms.access')
     
   
      
       <li class="nav-item {{in_array($request->segment(2), ['dashboard','rooms','pricing','bookings','calendar','extras','unavailables','coupons','reports','amenities','settings']) ? 'active active-sub' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#hms-menu" aria-expanded="true" aria-controls="hms-menu">
                <i class="ti-file"></i>
                <span>HMS</span>
            </a>
            <div id="hms-menu" class="collapse {{in_array($request->segment(2), ['dashboard','rooms','pricing','bookings','calendar','extras','unavailables','coupons','reports','amenities','settings']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                     <h6 class="collapse-header">Hms:</h6>
                    <a class="collapse-item {{ $request->segment(2) == 'dashboard'? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsController::class, 'index'])}}">Dashboard</a>
                    <a class="collapse-item {{ $request->segment(2) == 'rooms'? 'active' : '' }}"  href="{{action([Modules\Hms\Http\Controllers\RoomController::class, 'index'])}}">@lang('hms::lang.rooms')</a>
                     <a class="collapse-item {{ $request->segment(2) == 'pricing'? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\RoomController::class, 'pricing'])}}">@lang('hms::lang.prices')</a>
                    <a class="collapse-item {{ $request->segment(2) == 'bookings'? 'active' : '' }}"  href="{{action([Modules\Hms\Http\Controllers\HmsBookingController::class, 'index'])}}">@lang('hms::lang.bookings')</a>
                    <a class="collapse-item {{ $request->segment(2) == 'calendar'? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsBookingController::class, 'calendar'])}}">@lang('hms::lang.calendar')</a>
                    <a class="collapse-item {{ $request->segment(2) == 'extras'? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\ExtraController::class, 'index'])}}">@lang('hms::lang.extras')</a>
                    <a class="collapse-item {{ $request->segment(2) == 'unavailables'? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\UnavailableController::class, 'index'])}}">@lang('hms::lang.unavailable')</a>
                    <a class="collapse-item {{ $request->segment(2) == 'coupons'? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsCouponController::class, 'index'])}}">@lang('hms::lang.coupons')</a>
                    <a class="collapse-item {{ $request->segment(2) == 'reports'? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsReportController::class, 'index'])}}">@lang('hms::lang.reports')</a>
                    <a class="collapse-item {{ $request->segment(2) == 'amenities'? 'active' : '' }}" href="{{action([\App\Http\Controllers\TaxonomyController::class, 'index']) . '?type=amenities'}}">@lang('hms::lang.amenities')</a>
                    <a class="collapse-item {{ $request->segment(2) == 'settings'? 'active' : '' }}" href="{{action([Modules\Hms\Http\Controllers\HmsSettingController::class, 'index'])}}">@lang('messages.settings')</a>

                 </div>
            </div>
        </li>
      
   
        @endcan
        @endif 
    
   
     

    <!-- Start Petro Module -->
    @if ($enable_petro_module)
    @if (auth()->user()->can('petro.access')) @includeIf('petro::layouts_v2.partials.sidebar')
    @endif
    @endif

    @if($dsr_module || $ns_dsr_module)
        @includeIf('dsr::layouts_v2.partials.sidebar')
    @endif
        
    
    @if($issue_customer_bill)

    @can('issue_customer_bill.access')

    <li class="nav-item {{in_array($request->segment(2), ['issue-customer-bill']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#customerbill-menu" aria-expanded="true" aria-controls="customerbill-menu">
            <i class="ti-file"></i>
            <span>@lang('petro::lang.bill_to_customer')</span>
        </a>
        <div id="customerbill-menu " class="collapse {{in_array($request->segment(2), ['issue-customer-bill']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('petro::lang.bill_to_customer'):</h6>
                <a class="collapse-item {{ $request->segment(2) == 'issue-customer-bill'? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\IssueCustomerBillController@index')}}">@lang('petro::lang.issue_bills_customer')</a>
            </div>
        </div>
    </li>
    
    @endcan

    @endif
    
    
    @if($issue_customer_bill_vat)
        <li class="nav-item {{in_array($request->segment(2), ['issue-customer-bill-with-vat']) ? 'active active-sub' : '' }}">
            <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#customerbillwithvat-menu" aria-expanded="true" aria-controls="customerbillwithvat-menu">
                <i class="ti-file"></i>
                <span>@lang('superadmin::lang.issue_customer_bill_vat')</span>
            </a>
            <div id="customerbillwithvat-menu" class="collapse {{in_array($request->segment(2), ['issue-customer-bill-with-vat']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
                <div class="bg-white py-2 collapse-inner rounded">
                     <h6 class="collapse-header">@lang('superadmin::lang.issue_customer_bill_vat'):</h6>
                    <a class="collapse-item {{ $request->segment(2) == 'issue-customer-bill-with-vat'? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\IssueCustomerBillWithVATController@index')}}">@lang('superadmin::lang.issue_customer_bill_vat')</a>
                    <a class="collapse-item {{ $request->segment(2) == 'issue-customer-bill-with-vat'? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\CustomerBillVatPrefixController@index')}}">@lang('petro::lang.prefix_and_starting_nos')</a>
                </div>
            </div>
        </li>
    @endif
    
    
    <!-- End Petro Module -->
    @if ($distribution_module)
    <li class="nav-item {{ in_array($request->segment(1), ['distribution']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#distribution-menu" aria-expanded="true" aria-controls="distribution-menu">
            <i class="ti-car"></i>
            <span>Distribution</span>
        </a>
        <div id="distribution-menu" class="collapse {{ in_array($request->segment(1), ['distribution']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Distribution:</h6>
                <a class="collapse-item {{ $request->segment(1) == 'vehicle' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ action('\Modules\Distribution\Http\Controllers\SettingController@index') }}">Settings</a>
            </div>
        </div>
    </li>
    @endif

    @if ($spreadsheet)
    <li class="nav-item {{ in_array($request->segment(1), ['spreadsheet']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#spreadsheet-menu" aria-expanded="true" aria-controls="spreadsheet-menu">
            <i class="fas fa fa-file-excel"></i>
            <span>Spreadsheet</span>
        </a>
        <div id="spreadsheet-menu" class="collapse {{ in_array($request->segment(1), ['spreadsheet']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">Spreadsheet:</h6>

                <a class="collapse-item {{ $request->segment(1) == 'spreadsheet' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ action([\Modules\Spreadsheet\Http\Controllers\SpreadsheetController::class, 'index']) }}">Spreadsheet</a>
            </div>
        </div>
    </li>
    @endif

    @if ($petro_quota_module)

    <li class="nav-item {{ in_array($request->segment(1), ['vehicles']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#petroquota-menu" aria-expanded="true" aria-controls="petroquota-menu">
            <i class="ti-car"></i>
            <span>@lang('vehicle.petro_quota')</span>
        </a>
        <div id="petroquota-menu" class="collapse {{ in_array($request->segment(1), ['vehicles']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('vehicle.petro_quota'):</h6>

                <a class="collapse-item {{ $request->segment(1) == 'vehicles' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ action('\Modules\Petro\Http\Controllers\VehicleController@vehicles_list') }}">@lang('vehicle.registered_vehicle_details')</a>
            </div>
        </div>
    </li>

    @endif

    <!-- Start MPCS Module -->
    @if ($mpcs_module)
    @if (auth()->user()->can('mpcs.access'))
    @includeIf('mpcs::layouts_v2.partials.sidebar')
    @endif
    @endif
    <!-- End MPCS Module -->

    <!-- Start MPCS Module -->
    @if ($price_changes_module)
    @if (auth()->user()->can('pricechanges.access'))
    @includeIf('pricechanges::layouts_v2.partials.sidebar')
    @endif
    @endif
    <!-- End MPCS Module -->

    <!-- Start MPCS Module -->
    @if ($stock_taking_module)
    @if (auth()->user()->can('mpcs.access'))
    @includeIf('Stocktaking::layouts_v2.partials.sidebar')
    @endif
    @endif
    <!-- End MPCS Module -->

    <!-- Start Fleet Module -->
    @if ($fleet_module)
    @if (auth()->user()->can('fleet.access'))
    @includeIf('fleet::layouts_v2.partials.sidebar')
    @endif
    @endif
    <!-- End Fleet Module -->


    <!-- Start Ezyboat Module -->
    @if ($ezyboat_module) {{-- @if (auth()->user()->can('ezyboat.access')) --}}
    @includeIf('ezyboat::layouts_v2.partials.sidebar') {{-- @endif --}} @endif
    <!-- End Ezyboat Module -->


    <!-- Start Gold Module -->
    @if ($ran_module)
    @if (auth()->user()->can('ran.access'))
    @includeIf('ran::layouts_v2.partials.sidebar')
    @endif
    @endif
    <!-- End Gold Module -->


    @if (Module::has('Manufacturing'))
    @if ($mf_module)
    @if (auth()->user()->is_customer == 0)
    @if (auth()->user()->can('manufacturing.access_recipe') ||
    auth()->user()->can('manufacturing.access_production'))
    @include('manufacturing::layouts_v2.partials.sidebar') @endif
    @endif
    @endif
    @endif

    @if ($purchase)
    @if (auth()->user()->can('purchase.view') ||
    auth()->user()->can('purchase.create') ||
    auth()->user()->can('purchase.update'))
    <li class="nav-item {{ in_array($request->segment(1), ['purchases', 'purchase-return', 'import-purchases']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#purchases-menu" aria-expanded="true" aria-controls="purchases-menu">
            <i class="ti-shopping-cart-full"></i>
            <span>@lang('purchase.purchases')</span>
        </a>
        <div id="purchases-menu" class="collapse {{ in_array($request->segment(1), ['purchases', 'purchase-return', 'import-purchases']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('purchase.purchases'):</h6>
                @if ($all_purchase)
                <a class="collapse-item {{ $request->segment(1) == 'purchases' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('PurchaseController@index') }}">@lang('purchase.list_purchase')</a>
                @endif
                @if ($add_bulk_purchase)
                <a class="collapse-item {{ $request->segment(1) == 'purchases' && $request->segment(2) == 'add-purchase-bulk' ? 'active' : '' }}" href="{{ action('PurchaseController@addPurchaseBulk') }}">@lang('purchase.add_purchase_bulk')</a>
                @endif
                @if ($add_purchase)
                <a class="collapse-item {{ $request->segment(1) == 'purchases' && $request->segment(2) == 'create' ? 'active' : '' }}" href="{{ action('PurchaseController@create') }}">@lang('purchase.add_purchase')</a>
                @endif
                @if ($purchase_return)
                <a class="collapse-item {{ $request->segment(1) == 'purchase-return' ? 'active' : '' }}" href="{{ action('PurchaseReturnController@index') }}">@lang('lang_v1.list_purchase_return')</a>
                @endif
                @if ($import_purchase)
                <a class="collapse-item {{ $request->segment(1) == 'import-purchases' ? 'active' : '' }}" href="{{ action('ImportPurchasesController@index') }}">@lang('lang_v1.import_purchases')</a>
                @endif
            </div>
        </div>
    </li>

    @endif
    @endif


    @if ($sale_module)
    @if (auth()->user()->can('sell.view') ||
    auth()->user()->can('sell.create') ||
    auth()->user()->can('direct_sell.access') ||
    auth()->user()->can('view_own_sell_only'))
    <li class="nav-item {{ in_array($request->segment(1), ['sales', 'pos', 'sell-return', 'ecommerce', 'discount', 'shipments', 'import-sales', 'reserved-stocks']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#sale-menu" aria-expanded="true" aria-controls="sale-menu">
            <i class="ti-shopping-cart"></i>
            <span>@lang('sale.sale')</span>
        </a>
        <div id="sale-menu" class="collapse  {{ in_array($request->segment(1), ['sales', 'pos', 'sell-return', 'ecommerce', 'discount', 'shipments', 'import-sales', 'reserved-stocks']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('sale.sale'):</h6>
                @if ($all_sales)
                @if (auth()->user()->can('direct_sell.access') ||
                auth()->user()->can('view_own_sell_only'))
                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('SellController@index') }}">@lang('lang_v1.all_sales')</a>
                @endif
                @endif
                <!-- Call superadmin module if defined -->
                @if (Module::has('Ecommerce'))
                @includeIf('ecommerce::layouts_v2.partials.sell_sidebar') @endif @if ($add_sale) @can('direct_sell.access')
                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == 'create' ? 'active' : '' }}" href="{{ action('SellController@create') }}">@lang('sale.add_sale')</a>
                @endcan @endif @if ($list_pos) @can('sell.view')
                <a class="collapse-item {{ $request->segment(1) == 'pos' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('SellPosController@index') }}">@lang('sale.list_pos')</a>
                @endcan @endif @can('sell.create')
                <a class="collapse-item {{ $request->segment(1) == 'pos' && $request->segment(2) == 'create' ? 'active' : '' }}" href="{{ action('SellPosController@create') }}">@lang('sale.pos_sale')</a>
                @endcan @if ($list_draft)
                @can('list_drafts')
                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == 'drafts' ? 'active' : '' }}" href="{{ action('SellController@getDrafts') }}">@lang('lang_v1.list_drafts')</a>
                @endcan @endif @if ($list_quotation)
                @can('list_quotations')
                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == 'quotations' ? 'active' : '' }}" href="{{ action('SellController@getQuotations') }}">@lang('lang_v1.list_quotations')</a>
                @endcan @endif @if ($customer_order_own_customer == 1 || $customer_order_general_customer == 1)
                @if ($list_orders)
                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == 'customer-orders' ? 'active' : '' }}" href="{{ action('SellController@getCustomerOrders') }}">@lang('lang_v1.list_orders')</a>
                @endif @if ($upload_orders)
                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == 'customer-orders' ? 'active' : '' }}" href="{{ action('SellController@getCustomerUploadedOrders') }}">@lang('customer.uploaded_orders')</a>
                @endif @endif @if ($list_sell_return)
                @can('sell.view')
                <a class="collapse-item {{ $request->segment(1) == 'sell-return' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('SellReturnController@index') }}">@lang('lang_v1.list_sell_return')</a>
                @endcan @endif @if ($shipment)
                @can('access_shipping')
                <a class="collapse-item {{ $request->segment(1) == 'shipments' ? 'active' : '' }}" href="{{ action('SellController@shipments') }}">@lang('lang_v1.shipments')</a>
                @endcan @endif @if ($discount)
                @can('discount.access')
                <a class="collapse-item {{ $request->segment(1) == 'discount' ? 'active' : '' }}" href="{{ action('DiscountController@index') }}">@lang('lang_v1.discounts')</a>
                @endcan @endif @if ($subcriptions)
                @if (auth()->user()->can('direct_sell.access'))
                <a class="collapse-item {{ $request->segment(1) == 'subscriptions' ? 'active' : '' }}" href="{{ action('SellPosController@listSubscriptions') }}">@lang('lang_v1.subscriptions')</a>
                @endif
                @endif
                @if ($import_sale)
                <a class="collapse-item {{ $request->segment(1) == 'import-sales' ? 'active' : '' }}" href="{{ action('ImportSalesController@index') }}">@lang('lang_v1.import_sales')</a>
                @endif @if ($reserved_stock)
                <a class="collapse-item {{ $request->segment(1) == 'reserved-stocks' ? 'active' : '' }}" href="{{ action('ReservedStocksController@index') }}">@lang('lang_v1.reserved_stocks')</a>

                @endif
                @if ($customer_settings)
                @if ($over_limit_sales)
                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == 'over-limit-sales' ? 'active' : '' }}" href="{{ action('SellController@overLimitSales') }}">@lang('sale.over_limit_sales')</a>
                @endif
                @endif
            </div>
        </div>
    </li>
    @endif @endif

    @if($tpos_module)
    <li class="nav-item {{ in_array($request->segment(1), ['sell', 'tpos', 'fpos']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#tpos-menu" aria-expanded="true" aria-controls="sale-menu">
            <i class="ti-shopping-cart"></i>
            <span>@lang('tpos.tpos')</span>
        </a>
        <div id="tpos-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('tpos.tpos'):</h6>
                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('TposController@create') }}">@lang('tpos.add_tpos')</a>

                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('TposController@index') }}">@lang('tpos.list_tpos')</a>

                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('TposController@createFpos') }}">@lang('tpos.add_fpos')</a>

                <a class="collapse-item {{ $request->segment(1) == 'sales' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('TposController@indexFpos') }}">@lang('tpos.list_fpos')</a>
            </div>
        </div>
    </li>
    @endif




    @if (Module::has('Repair'))
    @if ($repair_module)
    @if (auth()->user()->can('repair.access'))

    @includeIf('repair::layouts.sidebar')
  

    @endif @endif @endif
    
  
    @if ($auto_services_and_repair_module)
    @if (auth()->user()->can('repair.access'))

    
    @includeIf('autorepairservices::layouts.sidebar')

    @endif @endif 

    @if ($stock_transfer)
    @if (auth()->user()->can('purchase.view') ||
    auth()->user()->can('purchase.create'))

    <li class="nav-item {{ $request->segment(1) == 'stock-transfers' || $request->segment(1) == 'stock-transfers-request' ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#stocktransfer-menu" aria-expanded="true" aria-controls="stocktransfer-menu">
            <i class="fa fa-truck"></i>
            <span>@lang('lang_v1.stock_transfers')</span>
        </a>
        <div id="stocktransfer-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('lang_v1.stock_transfers'):</h6>
                @can('purchase.view')
                <a class="collapse-item {{ $request->segment(1) == 'stock-transfers' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('StockTransferController@index') }}">@lang('lang_v1.list_stock_transfers')</a>
                @endcan @can('purchase.create')
                <a class="collapse-item {{ $request->segment(1) == 'stock-transfers' && $request->segment(2) == 'create' ? 'active' : '' }}" href="{{ action('StockTransferController@create') }}">@lang('lang_v1.add_stock_transfer')</a>
                @endcan {{-- @can('purchase.create') --}}
                <a class="collapse-item {{ $request->segment(1) == 'stock-transfers-request' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('StockTransferRequestController@index') }}">@lang('lang_v1.stock_transfer_request')</a>
                {{-- @endcan --}}
            </div>
        </div>
    </li>
    @endif
    @endif

    @if ($stock_adjustment)
    {{-- @if (in_array('stock_adjustment', $enabled_modules)) --}}
    {{-- @if (auth()->user()->can('purchase.view') || auth()->user()->can('purchase.create')) --}}
    <li class="nav-item {{ $request->segment(1) == 'stock-adjustments' ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#stockadjustments-menu" aria-expanded="true" aria-controls="stockadjustments-menu">
            <i class="fa fa-database"></i>
            <span>@lang('stock_adjustment.stock_adjustment')</span>
        </a>
        <div id="stockadjustments-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('stock_adjustment.stock_adjustment'):</h6>
                @can('purchase.view')
                <a class="collapse-item {{ $request->segment(1) == 'stock-adjustments' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('StockAdjustmentController@index') }}">@lang('stock_adjustment.list')</a>
                @endcan @can('purchase.create')
                <a class="collapse-item {{ $request->segment(1) == 'stock-adjustments' && $request->segment(2) == 'create' ? 'active' : '' }}" href="{{ action('StockAdjustmentController@create') }}">@lang('stock_adjustment.add')</a>
                @endcan

                <a class="collapse-item {{ $request->segment(1) == 'stock-settings' && $request->segment(2) == null ? 'active' : '' }}" href="{{ action('StockAdjustmentSettings@create') }}">@lang('stock_adjustment_settings.list')</a>
            </div>
        </div>
    </li>
    {{-- @endif --}}
    {{-- @endif --}}
    @endif

    @if ($expenses)
    @if (auth()->user()->can('expense.access'))

    <li class="nav-item {{ in_array($request->segment(1), ['expense-categories', 'expenses']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#expenses-menu" aria-expanded="true" aria-controls="expenses-menu">
            <i class="fa fa-money"></i>
            <span>@lang('expense.expenses')</span>
        </a>
        <div id="expenses-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('expense.expenses'):</h6>
                <a class="collapse-item {{ $request->segment(1) == 'expenses' && empty($request->segment(2)) ? 'active' : '' }}" href="{{ action('ExpenseController@index') }}">@lang('lang_v1.list_expenses')</a>
                <a class="collapse-item {{ $request->segment(1) == 'expenses' && $request->segment(2) == 'create' ? 'active' : '' }}" href="{{ action('ExpenseController@create') }}">@lang('messages.add')
                    @lang('expense.expenses')</a>

                <a class="collapse-item {{ $request->segment(1) == 'expense-categories' ? 'active' : '' }}" href="{{ action('ExpenseCategoryController@index') }}">@lang('expense.expense_categories')</a>

                <a class="collapse-item {{ $request->segment(1) == 'expense-categories-code' ? 'active' : '' }}" href="{{ action('ExpenseCategoryCodeController@index') }}">@lang('expense.expense_categories_code')</a>
            </div>
        </div>
    </li>
    @endif
    @endif

    <!-- Start PayRoll Module -->
    @if ($payday)
    @if (auth()->user()->can('payday') &&
    !auth()->user()->is_pump_operator &&
    !auth()->user()->is_property_user)
    <li class="nav-item">
        <a class="nav-link" href="#" id="login_payroll">
            <i class="fa fa-briefcase"></i>
            <span>PayRoll</span></a>
    </li>

    @endif
    @endif
    <!-- End PayRoll Module -->

    @if ($loan_module)
    @include('loan::layouts.nav')
    @endif

    <!-- End Task Management Module -->
    @if ($banking_module == 1 || $access_account == 1)
    @can('account.access')
    <li class="nav-item {{ $request->segment(1) == 'accounting-module' ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#accounting-menu" aria-expanded="true" aria-controls="accounting-menu">
            <i class="fa fa-money"></i>
            <span>
                @if ($access_account)
                @lang('account.accounting_module')
                @else
                @lang('account.banking_module') @endif
            </span>
        </a>
        <div id="accounting-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                    @if ($access_account)
                    @lang('account.accounting_module')
                    @else
                    @lang('account.banking_module') @endif:
                </h6>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'account' ? 'active' : '' }}" href="{{ action('AccountController@index') }}">@lang('account.list_accounts')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'disabled-account' ? 'active' : '' }}" href="{{ action('AccountController@disabledAccount') }}">@lang('account.disabled_account')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'journals' ? 'active' : '' }}" href="{{ action('JournalController@index') }}">@lang('account.list_journals')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'get-profit-loss-report' ? 'active' : '' }}" href="{{ action('AccountController@getProfitLossReport') }}">@lang('lang_v1.profit_loss_report')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'income-statement' ? 'active' : '' }}" href="{{ action('AccountReportsController@incomeStatement') }}">@lang('account.income_statement')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'balance-sheet' ? 'active' : '' }}" href="{{ action('AccountReportsController@balanceSheet') }}">@lang('account.balance_sheet')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'balance-sheet-comparison' ? 'active' : '' }}" href="{{ action('AccountReportsController@balanceSheetComparison') }}">@lang('account.balance_sheet_comparison')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'fixed-asset' ? 'active' : '' }}" href="{{ action('FixedAssetController@index') }}">@lang('account.fixed_assets')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'trial-balance' ? 'active' : '' }}" href="{{ action('AccountReportsController@trialBalance') }}">@lang('account.trial_balance')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'trial-balance-cumulative' ? 'active' : '' }}" href="{{ action('AccountReportsController@trialBalanceCumulative') }}">@lang('account.trial_balance_cumulative')</a>
                
                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'cash-flow' ? 'active' : '' }}" href="{{ action('AccountController@cashFlow') }}">@lang('lang_v1.cash_flow')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'payment-account-report' ? 'active' : '' }}" href="{{ action('AccountReportsController@paymentAccountReport') }}">@lang('account.payment_account_report')</a>

                <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'import' ? 'active' : '' }}" href="{{ action('AccountController@getImportAccounts') }}">@lang('lang_v1.import_accounts')</a>
                
            </div>
        </div>
    </li>
    
    @if($post_dated_cheque)
    <li class="nav-item {{ $request->segment(2) == 'post-dated-cheques' ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#post-dated-cheques-menu" aria-expanded="true" aria-controls="post-dated-cheques-menu">
            <i class="fa fa-money"></i>
            <span>
                @lang('account.pd_cheques_management')
                
            </span>
        </a>
        <div id="post-dated-cheques-menu" class="collapse {{ $request->segment(2) == 'post-dated-cheques' ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                     @lang('account.pd_cheques_management')
                </h6>
                    <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'postdated-cheques' ? 'active' : '' }}" href="{{ action('PostdatedChequeController@create') }}">@lang('account.add_pd_cheques')</a>
                    <a class="collapse-item {{ $request->segment(1) == 'accounting-module' && $request->segment(2) == 'postdated-cheques' ? 'active' : '' }}" href="{{ action('PostdatedChequeController@index') }}">@lang('account.post_dated_cheque')</a>
                
            </div>
        </div>
    </li>
    @endif
    @endcan
    @endif
    
    @if($deposits_module || $ns_deposits_module)

    <li class="nav-item {{ in_array($request->segment(1), ['deposits-module']) ? 'active active-sub' : '' }}">
        <a class="nav-link" href="{{ action('DepositsController@index') }}">
            <i class="fa fa-money"></i>
            <span>@lang('deposits.deposits_module')</span></a>
    </li>
    @endif
    
    @if($realize_cheque)
        <li class="nav-item {{ in_array($request->segment(1), ['accounting-module']) ? 'active active-sub' : '' }}">
            <a class="nav-link" href="{{ action('RealizedChequeController@index') }}">
                <i class="fa fa-money"></i>
                <span>@lang('account.list_realize_cheque')</span></a>
        </li>
    @endif
    
    @if($docmanagement_module)
         @includeIf('docmanagement::layouts.partials.sidebar') 
    @endif
    @includeIf('salesdiscounts::layouts.partials.sidebar')
    @if($asset_module || $ns_asset_management)
        @includeIf('assetmanagement::layouts.nav')
    @endif
    
    @if($vat_module || $ns_vat_module)
        @includeIf('vat::layouts_v2.partials.sidebar')
    @endif
    
    @if ($report_module)
    @if (auth()->user()->can('report.access'))

    <li class="nav-item {{ in_array($request->segment(1), ['reports']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#reports-menu" aria-expanded="true" aria-controls="reports-menu">
            <i class="fa fa-bar-chart"></i>
            <span>@lang('report.reports')</span>
        </a>
        <div id="reports-menu" class="collapse {{ in_array($request->segment(1), ['reports']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('report.reports'):</h6>
                @if ($product_report)
                @if (auth()->user()->can('stock_report.view') ||
                auth()->user()->can('stock_adjustment_report.view') ||
                auth()->user()->can('item_report.view') ||
                auth()->user()->can('product_purchase_report.view') ||
                auth()->user()->can('product_sell_report.view') ||
                auth()->user()->can('product_transaction_report.view'))
                <a class="collapse-item {{ $request->segment(2) == 'product' ? 'active' : '' }}" href="{{ action('ReportController@getProductReport') }}">@lang('report.product_report')</a>
                @endif
                @endif 
                @if ($payment_status_report)
                @if (auth()->user()->can('purchase_payment_report.view') ||
                auth()->user()->can('sell_payment_report.view') ||
                auth()->user()->can('outstanding_received_report.view') ||
                auth()->user()->can('aging_report.view'))

                <a class="collapse-item {{ $request->segment(2) == 'payment-status' ? 'active' : '' }}" href="{{ action('ReportController@getPaymentStatusReport') }}">@lang('report.payment_status_report')</a>
                @endif @endif @if (auth()->user()->can('daily_report.view') ||
                auth()->user()->can('daily_summary_report.view') ||
                auth()->user()->can('register_report.view') ||
                auth()->user()->can('profit_loss_report.view'))
                <a class="collapse-item {{ $request->segment(2) == 'management' ? 'active' : '' }}" href="{{ action('ReportController@getManagementReport') }}">@lang('report.management_report')</a>

                @endif @if ($verification_report || $report_verification)
                <a class="collapse-item {{ $request->segment(2) == 'verification' ? 'active' : '' }}" href="{{ action('ReportController@getVerificationReport') }}">@lang('report.verification_reports')</a>
                @endif @if ($activity_report)
                @if (auth()->user()->can('sales_report.view') ||
                auth()->user()->can('purchase_and_slae_report.view') ||
                auth()->user()->can('expense_report.view') ||
                auth()->user()->can('sales_representative.view') ||
                auth()->user()->can('tax_report.view'))
                    <a class="collapse-item {{ $request->segment(2) == 'activity' ? 'active' : '' }}" href="{{ action('ReportController@getActivityReport') }}">@lang('report.activity_report')</a>
                @endif @endif
                @can('stock_report.view')
                @if (session('business.enable_product_expiry') == 1)
                <a class="collapse-item {{ $request->segment(2) == 'stock-expiry' ? 'active' : '' }}" href="{{ action('ReportController@getStockExpiryReport') }}">@lang('report.stock_expiry_report')</a>
                @endif
                @endcan
                @can('stock_report.view')
                @if (session('business.enable_lot_number') == 1)
                <a class="collapse-item {{ $request->segment(2) == 'lot-report' ? 'active' : '' }}" href="{{ action('ReportController@getLotReport') }}">@lang('lang_v1.lot_report')</a>
                @endif
                @endcan
                @if ($trending_product)
                @can('trending_products.view')
                <a class="collapse-item {{ $request->segment(2) == 'trending-products' ? 'active' : '' }}" href="{{ action('ReportController@getTrendingProducts') }}">@lang('report.trending_products')</a>
                @endcan
                @endif
                @if ($user_activity)
                @can('user_activity.view')
                <a class="collapse-item {{ $request->segment(2) == 'user_activity' ? 'active' : '' }}" href="{{ action('ReportController@getUserActivityReport') }}">@lang('report.user_activity')</a>

                @endcan
                @endif
                @if ($report_table)
                @can('report_table.view')
                <a class="collapse-item {{ $request->segment(2) == 'table-report' ? 'active' : '' }}" href="{{ action('ReportController@getTableReport') }}">@lang('restaurant.table_report')</a>
                @endcan
                @endif
                @if ($report_staff_service)
                @can('sales_representative.view')
                <a class="collapse-item {{ $request->segment(2) == 'service-staff-report' ? 'active' : '' }}" href="{{ action('ReportController@getServiceStaffReport') }}">@lang('restaurant.service_staff_report')</a>

                @endcan
                @endif

                @if ($contact_report)
                @can('contact_report.view')
                <a class="collapse-item {{ $request->segment(2) == 'contact' ? 'active' : '' }}" href="{{ action('ReportController@getContactReport') }}">@lang('report.contact_report')</a>
                @endcan
                @endif

            </div>
        </div>
    </li>

    @endif @endif
    
    
    @if ($report_module)
    @if (auth()->user()->can('report.access') && $customized_report)
    <li class="nav-item {{ in_array($request->segment(1), ['customized_report','129report']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#customized_reports-menu" aria-expanded="true" aria-controls="customized_reports-menu">
            <i class="fa fa-bar-chart"></i>
            <span>@lang('lang_v1.customized_report')</span>
        </a>
        <div id="customized_reports-menu" class="collapse  {{ in_array($request->segment(1), ['customized_report','129report']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">@lang('lang_v1.customized_report'):</h6>
          
                <a class="collapse-item {{ $request->segment(2) == '129report' ? 'active' : '' }}" href="{{ action('ReportCustomizedController@getProductReportCustomized') }}">@lang('lang_v1.129_reports')</a>  
            </div>
        </div>
    </li>
    @endif @endif @if ($catalogue_qr)
    @if (auth()->user()->can('catalogue.access'))
    <li class="nav-item {{ in_array($request->segment(1), ['backup']) ? 'active active-sub' : '' }}">
        <a class="nav-link" href="{{ action('\Modules\ProductCatalogue\Http\Controllers\ProductCatalogueController@generateQr') }}">
            <i class="fa fa-qrcode"></i>
            <span>@lang('lang_v1.catalogue_qr')</span></a>
    </li>
    @endif @endif @if ($backup_module) @can('backup')
    <li class="nav-item {{ in_array($request->segment(1), ['backup']) ? 'active active-sub' : '' }}">
        <a class="nav-link" href="{{ action('BackUpController@index') }}">
            <i class="fa fa-cloud-download"></i>
            <span>@lang('lang_v1.backup')</span></a>
    </li>
    @endcan
    @endif
    <!-- Call restaurant module if defined -->
    @if ($enable_booking)
    <!-- check if module in subscription -->
    @if (auth()->user()->can('crud_all_bookings') ||
    auth()->user()->can('crud_own_bookings'))
    <li class="nav-item {{ $request->segment(1) == 'bookings' ? 'active active-sub' : '' }}">
        <a class="nav-link" href="{{ action('Restaurant\BookingController@index') }}">
            <i class="fa fa-calendar-check-o"></i>
            <span>@lang('restaurant.bookings')</span></a>
    </li>
    @endif @endif @if ($kitchen)

    <li class="nav-item {{ $request->segment(1) == 'modules' && $request->segment(2) == 'kitchen' ? 'active active-sub' : '' }}">
        <a class="nav-link" href="{{ action('Restaurant\KitchenController@index') }}">
            <i class="fa fa-coffee"></i>
            <span>@lang('restaurant.kitchen')</span></a>
    </li>

    @endif @if ($orders)
    <li class="nav-item {{ $request->segment(1) == 'modules' && $request->segment(2) == 'orders' ? 'active active-sub' : '' }}">
        <a class="nav-link" href="{{ action('Restaurant\OrderController@index') }}">
            <i class="fa fa-clone"></i>
            <span>@lang('restaurant.orders')</span></a>
    </li>

    @endif @if ($notification_template_module)
    @can('send_notifications')


    <li class="nav-item {{ $request->segment(1) == 'notification-template' ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#notification-template" aria-expanded="true" aria-controls="notification-template">
            <i class="fa fa-envelope"></i>
            <span>@lang('lang_v1.notification_templates')</span>
        </a>
        <div id="notification-template" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                    @lang('lang_v1.notification_templates'):
                </h6>
                <a class="collapse-item {{ $request->segment(1) == 'notification-template' && $request->segment(2) == 'email' ? 'active' : '' }}" href="{{ url('notification-templates') }}?type=email">@lang('lang_v1.email')</a>
                <a class="collapse-item {{ $request->segment(1) == 'notification-template' && $request->segment(2) == 'sms' ? 'active' : '' }}" href="{{ url('notification-templates') }}?type=sms">@lang('lang_v1.sms')
                    &
                    @lang('lang_v1.whatsapp')</a>
                
                <a class="collapse-item {{ $request->segment(1) == 'notification-template' && $request->segment(2) == 'sms' ? 'active' : '' }}" href="{{ url('notification-templates') }}?type=sms&category=purchase">@lang('lang_v1.purchase_sms')
                </a>
                    
                <a class="collapse-item {{ $request->segment(1) == 'notification-template' && $request->segment(2) == 'sms' ? 'active' : '' }}" href="{{ url('notification-templates') }}?type=sms&category=expense">@lang('lang_v1.expense_sms')
                </a>
                    
                <a class="collapse-item {{ $request->segment(1) == 'notification-template' && $request->segment(2) == 'sms' ? 'active' : '' }}" href="{{ url('notification-templates') }}?type=sms&category=accounting">@lang('lang_v1.accounting_sms')
                </a>

            </div>
        </div>
    </li>
 @endif @endif
 
    
    @if($ns_discount_module || $discount_module)
    <!-- BEGIN: Discount module -->
    <li class="nav-item {{ $request->segment(1) == 'discount-template' ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#discount-template" aria-expanded="true" aria-controls="discount-template">
            <i class="fa fa-percent"></i>
            <span>@lang('lang_v1.discount_templates')</span>
        </a>
        <div id="discount-template" class="collapse {{ $request->segment(1) == 'discount-template' ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                    @lang('lang_v1.discount_templates')
                </h6>
                <a class="collapse-item" href="{{ url('discount-templates') }}">@lang('lang_v1.discount_levels')</a>
                <a class="collapse-item" href="{{ url('list-discounts') }}">@lang('lang_v1.list_discounts')</a>
            </div>
        </div>
    </li>
    <!-- END: DISCOUNT Module -->
    @endif
    
    @php $business_or_entity = App\System::getProperty('business_or_entity'); @endphp
    @if (!$disable_all_other_module_vr)
    @if (!auth()->user()->is_pump_operator)

    <li class="nav-item @if (in_array($request->segment(1), [
                                                                                                                            'pay-online',
                                                                                                                            'stores',
                                                                                                                            'business',
                                                                                                                            'tax-rates',
                                                                                                                            'barcodes',
                                                                                                                            'invoice-schemes',
                                                                                                                            'business-location',
                                                                                                                            'invoice-layouts',
                                                                                                                            'printers',
                                                                                                                            'subscription',
                                                                                                                            'types-of-service',
                                                                                                                        ]) || in_array($request->segment(2), ['tables', 'modifiers'])) {{ 'active active-sub' }} @endif">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#settings-menu" aria-expanded="true" aria-controls="settings-menu">
            <i class="fa fa-cogs"></i>
            <span>@lang('business.settings')</span>
        </a>
        <div id="settings-menu" class="collapse @if (in_array($request->segment(1), [
                                                                                                                            'pay-online',
                                                                                                                            'stores',
                                                                                                                            'business',
                                                                                                                            'tax-rates',
                                                                                                                            'barcodes',
                                                                                                                            'invoice-schemes',
                                                                                                                            'business-location',
                                                                                                                            'invoice-layouts',
                                                                                                                            'printers',
                                                                                                                            'subscription',
                                                                                                                            'types-of-service',
                                                                                                                        ]) || in_array($request->segment(2), ['tables', 'modifiers'])) {{ 'show' }} @endif" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                    @lang('business.settings'):
                </h6>
                @if ($settings_module)
                @can('business_settings.access')
                @if ($business_settings)
                @php \Log::error(json_encode($business_settings)); @endphp
                <a class="collapse-item {{ $request->segment(1) == 'business' ? 'active' : '' }}" href="{{ action('BusinessController@getBusinessSettings') }}">
                    @if ($business_or_entity == 'business')
                    {{ __('business.business_settings') }}
                    @elseif($business_or_entity == 'entity')
                    {{ __('lang_v1.entity_settings') }}
                    @else
                    {{ __('business.business_settings') }}
                    @endif
                </a>
                @endif
                @if ($business_location)
                <a class="collapse-item {{ $request->segment(1) == 'business-location' ? 'active' : '' }}" href="{{ action('BusinessLocationController@index') }}">
                    @if ($business_or_entity == 'business')
                    {{ __('business.business_locations') }}
                    @elseif($business_or_entity == 'entity')
                    {{ __('lang_v1.entity_locations') }}
                    @else
                    {{ __('business.business_locations') }}
                    @endif
                </a>
                @endif
                <a class="collapse-item {{ $request->segment(1) == 'stores' ? 'active' : '' }}" href="{{ action('StoreController@index') }}">@lang('business.stores_settings')</a>
                <a class="collapse-item {{ $request->segment(1) == 'stores' ? 'active' : '' }}" href="{{ action('StoreController@fetchUserStorePermissions') }}">@lang('store.store_permissions')</a>
                @endcan
                @can('invoice_settings.access')
                @if ($invoice_settings)
                <a class="collapse-item @if (in_array($request->segment(1), ['invoice-schemes', 'invoice-layouts'])) {{ 'active' }} @endif" href="{{ action('InvoiceSchemeController@index') }}">@lang('invoice.invoice_settings')</a>
                @endif
                @endcan
                @can('barcode_settings.access')
                <a class="collapse-item {{ $request->segment(1) == 'barcodes' ? 'active' : '' }}" href="{{ action('BarcodeController@index') }}">@lang('barcode.barcode_settings')</a>
                @endcan
                <a class="collapse-item {{ $request->segment(1) == 'printers' ? 'active' : '' }}" href="{{ action('PrinterController@index') }}">@lang('printer.receipt_printers')</a>
                @if (auth()->user()->can('tax_rate.view') ||
                auth()->user()->can('tax_rate.create'))
                @if ($tax_rates)
                <a class="collapse-item {{ $request->segment(1) == 'tax-rates' ? 'active' : '' }}" href="{{ action('TaxRateController@index') }}">@lang('tax_rate.tax_rates')</a>
                @endif
                @endif
                @if ($customer_settings)
                @if (auth()->user()->can('customer_settings.access'))
                <a class="collapse-item {{ $request->segment(1) == 'customer-settings' ? 'active' : '' }}" href="{{ action('CustomerSettingsController@index') }}">@lang('lang_v1.customer_settings')></a>
                @endif
                @can('business_settings.access')
                <a class="collapse-item {{ $request->segment(1) == 'modules' && $request->segment(2) == 'tables' ? 'active' : '' }}" href="{{ action('Restaurant\TableController@index') }}">@lang('restaurant.tables')</a>
                @endcan
                @if ($expenses)
                @if (auth()->user()->can('product.view') ||
                auth()->user()->can('product.create'))
                <a class="collapse-item {{ $request->segment(1) == 'modules' && $request->segment(2) == 'modifiers' ? 'active' : '' }}" href="{{ action('Restaurant\ModifierSetsController@index') }}">@lang('restaurant.modifiers')</a>
                @endif
                @endif
                @endif
                @if (!$property_module)
                <a class="collapse-item {{ $request->segment(1) == 'types-of-service' ? 'active active-sub' : '' }}" href="{{ action('TypesOfServiceController@index') }}">@lang('lang_v1.types_of_service')</a>
                @endif
                @endif
                @if (Module::has('Superadmin'))
                @endif
                <a class="collapse-item {{ $request->segment(1) == 'opt-verification' && $request->segment(2) == 'index' ? 'active active-sub' : '' }}" href="{{ action('\App\Http\Controllers\UsersOPTController@index') }}">@lang('superadmin::lang.OTP_verification')</a>

                <a class="collapse-item {{ $request->segment(1) == 'pay-online' && $request->segment(2) == 'create' ? 'active active-sub' : '' }}" href="{{ action('\Modules\Superadmin\Http\Controllers\PayOnlineController@create') }}">@lang('superadmin::lang.pay_online')</a>

                <a class="collapse-item {{ $request->segment(1) == 'reports' ? 'active active-sub' : '' }}" href="{{ action('ReportConfigurationsController@index') }}">@lang('reports_configurations.reports_configurations')</a>
                
                <a class="collapse-item {{ $request->segment(1) == 'user-locations' ? 'active active-sub' : '' }}" href="{{ route('userlocations.index') }}">@lang('superadmin::lang.user_locations_sidebar')</a>
                
            </div>
        </div>
    </li>

    @endif @endif 
    
    @if ($enable_sms && $list_sms)
        @can('sms.access')
            @includeIf('sms::layouts_v2.partials.sidebar')
        @endcan 
    @endif
    
    @if ($enable_sms && $smsmodule_module)
        @includeIf('sms::layouts_v2.partials.smsmodule_sidebar')
    @endif

    @if ($member_registration)
    @can('member.access')
    @includeIf('member::layouts_v2.partials.sidebar')
    @endcan @endif


    @if (auth()->user()->hasRole('Super Manager#1'))

    <li class="nav-item {{ in_array($request->segment(1), ['super-manager']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#visitors-menusup" aria-expanded="true" aria-controls="visitors-menusup">
            <i class="fa fa-group"></i>
            <span>@lang('lang_v1.super_manager')</span>
        </a>
        <div id="visitors-menusup" class="collapse {{ in_array($request->segment(1), ['super-manager']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                    @lang('lang_v1.super_manager'):
                </h6>
                <a class="collapse-item {{ $request->segment(2) == 'visitors' ? 'active active-sub' : '' }}" href="{{ action('SuperManagerVisitorController@index') }}">@lang('lang_v1.all_visitor_details')</a>
            </div>
        </div>
    </li>

    @endif @if ($visitors_registration_module)
    @includeIf('visitor::layouts_v2.partials.sidebar')
    @endif @if ($user_management_module)
    @if (auth()->user()->can('user.view') ||
    auth()->user()->can('user.create') ||
    auth()->user()->can('roles.view'))
    <li class="nav-item {{ in_array($request->segment(1), ['roles', 'users', 'sales-commission-agents']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#user-menu" aria-expanded="true" aria-controls="user-menu">
            <i class="fa fa-group"></i>
            <span>@lang('user.user_management')</span>
        </a>
        <div id="user-menu" class="collapse {{ in_array($request->segment(1), ['roles', 'users', 'sales-commission-agents']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                    @lang('user.user_management'):
                </h6>
                @can('user.view')
                <a class="collapse-item {{ $request->segment(1) == 'users' ? 'active active-sub' : '' }}" href="{{ action('ManageUserController@index') }}">@lang('user.users')</a>
                @endcan
                @can('roles.view')
                <a class="collapse-item {{ $request->segment(1) == 'roles' ? 'active active-sub' : '' }}" href="{{ action('RoleController@index') }}">@lang('user.roles')</a>
                @endcan
                @if ($enable_sale_cmsn_agent == 1)
                @can('user.create')
            
                <a class="collapse-item {{ $request->segment(1) == 'users' ? 'active active-sub' : '' }}" href="{{ action('ManageUserController@list') }}">@lang('user.list')</a>

                <a class="collapse-item {{ $request->segment(1) == 'sales-commission-agents' ? 'active active-sub' : '' }}" href="{{ action('SalesCommissionAgentController@index') }}">
                    @lang('lang_v1.sales_commission_agents')</a>
                @endcan
                @endif
            </div>
        </div>
    </li>

    @endif
    @endif
    <!-- call Project module if defined -->
    @if (Module::has('Project'))
    @includeIf('project::layouts.partials.sidebar')
    @endif
    <!-- call Essentials module if defined -->
    @if (Module::has('Essentials'))
    @if ($hr_module)
    @includeIf('essentials::layouts.partials.sidebar_hrm')
    @endif

    @if ($essentials_module)
    @includeIf('essentials::layouts.partials.sidebar')
    @endif
    @endif


    @if (Module::has('Woocommerce'))
    @includeIf('woocommerce::layouts.partials.sidebar')
    @endif
    <!-- only customer accessable pages -->
    @if (auth()->user()->is_customer == 1)

    <li class="nav-item {{ in_array($request->segment(1), ['customer-sales', 'customer-sell-return', 'customer-order', 'customer-order-list']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#customer-menu" aria-expanded="true" aria-controls="customer-menu">
            <i class="fa fa-folder-open"></i>
            <span>@lang('sale.sale')</span>
        </a>
        <div id="customer-menu" class="collapse {{ in_array($request->segment(1), ['customer-sales', 'customer-sell-return', 'customer-order', 'customer-order-list']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                    @lang('sale.sale'):
                </h6>

                <a class="collapse-item {{ $request->segment(1) == 'customer-sales' ? 'active' : '' }}" href="{{ action('CustomerSellController@index') }}">@lang('lang_v1.all_sales')</a>

                <a class="collapse-item {{ $request->segment(1) == 'customer-sell-return' ? 'active' : '' }}" href="{{ action('CustomerSellReturnController@index') }}">@lang('lang_v1.list_sell_return')</a>

                <a class="collapse-item {{ $request->segment(1) == 'customer-order' ? 'active' : '' }}" href="{{ action('CustomerOrderController@create') }}">@lang('lang_v1.order')</a>

                <a class="collapse-item {{ $request->segment(1) == 'customer-order-list' ? 'active' : '' }}" href="{{ action('CustomerOrderController@getOrders') }}">@lang('lang_v1.list_order')</a>

            </div>
        </div>
    </li>
    @endif
    <!-- end only customer accessable pages -->
    @if ($enable_cheque_writing == 1)
    @if (auth()->user()->can('enable_cheque_writing'))

    <li class="nav-item {{ in_array($request->segment(1), ['cheque-templates', 'cheque-write', 'stamps', 'cheque-numbers', 'payees', 'deleted_cheque_details', 'printed_cheque_details', 'default_setting', 'cheque-dashboard']) ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#cheque-menu" aria-expanded="true" aria-controls="cheque-menu">
            <i class="fa fa-folder-open"></i>
            <span>@lang('cheque.cheque_writing_module')</span>
        </a>
        <div id="cheque-menu" class="collapse {{ in_array($request->segment(1), ['cheque-templates', 'cheque-write', 'stamps', 'cheque-numbers', 'payees', 'deleted_cheque_details', 'printed_cheque_details', 'default_setting', 'cheque-dashboard']) ? 'show' : '' }}" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                    @lang('cheque.cheque_writing_module'):
                </h6>
                
                @if($cheque_dashboard)
                <a class="collapse-item {{ $request->segment(1) == 'cheque-dashboard' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ url('chequerDashboard') }}">Chequer
                    Dashboard</a>
                @endif

                @if ($cheque_templates)
                <a class="collapse-item {{ $request->segment(1) == 'cheque-templates' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ action('Chequer\ChequeTemplateController@index') }}">@lang('cheque.templates')</a>
                @endif
                
                @if($cheque_add_template)
                <a class="collapse-item {{ $request->segment(1) == 'cheque-templates' && $request->segment(2) == 'create' ? 'active' : '' }}" href="{{ action('Chequer\ChequeTemplateController@create') }}">@lang('cheque.add_new_templates')</a>
                @endif
                
                @if ($write_cheque)
                <a class="collapse-item {{ $request->segment(1) == 'cheque-write' && $request->segment(2) == 'create' ? 'active' : '' }}" href="{{ action('Chequer\ChequeWriteController@create') }}">@lang('cheque.write_cheque')</a>
                @endif
                
                @if ($manage_stamps)
                <a class="collapse-item {{ $request->segment(1) == 'stamps' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ action('Chequer\ChequerStampController@index') }}">@lang('cheque.manage_stamps')</a>
                @endif
                
                @if ($manage_payee)
                <a class="collapse-item {{ $request->segment(1) == 'payees' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ url('payees') }}">Manage
                    Payee</a>
                @endif
                
                @if ($cheque_number_list)
                <a class="collapse-item {{ $request->segment(1) == 'cheque-numbers' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ action('Chequer\ChequeNumberController@index') }}">@lang('cheque.cheque_number_list')</a>
                <a class="collapse-item {{ $request->segment(1) == 'cheque-numbers-m-entries' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ action('Chequer\ChequeNumbersMEntryController@index') }}">@lang('cheque.cheque_number_m_entries')</a>
                @endif
                @if ($cheque_cancelled_cheques)
                <a class="collapse-item {{ $request->segment(1) == 'cancell_cheque_details' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ route('cancell_cheque_details.create') }}">@lang('cheque.cancel_cheque_menu')</a>
                <a class="collapse-item {{ $request->segment(1) == 'cancell_cheque_details' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ route('cancell_cheque_details.index') }}">@lang('cheque.list_cancel_cheque_menu')</a>
                @endif
                
                @if ($cheque_printed_cheques)
                <a class="collapse-item {{ $request->segment(1) == 'printed_cheque_details' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ url('printed_cheque_details') }}">Printed
                    Cheque
                    Details.</a>
                @endif
                
                @if ($default_setting)
                <a class="collapse-item {{ $request->segment(1) == 'default_setting' && $request->segment(2) == '' ? 'active' : '' }}" href="{{ url('default_setting') }}">Default
                    Settings</a>
                  
                @endif
            </div>
        </div>
    </li>
    @endif
    @endif

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">


</ul>
<script>
        document.addEventListener('DOMContentLoaded', (event) => {
            const filterInput = document.getElementById('sidebarFilter');
            const navItems = document.querySelectorAll('.sidebar .nav-item:not(:first-child)'); // Exclude the filter input

            // Load filter value from localStorage
            const filterValue = localStorage.getItem('sidebarFilter') || '';
            filterInput.value = filterValue;
            filterMenu(filterValue);

            // Add event listener for filter input
            filterInput.addEventListener('input', (e) => {
                const value = e.target.value.toLowerCase();
                console.log('value',value);
                localStorage.setItem('sidebarFilter', value);
                console.log('value',value);
                filterMenu(value);
            });

            function filterMenu(value) {
                navItems.forEach(item => {
                    const text = item.textContent.toLowerCase();
                    if (text.includes(value)) {
                        item.style.display = 'block';
                    } else {
                        item.style.display = 'none';
                    }
                });
            }
        });
    </script>

@endif