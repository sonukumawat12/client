<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
   
      <style>
          /* Override Select2's width */
          .my-modal .select2-container--default .select2-selection--single {
              width: 100% !important;
          }
      </style>
    <div class="card">
        <div class="card-body">
            <div class="row">
            <div class="form-group col-sm-3">
                {!! Form::label('airticket_no', __( 'airline::lang.airticket_no' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airticket_no')}}">
                            <i class="fa fa-ticket"></i>
                        </button>
                    </div>
                        
                    {!! Form::text('airticket_no', $ticket->airticket_no, [
                        'class' => 'form-control mousetrap',
                        'id' =>'airticket_no',
                        'placeholder' => __('airline::lang.airticket_no_placeholder'),
                        'disabled']); !!}
                    
                </div>
            </div>
            <div class="form-group col-sm-3">
                {!! Form::label('customer_group', __( 'airline::lang.customer_group' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer_group')}}">
                            <i class="fa fa-users"></i>
                        </button>
                    </div>
                    {!! Form::text('customer_group', $ticket->customer_grp, ['class' => 'form-control', 'id' => 'customer_group_select', 'disabled',
                        'placeholder' => __('airline::lang.customer_group_placeholder')]); !!}
                </div>
            </div>
            <div class="form-group col-sm-3">
                {!! Form::label('customer', __( 'airline::lang.customer' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer')}}">
                            <i class="fa fa-user"></i>
                        </button>
                    </div>
                    {!! Form::text('customer', $ticket->customer_name, ['class' => 'form-control', 'id' => 'customer_select', 'disabled']); !!}
                    
                </div>
            </div>
            <div class="form-group col-sm-3">
                {!! Form::label('airline', __( 'airline::lang.airline' ) . ':*') !!}
                <div class="input-group">
                    <div class="input-group-btn">
                        <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                            <i class="fa fa-plane"></i>
                        </button>
                    </div>
                    {!! Form::text('airline',$ticket->airline_name, ['class' => 'form-control', 'id' => 'airline_select', 'disabled',
                        'placeholder' => __('airline::lang.airline_placeholder')]); !!}
                    
                </div>
                
                
            </div>
        </div>
    <div class="row">
        <div class="form-group col-sm-3">
            {!! Form::label('airline_invoice_no', __( 'airline::lang.airline_invoice_no' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline_invoice_no')}}">
                        <i class="fa fa-file-invoice"></i>
                    </button>
                </div>
                {!! Form::text('airline_invoice_no', $ticket->airline_invoice_no, [
                    'class' => 'form-control mousetrap',
                    'id' =>'airline_invoice_no',
                    'placeholder' => __('airline::lang.airline_invoice_no_placeholder'),
                    'disabled']); !!}
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('airline_agent', __( 'airline::lang.airline_agent' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-user-tie"></i>
                    </button>
                </div>
                {!! Form::text('airline_agent', $ticket->airline_agent_name, ['class' => 'form-control', 'id' => 'airline_agent_select', 'disabled',
                'placeholder' => __('airline::lang.airline_agent_placeholder')]); !!}
                
            </div>
            
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('travel_mode', __( 'airline::lang.travel_mode' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.travel_mode')}}">
                        <i class="fa fa-info"></i>
                    </button>
                </div>
                {!! Form::select('travel_mode', ['One Way' => 'One Way', 'Return' => 'Return'], $ticket->travel_mode, ['class' => 'form-control', 'id' => 'travel_mode_select', 'disabled']); !!}
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('departure_country', __( 'airline::lang.departure_country' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-globe"></i>
                    </button>
                </div>
                {!! Form::text('departure_country', $ticket->departure_country, ['class' => 'form-control', 'id' => 'departure_country_select', 'disabled',
                'placeholder' => __('airline::lang.departure_country_placeholder')]); !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-3">
            {!! Form::label('departure_airport', __( 'airline::lang.departure_airport' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-plane-departure"></i>
                    </button>
                </div>
                {!! Form::text('departure_airport', $ticket->departure_airport_name, ['class' => 'form-control', 'id' => 'departure_airport_select', 'disabled',
                'placeholder' => __('airline::lang.departure_airport_placeholder')]); !!}
               
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('departure_date', __( 'airline::lang.departure_date' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-calendar"></i>
                    </button>
                </div>
                {!! Form::text('departure_date', @format_date($ticket->departure_date), ['class' => 'form-control datepicker', 'id' => 'departure_date', 'disabled',
                'placeholder' => __('airline::lang.departure_date_placeholder')]); !!}
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('departure_time', __( 'airline::lang.departure_time' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-clock"></i>
                    </button>
                </div>
                {!! Form::input('time', 'departure_time', $ticket->departure_time, ['class' => 'form-control timepicker', 'id' => 'departure_time', 'disabled',
                'placeholder' => __('airline::lang.departure_time_placeholder')]); !!}
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('transit', __( 'airline::lang.transit' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-hand-pointer"></i>
                    </button>
                </div>
                {!! Form::select('transit', [1 => 'Yes', 0 => 'No'], $ticket->transit, ['class' => 'form-control', 'id' => 'transit_select', 'disabled']); !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-3">
            {!! Form::label('transit_airport', __( 'airline::lang.transit_airport' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-plane"></i>
                    </button>
                </div>
                {!! Form::text('transit_airport', $ticket->transit_airport_name, ['class' => 'form-control', 'id' => 'transit_airport_select', 'disabled',
                'placeholder' => __('airline::lang.transit_airport_placeholder')]); !!}
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('arrival_country', __( 'airline::lang.arrival_country' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-globe"></i>
                    </button>
                </div>
                {!! Form::text('arrival_country', $ticket->arrival_country, ['class' => 'form-control', 'id' => 'arrival_country_select', 'disabled',
                'placeholder' => __('airline::lang.arrival_country_placeholder')]); !!}
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('arrival_airport', __( 'airline::lang.arrival_airport' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-plane-arrival"></i>
                    </button>
                </div>
                {!! Form::text('arrival_airport', $ticket->arrival_airport_name, ['class' => 'form-control', 'id' => 'arrival_airport_select', 'disabled',
                'placeholder' => __('airline::lang.arrival_airport_placeholder')]); !!}
                
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('arrival_date', __( 'airline::lang.arrival_date' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-calendar"></i>
                    </button>
                </div>
                {!! Form::text('arrival_date',  @format_date($ticket->arrival_date), ['class' => 'form-control datepicker', 'id' => 'arrival_date', 'disabled',
                'placeholder' => __('airline::lang.arrival_date_placeholder')]); !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-3">
            {!! Form::label('arrival_time', __( 'airline::lang.arrival_time' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-clock"></i>
                    </button>
                </div>
                {!! Form::input('time', 'arrival_time', $ticket->arrival_time, ['class' => 'form-control timepicker', 'id' => 'arrival_time', 'disabled',
                'placeholder' => __('airline::lang.arrival_time_placeholder')]); !!}
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="passenger_table" style="width:100%!important">
                <thead>
                    <tr>
                        <th>{{__('airline::lang.passenger_name')}}</th>
                        <th>{{__('airline::lang.passport_no')}}</th>
                        <th>{{__('airline::lang.airticket_no')}}</th>
                        <th>{{__('airline::lang.frequent_flyer_no')}}</th>
                        <th>{{__('airline::lang.passenger_type')}}</th>
                        <th>{{__('airline::lang.price')}}</th>
                        <th>{{__('airline::lang.expiry_date')}}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($passengers as $one)
                        <tr>
                            <td>{{$one->first_name}}</td>
                            <td>{{$one->nic_number}}</td>
                            <td>{{$one->airticket_no}}</td>
                            <td>{{$one->frequent_flyer_no}}</td>
                            <td>{{$one->passenger_type}}</td>
                            <td>{{@num_format($one->price)}}</td>
                            <td>{{$one->expiry_date}}</td>
                        </tr>
                    
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                        <td colspan="4" id="total_price">{{@num_format($ticket->final_total)}}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    
    <div class="row">
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="payment_table" style="width:100%!important">
                    <thead>
                        <tr>
                            <th>{{__('lang_v1.payment_method')}}</th>
                            <th>{{__('sale.amount')}}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = 0; @endphp
                        @foreach($tps as $one)
                        @php $total += $one->amount;@endphp
                            <tr>
                                <td>{{$one->method}}</td>
                                <td>{{@num_format($one->amount)}}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td><strong>@lang('sale.total'):</strong>
                            </td>
                            <td id="payment_total_price">{{@num_format($total)}}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
        </div>
    </div>
    
    
    
    
    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->