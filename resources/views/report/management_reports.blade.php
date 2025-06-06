@extends('layouts.app')
@section('title', __('report.management_report'))
@section('css')
    <style>
        .col-xs-5ths,
        .col-sm-5ths,
        .col-md-5ths,
        .col-lg-5ths {
            position: relative;
            min-height: 1px;
            padding-right: 15px;
            padding-left: 15px;
        }

        .col-xs-5ths {
            width: 20%;
            float: left;
        }


        @media (min-width: 768px) {
            .col-sm-5ths {
                width: 20%;
                float: left;
            }
        }

        @media (min-width: 992px) {
            .col-md-5ths {s
                width: 20%;
                float: left;
            }
        }

        @media (min-width: 1200px) {
            .col-lg-5ths {
                width: 20%;
                float: left;
            }
        }


        @media screen {
            .page-footer {
                display: none;
            }
        }
        /*modified by iftekhar*/
        @media print {
            td {

                line-height: 5px !important;

            }

            th {

                line-height: 15px !important;

            }

            .page-body {
                /*margin-top: 120px*/ /* @eng 14/2 */
            }

            .page-header {
                position: fixed;
                top: 0;
            }

            .page-footer {
                position: fixed;
                bottom: 0;
            }

            @page {
                margin: 10mm
            }
        }
    </style>
@endsection
@section('content')
    <!-- Main content -->
    <section class="content">

        <div class="row">
            <div class="col-md-12">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">

                        @can('daily_report.view')
                            <li class=" @if ($tab != 'credit_status') active @endif">
                                <a href="#daily_report" class="daily_report" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('report.daily_report')</strong>
                                </a>
                            </li>
                            
                            <li class="">
                                <a href="#financial_status" class="financial_status" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('report.financial_status')</strong>
                                </a>
                            </li>
                            
                        @endcan

                        @can('daily_summary_report.view')
                            <li class="">
                                <a href="#daily_summary_report" class="daily_summary_report" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('report.daily_summary_report')</strong>
                                </a>
                            </li>
                        @endcan

                        @can('register_report.view')
                            <li class="">
                                <a href="#register_report" class="register_report" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('report.register_report')</strong>
                                </a>
                            </li>
                        @endcan

                        @can('profit_loss_report.view')
                            <li class="">
                                <a href="#profit_loss" class="profit_loss" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>@lang('report.profit_loss')</strong>
                                </a>
                            </li>
                        @endcan
                        @can('DailyReviewAll')
                            <li class=" @if ($tab == 'review_changes') active @endif">
                                <a href="#review_changes" class="review_changes" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>Review Changes</strong>
                                </a>
                            </li>
                        @endcan
                       
                       @can('DailyReviewAll')
                            <li class=" @if ($tab == 'combined_report') active @endif">
                                <a href="#combined_report" class="combined_report" data-toggle="tab">
                                    <i class="fa fa-file-text-o"></i> <strong>Combined Report</strong>
                                </a>
                            </li>
                        @endcan
                        
                        <li class=" @if ($tab == 'stock_purchase_sale_report') active @endif">
                            <a href="#stock_purchase_sale_report" class="stock_purchase_sale_report" data-toggle="tab">
                                <i class="fa fa-file-text-o"></i> <strong>@lang('report.stock_purchase_sale_report')</strong>
                            </a>
                        </li>
                    </ul>
                    <div class="tab-content">
                        @can('daily_report.view')
                            <div class="tab-pane  @if ($tab != 'credit_status') active @endif" id="daily_report">
                                @include('report.partials.daily_report_header')
                            </div>
                            <div class="tab-pane" id="financial_status">
                                @include('report.financial_status')
                            </div>
                        @endcan

                        @can('daily_summary_report.view')
                            <div class="tab-pane" id="daily_summary_report">
                                @include('report.partials.daily_summary_report_header')
                            </div>
                        @endcan

                        @can('register_report.view')
                            <div class="tab-pane" id="register_report">
                                @include('report.register_report')
                            </div>
                        @endcan

                        @can('profit_loss_report.view')
                            <div class="tab-pane" id="profit_loss">
                                @include('report.profit_loss')
                            </div>
                        @endcan
                        @can('credit_status.view')
                            <div class="tab-pane @if ($tab == 'credit_status') active @endif" id="credit_status">
                                @include('report.credit_status')
                            </div>
                        @endcan
                        
                        @can('DailyReviewAll')
                            <div class="tab-pane @if ($tab == 'review_changes') active @endif" id="review_changes">
                                @include('report.review_changes')
                            </div>
                        @endcan
                        
                        @can('DailyReviewAll')
                            <div class="tab-pane @if ($tab == 'combined_report') active @endif" id="combined_report">
                                @include('report.combined_report')
                            </div>
                        
                        @endcan
                        
                        <div class="tab-pane @if ($tab == 'stock_purchase_sale_report') active @endif" id="stock_purchase_sale_report">
                            @include('report.stock_purchase_sale_report')
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </section>
    <!-- /.content -->
    

<div class="modal fade" tabindex="-1" id="email_report_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
<div class="modal-dialog" role="document">
  <div class="modal-content">

    {!! Form::open(['url' => "#", 'method' => 'post', 'id' => 'email_report_form' ]) !!}

    <div class="modal-header">
      <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
          aria-hidden="true">&times;</span></button>
      <h4 class="modal-title">Email report</h4>
    </div>

    <div class="modal-body">
      <div class="row">
          <input type="hidden" val="" id="file_url">
        <div class="form-group col-sm-12">
          {!! Form::label('recipient', 'Recipient:*') !!}
          {!! Form::text('recipient', null, ['class' => 'form-control', 'required', 'placeholder' => "Recipient Email",'id' => "recipient"]); !!}
        </div>

        <div class="form-group col-sm-12">
          {!! Form::label('subject', 'Email Subject:*') !!}
          {!! Form::text('subject', null, ['class' => 'form-control', 'required', 'placeholder' => "Subject",'id' => "subject"]); !!}
        </div>
        
        <div class="form-group col-sm-12">
          {!! Form::label('message', 'Add Message:*') !!}
          {!! Form::textarea('message', null, ['class' => 'form-control', 'placeholder' => "Add Message", 'id' => "message", 'rows' => 3]) !!}

        </div>

      </div>

    </div>

    <div class="modal-footer">
      <button type="submit" class="btn btn-primary">Send</button>
      <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
    </div>

    {!! Form::close() !!}

  </div><!-- /.modal-content -->
