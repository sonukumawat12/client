<style>
  .justify-content-between{
    justify-content:space-between!important;
  }
  .main-modal {
  height:675px;
  overflow:auto;
}
  </style>

<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content main-modal">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\RouteController@store'), 'method' =>
    'post', 'id' => !empty($quick_add) ? 'quick_add_route' : 'route_add_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.route' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <input type="hidden" name="quick_add" id="quick_add" value="{{$quick_add}}">
        <div class="form-group col-sm-6">
          {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
          {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'fleet::lang.date' )]); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('route_name', __( 'fleet::lang.route_name' ) . ':*') !!}
          {!! Form::text('route_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'fleet::lang.route_name'), 'id'
          => 'route_name']); !!}
        </div>
        
        <div class="form-group col-sm-6">
          {!! Form::label('trip_category', __( 'fleet::lang.trip_category' ) . ':*') !!}
          {!! Form::select('trip_category', $trip_categories, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        
        <div class="form-group col-sm-6">
          {!! Form::label('delivered_to_acc', __( 'fleet::lang.delivered_to_acc_no' ) . ':*') !!}
          {!! Form::select('delivered_to_acc', $delivered_to, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        
        <div class="form-group col-sm-6">
          {!! Form::label('orignal_location', __( 'fleet::lang.orignal_location' ) . ':*') !!}
          {!! Form::text('orignal_location', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.orignal_location'), 'id'
          => 'orignal_location']); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('destination', __( 'fleet::lang.destination' ) . ':*') !!}
          {!! Form::text('destination', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.destination'), 'id'
          => 'destination']); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('distance', __( 'fleet::lang.distance_km' ) . ':*') !!}
          {!! Form::text('distance', @num_format(0.00), ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.distance'), 'id'
          => 'distance']); !!}
        </div>
        
         <div class="form-group col-sm-6">
          {!! Form::label('actual_distance', __( 'fleet::lang.actual_distance' ) . ':*') !!}
          {!! Form::text('actual_distance', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.actual_distance'), 'id'
          => 'actual_distance']); !!}
        </div>
        
        <div class="form-group col-sm-6">
          {!! Form::label('rate', __( 'fleet::lang.rate_km' ) . ':*') !!}
          {!! Form::text('rate', @num_format(0.00), ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.rate'), 'id'
          => 'rate']); !!}
        </div>
        <div class="form-group col-sm-6">
          {!! Form::label('route_amount', __( 'fleet::lang.route_amount' ) . ':*') !!}
          {!! Form::text('route_amount', @num_format(0.00), ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.route_amount'), 'id'
          => 'route_amount', 'disabled' => 'true']); !!}
        </div>       
        <div class="form-group col-sm-6">
          {!! Form::label('driver_incentive', __( 'fleet::lang.driver_incentive' ) . ':*') !!}
          {!! Form::text('driver_incentive', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.driver_incentive'), 'id'
          => 'driver_incentive']); !!}
        </div>       
        <div class="form-group col-sm-6">
          {!! Form::label('helper_incentive', __( 'fleet::lang.helper_incentive' ) . ':*') !!}
          {!! Form::text('helper_incentive', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.helper_incentive'), 'id'
          => 'helper_incentive']); !!}
        </div>  
        
        <hr>
        <div class="form-group col-sm-12">
          <div class="d-flex justify-content-between py-2">
            <h4>Incentives</h4>
          </div>
          
        
            <div class="row bg-success" style="padding: 10px">
              <div class="form-group col-sm-4">
                  {!! Form::label('incentive_name', __( 'fleet::lang.incentive_name' ) . ':*') !!}
                  {!! Form::text('incentive_name', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.incentive_name'), 'id'
                  => 'incentive_name']); !!}
                </div>
        
                <div class="form-group col-sm-4">
                {!! Form::label('applicable_to', __( 'fleet::lang.applicable_to' ) . ':*') !!}
                  {!! Form::select('applicable_to', $applicable_to, null, ['class' => 'form-control select2', 'placeholder' =>
                  __('messages.please_select')]); !!}
                </div>

              <div class="form-group col-sm-4">
                {!! Form::label('incentive_type', __( 'fleet::lang.incentive_type' ) . ':*') !!}
                {!! Form::select('incentive_type', $incentive_type, null, ['class' => 'form-control select2', 'placeholder' =>
                __('messages.please_select'), 'id' => 'incentive_type']); !!}
              </div>


