@inject('request', 'Illuminate\Http\Request')
@php
    use Tymon\JWTAuth\Facades\JWTAuth;
    use Illuminate\Support\Facades\DB;
    use App\Utils\TransactionUtil;
    use App\Utils\ModuleUtil;
    use App\Utils\ContactUtil;
    
    $sms = new TransactionUtil(new ModuleUtil(), new ContactUtil());
    $sms_bal = $sms->__getSMSBalance(date('Y-m-d'));
     $user = auth()->user();
    $business_id = $user->business_id;//request()->session()->get('user.business_id');
   
 
    // Fetch the business name
    $bs_name = DB::table('business')->where('id', $business_id)->select('name')->first();

    // Set the customer_group attribute on the user
    $user->customer_group = $bs_name->name;

    // Generate the JWT token with the updated user attributes
    $token = JWTAuth::fromUser($user);
@endphp
@php
    $business_id = request()->session()->get('user.business_id');
    if (empty($business_id)) {
        return redirect('/logout');
    }
    $top_belt_bg = DB::table('site_settings')->where('id', 1)->select('topBelt_background_color')->first()
        ->topBelt_background_color;
@endphp

@php
    $business_id = request()->session()->get('user.business_id');

    $day_end = DB::table('business')->where('id', $business_id)->select('day_end')->first();
    if (!empty($day_end)) {
        $day_end = $day_end->day_end;
    } else {
        $day_end = 0;
    }
    $day_end_enable = DB::table('business')->where('id', $business_id)->select('day_end_enable')->first();
    if (!empty($day_end_enable)) {
        $day_end_enable = $day_end_enable->day_end_enable;
    } else {
        $day_end_enable = 0;
    }
    $tour_toggle = DB::table('site_settings')->where('id', 1)->select('tour_toggle')->first()->tour_toggle;

    $business_id = request()->session()->get('user.business_id');
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);

    $pop_button_on_top_belt = \App\Utils\ModuleUtil::hasThePermissionInSubscription(
        $business_id,
        'pop_button_on_top_belt',
    );

    $cache_clear = 0;
    $pacakge_details = [];
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
        if (array_key_exists('cache_clear', $pacakge_details)) {
            $cache_clear = $pacakge_details['cache_clear'];
        }
        if (array_key_exists('pos_sale', $pacakge_details)) {
            $pos_sale = $pacakge_details['pos_sale'];
        }

        if (array_key_exists('hr_module', $pacakge_details)) {
            $hr_module = $pacakge_details['hr_module'];
        }
    }
    if (auth()->user()->can('superadmin')) {
        $cache_clear = 1;
        $pos_sale = 1;
    }

    $help_desk_url = App\System::getProperty('helpdesk_system_url') . '?token=' . $token;
