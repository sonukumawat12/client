<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Bakery\Http\Controllers\RouteController@store'), 'method' =>
        'post', 'id' => 'route_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'fleet::lang.route' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-12">
                    {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
                    {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
                    'fleet::lang.date' )]); !!}
                </div>

                <div class="form-group col-sm-12" id="routeContainer">
                    <div class="inline-flex mr-1">
                        {!! Form::label('route', __( 'fleet::lang.route' ) . ':*') !!}
                        {!! Form::text('route[]', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.route'), 'id'
                        => 'route']); !!}
                        <button type="button" class="btn btn-primary " id="addMoreRoute">Add More</button>
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
    $('#joined_date').datepicker('setDate', new Date());

    $(document).ready(function () {
        $('#addMoreRoute').click(function () {
            var newRouteField = `
         <div class="form-group col-sm-12">
               {!! Form::text('route[]', null, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.route')]); !!}
            <button type="button" class="btn btn-danger removeRoute">Remove</button>
         </div>`;
          $('#routeContainer').append(newRouteField);
            updateRemoveButtons();
        });
    });

    function updateRemoveButtons() {
        if ($('.removeRoute').length === 0) {
            $('.removeRoute').hide();
        } else {
            $('.removeRoute').show();
        }
    }

    $('#routeContainer').on('click', '.removeRoute', function(){
        $(this).parent().remove();
        updateRemoveButtons();
    });
    updateRemoveButtons();


</script>