{{--              Code for merging and conditionally rendering--}}

                  <div id="incentive_option" >
                      <div id="fixed_amount_option" class="form-group col-sm-4">
                        {!! Form::label('fixed_amount', __( 'fleet::lang.fixed_amount' ) . ':*') !!}
                        {!! Form::text('fixed_amount', null, ['class' => 'form-control',  'placeholder' => __( 'fleet::lang.fixed_amount'), 'id'
                          => 'fixed_amount']); !!}
                      </div>
                      <div id="fixed_percentage_option" class="form-group col-sm-4">
                        {!! Form::label('percentage', __( 'fleet::lang.percentage' ) . ':*') !!}
                        {!! Form::number('percentage', null, ['class' => 'form-control',  'placeholder' => __( 'fleet::lang.percentage'), 'id'
                          => 'percentage', 'min' => '0', 'max' => '100','step' => 'any']); !!}
                      </div>
                  </div>



                  <div class="form-group col-sm-4 hide_first" id="based_on_hide">
                  {!! Form::label('based_on', __( 'fleet::lang.based_on' ) . ':*') !!}
                  {!! Form::select('based_on', $based_on, null, ['class' => 'form-control select2', 'placeholder' =>
                    __('messages.please_select')]); !!}
                  </div>
        
                <div id="incentive_type_fixed" class="hide_first">
                  <div class="form-group col-sm-4">
                    {!! Form::label('percentage_amount', __( 'fleet::lang.company_decision_amount' ) . ':*') !!}
                  {!! Form::text('percentage_amount', null, ['class' => 'form-control',  'placeholder' => __( 'fleet::lang.company_decision_amount'), 'id'
                    => 'percentage_amount','readonly']); !!}
                  </div>
                </div>
                
                <div class="form-group col-sm-4 hide_first">
                    <br><button type="button" class="btn  btn-primary" id="addIncentiveBtn">
                      <i class="fa fa-plus"></i> @lang('messages.add')</button>
                </div>
              
              </div>
              
            
          
        <div class="table-responsive">
          <table class="table table-bordered table-striped" id="add_incentive_table" style="width: 100%;">
            <thead>
              <tr>
                <th>@lang('fleet::lang.incentive_name')</th>
                <th>@lang('fleet::lang.incentive_type_th')</th>
                <th>@lang('fleet::lang.applicable_to')</th>
                <th>@lang('fleet::lang.fixed_amount')</th>
                <th>@lang('fleet::lang.percentage')</th>
                <th>@lang('fleet::lang.based_on')</th>
                <th>@lang('fleet::lang.percentage_amount')</th>
                <th class="notexport">@lang('messages.action')</th>
                
              </tr>
            </thead>
            <tbody>
          </tbody>
          </table>
        </div>
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
  var incentives=[];
  
 $('#date').datepicker('setDate', new Date());
 
 $(".select2").select2();
 
 $('#distance, #rate').change(function () {
    let distance = parseFloat($('#distance').val());
    let rate = parseFloat($('#rate').val());

    let route_amount = distance * rate;
    __write_number($('#route_amount'), route_amount);

 })

</script>
<script>
 $('#date').datepicker('setDate', new Date());
 $('#distance, #rate').change(function () {
    let distance = parseFloat($('#distance').val());
    let rate = parseFloat($('#rate').val());

    let route_amount = distance * rate;
    __write_number($('#route_amount'), route_amount);

 })

 function removeIncentive(e){
  $(e).parent().parent().remove();
 }
 
 $('.view_modal').modal({
      backdrop: 'static',
      keyboard: false
    });
</script>

<script>


