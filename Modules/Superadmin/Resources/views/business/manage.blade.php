@extends('layouts.app')
@section('title', __('superadmin::lang.superadmin') . ' | Business')

@section('content')

<style>
 .checkbox input[type="checkbox"]:not(:checked) + label:before {
  content: "";
  display: inline-block;
  width: 16px;
  height: 16px;
  border: 2px solid red;
  margin-right: 5px;
}


/* Background color for "danger" state */
        .bg-danger {
            background-color: #F6C8C8 !important; 
            color: #000 !important;
        }
        
         /*Background color for "success" state */
        .bg-success {
            background-color: #D1E6C0 !important; 
            color: #000 !important;
        }

</style>

<style>
    .input-icheck-red{
      accent-color: red !important;
      outline: 1px solid red !important;
      margin-right: 5px !important;
    }


</style>

    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>@lang( 'superadmin::lang.individual_company_permissions' )
        </h1>
    </section>
    
    @if(!empty($subscription))
    <!-- Main content -->
    <section class="content">
        <div class="box">
                @can('superadmin')
                                
                <div class="box-body">
                    
                    {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\BusinessController@saveManage',
                    $business->id), 'method' => 'post', 'id' => 'custom_permission_form', 'enctype' => 'multipart/form-data'])
                    !!}
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px">
                      <div class="card-header text-center">
                        <h4> @lang('superadmin::lang.business_name'): {{$business->name}}</h4>
                        <hr>
                      </div>
                      <div class="card-body">
                        <div class="row">
                                <div class="col-md-2">
                                    <div class="from-group">
                                        {!! Form::label('annual_fee_package', __('superadmin::lang.annual_fee_package'), ['class' =>
                                        'search_label']) !!}
                                        {!! Form::text('annual_fee_package', !empty($package_manage) ?
                                        number_format($package_manage->price, 2, '.', '') : null, ['class' => 'form-control', 'id'
                                        =>
                                        'annual_fee_package', 'placeholder' => __('superadmin::lang.annual_fee_package')]) !!}
                                    </div>
                                </div>
                                <input type="hidden" name="package_manage_id"
                                       value="{{!empty($package_manage) ? $package_manage->id : null}}">
                                <div class="col-sm-2">
                                    <div class="form-group">
                                        <label for="currency_id" class="search_label">Currency:</label>
                                        <div class="input-group">
                                    <span class="input-group-addon">
                                        <i class="fa fa-money"></i>
                                    </span>
                                            {!! Form::select('currency_id', $currencies, !empty($package_manage) ?
                                            $package_manage->currency_id : null, ['class' => 'form-control
                                            select2','placeholder' => __('business.currency_placeholder'), 'required', 'id' =>
                                            'currency_id_manage']); !!}
                                        </div>
                                    </div>
                                </div>
                                
                                
                                <div class="col-sm-4">
                                    <div class="form-group">
                                        <label for="search_settings">@lang('superadmin::lang.search_permissions'):</label>
                                        @include('superadmin::layouts.partials.search_settings')
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <label for="search_settings">Individual Package:</label>
                                    <select name="individual_package" id="individual_package" class="form-control ">
                                        <option value="">Please Select</option>
                                        <option value="yes">Yes</option>
                                        <option value="No">No</option>
                                    </select>
                                </div>
                                <div class="col-sm-2">
                                    <label for=""></label>
                                    <div class="checkbox mt-10">
                                         <label>
                                             {!! Form::checkbox('opt_verification', 1,$business->owner->setting->opt_verification_enabled ?? 0, [ 'class' => 'input-icheck-red','id' =>	'opt_verification']); !!} {{ __( 'superadmin::lang.OTP_verification_enabled' ) }} </label>
                                         </label>
                                     </div>
                                 </div>
    
                                
                            </div>
                        <div class="row">
                                
                                
                                <div class="col-sm-3 product_count">
                                    <div class="form-group">
                                        {!! Form::label('product_count', __('superadmin::lang.product_count').':') !!}
                                        {!! Form::number('product_count', !empty($manage_module_enable['product_count']) ? $manage_module_enable['product_count'] :
                                        $previous_package_data['product_count'], ['class' => 'form-control', 'required', 'min' =>
                                        0]); !!}
    
                                        <span class="help-block">
                                    @lang('superadmin::lang.infinite_help')
                                </span>
                                    </div>
                                </div>
                                <div class="col-sm-3 product_count">
                                    <div class="form-group">
                                        {!! Form::label('location_count', __('superadmin::lang.location_count').':') !!}
                                        {!! Form::number('location_count', !empty($manage_module_enable['location_count']) ? $manage_module_enable['location_count']
                                        : $previous_package_data['location_count'], ['class' => 'form-control', 'required', 'min' =>
                                        0]); !!}
    
                                        <span class="help-block">
                                    @lang('superadmin::lang.infinite_help')
                                </span>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="from-group">
                                        {!! Form::label('vat_effective_date', __('superadmin::lang.vat_effective_date'), ['class' =>
                                        'search_label']) !!}
                                        {!! Form::date('vat_effective_date', !empty($manage_module_enable['vat_effective_date']) ?
                                        $manage_module_enable['vat_effective_date'] : null, ['class' => 'form-control', 'id'
                                        =>
                                        'vat_effective_date', 'required', 'placeholder' => __('superadmin::lang.vat_effective_date')]) !!}
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <div class="from-group">
                                        {!! Form::label('post_dated_cheques_effective_date', __('superadmin::lang.post_dated_cheques_effective_date'), ['class' =>
                                        'search_label']) !!}
                                        {!! Form::date('post_dated_cheques_effective_date', !empty($manage_module_enable['post_dated_cheques_effective_date']) ?
                                        $manage_module_enable['post_dated_cheques_effective_date'] : null, ['class' => 'form-control', 'id'
                                        =>
                                        'post_dated_cheques_effective_date', 'required', 'placeholder' => __('superadmin::lang.post_dated_cheques_effective_date')]) !!}
                                    </div>
                                </div>
                                
                                <div class="clearfix"></div>
                                
                                <h5>{{__('superadmin::lang.register_count')}}</h5>
                                
                               @foreach ($business_locations as $location)
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            {!! Form::label('register_count['.$location->id.']', $location->name.':') !!}
                                            {!! Form::number(
                                                'register_count[' . $location->id . ']',
                                                isset($manage_module_enable['register_count'][$location->id]) ? $manage_module_enable['register_count'][$location->id] : null,
                                                ['class' => 'form-control', 'required', 'min' => 0]
                                            ) !!}
                                            <span class="help-block">
                                                @lang('superadmin::lang.infinite_help')
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
  
                                <div class="col-md-3">
                                    <div class="from-group">
                                {!! Form::label('whatsapp_phone_no', __('superadmin::lang.whatsapp_phone_no'), ['class' =>
                                        'search_label']) !!}
                                        {!! Form::text('whatsapp_phone_no', !empty($manage_module_enable['whatsapp_phone_no']) ?
                                        $manage_module_enable['whatsapp_phone_no'] : null, ['class' => 'form-control', 'id'
                                        =>
                                        'whatsapp_phone_no', 'required', 'placeholder' => __('superadmin::lang.whatsapp_phone_no')]) !!} 
                                        </div>
                                        </div>
                                <div class="col-sm-3">
                                    <label for=""></label>
                                    <div class="checkbox  mt-10">
                                        <label>
                                            {!! Form::checkbox('day_end_enable', 1, $business->day_end_enable , [ 'class' => 'input-icheck-red ch_select','id' =>	'day_end_enable']); !!} {{ __( 'superadmin::lang.day_end' ) }} </label>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <label for=""></label>
                                    <div class="checkbox mt-10">
                                         <label>
                                             {!! Form::checkbox('re_captcha_enabled', 1,$business->owner->setting->re_captcha_enabled ?? 0, [ 'class' => 'input-icheck-red','id' =>	're_captcha_enabled']); !!} {{ __( 'superadmin::lang.ReCAPTCHA_enabled_for_business' ) }} </label>
                                         </label>
                                     </div>
                                 </div>
                                <div class="col-sm-3">
                                    <label for=""></label>
                                 <div class="checkbox mt-10">
                                    <label>
                                        {!! Form::checkbox('sms_non_delivery', 1, $business->sms_non_delivery ? true : false, [ 'class' => 'input-icheck-red ch_select', 'id' => 'sms_non_delivery']) !!}
                                        {{ __( 'superadmin::lang.sms_non_delivery' ) }}
                                    </label>
                                </div>

                                </div>
                                
                                {{---<div class="form-group">
                                    <div class="checkbox">
                                        {!! Form::checkbox('select_all', 1, false, ['class' => 'input-icheck-red select_all',])
                                        !!}{{__('superadmin::lang.select_all')}}
                                    </div>
                                </div> --}}
                            </div>
                      </div>
                      
                    </div>

                    <div class="card text-left bg-success" style="border: 1px solid #D9D8D8; margin-bottom: 30px">
                        <div class="card-header text-center">
                            <h4>@lang('superadmin::lang.super_admin_settings')</h4>
                            <hr>
                        </div>
                        <div class="card-body">
                        <div class="row">
                            {{-- Super Admin Name --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('super_admin_name', __('superadmin::lang.super_admin_name')) !!}
                                    {!! Form::text('super_admin_name', $module_activation_data['super_admin_name'] ?? env('SUPER_ADMIN_NAME'), ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            {{-- Super Admin Address --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('super_admin_address', __('superadmin::lang.super_admin_address')) !!}
                                    {!! Form::text('super_admin_address', $module_activation_data['super_admin_address'] ?? env('SUPER_ADMIN_ADDRESS'), ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            {{-- Super Admin Contact --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('super_admin_contact', __('superadmin::lang.super_admin_contact')) !!}
                                    {!! Form::text('super_admin_contact', $module_activation_data['super_admin_contact'] ?? env('SUPER_ADMIN_CONTACT'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- VAT Registered --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('super_admin_vat_registered', __('superadmin::lang.vat_registered')) !!}
                                    {!! Form::select('super_admin_vat_registered', ['no' => 'No', 'yes' => 'Yes'], $module_activation_data['super_admin_vat_registered'] ?? env('SUPER_ADMIN_VAT_REGISTERED'), ['class' => 'form-control', 'id' => 'vat_registered']) !!}
                                </div>
                            </div>

                            {{-- VAT Number (conditionally visible) --}}
                            <div class="col-md-4" id="vat_number_section" style="{{ ($module_activation_data['super_admin_vat_registered'] ?? env('SUPER_ADMIN_VAT_REGISTERED')) == 'yes' ? '' : 'display:none;' }}">
                                <div class="form-group">
                                    {!! Form::label('super_admin_vat_number', __('superadmin::lang.vat_number')) !!}
                                    {!! Form::text('super_admin_vat_number', $module_activation_data['super_admin_vat_number'] ?? env('SUPER_ADMIN_VAT_NUMBER'), ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            {{-- App Footer --}}
                            <div class="col-md-4">
                                <div class="form-group">
                                    {!! Form::label('app_footer', __('superadmin::lang.app_footer')) !!}
                                    {!! Form::text('app_footer', $module_activation_data['app_footer'] ?? env('APP_FOOTER'), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Invoice Footer --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('invoice_footer', __('superadmin::lang.invoice_footer')) !!}
                                    {!! Form::textarea('invoice_footer', $module_activation_data['invoice_footer'] ?? env('INVOICE_FOOTER'), ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>

                            {{-- Report/Page Footer --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('report_footer', __('superadmin::lang.report_footer')) !!}
                                    {!! Form::textarea('report_footer', $module_activation_data['report_footer'] ?? env('REPORT_FOOTER'), ['class' => 'form-control', 'rows' => 3]) !!}
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            {{-- Payment Gateways --}}
                            <div class="col-md-6">
                                <div class="form-group">
                                    {!! Form::label('payment_gateways', __('superadmin::lang.payment_gateways')) !!}
                                    {!! Form::select('payment_gateways', [
                                        'stripe' => 'Stripe',
                                        'paypal' => 'PayPal',
                                        'razorpay' => 'Razorpay',
                                        'pesapal' => 'Pesapal',
                                        'payhere' => 'PayHere',
                                        'online' => 'Pay Online',
                                        'offline' => 'Pay Offline',
                                    ], $module_activation_data['payment_gateways'] ?? env('PAYMENT_GATEWAYS'), ['class' => 'form-control', 'placeholder' => __('superadmin::lang.select_payment_gateway')]) !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    </div>

                    <div class="card text-left bg-success" style="border: 1px solid #D9D8D8; margin-bottom: 30px">
                        <div class="card-header text-center">
                            <h4>@lang('superadmin::lang.daily_collection_settings')</h4>
                            <hr>
                        </div>
                        <div class="card-body">
                           <div class="row">
                                <div class="col-md-4">
                                    <div class="checkbox-wrapper">
                                        {!! Form::checkbox('same_order_no_daily_collection', 1,
                                        !empty($manage_module_enable['same_order_no_daily_collection']) ? true : false, ['class' =>
                                        'input-icheck-red ch_select']) !!}
                                        <label class="search_label">@lang('superadmin::lang.same_order_no')</label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('daily_shift_page', 1,
                                    !empty($manage_module_enable['daily_shift_page']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.daily_shift_page')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('daily_cash_tab', 1,
                                    !empty($manage_module_enable['daily_cash_tab']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">Daily Cash Tab</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    {!! Form::checkbox('daily_credit_sales', 1,
                                    !empty($manage_module_enable['daily_credit_sales']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">Daily Credit Sale Tab</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('daily_cards', 1,
                                    !empty($manage_module_enable['daily_cards']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">Daily Cards Tab</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('daily_shortage_excess', 1,
                                    !empty($manage_module_enable['daily_shortage_excess']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">Daily Shortage Excess Tab</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('daily_cheques', 1,
                                    !empty($manage_module_enable['daily_cheques']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">Daily Cheque Tab</label>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    {!! Form::checkbox('collection_summary', 1,
                                    !empty($manage_module_enable['collection_summary']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">Collection Summary Tab</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('daily_collections_settings', 1,
                                    !empty($manage_module_enable['daily_collections_settings']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">Daily Collection Settings Tab</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('daily_cash_status', 1,
                                    !empty($manage_module_enable['daily_cash_status']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">Daily Cash Status Tab</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('daily_shift', 1,
                                    !empty($manage_module_enable['daily_shift']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">Daily Shift Tab</label>
                                </div>
                            </div>
                        </div>

                         

                    </div>



                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px">
                      <div class="card-header text-center">
                        <h4> @lang('superadmin::lang.subscription_reminder'):</h4>
                        <hr>
                      </div>
                      <div class="card-body">
                        <div class="row">
                                <div class="col-md-3">
                                    <div class="from-group">
                                        {!! Form::label('expiry_date', __('superadmin::lang.expiry_date'), ['class' =>
                                        'search_label']) !!}
                                        {!! Form::text('expiry_date', !empty($subscription) ?
                                        @format_date($subscription->end_date) : null, ['class' => 'form-control', 'id'
                                        =>
                                        'expiry_date','disabled', 'placeholder' => __('superadmin::lang.expiry_date')]) !!}
                                    </div>
                                </div>
                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::label('first_reminder', __('superadmin::lang.first_reminder').':') !!}
                                        {!! Form::number('first_reminder', !empty($manage_module_enable['first_reminder']) ? $manage_module_enable['first_reminder']
                                        : 0, ['class' => 'form-control', 'required', 'min' =>
                                        0]); !!}
                                    </div>
                                </div>
                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::label('second_reminder', __('superadmin::lang.second_reminder').':') !!}
                                        {!! Form::number('second_reminder', !empty($manage_module_enable['second_reminder']) ? $manage_module_enable['second_reminder']
                                        : 0, ['class' => 'form-control', 'required', 'min' =>
                                        0]); !!}
                                    </div>
                                </div>
                                
                                <div class="col-sm-3">
                                    <div class="form-group">
                                        {!! Form::label('third_reminder', __('superadmin::lang.third_reminder').':') !!}
                                        {!! Form::number('third_reminder', !empty($manage_module_enable['third_reminder']) ? $manage_module_enable['third_reminder']
                                        : 0, ['class' => 'form-control', 'required', 'min' =>
                                        0]); !!}
                                    </div>
                                </div>
                                
                                
                                
                            </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>@lang('superadmin::lang.message_content')</label>
                                <textarea class="form-control" rows="5" name="message_content" required>{{!empty($manage_module_enable['message_content']) ? $manage_module_enable['message_content'] : ''}}</textarea>
                            </div>
                        </div>
                        <div class="row" id="amounts_row">
          
                        <div class="col-sm-12">
                              {!! Form::label('amount', __( 'superadmin::lang.phone_no' ) .":*") !!}
                          </div>
                          
                          @if(!empty($manage_module_enable['reminder_phone']))
                            @foreach(json_decode($manage_module_enable['reminder_phone'],true) as $key => $one_phone)
                                <div class="form-group col-sm-4 @if($key > 0) added-amount @endif">
                                    <div class="input-group">
                                      {!! Form::text('reminder_phone[]', $one_phone, ['class' => 'form-control', 'required','placeholder' => __(
                                        'superadmin::lang.phone_no'),]); !!}
                                        @if($key == 0)
                                            <span  class="input-group-addon bg-info" id="add_amount"> + </span>
                                        @else
                                            <span  class="input-group-addon bg-danger remove_amount"> - </span>
                                        @endif
                                      
                                    </div>
                                  </div>
                            @endforeach
                          @else
                            <div class="form-group col-sm-4">
                                <div class="input-group">
                                  {!! Form::text('reminder_phone[]', null, ['class' => 'form-control', 'required','placeholder' => __(
                                    'superadmin::lang.phone_no'),]); !!}
                                  <span  class="input-group-addon bg-info" id="add_amount"> + </span>
                                </div>
                              </div>
                          @endif
                          
                          
                          
                        </div>
                      </div>
                      
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px">
                      <div class="card-header text-center">
                        <h4> @lang('superadmin::lang.regenerate_vat'):</h4>
                        <hr>
                      </div>
                      <div class="card-body">
                        <div class="row">
                            <h5>@lang('superadmin::lang.single_transaction')</h5>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! Form::checkbox('individual_sale', 1,
                                !empty($manage_module_enable['individual_sale']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.sales')</label>
                            </div> 
                            
                            <div class="col-md-4">
                                {!! Form::checkbox('individual_purchase', 1,
                                !empty($manage_module_enable['individual_purchase']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.purchases')</label>
                            </div> 
                            
                            <div class="col-md-4">
                                {!! Form::checkbox('individual_expense', 1,
                                !empty($manage_module_enable['individual_expense']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.expenses')</label>
                            </div> 
                            
                        </div>
                        <hr>
                        
                        <div class="row">
                            <h5>@lang('superadmin::lang.date_range')</h5>
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-4">
                                {!! Form::checkbox('range_sale', 1,
                                !empty($manage_module_enable['range_sale']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.sales')</label>
                            </div> 
                            
                            <div class="col-md-4">
                                {!! Form::checkbox('range_purchase', 1,
                                !empty($manage_module_enable['range_purchase']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.purchases')</label>
                            </div> 
                            
                            <div class="col-md-4">
                                {!! Form::checkbox('range_expense', 1,
                                !empty($manage_module_enable['range_expense']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.expenses')</label>
                            </div> 
                            
                        </div>
                        
                      </div>
                      
                    </div>
                    
                
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px">
                      <div class="card-header text-center">
                        <h4> @lang('superadmin::lang.not_subscribed'):</h4>
                        <hr>
                      </div>
                      <div class="card-body">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-2">
                             {!! Form::hidden('ns_deposits_module', 0) !!}
                                {!! Form::checkbox('ns_deposits_module', 1,
                                !empty($manage_module_enable['ns_deposits_module']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.ns_deposit_module')</label>
                            </div> 
                            
                            <div class="col-md-2">
                            {!! Form::hidden('ns_asset_module', 0) !!}
                                {!! Form::checkbox('ns_asset_module', 1,
                                !empty($manage_module_enable['ns_asset_module']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.ns_asset_management')</label>
                            </div> 
                            
                            <div class="col-md-2">
                            {!! Form::hidden('ns_vat_module', 0) !!}
                                {!! Form::checkbox('ns_vat_module', 1,
                                !empty($manage_module_enable['ns_vat_module']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.ns_vat_module')</label>
                            </div> 
                            
                            <div class="col-md-2">
                            {!! Form::hidden('ns_discount_module', 0) !!}
                                {!! Form::checkbox('ns_discount_module', 1,
                                !empty($manage_module_enable['ns_discount_module']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.ns_discount_module')</label>
                            </div> 
                            
                            
                            
                            <div class="col-md-2">
                            {!! Form::hidden('ns_dsr_module', 0) !!}
                                {!! Form::checkbox('ns_dsr_module', 1,
                                !empty($manage_module_enable['ns_dsr_module']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.ns_dsr_module')</label>
                            </div> 
                             
                        </div>
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <div class="checkbox">
                                    <label>
                                        <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-md-3">
                            {!! Form::hidden('ns_cash', 0) !!}
                                {!! Form::checkbox('ns_cash', 1,
                                !empty($manage_module_enable['ns_cash']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.cash')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_cash_deposit', 0) !!}
                                {!! Form::checkbox('ns_cash_deposit', 1,
                                !empty($manage_module_enable['ns_cash_deposit']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.cash_deposit')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_cards', 0) !!}
                                {!! Form::checkbox('ns_cards', 1,
                                !empty($manage_module_enable['ns_cards']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.cards')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_cheques', 0) !!}
                                {!! Form::checkbox('ns_cheques', 1,
                                !empty($manage_module_enable['ns_cheques']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.cheques')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_expenses', 0) !!}
                                {!! Form::checkbox('ns_expenses', 1,
                                !empty($manage_module_enable['ns_expenses']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.expneses')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_shortage', 0) !!}
                                {!! Form::checkbox('ns_shortage', 1,
                                !empty($manage_module_enable['ns_shortage']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.shortage')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_excess', 0) !!}
                                {!! Form::checkbox('ns_excess', 1,
                                !empty($manage_module_enable['ns_excess']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.excess')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_credit_sales', 0) !!}
                                {!! Form::checkbox('ns_credit_sales', 1,
                                !empty($manage_module_enable['ns_credit_sales']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.credit_sales')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_loan_payments', 0) !!}
                                {!! Form::checkbox('ns_loan_payments', 1,
                                !empty($manage_module_enable['ns_loan_payments']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.loan_payments')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_drawing_payments', 0) !!}
                                {!! Form::checkbox('ns_drawing_payments', 1,
                                !empty($manage_module_enable['ns_drawing_payments']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.drawing_payments')</label>
                            </div> 
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_customer_loans', 0) !!}
                                {!! Form::checkbox('ns_customer_loans', 1,
                                !empty($manage_module_enable['ns_customer_loans']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('superadmin::lang.settlement')-@lang('petro::lang.customer_loans')</label>
                            </div>
                            
                            <div class="col-md-3">
                            {!! Form::hidden('ns_petro_sms_notifications', 0) !!}
                                {!! Form::checkbox('ns_petro_sms_notifications', 1,
                                (!empty($manage_module_enable['ns_petro_sms_notifications'])   || !array_key_exists('ns_petro_sms_notifications',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                <label class="search_label">@lang('petro::lang.petro_sms_notifications')</label>
                            </div>
                            
                        </div>
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-3">
                                {!! Form::label('ns_font_family', __( 'superadmin::lang.ns_font_family' ) .":") !!}
                                 {!! Form::select('ns_font_family', $fonts, !empty($manage_module_enable['ns_font_family']) ?
                                    $manage_module_enable['ns_font_family'] : null, [
                                                'class' => 'form-control select2',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all'),
                                                'required'
                                            ])
                                        !!}  
                            </div>
                            
                            <div class="col-md-3">
                                {!! Form::label('ns_font_size', __( 'superadmin::lang.ns_font_size' ) .":") !!}
                                 {!! Form::text('ns_font_size', !empty($manage_module_enable['ns_font_size']) ?
                                    $manage_module_enable['ns_font_size'] : null, [
                                                'class' => 'form-control',
                                                'style' => 'width:100%',
                                                'placeholder' => __('superadmin::lang.ns_font_size')
                                            ])
                                        !!}  
                            </div>
                            <div class="form-group col-md-3">
                                {!! Form::label('ns_font_color', __( 'superadmin::lang.ns_font_color' )) !!}
                                {!! Form::text('ns-font-color-picker', null, ['class' => 'form-control ns-font-color-picker', 'id' => 'ns-font-color-picker', 'placeholder' => __( 'superadmin::lang.ns_font_color' )]);
                                !!}
                                {!! Form::hidden('ns_font_color', !empty($manage_module_enable['ns_font_color']) ?
                                    $manage_module_enable['ns_font_color'] : null, ['id' => 'ns_font_color']) !!}
                             </div>
                             
                             <div class="form-group col-md-3">
                                {!! Form::label('ns_background_color', __( 'superadmin::lang.ns_background_color' )) !!}
                                {!! Form::text('ns-background-color-picker', null, ['class' => 'form-control ns-background-color-picker', 'id' => 'ns-background-color-picker', 'placeholder' => __( 'superadmin::lang.ns_background_color' )]);
                                !!}
                                {!! Form::hidden('ns_background_color', !empty($manage_module_enable['ns_background_size']) ?
                                    $manage_module_enable['ns_background_size'] : null, ['id' => 'ns_background_color']) !!}
                             </div>
                             
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <label>@lang('superadmin::lang.message_content')</label>
                                <textarea class="form-control" rows="5" name="notsubscribed_message_content" required>{{!empty($manage_module_enable['notsubscribed_message_content']) ? $manage_module_enable['notsubscribed_message_content'] : ''}}</textarea>
                            </div>
                        </div>
                        
                      </div>
                      
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                      <div class="card-header text-center">
                        <h4> @lang('superadmin::lang.manufacturing_module')</h4>
                        <hr>
                      </div>
                      <div class="card-body">
                          <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.manufacturing_module')</label>
                                </div>
                                
                                <div class="col-md-1">
                                    <label></label>
                                    {!! Form::hidden('mf_module', 0) !!}
                                    {!! Form::hidden('mf_module', 0) !!}
                                    {!! Form::checkbox('mf_module', 1, !empty($manage_module_enable['mf_module']) ? true : false,
                                    ['class' => 'input-icheck-red ch_select mf_module']) !!}
                                </div>
                                
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['mf_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['mf_expiry_date']))
                                        @if(strtotime($module_activation_data['mf_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['mf_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('mf_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['mf_interval']) ?
                                    $module_activation_data['mf_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('mf_length', !empty($module_activation_data['mf_length']) ?
                                    $module_activation_data['mf_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('mf_activated_on', !empty($module_activation_data['mf_activated_on']) ?
                                    $module_activation_data['mf_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('mf_expiry_date', !empty($module_activation_data['mf_expiry_date']) ?
                                    $module_activation_data['mf_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('mf_price', !empty($module_activation_data['mf_price']) ?
                                    $module_activation_data['mf_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="row mf_module_locations check_group">
                                <div class="col-md-3">
                                    <p style="padding-top: 9px;"><b>@lang('superadmin::lang.select_locations'): </b></p>
                                </div>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <br>
                                @foreach ($business_locations as $location)
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                {!! Form::checkbox('module_permission_location[mf_module]['.$location->id.']', 1,
                                                !empty($module_permission_locations_value['mf_module']->locations) ?
                                                array_key_exists($location->id,
                                                $module_permission_locations_value['mf_module']->locations) : false, ['class' =>
                                                'input-icheck-red ch_select']); !!}
                                                {{$location->name}}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                     </div>
                      
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.access_account')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.access_account')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('access_account', 0) !!}
                                    {!! Form::checkbox('access_account', 1, !empty($manage_module_enable['access_account']) ? true
                                    :
                                    false, ['class' => 'input-icheck-red ch_select accounting_module'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['ac_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['ac_expiry_date']))
                                        @if(strtotime($module_activation_data['ac_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['ac_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    {!! Form::select('ac_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['ac_interval']) ?
                                    $module_activation_data['ac_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('ac_length', !empty($module_activation_data['ac_length']) ?
                                    $module_activation_data['ac_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('ac_activated_on', !empty($module_activation_data['ac_activated_on']) ?
                                    $module_activation_data['ac_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('ac_expiry_date', !empty($module_activation_data['ac_expiry_date']) ?
                                    $module_activation_data['ac_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('ac_price', !empty($module_activation_data['ac_price']) ?
                                    $module_activation_data['ac_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('fixed_assets', 1, !empty($manage_module_enable['fixed_assets']) ?
                                    $manage_module_enable['fixed_assets'] : null, ['class' => 'input-icheck-red ch_select
                                    accounting_module'])
                                    !!} <label class="search_label">@lang('superadmin::lang.fixed_assets')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('zero_previous_accounting_values', 1, !empty($manage_module_enable['zero_previous_accounting_values']) ?
                                    $manage_module_enablea['zero_previous_accounting_values'] : null, ['class' => 'input-icheck-red ch_select
                                    accounting_module'])
                                    !!} <label class="search_label">@lang('superadmin::lang.zero_previous_accounting_values')</label>
                                </div>
                                
                                <div class="col-md-4">
                                    {!! Form::checkbox('acc_no_manually', 1, !empty($manage_module_enable['acc_no_manually']) ?
                                    $manage_module_enable['acc_no_manually'] : null, ['class' => 'input-icheck-red ch_select
                                    accounting_module'])
                                    !!} <label class="search_label">@lang('superadmin::lang.acc_no_manually')</label>
                                </div>
                                
                                <div class="col-md-4">
                                    {!! Form::checkbox('edit_ob', 1, !empty($manage_module_enable['edit_ob']) ?
                                    $manage_module_enable['edit_ob'] : null, ['class' => 'input-icheck-red ch_select
                                    accounting_module'])
                                    !!} <label class="search_label">@lang('superadmin::lang.edit_opening_balance')</label>
                                </div>
                                
                                 <div class="col-md-4">
                                    {!! Form::checkbox('realize_cheque', 1, !empty($manage_module_enable['realize_cheque']) ?
                                    $manage_module_enable['realize_cheque'] : null, ['class' => 'input-icheck-red ch_select
                                    accounting_module'])
                                    !!} <label class="search_label">@lang('account.realize_cheque')</label>
                                </div>
                                
                            </div>
                            <hr>
                            <div class="row accounting_module_locations check_group">
                                <div class="col-md-3">
                                    <p style="padding-top: 9px;"><b>@lang('superadmin::lang.select_locations'): </b></p>
                                </div>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <br>
                                @foreach ($business_locations as $location)
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                {!!
                                                Form::checkbox('module_permission_location[accounting_module]['.$location->id.']',
                                                1,
                                                !empty($module_permission_locations_value['accounting_module']->locations) ?
                                                array_key_exists($location->id,
                                                $module_permission_locations_value['accounting_module']->locations) : false,
                                                ['class' => 'input-icheck-red ch_select']); !!}
                                                {{$location->name}}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="row" style="margin-bottom: 20px">
                                @if(!empty($account_nos))
                                <div class="card-body" style="margin-bottom: 10px;background-color: #ffffff;">
                                    <h5 class="mb-0 text-black">
                                        <label class="search_label">@lang('account.account_numbers')</label>
                                        <i class="fa fa-angle-down rotate-icon pull-right"></i>
                                    </h5>
                                    
                                    <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    {!! Form::label('account_type', __( 'account.account_type' ) .":") !!}
                                                </div>
                                            </div>
                                          <div class="col-md-4">  
                                            <div class="form-group">
                                              {!! Form::label('prefix', __( 'lang_v1.prefix' ) .":*") !!}
                                            </div>
                                          </div>
                                        <div class="col-md-4">
                                          <div class="form-group">
                                              {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
                                          </div>
                                        </div>
                                            
                                        </div>
                                    
                                    @foreach($account_nos as $acc_no)
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="form-group">
                                                    <select name="account_nos[{{$acc_no->id}}][account_type]" class="form-control select2" id="account_type">
                                                        <option>@lang('messages.please_select')</option>
                                                        @foreach($account_types as $account_type)
                                                        <optgroup label="{{$account_type->name}}">
                                                            <option value="{{$account_type->id}}" @if($acc_no->account_type == $account_type->id)
                                                                selected @endif >{{$account_type->name}}</option>
                                                            @foreach($account_type->sub_types as $sub_type)
                                                            <option value="{{$sub_type->id}}" @if($acc_no->account_type == $sub_type->id) selected
                                                                @endif >{{$sub_type->name}}</option>
                                                            @endforeach
                                                        </optgroup>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                          <div class="col-md-4">  
                                            <div class="form-group">
                                              {!! Form::text('account_nos['.$acc_no->id.'][prefix]', $acc_no->prefix, ['class' => 'form-control', 'required','placeholder' => __( 'lang_v1.prefix' ) ]); !!}
                                           </div>
                                          </div>
                                        <div class="col-md-4">
                                          <div class="form-group">
                                              {!! Form::text('account_nos['.$acc_no->id.'][account_number]', $acc_no->account_number, ['class' => 'form-control', 'required','placeholder' => __( 'account.account_number' ) ]); !!}
                                          </div>
                                        </div>
                                            
                                        </div>
                                    @endforeach
                                    
                                </div>
                                @endif
                                <!--Accordion wrapper-->
                                <div class="accordion md-accordion" id="accordionEx1" role="tablist"
                                     aria-multiselectable="true">

                                    <div class="card">
                                        <div class="card-header" role="tab" id="headingTwo1">
                                            <a class="collapsed" data-toggle="collapse" data-parent="#accordionEx1"
                                               href="#collapseTwo1" aria-expanded="false" aria-controls="collapseTwo1">
                                                <h5 class="mb-0 text-black">
                                                    <label class="search_label">@lang('superadmin::lang.manage_accounts')</label>
                                                    <i class="fa fa-angle-down rotate-icon pull-right"></i>
                                                </h5>
                                            </a>
                                        </div>
                                        <div id="collapseTwo1" class="collapse" role="tabpanel" aria-labelledby="headingTwo1"
                                             data-parent="#accordionEx1">
                                            <div class="card-body" style="margin-bottom: 10px;">
                                                <hr>
                                                <div class="row">
                                                    <div class="col-md-12">
                                                        <div class="checkbox">
                                                            <label>
                                                                <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                                            </label>
                                                        </div>
                                                    </div>
                                                
                                                @foreach ($accounts as $account)
                                                    <div class="col-md-6">
                                                        <div class="checkbox">
                                                            <label>
                                                                {!!
                                                                Form::checkbox('accounts_enabled['.$account->id.']', 1,
                                                                $account->visible,['class' => 'input-icheck-red ch_select']); !!}
                                                                {{$account->name}}
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                                </div>
                                                <hr>
                                            </div>
                                        </div>

                                    </div>

                                </div>
                            </div>
                         </div>
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.post_dated_cheque')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                            <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('account.post_dated_cheque')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('post_dated_cheque', 0) !!}
                                    {!! Form::checkbox('post_dated_cheque', 1, !empty($manage_module_enable['post_dated_cheque']) ?
                                            $manage_module_enable['post_dated_cheque'] : null, ['class' => 'input-icheck-red ch_select
                                            accounting_module'])
                                            !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['post_dated_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['post_dated_expiry_date']))
                                        @if(strtotime($module_activation_data['post_dated_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['post_dated_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    {!! Form::select('post_dated_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['post_dated_interval']) ?
                                    $module_activation_data['post_dated_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('post_dated_length', !empty($module_activation_data['post_dated_length']) ?
                                    $module_activation_data['post_dated_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('post_dated_activated_on', !empty($module_activation_data['post_dated_activated_on']) ?
                                    $module_activation_data['post_dated_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('post_dated_expiry_date', !empty($module_activation_data['post_dated_expiry_date']) ?
                                    $module_activation_data['post_dated_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('post_dated_price', !empty($module_activation_data['post_dated_price']) ?
                                    $module_activation_data['post_dated_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                              
                            <div class="check_group">
                                <div class="row">
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">{{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                        
                                        <div class="clearfix"></div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('add_pd_cheque', 0) !!}
                                            {!! Form::checkbox('add_pd_cheque', 1, !empty($manage_module_enable['add_pd_cheque']) ?
                                            $manage_module_enable['add_pd_cheque'] : null, ['class' => 'input-icheck-red ch_select
                                            accounting_module'])
                                            !!} <label class="search_label">@lang('account.add_pd_cheques')</label>
                                        </div>
                                       
                                        <div class="col-md-4">
                                            {!! Form::checkbox('show_post_dated_cheque', 1, !empty($manage_module_enable['show_post_dated_cheque']) ?
                                            $manage_module_enable['show_post_dated_cheque'] : null, ['class' => 'input-icheck-red ch_select
                                            accounting_module'])
                                            !!} <label class="search_label">@lang('account.show_post_dated_cheque')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('update_post_dated_cheque', 1,
                                            !empty($manage_module_enable['update_post_dated_cheque']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.update_post_dated_cheque')</label>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                           
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('deposits.deposits_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                            <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('deposits.deposits_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('deposits_module', 0) !!}
                                    {!! Form::checkbox('deposits_module', 1, !empty($manage_module_enable['deposits_module']) ? true
                                    :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['deposits_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['deposits_expiry_date']))
                                        @if(strtotime($module_activation_data['deposits_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['deposits_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    {!! Form::select('deposits_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['deposits_interval']) ?
                                    $module_activation_data['deposits_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('deposits_length', !empty($module_activation_data['deposits_length']) ?
                                    $module_activation_data['deposits_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('deposits_activated_on', !empty($module_activation_data['deposits_activated_on']) ?
                                    $module_activation_data['deposits_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('deposits_expiry_date', !empty($module_activation_data['deposits_expiry_date']) ?
                                    $module_activation_data['deposits_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('deposits_price', !empty($module_activation_data['deposits_price']) ?
                                    $module_activation_data['deposits_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                         </div>
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                              <div class="card-header text-center">
                                <h4> @lang('superadmin::lang.enable_restaurant')</h4>
                                <hr>
                              </div>
                              <div class="card-body">
                                  <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.enable_restaurant')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('enable_restaurant', 0) !!}
                                    {!! Form::checkbox('enable_restaurant', 1, !empty($manage_module_enable['enable_restaurant'])
                                    ?
                                    true : false, ['class' => 'input-icheck-red
                                    ch_select restaurant_module']) !!}
                                </div>
                                <div class="col-md-1">
                                        @if(empty($module_activation_data['restaurant_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['restaurant_expiry_date']))
                                        @if(strtotime($module_activation_data['restaurant_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['restaurant_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                    </div> 
                                    
                                    <div class="col-md-1">
                                        {!! Form::select('restaurant_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['restaurant_interval']) ?
                                        $module_activation_data['restaurant_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('restaurant_length', !empty($module_activation_data['restaurant_length']) ?
                                        $module_activation_data['restaurant_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                    
                                    <div class="col-md-2">
                                        {!! Form::date('restaurant_activated_on', !empty($module_activation_data['restaurant_activated_on']) ?
                                        $module_activation_data['restaurant_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::date('restaurant_expiry_date', !empty($module_activation_data['restaurant_expiry_date']) ?
                                        $module_activation_data['restaurant_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::text('restaurant_price', !empty($module_activation_data['restaurant_price']) ?
                                        $module_activation_data['restaurant_price'] : null, ['class' => 'form-control']) !!}
                                    </div>
                                
                            </div>
                            
                            <div class="row restaurant_module_locations check_group">
                                <div class="col-md-3">
                                    <p style="padding-top: 9px;"><b>@lang('superadmin::lang.select_locations'): </b></p>
                                </div>
                                <div class="col-md-8">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <br>
                                @foreach ($business_locations as $location)
                                    <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                {!!
                                                Form::checkbox('module_permission_location[restaurant_module]['.$location->id.']',
                                                1,
                                                !empty($module_permission_locations_value['restaurant_module']->locations) ?
                                                array_key_exists($location->id,
                                                $module_permission_locations_value['restaurant_module']->locations) : false,
                                                ['class' => 'input-icheck-red ch_select']); !!}
                                                {{$location->name}}
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <hr>
                            <div class="row">
                                
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="search_label">@lang('superadmin::lang.enable_booking')</label>
                                </div>
                                <div class="col-md-3">
                                {!! Form::hidden('enable_booking', 0) !!}
                                    {!! Form::checkbox('enable_booking', 1, !empty($manage_module_enable['enable_booking']) ? true
                                    :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                
                                <div class="col-md-3">
                                    <label class="search_label">@lang('superadmin::lang.orders')</label>
                                </div>
                                <div class="col-md-3">
                                {!! Form::hidden('orders', 0) !!}
                                    {!! Form::checkbox('orders', 1,
                                    !empty($manage_module_enable['orders']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                            </div>
                            <hr>
                            
                             </div>
                              
                        </div>
                        
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.vat_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              
                            <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.vat_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('vat_module', 0) !!}
                                    {!! Form::checkbox('vat_module', 1,
                                            !empty($manage_module_enable['vat_module']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['vat_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['vat_expiry_date']))
                                        @if(strtotime($module_activation_data['vat_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['vat_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    {!! Form::select('vat_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['vat_interval']) ?
                                    $module_activation_data['vat_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('vat_length', !empty($module_activation_data['vat_length']) ?
                                    $module_activation_data['vat_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('vat_activated_on', !empty($module_activation_data['vat_activated_on']) ?
                                    $module_activation_data['vat_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('vat_expiry_date', !empty($module_activation_data['vat_expiry_date']) ?
                                    $module_activation_data['vat_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('vat_price', !empty($module_activation_data['vat_price']) ?
                                    $module_activation_data['vat_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                              
                            <div class="check_group">
                                <div class="row">
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">{{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                        
                                        <div class="clearfix"></div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('vat_credit_bill', 0) !!}
                                            {!! Form::checkbox('vat_credit_bill', 1,
                                                !empty($manage_module_enable['vat_credit_bill']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                            <label class="search_label">@lang('superadmin::lang.vat_credit_bill')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('vat_sale', 0) !!}
                                            {!! Form::checkbox('vat_sale', 1,
                                            !empty($manage_module_enable['vat_sale']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.vat_sale')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('list_vat_sale', 0) !!}
                                            {!! Form::checkbox('list_vat_sale', 1,
                                            !empty($manage_module_enable['list_vat_sale']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.list_vat_sale')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('vat_purchase', 0) !!}
                                            {!! Form::checkbox('vat_purchase', 1,
                                            !empty($manage_module_enable['vat_purchase']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.vat_purchase')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('list_vat_purchase', 0) !!}
                                            {!! Form::checkbox('list_vat_purchase', 1,
                                            !empty($manage_module_enable['list_vat_purchase']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.list_vat_purchase')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('vat_expense', 0) !!}
                                            {!! Form::checkbox('vat_expense', 1,
                                            !empty($manage_module_enable['vat_expense']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.vat_expense')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('list_vat_expense', 0) !!}
                                            {!! Form::checkbox('list_vat_expense', 1,
                                            !empty($manage_module_enable['list_vat_expense']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.list_vat_expense')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('vat_products', 0) !!}
                                            {!! Form::checkbox('vat_products', 1,
                                            !empty($manage_module_enable['vat_products']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.vat_products')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('vat_contacts', 0) !!}
                                            {!! Form::checkbox('vat_contacts', 1,
                                            !empty($manage_module_enable['vat_contacts']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.vat_contacts')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('vat_meter_sales', 0) !!}
                                            {!! Form::checkbox('vat_meter_sales', 1,
                                            !empty($manage_module_enable['vat_meter_sales']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.vat_meter_sales')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('customized_vat_invoices', 0) !!}
                                            {!! Form::checkbox('customized_vat_invoices', 1,
                                            !empty($manage_module_enable['customized_vat_invoices']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('vat::lang.customized_vat_invoices')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('fleet_vat_invoice2', 0) !!}
                                            {!! Form::checkbox('fleet_vat_invoice2', 1,
                                            !empty($manage_module_enable['fleet_vat_invoice2']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('vat::lang.fleet_vat_invoice')</label>
                                        </div>
                                        
                                         <div class="col-md-4">
                                         {!! Form::hidden('vat_linked_accounts', 0) !!}
                                            {!! Form::checkbox('vat_linked_accounts', 1,
                                            !empty($manage_module_enable['vat_linked_accounts']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('vat::lang.vat_payable_to')</label>
                                        </div>
                                        
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('vat_delete_customer_statement', 0) !!}
                                            {!! Form::checkbox('vat_delete_customer_statement', 1,
                                            (!empty($manage_module_enable['vat_delete_customer_statement'])  || !array_key_exists('vat_delete_customer_statement',$manage_module_enable)) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('lang_v1.vat.delete_customer_statement')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('vat_delete_statement_payment', 0) !!}
                                            {!! Form::checkbox('vat_delete_statement_payment', 1,
                                            (!empty($manage_module_enable['vat_delete_statement_payment'])  || !array_key_exists('vat_delete_statement_payment',$manage_module_enable)) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('lang_v1.vat.delete_statement_payment')</label>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                           
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.bakery_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              
                            <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.bakery_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('bakery_module', 0) !!}
                                    {!! Form::checkbox('bakery_module', 1,
                                            !empty($manage_module_enable['bakery_module']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['bakery_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['bakery_expiry_date']))
                                        @if(strtotime($module_activation_data['bakery_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['bakery_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    {!! Form::select('bakery_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['bakery_interval']) ?
                                    $module_activation_data['bakery_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('bakery_length', !empty($module_activation_data['bakery_length']) ?
                                    $module_activation_data['bakery_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('bakery_activated_on', !empty($module_activation_data['bakery_activated_on']) ?
                                    $module_activation_data['bakery_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('bakery_expiry_date', !empty($module_activation_data['bakery_expiry_date']) ?
                                    $module_activation_data['bakery_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('bakery_price', !empty($module_activation_data['bakery_price']) ?
                                    $module_activation_data['bakery_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            
                            <div class="check_group">
                                <div class="row">
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">{{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                        
                                        <div class="clearfix"></div>
                                      
                                        <div class="col-md-4">
                                        {!! Form::hidden('bakery_drivers', 0) !!}
                                            {!! Form::checkbox('bakery_drivers', 1,
                                            !empty($manage_module_enable['bakery_drivers']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.bakery_drivers')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('bakery_vehicles', 0) !!}
                                            {!! Form::checkbox('bakery_vehicles', 1,
                                            !empty($manage_module_enable['bakery_vehicles']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.bakery_vehicles')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('bakery_products', 0) !!}
                                            {!! Form::checkbox('bakery_products', 1,
                                            !empty($manage_module_enable['bakery_products']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.bakery_products')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('bakery_starting_no', 0) !!}
                                            {!! Form::checkbox('bakery_starting_no', 1,
                                            !empty($manage_module_enable['bakery_starting_no']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.bakery_starting_no')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                        {!! Form::hidden('bakery_list_products', 0) !!}
                                            {!! Form::checkbox('bakery_list_products', 1,
                                            !empty($manage_module_enable['bakery_list_products']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.bakery_list_products')</label>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                           
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.um_roles')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                            <div class="check_group">
                                <div class="row">
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">{{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                        
                                        <div class="clearfix"></div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('um_add_role', 1,
                                            !empty($manage_module_enable['um_add_role']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.um_add_role')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('um_edit_role', 1,
                                            !empty($manage_module_enable['um_edit_role']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.um_edit_role')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('um_delete_role', 1,
                                            !empty($manage_module_enable['um_delete_role']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.um_delete_role')</label>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                           
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('report.aging_report_total')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                            <div class="check_group">
                                <div class="row">
                                    <div class="check_group">
                                        <div class="col-md-3">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">{{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                        
                                        <div class="clearfix"></div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('1_7_days', 1,
                                            !empty($manage_module_enable['1_7_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.1_7_days')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('8_14_days', 1,
                                            !empty($manage_module_enable['8_14_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.8_14_days')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('15_21_days', 1,
                                            !empty($manage_module_enable['15_21_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.15_21_days')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('22_30_days', 1,
                                            !empty($manage_module_enable['22_30_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.22_30_days')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('over_30_days', 1,
                                            !empty($manage_module_enable['over_30_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.over_30_days')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('1_30_days', 1,
                                            !empty($manage_module_enable['1_30_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.1_30_days')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('31_45_days', 1,
                                            !empty($manage_module_enable['31_45_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.31_45_days')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('46_60_days', 1,
                                            !empty($manage_module_enable['46_60_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.46_60_days')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('61_90_days', 1,
                                            !empty($manage_module_enable['61_90_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.61_90_days')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('over_90_days', 1,
                                            !empty($manage_module_enable['over_90_days']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('report.over_90_days')</label>
                                        </div>
                                        
                                        
                                    </div>
                                </div>
                            </div>
                           
                         </div>
                          
                    </div>
                    
                        
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.hr_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="check_group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="search_label">@lang('superadmin::lang.hr_module')</label>
                                    </div>
                                    
                                    <div class="col-md-1">
                                    {!! Form::hidden('hr_module', 0) !!}
                                        {!! Form::checkbox('hr_module', 1, !empty($manage_module_enable['hr_module']) ? true :
                                        false,
                                        ['class' => 'input-icheck-red ch_select hr_module']) !!}
                                    </div>
                                    <div class="col-md-1">
                                        @if(empty($module_activation_data['hr_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['hr_expiry_date']))
                                        @if(strtotime($module_activation_data['hr_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['hr_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::select('hr_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['hr_interval']) ?
                                        $module_activation_data['hr_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('hr_length', !empty($module_activation_data['hr_length']) ?
                                        $module_activation_data['hr_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                    
                                    <div class="col-md-2">
                                        {!! Form::date('hr_activated_on', !empty($module_activation_data['hr_activated_on']) ?
                                        $module_activation_data['hr_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::date('hr_expiry_date', !empty($module_activation_data['hr_expiry_date']) ?
                                        $module_activation_data['hr_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::text('hr_price', !empty($module_activation_data['hr_price']) ?
                                        $module_activation_data['hr_price'] : null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="check_group">
                                        <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">{{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('leave_request', 1,
                                            !empty($manage_module_enable['leave_request']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.leave_request')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('attendance', 1,
                                            !empty($manage_module_enable['attendance']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.attendance')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('payroll', 1,
                                            !empty($manage_module_enable['payroll']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.payroll')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hr_settings', 1,
                                            !empty($manage_module_enable['hr_settings']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hr_settings')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('work_shift', 1,
                                            !empty($manage_module_enable['work_shift']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('essentials::lang.work_shift')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('holidays', 1,
                                            !empty($manage_module_enable['holidays']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.holidays')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('leave_type', 1,
                                            !empty($manage_module_enable['leave_type']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.leave_type')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('allowance_deduction', 1,
                                            !empty($manage_module_enable['allowance_deduction']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('essentials::lang.allowance_and_deduction')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hrm_ledger', 1,
                                            !empty($manage_module_enable['hrm_ledger']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hrm_ledger')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hrm_dashboard', 1,
                                            !empty($manage_module_enable['hrm_dashboard']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hrm_dashboard')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hrm_leave', 1,
                                            !empty($manage_module_enable['hrm_leave']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hrm_leave')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hrm_sales_target', 1,
                                            !empty($manage_module_enable['hrm_sales_target']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hrm_sales_target')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hrm_settings', 1,
                                            !empty($manage_module_enable['hrm_settings']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hrm_settings')</label>
                                        </div>
                                        
                                        <div class="col-md-4">
                                            {!! Form::checkbox('hrm_salary_details', 1,
                                            !empty($manage_module_enable['hrm_salary_details']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('superadmin::lang.hrm_salary_details')</label>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                           
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('essentials::lang.essentials')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="check_group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="search_label">@lang('essentials::lang.essentials')</label>
                                    </div>
                                    
                                    <div class="col-md-1">
                                    {!! Form::hidden('essentials_module', 0) !!}
                                        {!! Form::checkbox('essentials_module', 1, !empty($manage_module_enable['essentials_module']) ? true :
                                        false,
                                        ['class' => 'input-icheck-red ch_select essentials_module']) !!}
                                    </div>
                                    <div class="col-md-1">
                                        @if(empty($module_activation_data['essentials_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['essentials_expiry_date']))
                                        @if(strtotime($module_activation_data['essentials_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['essentials_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::select('essentials_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['essentials_interval']) ?
                                        $module_activation_data['essentials_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('essentials_length', !empty($module_activation_data['essentials_length']) ?
                                        $module_activation_data['essentials_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                    
                                    <div class="col-md-2">
                                        {!! Form::date('essentials_activated_on', !empty($module_activation_data['essentials_activated_on']) ?
                                        $module_activation_data['essentials_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::date('essentials_expiry_date', !empty($module_activation_data['essentials_expiry_date']) ?
                                        $module_activation_data['essentials_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::text('essentials_price', !empty($module_activation_data['essentials_price']) ?
                                        $module_activation_data['essentials_price'] : null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <hr>
                                <div class="row">
                                    <div class="check_group">
                                        <div class="col-md-3">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">{{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('essentials_todo', 1,
                                            !empty($manage_module_enable['essentials_todo']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('essentials::lang.todo')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('essentials_document', 1,
                                            !empty($manage_module_enable['essentials_document']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('essentials::lang.document')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('essentials_memos', 1,
                                            !empty($manage_module_enable['essentials_memos']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('essentials::lang.memos')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('essentials_reminders', 1,
                                            !empty($manage_module_enable['essentials_reminders']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('essentials::lang.reminders')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('essentials_messages', 1,
                                            !empty($manage_module_enable['essentials_messages']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('essentials::lang.messages')</label>
                                        </div>
                                        <div class="col-md-4">
                                            {!! Form::checkbox('essentials_settings', 1,
                                            !empty($manage_module_enable['essentials_settings']) ? true : false, ['class' =>
                                            'input-icheck-red
                                            ch_select']) !!}<label
                                                    class="search_label">@lang('business.settings')</label>
                                        </div>
                                        
                                    </div>
                                </div>
                            </div>
                           
                         </div>
                          
                    </div>
                    
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.visitors_registration_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="check_group">
                                <div class="row">
                                    <div class="col-md-2">
                                        <label
                                                class="search_label">@lang('superadmin::lang.visitors_registration_module')</label>
                                    </div>
                                    
                                    <div class="col-md-1">
                                    {!! Form::hidden('visitors_registration_module', 0) !!}
                                        {!! Form::checkbox('visitors_registration_module', 1,
                                        !empty($manage_module_enable['visitors_registration_module']) ? true :
                                        false,
                                        ['class' => 'input-icheck-red ch_select visitors_registration_module']) !!}
                                    </div>
                                        <div class="col-md-1">
                                            @if(empty($module_activation_data['vreg_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['vreg_expiry_date']))
                                        @if(strtotime($module_activation_data['vreg_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['vreg_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                        </div>
                                        
                                        <div class="col-md-1">
                                        {!! Form::select('vreg_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['vreg_interval']) ?
                                        $module_activation_data['vreg_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('vreg_length', !empty($module_activation_data['vreg_length']) ?
                                        $module_activation_data['vreg_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                        
                                        <div class="col-md-2">
                                            {!! Form::date('vreg_activated_on', !empty($module_activation_data['vreg_activated_on']) ?
                                            $module_activation_data['vreg_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::date('vreg_expiry_date', !empty($module_activation_data['vreg_expiry_date']) ?
                                            $module_activation_data['vreg_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::text('vreg_price', !empty($module_activation_data['vreg_price']) ?
                                            $module_activation_data['vreg_price'] : null, ['class' => 'form-control']) !!}
                                        </div>
                                </div>
                                <hr>
                                
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">
                                                {{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors', 1,
                                        !empty($manage_module_enable['visitors']) ? true : false, ['class' =>
                                        'input-icheck-red
                                        ch_select']) !!}<label class="search_label">@lang('superadmin::lang.visitors')</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors_registration', 1,
                                        !empty($manage_module_enable['visitors_registration']) ? true : false, ['class' =>
                                        'input-icheck-red
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.visitors_registration')</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors_registration_setting', 1,
                                        !empty($manage_module_enable['visitors_registration_setting']) ? true : false, ['class' =>
                                        'input-icheck-red
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.visitors_registration_setting')</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors_district', 1,
                                        !empty($manage_module_enable['visitors_district']) ? true : false, ['class' =>
                                        'input-icheck-red
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.visitors_district')</label>
                                    </div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('visitors_town', 1,
                                        !empty($manage_module_enable['visitors_town']) ? true : false, ['class' =>
                                        'input-icheck-red
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.visitors_town')</label>
                                    </div>
                                    <div class="clearfix"></div>
                                    <div class="col-md-4">
                                        {!! Form::checkbox('disable_all_other_module_vr', 1,
                                        !empty($manage_module_enable['disable_all_other_module_vr']) ? true : false, ['class' =>
                                        'input-icheck-red
                                        ch_select']) !!}<label
                                                class="search_label">@lang('superadmin::lang.disable_all_other_module_vr')</label>
                                    </div>

                                </div>
                            </div>
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.enable_petro_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.enable_petro_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('enable_petro_module', 0) !!}
                                    {!! Form::checkbox('enable_petro_module', 1,
                                    !empty($manage_module_enable['enable_petro_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['petro_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['petro_expiry_date']))
                                        @if(strtotime($module_activation_data['petro_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['petro_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('petro_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['petro_interval']) ?
                                        $module_activation_data['petro_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('petro_length', !empty($module_activation_data['petro_length']) ?
                                        $module_activation_data['petro_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                
                                <div class="col-md-2">
                                    {!! Form::date('petro_activated_on', !empty($module_activation_data['petro_activated_on']) ?
                                    $module_activation_data['petro_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('petro_expiry_date', !empty($module_activation_data['petro_expiry_date']) ?
                                    $module_activation_data['petro_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('petro_price', !empty($module_activation_data['petro_price']) ?
                                    $module_activation_data['petro_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>   
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label" class="flex-label">
                                           {!! Form::checkbox('petro_settlement', 1, 
                                                (isset($manage_module_enable['petro_settlement']) ? !empty($manage_module_enable['petro_settlement']) : true), 
                                                ['class' => 'input-icheck-red ch_select', 'id'=>'petro_settlement']
                                            ); !!}
                                            {{__('petro::lang.petro_settlement')}}
                                        </label>
                                    </div>
                                </div>
                               
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label" class="flex-label">
                                            {!! Form::checkbox('petro_sms_notifications', 1, !empty($manage_module_enable['petro_sms_notifications']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'petro_sms_notifications']); !!}
                                            {{__('petro::lang.petro_sms_notifications')}}
                                        </label>
                                    </div>
                                </div>
                                    
                                    
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label" class="flex-label">
                                            {!! Form::checkbox('edit_settlement_date', 1, !empty($manage_module_enable['edit_settlement_date']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'edit_settlement_date']); !!}
                                            {{__('superadmin::lang.edit_settlement_date')}}
                                        </label>
                                    </div>
                                </div>
                               
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label" class="flex-label">
                                            {!! Form::checkbox('rename_cash_tab', 1, !empty($manage_module_enable['rename_cash_tab']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'rename_cash_tab']); !!}
                                            {{__('superadmin::lang.rename_cash_tab')}}
                                        </label>
                                    </div>
                                </div>
                                
                                 <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label" class="flex-label">
                                            {!! Form::checkbox('only_walkin', 1, !empty($manage_module_enable['only_walkin']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'only_walkin']); !!}
                                            {{__('superadmin::lang.only_walkin')}}
                                        </label>
                                    </div>
                                </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('petro_daily_status', 1, !empty($manage_module_enable['petro_daily_status']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'petro_daily_status']); !!}
                                        {{__('superadmin::lang.petro_daily_status')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('tank_transfer', 1, !empty($manage_module_enable['tank_transfer']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'tank_transfer']); !!}
                                        {{__('superadmin::lang.tank_transfer')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('petro_dashboard', 1, !empty($manage_module_enable['petro_dashboard']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'petro_dashboard']); !!}
                                        {{__('superadmin::lang.petro_dashboard')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('petro_task_management', 1, !empty($manage_module_enable['petro_task_management']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'petro_task_management']); !!}
                                        {{__('superadmin::lang.petro_task_management')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('pump_management', 1, !empty($manage_module_enable['pump_management']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'pump_management']); !!}
                                        {{__('superadmin::lang.pump_management_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('pump_management_testing', 1, !empty($manage_module_enable['pump_management_testing']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'pump_management_testing']); !!}
                                        {{__('superadmin::lang.pump_management_testing_page')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('meter_resetting', 1, !empty($manage_module_enable['meter_resetting']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'meter_resetting']); !!}
                                        {{__('superadmin::lang.meter_resetting_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('meter_reading', 1, !empty($manage_module_enable['meter_reading']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'meter_reading']); !!}
                                        {{__('superadmin::lang.meter_reading_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('pump_dashboard_opening', 1, !empty($manage_module_enable['pump_dashboard_opening']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'pump_dashboard_opening']); !!}
                                        {{__('superadmin::lang.pump_dashboard_opening_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('pumper_management', 1, !empty($manage_module_enable['pumper_management']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'pumper_management']); !!}
                                        {{__('superadmin::lang.pump_management_sub_menu_page')}}
                                    </label>
                                </div>
                            </div>
                            
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('daily_collection', 1, !empty($manage_module_enable['daily_collection']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'daily_collection']); !!}
                                        {{__('superadmin::lang.daily_collection_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('settlement', 1, !empty($manage_module_enable['settlement']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'settlement']); !!}
                                        {{__('superadmin::lang.settlement_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('list_settlement', 1, !empty($manage_module_enable['list_settlement']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'list_settlement']); !!}
                                        {{__('superadmin::lang.list_settlement_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('delete_settlement', 1, !empty($manage_module_enable['delete_settlement']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'delete_settlement']); !!}
                                        {{__('superadmin::lang.delete_settlement_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('dip_management', 1, !empty($manage_module_enable['dip_management']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'dip_management']); !!}
                                        {{__('superadmin::lang.dip_management_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('fuel_tanks_edit', 1, !empty($manage_module_enable['fuel_tanks_edit']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'fuel_tanks_edit']); !!}
                                        {{__('superadmin::lang.fuel_tank_edit')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('fuel_tanks_delete', 1, !empty($manage_module_enable['fuel_tanks_delete']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'fuel_tanks_delete']); !!}
                                        {{__('superadmin::lang.fuel_tank_delete')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('pumps_edit', 1, !empty($manage_module_enable['pumps_edit']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'pump_edit']); !!}
                                        {{__('superadmin::lang.pump_edit')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('pumps_delete', 1, !empty($manage_module_enable['pumps_delete']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'pump_delete']); !!}
                                        {{__('superadmin::lang.pump_delete')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                    {!! Form::checkbox('pay_excess_commission', 1,
                                    !empty($manage_module_enable['pay_excess_commission']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.pay_excess_commission')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('recover_shortage', 1,
                                    !empty($manage_module_enable['recover_shortage']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.recover_shortage')</label>
                                </div>
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('pump_operator_ledger', 1,
                                    !empty($manage_module_enable['pump_operator_ledger']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.pump_operator_ledger')</label>
                                </div>
                                
                                
    

                               
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('commission_type', 1,
                                    !empty($manage_module_enable['commission_type']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.commission_type')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('select_pump_operator_in_settlement', 1,
                                    !empty($manage_module_enable['select_pump_operator_in_settlement']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.select_pump_operator_in_settlement')</label>
                                </div>
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('edit_settlement', 1,
                                    !empty($manage_module_enable['edit_settlement']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.edit_settlement')</label>
                                </div>
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('dip_resetting', 1,
                                    !empty($manage_module_enable['dip_resetting']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.dip_resetting')</label>
                                </div>
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('delete_settlement', 1,
                                    !empty($manage_module_enable['delete_settlement']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.delete_settlement_sub_menu')</label>
                                </div>
                                
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('tank_dip_chart', 1,
                                    !empty($manage_module_enable['tank_dip_chart']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                    <label class="search_label">@lang('superadmin::lang.tank_dip_chart')</label>
                                </div>
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('edit_settlement_no_change', 1,
                                    !empty($manage_module_enable['edit_settlement_no_change']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                    <label class="search_label">@lang('petro::lang.edit_no_change')</label>
                                </div>
                                
                        </div>
                       
                        
                        <div class="row">
                            <div class="col-sm-3 product_count">
                                    <div class="form-group">
                                        {!! Form::label('allowed_tanks', __('superadmin::lang.allowed_tanks').':') !!}
                                        {!! Form::number('allowed_tanks', !empty($manage_module_enable['allowed_tanks']) ? $manage_module_enable['allowed_tanks']
                                        : $previous_package_data['allowed_tanks'], ['class' => 'form-control', 'required', 'min' =>
                                        0]); !!}
    
                                        <span class="help-block">
                                        @lang('superadmin::lang.infinite_help')
                                    </span>
                                    </div>
                                </div>
                                
                            <div class="col-md-3 text-danger text-right">
                                <br>{!! Form::label('number_of_pumps', __('superadmin::lang.number_of_pumps'). ':', ['class' =>
                                'search_label']) !!}
                            </div>
                            @foreach ($business_locations as $location)
                                <div class="col-md-3">
                                    {!! Form::label('location_pumps', $location->name) !!}
                                    {!! Form::text('module_permission_location[number_of_pumps]['.$location->id.']',
                                    !empty($module_permission_locations_value['number_of_pumps']->locations[$location->id]) ?
                                    $module_permission_locations_value['number_of_pumps']->locations[$location->id] : null,
                                    ['class' => 'form-control']) !!}
                                </div>
                            @endforeach
                           
                        </div>
                            
                            
                           
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.tasks_management')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.tasks_management')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('tasks_management', 0) !!}
                                    {!! Form::checkbox('tasks_management', 1,
                                    !empty($manage_module_enable['tasks_management']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['tasks_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['tasks_expiry_date']))
                                        @if(strtotime($module_activation_data['tasks_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['tasks_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('tasks_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['tasks_interval']) ?
                                    $module_activation_data['tasks_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('tasks_length', !empty($module_activation_data['tasks_length']) ?
                                    $module_activation_data['tasks_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('tasks_activated_on', !empty($module_activation_data['tasks_activated_on']) ?
                                    $module_activation_data['tasks_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('tasks_expiry_date', !empty($module_activation_data['tasks_expiry_date']) ?
                                    $module_activation_data['tasks_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('tasks_price', !empty($module_activation_data['tasks_price']) ?
                                    $module_activation_data['tasks_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                                
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('notes_page', 1,
                                    !empty($manage_module_enable['notes_page']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.notes_page')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('tasks_page', 1,
                                    !empty($manage_module_enable['tasks_page']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.tasks_page')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('reminder_page', 1,
                                    !empty($manage_module_enable['reminder_page']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.reminder_page')</label>
                                </div>
                            </div>
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.repair_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.repair_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('repair_module', 0) !!}
                                    {!! Form::checkbox('repair_module', 1,
                                    !empty($manage_module_enable['repair_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['repair_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['repair_expiry_date']))
                                        @if(strtotime($module_activation_data['repair_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['repair_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('repair_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['repair_interval']) ?
                                        $module_activation_data['repair_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('repair_length', !empty($module_activation_data['repair_length']) ?
                                        $module_activation_data['repair_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('repair_activated_on', !empty($module_activation_data['repair_activated_on']) ?
                                    $module_activation_data['repair_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('repair_expiry_date', !empty($module_activation_data['repair_expiry_date']) ?
                                    $module_activation_data['repair_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('repair_price', !empty($module_activation_data['repair_price']) ?
                                    $module_activation_data['repair_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    {!! Form::checkbox('job_sheets', 1,
                                    !empty($manage_module_enable['job_sheets']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.job_sheets')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('add_job_sheet', 1,
                                    !empty($manage_module_enable['add_job_sheet']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.add_job_sheet')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('list_invoice', 1,
                                    !empty($manage_module_enable['list_invoice']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_invoice')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('add_invoice', 1,
                                    !empty($manage_module_enable['add_invoice']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.add_invoice')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('brands', 1,
                                    !empty($manage_module_enable['brands']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.brands')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('repair_settings', 1,
                                    !empty($manage_module_enable['repair_settings']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.repair_settings')</label>
                                </div>
                            </div>
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.fleet_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.fleet_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('fleet_module', 0) !!}
                                    {!! Form::checkbox('fleet_module', 1,
                                    !empty($manage_module_enable['fleet_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['fleet_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['fleet_expiry_date']))
                                        @if(strtotime($module_activation_data['fleet_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['fleet_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('fleet_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['fleet_interval']) ?
                                        $module_activation_data['fleet_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('fleet_length', !empty($module_activation_data['fleet_length']) ?
                                        $module_activation_data['fleet_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('fleet_activated_on', !empty($module_activation_data['fleet_activated_on']) ?
                                    $module_activation_data['fleet_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('fleet_expiry_date', !empty($module_activation_data['fleet_expiry_date']) ?
                                    $module_activation_data['fleet_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('fleet_price', !empty($module_activation_data['fleet_price']) ?
                                    $module_activation_data['fleet_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                
                                 <div class="col-md-4">
                                    {!! Form::checkbox('fleet_settings', 1,
                                    !empty($manage_module_enable['fleet_settings']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.fleet_settings')</label>
                                </div>
                                
                                 <div class="col-md-4">
                                    {!! Form::checkbox('add_trip_operations', 1,
                                    !empty($manage_module_enable['add_trip_operations']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.add_trip_operations')</label>
                                </div>
                                
                                 <div class="col-md-4">
                                    {!! Form::checkbox('list_fleet', 1,
                                    !empty($manage_module_enable['list_fleet']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.list_fleet')</label>
                                </div>
                                
                                 <div class="col-md-4">
                                    {!! Form::checkbox('milage_changes', 1,
                                    !empty($manage_module_enable['milage_changes']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.milage_changes')</label>
                                </div>
                                
                                 <div class="col-md-4">
                                    {!! Form::checkbox('list_trip_operations', 1,
                                    !empty($manage_module_enable['list_trip_operations']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.list_trip_operations')</label>
                                </div>
                                
                                 <div class="col-md-4">
                                    {!! Form::checkbox('fleet_invoices', 1,
                                    !empty($manage_module_enable['fleet_invoices']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.fleet_invoices')</label>
                                </div>
                                
                                 <div class="col-md-4">
                                    {!! Form::checkbox('fuel_management', 1,
                                    !empty($manage_module_enable['fuel_management']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.fuel_management')</label>
                                </div>
                                
                                 <div class="col-md-4">
                                    {!! Form::checkbox('fleet_p_l', 1,
                                    !empty($manage_module_enable['fleet_p_l']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.fleet_p_l')</label>
                                </div>
                                
                                
                                <div class="col-sm-3 product_count">
                                    <div class="form-group">
                                        {!! Form::label('vehicle_count', __('superadmin::lang.no_of_vehicles').':') !!}
                                        {!! Form::number('vehicle_count', !empty($manage_module_enable['vehicle_count']) ? $manage_module_enable['vehicle_count']
                                        : $previous_package_data['vehicle_count'], ['class' => 'form-control', 'required', 'min' =>
                                        0]); !!}
    
                                        <span class="help-block">
                                        @lang('superadmin::lang.infinite_help')
                                    </span>
                                    </div>
                                </div>
                            </div>
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.mpcs_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.mpcs_module')</label>
                                </div>
                                <div class="col-md-1">
                               
                                    {!! Form::hidden('mpcs_module', 0) !!} <!-- Hidden input to ensure unchecked state is sent -->
                                    {!! Form::checkbox('mpcs_module', 1, 
                                    isset($manage_module_enable['mpcs_module']) && $manage_module_enable['mpcs_module'] == 1, 
                                    ['class' => 'input-icheck-red ch_select']) !!}
                                </div>

                               
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['mpcs_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['mpcs_expiry_date']))
                                        @if(strtotime($module_activation_data['mpcs_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['mpcs_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('mpcs_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['mpcs_interval']) ?
                                        $module_activation_data['mpcs_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('mpcs_length', !empty($module_activation_data['mpcs_length']) ?
                                        $module_activation_data['mpcs_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('mpcs_activated_on', !empty($module_activation_data['mpcs_activated_on']) ?
                                    $module_activation_data['mpcs_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('mpcs_expiry_date', !empty($module_activation_data['mpcs_expiry_date']) ?
                                    $module_activation_data['mpcs_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('mpcs_price', !empty($module_activation_data['mpcs_price']) ?
                                    $module_activation_data['mpcs_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <br>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    {!! Form::checkbox('mpcs_form_settings', 1,
                                    !empty($manage_module_enable['mpcs_form_settings']) ? true : false, ['class' =>
                                    'input-icheck-red ch_select
                                    ']) !!}<label class="search_label">@lang('mpcs::lang.mpcs_form_settings')</label>
                                </div>
                                <div class="col-md-4">
                                    {!! Form::checkbox('list_opening_values', 1,
                                    !empty($manage_module_enable['list_opening_values']) ? true : false, ['class' =>
                                    'input-icheck-red ch_select
                                    ']) !!}<label class="search_label">@lang('mpcs::lang.list_opening_values')</label>
                                </div>
                                
                                <div class="col-md-4">
                                    {!! Form::checkbox('merge_sub_category', 1,
                                    !empty($manage_module_enable['merge_sub_category']) ? true : false, ['class' =>
                                    'input-icheck-red ch_select
                                    ch_select']) !!} <label class="search_label">@lang('superadmin::lang.merge_sub_category')</label>
                                </div>
                                
                            </div>
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.backup_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.backup_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('backup_module', 0) !!}
                                    {!! Form::checkbox('backup_module', 1,
                                    !empty($manage_module_enable['backup_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['backup_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['backup_expiry_date']))
                                        @if(strtotime($module_activation_data['backup_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['backup_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('backup_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['backup_interval']) ?
                                        $module_activation_data['backup_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('backup_length', !empty($module_activation_data['backup_length']) ?
                                        $module_activation_data['backup_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('backup_activated_on', !empty($module_activation_data['backup_activated_on']) ?
                                    $module_activation_data['backup_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('backup_expiry_date', !empty($module_activation_data['backup_expiry_date']) ?
                                    $module_activation_data['backup_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('backup_price', !empty($module_activation_data['backup_price']) ?
                                    $module_activation_data['backup_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                             <div class="row">
                                 <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('restore_module', 1,
                                    !empty($manage_module_enable['restore_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                    
                                    <label class="search_label">Restore Backups</label>
                                </div>
                                
                            </div>
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.contact_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                               <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.contact_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('contact_module', 0) !!}
                                    {!! Form::checkbox('contact_module', 1,
                                    !empty($manage_module_enable['contact_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['contact_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['contact_expiry_date']))
                                        @if(strtotime($module_activation_data['contact_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['contact_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('contact_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['contact_interval']) ?
                                        $module_activation_data['contact_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('contact_length', !empty($module_activation_data['contact_length']) ?
                                        $module_activation_data['contact_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('contact_activated_on', !empty($module_activation_data['contact_activated_on']) ?
                                    $module_activation_data['contact_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('contact_expiry_date', !empty($module_activation_data['contact_expiry_date']) ?
                                    $module_activation_data['contact_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('contact_price', !empty($module_activation_data['contact_price']) ?
                                    $module_activation_data['contact_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-4">
                                    <label class="search_label">@lang('superadmin::lang.customer_credit_notification_type')</label><br>
                                    
                                    {!! Form::select('customer_credit_notification_type[]', ['settlement' => __('contact.settlement'), 'customer_bill' => __('contact.bill_to_customer'),'pumper_dashboard' => __('contact.pumper_dashboard')],
                                    $customer_credit_notification_type, ['class' => 'form-control
                                    select2', 'multiple', 'id' => 'customer_credit_notification_type']); !!}
                                </div>
                            </div>
                            <hr>
                           
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                
                               
                                <div class="col-md-3">
                                    {!! Form::checkbox('contact_supplier', 1,
                                    !empty($manage_module_enable['contact_supplier']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                    <label class="search_label">@lang('superadmin::lang.contact_supplier')</label>
                                </div>
                          
                                <div class="col-md-3">
                                    {!! Form::checkbox('contact_customer', 1,
                                    !empty($manage_module_enable['contact_customer']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                    <label class="search_label">@lang('superadmin::lang.contact_customer')</label>
                                </div>
                                <div class="clearfix"></div>
                                
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_supplier', 1, !empty($manage_module_enable['contact_supplier']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_supplier']); !!}
                                        {{__('superadmin::lang.supplier_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_customer', 1, !empty($manage_module_enable['contact_customer']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_customer']); !!}
                                        {{__('superadmin::lang.customer_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_group_customer', 1, !empty($manage_module_enable['contact_group_customer']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_group_customer']); !!}
                                        {{__('superadmin::lang.contact_group_customer_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_group_supplier', 1, !empty($manage_module_enable['contact_group_supplier']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_group_supplier']); !!}
                                        {{__('superadmin::lang.contact_group_supplier_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('import_contact', 1, !empty($manage_module_enable['import_contact']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'import_contact']); !!}
                                        {{__('superadmin::lang.import_contact_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('customer_reference', 1, !empty($manage_module_enable['customer_reference']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'customer_reference']); !!}
                                        {{__('superadmin::lang.customer_reference_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('customer_statement', 1, !empty($manage_module_enable['customer_statement']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'customer_statement']); !!}
                                        {{__('superadmin::lang.customer_statement_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('customer_payment', 1, !empty($manage_module_enable['customer_payment']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=> 'customer_payment']); !!}
                                        {{__('superadmin::lang.customer_payment_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('outstanding_received', 1, !empty($manage_module_enable['outstanding_received']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'outstanding_received']); !!}
                                        {{__('superadmin::lang.outstanding_received_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('stock_taking_page', 1, !empty($manage_module_enable['stock_taking_page']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'stock_taking_page']); !!}
                                        {{__('superadmin::lang.stock_taking_page')}}
                                    </label>

                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('issue_payment_detail', 1, !empty($manage_module_enable['issue_payment_detail']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'issue_payment_detail']); !!}
                                        {{__('superadmin::lang.issue_payment_detail_tab_page')}}
                                    </label>

                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('edit_received_outstanding', 1, !empty($manage_module_enable['edit_received_outstanding']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'edit_received_outstanding']); !!}
                                        {{__('superadmin::lang.edit_received_outstanding')}}
                                    </label>
                                </div>
                            </div>
                            
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('customer_payment_simple', 1, !empty($manage_module_enable['customer_payment_simple']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'customer_payment_simple']); !!}
                                        {{__('superadmin::lang.customer_payment_simple')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('customer_payment_bulk', 1, !empty($manage_module_enable['customer_payment_bulk']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'customer_payment_bulk']); !!}
                                        {{__('superadmin::lang.customer_payment_bulk')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('list_customer_payments', 1, !empty($manage_module_enable['list_customer_payments']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'list_customer_payments']); !!}
                                        {{__('superadmin::lang.list_customer_payments')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('customer_interest', 1, !empty($manage_module_enable['customer_interest']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'customer_interest']); !!}
                                        {{__('superadmin::lang.customer_interest')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('interest_settings', 1, !empty($manage_module_enable['interest_settings']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'interest_settings']); !!}
                                        {{__('superadmin::lang.interest_settings')}}
                                    </label>
                                </div>
                            </div>
                            
                             <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('ledger_discount', 1, !empty($manage_module_enable['ledger_discount']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'ledger_discount']); !!}
                                        {{__('superadmin::lang.ledger_discount')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('customer_statement_pmt', 1, !empty($manage_module_enable['customer_statement_pmt']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'customer_statement_pmt']); !!}
                                        {{__('contact.customer_statements_with_payment')}}
                                    </label>
                                </div>
                            </div>
                            
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_list_customer_loans', 1, !empty($manage_module_enable['contact_list_customer_loans']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_list_customer_loans']); !!}
                                        {{__('superadmin::lang.contact_list_customer_loans')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_settings', 1, !empty($manage_module_enable['contact_settings']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_settings']); !!}
                                        {{__('superadmin::lang.contact_settings')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_list_supplier_map_products', 1, !empty($manage_module_enable['contact_list_supplier_map_products']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_list_supplier_map_products']); !!}
                                        {{__('superadmin::lang.contact_list_supplier_map_products')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_add_supplier_products', 1, !empty($manage_module_enable['contact_add_supplier_products']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_add_supplier_products']); !!}
                                        {{__('superadmin::lang.contact_add_supplier_products')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_import_opening_balalnces', 1, !empty($manage_module_enable['contact_import_opening_balalnces']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_import_opening_balalnces']); !!}
                                        {{__('superadmin::lang.contact_import_opening_balalnces')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_returned_cheque_details', 1, !empty($manage_module_enable['contact_returned_cheque_details']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'contact_returned_cheque_details']); !!}
                                        {{__('superadmin::lang.contact_returned_cheque_details')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-md-3">
                                {!! Form::checkbox('delete_customer_statement', 1,
                                (!empty($manage_module_enable['delete_customer_statement'])  || !array_key_exists('delete_customer_statement',$manage_module_enable)) ? true : false, ['class' =>
                                'input-icheck-red
                                ch_select']) !!}<label
                                        class="search_label">@lang('lang_v1.vat.delete_customer_statement')</label>
                            </div>
                            
                            <div class="col-md-3">
                                {!! Form::checkbox('delete_statement_payment', 1,
                                (!empty($manage_module_enable['delete_statement_payment'])  || !array_key_exists('delete_statement_payment',$manage_module_enable)) ? true : false, ['class' =>
                                'input-icheck-red
                                ch_select']) !!}<label
                                        class="search_label">@lang('lang_v1.vat.delete_statement_payment')</label>
                            </div>
                            
                        </div>
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.report_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                            <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.report_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('report_module', 0) !!}
                                    {!! Form::checkbox('report_module', 1,
                                    !empty($manage_module_enable['report_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['report_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['report_expiry_date']))
                                        @if(strtotime($module_activation_data['report_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['report_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('report_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['report_interval']) ?
                                        $module_activation_data['report_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('report_length', !empty($module_activation_data['report_length']) ?
                                        $module_activation_data['report_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('report_activated_on', !empty($module_activation_data['report_activated_on']) ?
                                    $module_activation_data['report_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('report_expiry_date', !empty($module_activation_data['report_expiry_date']) ?
                                    $module_activation_data['report_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('report_price', !empty($module_activation_data['report_price']) ?
                                    $module_activation_data['report_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                           
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('verification_report', 1,
                                    !empty($manage_module_enable['verification_report']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.verification_report')</label>
                                </div>
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('monthly_report', 1,
                                    !empty($manage_module_enable['monthly_report']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.monthly_report')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('comparison_report', 1,
                                    !empty($manage_module_enable['comparison_report']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.comparison_report')</label>
                                </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label" class="flex-label">
                                        {!! Form::checkbox('product_report', 1, !empty($manage_module_enable['product_report']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'product_report']); !!}
                                        {{__('superadmin::lang.product_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('payment_status_report', 1, !empty($manage_module_enable['payment_status_report']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'payment_status_report']); !!}
                                        {{__('superadmin::lang.payment_status_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('report_daily', 1, !empty($manage_module_enable['report_daily']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'report_daily']); !!}
                                        {{__('superadmin::lang.report_daily_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('report_daily_summary', 1, !empty($manage_module_enable['report_daily_summary']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'report_daily_summary']); !!}
                                        {{__('superadmin::lang.report_daily_summary_tab_page')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('report_register', 1, !empty($manage_module_enable['report_register']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'report_register']); !!}
                                        {{__('superadmin::lang.report_register_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('report_profit_loss', 1, !empty($manage_module_enable['report_profit_loss']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'report_profit_loss']); !!}
                                        {{__('superadmin::lang.report_profit_loss_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('report_credit_status', 1, !empty($manage_module_enable['report_credit_status']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'report_credit_status']); !!}
                                        {{__('superadmin::lang.report_credit_status_tab_page')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('activity_report', 1, !empty($manage_module_enable['activity_report']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'activity_report']); !!}
                                        {{__('superadmin::lang.activity_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('contact_report', 1, !empty($manage_module_enable['contact_report']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'contact_report']); !!}
                                        {{__('superadmin::lang.contact_report_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('trending_product', 1, !empty($manage_module_enable['trending_product']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'trending_product']); !!}
                                        {{__('superadmin::lang.trending_product_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('user_activity', 1, !empty($manage_module_enable['user_activity']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'user_activity']); !!}
                                        {{__('superadmin::lang.user_activity_sub_menu')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('report_verification', 1, !empty($manage_module_enable['report_verification']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'report_verification']); !!}
                                        @lang('lang_v1.verification_reports')
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('report_table', 1, !empty($manage_module_enable['report_table']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'report_table']); !!}
                                        @lang('lang_v1.table_report')
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('report_staff_service', 1, !empty($manage_module_enable['report_staff_service']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'report_staff_service']); !!}
                                        @lang('lang_v1.service_staff_reports')
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('customized_report', 1, !empty($manage_module_enable['customized_report']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'customized_report']); !!}
                                        @lang('lang_v1.customized_report')
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                         </div>
                          
                    </div>
                    
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.settings_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.settings_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('settings_module', 0) !!}
                                    {!! Form::checkbox('settings_module', 1,
                                    !empty($manage_module_enable['settings_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['settings_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['settings_expiry_date']))
                                        @if(strtotime($module_activation_data['settings_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['settings_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('settings_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['settings_interval']) ?
                                        $module_activation_data['settings_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('settings_length', !empty($module_activation_data['settings_length']) ?
                                        $module_activation_data['settings_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('settings_activated_on', !empty($module_activation_data['settings_activated_on']) ?
                                    $module_activation_data['settings_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('settings_expiry_date', !empty($module_activation_data['settings_expiry_date']) ?
                                    $module_activation_data['settings_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('settings_price', !empty($module_activation_data['settings_price']) ?
                                    $module_activation_data['settings_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('business_settings', 1,
                                    !empty($manage_module_enable['business_settings']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.business_settings')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('business_location', 1,
                                    !empty($manage_module_enable['business_location']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.business_location')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('invoice_settings', 1,
                                    !empty($manage_module_enable['invoice_settings']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.invoice_settings')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('tax_rates', 1,
                                    !empty($manage_module_enable['tax_rates']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.tax_rates')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('settings_otp_verification', 1,
                                    !empty($manage_module_enable['settings_otp_verification']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.OTP_verification_enabled')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('settings_pay_online', 1,
                                    !empty($manage_module_enable['settings_pay_online']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">Pay Online</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('settings_reports_configurations', 1,
                                    !empty($manage_module_enable['settings_reports_configurations']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">Reports Configurations</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('settings_user_locations', 1,
                                    !empty($manage_module_enable['settings_user_locations']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">User Locations</label>
                                </div>
                            </div> 
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.sale_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                            <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.sale_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('sale_module', 0) !!}
                                    {!! Form::checkbox('sale_module', 1,
                                    !empty($manage_module_enable['sale_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['sale_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['sale_expiry_date']))
                                        @if(strtotime($module_activation_data['sale_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['sale_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('sale_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['sale_interval']) ?
                                        $module_activation_data['sale_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('sale_length', !empty($module_activation_data['sale_length']) ?
                                        $module_activation_data['sale_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('sale_activated_on', !empty($module_activation_data['sale_activated_on']) ?
                                    $module_activation_data['sale_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('sale_expiry_date', !empty($module_activation_data['sale_expiry_date']) ?
                                    $module_activation_data['sale_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('sale_price', !empty($module_activation_data['sale_price']) ?
                                    $module_activation_data['sale_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('all_sales', 1,
                                    !empty($manage_module_enable['all_sales']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.all_sales')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('add_sale', 1,
                                    !empty($manage_module_enable['add_sale']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.add_sale')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('pos_sale', 1,
                                    !empty($manage_module_enable['pos_sale']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.pos')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('list_pos', 1,
                                    !empty($manage_module_enable['list_pos']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_pos')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('list_draft', 1,
                                    !empty($manage_module_enable['list_draft']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_draft')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('list_quotation', 1,
                                    !empty($manage_module_enable['list_quotation']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_quotation')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('list_sell_return', 1,
                                    !empty($manage_module_enable['list_sell_return']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.list_sell_return')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('shipment', 1,
                                    !empty($manage_module_enable['shipment']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.shipment')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('discount', 1,
                                    !empty($manage_module_enable['discount']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.discount')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('import_sale', 1,
                                    !empty($manage_module_enable['import_sale']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.import_sale')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('reserved_stock', 1,
                                    !empty($manage_module_enable['reserved_stock']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.reserved_stock')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('list_orders', 1,
                                    !empty($manage_module_enable['list_orders']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.list_orders')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('upload_orders', 1,
                                    !empty($manage_module_enable['upload_orders']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.upload_orders')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('subcriptions', 1,
                                    !empty($manage_module_enable['subcriptions']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.subcriptions')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('over_limit_sales', 1,  
                                    !empty($manage_module_enable['over_limit_sales']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label
                                            class="search_label">@lang('superadmin::lang.over_limit_sales')</label> 
                                </div>
                            
                                <div class="col-md-3">
                                    {!! Form::checkbox('status_order', 1,
                                    !empty($manage_module_enable['status_order']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.status_order')</label>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label search_label">
                                            {!! Form::checkbox('pos_button_on_top_belt', 1, !empty($manage_module_enable['pos_button_on_top_belt']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'pos_button_on_top_belt']); !!}
                                            {{__('superadmin::lang.pos_button_on_top_belt')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                            
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.leads_module')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.leads_module')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('leads_module', 0) !!}
                                    {!! Form::checkbox('leads_module', 1,
                                    !empty($manage_module_enable['leads_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['leads_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['leads_expiry_date']))
                                        @if(strtotime($module_activation_data['leads_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['leads_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('leads_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['leads_interval']) ?
                                        $module_activation_data['leads_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('leads_length', !empty($module_activation_data['leads_length']) ?
                                        $module_activation_data['leads_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('leads_activated_on', !empty($module_activation_data['leads_activated_on']) ?
                                    $module_activation_data['leads_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('leads_expiry_date', !empty($module_activation_data['leads_expiry_date']) ?
                                    $module_activation_data['leads_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('leads_price', !empty($module_activation_data['leads_price']) ?
                                    $module_activation_data['leads_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                
                                <div class="col-md-3">
                                    {!! Form::checkbox('leads', 1,
                                    !empty($manage_module_enable['leads']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.leads')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('day_count', 1,
                                    !empty($manage_module_enable['day_count']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.day_count')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('leads_import', 1,
                                    !empty($manage_module_enable['leads_import']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.leads_import')</label>
                                </div>
                                <div class="col-md-3">
                                    {!! Form::checkbox('leads_settings', 1,
                                    !empty($manage_module_enable['leads_settings']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}<label class="search_label">@lang('superadmin::lang.settings')</label>
                                </div>
                            </div>
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.enable_sms')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              
                            <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                
                                <div class="row">
                                    <div class="col-md-2">
                                        <label class="search_label">@lang('superadmin::lang.enable_sms')</label>
                                    </div>
                                    
                                    <div class="col-md-1">
                                    {!! Form::hidden('enable_sms', 0) !!}
                                        {!! Form::checkbox('enable_sms', 1, !empty($manage_module_enable['enable_sms']) ? true :
                                        false,
                                        ['class' => 'input-icheck-red ch_select']) !!}
                                    </div>
                                        <div class="col-md-1">
                                            @if(empty($module_activation_data['enable_sms_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['enable_sms_expiry_date']))
                                        @if(strtotime($module_activation_data['enable_sms_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['enable_sms_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                        </div>
                                        
                                        <div class="col-md-1">
                                        {!! Form::select('enable_sms_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['enable_sms_interval']) ?
                                        $module_activation_data['enable_sms_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('enable_sms_length', !empty($module_activation_data['enable_sms_length']) ?
                                        $module_activation_data['enable_sms_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                        
                                        <div class="col-md-2">
                                            {!! Form::date('enable_sms_activated_on', !empty($module_activation_data['enable_sms_activated_on']) ?
                                            $module_activation_data['enable_sms_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::date('enable_sms_expiry_date', !empty($module_activation_data['enable_sms_expiry_date']) ?
                                            $module_activation_data['enable_sms_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                        </div>
                                        <div class="col-md-2">
                                            {!! Form::text('enable_sms_price', !empty($module_activation_data['enable_sms_price']) ?
                                            $module_activation_data['enable_sms_price'] : null, ['class' => 'form-control']) !!}
                                        </div>
                                </div>

                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.list_sms')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('list_sms', 1,
                                    !empty($manage_module_enable['list_sms']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['list_sms_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['list_sms_expiry_date']))
                                        @if(strtotime($module_activation_data['list_sms_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['list_sms_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('list_sms_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['list_sms_interval']) ?
                                    $module_activation_data['list_sms_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('list_sms_length', !empty($module_activation_data['list_sms_length']) ?
                                    $module_activation_data['list_sms_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('list_sms_activated_on', !empty($module_activation_data['list_sms_activated_on']) ?
                                    $module_activation_data['list_sms_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('list_sms_expiry_date', !empty($module_activation_data['list_sms_expiry_date']) ?
                                    $module_activation_data['list_sms_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('list_sms_price', !empty($module_activation_data['list_sms_price']) ?
                                    $module_activation_data['list_sms_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                               
                                </div>
                            <hr>
                           
                            
                            <div class="row">
                                <div class="col-md-12">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">
                                                {{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                    </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label search_label">
                                            {!! Form::checkbox('access_sms_settings', 1,!empty($manage_module_enable['access_sms_settings']) ? true : false, ['class' => 'input-icheck-red
                                                ch_select', 'id' => 'sms_settings_checkbox']) !!}
                                            @lang('superadmin::lang.access_sms_settings')
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label search_label">
                                            {!! Form::checkbox('sms_ledger', 1, !empty($manage_module_enable['sms_ledger']) ? true : false, ['class' => 'input-icheck-red ch_select']); !!}
                                            {{__('lang_v1.sms_ledger')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label search_label">
                                            {!! Form::checkbox('sms_delivery_report', 1, !empty($manage_module_enable['sms_delivery_report']) ? true : false, ['class' => 'input-icheck-red ch_select']); !!}
                                            {{__('lang_v1.sms_delivery_report')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label search_label">
                                            {!! Form::checkbox('sms_history', 1, !empty($manage_module_enable['sms_history']) ? true : false, ['class' => 'input-icheck-red ch_select']); !!}
                                            {{__('lang_v1.sms_history')}}
                                        </label>
                                    </div>
                                </div>
                            </div>
                                
                            <div class="row sms_setting_div">
                                 @include('business.partials.settings_sms')
                            </div>
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.enable_sms')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              
                            <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('lang_v1.smsmodule')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('smsmodule_module', 0) !!}
                                    {!! Form::checkbox('smsmodule_module', 1,
                                    !empty($manage_module_enable['smsmodule_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['smsmodule_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['smsmodule_expiry_date']))
                                        @if(strtotime($module_activation_data['smsmodule_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['smsmodule_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('smsmodule_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['smsmodule_interval']) ?
                                    $module_activation_data['smsmodule_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('smsmodule_length', !empty($module_activation_data['smsmodule_length']) ?
                                    $module_activation_data['smsmodule_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('smsmodule_activated_on', !empty($module_activation_data['smsmodule_activated_on']) ?
                                    $module_activation_data['smsmodule_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('smsmodule_expiry_date', !empty($module_activation_data['smsmodule_expiry_date']) ?
                                    $module_activation_data['smsmodule_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('smsmodule_price', !empty($module_activation_data['smsmodule_price']) ?
                                    $module_activation_data['smsmodule_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                               
                                </div>
                            <hr>
                           
                            
                            <div class="row">
                                <div class="col-md-12">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" class="check_all input-icheck-red">
                                                {{ __( 'role.select_all' ) }}
                                            </label>
                                        </div>
                                    </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label search_label">
                                            {!! Form::checkbox('sms_quick_send', 1,!empty($manage_module_enable['sms_quick_send']) ? true : false, ['class' => 'input-icheck-red
                                                ch_select', 'id' => 'sms_settings_checkbox']) !!}
                                            @lang('lang_v1.sms_quick_send')
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label search_label">
                                            {!! Form::checkbox('sms_from_file', 1, !empty($manage_module_enable['sms_from_file']) ? true : false, ['class' => 'input-icheck-red ch_select']); !!}
                                            {{__('lang_v1.sms_from_file')}}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label search_label">
                                            {!! Form::checkbox('sms_campaign', 1, !empty($manage_module_enable['sms_campaign']) ? true : false, ['class' => 'input-icheck-red ch_select']); !!}
                                            {{__('lang_v1.sms_campaign')}}
                                        </label>
                                    </div>
                                </div>
                               
                            </div>
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> Purchase Module</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                          
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.purchase')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('purchase', 0) !!}
                                    {!! Form::checkbox('purchase', 1,
                                    !empty($manage_module_enable['purchase']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['purchase_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['purchase_expiry_date']))
                                        @if(strtotime($module_activation_data['purchase_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['purchase_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('purchase_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['purchase_interval']) ?
                                        $module_activation_data['purchase_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('purchase_length', !empty($module_activation_data['purchase_length']) ?
                                        $module_activation_data['purchase_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('purchase_activated_on', !empty($module_activation_data['purchase_activated_on']) ?
                                    $module_activation_data['purchase_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('purchase_expiry_date', !empty($module_activation_data['purchase_expiry_date']) ?
                                    $module_activation_data['purchase_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('purchase_price', !empty($module_activation_data['purchase_price']) ?
                                    $module_activation_data['purchase_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                              
                            </div>
                            <hr>
                            
                            <div class="row">
                            <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('all_purchase', 1, !empty($manage_module_enable['all_purchase']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'all_purchase']); !!}
                                        {{__('All Purchase')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('add_purchase', 1, !empty($manage_module_enable['add_purchase']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'add_purchase']); !!}
                                        {{__('Add Purchase')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('add_bulk_purchase', 1, !empty($manage_module_enable['add_bulk_purchase']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'add_bulk_purchase']); !!}
                                        {{__('Add Bulk Purchase')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('import_purchase', 1, !empty($manage_module_enable['import_purchase']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'import_purchase']); !!}
                                        {{__('Import Purchase')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('pop_button_on_top_belt', 1, !empty($manage_module_enable['pop_button_on_top_belt']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'pop_button_on_top_belt']); !!}
                                        {{__('Pop Button On The Belt')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('purchase_return', 1, !empty($manage_module_enable['purchase_return']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'purchase_return']); !!}
                                        {{__('Purchase Return')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> Products Module</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                          
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.products')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('products', 0) !!}
                                    {!! Form::checkbox('products', 1,
                                    !empty($manage_module_enable['products']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['products_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['products_expiry_date']))
                                        @if(strtotime($module_activation_data['products_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['products_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('products_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['products_interval']) ?
                                        $module_activation_data['products_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('products_length', !empty($module_activation_data['products_length']) ?
                                        $module_activation_data['products_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('products_activated_on', !empty($module_activation_data['products_activated_on']) ?
                                    $module_activation_data['products_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('products_expiry_date', !empty($module_activation_data['products_expiry_date']) ?
                                    $module_activation_data['products_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('products_price', !empty($module_activation_data['products_price']) ?
                                    $module_activation_data['products_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                              
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('stock_taking', 1, !empty($manage_module_enable['stock_taking']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'stock_taking']); !!}
                                        {{__('superadmin::lang.stock_taking')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('fixed_conversion', 1, !empty($manage_module_enable['fixed_conversion']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'fixed_conversion']); !!}
                                        {{__('superadmin::lang.fixed_conversion')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_list_product', 1, (!empty($manage_module_enable['products_list_product']) || !array_key_exists('products_list_product',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'products_list_product']); !!}
                                        {{__('superadmin::lang.products_list_product')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_all_products', 1, (!empty($manage_module_enable['products_all_products']) || !array_key_exists('products_all_products',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_all_products']); !!}
                                        {{__('superadmin::lang.products_all_products')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_current_stock', 1, (!empty($manage_module_enable['products_current_stock']) || !array_key_exists('products_current_stock',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_current_stock']); !!}
                                        {{__('superadmin::lang.products_current_stock')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_add_edit', 1, (!empty($manage_module_enable['products_add_edit']) || !array_key_exists('products_add_edit',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_add_edit']); !!}
                                        {{__('superadmin::lang.products_add_edit')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_stock_history', 1, (!empty($manage_module_enable['products_stock_history']) || !array_key_exists('products_stock_history',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_stock_history']); !!}
                                        {{__('superadmin::lang.products_stock_history')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_stock_report', 1, (!empty($manage_module_enable['products_stock_report']) || !array_key_exists('products_stock_report',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_stock_report']); !!}
                                        {{__('superadmin::lang.products_stock_report')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_opening_stock', 1, (!empty($manage_module_enable['products_opening_stock']) || !array_key_exists('products_opening_stock',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_opening_stock']); !!}
                                        {{__('superadmin::lang.products_opening_stock')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_variations', 1, (!empty($manage_module_enable['products_variations']) || !array_key_exists('products_variations',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_variations']); !!}
                                        {{__('superadmin::lang.products_variations')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_import', 1, (!empty($manage_module_enable['products_import']) || !array_key_exists('products_import',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_import']); !!}
                                        {{__('superadmin::lang.products_import')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_import_opening_stock', 1, (!empty($manage_module_enable['products_import_opening_stock']) || !array_key_exists('products_import_opening_stock',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_import_opening_stock']); !!}
                                        {{__('superadmin::lang.products_import_opening_stock')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_selling_price_group', 1, (!empty($manage_module_enable['products_selling_price_group']) || !array_key_exists('products_selling_price_group',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_selling_price_group']); !!}
                                        {{__('superadmin::lang.products_selling_price_group')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_units', 1, (!empty($manage_module_enable['products_units']) || !array_key_exists('products_units',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_units']); !!}
                                        {{__('superadmin::lang.products_units')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_stock_conversion', 1, (!empty($manage_module_enable['products_stock_conversion']) || !array_key_exists('products_stock_conversion',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_stock_conversion']); !!}
                                        {{__('superadmin::lang.products_stock_conversion')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_categories', 1, (!empty($manage_module_enable['products_categories']) || !array_key_exists('products_categories',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_categories']); !!}
                                        {{__('superadmin::lang.products_categories')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('products_brand_warranties', 1, (!empty($manage_module_enable['products_brand_warranties']) || !array_key_exists('products_brand_warranties',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'products_brand_warranties']); !!}
                                        {{__('superadmin::lang.products_brand_warranties')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('product_print_labels', 1, (!empty($manage_module_enable['product_print_labels']) || !array_key_exists('product_print_labels',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'product_print_labels']); !!}
                                        {{__('superadmin::lang.product_print_labels')}}
                                    </label>
                                </div>
                            </div>
                            
                            
                        </div>
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('superadmin::lang.show_in_role_page')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('management_reports', 1, (!empty($manage_module_enable['management_reports']) || !array_key_exists('management_reports',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'management_reports']); !!}
                                        {{__('superadmin::lang.management_reports')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('unfinished_form', 1, (!empty($manage_module_enable['unfinished_form']) || !array_key_exists('unfinished_form',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'unfinished_form']); !!}
                                        {{__('superadmin::lang.unfinished_form')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('routes', 1, (!empty($manage_module_enable['routes']) || !array_key_exists('routes',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'routes']); !!}
                                        {{__('superadmin::lang.routes')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('drivers', 1, (!empty($manage_module_enable['drivers']) || !array_key_exists('drivers',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'drivers']); !!}
                                        {{__('superadmin::lang.drivers')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('helpers', 1, (!empty($manage_module_enable['helpers']) || !array_key_exists('helpers',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'helpers']); !!}
                                        {{__('superadmin::lang.helpers')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('pump_operator', 1, (!empty($manage_module_enable['pump_operator']) || !array_key_exists('pump_operator',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'pump_operator']); !!}
                                        {{__('superadmin::lang.pump_operator')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('daily_pump_status', 1, (!empty($manage_module_enable['daily_pump_status']) || !array_key_exists('daily_pump_status',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'daily_pump_status']); !!}
                                        {{__('superadmin::lang.daily_pump_status')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('day_count', 1, (!empty($manage_module_enable['day_count']) || !array_key_exists('day_count',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'day_count']); !!}
                                        {{__('superadmin::lang.day_count')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('access_selling_price', 1, (!empty($manage_module_enable['access_selling_price']) || !array_key_exists('access_selling_price',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'access_selling_price']); !!}
                                        {{__('superadmin::lang.access_selling_price')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('set_minimum_price', 1, (!empty($manage_module_enable['set_minimum_price']) || !array_key_exists('set_minimum_price',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'set_minimum_price']); !!}
                                        {{__('superadmin::lang.set_minimum_price')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('view_sales_commission', 1, (!empty($manage_module_enable['view_sales_commission']) || !array_key_exists('view_sales_commission',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'view_sales_commission']); !!}
                                        {{__('superadmin::lang.view_sales_commission')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('current_sale', 1, (!empty($manage_module_enable['current_sale']) || !array_key_exists('current_sale',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'current_sale']); !!}
                                        {{__('superadmin::lang.current_sale')}}
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4>{{__('superadmin::lang.subscriptions_module')}}</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              
                            <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">{{__('superadmin::lang.subscriptions_module')}}</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('subscriptions_module', 0) !!}
                                    {!! Form::checkbox('subscriptions_module', 1, (!empty($manage_module_enable['subscriptions_module']) || !array_key_exists('subscriptions_module',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'subscriptions_module']); !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['subscriptions_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['subscriptions_expiry_date']))
                                        @if(strtotime($module_activation_data['subscriptions_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['subscriptions_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                <div class="col-md-1">
                                    {!! Form::select('subscriptions_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['subscriptions_interval']) ?
                                    $module_activation_data['subscriptions_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('subscriptions_length', !empty($module_activation_data['subscriptions_length']) ?
                                    $module_activation_data['subscriptions_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('subscriptions_activated_on', !empty($module_activation_data['subscriptions_activated_on']) ?
                                    $module_activation_data['subscriptions_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('subscriptions_expiry_date', !empty($module_activation_data['subscriptions_expiry_date']) ?
                                    $module_activation_data['subscriptions_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('subscriptions_price', !empty($module_activation_data['subscriptions_price']) ?
                                    $module_activation_data['subscriptions_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                          
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('list_subscriptions', 1, (!empty($manage_module_enable['list_subscriptions']) || !array_key_exists('list_subscriptions',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'list_subscriptions']); !!}
                                        {{__('superadmin::lang.list_subscriptions')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('subscriptions_settings', 1, (!empty($manage_module_enable['subscriptions_settings']) || !array_key_exists('subscriptions_settings',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'subscriptions_settings']); !!}
                                        {{__('superadmin::lang.subscriptions_settings')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('subscriptions_sms_template', 1, (!empty($manage_module_enable['subscriptions_sms_template']) || !array_key_exists('subscriptions_sms_template',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'subscriptions_sms_template']); !!}
                                        {{__('superadmin::lang.subscriptions_sms_template')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('subscriptions_user_activity', 1, (!empty($manage_module_enable['subscriptions_user_activity']) || !array_key_exists('subscriptions_user_activity',$manage_module_enable)) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'subscriptions_user_activity']); !!}
                                        {{__('superadmin::lang.subscriptions_user_activity')}}
                                    </label>
                                </div>
                            </div>
                            
                        </div>
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> Price Changes Module</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                          
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('pricechanges::lang.mpcs')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('price_changes_module', 0) !!}
                                    {!! Form::checkbox('price_changes_module', 1,
                                    !empty($manage_module_enable['price_changes_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['price_changes_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['price_changes_expiry_date']))
                                        @if(strtotime($module_activation_data['price_changes_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['price_changes_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('price_changes_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['price_changes_interval']) ?
                                    $module_activation_data['price_changes_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('price_changes_length', !empty($module_activation_data['price_changes_length']) ?
                                    $module_activation_data['price_changes_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('price_changes_activated_on', !empty($module_activation_data['price_changes_activated_on']) ?
                                    $module_activation_data['price_changes_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('price_changes_expiry_date', !empty($module_activation_data['price_changes_expiry_date']) ?
                                    $module_activation_data['price_changes_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('price_changes_price', !empty($module_activation_data['price_changes_price']) ?
                                    $module_activation_data['price_changes_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                               
                                </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="checkbox">
                                        <label class="flex-label search_label">
                                            {!! Form::checkbox('price_change_edit_qty', 1, !empty($manage_module_enable['price_change_edit_qty']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'price_change_edit_qty']); !!}
                                            {{__('superadmin::lang.price_change_edit_qty')}}
                                        </label>
                                    </div>
                                </div>
                                
                            </div>
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> @lang('petro::lang.ezy_products')</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                          
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('petro::lang.ezy_products')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('ezy_products', 0) !!}
                                    {!! Form::checkbox('ezy_products', 1,
                                    !empty($manage_module_enable['ezy_products']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['ezy_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['ezy_expiry_date']))
                                        @if(strtotime($module_activation_data['ezy_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['ezy_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('ezy_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['ezy_interval']) ?
                                    $module_activation_data['ezy_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('ezy_length', !empty($module_activation_data['ezy_length']) ?
                                    $module_activation_data['ezy_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('ezy_activated_on', !empty($module_activation_data['ezy_activated_on']) ?
                                    $module_activation_data['ezy_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('ezy_expiry_date', !empty($module_activation_data['ezy_expiry_date']) ?
                                    $module_activation_data['ezy_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('ezy_price', !empty($module_activation_data['ezy_price']) ?
                                    $module_activation_data['ezy_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                                
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('ezy_list_products', 1, !empty($manage_module_enable['ezy_list_products']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'ezy_list_products']); !!}
                                        {{__('superadmin::lang.ezy_list_products')}}
                                    </label>
                                </div>
                            </div>
                         
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('ezy_units', 1, !empty($manage_module_enable['ezy_units']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'ezy_units']); !!}
                                        {{__('superadmin::lang.ezy_units')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('ezy_categories', 1, !empty($manage_module_enable['ezy_categories']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'ezy_categories']); !!}
                                        {{__('superadmin::lang.ezy_categories')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('ezy_show_current_stock', 1, !empty($manage_module_enable['ezy_show_current_stock']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'ezy_show_current_stock']); !!}
                                        {{__('superadmin::lang.ezy_show_current_stock')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('ezy_show_stock_report', 1, !empty($manage_module_enable['ezy_show_stock_report']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'ezy_show_stock_report']); !!}
                                        {{__('superadmin::lang.ezy_show_stock_report')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> Cheque Writing Module</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                          
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('lang_v1.enable_cheque_writing')</label>
                                </div>
                                <div class="col-md-1">
                                {!! Form::hidden('enable_cheque_writing', 0) !!}
                                    {!! Form::checkbox('enable_cheque_writing', 1,
                                    !empty($manage_module_enable['enable_cheque_writing']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['cheque_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['cheque_expiry_date']))
                                        @if(strtotime($module_activation_data['cheque_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['cheque_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('cheque_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['cheque_interval']) ?
                                    $module_activation_data['cheque_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('cheque_length', !empty($module_activation_data['cheque_length']) ?
                                    $module_activation_data['cheque_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('cheque_activated_on', !empty($module_activation_data['cheque_activated_on']) ?
                                    $module_activation_data['cheque_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('cheque_expiry_date', !empty($module_activation_data['cheque_expiry_date']) ?
                                    $module_activation_data['cheque_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('cheque_price', !empty($module_activation_data['cheque_price']) ?
                                    $module_activation_data['cheque_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                                
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red">
                                            {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('cheque_templates', 1, !empty($manage_module_enable['cheque_templates']) ? true : false, ['class' => 'input-icheck-red ch_select', 'id'=>'cheque_templates']); !!}
                                        {{__('Templates')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('write_cheque', 1, !empty($manage_module_enable['write_cheque']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'write_cheque']); !!}
                                        {{__('Write Cheque')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('manage_stamps', 1, !empty($manage_module_enable['manage_stamps']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'manage_stamps']); !!}
                                        {{__('Manage Stamps')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('manage_payee', 1, !empty($manage_module_enable['manage_payee']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'manage_payee']); !!}
                                        {{__('Manage Payee')}}
                                    </label>
                                </div>
                            </div>
                        
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('cheque_number_list', 1, !empty($manage_module_enable['cheque_number_list']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'cheque_number_list']); !!}
                                        {{__('Cheque Number List')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('deleted_cheque_details', 1, !empty($manage_module_enable['deleted_cheque_details']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'deleted_cheque_details']); !!}
                                        {{__('Delete Cheque Numbers')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('printed_cheque_details', 1, !empty($manage_module_enable['printed_cheque_details']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'printed_cheque_details']); !!}
                                        {{__('Printed Cheque Details')}}
                                    </label>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('default_setting', 1, !empty($manage_module_enable['default_setting']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'default_setting']); !!}
                                        {{__('Default Settings')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('default_setting', 1, !empty($manage_module_enable['default_setting']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'default_setting']); !!}
                                        {{__('superadmin::lang.cheque_dashboard')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('cheque_add_template', 1, !empty($manage_module_enable['cheque_add_template']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'cheque_add_template']); !!}
                                        {{__('superadmin::lang.cheque_add_template')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('cheque_cancelled_cheques', 1, !empty($manage_module_enable['cheque_cancelled_cheques']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'cheque_cancelled_cheques']); !!}
                                        {{__('superadmin::lang.cheque_cancelled_cheques')}}
                                    </label>
                                </div>
                            </div>
                            
                            <div class="col-sm-3">
                                <div class="checkbox">
                                    <label class="flex-label search_label">
                                        {!! Form::checkbox('cheque_printed_cheques', 1, !empty($manage_module_enable['cheque_printed_cheques']) ? true : false, ['class' => 'input-icheck-red ch_select','id'=>'cheque_printed_cheques']); !!}
                                        {{__('superadmin::lang.cheque_printed_cheques')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                            
                         </div>
                          
                    </div>
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> Settlement SW Module</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>

                            
                          
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.settlement_sw')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('settlement_sw_module', 1, !empty($manage_module_enable['settlement_sw_module']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['settlement_sw_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['settlement_sw_module_expiry_date']))
                                        @if(strtotime($module_activation_data['settlement_sw_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['settlement_sw_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('settlement_sw_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['settlement_sw_module_interval']) ?
                                    $module_activation_data['settlement_sw_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('settlement_sw_module_length', !empty($module_activation_data['settlement_sw_module_length']) ?
                                    $module_activation_data['settlement_sw_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('settlement_sw_module_activated_on', !empty($module_activation_data['settlement_sw_module_activated_on']) ?
                                    $module_activation_data['settlement_sw_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('settlement_sw_module_expiry_date', !empty($module_activation_data['settlement_sw_module_expiry_date']) ?
                                    $module_activation_data['settlement_sw_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('settlement_sw_module_price', !empty($module_activation_data['settlement_sw_module_price']) ?
                                    $module_activation_data['settlement_sw_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_other_income', 0) !!}
                                        {!! Form::checkbox('settlement_sw_other_income', 1,
                                            !empty($manage_module_enable['settlement_sw_other_income']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_other_income">Settlement SW Other Income</label>
                                    </div>
                                
                                    <div class="col-md-2" style="background: #d6e8c8; width: 276px;">
                                        {!! Form::hidden('settlement_sw_customer_payments', 0) !!}
                                        {!! Form::checkbox('settlement_sw_customer_payments', 1,
                                            !empty($manage_module_enable['settlement_sw_customer_payments']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_customer_payments" >Settlement SW Customer Payments</label>
                                    </div>
                                
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_expenses', 0) !!}
                                        {!! Form::checkbox('settlement_sw_expenses', 1,
                                            !empty($manage_module_enable['settlement_sw_expenses']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_expenses">Settlement SW Expenses</label>
                                    </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_payment', 0) !!}
                                        {!! Form::checkbox('settlement_sw_payment', 1,
                                            !empty($manage_module_enable['settlement_sw_payment']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_payment">Settlement SW Payment</label>
                                    </div>
                                
                            </div>
                            <hr style="border: 2px solid #2196F3; margin: 20px 0;">
                            <div class="row">
                                <h5 style="margin-left: 15px;">Settlement SW Payment Section with Multi Tabs</h5>
                                <div class="col-md-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="check_all input-icheck-red"> {{ __( 'role.select_all' ) }}
                                        </label>
                                    </div>
                                </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_cash', 0) !!}
                                        {!! Form::checkbox('settlement_sw_cash', 1,
                                            !empty($manage_module_enable['settlement_sw_cash']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_cash">Cash</label>
                                    </div>
                                
                                    <div class="col-md-2" style="background: #d6e8c8; width: 276px;">
                                        {!! Form::hidden('settlement_sw_cash_deposit', 0) !!}
                                        {!! Form::checkbox('settlement_sw_cash_deposit', 1,
                                            !empty($manage_module_enable['settlement_sw_cash_deposit']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_cash_deposit" >Cash Deposit</label>
                                    </div>
                                
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_cards', 0) !!}
                                        {!! Form::checkbox('settlement_sw_cards', 1,
                                            !empty($manage_module_enable['settlement_sw_cards']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_cards">Cards</label>
                                    </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_cheque', 0) !!}
                                        {!! Form::checkbox('settlement_sw_cheque', 1,
                                            !empty($manage_module_enable['settlement_sw_cheque']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_cheque">Cheques</label>
                                    </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_payment_expenses', 0) !!}
                                        {!! Form::checkbox('settlement_sw_payment_expenses', 1,
                                            !empty($manage_module_enable['settlement_sw_payment_expenses']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_payment_expenses">Expenses</label>
                                    </div>
                                     <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_shortage', 0) !!}
                                        {!! Form::checkbox('settlement_sw_shortage', 1,
                                            !empty($manage_module_enable['settlement_sw_shortage']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_shortage">Shortage</label>
                                    </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_excess', 0) !!}
                                        {!! Form::checkbox('settlement_sw_excess', 1,
                                            !empty($manage_module_enable['settlement_sw_excess']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_excess">Excess</label>
                                    </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_credit_sales', 0) !!}
                                        {!! Form::checkbox('settlement_sw_credit_sales', 1,
                                            !empty($manage_module_enable['settlement_sw_credit_sales']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_credit_sales">Credit Sales</label>
                                    </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_loan_payments', 0) !!}
                                        {!! Form::checkbox('settlement_sw_loan_payments', 1,
                                            !empty($manage_module_enable['settlement_sw_loan_payments']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_loan_payments">Loan Payments</label>
                                    </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_owners_drawings', 0) !!}
                                        {!! Form::checkbox('settlement_sw_owners_drawings', 1,
                                            !empty($manage_module_enable['settlement_sw_owners_drawings']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_owners_drawings">Owners Drawings</label>
                                    </div>
                                    <div class="col-md-2" style="background: #d6e8c8;">
                                        {!! Form::hidden('settlement_sw_loan_to_customer', 0) !!}
                                        {!! Form::checkbox('settlement_sw_loan_to_customer', 1,
                                            !empty($manage_module_enable['settlement_sw_loan_to_customer']) ? true : false,
                                            ['class' => 'input-icheck-red ch_select']
                                        ) !!}
                                        <label for="settlement_sw_loan_to_customer">Loan to Customer</label>
                                    </div>
                                
                            </div>

                            
                            <hr>
                            
                             
                        
                            
                         </div>

                         

                        

                          
                    </div>
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> HMS  Module</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                          
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.hms_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('hms_module', 1, !empty($manage_module_enable['hms_module']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['hms_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['hms_module_expiry_date']))
                                        @if(strtotime($module_activation_data['hms_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['hms_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('hms_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['hms_module_interval']) ?
                                    $module_activation_data['hms_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('hms_module_length', !empty($module_activation_data['hms_module_length']) ?
                                    $module_activation_data['hms_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('hms_module_activated_on', !empty($module_activation_data['hms_module_activated_on']) ?
                                    $module_activation_data['hms_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('hms_module_expiry_date', !empty($module_activation_data['hms_module_expiry_date']) ?
                                    $module_activation_data['hms_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('hms_module_price', !empty($module_activation_data['hms_module_price']) ?
                                    $module_activation_data['hms_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="room_subscribe">{{ __('No of Room Subscribe') }}</label>
                                        {!! Form::text('room_subscribe', !empty($module_activation_data['room_subscribe']) ?
                                            $module_activation_data['room_subscribe'] : $previous_package_data['room_subscribe'], ['class' => 'form-control']) !!}
                                       
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                    <label for="room_added">{{ __('No of Room Added') }}</label>
                                    {!! Form::text('room_added', !empty($module_activation_data['room_added']) ?
                                        $module_activation_data['room_added'] : $previous_package_data['room_added'], ['class' => 'form-control', 'readonly' => 'readonly']) !!}

                                     
                                       
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label for="room_could_be_added">{{ __('No of Room Could be added') }}</label>
                                        {!! Form::text('room_could_be_added', !empty($module_activation_data['room_could_be_added']) ?
                                            $module_activation_data['room_could_be_added'] : $previous_package_data['room_could_be_added'], ['class' => 'form-control']) !!}

                                      
                                    </div>
                                </div>
                            </div>

                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> Other Modules Activation</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row bg-danger" style="margin-bottom: 10px;">
                                <div class="col-md-2">
                                    <b>Module Name</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Enable</b>
                                </div>
                                
                                <div class="col-md-1">
                                    <b>Status</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval</b>
                                </div>
                                <div class="col-md-1">
                                    <b>Interval Length</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Activated on</b>
                                </div>
                                <div class="col-md-2">
                                    <b>Expiry</b>
                                </div>
                                <div class="col-md-2  text-center">
                                    <h5><b>Module Price</b></h5>
                                </div>
                            </div>
                            
                             {{-- <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.issue_customer_bill')</label>
                                </div>
                                <div class="col-md-2">
                                     {!! Form::hidden('issue_customer_bill', 0) !!}
                                    {!! Form::checkbox('issue_customer_bill', 1,
                                    !empty($manage_module_enable['issue_customer_bill']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                --}}
                                
                            <!--Additional modules-->
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.issue_customer_bill')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('issue_customer_bill', 1, !empty($manage_module_enable['issue_customer_bill']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['issue_customer_bill_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['issue_customer_bill_expiry_date']))
                                        @if(strtotime($module_activation_data['issue_customer_bill_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['issue_customer_bill_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('issue_customer_bill_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['issue_customer_bill_interval']) ?
                                    $module_activation_data['issue_customer_bill_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('issue_customer_bill_length', !empty($module_activation_data['issue_customer_bill_length']) ?
                                    $module_activation_data['issue_customer_bill_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('issue_customer_bill_activated_on', !empty($module_activation_data['issue_customer_bill_activated_on']) ?
                                    $module_activation_data['issue_customer_bill_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('issue_customer_bill_expiry_date', !empty($module_activation_data['issue_customer_bill_expiry_date']) ?
                                    $module_activation_data['issue_customer_bill_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('issue_customer_bill_price', !empty($module_activation_data['issue_customer_bill_price']) ?
                                    $module_activation_data['issue_customer_bill_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            
                            <!--Additional modules-->
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.issue_customer_bill_vat')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('issue_customer_bill_vat', 1, !empty($manage_module_enable['issue_customer_bill_vat']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['issue_customer_bill_vat_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['issue_customer_bill_vat_expiry_date']))
                                        @if(strtotime($module_activation_data['issue_customer_bill_vat_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['issue_customer_bill_vat_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('issue_customer_bill_vat_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['issue_customer_bill_vat_interval']) ?
                                    $module_activation_data['issue_customer_bill_vat_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('issue_customer_bill_vat_length', !empty($module_activation_data['issue_customer_bill_vat_length']) ?
                                    $module_activation_data['issue_customer_bill_vat_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('issue_customer_bill_vat_activated_on', !empty($module_activation_data['issue_customer_bill_vat_activated_on']) ?
                                    $module_activation_data['issue_customer_bill_vat_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('issue_customer_bill_vat_expiry_date', !empty($module_activation_data['issue_customer_bill_vat_expiry_date']) ?
                                    $module_activation_data['issue_customer_bill_vat_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('issue_customer_bill_vat_price', !empty($module_activation_data['issue_customer_bill_vat_price']) ?
                                    $module_activation_data['issue_customer_bill_vat_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            
                            <!--Additional modules-->
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.crm_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('crm_module', 1, !empty($manage_module_enable['crm_module']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['crm_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['crm_module_expiry_date']))
                                        @if(strtotime($module_activation_data['crm_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['crm_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('crm_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['crm_module_interval']) ?
                                    $module_activation_data['crm_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('crm_module_length', !empty($module_activation_data['crm_module_length']) ?
                                    $module_activation_data['crm_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('crm_module_activated_on', !empty($module_activation_data['crm_module_activated_on']) ?
                                    $module_activation_data['crm_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('crm_module_expiry_date', !empty($module_activation_data['crm_module_expiry_date']) ?
                                    $module_activation_data['crm_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('crm_module_price', !empty($module_activation_data['crm_module_price']) ?
                                    $module_activation_data['crm_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            <!--Additional modules-->
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.ezyinvoice_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('ezyinvoice_module', 1, !empty($manage_module_enable['ezyinvoice_module']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['ezyinvoice_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['ezyinvoice_module_expiry_date']))
                                        @if(strtotime($module_activation_data['ezyinvoice_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['ezyinvoice_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('ezyinvoice_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['ezyinvoice_module_interval']) ?
                                    $module_activation_data['ezyinvoice_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('ezyinvoice_module_length', !empty($module_activation_data['ezyinvoice_module_length']) ?
                                    $module_activation_data['ezyinvoice_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('ezyinvoice_module_activated_on', !empty($module_activation_data['ezyinvoice_module_activated_on']) ?
                                    $module_activation_data['ezyinvoice_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('ezyinvoice_module_expiry_date', !empty($module_activation_data['ezyinvoice_module_expiry_date']) ?
                                    $module_activation_data['ezyinvoice_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('ezyinvoice_module_price', !empty($module_activation_data['ezyinvoice_module_price']) ?
                                    $module_activation_data['ezyinvoice_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.airline_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('airline_module', 1, !empty($manage_module_enable['airline_module']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['airline_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['airline_module_expiry_date']))
                                        @if(strtotime($module_activation_data['airline_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['airline_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('airline_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['airline_module_interval']) ?
                                    $module_activation_data['airline_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('airline_module_length', !empty($module_activation_data['airline_module_length']) ?
                                    $module_activation_data['airline_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('airline_module_activated_on', !empty($module_activation_data['airline_module_activated_on']) ?
                                    $module_activation_data['airline_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('airline_module_expiry_date', !empty($module_activation_data['airline_module_expiry_date']) ?
                                    $module_activation_data['airline_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('airline_module_price', !empty($module_activation_data['airline_module_price']) ?
                                    $module_activation_data['airline_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.shipping_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('shipping_module', 1, !empty($manage_module_enable['shipping_module']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['shipping_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['shipping_module_expiry_date']))
                                        @if(strtotime($module_activation_data['shipping_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['shipping_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('shipping_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['shipping_module_interval']) ?
                                    $module_activation_data['shipping_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('shipping_module_length', !empty($module_activation_data['shipping_module_length']) ?
                                    $module_activation_data['shipping_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('shipping_module_activated_on', !empty($module_activation_data['shipping_module_activated_on']) ?
                                    $module_activation_data['shipping_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('shipping_module_expiry_date', !empty($module_activation_data['shipping_module_expiry_date']) ?
                                    $module_activation_data['shipping_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('shipping_module_price', !empty($module_activation_data['shipping_module_price']) ?
                                    $module_activation_data['shipping_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.asset_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('asset_module', 1, !empty($manage_module_enable['asset_module']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['asset_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['asset_module_expiry_date']))
                                        @if(strtotime($module_activation_data['asset_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['asset_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('asset_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['asset_module_interval']) ?
                                    $module_activation_data['asset_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('asset_module_length', !empty($module_activation_data['asset_module_length']) ?
                                    $module_activation_data['asset_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('asset_module_activated_on', !empty($module_activation_data['asset_module_activated_on']) ?
                                    $module_activation_data['asset_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('asset_module_expiry_date', !empty($module_activation_data['asset_module_expiry_date']) ?
                                    $module_activation_data['asset_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('asset_module_price', !empty($module_activation_data['asset_module_price']) ?
                                    $module_activation_data['asset_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.access_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('access_module', 1, !empty($manage_module_enable['access_module']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['access_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['access_module_expiry_date']))
                                        @if(strtotime($module_activation_data['access_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['access_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('access_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['access_module_interval']) ?
                                    $module_activation_data['access_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('access_module_length', !empty($module_activation_data['access_module_length']) ?
                                    $module_activation_data['access_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('access_module_activated_on', !empty($module_activation_data['access_module_activated_on']) ?
                                    $module_activation_data['access_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('access_module_expiry_date', !empty($module_activation_data['access_module_expiry_date']) ?
                                    $module_activation_data['access_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('access_module_price', !empty($module_activation_data['access_module_price']) ?
                                    $module_activation_data['access_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> Installment Module</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('installment_module', 1, !empty($manage_module_enable['installment_module']) ? true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['installment_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['installment_module_expiry_date']))
                                        @if(strtotime($module_activation_data['installment_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['installment_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('installment_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['installment_module_interval']) ?
                                    $module_activation_data['installment_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('installment_module_length', !empty($module_activation_data['installment_module_length']) ?
                                    $module_activation_data['installment_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('installment_module_activated_on', !empty($module_activation_data['installment_module_activated_on']) ?
                                    $module_activation_data['installment_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('installment_module_expiry_date', !empty($module_activation_data['installment_module_expiry_date']) ?
                                    $module_activation_data['installment_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('installment_module_price', !empty($module_activation_data['installment_module_price']) ?
                                    $module_activation_data['installment_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                             <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label"> @lang('superadmin::lang.hospital_system')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('hospital_system', 1, !empty($manage_module_enable['hospital_system']) ?
                                    true :
                                    false, ['class' => 'input-icheck-red ch_select'])
                                    !!}
                                </div>
                                
                                <div class="col-md-1">
                                        @if(empty($module_activation_data['hospital_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['hospital_expiry_date']))
                                        @if(strtotime($module_activation_data['hospital_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['hospital_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::select('hospital_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['hospital_interval']) ?
                                        $module_activation_data['hospital_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('hospital_length', !empty($module_activation_data['hospital_length']) ?
                                        $module_activation_data['hospital_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                    
                                    <div class="col-md-2">
                                        {!! Form::date('hospital_activated_on', !empty($module_activation_data['hospital_activated_on']) ?
                                        $module_activation_data['hospital_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::date('hospital_expiry_date', !empty($module_activation_data['hospital_expiry_date']) ?
                                        $module_activation_data['hospital_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                    </div>
                                    <div class="col-md-2">
                                        {!! Form::text('hospital_price', !empty($module_activation_data['hospital_price']) ?
                                        $module_activation_data['hospital_price'] : null, ['class' => 'form-control']) !!}
                                    </div>
                                
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.enable_duplicate_invoice')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('enable_duplicate_invoice', 1,
                                    !empty($manage_module_enable['enable_duplicate_invoice']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['duplicate_invoice_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['duplicate_invoice_expiry_date']))
                                        @if(strtotime($module_activation_data['duplicate_invoice_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['duplicate_invoice_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('duplicate_invoice_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['duplicate_invoice_interval']) ?
                                    $module_activation_data['duplicate_invoice_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('duplicate_invoice_length', !empty($module_activation_data['duplicate_invoice_length']) ?
                                    $module_activation_data['duplicate_invoice_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('duplicate_invoice_activated_on', !empty($module_activation_data['duplicate_invoice_activated_on']) ?
                                    $module_activation_data['duplicate_invoice_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('duplicate_invoice_expiry_date', !empty($module_activation_data['duplicate_invoice_expiry_date']) ?
                                    $module_activation_data['duplicate_invoice_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('duplicate_invoice_price', !empty($module_activation_data['duplicate_invoice_price']) ?
                                    $module_activation_data['duplicate_invoice_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                                
                                
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('lang_v1.auto_services_and_repair_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('auto_services_and_repair_module', 1,
                                    !empty($manage_module_enable['auto_services_and_repair_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['auto_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['auto_expiry_date']))
                                        @if(strtotime($module_activation_data['auto_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['auto_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('auto_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['auto_interval']) ?
                                        $module_activation_data['auto_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('auto_length', !empty($module_activation_data['auto_length']) ?
                                        $module_activation_data['auto_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('auto_activated_on', !empty($module_activation_data['auto_activated_on']) ?
                                    $module_activation_data['auto_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('auto_expiry_date', !empty($module_activation_data['auto_expiry_date']) ?
                                    $module_activation_data['auto_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('auto_price', !empty($module_activation_data['auto_price']) ?
                                    $module_activation_data['auto_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                               
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('lang_v1.stock_taking_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('stock_taking_module', 1,
                                    !empty($manage_module_enable['stock_taking_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['stock_taking_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['stock_taking_expiry_date']))
                                        @if(strtotime($module_activation_data['stock_taking_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['stock_taking_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('stock_taking_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['stock_taking_interval']) ?
                                        $module_activation_data['stock_taking_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('stock_taking_length', !empty($module_activation_data['stock_taking_length']) ?
                                        $module_activation_data['stock_taking_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('stock_taking_activated_on', !empty($module_activation_data['stock_taking_activated_on']) ?
                                    $module_activation_data['stock_taking_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('stock_taking_expiry_date', !empty($module_activation_data['stock_taking_expiry_date']) ?
                                    $module_activation_data['stock_taking_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('stock_taking_price', !empty($module_activation_data['stock_taking_price']) ?
                                    $module_activation_data['stock_taking_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                               
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.property_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('property_module', 1,
                                    !empty($manage_module_enable['property_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['property_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['property_expiry_date']))
                                        @if(strtotime($module_activation_data['property_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['property_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('property_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['property_interval']) ?
                                        $module_activation_data['property_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('property_length', !empty($module_activation_data['property_length']) ?
                                        $module_activation_data['property_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('property_activated_on', !empty($module_activation_data['property_activated_on']) ?
                                    $module_activation_data['property_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('property_expiry_date', !empty($module_activation_data['property_expiry_date']) ?
                                    $module_activation_data['property_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('property_price', !empty($module_activation_data['property_price']) ?
                                    $module_activation_data['property_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.ran_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('ran_module', 1,
                                    !empty($manage_module_enable['ran_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['ran_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['ran_expiry_date']))
                                        @if(strtotime($module_activation_data['ran_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['ran_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('ran_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['ran_interval']) ?
                                        $module_activation_data['ran_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('ran_length', !empty($module_activation_data['ran_length']) ?
                                        $module_activation_data['ran_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('ran_activated_on', !empty($module_activation_data['ran_activated_on']) ?
                                    $module_activation_data['ran_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('ran_expiry_date', !empty($module_activation_data['ran_expiry_date']) ?
                                    $module_activation_data['ran_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('ran_price', !empty($module_activation_data['ran_price']) ?
                                    $module_activation_data['ran_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.notification_template_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('notification_template_module', 1,
                                    !empty($manage_module_enable['notification_template_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['notification_template_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['notification_template_expiry_date']))
                                        @if(strtotime($module_activation_data['notification_template_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['notification_template_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('notification_template_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['notification_template_interval']) ?
                                        $module_activation_data['notification_template_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('notification_template_length', !empty($module_activation_data['notification_template_length']) ?
                                        $module_activation_data['notification_template_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('notification_template_activated_on', !empty($module_activation_data['notification_template_activated_on']) ?
                                    $module_activation_data['notification_template_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('notification_template_expiry_date', !empty($module_activation_data['notification_template_expiry_date']) ?
                                    $module_activation_data['notification_template_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('notification_template_price', !empty($module_activation_data['notification_template_price']) ?
                                    $module_activation_data['notification_template_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.list_easy_payment')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('list_easy_payment', 1,
                                    !empty($manage_module_enable['list_easy_payment']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['list_easy_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['list_easy_expiry_date']))
                                        @if(strtotime($module_activation_data['list_easy_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['list_easy_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('list_easy_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['list_easy_interval']) ?
                                    $module_activation_data['list_easy_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('list_easy_length', !empty($module_activation_data['list_easy_length']) ?
                                    $module_activation_data['list_easy_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('list_easy_activated_on', !empty($module_activation_data['list_easy_activated_on']) ?
                                    $module_activation_data['list_easy_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('list_easy_expiry_date', !empty($module_activation_data['list_easy_expiry_date']) ?
                                    $module_activation_data['list_easy_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('list_easy_price', !empty($module_activation_data['list_easy_price']) ?
                                    $module_activation_data['list_easy_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                                
                                
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.user_management_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('user_management_module', 1,
                                    !empty($manage_module_enable['user_management_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['um_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['um_expiry_date']))
                                        @if(strtotime($module_activation_data['um_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['um_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                
                                <div class="col-md-1">
                                        {!! Form::select('um_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['um_interval']) ?
                                        $module_activation_data['um_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('um_length', !empty($module_activation_data['um_length']) ?
                                        $module_activation_data['um_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                
                                <div class="col-md-2">
                                    {!! Form::date('um_activated_on', !empty($module_activation_data['um_activated_on']) ?
                                    $module_activation_data['um_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('um_expiry_date', !empty($module_activation_data['um_expiry_date']) ?
                                    $module_activation_data['um_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('um_price', !empty($module_activation_data['um_price']) ?
                                    $module_activation_data['um_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.banking_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('banking_module', 1,
                                    !empty($manage_module_enable['banking_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['banking_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['banking_expiry_date']))
                                        @if(strtotime($module_activation_data['banking_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['banking_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('banking_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['banking_interval']) ?
                                        $module_activation_data['banking_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('banking_length', !empty($module_activation_data['banking_length']) ?
                                        $module_activation_data['banking_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('banking_activated_on', !empty($module_activation_data['banking_activated_on']) ?
                                    $module_activation_data['banking_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('banking_expiry_date', !empty($module_activation_data['banking_expiry_date']) ?
                                    $module_activation_data['banking_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('banking_price', !empty($module_activation_data['banking_price']) ?
                                    $module_activation_data['banking_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <hr>
                          
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.stock_transfer')</label>
                                </div>
                                <div class="col-md-1">
                                     {!! Form::checkbox('stock_transfer', 1,
                                    !empty($manage_module_enable['stock_transfer']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['stock_transfer_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['stock_transfer_expiry_date']))
                                        @if(strtotime($module_activation_data['stock_transfer_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['stock_transfer_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('stock_transfer_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['stock_transfer_interval']) ?
                                        $module_activation_data['stock_transfer_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('stock_transfer_length', !empty($module_activation_data['stock_transfer_length']) ?
                                        $module_activation_data['stock_transfer_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('stock_transfer_activated_on', !empty($module_activation_data['stock_transfer_activated_on']) ?
                                    $module_activation_data['stock_transfer_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('stock_transfer_expiry_date', !empty($module_activation_data['stock_transfer_expiry_date']) ?
                                    $module_activation_data['stock_transfer_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('stock_transfer_price', !empty($module_activation_data['stock_transfer_price']) ?
                                    $module_activation_data['stock_transfer_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                              
                            </div>
                           <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">Daily Report Review</label>
                                </div>
                                <div class="col-md-1">
                                     {!! Form::checkbox('daily_review', 1,
                                    !empty($manage_module_enable['daily_review']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['daily_review_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['daily_review_expiry_date']))
                                        @if(strtotime($module_activation_data['daily_review_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['daily_review_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('daily_review_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['daily_review_interval']) ?
                                        $module_activation_data['daily_review_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('daily_review_length', !empty($module_activation_data['daily_review_length']) ?
                                        $module_activation_data['daily_review_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('daily_review_activated_on', !empty($module_activation_data['daily_review_activated_on']) ?
                                    $module_activation_data['daily_review_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('daily_review_expiry_date', !empty($module_activation_data['daily_review_expiry_date']) ?
                                    $module_activation_data['daily_review_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('daily_review_price', !empty($module_activation_data['daily_review_price']) ?
                                    $module_activation_data['daily_review_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                              
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">Distribution</label>
                                </div>
                                <div class="col-md-1">
                                     {!! Form::checkbox('distribution_module', 1,
                                    !empty($manage_module_enable['distribution_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['distribution_module_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['distribution_module_expiry_date']))
                                        @if(strtotime($module_activation_data['distribution_module_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['distribution_module_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('distribution_module_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['distribution_module_interval']) ?
                                        $module_activation_data['distribution_module_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('distribution_module_length', !empty($module_activation_data['distribution_module_length']) ?
                                        $module_activation_data['distribution_module_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('distribution_module_activated_on', !empty($module_activation_data['distribution_module_activated_on']) ?
                                    $module_activation_data['distribution_module_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('distribution_module_expiry_date', !empty($module_activation_data['distribution_module_expiry_date']) ?
                                    $module_activation_data['distribution_module_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('distribution_module_price', !empty($module_activation_data['distribution_module_price']) ?
                                    $module_activation_data['distribution_module_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                              
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">Spreadsheet Module</label>
                                </div>
                                <div class="col-md-1">
                                     {!! Form::checkbox('spreadsheet', 1,
                                    !empty($manage_module_enable['spreadsheet']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['spreadsheet_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['spreadsheet_expiry_date']))
                                        @if(strtotime($module_activation_data['spreadsheet_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['spreadsheet_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('spreadsheet_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['spreadsheet_interval']) ?
                                        $module_activation_data['spreadsheet_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('spreadsheet_length', !empty($module_activation_data['spreadsheet_length']) ?
                                        $module_activation_data['spreadsheet_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('spreadsheet_activated_on', !empty($module_activation_data['spreadsheet_activated_on']) ?
                                    $module_activation_data['spreadsheet_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('spreadsheet_expiry_date', !empty($module_activation_data['spreadsheet_expiry_date']) ?
                                    $module_activation_data['spreadsheet_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('spreadsheet_price', !empty($module_activation_data['spreadsheet_price']) ?
                                    $module_activation_data['spreadsheet_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                              
                            </div>
                            
                            <hr>
                           
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.service_staff')</label>
                                </div>
                                <div class="col-md-1">
                                     {!! Form::checkbox('service_staff', 1,
                                    !empty($manage_module_enable['service_staff']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['service_staff_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['service_staff_expiry_date']))
                                        @if(strtotime($module_activation_data['service_staff_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['service_staff_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('service_staff_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['service_staff_interval']) ?
                                        $module_activation_data['service_staff_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('service_staff_length', !empty($module_activation_data['service_staff_length']) ?
                                        $module_activation_data['service_staff_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('service_staff_activated_on', !empty($module_activation_data['service_staff_activated_on']) ?
                                    $module_activation_data['service_staff_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('service_staff_expiry_date', !empty($module_activation_data['service_staff_expiry_date']) ?
                                    $module_activation_data['service_staff_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('service_staff_price', !empty($module_activation_data['service_staff_price']) ?
                                    $module_activation_data['service_staff_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                              
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.enable_subscription')</label>
                                </div>
                                <div class="col-md-1">
                                     {!! Form::checkbox('enable_subscription', 1,
                                    !empty($manage_module_enable['enable_subscription']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['enable_subscription_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['enable_subscription_expiry_date']))
                                        @if(strtotime($module_activation_data['enable_subscription_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['enable_subscription_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                        {!! Form::select('enable_subscription_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['enable_subscription_interval']) ?
                                        $module_activation_data['enable_subscription_interval'] :null, [ 'class' => 'form-control act_interval','style' => 'width:100%', 'placeholder' => __('lang_v1.all')
                                                ])
                                            !!}   
                            
                                    </div>
                                    
                                    <div class="col-md-1">
                                        {!! Form::text('enable_subscription_length', !empty($module_activation_data['enable_subscription_length']) ?
                                        $module_activation_data['enable_subscription_length'] : null, ['class' => 'form-control act_length']) !!}
                                    </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('enable_subscription_activated_on', !empty($module_activation_data['enable_subscription_activated_on']) ?
                                    $module_activation_data['enable_subscription_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('enable_subscription_expiry_date', !empty($module_activation_data['enable_subscription_expiry_date']) ?
                                    $module_activation_data['enable_subscription_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('enable_subscription_price', !empty($module_activation_data['enable_subscription_price']) ?
                                    $module_activation_data['enable_subscription_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                              
                            </div>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.pump_operator_dashboard')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('pump_operator_dashboard', 1,
                                    !empty($manage_module_enable['pump_operator_dashboard']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['pump_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['pump_expiry_date']))
                                        @if(strtotime($module_activation_data['pump_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['pump_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('pump_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['pump_interval']) ?
                                    $module_activation_data['pump_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('pump_length', !empty($module_activation_data['pump_length']) ?
                                    $module_activation_data['pump_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('pump_activated_on', !empty($module_activation_data['pump_activated_on']) ?
                                    $module_activation_data['pump_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('pump_expiry_date', !empty($module_activation_data['pump_expiry_date']) ?
                                    $module_activation_data['pump_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('pump_price', !empty($module_activation_data['pump_price']) ?
                                    $module_activation_data['pump_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                               
                                </div>
                                
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('lang_v1.customer_interest_deduction')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('customer_interest_module', 1,
                                    !empty($manage_module_enable['customer_interest_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['customer_interest_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['customer_interest_expiry_date']))
                                        @if(strtotime($module_activation_data['customer_interest_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['customer_interest_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('customer_interest_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['customer_interest_interval']) ?
                                    $module_activation_data['customer_interest_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('customer_interest_length', !empty($module_activation_data['customer_interest_length']) ?
                                    $module_activation_data['customer_interest_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('customer_interest_activated_on', !empty($module_activation_data['customer_interest_activated_on']) ?
                                    $module_activation_data['customer_interest_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('customer_interest_expiry_date', !empty($module_activation_data['customer_interest_expiry_date']) ?
                                    $module_activation_data['customer_interest_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('customer_interest_price', !empty($module_activation_data['customer_interest_price']) ?
                                    $module_activation_data['customer_interest_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                               
                                </div>
                                
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('lang_v1.day_end_module')</label>
                                </div>
                                <div class="col-md-1">
                                    {!! Form::checkbox('day_end_module', 1,
                                    !empty($manage_module_enable['day_end_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-1">
                                    @if(empty($module_activation_data['day_end_expiry_date']))
                                    <span class="badge badge-danger">not set</span>
                                    @endif
                                    @if(!empty($module_activation_data['day_end_expiry_date']))
                                        @if(strtotime($module_activation_data['day_end_expiry_date']) >= time())
                                            <span class="label label-pill label-primary">active</span>
                                        @endif
                                        
                                        @if(strtotime($module_activation_data['day_end_expiry_date']) < time())
                                            <span class="label label-pill label-danger">expired</span>
                                        @endif
                                        
                                    @endif
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::select('day_end_interval', ['Years' => 'Years', 'Months' => 'Months', 'Days' => 'Days'], !empty($module_activation_data['day_end_interval']) ?
                                    $module_activation_data['day_end_interval'] : null, [
                                                'id' => 'status',
                                                'class' => 'form-control act_interval',
                                                'style' => 'width:100%',
                                                'placeholder' => __('lang_v1.all')
                                            ])
                                        !!}   
                        
                                </div>
                                
                                <div class="col-md-1">
                                    {!! Form::text('day_end_length', !empty($module_activation_data['day_end_length']) ?
                                    $module_activation_data['day_end_length'] : null, ['class' => 'form-control act_length']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    {!! Form::date('day_end_activated_on', !empty($module_activation_data['day_end_activated_on']) ?
                                    $module_activation_data['day_end_activated_on'] : null, ['class' => 'form-control act_on']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::date('day_end_expiry_date', !empty($module_activation_data['day_end_expiry_date']) ?
                                    $module_activation_data['day_end_expiry_date'] : null, ['class' => 'form-control act_expiry', 'disabled' => 'disabled']) !!}
                                </div>
                                <div class="col-md-2">
                                    {!! Form::text('day_end_price', !empty($module_activation_data['day_end_price']) ?
                                    $module_activation_data['day_end_price'] : null, ['class' => 'form-control']) !!}
                                </div>
                               
                                </div>
                            
                            
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> Other Permissions</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row">
                                    <div class="col-md-2">
                                        <label class="search_label">@lang('superadmin::lang.enable_crm')</label>
                                    </div>
                                    <div class="col-md-2">
                                    {!! Form::hidden('enable_crm', 0) !!}
                                        {!! Form::checkbox('enable_crm', 1, !empty($manage_module_enable['enable_crm']) ? true :
                                        false,
                                        ['class' => 'input-icheck-red ch_select']) !!}
                                    </div>
                                    
                                    <div class="col-md-2">
                                        <label class="search_label">@lang('superadmin::lang.catalogue_qr')</label>
                                    </div>
                                    <div class="col-md-2">
                                    {!! Form::hidden('catalogue_qr', 0) !!}
                                        {!! Form::checkbox('catalogue_qr', 1,
                                        !empty($manage_module_enable['catalogue_qr']) ? true : false, ['class' =>
                                        'input-icheck-red
                                        ch_select']) !!}
                                    </div>
          
                                    <div class="col-md-2">
                                        <label class="search_label">@lang('superadmin::lang.enable_sale_cmsn_agent')</label>
                                    </div>
                                    <div class="col-md-2">
                                    {!! Form::hidden('enable_sale_cmsn_agent', 0) !!}
                                        {!! Form::checkbox('enable_sale_cmsn_agent', 1,
                                        !empty($manage_module_enable['enable_sale_cmsn_agent']) ? true : false, ['class' =>
                                        'input-icheck-red
                                        ch_select']) !!}
                                    </div>
                                    
                                </div><hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.monthly_total_sales_volumn')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('monthly_total_sales_volumn', 0) !!}
                                    {!! Form::checkbox('monthly_total_sales_volumn', 1,
                                    !empty($manage_module_enable['monthly_total_sales_volumn']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.customer_order_own_customer')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('customer_order_own_customer', 0) !!}
                                    {!! Form::checkbox('customer_order_own_customer', 1,
                                    !empty($manage_module_enable['customer_order_own_customer']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.customer_settings')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('customer_settings', 0) !!}
                                    {!! Form::checkbox('customer_settings', 1,
                                    !empty($manage_module_enable['customer_settings']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                            </div> <hr>   
                            <div class="row">    
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.customer_order_general_customer')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('customer_order_general_customer', 0) !!}
                                    {!! Form::checkbox('customer_order_general_customer', 1,
                                    !empty($manage_module_enable['customer_order_general_customer']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                 <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.customer_to_directly_in_panel')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('customer_to_directly_in_panel', 0) !!}
                                    {!! Form::checkbox('customer_to_directly_in_panel', 1,
                                    !empty($manage_module_enable['customer_to_directly_in_panel']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.member_registration')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('member_registration', 0) !!}
                                    {!! Form::checkbox('member_registration', 1,
                                    !empty($manage_module_enable['member_registration']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                            </div><hr>
                             <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('contact.enable_separate_customer_statement_no')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('enable_separate_customer_statement_no', 0) !!}
                                    {!! Form::checkbox('enable_separate_customer_statement_no', 1,
                                    !empty($manage_module_enable['enable_separate_customer_statement_no']) ? true : false,
                                    ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('contact.edit_customer_statement')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('edit_customer_statement', 0) !!}
                                    {!! Form::checkbox('edit_customer_statement', 1,
                                    !empty($manage_module_enable['edit_customer_statement']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                {{-- <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.issue_customer_bill')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('issue_customer_bill', 0) !!}
                                    {!! Form::checkbox('issue_customer_bill', 1,
                                    !empty($manage_module_enable['issue_customer_bill']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                --}}
                                
                                
                                
                            </div><hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.home_dashboard')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('home_dashboard', 0) !!}
                                    {!! Form::checkbox('home_dashboard', 1,
                                    !empty($manage_module_enable['home_dashboard']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.stock_adjustment')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('stock_adjustment', 0) !!}
                                    {!! Form::checkbox('stock_adjustment', 1,
                                    !empty($manage_module_enable['stock_adjustment']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                 <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.tables')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('tables', 0) !!}
                                    {!! Form::checkbox('tables', 1,
                                    !empty($manage_module_enable['tables']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                            </div><hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.type_of_service')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('type_of_service', 0) !!}
                                    {!! Form::checkbox('type_of_service', 1,
                                    !empty($manage_module_enable['type_of_service']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.expenses')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('expenses', 0) !!}
                                    {!! Form::checkbox('expenses', 1,
                                    !empty($manage_module_enable['expenses']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.modifiers')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('modifiers', 0) !!}
                                    {!! Form::checkbox('modifiers', 1,
                                    !empty($manage_module_enable['modifiers']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                
                                
                            </div><hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.kitchen')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('kitchen', 0) !!}
                                    {!! Form::checkbox('kitchen', 1,
                                    !empty($manage_module_enable['kitchen']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.cache_clear')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('cache_clear', 0) !!}
                                    {!! Form::checkbox('cache_clear', 1,
                                    !empty($manage_module_enable['cache_clear']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.customer_interest_deduct_option')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('customer_interest_deduct_option', 0) !!}
                                    {!! Form::checkbox('customer_interest_deduct_option', 1,
                                    !empty($manage_module_enable['customer_interest_deduct_option']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                               
                            </div><hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.upload_images')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('upload_images', 0) !!}
                                    {!! Form::checkbox('upload_images', 1,
                                    !empty($manage_module_enable['upload_images']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.dsr_module')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('dsr_module', 0) !!}
                                    {!! Form::checkbox('dsr_module', 1,
                                    !empty($manage_module_enable['dsr_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.discount_module')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('discount_module', 0) !!}
                                    {!! Form::checkbox('discount_module', 1,
                                    !empty($manage_module_enable['discount_module']) ? true : false, ['class' =>
                                    'input-icheck-red
                                    ch_select']) !!}
                                </div>
                                
                                
                            </div>
                            <hr>
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.tpos_module')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('tpos_module', 0) !!}
                                    {!! Form::checkbox('tpos_module', 1,
                                    !empty($manage_module_enable['tpos_module']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                    
                                </div> 
                                
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.stock_conversion_module')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('stock_conversion_module', 0) !!}
                                    {!! Form::checkbox('stock_conversion_module', 1,
                                    !empty($manage_module_enable['stock_conversion_module']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                    
                                </div> 
                                
                                
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.list_credit_sales_page')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('list_credit_sales_page', 0) !!}
                                    {!! Form::checkbox('list_credit_sales_page', 1,
                                    !empty($manage_module_enable['list_credit_sales_page']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                    
                                </div> 
                                
                            </div>
                            
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.docmanagement_module')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('docmanagement_module', 0) !!}
                                    {!! Form::checkbox('docmanagement_module', 1,
                                    !empty($manage_module_enable['docmanagement_module']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                    
                                </div> 
                                 
                                <div class="col-md-2">
                                    <label class="search_label">@lang('superadmin::lang.duplicate_slip_numbers')</label>
                                </div>
                                <div class="col-md-2">
                                {!! Form::hidden('duplicate_slip_numbers', 0) !!}
                                    {!! Form::checkbox('duplicate_slip_numbers', 1,
                                    !empty($manage_module_enable['duplicate_slip_numbers']) ? true : false, ['class' => 'input-icheck-red ch_select']) !!}
                                    
                                </div>  
                            </div>
                            
                            
                         </div>
                          
                    </div>
                    
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                      <div class="card-header text-center">
                        <h4> Payment Options</h4>
                        <hr>
                      </div>
                      <div class="card-body">
                          <div class="row">
                        <div class="col-sm-12">
                        <label class="search_label">@lang('lang_v1.payment_methods'):</label>
                 
                    @foreach ($business_locations as $business_location)
                        <!--Accordion wrapper-->
                            <div class="accordion md-accordion" id="accordionEx{{$business_location->id}}" role="tablist"
                                 aria-multiselectable="true">

                                <div class="card">
                                    <div class="card-header" role="tab" id="headingTwo{{$business_location->id}}">
                                        <a class="collapsed" data-toggle="collapse"
                                           data-parent="#accordionEx{{$business_location->id}}"
                                           href="#collapseTwo{{$business_location->id}}" aria-expanded="false"
                                           aria-controls="collapseTwo{{$business_location->id}}">
                                            <h5 class="mb-0 text-black">
                                                <label class="search_label">{{$business_location->name}}</label>
                                                <i class="fa fa-angle-down rotate-icon pull-right"></i>
                                            </h5>
                                        </a>
                                    </div>
                                    <div id="collapseTwo{{$business_location->id}}" class="collapse" role="tabpanel"
                                         aria-labelledby="headingTwo{{$business_location->id}}"
                                         data-parent="#accordionEx{{$business_location->id}}">
                                        <div class="card-body" style="margin-bottom: 10px;">
                                            <hr>
                                            @php
                                                $default_payment_accounts = json_decode($business_location->default_payment_accounts);
                                            @endphp
                                            <table class="table table-condensed table-striped">
                                                <thead>
                                                <tr>
                                                    <th class="text-center">@lang('lang_v1.payment_method')</th>
                                                    <th class="text-center">@lang('lang_v1.enable')</th>
                                                    
                                                    <th class="text-center">@lang('superadmin::lang.purchases')</th>
                                                    <th class="text-center">@lang('superadmin::lang.sales')</th>
                                                    <th class="text-center">@lang('superadmin::lang.expenses')</th>
                                                    <th class="text-center">@lang('superadmin::lang.purchase_return')</th>
                                                    <th class="text-center">@lang('superadmin::lang.sales_return')</th>
                                                    
                                                    <th class="text-center @if(empty($accounts)) hide @endif">
                                                        @lang('lang_v1.default_account_groups')
                                                        @show_tooltip(__('lang_v1.default_account_help'))</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                
                                                @php $i = 0; @endphp   
                                                @foreach($default_payment_accounts as $key => $value)
                                                    <tr>
                                                        <td class="text-center">{{ucfirst($key)}}</td>
                                                        <td class="text-center">
                                                            <input type="hidden" name="default_payment_accounts[{{$business_location->id}}][name][{{$i}}]" value="{{$key}}">
                                                            {!! Form::select('default_payment_accounts['.$business_location->id.'][is_enabled]['.$i.']', ['0' => 'Not Active','1' => 'Active'],!empty($default_payment_accounts->$key->is_enabled) ? $default_payment_accounts->$key->is_enabled : 0,['class' => 'form-control input-sm','placeholder' => __('superadmin::lang.please_select'),'required']); !!}</td>
                                                        
                                                        <td>
                                                            {!! Form::hidden('default_payment_accounts['.$business_location->id.'][is_purchase_enabled]['.$i.']', 0) !!}
                                                            {!! Form::checkbox('default_payment_accounts['.$business_location->id.'][is_purchase_enabled]['.$i.']', 1, 
                                                                !empty($default_payment_accounts->$key->is_purchase_enabled) ?
                                                                $default_payment_accounts->$key->is_purchase_enabled : 0, ['class' => 'form-control input-sm']); !!}
                                                        </td>
                                                        
                                                        <td>
                                                            {!! Form::hidden('default_payment_accounts['.$business_location->id.'][is_sale_enabled]['.$i.']', 0) !!}
                                                            {!! Form::checkbox('default_payment_accounts['.$business_location->id.'][is_sale_enabled]['.$i.']', 1, 
                                                                !empty($default_payment_accounts->$key->is_sale_enabled) ?
                                                                $default_payment_accounts->$key->is_sale_enabled : 0, ['class' => 'form-control input-sm']); !!}
                                                        </td>
                                                        
                                                        <td>
                                                            {!! Form::hidden('default_payment_accounts['.$business_location->id.'][is_expense_enabled]['.$i.']', 0) !!}
                                                            {!! Form::checkbox('default_payment_accounts['.$business_location->id.'][is_expense_enabled]['.$i.']', 1, 
                                                                !empty($default_payment_accounts->$key->is_expense_enabled) ?
                                                                $default_payment_accounts->$key->is_expense_enabled : 0, ['class' => 'form-control input-sm']); !!}
                                                        </td>
                                                        
                                                        <td>
                                                            {!! Form::hidden('default_payment_accounts['.$business_location->id.'][is_purchase_return_enabled]['.$i.']', 0) !!}
                                                            {!! Form::checkbox('default_payment_accounts['.$business_location->id.'][is_purchase_return_enabled]['.$i.']', 1, 
                                                                !empty($default_payment_accounts->$key->is_purchase_return_enabled) ?
                                                                $default_payment_accounts->$key->is_purchase_return_enabled : 0, ['class' => 'form-control input-sm']); !!}
                                                        </td>
                                                        
                                                        <td>
                                                            {!! Form::hidden('default_payment_accounts['.$business_location->id.'][is_sale_return_enabled]['.$i.']', 0) !!}
                                                            {!! Form::checkbox('default_payment_accounts['.$business_location->id.'][is_sale_return_enabled]['.$i.']', 1, 
                                                                !empty($default_payment_accounts->$key->is_sale_return_enabled) ?
                                                                $default_payment_accounts->$key->is_sale_return_enabled : 0, ['class' => 'form-control input-sm']); !!}
                                                        </td>

                                                        
                                                        <td class="text-center @if(empty($accounts)) hide @endif">
                                                            {!! Form::select('default_payment_accounts['.$business_location->id.'][account]['.$i.']', $account_groups,
                                                            !empty($default_payment_accounts->$key->account) ?
                                                            $default_payment_accounts->$key->account :
                                                            null, ['class' => 'form-control input-sm', 'id' => 'account_'.$key,'placeholder' => __('superadmin::lang.please_select'),'required']);
                                                            !!}
                                                        </td>
                                                        <td>
                                                            @if($i == 0 || sizeof(json_decode($business_location->default_payment_accounts,true)) == 1)
                                                                    <button type="button" class="btn btn-success add-row" data-id="{{$business_location->id}}"> + </button>
                                                            @endif
                                                            @if(!empty($default_payment_accounts->$key->is_custom) && $default_payment_accounts->$key->is_custom == 1)
                                                                <button type="button" class="btn btn-danger remove-row"> - </button>
                                                                <input type="hidden" name="default_payment_accounts[{{$business_location->id}}][is_custom][{{$i}}]" value="1">
                                                            @else
                                                                <input type="hidden" name="default_payment_accounts[{{$business_location->id}}][is_custom][{{$i}}]" value="0">
                                                                
                                                            @endif 
                                                        </td>
                                                        
                                                    </tr>
                                                    @php $i++; @endphp
                                                @endforeach
                                                <input type="hidden" id="current_id" value="{{$i}}">
                                                </tbody>
                                            </table>
                                            <hr>
                                        </div>
                                    </div>

                                </div>

                            </div>
                        @endforeach
                    </div>
                    </div>
                     </div>
                      
                </div>
                
                    <div class="card text-left bg-success"  style="border: 1px solid #D9D8D8; margin-bottom: 30px;">
                          <div class="card-header text-center">
                            <h4> General</h4>
                            <hr>
                          </div>
                          <div class="card-body">
                              <div class="row">
                                <div class="col-md-3">
                                    <div class="form-group">
                                        <label>@lang('superadmin::lang.login_page_showing_type')</label>
                                        {!! Form::select('background_showing_type', ['only_background_image'
                                        =>__('superadmin::lang.only_background_image'), 'background_image_and_logo' =>
                                        __('superadmin::lang.background_image_and_logo')],
                                        $business_details->background_showing_type , ['class' => 'form-control',
                                        'placeholder' => __('superadmin::lang.please_select')]) !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('background_image', __( 'superadmin::lang.background_image' ) . ':') !!}
                                        {!! Form::file('background_image', ['accept' => 'image/*']); !!}
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-group">
                                        {!! Form::label('logo', __( 'superadmin::lang.logo' ) . ':') !!}
                                        {!! Form::file('logo', ['accept' => 'image/*']); !!}
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="row">
                                <h3> @lang('superadmin::lang.with_variables')</h3>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-5">

                            </div>
                            <div class="col-md-3">
                                {!! Form::label('current_value', __('superadmin::lang.current_values'), ['class' =>
                                'search_label']) !!}
                            </div>
                            <div class="col-md-4">

                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_branches')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_branches]',
                                    !empty($current_values['number_of_branches']) ? $current_values['number_of_branches'] :
                                    null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '0', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_users')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_users]',
                                    !empty($current_values['number_of_users']) ? $current_values['number_of_users'] : null,
                                    ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '1', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_customers')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_customers]',
                                    !empty($current_values['number_of_customers']) ? $current_values['number_of_customers'] :
                                    null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '4', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_products')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_products]',
                                    !empty($current_values['number_of_products']) ? $current_values['number_of_products'] :
                                    null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '2', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_periods')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_periods]',
                                    !empty($current_values['number_of_periods']) ? $current_values['number_of_periods'] : null,
                                    ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '3', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                        </br>
                        <div class="row">
                            <div class="col-md-5">
                                <label class="search_label">@lang('superadmin::lang.number_of_stores')</label>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    {!! Form::number('current_values[number_of_stores]',
                                    !empty($current_values['number_of_stores']) ? $current_values['number_of_stores'] : null,
                                    ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button class="btn btn-primary btn-xs btn-modal" data-container=".option_modal"
                                        data-href="{{action('\Modules\Superadmin\Http\Controllers\CompanyPackageVariableController@getOptionVariables', [ 'id' => '5', 'business_id' => $business->id])}}">@lang('superadmin::lang.enter_variables')</button>
                            </div>
                        </div>
                    </div>
                            </div>
                         </div>
                          
                    </div>
                 
                    
                @endcan
            </div>
        </div>
        <div class="box" style="padding-left: 50px">
            
        </div>
        <input type="hidden" name="opt_vars" id="opt_vars" value="">
        <div class="clearfix"></div>
        
        <div class="clearfix"></div>
        <button type="submit" class="btn btn-danger pull-right"
                id="custom_permission_btn">@lang('superadmin::lang.save')</button>
        <div class="clearfix"></div>
        {!! Form::close() !!}
        <div class="modal fade option_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
        </div>

    </section>
    <!-- /.content -->
    @endif
    
    @if(empty($subscription))
        <div class="col-sm-12 alert alert-danger">@lang('superadmin::lang.please_activate_subscription')</div>
    @endif
    
    

@endsection

@section('javascript')
    <script>
        $('#currency_id_manage').select2();
    </script>
    <script>
        $(document).ready(function() {
            $('.check_all').change(function() {
                var isChecked = $(this).prop('checked');
                $(this).closest('.row').find('.ch_select').prop('checked', isChecked);
            });
        });
        
        
        $('#test_sms_btn').click( function() {
        var test_number = $('#test_number').val();
        if (test_number.trim() == '') {
            toastr.error('{{__("lang_v1.test_number_is_required")}}');
            $('#test_number').focus();

            return false;
        }

        var data = {
            url: $('#sms_settings_url').val(),
            send_to_param_name: $('#send_to_param_name').val(),
            msg_param_name: $('#msg_param_name').val(),
            request_method: $('#request_method').val(),
            param_1: $('#sms_settings_param_key1').val(),
            param_2: $('#sms_settings_param_key2').val(),
            param_3: $('#sms_settings_param_key3').val(),
            param_4: $('#sms_settings_param_key4').val(),
            param_5: $('#sms_settings_param_key5').val(),
            param_6: $('#sms_settings_param_key6').val(),
            param_7: $('#sms_settings_param_key7').val(),
            param_8: $('#sms_settings_param_key8').val(),
            param_9: $('#sms_settings_param_key9').val(),
            param_10: $('#sms_settings_param_key10').val(),

            param_val_1: $('#sms_settings_param_val1').val(),
            param_val_2: $('#sms_settings_param_val2').val(),
            param_val_3: $('#sms_settings_param_val3').val(),
            param_val_4: $('#sms_settings_param_val4').val(),
            param_val_5: $('#sms_settings_param_val5').val(),
            param_val_6: $('#sms_settings_param_val6').val(),
            param_val_7: $('#sms_settings_param_val7').val(),
            param_val_8: $('#sms_settings_param_val8').val(),
            param_val_9: $('#sms_settings_param_val9').val(),
            param_val_10: $('#sms_settings_param_val10').val(),
            test_number: test_number
        };

        $.ajax({
            method: 'post',
            data: data,
            url: "{{ action('BusinessController@testSmsConfiguration') }}",
            dataType: 'json',
            success: function(result) {
                if (result.success == true) {
                    swal({
                        text: result.msg,
                        icon: 'success'
                    });
                } else {
                    swal({
                        text: result.msg,
                        icon: 'error'
                    });
                }
            },
        });
    });
       

        let  opt_val_array = [];

        $('#custom_permission_btn').click(function(e){
            e.preventDefault();
            if(Array.isArray(opt_val_array) && opt_val_array.length){
                $('#opt_vars').val(JSON.stringify(opt_val_array));
            }
            $('#custom_permission_form').submit();
        });
        @php
            $manage_module_enable = (array) $manage_module_enable;
        @endphp
        @if(is_array($manage_module_enable) && empty($manage_module_enable['access_sms_settings']))
        $('.sms_setting_div').addClass('hide');
        @endif

        
        $('#sms_settings_checkbox').change(function() {
            if ($(this).is(':checked')) {
                $('div.sms_setting_div').removeClass('hide');
            } else {
                $('div.sms_setting_div').addClass('hide');
            }
        });
    

        $('#customer_interest_deduct_option').on('ifChecked', function(event){
            $('div.customer_interest_deduct_option').removeClass('hide');
        });
        $('#customer_interest_deduct_option').on('ifUnchecked', function(event){
            $('div.customer_interest_deduct_option').addClass('hide');
        });

        @foreach ($module_permission_locations as $module)
        $('.{{$module}}').on('ifChecked', function(event){
            $('.{{$module}}_locations').removeClass('hide');
        });
        $('.{{$module}}').on('ifUnChecked', function(event){
            $('.{{$module}}_locations').addClass('hide');
        });
        @endforeach
        $(document).ready(function(){
            let sale_date = @if(!empty($sale_import_date)) '{{$sale_import_date}}' @else new Date() @endif;
            $('#sale_import_date').datepicker("setDate", sale_date);

            let purchase_date = @if(!empty($purchase_import_date)) {{$purchase_import_date}} @else new Date() @endif;
            $('#purchase_import_date').datepicker("setDate", purchase_date);

        })
        
        $(".act_interval, .act_on, .act_length").on("input change", function() {
            var interval = $(this).closest('.row').find('.act_interval').val();
            var length = $(this).closest('.row').find('.act_length').val();
            var act_on = $(this).closest('.row').find('.act_on').val();
            
            if(interval && length && act_on){
                $(this).closest('.row').find('.act_expiry').prop("disabled", false);
                
                switch(interval){
                    case "Years":
                        var expiry = addYears(new Date(act_on),length).toISOString().substr(0, 10);
                        
                        console.log(expiry);
                    
                        $(this).closest('.row').find('.act_expiry').val(expiry);
                        
                        break;
                    case "Months" : 
                        var expiry = addMonths(new Date(act_on),length).toISOString().substr(0, 10);
                        
                        $(this).closest('.row').find('.act_expiry').val(expiry);
                        
                        break;
                    case "Days":
                        
                        var expiry = addDays(act_on,length).toISOString().substr(0, 10);
                        
                        $(this).closest('.row').find('.act_expiry').val(expiry);
                        
                        break;
                }
                
                 $(this).closest('.row').find('.act_expiry').prop("disabled", true);
            }
        });
        
        function addDays(date, days) {
          var result = new Date(date);
          result.setDate(result.getDate() + +days);
          return result;
        }
        
        
        function addMonths(date, months) {
            var d = date.getDate();
            date.setMonth(date.getMonth() + +months);
            if (date.getDate() != d) {
              date.setDate(0);
            }
            return date;
        }
        
        function addYears(date, months) {
            var currentDate = new Date(date);
            currentDate.setFullYear(currentDate.getFullYear() + +months);
            var d = currentDate;
            
            return d;
        }
        
        $(document).on('click', '.add-row', function() {
            var current_id = $("#current_id").val();
            var new_id = parseInt(current_id) + 1;
            $("#current_id").val(new_id);
            
            var location_id = $(this).data('id');
            var account_options = "";
            $.each(<?php echo json_encode($account_groups); ?>, function(key, value) {
               account_options += '<option value="' + key + '">' + value + '</option>';
            });
            
            var newRow = `
              <tr>
                <td class="text-center"><input type="text" name="default_payment_accounts[`+location_id+`][name][`+new_id+`]" class="form-control" required></td>
                <td class="text-center">
                  <select class="form-control input-sm" name="default_payment_accounts[`+location_id+`][is_enabled][`+new_id+`]" required>
                        <option value="">Select one</option>
                        <option value="0">Not Active </option>
                        <option value = "1" >Active </option>
                  </select>
                </td>
                <td>
                    <input type="hidden" name="default_payment_accounts[`+location_id+`][is_purchase_enabled][`+new_id+`]" value="0">
                    <input class="form-control input-sm" name="default_payment_accounts[`+location_id+`][is_purchase_enabled][`+new_id+`]" type="checkbox" value="1">
                </td>
                
                <td>
                    <input type="hidden" name="default_payment_accounts[`+location_id+`][is_sale_enabled][`+new_id+`]" value="0">
                    <input class="form-control input-sm" name="default_payment_accounts[`+location_id+`][is_sale_enabled][`+new_id+`]" type="checkbox" value="1">
                </td>
                
                <td>
                    <input type="hidden" name="default_payment_accounts[`+location_id+`][is_expense_enabled][`+new_id+`]" value="0">
                    <input class="form-control input-sm" name="default_payment_accounts[`+location_id+`][is_expense_enabled][`+new_id+`]" type="checkbox" value="1">
                </td>
                
                <td>
                    <input type="hidden" name="default_payment_accounts[`+location_id+`][is_purchase_return_enabled][`+new_id+`]" value="0">
                    <input class="form-control input-sm" name="default_payment_accounts[`+location_id+`][is_purchase_return_enabled][`+new_id+`]" type="checkbox" value="1">
                </td>
                
                <td>
                    <input type="hidden" name="default_payment_accounts[`+location_id+`][is_sale_return_enabled][`+new_id+`]" value="0">
                    <input class="form-control input-sm" name="default_payment_accounts[`+location_id+`][is_sale_return_enabled][`+new_id+`]" type="checkbox" value="1">
                </td>
                
                <td class="text-center">
                  <select class="form-control input-sm" name="default_payment_accounts[`+location_id+`][account][`+new_id+`]">
                    <option value="">Please select</option>
                    `+account_options+`
                  </select>
                </td>
                <td>
                  <button type="button" class="btn btn-danger remove-row"> - </button>
                  <input type="hidden" name="default_payment_accounts[`+location_id+`][is_custom][`+new_id+`]" value="1">
                </td>
              </tr>
            `;
            $(this).closest('tr').after(newRow);
          });
        
          // Remove row
          $(document).on('click', '.remove-row', function() {
            $(this).closest('tr').remove();
          });
          
          $(document).on('click','#add_amount',function(){
              var item_element = `
                <div class="form-group col-sm-4 added-amount">
                    <div class="input-group">
                      {!! Form::text('reminder_phone[]', null, ['class' => 'form-control', 'required','placeholder' => __(
                        'superadmin::lang.phone_no')]); !!}
                      <span  class="input-group-addon bg-danger remove_amount"> - </span>
                    </div>
                  </div>
              `;
              
              $("#amounts_row").append(item_element);
          });
          
          $(document).on('click', '.remove_amount', function () {
            $(this).closest('.added-amount').remove();
        });

        
    </script>
    
    <script>
   const pickr = Pickr.create({
    el: '.ns-font-color-picker',
    theme: 'classic', // or 'monolith', or 'nano'

    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 0.95)',
        'rgba(156, 39, 176, 0.9)',
        'rgba(103, 58, 183, 0.85)',
        'rgba(63, 81, 181, 0.8)',
        'rgba(33, 150, 243, 0.75)',
        'rgba(3, 169, 244, 0.7)',
        'rgba(0, 188, 212, 0.7)',
        'rgba(0, 150, 136, 0.75)',
        'rgba(76, 175, 80, 0.8)',
        'rgba(139, 195, 74, 0.85)',
        'rgba(205, 220, 57, 0.9)',
        'rgba(255, 235, 59, 0.95)',
        'rgba(255, 193, 7, 1)'
    ],

    components: {

        // Main components
        preview: true,
        opacity: true,
        hue: true,

        // Input / output Options
        interaction: {
            hex: true,
            input: true,
            clear: true,
            save: true,
            useAsButton: false,
        }
    }
  }).on('save', (color, instance) => {
      $('#ns_font_color').val(color.toHEXA().toString());
  });
  
  const pickr2 = Pickr.create({
    el: '.ns-background-color-picker',
    theme: 'classic', // or 'monolith', or 'nano'

    swatches: [
        'rgba(244, 67, 54, 1)',
        'rgba(233, 30, 99, 0.95)',
        'rgba(156, 39, 176, 0.9)',
        'rgba(103, 58, 183, 0.85)',
        'rgba(63, 81, 181, 0.8)',
        'rgba(33, 150, 243, 0.75)',
        'rgba(3, 169, 244, 0.7)',
        'rgba(0, 188, 212, 0.7)',
        'rgba(0, 150, 136, 0.75)',
        'rgba(76, 175, 80, 0.8)',
        'rgba(139, 195, 74, 0.85)',
        'rgba(205, 220, 57, 0.9)',
        'rgba(255, 235, 59, 0.95)',
        'rgba(255, 193, 7, 1)'
    ],

    components: {

        // Main components
        preview: true,
        opacity: true,
        hue: true,

        // Input / output Options
        interaction: {
            hex: true,
            input: true,
            clear: true,
            save: true,
            useAsButton: false,
        }
    }
  }).on('save', (color, instance) => {
      $('#ns_background_color').val(color.toHEXA().toString());
  });
</script>

<script>
    $('#vat_registered').on('change', function() {
        $('#vat_number_section').toggle($(this).val() === 'yes');
    });
</script>

@endsection