</div>
</div>  

<div class="modal fade" tabindex="-1" id="prepare_print" role="dialog" aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
    
        
    
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
              aria-hidden="true">&times;</span></button>
          <h4 class="modal-title">@lang('report.configure_printout')</h4>
        </div>
    
        <div class="modal-body check_group">
          <div class="row">
                <div class="col-md-12">
                    <input type="checkbox" class="check_all input-icheck"> {{ __('role.select_all') }}
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    {!! Form::checkbox('activate_sales_section', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_sales_section']); !!} 
                    {{ __( 'report.sales_section' ) }}
                </div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_sales_by_cashier', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_sales_by_cashier']); !!} 
                    {{ __( 'report.sales_by_cashier' ) }}
                </div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_add', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_add']); !!} 
                    {{ __( 'report.add' ) }}
                </div>
                
                <div class="clearfix"></div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_less', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_less']); !!} 
                    {{ __( 'report.less' ) }}
                </div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_sales_return', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_sales_return']); !!} 
                    {{ __( 'report.sales_return' ) }}
                </div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_purchase_return', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_purchase_return']); !!} 
                    {{ __( 'report.purchase_return' ) }}
                </div>
                
                <div class="clearfix"></div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_financial_status', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_financial_status']); !!} 
                    {{ __( 'report.financial_status' ) }}
                </div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_financial_status_2', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_financial_status_2']); !!} 
                    {{ __( 'report.financial_status_2' ) }}
                </div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_financial_status_breakups', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_financial_status_breakups']); !!} 
                    {{ __( 'report.financial_status_breakups' ) }}
                </div>
                
                <div class="clearfix"></div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_outstanding_details', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_outstanding_details']); !!} 
                    {{ __( 'report.outstanding_details' ) }}
                </div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_stock_value_status', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_stock_value_status']); !!} 
                    {{ __( 'report.stock_value_status' ) }}
                </div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_pump_operators_shortage', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_pump_operators_shortage']); !!} 
                    {{ __( 'report.pump_operators_shortage' ) }}
                </div>
                
                <div class="clearfix"></div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_pump_operators_excess', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_pump_operators_excess']); !!} 
                    {{ __( 'report.pump_operators_excess' ) }}
                </div>
                
                <div class="col-md-4">
                    {!! Form::checkbox('activate_dip_details', 1, false, ['class' => 'input-icheck item-checkbox', 'id' => 'activate_dip_details']); !!} 
                    {{ __( 'report.dip_details' ) }}
                </div>
            </div>
        </div>
    
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary print_report" onclick="printDailyReport()">Print</button>
          <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
        </div>
    
      </div><!-- /.modal-content -->
    </div>
</div>  


