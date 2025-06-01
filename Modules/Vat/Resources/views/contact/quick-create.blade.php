<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    @php
        $business_id = request()->session()->get('user.business_id');
        $customers = \App\Contact::customersDropdown($business_id, false);
        $type = 'customer';
    @endphp
    {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@storeQuickCustomer'), 'method' => 'post', 'id' => 'vat_quick_add_customer','enctype'=>"multipart/form-data",'files' => true ]) !!}

    <div class="modal-header">
      <button type="button" class="close closing_contact_modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('contact.add_contact')</h4>
    </div>

    <div class="modal-body">
      <div class="row">

        <div class="col-md-3 contact_type_div">
          <div class="form-group">
            {!! Form::label('type', __('contact.contact_type') . ':*' ) !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::select('type', $types, !empty($type) ? $type : null , ['class' => 'form-control', 'id' =>
              'contact_type','placeholder'
              => __('messages.please_select'), 'required']); !!}
            </div>
          </div>
        </div>
        <div class="col-md-3   ">
          <div class="form-group">
            {!! Form::label('name', __('contact.name') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::text('name', null, ['class' => 'form-control','placeholder' => __('contact.name'), 'required']);
              !!}
            </div>
          </div>
        </div>
        
        <div class="col-md-3   ">
          <div class="form-group">
            {!! Form::label('name', __('contact.should_notify') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::select('should_notify', ['1' => __('messages.yes'), '0' => __('messages.no')], null, ['placeholder'
                  => __( 'messages.please_select' ), 'required', 'class' => 'form-control']); !!}
            </div>
          </div>
        </div>
        
        <div class="col-md-3   ">
          <div class="form-group">
            {!! Form::label('name', __('contact.credit_notification') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-envelope"></i>
              </span>
              {!! Form::select('credit_notification', ['settlement' => __('contact.settlement'), 'customer_bill' => __('contact.bill_to_customer'),'pumper_dashboard' => __('contact.pumper_dashboard')], null, ['placeholder'
                  => __( 'contact.none' ), 'class' => 'form-control']); !!}
            </div>
          </div>
        </div>
        
        <div class="clearfix"></div>
        <div class="col-md-3">
            {!! Form::checkbox('sub_customer', 1, !empty($contact->sub_customer) ? true : false, ['class' => 'sub_customer']) !!} {{__('contact.sub_customer')}}
        </div>
        
        <div class="col-md-9 sub_customer_field hide">
          <div class="form-group">
            {!! Form::label('name', __('contact.sub_customers') . ':*') !!}
            {!! Form::select('sub_customers[]', $customers, null, ['class' => 'form-control select2','multiple']); !!}
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
              {!! Form::text('contact_id', !empty($contact_id) ? $contact_id : null, ['class' => 'form-control','placeholder' =>
              __('lang_v1.contact_id'), 'readonly']); !!}
            </div>
          </div>
        </div>

        
        <div
          class="col-md-3">
          <div class="form-group">
            {!! Form::label('vat_number', __('contact.vat_number') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-info"></i>
              </span>
              {!! Form::text('vat_number', null, ['class' => 'form-control', 'placeholder' =>
              __('contact.vat_number')]); !!}
            </div>
          </div>
        </div>
        
        <div class="col-md-3 ">
          <div class="form-group">
            {!! Form::label('opening_balance', __('lang_v1.opening_balance') . ':') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-money"></i>
              </span>
              {!! Form::text('opening_balance', 0, ['class' => 'form-control input_number']); !!}
            </div>
          </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
              {!! Form::label('transaction_date', __( 'vat::lang.transaction_date' )) !!}
              {!! Form::text('transaction_date', null, ['class' => 'form-control','readonly', 'placeholder' =>
              __( 'vat::lang.transaction_date')]);
              !!}
            </div>
      </div>
        
        </div>

       
    </div>
    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default closing_contact_modal">@lang( 'messages.close' )</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
    $(document).ready(function() {
        
        $(document).on('click', '.closing_contact_modal', function() {
            $('.contact_modal').modal('hide');
        })
        
        $('#transaction_date').datepicker("setDate" , new Date());
        $('.select2').select2();
        
        $(document).on('change', '.sub_customer', function() {
            if($(this).is(':checked')){
                $(".sub_customer_field").removeClass('hide');
            }else{
                $(".sub_customer_field").addClass('hide');
            }
        });
    })
    
    
</script>