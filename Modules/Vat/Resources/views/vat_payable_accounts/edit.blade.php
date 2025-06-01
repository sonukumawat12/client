<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatPayableToAccountController@update',[$data->id]), 'method' => 'put', 'id' =>
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
                        {!! Form::label('type', __( 'vat::lang.vat_opening_balance_type' ) . ':*') !!}
                        {!! Form::select('type',['vat_payable_account' => __('vat::lang.vat_payable_account'),'vat_receivable_account' => __('vat::lang.vat_receivable_account')], $data->type, ['class' => 'form-control select2', 'required',  'style' => 'width: 100%;','placeholder' => __('lang_v1.please_select')]); !!}
                    </div>
                </div>
                
                <div class="col-md-12" id="payable_fields" @if($data->type == 'vat_receivable_account') hidden @endif>
                    <div class="form-group">
                        {!! Form::label('account_id', __( 'vat::lang.vat_payable_account' ) . ':*') !!}
                        {!! Form::select('account_id',$accounts, $data->account_id, ['class' => 'form-control select2', 'required',  'style' => 'width: 100%;','placeholder' => __('lang_v1.please_select')]); !!}
                    </div>
                </div>
                
                <div class="col-md-12" id="receivable_fields" @if($data->type == 'vat_payable_account') hidden @endif>
                    <div class="form-group">
                        {!! Form::label('rec_account_id', __( 'vat::lang.vat_receivable_account' ) . ':*') !!}
                        {!! Form::select('rec_account_id',$asset_accounts, $data->account_id, ['class' => 'form-control select2', 'required',  'style' => 'width: 100%;','placeholder' => __('lang_v1.please_select')]); !!}
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('amount', __( 'vat::lang.opening_balance_amount' ) . ':*') !!}
                        {!! Form::text('amount',$data->amount, ['class' => 'form-control', 'required',  'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="form-group">
                        {!! Form::label('note', __( 'vat::lang.note' ) . ':*') !!}
                        {!! Form::textarea('note',$data->note, ['class' => 'form-control ',   'style' => 'width: 100%;','rows' => '3']); !!}
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
    $(document).ready(function(){
        $('#type').trigger('change');
    })
    $(document).on('change','#type',function(){
        var type = $(this).val();
        if(type == 'vat_payable_account'){
            $("#payable_fields").show();
            $("#receivable_fields").hide();
            
            $("#account_id").prop('required',true);
            $("#rec_account_id").prop('required',false);
            
        }else if(type == 'vat_receivable_account'){
            $("#payable_fields").hide();
            $("#receivable_fields").show();
            
            $("#account_id").prop('required',false);
            $("#rec_account_id").prop('required',true);
        }else{
            $("#payable_fields").show();
            $("#receivable_fields").show();
            
            $("#account_id").prop('required',false);
            $("#rec_account_id").prop('required',false);
        }
    });
</script>
  