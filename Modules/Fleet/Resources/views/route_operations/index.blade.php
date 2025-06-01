@extends('layouts.app')

@section('title', __('fleet::lang.route_operations'))

@section('content')
<!-- Main content -->
<section class="content">
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
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __( 'fleet::lang.location' )) !!}
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'location_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('contact_id', __( 'fleet::lang.customer' )) !!}
                    {!! Form::select('contact_id', $contacts, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'contact_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('route_id', __( 'fleet::lang.route' )) !!}<br>
                    {!! Form::select('route_id', $routes, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'route_id']);
                    !!}
                </div>
            </div>
            </div><div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('vehicle_no', __( 'fleet::lang.vehicle_no' )) !!}
                    {!! Form::select('vehicle_no', $vehicle_numbers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'vehicle_no']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('driver_id', __( 'fleet::lang.driver' )) !!}<br>
                    {!! Form::select('driver_id', $drivers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'driver_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('helper_id', __( 'fleet::lang.helper' )) !!}<br>
                    {!! Form::select('helper_id', $helpers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'helper_id']);
                    !!}
                </div>
            </div>
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
            </div><div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('payment_method', __( 'fleet::lang.payment_methods' )) !!}
                    {!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'payment_method']);
                    !!}
                </div>
            </div>
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'fleet::lang.all_your_route_operations')])
            @slot('tool')
            <div class="box-tools">
                <a class="btn btn-primary pull-right" id="add_route_operation_btn"
                    href="{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@create')}}"
                   >
                    <i class="fa fa-plus"></i> @lang( 'fleet::lang.add' )</a>
            </div>
            @endslot

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="route_operation_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'messages.action' )</th>
                                     <th>@lang( 'fleet::lang.id' )</th>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>@lang( 'fleet::lang.location' )</th>
                                    <th>@lang( 'fleet::lang.customer' )</th>
                                    <th>@lang( 'fleet::lang.trip_categories' )</th>
                                    <th>@lang( 'fleet::lang.route' )</th>
                                    <th>@lang( 'fleet::lang.bowser_no' )</th>
                                    <th>@lang( 'fleet::lang.order_number' )</th>
                                    <th>@lang( 'fleet::lang.invoice_no' )</th>
                                    <th>@lang( 'fleet::lang.amount' )</th>
                                    <th>@lang( 'fleet::lang.payment_method' )</th>
                                     <th>@lang( 'fleet::lang.payment_status' )</th>
                                    <th>@lang( 'fleet::lang.balance_due' )</th>
                                    <th>@lang( 'fleet::lang.product' )</th>
                                    <th>@lang( 'fleet::lang.qty' )</th>
                                    <th>@lang( 'fleet::lang.driver' )</th>
                                    <th>@lang( 'fleet::lang.helper' )</th>
                                    <th>@lang( 'fleet::lang.distance_km' )</th>
                                    <th>@lang( 'fleet::lang.starting_meter' )</th>
                                    <th>@lang( 'fleet::lang.ending_meter' )</th>
                                    <th>@lang( 'fleet::lang.meter_difference' )</th>
                                    <th>@lang( 'fleet::lang.driver_incentive' )</th>
                                    <th>@lang( 'fleet::lang.helper_incentive' )</th>
                                   
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
    <div class="modal fade fleet_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade payment_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    
    <div class="modal fade" id="noteModal" role="dialog" aria-labelledby="gridSystemModalLabel">
        <div class="modal-dialog">
          <div class="modal-content">
    
            <!-- Modal Header -->
            <div class="modal-header">
              <h4 class="modal-title">@lang( 'fleet::lang.delivered_to_acc_no' )</h4>
              <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>
    
            <!-- Modal Body -->
            <div class="modal-body">
              <p id="noteContent" class="text-center text-bold"></p>
            </div>
    
          </div>
        </div>
      </div>
    
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
$(document).on('click', '.delivered-to-acc-no', function(e){
  let note = $(this).data('string');
  $("#noteContent").html(note);
  $("#noteModal").modal('show');
   
});
      
    $('#location_id option:eq(1)').attr('selected', true);
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            route_operation_table.ajax.reload();
            
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

    // route_operation_table
    $(document).ready(function(){
        route_operation_table = $('#route_operation_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@index')}}",
                    data: function(d){
                        d.location_id = $('#location_id').val();
                        d.contact_id = $('#contact_id').val();
                        d.route_id = $('#route_id').val();
                        d.vehicle_no = $('#vehicle_no').val();
                        d.driver_id = $('#driver_id').val();
                        d.helper_id = $('#helper_id').val();
                        d.payment_status = $('#payment_status').val();
                        d.payment_method = $('#payment_method').val();
                        d.start_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'action', name: 'action'},
                    {data: 'id', name: 'id'},
                    {data: 'date_of_operation', name: 'date_of_operation'},
                    {data: 'location_name', name: 'business_locations.name'},
                    {data: 'contact_name', name: 'contacts.name'},
                    {data: 'amount_method', name: 'trip_categories.amount_method'},
                    {data: 'route_name', name: 'route_name'},
                    {data: 'vehicle_number', name: 'vehicle_number'},
                    {data: 'order_number', name: 'order_number'},
                    {data: 'invoice_no', name: 'invoice_no'},
                    {data: 'amount', name: 'amount'},
                    {data: 'method', name: 'method'},
                     {data: 'payment_status', name: 'payment_status'},
                    {data: 'balance_due', name: 'balance_due'},
                    {data: 'product_name', name: 'route_products.name'},
                    {data: 'qty', name: 'qty'},
                    {data: 'driver_name', name: 'driver_name'},
                    {data: 'helper_name', name: 'helper_name'},
                    {data: 'distance', name: 'distance'},
                    {data: 'starting_meter', name: 'starting_meter'},
                    {data: 'ending_meter', name: 'ending_meter'},
                    {data: 'meter_difference', name: 'meter_difference'},
                    {data: 'driver_incentive', name: 'driver_incentive'},
                    {data: 'helper_incentive', name: 'helper_incentive'},
                  
                ],
                createdRow: function( row, data, dataIndex ) {
                }
            });
        });

        $('#date_range_filter, #location_id, #contact_id, #route_id, #vehicle_no, #driver_id, #helper_id, #payment_status, #payment_method').change(function () {
            route_operation_table.ajax.reload();
        })
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
                            route_operation_table.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection