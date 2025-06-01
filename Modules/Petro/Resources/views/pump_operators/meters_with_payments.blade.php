@extends('layouts.'.$layout)
@section('title', __('petro::lang.meters_with_payments'))

@section('content')
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <div class="col-md-12">
            <h1 class="pull-left">@lang('petro::lang.meters_with_payments')</h1>
            <h2 style="color: red; text-align: center;">Shift_NO: {{$shift_number}}</h2>
        </div>
        <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-lg pull-right" style=" background-color: orange; color: #fff; margin-left: 5px;">
            @lang('petro::lang.logout')
        </a>
        <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}" class="btn btn-flat btn-lg pull-right" style="color: #fff; background-color:#810040;">
            @lang('petro::lang.dashboard')
        </a>
    </section>

    <div class="clearfix"></div>

    @include('petro::pump_operators.partials.meters_with_payments')
    
@endsection

@section('javascript')
    <script type="text/javascript">
        var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";

        $(document).ready( function(){
            if ($('#meters_with_payments_date_range').length == 1) {
                $('#meters_with_payments_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                    $('#meters_with_payments_date_range').val(
                        start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                    );
                    pump_operators_meters_with_payments_table.ajax.reload();
                });
                $('#custom_date_apply_button').on('click', function() {
                    let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                    let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();
            
                    if (startDate.length === 10 && endDate.length === 10) {
                        let formattedStartDate = moment(startDate).format(moment_date_format);
                        let formattedEndDate = moment(endDate).format(moment_date_format);
            
                        $('#meters_with_payments_date_range').val(
                            formattedStartDate + ' ~ ' + formattedEndDate
                        );
            
                        $('#meters_with_payments_date_range').data('daterangepicker').setStartDate(moment(startDate));
                        $('#meters_with_payments_date_range').data('daterangepicker').setEndDate(moment(endDate));
            
                        $('.custom_date_typing_modal').modal('hide');
                        pump_operators_meters_with_payments_table.ajax.reload();
                    } else {
                        alert("Please select both start and end dates.");
                    }
                });
                $('#meters_with_payments_date_range').on('apply.daterangepicker', function(ev, picker) {
                    if (picker.chosenLabel === 'Custom Date Range') {
                        $('.custom_date_typing_modal').modal('show');
                    }
                });
                $('#meters_with_payments_date_range').on('cancel.daterangepicker', function(ev, picker) {
                    $('#meters_with_payments_date_range').val('');
                });
                $('#meters_with_payments_date_range')
                    .data('daterangepicker')
                    .setStartDate(moment().startOf('month'));
                $('#meters_with_payments_date_range')
                    .data('daterangepicker')
                    .setEndDate(moment().endOf('month'));
            }
            pump_operators_meters_with_payments_table = $('#pump_operators_meters_with_payments_table').DataTable({
                processing: true,
                serverSide: true,
                aaSorting: [[0, 'asc']],
                ajax: {
                    url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@metersWithPayments', ['only_pumper' => true])}}",
                    data: function(d) {
                        // d.shift_id = $("#payment_summary_shift_id").val();
                    },
                },
                columnDefs: [ {
                    "targets": 0,
                    "orderable": false,
                    "searchable": false
                },
                {
                    "targets": 2,
                    "visible": false
                }],
                columns: [
                    { data: 'date', name: 'date' },
                    { data: 'time', name: 'time' },
                    { data: 'pump_operator_name', name: 'pump_operators.name' },
                    { data: 'collection_form_no', name: 'collection_form_no' },
                    { data: 'pumps', name: 'pumps' },
                    { data: 'unit_price', name: 'unit_price' },
                    { data: 'last_meter', name: 'last_meter' },
                    { data: 'new_meter', name: 'new_meter' },
                    { data: 'qty_sold', name: 'qty_sold' },
                    { data: 'total_sold_amount', name: 'total_sold_amount' },
                    { data: 'payment_type', name: 'payment_type' },
                    { data: 'amount', name: 'amount' }
                ],
            });

            // $(document).on('change', '#payment_summary_shift_id', function(){
            //     pump_operators_meters_with_payments_table.ajax.reload();
            // });
        });
    </script>
@endsection