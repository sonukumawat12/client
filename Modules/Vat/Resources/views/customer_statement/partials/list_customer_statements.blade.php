<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3">
                {!! Form::label('list_customer_statement_customer_id', __('contact.customer'). ':', []) !!}
                {!! Form::select('list_customer_statement_customer_id', $customers, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('list_customer_statement_date_range', __('report.date_range') . '(statement date):') !!}
                    {!! Form::text('list_customer_statement_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'list_customer_statement_date_range', 'readonly']); !!}
                </div>
            </div>
            

            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped" id="customer_statement_list_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.action')</th>
                                <th>@lang('contact.date_printed')</th>
                                <th>@lang('contact.date_from')</th>
                                <th>@lang('contact.date_to')</th>
                                <th>@lang('contact.customer')</th>
                                <th>@lang('contact.statement_no')</th>
                                <th>@lang('contact.statement_amount')</th>
                                <th>@lang('contact.payment_status')</th>
                                <th>@lang('contact.added_by')</th>
                                <th>@lang('contact.description')</th>

                            </tr>
                        </thead>
                        <tfoot>
                            <tr>
                                <th colspan="6"></th>
                                <th><span id="grand_total">0.00</span></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
        @endcomponent
    </div>
</section>