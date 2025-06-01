


<div class="page-title-area">
    <div class="row align-items-center">
        <div class="col-sm-6">
            <div class="breadcrumbs-area clearfix">
            <ul class="breadcrumbs pull-left" style="margin-top: 15px">
                    <li><a href="#">@lang('petro::lang.collection_summary')</a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
               
            <div class="row">
                
                
                 <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('cs_pump_operator', __('petro::lang.pump_operator').':') !!}<br>
                        {!! Form::select('cs_pump_operator', $pump_operators, null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="form-group">
                        {!! Form::label('cs_date_range', __('report.date_range') . ':') !!}
                        {!! Form::text('cs_date_range', @format_date('first day of this month') . ' ~ ' . @format_date('last day of this month') , ['placeholder' => __('lang_v1.select_a_date_range'), 'class' => 'form-control', 'id' => 'cs_date_range', 'readonly']); !!}
                    </div>
                </div>
                
                <div class="col-sm-3">
                    <div class="form-group">
                        {!! Form::label('daily_collection_type', __('petro::lang.daily_collection_type').':') !!}<br>
                        {!! Form::select('daily_collection_type', array('daily_cash' => 'Daily Cash', 'daily_voucher' => 'Daily Credit Sales', 'daily_card' => 'Daily Cards', 'shortage_excess' => 'Shortage Excess','other' => 'Other Payments', 'cheque' => 'Cheques'), null, ['class' => 'form-control select2', 'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                    </div>
                </div>
                
               
            </div>
                
                
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' => __('petro::lang.collection_summary')])
   
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="collection_summary_table" width="100%">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.settlement_no')</th>
                    <th>@lang('petro::lang.last_collection_form_no')</th>
                    <th>@lang('petro::lang.total_amount')</th>
                    <th>@lang('petro::lang.daily_collection_type' )</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

    <div class="modal fade pump_operator_modal" role="dialog" aria-labelledby="gridSystemModalLabel">
    </div>

    <div id="daily_card_print"></div>

</section>
<!-- /.content -->
