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
						<span class="label" style="background-color: {{$sell->repair_status_color}};">
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
						{{$sell->product_condition}}
					@endif
					Condition
				</div>
				<div class="col-md-6">
					<strong>
						Product Configuration:
					</strong>
					@if(!empty($sell->product_configuration))
						{{$sell->product_configuration}}
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
						{{$sell->defects}}
					@endif
				</div>
			</div>


			




				<div class="row mt-10">
					<div class="col-md-12 col-xs-12">
						<strong>
							<span>{{ __('repair::lang.activities') }}:</span>
						</strong>
						@includeIf('autorepairservices::customer_repair.repair_activities', ['activities' => $sell['activities']])
					</div>
				</div>
			</div>
		</div>
	</div>
@endforeach