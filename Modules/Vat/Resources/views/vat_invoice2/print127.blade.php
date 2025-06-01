

@php
$font_size = $receipt_details->font_size;
$h_font_size = $receipt_details->header_font_size;
$f_font_size = $receipt_details->footer_font_size;
$b_font_size = $receipt_details->business_name_font_size;
$i_font_size = $receipt_details->invoice_heading_font_size;
$footer_top_margin = $receipt_details->footer_top_margin;
$admin_invoice_footer = $receipt_details->admin_invoice_footer;
$logo_height = $receipt_details->logo_height;
$logo_width = $receipt_details->logo_width;
$logo_margin_top = $receipt_details->logo_margin_top;
$logo_margin_bottom = $receipt_details->logo_margin_bottom;
$header_align = $receipt_details->header_align;
$contact_details = $receipt_details->contact_details;
$tax = $receipt_details->tax_rate->amount ?? 0;

$report_name = \Modules\Vat\Entities\VatSetting::where('business_id',request()->session()->get('user.business_id'))->where('status',1)->first()->tax_report_name ?? 'vat';

@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">

<style>
.header-section {
  line-height: 20px !important;
}
@page { size: A4 potrait; margin: 5px; } body { margin: 5px; padding: 30px 100px 30px 100px; }
@media print {
  

  .header-section {
    line-height: 20px !important;
    font-size : {{$i_font_size}}px !important;
  }

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
    
    .border-top-only{
        border-bottom: 1px solid black;
        border-top: 1px solid black;
        border-left: 1px solid white;
    }
    
    .uppercase{
        text-transform: uppercase;
    }
    
    ul {
        list-style-type: disc !important; /* Force dot as bullet */
        padding-left: 30px;
    }

}
</style>

<!-- Your existing HTML content here -->


<!-- Your existing HTML content here -->

<div class="A4" style="width: 100% !important;">
    
