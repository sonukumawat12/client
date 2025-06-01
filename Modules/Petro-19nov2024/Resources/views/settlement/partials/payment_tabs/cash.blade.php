@php

    $cashdenoms = $settlement->cash_denomination;
    if(!empty($cashdenoms)){
        $cashdenoms = json_decode($cashdenoms,true);
    }else{
        $cashdenoms = [];
    }

@endphp


<div class="col-md-12">
    <div class="row">
        <div class="col-md-3 cash_to_disable">
            <div class="form-group">
                {!! Form::label('cash_customer_id', __('petro::lang.customer').':') !!}
                {!! Form::select('cash_customer_id', $customers, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3 cash_to_disable">
            <div class="form-group 123">
                {!! Form::label('cash_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('cash_amount', null, ['class' => 'form-control cash_fields cust_input_number
                cash_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        <div class="col-md-5 cash_to_disable">
            <div class="form-group">
              {!! Form::label("cash_note", __('lang_v1.payment_note') . ':') !!}
              {!! Form::textarea("cash_note", null, ['class' => 'form-control cash_fields', 'rows' => 3]); !!}
            </div>
        </div>
        
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary cash_to_disable cash_add_updated"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
        
        @if(!empty($cash_denoms))
        <div class="col-md-4">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('enable_cash_denoms', '1', !empty($cashdenoms) ? true : false,
                    [ 'class' => 'input-icheck','id' => 'enable_cash_denoms']); !!} {{ __( 'lang_v1.enable_cash_denoms' ) }}
                </label>
            </div>
        </div>
        <div class="col-md-4 denoms_row">
            <div class="checkbox">
                <label>
                    {!! Form::checkbox('calculate_cash', '1', false,
                    [ 'class' => 'input-icheck','id' => 'calculate_cash']); !!} {{ __( 'lang_v1.calculate' ) }}
                </label>
            </div>
        </div>
        @endif
        
        
        
        
    </div>
    <div class="row denoms_row" id="denoms_row" style="margin-top: 10px">
        <div class="col-md-12">
            @foreach($cash_denoms as $denom)
            @php  
                $index = array_search($denom, array_column($cashdenoms, 'value'));
                if ($index !== false) {
                    $qty = $cashdenoms[$index]['qty'];
                    $denomtotal = $qty*$denom;
                    
                } else {
                    $qty = null;
                    $denomtotal = null;
                }
            @endphp
                <div class="row">
                    <input type="hidden" value="{{$denom}}" class="denom_value">
                    <div class="col-md-4">
                        <div class="form-group">
                            <label>{{number_format($denom, $currency_precision)}}</label>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::number('qty[]', $qty, ['class' => 'form-control denom_qty', 'required',
                            'placeholder' => __(
                            'petro::lang.qty' ) ]); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <div class="form-group">
                            {!! Form::text('total_amount[]', $denomtotal, ['class' => 'form-control denom_amt', 'required','readonly',
                            'placeholder' => __(
                            'petro::lang.total_amount' ) ]); !!}
                        </div>
                    </div>
                </div>
            @endforeach
                <div class="row denoms_totals">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __( 'lang_v1.total' ) }}</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::text('grand_total', null, ['class' => 'form-control denom_total', 'required','readonly',
                            'placeholder' => __(
                            'petro::lang.total' ),'style' => 'color:green;font-weight:bold;' ]); !!}
                        </div>
                    </div>
                    
                    <div class="col-md-3">
                        <div class="form-group">
                            <label>{{ __( 'lang_v1.balance' ) }}</label>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            {!! Form::text('grand_bal', null, ['class' => 'form-control denom_bal', 'required','readonly',
                            'placeholder' => __(
                            'petro::lang.balance' ),'style' => 'color:red;font-weight:bold;' ]); !!}
                        </div>
                    </div>
                    
                </div>
        </div>
        
    </div>
</div>
<br><br>

