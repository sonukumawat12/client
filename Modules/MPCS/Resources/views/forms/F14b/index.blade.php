@extends('layouts.app')
@section('title', __('mpcs::lang.F20andF14b_form'))

@section('content')
<!-- Main content -->
<section class="content">
    <style>
        table {
          width: 100%;
          border-collapse: collapse;
        }
        
        table td {
          height: 10px; 
        }
        
        table td[align="right"] {
          text-align: right;  
          padding-right: 10px;
        }

        .no-border-table {
            border-collapse: collapse;
            width: 100%;
        }

        .no-border-table tr {
            border: none !important;
        }

        .no-border-table td {
            border: none !important;
            padding: 8px;
        }

        .no-border-table td,
        .no-border-table th {
            border-bottom: none !important;
        }

        .note-container {
            width: 100%;
            height: 150px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid black;
            box-sizing: border-box;
        }

        .dataTables_filter, .dataTables_info { display: none; }

        .dots {
            display: flex;
            justify-content: space-between;
            width: 100%;
        }

        .dots::before {
            content: "";
            flex-grow: 1;
            border-bottom: 1px dotted black;
            margin: 0 5px;
        }

        /* Styling for print */
        @media print {
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
            }

            /* Hide non-printable elements */
            #printButton, .dataTables_filter, .dataTables_info, .table-responsive .no-print {
                display: none;
            }

            /* Adjust table layout */
            .table-responsive {
                width: 100%;
                margin: 0;
            }

            /* Style adjustments for tables */
            .no-border-table {
                width: 100%;
                margin-top: 20px;
            }

            /* Ensure all the content fits within the page */
            @page {
                margin: 20mm;
            }

            .no-print, .no-print * {
                display: none !important;
            } 
            .header-area, .header-area * {
                display: none !important;
            }
            .nav, .nav-tabs * {
                display: none !important;
            }
            .page-title-area * {
                display: none !important;
            }

            .dots {
                display: flex;
                justify-content: space-between;
                width: 100%;
            }

            .dots::before {
                content: "";
                flex-grow: 1;
                border-bottom: 1px dotted black;
                margin: 0 5px;
            }
        }
    </style>
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_9a_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('form_9a_location_id', $business_locations, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_9a_date', __('date') . ':') !!}
                    {!! Form::date('start_date', now()->subMonth()->format('Y-m-d'), ['class' => 'form-control', 'placeholder' => __('mpcs::lang.date_and_time'), 'id' => 'form_9a_start_date']) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('end_date', __('end_date') . ':') !!}
                    {!! Form::date('end_date', now()->format('Y-m-d'), ['class' => 'form-control', 'placeholder' => __('mpcs::lang.date_and_time'), 'id' => 'form_9a_end_date']) !!}
                </div>
            </div>

            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
            <div class="box-tools">
                <button class="btn btn-primary print_report pull-right" id="print_div">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
            </div>
            @endslot

            <div class="col-md-12">
                <div class="row" style="margin-top: 20px;" id="print_content">
                    <div class="col-md-12">
                        <h4>@lang('mpcs::lang.credit_sales_report')</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="form_9b_table">
                                        <thead class="align-middle">
                                            <tr class="align-middle text-center">
                                                <th>@lang('mpcs::lang.settlement_date')</th>
                                                <th>@lang('mpcs::lang.description')</th>
                                                <th>@lang('mpcs::lang.order_no')</th>
                                                <th>@lang('mpcs::lang.balance_qty')</th>
                                                <th>@lang('mpcs::lang.unit_price')</th>
                                                <th>@lang('mpcs::lang.final_total')</th>
                                                <th>@lang('mpcs::lang.customer')</th>
                                                <th>@lang('mpcs::lang.location')</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse ($credit_sales as $sale)
                                                <tr>
                                                    <td>{{ $sale->settlement_date }}</td>
                                                    <td>{{ $sale->description }}</td>
                                                    <td>{{ $sale->order_no }}</td>
                                                    <td>{{ $sale->balance_qty }}</td>
                                                    <td>{{ $sale->unit_price }}</td>
                                                    <td>{{ $sale->final_total }}</td>
                                                    <td>{{ $sale->customer }}</td>
                                                    <td>{{ $sale->location }}</td>
                                                </tr>
                                            @empty
                                                <tr>
                                                    <td colspan="8" class="text-center">@lang('mpcs::lang.no_data_found')</td>
                                                </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->
@endsection
@section('javascript')
<script>
    // form 14b
     $('#f14b_date').daterangepicker();
     if ($('#f14b_date').length == 1) {
         $('#f14b_date').daterangepicker(dateRangeSettings, function(start, end) {
             $('#f14b_date').val(
                 start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
             );
         });
         $('#f14b_date').on('cancel.daterangepicker', function(ev, picker) {
             $('#product_sr_date_filter').val('');
         });
         $('#f14b_date')
             .data('daterangepicker')
             .setStartDate(moment().startOf('month'));
         $('#f14b_date')
             .data('daterangepicker')
             .setEndDate(moment().endOf('month'));
     }
     $(document).ready(function(){
        getForm14b();
        $('#f14b_date, #f14b_location_id').change(function(){
            getForm14b();
        })
     })
     function getForm14b(){
            var start_date = $('input#f14b_date')
                    .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#f14b_date')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            start_date = start_date;
            end_date = end_date;
            location_id = $('#f14b_location_id').val();

         $.ajax({
             method: 'get',
             url: '/mpcs/get-form-14b',
             data: {
                start_date,
                end_date,
                location_id
            },
            contentType: 'html',
            success: function(result) {
                $('#form14B_content').empty().append(result)
            },
         });
     }
     

     

});

</script>
@endsection