@extends('layouts.app')
@section('title', __('fleet::lang.view_helper'))

@section('content')
<style>
    .select2{
        width: 100% !important;
    }
</style>
<!-- Content Header (Page header) -->
<section class="content-header no-print">
    <h1>{{ __('fleet::lang.view_helper') }}</h1>
</section>

<!-- Main content -->
<section class="content no-print">
    <div class="row">
        <div class="col-md-4 col-xs-12">
            {!! Form::select('fleet_id', $helper_dropdown, $helper->id , ['class' => 'form-control select2', 'id' =>
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
                                @include('fleet::settings.helpers.partials.ledger_tab')
                            </div>
                        </div>
                    </div>
                   
                </div>
            </div>
        </div>
    </div>
</section>

@endsection

@section('javascript')
<script>
     $('#fleet_id').change( function() {
        if ($(this).val()) {
            window.location = "{{url('/fleet-management/helpers')}}/" + $(this).val()+"?tab={{$view_type}}";
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
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@fetchLedgerSummarised')}}',
                data: {
                    contact_id : "{{$contact_id}}",
                    contact_type : "helper",
                    type : $("#ledger_transaction_type").val(),
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
        updateSummary()
       fleet_ledger_table = $('#fleet_ledger_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'asc']],
            ajax: {
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteOperationController@fetchLedger')}}',
                data: function (d) {
                    d.contact_id = "{{$contact_id}}";
                    d.contact_type = "helper";
                    d.type = $("#ledger_transaction_type").val();
                    
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
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'operation_date', name: 'operation_date' },
                { data: 'description', name: 'description' },
                { data: 'amount', name: 'amount' },
                { data: 'amount_paid', name: 'amount_paid' },
                { data: 'balance', name: 'balance' },
                { data: 'payment_method', name: 'payment_method' }
                
                
               
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        $(document).on('change', '#ledger_transaction_type', function() {
            fleet_ledger_table.ajax.reload();
        });

        
    })
    
    
</script>
    
@endsection