@extends('layouts.'.$layout)
@section('title', __('petro::lang.closing_meter'))
<style>
    .side-label {
        font-size: 21px;
        font-weight: bold;
        padding-top: 5px;
    }

    #key_pad input {
        border: none
    }

    #key_pad button {
        height: 80px;
        width: 80px;
        font-size: 25px;
        margin: 2px 1px;
        border: none !important;
    }

    :focus {
        outline: 0 !important
    }
    .disabled-link {
        pointer-events: none;
        cursor: not-allowed;
        opacity: 0.5;
    }
</style>
@section('content')
<div class="container">
    <div class="col-md-12">
        <br>
        <br>
        <h4 class="text-center" style="color: red;">After entering the Testing Meter (if any) and Closing Meter, then it is necessary to click the Green Colour Arrow Button</h4>
        <br>
        {!! Form::open(['url' =>
        action('\Modules\Petro\Http\Controllers\PumpOperatorActionsController@postClosingMeter', $pump->id), 'method' =>
        'post',
        'id' =>
        'closing_meter_form' ]) !!}
        <div class="row">
            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('pump_no', __('petro::lang.pump_no') .':', ['class' => 'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! Form::text('pump_no', $pump->pump_no, ['class' => 'form-control input-lg', 'readonly']) !!}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('sale_price', __('petro::lang.sale_price') .':', ['class' => 'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        @php
                            $business = \App\Business::where('id', $business_id)->first();
                            $currency_precision = $business->currency_precision;
                        @endphp
                        {!! Form::text('sale_price', number_format($pump->sell_price_inc_tax, $currency_precision, '.', ''), ['class' => 'form-control input-lg',
                        'readonly']) !!}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('starting_meter', __('petro::lang.starting_meter') .':', ['class' =>
                        'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! Form::text('starting_meter', !empty($pump->pod_last_meter) ? ($pump->pod_last_meter >= $pump->last_meter_reading ?  number_format($pump->pod_last_meter,3,".","") : number_format($pump->last_meter_reading,3,".","")) :  number_format($pump->last_meter_reading,3,".",""), ['class' => 'form-control input-lg',
                        'readonly', 'required']) !!}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('testing_ltr', __('petro::lang.testing_liters') .':', ['class' =>
                        'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! Form::text('testing_ltr', 0.00, ['class' => 'form-control input-lg inputcalculater',
                        ]) !!}
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('closing_meter', __('petro::lang.closing_meter') .':', ['class' =>
                        'side-label']) !!}
                    </div>
                    <div class="col-md-7">
                        <div class="input-group">
                            {!! Form::text('closing_meter', null, ['class' => 'form-control input-lg inputcalculater', 'required', 'oninput' => "this.value = this.value.match(/^\\d+(\\.\\d{0,3})?/)?.[0] || ''"
                            ]) !!}
                            <div class="input-group-addon calculate_total disabled-link"
                                style="background: #00a65a; color: #fff; cursor: pointer" id="calculate_total_btn" disabled>
                                ⏎
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-5">
                        {!! Form::label('amount', __('petro::lang.total_amount') .':', ['class' => 'side-label
                        text-red']) !!}
                    </div>
                    <div class="col-md-7">
                        {!! Form::text('amount', 0.00, ['class' => 'form-control input-lg', 'readonly' , 'required'])
                        !!}
                    </div>
                    <input type="hidden" name="sold_ltr" id="sold_ltr" value="0">
                    <input type="hidden" name="amount_hidden" id="amount_hidden" value="0">
                </div>
            </div>
            <div class="col-md-3">
                <div class="row">
                    <div id="key_pad" tabindex="1">
                        <div class="row text-center" id="calc">
                            <div class="calcBG col-md-12 text-center">
                                <div class="row">
                                    <button id="7" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">7</button>
                                    <button id="8" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">8</button>
                                    <button id="9" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">9</button>

                                </div>
                                <div class="row">
                                    <button id="4" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">4</button>
                                    <button id="5" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">5</button>
                                    <button id="6" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">6</button>
                                </div>
                                <div class="row">
                                    <button id="1" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">1</button>
                                    <button id="2" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">2</button>
                                    <button id="3" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">3</button>
                                </div>
                                <div class="row">
                                    <button id="backspace" type="button" class="btn btn-danger"
                                        onclick="enterVal(this.id)">⌫</button>
                                    <button id="0" type="button" class="btn btn-primary btn-sm"
                                        onclick="enterVal(this.id)">0</button>
                                    <button id="precision" type="button" class="btn btn-success"
                                        onclick="enterVal(this.id)">.</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"
                    class="btn btn-flat btn-block btn-lg disabled-link"
                    style="color: #fff; background-color:#810040;" id="dashboard" disabled>@lang('petro::lang.dashboard')</a>
                <br><br>
                <button type="submit" class="btn btn-flat btn-block btn-lg"
                    style="color: #fff; background-color:#2874A6;" id="save" disabled>@lang('petro::lang.save')</button><br><br>

                <a href="#"
                    class="btn btn-flat btn-block btn-lg"
                    style="color: #fff; background-color:#CC0000;" id="cancel">@lang('petro::lang.cancel')</a><br><br>
                <a href="{{action('Auth\PumpOperatorLoginController@logout')}}"
                    class="btn btn-flat btn-block btn-lg pull-right disabled-link"
                    style=" background-color: orange; color: #fff;" id="logout" disabled>@lang('petro::lang.logout')</a>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>

<div id="confirmationModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="confirmationModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="confirmationModalLabel">Confirm zero Total Amount</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                Total Amount is zero, No Values. Are you sure to save?
            </div>
            <div class="modal-footer">
                <button id="confirmYes" class="btn btn-success" style="float:left;">Yes</button>
                <button id="confirmNo" class="btn btn-danger" style="float:right;">No</button>
            </div>
        </div>
    </div>
</div>
@endsection


@section('javascript')
<script type="text/javascript">
    $('#closing_meter_form').validate();
    var  current_id = null;
    $('.inputcalculater').on('focus', function() {
         //console.log(this.id);
      current_id = this.id;
    });
    function enterVal(val) {
        
       
        $('#'+current_id).focus();
        
        if(val === 'enter'){
            $('#'+current_id).next('.form-control')
            toggle_calculate_total_btn();
            return;
        }
        if(val === 'precision'){
            str = $('#'+current_id).val();
            str = str + '.';
            $('#'+current_id).val(str);
            toggle_calculate_total_btn();
            return;
        }
        if(val === 'backspace'){
            str = $('#'+current_id).val();
            str = str.substring(0, str.length - 1);
            $('#'+current_id).val(str);
            toggle_calculate_total_btn();
            return;
        }
        let closing_meter = $('#'+current_id).val() + val;
        closing_meter = closing_meter.replace(',', '');
        $('#'+current_id).val(closing_meter);
        toggle_calculate_total_btn();
    };
    
    
    
   
$('.calculate_total').click(function() {
    let closing_meter = $('#closing_meter').val().replace(',', '');
    if(closing_meter === '' || closing_meter === undefined || closing_meter === NaN){
        toastr.error('Closing meter value is required');
        $("#dashboard").attr('disabled',true);
        $("#save").attr('disabled',true);
        $("#logout").attr('disabled',true);
        $("#dashboard").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#save").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#logout").addClass('disabled-link').attr('aria-disabled', 'true');
        return false;
    }
    let starting_meter = parseFloat($('#starting_meter').val());
    closing_meter = parseFloat($('#closing_meter').val().replace(',', ''));
    let testing_ltr = parseFloat($('#testing_ltr').val().replace(',', ''));
    let sold_ltr = closing_meter - starting_meter - testing_ltr

    if(sold_ltr > {{$pump->qty_available}}){
        toastr.error('Out of Stock');
        $('#closing_meter').val(0);
        $("#dashboard").attr('disabled',true);
        $("#save").attr('disabled',true);
        $("#logout").attr('disabled',true);
        $("#dashboard").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#save").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#logout").addClass('disabled-link').attr('aria-disabled', 'true');
        return false;
    }
    if(closing_meter < starting_meter){
        toastr.error('Closing meter value should not less then starting meter value');
        $('#closing_meter').val(0);
        $("#dashboard").attr('disabled',true);
        $("#save").attr('disabled',true);
        $("#logout").attr('disabled',true);
        $("#dashboard").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#save").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#logout").addClass('disabled-link').attr('aria-disabled', 'true');
        return false;
    }

    calculateTotal();
});

function calculateTotal(){
    let sale_price = parseFloat($('#sale_price').val());
    let starting_meter = parseFloat($('#starting_meter').val());
    let closing_meter = parseFloat($('#closing_meter').val());
    let testing_ltr = parseFloat($('#testing_ltr').val());
    let sold_ltr = closing_meter - starting_meter - testing_ltr

    let total = sale_price * (sold_ltr);
    __write_number($('#amount'), total);
    $('#sold_ltr').val(sold_ltr);
    $('#amount_hidden').val(total);
    if(total >= 0){
        if (total == 0) {
            $("#confirmationModal").modal("show");
        } else {
            $("#dashboard").attr('disabled',false);
            $("#save").attr('disabled',false);
            $("#logout").attr('disabled',false);
            $("#dashboard").removeClass('disabled-link').removeAttr('aria-disabled');
            $("#save").removeClass('disabled-link').removeAttr('aria-disabled');
            $("#logout").removeClass('disabled-link').removeAttr('aria-disabled');
        }
    } else {
        $("#dashboard").attr('disabled',true);
        $("#save").attr('disabled',true);
        $("#logout").attr('disabled',true);
        $("#dashboard").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#save").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#logout").addClass('disabled-link').attr('aria-disabled', 'true');
    }
}

    $('#cancel').click(function() {
        $('#closing_meter').val(0);
        $('#testing_ltr').val(0);
        __write_number($('#amount'), 0);
        $('#sold_ltr').val(0);
        $('#amount_hidden').val(0);
        $("#dashboard").attr('disabled',true);
        $("#save").attr('disabled',true);
        $("#logout").attr('disabled',true);
        $("#dashboard").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#save").addClass('disabled-link').attr('aria-disabled', 'true');
        $("#logout").addClass('disabled-link').attr('aria-disabled', 'true');
    });

    $('#closing_meter').on('input change', function() {
        toggle_calculate_total_btn();
    });

    function toggle_calculate_total_btn(){
        let closing_meter = $('#closing_meter').val().replace(',', '');
        if(closing_meter === '' || closing_meter === undefined || closing_meter === NaN){
            $("#calculate_total_btn").attr('disabled',true);
            $("#calculate_total_btn").addClass('disabled-link').attr('aria-disabled', 'true');
            return;
        }
        let starting_meter = parseFloat($('#starting_meter').val());
        closing_meter = parseFloat($('#closing_meter').val().replace(',', ''));
        let testing_ltr = parseFloat($('#testing_ltr').val().replace(',', ''));
        let sold_ltr = closing_meter - starting_meter - testing_ltr

        if(closing_meter < starting_meter){
            $("#calculate_total_btn").attr('disabled',true);
            $("#calculate_total_btn").addClass('disabled-link').attr('aria-disabled', 'true');
        } else {
            $("#calculate_total_btn").attr('disabled',false);
            $("#calculate_total_btn").removeClass('disabled-link').removeAttr('aria-disabled');
        }
    }
    $('#confirmYes').click(function() {
        $("#confirmationModal").modal("hide");
        $("#dashboard").attr('disabled',false);
        $("#save").attr('disabled',false);
        $("#logout").attr('disabled',false);
        $("#dashboard").removeClass('disabled-link').removeAttr('aria-disabled');
        $("#save").removeClass('disabled-link').removeAttr('aria-disabled');
        $("#logout").removeClass('disabled-link').removeAttr('aria-disabled');
    });

    $('#confirmNo').click(function() {
        $("#confirmationModal").modal("hide");
    });
</script>
@endsection