@extends('layouts.app')
@section('title', __('vat::lang.vat_invoice'))

@section('content')
<!-- Main content -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">
<style>
button {
    text-decoration: none;
    color: #fff;
    font-size: 15px;
    padding:10px 15px;
    border:none;
    background-color: #2874a6;
    float:right;
    margin-top:50px;
    margin-right:50px;
    
}
input {
    border: 1px solid blue;
    width:50px;
    height:25px;
    font-size: 20px;
    margin-right:5px
}
</style>
{!! Form::open(['method' => 'post', 'id' => 'issue_bill_customer_form' ])!!}
<section class="content">

<div class="row" >
<div class="row">
    @include('vat::vat_invoice2.partials.nav')
    <button type="submit" formaction="{{action('\Modules\Vat\Http\Controllers\VatInvoice2Controller@updateSetting')}}" >Save</button>
    <div style="width:70%; margin:auto;">
    <div class="col-md-12" style="margin-top:50px;">
        <div class="text-center border-top-only">
            <input type="number" name="header_size" id="header_size" value="@if(!empty($invoice2_settings->header_size)){{$invoice2_settings->header_size}}@else{{'18'}}@endif" >
            <span id="header"><strong>AT INVOICE</strong></span>
        </div>
        <div class="text-center border-top-only">
            <input name="company_size"  type="number" id="company_size" value="@if(!empty($invoice2_settings->company_size)){{$invoice2_settings->company_size}}@else{{'18'}}@endif" >
            <span id="company"><strong>Company Name</strong></span>
        </div>
        <div class="text-center border-top-only">
            <input name="address_size"  type="number" id="address_size" value="@if(!empty($invoice2_settings->address_size)){{$invoice2_settings->address_size}}@else{{'18'}}@endif" >
            <span id="address"><strong>Company Address</strong></span>
        </div>
        <table style="width: 100%; border-collapse: collapse; border-top: 2px solid black;"  class="table">
            <tr style="border-bottom: 2px solid black">
                <td colspan="5" class="text-left bordered zero-padding" >
                    <div>
                        <span id = "customer"><strong>Customer</strong></span>
                        <input  type="number" name="customer_size" id="customer_size" value="@if(!empty($invoice2_settings->customer_size)){{$invoice2_settings->customer_size}}@else{{'18'}}@endif" >
                    </div>
                    <div>
                        <span id="vat"><strong>Address</strong></span> <br/>
                        <span id="vat1"><strong>VAT No</strong></span>
                        <input name="vat_size"  type="number" id="vat_size" value="@if(!empty($invoice2_settings->vat_size)){{$invoice2_settings->vat_size}}@else{{'18'}}@endif" >
                    </div>
                    <div>
                        <span id="method"><strong>Payment Method</strong> </span>
                        <input name="method_size"  type="number" id="method_size" value="@if(!empty($invoice2_settings->method_size)){{$invoice2_settings->method_size}}@else{{'18'}}@endif" >
                    </div>
                    <br/>

                </td>
                <td colspan="3" class="text-left bordered zero-padding" >
                    <div>
                        <input  type="number" name="registration_size" id="registration_size" value="@if(!empty($invoice2_settings->registration_size)){{$invoice2_settings->registration_size}}@else{{'18'}}@endif" >
                        <span id="registration"><strong>@lang('vat::lang.invoice_no'):</strong> </span>
                    </div>
                    <div>
                        <input  type="number" name="invoice_size" id="invoice_size" value="@if(!empty($invoice2_settings->invoice_size)){{$invoice2_settings->invoice_size}}@else{{'18'}}@endif" >
                        <span id="invoice"><strong>INVOICE #</strong> </span>
                    </div>
                    <div>
                        <input  type="number" name="date_size" id="date_size" value="@if(!empty($invoice2_settings->date_size)){{$invoice2_settings->date_size}}@else{{'18'}}@endif" >
                        <span id="date"><strong>INVOICE DATE</strong> </span>
                    </div>
                    
                </td>
                    
            </tr>
          
            <tr id="thead" class="bordered">
                <td style="border: 1px solid black" class="uppercase text-center text-bold bordered zero-padding" style="width: 5%">@lang('vat::lang.s_no')
                    <input type="number" name="thead_size" id="thead_size" value="@if(!empty($invoice2_settings->thead_size)){{$invoice2_settings->thead_size}}@else{{'18'}}@endif" >
                </td>
                <td style="border: 1px solid black" class="uppercase text-center text-bold bordered zero-padding" style="width: 30%">@lang('vat::lang.description_of_goods')</td>
                <td style="border: 1px solid black" class="uppercase text-center text-bold bordered zero-padding" style="width: 8%">@lang('vat::lang.qty')</td>
                <td style="border: 1px solid black" class="uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.base_unit_rate')</td>
                <td style="border: 1px solid black" class="uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.discount')</td>
                <td style="border: 1px solid black" class="uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.total_tax_payable')  %</td>
                <td style="border: 1px solid black" class="uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.net_amount')</td>
                <td style="border: 1px solid black" class="uppercase text-center text-bold bordered zero-padding" style="width: 20%">@lang('vat::lang.total_payable_with_tax')</td>
            </tr>
            <tr id="tbody" class="bordered"> 
                <td class="uppercase text-center text-bold bordered zero-padding" style="border: 1px solid black">
                    <input  type="number" name="tbody_size" id="tbody_size" value="@if(!empty($invoice2_settings->tbody_size)){{$invoice2_settings->tbody_size}}@else{{'18'}}@endif" >
                </td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="border: 1px solid black">XXXXX</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="border: 1px solid black">XXXXX</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="border: 1px solid black">XXXXX</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="border: 1px solid black">XXXXX</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="border: 1px solid black">XXXXX</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="border: 1px solid black">XXXXX</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="border: 1px solid black">XXXXX</td>
            </tr>
            
        </table>
    </div>
    <div class="col-md-7"></div>
    <div class="col-md-5">
        <input  type="number" name="sub_size" id="sub_size" value="@if(!empty($invoice2_settings->sub_size)){{$invoice2_settings->sub_size}}@else{{'18'}}@endif" >
        <table id="sub">
            <tr height="50" style="border:2px solid black; border-top: 2px solid black;">   
                <td class="uppercase text-center text-bold bordered zero-padding" style="border-right:1px solid black">Total Invoice Amount(with VAT)</td>
                <td class="uppercase text-center text-bold bordered zero-padding"></td>      
            </tr>
            <tr height="50" style="border: 2px solid black">
                <td class="uppercase text-center text-bold bordered zero-padding" style="border-right:1px solid black">Tax Base Value</td>
                <td class="uppercase text-center text-bold bordered zero-padding"></td>      
            </tr>
            <tr height="50" style="border: 2px solid black">
                <td class="uppercase text-center text-bold bordered zero-padding" style="border-right:1px solid black">VAT(18%)</td>
                <td class="uppercase text-center text-bold bordered zero-padding"></td>      
            </tr>
            <tr height="50" style="border: 2px solid black">
                <td class="uppercase text-center text-bold bordered zero-padding" style="border-right:1px solid black">Price Adjustment</td>
                <td class="uppercase text-center text-bold bordered zero-padding"></td>      
            </tr>
            <tr height="50" style="border: 2px solid black">
                <td width="300" class="uppercase text-center text-bold bordered zero-padding" style="border-right:1px solid black">Total Invoice Amount(with VAT)</td>
                <td width="400" class="uppercase text-center text-bold bordered zero-padding"></td>      
            </tr>
        </table>
    </div>
   
    <div>
        <div class="col-md-12" style="text-align:center;"><input  type="number" name="footer_size" id="footer_size" value="@if(!empty($invoice2_settings->footer_size)){{$invoice2_settings->footer_size}}@else{{'18'}}@endif" ></div>
        <div id="footer1" style="font-weight: bold;">
            <div class="col-md-4" style="text-align:center; margin-top: 5px">................................</div>
            <div class="col-md-4" style="text-align:center; margin-top: 5px">................................</div>
            <div class="col-md-4" style="text-align:center; margin-top: 5px">................................</div>

    
            
            <div class="col-md-4" style="text-align:center;">Prepared By</div>
            <div class="col-md-4" style="text-align:center;">Checked By</div>
            <div class="col-md-4" style="text-align:center; ">Customer Signature</div>    
        </div>
    </div>
    <div class="col-md-12" style="text-align:center; margin-top:50px">Thank you! Come Again. This Software is developed by SYZYGY Technologies. Contact: 077 4055 434/071 1616 192</div>
    </div>
