@php
$contact_fields = !empty(session('business.contact_fields')) ? session('business.contact_fields') : [];
$business_id = request()->session()->get('user.business_id');
$supplier_groups = App\ContactGroup::where('business_id',$business_id)
                        ->where(function ($query) {
                            $query->where('contact_groups.type', 'supplier')
                                ->orWhere('contact_groups.type', 'both');
                        })->pluck('name','id');
                        
    $customer_groups = App\ContactGroup::where('business_id',$business_id)
                        ->where(function ($query) {
                            $query->where('contact_groups.type', 'customer')
                                ->orWhere('contact_groups.type', 'both');
                        })->pluck('name','id');
                        
    $both_groups = App\ContactGroup::where('business_id',$business_id)
                        ->where(function ($query){
                            $query->where('contact_groups.type', 'both');
                        })->pluck('name','id');
    $customers = \App\Contact::customersDropdown($business_id, false);
@endphp

@php

$notification_numbers = json_decode($contact->notification_contacts,true);

@endphp



<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('ContactController@update', [$contact->id]), 'method' => 'PUT', 'id' =>
    'contact_edit_form']) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.edit_contact')</h4>
    </div>

    <div class="modal-body">

      <div class="row">

        <div class="col-md-3 ">
          <div class="form-group">
            {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::select('type', $types, $contact->type, ['class' => 'form-control', 'id' =>
              'contact_type','placeholder' => __('messages.please_select'), 'required']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-3 ">
          <div class="form-group">
            {!! Form::label('name', __('contact.name') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::text('name', $contact->name, ['class' => 'form-control','placeholder' => __('contact.name'),
              'required']); !!}
            </div>
          </div>
        </div>
         @if(isset($customerSettings->need_to_send_sms) && $customerSettings->need_to_send_sms == 1)
        <div class="col-md-3   ">
          <div class="form-group">
            {!! Form::label('name', __('contact.should_notify') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::select('should_notify', ['1' => __('messages.yes'), '0' => __('messages.no')], $contact->should_notify, ['placeholder'
                  => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
            </div>
          </div>
        </div>
        @endif
        @if(isset($customerSettings->credit_notification_type) && $customerSettings->credit_notification_type == 1 )
         <div class="col-md-3   ">
          <div class="form-group">
            {!! Form::label('name', __('contact.credit_notification') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::select('credit_notification', ['settlement' => __('contact.settlement'), 'customer_bill' => __('contact.bill_to_customer'),'pumper_dashboard' => __('contact.pumper_dashboard')], $contact->credit_notification, ['placeholder'
                  => __( 'contact.none' ), 'class' => 'form-control']); !!}
            </div>
          </div>
        </div>
        @endif

        <div class="col-md-4 fi-gr manual_bill_div">
          <div class="form-group">
              {!! Form::label('manual_bill_settlement', 'Manual Bill to Settlement:') !!}
              <div class="input-group">
                  <span class="input-group-addon">
                      <i class="fa fa-file-text-o"></i>
                  </span>
                  {!! Form::select('manual_bill_settlement', [0 => 'No', 1 => 'Yes'], old('manual_bill_settlement', $contact->manual_bill_settlement ?? 0), [
                      'class' => 'form-control',
                      'id' => 'manual_bill_settlement',
                      'placeholder' => __('messages.please_select'),
                      'required'
                  ]) !!}
              </div>
          </div>
      </div>
    <input type="hidden" name="is_modal" id="is_modal" value="1">
        
        <div class="clearfix"></div>
        @if( isset($customerSettings->sub_customer) && $customerSettings->sub_customer == 1  )
        <div class="col-md-3">
            {!! Form::checkbox('sub_customer', 1, !empty($contact->sub_customer) ? true : false, ['class' => 'sub_customer']) !!} {{__('contact.sub_customer')}}
        </div>
        @endif
        
        <div class="col-md-9 sub_customer_field @if(empty($contact->sub_customer)) hide @endif">
          <div class="form-group">
            {!! Form::label('name', __('contact.sub_customers') . ':*') !!}
            {!! Form::select('sub_customers[]', $customers, !empty($contact->sub_customers) ? json_decode($contact->sub_customers) : null, ['class' => 'form-control select2','multiple']); !!}
          </div>
        </div>
        
        
        <div class="clearfix"></div>
        <div class="col-md-3 ">
          <div class="form-group">
            {!! Form::label('contact_id', __('lang_v1.contact_id') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-id-badge"></i>
              </span>
              <input type="hidden" id="hidden_id" value="{{$contact->id}}">
              {!! Form::text('contact_id', $contact->contact_id, ['class' => 'form-control','placeholder' =>
              __('lang_v1.contact_id'), 'readonly']); !!}
            </div>
          </div>
        </div>
         @if(isset($customerSettings->tax_number) && $customerSettings->tax_number == 1 )
        <div
          class="col-md-3   @if($contact->type=='supplier' && !array_key_exists('supplier_tax_number', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('tax_number', __('contact.tax_no') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-info"></i>
              </span>
              {!! Form::text('tax_number', $contact->tax_number, ['class' => 'form-control', 'placeholder' =>
              __('contact.tax_no')]); !!}
            </div>
          </div>
        </div>
        @endif
         @if(isset($customerSettings->vat_no) && $customerSettings->vat_no == 1 )
        <div
          class="col-md-3">
          <div class="form-group">
            {!! Form::label('vat_number', __('contact.vat_number') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-info"></i>
              </span>
              {!! Form::text('vat_number', $contact->vat_number, ['class' => 'form-control', 'placeholder' =>
              __('contact.vat_number')]); !!}
            </div>
          </div>
        </div>
        @endif
        <div class="col-md-3">
          <div class="form-group">
            {!! Form::label('opening_balance', __('lang_v1.opening_balance') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::text('opening_balance', $opening_balance, ['class' => 'form-control input_number']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-4 ">
          <div class="form-group">
            {!! Form::label('transaction_date', __('lang_v1.transaction_date') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-calendar"></i>
              </span>
              {!! Form::text('transaction_date', null, ['class' => 'form-control input_number', 'id' =>
              'transaction_date_contact','required']); !!}
            </div>

          </div>
        </div>
        @if( isset($customerSettings->pay_term) && $customerSettings->pay_term == 1)
        <div
          class="col-md-4   @if($contact->type=='supplier' && !array_key_exists('supplier_pay_term', $contact_fields)) hide @endif">
          <div class="form-group">
            <div class="multi-input">
              {!! Form::label('pay_term_number', __('contact.pay_term') . ':') !!} @show_tooltip(__('tooltip.pay_term'))
              <br />
              {!! Form::number('pay_term_number', $contact->pay_term_number, ['class' => 'form-control width-40
              pull-left', 'placeholder' => __('contact.pay_term')]); !!}

              {!! Form::select('pay_term_type', ['months' => __('lang_v1.months'), 'days' => __('lang_v1.days')],
              $contact->pay_term_type, ['class' => 'form-control width-60 pull-left','placeholder' =>
              __('messages.please_select')]); !!}
            </div>
          </div>
        </div>
        @endif
        @if( isset($customerSettings->sub_customer) && $customerSettings->customer_group == 1 )
        <div
          class="col-md-4 customer_fields">
          <div class="form-group">
            {!! Form::label('customer_group_id', __('lang_v1.customer_group') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-users"></i>
              </span>
              {!! Form::select('customer_group_id', $customer_groups, $contact->customer_group_id, ['class' =>
              'form-control']); !!}
            </div>
          </div>
        </div>
        @endif

        <div
          class="col-md-4 supplier_fields">
          <div class="form-group">
            {!! Form::label('supplier_group_id', __('lang_v1.supplier_group') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-users"></i>
              </span>
              {!! Form::select('supplier_group_id', ($contact->type == 'supplier') ? $supplier_groups : $customer_groups, $contact->supplier_group_id, ['class' => 'form-control']); !!}
              
              
            </div>
          </div>
        </div>
 @if(isset($customerSettings->credit_limit) && $customerSettings->credit_limit == 1 )
        <div
          class="col-md-4 customer_fields  @if($contact->type=='customer' && !array_key_exists('customer_credit_limit', $contact_fields)) hide backend_hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_credit_limit', $contact_fields)) hide backend_hide @endif">
          <div class="form-group">
            {!! Form::label('credit_limit', __('lang_v1.credit_limit') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::text('credit_limit', $contact->credit_limit, ['class' => 'form-control input_number']); !!}
            </div>
            <p class="help-block">@lang('lang_v1.credit_limit_help')</p>
          </div>
        </div>
        @endif
         @if( isset($customerSettings->password) &&  $customerSettings->password == 1)
        <div
          class="col-md-4 customer_fields  ">
          <div class="form-group">
            {!! Form::label('password', __('business.password') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-key"></i>
              </span>

              {!! Form::password('password', ['class' => 'form-control', 'id' => 'password','placeholder' =>
              __('business.password')]); !!}
            </div>
          </div>
        </div>
        @endif
         @if(isset($customerSettings->confirm_password) && $customerSettings->confirm_password == 1)
        <div
          class="col-md-4 customer_fields  ">
          <div class="form-group">
            {!! Form::label('confirm_password', __('business.confirm_password') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-key"></i>,
              </span>
              {!! Form::password('confirm_password', ['class' => 'form-control', 'id' => 'confirm_password',
              'placeholder' => __('business.confirm_password')]); !!}
            </div>
          </div>
        </div>
        @endif
         @if(isset($customerSettings->add_more_mobile_numbers) && $customerSettings->add_more_mobile_numbers == 1)
        <div class="col-md-12">
          <hr />
          {!! Form::checkbox('add_more_nos', 1, !empty($contact->notification_contacts) ? true : false, ['class' => 'add_more_nos']) !!} {{__('lang_v1.add_more_nos_for_sms')}} <br><br>
          <table class="table table-bordered table-striped" style="width: 100%" id="numbers_table">
            <thead>
                <tr>
                    <th>@lang('lang_v1.name')</th>
                    <th>@lang('lang_v1.phone')</th>
                    @foreach($notifications as $key => $notification)
                        <th>{{$notification['name']}}</th>
                    @endforeach
                    
                    <th>*</th>
                </tr>
            </thead>
            
            <tbody>
               @if(empty($notification_numbers))
                    <tr>
                        <td>
                            {!! Form::text('phone_name[]', null, ['class' => 'form-control notification_fields']); !!}
                        </td>
                        <td>
                            {!! Form::text('phone_number[]', null, ['class' => 'form-control notification_fields']); !!}
                        </td>
                        @foreach($notifications as $key => $notification)
                            <td class="text-center">
                                {!! Form::checkbox($key . '[]', 1, false, ['class' => 'toggler', 'data-toggle_id' => 'base_unit_div']) !!}
                            </td>
                        @endforeach
    
                        <td>
                            <button type="button" id="add_number_row" class="btn btn-success">+</button>
                        </td>
                    </tr>
                @else
                
                    @php $count = 0; @endphp
                
                    @foreach($notification_numbers as $no)
                        <tr>
                            
                            <td>
                            {!! Form::text('phone_name[]', !empty($no['phone_name']) ? $no['phone_name'] : null, ['class' => 'form-control notification_fields']); !!}
                        </td>
                            
                            <td>
                                {!! Form::text('phone_number[]', $no['phone_number'], ['class' => 'form-control', 'required']); !!}
                            </td>
                            @foreach($notifications as $key => $notification)
                                <td class="text-center">
                                    {!! Form::checkbox($key . '[]', 1, !empty($no['notifications'][$key]) ? $no['notifications'][$key] : false, ['class' => 'toggler', 'data-toggle_id' => 'base_unit_div']) !!}
                                </td>
                            @endforeach
        
                            <td>
                                @if($count == 0)
                                    <button type="button" id="add_number_row" class="btn btn-success">+</button>
                                @else
                                    <button type="button" class="btn btn-danger remove-number-row">-</button>
                                @endif
                                
                            </td>
                        </tr>
                        @php $count++; @endphp
                    @endforeach
                
                @endif
                
                
            </tbody>
            
        </table>
        
        <input type="hidden" id="notification_parameters" value="{{$contact->notification_contacts}}" name="notification_parameters">
        
        </div>
        @endif
        @if( isset($customerSettings->email) && $customerSettings->email == 1)
        <div
        <div
          class="col-md-3   @if($contact->type=='supplier' && !array_key_exists('supplier_email', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('email', __('business.email') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::email('email', $contact->email, ['class' => 'form-control','placeholder' =>
              __('business.email')]); !!}
            </div>
          </div>
        </div>
        @endif
        @if( isset($customerSettings->mobile) && $customerSettings->mobile == 1)
        <div
          class="col-md-3   @if($contact->type=='supplier' && !array_key_exists('supplier_mobile', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('mobile', __('contact.mobile') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-mobile"></i>
              </span>
              {!! Form::text('mobile', $contact->mobile, ['class' => 'form-control', 'required', 'placeholder' =>
              __('contact.mobile')]); !!}
            </div>
          </div>
        </div>
        @endif
         @if( isset($customerSettings->alternate_contact_number) && $customerSettings->alternate_contact_number == 1)
        <div
          class="col-md-3  @if($contact->type=='supplier' && !array_key_exists('supplier_alternate_contact_number', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('alternate_number', __('contact.alternate_contact_number') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('alternate_number', $contact->alternate_number, ['class' => 'form-control', 'placeholder'
              => __('contact.alternate_contact_number')]); !!}
            </div>
          </div>
        </div>
        @endif
         @if( isset($customerSettings->landline) && $customerSettings->landline == 1)
        <div
          class="col-md-3  @if($contact->type=='supplier' && !array_key_exists('supplier_landline', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('landline', __('contact.landline') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('landline', $contact->landline, ['class' => 'form-control', 'placeholder' =>
              __('contact.landline')]); !!}
            </div>
          </div>
        </div>
        @endif
         @if( isset($customerSettings->assigned_to) && $customerSettings->assigned_to)
        <div
          class="col-md-6 col-md-offset-3">
          <div class="form-group">
            {!! Form::label('assigned_to', __('lang_v1.assigned_to')) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::select('assigned_to', $user_groups , $contact->user_id, ['class' => 'form-control select2']); !!}
            </div>
          </div>
        </div>
        @endif
        
        <div class="clearfix"></div>
         @if( isset($customerSettings->address) && $customerSettings->address == 1)
        <div
          class="col-md-12">
          <div class="form-group">
            {!! Form::label('address', __('contact.address') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('address', $contact->address, ['class' => 'form-control', 'placeholder' =>
              __('contact.address')]);
              !!}
            </div>
          </div>
        </div>
        @endif
        @if( isset($customerSettings->address_line_2) && $customerSettings->address_line_2 == 1)
        <div
          class="col-md-12">
          <div class="form-group">
            {!! Form::label('address', __('contact.address_2') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('address_2', $contact->address_2, ['class' => 'form-control', 'placeholder' =>
              __('contact.address_2')]);
              !!}
            </div>
          </div>
        </div>
        @endif
        @if(isset($customerSettings->address_line_3) && $customerSettings->address_line_3 == 1 )
        <div
          class="col-md-12">
          <div class="form-group">
            {!! Form::label('address_3', __('contact.address_3') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-phone"></i>
              </span>
              {!! Form::text('address_3', $contact->address_3, ['class' => 'form-control', 'placeholder' =>
              __('contact.address_3')]);
              !!}
            </div>
          </div>
        </div>
        @endif
        @if(isset($customerSettings->city) &&$customerSettings->city == 1)
        <div
          class="col-md-3  @if($contact->type=='supplier' && !array_key_exists('supplier_city', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('city', __('business.city') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('city', $contact->city, ['class' => 'form-control', 'placeholder' => __('business.city')]);
              !!}
            </div>
          </div>
        </div>
        @endif
         @if(isset($customerSettings->state) && $customerSettings->state == 1)
        <div
          class="col-md-3  @if($contact->type=='supplier' && !array_key_exists('supplier_state', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('state', __('business.state') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('state', $contact->state, ['class' => 'form-control', 'placeholder' =>
              __('business.state')]); !!}
            </div>
          </div>
        </div>
        @endif
          @if(isset($customerSettings->country) && $customerSettings->country == 1)
        <div
          class="col-md-3  @if($contact->type=='supplier' && !array_key_exists('supplier_country', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('country', __('business.country') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-globe"></i>
              </span>
              {!! Form::text('country', $contact->country, ['class' => 'form-control', 'placeholder' =>
              __('business.country')]); !!}
            </div>
          </div>
        </div>
         @endif
        @if( isset($customerSettings->landmark) && $customerSettings->landmark == 1)
        <div
          class="col-md-3  @if($contact->type=='supplier' && !array_key_exists('supplier_landmark', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('landmark', __('business.landmark') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-map-marker"></i>
              </span>
              {!! Form::text('landmark', $contact->landmark, ['class' => 'form-control', 'placeholder' =>
              __('business.landmark')]); !!}
            </div>
          </div>
        </div>
        @endif
        <div class="clearfix"></div>
        
         <div class="col-md-12 customer_fields">
              @if(isset($customerSettings->passport_nic_no) && $customerSettings->passport_nic_no == 1)
            <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('nic_number',__('contact.passport_no')) !!}
                <div class="input-group">
                  <span class="input-group-addon">
                    <i class="fa fa-user"></i>
                  </span>
                  {!! Form::text('nic_number', $contact->nic_number, ['class' => 'form-control nic_number', 'placeholder' =>__('contact.passport_no')]); !!}
                </div>
              </div>
            </div>
            @endif
         @if(isset($customerSettings->passport_nic_image) && $customerSettings->passport_nic_image == 1)
             <div class="col-md-4">
              <div class="form-group">
                {!! Form::label('image',__('contact.passport_image')) !!}
                {!! Form::file('image', ['id' => 'image','accept' => 'image/*']); !!}
              </div>
            </div>
            @endif
     @if(isset($customerSettings->signature) && $customerSettings->signature == 1)
            <div  class="col-md-4 ">
              <div class="form-group">
                {!! Form::label('signature', __('contact.signature')) !!}
                {!! Form::file('signature', ['id' => 'signature','accept' => 'image/*']); !!}
              </div>
            </div>
            @endif
        </div>
        
        <div class="col-md-12">
          <hr />
        </div>
        <div
          class="col-md-3 customer_fields  @if($contact->type=='customer' && !array_key_exists('customer_custom_field_1', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_custom_field_1', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('custom_field1', __('lang_v1.contact_custom_field1') . ':') !!}
            {!! Form::text('custom_field1', $contact->custom_field1, ['class' => 'form-control',
            'placeholder' => __('lang_v1.contact_custom_field1')]); !!}
          </div>
        </div>
        <div
          class="col-md-3 customer_fields  @if($contact->type=='customer' && !array_key_exists('customer_custom_field_2', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_custom_field_2', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('custom_field2', __('lang_v1.contact_custom_field2') . ':') !!}
            {!! Form::text('custom_field2', $contact->custom_field2, ['class' => 'form-control',
            'placeholder' => __('lang_v1.contact_custom_field2')]); !!}
          </div>
        </div>
        <div
          class="col-md-3 customer_fields  @if($contact->type=='customer' && !array_key_exists('customer_custom_field_3', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_custom_field_3', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('custom_field3', __('lang_v1.contact_custom_field3') . ':') !!}
            {!! Form::text('custom_field3', $contact->custom_field3, ['class' => 'form-control',
            'placeholder' => __('lang_v1.contact_custom_field3')]); !!}
          </div>
        </div>
        <div
          class="col-md-3 customer_fields  @if($contact->type=='customer' && !array_key_exists('customer_custom_field_4', $contact_fields)) hide @endif @if($contact->type=='supplier' && !array_key_exists('supplier_custom_field_4', $contact_fields)) hide @endif">
          <div class="form-group">
            {!! Form::label('custom_field4', __('lang_v1.contact_custom_field4') . ':') !!}
            {!! Form::text('custom_field4', $contact->custom_field4, ['class' => 'form-control',
            'placeholder' => __('lang_v1.contact_custom_field4')]); !!}
          </div>
        </div>
        <div class="clearfix"></div>

      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.update' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>

@if(empty($contact->notification_contacts))
    $("#numbers_table").hide();
@endif

// Add row when add_number_row button is clicked
    $('#numbers_table').on('click', '#add_number_row', function() {
        var lastRow = $('#numbers_table tbody tr:last');
        var newRow = lastRow.clone(); // Clone the last row
        newRow.find('input[type="text"]').val(''); // Clear the input value
        lastRow.after(newRow); // Append the new row after the last row

        // Add remove button to the new row
        var removeButton = $('<button>', {
            'type': 'button',
            'class': 'btn btn-danger remove-number-row',
            'text': '-'
        });
        newRow.find('td:last').html(removeButton); // Add the remove button to the last cell
    });

    // Remove row when remove-number-row button is clicked
    $('#numbers_table').on('click', '.remove-number-row', function() {
        $(this).closest('tr').remove(); // Remove the row
    });
    
    $(document).on('change', '.add_more_nos', function() {
        if($(this).is(':checked')){
            
            $("#numbers_table").show();
            $("#notification_fields").attr('required',true);
        }else{
            
            $("#numbers_table").hide();
            $("#notification_fields").attr('required',false);
        }
    });
    
    $(document).on('change', '.sub_customer', function() {
            if($(this).is(':checked')){
                $(".sub_customer_field").removeClass('hide');
            }else{
                $(".sub_customer_field").addClass('hide');
            }
        });
    
    $(document).on('change', '.toggler', function() {
        var formData = [];
        
        $('#numbers_table tbody tr').each(function() {
            var phoneNumber = $(this).find('input[name="phone_number[]"]').val(); // Get phone number value
            var phoneName = $(this).find('input[name="phone_name[]"]').val(); // Get phone number value
            var checkboxes = $(this).find('.toggler'); // Get checkboxes
            var rowValues = {
              'phone_number': phoneNumber,
              'notifications': {} ,
              'phone_name' : phoneName
            };
            
            checkboxes.each(function() {
              var checkboxName = $(this).attr('name').replace('[]', ''); // Get the checkbox name
              var checkboxValue = $(this).is(':checked') ? 1 : 0; // Determine checkbox value (1 if checked, 0 if not)
              rowValues['notifications'][checkboxName] = checkboxValue; // Add checkbox name and value to rowValues object
            });
            
            formData.push(rowValues); // Add rowValues to formData array
          });
          
        $("#notification_parameters").val(JSON.stringify(formData));
    });
    
 
  @if(!empty($ob_transaction->transaction_date))
      $('#transaction_date_contact').datepicker('setDate', "{{@format_date($ob_transaction->transaction_date)}}" );
  @elseif(!empty($contact->contact_transaction_date))
      $('#transaction_date_contact').datepicker('setDate', "{{@format_date($contact->contact_transaction_date)}}" );
  @else
  $('#transaction_date_contact').datepicker('setDate', "{{@format_date($contact->created_at)}}");
  @endif

</script>