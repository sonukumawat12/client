{!! Form::open(['url' => action('\Modules\Petro\Http\Controllers\PumpOperatorPaymentController@saveCardPayment'), 'method' =>'post']) !!}
<div class="row">
    <div class="col-md-8">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('card_type', __('petro::lang.card_type').':') !!}
                    {!! Form::select('card_type', $card_types, null, ['class' => 'form-control select2',
                    'style' => 'width: 100%;','placeholder' => __('messages.please_select')]); !!}
                </div>
            </div>

            <div class="col-md-3"  @if($card_pmt_type == 'bulk') hidden @endif>
                <div class="form-group">
                    {!! Form::label('slip_no', __( 'petro::lang.slip_no' ) ) !!}
                    {!! Form::text('slip_no', null, ['class' => 'form-control card_payment_input',
                    'placeholder' => __('petro::lang.slip_no' ) ]); !!}
                </div>
            </div>
            
            <div class="col-md-3"  @if($card_pmt_type == 'bulk') hidden @endif>
                <div class="form-group">
                    {!! Form::label('card_no', __( 'petro::lang.card_number' ) ) !!}
                    {!! Form::text('card_no', null, ['class' => 'form-control card_payment_input',
                    'placeholder' => __('petro::lang.card_number' ) ]); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('card_amount', __( 'petro::lang.amount' ) ) !!}
                    {!! Form::text('card_amount', null, ['class' => 'form-control card_payment_input',
                    'placeholder' => __('petro::lang.amount' ) ]); !!}
                </div>
            </div>
            
            <div class="clearfix"></div>
         
            <div class="col-md-2 pull-right">
                <button type="button" class="btn btn-primary pull-right card_payment_add"
                    style="margin: 10px;">@lang('messages.add')</button>
            </div>
            

        </div>
        
        <div class="row">
            <div class="col-md-12">
                <table class="table table-bordered table-striped" id="card_payment_table">
                    <thead>
                        <tr>
                            <th>@lang('petro::lang.card_type' )</th>
                            <th>@lang('petro::lang.slip_no' )</th>
                            <th>@lang('petro::lang.card_no' )</th>
                            <th>@lang('petro::lang.amount' )</th>
                            <th>*</th>
                        </tr>
                    </thead>
                    <tbody>
                      
                    </tbody>
                </table>
            </div>
            
            <button type="submit" class="btn btn-danger pull-right card-save-btn"
            style="margin-top: 23px;">@lang('petro::lang.save')</button>
            
        </div>
    </div>
    
    <div id="key_pad" class="row col-md-4 text-center" style="margin-left: 7px;">
        <div class="row">
            <button id="7" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">7</button>
            <button id="8" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">8</button>
            <button id="9" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">9</button>
        </div>
        <div class="row">
            <button id="4" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">4</button>
            <button id="5" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">5</button>
            <button id="6" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">6</button>
        </div>
        <div class="row">
            <button id="1" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">1</button>
            <button id="2" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">2</button>
            <button id="3" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">3</button>
        </div>
        <div class="row">
            <button id="backspace" type="button" class="btn btn-danger" onclick="cardPaymentEnterVal(this.id)">⌫</button>
            <button id="0" type="button" class="btn btn-primary btn-sm" onclick="cardPaymentEnterVal(this.id)">0</button>
            <button id="precision" type="button" class="btn btn-success" onclick="cardPaymentEnterVal(this.id)">.</button>
        </div>
    </div>
</div>
{!! Form::close() !!}