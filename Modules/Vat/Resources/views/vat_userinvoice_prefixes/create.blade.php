<div class="modal-dialog" role="document" style="width: 50%;">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatUserInvoicePrefixController@store'), 'method' => 'post', 'id' =>
        'transfer_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'vat::lang.add' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('date_time', __( 'vat::lang.date_time' ) . ':*') !!}
                        {!! Form::input('datetime-local', 'date_time', null, [
                            'class' => 'form-control',
                            'required' => 'required',
                            'placeholder' => __('vat::lang.date_time'),
                            'style' => 'width: 100%;'
                        ]) !!}

                    </div>
                </div>
                    
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('location_id', __( 'vat::lang.location' ) . ':*') !!}
                        {!! Form::select('location_id',$business_locations, null, ['class' => 'form-control select2', 'required',  'style' => 'width: 100%;']); !!}
                    </div>
                </div>
            </div>
            <div class="row">
                 <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('user_id', __( 'vat::lang.user' ) . ':*') !!}
                        {!! Form::select('user_id',$users, null, ['class' => 'form-control select2', 'required',  'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('prefix_id', __( 'vat::lang.vat_invoice_prefix' ) . ':*') !!}
                        {!! Form::select('prefix_id',$prefixes, null, ['class' => 'form-control select2','placeholder' => __('vat::lang.select_one'), 'required',  'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('prefix_id2', __( 'vat::lang.vat_invoice2_prefix' ) . ':*') !!}
                        {!! Form::select('prefix_id2',$prefixes2, null, ['class' => 'form-control select2','placeholder' => __('vat::lang.select_one'), 'required',  'style' => 'width: 100%;']); !!}
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
        $(".select2").select2();
    </script>
  