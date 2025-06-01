@extends('layouts.app')
@section('title', 'Airline Ticketing')

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

<style>
    .select2{
        width: 100% !important;
    }
</style>

<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h5 class="page-title pull-left">Airline Ticketing</h5>
                
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content main-content-inner">

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range_filter', @format_date('first day of this month') . ' ~ ' .
                        @format_date('last
                        day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                        'form-control date_range', 'id' => 'date_range_filter', 'readonly']); !!}
                    </div>
                </div>
                
                <div class="form-group col-md-3">
                    {!! Form::label('customer_group', __( 'airline::lang.customer_group' ) . ':*') !!}
                    {!! Form::select('customer_group', $customer_groups, null, ['class' => 'form-control select2', 'style' => 'width: 100% !important;', 'id' => 'customer_group_select', 'required',
                            'placeholder' => __('airline::lang.customer_group_placeholder')]); !!}
                    
                </div>
                
                <div class="form-group col-sm-3">
                    {!! Form::label('customer', __( 'airline::lang.customer' ) . ':*') !!}
                    {!! Form::select('customer', [], null, ['class' => 'form-control select2', 'id' => 'customer_select', 'required']); !!}
                    
                </div>
                
                <div class="form-group col-sm-3">
                    {!! Form::label('airline_agent', __( 'airline::lang.airline_agent' ) . ':*') !!}
                    {!! Form::select('airline_agent', [], null, ['class' => 'form-control select2', 'id' => 'airline_agent_select', 'required',
                        'placeholder' => __('airline::lang.airline_agent_placeholder')]); !!}
                   
                    
                       
                </div>
                    
            
            
            </div>
            <div class="row">
            
            
            
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('payment_status', __( 'fleet::lang.payment_status' )) !!}
                        {!! Form::select('payment_status', $payment_status, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 'payment_status']);
                        !!}
                    </div>
                </div>
                
                
                <div class="form-group col-sm-3">
                    {!! Form::label('departure_country', __( 'airline::lang.departure_country' ) . ':*') !!}
                    {!! Form::select('departure_country', [], null, ['class' => 'form-control', 'id' => 'departure_country_select', 'required',
                        'placeholder' => __('airline::lang.departure_country_placeholder')]); !!}
                    
                </div>
                
                <div class="form-group col-sm-3">
                    {!! Form::label('arrival_country', __( 'airline::lang.arrival_country' ) . ':*') !!}
                    {!! Form::select('arrival_country', [], null, ['class' => 'form-control', 'id' => 'arrival_country_select', 'required',
                        'placeholder' => __('airline::lang.arrival_country_placeholder')]); !!}
                    
                </div>
                
                <div class="form-group col-sm-3">
                    {!! Form::label('transit', __( 'airline::lang.transit' ) . ':*') !!}
                    {!! Form::select('transit', [''=>'',1 => 'Yes', 0 => 'No'], null, ['class' => 'form-control select2', 'id' => 'transit_select', 'required']); !!}
                   
                </div>
            
            
            
                
            </div>
            <div class="row">
            
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'airline::lang.all_your_air_tickets')])
            @slot('tool')
            <div class="box-tools">
                <a class="btn btn-primary pull-right" id=""
                    href="{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@create_invoice')}}"
                   >
                    <i class="fa fa-plus"></i> @lang( 'fleet::lang.add' )</a>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="airline_tickets_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'messages.action' )</th>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>{{__( 'airline::lang.airline_invoice_no' )}}</th>
                                    <th>{{__( 'airline::lang.customer_group' )}}</th>
                                    <th>{{__( 'airline::lang.customer' ) }}</th>
                                    <th>{{__( 'airline::lang.airline' )}}</th>
                                    <th>{{__( 'airline::lang.airline_invoice_no' )}}</th>
                                    <th>{{__( 'airline::lang.airline_agent' )}}</th>
                                    <th>{{ __( 'airline::lang.departure_country' )}}</th>
                                    <th> {{__( 'airline::lang.arrival_country' )}} </th>
                                    <th>{{ __( 'airline::lang.transit' )}}</th>
                                    <th> {{__( 'airline::lang.passengers' )}}</th>
                                    <th> {{__( 'airline::lang.payment_status' )}}</th>
                                    <th> {{__( 'airline::lang.payment_mode' )}}</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                              
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    
    <div class="modal fade payment_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    
    <div class="modal fade fleet_model" role="dialog" aria-labelledby="gridSystemModalLabel"  id="transitModal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="service_form_title">Transit</h4>
                </div>
                <div class="modal-body">
                    <div class="row  form-section">
                        <div class="col-md-12">
                           
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
 

