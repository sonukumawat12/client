

<div class="modal-dialog" role="document" style="width: 50%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.update_passcode' )</h4>
        </div>

        <div class="modal-body">
            
            {!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PumpOperatorController@store_passcode'), 'method' =>'post', 'id' =>'add_settings_form' ]) !!}
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('current_pass', __( 'petro::lang.current_pass' ) . ':*') !!}
                        {!! Form::number('current_pass', null, ['class' =>
                        'form-control', 'required', 'placeholder' => __('petro::lang.current_pass' ) ]); !!}
                    </div>
                </div>
                
                
                <br><button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
                
            </div>
            
        {!! Form::close() !!}
        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>



    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
$(".select2").select2();
</script>