<!-- Main content -->
<style>
    footer.main-footer.no-print {
        margin-left: 0 !important;
    }
</style>
@if(auth()->user()->is_pump_operator)
<div class="col-md-12">
    <a href="{{action('Auth\PumpOperatorLoginController@logout')}}" class="btn btn-flat btn-lg pull-right"
        style=" background-color: orange; color: #fff; margin-left: 5px;">@lang('petro::lang.logout')</a>
    <span><a href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorController@dashboard')}}"
            class="btn btn-flat btn-lg pull-right"
            style="color: #fff; background-color:#810040;">@lang('petro::lang.dashboard')</a></span>
</div>
<div class="clearfix"></div>
@endif

<section class="content" style="overflow: visible;">
    <div class="row">
        <div class="col-md-12">
            @component('components.widget', ['class' => 'box-primary'])

            <div class="row">
                @foreach ($pumps as $pump)
                @if($pump->is_confirmed == 1)
                    <div class="col-md-2 text-center" style="background: #8F3A84; margin: 10px 10px 10px 20px; color: #fff ">
                    <h3 style="padding: 0px 0px 6px 0px;"> {{$pump->pump_no}}</h3>
                    <h3 style="padding: 0px 0px 11px 0px;"> {{$pump->pumper_name}}</h3>
                </div>
                @else
                <div class="col-md-2 text-center" style="background: orange; margin: 10px 10px 10px 20px; color: #fff ">
                    <button type="button" class="btn  btn-primary btn-flat btn-modal"
                        style="height: 120px; width:100%; background: transparent; border: 0px;"
                        data-href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@confirmAssignment', [$pump->assignment_id])}}"
                        data-container=".pump_operator_modal">
                        <h3 style="padding: 0px 0px 6px 0px;"> {{$pump->pump_no}}</h3>
                        <h3 style="padding: 0px 0px 11px 0px;"> {{$pump->pumper_name}}</h3>
                    </button>
                    
                </div>
                
                @endif
                @endforeach
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('petro::lang.all_your_daily_collection')])
    
    @slot('tool')
    <div class="row">
        <div class="box-tools pull-right ">
            @can('bulk_assign_pumps')
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Petro\Http\Controllers\PumpOperatorAssignmentController@create')}}"
                data-container=".pump_operator_modal">
                <i class="fa fa-plus"></i> @lang('petro::lang.assign_pumps')</button>
            @endcan
        </div>
    </div>
        
    @endslot
    
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_daily_collection_table" style="width: 100%;">
            <thead>
                <tr>
                    <th>@lang('petro::lang.date')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('petro::lang.pump_operator')</th>
                    <th>@lang('petro::lang.shift_number')</th>
                    <th>@lang('petro::lang.pump_no')</th>
                    <th>@lang('petro::lang.starting_meter')</th>
                    <th>@lang('petro::lang.closing_meter')</th>
                    <th>@lang('petro::lang.shift_closed')</th>
                    <th>@lang('petro::lang.sold_ltr')</th>
                    <th>@lang('petro::lang.testing_ltr')</th>
                    <th>@lang('petro::lang.sold_amount')</th>

                </tr>
            </thead>

            <tfoot>
                <tr class="bg-gray font-17 footer-total text-center">
                     <td colspan="8"><strong>@lang('sale.total'):</strong></td>
                    <td><span class="display_currency" id="dc_footer_sold_fuel_qty" data-currency_symbol="false"></span></td>
                    <td><span class="display_currency" id="dc_footer_testing_qty" data-currency_symbol="false"></span></td>
                    <td><span class="display_currency" id="dc_footer_sold_fuel_amount" data-currency_symbol="false"></span></td>

                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->