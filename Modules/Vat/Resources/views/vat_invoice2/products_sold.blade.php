@extends('layouts.app')
@section('title', __('vat::lang.vat_products_sold'))

@section('content')

@php
    $business_id = request()->session()->get('business.id');
    $tax_rate = \App\TaxRate::where('business_id',$business_id)->first();
    $tax = !empty($tax_rate) ? $tax_rate->amount : 0;
@endphp
<!-- Main content -->
<section class="content">

    <div class="row">
    @include('vat::vat_invoice2.partials.nav')
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('product_id',  __('vat::lang.product') . ':') !!}
                        {!! Form::select('product_id', $products, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        {!! Form::label('customer_id',  __('vat::lang.customer') . ':') !!}
                        {!! Form::select('customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]) !!}
        
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                    <!-- D 81 Added some code here-->
                        {!! Form::label('date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range',  @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month')  , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent
    
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'vat::lang.vat_products_sold')])
        
            
            <div class="row">
                <div class="col-md-12">
                    <table class="table table-striped table-bordered" id="vat_products_solds_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang( 'vat::lang.date' )</th>
                                <th>@lang( 'vat::lang.vat_invoice_no' )</th>
                                <th>@lang( 'vat::lang.customer' )</th>
                                <th>@lang( 'vat::lang.product' )</th>
                                <th>@lang('vat::lang.unit_price_before_vat')</th>
                                <th>@lang('vat::lang.total_discount')</th>
                                <th>@lang( 'vat::lang.qty' )</th>
                                <th>@lang('vat::lang.total_vat') {{$tax}} %</th>
                                <th>@lang( 'vat::lang.total_amount' )</th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade issue_bill_customer_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script>
$('#date_range').daterangepicker(
        dateRangeSettings,
        function (start, end) {
            $('#date_range').val(start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format));
           vat_products_solds_table.ajax.reload();
        }
    );
    $('#date_range').on('cancel.daterangepicker', function(ev, picker) {
        $('#date_range').val('');
        vat_products_solds_table.ajax.reload();
    });
    //D 81 Added the following two line of code
    $('#date_range').data('daterangepicker').setStartDate(moment().startOf('month'));
    $('#date_range').data('daterangepicker').setEndDate(moment().endOf('month'));
   


    // vat_products_solds_table
        vat_products_solds_table = $('#vat_products_solds_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@productsSold')}}",
                data: function(d) {
                    d.customer_id = $("#customer_id").val();
                    d.product_id = $("#product_id").val();
                    
                    if($('#date_range').val()) {
                        var start = $('#date_range').data('daterangepicker').startDate.format('YYYY-MM-DD');
                        var end = $('#date_range').data('daterangepicker').endDate.format('YYYY-MM-DD');
                        d.start_date = start;
                        d.end_date = end;
                    }
                },
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                {data: 'date', name: 'date'},
                {data: 'customer_bill_no', name: 'customer_bill_no'},
                {data: 'customer_name', name: 'contacts.name'},
                
                {data: 'product_name', name: 'products.name'},
                
                {data: 'unit_price_before_tax', name: 'unit_price_before_tax'},
                {data: 'total_discount', name: 'total_discount'},
                
                {data: 'qty', name: 'qty'},
                
                {data: 'total_vat', name: 'total_vat'},
                {data: 'sub_total', name: 'sub_total'},
            ],
            "fnDrawCallback": function (oSettings) {
            }
        });
        
        $(document).on('change','#customer_id,#product_id',function(){
           vat_products_solds_table.ajax.reload(); 
        });
</script>
@endsection