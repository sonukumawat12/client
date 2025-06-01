<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">{{$product->name}}</h4>
	    </div>
	    <div class="modal-body">
      		<div class="row">
      			<div class="col-sm-9">
	      			<div class="col-sm-4 invoice-col">
	      				<b>@lang('product.sku'):</b>
						{{$product->sku }}<br>
						
						<b>@lang('product.unit'): </b>
						{{$product->unit->short_name ?? '--' }}<br>
						
						
						@if(!empty($product->media->first())) <br>
							<strong>@lang('lang_v1.product_brochure'):</strong>
							<a href="{{$product->media->first()->display_url}}" download="{{$product->media->first()->display_name}}">
								<span class="label label-info">
									<i class="fas fa-download"></i>
									{{$product->media->first()->display_name}}
								</span>
							</a>
						@endif
	      			</div>

	      			<div class="col-sm-4 invoice-col">

						@if(!empty($product->warranty))
							<br>
							<b>@lang('lang_v1.warranty'): </b>
							{{$product->warranty->display_name }}
						@endif
	      			</div>
					
	      			<div class="col-sm-4 invoice-col">
	      				
						<b>@lang('product.applicable_tax'): </b>
						{{$product->product_tax->name ?? __('lang_v1.none') }}<br>
						@php
							$tax_type = ['inclusive' => __('product.inclusive'), 'exclusive' => __('product.exclusive')];
						@endphp
						<b>@lang('product.selling_price_tax_type'): </b>
						{{$tax_type[$product->tax_type]  }}<br>
						<b>@lang('product.product_type'): </b>
						@lang('lang_v1.' . $product->type)
						
	      			</div>
	      			<div class="clearfix"></div>
	      			<br>
      				<div class="col-sm-12">
      					{!! $product->product_description !!}
      				</div>
	      		</div>
      			<div class="col-sm-3 col-md-3 invoice-col">
      				<div class="thumbnail">
      					<img src="{{$product->image_url}}" alt="Product image">
      				</div>
      			</div>
      		</div>
      		
      		@if($product->type == 'single')
      			@include('product.partials.single_product_details')
      		@endif
      		
      	</div>
      	<div class="modal-footer">
      		<button type="button" class="btn btn-primary no-print" 
	        aria-label="Print" 
	          onclick="$(this).closest('div.modal').printThis();">
	        <i class="fa fa-print"></i> @lang( 'messages.print' )
	      </button>
	      	<button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
	    </div>
	</div>
</div>
