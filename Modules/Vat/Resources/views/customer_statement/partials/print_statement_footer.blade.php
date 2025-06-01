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
        padding: 50px;
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
</style>

<div class="row" style="margin-top: 0x;">
    <div class="col-md-12">
        <table style="width: 100%; margin-top: 30px; border-collapse: collapse; border-top: 2px solid black;" >
            <tfoot>
                 @php
                    $tax_rate = \App\TaxRate::where('business_id',request()->session()->get('business.id'))->first()->amount ?? 0;
                    $pre_tax = $total / (1+ ($tax_rate/100));
                    $tax_total = ($tax_rate/100) * $pre_tax;
                    $grand_total = $tax_total + $pre_tax;
                 @endphp
                <tr>
                    <td class="border-none" colspan="6" style="width: 70% !important;">&nbsp;</td>
                    <td class="border-bottom-only" colspan="3" style="">&nbsp;</td>
                </tr>
                
                <tr>
                    <td class="text-right zero-padding border-none" colspan="6" style="width: 70% !important;">&nbsp;</td>
                    <td class="text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.total_invoice_amount_with_vat')</td>
                    <td class="text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;"><span id="total-invoice-amount">{{@num_format($total)}}</span></td>
                </tr>
                
                <tr>
                    <td class="text-right zero-padding border-none" colspan="6" style="width: 70% !important;">&nbsp;</td>
                    <td class="text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.tax_base_value')</td>
                    <td class="text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($pre_tax)}}</td>
                </tr>
                
                <tr>
                    <td class="text-right zero-padding border-none" colspan="6" style="width: 70% !important;">&nbsp;</td>
                    <td class="text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.vat') {{$tax_rate}}%</td>
                    <td class="text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($tax_total)}}</td>
                </tr>
                
                 <tr>
                    <td class="text-right zero-padding border-none" colspan="6" style="width: 70% !important;">&nbsp;</td>
                    <td class="text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.price_adjustment')</td>
                    <td class="text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($price_adjustment)}}</td>
                </tr>
                
                <tr>
                    <td class="text-right zero-padding border-none" colspan="6" style="width: 70% !important;">&nbsp;</td>
                    <td class="text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.total_invoice_amount_with_vat')</td>
                    <td class="text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($grand_total+$price_adjustment)}}</td>
                </tr>
            </tfoot>
        </table>
        <table style="width: 100%; margin-top: 30px; border-collapse: collapse; border-top: 2px solid black;" >
            <tfoot>
            <tr>
                    <td class="border-none" colspan="6" style="width: 70% !important;">&nbsp;</td>
                    <td class="border-bottom-only" colspan="3" style="">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-right zero-padding border-none" colspan="6" style="width: 70% !important;">&nbsp;</td>
                    <td class="text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;color: red;" colspan="2"> <span onclick="roundingOnclick()" style="cursor: pointer;"> Amount before rounding Off</span></td>
                    <td class="text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;color: red;"><span id="amount-before-rounding-off">{{$grand_total+$price_adjustment}}</span></td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
