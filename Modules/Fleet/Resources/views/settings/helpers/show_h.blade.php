
<div class="modal-dialog" role="document">
  <div class="modal-content">

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">@lang( 'fleet::lang.helper' )</h4>
    </div>

    <div class="modal-body">
        <div class="row">
            <div class="form-group col-sm-12">
                {!! Form::label('joined_date', __( 'fleet::lang.joined_date' ) . ':*') !!}
                {!! Form::text('joined_date', $helper->joined_date, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
            'fleet::lang.joined_date' )]) !!}
            </div>
            <div class="col-md-12">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('hrm_enabled', '1', $helper->hrm_enabled == 1, [ 'class' => 'input-icheck', 'readonly', 'id' => 'hrm_enabled']) !!}
                        {{ __( 'fleet::lang.hrm_enabled' ) }}
                    </label>
                </div>
            </div>
            <div class="form-group col-sm-12 employee_select" hidden>
                {!! Form::label('department', __( 'essentials::lang.department' ) .":") !!}
                {!! Form::text('department', isset($departments) && !empty($departments->name) ? $departments->name : '', ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
               'fleet::lang.department' )]) !!}
            </div>

            <div class="form-group col-sm-12 employee_select" hidden>
                {!! Form::label('designation', __( 'essentials::lang.designation' ) .":") !!}
                {!! Form::text('designation', isset($designation) && !empty($designation->name) ? $designation->name : '', ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
               'fleet::lang.designation' )]) !!}
            </div>
            <div class="form-group col-sm-12">
            {!! Form::label('employee_no', __( 'fleet::lang.employee_no' ) . ':*') !!}
            {!! Form::text('employee_no', $helper->employee_no, ['class' => 'form-control', 'readonly', 'placeholder' => __(
            'fleet::lang.employee_no'), 'id'
            => 'update_employee_no', 'readonly']) !!}
            </div>
            <div class="form-group col-sm-12 helper_name_text">
            {!! Form::label('helper_name', __( 'fleet::lang.helper_name' ) . ':*') !!}
            {!! Form::text('helper_name', $helper->helper_name, ['class' => 'form-control', 'readonly', 'placeholder' => __(
            'fleet::lang.helper_name'), 'id'
            => 'helper_name']) !!}
            </div>
            <div class="form-group col-sm-12">
            {!! Form::label('nic_number', __( 'fleet::lang.nic_number' ) . ':*') !!}
            {!! Form::text('nic_number', $helper->nic_number, ['class' => 'form-control', 'readonly', 'placeholder' => __(
            'fleet::lang.nic_number'), 'id'
            => 'update_nic_number']) !!}
            </div>
            <div class="form-group col-sm-12">
                {!! Form::label('pass_no', __( 'fleet::lang.pass_no' ) . ':') !!}
            {!! Form::text('pass_no', $helper->pass_no, ['class' => 'form-control', 'readonly', 'placeholder' => __('fleet::lang.pass_no'), 'required']); !!}
        
            </div>
            
            <div class="form-group col-sm-12">
                {!! Form::label('pass_expiry_date', __( 'fleet::lang.pass_expiry_date' ) . ':') !!}
            {!! Form::date('pass_expiry_date', $helper->pass_expiry_date, ['class' => 'form-control', 'readonly', 'placeholder' => __('fleet::lang.pass_expiry_date')]); !!}
            </div>


            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('salary_expense_category', __( 'fleet::lang.salary_expense_category')) !!} 
                {!! Form::text('salary_expense_category', isset($salary_expense_category) && !empty($salary_expense_category->name) ? $salary_expense_category->name : '', ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
               'fleet::lang.salary_expense_category' )]) !!}
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('advance_expense_category', __( 'fleet::lang.advance_expense_category')) !!}
                {!! Form::text('advance_expense_category', isset($advance_expense_category) && !empty($advance_expense_category->name) ? $advance_expense_category->name : '', ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
               'fleet::lang.advance_expense_category' )]) !!}
                </div>
            </div>
            
            <div class="col-md-12">
                <div class="form-group">
                {!! Form::label('bata_expense_category', __( 'fleet::lang.bata_expense_category')) !!}
                {!! Form::text('bata_expense_category', isset($bata_expense_category) && !empty($bata_expense_category->name) ? $bata_expense_category->name : '', ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
               'fleet::lang.bata_expense_category' )]) !!}
                </div>
            </div>
            
        </div>
    </div>

    <div class="modal-footer">
      <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
    </div>
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(document).ready(function() {
  
  var is_hrm_enabled = @json($helper->hrm_enabled);
  if ($('#hrm_enabled').is(':checked')) {
      $(".employee_select").prop('hidden', false);
  } else {
        $(".employee_select").prop('hidden', true);
  }
});
</script>