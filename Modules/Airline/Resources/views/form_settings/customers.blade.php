    <div class="row mt-15">
        <div class="col-md-2">
             {!! Form::label('location', __('lang_v1.location') . ':',['class'=>'mt-10  w-100']) !!}
               
        </div>
        <div class="col-md-4">
            <div class="form-group" style="">
            @php
                $location_id = (isset($business->contact_fields) && isset($business->contact_fields['location']) )?$business->contact_fields['location'] : 0;
            @endphp    
            {!! Form::select('contact_fields[location]', $businessLocations->prepend('All Locations',0), $location_id, [
                    'id' => 'location',
                    'class' => 'form-control select2'
                ]); !!}
            </div>
        </div>
    </div>
    <!-- <h3>@lang('lang_v1.select_the_field_you_want_in_adding_contact')</h3> -->
    <div class="row">
        
        <div class="col-md-12">
            
            <form id="customers-form" action="javascript:void(0)" method="POST">
                @csrf 
            
           

            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
            <input type="checkbox" name="customer_name" value="1" class="input-icheck" checked disabled>
            {{ __('lang_v1.name') }}
        </label>
                </div>
            </div>

            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="need_to_send_sms" value="1" class="input-icheck" >
                        {{ __('lang_v1.need_to_send_sms') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="credit_notification_type" value="1" class="input-icheck" >
                        {{ __('lang_v1.credit_notification_type') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="vat_no" value="1" class="input-icheck" >
                        {{ __('lang_v1.vat_no') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_opening_balance" value="1" class="input-icheck " checked disabled >
                        {{ __('lang_v1.opening_balance') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_customer_group" value="1" class="input-icheck" >
                        {{ __('lang_v1.customer_group') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_credit_limit" value="1" class="input-icheck" >
                        {{ __('lang_v1.credit_limit') }}
                    </label>
                </div>
            </div>
            <!-- Contact Type Checkbox - Checked and Disabled -->
<div class="col-sm-4">
    <div class="checkbox">
        <label>
            <input type="checkbox" name="contact_type" value="1" class="input-icheck" checked disabled>
            {{ __('lang_v1.contact_type') }}
        </label>
    </div>
</div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label> 
                        <input type="checkbox" name="customer_transaction_date" value="1" class="input-icheck " checked disabled >
                        {{ __('lang_v1.transaction_date') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="add_more_mobile" value="1" class="input-icheck" >
                        {{ __('lang_v1.add_more_mobile') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_mobile" value="1" class="input-icheck" >
                        {{ __('lang_v1.mobile') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_landline" value="1" class="input-icheck" >
                        {{ __('lang_v1.landline') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="assigned_to" value="1" class="input-icheck" >
                        {{ __('lang_v1.assigned_to') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_address" value="1" class="input-icheck" >
                        {{ __('lang_v1.address') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="address_line_2" value="1" class="input-icheck" >
                        {{ __('lang_v1.address_line_2') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_city" value="1" class="input-icheck" >
                        {{ __('lang_v1.city') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_state" value="1" class="input-icheck" >
                        {{ __('lang_v1.state') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_country" value="1" class="input-icheck" >
                        {{ __('lang_v1.country') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_landmark" value="1" class="input-icheck" >
                        {{ __('lang_v1.landmark') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_tax_number" value="1" class="input-icheck">
                        {{ __('lang_v1.tax_number') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_pay_term" value="1" class="input-icheck">
                        {{ __('lang_v1.pay_term') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_password" value="1" class="input-icheck">
                        {{ __('lang_v1.password') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_confirm_password" value="1" class="input-icheck">
                        {{ __('lang_v1.confirm_password') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_email" value="1" class="input-icheck">
                        {{ __('lang_v1.email') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="customer_alternate_contact_number" value="1" class="input-icheck">
                        {{ __('lang_v1.alternate_contact_number') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="sub_customer" value="1" class="input-icheck">
                        {{ __('lang_v1.sub_customer') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="vehicle_no" value="1" class="input-icheck">
                        {{ __('lang_v1.vehicle_no') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="address_line_3" value="1" class="input-icheck">
                        {{ __('lang_v1.address_line_3') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="passport_nic_no" value="1" class="input-icheck">
                        {{ __('lang_v1.passport_nic_no') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="passport_nic_image" value="1" class="input-icheck">
                        {{ __('lang_v1.passport_nic_image') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="signature" value="1" class="input-icheck">
                        {{ __('lang_v1.signature') }}
                    </label>
                </div>
            </div>
            
            <div class="col-sm-4">
                <div class="checkbox">
                    <label>
                        <input type="checkbox" name="whatsapp_number" value="1" class="input-icheck">
                        Whatsapp Number
                    </label>
                </div>
            </div>

           
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8">
               
        </div>
        <div class="col-sm-4">
            <button type="submit" class="btn btn-primary mt-5 ml-5">Update Customers</button>
        </div>

    </div>

</form>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>


<script>

$(document).ready(function() {
    $('#customers-form').on('submit', function(e) {

        e.preventDefault();

            var formData = $(this).serialize();

            $.ajax({
                url: "{{ url('airline/form_settings/update_customers') }}", 
                type: 'POST',
                data: formData,
                success: function(response) {
                    alert('Form submitted successfully!');

                    checkData();
                

                    
                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });
       
    });

    checkData();
    function checkData(){

        $.ajax({
                url: "{{ url('airline/form_settings/check_form_settings_customers') }}", 
                type: 'GET',
                
                success: function(response) {

                    var settings = response.data; 
                    console.log('Form submitted successfully:', settings.transaction_date);
                    
                    $('input[name="customer_name"]').prop('checked', settings.name == 1);
                    $('input[name="need_to_send_sms"]').prop('checked', settings.need_to_send_sms == 1);
                    $('input[name="credit_notification_type"]').prop('checked', settings.credit_notification_type == 1);
                    $('input[name="vat_no"]').prop('checked', settings.vat_no == 1);
                    $('input[name="customer_opening_balance"]').prop('checked', settings.opening_balance == 1);
                    $('input[name="customer_customer_group"]').prop('checked', settings.customer_group == 1);
                    $('input[name="customer_credit_limit"]').prop('checked', settings.credit_limit == 1);
                    $('input[name="customer_transaction_date"]').prop('checked', settings.transaction_date == 1);
                    $('input[name="add_more_mobile"]').prop('checked', settings.add_more_mobile_numbers == 1);
                    $('input[name="customer_mobile"]').prop('checked', settings.mobile == 1);
                    $('input[name="customer_landline"]').prop('checked', settings.landline == 1);
                    $('input[name="assigned_to"]').prop('checked', settings.assigned_to == 1);
                    $('input[name="customer_address"]').prop('checked', settings.address == 1);
                    $('input[name="address_line_2"]').prop('checked', settings.address_line_2 == 1);
                    $('input[name="customer_city"]').prop('checked', settings.city == 1);
                    $('input[name="customer_state"]').prop('checked', settings.state == 1);
                    $('input[name="customer_country"]').prop('checked', settings.country == 1);
                    $('input[name="customer_landmark"]').prop('checked', settings.landmark == 1);
                    $('input[name="customer_tax_number"]').prop('checked', settings.tax_number == 1);
                    $('input[name="customer_pay_term"]').prop('checked', settings.pay_term == 1);
                    $('input[name="customer_password"]').prop('checked', settings.password == 1);
                    $('input[name="customer_confirm_password"]').prop('checked', settings.confirm_password == 1);
                    $('input[name="customer_email"]').prop('checked', settings.email == 1);
                    $('input[name="customer_alternate_contact_number"]').prop('checked', settings.alternate_contact_number == 1);
                    $('input[name="sub_customer"]').prop('checked', settings.sub_customer == 1);
                    $('input[name="vehicle_no"]').prop('checked', settings.vehicle_no == 1);
                    $('input[name="address_line_3"]').prop('checked', settings.address_line_3 == 1);
                    $('input[name="passport_nic_no"]').prop('checked', settings.passport_nic_no == 1);
                    $('input[name="passport_nic_image"]').prop('checked', settings.passport_nic_image == 1);
                    $('input[name="signature"]').prop('checked', settings.signature == 1);
                    $('input[name="whatsapp_number"]').prop('checked', settings.whatsapp_number == 1);
              

                },
                error: function(xhr, status, error) {
                    alert('Error: ' + error);
                }
            });

    }
});


</script>