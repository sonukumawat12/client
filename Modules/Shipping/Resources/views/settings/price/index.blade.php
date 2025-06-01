<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('price_date_range_filter', __('report.date_range') . ':') !!}
                        {!! Form::text(
                            'price_date_range_filter',
                            @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'),
                            [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control date_range',
                                'id' => 'price_date_range_filter',
                                'readonly',
                            ],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('shipping_mode_price', __('shipping::lang.shipping_mode')) !!}
                        {!! Form::select('shipping_mode_price', $shipping_mode, null, [
                            'class' => 'form-control select2',
                            'required',
                            'placeholder' => __('shipping::lang.please_select'),
                            'id' => 'shipping_mode_price',
                        ]) !!}
                    </div>
                </div>
                @php
                    $shipping_partners = DB::table('shipping_partners')->get();
                    $shipping_partner_options = [];

                    foreach ($shipping_partners as $partner) {
                        $shipping_partner_options[$partner->id] = $partner->name;
                    }
                @endphp
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('shipping_mode_partner', __('shipping::lang.shipping_partner')) !!}
                        {!! Form::select('shipping_mode_partner', $shipping_partner_options, null, [
                            'class' => 'form-control select2',
                            'placeholder' => __('shipping::lang.please_select'),
                            'id' => 'shipping_mode_partner',
                        ]) !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.price')])
        @slot('tool')
            <div class="box-tools ">
                <button type="button" class="btn  btn-primary btn-modal pull-right"
                    data-href="{{ action('\Modules\Shipping\Http\Controllers\PriceController@create') }}"
                    data-container=".view_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')</button>

            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="price_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="notexport">@lang('messages.action')</th>
                        <th>@lang('shipping::lang.added_date')</th>
                        <th>@lang('shipping::lang.package')</th>
                        <th>@lang('shipping::lang.per_kg')</th>
                        <th>@lang('shipping::lang.fixed_price')</th>
                        <th>@lang('shipping::lang.constant_value')</th>
                        <th>@lang('shipping::lang.shipping_partner')</th>
                        <th>@lang('shipping::lang.shipping_mode')</th>
                        <th>@lang('shipping::lang.status')</th>
                        <th>@lang('shipping::lang.created_by')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
<!-- /.content -->
