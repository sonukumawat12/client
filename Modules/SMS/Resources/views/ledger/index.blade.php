@extends('layouts.app')

@section('title', __('sms::lang.sms_ledger'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
        @component('components.widget', ['class' => 'box'])
            
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ledger_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('ledger_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly', 'id' => 'ledger_date_range_new']); !!}
                    </div>
                </div>
                
                {{-- <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('ledger_transaction_type', __('lang_v1.transaction_type') . ':') !!}
                        {!! Form::select('ledger_transaction_type', ['debit' => 'Debit', 'credit' => 'Credit'], null, ['placeholder' => __('lang_v1.please_select'), 'style' => 'width: 100%', 'class' => 'form-control select2', 'readonly', 'id' => 'ledger_transaction_type']); !!}
                    </div>
                </div> --}}
           
            </div>
            
            @endcomponent
        </div>
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box'])
            <div id="contact_ledger_div"></div>
            @endcomponent
        </div>
    </div>
@endsection

@section('javascript')
<script>
$(document).ready(function(){
if ($('#ledger_date_range_new').length == 1) {
            $('#ledger_date_range_new').daterangepicker(dateRangeSettings, function(start, end) {
                $('#ledger_date_range_new').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
                
                get_contact_ledger();
                
            });
            $('#ledger_date_range_new').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#ledger_date_range_new')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#ledger_date_range_new')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        
        $('#ledger_date_range_new, #ledger_transaction_amount, #ledger_transaction_type').change( function(){
            get_contact_ledger();
        });
        get_contact_ledger();
        
        function get_contact_ledger() {
            var start_date = '';
            var end_date = '';
            var transaction_type = $('select#ledger_transaction_type').val();
            
            if($('#ledger_date_range_new').val()) {
                start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
                end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
            
            $('#contact_ledger_div').empty();
            
            $.ajax({
                url: '/sms/ledger?start_date=' + start_date + '&transaction_type=' + transaction_type + '&end_date=' + end_date,
                dataType: 'html',
                success: function(result) {
                    $('#contact_ledger_div')
                        .html(result);
                    $('#ledger_table').DataTable({
                        searching: true,
                        ordering:false,
                        paging:true,
                        // dom: 't'
                    });
                },
            });
        }
        
})
</script>
@endsection