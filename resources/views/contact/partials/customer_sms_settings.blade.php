<!-- Main content -->
<section class="content">
    @component('components.widget', ['class' => 'box-primary', 'title' => __( 'contact.customer_sms_settings' )])
        
        @slot('tool')
            <div class="box-tools">
                <button type="button" class="btn pull-right btn-primary btn-modal" 
                    data-href="{{action('CustomerSmsSettingController@create')}}" 
                    data-container=".crm_groups_modal">
                    <i class="fa fa-plus"></i> @lang( 'messages.add' )</button>
            </div>
        @endslot
       
        
        <div class="table-responsive">
            <table class="table table-bordered table-striped" id="customer_sms_settings_table" width="100%">
                <thead>
                    <tr>
                        <th>@lang( 'contact.location' )</th>
                        <th>@lang( 'contact.date_time' )</th>
                        <th>@lang( 'contact.show_customer' )</th>
                        <th>@lang( 'contact.show_supplier' )</th>
                        <th>@lang( 'contact.user_added' )</th>
                        <th>@lang( 'messages.action' )</th>
                    </tr>
                </thead>
            </table>
        </div>
        
    @endcomponent

    <div class="modal fade crm_groups_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>
    <div class="modal fade edit_crm_groups_modal" tabindex="-1" role="dialog" 
    	aria-labelledby="gridSystemModalLabel">
    </div>

</section>
<!-- /.content -->