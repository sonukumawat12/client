<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('StoreController@storeUserPermission'), 'method' => 'post', 'id' => 'store_edit_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'store.add_store_permission' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-12">
                    {!! Form::label('store_id', __( 'store.store' ) . ':*') !!}
                    <select class="form-control select2" name="store_id" id="store_id" style="width: 100%" required>
                        @foreach($stores as $key => $value)
                        <option value="{{ $key }}" {{$key == $store->store_id ? "selected" : ""}}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group col-sm-12">
                    {!! Form::label('user_id', __( 'store.user' ) . ':*') !!}
                    <select class="form-control select2" name="user_id" id="user_id" style="width: 100%" required>
                        @foreach($users as $key => $value)
                        <option value="{{ $key }}" {{$key == $store->user_id ? "selected" : ""}}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                

                <div class="col-md-6">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('sell', 1, $store->sell, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'store.sell' ) }}
                      </label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('purchase', 1, $store->purchase, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'store.purchase' ) }}
                      </label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('stores_transfer', 1, $store->stores_transfer, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'store.stores_transfer' ) }}
                      </label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('stock_adjustment', 1, $store->stock_adjustment, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'store.stock_adjustment' ) }}
                      </label>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="checkbox">
                      <label>
                        {!! Form::checkbox('sell_return', 1, $store->sell_return, 
                        [ 'class' => 'input-icheck']); !!} {{ __( 'store.sell_return' ) }}
                      </label>
                    </div>
                </div>
                
                
                
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $(".select2").select2();
</script>