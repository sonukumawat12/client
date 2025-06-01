@extends('layouts.app')

@section('title', __('fleet::lang.milage_changes'))

@section('content')

<style>
    .bg_color {
        background: #357ca5;
        font-size: 20px;
        color: #fff;
    }

    .text-center {
        text-align: center;
    }

    #customer_detail_table th {
        background: #357ca5;
        color: #fff;
    }

    #customer_detail_table>tbody>tr:nth-child(2n+1)>td,
    #customer_detail_table>tbody>tr:nth-child(2n+1)>th {
        background-color: rgba(89, 129, 255, 0.3);
    }
</style>


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
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('milage_status', __( 'fleet::lang.milage_status' )) !!}<br>
                    {!! Form::select('milage_status', ['higher' => 'Higher', 'lower' => 'Lower'], null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'milage_status']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('route_operations', __( 'fleet::lang.route_operations' )) !!}<br>
                    {!! Form::select('route_operations', $route_operations, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'route_operations']);
                    !!}
                </div>
            </div>
            
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('route', __( 'fleet::lang.routes' )) !!}<br>
                    {!! Form::select('route', $routes, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'route']);
                    !!}
                </div>
            </div>
                
            </div>
            @endcomponent
        </div>
    </div>

    <div class="row">
        
        <div class="col-md-12">
        
        <div class="col-md-6"></div>

    </div>
        
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'fleet::lang.milage_changes')])

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="milage_changes_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'fleet::lang.trip_date' )</th>
                                    <th>@lang( 'fleet::lang.meter_changes_date')</th>
                                    <th>@lang( 'fleet::lang.vehicle_no' )</th>
                                    <th>@lang( 'fleet::lang.trip_no' )</th>
                                    <th>@lang( 'fleet::lang.milage_status' )</th>
                                    <th>@lang( 'fleet::lang.changed_distance' )</th>
                                    <th>@lang( 'fleet::lang.driver' )</th>
                                    <th>@lang( 'fleet::lang.helper' )</th>
                                    <th>@lang( 'fleet::lang.changed_by' )</th>
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
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
    
    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            milage_changes_table.ajax.reload();
            
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
    
   // milage_changes_table
    $(document).ready(function(){
        
        milage_changes_table = $('#milage_changes_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@milage_changes')}}",
                    data: function(d){
                        d.vehicle_no = $('#vehicle_no').val();
                        d.type = $('#type').val();
                        d.invoice_no = $('#invoice_no').val();
                        d.start_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#date_range_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.milage_status = $("#milage_status").val();
                        d.route_operations = $("#route_operations").val();
                        d.route = $("#route").val();
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'date_of_operation', name: 'date_of_operation'},
                    {data: 'actual_meter_added_on', name: 'actual_meter_added_on'},
                    {data: 'vehicle_number', name: 'vehicle_number'},
                    {data: 'invoice_no', name: 'invoice_no'},
                    {data: 'milage_status', name: 'milage_status'},
                    {data: 'changed_distance', name: 'changed_distance'},
                    {data: 'driver_name', name: 'driver_name'},
                    {data: 'helper_name', name: 'helper_name'},
                    {data: 'names', name: 'names'},
                  
                ],
                createdRow: function( row, data, dataIndex ) {
                }
            });
        });

        $('#date_range_filter,  #vehicle_no, #driver_id, #helper_id, #milage_status,#route_operations,#route').change(function () {
            milage_changes_table.ajax.reload();
        })
</script>
@endsection