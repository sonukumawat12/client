<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('dimension_date_range_filter', __('report.date_range') . ':') !!}
                        {!! Form::text(
                            'dimension_date_range_filter',
                            @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month'),
                            [
                                'placeholder' => __('lang_v1.select_a_date_range'),
                                'class' => 'form-control date_range',
                                'id' => 'dimension_date_range_filter',
                                'readonly',
                            ],
                        ) !!}
                    </div>
                </div>
                
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('shipping::lang.dimensions')])
        @slot('tool')
            <div class="box-tools ">
                <button type="button" class="btn  btn-primary btn-modal pull-right"
                    data-href="{{ action('\Modules\Shipping\Http\Controllers\DimensionController@create') }}"
                    data-container=".view_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')</button>

            </div>
        @endslot
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="dimension_table" style="width: 100%;">
                <thead>
                    <tr>
                        <th class="notexport">@lang('messages.action')</th>
                        <th>@lang('shipping::lang.added_date')</th>
                        <th>@lang('shipping::lang.dimension_no')</th>
                        <th>@lang('shipping::lang.weight')</th>
                        <th>@lang('shipping::lang.length')</th>
                        <th>@lang('shipping::lang.width')</th>
                        <th>@lang('shipping::lang.height')</th>
                        <th>@lang('shipping::lang.status')</th>
                        <th>@lang('shipping::lang.created_by')</th>
                    </tr>
                </thead>
            </table>
        </div>
    @endcomponent
</section>
<!-- /.content -->
