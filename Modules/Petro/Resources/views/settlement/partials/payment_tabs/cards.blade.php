@inject('request', 'Illuminate\Http\Request')
@php
$sidebar_setting = App\SiteSettings::where('id', 1)
->select('ls_side_menu_bg_color', 'ls_side_menu_font_color', 'sub_module_color', 'sub_module_bg_color')
->first();


$module_array['duplicate_slip_numbers'] = 0;

foreach ($module_array as $key => $module_value) {
    ${$key} = 0;
}

$business_id = request()->session()->get('user.business_id');
$subscription = Modules\Superadmin\Entities\Subscription::current_subscription($business_id);
$stock_adjustment = 0;
$pacakge_details = array();

if (!empty($subscription)) {
    $pacakge_details = $subscription->package_details;
    $stock_adjustment = $pacakge_details['stock_adjustment'];
    $disable_all_other_module_vr = 0;

    if (array_key_exists('disable_all_other_module_vr', $pacakge_details)) {
        $disable_all_other_module_vr = $pacakge_details['disable_all_other_module_vr'];
    }

    foreach ($module_array as $key => $module_value) {
        if ($disable_all_other_module_vr == 0) {
            if (array_key_exists($key, $pacakge_details)) {
                ${$key} = $pacakge_details[$key];
                //logger($key." ".$pacakge_details[$key]);
            } else {
                ${$key} = 0;
            }
        } else {
            ${$key} = 0;
            $disable_all_other_module_vr = 1;
            $visitors_registration_module = 1;
            $visitors = 1;
            $visitors_registration = 1;
            $visitors_registration_setting = 1;
            $visitors_district = 1;
            $visitors_town = 1;
        }
    }
}

if (auth()->user()->can('superadmin')) {
    foreach ($module_array as $key => $module_value) {
        ${$key} = 1;
    }
    $disable_all_other_module_vr = 0;
}



@endphp
<div class="col-md-12">
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('card_customer_id', __('petro::lang.customer').':') !!}
                {!! Form::select('card_customer_id', $customers, null, ['class' => 'form-control select2', 'style' => 'width: 100%;']); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('card_type', __('petro::lang.card_type').':') !!}
                {!! Form::select('card_type', $card_types, null, ['class' => 'form-control
                select2', 'style' => 'width: 100%;', 'placeholder' => __('petro::lang.please_select' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('card_number', __( 'petro::lang.card_number' ) ) !!}
                {!! Form::text('card_number', null, ['class' => 'form-control card_fields input_number
                card_number',
                'placeholder' => __(
                'petro::lang.card_number' ) ]); !!}
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('card_amount', __( 'petro::lang.amount' ) ) !!}
                {!! Form::text('card_amount', null, ['class' => 'form-control card_fields cust_input_number
                card_amount', 'required',
                'placeholder' => __(
                'petro::lang.amount' ) ]); !!}
            </div>
        </div>
        
         <div class="col-md-3">
            <div class="form-group">
                {!! Form::label('slip_no', __( 'petro::lang.slip_no' ) ) !!}
                {!! Form::text('slip_no', null, ['class' => 'form-control card_fields 
                slip_no', 'required',
                'placeholder' => __(
                'petro::lang.slip_no' ) ]); !!}
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="form-group">
              {!! Form::label("card_note", __('lang_v1.payment_note') . ':') !!}
              {!! Form::textarea("card_note", null, ['class' => 'form-control cash_fields', 'rows' => 3]); !!}
            </div>
        </div>
        
        <div class="col-md-3 pull-right">
            <button type="button" class="btn btn-primary card_add pull-right"
            style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
       
    </div>
</div>
<div class="clearfix"></div>
<br><br>

<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="card_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.cusotmer_name' )</th>
                    <th>@lang('petro::lang.card_type' )</th>
                    <th>@lang('petro::lang.card_number' )</th>
                    <th>@lang('petro::lang.amount' )</th>
                    <th>@lang('petro::lang.slip_no' )</th>
                    <th>@lang('lang_v1.note') </th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>
            <tbody id="card_table_body">
                @php
                    $card_total = $settlement_card_payments->sum('amount');
                @endphp
                @foreach ($settlement_card_payments as $card_payment)
                    <tr>
                        <td>{{$card_payment->customer_name}}</td>
                        <td>{{$card_payment->card_type}}</td>
                        <td>{{$card_payment->card_number}}</td>
                        <td class="card_amount">{{number_format($card_payment->amount, $currency_precision)}}</td>
                        <td>{{$card_payment->slip_no}}</td>
                        <td>{{$card_payment->note}}</td>
                        <td><button type="button" class="btn btn-xs btn-danger delete_card_payment" data-href="/petro/settlement/payment/delete-card-payment/{{$card_payment->id}}"><i
                                    class="fa fa-times"></i></button></td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr>
                    <td colspan="3" style="text-align: right; font-weight: bold;">@lang('petro::lang.total') :</td>
                    <td style="text-align: left; font-weight: bold;" class="card_total">
                       {{number_format($card_total, $currency_precision)}}</td>
                </tr>
               
                
               
               
                <input type="hidden" value="{{$card_total}}" name="card_total" id="card_total">

            </tfoot>

        </table>
        <div id="slip_no_message"></div>

    </div>
</div>




<script>
    $(document).ready(function(){
        $("#card_customer_id").val($("#card_customer_id option:eq(0)").val()).trigger('change');
        $('#add_slip_no').on('change', function() {
        var slipNo = $(this).val();

        if (slipNo !== '') {
            $.ajax({
                url: "/petro/settlement/check-slip-no",                
                type: 'GET',
                data: { slip_no: slipNo },
                success: function(response) {
                    console.log(response);
                    if (response.exists && !response.allow_duplicates) {
                        $('#add_slip_no').val('');
                        $('#slip_no_message')
                            .removeClass('text-success')
                            .addClass('text-danger')
                            .text('Duplicate Slip Number, Not allowed to enter');
                    } else {
                        $('#slip_no_message')
                            .removeClass('text-danger')
                            .addClass('text-success')
                            .text('');
                    }
                }
            });
        } else {
            $('#slip_no_message').text('');
        }
    });
    });
</script>