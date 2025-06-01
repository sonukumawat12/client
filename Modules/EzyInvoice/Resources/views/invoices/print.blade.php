<!-- app css -->


<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<link rel="stylesheet" href="{{ asset('css/app.css?v='.$asset_v) }}">
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/css/bootstrap.min.css">
<title>@lang('ezyinvoice::lang.print_settlement')</title>

@php
$business_id = session()->get('user.business_id');
$business_details = App\Business::find($business_id);
$currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
@endphp


<style>
	.settlement_print_div table. {
		border: 1px solid #222;
		margin-top: 10px;
		margin-bottom: 0px;
	}

	.settlement_print_div table.table-bordered>thead>tr>th {
		border: 1px solid #222;
		;
	}

	.settlement_print_div table.table-bordered>tbody>tr>td {
		border: 1px solid #222;
		font-size: 13px;
	}

	.settlement_print_div {
		max-width: 70%;
	}
	
      /* Signature style */
      .signature {
        text-align: center;
        margin-top: 10px;
        width: 33.33%; /* Each signature takes 1/3 of the width */
      }
    
      .signature span {
        margin-right: 5px;
      }
      .center-top {
        text-align: center;
        width: 100%;
      }	
	  .left-align {
        text-align: left;
        width:100%;
      }
	

	@media print {

		.no-print,
		.no-print * {
			display: none !important;
		}
	}
</style>

<div class="container settlement_print_div" style="width: 65%; margin-auto">
	<div class="col-xs-12 text-center">
		<p style="font-size: 22px;" class="text-center"><strong>{{request()->session()->get('business.name')}}</strong>
		</p>
		<p style="font-size: 16px;"><strong>@lang('ezyinvoice::lang.pump_operator_sale_report')</strong></p>

		<a style=" border-radius: 0px !important; float: right; margin-bottom: 10px;" class="btn btn-success btn-sm btn-flat pull-right  no-print"
			href="{{action('\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@index')}}">@lang('ezyinvoice::lang.back_to_invoice')</a>

	</div>
	<div class="clearfix"></div>
	<br>
	<div class="col-xs-12 text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 18px;">
		@lang('ezyinvoice::lang.credit_sales')
	</div>
	<div class="center-top">
	    <div>
            29COPY
        </div>
        <div>
            1 1,CP,SL
        </div>
        
        <p>MOBILE: 55555444 EMAIL: ABCD@1.COM</p>
    </div>
    
    <div class="left-align">
        <div>
            Customer: {{!empty($customer_name) ? $customer_name->name : ''}}
        </div>
        <div>
            Address: {{!empty($customer_name) ? $customer_name->name : ''}}
        </div>
        <div>
            VAT NO: {{!empty($customer_name) ? $customer_name->name : ''}}
        </div>
        <div>
            Payment Method: {{!empty($customer_name) ? $customer_name->name : ''}}
        </div>
    </div>
	<div class="row">
		<div class="col-md-12">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>@lang('ezyinvoice::lang.cusotmer_name' )</th>
						<th>@lang('ezyinvoice::lang.current_outstanding_before_sale' )</th>
						<th>@lang('ezyinvoice::lang.voucher_no' )</th>
						<th>@lang('ezyinvoice::lang.product_name' )</th>
						<th>@lang('ezyinvoice::lang.qty' )</th>
						<th>@lang('ezyinvoice::lang.unit_rate' )</th>
						<th>@lang('ezyinvoice::lang.total' )</th>
					</tr>
				</thead>
				<tbody>
					@php
					$credit_sale_total = $settlement->credit_sale_payments->sum('amount');
					@endphp
					@if(!empty($settlement->credit_sale_payments ))
					@foreach ($settlement->credit_sale_payments as $credit_sale_payment)
					@php
					$customer_name = App\Contact::where('id', $credit_sale_payment->customer_id)->first();
					$product = App\Product::where('id', $credit_sale_payment->product_id)->first();
					@endphp
					<tr>
						<td>{{!empty($customer_name) ? $customer_name->name : ''}}</td>
						<td>{{@num_format($credit_sale_payment->outstanding)}}</td>
						<td>{{$credit_sale_payment->order_number}}</td>
						<td>{{!empty($product)? $product->name : '' }}</td>
						<td>{{@num_format($credit_sale_payment->qty)}}</td>
						<td>{{@num_format($credit_sale_payment->price)}}</td>
						<td class="credit_sale_amount text-right">
							{{@num_format($credit_sale_payment->price * $credit_sale_payment->qty)}}</td>
					</tr>
					@endforeach
					@endif
					<tr>
						<td colspan="5" style="text-align: right;"><b>@lang('ezyinvoice::lang.sub_total')</b></td>
						<td class="text-right">{{@num_format($credit_sale_total)}}</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
	
	<hr>
	<hr>
	
    <div class="row">	
        <div class="signature">
          <span style="margin-right: 5px">-------------------------</span>
          <p>Prepared By</p>
        </div>
        
        <div class="signature">
          <span style="margin-right: 5px">-------------------------</span>
          <p>Checked By</p>
        </div>
        
        <div class="signature">
          <span style="margin-right: 5px">-------------------------</span>
          <p>Customer Signature</p>
        </div>
        
    </div>
    
	
	<div style="margin-top:10px;text-align:center;">
	    <p>Thank You! Come Again. The Software is developed by SYZYGY Technologies. Contact: 077 4055 434/071 3636 192</p>
	</div>

	<div class="clearfix"></div>
	<br>
	<div class="col-xs-12 text-center no-print"
		style="font-weight: bold; margin-bottom: 20px !important; margin-top: 10px !important; ">
		<a style=" border-radius: 0px !important;" class="btn btn-success btn-sm btn-flat"
			href="{{action('\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@index')}}">@lang('ezyinvoice::lang.back_to_invoice')</a>
	</div>
</div>