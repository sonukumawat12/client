@extends('layouts.app')
@section('title', __('fleet::lang.view_driver'))

@section('content')
<style>
    .select2{
        width: 100% !important;
    }
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
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ $driver->name }}</h1>
</section>
<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-4 col-xs-12">
            {!! Form::select('fleet_id', $driver_dropdown, $driver->id , ['class' => 'form-control select2', 'id' =>
            'fleet_id']); !!}
        </div>
        <div class="col-md-2 col-xs-12"></div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="nav-tabs-custom">
                <ul class="nav nav-tabs nav-justified">
                    <li class="
                            @if(!empty($view_type) &&  $view_type == 'ledger')
                                active
                            @else
                                ''
                            @endif">
                        <a href="#ledger_tab" data-toggle="tab" aria-expanded="true"><i class="fa fa-anchor"
                                aria-hidden="true"></i> @lang('lang_v1.ledger')</a>
                    </li>
                </ul>

                <div class="tab-content" style="background: #fbfcfc;">
                    <div class="tab-pane
                                @if(!empty($view_type) &&  $view_type == 'ledger')
                                    active
                                @else
                                    ''
                                @endif" id="ledger_tab">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-12">

                                        <div class="col-md-3">
                                            <div class="form-group">
                                                {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
                                                {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'ledger_date_range_new']); !!}
                                            </div>
                                        </div>
                                    

                                    </div>
                                    <div class="col-md-12">
                                        <div class="col-md-6"></div>
                                        <div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
                                            <p class=" bg_color"
                                                style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
                                                @lang('lang_v1.account_summary')</p>
                                            <hr>
                                            <table
                                                class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif"
                                                id="customer_detail_table">
                                                <tr>
                                                    <td>@lang('lang_v1.beginning_balance')</td>
                                                    <td id="bf_balance">0.00</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>@lang('essentials::lang.salaries_income')</td>
                                                    <td id="total_income">0.00</td>
                                                </tr>
                                                
                                                <tr>
                                                    <td>@lang('essentials::lang.loans_advances')</td>
                                                    <td id="total_paid">0.00</td>
                                                </tr>
                                                <tr>
                                                    <td><strong>@lang('lang_v1.balance_due')</strong></td>
                                                    <td id="balance_due">0.00</td>
                                                </tr>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="fleet_ledger_table">
                                        <thead>
                                            <tr>
                                                <th>@lang('lang_v1.date')</th>                                
                                                <th>@lang('fleet::lang.description')</th>
                                                <th>@lang('fleet::lang.income')</th>
                                                <th>@lang('fleet::lang.paid_amount')</th>
                                                <th>@lang('fleet::lang.balance')</th>
                                                <th>@lang('fleet::lang.payment_method')</th>
                                            </tr>
                                        </thead>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</section>
<div class="modal fade" id="view_note_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
</div>
@endsection

@section('javascript')
<script>
     $('#fleet_id').change( function() {
        if ($(this).val()) {
            window.location = "{{url('/hrm/employee-ledger')}}/" + $(this).val()+"?tab={{$view_type}}";
        }
    });
    
    
    function updateSummary(){
        var start_date = $('#ledger_date_range_new')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('#ledger_date_range_new')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
                        
        $.ajax({
                method: 'get',
                contentType: 'json',
                url: '{{action('\Modules\Essentials\Http\Controllers\EssentialsEmployeesController@fetchLedgerSummarised')}}',
                data: {
                    contact_id : "{{$emp_id}}",
                    start_date : start_date,
                    end_date : end_date
                },
                success: function(result) {
                    $("#bf_balance").html(result.balance);
                    $("#total_income").html(result.income);
                    $("#total_paid").html(result.paid);
                    $("#balance_due").html(result.balance_due);
                },
            });
    }
    
    
    if ($('#ledger_date_range_new').length == 1) {
        $('#ledger_date_range_new').daterangepicker(dateRangeSettings, function(start, end) {
            $('#ledger_date_range_new').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            
           fleet_ledger_table.ajax.reload();
           updateSummary();
        });
        $('#custom_date_apply_button').on('click', function() {
            let startDate = $('#custom_date_from_year1').val() + $('#custom_date_from_year2').val() + $('#custom_date_from_year3').val() + $('#custom_date_from_year4').val() + "-" + $('#custom_date_from_month1').val() + $('#custom_date_from_month2').val() + "-" + $('#custom_date_from_date1').val() + $('#custom_date_from_date2').val();
            let endDate = $('#custom_date_to_year1').val() + $('#custom_date_to_year2').val() + $('#custom_date_to_year3').val() + $('#custom_date_to_year4').val() + "-" + $('#custom_date_to_month1').val() + $('#custom_date_to_month2').val() + "-" + $('#custom_date_to_date1').val() + $('#custom_date_to_date2').val();

            if (startDate.length === 10 && endDate.length === 10) {
                let formattedStartDate = moment(startDate).format(moment_date_format);
                let formattedEndDate = moment(endDate).format(moment_date_format);

                $('#ledger_date_range_new').val(
                    formattedStartDate + ' ~ ' + formattedEndDate
                );

                $('#ledger_date_range_new').data('daterangepicker').setStartDate(moment(startDate));
                $('#ledger_date_range_new').data('daterangepicker').setEndDate(moment(endDate));

                $('.custom_date_typing_modal').modal('hide');
                fleet_ledger_table.ajax.reload();
                updateSummary();
            } else {
                alert("Please select both start and end dates.");
            }
        });
        $('#ledger_date_range_new').on('apply.daterangepicker', function(ev, picker) {
            if (picker.chosenLabel === 'Custom Date Range') {
                $('.custom_date_typing_modal').modal('show');
            }
        });
        $('#ledger_date_range_new').on('cancel.daterangepicker', function(ev, picker) {
            
        });
        $('#ledger_date_range_new')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#ledger_date_range_new')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    $(document).ready(function () {
       updateSummary();
       fleet_ledger_table = $('#fleet_ledger_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'asc']],
            ajax: {
                url: '{{action('\Modules\Essentials\Http\Controllers\EssentialsEmployeesController@fetchEmployeeLedger')}}',
                data: function (d) {
                    d.emp_id = "{{$emp_id}}";
                    
                    var start_date = $('#ledger_date_range_new')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('#ledger_date_range_new')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start_date;
                    d.end_date = end_date;
                }
            },
            columns: [
                { data: 'date', name: 'date' },
                { data: 'description', name: 'description' },
                { data: 'amount', name: 'amount' },
                { data: 'paid_amount', name: 'paid_amount' },
                { data: 'balance', name: 'balance' },
                { data: 'payment_method', name: 'payment_method' },
                
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        $(document).on('change', '#ledger_transaction_type', function() {
            fleet_ledger_table.ajax.reload();
            updateSummary();
        });

        
    })
    
</script>
    
@endsection