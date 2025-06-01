
@php
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp
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

<table style="width: 100%;margin-bottom: 30px;">
    @if(!empty($location_details->name))
        <tr>
            <td class="text-center border-none uppercase" colspan="3"><h4><strong>{{$location_details->name}}</strong></h4></td>
        </tr>
    @endif
    
    @if(!empty($location_details->address))
        <tr>
            <td class="text-center border-none" colspan="3"><h5><strong>{{$location_details->address}}</strong></h5></td>
        </tr>
    @endif
    <tr>
        
        <td class="text-center border-none" colspan="3"><h5><strong>@lang('vat::lang.vat_invoice')</strong></h5></td>
    </tr>
    <tr>
        <td colspan="2" class="text-left border-none"><h5><strong>{{$statement_no}}</strong></h5></td>
        <td class="text-right border-none"><h5><strong>@lang('vat::lang.date'): </strong> {{ @format_date(date('Y-m-d')) }}</h5></td>
    </tr>
    <tr>
        <td class="text-left border-none uppercase"><h5><strong>@lang('vat::lang.our_vat_no'): </strong>{{ $business_details->tax_number_1 }}</h5></td>
        <td class="text-leftt border-none uppercase"><h5><strong>@lang('vat::lang.your_vat'): </strong>{{$contact->vat_number}}</h5></td>
        <td class="text-leftt border-none uppercase"></td>
    </tr>
    <tr>
        <td colspan="2" class="text-left border-bottom-only"><h5><strong>@lang('vat::lang.customer'): </strong>{{$contact->name}}</h5></td>
        <td class="text-right border-bottom-only"><h5><strong>@lang('vat::lang.payment_method'): </strong>@lang('vat::lang.credit')</h5></td>
    </tr>
</table>