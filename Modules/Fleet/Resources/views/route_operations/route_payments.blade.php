@extends('layouts.app')

@section('title', __('fleet::lang.trip_payments'))

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
                        {!! Form::label('payment_for', __( 'fleet::lang.payment_for' )) !!}<br>
                        {!! Form::select('payment_for', $payment_for, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 'payment_for']);
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
            'fleet::lang.trip_payments')])
            

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="ro_advances_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>@lang( 'fleet::lang.staff' )</th>
                                    <th>@lang( 'fleet::lang.route' )</th>
                                    <th>@lang( 'fleet::lang.vehicle_no' )</th>
                                    <th>@lang( 'fleet::lang.amount' )</th>
                                    <th>@lang( 'fleet::lang.payment_method' )</th>
                                    <th>@lang( 'fleet::lang.payment_for' )</th>
                                    <th>@lang( 'fleet::lang.user' )</th>
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
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
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

    // ro_advances_table
    $(document).ready(function(){
        ro_advances_table = $('#ro_advances_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@getPayments',$id)}}",
                    data: function(d){
                        d.payment_for = $('#payment_for').val();
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
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'staff_name', name: 'staff_name'},
                    {data: 'route_name', name: 'route_name'},
                    {data: 'vehicle_number', name: 'vehicle_number'},
                    {data: 'final_total', name: 'final_total'},
                    {data: 'method', name: 'method'},
                    {data: 'type', name: 'type'},
                    {data: 'username', name: 'username'},
                  
                ],
                createdRow: function( row, data, dataIndex ) {
                }
            });
        });
        
        $('#date_range_filter,#payment_for').change(function () {
            ro_advances_table.ajax.reload();
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
                            ro_advances_table.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection