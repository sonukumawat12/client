@extends('layouts.printlayout')
@section('title')
Expense
@endsection
@section('content')

<style>

  .cus-table table {
      width: 100%;
  }
  .cus-table table, td, th {
      height: 40px;
      padding-left: 5px;
      font-weight: bold;
  }
 
  
  </style>
  
  
  
  <div class="modal-dialog" role="document" style="width: 80%;">
      <div class="modal-content" style="min-height:510px">
         
          <div class="modal-header" style="border-bottom:none;">
              <div class="text-center">
                  <h4 style="font-weight: bold">
                      {{ $business->name }}
                  </h4>
              </div>
              <div class="text-center">
                  <h4 style="font-weight: bold">
                      {{ $business_locations->address_1 }}
                  </h4>
              </div>
              <div class="text-center">
                  <h4 style="font-weight: bold">Payment Voucher</h4>
              </div>
  
          </div>
          
          <div class="cus-table" style="font-weight: bold; margin:0px 20px; border: 1px solid #e5e5e5;">
              <div style="border: 1px solid #e5e5e5; width: 110px; height: 30px; padding-top:3px; padding-left:5px; padding-right:5px;">PV No: <span class="print_prefix_number"></span></div><br/>
              <table>
                  <tr>
                      <td>Amount: <span class="print_amount"></span></td>
                      <td colspan="2">Date: <span class="print_date"></span></td>
                  </tr>
                  <tr>
                      <td colspan="3"></td>
                  </tr>
                  <tr>
                      <td colspan="3">Payment Method: <span class="print_payment_method"></span></td>
                  </tr>
                  <tr>
                      <td colspan="3">To: <span class="print_category"></span><td>
                  </tr>
                  <tr>
                      <td colspan="3">The sum of: <span class="print_sum"></span></td>
                  </tr>
                  <tr style="width: 55px">
                      <td colspan="2">Being: <input type="text" style="border:none; margin-left:5px; border-bottom: 1px solid #e5e5e5" width=100/> <span class="print_detail"></span></td>
                      <td style="margin-top: 5px;">Arranged By: <span class="print_user_name"></span><br/>Designation: <span class="print_user_designation"></span></td>
                  </tr>
                  <tr>
                      <td>Approved By:  <input type="text" style="border:none; margin-left:5px; border-bottom: 1px solid #e5e5e5" width=100/></td>
                      <td>Paid By: <input type="text" style="border:none; margin-left:5px; border-bottom: 1px solid #e5e5e5" width=100/></td>
                      <td><span style="vertical-align: 15px;">Signature: </span><img style="display:none" class="new-img"/><canvas id="signature-pad" class="signature-pad" width=100 height=50></canvas></td>
                  </tr>    
              </table>
          </div>
  
          <div class="modal-footer no-print" style="border-top:none">
              <button type="button" class="btn btn-primary sub-button" aria-label="Print">
                  <i class="fa fa-print"></i> @lang( 'messages.print' )
              </button>
              <a href="{{ url('expenses')}}"  class="btn btn-default" data-dismiss="modal">@lang( 'messages.close')</a>
          </div>
      </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
  
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
     
    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>

    <script src="{{ asset('AdminLTE/plugins/select2/js/select2.full.min.js?v=' . $asset_v) }}"></script>
  
<script>
    
    $(document).ready(function(){
        var signaturePad = new SignaturePad(document.getElementById('signature-pad'), {
            backgroundColor: 'rgba(255, 255, 255, 0)',
            penColor: 'rgb(0, 0, 0)'
        });
        var saveButton = document.getElementById('save');
        var cancelButton = document.getElementById('clear');
        var signatureDataURL = signaturePad.toDataURL('image/png');
    
        $('.modal-footer').on('click', 'button[aria-label="Print"]', function () {
            $(this).closest('.no-print').hide();
            var signatureImage = new Image();
            signatureImage.src = signatureDataURL;
            // Insert the image into the DOM, replacing the canvas element
            $('.new-img').replaceWith(signatureImage);
            $('img').hide();
            $(this).closest('div.modal-dialog').printThis({
                importCSS: true,
                loadCSS: "", // path to additional css file - use an array [] for multiple
                pageTitle: "", // add title to print page
                removeInline: false, // remove all inline styles from print elements
                printDelay: 333, // variable print delay
                header: null, // prefix to html
                footer: null, // postfix to html
                base: false, // preserve the BASE tag or accept a string for the URL
                formValues: true, // preserve input/form values
                canvas: true, // copy canvas content (experimental)
                doctypeString: '<!DOCTYPE html>', // enter a different doctype for older markup
                removeScripts: false, // remove script tags before appending
                copyTagClasses: false, // copy classes from the html & body tag
            });

            setTimeout(() => {
                $(this).closest('.no-print').show();
        
            }, 1000);
        })
        // Create an image element and set the data URL as the source
    
    })
    </script>
    
    <script>
        
        
        $(document).ready(function(){
            $(".select2").select2();
            $.ajax({
                method: 'GET',
                url: '{{action("TransactionPaymentController@getPaymentDatatable", $id)}}',
                success: function(result) {
                    console.log('--##--');
                    console.log(result);
                    const data = result.data[0];
                    function numberToWords(num) {
                        const belowTen = ['Zero', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine'];
                        const belowTwenty = ['Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
                        const belowHundred = ['Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];
    
                        function toWords(n) {
                            if (n < 10) return belowTen[n];
                            else if (n < 20) return belowTwenty[n - 10];
                            else if (n < 100) return belowHundred[Math.floor(n / 10) - 2] + (n % 10 !== 0 ? ' ' + belowTen[n % 10] : '');
                            else if (n < 1000) return belowTen[Math.floor(n / 100)] + ' Hundred' + (n % 100 !== 0 ? ' ' + toWords(n % 100) : '');
                            else if (n < 1000000) return toWords(Math.floor(n / 1000)) + ' Thousand' + (n % 1000 !== 0 ? ' ' + toWords(n % 1000) : '');
                            // Extend the function for larger numbers here if needed
                        }
    
                        return toWords(num);
                    }
    
                    var numberWithoutCommas = data.amount.replace(/,/g, '')
                    var amount = parseInt(numberWithoutCommas, 10);
                    str = numberToWords(amount);
                    console.log(str);
                    $('.print_prefix_number').text(data.prefix + " " + data.starting_no);
                    $('.print_amount').text(data.amount);
                    $('.print_date').text(data.cheque_date);
                    
                    $('.print_payment_method').html(data.method);
                    $('.print_bank').html(data.method);
                    
                    $('.print_cheque_number').text(data.cheque_date);
                    $('.print_category').text(data.name);
                    $('.print_bank').text(data.bank_name);
                    $('.print_sum').text(str);
                    $('.print_detail').text(data.bank_name);
                    $('.print_bank').text(data.bank_name);
                    $('.print_user_name').text(data.first_name + " " + data.last_name);
                    $('.print_user_designation').text(data.designation);
                    $('.print_approved').text(data.designation);
                    $('.print_paid').text(data.designation);
                    $('.print_signature').text(data.designation);
                },
            });
        })

       
    
    
    </script>
    
  
  
  
  
  

@endsection   

    