 <!-- Main content -->
    <section class="content" id="dsrSettings">
        <div class="row">
            <div class="col-md-12">
                @component('components.widget', ['class' => 'box-primary', 'title' => __(
              'dsr::lang.all_dsr_settings')])
                    @slot('tool')
                        <div class="box-tools">
                            <button type="button" class="btn btn-primary btn-modal mb-2 pull-right" id="add_dsr_btn"
                                    data-href="{{action('\Modules\Dsr\Http\Controllers\DsrSettingsController@create')}}"
                                    data-container=".dsr_settings_model">
                                <i class="fa fa-plus"></i> @lang('dsr::lang.add_dsr_settings')</button>
                        </div>
                    @endslot
                    <div class="row">
                        <div class="col-md-12">
                            <table class="table table-striped table-bordered" id="dsr_settings_table" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th>@lang('dsr::lang.dealer_name')</th>
                                    <th>@lang('dsr::lang.country')</th>
                                    <th>@lang('dsr::lang.province')</th>
                                    <th>@lang('messages.action')</th>
                                </tr>
                                </thead>
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                @endcomponent
            </div>
        </div>
        
    </section>



