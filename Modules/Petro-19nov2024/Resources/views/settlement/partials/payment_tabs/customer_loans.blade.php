


<div class="col-md-12">
    <div class="row">
        <div class="col-md-3 customer_loans_to_disable">
            <div class="form-group">
                {!! Form::label('customer_loans_customer_id', __('petro::lang.customer').':') !!}
                {!! Form::select('customer_loans_customer_id', $customers, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3 customer_loans_to_disable">
            <div class="form-group">
                {!! Form::label('customer_loans_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('customer_loans_amount', null, ['class' => 'form-control customer_loans_fields cust_input_number
                customer_loans_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        
        <div class="col-md-5 customer_loans_to_disable">
            <div class="form-group">
              {!! Form::label("customer_loans_note", __('lang_v1.payment_note') . ':') !!}
              {!! Form::textarea("customer_loans_note", null, ['class' => 'form-control customer_loans_fields', 'rows' => 3]); !!}
            </div>
        </div> 
        
        
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary customer_loans_to_disable customer_loans_add"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
        
        
        
        
    </div>
    
</div>
<br><br>

<div class="row" style="margin-top: 10px">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="customer_loans_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.customer_name' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.note' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $customer_loans_total = $settlement_customer_loans->sum('amount');
                @endphp
                @foreach ($settlement_customer_loans as $customer_loan)
                    <tr>
                        <td>{{$customer_loan->customer_name}}</td>
                        <td class="customer_loan_amount">{{number_format($customer_loan->amount, $currency_precision)}}</td>
                        <td>{{$customer_loan->note}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_customer_loans_payment" data-href="/petro/settlement/payment/delete-customer-loans/{{$customer_loan->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
                
            </tbody>

            <tfoot>
                <tr>
                    <td style="text-align: right; font-weight: bold;">@lang('petro::lang.settlement_customer_loans_total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="customer_loans_total">
                    {{number_format($customer_loans_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$customer_loans_total}}" name="customer_loans_total" id="customer_loans_total">
            </tfoot>
        </table>
    </div>
</div>




<script>
    $('#customer_loans_customer_id').select2();
    $(document).ready(function(){
        
        $("#customer_loans_customer_id").val($("#customer_loans_customer_id option:eq(0)").val()).trigger('change');
        
          
        
    });
</script>