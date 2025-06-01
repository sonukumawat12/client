
<!-- Main content -->

<section class="content">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
         
             <div class="col-md-3" id="location_filter">
                <div class="form-group">
                    {!! Form::label('f21c_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('f21c_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('form_21c_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'form_21c_date_range', 'readonly']); !!}
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('mpcs::lang.F21c_from_no') . ':') !!}
                    {!! Form::text('F21c_from_no', $F21c_from_no, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>
         
           
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
            <div class="box-tools">
                <!-- Standard Print button -->
                <button class="btn btn-primary print_report pull-right" onclick="printDiv()">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
            </div>
            @endslot
            <div class="col-md-12" id="print_content">
                <style>
                    @media print {
                        .col-print-1 {
                            width: 8%;
                            float: left;
                        }

                        .col-print-2 {
                            width: 16%;
                            float: left;
                        }

                        .col-print-3 {
                            width: 25%;
                            float: left;
                        }

                        .col-print-4 {
                            width: 33%;
                            float: left;
                        }

                        .col-print-5 {
                            width: 42%;
                            float: left;
                        }

                        .col-print-6 {
                            width: 50%;
                            float: left;
                        }

                        .col-print-7 {
                            width: 58%;
                            float: left;
                        }

                        .col-print-8 {
                            width: 66%;
                            float: left;
                        }

                        .col-print-9 {
                            width: 75%;
                            float: left;
                        }

                        .col-print-10 {
                            width: 83%;
                            float: left;
                        }

                        .col-print-11 {
                            width: 92%;
                            float: left;
                        }

                        .col-print-12 {
                            width: 100%;
                            float: left;
                        }

                    }
                </style>
                <div class="row">
                    <div class="col-md-4 text-red" style="margin-top: 14px;">
                        <b>@lang('petro::lang.date_range'): <span class="21c_from_date"></span> @lang('petro::lang.to') <span class="21c_to_date"></span> </b>
                    </div>
                    <div class="col-md-5">
                        <div class="text-center">
                            <h5 style="font-weight: bold;">{{request()->session()->get('business.name')}} <br>
                                <span class="f21c_location_name">@lang('petro::lang.all')</span></h5>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="text-center pull-left">
                            <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.21c_form') @lang('mpcs::lang.form_no') : {{$F21c_from_no}}</h5>
                        </div>
                    </div>
                </div>
                <div class="row" style="margin-top: 20px;">
                    <div class="table-responsive">
                        <table class="table table-responsive table-bordered 21c_table" style="width: 100%;">
                  
                            <thead>
                                <tr>
                                    <th class="text-center" rowspan="2">@lang('mpcs::lang.description')</th>
                                    <th class="text-center" rowspan="2">@lang('mpcs::lang.no')</th>
                                    @foreach ($merged_sub_categories as $merged)
                                    <!--<th class="text-center" colspan="2">{{$merged->merged_sub_category_name}}</th>-->
                                    @endforeach
                                    <th class="text-center">@lang('mpcs::lang.total')</th>
                                </tr>
                                <tr>
                                    @foreach ($merged_sub_categories as $merged)
                                    <!--<th class="text-center">@lang('mpcs::lang.balance_qty')</th>
                                    <th class="text-center">@lang('mpcs::lang.value')</th>-->
                                    @endforeach
                                    <th class="text-center">@lang('mpcs::lang.value')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <!-- Brown background color -->
                                    <td colspan="11" class="text-warning bg-warning bg-gradient"><b>@lang('mpcs::lang.receipts')</b></td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang._today')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_today" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang._previous_day')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_previous_day" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang._total_receipts')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_total_receipts" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang._opening_stock')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_opening_stock" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang._price_increment_today')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_price_increment_today" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang._price_increment_pre_date')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_price_increment_pre_date" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang._price_increment_total')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_price_increment_total" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang._total_receipt_to_date')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_total_receipt_to_date" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                    <tr>
                                    <!-- Brown background color -->
                                    <td colspan="11" class="text-warning bg-warning bg-gradient"><b>@lang('mpcs::lang._issues_section')</b></td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang._cash_sales_today')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_cash_sales_today" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                    <tr>
                                    <td><b>@lang('mpcs::lang._credit_sales_today')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_credit_sales_today" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <tr>
                                    <td><b>@lang('mpcs::lang._own_usage_sales_today')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_own_usage_sales_today" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <tr>
                                    <td><b>@lang('mpcs::lang._price_reduction_today')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_price_reduction_today" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <tr>
                                    <td><b>@lang('mpcs::lang._price_reduction_predate')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_price_reduction_predate" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <tr>
                                    <td><b>@lang('mpcs::lang._price_reduction_total')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_price_reduction_total" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <tr>
                                    <tr>
                                    <td><b>@lang('mpcs::lang._total_issued_today')</b></td>
                                    <td></td>
                                    <td>
                                        <input type="text" name="" id="_total_issued_today" class="form-control total_today_receipt" style="width: 100%;">
                                    </td>
                                <tr>
                                <!--<tr>
                                    <td><b>@lang('mpcs::lang.today')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                        
                                    <td>
                                        <input type="text" name="" id="today_receipt_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="today_receipt_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control total_today_receipt" style="width: 80%;">
                                    </td>
                                <tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.previous_day')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="total_previous_1" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="total_previous_2" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name=""  class="form-control total_today_receipt" style="width: 80%;">
                                    </td>
                                <tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.total_receipts')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="total_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="total_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control total_today_receipt" style="width: 80%;">
                                    </td>
                                <tr>
                                    <td><b>@lang('mpcs::lang.opening_stock')</b></td>
                                    <td></td>
                                    @foreach ($sub_categories as $merged)

                                    <td>
                                        <input type="text" name="" id="open_stock_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name=""  id="open_stock_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control total_today_receipt" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.price_increment_today')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="price_increment_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="price_increment_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.price_increment_pre_date')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="price_increment_pre_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="price_increment_pre_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.price_increment_total')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="price_increment_total_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="price_increment_total_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.total_receipts_to_date')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="total_receipt_to_date_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="total_receipt_to_date_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="11" class="text-red"><b>@lang('mpcs::lang.issues')</b></td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.cash_today')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="cash_today_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="cash_today_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.credit_today')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="credit_today_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="credit_today_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.own_usage_today')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="own_today_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="own_today_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.total_today')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="total_today_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="total_today_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.previous_day')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                   
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" id="total_previous_value"  class="form-control" style="width: 100%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.total_1')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="total_1_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="total_1_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.price_reduction_today')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="price_reduction_today_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="price_reduction_today_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.price_reduction_pre_day')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="price_reduction_pre_day_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="price_reduction_pre_day_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.total_2')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="total_2_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="total_2_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.total_as_of_today')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="total_as_of_today" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="total_as_of_today_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.final_balance')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="final_balance_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="final_balance_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>
                                <tr>
                                    <td><b>@lang('mpcs::lang.total_receipts_todate')</b></td>
                                    <td></td>
                                    @foreach ($merged_sub_categories as $merged)
                                    <td>
                                        <input type="text" name="" id="total_receipts_todate_qty" class="form-control" style="width: 80%;">
                                    </td>
                                    <td>
                                        <input type="text" name="" id="total_receipts_todate_value" class="form-control" style="width: 80%;">
                                    </td>
                                    @endforeach
                                    <td>
                                        <input type="text" name="" class="form-control" style="width: 80%;">
                                    </td>
                                </tr>-->
                            </tbody>
                        </table>
                    </div>
                    <div class="row">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('finalize', 1, false, ['class' => 'input-icheck', 'id' => 'finalize']); !!}
                                @lang('mpcs::lang.f21c_acknowledge')
                            </label>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-6">
                            <p>@lang('mpcs::lang.checked_by')____________</p>  <br>
                            <p>@lang('mpcs::lang.date')____________</p> 
                        </div>
                        <div class="col-md-6 text-right">
                            <p>@lang('mpcs::lang.manage_signature')____________</p>  <br>
                            <p>@lang('mpcs::lang.date')____________</p> 
                        </div>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->

<script>
     function printDiv() {
		var w = window.open('', '_self');
		var html ='<html><body class="col-print-12">'  +document.getElementById("print_content").innerHTML + '</body></html>'  ;
		$(w.document.body).html(html);
		w.print();
		w.close();
		window.location.href = "{{URL::to('/')}}/mpcs/form-set-1";
	}
</script>