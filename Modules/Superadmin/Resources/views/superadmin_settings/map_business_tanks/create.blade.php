<div class="modal-dialog" role="document" style="width: 55%">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Superadmin\Http\Controllers\MapBusinessTankController@store'), 'method'
        => 'post', 'id' => 'map_business_tank_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'superadmin::lang.map_business_tank' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('business_id', __( 'superadmin::lang.business' ) . ':*') !!}
                    {!! Form::select('business_id', $businesses,null, ['class' => 'form-control select2', 'required', 'placeholder'
                    => __(
                    'superadmin::lang.please_select' ) ]); !!}
                </div>
            </div>
            
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('sheet_id', __( 'superadmin::lang.sheet_name' ) . ':*') !!}
                    <select class="form-control select2" id="add_sheet_name" name="sheet_id" required>
                        <option value="">@lang('superadmin::lang.please_select')</option>
                        @foreach($sheet_names as $sheet_name)
                            <option value="{{$sheet_name->id}}" data-capacity="{{$sheet_name->tank_capacity}}"  data-manufacturer="{{$sheet_name->tank_manufacturer}}">{{$sheet_name->sheet_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('tank_manufacturer', __( 'superadmin::lang.tank_manufacturer' ) . ':*') !!}
                    {!! Form::text('tank_manufacturer', null, ['class' => 'form-control add_tank_manufacturer', 'disabled', 'placeholder' =>
                    __( 'superadmin::lang.tank_manufacturer' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('tank_capacity', __( 'superadmin::lang.tank_capacity' ) . ':*') !!}
                    {!! Form::text('tank_capacity', null, ['class' => 'form-control add_tank_capacity', 'disabled', 'placeholder' => __(
                    'superadmin::lang.tank_capacity' ) ]); !!}
                </div>
            </div>
            
        </div>

        <div class="clearfix"></div>
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