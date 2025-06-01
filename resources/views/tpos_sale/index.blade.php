@extends('layouts.app')

@section('title', __( 'tpos.list_tpos'))

@section('css')
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.4.0/jspdf.umd.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.9.2/html2pdf.bundle.min.js"></script>

<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.2/pdfmake.min.js"></script> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.2/vfs_fonts.js"></script> -->
@endsection


@section('content')
<!-- Content Header (Page header) -->

<!-- Main content -->
<section class="content main-content-inner no-print">
    @component('components.filters', ['title' => __('report.filters')])
        @include('tpos_sale.partials.list_filters')
    @endcomponent

    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'tpos.list_tpos'), 'date' => ''])
        @can('sell.create')
            @slot('tool')
                <div class="box-tools pull-right">
                    <a class="btn btn-primary" href="{{action('TposController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
                </div>
            @endslot
        @endcan
        @can('sell.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped ajax_view" id="tpos_table">
                    <thead>
                        <tr>
                            <th>@lang('tpos.date')</th>
                            <th>@lang('tpos.status')</th>
                            <th>@lang('tpos.customer')</th>
                            <th>@lang('tpos.tpos_no')</th>
                            <th>@lang('tpos.fpos_no')</th>
                            <th>@lang('tpos.fpos_amount')</th>
                            <th>@lang('tpos.price_gain')</th>
                            <th>@lang('tpos.cashier')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 footer-total text-center">
                            <td colspan="6"><strong>@lang('sale.total'):</strong></td>
                            <td><span class="display_currency" id="footer_sale_total" data-currency_symbol ="true"></span></td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endcan
    @endcomponent
</section>
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade register_details_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>
<div class="modal fade close_register_modal" tabindex="-1" role="dialog" 
    aria-labelledby="gridSystemModalLabel">
</div>

<!-- This will be printed -->
<!-- <section class="invoice print_section" id="receipt_section">
</section> -->


@stop

@section('javascript')
<script type="text/javascript">
$(document).ready( function(){
    //Date range as a button
    $('#list_filter_date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#list_filter_date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
            $("#report_date_range").text("Date Range: "+ $("#list_filter_date_range").val());
            tpos_table.ajax.reload();
        }
    );
    $('#list_filter_date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#list_filter_date_range').val('');
        $("#report_date_range").text("Date Range: - ");
        tpos_table.ajax.reload();
    });
    
    $('#list_filter_date_range').data('daterangepicker').setStartDate(moment().startOf('month'));

    $('#list_filter_date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
    
    tpos_table = $('#tpos_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[1, 'desc']],
        "ajax": {
            "url": "/tpos",
            "data": function ( d ) {
                if($('#list_filter_date_range').val()) {
                    var start = $('#list_filter_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                    var end = $('#list_filter_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                    d.start_date = start;
                    d.end_date = end;
                }
                
                d.location_id = $('#list_filter_location_id').val();
                d.customer_id = $('#list_filter_customer_id').val();
                d.status = $('#list_filter_status').val();
                d.tpos_no = $('#tpos_no').val();
                d.fpos_no = $('#fpos_no').val();
            }
        },
        columns: [
            { data: 'date', name: 'date'  },
            { data: 'status', name: 'status'},
            { data: 'customer_name', name: 'contacts.name'},
            { data: 'tpos_no', name: 'tpos_no'},
            { data: 'fpos_no', name: 'fpos_no'},
            { data: 'fpos_amount', name: 'fpos_amount'},
            { data: 'price_gain', name: 'price_gain'},
            { data: 'username', name: 'users.username'},
        ],
        "fnDrawCallback": function (oSettings) {
            
            $('#footer_sale_total').text(sum_table_col($('#tpos_table'), 'total-price'));

            __currency_convert_recursively($('#tpos_table'));
        },
        
    });

    $(document).on('change', '#list_filter_location_id, #list_filter_customer_id, #list_filter_status, #fpos_no, #tpos_no',  function() {
        tpos_table.ajax.reload();
    });
});

</script>
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
@endsection