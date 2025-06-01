<!-- Main content -->
<section class="content main-content-inner" style="margin-top:20px;">
    @if(!empty($message)) {!! $message !!} @endif
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('location_id',  __('purchase.business_location') . ':') !!}
                        {!! Form::select('location_id', $business_locations, null, ['class' => 'form-control select2', 'placeholder' => __('ezyinvoice::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
               
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    <div class="box-tools pull-right ">
            <a class="btn  btn-primary" href="{{action('\Modules\EzyInvoice\Http\Controllers\EzyInvoiceController@create')}}">
                <i class="fa fa-plus"></i> @lang('messages.add')</a>
    </div>
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_settlement" style="width:100%">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('ezyinvoice::lang.status')</th>
                    <th>@lang('ezyinvoice::lang.settlement_date')</th>
                    <th>@lang('ezyinvoice::lang.invoice_no')</th>
                    <th>@lang('ezyinvoice::lang.location')</th>
                    <th>@lang('ezyinvoice::lang.total_amnt')</th>
                    <th>@lang('ezyinvoice::lang.added_user')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade settlement_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div id="settlement_print" class="container"></div>
</section>
<!-- /.content -->
