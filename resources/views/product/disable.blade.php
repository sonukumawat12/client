<div class="modal-dialog modal-xl" role="document">
	<div class="modal-content">
	{!! Form::open(['url' => action([\App\Http\Controllers\ProductController::class, 'saveDisable']), 'method' => 'post', 'id' => 'disable_form' ]) !!}
	    <input type="hidden" name="product_id" value="{{$id}}">
	
		<div class="modal-header">
		    <button type="button" class="close no-print" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		      <h4 class="modal-title" id="modalTitle">@lang('product.disable')</h4>
	    </div>
	    <div class="modal-body">
	        <div class="card" style="padding: 10px">
	            <div class="row">
    			   @php
                        $chunks = array_chunk($disable_fields, 4);
                    @endphp
                    
                    @foreach ($chunks as $chunk)
                        <div class="row">
                            @foreach ($chunk as $one)
                                <div class="col-md-3">
                                    {!! Form::checkbox('disabled_in[]', $one, in_array($one,$product->disabled_in)) !!}
                                    {{ __('product.' . $one) }}
                                </div>
                            @endforeach
                        </div>
                        <hr>
                    @endforeach
    			</div>
	        </div>
    			
		</div>
		<div class="modal-footer">
			<button type="submit" class="btn btn-primary" >@lang('messages.save')</button>
		    <button type="button" class="btn btn-default no-print" data-dismiss="modal">@lang( 'messages.close' )</button>
		 </div>
	 {!! Form::close() !!}
	</div>
</div>