@endphp
<!-- Main Header -->
<div class="header-area">
    <div class="row align-items-center" style="display: flex; align-items: center;">
        <!-- nav and search button -->
        <div class="d-flex align-items-center px-2 flex-wrap" style="width: 20%; min-width: 20px; background-color: #f5f5f5; min-height: 50px;">
            {{-- Sidebar Toggle --}}
            <div class="nav-btn pull-left rounded" style="background-color: #565656; padding: 3px; margin-bottom: 10px;" id="sidebar_collapser">
                <span></span>
                <span></span>
                <span></span>
            </div>

            {{-- Current Package & Clear Cache --}}
            <div class="d-flex align-items-center flex-wrap ms-2" style="gap: 10px;">
                @include('superadmin::layouts.partials.active_subscription')

                @if ($cache_clear)
                    <a href="{{ action('BusinessController@clearCache') }}"
                        class="btn btn-sm btn-danger clear_cache_btn">
                        @lang('lang_v1.clear_cache')
                    </a>
                @endif
            </div>
        </div>


        <!-- profile info & task notification -->
        <div class="my-div1" style="flex: 1; margin-left: 20px; background-color: #2596be; height: 50px; border-radius: 5px;">
            <ul class="notification-area pull-right my-div3">

                @if (Module::has('Essentials'))
                    @if (isset($hr_module) && $hr_module == 1)
                        @includeIf('essentials::layouts.partials.header_part')
                    @endif

                @endif
                
                <a href="{{action('\Modules\SMS\Http\Controllers\SMSController@index')}}" type="button"
                    class="btn btn-flat pull-left m-8 hidden-xs btn-sm mt-10 @if($sms_bal > 0) text-white @else text-danger @endif">
                    <strong>@lang('sms::lang.sms_bal') : {{ @num_format($sms_bal) }}</strong>
                </a>

                <a target="_blank" href="{{ action('\Modules\HelpGuide\Http\Controllers\Frontend\IndexController@index') }}" title="Help Guide" type="button"
                    class="btn btn-flat pull-left m-8 hidden-xs btn-sm mt-10 text-white">
                    <strong>Help Guide</strong>
                </a>
                @if (request()->session()->get('superadmin-logged-in') && !request()->session()->get('user.is_pump_operator'))
                    <a href="{{ action('\Modules\Superadmin\Http\Controllers\BusinessController@backToSuperadmin') }}"
                        title="@lang('lang_v1.back_to_superadmin')" type="button"
                        class="btn btn-flat pull-left m-8 text-white hidden-xs btn-sm mt-10">
                        <strong><i class="fa fa-arrow-left fa-lg" aria-hidden="true"></i></strong>
                    </a>
                @endif
                @if (!request()->session()->get('user.is_pump_operator'))
                    <a href="#" id="btnLock" title="@lang('lang_v1.lock_screen')" type="button"
                        class="btn btn-flat pull-left m-8 hidden-xs btn-sm text-white mt-10 popover-default"
                        data-placement="bottom">
                        <strong><i class="fa fa-lock fa-lg" aria-hidden="true"></i></strong>
                    </a>
                @endif

                {{-- <a href="#" id="btnCalculator" title="@lang('lang_v1.calculator')" type="button"
                    class="btn  btn-flat text-white pull-left m-8 hidden-xs btn-sm mt-10 popover-default" tabindex="-1"
                    data-toggle="click" data-trigger="click" data-content='@include('layouts.partials.calculator')'
                    data-html="true" data-placement="bottom">
                    <strong><i class="fa fa-calculator fa-lg" aria-hidden="true"></i></strong>
                </a> --}}

                @if ($request->segment(1) == 'pos')
                    <a href="#" type="button" id="register_details"
                        title="{{ __('cash_register.register_details') }}" data-toggle="tooltip" data-placement="bottom"
                        class="btn text-white btn-flat pull-left m-8 hidden-xs btn-sm mt-10 btn-modal"
                        data-container=".register_details_modal"
                        data-href="{{ action('CashRegisterController@getRegisterDetails') }}">
                        <strong><i class="fa fa-briefcase fa-lg" aria-hidden="true"></i></strong>
                    </a>
                    <a href="#" type="button" id="close_register"
                        title="{{ __('cash_register.close_register') }}" data-toggle="tooltip" data-placement="bottom"
                        class="btn text-white btn-flat pull-left m-8 hidden-xs btn-sm mt-10 btn-modal"
                        data-container=".close_register_modal"
                        data-href="{{ action('CashRegisterController@getCloseRegister') }}">
                        <strong><i class="fa fa-window-close fa-lg"></i></strong>
                    </a>
                @endif

                @if (
                    !request()->session()->get('business.is_patient') &&
                        !request()->session()->get('business.is_hospital') &&
                        !request()->session()->get('business.is_pharmacy') &&
                        !request()->session()->get('business.is_laboratory'))
                    @if ($day_end_enable == 1)
                        @can('day_end.view')
                            <a href="{{ action('BusinessController@dayEnd') }}" title="Day End" data-toggle="tooltip"
                                data-placement="bottom"
                                class="btn @if ($day_end == 0) @else text-white @endif btn-flat pull-left m-8 hidden-xs btn-sm mt-10">
                                <strong><i class="fa fa-sun-o"></i> &nbsp;@if ($day_end == 0)
                                        @lang('lang_v1.day_end')
                                    @else
                                        @lang('lang_v1.day_ended')
                                    @endif
                                </strong>
                            </a>
                        @endcan
                    @endif
                    @if ((isset($pos_sale) && $pos_sale == 1) || $pop_button_on_top_belt == 1)
                        <div class="btn-group pull-left m-8 hidden-xs mt-10">
                            <button type="button" class="btn btn-flat text-white btn-sm dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" style="background-color: transparent; color: white;">
                                <strong><i class="fa fa-th-large"></i> &nbsp; @lang('sale.pos_pop_sale')</strong> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @if (isset($pos_sale) && $pos_sale == 1)
                                    @can('sell.create')
                                        <li style="width: 80%; margin-bottom: 5px;">
                                            <a href="{{ action('SellPosController@create') }}" title="POS">
                                                <i class="fa fa-th-large"></i> &nbsp; @lang('sale.pos_sale')
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                                @if ($pop_button_on_top_belt == 1)
                                    @can('purchase.create')
                                        <li style="width: 80%">
                                            <a href="{{ action('PurchasePosController@create') }}" title="POP">
                                                <i class="fa fa-th-large"></i> &nbsp; @lang('purchase.pop')
                                            </a>
                                        </li>
                                    @endcan
                                @endif
                            </ul>
                        </div>
                    @endif


                    @can('profit_loss_report.view')
                        <a href="#" type="button" id="view_todays_profit" title="{{ __('home.todays_profit') }}"
                            data-toggle="tooltip" data-placement="bottom"
                            class="btn   btn-flat pull-left m-8 hidden-xs btn-sm mt-10">
                            <strong><i class="fa fa-money fa-md text-white"></i></strong>
                        </a>
                    @endcan

                    <!-- Help Button -->
                    @if ($tour_toggle == 1)
                        @if (auth()->user()->hasRole('Admin#' . auth()->user()->business_id))
                            <a href="#" type="button" id="start_tour" title="@lang('lang_v1.application_tour')"
                                data-toggle="tooltip" data-placement="bottom"
                                class="btn text-white  btn-flat pull-left m-8 hidden-xs btn-sm mt-10">
                                <strong><i class="fa fa-question-circle fa-md" aria-hidden="true"></i></strong>
                            </a>
                        @endif
                    @endif
                @endif


                <ul class="nav navbar-nav" style="margin-right: 10px; margin-left: -35px;">

                    <!-- User Account Menu -->
                    <li class="dropdown user user-menu my-div2" style="min-width: 100px;">
                        <!-- Menu Toggle Button -->
                        <a href="#" class="dropdown-toggle btn  btn-sm btn-danger btn-flat mt-10 ml-10"
                            style="height: 30px; padding: 3px;" data-toggle="dropdown">
                            <!-- The user image in the navbar-->
                            @php
                                $profile_photo = auth()->user()->media;
                            @endphp
                            @if (!empty($profile_photo))
                                <img src="{{ $profile_photo->display_url }}" class="user-image" alt="User Image">
                            @endif
                            <!-- hidden-xs hides the username on small devices so only the image appears. -->
                            <span id="span_username">
                                {{ strlen(Auth::User()->first_name) > 27 ? substr(Auth::User()->first_name, 0, 27) . '...' : Auth::User()->first_name }}
                            </span>
                        </a>
                        <ul class="dropdown-menu rounded shadow-sm p-3 mb-5 bg-white rounded"
                            style="margin-top: 15px;">
                            <!-- The user image in the menu -->
                            <li style="width: 100%; text-align : center;margin-left: 0px !important">
                                <b>{{ Auth::User()->first_name }} {{ Auth::User()->last_name }}</b>
                            </li>
                            <hr>
                            <!-- Menu Body -->
                            <!-- Menu Footer-->
                            <li class="">
                                <a href="{{ action('UserController@getProfile') }}" class=""><i
                                        class="fa fa-user"></i>&nbsp; @lang('lang_v1.profile')</a>
                            </li>
                            <li>
                                @if (auth()->user()->is_pump_operator)
                                    <a href="{{ action('Auth\PumpOperatorLoginController@logout') }}"
                                        class=""><i class="fa fa-sign-out"></i>&nbsp; @lang('lang_v1.sign_out')</a>
                                @elseif(auth()->user()->is_property_user)
                                    <a href="{{ action('Auth\PropertyUserLoginController@logout') }}?id={{ request()->session()->get('business.company_number') }}"
                                        class=""><i class="fa fa-sign-out"></i>&nbsp; @lang('lang_v1.sign_out')</a>
                                @else
                                    <a href="{{ action('Auth\LoginController@logout') }}?id={{ request()->session()->get('business.company_number') }}"
                                        class=""><i class="fa fa-sign-out"></i>&nbsp; @lang('lang_v1.sign_out')</a>
                                @endif
                            </li>
                        </ul>
                    </li>
                    <!-- Control Sidebar Toggle Button -->
                </ul>








            </ul>
        </div>
        {{-- <div class="col-md-12">
        </div> --}}
        <style>
            @media (max-width: 480px) {
                #span_username {
                    display: none;
                }

                .my-div2 {
                    margin-top: -8px !important;
                }

                .my-div3 {
                    margin: 0px 0 0px !important;
                    width: 100% !important;
                }
            }
        </style>
    </div>
</div>