$(document).ready(function() {
  $('.hide_first').hide();
  $('#incentive_option').hide();
    
        $('#addIncentiveBtn').on('click', function() {
            saveIncentive();
            // if ($('#incentive_name').valid() &&
            //     $('#incentive_type').valid() &&
            //     $('#applicable_to').valid() &&
            //     ($('#incentive_type_percentage').is(':visible') ? $('#percentage').valid() : true) &&
            //     ($('#incentive_type_fixed').is(':visible') ? $('#amount').valid() : true)
            // ) {
            //     saveIncentive();
            // }
        });

        $('#incentive_name').rules('add', {
            required: true,
            messages: {
                required: "Please enter the incentive name"
            }
        });

        $('#incentive_type').rules('add', {
            required: true,
            messages: {
                required: "Please select the incentive type"
            }
        });

        $('#applicable_to').rules('add', {
            required: true,
            messages: {
                required: "Please select the applicable role"
            }
        });

        $('#percentage').rules('add', {
            number: true,
            messages: {
                number: "Please enter a valid number"
            }
        });

        $('#amount').rules('add', {
            number: true,
            messages: {
                number: "Please enter a valid number"
            }
        });

});

  __write_number($('#amount'), 0.00);

  $('#incentive_type').change(function () {
    $('#percentage').val('');
    $('#based_on').val('');
    resetAmount();

    //onchange
    var selectedValue = $(this).val();
    if(selectedValue === "fixed") {
      $('.hide_first').show();
      $('#incentive_option').show();


      $('#fixed_amount_option').show();
      $('#fixed_percentage_option').hide();

      //hide also based-on and company_dec
      $('#based_on_hide').hide();
      $('#incentive_type_fixed').hide();

    } else if(selectedValue === "percentage") {
      $('.hide_first').show();
      $('#incentive_option').show();


      $('#fixed_amount_option').hide();
      $('#fixed_percentage_option').show();

      $('#based_on_hide').show();
      $('#incentive_type_fixed').show();

    } else {
      $('.hide_first').hide();
      $('#incentive_option').hide();

      $('#based_on_hide').hide();
      $('#incentive_type_fixed').hide();

    }

  });
  $('#based_on').change(function () {
    resetAmount();
   $('#incentive_type_fixed').show();
   if($(this).val()=='trip_amount'){
      $('#percentage_amount').val($('#route_amount').val());
      $('#percentage_amount').attr('readonly',true);
    } else {
      $('#percentage_amount').attr('readonly',false);
    
    }
 });

 function resetAmount(){
  __write_number($('#amount'), 0.00);
      $('#amount').attr('readonly',false);
      $('#amount').removeAttr('min',0);
      $('#amount').removeAttr('max',100);
  
 }


 function saveIncentive(){
  var obj={
    incentive_name:$('#incentive_name').val(),
    applicable_to:$('#applicable_to').val(),
    incentive_type:$('#incentive_type').val(),
    percentage:$('#percentage').val(),
    based_on:$('#based_on').val(),
    fixed_amount:$('#fixed_amount').val(),
    percentage_amount : $('#percentage_amount').val(),
    
  };
  buildIncentiveRow(obj);
  
  // Reset input fields to default values
    $('#incentive_name').val('');
    $('#incentive_type').val('').trigger('change');
    $('#applicable_to').val('').trigger('change');
    $('#percentage').val('');
    $('#fixed_amount').val('');
    $('#percentage_amount').val('');
 }
 function buildIncentiveRow(obj){

  var fixed_amount='--';
  var company_decision='--';
  var based_on='--';
  var company_decision='--';
  var incentive_type='--';
  var incentive_type='--';
  var incentive_type='--';
  
  fixed_amount=obj.fixed_amount;
  
  if(obj.incentive_type=='fixed'){
    incentive_type='Fixed';
  } else if(obj.incentive_type=='percentage'){
    incentive_type='Percentage';
  }
  
  company_decision=obj.percentage_amount;
  
  if(obj.based_on=='company_decision'){
    based_on='Company Decision';
  }else if(obj.based_on=='trip_amount'){
    based_on='Trip Amount';
  }
  
  if(obj.percentage==''){
    percentage='--';
  } else {
    percentage=obj.percentage;
  }
  
  var applicable_to='';
  if(obj.applicable_to=='driver'){
    applicable_to='Driver';
  } else if(obj.applicable_to=='helper') {
    applicable_to='Helper';
  } else {
    applicable_to="Both";
  }
  var tr=`
  <tr>
  <td>${obj.incentive_name}<input type="hidden" value="${obj.incentive_name}" name="incentive_name[]"></td>
  <td>${incentive_type}<input type="hidden" value="${obj.incentive_type}" name="incentive_type[]"></td>
  <td>${applicable_to}<input type="hidden" name="applicable_to[]" value="${obj.applicable_to}"></td>
  <td>${fixed_amount.toLocaleString()}<input type="hidden" value="${fixed_amount}" name="fixed_amount[]"></td>
  <td>${percentage}<input type="hidden" value="${obj.percentage}" name="percentage[]"></td>
  <td>${based_on}<input type="hidden" value="${obj.based_on}" name="based_on[]"></td>
  <td>${company_decision}<input type="hidden" value="${company_decision}" name="company_decision[]"></td>
  <td><button type="button" onclick="removeIncentive(this)" class="btn btn-danger" aria-label="Left Align">
                    <span aria-hidden="true">×</span>
                    </button></td>
  </tr>
  `;
  $('#add_incentive_table tbody').append(tr);
  $('.view_modal_2').modal('hide');
   $('.hide_first').hide();

 }


</script>