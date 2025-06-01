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
<div class="row">
	<div class="col-md-12">
		<h4>@lang('lang_v1.combo'):</h4>
	</div>
	<div class="col-md-12">
		<div class="table-responsive">
			<table class="table bg-gray">
				<tr class="bg-green">
					<th>@lang('product.product_name')</th>
					@can('view_purchase_price')
						<th>@lang('product.default_purchase_price') (@lang('product.exc_of_tax'))</th>
						<th>@lang('product.default_purchase_price') (@lang('product.inc_of_tax'))</th>
					@endcan
					@can('access_default_selling_price')
						@can('view_purchase_price')
				        	<th>@lang('product.profit_percent')</th>
				        @endcan
				        <th>@lang('product.default_selling_price') (@lang('product.exc_of_tax'))</th>
				        <th>@lang('product.default_selling_price') (@lang('product.inc_of_tax'))</th>
			        @endcan
			        <th>@lang('sale.qty')</th>
			        <th class="text-center">
						@lang('lang_v1.total_amount_exc_tax')
					</th>
			        <th>@lang('lang_v1.variation_images')</th>
				</tr>
				@foreach($combo_variations as $variation)
				<tr>
					<td>
						{{$variation['variation']['product']->name}} 

						@if($variation['variation']['product']->type == 'variable')
							- {{$variation['variation']->name}}
						@endif
						
						({{$variation['variation']->sub_sku}})
					</td>
					@can('view_purchase_price')
						<td>
							<span style="float: right;">{{ $currency_symbol }} {{ number_format($variation['variation']->default_purchase_price,'6','.',',') }}</span>
						</td>
						<td>
							<span style="float: right;">{{ $currency_symbol }} {{ number_format($variation['variation']->dpp_inc_tax,'6','.',',') }}</span>
						</td>
					@endcan
					@can('access_default_selling_price')
						@can('view_purchase_price')
						<td>
							{{ @num_format($variation['variation']->profit_percent) }}
						</td>
						@endcan
						<td>
							<span style="float: right;">{{ $currency_symbol }} {{ number_format($variation['variation']->default_sell_price,'6','.',',') }}</span>
						</td>
						<td>
							<span style="float: right;">{{ $currency_symbol }} {{ number_format($variation['variation']->sell_price_inc_tax,'6','.',',') }}</span>
						</td>
					@endcan
					<td>
						<span class="display_currency" data-currency_symbol="false" data-is_quantity=true >{{$variation['quantity']}}</span> {{$variation['unit_name']}}
					</td>
					<td>
						<span style="float: right;">{{ $currency_symbol }} {{ number_format(($variation['variation']->default_purchase_price * $variation['quantity'] * $variation['multiplier']),'6','.',',') }}</span>
					</td>
					<td>
			        	@foreach($variation['variation']->media as $media)
			        		{!! $media->thumbnail([60, 60], 'img-thumbnail') !!}
			        	@endforeach
			        </td>
				</tr>
				@endforeach
			</table>
		</div>
	</div>
	<div class="col-md-12 text-right">
		<strong>@lang('product.default_selling_price'): </strong> 
		<span style="float: right;">{{ $currency_symbol }} {{ number_format($product->variations->first()->sell_price_inc_tax,'6','.',',') }}</span>
	</div>
</div>