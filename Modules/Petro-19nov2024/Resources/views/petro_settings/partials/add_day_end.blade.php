<div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\DayEndSettlementController@store'), 'method' =>
        'post',
        'id' =>
        'add_day_end' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.day_end_settlement' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('date_and_time', __( 'petro::lang.date_and_time' ) . ':*') !!}
                            {!! Form::text('date_and_time', @format_datetime(date('Y-m-d H:i')), ['class' => 'form-control', 'required', 'readonly',
                            'placeholder' => __(
                            'petro::lang.date_and_time' ) ]); !!}
                        </div>
                    </div>
                    
                    
                     <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('day_end_date', __( 'petro::lang.day_end_date' ) . ':*') !!}
                            {!! Form::text('day_end_date', null, ['class' => 'form-control date_and_time', 'required', 'readonly',
                            'placeholder' => __(
                            'petro::lang.day_end_date' ) ]); !!}
                        </div>
                    </div>
                    
                    <div class="clearfix"></div>
                    
                    <hr>
                    <div class="clearfix"></div>
                    
                    <div class="row">
                        <div class="col-sm-6">
                            <h5 class="text-center"><b>@lang('petro::lang.pending_pumps')</b></h5><hr><br>
                            <div class="col-md-6"><b>@lang('petro::lang.pump')</b></div>
                            <div class="col-md-6"><b>@lang('petro::lang.no_operation')</b></div>
                            <div class="clearfix"></div>
                            <div class="pending_pumps">{!! $html !!}</div>
                        </div>
                        
                        <div class="col-sm-6">
                            <h5 class="text-center"><b>@lang('petro::lang.pumps_in_settlement')</b></h5><hr>
                            <div class="col-md-6"><b>@lang('petro::lang.pump')</b></div>
                            <div class="col-md-6"><b>@lang('petro::lang.settlement_no')</b></div>
                            <div class="clearfix"></div>
                            <div class="sold_pumps">{!! $html_sold !!}</div>
                        </div>
                    </div>
                    
                    
                    
                </div>
                
               
            </div>
            <div class="clearfix"></div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>

            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
      
        $('.date_and_time').datepicker("setDate", new Date());
        
        $(document).on('change','.date_and_time',function(){
            $('.pending_pumps').empty();
            $('.sold_pumps').empty();
            
            $.ajax({
                url: '/petro/day-end-settlement-pumps',
                type: 'GET', // or 'POST' if needed
                data: {
                    date: $(this).val()
                },
                success: function(response) {
                    // Append the response to the pending_pumps div
                    $('.pending_pumps').html(response.pending);
                    $('.sold_pumps').html(response.sold);
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error: ', error);
                }
            });
        });
        
        $('.select2').select2();
    </script>