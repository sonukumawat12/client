<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_payment_date_range', __('lang_v1.date_range').':') !!}
                        {!! Form::text('customer_payment_date_range', null, ['class' => 'form-control ','id'=>'customer_payment_date_range', 'style' => 'width: 100%;']); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                    <!--{!! Form::label('customer_payment_customer_id', __('lang_v1.customer').':') !!}-->
                    <!--{!! Form::select('customer_payment_customer_id', $customers, null, ['class' => 'form-control-->
                    <!--select2', 'style' => 'width: 100%;', 'required', 'placeholder' => __('lang_v1.all')]); !!}-->
                    {!! Form::label('customer_payment_customer_id', __('petro::lang.customer').':') !!}
                    {!! Form::select('customer_payment_customer_id', $customers, null, ['class' => 'form-control
                    select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                    <!--{!! Form::label('customer_payment_bulk_customer_id', __('petro::lang.customer').':') !!}-->
                    <!--{!! Form::select('customer_payment_bulk_customer_id', $customers, null, ['class' => 'form-control-->
                    <!--select2', 'style' => 'width: 100%;', 'required', 'placeholder' => __('lang_v1.all')]); !!}-->
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_payment_location_id', __('lang_v1.location').'123:') !!}
                        {!! Form::select('customer_payment_location_id', $business_locations, null, ['class' => 'form-control
                        select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('customer_payment_method', __('lang_v1.payment_method').':') !!}
                        {!! Form::select('customer_payment_method', $payment_types, null, ['class' => 'form-control
                        select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('paid_in_type', __('lang_v1.paid_in_type').':') !!}
                        {!! Form::select('paid_in_type', ['customer_bulk' => "Customer Bulk Page",'customer_page' => 'Customer Page', 'all_sale_page' => 'All Sale Page', 'settlement' => 'Settlement'],null, ['class' => 'form-control select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}

                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('customer_amount', __('lang_v1.amount').':') !!}
                    {!! Form::select('customer_amount', [], null, ['class' => 'form-control
                    select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                    {!! Form::label('customer_cheque_no', __('lang_v1.cheque_no').':') !!}
                    {!! Form::select('customer_cheque_no', [], null, ['class' => 'form-control
                    select2', 'style' => 'width: 100%;', 'placeholder' => __('lang_v1.all')]); !!}
                    </div>
                </div>
            @endcomponent
        </div>
    </div>
    <br>
    <br>
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
                <table class="table table-bordered table-striped" id="customer_payments_table" style="width: 100%;">
                    <thead>
                    <tr>
                        <th class="notexport">@lang('messages.action')</th>
                        <th>@lang('lang_v1.date' )</th>
                        <th>@lang('lang_v1.system_date' )</th>
                        <th>@lang('lang_v1.location' )</th>
                        <th>@lang('lang_v1.payment_ref_no' )</th>
                        <th>@lang('lang_v1.customer' )</th>
                        <th>@lang('lang_v1.interest' )</th>
                        <th>@lang('lang_v1.amount' )</th>
                        <th>@lang('lang_v1.payment_method' )</th>
                        <th>@lang('lang_v1.paid_in_type' )</th>
                       <th>@lang('contact.user')</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray font-17 text-center footer-total">
                            <td colspan="5"></td>
                            <td><strong>@lang('sale.total'):</strong></td>
                            <td><span class="display_currency" id="footer_interest" data-currency_symbol="true"></span></td>
                            <td><span class="display_currency" id="footer_total" data-currency_symbol="true"></span></td>
                            <td colspan="3"></td>
                        </tr>
                    </tfoot>
                </table>
            @endcomponent
        </div>
    </div>
</section>
<!-- /.content -->
