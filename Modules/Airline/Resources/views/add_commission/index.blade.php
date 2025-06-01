@extends('layouts.app')
@section('title', 'List Commission')

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
                <h5 class="page-title pull-left">List Commission</h5>
                
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
                        <!-- D 81 Added some code here-->
                            {!! Form::label('date_range_filter', __('report.date_range') . ':') !!}
                            {!! Form::text('date_range_filter',  @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month')  , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                        </div>
                </div>
                
                <div class="form-group col-md-3">
                    {!! Form::label('location', __( 'airline::lang.location' ) . ':*') !!}
                    {!! Form::select('location', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width: 100% !important;', 'id' => 'location', 'required',
                            'placeholder' => __('airline::lang.customer_group_placeholder')]); !!}
                    
                </div>
                
                
                
                <div class="form-group col-sm-3">
                    {!! Form::label('airline_agent', __( 'airline::lang.airline_agent' ) . ':*') !!}
                    {!! Form::select('airline_agent', $agent, null, ['class' => 'form-control select2', 'id' => 'airline_agent_select', 'required',
                        'placeholder' => __('airline::lang.airline_agent_placeholder')]); !!}
                   
                    
                       
                </div>
                    
            
            
            </div>
            <div class="row">
            
            
            
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('invoice_no', __( 'fleet::lang.invoice_no' )) !!}
                        {!! Form::select('invoice_no', $invoice_no, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 'invoice_no']);
                        !!}
                    </div>
                </div>
                
                
                <div class="form-group col-sm-3">
                    {!! Form::label('prt_ticket_no', __( 'airline::lang.prt_ticket_no' ) . ':*') !!}
                    {!! Form::select('prt_ticket_no', $ticket_no, null, ['class' => 'form-control', 'id' => 'prt_ticket_no', 'required',
                        'placeholder' => __('airline::lang.prt_ticket_no')]); !!}
                    
                </div>
                
                <div class="form-group col-sm-3">
                    {!! Form::label('air_ticket_date', __( 'airline::lang.air_ticket_date' ) . ':*') !!}
                    {!! Form::date('air_ticket_date', null, [
                    'class' => 'form-control mousetrap',
                    'id' => 'air_ticket_date',
                    'placeholder' => __('airline::lang.expiry_date_placeholder'), 
                    'required',
                ]) !!}
                    
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
            'All Your List Commission')])
            @slot('tool')
            
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        
                        <table class="table table-striped table-bordered" id="airline_tickets_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'fleet::lang.action' )</th>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>{{__( 'airline::lang.location' )}}</th>
                                    <th>{{__( 'airline::lang.airline_agent' )}}</th>
                                    <th>{{__( 'airline::lang.airline_invoice_no' ) }}</th>
                                    <th>{{__( 'airline::lang.prt_ticket_no' )}}</th>
                                    <th>{{__( 'airline::lang.air_ticket_date' )}}</th>
                                      <th>{{__( 'airline::lang.amount' )}}</th>
                                   
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($add_commission as $commission)
                                <tr>
                                     <td><div class="btn-group">
                    <button type="button" class="btn btn-info dropdown-toggle btn-xs" 
                        data-toggle="dropdown" aria-expanded="false">
                            actions
                            <span class="caret"></span><span class="sr-only">Toggle Dropdown
                        </span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-left" role="menu">
                         
                      <li><a href="{{ action('\Modules\Airline\Http\Controllers\AirlineTicketingController@get_airline_commission_print', ['ticket_no' => $commission->ticket_no]) }}"><i class="glyphicon glyphicon-print"></i>Print</a></li>
                        
                        </ul>
                 </td>
                                    <td>{{ $commission->date }}</td>
                                    <td>{{ $commission->location }}</td>
                                    <td>{{ $commission->airline_agent }}</td>
                                    <td>{{ $commission->invoice_no }}</td>
                                    <td>{{ $commission->ticket_no }}</td>
                                    <td>{{ $commission->air_ticket_date }}</td>
                                      <td>{{ $commission->commision_amount }}</td> 
                                </tr>
                                @endforeach 
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
 

<div class="modal fade commission_modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
  
