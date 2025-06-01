
@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Superadmin Settings')

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang('superadmin::lang.super_admin_settings')<small>@lang('superadmin::lang.edit_super_admin_settings')</small>
    </h1>
</section>
<style>
.bg-blue {
  background: #206bc4; }

.text-blue {
  color: #206bc4 !important; }

.bg-blue-lt {
  color: #206bc4 !important;
  background: rgba(32, 107, 196, 0.1) !important; }

.bg-azure {
  background: #4299e1; }

.text-azure {
  color: #4299e1 !important; }

.bg-azure-lt {
  color: #4299e1 !important;
  background: rgba(66, 153, 225, 0.1) !important; }

.bg-indigo {
  background: #4263eb; }

.text-indigo {
  color: #4263eb !important; }

.bg-indigo-lt {
  color: #4263eb !important;
  background: rgba(66, 99, 235, 0.1) !important; }

.bg-purple {
  background: #ae3ec9; }

.text-purple {
  color: #ae3ec9 !important; }

.bg-purple-lt {
  color: #ae3ec9 !important;
  background: rgba(174, 62, 201, 0.1) !important; }

.bg-pink {
  background: #d6336c; }

.text-pink {
  color: #d6336c !important; }

.bg-pink-lt {
  color: #d6336c !important;
  background: rgba(214, 51, 108, 0.1) !important; }

.bg-red {
  background: #d63939; }

.text-red {
  color: #d63939 !important; }

.bg-red-lt {
  color: #d63939 !important;
  background: rgba(214, 57, 57, 0.1) !important; }

.bg-orange {
  background: #f76707; }

.text-orange {
  color: #f76707 !important; }

.bg-orange-lt {
  color: #f76707 !important;
  background: rgba(247, 103, 7, 0.1) !important; }

.bg-yellow {
  background: #f59f00; }

.text-yellow {
  color: #f59f00 !important; }

.bg-yellow-lt {
  color: #f59f00 !important;
  background: rgba(245, 159, 0, 0.1) !important; }

.bg-lime {
  background: #74b816; }

.text-lime {
  color: #74b816 !important; }

.bg-lime-lt {
  color: #74b816 !important;
  background: rgba(116, 184, 22, 0.1) !important; }

.bg-green {
  background: #2fb344; }

.text-green {
  color: #2fb344 !important; }

.bg-green-lt {
  color: #2fb344 !important;
  background: rgba(47, 179, 68, 0.1) !important; }

.bg-teal {
  background: #0ca678; }

.text-teal {
  color: #0ca678 !important; }

.bg-teal-lt {
  color: #0ca678 !important;
  background: rgba(12, 166, 120, 0.1) !important; }

.bg-cyan {
  background: #17a2b8; }

.text-cyan {
  color: #17a2b8 !important; }

.bg-cyan-lt {
  color: #17a2b8 !important;
  background: rgba(23, 162, 184, 0.1) !important; }

.bg-dark {
  background: #232e3c; }

.text-dark {
  color: #232e3c !important; }

.bg-dark-lt {
  color: #232e3c !important;
  background: rgba(35, 46, 60, 0.1) !important; }

.bg-muted {
  background: #656d77; }

.text-muted {
  color: #656d77 !important; }

.bg-muted-lt {
  color: #656d77 !important;
  background: rgba(101, 109, 119, 0.1) !important; }

.bg-white {
  background: #ffffff; }

.text-white {
  color: #ffffff !important; }

.bg-white-lt {
  color: #ffffff !important;
  background: rgba(255, 255, 255, 0.1) !important; }

    .col-auto {
  flex: 0 0 auto;
  width: auto; }
  .g-2,
.gx-2 {
  --tblr-gutter-x: 0.5rem; }

.g-2,
.gy-2 {
  --tblr-gutter-y: 0.5rem; }

    .wrapper {
        overflow: hidden;
    }
    .row{
       margin-top: 20px; 
    }
    
    .form-colorinput {
  position: relative;
  display: inline-block;
  margin: 0;
  line-height: 1;
  cursor: pointer; }

.form-colorinput-input {
  position: absolute;
  z-index: -1;
  opacity: 0; }

.form-colorinput-color {
  display: block;
  width: 1.5rem;
  height: 1.5rem;
  color: #ffffff;
  border: 1px solid rgba(101, 109, 119, 0.16);
  border-radius: 3px;
  box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
  .form-colorinput-color:before {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    content: "";
    background: no-repeat center center/1rem;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' width='16' height='16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 8.5l2.5 2.5l5.5 -5.5'/%3e%3c/svg%3e");
    opacity: 0;
    transition: .3s opacity; }
    .form-colorinput-input:checked ~ .form-colorinput-color:before {
      opacity: 1; }
  .form-colorinput-input:focus ~ .form-colorinput-color {
    border-color: #206bc4;
    box-shadow: 0 0 0 0.25rem rgba(32, 107, 196, 0.25); }
  .form-colorinput-light .form-colorinput-color:before {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16' width='16' height='16'%3e%3cpath fill='none' stroke='%23232e3c' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 8.5l2.5 2.5l5.5 -5.5'/%3e%3c/svg%3e"); }

</style>
<!-- Main content -->
<section class="content">
     <div class="row-deck row-cards">
                <div class="col-sm-12 col-lg-12">
                    <form action="{{ action('\Modules\Superadmin\Http\Controllers\SuperadminSettingsController@changeSettings') }}" method="post" enctype="multipart/form-data"
                        class="card">
                        @csrf
                        <div class="card-body" style="width: 100%">
                            <div class="col-xl-12">
                                    <div class="row" style="">

                                        <div class="col-md-3 ">
                                            <div class="mb-3">
                                                <label class="form-label" for="timezone">{{ __('Timezone') }}</label>
                                                <select name="timezone" id="timezone" class="form-control">
                                                    @foreach (timezone_identifiers_list() as $timezone)
                                                    <option value="{{ $timezone }}"
                                                        {{ $config[2]->config_value == $timezone ? ' selected' : '' }}>
                                                        {{ $timezone }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label" for="currency">{{ __('Currency') }}</label>
                                                <select name="currency" id="currency" class="form-control">
                                                    @foreach ($currencies as $currency)
                                                    <option value="{{ $currency->code }}"
                                                        {{ $config[1]->config_value == $currency->code ? ' selected' : '' }}>
                                                        {{ $currency->country." | ".$currency->currency }} ({{ $currency->symbol }})</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 ">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="term">{{ __('Default Plan Term') }}</label>
                                                <select name="term" id="term" class="form-control">
                                                    <option value="monthly"
                                                        {{ $config[8]->config_value == 'monthly' ? ' selected' : '' }}>
                                                        {{ __('Monthly') }}</option>
                                                    <option value="yearly"
                                                        {{ $config[8]->config_value == 'yearly' ? ' selected' : '' }}>
                                                        {{ __('Yearly') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-3 ">
                                            <div class="mb-3">
                                                <label class="form-label"
                                                    for="image_limit">{{ __('Image Upload Limit') }} </label>
                                                <input type="number" class="form-control" name="image_limit"
                                                    value="{{ $settings->image_limit['SIZE_LIMIT'] }}"
                                                    placeholder="{{ __('Size') }}...">
                                            </div>
                                        </div>
                                        
                                    </div> <div class="row">

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Offline (Bank Transfer) Details') }}</label>
                                                <textarea class="form-control" name="bank_transfer" rows="3"
                                                    placeholder="{{ __('Offline (Bank Transfer) Details') }}">{{ $config[31]->config_value }}</textarea>
                                            </div>
                                        </div>

                                        <div class="col-md-3 ">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('reCAPTCHA Site Key') }}</label>
                                                <input type="text" class="form-control" name="recaptcha_site_key"
                                                    value="{{ $settings->recaptcha_configuration['RECAPTCHA_SITE_KEY'] }}"
                                                    placeholder="{{ __('reCAPTCHA Site Key') }}...">
                                            </div>
                                            <span>{{ __('If you did not get a reCAPTCHA, create a') }} <a
                                                    href="https://www.google.com/recaptcha/about/"
                                                    target="_blank">{{ __('reCAPTCHA') }}</a> </span>
                                        </div>
                                        <div class="col-md-3 ">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('reCAPTCHA Secret Key') }}</label>
                                                <input type="text" class="form-control" name="recaptcha_secret_key"
                                                    value="{{ $settings->recaptcha_configuration['RECAPTCHA_SECRET_KEY'] }}"
                                                    placeholder="{{ __('reCAPTCHA Secret Key') }}...">
                                            </div>
                                        </div>

                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Google Analytics ID') }}</label>
                                                <input type="text" class="form-control" name="google_analytics_id"
                                                    value="{{ $settings->google_analytics_id }}"
                                                    placeholder="{{ __('Google Analytics ID') }}...">
                                            </div>
                                            <span>{{ __('If you did not get a google analytics id, create a') }} <a
                                                    href="https://analytics.google.com/analytics/web/"
                                                    target="_blank">{{ __('new analytics id.') }}</a> </span>
                                        </div>
                                        
                                        
                                    </div> <div class="row">
                                        
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="form-label">.</div>
                                                <label class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" {{ $settings->recaptcha_configuration['RECAPTCHA_ENABLE'] == 'on' ? 'checked' : '' }}
                                                        name="recaptcha_enable">
                                                        {{ __('reCAPTCHA Enable') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="form-label">.</div>
                                                <label class="form-check form-switch">
                                                    <input class="form-check-input" type="checkbox" {{ $settings->google_configuration['GOOGLE_ENABLE'] == 'on' ? 'checked' : '' }}
                                                        name="google_auth_enable"> {{ __('Google Auth Enable') }}
                                                </label>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Google Client ID') }}</label>
                                                <input type="text" class="form-control" name="google_client_id"
                                                    value="{{ $settings->google_configuration['GOOGLE_CLIENT_ID'] }}"
                                                    placeholder="{{ __('Google CLIENT ID') }}...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Google Client Secret') }}</label>
                                                <input type="text" class="form-control" name="google_client_secret"
                                                    value="{{ $settings->google_configuration['GOOGLE_CLIENT_SECRET'] }}"
                                                    placeholder="{{ __('Google CLIENT Secret') }}...">
                                            </div>
                                        </div>
                                        
                                     </div> <div class="row">
                                         
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Google Redirect') }}</label>
                                                <input type="text" class="form-control" name="google_redirect"
                                                    value="{{ $settings->google_configuration['GOOGLE_REDIRECT'] }}"
                                                    placeholder="{{ __('Google Redirect') }}...">
                                            </div>
                                        </div>
                                        <div class="col-md-6 alert alert-warning ">
                                            <span>{{ __('If you did not get a google OAuth Client ID & Secret Key, follow a') }} <a
                                                href="https://support.google.com/cloud/answer/6158849?hl=en#zippy=%2Cuser-consent%2Cpublic-and-internal-applications%2Cauthorized-domains/"
                                                target="_blank">{{ __(' steps') }}</a> </span>
                                        </div>
                                    </div> <div class="row">
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Google AdSense code')
                                                    }}</label>
                                                <div class="input-group mb-2">
                                                    <span class="input-group-text">
                                                        {{ __('https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=') }}
                                                    </span>
                                                    <input type="text" class="form-control" name="google_adsense_code" value="{{ $settings->google_configuration['GOOGLE_ADSENSE_CODE'] }}" placeholder="{{ __('Google AdSense code') }}..." autocomplete="off">

                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6 alert alert-warning ">
                                            <span>{{ __('If you did not get a google adsense code, create a') }} <a
                                                        href="https://www.google.com/adsense/new"
                                                        target="_blank">{{
                                                        __('new adsense code.') }}</a> </span>
                                        </div>
                                    </div> <div class="row">
                                        <h4 class=" my-3">
                                            {{ __('Site Settings') }}
                                        </h4>
                                        <div class="col-md-12 col-xl-12">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Theme Colors') }}</label>
                                                <div class="row g-2">

                                                        <label class="form-colorinput">
                                                            <input name="app_theme" type="radio" value="blue"
                                                                class="form-colorinput-input"
                                                                {{ $config[11]->config_value == 'blue' ? 'checked' : ''  }} />
                                                            <span class="form-colorinput-color bg-blue"></span>
                                                        </label>
                                                        <label class="form-colorinput form-colorinput-light">
                                                            <input name="app_theme" type="radio" value="indigo"
                                                                class="form-colorinput-input"
                                                                {{ $config[11]->config_value == 'indigo' ? 'checked' : ''  }} />
                                                            <span class="form-colorinput-color bg-indigo"></span>
                                                        </label>
                                                    
                                                        <label class="form-colorinput">
                                                            <input name="app_theme" type="radio" value="green"
                                                                class="form-colorinput-input"
                                                                {{ $config[11]->config_value == 'green' ? 'checked' : ''  }} />
                                                            <span class="form-colorinput-color bg-green"></span>
                                                        </label>
                                                    
                                                        <label class="form-colorinput">
                                                            <input name="app_theme" type="radio" value="yellow"
                                                                class="form-colorinput-input"
                                                                {{ $config[11]->config_value == 'yellow' ? 'checked' : ''  }} />
                                                            <span class="form-colorinput-color bg-yellow"></span>
                                                        </label>
                                                    
                                                        <label class="form-colorinput">
                                                            <input name="app_theme" type="radio" value="red"
                                                                class="form-colorinput-input"
                                                                {{ $config[11]->config_value == 'red' ? 'checked' : ''  }} />
                                                            <span class="form-colorinput-color bg-red"></span>
                                                        </label>
                                                    
                                                        <label class="form-colorinput">
                                                            <input name="app_theme" type="radio" value="purple"
                                                                class="form-colorinput-input"
                                                                {{ $config[11]->config_value == 'purple' ? 'checked' : ''  }} />
                                                            <span class="form-colorinput-color bg-purple"></span>
                                                        </label>
                                                    
                                                        <label class="form-colorinput">
                                                            <input name="app_theme" type="radio" value="pink"
                                                                class="form-colorinput-input"
                                                                {{ $config[11]->config_value == 'pink' ? 'checked' : ''  }} />
                                                            <span class="form-colorinput-color bg-pink"></span>
                                                        </label>
                                                    
                                                        <label class="form-colorinput form-colorinput-light">
                                                            <input name="app_theme" type="radio" value="gray"
                                                                class="form-colorinput-input"
                                                                {{ $config[11]->config_value == 'gray' ? 'checked' : ''  }} />
                                                            <span class="form-colorinput-color bg-muted"></span>
                                                        </label>
                                                    
                                                </div>
                                            </div>
                                        </div>
                                    </div> <div class="row">
                                        <h4 class="my-3">
                                            {{ __('Paypal Settings') }}
                                        </h4>
                                        <div class="col-md-4 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Mode' )}}</label><br>
                                                <select class="form-control"
                                                    placeholder="Select a payment mode" id="select-tags-advanced"
                                                    name="paypal_mode" style="width: 100%">
                                                    <option value="sandbox"
                                                        {{ $config[3]->config_value == 'sandbox' ? 'selected' : '' }}>
                                                        {{ __('Sandbox') }}</option>
                                                    <option value="live"
                                                        {{ $config[3]->config_value == 'live' ? 'selected' : '' }}>
                                                        {{ __('Live') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Client Key') }}</label>
                                                <input type="text" class="form-control" name="paypal_client_key"
                                                    value="{{ $config[4]->config_value }}"
                                                    placeholder="{{ __('Client Key') }}...">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Secret') }}</label>
                                                <input type="text" class="form-control" name="paypal_secret"
                                                    value="{{ $config[5]->config_value }}"
                                                    placeholder="{{ __('Secret') }}...">
                                            </div>
                                        </div>
                                    </div> <div class="row">
                                        <div class="col-sm-6">
                                            <h4 class="my-3">
                                                {{ __('Razorpay Settings') }}
                                            </h4>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('Client Key') }}</label>
                                                    <input type="text" class="form-control" name="razorpay_client_key"
                                                        value="{{ $config[6]->config_value }}"
                                                        placeholder="{{ __('Client Key') }}...">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('Secret') }}</label>
                                                    <input type="text" class="form-control" name="razorpay_secret"
                                                        value="{{ $config[7]->config_value }}"
                                                        placeholder="{{ __('Secret') }}...">
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-sm-6">
                                            <h4 class="my-3">
                                            {{ __('Stripe Settings') }}
                                        </h4>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('Publishable Key') }}</label>
                                                    <input type="text" class="form-control" name="stripe_publishable_key"
                                                        value="{{ $config[9]->config_value }}"
                                                        placeholder="{{ __('Publishable Key') }}...">
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="mb-3">
                                                    <label class="form-label">{{ __('Secret') }}</label>
                                                    <input type="text" class="form-control" name="stripe_secret"
                                                        value="{{ $config[10]->config_value }}"
                                                        placeholder="{{ __('Secret') }}...">
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </div> <div class="row">
                                        <h4 class="my-3">
                                            {{ __('PayHere Settings') }}
                                        </h4>
                                        <div class="col-md-4 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Merchant ID') }}</label>
                                                <input type="text" class="form-control" name="payhere_merchant_id"
                                                    value="{{ $config[33]->config_value }}"
                                                    placeholder="{{ __('Merchant ID') }}...">
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-xl-4">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Merchant Secret') }}</label>
                                                <input type="text" class="form-control" name="payhere_merchant_secret" 
                                                value="{{ $config[32]->config_value }}"
                                                placeholder="{{ __('Merchant Secret') }}...">
                                            </div>
                                        </div>
                                    </div> <div class="row">
                                        
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="form-label">{{ __('Home Banner Image') }} <span
                                                        class="text-danger">
                                                        ({{ __('Recommended size : 728x680') }})</span></div>
                                                <input type="file" class="form-control" name="primary_image"
                                                    placeholder="{{ __('Home Banner Image') }}..."
                                                    accept=".png,.jpg,.jpeg,.gif,.svg" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="form-label">{{ __('Login Page Image') }} <span
                                                        class="text-danger"> ({{ __('Recommended size : 728x680') }})
                                                    </span></div>
                                                <input type="file" class="form-control" name="secondary_image"
                                                    placeholder="{{ __('Login Image') }}..."
                                                    accept=".png,.jpg,.jpeg,.gif,.svg" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="form-label">{{ __('Register Page Image') }} <span
                                                        class="text-danger"> ({{ __('Recommended size : 728x680') }})
                                                    </span></div>
                                                <input type="file" class="form-control" name="register_image"
                                                    placeholder="{{ __('Register Image') }}..."
                                                    accept=".png,.jpg,.jpeg,.gif,.svg" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="form-label">{{ __('Site Logo') }} <span class="text-danger">
                                                        ({{ __('Recommended size : 180x60') }})</span></div>
                                                <input type="file" class="form-control" name="site_logo"
                                                    placeholder="{{ __('Site Logo') }}..."
                                                    accept=".png,.jpg,.jpeg,.gif,.svg" />
                                            </div>
                                        </div>
                                    </div> <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <div class="form-label">{{ __('Favicon') }} <span class="text-danger">
                                                        ({{ __('Recommended size : 200x200') }})</span></div>
                                                <input type="file" class="form-control" name="favi_icon"
                                                    placeholder="{{ __('Favicon') }}..."
                                                    accept=".png,.jpg,.jpeg,.gif,.svg" />
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                        @php
                                            $appNameValue = tenant() ? ($app_name ?? '') : config('app.name');
                                        @endphp

                                        <div class="mb-3">
                                            <label class="form-label">{{ __('App Name') }}</label>
                                            <input type="text" class="form-control" name="app_name"
                                                value="{{ $appNameValue }}"
                                                placeholder="{{ __('App Name') }}...">
                                        </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Site Name') }}</label>
                                                <input type="text" class="form-control" name="site_name"
                                                    value="{{ $settings->site_name }}"
                                                    placeholder="{{ __('Site Name') }}...">
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('SEO Meta Description') }}</label>
                                                <textarea class="form-control" name="seo_meta_desc" rows="3"
                                                    placeholder="{{ __('SEO Meta Description') }}">{{ $settings->seo_meta_description }}</textarea>
                                            </div>
                                        </div>
                                    </div> <div class="row">
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('SEO Keywords') }}</label>
                                                <textarea class="form-control" name="meta_keywords" rows="3"
                                                    placeholder="{{ __('SEO Keywords (Keyword 1, Keyword 2)') }}">{{ $settings->seo_keywords }}</textarea>
                                            </div>
                                        </div>
                                    
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Share Content') }}</label>
                                                <textarea class="form-control" name="share_content" id="share_content"
                                                    cols="10" rows="3"
                                                    placeholder="{{ __('Share Content') }}...">{{ $config[30]->config_value }}</textarea>
                                            </div>
                                        </div>
                                        <div class="col-md-3">
                                            <h2 class="text-danger"> {{ __('Short Codes :') }} </h2>
                                            <span><span class="font-weight-bold">{ business_name }</span> - {{ __('Business Name') }}</span><br>
                                            <span><span class="font-weight-bold">{ business_url }</span> - {{ __('Business URL or Address') }}</span><br>
                                            <span><span class="font-weight-bold">{ appName }</span> - {{ __('App Name') }}</span><br>
                                            <span><span class="font-weight-bold">{ refferal_no }</span> - {{ __('Refferal No') }}</span>
                                        </div>
                                    </div> <div class="row">

                                        
                                        <h4 class=" my-3">
                                            {{ __('Tawk Chat Settings') }}
                                        </h4>
                                        <div class="col-md-3">
                                            <div class="mb-3">
                                                <label class="form-label">{{ __('Tawk Chat URL (s1.src)') }}</label>
                                                <div class="input-group">
                                                    <span class="input-group-text">
                                                        {{ __('https://embed.tawk.to/') }}
                                                    </span>
                                                    <input type="text" class="form-control" name="tawk_chat_bot_key"
                                                        value="{{ $settings->tawk_chat_bot_key }}"
                                                        placeholder="{{ __('Tawk Chat Key') }}" autocomplete="off">
                                                </div>
                                            </div>
                                        </div>
                                       <div class="mb-3 pull-right">
                                                <button type="submit" class="btn btn-primary">

                                                    {{ __('Update') }}
                                                </button>
                                            </div>
                                    </div> 
                                </div>
                        </div>
                    </form>
                </div>
            </div>
</section>
@endsection
@section('javascript')
    @if(session('toast_error'))
        <script>
            toastr.error('{{ session('toast_error') }}');
        </script>
    @endif
    
    @if(session('toast_success'))
        <script>
            toastr.success('{{ session('toast_success') }}');
        </script>
    @endif

    <script type="text/javascript">
        
    </script>
@endsection
