@component('components.widget', ['class' => 'box-primary', 'title' => __( 'superadmin::lang.sms_packages' )])
@slot('tool')
<div class="box-tools pull-right">
    <button type="button" class="btn btn-primary btn-modal"
        data-href="{{action('\Modules\Superadmin\Http\Controllers\SmsRefillPackageController@create')}}"
        data-container=".packages_modal">
        <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
</div>
@endslot

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="sms_packages_table" style="width: 100%;">
        <thead>
            <tr>
                <th>@lang( 'superadmin::lang.date' )</th>
                <th>@lang( 'superadmin::lang.package_name' )</th>
                <th>@lang( 'superadmin::lang.unit_cost' )</th>
                <th>@lang( 'superadmin::lang.amount' )</th>
                <th>@lang( 'superadmin::lang.no_of_sms' )</th>
                <th>@lang( 'superadmin::lang.user_added' )</th>
                <th class="notexport">@lang( 'messages.action' )</th>
            </tr>
        </thead>
    </table>
</div>

@endcomponent