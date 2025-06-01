@extends('layouts.app')
@section('title', 'Create Ticket')

@section('css')
<style>
    table > tbody > tr > td{
        vertical-align: middle;
    }
</style>
@endsection

@section('content')

@php
$business_id = request()->session()->get('user.business_id');
$type = !empty($type) ? $type : 'customer';
$customer_groups = App\ContactGroup::where('business_id',$business_id)
                        ->where(function ($query) use ($type) {
                            $query->where('contact_groups.type', 'customer')
                                ->orWhere('contact_groups.type', 'both');
                        })->pluck('name','id');
@endphp
<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h5 class="page-title pull-left">Create Ticket</h5>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content main-content-inner">
    <div class="row">
        <div class="form-group col-sm-4">
            {!! Form::label('airticket_no', __( 'airline::lang.airticket_no' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airticket_no')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::text('airticket_no', null, [
                    'class' => 'form-control mousetrap', 
                    'id' =>'airticket_no_text', 
                    'placeholder' => __('airline::lang.airticket_no_placeholder'),
                    'disabled' => false]); !!}
            </div>
        </div>
        <div class="form-group col-sm-4">
            {!! Form::label('customer_group', __( 'airline::lang.customer_group' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer_group')}}">
                        <i class="fa fa-users"></i>
                    </button>
                </div>
                {!! Form::select('customer_group', $customer_groups, null, ['class' => 'form-control', 'id' => 'customer_group_select', 'required',
                    'placeholder' => __('airline::lang.customer_group_placeholder')]); !!}
            </div>
        </div>
        <div class="form-group col-sm-4">
            {!! Form::label('customer', __( 'airline::lang.customer' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('customer', [], null, ['class' => 'form-control', 'id' => 'customer_select', 'required']); !!}
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat btn-modal" 
                            data-href="/contacts/create_customer" data-container=".contact_modal">
                        <i class="fa fa-plus-circle text-primary fa-lg"></i>
                    </button>
                </span>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-4">
            {!! Form::label('airline', __( 'airline::lang.airline' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('airline', [], null, ['class' => 'form-control', 'id' => 'airline_select', 'required',
                    'placeholder' => __('airline::lang.airline_placeholder')]); !!}
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" id="create_airline">
                        <i class="fa fa-plus-circle text-primary fa-lg"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="form-group col-sm-4">
            {!! Form::label('airline_invoice_no', __( 'airline::lang.airline_invoice_no' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline_invoice_no')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::text('airline_invoice_no', null, [
                    'class' => 'form-control mousetrap', 
                    'id' =>'airline_invoice_no_text', 
                    'placeholder' => __('airline::lang.airline_invoice_no_placeholder'),
                    'disabled' => false]); !!}
            </div>
        </div>
        
    </div>
    <div class="row">
        <div class="form-group col-sm-4">
            {!! Form::label('airline_agent', __( 'airline::lang.airline_agent' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('airline_agent', [], null, ['class' => 'form-control', 'id' => 'airline_agent_select', 'required',
                'placeholder' => __('airline::lang.airline_agent_placeholder')]); !!}
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" id="create_airline_agent">
                        <i class="fa fa-plus-circle text-primary fa-lg"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="form-group col-sm-4">
            {!! Form::label('travel_mode', __( 'airline::lang.travel_mode' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.travel_mode')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('travel_mode', ['One Way' => 'One Way', 'Return' => 'Return'], null, ['class' => 'form-control', 'id' => 'travel_mode_select', 'required']); !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-3">
            {!! Form::label('departure_country', __( 'airline::lang.departure_country' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('departure_country', [], null, ['class' => 'form-control', 'id' => 'departure_country_select', 'required',
                'placeholder' => __('airline::lang.departure_country_placeholder')]); !!}
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('departure_airport', __( 'airline::lang.departure_airport' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('departure_airport', [], null, ['class' => 'form-control', 'id' => 'departure_airport_select', 'required',
                'placeholder' => __('airline::lang.departure_airport_placeholder')]); !!}
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" id="create_departure_airport">
                        <i class="fa fa-plus-circle text-primary fa-lg"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="form-group col-sm-2">
            {!! Form::label('departure_date', __( 'airline::lang.departure_date' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::text('departure_date', @format_date(date('Y-m-d')), ['class' => 'form-control datepicker', 'id' => 'departure_date', 'required',
                'placeholder' => __('airline::lang.departure_date_placeholder')]); !!}
            </div>
        </div>
        <div class="form-group col-sm-2">
            {!! Form::label('departure_time', __( 'airline::lang.departure_time' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::input('time', 'departure_time', '12:00', ['class' => 'form-control timepicker', 'id' => 'departure_time', 'required',
                'placeholder' => __('airline::lang.departure_time_placeholder')]); !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-3">
            {!! Form::label('transit', __( 'airline::lang.transit' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('transit', [1 => 'Yes', 0 => 'No'], null, ['class' => 'form-control', 'id' => 'transit_select', 'required']); !!}
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('transit_airport', __( 'airline::lang.transit_airport' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('transit_airport', [], null, ['class' => 'form-control', 'id' => 'transit_airport_select', 'required',
                'placeholder' => __('airline::lang.transit_airport_placeholder')]); !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="form-group col-sm-3">
            {!! Form::label('arrival_country', __( 'airline::lang.arrival_country' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('arrival_country', [], null, ['class' => 'form-control', 'id' => 'arrival_country_select', 'required',
                'placeholder' => __('airline::lang.arrival_country_placeholder')]); !!}
            </div>
        </div>
        <div class="form-group col-sm-3">
            {!! Form::label('arrival_airport', __( 'airline::lang.arrival_airport' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::select('arrival_airport', [], null, ['class' => 'form-control', 'id' => 'arrival_airport_select', 'required',
                'placeholder' => __('airline::lang.arrival_airport_placeholder')]); !!}
                <span class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" id="create_arrival_airport">
                        <i class="fa fa-plus-circle text-primary fa-lg"></i>
                    </button>
                </span>
            </div>
        </div>
        <div class="form-group col-sm-2">
            {!! Form::label('arrival_date', __( 'airline::lang.arrival_date' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::text('arrival_date',  @format_date(date('Y-m-d')), ['class' => 'form-control datepicker', 'id' => 'arrival_date', 'required',
                'placeholder' => __('airline::lang.arrival_date_placeholder')]); !!}
            </div>
        </div>
        <div class="form-group col-sm-2">
            {!! Form::label('arrival_time', __( 'airline::lang.arrival_time' ) . ':*') !!}
            <div class="input-group">
                <div class="input-group-btn">
                    <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.airline')}}">
                        <i class="fa fa-barcode"></i>
                    </button>
                </div>
                {!! Form::input('time', 'arrival_time', '12:00', ['class' => 'form-control timepicker', 'id' => 'arrival_time', 'required',
                'placeholder' => __('airline::lang.arrival_time_placeholder')]); !!}
            </div>
        </div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
        <button type="button" class="btn btn-primary btn-modal pull-right" id="create_passenger">
            <i class="fa fa-plus"></i>
            Add Passenger
        </button>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="passenger_table" style="width:100%!important">
                <thead>
                    <tr>
                        <th width="13%">Passport No</th>
                        <th width="13%">Passport Number</th>
                        <th width="10%">Image</th>
                        <th width="12%">Airline Itinerary</th>
                        <th width="12%">Ariticket No</th>
                        <th width="15%">Frequent Flyer No</th>
                        <th width="5%">Child</th>
                        <th width="10%">Price</th>
                        <th width="10%">Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
                <!-- <tfoot>
                    <tr class="bg-gray font-17 footer-total text-center">
                        <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                    </tr>
                </tfoot> -->
            </table>
        </div>
    </div>
    <div class="row" style="margin-bottom: 10px;">
        <button type="button" class="btn btn-primary btn-modal pull-right" id="save">
            <i class="fa fa-save"></i>
            Save
        </button>
    </div>
</section>
<div class="modal fade" id="airport_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="airport_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title" id="airport_title">Create Airport</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-group col-sm-12">
                            {!! Form::label('date_added', __( 'airline::lang.date_added' ) . ':*') !!}
                            {!! Form::text('date_added', @format_date(date('Y-m-d')), ['class' => 'form-control','required', 
                            'placeholder' => __('airline::lang.date_added' )]); !!}
                        </div>
                        <div class="form-group col-sm-12">
                            {!! Form::label('country_select', __( 'airline::lang.country_select' ) .":*") !!}
                            {!! Form::select('country_select', [], null, ['class' => 'form-control', 'id' => 'country_select', 'required', 'disabled',
                            'placeholder' =>__('messages.please_select')]); !!}
                        </div>

                        <div class="form-group col-sm-12">
                            {!! Form::label('province_select', __( 'airline::lang.province_select' ) .":*") !!}
                            {!! Form::text('province_select', null, ['class' => 'form-control', 'id' => 'province_select', 'required',
                            'placeholder' => __('airline::lang.province') ]); !!}
                        </div>
                        <div class="form-group col-sm-12">
                            {!! Form::label('airport_name', __( 'airline::lang.airport_name' ) . ':*') !!}
                            {!! Form::text('airport_name', null, ['class' => 'form-control', 'id' => 'airport_name', 'required',
                            'placeholder' => __('airline::lang.airport_name'), ]); !!}
                        </div>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="airline_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="airline_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="airline_form_title">Create Airline</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="airline" class="col-md-4 col-form-label text-md-right">Airline:*</label>
                        <div class="col-md-6">
                            <input type="text" class="form-control" name="airline" id="airline" autocomplete="name" autofocus>
                            <span class="invalid-feedback" role="alert">Min 3 characters</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="agent_form_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog" role="document">
        <form id="agent_form">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="agent_form_title">Create Airline Agent</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group row">
                        <label for="agent" class="col-md-4 col-form-label text-md-right">Agent:*</label>

                        <div class="col-md-6">
                            <input type="text" class="form-control" id="agent" name="agent" autocomplete="name" autofocus>
                            <span class="invalid-feedback" role="alert">Min 3 characters</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
</div>
@endsection

@section('javascript')
<script>

    const createticket_module = {
        selected_modal: null,
        init: function() {
            
            this.init_airlines();
            this.init_airline_agents();
            this.init_countries();
            this.init_airports(null, $('#transit_airport_select'));
            
            $('#departure_date').datepicker('setDate', new Date());
            $('#arrival_date').datepicker('setDate', new Date());

            this.listener();
        },
        init_airlines: function() {
            $.ajax({
                url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@airlines')}}",
                method: 'GET',
                success: function(airlines) {
                    $('#airline_select').empty();
                    Object.keys(airlines).forEach(id => {
                        var newOption = $('<option>').text(airlines[id]).val(parseInt(id));
                        $('#airline_select').append(newOption);
                    })
                },
                error: function(error) {
                    console.log(error);
                }
            });
        },
        init_airline_agents: function() {
            $.ajax({
                url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@airline_agents')}}",
                method: 'GET',
                success: function(airlines) {
                    $('#airline_agent_select').empty();
                    Object.keys(airlines).forEach(id => {
                        var newOption = $('<option>').text(airlines[id]).val(parseInt(id));
                        $('#airline_agent_select').append(newOption);
                    })
                },
                error: function(error) {
                    console.log(error);
                }
            });
        },
        init_countries: function() {
            fetch("https://restcountries.com/v2/all")
            .then(response => response.json())
            .then(data => {
                $('#departure_country_select').empty();
                $('#arrival_country_select').empty();
                $('#country_select').empty();
                data.forEach(country => {
                    $('#departure_country_select').append($('<option>').text(country.name).val(country.name));
                    $('#arrival_country_select').append($('<option>').text(country.name).val(country.name));
                    $('#country_select').append($('<option>').text(country.name).val(country.name));
                });
                if(data.length > 0) {
                    $('#departure_country_select').val(data[0].name).trigger('change');
                    $('#arrival_country_select').val(data[0].name).trigger('change');
                    $('#country_select').val(data[0].name).trigger('change');
                }
            })
            .catch(error => {
                console.log(error);
            })
        },
        init_airports: function(country, component) {
            $.ajax({
                url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@airline_airports')}}",
                method: 'GET',
                data: { country },
                success: function(airports) {
                    $(component).empty();
                    Object.keys(airports).forEach(id => {
                        var newOption = $('<option>').text(airports[id]).val(parseInt(id));
                        $(component).append(newOption);
                    })
                },
                error: function(error) {
                    console.log(error);
                }
            });
        },
        listener: function() {
            $('#customer_group_select').change(function() {
                var customer_group_id = $(this).val();
                if(!isNaN(parseInt(customer_group_id))) {
                    $.ajax({
                        url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@customers_by_group_id')}}",
                        method: 'GET',
                        data: { customer_group_id },
                        success: function(customers) {
                            Object.keys(customers).forEach(id => {
                                var newOption = $('<option>').text(customers[id]).val(parseInt(id));
                                $('#customer_select').append(newOption);
                            })
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                } else {
                   $('#customer_select').empty();
                }
            });

            $('#departure_country_select').change(function() {
                var country = $(this).val();
                createticket_module.init_airports(country, $('#departure_airport_select'));
            });

            $('#arrival_country_select').change(function() {
                var country = $(this).val();
                createticket_module.init_airports(country, $('#arrival_airport_select'));
            });

            $('#transit_select').change(function() {
                var is_transit = $(this).val();
                if(parseInt(is_transit)) {
                    document.getElementById('transit_airport_select').disabled = false;
                }
                else {
                    document.getElementById('transit_airport_select').disabled = true;
                }
            });

            $('#create_departure_airport').click(function() {
                $('#date_added').datepicker('setDate', new Date());
                $('#country_select').val($('#departure_country_select').val()).trigger('change');
                $('#province_select').val('');
                $('#airport_name').val('');
                createticket_module.selected_modal = $('#departure_airport_select');
                $('#airport_form_modal').modal('show');
            });
            
            $('#create_arrival_airport').click(function() {
                $('#date_added').datepicker('setDate', new Date());
                $('#country_select').val($('#arrival_country_select').val()).trigger('change');
                $('#province_select').val('');
                $('#airport_name').val('');
                createticket_module.selected_modal = $('#arrival_airport_select');
                $('#airport_form_modal').modal('show');
            });

            $('#airport_form').submit((e) => {
                e.preventDefault();
                
                const data = {};
                const inputs = $('#airport_forcm .form-ontrol');
                for (let i = 0; i < inputs.length; i++) {
                    const value = $(inputs[i]).val();
                    if (value) {
                        data[inputs[i].name] = value;
                    }
                }

                $.ajax({
                    url: '/airline/add_airport',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: data,
                    success: function(data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#airport_form_modal').modal('hide');
                            $(createticket_module.selected_modal).append($('<option>').text(data.data.airport_name).val(data.data.id))
                            $('#transit_airport_select').append($('<option>').text(data.data.airport_name).val(data.data.id))
                            $(createticket_module.selected_modal).val(data.data.id).trigger('change');
                        }
                    },
                    error: function(data, b, c) {
                        toastr.success(data.message);
                    }
                });
            });

            $('#create_airline').click(function() {
                $('#airline').val('');
                createticket_module.selected_modal = $('#airline_select');
                $('#airline_form_modal').modal('show');
            });

            $('#airline_form').submit((e) => {
                e.preventDefault();

                $.ajax({
                    url: '/airline/add_airline',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        airline: $('#airline').val()
                    },
                    success: function (data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#airline_form_modal').modal('hide');
                            $(createticket_module.selected_modal).append($('<option>').text(data.data.airline).val(data.data.id))
                            $(createticket_module.selected_modal).val(data.data.id).trigger('change');
                        }
                    },
                    error: function(data, b, c) {
                    }
                });
            })

            $('#create_airline_agent').click(function() {
                $('#agent').val('');
                createticket_module.selected_modal = $('#airline_agent_select');
                $('#agent_form_modal').modal('show');
            });

            $('#agent_form').submit((e) => {
                e.preventDefault();

                $.ajax({
                    url: '/airline/add_agent',
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    },
                    data: {
                        agent: $('#agent').val()
                    },
                    success: function (data, b, c) {
                        if (data.statusText == 'Success') {
                            toastr.success(data.message);
                            $('#agent_form_modal').modal('hide');
                            $(createticket_module.selected_modal).append($('<option>').text(data.data.agent).val(data.data.id))
                            $(createticket_module.selected_modal).val(data.data.id).trigger('change');
                        }
                    },
                    error: function(data, b, c) {
                    }
                });
            })

            

        }
    }

    $(document).ready(() => {
        createticket_module.init();
    });

    
</script>
@endsection