</section>
<!-- /.content -->
<style>
  .nav-tabs-custom>.nav-tabs>li.active a{
    color:#3c8dbc;
  }
  .nav-tabs-custom>.nav-tabs>li.active a:hover{
    color:#3c8dbc;
  }
</style>
@endsection

@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>

    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
            airline_tickets_table.ajax.reload();
            
        });
        $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#date_range_filter')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#date_range_filter')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    
    const createticket_module = {
        selected_modal: null,
        init: function() {

            this.init_airline_agents();
            this.init_countries();
            
            

            this.listener();
        },
        init_airline_agents: function() {
            $.ajax({
                url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@airline_agents')}}",
                method: 'GET',
                success: function(airlines) {
                    $('#airline_agent_select').empty();
                    var newOption = $('<option>').text('Select airline agent').val('');
                    $('#airline_agent_select').append(newOption);
                    
                    if (Object.keys(airlines).length > 0) {
                        Object.keys(airlines).forEach(id => {
                            var newOption = $('<option>').text(airlines[id]).val(parseInt(id));
                        $('#airline_agent_select').append(newOption);
                    });

                        // Initialize Select2 on the customer_select element
                        $('#airline_agent_select').select2({
                            minimumResultsForSearch: 0, // Set this to 0 to always display the search input

                            placeholder: 'Select airline agent', // Placeholder text
                            allowClear: true, // Allow clearing the selection
                            // Add any other options you need
                        });
                    }

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
                $('#departure_country_select').append($('<option>').text('Select departure country').val(''));
                $('#arrival_country_select').append($('<option>').text('Select Arrival country').val(''));
                $('#country_select').append($('<option>').text('Select Country').val(''));
                data.forEach(country => {
                    $('#departure_country_select').append($('<option>').text(country.name).val(country.name));
                    $('#arrival_country_select').append($('<option>').text(country.name).val(country.name));
                    $('#country_select').append($('<option>').text(country.name).val(country.name));
                });
            $('#departure_country_select').select2({
                minimumResultsForSearch: 0, // Set this to 0 to always display the search input
                placeholder: 'Select departure country', // Placeholder text
                allowClear: true, // Allow clearing the selection
                // Add any other options you need
            });
            $('#arrival_country_select').select2({
                minimumResultsForSearch: 0, // Set this to 0 to always display the search input
                placeholder: 'Select departure country', // Placeholder text
                allowClear: true, // Allow clearing the selection
                // Add any other options you need
            });
                if(data.length > 0) {
                    $('#departure_country_select').val('').trigger('change');
                    $('#arrival_country_select').val('').trigger('change');
                    $('#country_select').val('').trigger('change');
                }
            })
            .catch(error => {
                console.log(error);
            })
        },
        
        
        listener: function() {
        
            $('#customer_group_select').change(function() {
                airline_tickets_table.ajax.reload();
                
                $('#customer_select').html('');
                var customer_group_id = $(this).val();
                if(!isNaN(parseInt(customer_group_id))) {
                    $.ajax({
                        url: "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@customers_by_group_id')}}",
                        method: 'GET',
                        data: { customer_group_id },
                        success: function(customers) {
                            console.log(customers);
                            if (Object.keys(customers).length > 0) {
                                
                                /*
                                
                                Object.keys(customers).forEach(id => {
                                    var newOption = $('<option>').text(customers[id]).val(parseInt(id));
                                $('#customer_select').append(newOption);
                            });
                            */
                            
                         $.each(customers, function (key, value){
                             var newOption = $('<option>');
                             //console.log('Key:' + key + 'Value:' + value);
                             newOption.text(value);
                             newOption.val(parseInt(key));
                             $('#customer_select').append(newOption);
                         })
                            
                    
                                
                                
                            

                                // Initialize Select2 on the customer_select element
                                $('#customer_select').select2({
                                    placeholder: 'Select a customer', // Placeholder text
                                    allowClear: true, // Allow clearing the selection
                                    // Add any other options you need
                                });
                                
                                $('#customer_select').trigger('change');
                            }
                        },
                        error: function(error) {
                            console.log(error);
                        }
                    });
                } else {
                   $('#customer_select').empty();
                }
            });
            
           

        }
    }

    $(document).ready(() => {
        createticket_module.init();
        
        airline_tickets_table = $('#airline_tickets_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Airline\Http\Controllers\AirlineTicketingController@index')}}",
                    data: function(d){
                        
                        
                        
                        d.payment_status = $('#payment_status').val();
                        
                        d.start_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                            
                        d.customer_group = $("#customer_group_select").val();
                        d.customer = $("#customer_select").val();
                        d.airline_agent = $("#airline_agent_select").val();
                        d.departure_country = $("#departure_country_select").val();
                        d.arrival_country = $("#arrival_country_select").val();
                        d.transit = $("#transit_select").val();
                                                   
                        
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'action', name: 'action'},
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'airticket_no', name: 'airticket_no'},
                    {data: 'customer_grp', name: 'air_ticket_invoices.customer_group'},
                    {data: 'customer_name', name: 'air_ticket_invoices.customer'},
                    {data: 'airline_name', name: 'air_ticket_invoices.airline'},
                    {data: 'airline_invoice_no', name: 'airline_invoice_no'},
                    {data: 'airline_agent_name', name: 'air_ticket_invoices.airline_agent'},
                    {data: 'departure_country', name: 'departure_country'},
                    {data: 'arrival_country', name: 'arrival_country'},
                    {
                        data: 'transit', 
                        name: 'transit',
                        render: function (data, type, full, meta) {
                            return '<button class="label bg-light-green btn-view border-0" data-transaction-id="' + full.t_id + '">Note</button> ' + data;
                        },
                    },
                    {data: 'passengers', name: 'passengers'},
                    {data: 'payment_status', name: 'payment_status'},
                    {data: 'method', name: 'method'},
                  
                ],
                createdRow: function( row, data, dataIndex ) {
                }
            });
            
            $(document).on('click', 'a.delete-fleet', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            airline_tickets_table.ajax.reload();
                        },
                    });
                }
            });
        })
        
            $('#payment_status,#customer_select,#airline_agent_select,#departure_country_select,#arrival_country_select,#transit_select').change(function () {
                airline_tickets_table.ajax.reload();
            });
    });
    $(document).on('click', '.btn-view', function () {
    var transaction_id = $(this).data('transaction-id');
    // Make an Ajax request to fetch additional user information
    $.ajax({
        url: '/airline/ticketing/get-transit/' + transaction_id, // Replace with your actual endpoint
        method: 'GET',
        success: function (data) {
             updateTransitModal(data);
            
            $('#transitModal').modal('show');
        },
        error: function (error) {
            console.log(error);
        }
    });
    
    // Function to update the modal content with the received data
    
});
    function updateTransitModal(data) {
        var modalBody = $('#transitModal').find('.form-section .col-md-12');
        modalBody.empty(); 
          // Create a new div element
        var dynamicDiv = $('<div class="alert alert-info"></div>');
        var dynamicContent = data.note??'There is no note available for this transit';
    
        // Set the dynamic content in the div
        dynamicDiv.text(dynamicContent);
    
        // Append the div to the modal body
        $('#transitModal .form-section .col-md-12').append(dynamicDiv);

    }
</script>

@endsection