@extends('layouts.app')

@section('title', __('fleet::lang.fleet_profit_loss'))

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
                        {!! Form::label('invoice_no', __( 'fleet::lang.ref_no' )) !!}<br>
                        {!! Form::select('invoice_no', $refs, null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 'invoice_no']);
                        !!}
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
                        {!! Form::label('type', __( 'fleet::lang.type' )) !!}<br>
                        {!! Form::select('type', ['expense' => 'Expense', 'income' => 'Income'], null, ['class' => 'form-control select2',
                        'required',
                        'placeholder' => __(
                        'fleet::lang.please_select' ), 'id' => 'type']);
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

        <div class="col-md-6 col-sm-6 col-xs-6 text-right align-right @if(!empty($for_pdf)) width-50 f-left @endif">
            <p class=" bg_color"
                style="margin-top: @if(!empty($for_pdf)) 20px @else 0px @endif; font-weight: 500;">
                @lang('fleet::lang.profit_loss_summary')</p>
            <hr>
            <table
                class="table table-condensed text-left align-left no-border @if(!empty($for_pdf)) table-pdf @endif"
                id="customer_detail_table">
                <tr>
                    <td>@lang('fleet::lang.beginning_balance')</td>
                    <td id="bf_balance">0.00</td>
                </tr>
                
                <tr>
                    <td>@lang('fleet::lang.total_income')</td>
                    <td id="total_income">0.00</td>
                </tr>
                
                <tr>
                    <td>@lang('fleet::lang.total_expense')</td>
                    <td id="total_expense">0.00</td>
                </tr>
                <tr>
                    <td><strong>@lang('fleet::lang.profit_loss')</strong></td>
                    <td id="profit_loss">0.00</td>
                </tr>
            </table>
        </div>

    </div>
        
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'fleet::lang.fleet_profit_loss')])

            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="profit_loss_account_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>@lang( 'fleet::lang.vehicle_no' )</th>
                                    <th>@lang( 'fleet::lang.ref_no' )</th>
                                    <th>@lang( 'fleet::lang.description' )</th>
                                    <th>@lang( 'fleet::lang.income' )</th>
                                    <th>@lang( 'fleet::lang.expense' )</th>
                                    <th>@lang( 'fleet::lang.balance' )</th>
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
    $('#location_id option:eq(1)').attr('selected', true);
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    if ($('#date_range_filter').length == 1) {
        $('#date_range_filter').daterangepicker(dateRangeSettings, function(start, end) {
            $('#date_range_filter').val(
               start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
            );
            profit_loss_account_table.ajax.reload();
            loadSummary();
            
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
    
    function loadSummary(){
        var vehicle_no = $('#vehicle_no').val();
        var type = $('#type').val();
        var invoice_no = $('#invoice_no').val();
        var start_date = $('#date_range_filter')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
            
        var end_date = $('#date_range_filter')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
            
            
        $.ajax({
            method: 'get',
            url: "{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@fetch_profit_loss_summary')}}",
            data: { vehicle_no,type,invoice_no,start_date,end_date },
            success: function(result) {
                $('#bf_balance').text(result.bf);
                $('#total_income').text(result.income);
                $('#total_expense').text(result.expenses);
                $('#profit_loss').text(result.profit);
            },
        });
                    
                    
    }
    

    // profit_loss_account_table
    $(document).ready(function(){
        
        loadSummary();
        
        profit_loss_account_table = $('#profit_loss_account_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@fleet_profit_loss')}}",
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
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'date_of_operation', name: 'date_of_operation'},
                    {data: 'vehicle_number', name: 'vehicle_number'},
                    {data: 'invoice_no', name: 'invoice_no'},
                    {data: 'description', name: 'description'},
                    {data: 'amount', name: 'amount'},
                    {data: 'expense', name: 'expense'},
                    {data: 'balance', name: 'balance'},
                  
                ],
                createdRow: function( row, data, dataIndex ) {
                }
            });
        });

        $('#date_range_filter,  #invoice_no, #vehicle_no, #type').change(function () {
            profit_loss_account_table.ajax.reload();
            loadSummary();
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
                            profit_loss_account_table.ajax.reload();
                        },
                    });
                }
            });
        })
</script>
@endsection