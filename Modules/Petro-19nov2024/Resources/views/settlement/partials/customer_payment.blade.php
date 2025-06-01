<br>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('customer_payment_customer_id', __('petro::lang.customer').':') !!}
                {!! Form::select('customer_payment_customer_id', $customers, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('customer_payment_payment_method', __('petro::lang.payment_method').':') !!}
                {!! Form::select('customer_payment_payment_method',['cash' => 'Cash', 'card' => 'Card', 'cheque' =>
                'Cheque'], null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __(
                'petro::lang.please_select' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3 hide cheque_divs">
            <div class="form-group">
                {!! Form::label('customer_payment_bank_name', __( 'petro::lang.bank_name' ) ) !!}
                {!! Form::text('customer_payment_bank_name', null, ['class' => 'form-control customer_payment_fields
                bank_name',
                'placeholder' => __(
                'petro::lang.bank_name' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3 hide cheque_divs">
            <div class="form-group">
                {!! Form::label('customer_payment_cheque_date', __( 'petro::lang.cheque_date' ) ) !!}
                {!! Form::text('customer_payment_cheque_date', null, ['class' => 'form-control
                cheque_date',
                'placeholder' => __(
                'petro::lang.cheque_date' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3 hide cheque_divs">
            <div class="form-group">
                {!! Form::label('customer_payment_cheque_number', __( 'petro::lang.cheque_number' ) ) !!}
                {!! Form::text('customer_payment_cheque_number', null, ['class' => 'form-control customer_payment_fields
                cheque_number',
                'placeholder' => __(
                'petro::lang.cheque_number' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('customer_payment_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('customer_payment_amount', null, ['class' => 'form-control customer_payment_fields
                customer_payment', 'required', 'id' => 'customer_payment_amount',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        
        @php
                    
                $business_id = request()
                    ->session()
                    ->get('user.business_id');
                
                $pacakge_details = [];
                    
                $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
                if (!empty($subscription)) {
                    $pacakge_details = $subscription->package_details;
                }
            
            @endphp
            
            @if(!empty($pacakge_details['show_post_dated_cheque']))
              <div class="col-md-3 text-center" >
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('post_dated_cheque', '1', false,
                            [ 'class' => 'input-icheck customer_payment_fields','id' => 'customer_payment_post_dated_cheque']); !!} {{ __( 'account.post_dated_cheque' ) }}
                        </label>
                    </div>
                </div>
            @endif

        
        <div class="col-md-1">
            <button type="button" class="btn btn-primary btn_customer_payment"
                style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br>
<br>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="customer_payment_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.customer' )</th>
                    <th>@lang('petro::lang.payment_method' )</th>
                    <th>@lang('petro::lang.bank_name' )</th>
                    <th>@lang('petro::lang.cheque_date' )</th>
                    <th>@lang('petro::lang.cheque_number' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.settlement_number' )</th>
                    <th>@lang('petro::lang.shift_number' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody>
                @php
                $customer_payment_total = 0.00;
                @endphp
                @if (!empty($active_settlement))
                @foreach ($active_settlement->customer_payments as $item)
                @php
                $customer_name = App\Contact::where('id', $item->customer_id)->select('name')->first()->name;
                $customer_payment_total += $item->sub_total;
                @endphp
                <tr>
                    <td>{{ $customer_name}}</td>
                    <td>{{ $item->payment_method }}</td>
                    <td>{{ $item->bank_name }}</td>
                    <td>{{ @format_date($item->cheque_date) }}</td>
                    <td>{{ $item->cheque_number }}</td>
                    <td>{{ number_format($item->amount, $currency_precision)}}</td>
                    <td>{{ $active_settlement->settlement_no }}</td>
                    <td>
                        @if (is_array($active_settlement->work_shift) || is_object($active_settlement->work_shift))
                            @forelse ($active_settlement->work_shift as $work_shift)
                                {{ $work_shift }},
                            @empty
                                {{ "-" }}
                            @endforelse
                        @else
                            {{ "-" }}
                        @endif
                    </td>
                    <td><button class="btn btn-xs btn-danger delete_customer_payment"
                            data-href="/petro/settlement/delete-customer-payment/{{$item->id}}"><i
                                class="fa fa-times"></i></td>
                </tr>
                @endforeach
                @endif

            </tbody>

            <tfoot>
                <tr>
                    <td colspan="5" style="text-align: right; font-weight: bold;">@lang('petro::lang.total')
                        :</td>
                    <td style="text-align: left; font-weight: bold;" class="customer_payment_total">
                        {{number_format( $customer_payment_total, $currency_precision)}}</td>
                </tr>
                <input type="hidden" value="{{$customer_payment_total}}" name="customer_payment_total"
                    id="customer_payment_total">
            </tfoot>
        </table>
    </div>
</div>