@extends('layouts.app')
@section('title', __('pricechanges::lang.F17_form'))

@section('content')
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#f17_from_tab" class="f17_from_tab" data-toggle="tab">
                            <i class="fa fa-file-text-o"></i> <strong>@lang('pricechanges::lang.f17_from')</strong>
                        </a>
                    </li>

                    <li>
                        <a href="#list_f17_from_tab" class="list_f17_from_tab" style="" data-toggle="tab">
                            <i class="fa fa-list"></i> <strong>
                                @lang('pricechanges::lang.list_f17_from') </strong>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#price_change_settings_tab" class="price_change_settings_tab" data-toggle="tab">
                            <i class="fa fa-cog"></i> <strong>
                                @lang('pricechanges::lang.price_change_settings') </strong>
                        </a>
                    </li>
                    
                    <li>
                        <a href="#price_changed_details_tab" class="price_changed_details_tab" data-toggle="tab">
                            <i class="fa fa-list"></i> <strong>
                                @lang('pricechanges::lang.price_changed_details') </strong>
                        </a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="f17_from_tab">
                        @include('pricechanges::forms.F17.partials.f17_from')
                    </div>

                    <div class="tab-pane" id="list_f17_from_tab">
                        @include('pricechanges::forms.F17.partials.list_f17_from')
                    </div>
                    
                    <div class="tab-pane" id="price_change_settings_tab">
                        @include('pricechanges::forms.F17.partials.price_change_settings')
                    </div>
                    
                    <div class="tab-pane" id="price_changed_details_tab">
                        @include('pricechanges::forms.F17.partials.price_changed_details')
                    </div>
                </div>
            </div>
        </div>
    </div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script type="text/javascript">

    $('#f17_date').datepicker().datepicker('setDate', 'today');
    $('#price_changed_date_range').datepicker().datepicker('setDate','today');
    $('#location_id option:eq(1)').attr('selected', true);
    $('#list_form_f17_location_id option:eq(1)').attr('selected', true);
    $(document).ready(function(){
        
        form17Table();
        




        $(document).on('keyup', '.new_price_value', function(){
            let tr = $(this).parent().parent();
            let unit_price =  parseFloat(tr.find('.unit_price').data('orig-value'));
            let select_mode =  tr.find('.select_mode').val();
            let current_stock =  parseFloat(tr.find('.current_stock').data('orig-value'));

            price_gain = 0;
            price_loss = 0;
            difference = 0;
            
            

            difference =parseFloat($(this).val()) - unit_price;

            if(difference > 0){
                price_gain =  current_stock * difference;
            }
            if(difference < 0){
                price_loss =  current_stock * difference;
            }
           
          
            tr.find('.price_changed_loss').text(__number_f(Math.abs(price_loss), false, false, __currency_precision));
            tr.find('.price_changed_gain').text(__number_f(price_gain, false, false, __currency_precision));
            tr.find('.unit_price_difference').text(__number_f(difference, false, false, __currency_precision));
            tr.find('.price_changed_gain_value').val(price_gain);
            tr.find('.price_changed_loss_value').val(price_loss);
            tr.find('.unit_price_difference_value').val(difference);
            
            var total_price_gain = 0;
            var total_price_loss = 0;
            
          /*tr.find('.price_changed_gain_value').each(function() {
            total_price_gain += parseFloat(tr.find('.price_changed_gain_value').val());
            total_price_loss += parseFloat(tr.find('.price_changed_loss_value').val());
          });*/
          
            $('.price_changed_gain_value').each(function() {
                total_price_gain += parseFloat($(this).val() ? $(this).val() : 0);
            });
        
            $('.price_changed_loss_value').each(function() {
                total_price_loss += parseFloat($(this).val() ? $(this).val() : 0);
            });
          
         
          $('.price_changed_total_gain_value').text(__number_f(total_price_gain));
          $('.price_changed_total_loss_value').text(__number_f( Math.abs(total_price_loss)));
              

        });

        
        $(document).on('keyup', '.new_sale_price_value', function(){
            
            let tr = $(this).parent().parent();
            let current_sale_price =  parseFloat(tr.find('.current_sale_price').data('orig-value'));
            
            let current_stock =  parseFloat(tr.find('.current_stock').data('orig-value'));
            

            let sale_difference = parseFloat($(this).val()) - current_sale_price;
            sale_difference *= current_stock;
            
            tr.find('.total_sale_difference').text(__number_f(sale_difference, false, false, __currency_precision));
            
            tr.find('.total_sale_difference_value').val(sale_difference);
              

        });
        

        $(document).on('change', '.select_mode', function(){
            let tr = $(this).parent().parent();
            tr.find('.new_price_value').trigger('keyup');
        });

        $('.f17_filter').change(function(){
            form_17_table.ajax.reload();
        });

        $('#f17_save').click(function(e){
            e.preventDefault();
            $(this).attr('disabled', 'disabled');
            $.ajax({
                method: 'post',
                url: '/pricechanges/save-form-f17',
                data: { 
                    data: form_17_table.$('input, select').serialize(), 
                    date : $('#f17_date').val(), 
                    form_no : $('#F17_from_no').val(),
                    location_id : $('#location_id').val(),
                    store_id : $('#store_id').val(),
                    category_id : $('#product_list_filter_category_id').val(),
                    unit_id : $('#product_list_filter_unit_id').val(),
                    brand_id : $('#product_list_filter_brand_id').val(),
                },
                success: function(result) {
                    console.log(result);
                    
                    if(result.success == 0){
                        toastr.error(result.msg);
                        return false;
                    }else{
                        window.location.href = '{{URL::to('/')}}/pricechanges/';
                    }
                    
                },
            });
        });

        $('#location_id').change(function(){
            $.ajax({
                method: 'get',
                url: '/stock-transfer/get_transfer_store_id/'+$('#location_id').val(),
                data: { },
                success: function(result) {
                    $('#store_id').empty();
                    $('#store_id').append(`<option value= "">Please Select</option>`);
                    $.each(result, function(i, location) {
                        $('#store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
                    });
                },
            });
        });
        $('#list_form_f17_location_id').change(function(){
            $.ajax({
                method: 'get',
                url: '/stock-transfer/get_transfer_store_id/'+$('#list_form_f17_location_id').val(),
                data: { },
                success: function(result) {
                    $('#list_store_id').empty();
                    $('#list_store_id').append(`<option value= "">Please Select</option>`);
                    $.each(result, function(i, location) {
                        $('#list_store_id').append(`<option value= "`+location.id+`">`+location.name+`</option>`);
                    });
                },
            });
        });

        $('#from_no_filter').select2();

        $('#list_f17_date_range').daterangepicker();
        if ($('#list_f17_date_range').length == 1) {
            $('#list_f17_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#list_f17_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#list_f17_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#list_f17_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#list_f17_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        
        $('#price_change_settings_table').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: '/pricechanges/list-prices_change_settings/'
        },
        columns: [
            { data: 'date', name: 'date' },
            { data: 'gain_account_name', name: 'gain_account_name' },
            { data: 'loss_account_name', name: 'loss_account_name' },
            { data: 'user', name: 'user' },
            { data: 'edit', name: 'edit'}
          
        ],
        
    }); 
        
        
     //list_form_f17_table 
     list_form_f17_table = $('#list_form_f17_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '/pricechanges/list-F17',
            data: function(d) {
                var start_date = $('input#list_f17_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                var end_date = $('input#list_f17_date_range')
                    .data('daterangepicker')
                    .endDate.format('YYYY-MM-DD');
                d.start_date = start_date;
                d.end_date = end_date;
                d.from_no = $('#from_no_filter').val();
                d.category_id = $('#list_f17_category_id').val();
                d.unit_id = $('#list_f17_unit_id').val();
                d.brand_id = $('#list_f17_brand_id').val();
                d.location_id = $('#list_form_f17_location_id').val();
                d.store_id = $('#list_store_id').val();
            }
        },
        columns: [
            { data: 'action', name: 'action' },
            { data: 'date', name: 'date' },
            { data: 'form_no', name: 'form_no' },
            { data: 'total_price_change_loss', name: 'total_price_change_loss' },
            { data: 'total_price_change_gain', name: 'total_price_change_gain' },
            { data: 'username', name: 'username' },
          
        ],
        fnDrawCallback: function(oSettings) {
         
        },
        
        drawCallback: function () {
             var api = this.api();
             $(".price_changed_gain_value").text("Total Amount: "+ api.column(8, {page: 'current'}).data().sum().toLocaleString());
        },
    });

    $('.list_f17_filter').change(function(){
        list_form_f17_table.ajax.reload();
    })
    

    
        $('#f17_price_change').click(function(e){
            e.preventDefault();
            $(this).attr('disabled', 'disabled');
            $.ajax({
                method: 'post',
                url: '/pricechanges/save-prices_change_settings',
                data: { 
                    price_gain_account_id : $('#income_account_id').val(),
                    price_loss_account_id : $('#expense_account_id').val(),
                },
                success: function(result) {
                    console.log(result);
                    
                    if(result.success == 0){
                        toastr.error(result.msg);
                        return false;
                    }else{
                        window.location.href = '{{URL::to('/')}}/pricechanges/';
                    }
                    
                },
            });
        });
    
        $('#price_changed_date_range').daterangepicker();
        if ($('#price_changed_date_range').length == 1) {
            $('#price_changed_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#price_changed_date_range').val(
                    start.format(moment_date_format) + ' - ' + end.format(moment_date_format)
                );
            });
            $('#price_changed_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#product_sr_date_filter').val('');
            });
            $('#price_changed_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#price_changed_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
        
                //price changed details
        /*$('#price_changed_details_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/pricechanges/details',
                data: function(d) {
                    var start_date = $('input#price_changed_date_range')
                    .data('daterangepicker')
                    .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#price_changed_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start_date;
                    d.end_date = end_date;
                    d.category_id = $('#price_changed_filter_category_id').val();
                    d.subcategory_id = $('#subcategory_details_id').val();
                    d.user_id = $('#price_changed_filter_user_id').val();
                    d.product_id = $("#price_changed__filter_product_id").val();
                }
            },
           
            columns: [
                //{ data: 'DT_Row_Index', name: 'DT_Row_Index' , orderable: false, searchable: false},
                { data: 'date', name: 'date' },
                { data: 'form_no', name: 'form_no' },
                { data: 'category', name: 'category' },
                { data: 'sub_category', name: 'sub_category' },
                { data: 'current_price', name: 'current_price' },
                { data: 'new_price', name: 'new_price' },
                { data: 'quantity', name: 'quantity' },
                { data: 'total_gain', name: 'total_gain' },
                { data: 'total_loss', name: 'total_loss' },
                { data: 'user', name: 'user' }
            ],
            columnDefs: [
                { width: 20, targets: 6 }
            ]
        });*/
        
            
            
        
        $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
          var target = $(e.target).attr("href") // activated tab
          //if($(target).find('.dataTable').length > 0) {
              if ($.fn.DataTable.isDataTable('.dataTable')) {
                    $('.dataTable').DataTable().destroy();
                }
          console.log(target);
          if(target == "#price_changed_details_tab") {
            priceChangedDetailsTable();
          } else if(target == "#f17_from_tab") {
              form17Table();
          }
            
        });
        
        
    
});

