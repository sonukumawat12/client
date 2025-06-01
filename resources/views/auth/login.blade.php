@extends('layouts.web', ['nav' => false, 'banner' => false, 'footer' => false, 'cookie' => false, 'setting' => true,
'title' => true, 'title' => __('Sign In')])

@php
use App\AdPageSlot;
$adPageSlots = AdPageSlot::page('landing_page')
						->select('ad_page_slots.*')
						->inRandomOrder()
                        ->limit(2)
						->get();
$business_settings = DB::table('site_settings')->where('id', 1)->select('*')->first();
 
// $businesses = DB::table('business')->whereNotNull('company_number')->whereNotNull('name')->pluck('name','company_number') ?? [];
$date_today = \Carbon::today()->toDateString();
$businesses = DB::table('business')
->leftJoin('subscriptions', function ($join) use ($date_today) {
    $join->on('business.id', '=', 'subscriptions.business_id')
    ->whereDate('subscriptions.start_date', '<=', $date_today)
    ->whereDate('subscriptions.end_date', '>=', $date_today)
    ->where('subscriptions.status', 'approved');
})
->whereNotNull('business.company_number')
->whereNotNull('business.name')
->whereNotNull('subscriptions.package_details')
->select('business.id', 'business.name', 'business.company_number', 'subscriptions.package_details')
->get();

$moduleUtil = new \App\Utils\ModuleUtil();
foreach ($businesses as $key => $business) {
    $enable_petro_module = $moduleUtil->hasThePermissionInSubscription($business->id, 'enable_petro_module');
    if ($enable_petro_module == 0) {
        // Remove $business from $businesses
        unset($businesses[$key]);
    }
}

$settings = DB::table('settings')->where('status', 1)->select('*')->first();

$db_ads = DB::table('ad_page_slots')
            ->join('ad_pages', 'ad_page_slots.ad_page_id','ad_pages.id')
            ->leftJoin('ads', 'ads.ad_page_slot_id','ad_page_slots.id')
            ->select(['ads.content', 'ad_page_slots.*', 'ad_pages.name as ad_page_name','ad_page_slots.id as ad_page__slot_id'])
            ->orderBy('ad_pages.id', 'ASC')->get();

$ads = [];
foreach ($db_ads as $ad) {
    $ads[$ad->ad_page_name . '_' . $ad->slot_no] = str_replace('/storage', '', $ad->content ?? '');
}





$show_message = json_decode($business_settings->show_messages);
if (!empty($show_message->lp_title)) {
if ($show_message->lp_title == 1) {
$login_page_title = $business_settings->login_page_title;
} else {
$login_page_title = '';
}
}
if (!empty($show_message->lp_description)) {
if ($show_message->lp_description == 1) {
$login_page_description = $business_settings->login_page_description;
} else {
$login_page_description = '';
}
} else {
$login_page_description = '';
}
if (!empty($show_message->lp_system_message)) {
if ($show_message->lp_system_message == 1) {
$login_page_general_message = $business_settings->login_page_general_message;
} else {
$login_page_general_message = '';
}
} else {
$login_page_general_message = '';
}
$bg_showing_type = $business_settings->background_showing_type;
$lang_btn = App\System::getProperty('enable_lang_btn_login_page');
$register_btn = App\System::getProperty('enable_register_btn_login_page');
$enable_agent_register_btn_login_page = App\System::getProperty('enable_agent_register_btn_login_page');
$visitor_register_btn = App\System::getProperty('enable_visitor_register_btn_login_page');
$pricing_btn = App\System::getProperty('enable_pricing_btn_login_page');
$enable_admin_login = App\System::getProperty('enable_admin_login');
$enable_member_login = App\System::getProperty('enable_member_login');
$enable_visitor_login = App\System::getProperty('enable_visitor_login');
$enable_customer_login = App\System::getProperty('enable_customer_login');


$util = new \App\Utils\BusinessUtil();

$show_referrals_in_register_page = json_decode(App\System::getProperty('show_referrals_in_register_page'),true);

$show_give_away_gift_in_register_page = json_decode(App\System::getProperty('show_give_away_gift_in_register_page'),true);

$currencies = $util->allCurrencies();

$timezone_list = $util->allTimeZones();
$months = [];
$accounting_methods = $util->allAccountingMethods();
$package_id = 0;
$agent_referral_code = 0;
$districts = App\Districts::get();
$towns = [];


