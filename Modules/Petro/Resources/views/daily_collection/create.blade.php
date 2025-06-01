@php
$business_id = session()->get('user.business_id');
@endphp
<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DailyCollectionController@store'), 'method' =>
        'post',
        'id' =>
        'add_pumps_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.add_collection' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' ) . ':*') !!}
                            {!! Form::text('transaction_date', date('m/d/Y'), ['class' => 'form-control transaction_date', 'required',
                            'placeholder' => __(
                            'petro::lang.transaction_date' ), 'readonly' ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('collection_form_no', __( 'petro::lang.collection_form_no' ) . ':*') !!}
                            {!! Form::text('collection_form_no', $collection_form_no, ['class' => 'form-control collection_form_no', 'required',
                            'placeholder' => __(
                            'petro::lang.collection_form_no' ), 'readonly' ]); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('pump_operator', __( 'petro::lang.pump_operator' ) . ':*') !!}
                            {!! Form::select('pump_operator_id', $dailyShiftOperators, null , ['class' => 'form-control select2
                            pump_operator', 'required', 'id' => 'pump_operator_id',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('daily_shift_no', 'Daily Shift no:*') !!}
                            <select name="daily_shift_no" id="daily_shift_no"
                                    class="form-control select2 pump_operator"
                                    required
                                    style="width: 100%;">
                                <option value="">{{ __( 'petro::lang.please_select' ) }}</option>
                            </select>
                        </div>
                    </div>

                      {{--
                                        <div class="col-md-6" id="daily_shift_number_wrapper">
                                            <div class="form-group">
                                                {!! Form::label('daily_shift_number', 'Daily Shifts:') !!}
                                                <select name="daily_shift_number" id="daily_shift_number" class="form-control select2" style="width: 100%;">
                                                    <option value="">{{ __( 'petro::lang.please_select' ) }}</option>
                                                    @if(isset($all) && $all)
                                                        @foreach($all_pump_operators as $id => $name)
                                                            <option value="{{ $id }}">{{ $name }}</option>
                                                        @endforeach
                                                    @else
                                                        @foreach($assigned_operators as $id => $name)
                                                            <option value="{{ $id }}">{{ $name }}</option>
                                                        @endforeach
                                                    @endif
                                                </select>
                                            </div>
                                        </div>
                                        @if(\App\Utils\ModuleUtil::hasThePermissionInSubscription($business_id,'daily_shift'))
                                            <div class="col-md-6" id="shift_number_wrapper" style="display: none;">
                                                <div class="form-group">
                                                    {!! Form::label('shift_number', __( 'petro::lang.shift_number' ) . ':') !!}
                                                    <select name="shift_number" id="shift_number" class="form-control select2" style="width: 100%;">
                                                        <option value="">{{ __( 'petro::lang.please_select' ) }}</option>
                                                        @foreach($shiftNumbers as $shift)
                                                            <option value="{{ $shift }}">{{ $shift }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group">
                                                    {!! Form::label('shift', __( 'petro::lang.shift' ) . ':*') !!}
                                                    {!! Form::text('shift',null, ['class' => 'form-control shift', 'required',
                                                    'placeholder' => __(
                                                    'petro::lang.enter_shift' ),]); !!}
                                                </div>
                                            </div>
                                        @endif  --}}


                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('balance_collection', __( 'petro::lang.balance_collection' ) . ':*') !!}
                            {!! Form::text('balance_collection', null, ['class' => 'form-control balance_collection input_number', 'required',
                            'placeholder' => __(
                            'petro::lang.balance_collection' ),  'readonly' => 'readonly' ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('current_amount', __( 'petro::lang.current_amount' ) . ':*') !!}
                            {!! Form::text('current_amount', null, ['class' => 'form-control current_amount input_number', 'required',
                            'placeholder' => __(
                            'petro::lang.current_amount' ) ]); !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('location_id', __( 'petro::lang.location' ) . ':*') !!}
                            {!! Form::select('location_id', $locations, !empty($default_location) ? $default_location : null , ['class' => 'form-control select2
                            location_id', 'required',
                            'placeholder' => __(
                            'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                        </div>
                    </div>

                </div>
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary add_fuel_tank_btn">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        $('.location_id').select2();
        $('.pump_operator').select2();

        $('#pump_operator_id').change(function(){
            pump_operator_id = $('#pump_operator_id').val();

            $.ajax({
                method: 'get',
                url: '/petro/daily-collection/get-balance-collection/'+pump_operator_id,
                data: {  },
                success: function(result) {
                    if(result){
                        $('#balance_collection').val(result.balance_collection);
                    }
                },
            });
            $.ajax({
                method: 'get',
                url: '/petro/daily-collection/get-daily-shift/'+pump_operator_id,
                data: {  },
                success: function(result) {
                    let dropdown = $('#daily_shift_no');
                    dropdown.empty().append(`<option value="">{{ __('petro::lang.please_select') }}</option>`);

                    $.each(result, function (index, shiftNo) {
                        dropdown.append(
                            $('<option>', {
                                value: shiftNo,
                                text: shiftNo
                            })
                        );
                    });

                    dropdown.trigger('change');
                },
            });
        });
    </script>


<script>
    $(document).ready(function() {
        // Assigned operator IDs from Laravel
        var assignedOperators = @json(array_column($assigned, 'id'));
        // Only apply logic if there are assigned operators
        if (assignedOperators.length > 0) {
            // Make shift required
            $('.shift').attr('required', true);
        } else {
            // No assigned operators, ensure shift is not required
            $('.shift').removeAttr('required');
        }
    });
</script>


<script>
    $(document).ready(function () {
    $('#pump_operator_id').change(function () {
        let selected = $(this).val();

        if (selected) {
            $('#shift_number_wrapper').show();
        } else {
            $('#shift_number_wrapper').hide();
            $('#shift_number').val(''); // reset selection
        }
    });
});

</script>

