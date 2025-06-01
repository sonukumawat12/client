
<div class="col-md-12">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('drawing_payments_bank', __('petro::lang.account').':') !!}
                {!! Form::select('drawing_payments_bank', $drawings_acc, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('drawing_payments_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('drawing_payments_amount', null, ['class' => 'form-control drawing_payments_fields cust_input_number
                drawing_payments_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        
        <div class="col-md-5">
            <div class="form-group">
              {!! Form::label("drawing_payments_note", __('lang_v1.payment_note') . ':') !!}
              {!! Form::textarea("drawing_payments_note", null, ['class' => 'form-control drawing_payments_fields', 'rows' => 3]); !!}
            </div>
        </div> 
          
        <div class="col-md-1">
            <button type="button" class="btn btn-primary drawing_payments_add"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
        
    </div>
    
</div>
<br><br>

<div class="row" style="margin-top: 10px">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="drawing_payments_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.account' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.note' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cash_total = $settlement_drawings_payments->sum('amount');
                @endphp
                @foreach ($settlement_drawings_payments as $cash_payment)
                    <tr>
                        <td>{{$cash_payment->loan_account_name}}</td>
                        <td class="drawing_payments_amount">{{number_format($cash_payment->amount, $currency_precision)}}</td>
                        <td>{{$cash_payment->note}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_drawing_payment" data-href="/petro/settlement/payment/delete-drawing-payment/{{$cash_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
               
            </tbody>

            <tfoot>
                <tr>
                    <td style="text-align: right; font-weight: bold;">@lang('petro::lang.settlement_drawing_payments_total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="drawing_payments_total">
                    {{number_format($cash_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$cash_total}}" name="drawing_payments_total" id="drawing_payments_total">
            </tfoot>
        </table>
    </div>
</div>



<script>
    $('#drawing_payments_bank').select2();
    $(document).ready(function(){
        $("#drawing_payments_bank").val($("#drawing_payments_bank option:eq(0)").val()).trigger('change');
        
    });
</script>