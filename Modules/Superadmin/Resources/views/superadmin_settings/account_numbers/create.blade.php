<div class="modal-dialog" role="document">
    <div class="modal-content">
  
      {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\AccountNumbersController@store'), 'method' => 'post', 'id' => 'account_numbers_form' ]) !!}
  
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">@lang( 'account.add_account' )</h4>
      </div>
  
      <div class="modal-body">
              <div class="form-group">
                  {!! Form::label('prefix', __( 'lang_v1.prefix' ) .":*") !!}
                  {!! Form::text('prefix', null, ['class' => 'form-control', 'placeholder' => __( 'lang_v1.prefix' ) ]); !!}
              </div>
  
              <div class="form-group">
                  {!! Form::label('account_number', __( 'account.account_number' ) .":*") !!}
                  {!! Form::text('account_number', null, ['class' => 'form-control', 'required','placeholder' => __( 'account.account_number' ) ]); !!}
              </div>
  
              <div class="form-group">
                  {!! Form::label('account_type', __( 'account.account_type' ) .":") !!}
                  <select name="account_type" class="form-control select2 account_type" id="account_type">\
                      <option>@lang('messages.please_select')</option>
                      @foreach($account_types as $account_type)
                          <optgroup label="{{$account_type->name}}">
                              <option value="{{$account_type->id}}">{{$account_type->name}}</option>
                              @foreach($account_type->sub_types as $sub_type)
                                  <option value="{{$sub_type->id}}">{{$sub_type->name}}</option>
                              @endforeach
                          </optgroup>
                      @endforeach
                  </select>
              </div>
              
      </div>
  
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
        <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
      </div>
  
      {!! Form::close() !!}
  
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
  <script >
      var asset_type_array = {{$asset_type_ids}};
  </script>