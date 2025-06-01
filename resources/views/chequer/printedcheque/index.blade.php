@extends('layouts.app')
@section('title', __('cheque.templates'))

@section('content')

<style>
    @media print {
        #printvoucherdive {
            width: 176mm; /* B5 width */
            height: 250mm; /* B5 height */
            margin: 0 auto;
            padding: 10mm; /* Optional: to add padding inside the print */
            overflow: hidden; /* Prevent overflow */
        }
        
    }

</style>

<!-- Content Header (Page header) -->
<section class="content-header">
    	<?php

    if(count($get_defultvalu) > 0){
        foreach($get_defultvalu as $values){}
        $def_tempid			= $values->def_tempid;
        $def_curnctname		= $values->def_curnctname;
        $def_stampid		= $values->def_stampid;
        $def_entrydt		= $values->def_entrydt;
        $def_currency		= $values->def_currency;
        $def_stamp			= $values->def_stamp;
        $def_cheque_templete= $values->def_cheque_templete;
        $def_bank_account	= $values->def_bank_account;
        $def_font			= $values->def_font;
        $def_font_size		= $values->def_font_size;
    }else{
        $def_tempid			= '';
        $def_curnctname		= '';
        $def_stampid		= '';
        $def_entrydt		= '';
        $def_currency		= '';
        $def_stamp			= '';
        $def_cheque_templete= '';
        $def_bank_account	= '';
        $def_font			= '';
        $def_font_size		= '';
    }
    ?>
    <h1>Printed Cheque</h1>
    <div class="box box-info">
        <div class="box-header">
            <i class="fa fa-filter" aria-hidden="true"></i>
            <h3 class="box-title">Filters</h3>
        </div>
        <div class="box-body">
            {{ Form::open(array('id' => 'filterForm')) }}
            <div class="row">
                <div class="col-md-3">
                    {!! Form::label('bank_acount_no',__('lang_v1.bank_account_no') . ':') !!}
                    {!! Form::select('bank_acount_no', $bankAcounts, ($defaultVal)?$defaultVal['bank_acount_no']:null, ['placeholder' =>__('report.all'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'bank_acount_no']); !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('payee_no','Supplier/Payee :') !!}
                    {!! Form::select('payee_no', $payeeList, ($defaultVal)?$defaultVal['payee_no']:null, ['placeholder' =>__('report.all'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'payee_no']); !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('cheque_no',__('cheque.cheque_number') . ':') !!}
                    {!! Form::select('cheque_no', $chequeNumbers, ($defaultVal)?$defaultVal['cheque_no']:null, ['placeholder' =>__('report.all'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'cheque_no']); !!}
                </div>
                <div class="col-md-3">
                    {!! Form::label('payment_status',__('purchase.payment_status').':') !!}
                    {!! Form::select('payment_status', $paymentStatus, ($defaultVal)?$defaultVal['payment_status']:null, ['placeholder' =>__('report.all'), 'class' => 'form-control select2 filter-control', 'style' => 'width:100%', 'id' =>'payment_status']); !!}
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    {!! Form::label('date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range',($defaultVal)? $defaultVal['startDate'].' - '.$defaultVal['endDate']: @date('m/01/Y').' - '.@date('m/t/Y') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>'form-control filter-control', 'id' => 'date_range', 'readonly']); !!}

                </div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
                <div class="col-md-3"></div>
            </div>
            {{ Form::close() }}
        </div>
    </div>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => 'Printed Cheque List'])

   
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="templates_table">
            <thead>
                <tr>
                    <th>Date & Time</th>
                    <th>Bank Account No</th>
                    <th>Payment For</th>
                    <th>Supplier/Payee</th>
                    <th>Purchase Order Number</th>
                    <!--<th>Purchase Bill Number</th>-->
                    <!--<th>Supplier Bill Number</th>-->
                    <th>Cheque No</th>
                    <th>Cheque Amount</th>
                    <th>Cheque Date</th>
                    <th>Reference/Invoice Number</th>
                    <th>Payment Status</th>
                    <th>User</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($printedcheque->sortByDesc('created_at') as $data)
                    <tr>
                        <td>{{date("Y-m-d", strtotime($data->created_at))}}</td>
                        @php
                            $payee= json_decode($payeeList, true);
                             $contact = \App\Contact::find($data->payee);
                        @endphp

                       <td>{{$data->bank_account_no}}</td>

                        <td>{{$data->type ?? 'payment only'}}</td>
                        
                        <td>
                             {{ $contact ? $contact->name : 'Unknown' }}
                            
                        </td>
                         <td>{{$data->invoice_no}}</td>
                        <td>
                           
                            {{$data->cheque_no}}
                            @if($data->print_type=='dateonly')
                            <span class="badge badge-danger navbar-badge">Printed Date Only</span>
                            @endif
                        </td>
                        <!--<td></td>-->
                        <!--<td></td>-->
                        <td>
                            
                            {{@num_format($data->cheque_amount)}}
                        </td>
                        <td>{{$data->cheque_date}}</td>
                        <td>{{$data->ref_no}}</td>
                        <td>{{  $data->status }}</td>
                       <td>{{$data->username}}</td>
                        
                        <td>
                        <div class="btn-group">
    <button class="btn btn-info dropdown-toggle btn-xs" type="button" id="actionMenu" data-toggle="dropdown" aria-expanded="false" >
        Actions <span class="caret"></span><span class="sr-only">Toggle Dropdown
                            </span>
    </button>
   <ul class="dropdown-menu dropdown-menu-left" role="menu">
        <li>
            <a class="dropdown-item" href="javascript:void(0);" 
               onclick="myFunctionPrintVoucher(
                   '{{$data->name}}', 
                   '{{$data->cheque_no}}', 
                   '{{$data->payment_status ?? $data->status}}', 
                   '{{$data->payment_for}}', 
                   '{{$data->type}}', 
                   '{{$bankAcounts}}', 
                   '{{$data->cheque_date}}', 
                   '{{ @num_format($data->cheque_amount) }}', 
                   '{{$data->amount_word}}'
               )"><i class="glyphicon glyphicon-print"></i>
               Print Voucher
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="javascript:void(0);" onclick="openNoteModals('{{$data->id}}')"><span class="glyphicon glyphicon-book"> </span> Note</a>
        </li>
    </ul>
