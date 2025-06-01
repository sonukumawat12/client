@extends('layouts.app')
@section('title','Loading')
<style>
    .select2 {
        width: 100% !important;
    }

    #product_modal_bakery {
            width: 500px;
            margin: auto;
    }
</style>
@section('content')

    <section class="content-header">
        <div class="row">
            <div class="col-md-12 dip_tab">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs">
                            @if(auth()->user()->can('bakery_add_loading'))
                            <li class=" @if(session('status.tab') == 'loading'  || empty(session('status.tab')))) active @endif">
                                <a style="font-size:13px;" href="#loading" data-toggle="tab">
                                    <i class="fa-solid fa-car"></i><strong>@lang('bakery::lang.loading')</strong>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->can('bakery_list_loading'))
                            <li class=" @if(session('status.tab') == 'list_loading') active @endif">
                                <a style="font-size:13px;" href="#list_loading" data-toggle="tab">
                                     <strong>@lang('bakery::lang.list_loading')</strong>
                                </a>
                            </li>
                            @endif
                            
                            @if(auth()->user()->can('bakery_returns'))
                             <li class=" @if(session('status.tab') == 'returns') active @endif">
                                <a style="font-size:13px;" href="#returns" data-toggle="tab">
                                     <strong>@lang('bakery::lang.returns')</strong>
                                </a>
                            </li>
                            @endif
                            
                    </ul>
                </div>
            </div>
        </div>
    <div class="tab-content">

        
        @if(auth()->user()->can('bakery_add_loading'))
        <div class="tab-pane @if(session('status.tab') == 'loading' || empty(session('status.tab'))) active @endif" id="loading">
             @include('bakery::bakery_loading.partials.loading')
        </div>
        @endif
        
        @if(auth()->user()->can('bakery_list_loading'))
        <div class="tab-pane  @if(session('status.tab') == 'list_loading') active @endif" id="list_loading">
            @include('bakery::bakery_loading.partials.list_loading')
        </div>
        @endif
        
        @if(auth()->user()->can('bakery_returns'))
        <div class="tab-pane  @if(session('status.tab') == 'returns') active @endif" id="returns">
            @include('bakery::bakery_loading.partials.returns')
        </div>
        @endif

    </div>

    
</section>

@endsection

@section('javascript')
<script>

//driver tab script
$(document).on('submit', "#route_add_form", function(e) {
    e.preventDefault();

    var form = $(this);
    var url = form.attr('action');
    var data = form.serialize();

    var submitButton = form.find('.submit-btn');
    submitButton.prop('disabled', true);

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function(response) {
            if(response.success){
                toastr.success(response.msg, 'Success');
                $("#route_id").append('<option value="'+response.data.id+'">'+response.data.route+'</option>').val(response.data.id).trigger('change');
                $('.modal').modal('hide');
            }else{
                toastr.error(response.msg, 'Error');
            }
            
        },
        error: function(xhr, status, error) {
            // Handle error response
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = errors;

                toastr.error(errorMessage, 'Validation Errors');
            } else {
                var error = xhr.responseJSON.message ?? "";
                if (error == "") {
                    var error = 'Something Went Wrong!, Try again!';
                }
                toastr.error(error, 'Error');
            }
        },
        complete: function() {
            submitButton.prop('disabled', false);
        }
    });

});

$(document).on('submit', "#product_add_form", function(e) {
    e.preventDefault();

    var form = $(this);
    var url = form.attr('action');
    var data = form.serialize();

    var submitButton = form.find('.submit-btn');
    submitButton.prop('disabled', true);

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function(response) {
            if(response.success){
                toastr.success(response.msg, 'Success');
                $("#product_id").append('<option value="'+response.data.id+'">'+response.data.name+'</option>').val(response.data.id).trigger('change');
                $('.modal').modal('hide');
            }else{
                toastr.error(response.msg, 'Error');
            }
            
        },
        error: function(xhr, status, error) {
            // Handle error response
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = errors;

                toastr.error(errorMessage, 'Validation Errors');
            } else {
                var error = xhr.responseJSON.message ?? "";
                if (error == "") {
                    var error = 'Something Went Wrong!, Try again!';
                }
                toastr.error(error, 'Error');
            }
        },
        complete: function() {
            submitButton.prop('disabled', false);
        }
    });

});

$(document).on('submit', "#driver_add_form", function(e) {
    e.preventDefault();

    var form = $(this);
    var url = form.attr('action');
    var data = form.serialize();

    var submitButton = form.find('.submit-btn');
    submitButton.prop('disabled', true);

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function(response) {
            if(response.success){
                toastr.success(response.msg, 'Success');
                $("#driver_id").append('<option value="'+response.data.id+'">'+response.data.driver_name+'</option>').val(response.data.id).trigger('change');
                $('.modal').modal('hide');
            }else{
                toastr.error(response.msg, 'Error');
            }
            
        },
        error: function(xhr, status, error) {
            // Handle error response
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = errors;

                toastr.error(errorMessage, 'Validation Errors');
            } else {
                var error = xhr.responseJSON.message ?? "";
                if (error == "") {
                    var error = 'Something Went Wrong!, Try again!';
                }
                toastr.error(error, 'Error');
            }
        },
        complete: function() {
            submitButton.prop('disabled', false);
        }
    });

});

