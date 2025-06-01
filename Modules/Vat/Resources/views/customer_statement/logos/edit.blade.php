@php
$alignment = ['Left' => 'Left','Center' => 'Center', 'Right' => 'Right' ];
$text_position = ['below' => 'Below the signature line','above' => 'Above the signature line' ];

@endphp

<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatStatementLogoController@update', $driver->id), 'method' =>
    'put', 'id' => 'fleet_logos_edit_form','enctype' => 'multipart/form-data' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'lang_v1.statement_settings' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        <div class="form-group col-sm-12">
          {!! Form::label('image_name', __( 'fleet::lang.image_name' ) . ':*') !!}
          {!! Form::text('image_name', $driver->image_name, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.image_name')]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('alignment', __( 'fleet::lang.alignment' ) . ':*') !!}
          {!! Form::select('alignment', $alignment, $driver->alignment, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        
        <div class="form-group  col-sm-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('business_name', 1, $driver->business_name, ['class' => 'input-icheck']); !!}
                    {{__('lang_v1.business_name')}}
                </label>
            </div>
        </div>
        
        <div class="form-group  col-sm-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('business_address', 1, $driver->business_address, ['class' => 'input-icheck']); !!}
                    {{__('lang_v1.business_address')}}
                </label>
            </div>
        </div>
        
        <div class="form-group  col-sm-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('contact_no', 1, $driver->contact_no, ['class' => 'input-icheck']); !!}
                    {{__('lang_v1.contact_no')}}
                </label>
            </div>
        </div>
        
        <div class="form-group  col-sm-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('email', 1, $driver->email, ['class' => 'input-icheck']); !!}
                    {{__('lang_v1.email')}}
                </label>
            </div>
        </div>
        
        <div class="form-group  col-sm-6">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('mobile_no', 1, $driver->mobile_no, ['class' => 'input-icheck']); !!}
                    {{__('lang_v1.mobile_no')}}
                </label>
            </div>
        </div>
        
         <div class="form-group col-sm-8">
          {!! Form::label('statement_note', __( 'vat::lang.statement_note' ) . ':*') !!}
          {!! Form::text('statement_note', $driver->statement_note, ['class' => 'form-control', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        
        <div class="form-group col-sm-4">
          {!! Form::label('text_position', __( 'vat::lang.text_position' ) . ':*') !!}
          {!! Form::select('text_position', $text_position, $driver->text_position, ['class' => 'form-control select2', 'required' ,'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
            
        
        
        <div class="form-group col-sm-12">
          {!! Form::label('attachment', __( 'lang_v1.add_image' )) !!}
          {!! Form::file('attachment', ['accept' => 'image/*']) !!}
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
 
</script>