</div>



                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    		<div class="row" id='printvoucherdive'
            style="display:none;background-color:white;">
            <div class="col-md-12">
                <br />
                <div class="col-md-2"></div>
                <div class="col-md-8">
                    @php
                    foreach($getvoucher as $vaoucherid){}
                    $Currentid=(!empty($vaoucherid['id'])? $vaoucherid['id'] : 0 )+1;
                    $settingData = App\SiteSettings::first();

                    @endphp
                    <center>
                        <h2>{{ $settingData->site_name }}</h2>
                    </center>
                </div>
                <div class="col-md-2"></div>
            </div>
            <div class="col-md-12">
                <div class="col-md-4"></div>
                <div class="col-md-4">
                    <h4>PAYMENT VOUCHER</h4>
                </div>
                <div class="col-md-1"></div>
                <div class="col-md-3 ">
                    <h5 id="voc_no"><b>No:</b> {{ $Currentid }}</h5>
                    <h5 id="datew_id"><b>Date:</b>{{date('d-m-Y') }}</h5>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <label><b>Bank Information:</b></label>
                </div>
                <div class="col-md-7">
                    <p id="vou_bank"></p>
                </div>
                <div class="col-md-2"></div>
                <br />
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <label><b>Payment Status:</b></label>
                </div>
                <div class="col-md-7">
                    <p id="vou_payment_status"></p>
                </div>
                <div class="col-md-2"></div>
                <br />
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <label><b>Payee name:</b></label>
                </div>
                <div class="col-md-7">
                    <p id="vou_pyeename"></p>
                </div>
                <div class="col-md-2"></div>
                <br />
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <label><b>Amount Number:</b></label>
                </div>
                <div class="col-md-7">
                    <p id="vou_payamnum"></p>
                </div>
                <div class="col-md-2"></div>
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <label><b>Amount in word:</b></label>
                </div>
                <div class="col-md-7">
                    <p id="vou_payamnumword"></p>
                </div>
                <div class="col-md-2"></div>
                <br />
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <label><b>Chaque no.:</b></label>
                </div>
                <div class="col-md-7">
                    <div class="col-md-7">
                        <p id="vou_chaqno"></p>
                    </div>
                </div>
                <div class="col-md-2"></div>
                <br />
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <label><b>Chaque Date:</b></label>
                </div>
                <div class="col-md-7">
                    <div class="col-md-7">
                        <p id="vou_date"><?php echo date('d-m-Y'); ?></p>
                    </div>
                </div>
                <div class="col-md-2"></div>
                <br />
            </div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <label><b>On Account of:</b></label>
                </div>
                <div class="col-md-7">
                    <label id="vou_amountof" style="border-bottom: 1px dashed;"></label>
                </div>
                <div class="col-md-2"></div>
                <br />
            </div>
            <input type="hidden" id="def_curnctname" name="def_curnctname" value="<?php echo $def_currency; ?>">
            <div class="col-md-12"></div>
            <div class="col-md-12"><br /><br /><br /><br /></div>
            <div class="col-md-12">
                <div class="col-md-3">
                    <?php // echo $this->session->userdata('user_role_name'); ?>
                    <label style="border-top: 1px dashed;"><b>Prepared by</b></label>
                </div>
                <div class="col-md-3">
                    <label style="border-top: 1px dashed;"><b>Approved by</b></label>
                </div>
                <div class="col-md-2">
                    <label style="border-top: 1px dashed;"><b>signature 1</b></label>
                </div>
                <div class="col-md-2">
                    <label style="border-top: 1px dashed;"><b>signature 2</b></label>
                </div>
                <div class="col-md-2">
                    <label style="border-top: 1px dashed;"><b>Received By</b></label>
                </div>
            </div>
        </div>
        <div class="row"><br /> <br /> <br /> <br /> <br /></div>
        <div class="canvasDiv"></div>

    @endcomponent
 

