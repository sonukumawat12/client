<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\SettingsController@store'), 'method' => 'post', 'id' =>
        'settings_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'vat::lang.add_settings' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('effective_date', __( 'vat::lang.effective_date' ) . ':*') !!}
                        {!! Form::date('effective_date', null , ['class' => 'form-control', 'required','placeholder' => __(
                        'vat::lang.effective_date' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                 <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('tax_report_name', __( 'vat::lang.tax_report_name' ) . ':*') !!}
                        {!! Form::select('tax_report_name', ['vat' =>  __('vat::lang.vat'),'tax' =>  __('vat::lang.tax')], null , ['class' => 'form-control add_tax_report_name select2', 'required', 'placeholder' => __(
                        'vat::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('vat_period', __( 'vat::lang.vat_period' ) . ':*') !!}
                        {!! Form::select('vat_period', ['daily' => 'Daily','weekly' => 'Weekly','monthly' => 'Monthly','bi-monthly' => 'Bi Monthly','quarterly' => 'Quartely','bi-annually' => 'Bi Annually','annually' => 'Annually','custom' => __('vat::lang.custom_starting_date')], null , ['class' => 'form-control add_vat_period select2', 'required', 'placeholder' => __(
                        'vat::lang.please_select' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
            </div>
            
            <div class="row" id="custom_fields" hidden>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('report_cycle_starting_date', __( 'vat::lang.report_cycle_starting_date' ) . ':*') !!}
                        {!! Form::date('report_cycle_starting_date', null , ['class' => 'form-control','placeholder' => __(
                        'vat::lang.report_cycle_starting_date' ), 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('report_cycle_ending_date', __( 'vat::lang.report_cycle_ending_date' ) . ':*') !!}
                        {!! Form::date('report_cycle_ending_date', null , ['class' => 'form-control', 'placeholder' => __(
                        'vat::lang.report_cycle_ending_date' ), 'style' => 'width: 100%;']); !!}
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
        
        $(document).ready(function(){
            $(document).change('#vat_period',function(){
                var vat_period = $('#vat_period').val();
                
                if(vat_period == 'custom'){
                    $("#custom_fields").show();
                    $("#report_cycle_ending_date").attr('required',true);
                    $("#report_cycle_starting_date").attr('required',true);
                }else{
                    $("#custom_fields").hide();
                    $("#report_cycle_ending_date").attr('required',false);
                    $("#report_cycle_starting_date").attr('required',false);   
                }
            })
        });
    </script>