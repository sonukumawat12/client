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
</style>

<div class="modal-dialog" role="document" style="width: 65%">
    <div class="modal-content">
         {!! Form::open(['url' =>action('\Modules\Petro\Http\Controllers\ClosingShiftController@update', $id), 'method' => 'put' ]) !!}
        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'petro::lang.closing_meter' )</h4>
        </div>

        <div class="modal-body">
            
            <div class="row">
                <div class="col-md-12">
                    <br>
                    <br>
                    <br>
                   
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
                                    {!! Form::text('sale_price', $pump->sell_price_inc_tax, ['class' => 'form-control input-lg',
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
                                    {!! Form::text('starting_meter', number_format($pump->starting_meter,3,".",""), ['class' => 'form-control input-lg',
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
                                    {!! Form::text('testing_ltr', number_format($pump->testing_ltr,3,".",""), ['class' => 'form-control input-lg inputcalculater',
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
                                        {!! Form::text('closing_meter', number_format($pump->closing_meter,3,".",""), ['class' => 'form-control input-lg inputcalculater', 'required'
                                        ]) !!}
                                        <div class="input-group-addon calculate_total"
                                            style="background: #00a65a; color: #fff; cursor: pointer">
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
                                    {!! Form::text('amount', number_format($pump->amount,3,".",""), ['class' => 'form-control input-lg', 'readonly' , 'required'])
                                    !!}
                                </div>
                                <input type="hidden" name="sold_ltr" id="sold_ltr" value="{{number_format($pump->sold_ltr,3,".","")}}">
                                <input type="hidden" name="amount_hidden" id="amount_hidden" value="{{number_format($pump->amount,3,".","")}}">
                            </div>
                        </div>
                        <div class="col-md-6">
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
                        
                    </div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn submit-btn" style="color: #fff; background-color:#2874A6;">@lang('petro::lang.save')</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>
    
    {!! Form::close() !!}


    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script type="text/javascript">
    $('#closing_meter_form').validate();
    $('.calculate_total').trigger('click');
    $(".submit-btn").prop('disabled',true);
    
    var  current_id = null;
    $('.inputcalculater').on('focus', function() {
         //console.log(this.id);
      current_id = this.id;
    });
    function enterVal(val) {
        
       
        $('#'+current_id).focus();
        
        if(val === 'enter'){
            $('#'+current_id).next('.form-control')
            return;
        }
        if(val === 'precision'){
            str = $('#'+current_id).val();
            str = str + '.';
            $('#'+current_id).val(str);
            return;
        }
        if(val === 'backspace'){
            str = $('#'+current_id).val();
            str = str.substring(0, str.length - 1);
            $('#'+current_id).val(str);
            return;
        }
        let closing_meter = $('#'+current_id).val() + val;
        closing_meter = closing_meter.replace(',', '');
        $('#'+current_id).val(closing_meter);
    };
    
    
    
   
$('.calculate_total').click(function() {
    let closing_meter = $('#closing_meter').val().replace(',', '');
    if(closing_meter === '' || closing_meter === undefined || closing_meter === NaN){
        toastr.error('Closing meter value is required');
        return false;
    }
    let starting_meter = parseFloat($('#starting_meter').val());
    closing_meter = parseFloat($('#closing_meter').val().replace(',', ''));
    let testing_ltr = parseFloat($('#testing_ltr').val().replace(',', ''));
    let sold_ltr = closing_meter - starting_meter - testing_ltr
    
    
    if(sold_ltr > {{$pump->qty_available}}){
        toastr.error('Out of Stock');
        $('#closing_meter').val(0);
        return false;
    }

    
    if(closing_meter < starting_meter){
        toastr.error('Closing meter value should not less then starting meter value');
        $('#closing_meter').val(0);
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
    
    
    $(".submit-btn").prop('disabled',false);
}

   
</script>