@php
    $default_store = request()->session()->get('business.default_store');
@endphp
<br>
<div class="col-md-12">
    <div class="row">
        <div class="col-md-3 pull-right">
            <div class="checkbox pull-right">
                <label>
                {!! Form::checkbox('show_bulk_tank', 1, false, ['class' => 'input-icheck', 'id' => 'show_bulk_tank']);
                !!}
                @lang('petro::lang.bulk_tank')
                </label>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="col-md-2 store_field">
            <div class="form-group">
                {!! Form::label('store', __('petro::lang.store').':') !!}
                {!! Form::select('store_id', $stores, $default_store, ['class' => 'form-control check_pumper
                select2', 'style' => 'width: 100%;', 'id' => 'store_id',
                'placeholder' => __('petro::lang.please_select')]); !!}
            </div>
        </div>
        <div class="col-md-2 bulk_tank_field hide">
            <div class="form-group">
                {!! Form::label('bulk_tank', __('petro::lang.bulk_tank').':') !!}
                {!! Form::select('bulk_tank', $bulk_tanks, null, ['class' => 'form-control check_pumper
                select2', 'style' => 'width: 100%;', 'id' => 'bulk_tank',
                'placeholder' => __('petro::lang.please_select')]); !!}
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                {!! Form::label('item', __('petro::lang.select_item').':') !!}
                {!! Form::select('item', $items, null, ['class' => 'form-control other_sale_fields check_pumper
                select2', 'style' => 'width: 100%;','placeholder' => __('petro::lang.please_select')]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('balance_stock', __( 'petro::lang.balance_stock' ) ) !!}
                {!! Form::text('balance_stock', null, ['class' => 'form-control other_sale_fields check_pumper input_number
                balance_stock', 'required', 'readonly',
                'placeholder' => __(
                'petro::lang.balance_stock' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('other_sale_price', __( 'petro::lang.price' ) ) !!}
                {!! Form::text('other_sale_price', null, ['class' => 'form-control other_sale_fields check_pumper input_number
                other_sale_price', 'required', 'readonly',
                'placeholder' => __(
                'petro::lang.price' ) ]); !!}
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="col-md-2 col-md-offset-3">
            <div class="form-group">
                {!! Form::label('other_sale_qty', __( 'petro::lang.qty' ) ) !!}
                {!! Form::text('other_sale_qty', null, ['class' => 'form-control other_sale_fields check_pumper qty input_number',
                'required',
                'placeholder' => __(
                'petro::lang.qty' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('other_sale_discount_type', __( 'petro::lang.discount_type' ) ) !!}
                {!! Form::select('other_sale_discount_type', ['fixed' => 'Fixed', 'percentage' => 'Percentage'], null, ['class' => 'form-control other_sale_fields check_pumper
                input_number
                other_sale_discount_type', 'required',
                'placeholder' => __(
                'petro::lang.please_select' ) ]); !!}
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                {!! Form::label('other_sale_discount', __( 'petro::lang.discount' ) ) !!}
                {!! Form::text('other_sale_discount', null, ['class' => 'form-control other_sale_fields check_pumper input_number
                other_sale_discount', 'required',
                'placeholder' => __(
                'petro::lang.discount' ) ]); !!}
            </div>
        </div>
        <div class="col-md-1">
            <button type="submit" class="btn btn-primary btn_other_sale"
                style="margin-top: 23px;">@lang('messages.add')</button>
        </div>
    </div>
</div>
<br>
<br>
<div class="row">
    <div class="col-md-12">
        <table class="table table-bordered table-striped" id="other_sale_table">
            <thead>
                <tr>
                    <th>@lang('petro::lang.code' )</th>
                    <th>@lang('petro::lang.products' )</th>
                    <th>@lang('petro::lang.balance_stock' )</th>
                    <th>@lang('petro::lang.price')</th>
                    <th>@lang('petro::lang.qty' )</th>
                    <th>@lang('petro::lang.discount_type' )</th>
                    <th>@lang('petro::lang.discount_value' )</th>
                    <th>@lang('petro::lang.before_discount' )</th>
                    <th>@lang('petro::lang.after_discount' )</th>
                    <th>@lang('petro::lang.action' )</th>
                </tr>
            </thead>

            <tbody>
                <!-- @php
                    $other_sale_final_total = 0.00;
                @endphp
                @if (!empty($active_settlement))
                    @foreach ($active_settlement->other_sales as $ot_item)
                        @php
                            $product = App\Product::where('id', $ot_item->product_id)->first();
                            $discount_amount = $ot_item->discount_amount;
                            $withDiscount = $ot_item->sub_total - $discount_amount;
                            $other_sale_final_total += $withDiscount;
                        @endphp
                      
                        <tr>
                            <td>{{!empty($product) ? $product->sku : ''}}</td>
                            <td>{{!empty($product) ? $product->name : ''}}</td>
                            <td>{{number_format($ot_item->balance_stock,4,'.',',')}}</td>
                            <td>{{number_format($ot_item->price, $currency_precision)}}</td>
                            <td>{{number_format($ot_item->qty,4,'.',',')}}</td>
                            <td>{{$ot_item->discount_type}}</td>
                            <td>{{number_format($ot_item->discount, $currency_precision)}}</td>
                            <td>{{number_format($ot_item->sub_total, $currency_precision)}}</td>
                            <td>{{number_format($withDiscount, $currency_precision)}}</td>
                            <td><button class="btn btn-xs btn-danger delete_other_sale"
                                    data-href="/petro/settlement/delete-other-sale/{{$ot_item->id}}"><i class="fa fa-times"></i>
                            </td>
                        </tr>
                    @endforeach 
                    @endif -->

                    @php $other_sale_final_total = 0.00; @endphp
                    @foreach ($combinedOtherSales as $item)
                    <tr>
                        <td>{{ $item['sku'] }}</td>
                        <td>{{ $item['name'] }}</td>
                        <td class="text-right">
                            {{ number_format((float) ($item['balance_stock'] ?? 0), 4, '.', ',') }}
                        </td>
                        <td class="text-right">{{ $item['price'] }}</td>
                        <td class="text-right">{{ $item['qty'] }}</td>
                        <td>{{ $item['discount_type'] }}</td>
                        <td class="text-right">{{ $item['discount'] }}</td>
                        <td class="text-right">{{ $item['sub_total'] }}</td>
                        <td class="text-right">{{ $item['with_discount'] }}</td>
                        <td>
                            @if ($item['user_check'] == 1)
                                <button class="btn btn-xs btn-danger delete_other_sale" data-href="/petro/settlement/delete-other-sale/{{ $item['id'] ?? '' }}">
                                    <i class="fa fa-times"></i>
                                </button>
                            @else
                                <!-- Optional: Add any action or leave blank -->
                            @endif
                        </td>
                    </tr>
                    @endforeach


            </tbody>

            <tfoot>
                <tr>
                    <td colspan="7" style="text-align: right; font-weight: bold;">@lang('petro::lang.other_sale_total')
                        :</td>
                    <td style="text-align: left; font-weight: bold;" class="other_sale_total">
                        {{number_format( $pump_other_sale_final_total, $currency_precision)}}</td>

                </tr>
                <input type="hidden" value="{{$pump_other_sale_final_total}}" name="other_sale_total" id="other_sale_total">
            </tfoot>
        </table>
    </div>
</div>

<input type="hidden" value="0" id="shift_operator_other_sale_total">

<div class="table-responsive 1234" id="outside_other_sale_table"  style="display: none;">
    <table class="table table-bordered table-striped" id="pump_operator_other_sale_table"
        style="width: 100%;">
        <thead>
            <tr>
                <th>@lang('petro::lang.code' )</th>
                <th>@lang('petro::lang.products' )</th>
                <th>@lang('petro::lang.balance_stock' )</th>
                <th>@lang('petro::lang.price')</th>
                <th>@lang('petro::lang.qty' )</th>
                <th>@lang('petro::lang.discount_type' )</th>
                <th>@lang('petro::lang.discount_value' )</th>
                <th>@lang('petro::lang.before_discount' )</th>
                <th>@lang('petro::lang.after_discount' )</th>
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