<script>
$(document).ready(function() {
  $('#airline_tickets_table').DataTable({
    "paging": true,
    "lengthChange": false,
    "searching": true,
    "ordering": true,
    "info": true,
    "autoWidth": false,
    "dom": 'Bfrtip',
    "buttons": [
               {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'colvis',
                text: '<i class="fa fa-columns"></i> Column Visibility',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
              {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            }
        ]
   
  });
});

$(document).ready(function() {
/* $('#airline_tickets_table').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "dom": 'lBfrtip',
        "buttons": [
               {
                extend: 'excel',
                text: '<i class="fa fa-file-excel-o"></i> Export to Excel',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'colvis',
                text: '<i class="fa fa-columns"></i> Column Visibility',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
            {
                extend: 'pdf',
                text: '<i class="fa fa-file-pdf-o"></i> Export to PDF',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            },
              {
                extend: 'print',
                text: '<i class="fa fa-print"></i> Print',
                className: 'btn btn-sm btn-default',
                exportOptions: {
                    columns: function (idx, data, node) {
                        return $(node).is(':visible') && !$(node).hasClass('notexport')
                            ? true
                            : false;
                    },
                },
            }
        ],
        "language": {
            "lengthMenu": "Show _MENU_ entries",
            "search": "Search",
            "info": "Showing _START_ to _END_ of _TOTAL_ entries",
            "paginate": {
                "first": "First",
                "last": "Last",
                "next": "Next",
                "previous": "Previous"
            }
        }
    }); */
  });
$('#airline_agent_select').change(function() {
     
    var selectedAgentText = $(this).find('option:selected').text();
    console.log(selectedAgentText);
      $.ajax({
            url: '/airline/Add_commission_filter',
            type: 'POST', // Use the POST method
            data: { agent: selectedAgentText },
            success: function(response) {
                console.log(response);
                $('#airline_tickets_table tbody').empty();
                  $.each(response, function(index, item) {
                      var ticketno='kk';// item.ticket_no;
            var newRow = '<tr>' +
                '<td><div class="btn-group">' +
                '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                'data-toggle="dropdown" aria-expanded="false">' +
                'actions <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>' +
                '</button>' +
                '<ul class="dropdown-menu dropdown-menu-left" role="menu">' +
             ' <li><a href="/airline/get_airline_commission_print?ticket_no=' + item.ticket_no + '"><i class="glyphicon glyphicon-print"></i>Print</a></li>' +
                '</ul>' +
            
                '<td>' + item.date + '</td>' +
                '<td>' + item.location + '</td>' +
                '<td>' + item.airline_agent + '</td>' +
                 '<td>' + item.invoice_no + '</td>' +
                  '<td>' + item.ticket_no + '</td>' +
                   '<td>' + item.air_ticket_date + '</td>' +
                    '<td>' + item.commision_amount + '</td>' +
                      
                '</tr>';
            $('#airline_tickets_table').append(newRow);
        });
        },
           
            error: function(xhr, status, error) {
                // Handle the error
            }
        });
    
});
         $('#location').change(function() {
               console.log("response");
            var selectedlocation=  $(this).find('option:selected').text();
                     $.ajax({
            url: '/airline/Add_commission_filter',
            type: 'POST', // Use the POST method
            data: { location: selectedlocation },
            success: function(response) {
                console.log(response);
                $('#airline_tickets_table tbody').empty();
                  $.each(response, function(index, item) {
            var newRow = '<tr>' +
                '<td><div class="btn-group">' +
                '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                'data-toggle="dropdown" aria-expanded="false">' +
                'actions <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>' +
                '</button>' +
                '<ul class="dropdown-menu dropdown-menu-left" role="menu">' +
                '<li><a data-href="#" class="btn-modal" data-container=".fleet_model">' +
                
             
                '<i class="glyphicon glyphicon-print"></i>Print</a></li>' +
                '</ul>' +
                '</div></td>' +
                '<td>' + item.date + '</td>' +
                '<td>' + item.location + '</td>' +
                '<td>' + item.airline_agent + '</td>' +
                 '<td>' + item.invoice_no + '</td>' +
                  '<td>' + item.ticket_no + '</td>' +
                   '<td>' + item.air_ticket_date + '</td>' +
                    '<td>' + item.commision_amount + '</td>' +
                      
                '</tr>';
            $('#airline_tickets_table').append(newRow);
        });
        },
           
            error: function(xhr, status, error) {
                // Handle the error
            }
        });
    
});

//invoice
  $('#invoice_no').change(function() {
               console.log("response");
            var invoice_no=  $(this).find('option:selected').text();
                     $.ajax({
            url: '/airline/Add_commission_filter',
            type: 'POST', // Use the POST method
            data: { invoice_no: invoice_no },
            success: function(response) {
                console.log(response);
                $('#airline_tickets_table tbody').empty();
                  $.each(response, function(index, item) {
            var newRow = '<tr>' +
                '<td><div class="btn-group">' +
                '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                'data-toggle="dropdown" aria-expanded="false">' +
                'actions <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>' +
                '</button>' +
                '<ul class="dropdown-menu dropdown-menu-left" role="menu">' +
                '<li><a data-href="#" class="btn-modal" data-container=".fleet_model">' +
                
             
                '<i class="glyphicon glyphicon-print"></i>Print</a></li>' +
                '</ul>' +
                '</div></td>' +
                '<td>' + item.date + '</td>' +
                '<td>' + item.location + '</td>' +
                '<td>' + item.airline_agent + '</td>' +
                 '<td>' + item.invoice_no + '</td>' +
                  '<td>' + item.ticket_no + '</td>' +
                   '<td>' + item.air_ticket_date + '</td>' +
                    '<td>' + item.commision_amount + '</td>' +
                      
                '</tr>';
            $('#airline_tickets_table').append(newRow);
        });
        },
           
            error: function(xhr, status, error) {
                // Handle the error
            }
        });
    
});

//ticket
  $('#prt_ticket_no').change(function() {
               console.log("response");
            var selectedprt_ticket_no=  $(this).find('option:selected').text();
                     $.ajax({
            url: '/airline/Add_commission_filter',
            type: 'POST', // Use the POST method
            data: { prt_ticket_no: selectedprt_ticket_no },
            success: function(response) {
                console.log(response);
                $('#airline_tickets_table tbody').empty();
                  $.each(response, function(index, item) {
            var newRow = '<tr>' +
                '<td><div class="btn-group">' +
                '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                'data-toggle="dropdown" aria-expanded="false">' +
                'actions <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>' +
                '</button>' +
                '<ul class="dropdown-menu dropdown-menu-left" role="menu">' +
                '<li><a data-href="#" class="btn-modal" data-container=".fleet_model">' +
                
             
                '<i class="glyphicon glyphicon-print"></i>Print</a></li>' +
                '</ul>' +
                '</div></td>' +
                '<td>' + item.date + '</td>' +
                '<td>' + item.location + '</td>' +
                '<td>' + item.airline_agent + '</td>' +
                 '<td>' + item.invoice_no + '</td>' +
                  '<td>' + item.ticket_no + '</td>' +
                   '<td>' + item.air_ticket_date + '</td>' +
                    '<td>' + item.commision_amount + '</td>' +
                      
                '</tr>';
            $('#airline_tickets_table').append(newRow);
        });
        },
           
            error: function(xhr, status, error) {
                // Handle the error
            }
        });
    
});
//air ticket date
  $('#location').change(function() {
               console.log("response");
            var selectedlocation=  $(this).find('option:selected').text();
                     $.ajax({
            url: '/airline/Add_commission_filter',
            type: 'POST', // Use the POST method
            data: { location: selectedlocation },
            success: function(response) {
                console.log(response);
                $('#airline_tickets_table tbody').empty();
                  $.each(response, function(index, item) {
            var newRow = '<tr>' +
                '<td><div class="btn-group">' +
                '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                'data-toggle="dropdown" aria-expanded="false">' +
                'actions <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>' +
                '</button>' +
                '<ul class="dropdown-menu dropdown-menu-left" role="menu">' +
                '<li><a data-href="#" class="btn-modal" data-container=".fleet_model">' +
                
             
                '<i class="glyphicon glyphicon-print"></i>Print</a></li>' +
                '</ul>' +
                '</div></td>' +
                '<td>' + item.date + '</td>' +
                '<td>' + item.location + '</td>' +
                '<td>' + item.airline_agent + '</td>' +
                 '<td>' + item.invoice_no + '</td>' +
                  '<td>' + item.ticket_no + '</td>' +
                   '<td>' + item.air_ticket_date + '</td>' +
                    '<td>' + item.commision_amount + '</td>' +
                      
                '</tr>';
            $('#airline_tickets_table').append(newRow);
        });
        },
           
            error: function(xhr, status, error) {
                // Handle the error
            }
        });
    
});
//date range
  $('#location').change(function() {
               console.log("response");
            var selectedlocation=  $(this).find('option:selected').text();
                     $.ajax({
            url: '/airline/Add_commission_filter',
            type: 'POST', // Use the POST method
            data: { location: selectedlocation },
            success: function(response) {
                console.log(response);
                $('#airline_tickets_table tbody').empty();
                  $.each(response, function(index, item) {
            var newRow = '<tr>' +
                '<td><div class="btn-group">' +
                '<button type="button" class="btn btn-info dropdown-toggle btn-xs" ' +
                'data-toggle="dropdown" aria-expanded="false">' +
                'actions <span class="caret"></span><span class="sr-only">Toggle Dropdown</span>' +
                '</button>' +
                '<ul class="dropdown-menu dropdown-menu-left" role="menu">' +
                '<li><a data-href="#" class="btn-modal" data-container=".fleet_model">' +
                
             
                '<i class="glyphicon glyphicon-print"></i>Print</a></li>' +
                '</ul>' +
                '</div></td>' +
                '<td>' + item.date + '</td>' +
                '<td>' + item.location + '</td>' +
                '<td>' + item.airline_agent + '</td>' +
                 '<td>' + item.invoice_no + '</td>' +
                  '<td>' + item.ticket_no + '</td>' +
                   '<td>' + item.air_ticket_date + '</td>' +
                    '<td>' + item.commision_amount + '</td>' +
                      
                '</tr>';
            $('#airline_tickets_table').append(newRow);
        });
        },
           
            error: function(xhr, status, error) {
                // Handle the error
            }
        });
    
});


</script>
<script>
/*
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
            
        },
        init_countries: function() {
             
        },
        
        
        listener: function() {
        
            $('#customer_group_select').change(function() {
               // airline_tickets_table.ajax.reload();
                
                $('#customer_select').html('');
                var customer_group_id = $(this).val();
                if(!isNaN(parseInt(customer_group_id))) {
                    $.ajax({ 
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

    } */
    
    
    $('#date_range_filter').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range_filter').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            var airlineTicketsTable = $('#airline_tickets_table');
        }
    );


    $('#date_range_filter').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range_filter').val('');
    });

	$('#date_range_filter').data('daterangepicker').setStartDate(moment().startOf('month'));
	$('#date_range_filter').data('daterangepicker').setEndDate(moment().endOf('month'));

</script>

@endsection