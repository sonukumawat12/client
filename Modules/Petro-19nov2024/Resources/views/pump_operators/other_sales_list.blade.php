@extends('layouts.'.$layout)
@section('title', __('petro::lang.list_other_sales'))

@section('content')
<!-- Content Header (Page header) -->
<section class="content-header">
    
    <div class="col-md-12">
        <h1 class="pull-left">@lang('petro::lang.list_other_sales')</h1>
        <h2 style="color: red; text-align: center;">Shift_NO: {{$shift_number}}</h2>
    </div>
    
    <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-lg pull-right"
    style=" background-color: orange; color: #fff; margin-left: 5px;">@lang('petro::lang.logout')</a>
    <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"
        class="btn btn-flat btn-lg pull-right"
        style="color: #fff; background-color:#810040;">@lang('petro::lang.dashboard')
    </a>

    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="@if(empty($only_pumper)) col-md-3 @else col-md-6 @endif px-4" >
                <div class="form-group">
                    {!! Form::label('shift_id',  __('petro::lang.shift') . ':') !!}
                    <select class="form-control select2" style = 'width:100%' id="pump_operators_list_other_sales_shift_id">
                        @foreach($shifts as $shift)
                            <option value="{{$shift->id}}">{{$shift->name." (".@format_date($shift->shift_date)}} to {{!empty($shift->closed_time) ? @format_datetime($shift->closed_time) : 'Open'}})</option>
                        @endforeach
                    </select>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' =>
        __('petro::lang.list_other_sales')])
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="pump_operators_list_other_sales_table"
                style="width: 100%;">
                <thead>
                    <tr>
                        <th>@lang('petro::lang.date')</th>
                        <th>@lang('petro::lang.product')</th>
                        <th>@lang('petro::lang.price')</th>
                        <th>@lang('petro::lang.quantity')</th>
                        <th>@lang('petro::lang.balance_stock')</th>
                        <th>@lang('petro::lang.discount')</th>
                        <th>@lang('petro::lang.sub_total')</th>
                        <th>@lang('petro::lang.shift_number')</th>
                    </tr>
                </thead>
    
                <tfoot>
                    <tr class="bg-gray font-17 footer-total">
                        <td colspan="6" class="text-right" style="color:brown">
                            <strong>@lang('sale.total'):</strong></td>
                        <td style="color:brown"><span class="display_currency" id="footer_list_other_sales_amount" data-currency_symbol="true"></span>
                    </tr>
                </tfoot>
            </table>
        </div>
    @endcomponent
</section>
<div class="clearfix"></div>



@endsection
@section('javascript')
<script type="text/javascript">
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
    
$(document).ready( function(){
    pump_operators_list_other_sales_table = $('#pump_operators_list_other_sales_table').DataTable({
        processing: true,
        serverSide: true,
        aaSorting: [[0, 'desc']],
        ajax: {
            url: "{{action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@otherSalesList')}}",
            data: function(d) {
                d.shift_id = $("#pump_operators_list_other_sales_shift_id").val();
            },
        },
        columnDefs: [ {
            "targets": 0,
            "orderable": false,
            "searchable": false
        }],
        columns: [
            { 
                data: 'created_at',
                name: 'created_at'
            },
            { 
                data: 'product_name',
                name: 'products.name'
            },
            { 
                data: 'price',
                name: 'price',
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).addClass('text-right');
                }
            },
            { 
                data: 'quantity',
                name: 'quantity',
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).addClass('text-right');
                }
            },
            { 
                data: 'balance_stock',
                name: 'balance_stock',
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).addClass('text-right');
                }
            },
            { 
                data: 'discount',
                name: 'discount',
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).addClass('text-right');
                }
            },
            { 
                data: 'sub_total',
                name: 'sub_total',
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).addClass('text-right');
                }
            },
            { 
                data: 'shift_number',
                name: 'shift_number',
                createdCell: function(td, cellData, rowData, row, col) {
                    $(td).addClass('text-right');
                }
            }
        ],
        fnDrawCallback: function(oSettings) {
            var footer_list_other_sales_amount = sum_table_col($('#pump_operators_list_other_sales_table'), 'sub_total');
            $('#footer_list_other_sales_amount').text(footer_list_other_sales_amount);
          
            __currency_convert_recursively($('#pump_operators_list_other_sales_table'));
        },
    });
    
     $(document).on('change', '#pump_operators_list_other_sales_shift_id', function(){
        pump_operators_list_other_sales_table.ajax.reload();
    });
});

</script>
@endsection