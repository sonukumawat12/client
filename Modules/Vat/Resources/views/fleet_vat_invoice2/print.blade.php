

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

$invoice2_settings = \Modules\Vat\Entities\VatInvoice2Setting::where('business_id',request()->session()->get('user.business_id'))->first()->settings ?? json_encode(array());
$invoice2_settings = (object) json_decode($invoice2_settings);

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
    
    .uppercase{
        text-transform: uppercase;
    }
    
    .header_size{
        font-size: @if(!empty($invoice2_settings->header_size)) {{$invoice2_settings->header_size}}px !important; @else 18px !important; @endif
    }
    .company_size{
        font-size: @if(!empty($invoice2_settings->company_size)) {{$invoice2_settings->company_size}}px !important; @else 18px !important; @endif
    }
    .address_size{
        font-size: @if(!empty($invoice2_settings->address_size)) {{$invoice2_settings->address_size}}px !important; @else 18px !important; @endif
    }
    .customer_size{
        font-size: @if(!empty($invoice2_settings->customer_size)) {{$invoice2_settings->customer_size}}px !important; @else 18px !important; @endif
    }
    .vat_size{
        font-size: @if(!empty($invoice2_settings->vat_size)) {{$invoice2_settings->vat_size}}px !important; @else 18px !important; @endif
    }
    .method_size{
        font-size: @if(!empty($invoice2_settings->method_size)) {{$invoice2_settings->method_size}}px !important; @else 18px !important; @endif
    }
    .registration_size{
        font-size: @if(!empty($invoice2_settings->registration_size)) {{$invoice2_settings->registration_size}}px !important; @else 18px !important; @endif
    }
    .invoice_size{
        font-size: @if(!empty($invoice2_settings->invoice_size)) {{$invoice2_settings->invoice_size}}px !important; @else 18px !important; @endif
    }
    .date_size{
        font-size: @if(!empty($invoice2_settings->date_size)) {{$invoice2_settings->date_size}}px !important; @else 18px !important; @endif
    }
    .thead_size{
        font-size: @if(!empty($invoice2_settings->thead_size)) {{$invoice2_settings->thead_size}}px !important; @else 18px !important; @endif
    }
    .tbody_size{
        font-size: @if(!empty($invoice2_settings->tbody_size)) {{$invoice2_settings->tbody_size}}px !important; @else 18px !important; @endif
    }
    .sub_size{
        font-size: @if(!empty($invoice2_settings->sub_size)) {{$invoice2_settings->sub_size}}px !important; @else 18px !important; @endif
    }
    .footer_size{
        font-size: @if(!empty($invoice2_settings->footer_size)) {{$invoice2_settings->footer_size}}px !important; @else 18px !important; @endif
    }

}
</style>

<!-- Your existing HTML content here -->


<!-- Your existing HTML content here -->

<div class="A4" style="width: 100% !important;">
    <div class="row">
        <div class="col-xs-12 text-center">
            <h2 class="uppercase header_size">@lang('vat::lang.'.$report_name) @lang('vat::lang.invoice')</h2>
            <h4 class="company_size">{{strtoupper($receipt_details->display_name)}}</h4>
            <h5 class="address_size">{!! strtoupper($receipt_details->address) !!}</h5>
            <h5 class="address_size">{{ strtoupper($receipt_details->contact) }}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4 text-left">
            <h5 class="customer_size">@lang('vat::lang.customer'): {{ strtoupper($receipt_details->customer_name) }}</h5>
            <h5 class="customer_size">@lang('vat::lang.address'): {{ $receipt_details->customer->address }}</h5>
            <h5 class="vat_size">@lang('contact.vat_number'): {{ $receipt_details->customer->vat_number }}</h5>
        </div>
        <div class="col-xs-3 text-left">
            
        </div>
        <div class="col-xs-3 text-left">
            <h5 class="registration_size">@lang('vat::lang.vat_registration_no'):</h5>
            <h5 class="uppercase invoice_size">@lang('vat::lang.invoice') #:</h5>
            <h5 class="uppercase date_size">@lang('vat::lang.invoice_date'):</h5>
        </div>
        <div class="col-xs-2 text-left">
            <h5 class="registration_size">{{$receipt_details->tax_info1 }}.</h5>
            <h5 class="invoice_size">{{$receipt_details->invoice_no}}</h5>
            <h5 class="invoice_date">{{$receipt_details->invoice_date}}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-2 text-left">
            <h5 class="method_size">@lang('vat::lang.payment_method'): </h5>
        </div>
        <div class="col-xs-3 text-left">
            @foreach($payment_details as $pmt)
                <h5 class="method_size">{{ucfirst(str_replace('_',' ',$pmt->method))}}</h5>
            @endforeach
        </div>
        
    </div>


