
<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">

        {!! Form::open(['url' =>
        action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@storeBulk'), 'method' =>
        'post',
        'id' =>
        'receive_pump_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <div style="display: flex">
                <h4 class="modal-title" style="width: 80%">@lang( 'petro::lang.assign_pumps' )</h4>
                {!! Form::label('shift_number', __( 'petro::lang.shift_number' ) . ' : ' . (sprintf("%04d", $shift_number + 1)), ['style' => 'font-size: 23px; color: red; font-weight: bold;']) !!}
            </div>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('date', __( 'petro::lang.date' ) . ':*') !!}
                                {!! Form::input('datetime-local', 'date', 
                                    \Carbon\Carbon::now()->format('Y-m-d\TH:i'), 
                                    ['class' => 'form-control', 'required', 'placeholder' => __('petro::lang.please_select'), 'style' => 'width: 100%;']) !!}

                            </div>
                        </div>


                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('pump_operator', __( 'petro::lang.pump_operator' ) . ':*') !!}
                                {!! Form::select('pump_operator', $pump_operators->pluck('name', 'id'),
                                null , ['class' => 'form-control select2', 'id' => 'pump_operator_selector','required',
                                'placeholder' => __(
                                'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-group">
                                {!! Form::label('pump', __( 'petro::lang.pump' ) . ':*') !!}
                                {!! Form::select('pump[]', $pumps,
                                null , ['id' => 'pump-selector', 'class' => 'form-control select2','multiple', 'style' => 'width: 100%;','required']); !!}
                            </div>
                        </div>
                    
                    </div>

                   
                </div>
            </div>
            <br>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary confirm_meter_reading_btn">@lang('messages.submit' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

   
    <script>
        $(".select2").select2();
        
        var isUpdating = false; // Flag to prevent loop
        $(document).ready(function () {
            $('#pump_operator_selector').change(function (e) {
                $('#pump-selector').val("").change();
            })
            
            $('#pump-selector').change(function (e) {
                if (isUpdating) return; // Prevent re-triggering
                
                let pumps = $(this).val();
                if (pumps && pumps.length) {let pump_operator_id = $('#pump_operator_selector').val();
                    pump_operator_id = pump_operator_id ? parseInt(pump_operator_id) : "";
                
                    const pump_operators = @json($pump_operators);
                    const pump_assignments = @json($pump_assignments); // Get pump assignments data
                
                    // Find the current pump operator
                    const pump_operator = pump_operators.find(po => po.id === pump_operator_id) || null;
                
                    if (pump_operator) {
                        const all_pumps = @json($pumps);
                
                        // Iterate through selected pumps
                        let revert_pumps = []
                        pumps.forEach(pump_id => {
                            pump_id = parseInt(pump_id);
                            const assigned_operator = pump_assignments.find(pa => pa.pump_id === pump_id) || null;
                            if(assigned_operator) {
                                const assigned_pump_operator = pump_operators.find(apo => apo.id === assigned_operator.pump_operator_id);
                                if(assigned_pump_operator && !assigned_pump_operator.settlement_no) {
                                    // added pump id in the revert pump
                                    revert_pumps.push(pump_id);
                                }
                            }
    
                        });
                        if(revert_pumps.length) {
                            // Prevent infinite loop by setting flag
                            isUpdating = true;
                            pumps = pumps.filter(pump => !revert_pumps.includes(pump));
                            $(this).val(pumps).trigger('change');
                            isUpdating = false; 
                            
                            const unsettled_pumps = revert_pumps.map(pump_id => all_pumps[pump_id])
            
                            alert(`Please complete the Settlement for ${unsettled_pumps.join(", ")} to select this pump again.`);
                        }
                    
                    }
                }

           }) 
        });
    </script>
    