<style>
    .select2{
        width: 100% !important;
    }
</style>
<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\RouteOperationController@postRO_Advance'), 'method' =>
    'post', 'id' => 'ro_advance_form' ]) !!}
    <input type="hidden" value="{{$route_operation->transaction_id}}" name="route_operation_id" required>
    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.advance' )</h4>
    </div>

    <div class="modal-body">
        
      <div class="row">
         <div class="form-group col-sm-4">
             <h5 class="text-danger" >@lang( 'fleet::lang.total_income' ):   <strong id="salary_total"></strong></h5>
         </div>
         
         <div class="form-group col-sm-4">
             <h5 class="text-danger" >@lang( 'fleet::lang.advance_paid' ):   <strong id="salary_paid"></strong></h5>
         </div>
         
         <div class="form-group col-sm-4">
             <h5 class="text-danger" >@lang( 'fleet::lang.remaining_salary' ):   <strong id="salary_balance"></strong></h5>
         </div>
      </div>
      <div class="row">  
      @if(count($business_locations) == 1)
				@php
				$default_location = current(array_keys($business_locations->toArray()))
				@endphp
				@else
				@php $default_location = null; @endphp
				@endif
		<div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('location_id', __('purchase.business_location').':*') !!}
				{!! Form::select('location_id', $business_locations,
				!empty($temp_data->location_id)?$temp_data->location_id: $default_location, ['class' =>
				'form-control select2', 'placeholder' => __('messages.please_select'), 'required']); !!}
			</div>
		</div>
		
		<div class="form-group col-sm-4">
          {!! Form::label('advance_for', __( 'fleet::lang.advance_for' ) .":") !!}
          {!! Form::select('advance_for', $staff, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select'),'required']); !!}
        </div>
		
		<div class="col-md-4">
          <div class="form-group">
            {!! Form::label('expense_category_id', __('expense.expense_category') . ':*') !!}
            {!! Form::text('expense_category_id_text',null, ['class' =>
            'form-control', 'placeholder' => __('expense.expense_category'), 'disabled','id' => 'expense_category_id_text']) !!}
            
            <input type="hidden" required name="expense_category_id" id="expense_category_id">
          </div>
        </div>
		
		<div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('ref_no', __('purchase.ref_no').':') !!}
				{!! Form::text('ref_no', !empty($temp_data->ref_no)?$temp_data->ref_no: $ref_no, ['class' =>
				'form-control']); !!}
			</div>
		</div>
		
		<div class="col-sm-4">
            <div class="form-group">
                {!! Form::label('fleet_id', __('fleet::lang.fleet').':') !!}
                {!! Form::select('fleet_id', $fleets, $ro->fleet_id, ['class' => 'form-control select2', 'placeholder' => __('messages.please_select'), 'required', 'disabled' => 'disabled']) !!}
            </div>
        </div>
        
        <div class="col-sm-4">
			<div class="form-group">
				{!! Form::label('trip_no', __('fleet::lang.trip_no').':') !!}
				{!! Form::text('trip_no', $ro->invoice_no, ['class' => 'form-control', 'placeholder' => __('fleet::lang.fleet'), 'readonly' => 'readonly']) !!}
			</div>
		</div>
				
        
    </div>
      <div class="row"> 
      
       <div class="form-group col-sm-4">
          {!! Form::label('transaction_date', __( 'fleet::lang.transaction_date' ) . ':*') !!}
          {!! Form::text('transaction_date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
          'fleet::lang.transaction_date' )]); !!}
        </div>
        
        <div class="col-sm-4">
          <div class="form-group">
            {!! Form::label('expense_account', __('sale.expense_account') . ':*') !!}
            {!! Form::text('expense_account_text',null, ['class' =>
            'form-control', 'placeholder' => __('sale.expense_account'), 'disabled','id' => 'expense_account_text']) !!}
            
            <input type="hidden" required name="expense_account" id="expense_account">

          </div>
        </div>
        
        <div class="form-group col-sm-4">
          {!! Form::label('advance_amount', __( 'fleet::lang.advance_amount' ) . ':*') !!}
          {!! Form::text('advance_amount', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.advance_amount'), 'id'
          => 'driver_advance_amount','required']); !!}
        </div>
        
        </div>
      <div class="row"> 
        
      <div class="form-group col-sm-6">
          {!! Form::label('method', __( 'fleet::lang.method' ) .":") !!}
          {!! Form::select('method', $payment_types, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select'),'required']); !!}
        </div> 
        
        <div class="form-group col-sm-6">
          <div class="form-group">
    			{!! Form::label("account_id" , __('lang_v1.payment_account') . ':') !!}
    			<div class="input-group">
    				<span class="input-group-addon">
    					<i class="fa fa-money"></i>
    				</span>
    				{!! Form::select("account_id", [],  null , ['class' =>
    				'form-control account_id
    				select2', 'placeholder' => __('lang_v1.please_select'), 'id' => "account_id", 'style' =>
    				'width:100%;','required']); !!}
    			</div>
    		</div>
        </div>
    </div>
    <input type="hidden" name="ro_id" value="{{$ro->id}}">
        <div class="bank_details row" hidden>
            <div class="form-group col-sm-6">
              {!! Form::label('cheque_number', __( 'fleet::lang.cheque_number' ) . ':*') !!}
              {!! Form::text('cheque_number', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.cheque_number'), 'id'
              => 'cheque_number']); !!}
            </div>
            <div class="form-group col-sm-6">
              {!! Form::label('cheque_date', __( 'fleet::lang.cheque_date' ) . ':*') !!}
              {!! Form::date('cheque_date', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.cheque_date'), 'id'
              => 'cheque_date']); !!}
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
 $('#transaction_date').datepicker('setDate', new Date());
 
 $('#driver_advance_amount').change(function() {
     console.log("change_detected");
    var staff = $('#advance_for').val();
    if(staff){
        var amounts = JSON.parse('{!! $amounts !!}');
        var max_amount = parseInt(amounts[staff]);
        if(max_amount < $(this).val()){
            toastr.error("@lang( 'fleet::lang.advance_exceeds' )");
            $('#driver_advance_amount').val("");
        }
    }else{
      
        toastr.error("@lang( 'fleet::lang.select_first' )");
        $('#driver_advance_amount').val("");
        
    } 
 });
 
 $('#advance_for').change(function() {
    
    if($(this).val()){
        var amounts = JSON.parse('{!! $amounts !!}');
        console.log(amounts);
        
        var max_amount = parseInt(amounts[$(this).val()]); // remamining salary
        var amt_paid = parseInt(amounts['paid_'+$(this).val()]);
        var salary_total = parseInt(amounts['salary_'+$(this).val()]);
        
        var acc_name = (amounts['acc_name_'+$(this).val()]);
        var acc_id = (amounts['acc_id_'+$(this).val()]);
        $("#expense_account_text").val(acc_name);
        $("#expense_account").val(acc_id);
        
        var cat_name = (amounts['cat_name_'+$(this).val()]);
        var cat_id = (amounts['cat_id_'+$(this).val()]);
        $("#expense_category_id_text").val(cat_name);
        $("#expense_category_id").val(cat_id);
        
        
        
        var formatter = new Intl.NumberFormat('en-US', {
          minimumFractionDigits: 2,
          maximumFractionDigits: 2
        });
        
        var formatted_max_amount = formatter.format(max_amount);
        
        var formatted_amt_paid = formatter.format(amt_paid);
        var formatted_salary_total = formatter.format(salary_total);
        

        $("#salary_balance").html(formatted_max_amount);
        $("#salary_paid").html(formatted_amt_paid);
        $("#salary_total").html(formatted_salary_total);
        
        
        var staff = $('#advance_for').val();
        var staff_amount = $('#driver_advance_amount').val();
        if(staff){
            
            if(max_amount < staff_amount){
                toastr.error("@lang( 'fleet::lang.advance_exceeds' )");
                $('#driver_advance_amount').val("");
            }
        }else{
            toastr.error("@lang( 'fleet::lang.select_first' )");
            $('#driver_advance_amount').val("");
        }
        
    }else{
        $("#salary_balance").html("");
        $("#salary_paid").html(formatted_amt_paid);
        $("#salary_total").html(formatted_salary_total);
    }
    
    
  });
 
  $('#method').change(function() {
    
    // if ($(this).val() == 'bank_transfer') {
    //     $(".bank_details").prop('hidden', false);
    //     var grp = 'Bank Account';
    // } else {
    //     var grp = 'Cash Account';
    //   $(".bank_details").prop('hidden', true);
    // }
    var grp = $(this).val();
    
    var location_id = "{{$business_id}}";
    
     $.ajax({

        method: 'get',

        url: '/accounting-module/get-account-group-name-dp',

        data: { group_name: grp, location_id: location_id },

        contentType: 'html',

        success: function(result) {
            
            $('#account_id').empty().append(result);

        },

    });
    
  });
  $(".select2").select2();
</script>