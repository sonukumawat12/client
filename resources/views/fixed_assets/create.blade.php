
<div class="modal-dialog modal-lg" role="document" style="width: 60%">
    <div class="modal-content">
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang('account.add_fixed_asset')</h4>
        </div>
        <div class="modal-body">
            {!! Form::open(['url' => action('FixedAssetController@store'), 'method' => 'post' ]) !!}
           
               <div class="row">
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('date', __('account.date')) !!}
                            <input type="datetime-local" class="form-control" name="date_of_operation" required >
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('account_id', __('account.account') . ':') !!}
                            {!! Form::select('account_id', $accounts, null, ['class' => 'form-control select2',
                            'placeholder' => 'Please select', 'required', 'style' => 'width:100%']); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('asset_name', __('account.asset_name')) !!}
                            {!! Form::text('asset_name', null, ['class' => 'form-control',
                            'required'])
                            !!}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('asset_location', __('account.asset_location')) !!}
                            {!! Form::text('asset_location', null, ['class' => 'form-control',
                            'required'])
                            !!}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::label('amount', __('account.amount')) !!}
                            {!! Form::text('amount', null, ['class' => 'form-control',
                            'required'])
                            !!}
                        </div>
                    </div>
                        
                   
                </div>
                
                 <button type="submit" class="btn btn-primary add_btn">@lang( 'messages.save' )</button>

                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close'
                        )</button>
            
            {!! Form::close() !!}
            
            </div>

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->

    <script>
        $('.journal_date').datepicker("setDate", new Date());
        $('.select2').select2();
    </script>