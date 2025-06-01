<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>F22-{{$header->form_no}} {{request()->session()->get('business.name')}} {{$header->location_name}}</title>
    {{-- @include('layouts.partials.css') --}}
    <style>
        table {
            border-collapse: collapse;
        }

        table tbody td {
            border: 1px solid black;
        }

        table thead th {
            border: 1px solid black;
        }

        tfoot {
            page-break-after: always !important;
        }

    </style>
</head>

<body>
    <div class="col-md-12" style="text-align:center;">
        <div class="row">
            <div class="col-md-5">
                <div class="text-center">
                    <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>
                        <span class="f22_location_name">{{$header->location_name}}</span></h5>
                </div>
            </div>
            <div class="col-md-3">
                <div class="text-center pull-left">
                    <h5 style="font-weight: bold;" class="text-red">@lang('Stocktaking::lang.f22_form')
                        @lang('Stocktaking::lang.form_no') : {{$header->form_no}}</h5>
                </div>
            </div>
        </div>
        @php
        $index = 1;
        $chunk_number = !empty($settings->F22_no_of_product_per_page) ? $settings->F22_no_of_product_per_page : 25;
        $chuncks = $details->chunk($chunk_number);
        $pre_page_total_purchase = 0;
        $pre_page_total_sale = 0;
        $grand_page_total_purchase = 0;
        $grand_page_total_sale = 0;
        @endphp
        @foreach ($chuncks as $key => $detail)
        <div class="row" style="margin-top: 20px; page-break-after: always;">
                <table class="table table-bordered table-striped" style="width: 100%;" id="form_22_print_table">
                    <thead>
                        <tr>
                            <th>@lang('Stocktaking::lang.index_no')</th>
                            <th>@lang('Stocktaking::lang.code')</th>
                            <th>@lang('Stocktaking::lang.book_no')</th>
                            <th>@lang('Stocktaking::lang.product')</th>
                            <th>@lang('Stocktaking::lang.current_stock')</th>
                            <th>@lang('Stocktaking::lang.stock_count')</th>
                            <th>@lang('Stocktaking::lang.unit_purchase_price')</th>
                            <th>@lang('Stocktaking::lang.total_purchase_price')</th>
                            <th>@lang('Stocktaking::lang.unit_sale_price')</th>
                            <th>@lang('Stocktaking::lang.total_sale_price')</th>
                            <th>@lang('Stocktaking::lang.qty_difference')</th>
                            <th>@lang('Stocktaking::lang.difference_value')</th>

                        </tr>
                    </thead>
                    @php
                    $this_page_total_purchase = 0.00;
                    $this_page_total_sale = 0.00;
                    @endphp
                    <tbody>
                        @foreach ($detail as $item)
                        <tr>
                            <td>{{$index}}</td>
                            <td>{{!empty($item->product_code) ? $item->product_code : ''}}</td>
                            <td>{{!empty($item->book_no) ? $item->book_no : ''}}</td>
                            <td>{{!empty($item->product) ? $item->product : ''}}</td>
                            <td>{{!empty($item->current_stock) ? @number_format($item->current_stock) : ''}}</td>
                            <td>{{!empty($item->stock_count) ? @number_format($item->stock_count) : ''}}</td>
                            <td>{{!empty($item->unit_purchase_price) ? @number_format($item->unit_purchase_price) : ''}}
                            </td>
                            <td><span class="display_currency total_purchase_price"
                                    data-orig-value="{{!empty($item->purchase_price_total) ? @number_format($item->purchase_price_total) : ''}}"
                                    data-currency_symbol="false">{{!empty($item->purchase_price_total) ? @number_format($item->purchase_price_total) : ''}}</span>
                            </td>
                            <td>{{!empty($item->unit_sale_price) ? @number_format($item->unit_sale_price) : ''}}</td>
                            <td><span class="display_currency total_sale_price"
                                    data-orig-value="{{!empty($item->sales_price_total) ? @number_format($item->sales_price_total) : ''}}"
                                    data-currency_symbol="false">{{!empty($item->sales_price_total) ? @number_format($item->sales_price_total) : ''}}</span>
                            </td>
                            <td>{{!empty($item->difference_qty) ? @number_format($item->difference_qty) : ''}}</td>
                            <td>{{!empty($item->difference_value) ? @number_format($item->difference_value) : ''}}</td>
                        </tr>

                        @php
                        $index++;

                        $this_page_total_purchase += $item->purchase_price_total;
                        $this_page_total_sale += $item->sales_price_total;
                        $grand_page_total_purchase += $item->purchase_price_total;
                        $grand_page_total_sale += $item->sales_price_total;
                        if($key == 0){
                        $pre_page_total_purchase = 0;
                        $pre_page_total_sale = 0;
                        }
                        @endphp
                        @endforeach

                    </tbody>
                    <tfoot class="bg-gray">
                        <tr>
                            <td class="text-red text-bold" colspan="7">@lang('Stocktaking::lang.total_this_page')</td>
                            <td class="text-red text-bold" id="footer_total_purchase_price">
                                {{@number_format($this_page_total_purchase)}}</td>
                            <td>&nbsp;</td>
                            <td class="text-red text-bold" colspan="3" id="footer_total_sale_price">
                                {{@number_format($this_page_total_sale)}}</td>
                        </tr>
                        <tr>
                            <td class="text-red text-bold" colspan="7">@lang('Stocktaking::lang.total_previous_page')
                            </td>
                            <td class="text-red text-bold" id="pre_total_purchase_price">{{ @number_format($pre_page_total_purchase)}}
                            </td>
                            <td>&nbsp;</td>
                            <td class="text-red text-bold" colspan="3" id="pre_total_sale_price">
                                {{ @number_format($pre_page_total_sale) }}</td>
                        </tr>
                        <tr>
                            <td class="text-red text-bold" colspan="7">@lang('Stocktaking::lang.grand_total')</td>
                            <td class="text-red text-bold" id="grand_total_purchase_price">
                                {{@number_format($grand_page_total_purchase)}}</td>
                            <td>&nbsp;</td>
                            <td class="text-red text-bold" colspan="3" id="grand_total_sale_price">
                                {{@number_format($grand_page_total_sale)}}</td>
                        </tr>
                        <tr>
                            <td colspan="12"> @lang('Stocktaking::lang.confirm_f22')</td>
                        </tr>
                        <tr>
                            <td colspan="7"  class="text-left" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                    @lang('Stocktaking::lang.checked_by'): ____________</h5>
                            </td>
                            <td colspan="5" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                    @lang('Stocktaking::lang.received_by'): ____________</h5> <br>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7" class="text-left" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                    @lang('Stocktaking::lang.signature_of_manager'): ____________</h5>
                            </td>
                            <td colspan="5" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                    @lang('Stocktaking::lang.handed_over_by'): ____________</h5>
                            </td>
                        </tr>
                        <tr>
                            <td colspan="7"  class="text-left" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-top: 10px; ">@lang('Stocktaking::lang.user'):
                                    {{auth()->user()->username }}</h5>
                            </td>
                            <td colspan="5" style="border: 0px !important">
                                <h5 style="font-weight: bold; margin-bottom: 0px; ">
                                   </h5>
                            </td>
                        </tr>
                    </tfoot>
                </table>
           
        </div>
        @php
            $pre_page_total_purchase = $grand_page_total_purchase;
            $pre_page_total_sale = $grand_page_total_sale;
        @endphp
        @endforeach
    </div>

    {{-- @include('layouts.partials.javascripts') --}}

</body>

</html>