
@component('components.widget', ['class' => 'box-primary', 'title' => __( 'sms::lang.sms_summary' )])

@slot('tool')
<div class="box-tools pull-right">
    <button type="button" class="btn btn-primary btn-modal"
        data-href="{{action('\Modules\Superadmin\Http\Controllers\SmsApiClientController@create')}}"
        data-container=".packages_modal">
        <i class="fa fa-plus"></i> @lang( 'superadmin::lang.add_external_client' )</button>
</div>
<br>
@endslot

<br>

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="external_api_clients_table" style="width: 100%;">
        <thead>
            <tr>
                <th width="7%">@lang( 'superadmin::lang.date' )</th>
                <th width="15%">@lang( 'superadmin::lang.name' )</th>
                <th width="7%">@lang( 'superadmin::lang.contact_mobile' )</th>
                <th width="7%">@lang( 'superadmin::lang.land_no' )</th>
                <th width="7%">@lang( 'superadmin::lang.contact_name' )</th>
                <th width="24%">@lang( 'superadmin::lang.api_key' )</th>
                <th width="10%">@lang( 'superadmin::lang.sender_names' )</th>
                <th width="7%">@lang( 'superadmin::lang.username' )</th>
                <th width="7%">@lang( 'superadmin::lang.password' )</th>
                <th width="7%" class="notexport">@lang( 'messages.action' )</th>
            </tr>
        </thead>
    </table>
</div>

@endcomponent