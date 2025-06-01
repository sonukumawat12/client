@extends('layouts.app')
@section('title', __('petro::lang.settlement'))

@section('content')
@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
$meeter_precision = 3;
@endphp


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <h4 class="page-title pull-left">@lang( 'petro::lang.settlement', ['contacts' => __('petro::lang.mange_settlement') ])</h4>
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.settlement')</a></li>
                    <li><span>@lang( 'petro::lang.settlement', ['contacts' => __('petro::lang.mange_settlement') ])</span></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content main-content-inner">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('settlement_no', __('petro::lang.settlement_no') . ':') !!}
                    {!! Form::text('settlement_no', !empty($active_settlement) ? $active_settlement->settlement_no :
                    $settlement_no, ['class' => 'form-control', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('location_id', $business_locations, !empty($active_settlement) ?
                    $active_settlement->location_id : (!empty($default_location) ? $default_location : null), ['class'
                    => 'form-control select2', 'id' => 'location_id',
                    'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('pump_operator', __('petro::lang.pump_operator').':') !!}
                    {!! Form::select('pump_operator_id', $pump_operators, !empty($active_settlement) ?
                    $active_settlement->pump_operator_id : null, ['class' => 'form-control select2', 'id' =>
                    'pump_operator_id',
                    'placeholder' => __('petro::lang.all')]); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('transaction_date', __( 'petro::lang.transaction_date' ) . ':*') !!}
                    {!! Form::text('transaction_date', null, ['class' =>
                    'form-control transaction_date', 'required',
                    'placeholder' => __(
                    'petro::lang.transaction_date' ) ]); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('work_shift', __('petro::lang.work_shift').':') !!}
                    {!! Form::select('work_shift[]', $wrok_shifts, !empty($active_settlement) ?
                    $active_settlement->work_shift : null, ['class' => 'form-control select2', 'id' => 'work_shift',
                    'multiple']); !!}
                </div>
            </div>


            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('note', __('petro::lang.note') . ':') !!}
                    {!! Form::text('note', !empty($active_settlement) ? $active_settlement->note : null, ['class' =>
                    'form-control note',
                    'placeholder' => __(
                    'petro::lang.note' ) ]); !!}
                </div>
            </div>
            
            <input type="hidden" id="is_edit" value="1">
            <input type="hidden" id="no_change" value="{{request()->no_change}}">
            
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary below_box', 'id' => 'below_box'])
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    <li class="active">
                        <a href="#meter_sale_tab" class="meter_sale_tab" data-toggle="tab">
                            <i class="fa fa-tachometer"></i> <strong>@lang('petro::lang.meter_sale')</strong>
                        </a>
                    </li>

                    <li>
                        <a href="#other_sale_tab" class="other_sale_tab" style="" data-toggle="tab">
                            <i class="fa fa-balance-scale"></i> <strong>
                                @lang('petro::lang.other_sale') </strong>
                        </a>
                    </li>

                    <li>
                        <a href="#other_income_tab" class="other_income_tab" style="" data-toggle="tab">
                            <i class="fa fa-thermometer"></i> <strong>
                                @lang('petro::lang.other_income') </strong>
                        </a>
                    </li>

                    <li>
                        <a href="#customer_payment_tab" class="customer_payment_tab" style="" data-toggle="tab">
                            <i class="fa fa-money"></i> <strong>
                                @lang('petro::lang.customer_payment') </strong>
                        </a>
                    </li>

                    <li>
                        <a href="#payment_tab" class="payment_tab" style="" data-toggle="tab">
                            <i class="fa fa-book"></i> <strong>
                                @lang('petro::lang.payment') </strong>
                        </a>
                    </li>

                </ul>
                <div class="tab-content">
                    <div class="tab-pane active" id="meter_sale_tab">
                        @include('petro::settlement.partials.meter_sale', ['edit' => 1])
                    </div>

                    <div class="tab-pane" id="other_sale_tab">
                        @include('petro::settlement.partials.other_sale')
                    </div>

                    <div class="tab-pane" id="other_income_tab">
                        @include('petro::settlement.partials.other_income')
                    </div>

                    <div class="tab-pane" id="customer_payment_tab">
                        @include('petro::settlement.partials.customer_payment')
                    </div>

                    <div class="tab-pane" id="payment_tab">
                        @include('petro::settlement.partials.payment')
                    </div>

                </div>
            </div>
        </div>
    </div>

    @endcomponent

    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade add_payment" role="dialog" aria-labelledby="gridSystemModalLabel" style="overflow-y: auto;">
    </div>
    <div class="modal fade preview_settlement" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div id="settlement_print"></div>
    
</section>
<!-- /.content -->

<div class="modal fade edit_disabled" role="dialog" 
        aria-labelledby="gridSystemModalLabel">
    <div class="modal-dialog">
      <div class="modal-content">

        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">@lang('petro::lang.edit_disabled')</h4>
        </div>

        <!-- Modal Body -->
        <div class="modal-body" style="padding: 50px">
          <p class="text-bold">@lang('petro::lang.edit_disabled_exp')</p>
          <p class="text-center">{!! $can_edit_details[1] !!}</p>
        </div>

      </div>
    </div>
  </div>

@endsection
@section('javascript')
<script src="{{url('Modules/Petro/Resources/assets/js/app.js?v=20')}}"></script>
<script src="{{url('Modules/Petro/Resources/assets/js/payment.js?v=20')}}"></script>
<script>
    $(document).on("click", ".credit_sale_add_updated", function () {
  console.log('789');

  if ($("#credit_sale_amount").val() == "") {
    toastr.error("Please enter amount");
    return false;
  }
  var credit_sale_customer_id = $("#credit_sale_customer_id").val();
  var customer_name = $("#credit_sale_customer_id :selected").text();
  var credit_sale_product_id = $("#credit_sale_product_id").val();
  var credit_sale_product_name = $("#credit_sale_product_id :selected").text();
  if (
    $("#customer_reference_one_time").val() !== "" &&
    $("#customer_reference_one_time").val() !== null &&
    $("#customer_reference_one_time").val() !== undefined
  ) {
    var customer_reference = $("#customer_reference_one_time").val();
  } else {
    var customer_reference = $("#customer_reference").val();
  }
  var settlement_no = $("#settlement_no").val();
  var order_date = $("#order_date").val();
  var order_number = $("#order_number").val();

  var credit_sale_price = __read_number($("#unit_price"));
  var credit_unit_discount = __read_number($("#unit_discount")) ?? 0;
  var credit_sale_qty = __read_number($("#credit_sale_qty")) ?? 0;
  var credit_total_amount = __read_number($("#credit_total_amount")) ?? 0;
  var credit_total_discount = __read_number($("#credit_discount_amount")) ?? 0;
  var credit_sub_total = __read_number($("#credit_sale_amount")) ?? 0;

  var outstanding = $(".current_outstanding").text();
  var credit_limit = $(".credit_limit").text();
  var credit_note = $("#credit_note").val();
  var is_edit = $("#is_edit").val() ?? 0;

  $.ajax({
    method: "post",
    url: "/petro/settlement/payment/save-credit-sale-payment",
    data: {
      settlement_no: settlement_no,
      customer_id: credit_sale_customer_id,
      product_id: credit_sale_product_id,
      order_number: order_number,
      order_date: order_date,

      price: credit_sale_price,
      unit_discount: credit_unit_discount,
      qty: credit_sale_qty,
      amount: credit_total_amount,
      sub_total: credit_sub_total,
      total_discount: credit_total_discount,
      outstanding: outstanding,
      credit_limit: credit_limit,
      customer_reference: customer_reference,
      note: credit_note,
      is_edit: is_edit,
    },
    success: function (result) {
      if (!result.success) {
        toastr.error(result.msg);
      } else {
        settlement_credit_sale_payment_id =
          result.settlement_credit_sale_payment_id;
        add_payment_updated(credit_total_amount - credit_total_discount);
        $("#credit_sale_table tbody").prepend(
          `
                    <tr> 
                        <td>` +
            customer_name +
            `</td>
                        <td>` +
            outstanding +
            `</td>
                        <td>` +
            credit_limit +
            `</td>
                        <td>` +
            order_number +
            `</td>
                        <td>` +
            order_date +
            `</td>
                        <td>` +
            customer_reference +
            `</td>
                        <td>` +
            credit_sale_product_name +
            `</td>
                        <td>` +
            __number_f(credit_sale_price, false, false, __currency_precision) +
            `</td>
                        <td>` +
            __number_f(credit_sale_qty, false, false, __currency_precision) +
            `</td>
                        <td class="credit_sale_amount">` +
            __number_f(
              credit_total_amount,
              false,
              false,
              __currency_precision
            ) +
            `</td>
                        
                        <td class="credit_tbl_discount_amount">` +
            __number_f(
              credit_total_discount,
              false,
              false,
              __currency_precision
            ) +
            `</td>
                        <td class="credit_tbl_total_amount">` +
            __number_f(credit_sub_total, false, false, __currency_precision) +
            `</td>
                        
                        
                        <td>` +
            credit_note +
            `</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_credit_sale_payment" data-href="/petro/settlement/payment/delete-credit-sale-payment/` +
            settlement_credit_sale_payment_id +
            `"><i class="fa fa-times"></i></button>
                        </td>
                    </tr>
                `
        );
        $("#customer_reference_one_time").val("").trigger("change");
        $(".credit_sale_fields").val("");
        $(".cash_fields").val("");
        $("#credit_sale_product_id").trigger("change");
        $("#order_number").val(order_number);
        calculateTotal(
          "#credit_sale_table",
          ".credit_sale_amount",
          ".credit_sale_total"
        );
        calculateTotal(
          "#credit_sale_table",
          ".credit_tbl_discount_amount",
          ".credit_tb_discount_total"
        );
        calculateTotal(
          "#credit_sale_table",
          ".credit_tbl_total_amount",
          ".credit_tbl_amount_total"
        );
      }
    },
  });
});

function add_payment_updated(add_amount) {
    add_amount = parseFloat(add_amount);
    total_balance = parseFloat($("#total_balance").val());
    total_paid = parseFloat($("#total_paid").val());
    total_balance = total_balance + add_amount;
    console.log('total_balance',total_balance);
    total_paid = total_paid + add_amount;
    $("#total_balance").val(__number_f(total_balance, false, false, __currency_precision));
    $("#total_paid").val(total_paid);
    $(".total_balance").text(__number_f(total_balance, false, false, __currency_precision));
    $(".total_paid").text(__number_f(total_paid, false, false, __currency_precision));
  /* if (total_balance === 0) {
        $("#settlement_save_btn").removeClass("hide");
    } else {
        $("#settlement_save_btn").addClass("hide");
    }*/
    show_hide_excess_shortage_tab();
    calculateDenoms(add_amount);
}
</script>
<script>
    
    @if($can_edit_details[0] == 0)
        $('.edit_disabled').modal({
            backdrop: 'static',
            keyboard: false
        });
    @endif

    @if(!empty($active_settlement))
    $('.transaction_date').datepicker("setDate", "{{\Carbon::parse($active_settlement->transaction_date)->format('m/d/Y')}}");
    @else
    $('.transaction_date').datepicker("setDate", new Date());
    @endif
    $('#customer_payment_cheque_date').datepicker("setDate", new Date());
    $('#location_id').select2();
    $('#shif_time_in').datetimepicker({
        format: 'LT'
    });
    $('#shif_time_out').datetimepicker({
        format: 'LT'
    });
    $('#item').select2();
    $('#store_id').select2();


    $('#add_payment').click(function() {
        /**
         * @ChangedBy Afes
         * @Date 25-05-2021
         * @Date 02-06-2021
         * @Task 12700
         * @Task 127004
         */
        url = $(this).data('href') + '&operator_id=' + $('#pump_operator_id').val();
        $('.add_payment').load(url, function() {
            $('.add_payment').modal({
                backdrop: 'static',
                keyboard: false
            });

            if (($('.add_payment #shortage_amount').val() && $('.add_payment #shortage_amount').length) || $('.add_payment #shortage_table tbody tr').length) {
                $('.add_payment #excess_amount').prop('disabled', true);
                $('.add_payment .excess_amount_err').removeClass('hidden');
            } else {
                $('.add_payment #excess_amount').prop('disabled', false);
                $('.add_payment .excess_amount_err').addClass('hidden');

            }

            if (($(".add_payment #excess_amount").length && $(".add_payment #excess_amount").val()) || $('.add_payment #excess_table tbody tr').length) {
                $('.add_payment #shortage_amount').prop('disabled', true);
                $('.add_payment .shortage_amount_err').removeClass('hidden');

            } else {
                $('.add_payment #shortage_amount').prop('disabled', false);
                $('.add_payment .shortage_amount_err').addClass('hidden');
            }


            $('#shortage_amount').on('input', function() {
                if ($(this).val().length || $('.add_payment #shortage_table tbody tr').length) {
                    $('#excess_amount').prop('disabled', true);
                    $('.excess_amount_err').removeClass('hidden');
                } else {
                    $('#excess_amount').prop('disabled', false);
                    $('.excess_amount_err').addClass('hidden');

                }
            });

            $('#excess_amount').on('input', function() {
                if ($(this).val().length || $('.add_payment #excess_table tbody tr').length) {
                    $('#shortage_amount').prop('disabled', true);
                    $('.shortage_amount_err').removeClass('hidden');

                } else {
                    $('#shortage_amount').prop('disabled', false);
                    $('.shortage_amount_err').addClass('hidden');
                }

            });
            var total_amount =  parseInt($('.total_balance').text());
            if( total_amount > 0){
              $('.excess_amount').prop('disabled', true);
            }else{
              $('.excess_amount').prop('disabled', false);
            }
            if (total_amount === 0) {
                $("#settlement_save_btn").removeClass("hide");
            } else {
                $("#settlement_save_btn").addClass("hide");
            }
            show_hide_excess_shortage_tab();
        });
    });

    $('#note, #work_shift, #transaction_date, #pump_operator_id, #location_id').change(function() {
        $.ajax({
            method: 'put',
            url: "{{action('\Modules\Petro\Http\Controllers\SettlementController@update', $active_settlement->id)}}",
            data: {
                note: $('#note').val(),
                work_shift: $('#work_shift').val(),
                transaction_date: $('#transaction_date').val(),
                pump_operator_id: $('#pump_operator_id').val(),
                location_id: $('#location_id').val()
            },
            success: function(result) {
                if (result.success == 1) {
                    toastr.success(result.msg);
                } else {
                    toastr.error(result.msg);
                }
            },
        });
    })


    $('#card_customer_id').select2();
    $('#work_shift').select2();
    $('#customer_payment_customer_id').select2();
    $('#settlement_print').css('visibility', 'hidden');
</script>


<script>
    $(document).on('click', '#save_edit_price_other_income_btn', function() {
        var edit_price = $('#other_income_edit_price').val();

        $('#other_income_price').val(edit_price);
        $('#other_income_edit_price').val('0');
        $('#edit_price_other_income').modal('hide');
    });

    $('#other_sale_qty').change(function() {
        if (parseFloat($(this).val()) > parseFloat($('#balance_stock').val())) {
            toastr.error('Out of Stock');
            $(this).val('').focus();
        }
    })

</script>
@endsection