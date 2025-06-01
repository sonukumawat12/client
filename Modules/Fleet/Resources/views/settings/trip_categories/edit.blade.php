<div class="modal-dialog" role="document">
    <div class="modal-content">
        {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\TripCategoryController@update', $fuel->id), 'method' => 'put', 'id' => 'trip_category_edit_form']) !!}
        
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
            <h4 class="modal-title">@lang('fleet::lang.trip_category')</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-12">
                    {!! Form::label('date', __('fleet::lang.date') . ':*') !!}
                    {!! Form::text('date', @format_datetime($fuel->date), ['class' => 'form-control', 'disabled', 'placeholder' => __('fleet::lang.date')]) !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('name', __('fleet::lang.trip_category_name') . ':*') !!}
                    {!! Form::text('name', $fuel->name, ['class' => 'form-control', 'placeholder' => __('fleet::lang.trip_category_name'), 'id' => 'trip_category_name', 'required']) !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('amount_method', __('fleet::lang.amount_calculated_method') . ':*') !!}
                    <div class="form-group col-sm-12">
                        {!! Form::select('manual_entry', ['manual_entry' => 'Manual Entry', 'automatic' => 'Automatic'], $fuel->manual_entry ?? null, ['class' => 'form-control', 'id' => 'manual_entry', 'placeholder' => 'Select Entry Way', 'required']) !!}
                    </div>
                    <div class="clearfix"></div>
                    <div class="col-md-6">
                        <label>
                            <input type="radio" name="amount_method" class="amount_method" value="km_distance" id="radio_km_distance" {{ $fuel->amount_method === 'km_distance' ? 'checked' : '' }}>
                            (Per km Rate) * (Distance)
                        </label>
                    </div>
                    <div class="col-md-6">
                        <label>
                            <input type="radio" name="amount_method" class="amount_method" value="km_distance_qty" id="radio_km_distance_qty" {{ $fuel->amount_method === 'km_distance_qty' ? 'checked' : '' }}>
                            (Per km Rate) * (Distance) * (Quantity)
                        </label>
                    </div>
                    <div class="form-group col-sm-12" id="manual_entry_amount_field" style="display: {{ $fuel->manual_entry === 'manual_entry' ? 'block' : 'none' }};">
                        {!! Form::label('trip_amount', 'Trip Amount:') !!}
                        {!! Form::text('trip_amount', $fuel->trip_amount ?? null, ['class' => 'form-control', 'id' => 'trip_amount', 'placeholder' => 'Enter Trip Amount']) !!}
                    </div>
                </div>
            </div>
        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save_trip_category">@lang('messages.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('messages.close')</button>
        </div>

        {!! Form::close() !!}
    </div>
</div>

<script>
$(document).ready(function () {
    const manualEntryDropdown = $('#manual_entry');
    const radioKmDistance = $('#radio_km_distance');
    const radioKmDistanceQty = $('#radio_km_distance_qty');
    const manualEntryAmountField = $('#manual_entry_amount_field');
    const tripAmountInput = $('#trip_amount');

    // Toggle fields based on dropdown value
    function toggleFields() {
        if (manualEntryDropdown.val() === 'manual_entry') {
            radioKmDistance.prop('disabled', true);
            radioKmDistanceQty.prop('disabled', true);
            manualEntryAmountField.show();
            tripAmountInput.prop('required', true);
        } else {
            radioKmDistance.prop('disabled', false);
            radioKmDistanceQty.prop('disabled', false);
            manualEntryAmountField.hide();
            tripAmountInput.prop('required', false);
        }
    }

    // Restrict trip_amount to numbers and decimals
    tripAmountInput.on('input', function () {
        this.value = this.value.replace(/[^0-9.]/g, '');
    });

    // Initial state
    toggleFields();
    manualEntryDropdown.on('change', toggleFields);

    // AJAX form submission
    $('#trip_category_edit_form').on('submit', function (e) {
        e.preventDefault();
        let form = $(this);
        let btn = $('#save_trip_category');
        btn.prop('disabled', true);

        $.ajax({
            url: form.attr('action'),
            type: 'POST', // Laravel expects POST with _method=PUT
            data: form.serialize() + '&_method=PUT',
            success: function (response) {
                if (response.success) {
                    alert(response.msg); // Replace with your preferred notification
                    $('#trip_category_edit_form').parents('.modal').modal('hide');
                    location.reload(); // Refresh page or update UI as needed
                } else {
                    alert(response.msg);
                }
            },
            error: function (xhr) {
                let errorMsg = 'An error occurred: ' + (xhr.responseJSON?.msg || 'Unknown error');
                alert(errorMsg);
            },
            complete: function () {
                btn.prop('disabled', false);
            }
        });
    });
});
</script>