<section class="content">
    <div class="row">
        <div class="col-md-12">
            <div id="report_div">
                <div id="print_header_div">

                    @php
                        $business_id = session()->get('user.business_id');
                        $business_details = App\Business::find($business_id);
                        $currency_precision = !empty($business_details->currency_precision) ? $business_details->currency_precision : 2;
                    @endphp

                    <style>
                        @media print {
                            #report_print_div {-webkit-print-color-adjust: exact;}
                        }
                        .bg_color {
                            background: #8F3A84 !important;
                            font-size: 20px;
                            color: #fff !important;
                            print-color-adjust: exact;
                        }

                        .text-center {
                            text-align: center;
                        }

                        #customer_detail_table th {
                            background: #8F3A84 !important;
                            color: #fff !important;
                            print-color-adjust: exact;
                        }
                        
                        #customer_statement_table th {
                            background: #8F3A84 !important;
                            color: #fff !important;
                            print-color-adjust: exact;
                        }

                        #customer_detail_table>tbody>tr:nth-child(2n+1)>td,
                        #customer_detail_table>tbody>tr:nth-child(2n+1)>th {
                            background-color: #F3BDEB !important;
                            print-color-adjust: exact;
                        }
                        .uppercase {
                          text-transform: uppercase;
                        }
                    </style>


                	<div class="col-xs-12 text-center text-danger">
                	    <h2 class="text-center"><strong>@lang('petro::lang.daily_status_report')</strong></h2>
                		<p style="font-size: 22px;" class="text-center"><strong>{{request()->session()->get('business.name')}}</strong>
                		</p>
                	</div>
                	<div class="col-md-12">
                	    <p class="text-center" style="color: #8F3A84 !important;print-color-adjust: exact;">
                                        <strong>Date Range from {{date('d M Y',strtotime($start_date))}} to {{date('d M Y',strtotime($end_date))}}</strong></p>
                	</div>
                	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
                		@lang('petro::lang.dip_details_section')
                	</h3>
                	<div class="row">
                		<div class="col-md-12">
                			<table class="table table-striped" id="dip_details_section" style="width: 100%;">
                				<thead>
                					<tr class="row-border">
                						<th>@lang('petro::lang.tank_no')</th>
                						<th>@lang('petro::lang.dip_stick_reading')</th>
                						<th>@lang('petro::lang.qty_in_liters')</th>
                						<th>@lang('petro::lang.qty_in_system')</th>
                						<th>@lang('petro::lang.difference')</th>
                					</tr>
                				</thead>
                			<tbody>
            					@foreach ($dip_sales as $item)
            					<tr>
            						<td>{{ $item->tank_no }}</td>
            						<td>{{ @num_format($item->dip_reading) }}</td>
            						<td>{{ @num_format($item->fuel_balance_dip_reading) }}</td>
            						<td>{{ @num_format($item->current_qty) }}</td>
            						<td>{{ @num_format($item->fuel_balance_dip_reading - $item->current_qty) }}</td>
            					</tr>
            					@endforeach
            				</tbody>
                			</table>
                		</div>
                	</div>
                	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
                		@lang('petro::lang.pump_sales_details')
                	</h3>
                	<div class="row">
                	    <div class="col-md-12">
                    		<table class="table table-striped" id="pump_sales_details" style="width: 100%;">
                    			<thead>
                    				<tr class="row-border">
                    					<th>@lang('petro::lang.pump_no' )</th>
                    					<th>@lang('petro::lang.previous_day_meter' )</th>
                    					<th>@lang('petro::lang.today_meter' )</th>
                    					<th>@lang('petro::lang.sold_qty_liters')</th>
                    					<th>@lang('petro::lang.amount')</th>
                    					<th>@lang('petro::lang.banked_by_3pm')</th>
                    					<th>@lang('petro::lang.locker')</th>
                    					<th>@lang('petro::lang.card')</th>
                    				</tr>
                    			</thead>
            					<tbody>
                					@php
                    					$totalAmount = 0;
                                        $totalBanked = 0;
                                        $totalLocker = 0;
                                        $totalCard = 0;
                					@endphp

                					@foreach ($pump_sales as $item)
                					@php
                    					$totalAmount += $item->amount;
                                        $totalBanked += $item->banked;
                                        $totalLocker += $item->locker;
                                        $totalCard += $item->card;
                					@endphp
                					<tr>
                						<td>{{ $item->pump_no }}</td>
                						<td>{{ @num_format($item->starting_meter) }}</td>
                						<td>{{ @num_format($item->closing_meter) }}</td>
                						<td>{{ @num_format($item->sold_qty) }}</td>
                						<td>{{ @num_format($item->amount) }}</td>
                						<td>{{ @num_format($item->banked) }}</td>
                						<td>{{ @num_format($item->locker) }}</td>
                						<td>{{ @num_format($item->card) }}</td>
                					</tr>
                					@endforeach

                					<tr>
                						<td colspan="4"  class="text-center"><b>@lang('petro::lang.total')</b></td>
                						<td class="text-left">{{@num_format($totalAmount)}}</td>
                						<td class="text-left">{{@num_format($totalBanked)}}</td>
                						<td class="text-left">{{@num_format($totalLocker)}}</td>
                						<td class="text-left">{{@num_format($totalCard)}}</td>
                					</tr>
                				</tbody>
                    		</table>
                	    </div>
                	</div>
                	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
                		@lang('petro::lang.fuel_sale_summary')
                	</h3>
                	<div class="row">
                		<div class="col-md-6">
                			<table class="table table-striped" id="fuel_sale" style="width: 100%;">
                    			<thead>
                    				<tr class="row-border">
                    					<th>@lang('petro::lang.sub_category' )</th>
                    					<th>@lang('petro::lang.qty' )</th>
                    					<th>@lang('petro::lang.total_amount' )</th>
                    				</tr>
                    			</thead>
                    			<tbody>
            					@foreach ($fuel_sales as $item)
            					<tr>
            						<td>{{ $item->name }}</td>
            						<td>{{ @num_format($item->qty) }}</td>
            						<td>{{ @num_format($item->value) }}</td>
            					</tr>
            					@endforeach
            				</tbody>
                    		</table>
                		</div>
                	</div>
                	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
                		@lang('petro::lang.lubricant_sale')
                	</h3>
                	<div class="row">
                		<div class="col-md-12">
                			<table class="table table-striped" id="lubricant_sale" style="width: 100%;">
                				<thead>
                					<tr class="row-border">
                						<th>@lang('petro::lang.product' )</th>
                						<th>@lang('petro::lang.starting_qty' )</th>
                						<th>@lang('petro::lang.purchase_qty' )</th>
                						<th>@lang('petro::lang.sold_qty' )</th>
                						<th>@lang('petro::lang.amount')</th>
                						<th>@lang('petro::lang.balance_qty')</th>
                					</tr>
                				</thead>
                				<tbody>
                					@php
                    					$totalAmount = 0;
                					@endphp

                					@foreach ($lubricant_sales as $item)
                					@php
                    					$totalAmount += $item->amount;
                					@endphp
                					<tr>
                						<td>{{ $item->product }}</td>
                						<td>{{ @num_format($item->starting_qty) }}</td>
                						<td>{{ @num_format($item->purchase_qty) }}</td>
                						<td>{{ @num_format($item->sold_qty) }}</td>
                						<td>{{ @num_format($item->amount) }}</td>
                						<td>{{ @num_format($item->balance_qty) }}</td>
                					</tr>
                					@endforeach

                					<tr>
                						<td colspan="4" class="text-center"><b>@lang('petro::lang.total')</b></td>
                						<td class="text-left">{{@num_format($totalAmount)}}</td>
                					</tr>
                				</tbody>
                			</table>
                		</div>
                	</div>
                	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
                		@lang('petro::lang.other_sales')
                	</h3>
                	<div class="row">
                		<div class="col-md-12">
                			<table class="table table-striped" id="other_sale" style="width: 100%;">
                				<thead>
                					<tr class="row-border">
                						<th>@lang('petro::lang.product' )</th>
                						<th>@lang('petro::lang.starting_qty' )</th>
                						<th>@lang('petro::lang.purchase_qty' )</th>
                						<th>@lang('petro::lang.sold_qty' )</th>
                						<th>@lang('petro::lang.amount')</th>
                						<th>@lang('petro::lang.balance_qty')</th>
                					</tr>
                				</thead>
                				<tbody>
                					@php
                    					$totalAmount = 0;
                					@endphp

                					@foreach ($other_sales as $item)
                					@php
                    					$totalAmount += $item->amount;
                					@endphp
                					<tr>
                						<td>{{ $item->product }}</td>
                						<td>{{ @num_format($item->starting_qty) }}</td>
                						<td>{{ @num_format($item->purchase_qty) }}</td>
                						<td>{{ @num_format($item->sold_qty) }}</td>
                						<td>{{ @num_format($item->amount) }}</td>
                						<td>{{ @num_format($item->balance_qty) }}</td>
                					</tr>
                					@endforeach

                					<tr>
                						<td colspan="4" class="text-center"><b>@lang('petro::lang.total')</b></td>
                						<td class="text-left">{{@num_format($totalAmount)}}</td>
                					</tr>
                				</tbody>
                			</table>
                		</div>
                	</div>
                	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
                		@lang('petro::lang.gas_sales')
                	</h3>
                	<div class="row">
                		<div class="col-md-12">
                			<table class="table table-striped" id="gas_sale" style="width: 100%;">
                				<thead>
                					<tr class="row-border">
                						<th>@lang('petro::lang.product' )</th>
                						<th>@lang('petro::lang.starting_qty' )</th>
                						<th>@lang('petro::lang.purchase_qty' )</th>
                						<th>@lang('petro::lang.sold_qty' )</th>
                						<th>@lang('petro::lang.amount')</th>
                						<th>@lang('petro::lang.balance_qty')</th>
                						<th>@lang('petro::lang.empty_cylinders')</th>
                					</tr>
                				</thead>
                				<tbody>
                					@php
                    					$totalAmount = 0;
                					@endphp

                					@foreach ($gas_sales as $item)
                					@php
                    					$totalAmount += $item->amount;
                					@endphp
                					<tr>
                						<td>{{ $item->product }}</td>
                						<td>{{ @num_format($item->starting_qty) }}</td>
                						<td>{{ @num_format($item->purchase_qty) }}</td>
                						<td>{{ @num_format($item->sold_qty) }}</td>
                						<td>{{ @num_format($item->amount) }}</td>
                						<td>{{ @num_format($item->balance_qty) }}</td>
                					</tr>
                					@endforeach

                					<tr>
                						<td colspan="4" class="text-center"><b>@lang('petro::lang.total')</b></td>
                						<td class="text-left">{{@num_format($totalAmount)}}</td>
                					</tr>
                				</tbody>
                			</table>
                		</div>
                	</div>
                	<h3 class="text-danger" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
                		@lang('petro::lang.total_payment_summary')
                	</h3>
                	<div class="d-flex" style="justify-content: space-between">
                	    @php
                	        $cash = $total_payments->total_cash_payments;
                            $scc = $total_payments->total_cash_payments + $total_payments->total_card_payments + $total_payments->total_cash_deposits + $total_payments->total_credit_sale_payments;
                            $total = $cash + $scc;
                	    @endphp
                		<div>
                		    <h4 class="text-center">@lang('petro::lang.total_sale')</h4>
                		    <p class="text-center">{{ @num_format($total) }}</p>
                		</div>
                		<div>
                		    <h4 class="text-center">@lang('petro::lang.total_card_credit_bank')</h4>
                		    <p class="text-center">{{ @num_format($scc) }}</p>
                		</div>
                		<div>
                		    <h4 class="text-center">@lang('petro::lang.cash')</h4>
                		    <p class="text-center">{{ @num_format($cash) }}</p>
                		</div>
                	</div>
                	<div class="row">
                		<div class="col-md-6">
                        	<h3 class="text-danger text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
                        		@lang('petro::lang.balance_credit_receipt')
                        	</h3>
                		</div>
                		<div class="col-md-6">
                    		 <h3 class="text-danger text-center" style="font-weight: bold; maring-bottom: 0px; font-size: 20px;">
                        		@lang('petro::lang.credit_sales')
                        	</h3>
                        	<table class="table table-striped table-bordered" id="credit_sales" style="width: 100%;">
                				<thead>
                					<tr class="row-border">
                						<th>@lang('petro::lang.customer' )</th>
                						<th>@lang('petro::lang.amount' )</th>
                					</tr>
                				</thead>
                				<tbody>
                					@php
                    					$totalAmount = 0;
                					@endphp

                					@foreach ($credit_sales as $item)
                					@php
                    					$totalAmount += $item->amount;
                					@endphp
                					<tr>
                						<td>{{ $item->customer }}</td>
                						<td>{{ @num_format($item->amount) }}</td>
                					</tr>
                					@endforeach

                					<tr>
                						<td class="text-center"><b>@lang('petro::lang.total')</b></td>
                						<td colspan="4" class="text-left">{{@num_format($totalAmount)}}</td>
                					</tr>
                				</tbody>
                			</table>
                		</div>
                	</div>
            </div>
        </div>
    </div>

</section>