</div>

<div class="modal fade issue_bill_customer_model" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
</section>
{!! Form::close() !!}


<!-- /.content -->

@endsection


@section('javascript')
<script>
    document.getElementById('header').style.fontSize =  document.getElementById('header_size').value + 'px';
    document.getElementById('company').style.fontSize =  document.getElementById('company_size').value + 'px';
    document.getElementById('address').style.fontSize =  document.getElementById('address_size').value + 'px';
    document.getElementById('customer').style.fontSize =  document.getElementById('customer_size').value + 'px';
    document.getElementById('vat').style.fontSize =  document.getElementById('vat_size').value + 'px';
    document.getElementById('registration').style.fontSize =  document.getElementById('registration_size').value + 'px';
    document.getElementById('invoice').style.fontSize =  document.getElementById('invoice_size').value + 'px';
    document.getElementById('date').style.fontSize =  document.getElementById('date_size').value + 'px';
    document.getElementById('method').style.fontSize =  document.getElementById('method_size').value + 'px';
    document.getElementById('thead').style.fontSize =  document.getElementById('thead_size').value + 'px';
    document.getElementById('tbody').style.fontSize =  document.getElementById('tbody_size').value + 'px';
    document.getElementById('sub').style.fontSize =  document.getElementById('sub_size').value + 'px';
    document.getElementById('footer1').style.fontSize =  document.getElementById('footer_size').value + 'px';
    
    
    
    // Function to update font size
    function updateFontSize(elementId, fontSize) {
        
        var element = document.getElementById(elementId);
        if (element) {
            element.style.fontSize = fontSize + 'px';
        }
    }
    
    // Event listener for invoice font size input
    document.getElementById('header_size').addEventListener('input', function() {
        updateFontSize('header', this.value);
    });

    // Event listener for header font size input
    document.getElementById('company_size').addEventListener('input', function() {
        updateFontSize('company', this.value);
    });

    document.getElementById('address_size').addEventListener('input', function() {
        updateFontSize('address', this.value);
    });

    document.getElementById('customer_size').addEventListener('input', function() {
        updateFontSize('customer', this.value);
    });

    document.getElementById('vat_size').addEventListener('input', function() {
        updateFontSize('vat', this.value);
        updateFontSize('vat1', this.value);
        
    });

    document.getElementById('registration_size').addEventListener('input', function() {
        updateFontSize('registration', this.value);
    });
    document.getElementById('invoice_size').addEventListener('input', function() {
        updateFontSize('invoice', this.value);
    });
    document.getElementById('date_size').addEventListener('input', function() {
        updateFontSize('date', this.value);
    });
    document.getElementById('method_size').addEventListener('input', function() {
        updateFontSize('method', this.value);
    });
    document.getElementById('footer_size').addEventListener('input', function() {
        updateFontSize('footer1', this.value);
    });
    document.getElementById('sub_size').addEventListener('input', function() {
        updateFontSize('sub', this.value);
    });
    document.getElementById('thead_size').addEventListener('input', function() {
        updateFontSize('thead', this.value);
    });
    document.getElementById('tbody_size').addEventListener('input', function() {
        updateFontSize('tbody', this.value);
    });
</script>
@endsection