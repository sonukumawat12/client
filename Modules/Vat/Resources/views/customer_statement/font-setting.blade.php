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

<style>
                
                  .row {
                    page-break-inside: avoid;
                  }
                
                  .zero-padding{
                      padding-bottom:0px !important;
                      padding-top: 0px important;
                  }
                  
                  .text-bold{
                      font-weight: bold;
                  }
                  
                  .bordered{
                      border: 1px solid black;
                  }
                  
                  .separator {
                        border: 1px dotted black;
                    }
                    
                    .pad-50{
                        padding: 20px;
                    }
                    
                    .border-none{
                        border-top: 1px solid white;
                        border-bottom: 1px solid white;
                        border-left: 1px solid white;
                    }
                    
                    .border-none-bottom{
                        border-top: 1px solid white;
                        border-left: 1px solid white;
                    }
                    
                    .border-bottom-only{
                        border-top: 1px solid white;
                        border-bottom: 1px solid black;
                        border-left: 1px solid white;
                    }
                    
                    .uppercase{
                        text-transform: uppercase;
                    }
                    
                    .text-center{
                        text-align: center;
                    }
                </style>

<!-- Main content -->
<section class="content">

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <form method="post">
                @csrf
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <div class="row">
                        <button type="submit" formaction="{{action('\Modules\Vat\Http\Controllers\CustomerStatementController@updateSetting')}}" >Save</button>
                        <div style="width:70%; margin:auto;">
                        <div class="col-md-12" style="margin-top:50px;">
                            <div class="text-center border-top-only">
                                <input type="number" name="header_size" id="header_size" value="@if(!empty($invoice2_settings->header_size)){{$invoice2_settings->header_size}}@else{{'18'}}@endif" >
                                <span id="header"><strong>TAX STATEMENT</strong></span>
                            </div>
                            <div class="text-center border-top-only">
                                <input name="company_size"  type="number" id="company_size" value="@if(!empty($invoice2_settings->company_size)){{$invoice2_settings->company_size}}@else{{'18'}}@endif" >
                                <span id="company"><strong>Company Name</strong></span>
                            </div>
                            <div class="text-center border-top-only">
                                <input name="address_size"  type="number" id="address_size" value="@if(!empty($invoice2_settings->address_size)){{$invoice2_settings->address_size}}@else{{'18'}}@endif" >
                                <span id="address"><strong>Company Address</strong></span>
                            </div>
                            
                            <table style="width: 100%" class="border-none">
                                <tr>
                                    <td style="width: 34% !important;font-size: 18px !important" class="border-none" id="left_header">
                                        <input type="number" name="left_header_size" id="left_header_size" value="@if(!empty($invoice2_settings->left_header_size)){{$invoice2_settings->left_header_size}}@else{{'18'}}@endif" >
                                        <br>@lang('vat::lang.customer'): <br>
                                        @lang('vat::lang.address'): <br>
                                        @lang('vat::lang.your_vat'): <br>
                                        @lang('vat::lang.payment_method'): Credit<br>
                                         @lang('vat::lang.date_period'):  @lang('contact.to') <br>
                                    </td>
                                    <td style="width: 25% !important;" class="border-none"></td>
                                    <td style="width: 25% !important;" class="border-none " id="right_header">
                                        <input type="number" name="right_header_size" id="right_header_size" value="@if(!empty($invoice2_settings->right_header_size)){{$invoice2_settings->right_header_size}}@else{{'18'}}@endif" >
                                
                                        <br>@lang('vat::lang.vat_registration_no'):<br>
                                        @lang('vat::lang.invoice') #:<br>
                                        @lang('vat::lang.invoice_date'):<br>
                                    </td>
                                    <td style="width: 16% !important;" class="border-none">
                                       
                                    </td>
                                </tr>
                                
                            </table>
                            
                            
                            <table style="width: 100%; border-collapse: collapse; border-top: 2px solid black;"  class="table">
                                
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
                            <div class="col-md-12" style="text-align:center; margin-top:50px" id="system_footer">
                                
                                <input  type="number" name="system_footer_size" id="system_footer_size" value="@if(!empty($invoice2_settings->system_footer_size)){{$invoice2_settings->system_footer_size}}@else{{'18'}}@endif" >
                                
                                This is a computer generated invoice, as such signature are not required</div>
                        
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
                        </div>
                    </div>
                </div>
            </div>
            </form>
        </div>
        @endcomponent
    </div>
</section>