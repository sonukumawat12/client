<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])

            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_9a_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('form_9a_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('form_9a_date', __('date') . ':') !!}
                    {!! Form::date('datepicker', date('Y-m-d'), [
                        'class' => 'form-control',
                        'placeholder' => __( 'mpcs::lang.date_and_time' ),
                        'id' => 'form_9a_date'
                    ]) !!}
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('type', __('mpcs::lang.from_no') . ':') !!}
                    {!! Form::text('form_9a_no', $form_9a_no, ['class' => 'form-control', 'readonly']) !!}
                </div>
            </div>

            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            @slot('tool')
            <div class="box-tools">
                <!-- Standard Print button -->
                <button class="btn btn-primary print_report pull-right" id="print_div">
                    <i class="fa fa-print"></i> @lang('messages.print')</button>
            </div>
            @endslot
            <div class="col-md-12">

                <div class="row" style="margin-top: 20px;" id="print_content">
                    <style>
                    </style>
                    <div class="col-md-12">
                        <h4>@lang('mpcs::lang.daily_sales_report')</h4>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="table-responsive">
                                    <table class="table table-bordered table-striped" id="form_9b_table">
                                        <thead class="align-middle">
                                            <tr class="align-middle text-center">
                                                <th class="align-middle text-center" rowspan="2">@lang('mpcs::lang.description')</th>
                                                <th class="align-middle text-center" colspan="2">Total Sale</th>
                                                <th class="align-middle text-center" colspan="2">Card Sale</th>
                                                <th class="align-middle text-center" colspan="2">Cash Sale</th>
                                                <th class="align-middle text-center" colspan="2">Empty Barrels</th>
                                                <th class="align-middle text-center" colspan="2">Others</th>
                                                <th class="align-middle text-center" colspan="2">Total</th>
                                                <th class="align-middle text-center" colspan="2">With Taxes</th>
                                                <th class="align-middle text-center" colspan="2">Without Taxes</th>
                                                <th class="align-middle text-center" rowspan="2">Office Use</th>
                                            </tr>
                                            <tr class="align-middle" style="text-align: center;">
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                                <td>Rupees</td>
                                                <td>Cents</td>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <th>Cash</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="card_sales"></td> <!-- Total Card Sales -->
                                                <td></td>
                                                <td id="cash_sales"></td> <!-- Total Cash Sales -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_cash_sale"></td> <!-- Total = Card Sales + Cash Sales -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td rowspan="7">
                                                    <p>Rceived On ...........................</p>
                                                    <p>Checked ................................</p>
                                                    <p>Approved .............................</p>
                                                    <p style="text-align: center;">After Checking</p>
                                                    <p style="display: flex; justify-content: center; gap: 10px;">
                                                        <span style="text-decoration-line: underline;"> Short Money </span>
                                                        <span style="text-decoration-line: underline;"> Excess Money </span>
                                                    </p>
                                                    <p>Today ...................................</p>
                                                    <p>Previous Day ........................</p>
                                                    <p>As of Today ...........................</p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <th>Deposit / Credit Sales</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_credit_sale"></td> <!-- Total Credit Sales -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th> &nbsp; </th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td> <!--  -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>MPCS Branches</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>Today Sale</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_sale"></td> <!-- 6 + 7 -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>Total Sale up to Previous Day</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_sale_pre_day"></td> <!-- Total Sale up to Previous Day -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                            <tr>
                                                <th>Total Sale as of Today</th>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td id="total_sale_today"></td> <!-- 8 + 9 -->
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                                <td></td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                        </div>
                    </div>
                </div>

            </div>
            @endcomponent
        </div>
    </div>

</section>
<!-- /.content -->