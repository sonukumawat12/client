
<!-- Main content -->
<section class="content">
    <div class="row">
        <div class="col-md-12">
            @component('components.filters', ['title' => __('report.filters')])
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('bakery_location_id', __('purchase.business_location') . ':') !!}
                    {!! Form::select('bakery_location_id', $business_locations, null, ['class' => 'form-control select2',
                    'placeholder' => __('petro::lang.all'), 'style' => 'width:100%']); !!}
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    {!! Form::label('bakery_pump_operator', __('bakery::lang.user').':') !!}
                    {!! Form::select('bakery_pump_operator', $pump_operators, null, ['class' => 'form-control select2',
                    'placeholder' => __('petro::lang.all')]); !!}
                </div>
            </div>
           
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('bakery_status', __('petro::lang.status') . ':') !!}
                    {!! Form::select('bakery_status', ['inactive' => __('petro::lang.inactive'),'active' =>
                    __('petro::lang.active')], null, ['class' => 'form-control
                    select2', 'style' => 'width:100%', 'placeholder' => __('lang_v1.all')]); !!}
                </div>
            </div>
            @endcomponent
        </div>
    </div>

    @component('components.widget', ['class' => 'box-primary', 'title' =>
    __('bakery::lang.users')])
    @slot('tool')
    @if(auth()->user()->can('bakery_add_user'))
    <div class="row">
        <div class="box-tools pull-right ">
            <button type="button" class="btn  btn-primary btn-modal"
                data-href="{{action('\Modules\Bakery\Http\Controllers\BakeryUserController@create')}}"
                data-container=".view_modal">
                <i class="fa fa-plus"></i> @lang('messages.add')</button>
        </div>
    </div>
    @endif
        
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="list_bakery_users_table" width="100%">
            <thead>
                <tr>
                    <th class="notexport">@lang('messages.action')</th>
                    <th>@lang('bakery::lang.date_added')</th>
                    <th>@lang('bakery::lang.user')</th>
                    <th>@lang('petro::lang.location')</th>
                    <th>@lang('bakery::lang.sold_amount')</th>
                    <th>@lang('petro::lang.commission_type')</th>
                    <th>@lang('petro::lang.commission_rate')</th>
                    <th>@lang('bakery::lang.earned_amount')</th>
                    <th>@lang('bakery::lang.balance_amount')</th>

                </tr>
            </thead>

            <tfoot>
                <tr class="bg-gray font-17 footer-total text-center">
                    <td colspan="4"><strong>@lang('sale.total'):</strong></td>
                    <td><span class="display_currency" id="footer_sold_amount" data-currency_symbol="false"></span>
                    </td>
                    
                    <td></td>
                    <td></td>
                    <td><span class="display_currency" id="footer_earned_amount" data-currency_symbol="true"></span>
                    </td>
                    <td><span class="display_currency" id="footer_balance_amount" data-currency_symbol="true"></span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->
