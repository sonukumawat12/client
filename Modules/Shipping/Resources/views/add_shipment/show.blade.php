@extends('layouts.app')
@section('title', __('Add Shipment'))

<style>
    .select2 {
        width: 100% !important;
    }
</style>
@section('content')

{!! Form::open(['url' => action('\Modules\Shipping\Http\Controllers\AddShipmentController@update', $data[0]->id), 'method' => 'put', 'id' => 'add_purchase_form',
	'files' => true ]) !!}
    <section class="content-header">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group col-sm-4">
                  {!! Form::label('date', __( 'shipping::lang.date' ) . ':*') !!}
                  {!! Form::text('date', @format_date(date('Y-m-d')), ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
                  'shipping::lang.date' )]); !!}
                </div>
                
                <div class="form-group col-sm-4">
                  {!! Form::label('tracking_no', __( 'shipping::lang.tracking_no' ) . ':*') !!}
                  {!! Form::text('tracking_no', $data[0]->tracking_no, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
                  'shipping::lang.tracking_no' )]); !!}
                </div>
                
                <div class="form-group col-sm-4">
                  {!! Form::label('total_payable', __( 'shipping::lang.total_payable' ) . ':*') !!}
                  {!! Form::text('total_payable', $data[0]->total, ['class' => 'form-control', 'required', 'readonly', 'placeholder' => __(
                  'shipping::lang.total_payable' )]); !!}
                </div>
                
            </div>
            <div class="form-group col-sm-3" style="margin-left:12px;">
                {!! Form::label('location', __('shipping::lang.select_location') . ':') !!}
                {!! Form::select('location', $businessLocations, $data[0]->business_id, [
                    'id' => 'location',
                    'class' => 'form-control select2',
                    'placeholder' => __('messages.please_select'),
                    'disabled' => 'disabled'
                ]); !!}
            </div>
            
            <div class="col-md-12 dip_tab">
                <div class="settlement_tabs">
                    <ul class="nav nav-tabs ">
                        <li class=" @if (empty(session('status.tab'))) == 'agent_details') active @endif @if (session('status.tab') == 'agent_details') active @endif">
                            <a style="font-size:13px;" href="#agent_details" data-toggle="tab">
                                <i class="fa fa-user"></i> <strong>@lang('shipping::lang.agent_details')</strong>
                            </a>
                        </li>

                        <li class=" @if (session('status.tab') == 'sender_customer') active @endif">
                            <a style="font-size:13px;" href="#sender_customer" data-toggle="tab">
                                <strong>@lang('shipping::lang.sender_customer')</strong>
                            </a>
                        </li>

                        <li class=" @if (session('status.tab') == 'recipient_tab') active @endif">
                            <a style="font-size:13px;" href="#recipient_tab" data-toggle="tab">
                                <strong>@lang('shipping::lang.recipient')</strong>
                            </a>
                        </li>
                        <li class=" @if (session('status.tab') == 'shipping_details') active @endif">
                            <a style="font-size:13px;" href="#shipping_details" data-toggle="tab">
                                <strong>@lang('shipping::lang.shipping_details')</strong>
                            </a>
                        </li>
                        <li class=" @if (session('status.tab') == 'package_details') active @endif">
                            <a style="font-size:13px;" href="#package_details" data-toggle="tab">
                                <strong>@lang('shipping::lang.package_details')</strong>
                            </a>
                        </li>
                        
                        <li class=" @if (session('status.tab') == 'payment_details') active @endif">
                            <a style="font-size:13px;" href="#payment_details" data-toggle="tab">
                                <strong>@lang('shipping::lang.payment_details')</strong>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="tab-content" style="margin-top: 20px;">
            <div class="tab-pane  @if (empty(session('status.tab'))) active @endif @if (session('status.tab') == 'agent_details') active @endif" id="agent_details">
                @include('shipping::add_shipment.agent_details')
            </div>
            <div class="tab-pane  @if (session('status.tab') == 'sender_customer') active @endif" id="sender_customer">
                 @php  $contact_id = $data[0]->customer_id; @endphp   
                 @include('shipping::add_shipment.sender_customer')         
            </div>
            <div class="tab-pane  @if (session('status.tab') == 'recipient') active @endif" id="recipient_tab">
                @include('shipping::add_shipment.recipient')                
            </div>
            <div class="tab-pane  @if (session('status.tab') == 'shipping_details') active @endif" id="shipping_details">
                @include('shipping::add_shipment.shipping_details')
            </div>

            <div class="tab-pane  @if (session('status.tab') == 'package_details') active @endif" id="package_details">
                @include('shipping::add_shipment.package_details')
            </div>
            
            <div class="tab-pane  @if (session('status.tab') == 'payment_details') active @endif" id="payment_details">
                @include('shipping::add_shipment.payment_details')


            </div>

        </div>
    </section>

{!! Form::close() !!}

@endsection