<div class="row">
	<div class="col-xs-12">
		<table style="width: 100%; margin-top: 200px; border-collapse: collapse; border-top: 2px solid black;"  class="table">
		    <tr>
                <td colspan="9" class="text-center border-top-only">
                    <h4 class="uppercase">@lang('vat::lang.'.$report_name) @lang('vat::lang.invoice')</h4>
                </td>
            </tr>
            
		     <tr>
                <td colspan="6" class="text-left bordered zero-padding" style="">
                    <h5 class="uppercase"><strong>@lang('vat::lang.seller')</strong></h5>
                    <p>
                        {{ucfirst($receipt_details->business_location->name)}}<br>
                        {{($receipt_details->business_location->address_1)}} <br>
                        {{($receipt_details->business_location->address_2)}}<br>
                        {{($receipt_details->business_location->address_3)}}
                    </p>
                </td>
                <td colspan="3" class="text-left bordered zero-padding" style="">
                    <h5 class="uppercase"><strong>@lang('vat::lang.invoice_no'):</strong> {{$receipt_details->invoice_no}}</h5>
                    <h5 class="uppercase"><strong>@lang('vat::lang.date'):</strong> {{$receipt_details->invoice_date}}</h5>
                </td>
            </tr>
             <tr>
                <td colspan="6" class="text-left bordered zero-padding" rowspan="2" style="">
                    <h5 class="uppercase"><strong>@lang('vat::lang.buyer')</h5></strong>
                    <p>
                        {{ucfirst($receipt_details->customer_name)}}<br>
                        {{($receipt_details->customer->address)}} <br>
                        {{($receipt_details->customer->address_2)}}<br>
                        {{($receipt_details->customer->address_3)}}
                    </p>
                    
                </td>
                <td colspan="3" class="text-left bordered zero-padding" style="">
                    <h5 class="uppercase"><strong>@lang('vat::lang.delivery_to')</strong></h5>
                </td>
            </tr>
             <tr>
                <td colspan="2" class="text-left bordered zero-padding" style="">
                    <h5 class="uppercase"><strong>@lang('vat::lang.payment_terms')</strong></h5>
                </td>
                <td class="text-right bordered zero-padding" style="">&nbsp;</td>
            </tr>
            <tr>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 5%">@lang('vat::lang.s_no')</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 30%">@lang('vat::lang.description_of_goods')</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 15%">@lang('vat::lang.reference_no')</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 8%">@lang('vat::lang.qty')</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.base_unit_rate')</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.discount')</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.total_tax_payable') {{$tax}} %</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.net_amount')</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 20%">@lang('vat::lang.total_payable_with_tax')</td>
            </tr>
            @php $i=0; @endphp
            @forelse($bill_details as $line)
            @php $i++; @endphp
            <tr style="">
                <td class="text-center bordered zero-padding" style="">{{$i}}</td>
                <td class=" bordered zero-padding" style=" word-break: break-all;">
                    {{strtoupper($line->product_name)}}
                </td>
                <td class="text-right bordered zero-padding" style="">{{$receipt_details->reference_no}}</td>
                <td class="text-right bordered zero-padding" style="">{{@num_format($line->qty)}}</td>
                <td class="text-right bordered zero-padding" style="">{{@num_format($line->unit_price_before_tax)}}</td>
                <td class="text-right bordered zero-padding" style="">{{@num_format($line->discount)}}</td>
                <td class="text-right bordered zero-padding" style="">{{@num_format($line->unit_vat_rate*$line->qty)}}</td>
                <td class="text-right bordered zero-padding" style="">{{@num_format($line->unit_price_before_tax*$line->qty)}}</td>
                <td class="text-right bordered zero-padding" style="">{{@num_format($line->sub_total)}}</td>
            </tr>
            
            @empty
            <tr>
                <td colspan="9" style="">&nbsp;</td>
            </tr>
            @endforelse
            <tr>
                <td class="text-left bordered zero-padding" colspan="9" style="">&nbsp;</td>
            </tr>
            <tr>
                <td class="text-left bordered zero-padding" colspan="8" style="">@lang('vat::lang.total_with_vat')</td>
                <td class="text-right bordered zero-padding">{{$receipt_details->total_with_vat}}</td>
            </tr>
            <tr>
                <td class="text-left bordered zero-padding" colspan="9" style="">
                    <h5 class="uppercase text-center"><u><strong>@lang('vat::lang.bank_details')</strong></u></h5><br>
                    <h5 class="uppercase"><strong>@lang('vat::lang.bank_name'):</strong> {{!empty($receipt_details->bank_detail) ? $receipt_details->bank_detail->bank_name : ''}} </h5>
                    <h5 class="uppercase"><strong>@lang('vat::lang.bank_branch'):</strong> {{!empty($receipt_details->bank_detail) ? $receipt_details->bank_detail->bank_branch : ''}} </h5>
                    <h5 class="uppercase"><strong>@lang('vat::lang.account_number'):</strong> {{!empty($receipt_details->bank_detail) ? $receipt_details->bank_detail->account_number: ''}} </h5>
                    <h5 class="uppercase"><strong>@lang('vat::lang.account_name'):</strong> {{!empty($receipt_details->bank_detail) ? $receipt_details->bank_detail->account_name : ''}} </h5>
                    
                    <h5 class="uppercase"><strong><u>@lang('vat::lang.special_instructions')</u></strong></h5>
                    @if (!empty($receipt_details->bank_detail))
                        <ul>
                            <li>{{ $receipt_details->bank_detail->special_instructions }}</li>
                        </ul>
                    @endif
                    
                </td>
            </tr>
            <tr>
                <td class="text-left bordered zero-padding" colspan="9" style="">
                    <ul>
                        <li><strong>@lang('vat::lang.for_any_concerns_contact')</strong></li>
                        @if (!empty($receipt_details->concern))
                            <ul>
                                <li>{{ $receipt_details->concern->line_1 }}</li>
                                <li>{{ $receipt_details->concern->line_2 }}</li>
                                <li>{{ $receipt_details->concern->line_3 }}</li>
                                <li>{{ $receipt_details->concern->line_4 }}</li>
                                <li>{{ $receipt_details->concern->line_5 }}</li>
                            </ul>
                        @endif
                        <li><strong>@lang('vat::lang.supply_from')</strong></li>
                        <p>{{!empty($receipt_details->supply_from) ? $receipt_details->supply_from->supply_from : ''}}</p>
                    </ul>
                
                </td>
            </tr>
            <tr>
                <td class="text-left bordered zero-padding" colspan="5" style="">
                    @if(!empty(request()->ack))
                        <h5 class="uppercase"><strong><u>@lang('vat::lang.acknowledgement')</u></strong></h5>
                        <p>@lang('vat::lang.ack_text')</p>
                        <p>@lang('vat::lang.vehicle_no'): ................... @lang('vat::lang.driver'): ..................... @lang('vat::lang.date') : .......................</p>
                        <p>@lang('vat::lang.customer'): ................... @lang('vat::lang.date'): ..................... @lang('vat::lang.time') : ........................</p>
                    @endif
                
                </td>
                <td class="text-left bordered zero-padding" colspan="4" style="">
                    <h5 class="text-center"><strong>@lang('vat::lang.authorised_by')</strong></h5>
                    <br><br>
                    <p class="text-center">.............................................................................</p>
                    <br>
                </td>
            </tr>
        </table>
		
	</div>
</div>

</div>