<div class="row" style="margin-top: 10px">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="cash_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.cusotmer_name' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('lang_v1.note') </th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cash_total = $settlement_cash_payments->sum('amount');
                @endphp
                @foreach ($settlement_cash_payments as $cash_payment)
                    <tr>
                        <td>{{$cash_payment->customer_name}}</td>
                        <td class="cash_amount">{{number_format($cash_payment->amount, $currency_precision)}}</td>
                        <td>{{$cash_payment->note}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cash_payment" data-href="/petro/settlement/payment/delete-cash-payment/{{$cash_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
                @if (!empty($total_daily_collection))
                    <tr>
                    <td class="text-red">@lang('petro::lang.daily_collections')</td>
                    <td class="text-red cash_amount">{{number_format($total_daily_collection, $currency_precision)}}</td>
                    <td></td>
                </tr>
                @endif
            </tbody>

            <tfoot>
                <tr>
                    <td style="text-align: right; font-weight: bold;">@lang('lang_v1.settlement_cash_total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="cash_total">
                    {{number_format($cash_total+$total_daily_collection, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$cash_total}}" name="cash_total" id="cash_total">
            </tfoot>
        </table>
    </div>
</div>



@if(!empty($cashdenoms))
<script>
      $(".denoms_row").show();
</script>
@else
<script>
    $(".denoms_row").hide();
    
</script>
@endif

<script>
    $(document).on("click", ".cash_add_updated", function () {
        console.log('123');
        if ($("#cash_amount").val() == "") {
            toastr.error("Please enter amount");
            return false;
        }
        var cash_customer_id = $("#cash_customer_id").val();
        var cash_amount = $("#cash_amount").val();
        var settlement_no = $("#settlement_no").val();
        var customer_name = $("#cash_customer_id :selected").text();
        var cash_note = $("#cash_note").val();
        var is_edit = $("#is_edit").val() ?? 0;

        $.ajax({
            method: "post",
            url: "/petro/settlement/payment/save-cash-payment",
            data: {
            customer_id: cash_customer_id,
            amount: cash_amount,
            settlement_no: settlement_no,
            note: cash_note,
            is_edit: is_edit,
            },
            success: function (result) {
            if (!result.success) {
                toastr.error(result.msg);
            } else {
                if ($("#calculate_cash").is(":checked")) {
                $(".denoms_totals").hide();
                $(".cash_to_disable").hide();
                $("#cash_amount").prop("readonly", true);
                } else {
                $(".denoms_totals").show();
                $(".cash_to_disable").show();
                $("#cash_amount").prop("readonly", false);
                }

                console.log("here is cash add data ==>", result);
                settlement_cash_payment_id = result.settlement_cash_payment_id;
                add_payment(cash_amount);
                $("#cash_table tbody").append(
                `
                            <tr> 
                                <td>` +
                    customer_name +
                    `</td>
                                <td class="cash_amount">` +
                    __number_f(cash_amount, false, false, __currency_precision) +
                    `</td>
                                <td>` +
                    cash_note +
                    `</td>
                                <td><button type="button" class="btn btn-xs btn-danger delete_cash_payment" data-href="/petro/settlement/payment/delete-cash-payment/` +
                    settlement_cash_payment_id +
                    `"><i class="fa fa-times"></i></button>
                                </td>
                            </tr>
                        `
                );
                $(".cash_fields").val("");
                calculateTotal("#cash_table", ".cash_amount", ".cash_total");
            }
            },
        });
    });
</script>
<script>
    $('#cash_customer_id').select2();
    $(document).ready(function(){
        
        $("#cash_customer_id").val($("#cash_customer_id option:eq(0)").val()).trigger('change');
        
        $('#enable_cash_denoms').change(function() {
            if ($(this).is(':checked')) {
                $(".denoms_row").show();
            } else {
              $(".denoms_row").hide();
              $('#calculate_cash').prop('checked', false).trigger('change');
            }
            calculateDenoms();
        });
        
        
        $('#calculate_cash').change(function() {
            var text = $('.cash_total').text();
            var val = parseFloat(text.replace(',', ''));
            
            if ($(this).is(':checked')) {
                $(".denoms_totals").hide();
                if(val > 0){
                    $(".cash_to_disable").hide();
                }else{
                    $(".cash_to_disable").show();
                }
            } else {
              $(".denoms_totals").show();
              $(".cash_to_disable").show();
            }
            calculateDenoms();
        });
          
          $(document).on('keyup', '.denom_qty', function() {
            var $row = $(this).closest('.row');
            var denom_value = parseFloat($row.find('.denom_value').val());
            var qty = parseFloat($(this).val());
            var ttotal = 0;
            
            if (!isNaN(denom_value) || !isNaN(qty)) {
                var total_amount = denom_value * qty;
                if(isNaN(total_amount)){
                    total_amount = 0;
                }
                
                ttotal = total_amount;
                
                total_amount = total_amount.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                
                $row.find('.denom_amt').val(total_amount);
                
                if ($('#calculate_cash').is(':checked')) {
                    $("#cash_amount").val(ttotal);
                }
                
              } else {
                $row.find('.denom_amt').val(''); // Reset denom_amt if input values are invalid
              }
              
              calculateDenoms();
              
          });
          
        
    });
</script>