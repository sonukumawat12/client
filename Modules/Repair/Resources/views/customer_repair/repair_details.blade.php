@foreach($sells as $sell)
	<div class="col-md-12">
		<div class="box box-solid">
			<div class="box-header with-border">
				<h2 class="box-title">

				<strong>@lang('repair::lang.job_sheet_no'): </strong> {{ $sell->job_sheet_no }}
					
				</h2>
			</div>
			<div class="box-body" style="color: black;">
				<div class="row">
					<div class="col-md-6">
						<strong>@lang('product.brand'): </strong> {{$sell->manufacturer}}
					</div>
					<div class="col-md-6">
						<strong>@lang('repair::lang.device'): </strong> {{$sell->repair_device}}
					</div>
				</div>
				<div class="row mt-10">
					<div class="col-md-6">
						<strong>@lang('repair::lang.model'): </strong> {{$sell->repair_model}}
					</div>
					<div class="col-md-6">
						<strong>
							@lang('repair::lang.serial_no'):
						</strong> {{$sell->serial_no}}
					</div>
				</div>
				<div class="row mt-10">
					<div class="col-md-6">
						<strong>
							{{ __('repair::lang.current_repair_status') }}:
						</strong>
						<span class="badge" style="background-color: {{$sell->repair_status_color}};">
							{{$sell->repair_status}}
						</span>
					</div>
					<div class="col-md-6">
						<strong>
							{{ __('repair::lang.expected_delivery_date') }}:
						</strong>
						@if(!empty($sell->delivery_date))
							{{\Carbon::parse($sell->delivery_date)->toDayDateTimeString()}}
						@endif
					</div>
				</div>

			<div class="row mt-10">
				<div class="col-md-6">
					<strong>
						Product Condition:
					</strong>
					@if(!empty($sell->product_condition))
						@foreach(json_decode($sell->product_condition) as $one)
						    <span class="badge badge-secondary">
    							{{$one->value}}
    						</span>
						@endforeach
					@endif
					Condition
				</div>
				<div class="col-md-6">
					<strong>
						Product Configuration:
					</strong>
					@if(!empty($sell->product_configuration))
						@foreach(json_decode($sell->product_configuration) as $one)
						    <span class="badge badge-secondary">
    							{{$one->value}}
    						</span>
						@endforeach
					@endif
				</div>
			</div>

			<div class="row mt-10">
				<div class="col-md-6">
					<strong>
						Warranty:
					</strong>
					@if(!empty($sell->warranty_number))
						{{$sell->warranty_number}}
					@endif
				</div>
				<div class="col-md-6">
					<strong>
						Problem Reported by Customer:
					</strong>
					@if(!empty($sell->defects))
						@foreach(json_decode($sell->defects) as $one)
						    <span class="badge badge-secondary">
    							{{$one->value}}
    						</span>
						@endforeach
					@endif
				</div>
			</div>
			<div class="row mt-10">
				<div class="col-md-12 col-xs-12">
					<strong>
						<span>Estimated Cost:</span>
					</strong>
					{{$sell->estimated_cost}}
				</div>
			</div>
			
			<div class="row mt-10">
				<div class="col-md-12 col-xs-12">
					<strong>
						<span>Added parts:</span>
					</strong>
					@includeIf('repair::customer_repair.added_parts', ['parts' => $sell['added_parts']])
				</div>
			</div>
			<hr>
			<div class="row mt-10">
				<div class="col-md-12 col-xs-12">
					<strong>
						<span>{{ __('repair::lang.activities') }}:</span>
					</strong>
					@includeIf('repair::customer_repair.repair_activities', ['activities' => $sell['activities']])
				</div>
			</div>
			
			
		</div>
	</div>
@endforeach