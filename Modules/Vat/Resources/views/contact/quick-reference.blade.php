<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    @php
        $business_id = request()->session()->get('user.business_id');
        $customers = \App\Contact::customersDropdown($business_id, false);
    @endphp
    {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@storeQuickReference'), 'method' => 'post', 'id' => 'vat_quick_add_reference','enctype'=>"multipart/form-data",'files' => true ]) !!}

    <div class="modal-header">
      <button type="button" class="close closing_contact_modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang('vat::lang.add_new')</h4>
    </div>

    <div class="modal-body">
      <div class="row">

        <div class="col-md-4   ">
          <div class="form-group">
            {!! Form::label('customer_reference', __('contact.customer_reference') . ':*') !!}
            <div class="input-group">
              <span class="input-group-addon">
                <i class="fa fa-user"></i>
              </span>
              {!! Form::text('customer_reference', null, ['class' => 'form-control','placeholder' => __('contact.customer_reference'), 'required']);
              !!}
            </div>
          </div>
        </div>
       
        <div class="col-md-4">
          <div class="form-group">
            {!! Form::label('customer_id', __('contact.customer') . ':*') !!}
            {!! Form::select('reference_customer_id', $customers, null, ['class' => 'form-control select2','id' => 'reference_customer_id','disabled','placeholder' => __('lang_v1.please_select')]); !!}
            <input type="hidden" name="customer_id" id="ref_customer_id" required>
          </div>
        </div>
        
        <div class="col-md-4">
            <div class="form-group">
              {!! Form::label('reference_date', __( 'vat::lang.reference_date' )) !!}
              {!! Form::text('reference_date', null, ['class' => 'form-control','readonly', 'placeholder' =>
              __( 'vat::lang.reference_date')]);
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
        
        $('#reference_date').datepicker("setDate" , new Date());
        $('.select2').select2();
        $("#reference_customer_id").val($("#customer_id").val()).trigger('change');
        $("#ref_customer_id").val($("#customer_id").val());
        
    })
    
</script>