<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('edit_settlement_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('edit_settlement_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'edit_settlement_date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('edit_settlement_id', __('petro::lang.settlment_nos') . ':') !!}
                        {!! Form::select('edit_settlement_id', $settlements, null, ['class' => 'form-control
                        select2 daily_report_change',
                        'placeholder' => __('petro::lang.all'), 'id' => 'edit_settlement_id', 'style' =>
                        'width:100%']); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __(
    'superadmin::lang.edit_settlement_date')])
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="edit_settlement_date_table" style="width:100%;">
            <thead>
                <tr>
                    <th>@lang('petro::lang.transaction_date')</th>
                    <th>@lang('petro::lang.settlement_no')</th>
                    <th>@lang('petro::lang.tank')</th>
                    <th>@lang('petro::lang.product')</th>
                    <th>@lang('lang_v1.created_at')</th>
                    <th>@lang('lang_v1.action')</th>

                </tr>
            </thead>
        </table>
    </div>
    @endcomponent
</section>
<!-- /.content -->

