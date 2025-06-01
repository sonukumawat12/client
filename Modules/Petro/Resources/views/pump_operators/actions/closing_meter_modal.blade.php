<div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <div style="display: flex">
                <h4 class="modal-title" style="width: 80%">@lang( 'petro::lang.closing_meter' )</h4>
                {!! Form::label('shift_number', __( 'petro::lang.shift_number' ) . ' : ' . (sprintf("%04d", $shift_number)), ['style' => 'font-size: 23px; color: red; font-weight: bold;']) !!}
            </div>
            
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="col-md-12">
                    @foreach ($pumps as $pump)
                    @if(!empty($pump->status) && $pump->status == 'close')
                    <div class="col-md-3 text-center" style="padding: 0px; margin: 10px;">
                        <a class="btn  btn-primary btn-flat"
                            href="{{action('\Modules\Petro\Http\Controllers\ClosingShiftController@show', $pump->assignment_id)}}"
                            style="height: 160px; width:100%; background: #800080; border: 0px; text-align: center !important; margin: 0px 10px;">
                            <span class="label label-danger" style="font-size: 17px;">@lang('petro::lang.closed')</span>
                            <h2 style="padding-top: 0px; margin-top: 10px">{{$pump->pump_no}} </h2>
                            <h4 style="padding-top: 0px; padding-bottom: 10px; margin-top; 0px">{{$pump->pumper_name}}
                            </h4>
                        </a>
                    </div>
                    @elseif($pump->is_confirmed == 0)
                    <div class="col-md-3 text-center" style="padding: 0px; margin: 10px;">
                        <a class="btn  btn-primary btn-flat"
                            href="#"
                            style="height: 160px; width:100%; background: #F9A825; border: 0px; text-align: center !important; margin: 0px 10px;">
                            <span class="label label-danger" style="font-size: 17px;">Pending @lang('petro::lang.receive_pump')</span>
                            <h2 style="padding-top: 0px; margin-top: 10px">{{$pump->pump_no}} </h2>
                            <h4 style="padding-top: 0px; padding-bottom: 10px; margin-top; 0px">{{$pump->pumper_name}}
                            </h4>
                        </a>
                    </div>
                    @else
                    <div class="col-md-3 text-center" style="padding: 0px; margin: 10px;">
                        <a class="btn  btn-primary btn-flat"
                            style="height: 160px; width:100%; background: #F9A825; border: 0px;  margin: 0px 10px;"
                            href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorActionsController@getClosingMeter', $pump->pump_id)}}">
                            <h2 style="padding-top: 0px; margin-top: 48px;">{{$pump->pump_no}}</h2>
                            <h4 style="padding-top: 0px; padding-bottom: 10px; margin-top; 0px">{{$pump->pumper_name}}
                            </h4>
                        </a>
                    </div>
                    @endif

                    @endforeach
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>



    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>

</script>