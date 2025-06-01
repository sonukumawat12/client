@php
$payment_methods = array(
    'Free' => 'Free',
    'Cash' => 'Cash',
    'Card' => 'Card',
    'Online Transfer' => 'Online Transfer',
    'Cheque' => 'Cheque'
);
@endphp


<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\RefillBusinessController@update',[$data->id]), 'method' => 'put', 'id' => 'sms_packages_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'superadmin::lang.sms_packages' )</h4>
      </div>
  
      <div class="modal-body">
          <div class="row">
              <div class="form-group col-md-4">
                  {!! Form::label('date', __( 'lang_v1.date' ) .":*") !!}
                  {!! Form::date('date', date('Y-m-d',strtotime($data->date)), ['class' => 'form-control', 'placeholder' => __( 'lang_v1.date' ) ]); !!}
              </div>
              
               <div class="form-group col-md-4">
                    {!! Form::label('business_id', __('superadmin::lang.business') . ':') !!}
                    <select class="form-control select2" name="business_id" id="add_business_id" style = 'width:100%'>
                        <option value="">@lang('lang_v1.please_select')</option>
                        @foreach($business as $one)
                            <option value="{{$one->id}}" data-string="{{$one->type}}"  @if($one->id == $data->business_id && $one->type == $data->type) selected @endif >{{$one->name}}</option>
                        @endforeach
                    </select>
                    
               </div>
              
               
               <div class="form-group col-md-4">
                    {!! Form::label('package_id', __('superadmin::lang.package') . ':') !!}
                    <select class="package_id select2 form-control" id="package_id" name="package_id" style="width:100%">
                        <option value="">{{__('lang_v1.all')}}</option>
                        @foreach($packages as $one)
                            <option  value="{{$one->id}}" data-amount="{{$one->amount}}" data-sms="{{$one->no_of_sms}}"  @if($one->id == $data->package_id) selected @endif>{{ $one->name }}</option>
                        @endforeach
                    </select>
                </div>

              <div class="clearfix"></div>
  
              <div class="form-group col-md-4">
                  {!! Form::label('amount', __( 'superadmin::lang.amount' ) .":*") !!}
                  {!! Form::text('amount', null, ['class' => 'form-control', 'disabled','placeholder' => __( 'superadmin::lang.amount' ) ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('no_of_sms', __( 'superadmin::lang.no_of_sms' ) .":*") !!}
                  {!! Form::text('no_of_sms', null, ['class' => 'form-control', 'disabled','placeholder' => __( 'superadmin::lang.no_of_sms' ) ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('type', __( 'superadmin::lang.business_type' ) .":*") !!}
                  {!! Form::text('type', $data->type, ['class' => 'form-control', 'readonly','placeholder' => __( 'superadmin::lang.business_type' ), 'id' => 'add_business_type' ]); !!}
              </div>
              
              
              <div class="clearfix"></div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('expiry_date', __( 'superadmin::lang.expiry_date' ) .":*") !!}
                  {!! Form::date('expiry_date', date('Y-m-d',strtotime($data->expiry_date)), ['class' => 'form-control', 'placeholder' => __( 'lang_v1.date' ) ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                {!! Form::label('payment_method', __('superadmin::lang.payment_method') . ':') !!}
                {!! Form::select('payment_method', $payment_methods, $data->payment_method, ['class' => 'form-control
                select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('note', __( 'superadmin::lang.note' ) .":*") !!}
                  {!! Form::textarea('note', $data->note, ['class' => 'form-control', 'rows' => 2 ]); !!}
              </div>
          </div>
          
          <div class="row cheque_fields hide">
              <div class="form-group col-md-4">
                  {!! Form::label('bank_name', __( 'superadmin::lang.bank_name' ) .":*") !!}
                  {!! Form::text('bank_name', $data->bank_name, ['class' => 'form-control', 'placeholder' => __( 'superadmin::lang.bank_name' ) ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('no_of_sms', __( 'superadmin::lang.cheque_date' ) .":*") !!}
                  {!! Form::date('cheque_date', !empty($data->cheque_date) ? date('Y-m-d',strtotime($data->cheque_date)) : null, ['class' => 'form-control', 'placeholder' => __( 'superadmin::lang.cheque_date' ) ]); !!}
              </div>
              
              <div class="form-group col-md-4">
                  {!! Form::label('cheque_no', __( 'superadmin::lang.cheque_no' ) .":*") !!}
                  {!! Form::text('cheque_no', $data->cheque_no, ['class' => 'form-control', 'placeholder' => __( 'superadmin::lang.cheque_no' ) ]); !!}
              </div>
          </div>
  
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script>
      $(".select2").select2();
      $(document).ready(function(){
          $('#payment_method').trigger('change');
          $('#package_id').trigger('change');
      });
      
      
      $(document).on('change','#payment_method',function(){
          if($(this).val() == 'Cheque'){
              $(".cheque_fields").removeClass('hide');
          }else{
              $(".cheque_fields").addClass('hide');
          }
      });
      
      $(document).on('change', '#package_id', function() {
        if ($(this).val()) {
            var selectedOption = $(this).find('option:selected');
            var amount = selectedOption.data('amount') || 0;
            var sms = selectedOption.data('sms') || 0;
    
            console.log("Selected Amount:", amount); // Debugging statement
            console.log("Selected SMS:", sms); // Debugging statement
    
            __write_number($("#amount"), amount);
            __write_number($("#no_of_sms"), sms);
        }
    });
    
    $(document).on('change', '#add_business_id', function() {
        if ($(this).val()) {
            var selectedOption = $(this).find('option:selected');
            var type = selectedOption.data('string') || '';
    
            $("#add_business_type").val(type);
        }
    });


  </script>
  