$(document).on('submit', "#fleet_form", function(e) {
    e.preventDefault();

    var form = $(this);
    var url = form.attr('action');
    var data = form.serialize();

    var submitButton = form.find('.submit-btn');
    submitButton.prop('disabled', true);

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        success: function(response) {
            if(response.success){
                toastr.success(response.msg, 'Success');
                $("#vehicle_id").append('<option value="'+response.data.id+'">'+response.data.vehicle_number+'</option>').val(response.data.id).trigger('change');
                $('.modal').modal('hide');
            }else{
                toastr.error(response.msg, 'Error');
            }
            
        },
        error: function(xhr, status, error) {
            // Handle error response
            if (xhr.status === 422) {
                var errors = xhr.responseJSON.errors;
                var errorMessage = errors;

                toastr.error(errorMessage, 'Validation Errors');
            } else {
                var error = xhr.responseJSON.message ?? "";
                if (error == "") {
                    var error = 'Something Went Wrong!, Try again!';
                }
                toastr.error(error, 'Error');
            }
        },
        complete: function() {
            submitButton.prop('disabled', false);
        }
    });

});

$(document).ready(function () {
        $(".select2").select2();
        $('#return_loading_date').datepicker('setDate', new Date());
    
        $('#date').datepicker('setDate', new Date());$('#date').datepicker('setDate', new Date());
        
        loading_product_table = $('#loading_product_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '{{action('\Modules\Bakery\Http\Controllers\BakeryLoadingController@getProducts')}}',
                data: function (d) {
                    d.product_id = $("#loading_product_id").val();
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'name', name: 'name' },
                { data: 'unit_cost', name: 'unit_cost' },
                
                { data: 'qty', name: 'qty' ,searchable: false},
                { data: 'total_due', name: 'total_due' ,searchable: false},
                
                { data: 'created_by', name: 'users.username' },
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        
        
        returns_product_table = $('#returns_product_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[0, 'desc']],
            ajax: {
                url: '/bakery/get-products-returns',
                data: function (d) {
                    
                    if($("#return_loading_form_no").val()){
                        var return_loading_form_no = $("#return_loading_form_no").val();
                        $(".submit-return").prop('disabled',false);
                    }else{
                        var return_loading_form_no = 0;
                        $(".submit-return").prop('disabled',true);
                    }
                    
                    d.product_id = $("#return_product_id").val();
                    d.id=return_loading_form_no;
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                { data: 'name', name: 'name' },
                { data: 'unit_cost', name: 'unit_cost' },
                { data: 'qty', name: 'qty' ,searchable: false},
                
                { data: 'total_loaded', name: 'total_loaded' ,searchable: false},
                { data: 'returned_qty', name: 'returned_qty' ,searchable: false},
                { data: 'returned_qty_amt', name: 'returned_qty_amt' ,searchable: false},
                { data: 'due_amount', name: 'due_amount' ,searchable: false},
                { data: 'settled_amt', name: 'settled_amt' ,searchable: false},
                { data: 'short_amt', name: 'short_amt' ,searchable: false},
                
                { data: 'created_by', name: 'users.username' },
            ],
            fnDrawCallback: function(oSettings) {
                $(".loading_table_returned_qty").trigger('input');
            },
        });
})

$(document).on('change',"#return_loading_form_no,#return_product_id",function(){
    returns_product_table.ajax.reload();
})

$(document).on('change',"#loading_product_id",function(){
    loading_product_table.ajax.reload();
})
   
    
$(document).ready(function() {
    function updateTotals() {
        var grandQty = 0;
        var grandTotal = 0;
        
        $('#loading_product_table tbody tr').each(function() {
            var $row = $(this);

            var unitCost = parseFloat($row.find('.table_unit_cost').val());
            var qty = parseFloat($row.find('input[name="table_qty[]"]').val());

            if (isNaN(qty)) {
                qty = 0;
            }
            
            grandQty += qty;

            var total = unitCost * qty;
            
            grandTotal += !isNaN(total) ? parseFloat(total) : 0;
            
            $row.find('.table_total_due').val(total.toFixed(2));
            $row.find('.table_span_total').text(__number_f(total));
            
            $('.grand_total').text(__number_f(grandTotal));
            $('.grand_qty').text(__number_f(grandQty));
        });
    }
    
    function updateEditTotals() {
        var grandQty = 0;
        var grandTotal = 0;
        
        $('#edit_loading_product_table tbody tr').each(function() {
            var $row = $(this);

            var unitCost = parseFloat($row.find('.table_unit_cost').val());
            var qty = parseFloat($row.find('input[name="table_qty[]"]').val());

            if (isNaN(qty)) {
                qty = 0;
            }
            
            grandQty += qty;

            var total = unitCost * qty;
            
            grandTotal += !isNaN(total) ? parseFloat(total) : 0;
            
            $row.find('.table_total_due').val(total.toFixed(2));
            $row.find('.table_span_total').text(__number_f(total));
            
            $('.edit_grand_total').text(__number_f(grandTotal));
            $('.edit_grand_qty').text(__number_f(grandQty));
        });
    }

    $(document).on('input', 'input[name="table_qty[]"]', function() {
        updateTotals();
        updateEditTotals();
    });

    updateTotals();
    updateEditTotals();
});


$(document).ready(function() {
    function updateReturnTotals() {
        
        var grand_total_loaded = 0;
        var grand_total_returned = 0;
        var grand_total_due = 0;
        var grand_total_settled = 0;
        var grand_total_short = 0;
        
        $('#returns_product_table tbody tr').each(function() {
            var $row = $(this);

            var unitCost = parseFloat($row.find('.loading_table_unit_cost').val());
            var qty_returned = __read_number($row.find('.loading_table_returned_qty'));
            var qty_returned_value = qty_returned*unitCost;
            grand_total_returned += qty_returned_value;
            
            var loaded_amount = __read_number($row.find('.loading_table_loaded_amount'));
            var total_due = loaded_amount - qty_returned_value
            grand_total_due += total_due;
            grand_total_loaded += loaded_amount;
            
            var total_settled =  __read_number($row.find('.loading_table_settled_amt'));
            grand_total_settled += total_settled;
            
            var total_short = total_due - total_settled;
            grand_total_short += total_short;
            
            
        
            
            $row.find('.loading_table_total_returned').val(qty_returned_value);
            $row.find('.loading_table_span_total_returned').text(__number_f(qty_returned_value));
            
            $row.find('.loading_table_due_amount').val(total_due);
            $row.find('.loading_table_span_due_amount').text(__number_f(total_due));
            
            $row.find('.loading_table_short_amt').val(total_short);
            $row.find('.loading_table_span_short_amt').text(__number_f(total_short));
           
        });
        
          $('.grand_total_returned').text(__number_f(grand_total_returned));
          $('.grand_total_due').text(__number_f(grand_total_due));
          $('.grand_total_loaded').text(__number_f(grand_total_loaded));
          $('.grand_total_settled').text(__number_f(grand_total_settled));
          $('.grand_total_short').text(__number_f(grand_total_short));
    }

    $(document).on('input', '.loading_table_returned_qty, .loading_table_settled_amt', function() {
        updateReturnTotals();
    });

    updateReturnTotals();
});


$(document).ready(function () {
        if ($('#list_loading_date_range').length == 1) {
            $('#list_loading_date_range').daterangepicker(dateRangeSettings, function(start, end) {
                $('#list_loading_date_range').val(
                   start.format(moment_date_format) + ' - ' +  end.format(moment_date_format)
                );
                
                list_loading_table.ajax.reload();
            });
            $('#list_loading_date_range').on('cancel.daterangepicker', function(ev, picker) {
                $('#list_loading_date_range').val('');
            });
            $('#list_loading_date_range')
                .data('daterangepicker')
                .setStartDate(moment().startOf('month'));
            $('#list_loading_date_range')
                .data('daterangepicker')
                .setEndDate(moment().endOf('month'));
        }
    
        list_loading_table = $('#list_loading_table').DataTable({
            processing: true,
            serverSide: true,
            aaSorting: [[1, 'desc']],
            ajax: {
                url: '{{action('\Modules\Bakery\Http\Controllers\BakeryLoadingController@index')}}',
                data: function (d) {
                    d.driver_id = $("#list_loading_driver_id").val(); 
                    d.vehicle_id = $("#list_loading_vehicle_id").val();
                    
                    var start_date = $('input#list_loading_date_range')
                        .data('daterangepicker')
                        .startDate.format('YYYY-MM-DD');
                    var end_date = $('input#list_loading_date_range')
                        .data('daterangepicker')
                        .endDate.format('YYYY-MM-DD');
                    d.start_date = start_date;
                    d.end_date = end_date;
                }
            },
            @include('layouts.partials.datatable_export_button')
            columns: [
                
                { data: 'action' ,searchable: false},
                
                { data: 'date', name: 'date' },
                { data: 'form_no', name: 'form_no' },
                { data: 'vehicle_number', name: 'bakery_fleets.vehicle_number' },
                { data: 'driver_name', name: 'bakery_drivers.driver_name' },
                { data: 'route_name', name: 'bakery_routes.route' },
                
                { data: 'total_due_amount', name: 'total_due_amount' ,searchable: false},
                { data: 'total_sold_amount', name: 'total_sold_amount' ,searchable: false},
                { data: 'total_returned_amount', name: 'total_returned_amount' ,searchable: false},
                { data: 'total_short_amount', name: 'total_short_amount' ,searchable: false},
                
                { data: 'username', name: 'users.username' },
                
            ],
            fnDrawCallback: function(oSettings) {
            
            },
        });
        
        $('#list_loading_driver_id, #list_loading_vehicle_id').change(function () {
            list_loading_table.ajax.reload();
        })
    
    })

</script>

@endsection


