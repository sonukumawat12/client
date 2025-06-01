@extends('layouts.app')

@section('title', __('fleet::lang.route_operations'))

@section('content')

<style>
    
    .bootstrap-tagsinput {
        transition: transform 0.3s ease, z-index 0s ease;
        transform-origin: center top; 
        overflow: hidden;
        white-space: normal;
        word-wrap: break-word;
        width: 100%;
    }
    
    /* Hover state */
    .bootstrap-tagsinput:hover {
        transform: scale(1.5); 
        z-index: 99999999;
        overflow: auto;
        margin-left: 50px !important;
        position: relative;
    }
</style>

<!-- Main content -->
<section class="content">
    {!! Form::open(['url' => action('\Modules\Fleet\Http\Controllers\RouteOperationController@store'), 'method' =>
    'post', 'id' => 'route_operation_form', 'enctype' => 'multipart/form-data' ])
    !!}
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary', 'title' => __(
            'fleet::lang.add_trip_operations')])
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('date_of_operation', __( 'fleet::lang.date_of_operation' )) !!}
                    {!! Form::text('date_of_operation', date('m/d/Y'), ['class' => 'form-control', 'required',
                    'placeholder' => __(
                    'fleet::lang.date_of_operation' ), 'readonly',
                    'id' => 'date_of_operation']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('invoice_no', __( 'fleet::lang.invoice_no' )) !!}
                    {!! Form::text('invoice_no', $invoice_number, ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.invoice_no' ), 'readonly',
                    'id' => 'invoice_no']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __( 'fleet::lang.location' )) !!}<br>
                    {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'location_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('contact_id', __( 'fleet::lang.customer' )) !!}
                    {!! Form::select('contact_id', $customers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'customer']);
                    !!}
                </div>
            </div>
            </div>
            
            <div class="row">
                <div class="form-group col-sm-3">
                    {!! Form::label('vat_number', __( 'airline::lang.customer_vat_no' ) . '') !!}
                    <div class="input-group">
                        <div class="input-group-btn">
                            <button type="button" class="btn btn-default bg-white btn-flat" title="{{__('airline::lang.customer_vat_no')}}">
                                <i class="fa fa-user"></i>
                            </button>
                        </div>
                        {!! Form::text('vat_number', null, ['class' => 'form-control', 'id' => 'customer_vat_number', 'readonly','placeholder' => __('airline::lang.customer_vat_no')]); !!}
                        <input type="hidden" id="vat_btn_input">
                        <span class="input-group-btn vat-btn-group hide">
                            <button type="button" class="btn btn-default bg-white btn-flat btn-vat-modal vat-btn-group-action" data-href="" data-container=".contact_modal_noreload">
                                <i class="fa fa-plus-circle text-primary fa-lg"></i>
                            </button>
                        </span>
                    </div>
                </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('order_number', __( 'fleet::lang.order_number' )) !!}
                    {!! Form::text('order_number', null, ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.order_number' ),
                    'id' => 'order_number']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('order_date', __( 'fleet::lang.order_date' )) !!}
                    {!! Form::text('order_date',  date('m/d/Y'), ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.order_date' ), 'readonly',
                    'id' => 'order_date']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('amt_method', __( 'fleet::lang.trip_categories' )) !!}
                    
                    <select class="form-control select2" required name="amt_method" id="amt_method">
                        <option value="">@lang('fleet::lang.please_select')</option>
                        @foreach($trip_cats as $one)
                            <option value="{{$one->id}}" data-string="{{$one->amount_method}}">{{$one->name}}</option>
                        @endforeach
                    </select>
                    
                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('route_id', __( 'fleet::lang.route' )) !!}
                    <div class="input-group">
                        
                        <select class="form-control select2" required name="route_id" id="route_id">
                            <option value="">@lang('fleet::lang.please_select')</option>
                            @foreach($routes as $one)
                                <option value="{{$one->id}}" data-string="{{$one->trip_category}}">{{$one->route_name}}</option>
                            @endforeach
                        </select>
                    
                        <span class="input-group-btn">
                            <button type="button" class="btn
                            btn-default
                            bg-white btn-flat btn-modal"
                                data-href="{{action('\Modules\Fleet\Http\Controllers\RouteController@create', ['quick_add' => true])}}"
                                title="@lang('fleet.add_route')" data-container=".view_modal"><i
                                    class="fa fa-plus-circle text-primary fa-lg"></i></button>
                        </span>
                    </div>
                </div>
            </div>
            
             <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('delivered_to_acc_no', __( 'fleet::lang.delivered_to_acc_no' )) !!}
                    {!! Form::text('delivered_to_acc_no', null, ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.delivered_to_acc_no' ),
                    'readonly',
                    'id' => 'delivered_to_acc_no']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('fleet_id', __( 'fleet::lang.vehicle_no' )) !!}
                    {!! Form::select('fleet_id', $fleets, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'fleet_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('product_id', __( 'fleet::lang.product' )) !!}<br>
                    {!! Form::select('product_id[]', $products, null, [
                        'class' => 'form-control select2',
                        'id' => 'product_id',
                        'multiple' => 'multiple',
                    ]) !!}

                </div>
            </div>
            </div>
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('qty', __( 'fleet::lang.qty_separated' )) !!}<br>
                    {!! Form::text('qty', null, [
                        'class' => 'form-control',
                        'style' => 'width: 100% !important;'
                    ]) !!}

                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('driver_id', __( 'fleet::lang.driver' )) !!}<br>
                    {!! Form::select('driver_id', $drivers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'driver_id']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('helper_id', __( 'fleet::lang.helper' )) !!}<br>
                    {!! Form::select('helper_id', $helpers, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'helper_id']);
                    !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('rate_per_km', __( 'fleet::lang.rate_km' )) !!}
                    {!! Form::text('rate_per_km', null, ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.rate_km' ),
                    'id' => 'rate_per_km','readonly']);
                    !!}
                </div>
            </div>
            
            </div>
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('distance', __( 'fleet::lang.distance_km' )) !!}
                    {!! Form::text('distance', null, ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.distance' ),
                    'id' => 'distance','readonly']);
                    !!}
                </div>
            </div>    
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('starting_meter', __( 'fleet::lang.starting_meter' )) !!}
                    {!! Form::text('starting_meter', 00, ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.starting_meter' ),
                    'id' => 'starting_meter','readonly','value'=>'00']);
                    !!}
                </div>
            </div>
            
             <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('ending_meter', __( 'fleet::lang.ending_meter' )) !!}
                    {!! Form::text('ending_meter', 00, ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.ending_meter' ),
                    'id' => 'ending_meter','readonly','value'=> '00']);
                    !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('amount', __( 'fleet::lang.amount' )) !!}
                    {!! Form::text('amount', null, ['class' => 'form-control tagsinput', 'placeholder' => __(
                    'fleet::lang.amount' ),
                    'id' => 'amount','readonly']);
                    !!}
                </div>
            </div>
            
            <div class="clearfix"></div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('driver_incentive', __( 'fleet::lang.driver_incentive' )) !!}
                    {!! Form::text('driver_incentive', null, ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.driver_incentive' ),
                    'id' => 'driver_incentive','readonly']);
                    !!}
                </div>
            </div>
            
            
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('helper_incentive', __( 'fleet::lang.helper_incentive' )) !!}
                    {!! Form::text('helper_incentive', null, ['class' => 'form-control', 'placeholder' => __(
                    'fleet::lang.helper_incentive' ),
                    'id' => 'helper_incentive','readonly']);
                    !!}
                </div>
            </div>
            
            <div class="col-sm-3">
        		<div class="form-group">
        			{!! Form::label('is_vat', __('lang_v1.is_vat')) !!}
        			{!! Form::select('is_vat', ['0' => __('lang_v1.no'),'1' => __('lang_v1.yes')],null, ['class' => 'form-control
        			select2', 'required']); !!}
        		</div>
        	</div>
            
            </div>
            
            <input type="hidden" name="grand_total_hidden" id="grand_total_hidden">
            <input type="hidden" name="final_total" id="final_total">

            @endcomponent

            @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
            
            <div id="payment_rows_div">
                <div class="box-body payment_row" data-row_id="0">
                    <button type="button" class="btn btn-primary pull-right"
                            id="add-payment-row">@lang('sale.add_payment_row')</button>
                    @include('sale_pos.partials.payment_row_form', ['row_index' => 0])
                </div>
            </div>

                

            <div class="row">
                <div class="col-sm-12">
                    <div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span
                            id="payment_due">0.00</span>
                    </div>

                </div>
            </div>
            <br>
            <input type="hidden" value="" id="actual_distance">
            <div class="row">
                    <div class="col-sm-12">
                        <button type="button" id="submit_route_operation_form"
                            class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
                    </div>
                </div>
            
            @endcomponent
        </div>
    </div>
    <div class="modal fade fleet_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    {!! Form::close() !!}
    
    <div class="modal fade contact_modal_noreload" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    </div>
</section>
<!-- /.content -->

@endsection

@section('javascript')
<script>

    $(document).ready(function() {
        $('#qty').tagsinput({
          allowDuplicates: true
        });
    });
    
     $(document).on('click','#update_vat_number',function(e) {
            e.preventDefault();
            
            if($("#update_fields_type").val() == 'nic_number'){
                var data = {'nic_number' : $("#add_nic_number").val()};
            } else if($("#update_fields_type").val() == 'mobile'){
                var data = {'mobile' : $("#add_mobile").val()};
            }else{
                if($("#is_single_field").val() == 'yes'){
                    var data = {'vat_number' : $("#main_add_vat_number").val()};  
                }else{
                    var data = {'vat_number' : $("#add_vat_number").val()};  
                }
                
            }
            
            
            $.ajax({
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: data,
                url: $('#contact_vat_number_form').attr('action'),
                success: function(result) {
                    if (result.success == true) {
                        $('div.contact_modal_noreload').modal('hide');
                        toastr.success(result.msg);
                        
                        if($("#update_fields_type").val() == 'nic_number'){
                            $("#passport_number_text").val(result.contact.nic_number);
                        } else if($("#update_fields_type").val() == 'mobile'){
                            $("#passenger_mobile_text").val(result.contact.mobile);
                        }else{
                            $("#customer_vat_number").val(result.contact.vat_number);
                        }
                        
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
  
  $(document).on('click', '.btn-vat-modal', function(e) {

          e.preventDefault();
          
          var url = '/contacts/update-vatnumber/' + $("#vat_btn_input").val();
          
          
          console.log(url);
    
          var container = $(this).data('container');
          
          $(container).empty();
    
          $.ajax({
    
              url: url,
    
              dataType: 'html',
    
              success: function(result) {
                  // var contact = $('#default_contact_id').val();
                  $(container).html(result).modal('show');
                  // $(container).find('input#customer').val(contact);
              },
    
          });
    
      });

  $(document).on('change','#customer',function(){
    let customer_id = $('#customer :selected').val();
    
    if(customer_id){
        $(".reference-btn").show();
        $(".vat-btn-group").removeClass('hide');
        $("#vat_btn_input").val(customer_id);
    }else{
        $(".reference-btn").hide();
        $(".vat-btn-group").addClass('hide');
        $("#customer_vat_number").val("");
    }
    
        
        $.ajax({
            method: "get",
            url: "/petro/settlement/payment/get-customer-details/" + customer_id,
            data: {},
            success: function (result) {
                
                $("#voucher_order_outstanding").val(result.total_outstanding);
                $("#voucher_order_creditlimit").val(result.credit_limit);
                $("#customer_vat_number").val(result.vat_number);
                
                $('#voucher_order_amount').trigger('change');
            },
        });
        
  })


    $('#fleet_id').change(function () {
        
        let fleet_id = $(this).val();
         var actual_meter=0;
         var distance =0 ;
          $.ajax({
            method: 'GET',
            url: "{{ url('fleet-management/route-operation/get-by-fleet')}}/"+fleet_id,
            dataType: 'json',
            success: function (result) {
                
              if(result)
               {
                   actual_meter = result.actual_meter;
               }
            },
             complete: function (data) {
               if($("#actual_distance").val())
                {
                    distance = parseInt($("#actual_distance").val().replace(/,/g, ''));
                }
                
                actual_meter = parseInt(actual_meter);
                
                 __write_number($('#starting_meter'), actual_meter);
                 
                __write_number($('#ending_meter'), (actual_meter + distance));
               
            }
        });
       
    });
    $('#location_id option:eq(1)').attr('selected', true);
    $('#date_of_operation').datepicker('setDate', new Date());
    $('#order_date').datepicker();
    $(document).ready(function() {
        $('#qty, #route_id, #amt_method').on('change', function() {
            let id = $("#route_id").val();
            
            if(id){
                $.ajax({
                method: 'get',
                url: '/fleet-management/routes/get-details/' + id,
                data: {},
                success: function(result) {
                    
                   
                    
                    __write_number($("#rate_per_km"),result.rate);
                    
                    var _totalAmount = result.route_amount;
                    
                    $("#delivered_to_acc_no").val(result.acc_no);
                    $("#actual_distance").val(result.actual_distance);
                    
                    
                    
                    var selectedOption = $('#amt_method').find('option:selected'); 
                    var dataString = selectedOption.data('string');
                    
                    if (dataString == 'km_distance_qty') {
                        var inputString = $('#qty').val();
    
                        // Remove any spaces from the string
                        var cleanedString = inputString.replace(/\s+/g, '');
    
                        // Split the string into an array
                        var stringArray = cleanedString.split(',');
    
                        // Convert the array of strings to an array of numbers and calculate the sum
                        var sum = stringArray.map(Number).reduce((a, b) => a + b, 0);
                        
                        _totalAmount = _totalAmount * sum;
                    }
    
                    // __write_number($('#distance'), result.distance);
                    __write_number($('#distance'), result.distance);
                    __write_number($('#amount'), _totalAmount);
                    $('#fleet_id').trigger('change');
    
                    $('#grand_total_hidden').val(_totalAmount);
                    $('#final_total').val(_totalAmount);
                    $('#payment_due').text(__currency_trans_from_en(_totalAmount, false, false));
                    $('#amount_0').val(Number(_totalAmount).toFixed(2));
                    $('#amount_0').trigger('change');
                    __write_number($('#driver_incentive'), result.driver_incentive);
                    __write_number($('#helper_incentive'), result.helper_incentive);
                },
            });
            }
    
            
        });
        
        var originalOptions = $('#route_id').html();
        $('#amt_method').on('change', function() {
            
            var selectedAmtMethod = $(this).val();
            var filteredOptions = '<option value="">@lang("fleet::lang.please_select")</option>';
            
            $(originalOptions).each(function() {
                var optionDataString = $(this).data('string');

                if (optionDataString == selectedAmtMethod) {
                    filteredOptions += '<option value="' + $(this).val() + '" data-string="' + optionDataString + '">' + $(this).text() + '</option>';
                }
            });

            $('#route_id').html(filteredOptions);
        });
    });

    $('button#add-payment-row').click(function () {
        var row_index = parseInt($('.payment_row_index').val()) + 1;
        var location_id = $('#location_id').val();

        $.ajax({
            method: 'POST',
            url: '/purchases/get_payment_row',
            data: { row_index: row_index, location_id: location_id },
            dataType: 'html',
            success: function (result) {
                if (result) {
                    var total_payable = __read_number($('input#grand_total_hidden'));
                    var total_paying = 0;
                    $('#payment_rows_div')
                        .find('.payment-amount')
                        .each(function () {
                            if (parseFloat($(this).val())) {
                                total_paying += __read_number($(this));
                            }
                        });
                    var b_due = total_payable - total_paying;
                    var appended = $('#payment_rows_div').append(result);
                    $(appended).find('input.payment-amount').focus();
                    var roundedValue = Math.round(b_due * 100) / 100; // Rounds to two decimal places
                    $(appended).find('input.payment-amount').last().val(roundedValue).change().select();
                    __select2($(appended).find('.select2'));
                    $('#amount_' + row_index).trigger('change');
                    $('#cheque_date_' + row_index).datepicker('setDate', new Date());
                    $('.payment_row_index').val(parseInt(row_index));
                    let cash_account_id = $('#cash_account_id').val();
                    $(appended).find('select.payment_types_dropdown ').last().val(cash_account_id).change().select();
                    calculate_balance_due();
                }
            },
        });
    });

    $(document).on('click', '.remove_payment_row', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $(this).closest('.payment_row').remove();
                calculate_balance_due();
            }
        });
    });

    $(document).on('change', '.payment-amount', function() {
        calculate_balance_due();
    });

    
    function calculate_balance_due() {
        var total_payable = __read_number($('#final_total'));
        var total_paying = 0;
        $('#payment_rows_div')
            .find('.payment-amount')
            .each(function() {
                if (parseFloat($(this).val())) {
                    total_paying += __read_number($(this));
                }
            });
        var bal_due = total_payable - total_paying;
        
        if(bal_due != 0) {
            $("#submit_route_operation_form").hide();
        }else{
            $("#submit_route_operation_form").show();
        }
    

        $('#payment_due').text(__currency_trans_from_en(bal_due, false, false));
    }

    $('#submit_route_operation_form').click(function () {
        $('#route_operation_form').validate();
        if($('#route_operation_form').valid()){
            $('#route_operation_form').submit();
        }
    })

    
 $(document).on('submit', 'form#quick_add_route', function (e) {
    e.preventDefault();
    var data = $(this).serialize();
    $.ajax({
      method: 'post',
      url: '{{action('\Modules\Fleet\Http\Controllers\RouteController@store')}}',
      dataType: 'json',
      data: data,
      success: function(result) {
        if(result.success){
            toastr.success(result.msg);
            $('.view_modal').modal('hide');
            $.ajax({
                method: 'get',
                url: '{{action('\Modules\Fleet\Http\Controllers\RouteController@getRouteDropdown')}}',
                data: {  },
                contactType: 'html',
                success: function(data_html) {
                    $('#route_id').empty().append(data_html);
                },
            });
        }else{
            toastr.error(result.msg);
        }
      },
    });
 })
</script>
@endsection