<div class="row">
	<div class="col-xs-12">
		<table style="width: 100%; margin-top: 30px; border-collapse: collapse; border-top: 2px solid black;"  class="table">
            <tr>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 7%">@lang('vat::lang.s_no')</td>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 30%">@lang('vat::lang.description')</td>
                <td class="uppercase text-center text-bold bordered zero-padding" style="width: 15%">@lang('vat::lang.reference_no')</td>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.qty')</td>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.unit_price')</td>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 10%">@lang('vat::lang.discount')</td>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 15%">@lang('vat::lang.unit_vat') {{$tax}} %</td>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding" style="width: 15%">@lang('vat::lang.amount')</td>
            </tr>
            @php $i=0; @endphp
            @forelse($bill_details as $line)
            @php $i++; @endphp
            <tr style="">
                <td class="tbody_size text-center bordered zero-padding" style="">{{$i}}</td>
                <td class="tbody_size  bordered zero-padding" style=" word-break: break-all;">
                    {{strtoupper($line->product_name)}}
                </td>
                <td class="text-right bordered zero-padding" style="">{{$receipt_details->reference_no}}</td>
                <td class="tbody_size text-right bordered zero-padding" style="">{{@num_format($line->qty)}}</td>
                <td class="tbody_size text-right bordered zero-padding" style="">{{@num_format($line->unit_price_before_tax)}}</td>
                <td class="tbody_size text-right bordered zero-padding" style="">{{@num_format($line->discount)}}</td>
                <td class="tbody_size text-right bordered zero-padding" style="">{{@num_format($line->unit_vat_rate)}}</td>
                <td class="tbody_size text-right bordered zero-padding" style="">{{@num_format($line->sub_total)}}</td>
            </tr>
            
            @empty
            <tr>
                <td colspan="8" style="">&nbsp;</td>
            </tr>
            @endforelse
            <tr>
                <td class="border-none" colspan="5" style="">&nbsp;</td>
                <td class="border-bottom-only" colspan="3" style="">&nbsp;</td>
            </tr>
            <tr>
                <td class="text-right zero-padding border-none" colspan="5" style="">&nbsp;</td>
                <td class="sub_size text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2">@lang('vat::lang.total_invoice_amount_with_vat')</td>
                <td class="sub_size text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{$receipt_details->total_with_vat}}</td>
            </tr>
            <tr>
                <td class="text-right zero-padding border-none" colspan="5" style="">&nbsp;</td>
                <td class="sub_size text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="2"> @lang('vat::lang.tax_base_value')</td>
                <td class="sub_size text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{$receipt_details->total}}</td>
            </tr>
            <tr>
                <td class="text-right zero-padding border-none" colspan="5" style="">&nbsp;</td>
                <td class="sub_size text-left bordered zero-padding" colspan="2">@lang('vat::lang.vat') @if(!empty($receipt_details->tax_rate)) ({{$receipt_details->tax_rate->amount}}%)  @endif</td>
                <td class="sub_size text-right bordered zero-padding">
                    {{$receipt_details->total_vat}}
                </td>
            </tr>
            
            
            <tr>
                <td class="text-right zero-padding border-none" colspan="5" style="">&nbsp;</td>
                <td class="sub_size text-left bordered zero-padding" colspan="2">@lang('vat::lang.price_adjustment')</td>
                <td class="sub_size text-right bordered zero-padding">{{$receipt_details->price_adjustment}}</td>
            </tr>
            <tr>
                <td class="text-right zero-padding border-none" colspan="5" style="">&nbsp;</td>
                <td class="sub_size text-left bordered zero-padding" colspan="2">@lang('vat::lang.total_invoice_amount_with_vat')</td>
                <td class="sub_size text-right bordered zero-padding">{{$receipt_details->final_total}}</td>
            </tr>
            
        </table>
		
	</div>
</div>

<br>

<div class="row">
	<div class="footer_size col-xs-12 text-center">
		
		<div class="col-xs-4 text-center pad-50">
		    <hr class="separator">
		    <h5>@lang('vat::lang.prepared_by')</h5>
		</div>
		
		<div class="col-xs-4 text-center pad-50">
		    <hr class="separator">
		    <h5>@lang('vat::lang.checked_by')</h5>
		</div>
		
		<div class="col-xs-4 text-center pad-50">
		    <hr class="separator">
		    <h5>@lang('vat::lang.customer_signature')</h5>
		</div>
		
	</div>
</div>


@if(!empty($admin_invoice_footer))
<div class="row">
	<div class="col-xs-12 text-center">
		<p class="centered"
			style="font-size: {{$f_font_size}}px !important; margin-top: @if(!empty($footer_top_margin)) {{$footer_top_margin }}px; @else 10px; @endif">
			{!! $admin_invoice_footer !!}
		</p>
	</div>
</div>
@endif
</div>