</section>
   <div class="modal fade" id="noteModal" tabindex="-1" aria-labelledby="noteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noteModalLabel">Add Note</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="noteForm">
                    @csrf
                    <input type="hidden" id="chequeId" name="cheque_id">
                    <div class="mb-3">
                        <label for="noteText" class="form-label">Note</label>
                        <textarea class="form-control" id="noteText" name="note" rows="3"></textarea>
                    </div>
                    <button type="button" class="btn btn-primary" onclick="saveNote()">Save Note</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
@section('javascript')
<script src="{{asset('js/html2canvas.js')}}"></script>
<script src="{{asset('js/html2canvas.min.js')}}"></script>
<script>
    $(document).ready(function(){
        
        
function openNoteModals(chequeId) {
    conole.log("modal");
    document.getElementById('chequeId').value = chequeId;
    var noteModal = new bootstrap.Modal(document.getElementById('noteModal'));
    noteModal.show();
}

function saveNote() {
    var chequeId = document.getElementById('chequeId').value;
    var noteText = document.getElementById('noteText').value;

    if (!noteText.trim()) {
        alert("Please enter a note.");
        return;
    }

    $.ajax({
        url: "{{ action('Chequer\ChequeWriteController@getChequedNoTemplate') }}", // Ensure correct route
        type: 'POST',
        data: {
            _token: "{{ csrf_token() }}",
            cheque_id: chequeId,
            note: noteText
        },
        success: function(response) {
            alert("Note saved successfully!");
            document.getElementById('noteForm').reset();
            $('#noteModal').modal('hide');
        },
        error: function(xhr) {
            console.error("Error:", xhr.responseText);
            alert("Failed to save note. Please try again.");
        }
    });
}
 
        
        
        $('input#date_range').daterangepicker(
            dateRangeSettings
        );
        $('.filter-control').on('change',function(){
            $('#filterForm').submit();
        })
    });
     $('#templates_table').DataTable({
       "order": [[0, "desc"]]  
    });
