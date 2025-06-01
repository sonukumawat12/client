<div class="modal-dialog"  role="document">
    <div class="modal-content">
  
    {!! Form::open(['url' => action([\Modules\Hms\Http\Controllers\HmsCouponController::class, 'update'],[$coupon->id]), 'method' => 'put', 'id' => 'edit_coupon' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'hms::lang.edit_coupon' )</h4>
      </div>
  
      <div class="modal-body">
            <div class="form-group">
                         <div class="d-flex justify-content-between align-items-center" style="
    gap: 10px;
">
                    {!! Form::label('hms_room_type_id', __('hms::lang.type') . '*') !!}
                    <div>
                        <label class="mb-0" style="font-weight: normal;">
                            <input type="checkbox" id="select_all_room_types">
                            Select All Accommodation Types
                        </label>
                    </div>
                </div>

                {!! Form::select('hms_room_type_id[]', $types, $selected_room_types, [
                    'class' => 'form-control',
                    'required',
                    'multiple' => true,
                    'id' => 'hms_room_type_id'
                ]) !!}
            </div>
        <div class="form-group">
        {!! Form::label('date_from', __('hms::lang.date_from') . '*') !!}
            {!! Form::text('start_date',!empty($coupon->start_date) ? @format_date($coupon->start_date) : null, [
                'class' => 'form-control date_picker',
                'required',
                'readonly',
            ]) !!}
        </div>
        <div class="form-group">
        {!! Form::label('date_to', __('hms::lang.date_to') . '*') !!}
            {!! Form::text('end_date', !empty($coupon->end_date) ? @format_date($coupon->end_date) : null, [
                'class' => 'form-control date_picker',
                'required',
                'readonly',
            ]) !!}
        </div>
        <div class="form-group">
            <div style="display: flex; align-items: center; gap: 10px;">
                {!! Form::label('coupon_code', __('hms::lang.coupon_code') . '*') !!}
                
                <div style="display: flex; align-items: center; gap: 5px;">
                    {!! Form::checkbox('apply_next_visit', 1, isset($coupon) && $coupon->is_next_visit, ['id' => 'apply_next_visit_checkbox']) !!}
                    <label for="apply_next_visit_checkbox" style="margin: 0;">Apply for next visit</label>
                </div>
            </div>
            
            <!-- Hidden Date-Time Range Fields -->
            <div class="form-group" id="next_visit_dates" style="display: {{ isset($coupon) && $coupon->is_next_visit ? 'block' : 'none' }};">
                {!! Form::label('valid_from', 'Valid From') !!}
                {!! Form::datetimeLocal('valid_from', isset($coupon) ? $coupon->next_visit_start_date : null, ['class' => 'form-control']) !!}
            
                {!! Form::label('valid_until', 'Valid Until') !!}
                {!! Form::datetimeLocal('valid_until', isset($coupon) ? $coupon->next_visit_end_date : null, ['class' => 'form-control']) !!}
            </div>
            
            {!! Form::text('coupon_code', isset($coupon) ? $coupon->coupon_code : null, [
                'class' => 'form-control',
                'required',
            ]) !!}
        </div>
        <div class="form-group">
        {!! Form::label('discount', __('hms::lang.discount') . '*') !!}
            {!! Form::number('discount', $coupon->discount, [
                'class' => 'form-control',
                'required',
                'step' => '0.01',
            ]) !!}
        </div>
        <div class="form-group">
        {!! Form::label('discount_type', __('hms::lang.discount_type'). '*') !!}
            {!! Form::select('discount_type', $discount_type, $coupon->discount_type, [
                'class' => 'form-control',
                'required',
            ]) !!}
        </div>
      </div>
  
      <div class="modal-footer">

            <button type="button" class="btn btn-secondary" data-dismiss="modal">
                @lang('messages.close')
            </button>
            <button type="submit" class="btn btn-primary">
                @lang('cheque.save')
            </button>
        </div>

  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->

<script>
$(document).ready(function() {
    // console.log("i am on");
    var $select = $('#hms_room_type_id');

    if (!$select.hasClass("select2-hidden-accessible")) {
        // console.log("dfasfasdfsdfasdf");
        $select.select2({
            placeholder: "{{ __('hms::lang.type') }}"
        });
    }

    $('#select_all_room_types').on('change', function() {
        // console.log("asfasdfadsfsfasdfasdfsdfsadfafasdfasdfsda");
        if ($(this).is(':checked')) {
            var allValues = $select.find('option').map(function() {
                return $(this).val();
            }).get();

            $select.val(allValues).trigger('change');
        } else {
            $select.val([]).trigger('change');
        }
    });
});
</script>
<script>
    document.getElementById('apply_next_visit_checkbox').addEventListener('change', function() {
        var dateFields = document.getElementById('next_visit_dates');
        if (this.checked) {
            dateFields.style.display = 'block';
        } else {
            dateFields.style.display = 'none';
        }
    });
</script>
