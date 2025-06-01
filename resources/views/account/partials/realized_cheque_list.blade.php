@if($cheque_lists->count() > 0)
@php
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp
@foreach ($cheque_lists as $item)
    <tr>
        <td>
            <!-- change item->id as item->t_id by virtual it professional referance docs number 7338 -->
            {!! Form::checkbox('select_cheques[]', $item->t_id, false, ['class' => 'input-icheck select_cheques']) !!}
        </td>
        <td>
            {{$item->customer_name}}
        </td>
        <td>
            <!-- change item->id as item->t_id by virtual it professional referance docs number 7338 -->
            {!! Form::date('paid_on_' . $item->t_id, \Carbon\Carbon::parse($item->paid_on)->format('Y-m-d'), ['class' => 'form-control', 'placeholder' => __('account.realize_date' ) ]); !!}
        </td>
        <td>
            <!-- added by virtual it professional referance docs number 7338 -->
            {!! Form::hidden('cheque_no', $item->cheque_number, null); !!}
            {{$item->cheque_number}}
        </td>
        <td>
            @if(!empty($item->cheque_date) && $item->cheque_date != '0000-00-00')
            {{@format_date($item->cheque_date)}}
            @endif
        </td>
        <td>
            <!-- added by virtual it professional referance docs number 7338 -->
            @php
                $sendingBank = DB::table('cheque_deposit_bank')
                ->join('accounts', 'cheque_deposit_bank.bank_id', '=', 'accounts.id')
                ->where('cheque_deposit_bank.account_trans_id', $item->sending_bank)
                ->select('accounts.name')
                ->first();
            @endphp
            {!! Form::hidden('given_bank', $item->sending_bank, null); !!}
            {{ $sendingBank->name ?? null }}

        </td>
        <td>
            <!-- added by virtual it professional referance docs number 7338 -->
            @php
                $receivingBank = DB::table('cheque_deposit_bank')
                ->join('accounts', 'cheque_deposit_bank.bank_id', '=', 'accounts.id')
                ->where('cheque_deposit_bank.account_trans_id', $item->receiving_bank)
                ->select('accounts.name as receiving_bank_name', 'accounts.id as receiving_id')
                ->first();
                if($receivingBank):
                    $receivingId = $receivingBank->receiving_id;
                    $receivingBankName = $receivingBank->receiving_bank_name;
                else:
                    $receivingId = '';
                    $receivingBankName = '';
                endif;
            @endphp

            {{ $receivingBankName ?? '' }}
            {!! Form::hidden("realize_cheque_bank_$item->t_id", null); !!}
        </td>
        <td class="one_cheque_amount" data-string="{{$item->amount}}">
            {{ number_format($item->amount,$currency_precision) }}
        </td>
    </tr>
@endforeach
@else
<tr>
    <!-- change colspan from 5 to 8 by virtual it professional referance docs number 7338 -->
    <td colspan="8" class="text-center">
        <p>@lang('account.no_item_found')</p>
    </td>

</tr>
@endif

<script>
    $('.select_cheques').change(function() {
        var $tr = $(this).closest('tr');
        var cheque_value = parseFloat($tr.find('.one_cheque_amount').data('string'));
        var cheque_id = $(this).val();
        var $pmt = $(this).closest('.payment-row');
        var $pmtAmt = $pmt.find('.payment-amount');
        
        
        totalChequeValue = 0;
        $('.select_cheques:checked').each(function() {
            var $tr = $(this).closest('tr');
            var cheque_value = parseFloat($tr.find('.one_cheque_amount').data('string'));
        
            totalChequeValue += cheque_value; // Sum up the cheque_value
          });
          $pmtAmt.val(totalChequeValue)
        
    });
         
</script>