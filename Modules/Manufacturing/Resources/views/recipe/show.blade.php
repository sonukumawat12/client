<style>
	.py-3{
		padding-top:1rem;
		padding-bottom:1rem;
	}
	.pr-5{
		padding-right:5rem;
	}
	.pr-4{
		padding-right:4rem;
	}
</style>
<div class="modal-dialog modal-lg" role="document">
	<div class="modal-content">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">{{$recipe->variation->full_name}}</h4>
	    </div>
	    <div class="modal-body">
	    	<div class="row">
      			<div class="col-md-6">
      				{!! $recipe->variation->product->product_description !!}
      			</div>
      			<div class="col-md-6">
      				@foreach($recipe->variation->media as $media)
			        	{!! $media->thumbnail([60, 60], 'img-thumbnail') !!}
			        @endforeach
      			</div>
      		</div>
      		<div class="row">
      			<div class="col-md-12">
      				<table class="table">
						<thead>
							<tr>
								<th>@lang('manufacturing::lang.ingredient')</th>
								<th>@lang('lang_v1.quantity')</th>
								<th>@lang('lang_v1.price')</th>
							</tr>
						</thead>
						<tbody>
							@php
								$ingredient_groups = [];
								$ingredient_total_price = 0;
							@endphp
							@foreach($ingredients as $ingredient)
								@php
									$ingredient_price = $ingredient['quantity']*$ingredient['dpp_inc_tax']*$ingredient['multiplier'];
									$ingredient_total_price += $ingredient_price;
								@endphp
								@if(empty($ingredient['mfg_ingredient_group_id']))
									<tr>
										<td>
											{{$ingredient['full_name']}}
										</td>
										<td><span class="display_currency" data-currency_symbol="false">{{$ingredient['quantity']}}</span> {{$ingredient['unit']}}</td>
										<td><span class="display_currency" data-currency_symbol="true">{{$ingredient_price}}</span></td>
									</tr>
								@else
									@php
										$ingredient_groups[$ingredient['mfg_ingredient_group_id']][] = $ingredient;
									@endphp
								@endif	
							@endforeach
							@foreach($ingredient_groups as $ingredient_group)
								<tr>
									<td colspan="3" class="bg-gray"><strong>{{$ingredient_group[0]['ingredient_group_name'] ?? ''}}</strong> @if(!empty($ingredient_group[0]['ig_description']))
									- {{$ingredient_group[0]['ig_description']}}
								@endif</td>
								</tr>
								
								@foreach($ingredient_group as $ingredient)
									<tr>
										<td>
											{{$ingredient['full_name']}}
										</td>
										<td><span class="display_currency" data-currency_symbol="false">{{$ingredient['quantity']}}</span> {{$ingredient['unit']}}</td>
										<td><span class="display_currency" data-currency_symbol="true">{{$ingredient['quantity']*$ingredient['dpp_inc_tax']*$ingredient['multiplier']}}</span></td>
									</tr>
								@endforeach
							@endforeach
						</tbody>
						<tfoot>
							<tr>
								<td colspan="2" class="text-right"><strong>@lang('manufacturing::lang.ingredients_cost')</strong></td>
								<td><span class="display_currency" data-currency_symbol="true">{{$ingredient_total_price}}</span></td>
							</tr>
						</tfoot>
					</table>
      			</div>
      		</div>
            @if(count($costs)>0)
			<div class="row">
				<div class="col-md-12">
				  <table class="table">
						<thead>
							<tr>
								<th>@lang('manufacturing::lang.type')</th>
								<th>@lang('manufacturing::lang.name')</th>
								<th>@lang('manufacturing::lang.fixed_percentage')</th>
								<th>@lang('manufacturing::lang.value')</th>
								<th>@lang('manufacturing::lang.total')</th>
							</tr>
						</thead>
						<tbody>
						@foreach($costs as $cost)
						<tr>
							<td>{{ucfirst($cost->type)}}</td>
							<td>{{ucfirst($cost->name)}}</td>
							<td>{{ucfirst($cost->cost_type)}}</td>
							<td>{{$cost->cost_value}}</td>
							<td><span class="display_currency" data-currency_symbol="true">{{$cost->cost_total}}</td>
						</tr>
						@endforeach
						</tbody>
						
					</table>
				</div>
			</div>
			@endif
      		<div class="row">
      			<div class="col-md-6">
      				<!-- <strong>@lang('manufacturing::lang.wastage'):</strong>
      				{{$recipe->waste_percent ?? 0}} % <br> -->
      				<strong>@lang('manufacturing::lang.total_output_quantity'):</strong>
      				@if(!empty($recipe->total_quantity)){{@format_quantity($recipe->total_quantity)}}@else 0 @endif @if(!empty($recipe->sub_unit)) {{$recipe->sub_unit->short_name}} @else {{$recipe->variation->product->unit->short_name}} @endif
      			</div>
      			<div class="col-md-6 text-right pr-4">
      				<!-- <strong>@lang('manufacturing::lang.extra_cost'):</strong>
      				<span ></span>{{@num_format($recipe->extra_cost)}}% <br> -->
      				@php
      					$final_price = $recipe->final_price;
      				@endphp
      				<strong>@lang('manufacturing::lang.final_total'):</strong>
      				<span class="display_currency" data-currency_symbol="true">{{$final_price}}</span>
      			</div>
      		</div>
      		<div class="row">
      			<div class="col-md-6 py-3">
      				<!-- <strong>@lang('manufacturing::lang.wastage'):</strong>
      				{{$recipe->waste_percent ?? 0}} % <br> -->
      				<strong>@lang('manufacturing::lang.byproducts_available'):</strong>
      				@if(!empty($recipe->by_product_available)){{ucfirst($recipe->by_product_available)}} @endif
      			</div>
				@if($recipe->by_product_available=='yes')
      			<div class="col-md-12">
				  <table class="table">
						<thead>
							<tr>
								<th>@lang('manufacturing::lang.by_products')</th>
								<th>@lang('lang_v1.quantity')</th>
							</tr>
						</thead>
						<tbody>
						@foreach($by_products as $prod)
						<tr>
							<td>{{$prod->name}}</td>
							<td>{{$prod->output_qty}} {{$prod->unit}}</td>
						</tr>
						@endforeach
						</tbody>
						
					</table>
				</div>
				@endif
      		</div>
      		<div class="row">
      			<div class="col-md-12">
					@if(!empty($recipe->instructions))
      				<strong>@lang('lang_v1.instructions'):</strong><br>
      				{!! $recipe->instructions !!}
					@endif
      			</div>
      		</div>
      	</div>
      	<div class="modal-footer">
      		<button type="button" class="btn btn-primary no-print" aria-label="Print" 
			      onclick="$(this).closest('div.modal-content').printThis();"><i class="fa fa-print"></i> @lang( 'messages.print' )
			</button>
	      	<button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	</div>
</div>
