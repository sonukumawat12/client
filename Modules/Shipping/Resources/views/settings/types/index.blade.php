<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('types_date_range_filter', __('report.date_range') . ':') !!}
                        {!! Form::text(
                            'types_date_range_filter',
                            @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'),
                            [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control date_range',
                                'id' => 'types_date_range_filter',
                                'readonly',
                            ],
                        ) !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('shipping_types', __('shipping::lang.shipping_types')) !!}
                        {!! Form::select('shipping_types', $shipping_types, null, [
                            'class' => 'form-control select2',
                            'required',
                            'placeholder' => __('shipping::lang.please_select'),
                            'id' => 'shipping_types',
                        ]) !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.all_your_types')])
        @slot('tool')
            <div class="box-tools ">
                <button type="button" class="btn  btn-primary btn-modal pull-right"
                    data-href="{{ action('\Modules\Shipping\Http\Controllers\TypeController@create') }}"
                    data-container=".view_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')</button>

            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="types_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="notexport">@lang('messages.action')</th>
                        <th>@lang('shipping::lang.added_date')</th>
                        <th>@lang('shipping::lang.shipping_types')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
<!-- /.content -->