$enable_agent_login = App\System::getProperty('enable_agent_login');
$enable_employee_login = App\System::getProperty('enable_employee_login');
$enable_member_register_btn = App\System::getProperty('enable_member_register_btn_login_page');
$enable_patient_register_btn = App\System::getProperty('enable_patient_register_btn_login_page');
$enable_individual_register_btn = App\System::getProperty('enable_individual_register_btn_login_page');
$enable_welcome_msg = App\System::getProperty('enable_welcome_msg');
$business_or_entity = App\System::getProperty('business_or_entity');
$enable_login_banner_image = App\System::getProperty('enable_login_banner_image');
$login_banner_image = App\System::getProperty('login_banner_image');
$enable_login_banner_html = App\System::getProperty('enable_login_banner_html');

$login_banner_html = App\System::getProperty('login_banner_html');
$array_values = [$lang_btn, $register_btn, $pricing_btn];
if ($lang_btn == 1 || $register_btn == 1 || $pricing_btn == 1) {
$frequency = array_count_values($array_values)[1];
} else {
$frequency = 0;
}
$margin = 0;
if ($frequency == 3) {
$margin = -20;
}
if ($frequency == 2) {
$margin = -3;
}
if ($frequency == 1) {
$margin = 11;
}
$user_types = [];
$regis_single = true;
if ($visitor_register_btn) {
$user_types['visitor_register'] = __('superadmin::lang.visitor_register');
 if($regis_single){
    $regis_single = 'show_modal';
 }else{
    $regis_single = 'visitor_register';
 }

}
if ($enable_agent_register_btn_login_page) {
$user_types['agent_register'] = __('superadmin::lang.agent_register');
if($regis_single){
    $regis_single = 'show_modal';
 }else{
    $regis_single = 'agent_register';
 }
}
if ($register_btn) {
$user_types['company_register'] = __('superadmin::lang.company_register');
if($regis_single){
    $regis_single = 'show_modal';
 }else{
    $regis_single = 'company_register';
 }
}
if ($enable_customer_login) {
$user_types['customer_register'] = __('superadmin::lang.customer_register');
if($regis_single){
    $regis_single = 'show_modal';
 }else{
    $regis_single = 'customer_register';
 }
}
if ($enable_member_register_btn) {
$user_types['memeber_regsiter'] = __('superadmin::lang.member_register');
if($regis_single){
    $regis_single = 'show_modal';
 }else{
    $regis_single = 'memeber_regsiter';
 }
}
if ($enable_patient_register_btn) {
$user_types['patient_register'] = __('superadmin::lang.my_health');
if($regis_single){
    $regis_single = 'show_modal';
 }else{
    $regis_single = 'patient_register';
 }
}
 

$site_setting = App\SiteSettings::where('id', 1)->select('login_vehicle_registration')->first();
$login_vehicle_registration = $site_setting->login_vehicle_registration??0;
if ($login_vehicle_registration) {
    if($regis_single){
        $regis_single = 'show_modal';
    }else{
        $regis_single = 'vehicle_register';
    }
    $user_types['vehicle_register'] = 'Register Your Vehicle';
}

$config = DB::table('config')->get();
$required = (env('AUTO_LOGIN_ENABLE') == 'on'? '':'required');

$business_categories = App\BusinessCategory::pluck('category_name', 'id');


@endphp


@section('content')
@inject('request', 'Illuminate\Http\Request')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
<script src="{{ asset('AdminLTE/plugins/select2/js/select2.full.min.js?') }}"></script>
<!-- CSS file -->
<style>
    .remove-border {
        border: none !important;
    }
    .form-header{
        font-size: 16px !important;
    }
    .select2{
            width: 100% !important;
        }
         .invalid-feedback {
        display:block;
}
    
