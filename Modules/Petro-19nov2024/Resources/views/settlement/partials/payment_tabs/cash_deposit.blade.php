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
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cash_deposit_bank', __('petro::lang.bank').':') !!}
                {!! Form::select('cash_deposit_bank', $bank_accounts, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cash_deposit_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('cash_deposit_amount', null, ['class' => 'form-control cash_deposit_fields cust_input_number
                cash_deposit_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cash_deposit_account', __( 'petro::lang.receipt_no' ) ) !!}
                {!! Form::text('cash_deposit_account', null, ['class' => 'form-control cash_deposit_fields cust_input_number
                cash_deposit_account', 'required',
                'placeholder' => __(
                'petro::lang.receipt_no' ) ]); !!}
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('cash_deposit_time', __( 'petro::lang.time' ) ) !!}
               {!! Form::input('datetime-local', 'cash_deposit_time', null, [
                    'class' => 'form-control cash_deposit_fields',
                    'required',
                    'placeholder' => __('petro::lang.time')
                ]) !!}

            </div>
        </div>
        
        
        <div class="col-md-1">
            <button type="button" class="btn btn-primary cash_deposit_add"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
        
    </div>
    
</div>
<br><br>

<div class="row" style="margin-top: 10px">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="cash_deposit_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.bank' )</th>
                    <th>@lang('petro::lang.receipt_no' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.time' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $cash_total = $settlement_cash_deposits->sum('amount');
                @endphp
                @foreach ($settlement_cash_deposits as $cash_payment)
                    <tr>
                        <td>{{$cash_payment->bank_name}}</td>
                        <td>{{$cash_payment->account_no}}</td>
                        <td class="cash_deposit_amount">{{number_format($cash_payment->amount, $currency_precision)}}</td>
                        <td>{{@format_datetime($cash_payment->time_deposited)}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_cash_payment" data-href="/petro/settlement/payment/delete-cash-deposit/{{$cash_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
               
            </tbody>

            <tfoot>
                <tr>
                    <td style="text-align: right; font-weight: bold;">@lang('petro::lang.settlement_cash_deposit_total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="cash_deposit_total">
                    {{number_format($cash_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$cash_total}}" name="cash_deposit_total" id="cash_deposit_total">
            </tfoot>
        </table>
    </div>
</div>



<script>
    $('#cash_deposit_bank').select2();
    $(document).ready(function(){
        
        $("#cash_deposit_bank").val($("#cash_deposit_bank option:eq(0)").val()).trigger('change');
        
        
    });
</script>