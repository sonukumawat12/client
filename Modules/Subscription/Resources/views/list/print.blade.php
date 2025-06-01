

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">

<style>
.header-section {
  line-height: 20px !important;
}
@page { size: A4 potrait; margin: 5px; } body { margin: 5px; padding: 30px 100px 30px 100px; }
@media print {
  

  .header-section {
    line-height: 20px !important;
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

}
</style>

<!-- Your existing HTML content here -->


<!-- Your existing HTML content here -->

<div class="A4" style="width: 100% !important;">
    <div class="row">
        <div class="col-xs-12 text-center">
            <h2 class="uppercase header_size">@lang('vat::lang.invoice')</h2>
            <h4 class="company_size">{{strtoupper($location_details->name)}}</h4>
            <h5 class="address_size">{{ strtoupper($location_details->city.",".$location_details->state." ".$location_details->country) }}</h5>
            <h5 class="address_size">{{ strtoupper($location_details->mobile) }}</h5>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4 text-left">
            <h5 class="customer_size">@lang('vat::lang.customer'): {{ strtoupper($settings->customer_name) }}</h5>
            <h5 class="customer_size">@lang('vat::lang.address'): {{ $settings->customer->address }}</h5>
            <h5 class="vat_size">@lang('contact.vat_number'): {{ $settings->customer->vat_number }}</h5>
        </div>
        <div class="col-xs-3 text-left">
            
        </div>
        <div class="col-xs-2 text-left">
            <h5 class="uppercase date_size">@lang('vat::lang.invoice_date'):</h5>
            <h5 class="uppercase date_size">@lang('subscription::lang.subscription_period'):</h5>
        </div>
        <div class="col-xs-3 text-left">
            <h5 class="invoice_date">{{@format_date($settings->expiry_date)}}</h5>
            <h5 class="invoice_date">{{@format_date($_GET['start_date'])}} - {{@format_date($_GET['end_date'])}}</h5>
        </div>
    </div>
    
<div class="row">
	<div class="col-xs-12">
		<table style="width: 100%; margin-top: 30px; border-collapse: collapse; border-top: 2px solid black;"  class="table">
            <tr>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding">@lang('vat::lang.s_no')</td>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding">@lang('vat::lang.product')</td>
                <td class="thead_size uppercase text-center text-bold bordered zero-padding">@lang('vat::lang.amount')</td>
            </tr>
            @php $i=0;$total = 0; @endphp
            @foreach($products as $line)
            @php $i++; $total += $line['price']; @endphp
            <tr style="">
                <td class="tbody_size text-center bordered zero-padding" style="">{{$i}}</td>
                <td class="tbody_size  bordered zero-padding" style=" word-break: break-all;">
                    {{strtoupper($line['product'])}}
                </td>
                <td class="tbody_size text-right bordered zero-padding" style="">{{@num_format($line['price'])}}</td>
            </tr>
            
            @endforeach
             
             
           
                <tr>
                    <td class="border-none" colspan="1" style="">&nbsp;</td>
                    <td class="border-bottom-only" colspan="2" style="">&nbsp;</td>
                </tr>
                <tr>
                    <td class="text-right zero-padding border-none" colspan="1" style="">&nbsp;</td>
                    <td class="sub_size text-left bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;" colspan="1">@lang('vat::lang.total')</td>
                    <td class="sub_size text-right bordered zero-padding" style="border-collapse: collapse; border-top: 2px solid black;">{{@num_format($total)}}</td>
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

</div>