function priceChangedDetailsTable() {
                  $('#form_17_table').DataTable().destroy();
              price_changed_details = $('#price_changed_details_table').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: '/pricechanges/details',
                    data: function(d) {
                        var start_date = $('input#price_changed_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                        var end_date = $('input#price_changed_date_range')
                            .data('daterangepicker')
                            .endDate.format('YYYY-MM-DD');
                        d.start_date = start_date;
                        d.end_date = end_date;
                        d.category_id = $('#price_changed_filter_category_id').val();
                        d.subcategory_id = $('#subcategory_details_id').val();
                        d.user_id = $('#price_changed_filter_user_id').val();
                        d.product_id = $("#price_changed__filter_product_id").val();
                    }
                },
               
                columns: [
                    //{ data: 'DT_Row_Index', name: 'DT_Row_Index' , orderable: false, searchable: false},
                    { data: 'date', name: 'date' },
                    { data: 'form_no', name: 'form_no' },
                    { data: 'category', name: 'category' },
                    { data: 'sub_category', name: 'sub_category' },
                    { data: 'current_price', name: 'variations.sell_price_inc_tax' },
                    { data: 'new_price', name: 'new_price' },
                    { data: 'quantity', name: 'quantity' },
                    { data: 'total_gain', name: 'total_gain' },
                    { data: 'total_loss', name: 'total_loss' },
                    { data: 'user', name: 'user' }
                ],
                columnDefs: [
                    { width: 20, targets: 6 }
                ]
            });
            $('.category_changed_details_filter').change(function(){
              var cat = $('.category_changed_details_filter').val();
              
              $.ajax({
                    method: 'POST',
                    url: '/products/get_sub_categories',
                    dataType: 'html',
                    data: { cat_id: cat },
                    success: function(result) {
                      if (result) {
                        $('.subcategory_details_filter').html(result);
                      }
                    },
                  });
            });
                
            $('.reload_price_details').change(function(){
                console.log("Hello");
                price_changed_details.ajax.reload();
            });
            $('input#price_changed_date_range').on('input', function() {
                console.log("Inside");
                price_changed_details.ajax.reload();
            });
}
function form17Table() {
            $('.price_changed_total_gain_value').text(__number_f(0));
          $('.price_changed_total_loss_value').text(__number_f(0));
            //form_17_table 
        form_17_table = $('#form_17_table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: '/pricechanges/create',
                data: function(d) {
                    var start_date = $('input#f17_date').val()
                    d.start_date = start_date;
                    d.category_id = $('#product_list_filter_category_id').val();
                    d.unit_id = $('#product_list_filter_unit_id').val();
                    d.brand_id = $('#product_list_filter_brand_id').val();
                    d.location_id = $('#location_id').val();
                    d.store_id = $('#store_id').val();
                    d.product_id = $("#product_list_filter_product_id").val();
                }
            },
           
            columns: [
                { data: 'DT_Row_Index', name: 'DT_Row_Index' , orderable: false, searchable: false},
                { data: 'sku', name: 'products.sku' },
                { data: 'product', name: 'products.name' },
                { data: 'current_stock', name: 'vld.qty_available' },
                { data: 'unit_price', name: 'variations.default_sell_price' },
                { data: 'new_price', name: 'new_price' },
                { data: 'unit_price_difference', name: 'unit_price_difference' },
                { data: 'price_changed_loss', name: 'price_changed_loss' },
                { data: 'price_changed_gain', name: 'price_changed_gain' },
                { data: 'current_sale_price', name: 'variations.sell_price_inc_tax' },
                { data: 'new_sale_price', name: 'new_sale_price' },
                { data: 'total_sale_difference', name: 'total_sale_difference' }
            ],
            columnDefs: [
                { width: 20, targets: 6 }
            ]
        });
}
    
    
</script>
@endsection