<div class="col-md-12">
  
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('income_date_range', __('report.date_range') . ':') !!}
            {!! Form::text('income_date_range', null, ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
            'form-control', 'readonly']); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('income_route_id', __('distribution::lang.route') . ':') !!}
            {!! Form::select('income_route_id', $routes, null, ['class' => 'form-control select2', 'style' => 'width:100%',
            'placeholder' => __('lang_v1.all')]); !!}
        </div>
    </div>
    <div class="col-md-3">
        <div class="form-group">
            {!! Form::label('income_payment_type', __('distribution::lang.payment_type') . ':') !!}
            {!! Form::select('income_payment_type', ['debit' => __('distribution::lang.debit'), 'credit' =>
            __('distribution::lang.credit')], null, ['class' => 'form-control select2', 'style' => 'width:100%', 'placeholder'
            => __('lang_v1.all')]); !!}
        </div>
    </div>
   
</div>
<div class="clearfix"></div>
<br>
<br>
<div class="col-md-12">
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="income_table" style="width: 100%">
            <thead>
                <tr>
                    <th>@lang('distribution::lang.date')</th>
                    <th>@lang('distribution::lang.route_operation_no')</th>
                    <th>@lang('distribution::lang.route')</th>
                    <th>@lang('distribution::lang.invoice_no')</th>
                    <th>@lang('distribution::lang.debit')</th>
                    <th>@lang('distribution::lang.credit')</th>
                    <th>@lang('distribution::lang.balance')</th>

                </tr>
            </thead>
        </table>
    </div>
   
</div>