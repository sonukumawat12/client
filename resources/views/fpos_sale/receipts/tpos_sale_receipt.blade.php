

@php
$font_size = 12;
$h_font_size = 12;
$f_font_size = 12;
$b_font_size = 14;
$i_font_size = 12;
$footer_top_margin = 10;
$admin_invoice_footer = 12;
$header_align = 'center';
$contact_details = $receipt_details['contact_details'];
@endphp

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/paper-css/0.3.0/paper.css">

<style>
.header-section {
  line-height: 20px !important;
}
@page { size: A5 landscape; margin: 5px; } body { margin: 5px; padding: 15px; }
@media print {
  

  .header-section {
    line-height: 20px !important;
    font-size : {{$i_font_size}}px !important;
  }

  .row {
    page-break-inside: avoid;
  }

  body {
    margin: 0;
    padding: 20;
  }
  
  
  
  .header1{
      text-transform : capitalize !important;
      /*font-size: 16px;*/
  }
  .header2{
      /*font-size: 13px;*/
  }
  .invoice-box{
      border: 1px solid #000000;
      padding: 5px !important;
      margin: 5px;
      font-weight: bold;
      font-size: {{$i_font_size}}px;
  }
  
  .logo-tag{
      border: 1px solid #000000;
      padding: 5px !important;
      margin: 5px;
      font-weight: bold;
      width: 100% !important;
      font-size: {{ceil(0.6*$h_font_size)}}px;
  }
  
  .separator{
      border-color: #000000;
  }
  
  .tr-head{
      border-color: #000000;
  }
  .business-name{
      font-size: {{$b_font_size}}px !important;
      font-weight: bold;
      text-transform : capitalize !important;
  }
  .logo{
      font-size: {{$h_font_size}}px !important;
      font-weight: bold;
  }
  
  .business-address{
      font-size: {{$i_font_size}}px;
  }
  
  .business-phone{
      font-size: {{$i_font_size}}px;
  }
  
}
</style>

<!-- Your existing HTML content here -->


<!-- Your existing HTML content here -->

<div class="A5" style="width: 100% !important;">
    <div class="row">
        
        <div class="col-xs-8">
            <div class="col-xs-12">
                
                   
                        <span class="business-name">
                			{{strtoupper($receipt_details['business_details']->name)}}
                		</span><br>
        		
                        <span class="business-address">
                            {!! $receipt_details['business_details']->city." ,".$receipt_details['business_details']->state."<br>".$receipt_details['business_details']->country !!}
                        </span>
                   
                
            </div>
            
            <div class="col-xs-4 business-phone">
                <span>
                        {{ $receipt_details['business_details']->mobile }}
                    <br>
                </span>
            </div>
            
            <div class="col-xs-8 text-right">
                <span class="invoice-box" style="width: 100% !important;">{{$receipt_details['sale_details']->tpos_no}}</span>
            </div>
        </div>
    </div>
    <div class="row" style="margin-top: 10px;">
    	
    	<div class="col-xs-12 header-section">
    			<div class="col-xs-6 text-left">
    			    @if(!empty($receipt_details['contact_details']->name))
        			    <small><span class="">
                				{{ strtoupper($receipt_details['contact_details']->name) }}
        			    </span></small>
    			    @endif
    			    
    			</div>
    			<div class="col-xs-6 text-left">
    			    <div class="pull-right">
    			        <small><span class="">{{@format_date($receipt_details['sale_details']->date)}}</span><br>
    			        @if(($receipt_details['sale_details']->reprint_no > 1)
                			<span class="">INVOICE REPRINT COPY - {{ ($receipt_details['sale_details']->reprint_no) }}</span><br></small>
            			@endif
    			    </div>
    			    
    			</div>
    	</div>
    </div>

<div class="row">
	<div class="col-xs-12">
		<table class="table table-responsive">
			<thead>
				<tr>
					<th style="border-color: #000000;border-width: 1px;font-size: {{$font_size}}px !important;">@lang('tpos.sub_category')</th>
					<th style="border-color: #000000;border-width: 1px;font-size: {{$font_size}}px !important;">@lang('tpos.product')</th>
					<th style="border-color: #000000;border-width: 1px;font-size: {{$font_size}}px !important;">@lang('tpos.unit')</th>
					<th style="border-color: #000000;border-width: 1px;font-size: {{$font_size}}px !important;">@lang('tpos.qty')</th>
					<th style="border-color: #000000;border-width: 1px;font-size: {{$font_size}}px !important;">@lang('tpos.total_qty')</th>
				</tr>
			</thead>
			<tbody>
			    @foreach($receipt_details['sale_products'] as $line)
			    <tr>
				    <td style="font-size: {{$font_size}}px !important;padding-top: 0px !important;padding-bottom: 0px !important">{{$line->cat_name}}</td>
					<td style="word-break: break-all; font-size: {{$font_size}}px !important;padding-top: 0px !important;padding-bottom: 0px !important">
						{{strtoupper($line->product_name)}} 
					</td>
					<td style="font-size: {{$font_size}}px !important;padding-top: 0px !important;padding-bottom: 0px !important">{{$line->unit_name}} </td>
					<td style="font-size: {{$font_size}}px !important;padding-top: 0px !important;padding-bottom: 0px !important">{{@num_format($line->qty)}}</td>
					<td style="font-size: {{$font_size}}px !important;padding-top: 0px !important;padding-bottom: 0px !important">{{@num_format($line->qty)}}</td>
				</tr>
    			@endforeach
    			
    			<tr>
					<td colspan="6">&nbsp;</td>
				</tr>
    				
			</tbody>
		
		</table>
		<hr class="separator">
		<div class="row" style="font-size: {{$font_size}}px !important;">
		    
		    <div class="col-xs-6">
		        <div class="col-xs-6">
		           @lang('tpos.total_items')
    		    </div>
    		    
    		    <div class="col-xs-6 text-right">
    		        
    		        <b>0.00</b>
    		       
    		    </div>
    		    
		    </div>
		    
		    <div class="col-xs-6">
		        <div class="col-xs-6">
		           @lang('tpos.total_qty')
    		    </div>
    		    
    		    <div class="col-xs-6 text-right">
    		        
    		        <b>0.00</b>
    		       
    		    </div>
    		    
		    </div>
		</div>
		
		
	</div>
</div>

<br>

</div>
