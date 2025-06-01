
@extends('layouts.app')

@section('title', __('subscription::lang.subscription_list'))

@section('content')
<!-- Main content -->
<section class="content">
    
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('index_product_id',  __('subscription::lang.product') . ':') !!}
                        {!! Form::select('index_product_id', $products, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('index_contact_id',  __('subscription::lang.customer') . ':') !!}
                        {!! Form::select('index_contact_id', $customers, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('index_subscription_cycle',  __('subscription::lang.subscription_cycle') . ':') !!}
                        {!! Form::select('index_subscription_cycle', $subscription_cycle, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
        </div>
    </div>
    
    
    <div class="row">
        <div class="col-md-12">
            
            <div class="row">
                <div class="col-md-11">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="subscription_list_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'subscription::lang.date' )</th>
                                    <th>@lang( 'subscription::lang.customer' )</th>
                                    <th>@lang( 'subscription::lang.product' )</th>
                                    <th>@lang( 'subscription::lang.expiry_date' )</th>
                                    <th>@lang( 'subscription::lang.subscription_amount' )</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade subscription_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>
   
    $(document).on('click', '#add_fleet_btn', function(){
        $('.subscription_modal').modal({
            backdrop: 'static',
            keyboard: false
        })
    })


    $(document).ready(function(){
    
        subscription_list_table = $('#subscription_list_table').DataTable({
                processing: true,
                serverSide: false,
                ajax: {
                    url : "{{action('\Modules\Subscription\Http\Controllers\SubscriptionListController@listInvoices')}}",
                    data: function(d){
                        d.product_id = $('#index_product_id').val();
                        d.contact_id = $('#index_contact_id').val();
                        d.subscription_cycle = $('#index_subscription_cycle').val();
                    }
                },
                columnDefs:[{
                        "targets": 1,
                        "orderable": false,
                        "searchable": false
                    }],
                columns: [
                    {data: 'transaction_date', name: 'transaction_date'},
                    {data: 'contact_name', name: 'contacts.name'},
                    {data: 'product', searchable: false},
                    {data: 'expiry_date', name: 'expiry_date'},
                    {data: 'subscription_amount', name: 'subscription_amount', searchable: false}
                  
                ],
                fnDrawCallback: function(oSettings) {
                    __currency_convert_recursively($('#subscription_list_table'));
                }
            });
        });
        
        

        $('#index_product_id, #index_contact_id,#index_subscription_cycle').change(function () {
            subscription_list_table.ajax.reload();
        })
        
        $(document).on('click', 'a.delete-button', function(){
            swal({
                title: LANG.sure,
                icon: "warning",
                buttons: true,
                dangerMode: true,
            }).then((willDelete)=>{
                if(willDelete){
                    let href = $(this).data('href');

                    $.ajax({
                        method: 'delete',
                        url: href,
                        data: {  },
                        success: function(result) {
                            if(result.success == 1){
                                toastr.success(result.msg);
                            }else{
                                toastr.error(result.msg);
                            }
                            subscription_list_table.ajax.reload();
                        },
                    });
                }
            });
        })
        
        
        $(document).on('change','.subscription_cycle', function(event) {
            
            var product = $(this).val();
            
            $(".product_id").empty();
            
            if(product){
                // Send an AJAX request
                $.ajax({
                    url: "/subscription/get-cycles/"+product,
                    method: 'GET',
                    data: {},
                    success: function(response) {
                       var cycle_details = JSON.stringify(response);
                       $(".cycle_details").val(cycle_details);
                       
                       $(".product_id").empty();
                       
                       $.each(response, function(index, element) {
                            $(".product_id").append('<option value="'+element.id+'">'+element.name+'</option>');
                        });
                        
                        if ($('#selected_settings_id').length) {
                            var selected_ids = JSON.parse($('#selected_settings_id').val());
                            $(".product_id").val(selected_ids).trigger('change');
                        }
                       
                    },
                    error: function(xhr, status, error) {
                        // Handle error response
                        console.error(xhr.responseText); // You can display an error message or perform further actions
                    }
                });
            }
           
                
        });
        
        $(document).on('change','.product_id', function(event) {
            
            var cycle = $(this).val();
            
           
            if(cycle){
                var price_total = 0;
                $.each(cycle, function(index, element) {
                    var cycle_details = JSON.parse($(".cycle_details").val());
                    var this_cycle = cycle_details[element];
                   
                    price_total += parseFloat(this_cycle.price);
                    var days = this_cycle.days ?? 0;
                    
                    var currentValue = $('.transaction_date').val();
                
                    var currentDate = new Date(currentValue);
                    
                    currentDate.setDate(currentDate.getDate() + days);
                    
                    var formattedDate = currentDate.toISOString().split('T')[0];
                    
                    $('.expiry_date').val(formattedDate);
                    
                });
                
                __write_number($(".subscription_amount"),price_total);
            }
           
                
        });
        
        $(document).on('change','.transaction_date', function() {
            $('.product_id').trigger('change');
        });
</script>
@endsection