
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('list_date_range_filter', __('report.date_range') . ':') !!}
                    {!! Form::text('list_date_range_filter', @format_date('first day of this month') . ' ~ ' .
                    @format_date('last
                    day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' =>
                    'form-control date_range', 'id' => 'list_date_range_filter', 'readonly']); !!}
                </div>
            </div>
            
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('list_invoice_name', __( 'fleet::lang.invoice_name' )) !!}
                    {!! Form::select('list_invoice_name', $invoice_name, null, ['class' => 'form-control select2',
                    'required',
                    'placeholder' => __(
                    'fleet::lang.please_select' ), 'id' => 'list_invoice_name']);
                    !!}
                </div>
            </div>
            
            
            
            </div>
           
            @endcomponent
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])
            
            <div class="row">
                <div class="col-md-12">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered" id="list_route_operation_table" style="width: 100%;">
                            <thead>
                                <tr>
                                    <th>@lang( 'fleet::lang.date' )</th>
                                    <th>@lang( 'fleet::lang.customer' )</th>
                                    <th>@lang( 'report.date_range' )</th>
                                    <th>@lang( 'fleet::lang.invoice_nos' )</th>
                                    <th>@lang( 'fleet::lang.invoice_name' )</th>
                                    <th>@lang( 'lang_v1.action' )</th>
                                </tr>
                            </thead>
                            <tbody>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @endcomponent
        </div>
    </div>
    <div class="modal fade fleet_model" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade payment_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>