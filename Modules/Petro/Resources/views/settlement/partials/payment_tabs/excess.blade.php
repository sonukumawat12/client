<div class="col-md-12">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('excess_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('excess_amount', null, ['class' => 'form-control excess_fields input_number
                excess_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
                <div class="text-center text-red excess_amount_err hidden">
                  
                  <span class="total_amount">Not Allowed. Already Shortage amount entered</span>
              </div>
            </div>
            
        </div>
        <div class="col-md-6">
                <div class="form-group">
                  {!! Form::label("excess_note", __('lang_v1.payment_note') . ':') !!}
                  {!! Form::textarea("excess_note", null, ['class' => 'form-control cash_fields', 'rows' => 3]); !!}
                </div>
            </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-primary excess_add_btn"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br><br>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="excess_table">
            <thead>
                <tr>
                    <th></th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('lang_v1.note') </th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody id="excess_table_body">
                @php
                    $excess_total = $settlement_excess_payments->sum('amount');
                @endphp
                @foreach ($settlement_excess_payments as $excess_payment)
                    <tr>
                        <td></td>
                        <td class="excess_amount">{{number_format($excess_payment->amount, $currency_precision)}}</td>
                        <td class="excess_amount">{{$excess_payment->note}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_excess_payment" data-href="/petro/settlement/payment/delete-excess-payment/{{$excess_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td style="text-align: right; font-weight: bold;">@lang('petro::lang.total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="excess_total">
                       {{number_format($excess_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$excess_total}}" name="excess_total" id="excess_total">
            </tfoot>
        </table>
    </div>
</div>




<script>
    $(document).ready(function(){
        
       
        $("#excess_customer_id").val($("#excess_customer_id option:eq(0)").val()).trigger('change');
        
    });
    
    
function add_payment(add_amount) {
    add_amount = parseFloat(add_amount);
    total_balance = parseFloat($("#total_balance").val().replace(",", ""));
    total_paid = parseFloat($("#total_paid").val());
    total_balance = total_balance - add_amount;
    total_paid = total_paid + add_amount;
    $("#total_balance").val(total_balance);
    $("#total_paid").val(total_paid);
    $(".total_balance").text(__number_f(total_balance, false, false, __currency_precision));
    $(".total_paid").text(__number_f(total_paid, false, false, __currency_precision));
//    if (total_balance === 0) {
//        $("#settlement_save_btn").removeClass("hide");
//    } else {
//        $("#settlement_save_btn").addClass("hide");
//    }
    show_hide_excess_shortage_tab();
    calculateDenoms(add_amount);
}

function calculateTotal(table_name, class_name_td, output_element) {
    let total = 0.0;
    $(table_name + " tbody")
        .find(class_name_td)
        .each(function () {
            total += parseFloat(__number_uf($(this).text()));
        });
    $(output_element).text(__number_f(total, false, false, __currency_precision));
}
</script>