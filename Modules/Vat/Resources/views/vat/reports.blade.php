@extends('layouts.app')

@section('title', __('vat::lang.vat_module'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs no-print">
                    @can('tax_report.view')
                    <li class="active">
                        <a href="#tax_report" class="tax_report" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('report.tax_report')</strong>
                        </a>
                    </li>
                    @endcan
                    
                   
                    
                </ul> 
                <div class="tab-content">
                    @can('tax_report.view')
                    <div class="tab-pane active" id="tax_report">
                        @include('vat::vat.vat_reports')
                    </div>
                    @endcan
                    
                    
                </div>
            </div>
        </div>
    </div>
    
    <div class="hide">
        <div id="report_print_div"></div>
    </div>
    
    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div id="settlement_print" class="container"></div>
    
</section>
<!-- /.content -->

@endsection
@section('javascript')
@if(!empty(session('status')) && empty(session('status')['success']))
    <script>
        toastr.error('{{session('status')['msg']}}');
    </script>
    
@endif 
<script src="{{ asset('js/report.js?v=' . $asset_v) }}"></script>
<script>
    $(document).on('click', '.print-report', function(e){
        e.preventDefault();
        href = "/vat-module/print";
        
        var start = $('#tax_report_date_filter').data('daterangepicker').startDate.format('YYYY-MM-DD');
        var end = $('#tax_report_date_filter').data('daterangepicker').endDate.format('YYYY-MM-DD');
        var location_id = $('#tax_report_location_filter').val();
        var contact_id = $("#contact_id").val();
        var reference_type = $("#reference_type").val();
        
        $.ajax({
            method: 'get',
            contentType: 'html',
            url: href,
            data: {
                "contact_id" : contact_id,
                "reference_type" : reference_type,
                "location_id" : location_id,
                "start_date" : start,
                "end_date" : end
            },
            success: function(result) {
                $('#report_print_div').empty().append(result);
                $('#report_print_div').printThis();

            },
        });
    });
    
    
    //save settlement
    $(document).on('click', '.print_settlement_button', function () {
        var url = $(this).data('href');
        $.ajax({
            method: 'get',
            url: url,
            data: {},
            success: function(result) {
                $('#settlement_print').html(result);
    
                var divToPrint=document.getElementById('settlement_print');
    
                var newWin=window.open('','Print-Ledger');
            
                newWin.document.open();
            
                newWin.document.write('<html><body onload="window.print()">'+divToPrint.innerHTML+'</body></html>');
            
                newWin.document.close();
                
            },
        });
    });
    
    $('#settlement_print').css('visibility', 'hidden');
    
    $(document).on('click', '#regenerate_vat', function(e) {

        e.preventDefault();
        $('#transaction_type').val('').trigger('change');
        $('#regenerate_vat_modal').modal('show');
    });
    
    
    $(document).on('submit', 'form#regenerate_vat_form', function(e) {
        e.preventDefault();
        var form = $(this);
        var data = form.serialize();

        $.ajax({
            method: 'POST',
            url: $(this).attr('action'),
            dataType: 'json',
            data: data,
            success: function(result) {
                if (result.success === true) {
                    $('div#regenerate_vat_modal').modal('hide');
                    
                    toastr.success(result.msg);
                    updateTaxReport();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
</script>
@endsection