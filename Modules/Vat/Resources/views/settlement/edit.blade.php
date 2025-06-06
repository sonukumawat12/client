@extends('layouts.app')
@section('title', __('vat::lang.vat_sale'))

@section('content')
@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
$meeter_precision = 3;
@endphp


@php
                    
    $pacakge_details = [];
        
    $subscription = Modules\Superadmin\Entities\Subscription::active_subscription($business_id);
    if (!empty($subscription)) {
        $pacakge_details = $subscription->package_details;
    }

@endphp


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
                <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('vat::lang.vat')</a></li>
                    <li><span>@lang( 'vat::lang.vat_sale')</span></li>
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
                    {!! Form::label('note', __('petro::lang.note') . ':') !!}
                    {!! Form::text('note', !empty($active_settlement) ? $active_settlement->note : null, ['class' =>
                    'form-control note',
                    'placeholder' => __(
                    'petro::lang.note' ) ]); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary below_box', 'id' => 'below_box'])
    <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                    @if(!empty($pacakge_details['vat_meter_sales']))
                    <li class="disabled">
                        <a href="#meter_sale_tab" class="meter_sale_tab" data-toggle="tab">
                            <i class="fa fa-tachometer"></i> <strong>@lang('petro::lang.meter_sale')</strong>
                        </a>
                    </li>
                    @endif

                    <li class="active">
                        <a href="#other_sale_tab" class="other_sale_tab" style="" data-toggle="tab">
                            <i class="fa fa-balance-scale"></i> <strong>
                                @lang('petro::lang.other_sale') </strong>
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
                    <div class="tab-pane" id="meter_sale_tab">
                        @include('vat::settlement.partials.meter_sale', ['edit' => 1])
                    </div>

                    <div class="tab-pane active" id="other_sale_tab">
                        @include('vat::settlement.partials.other_sale')
                    </div>

                    
                    <div class="tab-pane" id="payment_tab">
                        @include('vat::settlement.partials.payment')
                    </div>

                </div>
            </div>
        </div>
    </div>

    @endcomponent

    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade add_payment" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade preview_settlement" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div id="settlement_print"></div>

</section>
<!-- /.content -->

@endsection
@section('javascript')
<script src="{{url('Modules/Vat/Resources/assets/js/app.js')}}"></script>
<script src="{{url('Modules/Vat/Resources/assets/js/payment.js')}}"></script>
<script>

    @if(!empty($active_settlement))
    $('.transaction_date').datepicker("setDate", "{{\Carbon::parse($active_settlement->transaction_date)->format('m/d/Y')}}");
    @else
    $('.transaction_date').datepicker("setDate", new Date());
    @endif
    $('#customer_payment_cheque_date').datepicker("setDate", new Date());
    $('#location_id').select2();
    
    $('#item').select2();
    $('#store_id').select2();
    
    $(document).find('li.disabled a').on('click', function(e) { e.preventDefault(); return false; });

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

        });
    });

    $('#note, #work_shift, #transaction_date, #pump_operator_id, #location_id').change(function() {
        $.ajax({
            method: 'put',
            url: "{{action('\Modules\Vat\Http\Controllers\VatSettlementController@update', $active_settlement->id)}}",
            data: {
                note: $('#note').val(),
                transaction_date: $('#transaction_date').val(),
                pump_operator_id: $('#pump_operator_id').val(),
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