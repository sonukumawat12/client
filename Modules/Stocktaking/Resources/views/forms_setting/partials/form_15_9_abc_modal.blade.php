<div class="modal-dialog" role="document" style="width: 65%;">
    <div class="modal-content">
        <style>
            table.table-bordered>thead>tr>th {
                border: 1px solid #222 !important;
            }

            table.table-bordered>tbody>tr>td {
                border: 1px solid #222 !important;
                font-size: 13px;
            }
        </style>
        {!! Form::open(['url' => action('\Modules\Stocktaking\Http\Controllers\FormsSettingController@saveForm159ABCSetting'),
        'method' => 'post']) !!}

        <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                    aria-hidden="true">&times;</span></button>
            <h4 class="modal-title">@lang( 'Stocktaking::lang.15_9_abc_settings' )</h4>
        </div>

        <div class="modal-body">
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('F159ABC_form_tdate', __( 'lang_v1.transaction_date' ) . ':*') !!}
                    {!! Form::text('F159ABC_form_tdate', !empty($setting) ? $setting->F159ABC_form_tdate :null, ['class' => 'form-control', 'id' => 'F159ABC_setting_tdate',
                    'readonly', 'required', 'placeholder' => __( 'lang_v1.transaction_date' ) ]); !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    {!! Form::label('F159ABC_form_sn', __( 'Stocktaking::lang.form_starting_number' ) . ':*') !!}
                    {!! Form::text('F159ABC_form_sn', !empty($setting) ? $setting->F159ABC_form_sn :null, ['class' => 'form-control', 'id' => 'F159ABC_setting_sn',
                    'required', 'placeholder' => __( 'Stocktaking::lang.form_starting_number' ) ]); !!}
                </div>
            </div>
            <div class="clearfix"></div>
            <br>
            <div class="col-md-12 159abc_form_tables">
                <div class="col-md-6">
                    <div class="col-md-6">
                        <label for="">@lang('Stocktaking::lang.purchase_side')</label>
                        <table class="table table-responsive table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('Stocktaking::lang.description')</th>
                                    <th class="text-center">@lang('Stocktaking::lang.previous_day')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.opening_balance')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.purchase')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.from_own')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.exchange_return')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.price_increment')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.return')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <label for="">@lang('Stocktaking::lang.sale_side')</label>
                        <table class="table table-responsive table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('Stocktaking::lang.description')</th>
                                    <th class="text-center">@lang('Stocktaking::lang.previous_day')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.sales')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.others')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.price_reductions')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.sales_return')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.issue_on_society')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.credit_sales')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.final_stock')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="col-md-6">
                        <label for="">@lang('Stocktaking::lang.income_side')</label>
                        <table class="table table-responsive table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('Stocktaking::lang.description')</th>
                                    <th class="text-center">@lang('Stocktaking::lang.previous_day')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.cash_sales')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.cheques_sales')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.credit_card_sales')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.other_sales')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.exchange_return')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.purchase_increment')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.return')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <label for="">@lang('Stocktaking::lang.deposit_side')</label>
                        <table class="table table-responsive table-bordered" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th class="text-center">@lang('Stocktaking::lang.description')</th>
                                    <th class="text-center">@lang('Stocktaking::lang.previous_day')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.cash_deposited')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.cheques_deposited')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.credit_cards')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.others')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.cash_in_hand')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.purchase_increment')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        @lang('Stocktaking::lang.return')
                                    </td>
                                    <td>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="clearfix"></div>
            <br>
            <br>
            <br>
            <div class="col-md-12">
                <div class="col-md-4">
                    {!! Form::label('', __('Stocktaking::lang.total_previous_page_balance_zero'), []) !!}
                </div>
                <div class="col-md-8">
                    <div class="row">
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('F159ABC_first_day_after_stock_taking', 1, !empty($setting) ? $setting->F159ABC_first_day_after_stock_taking : false , ['class' =>
                                'input-icheck', 'id' => 'first_day_after_stock_taking']); !!}
                                @lang('Stocktaking::lang.first_day_after_stock_taking')
                            </label>
                        </div>
                        <div class="checkbox">
                            <label style="float:left;">
                                {!! Form::checkbox('F159ABC_first_day_of_next_month', 1, !empty($setting) ? $setting->F159ABC_first_day_of_next_month : false , ['class' => 'input-icheck',
                                'id' => 'first_day_of_next_month']); !!}
                                @lang('Stocktaking::lang.first_day_of_next_month')
                            </label> &Tab;
                            {!! Form::select('F159ABC_first_day_of_next_month_selected', $months, !empty($setting) ? $setting->F159ABC_first_day_of_next_month_selected : null, ['class' =>
                            'form-control', 'style' => 'width: 100px; float: left; margin-left: 10px; margin-right:
                            10px;', 'placeholder' => __('lang_v1.please_select')]) !!} @lang('Stocktaking::lang.month_end')
                        </div>
                        <div class="clearfix"></div>
                        @lang('Stocktaking::lang.balance_zero_example')
                    </div>
                </div>
            </div>

        </div>

        <div class="clearfix"></div>
        <div class="modal-footer">
            <button type="submit" class="btn btn-primary">@lang( 'messages.save' )</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">@lang( 'messages.close' )</button>
        </div>

        {!! Form::close() !!}

    </div><!-- /.modal-content -->
</div><!-- /.modal-dialog -->

<script>
    $('#F159ABC_setting_tdate').datepicker({
        format: 'mm/dd/yyyy'
    });
    $('#F159ABC_setting_tdate').datepicker("setDate", "{{!empty($setting->F159ABC_form_tdate) ? \Carbon::parse($setting->F159ABC_form_tdate)->format('m/d/Y') : date('m/d/Y')}}");
</script>
