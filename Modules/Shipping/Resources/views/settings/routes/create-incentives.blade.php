<style>
  .justify-content-between{
    justify-content:space-between!important;
  }

.view_modal_2{
  background:rgba(128, 128, 128, 0.7) !important;
}
  </style>
<div class="modal-dialog modal-md" role="document">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'shipping::lang.incentives' )</h4>
    </div>
    
    <form id="create_incentive_form" action="javascript:;">
    <div class="modal-body">
     
        <div class="form-group col-sm-12">
          {!! Form::label('incentive_name', __( 'shipping::lang.incentive_name' ) . ':*') !!}
          {!! Form::text('incentive_name', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'shipping::lang.incentive_name'), 'id'
          => 'incentive_name']); !!}
        </div>

        <div class="form-group col-sm-12">
        {!! Form::label('applicable_to', __( 'shipping::lang.applicable_to' ) . ':*') !!}
          {!! Form::select('applicable_to', $applicable_to, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>

        <div class="form-group col-sm-12">
        {!! Form::label('incentive_type', __( 'shipping::lang.incentive_type' ) . ':*') !!}
          {!! Form::select('incentive_type', $incentive_type, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>

        <div id="incentive_type_percentage" style="display:none">
          <div class="form-group col-sm-12">
          {!! Form::label('percentage', __( 'shipping::lang.percentage' ) . ':*') !!}
          {!! Form::number('percentage', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'shipping::lang.percentage'), 'id'
            => 'percentage', 'min' => '0', 'max' => '100','step' => 'any']); !!}
          </div>
          <div class="form-group col-sm-12">
          {!! Form::label('based_on', __( 'shipping::lang.based_on' ) . ':*') !!}
          {!! Form::select('based_on', $based_on, null, ['class' => 'form-control select2', 'placeholder' =>
            __('messages.please_select')]); !!}
          </div>
        </div>

        <div id="incentive_type_fixed" style="display:none">
          <div class="form-group col-sm-12">
            {!! Form::label('amount', __( 'shipping::lang.amount' ) . ':*') !!}
          {!! Form::text('amount', null, ['class' => 'form-control', 'required', 'placeholder' => __( 'shipping::lang.amount'), 'id'
            => 'amount']); !!}
          </div>
        </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>

    </form>


  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
$(document).ready(function() {
    $('#create_incentive_form').validate({
        rules: {
            incentive_name: {
                required: true
            },
            incentive_type: {
                required: true
            },
            percentage: {
                number: true
            },
            applicable_to: {
                required: true,
              },
            amount: {
                number: true,
                required:false
            }
        },
        messages: {
            incentive_name: {
                required: "Please enter the incentive name"
            },
            incentive_type: {
                required: "Please select the incentive type"
            },
            applicable_to: {
                required: "Please select the applicable role"
            },
            percentage: {
                number: "Please enter a valid number"
            },
            amount: {
                number: "Please enter a valid number"
            }
        },
        submitHandler: function(form) {
            // Submit the form if it passes validation
            saveIncentive();
        }
    });
});

  __write_number($('#amount'), 0.00);
  $('#incentive_type').change(function () {
    $('#percentage').val('');
    $('#based_on').val('');
    resetAmount();
    if($(this).val()=='percentage'){
      $('#incentive_type_percentage').show();
      $('#incentive_type_fixed').hide();
    } else {
      $('#incentive_type_fixed').show();
      $('#incentive_type_percentage').hide();
      
    }
  });
  $('#based_on').change(function () {
    resetAmount();
   $('#incentive_type_fixed').show();
   if($(this).val()=='trip_amount'){
      $('#amount').val($('#route_amount').val());
      $('#amount').attr('readonly',true);
    } else {
      $('#amount').attr('readonly',false);
    
    }
 });

 function resetAmount(){
  __write_number($('#amount'), 0.00);
      $('#amount').attr('readonly',false);
      $('#amount').removeAttr('min',0);
      $('#amount').removeAttr('max',100);
  
 }


 function saveIncentive(){
  console.log($('form#create_incentive_form').validate());
  var obj={
    incentive_name:$('#incentive_name').val(),
    applicable_to:$('#applicable_to').val(),
    incentive_type:$('#incentive_type').val(),
    percentage:$('#percentage').val(),
    based_on:$('#based_on').val(),
    amount:$('#amount').val(),
  };
  buildIncentiveRow(obj);
 }
 function buildIncentiveRow(obj){

  console.log(obj);
  var fixed_amount='--';
  var company_decision='--';
  var based_on='--';
  var company_decision='--';
  var incentive_type='--';
  var incentive_type='--';
  var incentive_type='--';
  if(obj.incentive_type=='fixed'){
    fixed_amount=obj.amount;
    incentive_type='Fixed';
  } else if(obj.incentive_type=='percentage'){
    incentive_type='Percentage';
  }
  if(obj.based_on=='company_decision'){
    company_decision=obj.amount;
    based_on='Company Decision';
  } if(obj.based_on=='trip_amount'){
    company_decison=obj.amount;
    based_on='Trip Amount';
  } else {
    based_on='--';
    company_decison='--';
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
  <td>${fixed_amount}<input type="hidden" value="${fixed_amount}" name="fixed_amount[]"></td>
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

 }


</script>