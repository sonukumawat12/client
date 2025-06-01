
<div
    class="pos-tab-content">
    <div class="row">
         
        @component('components.widget', ['class' => 'box-primary', 'title' => __( 'tax_rate.all_your_tax_rates' )])
        @can('tax_rate.create')
            @slot('tool')
                <div class="box-tools pull-right">
                    <button type="button" class="btn  btn-primary btn-modal" 
                            data-href="{{action('\Modules\Superadmin\Http\Controllers\DefaultTaxRateController@create')}}" 
                            data-container=".tax_rate_modal">
                            <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
                <hr>
            @endslot
        @endcan
        @can('tax_rate.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="default_tax_rates_table" width="100%">
                    <thead>
                        <tr>
                            <th>@lang( 'tax_rate.name' )</th>
                            <th>@lang( 'tax_rate.rate' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent

    @component('components.widget', ['class' => 'box-primary'])
        @slot('title')
            @lang( 'tax_rate.tax_groups' ) ( @lang('lang_v1.combination_of_taxes') ) @show_tooltip(__('tooltip.tax_groups'))
        @endslot
        @can('tax_rate.create')
            @slot('tool')
                <div class="box-tools pull-right">
                    <button type="button" class="btn  btn-primary btn-modal" 
                    data-href="{{action('\Modules\Superadmin\Http\Controllers\DefaultGroupTaxController@create')}}" 
                    data-container=".tax_group_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
                </div>
                <hr>
            @endslot
        @endcan
        @can('tax_rate.view')
            <div class="table-responsive">
                <table class="table table-bordered table-striped" id="default_tax_groups_table" width="100%">
                    <thead>
                        <tr>
                            <th>@lang( 'tax_rate.name' )</th>
                            <th>@lang( 'tax_rate.rate' )</th>
                            <th>@lang( 'tax_rate.sub_taxes' )</th>
                            <th>@lang( 'messages.action' )</th>
                        </tr>
                    </thead>
                </table>
            </div>
        @endcan
    @endcomponent
    
   
</div>
</div>