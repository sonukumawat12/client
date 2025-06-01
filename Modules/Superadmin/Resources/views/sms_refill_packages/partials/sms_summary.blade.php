
@component('components.widget', ['class' => 'box-primary', 'title' => __( 'sms::lang.sms_summary' )])

<div class="table-responsive">
    <table class="table table-bordered table-striped" id="sms_summary_table" style="width: 100%;">
        <thead>
            <tr>
                <th>@lang( 'superadmin::lang.business_client' )</th>
                <th>@lang( 'superadmin::lang.business_type' )</th>
                <th>@lang( 'sms::lang.sms_bal' )</th>
                <th class="notexport">@lang( 'messages.action' )</th>
            </tr>
        </thead>
    </table>
</div>

@endcomponent