@section('javascript') 
    <script>
    $(document).ready(function() {
        $('#customer_id').change(function() {
            var customer_id = $(this).val();
            
            if(customer_id != ""){
                $.ajax({
                        method: 'POST',
                        url: '{{ action('\Modules\Shipping\Http\Controllers\AddShipmentController@customer_details') }}',
                        dataType: 'json',
                        data: {'id' : customer_id},
                        success: function(result) {
                            var customer = result.customer;
                            
                            $("#address").val(customer.address);
                            $("#mobile").val(customer.mobile);
                        },
                });
            }
        });
        
        $('#recipient_id').change(function() {
            var rec_id = $(this).val();
            
            
            if(rec_id != ""){
                $.ajax({
                        method: 'POST',
                        url: '{{ action('\Modules\Shipping\Http\Controllers\AddShipmentController@recipient_details') }}',
                        dataType: 'json',
                        data: {'id' : rec_id},
                        success: function(result) {
                            var recipient = result.recipient;
                           
                            $("#rec_address").val(recipient.address);
                            $("#rec_mobile_1").val(recipient.mobile_1);
                            $("#rec_mobile_2").val(recipient.mobile_2);
                            $("#rec_postal_code").val(recipient.postal_code);
                            $("#rec_land_no").val(recipient.land_no);
                            $("#rec_landmarks").val(recipient.landmarks);
                            
                        },
                });
            }
        });
        
        $('#shipping_mode,#shipping_partner,#shipping_package').change(function() {
            var shipping_mode = $("#shipping_mode").val();
            var shipping_partner = $("#shipping_partner").val();
            var shipping_package = $("#shipping_package").val();
            
            
            if(shipping_mode != "" && shipping_partner != ""){
                $.ajax({
                        method: 'POST',
                        url: '{{ action('\Modules\Shipping\Http\Controllers\AddShipmentController@getRatePerKg') }}',
                        dataType: 'json',
                        data: {shipping_mode,shipping_partner,shipping_package},
                        success: function(result) {
                            //2b task done updated by dushyant
                            const label = document.getElementById('per_kg_label');
                            if(result.fixed_price == 0){
                                label.innerText = 'Price Per Kg:';
                                document.getElementById('fixed_price_value').value=0;
                            }else{                                
                                document.getElementById('fixed_price_value').value=1;
                                label.innerText = 'Fixed Price:';
                            }
                            $("#per_kg").val(result.cost);
                            $("#constant_value").val(result.constant);
                        },
                });
            }else{
                $("#per_kg").val(0);
                $("#constant_value").val(0);
            }
            $("#per_kg").trigger('change');
            $("#constant_value").trigger('change');
        });
        
        
        $('#constant_value,#length_cm,#width_cm,#height_cm').change(function() {
            var constant_value = $("#constant_value").val();
            var length_cm = $("#length_cm").val();
            var width_cm = $("#width_cm").val();
            var height_cm = $("#height_cm").val();
            
            if(shipping_mode != "" && shipping_partner != "" && length_cm != "" && width_cm != "" && height_cm != ""){
                var volumetric_weight = (length_cm * width_cm * height_cm) / (constant_value * 1000);
                $("#volumetric_weight").val(volumetric_weight);
                var fixed_price_value   = $("#fixed_price_value").val();
                var per_kg              = $("#per_kg").val();
                var shipping_charge = $("#shipping_charge").val();
                var service_fee = $("#service_fee").val();
                if(volumetric_weight != "" && per_kg != "" && service_fee != "" && shipping_charge != ""){
                    if(fixed_price_value == 0){
                        var amount =  (parseFloat(per_kg) * parseFloat(volumetric_weight)) + parseFloat(shipping_charge)+ parseFloat(service_fee);
                    }else{
                        var amount = parseFloat(per_kg) + parseFloat(shipping_charge)+ parseFloat(service_fee);

                    }
                    $(".payment-amount").val(amount);          
                }else{
                    $(".payment-amount").val(0);
                }

            }
        });
        
        $('#shipping_charge,#service_fee').change(function() {
            
            var shipping_charge = $("#shipping_charge").val();
            var service_fee = $("#service_fee").val();
            
            if(shipping_charge != "" && service_fee != ""){
                var total =  parseFloat(shipping_charge)+ parseFloat(service_fee);
                $("#total").val(total);

                /*  calculate amount 
                    point 6 done updated by dushyant
                */
                var fixed_price_value   = $("#fixed_price_value").val();
                var per_kg              = $("#per_kg").val();
                var volumetric_weight   = $("#volumetric_weight").val();

                if(volumetric_weight != "" && per_kg != ""){
                    if(fixed_price_value == 0){
                        var amount =  (parseFloat(per_kg) * parseFloat(volumetric_weight)) + parseFloat(shipping_charge)+ parseFloat(service_fee);
                    }else{
                        var amount = parseFloat(per_kg) + parseFloat(shipping_charge)+ parseFloat(service_fee);

                    }
                    $(".payment-amount").val(amount);          
                }else{
                    $(".payment-amount").val(0);
                }
                
            }else{
                $("#total").val(0);
            }
        });
        
        
        
        
        $('#constant_value,#per_kg,#length_cm,#width_cm,#height_cm,#price_type,#weight_cm').change(function() {
            var constant_value = $("#constant_value").val();
            var length_cm = $("#length_cm").val();
            var width_cm = $("#width_cm").val();
            var height_cm = $("#height_cm").val();
            var price_type = $("#price_type").val();
            
            if(price_type == 'manual'){
                $("#shipping_charge").attr("readonly", false); 
            }else{
                var volumetric_weight  = "";
                if(shipping_mode != "" && shipping_partner != "" && length_cm != "" && width_cm != "" && height_cm != ""){
                    volumetric_weight = (length_cm * width_cm * height_cm) / (constant_value * 1000);
                }
                
                var shipping_charge1 = volumetric_weight * $("#per_kg").val();
                var shipping_charge2 = $("#weight_cm").val() * $("#per_kg").val();
                
                if(shipping_charge1 > shipping_charge2 || shipping_charge2 == "" || shipping_charge2 == "NaN"){
                    $("#shipping_charge").val(shipping_charge1);
                }else if(shipping_charge2 > shipping_charge1 || shipping_charge1 == "" || shipping_charge1 == "NaN"){
                    $("#shipping_charge").val(shipping_charge2);
                }else{
                    $("#shipping_charge").val(shipping_charge1);
                }
                
                if($("#shipping_charge").val() == "NaN" || $("#shipping_charge").val() == ""){
                    $("#shipping_charge").val(0);
                }
                
                $("#shipping_charge").trigger('change');
                
                $("#shipping_charge").attr("readonly", true); 
                
            }
            
            
        });
        
        
    });
    
    
    $(document).ready(function() {
        // Event handler for the "Add Item" button
        $('#addItem').click(function() {
            // Get values from the input fields
            var packageName = $('#package_name').val();
            var lengthCm = $('#length_cm').val();
            var widthCm = $('#width_cm').val();
            var heightCm = $('#height_cm').val();
            var weightCm = $('#weight_cm').val();
            var shippingCharge = $('#shipping_charge').val();
            var total = $('#total').val();
            
            var isValid = true;
            var inputIds = [
                    'package_name',
                    'length_cm',
                    'width_cm',
                    'height_cm',
                    'weight_cm',
                    'per_kg',
                    'volumetric_weight',
                    'price_type',
                    'shipping_charge',
                    'declared_value',
                    'service_fee',
                    'total'
                ];
                
                var packageArray = [];
            
                // Loop through the specified input IDs
                for (var i = 0; i < inputIds.length; i++) {
                    var inputId = inputIds[i];
                    var inputValue = $('#' + inputId).val();

                    // Check if the field is empty
                    if (inputValue === '') {
                        isValid = false;
                        $('#' + inputId).addClass('error');
                    } else {
                        $('#' + inputId).removeClass('error');
                        
                        // hidden items html
                        packageArray.push('<input type="hidden" name="new_' + inputId + '[]" value="' + inputValue + '">');
                    }
                    
                }
                
                if (!isValid) {
                    toastr.error("Please fill all the fields!");
                    return false;
                }
                
                 packageArray.push('<input type="hidden" name="new_package_description[]" value="' + $("#package_description").val() + '">');
    
    
            // Create a new row with the values
            var newRow = '<tr>' +
                '<td>' + packageName + '</td>' +
                '<td>' + lengthCm + '</td>' +
                '<td>' + widthCm + '</td>' +
                '<td>' + heightCm + '</td>' +
                '<td>' + weightCm + '</td>' +
                '<td>' + shippingCharge + '</td>' +
                '<td class="total_cell">' + total + '</td>' +
                '<td><button type="button" class="btn btn-danger removeItem"> - </button>'+
                packageArray.join('') + // Join all hidden inputs into the row
                '</td>' +
                '</tr>';
    
            // Append the new row to the table
            $('#package_items_table').append(newRow);
    
            // Reset input values after adding the row
            $('.to_reset').val('');
            calculateTotal();
        });
        
        $('.contact_modal').on('shown.bs.modal', function() {
            $('.contact_modal')
            .find('.select2')
            .each(function() {
                var $p = $(this).parent();               
                $(this).select2({ 
                    dropdownParent: $p
                });
            });

        });

        
    
        // Event handler for removing a row
        $('#package_items_table').on('click', '.removeItem', function() {
            $(this).closest('tr').remove();
            calculateTotal();
        });
        
        
        
        
        $('.payment-amount').change(function() {
            calculateTotal();
        });
    
        
    });
    
    
    function calculateTotal() {
            var total = 0;
            var paid = 0;
            $('.total_cell').each(function() {
                var value = parseFloat($(this).text());
                if (!isNaN(value)) {
                    total += value;
                }
            });
            
            $('.payment-amount').each(function() {
                var value = parseFloat($(this).val());
                if (!isNaN(value)) {
                    paid += value;
                }
            });
            
            var balance = total- paid;
            
            
            $("#payment_due").html(balance);
            
            if(balance == 0){
                $("#save_formBtn").show();
            }else{
                $("#save_formBtn").hide();
            }
            
            $("#total_payable").val(total);
            
        }

    
    
    </script>
@endsection

