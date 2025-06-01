@extends('layouts.app')
@section('title', __('Stocktaking::lang.form_set_1'))

@section('content')
<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if(auth()->user()->can('f9c_form'))
                    <li class="active">
                        <a href="#9c_form_tab" class="9c_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('Stocktaking::lang.9c_form')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('f15a9abc_form'))
                    <li class="">
                        <a href="#15a9ab_form_tab" class="15a9ab_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('Stocktaking::lang.15a9ab_form')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('f16a_form'))
                    <li class="">
                        <a href="#16A_form_tab" class="16A_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('Stocktaking::lang.16A_form')</strong>
                        </a>
                    </li>
                    @endif
                    @if(auth()->user()->can('f21c_form'))
                    <li class="">
                        <a href="#21c_form_tab" class="21c_form_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('Stocktaking::lang.21c_form')</strong>
                        </a>
                    </li>
                    @endif
                </ul>
                <div class="tab-content">
                    @if(auth()->user()->can('f9c_form'))
                    <div class="tab-pane active" id="9c_form_tab">
                        @include('Stocktaking::forms.partials.9c_form')
                    </div>
                    @endif
                    @if(auth()->user()->can('f15a9abc_form'))
                    <div class="tab-pane" id="15a9ab_form_tab">
                        @include('Stocktaking::forms.partials.15a9ab_form')
                    </div>
                    @endif
                    @if(auth()->user()->can('f16a_form'))
                    <div class="tab-pane" id="16A_form_tab">
                        @include('Stocktaking::forms.partials.16a_form')
                    </div>
                    @endif
                    @if(auth()->user()->can('f21c_form'))
                    <div class="tab-pane" id="21c_form_tab">
                        @include('Stocktaking::forms.partials.21c_form')
                    </div>
                    @endif

                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">
    $('#form_9C_location_id option:eq(1)').attr('selected', true);
    $('#f15a9ab_location_id option:eq(1)').attr('selected', true);
    $('#16a_location_id option:eq(1)').attr('selected', true);
    $('#f21c_location_id option:eq(1)').attr('selected', true);

    $(document).ready(function(){

    //form 9c section
    $('#form_9C_date_range').daterangepicker();
        if ($('#form_9C_date_range').length == 1) {
            $('#form_9C_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#form_9C_date_range').val(
                    start.format(moment_date_format) + ' ~ ' + end.format(moment_date_format)
                );
            });
            $('#form_9C_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#form_9C_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#form_9C_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
    
        $('#form_9C_date_range, #form_9C_location_id').change(function(){
            get9CForm();
        });
        get9CForm();
        function get9CForm(){
            var start_date = $('input#form_9C_date_range')
                    .data('daterangepicker')
                .startDate.format('YYYY-MM-DD');
            var end_date = $('input#form_9C_date_range')
                .data('daterangepicker')
                .endDate.format('YYYY-MM-DD');
            start_date = start_date;
            end_date = end_date;
            location_id = $('#form_9C_location_id').val();

            $.ajax({
                method: 'get',
                url: '/Stocktaking/get-9c-form',
                data: {
                    start_date,
                    end_date,
                    location_id
                },
                contentType: 'html',
                success: function(result) {
                    $('#9c_details_section').empty().append(result);
                    get_this_page_total_9c();
                    get_previous_value_9c();
                },
            });
        }
    
    function get_previous_value_9c(){
        var start_date = $('input#form_9C_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#form_9C_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        var  location_id = $('#form_9C_location_id').val();

        $.ajax({
            method: 'get',
            url: '/Stocktaking/get_previous_value_9c',
            data: { start_date, end_date, location_id},
            success: function(result) {
                let pre_total_amount = 0;
                Object.keys(result).forEach((i) => {
                    pre_total_amount += parseFloat(result[i].amount); 
                    $('#footer_f9c_qty_pre_page_'+i).text(__number_f(result[i].qty, false, false, __currency_precision));
                    $('#footer_f9c_amount_pre_page_'+i).text(__number_f(result[i].amount, false, false, __currency_precision));
                    
                });
                $('#footer_f9c_total_amount_pre_page').text(__number_f(pre_total_amount, false, false, __currency_precision));

                @foreach($sub_categories as $sub_cat)
                var this_page_qty_{{$sub_cat->id}} = __read_number_from_text($('#footer_f9c_qty_this_page_{{$sub_cat->id}}'));
                var this_page_amount_{{$sub_cat->id}} = __read_number_from_text($('#footer_f9c_amount_this_page_{{$sub_cat->id}}'));
                
                var pre_page_qty_{{$sub_cat->id}} = __read_number_from_text($('#footer_f9c_qty_pre_page_{{$sub_cat->id}}'));
                var pre_page_amount_{{$sub_cat->id}} = __read_number_from_text($('#footer_f9c_amount_pre_page_{{$sub_cat->id}}'));

                var grand_total_qty_{{$sub_cat->id}} = this_page_qty_{{$sub_cat->id}} + pre_page_qty_{{$sub_cat->id}};
                var grand_total_amount_{{$sub_cat->id}} = this_page_amount_{{$sub_cat->id}} + pre_page_amount_{{$sub_cat->id}};
               
                $('#footer_f9c_qty_grand_{{$sub_cat->id}}').text(__number_f(grand_total_qty_{{$sub_cat->id}}, false, false, __currency_precision));
                $('#footer_f9c_amount_grand_{{$sub_cat->id}}').text(__number_f(grand_total_amount_{{$sub_cat->id}}, false, false, __currency_precision));
                @endforeach
                
                var footer_f9c_total_amount_this_page = __read_number_from_text($('#footer_f9c_total_amount_this_page'));
                var footer_f9c_total_amount_pre_page = __read_number_from_text($('#footer_f9c_total_amount_pre_page'));
               
                
                $('#footer_f9c_total_amount_grand').text(__number_f(footer_f9c_total_amount_this_page + footer_f9c_total_amount_pre_page , false, false, __currency_precision));
            },
        });
    }

    function get_this_page_total_9c(){
        @foreach ($sub_categories as $item)
        var total_qty_{{$item->id}} = sum_table_col($('#form_9c_table'), '{{$item->id}}_qty');
        $('#footer_f9c_qty_this_page_{{$item->id}}').text(__number_f(total_qty_{{$item->id}} , false, false, __currency_precision));
        var total_amount_{{$item->id}} = sum_table_col($('#form_9c_table'), '{{$item->id}}_amount');
        $('#footer_f9c_amount_this_page_{{$item->id}}').text(__number_f(total_amount_{{$item->id}} , false, false, __currency_precision));
        @endforeach
        var total_amount = sum_table_col($('#form_9c_table'), 'total_amount');
        $('#footer_f9c_total_amount_this_page').text(__number_f(total_amount , false, false, __currency_precision));
    }


    //form 16a section
    $('#form_16a_date_range').daterangepicker();
        if ($('#form_16a_date_range').length == 1) {
            $('#form_16a_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#form_16a_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#form_16a_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#form_16a_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#form_16a_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        
        let date = $('#form_16a_date_range').val().split(' - ');
        
        $('.from_date').text(date[0]);
        $('.to_date').text(date[1]);
    

     //form_16a_table 
     form_16a_table = $('#form_16a_table').DataTable({
        processing: true,
        serverSide: true,
        paging: false, 
        ajax: {
            url: '/Stocktaking/get-form-16a',
            data: function(d) {
                var start_date = $('input#form_16a_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                var end_date = $('input#form_16a_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.start_date = start_date;
                d.end_date = end_date;
                d.location_id = $('#16a_location_id').val();
            }
        },
        columns: [
            { data: 'index_no', name: 'index_no' },
            { data: 'product', name: 'product' },
            { data: 'location', name: 'location' },
            { data: 'received_qty', name: 'received_qty' },
            { data: 'unit_purchase_price', name: 'unit_purchase_price' },
            { data: 'total_purchase_price', name: 'total_purchase_price' },
            { data: 'unit_sale_price', name: 'unit_sale_price' },
            { data: 'total_sale_price', name: 'total_sale_price' },
            { data: 'reference_no', name: 'reference_no' },
            { data: 'stock_book_no', name: 'stock_book_no' },
        ],
        fnDrawCallback: function(oSettings) {
            caculateF16AFromTotal();
            get_previous_value_16a();
        },
    });

    let selected_date = date[0];
    var F16A_this_total = [];
    var F16A_pre_total = [];
    var F16A_grand_total = [];

    function caculateF16AFromTotal(){
        
        var total_purchase_price = @if($setting['F16A_first_day_after_stock_taking'] == 1) 0 @else sum_table_col($('#form_16a_table'), 'total_purchase_price') @endif;
        $('#footer_F16A_total_purchase_price').text(__number_f(total_purchase_price, false, false, __currency_precision));
        var total_sale_price =  @if($setting['F16A_first_day_after_stock_taking'] == 1) 0 @else sum_table_col($('#form_16a_table'), 'total_sale_price') @endif;
        $('#footer_F16A_total_sale_price').text(__number_f(total_sale_price, false, false, __currency_precision));
        $('#total_this_p').val(total_purchase_price);
        $('#total_this_s').val(total_sale_price);
    }

    $('#form_16a_date_range, #16a_location_id').change(function(){
        form_16a_table.ajax.reload();
        if($('#16a_location_id').val() !== ''  && $('#16a_location_id').val() !== undefined){
            $('.f16a_location_name').text($('#16a_location_id :selected').text())
        }else{
            $('.f16a_location_name').text('All')
        }
    });

    function get_previous_value_16a(){
        var start_date = $('input#form_16a_date_range')
            .data('daterangepicker')
            .startDate.format('YYYY-MM-DD');
        var end_date = $('input#form_16a_date_range')
            .data('daterangepicker')
            .endDate.format('YYYY-MM-DD');
        var  location_id = $('#16a_location_id').val();

        $.ajax({
            method: 'get',
            url: '/Stocktaking/get_previous_value_16a',
            data: { start_date, end_date, location_id},
            success: function(result) {
                
               let footer_total_purchase_price = __read_number($('#total_this_p'));
               let footer_total_sale_price = __read_number($('#total_this_s'));
               
               $('#pre_F16A_total_purchase_price').text(__number_f(result.pre_total_purchase_price, false, false, __currency_precision))
               $('#pre_F16A_total_sale_price').text(__number_f(result.pre_total_sale_price, false, false, __currency_precision))
               let grand_total_purchase_price = footer_total_purchase_price + parseFloat(result.pre_total_purchase_price);
               let grand_total_sale_price = footer_total_sale_price + parseFloat(result.pre_total_sale_price);
               $('#grand_F16A_total_purchase_price').text(__number_f(grand_total_purchase_price, false, false, __currency_precision))
               $('#grand_F16A_total_sale_price').text(__number_f(grand_total_sale_price, false, false, __currency_precision))
                
            },
        });
    }

    //form 21C
    $('#form_21c_date_range').daterangepicker();
    if ($('#form_21c_date_range').length == 1) {
        $('#form_21c_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#form_21c_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#form_21c_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#form_21c_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#form_21c_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    
    let f21c_date = $('#form_21c_date_range').val().split(' - ');
    
    $('.21c_from_date').text(f21c_date[0]);
    $('.21c_to_date').text(f21c_date[1]);

    if($('#f21c_location_id').val() !== ''  && $('#f21c_location_id').val() !== undefined){
        $('.f21c_location_name').text($('#f21c_location_id :selected').text())
    }else{
        $('.f21c_location_name').text('All')
    }

    $('#form_21c_date_range, #f21c_location_id').change(function(){
        let f21c_date = $('#form_21c_date_range').val().split(' - ');
        $('.21c_from_date').text(f21c_date[0]);
        $('.21c_to_date').text(f21c_date[1]);

        if($('#f21c_location_id').val() !== ''  && $('#f21c_location_id').val() !== undefined){
            $('.f21c_location_name').text($('#f21c_location_id :selected').text())
        }else{
            $('.f21c_location_name').text('All')
        }

        get21cDetails();
    });

    function get21cDetails(){
        var start_date = $('input#form_21c_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end_date = $('input#form_21c_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        location_id = $('#f21c_location_id').val();

        $.ajax({
            method: 'get',
            url: '/Stocktaking/get-21c-form',
            data: {
                start_date,
                end_date,
                location_id
            },
            contentType: 'json',
            success: function(result) {
                var credit_sales = result.credit_sales; 
                var merged_sub_categories = result.merged_sub_categories;
                var form22 = result.form22_details;
                var form17_increase = result.form17_increase;
                var form17_decrease = result.form17_decrease;
                var previous_credit_sales = result.previous_credit_sales;
                var transaction = result.transaction;
                var previous_transaction = result.previous_transaction;
                var credit_sales_transaction = result.credit_sales_transaction;
                var previous_credit_sales_transaction = result.previous_credit_sales_transaction;
                var own_group = result.own_group;
                var previous_own_group = result.previous_own_group;
                var form17_increase_previous = result.form17_increase_previous;
                var form17_decrease_previous = result.form17_decrease_previous;
                // console.log(result);

                if(credit_sales.length > 0) {
                    for(var i = 0; i <= credit_sales.length; i++){
                        for(var j = 0; j <= merged_sub_categories.length; j++){
                            var sub_categories = Object.values(merged_sub_categories[j].sub_categories);
                            for(var sub in sub_categories) {
                                if(sub_categories[sub] == credit_sales[i].sub_category_id) {
                                    var amount = credit_sales[i].quantity * credit_sales[i].unit_price;
                                    $('#today_receipt_qty').val(credit_sales[i].quantity);
                                    $('#today_receipt_value').val(amount);
                                }
                                var total_receipt = credit_sales[i].quantity * credit_sales[i].unit_price;
                                $('.total_today_receipt').val(total_receipt);
                            }
                        }
                    }
                }

                if(previous_credit_sales.length > 0) {
                    for(var i = 0; i <= previous_credit_sales.length; i++){
                        var total_previous = previous_credit_sales[i].quantity * previous_credit_sales[i].unit_price;
                        $('#total_previous_1').val(total_previous);
                        $('#total_previous_2').val(total_previous);
                    }
                }

                $('#total_qty').val($('#total_previous_1').val() +  $('#today_receipt_qty').val());
                $('#total_value').val($('#total_previous_2').val() +  $('#today_receipt_value').val());
                $('#open_stock_qty').val(form22.difference_qty);
                $('#open_stock_value').val(form22.unit_sale_price);
                $('#price_increment_qty').val(form17_increase.current_stock);
                $('#price_increment_value').val(form17_increase.unit_price);
                $('#cash_today_qty').val(transaction.quantity);
                $('#cash_today_value').val(transaction.unit_price);
                $('#credit_today_qty').val(credit_sales_transaction.quantity);
                $('#credit_today_value').val(credit_sales_transaction.unit_price);
                $('#own_today_qty').val(own_group.quantity);
                $('#own_today_value').val(own_group.unit_price);
                $('#price_increment_pre_qty').val(form17_increase_previous.quantity);
                $('#price_increment_pre_value').val(form17_increase_previous.unit_price);
                $('#price_increment_total_qty').val($('#price_increment_qty').val() + $('#price_increment_pre_qty').val());
                $('#price_increment_total_value').val($('#price_increment_value').val() + $('#price_increment_pre_value').val());
                $('#total_receipt_to_date_qty').val($('#total_qty').val() + $('#open_stock_qty').val() + $('#price_increment_pre_qty').val());
                $('#total_receipt_to_date_value').val($('#total_value').val() + $('#open_stock_value').val());
                $('#total_today_qty').val($('#cash_today_qty').val() + $('#credit_today_qty').val() + $('#own_today_qty').val());
                $('#total_today_value').val($('#cash_today_value').val() + $('#credit_today_value').val() + $('#own_today_value').val());
                $('#total_previous_qty').val(previous_transaction.quantity + previous_credit_sales_transaction.quantity + previous_own_group.quantity);
                $('#total_previous_value').val(previous_transaction.unit_price + previous_credit_sales_transaction.unit_price + previous_own_group.unit_price);
                $('#final_balance_qty').val($('#total_receipt_to_date_qty').val());
                $('#final_balance_value').val($('#total_receipt_to_date_value').val());
                $('#total_as_of_today_value').val($('#total_today_value').val());
                $('#price_reduction_today_qty').val(form17_decrease.current_stock);
                $('#price_reduction_today_value').val(form17_decrease.unit_price);
                $('#total_receipts_todate_qty').val($('#total_receipt_to_date_qty').val());
                $('#total_receipts_todate_value').val($('#total_today_value').val());
                $('#total_1_qty').val($('#total_today_qty').val() + $('#total_previous_qty').val());
                $('#total_1_value').val($('#total_today_value').val() + $('#total_previous_value').val());
                $('#total_2_qty').val($('#total_previous_qty').val());
                $('#total_2_value').val($('#total_previous_value').val());
                $('#total_as_of_today').val($('#total_receipt_to_date_qty').val() - $('#total_1_qty').val());
                $('#price_reduction_pre_day_qty').val(form17_decrease_previous.current_stock);
                $('#price_reduction_pre_day_value').val(form17_decrease_previous.unit_price);
            }
        });
    }

    //form 15a9ab
    $('#form_15a9ab_date_range').daterangepicker();
    if ($('#form_15a9ab_date_range').length == 1) {
        $('#form_15a9ab_date_range').daterangepicker(dateRangeSettings, function(start, end) {
            $('#form_15a9ab_date_range').val(
                start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
            );
        });
        $('#form_15a9ab_date_range').on('cancel.daterangepicker', function(ev, picker) {
            $('#product_sr_date_filter').val('');
        });
        $('#form_15a9ab_date_range')
            .data('daterangepicker')
            .setStartDate(moment().startOf('month'));
        $('#form_15a9ab_date_range')
            .data('daterangepicker')
            .setEndDate(moment().endOf('month'));
    }
    
    let f15a9ab_date = $('#form_15a9ab_date_range').val().split(' - ');
    
    $('.15a9ab_from_date').text(f15a9ab_date[0]);
    $('.15a9ab_to_date').text(f15a9ab_date[1]);

    if($('#f15a9ab_location_id').val() !== ''  && $('#f15a9ab_location_id').val() !== undefined){
        $('.f15a9ab_location_name').text($('#f15a9ab_location_id :selected').text())
    }else{
        $('.f15a9ab_location_name').text('All')
    }

    $('#form_15a9ab_date_range, #f15a9ab_location_id').change(function(){
        let f15a9ab_date = $('#form_15a9ab_date_range').val().split(' - ');
        $('.15a9ab_from_date').text(f15a9ab_date[0]);
        $('.15a9ab_to_date').text(f15a9ab_date[1]);

        if($('#f15a9ab_location_id').val() !== ''  && $('#f15a9ab_location_id').val() !== undefined){
            $('.f15a9ab_location_name').text($('#f15a9ab_location_id :selected').text())
        }else{
            $('.f15a9ab_location_name').text('All')
        }

        get15a9abDetails();
    });

    function get15a9abDetails(){
        var start_date = $('input#form_15a9ab_date_range').data('daterangepicker').startDate.format('YYYY-MM-DD'),
        end_date = $('input#form_15a9ab_date_range').data('daterangepicker').endDate.format('YYYY-MM-DD'),
        location_id = $('#f15a9ab_location_id').val();

        $.ajax({
            method: 'get',
            url: '/Stocktaking/F15-9ABC',
            data: {
                start_date,
                end_date,
                location_id
            },
            contentType: 'json',
            success: function(result) {
                var credit_sales = result.credit_sales; 

                if(previous_credit_sales.length > 0) {
                    for(var i = 0; i <= previous_credit_sales.length; i++){
                        $('#previous_opening_balance').val(previous_credit_sales[i].opening_balance);
                    }
                }

                if(credit_sales.length > 0) {
                    for(var i = 0; i <= credit_sales.length; i++){
                        $('#today_opening_balance').val(credit_sales[i].opening_balance); 
                    }
                }

            }
        });
    }
});
</script>
@endsection