<li class="nav-item {{ in_array($request->segment(1), ['mpcs']) ? 'active active-sub' : '' }}">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#mpcs-menu"
        aria-expanded="true" aria-controls="mpcs-menu">
        <i class="fa fa-calculator"></i>
        <span>@lang('mpcs::lang.mpcs')</span>
    </a>
    <div id="mpcs-menu" class="collapse" aria-labelledby="headingPages" data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">@lang('mpcs::lang.mpcs'):</h6>
            @if(auth()->user()->can('f16a_form') || auth()->user()->can('f15a9abc_form') || auth()->user()->can('f16a_form') || auth()->user()->can('f21c_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'form-set-1' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\MPCSController@FromSet1')}}">@lang('mpcs::lang.form_set_1')</a>
            @endif
            @if(auth()->user()->can('f9a_form') || auth()->user()->can('f9a_settings_form'))
          
            <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'form-9a' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\MPCSController@From9A')}}">@lang('mpcs::lang.form_9_a')</a>
            @endif
            
             
            
@php
    $canF9CForm = auth()->user()->can('f9c_form');
    $canF9CSettingsForm = auth()->user()->can('f9c_settings_form');
@endphp

 
@if($canF9CForm || $canF9CSettingsForm)
    <a class="collapse-item {{ request()->segment(1) == 'mpcs' && request()->segment(2) == 'form-9c' ? 'active' : '' }}" 
       href="{{ action('\Modules\MPCS\Http\Controllers\MPCSController@From9C') }}">
       @lang('mpcs::lang.9c_cash_form')
    </a>
@else
    <p>Access Denied</p>
@endif
@php
    $canF9CcrForm = auth()->user()->can('f9c_form');
    $canF9CcrSettingsForm = auth()->user()->can('f9c_settings_form');
@endphp

 
@if($canF9CcrForm || $canF9CcrSettingsForm)
    <a class="collapse-item {{ request()->segment(1) == 'mpcs' && request()->segment(2) == 'form-9ccr' ? 'active' : '' }}" 
       href="{{ action('\Modules\MPCS\Http\Controllers\MPCSController@From9CCR') }}">
       @lang('mpcs::lang.9c_credit_form')
    </a>
@else
    <p>Access Denied</p>
@endif
            
            <!--By Zamaluddin : Time 04:20 PM : 28 January 2025-->
            
          
            
            @if(auth()->user()->can('f15_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F15' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\F15FormController@index')}}">@lang('mpcs::lang.F15_form')</a>
            
            @endif
            
            <!--End-->
            
            @if(auth()->user()->can('f14_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F14' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\NewF14FormController@index')}}">@lang('mpcs::lang.F14_form')</a>
            
            @endif
            
            
            
            @if(auth()->user()->can('f16_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F116A' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\F16AFormController@index')}}">@lang('mpcs::lang.F16A_form')</a>
            
            @endif
            
              {{--@if(auth()->user()->can('f21_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F21' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\F21CFormController@index')}}">@lang('mpcs::lang.F21C_form')</a>
            @endif --}} 
            
            @if(auth()->user()->can('f17_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F17' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\F17FormController@index')}}">@lang('mpcs::lang.F17_form')</a>
            @endif
            @if(auth()->user()->can('f14b_form') || auth()->user()->can('f20_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F14B_F20_Forms' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\F20F14bFormController@index')}}">@lang('mpcs::lang.F20andF14b_form')</a>
            @endif
           
            @if(auth()->user()->can('f20_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == '20Form' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\F20FormController@index')}}">@lang('mpcs::lang.20form')</a>
            @endif
            @if(auth()->user()->can('f21_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == '21Form' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\F21FormController@get21Form')}}">@lang('mpcs::lang.f21_form')</a>
            @endif
           
            @if(auth()->user()->can('f21_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F21Form' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\F21FormController@index')}}">@lang('mpcs::lang.F21C_form')</a>
            @endif
           
            @if(auth()->user()->can('f22_stock_taking_form'))
                <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'F22_stock_taking' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\F22FormController@F22StockTaking')}}">@lang('mpcs::lang.F22StockTaking_form')</a>
                
            @endif
            <a class="collapse-item {{ $request->segment(1) == 'mpcs' && $request->segment(2) == 'forms-setting' ? 'active' : '' }}" href="{{action('\Modules\MPCS\Http\Controllers\FormsSettingController@index')}}">@lang('mpcs::lang.mpcs_forms_setting')</a>
        </div>
    </div>
</li>

