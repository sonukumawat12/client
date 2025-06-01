

<!-- Main content -->
<section class="content main-content-inner">
    
    @component('components.widget', ['class' => 'box-primary'])
    @slot('tool')
    
    <div class="box-tools pull-right">
        <button type="button" class="btn btn-primary btn-modal add_fuel_tank"
            data-href="{{action('\Modules\Vat\Http\Controllers\VatConcernController@create')}}"
            data-container=".fuel_tank_modal">
            <i class="fa fa-plus"></i> @lang('messages.add')</button>
    </div>
    
    @endslot
    <div class="table-responsive">
        <table class="table table-bordered table-striped" id="vat_concerns_table" style="width: 100%">
            <thead>
                <tr>
                    <th>@lang('vat::lang.line_1')</th>
                    <th>@lang('vat::lang.line_2')</th>
                    <th>@lang('vat::lang.line_3')</th>
                    <th>@lang('vat::lang.line_4')</th>
                    <th>@lang('vat::lang.line_5')</th>
                    <th>@lang('vat::lang.status')</th>
                    <th>@lang('vat::lang.created_by')</th>
                    <th>@lang('vat::lang.date_time')</th>
                    <th>@lang('lang_v1.action')</th>
                </tr>
            </thead>
        </table>
    </div>
    @endcomponent

</section>
<!-- /.content -->
