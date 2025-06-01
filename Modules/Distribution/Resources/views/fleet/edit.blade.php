<div class="modal-dialog" role="document" style="width: 65%;height: 95vh; overflow-y: auto"  >
    <div class="modal-content">

        <style>
            .select2 {
                width: 100% !important;
            }
        </style>
        {!! Form::open(['url' => action('\Modules\Distribution\Http\Controllers\DistributionController@update', $fleet->id), 'method' =>
        'put', 'id' => 'fleet_form', 'enctype' => 'multipart/form-data' ])
        !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'distribution::lang.fleet' )</h4>
        </div>
        
        <div class="modal-body">
            <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('date', __( 'distribution::lang.date' )) !!}
                    {!! Form::text('date', null, ['class' => 'form-control', 'required', 'placeholder' => __(
                    'distribution::lang.date' ),
                    'id' => 'leads_date']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('code_for_vehicle', __( 'distribution::lang.code_for_vehicle' )) !!}
                    {!! Form::text('code_for_vehicle', $fleet->code_for_vehicle, ['class' => 'form-control', 'readonly',
                    'placeholder' => __(
                    'distribution::lang.code_for_vehicle' ),
                    'id' => 'code_for_vehicle']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('location_id', __( 'distribution::lang.location' )) !!}
                    {!! Form::select('location_id', $business_locations, $fleet->location_id, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'location_id']);
                    !!}
                </div>
            </div>
             </div><div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('vehicle_number', __( 'distribution::lang.vehicle_number' )) !!}
                    {!! Form::text('vehicle_number', $fleet->vehicle_number, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.vehicle_number' ),
                    'id' => 'vehicle_number']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('vehicle_type', __( 'distribution::lang.vehicle_type' )) !!}
                    {!! Form::text('vehicle_type', $fleet->vehicle_type, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.vehicle_type' ),
                    'id' => 'vehicle_type']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('vehicle_brand', __( 'distribution::lang.vehicle_brand' )) !!}
                    {!! Form::text('vehicle_brand', $fleet->vehicle_brand, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.vehicle_brand' ),
                    'id' => 'vehicle_brand']);
                    !!}
                </div>
            </div>
             </div><div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('vehicle_model', __( 'distribution::lang.vehicle_model' )) !!}
                    {!! Form::text('vehicle_model', $fleet->vehicle_model, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.vehicle_model' ),
                    'id' => 'vehicle_model']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('chassis_number', __( 'distribution::lang.chassis_number' )) !!}
                    {!! Form::text('chassis_number', $fleet->chassis_number, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.chassis_number' ),
                    'id' => 'chassis_number']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('engine_number', __( 'distribution::lang.engine_number' )) !!}
                    {!! Form::text('engine_number', $fleet->engine_number, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.engine_number' ),
                    'id' => 'engine_number']);
                    !!}
                </div>
            </div>
             </div><div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('battery_detail', __( 'distribution::lang.battery_detail' )) !!}
                    {!! Form::text('battery_detail', $fleet->battery_detail, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.battery_detail' ),
                    'id' => 'battery_detail']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('tyre_detail', __( 'distribution::lang.tyre_detail' )) !!}
                    {!! Form::text('tyre_detail', $fleet->tyre_detail, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.tyre_detail' ),
                    'id' => 'tyre_detail']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('income_account_id', __( 'distribution::lang.income_account' )) !!}
                    {!! Form::select('income_account_id', $income_accounts, $fleet->income_account_id, ['class' => 'form-control select2',
                    'required', 'disabled' => empty($access_account) ? true : false,
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'income_account_id']);
                    !!}
                </div>
            </div>
             </div><div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('expense_account_id', __( 'distribution::lang.expense_account' )) !!}
                    {!! Form::select('expense_account_id', $expense_accounts, $fleet->expense_account_id, ['class' => 'form-control select2',
                    'required', 'disabled' => empty($access_account) ? true : false,
                    'placeholder' => __(
                    'distribution::lang.please_select' ), 'id' => 'expense_account']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('starting_meter', __( 'distribution::lang.starting_meter' )) !!}
                    {!! Form::text('starting_meter', $fleet->starting_meter, ['class' => 'form-control', 'placeholder' => __(
                    'distribution::lang.starting_meter' ),
                    'id' => 'starting_meter']);
                    !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('notes', __( 'distribution::lang.notes' )) !!}
                    {!! Form::textarea('notes', $fleet->notes, ['class' => 'form-control', 'rows' => 3, 'placeholder' => __(
                    'distribution::lang.notes' ),
                    'id' => 'notes']);
                    !!}
                </div>
            </div>
             </div>


        </div>
        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary" id="save_leads_btn">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#leads_date').datepicker('setDate', '{{@format_date($fleet->date)}}');
     $('.select2').select2();
     
     $('input[name=vehicle_number]').change(function() {
        var vehicle_number = $(this).val();
        var fleet_id = '{{$fleet->id}}';
        $.ajax({
            type: 'POST',
            url: "{{ route('vehicle_check') }}",
            data: {
                vehicle_number: vehicle_number,
                fleet_id : fleet_id,
                _token: "{{ csrf_token() }}"
            },
            success: function(data) {
              if(data.success == false)
              {
                  toastr.error(data.msg);
                  $('input[name=vehicle_number]').val('');
              }
            }
        });
     });
</script>