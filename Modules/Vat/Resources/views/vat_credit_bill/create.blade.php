<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatCreditBillController@store'), 'method' => 'post', 'id' =>
        'transfer_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'vat::lang.add' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="row">
               
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('customer_id', __( 'vat::lang.customer' ) . ':*') !!}
                        {!! Form::select('customer_id',$customers, null, ['class' => 'form-control select2', 'required',  'style' => 'width: 100%;','placeholder' => __('lang_v1.please_select')]); !!}
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('customer_group', __( 'vat::lang.customer_group' ) . ':*') !!}
                        {!! Form::select('customer_group',$customer_group, null, ['class' => 'form-control select2',  'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('linked_accounts', __( 'vat::lang.linked_account' ) . ':*') !!}
                        {!! Form::select('linked_accounts',['no' => __('messages.no'),'yes' => __('messages.yes')], null, ['class' => 'form-control select2', 'required',  'style' => 'width: 100%;','placeholder' => __('lang_v1.please_select')]); !!}
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
  