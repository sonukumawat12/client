<!-- Content Header (Page header) -->
<section class="content-header">
    <h1>@lang( 'vat::lang.vat_report_settings' )
    </h1>
</section>

<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary'])
        @slot('tool')
        
        <div class="box-tools pull-right">
           <button type="button" class="btn btn-primary btn-modal add_fuel_tank"
                    data-href="{{action('\Modules\Vat\Http\Controllers\SettingsController@create')}}"
                    data-container=".fuel_tank_modal">
                    <i class="fa fa-plus"></i> @lang('messages.add')</button>
        </div>
        
        @endslot
        <div class="table-responsive">
             <table class="table table-bordered table-striped" id="vat_settings_table" width="100%">
                <thead>
                    <tr>
                        <th>@lang('vat::lang.date_time')</th>
                        <th>@lang('vat::lang.vat_period')</th>
                        <th>@lang('vat::lang.effective_date')</th>
                        <th>@lang('vat::lang.status')</th>
                        <th>@lang('vat::lang.tax_report_name')</th>
                        <th>@lang('vat::lang.added_by')</th>
                    </tr>
                </thead>
                
            </table>
        </div>
    @endcomponent

  
</section>
<!-- /.content -->