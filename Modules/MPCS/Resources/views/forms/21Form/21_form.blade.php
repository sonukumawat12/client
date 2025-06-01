<!-- Main content -->
<section class="content">
    @php
        $formNumber = Modules\MPCS\Entities\MpcsFormSetting::where(
            'business_id',
            request()->session()->get('business.id'),
        )->value('F20_form_sn');
    @endphp

    {!! Form::open(['id' => 'f21_form']) !!}
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('form_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text(
                            'form_21_date_range',
                            @format_date('first day of this month') .
                                ' ~ ' .
                                @format_date('last
                                                                                                                                                                    day of this month'),
                            [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control',
                                'id' => 'form_21_date_range',
                                'readonly',
                            ],
                        ) !!}
                    </div>
                </div>

                <div class="col-md-3" id="location_filter">
                    <div class="form-group">
                        {!! Form::label('form_21_location_id', __('purchase.business_location') . ':') !!}
                        {!! Form::select('form_21_location_id', $business_locations, null, [
                            'class' => 'form-control select2',
                            'id' => 'form_21_location_id',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-3" id="location_filter">
                    <div class="form-group">
                        {!! Form::label('form_21_product_id', __('mpcs::lang.product') . ':') !!}
                        {!! Form::select('form_21_product_id', $products, null, [
                            'class' => 'form-control select2',
                            'id' => 'form_21_product_id',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>

                <div class="col-md-3" id="location_filter">
                    <div class="form-group">
                        {!! Form::label('f21_transaction_type', __('mpcs::lang.transaction_type') . ':') !!}
                        {!! Form::select('f21_transaction_type', $transactionTypes, null, [
                            'class' => 'form-control select2',
                            'id' => 'f21_transaction_type',
                            'style' => 'width:100%',
                            'placeholder' => __('lang_v1.all'),
                        ]) !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    <div class="row" style="margin-top: 20px;">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <div class="col-md-12">
                    <div class="row">
                        <div class="col-md-4 text-red" style="margin-top: 14px;">

                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <h5 style="font-weight: bold;">{{ request()->session()->get('business.name') }} <br>
                                    <span class="f16a_location_name">@lang('petro::lang.all')</span>
                                </h5>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-right">
                                <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.f21')</h5>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="text-right">
                                <button type="submit" name="submit_type" id="f21_print" value="print"
                                    class="btn btn-primary pull-right">@lang('mpcs::lang.print')</button>
                            </div>
                        </div>
                    </div><br>

                    <div class="row">
                        <div class="col-md-3 text-red">
                            <h5 style="font-weight: bold;" class="text-center">@lang('mpcs::lang.filling_station'): _________________</h5>
                            <input type="hidden" name="manager_name" value="" />
                        </div>
                        <div class="col-md-3">
                            <div class="text-center">
                                <div style="">
                                    <h5 style="font-weight: bold;">@lang('mpcs::lang.date_range_from') : _____________</h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-center">
                                <div style="">
                                    <h5 style="font-weight: bold;" class="text-red">@lang('mpcs::lang.to'): _________________
                                    </h5>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="text-center">
                                <h5 style="font-weight: bold;" id="fn" class="text-red">Form Number: <span
                                        id="form_no">{{ $formNumber ?? 1 }} </span></h5>
                                <input type="hidden" id="formnumber" value="{{ $formNumber ?? 1 }}">

                            </div>
                        </div>
                    </div><br>

                    <div class="row" style="margin-top: 20px;">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="form_f21_list_table" style="width:100%">
                                <thead>
                                    <tr>
                                        <th>@lang('mpcs::lang.date')</th>
                                        <th>@lang('mpcs::lang.bill_no')</th>
                                        <th>@lang('mpcs::lang.book_no')</th>
                                        <th>@lang('mpcs::lang.transaction_type')</th>
                                        <th>@lang('mpcs::lang.product_code')</th>
                                        <th>@lang('mpcs::lang.product_name')</th>
                                        <th>@lang('mpcs::lang.received_qty')</th>
                                        <th>@lang('mpcs::lang.sold_qty')</th>
                                        <th>@lang('mpcs::lang.balance_qty')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {!! Form::close() !!}
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->
