
@php
$alignment = ['Left' => 'Left','Center' => 'Center', 'Right' => 'Right',];

@endphp


<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\FleetLogoController@store'), 'method' =>
    'post', 'id' => 'fleet_logos_add_form','enctype' => 'multipart/form-data' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">@lang( 'fleet::lang.fleet_logos' )</h4>
    </div>

    <div class="modal-body">
      <div class="row">
        
        <div class="form-group col-sm-12">
          {!! Form::label('image_name', __( 'fleet::lang.image_name' ) . ':*') !!}
          {!! Form::text('image_name', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.image_name')]); !!}
        </div>
        <div class="form-group col-sm-12">
          {!! Form::label('alignment', __( 'fleet::lang.alignment' ) . ':*') !!}
          {!! Form::select('alignment', $alignment, null, ['class' => 'form-control select2', 'placeholder' =>
          __('messages.please_select')]); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::label('attachment', __( 'fleet::lang.add_image' )) !!}
          {!! Form::file('attachment', ['accept' => 'image/*', 'required']) !!}

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