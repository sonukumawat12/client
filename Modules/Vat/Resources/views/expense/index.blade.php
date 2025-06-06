@extends('layouts.app')
@section('title', __('expense.expenses'))

@section('content')

<!-- Content Header (Page header) -->
<section class="content-header" style="padding-top: 0px !important;padding-bottom: 0px !important;">
    <h1>@lang('expense.expenses')</h1>
</section>

<!-- Main content -->
<section class="content" style="padding-top:0px !important;">
    <div class="row">
         <div class="row">
        <div class="col-md-12">
            <div class="settlement_tabs">
                <ul class="nav nav-tabs">
                   <li class="">
                        <a href="{{action('\Modules\Vat\Http\Controllers\VatExpenseController@create')}}" >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.vat_expenses')</strong>
                        </a>
                    </li>
                  
                    <li class="active">
                        <a  href="{{action('\Modules\Vat\Http\Controllers\VatExpenseController@index')}}"  >
                            <i class="fa fa-file-text-o"></i> <strong>@lang('vat::lang.list_vat_expenses')</strong>
                        </a>
                    </li>

                </ul>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_category_id',__('expense.expense_category').':') !!}
                    {!! Form::select('expense_category_id', $categories, null, ['placeholder' =>
                    __('report.all'), 'class' => 'form-control select2', 'style' => 'width:100%', 'id' =>
                    'expense_category_id']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_date_range', __('report.date_range') . ':') !!}
                    {!! Form::text('date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control', 'id' => 'expense_date_range', 'readonly']); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('expense_payment_status', __('purchase.payment_status') . ':') !!}
                    {!! Form::select('expense_payment_status', ['paid' => __('lang_v1.paid'), 'due' =>
                    __('lang_v1.due'), 'partial' => __('lang_v1.partial')], null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget')
            @can('expense.create')
            @slot('tool')
            <div class="box-tools pull-right">
                <a class="btn btn-primary" href="{{action('\Modules\Vat\Http\Controllers\VatExpenseController@create')}}">
                    <i class="fa fa-plus"></i> @lang('messages.add')</a>
            </div>
            @endslot
            @endcan
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="vat_expense_table">
                    <thead>
                        <tr>
                            <th class="notexport">@lang('messages.action')</th>
                            <th>@lang('messages.date')</th>
                            <th>@lang('purchase.ref_no')</th>
                            <th>@lang('expense.expense_category')</th>
                            <th>@lang('sale.payment_status')</th>
                            <th>@lang('sale.total_amount')</th>
                            <th>@lang('expense.payment_method')
                            <th>@lang('lang_v1.added_by')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                            <td id="footer_payment_status_count"></td>
                            <td><span class="display_currency" id="footer_expense_total"
                                    data-currency_symbol="true"></span></td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->
<!-- /.content -->
<div class="modal fade payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>

<div class="modal fade edit_payment_modal" tabindex="-1" role="dialog" aria-labelledby="gridSystemModalLabel">
</div>
@stop
@section('javascript')
<script src="{{ asset('js/payment.js?v=' . $asset_v) }}"></script>
<script>
    var body = document.getElementsByTagName("body")[0];
    body.className += " sidebar-collapse";
</script>
@endsection