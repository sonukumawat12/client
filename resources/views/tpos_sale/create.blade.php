@extends('layouts.app')
@section('title', 'POS')
@section('content')
    @include('layouts.partials.header-tpos')
    @inject('request', 'Illuminate\Http\Request')
    <script></script>
    <style>
        .box-header {
            padding-bottom: 0px !important;
        }

        .box-body {
            padding-top: 5px !important;
        }

        .select2>.select2-container>.select2-container--default {
            display: none !important;
        }
    </style>
    <!-- Content Header (Page header) -->
    <!-- Main content -->
    @php
        $enable_line_discount = !empty($pos_settings['enable_line_discount']) ? 1 : 0;
    @endphp
    <section class="content no-print">
        <input type="hidden" name="enable_code" id="enable_code"
            value="{{ !empty($search_product_settings['enable_code']) ? 1 : '' }}">
        <input type="hidden" name="enable_rack_number" id="enable_rack_number"
            value="{{ !empty($search_product_settings['enable_rack_number']) ? 1 : '' }}">
        <input type="hidden" name="enable_qty" id="enable_qty"
            value="{{ !empty($search_product_settings['enable_qty']) ? 1 : '' }}">
        <input type="hidden" name="enable_product_cost" id="enable_product_cost"
            value="{{ !empty($search_product_settings['enable_product_cost']) ? 1 : '' }}">
        <input type="hidden" name="enable_product_supplier" id="enable_product_supplier"
            value="{{ !empty($search_product_settings['enable_product_supplier']) ? 1 : '' }}">
        
        <input type="hidden" id="module" value="tpos_add">
        
        @if (!empty($pos_settings['allow_overselling']))
            <input type="hidden" id="is_overselling_allowed">
        @endif
        @if (session('business.enable_rp') == 1)
            <input type="hidden" id="reward_point_enabled">
        @endif
        <div class="row">
            <div
                class="left_div @if (!empty($pos_settings['hide_product_suggestion']) && !empty($pos_settings['hide_recent_trans'])) col-md-10 col-md-offset-1 @else col-md-7 @endif col-sm-12">
                @component('components.widget', ['class' => 'box-success'])
                    @slot('header')
                    <div class='row'>
                         <div class="col-md-3">
                            <div class="col-md-12">
                                <p class="text-right  pull-left"><strong>@lang('sale.location'):</strong>
                                    {{ $default_location->name }}
                                </p>
                            </div>
                            <div class="col-md-12">
                                <h4 class="invoice_no" style="margin: 0; width: 150px;">Invoice No: <span
                                        class="invoice_no_span"></span></h4>
                            </div>
                        </div>
                        <div class="col-md-9">
                            <br>
                            <div class="col-md-6 text-red" style="font-size: 16px;">
                                <b>@lang('lang_v1.customer'):</b> <span class="customer_name"></span>
                            </div>
                            <div class="col-md-6 text-red" style="font-size: 16px;">
                                <b>@lang('lang_v1.due_amount'):</b> <span class="customer_due_amount"> </span>
                            </div>
                        </div>
                        <input type="hidden" id="item_addition_method" value="{{ $business_details->item_addition_method }}">
                        <input type="hidden" id="service_addition_method" value="{{ $business_details->service_addition_method }}">
                    </div>
                       
                    @endslot
                    {!! Form::open(['url' => action('TposController@store'), 'method' => 'post', 'id' => 'add_pos_sell_form']) !!}
                    <input type="hidden" name="price_later" id="price_later" value="0">
                    {!! Form::hidden('location_id', $default_location->id, [
                        'id' => 'location_id',
                        'data-receipt_printer_type' => !empty($default_location->receipt_printer_type)
                            ? $default_location->receipt_printer_type
                            : 'browser',
                        'data-default_accounts' => $default_location->default_payment_accounts,
                    ]) !!}
                    <style>
                        .select2-drop-active {
                            margin-top: -25px;
                        }
                    </style>
                    <!-- /.box-header -->
                    <div class="box-body">
                       

                        <div class="row">
                            <div class="@if (!empty($commission_agent)) col-sm-4 @else col-sm-6 @endif">
                                <div class="form-group" style="width: 100% !important">
                                    <div class="input-group">
                                        <span class="input-group-addon">
                                            <i class="fa fa-user"></i>
                                        </span>
                                        <input type="hidden" id="default_customer_id"
                                            value="{{ !empty($temp_data->default_customer_id) ? $temp_data->default_customer_id : $walk_in_customer['id'] }}">
                                        <input type="hidden" id="default_customer_name"
                                            value="{{ !empty($temp_data->default_customer_name) ? $temp_data->default_customer_name : $walk_in_customer['name'] }}">

                                       
                                        {!! Form::select('contact_id', [], '40', [
                                            'class' => 'form-control mousetrap myNewCustomer',
                                            'id' => 'customer_id',
                                            'placeholder' => 'Enter Customer name / phone',
                                            'required',
                                            'style' => 'width: 100%;',
                                            $disabled,
                                        ]) !!}
                                       
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="pay_term_number" id="pay_term_number"
                                value="{{ !empty($temp_data->pay_term_number) ? $temp_data->pay_term_number : $walk_in_customer['pay_term_number'] }}">
                            <input type="hidden" name="pay_term_type" id="pay_term_type"
                                value="{{ !empty($temp_data->pay_term_type) ? $temp_data->pay_term_type : $walk_in_customer['pay_term_type'] }}">
                            
                            <div class="@if (!empty($commission_agent)) col-sm-4 @else col-sm-6 @endif">
                                <div class="form-group">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <button type="button" class="btn btn-default bg-white btn-flat"
                                                data-toggle="modal" data-target="#configure_search_modal"
                                                title="{{ __('lang_v1.configure_product_search') }}"><i
                                                    class="fa fa-barcode"></i></button>
                                        </div>
                                        {!! Form::text('search_product', null, [
                                            'class' => 'form-control mousetrap',
                                            'id' => 'search_product',
                                            'placeholder' => __('lang_v1.search_product_placeholder'),
                                            'disabled' => is_null($default_location) ? true : false,
                                            'autofocus' => is_null($default_location) ? false : true,
                                        ]) !!}
                                        <span class="input-group-btn">
                                            <button type="button"
                                                class="btn btn-default bg-white btn-flat pos_add_quick_product"
                                                data-href="{{ action('ProductController@quickAdd') }}"
                                                data-container=".quick_add_product_modal"><i
                                                    class="fa fa-plus-circle text-primary fa-lg"></i></button>
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            @php
                        	//  temp cat id and brand id if there is any temp data
                        		$cat_id_suggestion = !empty($temp_data->cat_id_suggestion)?$temp_data->cat_id_suggestion:0;
                        		$brand_id_suggestion = !empty($temp_data->brand_id_suggestion)?$temp_data->brand_id_suggestion:0;
                        	@endphp
                        	@if(!empty($categories))
                        	<div class='col-sm-6'>
                        	    	<select class="select2" id="product_category" style="width:100% !important">
                        
                            			<option value="all">@lang('lang_v1.all_category')</option>
                            
                            			@foreach($categories as $category)
                            				<option value="{{$category['id']}}" @if($category['id'] == $cat_id_suggestion) selected @endif>{{$category['name']}}</option>
                            			@endforeach
                            
                            			@foreach($categories as $category)
                            				@if(!empty($category['sub_categories']))
                            					<optgroup label="{{$category['name']}}">
                            						@foreach($category['sub_categories'] as $sc)
                            							<i class="fa fa-minus"></i> <option value="{{$sc['id']}}">{{$sc['name']}}</option>
                            						@endforeach
                            					</optgroup>
                            				@endif
                            			@endforeach
                            		</select>
                        	</div>
                        	
                        	@endif
	
                            
                        </div>
                        
                        <hr>
                        

                        <div class="row">
                            <div class="col-sm-12 pos_product_div">
                                <input type="hidden" name="sell_price_tax" id="sell_price_tax"
                                    value="{{ !empty($temp_data->sell_price_tax) ? $temp_data->sell_price_tax : $business_details->sell_price_tax }}">
                                <!-- Keeps count of product rows -->
                                <input type="hidden" id="product_row_count"
                                    value="{{ !empty($temp_data->product_row_count) ? $temp_data->product_row_count : 0 }}">
                                @php
                                    $hide_tax = '';
                                    if (session()->get('business.enable_inline_tax') == 0) {
                                        $hide_tax = 'hide';
                                    }
                                @endphp
                                <table class="table table-condensed table-bordered table-striped table-responsive"
                                    id="pos_table">
                                    <thead>
            							<tr>
            							    <th width="24%" class="text-center">
            									@lang('tpos.sub_category')
            								</th>
            								
            								<th width="37%" class="text-center">
            									@lang('sale.product')
            								</th>
            								
                							<th  width="10%">@lang('sale.unit')</th>
                							
                							<th  width="18%" class="text-center">
            									@lang('sale.qty')
            								</th>
            								
            								
            								<th  width="8%" class="text-center">
            									@lang('tpos.total_qty')
            								</th>
            								<th  width="3%" class="text-center"><i class="fa fa-close" aria-hidden="true"></i></th>
            							</tr>
            						</thead>
                                    
                                    <tbody id="saleBody">
                                        
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        @include('tpos_sale.partials.pos_details')
                       
                    </div>
                    <!--  temp cat id and brand id if there is any temp data  -->
                    <input type="hidden" id="cat_id_suggestion" name="cat_id_suggestion"
                        value="{{ !empty($temp_data->cat_id_suggestion) ? $temp_data->cat_id_suggestion : 0 }}">
                    <input type="hidden" id="brand_id_suggestion" name="brand_id_suggestion"
                        value="{{ !empty($temp_data->brand_id_suggestion) ? $temp_data->brand_id_suggestion : 0 }}">
                    <input type="hidden" name="is_pos" value="1" id="is_pos">
                    <input type="hidden" name="is_duplicate" value="0" id="is_duplicate">
                    <input type="hidden" name="was_customer_wallet" id="was_customer_wallet" value=0>
                    <input type="hidden" name="in_customer_wallet" id="in_customer_wallet" value=0>

                    <!-- /.box-body -->
                    {!! Form::close() !!}
                @endcomponent
            </div>
            <div class="col-md-5 col-sm-12 right_div">
                @include('tpos_sale.partials.right_div')
            </div>
        </div>
    </section>
    <!-- This will be printed -->
    <section class="invoice print_section" id="receipt_section">
    </section>
    <div class="modal fade register_details_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade close_register_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade quick_return_modal" id="quick_return_modal" role="dialog"></div>
    <!-- quick product modal -->
    <div class="modal fade quick_add_product_modal" tabindex="-1" role="dialog" aria-labelledby="modalTitle"></div>
    <div class="modal fade patient_prescriptions_modal" role="dialog" aria-labelledby="modalTitle"></div>

    <div class="modal fade contact_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
        @include('contact.create', ['quick_add' => true])
    </div>
    <!-- /.content -->
    @include('tpos_sale.partials.configure_search_modal')