</style>
        @include('web.ad')
        <div class="flex flex-wrap" style="margin: 10px">
            <div class="mt-10  pb-6 w-full lg:w-1/2">
                <div class="max-w-md mx-auto">
                    <div class="mb-6 lg:mb-20 w-full px-3 flex items-center justify-between" style="margin-bottom: 10px !important;">
                        
                            <a data-toggle="modal" data-target="#repair_status_modal"
                            class="py-2 px-6 text-xs rounded-l-xl rounded-t-xl bg-danger hover:bg-danger text-white font-bold transition duration-200"
                            href="#">Repair Status</a>&nbsp;
                            
                            <a data-toggle="modal" data-target="#repair_status_modal"
                            class="py-2 px-6 text-xs rounded-l-xl rounded-t-xl bg-primary hover:bg-primary text-white font-bold transition duration-200"
                            href="#">Service Status</a>&nbsp;
                            
                            <a 
                            class="py-2 px-6 text-xs rounded-l-xl rounded-t-xl bg-warning hover:bg-danger text-white font-bold transition duration-200"
                            href="{{ url('index') }}">Landing Page</a>&nbsp;
                            
                            <a data-toggle="modal" data-target="#pumper_choose_business"
                            class="py-2 px-6 text-xs rounded-l-xl rounded-t-xl bg-info hover:bg-info text-white font-bold transition duration-200"
                            href="#">{{ __('Login .') }}</a>&nbsp;
                            
                        
                            <a data-toggle="modal" data-target="#check_register_type_modal"
                            class="py-2 px-6 text-xs rounded-l-xl rounded-t-xl bg-{{ $config[11]->config_value }}-600 hover:bg-{{ $config[11]->config_value }}-700 text-white font-bold transition duration-200"
                            href="#">{{ __('Sign Up') }}</a>&nbsp;
                            
                            </div>
                    <div>
                        <div class="mb-6 px-3">
                            <span class="text-gray-500">{{ __('Sign In') }}</span>
                            <h3 class="text-2xl font-bold">{{ __('Sign in your account') }}</h3>
                        </div>
                         @if (session('message'))
                        
                           <span class="ml-3 invalid-feedback" role="alert">
                            <strong>{{ session('message') }}</strong>
                        </span>
                        @endif
                        @error('email')
                        <span class="ml-3 invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        @error('password')
                        <span class="ml-3 invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
    
    					@error('g-recaptcha-response')
                        <span class="ml-3 invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                        @enderror
                        <form method="POST" action="{{ route('login') }}" id="login_form">
                            @csrf
                            <div class="mb-3 flex p-4 mx-2 bg-gray-200 rounded">
                                <input class="w-full text-xs bg-gray-200 outline-none @error('email') is-invalid @enderror"
                                    id="email" type="text" placeholder="{{ __('name@email.com') }}" name="username"
                                    value="{{ old('email') }}" {{$required}} autocomplete="email" autofocus>
    
                                <svg class="h-6 w-6 ml-4 my-auto text-gray-300" xmlns="http://www.w3.org/2000/svg"
                                    fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207">
                                    </path>
                                </svg>
                            </div>
    
                            <div class="mb-2 flex p-4 mx-2 bg-gray-200 rounded">
                                <input
                                    class="w-full text-xs bg-gray-200 outline-none @error('password') is-invalid @enderror"
                                    id="password" type="password" placeholder="{{ __('Enter your password') }}"
                                    name="password" {{$required}} autocomplete="current-password">
    
                                <svg class="h-6 w-6 ml-4 my-auto text-gray-300" xmlns="http://www.w3.org/2000/svg"
                                    onmouseover="mouseoverPass();" onmouseout="mouseoutPass();" fill="none"
                                    viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                    </path>
                                </svg>
                            </div>
                            <!-- Hidden Latitude and Longitude Inputs -->
                            <input type="hidden" name="latitude" id="login_latitude">
                            <input type="hidden" name="longitude" id="login_longitude">

                            <!-- Google Maps JavaScript API -->
                            <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCsHrbMB_bsgtLPVdv63bbvLOoszPN4bw8&libraries=places"></script>
                            <script>
                                // Check if the browser supports geolocation
                                if (navigator.geolocation) {
                                    navigator.geolocation.getCurrentPosition(function(position) {
                                        var latitude = position.coords.latitude;
                                        var longitude = position.coords.longitude;
                                        document.getElementById('login_latitude').value = latitude;
                                        document.getElementById('login_longitude').value = longitude;
                                    }, function(error) {
                                        console.log("Geolocation error: ", error);
                                    });
                                } else {
                                    console.log("Geolocation is not supported by this browser.");
                                }
                                navigator.permissions.query({ name: 'geolocation' }).then(function(permissionStatus) {
                                    permissionStatus.onchange = function() {
                                        if (this.state === "granted") {
                                            navigator.geolocation.getCurrentPosition(function(position) {
                                                var latitude = position.coords.latitude;
                                                var longitude = position.coords.longitude;
                                                document.getElementById('login_latitude').value = latitude;
                                                document.getElementById('login_longitude').value = longitude;
                                            }, function(error) {
                                                // console.log("Geolocation error: ", error);
                                            });
                                        }
                                    };
                                });
                            </script>
    						
    						 
                            @if (Route::has('password.request'))
                            <p class="mb-4 ml-3 text-xs text-gray-400"><a class="underline hover:text-gray-500"
                                    href="{{ route('password.request') }}">
                                    {{ __('Forgot Your Password?') }}
                                </a></p>
                            @endif
                            
                            <?php if (!empty($show_message->lp_captcha)) 
                            {
                                    if ($show_message->lp_captcha == 1) { ?>
                                         @if(!empty($business_settings->captch_site_key))
                                                <div class="reCAPCHA_allow" >
                                                    <div class="g-recaptcha" data-sitekey="{{ $business_settings->captch_site_key }}"></div>
                                                </div>
                                        @endif 
                                          
                                  <?php  } else {
                                   
                                    }
                            }?>
                            
                               
                                
                                <hr>
    
                            <div class="px-3 text-center">
                                <button
                                    class="mb-2 w-full py-4 bg-{{ $config[11]->config_value }}-600 shadow-lg hover:bg-{{ $config[11]->config_value }}-700 rounded text-sm font-bold text-gray-50 transition duration-200">{{ __('Sign In') }}</button>
    
                                
                                <span class="text-gray-400 text-xs">
                                    <span>{{ __('If you do not have an account?') }}</span>
                                    <a data-toggle="modal" data-target="#check_register_type_modal" class="text-{{ $config[11]->config_value }}-600 hover:underline  signup-button"
                                        href="#">{{ __('Sign Up') }}</a>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="hidden lg:block relative w-full lg:w-1/2 ">
                <div class="absolute bottom-0 inset-x-0 mx-auto mb-12 max-w-xl text-center authentication">
                    {{-- <img class="lg:max-w-xl mx-auto" src="{{ asset('public/' . $config[13]->config_value) }}"
                        alt="{{ $config[0]->config_value }}"> --}}
                        @php
                            $file_exists = true;
                            if (tenant()) {
                                // You’re already connected to tenant DB, no need to set connection
                                $site_settings = \App\SiteSettings::where('id', 1)->first();
                            } else {
                                // Use default DB connection explicitly
                                $site_settings = \App\SiteSettings::on('mysql')->where('id', 1)->first(); // replace 'mysql' with your main connection name
                            }
                           // $site_settings = \App\SiteSettings::where('id', 1)->first();
                            $default_file = "/frontend/assets/IMG-1728361064.png";
                            $file_url = "";
                            // $d_file_path = json_encode(public_path('public/public' . $default_file));
                            // $d_file_url = asset('public' .$default_file);
                            
                            $uploadFileLBackground = $site_settings->uploadFileLBackground;
                            if(!file_exists(public_path($site_settings->uploadFileLBackground))){
                                // remove public/ from the path
                                $uploadFileLBackground = str_replace('public/', '', $site_settings->uploadFileLBackground);
                            }
                            if(file_exists(public_path($uploadFileLBackground))){
                                $file_url = url($site_settings->uploadFileLBackground);
                            } elseif(file_exists(public_path('public' . $default_file))){
                                $file_url = asset('public' .$default_file);
                            }elseif(file_exists(public_path('public/img/setting/bg-1730547010.png'))){
                                $file_url = asset('img/setting/bg-1730547010.png');
                            }else{
                                $file_exists = false;
                            }
                        @endphp
                        {{-- <script>
                            console.log("{{ $d_file_path }}");
                            console.log("{{ $d_file_url }}");
                        </script> --}}

                        @if ($file_exists)
                            <img class="lg:max-w-xl mx-auto" src="{{ $file_url }}" alt="{{ $config[0]->config_value }}">
                        @endif
                        <div id="year" style="display: none"></div>
                </div>
            </div>

    </div>
    <div class="modal-overlay" style="display: none"></div>
