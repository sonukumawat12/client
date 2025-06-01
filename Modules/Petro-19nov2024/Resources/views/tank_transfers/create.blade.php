<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\TankTransferController@store'), 'method' => 'post', 'id' =>
        'transfer_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.add_tank_transfer' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('transfer_no', __( 'petro::lang.transfer_no' ) . ':*') !!}
                        {!! Form::text('transfer_no', $transfer_no , ['class' => 'form-control', 'required','readonly', 'placeholder' => __(
                        'petro::lang.transfer_no' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('from_tank', __( 'petro::lang.from_tank' ) . ':*') !!}
                        {!! Form::select('from_tank', $tank_numbers, null , ['class' => 'form-control add_from_tank select2', 'required', 'placeholder' => __(
                        'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
            </div>
            <div class="row">
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('from_qty', __( 'petro::lang.from_qty' ) . ':*') !!}
                        {!! Form::text('from_qty', null, ['class' => 'form-control', 'disabled',
                        'placeholder' => __(
                        'petro::lang.from_qty' ) ]); !!}
                    </div>
                </div>
    
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('quantity', __( 'petro::lang.quantity' ) . ':*') !!}
                        {!! Form::text('quantity', null, ['class' => 'form-control', 'required',
                        'placeholder' => __(
                        'petro::lang.quantity' ) ]); !!}
                    </div>
                </div>
             </div>
            <div class="row">
                 <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('to_tank', __( 'petro::lang.to_tank' ) . ':*') !!}
                        {!! Form::select('to_tank', $tank_numbers, null , ['class' => 'form-control add_to_tank select2', 'required', 'placeholder' => __(
                        'petro::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('date', __( 'petro::lang.date' ) . ':*') !!}
                        {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control fuel_tank_date',
                        'required', 'placeholder' => __(
                        'petro::lang.date' ) ]); !!}
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
        $('.select2').select2();
        
        $('.fuel_tank_date').datepicker();
        
        var originalToTankOptions = $('.add_to_tank').html();
        
        var tank_bals = JSON.parse('{!! json_encode($tank_bals) !!}');

        $(document).on('change','.add_from_tank', function () {
            var selectedOptionValue = $(this).val();
            
            __write_number($("#from_qty"),tank_bals[selectedOptionValue]);

            $('.add_to_tank').html(originalToTankOptions);

            $('.add_to_tank option[value="' + selectedOptionValue + '"]').remove();
        });

        
    </script>