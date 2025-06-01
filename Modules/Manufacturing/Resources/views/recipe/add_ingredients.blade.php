@extends('layouts.app')
@section('title', __('manufacturing::lang.add_ingredients'))

@section('content')
<style>
	.p-0 {
		padding: 0px;
	}

	.px-2 {
		padding-top: 1rem;
		padding-bottom: 1rem;
	}
	.pr-3{
		padding-right:3rem;
	}
	.pl-10p{
		padding-left:10px;
	}
</style>
<!-- Content Header (Page header) -->
<section class="content-header">
	<h1>@lang('manufacturing::lang.add_ingredients')</h1>
</section>

<!-- Main content -->
<section class="content">
	{!! Form::open(['url' => action('\Modules\Manufacturing\Http\Controllers\RecipeController@store'), 'method' => 'post', 'id' => 'recipe_form' ]) !!}
	<div id="box_group">
		<div class="box box-solid">
			<div class="box-header">
				<h4 class="box-title"><strong>@lang('sale.product'): </strong>{{$variation->product_name}} @if($variation->product_type == 'variable') - {{$variation->product_variation_name}} - {{$variation->name}} @endif</h4>
			</div>
			<div class="box-body">
				<div class="row">
					<div class="col-md-12">
						<button type="button" class="btn btn-success pull-right" id="add_ingredient_group">@lang('manufacturing::lang.add_ingredient_group') @show_tooltip(__('manufacturing::lang.ingredient_group_tooltip'))</button>
					</div>
					<div class="col-md-10 col-md-offset-1">
						<div class="form-group">
							{!! Form::label('search_product', __('manufacturing::lang.select_ingredient').':') !!}

							{!! Form::text('search_product', null, ['class' => 'form-control', 'id' => 'search_product', 'placeholder' => __('manufacturing::lang.select_ingredient'), 'autofocus' => true ]); !!}

							{!! Form::hidden('variation_id', $variation->id); !!}
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-md-12">
						<table class="table table-striped table-th-green text-center ingredients_table">
							<thead>
								<tr>
									<th>@lang('manufacturing::lang.ingredient')</th>
									<th>@lang('manufacturing::lang.waste_percent')</th>
									<th>@lang('manufacturing::lang.final_quantity')</th>
									<th>@lang('lang_v1.price')</th>
									<th>&nbsp;</th>
								</tr>
							</thead>
							<tbody>
								@php
								$row_index = 0;
								$ingredient_groups = [];
								$ingredient_total = 0;
								@endphp
								@if(!empty($ingredients))
								@foreach($ingredients as $ingredient)
								@php
								$ingredient_obj = (object) $ingredient;
								$price = !empty($ingredient_obj->quantity) ? $ingredient_obj->quantity * $ingredient_obj->dpp_inc_tax : $ingredient_obj->dpp_inc_tax;
								$price = $price * $ingredient_obj->multiplier;
								$ingredient_total += $price;
								@endphp
								@if(empty($ingredient['mfg_ingredient_group_id']))
								@php
								$row_index = $loop->index;
								@endphp

								@include('manufacturing::recipe.ingredient_row', ['ingredient' => (object) $ingredient, 'ig_index' => ''])

								@php
								$row_index++;
								@endphp
								@else
								@php
								$ingredient_groups[$ingredient['mfg_ingredient_group_id']][] = $ingredient;
								@endphp
								@endif
								@endforeach
								@endif
							</tbody>
						</table>
					</div>

				</div>
			</div>
		</div> <!--box end-->
		@php
		$ig_index = 0;
		@endphp
		@foreach($ingredient_groups as $ingredient_group)
		@php
		$ig_name = !empty($ingredient_group[0]['ingredient_group_name']) ? $ingredient_group[0]['ingredient_group_name'] : '';
		$ig_description = !empty($ingredient_group[0]['ig_description']) ? $ingredient_group[0]['ig_description'] : '';
		@endphp
		@include('manufacturing::recipe.ingredient_group', ['ingredients' => $ingredient_group, 'ig_index' => $ig_index, 'ig_name' => $ig_name, 'ig_description' => $ig_description])
		@php
		$ig_index++;
		$row_index += count($ingredient_group);
		@endphp
		@endforeach
	</div>
	<div class="box box-solid">
		<div class="box-body">
			<div class="row">
				<input type="hidden" id="row_index" value="{{$row_index}}">
				<input type="hidden" id="ig_index" value="{{$ig_index}}">

				<div class="col-md-12 text-right">
					<strong>@lang('manufacturing::lang.ingredients_cost'): </strong> <span id="ingredients_cost_text">{{@num_format($ingredient_total)}}</span>
					<input type="hidden" name="ingredients_cost" id="ingredients_cost" value="{{$recipe->ingredients_cost ?? 0}}">
				</div>

				<div class="col-md-12">
					<div class="row">
						<div class="col-md-2">
							<span>{!! Form::label('', __('Type')) !!}</span>
						</div>
						<div class="col-md-2">
							<span>{!! Form::label('', __('Name')) !!}</span>
						</div>
						<div class="col-md-2">
							<span>{!! Form::label('', __('Fixed/Percentage')) !!}</span>
						</div>
						<div class="col-md-3">
							<span>{!! Form::label('', __('Value')) !!}</span>
						</div>
						<div class="col-md-3">
							<span>{!! Form::label('', __('Total')) !!}</span>
						</div>
					</div>

					@foreach($settings as $key=>$setting)
					<div class="col-md-12 px-2">
						<div class="row">
							<div class="col-md-2 p-0">
								@if($setting->type=='by_products')
								<span>By Products</span>
								@elseif($setting->type=='extracost')
								<span>Extra Cost</span>
								@else
								<span>{{ucfirst($setting->type)}}</span>
								@endif

							</div>
							<div class="col-md-2">
								<span>{{$setting->name}}</span>
							</div>
							<div class="col-md-2">
								<input type="hidden" name="setting[{{$setting->id}}][setting_id]" value="{{$setting->id}}">
								<select name="setting[{{$setting->id}}][cost_type]" class="form-control" id="cost_type_{{$setting->id}}" onchange="updateTotalRow({{$setting->id}})">
									<option @if($setting->cost_type=='fixed') selected @endif value="fixed">Fixed</option>
									<option @if($setting->cost_type=='percentage') selected @endif value="percentage">Percentage</option>
								</select>
							</div>
							<div class="col-md-3">
								{!! Form::number('setting['.$setting->id.'][row_value]',$setting->cost_value, ['class' => 'form-control', 'placeholder' => __('0'),'onchange'=>"updateTotalRow($setting->id)",'id'=>'row_value_'.$setting->id ]); !!}
							</div>
							<div class="col-md-3">

								<div class="input-group">
									{!! Form::text('setting['.$setting->id.'][row_total]',$setting->cost_total, ['class' => 'form-control row_total', 'placeholder' => __('0'),'value'=>'0','readonly'=>true,'id'=>'row_total_'.$setting->id ]); !!}
									<span class="input-group-addon">
										{{$currency_details->symbol}}
									</span>
								</div>
							</div>
						</div>
					</div>
					@endforeach
				</div>
					<div class="col-md-12 px-2">
					<div class="row">
						<div class="col-md-3">
						
						</div>
						<div class="col-md-3">
						
						</div>
						<div class="col-md-3">
							<div class="form-group">
								{!! Form::label('total_quantity', __('manufacturing::lang.total_output_quantity').':') !!}
								<div class="@if(!is_array($unit_html)) input-group @else input_inline @endif">
									{!! Form::text('total_quantity',!empty($recipe->total_quantity) ? @num_format($recipe->total_quantity) : 1, ['class' => 'form-control input_number', 'placeholder' => __('manufacturing::lang.total_output_quantity') ]); !!}
									<span class="@if(!is_array($unit_html)) input-group-addon @endif">
										@if(is_array($unit_html))
										<select name="sub_unit_id" class="form-control" id="sub_unit_id">
											@foreach($unit_html as $key => $value)
											<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" @if(!empty($recipe->sub_unit_id) && $recipe->sub_unit_id == $key)
												selected
												@endif
												>{{$value['name']}}</option>
											@endforeach
										</select>
										@else
										{{ $unit_html }}
										@endif
									</span>
								</div>
							</div>
						</div>

						<div class="col-md-3 pr-3 pl-10p">
							<div class="form-group">
								{!! Form::label('total', __('sale.total').':') !!}
								<div class="input-group">
									@php
									if(isset($recipe->final_price)){
									$final_price = $recipe->final_price;
									} else {
									$final_price=0;
									}
									@endphp
									{!! Form::text('total', @num_format($final_price), ['id' => 'total', 'class' => "form-control", 'readonly']); !!}
									<span class="input-group-addon">
										{{$currency_details->symbol}}
									</span>
								</div>
							</div>
						</div>

						<div class="col-md-3">
							<div class="form-group">
								<label for="by_product_available">{!! Form::label('by_product_available', 'By Products Available:') !!}</label>
								<select name="by_product_available" class="form-control" id="by_product_available" onchange="updateByProduct()">
									<option value="no" @if(isset($recipe->by_product_available)) @if($recipe->by_product_available=='no') selected @endif @endif>No</option>
									<option value="yes" @if(isset($recipe->by_product_available)) @if($recipe->by_product_available=='yes') selected @endif @endif >Yes</option>
								</select>
							</div>
						</div>
					</div>
					</div>

			</div>
			<div class="row" id="by_products_row" @if(isset($recipe->by_product_available)) @if($recipe->by_product_available=='no') style="display:none;" @endif @else style="display:none;" @endif>
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								{!! Form::label('', 'Product') !!}
							</div>
						</div>
						<!-- <div class="col-md-3">
							<div class="form-group">
								
							{!! Form::label('', 'Percentage') !!}
							</div>
						</div> -->
						<div class="col-md-3">
							<div class="form-group">

								{!! Form::label('', 'Output Qty') !!}
							</div>
						</div>

					</div>
				</div>


				@if(!empty($mfg_byproducts))
				@foreach($mfg_byproducts as $key=>$prod)
				@if($key==0)
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								{!! Form::select('product_list[]', [$prod->variation_id => $prod->name], $prod->variation_id, ['class' => 'form-control product_select', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
							</div>
						</div>
						<!-- <div class="col-md-3">
							<div class="form-group">
							{!! Form::text('by_product_percentage[]', 0, ['class' => "form-control"]); !!}
						</div>
						</div> -->
						<div class="col-md-3">
							<div class="form-group">
								<div class="@if(!is_array($unit_html)) input-group @else input_inline @endif">
									{!! Form::number('output_qty[]', $prod->output_qty, ['class' => "form-control"]); !!}
									<span class="@if(!is_array($unit_html)) input-group-addon @endif">
										@if(is_array($unit_html))
										<select name="by_product_sub_unit_id[]" class="form-control">
											@foreach($unit_html as $key => $value)
											<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" @if($prod->sub_unit_id == $key)
												selected
												@endif
												>{{$value['name']}}</option>
											@endforeach
										</select>
										@else
										{{ $unit_html }}
										@endif
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<button type="button" onclick="addByProductRow()" class="btn btn-primary" aria-label="Left Align">
								<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
						</div>
					</div>
				</div>
				@else

				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								{!! Form::select('product_list[]', [$prod->variation_id => $prod->name], $prod->variation_id, ['class' => 'form-control product_select', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
							</div>
						</div>
						<!-- <div class="col-md-3">
							<div class="form-group">
							{!! Form::text('by_product_percentage[]', 0, ['class' => "form-control"]); !!}
						</div>
						</div> -->
						<div class="col-md-3">
							<div class="form-group">
								<div class="@if(!is_array($unit_html)) input-group @else input_inline @endif">
									{!! Form::number('output_qty[]', $prod->output_qty, ['class' => "form-control"]); !!}
									<span class="@if(!is_array($unit_html)) input-group-addon @endif">
										@if(is_array($unit_html))
										<select name="by_product_sub_unit_id[]" class="form-control">
											@foreach($unit_html as $key => $value)
											<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" @if($prod->sub_unit_id == $key)
												selected
												@endif
												>{{$value['name']}}</option>
											@endforeach
										</select>
										@else
										{{ $unit_html }}
										@endif
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<button type="button" onclick="removeByProductRow(this)" class="btn btn-danger" aria-label="Left Align">
								<span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
							</button>
						</div>
					</div>
				</div>

				@endif
				@endforeach
				@else
				<div class="col-md-12">
					<div class="row">
						<div class="col-md-3">
							<div class="form-group">
								{!! Form::select('product_list[]', [], null, ['class' => 'form-control product_select', 'placeholder' => __('messages.please_select'), 'style' => 'width: 100%;']); !!}
							</div>
						</div>
						<!-- <div class="col-md-3">
							<div class="form-group">
							{!! Form::text('by_product_percentage[]', 0, ['class' => "form-control"]); !!}
						</div>
						</div> -->
						<div class="col-md-3">
							<div class="form-group">
								<div class="@if(!is_array($unit_html)) input-group @else input_inline @endif">
									{!! Form::number('output_qty[]', 0, ['class' => "form-control"]); !!}
									<span class="@if(!is_array($unit_html)) input-group-addon @endif">
										@if(is_array($unit_html))
										<select name="by_product_sub_unit_id[]" class="form-control">
											@foreach($unit_html as $key => $value)
											<option value="{{$key}}" data-multiplier="{{$value['multiplier']}}" @if(!empty($recipe->sub_unit_id) && $recipe->sub_unit_id == $key)
												selected
												@endif
												>{{$value['name']}}</option>
											@endforeach
										</select>
										@else
										{{ $unit_html }}
										@endif
									</span>
								</div>
							</div>
						</div>
						<div class="col-md-2">
							<button type="button" onclick="addByProductRow()" class="btn btn-primary" aria-label="Left Align">
								<span class="glyphicon glyphicon-plus" aria-hidden="true"></span>
							</button>
						</div>
					</div>
				</div>
				@endif
			</div>



			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						{!! Form::label('instructions', __('manufacturing::lang.recipe_instructions').':') !!}

						{!! Form::textarea('instructions',!empty($recipe) ? $recipe->instructions : null, ['class' => 'form-control', 'placeholder' => __('manufacturing::lang.recipe_instructions') ]); !!}
					</div>
				</div>
			</div>
			<br>
			<div class="row">
				<div class="col-sm-12">
					<button type="submit" class="btn btn-primary pull-right">@lang('messages.save')</button>
				</div>
			</div>
		</div>
	</div>
	{!! Form::close() !!}
</section>
@stop

@section('javascript')
@include('manufacturing::layouts.partials.common_script')

@endsection