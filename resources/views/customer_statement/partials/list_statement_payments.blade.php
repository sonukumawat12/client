<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
           
            <div class="col-md-3">
                {!! Form::label('list_statement_payment_customer_id', __('contact.customer'). ':', []) !!}
                {!! Form::select('list_statement_payment_customer_id', $customers, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
            </div>

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('list_statement_payment_date_range', __('report.date_range') . '(statement date):') !!}
                    {!! Form::text('list_statement_payment_date_range', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'list_statement_payment_date_range', 'readonly']); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                {!! Form::label('list_statement_payment_method', __('lang_v1.payment_method'). ':', []) !!}
                {!! Form::select('list_statement_payment_method', $payment_methods, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
            </div>
            
            <div class="col-md-3">
                {!! Form::label('list_statement_payment_statement_no', __('lang_v1.statement_no'). ':', []) !!}
                {!! Form::select('list_statement_payment_statement_no', $statement_nos, null, ['class' => 'form-control select2',
                'placeholder' => __('lang_v1.all'), 'style' => 'width: 100%;']) !!}
            </div>


            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            <div class="row" style="margin-top: 20px;">
                <div class="col-md-12">
                    <table class="table table-bordered table-striped" id="list_statement_payment_table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>@lang('lang_v1.transaction_date')</th>
                                <th>@lang('contact.customer')</th>
                                <th>@lang('contact.system_entered_date')</th>
                                <th>@lang('contact.statement_no')</th>
                                <th>@lang('contact.statement_amount')</th>
                                <th>@lang('contact.paid_amount')</th>
                                <th>@lang('lang_v1.payment_method')</th>
                                <th>@lang('contact.added_by')</th>

                            </tr>
                        </thead>
                       
                    </table>
                </div>
            </div>
        </div>
        @endcomponent
    </div>
</section>