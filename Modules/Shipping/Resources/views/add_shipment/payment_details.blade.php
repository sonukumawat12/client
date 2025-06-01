@component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
	<div class="box-body payment_row" data-row_id="0">
		<div id="payment_rows_div">
			
			@include('sale_pos.partials.payment_row_form', ['row_index' => 0])
			<hr>
		</div>

		<div class="row">
			<div class="col-md-12">
				<button type="button" class="btn btn-primary pull-right"
					id="add-payment-row">@lang('sale.add_payment_row')</button>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span
						id="payment_due">0.00</span>
				</div>

			</div>
		</div>
		<br>
		@if(!isset($view_page))
		<div class="row">
			<div class="col-sm-12">
				<button type="submit" id="save_formBtn" class="btn btn-success pull-right btn-flat">@lang('messages.save')</button>
			</div>
		</div>
		@endif
	</div>
	@endcomponent