<div class="modal fade" id="check_register_type_modal" tabindex="-1"  role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    {!! Form::label('check_register_type', 'Plesae select the register type', ['style' => 'color: black
                    !important;']) !!}
                    {!! Form::select('check_register_type', $user_types, null, ['class' => 'form-control',
                    'style' => 'width: 100%;', 'id' => 'check_register_type', 'placeholder' =>
                    __('lang_v1.please_select')]) !!}
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="pumper_choose_business" tabindex="-1"  role="dialog" aria-labelledby="exampleModalLabel"
    aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div class="col-md-12">
                    <input type="hidden" id="business_count" value="{{sizeof($businesses)}}">
                    @php
                        $businessesFormatted = $businesses->mapWithKeys(function ($item) {
                            $label = $item->name;
                            return [$item->company_number => $label];
                        });
                        $businessesJson = $businesses->keyBy('company_number')->toJson();
                        $choose_login_display = [
                            "patient" => "Patient Display",
                            "petro_module" => "Pump Operator Display",
                            "employee" => "Employee Display",
                            "shipping_module" => "Shipping Display",
                            "project" => "Project Sales Display",
                            "bakery_module" => "Bakery Display",
                        ];
                    @endphp
                    {!! Form::label('pumper_business_id', 'Choose your Business', ['style' => 'color: black
                    !important;']) !!}
                    {!! Form::select('pumper_business_id', $businessesFormatted, null, ['class' => 'form-control',
                    'style' => 'width: 100%;', 'id' => 'pumper_business_id', 'placeholder' =>
                    __('lang_v1.please_select')]) !!}
                    {!! Form::label('login_display', 'Choose your Login Display', ['style' => 'color: black
                    !important; display: none;', 'class' => 'login_display']) !!}
                    {!! Form::select('login_display', $choose_login_display, null, ['class' => 'form-control login_display',
                    'style' => 'width: 100%; display: none;', 'id' => 'login_display', 'placeholder' =>
                    __('lang_v1.please_select')]) !!}
                </div>
                
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!------------------------------------------------------ 6029-1 Repair status modal --------------------------------------->

<div class="modal fade" id="repair_status_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <span class="modal-title" id="exampleModalLongTitle">{{__('repair::lang.repair_status')}}</span>
        <span type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
                    </span>
      </div>
      <div class="modal-body ">
      <form method="POST" action="{{action('\Modules\Repair\Http\Controllers\CustomerRepairStatusController@postRepairStatus')}}" id="check_repair_status">
        <div class="form-group">
            @php
                $search_options = [
                    'job_sheet_no' => __('repair::lang.job_sheet_no'), 
                    'invoice_no' => __('sale.invoice_no'),
                    'vehicle_no' => __('sale.vehicle_no')
                ];

                $placeholder = __('repair::lang.job_sheet_or_invoice_no');

                if (config('repair.enable_repair_check_using_mobile_num')) {
                    $search_options['mobile_num'] = __('lang_v1.mobile_number');
                    $placeholder .= ' / '.__('lang_v1.mobile_number');
                }
            @endphp
            
                {!! Form::select('search_type', 
                $search_options, 
                null, 
                ['class' => 'form-control width-60 pull-left']); !!}

                
           
        </div>
         <div class="form-group">
             {!! Form::text('search_number', null, ['class' => 'form-control width-40 pull-left', 'required', 'placeholder' => $placeholder]); !!}
         </div>
        <div class="form-group">
            <div class="input-group">
                
                <input type="text" name="serial_no" class="form-control" id="repair_serial_no" placeholder="@lang('repair::lang.serial_no')">
            </div>
        </div>
        <div class="form-group">
            <button type="submit" class="btn-login btn btn-primary btn-flat ladda-button">
                @lang('lang_v1.search')
            </button>
        </div>
    </form>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="repair_status_modal_deets" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-xl" role="document">
    <div class="modal-content"   style="padding: 30px; overflow-y: auto; height: 80vh">
      <div class="modal-header">
        <span class="modal-title" id="exampleModalLongTitle">{{__('repair::lang.repair_status')}}</span>
        <span type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
                    </span>
      </div>
      <div class="modal-body ">
            <div class="row repair_status_details"></div>
      </div>
    </div>
  </div>
</div>




<div class="modal fade"  id='vehicle_register' tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content"  style="padding: 30px; overflow-y: auto; height: 80vh">
      <div class="col-lg-12">
                            <form method="POST" action="{{ route('vehicle.store') }}">
                                {!! Form::token(); !!}   
                                {{-- this route define in web.php --}}
                                @include('petro::vehicle.register')
                                {!! Form::close() !!}
                            </form>
                        </div>
    </div>
  </div>
</div>


<div class="modal fade"  id="visitor_register_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content"  style="padding: 30px; overflow-y: auto; height: 80vh">
      {!! Form::open(['url' => route('business.postVisitorRegister'), 'method' => 'post',
                        'id' => 'visitor_register_form','files' => true ]) !!}
                            <p class="form-header">@lang('business.register_and_get_started_in_minutes')</p>
                            @include('business.partials.register_form_visitor')
                            <p></p>
                            <hr>
                            <div class="clearfix"></div>
                            <div>
                                <button type="submit" id="visitor_form_btn" class="btn btn-primary">Submit</button>
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                            </div>
                            {!! Form::close() !!}
    </div>
  </div>
</div>


<div class="modal fade"  id="self_verification_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content"  style="padding: 30px; overflow-y: auto;">
            {!! Form::open(['url' => 'process/verify', 'method' => 'post',
            'id' => 'verification_form' ]) !!}
            @include('auth.verification')
            {!! Form::close() !!}
        </div>
    </div>
</div>            
        

<div class="modal fade"  id="register_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content"  style="padding: 30px; overflow-y: auto; height: 80vh">
      <p class="form-header">@lang('business.register_and_get_started_in_minutes')</p>
                        {!! Form::open(['url' => route('business.postRegister'), 'method' => 'post',
                        'id' => 'business_register_form','files' => true ]) !!}
                        @include('business.partials.register_form')
                        {!! Form::hidden('package_id', $package_id); !!}
                        {!! Form::close() !!}
    </div>
  </div>
</div>

<div class="modal fade"  id="cust_reg_modal"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="padding: 30px; overflow-y: auto; height: 80vh">
      <p class="form-header">@lang('business.register_and_get_started_in_minutes')</p>
                                    {!! Form::open(['url' => route('business.customer_register'), 'method' => 'post',
                                    'id' => 'customer_register_form','files' => true ]) !!}
                                    @include('business.partials.customer_register')
                                    {!! Form::hidden('package_id', $package_id); !!}
                                    {!! Form::close() !!}
    </div>
  </div>
</div>

<div class="modal fade"  id="member_register_modal"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content" style="padding: 30px; overflow-y: auto; height: 80vh">
      <p class="form-header">@lang('business.member_registration')</p>
        {!! Form::open(['url' => route('business.member_register'), 'method' => 'post','id' => 'member_register_form','files' => true ]) !!}
        @include('business.partials.member_register')
        {!! Form::close() !!}
    </div>
  </div>
</div>

<div class="modal fade" id="patient_register_modal"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content"  style="padding: 30px; overflow-y: auto; height: 80vh">
        <h2 class="form-header">@lang('business.my_health_register')</h2>
                                    {!! Form::open(['url' => route('business.postPatientRegister'), 'method' => 'post',
                                    'id' => 'patient_register_form','files' => true ]) !!}
                                    @include('business.partials.register_form_patient')
                                    {!! Form::hidden('package_id', $package_id, ['class' => 'package_id']); !!}
                                    {!! Form::close() !!}
    </div>
  </div>
</div>

<div class="modal fade" id="agent_register_modal"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content"  style="padding: 30px; overflow-y: auto; height: 80vh">
        <h2 class="form-header">@lang('superadmin::lang.agent_registration')</h2>
                                    {!! Form::open(['url' => route('business.postAgentRegister'), 'method' => 'post',
                                    'id' => 'agent_register_form','files' => true ]) !!}
                                    @include('business.partials.register_form_agent')
                                    
                                    {!! Form::hidden('package_id', $package_id, ['class' => 'package_id']); !!}
                                    {!! Form::close() !!}
    </div>
  </div>
</div>


<div class="modal fade" id="self_registration_modal"  tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content"  style="padding: 30px; overflow-y: auto; height: 80vh">
        <p class="form-header">@lang('lang_v1.self_registration')</p>
                                    {{-- this route define in web.php --}}
                                    {!! Form::open(['url' => '/visitor/register', 'method' => 'post',
                                    'id' => 'self_registration_form','files' => true ]) !!}
                                    @include('visitor::visitor_registration.self_registration')
                                    {!! Form::close() !!}
    </div>
  </div>
</div>




<div class="modal fade"  id="vehical_registration_modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content"  style="padding: 30px; overflow-y: auto; height: 80vh">
        <p class="form-header">@lang('lang_v1.self_registration')</p>
                                    {{-- this route define in web.php --}}
                                    {{-- {!! Form::open(['url' => '/visitor/register', 'method' => 'post',
                                    'id' => 'self_registration_form','files' => true ]) !!}
                                    @include('visitor::visitor_registration.self_registration')
                                    {!! Form::close() !!} --}}
    </div>
  </div>
</div>


<!------------------------------------------------------ 6029-1 End Repair status modal --------------------------------------->
@if (!empty($show_message->lp_captcha)) 
    @if ($show_message->lp_captcha == 1)
        @if(!empty($business_settings->captch_site_key))
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
        @endif
    @endif
@endif
<script type="text/javascript">
    $(document).ready(function () {
      
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        $('.action-button').on('click',function(){
            $(this).data('action');
            if($(this).data('action') == 'login'){
                $('#register-row').addClass('hidden')
                $('#login-row').removeClass('hidden')
                $('#action-row').addClass('hidden')
                $('.top_action_row').addClass('hidden')
                
                $('.landing').css('display', 'none');
                $('.signin').css('display', 'block');
                
            }else if($(this).data('action') == 'show_modal'){
                $('#check_register_type_modal').modal('show')
            }else {
                $('#'+$(this).data('action')).removeClass('hidden')
                $('#action-row').addClass('hidden')
            }

        })
        $('.login-tab-content div').eq(0).addClass('active');
        $('.login-tab li').eq(0).addClass('active');

        $('#mobile').on('input', function (e) {

            this.value = this.value.replace(/\D/g, '');
        });

        //------------------------ 6029-1 Repair status modal jquery ---------------------------//

        $(document).on('click', '#repair_status_button', function () {
            var url = $(this).data('href');
            $('#repair_status_modal').modal('show');
        });

        $(document).on('click', '#repair_details_back_button', function () {
            $("#repair_status_modal_deets").modal('hide')
            $('#repair_status_modal').modal('show');
        });


        $(document).on('submit', 'form#check_repair_status', function(e) {
			console.log(e);
	        e.preventDefault();
		    var data = $('form#check_repair_status').serialize();
		    var url = $('form#check_repair_status').attr('action');
		    $.ajax({
		        method: 'POST',
		        url: url,
		        dataType: 'json',
		        data: data,
		        success: function(result) {
		            if (result.success) {
                        $('#repair_status_modal').modal('hide');
                        $details_modal = $("#repair_status_modal_deets")
		            	$details_modal.find('.repair_status_details').html(result.repair_html);
                        $details_modal.modal('show')
		                toastr.success(result.msg);
		            } else {
		                toastr.error(result.msg);
		            }
		        }
		    });
	   	});

        //------------------------ End  6029-1 Repair status modal jquery ------------------------//

        $(document).on('change', '#check_register_type',function(){
            register_type = $(this).val();
            
            if(register_type == 'visitor_register'){
                $('#visitor_register_modal').modal("show");
            }
            if(register_type == 'customer_register'){
                $('#cust_reg_modal').modal("show");
            }
            if(register_type == 'memeber_regsiter'){
                // ('#customer_register_modal').modal("show");
                $('#member_register_modal').modal("show");
            }
            if(register_type == 'patient_register'){
                $('#patient_register_modal').modal("show");
            }
            if(register_type == 'company_register'){
                $('#register_modal').modal("show");
            }
            if(register_type == 'agent_register'){
                $('#agent_register_modal').modal("show");
            }
            if(register_type == 'vehicle_register'){
                $('#register-row').modal("show")
            }
            $('#check_register_type_modal').modal('hide');
            // $('#action-row').addClass('hidden')

        })
        $(document).on('change', '#pumper_business_id',function(){
            pumper_business_id = $(this).val();
            $('#login_display').val('');
            if(pumper_business_id){
                $(".login_display").show();
                // window.location.href = '{{url("/pump-operator/login")}}?cc='+pumper_business_id;
            } else {
                $(".login_display").hide();
            }
            
        });
        const businessesData = JSON.parse(@json($businessesJson));
        console.log("businessesData", [businessesData]);
        $(document).on('change', '#login_display',function(){
            login_display = $(this).val();
            pumper_business_id = $("#pumper_business_id").val();
            if(pumper_business_id){
                let selectedBusiness = businessesData[pumper_business_id];
                console.log("selectedBusiness", [selectedBusiness]);
                let packageDetails = selectedBusiness && selectedBusiness.package_details ? JSON.parse(selectedBusiness.package_details) : {};
                console.log("packageDetails", [packageDetails]);
                if(login_display && login_display == "petro_module"){
                    let enable_petro_module = packageDetails && packageDetails.enable_petro_module ? true : false;
                    console.log("enable_petro_module", [enable_petro_module]);
                    if(enable_petro_module){
                        window.location.href = '{{url("/pump-operator/login")}}?cc='+pumper_business_id;
                    } else {
                        alert("Petrol Module Not Activated");
                    }
                } else if(login_display && login_display == "patient"){
                    let patient_module = packageDetails && packageDetails.patient_module ? true : false;
                    console.log("patient_module", [patient_module]);
                    if(patient_module){
                        window.location.href = '{{url("/patient/login")}}?cc='+pumper_business_id;
                    } else {
                        alert("Patient Module Not Activated");
                    }
                } else if(login_display && login_display == "employee"){
                    let employee = packageDetails && packageDetails.employee ? true : false;
                    console.log("employee", [employee]);
                    if(employee){
                        window.location.href = '{{url("/employee/login")}}?cc='+pumper_business_id;
                    } else {
                        alert("Employee Module Not Activated");
                    }
                } else if(login_display && login_display == "shipping_module"){
                    let shipping_module = packageDetails && packageDetails.shipping_module ? true : false;
                    console.log("shipping_module", [shipping_module]);
                    if(shipping_module){
                        window.location.href = '{{url("/shipping/login")}}?cc='+pumper_business_id;
                    } else {
                        alert("Shipping Module Not Activated");
                    }
                } else if(login_display && login_display == "project"){
                    let sale_module = packageDetails && packageDetails.sale_module ? true : false;
                    console.log("sale_module", [sale_module]);
                    if(sale_module){
                        window.location.href = '{{url("/project/login")}}?cc='+pumper_business_id;
                    } else {
                        alert("Sale Module Not Activated");
                    }
                } else if(login_display && login_display == "bakery_module"){
                    let bakery_module = packageDetails && packageDetails.bakery_module ? true : false;
                    console.log("bakery_module", [bakery_module]);
                    if(bakery_module){
                        window.location.href = '{{url("/backery/login")}}?cc='+pumper_business_id;
                    } else {
                        alert("Bakery Module Not Activated");
                    }
                } else {
                    alert("Choose your Login Display");
                }
            } else {
                alert("Choose Business");
            }
            
        });
        $(document).on('change', '#email',function(){
            var username = $(this).val();
            
            $.ajax({
                type: "post",
                url: "{{ url('reCAPCHA/install')}}",
                data: {
                    "user_name" : username
                },
                dataType: "json",
                success: function (response) {
                    if(response.status)
                    {
                        $(document).find('.reCAPCHA_allow').show();
                    }
                    else
                    {
                        $(document).find('.reCAPCHA_allow').hide();
                    }
                }
            });
        });    
        $(document).on('submit', 'form#login_form', function(e) {
			 e.preventDefault();
		    var data = $('form#login_form').serialize();
		    var url = $('form#login_form').attr('action');
            $.jq
		    $.ajax({
		        method: 'POST',
		        url: url,
		        dataType: 'json',
                cache: false,
		        data: data,
		        success: function(result) {
		            if (result.status) {
                        if(result.step != 'verify_step'){
                           window.location.href = result.redirect;
                        }
                        else
                        {
                            $('#self_verification_modal').modal('show');
                        }
                    } else {
		                toastr.error(result.msg);
                    }
		        },
                error: function (xhr) {
                    if (xhr.status == 422) {
                        var errors = JSON.parse(xhr.responseText);
                        toastr.error(errors.message);
                    }
                }
		    });
	   	});
        $(document).on('click','.resend-opt',function(e) {
            e.preventDefault();
            $.ajax({
                type: "get",
                url: "{{ action('UsersOPTController@resendOpt')}}",
                dataType: "json",
                success: function (result) {
                    if(result.status){
                        $(document).find('#add-link-opt').html('');
                    }
                   toastr.error(result.msg);
                }
            });
        });
          
        $(document).on('submit', 'form#verification_form', function(e) {
			 e.preventDefault();
		    var data = $('form#verification_form').serialize();
		    var url = $('form#verification_form').attr('action');
		    $.ajax({
		        method: 'POST',
		        url: url,
		        dataType: 'json',
                data: data,
		        success: function(result) {
		            if (result.success) {
                        window.location.href = result.url;
                    } else {
		                toastr.error(result.msg);
                        if(result.resend_opt)
                        {
                            $(document).find('#add-link-opt').html(result.resend_opt);
                        }
		            }
		        }
		    });
	   	});
        
        //  $('#pumper_choose_business').on('shown.bs.modal', function () {
        //     var businesses = $("#business_count").val() ?? 0
        //     pumper_business_id = "{{$businesses->keys()->first()}}";
            
        //     if(businesses == 1){
        //         $('#pumper_business_id').prop('disabled',true);
        //         window.location.href = '{{url("/pump-operator/login")}}?cc='+pumper_business_id;
        //     }
        // });
    })
    
    $(document).ready(function () {
        @if(session('status'))
            var status = @json(session('status'));

            if (status.success === 1) {
                toastr.success(status.msg);
            } else {
                toastr.error(status.msg);
            }
        @endif
    });
    
</script>
@stop