<span id="pl_span">
<div class="col-xs-6">
    @component('components.widget')
        <table class="table table-striped">
            <tr>
                <th>{{ __('report.opening_stock') }}:</th>
                <td>
                    <span class="opening_stock">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('home.total_purchase') }}:</th>
                <td>
                     <span class="total_purchase">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr class="hide">
                <th>{{ __('report.total_stock_adjustment') }}:</th>
                <td>
                     <span class="total_adjustment">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr> 
            <tr>
                <th>{{ __('report.stock_adjustment_increase') }}:</th>
                <td>
                     <span class="increase_stock_adjustment">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('report.stock_adjustment_decrease') }}:</th>
                <td>
                     <span class="decrease_stock_adjustment">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('report.total_expense') }}:</th>
                <td>
                     <span class="total_expense">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            @php
                $essentials_enabled = Module::has('Essentials') && !empty($__is_essentials_enabled) ? true : false;
            @endphp
            @if($essentials_enabled)
                <tr>
                    <th>{{ __('essentials::lang.total_payroll') }}:</th>
                    <td>
                         <span class="total_payroll">
                            <i class="fa fa-refresh fa-spin fa-fw"></i>
                        </span>
                    </td>
                </tr>
            @endif

            @if(isset($show_manufacturing_data) && $show_manufacturing_data)
                <tr>
                    <th>{{ __('manufacturing::lang.total_production_cost') }}:</th>
                    <td>
                         <span class="total_production_cost">
                            <i class="fa fa-refresh fa-spin fa-fw"></i>
                        </span>
                    </td>
                </tr>
            @endif

            <!--<tr>
                <th>{{ __('lang_v1.total_shipping_charges') }}:</th>
                <td>
                     <span class="total_transfer_shipping_charges">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_sell_discount') }}:</th>
                <td>
                     <span class="total_sell_discount">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_reward_amount') }}:</th>
                <td>
                     <span class="total_reward_amount">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_sell_return') }}:</th>
                <td>
                     <span class="total_sell_return">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>-->
        </table>
    @endcomponent
</div>

<div class="col-xs-6">
    @component('components.widget')
        <table class="table table-striped">
            <tr>
                <th>{{ __('report.closing_stock') }}:</th>
                <td>
                    <span class="closing_stock">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('home.total_sell') }}: </th>
                <td>
                     <span class="total_sell">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('report.total_sales_on_cost') }}:</th>
                <td>
                     <span class="total_sales_on_cost">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <!--<tr>
                <th>{{ __('report.total_stock_recovered') }}:</th>
                <td>
                     <span class="total_recovered">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_purchase_return') }}:</th>
                <td>
                     <span class="total_purchase_return">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>
            <tr>
                <th>{{ __('lang_v1.total_purchase_discount') }}:</th>
                <td>
                     <span class="total_purchase_discount">
                        <i class="fa fa-refresh fa-spin fa-fw"></i>
                    </span>
                </td>
            </tr>-->
            <tr>
                <td colspan="2">
                &nbsp;
                </td>
            </tr>
            <tr>
                <td colspan="2">
                &nbsp;
                </td>
            </tr>
        </table>
    @endcomponent
</div>
<div class="col-xs-12">
    @component('components.widget')
        <h3 class="text-muted mb-0">
            {{ __('lang_v1.gross_profit') }} (1): 
            <span class="profit_without_expense">
                <i class="fa fa-refresh fa-spin fa-fw"></i>
            </span>
        </h3>
        <small class="help-block">(@lang('report.profit_without_expense'))</small>
       
        <h3 class="text-muted mb-0">
            {{ __('lang_v1.gross_profit') }} (2): 
            <span class="gross_profit">
                <i class="fa fa-refresh fa-spin fa-fw"></i>
            </span>
        </h3>
        <small class="help-block">(@lang('lang_v1.gross_profit') - @lang('lang_v1.direct_expense'))</small>

        <h3 class="text-muted mb-0">
            {{ __('lang_v1.gross_profit') }} (3): 
            <span class="net_profit">
                <i class="fa fa-refresh fa-spin fa-fw"></i>
            </span>
        </h3>
        <small class="help-block">(@lang('lang_v1.gross_profit') - @lang('lang_v1.total_expense_exclude_cogs_direct'))</small>
    @endcomponent
</div>
</span>
@php
                $reports_footer = \App\System::where('key','admin_reports_footer')->first();
            @endphp
            
            @if(!empty($reports_footer))
                <style>
                    #footer {
                        display: none;
                    }
                
                    @media print {
                        #footer {
                            display: block !important;
                            position: fixed;
                            bottom: -1mm;
                            width: 100%;
                            text-align: center;
                            font-size: 12px;
                            color: #333;
                        }
                    }
                </style>
        
                <div id="footer">
                    {{ ($reports_footer->value) }}
                </div>
            @endif