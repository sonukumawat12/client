
<div class="hide bank_transfer_fields">
    @if($contact_details->type == 'supplier')
      <div class="col-md-6">
            <div class="form-group">
                {!! Form::label("bank_name",__('lang_v1.bank_name')) !!}
                {!! Form::select( "bank_name", $bank_accounts->pluck('name', 'name'), null, ['class' => 'form-control', 'placeholder' => __('lang_v1.please_select')]); !!}
            </div>
        </div>
    @endif
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label("cheque_number",__('lang_v1.cheque_no')) !!}
			{!! Form::text("cheque_number", null, ['class' => 'form-control', 'placeholder' => __('lang_v1.cheque_no')]); !!}
		</div>
	</div>
	<div class="col-md-6">
		<div class="form-group">
			{!! Form::label("cheque_date",__('lang_v1.cheque_date')) !!}
			{!! Form::date("cheque_date", null, ['class' => 'form-control cheque_date', 'placeholder' => __('lang_v1.cheque_date')]); !!}
		</div>
	</div>
</div>

