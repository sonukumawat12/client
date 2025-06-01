<div class="modal-dialog modal-lg" role="document">
  <div class="modal-content">
    @php
   
    @endphp
    <form id="payments_form_sub">
        <div class="card">
            <div class="card-body">
                @component('components.widget', ['class' => 'box-primary', 'title' => __('purchase.add_payment')])
                    <div id="payment_rows_div">
                        <div class="box-body payment_row" data-row_id="0">
                            <button type="button" class="btn btn-primary pull-right"
                                    id="add-payment-row">@lang('sale.add_payment_row')</button>
                            @include('sale_pos.partials.payment_row_form', ['row_index' => 0])
                        </div>
                    </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="pull-right"><strong>@lang('purchase.payment_due'):</strong> <span
                                id="modal_payment_due">0.00</span>
                        </div>
    
                    </div>
                </div>
                <br>
                <div class="row">
                        <div class="col-sm-12">
                            <button type="submit" id="submit_payments_form"
                                class="btn btn-primary pull-right btn-flat">@lang('messages.save')</button>
                                
                            <button style="margin-right: 10px" type="button" class="btn btn-default pull-right" data-dismiss="modal">@lang( 'messages.close' )</button> 
                            
                            
                        </div>
                    </div>
                
                @endcomponent
            </div>
        </div>
    </form>
        
  </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->
<script>
  modal_calculate_balance_due();
  $("#modal_payment_due").text(parseInt(__number_uf($('#payment_due').text()), 10));
  
  $('#payments_form_sub').submit(function(event) {
    event.preventDefault();
    
    var row = '';
    
    var formDataArray = $(this).serializeArray();
    var total = 0;
    $('.payment-amount').each(function() {
        
        if (parseFloat($(this).val())) {
            total += __read_number($(this));
        }
                
                
        var id = $(this).attr('id');
        var index = id.split('_')[1];  
        
        row += `<tr>
                      
                    <td>`+$('#method_'+index+' option:selected').text()+`</td>
                    <td>`+$('#account_'+index+' option:selected').text()+`</td>
                    <td>
                    <input type="hidden" class="pmt-amount" value="`+$('#amount_'+index).val()+`">
                    `+__number_f($('#amount_'+index).val())+`</td>
            </tr>
                `;
    });
    
    $("#payment_total_price_supplier").text(__number_f(total));
    calculate_balance_due()
    
    $("#payment_methods_data").val(JSON.stringify(formDataArray));
    
    $('#payment_table_supplier').append(row);
    $('div.invoice_payment_modal_supplier').modal('hide');
    
});


  $('.save').click(function(e) {
      e.preventDefault();
      $('div.invoice_payment_modal_supplier').modal('hide');
      var newRow = $('<tr>');
      newRow.append($('<td>').text($('#payment_method').val()));
      newRow.append($('<td>').text($('#price').val()));
      
      $('#payment_table_supplier').append(newRow);
      $('#payment_total_price').text(parseInt($('#payment_total_price').text(), 10) + parseInt($('#price').val()))
  });
  
  $('button#add-payment-row').click(function () {
        var row_index = parseInt($('.payment_row_index').val()) + 1;
        var location_id = $('#location_id').val();
        var $this = $(this);

        $.ajax({
            method: 'POST',
            url: '/purchases/get_payment_row',
            data: { row_index: row_index, location_id: location_id },
            dataType: 'html',
            success: function (result) {
                if (result) {
                    var total_payable = __read_number($('input#grand_total_hidden'));
                    var total_paying = 0;
                    $this.closest('#payment_rows_div')
                        .find('.payment-amount')
                        .each(function () {
                            if (parseFloat($(this).val())) {
                                total_paying += __read_number($(this));
                            }
                        });
                    var b_due = total_payable - total_paying;
                    var appended = $this.closest('#payment_rows_div').append(result);
                    $(appended).find('input.payment-amount').focus();
                    $(appended).find('input.payment-amount').last().val(b_due).change().select();
                    __select2($(appended).find('.select2'));
                    $('#amount_' + row_index).trigger('change');
                    $('#cheque_date_' + row_index).datepicker('setDate', new Date());
                    $('.payment_row_index').val(parseInt(row_index));
                    let cash_account_id = $('#cash_account_id').val();
                    $(appended).find('select.payment_types_dropdown ').last().val(cash_account_id).change().select();
                    modal_calculate_balance_due();
                }
            },
        });
    });
    
    $(document).on('click', '.remove_payment_row', function () {
        swal({
            title: LANG.sure,
            icon: 'warning',
            buttons: true,
            dangerMode: true,
        }).then((willDelete) => {
            if (willDelete) {
                $(this).closest('.payment_row').remove();
                modal_calculate_balance_due();
            }
        });
    });
    
    
    $(document).on('change', '.payment-amount', function() {
        modal_calculate_balance_due();
    });
    
      $(document).on('change', '.payment_types_dropdown', function() {
        modal_calculate_balance_due();
    });
    
    
    
    function modal_calculate_balance_due() {
        var total_payable = parseInt(__number_uf($('#payment_due').text()), 10);

        var total_paying = 0;
        $('#payment_rows_div')
            .find('.payment-amount')
            .each(function() {
                console.log($(this).val());
                if (parseFloat($(this).val())) {
                    total_paying += __read_number($(this));
                }
            });
            
         var bal_due = total_payable - total_paying;
            // console.log(total_payable,total_paying,bal_due)
        if(bal_due != 0) {
            $("#submit_payments_form").hide();
        }else{
            $("#submit_payments_form").show();
        }
        

        $('#modal_payment_due').text(__currency_trans_from_en(bal_due, false, false));
    }
</script>