</script>
<script>
//Print Voucher

function myFunctionPrintVoucher(payName, chequeNo, paymentStatus, paymentFor, paymentType, bankInfo, cheque_date, cheque_amount, amount_word) {
    
    // Assuming bankInfo is a JSON string, parse it first
    const parsedBankInfo = JSON.parse(bankInfo);
    const bankDetails = Object.values(parsedBankInfo)[0];
    $("#vou_bank").text(bankDetails);

    // Show the voucher printing section
    $("#printvoucherdive").css('display', 'block');
    
    $("#vou_payment_status").text(paymentStatus);
    $("#vou_pyeename").text(payName);
    $("#vou_chaqno").text(chequeNo);
    $("#vou_payamnum").text(parseFloat(cheque_amount).toFixed(2));
    $("#vou_payamnumword").text(amount_word);
    $("#vou_date").text(cheque_date);
    $("#vou_amountof").text(paymentFor);

    // Print the content
    html2canvas(document.querySelector("#printvoucherdive")).then(canvas => {
        var imgData = canvas.toDataURL("image/png");
        var newWindow = window.open("", "", "width=800,height=600");
        newWindow.document.write('<html><head><title>Print Voucher</title>');
        // newWindow.document.write('<style>@media print { #printvoucherdive { width: 210mm; height: 297mm; margin: 0; padding: 0; overflow: hidden; } }</style>');
        newWindow.document.write('</head><body>');
        newWindow.document.write('<img src="' + imgData + '" style="width:100%;">');
        newWindow.document.write('</body></html>');
        newWindow.document.close();
        
        // Automatically print
        newWindow.onload = function () {
            newWindow.print();
            newWindow.focus();
            setTimeout(function () {
                newWindow.close();
            }, 1000);
        };
        
        // Hide the voucher printing section after capturing
        $("#printvoucherdive").css('display', 'none');
    }).catch(error => {
        console.error('Error capturing the voucher:', error);
    });
}

// Helper function to convert amount to words (optional)
function convertAmountToWords(amount) {
    const units = [
        '', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
        'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen',
        'Seventeen', 'Eighteen', 'Nineteen'
    ];

    const tens = [
        '', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'
    ];

    const thousands = ['', 'Thousand', 'Million', 'Billion'];

    const convert = (num) => {
        let words = '';

        if (num < 20) {
            words = units[num];
        } else if (num < 100) {
            words = tens[Math.floor(num / 10)] + (num % 10 !== 0 ? ' ' + units[num % 10] : '');
        } else if (num < 1000) {
            words = units[Math.floor(num / 100)] + ' Hundred' + (num % 100 !== 0 ? ' ' + convert(num % 100) : '');
        } else {
            for (let i = 0; i < thousands.length; i++) {
                const divisor = Math.pow(1000, i);
                if (num < divisor * 1000) {
                    words = convert(Math.floor(num / divisor)) + ' ' + thousands[i] + (num % divisor !== 0 ? ' ' + convert(num % divisor) : '');
                    break;
                }
            }
        }

        return words.trim();
    };

    // Handle decimal parts
    let integerPart = Math.floor(amount);
    let decimalPart = Math.round((amount - integerPart) * 100);

    let words = convert(integerPart) + ' Dollar' + (integerPart !== 1 ? 's' : '');

    if (decimalPart > 0) {
        words += ' and ' + convert(decimalPart) + ' Cent' + (decimalPart !== 1 ? 's' : '');
    }

    return words;
}





</script>
@endsection