@endsection
<!--modified by iftekhar-->
@push('javascript')
    <script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
{{--    <script src="https://code.jquery.com/jquery-3.5.1.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/1.13.1/js/jquery.dataTables.min.js"></script>--}}
{{--    <script src="https://cdn.datatables.net/1.13.1/js/dataTables.bootstrap4.min.js"></script>--}}
    {!! $sells_chart_1->script() !!}
    <script>
  
        $('.email_report').hide();
        $('.whatsapp_report').hide();

        var body = document.getElementsByTagName("body")[0];
        body.className += " sidebar-collapse";
        $(document).ready(function() {
            loadFinancialStatus();
            
            $('#email_report_form').submit(function(e) {
                e.preventDefault(); 
                
                var recipient = $("#recipient").val();
                var subject = $("#subject").val();
                var email = $("#message").val();
                var file = $("#file_url").val();
                
                
                $.ajax({
                url: '/reports/email_report',
                method: 'POST',
                data: {
                    recipient: recipient,
                    subject: subject,
                    email: email,
                    file: file
                },
                success: function(data) {
                    if(data['status'] == 0){
                        toastr.error(data['msg']);
                    }else{
                        toastr.success(data['msg']);
                    }
                    
                    $("#email_report_modal").modal('hide');
                },
                error: function(xhr, status, error) {
                    // Handle the error response, for example:
                    toastr.error('A error occurred!');
                }
            });
                
                
            });
            
            
            $('.credit_filter_change').change(function() {
                var start_date = $('input#credit_status_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                var end_date = $('input#credit_status_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');

                $('#credit_start_date').val(start_date);
                $('#credit_end_date').val(end_date);
                $('#credit_filter_form').submit();
            })
        })
        if ($('#daily_report_date_range').length == 1) {
            $('#daily_report_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#daily_report_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
                getDailyReport(false,"print");
            });
            $('#custom_date_apply_button').on('click', function() {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);

                    $('#daily_report_date_range').val(formattedStartDate + ' ~ ' + formattedEndDate);

                    $('#daily_report_date_range').data('daterangepicker').setStartDate(moment(startDate));
                    $('#daily_report_date_range').data('daterangepicker').setEndDate(moment(endDate));

                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
            $('#daily_report_date_range').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('.custom_date_typing_modal').modal('show');
                }
            });
            $('#daily_report_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#daily_report_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#daily_report_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        
        
        if ($('#review_change_date').length == 1) {
            $('#review_change_date').daterangepicker(dateRangeSettings, function(start, end) {
                $('#review_change_date').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
                review_changes_table.ajax.reload();
            });
            $('#custom_date_apply_button').on('click', function() {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);

                    $('#review_change_date').val(formattedStartDate + ' ~ ' + formattedEndDate);

                    $('#review_change_date').data('daterangepicker').setStartDate(moment(startDate));
                    $('#review_change_date').data('daterangepicker').setEndDate(moment(endDate));

                    $('.custom_date_typing_modal').modal('hide');
                    review_changes_table.ajax.reload();
                } else {
                    alert("Please select both start and end dates.");
                }
            });
            $('#review_change_date').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('.custom_date_typing_modal').modal('show');
                }
            });

            $('#review_change_date').on('cancel.daterangepicker', function(ev, picker) {
                
            });
            $('#review_change_date')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#review_change_date')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        
        // Combined Reports
        if ($('#combined_report_date').length == 1) {
            $('#combined_report_date').daterangepicker(dateRangeSettings, function(start, end) {
                $('#combined_report_date').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
                stock_report.ajax.reload();
                customer_outstanding_report.ajax.reload();
                supplier_outstanding_report.ajax.reload();
            });
            $('#custom_date_apply_button').on('click', function() {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);

                    $('#combined_report_date').val(formattedStartDate + ' ~ ' + formattedEndDate);

                    $('#combined_report_date').data('daterangepicker').setStartDate(moment(startDate));
                    $('#combined_report_date').data('daterangepicker').setEndDate(moment(endDate));

                    $('.custom_date_typing_modal').modal('hide');
                    stock_report.ajax.reload();
                    customer_outstanding_report.ajax.reload();
                    supplier_outstanding_report.ajax.reload();
                } else {
                    alert("Please select both start and end dates.");
                }
            });
            $('#combined_report_date').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('.custom_date_typing_modal').modal('show');
                }
            });

            $('#combined_report_date').on('cancel.daterangepicker', function(ev, picker) {
                
            });
            $('#combined_report_date')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#combined_report_date')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }

        if ($('#stock_purchase_sale_report_date').length == 1) {
            $('#stock_purchase_sale_report_date').daterangepicker(dateRangeSettings, function(start, end) {
                $('#stock_purchase_sale_report_date').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
                stock_purchase_sale_report_table.ajax.reload();
            });
            $('#custom_date_apply_button').on('click', function() {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);

                    $('#stock_purchase_sale_report_date').val(formattedStartDate + ' ~ ' + formattedEndDate);

                    $('#stock_purchase_sale_report_date').data('daterangepicker').setStartDate(moment(startDate));
                    $('#stock_purchase_sale_report_date').data('daterangepicker').setEndDate(moment(endDate));

                    $('.custom_date_typing_modal').modal('hide');
                    stock_purchase_sale_report_table.ajax.reload();
                } else {
                    alert("Please select both start and end dates.");
                }
            });
            $('#stock_purchase_sale_report_date').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('.custom_date_typing_modal').modal('show');
                }
            });

            $('#stock_purchase_sale_report_date').on('cancel.daterangepicker', function(ev, picker) {
                
            });
            $('#stock_purchase_sale_report_date')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#stock_purchase_sale_report_date')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        
        if ($('#financial_status_date_range').length == 1) {
            $('#financial_status_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#financial_status_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
                loadFinancialStatus();
            });
            $('#custom_date_apply_button').on('click', function() {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);

                    $('#financial_status_date_range').val(formattedStartDate + ' ~ ' + formattedEndDate);

                    $('#financial_status_date_range').data('daterangepicker').setStartDate(moment(startDate));
                    $('#financial_status_date_range').data('daterangepicker').setEndDate(moment(endDate));

                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
            $('#financial_status_date_range').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('.custom_date_typing_modal').modal('show');
                }
            });
            $('#financial_status_date_range').on('cancel.daterangepicker', function(ev, picker) {
                
            });
            $('#financial_status_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#financial_status_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        

        $('.daily_report_change').change(function() {
            getDailyReport(false,"print");
        });
        $(document).ready(function() {
            getDailyReport(false,"print");
        });



        let selected_report_cases = []
        
        function generatePdf(html,action,start_date) {
            console.log(html);
            $.ajax({
                url: '/download-pdf',
                method: 'POST',
                data: {
                    html: html
                },
                success: function(data) {
                    // Handle the success response, for example:
                    var downloadUrl = data.path;
                    if(action == "email"){
                        emailPdf(downloadUrl,start_date);
                    }else if(action == "whatsapp"){
                        whatsappPdf(downloadUrl);
                    }
                    
                    
                },
                error: function(xhr, status, error) {
                    // Handle the error response, for example:
                    toastr.error('A error occurred!');
                }
            });
        }
        function whatsappPdf(file){
            var whatsappUrl = 'https://api.whatsapp.com/send?text=' + encodeURIComponent(file);
            window.open(whatsappUrl, '_blank');
        }
        function emailPdf(file,start_date){
            start_date = new Date(start_date);
            start_date = $.datepicker.formatDate('dd/mm/yy', start_date);
            $("#file_url").val(file);
            $("#subject").val("Daily report of "+start_date);
            var msg = "Dear Sir / Madam, \n";
                msg += "\nPlease find the Daily Report of "+start_date+" date for your kind attention. \n";
                msg += "\nThis is a PDF report which could be opened online. \n";
                msg += "\nThank You";
            $("#message").val(msg);
            $("#email_report_modal").modal('show');
        }

        function getDailyReport(print,action) {
            var location_id = $('#daily_report_location_id').val();
            var work_shift = $('#daily_report_work_shift').val();
            
            
            var start_date = $('input#daily_report_date_range')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#daily_report_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            
            
            const start_parts = start_date.split('-');
            const formattedStart = `${start_parts[2]}/${start_parts[1]}/${start_parts[0]}`;
            
            const end_parts = end_date.split('-');
            const formattedEnd = `${end_parts[2]}/${end_parts[1]}/${end_parts[0]}`;
            
            
            var selectedDate = "Date Range: From " + formattedStart + " to "+ formattedEnd;
            
            $("#selected_range").html(selectedDate);
            
            
                
            if(!print){
                var dr_loader = '<div class="row text-center"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></div>';
                $('.daily_report_content').html(dr_loader);
            }

            let print_only = print;
            let action_r = action;
            let report_cases = selected_report_cases
            console.log('report_cases', report_cases)
            
            var activate_sales_section = $('#activate_sales_section').is(':checked') ? 1 : 0;
            var activate_sales_by_cashier = $('#activate_sales_by_cashier').is(':checked') ? 1 : 0;
            var activate_add = $('#activate_add').is(':checked') ? 1 : 0;
            var activate_less = $('#activate_less').is(':checked') ? 1 : 0;
            var activate_sales_return = $('#activate_sales_return').is(':checked') ? 1 : 0;
            var activate_purchase_return = $('#activate_purchase_return').is(':checked') ? 1 : 0;
            var activate_financial_status = $('#activate_financial_status').is(':checked') ? 1 : 0;
            var activate_financial_status_2 = $('#activate_financial_status_2').is(':checked') ? 1 : 0;
            var activate_financial_status_breakups = $('#activate_financial_status_breakups').is(':checked') ? 1 : 0;
            var activate_outstanding_details = $('#activate_outstanding_details').is(':checked') ? 1 : 0;
            var activate_stock_value_status = $('#activate_stock_value_status').is(':checked') ? 1 : 0;
            var activate_pump_operators_shortage = $('#activate_pump_operators_shortage').is(':checked') ? 1 : 0;
            var activate_pump_operators_excess = $('#activate_pump_operators_excess').is(':checked') ? 1 : 0;
            var activate_dip_details = $('#activate_dip_details').is(':checked') ? 1 : 0;

            
            
            $.ajax({
                method: 'get',
                url: '/reports/daily-report',
                data: {
                    location_id,
                    work_shift,
                    start_date,
                    end_date,
                    print_only,
                    report_cases,
                    action_r,
                    activate_sales_section,
                    activate_sales_by_cashier,
                    activate_add,
                    activate_less,
                    activate_sales_return,
                    activate_purchase_return,
                    activate_financial_status,
                    activate_financial_status_2,
                    activate_financial_status_breakups,
                    activate_outstanding_details,
                    activate_stock_value_status,
                    activate_pump_operators_shortage,
                    activate_pump_operators_excess,
                    activate_dip_details
                    
                },
                contentType: 'html',
                success: function(result) {
                    if(print)
                    {
                        if(action == "print"){
                            $('.print_report').html('Print <i class="fa fa-print"></i>')
                            let w = window.open('', '_self');
    
                            $(w.document.body).html(result);
                            w.print();
                            w.close();
                            location.reload();
                        }else if(action == "email"){
                            generatePdf(result,action,start_date)
                        }else if(action == "whatsapp"){
                            generatePdf(result,action,start_date)
                        }
                        
                    }else{
                        $('.daily_report_content').empty().append(result);
                        $("body").on("change", ".report-cases", function() {
                            if ($(this).prop('checked')) {
                                selected_report_cases.push($(this).val())
                                $('#step-' + $(this).val()).show();
                            } else {
                                selected_report_cases.splice(selected_report_cases.indexOf($(this).val()),1);
                                $('#step-' + $(this).val()).hide();
                            }
                        });

                        $('#table-step-1').DataTable( {
                            processing: true,
                            serverSide: true,
                            "ajax": "/reports/daily-report/getOutStandingReceived?start_date="+start_date+"&end_date="+end_date,
                            "columns": [
                                { "data": "operation_date" },
                                { "data": "amount","className": "text-right" ,
                                
                                "render": function(data, type, row, meta) {
                                  return '<span class="od_amount" data-orig-value="' + data.replace(/,/g, '') + '">' + data + '</span>';
                                }},
                                { "data": "customer_name" },
                                { "data": "payment_method" },
                                { "data": "bank_account_number" },
                                { "data": "cheque_numbers" },
                                { "data": "cheque_date" }
                            ],
                            "fnDrawCallback": function (oSettings) {
                                $('#od_total').text(sum_table_col($('#table-step-1'), 'od_amount'));
                                
                                __currency_convert_recursively($('#table-step-1'));
                                
                            },
                        } );
                        // Modified By iftekhar
                        $('#Atable-step-2').DataTable( {
                            processing: true,
                            serverSide: true,
                            "ajax": "/reports/daily-report/getMeterSalesDetails?start_date="+start_date+"&end_date="+end_date,
                            "columns": [
                                { "data": "created_at" },
                                {"data" : "invoice_no"},
                                {"data": "location"},
                                {"data": "username"},
                                { "data": "pump_name" },
                                { "data": "product_name" },
                                { "data": "starting_meter", "className" : "text-right" },
                                { "data": "closing_meter", "className" : "text-right" },
                                { "data": "testing", "className" : "text-right" }, // @eng 8/2 1323
                                { "data": "price", "className" : "text-right" },
                                { "data": "qty", "className" : "text-right" },
                                { "data": "discount_amount", "className" : "text-right",
                                
                                "render": function(data, type, row, meta) {
                                  return '<span class="discount_amount" data-orig-value="' + data.replace(/,/g, '') + '">' + data + '</span>';
                                }},
                                
                                { "data": "total_amount", "className" : "text-right",
                                "render": function(data, type, row, meta) {
                                  return '<span class="total_amount" data-orig-value="' + data.replace(/,/g, '') + '">' + data + '</span>';
                                }} 
                            ],
                            "fnDrawCallback": function (oSettings) {
                                $('#ms_discount').text(sum_table_col($('#Atable-step-2'), 'discount_amount'));
                                $('#ms_total').text(sum_table_col($('#Atable-step-2'), 'total_amount'));
                                
                                __currency_convert_recursively($('#Atable-step-2'));
                                
                            },
                        } );
                        // Modified By iftekhar
                        $('#table-step-3').DataTable( {
                            processing: true,
                            serverSide: true,
                            "ajax": "/reports/daily-report/getSoldItemsReportDetail?start_date="+start_date+"&end_date="+end_date,
                            "columns": [
                                // @eng START 8/2 2057
                                { "data": "transaction_date" },
                                { "data": "invoice_no" },
                                { "data": "customer_name" },
                                { "data": "contact_no" },
                                { "data": "location" },
                                { "data": "method" },
                                { "data": "total_amount","className": "text-right",
                                
                                "render": function(data, type, row, meta) {
                                  return '<span class="total_amount" data-orig-value="' + data.replace(/,/g, '') + '">' + data + '</span>';
                                } },
                                
                                { "data": "total_paid","className": "text-right" ,
                                
                                "render": function(data, type, row, meta) {
                                  return '<span class="total_paid" data-orig-value="' + data.replace(/,/g, '') + '">' + data + '</span>';
                                }},
                                
                                { "data": "sell_return_due","className": "text-right",
                                
                                "render": function(data, type, row, meta) {
                                  return '<span class="sell_return_due" data-orig-value="' + data.replace(/,/g, '') + '">' + data + '</span>';
                                } },
                                
                                { "data": "sell_due","className": "text-right",
                                
                                "render": function(data, type, row, meta) {
                                  return '<span class="sell_due" data-orig-value="' + data.replace(/,/g, '') + '">' + data + '</span>';
                                } },
                                
                                { "data": "shipping_status" },
                                { "data": "total_items","className": "text-right" },
                                { "data": "types_of_service_name" },
                                { "data": "added_by" },
                                { "data": "staff_note"}
                                // @eng END 8/2 2057
                            ],
                            "fnDrawCallback": function (oSettings) {
                                $('#is_total').text(sum_table_col($('#table-step-3'), 'total_amount'));
                                $('#is_paid').text(sum_table_col($('#table-step-3'), 'total_paid'));
                                $('#is_sell_return_due').text(sum_table_col($('#table-step-3'), 'sell_return_due'));
                                $('#is_sell_due').text(sum_table_col($('#table-step-3'), 'sell_due'));
                                
                                __currency_convert_recursively($('#table-step-3'));
                                
                            },
                        } );

                        $('#table-step-4').DataTable( {
                            processing: true,
                            serverSide: true,
                            "ajax": "/reports/daily-report/getchequesReceivedReport?start_date="+start_date+"&end_date="+end_date,
                            "columns": [
                                // @eng START 8/2 2057
                                {"data": "operation_date"},
                                {"data":"cheque_number"},
                                {"data":"note"},
                                {"data":"image"},
                                {"data":"added_by"},
                                {"data":"opening_balance"},
                                {"data":"debit_amount"},
                                {"data":"credit_amount"},
                                {"data": "remaining_balance"}
                                // @eng END 8/2 2057
                            ]
                        } );

                        $('#table-step-5').DataTable( {
                            processing: true,
                            serverSide: true,
                            "ajax": "/reports/daily-report/getexpensesReport?start_date="+start_date+"&end_date="+end_date,
                            "columns": [
                                // @eng start 8/2 1740
                                { "data": "transaction_date" }, 
                                { "data": "ref_no" },
                                { "data": "payee_name" },
                                { "data": "expense_category" },
                                { "data": "payment_status" },
                                { "data": "final_total", "className" : "text-right" },
                                { "data": "payment_due", "className" : "text-right"},
                                { "data": "payment_method"},
                                { "data": "expense_for"}
                                // @eng END 8/2 1740
                            ]
                        } );
                        //
                    }
                },
            });
        }
        
        function loadFinancialStatus() {
            
            var start_date = $('input#financial_status_date_range')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#financial_status_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
          
                
            if(!print){
                var dr_loader = '<div class="row text-center"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></div>';
                $('#financial_status_report_table').html(dr_loader);
            }

            
            $.ajax({
                method: 'get',
                url: '/reports/financial-status',
                data: {
                    start_date,
                    end_date
                },
                contentType: 'html',
                success: function(result) {
                    $('#financial_status_report_table').empty().append(result);
                },
            });
        }
        
        
        function prepareToPrint(){
            $("#prepare_print").modal("show");
        }



        function printDailyReport() {
            $('.print_report').html('Printing . . .')
            getDailyReport(true,"print");
        }

        function printStepNum($dom, step) {

        }
    </script>



    <script>
        if ($('#daily_summary_report_date_range').length == 1) {
            $('#daily_summary_report_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#daily_summary_report_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#custom_date_apply_button').on('click', function() {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);

                    $('#daily_summary_report_date_range').val(formattedStartDate + ' ~ ' + formattedEndDate);

                    $('#daily_summary_report_date_range').data('daterangepicker').setStartDate(moment(startDate));
                    $('#daily_summary_report_date_range').data('daterangepicker').setEndDate(moment(endDate));

                    $('.custom_date_typing_modal').modal('hide');
                } else {
                    alert("Please select both start and end dates.");
                }
            });
            $('#daily_summary_report_date_range').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('.custom_date_typing_modal').modal('show');
                }
            });
            $('#daily_summary_report_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#daily_summary_report_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#daily_summary_report_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }

        $('.daily_summary_report_change').change(function() {
            getDailySummaryReport();
        });

        $(document).ready(function() {
            getDailySummaryReport();
        });

        function getDailySummaryReport() {
            var location_id = $('#daily_summary_report_location_id').val();
            var work_shift = $('#daily_summary_report_work_shift').val();
            var start_date = $('input#daily_summary_report_date_range')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#daily_summary_report_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');

            var dsr_loader = '<div class="row text-center"><i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i></div>';
            $('.daily_summary_report_content').html(dsr_loader);

            $.ajax({
                method: 'get',
                url: '/reports/daily-summary-report',
                data: {
                    location_id,
                    work_shift,
                    start_date,
                    end_date,
                },
                contentType: 'html',
                success: function(result) {
                    $('.daily_summary_report_content').empty().append(result);
                },
            });
        }


        function printDailySummaryDiv() {
            var w = window.open('', '_self');
            var html = document.getElementById("daily_summary_report_div").innerHTML;
            $(w.document.body).html(html);
            w.print();
            w.close();
            location.reload();
        }
    </script>


    <script type="text/javascript">
        $(document).ready(function() {
            
            $('#profit_tabs_filter').daterangepicker(dateRangeSettings, function(start, end) {
                $('#profit_tabs_filter span').html(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
                
                if ($('.categorised_reports li.active').length === 0) {
                    $('.categorised_reports li:first-child').addClass('active');
                }
                
                $('.categorised_reports li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
                updateProfitLoss();
            });
            $('#custom_date_apply_button').on('click', function() {
                let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
                let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

                if (startDate.length === 10 && endDate.length === 10) {
                    let formattedStartDate = moment(startDate).format(moment_date_format);
                    let formattedEndDate = moment(endDate).format(moment_date_format);

                    $('#profit_tabs_filter span').html(
                        formattedStartDate + ' ~ ' + formattedEndDate
                    );

                    $('#profit_tabs_filter').data('daterangepicker').setStartDate(moment(startDate));
                    $('#profit_tabs_filter').data('daterangepicker').setEndDate(moment(endDate));

                    $('.custom_date_typing_modal').modal('hide');
                    $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
                    updateProfitLoss();
                } else {
                    alert("Please select both start and end dates.");
                }
            });
            $('#profit_tabs_filter').on('apply.daterangepicker', function(ev, picker) {
                if (picker.chosenLabel === 'Custom Date Range') {
                    $('.custom_date_typing_modal').modal('show');
                }
            });
            $('#profit_tabs_filter').on('cancel.daterangepicker', function(ev, picker) {
                $('#profit_tabs_filter').html(
                    '<i class="fa fa-calendar"></i> ' + LANG.filter_by_date
                );
                $('.nav-tabs li.active').find('a[data-toggle="tab"]').trigger('shown.bs.tab');
            });
            
            var startDate = moment().startOf('month');
            var endDate = moment().endOf('month');
        
            $('#profit_tabs_filter').data('daterangepicker').setStartDate(startDate);
            $('#profit_tabs_filter').data('daterangepicker').setEndDate(endDate);
        
            // Trigger the change event to update the displayed date range
            $('#profit_tabs_filter').trigger('change');
            
            
            // @eng 9/2 START 2359
            profit_by_products_table = $('#profit_by_products_table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": {
                    "url": "/reports/get-profit/product",
                    "data": function(d) {
                        d.start_date = $('#profit_tabs_filter')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#profit_tabs_filter')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                columns: [
                    {
                        data: 'product',
                        name: 'P.name'
                    },
                    {
                        data: 'total_sales'
                    },
                    {
                        data: 'gross_profit',
                        "searchable": false
                    }
                ],
                fnDrawCallback: function(oSettings) {
                    // var total_profit = sum_table_col($('#profit_by_products_table'), 'gross-profit');
                    // $('#profit_by_products_table .footer_total').text(total_profit);

                    // __currency_convert_recursively($('#profit_by_products_table'));
                                var total_profit = sum_table_col($('#profit_by_products_table'),
                                    'gross-profit');
                                $('#profit_by_products_table .footer_total').text(total_profit);
                                var footer_total_sales = sum_table_col($(
                                    '#profit_by_products_table'), 'total-sales');
                                $('#profit_by_products_table .footer_total_sales').text(
                                    footer_total_sales);
                                    __currency_convert_recursively($('#profit_by_products_table'));                          
                },
            });
            
            
            review_changes_table = $('#review_changes_table').DataTable({
                processing: true,
                serverSide: true,
                "ajax": {
                    "url": "/reports/getreview_changes_table",
                    "data": function(d) {
                        console.log(d)
                        d.start_date = $('#review_change_date')
                            .data('daterangepicker')
                            .startDate.format('YYYY-MM-DD');
                        d.end_date = $('#review_change_date')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                    }
                },
                columns: [
                    {
                        data: 'date',
                        name: 'date'
                    },
                    {
                        data: 'bname',
                        name: 'bname'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                    ,
                    {
                        data: 'action',
                        name: 'action'
                    }
                ],
                
            });

    // In combined Report
    if ($.fn.DataTable.isDataTable('#stock_report')) {
        $('#stock_report').DataTable().destroy();
    }
                stock_report = $('#stock_report').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/combined/stock-report",
                                "data": function(d) {
                                    console.log(d)
                                    d.start_date = $('#combined_report_date')
                                        .data('daterangepicker')
                                        .startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#combined_report_date')
                                        .data('daterangepicker')
                                        .endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [
                                { data: 'SKU', name: 'SKU'},
                                { data: 'Product_Name', name: 'Product Name' },
                                { data: 'Category', name: 'Category' },
                                { data: 'Sub_Category', name: 'Sub Category' },
                                { data: 'Opening_Stock', name: 'Opening Stock' },
                                { data: 'Total_Purchase_Stock', name: 'Total Purchase Stock' },
                                { data: 'Total_Sold_Qty', name: 'Total Sold Qty' },
                                { data: 'Balance', name: 'Balance', render: function(data, type, row) {
                                    let color = 'red';
                                    return `<span style="color:${color};">${data}</span>`;
                                }},
                            ],
                            
                        });
                customer_outstanding_report = $('#customer_outstanding_report').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/combined/customer-outstanding-report",
                                "data": function(d) {
                                    console.log(d)
                                    d.start_date = $('#combined_report_date')
                                        .data('daterangepicker')
                                        .startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#combined_report_date')
                                        .data('daterangepicker')
                                        .endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [
                                { data: 'Customer_Name', name: 'Customer Name'},
                                { data: 'Opening_Balance', name: 'Opening Balance' },
                                { data: 'Total_Credit_Sales', name: 'Total Credit Sales' },
                                { data: 'Total_Payment_Recieved', name: 'Total Payment Recieved' },
                                { data: 'Balance', name: 'Balance', render: function(data, type, row) {
                                    let color = 'red';
                                    return `<span style="color:${color};">${data}</span>`;
                                }},
                            ],
                            
                        });
                supplier_outstanding_report = $('#supplier_outstanding_report').DataTable({
                    processing: true,
                    serverSide: true,
                    "ajax": {
                        "url": "/reports/combined/supplier-outstanding-report",
                        "data": function(d) {
                            console.log(d)
                            d.start_date = $('#combined_report_date')
                                .data('daterangepicker')
                                .startDate.format('YYYY-MM-DD');
                            d.end_date = $('#combined_report_date')
                                .data('daterangepicker')
                                .endDate.format('YYYY-MM-DD');
                        }
                    },
                    columns: [
                        { data: 'Supplier_Name', name: 'Supplier Name'},
                        { data: 'Opening_Balance', name: 'Opening Balance' },
                        { data: 'Total_Credit_Purchase', name: 'Total Credit Purchase' },
                        { data: 'Total_Payment_Paid', name: 'Total Payment Paid' },
                        { data: 'Balance', name: 'Balance', render: function(data, type, row) {
                                    let color = 'red';
                                    return `<span style="color:${color};">${data}</span>`;
                                }},
                    ],
                    
                });
    if ($.fn.DataTable.isDataTable('#stock_purchase_sale_report_table')) {
        $('#stock_purchase_sale_report_table').DataTable().destroy();
    }
    stock_purchase_sale_report_table = $('#stock_purchase_sale_report_table').DataTable({
        processing: true,
        serverSide: true,
        cache: false,
        "ajax": {
            "url": "/reports/stock-purchase-sale-report",
            "data": function(d) {
                d.start_date = $('#stock_purchase_sale_report_date')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
                d.end_date = $('#stock_purchase_sale_report_date')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            }
        },
        columns: [
            { data: 'sku', name: 'products.sku'},
            { data: 'product_name', name: 'products.name' },
            { data: 'opening_stock', name: 'transactions.final_total', searchable: false },
            { data: 'purchase_qty', name: 'purchase_qty', searchable: false },
            { data: 'total_purchase_stock', name: 'total_purchase_stock', searchable: false },
            { data: 'pur_returned_qty', name: 'pur_returned_qty', searchable: false },
            { data: 'total_sold_qty', name: 'total_sold_qty', searchable: false },
            { data: 'sale_amount', name: 'sale_amount', searchable: false },
            { data: 'sold_returned_qty', name: 'sold_returned_qty', searchable: false },
            { data: 'sale_return_amount', name: 'sale_return_amount', searchable: false },
            { data: 'total_sold_qty', name: 'total_sold_qty', searchable: false },
            { data: 'total_sale_amount', name: 'total_sale_amount', searchable: false },
            { data: 'avr_stock_qty', name: 'avr_stock_qty', searchable: false },
            { data: 'avr_stock_value', name: 'avr_stock_value', searchable: false },
            { data: 'profit', name: 'profit', searchable: false },
        ],
    });
            
            $('a[data-toggle="tab"]').on('shown.bs.tab', function(e) {
                var target = $(e.target).attr('href');
                if (target == '#profit_by_categories') {
                    if (typeof profit_by_categories_datatable == 'undefined') {
                        profit_by_categories_datatable = $('#profit_by_categories_table').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/get-profit/category",
                                "data": function(d) {
                                    d.start_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [{
                                    data: 'category',
                                    name: 'C.name'
                                },
                                {
                                    data: 'total_sales'
                                },
                                {
                                    data: 'gross_profit',
                                    "searchable": false
                                }
                            ],
                            fnDrawCallback: function(oSettings) {
                                // var total_profit = sum_table_col($(
                                //     '#profit_by_categories_table'), 'gross-profit');
                                // $('#profit_by_categories_table .footer_total').text(
                                //     total_profit);

                                // __currency_convert_recursively($(
                                //     '#profit_by_categories_table'));
                                var total_profit = sum_table_col($('#profit_by_categories_table'),
                                    'gross-profit');
                                $('#profit_by_categories_table .footer_total').text(total_profit);
                                var footer_total_sales = sum_table_col($(
                                    '#profit_by_categories_table'), 'total-sales');
                                $('#profit_by_categories_table .footer_total_sales').text(
                                    footer_total_sales);
                                    __currency_convert_recursively($('#profit_by_categories_table'));                                
                            },
                        });
                    } else {
                        profit_by_categories_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_brands') {
                    if (typeof profit_by_brands_datatable == 'undefined') {
                        profit_by_brands_datatable = $('#profit_by_brands_table').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/get-profit/brand",
                                "data": function(d) {
                                    d.start_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [{
                                    data: 'brand',
                                    name: 'B.name'
                                },                                
                                {
                                    data: 'total_sales'
                                },
                                {
                                    data: 'gross_profit',
                                    "searchable": false
                                },
                            ],
                            fnDrawCallback: function(oSettings) {
                                // var total_profit = sum_table_col($('#profit_by_brands_table'),
                                //     'gross-profit');
                                // $('#profit_by_brands_table .footer_total').text(total_profit);

                                // __currency_convert_recursively($('#profit_by_brands_table'));
                                
                                var total_profit = sum_table_col($('#profit_by_brands_table'),
                                    'gross-profit');
                                $('#profit_by_brands_table .footer_total').text(total_profit);
                                var footer_total_sales = sum_table_col($(
                                    '#profit_by_brands_table'), 'total-sales');
                                $('#profit_by_brands_table .footer_total_sales').text(
                                    footer_total_sales);
                                    __currency_convert_recursively($('#profit_by_brands_table'));
                            },
                        });
                    } else {
                        profit_by_brands_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_locations') {
                    if (typeof profit_by_locations_datatable == 'undefined') {
                        profit_by_locations_datatable = $('#profit_by_locations_table').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/get-profit/location",
                                "data": function(d) {
                                    d.start_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [{
                                    data: 'location',
                                    name: 'L.name'
                                },
                                {
                                    data: 'total_sales'
                                },
                                {
                                    data: 'gross_profit',
                                    "searchable": false
                                },
                            ],
                            fnDrawCallback: function(oSettings) {
                                // var total_profit = sum_table_col($(
                                //     '#profit_by_locations_table'), 'gross-profit');
                                // $('#profit_by_locations_table .footer_total').text(
                                //     total_profit);

                                // __currency_convert_recursively($('#profit_by_locations_table'));
                                var total_profit = sum_table_col($('#profit_by_locations_table'),
                                    'gross-profit');
                                $('#profit_by_locations_table .footer_total').text(total_profit);
                                var footer_total_sales = sum_table_col($(
                                    '#profit_by_locations_table'), 'total-sales');
                                $('#profit_by_locations_table .footer_total_sales').text(
                                    footer_total_sales);
                                    __currency_convert_recursively($('#profit_by_locations_table'));
                            },
                        });
                    } else {
                        profit_by_locations_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_invoice') {
                    if (typeof profit_by_invoice_datatable == 'undefined') {
                        profit_by_invoice_datatable = $('#profit_by_invoice_table').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/get-profit/invoice",
                                "data": function(d) {
                                    d.start_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [{
                                    data: 'invoice_no',
                                    name: 'sale.invoice_no'
                                },
                                {
                                    data: 'final_total',
                                    name: 'final_total'
                                },
                                {
                                    data: 'gross_profit',
                                    "searchable": false
                                },
                            ],
                            fnDrawCallback: function(oSettings) {
                                var total_profit = sum_table_col($('#profit_by_invoice_table'),
                                    'gross-profit');
                                $('#profit_by_invoice_table .footer_total').text(total_profit);
                                var footer_final_total = sum_table_col($(
                                    '#profit_by_invoice_table'), 'final-total');
                                $('#profit_by_invoice_table .footer_final_total').text(
                                    footer_final_total);

                                __currency_convert_recursively($('#profit_by_invoice_table'));
                            },
                        });
                    } else {
                        profit_by_invoice_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_date') {
                    if (typeof profit_by_date_datatable == 'undefined') {
                        profit_by_date_datatable = $('#profit_by_date_table').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/get-profit/date",
                                "data": function(d) {
                                    d.start_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [{
                                    data: 'transaction_date',
                                    name: 'sale.transaction_date'
                                },
                                {
                                    data: 'total_sales'
                                },
                                {
                                    data: 'gross_profit',
                                    "searchable": false
                                }
                            ],
                            fnDrawCallback: function(oSettings) {
                                // var total_profit = sum_table_col($('#profit_by_date_table'),
                                //     'gross-profit');
                                // $('#profit_by_date_table .footer_total').text(total_profit);
                                // __currency_convert_recursively($('#profit_by_date_table'));
                                
                                var total_profit = sum_table_col($('#profit_by_date_table'),
                                    'gross-profit');
                                $('#profit_by_date_table .footer_total').text(total_profit);
                                var footer_total_sales = sum_table_col($(
                                    '#profit_by_date_table'), 'total-sales');
                                $('#profit_by_date_table .footer_total_sales').text(
                                    footer_total_sales);
                                    __currency_convert_recursively($('#profit_by_date_table'));
                            },
                        });
                    } else {
                        profit_by_date_datatable.ajax.reload();
                    }
                } else if (target == '#profit_by_customer') {
                    if (typeof profit_by_customers_table == 'undefined') {
                        profit_by_customers_table = $('#profit_by_customer_table').DataTable({
                            processing: true,
                            serverSide: true,
                            "ajax": {
                                "url": "/reports/get-profit/customer",
                                "data": function(d) {
                                    d.start_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .startDate.format('YYYY-MM-DD');
                                    d.end_date = $('#profit_tabs_filter')
                                        .data('daterangepicker')
                                        .endDate.format('YYYY-MM-DD');
                                }
                            },
                            columns: [{
                                    data: 'customer',
                                    name: 'CU.name'
                                },
                                {
                                    data: 'total_sales'
                                },
                                {
                                    data: 'gross_profit',
                                    "searchable": false
                                }
                            ],
                            fnDrawCallback: function(oSettings) {
                                // var total_profit = sum_table_col($('#profit_by_customer_table'),
                                //     'gross-profit');
                                // $('#profit_by_customer_table .footer_total').text(total_profit);
                                // __currency_convert_recursively($('#profit_by_customer_table'));
                                var total_profit = sum_table_col($('#profit_by_customer_table'),
                                    'gross-profit');
                                $('#profit_by_customer_table .footer_total').text(total_profit);
                                var footer_total_sales = sum_table_col($(
                                    '#profit_by_customer_table'), 'total-sales');
                                $('#profit_by_customer_table .footer_total_sales').text(
                                    footer_total_sales);
                                    __currency_convert_recursively($('#profit_by_customer_table'));                                 
                            },
                        });
                    } else {
                        profit_by_customers_table.ajax.reload();
                    }
                } else if (target == '#profit_by_day') {
                    var start_date = $('#profit_tabs_filter')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');

                    var end_date = $('#profit_tabs_filter')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    var url = '/reports/get-profit/day?start_date=' + start_date + '&end_date=' + end_date;
                    $.ajax({
                        url: url,
                        dataType: 'html',
                        success: function(result) {
                            $('#profit_by_day').html(result);
                            profit_by_days_table = $('#profit_by_day_table').DataTable({
                                "searching": false,
                                'paging': false,
                                'ordering': false,
                            });
                            // var total_profit = sum_table_col($('#profit_by_day_table'),
                            //     'gross-profit');
                            // $('#profit_by_day_table .footer_total').text(total_profit);
                            // __currency_convert_recursively($('#profit_by_day_table'));
                                var total_profit = sum_table_col($('#profit_by_day_table'),
                                    'gross-profit');
                                $('#profit_by_day_table .footer_total').text(total_profit);
                                var footer_total_sales = sum_table_col($(
                                    '#profit_by_day_table'), 'total-sales');
                                $('#profit_by_day_table .footer_total_sales').text(
                                    footer_total_sales);
                                    __currency_convert_recursively($('#profit_by_day_table'));                               
                        },
                    });
                } else if (target == '#profit_by_products') {
                    profit_by_products_table.ajax.reload();
                }
            });
            // @eng 9/2 END 2359
        });

        //credit status section
        if ($('#credit_status_date_range').length == 1) {
            $('#credit_status_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#credit_status_date_range').val(
                    start.format('MM/DD/YYYY') + ' ~ ' + end.format('MM/DD/YYYY')
                );
            });
            $('#credit_status_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            let date_range =
                @if (!empty(request()->date_range))
                    "{{ request()->date_range }}".split(' - ')
                @else
                    []
                @endif ;
            let set_strat_date = date_range.length ? date_range[0] : moment().startOf('month');
            let set_end_date = date_range.length ? date_range[1] : moment().endOf('month');
            $('#credit_status_date_range')
                .data('daterangepicker')
                .setStartDate(set_strat_date);
            $('#credit_status_date_range')
                .data('daterangepicker')
                .setEndDate(set_end_date);
        }


        $(document).ready(function() {
            var start_date = $('input#credit_status_date_range')
                .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#credit_status_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            update_statistics(start_date, end_date);
            $(document).on('change',
                'input[name="date-period"], #credit_status_business_location, #credit_status_date_range',
                function() {
                    var start_date = $('input#credit_status_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#credit_status_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    update_statistics(start_date, end_date);
                });
        });


        function update_statistics(start, end) {
            var locations_id = $('#credit_status_business_location').val();
            var data = {
                start: start,
                end: end,
                location_id: locations_id
            };
            //get purchase details
            var loader = '<i class="fa fa-refresh fa-spin fa-fw margin-bottom"></i>';
            var period = $('input[name="date-period"]:checked').data('period');
            $('.total_purchase').html(loader);
            $('.purchase_due').html(loader);
            $('.total_sell').html(loader);
            $('.invoice_due').html(loader);
            $.ajax({
                method: 'get',
                url: '/reports/get-credit-status-totals',
                dataType: 'json',
                data: data,
                success: function(data) {
                    $('.total_credit_issued').html(__currency_trans_from_en(data.total_credit_sales, true));
                    $('.total_credit_paid').html(__currency_trans_from_en(data.total_credit_sales_paid, true));
                    $('.total_credit_due').html(__currency_trans_from_en(data.total_credit_sales_due, true));
                },
            });
        }


    </script>

@endpush
