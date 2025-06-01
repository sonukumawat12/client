@php
	$bus_id = request()->session()->get('business.id');
	$business = \App\Business::where('id', $bus_id)->first();
	$currency_symbol = \App\Currency::select('symbol')->where('id', $business->currency_id)->first();
	if(empty($currency_symbol)){
		$currency_symbol = '$';
	}else{
		$currency_symbol = $currency_symbol->symbol;
	}
@endphp
<br>
<div class="row">
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table bg-gray">
				<tr class="bg-green">
					@can('view_purchase_price')
						<th>@lang('product.default_purchase_price') (@lang('product.exc_of_tax'))</th>
						<th>@lang('product.default_purchase_price') (@lang('product.inc_of_tax'))</th>
					@endcan
					@can('access_default_selling_price')
						@can('view_purchase_price')
				        	<th>@lang('product.profit_percent')</th>
				        @endcan
				        <th>@lang('product.default_selling_price') (@lang('product.exc_of_sales_tax'))</th>
				        <th>@lang('product.default_selling_price') (@lang('product.inc_of_sales_tax'))</th>
				    @endcan
				    @if(!empty($allowed_group_prices))
			        	<th>@lang('lang_v1.group_prices')</th>
			        @endif
			        <th>@lang('lang_v1.variation_images')</th>
				</tr>
				@foreach($product->variations as $variation)
				<tr>
					@can('view_purchase_price')
					<td>
						<span style="float: right;">{{ $currency_symbol }} {{ currencyFormat($variation->default_purchase_price) }}</span>
					</td>
					<td>
						<span style="float: right;">{{ $currency_symbol }} {{ currencyFormat($variation->dpp_inc_tax) }}</span>
					</td>
					@endcan
					@can('access_default_selling_price')
						@can('view_purchase_price')
						<td>
							{{ @num_format($variation->profit_percent) }}
						</td>
						@endcan
						<td>
							<span style="float: right;">{{ $currency_symbol }} {{ currencyFormat($variation->default_sell_price) }}</span>
						</td>
						<td>
							<span style="float: right;">{{ $currency_symbol }} {{ currencyFormat($variation->sell_price_inc_tax) }}</span>
						</td>
					@endcan
					@if(!empty($allowed_group_prices))
			        	<td class="td-full-width">
			        		@foreach($allowed_group_prices as $key => $value)
			        			<strong>{{$value}}</strong> - @if(!empty($group_price_details[$variation->id][$key]))
			        				<span style="float: right;">{{ $currency_symbol }} {{ currencyFormat($group_price_details[$variation->id][$key]) }}</span>
			        			@else
			        				0
			        			@endif
			        			<br>
			        		@endforeach
			        	</td>
			        @endif
			        <td>
			        	@foreach($variation->media as $media)
			        		{!! $media->thumbnail([60, 60], 'img-thumbnail') !!}
			        	@endforeach
			        </td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
</div>