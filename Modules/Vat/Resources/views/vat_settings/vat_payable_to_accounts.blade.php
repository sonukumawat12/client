

<!-- Main content -->
<section class="content main-content-inner">
    
    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-primary btn-modal add_fuel_tank"
            data-href="{{action('\Modules\Vat\Http\Controllers\VatPayableToAccountController@create')}}"
            data-container=".fuel_tank_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="vat_payable_to_table" style="width: 100%">
            <thead>
                <tr>
                    <th>@lang('vat::lang.date_time')</th>
                    <th>@lang('vat::lang.vat_opening_balance_type')</th>
                    <th>@lang('vat::lang.vat_payable_account') / @lang('vat::lang.vat_receivable_account')</th>
                    <th>@lang('vat::lang.opening_balance_amount')</th>
                    <th>@lang('vat::lang.created_by')</th>
                    <th>@lang('vat::lang.note')</th>
                    <th>@lang('lang_v1.action')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->

            