@stop
@section('javascript')
    <script src="{{ asset('js/tpos.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/printer.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/product.js?v=' . $asset_v) }}"></script>
    <script src="{{ asset('js/opening_stock.js?v=' . $asset_v) }}"></script>
    @include('tpos_sale.partials.keyboard_shortcuts')
    <!-- Call restaurant module if defined -->
    @if (in_array('tables', $enabled_modules) ||
            in_array('modifiers', $enabled_modules) ||
            in_array('service_staff', $enabled_modules))
        <script src="{{ asset('js/restaurant.js?v=' . $asset_v) }}"></script>
    @endif
    <script src="{{ asset('js/sell_return.js?v=' . $asset_v) }}"></script>
    <script>
				$('#total_new').hide();

		    $(document).ready(function() {
				var show = $("#show").val();
				var total_new = $("#total_new").val();
	
	$('#show').hide();
			});
        $(document).ready(function() {
			
            setTimeout(() => {
                $(".payment_method").val($(".payment_method option:eq(1)").val());
                $(".payment_method").selectmenu().selectmenu("refresh");
                @can('is_service_staff')
                    $("#res_waiter_id").val("{{ auth()->user()->id }}");
                    $("#res_waiter_id").trigger('change.select2');
                @endcan
            }, 2000);
        });
      
    </script>
    <script>
        $('#toggle_popup').click(function() {
            $.ajax({
                url: '/toggle_popup',
                type: 'get',
                dataType: 'json',
                success: function(result) {}
            });
        });
		var show = $("#show").val();
        $('#show').hide();
        if (show != null) {
            $('#show').show();
            $('#hide').hide();
        }
    </script>
    <script>
        $('.right_div').show();
        $('.left_div').show();
        $("#hide_show_products").click(function() {
            $(".right_div").toggle();
            $('.left_div').toggleClass('col-md-7');
            $('.left_div').toggleClass('col-md-12');
        });
        $('document').ready(function() {
            reset_pos_form();
            $('.payment_types_dropdown').val('cash');
            $('.payment_types_dropdown').trigger('change');
        });
        $(document).on('change', '.payment_types_dropdown', function(e) {
            var payment_type = $(this).val();
            if (payment_type == 'direct_bank_deposit' || payment_type == 'bank_transfer') {
                $('.account_module').removeClass('hide');
            } else {
                $('.account_module').addClass('hide');
            }
        });
    </script>
    <script>
        var product_row = $('input#product_row_count').val();
        var location_id = $('input#location_id').val();
        var customer_id = $('select#customer_id').val();
        var is_direct_sell = false;
        if (
            $('input[name="is_direct_sale"]').length > 0 &&
            $('input[name="is_direct_sale"]').val() == 1
        ) {
            is_direct_sell = true;
        }
        var price_group = '';
        if ($('#price_group').length > 0) {
            price_group = parseInt($('#price_group').val());
        }
        //If default price group present
        if ($('#default_price_group').length > 0 &&
            !price_group) {
            price_group = $('#default_price_group').val();
        }
        //If types of service selected give more priority
        if ($('#types_of_service_price_group').length > 0 &&
            $('#types_of_service_price_group').val()) {
            price_group = $('#types_of_service_price_group').val();
        }
    </script>
    @if (!empty($temp_data->products))
        @php $i = -1; @endphp
        @foreach ($temp_data->products as $product)
            <script>
                $(document).ready(function() {
                    // base_url = '{{ URL::to('/') }}';
                    var qty = parseInt({{ $product->quantity }});
                    var variation_id = parseInt({{ $product->variation_id }});
                    add_pos_product_row(qty, variation_id, location_id);
                })
            </script>
            @php $i++; @endphp
        @endforeach
    @endif
    <script>
        $('#request_approval').click(function() {
            let customer_id = $('#customer_id').val();
            $.ajax({
                method: 'get',
                url: '/customer-limit-approval/send-reuqest-for-approval/' + customer_id,
                data: {},
                success: function(result) {
                    if (result.success === 1) {
                        toastr.success(result.msg)
                    }
                },
            });
        });
        //Update values for each row
        $('#is_duplicate').change(function() {
            getInvoice();
        });

        function getInvoice() {
            $.ajax({
                method: 'get',
                url: '{{ action('SellController@getInvoiveNo') }}',
                data: {
                    location_id: $('#location_id').val()
                },
                success: function(result) {
                    if (parseInt($('#is_duplicate').val()) == 1) {
                        $('.invoice_no_span').text(result.duplicate_invoice_no);
                    } else {
                        $('.invoice_no_span').text(result.orignal_invoice_no);
                    }
                },
            });
        }
        getInvoice();
        @if (auth()->user()->can('unfinished_form.pos'))
            setInterval(function() {
                $.ajax({
                    method: 'POST',
                    url: '{{ action('TempController@saveAddPosTemp') }}',
                    dataType: 'json',
                    data: $('#add_pos_sell_form').serialize(),
                    success: function(data) {},
                });
            }, 10000);
            @if (!empty($temp_data))
                swal({
                    title: "Do you want to load unsaved data?",
                    icon: "info",
                    buttons: {
                        confirm: {
                            text: "Yes",
                            value: false,
                            visible: true,
                            className: "",
                            closeModal: true
                        },
                        cancel: {
                            text: "No",
                            value: true,
                            visible: true,
                            className: "",
                            closeModal: true,
                        }
                    },
                    dangerMode: false,
                }).then((sure) => {
                    if (sure) {
                        window.location.href =
                            "{{ action('TempController@clearData', ['type' => 'add_pos_data']) }}";
                    }
                });
            @endif
        @endif

        // $(".myNewCustomer").select2({
        // 	disabled:'readonly',
        // 	minimumResultsForSearch: Infinity
        // });
        // $(".select2-container").hide();

        $('#customer_id').change(function(){
        	$.ajax({
        		method: 'post',
        		url: '/get-customer-details',
        		data: { contact_id : $(this).val() },
        		success: function(result) {
        		    
        		    result = JSON.parse(result);
        		    
        		    $('.customer_name').text(result['name']);
        			$('.customer_due_amount').text(result['due']);
        		},
        	});
        });

        $('#add_to_customer_wallet').click(function() {
            var change_return = parseFloat($('input#change_return').val().replace(',', ''));
            let was_customer_wallet = parseFloat($('#was_customer_wallet').val());
            $('input#in_customer_wallet').val(parseFloat(was_customer_wallet + change_return));
            $('span.customer_wallet').text(__currency_trans_from_en(parseFloat(was_customer_wallet + change_return),
                true));
        })
        $(document).on('click', '#verify_password_btn', function() {
            $.ajax({
                method: 'post',
                url: '/check_user_password',
                data: {
                    password: $('#verify_password').val()
                },
                success: function(result) {
                    if (result.success == 1) {
                        $('#verify_password_modal').find('.modal-title').empty().text('Enter Invoice');
                        $('#verify_password_modal').find('.modal-body').empty().append(`
				<input type="text" id="return_invoice" name="return_invoice" placeholder="@lang('lang_v1.enter_invoice')"
					style="margin-auto;" class="form-control">
				`);
                        $('#verify_password_modal').find('.modal-footer').empty().append(`
				<button type="button" id="return_invoice_btn" class="btn btn-primary">Submit</button>
        		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
				`);
                    } else {
                        toastr.error('Password does not match');
                    }
                },
            });
        });
        $(document).on('click', '#return_invoice_btn', function() {
            let return_invoice = $('#return_invoice').val();
            $.ajax({
                method: 'get',
                url: '/sell-return/add/' + return_invoice,
                data: {},
                success: function(result) {
                    if (result.success == 0) {
                        $('#verify_password_modal').modal('hide')
                        toastr.error(result.msg);
                        return false;
                    } else {
                        $('#verify_password_modal').modal('hide');
                        resetVerifyPasswordModal();
                        $('.quick_return_modal').empty().append(result);
                        $('.quick_return_modal').modal('show');
                        $('#pos_invoice_return').val($('.invoice_no_span').text());
                    }
                },
            });
        });

        function resetVerifyPasswordModal() {
            $('#verify_password_modal').find('.modal-title').empty().text('Enter Password');
            $('#verify_password_modal').find('.modal-body').empty().append(`
		<input type="password" id="verify_password" name="verify_password" placeholder="@lang('lang_v1.enter_password')"
		style="margin-auto;" class="form-control">
		`);
            $('#verify_password_modal').find('.modal-footer').empty().append(`
		<button type="button" id="verify_password_btn" class="btn btn-primary">@lang('lang_v1.verify')</button>
		<button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
	`);
        }
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('form#sell_return_form').validate();
            update_sell_return_total();
        });
        $(document).on('click', '#sell_return_submit', function(e) {
            e.preventDefault();
            var data = $('form#sell_return_form').serialize();
            $.ajax({
                method: 'POST',
                url: "{{ action('SellReturnController@savePosReturn') }}",
                dataType: 'json',
                data: data,
                success: function(result) {
                    var location_id = $('input#location_id').val();
                    if (result.success == true) {
                        $('.quick_return_modal').modal('hide');
                        jQuery.each(result.returns, function(id, obj) {
                            id = Object.keys(obj);
                            qty = Object.values(obj);
                            add_pos_product_row(qty * -1, id, location_id);
                            $('input#product_row_count').val(parseInt($(
                                'input#product_row_count').val()) + 1);
                        })
                    } else {
                        toastr.error(result.msg);
                    }
                },
            });
        });
        $(document).on('change', 'input.return_qty, #discount_amount, #discount_type', function() {
            update_sell_return_total()
        });

        function update_sell_return_total() {
            var net_return = 0;
            $('table#sell_return_table tbody tr').each(function() {
                var quantity = __read_number($(this).find('input.return_qty'));
                var unit_price = __read_number($(this).find('input.unit_price'));
                var subtotal = quantity * unit_price;
                $(this).find('.return_subtotal').text(__currency_trans_from_en(subtotal, true));
                net_return += subtotal;
            });
            var discount = 0;
            if ($('#discount_type').val() == 'fixed') {
                discount = __read_number($("#discount_amount"));
            } else if ($('#discount_type').val() == 'percentage') {
                var discount_percent = __read_number($("#discount_amount"));
                discount = __calculate_amount('percentage', discount_percent, net_return);
            }
            discounted_net_return = net_return - discount;
            var tax_percent = $('input#tax_percent').val();
            var total_tax = __calculate_amount('percentage', tax_percent, discounted_net_return);
            var net_return_inc_tax = total_tax + discounted_net_return;
            $('input#tax_amount').val(total_tax);
            $('span#total_return_discount').text(__currency_trans_from_en(discount, true));
            $('span#total_return_tax').text(__currency_trans_from_en(total_tax, true));
            $('span#net_return').text(__currency_trans_from_en(net_return_inc_tax, true));
        }

        function add_pos_product_row(qty, variation_id, location_id) {
            $.ajax({
                method: 'GET',
                url: '/sells/pos/get_product_row_temp/' + variation_id + '/' + location_id + '/' + qty,
                data: {
                    product_row: $('input#product_row_count').val(),
                    customer_id: customer_id,
                    is_direct_sell: is_direct_sell,
                    price_group: price_group,
                    purchase_line_id: null
                },
                dataType: 'json',
                success: function(result) {
                    if (result.success) {
                        $('table#pos_table tbody')
                            .append(result.html_content)
                            .find('input.pos_quantity');
                        //increment row count
                        var this_row = $('table#pos_table tbody')
                            .find('tr')
                            .last();
                        pos_each_row(this_row);
                        //For initial discount if present
                        var line_total = __read_number(this_row.find('input.pos_line_total'));
                        this_row.find('span.pos_line_total_text').text(line_total);
                        pos_total_row();
                        //Check if multipler is present then multiply it when a new row is added.
                        if (__getUnitMultiplier(this_row) > 1) {
                            this_row.find('select.sub_unit').trigger('change');
                        }
                        if (result.enable_sr_no == '1') {
                            var new_row = $('table#pos_table tbody')
                                .find('tr')
                                .last();
                            new_row.find('.add-pos-row-description').trigger('click');
                        }
                        round_row_to_iraqi_dinnar(this_row);
                        __currency_convert_recursively(this_row);
                        $('input#search_product')
                            .focus()
                            .select();
                        //Used in restaurant module
                        if (result.html_modifier) {
                            $('table#pos_table tbody')
                                .find('tr')
                                .last()
                                .find('td:first')
                                .append(result.html_modifier);
                        }
                        //scroll bottom of items list
                        $(".pos_product_div").animate({
                            scrollTop: $('.pos_product_div').prop("scrollHeight")
                        }, 1000);
                    } else {
                        toastr.error(result.msg);
                        $('input#search_product')
                            .focus()
                            .select();
                    }
                }
            });
        }
		$('#show').hide();
    </script>
@endsection
