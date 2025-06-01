<div class="modal-dialog" role="document">
    <div class="modal-content">

        {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\FuelController@addNewPrice', ['id'=>$fuel->id]), 'method' =>
        'put', 'id' => 'fleet_fuel_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'fleet::lang.new_price' )</h4>
        </div>

        <div class="modal-body">
            <div class="row">
                <div class="form-group col-sm-12">
                    {!! Form::label('user', __( 'fleet::lang.user_added' ) . ':*') !!}
                    {!! Form::text('user', $currentUser, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.user_added'), 'id'
                    => 'user','disabled' => 'disabled']); !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('date', __( 'fleet::lang.date' ) . ':*') !!}
                    {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'placeholder' => __(
                    'fleet::lang.date' ),'disabled' => 'disabled']); !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('price_per_litre', __( 'fleet::lang.price_per_litre' ) . ':*') !!}
                    {!! Form::text('price_per_litre', $fuel->price_per_litre, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.price_per_litre'), 'id'
                    => 'price_per_litre','disabled' => 'disabled']); !!}
                </div>
                <div class="form-group col-sm-12">
                    {!! Form::label('price_per_litre_new', __( 'fleet::lang.new_price' ) . ':*') !!}
                    {!! Form::text('price_per_litre_new', $fuel->price_per_litre, ['class' => 'form-control', 'placeholder' => __( 'fleet::lang.new_price'), 'id'
                    => 'price_per_litre_new']); !!}
                </div>
            </div>

        </div>

        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.add' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        <hr>
        <div class="table-responsive">
            <table class="table table-bordered table-striped"  style="width: 100%;">
                <thead>
                <tr>
                    <th>@lang('fleet::lang.date')</th>
                    <th>@lang('fleet::lang.fuel_type')</th>
                    <th>@lang('fleet::lang.new_price')</th>
                    <th>@lang('fleet::lang.current_price')</th>
                    <th>@lang('fleet::lang.user_added')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($previousFuels as $fuel)
                    <tr>
                        <td>{{@format_date($fuel->date)}}</td>
                        <td>{{$fuel->type}}</td>
                        <td>{{$fuel->new_price}}</td>
                        <td>{{ $fuel->price_per_litre }}</td>
                        <td>{{$fuel->first_name ?? ''}}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#date').datepicker('setDate', '{{@format_date($fuel->date)}}');
</script>