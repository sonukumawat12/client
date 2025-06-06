<div class="modal-dialog" role="document">
    <div class="modal-content">
        @php
            $business = \App\Business::find(request()->session()->get('business.id'));
            $currencyDecimal = $business->currency_precision;
        @endphp
        {!! Form::open(['url' => action('CustomerReferenceController@store'), 'method' => 'post', 'id' =>
        'customer_reference_add_form' ]) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
                    <button type="button" value="repeat" class="btn btn-primary pull-right toggle_reference" style=" margin-right: 24px;">@lang('lang_v1.one_time')</button>
            <h4 class="modal-title">@lang( 'lang_v1.add_customer_reference' )</h4>
        </div>

        <div class="modal-body ">
            <input type="hidden" id="currencyPrecision" value="{{$currencyDecimal}}">
            <input type="hidden" name="quick_add" id="quick_add" value="{{$quick_add}}">
            <div class="colr-md-4 repeat_field">
                <div class="form-group">
                    {!! Form::label('date', __( 'lang_v1.date' ) . ':*') !!}
                    {!! Form::text('date', date('m/d/Y'), ['class' => 'form-control reference_date', 'required',
                    'placeholder' => __(
                    'lang_v1.date' ) ]); !!}
                </div>
            </div>
            <div class="colr-md-4 repeat_field">
                <div class="form-group">
                    {!! Form::label('contact_id', __( 'lang_v1.customer' ) . ':*') !!}
                    {!! Form::select('contact_id', $contacts, null , ['class' => 'form-control select2
                    contact_reference',
                    'placeholder' => __(
                    'lang_v1.please_select' ), 'style' => 'width: 100%;']); !!}
                </div>
            </div>
            <div class="colr-md-4">
                <div class="form-group">
                    {!! Form::label('reference', __( 'lang_v1.reference' ) . ':*') !!}  @if(!empty($help_explanations['customer_reference'])) @show_tooltip($help_explanations['customer_reference']) @endif
                    {!! Form::text('reference', null, ['class' => 'form-control', 'placeholder' => __(
                    'lang_v1.reference' ) ]); !!}
                </div>
                <input type="hidden" name="barcode_src" id="barcode_src">
            </div>
            <div class="colr-md-4">
                <div class="form-group">
                    <input type="checkbox" id="balanceCheckbox"> Opening Balance
                    {!! Form::label('openingBalance', __( ' ' )) !!}
                    {!! Form::number('openingBalance', 0.00, ['class' => 'form-control d-none', 'placeholder' => __(
                    'lang_v1.openingBalance' ),'step'=>'0.01' ]); !!}
                </div>
            </div>
            <div class="col-md-12 repeat_field">
                {!! Form::label('barcode', __( 'lang_v1.barcode' ) ) !!}
                <div class="col-md-12 barcode-image">

                </div>
            </div>
            <br>
            <div class="col-md-12 repeat_field">
                <button type="button" class="btn btn-danger add_ref_list pull-right">@lang('messages.add')</button>
            </div>
            <div class="clearfix"></div>
            <div class="col-md-12 repeat_field" style="margin-top: 10px;">
                <table class="table table-bordered table-striped" id="customer_reference_list_table">
                    <thead>
                        <tr>
                            <th>@lang('lang_v1.contact_name')</th>
                            <th>@lang('lang_v1.reference')</th>
                            <th>@lang('lang_v1.openingBalance')</th>
                            <th>@lang('lang_v1.action')</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="input_value repeat_field">

            </div>
            <div class="modal-footer">
                <button type="submit" class="btn btn-primary repeat_field">@lang( 'messages.save' )</button>
                <button type="button" class="btn btn-default add_reference_btn" data-dismiss="modal">@lang(
                    'messages.close' )</button>
            </div>

            {!! Form::close() !!}

        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
    <div class="modal fade brands_modal" tabindex="-1" role="dialog" 
            aria-labelledby="gridSystemModalLabel">
    </div>
    <script>
        // function setDecimal(event) {
        //     this.value = parseFloat(this.value).toFixed(2);
        // }

        $('#openingBalance').change(function(){
            decimalValue = $('#currencyPrecision').val();
            this.value = parseFloat(this.value).toFixed(decimalValue);
        });

        $('#balanceCheckbox').change(function(){
            if($(this).is(":checked")) {
                $('#openingBalance').removeClass('d-none');
            } else {
                $('#openingBalance').addClass('d-none');
            }
        });
        // $('.contact_reference').select2();
        $('.reference_date').datepicker();
        var index = 0;
        $('.add_ref_list').click(function(){
            if($('.toggle_reference').val() === 'one_time'){
                let customer_reference = $(this).val();
                let html = '<option value="'+customer_reference+'" selected>'+customer_reference+'</option>';

                $('#customer_reference').empty().append(html).trigger('change');
            }
            else{
                $.ajax({
                    method: 'get',
                    url: '/get-customer-reference/barcode',
                    data: { 
                        customer_id : $('#contact_id').val(),
                        reference : $('#reference').val()
                    },
                    success: function(result) {
                        if(result.success == 1){
                            if($('#balanceCheckbox').is(":checked")){ 
                                balance = $('#openingBalance').val();
                            }else{
                                balance = '-'; 
                            }
                            $('.barcode-image').empty().append(result.html);
                            $('#barcode_src').val(result.src);
                            
                            $('#customer_reference_list_table tbody').append(`
                            <tr class="tr_`+index+`">
                                <td>`+$('#contact_id :selected').text()+`</td>
                                <td>`+$('#reference').val()+`</td>
                                <td>`+balance+`</td>
                                <td><button type="button" class="btn btn-xs btn-danger remove_row" style="margin-top: 2px;" data-rowid="`+index+`"><i class="fa fa-times"></i></button></td>
                            </tr>
                            `);
                            $('.input_value').append(`
                           <input class="input_`+index+`" type="hidden" name="ref[`+index+`][customer_id]" value="`+$('#contact_id :selected').val()+`">
                           <input class="input_`+index+`" type="hidden" name="ref[`+index+`][reference]" value="`+$('#reference').val()+`">
                           <input class="input_`+index+`" type="hidden" name="ref[`+index+`][date]" value="`+$('#date').val()+`">
                           <input class="input_`+index+`" type="hidden" name="ref[`+index+`][openingBalance]" value="`+$('#openingBalance').val()+`">
                           <input class="input_`+index+`" type="hidden" name="ref[`+index+`][barcode_src]" value="`+$('#barcode_src').val()+`">
                            `);
                            index = index +1;
                            $('#reference').val('');
                            $('#openingBalance').val('');
                        }
                        
                    },
                });
            }
            
        });

        $(document).on('click', 'button.remove_row', function(){
            rowid = $(this).data('rowid');
            
            $('.tr_'+rowid).remove();
            $('.input_'+rowid).remove();
        });

        // $('#reference').change(function(){
        //     console.log($('.toggle_reference').val());
        //     if($('.toggle_reference').val() === 'one_time'){
        //         let customer_reference = $(this).val();
        //         let html = '<option value="'+customer_reference+'" selected>'+customer_reference+'</option>';

        //         $('#customer_reference').empty().append(html).trigger('change');
        //     }
        //     else{
        //         $.ajax({
        //             method: 'get',
        //             url: '/get-customer-reference/barcode',
        //             data: { 
        //                 customer_id : $('#contact_id').val(),
        //                 reference : $('#reference').val()
        //             },
        //             success: function(result) {
        //                 if(result.success == 1){
        //                     $('.barcode-image').empty().append(result.html);
        //                     $('#barcode_src').val(result.src);
        //                 }
                        
        //             },
        //         });
        //     }
        // })

        $(document).on('click', '.toggle_reference', function(){
            if($(this).val() === 'repeat'){
                $(this).val('one_time');
                $(this).text('Repeat');
                $('.repeat_field').addClass('hide');
            }
            else{
                $(this).val('repeat');
                $(this).text('One Time')
                $('.repeat_field').removeClass('hide');
            }
        })
        $(document).on('change', '#reference', function(){

        });

        @if($quick_add)
            $('#contact_id').val($('#credit_sale_customer_id').val()).attr('disabled','disabled').click('off');
        @endif

        $("#contact_id").on('change',function(){
               
            $.ajax({
                method: 'GET',
                url: '/autorepairservices/vehicleDetails/'+$("#contact_id").val(),
                success: function(result) {
                    $("#vehicle_id").html('');
                    $("#vehicle_id").append('<option value="">Select Vehicle Number</option>');
                    var html='';
                    for(var i=0;i<result.length;i++)
                    {
                        html+='<option value="'+result[i].id+'">'+result[i].vehicle_no+'</option>';
                    }
                    $("#vehicle_id").append(html);
                }
            });

        })
    </script>