@can('superadmin')
<li class="nav-item {{ in_array($request->segment(1), ['superadmin', 'sample-medical-product-import', 'site-settings', 'pay-online']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#superadmin-menu"
        aria-expanded="true" aria-controls="superadmin-menu">
        <i class="ti-settings"></i>
        <span>@lang('superadmin::lang.superadmin')</span>
    </a>
    <div id="superadmin-menu" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('superadmin::lang.superadmin'):</h6>
            <a class="collapse-item {{ empty($request->segment(2)) && $request->segment(1) != 'site-settings' ? 'active active-sub' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminController@index')}}">@lang('superadmin::lang.superadmin')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'business' ? 'active active-sub' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\BusinessController@index')}}">@lang('superadmin::lang.all_business')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'tenant-management' ? 'active active-sub' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\TenantManagementController@index')}}">@lang('superadmin::lang.tenant_management')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'packages' ? 'active active-sub' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\PackagesController@index')}}">@lang('superadmin::lang.subscription_packages')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'referrals' ? 'active active-sub' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\ReferralController@index')}}">@lang('superadmin::lang.referrals')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'settings' ? 'active active-sub' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@edit')}}">@lang('superadmin::lang.super_admin_settings')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'imports-exports' ? 'active active-sub' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\ImportExportController@index')}}">@lang('superadmin::lang.import_export')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'help-explanation' ? 'active active-sub' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\HelpExplanationController@index')}}">@lang('superadmin::lang.help_explanation')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'communicator' ? 'active active-sub' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\CommunicatorController@index')}}">@lang('superadmin::lang.communicator')</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'site-settings'? 'active' : '' }}" href="{{route('site_settings.view')}}">@lang('site_settings.settings')</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'system_administration'? 'active' : '' }}" href="{{route('site_settings.help_view')}}">@lang('site_settings.help')</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'pages' ? 'active' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@pages')}}">Landing Page Content</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'landing-pages' ? 'active' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@landingSettings')}}">Enable Landing Pages</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'landing-settings' ? 'active' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@landAdminSettings')}}">Landing Page Settings</a>
            
            <a class="collapse-item {{ $request->segment(2) == 'landing-languages' ? 'active' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@landing_languages')}}">Landing Page Languages</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'sample-medical-product-import' ? 'active' : '' }}" href="{{action('ImportMedicalProductController@index')}}">@lang('lang_v1.sample_medical_product_import')</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'petro-quota-setting' ? 'active' : '' }}" href="{{action('\Modules\Petro\Http\Controllers\VehicleController@petro_qouta_setting')}}">@lang('vehicle.petro_qouta_setting')</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'smsrefill-package' ? 'active' : '' }}" href="{{action('\Modules\Superadmin\Http\Controllers\SmsRefillPackageController@index')}}">@lang('superadmin::lang.sms_refill')</a>
            
            <a class="collapse-item {{ $request->segment(1) == 'user-locations' ? 'active active-sub' : '' }}" href="{{ route('userlocations.index') }}">@lang('superadmin::lang.user_locations_sidebar')</a>
                    
        </div>
    </div>
</li>
<li class="nav-item {{ $request->segment(1) == 'default-notification-templates' ? 'active active-sub' : '' }}">
        <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#default-notification-template" aria-expanded="true" aria-controls="default-notification-template">
            <i class="fa fa-envelope"></i>
            <span>@lang('lang_v1.default_notification_templates')</span>
        </a>
        <div id="default-notification-template" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
            <div class="bg-white py-2 collapse-inner rounded">
                <h6 class="collapse-header">
                    @lang('lang_v1.default_notification_templates'):
                </h6>
                <a class="collapse-item {{ $request->segment(1) == 'notification-template' && $request->segment(2) == 'email' ? 'active' : '' }}" href="{{ url('superadmin/default-notification-templates') }}?type=email">@lang('lang_v1.email')</a>
                <a class="collapse-item {{ $request->segment(1) == 'notification-template' && $request->segment(2) == 'sms' ? 'active' : '' }}" href="{{ url('superadmin/default-notification-templates') }}?type=sms">@lang('lang_v1.sms')
                    &
                    @lang('lang_v1.whatsapp')
                </a>

            </div>
        </div>
    </li>
@endcan