@extends('layouts.app')
@section('title', __('salesdiscounts::lang.salesdiscounts'))

@section('content')
<!-- Main content -->
<section class="content">
     <div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">Sales Discounts </h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                   <li><a href="#">Sales Discount</a></li>
                    <li><span>List Sales Discount</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>
   <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

        
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('salesdiscounts::lang.date_range') . ':') !!}
                    {!! Form::text('form_16a_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'date_range_list', 'readonly']); !!}
                </div>
            </div>
             <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('location', __('salesdiscounts::lang.location') . ':') !!}
                    {!! Form::select('location', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'id' => 'location', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('salesdiscounts::lang.customer') . ':') !!}
                    {!! Form::select('customer',$customers, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'id' => 'customer', 'placeholder' => __('salesdiscounts::lang.all')]); !!}
                </div>
            </div>
                <div class="col-md-3">
                <div class="form-group">
                  <div class="form-group">
                    {!! Form::label('type', __('salesdiscounts::lang.discount_type') . ':') !!}
                    {!! Form::select('discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'id' => 'discount_type', 'placeholder' => __('salesdiscounts::lang.all')]); !!}
                </div>
                 
                </div>
            </div>

            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
  
   
            <div class="col-md-12">
                <div class="row" style="margin-top: 20px;">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped" id="sales_discount_list_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang('salesdiscounts::lang.transaction_date')</th>
                                    <th>@lang('salesdiscounts::lang.location')</th>
                                    <th>@lang('salesdiscounts::lang.invoice_no')</th>
                                    <th>@lang('salesdiscounts::lang.customer')</th>
                                    <th>@lang('salesdiscounts::lang.discount_type')</th>
                                    <th>@lang('salesdiscounts::lang.discount_amount')</th>
                                    <th>@lang('salesdiscounts::lang.added_by')</th>
                                    <th>@lang('salesdiscounts::lang.action')</th>
                                </tr>
                            </thead>
                            
                        </table>
                    </div>
                </div>
              
            </div>

            @endcomponent
        </div>
    </div>
<div class="modal fade sales_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <!-- <div class="modal fade sales_modal_edit" role="dialog" aria-labelledby="gridSystemModalLabel">-->
    <!--</div>-->
</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
   
    $(document).ready(function(){
        $(document).on('click', '#add_fleet_btn', function(){
        $('.subscription_modal').modal({
            backdrop: 'static',
            keyboard: false
        })
    });
  $(document).ready(function() {
    $(document).on('click', '.btn-modal', function() {
        var targetModal = $(this).data('container');
        $(targetModal).modal({
            backdrop: 'static',
            keyboard: false
        });
    });
});

//
 $('#date_range_list').daterangepicker();
        if ($('#date_range_list').length == 1) {
            $('#date_range_list').daterangepicker(dateRangeSettings, function(start, end) {
                $('#date_range_list').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
            });
            $('#date_range_list').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#date_range_list')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#date_range_list')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
    
        $('#date_range_list').change(function(){
            console.log("discountccc");
           sales_discount_table.ajax.reload();
        });
        
        $('#date_range_list').on('change', function() {
            sales_discount_table.ajax.reload();
        });
        // 
         $('#location').on('change', function() {
            sales_discount_table.ajax.reload();
        });
          $('#customer').on('change', function() {
            sales_discount_table.ajax.reload();
        });
          $('#discount_type').on('change', function() {
            sales_discount_table.ajax.reload();
        });
        
           sales_discount_table = $('#sales_discount_list_table').DataTable({
    processing: true,
    serverSide: false,
    ajax: {
        url: '/sales_discounts/get-Sales-Discount-List',
        data: function(d) {
            let location_id = $('select[id="location"] option:selected').val();
             let customer_id = $('select[id="customer"] option:selected').val();
             let discount_type = $('select[id="discount_type"] option:selected').text();
            let dateRange = $('#date_range_list').val();
            let [start, end] = dateRange.split(' - ');
            d.start_date = start;
            d.location_id=location_id;
            d.customer_id=customer_id;
            d.discount_type=discount_type;
            d.end_date = end;
             console.log(start);
              console.log(end);
            return d;
        }
    },
    columns: [
        { data: 'transaction_date', name: 'transaction_date' },
        { data: 'location_name', name: 'location_name' },
        { data: 'invoice_no', name: 'Invoice No' },
        { data: 'customer_name', name: 'customer_name' },
        { data: 'discount_type', name: 'discount_type' },
        { data: 'discount_amount', name: 'discount_amount' },
        { data: 'created_by', name: 'created_by' },
        { data: 'action', name: 'action' },
    ],
    fnDrawCallback: function(oSettings) {

    }
});
  

      

// Add event listener to the date range input field
 
    //form 15a9ab
   
});
</script>
@endsection