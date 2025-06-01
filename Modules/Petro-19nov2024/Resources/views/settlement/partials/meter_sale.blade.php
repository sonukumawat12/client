@php
 $currency_precision = 2;
 $meter_sale_arr = [];
 if(!empty($active_settlement) && request()->segment(2)=='edit')
 	$meter_sale_arr = $active_settlement->meter_sales->toArray()[0];
 @endphp
<br>
<div class="row" id="meter-sale-form-block">
	@include('petro::settlement.partials.meter_sale_form', ['meter_sale'=>$meter_sale_arr])
</div>
<br>
<br>
<div class="row">
	<div class="col-md-12">
		<table class="table table-bordered table-striped" id="meter_sale_table">
			<thead>
				<tr>
					<th>@lang('petro::lang.code' )1234</th>
					<th>@lang('petro::lang.products' )</th>
					<th>@lang('petro::lang.pump' )</th>
					<th style="width: 10px;">@lang('petro::lang.starting_meter')</th>
					<th style="width: 10px;">@lang('petro::lang.closing_meter')</th>
					<th>@lang('petro::lang.price')</th>
					<th>@lang('petro::lang.sold_qty' )</th> {{-- Qty = Closing Meter- Starting Meter - Testing Qty --}}
					<th style="width: 10px;">@lang('petro::lang.discount_type' )</th>
					<th style="width: 6px;">@lang('petro::lang.discount_value' )</th>
					<th style="width: 10px;">@lang('petro::lang.testing_qty' )</th>
					<th>@lang('petro::lang.total_qty' )</th>
					<th>@lang('petro::lang.before_discount' )</th>
					<th>@lang('petro::lang.after_discount' )</th>
					<th>@lang('petro::lang.action' )</th>
				</tr>
			</thead>
			<tbody>
				@php
    				$final_total = 0.00;
				@endphp
				@if (!empty($active_settlement))
    				@foreach ($active_settlement->meter_sales as $item)
        				@php
            				$product = App\Product::where('id', $item->product_id)->first();
            				$pump = Modules\Petro\Entities\Pump::where('id', $item->pump_id)->first();
            				$later_settlements = Modules\Petro\Entities\MeterSale::where('pump_id',$item->pump_id)->where('id','>',$item->id)->whereNotNull('transaction_id')->count();
        				@endphp
        				<tr>
                            @php
                                $qauntity = $item->closing_meter - $item->starting_meter - $item->testing_qty;
                                
                                if($pump->bulk_sale_meter == 1){
                                    $qauntity = $item->qty;
                                }
                                
                                $subTotal = $item->sub_total;
                                $withDiscount = $item->discount_amount;
                                $final_total += $withDiscount;
                            @endphp
        					<td>{{$product->sku}}</td>
        					<td><span class="product_name">{{$product->name}}</span></td>
        					
        					<td>{{$pump->pump_no}}</td>
        					<td>{{number_format($item->starting_meter, $meeter_precision, '.', ',')}}</td>
        					<td>{{number_format($item->closing_meter, $meeter_precision, '.', ',')}}</td>
        					<td>{{number_format($item->price, $currency_precision)}}</td>
        					
        					<td><span class="sold_qty">{{number_format($qauntity, $meeter_precision)}}</span></td>
        					
        					<td>{{ isset($discount_types[$item->discount_type]) ? $discount_types[$item->discount_type] : ''}}</td>
        					
        					<td>{{number_format($item->discount, $currency_precision)}}</td>
        					<td>{{number_format($item->testing_qty, $currency_precision)}}</td>
        					<td>{{number_format(($item->testing_qty+$qauntity), $meeter_precision)}}</td>
        					<td>{{number_format($subTotal, $currency_precision)}}</td>
                            <td>{{number_format($withDiscount, $currency_precision)}}</td>
        					<td>
									<button class="btn btn-xs btn-primary get_meter_sale_from" data-type="edit" data-href="/petro/settlement/get-meter-sale-form/{{$item->id}}"><i class="fa fa-edit"></i></button>
        					    @if($later_settlements < 1 || empty($item->transaction_id) || $pump->bulk_tank == 1)
            					    <button class="btn btn-xs btn-danger delete_meter_sale" data-href="/petro/settlement/delete-meter-sale/{{$item->id}}"><i class="fa fa-times"></i></button>
            					@endif
        					</td>
        				</tr>
    				@endforeach
				@endif
			</tbody>
			<tfoot>
				<tr>
				    <td colspan="6"></td>
				    <td><span class="product_summary"></span></td>
					<td colspan="3" style="text-align: right; font-weight: bold;">@lang('petro::lang.meter_sale_total')
						:</td>
					<td style="text-align: left; font-weight: bold;" class="meter_sale_total">
						{{number_format( $final_total, $currency_precision)}}</td>
				</tr>
				<input type="hidden" value="{{$final_total}}" name="meter_sale_total" id="meter_sale_total">
			</tfoot>
		</table>
	</div>
</div>

<div class="table-responsive 1234" id="outside_meter_sale_table"  style="display: none;">
    <table class="table table-bordered table-striped" id="pump_operator_meter_sale_table"
        style="width: 100%;">
        <thead>
            <tr>
                    <th>@lang('petro::lang.code' )1234</th>
					<th>@lang('petro::lang.products' )</th>
					<th>@lang('petro::lang.pump' )</th>
					<th style="width: 10px;">@lang('petro::lang.starting_meter')</th>
					<th style="width: 10px;">@lang('petro::lang.closing_meter')</th>
					<th>@lang('petro::lang.price')</th>
					<th>@lang('petro::lang.sold_qty' )</th> {{-- Qty = Closing Meter- Starting Meter - Testing Qty --}}
					<th style="width: 10px;">@lang('petro::lang.discount_type' )</th>
					<th style="width: 6px;">@lang('petro::lang.discount_value' )</th>
					<th style="width: 10px;">@lang('petro::lang.testing_qty' )</th>
					<th>@lang('petro::lang.total_qty' )</th>
					<th>@lang('petro::lang.before_discount' )</th>
					<th>@lang('petro::lang.after_discount' )</th>
					<th>@lang('petro::lang.action' )</th>
            </tr>
        </thead>

        <tfoot>
            <tr class="bg-gray font-17 footer-total">
                <td colspan="8" class="text-right" style="color:brown">
                    <strong>@lang('sale.total'):</strong></td>
                <td style="color:brown"><span class="display_currency" id="footer_list_other_sales_amount" data-currency_symbol="true"></span>
            </tr>
        </tfoot>
    </table>
</div>