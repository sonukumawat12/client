@extends('layouts.app')

@section('title', __('vat::lang.vat_report_ledger'))

@section('content')
<!-- Main content -->
<section class="content">


<div class="row">
    <div class="col-sm-12">
            <button type="button" class="btn btn-primary btn-sm pull-right" id="add_penalty" data-toggle="modal" >@lang('vat::lang.add_penalty')</button>
        </div>
        
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
                {!! Form::label('ledger_tax_type', __('vat::lang.tax_type') . ':') !!}
                {!! Form::select('ledger_tax_type', ['input' => __('vat::lang.input_tax'), 'output' => __('vat::lang.output_tax')], null, ['placeholder' => __('lang_v1.please_select'), 'style' => 'width: 100%', 'class' => 'form-control select2', 'readonly', 'id' => 'ledger_tax_type']); !!}
            </div>
        </div> --}}
        
       
        </div>
        
        @endcomponent
    </div>
    <div class="col-md-12">
        <hr>
        @component('components.widget', ['class' => 'box'])
        <div id="contact_ledger_div"></div>
        @endcomponent
    </div>
</div>

<div class="modal fade" id="add_penalty_modal" tabindex="-1" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            {!! Form::open(['url' => action('\Modules\Vat\Http\Controllers\VatPenaltyController@store'), 'method' => 'post', 'id' => 'add_penalty_form' ]) !!}
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('vat::lang.add_penalty')</h4>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    {!! Form::label('penalty_date', __( 'lang_v1.date' ) . ':*') !!}
                      {!! Form::text('date', null, ['class' => 'form-control', 'required','readonly', 'placeholder' => __( 'lang_v1.date' ), 'id' => 'penalty_date']); !!}
                </div>
                
                <div class="form-group">
                    {!! Form::label('amount', __( 'sale.amount' ) . ':*') !!}
                      {!! Form::text('amount', null, ['class' => 'form-control input_number', 'required', 'placeholder' => __( 'sale.amount' ) ]); !!}
                </div>

               
                <div class="form-group">
                    {!! Form::label('customer_id',  __('vat::lang.customer') . ':') !!}
                    {!! Form::select('customer_id', $customers, null, ['id'=>'customer_id','class' => 'form-control select2','required', 'style' => 'width:100%', 'placeholder' => __('lang_v1.please_select')]); !!}
                </div>
                
                <div class="form-group">
                    {!! Form::label('note', __( 'brand.note' ) . ':') !!}
                      {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => __( 'brand.note'), 'rows' => 3 ]); !!}
                </div>
            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary">@lang( 'messages.submit' )</button>
                <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
            </div>
            {!! Form::close() !!}
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->   
</div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
@if(!empty(session('status')) && empty(session('status')['success']))
    <script>
        toastr.error('{{session('status')['msg']}}');
    </script>
    
@endif 
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
        
        $('#ledger_date_range_new, #ledger_customer_id, #ledger_tax_type').change( function(){
            get_contact_ledger();
        });
        get_contact_ledger();
        
        function get_contact_ledger() {
            var start_date = '';
            var end_date = '';
            var tax_type = $('select#ledger_tax_type').val();
            
            
            
            if($('#ledger_date_range_new').val()) {
                start_date = $('#ledger_date_range_new').data('daterangepicker').startDate.format('YYYY-MM-DD');
                end_date = $('#ledger_date_range_new').data('daterangepicker').endDate.format('YYYY-MM-DD');
            }
            $.ajax({
                url: '/vat-module/reports-get-ledger?start_date=' + start_date + '&tax_type=' + tax_type  + '&end_date=' + end_date,
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
    
    $(document).on('click', '#add_penalty', function(e) {

        e.preventDefault();
        $('#penalty_date,#amount,#note').val('');
        $('#add_penalty_modal').modal('show');
    });
     //Date picker
    $('#penalty_date').datetimepicker({
        format: moment_date_format + ' ' + moment_time_format,
        ignoreReadonly: true,
    });
    
    $(document).on('submit', 'form#add_penalty_form', function(e) {
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
                    $('div#add_penalty_modal').modal('hide');
                    toastr.success(result.msg);
                    form[0].reset();
                    form.find('button[type="submit"]').removeAttr('disabled');
                    get_contact_ledger();
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    });
    
</script>
@endsection

